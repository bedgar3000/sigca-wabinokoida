<?php

//	imprime la fecha en formato dd-mm-aaaa
function formatFechaDMA($fecha) {
	list($_fecha, $_hora) = split(" ", $fecha);	
	list($anio, $mes, $dia) = split("[/.-]", $_fecha);
	list($hora, $min, $seg) = split("[:]", $_hora);
	if ($dia == "" || $dia == "0000") $f = ""; else $f = "$dia-$mes-$anio";
	if ($_hora == "") $h = ""; else $h = "$hora:$min:$seg";
	if ($f == "") return "";
	elseif ($h == "") return "$f";
	elseif ($f != "" && $h != "") return "$f $h";
}

//	imprime la fecha en formato aaaa-mm-dd
function formatFechaAMD($fecha) {
	list($_fecha, $_hora) = split(" ", $fecha);	
	list($dia, $mes, $anio) = split("[/.-]", $_fecha);
	list($hora, $min, $seg) = split("[:]", $_hora);
	if ($anio == "" || $anio == "0000") $f = ""; else $f = "$anio-$mes-$dia";
	if ($_hora == "") $h = ""; else $h = "$hora:$min:$seg";
	if ($f == "") return "";
	elseif ($h == "") return "$f";
	elseif ($f != "" && $h != "") return "$f $h";
}

//	imprime la hora en formato 12 horas
function formatHora12($hora, $seg=false) {
	list($h, $m, $s) = split("[:]", $hora);
	$time = "";
	if ($seg) {
		if ($h >= "01" && $h < 12) $time = "$h:$m:$s am";
		if ($h == 12) $time = "$h:$m:$s pm";
		elseif ($h == "00") $time = "12:$m:$s am";
		elseif ($h > 12) {
			$hh = $h - 12;
			if ($hh < 10) $hh = "0$hh";
			$time = "$hh:$m:$s pm";
		}
	} else {
		if ($h >= "01" && $h < 12) $time = "$h:$m am";
		if ($h == 12) $time = "$h:$m pm";
		elseif ($h == "00") $time = "12:$m am";
		elseif ($h > 12) {
			$hh = $h - 12;
			if ($hh < 10) $hh = "0$hh";
			$time = "$hh:$m pm";
		}
	}
	return $time;
}

//	imprime la hora en formato 12 horas
function formatHora24($_hora, $seg=false) {
	list($hora, $mer) = explode(" ", $_hora);
	list($h, $m, $s) = explode(":", $hora);
	if (strtolower($mer) == "pm" && $_hora < 12) $h = $h +12;
	elseif (strtolower($mer) == "am" && $_hora == 12) $h = 24;
	if ($h == 24) $h = "00";
	if ($seg) return "$h:$m:00";
	else return "$h:$m";
}

//	imprime la fecha completa e ormato dd-mm-aaaa hh:mm:ss md
function formatDateFull($tiempo, $seg=false) {
	list($fecha, $hora) = split("[ ]", $tiempo);
	list($fd, $fm, $fa) = split("[/.-]", $fecha);
	list($hh, $hm, $hs) = split("[:]", $hora);
	$FechaDMA = formatFechaDMA($fecha);
	if ($FechaDMA != "") $Hora12 = formatHora12($hora, $seg);
	return "$FechaDMA $Hora12";
}

//	devuelve el numero de dias de un mes
function getDiasMes($periodo) {
	list($anio, $mes) = split("[/.-]", $periodo);
	$anio = intval($anio);
	$mes = intval($mes);
	$dias_mes[1] = 31;
	if(!checkdate(02, 29, $anio)) $dias_mes[2] = 28; else $dias_mes[2] = 29;
	$dias_mes[3] = 31;
	$dias_mes[4] = 30;
	$dias_mes[5] = 31;
	$dias_mes[6] = 30;
	$dias_mes[7] = 31;
	$dias_mes[8] = 31;
	$dias_mes[9] = 30;
	$dias_mes[10] = 31;
	$dias_mes[11] = 30;
	$dias_mes[12] = 31;
	return $dias_mes[$mes];
}

