<?php
set_time_limit(-1);
ini_set('error_reporting', 'E_ERROR');
ini_set('memory_limit','128M');
session_start();
//	----------------------
extract($_POST);
extract($_GET);
include("conexion.php");
connect();
nocache();
$_PARAMETRO = parametros();
$Ahora = ahora();
list($FechaActual, $HoraActual) = explode(" ", $Ahora);
list($AnioActual, $MesActual, $DiaActual) = explode("-", $FechaActual);
$PeriodoActual = "$AnioActual-$MesActual";
list($HorasActual, $MinutosActual, $SegundosActual) = explode(":", $HoraActual);
//	----------------------
include("fphp_consultas.php");
include("fphp_selectores.php");
include("fphp_bd.php");
include("fphp_fechas.php");
include("fphp_validation.php");

//	limpiar cache
function nocache() {
	header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
}

// FUNCION PARA OBTENER LOS PARAMETROS DEL SISTEMA
function parametros() {
	global $_APLICACION;
	$sql = "SELECT * FROM mastparametros WHERE Estado = 'A'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	while ($field = mysql_fetch_array($query)) {
		$id = $field['ParametroClave'];
		$_PARAMETRO[$id] = $field['ValorParam'];
	}
	return $_PARAMETRO;
}

//	FUNCION PARA GENERAR UN NUEVO CODIGO
function getCodigo($tabla, $campo, $digitos, $campo2=NULL, $valor2=NULL, $campo3=NULL, $valor3=NULL, $campo4=NULL, $valor4=NULL, $campo5=NULL, $valor5=NULL) {
	$filtro = "";
	if ($campo2) $filtro .= " AND $campo2='".$valor2."'";
	if ($campo3) $filtro .= " AND $campo3='".$valor3."'";
	if ($campo4) $filtro .= " AND $campo4='".$valor4."'";
	if ($campo5) $filtro .= " AND $campo5='".$valor5."'";
	$sql="SELECT MAX($campo) FROM $tabla WHERE 1 $filtro";
	$query=mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	$field=mysql_fetch_array($query);
	$codigo=(int) ($field[0]+1);
	$codigo=(string) str_repeat("0", $digitos-strlen($codigo)).$codigo;
	return ($codigo);
}

//	FUNCION PARA GENERAR UN NUEVO CODIGO
function codigo($tabla, $campo, $digitos, $campos=NULL, $valores=NULL) {
	$filtro = "";
	if ($campos) {
		for($i=0;$i<count($campos);$i++) {
			$filtro .= " AND $campos[$i] = '$valores[$i]'";
		}
	}
	$sql = "SELECT MAX($campo) FROM $tabla WHERE 1 $filtro";
	$max = getVar3($sql);
	$codigo = intval($max) + 1;
	$codigo = (string) str_repeat("0", $digitos-strlen($codigo)).$codigo;
	return $codigo;
}

//	FUNCION PARA GENERAR UN NUEVO CODIGO
function getCodigo_2($tabla, $campo, $correlativo, $valor, $digitos) {
	$sql="select max($campo) FROM $tabla WHERE $correlativo = '$valor'";
	$query=mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	$field=mysql_fetch_array($query);
	$codigo=(int) ($field[0]+1);
	$codigo=(string) str_repeat("0", $digitos-strlen($codigo)).$codigo;
	return ($codigo);
}

//	FUNCION PARA GENERAR UN NUEVO CODIGO
function getCodigo_3($tabla, $campo, $correlativo1, $correlativo2, $valor1, $valor2, $digitos) {
	$sql="select max($campo) FROM $tabla WHERE $correlativo1 = '$valor1' AND $correlativo2 = '$valor2'";
	$query=mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	$field=mysql_fetch_array($query);
	$codigo=(int) ($field[0]+1);
	$codigo=(string) str_repeat("0", $digitos-strlen($codigo)).$codigo;
	return ($codigo);
}

//
function getCorrelativoSecuencia_2($tabla, $campo1, $campo2, $valor1, $valor2) {
	$sql = "SELECT $campo1, $campo2 FROM $tabla WHERE $campo1 = '$valor1' AND $campo2 = '$valor2'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	$rows = mysql_num_rows($query);
	return ++$rows;
}

