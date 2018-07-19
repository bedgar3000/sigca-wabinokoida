<?php
include("../../lib/fphp.php");
include("fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion.sql", "w+");
///////////////////////////////////////////////////////////////////////////////
//	PARA AJAX
///////////////////////////////////////////////////////////////////////////////
//	desrrollo de carreras y sucesion
if ($modulo == "desarrollo_carreras") {
	//	nuevo registro
	if ($accion== "nuevo") {
		mysql_query("BEGIN");
		//	-------------------
		//	valido
		$sql = "SELECT *
				FROM rh_asociacioncarreras
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					CodPersona = '".$CodPersona."' AND
					Secuencia = '".$Secuencia."'";
		$query = mysql_query($sql) or die($sql.mysql_error());
		if (mysql_num_rows($query) != 0) die("Ya existe un registro del empleado con la Evaluaci&oacute;n seleccionada");
	
		//	genero codigo
		$Codigo = getCodigo_3("rh_asociacioncarreras", "Codigo", "CodOrganismo", "Secuencia", $CodOrganismo, $Secuencia, 4);
		
		//	inserto
		$sql = "INSERT INTO rh_asociacioncarreras
				SET
					CodOrganismo = '".$CodOrganismo."',
					Secuencia = '".$Secuencia."',
					Codigo = '".$Codigo."',
					CodPersona = '".$CodPersona."',
					CodCargo = '".$CodCargo."',
					DescripCargo = '".changeUrl($DescripCargo)."',
					CodDependencia = '".$CodDependencia."',
					Periodo = '".$Periodo."',
					IniciadoPor = '".$IniciadoPor."',
					FechaIniciado = '".formatFechaAMD($FechaIniciado)."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		echo "|$Codigo";
		//	-------------------
		mysql_query("COMMIT");
	}
	
	//	actualizar registro
	elseif ($accion== "modificar") {
		mysql_query("BEGIN");
		//	-------------------
		//	capacitacion tecnica
		$sql = "DELETE FROM rh_asociacioncarrerascaptecnica
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					Secuencia = '".$Secuencia."' AND
					Codigo = '".$Codigo."'";
		$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		$_Linea = 0;
		$captecnica = split(";char:tr;", $detalles_captecnica);
		foreach ($captecnica as $_Descripcion) {
			//	inserto
			$sql = "INSERT INTO rh_asociacioncarrerascaptecnica
					SET
						CodOrganismo = '".$CodOrganismo."',
						Secuencia = '".$Secuencia."',
						Codigo = '".$Codigo."',
						Linea = '".++$_Linea."',
						Descripcion = '".changeUrl($_Descripcion)."',
						UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
						UltimaFecha = NOW()";
			$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		}
		
		//	habilidades
		$sql = "DELETE FROM rh_asociacioncarrerashabilidad
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					Secuencia = '".$Secuencia."' AND
					Codigo = '".$Codigo."'";
		$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		$_Linea = 0;
		$habilidad = split(";char:tr;", $detalles_habilidad);
		foreach ($habilidad as $_detalle) {
			list($_Tipo, $_Descripcion) = split(";char:td;", $_detalle);
			//	inserto
			$sql = "INSERT INTO rh_asociacioncarrerashabilidad
					SET
						CodOrganismo = '".$CodOrganismo."',
						Secuencia = '".$Secuencia."',
						Codigo = '".$Codigo."',
						Linea = '".++$_Linea."',
						Tipo = '".$_Tipo."',
						Descripcion = '".changeUrl($_Descripcion)."',
						UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
						UltimaFecha = NOW()";
			$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		}
		
		//	evaluaciones
		$sql = "DELETE FROM rh_asociacioncarrerasevaluacion
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					Secuencia = '".$Secuencia."' AND
					Codigo = '".$Codigo."'";
		$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		$_Linea = 0;
		$evaluacion = split(";char:tr;", $detalles_evaluacion);
		foreach ($evaluacion as $_Descripcion) {
			//	inserto
			$sql = "INSERT INTO rh_asociacioncarrerasevaluacion
					SET
						CodOrganismo = '".$CodOrganismo."',
						Secuencia = '".$Secuencia."',
						Codigo = '".$Codigo."',
						Linea = '".++$_Linea."',
						Descripcion = '".changeUrl($_Descripcion)."',
						UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
						UltimaFecha = NOW()";
			$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		}
		
		//	metas
		$sql = "DELETE FROM rh_asociacioncarrerasmetas
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					Secuencia = '".$Secuencia."' AND
					Codigo = '".$Codigo."'";
		$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		$_Linea = 0;
		$metas = split(";char:tr;", $detalles_metas);
		foreach ($metas as $_Descripcion) {
			//	inserto
			$sql = "INSERT INTO rh_asociacioncarrerasmetas
					SET
						CodOrganismo = '".$CodOrganismo."',
						Secuencia = '".$Secuencia."',
						Codigo = '".$Codigo."',
						Linea = '".++$_Linea."',
						Descripcion = '".changeUrl($_Descripcion)."',
						UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
						UltimaFecha = NOW()";
			$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		}
		//	-------------------
		mysql_query("COMMIT");
	}
	
	//	terminar
	elseif ($accion== "terminar") {
		mysql_query("BEGIN");
		//	-------------------
		//	actualizo
		$sql = "UPDATE rh_asociacioncarreras
				SET
					TerminadoPor = '".$TerminadoPor."',
					FechaTerminado = '".formatFechaAMD($FechaTerminado)."',
					Estado = 'TE',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		echo "|$Codigo";	die();
		//	-------------------
		mysql_query("COMMIT");
	}
}

