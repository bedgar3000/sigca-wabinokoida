<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$filtro = '';
if (trim($fCodOrganismo)) $filtro .= " AND pr.CodOrganismo = '$fCodOrganismo'";
if (trim($fEjercicio)) $filtro .= " AND pr.Ejercicio = '$fEjercicio'";
if (trim($fCodDependencia)) $filtro .= " AND prd.CodDependencia = '$fCodDependencia'";
if (trim($fCodUnidadEjec)) $filtro .= " AND ue.CodUnidadEjec = '$fCodUnidadEjec'";
if (trim($fIdSubSector)) $filtro .= " AND ss.IdSubSector = '$fIdSubSector'";
if (trim($fIdPrograma)) $filtro .= " AND pg.IdPrograma = '$fIdPrograma'";
if (trim($fIdSubPrograma)) $filtro .= " AND spg.IdSubPrograma = '$fIdSubPrograma'";
if (trim($fIdProyecto)) $filtro .= " AND py.IdProyecto = '$fIdProyecto'";
if (trim($fIdActividad)) $filtro .= " AND a.IdActividad = '$fIdActividad'";
if (count($fCodTipoNom)) {
	$filtro_nomina = "";
	foreach ($fCodTipoNom as $CodTipoNom) {
		if ($filtro_nomina) $filtro_nomina .= " OR tn.CodTipoNom = '$CodTipoNom'";
		else $filtro_nomina .= "tn.CodTipoNom = '$CodTipoNom'";
	}
	$filtro = $filtro . " AND ( $filtro_nomina )";
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
		global $field_unidad;
		global $_POST;
		extract($_POST);
		if (count($fCodTipoNom) == 1) $Nomina = $field_unidad['Nomina']; else $Nomina = "";
		##	
		$this->Image($_PARAMETRO['PATHLOGO'].$field['Logo'], 11, 11, 30, 23);
        $this->SetFont('Arial','B',8);
        $this->SetXY(11,35); $this->Cell(150, 5, utf8_decode($field['Organismo']), 0, 1, 'L');
        $this->SetFont('Arial','B',10);
        $this->Cell(195, 5, utf8_decode('REGISTRO DE INFORMACIÓN DE CARGOS'), 0, 1, 'C');
        $this->SetFont('Arial','B',8);
        $this->Cell(195, 5, utf8_decode($Nomina), 0, 1, 'C');
        $this->Cell(195, 5, utf8_decode('PRESUPUESTO: '.$fEjercicio), 0, 1, 'L');
		$this->SetDrawColor(0, 0, 0);
		$this->Rect(10, 10, 195, 45, "D");
		##	
		$this->SetY(60);
        $this->SetFont('Arial','B',8);
        $this->Cell(195, 5, utf8_decode('UNIDAD: '.$field_unidad['CategoriaProg'].' '.$field_unidad['UnidadEjecutora']), 0, 1, 'L');
		$this->SetDrawColor(0,0,0);
		$this->SetFillColor(255,255,255);
		$this->SetWidths(array(119,13,13,25,25));
		$this->SetAligns(array('C','C','C','C','C'));
		$this->Row(array(utf8_decode('CARGO'),
						 utf8_decode('GRADO'),
						 utf8_decode('PASO'),
						 utf8_decode('SUELDO'),
						 utf8_decode('SUELDO ANUAL')
				));
		$this->SetAligns(array('L','C','C','R','R'));
		$this->Ln(1);
	}
	
	//	Pie de página.
	function Footer() {
		global $FlagTotal;
		$h = $this->GetY() - 70;
		$this->SetDrawColor(0,0,0);
    	$this->Rect(10, 70, 0.1, $h, "FD");
    	$this->Rect(129, 70, 0.1, $h, "FD");
    	if ($FlagTotal) $this->Rect(142, 70, 0.1, $h-5, "FD");
    	else $this->Rect(142, 70, 0.1, $h, "FD");
    	$this->Rect(155, 70, 0.1, $h, "FD");
    	$this->Rect(180, 70, 0.1, $h, "FD");
    	$this->Rect(205, 70, 0.1, $h, "FD");
    	$y = 70 + $h;
    	$this->Rect(10, $y, 195, 0.1, "FD");
	}
}
//---------------------------------------------------

