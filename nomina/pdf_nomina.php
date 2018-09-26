<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");

$sql = "SELECT * FROM mastorganismos WHERE CodOrganismo = '$forganismo'";
$query_organismo = mysql_query($sql) or die ($sql.mysql_error());
$field_organismo = mysql_fetch_array($query_organismo);

class PDF extends FPDF {
	//	Cabecera de página.
	function Header() {
		global $_PARAMETRO;
		global $FechaActual;
		##	-------------------
		$this->SetFont('Arial', '', 7);
		$this->SetXY(175, 2); $this->Cell(10, 5, utf8_decode('Fecha: '), 0, 0, 'L');
		$this->Cell(30, 5, formatFechaDMA($FechaActual), 0, 1, 'L');
		$this->SetXY(175, 7); $this->Cell(10, 5, utf8_decode('Página: '), 0, 0, 'L');
		$this->Cell(30, 5, $this->PageNo().' de {nb}', 0, 1, 'L');
		##	-------------------
		$this->SetY(5); 
	}
	
	//	Pie de página.
	function Footer() {}
}

//	--------------------------------------------------
//	Creación del objeto de la clase heredada
$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->SetMargins(5, 5, 5);
$pdf->SetAutoPageBreak(10);


// Obtengo la fecha del payroll	...
$sql = "SELECT * FROM pr_procesoperiodo WHERE CodOrganismo = '".$forganismo."' AND Periodo = '".$fperiodo."' AND CodTipoNom = '".$ftiponom."' AND CodTipoProceso = '".$ftproceso."'";
$query_fecha = mysql_query($sql) or die ($sql.mysql_error());
if (mysql_num_rows($query_fecha) != 0) $field_fecha = mysql_fetch_array($query_fecha);
list($a, $m, $d)=SPLIT( '[-./]', $field_fecha['FechaDesde']); $de= "$d/$m/$a";
list($a, $m, $d)=SPLIT( '[-./]', $field_fecha['FechaHasta']); $a= "$d/$m/$a";

$pdf->AddPage();
$pdf->SetFont('Courier', 'B', 12);
$pdf->SetXY(5, $y); $pdf->Cell(150, 8, utf8_decode($field_organismo['Organismo']), 0, 1, 'L');
$pdf->SetFont('Courier', 'B', 12);	
$pdf->Cell(100, 5, ('De: '.$de.' A: '.$a), 0, 0, 'L');
$pdf->Cell(100, 5, utf8_decode($nomproceso), 0, 1, 'R');


//	Imprimo el tiupo de nomina
$sql = "SELECT * FROM tiponomina WHERE CodTipoNom = '".$ftiponom."'";
$query_nomina = mysql_query($sql) or die ($sql.mysql_error());
if (mysql_num_rows($query_nomina) != 0) $field_nomina = mysql_fetch_array($query_nomina);
$pdf->SetFont('Courier', 'B', 14);
$pdf->Cell(205, 5, strtoupper(utf8_decode($field_nomina['TituloBoleta'])), 0, 1, 'C');
//$pdf->AddPage();
$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(0, 0, 0); $y=$pdf->GetY(); $pdf->Rect(5, $y, 202, 0.4, 'DF');

$emp = 0;
$TotalIngresos=0;
$TotalEgresos=0;
//	Imprimo los datos
$sql = "SELECT 
			ptne.CodPersona,
			mp.Ndocumento 
		FROM 
			pr_tiponominaempleado ptne
			INNER JOIN mastpersonas mp ON (ptne.CodPersona = mp.CodPersona)
			INNER JOIN mastempleado e ON (e.CodPersona = mp.CodPersona)
		WHERE 
			ptne.Periodo = '".$fperiodo."' AND 
			ptne.CodTipoNom = '".$ftiponom."' AND 
			ptne.CodTipoProceso = '".$ftproceso."'
		ORDER BY e.CodDependencia, LENGTH(mp.Ndocumento), mp.Ndocumento";
