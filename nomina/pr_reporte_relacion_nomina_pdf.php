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
	$filtro1 = " AND ptne.CodPersona = '".$fCodPersona."'";
}
if ($fCodTipoNom != '') {$filtropp .= " AND (pp.CodTipoNom = '".$fCodTipoNom."')"; $filtroptne .= " AND (ptne.CodTipoNom = '".$fCodTipoNom."')";}
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
			pp.Periodo = '".$fPeriodo."' AND 
			pp.CodTipoProceso = '".$fCodTipoProceso."' $filtropp";
$field_proceso = getRecord($sql);
//---------------------------------------------------

class PDF extends FPDF {
	//	Cabecera de página.
	function Header() {
		global $_PARAMETRO;
		global $field_proceso;
		global $fPeriodo;
		global $FechaActual;
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
		//$this->Cell(205, 5, strtoupper(utf8_decode('LISTADO DE LOS MONTOS A TRANSFERIR - '.$field_proceso['Nomina'])), 0, 1, 'L');
		$this->Cell(205, 5, strtoupper(utf8_decode('NÓMINA DE '.$field_proceso['Nomina'])), 0, 1, 'L');
		$this->Cell(205, 5, strtoupper(utf8_decode('MES DE '.getNombreMes($fPeriodo).' '.substr($fPeriodo,0,4).' '.$field_proceso['TipoProceso'])), 0, 1, 'L');
		$this->Cell(205, 5, 'DEL '.formatFechaDMA($field_proceso['FechaDesde']).' AL '.formatFechaDMA($field_proceso['FechaHasta']), 0, 1, 'L');
		##	-------------------
		$this->SetFont('Arial', '', 8);
		$this->SetXY(175, 5); $this->Cell(10, 5, utf8_decode('Fecha: '), 0, 0, 'L');
		$this->Cell(30, 5, formatFechaDMA($FechaActual), 0, 1, 'L');
		$this->SetXY(175, 10); $this->Cell(10, 5, utf8_decode('Página: '), 0, 0, 'L');
		$this->Cell(30, 5, $this->PageNo().' de {nb}', 0, 1, 'L');
		##	-------------------
		$this->SetY(35); 
		$this->SetFont('Arial', 'B', 9);
		$this->Cell(205, 10, utf8_decode('MONTO A TRANSFERIR'), 0, 1, 'C');
		$this->SetTextColor(0,0,0);
		$this->SetDrawColor(0,0,0);
		$this->SetFillColor(255,255,255);
		##
		$this->SetFont('Arial','B',8);
		$this->SetWidths(array(20,120,30,35));
		$this->SetAligns(array('R','L','R','C'));
		$this->Row(array(utf8_decode('CEDULA'),
						 utf8_decode('NOMBRES Y APELLIDOS'),
						 utf8_decode('MONTO Bs.'),
						 utf8_decode('NRO. CUENTA')
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
$TotalNeto = 0;
$i = 0;
$sql = "SELECT 
			ptne.TotalNeto,
			rbp.CodBeneficiario,
			bp.Ncuenta,
			(CASE WHEN rbp.CodBeneficiario IS NULL THEN mp.Ndocumento ELSE rbp.NroDocumento END) AS Ndocumento,
			(CASE WHEN rbp.CodBeneficiario IS NULL THEN mp.NomCompleto ELSE rbp.NombreCompleto END) AS NomCompleto
		FROM
			mastpersonas mp
			INNER JOIN pr_tiponominaempleado ptne ON (mp.CodPersona = ptne.CodPersona)
			LEFT JOIN bancopersona bp ON (mp.CodPersona = bp.CodPersona)
			LEFT JOIN rh_beneficiariopension rbp ON (mp.CodPersona = rbp.CodPersona)
		WHERE
			ptne.CodOrganismo = '".$fCodOrganismo."' AND
			ptne.Periodo = '".$fPeriodo."' AND
			ptne.CodTipoProceso = '".$fCodTipoProceso."' $filtro1 $filtro $filtroptne
		ORDER BY length(mp.Ndocumento), mp.Ndocumento";
$field = getRecords($sql);
foreach($field as $f) {
	$TotalNeto += $f['TotalNeto'];
	++$i;
	##	
	$pdf->SetTextColor(0,0,0);
	$pdf->SetDrawColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',8);
	$pdf->Row(array(number_format($f['Ndocumento'],0,'','.'),
					utf8_decode($f['NomCompleto']),
					number_format($f['TotalNeto'],2,',','.'),
					$f['Ncuenta']
					));
}
##	
$pdf->SetTextColor(0,0,0);
$pdf->SetDrawColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','B',8);
$pdf->Row(array('','TOTAL',number_format($TotalNeto,2,',','.'),''));

$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(102, 5, utf8_decode('NRO. DE TRABAJADORES: '.$i), 0, 0, 'L');
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
