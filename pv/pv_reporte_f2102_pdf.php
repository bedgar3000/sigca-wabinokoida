<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$filtro = '';
if (trim($fCodOrganismo)) $filtro .= " AND pi.CodOrganismo = '$fCodOrganismo'";
if (trim($fEjercicio)) $filtro .= " AND pi.Ejercicio = '$fEjercicio'";
if (trim($fPar)) $filtro .= " AND SUBSTRING(pi.cod_partida, 1, 3) = '$fPar'";
if (trim($fGen)) $filtro .= " AND SUBSTRING(pi.cod_partida, 1, 6) = '$fPar.$fGen'";
if (trim($fEsp)) $filtro .= " AND SUBSTRING(pi.cod_partida, 1, 9) = '$fPar.$fGen.$fEsp'";
if (trim($fSub)) $filtro .= " AND SUBSTRING(pi.cod_partida, 1, 12) = '$fPar.$fGen.$fEsp.$fSub'";
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
		$this->SetY(36);
		$this->SetFont('Arial', 'B', 12);
		$this->Cell(195, 5, utf8_decode('PRESUPUESTO DE INGRESOS'), 0, 1, 'C');
		$this->SetDrawColor(0, 0, 0);
		$this->Rect(10, 18, 195, 30, "D");
		##	
		$this->SetY(50);
		$this->SetFont('Arial','B',7);
		$this->Cell(40, 5, utf8_decode('CÓDIGO'), 0, 1, 'C');
		$this->SetY(55);
		$this->SetDrawColor(255,255,255);
		$this->SetFillColor(255,255,255);
		$this->SetWidths(array(10,10,10,10,125,30));
		$this->SetAligns(array('C','C','C','C','C','C'));
		$this->Row(array(utf8_decode('RAMO'),
						 utf8_decode('SUB. RAMO'),
						 utf8_decode('ESP.'),
						 utf8_decode('SUB. ESP.'),
						 utf8_decode('DENOMINACION'),
						 utf8_decode('MONTO')
				));
		$this->Ln(1);
	}
	
	//	Pie de página.
	function Footer() {
		global $MontoTotal;
		global $FlagTotal;
		##	
		$this->SetDrawColor(0,0,0);
		$this->SetFillColor(0,0,0);
		$this->Rect(10, 50, 195, 200, "D");
		$this->Rect(20, 55, 0.1, 195, "FD");
		$this->Rect(30, 55, 0.1, 195, "FD");
		$this->Rect(40, 55, 0.1, 195, "FD");
		$this->Rect(50, 50, 0.1, 200, "FD");
		$this->Rect(175, 50, 0.1, 200, "FD");
		$this->Rect(10, 55, 40, 0.1, "FD");
		$this->Rect(10, 65, 195, 0.1, "FD");
		##	
		if ($FlagTotal) {
			$this->SetY(250);
			$this->SetDrawColor(0,0,0);
			$this->SetFont('Arial','B',7);
			$this->Cell(165, 5, utf8_decode('TOTAL'), 1, 0, 'R');
			$this->Cell(30, 5, number_format($MontoTotal,2,',','.'), 1, 0, 'R');
			##	
			$this->SetY(255);
			$this->SetFont('Arial','B',7);
			$this->Cell(195, 5, utf8_decode('FORMA:     2102'), 0, 0, 'L');
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
$sql = "(SELECT
			p.cod_partida,
			p.denominacion,
			(SELECT SUM(Monto) FROM vw_presupuestoingresos pi WHERE cod_partida LIKE CONCAT(p.cod_tipocuenta, '%') $filtro) AS Monto,
			SUBSTRING(p.cod_partida, 1, 3) AS Par,
			SUBSTRING(p.cod_partida, 5, 2) AS Gen,
			SUBSTRING(p.cod_partida, 8, 2) AS Esp,
			SUBSTRING(p.cod_partida, 11, 2) AS Sub,
			'Cta' AS Tipo
		 FROM pv_partida p
		 WHERE
			p.partida1 = '00' AND
			p.generica = '00' AND
			p.especifica = '00' AND
			p.subespecifica = '00' AND
			SUBSTRING(p.cod_partida, 1, 1) IN (SELECT SUBSTRING(cod_partida, 1, 1) AS partida FROM vw_presupuestoingresos pi WHERE 1 $filtro GROUP BY partida))
		UNION
		(SELECT
			p.cod_partida,
			p.denominacion,
			(SELECT SUM(Monto) FROM vw_presupuestoingresos pi WHERE cod_partida LIKE CONCAT(p.cod_tipocuenta, p.partida1, '.%') $filtro) AS Monto,
			SUBSTRING(p.cod_partida, 1, 3) AS Par,
			SUBSTRING(p.cod_partida, 5, 2) AS Gen,
			SUBSTRING(p.cod_partida, 8, 2) AS Esp,
			SUBSTRING(p.cod_partida, 11, 2) AS Sub,
			'Par' AS Tipo
		 FROM pv_partida p
		 WHERE
			p.partida1 <> '00' AND
			p.generica = '00' AND
			p.especifica = '00' AND
			p.subespecifica = '00' AND
			SUBSTRING(p.cod_partida, 1, 3) IN (SELECT SUBSTRING(cod_partida, 1, 3) AS partida FROM vw_presupuestoingresos pi WHERE 1 $filtro GROUP BY partida))
		UNION
		(SELECT
			p.cod_partida,
			p.denominacion,
			(SELECT SUM(Monto) FROM vw_presupuestoingresos pi WHERE cod_partida LIKE CONCAT(p.cod_tipocuenta, p.partida1, '.', p.generica, '.%') $filtro) AS Monto,
			SUBSTRING(p.cod_partida, 1, 3) AS Par,
			SUBSTRING(p.cod_partida, 5, 2) AS Gen,
			SUBSTRING(p.cod_partida, 8, 2) AS Esp,
			SUBSTRING(p.cod_partida, 11, 2) AS Sub,
			'Gen' AS Tipo
		 FROM pv_partida p
		 WHERE
			p.partida1 <> '00' AND
			p.generica <> '00' AND
			p.especifica = '00' AND
			p.subespecifica = '00' AND
			SUBSTRING(p.cod_partida, 1, 7) IN (SELECT SUBSTRING(cod_partida, 1, 7) AS partida FROM vw_presupuestoingresos pi WHERE 1 $filtro GROUP BY partida))
		UNION
		(SELECT
			pi.cod_partida,
			pi.denominacion,
			SUM(pi.Monto) AS Monto,
			SUBSTRING(pi.cod_partida, 1, 3) AS Par,
			SUBSTRING(pi.cod_partida, 5, 2) AS Gen,
			SUBSTRING(pi.cod_partida, 8, 2) AS Esp,
			SUBSTRING(pi.cod_partida, 11, 2) AS Sub,
			'Esp' AS Tipo
		 FROM vw_presupuestoingresos pi
		 WHERE 1 $filtro
		 GROUP BY CodOrganismo, Ejercicio, cod_partida)
		ORDER BY cod_partida";
$field_detalle = getRecords($sql);
foreach ($field_detalle as $f) {
	$pdf->SetDrawColor(255,255,255);
	$pdf->SetFillColor(255,255,255);
	if ($f['Tipo'] == 'Cta') $pdf->SetFont('Arial','BUI',7);
	elseif ($f['Tipo'] == 'Par') $pdf->SetFont('Arial','BU',7);
	elseif ($f['Tipo'] == 'Gen') $pdf->SetFont('Arial','B',7);
	else $pdf->SetFont('Arial','',7);
	$pdf->SetAligns(array('C','C','C','C','L','R'));
	$pdf->Row(array(utf8_decode($f['Par']),
					utf8_decode($f['Gen']),
					utf8_decode($f['Esp']),
					utf8_decode($f['Sub']),
					utf8_decode($f['denominacion']),
					number_format($f['Monto'],2,',','.')
				));
	if ($f['Tipo'] == 'Esp') $MontoTotal += $f['Monto'];
}
$FlagTotal = true;
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