$query_empleado = mysql_query($sql) or die ($sql.mysql_error());
$nro_empleado = mysql_num_rows($query_empleado);
while ($field_empleado = mysql_fetch_array($query_empleado)) {
	$persona = $field_empleado['CodPersona'];

	$emp++;
	$hoy = date("d/m/Y");
	
	$y = $pdf->GetY() + 2; 
	if ($y > 220) {$pdf->AddPage();$pdf->SetY(15);} else $pdf->SetY($y);
	
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
				tne.SueldoBasico,
				md.Dependencia, 
				me.CodEmpleado, 
				mp.NomCompleto, 
				rp.DescripCargo,
				rbp.CodBeneficiario,
				rbp.NroDocumento,
				rbp.NombreCompleto,
				cp.CategoriaProg,
				ue.Denominacion AS UnidadEjecutora,
				CONCAT(ss.CodSector, pr.CodPrograma, a.CodActividad) AS CodUnidad
			FROM mastempleado me 
			INNER JOIN rh_puestos rp ON (me.CodCargo = rp.CodCargo)
			INNER JOIN mastdependencias md ON (me.CodDependencia = md.CodDependencia) 
			INNER JOIN mastpersonas mp ON (me.CodPersona = mp.CodPersona)
			INNER JOIN tiponomina tn ON (me.CodTipoNom = tn.CodTipoNom)
			LEFT JOIN bancopersona bp ON (me.CodPersona = bp.CodPersona AND bp.FlagPrincipal = 'S')
			LEFT JOIN rh_beneficiariopension rbp ON (me.CodPersona = rbp.CodPersona)
			INNER JOIN pr_tiponominaempleado tne ON (me.CodPersona = tne.CodPersona AND tne.Periodo = '$fperiodo' AND tne.CodOrganismo = '$forganismo' AND tne.CodTipoProceso = '$ftproceso' AND tne.CodTipoNom = '$ftiponom')
			LEFT JOIN pv_categoriaprog cp ON (cp.CategoriaProg = me.CategoriaProg)
			LEFT JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cp.CodUnidadEjec)
			LEFT JOIN pv_actividades a ON (a.IdActividad = cp.IdActividad)
			LEFT JOIN pv_proyectos py ON (py.IdProyecto = a.IdProyecto)
			LEFT JOIN pv_subprogramas spr ON (spr.IdSubPrograma = py.IdSubPrograma)
			LEFT JOIN pv_programas pr ON (pr.IdPrograma = spr.IdPrograma)
			LEFT JOIN pv_subsector ss ON (ss.IdSubSector = pr.IdSubSector)
			WHERE me.CodPersona = '".$persona."'";
	$query_resto = mysql_query($sql) or die ($sql.mysql_error());
	if (mysql_num_rows($query_resto) != 0) $field_resto = mysql_fetch_array($query_resto);
	if ($field_resto['CodBeneficiario'] != "") {
		$nomcompleto = substr($field_resto['NomCompleto']." (".$field_resto['NombreCompleto'].")", 0, 45);
		$cargo = ("Beneficiario Pensión por Sobreviviente");
		$fingreso = "";
	} else {
		$nomcompleto = substr($field_resto['NomCompleto'], 0, 45);
		if ($nomina == "04") $cargo = ("Pensión por Inválidez");
		elseif ($nomina == "03") $cargo = "Jubilado";
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

	$pdf->SetFont('Courier', 'B', 13);
	$dependencia = substr(utf8_decode($field_resto['Dependencia']), 0, 45);
	$pdf->Cell(20, 5, utf8_decode('Código'), 0, 0, 'L'); $pdf->Cell(3, 5);
	$pdf->Cell(30, 5, utf8_decode('Cédula'), 0, 0, 'L'); $pdf->Cell(3, 5);
	$pdf->Cell(115, 5, 'Empleado', 0, 0, 'L'); $pdf->Cell(3, 5);
	$pdf->Cell(30, 5, 'Fecha Ing.', 0, 1, 'L');
	$pdf->Cell(20, 5, $codempleado, 0, 0, 'L'); $pdf->Cell(3, 5);
	$pdf->Cell(30, 5, $ndoc, 0, 0, 'L'); $pdf->Cell(3, 5);
	$pdf->Cell(115, 5, utf8_decode($nomcompleto), 0, 0, 'L'); $pdf->Cell(3, 5);
	$pdf->Cell(30, 5, $fingreso, 0, 1, 'L');
	$pdf->Cell(23, 5, 'Cargo: ', 0, 0, 'L');
	$pdf->Cell(155, 5, utf8_decode($cargo), 0, 0, 'L');
	$pdf->Cell(30, 5, 'Salario', 0, 1, 'L');
	$pdf->Cell(23, 5, 'Unidad: ', 0, 0, 'L');
	$pdf->Cell(150, 5, substr(utf8_decode($field_resto['CodUnidad'].'-'.$field_resto['UnidadEjecutora']), 0, 50), 0, 0, 'L');
	$pdf->Cell(30, 5, number_format($field_resto['SueldoBasico'],2,',','.'), 0, 1, 'R');
	$pdf->Ln(4);

	//	Imprimo los conceptos
	$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont('Courier', 'B', 11);
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
			FROM pr_tiponominaempleadoconcepto tnec 
			INNER JOIN pr_concepto c ON (tnec.CodConcepto = c.CodConcepto) 
			WHERE 
				tnec.CodPersona = '$persona' AND 
				tnec.Periodo = '$fperiodo' AND 
				tnec.CodOrganismo = '$forganismo' AND 
				tnec.CodTipoNom = '$ftiponom' AND 
				tnec.CodTipoProceso = '$ftproceso' 
			ORDER BY c.Tipo DESC, c.PlanillaOrden ASC";
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
			if ($yi >= 271) { $pdf->AddPage(); $yi = $pdf->GetY()+10; $yd = $pdf->GetY()+10; }
			$pdf->SetXY(4, $yi); $pdf->Row(array(utf8_decode($concepto), $monto, ''));
			$yi += 5;
			$lineai++;
		} 
		elseif ($field_conceptos['Tipo'] == "D") {
			if ($yd >= 271) { $pdf->AddPage(); $yi = $pdf->GetY()+10; $yd = $pdf->GetY()+10; }
			$pdf->SetXY(107, $yd); $pdf->Row(array(utf8_decode($concepto), $monto, ''));
			$yd += 5;
			$linead++;
		}
	}
	
	if ($lineai > $linead) $contador_conceptos = $lineai; else $contador_conceptos = $linead;

	if ($yi > $yd) $y = $yi + 2; else $y = $yd + 2;
	$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
	$pdf->Rect(5, $y, 202, 0.1);
	$pdf->Ln(1);
	$pdf->SetFont('Courier', 'B', 11);
	$pdf->SetY($y);
	$pdf->Cell(76, 5, 'TOTAL ASIGNACIONES', 0, 0, 'L'); $pdf->Cell(25, 5, $total_ingresos, 0, 0, 'R'); $pdf->Cell(1, 5);
	$pdf->Cell(76, 5, 'TOTAL DEDUCCIONES', 0, 0, 'L'); $pdf->Cell(25, 5, $total_egresos, 0, 1, 'R'); 
	$pdf->Cell(102, 5);	
	$pdf->Cell(76, 5, 'TOTAL A PAGAR', 0, 0, 'L'); $pdf->Cell(25, 5, $total_neto, 0, 1, 'R');
		$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(0, 0, 0); $y=$pdf->GetY(); $pdf->Rect(5, $y, 202, 0.4, 'DF');
	
	$pdf->Ln(3); 

	$TotalIngresos += $field_resto['TotalIngresos'];
	$TotalEgresos += $field_resto['TotalEgresos'];
	
	
	$total_ingresos=0;
	$total_egresos=0;
	$total_neto=0;
}
##	RESUMEN GENERAL
$pdf->Ln(7);
$y = $pdf->GetY() + 2; 
if ($y > 220) {$pdf->AddPage();$pdf->SetY(15);}
$TotalNeto = $TotalIngresos - $TotalEgresos;
$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(0, 0, 0); $y=$pdf->GetY(); $pdf->Rect(5, $y-1, 202, 0.5, 'DF'); $pdf->Rect(5, $y-2, 202, 0.5, 'DF');
$pdf->SetFont('Courier', 'B', 11);
$pdf->Cell(195, 5, 'RESUMEN GENERAL', 0, 1, 'C');
$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(0, 0, 0); $y=$pdf->GetY(); $pdf->Rect(5, $y, 202, 0.5, 'DF');
//	Imprimo los conceptos
$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Courier', 'B', 11);
$y = $pdf->GetY(); $pdf->Rect(5, $y, 202, 0.1);
$pdf->Cell(104, 5, 'A S I G N A C I O N E S', 0, 0, 'C');	$pdf->Cell(104, 5, 'D E D U C C I O N E S', 0, 1, 'C');
$y = $pdf->GetY(); $pdf->Rect(5, $y, 202, 0.1);
$pdf->Ln(1);
$y = $pdf->GetY();

