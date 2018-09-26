<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$fPeriodo = $fPeriodoAnio."-".$fPeriodoMes;
$filtro = '';
if ($fCodOrganismo) $filtro .= " AND tne.CodOrganismo = '$fCodOrganismo'";
if ($fCodTipoNom) $filtro .= " AND tne.CodTipoNom = '$fCodTipoNom'";
if ($fPeriodo) $filtro .= " AND tne.Periodo = '$fPeriodo'";
if ($fCodTipoProceso) $filtro .= " AND tne.CodTipoProceso = '$fCodTipoProceso'";
if ($fCodPersona) $filtro .= " AND tne.CodPersona = '$fCodPersona'";
if ($fCodDependencia) $filtro .= " AND e.CodDependencia = '$fCodDependencia'";
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
		$Logo = getValorCampo("mastorganismos", "CodOrganismo", "Logo", $fCodOrganismo);
		$NomOrganismo = getValorCampo("mastorganismos", "CodOrganismo", "Organismo", $fCodOrganismo);
		$NomDependencia = getValorCampo("mastdependencias", "CodDependencia", "Dependencia", $_PARAMETRO["DEPRHPR"]);
		##
		##	colores
		$this->SetDrawColor(0,0,0);
		$this->SetTextColor(0,0,0);
		##	cuadros y lineas
		$this->Rect(10, 10, 195, 36, 'D');
		$this->Rect(45, 10, 0.1, 36, 'D');
		$this->Rect(170, 10, 0.1, 36, 'D');
		$this->Rect(45, 35, 160, 0.1, 'D');
		$this->Rect(170, 20, 35, 0.1, 'D');
		$this->Rect(170, 25, 35, 0.1, 'D');
		$this->Rect(170, 30, 35, 0.1, 'D');
		$this->Rect(187.5, 25, 0.1, 10, 'D');
		##	imprimo membrete
		$this->Image($_PARAMETRO["PATHLOGO"].$Logo, 18, 12, 18, 18);
		$this->SetFont('Arial', 'B', 8);
		$this->SetXY(10, 32); $this->MultiCell(35, 4, utf8_decode($NomOrganismo), 0, 'C');
		##
		$this->SetFont('Arial', 'B', 11);
		$this->SetXY(40, 18); $this->MultiCell(125, 5, utf8_decode($NomDependencia), 0, 'C');
		##
		$this->SetFont('Arial', 'B', 10);
		$this->SetXY(40, 36);
		$this->Cell(125, 5, 'FORMATO', 0, 2, 'C');
		$this->Cell(125, 5, utf8_decode('REGISTRO DE ASIGNACIÓN DE CARGOS'), 0, 0, 'C');
		##
		$this->SetFont('Arial', 'B', 8);
		$this->SetXY(170, 10); $this->Cell(35, 5, utf8_decode('CODIGO:'), 0, 1, 'C');
		$this->SetXY(170, 15); $this->Cell(35, 5, utf8_decode('FOR-DRRHH-013'), 0, 1, 'C');
		$this->SetXY(170, 20); $this->Cell(35, 5, utf8_decode('REVISIÓN:'), 0, 1, 'C');
		$this->SetXY(170, 25); $this->Cell(17.5, 5, utf8_decode('N°:'), 0, 1, 'C');
		$this->SetXY(187.5, 25); $this->Cell(17.5, 5, utf8_decode('FECHA'), 0, 1, 'C');
		$this->SetXY(170, 30); $this->Cell(17.5, 5, utf8_decode('0'), 0, 1, 'C');
		$this->SetXY(187.5, 30); $this->Cell(17.5, 5, utf8_decode('05/2008'), 0, 1, 'C');
		$this->SetXY(170, 37); $this->Cell(35, 5, utf8_decode('PAGINA'), 0, 1, 'C');
		$this->SetFont('Arial', '', 8);
		$this->SetXY(170, 41); $this->Cell(35, 5, $this->PageNo().' DE {nb}', 0, 1, 'C');
		##	
		$this->Ln(10);
		
	}
	
	//	Pie de página.
	function Footer() {}
}
//---------------------------------------------------

//---------------------------------------------------
//	Creación del objeto de la clase heredada.
$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->SetMargins(10, 5, 5);
$pdf->SetAutoPageBreak(5, 5);
//---------------------------------------------------
$sql = "SELECT
			p.CodPersona,
			p.NomCompleto,
			p.Ndocumento,
			e.CodEmpleado,
			pt.DescripCargo,
			pt.CodDesc,
			pt.Grado
		FROM
			pr_tiponominaempleado tne
			INNER JOIN mastpersonas p ON (p.CodPersona = tne.CodPersona)
			INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
			INNER JOIN rh_puestos pt ON (pt.CodCargo = e.CodCargo)
		WHERE 1 $filtro";
