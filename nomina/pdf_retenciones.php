<?php
define('FPDF_FONTPATH','font/');
require('mc_table3.php');
require('fphp_nomina.php');
connect();
//---------------------------------------------------

//---------------------------------------------------
//	Imprime la cabedera del documento
function Cabecera($pdf, $ftiponom, $nomina, $proceso, $periodo, $periodo_fecha, $nom_concepto) {
	global $flagsueldo;
	global $forganismo;
	$pdf->AddPage();
	$pdf->SetDrawColor(255, 255, 255); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont('Arial', 'B', 8);
	
	$sql = "SELECT * FROM mastparametros WHERE ParametroClave = 'PATHLOGO'";
	$query_param = mysql_query($sql) or die(mysql_error());
	if (mysql_num_rows($query_param)) $field_param = mysql_fetch_array($query_param);
	
	$sql = "SELECT Logo FROM mastorganismos WHERE CodOrganismo = '".$forganismo."'";
	$query_organismo = mysql_query($sql) or die(mysql_error());
	if (mysql_num_rows($query_organismo)) $field_organismo = mysql_fetch_array($query_organismo);
	
	$pdf->Image($field_param['ValorParam'].$field_organismo['Logo'], 10, 15, 18, 18);
	$pdf->SetX(30); $pdf->Cell(190, 5, ('ALCALDIA BOLIVARAIANA DE ANGOSTURA'), 0, 1, 'L');
	$pdf->SetX(30); $pdf->Cell(190, 5, ('DIRECCION DE RECURSOS HUMANOS'), 0, 1, 'L');
	$pdf->SetX(30); $pdf->Cell(190, 5, utf8_decode('RESUMEN DE NOMINA '.$nom_concepto), 0, 1, 'L');
	$pdf->SetX(30); $pdf->Cell(190, 5, utf8_decode('TIPO DE NOMINA '.$nomina), 0, 1, 'L');
	$pdf->SetX(30); $pdf->Cell(190, 5, utf8_decode($proceso), 0, 1, 'L');
	$pdf->Ln(1);
	$pdf->SetFont('Arial', '', 8);
	$pdf->Cell(190, 5, ($periodo_fecha), 0, 1, 'C');
	$pdf->Ln(3);
	
	$pdf->SetFont('Arial', 'B', 8);
	$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
	if ($flagsueldo == "S") {
		$pdf->SetWidths(array(20, 110, 30, 30));
		$pdf->SetAligns(array('C', 'L', 'C', 'C'));
		$pdf->Row(array('CEDULA', 'APELLIDOS Y NOMBRES', 'SALARIO', $nom_concepto));
	} else {
		$pdf->SetWidths(array(20, 110, 30, 30));
		$pdf->SetAligns(array('C', 'L', 'C', 'C'));
		$pdf->Row(array('CEDULA', 'APELLIDOS Y NOMBRES', 'CANTIDAD', $nom_concepto));
	}
}
//---------------------------------------------------

//---------------------------------------------------
//	Creación del objeto de la clase heredada
$pdf = new PDF_MC_Table();
$pdf->Open();
$pdf->SetMargins(10, 15, 10);

//	Tipo de Nomina
$sql = "SELECT Nomina FROM tiponomina WHERE CodTipoNom = '".$ftiponom."'";
$query_nomina = mysql_query($sql) or die ($sql.mysql_error());
if (mysql_num_rows($query_nomina) != 0) $field_nomina = mysql_fetch_array($query_nomina);

//	Tipo de Proceso
$sql = "SELECT Descripcion FROM pr_tipoproceso WHERE CodTipoProceso = '".$ftproceso."'";
$query_proceso = mysql_query($sql) or die ($sql.mysql_error());
if (mysql_num_rows($query_proceso) != 0) $field_proceso = mysql_fetch_array($query_proceso);

//	Concepto
$sql = "SELECT Descripcion FROM pr_concepto WHERE CodConcepto = '".$codconcepto."'";
$query_concepto = mysql_query($sql) or die ($sql.mysql_error());
if (mysql_num_rows($query_concepto) != 0) $field_concepto = mysql_fetch_array($query_concepto);

//	Periodo
list($fecha_desde, $fecha_hasta) = getPeriodoProceso($ftproceso, $fperiodo, $ftiponom);
$periodo_fecha = "DESDE: ".formatFechaDMA($fecha_desde)." HASTA: ".formatFechaDMA($fecha_hasta);


Cabecera($pdf, $ftiponom, $field_nomina['Nomina'], $field_proceso['Descripcion'], $periodo, $periodo_fecha, $field_concepto['Descripcion']);

