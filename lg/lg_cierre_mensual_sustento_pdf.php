<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$LF = 0x0A;
$CR = 0x0D;
$nl = sprintf("%c%c",$CR,$LF);
//---------------------------------------------------

class PDF extends FPDF {
	//	Cabecera de página.
	function Header() {
		global $_PARAMETRO;
		global $CodOrganismo;
		global $Periodo;
		global $Ahora;
		global $field;
		global $_POST;
		extract($_POST);
		##
		$Logo = getValorCampo("mastorganismos", "CodOrganismo", "Logo", $CodOrganismo);
		$NomOrganismo = getValorCampo("mastorganismos", "CodOrganismo", "Organismo", $CodOrganismo);
		$NomDependencia = getValorCampo("mastdependencias", "CodDependencia", "Dependencia", $_PARAMETRO["DEPLOGCXP"]);
		##
		$this->SetFillColor(255, 255, 255);
		$this->SetDrawColor(0, 0, 0);
		$this->Image($_PARAMETRO["PATHLOGO"].$Logo, 3, 3, 10, 10);
		$this->SetFont('Arial', '', 8);
		$this->SetXY(12, 3); $this->Cell(100, 5, $NomOrganismo, 0, 1, 'L');
		$this->SetXY(12, 8); $this->Cell(100, 5, $NomDependencia, 0, 0, 'L');
		$this->SetFont('Arial', '', 8);
		$this->SetXY(175, 3); $this->Cell(10, 5, utf8_decode('Fecha: '), 0, 0, 'L');
		$this->Cell(30, 5, formatFechaDMA(substr($Ahora, 0, 10)), 0, 1, 'L');
		$this->SetXY(175, 8); $this->Cell(10, 5, utf8_decode('Página: '), 0, 0, 'L');
		$this->Cell(30, 5, $this->PageNo().' de {nb}', 0, 1, 'L');
		$this->SetFont('Arial', 'B', 12);
		$this->Ln(5);
		$this->Cell(210, 5, utf8_decode('Sustento de Cambios en Precio Promedio'), 0, 1, 'C', 0);
		$this->Ln(5);
		##
		$this->SetDrawColor(0, 0, 0);
		$this->SetFillColor(200, 200, 200);
		$this->SetWidths(array(25,45,25,30,25,30,30));
		$this->SetAligns(array('C','L','R','R','R','R','R'));
		$this->SetFont('Arial', 'B', 10);
		$this->Cell(125, 5); $this->Cell(55, 5, 'Acumulado', 1, 1, 'C', 1);
		$this->Row(array('Fecha',
						 'Doc. Referencia',
						 'Cantidad',
						 'Precio Unit.',
						 'Cantidad',
						 'Monto',
						 'Precio Promedio'));
		$this->Ln(1);
	}
	
	//	Pie de página.
	function Footer() {}
}
//---------------------------------------------------

//---------------------------------------------------
//	Creación del objeto de la clase heredada.
$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->SetMargins(3, 3, 3);
$pdf->SetAutoPageBreak(1, 5);
$pdf->AddPage();
//---------------------------------------------------
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetDrawColor(255, 255, 255);
//	consulto
$i = 0;
$sql = "SELECT
			cms.*,
			i.Descripcion
		FROM
			lg_cierremensualsustento cms
			INNER JOIN lg_itemmast i ON (i.CodItem = cms.CodItem)
		WHERE
			cms.Periodo = '".$Periodo."' AND
			cms.CodOrganismo = '".$CodOrganismo."'
		ORDER BY CodItem";
$field = getRecords($sql);
foreach($field as $f) {	++$i;
	if ($Grupo != $f['CodItem']) {
		$Grupo = $f['CodItem'];
		$pdf->SetFont('Arial', 'B', 10);
		$pdf->Cell(25, 5, $f['CodItem'], 0, 0, 'L');
		$pdf->Cell(185, 5, utf8_decode($f['Descripcion']), 0, 1, 'L');
	}
	$PrecioPromedio = round(($f['Monto'] / $f['CantidadAcumulada']), 2);
	$pdf->SetFont('Arial', '', 10);
	$pdf->Row(array(formatFechaDMA($f['FechaRecepcion']),
					$f['DocumentoReferencia'],
					number_format($f['Cantidad'], 2, ',', '.'),
					number_format($f['Precio'], 2, ',', '.'),
					number_format($f['CantidadAcumulada'], 2, ',', '.'),
					number_format($f['Monto'], 2, ',', '.'),
					number_format($PrecioPromedio, 2, ',', '.')));
	$pdf->Ln(1);
}
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
