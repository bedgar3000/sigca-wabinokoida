<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$fPeriodo = $fPeriodoAnio.'-'.$fPeriodoMes;
$Leyenda = false;
$filtro = '';
$filtro1 = '';
$filtro2 = '';
$filtro3 = '';
if ($fFechaGeneracionD != '') $filtro .= " AND (ptne.FechaGeneracion >= '".formatFechaAMD($fFechaGeneracionD)."')";
if ($fFechaGeneracionH != '') $filtro .= " AND (ptne.FechaGeneracion <= '".formatFechaAMD($fFechaGeneracionH)."')";
if ($fCodPersona != "") {
	$filtro1 = " AND ptne.CodPersona = '".$fCodPersona."'";
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
##	conceptos (asignaciones)
$sql = "SELECT
			tnec.CodConcepto,
			c.Descripcion,
			c.Abreviatura
		FROM
			pr_tiponominaempleadoconcepto tnec
			INNER JOIN pr_tiponominaempleado ptne ON (ptne.CodTipoNom = tnec.CodTipoNom AND 
													  ptne.Periodo = tnec.Periodo AND 
													  ptne.CodPersona = tnec.CodPersona AND 
													  ptne.CodOrganismo = tnec.CodOrganismo AND 
													  ptne.CodTipoProceso = tnec.CodTipoProceso)
			INNER JOIN pr_concepto c ON (tnec.CodConcepto = c.CodConcepto AND c.Tipo = 'I')
		WHERE
			tnec.CodOrganismo = '".$fCodOrganismo."' AND
			tnec.CodTipoNom = '".$fCodTipoNom."' AND
			tnec.Periodo = '".$fPeriodo."' AND
			tnec.CodTipoProceso = '".$fCodTipoProceso."' $filtro1 $filtro
		GROUP BY CodConcepto
		ORDER BY CodConcepto";
$field_asignaciones = getRecords($sql);
##	conceptos (deducciones)
$sql = "SELECT
			tnec.CodConcepto,
			c.Descripcion,
			c.Abreviatura
		FROM
			pr_tiponominaempleadoconcepto tnec
			INNER JOIN pr_tiponominaempleado ptne ON (ptne.CodTipoNom = tnec.CodTipoNom AND 
													  ptne.Periodo = tnec.Periodo AND 
													  ptne.CodPersona = tnec.CodPersona AND 
													  ptne.CodOrganismo = tnec.CodOrganismo AND 
													  ptne.CodTipoProceso = tnec.CodTipoProceso)
			INNER JOIN pr_concepto c ON (tnec.CodConcepto = c.CodConcepto AND c.Tipo = 'D')
		WHERE
			tnec.CodOrganismo = '".$fCodOrganismo."' AND
			tnec.CodTipoNom = '".$fCodTipoNom."' AND
			tnec.Periodo = '".$fPeriodo."' AND
			tnec.CodTipoProceso = '".$fCodTipoProceso."' $filtro1 $filtro
		GROUP BY CodConcepto
		ORDER BY CodConcepto";
$field_deducciones = getRecords($sql);
##	
$q = '';
$w = 110 + 10;
$Widths[] = 17;		$Aligns[] = 'R';	$Rows[] = utf8_decode('CEDULA');
$Widths[] = 80;		$Aligns[] = 'L';	$Rows[] = utf8_decode('NOMBRES Y APELLIDOS');
$Widths[] = 13;		$Aligns[] = 'C';	$Rows[] = utf8_decode('CARGO');
if ($FlagAsignaciones == 'I') {
	foreach($field_asignaciones as $f) {
		$Widths[] = 20;
		$Aligns[] = 'R';
		$Rows[] = utf8_decode($f['Abreviatura']);
		$Descripcion[] = utf8_decode($f['Descripcion']);
		$CodConcepto[] = $f['CodConcepto'];
		$w += 20;
		$q .= ", (SELECT Monto 
					FROM pr_tiponominaempleadoconcepto 
					WHERE
						CodOrganismo = '".$fCodOrganismo."' AND
						CodTipoNom = '".$fCodTipoNom."' AND
						Periodo = '".$fPeriodo."' AND
						CodTipoProceso = '".$fCodTipoProceso."' AND
						CodConcepto = '".$f['CodConcepto']."' AND
						CodPersona = p.CodPersona
					) AS '".$f['CodConcepto']."'";
	}
	$Widths[] = 20;		$Aligns[] = 'R';	$Rows[] = utf8_decode('T.ASIG.');	$CodConcepto[] = 'T.ASIG.';	$Descripcion[] = 'TOTAL ASIGNACIONES';
	$w += 20;
}
if ($FlagDeducciones == 'D') {
	foreach($field_deducciones as $f) {
		$Widths[] = 20;
		$Aligns[] = 'R';
		$Rows[] = utf8_decode($f['Abreviatura']);
		$Descripcion[] = utf8_decode($f['Descripcion']);
		$CodConcepto[] = $f['CodConcepto'];
		$w += 20;
		$q .= ", (SELECT Monto 
					FROM pr_tiponominaempleadoconcepto 
					WHERE
						CodOrganismo = '".$fCodOrganismo."' AND
						CodTipoNom = '".$fCodTipoNom."' AND
						Periodo = '".$fPeriodo."' AND
						CodTipoProceso = '".$fCodTipoProceso."' AND
						CodConcepto = '".$f['CodConcepto']."' AND
						CodPersona = p.CodPersona
					) AS '".$f['CodConcepto']."'";
	}
	$Widths[] = 20;		$Aligns[] = 'R';	$Rows[] = utf8_decode('T.DEDUC.');	$CodConcepto[] = 'T.DEDUC.';	$Descripcion[] = 'TOTAL DEDUCCIONES';
	$Widths[] = 20;		$Aligns[] = 'R';	$Rows[] = utf8_decode('TOTAL');		$CodConcepto[] = 'TOTAL';		$Descripcion[] = 'MONTO TOTAL';
	$w += 40;
}
//---------------------------------------------------