//	devuelve el numero de dias de un mes
function getDiasMesArray($anio) {
	$dias_mes[] = 31;
	if(!checkdate(02, 29, $anio)) $dias_mes[] = 28; else $dias_mes[] = 29;
	$dias_mes[] = 31;
	$dias_mes[] = 30;
	$dias_mes[] = 31;
	$dias_mes[] = 30;
	$dias_mes[] = 31;
	$dias_mes[] = 31;
	$dias_mes[] = 30;
	$dias_mes[] = 31;
	$dias_mes[] = 30;
	$dias_mes[] = 31;
	return $dias_mes;
}

function dias_del_mes($periodo) {
	list($anio, $mes) = explode('-', $periodo);
	$anio = intval($anio);
	$mes = intval($mes);
	$dias = cal_days_in_month(CAL_GREGORIAN, $mes, $anio);

	return $dias;
}

//	devuelve el numero de dias de un mes
function getDiasAnio($Anio) {
	if(!checkdate(02, 29, $Anio)) $Dias = 365; else $Dias = 366;
	return $Dias;
}

//	devuelve el nombre del mes
function getNombreMes($periodo) {
	list($anio, $mes) = split("[/.-]", $periodo);
	$anio = intval($anio);
	$mes = intval($mes);
	$nombre_mes[1] = "Enero";
	$nombre_mes[2] = "Febrero";
	$nombre_mes[3] = "Marzo";
	$nombre_mes[4] = "Abril";
	$nombre_mes[5] = "Mayo";
	$nombre_mes[6] = "Junio";
	$nombre_mes[7] = "Julio";
	$nombre_mes[8] = "Agosto";
	$nombre_mes[9] = "Septiembre";
	$nombre_mes[10] = "Octubre";
	$nombre_mes[11] = "Noviembre";
	$nombre_mes[12] = "Diciembre";
	return $nombre_mes[$mes];
}

//	devuelve el nombre del mes
function nombreMes($mes) {
	$mes = intval($mes);
	$nombre_mes[1] = "Enero";
	$nombre_mes[2] = "Febrero";
	$nombre_mes[3] = "Marzo";
	$nombre_mes[4] = "Abril";
	$nombre_mes[5] = "Mayo";
	$nombre_mes[6] = "Junio";
	$nombre_mes[7] = "Julio";
	$nombre_mes[8] = "Agosto";
	$nombre_mes[9] = "Septiembre";
	$nombre_mes[10] = "Octubre";
	$nombre_mes[11] = "Noviembre";
	$nombre_mes[12] = "Diciembre";
	return $nombre_mes[$mes];
}

//	devuelve el nombre del mes
function getNombreMesArray() {
	$mes[] = "Enero";
	$mes[] = "Febrero";
	$mes[] = "Marzo";
	$mes[] = "Abril";
	$mes[] = "Mayo";
	$mes[] = "Junio";
	$mes[] = "Julio";
	$mes[] = "Agosto";
	$mes[] = "Septiembre";
	$mes[] = "Octubre";
	$mes[] = "Noviembre";
	$mes[] = "Diciembre";
	return $mes;
}

//	devuelve una fecha que es la suma de una fecha inicial + dias
function getFechaFin($fecha, $dias) {
	$sumar = true;
	$dia_semana = getDiaSemana($fecha);	
	list($dia, $mes, $anio) = split("[/.-]", $fecha);
	$d = intval($dia); $m = intval($mes); $a = intval($anio);
	for ($i=1; $i<=$dias;) {
		$dia_semana++;
		if ($dia_semana == 8) $dia_semana = 1;
		if ($dia_semana >= 1 && $dia_semana <= 5) $i++; 
		$d++;
		$dias_mes = getDiasMes("$a-$m");
		if ($d > $dias_mes) { 
			$d = 1;
			$m++; 
			if ($m > 12) { $m = 1; $a++; }
		}
	}
	if ($d < 10) $d = "0$d";
	if ($m < 10) $m = "0$m";
	return "$d-$m-$a";
}

//	devuel el dia de la semana de una fecha
function getDiaSemana($fecha) {
	// primero creo un array para saber los días de la semana
	$dias = array(0, 1, 2, 3, 4, 5, 6);
	$dia = substr($fecha, 0, 2);
	$mes = substr($fecha, 3, 2);
	$anio = substr($fecha, 6, 4);
	// en la siguiente instrucción $pru toma el día de la semana, lunes, martes,
	$pru = strtoupper($dias[intval((date("w",mktime(0,0,0,$mes,$dia,$anio))))]);
	return $pru;
}