//	solicitud de vacaciones
elseif ($modulo == "vacaciones") {
	$CodTipoNom = getVar3("SELECT CodTipoNom FROM mastempleado WHERE CodPersona = '".$CodPersona."'");
	
	//	verifico el estado del empelado
	if ($accion != "anular") {
		$_Estado = getVar2("mastempleado", "Estado", array("CodPersona"), array($CodPersona));
		if ($_Estado != "A") die("No se puede generar Solicitud de Vacaciones para Empleados <strong>Inactivos</strong>");
	}
	
	//	nuevo registro
	if ($accion== "nuevo") {
		mysql_query("BEGIN");
		//	-----------------
		//	valido
		$sql = "SELECT * FROM rh_vacacionsolicitud WHERE CodPersona = '".$CodPersona."' AND (Estado = 'PE' OR Estado = 'RV' OR Estado = 'CO')";
		$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		if (mysql_num_rows($query) != 0) die("Existe una solicitud pendiente para este trabajador");
		
		//	genero codigo
		$CodSolicitud = getCodigo_2("rh_vacacionsolicitud", "CodSolicitud", "Anio", $Anio, 6);
		$NroSolicitud = codigo("rh_vacacionsolicitud", "NroSolicitud", 6, array("Anio","Tipo"), array($Anio,$Tipo));
		
		//	inserto
		$sql = "INSERT INTO rh_vacacionsolicitud
				SET
					Anio = '".$Anio."',
					CodSolicitud = '".$CodSolicitud."',
					NroSolicitud = '".$NroSolicitud."',
					CodPersona = '".$CodPersona."',
					Tipo = '".$Tipo."',
					Fecha = '".formatFechaAMD($Fecha)."',
					Periodo = '".$Periodo."',
					FechaSalida = '".formatFechaAMD($FechaSalida)."',
					FechaTermino = '".formatFechaAMD($FechaTermino)."',
					NroDias = '".setNumero($NroDias)."',
					FechaIncorporacion = '".formatFechaAMD($FechaIncorporacion)."',
					Motivo = '".changeUrl($Motivo)."',
					CreadoPor = '".$CreadoPor."',
					FechaCreacion = NOW(),
					CodOrganismo = '".$CodOrganismo."',
					CodDependencia = '".$CodDependencia."',
					SolicitudRelacionada = '$AnioRelacionada-$CodSolicitudRelacionada',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	periodos vacacionales
		$_Secuencia = 0;
		$detalle = split(";char:tr;", $detalles);
		foreach ($detalle as $linea) {
			list($_NroPeriodo, $_FlagUtilizarPeriodo, $_NroDias, $_FechaInicio, $_FechaFin, $_DiasDerecho, $_DiasUsados, $_DiasPendientes, $_Observaciones, $_SecuenciaLinea) = split(";char:td;", $linea);
			$_FechaIncorporacion = getFechaFinHabiles($_FechaFin, 2);
			//	inserto
			$sql = "INSERT INTO rh_vacacionsolicituddetalle
					SET
						Anio = '".$Anio."',
						CodSolicitud = '".$CodSolicitud."',
						Secuencia = '".++$_Secuencia."',
						CodPersona = '".$CodPersona."',
						NroPeriodo = '".$_NroPeriodo."',
						CodTipoNom = '".$CodTipoNom."',
						Periodo = '".$Periodo."',
						FechaInicio = '".formatFechaAMD($_FechaInicio)."',
						FechaFin = '".formatFechaAMD($_FechaFin)."',
						FechaIncorporacion = '".formatFechaAMD($_FechaIncorporacion)."',
						NroDias = '".setNumero($_NroDias)."',
						DiasDerecho = '".setNumero($_DiasDerecho)."',
						DiasPendientes = '".setNumero($_DiasPendientes)."',
						DiasUsados = '".setNumero($_DiasUsados)."',
						FlagUtilizarPeriodo = '".$_FlagUtilizarPeriodo."',
						UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
						UltimaFecha = NOW()";
			$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		}
		echo "|$Anio-$NroSolicitud";
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	modificar registro
	elseif ($accion== "modificar") {
		mysql_query("BEGIN");
		//	-----------------
		if ($Estado == "AP" && $Tipo == "G") {
			//	modifico
			$sql = "UPDATE rh_vacacionsolicitud
					SET
						Documento = '".changeUrl($Documento)."',
						Observaciones = '".changeUrl($Observaciones)."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()
					WHERE
						Anio = '".$Anio."' AND
						CodSolicitud = '".$CodSolicitud."'";
			$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			
			//	periodos vacacionales
			$_Secuencia = 0;
			$detalle = split(";char:tr;", $detalles);
			foreach ($detalle as $linea) {
				list($_NroPeriodo, $_FlagUtilizarPeriodo, $_NroDias, $_FechaInicio, $_FechaFin, $_DiasDerecho, $_DiasUsados, $_DiasPendientes, $_Observaciones, $_SecuenciaLinea) = split(";char:td;", $linea);
				//	inserto
				$sql = "UPDATE rh_vacacionsolicituddetalle
						SET
							Observaciones = '".changeUrl($_Observaciones)."',
							UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
							UltimaFecha = NOW()
						WHERE
							Anio = '".$Anio."' AND
							CodSolicitud = '".$CodSolicitud."' AND
							Secuencia = '".$_SecuenciaLinea."'";
				$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			}
		}
		else {
			//	modifico
			$sql = "UPDATE rh_vacacionsolicitud
					SET
						FechaSalida = '".formatFechaAMD($FechaSalida)."',
						FechaTermino = '".formatFechaAMD($FechaTermino)."',
						NroDias = '".setNumero($NroDias)."',
						FechaIncorporacion = '".formatFechaAMD($FechaIncorporacion)."',
						Motivo = '".changeUrl($Motivo)."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()
					WHERE
						Anio = '".$Anio."' AND
						CodSolicitud = '".$CodSolicitud."'";
			$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			
			//	periodos vacacionales
			$sql = "DELETE FROM rh_vacacionsolicituddetalle
					WHERE
						Anio = '".$Anio."' AND
						CodSolicitud = '".$CodSolicitud."'";
			$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			$_Secuencia = 0;
			$detalle = split(";char:tr;", $detalles);
			foreach ($detalle as $linea) {
				list($_NroPeriodo, $_FlagUtilizarPeriodo, $_NroDias, $_FechaInicio, $_FechaFin, $_DiasDerecho, $_DiasUsados, $_DiasPendientes, $_Observaciones) = split(";char:td;", $linea);
				$_FechaIncorporacion = getFechaFinHabiles($_FechaFin, 2);
				//	inserto
				$sql = "INSERT INTO rh_vacacionsolicituddetalle
						SET
							Anio = '".$Anio."',
							CodSolicitud = '".$CodSolicitud."',
							Secuencia = '".++$_Secuencia."',
							CodPersona = '".$CodPersona."',
							NroPeriodo = '".$_NroPeriodo."',
							CodTipoNom = '".$CodTipoNom."',
							Periodo = '".$Periodo."',
							FechaInicio = '".formatFechaAMD($_FechaInicio)."',
							FechaFin = '".formatFechaAMD($_FechaFin)."',
							FechaIncorporacion = '".formatFechaAMD($_FechaIncorporacion)."',
							NroDias = '".setNumero($_NroDias)."',
							DiasDerecho = '".setNumero($_DiasDerecho)."',
							DiasPendientes = '".setNumero($_DiasPendientes)."',
							DiasUsados = '".setNumero($_DiasUsados)."',
							FlagUtilizarPeriodo = '".$_FlagUtilizarPeriodo."',
							Observaciones = '".changeUrl($_Observaciones)."',
							UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
							UltimaFecha = NOW()";
				$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			}
		}
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	revisar registro
	elseif ($accion== "revisar") {
		//	modifico
		$sql = "UPDATE rh_vacacionsolicitud
				SET
					Estado = 'RV',
					RevisadoPor = '".$RevisadoPor."',
					FechaRevision = NOW(),
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					Anio = '".$Anio."' AND
					CodSolicitud = '".$CodSolicitud."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	}
	
	//	conformar registro
	elseif ($accion== "conformar") {
		//	modifico
		$sql = "UPDATE rh_vacacionsolicitud
				SET
					Estado = 'CO',
					Documento = '".$Documento."',
					ConformadoPor = '".$ConformadoPor."',
					FechaConformacion = NOW(),
					Observaciones = '".changeUrl($Observaciones)."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					Anio = '".$Anio."' AND
					CodSolicitud = '".$CodSolicitud."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	periodos vacacionales
		$detalle = split(";char:tr;", $detalles);
		foreach ($detalle as $linea) {
			list($_NroPeriodo, $_FlagUtilizarPeriodo, $_NroDias, $_FechaInicio, $_FechaFin, $_DiasDerecho, $_DiasUsados, $_DiasPendientes, $_Observaciones, $_SecuenciaLinea) = split(";char:td;", $linea);
			//	actualizo observaciones
			$sql = "UPDATE rh_vacacionsolicituddetalle
					SET
						Observaciones = '".changeUrl($_Observaciones)."',
						UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
						UltimaFecha = NOW()
					WHERE
						Anio = '".$Anio."' AND
						CodSolicitud = '".$CodSolicitud."' AND
						Secuencia = '".$_SecuenciaLinea."'";
			$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		}
	}
	
	//	aprobar registro
	elseif ($accion== "aprobar") {
		mysql_query("BEGIN");
		//	-----------------
		//	modifico
		$sql = "UPDATE rh_vacacionsolicitud
				SET
					Estado = 'CO',
					Documento = '".$Documento."',
					ConformadoPor = '".$ConformadoPor."',
					FechaConformacion = NOW(),
					Observaciones = '".changeUrl($Observaciones)."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					Anio = '".$Anio."' AND
					CodSolicitud = '".$CodSolicitud."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	periodos vacacionales
		$detalle = split(";char:tr;", $detalles);
		foreach ($detalle as $linea) {
			list($_NroPeriodo, $_FlagUtilizarPeriodo, $_NroDias, $_FechaInicio, $_FechaFin, $_DiasDerecho, $_DiasUsados, $_DiasPendientes, $_Observaciones, $_SecuenciaLinea) = split(";char:td;", $linea);
			//	actualizo observaciones
			$sql = "UPDATE rh_vacacionsolicituddetalle
					SET
						Observaciones = '".changeUrl($_Observaciones)."',
						UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
						UltimaFecha = NOW()
					WHERE
						Anio = '".$Anio."' AND
						CodSolicitud = '".$CodSolicitud."' AND
						Secuencia = '".$_SecuenciaLinea."'";
			$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		}
		
		//	consulto empleado
		$sql = "SELECT CodTipoNom FROM mastempleado WHERE CodPersona = '".$CodPersona."'" ;
		$query_empleado = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		if (mysql_num_rows($query_empleado) != 0) $field_empleado = mysql_fetch_array($query_empleado);
		
		//	genero codigo
		$NroOtorgamiento = getCodigo_2("rh_vacacionsolicitud", "NroOtorgamiento", "Anio", $Anio, 6);
		
		//	actualizo solicitud
		$sql = "UPDATE rh_vacacionsolicitud
				SET
					NroOtorgamiento = '".$NroOtorgamiento."',
					Estado = 'AP',
					AprobadoPor = '".$AprobadoPor."',
					FechaAprobacion = NOW(),
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					Anio = '".$Anio."' AND
					CodSolicitud = '".$CodSolicitud."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	periodos vacacionales
		$detalle = split(";char:tr;", $detalles);
		foreach ($detalle as $linea) {
			list($_NroPeriodo, $_FlagUtilizarPeriodo, $_NroDias, $_FechaInicio, $_FechaFin, $_DiasDerecho, $_DiasUsados, $_DiasPendientes, $_Observaciones) = split(";char:td;", $linea);			
			//	inserto utilizacion
			$_Secuencia = getCodigo_3("rh_vacacionutilizacion", "Secuencia", "CodPersona", "NroPeriodo", $CodPersona, $_NroPeriodo, 2);
			$_Secuencia = intval($_Secuencia);
			$sql = "INSERT INTO rh_vacacionutilizacion
					SET
						Secuencia = '".$_Secuencia."',
						CodPersona = '".$CodPersona."',
						NroPeriodo = '".$_NroPeriodo."',
						CodTipoNom = '".$field_empleado['CodTipoNom']."',
						FechaInicio = '".formatFechaAMD($_FechaInicio)."',
						FechaFin = '".formatFechaAMD($_FechaFin)."',
						TipoUtilizacion = '".$Tipo."',
						DiasUtiles = '".setNumero($_NroDias)."',
						Anio = '".$Anio."',
						CodSolicitud = '".$CodSolicitud."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		}
		
		//	actualizo periodos vacacionales
		actualizarPeriodosVacacionales($CodPersona);
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	anular registro
	elseif ($accion== "anular") {
		if ($Estado == "AP") {
			$EstadoNuevo = "PE";
			##	elimino de utilizacion
			$sql = "DELETE FROM rh_vacacionutilizacion
					WHERE
						Anio = '".$Anio."' AND
						CodSolicitud = '".$CodSolicitud."'";
			$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		}
		elseif ($Estado == "RV") $EstadoNuevo = "PE";
		elseif ($Estado == "CO") $EstadoNuevo = "PE";
		elseif ($Estado == "PE") $EstadoNuevo = "AN";
		
		//	modifico
		$sql = "UPDATE rh_vacacionsolicitud
				SET
					Estado = '".$EstadoNuevo."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					Anio = '".$Anio."' AND
					CodSolicitud = '".$CodSolicitud."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	actualizo periodos vacacionales
		actualizarPeriodosVacacionales($CodPersona);
	}
}

//	requerimientos
elseif ($modulo == "requerimientos") {
	//	nuevo registro
	if ($accion== "nuevo") {
		//	genero codigo
		$Requerimiento = getCodigo_2("rh_requerimiento", "Requerimiento", "CodOrganismo", $CodOrganismo, 6);
		
		//	inserto
		$sql = "INSERT INTO rh_requerimiento
				SET
					Requerimiento = '".$Requerimiento."',
					FechaSolicitud = NOW(),
					NumeroSolicitado = '".$NumeroSolicitado."',
					NumeroPendiente = '".$NumeroSolicitado."',
					CodDependencia = '".$CodDependencia."',
					CodOrganismo = '".$CodOrganismo."',
					CodPersona = '".$CodPersona."',
					Modalidad = '".$Modalidad."',
					VigenciaInicio = '".formatFechaAMD($VigenciaInicio)."',
					VigenciaFin = '".formatFechaAMD($VigenciaFin)."',
					CodCargo = '".$CodCargo."',
					Motivo = '".$Motivo."',
					TipoContrato = '".$TipoContrato."',
					FechaDesde = '".formatFechaAMD($FechaDesde)."',
					FechaHasta = '".formatFechaAMD($FechaHasta)."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	evaluaciones
		if ($evaluacion != "") {
			$detalles_evaluacion = split(";char:tr;", $evaluacion);
			foreach ($detalles_evaluacion as $linea) {
				list($_Secuencia, $_Evaluacion, $_Etapa, $_PlantillaEvaluacion) = split(";char:td;", $linea);
				//	inserto
				$sql = "INSERT INTO rh_requerimientoeval
						SET
							Requerimiento = '".$Requerimiento."',
							CodOrganismo = '".$CodOrganismo."',
							Secuencia = '".$_Secuencia."',
							Evaluacion = '".$_Evaluacion."',
							Etapa = '".$_Etapa."',
							PlantillaEvaluacion = '".$_PlantillaEvaluacion."',
							UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
							UltimaFecha = NOW()";
				$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			}
		}
		echo "|$Requerimiento";
	}
	
	//	modificar registro
	elseif ($accion== "modificar") {
		//	actualizo
		$sql = "UPDATE rh_requerimiento
				SET
					NumeroSolicitado = '".$NumeroSolicitado."',
					NumeroPendiente = '".$NumeroSolicitado."',
					CodDependencia = '".$CodDependencia."',
					Modalidad = '".$Modalidad."',
					VigenciaInicio = '".formatFechaAMD($VigenciaInicio)."',
					VigenciaFin = '".formatFechaAMD($VigenciaFin)."',
					Motivo = '".$Motivo."',
					TipoContrato = '".$TipoContrato."',
					FechaDesde = '".formatFechaAMD($FechaDesde)."',
					FechaHasta = '".formatFechaAMD($FechaHasta)."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					Requerimiento = '".$Requerimiento."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	evaluaciones
		$sql = "DELETE FROM rh_requerimientoeval
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					Requerimiento = '".$Requerimiento."'";
		$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		if ($evaluacion != "") {
			$detalles_evaluacion = split(";char:tr;", $evaluacion);
			foreach ($detalles_evaluacion as $linea) {
				list($_Secuencia, $_Evaluacion, $_Etapa, $_PlantillaEvaluacion) = split(";char:td;", $linea);
				//	inserto
				$sql = "INSERT INTO rh_requerimientoeval
						SET
							Requerimiento = '".$Requerimiento."',
							CodOrganismo = '".$CodOrganismo."',
							Secuencia = '".$_Secuencia."',
							Evaluacion = '".$_Evaluacion."',
							Etapa = '".$_Etapa."',
							PlantillaEvaluacion = '".$_PlantillaEvaluacion."',
							UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
							UltimaFecha = NOW()";
				$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			}
		}
	}
	
	//	aprobar registro
	elseif ($accion== "aprobar") {
		//	actualizo
		$sql = "UPDATE rh_requerimiento
				SET
					Estado = 'AP',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					Requerimiento = '".$Requerimiento."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	}
	
	//	terminar registro
	elseif ($accion== "finalizar") {
		//	actualizo
		$sql = "UPDATE rh_requerimiento
				SET
					Estado = 'TE',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					Requerimiento = '".$Requerimiento."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	}
	
	//	insertar candidato
	elseif ($accion== "insertar-candidato") {
		//	inicio transaccion
		mysql_query("BEGIN");
		
		//	inserto
		$sql = "INSERT INTO rh_requerimientopost
				SET
					Requerimiento = '".$Requerimiento."',
					CodOrganismo = '".$CodOrganismo."',
					TipoPostulante = '".$TipoPostulante."',
					Postulante = '".$Postulante."',
					Estado = 'P',
					Puntaje = '0.00',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	inserto evaluaciones
		$sql = "INSERT INTO rh_requerimientoevalpost (
							CodOrganismo,
							Requerimiento,
							TipoPostulante,
							Postulante,
							Secuencia,
							Calificativo,
							FlagAprobacion,
							UltimoUsuario,
							UltimaFecha
				)
						SELECT
							CodOrganismo,
							Requerimiento,
							'".$TipoPostulante."' AS TipoPostulante,
							'".$Postulante."' AS Postulante,
							Secuencia,
							'0.00' AS Calificativo,
							'N' AS FlagAprobacion,
							'".$_SESSION["USUARIO_ACTUAL"]."' AS UltimoUsuario,
							NOW() AS UltimaFecha
						FROM rh_requerimientoeval
						WHERE
							CodOrganismo = '".$CodOrganismo."' AND
							Requerimiento = '".$Requerimiento."'";
		$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		if ($TipoPostulante == "I") {
			//	consulto la persona
			$sql = "SELECT
						p.CodPersona,
						p.NomCompleto,
						e.CodEmpleado
					FROM
						mastpersonas p
						INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
					WHERE p.CodPersona = '".$Postulante."'";
		}
		elseif ($TipoPostulante == "E") {
			//	consulto la persona
			$sql = "SELECT
						p.Postulante AS CodPersona,
						CONCAT(p.Nombres, ' ', p.Apellido1, ' ', p.Apellido2) AS NomCompleto,
						p.Postulante AS CodEmpleado
					FROM rh_postulantes p
					WHERE p.Postulante = '".$Postulante."'";
		}
		$query_candidato = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		if (mysql_num_rows($query_candidato) != 0) $field_candidato = mysql_fetch_array($query_candidato);
		
		//	tr a insertar en la lista
		echo "|";
		?>
        <tr class="trListaBody" onclick="mClk(this, 'sel_candidato'); document.getElementById('frm_candidato').submit();" id="candidato_<?=$TipoPostulante.$field_candidato['CodPersona']?>">
            <th>
                <?=$nro_candidato?>
            </th>
            <td align="center">
                <input type="hidden" name="TipoPostulante" value="<?=$TipoPostulante?>" />
                <input type="hidden" name="Postulante" value="<?=$field_candidato['CodPersona']?>" />
                <?=$TipoPostulante.$field_candidato['CodEmpleado']?>
            </td>
            <td>
                <?=$field_candidato['NomCompleto']?>
            </td>
            <td>
                <input type="text" name="Puntaje" class="cell2" style="text-align:right;" value="0,00" readonly="readonly" />
            </td>
            <td>
                <?=printValores("ESTADO-POSTULANTE", "P")?>
            </td>
        </tr>
        <?php
		
		mysql_query("COMMIT");
		//	fin transaccion
	}
	
	//	borrar candidato
	elseif ($accion== "borrar-candidato") {
		//	inicio transaccion
		mysql_query("BEGIN");
		
		$TipoPostulante = substr($seldetalle, 10, 1);
		$Postulante = substr($seldetalle, 11, 6);
		
		//	elimino
		$sql = "DELETE FROM rh_requerimientoevalpost
				WHERE
					Requerimiento = '".$Requerimiento."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					TipoPostulante = '".$TipoPostulante."' AND
					Postulante = '".$Postulante."'";
		$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	inserto evaluaciones
		$sql = "DELETE FROM rh_requerimientopost
				WHERE
					Requerimiento = '".$Requerimiento."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					TipoPostulante = '".$TipoPostulante."' AND
					Postulante = '".$Postulante."'";
		$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		mysql_query("COMMIT");
		//	fin transaccion
	}
	
	//	regitrar puntaje competencia
	elseif ($accion== "registrar-puntaje") {
		//	inicio transaccion
		mysql_query("BEGIN");
		
		//	actualizo estado del requerimiento
		$sql = "UPDATE rh_requerimiento
				SET Estado = 'EE'
				WHERE
					Requerimiento = '".$Requerimiento."' AND
					CodOrganismo = '".$CodOrganismo."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	competencias
		$sql = "DELETE FROM rh_requerimientocomp
				WHERE
					Requerimiento = '".$Requerimiento."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					TipoPostulante = '".$TipoPostulante."' AND
					Postulante = '".$Postulante."' AND
					Evaluacion = '".$Evaluacion."'";
		$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		if ($competencias != "") {
			$_Secuencia = 0;
			$detalles_competencias = split(";char:tr;", $competencias);
			foreach ($detalles_competencias as $linea) {
				list($Registro, $Puntaje) = split(";char:td;", $linea);
				list($C, $Competencia, $P) = split("[_]", $Registro);
				//	inserto
				$sql = "INSERT INTO rh_requerimientocomp
						SET
							Requerimiento = '".$Requerimiento."',
							CodOrganismo = '".$CodOrganismo."',
							TipoPostulante = '".$TipoPostulante."',
							Postulante = '".$Postulante."',
							Evaluacion = '".$Evaluacion."',
							Competencia = '".$Competencia."',
							Puntaje = '".$Puntaje."',
							UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
							UltimaFecha = NOW()";
				$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
				$TotalPuntaje += $Puntaje;
				++$_Secuencia;
			}
		}
		
		//	consulto nro de competencias
		$sql = "SELECT COUNT(*) AS Nro FROM rh_evaluacionitems WHERE Evaluacion = '".$Evaluacion."'";
		$query_count = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		if (mysql_num_rows($query_count) != 0) $field_count = mysql_fetch_array($query_count);
		
		//	actualizar calificativo de la evaluacion
		$Calificativo = $TotalPuntaje / $field_count['Nro'];
		$sql = "UPDATE rh_requerimientoevalpost
				SET
					Calificativo = '".$Calificativo."',
					UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
					UltimaFecha = NOW()
				WHERE
					Requerimiento = '".$Requerimiento."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					TipoPostulante = '".$TipoPostulante."' AND
					Postulante = '".$Postulante."' AND
					Secuencia = '".$Secuencia."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	actualizo puntaje del postulante
		$sql = "SELECT
					COUNT(*) AS Nro,
					(SELECT SUM(Calificativo)
					 FROM rh_requerimientoevalpost
					 WHERE
						Requerimiento = rep.Requerimiento AND
						CodOrganismo = rep.CodOrganismo AND
						TipoPostulante = rep.TipoPostulante AND
						Postulante = rep.Postulante) AS Calificativo
				FROM rh_requerimientoevalpost rep
				WHERE
					Requerimiento = '".$Requerimiento."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					TipoPostulante = '".$TipoPostulante."' AND
					Postulante = '".$Postulante."'";
		$query_eval = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		if (mysql_num_rows($query_eval) != 0) $field_eval = mysql_fetch_array($query_eval);
		
		//	actualizar calificativo de la evaluacion
		$Puntaje = $field_eval['Calificativo'] / $field_eval['Nro'];
		$sql = "UPDATE rh_requerimientopost
				SET
					Puntaje = '".$Puntaje."',
					UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
					UltimaFecha = NOW()
				WHERE
					Requerimiento = '".$Requerimiento."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					TipoPostulante = '".$TipoPostulante."' AND
					Postulante = '".$Postulante."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	fin transaccion
		mysql_query("COMMIT");
	}
	
	//	aprobar candidato
	elseif ($accion== "aprobar-candidato") {
		//	inicio transaccion
		mysql_query("BEGIN");
		
		//	valido
		$sql = "SELECT Estado, Puntaje
				FROM rh_requerimientopost
				WHERE
					Requerimiento = '".$Requerimiento."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					TipoPostulante = '".$TipoPostulante."' AND
					Postulante = '".$Postulante."'";
		$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
		if ($field['Estado'] == "A") die("El Candidato ya encuentra en Estado Aprobado");
		else if ($field['Estado'] == "C") die("El Candidato ya encuentra en Estado Aprobado");
		else if ($field['Puntaje'] == 0) die("No puede Aprobar un Candidato sin Puntaje");
		
		//	actualizo estado del requerimiento del postulante
		$sql = "UPDATE rh_requerimientopost
				SET Estado = 'A'
				WHERE
					Requerimiento = '".$Requerimiento."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					TipoPostulante = '".$TipoPostulante."' AND
					Postulante = '".$Postulante."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	actualizo estado de la evaluacion del requerimiento del postulante
		$sql = "UPDATE rh_requerimientoevalpost
				SET FlagAprobacion = 'S'
				WHERE
					Requerimiento = '".$Requerimiento."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					TipoPostulante = '".$TipoPostulante."' AND
					Postulante = '".$Postulante."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	actualizo estado del postulante
		if ($TipoPostulante == "E") {
			$sql = "UPDATE rh_postulantes SET Estado = 'A' WHERE Postulante = '".$Postulante."'";
			$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		}
		
		//	fin transaccion
		mysql_query("COMMIT");
	}
	
	//	descalificar candidato
	elseif ($accion== "descalificar-candidato") {
		//	inicio transaccion
		mysql_query("BEGIN");
		
		//	valido estado
		$sql = "SELECT Estado 
				FROM rh_requerimientopost
				WHERE
					Requerimiento = '".$Requerimiento."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					TipoPostulante = '".$TipoPostulante."' AND
					Postulante = '".$Postulante."'";
		$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
		if ($field['Estado'] == "D") die("El Candidato ya encuentra Descalificado");
		else if ($field['Estado'] == "C") die("No se puede descalificar un Candidato que ha sido Contratado");
		
		//	actualizo estado del requerimiento del postulante
		$sql = "UPDATE rh_requerimientopost
				SET Estado = 'D'
				WHERE
					Requerimiento = '".$Requerimiento."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					TipoPostulante = '".$TipoPostulante."' AND
					Postulante = '".$Postulante."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	actualizo estado de la evaluacion del requerimiento del postulante
		$sql = "UPDATE rh_requerimientoevalpost
				SET FlagAprobacion = 'N'
				WHERE
					Requerimiento = '".$Requerimiento."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					TipoPostulante = '".$TipoPostulante."' AND
					Postulante = '".$Postulante."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	actualizo estado del postulante
		if ($TipoPostulante == "E") {
			$sql = "UPDATE rh_postulantes SET Estado = 'P' WHERE Postulante = '".$Postulante."'";
			$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		}
		
		//	fin transaccion
		mysql_query("COMMIT");
	}
}

//	tipos de evaluacion
elseif ($modulo == "evaluacion_tipo") {
	//	nuevo registro
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-------------
		//	inserto
		$sql = "INSERT INTO rh_tipoevaluacion
				SET
					TipoEvaluacion = '".$TipoEvaluacion."',
					Descripcion = '".changeUrl($Descripcion)."',
					Estado = '".$Estado."',
					FlagSistema = '".$FlagSistema."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	grados de calificacion
		if ($detalles_grados != "") {
			$_Secuencia = 0;
			$grados = split(";char:tr;", $detalles_grados);
			foreach ($grados as $grado) {
				list($_Grado, $_Descripcion, $_PuntajeMin, $_PuntajeMax, $_Estado) = split(";char:td;", $grado);
				//	inserto
				$sql = "INSERT INTO rh_gradoscompetencia
						SET
							TipoEvaluacion = '".$TipoEvaluacion."',
							Grado = '".$_Grado."',
							Descripcion = '".$_Descripcion."',
							PuntajeMin = '".$_PuntajeMin."',
							PuntajeMax = '".$_PuntajeMax."',
							Estado = '".$_Estado."',
							UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
							UltimaFecha = NOW()";
				$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			}
		}
		//	-------------
		mysql_query("COMMIT");
		//	-------------
	}
	
	//	modificar registro
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		//	-------------
		//	inserto
		$sql = "UPDATE rh_tipoevaluacion
				SET
					Descripcion = '".changeUrl($Descripcion)."',
					Estado = '".$Estado."',
					FlagSistema = '".$FlagSistema."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE TipoEvaluacion = '".$TipoEvaluacion."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	grados de calificacion
		$sql = "DELETE FROM rh_gradoscompetencia WHERE TipoEvaluacion = '".$TipoEvaluacion."'";
		$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		if ($detalles_grados != "") {
			$_Secuencia = 0;
			$grados = split(";char:tr;", $detalles_grados);
			foreach ($grados as $grado) {
				list($_Grado, $_Descripcion, $_PuntajeMin, $_PuntajeMax, $_Estado) = split(";char:td;", $grado);
				//	inserto
				$sql = "INSERT INTO rh_gradoscompetencia
						SET
							TipoEvaluacion = '".$TipoEvaluacion."',
							Grado = '".$_Grado."',
							Descripcion = '".$_Descripcion."',
							PuntajeMin = '".$_PuntajeMin."',
							PuntajeMax = '".$_PuntajeMax."',
							Estado = '".$_Estado."',
							UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
							UltimaFecha = NOW()";
				$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			}
		}
		//	-------------
		mysql_query("COMMIT");
		//	-------------
	}
	
	//	eliminar registro
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-------------
		//	consulto
		$sql = "SELECT FlagSistema FROM rh_tipoevaluacion WHERE TipoEvaluacion = '".$registro."' AND FlagSistema = 'S'";
		$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		if (mysql_num_rows($query) != 0) die("No puede eliminar este registro (Transacci&oacute;n del Sistema)");
		
		//	elimino
		$sql = "DELETE FROM rh_gradoscompetencia WHERE TipoEvaluacion = '".$registro."'";
		$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	elimino
		$sql = "DELETE FROM rh_tipoevaluacion WHERE TipoEvaluacion = '".$registro."'";
		$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		//	-------------
		mysql_query("COMMIT");
		//	-------------
	}
}

//	evaluacion
elseif ($modulo == "evaluacion") {
	//	nuevo registro
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-------------
		//	genero codigo
		$Evaluacion = getCodigo("rh_evaluacion", "Evaluacion", 4);
		$Evaluacion = intval($Evaluacion);
		
		//	inserto
		$sql = "INSERT INTO rh_evaluacion
				SET
					Evaluacion = '".$Evaluacion."',
					TipoEvaluacion = '".$TipoEvaluacion."',
					Descripcion = '".changeUrl($Descripcion)."',
					PuntajeMin = '".$PuntajeMin."',
					PuntajeMax = '".$PuntajeMax."',
					Plantilla = '".$Plantilla."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		//	-------------
		mysql_query("COMMIT");
		//	-------------
	}
	
	//	modificar registro
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		//	-------------
		//	inserto
		$sql = "UPDATE rh_evaluacion
				SET
					TipoEvaluacion = '".$TipoEvaluacion."',
					Descripcion = '".changeUrl($Descripcion)."',
					PuntajeMin = '".$PuntajeMin."',
					PuntajeMax = '".$PuntajeMax."',
					Plantilla = '".$Plantilla."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE Evaluacion = '".$Evaluacion."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		//	-------------
		mysql_query("COMMIT");
		//	-------------
	}
	
	//	eliminar registro
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-------------
		//	elimino
		$sql = "DELETE FROM rh_evaluacion WHERE Evaluacion = '".$registro."'";
		$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		//	-------------
		mysql_query("COMMIT");
		//	-------------
	}
}

