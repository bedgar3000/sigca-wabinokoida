<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
$FechaActual = "$AnioActual-$MesActual-$DiaActual";
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
if ($fMes != "") $filtro .= " AND (p.Fnacimiento LIKE '%-".$fMes."-%')";
if ($fEdadD != "") $filtro .= " AND ((YEAR(CONCAT(SUBSTRING(CURDATE(), 1, 4), '-', SUBSTRING(p.Fnacimiento, 6, 2), '-', SUBSTRING(p.Fnacimiento, 9, 2))) - YEAR(p.Fnacimiento)) - (RIGHT(CONCAT(SUBSTRING(CURDATE(), 1, 4), '-', SUBSTRING(p.Fnacimiento, 6, 2), '-', SUBSTRING(p.Fnacimiento, 9, 2)), 5)<RIGHT(p.Fnacimiento, 5)) >= '".$fEdadD."')";
if ($fEdadH != "") $filtro .= " AND ((YEAR(CONCAT(SUBSTRING(CURDATE(), 1, 4), '-', SUBSTRING(p.Fnacimiento, 6, 2), '-', SUBSTRING(p.Fnacimiento, 9, 2))) - YEAR(p.Fnacimiento)) - (RIGHT(CONCAT(SUBSTRING(CURDATE(), 1, 4), '-', SUBSTRING(p.Fnacimiento, 6, 2), '-', SUBSTRING(p.Fnacimiento, 9, 2)), 5)<RIGHT(p.Fnacimiento, 5)) <= '".$fEdadH."')";
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
		$this->SetFont('Arial', '', 8);
		$this->SetXY(180, 5); $this->Cell(10, 5, utf8_decode('Fecha: '), 0, 0, 'L');
		$this->Cell(30, 5, formatFechaDMA(substr($Ahora, 0, 10)), 0, 1, 'L');
		$this->SetXY(180, 10); $this->Cell(10, 5, utf8_decode('Página: '), 0, 0, 'L');
		$this->Cell(30, 5, $this->PageNo().' de {nb}', 0, 1, 'L');
		$this->SetFont('Arial', 'B', 10);
		$this->SetXY(10, 20); $this->Cell(200, 5, utf8_decode('Personal de Cumpleaños'), 0, 1, 'C', 0);
		$this->Ln(5);
		##
		$this->SetFillColor(255, 255, 255);
		$this->SetDrawColor(255, 255, 255);
		$this->SetTextColor(0, 0, 0);
		$this->SetFont('Arial', 'B', 8);
		$this->SetWidths(array(22, 105, 40, 20, 13));
		$this->SetAligns(array('R', 'L', 'L', 'C', 'C'));
		$this->Row(array('DOCUMENTO',
						 'NOMBRE COMPLETO',
						 'NOMINA',
						 'FECHA NACIMIENTO',
						 'EDAD'));
		$this->SetDrawColor(0, 0, 0);
		$this->Line(10, $this->GetY(), 210, $this->GetY());
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
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(5, 5);
$pdf->AddPage();
//---------------------------------------------------
$pdf->SetFillColor(255, 255, 255);
$pdf->SetDrawColor(255, 255, 255);
$pdf->SetTextColor(0, 0, 0);
//	consulto datos
$i = 0;
$Nro = 0;
$sql = "SELECT
			p.Ndocumento,
			p.NomCompleto,
			e.CodEmpleado,
			p.Fnacimiento,
			SUBSTRING(p.Fnacimiento, 6, 2) AS MesNacimiento,
			SUBSTRING(p.Fnacimiento, 9, 2) AS DiaNacimiento,
			(YEAR(CONCAT(SUBSTRING(CURDATE(), 1, 4), '-', SUBSTRING(p.Fnacimiento, 6, 2), '-', SUBSTRING(p.Fnacimiento, 9, 2))) - YEAR(p.Fnacimiento)) - (RIGHT(CONCAT(SUBSTRING(CURDATE(), 1, 4), '-', SUBSTRING(p.Fnacimiento, 6, 2), '-', SUBSTRING(p.Fnacimiento, 9, 2)), 5)<RIGHT(p.Fnacimiento, 5)) AS Anios,
			tn.Nomina,
			pt.DescripCargo
		FROM
			mastpersonas p
			INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
			INNER JOIN tiponomina tn ON (tn.CodTipoNom = e.CodTipoNom)
			INNER JOIN rh_puestos pt ON (pt.CodCargo = e.CodCargo)
		WHERE 1 $filtro
		ORDER BY MesNacimiento, DiaNacimiento, Fnacimiento";
$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
while($field = mysql_fetch_array($query)) {
	++$i;
	##
	if ($Grupo != $field['MesNacimiento']) {
		if ($Nro > 0) {
			$pdf->SetFillColor(255, 255, 255);
			$pdf->SetDrawColor(255, 255, 255);
			$pdf->SetFont('Arial', 'BI', 8);
			$pdf->Cell(40, 5);
			$pdf->Cell(30, 5, 'Nro. Trabajadores: ', 0, 0, 'L', 0);
			$pdf->Cell(10, 5, $Nro, 0, 1, 'L', 0);
		}
		$Grupo = $field['MesNacimiento'];
		$Nro = 0;
		$pdf->Ln(3);
		$pdf->SetFont('Arial', 'B', 8);
		$pdf->Cell(200, 5, getNombreMes($field['Fnacimiento']), 0, 1, 'L', 0);
	}
	if ($Nro % 2 == 0) { $pdf->SetFillColor(255, 255, 255); $pdf->SetDrawColor(255, 255, 255); }
	else { $pdf->SetFillColor(240, 240, 240); $pdf->SetDrawColor(240, 240, 240); }
	$pdf->SetFont('Arial', '', 8);
	$pdf->Row(array(number_format($field['Ndocumento'], 0, '', '.'),
					utf8_decode($field['NomCompleto']),
					utf8_decode($field['Nomina']),
					formatFechaDMA($field['Fnacimiento']),
					$field['Anios']));
	$pdf->Ln(1);
	++$Nro;
}
$pdf->SetFillColor(255, 255, 255);
$pdf->SetDrawColor(255, 255, 255);
$pdf->SetFont('Arial', 'BI', 8);
$pdf->Cell(40, 5);
$pdf->Cell(30, 5, 'Nro. Trabajadores: ', 0, 0, 'L', 0);
$pdf->Cell(10, 5, $Nro, 0, 1, 'L', 0);
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  