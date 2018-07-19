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
if ($fCodOrganismo != "") $filtro .= " AND (e.CodOrganismo = '".$fCodOrganismo."')";
if ($fEdoReg != "") $filtro .= " AND (p.Estado = '".$fEdoReg."')";
if ($fCodDependencia != "") $filtro .= " AND (e.CodDependencia = '".$fCodDependencia."')";
if ($fSitTra != "") $filtro .= " AND (e.Estado = '".$fSitTra."')";
if ($fCodCentroCosto != "") $filtro .= " AND (e.CodCentroCosto = '".$fCodCentroCosto."')";
if ($fCodTipoNom != "") $filtro .= " AND (e.CodTipoNom = '".$fCodTipoNom."')";
if ($fCodTipoTrabajador != "") $filtro .= " AND (e.CodTipoTrabajador = '".$fCodTipoTrabajador."')";
if ($fMes != "") $filtro .= " AND (e.Fingreso LIKE '%-".$fMes."-%')";
if ($fEdadD != "") $filtro .= " AND ((YEAR(CONCAT(SUBSTRING(CURDATE(), 1, 4), '-', SUBSTRING(e.Fingreso, 6, 2), '-', SUBSTRING(e.Fingreso, 9, 2))) - YEAR(e.Fingreso)) - (RIGHT(CONCAT(SUBSTRING(CURDATE(), 1, 4), '-', SUBSTRING(e.Fingreso, 6, 2), '-', SUBSTRING(e.Fingreso, 9, 2)), 5)<RIGHT(e.Fingreso, 5)) >= '".$fEdadD."')";
if ($fEdadH != "") $filtro .= " AND ((YEAR(CONCAT(SUBSTRING(CURDATE(), 1, 4), '-', SUBSTRING(e.Fingreso, 6, 2), '-', SUBSTRING(e.Fingreso, 9, 2))) - YEAR(e.Fingreso)) - (RIGHT(CONCAT(SUBSTRING(CURDATE(), 1, 4), '-', SUBSTRING(e.Fingreso, 6, 2), '-', SUBSTRING(e.Fingreso, 9, 2)), 5)<RIGHT(e.Fingreso, 5)) <= '".$fEdadH."')";
if ($fVence1 != "") {
	$Fecha = "'".formatFechaAMD($fVence1)."'";
} else $Fecha = "CURDATE()";
if ($FlagMostrar != "S") {
	$filtro .= "AND ((YEAR($Fecha) - YEAR(e.Fingreso)) - (RIGHT($Fecha, 5) < RIGHT(e.Fingreso, 5)) = 5 OR
					 (YEAR($Fecha) - YEAR(e.Fingreso)) - (RIGHT($Fecha, 5) < RIGHT(e.Fingreso, 5)) = 10 OR
					 (YEAR($Fecha) - YEAR(e.Fingreso)) - (RIGHT($Fecha, 5) < RIGHT(e.Fingreso, 5)) = 15 OR
					 (YEAR($Fecha) - YEAR(e.Fingreso)) - (RIGHT($Fecha, 5) < RIGHT(e.Fingreso, 5)) = 20 OR
					 (YEAR($Fecha) - YEAR(e.Fingreso)) - (RIGHT($Fecha, 5) < RIGHT(e.Fingreso, 5)) = 25 OR
					 (YEAR($Fecha) - YEAR(e.Fingreso)) - (RIGHT($Fecha, 5) < RIGHT(e.Fingreso, 5)) = 30)";
}
//---------------------------------------------------

