<?php
//	------------------------------------------
//	LISTA DE FUNCIONES (EDITOR DE FORMULA)
//	------------------------------------------
//	obtener numero de hijos
function NUMERO_DE_HIJOS($edad=NULL, $fecha=NULL) {
	global $_ARGS;
	global $_PARAMETRO;
	if ($edad) {
		list($Anio, $Mes, $Dia) = explode('-', $_ARGS["_HASTA"]);
		$AnioDesde = $Anio - ($edad + 1);
		$FechaDesde = $AnioDesde.'-'.$Mes.'-'.$Dia;
		if (!$fecha) $fecha = formatFechaDMA(obtenerFechaFin(formatFechaDMA($_ARGS["_HASTA"]), 0));
		list($fecha_desde, $fecha_hasta) = getFechasEdad($edad, $fecha);
		$filtro = "AND FechaNacimiento >= '".formatFechaAMD($fecha_hasta)."'";
	}
	//	consulto
	$sql = "SELECT *
			FROM rh_cargafamiliar
			WHERE
				CodPersona = '".$_ARGS['_PERSONA']."' AND
				Parentesco = 'HI' $filtro";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	return intval(mysql_num_rows($query));
}

//	obtener numero de hijos
function HIJOS($edad=NULL) {
	global $_ARGS;
	global $_PARAMETRO;
	$filtro = "";
	if ($edad) {
		$filtro .= " AND (antiguedad_anios(FechaNacimiento, '".$_ARGS["_HASTA"]."')) <= ".intval($edad);
	}
	//	consulto
	$sql = "SELECT *
			FROM rh_cargafamiliar
			WHERE
				CodPersona = '".$_ARGS['_PERSONA']."' AND
				Parentesco = 'HI' $filtro";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	return intval(mysql_num_rows($query));
}

//	obtener numero de cursos
function NUMERO_DE_CURSOS() {
	global $_ARGS;
	global $_PARAMETRO;
	$sql = "SELECT *
			FROM rh_empleado_cursos
			WHERE
				CodPersona = '".$_ARGS['_PERSONA']."' AND
				FechaCulminacion <= '".$_ARGS['_PERIODO']."' AND
				FlagPago = 'S'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	return mysql_num_rows($query);
}

//	devuelve si es universitario
function UNIVERSITARIO() {
	global $_ARGS;
	global $_PARAMETRO;
	$sql = "SELECT *
			FROM rh_empleado_instruccion
			WHERE
				CodPersona = '".$_ARGS['_PERSONA']."' AND
				CodGradoInstruccion = 'UNI'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) return true;
	else return false;
}

//	devuelve si es tsu
function TSU() {
	global $_ARGS;
	global $_PARAMETRO;
	$sql = "SELECT *
			FROM rh_empleado_instruccion
			WHERE
				CodPersona = '".$_ARGS['_PERSONA']."' AND
				CodGradoInstruccion = 'TSU'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) return true;
	else return false;
}

//	obtener años de servicio
function ANIOS_DE_SERVICIO() {
	global $_ARGS;
	global $_PARAMETRO;
	//$sql = "SELECT antiguedad('".$_ARGS['_FECHA_INGRESO']."','".$_ARGS['_HASTA']."') AS tiempo";
	//$tiempo = getVar3($sql);
	//list($AniosOrganismo, $MesesOrganismo, $DiasOrganismo) = explode('-', $tiempo);
	list($AniosOrganismo, $MesesOrganismo, $DiasOrganismo) = getEdad(formatFechaDMA($_ARGS['_FECHA_INGRESO']), formatFechaDMA($_ARGS['_HASTA']));
	//if ($AniosOrganismo >= 1) list($AniosAntecedente, $MesesAntecedente, $DiasAntecedente) = getTiempoAntecedente($_ARGS['_PERSONA'], 'S');
	//else {
		$AniosAntecedente = 0; 
		$MesesAntecedente = 0; 
		$DiasAntecedente = 0;
	//}
	list($AniosServicio, $MesesServicio, $DiasServicio) = totalTiempo($AniosAntecedente+$AniosOrganismo, $MesesAntecedente+$MesesOrganismo, $DiasAntecedente+$DiasOrganismo);
	return intval($AniosServicio);
}

//	obtener años de servicio
function ANIOS_DE_SERVICIO_FRACCION() {
	global $_ARGS;
	global $_PARAMETRO;
	list($anios, $meses, $dias) = getEdad(formatFechaDMA($_ARGS['_FECHA_INGRESO']), formatFechaDMA($_ARGS['_HASTA']));
	if ($meses >= 6 && $dias > 0) ++$anios;
	return intval($anios);
}

//	obtener años de servicio
function ANIOS_DE_SERVICIO_PRESTACION() {
	global $_ARGS;
	global $_PARAMETRO;
	list($anios, $meses, $dias) = getEdad(formatFechaDMA($_ARGS['_FECHA_INGRESO']), formatFechaDMA($_ARGS['_FECHA_EGRESO']));
	if ($meses >= 6) ++$anios;
	return intval($anios);
}

//	devuelve si el empleado ocupa un cargo titular de jefatura
function JEFE_TITULAR() {
	global $_ARGS;
	global $_PARAMETRO;
	$sql = "SELECT
				me.CodPersona,
				rp.Grado
			FROM
				mastempleado me
				INNER JOIN rh_puestos rp ON (me.CodCargo = rp.CodCargo)
			WHERE
				me.CodPersona = '".$_ARGS['_PERSONA']."' AND
				rp.Grado >= '90' AND rp.Grado <= '99'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) return true; else return false;
}

//	obtener los dias como titular del empleado en cargos de jefatura
function DIAS_JERARQUIA() {
	global $_ARGS;
	global $_PARAMETRO;
	$suma = 0;
	$sql = "SELECT
				  en.Fecha,
				  en.FechaHasta,
				  p.Grado,
				  ns.SueldoPromedio AS SueldoBasico
			FROM
				  rh_empleadonivelacion en
				  INNER JOIN rh_puestos p ON (en.CodCargo = p.CodCargo)
				  INNER JOIN rh_nivelsalarial ns ON (p.CategoriaCargo = ns.CategoriaCargo AND p.Grado = ns.Grado AND en.Paso = ns.Paso)
			WHERE
				  en.CodOrganismo = '".$_ARGS['_ORGANISMO']."' AND
				  en.CodPersona = '".$_ARGS['_PERSONA']."' AND
				  en.TipoAccion <> 'ET' AND
				  ((en.FechaHasta = '0000-00-00' AND en.Fecha <= '".$_ARGS['_HASTA']."') OR
				   ('".$_ARGS['_DESDE']."' <= en.FechaHasta))
			ORDER BY en.Fecha";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	while ($field = mysql_fetch_array($query)) {
		if ($field['Fecha'] < $_ARGS['_DESDE']) $desde = $_ARGS['_DESDE'];
		else $desde = $field['Fecha'];
		if ($field['FechaHasta'] == "0000-00-00" || $field['FechaHasta'] > $_ARGS['_HASTA']) $hasta = $_ARGS['_HASTA'];
		else $hasta = $field['FechaHasta'];
		if ($field['Grado'] == "90" || $field['Grado'] == "96" || $field['Grado'] == "97" || $field['Grado'] == "98" || $field['Grado'] == "99") {
			$dias = DIAS_FECHA($desde, $hasta);
			$suma += $dias;
		}
	}
	return intval($suma);
}

//	obtener los dias de encargaduria del empleado en cargos de jefatura
function DIAS_JERARQUIA_DIFERENCIA() {
	global $_ARGS;
	global $_PARAMETRO;
	$suma = 0;
	$sql = "SELECT 
				  en.Fecha, 
				  en.FechaHasta, 
				  p.Grado, 
				  ns.SueldoPromedio AS SueldoBasico 
			FROM 
				  rh_empleadonivelacion en 
				  INNER JOIN rh_puestos p ON (en.CodCargo = p.CodCargo) 
				  INNER JOIN rh_nivelsalarial ns ON (p.CategoriaCargo = ns.CategoriaCargo AND p.Grado = ns.Grado AND en.Paso = ns.Paso) 
			WHERE
				  en.CodOrganismo = '".$_ARGS['_ORGANISMO']."' AND
				  en.CodPersona = '".$_ARGS['_PERSONA']."' AND
				  en.TipoAccion = 'ET' AND
				  ((en.FechaHasta = '0000-00-00' AND en.Fecha <= '".$_ARGS['_HASTA']."') OR
				   ('".$_ARGS['_DESDE']."' <= en.FechaHasta))
			ORDER BY en.Fecha";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	while ($field = mysql_fetch_array($query)) {
		if ($field['Fecha'] < $_ARGS['_DESDE']) $desde = $_ARGS['_DESDE'];
		else $desde = $field['Fecha'];
		if ($field['FechaHasta'] == "0000-00-00" || $field['FechaHasta'] > $_ARGS['_HASTA']) $hasta = $_ARGS['_HASTA'];
		else $hasta = $field['FechaHasta'];
		if ($field['Grado'] == "90" || $field['Grado'] == "96" || $field['Grado'] == "97" || $field['Grado'] == "98" || $field['Grado'] == "99") {
			$dias = DIAS_FECHA($desde, $hasta);
			$suma += $dias;
		}
	}
	return intval($suma);
}

