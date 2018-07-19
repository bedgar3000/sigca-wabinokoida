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
if ($fClasificacion != "") $filtro.=" AND (cm.Clasificacion = '".$fClasificacion."')";
if ($fCodClasificacion != "") $filtro.=" AND (cs.CodClasificacion = '".$fCodClasificacion."')";
if ($fCommodityMast != "") $filtro.=" AND (cm.CommodityMast = '".$fCommodityMast."')";
if ($fBuscar != "") $filtro .= " AND (cc.Clasificacion LIKE '%".$fBuscar."%' OR
									  cc.Descripcion LIKE '%".$fBuscar."%' OR
									  cm.CommodityMast LIKE '%".$fBuscar."%' OR
									  cm.Descripcion LIKE '%".$fBuscar."%' OR
									  cs.Codigo LIKE '%".$fBuscar."%' OR
									  cs.Descripcion LIKE '%".$fBuscar."%' OR
									  cs.CodUnidad LIKE '%".$fBuscar."%' OR
									  cs.cod_partida LIKE '%".$fBuscar."%' OR
									  cs.CodCuenta LIKE '%".$fBuscar."%' OR
									  cs.CodCuentaPub20 LIKE '%".$fBuscar."%' OR
									  cs.CodClasificacion LIKE '%".$fBuscar."%')";
if ($fCodUnidad != "") $filtro.=" AND (cs.CodUnidad = '".$fCodUnidad."')";
if ($fEstado != "") $filtro.=" AND (cs.Estado = '".$fEstado."')";
if ($fCodCuenta != "") $filtro.=" AND (cs.CodCuenta = '".$fCodCuenta."')";
if ($fCodCuentaPub20 != "") $filtro.=" AND (cs.CodCuentaPub20 = '".$fCodCuentaPub20."')";
if ($fcod_partida != "") $filtro.=" AND (cs.cod_partida = '".$fcod_partida."')";
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
		$this->Cell(210, 5, utf8_decode('Información de Commodities'), 0, 1, 'C', 0);
		$this->Ln(5);
		##
		$this->SetDrawColor(0, 0, 0);
		$this->SetFillColor(200, 200, 200);
		$this->SetWidths(array(20,100,10,20,15,25,20));
		$this->SetAligns(array('C','L','C','C','C','C','C'));
		$this->SetFont('Arial', 'B', 8);
		$this->Row(array('Sub-Clase',
						 'Descripcion',
						 'Und.',
						 'Partida',
						 'Cta. Gasto',
						 'Cta. Gasto (Pub.20)',
						 utf8_decode('Clasificación')));
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
$pdf->SetAutoPageBreak(1, 0);
$pdf->AddPage();
//---------------------------------------------------
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetDrawColor(255, 255, 255);
//	consulto
$i = 0;
$sql = "SELECT			
			cc.Clasificacion,
			cc.Descripcion AS NomClasificacion,
			cm.CommodityMast,
			cm.Descripcion AS NomCommodity,
			cs.Codigo,
			cs.Descripcion,
			cs.CodUnidad,
			cs.cod_partida,
			cs.CodCuenta,
			cs.CodCuentaPub20,
			cs.CodClasificacion
		FROM
			lg_commoditymast cm
			INNER JOIN lg_commoditysub cs ON (cm.CommodityMast = cs.CommodityMast)
			INNER JOIN lg_commodityclasificacion cc ON (cm.Clasificacion = cc.Clasificacion)
			LEFT JOIN af_clasificacionactivo ca On (cs.CodClasificacion = ca.CodClasificacion)
		WHERE 1 $filtro
		ORDER BY Clasificacion, Codigo";
$field = getRecords($sql);
foreach($field as $f) {	++$i;
	if ($Grupo1 != $f['Clasificacion']) {
		$Grupo1 = $f['Clasificacion'];
		$Grupo2 = "";
		$pdf->SetFillColor(220, 220, 220);
		$pdf->SetFont('Arial', 'BU', 8);
		$pdf->Cell(8, 5, $f['Clasificacion'], 0, 0, 'L', 1);
		$pdf->Cell(202, 5, $f['NomClasificacion'], 0, 1, 'L', 1);
		$pdf->SetFillColor(255, 255, 255);
	}
	if ($Grupo2 != $f['CommodityMast']) {
		$Grupo2 = $f['CommodityMast'];
		$pdf->SetFont('Arial', 'B', 8);
		$pdf->Cell(8, 5, $f['CommodityMast'], 0, 0, 'L');
		$pdf->Cell(202, 5, $f['NomCommodity'], 0, 1, 'L');
	}
	$pdf->SetFont('Arial', '', 8);
	$pdf->Row(array($f['Codigo'],
					utf8_decode($f['Descripcion']),
					$f['CodUnidad'],
					$f['cod_partida'],
					$f['CodCuenta'],
					$f['CodCuentaPub20'],
					$f['CodClasificacion']));
	$pdf->Ln(1);
}
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
