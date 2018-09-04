<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$filtro = '';
if ($fCodOrganismo != "") $filtro.=" AND (le.CodOrganismo = '".$fCodOrganismo."')";
if ($fCodTipoNom != "") $filtro.=" AND (le.CodTipoNom = '".$fCodTipoNom."')";
if ($fCodDependencia != "") $filtro.=" AND (e.CodDependencia = '".$fCodDependencia."')";
if ($fFliquidacionD != "" || $fFliquidacionH != "") {
    if ($fFliquidacionD != "") $filtro.=" AND (le.Fliquidacion >= '".formatFechaAMD($fFliquidacionD)."')";
    if ($fFliquidacionH != "") $filtro.=" AND (le.Fliquidacion <= '".formatFechaAMD($fFliquidacionH)."')";
}
if ($fCodPersona != "") $filtro.=" AND (le.CodPersona = '".$fCodPersona."')";
if ($fCodMotivoCes != "") $filtro.=" AND (le.CodMotivoCes = '".$fCodMotivoCes."')";
if ($fFlagPendientes == "S") $filtro.=" AND ((le.TotalPrestaciones - le.MontoPagado) > 0.00)";
$fOrderBy = "CodOrganismo";
//---------------------------------------------------

class PDF extends FPDF {
	//	Cabecera de página.
	function Header() {
		global $_PARAMETRO;
		global $Ahora;
		global $field;
		global $_POST;
		extract($_POST);
		##
		$Logo = getValorCampo("mastorganismos", "CodOrganismo", "Logo", $fCodOrganismo);
		$NomOrganismo = getValorCampo("mastorganismos", "CodOrganismo", "Organismo", $fCodOrganismo);
		$NomDependencia = getValorCampo("mastdependencias", "CodDependencia", "Dependencia", $_PARAMETRO["DEPAT"]);
		##
		$this->SetFillColor(255, 255, 255);
		$this->SetDrawColor(0, 0, 0);
		$this->Image($_PARAMETRO["PATHLOGO"].$Logo, 5, 5, 10, 10);		
		$this->SetFont('Arial', '', 8);
		$this->SetXY(15, 5); $this->Cell(100, 5, utf8_decode($NomOrganismo), 0, 1, 'L');
		$this->SetXY(15, 10); $this->Cell(100, 5, utf8_decode($NomDependencia), 0, 0, 'L');	
		$this->SetFont('Arial', '', 8);
		$this->SetXY(240, 5); $this->Cell(15, 5, utf8_decode('Fecha: '), 0, 0, 'L');
		$this->Cell(30, 5, formatFechaDMA(substr($Ahora, 0, 10)), 0, 1, 'L');
		$this->SetXY(240, 10); $this->Cell(15, 5, utf8_decode('Página: '), 0, 0, 'L'); 
		$this->Cell(30, 5, $this->PageNo().' de {nb}', 0, 1, 'L');
		$this->SetFont('Arial', 'B', 10);
		$this->SetY(20); $this->Cell(270, 5, utf8_decode('LISTADO DE LIQUIDACIONES'), 0, 1, 'C');
		##	-------------------
		$this->SetTextColor(0,0,0);
		$this->SetDrawColor(0,0,0);
		$this->SetFillColor(225,225,225);
		$this->Ln(3);
		##
		$this->SetFont('Arial', 'B', 7);
		$this->SetWidths(array(20,70,20,20,20,20,20,20,20,20,20));
		$this->SetAligns(array('R','L','C','R','R','R','R','R','R','R','R'));
		$this->Row(array(utf8_decode('Cédula'),
						 utf8_decode('Empleado'),
						 utf8_decode('Fecha Liquidación'),
						 utf8_decode('Total Ingresos'),
						 utf8_decode('Total Egresos'),
						 utf8_decode('Total Adelantos'),
						 utf8_decode('Total Neto'),
						 utf8_decode('Monto Intereses'),
						 utf8_decode('Monto Total'),
						 utf8_decode('Monto Pagado'),
						 utf8_decode('Monto Pendiente')
		));
		$this->Ln(1);
	}
	