//	itesms para evaluacion
elseif ($modulo == "evaluacion_items") {
	//	nuevo registro
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-------------
		//	genero codigo
		$CodItem = getCodigo_2("rh_evaluacionitems", "CodItem", "Evaluacion", $Evaluacion, 4);
		$CodItem = intval($CodItem);
		
		//	inserto
		$sql = "INSERT INTO rh_evaluacionitems
				SET
					Evaluacion = '".$Evaluacion."',
					CodItem = '".$CodItem."',
					Descripcion = '".changeUrl($Descripcion)."',
					PuntajeMin = '".$PuntajeMin."',
					PuntajeMax = '".$PuntajeMax."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		//	-------------
		mysql_query("COMMIT");
		//	-------------
	}
	
	//	modificar registro
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		//	-------------
		//	inserto
		$sql = "UPDATE rh_evaluacionitems
				SET
					Descripcion = '".changeUrl($Descripcion)."',
					PuntajeMin = '".$PuntajeMin."',
					PuntajeMax = '".$PuntajeMax."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					Evaluacion = '".$Evaluacion."' AND
					CodItem = '".$CodItem."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		//	-------------
		mysql_query("COMMIT");
		//	-------------
	}
	
	//	eliminar registro
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-------------
		list($Evaluacion, $CodItem) = split("[.]", $registro);
		//	elimino
		$sql = "DELETE FROM rh_evaluacionitems
				WHERE
					Evaluacion = '".$Evaluacion."' AND
					CodItem = '".$CodItem."'";
		$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		//	-------------
		mysql_query("COMMIT");
		//	-------------
	}
}

//	grupos de competencia
elseif ($modulo == "competencias_grupo") {
	//	nuevo registro
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-------------
		//	inserto
		$sql = "INSERT INTO rh_evaluacionarea
				SET
					Area = '".$Area."',
					Descripcion = '".changeUrl($Descripcion)."',
					TipoEvaluacion = '".$TipoEvaluacion."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		//	-------------
		mysql_query("COMMIT");
		//	-------------
	}
	
	//	modificar registro
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		//	-------------
		//	inserto
		$sql = "UPDATE rh_evaluacionarea
				SET
					Descripcion = '".changeUrl($Descripcion)."',
					TipoEvaluacion = '".$TipoEvaluacion."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE Area = '".$Area."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		//	-------------
		mysql_query("COMMIT");
		//	-------------
	}
	
	//	eliminar registro
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-------------
		//	elimino
		$sql = "DELETE FROM rh_evaluacionarea WHERE Area = '".$registro."'";
		$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		//	-------------
		mysql_query("COMMIT");
		//	-------------
	}
}

//	competencias
elseif ($modulo == "competencias") {
	//	nuevo registro
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-------------
		//	genero codigo
		$Competencia = getCodigo("rh_evaluacionfactores", "Competencia", 3);
		$Competencia = intval($Competencia);
		
		//	inserto
		$sql = "INSERT INTO rh_evaluacionfactores
				 SET
  					Competencia = '".$Competencia."',
  					Descripcion = '".changeUrl($Descripcion)."',
  					Explicacion = '".changeUrl($Explicacion)."',
  					TipoCompetencia = '".$TipoCompetencia."',
  					Area = '".$Area."',
  					Nivel = '".$Nivel."',
  					Calificacion = '".$Calificacion."',
  					FlagPlantilla = '".$FlagPlantilla."',
  					ValorRequerido = '".$ValorRequerido."',
  					ValorMinimo = '".$ValorMinimo."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	grados de calificacion
		if ($detalles_grados != "") {
			$_Secuencia = 0;
			$grados = split(";char:tr;", $detalles_grados);
			foreach ($grados as $grado) {
				list($_TipoEvaluacion, $_Grado, $_Valor, $_Explicacion, $_Explicacion2, $_Estado) = split(";char:td;", $grado);
				//	inserto
				$sql = "INSERT INTO rh_factorvalor
						 SET
  							Secuencia = '".++$_Secuencia."',
  							Competencia = '".$Competencia."',
  							TipoEvaluacion = '".$_TipoEvaluacion."',
  							Grado = '".$_Grado."',
  							Explicacion = '".$_Explicacion."',
  							Explicacion2 = '".$_Explicacion2."',
  							Valor = '".$_Valor."',
							Estado = '".$_Estado."',
							UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
							UltimaFecha = NOW()";
				$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			}
		}
		//	-------------
		mysql_query("COMMIT");
		//	-------------
	}
	
	//	modificar registro
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		//	-------------
		//	inserto
		$sql = "UPDATE rh_evaluacionfactores
				 SET
  					Descripcion = '".changeUrl($Descripcion)."',
  					Explicacion = '".changeUrl($Explicacion)."',
  					TipoCompetencia = '".$TipoCompetencia."',
  					Area = '".$Area."',
  					Nivel = '".$Nivel."',
  					Calificacion = '".$Calificacion."',
  					FlagPlantilla = '".$FlagPlantilla."',
  					ValorRequerido = '".$ValorRequerido."',
  					ValorMinimo = '".$ValorMinimo."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				 WHERE Competencia = '".$Competencia."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	grados de calificacion
		$sql = "DELETE FROM rh_factorvalor WHERE Competencia = '".$Competencia."'";
		$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		//	grados de calificacion
		if ($detalles_grados != "") {
			$_Secuencia = 0;
			$grados = split(";char:tr;", $detalles_grados);
			foreach ($grados as $grado) {
				list($_TipoEvaluacion, $_Grado, $_Valor, $_Explicacion, $_Explicacion2, $_Estado) = split(";char:td;", $grado);
				//	inserto
				$sql = "INSERT INTO rh_factorvalor
						 SET
  							Secuencia = '".++$_Secuencia."',
  							Competencia = '".$Competencia."',
  							TipoEvaluacion = '".$_TipoEvaluacion."',
  							Grado = '".$_Grado."',
  							Explicacion = '".$_Explicacion."',
  							Explicacion2 = '".$_Explicacion2."',
  							Valor = '".$_Valor."',
							Estado = '".$_Estado."',
							UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
							UltimaFecha = NOW()";
				$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			}
		}
		//	-------------
		mysql_query("COMMIT");
		//	-------------
	}
	
	//	eliminar registro
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-------------
		//	elimino
		$sql = "DELETE FROM rh_factorvalor WHERE Competencia = '".$registro."'";
		$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	elimino
		$sql = "DELETE FROM rh_evaluacionfactores WHERE Competencia = '".$registro."'";
		$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		//	-------------
		mysql_query("COMMIT");
		//	-------------
	}
}

//	plantilla de competencias
elseif ($modulo == "competencias_plantilla") {
	//	nuevo registro
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-------------
		//	genero codigo
		$Plantilla = getCodigo("rh_evaluacionfactoresplantilla", "Plantilla", 3);
		$Plantilla = intval($Plantilla);
		
		//	inserto
		$sql = "INSERT INTO rh_evaluacionfactoresplantilla
				 SET
  					Plantilla = '".$Plantilla."',
  					Descripcion = '".changeUrl($Descripcion)."',
  					TipoEvaluacion = '".$TipoEvaluacion."',
  					FlagTipoEvaluacion = '".$FlagTipoEvaluacion."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	competencias
		if ($detalles_competencias != "") {
			$_Secuencia = 0;
			$competencias = split(";char:tr;", $detalles_competencias);
			foreach ($competencias as $competencia) {
				list($_Competencia, $_Peso, $_FactorParticipacion, $_FlagPotencial, $_FlagCompetencia, $_FlagConceptual) = split(";char:td;", $competencia);
				//	inserto
				$sql = "INSERT INTO rh_factorvalorplantilla
						 SET
  							Plantilla = '".$Plantilla."',
  							Competencia = '".$_Competencia."',
  							Peso = '".$_Peso."',
  							FactorParticipacion = '".$_FactorParticipacion."',
  							FlagPotencial = '".$_FlagPotencial."',
  							FlagCompetencia = '".$_FlagCompetencia."',
  							FlagConceptual = '".$_FlagConceptual."',
							UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
							UltimaFecha = NOW()";
				$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			}
		}
		//	-------------
		mysql_query("COMMIT");
		//	-------------
	}
	
	//	modificar registro
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		//	-------------
		//	actualizo
		$sql = "UPDATE rh_evaluacionfactoresplantilla
				 SET
  					Descripcion = '".changeUrl($Descripcion)."',
  					TipoEvaluacion = '".$TipoEvaluacion."',
  					FlagTipoEvaluacion = '".$FlagTipoEvaluacion."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE Plantilla = '".$Plantilla."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	competencias
		$sql = "DELETE FROM rh_factorvalorplantilla WHERE Plantilla = '".$Plantilla."'";
		$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		if ($detalles_competencias != "") {
			$_Secuencia = 0;
			$competencias = split(";char:tr;", $detalles_competencias);
			foreach ($competencias as $competencia) {
				list($_Competencia, $_Peso, $_FactorParticipacion, $_FlagPotencial, $_FlagCompetencia, $_FlagConceptual) = split(";char:td;", $competencia);
				//	inserto
				$sql = "INSERT INTO rh_factorvalorplantilla
						 SET
  							Plantilla = '".$Plantilla."',
  							Competencia = '".$_Competencia."',
  							Peso = '".$_Peso."',
  							FactorParticipacion = '".$_FactorParticipacion."',
  							FlagPotencial = '".$_FlagPotencial."',
  							FlagCompetencia = '".$_FlagCompetencia."',
  							FlagConceptual = '".$_FlagConceptual."',
							UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
							UltimaFecha = NOW()";
				$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			}
		}
		//	-------------
		mysql_query("COMMIT");
		//	-------------
	}
	
	//	eliminar registro
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-------------
		//	elimino
		$sql = "DELETE FROM rh_factorvalorplantilla WHERE Plantilla = '".$registro."'";
		$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	elimino
		$sql = "DELETE FROM rh_evaluacionfactoresplantilla WHERE Plantilla = '".$registro."'";
		$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		//	-------------
		mysql_query("COMMIT");
		//	-------------
	}
}

