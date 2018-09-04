<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$filtro = '';
##	general
$sql = "SELECT
			pr.CodOrganismo,
			pr.CodTipoNom,
			pr.Ejercicio,
			pr.CodRecurso,
			pp.CodParametro,
			pp.CodTipoProceso
		FROM
			pr_proyparametro pp
			INNER JOIN pr_proyrecursos pr ON (pr.CodRecurso = pp.CodRecurso)
		WHERE pp.CodParametro = '$CodParametro'";
$field = getRecord($sql);
##	conceptos a imprimir
$sql = "SELECT
			pe.CodConcepto,
			c.Abreviatura AS Concepto
		FROM
			pr_proyejecucion pe
			INNER JOIN pr_concepto c On (c.CodConcepto = pe.CodConcepto)
		WHERE
			pe.CodParametro = '$field[CodParametro]' AND
			pe.CodRecurso = '$field[CodRecurso]'
		GROUP BY CodConcepto
		ORDER BY CodConcepto";
$field_conceptos = getRecords($sql);
$_width = 370 + (count($field_conceptos) * 30);
//---------------------------------------------------
class PDF extends FPDF {
	//	Cabecera de página.
	function Header() {
		global $_PARAMETRO;
		global $Ahora;
		global $field;
		global $field_conceptos;
		global $_width;
		global $_POST;
		global $_GET;
		extract($_POST);
		extract($_GET);
		##	
		$Logo = getValorCampo("mastorganismos", "CodOrganismo", "Logo", $field['CodOrganismo']);
		$NomOrganismo = getValorCampo("mastorganismos", "CodOrganismo", "Organismo", $field['CodOrganismo']);
		$NomDependencia = getValorCampo("mastdependencias", "CodDependencia", "Dependencia", $_PARAMETRO["DEPRHPR"]);
		##	
		$this->SetFillColor(255, 255, 255);
		$this->SetDrawColor(0, 0, 0);
		$this->Image($_PARAMETRO["PATHLOGO"].$Logo, 5, 5, 10, 10);
		$this->SetFont('Arial', '', 8);
		$this->SetXY(15, 5); $this->Cell(100, 5, utf8_decode($NomOrganismo), 0, 1, 'L');
		$this->SetXY(15, 10); $this->Cell(100, 5, utf8_decode($NomDependencia), 0, 0, 'L');	
		$this->SetFont('Arial', '', 8);
		$this->SetXY($_width-50, 5); $this->Cell(20, 5, utf8_decode('Fecha: '), 0, 0, 'L');
		$this->Cell(30, 5, formatFechaDMA(substr($Ahora, 0, 10)), 0, 1, 'L');
		$this->SetXY($_width-50, 10); $this->Cell(20, 5, utf8_decode('Página: '), 0, 0, 'L'); 
		$this->Cell(30, 5, $this->PageNo().' de {nb}', 0, 1, 'L');
		$this->SetFont('Arial', 'B', 10);
		$this->SetY(20); $this->Cell($_width-10, 5, utf8_decode('PROYECCIÓN DE RECURSOS'), 0, 1, 'C');
		$this->SetY(25); $this->Cell($_width-10, 5, utf8_decode("($PeriodoDesde - $PeriodoHasta)"), 0, 1, 'C');
		$this->Ln(5);
		##	-------------------
		$this->SetTextColor(0, 0, 0);
		$this->SetDrawColor(0, 0, 0);
		$this->SetFillColor(245, 245, 245);
		$this->Ln(3);
		##	
		$this->SetFont('Arial', 'B', 8);
		$SetWidths = array(15,75,20,95,95,15,15);
		$SetAligns = array('C','L','R','L','L','C','C');
		$Rows = array(utf8_decode('Código'),
					  utf8_decode('Empleado'),
					  utf8_decode('Cédula'),
					  utf8_decode('Dependencia'),
					  utf8_decode('Cargo'),
					  utf8_decode('Grado'),
					  utf8_decode('Paso'));
		foreach ($field_conceptos as $fc) {
			$SetWidths[] = 30;
			$SetAligns[] = 'R';
			$Rows[] = $fc['Concepto'];
		}
		$SetWidths[] = 30;
		$SetAligns[] = 'R';
		$Rows[] = 'Total';
		$this->SetWidths($SetWidths);
		$this->SetAligns($SetAligns);
		$this->Row($Rows);
	}
	
	//	Pie de página.
	function Footer() {}
}
//---------------------------------------------------