//	devuel el dia de la semana de una fecha
function getWeekDay($fecha) {
	$sql = "SELECT WEEKDAY('".formatFechaAMD($fecha)."') AS DiaSemana;";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	return ++$field['DiaSemana'];
}

//	devuelve fecha de inicio y fin de un numero cualquiera (edad, tiempo, etc)
function getFechasTiempo($tiempo) {
	global $Ahora;
	list($anio, $mes, $dia) = split("[.-/]", substr($Ahora, 0, 10));
	$a = intval($anio);
	$m = intval($mes);
	$d = intval($dia);
	$anio_inicio = intval($a - $tiempo - 1);
	$mes_inicio = $m;
	$dia_inicio = $d + 1;	
	$anio_fin = intval($a - $tiempo);
	$mes_fin = $m;
	$dia_fin = $d;	
	if ($dia_inicio > getDiasMes("$anio-$mes")) {
		$dia_inicio = 1;
		if ($mes_inicio == 12) {
			$mes_inicio = 1;
			$anio_inicio++;
		} else {
			$mes_inicio++;
		}
	}	
	if ($dia_inicio < 10) $di = "0$dia_inicio";
	if ($mes_inicio < 10) $mi = "0$mes_inicio";	
	if ($dia_fin < 10) $df = "0$dia_fin";
	if ($mes_fin < 10) $mf = "0$mes_fin";	
	$fecha_inicio = "$di-$mi-$anio_inicio";
	$fecha_fin = "$df-$mf-$anio_fin";	
	return array($fecha_inicio, $fecha_fin);
}

//	devuelve la fecha de inicio y fin para una edad
function getFechasEdad($edad, $fecha=NULL) {
	if (!$fecha) $fecha = formatFechaDMA(substr(ahora(), 0, 10));
	list($a, $m, $d) = explode("-", $fecha);
	$aniod = $a-$edad-1;
	$mesd = intval($m);
	$diad = intval($d) + 1;
	$anioh = $a - $edad; 
	$mesh = intval($m);
	$diah = intval($d);
	if ($diad > getDiasMes("$aniod-$mesd")) {
		$diad = 1;
		if ($mesd == 12) {
			$mesd = 1; 
			$anniod = $a - $edad;
		}
		else $mesd = $mesd + 1;
	}
	if ($mesd < 10) $mes_desde = "0$mesd"; else $mes_desde = $mesd;
	if ($diad < 10) $dia_desde = "0$diad"; else $dia_desde = $diad;
	if ($mesh < 10) $mes_hasta = "0$mesh"; else $mes_hasta = $mesh;
	if ($diah < 10) $dia_hasta = "0$diah"; else $dia_hasta = $diah;
	$fechad = $dia_desde."-".$mes_desde."-".$aniod;
	$fechah = $dia_hasta."-".$mes_hasta."-".$anioh;
	return array($fechad, $fechah);
}

//	funcion para obtener los años meses y dias entre dos fechas
function getTiempo($_DESDE, $_HASTA) {
	$desde = formatFechaAMD($_DESDE);
	$hasta = formatFechaAMD($_HASTA);
	$fecha_fin = addDate($hasta, 1);
	$sql = "SELECT antiguedad('".$desde."','".$fecha_fin."') AS tiempo";
	$tiempo = getVar3($sql);
	list($Anios, $Meses, $Dias) = explode('-', $tiempo);
	return array($Anios, $Meses, $Dias);
}

function addDate($desde, $dias) {
	$sql = "SELECT ADDDATE('".$desde."',".intval($dias).") AS Fecha";
	$Fecha = getVar3($sql);
	return (string) $Fecha;
}
/**
 * Sumar dias/meses a una fecha.
 * Si la cantidad a sumar es mayor a 1 agregar en plural el valor del interval (Ej. day -> days)
 *
 * @param  String
 * @param  Integer
 * @param  String ('day[s]','month[s]','week[s]','hour[s]','second[s]','minute[s]','year[s]')
 * @param  String
 * @return String
 */