//	grados de calificacion
elseif ($modulo == "grados_calificacion") {
	//	nuevo registro
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-------------
		//	genero codigo
		$Grado = getCodigo("rh_gradoscalificacion", "Grado", 4);
		$Grado = intval($Grado);
		
		//	inserto
		$sql = "INSERT INTO rh_gradoscalificacion
				SET
					Grado = '".$Grado."',
					Descripcion = '".changeUrl($Descripcion)."',
					Definicion = '".changeUrl($Definicion)."',
					PuntajeMin = '".$PuntajeMin."',
					PuntajeMax = '".$PuntajeMax."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		//	-------------
		mysql_query("COMMIT");
		//	-------------
	}
	
	//	modificar registro
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		//	-------------
		//	inserto
		$sql = "UPDATE rh_gradoscalificacion
				SET
					Descripcion = '".changeUrl($Descripcion)."',
					Definicion = '".changeUrl($Definicion)."',
					PuntajeMin = '".$PuntajeMin."',
					PuntajeMax = '".$PuntajeMax."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE Grado = '".$Grado."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		//	-------------
		mysql_query("COMMIT");
		//	-------------
	}
	
	//	eliminar registro
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-------------
		//	elimino
		$sql = "DELETE FROM rh_gradoscalificacion WHERE Grado = '".$registro."'";
		$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		//	-------------
		mysql_query("COMMIT");
		//	-------------
	}
}

//	apertura de periodo (bono de alimentacion)
/*elseif ($modulo == "bono_periodos") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-----------------
		//	genero codigo
		$Anio = substr($Periodo, 0, 4);
		$CodBonoAlim = getCodigo_3("rh_bonoalimentacion", "CodBonoAlim", "Anio", "CodOrganismo", $Anio, $CodOrganismo, 3);
		
		//	inserto
		$sql = "INSERT INTO rh_bonoalimentacion
				SET
					Anio = '".$Anio."',
					CodOrganismo = '".$CodOrganismo."',
					CodBonoAlim = '".$CodBonoAlim."',
					Periodo = '".$Periodo."',
					Descripcion = '".changeUrl($Descripcion)."',
					FechaInicio = '".formatFechaAMD($FechaInicio)."',
					FechaFin = '".formatFechaAMD($FechaFin)."',
					CodTipoNom = '".$CodTipoNom."',
					TotalDiasPeriodo = '".setNumero($TotalDiasPeriodo)."',
					TotalDiasPago = '".setNumero($TotalDiasPago)."',
					TotalFeriados = '".setNumero($TotalFeriados)."',
					ValorDia = '".setNumero($ValorDia)."',
					HorasDiaria = '".setNumero($HorasDiaria)."',
					HorasSemanal = '".setNumero($HorasSemanal)."',
					ValorSemanal = '".setNumero($ValorSemanal)."',
					ValorMes = '".setNumero($ValorMes)."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		
		//	empleados
		$_dias_completos = setNumero($TotalDiasPeriodo);
		$empleados = split(";char:tr;", $detalles_empleados);
		foreach ($empleados as $_CodPersona) {
			$_ValorPagar = 0;
			$_DiasInactivos = 0;
			$_dia_semana = getDiaSemana($FechaInicio);
			$_fecha = $FechaInicio;
			//	obtengo la leyenda de los dias
			for ($i=1; $i<=$_dias_completos; $i++) {
				if ($_dia_semana == 7) $_dia_semana = 0;
				if ($_dia_semana >= 1 && $_dia_semana <= 5) {
					if (getDiasFeriados($_fecha, $_fecha) > 0) $l = "F";
					else { $l = "X"; $_ValorPagar += setNumero($ValorDia); }
				}
				elseif ($_dia_semana == 0 || $_dia_semana == 6) { $l = "I"; $_DiasInactivos++; }
				$_Dia[$i] = $l;
				##
				$_dia_semana++;
				$_fecha = obtenerFechaFin($_fecha, 2);
			}
			//	inserto
			$_ValorPagar = setNumero($TotalDiasPago) * setNumero($ValorDia);
			$sql = "INSERT INTO rh_bonoalimentaciondet
					SET
						Anio = '".$Anio."',
						CodOrganismo = '".$CodOrganismo."',
						CodBonoAlim = '".$CodBonoAlim."',
						CodPersona = '".$_CodPersona."',
						Dia1 = '".$_Dia[1]."',
						Dia2 = '".$_Dia[2]."',
						Dia3 = '".$_Dia[3]."',
						Dia4 = '".$_Dia[4]."',
						Dia5 = '".$_Dia[5]."',
						Dia6 = '".$_Dia[6]."',
						Dia7 = '".$_Dia[7]."',
						Dia8 = '".$_Dia[8]."',
						Dia9 = '".$_Dia[9]."',
						Dia10 = '".$_Dia[10]."',
						Dia11 = '".$_Dia[11]."',
						Dia12 = '".$_Dia[12]."',
						Dia13 = '".$_Dia[13]."',
						Dia14 = '".$_Dia[14]."',
						Dia15 = '".$_Dia[15]."',
						Dia16 = '".$_Dia[16]."',
						Dia17 = '".$_Dia[17]."',
						Dia18 = '".$_Dia[18]."',
						Dia19 = '".$_Dia[19]."',
						Dia20 = '".$_Dia[20]."',
						Dia21 = '".$_Dia[21]."',
						Dia22 = '".$_Dia[22]."',
						Dia23 = '".$_Dia[23]."',
						Dia24 = '".$_Dia[24]."',
						Dia25 = '".$_Dia[25]."',
						Dia26 = '".$_Dia[26]."',
						Dia27 = '".$_Dia[27]."',
						Dia28 = '".$_Dia[28]."',
						Dia29 = '".$_Dia[29]."',
						Dia30 = '".$_Dia[30]."',
						Dia31 = '".$_Dia[31]."',
						DiasPeriodo = '".setNumero($TotalDiasPeriodo)."',
						DiasPago = '".setNumero($TotalDiasPago)."',
						DiasFeriados = '".setNumero($TotalFeriados)."',
						DiasInactivos = '".$_DiasInactivos."',
						ValorPagar = '".$_ValorPagar."',
						TotalPagar = '".$_ValorPagar."',
						UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	modificar
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "UPDATE rh_bonoalimentacion
				SET
					Periodo = '".$Periodo."',
					Descripcion = '".changeUrl($Descripcion)."',
					FechaInicio = '".formatFechaAMD($FechaInicio)."',
					FechaFin = '".formatFechaAMD($FechaFin)."',
					CodTipoNom = '".$CodTipoNom."',
					TotalDiasPeriodo = '".setNumero($TotalDiasPeriodo)."',
					TotalDiasPago = '".setNumero($TotalDiasPago)."',
					TotalFeriados = '".setNumero($TotalFeriados)."',
					ValorDia = '".setNumero($ValorDia)."',
					HorasDiaria = '".setNumero($HorasDiaria)."',
					HorasSemanal = '".setNumero($HorasSemanal)."',
					ValorSemanal = '".setNumero($ValorSemanal)."',
					ValorMes = '".setNumero($ValorMes)."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					CodBonoAlim = '".$CodBonoAlim."'";
		execute($sql);
		
		//	empleados
		$sql = "DELETE FROM rh_bonoalimentaciondet
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					CodBonoAlim = '".$CodBonoAlim."'";
		$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		$_dias_completos = setNumero($TotalDiasPeriodo);
		$empleados = split(";char:tr;", $detalles_empleados);
		foreach ($empleados as $_CodPersona) {
			$_ValorPagar = 0;
			$_DiasInactivos = 0;
			$_dia_semana = getDiaSemana($FechaInicio);
			$_fecha = $FechaInicio;
			//	obtengo la leyenda de los dias
			for ($i=1; $i<=$_dias_completos; $i++) {
				if ($_dia_semana == 7) $_dia_semana = 0;
				if ($_dia_semana >= 1 && $_dia_semana <= 5) {
					if (getDiasFeriados($_fecha, $_fecha) > 0) $l = "F";
					else { $l = "X"; $_ValorPagar += setNumero($ValorDia); }
				}
				elseif ($_dia_semana == 0 || $_dia_semana == 6) { $l = "I"; $_DiasInactivos++; }
				$_Dia[$i] = $l;
				##
				$_dia_semana++;
				$_fecha = obtenerFechaFin($_fecha, 2);
			}
			//	inserto
			$_ValorPagar = setNumero($TotalDiasPago) * setNumero($ValorDia);
			$sql = "INSERT INTO rh_bonoalimentaciondet
					SET
						Anio = '".$Anio."',
						CodOrganismo = '".$CodOrganismo."',
						CodBonoAlim = '".$CodBonoAlim."',
						CodPersona = '".$_CodPersona."',
						Dia1 = '".$_Dia[1]."',
						Dia2 = '".$_Dia[2]."',
						Dia3 = '".$_Dia[3]."',
						Dia4 = '".$_Dia[4]."',
						Dia5 = '".$_Dia[5]."',
						Dia6 = '".$_Dia[6]."',
						Dia7 = '".$_Dia[7]."',
						Dia8 = '".$_Dia[8]."',
						Dia9 = '".$_Dia[9]."',
						Dia10 = '".$_Dia[10]."',
						Dia11 = '".$_Dia[11]."',
						Dia12 = '".$_Dia[12]."',
						Dia13 = '".$_Dia[13]."',
						Dia14 = '".$_Dia[14]."',
						Dia15 = '".$_Dia[15]."',
						Dia16 = '".$_Dia[16]."',
						Dia17 = '".$_Dia[17]."',
						Dia18 = '".$_Dia[18]."',
						Dia19 = '".$_Dia[19]."',
						Dia20 = '".$_Dia[20]."',
						Dia21 = '".$_Dia[21]."',
						Dia22 = '".$_Dia[22]."',
						Dia23 = '".$_Dia[23]."',
						Dia24 = '".$_Dia[24]."',
						Dia25 = '".$_Dia[25]."',
						Dia26 = '".$_Dia[26]."',
						Dia27 = '".$_Dia[27]."',
						Dia28 = '".$_Dia[28]."',
						Dia29 = '".$_Dia[29]."',
						Dia30 = '".$_Dia[30]."',
						Dia31 = '".$_Dia[31]."',
						DiasPeriodo = '".setNumero($TotalDiasPeriodo)."',
						DiasPago = '".setNumero($TotalDiasPago)."',
						DiasFeriados = '".setNumero($TotalFeriados)."',
						DiasInactivos = '".$_DiasInactivos."',
						ValorPagar = '".$_ValorPagar."',
						TotalPagar = '".$_ValorPagar."',
						UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	cerrar
	elseif ($accion == "cerrar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "UPDATE rh_bonoalimentacion
				SET
					Estado = 'C',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					CodBonoAlim = '".$CodBonoAlim."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	actualizo
		$sql = "UPDATE rh_bonoalimentaciondet
				SET
					Estado = 'C',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					CodBonoAlim = '".$CodBonoAlim."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		//	-----------------
		mysql_query("COMMIT");
	}
}*/

//	apertura de periodo (bono de alimentacion)
elseif ($modulo == "bono_periodos_registrar_eventos_procesar") {
	mysql_query("BEGIN");
	//	-----------------
	//	elimino eventos
	$sql = "DELETE FROM rh_bonoalimentacioneventos
			WHERE
				Anio = '".$Anio."' AND
				CodOrganismo = '".$CodOrganismo."' AND
				CodBonoAlim = '".$CodBonoAlim."' AND
				CodPersona = '".$CodPersona."'";
	$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	
	//	actualizo los descuentos
	for($i=1;$i<=31;$i++) {
		$sql = "UPDATE rh_bonoalimentaciondet
				SET Dia".$i." = 'X'
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					CodBonoAlim = '".$CodBonoAlim."' AND
					CodPersona = '".$CodPersona."' AND
					Dia".$i." = 'D'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	}
	
	//	eventos
	$_Secuencia = 0;
	list($_HorasPar, $_MinutosPar) = split("[:]", $_PARAMETRO['UTDESC']);
	$eventos = split(";char:tr;", $detalles_eventos);
	foreach ($eventos as $linea) {
		list($_Fecha, $_HoraSalida, $_HoraEntrada, $_TotalHoras, $_Motivo, $_TipoEvento, $_Observaciones) = split(";char:td;", $linea);
		if ($_EventoHoras[$_Fecha] != "") {
			$_EventoHoras[$_Fecha] = sumarHoras($_EventoHoras[$_Fecha], $_TotalHoras);
			$_EventoFecha[$_Fecha] = $_Fecha;
		} else {
			$_EventoHoras[$_Fecha] = $_TotalHoras;
			$_EventoFecha[$_Fecha] = $_Fecha;
		}
		//	inserto
		if ($_HoraSalida != "") $Salida = "HoraSalida = '".$_HoraSalida."',"; else $Salida = "HoraSalida = NULL,";
		if ($_HoraEntrada != "") $HoraEntrada = "HoraEntrada = '".$_HoraEntrada."',"; else $HoraEntrada = "HoraEntrada = NULL,";
		$sql = "INSERT INTO rh_bonoalimentacioneventos
				SET
					Anio = '".$Anio."',
					CodOrganismo = '".$CodOrganismo."',
					CodBonoAlim = '".$CodBonoAlim."',
					CodPersona = '".$CodPersona."',
					Secuencia = '".++$_Secuencia."',
					Fecha = '".$_Fecha."',
					$Salida
					$Entrada
					HoraEntrada = '".$_HoraEntrada."',
					TotalHoras = '".$_TotalHoras."',
					TipoEvento = '".$_TipoEvento."',
					Motivo = '".$_Motivo."',
					Observaciones = '".$_Observaciones."',
					UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
					UltimaFecha = NOW()";
		$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	}
	
	//	sumo los descuentos
	$_DiasDescuento = 0;
	foreach ($_EventoFecha as $_Fecha) {
		list($_Horas, $_Minutos) = split("[:]", $_EventoHoras[$_Fecha]);
		if (($_Horas > $_HorasPar) || ($_Horas == $_HorasPar && $_Minutos >= $_MinutosPar)) {
			$_DiasDescuento++;
			$_Dia = getFechaDias(formatFechaDMA($FechaInicio), formatFechaDMA($_Fecha)) + 1;
			//	actualizo detalle del bono alimenticio
			$sql = "UPDATE rh_bonoalimentaciondet
					SET Dia".$_Dia." = 'D'
					WHERE
						Anio = '".$Anio."' AND
						CodOrganismo = '".$CodOrganismo."' AND
						CodBonoAlim = '".$CodBonoAlim."' AND
						CodPersona = '".$CodPersona."'";
			$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		} 
		elseif ($_Fecha) {
			die("El tiempo total para el dia <strong>".formatFechaDMA($_Fecha)."</strong> es <strong>($_EventoHoras[$_Fecha])</strong> y no puede ser menor a <strong>$_PARAMETRO[UTDESC]</strong>");
		}
	}
	
	//	actualizo los descuentos
	$sql = "UPDATE rh_bonoalimentaciondet
			SET
				DiasDescuento = '".$_DiasDescuento."',
				ValorDescuento = ((ValorPagar / DiasPago) * ".intval($_DiasDescuento)."),
				TotalPagar = (ValorPagar - ((ValorPagar / DiasPago) * ".intval($_DiasDescuento)."))
			WHERE
				Anio = '".$Anio."' AND
				CodOrganismo = '".$CodOrganismo."' AND
				CodBonoAlim = '".$CodBonoAlim."' AND
				CodPersona = '".$CodPersona."'";
	$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	//	-----------------
	mysql_query("COMMIT");
}