//	FUNCION PARA IMPRIMIR EN UNA TABLA VALORES
function printValoresGeneral($tabla, $codigo) {
	switch ($tabla) {
		case "ESTADO":
			$c[] = "A"; $v[] = "Activo";
			$c[] = "I"; $v[] = "Inactivo";
			break;

		case "ESTADO-PERMISOS":
			$c[] = "P"; $v[] = "Pendiente";
			$c[] = "A"; $v[] = "Aprobado";
			$c[] = "N"; $v[] = "Anulado";
			break;

		case "SEXO":
			$c[] = "M"; $v[] = "Masculino";
			$c[] = "F"; $v[] = "Femenino";
			break;

		case "NACIONALIDAD":
			$c[] = "N"; $v[] = "Nacional";
			$c[] = "E"; $v[] = "Extranjero";
			break;

		case "ESTADO-OBLIGACIONES":
			$c[] = "PR"; $v[] = "En Preparacion";
			$c[] = "RV"; $v[] = "Revisada";
			$c[] = "AP"; $v[] = "Aprobada";
			$c[] = "AN"; $v[] = "Anulada";
			$c[] = "PA"; $v[] = "Pagada";
			break;

		case "TIPO-CAMPO":
			$c[] = "T"; $v[] = "Texto";
			$c[] = "N"; $v[] = "Número";
			$c[] = "F"; $v[] = "Fecha";
			break;

		case "TIPO-SALDO":
			$c[] = "D"; $v[] = "Deudora";
			$c[] = "A"; $v[] = "Acreedora";
			break;

		case "PRIORIDAD":
			$c[] = "N"; $v[] = "Normal";
			$c[] = "U"; $v[] = "Urgente";
			$c[] = "M"; $v[] = "Muy Urgente";
			break;

		case "PROVISIONAR":
			$c[] = "P"; $v[] = "Pago del Documento";
			$c[] = "N"; $v[] = "Provisión del Documento";
			break;

		case "IMPONIBLE":
			$c[] = "N"; $v[] = "Monto Afecto";
			$c[] = "I"; $v[] = "Monto IGV/IVA";
			break;

		case "IMPUESTO-PROVISION":
			$c[] = "N"; $v[] = "Provisión del Documento";
			$c[] = "P"; $v[] = "Pago del Documento";
			break;

		case "IMPUESTO-IMPONIBLE":
			$c[] = "N"; $v[] = "Monto Afecto";
			$c[] = "B"; $v[] = "Monto Bruto";
			$c[] = "I"; $v[] = "IGV/IVA";
			$c[] = "T"; $v[] = "Monto Total";
			break;

		case "IMPUESTO-COMPROBANTE":
			$c[] = "IVA"; $v[] = "IVA";
			$c[] = "ISLR"; $v[] = "ISLR";
			$c[] = "1X1000"; $v[] = "1X1000";
			$c[] = "OTRO"; $v[] = "OTRO";
			break;

		case "SIGNO":
			$c[] = "P"; $v[] = "+";
			$c[] = "N"; $v[] = "-";
			break;

		case "ESTADO-DOCUMENTOS":
			$c[] = "PR"; $v[] = "Pendiente";
			$c[] = "RV"; $v[] = "Facturado";
			break;

		case "FLAG-CONTABILIZADO":
			$c[] = "N"; $v[] = "Si";
			$c[] = "S"; $v[] = "No";
			break;

		case "DIRIGIDO":
			$c[] = "C"; $v[] = "Compras";
			$c[] = "A"; $v[] = "Almacen";
			break;

		case "ESTADO-COMPRA":
			$c[] = "PR"; $v[] = "En Preparacion";
			$c[] = "RV"; $v[] = "Revisada";
			$c[] = "AP"; $v[] = "Aprobada";
			$c[] = "AN"; $v[] = "Anulada";
			$c[] = "RE"; $v[] = "Rechazada";
			$c[] = "CE"; $v[] = "Cerrada";
			$c[] = "CO"; $v[] = "Completada";
			break;

		case "ESTADO-COMPRA-DETALLE":
			$c[] = "PR"; $v[] = "En Preparacion";
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "RE"; $v[] = "Rechazada";
			$c[] = "CE"; $v[] = "Cerrada";
			$c[] = "AN"; $v[] = "Anulada";
			$c[] = "CO"; $v[] = "Completada";
			break;

		case "ESTADO-SERVICIO":
			$c[] = "PR"; $v[] = "En Preparacion";
			$c[] = "RV"; $v[] = "Revisada";
			$c[] = "AP"; $v[] = "Aprobada";
			$c[] = "AN"; $v[] = "Anulada";
			$c[] = "RE"; $v[] = "Rechazada";
			$c[] = "CE"; $v[] = "Cerrada";
			$c[] = "CO"; $v[] = "Completada";
			break;

		case "ESTADO-SERVICIO-DETALLE":
			$c[] = "N"; $v[] = "Pendiente";
			$c[] = "S"; $v[] = "Completado";
			break;

		case "TIPO-MOVIMIENTO-TRANSACCION":
			$c[] = "I"; $v[] = "Ingreso";
			$c[] = "E"; $v[] = "Egreso";
			$c[] = "T"; $v[] = "Transferencia";
			break;

		case "ESTADO-REQUERIMIENTO-DETALLE":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "AN"; $v[] = "Anulado";
			$c[] = "CE"; $v[] = "Cerrado";
			$c[] = "CO"; $v[] = "Completado";
			break;

		case "FLAG":
			$c[] = "S"; $v[] = "Si";
			$c[] = "N"; $v[] = "No";
			break;

		case "DIA-SEMANA":
			$c[] = "1"; $v[] = "Lunes";
			$c[] = "2"; $v[] = "Martes";
			$c[] = "3"; $v[] = "Miercoles";
			$c[] = "4"; $v[] = "Jueves";
			$c[] = "5"; $v[] = "Viernes";
			$c[] = "6"; $v[] = "Sabado";
			$c[] = "7"; $v[] = "Domingo";
			break;

		case "TIPO-TRANSACCION-BANCARIA":
			$c[] = "I"; $v[] = "Ingreso";
			$c[] = "E"; $v[] = "Egreso";
			$c[] = "T"; $v[] = "Transacción";
			break;

		case "CONCEPTO-TIPO":
			$c[] = "I"; $v[] = "Ingresos";
			$c[] = "D"; $v[] = "Descuentos";
			$c[] = "A"; $v[] = "Aportes";
			$c[] = "P"; $v[] = "Provisiones";
			$c[] = "T"; $v[] = "Totales";
			break;

		case "ESTADO-ACTIVO":
			$c[] = "PE"; $v[] = "Pendiente de Activar";
			$c[] = "AP"; $v[] = "Activado";
			break;

		case "PODER-PUBLICO":
			$c[] = "N"; $v[] = "Nacional";
			$c[] = "E"; $v[] = "Estadal";
			$c[] = "M"; $v[] = "Municipal";
			break;

		case "ESTADO-ACTUACION":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "RV"; $v[] = "Revisada";
			$c[] = "AP"; $v[] = "Aprobada";
			$c[] = "TE"; $v[] = "Terminada";
			$c[] = "CO"; $v[] = "Completada";
			$c[] = "AN"; $v[] = "Anulada";
			$c[] = "CE"; $v[] = "Cerrada";
			$c[] = "EV"; $v[] = "Enviado CGR";
			break;

		case "ESTADO-VALORACION":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "RV"; $v[] = "Revisada";
			$c[] = "AP"; $v[] = "Aprobada";
			$c[] = "AC"; $v[] = "Auto de Proceder";
			$c[] = "AA"; $v[] = "Auto de Archivo";
			$c[] = "AN"; $v[] = "Anulada";
			$c[] = "CE"; $v[] = "Cerrada";
			$c[] = "EV"; $v[] = "Enviado CGR";
			$c[] = "VJ"; $v[] = "Enviado VJPA";
			break;

		case "ESTADO-POTESTAD":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "RV"; $v[] = "Revisada";
			$c[] = "AP"; $v[] = "Aprobada";
			$c[] = "TE"; $v[] = "Terminada";
			$c[] = "CO"; $v[] = "Completada";
			$c[] = "AN"; $v[] = "Anulada";
			$c[] = "CE"; $v[] = "Cerrada";
			$c[] = "EV"; $v[] = "Enviado CGR";
			$c[] = "AA"; $v[] = "Auto de Archivo";
			break;

		case "ESTADO-DETERMINACION-VALORACION":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "RV"; $v[] = "Revisada";
			$c[] = "AP"; $v[] = "Aprobada";
			$c[] = "AC"; $v[] = "Auto de Proceder";
			$c[] = "AA"; $v[] = "Auto de Archivo";
			$c[] = "AN"; $v[] = "Anulada";
			$c[] = "CE"; $v[] = "Cerrada";
			$c[] = "EV"; $v[] = "Enviado CGR";
			$c[] = "DV"; $v[] = "Devuelto";
			break;

		case "ESTADO-DETERMINACION":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "RV"; $v[] = "Revisada";
			$c[] = "AP"; $v[] = "Aprobada";
			$c[] = "TE"; $v[] = "Terminada";
			$c[] = "CO"; $v[] = "Completada";
			$c[] = "AN"; $v[] = "Anulada";
			$c[] = "CE"; $v[] = "Cerrada";
			$c[] = "EV"; $v[] = "Enviado CGR";
			break;

		case "ACTUALIZAR-PERSONA":
			$c[] = "Persona"; $v[] = "Persona";
			$c[] = "Empleado"; $v[] = "Empleado";
			$c[] = "Proveedor"; $v[] = "Proveedor";
			$c[] = "Cliente"; $v[] = "Cliente";
			$c[] = "Otro"; $v[] = "Otro";
			break;

		case "TIPO-PERSONA":
			$c[] = "N"; $v[] = "Natural";
			$c[] = "J"; $v[] = "Jurídica";
			break;

		case "CLASE-PERSONA":
			$c[] = "EsEmpleado"; $v[] = "Empleado";
			$c[] = "EsProveedor"; $v[] = "Proveedor";
			$c[] = "EsCliente"; $v[] = "Cliente";
			$c[] = "EsOtro"; $v[] = "Otro";
			break;

		case "presupuesto-estado":
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "forma-evaluacion-poa":
			$c[] = "C"; $v[] = "Cantidad";
			$c[] = "P"; $v[] = "Porcentual";
			break;

		case "poa-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "RV"; $v[] = "Revisado";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "presupuesto-hacienda-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "plan-obras-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "GE"; $v[] = "Generado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "plan-obras-tipo":
			$c[] = "PU"; $v[] = "Dominio Público";
			$c[] = "PR"; $v[] = "Dominio Privado";
			break;

		case "plan-obras-situacion":
			$c[] = "AI"; $v[] = "A Iniciar";
			$c[] = "EJ"; $v[] = "En Ejecución";
			$c[] = "TE"; $v[] = "Terminado";
			$c[] = "PA"; $v[] = "Paralizado";
			break;

		case "ESTADO-COMPRA-DETALLE":
			$c[] = "PR"; $v[] = "En Preparacion";
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "RE"; $v[] = "Rechazada";
			$c[] = "CE"; $v[] = "Cerrada";
			$c[] = "CO"; $v[] = "Completada";
			break;

		case "obras-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "GE"; $v[] = "Generado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "obras-situacion":
			$c[] = "AI"; $v[] = "A Iniciar";
			$c[] = "EJ"; $v[] = "En Ejecución";
			$c[] = "TE"; $v[] = "Terminado";
			$c[] = "PA"; $v[] = "Paralizado";
			break;

		case "contribuyente-estado":
			$c[] = "AC"; $v[] = "Activo";
			$c[] = "SP"; $v[] = "Suspendido";
			$c[] = "CA"; $v[] = "Cancelado";
			$c[] = "CE"; $v[] = "Cesado";
			break;

		case "cliente-clasificacion":
			$c[] = "E"; $v[] = "Excelente";
			$c[] = "B"; $v[] = "Bueno";
			$c[] = "R"; $v[] = "Regular";
			$c[] = "M"; $v[] = "Malo";
			break;

		case "monedas":
			$c[] = "L"; $v[] = "Local";
			break;
			
		case "ESTADO-TRANSACCION":
			$c[] = "PR"; $v[] = "Pendiente";
			$c[] = "CO"; $v[] = "Ejecutado";
			$c[] = "AN"; $v[] = "Anulado";
			break;
			
		case "IMPUESTO-COMPROBANTE":
			$c[] = "IVA"; $v[] = "IVA";
			$c[] = "ISLR"; $v[] = "ISLR";
			$c[] = "1X1000"; $v[] = "1X1000";
			$c[] = "OTRO"; $v[] = "OTRO";
			break;

		case "co-documento-pagos":
			$c[] = "PP"; $v[] = "Adelantos Pendientes de Pago";
			$c[] = "PA"; $v[] = "Pagados";
			break;

		case "co-documento1-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "FA"; $v[] = "Facturado";
			$c[] = "AN"; $v[] = "Anulado";
			$c[] = "RE"; $v[] = "Rechazado";
			break;

		case "co-documento1-estado-detalle":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "FA"; $v[] = "Facturado";
			$c[] = "AN"; $v[] = "Anulado";
			$c[] = "RE"; $v[] = "Rechazado";
			break;

		case "co-documento2-estado":
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "CA"; $v[] = "Canjeado";
			$c[] = "CO"; $v[] = "Cobrado";
			$c[] = "AN"; $v[] = "Anulado";
			$c[] = "CT"; $v[] = "Castigado";
			break;

		case "co-documento2-estado-detalle":
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "CA"; $v[] = "Canjeado";
			$c[] = "CO"; $v[] = "Cobrado";
			$c[] = "AN"; $v[] = "Anulado";
			$c[] = "CT"; $v[] = "Castigado";
			break;

		case "co-documento3-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "FA"; $v[] = "Facturado";
			$c[] = "AN"; $v[] = "Anulado";
			$c[] = "RE"; $v[] = "Rechazado";
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "CA"; $v[] = "Canjeado";
			$c[] = "CO"; $v[] = "Cobrado";
			$c[] = "AN"; $v[] = "Anulado";
			$c[] = "CT"; $v[] = "Castigado";
			break;
			
		case "adelanto-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "GE"; $v[] = "Generado";
			$c[] = "PA"; $v[] = "Pagado";
			$c[] = "AC"; $v[] = "Aplicado";
			$c[] = "AN"; $v[] = "Anulado";
			break;
			
		case "adelanto-tipo":
			$c[] = "P"; $v[] = "Proveedor";
			$c[] = "C"; $v[] = "Contratista";
			break;
	}

	$i=0;
	foreach ($c as $cod) {
		if ($cod == $codigo) return $v[$i];
		$i++;
	}
}

