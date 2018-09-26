<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$filtro = '';
if ($fCodOrganismo != "") $filtro.=" AND (r.CodOrganismo = '".$fCodOrganismo."')";
if ($fCodDependencia != "") $filtro.=" AND (r.CodDependencia = '".$fCodDependencia."')";
if ($fCodCentroCosto != "") $filtro.=" AND (r.CodCentroCosto = '".$fCodCentroCosto."')";
if ($fClasificacion != "") $filtro.=" AND (r.Clasificacion = '".$fClasificacion."')";
if ($fEstado != "") { 
	if ($fEstado == 'AP/CO') $filtro.=" AND (r.Estado = 'AP' OR r.Estado = 'CO')";
	else $filtro.=" AND (r.Estado = '".$fEstado."')"; 
}
if ($fTipoClasificacion != "") $filtro.=" AND (r.TipoClasificacion = '".$fTipoClasificacion."')";
if ($fBuscar != "") { 
	$filtro.=" AND (r.CodInterno LIKE '%".$fBuscar."%' OR 
					a.Descripcion LIKE '%".utf8_decode($fBuscar)."%' OR 
					r.CodCentroCosto LIKE '%".utf8_decode($fBuscar)."%' OR 
					c.Descripcion LIKE '%".utf8_decode($fBuscar)."%')";
}
if ($fFechaPreparaciond != "" || $fFechaPreparacionh != "") {
	if ($fFechaPreparaciond != "") $filtro.=" AND (r.FechaPreparacion >= '".formatFechaAMD($fFechaPreparaciond)."')";
	if ($fFechaPreparacionh != "") $filtro.=" AND (r.FechaPreparacion <= '".formatFechaAMD($fFechaPreparacionh)."')";
}
if ($fFlagCajaChica == 'S') $filtro .= " AND r.FlagCajaChica = 'S'";
//---------------------------------------------------

class PDF extends FPDF {
	//	Cabecera de página.
	function Header() {
		global $_PARAMETRO;
		global $Ahora;
		global $_POST;
		extract($_POST);
		##
		$Logo = getValorCampo("mastorganismos", "CodOrganismo", "Logo", $fCodOrganismo);
		$NomOrganismo = getValorCampo("mastorganismos", "CodOrganismo", "Organismo", $fCodOrganismo);
		$NomDependencia = getValorCampo("mastdependencias", "CodDependencia", "Dependencia", $_PARAMETRO["DEPLOGCXP"]);
		##
		$this->SetFillColor(255,255,255);
		$this->SetDrawColor(0,0,0);
		$this->Image($_PARAMETRO["PATHLOGO"].$Logo, 5, 5, 10, 10);		
		$this->SetFont('Arial', '', 8);
		$this->SetXY(15, 5); $this->Cell(100, 5, $NomOrganismo, 0, 1, 'L');
		$this->SetXY(15, 10); $this->Cell(100, 5, $NomDependencia, 0, 0, 'L');	
		$this->SetFont('Arial', '', 8);
		$this->SetXY(300, 5); $this->Cell(20, 5, utf8_decode('Fecha: '), 0, 0, 'L');
		$this->Cell(30, 5, formatFechaDMA(substr($Ahora, 0, 10)), 0, 1, 'L');
		$this->SetXY(300, 10); $this->Cell(20, 5, utf8_decode('Página: '), 0, 0, 'L'); 
		$this->Cell(30, 5, $this->PageNo().' de {nb}', 0, 1, 'L');
		$this->SetFont('Arial', 'B', 8);
		$this->SetY(20); $this->Cell(345, 5, utf8_decode('LISTA DE REQUERIMIENTOS'), 0, 1, 'C');
		$this->Ln(5);
		##	-------------------
		$this->SetFillColor(200,200,200);
		$this->SetWidths(array(20,17,17,26,115,15,20,15,15,35,50));
		$this->SetAligns(array('C','C','C','L','L','C','C','C','C','L','L'));
		$this->SetFont('Arial', 'B', 6);
		$this->Row(array(utf8_decode('# Requerimiento'),
						 utf8_decode('Fecha Preparación'),
						 utf8_decode('Fecha Aprobación'),
						 utf8_decode('Clasificación'),
						 utf8_decode('Comentarios'),
						 utf8_decode('Prioridad'),
						 utf8_decode('Estado'),
						 utf8_decode('C.Costo'),
						 utf8_decode('Dirigido A'),
						 utf8_decode('Almacén'),
						 utf8_decode('Aprobado Por')
						));
	}
	
	//	Pie de página.
	function Footer() {}
}
//---------------------------------------------------

//---------------------------------------------------
//	Creación del objeto de la clase heredada.
$pdf = new PDF('L', 'mm', 'Legal');
$pdf->AliasNbPages();
$pdf->SetMargins(5, 5, 5);
$pdf->SetAutoPageBreak(5, 5);
//---------------------------------------------------
$pdf->AddPage();
$sql = "SELECT
			r.*,
			o.Organismo,
			d.Dependencia,
			a.Descripcion AS NomAlmacen,
			c.Descripcion AS NomClasificacion,
			cc.Descripcion AS NomCentroCosto,
			p.Busqueda AS NomAprobadoPor
		FROM
			lg_requerimientos r
			INNER JOIN lg_almacenmast a ON (r.CodAlmacen = a.CodAlmacen)
			INNER JOIN lg_clasificacion c ON (r.Clasificacion = c.Clasificacion)
			INNER JOIN ac_mastcentrocosto cc ON (r.CodCentroCosto = cc.CodCentroCosto)
			INNER JOIN mastorganismos o ON (r.CodOrganismo = o.CodOrganismo)
			INNER JOIN mastdependencias d ON (r.CodDependencia = d.CodDependencia)
			LEFT JOIN mastpersonas p ON (r.AprobadaPor = p.CodPersona)
			INNER JOIN seguridad_alterna sa ON (r.CodDependencia = sa.CodDependencia AND
												sa.CodAplicacion = '".$_SESSION["APLICACION_ACTUAL"]."' AND
												sa.Usuario = '".$_SESSION["USUARIO_ACTUAL"]."' AND
												sa.FlagMostrar = 'S')
		WHERE 1 $filtro
		ORDER BY CodOrganismo, CodDependencia, Anio, Secuencia";
$field = getRecords($sql);
foreach ($field as $f) {
	if ($Grupo != $f['CodDependencia']) {
		$Grupo = $f['CodDependencia'];
		$pdf->SetFillColor(225,225,225);
		$pdf->SetFont('Arial', 'B', 6);
		$pdf->Cell(345, 5, utf8_decode($f['Dependencia']), 1, 1, 'L',1);
	}
	$pdf->SetFillColor(255,255,255);
	$pdf->SetDrawColor(0,0,0);
	$pdf->SetFont('Arial', '', 6);
	$pdf->Row(array(utf8_decode($f['CodInterno']),
					formatFechaDMA($f['FechaPreparacion']),
					formatFechaDMA($f['FechaAprobacion']),
					utf8_decode($f['NomClasificacion']),
					utf8_decode($f['Comentarios']),
					utf8_decode(printValoresGeneral("PRIORIDAD", $f['Prioridad'])),
					utf8_decode(printValores("ESTADO-REQUERIMIENTO", $f['Estado'])),
					utf8_decode($f['CodCentroCosto']),
					utf8_decode(printValores("DIRIGIDO", $f['TipoClasificacion'])),
					utf8_decode($f['NomAlmacen']),
					utf8_decode($f['NomAprobadoPor'])
					));
}
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