function addDias($fecha, $dias = 1, $cant = 'day', $formato = 'Y-m-d')
{
	$interval = $dias . ' ' . $cant;

	return date_format(date_add(date_create($fecha), date_interval_create_from_date_string($interval)), $formato);
}

//	FUNCION PARA OBTENER LOS ANIOS, MESES Y DIAS ENTRE DOS FECHAS
function getEdad($_DESDE, $_HASTA) {
	$error = 0;
	$listo = 0;
	if ((strlen($_DESDE)) < 10) $error = 1;
	else {
		list($d, $m, $a) = SPLIT("[/.-]", $_HASTA);
		$diaActual = $d;
		$mesActual = $m;
		$annioActual = $a;
		##
		list($d, $m, $a) = split("[/.-]", $_DESDE);
		$dia = intval($d);
		$mes = intval($m);
		$annio = intval($a);
		$dias = 0;
		$meses = 0;
		$annios = 0;
		##
		if ($annio > $annioActual || ($annio == $annioActual && $mes > $mesActual) || ($annio == $annioActual && $mes == $mesActual && $dia > $diaActual)) $error = 2;
		else {
			$annios = $annioActual - $annio;
			$meses = $mesActual - $mes;
			$dias = $diaActual - $dia;
			##
			if ($dias < 0) { $meses--; $dias = 30 + $dias; }
			if ($meses < 0) { $annios--; $meses = 12 + $meses; }
			##
			if ($dias >= 30) { $meses++; $dias = 0; }
			if ($meses >= 12) { $annios++; $meses = 0; }
			##
			return array($annios, $meses, $dias);
		}
	}
	if ($error!=0) return array("", "", "");
}

//	FUNCION PARA OBTENER LOS ANIOS, MESES Y DIAS ENTRE DOS FECHAS
function edad($_DESDE, $_HASTA) {
	$error = 0;
	$listo = 0;
	$edad['Anios'] = '';
	$edad['Meses'] = '';
	$edad['Dias'] = '';
	if ((strlen($_DESDE)) < 10) $error = 1;
	else {
		list($d, $m, $a) = SPLIT("[/.-]", $_HASTA);
		$diaActual = $d;
		$mesActual = $m;
		$annioActual = $a;
		##
		list($d, $m, $a) = split("[/.-]", $_DESDE);
		$dia = intval($d);
		$mes = intval($m);
		$annio = intval($a);
		$dias = 0;
		$meses = 0;
		$annios = 0;
		##
		if ($annio > $annioActual || ($annio == $annioActual && $mes > $mesActual) || ($annio == $annioActual && $mes == $mesActual && $dia > $diaActual)) $error = 2;
		else {
			$annios = $annioActual - $annio;
			$meses = $mesActual - $mes;
			$dias = $diaActual - $dia;
			##
			if ($dias < 0) { $meses--; $dias = 30 + $dias; }
			if ($meses < 0) { $annios--; $meses = 12 + $meses; }
			##
			if ($dias >= 30) { $meses++; $dias = 0; }
			if ($meses >= 12) { $annios++; $meses = 0; }
			##
			$edad['Anios'] = $annios;
			$edad['Meses'] = $meses;
			$edad['Dias'] = $dias;
			return $edad;
		}
	}
	if ($error!=0) return $edad;
}

/**
 * Obtener años meses dias entre dos fechas
 *
 * @param (string) $desde fecha de inicio
 * @param (string) $hasta fecha de término
 * @param (integer) $adic dias adicionales (tomar en cuenta el dia de inicio para el calculo)
 *
 * @return	array
 */
if ( ! function_exists('getDiffFecha')) {
	function getDiffFecha($desde, $hasta=false, $adic=0) {
		$fecha_desde = new DateTime("2016-05-01");
		$fecha_hasta = new DateTime("2016-05-30");

		$diferencia = $fecha_desde->diff($fecha_hasta);

		$Anios = $diferencia->y;
		$Meses = $diferencia->m;
		$Dias = $diferencia->d;
		if ($adic) {
			++$Dias;
			if ($Dias >= 30) {
				$Dias = 0;
				++$Meses;
				if ($Meses > 12) {
					$Meses = 0;
					++$Anios;
				}
			}
		}

		$tiempo = [
			'Anios' => $$Anios,
			'Meses' => $Meses,
			'Dias' => $Dias,
		];

		return $tiempo;
	}
}

