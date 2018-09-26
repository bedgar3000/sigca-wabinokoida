<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$fPeriodo = $fPeriodoAnio.'-'.$fPeriodoMes;
$filtro = '';
$filtro1 = '';
$filtro2 = '';
$filtro3 = '';
if ($fFechaGeneracionD != '') $filtro .= " AND (ptne.FechaGeneracion >= '".formatFechaAMD($fFechaGeneracionD)."')";
if ($fFechaGeneracionH != '') $filtro .= " AND (ptne.FechaGeneracion <= '".formatFechaAMD($fFechaGeneracionH)."')";
if ($fCodPersona != "") {
	$filtro1 = " AND tnec2.CodPersona = '".$fCodPersona."'";
	$filtro2 = " AND ptnec.CodPersona = '".$fCodPersona."'";
	$filtro3 = " AND CodPersona = '".$fCodPersona."'";
}
//---------------------------------------------------
##	proceso
$sql = "SELECT 
			pp.*,
			tn.Nomina,
			tp.Descripcion AS TipoProceso
		FROM 
			pr_procesoperiodo pp 
			INNER JOIN tiponomina tn ON (tn.CodTipoNom = pp.CodTipoNom)
			INNER JOIN pr_tipoproceso tp ON (tp.CodTipoProceso = pp.CodTipoProceso)
		WHERE 
			pp.CodOrganismo = '".$fCodOrganismo."' AND 
			pp.CodTipoNom = '".$fCodTipoNom."' AND 
			pp.Periodo = '".$fPeriodo."' AND 
			pp.CodTipoProceso = '".$fCodTipoProceso."'";
$field_proceso = getRecord($sql);
//---------------------------------------------------

class PDF extends FPDF {
	//	Cabecera de página.
	function Header() {
		global $_PARAMETRO;
		global $field_proceso;
		global $fPeriodo;
		global $_POST;
		extract($_POST);
		##
		$NomOrganismo = getValorCampo("mastorganismos", "CodOrganismo", "Organismo", $fCodOrganismo);
		$NomDependencia = getValorCampo("mastdependencias", "CodDependencia", "Dependencia", $_PARAMETRO["DEPRHPR"]);
		##
		$this->SetFillColor(255, 255, 255);
		$this->SetDrawColor(0, 0, 0);
		$this->SetFont('Arial', 'B', 8);
		$this->Cell(205, 5, strtoupper(utf8_decode($NomOrganismo)), 0, 1, 'L');
		$this->Cell(205, 5, strtoupper($NomDependencia), 0, 1, 'L');
		$this->Cell(205, 5, strtoupper(utf8_decode('RESUMEN DE CONCEPTOS - '.$field_proceso['Nomina'])), 0, 1, 'L');
		$this->Cell(205, 5, strtoupper(utf8_decode('MES DE '.getNombreMes($fPeriodo).' '.substr($fPeriodo,0,4).' '.$field_proceso['TipoProceso'])), 0, 1, 'L');
		$this->Cell(205, 5, 'DEL '.formatFechaDMA($field_proceso['FechaDesde']).' AL '.formatFechaDMA($field_proceso['FechaHasta']), 0, 1, 'L');
		##	-------------------
		$this->SetTextColor(0,0,0);
		$this->SetDrawColor(255,255,255);
		$this->SetFillColor(255,255,255);
		$this->Ln(5);
		##
		$this->SetFont('Arial', 'B', 8);
		$this->SetHeights(array(3,3,3));
		$this->SetWidths(array(135,35,35));
		$this->SetAligns(array('L','R','R'));
		$this->Row(array(utf8_decode('CONCEPTO'),
						 utf8_decode('ASIGNACIONES'),
						 utf8_decode('DEDUCCIONES')
						 ));
		$this->Ln(1);
		##	
		$this->SetDrawColor(0,0,0);
		$this->Line(5, $this->GetY(), 210, $this->GetY());
		$this->Ln(2);
	}
	
	//	Pie de página.
	function Footer() {}
}
//---------------------------------------------------

//---------------------------------------------------
//	Creación del objeto de la clase heredada.
$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->SetMargins(5, 5, 5);
$pdf->SetAutoPageBreak(5, 5);
$pdf->AddPage();
//---------------------------------------------------
##	asignaciones
$TotalAsignaciones = 0;
$sql = "SELECT
			pc.CodConcepto,
			pc.Descripcion,
			SUM(ptnec.Monto) AS Monto
		FROM
			pr_concepto pc
			INNER JOIN pr_tiponominaempleadoconcepto ptnec ON (pc.CodConcepto = ptnec.CodConcepto)
			INNER JOIN pr_tiponominaempleado ptne ON (ptne.CodTipoNom = ptnec.CodTipoNom AND 
													  ptne.Periodo = ptnec.Periodo AND 
													  ptne.CodPersona = ptnec.CodPersona AND 
													  ptne.CodOrganismo = ptnec.CodOrganismo AND 
													  ptne.CodTipoProceso = ptnec.CodTipoProceso)
			LEFT JOIN tiponomina tn ON (ptnec.CodTipoNom = tn.CodTipoNom)
			LEFT JOIN pr_conceptoperfil cp ON (tn.CodPerfilConcepto = cp.CodPerfilConcepto)
			LEFT JOIN pr_conceptoperfildetalle cpd ON (cp.CodPerfilConcepto = cpd.CodPerfilConcepto AND
													   ptnec.CodTipoProceso = cpd.CodTipoProceso AND
													   pc.CodConcepto = cpd.CodConcepto)
		WHERE
			pc.Tipo = 'I' AND
			ptnec.CodTipoNom = '".$fCodTipoNom."' AND 
			ptnec.Periodo = '".$fPeriodo."' AND 
			ptnec.CodTipoProceso = '".$fCodTipoProceso."' $filtro2 $filtro
		GROUP BY
			ptnec.CodTipoNom,
			ptnec.Periodo,
			ptnec.CodTipoProceso,
			ptnec.CodConcepto";
