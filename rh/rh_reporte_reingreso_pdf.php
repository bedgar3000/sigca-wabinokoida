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
if ($fCodOrganismo != "") $filtro.=" AND (pc.CodOrganismo = '".$fCodOrganismo."')";
if ($fCodDependencia != "") $filtro.=" AND (pc.CodDependencia = '".$fCodDependencia."')";
if ($fCodTipoNom != "") $filtro.=" AND (pc.CodTipoNom = '".$fCodTipoNom."')";
if ($fTipo != "") $filtro.=" AND (pc.Tipo = '".$fTipo."')";
if ($fBuscar != "") $filtro .= " AND (pc.Tipo LIKE '%".$fBuscar."%' OR
									  pc.CodProceso LIKE '%".$fBuscar."%' OR
									  pc.Fecha LIKE '%".$fBuscar."%' OR
									  e.CodEmpleado LIKE '%".$fBuscar."%' OR
									  p.NomCompleto LIKE '%".$fBuscar."%')";
if ($fFechaD != "") $filtro .= " AND (FechaDesde >= '".$fFechaD."')";
if ($fFechaH != "") $filtro .= " AND (FechaHasta <= '".$fFechaH."')";
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
		$this->Ln(5);
		$this->SetFont('Arial', 'B', 10);
		$this->Cell(210, 5, utf8_decode('Listado de Ceses/Reingresos'), 0, 1, 'C', 0);
		$this->Ln(5);
		##	
		if ($fCodDependencia) {
			$Dependencia = getVar3("SELECT Dependencia FROM mastdependencias WHERE CodDependencia = '$fCodDependencia'");
			$this->SetFont('Arial', 'B', 8);
			$this->Cell(210, 5, utf8_decode('DEPENDENCIA: '.$Dependencia), 0, 1, 'L', 0);
		}
		if ($fCodTipoNom) {
			$Nomina = getVar3("SELECT Nomina FROM tiponomina WHERE CodTipoNom = '$fCodTipoNom'");
			$this->SetFont('Arial', 'B', 8);
			$this->Cell(210, 5, utf8_decode('NÓMINA: '.$Nomina), 0, 1, 'L', 0);
		}
		$this->SetDrawColor(0, 0, 0);
		$this->SetFillColor(200, 200, 200);
		$this->SetWidths(array(10,20,20,110,25,25));
		$this->SetAligns(array('C','C','C','L','C','C'));
		$this->SetFont('Arial', 'B', 8);
		$this->Row(array('#',
						 'Nro. Proceso',
						 'Empleado',
						 'Nombre Completo',
						 'Tipo',
						 'Fecha'));
		$this->Ln(1);
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
$pdf->AddPage();
//---------------------------------------------------
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(255, 255, 255);
//	consulto
$i = 0;
$sql = "SELECT
			pc.Tipo,
			pc.CodProceso,
			pc.Fecha,
			pc.Estado,
			e.CodEmpleado,
			p.NomCompleto
		FROM
			rh_procesocesereing pc
			INNER JOIN mastpersonas p ON (p.CodPersona = pc.CodPersona)
			INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
		WHERE 1 $filtro";
$field = getRecords($sql);
foreach($field as $f) {	++$i;
	if ($i % 2 == 0) { $pdf->SetFillColor(240, 240, 240); $pdf->SetDrawColor(240, 240, 240);  }
	else { $pdf->SetFillColor(255, 255, 255); $pdf->SetDrawColor(255, 255, 255); }
	$pdf->SetFont('Arial', '', 8);
	$pdf->Row(array($i,
					$f['CodProceso'],
					$f['CodEmpleado'],
					utf8_decode($f['NomCompleto']),
					printValores("TIPO-REINGRESO", $f['Tipo']),
					formatFechaDMA($f['Fecha'])));
	$pdf->Ln(1);
}
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
