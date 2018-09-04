<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$filtro = '';
if ($fBuscar != "") {
	$filtro .= " AND (v.CodOrganismo LIKE '%".$fBuscar."%' OR
					  v.CodViatico LIKE '%".$fBuscar."%' OR
					  v.CodInterno LIKE '%".$fBuscar."%' OR
					  v.Periodo LIKE '%".$fBuscar."%' OR
					  v.Motivo LIKE '%".$fBuscar."%' OR
					  v.Monto LIKE '%".setNumero($fBuscar)."%' OR
					  v.FechaPreparado LIKE '%".formatFechaAMD($fBuscar)."%' OR
					  v.FechaRevisado LIKE '%".formatFechaAMD($fBuscar)."%' OR
					  p.NomCompleto LIKE '%".$fBuscar."%')";
}
if ($fEstado != "") $filtro.=" AND (v.Estado = '".$fEstado."')";
if ($fCodOrganismo != "") $filtro.=" AND (v.CodOrganismo = '".$fCodOrganismo."')";
if ($fCodDependencia != "") $filtro.=" AND (v.CodDependencia = '".$fCodDependencia."')";
if ($fPeriodo != "") $filtro.=" AND (v.Periodo = '".$fPeriodo."')";
if ($fFechaPreparadoD != "" || $fFechaPreparadoH != "") {
	if ($fFechaPreparadoD != "") $filtro.=" AND (v.FechaPreparado >= '".formatFechaAMD($fFechaPreparadoD)."')";
	if ($fFechaPreparadoH != "") $filtro.=" AND (v.FechaPreparado <= '".formatFechaAMD($fFechaPreparadoH)."')";
}
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
		$NomDependencia = getValorCampo("mastdependencias", "CodDependencia", "Dependencia", $_PARAMETRO["DEPAT"]);
		##
		$this->SetFillColor(255, 255, 255);
		$this->SetDrawColor(0, 0, 0);
		$this->Image($_PARAMETRO["PATHLOGO"].$Logo, 5, 5, 10, 10);		
		$this->SetFont('Arial', '', 6);
		$this->SetXY(15, 5); $this->Cell(100, 5, $NomOrganismo, 0, 1, 'L');
		$this->SetXY(15, 10); $this->Cell(100, 5, $NomDependencia, 0, 0, 'L');	
		$this->SetFont('Arial', '', 6);
		$this->SetXY(240, 5); $this->Cell(20, 5, utf8_decode('Fecha: '), 0, 0, 'L');
		$this->Cell(30, 5, formatFechaDMA(substr($Ahora, 0, 10)), 0, 1, 'L');
		$this->SetXY(240, 10); $this->Cell(20, 5, utf8_decode('Página: '), 0, 0, 'L'); 
		$this->Cell(30, 5, $this->PageNo().' de {nb}', 0, 1, 'L');
		$this->SetFont('Arial', 'B', 8);
		$this->SetY(20); $this->Cell(270, 5, utf8_decode('LISTADO DE VIATICOS'), 0, 1, 'C');
		$this->Ln(5);
		##	-------------------
		if ($fCodDependencia) {
			$Dependencia = getVar3("SELECT Dependencia FROM mastdependencias WHERE CodDependencia = '".$fCodDependencia."'");
			$this->SetFont('Arial', '', 6);
			$this->Cell(30, 5, utf8_decode('DEPENDENCIA: '), 0, 0, 'L');
			$this->SetFont('Arial', 'B', 6);
			$this->Cell(240, 5, utf8_decode($Dependencia), 0, 0, 'L');
			$this->Ln(5);
		}
		##	-------------------
		$this->SetTextColor(0, 0, 0);
		$this->SetDrawColor(0, 0, 0);
		$this->SetFillColor(255, 255, 255);
		$this->Ln(3);
		##
		$this->SetFont('Arial', 'B', 6);
		$this->SetWidths(array(10,12,10,55,20,20,15,125));
		$this->SetAligns(array('C','C','C','L','R','C','C','L'));
		$this->Row(array(utf8_decode('Código'),
						 utf8_decode('# Interno'),
						 utf8_decode('Periodo'),
						 utf8_decode('Beneficiario'),
						 utf8_decode('Monto'),
						 utf8_decode('Estado'),
						 utf8_decode('Fecha Preparado'),
						 utf8_decode('Motivo')
						 ));
		$this->Ln(1);
	}
	
	//	Pie de página.
	function Footer() {}
}
//---------------------------------------------------

//---------------------------------------------------
//	Creación del objeto de la clase heredada.
$pdf = new PDF('L', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->SetMargins(5, 5, 5);
$pdf->SetAutoPageBreak(5, 5);
//---------------------------------------------------
//	consulto
$sql = "SELECT
			v.CodOrganismo,
			v.CodViatico,
			v.CodInterno,
			v.Periodo,
			v.Motivo,
			v.Monto,
			v.FechaPreparado,
			v.FechaRevisado,
			v.Estado,
			p.NomCompleto AS NomPersona
		FROM
			ap_viaticos v
			INNER JOIN mastpersonas p ON (p.CodPersona = v.CodPersona)
		WHERE 1 $filtro
		ORDER BY CodViatico";
$field = getRecords($sql);
if (count($field) > 0) $pdf->AddPage();
foreach($field as $f) {
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetDrawColor(255, 255, 255);
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 6);
	$pdf->Row(array($f['CodViatico'],
					$f['CodInterno'],
					$f['Periodo'],
					utf8_decode($f['NomPersona']),
					number_format($f['Monto'],2,',','.'),
					utf8_decode(printValores("ESTADO-VIATICOS", $f['Estado'])),
					formatFechaDMA(substr($f['FechaPreparado'], 0, 10)),
					utf8_decode($f['Motivo'])
					));
	$pdf->Ln(2);
}
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
