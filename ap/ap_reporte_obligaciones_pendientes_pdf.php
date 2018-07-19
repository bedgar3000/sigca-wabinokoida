<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$LF = 0x0A;
$CR = 0x0D;
$nl = sprintf("%c%c",$CR,$LF);
//---------------------------------------------------
$filtro = "";
if ($fCodOrganismo != "") $filtro.=" AND (o.CodOrganismo = '".$fCodOrganismo."')";
if ($fCodProveedor != "") $filtro.=" AND (o.CodProveedor = '".$fCodProveedor."')";
if ($fFechaRegistroD != "") $filtro.=" AND (o.FechaRegistro >= '".$fFechaRegistroD."')";
if ($fFechaRegistroH != "") $filtro.=" AND (o.FechaRegistro <= '".$fFechaRegistroH."')";
if ($fFechaVencimientoD != "") $filtro.=" AND (o.FechaVencimiento >= '".$fFechaVencimientoD."')";
if ($fFechaVencimientoH != "") $filtro.=" AND (o.FechaVencimiento <= '".$fFechaVencimientoH."')";
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
		$Logo = getValorCampo("mastorganismos", "CodOrganismo", "Logo", $_SESSION['ORGANISMO_ACTUAL']);
		$NomOrganismo = getValorCampo("mastorganismos", "CodOrganismo", "Organismo", $_SESSION['ORGANISMO_ACTUAL']);
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
		$this->SetFont('Arial', 'B', 10);
		$this->Ln(5);
		$this->Cell(210, 5, utf8_decode('OBLIGACIONES PENDIENTES'), 0, 1, 'C', 0);
		$this->Ln(5);
		##
		$this->SetDrawColor(0, 0, 0);
		$this->SetFillColor(200, 200, 200);
		$this->SetWidths(array(33,12,100,15,15,15,20));
		$this->SetAligns(array('C','C','L','C','C','C','R'));
		$this->SetFont('Arial', 'B', 6);
		$this->Row(array('Documento',
						 'Nro. Reg.',
						 'Detalle',
						 'Fecha Oblig.',
						 'Fecha Recp.',
						 'Fecha Prog.',
						 'Monto'));
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
$pdf->SetAutoPageBreak(1, 3);
$pdf->AddPage();
//---------------------------------------------------
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetDrawColor(255, 255, 255);
//	consulto
$i = 0;
$sql = "SELECT
			o.CodProveedor,
			CONCAT(o.CodTipoDocumento, '-', o.NroDocumento) AS Documento,
			o.NroRegistro,
			o.Comentarios,
			o.FechaRegistro,
			o.FechaRecepcion,
			o.FechaProgramada,
			o.MontoObligacion,
			o.FlagContabilizacionPendiente,
			o.FlagContPendientePub20,
			p.NomCompleto AS NomProveedor,
			p.Ndocumento,
			p.DocFiscal,
			p.Telefono1,
			p.Direccion
		FROM
			ap_obligaciones o
			INNER JOIN mastpersonas p ON (o.CodProveedor = p.CodPersona)
		WHERE
			o.Estado <> 'PA' AND
			o.Estado <> 'AN' $filtro
		ORDER BY CodProveedor";
$field = getRecords($sql);
foreach($field as $f) {	++$i;
	if ($Grupo1 != $f['CodProveedor']) {
		$Grupo1 = $f['CodProveedor'];
		##
		if ($i > 1) {
			$pdf->SetFont('Arial', 'B', 6);
			$pdf->Cell(180, 4, 'Total Contabilizado: ', 0, 0, 'R', 1);
			$pdf->Cell(30, 4, number_format($SubTotalContabilizado, 2, ',', '.'), 0, 1, 'R');
			$pdf->Cell(180, 4, 'Total Proveedor: ', 0, 0, 'R', 1);
			$pdf->Cell(30, 4, number_format($SubTotalProveedor, 2, ',', '.'), 0, 1, 'R');
			$pdf->Ln(5);
		}
		$pdf->SetFont('Arial', 'B', 6);
		$pdf->Cell(10, 5, utf8_decode($f['CodProveedor']), 0, 0, 'L');
		$pdf->Cell(70, 5, utf8_decode($f['NomProveedor']), 0, 0, 'L');
		$pdf->Cell(30, 5, 'Rif. '.$f['DocFiscal'], 0, 1, 'L');
		##
		$SubTotalProveedor = 0;
		$SubTotalContabilizado = 0;
	}
	$pdf->SetFont('Arial', '', 6);
	$pdf->SetDrawColor(255, 255, 255);
	$pdf->SetFillColor(255, 255, 255);
	$pdf->Row(array($f['Documento'],
					$f['NroRegistro'],
					utf8_decode($f['Comentarios']),
					formatFechaDMA($f['FechaRegistro']),
					formatFechaDMA($f['FechaRecepcion']),
					formatFechaDMA($f['FechaProgramada']),
					number_format($f['MontoObligacion'], 2, ',', '.')));
	##
	$SubTotalProveedor += $f['MontoObligacion'];
	$TotalProveedor += $f['MontoObligacion'];
	if (($f['FlagContabilizacionPendiente'] == "N" || $f['FlagContPendientePub20'] == "N") && ($_PARAMETRO['CONTONCO'] == "S" || $_PARAMETRO['CONTPUB20'] == "S")) {
		$SubTotalContabilizado += $f['MontoObligacion'];
		$TotalContabilizado += $f['MontoObligacion'];
	}
}
##	
$pdf->SetFont('Arial', 'B', 6);
$pdf->Cell(180, 4, 'Total Contabilizado: ', 0, 0, 'R', 1);
$pdf->Cell(30, 4, number_format($SubTotalContabilizado, 2, ',', '.'), 0, 1, 'R');
$pdf->Cell(180, 4, 'Total Proveedor: ', 0, 0, 'R', 1);
$pdf->Cell(30, 4, number_format($SubTotalProveedor, 2, ',', '.'), 0, 1, 'R');
$pdf->Ln(5);
##	imprimo total
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetFillColor(0, 0, 0);
$y = $pdf->GetY();
$pdf->Rect(183, $y, 30, 0.1, "FD");
$y = $pdf->GetY() + 10;
$pdf->Rect(183, $y, 30, 0.1, "FD");
$pdf->SetFont('Arial', 'B', 6);
$pdf->Cell(180, 5, 'Total Contabilizado: ', 0, 0, 'R');
$pdf->Cell(30, 5, number_format($TotalContabilizado, 2, ',', '.'), 0, 0, 'R');
$pdf->Ln(5);
$pdf->Cell(180, 5, 'Total Proveedor: ', 0, 0, 'R');
$pdf->Cell(30, 5, number_format($TotalProveedor, 2, ',', '.'), 0, 0, 'R');
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