$yi = $y; $yd = $y;
$sql = "SELECT 
			SUM(tnec.Monto) AS Monto, 
			SUM(tnec.Cantidad) AS Cantidad, 
			tnec.Saldo, 
			c.CodConcepto,
			c.TextoImpresion AS NomConcepto, 
			c.Tipo,
			c.FlagRetencion
		FROM 
			pr_tiponominaempleadoconcepto tnec 
			INNER JOIN pr_concepto c ON (tnec.CodConcepto = c.CodConcepto) 
		WHERE 
			tnec.Periodo = '".$fperiodo."' AND 
			tnec.CodOrganismo = '".$forganismo."' AND 
			tnec.CodTipoNom = '".$ftiponom."' AND 
			tnec.CodTipoProceso = '".$ftproceso."' 
		GROUP BY CodConcepto
		ORDER BY c.Tipo DESC, c.PlanillaOrden ASC";
$query_conceptos=mysql_query($sql) or die ($sql.mysql_error()); $contador_conceptos = 0; $linead = 0; $lineai = 0;
while ($field_conceptos = mysql_fetch_array($query_conceptos)) {
	$concepto = substr($field_conceptos['NomConcepto'], 0, 20);
	$monto = number_format($field_conceptos['Monto'], 2, ',', '.');
	$cantidad = number_format($field_conceptos['Cantidad'], 0, '', '.'); if ($cantidad == 0) $cantidad = "";
	
	$pdf->SetDrawColor(255, 255, 255); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont('Courier', 'B', 11);
	$pdf->SetWidths(array(50, 20, 32, 1));
	$pdf->SetAligns(array('L', 'R', 'R', 'R'));
			
	if ($field_conceptos['Tipo'] == "I") {
		$pdf->SetXY(4, $yi); $pdf->Row(array(utf8_decode($concepto), $cantidad, $monto, ''));
		$yi += 5;
		$lineai++;
	} 
	elseif ($field_conceptos['Tipo'] == "D") {
		$pdf->SetXY(107, $yd); $pdf->Row(array(utf8_decode($concepto), '', $monto, ''));
		$yd += 5;
		$linead++;
	}
}
##	
if ($yi > $yd) $y = $yi + 2; else $y = $yd + 2;
$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
$pdf->Rect(5, $y, 202, 0.1);
$pdf->Ln(1);
$pdf->SetFont('Courier', 'B', 11);
$pdf->SetY($y);
$pdf->Cell(76, 5, 'TOTAL ASIGNACIONES', 0, 0, 'L'); $pdf->Cell(25, 5, number_format($TotalIngresos,2,',','.'), 0, 0, 'R'); $pdf->Cell(1, 5);
$pdf->Cell(76, 5, 'TOTAL DEDUCCIONES', 0, 0, 'L'); $pdf->Cell(25, 5, number_format($TotalEgresos,2,',','.'), 0, 1, 'R'); 
$pdf->Cell(102, 5);	
$pdf->Cell(76, 5, 'TOTAL A PAGAR', 0, 0, 'L'); $pdf->Cell(25, 5, number_format($TotalNeto,2,',','.'), 0, 1, 'R');
$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(0, 0, 0); $y=$pdf->GetY(); $pdf->Rect(5, $y, 202, 0.5, 'DF');
##	TOTAL GENERAL
/*$pdf->Ln(7);
$y = $pdf->GetY() + 2; 
if ($y > 220) $pdf->AddPage();
$TotalNeto = $TotalIngresos - $TotalEgresos;
$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(0, 0, 0); $y=$pdf->GetY(); $pdf->Rect(5, $y-1, 202, 0.5, 'DF'); $pdf->Rect(5, $y-2, 202, 0.5, 'DF');
$pdf->SetFont('Courier', 'B', 11);
$pdf->Cell(195, 5, 'TOTAL GENERAL', 0, 1, 'C');
$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(0, 0, 0); $y=$pdf->GetY(); $pdf->Rect(5, $y, 202, 0.5, 'DF');
$pdf->Cell(76, 5, 'TOTAL ASIGNACIONES', 0, 0, 'L'); $pdf->Cell(25, 5, number_format($TotalIngresos,2,',','.'), 0, 0, 'R'); $pdf->Cell(1, 5);
$pdf->Cell(76, 5, 'TOTAL DEDUCCIONES', 0, 0, 'L'); $pdf->Cell(25, 5, number_format($TotalEgresos,2,',','.'), 0, 1, 'R'); 
$pdf->Cell(102, 5);	
$pdf->Cell(76, 5, 'TOTAL A PAGAR', 0, 0, 'L'); $pdf->Cell(25, 5, number_format($TotalNeto,2,',','.'), 0, 1, 'R');
$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(0, 0, 0); $y=$pdf->GetY(); $pdf->Rect(5, $y, 202, 0.5, 'DF');*/

$pdf->SetFont('Courier', 'B', 11);
$pdf->Cell(195, 5, 'NRO DE TRABAJADORES: '.$nro_empleado, 0, 1, 'L');


//}

//	--------------------------------------------------

$pdf->Output();
?>