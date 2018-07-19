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
$filtro_detalle = "";
if ($fCodOrganismo != "") $filtro .= " AND (oc.CodOrganismo = '".$fCodOrganismo."')";
if ($fCodProveedor != "") $filtro .= " AND (oc.CodProveedor = '".$fCodProveedor."')";
if ($fEstadoMast != "") $filtro.=" AND (oc.Estado = '".$fEstadoMast."')";
if ($fFechaPreparacionD != "") $filtro .= " AND (oc.FechaPreparacion >= '".formatFechaAMD($fFechaPreparacionD)."')";
if ($fFechaPreparacionH != "") $filtro .= " AND (oc.FechaPreparacion <= '".formatFechaAMD($fFechaPreparacionH)."')";
if ($fFechaAprobacionD != "") $filtro .= " AND (oc.FechaAprobacion >= '".formatFechaAMD($fFechaAprobacionD)."')";
if ($fFechaAprobacionH != "") $filtro .= " AND (oc.FechaAprobacion <= '".formatFechaAMD($fFechaAprobacionH)."')";
if ($fMontoTotalD != "") $filtro .= " AND (oc.TotalMontoIva >= ".floatval(setNumero($fMontoTotalD)).")";
if ($fMontoTotalH != "") $filtro .= " AND (oc.TotalMontoIva <= ".floatval(setNumero($fMontoTotalH)).")";
##
if ($fCommoditySub != "") $filtro_detalle.=" AND (ocd.CommoditySub = '".$fCommoditySub."')";
if ($fFlagTerminado != "") $filtro_detalle.=" AND (ocd.FlagTerminado = '".$fFlagTerminado."')";
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
		$this->Cell(210, 5, utf8_decode('Ordenes de Servicio x Commodity'), 0, 1, 'C', 0);
		$this->Ln(5);
		##
		$this->SetDrawColor(0, 0, 0);
		$this->SetFillColor(200, 200, 200);
		$this->SetWidths(array(15,140,15,15,25));
		$this->SetAligns(array('C','L','C','R','L'));
		$this->SetFont('Arial', 'B', 8);
		$this->Row(array('Item',
						 utf8_decode('Descripción'),
						 'Atraso',
						 'Cantidad',
						 'Monto Total'));
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
$Nro = 0;
$contador = 0;
$sumador = 0;
$sql = "SELECT 
			ocd.CommoditySub,
			ocd.Descripcion,
			oc.NroInterno,
			oc.FechaDocumento,
			oc.CodProveedor,
			oc.NomProveedor,
			oc.FechaAprobacion,
			oc.CodCentroCosto,
			ocd.CantidadPedida,
			ocd.Total
		FROM
			lg_ordenservicio oc
			INNER JOIN lg_ordenserviciodetalle ocd ON (ocd.CodOrganismo = oc.CodOrganismo AND
													   ocd.Anio = oc.Anio AND
													   ocd.NroOrden = oc.NroOrden)
		WHERE 1 $filtro $filtro_detalle
		ORDER BY CommoditySub, Descripcion, FechaAprobacion, NomProveedor";
$field = getRecords($sql);
foreach($field as $f) {	++$i;
	if ($Grupo != $f['CommoditySub'].$f['Descripcion']) {
		$Grupo = $f['CommoditySub'].$f['Descripcion'];
		##	total x item
		$sql = "SELECT SUM(ocd.CantidadPedida) AS Cantidad, SUM(ocd.Total) AS Total
				FROM
					lg_ordenservicio oc
					INNER JOIN lg_ordenserviciodetalle ocd ON (ocd.CodOrganismo = oc.CodOrganismo AND
															   ocd.Anio = oc.Anio AND
															   ocd.NroOrden = oc.NroOrden)
				WHERE
					ocd.CommoditySub = '".$f['CommoditySub']."' AND
					ocd.Descripcion = '".$f['Descripcion']."' $filtro
				GROUP BY ocd.CommoditySub";
		$field_total = getRecord($sql);
		##
		$pdf->SetFont('Arial', 'B', 8);
		$pdf->SetWidths(array(15,155,15,25));
		$pdf->SetAligns(array('C','L','R','R'));
		$pdf->Row(array($f['CommoditySub'],
						utf8_decode($f['Descripcion']),
						number_format($field_total['Cantidad'], 2, ',', '.'),
						number_format($field_total['Total'], 2, ',', '.')));
		$Nro = 0;
		++$contador;
		$sumador += $field_total['Total'];
	}
	if ($fVerDetalle == "S") {
		$pdf->SetFont('Arial', '', 8);
		$pdf->SetWidths(array(7,25,18,92,18,10,15,25));
		$pdf->SetAligns(array('C','C','C','L','C','C','R','R'));
		$pdf->Row(array(++$Nro,
						'O/S '.$f['NroInterno'],
						formatFechaDMA($f['FechaDocumento']),
						$f['CodProveedor'].' '.utf8_decode($f['NomProveedor']),
						formatFechaDMA($f['FechaAprobacion']),
						$f['CodCentroCosto'],
						number_format($f['CantidadPedida'], 2, ',', '.'),						
						number_format($f['Total'], 2, ',', '.')));
		$pdf->Ln(1);
	}
	$Total += $f['Total'];
}
$pdf->SetDrawColor(0,0,0);
$pdf->Line(5, $pdf->GetY(), 275, $pdf->GetY());
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(105, 8, 'Cantidad de Ordenes: ' . number_format($contador, 2, ',', '.'), 0, 0, 'L');

$pdf->Cell(105, 8, 'Monto Total de las Ordenes:         ' . number_format($sumador, 2, ',', '.'), 0, 1, 'R');
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