class PDF extends FPDF {
	//	Cabecera de página.
	function Header() {
		global $_PARAMETRO;
		global $field_proceso;
		global $Widths;
		global $Aligns;
		global $Rows;
		global $Leyenda;
		global $fPeriodo;
		global $_POST;
		extract($_POST);
		##
		if ($FlagAsignaciones == 'I' && $FlagDeducciones == 'D') $lista = 'RESUMEN DETALLADO DE CONCEPTOS';
		elseif ($FlagAsignaciones == 'I') $lista = 'LISTA DE ASIGNACIONES';
		elseif ($FlagDeducciones == 'D') $lista = 'LISTA DE DEDUCCIONES';
		$NomOrganismo = getValorCampo("mastorganismos", "CodOrganismo", "Organismo", $fCodOrganismo);
		$NomDependencia = getValorCampo("mastdependencias", "CodDependencia", "Dependencia", $_PARAMETRO["DEPRHPR"]);
		##
		$this->SetFillColor(255, 255, 255);
		$this->SetDrawColor(0, 0, 0);
		$this->SetFont('Arial', 'B', 8);
		$this->Cell(205, 5, strtoupper(utf8_decode($NomOrganismo)), 0, 1, 'L');
		$this->Cell(205, 5, strtoupper($NomDependencia), 0, 1, 'L');
		$this->Cell(205, 5, strtoupper(utf8_decode('RESUMEN DETALLADO DE CONCEPTOS - '.$field_proceso['Nomina'])), 0, 1, 'L');
		$this->Cell(205, 5, strtoupper(utf8_decode('MES DE '.getNombreMes($fPeriodo).' '.substr($fPeriodo,0,4).' '.$field_proceso['TipoProceso'])), 0, 1, 'L');
		$this->Cell(205, 5, 'DESDE '.formatFechaDMA($field_proceso['FechaDesde']).' HASTA '.formatFechaDMA($field_proceso['FechaHasta']), 0, 1, 'L');
		$this->Cell(205, 5, utf8_decode($lista), 0, 1, 'L');
		$this->Cell(205, 5, utf8_decode('PÁGINA ').$this->PageNo().' DE {nb}', 0, 1, 'L');
		##	-------------------
		$this->SetTextColor(0,0,0);
		$this->SetDrawColor(255,255,255);
		$this->SetFillColor(200,200,200);
		$this->Ln(2);
		##
		$this->SetFont('Arial','B',8);
		$this->SetWidths($Widths);
		$this->SetAligns($Aligns);
		if (!$Leyenda) $this->Row($Rows);
	}
	
	//	Pie de página.
	function Footer() {}
}
//---------------------------------------------------

