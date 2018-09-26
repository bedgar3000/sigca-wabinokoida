<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$LF = 0x0A;
$CR = 0x0D;
$nl = sprintf("%c%c",$CR,$LF);
//---------------------------------------------------
$CodPersona = $sel_registros;
//	consulto datos
$sql = "SELECT
			e.CodPersona,
			e.CodEmpleado,
			e.Estado,
			e.Fingreso,
			e.CodDependencia,
			e.CodPerfil,
			e.CodCargo,
			p.Apellido1,
			p.Apellido2,
			p.Nombres,
			p.Ndocumento,
			o.Organismo,
			d.Dependencia,
			d.CodPersona AS CodJefeInmediato,
			d.CodCargo AS CodCargoJefeInmediato,
			pt.DescripCargo,
			md.Descripcion AS NomCategoriaCargo,
			p2.Apellido1 AS Apellido1Jefe,
			p2.Apellido2 AS Apellido2Jefe,
			p2.Nombres AS NombresJefe,
			pp.ProcesadoPor,
			pp.AprobadoPor,
			pp.FechaProceso
		FROM
			pr_tiponominaempleado tne
			INNER JOIN mastempleado e ON (e.CodPersona = tne.CodPersona)
			INNER JOIN mastpersonas p ON (p.CodPersona = e.CodPersona)
			INNER JOIN mastorganismos o ON (o.Codorganismo = e.Codorganismo)
			INNER JOIN mastdependencias d ON (d.CodDependencia = e.CodDependencia)
			INNER JOIN rh_puestos pt ON (pt.CodCargo = e.CodCargo)
			LEFT JOIN mastmiscelaneosdet md ON (md.CodDetalle = pt.CategoriaCargo AND
												md.CodMaestro = 'CATCARGO')
			LEFT JOIN mastpersonas p2 ON (p2.CodPersona = d.CodPersona)
			LEFT JOIN pr_procesoperiodo pp ON (pp.CodOrganismo = tne.CodOrganismo AND
											   pp.CodTipoNom = tne.CodTipoNom AND
											   pp.Periodo = tne.Periodo AND
											   pp.CodTipoProceso = tne.CodTipoProceso)
		WHERE
			tne.CodOrganismo = '".$fCodOrganismo."' AND
			tne.CodTipoNom = '".$fCodTipoNom."' AND
			tne.Periodo = '".$fPeriodo."' AND
			tne.CodTipoProceso = '".$fCodTipoProceso."' AND
			tne.CodPersona = '".$CodPersona."'";
$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
//	consulto el monto
$sql = "SELECT
			SUM(tnec.Monto) AS Monto,
			Cantidad
		FROM
			pr_tiponominaempleadoconcepto tnec
			INNER JOIN pr_concepto c ON (tnec.CodConcepto = c.CodConcepto AND c.Tipo = 'I')
		WHERE
			tnec.CodOrganismo = '".$fCodOrganismo."' AND
			tnec.CodTipoNom = '".$fCodTipoNom."' AND
			tnec.Periodo = '".$fPeriodo."' AND
			tnec.CodTipoProceso = '".$fCodTipoProceso."' AND
			tnec.CodPersona = '".$CodPersona."'";
$query_ingresos = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
if (mysql_num_rows($query_ingresos) != 0) $field_ingresos = mysql_fetch_array($query_ingresos);
//	consulto el monto
$sql = "SELECT SUM(tnec.Monto) AS Monto
		FROM
			pr_tiponominaempleadoconcepto tnec
			INNER JOIN pr_concepto c ON (tnec.CodConcepto = c.CodConcepto AND c.Tipo = 'D')
		WHERE
			tnec.CodOrganismo = '".$fCodOrganismo."' AND
			tnec.CodTipoNom = '".$fCodTipoNom."' AND
			tnec.Periodo = '".$fPeriodo."' AND
			tnec.CodTipoProceso = '".$fCodTipoProceso."' AND
			tnec.CodPersona = '".$CodPersona."'";
