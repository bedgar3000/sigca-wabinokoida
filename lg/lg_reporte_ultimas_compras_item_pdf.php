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
if ($fCodOrganismo != "") $filtro .= " AND (oc.CodOrganismo = '".$fCodOrganismo."')";
if ($fCodProveedor != "") $filtro .= " AND (oc.CodProveedor = '".$fCodProveedor."')";
if ($fCodAlmacen != "") $filtro .= " AND (oc.CodAlmacen = '".$fCodAlmacen."')";
$filtro .= " AND (oc.FechaAprobacion >= '".formatFechaAMD($fFechaAprobacionD)."')";
$filtro .= " AND (oc.FechaAprobacion <= '".formatFechaAMD($fFechaAprobacionH)."')";
##
if ($fCodItem != "") $filtro.=" AND (ocd.CodItem = '".$fCodItem."')";
if ($fCodLinea != "") $filtro.=" AND (i.CodLinea = '".$fCodLinea."')";
if ($fCodFamilia != "") $filtro.=" AND (i.CodFamilia = '".$fCodFamilia."')";
if ($fCodSubFamilia != "") $filtro.=" AND (i.CodSubFamilia = '".$fCodSubFamilia."')";
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
		$this->Cell(210, 5, utf8_decode('Últimas Compras Realizadas (Stock)'), 0, 1, 'C', 0);
		$this->Ln(5);
		##
		$this->SetFont('Arial', 'B', 9);
		$this->Cell(210, 5, utf8_decode('Fecha de Aprobación entre '.$fFechaAprobacionD.' y '.$fFechaAprobacionH), 0, 1, 'L', 0);
		$this->SetDrawColor(0, 0, 0);
		$this->SetFillColor(200, 200, 200);
		$this->SetWidths(array(18,10,142,15,25));
		$this->SetAligns(array('C','C','L','R','L'));
		$this->SetFont('Arial', 'B', 8);
		$this->Row(array('Item',
						 'Und.',
						 utf8_decode('Descripción'),
						 'Cantidad',
						 'Almacen'));
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
			oc.CodOrganismo,
			oc.Anio,
			oc.NroOrden,
			oc.NroInterno,
			oc.CodProveedor,
			oc.NomProveedor,
			oc.FechaAprobacion,
			oc.CodAlmacen,
			ocd.CodItem,
			ocd.Descripcion,
			ocd.CodUnidad,
			ocd.CantidadPedida,
			ocd.PrecioUnit
		FROM
			lg_ordencompra oc
			INNER JOIN lg_ordencompradetalle ocd ON (ocd.CodOrganismo = oc.CodOrganismo AND
													 ocd.Anio = oc.Anio AND
													 ocd.NroOrden = oc.NroOrden)
			INNER JOIN lg_itemmast i ON (i.CodItem = ocd.CodItem)
		WHERE 1 $filtro
		ORDER BY CodItem, FechaAprobacion, NomProveedor";
$field = getRecords($sql);
foreach($field as $f) {	++$i;
	if ($Grupo != $f['CodItem']) {
		$Grupo = $f['CodItem'];
		##	total x item
		$sql = "SELECT SUM(ocd.CantidadPedida)
				FROM
					lg_ordencompra oc
					INNER JOIN lg_ordencompradetalle ocd ON (ocd.CodOrganismo = oc.CodOrganismo AND
															 ocd.Anio = oc.Anio AND
															 ocd.NroOrden = oc.NroOrden)
					INNER JOIN lg_itemmast i ON (i.CodItem = ocd.CodItem)
				WHERE ocd.CodItem = '".$f['CodItem']."' $filtro
				GROUP BY ocd.CodItem";
		$TotalItem = getVar3($sql);
		##
		$pdf->SetFont('Arial', 'B', 8);
		$pdf->SetWidths(array(18,10,142,15));
		$pdf->SetAligns(array('L','C','L','R'));
		$pdf->Row(array($f['CodItem'],
						$f['CodUnidad'],
						utf8_decode($f['Descripcion']),
						number_format($TotalItem, 2, ',', '.')));
	}
	if ($fVerDetalle == "S") {
		$pdf->SetFont('Arial', '', 8);
		$pdf->SetWidths(array(20,12,90,38,10,15,25));
		$pdf->SetAligns(array('C','C','L','R','C','R','L'));	
		$pdf->Row(array(formatFechaDMA($f['FechaAprobacion']),
						$f['CodProveedor'],
						utf8_decode($f['NomProveedor']),
						number_format($f['PrecioUnit'], 2, ',', '.').'  Bs.',
						'',
						number_format($f['CantidadPedida'], 2, ',', '.'),
						$f['CodAlmacen']));
		$pdf->Ln(1);
	}
	$Total += $f['Total'];
}
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