//---------------------------------------------------
//	Creación del objeto de la clase heredada.
$pdf = new PDF('L', 'mm', array(215.9, $w));
$pdf->AliasNbPages();
$pdf->SetMargins(5, 5, 5);
$pdf->SetAutoPageBreak(5, 5);
$pdf->AddPage();
//---------------------------------------------------
$j = 0;
$sql = "SELECT
			  ptne.CodPersona,
			  ptne.TotalIngresos,
			  ptne.TotalEgresos,
			  ptne.TotalNeto,
			  p.Ndocumento,
			  p.NomCompleto,
			  c.CodDesc AS CodCargo
			  $q
		FROM
			  pr_tiponominaempleado ptne
			  INNER JOIN mastpersonas p ON (ptne.CodPersona = p.CodPersona)
			  INNER JOIN mastempleado e ON (p.CodPersona = e.CodPersona)
			  INNER JOIN rh_puestos c ON (e.CodCargo = c.CodCargo)
		WHERE
			  ptne.CodTipoNom = '".$fCodTipoNom."' AND
			  ptne.Periodo = '".$fPeriodo."' AND
			  ptne.CodOrganismo = '".$fCodOrganismo."' AND
			  ptne.CodTipoProceso = '".$fCodTipoProceso."' $filtro2 $filtro
		ORDER BY length(p.Ndocumento), Ndocumento";
$field = getRecords($sql);
foreach($field as $f) {
	$pdf->SetTextColor(0,0,0);
	if ($j % 2 == 0) { $pdf->SetDrawColor(255,255,255); $pdf->SetFillColor(255,255,255); }
	else { $pdf->SetDrawColor(230,230,230); $pdf->SetFillColor(230,230,230); }
	$pdf->SetFont('Arial','',8);
	$pdf->Cell($Widths[0], 5, number_format($f['Ndocumento'],0,'','.'), 1, 0, 'R',1);
	$pdf->Cell($Widths[1], 5, utf8_decode($f['NomCompleto']), 1, 0, 'L',1);
	$pdf->Cell($Widths[2], 5, $f['CodCargo'], 1, 0, 'C',1);
	##	conceptos
	$i = 0;
	for($wa=3; $wa<count($Widths); $wa++) {
		$index = $CodConcepto[$i];
		if ($CodConcepto[$i] == 'T.ASIG.') $Monto = $f['TotalIngresos'];
		elseif ($CodConcepto[$i] == 'T.DEDUC.') $Monto = $f['TotalEgresos'];
		elseif ($CodConcepto[$i] == 'TOTAL') $Monto = $f['TotalNeto'];
		else $Monto = $f[$index];
		$TotalConcepto[$index] += $Monto;
		##	
		$pdf->Cell($Widths[$wa], 5, number_format($Monto,2,',','.'), 1, 0, 'R',1);
		##	
		++$i;
	}
	$pdf->Ln();
	++$j;
}
##	
$pdf->SetTextColor(0,0,0);
$pdf->SetDrawColor(255,255,255);
$pdf->SetFillColor(200,200,200);
$pdf->SetFont('Arial','B',8);
$pdf->Cell(($Widths[0]+$Widths[1]+$Widths[2]), 5);
##	totales conceptos
$i = 0;
for($wa=3; $wa<count($Widths); $wa++) {
	$index = $CodConcepto[$i];
	##	
	$pdf->Cell($Widths[$wa], 5, number_format($TotalConcepto[$index],2,',','.'), 1, 0, 'R',1);
	##	
	++$i;
}
$pdf->Ln();
##	
$Leyenda = true;
$pdf->AddPage();
$pdf->SetTextColor(0,0,0);
$pdf->SetDrawColor(255,255,255);
$pdf->SetFillColor(200,200,200);
$pdf->SetFont('Arial','B',8);
$pdf->SetWidths(array(30,120));
$pdf->SetAligns(array('L','L'));
$pdf->Row(array('LEYENDA','DESCRIPCION'));
$i = 0;
for($wa=3; $wa<count($Widths); $wa++) {
	$index = $CodConcepto[$i];
	##	
	$pdf->SetTextColor(0,0,0);
	if ($i % 2 == 0) { $pdf->SetDrawColor(255,255,255); $pdf->SetFillColor(255,255,255); }
	else { $pdf->SetDrawColor(230,230,230); $pdf->SetFillColor(230,230,230); }
	$pdf->SetFont('Arial','',8);
	$pdf->Row(array($Rows[$wa], $Descripcion[$i]));
	##	
	++$i;
}
##	
$pdf->Ln(25);
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