$query_egresos = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
if (mysql_num_rows($query_egresos) != 0) $field_egresos = mysql_fetch_array($query_egresos);
//	consulto el monto
$sql = "SELECT
			TotalIngresos,
			(TotalIngresos / 30) AS Diario
		FROM pr_tiponominaempleado
		WHERE
			CodOrganismo = '".$fCodOrganismo."' AND
			CodTipoNom = '".$fCodTipoNom."' AND
			Periodo < '".$fPeriodo."' AND
			CodTipoProceso = 'FIN' AND
			CodPersona = '".$CodPersona."'
		ORDER BY Periodo DESC
		LIMIT 0, 1";
$query_sueldo = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
if (mysql_num_rows($query_sueldo) != 0) $field_sueldo = mysql_fetch_array($query_sueldo);
//	consulto los retroactivos
list($xa, $xm) = explode('-', $fPeriodo);
$nm = intval($xm) - 1;
if ($nm == 0) { --$xa; $nm = 12; }
if ($nm < 10) $nm = "0".$nm;
$PeriodoNomina = $xa."-".$nm;
/*$sql = "SELECT tne.TotalIngresos
		FROM
			pr_tiponominaempleado tne
			INNER JOIN pr_procesoperiodo pp ON (pp.CodOrganismo = tne.CodOrganismo AND
												pp.CodTipoNom = tne.CodTipoNom AND
												pp.Periodo = tne.Periodo AND
												pp.CodTipoProceso = tne.CodTipoProceso)
			INNER JOIN pr_tipoproceso tp ON (tp.CodTipoProceso = tp.CodTipoProceso)
		WHERE
			tne.CodOrganismo = '".$fCodOrganismo."' AND
			tne.CodTipoNom = '".$fCodTipoNom."' AND
			tne.CodTipoProceso = 'RTA' AND
			tne.CodPersona = '".$CodPersona."' AND
			pp.PeriodoNomina = '".$PeriodoNomina."' AND
			tp.FlagRetroactivo = 'S'";*/
$sql = "SELECT
			tne.TotalIngresos,
			(tne.TotalIngresos / 30) AS Diario
		FROM pr_tiponominaempleado tne
		WHERE
			tne.CodOrganismo = '$fCodOrganismo' AND
			tne.CodTipoNom = '$fCodTipoNom' AND
			tne.Periodo = (SELECT Periodo 
						   FROM pr_procesoperiodo
						   WHERE CodOrganismo = tne.CodOrganismo AND CodTipoNom = tne.CodTipoNom AND CodTipoProceso = tne.CodTipoProceso AND Periodo < '$fPeriodo' ORDER BY Periodo DESC LIMIT 0, 1) AND
			tne.CodTipoProceso = 'RTA' AND
			tne.CodPersona = '$CodPersona'
		ORDER BY Periodo DESC
		LIMIT 0, 1";
$query_retro = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
//if (mysql_num_rows($query_retro) != 0) $field_retro = mysql_fetch_array($query_retro);
##	
$sql = "SELECT
			tne.TotalIngresos,
			(tne.TotalIngresos / 30) AS Diario
		FROM pr_tiponominaempleado tne
		WHERE
			tne.CodOrganismo = '$fCodOrganismo' AND
			tne.CodTipoNom = '$fCodTipoNom' AND
			tne.Periodo < '$fPeriodo' AND
			tne.CodTipoProceso = 'DEM' AND
			tne.CodPersona = '$CodPersona'
		ORDER BY Periodo DESC
		LIMIT 0, 1";
