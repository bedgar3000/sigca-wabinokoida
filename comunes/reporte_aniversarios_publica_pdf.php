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
if ($fVence2 != "") {
	$Fecha = "'".formatFechaAMD($fVence2)."'";
} else $Fecha = "CURDATE()";
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
		$this->SetXY(10, 20); $this->MultiCell(260, 5, utf8_decode('CUADRO DEMOSTRATIVO DE LOS FUNCIONARIOS Y TRABAJADORES CON RECONOCIMIENTO POR AÑOS DE SERVICIOS EN LA ADMINISTRACION PUBLICA'), 0, 'C', 0);
		$this->Ln(5);
		##
		$this->SetFillColor(255, 255, 255);
		$this->SetDrawColor(255, 255, 255);
		$this->SetTextColor(0, 0, 0);
		$this->SetFont('Arial', 'B', 8);
		$this->SetWidths(array(22, 77, 80, 20, 10, 17, 17, 17));
		$this->SetAligns(array('R', 'L', 'L', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
		$this->Row(array('DOCUMENTO',
						 'NOMBRE COMPLETO',
						 'CARGO',
						 'FECHA INGRESO',
						 'EDAD',
						 'TIEMPO ACTUAL',
						 'TIEMPO ADM. PUB.',
						 'TIEMPO TOTAL'));
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
			p.CodPersona,
			p.Ndocumento,
			p.NomCompleto,
			p.Fnacimiento,
			e.CodEmpleado,
			e.Fingreso,
			SUBSTRING(e.Fingreso, 6, 2) AS MesIngreso,
			(YEAR(CURDATE()) - YEAR(p.Fnacimiento)) - (RIGHT(CURDATE(), 5) < RIGHT(p.Fnacimiento, 5)) AS Edad,
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
	list($AniosAntecedente, $MesesAntecedente, $DiasAntecedente) = getTiempoAntecedente($field['CodPersona']);
	$Total = $field['Anios'] + $AniosAntecedente;
	##
	$Mostrar = false;
	if ($fEdadD == "" && $fEdadH == "") $Mostrar = true;
	elseif ($fEdadD != "" && $Total >= $fEdadD && $fEdadH != "" && $Total <= $fEdadH) $Mostrar = true;
	elseif ($fEdadD != "" && $Total >= $fEdadD && $fEdadH == "") $Mostrar = true;
	elseif ($fEdadH != "" && $Total <= $fEdadH && $fEdadD == "") $Mostrar = true;
	##
	if ($Mostrar) {
		++$i;
		##
		if ($i % 2 == 0) { $pdf->SetFillColor(255, 255, 255); $pdf->SetDrawColor(255, 255, 255); }
		else { $pdf->SetFillColor(240, 240, 240); $pdf->SetDrawColor(240, 240, 240); }
		$pdf->SetFont('Arial', '', 8);
		$pdf->Row(array(number_format($field['Ndocumento'], 0, '', '.'),
						utf8_decode($field['NomCompleto']),
						utf8_decode($field['DescripCargo']),
						formatFechaDMA($field['Fingreso']),
						$field['Edad'],
						$field['Anios'],
						$AniosAntecedente,
						$Total));
		$pdf->Ln(1);
	}
}
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  