//	devuelve si el empleado tiene una especializacion
function ESPECIALIZACION() {
	global $_ARGS;
	global $_PARAMETRO;
	$sql = "SELECT *
			FROM rh_empleado_instruccion
			WHERE
				CodPersona = '".$_ARGS['_PERSONA']."' AND
				CodGradoInstruccion = 'POS' AND
				Nivel = '01'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) return true;
	else return false;
}

//	devuelve si el empleado tiene un magister
function MAGISTER() {
	global $_ARGS;
	global $_PARAMETRO;
	$sql = "SELECT *
			FROM rh_empleado_instruccion
			WHERE
				CodPersona = '".$_ARGS['_PERSONA']."' AND
				CodGradoInstruccion = 'POS' AND
				Nivel = '02'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) return true;
	else return false;
}

//	devuelve si el empleado tiene un doctorado
function DOCTORADO() {
	global $_ARGS;
	global $_PARAMETRO;
	$sql = "SELECT *
			FROM rh_empleado_instruccion
			WHERE
				CodPersona = '".$_ARGS['_PERSONA']."' AND
				CodGradoInstruccion = 'POS' AND
				Nivel = '03'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) return true;
	else return false;
}

//	obtener diferencia de sueldo basico
function DIFERENCIA_SUELDO_BASICO() {
	global $_ARGS;
	global $_PARAMETRO;	
	$sum_diferencia = 0;
	//	Obtengo el sueldo basico mensual...
	$sql = "SELECT ns.SueldoPromedio AS SueldoBasico 
			FROM 
				  rh_empleadonivelacion en 
				  INNER JOIN rh_puestos p ON (en.CodCargo = p.CodCargo) 
				  INNER JOIN rh_nivelsalarial ns ON (p.CategoriaCargo = ns.CategoriaCargo AND p.Grado = ns.Grado AND en.Paso = ns.Paso) 
			WHERE 
				  en.CodOrganismo = '".$_ARGS['_ORGANISMO']."' AND  
				  en.CodPersona = '".$_ARGS['_PERSONA']."' AND 
				  en.TipoAccion <> 'ET' AND 
				  en.FechaHasta = '0000-00-00'
			ORDER BY en.Fecha";
	$query_sueldo = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query_sueldo) != 0) $field_sueldo = mysql_fetch_array($query_sueldo);
	##
	$sql = "SELECT
				  en.Fecha, 
				  en.FechaHasta, 
				  ns.SueldoPromedio AS SueldoTemporal 
			FROM 
				  rh_empleadonivelacion en 
				  INNER JOIN rh_puestos p ON (en.CodCargo = p.CodCargo) 
				  INNER JOIN rh_nivelsalarial ns ON (p.CategoriaCargo = ns.CategoriaCargo AND p.Grado = ns.Grado AND en.Paso = ns.Paso) 
			WHERE 
				  en.CodOrganismo = '".$_ARGS['_ORGANISMO']."' AND  
				  en.CodPersona = '".$_ARGS['_PERSONA']."' AND 
				  en.TipoAccion = 'ET' AND 
				  ((en.FechaHasta = '0000-00-00' AND en.Fecha <= '".$_ARGS['_HASTA']."') OR 
				   ('".$_ARGS['_DESDE']."' >= en.Fecha AND 
				    '".$_ARGS['_DESDE']."' <= en.FechaHasta) OR 
				   (en.Fecha >= '".$_ARGS['_DESDE']."' AND 
				    en.Fecha <= '".$_ARGS['_HASTA']."')) 
			ORDER BY en.Fecha";
	$query_nivelaciones = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	while ($field_nivelaciones = mysql_fetch_array($query_nivelaciones)) {
		if ($field_nivelaciones['Fecha'] < $_ARGS['_DESDE']) $desde = $_ARGS['_DESDE'];
		else $desde = $field_nivelaciones['Fecha'];
		##
		if ($field_nivelaciones['FechaHasta'] == "0000-00-00" || $field_nivelaciones['FechaHasta'] > $_ARGS['_HASTA']) $hasta = $_ARGS['_HASTA'];
		else $hasta = $field_nivelaciones['FechaHasta'];
		##
		$dias = DIAS_FECHA($desde, $hasta);
		##
		$Diferencia = $field_nivelaciones['SueldoTemporal'] - $field_sueldo['SueldoBasico'];
		$Diario = $Diferencia / $_PARAMETRO['MAXDIASMES'];
		$monto = $Diario * $dias;
		##
		$sum_diferencia += $monto;
	}
	return $sum_diferencia;
}

//	obtener adelanto de quincena
function ADELANTO_QUINCENA() {
	global $_ARGS;
	global $_PARAMETRO;
	$sql = "SELECT TotalIngresos
			FROM pr_tiponominaempleado
			WHERE
				Periodo = '".$_ARGS['_PERIODO']."' AND
				CodPersona = '".$_ARGS['_PERSONA']."' AND
				CodOrganismo = '".$_ARGS['_ORGANISMO']."' AND
				CodTipoProceso = 'ADE'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) {
		$field = mysql_fetch_array($query);
		return floatval($field[0]);
	} else return 0;
}

function LUNES_PROCESO() {
	global $_ARGS;
	global $_PARAMETRO;
	$lunes = 0;
	$fecha1 = strtotime($_ARGS['_DESDE']); 
	$fecha2 = strtotime($_ARGS['_HASTA']);
	for($fecha1;$fecha1<=$fecha2;$fecha1=strtotime('+1 day ' . date('Y-m-d',$fecha1))){ 
		if((strcmp(date('D',$fecha1),'Mon')==0)){
			++$lunes;
		}
	}
	return intval($lunes);
}

function lunes_x_fecha($fecha1, $fecha2) {
	$lunes = 0;
	$fecha1 = strtotime($fecha1); 
	$fecha2 = strtotime($fecha2);
	for($fecha1;$fecha1<=$fecha2;$fecha1=strtotime('+1 day ' . date('Y-m-d',$fecha1))){ 
		if((strcmp(date('D',$fecha1),'Mon')==0)){
			++$lunes;
		}
	}
	return intval($lunes);
}

//	obtener numero de lunes
function NUMERO_LUNES_FECHA() {
	global $_ARGS;
	global $_PARAMETRO;
	if ($_ARGS['_FECHA_INGRESO'] <= $_ARGS['_DESDE']) $FechaInicio = $_ARGS['_DESDE'];
	else $FechaInicio = $_ARGS['_FECHA_INGRESO'];
	if ($_ARGS['_FECHA_EGRESO'] == "I" && $_ARGS['_FECHA_EGRESO'] <= $_ARGS['_HASTA']) $FechaFin = $_ARGS['_FECHA_EGRESO'];
	else $FechaFin = $_ARGS['_HASTA'];
	$lunes = lunes_x_fecha($FechaInicio, $FechaFin);
	return $lunes;
}

//	obtener numero de lunes
function NUMERO_LUNES_MES() {
	global $_ARGS;
	global $_PARAMETRO;
	list($a, $m, $d) = explode("-", $_ARGS['_DESDE']);
	if ($_ARGS['_FECHA_INGRESO'] <= $_ARGS['_DESDE']) $FechaInicio = "$a-$m-01";
	else $FechaInicio = $_ARGS['_FECHA_INGRESO'];
	if ($_ARGS['_FECHA_EGRESO'] == "I" && $_ARGS['_FECHA_EGRESO'] <= $_ARGS['_HASTA']) $FechaFin = $_ARGS['_FECHA_EGRESO'];
	else {
		list($anio, $mes, $dia) = explode('-', $_ARGS['_HASTA']);
		$FechaFin = "$anio-$mes-".getDiasMes("$anio-$mes");
	}
	$lunes = lunes_x_fecha($FechaInicio, $FechaFin);
	return $lunes;
}

//	obtener numero de lunes
function NUMERO_LUNES_PERIODO() {
	global $_ARGS;
	global $_PARAMETRO;
	list($a, $m, $d) = explode("-", $_ARGS['_DESDE']);
	if ($_ARGS['_FECHA_INGRESO'] <= $_ARGS['_DESDE']) $FechaInicio = $_ARGS['_DESDE'];
	else $FechaInicio = $_ARGS['_FECHA_INGRESO'];
	if ($_ARGS['_FECHA_EGRESO'] == "I" && $_ARGS['_FECHA_EGRESO'] <= $_ARGS['_HASTA']) $FechaFin = $_ARGS['_FECHA_EGRESO'];
	else {
		list($anio, $mes, $dia) = explode('-', $_ARGS['_HASTA']);
		$FechaFin = "$anio-$mes-".getDiasMes("$anio-$mes");
	}
	$lunes = lunes_x_fecha($FechaInicio, $FechaFin);
	return $lunes;
}

