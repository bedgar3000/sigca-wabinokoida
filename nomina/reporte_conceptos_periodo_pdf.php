<?php
define('FPDF_FONTPATH','font/');
require('mc_table3.php');
require('fphp_nomina.php');
connect();
//---------------------------------------------------

//---------------------------------------------------
//	Imprime la cabedera del documento
function Cabecera($pdf, $ftiponom, $nomina, $proceso, $periodo, $periodo_fecha, $nom_concepto) {
	$pdf->AddPage();
	$pdf->SetDrawColor(255, 255, 255); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont('Arial', 'B', 8);	
	$pdf->Cell(190, 5, ('ALCALDIA BOLIVARAIANA DE ANGOSTURA'), 0, 1, 'L');
	$pdf->Cell(190, 5, ('DIRECCION DE RECURSOS HUMANOS'), 0, 1, 'L');
	$pdf->Cell(190, 5, ('REPORTE DEL CONCEPTO '.$nom_concepto), 0, 1, 'L');
	$pdf->Cell(190, 5, ('TIPO DE NOMINA '.$nomina), 0, 1, 'L');
	$pdf->Cell(190, 5, ('AÑO: '.$periodo), 0, 1, 'L');
	$pdf->Ln(20);	
	$pdf->SetFont('Arial', 'B', 8);
	$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
	$pdf->SetWidths(array(20, 30));
	$pdf->SetAligns(array('C', 'R'));
	$pdf->Cell(65, 5); $pdf->Row(array('PERIODO', 'MONTO'));
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

Cabecera($pdf, $ftiponom, $field_nomina['Nomina'], $field_proceso['Descripcion'], $fperiodo, $periodo_fecha, $field_concepto['Descripcion']);

//	Cuerpo
$sql = "SELECT
			ptnec.Periodo,
			SUM(ptnec.Monto) AS Monto
		FROM
			pr_tiponominaempleadoconcepto ptnec
		WHERE
			ptnec.CodOrganismo = '".$forganismo."' AND
			ptnec.CodTipoNom = '".$ftiponom."' AND
			ptnec.CodTipoProceso = '".$ftproceso."' AND
			SUBSTRING(ptnec.Periodo, 1, 4) = '".$fperiodo."' AND
			ptnec.CodConcepto = '".$codconcepto."'
		GROUP BY Periodo
		ORDER BY Periodo";
$query = mysql_query($sql) or die ($sql.mysql_error());
while ($field = mysql_fetch_array($query)) {
	$sum_monto += $field['Monto'];
	$pdf->SetFont('Arial', '', 8);
	$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
	$pdf->SetWidths(array(20, 30));
	$pdf->SetAligns(array('R', 'R'));
	$pdf->Cell(65, 5);
	$pdf->Row(array($field['Periodo'], 
					number_format($field['Monto'], 2, ',', '.')));
	if ($pdf->GetY() > 260) Cabecera($pdf, $ftiponom, $field_nomina['Nomina'], $field_proceso['Descripcion'], $fperiodo, $periodo_fecha, $field_concepto['Descripcion']);
}
//---------------------------------------------------
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(65, 5); $pdf->Row(array('TOTAL', number_format($sum_monto, 2, ',', '.')));
//---------------------------------------------------
if ($pdf->GetY() > 260) Cabecera($pdf, $ftiponom, $field_nomina['Nomina'], $field_proceso['Descripcion'], $fperiodo, $periodo_fecha, $field_concepto['Descripcion']);
$y = $pdf->GetY() + 5;
//---------------------------------------------------
$pdf->Output();
?>