//
function getDiffHora($Desde, $Hasta) {
	$sql = "SELECT TIMEDIFF('$Hasta', '$Desde') AS TotalHoras;";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	return substr($field['TotalHoras'], 0, 5);
}

//
function sumarHoras($Hora1, $Hora2) {
	list($h1, $m1, $s1) = split("[:]", $Hora1);
	list($h2, $m2, $s2) = split("[:]", $Hora2);
	$totalh = intval($h1) + intval($h2);
	$totalm = intval($m1) + intval($m2);
	if ($totalm >= 60) {
		$hsumar = intval($totalm / 60);
		$totalh += $hsumar;
		$totalm = $totalm - (60 * $hsumar);
	}
	return "$totalh:$totalm";
}

//
function sumarHorasXDias($Hora, $Dias) {
	$totalh = 0;
	$totalm = 0;
	for ($i=0; $i < $Dias; $i++) {
		list($h, $m, $s) = explode(':', $Hora);
		$totalh += intval($h);
		$totalm += intval($m);
		if ($totalm >= 60) {
			$hsumar = intval($totalm / 60);
			$totalh += $hsumar;
			$totalm = $totalm - (60 * $hsumar);
		}
	}
	return "$totalh:$totalm";
}

//
function sumarHorasArray($HoraArray) {
	$totalh = 0;
	$totalm = 0;
	foreach ($HoraArray as $Hora) {
		list($h, $m, $s) = explode(':', $Hora);
		$totalh += intval($h);
		$totalm += intval($m);
		if ($totalm >= 60) {
			$hsumar = intval($totalm / 60);
			$totalh += $hsumar;
			$totalm = $totalm - (60 * $hsumar);
		}
	}
	return "$totalh:$totalm";
}

//	
function getDiasHabiles($desde, $hasta) {
	$dias_completos = getFechaDias($desde, $hasta);
	$dias_feriados = getDiasFeriados($desde, $hasta);
	$dia_semana = getDiaSemana($desde);
	$dias_habiles = 0;
	for ($i=0; $i<=$dias_completos; $i++) {
		if ($dia_semana >= 1 && $dia_semana <= 5) $dias_habiles++;
		$dia_semana++;
		if ($dia_semana == 7) $dia_semana = 0;
	}
	$dias_habiles -= $dias_feriados;
	return $dias_habiles;
}

//	
function getFechaDias($fechad, $fechah) {
	list($dd, $md, $ad) = SPLIT( '[/.-]', $fechad);	$desde = "$ad-$md-$dd";
	list($dh, $mh, $ah) = SPLIT( '[/.-]', $fechah);	$hasta = "$ah-$mh-$dh";
	$sql = "SELECT DATEDIFF('$hasta', '$desde');";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	$field = mysql_fetch_array($query);
	return intval($field[0]);
}

//	
function getFechaDiasFull($fechad, $fechah) {
	list($dd, $md, $ad) = SPLIT( '[/.-]', $fechad);	$desde = "$ad-$md-$dd";
	list($dh, $mh, $ah) = SPLIT( '[/.-]', $fechah);	$hasta = "$ah-$mh-$dh";
	$sql = "SELECT DATEDIFF('$hasta', '$desde');";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	$field = mysql_fetch_array($query);
	return intval($field[0] + 1);
}

//	obtener fecha fin a partir de una fecha inicial + dias
function obtenerFechaFin($FechaInicial, $Dias) {
	$sql = "SELECT ADDDATE('".formatFechaAMD($FechaInicial)."', ".intval($Dias-1).") AS FechaResultado";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	$field = mysql_fetch_array($query);
	return formatFechaDMA($field['FechaResultado']);
}

//	obtener fecha fin a partir de una fecha inicial + dias
function fechaFin($FechaInicial, $Dias) {
	$sql = "SELECT ADDDATE('".formatFechaAMD($FechaInicial)."', ".intval($Dias).") AS FechaResultado";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	$field = mysql_fetch_array($query);
	return formatFechaDMA($field['FechaResultado']);
}