//	proceso de jubilacion
elseif ($modulo == "jubilaciones") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-----------------
		//	valido
		if ($MontoJubilacion > ($SueldoBase * $_PARAMETRO['PORCLIMITJUB'] / 100)) die("El Monto de Jubilaci&oacute;n no puede ser mayor al ".$_PARAMETRO['PORCLIMITJUB']."% del Sueldo Base");
		
		//	genero codigo
		$CodProceso = getCodigo("rh_proceso_jubilacion", "CodProceso", 4);
		
		//	inserto
		$sql = "INSERT INTO rh_proceso_jubilacion
				SET
					CodProceso = '".$CodProceso."',
					CodOrganismo = '".$CodOrganismo."',
					CodPersona = '".$CodPersona."',
					AniosServicio = '".$AniosServicio."',
					AniosServicioExceso = '".$AniosServicioExceso."',
					Edad = '".$Edad."',
					CodDependencia = '".$CodDependencia."',
					NomDependencia = '".changeUrl($NomDependencia)."',
					CodCargo = '".$CodCargo."',
					DescripCargo = '".changeUrl($DescripCargo)."',
					ProcesadoPor = '".$ProcesadoPor."',
					FechaProcesado = NOW(),
					ObsProcesado = '".changeUrl($ObsProcesado)."',
					MontoJubilacion = '".$MontoJubilacion."',
					Coeficiente = '".$Coeficiente."',
					TotalSueldo = '".$TotalSueldo."',
					TotalPrimas = '".$TotalPrimas."',
					Periodo = NOW(),
					Fingreso = '".formatFechaAMD($Fingreso)."',
					SueldoActual = '".setNumero($SueldoActual)."',
					Estado = '".$Estado."',
					CodTipoNom = '".$CodTipoNom."',
					CodTipoTrabajador = '".$CodTipoTrabajador."',
					ObsCese = '".changeUrl($ObsCese)."',
					SitTra = '".$SitTra."',
					CodMotivoCes = '".$CodMotivoCes."',
					Fegreso = '".formatFechaAMD($Fegreso)."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	antecedentes
		$_Secuencia = 0;
		$antecedentes = split(";char:tr;", $detalles_antecedentes);
		foreach ($antecedentes as $linea) {
			list($_Organismo, $_FIngreso, $_FEgreso, $_Anios, $_Meses, $_Dias) = split(";char:td;", $linea);
			
			//	inserto
			$sql = "INSERT INTO rh_empleado_antecedentes
					SET
						CodProceso = '".$CodProceso."',
						Secuencia = '".++$_Secuencia."',
						CodPersona = '".$CodPersona."',
						Organismo = '".changeUrl($_Organismo)."',
						FIngreso = '".$_FIngreso."',
						FEgreso = '".$_FEgreso."',
						TipoProceso = '".$TipoProceso."',
						Anios = '".$_Anios."',
						Meses = '".$_Meses."',
						Dias = '".$_Dias."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		}
		
		//	sueldos
		$sueldos = split(";char:tr;", $detalles_sueldos);
		foreach ($sueldos as $linea) {
			list($_Secuencia, $_Periodo, $_CodConcepto, $_Monto) = split(";char:td;", $linea);
			
			//	inserto
			$sql = "INSERT INTO rh_relacionsueldojubilacion
					SET
						CodProceso = '".$CodProceso."',
						Secuencia = '".$_Secuencia."',
						CodPersona = '".$CodPersona."',
						TipoProceso = '".$TipoProceso."',
						Periodo = '".$_Periodo."',
						CodConcepto = '".$_CodConcepto."',
						Monto = '".$_Monto."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		}
		echo "|$CodProceso";
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	modificar
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "UPDATE rh_proceso_jubilacion
				SET
					ObsProcesado = '".changeUrl($ObsProcesado)."',
					CodTipoNom = '".$CodTipoNom."',
					CodTipoTrabajador = '".$CodTipoTrabajador."',
					ObsCese = '".changeUrl($ObsCese)."',
					SitTra = '".$SitTra."',
					CodMotivoCes = '".$CodMotivoCes."',
					Fegreso = '".formatFechaAMD($Fegreso)."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodProceso = '".$CodProceso."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	conformar
	elseif ($accion == "conformar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "UPDATE rh_proceso_jubilacion
				SET
					Estado = 'CN',
					ConformadoPor = '".$ConformadoPor."',
					ObsConformado = '".changeUrl($ObsConformado)."',
					FechaConformado = NOW(),
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodProceso = '".$CodProceso."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	aprobar
	elseif ($accion == "aprobar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "UPDATE rh_proceso_jubilacion
				SET
					Estado = 'AP',
					AprobadoPor = '".$AprobadoPor."',
					ObsAprobado = '".changeUrl($ObsAprobado)."',
					FechaAprobado = NOW(),
					FechaJubilacion = '".formatFechaAMD($Fegreso)."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodProceso = '".$CodProceso."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	modifico empleado
		$sql = "UPDATE mastempleado
				SET
					CodTipoNom = '".$CodTipoNom."',
					CodTipoTrabajador = '".$CodTipoTrabajador."',
					CodMotivoCes = '".$CodMotivoCes."',
					Fegreso = '".formatFechaAMD($Fegreso)."',
					Estado = '".$SitTra."',
					ObsCese = '".changeUrl($ObsCese)."',
					MontoJubilacion = '".$MontoJubilacion."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodPersona = '".$CodPersona."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	actualizo nivelaciones anterior
		$FechaHasta = obtenerFechaFin($Fegreso, -1);
		$sql = "UPDATE rh_empleadonivelacion
				SET FechaHasta = '".formatFechaAMD($FechaHasta)."'
				WHERE
					CodPersona = '".$CodPersona."' AND
					FechaHasta = '0000-00-00'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	inserto en nivelacion
		$Secuencia = getCodigo_2("rh_empleadonivelacion", "Secuencia", "CodPersona", $CodPersona, 6);
		$Secuencia = intval($Secuencia);
		$sql = "INSERT INTO rh_empleadonivelacion
				SET
					CodPersona = '".$CodPersona."',
					Secuencia = '".$Secuencia."',
					Fecha = '".formatFechaAMD($Fingreso)."',
					CodOrganismo = '".$CodOrganismo."',
					CodDependencia = '".$CodDependencia."',
					CodCargo = '".$CodCargo."',
					CodTipoNom = '".$CodTipoNom."',
					Estado = '".$SitTra."',
					FechaHasta = '0000-00-00',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	inserto en nivelacion historial
		$SecuenciaH = getCodigo_2("rh_empleadonivelacionhistorial", "Secuencia", "CodPersona", $CodPersona, 6);
		$SecuenciaH = intval($SecuenciaH);
		$sql = "INSERT INTO rh_empleadonivelacionhistorial (
							CodPersona,
							Secuencia,
							Fecha,
							Organismo,
							Dependencia,
							Cargo,
							NivelSalarial,
							CategoriaCargo,
							TipoNomina,
							Estado,
							UltimoUsuario,
							UltimaFecha
				)
						SELECT
							en.CodPersona,
							'$Secuencia' AS Secuencia,
							en.Fecha,
							o.Organismo,
							d.Dependencia,
							pt.DescripCargo AS Cargo,
							pt.NivelSalarial,
							md.Descripcion AS CategoriaCargo,
							tn.Nomina AS TipoNomina,
							en.Estado,
							'".$_SESSION["USUARIO_ACTUAL"]."' AS UltimoUsuario,
							NOW() AS UltimaFecha
						FROM
							rh_empleadonivelacion en
							INNER JOIN mastorganismos o ON (o.CodOrganismo = en.CodOrganismo)
							INNER JOIN mastdependencias d ON (d.CodDependencia = en.CodDependencia)
							INNER JOIN tiponomina tn ON (tn.CodTipoNom = en.CodTipoNom)
							INNER JOIN rh_puestos pt ON (pt.CodCargo = en.CodCargo)
							LEFT JOIN mastmiscelaneosdet md ON (md.CodDetalle = pt.CategoriaCargo AND
																md.CodMaestro = 'CATCARGO')
						WHERE
							en.CodPersona = '".$CodPersona."' AND
							en.Secuencia = '".$Secuencia."'";
		$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	inserto en historial
		$Secuencia = getCodigo_2("rh_historial", "Secuencia", "CodPersona", $CodPersona, 6);
		$Secuencia = intval($Secuencia);
		$sql = "INSERT INTO rh_historial (
							CodPersona,
							Secuencia,
							Periodo,
							Fingreso,
							Organismo,
							Dependencia,
							Cargo,
							NivelSalarial,
							CategoriaCargo,
							TipoNomina,
							TipoPago,
							Estado,
							MotivoCese,
							Fegreso,
							ObsCese,
							TipoTrabajador,
							UltimoUsuario,
							UltimaFecha
				)
						SELECT
							e.CodPersona,
							'$Secuencia' AS Secuencia,
							NOW() AS Periodo,
							e.Fingreso,
							o.Organismo,
							d.Dependencia,
							pt.DescripCargo AS Cargo,
							pt.NivelSalarial,
							md.Descripcion AS CategoriaCargo,
							tn.Nomina AS TipoNomina,
							tp.TipoPago,
							e.Estado,
							mc.MotivoCese,
							e.Fegreso,
							e.ObsCese,
							tt.TipoTrabajador,
							'".$_SESSION["USUARIO_ACTUAL"]."' AS UltimoUsuario,
							NOW() AS UltimaFecha
						FROM
							mastempleado e
							INNER JOIN mastorganismos o ON (o.CodOrganismo = e.CodOrganismo)
							INNER JOIN mastdependencias d ON (d.CodDependencia = e.CodDependencia)
							INNER JOIN tiponomina tn ON (tn.CodTipoNom = e.CodTipoNom)
							INNER JOIN rh_tipotrabajador tt ON (tt.CodTipoTrabajador = e.CodTipoTrabajador)
							INNER JOIN masttipopago tp ON (tp.CodTipoPago = e.CodTipoPago)
							INNER JOIN rh_puestos pt ON (pt.CodCargo = e.CodCargo)
							LEFT JOIN mastmiscelaneosdet md ON (md.CodDetalle = pt.CategoriaCargo AND
																md.CodMaestro = 'CATCARGO')
							LEFT JOIN rh_motivocese mc ON (mc.CodMotivoCes = e.CodMotivoCes)
						WHERE e.CodPersona = '".$CodPersona."'";
		$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		//	-----------------
		mysql_query("COMMIT");
	}
}

//	proceso de pension x invalidez
elseif ($modulo == "pensiones_invalidez") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-----------------
		//	valido
		$SueldoActual = setNumero($UltimoSueldo);
		$Monto = setNumero($MontoPension);
		$MontoMin = $SueldoActual * $_PARAMETRO['PENPORMIN'] / 100;
		$MontoMax = $SueldoActual * $_PARAMETRO['PENPORMAX'] / 100;
		if ($Monto < $MontoMin || $Monto > $MontoMax) die("El Sueldo NO puede ser menor que el ".$_PARAMETRO['PENPORMIN']."% ni mayor que el ".$_PARAMETRO['PENPORMAX']."% del Ultimo Sueldo");
		
		//	genero codigo
		$CodProceso = getCodigo("rh_proceso_pension", "CodProceso", 4);
		
		//	inserto
		$sql = "INSERT INTO rh_proceso_pension
				SET
					CodProceso = '".$CodProceso."',
					CodOrganismo = '".$CodOrganismo."',
					CodPersona = '".$CodPersona."',
					AniosServicio = '".$AniosServicio."',
					Edad = '".$Edad."',
					CodDependencia = '".$CodDependencia."',
					NomDependencia = '".changeUrl($NomDependencia)."',
					CodCargo = '".$CodCargo."',
					DescripCargo = '".changeUrl($DescripCargo)."',
					ProcesadoPor = '".$ProcesadoPor."',
					FechaProcesado = NOW(),
					ObsProcesado = '".changeUrl($ObsProcesado)."',
					MontoPension = '".setNumero($MontoPension)."',
					Fingreso = '".formatFechaAMD($Fingreso)."',
					UltimoSueldo = '".setNumero($UltimoSueldo)."',
					TipoPension = '".$TipoPension."',
					MotivoPension = '".$MotivoPension."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		echo "|$CodProceso";
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	modificar
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		//	-----------------
		//	valido
		$SueldoActual = setNumero($UltimoSueldo);
		$Monto = setNumero($MontoPension);
		$MontoMin = $SueldoActual * $_PARAMETRO['PENPORMIN'] / 100;
		$MontoMax = $SueldoActual * $_PARAMETRO['PENPORMAX'] / 100;
		if ($Monto < $MontoMin || $Monto > $MontoMax) die("El Sueldo NO puede ser menor que el ".$_PARAMETRO['PENPORMIN']."% ni mayor que el ".$_PARAMETRO['PENPORMAX']."% del Ultimo Sueldo");
		
		//	actualizo
		$sql = "UPDATE rh_proceso_pension
				SET
					MotivoPension = '".$MotivoPension."',
					MontoPension = '".setNumero($MontoPension)."',
					ObsProcesado = '".changeUrl($ObsProcesado)."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodProceso = '".$CodProceso."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	conformar
	elseif ($accion == "conformar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "UPDATE rh_proceso_pension
				SET
					Estado = 'CN',
					ConformadoPor = '".$ConformadoPor."',
					ObsConformado = '".changeUrl($ObsConformado)."',
					FechaConformado = NOW(),
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodProceso = '".$CodProceso."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	aprobar
	elseif ($accion == "aprobar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "UPDATE rh_proceso_pension
				SET
					Estado = 'AP',
					AprobadoPor = '".$AprobadoPor."',
					ObsAprobado = '".changeUrl($ObsAprobado)."',
					FechaAprobado = NOW(),
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodProceso = '".$CodProceso."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	modifico empleado
		$sql = "UPDATE mastempleado
				SET
					CodTipoNom = '".$CodTipoNom."',
					CodTipoTrabajador = '".$CodTipoTrabajador."',
					CodMotivoCes = '".$CodMotivoCes."',
					Fegreso = '".formatFechaAMD($Fegreso)."',
					Estado = '".$SitTra."',
					ObsCese = '".changeUrl($ObsCese)."',
					SueldoAnterior = '".setNumero($UltimoSueldo)."',
					SueldoActual = '".setNumero($MontoPension)."',
					MontoJubilacion = '".setNumero($MontoPension)."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodPersona = '".$CodPersona."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	actualizo nivelaciones anterior
		$FechaHasta = obtenerFechaFin($Fegreso, -1);
		$sql = "UPDATE rh_empleadonivelacion
				SET FechaHasta = '".formatFechaAMD($FechaHasta)."'
				WHERE
					CodPersona = '".$CodPersona."' AND
					FechaHasta = '0000-00-00'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	inserto en nivelacion
		$Secuencia = getCodigo_2("rh_empleadonivelacion", "Secuencia", "CodPersona", $CodPersona, 6);
		$Secuencia = intval($Secuencia);
		$sql = "INSERT INTO rh_empleadonivelacion
				SET
					CodPersona = '".$CodPersona."',
					Secuencia = '".$Secuencia."',
					Fecha = '".formatFechaAMD($Fingreso)."',
					CodOrganismo = '".$CodOrganismo."',
					CodDependencia = '".$CodDependencia."',
					CodCargo = '".$CodCargo."',
					CodTipoNom = '".$CodTipoNom."',
					Estado = '".$SitTra."',
					FechaHasta = '0000-00-00',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	inserto en nivelacion historial
		$SecuenciaH = getCodigo_2("rh_empleadonivelacionhistorial", "Secuencia", "CodPersona", $CodPersona, 6);
		$SecuenciaH = intval($SecuenciaH);
		$sql = "INSERT INTO rh_empleadonivelacionhistorial (
							CodPersona,
							Secuencia,
							Fecha,
							Organismo,
							Dependencia,
							Cargo,
							NivelSalarial,
							CategoriaCargo,
							TipoNomina,
							Estado,
							UltimoUsuario,
							UltimaFecha
				)
						SELECT
							en.CodPersona,
							'$SecuenciaH' AS Secuencia,
							en.Fecha,
							o.Organismo,
							d.Dependencia,
							pt.DescripCargo AS Cargo,
							pt.NivelSalarial,
							md.Descripcion AS CategoriaCargo,
							tn.Nomina AS TipoNomina,
							en.Estado,
							'".$_SESSION["USUARIO_ACTUAL"]."' AS UltimoUsuario,
							NOW() AS UltimaFecha
						FROM
							rh_empleadonivelacion en
							INNER JOIN mastorganismos o ON (o.CodOrganismo = en.CodOrganismo)
							INNER JOIN mastdependencias d ON (d.CodDependencia = en.CodDependencia)
							INNER JOIN tiponomina tn ON (tn.CodTipoNom = en.CodTipoNom)
							INNER JOIN rh_puestos pt ON (pt.CodCargo = en.CodCargo)
							LEFT JOIN mastmiscelaneosdet md ON (md.CodDetalle = pt.CategoriaCargo AND
																md.CodMaestro = 'CATCARGO')
						WHERE
							en.CodPersona = '".$CodPersona."' AND
							en.Secuencia = '".$Secuencia."'";
		$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	inserto en historial
		$Secuencia = getCodigo_2("rh_historial", "Secuencia", "CodPersona", $CodPersona, 6);
		$Secuencia = intval($Secuencia);
		$sql = "INSERT INTO rh_historial (
							CodPersona,
							Secuencia,
							Periodo,
							Fingreso,
							Organismo,
							Dependencia,
							Cargo,
							NivelSalarial,
							CategoriaCargo,
							TipoNomina,
							TipoPago,
							Estado,
							MotivoCese,
							Fegreso,
							ObsCese,
							TipoTrabajador,
							UltimoUsuario,
							UltimaFecha
				)
						SELECT
							e.CodPersona,
							'$Secuencia' AS Secuencia,
							NOW() AS Periodo,
							e.Fingreso,
							o.Organismo,
							d.Dependencia,
							pt.DescripCargo AS Cargo,
							pt.NivelSalarial,
							md.Descripcion AS CategoriaCargo,
							tn.Nomina AS TipoNomina,
							tp.TipoPago,
							e.Estado,
							mc.MotivoCese,
							e.Fegreso,
							e.ObsCese,
							tt.TipoTrabajador,
							'".$_SESSION["USUARIO_ACTUAL"]."' AS UltimoUsuario,
							NOW() AS UltimaFecha
						FROM
							mastempleado e
							INNER JOIN mastorganismos o ON (o.CodOrganismo = e.CodOrganismo)
							INNER JOIN mastdependencias d ON (d.CodDependencia = e.CodDependencia)
							INNER JOIN tiponomina tn ON (tn.CodTipoNom = e.CodTipoNom)
							INNER JOIN rh_tipotrabajador tt ON (tt.CodTipoTrabajador = e.CodTipoTrabajador)
							INNER JOIN masttipopago tp ON (tp.CodTipoPago = e.CodTipoPago)
							INNER JOIN rh_puestos pt ON (pt.CodCargo = e.CodCargo)
							LEFT JOIN mastmiscelaneosdet md ON (md.CodDetalle = pt.CategoriaCargo AND
																md.CodMaestro = 'CATCARGO')
							LEFT JOIN rh_motivocese mc ON (mc.CodMotivoCes = e.CodMotivoCes)
						WHERE e.CodPersona = '".$CodPersona."'";
		$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		//	-----------------
		mysql_query("COMMIT");
	}
}