//	FUNCION PARA IMPRIMIR UN CHECK
function printFlag($check) {
	if (file_exists("../imagenes/checked.png")) $path = "../imagenes/checked.png";
	elseif (file_exists("../../imagenes/checked.png")) $path = "../../imagenes/checked.png";
	if ($check == "S" || $check == "X" || $check == "1") $flag = "<img src='$path' width='12' height='12' />";
	return $flag;
}

//	FUNCION PARA IMPRIMIR UN CHECK
function printFlag2($check) {
	if ($check == "S" || $check == "X") {
		if (file_exists("../imagenes/checked.png")) $path = "../imagenes/checked.png";
		elseif (file_exists("../../imagenes/checked.png")) $path = "../../imagenes/checked.png";

		$flag = "<img src='$path' width='16' height='16' />";
	}
	return $flag;
}

//	FUNCION PARA IMPRIMIR UN CHECK
function printFlagEstado($check) {
	if ($check == "A") $flag = "<img src='../imagenes/arriba.png' width='20' height='20' />";
	elseif ($check == "I") $flag = "<img src='../imagenes/abajo.png' width='20' height='20' />";
	return $flag;
}

//	FUNCION PARA IMPRIMIR UN CHECK
function printSigno($signo) {
	if ($signo == "P") {
		if (file_exists("../imagenes/positivo.png")) $flag = "<img src='../imagenes/positivo.png' width='16' height='16' />";
		else $flag = "<img src='../../imagenes/positivo.png' width='16' height='16' />";
	}
	elseif ($signo == "N") {
		if (file_exists("../imagenes/negativo.png")) $flag = "<img src='../imagenes/negativo.png' width='16' height='16' />";
		else $flag = "<img src='../../imagenes/negativo.png' width='16' height='16' />";
	}
	return $flag;
}

//	funcion para convertir un numero formateado en su valor real
function setNumero($num) {
	$num = str_replace(".", "", $num);
	$num = str_replace(",", ".", $num);
	$numero = floatval($num);
	return $numero;
}

//	funcion para convertir un numero formateado en su valor real
function getNumero($num) {
	$num = str_replace(".", "", $num);
	$num = str_replace(",", ".", $num);
	$numero = ($num);
	return $numero;
}

//	funcion para redondear un numero con decimales
function redondeo($VALOR, $DECIMALES) {
	$ceros = (string) str_repeat("0", $DECIMALES);
	$unidad = "1".$ceros;
	$unidad = intval($unidad);
	$multiplicamos = $VALOR * $unidad;
	list($parte_entera, $numero_redondeo) = split('[.]', $multiplicamos);
	$numero_redondeo = substr($numero_redondeo, 0, 1);
	if ($numero_redondeo >= 5) $parte_entera++;
	$resultado = $parte_entera / $unidad;
	return $resultado;
}

// FUNCIONES DE CONVERSION DE NUMEROS A LETRAS.
function num2letras($num, $fem = true, $dec = true) {
//if (strlen($num) > 14) die("El número introducido es demasiado grande");
   $matuni[2]  = "dos";
   $matuni[3]  = "tres";
   $matuni[4]  = "cuatro";
   $matuni[5]  = "cinco";
   $matuni[6]  = "seis";
   $matuni[7]  = "siete";
   $matuni[8]  = "ocho";
   $matuni[9]  = "nueve";
   $matuni[10] = "diez";
   $matuni[11] = "once";
   $matuni[12] = "doce";
   $matuni[13] = "trece";
   $matuni[14] = "catorce";
   $matuni[15] = "quince";
   $matuni[16] = "dieciseis";
   $matuni[17] = "diecisiete";
   $matuni[18] = "dieciocho";
   $matuni[19] = "diecinueve";
   $matuni[20] = "veinte";
   $matunisub[2] = "dos";
   $matunisub[3] = "tres";
   $matunisub[4] = "cuatro";
   $matunisub[5] = "quin";
   $matunisub[6] = "seis";
   $matunisub[7] = "sete";
   $matunisub[8] = "ocho";
   $matunisub[9] = "nove";
   $matdec[2] = "veint";
   $matdec[3] = "treinta";
   $matdec[4] = "cuarenta";
   $matdec[5] = "cincuenta";
   $matdec[6] = "sesenta";
   $matdec[7] = "setenta";
   $matdec[8] = "ochenta";
   $matdec[9] = "noventa";
   $matsub[3]  = "mill";
   $matsub[5]  = "bill";
   $matsub[7]  = "mill";
   $matsub[9]  = "trill";
   $matsub[11] = "mill";
   $matsub[13] = "bill";
   $matsub[15] = "mill";
   $matmil[4]  = "millones";
   $matmil[6]  = "billones";
   $matmil[7]  = "de billones";
   $matmil[8]  = "millones de billones";
   $matmil[10] = "trillones";
   $matmil[11] = "de trillones";
   $matmil[12] = "millones de trillones";
   $matmil[13] = "de trillones";
   $matmil[14] = "billones de trillones";
   $matmil[15] = "de billones de trillones";
   $matmil[16] = "millones de billones de trillones";
   $num = trim((string)@$num);
   if ($num[0] == "-") {
      $neg = "menos ";
      $num = substr($num, 1);
   }else
      $neg = "";
   while ($num[0] == "0") $num = substr($num, 1);
   if ($num[0] < "1" or $num[0] > 9) $num = "0" . $num;
   $zeros = true;
   $punt = false;
   $ent = "";
   $fra = "";
   for ($c = 0; $c < strlen($num); $c++) {
      $n = $num[$c];
      if (! (strpos(".,´´`", $n) === false)) {
         if ($punt) break;
         else{
            $punt = true;
            continue;
         }
      }elseif (! (strpos("0123456789", $n) === false)) {
         if ($punt) {
            if ($n != "0") $zeros = false;
            $fra .= $n;
         }else
            $ent .= $n;
      }else
         break;
   }

   $ent = "     " . $ent;

   if ($dec and $fra and ! $zeros) {
      $fin = " coma";
      for ($n = 0; $n < strlen($fra); $n++) {
         if (($s = $fra[$n]) == "0")
            $fin .= " cero";
         elseif ($s == "1")
            $fin .= $fem ? " una" : " un";
         else
            $fin .= " " . $matuni[$s];
      }
   }else
      $fin = "";
   if ((int)$ent === 0) return "Cero " . $fin;
   $tex = "";
   $sub = 0;
   $mils = 0;
   $neutro = false;

   while ( ($num = substr($ent, -3)) != "   ") {

      $ent = substr($ent, 0, -3);
      if (++$sub < 3 and $fem) {
         $matuni[1] = "una";
         $subcent = "as";
      }else{
         //$matuni[1] = $neutro ? "un" : "uno";
         $matuni[1] = $neutro ? "un" : "un";
         $subcent = "os";
      }
      $t = "";
      $n2 = substr($num, 1);
      if ($n2 == "00") {
      }elseif ($n2 < 21)
         $t = " " . $matuni[(int)$n2];
      elseif ($n2 < 30) {
         $n3 = $num[2];
         if ($n3 != 0) $t = "i" . $matuni[$n3];
         $n2 = $num[1];
         $t = " " . $matdec[$n2] . $t;
      }else{
         $n3 = $num[2];
         if ($n3 != 0) $t = " y " . $matuni[$n3];
         $n2 = $num[1];
         $t = " " . $matdec[$n2] . $t;
      }

      $n = $num[0];
      if ($n == 1) {
         if ($num == 100) $t = " cien" . $t; else $t = " ciento" . $t;
      }elseif ($n == 5){
         $t = " " . $matunisub[$n] . "ient" . $subcent . $t;
      }elseif ($n != 0){
         $t = " " . $matunisub[$n] . "cient" . $subcent . $t;
      }

      if ($sub == 1) {
      }elseif (! isset($matsub[$sub])) {
         if ($num == 1) {
            $t = " mil";
         }elseif ($num > 1){
            $t .= " mil";
         }
      }elseif ($num == 1) {
         $t .= " " . $matsub[$sub] . "ón";
      }elseif ($num > 1){
         $t .= " " . $matsub[$sub] . "ones";
      }
      if ($num == "000") $mils ++;
      elseif ($mils != 0) {
         if (isset($matmil[$sub])) $t .= " " . $matmil[$sub];
         $mils = 0;
      }
      $neutro = true;
      $tex = $t . $tex;
   }
   $tex = $neg . substr($tex, 1) . $fin;
   return $tex;
}
function convertir_a_letras($numero, $tipo) {
	list($e, $d) = SPLIT('[.]', $numero);
	if ($tipo == "moneda")
		return num2letras($e, false, false)." bolivares con ".num2letras($d, false, false)." centimos";
	else if ($tipo == "decimal")
		return num2letras($e, false, false)." con ".num2letras($d, false, false);
	else if ($tipo == "entero")
		return num2letras($e, false, false);
}
//	-------------------------------

