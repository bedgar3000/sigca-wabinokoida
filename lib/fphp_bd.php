<?php
/*
 * CONSULTAS A LA BD
 */

//	devuelve el valor de un campo
function getValorCampo($tabla, $codcampo, $nomcampo, $codigo) {
	$sql = "SELECT $nomcampo FROM $tabla WHERE $codcampo = '".$codigo."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	return $field[0];
}

//	devuelve el valor de un campo
function getVar($tabla, $campo, $param1=NULL, $valor1=NULL, $param2=NULL, $valor2=NULL, $param3=NULL, $valor3=NULL) {
	global $__archivo;
	unset($field);
	$filtro = "";
	if ($param1) $filtro .= " AND $param1 = '".$valor1."'";
	if ($param2) $filtro .= " AND $param2 = '".$valor2."'";
	if ($param3) $filtro .= " AND $param3 = '".$valor3."'";
	$sql = "SELECT $campo FROM $tabla WHERE 1 $filtro LIMIT 0, 1";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	//if ($__archivo) fwrite($__archivo, $sql.";\n\n");
	return $field[0];
}

//	devuelve el valor de un campo
function getVar2($tabla, $campo, $campos=NULL, $valores=NULL) {
	global $__archivo;
	unset($field);
	$filtro = "";
	if ($campos) {
		for($i=0;$i<count($campos);$i++) {
			$filtro .= " AND $campos[$i] = '$valores[$i]'";
		}
	}
	$sql = "SELECT $campo FROM $tabla WHERE 1 $filtro LIMIT 0, 1";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	//if ($__archivo) fwrite($__archivo, $sql.";\n\n");
	return $field[0];
}

//	devuelve los valores de una consulta
function getVar3($sql) {
	global $__archivo;
	//if ($__archivo) fwrite($__archivo, $sql.";\n\n");
	unset($field);
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	return $field[0];
}

//	devuelve los valores de una consulta
function getRecord($sql) {
	global $__archivo;
	//if ($__archivo) fwrite($__archivo, $sql.";\n\n");
	unset($field);
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	return $field;
}

//	devuelve los valores de una consulta
function getRecords($sql) {
	global $__archivo;
	//if ($__archivo) fwrite($__archivo, $sql.";\n\n");
	unset($f);
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	while($field = mysql_fetch_array($query)) {
		$f[] = $field;
	}
	return $f;
}

//	ejecuta un query
function execute($sql) {
	global $__archivo;
	if ($__archivo) fwrite($__archivo, $sql.";\n\n");
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	return true;
}

//	devuelve el valor de un campo
function delete($tabla, $campos=NULL, $valores=NULL) {
	$filtro = "";
	if ($campos) {
		for($i=0;$i<count($campos);$i++) {
			$filtro .= " AND $campos[$i] = '$valores[$i]'";
		}
	}
	$sql = "DELETE FROM $tabla WHERE 1 $filtro";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	return $field[0];
}

//	devuelve el valor de un campo
function getNumRows($tabla, $param1=NULL, $valor1=NULL, $param2=NULL, $valor2=NULL, $param3=NULL, $valor3=NULL) {
	$filtro = "";
	if ($param1) $filtro .= " AND $param1 = '".$valor1."'";
	if ($param2) $filtro .= " AND $param2 = '".$valor2."'";
	if ($param3) $filtro .= " AND $param3 = '".$valor3."'";
	$sql = "SELECT * FROM $tabla WHERE 1 $filtro";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	return mysql_num_rows($query);
}

//	devuelve el valor de un campo
function getNumRows2($tabla, $campos=NULL, $valores=NULL) {
	global $__archivo;
	$filtro = "";
	if ($campos) {
		for($i=0;$i<count($campos);$i++) {
			$filtro .= " AND $campos[$i] = '$valores[$i]'";
		}
	}
	$sql = "SELECT * FROM $tabla WHERE 1 $filtro";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	return intval(mysql_num_rows($query));
}

//	devuelve el valor de un campo
function getNumRows3($sql) {
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	return intval(mysql_num_rows($query));
}

//	devuelve las filas que fueron afectadas por una operación
function affected_rows() {
	return intval(mysql_affected_rows());
}

//	IMPRIMIR ERROR DE CONSULTA SQL
function getErrorSql($nroerror, $deterror, $sql) {
	mysql_query("ROLLBACK");
	switch ($nroerror) {
		case "1451":
			$error = "¡ERROR: Registro enlazado a otra tabla!: <br />".$deterror;
			break;
			
		case "1062":
			$deterror = '';
			//$sql = '';
			$error = "¡ERROR: Registro Existe!: <br />".$sql." <br />".$deterror;
			break;
			
		case "1064":
			$error = "Error de sintaxis: <br />".$sql." <br />".$deterror;
			break;
			
		case "1644":
			$error = "Error: ".$nroerror." <br />".$deterror;
			break;
			
		default:
			$error = "Error: ".$nroerror." <br />".$sql." <br />".$deterror;
			break;
	}
	return $error;
}
?>