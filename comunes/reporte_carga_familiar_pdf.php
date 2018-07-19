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
$filtro_empleado = "";
if ($fCodOrganismo != "") $filtro_empleado .= " AND (e.CodOrganismo = '".$fCodOrganismo."')";
if ($fEdoReg != "") $filtro_empleado .= " AND (p.Estado = '".$fEdoReg."')";
if ($fCodDependencia != "") $filtro_empleado .= " AND (e.CodDependencia = '".$fCodDependencia."')";
if ($fSitTra != "") $filtro_empleado .= " AND (e.Estado = '".$fSitTra."')";
if ($fCodCentroCosto != "") $filtro_empleado .= " AND (e.CodCentroCosto = '".$fCodCentroCosto."')";
if ($fCodTipoNom != "") $filtro_empleado .= " AND (e.CodTipoNom = '".$fCodTipoNom."')";
if ($fSexo != "") $filtro_empleado .= " AND (p.Sexo = '".$fSexo."')";
if ($fBuscar != "") {
	$filtro_empleado.= " AND (e.CodEmpleado LIKE '%".$fbuscar."%' OR 
							  p.NomCompleto LIKE '%".$fbuscar."%')";
}
$filtro_carga = "";
if ($fSexoFam != "") $filtro_carga .= " AND (cf.Sexo = '".$fSexoFam."')";
if ($fParentesco != "") $filtro_carga .= " AND (cf.Parentesco = '".$fParentesco."')";
if ($fEstadoCivil != "") $filtro_carga .= " AND (cf.EstadoCivil = '".$fEstadoCivil."')";
if ($fEdadD != "") $filtro_carga .= " AND ((YEAR(CONCAT(SUBSTRING(CURDATE(), 1, 4), '-', SUBSTRING(cf.FechaNacimiento, 6, 2), '-', SUBSTRING(cf.FechaNacimiento, 9, 2))) - YEAR(cf.FechaNacimiento)) - (RIGHT(CONCAT(SUBSTRING(CURDATE(), 1, 4), '-', SUBSTRING(cf.FechaNacimiento, 6, 2), '-', SUBSTRING(cf.FechaNacimiento, 9, 2)), 5)<RIGHT(cf.FechaNacimiento, 5)) >= '".$fEdadD."')";
if ($fEdadH != "") $filtro_carga .= " AND ((YEAR(CONCAT(SUBSTRING(CURDATE(), 1, 4), '-', SUBSTRING(cf.FechaNacimiento, 6, 2), '-', SUBSTRING(cf.FechaNacimiento, 9, 2))) - YEAR(cf.FechaNacimiento)) - (RIGHT(CONCAT(SUBSTRING(CURDATE(), 1, 4), '-', SUBSTRING(cf.FechaNacimiento, 6, 2), '-', SUBSTRING(cf.FechaNacimiento, 9, 2)), 5)<RIGHT(cf.FechaNacimiento, 5)) <= '".$fEdadH."')";
if ($fAfiliado == "S") $filtro_carga .= " AND (cf.Afiliado = 'S')";
if ($fFlagDiscapacidad == "S") $filtro_carga .= " AND (cf.FlagDiscapacidad = 'S')";
if ($fFlagEstudia == "S") $filtro_carga .= " AND (cf.FlagEstudia = 'S')";
//---------------------------------------------------

