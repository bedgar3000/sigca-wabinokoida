<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$filtro = '';
if (trim($fCodOrganismo)) $filtro .= " AND pyr.CodOrganismo = '$fCodOrganismo'";
if (trim($fEjercicio)) $filtro .= " AND pyr.Ejercicio = '$fEjercicio'";
$filtro .= " AND pyr.CodTipoNom <> '02'";
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
$field = getRecord($sql);
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
		$this->Cell(195, 5, utf8_decode('RESUMEN DE LOS RECURSOS HUMANOS POR ESCALA DE SUELDOS'), 0, 1, 'C');
		$this->SetDrawColor(0, 0, 0);
		$this->Rect(10, 18, 195, 30, "D");
		##	
		$this->SetFont('Arial','B',8);
		$this->SetY(50);
		$this->Cell(70, 5, utf8_decode('PERSONAL FIJO A TIEMPO COMPLETO _____'), 0, 0, 'L');
		$this->Cell(70, 5, utf8_decode('PERSONAL FIJO A TIEMPO PARCIAL _____'), 0, 0, 'L');
		$this->Cell(55, 5, utf8_decode('PERSONAL CONTRATADO ____'), 0, 0, 'L');
		$this->SetFont('Arial','B',9);
		$this->SetXY(70,55); $this->Cell(135, 5, utf8_decode('AÑO PRESUPUESTADO'), 0, 1, 'C');
		$this->SetXY(100,60); $this->Cell(105, 5, utf8_decode('MONTO ANUAL DE LAS REMUNERACIONES'), 0, 1, 'C');
		$this->SetY(65);
		$this->SetDrawColor(255,255,255);
		$this->SetFillColor(255,255,255);
		$this->SetWidths(array(15,45,30,35,35,35));
		$this->SetAligns(array('C','C','C','C','C','C'));
		$this->Row(array(utf8_decode('GRUPO'),
						 utf8_decode('ESCALA'),
						 utf8_decode('N° DE CARGOS'),
						 utf8_decode('SUELDO BÁSICO'),
						 utf8_decode('COMPENSACIONES'),
						 utf8_decode('TOTAL')
				));
		$this->Ln(1);
	}
	
	//	Pie de página.
	function Footer() {
		global $MontoTotal;
		global $TotalSueldos;
		global $TotalCompensaciones;
		global $NroCargos;
		global $FlagTotal;
		##	
		$this->SetDrawColor(0,0,0);
		$this->SetFillColor(0,0,0);
		$this->Rect(10, 48, 195, 202, "D");
		$this->Rect(25, 55, 0.1, 195, "FD");
		$this->Rect(70, 55, 0.1, 195, "FD");
		$this->Rect(100, 60, 0.1, 190, "FD");
		$this->Rect(135, 65, 0.1, 185, "FD");
		$this->Rect(170, 65, 0.1, 185, "FD");
		$this->Rect(10, 55, 195, 0.1, "FD");
		$this->Rect(70, 60, 135, 0.1, "FD");
		$this->Rect(100, 65, 105, 0.1, "FD");
		$this->Rect(10, 70, 195, 0.1, "FD");
		##	
		if ($FlagTotal) {
			$this->SetY(250);
			$this->SetDrawColor(0,0,0);
			$this->SetFont('Arial','B',9);
			$this->Cell(60, 5, utf8_decode('TOTALES'), 1, 0, 'R');
			$this->Cell(30, 5, intval($NroCargos), 1, 0, 'C');
			$this->Cell(35, 5, number_format($TotalSueldos,2,',','.'), 1, 0, 'R');
			$this->Cell(35, 5, number_format($TotalCompensaciones,2,',','.'), 1, 0, 'R');
			$this->Cell(35, 5, number_format($MontoTotal,2,',','.'), 1, 0, 'R');
		}
		##	
		$this->SetY(255);
		$this->SetFont('Arial','B',7);
		$this->Cell(195, 5, utf8_decode('FORMA:     2108'), 0, 0, 'L');
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
$TotalSueldos = 0;
$TotalCompensaciones = 0;
$NroCargos = 0;
##	
$sql = "(
			SELECT
				'I' AS Grupo,
				'HASTA 4.251' AS Escala,
				SUM(NroCargos) AS NroCargos,
				SUM(TotalSueldos) AS TotalSueldos,
				SUM(TotalCompensaciones) AS TotalCompensaciones
			FROM vw_proyrecursos_escala pyr
			WHERE 1 $filtro AND pyr.Grupo = 'I'
		)
		UNION
		(
			SELECT
				'II' AS Grupo,
				'4.252 - 4.302' AS Escala,
				SUM(NroCargos) AS NroCargos,
				SUM(TotalSueldos) AS TotalSueldos,
				SUM(TotalCompensaciones) AS TotalCompensaciones
			FROM vw_proyrecursos_escala pyr
			WHERE 1 $filtro AND pyr.Grupo = 'II'
		)
		UNION
		(
			SELECT
				'III' AS Grupo,
				'4.303 - 4.353' AS Escala,
				SUM(NroCargos) AS NroCargos,
				SUM(TotalSueldos) AS TotalSueldos,
				SUM(TotalCompensaciones) AS TotalCompensaciones
			FROM vw_proyrecursos_escala pyr
			WHERE 1 $filtro AND pyr.Grupo = 'III'
		)
		UNION
		(
			SELECT
				'IV' AS Grupo,
				'4.354 - 4.404' AS Escala,
				SUM(NroCargos) AS NroCargos,
				SUM(TotalSueldos) AS TotalSueldos,
				SUM(TotalCompensaciones) AS TotalCompensaciones
			FROM vw_proyrecursos_escala pyr
			WHERE 1 $filtro AND pyr.Grupo = 'IV'
		)
		UNION
		(
			SELECT
				'V' AS Grupo,
				'4.405 - 4.455' AS Escala,
				SUM(NroCargos) AS NroCargos,
				SUM(TotalSueldos) AS TotalSueldos,
				SUM(TotalCompensaciones) AS TotalCompensaciones
			FROM vw_proyrecursos_escala pyr
			WHERE 1 $filtro AND pyr.Grupo = 'V'
		)
		UNION
		(
			SELECT
				'VI' AS Grupo,
				'4.456 - 4.506' AS Escala,
				SUM(NroCargos) AS NroCargos,
				SUM(TotalSueldos) AS TotalSueldos,
				SUM(TotalCompensaciones) AS TotalCompensaciones
			FROM vw_proyrecursos_escala pyr
			WHERE 1 $filtro AND pyr.Grupo = 'VI'
		)
		UNION
		(
			SELECT
				'VII' AS Grupo,
				'4.507 - 4.557' AS Escala,
				SUM(NroCargos) AS NroCargos,
				SUM(TotalSueldos) AS TotalSueldos,
				SUM(TotalCompensaciones) AS TotalCompensaciones
			FROM vw_proyrecursos_escala pyr
			WHERE 1 $filtro AND pyr.Grupo = 'VII'
		)
		UNION
		(
			SELECT
				'VIII' AS Grupo,
				'4.558 - 4.608' AS Escala,
				SUM(NroCargos) AS NroCargos,
				SUM(TotalSueldos) AS TotalSueldos,
				SUM(TotalCompensaciones) AS TotalCompensaciones
			FROM vw_proyrecursos_escala pyr
			WHERE 1 $filtro AND pyr.Grupo = 'VIII'
		)
		UNION
		(
			SELECT
				'IX' AS Grupo,
				'4.609 - 4.659' AS Escala,
				SUM(NroCargos) AS NroCargos,
				SUM(TotalSueldos) AS TotalSueldos,
				SUM(TotalCompensaciones) AS TotalCompensaciones
			FROM vw_proyrecursos_escala pyr
			WHERE 1 $filtro AND pyr.Grupo = 'IX'
		)
		UNION
		(
			SELECT
				'X' AS Grupo,
				'4.660 - 4.710' AS Escala,
				SUM(NroCargos) AS NroCargos,
				SUM(TotalSueldos) AS TotalSueldos,
				SUM(TotalCompensaciones) AS TotalCompensaciones
			FROM vw_proyrecursos_escala pyr
			WHERE 1 $filtro AND pyr.Grupo = 'X'
		)
		UNION
		(
			SELECT
				'XI' AS Grupo,
				'4.711 - 4.761' AS Escala,
				SUM(NroCargos) AS NroCargos,
				SUM(TotalSueldos) AS TotalSueldos,
				SUM(TotalCompensaciones) AS TotalCompensaciones
			FROM vw_proyrecursos_escala pyr
			WHERE 1 $filtro AND pyr.Grupo = 'XI'
		)
		UNION
		(
			SELECT
				'XII' AS Grupo,
				'4.762 - 4.812' AS Escala,
				SUM(NroCargos) AS NroCargos,
				SUM(TotalSueldos) AS TotalSueldos,
				SUM(TotalCompensaciones) AS TotalCompensaciones
			FROM vw_proyrecursos_escala pyr
			WHERE 1 $filtro AND pyr.Grupo = 'XII'
		)
		UNION
		(
			SELECT
				'XIII' AS Grupo,
				'4.813 - 4.863' AS Escala,
				SUM(NroCargos) AS NroCargos,
				SUM(TotalSueldos) AS TotalSueldos,
				SUM(TotalCompensaciones) AS TotalCompensaciones
			FROM vw_proyrecursos_escala pyr
			WHERE 1 $filtro AND pyr.Grupo = 'XIII'
		)
		UNION
		(
			SELECT
				'XIV' AS Grupo,
				'4.864 - 5.016' AS Escala,
				SUM(NroCargos) AS NroCargos,
				SUM(TotalSueldos) AS TotalSueldos,
				SUM(TotalCompensaciones) AS TotalCompensaciones
			FROM vw_proyrecursos_escala pyr
			WHERE 1 $filtro AND pyr.Grupo = 'XIV'
		)
		UNION
		(
			SELECT
				'XV' AS Grupo,
				'4.915 - 4.965' AS Escala,
				SUM(NroCargos) AS NroCargos,
				SUM(TotalSueldos) AS TotalSueldos,
				SUM(TotalCompensaciones) AS TotalCompensaciones
			FROM vw_proyrecursos_escala pyr
			WHERE 1 $filtro AND pyr.Grupo = 'XV'
		)
		UNION
		(
			SELECT
				'XVI' AS Grupo,
				'4.966 - 5.016' AS Escala,
				SUM(NroCargos) AS NroCargos,
				SUM(TotalSueldos) AS TotalSueldos,
				SUM(TotalCompensaciones) AS TotalCompensaciones
			FROM vw_proyrecursos_escala pyr
			WHERE 1 $filtro AND pyr.Grupo = 'XVI'
		)
		UNION
		(
			SELECT
				'XVII' AS Grupo,
				'5.017 Y MÁS' AS Escala,
				SUM(NroCargos) AS NroCargos,
				SUM(TotalSueldos) AS TotalSueldos,
				SUM(TotalCompensaciones) AS TotalCompensaciones
			FROM vw_proyrecursos_escala pyr
			WHERE 1 $filtro AND pyr.Grupo = 'XVII'
		);";
