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
if ($fFingresoD != "") $filtro.=" AND (e.Fingreso >= '".formatFechaAMD($fFingresoD)."')";
if ($fFingresoH != "") $filtro.=" AND (e.Fingreso <= '".formatFechaAMD($fFingresoH)."')";
if ($fCodTipoNom != "") $filtro .= " AND (e.CodTipoNom = '".$fCodTipoNom."')";
if ($fFegresoD != "") $filtro.=" AND (e.Fegreso >= '".formatFechaAMD($fFegresoD)."')";
if ($fFegresoH != "") $filtro.=" AND (e.Fegreso <= '".formatFechaAMD($fFegresoH)."')";
if ($fBuscar != "") {
	$filtro .= " AND (p.Ndocumento LIKE '%".$fBuscar."%' OR
					  p.NomCompleto LIKE '%".$fBuscar."%' OR
					  e.CodEmpleado LIKE '%".$fBuscar."%' OR
					  pt.DescripCargo LIKE '%".$fBuscar."%' OR
					  tn.Nomina LIKE '%".$fBuscar."%' OR
					  tt.TipoTrabajador LIKE '%".$fBuscar."%' OR
					  d.Dependencia LIKE '%".$fBuscar."%' OR
					  pn.Perfil LIKE '%".$fBuscar."%' OR
					  tp.TipoPago LIKE '%".$fBuscar."%' OR
					  cc.Descripcion LIKE '%".$fBuscar."%')";
}
if ($fCodTipoTrabajador != "") $filtro .= " AND (e.CodTipoTrabajador = '".$fCodTipoTrabajador."')";
if ($fCodTipoPago != "") $filtro .= " AND (e.CodTipoPago = '".$fCodTipoPago."')";
if ($fCodPerfil != "") $filtro .= " AND (e.CodPerfil = '".$fCodPerfil."')";
##
$fNomDependencia = getVar3("SELECT Dependencia FROM mastdependencias WHERE CodDependencia = '".$fCodDependencia."'");
$fNomCentroCosto = getVar3("SELECT Descripcion FROM ac_mastcentrocosto WHERE CodCentroCosto = '".$fCodCentroCosto."'");
$fNomTipoNom = getVar3("SELECT Nomina FROM tiponomina WHERE CodTipoNom = '".$fCodTipoNom."'");
$fNomTipoTrabajador = getVar3("SELECT TipoTrabajador FROM rh_tipotrabajador WHERE CodTipoTrabajador = '".$fCodTipoTrabajador."'");
$fNomPerfil = getVar3("SELECT Perfil FROM tipoperfilnom WHERE CodPerfil = '".$fCodPerfil."'");
##
$MostrarAgrupador = true;
if ($fAgrupador == "CodDependencia") {
	if ($fCodDependencia != "") $MostrarAgrupador = false;
	else $OrdenAgrupador = "$fAgrupador, ";
}
elseif ($fAgrupador == "CodTipoNom") {
	if ($fCodTipoNom != "") $MostrarAgrupador = false;
	else $OrdenAgrupador = "$fAgrupador, ";
}
##
$Width = 3;
$SetWidths[] = 21; $SetAligns[] = 'R'; $Row1[] = 'DOCUMENTO'; $Width += 21;
$SetWidths[] = 75; $SetAligns[] = 'L'; $Row1[] = 'NOMBRE COMPLETO'; $Width += 75;
//$SetWidths[] = 20; $SetAligns[] = 'C'; $Row1[] = 'CODIGO'; $Width += 20;
if ($FlagCodEmpleado == "S") { $SetWidths[] = 20; $SetAligns[] = 'C'; $Row1[] = 'CODIGO'; $Width += 20; }
if ($FlagFingreso == "S") { $SetWidths[] = 20; $SetAligns[] = 'C'; $Row1[] = 'FECHA INGRESO'; $Width += 20; }
if ($FlagGrado == "S") { $SetWidths[] = 20; $SetAligns[] = 'C'; $Row1[] = 'GRADO'; $Width += 20; }
if ($FlagPaso == "S") { $SetWidths[] = 20; $SetAligns[] = 'C'; $Row1[] = 'PASO'; $Width += 20; }
if ($FlagCodCargo == "S") { $SetWidths[] = 100; $SetAligns[] = 'L'; $Row1[] = 'CARGO'; $Width += 100; }
if ($FlagCodDependencia == "S") { $SetWidths[] = 100; $SetAligns[] = 'L'; $Row1[] = 'DEPENDENCIA'; $Width += 100; }
if ($FlagFegreso == "S") { $SetWidths[] = 20; $SetAligns[] = 'C'; $Row1[] = 'FECHA EGRESO'; $Width += 20; }
if ($FlagCodTipoNom == "S") { $SetWidths[] = 30; $SetAligns[] = 'L'; $Row1[] = 'NOMINA'; $Width += 30; }
if ($FlagCodTipoTrabajador == "S") { $SetWidths[] = 30; $SetAligns[] = 'L'; $Row1[] = 'TIPO DE TRABAJADOR'; $Width += 30; }
if ($FlagCodPerfil== "S") { $SetWidths[] = 50; $SetAligns[] = 'L'; $Row1[] = 'PERFIL'; $Width += 50; }
if ($FlagCodTipoPago == "S") { $SetWidths[] = 30; $SetAligns[] = 'L'; $Row1[] = 'TIPO DE PAGO'; $Width += 30; }
if ($FlagCodCentroCosto == "S") { $SetWidths[] = 75; $SetAligns[] = 'L'; $Row1[] = 'CENTRO DE COSTO'; $Width += 75; }
if ($FlagSueldoBasico == "S") { $SetWidths[] = 25; $SetAligns[] = 'R'; $Row1[] = 'SUELDO BASICO'; $Width += 25; }
if ($FlagSueldoAnual == "S") { $SetWidths[] = 25; $SetAligns[] = 'R'; $Row1[] = 'SUELDO ANUAL'; $Width += 25; }
if ($FlagSueldoNormal == "S") { $SetWidths[] = 25; $SetAligns[] = 'R'; $Row1[] = 'SUELDO NORMAL'; $Width += 25; }
if ($FlagCuentaBancaria == "S") { $SetWidths[] = 40; $SetAligns[] = 'C'; $Row1[] = 'CTA. BANCARIA'; $Width += 40; }
if ($Width < 175) { $SetWidths[1] = 175 - ($Width - $SetWidths[1]); $Width = 175; }
//$SetWidths[1] = 75 + (279.4 - $Width);
//---------------------------------------------------

