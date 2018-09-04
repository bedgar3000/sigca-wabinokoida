<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$fPeriodo = $fPeriodoAnio.'-'.$fPeriodoMes;
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
		$Concepto = getVar3("SELECT Descripcion FROM pr_concepto WHERE CodConcepto = '".$fCodConcepto."'");
		##
		$this->SetFillColor(255, 255, 255);
		$this->SetDrawColor(0, 0, 0);
		$this->SetFont('Arial', 'B', 8);
		$this->Cell(205, 5, strtoupper(utf8_decode($NomOrganismo)), 0, 1, 'L');
		$this->Cell(205, 5, strtoupper($NomDependencia), 0, 1, 'L');
		$this->Cell(205, 5, strtoupper(utf8_decode('REPORTE DE '.$Concepto)), 0, 1, 'L');
		$this->Cell(205, 5, strtoupper(utf8_decode('TIPO DE NOMINA '.$field_proceso['Nomina'])), 0, 1, 'L');
		$this->Cell(205, 5, strtoupper(utf8_decode($field_proceso['TipoProceso'])), 0, 1, 'L');
		$this->Ln(5);
		$this->Cell(205, 5, 'DESDE '.formatFechaDMA($field_proceso['FechaDesde']).' HASTA '.formatFechaDMA($field_proceso['FechaHasta']), 0, 1, 'C');
		##	-------------------
		$this->SetTextColor(0,0,0);
		$this->SetDrawColor(0,0,0);
		$this->SetFillColor(255,255,255);
		$this->Ln(5);
		##
		$this->SetFont('Arial','B',8);
		$this->SetWidths(array(20,155,30));
		$this->SetAligns(array('R','L','R'));
		$this->Row(array(utf8_decode('CEDULA'),
						 utf8_decode('NOMBRES Y APELLIDOS'),
						 utf8_decode('RETENCIONES')
						 ));
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
$sql = "SELECT 
			mp.Ndocumento, 
			mp.NomCompleto,
			ptnec.Cantidad,
			ptnec.Monto
		FROM
			mastpersonas mp
			INNER JOIN pr_tiponominaempleado ptne ON (mp.CodPersona = ptne.CodPersona)
			INNER JOIN pr_tiponominaempleadoconcepto ptnec ON (ptne.CodOrganismo = ptnec.CodOrganismo AND
															   ptne.CodTipoNom = ptnec.CodTipoNom AND 
															   ptne.Periodo = ptnec.Periodo AND 
															   ptne.CodTipoproceso = ptnec.CodTipoProceso AND 
															   ptne.CodPersona = ptnec.CodPersona AND
															   ptnec.CodConcepto = '".$fCodConcepto."')
			LEFT JOIN rh_sueldos s ON (s.Periodo = ptne.Periodo AND
										s.CodPersona = ptne.CodPersona)
		WHERE
			ptne.CodOrganismo = '".$fCodOrganismo."' AND
			ptne.CodTipoNom = '".$fCodTipoNom."' AND
			ptne.Periodo = '".$fPeriodo."' AND
			ptne.CodTipoProceso = '".$fCodTipoProceso."'
		ORDER BY LENGTH(mp.Ndocumento), mp.Ndocumento";
$field = getRecords($sql);
foreach($field as $f) {
	$Monto += $f['Monto'];
	##	
	$pdf->SetTextColor(0,0,0);
	$pdf->SetDrawColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',8);
	$pdf->Row(array(number_format($f['Ndocumento'],0,'','.'),
					utf8_decode($f['NomCompleto']),
					number_format($f['Monto'],2,',','.')
					));
}
##	
$pdf->SetTextColor(0,0,0);
$pdf->SetDrawColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','B',8);
$pdf->Row(array('',
				'TOTAL',
				number_format($Monto,2,',','.')
				));
##	
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