class PDF extends FPDF {
	//	Cabecera de página.
	function Header() {
		global $_PARAMETRO;
		global $Ahora;
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
		$this->Image($_PARAMETRO["PATHLOGO"].$Logo, 1, 5, 10, 10);
		$this->SetFont('Arial', '', 8);
		$this->SetXY(12, 5); $this->Cell(100, 5, utf8_decode($NomOrganismo), 0, 1, 'L');
		$this->SetXY(12, 10); $this->Cell(100, 5, utf8_decode($NomDependencia), 0, 0, 'L');
		$this->SetFont('Arial', '', 8);
		$this->SetXY(185, 5); $this->Cell(10, 5, utf8_decode('Fecha: '), 0, 0, 'L');
		$this->Cell(30, 5, formatFechaDMA(substr($Ahora, 0, 10)), 0, 1, 'L');
		$this->SetXY(185, 10); $this->Cell(10, 5, utf8_decode('Página: '), 0, 0, 'L');
		$this->Cell(30, 5, $this->PageNo().' de {nb}', 0, 1, 'L');
		$this->SetFont('Arial', 'B', 10);
		$this->SetY(20); $this->Cell(214, 5, utf8_decode('Relación de Carga Familiar'), 0, 1, 'C', 0);
		$this->Ln(5);
		##
		if ($fCodDependencia != "") {
			$fNomDependencia = getVar3("SELECT Dependencia FROM mastdependencias WHERE CodDependencia = '".$fCodDependencia."'");
			$this->SetFillColor(230, 230, 230);
			$this->SetFont('Arial', 'B', 8);
			$this->Cell(30, 5, 'DEPENDENCIA: ', 0, 0, 'L', 1);
			$this->SetFont('Arial', '', 8);
			$this->Cell(184, 5, utf8_decode($fNomDependencia), 0, 1, 'L', 0);
			$this->Ln(1);
		}
		if ($fCodCentroCosto != "") {
			$fNomCentroCosto = getVar3("SELECT Descripcion FROM ac_mastcentrocosto WHERE CodCentroCosto = '".$fCodCentroCosto."'");
			$this->SetFillColor(230, 230, 230);
			$this->SetFont('Arial', 'B', 8);
			$this->Cell(30, 5, 'CENTRO DE COSTO: ', 0, 0, 'L', 1);
			$this->SetFont('Arial', '', 8);
			$this->Cell(184, 5, utf8_decode($fNomCentroCosto), 0, 1, 'L', 0);
			$this->Ln(1);
		}
		if ($fCodTipoNom != "") {
			$fNomina = getVar3("SELECT Nomina FROM tiponomina WHERE CodTipoNom = '".$fCodTipoNom."'");
			$this->SetFillColor(230, 230, 230);
			$this->SetFont('Arial', 'B', 8);
			$this->Cell(30, 5, utf8_decode('NÓMINA: '), 0, 0, 'L', 1);
			$this->SetFont('Arial', '', 8);
			$this->Cell(184, 5, utf8_decode($fNomina), 0, 1, 'L', 0);
			$this->Ln(1);
		}
		$this->Ln(5);
		##
		$this->SetFillColor(255, 255, 255);
		$this->SetDrawColor(255, 255, 255);
		$this->SetTextColor(0, 0, 0);
		$this->SetFont('Arial', 'B', 8);
		$this->SetWidths(array(22, 100, 27, 20, 15, 10, 10, 10));
		$this->SetAligns(array('R', 'L', 'C', 'C', 'C', 'C', 'C', 'C'));
		$this->Row(array('DOC. IDENTIDAD',
						 'NOMBRE COMPLETO',
						 'PARENTESCO',
						 'FECHA NACIMIENTO',
						 'EDAD',
						 'SEG.',
						 'ESP.',
						 'EST.'));
		$this->SetDrawColor(0, 0, 0);
		$this->Line(1, $this->GetY(), 215, $this->GetY());
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
$pdf->SetMargins(1, 1, 1);
$pdf->SetAutoPageBreak(1, 1);
$pdf->AddPage();
//---------------------------------------------------
$pdf->SetFillColor(255, 255, 255);
$pdf->SetDrawColor(255, 255, 255);
$pdf->SetTextColor(0, 0, 0);
//	consulto datos
$i = 0;
$Nro = 0;
$sql = "SELECT
			p.Ndocumento AS DocEmpleado,
			p.NomCompleto AS NomEmpleado,
			p.CodPersona,
			e.CodEmpleado,
			d.Dependencia,
			cf.Ndocumento AS DocCargaFamiliar,
			CONCAT(cf.NombresCarga, ' ', cf.ApellidosCarga) NomCargaFamiliar,
			cf.FechaNacimiento,
			(YEAR(CONCAT(SUBSTRING(CURDATE(), 1, 4), '-', SUBSTRING(cf.FechaNacimiento, 6, 2), '-', SUBSTRING(cf.FechaNacimiento, 9, 2))) - YEAR(cf.FechaNacimiento)) - (RIGHT(CONCAT(SUBSTRING(CURDATE(), 1, 4), '-', SUBSTRING(cf.FechaNacimiento, 6, 2), '-', SUBSTRING(cf.FechaNacimiento, 9, 2)), 5) < RIGHT(cf.FechaNacimiento, 5)) AS Edad,
			cf.Afiliado,
			cf.FlagDiscapacidad,
			cf.FlagEstudia,
			md.Descripcion AS NomParentesco,
			pt.DescripCargo
		FROM
			rh_cargafamiliar cf
			INNER JOIN mastpersonas p ON (p.CodPersona = cf.CodPersona)
			INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
			INNER JOIN mastdependencias d ON (d.CodDependencia = e.CodDependencia)
			INNER JOIN rh_puestos pt ON (pt.CodCargo = e.CodCargo)
			LEFT JOIN mastmiscelaneosdet md ON (md.CodDetalle = cf.Parentesco AND
												md.CodMaestro = 'PARENT' AND
												md.CodAplicacion = 'RH')
		WHERE cf.Estado = 'A' $filtro_empleado $filtro_carga
		ORDER BY e.CodDependencia, LENGTH(p.Ndocumento), p.Ndocumento, cf.FechaNacimiento";
$field_carga = getRecords($sql);
foreach($field_carga as $f) {
	++$i;
	##
	if ($f['Afiliado'] == "S") $Afiliado = "X"; else $Afiliado = "";
	if ($f['FlagDiscapacidad'] == "S") $FlagDiscapacidad = "X"; else $FlagDiscapacidad = "";
	if ($f['FlagEstudia'] == "S") $FlagEstudia = "X"; else $FlagEstudia = "";
	if ($f['DocCargaFamiliar'] == 0) $DocCargaFamiliar = ""; else $DocCargaFamiliar = number_format($f['DocCargaFamiliar'], 0, '', '.');
	if ($Grupo != $f['CodPersona']) {
		$Grupo = $f['CodPersona'];
		$Nro = 0;
		$pdf->SetFont('Arial', 'B', 8.5);
		$pdf->Cell(22, 5, number_format($f['DocEmpleado'], 0, '', '.'), 0, 0, 'R', 0);
		$pdf->Cell(76, 5, utf8_decode($f['NomEmpleado']), 0, 0, 'L', 0);
		if ($fCodDependencia == "") $pdf->Cell(116, 5, substr(utf8_decode($f['Dependencia']), 0, 60), 0, 0, 'L', 0);
		$pdf->Ln(5);
		$pdf->Cell(22, 5); $pdf->Cell(76, 5, utf8_decode($f['DescripCargo']), 0, 0, 'L', 0);
		$pdf->Ln(5);
	}
	$pdf->SetFont('Arial', '', 8);
	$pdf->Row(array($DocCargaFamiliar,
					utf8_decode($f['NomCargaFamiliar']),
					utf8_decode($f['NomParentesco']),
					formatFechaDMA($f['FechaNacimiento']),
					$f['Edad'],
					$Afiliado,
					$FlagDiscapacidad,
					$FlagEstudia));
	$pdf->Ln(1);
	++$Nro;
}
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  