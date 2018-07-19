<?php
session_start();
include("../../lib/fphp.php");
include("fphp.php");
//	$__archivo = fopen("$modulo-$accion.sql", "w+");
//	fwrite($__archivo, $sql.";\n\n");
///////////////////////////////////////////////////////////////////////////////
//	PARA AJAX
///////////////////////////////////////////////////////////////////////////////
//	horario laboral
if ($modulo == "horario_laboral") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-----------------
		//	genero codigo
		$CodHorario = getCodigo("rh_horariolaboral", "CodHorario", 3);
		
		//	inserto
		$sql = "INSERT INTO rh_horariolaboral
				SET
					CodHorario = '".$CodHorario."',
					Descripcion = '".changeUrl($Descripcion)."',
					FlagCorrido = '".$FlagCorrido."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	horario
		$_Secuencia = 0;
		$horario = split(";char:tr;", $detalles_horario);
		foreach ($horario as $_detalle) {
			list($_FlagLaborable, $_Dia, $_Entrada1, $_Salida1, $_Entrada2, $_Salida2) = split(";char:td;", $_detalle);
			//	inserto
			$sql = "INSERT INTO rh_horariolaboraldet
					SET
						CodHorario = '".$CodHorario."',
						Secuencia = '".++$_Secuencia."',
						FlagLaborable = '".$_FlagLaborable."',
						Dia = '".$_Dia."',
						Entrada1 = '".$_Entrada1."',
						Salida1 = '".$_Salida1."',
						Entrada2 = '".$_Entrada2."',
						Salida2 = '".$_Salida2."',
						UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
						UltimaFecha = NOW()";
			$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		}
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	modificar
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "UPDATE rh_horariolaboral
				SET
					Descripcion = '".changeUrl($Descripcion)."',
					FlagCorrido = '".$FlagCorrido."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodHorario = '".$CodHorario."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	horario
		$sql = "DELETE FROM rh_horariolaboraldet WHERE CodHorario = '".$CodHorario."'";
		$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		$_Secuencia = 0;
		$horario = split(";char:tr;", $detalles_horario);
		foreach ($horario as $_detalle) {
			list($_FlagLaborable, $_Dia, $_Entrada1, $_Salida1, $_Entrada2, $_Salida2) = split(";char:td;", $_detalle);
			//	inserto
			$sql = "INSERT INTO rh_horariolaboraldet
					SET
						CodHorario = '".$CodHorario."',
						Secuencia = '".++$_Secuencia."',
						FlagLaborable = '".$_FlagLaborable."',
						Dia = '".$_Dia."',
						Entrada1 = '".$_Entrada1."',
						Salida1 = '".$_Salida1."',
						Entrada2 = '".$_Entrada2."',
						Salida2 = '".$_Salida2."',
						UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
						UltimaFecha = NOW()";
			$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		}
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	eliminar
	elseif ($accion == "eliminar") {
		//	elimino
		$sql = "DELETE FROM rh_horariolaboral WHERE CodHorario = '".$registro."'";
		$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	}
}

//	tipos de nomina
elseif ($modulo == "tipo_nomina") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-----------------
		//	inserto
		$sql = "INSERT INTO tiponomina
				SET
					CodTipoNom = '".$CodTipoNom."',
					Nomina = '".changeUrl($Nomina)."',
					TituloBoleta = '".changeUrl($TituloBoleta)."',
					FlagPagoMensual = '".$FlagPagoMensual."',
					CodPerfilConcepto = '".$CodPerfilConcepto."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	periodos
		$periodos = split(";char:tr;", $detalles_periodos);
		foreach ($periodos as $_detalle) {
			list($_Periodo, $_Mes, $_Secuencia) = split(";char:td;", $_detalle);
			//	inserto
			$sql = "INSERT INTO pr_tiponominaperiodo
					SET
						CodTipoNom = '".$CodTipoNom."',
						Periodo = '".$_Periodo."',
						Mes = '".$_Mes."',
						Secuencia = '".$_Secuencia."',
						UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
						UltimaFecha = NOW()";
			$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		}
		
		//	procesos
		if ($detalles_procesos != "") {
			$procesos = split(";char:tr;", $detalles_procesos);
			foreach ($procesos as $_detalle) {
				list($_CodTipoProceso, $_CodTipoDocumento) = split(";char:td;", $_detalle);
				//	inserto
				$sql = "INSERT INTO pr_tiponominaproceso
						SET
							CodTipoNom = '".$CodTipoNom."',
							CodTipoProceso = '".$_CodTipoProceso."',
							CodTipoDocumento = '".$_CodTipoDocumento."',
							UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
							UltimaFecha = NOW()";
				$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			}
		}
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	modificar
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "UPDATE tiponomina
				SET
					Nomina = '".changeUrl($Nomina)."',
					TituloBoleta = '".changeUrl($TituloBoleta)."',
					FlagPagoMensual = '".$FlagPagoMensual."',
					CodPerfilConcepto = '".$CodPerfilConcepto."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodTipoNom = '".$CodTipoNom."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	periodos
		$sql = "DELETE FROM pr_tiponominaperiodo WHERE CodTipoNom = '".$CodTipoNom."'";
		$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		$periodos = split(";char:tr;", $detalles_periodos);
		foreach ($periodos as $_detalle) {
			list($_Periodo, $_Mes, $_Secuencia) = split(";char:td;", $_detalle);
			//	inserto
			$sql = "INSERT INTO pr_tiponominaperiodo
					SET
						CodTipoNom = '".$CodTipoNom."',
						Periodo = '".$_Periodo."',
						Mes = '".$_Mes."',
						Secuencia = '".$_Secuencia."',
						UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
						UltimaFecha = NOW()";
			$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		}
		
		//	procesos
		if ($detalles_procesos != "") {
			$sql = "DELETE FROM pr_tiponominaproceso WHERE CodTipoNom = '".$CodTipoNom."'";
			$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			$procesos = split(";char:tr;", $detalles_procesos);
			foreach ($procesos as $_detalle) {
				list($_CodTipoProceso, $_CodTipoDocumento) = split(";char:td;", $_detalle);
				//	inserto
				$sql = "INSERT INTO pr_tiponominaproceso
						SET
							CodTipoNom = '".$CodTipoNom."',
							CodTipoProceso = '".$_CodTipoProceso."',
							CodTipoDocumento = '".$_CodTipoDocumento."',
							UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
							UltimaFecha = NOW()";
				$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			}
		}
		//	-----------------
		mysql_query("COMMIT");
	}
}