class PDF extends FPDF {
	//	Cabecera de página.
	function Header() {
		global $_PARAMETRO;
		global $Ahora;
		global $field;
		global $SetWidths;
		global $SetAligns;
		global $Row1;
		global $Width;
		global $fNomDependencia;
		global $fNomCentroCosto;
		global $fNomTipoNom;
		global $fNomTipoTrabajador;
		global $fNomPerfil;
		global $_POST;
		global $_POST;
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
		$this->SetXY(10, 5); $this->Cell(100, 5, utf8_decode($NomOrganismo), 0, 1, 'L');
		$this->SetXY(10, 10); $this->Cell(100, 5, utf8_decode($NomDependencia), 0, 0, 'L');
		$this->SetFont('Arial', '', 8);
		$this->SetXY($Width-35, 5); $this->Cell(10, 5, utf8_decode('Fecha: '), 0, 0, 'L');
		$this->Cell(30, 5, formatFechaDMA(substr($Ahora, 0, 10)), 0, 1, 'L');
		$this->SetXY($Width-35, 10); $this->Cell(10, 5, utf8_decode('Página: '), 0, 0, 'L');
		$this->Cell(30, 5, $this->PageNo().' de {nb}', 0, 1, 'L');
		$this->SetFont('Arial', 'B', 10);
		$this->SetY(20); $this->Cell($Width-3, 5, utf8_decode('LISTADO DE EMPLEADOS'), 0, 1, 'C', 0);
		$this->Ln(5);
		##
		if ($fCodDependencia) {
			$this->SetFont('Arial', 'B', 8);
			$this->SetFillColor(220, 220, 220);
			$this->Cell(25, 5, 'DEPENDENCIA:', 0, 0, 'L', 1);
			$this->SetFont('Arial', '', 8);
			$this->Cell($Width-25-3, 5, utf8_decode($fNomDependencia), 0, 1, 'L');
			$this->Ln(1);
		}
		if ($fCodCentroCosto) {
			$this->SetFont('Arial', 'B', 8);
			$this->SetFillColor(220, 220, 220);
			$this->Cell(25, 5, 'CENTRO DE COSTO:', 0, 0, 'L', 1);
			$this->SetFont('Arial', '', 8);
			$this->Cell($Width-25-3, 5, utf8_decode($fNomCentroCosto), 0, 1, 'L');
			$this->Ln(1);
		}
		if ($fCodTipoNom) {
			$this->SetFont('Arial', 'B', 8);
			$this->SetFillColor(220, 220, 220);
			$this->Cell(25, 5, 'NOMINA:', 0, 0, 'L', 1);
			$this->SetFont('Arial', '', 8);
			$this->Cell($Width-25-3, 5, utf8_decode($fNomTipoNom), 0, 0, 'L');
			$this->Ln(1);
		}
		if ($fCodTipoTrabajador) {
			$this->SetFont('Arial', 'B', 8);
			$this->SetFillColor(220, 220, 220);
			$this->Cell(25, 5, 'TIPO DE TRABAJADOR:', 0, 0, 'L', 1);
			$this->SetFont('Arial', '', 8);
			$this->Cell($Width-25-3, 5, utf8_decode($fNomTipoTrabajador), 0, 0, 'L');
			$this->Ln(1);
		}
		if ($fCodPerfil) {
			$this->SetFont('Arial', 'B', 8);
			$this->SetFillColor(220, 220, 220);
			$this->Cell(25, 5, 'PERFIL DE NOMINA:', 0, 0, 'L', 1);
			$this->SetFont('Arial', '', 8);
			$this->Cell($Width-25-3, 5, utf8_decode($fNomPerfil), 0, 0, 'L');
			$this->Ln(1);
		}
		$this->Ln(5);
		##
		$this->SetFillColor(255, 255, 255);
		$this->SetDrawColor(0, 0, 0);
		$this->SetTextColor(0, 0, 0);
		$this->SetFont('Arial', 'B', 8);
		$this->SetWidths($SetWidths);
		$this->SetAligns($SetAligns);
		$this->Row($Row1);
		##
		$this->SetDrawColor(0, 0, 0);
		//$this->Line(1, $this->GetY(), $Width-1, $this->GetY());
		//$this->Ln(2);
	}
	