//	obtengo la tasa de interes para un periodo
function tasaInteres($periodo) {
	$sql = "SELECT Porcentaje FROM masttasainteres WHERE Periodo = '".$periodo."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	return $field['Porcentaje'];
}

//	obtengo el codigo interno de una dependencia
function getCodInternoDependencia($dependencia) {
	$sql = "SELECT CodInterno FROM mastdependencias WHERE CodDependencia = '".$dependencia."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	return $field['CodInterno'];
}

//	obtengo partida y cuenta de un item
function getPartidaCuentaItem($codigo) {
	$sql = "SELECT CtaGasto, PartidaPresupuestal FROM lg_itemmast WHERE CodItem = '".$codigo."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	return array($field['CtaGasto'], $field['PartidaPresupuestal']);
}

//	obtengo partida y cuenta de un item
function getPartidaCuentaCommodity($codigo) {
	$sql = "SELECT CodCuenta, cod_partida FROM lg_commoditysub WHERE Codigo = '".$codigo."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	return array($field['CodCuenta'], $field['cod_partida']);
}

//	devuelve el valor de un campo
function valorExiste($tabla, $campo, $codigo) {
	$sql = "SELECT * FROM $tabla WHERE $campo = '".$codigo."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) return true;
	else return false;
}

//	devuelve el valor de un campo
function valorExisteUp($tabla, $campo, $codigo, $codcampo, $codvalor) {
	$sql = "SELECT * FROM $tabla WHERE $campo = '".$codigo."' AND $codcampo <> '".$codvalor."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) return true;
	else return false;
}

//	funcion que devuel el primer registro de una tabla
function getPrimeroDefault($tabla, $codigo, $nombre, $order=NULL, $campo1, $valor1) {
	$filtro = "";
	if ($order) $fOrderBy = $order; else $fOrderBy = $codigo;
	if ($campo1) $filtro .= " AND $campo1 = '$valor1'";
	$sql = "SELECT $codigo, $nombre FROM $tabla WHERE 1 $filtro ORDER BY $fOrderBy LIMIT 0, 1";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	return array($field[0], $field[1]);
}

//	paginacion
function paginacion($rows_total, $rows, $maxlimit, $limit) {
	if ($rows_total <= $maxlimit) {
        $p = "style = 'visibility:hidden;'";
        $a = "style = 'visibility:hidden;'";
        $s = "style = 'visibility:hidden;'";
        $u = "style = 'visibility:hidden;'";
        $dh = "style = 'visibility:hidden;'";
    }
    elseif ($limit == 0) {
        $p = "style = 'visibility:hidden;'";
        $a = "style = 'visibility:hidden;'";
    }
    elseif (($limit + $maxlimit) >= $rows_total) {
        $s = "style = 'visibility:hidden;'";
        $u = "style = 'visibility:hidden;'";
    }
    $primero = 0;
    $anterior = $limit - $maxlimit;
    $siguiente = $limit + $maxlimit;
    $num = (int) ($rows_total / $maxlimit);
    $ultimo = $num * $maxlimit;
    if ($ultimo == $rows_total) $ultimo = $ultimo - $maxlimit;
    $desde = $limit + 1;
    if ($maxlimit > $rows) $hasta = ($limit + $rows); else $hasta = ($limit + $maxlimit);

	if ($rows_total % $maxlimit == 0) $paginas = ($rows_total / $maxlimit); else $paginas = (int) ($rows_total / $maxlimit) + 1;
	if ($hasta % $maxlimit == 0) $pagina_actual = $hasta / $maxlimit;	else $pagina_actual = (int) (($hasta / $maxlimit) + 1);
	if ($paginas <= 1) $pag = "style = 'visibility:hidden;'";

	if (file_exists("../imagenes/f_primero.png")) $path = "../imagenes/";
	elseif (file_exists("../../imagenes/f_primero.png")) $path = "../../imagenes/";
    ?>
    <table>
        <tr>
            <td scope="row" width="16">
            	<button onclick="cargarPagina(this.form, this.form.action+'&limit=<?=$primero?>');" <?=$p?>>
                <img src="<?=$path?>f_primero.png" height="12" title="Primera página" />
                </button>
            </td>
            <td scope="row" width="16">
            	<button onclick="cargarPagina(this.form, this.form.action+'&limit=<?=$anterior?>');" <?=$a?>>
                <img src="<?=$path?>f_anterior.png" height="12" title="Primera anterior" />
                </button>
            </td>
            <td scope="row">
                <span <?=$pag?>>P&aacute;gina <?=$pagina_actual?> de <?=$paginas?></span>
            </td>
            <td scope="row" width="16">
            	<button onclick="cargarPagina(this.form, this.form.action+'&limit=<?=$siguiente?>');" <?=$s?>>
                <img src="<?=$path?>f_siguiente.png" height="12" title="Página siguiente" />
                </button>
            </td>
            <td scope="row" width="16">
            	<button onclick="cargarPagina(this.form, this.form.action+'&limit=<?=$ultimo?>');" <?=$u?>>
                <img src="<?=$path?>f_ultimo.png" height="12" title="Última página" />
                </button>
            </td>
        </tr>
    </table>
	<?php
}

//	------------------------------
function setUsuario($CodPersona) {
	$sql = "SELECT Apellido1, Apellido2, Nombres FROM mastpersonas WHERE CodPersona = '".$CodPersona."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	$nombres = split(" ", $field['Nombres']);
	$n1 = "$nombres[0]";
	$n2 = "$nombres[1]";
	$n3 = "$nombres[2]";
	$i1 = substr($n1, 0, 1);
	$i2 = substr($n2, 0, 1);
	$i3 = substr($n3, 0, 1);
	if (strtoupper($n2) == "DE" || strtoupper($n2) == "DEL") $i2 = substr($n3, 0, 1);
	else $i2 = substr($n2, 0, 1);
	if ($field['Apellido1'] == "") $apellido = $field['Apellido2'];
	else $apellido = $field['Apellido1'];
	$usuario = strtoupper("$i1$i2$apellido");
	return $usuario;
}
//	------------------------------

//	FUNCION PARA IMPRIMIR UN CHECK
function printEstado($Estado) {
	$path = "../imagenes";
	if ($Estado == "A") $flag = "<img src='$path/arriba.png' width='16' height='16' />";
	else $flag = "<img src='$path/abajo.png' width='16' height='16' />";
	return $flag;
}

//	FUNCION PARA IMPRIMIR UN CHECK
function printWarning($Estado) {
	$path = "../imagenes";
	if ($Estado == "S") $flag = "<img src='$path/warning.png' width='16' height='16' />";
	else $flag = "";
	return $flag;
}

//	FUNCION QUE CHEQUEA UN VALOR PARA UN CHECBOX
function chkFlag($chk) {
	$chk = strtoupper($chk);
	if ($chk == "S" || $chk == 1 || $chk == '1' || $chk == 'X') echo "checked='checked'";
	else echo "";
}

//	FUNCION QUE CHEQUEA UN VALOR PARA UN RADIO
function chkOpt($chk, $value) {
	if ($chk == $value) echo "checked='checked'";
	else echo "";
}

//	FUNCION QUE VERIFICA SI EL TIPO DE SERVICIO ES AFECTO A IMPUESTO
function afectaTipoServicio($CodTipoServicio) {
	global $_PARAMETRO;
	$sql = "SELECT tsi.CodImpuesto
			FROM
				masttiposervicioimpuesto tsi
				INNER JOIN mastimpuestos i ON (tsi.CodImpuesto = i.CodImpuesto AND
											   i.CodRegimenFiscal = 'I')
			WHERE tsi.CodTipoServicio = '".$CodTipoServicio."'";
	$query_impuesto = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query_impuesto) != 0) return true; else return false;
}

//	Obtengo el porcentaje del igv/iva
function getFactorImpuesto() {
	global $_PARAMETRO;
	$sql = "SELECT FactorPorcentaje
			FROM mastimpuestos
			WHERE CodImpuesto = '".$_PARAMETRO['IGVCODIGO']."'";
	$query_impuesto = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query_impuesto) != 0) $field_impuesto = mysql_fetch_array($query_impuesto);
	return $field_impuesto['FactorPorcentaje'];
}

//	Obtengo el porcentaje del igv/iva
function getPorcentajeIVA($CodTipoServicio) {
	$sql = "SELECT FactorPorcentaje
			FROM
				masttiposervicio ts
				INNER JOIN masttiposervicioimpuesto tsi ON (ts.CodTipoServicio = tsi.CodTipoServicio)
				INNER JOIN mastimpuestos i ON (tsi.CodImpuesto = i.CodImpuesto AND
											   i.CodRegimenFiscal = 'I' AND
											   i.Signo = 'P')
			WHERE ts.CodTipoServicio = '".$CodTipoServicio."'";
	$query_impuesto = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query_impuesto) != 0) $field_impuesto = mysql_fetch_array($query_impuesto);
	return floatval($field_impuesto['FactorPorcentaje']);
}

