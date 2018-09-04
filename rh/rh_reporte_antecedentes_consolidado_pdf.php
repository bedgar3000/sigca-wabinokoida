<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$LF = 0x0A;
$CR = 0x0D;
$nl = sprintf("%c%c",$CR,$LF);
//---------------------------------------------------
$filtro = "";
if ($fTipoEnte != "") $filtro .= " AND (TipoEnte = '".$fTipoEnte."')";
if ($fMotivoCese != "") $filtro .= " AND (MotivoCese = '".$fMotivoCese."')";
if ($fAreaExperiencia != "") $filtro .= " AND (AreaExperiencia = '".$fAreaExperiencia."')";
if ($fFechaD != "") $filtro .= " AND (FechaDesde >= '".$fFechaD."')";
if ($fFechaH != "") $filtro .= " AND (FechaHasta <= '".$fFechaH."')";
//---------------------------------------------------

class PDF extends FPDF {
	//	Cabecera de página.
	function Header() {
		global $_PARAMETRO;
		global $Ahora;
		global $field_empleado;
		global $_POST;
		extract($_POST);
		##
		$Logo = getValorCampo("mastorganismos", "CodOrganismo", "Logo", $field_empleado['CodOrganismo']);
		$NomOrganismo = getValorCampo("mastorganismos", "CodOrganismo", "Organismo", $field_empleado['CodOrganismo']);
		$NomDependencia = getValorCampo("mastdependencias", "CodDependencia", "Dependencia", $_PARAMETRO["DEPRHPR"]);
		##
		$this->SetFillColor(255, 255, 255);
		$this->SetDrawColor(0, 0, 0);
		$this->Image($_PARAMETRO["PATHLOGO"].$Logo, 3, 3, 10, 10);
		$this->SetFont('Arial', '', 8);
		$this->SetXY(12, 3); $this->Cell(100, 5, $NomOrganismo, 0, 1, 'L');
		$this->SetXY(12, 8); $this->Cell(100, 5, $NomDependencia, 0, 0, 'L');
		$this->SetFont('Arial', '', 8);
		$this->SetXY(185, 3); $this->Cell(10, 5, utf8_decode('Fecha: '), 0, 0, 'L');
		$this->Cell(30, 5, formatFechaDMA(substr($Ahora, 0, 10)), 0, 1, 'L');
		$this->SetXY(185, 8); $this->Cell(10, 5, utf8_decode('Página: '), 0, 0, 'L');
		$this->Cell(30, 5, $this->PageNo().' de {nb}', 0, 1, 'L');
		$this->SetFont('Arial', 'B', 10);
		$this->Ln(5);
		$this->Cell(210, 5, utf8_decode('Antecedentes de Servicio'), 0, 1, 'C', 0);
		$this->Ln(8);
		##
		$this->SetDrawColor(0, 0, 0);
		$this->SetFillColor(200, 200, 200);
		$this->SetFont('Arial', 'B', 8);
		
		$this->Cell(120, 5);
		$this->SetWidths(array(30,30,30));
		$this->SetAligns(array('C','C','C'));
		$this->Row(array('Antecedentes',
						 'Organismo',
						 'Total'));
		
		$this->SetWidths(array(20,100,10,10,10,10,10,10,10,10,10));
		$this->SetAligns(array('C','L','C','C','C','C','C','C','C','C','C'));
		$this->Row(array('Empleado',
						 'Nombre Completo',
						 'A',
						 'M',
						 'D',
						 'A',
						 'M',
						 'D',
						 'A',
						 'M',
						 'D'));
	}
	
	//	Pie de página.
	function Footer() {}
}
//---------------------------------------------------

//---------------------------------------------------
//	Creación del objeto de la clase heredada.
$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->SetMargins(3, 3, 3);
$pdf->SetAutoPageBreak(1, 0);
//---------------------------------------------------
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(255, 255, 255);
//---------------------------------------------------
$i = 0;
$registros = explode("|", $sel_registros);
foreach($registros as $CodPersona) {
	$sql = "SELECT
				e.CodOrganismo,
				e.Fingreso,
				e.Fegreso,
				e.Estado,
				e.CodEmpleado,
				p.NomCompleto
			FROM
				mastempleado e
				INNER JOIN mastpersonas p ON (p.CodPersona = e.CodPersona)
			WHERE e.CodPersona = '".$CodPersona."'";
	$field_empleado = getRecord($sql);
	//	consulto
	$AniosTotal = 0;
	$MesesTotal = 0;
	$DiasTotal = 0;
	$sql = "SELECT
				Empresa,
				FechaDesde,
				FechaHasta
			FROM rh_empleado_experiencia
			WHERE
				CodPersona = '".$CodPersona."' AND
				TipoEnte = '02'
				$filtro";
	$field = getRecords($sql);
	foreach($field as $f) {
		list($Anios, $Meses, $Dias) = getTiempo(formatFechaDMA($f['FechaDesde']), formatFechaDMA($f['FechaHasta']));
		$AniosTotal += $Anios;
		$MesesTotal += $Meses;
		$DiasTotal += $Dias;
	}
	list($AntecedentesA, $AntecedentesM, $AntecedentesD) = totalTiempo($AniosTotal, $MesesTotal, $DiasTotal);
	if ($field_empleado['Estado'] == "I") $FechaHasta = $field_empleado['Fegreso'];
	else $FechaHasta = $FechaActual;
	list($OrganismoA, $OrganismoM, $OrganismoD) = getTiempo(formatFechaDMA($field_empleado['Fingreso']), formatFechaDMA($FechaHasta));
	list($TotalA, $TotalM, $TotalD) = totalTiempo(($AntecedentesA+$OrganismoA), ($AntecedentesM+$OrganismoM), ($AntecedentesD+$OrganismoD));
	##
	if ($i == 0) $pdf->AddPage();
	$pdf->SetFont('Arial', '', 8);
	$pdf->SetWidths(array(20,100,10,10,10,10,10,10,10,10,10));
	$pdf->SetAligns(array('C','L','C','C','C','C','C','C','C','C','C'));
	$pdf->Row(array($field_empleado['CodEmpleado'],
					utf8_decode($field_empleado['NomCompleto']),
					$AntecedentesA,
					$AntecedentesM,
					$AntecedentesD,
					$OrganismoA,
					$OrganismoM,
					$OrganismoD,
					$TotalA,
					$TotalM,
					$TotalD));
	++$i;
}
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