//---------------------------------------------------
//	Creación del objeto de la clase heredada.
$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(1, 20);
//---------------------------------------------------
$FlagTotal = false;
$MontoTotal = 0;
$Grupo = '';
##	
$sql = "SELECT
			pr.CodRecurso,
			pr.CodOrganismo,
			pr.Ejercicio,
			pr.CodTipoNom,
			tn.Nomina,
			prd.CategoriaProg,
			prd.Grado,
			prd.Paso,
			pt.DescripCargo,
			ue.Denominacion As UnidadEjecutora,
			pagd.SueldoTotal
		FROM
			pr_proyrecursosdet prd
			INNER JOIN pr_proyrecursos pr ON (pr.CodRecurso = prd.CodRecurso)
			INNER JOIN pr_proyajustegradodet pagd ON (pagd.CategoriaCargo = prd.CategoriaCargo AND pagd.Grado = prd.Grado AND pagd.Paso = prd.Paso)
			INNER JOIN pr_proyajustegrado pag ON (pag.CodAjuste = pagd.CodAjuste AND pag.Ejercicio = pr.Ejercicio AND pag.CodOrganismo = pr.CodOrganismo)
			INNER JOIN rh_puestos pt ON (pt.CodCargo = prd.CodCargo)
			INNER JOIN tiponomina tn ON (tn.CodTipoNom = pr.CodTipoNom)
			INNER JOIN pv_categoriaprog cp ON (cp.CategoriaProg = prd.CategoriaProg)
			INNER JOIN pv_actividades a ON (a.IdActividad = cp.IdActividad)
			INNER JOIN pv_proyectos py ON (py.IdProyecto = a.IdProyecto)
			INNER JOIN pv_subprogramas spg ON (spg.IdSubPrograma = py.IdSubPrograma)
			INNER JOIN pv_programas pg ON (pg.IdPrograma = spg.IdPrograma)
			INNER JOIN pv_subsector ss ON (ss.IdSubSector = pg.IdSubSector)
			INNER JOIN pv_sector s ON (s.CodSector = ss.CodSector)
			INNER JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cp.CodUnidadEjec)
		WHERE 1 $filtro
		GROUP BY CodRecurso, CodOrganismo, Ejercicio, CodTipoNom, CategoriaProg, pt.CodCargo, Grado, Paso
		ORDER BY CodOrganismo, Ejercicio, CategoriaProg, Grado DESC, Paso DESC, DescripCargo";
$field_detalle = getRecords($sql);
foreach ($field_detalle as $f) {
		$FlagTotal=false;
	$field_unidad = $f;
	$id = $f['CodOrganismo'].$f['Ejercicio'].$f['CategoriaProg'];
	if ($Grupo != $id) {
		if ($Grupo) {
			$FlagTotal=true;
			$pdf->SetDrawColor(0,0,0);
	        $pdf->SetFont('Arial','B',8);
	        $pdf->Cell(119, 5, utf8_decode('NRO. CARGOS: '.$NroCargos), 1, 0, 'C');
	        $pdf->Cell(26, 5, utf8_decode('TOTAL'), 1, 0, 'C');
	        $pdf->Cell(25, 5, number_format($SueldoTotal,2,',','.'), 1, 0, 'R');
	        $pdf->Cell(25, 5, number_format($SueldoTotalAnualTotal,2,',','.'), 1, 1, 'R');
		}
		##	
		$Grupo = $id;
		$SueldoTotal = 0;
		$SueldoTotalAnualTotal = 0;
		$NroCargos = 0;
		$pdf->AddPage();
	}
    //$y1 = $pdf->GetY() - 1;
	$SueldoTotalAnual = $f['SueldoTotal'] * 12;
	$pdf->SetDrawColor(255,255,255);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',8);
	$pdf->SetAligns(array('L','C','C','R','R'));
	$pdf->Row(array(utf8_decode($f['DescripCargo']),
					utf8_decode($f['Grado']),
					utf8_decode($f['Paso']),
					number_format($f['SueldoTotal'],2,',','.'),
					number_format($SueldoTotalAnual,2,',','.')
				));
	$pdf->Ln(1);
	$SueldoTotal += $f['SueldoTotal'];
	$SueldoTotalAnualTotal += $SueldoTotalAnual;
	++$NroCargos;
    ##
    //$y2 = $pdf->GetY();
    //$pdf->SetDrawColor(0,0,0);
    //$pdf->Rect(10, $y1, 0.1, $y2-$y1, "FD");
    //$pdf->Rect(129, $y1, 0.1, $y2-$y1, "FD");
    //$pdf->Rect(142, $y1, 0.1, $y2-$y1, "FD");
    //$pdf->Rect(155, $y1, 0.1, $y2-$y1, "FD");
    //$pdf->Rect(180, $y1, 0.1, $y2-$y1, "FD");
    //$pdf->Rect(205, $y1, 0.1, $y2-$y1, "FD");
}
$pdf->SetDrawColor(0,0,0);
$pdf->SetFont('Arial','B',8);
$pdf->Cell(119, 5, utf8_decode('NRO. CARGOS: '.$NroCargos), 1, 0, 'C');
$pdf->Cell(26, 5, utf8_decode('TOTAL'), 1, 0, 'C');
$pdf->Cell(25, 5, number_format($SueldoTotal,2,',','.'), 1, 0, 'R');
$pdf->Cell(25, 5, number_format($SueldoTotalAnualTotal,2,',','.'), 1, 1, 'R');
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