//	proceso de pension x sobreviviente
elseif ($modulo == "pensiones_sobreviviente") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-----------------
		//	valido
		if ($MontoJubilacion > ($SueldoBase * $_PARAMETRO['PORCLIMITJUB'] / 100)) die("El Monto de Jubilaci&oacute;n no puede ser mayor al ".$_PARAMETRO['PORCLIMITJUB']."% del Sueldo Base");
		
		//	genero codigo
		$CodProceso = getCodigo("rh_proceso_pension", "CodProceso", 4);
		
		//	inserto
		$sql = "INSERT INTO rh_proceso_pension
				SET
					CodProceso = '".$CodProceso."',
					CodOrganismo = '".$CodOrganismo."',
					CodPersona = '".$CodPersona."',
					AniosServicio = '".$AniosServicio."',
					AniosServicioExceso = '".$AniosServicioExceso."',
					Edad = '".$Edad."',
					CodDependencia = '".$CodDependencia."',
					NomDependencia = '".changeUrl($NomDependencia)."',
					CodCargo = '".$CodCargo."',
					DescripCargo = '".changeUrl($DescripCargo)."',
					ProcesadoPor = '".$ProcesadoPor."',
					FechaProcesado = NOW(),
					ObsProcesado = '".changeUrl($ObsProcesado)."',
					MontoJubilacion = '".$MontoJubilacion."',
					Coeficiente = '".$Coeficiente."',
					TotalSueldo = '".$TotalSueldo."',
					TotalPrimas = '".$TotalPrimas."',
					Periodo = NOW(),
					Fingreso = '".formatFechaAMD($Fingreso)."',
					UltimoSueldo = '".setNumero($UltimoSueldo)."',
					MontoPension = '".setNumero($MontoPension)."',
					TipoPension = '".$TipoPension."',
					MotivoPension = '".$MotivoPension."',
					Estado = '".$Estado."',
					CodTipoNom = '".$CodTipoNom."',
					CodTipoTrabajador = '".$CodTipoTrabajador."',
					ObsCese = '".changeUrl($ObsCese)."',
					SitTra = '".$SitTra."',
					CodMotivoCes = '".$CodMotivoCes."',
					Fegreso = '".formatFechaAMD($Fegreso)."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	antecedentes
		$_Secuencia = 0;
		$antecedentes = split(";char:tr;", $detalles_antecedentes);
		foreach ($antecedentes as $linea) {
			list($_Organismo, $_FIngreso, $_FEgreso, $_Anios, $_Meses, $_Dias) = split(";char:td;", $linea);
			//	inserto
			$sql = "INSERT INTO rh_empleado_antecedentes
					SET
						CodProceso = '".$CodProceso."',
						Secuencia = '".++$_Secuencia."',
						CodPersona = '".$CodPersona."',
						Organismo = '".changeUrl($_Organismo)."',
						FIngreso = '".$_FIngreso."',
						FEgreso = '".$_FEgreso."',
						TipoProceso = '".$TipoProceso."',
						Anios = '".$_Anios."',
						Meses = '".$_Meses."',
						Dias = '".$_Dias."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		}
		
		//	sueldos
		$sueldos = split(";char:tr;", $detalles_sueldos);
		foreach ($sueldos as $linea) {
			list($_Secuencia, $_Periodo, $_CodConcepto, $_Monto) = split(";char:td;", $linea);
			//	inserto
			$sql = "INSERT INTO rh_relacionsueldojubilacion
					SET
						CodProceso = '".$CodProceso."',
						Secuencia = '".$_Secuencia."',
						CodPersona = '".$CodPersona."',
						TipoProceso = '".$TipoProceso."',
						Periodo = '".$_Periodo."',
						CodConcepto = '".$_CodConcepto."',
						Monto = '".$_Monto."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		}
		
		//	beneficiarios
		$_Secuencia = 0;
		$beneficiarios = split(";char:tr;", $detalles_beneficiarios);
		foreach ($beneficiarios as $linea) {
			list($_NroDocumento, $_NombreCompleto, $_FlagPrincipal, $_Parentesco, $_FechaNacimiento, $_Sexo, $_FlagIncapacitados, $_FlagEstudia) = split(";char:td;", $linea);
			//	inserto
			$sql = "INSERT INTO rh_beneficiariopension
					SET
						CodProceso = '".$CodProceso."',
						Secuencia = '".++$_Secuencia."',
						CodPersona = '".$CodPersona."',
						NroDocumento = '".$_NroDocumento."',
						NombreCompleto = '".changeUrl($_NombreCompleto)."',
						FechaNacimiento = '".$_FechaNacimiento."',
						Sexo = '".$_Sexo."',
						FlagIncapacitados = '".$_FlagIncapacitados."',
						FlagEstudia = '".$_FlagEstudia."',
						FlagPrincipal = '".$_FlagPrincipal."',
						Parentesco = '".$_Parentesco."',
						Estado = 'A',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		}
		echo "|$CodProceso";
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	modificar
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		//	-----------------
		//	inserto
		$sql = "UPDATE rh_proceso_pension
				SET
					ObsProcesado = '".changeUrl($ObsProcesado)."',
					CodTipoNom = '".$CodTipoNom."',
					CodTipoTrabajador = '".$CodTipoTrabajador."',
					ObsCese = '".changeUrl($ObsCese)."',
					SitTra = '".$SitTra."',
					CodMotivoCes = '".$CodMotivoCes."',
					Fegreso = '".formatFechaAMD($Fegreso)."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodProceso = '".$CodProceso."'";
		$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	beneficiarios
		$sql = "DELETE FROM rh_beneficiariopension WHERE CodProceso = '".$CodProceso."'";
		$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		$_Secuencia = 0;
		$beneficiarios = split(";char:tr;", $detalles_beneficiarios);
		foreach ($beneficiarios as $linea) {
			list($_NroDocumento, $_NombreCompleto, $_FlagPrincipal, $_Parentesco, $_FechaNacimiento, $_Sexo, $_FlagIncapacitados, $_FlagEstudia) = split(";char:td;", $linea);
			//	inserto
			$sql = "INSERT INTO rh_beneficiariopension
					SET
						CodProceso = '".$CodProceso."',
						Secuencia = '".++$_Secuencia."',
						CodPersona = '".$CodPersona."',
						NroDocumento = '".$_NroDocumento."',
						NombreCompleto = '".changeUrl($_NombreCompleto)."',
						FechaNacimiento = '".$_FechaNacimiento."',
						Sexo = '".$_Sexo."',
						FlagIncapacitados = '".$_FlagIncapacitados."',
						FlagEstudia = '".$_FlagEstudia."',
						FlagPrincipal = '".$_FlagPrincipal."',
						Parentesco = '".$_Parentesco."',
						Estado = 'A',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		}
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	conformar
	elseif ($accion == "conformar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "UPDATE rh_proceso_pension
				SET
					Estado = 'CN',
					ConformadoPor = '".$ConformadoPor."',
					ObsConformado = '".changeUrl($ObsConformado)."',
					FechaConformado = NOW(),
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodProceso = '".$CodProceso."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	aprobar
	elseif ($accion == "aprobar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "UPDATE rh_proceso_pension
				SET
					Estado = 'AP',
					AprobadoPor = '".$AprobadoPor."',
					ObsAprobado = '".changeUrl($ObsAprobado)."',
					FechaAprobado = NOW(),
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodProceso = '".$CodProceso."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	modifico empleado
		$sql = "UPDATE mastempleado
				SET
					CodTipoNom = '".$CodTipoNom."',
					CodTipoTrabajador = '".$CodTipoTrabajador."',
					CodMotivoCes = '".$CodMotivoCes."',
					Fegreso = '".formatFechaAMD($Fegreso)."',
					Estado = '".$SitTra."',
					ObsCese = '".changeUrl($ObsCese)."',
					MontoJubilacion = '".$MontoJubilacion."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodPersona = '".$CodPersona."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	actualizo nivelaciones anterior
		$FechaHasta = obtenerFechaFin($Fegreso, -1);
		$sql = "UPDATE rh_empleadonivelacion
				SET FechaHasta = '".formatFechaAMD($FechaHasta)."'
				WHERE
					CodPersona = '".$CodPersona."' AND
					FechaHasta = '0000-00-00'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	inserto en nivelacion
		$Secuencia = getCodigo_2("rh_empleadonivelacion", "Secuencia", "CodPersona", $CodPersona, 6);
		$Secuencia = intval($Secuencia);
		$sql = "INSERT INTO rh_empleadonivelacion
				SET
					CodPersona = '".$CodPersona."',
					Secuencia = '".$Secuencia."',
					Fecha = '".formatFechaAMD($Fingreso)."',
					CodOrganismo = '".$CodOrganismo."',
					CodDependencia = '".$CodDependencia."',
					CodCargo = '".$CodCargo."',
					CodTipoNom = '".$CodTipoNom."',
					Estado = '".$SitTra."',
					FechaHasta = '0000-00-00',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	inserto en nivelacion historial
		$SecuenciaH = getCodigo_2("rh_empleadonivelacionhistorial", "Secuencia", "CodPersona", $CodPersona, 6);
		$SecuenciaH = intval($SecuenciaH);
		$sql = "INSERT INTO rh_empleadonivelacionhistorial (
							CodPersona,
							Secuencia,
							Fecha,
							Organismo,
							Dependencia,
							Cargo,
							NivelSalarial,
							CategoriaCargo,
							TipoNomina,
							Estado,
							UltimoUsuario,
							UltimaFecha
				)
						SELECT
							en.CodPersona,
							'$Secuencia' AS Secuencia,
							en.Fecha,
							o.Organismo,
							d.Dependencia,
							pt.DescripCargo AS Cargo,
							pt.NivelSalarial,
							md.Descripcion AS CategoriaCargo,
							tn.Nomina AS TipoNomina,
							en.Estado,
							'".$_SESSION["USUARIO_ACTUAL"]."' AS UltimoUsuario,
							NOW() AS UltimaFecha
						FROM
							rh_empleadonivelacion en
							INNER JOIN mastorganismos o ON (o.CodOrganismo = en.CodOrganismo)
							INNER JOIN mastdependencias d ON (d.CodDependencia = en.CodDependencia)
							INNER JOIN tiponomina tn ON (tn.CodTipoNom = en.CodTipoNom)
							INNER JOIN rh_puestos pt ON (pt.CodCargo = en.CodCargo)
							LEFT JOIN mastmiscelaneosdet md ON (md.CodDetalle = pt.CategoriaCargo AND
																md.CodMaestro = 'CATCARGO')
						WHERE
							en.CodPersona = '".$CodPersona."' AND
							en.Secuencia = '".$Secuencia."'";
		$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	inserto en historial
		$Secuencia = getCodigo_2("rh_historial", "Secuencia", "CodPersona", $CodPersona, 6);
		$Secuencia = intval($Secuencia);
		$sql = "INSERT INTO rh_historial (
							CodPersona,
							Secuencia,
							Periodo,
							Fingreso,
							Organismo,
							Dependencia,
							Cargo,
							NivelSalarial,
							CategoriaCargo,
							TipoNomina,
							TipoPago,
							Estado,
							MotivoCese,
							Fegreso,
							ObsCese,
							TipoTrabajador,
							UltimoUsuario,
							UltimaFecha
				)
						SELECT
							e.CodPersona,
							'$Secuencia' AS Secuencia,
							NOW() AS Periodo,
							e.Fingreso,
							o.Organismo,
							d.Dependencia,
							pt.DescripCargo AS Cargo,
							pt.NivelSalarial,
							md.Descripcion AS CategoriaCargo,
							tn.Nomina AS TipoNomina,
							tp.TipoPago,
							e.Estado,
							mc.MotivoCese,
							e.Fegreso,
							e.ObsCese,
							tt.TipoTrabajador,
							'".$_SESSION["USUARIO_ACTUAL"]."' AS UltimoUsuario,
							NOW() AS UltimaFecha
						FROM
							mastempleado e
							INNER JOIN mastorganismos o ON (o.CodOrganismo = e.CodOrganismo)
							INNER JOIN mastdependencias d ON (d.CodDependencia = e.CodDependencia)
							INNER JOIN tiponomina tn ON (tn.CodTipoNom = e.CodTipoNom)
							INNER JOIN rh_tipotrabajador tt ON (tt.CodTipoTrabajador = e.CodTipoTrabajador)
							INNER JOIN masttipopago tp ON (tp.CodTipoPago = e.CodTipoPago)
							INNER JOIN rh_puestos pt ON (pt.CodCargo = e.CodCargo)
							LEFT JOIN mastmiscelaneosdet md ON (md.CodDetalle = pt.CategoriaCargo AND
																md.CodMaestro = 'CATCARGO')
							LEFT JOIN rh_motivocese mc ON (mc.CodMotivoCes = e.CodMotivoCes)
						WHERE e.CodPersona = '".$CodPersona."'";
		$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	inserto los beneficiarios en persona
		$sql = "";
		//	-----------------
		mysql_query("COMMIT");
	}
}