//	tipos de trabajador
elseif ($modulo == "tipo_trabajador") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-----------------
		//	inserto
		$CodTipoTrabajador = codigo("rh_tipotrabajador", "CodTipoTrabajador", 2);
		$sql = "INSERT INTO rh_tipotrabajador
				SET
					CodTipoTrabajador = '".$CodTipoTrabajador."',
					TipoTrabajador = '".changeUrl($TipoTrabajador)."',
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
		$sql = "UPDATE rh_tipotrabajador
				SET
					TipoTrabajador = '".changeUrl($TipoTrabajador)."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodTipoTrabajador = '".$CodTipoTrabajador."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "DELETE FROM rh_tipotrabajador WHERE CodTipoTrabajador = '".$registro."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}

//	perfiles de nomina
elseif ($modulo == "perfil_nomina") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-----------------
		//	inserto
		$CodPerfil = codigo("tipoperfilnom", "CodPerfil", 2);
		$sql = "INSERT INTO tipoperfilnom
				SET
					CodPerfil = '".$CodPerfil."',
					Perfil = '".changeUrl($Perfil)."',
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
		$sql = "UPDATE tipoperfilnom
				SET
					Perfil = '".changeUrl($Perfil)."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodPerfil = '".$CodPerfil."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "DELETE FROM tipoperfilnom WHERE CodPerfil = '".$registro."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}

//	tipos de contrato
elseif ($modulo == "tipo_contrato") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-----------------
		//	inserto
		$sql = "INSERT INTO rh_tipocontrato
				SET
					TipoContrato = '".$TipoContrato."',
					Descripcion = '".changeUrl($Descripcion)."',
					FlagNomina = '".$FlagNomina."',
					FlagVencimiento = '".$FlagVencimiento."',
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
		$sql = "UPDATE rh_tipocontrato
				SET
					Descripcion = '".changeUrl($Descripcion)."',
					FlagNomina = '".$FlagNomina."',
					FlagVencimiento = '".$FlagVencimiento."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE TipoContrato = '".$TipoContrato."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "DELETE FROM rh_tipocontrato WHERE TipoContrato = '".$registro."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}

//	formatos de contrato
elseif ($modulo == "formato_contrato") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-----------------
		//	inserto
		$sql = "INSERT INTO rh_formatocontrato
				SET
					CodFormato = '".$CodFormato."',
					TipoContrato = '".$TipoContrato."',
					Documento = '".changeUrl($Documento)."',
					RutaPlant = '".changeUrl($txtRutaPlant)."',
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
		$sql = "UPDATE rh_formatocontrato
				SET
					TipoContrato = '".$TipoContrato."',
					Documento = '".changeUrl($Documento)."',
					RutaPlant = '".changeUrl($txtRutaPlant)."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodFormato = '".$CodFormato."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		//	elimino
		$RutaPlant = getVar3("SELECT RutaPlant FROM rh_formatocontrato WHERE CodFormato = '".$registro."'");
		unlink('../'.$_PARAMETRO["PATHFORM"].$RutaPlant);
		//	elimino
		execute("DELETE FROM rh_formatocontrato WHERE CodFormato = '".$registro."'");
		//	-----------------
		mysql_query("COMMIT");
	}
}

//	motivos de cese
elseif ($modulo == "motivo_cese") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-----------------
		//	inserto
		$CodMotivoCes = codigo("rh_motivocese", "CodMotivoCes", 2);
		$sql = "INSERT INTO rh_motivocese
				SET
					CodMotivoCes = '".$CodMotivoCes."',
					MotivoCese = '".changeUrl($MotivoCese)."',
					FlagFaltaGrave = '".$FlagFaltaGrave."',
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
		$sql = "UPDATE rh_motivocese
				SET
					MotivoCese = '".changeUrl($MotivoCese)."',
					FlagFaltaGrave = '".$FlagFaltaGrave."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodMotivoCes = '".$CodMotivoCes."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "DELETE FROM rh_motivocese WHERE CodMotivoCes = '".$registro."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
?>