//	obtener el sueldo minimo
function SUELDO_MINIMO() {
	global $_ARGS;
	global $_PARAMETRO;
	$sql = "SELECT Monto
			FROM mastsueldosmin
			WHERE Periodo = (SELECT MAX(Periodo) FROM mastsueldosmin WHERE Periodo <= '".$_ARGS['_PERIODO']."')";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) {
		$field = mysql_fetch_array($query);
		return floatval($field[0]);
	} else return 0;
}

//	obtener el sueldo minimo
function ULTIMO_SUELDO_MINIMO() {
	global $_ARGS;
	global $_PARAMETRO;
	$sql = "SELECT Monto
			FROM mastsueldosmin
			WHERE Periodo < (SELECT MAX(Periodo) FROM mastsueldosmin WHERE Periodo <= '".$_ARGS['_PERIODO']."')
			ORDER BY Periodo DESC
			LIMIT 0, 1";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) {
		$field = mysql_fetch_array($query);
		return floatval($field[0]);
	} else return 0;
}

//	obtener el tipo de retencion del empleado (M:MONTO; P:PORCENTAJE)
function TIPO_RETENCION() {
	global $_ARGS;
	global $_PARAMETRO;
	$sql = "SELECT rjc.TipoDescuento 
			FROM 
				rh_retencionjudicial rj
				INNER JOIN rh_retencionjudicialconceptos rjc ON (rj.CodOrganismo = rjc.CodOrganismo AND rj.CodRetencion = rjc.CodRetencion)
			WHERE 
				rj.CodPersona = '".$_ARGS['_PERSONA']."' AND 
				rj.FechaResolucion <= '".$_ARGS['_HASTA']."' AND 
				rjc.CodConcepto = '".$_ARGS['_CONCEPTO']."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	if ($field['TipoDescuento'] == "P") $_TIPO = "PORCENTAJE";
	elseif ($field['TipoDescuento'] == "M") $_TIPO = "MONTO";
	return $_TIPO;
}

//	obtener el monto de la retencion judicial del empleado
function RETENCION_JUDICIAL() {
	global $_ARGS;
	global $_PARAMETRO;
	$sql = "SELECT rjc.Descuento 
			FROM 
				rh_retencionjudicial rj
				INNER JOIN rh_retencionjudicialconceptos rjc ON (rj.CodOrganismo = rjc.CodOrganismo AND rj.CodRetencion = rjc.CodRetencion)
			WHERE 
				rj.CodPersona = '".$_ARGS['_PERSONA']."' AND 
				rj.FechaResolucion <= '".$_ARGS['_HASTA']."' AND 
				rjc.CodConcepto = '".$_ARGS['_CONCEPTO']."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) {
		$field = mysql_fetch_array($query);
		return floatval($field[0]);
	} else return 0;
}

//	obtener el porcentaje a descontar por impuesto soble la renta
function PORCENTAJE_ISLR() {
	global $_ARGS;
	global $_PARAMETRO;
	$sql = "SELECT Porcentaje
			FROM pr_impuestorenta
			WHERE
				CodPersona = '".$_ARGS['_PERSONA']."' AND
				'".$_ARGS['_PERIODO']."' >= Desde AND '".$_ARGS['_PERIODO']."' <= Hasta";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) {
		$field = mysql_fetch_array($query);
		return floatval($field[0]);
	} else return 0;
}

//	obtener meses de antiguedad del empleado
function MESES_ANTIGUEDAD() {
	global $_ARGS;
	global $_PARAMETRO;
	list($anios, $meses, $dias) = getTiempo(formatFechaDMA($_ARGS['_FECHA_INGRESO']), formatFechaDMA($_ARGS['_HASTA']));
	$cantidad = $meses + ($anios * 12);
	return intval($cantidad);
}

//	obtener el sueldo normal
function SUELDO_NORMAL() {
	global $_ARGS;
	global $_PARAMETRO;
	$sql = "SELECT SueldoNormal
			FROM rh_sueldos
			WHERE
				CodPersona = '".$_ARGS['_PERSONA']."' AND
				Periodo = '".$_ARGS['_PERIODO']."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) {
		$field = mysql_fetch_array($query);
		return floatval($field[0]);
	} else return 0;
}

//	obtener el sueldo normal
function SUELDO_NORMAL_DIARIO() {
	global $_ARGS;
	global $_PARAMETRO;
	$SueldoNormal = SUELDO_NORMAL();
	return floatval(round(($SueldoNormal / 30), 2));
}

//	devuelve el ultimo sueldo basico del empleado (ultimo periodo anterior)
function ULTIMO_SUELDO_BASICO($Periodo = NULL) {
	global $_ARGS;
	global $_PARAMETRO;
	##	
	if ($Periodo) $fPeriodo = "Periodo < '".$_ARGS['_PERIODO']."'"; else $fPeriodo = "Periodo = '".$_ARGS['_PERIODO']."'";
	$sql = "SELECT SueldoBasico
			FROM rh_sueldos
			WHERE
				CodPersona = '".$_ARGS['_PERSONA']."' AND
				$fPeriodo
			ORDER BY Periodo DESC
			LIMIT 0, 1";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) {
		$field = mysql_fetch_array($query);
		return floatval($field[0]);
	} else return 0;
}

function SUELDO_BASICO_NOMINA($Periodo = NULL, $CodTipoProceso = NULL)
{
	global $_ARGS;
	global $_PARAMETRO;
	##	
	if ($Periodo) $fPeriodo = "AND Periodo = '$Periodo'"; else $fPeriodo = "AND Periodo = '$_ARGS[_PERIODO]'";
	if ($CodTipoProceso) $fCodTipoProceso = "AND CodTipoProceso = '$CodTipoProceso'"; else $fCodTipoProceso = "AND CodTipoProceso = '$_ARGS[_PROCESO]'";
	$sql = "SELECT *
			FROM pr_tiponominaempleado
			WHERE
				CodOrganismo = '$_ARGS[_ORGANISMO]'
				AND CodTipoNom = '$_ARGS[_NOMINA]'
				AND CodPersona = '$_ARGS[_PERSONA]'
				$fCodTipoProceso
				$fPeriodo";
	$field = getRecord($sql);

	return floatval($field['SueldoBasico']);
}

//	devuelve el ultimo sueldo normal del empleado (ultimo periodo anterior)
function ULTIMO_SUELDO_NORMAL() {
	global $_ARGS;
	global $_PARAMETRO;
	$sql = "SELECT SueldoNormal
			FROM rh_sueldos
			WHERE
				CodPersona = '".$_ARGS['_PERSONA']."' AND
				Periodo < '".$_ARGS['_PERIODO']."'
			ORDER BY Periodo DESC
			LIMIT 0, 1";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) {
		$field = mysql_fetch_array($query);
		return floatval($field['SueldoNormal']);
	} else return 0;
}

//	devuelve el ultimo sueldo normal diario del empleado (ultimo periodo anterior)
function ULTIMO_SUELDO_NORMAL_DIARIO() {
	global $_ARGS;
	global $_PARAMETRO;
	$sql = "SELECT Periodo, SueldoNormal
			FROM rh_sueldos
			WHERE
				CodPersona = '".$_ARGS['_PERSONA']."' AND
				Periodo < '".$_ARGS['_PERIODO']."'
			ORDER BY Periodo DESC
			LIMIT 0, 1";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) {
		$field = mysql_fetch_array($query);
		$UltimoSueldoNormal = floatval($field['SueldoNormal']);
	} else $UltimoSueldoNormal = 0;
	##
	$DiasPeriodo = getDiasMes($field['Periodo']);
	$FechaEgreso = getVar3("SELECT FechaFinNomina FROM mastempleado WHERE CodPersona = '".$_ARGS['_PERSONA']."'");
	list($AnioEgreso, $MesEgreso, $DiaEgreso) = explode('-', $FechaEgreso);
	$PeriodoEgreso = $AnioEgreso.'-'.$MesEgreso;
	if ($_ARGS['_ESTADO'] == 'I' && $PeriodoEgreso == $field['Periodo']) $DiasTrabajados = intval($DiaEgreso);
	else $DiasTrabajados = $DiasPeriodo;
	if ($DiasTrabajados >= $DiasPeriodo) $DiasParaDiario = 30; else $DiasParaDiario = $DiasTrabajados;
	##	
	$UltimoSueldoNormal = ULTIMO_SUELDO_NORMAL();
	return floatval(round(($UltimoSueldoNormal / $DiasParaDiario), 2));
}

//	obtener el sueldo integral
function SUELDO_INTEGRAL() {
	global $_ARGS;
	global $_PARAMETRO;
	$sql = "SELECT SueldoIntegral
			FROM rh_sueldos
			WHERE
				CodPersona = '".$_ARGS['_PERSONA']."' AND
				Periodo = '".$_ARGS['_PERIODO']."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) {
		$field = mysql_fetch_array($query);
		return floatval($field[0]);
	} else return 0;
}

