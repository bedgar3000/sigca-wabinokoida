<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$LF = 0x0A;
$CR = 0x0D;
$nl = sprintf("%c%c",$CR,$LF);
//---------------------------------------------------
//	consulto datos
$sql = "SELECT
			p.NomCompleto,
			p.Nacionalidad,
			p.Ndocumento,
			e.CodOrganismo,
			e.Fingreso,
			e.SueldoActual AS SueldoBasico,
			e.CodtipoNom,
			pt.DescripCargo
		FROM
			mastpersonas p
			INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
			INNER JOIN rh_puestos pt ON (pt.CodCargo = e.CodCargo)
		WHERE p.CodPersona = '".$sel_registros."'";
$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
##
if ($field['Sexo'] == "F" && $field['CodTipoNom'] == "02") $funcionario = "la trabajadora";
elseif ($field['Sexo'] == "F") $funcionario = "la funcionaria"; 
elseif ($field['Sexo'] == "M" && $field['CodTipoNom'] == "02") $funcionario = "el trabajador";
elseif ($field['Sexo'] == "M") $funcionario = "el funcionario";
$NomCompleto = trim(strtoupper($field['NomCompleto']));
$Ndocumento = number_format($field['Ndocumento'], 0, '', '.');
$Fingreso = formatFechaDMA($field['Fingreso']);
$DescripCargo = trim(strtoupper($field['DescripCargo']));
##
if ($fPeriodo) $filtro_sueldo = " AND Periodo <= '$fPeriodo'";
$sql = "SELECT * FROM rh_sueldos WHERE CodPersona = '".$sel_registros."' $filtro_sueldo ORDER BY Periodo DESC LIMIT 0, 1";
$field_sueldo = getRecord($sql);
$SueldoBasico = strtoupper(convertir_a_letras($field['SueldoBasico'], "moneda")." (Bs. ".number_format($field['SueldoBasico'], 2, ',', '.').")");
$Diferencia = number_format(($field_sueldo['SueldoNormal']-$field_sueldo['Sueldo']), 2, '.', '');
$Primas = strtoupper(convertir_a_letras($Diferencia, "moneda")." (Bs. ".number_format($Diferencia, 2, ',', '.').")");
$SueldoNormal = strtoupper(convertir_a_letras($field_sueldo['SueldoNormal'], "moneda")." (Bs. ".number_format($field_sueldo['SueldoNormal'], 2, ',', '.').")");
$SueldoIntegral = strtoupper(convertir_a_letras($field_sueldo['SueldoIntegral'], "moneda")." (Bs. ".number_format($field_sueldo['SueldoIntegral'], 2, ',', '.').")");
$ActualDia = convertir_a_letras($DiaActual, "entero")." ($DiaActual)";
$ActualMes = getNombreMes("$AnioActual-$MesActual");
$Ciudad = ucwords(strtolower(getVar3("SELECT Ciudad FROM mastciudades WHERE CodCiudad = '".$_PARAMETRO['CIUDADDEFAULT']."'")));
$Estado = ucwords(strtolower(getVar3("SELECT Estado FROM mastestados WHERE CodEstado = '".$_PARAMETRO['ESTADODEFAULT']."'")));
##	
list($FirmaNombre, $FirmaCargo, $FirmaNivel) = getFirma(getPersonaUnidadEjecutora($_PARAMETRO["CATRRHH"]));
//list($FirmaNombre, $FirmaCargo, $FirmaNivel) = getFirmaxDependencia($_PARAMETRO["DEPRHPR"], 1, 1);
$Firma = getRecord("SELECT
						p.Nacionalidad,
						p.Ndocumento,
						p.CodPersona
					FROM
						mastdependencias d
						INNER JOIN mastpersonas p ON (p.CodPersona = d.CodPersona)
					WHERE d.CodDependencia = '".$_PARAMETRO["DEPRHPR"]."'");
$FirmaNdocumento = number_format($Firma['Ndocumento'], 0, '', '.');
$FirmaNombre = strtoupper($FirmaNombre);
$FirmaCargo = strtoupper($FirmaCargo);
$FirmaCargoCompleto = str_replace("(P)", "(PROVISIONAL)", $FirmaCargo);
$FirmaNivel = strtoupper($FirmaNivel);
//---------------------------------------------------

class PDF extends FPDF {
	//	Cabecera de página.
	function Header() {
		global $_PARAMETRO;
		global $Ahora;
		global $field;
		global $_POST;
		global $_GET;
		extract($_POST);
		extract($_GET);
		##
		$Logo = getValorCampo("mastorganismos", "CodOrganismo", "Logo", $field['CodOrganismo']);
		$NomOrganismo = getValorCampo("mastorganismos", "CodOrganismo", "Organismo", $field['CodOrganismo']);
		$DocFiscal = getValorCampo("mastorganismos", "CodOrganismo", "DocFiscal", $field['CodOrganismo']);
		$NomDependencia = getValorCampo("mastdependencias", "CodDependencia", "Dependencia", $_PARAMETRO["DEPRHPR"]);
		##
		$this->SetFillColor(255, 255, 255);
		$this->SetDrawColor(0, 0, 0);
		$this->SetTextColor(50, 50, 50);
		$this->Image($_PARAMETRO["PATHLOGO"].$Logo, 20, 12, 25, 23);
		$this->Image($_PARAMETRO["PATHLOGO"].'LOGOSNCF.jpg', 150, 0, 60, 52);

		$this->SetFont('Arial', 'B', 12);
		$this->SetXY(50, 15); $this->Cell(108, 5, utf8_decode('REPÚBLICA BOLIVARIANA DE VENEZUELA'), 0, 1, 'C', 1);
		$this->SetXY(50, 22); $this->Cell(108, 5, utf8_decode($NomOrganismo), 0, 0, 'C', 1);
		$this->SetXY(50, 27); $this->Cell(108, 5, utf8_decode('RIF. '.$DocFiscal), 0, 0, 'C', 1);
		$this->SetXY(50, 32); $this->Cell(108, 5, utf8_decode($NomDependencia), 0, 0, 'C', 1);
		$this->SetTextColor(0, 0, 0);
		$this->SetFont('Arial', 'B', 16);
		$this->SetY(55); $this->Cell(178, 5, utf8_decode('CONSTANCIA'), 0, 1, 'C');
		$this->Ln(15);
	}
	
	//	Pie de página.
	function Footer() {
		global $_PARAMETRO;
		global $Ahora;
		global $field;
		global $FirmaNombre;
		global $FirmaCargo;
		global $FirmaNivel;
		global $FirmaEstado;
		global $_POST;
		global $_GET;
		extract($_POST);
		extract($_GET);
		##
		$pie4 = "ALCALDIA DEL MUNICIPIO BOLIVARIANO ANGOSTURA CIUDAD PIAR - ESTADO BOLIVAR";
		$pie5 = "AV. MACK C. LAKE, CAMPO B, N° 1001 - 1002 APDO POSTAL 8003, TELF: (0285) 8891311 (FAX) 8891357";
		$pie6 = "alcaldia_angostura1986@hotmail.com                                               ";
		$pie7 = "                                                          Twitter: @AMBAngostura";
		##	
		$this->SetTextColor(50,50,50);
		$this->SetFont('Arial','B',8);
		$this->SetY(252); $this->Cell(178, 5, utf8_decode($pie4), 0, 1, 'C');
		$this->SetFont('Arial','',8);
		$this->SetY(256); $this->Cell(178, 5, utf8_decode($pie5), 0, 1, 'C');
		$this->SetTextColor(0,0,255);
		$this->SetY(260); $this->Cell(178, 5, utf8_decode($pie6), 0, 0, 'C');
		$this->SetTextColor(50,50,50);
		$this->SetY(260); $this->Cell(178, 5, utf8_decode($pie7), 0, 0, 'C');
		$this->SetTextColor(50,50,50);
	}
}
//---------------------------------------------------
//	Creación del objeto de la clase heredada.
$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->SetMargins(20, 10, 30);
$pdf->SetAutoPageBreak(1, 2);
$pdf->AddPage();
//---------------------------------------------------

//---------------------------------------------------
/*
if ($FlagSueldo == "S")
	$parrafo1 = "Quien suscribe, $FirmaNivel $FirmaNombre, titular de la cédula de identidad $Firma[Nacionalidad]-$FirmaNdocumento, en mi condición de $FirmaCargoCompleto, hago constar que $funcionario $NomCompleto, titular de la cédula de identidad número: $field[Nacionalidad]-$Ndocumento, labora en esta Institución desde la fecha $Fingreso, y actualmente ocupa el cargo de $DescripCargo, devengando una remuneración salarial básica mensual de $SueldoBasico, más primas por la cantidad de $Primas; totalizando una remuneración normal de $SueldoNormal.";
else if ($FlagSueldoIntegral == "S")
	$parrafo1 = "Quien suscribe, $FirmaNivel $FirmaNombre, titular de la cédula de identidad $Firma[Nacionalidad]-$FirmaNdocumento, en mi condición de $FirmaCargoCompleto, hago constar que $funcionario $NomCompleto, titular de la cédula de identidad número: $field[Nacionalidad]-$Ndocumento, labora en esta Institución desde la fecha $Fingreso, y actualmente ocupa el cargo de $DescripCargo, devengando una remuneración salarial integral mensual de $SueldoIntegral.";
else
	$parrafo1 = "Quien suscribe, $FirmaNivel $FirmaNombre, titular de la cédula de identidad $Firma[Nacionalidad]-$FirmaNdocumento, en mi condición de $FirmaCargoCompleto, hago constar que $funcionario $NomCompleto, titular de la cédula de identidad número: $field[Nacionalidad]-$Ndocumento, labora en esta Institución desde la fecha $Fingreso, y actualmente ocupa el cargo de $DescripCargo.";
*/

if ($FlagSueldo == "S")
	$parrafo1 = "Quien suscribe, $FirmaNivel $FirmaNombre, titular de la cédula de identidad $Firma[Nacionalidad]-$FirmaNdocumento, en mi condición de $FirmaCargoCompleto, hago constar que $funcionario $NomCompleto, titular de la cédula de identidad número: $field[Nacionalidad]-$Ndocumento, labora en esta Institución desde la fecha $Fingreso, y actualmente ocupa el cargo de $DescripCargo, devengando una remuneración salarial básica mensual de $SueldoBasico.";
else if ($FlagSueldoIntegral == "S")
	$parrafo1 = "Quien suscribe, $FirmaNivel $FirmaNombre, titular de la cédula de identidad $Firma[Nacionalidad]-$FirmaNdocumento, en mi condición de $FirmaCargoCompleto, hago constar que $funcionario $NomCompleto, titular de la cédula de identidad número: $field[Nacionalidad]-$Ndocumento, labora en esta Institución desde la fecha $Fingreso, y actualmente ocupa el cargo de $DescripCargo, devengando una remuneración salarial integral mensual de $SueldoIntegral.";
else
	$parrafo1 = "Quien suscribe, $FirmaNivel $FirmaNombre, titular de la cédula de identidad $Firma[Nacionalidad]-$FirmaNdocumento, en mi condición de $FirmaCargoCompleto, hago constar que $funcionario $NomCompleto, titular de la cédula de identidad número: $field[Nacionalidad]-$Ndocumento, labora en esta Institución desde la fecha $Fingreso, y actualmente ocupa el cargo de $DescripCargo.";

$parrafo2 = "Constancia que se expide a petición de la parte interesada. En $Ciudad, Estado $Estado, a los $ActualDia día(s) del mes de $ActualMes de $AnioActual.";
$parrafo3 = "Válida por tres meses.";
##
$pdf->SetFont('Arial', '', 12);
$pdf->MultiCell(178, 6, utf8_decode($parrafo1), 0, 'J');
$pdf->Ln(5);
$pdf->MultiCell(178, 6, utf8_decode($parrafo2), 0, 'J');
$pdf->Ln(5);
$pdf->MultiCell(178, 6, utf8_decode($parrafo3), 0, 'J');
##	
$pdf->Ln(20);
/*$pie1 = "Según Resolución Nº. 01-00-129  de fecha 12-06-2012,";
$pie2 = "Emanada del Despacho de la Contralora General de la República,";
$pie3 = "Publicada en G. O. Nº  39.943  de fecha 13-06-2012";*/
$pie1 = "";
$pie2 = "";
$pie3 = "";
##	
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(178, 5, utf8_decode($FirmaNivel.' '.$FirmaNombre), 0, 1, 'C');
$pdf->Cell(178, 5, utf8_decode($FirmaCargo), 0, 1, 'C');
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(178, 5, utf8_decode($pie1), 0, 1, 'C');
$pdf->Cell(178, 5, utf8_decode($pie2), 0, 1, 'C');
$pdf->Cell(178, 5, utf8_decode($pie3), 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(178, 5, utf8_decode($_PARAMETRO['INICONSTANCIA']), 0, 1, 'L');
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  