<?php
session_start();
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion.sql", "w+");
//	fwrite($__archivo, $sql.";\n\n");
///////////////////////////////////////////////////////////////////////////////
//	GRUPO OCUPACIONAL (NUEVO, MODIFICAR, ELIMINAR)
///////////////////////////////////////////////////////////////////////////////
if ($modulo == "permisos") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-----------------
		//	inserto
		$Anio = substr($PeriodoContable, 0, 4);
		$Secuencia = codigo('rh_permisos', 'Secuencia', 6, array('Anio'), array($Anio));
		$CodPermiso = $Anio.$Secuencia;
		$sql = "INSERT INTO rh_permisos
				SET
					CodPermiso = '".$CodPermiso."',
					Anio = '".$Anio."',
					Secuencia = '".$Secuencia."',
					CodPersona = '".$CodPersona."',
					Aprobador = '".$Aprobador."',
					FechaIngreso = '".formatFechaAMD($FechaIngreso)."',
					PeriodoContable = '".$PeriodoContable."',
					TipoFalta = '".$TipoFalta."',
					DescFalta = '".changeUrl($DescFalta)."',
					TipoPermiso = '".$TipoPermiso."',
					FechaDesde = '".formatFechaAMD($FechaDesde)."',
					FechaHasta = '".formatFechaAMD($FechaHasta)."',
					HoraDesde = '".formatHora24($HoraDesde)."',
					HoraHasta = '".formatHora24($HoraHasta)."',
					ObsMotivo = '".changeUrl($ObsMotivo)."',
					FlagRemunerado = '".$FlagRemunerado."',
					FlagJustificativo = '".$FlagJustificativo."',
					FlagExento = '".$FlagExento."',
					TotalDias = '".$TotalDias."',
					TotalHoras = '".$TotalHoras."',
					TotalMinutos = '".$TotalMinutos."',
					TotalFecha = '".$TotalFecha."',
					TotalTiempo = '".$TotalTiempo."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	modificar
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "UPDATE rh_permisos
				SET
					CodPersona = '".$CodPersona."',
					Aprobador = '".$Aprobador."',
					TipoFalta = '".$TipoFalta."',
					DescFalta = '".changeUrl($DescFalta)."',
					TipoPermiso = '".$TipoPermiso."',
					FechaDesde = '".formatFechaAMD($FechaDesde)."',
					FechaHasta = '".formatFechaAMD($FechaHasta)."',
					HoraDesde = '".formatHora24($HoraDesde)."',
					HoraHasta = '".formatHora24($HoraHasta)."',
					ObsMotivo = '".changeUrl($ObsMotivo)."',
					TotalDias = '".$TotalDias."',
					TotalHoras = '".$TotalHoras."',
					TotalMinutos = '".$TotalMinutos."',
					TotalFecha = '".$TotalFecha."',
					TotalTiempo = '".$TotalTiempo."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodPermiso = '".$CodPermiso."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	aprobar
	elseif ($accion == "aprobar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "UPDATE rh_permisos
				SET
					FechaAprobado = '".formatFechaAMD($FechaAprobado)."',
					ObsAprobado = '".changeUrl($ObsAprobado)."',
					FlagRemunerado = '".$FlagRemunerado."',
					FlagJustificativo = '".$FlagJustificativo."',
					FlagExento = '".$FlagExento."',
					Estado = 'A',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodPermiso = '".$CodPermiso."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	anular
	elseif ($accion == "anular") {
		mysql_query("BEGIN");
		//	-----------------
		if ($Estado == "A") $NuevoEstado = "P";
		elseif ($Estado == "P") $NuevoEstado = "N";
		//	actualizo
		$sql = "UPDATE rh_permisos
				SET
					Estado = '".$NuevoEstado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodPermiso = '".$CodPermiso."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
elseif ($modulo == "ajax") {
	//	calculo total fecha
	if ($accion == "getTotalFecha") {
		##	horario
		$DiaSemana = getWeekDay($FechaDesde);
		$sql = "SELECT *
				FROM rh_horariolaboraldet
				WHERE
					CodHorario = '".$CodHorario."' AND
					Dia = '".$DiaSemana."' AND
					FlagLaborable = 'S'";
		$field_horario = getRecord($sql);
		##	horario del dia
		$HoraDesde = formatHora12($field_horario['Entrada1']);
		if ($field_horario['Salida2'] == '00:00:00') {
			$HoraHasta = formatHora12($field_horario['Salida1']);
		} else {
			$HoraHasta = formatHora12($field_horario['Salida2']);
			$Turno2 = getDiffHora($field_horario['Entrada2'], $field_horario['Salida2']);
		}
		$Turno1 = getDiffHora($field_horario['Entrada1'], $field_horario['Salida1']);
		$TotalTiempo = sumarHoras($Turno1, $Turno2);
		$TotalFecha = getDiasHabiles($FechaDesde, $FechaHasta);
		list($horas, $minutos) = explode(":", $TotalTiempo);
		if ($TotalFecha > 0) {
			$TotalHoras = $horas * $TotalFecha;
			$TotalMinutos = $minutos * $TotalFecha;
		} else {
			$TotalHoras = $horas;
			$TotalMinutos = $minutos;
		}
		##
		echo "$TotalFecha|$TotalTiempo|$HoraDesde|$HoraHasta|$TotalHoras|$TotalMinutos";
	}
	//	calculo total tiempo
	elseif ($accion == "getTotalTiempo") {
		##	horario
		$DiaSemana = getWeekDay($FechaDesde);
		$sql = "SELECT *
				FROM rh_horariolaboraldet
				WHERE
					CodHorario = '".$CodHorario."' AND
					Dia = '".$DiaSemana."' AND
					FlagLaborable = 'S'";
		$field_horario = getRecord($sql);
		##	
		$Desde = formatHora24($HoraDesde, 1);
		$Hasta = formatHora24($HoraHasta, 1);
		##	si la hora de inicio y fin estan en el mismo turno
		if ((($Desde >= $field_horario['Entrada1'] && $Desde <= $field_horario['Salida1']) && ($Hasta >= $field_horario['Entrada1'] && $Hasta <= $field_horario['Salida1'])) || (($Desde >= $field_horario['Entrada2'] && $Desde <= $field_horario['Salida2']) && ($Hasta >= $field_horario['Entrada2'] && $Hasta <= $field_horario['Salida2']))) {
			$Inicio1 = $Desde;
			$Fin1 = $Hasta;
		}
		elseif ((($Desde >= $field_horario['Entrada1'] && $Desde <= $field_horario['Salida1']) || ($Desde >= $field_horario['Entrada2'] && $Desde <= $field_horario['Salida2'])) && (($Hasta >= $field_horario['Entrada1'] && $Hasta <= $field_horario['Salida1']) || ($Hasta >= $field_horario['Entrada2'] && $Hasta <= $field_horario['Salida2']))) {
			$Inicio1 = $Desde;
			$Fin1 = $field_horario['Salida1'];
			$Inicio2 = $field_horario['Entrada2'];
			$Fin2 = $Hasta;
		}
		$Turno1 = getDiffHora($Inicio1, $Fin1);
		$Turno2 = getDiffHora($Inicio2, $Fin2);
		$TotalTiempo = sumarHoras($Turno1, $Turno2);
		if ($field_horario['Salida2'] == '00:00:00' && $Desde == $field_horario['Entrada1'] && $Hasta == $field_horario['Salida1']) $TotalFecha = 1;
		elseif ($field_horario['Salida2'] != '00:00:00' && $Desde == $field_horario['Entrada1'] && $Hasta == $field_horario['Salida2']) $TotalFecha = 1;
		else $TotalFecha = 0;
		list($horas, $minutos) = explode(":", $TotalTiempo);
		if ($TotalFecha > 0) {
			$TotalHoras = $horas * $TotalFecha;
			$TotalMinutos = $minutos * $TotalFecha;
		} else {
			$TotalHoras = $horas;
			$TotalMinutos = $minutos;
		}
		echo "$TotalTiempo|$TotalFecha|$TotalHoras|$TotalMinutos";
	}
	//	valido modificar
	elseif ($accion == "permisos_modificar") {
		$sql = "SELECT Estado FROM rh_permisos WHERE CodPermiso = '".$codigo."'";
		$Estado = getVar3($sql);
		if ($Estado != "P") die("No puede modificar este registro");
	}
	//	valido aprobar
	elseif ($accion == "permisos_aprobar") {
		##
		$sql = "SELECT Aprobador, Estado FROM rh_permisos WHERE CodPermiso = '".$codigo."'";
		$field_permiso = getRecord($sql);
		if ($field_permiso['Aprobador'] != $_SESSION["CODPERSONA_ACTUAL"]) die("No esta autorizado para aprobar este registro");
		elseif ($field_permiso['Estado'] != "P") die("No puede aprobar este registro");
	}
	//	valido anular
	elseif ($accion == "permisos_anular") {
		$sql = "SELECT Estado FROM rh_permisos WHERE CodPermiso = '".$codigo."'";
		$Estado = getVar3($sql);
		if ($Estado == "N") die("No puede anular este registro");
	}
}
?>