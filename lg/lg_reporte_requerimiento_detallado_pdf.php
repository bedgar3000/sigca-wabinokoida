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
if ($fCodOrganismo != "") $filtro.=" AND (r.CodOrganismo = '".$fCodOrganismo."')";
if ($fCodDependencia != "") $filtro.=" AND (r.CodDependencia = '".$fCodDependencia."')";
if ($fCodCentroCosto != "") $filtro.=" AND (r.CodCentroCosto = '".$fCodCentroCosto."')";
if ($fTipoClasificacion != "") $filtro.=" AND (r.TipoClasificacion = '".$fTipoClasificacion."')";
if ($fClasificacion != "") $filtro.=" AND (r.Clasificacion = '".$fClasificacion."')";
if ($fCodAlmacen != "") $filtro.=" AND (r.CodAlmacen = '".$fCodAlmacen."')";
if ($fEstado != "") $filtro.=" AND (rd.Estado = '".$fEstado."')";
if ($fFechaPreparacionD != "") $filtro.=" AND (r.FechaPreparacion >= '".formatFechaAMD($fFechaPreparacionD)."')";
if ($fFechaPreparacionH != "") $filtro.=" AND (r.FechaPreparacion <= '".formatFechaAMD($fFechaPreparacionH)."')";
if ($fBuscar != "") $filtro .= " AND (r.CodInterno LIKE '%".$fBuscar."%' OR
									  r.CodCentroCosto LIKE '%".$fBuscar."%' OR
									  r.CodAlmacen LIKE '%".$fBuscar."%' OR
									  cc.Abreviatura LIKE '%".$fBuscar."%' OR
									  c.Descripcion LIKE '%".$fBuscar."%')";
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
		$this->Cell(210, 5, utf8_decode('Detallado por Requerimientos'), 0, 1, 'C', 0);
		$this->Ln(5);
		##
		$this->SetDrawColor(0, 0, 0);
		$this->SetFillColor(200, 200, 200);
		$this->SetWidths(array(8,20,90,10,15,15,17,20,15));
		$this->SetAligns(array('R','C','L','C','R','R','R','C','R'));
		$this->SetFont('Arial', 'B', 8);
		$this->Row(array('#',
						 'Item / Commodity',
						 utf8_decode('Descripción'),
						 'Und.',
						 'Pedida',
						 'Recibida',
						 'Pendiente',
						 'Estado',
						 'Stock Actual'));
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
$sql = "(SELECT
			r.CodRequerimiento,
			r.CodInterno,
			r.CodCentroCosto,
			r.CodAlmacen,
			r.Estado AS EstadoMast,
			r.FechaPreparacion,
			r.FechaAprobacion,
			rd.CodItem As Codigo,
			rd.Descripcion,
			rd.CodUnidad,
			rd.CantidadPedida,
			rd.CantidadRecibida,
			(rd.CantidadPedida - rd.CantidadRecibida) AS CantidadPendiente,
			rd.Estado AS EstadoDet,
			cc.Abreviatura,
			c.Descripcion AS NomClasificacion,
			iai.StockActual
		 FROM
			lg_requerimientosdet rd
			INNER JOIN lg_requerimientos r ON (r.CodRequerimiento = rd.CodRequerimiento)
			INNER JOIN lg_itemalmaceninv iai ON (iai.CodAlmacen = r.CodAlmacen AND iai.CodItem = rd.CodItem)
			INNER JOIN ac_mastcentrocosto cc ON (cc.CodCentroCosto = r.CodCentroCosto)
			INNER JOIN lg_clasificacion c ON (c.Clasificacion = r.Clasificacion)
		 WHERE 1 $filtro)
		UNION
		(SELECT
			r.CodRequerimiento,
			r.CodInterno,
			r.CodCentroCosto,
			r.CodAlmacen,
			r.Estado AS EstadoMast,
			r.FechaPreparacion,
			r.FechaAprobacion,
			rd.CommoditySub As Codigo,
			rd.Descripcion,
			rd.CodUnidad,
			rd.CantidadPedida,
			rd.CantidadRecibida,
			(rd.CantidadPedida - rd.CantidadRecibida) AS CantidadPendiente,
			rd.Estado AS EstadoDet,
			cc.Abreviatura,
			c.Descripcion AS NomClasificacion,
			iai.Cantidad AS StockActual
		 FROM
			lg_requerimientosdet rd
			INNER JOIN lg_requerimientos r ON (r.CodRequerimiento = rd.CodRequerimiento)
			INNER JOIN lg_commoditystock iai ON (iai.CodAlmacen = r.CodAlmacen AND iai.CommoditySub = rd.CommoditySub)
			INNER JOIN ac_mastcentrocosto cc ON (cc.CodCentroCosto = r.CodCentroCosto)
			INNER JOIN lg_clasificacion c ON (c.Clasificacion = r.Clasificacion)
		 WHERE 1 $filtro)
		ORDER BY CodInterno";
$field = getRecords($sql);
foreach($field as $f) {	++$i;
	if ($Grupo != $f['CodRequerimiento']) {
		$Grupo = $f['CodRequerimiento'];
		$pdf->Ln(3);
		$pdf->SetFillColor(230, 230, 230);
		$pdf->SetFont('Arial', 'B', 6);
		$pdf->Cell(20, 4, 'Centro de Costos: ', 0, 0, 'L', 1);
		$pdf->Cell(30, 4, $f['CodCentroCosto'].' '.$f['Abreviatura'], 0, 0, 'L', 1);		
		$pdf->Cell(12, 4, utf8_decode('Almacén: '), 0, 0, 'L', 1);
		$pdf->Cell(50, 4, $f['CodAlmacen'], 0, 0, 'L', 1);		
		$pdf->Cell(16, 4, utf8_decode('Clasificación: '), 0, 0, 'L', 1);
		$pdf->Cell(82, 4, $f['NomClasificacion'], 0, 1, 'L', 1);
		##
		$pdf->Cell(20, 4, '# Requerimiento: ', 0, 0, 'L', 1);
		$pdf->Cell(92, 4, $f['CodInterno'], 0, 0, 'L', 1);
		$pdf->Cell(16, 4, 'Estado: ', 0, 0, 'L', 1);
		$pdf->Cell(82, 4, utf8_decode(printValores("ESTADO-REQUERIMIENTO", $f['EstadoMast'])), 0, 1, 'L', 1);
		##
		$pdf->Cell(20, 4, utf8_decode('F. Preparación: '), 0, 0, 'L', 1);
		$pdf->Cell(92, 4, formatFechaDMA($f['FechaPreparacion']), 0, 0, 'L', 1);
		$pdf->Cell(16, 4, utf8_decode('F. Aprobación: '), 0, 0, 'L', 1);
		$pdf->Cell(82, 4, formatFechaDMA($f['FechaAprobacion']), 0, 1, 'L', 1);
		$Nro = 0;
	}
	##
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 7);
	$pdf->Row(array(++$Nro,
					$f['Codigo'],
					utf8_decode($f['Descripcion']),
					$f['CodUnidad'],
					number_format($f['CantidadPedida'], 2, ',', '.'),
					number_format($f['CantidadRecibida'], 2, ',', '.'),
					number_format($f['CantidadPendiente'], 2, ',', '.'),
					utf8_decode(printValores("ESTADO-REQUERIMIENTO-DETALLE", $f['EstadoDet'])),
					number_format($f['StockActual'], 2, ',', '.')));
	$pdf->Ln(1);
}
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