//	Cuerpo
$TotalCantidad = 0;
$sql = "SELECT 
			mp.CodPersona,
			mp.Ndocumento,
			mp.NomCompleto AS Busqueda,
			ptne.TotalIngresos,
			(SELECT SUM(TotalIngresos) 
				FROM pr_tiponominaempleado 
					WHERE CodPersona = mp.CodPersona AND CodTipoNom = '".$ftiponom."' AND Periodo = '".$fperiodo."') AS TotalIngresosMes,
			ptnec.Monto,
			ptnec.Cantidad
		FROM
			mastpersonas mp
			INNER JOIN pr_tiponominaempleado ptne ON (mp.CodPersona = ptne.CodPersona)
			INNER JOIN pr_tiponominaempleadoconcepto ptnec ON (ptne.CodPersona = ptnec.CodPersona AND ptne.CodTipoNom = ptnec.CodTipoNom AND ptne.Periodo = ptnec.Periodo AND ptne.CodTipoproceso = ptnec.CodTipoProceso AND ptnec.CodConcepto = '".$codconcepto."')
		WHERE
			ptne.CodTipoNom = '".$ftiponom."' AND
			ptne.Periodo = '".$fperiodo."' AND
			ptne.CodTipoProceso = '".$ftproceso."'
		ORDER BY length(mp.Ndocumento), mp.Ndocumento";
$query = mysql_query($sql) or die ($sql.mysql_error());
while ($field = mysql_fetch_array($query)) {
	$sum_ingresos += $field['TotalIngresos'];
	$sum_retenciones += $field['Monto'];
	$sum_aportes += $field['Aporte'];
	$pdf->SetFont('Arial', '', 8);
	$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
	if ($flagsueldo == "S") {
		$pdf->SetWidths(array(20, 110, 30, 30));
		$pdf->SetAligns(array('R', 'L', 'R', 'R'));
		$pdf->Row(array(number_format($field['Ndocumento'], 0, '', '.'), $field['Busqueda'], number_format($field['TotalIngresos'], 2, ',', '.'), number_format($field['Monto'], 2, ',', '.')));
	} else {
		$pdf->SetWidths(array(20, 110, 30, 30));
		$pdf->SetAligns(array('R', 'L', 'C', 'R'));
		$pdf->Row(array(number_format($field['Ndocumento'], 0, '', '.'), $field['Busqueda'], floatval($field['Cantidad']), number_format($field['Monto'], 2, ',', '.')));
	}
	if ($pdf->GetY() > 260) Cabecera($pdf, $ftiponom, $field_nomina['Nomina'], $field_proceso['Descripcion'], $periodo, $periodo_fecha, $field_concepto['Descripcion']);
	$TotalCantidad += $field['Cantidad'];
}
//---------------------------------------------------
$pdf->SetFont('Arial', 'B', 8);
if ($flagsueldo == "S") {
	$pdf->Row(array('', 'TOTAL', number_format($sum_ingresos, 2, ',', '.'), number_format($sum_retenciones, 2, ',', '.')));
} else {
	$pdf->Row(array('', 'TOTAL', $TotalCantidad, number_format($sum_retenciones, 2, ',', '.')));
}
//---------------------------------------------------
if ($pdf->GetY() > 260) Cabecera($pdf, $ftiponom, $field_nomina['Nomina'], $field_proceso['Descripcion'], $periodo, $periodo_fecha, $field_concepto['Descripcion']);

$y = $pdf->GetY() + 5;

//---------------------------------------------------

//---------------------------------------------------
/*list($nomelaborado, $carelaborado) = getFirmaNomina($ftiponom, $fperiodo, $ftproceso, "ProcesadoPor");
list($nomaprobado, $caraprobado) = getFirmaNomina($ftiponom, $fperiodo, $ftproceso, "AprobadoPor");
//---------------------------------------------------
$pdf->Rect(10, $y+5, 70, 0.1, "DF");
$pdf->Rect(120, $y+5, 70, 0.1, "DF");
$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(10, $y+10);
$pdf->Cell(110, 4, utf8_decode('ELABORADO POR:'), 0, 0, 'L');
$pdf->Cell(80, 4, utf8_decode('CONFORMADO POR:'), 0, 1, 'L');
$pdf->Cell(110, 4, utf8_decode($nomelaborado), 0, 0, 'L');
$pdf->Cell(80, 4, utf8_decode($nomaprobado), 0, 1, 'L');
$pdf->Cell(110, 4, utf8_decode($carelaborado), 0, 0, 'L');
//$pdf->Cell(80, 4, utf8_decode($caraprobado), 0, 1, 'L');
$pdf->Cell(80, 4, utf8_decode('DIRECTORA DE RECURSOS HUMANOS (E)'), 0, 1, 'L');*/
//---------------------------------------------------
$pdf->Output();
?>  