//
function valObligacion($CodProveedor, $CodTipoDocumento, $NroDocumento) {
	$sql = "SELECT CodProveedor, CodTipoDocumento, NroDocumento
			FROM ap_obligaciones
			WHERE
				CodProveedor = '".$CodProveedor."' AND
				CodTipoDocumento = '".$CodTipoDocumento."' AND
				NroDocumento = '".$NroDocumento."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) return true; else return false;
}

//
function ahora() {
	$sql = "SELECT NOW() As Ahora";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) {
		$field = mysql_fetch_array($query);
		return $field[0];
	}
}

//	obtiene
function getFirma($CodPersona, $swNomCompleto=NULL, $swEstado=NULL, $swCodCargoTemp=NULL) {
	global $_PARAMETRO;
	$_CodCargoTemp = $swCodCargoTemp;
	if ($swCodCargoTemp) $innerp2 = $swCodCargoTemp; else $innerp2 = 'me.CodCargoTemp';
	$sql = "SELECT
				mp.Apellido1,
				mp.Apellido2,
				mp.Nombres,
				mp.NomCompleto,
				mp.Sexo,
				p1.DescripCargo AS Cargo,
				p2.CodCargo AS CodCargoEncargado,
				p2.DescripCargo AS CargoEncargado,
				p2.Grado AS GradoEncargado
			FROM
				mastpersonas mp
				INNER JOIN mastempleado me ON (mp.CodPersona = me.CodPersona)
				INNER JOIN rh_puestos p1 ON (me.CodCargo = p1.CodCargo)
				LEFT JOIN rh_puestos p2 ON ($innerp2 = p2.CodCargo)
			WHERE mp.CodPersona = '".$CodPersona."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	##
	if ($swNomCompleto) {
		$NomCompleto = $field['NomCompleto'];
	} else {
		list($Nombre) = split("[ ]", $field['Nombres']);
		if ($field['Apellido1'] != "") $Apellido = $field['Apellido1']; else $Apellido = $field['Apellido2'];
		$NomCompleto = "$Nombre $Apellido";
	}
	if ($swEstado) $Estado = getVar3("SELECT Estado FROM mastestados WHERE CodEstado = '".$_PARAMETRO['ESTADODEFAULT']."'");
	else $Estado = "";
	##
	if ($field['CargoEncargado'] != "" && $_CodCargoTemp != $field['CodCargoEncargado']) {
		if ($field['GradoEncargado'] == "99" && $_PARAMETRO['PROV99'] == $CodPersona) $tmp = " PROVISIONAL"; else $tmp = " (E)";
		$Cargo = $field['CargoEncargado'];
	}
	else { $Cargo = $field['Cargo']; $tmp = ""; }
	##
	$Cargo = str_replace("(A)", "", $Cargo);
	if ($field['Sexo'] == "M") {
	} else {
		$Cargo = str_replace("JEFE", "JEFA", $Cargo);
		$Cargo = str_replace("DIRECTOR", "DIRECTORA", $Cargo);
		$Cargo = str_replace("CONTRALOR", "CONTRALORA", $Cargo);
		$Cargo = trim($Cargo);
	}

	##	consulto el nivel de instruccion
	$sql = "SELECT
				ei.Nivel,
				ngi.AbreviaturaM,
				ngi.AbreviaturaF
			FROM
				rh_empleado_instruccion ei
				INNER JOIN rh_nivelgradoinstruccion ngi ON (ngi.CodGradoInstruccion = ei.CodGradoInstruccion AND
														    ngi.Nivel = ei.Nivel)
			WHERE
				ei.CodPersona = '".$CodPersona."' AND
				(ngi.AbreviaturaM <> '' OR ngi.AbreviaturaF <> '')
			ORDER BY ei.FechaGraduacion DESC
			LIMIT 0, 1";
	$query_nivel = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query_nivel) != 0) $field_nivel = mysql_fetch_array($query_nivel);
	if ($field['Sexo'] == "M") $nivel = $field_nivel['AbreviaturaM']; else $nivel = $field_nivel['AbreviaturaF'];
	##
	return array($NomCompleto, $Cargo.$tmp, $nivel);
}

//	obtiene
function getFirmaxDependencia($CodDependencia, $swNomCompleto=NULL, $swEstado=NULL) {
	global $_PARAMETRO;
	//	obtengo responsable de la dependencia
	$sql = "SELECT
				d.CodPersona,
				d.CodCargo AS CodCargoDependencia,
				e.CodCargo AS CodCargoEmpleado,
				e.CodCargoTemp AS CodCargoEmpleadoTemp
			FROM
				mastdependencias d
				INNER JOIN mastempleado e ON (e.CodPersona = d.CodPersona)
			WHERE d.CodDependencia = '".$CodDependencia."'";
	$field = getRecord($sql);
	//	valido si es encargado
	/*if ($field['CodCargoDependencia'] == $field['CodCargoEmpleado'] || $field['CodCargoDependencia'] == $field['CodCargoEmpleadoTemp'])
		list($Nombre, $Cargo, $Nivel) = getFirma($field['CodPersona'], $swNomCompleto, $swEstado);
	else
		list($Nombre, $Cargo, $Nivel) = getFirma($field['CodPersona'], $swNomCompleto, $swEstado, $field['CodCargoDependencia']);*/
	//	valido si es encargado
	if ($field['CodCargoDependencia'] == $field['CodCargoEmpleadoTemp'])
		list($Nombre, $Cargo, $Nivel) = getFirma($field['CodPersona'], $swNomCompleto, $swEstado);
	else
		list($Nombre, $Cargo, $Nivel) = getFirma($field['CodPersona'], $swNomCompleto, $swEstado, $field['CodCargoDependencia']);
	##
	return array($Nombre, $Cargo, $Nivel);
}

function getFirmaxDependencia2($CodDependencia) {
	global $_PARAMETRO;
	//	obtengo responsable de la dependencia
	$sql = "SELECT
				d.CodPersona,
				d.CodCargo AS CodCargoDependencia,
				e.CodCargo AS CodCargoEmpleado,
				e.CodCargoTemp AS CodCargoEmpleadoTemp,
				pt1.Grado AS GradoDependencia,
				pt1.DescripCargo AS CargoDependencia,
				pt2.DescripCargo AS CargoEmpleado,
				pt3.DescripCargo AS CargoEmpleadoTemp,
				p.Nombres,
				p.Apellido1,
				p.Apellido2,
				p.Sexo
			FROM
				mastdependencias d
				INNER JOIN mastempleado e ON (e.CodPersona = d.CodPersona)
				INNER JOIN mastpersonas p ON (p.CodPersona = e.CodPersona)
				INNER JOIN rh_puestos pt1 ON (d.CodCargo = pt1.CodCargo)
				INNER JOIN rh_puestos pt2 ON (e.CodCargo = pt2.CodCargo)
				LEFT JOIN rh_puestos pt3 ON (e.CodCargoTemp = pt3.CodCargo)
			WHERE d.CodDependencia = '".$CodDependencia."'";
	$field = getRecord($sql);
	##
	list($Nombre) = split("[ ]", $field['Nombres']);
	if ($field['Apellido1'] != "") $Apellido = $field['Apellido1']; else $Apellido = $field['Apellido2'];
	$NomCompleto = "$Nombre $Apellido";
	##
	if ($field['CodCargoDependencia'] != $field['CodCargoEmpleado']) {
		if ($field['GradoDependencia'] == "99" && $_PARAMETRO['PROV99'] == $field['CodPersona']) $tmp = " PROVISIONAL"; else $tmp = " (E)";
		$Cargo = $field['CargoDependencia'];
	}
	else { $Cargo = $field['CargoEmpleado']; $tmp = ""; }
	##
	$Cargo = str_replace("(A)", "", $Cargo);
	if ($field['Sexo'] == "M") {
	} else {
		$Cargo = str_replace("JEFE", "JEFA", $Cargo);
		$Cargo = str_replace("DIRECTOR", "DIRECTORA", $Cargo);
		$Cargo = str_replace("CONTRALOR", "CONTRALORA", $Cargo);
		$Cargo = trim($Cargo);
	}
	##	consulto el nivel de instruccion
	$sql = "SELECT
				ei.Nivel,
				ngi.AbreviaturaM,
				ngi.AbreviaturaF
			FROM
				rh_empleado_instruccion ei
				INNER JOIN rh_nivelgradoinstruccion ngi ON (ngi.CodGradoInstruccion = ei.CodGradoInstruccion AND
														    ngi.Nivel = ei.Nivel)
			WHERE
				ei.CodPersona = '".$field['CodPersona']."' AND
				(ngi.AbreviaturaM <> '' OR ngi.AbreviaturaF <> '')
			ORDER BY ei.FechaGraduacion DESC
			LIMIT 0, 1";
	$field_nivel = getRecord($sql);
	if ($field['Sexo'] == "M") $nivel = $field_nivel['AbreviaturaM']; else $nivel = $field_nivel['AbreviaturaF'];

	return array($NomCompleto, $Cargo.$tmp, $nivel);
}