//---------------------------------------------------
//	Creación del objeto de la clase heredada.
$pdf = new PDF('P', 'mm', array($_width, 226));
$pdf->AliasNbPages();
$pdf->SetMargins(5, 5, 5);
$pdf->SetAutoPageBreak(5, 5);
$pdf->AddPage();
//---------------------------------------------------
$Total = 0;
//	listado
$sql = "SELECT
			pe.CodParametro,
			pe.CodRecurso,
			pe.Secuencia,
			d.Dependencia,
			pt.DescripCargo,
			prd.Grado,
			prd.Paso,
			e.CodEmpleado,
			p.NomCompleto,
			p.Ndocumento
		FROM
			pr_proyejecucion pe
			INNER JOIN pr_proyrecursosdet prd ON (prd.CodRecurso = pe.CodRecurso AND prd.Secuencia = pe.Secuencia)
			INNER JOIN mastdependencias d ON (d.CodDependencia = prd.CodDependencia)
			INNER JOIN rh_puestos pt ON (pt.CodCargo = prd.CodCargo)
			LEFT JOIN mastpersonas p ON (p.CodPersona = prd.CodPersona)
			LEFT JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
		WHERE
			pe.CodParametro = '$field[CodParametro]' AND
			pe.CodRecurso = '$field[CodRecurso]'
		GROUP BY CodParametro, CodRecurso, Secuencia";
$field_empleados = getRecords($sql);
foreach ($field_empleados as $f) {
	$MontoTotal = 0;
	$Rows = array(utf8_decode($f['CodEmpleado']),
				  utf8_decode($f['NomCompleto']),
				  number_format($f['Ndocumento'],0,'','.'),
				  utf8_decode($f['Dependencia']),
				  utf8_decode($f['DescripCargo']),
				  utf8_decode($f['Grado']),
				  utf8_decode($f['Paso']));
	$sql = "SELECT
				c.CodConcepto,
				pe.Cantidad,
				pe.Monto
			FROM
				pr_proyejecucion pe
				INNER JOIN pr_concepto c ON (c.CodConcepto = pe.CodConcepto)
			WHERE
				pe.CodParametro = '$f[CodParametro]' AND
				pe.CodRecurso = '$f[CodRecurso]' AND
				pe.Secuencia = '$f[Secuencia]'
			ORDER BY CodConcepto";
	$field_montos = getRecords($sql);
	foreach ($field_montos as $fm) {
		$idx = $fm['CodConcepto'];
		$Rows[] = number_format($fm['Monto'],2,',','.');
		$MontoTotal += $fm['Monto'];
		$Concepto[$idx] += $fm['Monto'];
	}
	$Rows[] = number_format($MontoTotal,2,',','.');
	$pdf->SetFont('Arial', '', 8);
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetDrawColor(0, 0, 0);
	$pdf->SetFillColor(255, 255, 255);
	$pdf->Row($Rows);
	$Total += $MontoTotal;
}
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetFillColor(245, 245, 245);


		$SetWidths = array(330);
		$SetAligns = array('R');
		$Rows = array(utf8_decode('TOTAL            '));
		foreach ($field_conceptos as $fc) {
			$SetWidths[] = 30;
			$SetAligns[] = 'R';
			$idx = $fc['CodConcepto'];
			$Rows[] = number_format($Concepto[$idx],2,',','.');
		}
		$SetWidths[] = 30;
		$SetAligns[] = 'R';
		$Rows[] = number_format($Total,2,',','.');
		$pdf->SetWidths($SetWidths);
		$pdf->SetAligns($SetAligns);
		$pdf->Row($Rows);

		/*$fechainicial = new DateTime($PeriodoDesde.'-01');
		$fechafinal = new DateTime($PeriodoHasta.'-'.getDiasMes($PeriodoHasta));
		$diferencia = $fechainicial->diff($fechafinal);
		$meses = ( $diferencia->y * 12 ) + $diferencia->m + 1;
		$SetWidths = array(330);
		$SetAligns = array('R');
		$Rows = array(utf8_decode("TOTAL ($meses MESES)            "));
		foreach ($field_conceptos as $fc) {
			$SetWidths[] = 30;
			$SetAligns[] = 'R';
			$idx = $fc['CodConcepto'];
			$Rows[] = number_format(($Concepto[$idx]*$meses),2,',','.');
		}
		$SetWidths[] = 30;
		$SetAligns[] = 'R';
		$Rows[] = number_format(($Total*$meses),2,',','.');
		$pdf->SetWidths($SetWidths);
		$pdf->SetAligns($SetAligns);
		$pdf->Row($Rows);*/






/*$Widths = 330;
foreach ($field_conceptos as $fc) {
	$Widths += 20;
}
$SetWidths = array($Widths,25);
$SetAligns = array('R','R');
$Rows = array('TOTAL', number_format($Total,2,',','.'));
$pdf->SetWidths($SetWidths);
$pdf->SetAligns($SetAligns);
$pdf->Row($Rows);*/
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