$field = getRecords($sql);
foreach ($field as $f) {
	$pdf->AddPage();
	$pdf->SetDrawColor(0,0,0);
	$pdf->SetTextColor(0,0,0);
	##	IDENTIFICACIÓN DEL FUNCIONARIO
	$pdf->SetFillColor(220,220,220);
	$pdf->SetFont('Arial', 'B', 10);
	$pdf->Cell(195, 5, utf8_decode('IDENTIFICACIÓN DEL FUNCIONARIO'), 1, 1, 'C', 1);
	$pdf->SetFillColor(255,255,255);
	$pdf->Cell(125, 5, utf8_decode('NOMBRES Y APELLIDOS'), 1, 0, 'C', 1);
	$pdf->Cell(30, 5, utf8_decode('CÉDULA'), 1, 0, 'C', 1);
	$pdf->Cell(40, 5, utf8_decode('CÓDIGO PERSONAL'), 1, 1, 'C', 1);
	$pdf->SetFont('Arial', '', 10);
	$pdf->SetWidths(array(125,30,40));
	$pdf->SetAligns(array('C','C','C'));
	$pdf->Row(array(utf8_decode($f['NomCompleto']),
					number_format($f['Ndocumento'],0,'','.'),
					utf8_decode($f['CodEmpleado'])
					));
	##	DATOS DEL CARGO
	$pdf->SetFillColor(220,220,220);
	$pdf->SetFont('Arial', 'B', 10);
	$pdf->Cell(195, 5, utf8_decode('DATOS DEL CARGO'), 1, 1, 'C', 1);
	$pdf->SetFillColor(255,255,255);
	$pdf->Cell(125, 5, utf8_decode('DENOMINACIÓN DEL CARGO'), 1, 0, 'C', 1);
	$pdf->Cell(30, 5, utf8_decode('CÓDIGO'), 1, 0, 'C', 1);
	$pdf->Cell(40, 5, utf8_decode('GRADO'), 1, 1, 'C', 1);
	$pdf->SetFont('Arial', '', 10);
	$pdf->SetWidths(array(125,30,40));
	$pdf->SetAligns(array('C','C','C'));
	$pdf->Row(array(utf8_decode($f['DescripCargo']),
					utf8_decode($f['CodDesc']),
					utf8_decode($f['Grado'])
					));
	##	DATOS SALARIALES
	$pdf->SetFillColor(220,220,220);
	$pdf->SetFont('Arial', 'B', 10);
	$pdf->Cell(195, 5, utf8_decode('DATOS SALARIALES'), 1, 1, 'C', 1);
	##	Sueldo Básico
	$sql = "SELECT
				c.Descripcion AS Concepto,
				tnec.Monto
			FROM
				pr_tiponominaempleadoconcepto tnec
				INNER JOIN pr_tiponominaempleado tne ON (tne.CodOrganismo = tnec.CodOrganismo AND
														 tne.CodTipoNom = tnec.CodTipoNom AND
														 tne.Periodo = tnec.Periodo AND
														 tne.CodTipoProceso = tnec.CodTipoProceso AND
														 tne.CodPersona = tnec.CodPersona)
				INNER JOIN pr_concepto c ON (c.CodConcepto = tnec.CodConcepto)
				INNER JOIN mastempleado e ON (e.CodPersona = tnec.CodPersona)
			WHERE
				tnec.CodPersona = '$f[CodPersona]' AND
				c.CodConcepto = '0001'
				$filtro";
	$field_sueldo = getRecord($sql);
	$pdf->SetFillColor(255,255,255);
	$pdf->Cell(155, 5, utf8_decode('CONCEPTO'), 1, 0, 'C', 1);
	$pdf->Cell(40, 5, utf8_decode('MONTO'), 1, 1, 'C', 1);
	$pdf->SetFont('Arial', '', 10);
	$pdf->SetWidths(array(155,40));
	$pdf->SetAligns(array('L','R'));
	$pdf->Row(array(utf8_decode($field_sueldo['Concepto']),
					number_format($field_sueldo['Monto'],2,',','.')
					));
	##	Compensaciones Fijas
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial', 'B', 10);
	$pdf->Cell(195, 5, utf8_decode('COMPENSACIONES FIJAS'), 1, 1, 'C', 1);
	$pdf->SetFillColor(255,255,255);
	$pdf->Cell(155, 5, utf8_decode('CONCEPTO'), 1, 0, 'C', 1);
	$pdf->Cell(40, 5, utf8_decode('MONTO'), 1, 1, 'C', 1);
	$sql = "SELECT
				c.Descripcion AS Concepto,
				tnec.Monto
			FROM
				pr_tiponominaempleadoconcepto tnec
				INNER JOIN pr_tiponominaempleado tne ON (tne.CodOrganismo = tnec.CodOrganismo AND
														 tne.CodTipoNom = tnec.CodTipoNom AND
														 tne.Periodo = tnec.Periodo AND
														 tne.CodTipoProceso = tnec.CodTipoProceso AND
														 tne.CodPersona = tnec.CodPersona)
				INNER JOIN pr_concepto c ON (c.CodConcepto = tnec.CodConcepto)
				INNER JOIN mastempleado e ON (e.CodPersona = tnec.CodPersona)
			WHERE
				tnec.CodPersona = '$f[CodPersona]' AND
				c.CodConcepto <> '0001' AND
				c.Tipo = 'I' AND
				c.FlagDiferencia <> 'S'
				$filtro";
	$field_sueldos = getRecords($sql);
	$total_compensaciones_fijas = 0;
	foreach ($field_sueldos as $fs) {
		$pdf->SetFont('Arial', '', 10);
		$pdf->SetWidths(array(155,40));
		$pdf->SetAligns(array('L','R'));
		$pdf->Row(array(utf8_decode($fs['Concepto']),
						number_format($fs['Monto'],2,',','.')
						));
		$total_compensaciones_fijas += $fs['Monto'];
	}
	for ($i=0; $i < (12 - count($field_sueldos)); $i++) $pdf->Row(array('',''));
	$pdf->SetFont('Arial', 'B', 10);
	$pdf->SetWidths(array(155,40));
	$pdf->SetAligns(array('R','R'));
	$pdf->Row(array(utf8_decode('TOTAL COMPENSACIONES FIJAS ->'),
					number_format($total_compensaciones_fijas,2,',','.')
					));
	$pdf->Row(array('',''));
	##	Otras Compensaciones
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial', 'B', 10);
	$pdf->Cell(195, 5, utf8_decode('OTRAS COMPENSACIONES'), 1, 1, 'C', 1);
	$pdf->SetFillColor(255,255,255);
	$pdf->Cell(155, 5, utf8_decode('CONCEPTO'), 1, 0, 'C', 1);
	$pdf->Cell(40, 5, utf8_decode('MONTO'), 1, 1, 'C', 1);
	$sql = "SELECT
				c.Descripcion AS Concepto,
				tnec.Monto
			FROM
				pr_tiponominaempleadoconcepto tnec
				INNER JOIN pr_tiponominaempleado tne ON (tne.CodOrganismo = tnec.CodOrganismo AND
														 tne.CodTipoNom = tnec.CodTipoNom AND
														 tne.Periodo = tnec.Periodo AND
														 tne.CodTipoProceso = tnec.CodTipoProceso AND
														 tne.CodPersona = tnec.CodPersona)
				INNER JOIN pr_concepto c ON (c.CodConcepto = tnec.CodConcepto)
				INNER JOIN mastempleado e ON (e.CodPersona = tnec.CodPersona)
			WHERE
				tnec.CodPersona = '$f[CodPersona]' AND
				c.CodConcepto <> '0001' AND
				c.Tipo = 'I' AND
				c.FlagDiferencia = 'S'
				$filtro";
	$field_sueldos = getRecords($sql);
	$total_compensaciones_otras = 0;
	foreach ($field_sueldos as $fs) {
		$pdf->SetFont('Arial', '', 10);
		$pdf->SetWidths(array(155,40));
		$pdf->SetAligns(array('L','R'));
		$pdf->Row(array(utf8_decode($fs['Concepto']),
						number_format($fs['Monto'],2,',','.')
						));
		$total_compensaciones_otras += $fs['Monto'];
	}
	for ($i=0; $i < (6 - count($field_sueldos)); $i++) $pdf->Row(array('',''));
	$pdf->SetFont('Arial', 'B', 10);
	$pdf->SetWidths(array(155,40));
	$pdf->SetAligns(array('R','R'));
	$pdf->Row(array(utf8_decode('TOTAL OTRAS COMPENSACIONES ->'),
					number_format($total_compensaciones_fijas,2,',','.')
					));
	$pdf->Row(array(utf8_decode('TOTAL SUELDO NORMAL ->'),
					number_format(($total_compensaciones_fijas + $total_compensaciones_otras),2,',','.')
					));
	##	FIRMAS
	$pdf->SetFillColor(220,220,220);
	$pdf->SetFont('Arial', 'B', 10);
	$pdf->Cell(195, 5, utf8_decode('FIRMAS'), 1, 1, 'C', 1);
	$pdf->SetFillColor(255,255,255);
	$pdf->Cell(98, 5, utf8_decode('ELABORADO POR'), 1, 0, 'C', 1);
	$pdf->Cell(97, 5, utf8_decode('CONFORMADO POR'), 1, 1, 'C', 1);
	$y = $pdf->GetY();
	$pdf->SetDrawColor(0,0,0);
	$pdf->Rect(10, $y, 98, 30, 'D');
	$pdf->Rect(108, $y, 97, 30, 'D');
	$pdf->Cell(98, 5, utf8_decode('ANALISTA DE RECURSOS HUMANOS I'), 0, 0, 'L');
	$pdf->Cell(97, 5, utf8_decode('DIRECTORA DE RECURSOS HUMANOS'), 0, 1, 'L');
	$pdf->Ln(20);
	$pdf->Cell(98, 5, utf8_decode('FECHA:'), 0, 0, 'L');
	$pdf->Cell(97, 5, utf8_decode('FECHA:'), 0, 1, 'L');
}

//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