	//	Pie de página.
	function Footer() {}
}
//---------------------------------------------------

//---------------------------------------------------
//	Creación del objeto de la clase heredada.
$pdf = new PDF('L', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->SetMargins(5, 5, 5);
$pdf->SetAutoPageBreak(5, 5);
//---------------------------------------------------
//	consulto
$i = 0;
$TotalIngresos = 0;
$TotalEgresos = 0;
$TotalDescuento = 0;
$TotalNeto = 0;
$MontoIntereses = 0;
$TotalPrestaciones = 0;
$MontoPagado = 0;
$SumMontoPendiente = 0;
$sql = "SELECT
            le.*,
            e.CodEmpleado,
            p1.Ndocumento,
            p1.NomCompleto,
            p2.NomCompleto AS NomProcesadoPor
        FROM
            pr_liquidacionempleado le
            INNER JOIN mastempleado e ON (e.CodPersona = le.CodPersona)
            INNER JOIN mastpersonas p1 ON (p1.CodPersona = le.CodPersona)
            INNER JOIN mastpersonas p2 ON (p2.CodPersona = le.ProcesadoPor)
            LEFT JOIN rh_motivocese cs ON (cs.CodMotivoCes = le.CodMotivoCes)
        WHERE 1 $filtro
        ORDER BY LENGTH(p1.Ndocumento), p1.Ndocumento";
$field = getRecords($sql);
if (count($field) > 0) $pdf->AddPage();
foreach($field as $f) {
	$MontoPendiente = $f['TotalPrestaciones'] - $f['MontoPagado'];
	##	
	$pdf->SetTextColor(0,0,0);
	$pdf->SetDrawColor(255,255,255);
	if ($i % 2 == 0) { $pdf->SetFillColor(255,255,255); $pdf->SetDrawColor(255,255,255); }
	else { $pdf->SetFillColor(225,225,225); $pdf->SetDrawColor(225,225,225); }
	$pdf->SetFont('Arial', '', 7);
	$pdf->Row(array(number_format($f['Ndocumento'],0,'','.'),
					utf8_decode($f['NomCompleto']),
					formatFechaDMA($f['Fliquidacion']),
					number_format($f['TotalIngresos'],2,',','.'),
					number_format($f['TotalEgresos'],2,',','.'),
					number_format($f['TotalDescuento'],2,',','.'),
					number_format($f['TotalNeto'],2,',','.'),
					number_format($f['MontoIntereses'],2,',','.'),
					number_format($f['TotalPrestaciones'],2,',','.'),
					number_format($f['MontoPagado'],2,',','.'),
					number_format($MontoPendiente,2,',','.')
	));
	$TotalIngresos += $f['TotalIngresos'];
	$TotalEgresos += $f['TotalEgresos'];
	$TotalDescuento += $f['TotalDescuento'];
	$TotalNeto += $f['TotalNeto'];
	$MontoIntereses += $f['MontoIntereses'];
	$TotalPrestaciones += $f['TotalPrestaciones'];
	$MontoPagado += $f['MontoPagado'];
	$SumMontoPendiente += $MontoPendiente;
	$pdf->Ln(2);
	++$i;
}
$pdf->Ln(3);
$pdf->SetTextColor(0,0,0);
$pdf->SetDrawColor(255,255,255);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial', 'B', 7);
$pdf->Row(array('',
				'TOTAL',
				'',
				number_format($TotalIngresos,2,',','.'),
				number_format($TotalEgresos,2,',','.'),
				number_format($TotalDescuento,2,',','.'),
				number_format($TotalNeto,2,',','.'),
				number_format($MontoIntereses,2,',','.'),
				number_format($TotalPrestaciones,2,',','.'),
				number_format($MontoPagado,2,',','.'),
				number_format($SumMontoPendiente,2,',','.')
));

//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