//	devuelve el ultimo sueldo integral del empleado (ultimo periodo anterior)
function ULTIMO_SUELDO_INTEGRAL() {
	global $_ARGS;
	global $_PARAMETRO;
	$sql = "SELECT SueldoIntegral
			FROM rh_sueldos
			WHERE
				CodPersona = '".$_ARGS['_PERSONA']."' AND
				Periodo < '".$_ARGS['_PERIODO']."'
			ORDER BY Periodo DESC
			LIMIT 0, 1";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) {
		$field = mysql_fetch_array($query);
		return floatval($field['SueldoIntegral']);
	} else return 0;
}

//	devuelve el ultimo sueldo integral diario del empleado (ultimo periodo anterior)
function ULTIMO_SUELDO_INTEGRAL_DIARIO() {
	global $_ARGS;
	global $_PARAMETRO;
	$UltimoSueldoNormal = ULTIMO_SUELDO_INTEGRAL();
	return floatval(round(($UltimoSueldoNormal / 30), 2));
}

//	obtener la ultima alicuota vacacional
function ULTIMA_ALICUOTA_VACACIONAL() {
	global $_ARGS;
	global $_PARAMETRO;
	$sql = "SELECT AliVac
			FROM rh_sueldos
			WHERE
				CodPersona = '".$_ARGS['_PERSONA']."' AND
				Periodo < '".$_ARGS['_PERIODO']."'
			ORDER BY Periodo DESC
			LIMIT 0, 1";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) {
		$field = mysql_fetch_array($query);
		return floatval($field[0]);
	} else return 0;
}

//	obtener el sueldo integral
function SUELDO_INTEGRAL_PARCIAL() {
	global $_ARGS;
	global $_PARAMETRO;
	if ($_ARGS['_ESTADO'] == "A") {
		$sql = "SELECT SueldoIntegralParcial
				FROM rh_sueldos
				WHERE
					CodPersona = '".$_ARGS['_PERSONA']."' AND
					Periodo = '".$_ARGS['_PERIODO']."'";
	} else {
		$sql = "SELECT SueldoNormal
				FROM rh_sueldos
				WHERE
					CodPersona = '".$_ARGS['_PERSONA']."' AND
					Periodo = '".$_ARGS['_PERIODO']."'";
	}
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) {
		$field = mysql_fetch_array($query);
		return floatval($field[0]);
	} else return 0;
}

//	obtener el sueldo integral
function SUELDO_INTEGRAL_PARCIAL_DIARIO() {
	global $_ARGS;
	global $_PARAMETRO;
	$SUELDO_INTEGRAL_PARCIAL = SUELDO_INTEGRAL_PARCIAL();
	return floatval(round(($SUELDO_INTEGRAL_PARCIAL / 30), 2));
	
}

//	devuelve el ultimo sueldo integral del empleado (ultimo periodo anterior)
function ULTIMO_SUELDO_INTEGRAL_PARCIAL() {
	global $_ARGS;
	global $_PARAMETRO;
	$sql = "SELECT SueldoIntegralParcial
			FROM rh_sueldos
			WHERE
				CodPersona = '".$_ARGS['_PERSONA']."' AND
				Periodo < '".$_ARGS['_PERIODO']."'
			ORDER BY Periodo DESC
			LIMIT 0, 1";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) {
		$field = mysql_fetch_array($query);
		return floatval($field['SueldoIntegralParcial']);
	} else return 0;
}

//	devuelve el ultimo sueldo integral diario del empleado (ultimo periodo anterior)
function ULTIMO_SUELDO_INTEGRAL_PARCIAL_DIARIO() {
	global $_ARGS;
	global $_PARAMETRO;
	$UltimoSueldoNormal = ULTIMO_SUELDO_INTEGRAL_PARCIAL();
	return floatval(round(($UltimoSueldoNormal / 30), 2));
}

//	obtener la suma de las bonificaciones
function BONOS() {
	global $_ARGS;
	global $_PARAMETRO;
	$sql = "SELECT SUM(tnec.Monto) AS Bonos
			FROM
				pr_concepto c
				INNER JOIN pr_tiponominaempleadoconcepto tnec ON (tnec.CodConcepto = c.CodConcepto)
			WHERE
				c.FlagBonoRemuneracion = 'S' AND
				tnec.Periodo = '".$_ARGS['_PERIODO']."' AND
				tnec.CodPersona = '".$_ARGS['_PERSONA']."'
			GROUP BY tnec.CodPersona";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) {
		$field = mysql_fetch_array($query);
		return floatval($field['Bonos']);
	} else return 0;
}

//	obtener la remuneracion diaria
function REMUNERACION_DIARIA() {
	global $_ARGS;
	global $_PARAMETRO;
	$Sueldo = $_ARGS['_SUELDO_NORMAL_DIARIO'];
	$Bonos = round(BONOS() / 30, 2);
	$Diario = $Sueldo + $Bonos;
	return floatval($Diario);
}

//	obtener la remuneracion diaria
function ULTIMA_REMUNERACION_DIARIA() {
	global $_ARGS;
	global $_PARAMETRO;
	$Sueldo = ULTIMO_SUELDO_NORMAL_DIARIO();
	$Bonos = round(BONOS() / 30, 2);
	$Diario = $Sueldo + $Bonos;
	return floatval($Diario);
}

//	devuelve el total de ingresos para un proceso
function TOTAL_INGRESOS($CodTipoProceso=NULL, $Periodo=NULL) {
	global $_ARGS;
	global $_PARAMETRO;
	##
	if (!$CodTipoProceso) $CodTipoProceso = $_ARGS['_PROCESO'];
	if (!$Periodo) $Periodo = $_ARGS['_PERIODO'];
	##
	$sql = "SELECT TotalIngresos
			FROM pr_tiponominaempleado
			WHERE
				CodTipoNom = '".$_ARGS['_NOMINA']."' AND
				CodPersona = '".$_ARGS['_PERSONA']."' AND
				CodOrganismo = '".$_ARGS['_ORGANISMO']."' AND
				CodTipoProceso = '".$CodTipoProceso."' AND
				Periodo = '".$Periodo."'";
	$TotalIngresos = getVar3($sql);
	return floatval($TotalIngresos);
}

//	devuelve el total de ingresos para un proceso
function ULTIMO_TOTAL_INGRESOS($CodTipoProceso=NULL) {
	global $_ARGS;
	global $_PARAMETRO;
	##
	if (!$CodTipoProceso) $CodTipoProceso = $_ARGS['_PROCESO'];
	##
	$sql = "SELECT TotalIngresos
			FROM pr_tiponominaempleado
			WHERE
				CodTipoNom = '".$_ARGS['_NOMINA']."' AND
				CodPersona = '".$_ARGS['_PERSONA']."' AND
				CodOrganismo = '".$_ARGS['_ORGANISMO']."' AND
				CodTipoProceso = '".$CodTipoProceso."' AND
				Periodo < '".$_ARGS['_PERIODO']."'
			ORDER BY Periodo DESC
			LIMIT 0, 1";
	$TotalIngresos = getVar3($sql);
	return floatval($TotalIngresos);
}

//	devuelve si el periodo cumple el trimestre
function TRIMESTRE() {
	global $_ARGS;
	global $_PARAMETRO;
	list($Anio, $Mes) = split("[./-]", $_ARGS['_PERIODO']);
	if ($Mes == "03" || $Mes == "06" || $Mes == "09" || $Mes == "12") return true;
	else return false;
}

//	devuelve los dias para el deposito de antiguedad
function DIAS_ANTIGUEDAD_TRIMESTRAL() {
	global $_ARGS;
	global $_PARAMETRO;
	if ($_ARGS['_MES_PROCESO'] <= "03") $InicioTri = $_ARGS['_ANO_PROCESO']."-01-01";
	elseif ($_ARGS['_MES_PROCESO'] <= "06") $InicioTri = $_ARGS['_ANO_PROCESO']."-04-01";
	elseif ($_ARGS['_MES_PROCESO'] <= "09") $InicioTri = $_ARGS['_ANO_PROCESO']."-07-01";
	elseif ($_ARGS['_MES_PROCESO'] <= "12") $InicioTri = $_ARGS['_ANO_PROCESO']."-10-01";
	if ($_ARGS['_FECHA_INGRESO'] <= $InicioTri) $Desde = $InicioTri; else $Desde = $_ARGS['_FECHA_INGRESO'];
	if ($_ARGS['_ESTADO'] == "A") $Hasta = $_ARGS['_HASTA']; else $Hasta = $_ARGS['_FECHA_EGRESO'];
	if (($_ARGS['_ESTADO'] == "A" && TRIMESTRE()) || $_ARGS['_ESTADO'] == "I") {
		list($Anios, $Meses, $Dias) = getTiempo(formatFechaDMA($Desde), formatFechaDMA($Hasta));
		$Dias++;
	}
	if ($Dias >= 1) ++$Meses;
	if ($Meses >= 3) $DiasAntiguedad = $_PARAMETRO['DIASANTIG'] * 3;
	else $DiasAntiguedad = $_PARAMETRO['DIASANTIG'] * $Meses;
	return intval($DiasAntiguedad);
}

