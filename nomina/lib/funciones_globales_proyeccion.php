<?php
//	sueldo mensual
function SUELDO_BASICO() {
	global $_ARGS;
	global $_PARAMETRO;
	$SueldoBasico = 0;

	//	consulto si existe un ajuste por grado
	$sql = "SELECT *
			FROM pr_proyajustegrado
			WHERE
				CodOrganismo = '$_ARGS[CodOrganismo]' AND
				Ejercicio = '$_ARGS[Ejercicio]'";
	$field_ajuste_grado = getRecords($sql);
	foreach ($field_ajuste_grado as $f) {
		$sql = "SELECT pagd.SueldoTotal
				FROM
					pr_proyrecursosdet prd
					INNER JOIN pr_proyajustegradodet pagd ON (pagd.CategoriaCargo = prd.CategoriaCargo AND
															  pagd.Grado = prd.Grado AND
															  pagd.Paso = prd.Paso)
				WHERE
					prd.CodRecurso = '$_ARGS[CodRecurso]' AND
					prd.Secuencia = '$_ARGS[Secuencia]' AND
					pagd.CodAjuste = '$f[CodAjuste]'";
		$Resultado = getVar3($sql);
		if ($Resultado) $SueldoBasico = $Resultado;
	}

	return floatval($SueldoBasico);
}

//	total asignaciones
function TOTAL_ASIGNACIONES() {
	global $_ARGS;
	global $_PARAMETRO;

	##	obtengo parametro
	$sql = "SELECT pr.CodParametro 
			FROM
				pr_proyparametro pr
				INNER JOIN pr_proyrecursos rc ON (rc.CodRecurso = pr.CodRecurso)
			WHERE
				pr.Ejercicio = '$_ARGS[Ejercicio]' AND
				rc.CodTipoNom = '$_ARGS[CodTipoNom]' AND 
				pr.CodTipoProceso = 'FIN'";
	$CodParametro = getVar3($sql);

	##	obtengo el monto
	$sql = "SELECT SUM(Monto)
			FROM
				pr_proyejecucion e
				INNER JOIN pr_concepto c On (c.CodConcepto = e.CodConcepto)
			WHERE
				e.CodParametro = '$CodParametro' AND
				e.CodRecurso = '$_ARGS[CodRecurso]' AND
				e.Secuencia = '$_ARGS[Secuencia]' AND
				c.Tipo = 'I'";
	$Resultado = getVar3($sql);

	return floatval($Resultado);
}

//	total asignaciones
function TOTAL_ASIGNACIONES_DIARIA() {
	global $_ARGS;
	global $_PARAMETRO;

	$Resultado = round((TOTAL_ASIGNACIONES() / 30), 2);

	return floatval($Resultado);
}

//	total asignaciones
function ALICUOTA_VACACIONAL($DiasVac = 105) {
	global $_ARGS;
	global $_PARAMETRO;

	$TOTAL_ASIGNACIONES_DIARIA = TOTAL_ASIGNACIONES_DIARIA();
	$Resultado = round(($TOTAL_ASIGNACIONES_DIARIA * $DiasVac / 360), 2);

	return floatval($Resultado);
}

//	total asignaciones
function ALICUOTA_FIN($DiasVac = 105, $DiasFin = 150) {
	global $_ARGS;
	global $_PARAMETRO;

	$TOTAL_ASIGNACIONES = TOTAL_ASIGNACIONES();
	$ALICUOTA_VACACIONAL = ALICUOTA_VACACIONAL($DiasVac);
	$Resultado = round((($TOTAL_ASIGNACIONES * $DiasFin / 360) + $ALICUOTA_VACACIONAL), 2);

	return floatval($Resultado);
}

//	obtener nro. de hijos
function HIJOS($edad=NULL) {
	global $_ARGS;
	global $_PARAMETRO;
	$filtro = "";
	$Hijos = 0;

	if (($_ARGS['FlagEjecucion'] == 'S' && $_ARGS['FlagParametrizable'] == 'N') || ($_ARGS['FlagEjecucion'] == 'N' && $_ARGS['FlagParametrizable'] == 'S')) {
		if ($edad) {
			$filtro .= " AND (antiguedad_anios(FechaNacimiento, '".$_ARGS["Ejercicio"]."-12-31')) < ".intval($edad);
		}
		//	consulto
		$sql = "SELECT COUNT(*)
				FROM rh_cargafamiliar
				WHERE
					CodPersona = '".$_ARGS['CodPersona']."' AND
					Parentesco = 'HI' $filtro";
		$Hijos = getVar3($sql);
	} else {
		$sql = "SELECT Valor
				FROM pr_proyrecursosparametros
				WHERE
					CodParametro = '$_ARGS[CodParametro]' AND
					CodConcepto = '$_ARGS[CodConcepto]' AND
					CodRecurso = '$_ARGS[CodRecurso]' AND
					Secuencia = '$_ARGS[Secuencia]'";
		$Hijos = getVar3($sql);
	}
	return intval($Hijos);
}