//	reingreso/cese
elseif ($modulo == "reingreso") {
	//	nuevo registro
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-----------------
		//	genero codigo
		$CodProceso = getCodigo_2("rh_procesocesereing", "CodProceso", "Tipo", $Tipo, 6);
		//	inserto
		$sql = "INSERT INTO rh_procesocesereing
				SET
					Tipo = '".$Tipo."',
					CodProceso = '".$CodProceso."',
					CodOrganismo = '".$CodOrganismo."',
					CodPersona = '".$CodPersona."',
					Periodo = '".$Periodo."',
					Fecha = '".formatFechaAMD($Fecha)."',
					AnioServicio = '".$AnioServicio."',
					Edad = '".$Edad."',
					CodDependencia = '".$CodDependencia."',
					Dependencia = '".changeUrl($Dependencia)."',
					CodCargo = '".$CodCargo."',
					DescripCargo = '".changeUrl($DescripCargo)."',
					SueldoActual = '".setNumero($SueldoActual)."',
					FechaIngreso = '".formatFechaAMD($FechaIngreso)."',
					CodTipoNom = '".$CodTipoNom."',
					CodTipoTrabajador = '".$CodTipoTrabajador."',
					CodMotivoCes = '".$CodMotivoCes."',
					FechaEgreso = '".formatFechaAMD($FechaEgreso)."',
					ObsCese = '".changeUrl($ObsCese)."',
					SitTra = '".$SitTra."',
					CreadoPor = '".$CreadoPor."',
					FechaCreado = '".formatFechaAMD($FechaCreado)."',
					ObsCreado = '".changeUrl($ObsCreado)."',
					NroResolucion = '".$NroResolucion."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
	//	modificar registro
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizar
		$sql = "UPDATE rh_procesocesereing
				SET
					CodOrganismo = '".$CodOrganismo."',
					CodDependencia = '".$CodDependencia."',
					Dependencia = '".changeUrl($Dependencia)."',
					CodCargo = '".$CodCargo."',
					DescripCargo = '".changeUrl($DescripCargo)."',
					SueldoActual = '".setNumero($SueldoActual)."',
					FechaIngreso = '".formatFechaAMD($FechaIngreso)."',
					CodTipoNom = '".$CodTipoNom."',
					CodTipoTrabajador = '".$CodTipoTrabajador."',
					CodMotivoCes = '".$CodMotivoCes."',
					FechaEgreso = '".formatFechaAMD($FechaEgreso)."',
					ObsCese = '".changeUrl($ObsCese)."',
					SitTra = '".$SitTra."',
					NroResolucion = '".$NroResolucion."',
					ObsCreado = '".changeUrl($ObsCreado)."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					Tipo = '".$Tipo."' AND
					CodProceso = '".$CodProceso."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
	//	conformar registro
	elseif ($accion == "conformar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizar
		$sql = "UPDATE rh_procesocesereing
				SET
					ConformadoPor = '".$ConformadoPor."',
					FechaConformado = '".formatFechaAMD($FechaConformado)."',
					ObsConformado = '".changeUrl($ObsConformado)."',
					Estado = 'CN',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					Tipo = '".$Tipo."' AND
					CodProceso = '".$CodProceso."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		//	-----------------
		mysql_query("COMMIT");
	}
	//	aprobar registro
	elseif ($accion == "aprobar") {
		mysql_query("BEGIN");
		//	-----------------
		$FechaFinNomina = fechaFin($FechaEgreso, -1);
		//	actualizar
		$sql = "UPDATE rh_procesocesereing
				SET
					AprobadoPor = '".$AprobadoPor."',
					FechaAprobado = '".formatFechaAMD($FechaAprobado)."',
					ObsAprobado = '".changeUrl($ObsAprobado)."',
					Estado = 'AP',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					Tipo = '".$Tipo."' AND
					CodProceso = '".$CodProceso."'";
		execute($sql);
		//	actualizar
		$res_empleado = "";
		$sql = "UPDATE mastempleado
				SET
					CodTipoNom = '".$CodTipoNom."',
					CodTipoTrabajador = '".$CodTipoTrabajador."',
					Estado = '".$SitTra."',
					CodMotivoCes = '".$CodMotivoCes."',
					Fegreso = '".formatFechaAMD($FechaEgreso)."',
					FechaFinNomina = '".formatFechaAMD($FechaFinNomina)."',
					ObsCese = '".changeUrl($ObsCese)."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodPersona = '".$CodPersona."'";
		execute($sql);
		//	nivelacion
		$Secuencia = getCodigo_2("rh_empleadonivelacion", "Secuencia", "CodPersona", $CodPersona, 6); 
		$Secuencia = intval($Secuencia);
		if ($Tipo == "C") {
			$FechaHasta = $FechaEgreso;
			$FechaHastaAnterior = obtenerFechaFin($FechaEgreso, 0);
			##	nivelacion anterior
			$sql = "UPDATE rh_empleadonivelacion
					SET
						FechaHasta = '".formatFechaAMD($FechaHastaAnterior)."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()
					WHERE
						CodPersona = '".$CodPersona."' AND
						(FechaHasta = '0000-00-00' OR FechaHasta = '' OR FechaHasta IS NULL)";
			execute($sql);
		} else {
			$FechaHasta = "00-00-0000";
			##	inserto
			$sql = "INSERT INTO rh_empleadonivelacion
					SET
						CodPersona = '".$CodPersona."',
						Secuencia = '".$Secuencia."',
						Fecha = '".formatFechaAMD($FechaIngreso)."',
						CodOrganismo = '".$CodOrganismo."',
						CodDependencia = '".$CodDependencia."',
						CodCargo = '".$CodCargo."',
						Paso = (SELECT Paso FROM mastempleado WHERE CodPersona = '$CodPersona'),
						CodTipoNom = '".$CodTipoNom."',
						FechaHasta = '".formatFechaAMD($FechaHasta)."',
						Documento = '".$NroResolucion."',
						Estado = '".$SitTra."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		//	nivelacion historial
		$Secuencia = getCodigo_2("rh_empleadonivelacionhistorial", "Secuencia", "CodPersona", $CodPersona, 6); 
		$Secuencia = intval($Secuencia);
		$sql = "INSERT INTO rh_empleadonivelacionhistorial (
							CodPersona,
							Secuencia,
							Fecha,
							Organismo,
							Dependencia,
							Cargo,
							NivelSalarial,
							CategoriaCargo,
							TipoNomina,
							Estado,
							UltimoUsuario,
							UltimaFecha
				)
						SELECT
							en.CodPersona,
							'$Secuencia' AS Secuencia,
							en.Fecha,
							o.Organismo,
							d.Dependencia,
							pt.DescripCargo AS Cargo,
							pt.NivelSalarial,
							md.Descripcion AS CategoriaCargo,
							tn.Nomina AS TipoNomina,
							en.Estado,
							'".$_SESSION["USUARIO_ACTUAL"]."' AS UltimoUsuario,
							NOW() AS UltimaFecha
						FROM
							rh_empleadonivelacion en
							INNER JOIN mastorganismos o ON (o.CodOrganismo = en.CodOrganismo)
							INNER JOIN mastdependencias d ON (d.CodDependencia = en.CodDependencia)
							INNER JOIN tiponomina tn ON (tn.CodTipoNom = en.CodTipoNom)
							INNER JOIN rh_puestos pt ON (pt.CodCargo = en.CodCargo)
							LEFT JOIN mastmiscelaneosdet md ON (md.CodDetalle = pt.CategoriaCargo AND
																md.CodMaestro = 'CATCARGO')
						WHERE
							en.CodPersona = '".$CodPersona."' AND
							en.Secuencia = '".$Secuencia."'";
		execute($sql);
		//	historial
		$Secuencia = getCodigo_2("rh_historial", "Secuencia", "CodPersona", $CodPersona, 6);
		$Secuencia = intval($Secuencia);
		$sql = "INSERT INTO rh_historial (
							CodPersona,
							Secuencia,
							Periodo,
							Fingreso,
							Organismo,
							Dependencia,
							Cargo,
							NivelSalarial,
							CategoriaCargo,
							TipoNomina,
							TipoPago,
							Estado,
							MotivoCese,
							Fegreso,
							ObsCese,
							TipoTrabajador,
							UltimoUsuario,
							UltimaFecha
				)
						SELECT
							e.CodPersona,
							'$Secuencia' AS Secuencia,
							NOW() AS Periodo,
							e.Fingreso,
							o.Organismo,
							d.Dependencia,
							pt.DescripCargo AS Cargo,
							pt.NivelSalarial,
							md.Descripcion AS CategoriaCargo,
							tn.Nomina AS TipoNomina,
							tp.TipoPago,
							e.Estado,
							mc.MotivoCese,
							e.Fegreso,
							e.ObsCese,
							tt.TipoTrabajador,
							'".$_SESSION["USUARIO_ACTUAL"]."' AS UltimoUsuario,
							NOW() AS UltimaFecha
						FROM
							mastempleado e
							INNER JOIN mastorganismos o ON (o.CodOrganismo = e.CodOrganismo)
							INNER JOIN mastdependencias d ON (d.CodDependencia = e.CodDependencia)
							INNER JOIN tiponomina tn ON (tn.CodTipoNom = e.CodTipoNom)
							INNER JOIN rh_tipotrabajador tt ON (tt.CodTipoTrabajador = e.CodTipoTrabajador)
							INNER JOIN masttipopago tp ON (tp.CodTipoPago = e.CodTipoPago)
							INNER JOIN rh_puestos pt ON (pt.CodCargo = e.CodCargo)
							LEFT JOIN mastmiscelaneosdet md ON (md.CodDetalle = pt.CategoriaCargo AND
																md.CodMaestro = 'CATCARGO')
							LEFT JOIN rh_motivocese mc ON (mc.CodMotivoCes = e.CodMotivoCes)
						WHERE e.CodPersona = '".$CodPersona."'";
		execute($sql);
		//	si es cese
		if ($Tipo == "C") {
			$sql = "UPDATE mastempleado SET NroResolucionEgreso = '".$NroResolucion."' WHERE CodPersona = '".$CodPersona."'";
			execute($sql);
			//	elimino el periodo vacacional si fue insertado
			$Anio = getVar2("rh_vacacionperiodo", "MAX(Anio)", array("CodPersona"), array($CodPersona));
			list($di, $mi, $ai) = split("[/.-]", $FechaIngreso);
			list($de, $me, $ae) = split("[/.-]", $FechaEgreso);
			if ("$me-$de" < "$mi-$di" && ($Anio+1) == $AnioActual) {
				delete("rh_vacacionperiodo", array("CodPersona","Anio"), array($CodPersona,$Anio));
			}
			//	inactivo de bono de alimentacion los dias
			##	obtengo el periodo de bono de alimentacion
			$_CodTipoNom = getVar2("mastempleado", "CodTipoNom", array("CodPersona"), array($CodPersona));
			$sql = "SELECT *
					FROM rh_bonoalimentacion
					WHERE
						CodTipoNom = '".$_CodTipoNom."' AND
						'".formatFechaAMD($FechaEgreso)."' >= FechaInicio AND
						'".formatFechaAMD($FechaEgreso)."' <= FechaFin";
			$field_ba = getRecord($sql);
			##	obtengo el detalle del empleado
			$sql = "SELECT *
					FROM rh_bonoalimentaciondet
					WHERE
						Anio = '".$field_ba['Anio']."' AND
						CodOrganismo = '".$field_ba['CodOrganismo']."' AND
						CodBonoAlim = '".$field_ba['CodBonoAlim']."' AND
						CodPersona = '".$CodPersona."'";
			$field_bono = getRecord($sql);
			$campos_dias = "";
			$_FechaHasta = obtenerFechaFin($FechaEgreso, 0);
			$inicio = getFechaDias(formatFechaDMA($field_ba['FechaInicio']), $_FechaHasta);
			$DiasPago = 0;
			$DiasFeriados = 0;
			$DiasInactivos = 0;
			for($i=$inicio+2;$i<=$field_ba['TotalDiasPeriodo'];$i++) {
				$Dia = "Dia".$i;
				if ($field_bono[$Dia] == "X") { ++$DiasPago; ++$DiasInactivos; $campos_dias .= ", Dia".$i." = 'I'"; }
				elseif ($field_bono[$Dia] == "F") { ++$DiasFeriados; ++$DiasInactivos; $campos_dias .= ", Dia".$i." = 'I'"; }
			}
			##	actualizo periodo del empleado
			$sql = "UPDATE rh_bonoalimentaciondet
					SET
						DiasPago = DiasPago - ".intval($DiasPago).",
						DiasFeriados = DiasFeriados - ".intval($DiasFeriados).",
						DiasInactivos = DiasInactivos + ".intval($DiasInactivos)."
						$campos_dias
					WHERE
						Anio = '".$field_ba['Anio']."' AND
						CodOrganismo = '".$field_ba['CodOrganismo']."' AND
						CodBonoAlim = '".$field_ba['CodBonoAlim']."' AND
						CodPersona = '".$CodPersona."'";
			execute($sql);
			##	consulto nuevos valores
			$sql = "SELECT DiasPago
					FROM rh_bonoalimentaciondet
					WHERE
						Anio = '".$field_ba['Anio']."' AND
						CodOrganismo = '".$field_ba['CodOrganismo']."' AND
						CodBonoAlim = '".$field_ba['CodBonoAlim']."' AND
						CodPersona = '".$CodPersona."'";
			$field_det = getRecord($sql);
			##	actualizo el nuevo monto
			$ValorPagar = $field_det['DiasPago'] * $field_ba['ValorDia'];
			$ValorDescuento = $field_det['DiasDescuento'] * $field_ba['ValorDia'];
			$TotalPagar = $ValorPagar - $ValorDescuento;
			$sql = "UPDATE rh_bonoalimentaciondet
					SET
						ValorPagar = ".floatval($ValorPagar).",
						ValorDescuento = ".floatval($ValorDescuento).",
						TotalPagar = ".floatval($TotalPagar)."
					WHERE
						Anio = '".$field_ba['Anio']."' AND
						CodOrganismo = '".$field_ba['CodOrganismo']."' AND
						CodBonoAlim = '".$field_ba['CodBonoAlim']."' AND
						CodPersona = '".$CodPersona."'";
			execute($sql);
		}
		elseif ($Tipo == "R") {
			$sql = "UPDATE mastempleado SET NroResolucionIngreso = '".$NroResolucion."' WHERE CodPersona = '".$CodPersona."'";
			execute($sql);
			##	
			list($DiaIngreso, $MesIngreso, $AnioIngreso) = split("[./-]", $FechaIngreso);
			$sql = "SELECT *
					FROM rh_bonoalimentacion
					WHERE
						Anio = '".$AnioIngreso."' AND
						CodOrganismo = '".$CodOrganismo."' AND
						CodTipoNom = '".$CodTipoNom."' AND
						'".formatFechaAMD($FechaIngreso)."' >= FechaInicio AND
						'".formatFechaAMD($FechaIngreso)."' <= FechaFin";
			$field_ba = getRecord($sql);
			if ($field_ba) {
				$ValorDia = $field_ba['ValorDia'];
				$DiasInactivos = 0;
				$_dia_semana = getDiaSemana(formatFechaDMA($field_ba['FechaInicio']));
				$_fecha = formatFechaDMA($field_ba['FechaInicio']);
				$inicio = getFechaDias(formatFechaDMA($field_ba['FechaInicio']), $FechaIngreso);
				//	obtengo la leyenda de los dias
				for ($i=1; $i<=$field_ba['TotalDiasPeriodo']; $i++) {
					if ($_dia_semana == 7) $_dia_semana = 0;
					if ($_dia_semana >= 1 && $_dia_semana <= 5) {
						if ($i <= ($inicio)) { $l = "I"; $DiasInactivos++; }
						elseif (getDiasFeriados($_fecha, $_fecha) > 0) $l = "F";
						else $l = "X";
					}
					elseif ($_dia_semana == 0 || $_dia_semana == 6) { $l = "I"; $DiasInactivos++; }
					$_Dia[$i] = $l;
					##
					$_dia_semana++;
					$_fecha = obtenerFechaFin($_fecha, 2);
				}
				$DiasPago = $field_ba['TotalDiasPeriodo'] - $DiasInactivos;
				$ValorPagar = $field_ba['ValorDia'] * $DiasPago;
				//	elimino
				$sql = "DELETE FROM rh_bonoalimentaciondet
						WHERE
							Anio = '".$field_ba['Anio']."' AND
							CodOrganismo = '".$CodOrganismo."' AND
							CodBonoAlim = '".$field_ba['CodBonoAlim']."' AND
							CodPersona = '".$CodPersona."'";
				execute($sql);
				//	inserto
				$sql = "INSERT INTO rh_bonoalimentaciondet
						SET
							Anio = '".$field_ba['Anio']."',
							CodOrganismo = '".$CodOrganismo."',
							CodBonoAlim = '".$field_ba['CodBonoAlim']."',
							CodPersona = '".$CodPersona."',
							Dia1 = '".$_Dia[1]."',
							Dia2 = '".$_Dia[2]."',
							Dia3 = '".$_Dia[3]."',
							Dia4 = '".$_Dia[4]."',
							Dia5 = '".$_Dia[5]."',
							Dia6 = '".$_Dia[6]."',
							Dia7 = '".$_Dia[7]."',
							Dia8 = '".$_Dia[8]."',
							Dia9 = '".$_Dia[9]."',
							Dia10 = '".$_Dia[10]."',
							Dia11 = '".$_Dia[11]."',
							Dia12 = '".$_Dia[12]."',
							Dia13 = '".$_Dia[13]."',
							Dia14 = '".$_Dia[14]."',
							Dia15 = '".$_Dia[15]."',
							Dia16 = '".$_Dia[16]."',
							Dia17 = '".$_Dia[17]."',
							Dia18 = '".$_Dia[18]."',
							Dia19 = '".$_Dia[19]."',
							Dia20 = '".$_Dia[20]."',
							Dia21 = '".$_Dia[21]."',
							Dia22 = '".$_Dia[22]."',
							Dia23 = '".$_Dia[23]."',
							Dia24 = '".$_Dia[24]."',
							Dia25 = '".$_Dia[25]."',
							Dia26 = '".$_Dia[26]."',
							Dia27 = '".$_Dia[27]."',
							Dia28 = '".$_Dia[28]."',
							Dia29 = '".$_Dia[29]."',
							Dia30 = '".$_Dia[30]."',
							Dia31 = '".$_Dia[31]."',
							DiasPeriodo = '".$field_ba['TotalDiasPeriodo']."',
							DiasPago = '".$DiasPago."',
							DiasFeriados = '".setNumero($TotalFeriados)."',
							DiasInactivos = '".$DiasInactivos."',
							ValorPagar = '".$ValorPagar."',
							TotalPagar = '".$ValorPagar."',
							UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
							UltimaFecha = NOW()";
				execute($sql);
			}
		}
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	anular registro
	elseif ($accion == "anular") {
		mysql_query("BEGIN");
		//	-----------------
		if ($Estado == "AN") die("Registro ya se encuentra anulado.");
		elseif ($Estado == "AP") $NuevoEstado = "CN";
		elseif ($Estado == "CN") $NuevoEstado = "PE";
		elseif ($Estado == "PE") $NuevoEstado = "AN";
		//	actualizar
		$sql = "UPDATE rh_procesocesereing
				SET
					Estado = '".$NuevoEstado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					Tipo = '".$Tipo."' AND
					CodProceso = '".$CodProceso."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		//	-----------------
		mysql_query("COMMIT");
	}
}