//	funcion para sustituir caracteres especiales
function changeUrl($texto) {
	//eregi_replace("foros","tutoriales","$cadena");
	$texto = str_replace("|char:ampersand|", "&", $texto);
	$texto = str_replace("|char:mas|", "+", $texto);
	$texto = str_replace("|char:comillasimple|", "\'", $texto);
	$texto = str_replace("|char:comilladoble|", "\"", $texto);
	return $texto;
}

//	funcion para verificar
function periodoAbierto($CodOrganismo, $Periodo) {
	$sql = "SELECT *
			FROM lg_periodocontrol
			WHERE
				CodOrganismo = '".$CodOrganismo."' AND
				Periodo = '".$Periodo."' AND
				FlagTransaccion = 'S' AND
				Estado = 'A'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) == 0) return false;
	else return true;
}

//	funcion para copiar una imagen
function copiarImagenTMP($imagen) {
	global $_FILES;
	global $_SESSION;
	$path = "../imagenes/tmp/";
	$im_name = str_replace(' ', '_', $_FILES[$imagen]['name']);
	$im_type = $_FILES[$imagen]['type'];
	$im_size = $_FILES[$imagen]['size'];
	$im_tmp_name = $_FILES[$imagen]['tmp_name'];
	##
	if ($im_size > 10000000) {
		$_error = "Se produjo un error al cargar la imagen (Tamaño excedido)";
	} else {
		$partes =  explode("." , $im_name);
		$ruta = $path.$_SESSION["_USUARIO"]."_tmp_".$partes[0].".".$partes[1];
		$im = $_SESSION["_USUARIO"]."_tmp_".$partes[0].".".$partes[1];
		$existe = true;
		while($existe) {
			if(file_exists($ruta)) {
				unlink($ruta);
				$ran = rand(0, 1000000);
				$ruta = $path.$_SESSION["_USUARIO"]."_tmp_$ran".".".$partes[1];
				$im = $_SESSION["_USUARIO"]."_tmp_$ran".".".$partes[1];
			} else {
				$existe = false;
			}
		}
		if(!copy($im_tmp_name, $ruta)) {
			$_error = "Se produjo un error al cargar la imagen ($im_tmp_name, $ruta)";
			$im = "";
		}
	}
	return array($im, $_error);
}

//	funcion para copiar una imagen
function copiarFoto($imagen, $nombre, $path) {
	global $_FILES;
	global $_SESSION;
	global $_PARAMETRO;
	$im_name = str_replace(' ', '_', $_FILES[$imagen]['name']);
	$im_type = $_FILES[$imagen]['type'];
	$im_size = $_FILES[$imagen]['size'];
	$im_tmp_name = $_FILES[$imagen]['tmp_name'];
	##
	if ($im_size > 10000000) {
		$_error = "Se produjo un error al cargar la imagen (Tamaño excedido)";
	} else {
		$partes =  explode("." , $im_name);
		$ruta = $path.$nombre.".".$partes[1];
		$im = $nombre.".".$partes[1];
		$existe = true;
		while($existe) {
			if(file_exists($ruta)) {
				unlink($ruta);
				$ran = rand(0, 1000000);
				$ruta = $path.$nombre."_$ran".".".$partes[1];
				$im = $nombre."_$ran".".".$partes[1];
			} else {
				$existe = false;
			}
		}
		if(!copy($im_tmp_name, $ruta)) {
			$_error = "Se produjo un error al cargar la imagen ($im_tmp_name, $ruta)";
			$im = "";
		}
	}
	return array($im, $_error);
}

//	funcion para armar el lugar
function setLugar($CodCiudad) {
	$sql = "SELECT
				p.Pais,
				e.Estado,
				m.Municipio,
				c.Ciudad
			FROM
				mastciudades c
				INNER JOIN mastmunicipios m ON (m.CodMunicipio = c.CodMunicipio)
				INNER JOIN mastestados e ON (e.CodEstado = m.CodEstado)
				INNER JOIN mastpaises p ON (p.CodPais = e.CodPais)
			WHERE c.CodCiudad = '".$CodCiudad."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	while ($field = mysql_fetch_array($query)) {
		$r = "$field[Ciudad] ESTADO $field[Estado]";
	}
	return $r;
}

// funcion para validar los dos dias adicionales por antiguedad
function getDiasAdicionalesTrimestral($Fingreso, $FechaDesde, $FechaHasta, $Fegreso) {
	list($iAnio, $iMes, $iDia) = split("[-]", $Fingreso);
	list($eAnio, $eMes, $eDia) = split("[-]", $Fegreso);
	list($dAnio, $dMes, $dDia) = split("[-]", $FechaDesde);
	list($hAnio, $hMes, $hDia) = split("[-]", $FechaHasta);
	##
	$Cantidad = 0;
	if ($eDia == "" || $eDia == "00") {
		list($sAnios, $sMeses, $sDias) = getTiempo(formatFechaDMA($Fingreso), formatFechaDMA($FechaHasta));
		if ($sAnios >= 2) {
			if ($iAnio <= 1997 && $dMes == "06") $Cantidad = 2;
			elseif ($iAnio > 1997 && ($iMes == $dMes || $iMes == $hMes) && $iDia >= $dDia && $iDia <= $hDia) $Cantidad = 2;
		}
	} else {
		list($sAnios, $sMeses, $sDias) = getTiempo(formatFechaDMA($Fingreso), formatFechaDMA($Fegreso));
		if ($sAnios >= 2) {
			if ($iAnio <= 1997 && $dMes == "06") $Cantidad = 2;
			elseif ($iAnio > 1997 && ($iMes == $dMes || $iMes == $hMes)) $Cantidad = 2;
		}
	}
	return $Cantidad;
}

//	funcion para obtener el complemento por los dos dias adicionales de antiguedad
function calculo_antiguedad_complemento_trimestral($CodPersona, $Fingreso, $FechaDesde, $FechaHasta) {
	if ($Fingreso <= "1997-06-30") $Fingreso = "1997-06-01";
	list($iAnio, $iMes, $iDia) = split("[-]", $Fingreso);
	list($dAnio, $dMes, $dDia) = split("[-]", $FechaDesde);
	list($hAnio, $hMes, $hDia) = split("[-]", $FechaHasta);
	##
	$mDesde = intval($iMes) + 1;
	if ($mDesde > 12) {
		$mDesde = 1;
		$aDesde = $hAnio;
	} else $aDesde = $hAnio - 1;
	if ($mDesde < 10) $mDesde = "0$mDesde";
	$dPeriodo = "$aDesde-$mDesde";
	$hPeriodo = "$hAnio-$iMes";
	//	obtengo los sueldos mensuales
	$sql = "SELECT Periodo, SueldoNormal, AliVac, AliFin
			FROM rh_sueldos
			WHERE
				CodPersona = '".$CodPersona."' AND
				Periodo >= '".$dPeriodo."' AND
				Periodo <= '".$hPeriodo."'";
	$query_sueldo = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	while($field_sueldo = mysql_fetch_array($query_sueldo)) {
		$periodo = $field_sueldo['Periodo'];
		$_PERIODOS[$periodo] = $periodo;
		$_SUELDO[$periodo] = $field_sueldo['SueldoNormal'];
		$_ALIVAC[$periodo] = $field_sueldo['AliVac'];
		$_ALIFIN[$periodo] = $field_sueldo['AliFin'];
	}
	//	esto aplica para delta amacuro solamente
	if ("2011-12" >= $dPeriodo && "2011-12" <= $hPeriodo) {
		$filtro_concepto1 = " OR c.CodConcepto = '0064'";
	}
	//	FIN----------------------------------------
	//	obtengo bonos adicionales
	$sql = "SELECT Periodo, SUM(tnec.Monto) AS Monto
			FROM
				pr_tiponominaempleadoconcepto tnec
				INNER JOIN pr_concepto c ON (tnec.CodConcepto = c.CodConcepto AND
											 ((c.Tipo = 'I' AND c.FlagBonoRemuneracion = 'S') $filtro_concepto1))
			WHERE
				tnec.CodPersona = '".$CodPersona."' AND
				tnec.Periodo >= '".$dPeriodo."' AND
				tnec.Periodo <= '".$hPeriodo."'
			GROUP BY Periodo";
	$query_bonos = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	while($field_bonos = mysql_fetch_array($query_bonos)) {
		$periodo = $field_bonos['Periodo'];
		$_BONOS[$periodo] = $field_bonos['Monto'];
	}
	//	obtengo los dias acumulados
	if ($Fingreso <= "1997-06-30") $FechaInicio = "1997-06-01"; else $FechaInicio = $Fingreso;
	list($sAnios, $sMeses, $sDias) = getTiempo(formatFechaDMA($FechaInicio), formatFechaDMA($FechaHasta));
	$DiasAcumulados = ($sAnios - 1) * 2;
	$DiasAcumulados = (($DiasAcumulados > 30)?30:$DiasAcumulados);
	//	operaciones
	$SueldoAlicuotas = 0;
	foreach ($_PERIODOS as $periodo) {
		$RemuneracionDiaria = round(($_SUELDO[$periodo] + $_BONOS[$periodo]) / 30, 2);
		$SueldoAlicuotas += $_ALIVAC[$periodo] + $_ALIFIN[$periodo] + $RemuneracionDiaria;
	}
	$Monto = $SueldoAlicuotas / 12 * $DiasAcumulados;
	return $Monto;
}