$field_asignaciones = getRecords($sql);
foreach($field_asignaciones as $f) {
	$TotalAsignaciones += $f['Monto'];
	##	
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetDrawColor(255, 255, 255);
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 8);
	$pdf->Row(array(utf8_decode($f['Descripcion']),
					number_format($f['Monto'],2,',','.'),
					''
					));
	$pdf->Ln(2);
}
$pdf->SetTextColor(0, 0, 0);
$pdf->SetDrawColor(255, 255, 255);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Row(array('TOTAL ASIGNACIONES',
				number_format($TotalAsignaciones,2,',','.'),
				''
				));
$pdf->Ln(5);
##	deducciones
$TotalDeducciones = 0;
$sql = "SELECT
			pc.CodConcepto,
			pc.Descripcion,
			SUM(ptnec.Monto) AS Monto
		FROM
			pr_concepto pc
			INNER JOIN pr_tiponominaempleadoconcepto ptnec ON (pc.CodConcepto = ptnec.CodConcepto)
			INNER JOIN pr_tiponominaempleado ptne ON (ptne.CodTipoNom = ptnec.CodTipoNom AND 
													  ptne.Periodo = ptnec.Periodo AND 
													  ptne.CodPersona = ptnec.CodPersona AND 
													  ptne.CodOrganismo = ptnec.CodOrganismo AND 
													  ptne.CodTipoProceso = ptnec.CodTipoProceso)
			LEFT JOIN tiponomina tn ON (ptnec.CodTipoNom = tn.CodTipoNom)
			LEFT JOIN pr_conceptoperfil cp ON (tn.CodPerfilConcepto = cp.CodPerfilConcepto)
			LEFT JOIN pr_conceptoperfildetalle cpd ON (cp.CodPerfilConcepto = cpd.CodPerfilConcepto AND
													   ptnec.CodTipoProceso = cpd.CodTipoProceso AND
													   pc.CodConcepto = cpd.CodConcepto)
		WHERE
			pc.Tipo = 'D' AND
			ptnec.CodTipoNom = '".$fCodTipoNom."' AND 
			ptnec.Periodo = '".$fPeriodo."' AND 
			ptnec.CodTipoProceso = '".$fCodTipoProceso."' $filtro2 $filtro
		GROUP BY
			ptnec.CodTipoNom,
			ptnec.Periodo,
			ptnec.CodTipoProceso,
			ptnec.CodConcepto";
$field_deducciones = getRecords($sql);
foreach($field_deducciones as $f) {
	$TotalDeducciones += $f['Monto'];
	##	
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetDrawColor(255, 255, 255);
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 8);
	$pdf->Row(array(utf8_decode($f['Descripcion']),
					'',
					number_format($f['Monto'],2,',','.')
					));
	$pdf->Ln(2);
}
$pdf->SetTextColor(0, 0, 0);
$pdf->SetDrawColor(255, 255, 255);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Row(array('TOTAL DEDUCCIONES',
				'',
				number_format($TotalDeducciones,2,',','.')
				));
$pdf->Ln(5);
##	neto
$pdf->SetDrawColor(0,0,0);
$pdf->Line(5, $pdf->GetY(), 210, $pdf->GetY());
$pdf->Ln(1);
$TotalNeto = $TotalAsignaciones - $TotalDeducciones;
##	
$pdf->SetTextColor(0, 0, 0);
$pdf->SetDrawColor(255, 255, 255);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Row(array('TOTAL NETO',
				'',
				number_format($TotalNeto,2,',','.')
				));
$pdf->Ln(30);
$pdf->SetDrawColor(0,0,0);
$pdf->Line(6, $pdf->GetY(), 61, $pdf->GetY());
$pdf->Line(108, $pdf->GetY(), 163, $pdf->GetY());
list($ElaboradoNombre, $ElaboradoCargo, $ElaboradoNivel) = getFirma($field_proceso['ProcesadoPor']);
list($ConformadoNombre, $ConformadoCargo, $ConformadoNivel) = getFirmaxDependencia($_PARAMETRO["DEPRHPR"]);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(102, 5, utf8_decode('ELABORADO POR:'), 0, 0, 'L');
$pdf->Cell(103, 5, utf8_decode('CONFORMADO POR:'), 0, 1, 'L');
$pdf->Cell(102, 5, utf8_decode($ElaboradoNombre), 0, 0, 'L');
$pdf->Cell(103, 5, utf8_decode($ConformadoNombre), 0, 1, 'L');
$pdf->Cell(102, 5, utf8_decode($ElaboradoCargo), 0, 0, 'L');
$pdf->Cell(103, 5, utf8_decode($ConformadoCargo), 0, 1, 'L');
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