//	tabla de disfrutes vacacionales
elseif ($modulo == "disfrute_vacacionales") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-----------------
		//	inserto
		$sql = "INSERT INTO rh_vacaciontabla
				SET
					CodTipoNom = '".$CodTipoNom."',
					NroAnio = '".$NroAnio."',
					DiasDisfrutes = '".$DiasDisfrutes."',
					DiasAdicionales = '".$DiasAdicionales."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	modificar registro
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizar
		$sql = "UPDATE rh_vacaciontabla
				SET
					DiasDisfrutes = '".$DiasDisfrutes."',
					DiasAdicionales = '".$DiasAdicionales."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodTipoNom = '".$CodTipoNom."' AND
					NroAnio = '".$NroAnio."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	eliminar registro
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		//	elimino
		list($CodTipoNom, $NroAnio) = explode("_", $registro);
		$sql = "DELETE FROM rh_vacaciontabla
				WHERE
					CodTipoNom = '".$CodTipoNom."' AND
					NroAnio = '".$NroAnio."'";
		$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		//	-----------------
		mysql_query("COMMIT");
	}
}

//	actualizar periodos vacacionales en forma masiva para los empleados del periodo
elseif ($modulo == "vacaciones-actualizar") {
	mysql_query("BEGIN");
	//	-----------------
	$sql = "SELECT tn.CodTipoNom
			FROM
				tiponomina tn
				INNER JOIN rh_vacaciontabla vt ON (vt.CodTipoNom = tn.CodTipoNom)
			GROUP BY CodTipoNom
			ORDER BY CodTipoNom";
	$field_nomina = getRecords($sql);
	foreach($field_nomina as $fn) {
		##	tabla de disrute
		$sql = "SELECT * FROM rh_vacaciontabla WHERE CodTipoNom = '".$fn['CodTipoNom']."'";
		$field_tabla = getRecords($sql);
		$MaxDisfrute = 0;
		foreach($field_tabla as $ft) {
			$id = $ft['NroAnio'];
			$_DISFRUTES[$id] = $ft['DiasDisfrutes'];
			$_ADICIONAL[$id] = $ft['DiasAdicionales'];
			$MaxDisfrute = $ft['DiasDisfrutes'];
		}
		//	-----------------
		list($AnioActual, $MesActual, $DiaActual) = split("[/.-]", substr($Ahora, 0, 10));
		$FechaActual = "$AnioActual-$MesActual-$DiaActual";
		$PeriodoActual = "$AnioActual-$MesActual";
		$AnioSiguiente = $AnioActual;
		$MesSiguiente = intval($MesActual) + 1;
		if ($MesSiguiente > 12) {
			$AnioSiguiente = $AnioActual + 1;
			$MesSiguiente = "01";
		} elseif ($MesSiguiente < 10) $MesSiguiente = "0$MesSiguiente";
		$PeriodoSiguiente = "$AnioSiguiente-$MesSiguiente";
		$FechaSiguiente = "$AnioSiguiente-$MesSiguiente-".getDiasMes("$AnioSiguiente-$MesSiguiente");
		//	consulto los empleados activos
		$sql = "SELECT
					p.CodPersona,
					p.NomCompleto,
					e.CodEmpleado,
					e.Fingreso,
					e.CodTipoNom,
					SUBSTRING(e.Fingreso, 6, 2) AS MesIngreso,
					pt.Grado
				FROM
					mastpersonas p
					INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
					INNER JOIN rh_puestos pt ON (pt.CodCargo = e.CodCargo)
				WHERE 
					e.Estado = 'A' AND 
					SUBSTRING(e.Fingreso, 6, 2) = '$MesSiguiente'
				ORDER BY CodPersona";
		$field_empleados = getRecords($sql);
		foreach($field_empleados as $field_empleado) {
			list($AniosAntecedente, $MesesAntecedente, $DiasAntecedente) = getTiempoAntecedente($field_empleado['CodPersona'], 'S');
			list($AniosOrganismo, $MesesOrganismo, $DiasOrganismo) = getEdad(formatFechaDMA($field_empleado['Fingreso']), formatFechaDMA($FechaSiguiente));
			list($AniosServicio, $MesesServicio, $DiasServicio) = totalTiempo($AniosAntecedente+$AniosOrganismo, $MesesAntecedente+$MesesOrganismo, $DiasAntecedente+$DiasOrganismo);
			##
			list($AnioIngreso, $MesIngreso, $DiaIngreso) = split("[/.-]", $field_empleado['Fingreso']);
			if ($field_empleado['Grado'] == "99") $DiasPago = $_PARAMETRO["PAGOFINDC"]; else $DiasPago = $_PARAMETRO["PAGOVACA"];
			##
			$NroPeriodo = "";
			$Anio = "";
			$Mes = "";
			$Derecho = "";
			$PendientePeriodo = "";
			$DiasGozados = "";
			$DiasTrabajados = "";
			$DiasInterrumpidos = "";
			$DiasNoGozados = "";
			$TotalUtilizados = "";
			$Pendientes = "";
			$PagosRealizados = "";
			$PendientePago = "";
			//	obtengo los valores almacenados del empleado para el periodo
			$sql = "SELECT
						NroPeriodo,
						Anio,
						Mes,
						Derecho,
						PendientePeriodo,
						DiasGozados,
						DiasTrabajados,
						DiasInterrumpidos,
						DiasNoGozados,
						TotalUtilizados,
						Pendientes,
						PagosRealizados,
						PendientePago
					FROM rh_vacacionperiodo
					WHERE
						CodPersona = '".$field_empleado['CodPersona']."' AND
						CodTipoNom = '".$field_empleado['CodTipoNom']."'";
			$field_periodos = getRecords($sql);	$i=0;
			foreach($field_periodos as $field_periodo) {
				$NroPeriodo[$i] = $field_periodo['NroPeriodo'];
				$Anio[$i] = $field_periodo['Anio'];
				$Mes[$i] = $field_periodo['Mes'];
				$Derecho[$i] = $field_periodo['Derecho'];
				$PendientePeriodo[$i] = $field_periodo['PendientePeriodo'];
				$DiasGozados[$i] = $field_periodo['DiasGozados'];
				$DiasTrabajados[$i] = $field_periodo['DiasTrabajados'];
				$DiasInterrumpidos[$i] = $field_periodo['DiasInterrumpidos'];
				$DiasNoGozados[$i] = $field_periodo['DiasNoGozados'];
				$TotalUtilizados[$i] = $field_periodo['DiasGozados'] - $field_periodo['DiasInterrumpidos'];
				$Pendientes[$i] = $field_periodo['Pendientes'];
				$PagosRealizados[$i] = $field_periodo['PagosRealizados'];
				$PendientePago[$i] = $field_periodo['PendientePago'];
				$i++;
			}
			list($Anios, $Meses, $Dias) = getTiempo(formatFechaDMA($field_empleado['Fingreso']), formatFechaDMA($FechaSiguiente));
			$NroPeriodos = $Anios;
			//	recorro los periodos y almaceno
			$Pendiente = 0;
			for($i=0; $i<$NroPeriodos; $i++) {
				$Anio[$i] = $AnioIngreso + $i;
				$_Mes = intval($Mes[$i]);
				if ($_Mes <= 1 || $_Mes >= 12) $_Mes = "$MesIngreso"; else $_Mes = $Mes[$i];
				if ($NroPeriodo[$i] == "") {
					$NroPeriodo[$i] = $i + 1;
					$Mes[$i] = $MesIngreso;
					##	obtengo los dias de derecho
					if ($_PARAMETRO['VACANTECEDENT'] == "S") {
						if (isset($_DISFRUTES[$i+1+$AniosAntecedente])) $_DiasDisfrutes = $_DISFRUTES[$i+1+$AniosAntecedente];
						else $_DiasDisfrutes = $MaxDisfrute;
					} else {
						if (isset($_DISFRUTES[$i+1])) $_DiasDisfrutes = $_DISFRUTES[$i+1];
						else $_DiasDisfrutes = $MaxDisfrute;
					}
					$Derecho[$i] = $_DiasDisfrutes + $_ADICIONAL[$i+1];
					$PendientePeriodo[$i] += $Pendientes[$i-1];
					$DiasGozados[$i] = 0;
					$DiasTrabajados[$i] = 0;
					$DiasInterrumpidos[$i] = 0;
					$TotalUtilizados[$i] = 0;
					$PendientePago[$i] += $PendientePago[$i-1] + $DiasPago;
					$Pendientes[$i] = $Derecho[$i] + $PendientePeriodo[$i] - $TotalUtilizados[$i];
					##
					$sql = "INSERT INTO rh_vacacionperiodo
							SET
								CodPersona = '".$field_empleado['CodPersona']."',
								NroPeriodo = '".$NroPeriodo[$i]."',
								CodTipoNom = '".$field_empleado['CodTipoNom']."',
								Anio = '".$Anio[$i]."',
								Mes = '".$_Mes."',
								Derecho = '".$Derecho[$i]."',
								PendientePeriodo = '".$PendientePeriodo[$i]."',
								DiasGozados = '".$DiasGozados[$i]."',
								DiasTrabajados = '".$DiasTrabajados[$i]."',
								DiasInterrumpidos = '".$DiasInterrumpidos[$i]."',
								TotalUtilizados = '".$TotalUtilizados[$i]."',
								Pendientes = '".$Pendientes[$i]."',
								PagosRealizados = '".$PagosRealizados[$i]."',
								PendientePago = '".$PendientePago[$i]."',
								UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
								UltimaFecha = NOW()
							ON DUPLICATE KEY UPDATE
								UltimoUsuario = UltimoUsuario";
					execute($sql);
				}
			}
		}
		$sql = "UPDATE mastparametros SET ValorParam = '".$PeriodoSiguiente."' WHERE ParametroClave = 'ACTVACAPER'";
		execute($sql);
	}
	//	-----------------
	mysql_query("COMMIT");
	
}

//	grados de instruccion
elseif ($modulo == "grado_instruccion") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-----------------
		//	inserto
		$sql = "INSERT INTO rh_gradoinstruccion
				SET
					CodGradoInstruccion = '".$CodGradoInstruccion."',
					Descripcion = '".changeUrl($Descripcion)."',
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
		$sql = "UPDATE rh_gradoinstruccion
				SET
					Descripcion = '".changeUrl($Descripcion)."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodGradoInstruccion = '".$CodGradoInstruccion."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "DELETE FROM rh_gradoinstruccion WHERE CodGradoInstruccion = '".$registro."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}

//	profesiones
elseif ($modulo == "profesiones") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-----------------
		//	inserto
		$CodProfesion = codigo('rh_profesiones', 'CodProfesion', 6);
		$sql = "INSERT INTO rh_profesiones
				SET
					CodProfesion = '".$CodProfesion."',
					CodGradoInstruccion = '".$CodGradoInstruccion."',
					Area = '".$Area."',
					Descripcion = '".changeUrl($Descripcion)."',
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
		$sql = "UPDATE rh_profesiones
				SET
					CodGradoInstruccion = '".$CodGradoInstruccion."',
					Area = '".$Area."',
					Descripcion = '".changeUrl($Descripcion)."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodProfesion = '".$CodProfesion."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "DELETE FROM rh_profesiones WHERE CodProfesion = '".$registro."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
?>