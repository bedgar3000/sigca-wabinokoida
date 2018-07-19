<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$filtro = '';
if (trim($fCodOrganismo)) $filtro .= " AND dis.CodOrganismo = '$fCodOrganismo'";
if (trim($fCodDependencia)) $filtro .= " AND ued.CodDependencia = '$fCodDependencia'";
if (trim($fCodUnidadEjec)) $filtro .= " AND cp.CodUnidadEjec = '$fCodUnidadEjec'";
if (trim($fCodSector)) $filtro .= " AND s.CodSector = '$fCodSector'";
if (trim($fIdSubSector)) $filtro .= " AND ss.IdSubSector = '$fIdSubSector'";
if (trim($fIdPrograma)) $filtro .= " AND pr.IdPrograma = '$fIdPrograma'";
if (trim($fIdSubPrograma)) $filtro .= " AND spr.IdSubPrograma = '$fIdSubPrograma'";
if (trim($fIdProyecto)) $filtro .= " AND py.IdProyecto = '$fIdProyecto'";
if (trim($fIdActividad)) $filtro .= " AND a.IdActividad = '$fIdActividad'";
if (trim($fEjercicio)) $filtro .= " AND dis.Ejercicio = '$fEjercicio'";
if (trim($fBuscar)) {
	$filtro .= " AND (dis.CategoriaProg LIKE '%$fBuscar%'
					  OR dis.cod_partida LIKE '%$fBuscar%'
					  )";
}
$sql = "SELECT
			o.Organismo,
			o.Logo,
			e.Estado As NomEstado,
			m.Municipio
		FROM
			mastorganismos o
			INNER JOIN mastciudades c ON (c.CodCiudad = o.CodCiudad)
			INNER JOIN mastmunicipios m ON (m.CodMunicipio = c.CodMunicipio)
			INNER JOIN mastestados e ON (e.CodEstado = m.CodEstado)
		WHERE o.CodOrganismo = '$fCodOrganismo'";
$field = getRecord($sql);
//---------------------------------------------------

class PDF extends FPDF {
	//	Cabecera de página.
	function Header() {
		global $_PARAMETRO;
		global $Ahora;
		global $field;
		global $field_detalle;
		global $_POST;
		extract($_POST);
		##	
		$this->Image($_PARAMETRO['PATHLOGO'].$field['Logo'], 10, 12, 30, 23);
        $this->SetFont('Arial','B',8);
        $this->SetXY(40,15); $this->Cell(150, 5, utf8_decode($field['Organismo']), 0, 1, 'L');
        $this->SetXY(40,20); $this->Cell(150, 5, utf8_decode('OFICINA DE PLANIFICACIÓN Y PRESUPUESTO'), 0, 1, 'L');
        $this->SetXY(40,25); $this->Cell(150, 5, utf8_decode('ENTIDAD FEDERAL: '.$field['NomEstado']), 0, 1, 'L');
        $this->SetXY(40,30); $this->Cell(150, 5, utf8_decode('MUNICIPIO: '.$field['Municipio']), 0, 1, 'L');
		$this->SetY(38);
        $this->SetFont('Arial','B',10);
        $this->Cell(195, 5, utf8_decode('RESUMEN PRESUPUESTARIO POR CATEGORIAS PROGRAMATICAS'), 0, 1, 'C');
		##	
        $this->SetFont('Arial','B',8);
		$this->SetY(50);
		$this->SetDrawColor(255,255,255);
		$this->SetFillColor(200,200,200);
		$this->SetWidths(array(20,135,40));
		$this->SetAligns(array('C','L','C'));
		$this->Row(array(utf8_decode('PARTIDA'),
						 utf8_decode('DENOMINACION'),
						 utf8_decode('MONTO')
				));
		$this->Ln(1);
		$this->SetAligns(array('C','L','R'));
	}
	
	//	Pie de página.
	function Footer() {
		global $MontoTotal;
		global $FlagTotal;
		##	
		if ($FlagTotal) {
			//$this->SetY(250);
			$this->SetDrawColor(0,0,0);
			$this->SetFont('Arial','B',7);
			$this->Cell(165, 5, utf8_decode('TOTAL'), 0, 0, 'R');
			$this->Cell(30, 5, number_format($MontoTotal,2,',','.'), 0, 0, 'R');
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
$MontoTotal = 0;
##	
$sql = "SELECT
			dis.CodOrganismo,
			dis.CategoriaProg,
			ue.Denominacion AS UnidadEjecutora,
			dis.cod_partida,
			pv.denominacion,
			SUM(dis.Monto) AS Monto
		FROM
		 	vw_003formulacionpersonaldist dis
		 	INNER JOIN pv_partida pv ON (pv.cod_partida = dis.cod_partida)
		 	INNER JOIN pv_categoriaprog cp ON (cp.CategoriaProg = dis.CategoriaProg)
		 	INNER JOIN pv_actividades a ON (a.IdActividad = cp.IdActividad)
		 	INNER JOIN pv_proyectos py ON (py.IdProyecto = a.IdProyecto)
		 	INNER JOIN pv_subprogramas spr ON (spr.IdSubPrograma = py.IdSubPrograma)
		 	INNER JOIN pv_programas pr ON (pr.IdPrograma = spr.IdPrograma)
		 	INNER JOIN pv_subsector ss ON (ss.IdSubSector = pr.IdSubSector)
		 	INNER JOIN pv_sector s ON (s.Codsector = ss.CodSector)
		 	INNER JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cp.CodUnidadEjec)
		 	LEFT JOIN pv_unidadejecutoradep ued ON (ued.CodUnidadEjec = ue.CodUnidadEjec)
		WHERE dis.Monto > 0 $filtro
		GROUP BY CodOrganismo, Ejercicio, CategoriaProg, cod_partida
		ORDER BY CodOrganismo, Ejercicio, CategoriaProg, cod_partida";
$field_detalle = getRecords($sql);
foreach ($field_detalle as $f) {
	if ($Grupo != $f['CategoriaProg']) {
		$Grupo = $f['CategoriaProg'];
		$pdf->SetFont('Arial','B',8);
		$pdf->Cell(195, 5, utf8_decode($f['CategoriaProg'].' '.$f['UnidadEjecutora']), 0, 1, 'L');
	}

	$pdf->SetDrawColor(255,255,255);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',8);
	$pdf->SetAligns(array('C','L','R'));
	$pdf->Row(array(utf8_decode($f['cod_partida']),
					utf8_decode($f['denominacion']),
					number_format($f['Monto'],2,',','.')
				));
	$MontoTotal += $f['Monto'];
}
$FlagTotal = true;
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
