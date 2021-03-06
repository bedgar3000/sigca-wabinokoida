<?php
session_start();
if (!isset($_SESSION['USUARIO_ACTUAL']) || !isset($_SESSION['ORGANISMO_ACTUAL'])) header("Location: ../index.php");
?>
<?php
define('FPDF_FONTPATH','font/');
require('mc_table3.php');
require('fphp.php');
connect();

$sql = "SELECT * FROM mastorganismos WHERE CodOrganismo = '$forganismo'";
$query_organismo = mysql_query($sql) or die ($sql.mysql_error());
$field_organismo = mysql_fetch_array($query_organismo);

//	--------------------------------------------------
//	Creación del objeto de la clase heredada
$pdf=new PDF_MC_Table('P', 'mm', 'Letter');
$pdf->Open();
$pdf->SetMargins(5, 5, 5);
$pdf->SetAutoPageBreak(10);

$emp = 0;
//	Imprimo los datos
$sql = "SELECT 
			ptne.CodPersona,
			ptne.SueldoBasico,
			mp.Ndocumento 
		FROM 
			pr_tiponominaempleado ptne
			INNER JOIN mastpersonas mp ON (ptne.CodPersona = mp.CodPersona)
		WHERE 
			ptne.Periodo = '".$fperiodo."' AND 
			ptne.CodTipoNom = '".$ftiponom."' AND 
			ptne.CodTipoProceso = '".$ftproceso."'
		ORDER BY length(mp.Ndocumento), mp.Ndocumento";