	//	Pie de página.
	function Footer() {}
}
//---------------------------------------------------

//---------------------------------------------------
//	Creación del objeto de la clase heredada.
//$pdf = new PDF('L', 'mm', array(215.9,279.4));
$pdf = new PDF('L', 'mm', array(215.9,$Width));
$pdf->AliasNbPages();
$pdf->SetMargins(1, 1, 1);
$pdf->SetAutoPageBreak(5, 5);
$pdf->AddPage();
//---------------------------------------------------
$pdf->SetFillColor(255, 255, 255);
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetTextColor(0, 0, 0);
//	consulto datos
$i = 0;
$Nro = 0;
$sql = "SELECT
			p.Ndocumento,
			p.NomCompleto,			
			e.CodEmpleado,
			e.Fingreso,
			e.Fegreso,
			e.CodTipoNom,
			e.CodDependencia,
			e.Paso,
			pt.Grado,
			d.Dependencia,
			pt.DescripCargo,
			ns.SueldoPromedio AS NivelSalarial,
			tn.Nomina,
			tt.TipoTrabajador,
			pn.Perfil,
			tp.TipoPago,
			cc.Descripcion AS NomCentroCosto,
			(SELECT s2.SueldoNormal FROM rh_sueldos s2 WHERE s2.CodPersona = p.CodPersona ORDER BY Periodo DESC LIMIT 0, 1) AS SueldoNormal,
			(SELECT Ncuenta FROM bancopersona bp2 WHERE bp2.CodPersona = p.CodPersona AND bp2.FlagPrincipal = 'S') AS CuentaBancaria
		FROM
			mastpersonas p
			INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
			INNER JOIN mastdependencias d ON (d.CodDependencia = e.CodDependencia)
			LEFT JOIN rh_puestos pt ON (pt.CodCargo = e.CodCargo)
			LEFT JOIN rh_nivelsalarial ns ON (ns.CategoriaCargo = pt.CategoriaCargo AND ns.Grado = pt.Grado AND ns.Paso = e.Paso)
			LEFT JOIN tiponomina tn ON (tn.CodTipoNom = e.CodTipoNom)
			LEFT JOIN rh_tipotrabajador tt ON (tt.CodTipotrabajador = e.CodTipoTrabajador)
			LEFT JOIN tipoperfilnom pn ON (pn.CodPerfil = e.CodPerfil)
			LEFT JOIN masttipopago tp ON (tp.CodTipoPago = e.CodTipoPago)
			LEFT JOIN ac_mastcentrocosto cc ON (cc.CodCentroCosto = e.CodCentroCosto)
		WHERE 1 $filtro
		ORDER BY $OrdenAgrupador LENGTH(Ndocumento), Ndocumento";
