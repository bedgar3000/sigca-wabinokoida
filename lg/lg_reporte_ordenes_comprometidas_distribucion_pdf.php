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
if ($fFechaPreparacionD != "") $filtro .= " AND (oc.FechaPreparacion >= '".formatFechaAMD($fFechaPreparacionD)."')";
if ($fFechaPreparacionH != "") $filtro .= " AND (oc.FechaPreparacion <= '".formatFechaAMD($fFechaPreparacionH)."')";
if ($fCodProveedor != "") $filtro .= " AND (oc.CodProveedor = '".$fCodProveedor."')";
if ($fFechaAprobacionD != "") $filtro .= " AND (oc.FechaAprobacion >= '".formatFechaAMD($fFechaAprobacionD)."')";
if ($fFechaAprobacionH != "") $filtro .= " AND (oc.FechaAprobacion <= '".formatFechaAMD($fFechaAprobacionH)."')";
if ($fEstadoMast != "") $filtro.=" AND (oc.Estado = '".$fEstadoMast."')";
if ($fMontoTotalD != "") $filtro .= " AND (oc.MontoTotal >= ".floatval(setNumero($fMontoTotalD)).")";
if ($fMontoTotalH != "") $filtro .= " AND (oc.MontoTotal <= ".floatval(setNumero($fMontoTotalH)).")";
##
if ($fcod_partida != "") $filtro_detalle .= " AND (ocd.cod_partida = '".$fcod_partida."')";
if ($fCodCuenta != "") $filtro_detalle .= " AND (ocd.CodCuenta = '".$fCodCuenta."')";
if ($fCodCuentaPub20 != "") $filtro_detalle .= " AND (oc.CodCuentaPub20 = '".$fCodCuentaPub20."')";
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
		$this->SetXY(310, 3); $this->Cell(10, 5, utf8_decode('Fecha: '), 0, 0, 'L');
		$this->Cell(30, 5, formatFechaDMA(substr($Ahora, 0, 10)), 0, 1, 'L');
		$this->SetXY(310, 8); $this->Cell(10, 5, utf8_decode('Página: '), 0, 0, 'L');
		$this->Cell(30, 5, $this->PageNo().' de {nb}', 0, 1, 'L');
		$this->SetFont('Arial', 'B', 10);
		$this->Ln(5);
		$this->Cell(350, 5, utf8_decode('Distribución Ordenes Comprometidas'), 0, 1, 'C', 0);
		$this->Ln(5);
		##
		$this->SetDrawColor(0, 0, 0);
		$this->SetFillColor(200, 200, 200);
		$this->SetWidths(array(21,89,21,89,21,89,20));
		$this->SetAligns(array('C','L','C','L','C','L','R'));
		$this->SetFont('Arial', 'B', 8);
		$this->Row(array(utf8_decode('Partida'),
						 utf8_decode('Descripción'),
						 utf8_decode('Cuenta'),
						 utf8_decode('Descripción'),
						 utf8_decode('Cta.Pub.20'),
						 utf8_decode('Descripción'),
						 'Monto'));
		$this->Ln(1);
	}
	
	//	Pie de página.
	function Footer() {}
}
//---------------------------------------------------