//	obtener los bonos en un rango de periodos
function getBono($CodPersona, $dPeriodo, $hPeriodo=NULL) {
	$filtro = "";
	if ($CodPersona != "") $filtro .= " AND tnec.CodPersona = '".$CodPersona."'";
	if ($hPeriodo != "") {
		$filtro .= " AND tnec.Periodo >= '".$dPeriodo."'";
		$filtro .= " AND tnec.Periodo <= '".$hPeriodo."'";
	}
	elseif ($dPeriodo != "") $filtro .= " AND tnec.Periodo = '".$dPeriodo."'";
	$Bonos = 0;
	//	obtengo bonos adicionales
	$sql = "SELECT SUM(tnec.Monto)
			FROM
				pr_tiponominaempleadoconcepto tnec
				INNER JOIN pr_concepto c ON (tnec.CodConcepto = c.CodConcepto AND
											 c.Tipo = 'I' AND
											 c.FlagBonoRemuneracion = 'S')
			WHERE 1 $filtro
			GROUP BY Periodo";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	while($field = mysql_fetch_array($query)) {
		$Bonos = $field[0];
	}
	//	esto aplica para delta amacuro solamente
	$sql = "SELECT Monto
			FROM pr_tiponominaempleadoconcepto
			WHERE
				CodConcepto = '0064' AND
				CodPersona = '".$CodPersona."' AND
				(('2011-12' = '".$dPeriodo."' OR '2011-12' = '".$hPeriodo."') OR
				 ('2011-12' >= '".$dPeriodo."' AND '2011-12' <= '".$hPeriodo."'))";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	while($field = mysql_fetch_array($query)) $Bonos += $field[0];
	//	FIN----------------------------------------
	return floatval($Bonos);
}

//	head de las competencias
function printHeadCompetencias($TipoEvaluacion, $w, $h, $dif=0) {
	?>
	<tr style="background-color:#333;">
        <td style="padding-left:4px;">
		<?php
        //	consulto los grados
        $sql = "SELECT
        			PuntajeMin,
        			PuntajeMax,
        			(PuntajeMax - PuntajeMin + 1) AS Cols,
        			Descripcion
                FROM rh_gradoscompetencia
                WHERE TipoEvaluacion = '".$TipoEvaluacion."'
                ORDER BY PuntajeMin";
        $query_grados = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
        while($field_grados = mysql_fetch_array($query_grados)) {
            $grados[] = $field_grados;
        }

        //	imprimir los titulos
        foreach ($grados as $grado) {
	        $width = ($w * $grado['Cols']) + (($grado['Cols'] - $dif) * 3);
            ?><div class="divHeadCompetencias" style="width:<?=$width?>px; height:<?=$h?>px;"><?=$grado['Descripcion']?></div><?php
        }
		?><br style="clear:both;" /><?php
        foreach ($grados as $grado) {
        	for ($i=$grado['PuntajeMin']; $i<=$grado['PuntajeMax']; $i++) {
            	?><div class="divHeadCompetencias" style="width:<?=$w?>px; height:15px;"><?=$i?></div><?php
        	}
        }
        ?>
        </td>
    </tr>
	<?php
}

//	head de las competencias
function printBodyCompetenciasCargo($CodCargo, $TipoEvaluacion, $w, $h) {
	//	consulto los grados
	$sql = "SELECT
       			PuntajeMin,
       			PuntajeMax,
       			(PuntajeMax - PuntajeMin + 1) AS Cols,
       			Descripcion
    		FROM rh_gradoscompetencia
    		WHERE TipoEvaluacion = '".$TipoEvaluacion."'
    		ORDER BY PuntajeMin";
    $query_grados = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
    while($field_grados = mysql_fetch_array($query_grados)) {
    	$grados[] = $field_grados;
    }

    $nro_competencias = 0;
	//	consulto datos generales
	$sql = "SELECT
				ef.Competencia,
				ef.Descripcion,
				ef.TipoCompetencia,
				ef.ValorRequerido,
				ef.ValorMinimo,
				md.Descripcion AS NomTipoCompetencia
			FROM
				rh_cargocompetencia cc
				INNER JOIN rh_evaluacionfactores ef ON (ef.Competencia = cc.Competencia)
				LEFT JOIN mastmiscelaneosdet md ON (md.CodDetalle = ef.TipoCompetencia AND
													md.CodAplicacion = 'RH' AND
													md.CodMaestro = 'TIPOCOMPE')
			WHERE cc.CodCargo = '".$CodCargo."'
			ORDER BY NomTipoCompetencia, TipoCompetencia, Competencia";
	$query_competencias = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	while ($field_competencias = mysql_fetch_array($query_competencias)) {	$i++;
		$nro_competencias++;
		if ($grupo != $field_competencias['TipoCompetencia']) {
			$grupo = $field_competencias['TipoCompetencia'];
			?>
            <tr class="trListaBody2">
                <td><?=htmlentities($field_competencias['NomTipoCompetencia'])?></td>
            </tr>
            <?php
		}
		?>
		<tr class="trListaBody">
			<td>
				<strong><?=htmlentities($field_competencias['Descripcion'])?></strong><br />
                <?php
				//	imprimo valor requerido
				foreach ($grados as $grado) {
					for ($i=$grado['PuntajeMin']; $i<=$grado['PuntajeMax']; $i++) {
						if ($field_competencias['ValorRequerido'] >= $i) $style = "background-color:#000;"; else $style = "";
            			?><div class="divBodyCompetencias" style=" <?=$style?> width:<?=$w?>px; height:<?=$h?>px;"></div><?php
        			}
				}
				?><br style="clear:both;" /><?php
				//	imprimo valor minimo
				foreach ($grados as $grado) {
					for ($i=$grado['PuntajeMin']; $i<=$grado['PuntajeMax']; $i++) {
						if ($field_competencias['ValorMinimo'] >= $i) $style = "background-color:#5F160E;"; else $style = "";
            			?><div class="divBodyCompetencias" style=" <?=$style?> width:<?=$w?>px; height:<?=$h?>px;"></div><?php
        			}
				}
				?>
            </td>
		</tr>
		<?php
	}
}

//	devuelve el valor de un campo
function getUT($Anio, $Secuencia=NULL) {
	if ($Secuencia != "") {
		$filtro_secuencia .= " AND ut.Secuencia = '".$Secuencia."'";
	} else {
		$filtro_maximo = " AND ut.Secuencia = (SELECT MAX(Secuencia) FROM mastunidadtributaria WHERE Anio = ut.Anio)";
	}
	$sql = "SELECT ut.Valor
			FROM mastunidadtributaria ut
			WHERE ut.Anio = '".$Anio."' $filtro_secuencia $filtro_maximo
			LIMIT 0, 1";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	return $field[0];
}

//	valido si hay presupuesto para una partida en especifico
function valPresupuesto($CodOrganismo, $EjercicioPpto, $cod_partida, $Monto) {
	$sql = "SELECT (pvd.MontoAjustado - pvd.MontoCompromiso) AS MontoDisponible
			FROM
				pv_presupuesto pv
				LEFT JOIN pv_presupuestodet pvd ON (pvd.Organismo = pv.Organismo AND
													pvd.CodPresupuesto = pv.CodPresupuesto)
			WHERE
				pv.Organismo = '".$CodOrganismo."' AND
				pv.EjercicioPpto = '".$EjercicioPpto."' AND
				pvd.cod_partida = '".$cod_partida."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	if ($Monto <= $field['MontoDisponible']) return true; else return false;
}

//
function totalTiempo($TotalTiempoA, $TotalTiempoM, $TotalTiempoD) {
	$_Dias_A_Meses = intval($TotalTiempoD / 30);
	$TotalTiempoM += $_Dias_A_Meses;
	$_Meses_A_Anios = intval($TotalTiempoM / 12);
	$TotalTiempoA += $_Meses_A_Anios;
	$_Dias = $TotalTiempoD - (intval($TotalTiempoD / 30) * 30);
	$_Meses = $TotalTiempoM - (intval($TotalTiempoM / 12) * 12);
	$_Anios = $TotalTiempoA;
	return array(intval($_Anios), intval($_Meses), intval($_Dias));
}

//	funcion para verificar
function getPartidaCuentaFromIGV($CodImpuesto) {
	//	comprometido
	$sql = "SELECT
				cod_partida,
				CodCuenta,
				CodCuentaPub20
			FROM mastimpuestos
			WHERE CodImpuesto = '".$CodImpuesto."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	return array($field['cod_partida'], $field['CodCuenta'], $field['CodCuentaPub20']);
}