$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
while($field = mysql_fetch_array($query)) {
	++$i;
	##
	if ($fAgrupador == "CodDependencia") {
		$Agrupador = $field['CodDependencia'];
		$NomAgrupador = $field['Dependencia'];
	}
	elseif ($fAgrupador == "CodTipoNom") {
		$Agrupador = $field['CodTipoNom'];
		$NomAgrupador = $field['Nomina'];
	}
	if ($Grupo != $Agrupador && $MostrarAgrupador) {
		$Grupo = $Agrupador;
		$pdf->SetFont('Arial', 'BU', 8);
		$pdf->Cell($Width-3, 5, utf8_decode($NomAgrupador), 1, 1, 'L');
	}
	##
	$Row2[] = number_format($field['Ndocumento'], 0, '', '.');
	$Row2[] = utf8_decode($field['NomCompleto']);
	//$Row2[] = $field['CodEmpleado'];
	if ($FlagCodEmpleado == "S") $Row2[] = utf8_decode($field['CodEmpleado']);
	if ($FlagFingreso == "S") $Row2[] = formatFechaDMA($field['Fingreso']);
	if ($FlagGrado == "S") $Row2[] = utf8_decode($field['Grado']);
	if ($FlagPaso == "S") $Row2[] = utf8_decode($field['Paso']);
	if ($FlagCodCargo == "S") $Row2[] = utf8_decode($field['DescripCargo']);
	if ($FlagCodDependencia == "S") $Row2[] = utf8_decode($field['Dependencia']);
	if ($FlagFegreso == "S") $Row2[] = formatFechaDMA($field['Fegreso']);
	if ($FlagCodTipoNom == "S") $Row2[] = utf8_decode($field['Nomina']);
	if ($FlagCodTipoTrabajador == "S") $Row2[] = utf8_decode($field['TipoTrabajador']);
	if ($FlagCodPerfil== "S") $Row2[] = utf8_decode($field['Perfil']);
	if ($FlagCodTipoPago == "S") $Row2[] = utf8_decode($field['TipoPago']);
	if ($FlagCodCentroCosto == "S") $Row2[] = utf8_decode($field['NomCentroCosto']);
	if ($FlagSueldoBasico == "S") $Row2[] = number_format($field['NivelSalarial'],2,',','.');
	if ($FlagSueldoAnual == "S") $Row2[] = number_format(($field['NivelSalarial']*12),2,',','.');
	if ($FlagSueldoNormal == "S") $Row2[] = number_format($field['SueldoNormal'],2,',','.');
	if ($FlagCuentaBancaria == "S") $Row2[] = utf8_decode($field['CuentaBancaria']);
	##
	//if ($i % 2 == 0) { $pdf->SetFillColor(255, 255, 255); $pdf->SetDrawColor(255, 255, 255); }
	//else { $pdf->SetFillColor(240, 240, 240); $pdf->SetDrawColor(240, 240, 240); }
	$pdf->SetFillColor(255, 255, 255); 
	$pdf->SetDrawColor(0, 0, 0);

	$pdf->SetFont('Arial', '', 8);
	$pdf->Row($Row2);
	//$pdf->Ln(1);
	unset($Row2);
}
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  