//	devuelve el monto de un concepto calculado
function RETROACTIVO($CONCEPTO, $CodTipoProceso) {
	global $_ARGS;
	global $_PARAMETRO;
	##
	$sql = "SELECT PeriodoNomina
			FROM pr_procesoperiodo
			WHERE
				CodOrganismo = '".$_ARGS['_ORGANISMO']."' AND
				CodTipoNom = '".$_ARGS['_NOMINA']."' AND
				Periodo = '".$_ARGS['_PERIODO']."' AND
				CodTipoProceso = '".$_ARGS['_PROCESO']."'";
	$query_pp = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query_pp) != 0) {
		$field_pp = mysql_fetch_array($query_pp);
		$PeriodoNomina = $field_pp[0];
	} else $PeriodoNomina = "";
	##
	$sql = "SELECT Monto
			FROM pr_tiponominaempleadoconcepto
			WHERE
				CodOrganismo = '".$_ARGS['_ORGANISMO']."' AND
				CodTipoNom = '".$_ARGS['_NOMINA']."' AND
				CodPersona = '".$_ARGS['_PERSONA']."' AND
				Periodo = '".$PeriodoNomina."' AND
				CodTipoProceso = '".$CodTipoProceso."' AND
				CodConcepto = '".$CONCEPTO."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) {
		$field = mysql_fetch_array($query);
		return floatval($field[0]);
	} else return 0;
}

//	devuelve el monto de un concepto calculado
function CONCEPTO($CONCEPTO) {
	global $_ARGS;
	global $_PARAMETRO;
	$sql = "SELECT Monto
			FROM pr_tiponominaempleadoconcepto
			WHERE
				CodOrganismo = '".$_ARGS['_ORGANISMO']."' AND
				CodTipoNom = '".$_ARGS['_NOMINA']."' AND
				Periodo = '".$_ARGS['_PERIODO']."' AND
				CodTipoProceso = '".$_ARGS['_PROCESO']."' AND
				CodPersona = '".$_ARGS['_PERSONA']."' AND
				CodConcepto = '".$CONCEPTO."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) {
		$field = mysql_fetch_array($query);
		return floatval($field[0]);
	} else return 0;
}

//	devuelve el monto de un concepto calculado
function ULTIMO_CONCEPTO($CONCEPTO, $CodTipoProceso=NULL, $Periodo=NULL) {
	global $_ARGS;
	global $_PARAMETRO;
	$filtro = "";
	if ($CodTipoProceso != "") $filtro .= " AND CodTipoProceso = '".$CodTipoProceso."'";
	if (!$Periodo) $filtro_periodo = "Periodo < '".$_ARGS['_PERIODO']."' AND"; else $filtro_periodo = "Periodo = '".$Periodo."' AND";
	$sql = "SELECT Monto
			FROM pr_tiponominaempleadoconcepto
			WHERE
				$filtro_periodo
				CodPersona = '".$_ARGS['_PERSONA']."' AND
				CodConcepto = '".$CONCEPTO."'
				$filtro
			ORDER BY Periodo DESC
			LIMIT 0, 1";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) {
		$field = mysql_fetch_array($query);
		return floatval($field[0]);
	} else return 0;
}

//	devuelve el monto de un concepto calculado
function ULTIMO_CONCEPTO_PERIODO($CONCEPTO) {
	global $_ARGS;
	global $_PARAMETRO;
	$filtro = "";
	##	
	$sql = "SELECT Periodo
			FROM pr_tiponominaempleado
			WHERE
				CodTipoNom = '".$_ARGS['_NOMINA']."' AND
				Periodo < '".$_ARGS['_PERIODO']."' AND
				CodPersona = '".$_ARGS['_PERSONA']."' AND
				CodOrganismo = '".$_ARGS['_ORGANISMO']."'
			ORDER BY Periodo DESC
			LIMIT 0, 1";
	$PeriodoAnterior = getVar3($sql);
	##	
	$sql = "SELECT SUM(Monto) AS Monto
			FROM pr_tiponominaempleadoconcepto
			WHERE
				CodTipoNom = '".$_ARGS['_NOMINA']."' AND
				Periodo = '".$PeriodoAnterior."' AND
				CodPersona = '".$_ARGS['_PERSONA']."' AND
				CodOrganismo = '".$_ARGS['_ORGANISMO']."' AND
				CodConcepto = '".$CONCEPTO."'";
	$Monto = getVar3($sql);
	return floatval($Monto);
}

//	devuelve el monto de un concepto calculado
function CONCEPTO_PERIODOS($CodConcepto, $Periodos=NULL) {
	global $_ARGS;
	global $_PARAMETRO;
	##	
	$filtro_periodo = "";

	if ($Periodos)
	{
		$array = explode(',', $Periodos);
		$periodo = "";
		foreach ($array as $p)
		{
			$p = trim($p);
			if ($periodo) $periodo .= ",'$p'"; else $periodo .= "'$p'";
		}
		$filtro_periodo .= " AND Periodo IN ($periodo)";
	}
	else
	{
		$filtro_periodo .= " AND Periodo = '$_ARGS[_PERIODO]'";
	}
	##	
	$sql = "SELECT Monto
			FROM pr_tiponominaempleadoconcepto
			WHERE
				CodOrganismo = '$_ARGS[_ORGANISMO]'
				AND CodTipoNom = '$_ARGS[_NOMINA]'
				AND CodPersona = '$_ARGS[_PERSONA]'
				AND CodConcepto = '$CodConcepto'
				$filtro_periodo";
	$field = getRecord($sql);

	return floatval($field['Monto']);
}

//	devuelve el nro de dias pendientes de pago de vacaciones
function DIAS_VACACIONES_PENDIENTES() {
	global $_ARGS;
	global $_PARAMETRO;
	$sql = "SELECT PendientePago
			FROM rh_vacacionperiodo
			WHERE CodPersona = '".$_ARGS['_PERSONA']."'
			ORDER BY NroPeriodo DESC
			LIMIT 0, 1";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) {
		$field = mysql_fetch_array($query);
		return floatval($field[0]);
	} else return 0;
}

//	obtener meses de antiguedad del empleado
function MESES_FRACCION() {
	global $_ARGS;
	global $_PARAMETRO;
	list($Anios, $Meses, $Dias) = getEdad(formatFechaDMA($_ARGS['_FECHA_INGRESO']), formatFechaDMA($_ARGS['_FECHA_EGRESO']));
	return intval($Meses);
}

//	
function MESES_FRACCION_ACTUAL() {
	global $_ARGS;
	global $_PARAMETRO;
	list($anios, $meses) = split("[./-]", $_ARGS['_PERIODO']);
	return intval($meses);
}

//	
function MESES_FRACCION_EGRESO() {
	global $_ARGS;
	global $_PARAMETRO;
	list($Anio, $Mes, $Dia) = split("[./-]", $_ARGS['_FECHA_EGRESO']);
	if ($Dia < getDiasMes("$Anio-$Mes")) return (intval($Mes) - 1);
	else return intval($Mes);
}

//	obtener meses de antiguedad del empleado del año
function MESES_FRACCION_ANUAL() {
	global $_ARGS;
	global $_PARAMETRO;

	$FechaDesde = $_ARGS['_ANO_PROCESO']."-01-01";
	$FechaHasta = $_ARGS['_ANO_PROCESO']."-12-31";
	if ($_ARGS['_FECHA_INGRESO'] > $FechaDesde) $FechaDesde = $_ARGS['_FECHA_INGRESO'];
	if ($_ARGS['_FECHA_EGRESO'] < $FechaHasta) $FechaHasta = $_ARGS['_FECHA_EGRESO'];

	list($Anios, $Meses, $Dias) = getEdad(formatFechaDMA($FechaDesde), formatFechaDMA($FechaHasta));
	return intval($Meses);
}

//	devuelve el nro de dias pendientes de disfrute de vacaciones
function DIAS_DISFRUTE_PENDIENTE() {
	global $_ARGS;
	global $_PARAMETRO;
	$sql = "SELECT Pendientes
			FROM rh_vacacionperiodo
			WHERE CodPersona = '".$_ARGS['_PERSONA']."'
			ORDER BY NroPeriodo DESC
			LIMIT 0, 1";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) {
		$field = mysql_fetch_array($query);
		return floatval($field[0]);
	} else return 0;
}

