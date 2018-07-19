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
if ($fEstado != "") $filtro.=" AND (o.Estado = '".$fEstado."')";
if ($fCodProveedor != "") $filtro.=" AND (o.CodProveedor = '".$fCodProveedor."')";
if ($fFechaRegistroD != "") $filtro.=" AND (o.FechaRegistro >= '".$fFechaRegistroD."')";
if ($fFechaRegistroH != "") $filtro.=" AND (o.FechaRegistro <= '".$fFechaRegistroH."')";
if ($fCodCentroCosto != "") $filtro.=" AND (o.CodCentroCosto = '".$fCodCentroCosto."')";
if ($fFechaPagoD != "") $filtro.=" AND (o.FechaPago >= '".$fFechaPagoD."')";
if ($fFechaPagoH != "") $filtro.=" AND (o.FechaPago <= '".$fFechaPagoH."')";
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
		$this->Cell(210, 5, utf8_decode('OBLIGACIONES VS. CUENTAS CONTABLES'), 0, 1, 'C', 0);
		$this->Ln(5);
		##
		$this->SetDrawColor(0, 0, 0);
		$this->SetFillColor(200, 200, 200);
		$this->SetWidths(array(25,140,15,30));
		$this->SetAligns(array('C','L','C','R'));
		$this->SetFont('Arial', 'B', 8);
		$this->Row(array('Cuenta',
						 utf8_decode('Descripción'),
						 'C.Costo',
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
			o.CodTipoDocumento,
			o.NroDocumento,
			o.MontoObligacion,
			o.MontoImpuestoOtros,
			o.MontoImpuesto,
			o.FechaRecepcion,
			o.FechaRegistro,
			o.FechaDocumento,
			oc.CodCuenta,
			oc.CodCentroCosto,
			oc.Monto,
			p.NomCompleto AS NomProveedor,
			p.Ndocumento,
			p.Telefono1,
			p.Direccion,
			p.DocFiscal,
			pc.Descripcion AS NomCuenta
		FROM
			ap_obligaciones o
			INNER JOIN mastpersonas p ON (o.CodProveedor = p.CodPersona)
			INNER JOIN ap_obligacionescuenta oc ON (o.CodProveedor = oc.CodProveedor AND
													o.CodTipoDocumento = oc.CodTipoDocumento AND
													o.NroDocumento = oc.NroDocumento)
			INNER JOIN ac_mastplancuenta pc ON (pc.CodCuenta = oc.CodCuenta)
		WHERE 1 $filtro
		ORDER BY FechaRegistro, CodProveedor, CodTipoDocumento, NroDocumento";
$field = getRecords($sql);
foreach($field as $f) {	++$i;
	$Id = $f['CodProveedor'].$f['CodTipoDocumento'].$f['NroDocumento'];
	if ($Grupo1 != $Id) {
		$Grupo1 = $Id;
		##
		if ($i > 1) $pdf->Ln(3);
		$pdf->SetFillColor(240, 240, 240);
		$pdf->SetFont('Arial', 'B', 8);
		$pdf->Cell(85, 5, utf8_decode($f['NomProveedor']), 0, 0, 'L', 1);
		$pdf->Cell(22, 5, $f['DocFiscal'], 0, 0, 'L', 1);
		$pdf->Cell(40, 5, $f['CodTipoDocumento'].'-'.$f['NroDocumento'], 0, 0, 'L', 1);
		$pdf->Cell(33, 5, formatFechaDMA($f['FechaRegistro']), 0, 0, 'L', 1);
		$pdf->Cell(30, 5, number_format($f['MontoObligacion'], 2, ',', '.'), 0, 1, 'R', 1);
	}
	$pdf->SetFont('Arial', '', 8);
	$pdf->SetDrawColor(255, 255, 255);
	$pdf->SetFillColor(255, 255, 255);
	$pdf->Row(array($f['CodCuenta'],
					utf8_decode($f['NomCuenta']),
					$f['CodCentroCosto'],
					number_format($f['Monto'], 2, ',', '.')));
}
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