//	obtener numero de cursos
function CURSOS() {
	global $_ARGS;
	global $_PARAMETRO;

	$sql = "SELECT *
			FROM rh_empleado_cursos
			WHERE
				CodPersona = '".$_ARGS['CodPersona']."' AND
				FechaCulminacion <= '".$_ARGS['Ejercicio']."-12-31' AND
				FlagPago = 'S'";
	$field = getRecords($sql);

	return count($field);
}

//	devuelve si es universitario
function UNIVERSITARIO() {
	global $_ARGS;
	global $_PARAMETRO;

	$sql = "SELECT *
			FROM rh_empleado_instruccion
			WHERE
				CodPersona = '".$_ARGS['CodPersona']."' AND
				CodGradoInstruccion = 'UNI'";
	$field = getRecords($sql);

	if (count($field)) return true; else return false;
}

//	devuelve si es tsu
function TSU() {
	global $_ARGS;
	global $_PARAMETRO;

	$sql = "SELECT *
			FROM rh_empleado_instruccion
			WHERE
				CodPersona = '".$_ARGS['CodPersona']."' AND
				CodGradoInstruccion = 'TSU'";
	$field = getRecords($sql);

	if (count($field)) return true; else return false;
}

//	devuelve si el empleado tiene una especializacion
function ESPECIALIZACION() {
	global $_ARGS;
	global $_PARAMETRO;

	$sql = "SELECT *
			FROM rh_empleado_instruccion
			WHERE
				CodPersona = '".$_ARGS['CodPersona']."' AND
				CodGradoInstruccion = 'POS' AND
				Nivel = '01'";
	$field = getRecords($sql);

	if (count($field)) return true; else return false;
}

//	devuelve si el empleado tiene un magister
function MAGISTER() {
	global $_ARGS;
	global $_PARAMETRO;

	$sql = "SELECT *
			FROM rh_empleado_instruccion
			WHERE
				CodPersona = '".$_ARGS['CodPersona']."' AND
				CodGradoInstruccion = 'POS' AND
				Nivel = '02'";
	$field = getRecords($sql);

	if (count($field)) return true; else return false;
}

//	devuelve si el empleado tiene un doctorado
function DOCTORADO() {
	global $_ARGS;
	global $_PARAMETRO;

	$sql = "SELECT *
			FROM rh_empleado_instruccion
			WHERE
				CodPersona = '".$_ARGS['CodPersona']."' AND
				CodGradoInstruccion = 'POS' AND
				Nivel = '03'";
	$field = getRecords($sql);

	if (count($field)) return true; else return false;
}

//	devuelve si el empleado ocupa un cargo titular de jefatura
function JERARQUIA() {
	global $_ARGS;
	global $_PARAMETRO;

	$sql = "SELECT
				pt1.Grado,
				pt2.Grado AS GradoTemp
			FROM
				mastempleado e
				INNER JOIN rh_puestos pt1 ON (e.CodCargo = pt1.CodCargo)
				LEFT JOIN rh_puestos pt2 ON (e.CodCargoTemp = pt2.CodCargo)
			WHERE
				e.CodPersona = '".$_ARGS['CodPersona']."'";
	$field = getRecord($sql);

	if (($field['Grado'] >= '90' && $field['Grado'] <= '99') || ($field['GradoTemp'] >= '90' && $field['GradoTemp'] <= '99')) return true; else return false;
}

//	
function JERARQUIA_MONTO() {
	global $_ARGS;
	global $_PARAMETRO;

	$Valor = '';

	if ($_ARGS['FlagEjecucion'] == 'S' && $_ARGS['FlagParametrizable'] == 'S') {
		$sql = "SELECT Valor
				FROM pr_proyrecursosparametros
				WHERE
					CodParametro = '$_ARGS[CodParametro]' AND
					CodConcepto = '$_ARGS[CodConcepto]' AND
					CodRecurso = '$_ARGS[CodRecurso]' AND
					Secuencia = '$_ARGS[Secuencia]'";
		$Valor = getVar3($sql);
	}
	
	return $Valor;
}

//	obtener años de servicio
function ANIOS_DE_SERVICIO() {
	global $_ARGS;
	global $_PARAMETRO;

	//$sql = "SELECT antiguedad('".$_ARGS['_FECHA_INGRESO']."','".$_ARGS['_HASTA']."') AS tiempo";
	//$tiempo = getVar3($sql);
	//list($AniosOrganismo, $MesesOrganismo, $DiasOrganismo) = explode('-', $tiempo);
	//die(print_r($_ARGS));
	list($AniosOrganismo, $MesesOrganismo, $DiasOrganismo) = getEdad(formatFechaDMA($_ARGS['FechaIngreso']), formatFechaDMA($_ARGS['Ejercicio'].'-12-31'));
	//if ($AniosOrganismo >= 1) list($AniosAntecedente, $MesesAntecedente, $DiasAntecedente) = getTiempoAntecedente($_ARGS['_PERSONA'], 'S');
	//else {
		$AniosAntecedente = 0; 
		$MesesAntecedente = 0; 
		$DiasAntecedente = 0;
	//}
	list($AniosServicio, $MesesServicio, $DiasServicio) = totalTiempo($AniosAntecedente+$AniosOrganismo, $MesesAntecedente+$MesesOrganismo, $DiasAntecedente+$DiasOrganismo);
	return intval($AniosServicio);
}
?>