//	devuelve el nro de dias por derecho pediente del empleado
function DIAS_POR_DERECHO_PENDIENTE() {
	global $_ARGS;
	global $_PARAMETRO;
	##	tabla de disrute
	$sql = "SELECT * FROM rh_vacaciontabla";
	$query_periodos = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	while ($field_periodos = mysql_fetch_array($query_periodos)) {
		$id = $field_periodos['NroAnio'];
		$_DISFRUTES[$id] = $field_periodos['DiasDisfrutes'];
		$_ADICIONAL[$id] = $field_periodos['DiasAdicionales'];
	}
	##	obtengo los dias de derecho
	list($AniosAntecedente, $MesesAntecedente, $DiasAntecedente) = getTiempoAntecedente($_ARGS['_PERSONA'], 'S');
	list($AniosOrganismo, $MesesOrganismo, $DiasOrganismo) = getEdad(formatFechaDMA($_ARGS['_FECHA_INGRESO']), formatFechaDMA($_ARGS['_HASTA']));
	if ($_PARAMETRO['VACANTECEDENT'] == "S") {
		$_DiasDisfrutes = $_DISFRUTES[$AniosOrganismo+1+$AniosAntecedente];
		$_DiasAdicionales = $_ADICIONAL[$AniosOrganismo+1];
		$Derecho = $_DiasDisfrutes + $_DiasAdicionales;
	} else $Derecho = $_DISFRUTES[$AniosOrganismo+1] + $_ADICIONAL[$AniosOrganismo+1];
	return intval($Derecho);
}

//	devuelve el monto pediente del empleado
function PROCESOS_PENDIENTES($CodTipoProceso=NULL, $Periodo=NULL) {
	global $_ARGS;
	global $_PARAMETRO;
	##
	$filtro = "";
	if ($CodTipoProceso) $filtro .= " AND CodTipoProceso = '".$CodTipoProceso."'";
	if ($Periodo) $filtro .= " AND Periodo = '".$Periodo."'";
	##	consulto la suma de los procesos pendientes
	$sql = "SELECT SUM(TotalNeto) AS Total
			FROM pr_tiponominaempleado
			WHERE
				CodPersona = '".$_ARGS['_PERSONA']."' AND
				EstadoPago = 'PE'
				$filtro
			GROUP BY CodPersona";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) {
		$field = mysql_fetch_array($query);
		return floatval($field[0]);
	} else return 0;
}

//	devuelve el nro de dias pediente del empleado
function PROCESOS_PENDIENTES_NRO($CodTipoProceso=NULL, $Periodo=NULL) {
	global $_ARGS;
	global $_PARAMETRO;
	##
	$filtro = "";
	if ($CodTipoProceso != "") $filtro .= " AND CodTipoProceso = '".$CodTipoProceso."'";
	if ($Periodo != "") $filtro .= " AND Periodo = '".$CodTipoProceso."'";
	##	consulto la suma de los procesos pendientes
	$sql = "SELECT COUNT(*) AS Nro
			FROM pr_tiponominaempleado
			WHERE
				CodPersona = '".$_ARGS['_PERSONA']."' AND
				EstadoPago = 'PE'
				$filtro
			GROUP BY CodPersona";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) {
		$field = mysql_fetch_array($query);
		return floatval($field[0]);
	} else return 0;
}

//	devuelve el acumulado de antiguedad
function ANTIGUEDAD_ACUMULADA() {
	global $_ARGS;
	global $_PARAMETRO;
	##
	$sql = "SELECT (AcumuladoInicialProv + AcumuladoProv) AS Acumulado
			FROM pr_acumuladofideicomiso
			WHERE CodPersona = '".$_ARGS['_PERSONA']."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) {
		$field = mysql_fetch_array($query);
		return floatval($field[0]);
	} else return 0;
}

//	devuelve el acumulado de antiguedad
function ANTIGUEDAD_ACUMULADA_DIAS() {
	global $_ARGS;
	global $_PARAMETRO;
	##
	$sql = "SELECT (AcumuladoInicialDias + AcumuladoProvDias) AS AcumuladoDias
			FROM pr_acumuladofideicomiso
			WHERE CodPersona = '".$_ARGS['_PERSONA']."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) {
		$field = mysql_fetch_array($query);
		return floatval($field[0]);
	} else return 0;
}

//	devuelve el acumulado de antiguedad
function ANTIGUEDAD_ACUMULADA_COMPLEMENTO_DIAS() {
	global $_ARGS;
	global $_PARAMETRO;
	##
	$sql = "SELECT (AcumuladoDiasAdicionalInicial + AcumuladoDiasAdicional) AS AcumuladoDias
			FROM pr_acumuladofideicomiso
			WHERE CodPersona = '".$_ARGS['_PERSONA']."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) {
		$field = mysql_fetch_array($query);
		return floatval($field[0]);
	} else return 0;
}

//	devuelve el acumulado de antiguedad
function ANTIGUEDAD_ACUMULADA_FRACCION() {
	global $_ARGS;
	global $_PARAMETRO;
	##
	$sql = "SELECT (SUM(Transaccion) + SUM(Complemento)) AS Fraccion
			FROM pr_acumuladofideicomisodetalle
			WHERE
				CodPersona = '".$_ARGS['_PERSONA']."' AND
				FlagFraccionado = 'S'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) {
		$field = mysql_fetch_array($query);
		return floatval($field[0]);
	} else return 0;
}

//	devuelve el acumulado de antiguedad
function ANTIGUEDAD_ACUMULADA_FRACCION_DIAS() {
	global $_ARGS;
	global $_PARAMETRO;
	##
	$sql = "SELECT (SUM(Dias) + SUM(DiasAdicional)) AS FraccionDias
			FROM pr_acumuladofideicomisodetalle
			WHERE
				CodPersona = '".$_ARGS['_PERSONA']."' AND
				FlagFraccionado = 'S'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) {
		$field = mysql_fetch_array($query);
		return floatval($field[0]);
	} else return 0;
}

//	devuelve el acumulado de antiguedad
function FIDEICOMISO_ACUMULADO() {
	global $_ARGS;
	global $_PARAMETRO;
	##
	$sql = "SELECT (AcumuladoInicialFide + AcumuladoFide) AS Acumulado
			FROM pr_acumuladofideicomiso
			WHERE CodPersona = '".$_ARGS['_PERSONA']."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) {
		$field = mysql_fetch_array($query);
		return floatval($field[0]);
	} else return 0;
}

//	devuelve el salario por jubilacion
function SALARIO_JUBILACION() {
	global $_ARGS;
	global $_PARAMETRO;
	##
	$sql = "SELECT MontoJubilacion FROM mastempleado WHERE CodPersona = '".$_ARGS['_PERSONA']."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) {
		$field = mysql_fetch_array($query);
		return floatval($field[0]);
	} else return 0;
}

//	devuelve la tasa de interes de un periodo
function TASA_INTERES($Periodo=NULL) {
	global $_ARGS;
	global $_PARAMETRO;
	##
	if (!$Periodo) $Periodo = $_ARGS['_PERIODO'];
	$sql = "SELECT Porcentaje FROM masttasainteres WHERE Periodo = '".$Periodo."'";
	$Porcentaje = getVar3($sql);
	return floatval($Porcentaje);
}

//	devuelve las diferencias de un periodo
function DIFERENCIAS($CodTipoProceso=NULL) {
	global $_ARGS;
	global $_PARAMETRO;
	##
	if (!$CodTipoProceso) $CodTipoProceso = $_ARGS['_PROCESO'];
	$sql = "SELECT SUM(tnec.Monto) AS Diferencia
			FROM
				pr_tiponominaempleadoconcepto tnec
				INNER JOIN pr_concepto c ON (c.CodConcepto = tnec.CodConcepto)
			WHERE
				tnec.CodTipoNom = '".$_ARGS['_NOMINA']."' AND
				tnec.Periodo = '".$_ARGS['_PERIODO']."' AND
				tnec.CodPersona = '".$_ARGS['_PERSONA']."' AND
				tnec.CodOrganismo = '".$_ARGS['_ORGANISMO']."' AND
				tnec.CodTipoProceso = '".$_ARGS['_PROCESO']."' AND
				c.FlagDiferencia = 'S' AND
				c.Tipo = 'I'
			GROUP BY tnec.CodPersona";
	$Monto = getVar3($sql);
	return floatval($Monto);
}

//	devuelve las diferencias de un periodo
function ULTIMAS_DIFERENCIAS($CodTipoProceso=NULL) {
	global $_ARGS;
	global $_PARAMETRO;
	##	periodo anterior
	if (!$CodTipoProceso) $CodTipoProceso = $_ARGS['_PROCESO'];
	$sql = "SELECT Periodo
			FROM pr_tiponominaempleado
			WHERE
				CodTipoNom = '".$_ARGS['_NOMINA']."' AND
				Periodo < '".$_ARGS['_PERIODO']."' AND
				CodPersona = '".$_ARGS['_PERSONA']."' AND
				CodOrganismo = '".$_ARGS['_ORGANISMO']."' AND
				CodTipoProceso = '".$CodTipoProceso."'
			ORDER BY Periodo DESC
			LIMIT 0, 1";
	$PeriodoAnterior = getVar3($sql);
	##	monto
	$sql = "SELECT SUM(tnec.Monto) AS Diferencia
			FROM
				pr_tiponominaempleadoconcepto tnec
				INNER JOIN pr_concepto c ON (c.CodConcepto = tnec.CodConcepto)
			WHERE
				tnec.CodTipoNom = '".$_ARGS['_NOMINA']."' AND
				tnec.Periodo = '".$PeriodoAnterior."' AND
				tnec.CodPersona = '".$_ARGS['_PERSONA']."' AND
				tnec.CodOrganismo = '".$_ARGS['_ORGANISMO']."' AND
				tnec.CodTipoProceso = '".$CodTipoProceso."' AND
				c.FlagDiferencia = 'S' AND
				c.Tipo = 'I'
			GROUP BY tnec.CodPersona";
	$Monto = getVar3($sql);
	return floatval($Monto);
}