//	
function getFechaFinHabiles($fecha, $dias) {
	$finicio = $fecha;
	$ffin = fechaFinHabiles($finicio, $dias);
	$feriados = getDiasFeriados($finicio, $ffin);
	while($feriados > 0) {
		$finicio = fechaFinHabiles($ffin, 2);
		$ffin = fechaFinHabiles($finicio, $feriados);
		$feriados = getDiasFeriados($finicio, $ffin);
	}
	return $ffin;
}

//	
function fechaFinHabiles($fecha, $dias) {
	if ($dias==1 || $dias==0) $dias=0; else $dias--;
	$sumar=true;
	$dia_semana=getDiaSemana($fecha);
	list($dia, $mes, $anio)=SPLIT('[/.-]', $fecha);
	$d=(int) $dia; $m=(int) $mes; $a=(int) $anio;
	for ($i=1; $i<=$dias;) {
		$dia_semana++;
		if ($dia_semana==8) $dia_semana=1;
		if ($dia_semana>=1 && $dia_semana<=5) $i++;
		$d++;
		$dias_mes=getDiasMes("$a-$m");
		if ($d>$dias_mes) { 
			$d=1; $m++; 
			if ($m>12) { $m=1; $a++; }
		}
	}
	if ($d<10) $d="0$d";
	if ($m<10) $m="0$m";
	return "$d-$m-$a";
}

//	
function getDiasFeriados($fdesde, $fhasta) {
	list($dia_desde, $mes_desde, $anio_desde)=SPLIT('[/.-]', $fdesde); $DiaDesde = "$mes_desde-$dia_desde";
	list($dia_hasta, $mes_hasta, $anio_hasta)=SPLIT('[/.-]', $fhasta); $DiaHasta = "$mes_hasta-$dia_hasta";
	$sql = "SELECT * 
			FROM rh_feriados 
			WHERE 
				(FlagVariable = 'S' AND
				 (AnioFeriado = '".$anio_desde."' OR AnioFeriado = '".$anio_hasta."') AND 
				 (DiaFeriado >= '".$DiaDesde."' AND DiaFeriado <= '".$DiaHasta."')) OR
				(FlagVariable = 'N' AND DiaFeriado >= '".$DiaDesde."' AND DiaFeriado <= '".$DiaHasta."')";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	$rows = mysql_num_rows($query);	$dias_feriados = 0;
	while ($field = mysql_fetch_array($query)) {
		list($mes, $dia) = SPLIT('[/.-]', $field['DiaFeriado']);
		if ($field['AnioFeriado'] == "") $anio = $anio_desde; else $anio = $field['AnioFeriado'];
		$fecha = "$dia-$mes-$anio";
		$dia_semana = getDiaSemana($fecha);
		if ($dia_semana >= 1 && $dia_semana <= 5) $dias_feriados++;
		if ($anio_desde != $anio_hasta) {
			if ($field['AnioFeriado'] == "") $anio = $anio_hasta; else $anio = $field['AnioFeriado'];
			$fecha = "$dia-$mes-$anio";
			$dia_semana = getDiaSemana($fecha);
			if ($dia_semana >= 1 && $dia_semana <= 5) $dias_feriados++;
		}
	}
	return intval($dias_feriados);
}

//	
function diasSemanaXFecha($fecha1, $fecha2, $d) {
	$fecha1 = formatFechaAMD($fecha1);
	$fecha2 = formatFechaAMD($fecha2);
	$l[1] = 'Mon';
	$l[2] = 'Tue';
	$l[3] = 'Wed';
	$l[4] = 'Thu';
	$l[5] = 'Fri';
	$l[6] = 'Sat';
	$l[7] = 'Sun';
	$dias = 0;
	$fecha1 = strtotime($fecha1);
	$fecha2 = strtotime($fecha2);
	for($fecha1; $fecha1 <= $fecha2; $fecha1 = strtotime('+1 day ' . date('Y-m-d', $fecha1))) {
		if((strcmp(date('D', $fecha1), $l[$d]) == 0)) {
			++$dias;
		}
	}
	return intval($dias);
}

/**
 * Validadr una fecha
 *
 * @param (string) $date
 * @param (string) $format
 *
 * @return	boolean
 */
if ( ! function_exists('validateDate')) {
	function validateDate($date, $format = 'Y-m-d H:i:s') {
	    $d = DateTime::createFromFormat($format, $date);
	    return $d && $d->format($format) == $date;
	}
}
?>