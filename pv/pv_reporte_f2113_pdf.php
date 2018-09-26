<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$filtro = '';
if (trim($fCodOrganismo)) $filtro .= " AND cp.CodOrganismo = '$fCodOrganismo'";
$sql = "SELECT
			o.Organismo,
			e.Estado As NomEstado,
			m.Municipio
		FROM
			mastorganismos o
			INNER JOIN mastciudades c ON (c.CodCiudad = o.CodCiudad)
			INNER JOIN mastmunicipios m ON (m.CodMunicipio = c.CodMunicipio)
			INNER JOIN mastestados e ON (e.CodEstado = m.CodEstado)
		WHERE o.CodOrganismo = '$fCodOrganismo'";
$field_organismo = getRecord($sql);
//---------------------------------------------------

class PDF extends FPDF {
	//	Cabecera de página.
	function Header() {
		global $_PARAMETRO;
		global $Ahora;
		global $field_organismo;
		global $_POST;
		extract($_POST);
		##	
		$this->SetY(20);
		$this->SetFont('Arial','B',8);
		$this->Cell(30, 4, utf8_decode('ENTIDAD FEDERAL: '), 0, 0, 'L');
		$this->Cell(170, 4, utf8_decode($field['NomEstado']), 0, 1, 'L');
		$this->Cell(90, 4, utf8_decode('CODIGO PRESUPUESTARIO Y NOMBRE DEL MUNICIPIO: E5607 - '), 0, 0, 'L');
		$this->Cell(170, 4, utf8_decode($field['Municipio']), 0, 1, 'L');
		$this->Cell(42, 4, utf8_decode('PERIODO PRESUPUESTARIO:'), 0, 0, 'L');
		$this->Cell(170, 4, utf8_decode($fEjercicio), 0, 1, 'L');
		$this->SetY(40);
		$this->SetFont('Arial', 'B', 10);
		$this->Cell(195, 5, utf8_decode('DESCRIPCION DEL PROGRAMA Y SUB - PROGRAMA'), 0, 1, 'C');
		$this->SetDrawColor(0, 0, 0);
		$this->Rect(10, 18, 195, 30, "D");
		##	
		$this->SetY(50);
	}
	
	//	Pie de página.
	function Footer() {
		global $FlagTotal;
		##	
		$this->SetDrawColor(0,0,0);
		$this->SetFillColor(0,0,0);
		$this->Rect(10, 50, 195, 205, "D");
		##	
		if ($FlagTotal) {
			##	
			$this->SetY(255);
			$this->SetFont('Arial','B',7);
			$this->Cell(195, 5, utf8_decode('FORMA:     2113'), 0, 0, 'L');
		}
	}
}
//---------------------------------------------------

//---------------------------------------------------
//	Creación del objeto de la clase heredada.
$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->SetMargins(10, 10, 50);
$pdf->SetAutoPageBreak(1, 25);
//---------------------------------------------------
$FlagTotal = false;
##	
$sql = "SELECT
			ss.CodSector,
			pg.IdPrograma,
			ss.Descripcion,
			spg.IdSubPrograma,
			ss.CodClaSectorial,
			ss.Denominacion AS Sector,
			pg.CodPrograma,
			pg.Denominacion AS Programa,
			spg.CodSubPrograma,
			spg.Denominacion AS SubPrograma
		FROM
			pv_categoriaprog cp
			INNER JOIN pv_actividades a ON (a.IdActividad = cp.IdActividad)
			INNER JOIN pv_proyectos py ON (py.IdProyecto = a.IdProyecto)
			INNER JOIN pv_subprogramas spg ON (spg.IdSubPrograma = py.IdSubPrograma)
			INNER JOIN pv_programas pg ON (pg.IdPrograma = spg.IdPrograma)
			INNER JOIN pv_subsector ss ON (ss.IdSubSector = pg.IdSubSector)
		WHERE 1 $filtro
		GROUP BY ss.CodSector, pg.IdPrograma, spg.IdSubPrograma
		ORDER BY ss.CodSector, pg.CodPrograma, spg.CodSubPrograma";
$field_detalle = getRecords($sql);
foreach ($field_detalle as $f) {
	$sql = "SELECT
				ue.CodUnidadEjec,
				ue.Denominacion
			FROM
				pv_categoriaprog cp
				INNER JOIN pv_actividades a ON (a.IdActividad = cp.IdActividad)
				INNER JOIN pv_proyectos py ON (py.IdProyecto = a.IdProyecto)
				INNER JOIN pv_subprogramas spg ON (spg.IdSubPrograma = py.IdSubPrograma)
				INNER JOIN pv_programas pg ON (pg.IdPrograma = spg.IdPrograma)
				INNER JOIN pv_subsector ss ON (ss.IdSubSector = pg.IdSubSector)
				INNER JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cp.CodUnidadEjec)
				INNER JOIN pv_unidadejecutoradep ued ON (ued.CodUnidadEjec = ue.CodUnidadEjec)
			WHERE
				ss.CodSector = '$f[CodSector]' AND
				pg.IdPrograma = '$f[IdPrograma]' AND
				spg.IdSubPrograma = '$f[IdSubPrograma]'
			ORDER BY CodUnidadEjec";
	$field_unidades = getRecords($sql);
	$CodUnidadEjec = "";
	$UnidadEjecutora = "";
	foreach ($field_unidades as $fu) {
		$CodUnidadEjec .= utf8_decode($fu['CodUnidadEjec']).$nl;
		$UnidadEjecutora .= utf8_decode($fu['Denominacion']).$nl;
	}
	##	
	$pdf->AddPage();
	$pdf->SetFont('Arial','B',8);
	$pdf->SetDrawColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetWidths(array(45,15,135));
	$pdf->SetAligns(array('C','C','C'));
	$pdf->Row(array(utf8_decode(''), utf8_decode('CÓDIGO'), utf8_decode('DENOMINACIÓN')));
	$pdf->SetAligns(array('L','C','L'));
	$pdf->Row(array(utf8_decode('SECTOR:'), utf8_decode($f['CodClaSectorial']), utf8_decode(mb_strtoupper($f['Sector']))));
	$pdf->Row(array(utf8_decode('PROGRAMA:'), utf8_decode($f['CodPrograma']), utf8_decode(mb_strtoupper($f['Programa']))));
	$pdf->Row(array(utf8_decode('SUB-PROGRAMA:'), utf8_decode($f['CodSubPrograma']), utf8_decode(mb_strtoupper($f['SubPrograma']))));
	$pdf->Row(array(utf8_decode('UNIDAD (ES) EJECUTORA (S):'), $CodUnidadEjec, $UnidadEjecutora));
	$pdf->Cell(195, 6, utf8_decode('DESCRIPCIÓN'), 1, 1, 'C');
	$pdf->SetFont('Arial','',8);
	$pdf->MultiCell(195, 5, utf8_decode(mb_strtoupper($f['Descripcion'])), 0, 'FJ');
}
$FlagTotal = true;
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