//	devuelve las diferencias de un periodo
function DIFERENCIAS_DIARIA($CodTipoProceso=NULL) {
	global $_ARGS;
	global $_PARAMETRO;
	##
	if (!$CodTipoProceso) $CodTipoProceso = $_ARGS['_PROCESO'];
	$Diferencias = DIFERENCIAS($CodTipoProceso);
	$Diario = round(($Diferencias / 30), 2);
	return floatval($Diario);
}

//	devuelve las diferencias de un periodo
function ULTIMAS_DIFERENCIAS_DIARIA($CodTipoProceso=NULL) {
	global $_ARGS;
	global $_PARAMETRO;
	##
	if (!$CodTipoProceso) $CodTipoProceso = $_ARGS['_PROCESO'];
	$Diferencias = ULTIMAS_DIFERENCIAS($CodTipoProceso);
	$Diario = round(($Diferencias / 30), 2);
	return floatval($Diario);
}

//	obtener los meses por derecho para la bonificacion de fin de año
function MESES_POR_DERECHO() {
	global $_ARGS;
	global $_PARAMETRO;
	##
	list($PeriodoAnio, $PeriodoMes) = explode("-", $_ARGS['_PERIODO']);
	list($Anios, $Meses, $Dias) = getTiempo(formatFechaDMA($_ARGS['_FECHA_INGRESO']), "31-12-".$PeriodoAnio);
	$MesesPorDerecho = $Meses + ($Anios * 12);
	if ($MesesPorDerecho > 12) $MesesPorDerecho = 12;
	return intval($MesesPorDerecho);
}

//	------------------------------------------
//	LISTA DE FUNCIONES ADICIONALES
//	------------------------------------------
//	obtener los valores de los parametros
function PARAMETROS_FORMULA() {
	global $_ARGS;
	global $_PARAMETRO;
	$sql = "SELECT * FROM mastparametros";
	$query_parametro = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	while ($field_parametro = mysql_fetch_array($query_parametro)) {
		$id = "P_".$field_parametro['ParametroClave'];
		$_PARAMETRO[$id] = $field_parametro['ValorParam'];
	}
	return $_PARAMETRO;
}

//	obtener sueldo basico
function SUELDO_BASICO() {
	global $_ARGS;
	global $_PARAMETRO;
	$sum_sueldo = 0;
	$sql = "SELECT
				  en.Fecha,
				  en.FechaHasta,
				  ns.SueldoPromedio AS SueldoBasico,
				  e.Estado,
				  e.Fegreso
			FROM 
				  rh_empleadonivelacion en
				  INNER JOIN mastempleado e ON (en.CodPersona = e.CodPersona)
				  INNER JOIN rh_puestos p ON (en.CodCargo = p.CodCargo)
				  INNER JOIN rh_nivelsalarial ns ON (p.CategoriaCargo = ns.CategoriaCargo AND p.Grado = ns.Grado AND en.Paso = ns.Paso)
			WHERE
				  en.CodOrganismo = '".$_ARGS['_ORGANISMO']."' AND
				  en.CodPersona = '".$_ARGS['_PERSONA']."' AND
				  en.TipoAccion <> 'ET' AND
				  ((en.FechaHasta = '0000-00-00' AND en.Fecha <= '".$_ARGS['_HASTA']."') OR
				   ('".$_ARGS['_DESDE']."' >= en.Fecha AND
				    '".$_ARGS['_DESDE']."' <= en.FechaHasta))
			ORDER BY en.Fecha";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	while ($field = mysql_fetch_array($query)) {
		if ($field['Fecha'] < $_ARGS['_DESDE']) $desde = $_ARGS['_DESDE'];
		else $desde = $field['Fecha'];
		if ($field['FechaHasta'] == "0000-00-00" || $field['FechaHasta'] > $_ARGS['_HASTA']) $hasta = $_ARGS['_HASTA'];
		else $hasta = $field['FechaHasta'];
		if ($field['Estado'] == "A") { $dias = DIAS_FECHA($desde, $hasta); }
		else {
			if ($field['FechaHasta'] == "0000-00-00") $dias = DIAS_FECHA($desde, $hasta);
			elseif ($field['Fegreso'] < $_ARGS['_DESDE']) $dias = 0;
			else $dias = DIAS_FECHA($_ARGS['_DESDE'], $field['Fegreso'], 0);
		}
		$monto = round(($field['SueldoBasico'] / $_PARAMETRO['MAXDIASMES'] * $dias), 2);
		$sum_sueldo += $monto;
	}
	return $sum_sueldo;
}

//	obtener los dias de sueldo basico
function DIAS_SUELDO_BASICO() {
	global $_ARGS;
	global $_PARAMETRO;
	if ($_ARGS['_ESTADO'] == "A") {
		if ($_ARGS['_FECHA_INGRESO'] < $_ARGS['_DESDE']) return DIAS_FECHA($_ARGS['_DESDE'], $_ARGS['_HASTA']);
		else return DIAS_FECHA($_ARGS['_FECHA_INGRESO'], $_ARGS['_HASTA']);
	} else {
		if ($_ARGS['_FECHA_EGRESO'] < $_ARGS['_DESDE']) return 0;
		else return DIAS_FECHA($_ARGS['_DESDE'], $_ARGS['_FECHA_EGRESO'], 0);
	}
}

//	obtener la fecha desde y hasta del proceso
function FECHA_PROCESO($tabla = 'pr_procesoperiodo') {
	global $_ARGS;
	global $_PARAMETRO;
	$sql = "SELECT
				FechaDesde,
				FechaHasta
			FROM $tabla
			WHERE
				CodOrganismo = '".$_ARGS['_ORGANISMO']."' AND
				CodTipoNom = '".$_ARGS['_NOMINA']."' AND
				Periodo = '".$_ARGS['_PERIODO']."' AND
				CodTipoProceso = '".$_ARGS['_PROCESO']."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	return array($field['FechaDesde'], $field['FechaHasta']);
}

//	obtener total de dias del proceso
function DIAS_PROCESO($tabla = 'pr_procesoperiodo') {
	global $_ARGS;
	global $_PARAMETRO;
	$sql = "SELECT
				FechaDesde,
				FechaHasta
			FROM $tabla
			WHERE
				CodOrganismo = '".$_ARGS['_ORGANISMO']."' AND
				CodTipoNom = '".$_ARGS['_NOMINA']."' AND
				Periodo = '".$_ARGS['_PERIODO']."' AND
				CodTipoProceso = '".$_ARGS['_PROCESO']."' AND
				FlagProcesado = 'N'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	return DIAS_FECHA($field['FechaDesde'], $field['FechaHasta']);
}

//	obtener dias entre dos fechas
function DIAS_FECHA($_DESDE, $_HASTA, $Adic=1) {
	global $_ARGS;
	global $_PARAMETRO;
	$sql = "SELECT DATEDIFF('$_HASTA', '$_DESDE');";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	$field = mysql_fetch_array($query);
	$dias = $field[0] + $Adic;
	if (substr($_HASTA, 5, 5) == "02-28" && $_ARGS['_PROCESO'] != "SE1" && $_ARGS['_PROCESO'] != "SE2" && $_ARGS['_PROCESO'] != "SE3" && $_ARGS['_PROCESO'] != "SE4" && $_ARGS['_PROCESO'] != "SE5") $dias+=2;
	elseif (substr($_HASTA, 5, 5) == "02-29" && $_ARGS['_PROCESO'] != "SE1" && $_ARGS['_PROCESO'] != "SE2" && $_ARGS['_PROCESO'] != "SE3" && $_ARGS['_PROCESO'] != "SE4" && $_ARGS['_PROCESO'] != "SE5") $dias+=1;
	return intval($dias);
}

//	obtener dias del año
function DIAS_ANIO($Periodo=NULL) {
	global $_ARGS;
	global $_PARAMETRO;
	##
	if (!$Periodo) $Periodo = $_ARGS['_PERIODO'];
	$Anio = substr($Periodo, 0, 4);
	$sql = "SELECT DATEDIFF('$Anio-12-31', '$Anio-01-01');";
	$Dias = getVar3($sql);
	return (intval($Dias) + 1);
}