class PDF extends FPDF {
	//	Cabecera de página.
	function Header() {
		global $_PARAMETRO;
		global $Ahora;
		global $field;
		global $field_sn;
		global $_POST;
		global $_GET;
		extract($_POST);
		extract($_GET);
		##
		$Logo = getValorCampo("mastorganismos", "CodOrganismo", "Logo", $fCodOrganismo);
		$NomOrganismo = getValorCampo("mastorganismos", "CodOrganismo", "Organismo", $fCodOrganismo);
		$NomDependencia = getValorCampo("mastdependencias", "CodDependencia", "Dependencia", $_PARAMETRO["DEPRHPR"]);
		##
		$this->SetFillColor(255, 255, 255);
		$this->SetDrawColor(0, 0, 0);
		$this->Image($_PARAMETRO["PATHLOGO"].$Logo, 10, 5, 10, 10);
		$this->SetFont('Arial', '', 8);
		$this->SetXY(20, 5); $this->Cell(100, 5, utf8_decode($NomOrganismo), 0, 1, 'L');
		$this->SetXY(20, 10); $this->Cell(100, 5, utf8_decode($NomDependencia), 0, 0, 'L');
		$this->SetFont('Arial', 'B', 10);
		$this->SetXY(10, 20); $this->MultiCell(260, 5, utf8_decode('CUADRO DEMOSTRATIVO DE LOS FUNCIONARIOS Y TRABAJADORES CON RECONOCIMIENTO POR AÑOS DE SERVICIOS EN ESTE ORGANISMO'), 0, 'C', 0);
		$this->Ln(5);
		##
		$this->SetFillColor(255, 255, 255);
		$this->SetDrawColor(255, 255, 255);
		$this->SetTextColor(0, 0, 0);
		$this->SetFont('Arial', 'B', 8);
		$this->SetWidths(array(22, 60, 64, 17, 13, 14, 14, 14, 14, 14, 14));
		$this->SetAligns(array('R', 'L', 'L', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
		$this->Row(array('DOCUMENTO',
						 'NOMBRE COMPLETO',
						 'CARGO',
						 'FECHA INGRESO',
						 'TIEMPO',
						 utf8_decode('5   AÑOS'),
						 utf8_decode('10 AÑOS'),
						 utf8_decode('15 AÑOS'),
						 utf8_decode('20 AÑOS'),
						 utf8_decode('25 AÑOS'),
						 utf8_decode('30 AÑOS')));
		$this->SetDrawColor(0, 0, 0);
		$this->Line(10, $this->GetY(), 270, $this->GetY());
		$this->Ln(2);
	}
	
	//	Pie de página.
	function Footer() {}
}
//---------------------------------------------------

//---------------------------------------------------
//	Creación del objeto de la clase heredada.
$pdf = new PDF('L', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(5, 5);
$pdf->AddPage();
//---------------------------------------------------
$pdf->SetFillColor(255, 255, 255);
$pdf->SetDrawColor(255, 255, 255);
$pdf->SetTextColor(0, 0, 0);
//	consulto datos
$i = 0;
$sql = "SELECT
			p.Ndocumento,
			p.NomCompleto,
			e.CodEmpleado,
			e.Fingreso,
			SUBSTRING(e.Fingreso, 6, 2) AS MesIngreso,
			(YEAR(CONCAT(SUBSTRING(CURDATE(), 1, 4), '-', SUBSTRING(e.Fingreso, 6, 2), '-', SUBSTRING(e.Fingreso, 9, 2))) - YEAR(e.Fingreso)) - (RIGHT(CONCAT(SUBSTRING(CURDATE(), 1, 4), '-', SUBSTRING(e.Fingreso, 6, 2), '-', SUBSTRING(e.Fingreso, 9, 2)), 5)<RIGHT(e.Fingreso, 5)) AS Anios,
			pt.DescripCargo
		FROM
			mastpersonas p
			INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
			INNER JOIN rh_puestos pt ON (pt.CodCargo = e.CodCargo)
		WHERE 1 $filtro
		ORDER BY LENGTH(Ndocumento), Ndocumento";
$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
while($field = mysql_fetch_array($query)) {
	if ($field['Anios'] >= 30) {
		$x5 = "X"; $x10 = "X"; $x15 = "X"; $x20 = "X"; $x25 = "X"; $x30 = "X";
	}
	elseif ($field['Anios'] >= 25 && $field['Anios'] <= 29) {
		$x5 = "X"; $x10 = "X"; $x15 = "X"; $x20 = "X"; $x25 = "X"; $x30 = "";
	}
	elseif ($field['Anios'] >= 20 && $field['Anios'] <= 24) {
		$x5 = "X"; $x10 = "X"; $x15 = "X"; $x20 = "X"; $x25 = ""; $x30 = "";
	}
	elseif ($field['Anios'] >= 15 && $field['Anios'] <= 19) {
		$x5 = "X"; $x10 = "X"; $x15 = "X"; $x20 = ""; $x25 = ""; $x30 = "";
	}
	elseif ($field['Anios'] >= 10 && $field['Anios'] <= 14) {
		$x5 = "X"; $x10 = "X"; $x15 = ""; $x20 = ""; $x25 = ""; $x30 = "";
	}
	elseif ($field['Anios'] >= 5 && $field['Anios'] <= 9) {
		$x5 = "X"; $x10 = ""; $x15 = ""; $x20 = ""; $x25 = ""; $x30 = "";
	}
	else {
		$x5 = ""; $x10 = ""; $x15 = ""; $x20 = ""; $x25 = ""; $x30 = "";
	}
	##
	++$i;
	##
	if ($i % 2 == 0) { $pdf->SetFillColor(255, 255, 255); $pdf->SetDrawColor(255, 255, 255); }
	else { $pdf->SetFillColor(240, 240, 240); $pdf->SetDrawColor(240, 240, 240); }
	$pdf->SetFont('Arial', '', 8);
	$pdf->Row(array(number_format($field['Ndocumento'], 0, '', '.'),
					utf8_decode($field['NomCompleto']),
					utf8_decode($field['DescripCargo']),
					formatFechaDMA($field['Fingreso']),
					$field['Anios'],
					$x5,
					$x10,
					$x15,
					$x20,
					$x25,
					$x30));
	$pdf->Ln(1);
}
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  