//	funcion para obtener el codigo del presupuesto por l año
function setPresupuesto($Organismo, $EjercicioPpto) {
	$sql = "SELECT CodPresupuesto
			FROM pv_presupuesto
			WHERE
				Organismo = '".$Organismo."' AND
				EjercicioPpto = '".$EjercicioPpto."'";
	$query_presupuesto = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query_presupuesto)) $field_presupuesto = mysql_fetch_array($query_presupuesto);
	echo $field_presupuesto['CodPresupuesto'];
}

//	funcion para obtener el codigo del presupuesto por l año
function getPresupuesto($Organismo, $EjercicioPpto) {
	$sql = "SELECT CodPresupuesto
			FROM pv_presupuesto
			WHERE
				Organismo = '".$Organismo."' AND
				EjercicioPpto = '".$EjercicioPpto."'";
	return getVar3($sql);
}

//
function getJefeUnidad($dependencia) {
	$sql = "SELECT p.NomCompleto
			FROM
				mastdependencias d
				INNER JOIN mastpersonas p ON (d.CodPersona = p.CodPersona)
			WHERE d.CodDependencia = '".$dependencia."'";
	$query = mysql_query($sql) or die($sql.mysql_error());
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	return $field[0];
}

//	valido fecha en formato dd-mm-aaaa
function valFecha($fecha) {
	$bOk = true;
	list($dia, $mes, $anio) = split("[./-]", $fecha);
	if (strlen($dia) != 2 || strlen($mes) != 2 || strlen($anio) != 4) $bOk = false;
	else {
		$d = intval($dia);
		$m = intval($mes);
		$a = intval($anio);
		$bOk = checkdate($m, $d, $a);
	}
	return $bOk;
}

//	devuelve si el periodo cumple el trimestre
function esTrimestre($Periodo) {
	list($Anio, $Mes) = split("[./-]", $Periodo);
	if ($Mes == "03" || $Mes == "06" || $Mes == "09" || $Mes == "12") return true;
	else return false;
}

//
function actualizarPeriodosVacacionales($CodPersona) {
	global $_PARAMETRO;
	##	consulto empleado
	$sql = "SELECT
				e.CodTipoNom,
				pt.Grado,
				pt2.Grado AS GradoTemp
			FROM
				mastempleado e
				INNER JOIN rh_puestos pt ON (pt.CodCargo = e.CodCargo)
				LEFT JOIN rh_puestos pt2 ON (pt2.CodCargo = e.CodCargoTemp)
			WHERE e.CodPersona = '".$CodPersona."'";
	$field_empleado = getRecord($sql);
	//	actualizo periodos vacacionales
	$sql = "SELECT *
			FROM rh_vacacionperiodo
			WHERE
				CodPersona = '".$CodPersona."' AND
				CodTipoNom = '".$field_empleado['CodTipoNom']."'
			ORDER BY NroPeriodo";
	$fp = getRecords($sql);
	foreach ($fp as $field_periodos) {
		//	consulto pendientes anteriores
		$sql = "SELECT Pendientes
				FROM rh_vacacionperiodo
				WHERE
					CodPersona = '".$field_periodos['CodPersona']."' AND
					NroPeriodo = '".($field_periodos['NroPeriodo']-1)."' AND
					CodTipoNom = '".$field_empleado['CodTipoNom']."'";
		$field_pendientes_ant = getRecord($sql);
		//	consulto gozados
		$sql = "SELECT SUM(DiasUtiles) AS DiasGozados
				FROM rh_vacacionutilizacion
				WHERE
					CodPersona = '".$field_periodos['CodPersona']."' AND
					NroPeriodo = '".$field_periodos['NroPeriodo']."' AND
					TipoUtilizacion = 'G' AND
					CodTipoNom = '".$field_empleado['CodTipoNom']."'";
		$field_gozados = getRecord($sql);
		//	consulto interrumpidos
		$sql = "SELECT SUM(DiasUtiles) AS DiasInterrumpidos
				FROM rh_vacacionutilizacion
				WHERE
					CodPersona = '".$field_periodos['CodPersona']."' AND
					NroPeriodo = '".$field_periodos['NroPeriodo']."' AND
					TipoUtilizacion = 'I' AND
					CodTipoNom = '".$field_empleado['CodTipoNom']."'";
		$field_interrumpidos = getRecord($sql);
		//	consulto pendientes anteriores
		$sql = "SELECT PendientePago
				FROM rh_vacacionperiodo
				WHERE
					CodPersona = '".$field_periodos['CodPersona']."' AND
					NroPeriodo = '".($field_periodos['NroPeriodo']-1)."' AND
					CodTipoNom = '".$field_empleado['CodTipoNom']."'";
		$field_pendientes_pago_ant = getRecord($sql);
		//	consulto pagados
		$sql = "SELECT SUM(DiasPago) AS PagosRealizados
				FROM rh_vacacionpago
				WHERE
					CodPersona = '".$field_periodos['CodPersona']."' AND
					NroPeriodo = '".$field_periodos['NroPeriodo']."' AND
					CodTipoNom = '".$field_empleado['CodTipoNom']."'";
		$field_pagados = getRecord($sql);
		##	calculo dias
		$Pendientes = $field_periodos['Derecho'] + $field_pendientes_ant['Pendientes'] - $field_gozados['DiasGozados'] + $field_interrumpidos['DiasInterrumpidos'];
		if ($field_empleado['Grado'] == "99" || $field_empleado['GradoTemp'] == "99") $DiasPago = $_PARAMETRO["PAGOFINDC"]; else $DiasPago = $_PARAMETRO["PAGOVACA"];
		$PendientePago = $DiasPago + $field_pendientes_pago_ant['PendientePago'] - $field_pagados['PagosRealizados'];
		//	actualizo
		$TotalUtilizados = $field_gozados['DiasGozados'] - $field_interrumpidos['DiasInterrumpidos'];
		$sql = "UPDATE rh_vacacionperiodo
				SET
					PendientePeriodo = '".$field_pendientes_ant['Pendientes']."',
					DiasGozados = '".$field_gozados['DiasGozados']."',
					DiasInterrumpidos = '".$field_interrumpidos['DiasInterrumpidos']."',
					TotalUtilizados = '".$TotalUtilizados."',
					Pendientes = '".$Pendientes."',
					PagosRealizados = '".$field_pagados['PagosRealizados']."',
					PendientePago = '".$PendientePago."'
				WHERE
					CodPersona = '".$field_periodos['CodPersona']."' AND
					NroPeriodo = '".$field_periodos['NroPeriodo']."' AND
					CodTipoNom = '".$field_empleado['CodTipoNom']."'";
		execute($sql);
	}
}

function valFlag($value) {
	if ($value != "S") return "N"; else return "S";
}

/**
 * addMonth
 * Sumar meses a una fecha
 */
function addMonth($desde, $meses) {
	$Fecha = getVar3("SELECT DATE_ADD('".$desde."', INTERVAL ".intval($meses)." MONTH) AS Fecha");
	return (string) $Fecha;
}

//
function avisoPeriodosVacacionales() {
	global $_PARAMETRO;
	global $Ahora;
	list($AnioActual, $MesActual, $DiaActual) = split("[/.-]", substr($Ahora, 0, 10));
	$FechaActual = "$AnioActual-$MesActual-$DiaActual";
	$PeriodoActual = "$AnioActual-$MesActual";
	if ($_PARAMETRO['ACTVACA'] == "S") {
		$FechaFinMes = "$AnioActual-$MesActual-".getDiasMes($PeriodoActual);
		$FechaEjecucion = obtenerFechaFin(formatFechaDMA($FechaFinMes), -$_PARAMETRO['VACVENDIAS']);
		$FechaEjecucion = formatFechaAMD($FechaEjecucion);
		$AnioSiguiente = $AnioActual;
		$MesSiguiente = intval($MesActual) + 1;
		if ($MesSiguiente > 12) {
			$AnioSiguiente = $AnioActual + 1;
			$MesSiguiente = "01";
		} elseif ($MesSiguiente < 10) $MesSiguiente = "0$MesSiguiente";
		$PeriodoSiguiente = "$AnioSiguiente-$MesSiguiente";
		//	si se cumple entonces
		if ($FechaActual >= $FechaEjecucion && $PeriodoSiguiente <> $_PARAMETRO['ACTVACAPER']) {
			?>
            <script type="text/javascript" language="javascript">
			$(document).ready(function() {
				$("#cajaModal").dialog({
					buttons: {
						"Si": function() {
							$(this).dialog("close");
							$.ajax({
								type: "POST",
								url: "<?=$_PARAMETRO["PATHSIA"]?>rh/lib/form_ajax.php",
								data: "modulo=vacaciones-actualizar",
								async: false,
								success: function(resp) {
									if (resp.trim() != "") cajaModal(resp, "error", 400);
								}
							});
						},
						"No": function() {
							$(this).dialog("close");
						}
					}
				});
				cajaModalConfirm("¿Actualizar los periodos vacacionales de los Empleados?", 400, 1);
			});
			</script>
            <?php
		}
	}
}

function set_rif($string) {
	$len = strlen($string);
	$var1 = substr($string, 0, 1);
	$var2 = substr($string, 1, $len - 2);
	$var3 = substr($string, $len - 1, 1);

	return $var1 . '-' . $var2 . '-' .$var3;
}
?>