$query_dem = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
if (mysql_num_rows($query_dem) != 0) $field_dem = mysql_fetch_array($query_dem);
//---------------------------------------------------
//	obtengo las firmas
list($_CREADO['Nombre'], $_CREADO['Cargo'], $_CREADO['Nivel']) = getFirma($field['ProcesadoPor']);
list($_REVISADO['Nombre'], $_REVISADO['Cargo'], $_REVISADO['Nivel']) = getFirmaxDependencia($_PARAMETRO['DEPRHPR']);
list($_CONFORMADO['Nombre'], $_CONFORMADO['Cargo'], $_CONFORMADO['Nivel']) = getFirmaxDependencia($_PARAMETRO['DEPCON']);
list($_PERSONA['Nombre'], $_PERSONA['Cargo'], $_PERSONA['Nivel']) = getFirma($CodPersona);
$CodAprueba = getVar("mastdependencias", "CodPersona", "CodDependencia", $_PARAMETRO['DEPMAXORG']);
/*$CodAprueba = getVar("mastdependencias", "CodPersona", "CodDependencia", $_PARAMETRO['DEPMAXORG']);
list($_PERSONA['Nombre'], $_PERSONA['Cargo'], $_PERSONA['Nivel']) = getFirma($CodPersona);
list($_CREADO['Nombre'], $_CREADO['Cargo'], $_CREADO['Nivel']) = getFirma($field['ProcesadoPor']);
list($_REVISADO['Nombre'], $_REVISADO['Cargo'], $_REVISADO['Nivel']) = getFirma($_PARAMETRO['FIRMARHPR']);
list($_CONFORMADO['Nombre'], $_CONFORMADO['Cargo'], $_CONFORMADO['Nivel']) = getFirma($field['AprobadoPor']);
list($_APROBADO['Nombre'], $_APROBADO['Cargo'], $_APROBADO['Nivel']) = getFirma($CodAprueba);*/
//---------------------------------------------------
//	observaciones
$ObsGeneral = "$_PARAMETRO[COMENTVACGEN]";
$ObsAprueba = "$_PARAMETRO[COMENTVACCON]";
if ($CodPersona != $CodAprueba) $Observaciones = $ObsGeneral; else $Observaciones = $ObsAprueba;
//---------------------------------------------------
//	jefe inmediato
if ($field['CodJefeInmediato'] == $CodAprueba) { $NomCompletoJefe = ""; $CargoJefe = $_APROBADO['Cargo']; }
elseif ($field['CodJefeInmediato'] == $CodPersona) {
	$sql = "SELECT
				p.CodPersona,
				p.Nombres,
				p.Apellido1,
				p.Apellido2,
				e.CodDependencia,
				pt.DescripCargo
			FROM
				rh_cargoreporta cr
				INNER JOIN mastempleado e ON (e.CodCargo = cr.CargoReporta)
				INNER JOIN mastpersonas p ON (p.CodPersona = e.CodPersona)
				INNER JOIN rh_puestos pt ON (pt.CodCargo = cr.CargoReporta)
			WHERE
				cr.CodCargo = '".$field['CodCargoJefeInmediato']."' AND
				e.Estado = 'A'";
	$query_jefe = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query_jefe) != 0) {
		$field_jefe = mysql_fetch_array($query_jefe);
		/*list($Nombre) = split("[ ]", $field_jefe['Nombres']);
		if ($field_jefe['Apellido1'] != "") $Apellido = $field_jefe['Apellido1']; else $Apellido = $field_jefe['Apellido2'];
		$NomCompletoJefe = "$Apellido $Nombre";
		list($NombreJefe, $CargoJefe, $NivelJefe) = getFirma($field_jefe['CodPersona']);*/
		list($NombreJefe, $CargoJefe, $NivelJefe) = getFirmaxDependencia($field_jefe['CodDependencia']);
		$NomCompletoJefe = $NombreJefe;
	} else {
		$sql = "SELECT
					p.CodPersona,
					p.Nombres,
					p.Apellido1,
					p.Apellido2
				FROM
					mastdependencias d
					INNER JOIN mastempleado e ON (e.CodPersona = d.CodPersona)
					INNER JOIN mastpersonas p ON (p.CodPersona = e.CodPersona)
				WHERE d.CodDependencia = '".$_PARAMETRO['DEPMAXORG']."'";
		$field_jefe = getRecord($sql);
		list($Nombre) = split("[ ]", $field_jefe['Nombres']);
		if ($field_jefe['Apellido1'] != "") $Apellido = $field_jefe['Apellido1']; else $Apellido = $field_jefe['Apellido2'];
		$NomCompletoJefe = "$Apellido $Nombre";
		list($NombreJefe, $CargoJefe, $NivelJefe) = getFirmaxDependencia($_PARAMETRO['DEPMAXORG']);
	}
}
else {
	/*
	list($NombreJefe) = split("[ ]", $field['NombresJefe']);
	if ($field['Apellido1Jefe'] != "") $ApellidoJefe = $field['Apellido1Jefe']; else $ApellidoJefe = $field['Apellido2Jefe'];
	$NomCompletoJefe = "$ApellidoJefe $NombreJefe";
	
	$sql = "SELECT * FROM mastempleado WHERE CodPersona = '".$field['CodJefeInmediato']."'";
	$field_jefe = getRecord($sql);
	
	if ($field_jefe['CodCargoTemp'] == $field['CodCargoJefeInmediato'] || $field_jefe['CodCargo'] == $field['CodCargoJefeInmediato']) {
		list($NombreJefe, $CargoJefe, $NivelJefe) = getFirma($field['CodJefeInmediato']);
	} else {
		list($NombreJefe, $CargoJefe, $NivelJefe) = getFirma($field['CodJefeInmediato'], NULL, NULL, $field['CodCargoJefeInmediato']);
	}*/
	$sql = "SELECT
				d.CodPersona,
				d.CodCargo,
				pu.DescripCargo
			FROM
				mastdependencias d
				INNER JOIN rh_puestos pu ON (pu.CodCargo = d.CodCargo)
			WHERE d.CodDependencia = '".$field['CodDependencia']."'";
	$field_dependencia = getRecord($sql);
	if ($field['CodJefeInmediato'] == $field_dependencia['CodPersona']) 
		list($NombreJefe, $CargoJefe, $NivelJefe) = getFirmaxDependencia($field['CodDependencia']);
	else 
		list($NombreJefe, $CargoJefe, $NivelJefe) = getFirma($field['CodJefeInmediato']);

	$NomCompletoJefe = $NombreJefe;
}
//---------------------------------------------------