$query_empleado = mysql_query($sql) or die ($sql.mysql_error());	
while ($field_empleado = mysql_fetch_array($query_empleado)) {
	$persona = $field_empleado['CodPersona'];
	$emp++;
	$hoy = date("d/m/Y");
	
	// Obtengo la fecha del payroll	...
	$sql = "SELECT 
				pp.*,
				p.Descripcion AS NomProceso 
			FROM 
				pr_procesoperiodo pp
				INNER JOIN pr_tipoproceso p ON (pp.CodTipoProceso = p.CodTipoProceso)
			WHERE 
				pp.CodOrganismo = '".$forganismo."' AND 
				pp.Periodo = '".$fperiodo."' AND 
				pp.CodTipoNom = '".$ftiponom."' AND 
				pp.CodTipoProceso = '".$ftproceso."'";
	$query_fecha = mysql_query($sql) or die ($sql.mysql_error());
	if (mysql_num_rows($query_fecha) != 0) $field_fecha = mysql_fetch_array($query_fecha);
	list($a, $m, $d)=SPLIT( '[-./]', $field_fecha['FechaDesde']); $de= "$d/$m/$a";
	list($a, $m, $d)=SPLIT( '[-./]', $field_fecha['FechaHasta']); $a= "$d/$m/$a";

	//	Imprimo el encabezado
	if ($contador_conceptos > 8)  {
		if ($emp == 2) $emp = 1;
	}
	
	if ($emp == 3) $emp = 1; 
	if ($emp == 1) {
		$pdf->AddPage();
		$y = 5;
	} else $y = 165;
	
	$pdf->SetFont('Courier', 'B', 12);
	$pdf->SetXY(5, $y); $pdf->Cell(150, 8, utf8_decode($field_organismo['Organismo']), 0, 1, 'L');
	$pdf->SetFont('Courier', 'B', 12);	
	$pdf->Cell(100, 5, ('De: '.$de.' A: '.$a), 0, 0, 'L');
	$pdf->Cell(100, 5, utf8_decode($field_fecha['NomProceso']), 0, 1, 'R');
	$pdf->Ln(3);
	
	//	Consulta para obtener el resto de los datos
	$sql = "SELECT 
				mp.Ndocumento, 
				me.Fingreso, 
				me.SueldoActual, 
				tn.TituloBoleta AS Nomina,
				bp.Ncuenta,
				tne.TotalIngresos,
				tne.TotalEgresos,
				tne.TotalPatronales, 
				md.Dependencia, 
				me.CodEmpleado, 
				mp.NomCompleto, 
				rp.DescripCargo,
				rbp.CodBeneficiario,
				rbp.NroDocumento,
				rbp.NombreCompleto
			FROM 
				mastempleado me 
				INNER JOIN rh_puestos rp ON (me.CodCargo = rp.CodCargo)
				INNER JOIN mastdependencias md ON (me.CodDependencia = md.CodDependencia) 
				INNER JOIN mastpersonas mp ON (me.CodPersona = mp.CodPersona)
				INNER JOIN tiponomina tn ON (me.CodTipoNom = tn.CodTipoNom)
				LEFT JOIN bancopersona bp ON (me.CodPersona = bp.CodPersona AND bp.FlagPrincipal = 'S')
				LEFT JOIN rh_beneficiariopension rbp ON (me.CodPersona = rbp.CodPersona)
				INNER JOIN pr_tiponominaempleado tne ON (me.CodPersona = tne.CodPersona AND 
														 tne.Periodo = '".$fperiodo."' AND 
														 tne.CodOrganismo = '".$forganismo."' AND 
														 tne.CodTipoProceso = '".$ftproceso."' AND 
														 tne.CodTipoNom = '".$ftiponom."')
			WHERE me.CodPersona = '".$persona."'";
	$query_resto = mysql_query($sql) or die ($sql.mysql_error());
	if (mysql_num_rows($query_resto) != 0) $field_resto = mysql_fetch_array($query_resto);
	if ($field_resto['CodBeneficiario'] != "") {
		$nomcompleto = substr($field_resto['NomCompleto']." (".$field_resto['NombreCompleto'].")", 0, 45);
		$cargo = ("Beneficiario Pensión por Sobreviviente");
		$fingreso = "";
	} else {
		$nomcompleto = substr($field_resto['NomCompleto'], 0, 45);
		if ($ftiponom == "04") $cargo = ("Pensión por Inválidez");
		elseif ($ftiponom == "03") $cargo = "Jubilado";
		else $cargo = substr($field_resto['DescripCargo'], 0, 45);
		list($a, $m, $d)=SPLIT( '[-./]', $field_resto['Fingreso']); $fingreso = "$d/$m/$a";
	}
	$ndoc = number_format($field_resto['Ndocumento'], 0, '', '.');
	$codempleado = $field_resto['CodEmpleado'];
	
	$total_ingresos = number_format($field_resto['TotalIngresos'], 2, ',', '.');
	$total_egresos = number_format($field_resto['TotalEgresos'], 2, ',', '.');
	$total_patronales = number_format($field_resto['TotalPatronales'], 2, ',', '.');
	$total = $field_resto['TotalIngresos'] - $field_resto['TotalEgresos'];
	$total_neto = number_format($total, 2, ',', '.');
	
	//	Imprimo el tiupo de nomina y los datos del empleado	
	$pdf->SetFont('Courier', 'B', 14);
	$pdf->Cell(205, 5, utf8_decode($field_resto['Nomina']), 0, 1, 'C');
	$pdf->Ln(4);
	
	$pdf->SetFont('Courier', 'B', 13);
	$dependencia = substr($field_resto['Dependencia'], 0, 45);
	$pdf->Cell(20, 5, utf8_decode('Código'), 0, 0, 'L'); $pdf->Cell(3, 5);
	$pdf->Cell(30, 5, utf8_decode('Cédula'), 0, 0, 'L'); $pdf->Cell(3, 5);
	$pdf->Cell(115, 5, 'Nombre y Apellido', 0, 0, 'L'); $pdf->Cell(3, 5);
	if ($field_resto['CodBeneficiario'] == "") $pdf->Cell(30, 5, 'Fecha Ing.', 0, 1, 'L'); else $pdf->Ln();
	$pdf->Cell(20, 5, $codempleado, 0, 0, 'L'); $pdf->Cell(3, 5);
	$pdf->Cell(30, 5, $ndoc, 0, 0, 'L'); $pdf->Cell(3, 5);
	$pdf->Cell(115, 5, utf8_decode($nomcompleto), 0, 0, 'L'); $pdf->Cell(3, 5);
	if ($field_resto['CodBeneficiario'] == "") $pdf->Cell(30, 5, $fingreso, 0, 1, 'L'); else $pdf->Ln();
	$pdf->Cell(23, 5, 'Cargo: ', 0, 0, 'L');
	$pdf->Cell(185, 5, utf8_decode($cargo), 0, 1, 'L');
	$pdf->Ln(4);
	
	//	Imprimo los conceptos
	$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont('Courier', 'B', 12);
	$y = $pdf->GetY(); $pdf->Rect(5, $y, 202, 0.1);
	$pdf->Cell(104, 5, 'A S I G N A C I O N E S', 0, 0, 'C');	$pdf->Cell(104, 5, 'D E D U C C I O N E S', 0, 1, 'C');
	$y = $pdf->GetY(); $pdf->Rect(5, $y, 202, 0.1);
	$pdf->Ln(1);
	$y = $pdf->GetY();
	
	$yi = $y; $yd = $y;
	$sql = "SELECT 
					tnec.Monto, 
					tnec.Cantidad, 
					tnec.Saldo, 
					c.CodConcepto,
					c.TextoImpresion AS NomConcepto, 
					c.Tipo 
			FROM 
					pr_tiponominaempleadoconcepto tnec 
					INNER JOIN pr_concepto c ON (tnec.CodConcepto = c.CodConcepto) 
			WHERE 
					tnec.CodPersona = '".$persona."' AND 
					tnec.Periodo = '".$fperiodo."' AND 
					tnec.CodOrganismo = '".$forganismo."' AND 
					tnec.CodTipoNom = '".$ftiponom."' AND 
					tnec.CodTipoProceso = '".$ftproceso."' 
			ORDER BY 
					c.Tipo DESC, c.PlanillaOrden ASC";
	$query_conceptos=mysql_query($sql) or die ($sql.mysql_error()); $contador_conceptos = 0; $linead = 0; $lineai = 0;
	while ($field_conceptos = mysql_fetch_array($query_conceptos)) {
		$concepto = substr($field_conceptos['NomConcepto'], 0, 24);
		$monto = number_format($field_conceptos['Monto'], 2, ',', '.');
		$cantidad = number_format($field_conceptos['Cantidad'], 0, '', '.'); if ($cantidad == 0) $cantidad = "";
		
		$pdf->SetDrawColor(255, 255, 255); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
		$pdf->SetFont('Courier', 'B', 11);
		$pdf->SetWidths(array(70, 32, 1));
		$pdf->SetAligns(array('L', 'R', 'R'));
				
		if ($field_conceptos['Tipo'] == "I") {
			$pdf->SetXY(4, $yi); $pdf->Row(array(utf8_decode($concepto), $monto, ''));
			$yi += 5;
			$lineai++;
		} 
		elseif ($field_conceptos['Tipo'] == "D") {
			$pdf->SetXY(107, $yd); $pdf->Row(array(utf8_decode($concepto), $monto, ''));
			$yd += 5;
			$linead++;
		}
	}
	
	if ($lineai > $linead) $contador_conceptos = $lineai; else $contador_conceptos = $linead;
	
	
	if ($yi > $yd) $y = $yi; else $y = $yd;
	$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
	$pdf->Rect(5, $y, 202, 0.1);
	$pdf->Ln(1);
	$pdf->SetFont('Courier', 'B', 11);
	$pdf->SetY($y);
	$pdf->Cell(76, 5, 'TOTAL ASIGNACIONES', 0, 0, 'L'); $pdf->Cell(25, 5, $total_ingresos, 0, 0, 'R'); $pdf->Cell(1, 5);
	$pdf->Cell(76, 5, 'TOTAL DEDUCCIONES', 0, 0, 'L'); $pdf->Cell(25, 5, $total_egresos, 0, 1, 'R'); 
	$pdf->Cell(102, 5);	
	$pdf->Cell(76, 5, 'TOTAL A PAGAR', 0, 0, 'L'); $pdf->Cell(25, 5, $total_neto, 0, 1, 'R');
	
	$pdf->Ln(7); 
	
	$pdf->SetX(20); $pdf->Cell(80, 5, '____________________', 0, 1, 'C');
	$pdf->SetX(20); $pdf->Cell(80, 5, 'RECIBI CONFORME', 0, 0, 'C');
	
	
	$total_ingresos=0;
	$total_egresos=0;
	$total_neto=0;
}

//	--------------------------------------------------

$pdf->Output();
?>