//---------------------------------------------------
//	Creación del objeto de la clase heredada.
$pdf = new PDF('L', 'mm', 'Legal');
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
$NroOrden = 0;
$sql = "SELECT
			oc.CodOrganismo,
			oc.Anio,
			oc.NroOrden,
			oc.NroInterno,
			oc.CodProveedor,
			oc.NomProveedor,
			oc.FechaPreparacion,
			oc.MontoTotal AS TotalOrden,
			oc.MontoIGV,
			oc.MontoBruto,
			oc.Estado AS EstadoMast,
			ocd.cod_partida,
			ocd.CodCuenta,
			ocd.CodCuentaPub20,
			ocd.Monto,
			p.denominacion AS NomPartida,
			pc.Descripcion AS NomCuenta,
			pc20.Descripcion AS NomCuentaPub20
		FROM
			lg_ordencompra oc
			INNER JOIN lg_distribucionoc ocd ON (ocd.CodOrganismo = oc.CodOrganismo AND
												 ocd.Anio = oc.Anio AND
												 ocd.NroOrden = oc.NroOrden)
			LEFT JOIN pv_partida p ON (p.cod_partida = ocd.cod_partida)
			LEFT JOIN ac_mastplancuenta pc ON (pc.CodCuenta = ocd.CodCuenta)
			LEFT JOIN ac_mastplancuenta20 pc20 ON (pc20.CodCuenta = ocd.CodCuentaPub20)
		WHERE
			(oc.Estado = 'CO' OR oc.Estado = 'AP' OR oc.Estado = 'RV')
			$filtro $filtro_detalle
		ORDER BY CodOrganismo, CodProveedor, Anio, NroInterno";
$field = getRecords($sql);
foreach($field as $f) {	++$i;
	if ($Grupo != $f['CodProveedor']) {
		$Grupo = $f['CodProveedor'];
		$Grupo2 = "";
		$NroOrden = 0;
		##	total x proveedor
		$sql = "SELECT SUM(oc.MontoTotal)
				FROM lg_ordencompra oc
				WHERE
					(oc.Estado = 'CO' OR oc.Estado = 'AP' OR oc.Estado = 'RV') AND
					oc.CodProveedor = '".$f['CodProveedor']."'
					$filtro
				GROUP BY CodProveedor";
		$TotalProveedor = getVar3($sql);
		##
		$pdf->SetFillColor(230, 230, 230);
		$pdf->SetFont('Arial', 'B', 8);
		$pdf->Cell(259, 8, utf8_decode($f['NomProveedor']), 0, 0, 'L', 1);
		$pdf->Cell(66, 8, 'Total Proveedor: ', 0, 0, 'R', 1);
		$pdf->SetFont('Arial', 'BU', 8);
		$pdf->Cell(25, 8, number_format($TotalProveedor, 2, ',', '.'), 0, 1, 'R', 1);
	}
	if ($Grupo2 != $f['NroOrden']) {
		$Grupo2 = $f['NroOrden'];
		$pdf->SetFont('Arial', 'B', 8);
		$pdf->Cell(10, 8, 'O/C: ', 0, 0, 'R');
		$pdf->Cell(15, 8, $f['NroInterno'], 0, 0, 'L');
		$pdf->Cell(30, 8, formatFechaDMA($f['FechaPreparacion']), 0, 0, 'C');
		$pdf->Cell(22, 8, printValoresGeneral("ESTADO-COMPRA", $f['EstadoMast']), 0, 0, 'L');
		$pdf->Cell(66, 8, 'Monto Bruto: ', 0, 0, 'R');
		$pdf->Cell(25, 8, number_format($f['MontoBruto'], 2, ',', '.'), 0, 0, 'R');
		$pdf->Cell(66, 8, 'I.V.A: ', 0, 0, 'R');
		$pdf->Cell(25, 8, number_format($f['MontoIGV'], 2, ',', '.'), 0, 0, 'R');
		$pdf->Cell(66, 8, 'Total Orden: ', 0, 0, 'R');
		$pdf->SetFont('Arial', 'BU', 8);
		$pdf->Cell(25, 8, number_format($f['TotalOrden'], 2, ',', '.'), 0, 1, 'R');
	}
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 7);	
	$pdf->Row(array($f['cod_partida'],
					utf8_decode($f['NomPartida']),
					$f['CodCuenta'],
					utf8_decode($f['NomCuenta']),
					$f['CodCuentaPub20'],
					utf8_decode($f['NomCuentaPub20']),
					number_format($f['Monto'], 2, ',', '.')));
	$pdf->Ln(1);
}
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