class PDF extends FPDF {
	//	Cabecera de página.
	function Header() {
		global $_PARAMETRO;
		global $Ahora;
		global $_POST;
		global $_GET;
		global $field;
		global $field_ingresos;
		global $field_egresos;
		global $field_sueldo;
		global $field_retro;
		global $field_dem;
		global $_CREADO;
		global $_REVISADO;
		global $_CONFORMADO;
		//global $_APROBADO;
		global $_PERSONA;
		global $Observaciones;
		global $CodAprueba;
		global $CodPersona;
		global $NomCompletoJefe;
		global $CargoJefe;
		global $nl;
		extract($_POST);
		extract($_GET);
		##	
		//	membrete
		##	obtengo los valores
		$Logo = getValorCampo("mastorganismos", "CodOrganismo", "Logo", $fCodOrganismo);
		$NomDependencia = getValorCampo("mastdependencias", "CodDependencia", "Dependencia", $_PARAMETRO["DEPRHPR"]);		
		##	colores
		$this->SetDrawColor(0, 0, 0);
		$this->SetTextColor(0, 0, 0);
		##	cuadros y lineas
		$this->Rect(10, 10, 195, 36, 'D');
		$this->Rect(45, 10, 0.1, 36, 'D');
		$this->Rect(170, 10, 0.1, 36, 'D');
		$this->Rect(45, 35, 160, 0.1, 'D');
		$this->Rect(170, 20, 35, 0.1, 'D');
		$this->Rect(170, 25, 35, 0.1, 'D');
		$this->Rect(170, 30, 35, 0.1, 'D');
		$this->Rect(187.5, 25, 0.1, 10, 'D');
		##	imprimo membrete
		$this->Image($_PARAMETRO["PATHLOGO"].$Logo, 18, 12, 18, 18);
		$this->SetFont('Arial', 'B', 8);
		$this->SetXY(10, 32); $this->MultiCell(35, 4, utf8_decode($field['Organismo']), 0, 'C');
		##
		$this->SetFont('Arial', 'B', 11);
		$this->SetXY(40, 18); $this->MultiCell(125, 5, utf8_decode($NomDependencia), 0, 'C');
		##
		$this->SetFont('Arial', 'B', 10);
		$this->SetXY(40, 36);
		$this->Cell(125, 5, 'FORMATO', 0, 2, 'C');
		$this->Cell(125, 5, utf8_decode('PARTICIPACIÓN DE PAGO DE VACACIONES'), 0, 0, 'C');
		##
		$this->SetFont('Arial', 'B', 8);
		$this->SetXY(170, 10); $this->Cell(35, 5, utf8_decode('CODIGO:'), 0, 1, 'C');
		$this->SetXY(170, 15); $this->Cell(35, 5, utf8_decode('FOR-DRRHH-013'), 0, 1, 'C');
		$this->SetXY(170, 20); $this->Cell(35, 5, utf8_decode('REVISIÓN:'), 0, 1, 'C');
		$this->SetXY(170, 25); $this->Cell(17.5, 5, utf8_decode('N°:'), 0, 1, 'C');
		$this->SetXY(187.5, 25); $this->Cell(17.5, 5, utf8_decode('FECHA'), 0, 1, 'C');
		$this->SetXY(170, 30); $this->Cell(17.5, 5, utf8_decode('0'), 0, 1, 'C');
		$this->SetXY(187.5, 30); $this->Cell(17.5, 5, utf8_decode('05/2008'), 0, 1, 'C');
		$this->SetXY(170, 37); $this->Cell(35, 5, utf8_decode('PAGINA'), 0, 1, 'C');
		$this->SetFont('Arial', '', 8);
		$this->SetXY(170, 41); $this->Cell(35, 5, $this->PageNo().' DE {nb}', 0, 1, 'C');
		//	cuerpo
		##	cuadro
		$this->Rect(10, 55, 195, 210, 'D');
		##	funcionario o trabajador
		list($Nombre) = split("[ ]", $field['Nombres']);
		if ($field['Apellido1'] != "") $Apellido = $field['Apellido1']; else $Apellido = $field['Apellido2'];
		$NomCompleto = "$Apellido $Nombre";
		##
		$this->SetFont('Arial', 'B', 12);
		$this->SetXY(10, 55); 
		$this->Cell(165, 10, utf8_decode('BONO VACACIONAL'), 1, 0, 'C');
		$this->SetFont('Arial', '', 10);
		$this->Cell(30, 5, utf8_decode('FECHA:'), 1, 2, 'C');
		$this->Cell(30, 5, formatFechaDMA(substr($field['FechaProceso'], 0, 10)), 1, 0, 'C');
		##
		$this->Ln();
		$this->SetFillColor(200, 200, 200);
		$this->SetFont('Arial', '', 10);
		$this->Cell(195, 5, utf8_decode('FUNCIONARIO O TRABAJADOR'), 1, 0, 'C', 1);
		##
		$this->Ln();
		$this->SetFillColor(255, 255, 255);
		$this->SetFont('Arial', 'B', 10);
		$this->Cell(30, 15, utf8_decode($field['NomCategoriaCargo']), 0, 0, 'C');
		$this->SetWidths(array(120, 45));
		$this->SetAligns(array('C', 'C'));
		$this->SetHeights(array(5, 4.5, 5));
		$this->SetFont('Arial', '', 10);
		$this->Row(array(utf8_decode('APELLIDOS Y NOMBRES'),
						 utf8_decode('CEDULA DE IDENTIDAD')));
		$this->SetX(40);
		$this->SetFont('Arial', 'B', 10);
		$this->SetHeights(array(10, 9.5, 10));
		$this->Row(array(utf8_decode($NomCompleto),
						 number_format($field['Ndocumento'], 0, '', '.')));
		##
		$this->SetFont('Arial', '', 10);
		$this->SetWidths(array(97.5, 97.5));
		$this->SetAligns(array('C', 'C'));
		$this->SetHeights(array(5, 4.5, 5));
		$this->Row(array(utf8_decode('CARGO'),
						 utf8_decode('DEPENDENCIA')));
		$this->SetFont('Arial', 'B', 10);
		$this->SetHeights(array(5, 4.5, 10));
		$this->Row(array(utf8_decode($_PERSONA['Cargo']),
						 utf8_decode($field['Dependencia'])));
		##
		$this->SetFont('Arial', '', 10);
		$this->SetWidths(array(65, 65, 65));
		$this->SetAligns(array('C', 'C', 'C'));
		$this->SetHeights(array(5, 4.5, 5));
		$this->Row(array(utf8_decode('FECHA DE INGRESO'),
						 utf8_decode('PERIODO'),
						 utf8_decode('SUELDO')));
		$this->SetFont('Arial', 'B', 10);
		$this->SetHeights(array(10, 9.5, 10));
		list($AnioPeriodo, $MesPeriodo) = split("[./-]", $fPeriodo);
		$_Periodo = ($AnioPeriodo-1)." - ".$AnioPeriodo;
		$_Sueldo = $field_sueldo['TotalIngresos'] + $field_retro['TotalIngresos'] + $field_dem['TotalIngresos'];
		##	
		if ($CodPersona == '000064' && $fPeriodo == '2016-09') $_Sueldo = 53718;
		##	
		$this->Row(array(formatFechaDMA($field['Fingreso']),
						 $_Periodo,
						 //number_format(($field_sueldo['TotalIngresos']), 2, ',', '.')
						 number_format($_Sueldo, 2, ',', '.')
						));
		##
		$this->SetFont('Arial', '', 10);
		$this->SetWidths(array(97.5, 97.5));
		$this->SetAligns(array('C', 'C'));
		$this->SetHeights(array(5, 4.5, 5));
		$this->Row(array(utf8_decode('FIRMA'),
						 utf8_decode('FECHA')));
		$this->SetFont('Arial', 'B', 10);
		$this->SetHeights(array(5, 4.5, 10));
		$this->Row(array('', ''));
		##	jefe inmediato
		##
		$this->SetFillColor(200, 200, 200);
		$this->SetFont('Arial', '', 10);
		$this->Cell(195, 5, utf8_decode('JEFE INMEDIATO'), 1, 0, 'C', 1);
		##
		$this->Ln();
		$this->SetFillColor(255, 255, 255);
		$this->SetFont('Arial', '', 10);
		$this->SetWidths(array(97.5, 97.5));
		$this->SetAligns(array('C', 'C'));
		$this->SetHeights(array(5, 4.5, 5));
		$this->Row(array(utf8_decode('APELLIDOS Y NOMBRES'),
						 utf8_decode('CARGO')));
		$this->SetFont('Arial', 'B', 10);
		$this->SetHeights(array(5, 4.5, 10));
		$this->Row(array(utf8_decode($NomCompletoJefe),
						 utf8_decode($CargoJefe)));
		##
		$this->SetFont('Arial', '', 10);
		$this->SetWidths(array(97.5, 97.5));
		$this->SetAligns(array('C', 'C'));
		$this->SetHeights(array(5, 4.5, 5));
		$this->Row(array(utf8_decode('FIRMA'),
						 utf8_decode('FECHA')));
		$this->SetFont('Arial', 'B', 10);
		$this->SetHeights(array(5, 4.5, 10));
		$this->Row(array('', ''));
		##	detalles
		$this->SetFillColor(200, 200, 200);
		$this->SetFont('Arial', '', 10);
		$this->Cell(195, 5, utf8_decode('DETALLES DEL BONO VACACIONAL'), 1, 0, 'C', 1);
		##	
		//	consulto las deducciones
		$sql = "SELECT
					tnec.Monto,
					tnec.Cantidad
				FROM
					pr_tiponominaempleadoconcepto tnec
					INNER JOIN pr_concepto c ON (tnec.CodConcepto = c.CodConcepto AND c.Tipo = 'D')
				WHERE
					tnec.CodOrganismo = '".$fCodOrganismo."' AND
					tnec.CodTipoNom = '".$fCodTipoNom."' AND
					tnec.Periodo = '".$fPeriodo."' AND
					tnec.CodTipoProceso = '".$fCodTipoProceso."' AND
					tnec.CodPersona = '".$CodPersona."'";
		$query_deducciones = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		if (mysql_num_rows($query_deducciones) != 0) {
			$txt = "";
			$deducciones = 0;
			while($field_deducciones = mysql_fetch_array($query_deducciones)) {
				$deducciones += $field_deducciones['Monto'];
				$_Total = $field_ingresos['Monto'] - $deducciones;
				$txt .= number_format($field_ingresos['Monto'], 2, ',', '.')." x ".number_format($field_deducciones['Cantidad'], 2, ',', '.')." = ".number_format($field_deducciones['Monto'], 2, ',', '.')." - ".number_format($field_ingresos['Monto'], 2, ',', '.')." = ".number_format($_Total, 2, ',', '.');
				$_Monto = $txt;
			}
		} else {
			$_Monto = number_format($field_ingresos['Monto'], 2, ',', '.');
		}
		##
		$this->Ln();
		$this->SetFillColor(255, 255, 255);
		$this->SetFont('Arial', '', 10);
		$this->SetWidths(array(65, 65, 65));
		$this->SetAligns(array('C', 'C', 'C'));
		$this->SetHeights(array(5, 4.5, 5));
		$this->Row(array(utf8_decode('DIAS DE BONO VACACIONAL'),
						 utf8_decode('SUELDO DIARIO (Bs.)'),
						 utf8_decode('TOTAL (Bs.)')));
		$this->SetFont('Arial', 'B', 12);
		$this->SetHeights(array(10, 5, 15));
		$_Sueldo_Diario = $_Sueldo / 30;
		##	
		$this->Row(array(number_format($field_ingresos['Cantidad'], 2, ',', '.'),
						 //number_format(round((($field_sueldo['TotalIngresos'])/30),2), 2, ',', '.'),
						 number_format($_Sueldo_Diario, 2, ',', '.'),
						 $_Monto));
		##	observaciones
		$this->SetFillColor(200, 200, 200);
		$this->SetFont('Arial', '', 10);
		$this->Cell(195, 5, utf8_decode('OBSERVACIONES'), 1, 0, 'C', 1);
		##
		$this->Ln();
		$this->SetFillColor(255, 255, 255);
		$this->SetFont('Arial', '', 10);
		$this->SetWidths(array(195));
		$this->SetAligns(array('J'));
		$this->SetHeights(array(5, 4.5, 35));
		$this->Row(array(utf8_decode($Observaciones)));
		##	firmas
		$Creado = " $_CREADO[Nivel] $_CREADO[Nombre] $nl $_CREADO[Cargo]";
		$Revisado = " $_REVISADO[Nivel] $_REVISADO[Nombre] $nl $_REVISADO[Cargo]";
		$Conformado = " $_CONFORMADO[Nivel] $_CONFORMADO[Nombre] $nl $_CONFORMADO[Cargo]";
		//$Aprobado = " $_APROBADO[Nivel] $_APROBADO[Nombre] $nl $_APROBADO[Cargo]";
		
		$this->SetFillColor(200, 200, 200);
		$this->SetFont('Arial', '', 10);
		$this->Cell(195, 5, utf8_decode('CONFORMACIÓN Y APROBACIÓN'), 1, 0, 'C', 1);
		##
		$this->Ln();
		$this->SetFillColor(255, 255, 255);
		$this->SetFont('Arial', '', 10);
		$this->SetWidths(array(65, 65, 65));
		$this->SetAligns(array('C', 'C', 'C'));
		$this->SetHeights(array(5, 4.5, 5));
		$this->Row(array(utf8_decode('ELABORADO POR'),
						 utf8_decode('REVISADO POR'),
						 utf8_decode('CONFORMADO POR')));
		$this->SetFont('Arial', 'B', 8);
		$this->SetAligns(array('L', 'L'));
		$this->SetHeights(array(5, 4.5, 25));
		$this->Row(array(utf8_decode($Creado),
						 utf8_decode($Revisado),
						 utf8_decode($Conformado)));
		##	firmas
		/*$Creado = " $_CREADO[Nivel] $_CREADO[Nombre] $nl $_CREADO[Cargo]";
		$Revisado = " $_REVISADO[Nivel] $_REVISADO[Nombre] $nl $_REVISADO[Cargo]";
		$Conformado = " $_CONFORMADO[Nivel] $_CONFORMADO[Nombre] $nl $_CONFORMADO[Cargo]";
		$Aprobado = " $_APROBADO[Nivel] $_APROBADO[Nombre] $nl $_APROBADO[Cargo]";
		
		$this->SetFillColor(200, 200, 200);
		$this->SetFont('Arial', '', 10);
		$this->Cell(195, 5, utf8_decode('CONFORMACIÓN Y APROBACIÓN'), 1, 0, 'C', 1);
		##
		$this->Ln();
		$this->SetFillColor(255, 255, 255);
		$this->SetFont('Arial', '', 10);
		$this->SetWidths(array(97.5, 97.5));
		$this->SetAligns(array('C', 'C'));
		$this->SetHeights(array(5, 4.5, 5));
		$this->Row(array(utf8_decode('ELABORADO POR'),
						 utf8_decode('CONFORMADO POR')));
		$this->SetFont('Arial', 'B', 9);
		$this->SetAligns(array('L', 'L'));
		$this->SetHeights(array(5, 4.5, 20));
		$this->Row(array(utf8_decode($Creado),
						 utf8_decode($Conformado)));
		$this->SetFont('Arial', '', 10);
		$this->SetAligns(array('C', 'C'));
		$this->SetHeights(array(5, 4.5, 5));
		$this->Row(array(utf8_decode('REVISADO POR'),
						 utf8_decode('APROBADO POR')));
		$this->SetFont('Arial', 'B', 9);
		$this->SetAligns(array('L', 'L'));
		$this->SetHeights(array(5, 4.5, 20));
		$this->Row(array(utf8_decode($Revisado),
						 utf8_decode($Aprobado)));
		##
		$this->SetFont('Arial', 'B', 8);
		$this->SetXY(11, 240);
		$this->Cell(68, 5, utf8_decode('Firma: '), 0, 0, 'L');
		$this->Cell(20, 5, utf8_decode('Fecha: '.formatFechaDMA(substr($field['FechaCreacion'], 0, 10))), 0, 0, 'L');
		$this->SetXY(108.5, 240);
		$this->Cell(68, 5, utf8_decode('Firma: '), 0, 0, 'L');
		$this->Cell(20, 5, utf8_decode('Fecha: '.formatFechaDMA(substr($field['FechaRevision'], 0, 10))), 0, 0, 'L');
		$this->SetXY(11, 265);
		$this->Cell(68, 5, utf8_decode('Firma: '), 0, 0, 'L');
		$this->Cell(20, 5, utf8_decode('Fecha: '.formatFechaDMA(substr($field['FechaConformacion'], 0, 10))), 0, 0, 'L');
		$this->SetXY(108.5, 265);
		$this->Cell(68, 5, utf8_decode('Firma: '), 0, 0, 'L');
		$this->Cell(20, 5, utf8_decode('Fecha: '.formatFechaDMA(substr($field['FechaAprobacion'], 0, 10))), 0, 0, 'L');*/
	}
	
	//	Pie de página.
	function Footer() {}
}
//---------------------------------------------------

//---------------------------------------------------
//	Creación del objeto de la clase heredada.
$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->SetMargins(10, 1, 1);
$pdf->SetAutoPageBreak(5, 5);
//---------------------------------------------------
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