//	devuelve el ultimo sueldo integral del empleado (ultimo periodo anterior)
function SUELDO_INTEGRAL_DIARIO_ULTIMO() {
	global $_ARGS;
	global $_PARAMETRO;
	$sql = "SELECT *
			FROM pr_fideicomisocalculo
			WHERE CodPersona = '".$_ARGS['_PERSONA']."'
			ORDER BY Periodo DESC
			LIMIT 0, 1";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) {
		$field = mysql_fetch_array($query);
		if (($field['DiasMes'] > 30) || ($_ARGS['_MES_PROCESO'] == '02' && $field['DiasMes'] == getDiasMes($_ARGS['_PERIODO']))) {
			$DiasMes = 30;
		} else {
			$DiasMes = $field['DiasMes'];
		}
		$SueldoNormalDiario = round($field['SueldoMensual'] / $DiasMes, 2);
		$SueldoIntegralDiario = $SueldoNormalDiario + $field['AliVac'] + $field['AliFin'];
		return floatval($SueldoIntegralDiario);
	} else return 0;
}

//	devuelve el ultimo sueldo normal del empleado
function SUELDO_NORMAL_ACTUAL() {
	global $_ARGS;
	global $_PARAMETRO;
	$sql = "SELECT TotalIngresos
			FROM pr_tiponominaempleado
			WHERE
				CodPersona = '".$_ARGS['_PERSONA']."' AND
				Periodo = '".$_ARGS['_PERIODO']."' AND
				CodTipoProceso = 'FIN'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) {
		$field = mysql_fetch_array($query);
		return floatval($field['TotalIngresos']);
	} else return 0;
}

//	devuelve el ultimo sueldo normal del empleado
function SUELDO_NORMAL_DIARIO_ACTUAL() {
	global $_ARGS;
	global $_PARAMETRO;
	$Monto = round(SUELDO_NORMAL_ACTUAL() / 30, 2);
	return floatval($Monto);
}

function UNIDAD_TRIBUTARIA($Periodo = NULL) {
	global $_ARGS;
	global $_PARAMETRO;
	if ($Periodo) $filtro = " AND Anio = '".$Periodo."'"; else $filtro = " AND Anio = '".substr($_ARGS['_PERIODO'],0,4)."'";
	$sql = "SELECT Valor FROM mastunidadtributaria WHERE 1 $filtro";
	$Valor = getVar3($sql);
	return floatval($Valor);
}

function DOCUMENTO_ENTREGADO($CodDocumento) {
	global $_ARGS;
	global $_PARAMETRO;
	$sql = "SELECT * 
			FROM rh_empleado_documentos 
			WHERE 
				CodPersona = '".$_ARGS['_PERSONA']."' AND 
				Documento = '".$CodDocumento."'";
	$field_documento = getRecord($sql);
	if (count($field_documento)) return true; else return false;
}

function DIAS_A_DESCONTAR() {
	global $_ARGS;
	global $_PARAMETRO;
	$sql = "SELECT SUM(TotalDias) Total
			FROM rh_permisos 
			WHERE 
				CodPersona = '".$_ARGS['_PERSONA']."' AND 
				FechaDesde >= '".$_ARGS['_DESDE']."' AND 
				FechaHasta <= '".$_ARGS['_HASTA']."' AND 
				FlagRemunerado = 'N'";
	$Total = getVar3($sql);
	return intval($Total);
}

//	obtener la edad de una persona
function EDAD_PERSONA() {
	global $_ARGS;
	global $_PARAMETRO;
	$sql = "SELECT antiguedad('".$_ARGS['_FECHA_NACIMIENTO']."','".$_ARGS['_HASTA']."') AS edad";
	$edad = getVar3($sql);
	list($Anios, $Meses, $Dias) = explode('-', $edad);
	return intval($Anios);
}

function FRACCION_DIAS_COMPLEMENTO() {
	global $_ARGS;
	global $_PARAMETRO;
	##	obtener la antiguedad
	list($Anios, $Meses, $Dias) = getEdad(formatFechaDMA($_ARGS['_FECHA_INGRESO']), formatFechaDMA($_ARGS['_FECHA_EGRESO']));
	if (($Meses >= 6 && $Meses <= 11) && $Anios >= 2) {
		$DiasComplemento = $Anios * 2;
		return intval($DiasComplemento);
	} else return 0;
}

function FRACCION_COMPLEMENTO() {
	global $_ARGS;
	global $_PARAMETRO;
	##	obtener la antiguedad
	list($Anios, $Meses, $Dias) = getEdad(formatFechaDMA($_ARGS['_FECHA_INGRESO']), formatFechaDMA($_ARGS['_FECHA_EGRESO']));
	if (($Meses >= 6 && $Meses <= 11) && $Anios >= 2) {
		$DiasComplemento = $Anios * 2;
		##	obtener los sueldos
		$SueldosAli = 0;
		$sql = "SELECT * FROM pr_fideicomisocalculo WHERE CodPersona = '".$_ARGS['_PERSONA']."' ORDER BY Periodo DESC LIMIT 0, $Meses";
		$field = getRecords($sql);
		foreach ($field as $f) {
			$SueldosAli += $f['SueldoDiarioAli'];
		}
		$Complemento = $SueldosAli / $Meses * $DiasComplemento;
		return floatval(round($Complemento, 2));
	} else return 0;
}

function DIAS_DESCANSO() {
	global $_ARGS;
	global $_PARAMETRO;
	##	
	$sql = "SELECT COUNT(*) FROM pr_complementodias WHERE CodOrganismo = '$_ARGS[_ORGANISMO]' AND CodTipoNom = '$_ARGS[_NOMINA]' AND Periodo = '$_ARGS[_PERIODO]' AND CodTipoProceso = '$_ARGS[_PROCESO]' AND CodPersona = '$_ARGS[_PERSONA]' AND TipoDia = 'DD'";
	$Dias = getVar3($sql);

	return intval($Dias);
}

function DIAS_FERIADOS() {
	global $_ARGS;
	global $_PARAMETRO;
	##	
	$sql = "SELECT COUNT(*) FROM pr_complementodias WHERE CodOrganismo = '$_ARGS[_ORGANISMO]' AND CodTipoNom = '$_ARGS[_NOMINA]' AND Periodo = '$_ARGS[_PERIODO]' AND CodTipoProceso = '$_ARGS[_PROCESO]' AND CodPersona = '$_ARGS[_PERSONA]' AND TipoDia = 'DF'";
	$Dias = getVar3($sql);

	return intval($Dias);
}

function HORAS_EXTRAS_DIURNAS() {
	global $_ARGS;
	global $_PARAMETRO;
	##	
	$sql = "SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(HED))) FROM pr_complementohoras WHERE CodOrganismo = '$_ARGS[_ORGANISMO]' AND CodTipoNom = '$_ARGS[_NOMINA]' AND Periodo = '$_ARGS[_PERIODO]' AND CodTipoProceso = '$_ARGS[_PROCESO]' AND CodPersona = '$_ARGS[_PERSONA]'";
	$Horas = getVar3($sql);
	list($h, $m, $s) = explode(':', $Horas);

	return intval($h);
}

function HORAS_EXTRAS_NOCTURNAS() {
	global $_ARGS;
	global $_PARAMETRO;
	##	
	$sql = "SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(HEN))) FROM pr_complementohoras WHERE CodOrganismo = '$_ARGS[_ORGANISMO]' AND CodTipoNom = '$_ARGS[_NOMINA]' AND Periodo = '$_ARGS[_PERIODO]' AND CodTipoProceso = '$_ARGS[_PROCESO]' AND CodPersona = '$_ARGS[_PERSONA]'";
	$Horas = getVar3($sql);
	list($h, $m, $s) = explode(':', $Horas);

	return intval($h);
}

function DIAS_DISFRUTE_VACACION() {
	global $_ARGS;
	global $_PARAMETRO;
	##	
	list($AniosOrganismo, $MesesOrganismo, $DiasOrganismo) = getEdad(formatFechaDMA($_ARGS['_FECHA_INGRESO']), $_ARGS['_DIA_INGRESO'].'-'.$_ARGS['_MES_INGRESO'].'-'.$_ARGS['_ANO_PROCESO']);
	##	
	$sql = "SELECT DiasDisfrutes FROM rh_vacaciontabla WHERE CodTipoNom = '$_ARGS[_NOMINA]' AND NroAnio = '$AniosOrganismo'";
	$DiasDisfrutes = getVar3($sql);
	##
	if (!$DiasDisfrutes && $AniosOrganismo >= 1) {
		$sql = "SELECT MAX(DiasDisfrutes) FROM rh_vacaciontabla WHERE CodTipoNom = '$_ARGS[_NOMINA]'";
		$DiasDisfrutes = getVar3($sql);
	}
	##	
	return intval($DiasDisfrutes);
}

function RETROACTIVO_PERIODO($Periodo, $CodTipoProceso) {
	global $_ARGS;
	global $_PARAMETRO;
	##	
	$SueldoActual = $_ARGS['_SUELDO_ACTUAL'];
	$SueldoBasico = SUELDO_BASICO_NOMINA($Periodo, $CodTipoProceso);
	if ($SueldoBasico > 0) $Monto = $SueldoActual - $SueldoBasico; else $Monto = 0;
	##	
	return floatval($Monto);
}
?>