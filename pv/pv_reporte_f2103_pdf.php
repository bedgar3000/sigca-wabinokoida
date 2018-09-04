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
		WHERE o.CodOrganismo = '".$_SESSION["FILTRO_ORGANISMO_ACTUAL"]."'";
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
		$this->Cell(170, 4, utf8_decode($field_organismo['NomEstado']), 0, 1, 'L');
		$this->Cell(90, 4, utf8_decode('CODIGO PRESUPUESTARIO Y NOMBRE DEL MUNICIPIO: E5607 - '), 0, 0, 'L');
		$this->Cell(170, 4, utf8_decode($field_organismo['Municipio']), 0, 1, 'L');
		$this->Cell(42, 4, utf8_decode('PERIODO PRESUPUESTARIO:'), 0, 0, 'L');
		$this->Cell(170, 4, utf8_decode($fEjercicio), 0, 1, 'L');
		$this->SetY(36);
		$this->SetFont('Arial', 'B', 12);
		$this->Cell(195, 5, utf8_decode('ÍNDICE DE CATEGORÍAS PROGRAMÁTICAS'), 0, 1, 'C');
		$this->SetDrawColor(0, 0, 0);
		$this->Rect(10, 18, 195, 30, "D");
		##	
		$this->SetY(60);
		$this->SetFont('Arial','B',7);
		$this->SetDrawColor(255,255,255);
		$this->SetFillColor(255,255,255);
		$this->SetWidths(array(8,8,8,8,8,75,80));
		$this->SetAligns(array('C','C','C','C','C','C','C'));
		$this->Row(array('','','','','',
						 utf8_decode('DENOMINACION'),
						 utf8_decode('UNIDAD EJECUTORA')));
		$this->SetFontSize(7);
		$this->TextWithDirection(15, 72,'SECTOR','U');
		$this->TextWithDirection(23, 72,'PROGRAMA','U');
		$this->TextWithDirection(29, 72,'SUB - ','U');
		$this->TextWithDirection(33, 72,'PROGRAMA','U');
		$this->TextWithDirection(39, 72,'PROYECTO','U');
		$this->TextWithDirection(45, 72,'ACTIVIDAD U ','U');
		$this->TextWithDirection(49, 72,'OBRA','U');

		$this->SetY(75);
		$this->Ln(1);
		$this->SetWidths(array(8,8,8,8,8,75,80));
		$this->SetAligns(array('C','C','C','C','C','L','L'));
	}
	
	//	Pie de página.
	function Footer() {
		global $FlagTotal;
		##	
		$this->SetDrawColor(0,0,0);
		$this->SetFillColor(0,0,0);
		$this->Rect(10, 50, 195, 200, "D");
		$this->Rect(18, 50, 0.1, 200, "FD");
		$this->Rect(26, 50, 0.1, 200, "FD");
		$this->Rect(34, 50, 0.1, 200, "FD");
		$this->Rect(42, 50, 0.1, 200, "FD");
		$this->Rect(50, 50, 0.1, 200, "FD");
		$this->Rect(125, 50, 0.1, 200, "FD");
		$this->Rect(10, 75, 195, 0.1, "FD");
		##	
		if ($FlagTotal) {
			##	
			$this->SetY(255);
			$this->SetFont('Arial','B',7);
			$this->Cell(195, 5, utf8_decode('FORMA:     2103'), 0, 0, 'L');
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
$pdf->AddPage();
//---------------------------------------------------
$FlagTotal = false;
##	
$sql = "(SELECT
			cp.*,
			ss.CodClaSectorial,
			'' AS CodPrograma,
			'' AS CodSubPrograma,
			'' AS CodProyecto,
			'' AS CodActividad,
			ss.Denominacion,
			ue.Denominacion AS UnidadEjecutora
		 FROM
			pv_categoriaprog cp
			INNER JOIN pv_actividades a ON (a.IdActividad = cp.IdActividad)
			INNER JOIN pv_proyectos py ON (py.IdProyecto = a.IdProyecto)
			INNER JOIN pv_subprogramas spg ON (spg.IdSubPrograma = py.IdSubPrograma)
			INNER JOIN pv_programas pg ON (pg.IdPrograma = spg.IdPrograma)
			INNER JOIN pv_subsector ss ON (ss.IdSubSector = pg.IdSubSector)
			INNER JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cp.CodUnidadEjec)
			INNER JOIN pv_unidadejecutoradep ued ON (ued.CodUnidadEjec = ue.CodUnidadEjec)
		 WHERE 1 $filtro
		 GROUP BY CodClaSectorial)
		UNION
		(SELECT
			cp.*,
			ss.CodClaSectorial,
			pg.CodPrograma,
			'' AS CodSubPrograma,
			'' AS CodProyecto,
			'' AS CodActividad,
			pg.Denominacion,
			ue.Denominacion AS UnidadEjecutora
		 FROM
			pv_categoriaprog cp
			INNER JOIN pv_actividades a ON (a.IdActividad = cp.IdActividad)
			INNER JOIN pv_proyectos py ON (py.IdProyecto = a.IdProyecto)
			INNER JOIN pv_subprogramas spg ON (spg.IdSubPrograma = py.IdSubPrograma)
			INNER JOIN pv_programas pg ON (pg.IdPrograma = spg.IdPrograma)
			INNER JOIN pv_subsector ss ON (ss.IdSubSector = pg.IdSubSector)
			INNER JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cp.CodUnidadEjec)
			INNER JOIN pv_unidadejecutoradep ued ON (ued.CodUnidadEjec = ue.CodUnidadEjec)
		 WHERE pg.CodPrograma <> '00' $filtro
		 GROUP BY CodClaSectorial, CodPrograma)
		UNION
		(SELECT
			cp.*,
			ss.CodClaSectorial,
			pg.CodPrograma,
			spg.CodSubPrograma,
			'' AS CodProyecto,
			'' AS CodActividad,
			spg.Denominacion,
			ue.Denominacion AS UnidadEjecutora
		 FROM
			pv_categoriaprog cp
			INNER JOIN pv_actividades a ON (a.IdActividad = cp.IdActividad)
			INNER JOIN pv_proyectos py ON (py.IdProyecto = a.IdProyecto)
			INNER JOIN pv_subprogramas spg ON (spg.IdSubPrograma = py.IdSubPrograma)
			INNER JOIN pv_programas pg ON (pg.IdPrograma = spg.IdPrograma)
			INNER JOIN pv_subsector ss ON (ss.IdSubSector = pg.IdSubSector)
			INNER JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cp.CodUnidadEjec)
			INNER JOIN pv_unidadejecutoradep ued ON (ued.CodUnidadEjec = ue.CodUnidadEjec)
		 WHERE spg.CodSubPrograma <> '00'
		 GROUP BY CodClaSectorial, CodPrograma, CodSubPrograma)
		UNION
		(SELECT
			cp.*,
			ss.CodClaSectorial,
			pg.CodPrograma,
			spg.CodSubPrograma,
			py.CodProyecto,
			'' AS CodActividad,
			py.Denominacion,
			ue.Denominacion AS UnidadEjecutora
		 FROM
			pv_categoriaprog cp
			INNER JOIN pv_actividades a ON (a.IdActividad = cp.IdActividad)
			INNER JOIN pv_proyectos py ON (py.IdProyecto = a.IdProyecto)
			INNER JOIN pv_subprogramas spg ON (spg.IdSubPrograma = py.IdSubPrograma)
			INNER JOIN pv_programas pg ON (pg.IdPrograma = spg.IdPrograma)
			INNER JOIN pv_subsector ss ON (ss.IdSubSector = pg.IdSubSector)
			INNER JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cp.CodUnidadEjec)
			INNER JOIN pv_unidadejecutoradep ued ON (ued.CodUnidadEjec = ue.CodUnidadEjec)
		 WHERE py.CodProyecto <> '00' $filtro
		 GROUP BY CodClaSectorial, CodPrograma, CodSubPrograma, CodProyecto)
		UNION
		(SELECT
			cp.*,
			ss.CodClaSectorial,
			pg.CodPrograma,
			spg.CodSubPrograma,
			py.CodProyecto,
			a.CodActividad,
			a.Denominacion,
			ue.Denominacion AS UnidadEjecutora
		 FROM
			pv_categoriaprog cp
			INNER JOIN pv_actividades a ON (a.IdActividad = cp.IdActividad)
			INNER JOIN pv_proyectos py ON (py.IdProyecto = a.IdProyecto)
			INNER JOIN pv_subprogramas spg ON (spg.IdSubPrograma = py.IdSubPrograma)
			INNER JOIN pv_programas pg ON (pg.IdPrograma = spg.IdPrograma)
			INNER JOIN pv_subsector ss ON (ss.IdSubSector = pg.IdSubSector)
			INNER JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cp.CodUnidadEjec)
			INNER JOIN pv_unidadejecutoradep ued ON (ued.CodUnidadEjec = ue.CodUnidadEjec)
		 WHERE 1 $filtro
		 GROUP BY CodClaSectorial, CodPrograma, CodSubPrograma, CodProyecto, CodActividad)
		ORDER BY CodClaSectorial, CodPrograma, CodSubPrograma, CodProyecto, CodActividad";
$field_detalle = getRecords($sql);
foreach ($field_detalle as $f) {
	$pdf->SetDrawColor(255,255,255);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',7);
	$pdf->SetAligns(array('C','C','C','C','C','L','L'));
	$pdf->Row(array(utf8_decode($f['CodClaSectorial']),
					utf8_decode($f['CodPrograma']),
					utf8_decode($f['CodSubPrograma']),
					utf8_decode($f['CodProyecto']),
					utf8_decode($f['CodActividad']),
					utf8_decode($f['Denominacion']),
					utf8_decode($f['UnidadEjecutora'])
				));
}
$FlagTotal = true;
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