$field_detalle = getRecords($sql);
foreach ($field_detalle as $f) {
	$Total = $f['TotalSueldos'] + $f['TotalCompensaciones'];
	##	
	$pdf->SetDrawColor(255,255,255);
	$pdf->SetFillColor(255,255,255);
	if ($f['Tipo'] == 'Cta') $pdf->SetFont('Arial','BUI',7);
	elseif ($f['Tipo'] == 'Par') $pdf->SetFont('Arial','BU',7);
	elseif ($f['Tipo'] == 'Gen') $pdf->SetFont('Arial','B',7);
	else $pdf->SetFont('Arial','',9);
	$pdf->SetAligns(array('C','C','C','R','R','R'));
	$pdf->Row(array(utf8_decode($f['Grupo']),
					utf8_decode($f['Escala']),
					intval($f['NroCargos']),
					number_format($f['TotalSueldos'],2,',','.'),
					number_format($f['TotalCompensaciones'],2,',','.'),
					number_format($Total,2,',','.')
				));
	$pdf->Ln(2);
	$MontoTotal += $Total;
	$TotalSueldos += $f['TotalSueldos'];
	$TotalCompensaciones += $f['TotalCompensaciones'];
	$NroCargos += $f['NroCargos'];
}
$FlagTotal = true;
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
