<?php
include("../../lib/fphp.php");
include("fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion".".sql", "w+");
///////////////////////////////////////////////////////////////////////////////
//	PARA AJAX
///////////////////////////////////////////////////////////////////////////////
//	perfil de conceptos
if ($modulo == "conceptos_perfil") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-----------------
		//	genero codigo
		$CodPerfilConcepto = getCodigo("pr_conceptoperfil", "CodPerfilConcepto", 4);
		
		//	inserto
		$sql = "INSERT INTO pr_conceptoperfil
				SET
					CodPerfilConcepto = '".$CodPerfilConcepto."',
					Descripcion = '".changeUrl($Descripcion)."',
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
		$sql = "UPDATE pr_conceptoperfil
				SET
					Descripcion = '".changeUrl($Descripcion)."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodPerfilConcepto = '".$CodPerfilConcepto."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	perfil de conceptos
	elseif ($accion == "conceptos") {
		mysql_query("BEGIN");
		//	-----------------
		//	conceptos
		$sql = "DELETE FROM pr_conceptoperfildetalle WHERE CodPerfilConcepto = '".$CodPerfilConcepto."'";
		execute($sql);
		if ($detalles_conceptos != "") {
			$conceptos = split(";char:tr;", $detalles_conceptos);
			foreach ($conceptos as $_linea) {
				list($_CodTipoProceso, $_CodConcepto, $_cod_partida, $_CuentaDebe, $_CuentaDebePub20, $_FlagDebeCC, $_CuentaHaber, $_CuentaHaberPub20, $_FlagHaberCC, $_FlagCategoriaProg, $_CategoriaProg) = split(";char:td;", $_linea);
				//	inserto
				$sql = "INSERT INTO pr_conceptoperfildetalle
						SET
							CodPerfilConcepto = '".$CodPerfilConcepto."',
							CodTipoProceso = '".$_CodTipoProceso."',
							CodConcepto = '".$_CodConcepto."',
							cod_partida = '".$_cod_partida."',
							CuentaDebe = '".$_CuentaDebe."',
							CuentaDebePub20 = '".$_CuentaDebePub20."',
							FlagDebeCC = '".$_FlagDebeCC."',
							CuentaHaber = '".$_CuentaHaber."',
							CuentaHaberPub20 = '".$_CuentaHaberPub20."',
							FlagHaberCC = '".$_FlagHaberCC."',
							FlagCategoriaProg = '".$_FlagCategoriaProg."',
							CategoriaProg = '".$_CategoriaProg."',
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()";
				execute($sql);
			}
		}
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	eliminar registro
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		//	elimino
		$sql = "DELETE FROM pr_conceptoperfil WHERE CodPerfilConcepto = '".$registro."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
//	control de procesos
elseif ($modulo == "procesos_control") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-----------------
		//	inserto
		$sql = "INSERT INTO pr_procesoperiodo
				SET
					CodOrganismo = '".$CodOrganismo."',
					CodTipoNom = '".$CodTipoNom."',
					Periodo = '".$Periodo."',
					CodTipoProceso = '".$CodTipoProceso."',
					PeriodoNomina = '".$PeriodoNomina."',
					FechaDesde = '".formatFechaAMD($FechaDesde)."',
					FechaHasta = '".formatFechaAMD($FechaHasta)."',
					CreadoPor = '".$CreadoPor."',
					FechaCreado = '".formatFechaAMD($FechaCreado)."',
					FlagProcesado = 'N',
					FlagAprobado = 'N',
					FlagMensual = '".$FlagMensual."',
					FlagUltimaSemana = '".$FlagUltimaSemana."',
					FlagPagado = 'N',
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
		$sql = "UPDATE pr_procesoperiodo
				SET
					PeriodoNomina = '".$PeriodoNomina."',
					FechaDesde = '".formatFechaAMD($FechaDesde)."',
					FechaHasta = '".formatFechaAMD($FechaHasta)."',
					FlagMensual = '".$FlagMensual."',
					FlagUltimaSemana = '".$FlagUltimaSemana."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					CodTipoNom = '".$CodTipoNom."' AND
					Periodo = '".$Periodo."' AND
					CodTipoProceso = '".$CodTipoProceso."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	aprobar
	elseif ($accion == "aprobar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizar
		$sql = "UPDATE pr_procesoperiodo
				SET
					AprobadoPor = '".$AprobadoPor."',
					FechaAprobado = '".formatFechaAMD($FechaAprobado)."',
					FlagAprobado = 'S',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					CodTipoNom = '".$CodTipoNom."' AND
					Periodo = '".$Periodo."' AND
					CodTipoProceso = '".$CodTipoProceso."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	activar/desactivar
	elseif ($accion == "activar") {
		mysql_query("BEGIN");
		//	-----------------
		list($CodOrganismo, $CodTipoNom, $Periodo, $CodTipoProceso) = split("[_]", $registro);
		//	consulto estado
		$sql = "SELECT Estado
				FROM pr_procesoperiodo
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					CodTipoNom = '".$CodTipoNom."' AND
					Periodo = '".$Periodo."' AND
					CodTipoProceso = '".$CodTipoProceso."'";
		$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		if (mysql_num_rows($query)) {
			$field = mysql_fetch_array($query);
			if ($field['Estado'] == "A") $Estado = "I"; else $Estado = "A";
			//	actualizo
			$sql = "UPDATE pr_procesoperiodo
					SET Estado = '".$Estado."'
					WHERE
						CodOrganismo = '".$CodOrganismo."' AND
						CodTipoNom = '".$CodTipoNom."' AND
						Periodo = '".$Periodo."' AND
						CodTipoProceso = '".$CodTipoProceso."'";
			$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		} else die("No se encontr&oacute; el registro");
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	cerrar
	elseif ($accion == "cerrar") {
		execute("BEGIN");
		//	-----------------
		list($CodOrganismo, $CodTipoNom, $Periodo, $CodTipoProceso) = split("[_]", $registro);
		//	actualizo
		$sql = "UPDATE pr_procesoperiodo
				SET 
					FlagPagado = 'S',
					EstadoPago = 'PA'
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					CodTipoNom = '".$CodTipoNom."' AND
					Periodo = '".$Periodo."' AND
					CodTipoProceso = '".$CodTipoProceso."'";
		execute($sql);
		//	-----------------
		execute("COMMIT");
	}
}
//	ejecucion de procesos
elseif ($modulo == "procesos_control_ejecucion") {
	//	nuevo
	if ($accion == "ejecutar") {
		mysql_query("BEGIN");
		//	-----------------
		include("funciones_globales_nomina.php");
		##	parametros
		$_PARAMETROS_FORMULA = PARAMETROS_FORMULA();
		extract($_PARAMETROS_FORMULA);
		##	
		$sql = "SELECT * FROM pr_procesoperiodo WHERE CodOrganismo = '$fCodOrganismo' AND CodTipoNom = '$fCodTipoNom' AND Periodo = '$fPeriodo' AND CodTipoProceso = '$fCodTipoProceso'";
		$field_proceso = getRecord($sql);
		##	variables generales
		$ALIVAC = $_PARAMETRO['ALIVAC'];
		$ALIFIN = $_PARAMETRO['ALIFIN'];
		$_ARGS['_ORGANISMO'] = $fCodOrganismo;
		$_ARGS['_NOMINA'] = $fCodTipoNom;
		$_ARGS['_PERIODO'] = $fPeriodo;
		$_ARGS['_PROCESO'] = $fCodTipoProceso;
		$_ARGS['_ULTIMA_SEMANA'] = $field_proceso['FlagUltimaSemana'];
		if ($PreNomina == "S") {
			$_ARGS['_PERIODONOMINA'] = getVar2("pr_procesoperiodoprenomina","PeriodoNomina",array('CodOrganismo','CodTipoNom','Periodo','CodTipoProceso'),array($fCodOrganismo,$fCodTipoNom,$fPeriodo,$fCodTipoProceso));
			list($_ARGS['_DESDE'], $_ARGS['_HASTA']) = FECHA_PROCESO('pr_procesoperiodoprenomina');
			$_ARGS['_DIAS'] = DIAS_PROCESO('pr_procesoperiodoprenomina');
		}
		else {
			$_ARGS['_PERIODONOMINA'] = getVar2("pr_procesoperiodo","PeriodoNomina",array('CodOrganismo','CodTipoNom','Periodo','CodTipoProceso'),array($fCodOrganismo,$fCodTipoNom,$fPeriodo,$fCodTipoProceso));
			list($_ARGS['_DESDE'], $_ARGS['_HASTA']) = FECHA_PROCESO('pr_procesoperiodo');
			$_ARGS['_DIAS'] = DIAS_PROCESO('pr_procesoperiodo');
		}
		$_ARGS['_RETROACTIVO'] = getVar3("SELECT FlagRetroactivo FROM pr_tipoproceso WHERE CodTipoProceso = '".$_ARGS['_PROCESO']."'");
		list($_ARGS['_ANO_PROCESO'], $_ARGS['_MES_PROCESO']) = split("[./-]", $_ARGS['_PERIODO']);
		$_ARGS['_DIAS_ANIO'] = DIAS_ANIO();
		//	empleados
		if ($detalles_personas != "") {
			//	proceso
			if ($PreNomina == "S") {
				$sql = "UPDATE pr_procesoperiodoprenomina
						SET
							ProcesadoPor = '".$_SESSION['CODPERSONA_ACTUAL']."',
							FechaProceso = NOW(),
							UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
							UltimaFecha = NOW()
						WHERE
							CodTipoNom = '".$fCodTipoNom."' AND 
							Periodo = '".$fPeriodo."' AND 
							CodOrganismo = '".$fCodOrganismo."' AND 
							CodTipoProceso = '".$fCodTipoProceso."'";
			} else {
				$sql = "UPDATE pr_procesoperiodo
						SET
							ProcesadoPor = '".$_SESSION['CODPERSONA_ACTUAL']."',
							FechaProceso = NOW(),
							UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
							UltimaFecha = NOW()
						WHERE
							CodTipoNom = '".$fCodTipoNom."' AND 
							Periodo = '".$fPeriodo."' AND 
							CodOrganismo = '".$fCodOrganismo."' AND 
							CodTipoProceso = '".$fCodTipoProceso."'";
			}
			execute($sql);
			//	empleados selecionados
			$personas = split(";char:tr;", $detalles_personas);
			foreach ($personas as $CodPersona) {
				//	elimino datos anteriores
				if ($PreNomina == "S") {
					$sql = "DELETE FROM pr_tiponominaempleadoconceptoprenomina
							WHERE
								CodPersona = '".$CodPersona."' AND 
								CodTipoNom = '".$fCodTipoNom."' AND 
								Periodo = '".$fPeriodo."' AND 
								CodOrganismo = '".$fCodOrganismo."' AND 
								CodTipoProceso = '".$fCodTipoProceso."'";
					execute($sql);
					##
					$sql = "DELETE FROM pr_tiponominaempleadoprenomina
							WHERE
								CodPersona = '".$CodPersona."' AND
								CodTipoNom = '".$fCodTipoNom."' AND
								Periodo = '".$fPeriodo."' AND
								CodOrganismo = '".$fCodOrganismo."' AND
								CodTipoProceso = '".$fCodTipoProceso."'";
					execute($sql);
				} else {
					$sql = "DELETE FROM pr_tiponominaempleadoconcepto
							WHERE
								CodPersona = '".$CodPersona."' AND 
								CodTipoNom = '".$fCodTipoNom."' AND 
								Periodo = '".$fPeriodo."' AND 
								CodOrganismo = '".$fCodOrganismo."' AND 
								CodTipoProceso = '".$fCodTipoProceso."'";
					execute($sql);
					##
					$sql = "DELETE FROM pr_tiponominaempleado
							WHERE
								CodPersona = '".$CodPersona."' AND
								CodTipoNom = '".$fCodTipoNom."' AND
								Periodo = '".$fPeriodo."' AND
								CodOrganismo = '".$fCodOrganismo."' AND
								CodTipoProceso = '".$fCodTipoProceso."'";
					execute($sql);
					##	interfase ctas x pagar
					$sql = "SELECT
								CodProveedor,
								CodTipoDocumento,
								NroDocumento
							FROM pr_obligaciones
							WHERE
								CodTipoNom = '".$fCodTipoNom."' AND
								PeriodoNomina = '".$fPeriodo."' AND
								CodTipoProceso = '".$fCodTipoProceso."' AND
								CodOrganismo = '".$fCodOrganismo."' AND
								Estado = 'PE'";
					$fprobligacion = getRecords($sql);
					foreach ($fprobligacion as $field_probligacion) {
						$sql = "DELETE FROM pr_obligacionescuenta
								WHERE
									CodProveedor = '".$field_probligacion['CodProveedor']."' AND
									CodTipoDocumento = '".$field_probligacion['CodTipoDocumento']."' AND
									NroDocumento = '".$field_probligacion['NroDocumento']."'";
						execute($sql);
						##
						$sql = "DELETE FROM pr_obligacionesretenciones
								WHERE
									CodProveedor = '".$field_probligacion['CodProveedor']."' AND
									CodTipoDocumento = '".$field_probligacion['CodTipoDocumento']."' AND
									NroDocumento = '".$field_probligacion['NroDocumento']."'";
						execute($sql);
					}
					##
					$sql = "DELETE FROM pr_obligaciones
							WHERE
								CodTipoNom = '".$fCodTipoNom."' AND
								PeriodoNomina = '".$fPeriodo."' AND
								CodTipoProceso = '".$fCodTipoProceso."' AND
								CodOrganismo = '".$fCodOrganismo."' AND
								Estado = 'PE'";
					execute($sql);
				}
				//	consulto empleado
				$sql = "SELECT
							p.CodPersona,
							e.CodEmpleado,
							e.Fingreso,
							e.Estado,
							e.Fegreso,
							e.CodTipoPago,
							e.CodCargo,
							e.CodMotivoCes,
							e.ObsCese,
							e.SueldoActual,
							e.SueldoAnterior,
							e.FechaFinNomina,
							bp.Ncuenta,
							bp.CodBanco,
							bp.TipoCuenta,
							p.EstadoCivil,
							p.Fnacimiento,
							p.Sexo,
							pt1.Grado,
							pt2.Grado AS GradoEncargado
						FROM
							mastpersonas p
							INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
							LEFT JOIN bancopersona bp ON (e.CodPersona = bp.CodPersona AND FlagPrincipal = 'S')
							LEFT JOIN rh_puestos pt1 ON (pt1.CodCargo = e.CodCargo)
							LEFT JOIN rh_puestos pt2 ON (pt2.CodCargo = e.CodCargoTemp)
						WHERE p.CodPersona = '".$CodPersona."'";
				$field_empleado = getRecord($sql);
				##	variables empleados
				$_ARGS['_PERSONA'] = $CodPersona;
				$_ARGS['_EMPLEADO'] = $field_empleado['CodEmpleado'];
				$_ARGS['_FECHA_INGRESO'] = $field_empleado['Fingreso'];
				list($_ARGS['_ANO_INGRESO'], $_ARGS['_MES_INGRESO'], $_ARGS['_DIA_INGRESO']) = split("[./-]", $_ARGS['_FECHA_INGRESO']);
				$_ARGS['_FECHA_EGRESO'] = $field_empleado['Fegreso'];
				list($_ARGS['_ANO_EGRESO'], $_ARGS['_MES_EGRESO'], $_ARGS['_DIA_EGRESO']) = split("[./-]", $_ARGS['_FECHA_EGRESO']);
				$_ARGS['_FECHA_EGRESO_NOMINA'] = $field_empleado['FechaFinNomina'];
				list($_ARGS['_ANO_EGRESO_NOMINA'], $_ARGS['_MES_EGRESO_NOMINA'], $_ARGS['_DIA_EGRESO_NOMINA']) = split("[./-]", $_ARGS['_FECHA_EGRESO_NOMINA']);
				$_ARGS['_ESTADO'] = $field_empleado['Estado'];
				$_ARGS['_TIPO_PAGO'] = $field_empleado['CodTipoPago'];
				$_ARGS['_CTA_BANCARIA'] = $field_empleado['Ncuenta'];
				$_ARGS['_BANCO'] = $field_empleado['CodBanco'];
				$_ARGS['_TIPO_CUENTA'] = $field_empleado['TipoCuenta'];
				$_ARGS['_CARGO'] = $field_empleado['CodCargo'];
				$_ARGS['_GRADO_SALARIAL'] = $field_empleado['Grado'];
				$_ARGS['_GRADO_SALARIAL_ENCARGADO'] = $field_empleado['GradoEncargado'];
				$_ARGS['_MOTIVO_CESE'] = $field_empleado['CodMotivoCes'];
				$_ARGS['_SUELDO_ACTUAL'] = $field_empleado['SueldoActual'];
				$_ARGS['_SUELDO_ANTERIOR'] = $field_empleado['SueldoAnterior'];
				$_ARGS['_SUELDO_BASICO'] = SUELDO_BASICO();
				$_ARGS['_SUELDO_BASICO_DIARIO'] = round(($_ARGS['_SUELDO_BASICO'] / $_ARGS['_DIAS']), 2);
				$_ARGS['_SUELDO_ACTUAL_DIARIO'] = round(($_ARGS['_SUELDO_ACTUAL'] / 30), 2);
				$_ARGS['_SUELDO_POR_HORA'] = round(($_ARGS['_SUELDO_ACTUAL_DIARIO'] / 7), 2);
				$_ARGS['_DIAS_SUELDO_BASICO'] = DIAS_SUELDO_BASICO();
				$_ARGS['_SUELDO_NORMAL'] = 0;
				$_ARGS['_SUELDO_NORMAL_DIARIO'] = 0;
				$_ARGS['_SUELDO_INTEGRAL'] = 0;
				$_ARGS['_SUELDO_INTEGRAL_DIARIO'] = 0;
				$_ARGS['_ASIGNACIONES'] = 0;
				$_ARGS['_PROVISIONES'] = 0;
				$_ARGS['_DEDUCCIONES'] = 0;
				$_ARGS['_APORTES'] = 0;
				$_ARGS['_ESTADO_CIVIL'] = $field_empleado['EstadoCivil'];
				$_ARGS['_SEXO'] = $field_empleado['Sexo'];
				$_ARGS['_FECHA_NACIMIENTO'] = $field_empleado['Fnacimiento'];
				$_ARGS['_EDAD'] = EDAD_PERSONA();
				unset($_CONCEPTO);
				//	consulto los conceptos
				$sql = "(SELECT
							pc.CodConcepto,
							pc.Descripcion,
							pc.PlanillaOrden,
							pc.FlagAutomatico,
							pc.Formula,
							pc.Tipo,
							pc.FlagBono,
							pec.Monto,
							pec.Cantidad,
							'1' AS Orden,
							pec.FlagManual
						 FROM
							pr_empleadoconcepto pec
							INNER JOIN pr_concepto pc ON (pec.CodConcepto = pc.CodConcepto)
							INNER JOIN pr_conceptotiponomina pctn ON (pc.CodConcepto = pctn.CodConcepto AND pctn.CodTipoNom = '".$_ARGS['_NOMINA']."' )
						 WHERE
							(pec.Estado = 'A' AND pc.Estado = 'A') AND
							(pc.Tipo = 'I') AND	(pec.CodPersona = '".$CodPersona."') AND
							(pec.Procesos = '[TODOS]' OR pec.Procesos LIKE '%".$_ARGS['_PROCESO']."%') AND
							((pec.TipoAplicacion = 'T' AND 
							  pec.PeriodoHasta >= '".$_ARGS['_PERIODO']."' AND 
							  pec.PeriodoDesde <= '".$_ARGS['_PERIODO']."') OR 
							  (pec.TipoAplicacion = 'P' AND pec.PeriodoDesde <= '".$_ARGS['_PERIODO']."'))
						 GROUP BY CodConcepto)
						UNION
						(SELECT
							pc.CodConcepto,
							pc.Descripcion,
							pc.PlanillaOrden,
							pc.FlagAutomatico,
							pc.Formula,
							pc.Tipo,
							pc.FlagBono,
							'' AS Monto,
							'' AS Cantidad,
							'1' AS Orden,
							'N' AS FlagManual
						 FROM
							pr_concepto pc
							INNER JOIN pr_conceptoproceso pcp ON (pc.CodConcepto = pcp.CodConcepto AND 
																  pcp.CodTipoProceso = '".$_ARGS['_PROCESO']."')
							INNER JOIN pr_conceptotiponomina pctn ON (pc.CodConcepto = pctn.CodConcepto AND 
																	  pctn.CodTipoNom = '".$_ARGS['_NOMINA']."' )
						 WHERE
							(pc.Estado = 'A') AND
							 pc.Tipo = 'I' AND pc.FlagAutomatico = 'S' AND 
							 pc.CodConcepto NOT IN (SELECT CodConcepto FROM pr_empleadoconcepto WHERE CodPersona = '".$CodPersona."')
						 GROUP BY CodConcepto)
						UNION
						(SELECT
							pc.CodConcepto,
							pc.Descripcion,
							pc.PlanillaOrden,
							pc.FlagAutomatico,
							pc.Formula,
							pc.Tipo,
							pc.FlagBono,
							pec.Monto,
							pec.Cantidad,
							'2' AS Orden,
							pec.FlagManual
						 FROM
							pr_empleadoconcepto pec
							INNER JOIN pr_concepto pc ON (pec.CodConcepto = pc.CodConcepto)
							INNER JOIN pr_conceptotiponomina pctn ON (pc.CodConcepto = pctn.CodConcepto AND pctn.CodTipoNom = '".$_ARGS['_NOMINA']."' )
						 WHERE
							(pec.Estado = 'A' AND pc.Estado = 'A') AND
							(pc.Tipo = 'P') AND	(pec.CodPersona = '".$CodPersona."') AND
							(pec.Procesos = '[TODOS]' OR pec.Procesos LIKE '%".$_ARGS['_PROCESO']."%') AND
							((pec.TipoAplicacion = 'T' AND pec.PeriodoHasta >= '".$_ARGS['_PERIODO']."' AND pec.PeriodoDesde <= '".$_ARGS['_PERIODO']."') OR 
							 (pec.TipoAplicacion = 'P' AND pec.PeriodoDesde <= '".$_ARGS['_PERIODO']."'))
						GROUP BY CodConcepto)
						UNION
						(SELECT
							pc.CodConcepto,
							pc.Descripcion,
							pc.PlanillaOrden,
							pc.FlagAutomatico,
							pc.Formula,
							pc.Tipo,
							pc.FlagBono,
							'' AS Monto,
							'' AS Cantidad,
							'2' AS Orden,
							'N' AS FlagManual
						 FROM
							pr_concepto pc
							INNER JOIN pr_conceptoproceso pcp ON (pc.CodConcepto = pcp.CodConcepto AND pcp.CodTipoProceso = '".$_ARGS['_PROCESO']."')
							INNER JOIN pr_conceptotiponomina pctn ON (pc.CodConcepto = pctn.CodConcepto AND pctn.CodTipoNom = '".$_ARGS['_NOMINA']."' )
						 WHERE
							(pc.Estado = 'A') AND
							pc.Tipo = 'P' AND pc.FlagAutomatico = 'S' AND 
							pc.CodConcepto NOT IN (SELECT CodConcepto FROM pr_empleadoconcepto WHERE CodPersona = '".$CodPersona."')
						GROUP BY CodConcepto)
						UNION
						(SELECT
							pc.CodConcepto,
							pc.Descripcion,
							pc.PlanillaOrden,
							pc.FlagAutomatico,
							pc.Formula,
							pc.Tipo,
							pc.FlagBono,
							pec.Monto,
							pec.Cantidad,
							'3' AS Orden,
							pec.FlagManual
						 FROM
							pr_empleadoconcepto pec
							INNER JOIN pr_concepto pc ON (pec.CodConcepto = pc.CodConcepto)
							INNER JOIN pr_conceptotiponomina pctn ON (pc.CodConcepto = pctn.CodConcepto AND pctn.CodTipoNom = '".$_ARGS['_NOMINA']."' )
						 WHERE
							(pec.Estado = 'A' AND pc.Estado = 'A') AND
							(pc.Tipo = 'D') AND	(pec.CodPersona = '".$CodPersona."') AND
							(pec.Procesos = '[TODOS]' OR pec.Procesos LIKE '%".$_ARGS['_PROCESO']."%') AND
							((pec.TipoAplicacion = 'T' AND pec.PeriodoHasta >= '".$_ARGS['_PERIODO']."' AND pec.PeriodoDesde <= '".$_ARGS['_PERIODO']."') OR 
							 (pec.TipoAplicacion = 'P' AND pec.PeriodoDesde <= '".$_ARGS['_PERIODO']."'))
						GROUP BY CodConcepto)
						UNION
						(SELECT
							pc.CodConcepto,
							pc.Descripcion,
							pc.PlanillaOrden,
							pc.FlagAutomatico,
							pc.Formula,
							pc.Tipo,
							pc.FlagBono,
							'' AS Monto,
							'' AS Cantidad,
							'3' AS Orden,
							'N' AS FlagManual
						 FROM
							pr_concepto pc
							INNER JOIN pr_conceptoproceso pcp ON (pc.CodConcepto = pcp.CodConcepto AND pcp.CodTipoProceso = '".$_ARGS['_PROCESO']."')
							INNER JOIN pr_conceptotiponomina pctn ON (pc.CodConcepto = pctn.CodConcepto AND pctn.CodTipoNom = '".$_ARGS['_NOMINA']."' )
						 WHERE
							(pc.Estado = 'A') AND
							pc.Tipo = 'D' AND pc.FlagAutomatico = 'S' AND 
							pc.CodConcepto NOT IN (SELECT CodConcepto FROM pr_empleadoconcepto WHERE CodPersona = '".$CodPersona."')
						 GROUP BY CodConcepto)
						UNION
						(SELECT
							pc.CodConcepto,
							pc.Descripcion,
							pc.PlanillaOrden,
							pc.FlagAutomatico,
							pc.Formula,
							pc.Tipo,
							pc.FlagBono,
							pec.Monto,
							pec.Cantidad,
							'4' AS Orden,
							pec.FlagManual
						 FROM
							pr_empleadoconcepto pec
							INNER JOIN pr_concepto pc ON (pec.CodConcepto = pc.CodConcepto)
							INNER JOIN pr_conceptotiponomina pctn ON (pc.CodConcepto = pctn.CodConcepto AND pctn.CodTipoNom = '".$_ARGS['_NOMINA']."' )
						 WHERE
							(pec.Estado = 'A' AND pc.Estado = 'A') AND
							(pc.Tipo = 'A') AND	(pec.CodPersona = '".$CodPersona."') AND
							(pec.Procesos = '[TODOS]' OR pec.Procesos LIKE '%".$_ARGS['_PROCESO']."%') AND
							((pec.TipoAplicacion = 'T' AND pec.PeriodoHasta >= '".$_ARGS['_PERIODO']."' AND pec.PeriodoDesde <= '".$_ARGS['_PERIODO']."') OR 
							 (pec.TipoAplicacion = 'P' AND pec.PeriodoDesde <= '".$_ARGS['_PERIODO']."'))
						GROUP BY CodConcepto)
						UNION
						(SELECT
							pc.CodConcepto,
							pc.Descripcion,
							pc.PlanillaOrden,
							pc.FlagAutomatico,
							pc.Formula,
							pc.Tipo,
							pc.FlagBono,
							'' AS Monto,
							'' AS Cantidad,
							'4' AS Orden,
							'N' AS FlagManual
						 FROM
							pr_concepto pc
							INNER JOIN pr_conceptoproceso pcp ON (pc.CodConcepto = pcp.CodConcepto AND pcp.CodTipoProceso = '".$_ARGS['_PROCESO']."')
							INNER JOIN pr_conceptotiponomina pctn ON (pc.CodConcepto = pctn.CodConcepto AND pctn.CodTipoNom = '".$_ARGS['_NOMINA']."' )
						 WHERE
							(pc.Estado = 'A') AND
							pc.Tipo = 'A' AND pc.FlagAutomatico = 'S' AND 
							pc.CodConcepto NOT IN (SELECT CodConcepto FROM pr_empleadoconcepto WHERE CodPersona = '".$CodPersona."')
						 GROUP BY CodConcepto)
						ORDER BY Orden, PlanillaOrden";
				$fconceptos = getRecords($sql);
				foreach ($fconceptos as $field_conceptos) {
					##	variables conceptos
					$ID = $field_conceptos['CodConcepto'];
					$_ARGS['_CONCEPTO'] = $field_conceptos['CodConcepto'];
					$_ARGS['_TIPO_CONCEPTO'] = $field_conceptos['Tipo'];
					$_C = "\$C_".$_ARGS['_CONCEPTO'];
					//	si tiene formula
					if (trim($field_conceptos['Formula']) != "" && $field_conceptos['FlagManual'] != "S") {
						##	variables conceptos
						$_ARGS['_FORMULA'] = $field_conceptos['Formula'];
						$_ARGS['_FLAGBONO'] = $field_conceptos['FlagBono'];
						$_ARGS['_MONTO'] = 0;
						$_ARGS['_CANTIDAD'] = 0;
						$_MONTO = 0;
						$_CANTIDAD = 0;
						//	ejecuto
						extract($_ARGS);
						eval($field_conceptos['Formula']);
						$_ARGS['_MONTO'] = round($_MONTO, 2);
						$_ARGS['_CANTIDAD'] = round($_CANTIDAD, 2);
					} else {
						$_ARGS['_MONTO'] = round($field_conceptos['Monto'], 2);
						$_ARGS['_CANTIDAD'] = round($field_conceptos['Cantidad'], 2);
					}
					##	valor del concepto
					$_VALOR[$ID] = $_ARGS['_MONTO'];
					$CONCEPTO = "$_C = $_ARGS[_MONTO];";
					eval($CONCEPTO);
					//	inserto el concepto
					if ($_ARGS['_MONTO'] > 0) {
						##	sumadores de los totales por tipo
						if ($_ARGS['_TIPO_CONCEPTO'] == "I") {
							$_ARGS['_ASIGNACIONES'] += $_ARGS['_MONTO'];
							$_ARGS['_SUELDO_NORMAL'] += $_ARGS['_MONTO'];
							$_ARGS['_SUELDO_NORMAL_DIARIO'] = round(($_ARGS['_SUELDO_NORMAL'] / $_ARGS['_DIAS_SUELDO_BASICO']), 2);
						}
						elseif ($_ARGS['_TIPO_CONCEPTO'] == "P") {
							$_ARGS['_PROVISIONES'] += $_ARGS['_MONTO'];
							if ($_ARGS['_CONCEPTO'] == $_PARAMETRO['ALIVAC'] || $_ARGS['_CONCEPTO'] == $_PARAMETRO['ALIFIN']) {
								$_ARGS['_SUELDO_INTEGRAL'] = $_ARGS['_SUELDO_NORMAL'] + ($_VALOR[$ALIVAC] * $_ARGS['_DIAS_SUELDO_BASICO']) + ($_VALOR[$ALIFIN] * $_ARGS['_DIAS_SUELDO_BASICO']);
								$_ARGS['_SUELDO_INTEGRAL_DIARIO'] = round(($_ARGS['_SUELDO_INTEGRAL'] / $_ARGS['_DIAS_SUELDO_BASICO']), 2);
								$_ARGS['_SUELDO_INTEGRAL_PARCIAL'] = $_ARGS['_SUELDO_NORMAL'] + ($_VALOR[$ALIVAC] * $_ARGS['_DIAS_SUELDO_BASICO']);
								$_ARGS['_SUELDO_INTEGRAL_PARCIAL_DIARIO'] = round(($_ARGS['_SUELDO_INTEGRAL_PARCIAL'] / $_ARGS['_DIAS_SUELDO_BASICO']), 2);
							}
						}
						elseif ($_ARGS['_TIPO_CONCEPTO'] == "D") $_ARGS['_DEDUCCIONES'] += $_ARGS['_MONTO'];
						elseif ($_ARGS['_TIPO_CONCEPTO'] == "A") $_ARGS['_APORTES'] += $_ARGS['_MONTO'];
						
						//	inserto
						if ($PreNomina == "S") {
							$sql = "INSERT INTO pr_tiponominaempleadoconceptoprenomina
									SET
										CodTipoNom = '".$_ARGS['_NOMINA']."',
										Periodo = '".$_ARGS['_PERIODO']."',
										CodPersona = '".$_ARGS['_PERSONA']."',
										CodOrganismo = '".$_ARGS['_ORGANISMO']."',
										CodConcepto = '".$_ARGS['_CONCEPTO']."',
										CodTipoProceso = '".$_ARGS['_PROCESO']."',
										Monto = '".$_ARGS['_MONTO']."',
										Cantidad = '".$_ARGS['_CANTIDAD']."',
										UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
										UltimaFecha = NOW()";
						} else {
							$sql = "INSERT INTO pr_tiponominaempleadoconcepto
									SET
										CodTipoNom = '".$_ARGS['_NOMINA']."',
										Periodo = '".$_ARGS['_PERIODO']."',
										CodPersona = '".$_ARGS['_PERSONA']."',
										CodOrganismo = '".$_ARGS['_ORGANISMO']."',
										CodConcepto = '".$_ARGS['_CONCEPTO']."',
										CodTipoProceso = '".$_ARGS['_PROCESO']."',
										Monto = '".$_ARGS['_MONTO']."',
										Cantidad = '".$_ARGS['_CANTIDAD']."',
										UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
										UltimaFecha = NOW()";
						}
						execute($sql);
					}
				}
				//	inserto empleado
				if ($_ARGS['_ASIGNACIONES'] > 0 || $_ARGS['_PROVISIONES'] > 0) {
					//	inserto
					$_ARGS['_TOTAL_NETO'] = $_ARGS['_ASIGNACIONES'] - $_ARGS['_DEDUCCIONES'];
					if ($PreNomina == "S") {
						$sql = "INSERT INTO pr_tiponominaempleadoprenomina
								SET
									CodTipoNom = '".$_ARGS['_NOMINA']."',
									Periodo = '".$_ARGS['_PERIODO']."',
									CodPersona = '".$_ARGS['_PERSONA']."',
									CodOrganismo = '".$_ARGS['_ORGANISMO']."',
									CodTipoProceso = '".$_ARGS['_PROCESO']."',
									SueldoBasico = '".$_ARGS['_SUELDO_ACTUAL']."',
									TotalIngresos = '".$_ARGS['_ASIGNACIONES']."',
									TotalEgresos = '".$_ARGS['_DEDUCCIONES']."',
									TotalPatronales = '".$_ARGS['_APORTES']."',
									TotalProvisiones = '".$_ARGS['_PROVISIONES']."',
									TotalNeto = '".$_ARGS['_TOTAL_NETO']."',
									GeneradoPor = '".$_SESSION['CODPERSONA_ACTUAL']."',
									FechaGeneracion = NOW(),
									UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
									UltimaFecha = NOW()";
					} else {
						$sql = "INSERT INTO pr_tiponominaempleado
								SET
									CodTipoNom = '".$_ARGS['_NOMINA']."',
									Periodo = '".$_ARGS['_PERIODO']."',
									CodPersona = '".$_ARGS['_PERSONA']."',
									CodOrganismo = '".$_ARGS['_ORGANISMO']."',
									CodTipoProceso = '".$_ARGS['_PROCESO']."',
									SueldoBasico = '".$_ARGS['_SUELDO_ACTUAL']."',
									SueldoIntegral = '".$_ARGS['_SUELDO_INTEGRAL']."',
									TotalIngresos = '".$_ARGS['_ASIGNACIONES']."',
									TotalEgresos = '".$_ARGS['_DEDUCCIONES']."',
									TotalPatronales = '".$_ARGS['_APORTES']."',
									TotalProvisiones = '".$_ARGS['_PROVISIONES']."',
									TotalNeto = '".$_ARGS['_TOTAL_NETO']."',
									CodBanco = '".$_ARGS['_BANCO']."',
									TipoCuenta = '".$_ARGS['_TIPO_CUENTA']."',
									Ncuenta = '".$_ARGS['_CTA_BANCARIA']."',
									CodTipoPago = '".$_ARGS['_TIPO_PAGO']."',
									GeneradoPor = '".$_SESSION['CODPERSONA_ACTUAL']."',
									FechaGeneracion = NOW(),
									UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
									UltimaFecha = NOW()";
					}
					execute($sql);
				}
				//	fin de mes
				if (($_ARGS['_PROCESO'] == "ADE" || $_ARGS['_PROCESO'] == "FIN") && $PreNomina != "S") {
					//	inserto en sueldos
					$sql = "INSERT INTO rh_sueldos
							SET
								CodPersona = '".$_ARGS['_PERSONA']."',
								Periodo = '".$_ARGS['_PERIODO']."',
								Sueldo = '".$_ARGS['_SUELDO_ACTUAL']."',
								SueldoNormal = '".$_ARGS['_SUELDO_NORMAL']."',
								SueldoIntegral = '".$_ARGS['_SUELDO_INTEGRAL']."',
								SueldoIntegralParcial = '".$_ARGS['_SUELDO_INTEGRAL_PARCIAL']."',
								AliVac = '".$_VALOR[$ALIVAC]."',
								AliFin = '".$_VALOR[$ALIFIN]."',
								UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
								UltimaFecha = NOW()
							ON DUPLICATE KEY UPDATE
								Sueldo = '".$_ARGS['_SUELDO_BASICO']."',
								SueldoNormal = '".$_ARGS['_SUELDO_NORMAL']."',
								SueldoIntegral = '".$_ARGS['_SUELDO_INTEGRAL']."',
								SueldoIntegralParcial = '".$_ARGS['_SUELDO_INTEGRAL_PARCIAL']."',
								AliVac = '".$_VALOR[$ALIVAC]."',
								AliFin = '".$_VALOR[$ALIFIN]."',
								UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
								UltimaFecha = NOW()";
					execute($sql);
				}
				//	quincena
				if (($_ARGS['_PROCESO'] == "QU1" || $_ARGS['_PROCESO'] == "QU2") && $PreNomina != "S") {
					$sql = "SELECT
								SUM(CASE WHEN tnec.CodConcepto = '0001' THEN tnec.Monto ELSE 0 END) AS Sueldo,
								SUM(CASE WHEN c.Tipo = 'I' THEN tnec.Monto ELSE 0 END) AS SueldoNormal,
								SUM(CASE WHEN tnec.CodConcepto = '0046' THEN tnec.Monto ELSE 0 END) AS AliVac,
								SUM(CASE WHEN tnec.CodConcepto = '0047' THEN tnec.Monto ELSE 0 END) AS AliFin
							FROM
								pr_tiponominaempleadoconcepto tnec
								INNER JOIN pr_concepto c ON (c.CodConcepto = tnec.CodConcepto)
							WHERE
								tnec.CodTipoNom = '".$_ARGS['_NOMINA']."' AND
								tnec.Periodo = '".$_ARGS['_PERIODO']."' AND
								tnec.CodPersona = '".$_ARGS['_PERSONA']."' AND
								tnec.CodOrganismo = '".$_ARGS['_ORGANISMO']."' AND
								(tnec.CodTipoProceso = 'QU1' OR tnec.CodTipoProceso = 'QU2')";
					$field_sueldos = getRecord($sql);
					$_SueldoIntegral = $field_sueldos['SueldoNormal'] + ($field_sueldos['AliVac'] * 30) + ($field_sueldos['AliFin'] * 30);
					$_SueldoIntegralParcial = $field_sueldos['SueldoNormal'] + ($field_sueldos['AliVac'] * 30);
					//	inserto en sueldos
					$sql = "INSERT INTO rh_sueldos
							SET
								CodPersona = '".$_ARGS['_PERSONA']."',
								Periodo = '".$_ARGS['_PERIODO']."',
								Sueldo = '".$field_sueldos['Sueldo']."',
								SueldoNormal = '".$field_sueldos['SueldoNormal']."',
								SueldoIntegral = '".$_SueldoIntegral."',
								SueldoIntegralParcial = '".$_SueldoIntegralParcial."',
								AliVac = '".$field_sueldos['AliVac']."',
								AliFin = '".$field_sueldos['AliFin']."',
								UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
								UltimaFecha = NOW()
							ON DUPLICATE KEY UPDATE
								Sueldo = '".$field_sueldos['Sueldo']."',
								SueldoNormal = '".$field_sueldos['SueldoNormal']."',
								SueldoIntegral = '".$_SueldoIntegral."',
								SueldoIntegralParcial = '".$_SueldoIntegralParcial."',
								AliVac = '".$field_sueldos['AliVac']."',
								AliFin = '".$field_sueldos['AliFin']."',
								UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
								UltimaFecha = NOW()";
					execute($sql);
				}
				//	semana
				elseif (($_ARGS['_PROCESO'] == "SE1" || $_ARGS['_PROCESO'] == "SE2" || $_ARGS['_PROCESO'] == "SE3" || $_ARGS['_PROCESO'] == "SE4" || $_ARGS['_PROCESO'] == "SE5") && $PreNomina != "S") {
					$sql = "SELECT
								SUM(CASE WHEN tnec.CodConcepto = '0001' THEN tnec.Monto ELSE 0 END) AS Sueldo,
								SUM(CASE WHEN tnec.CodConcepto = '0001' THEN tnec.Cantidad ELSE 0 END) AS Cantidad,
								SUM(CASE WHEN c.Tipo = 'I' THEN tnec.Monto ELSE 0 END) AS SueldoNormal,
								SUM(CASE WHEN tnec.CodConcepto = '0046' THEN tnec.Monto ELSE 0 END) AS AliVac,
								SUM(CASE WHEN tnec.CodConcepto = '0047' THEN tnec.Monto ELSE 0 END) AS AliFin
							FROM
								pr_tiponominaempleadoconcepto tnec
								INNER JOIN pr_concepto c ON (c.CodConcepto = tnec.CodConcepto)
							WHERE
								tnec.CodTipoNom = '".$_ARGS['_NOMINA']."' AND
								tnec.Periodo = '".$_ARGS['_PERIODO']."' AND
								tnec.CodPersona = '".$_ARGS['_PERSONA']."' AND
								tnec.CodOrganismo = '".$_ARGS['_ORGANISMO']."' AND
								(tnec.CodTipoProceso = 'SE1' OR tnec.CodTipoProceso = 'SE2' OR tnec.CodTipoProceso = 'SE3' OR tnec.CodTipoProceso = 'SE4' OR tnec.CodTipoProceso = 'SE5')";
					$field_sueldos = getRecord($sql);
					$Sueldo = $field_sueldos['Sueldo'] / $field_sueldos['Cantidad'] * 30;
					$SueldoNormal = $field_sueldos['SueldoNormal'] / $field_sueldos['Cantidad'] * 30;
					$AliVac = $field_sueldos['AliVac'] / $field_sueldos['Cantidad'] * 30;
					$AliFin = $field_sueldos['AliFin'] / $field_sueldos['Cantidad'] * 30;
					$_SueldoIntegral = $SueldoNormal + ($AliVac * 30) + ($AliFin * 30);
					$_SueldoIntegralParcial = $SueldoNormal + ($AliVac * 30);
					//	inserto en sueldos
					$sql = "INSERT INTO rh_sueldos
							SET
								CodPersona = '".$_ARGS['_PERSONA']."',
								Periodo = '".$_ARGS['_PERIODO']."',
								Sueldo = '".$Sueldo."',
								SueldoNormal = '".$SueldoNormal."',
								SueldoIntegral = '".$_SueldoIntegral."',
								SueldoIntegralParcial = '".$_SueldoIntegralParcial."',
								AliVac = '".$AliVac."',
								AliFin = '".$AliFin."',
								UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
								UltimaFecha = NOW()
							ON DUPLICATE KEY UPDATE
								Sueldo = '".$Sueldo."',
								SueldoNormal = '".$SueldoNormal."',
								SueldoIntegral = '".$_SueldoIntegral."',
								SueldoIntegralParcial = '".$_SueldoIntegralParcial."',
								AliVac = '".$AliVac."',
								AliFin = '".$AliFin."',
								UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
								UltimaFecha = NOW()";
					execute($sql);
				}
				//	prestaciones sociales
				elseif ($_ARGS['_PROCESO'] == "PRS" && $PreNomina != "S") {
					if (getNumRows2("pr_liquidacionempleado", array("CodPersona","Periodo"), array($_ARGS['_PERSONA'],$_ARGS['_PERIODO']))) {
						$sql = "DELETE FROM pr_liquidacionempleado
								WHERE
									CodPersona = '".$_ARGS['_PERSONA']."' AND
									Periodo = '".$_ARGS['_PERIODO']."'";
						execute($sql);
					}
					##	consulto todos los adelantos
					$sql = "SELECT SUM(TotalNeto)
							FROM pr_tiponominaempleado
							WHERE
								CodPersona = '".$_ARGS['_PERSONA']."' AND
								CodTipoProceso = 'APR' AND
								EstadoPago = 'PA'
							GROUP BY CodPersona";
					$Adelantos = getVar3($sql);
					//	inserto en control de prestaciones
					$_SecuenciaLiquidacion = getCodigo("pr_liquidacionempleado", "Secuencia", 2, "CodPersona", $_ARGS['_PERSONA']);
					$sql = "INSERT INTO pr_liquidacionempleado
							SET
								CodPersona = '".$_ARGS['_PERSONA']."',
								Secuencia = '".$_SecuenciaLiquidacion."',
								CodTipoNom = '".$_ARGS['_NOMINA']."',
								Periodo = '".$_ARGS['_PERIODO']."',
								CodOrganismo = '".$_ARGS['_ORGANISMO']."',
								CodTipoProceso = '".$_ARGS['_PROCESO']."',
								ProcesadoPor = '".$_SESSION['CODPERSONA_ACTUAL']."',
								FechaProceso = NOW(),
								SueldoBasico = '".$_ARGS['_SUELDO_ACTUAL']."',
								TotalIngresos = '".$_ARGS['_ASIGNACIONES']."',
								TotalEgresos = '".$_ARGS['_DEDUCCIONES']."',
								TotalDescuento = '".$Adelantos."',
								TotalNeto = '".($_ARGS['_TOTAL_NETO'] - $Adelantos)."',
								MontoIntereses = '0.00',
								TotalPrestaciones = '".($_ARGS['_TOTAL_NETO'] - $Adelantos)."',
								TotalPatronales = '".$_ARGS['_APORTES']."',
								TotalProvisiones = '".$_ARGS['_PROVISIONES']."',
								Fingreso = '".$_ARGS['_FECHA_INGRESO']."',
								Fegreso = '".$_ARGS['_FECHA_EGRESO']."',
								CodCargo = '".$_ARGS['_CARGO']."',
								CodMotivoCes = '".$_ARGS['_MOTIVO_CESE']."',
								ObsCese = '".$field_empleado['ObsCese']."',
								EstadoPago = 'PE',
								UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
								UltimaFecha = NOW()";
					execute($sql);
					//	actualizo nomina
					$sql = "UPDATE pr_tiponominaempleado
							SET SecuenciaLiquidacion = '".$_SecuenciaLiquidacion."'
							WHERE
								CodTipoNom = '".$_ARGS['_NOMINA']."' AND
								Periodo = '".$_ARGS['_PERIODO']."' AND
								CodPersona = '".$_ARGS['_PERSONA']."' AND
								CodOrganismo = '".$_ARGS['_ORGANISMO']."' AND
								CodTipoProceso = '".$_ARGS['_PROCESO']."'";
					execute($sql);
				}
				//	retroactivo
				elseif ($_ARGS['_RETROACTIVO'] == "S") {
					##	obtengo periodo anterior
					$sql = "SELECT *
							FROM pr_tiponominaempleado
							WHERE
								CodOrganismo = '".$_ARGS['_ORGANISMO']."' AND
								CodPersona = '".$_ARGS['_PERSONA']."' AND
								CodTipoProceso = 'FIN' AND
								Periodo = '".$_ARGS['_PERIODONOMINA']."'";
					$field_anterior = getRecord($sql);
					##	
					$SueldoBasico = $field_anterior['SueldoBasico'] + $_C0001;
					$SueldoNormal = $field_anterior['TotalIngresos'] + $_ARGS['_SUELDO_NORMAL'];
					$SueldoDiario = round(($SueldoNormal / 30), 2);
					if ($_ARGS['_NOMINA'] == '05') {
						$AliVac = round(($SueldoDiario * $P_PAGOVACADC / 360), 2);
						$AliFin = round((($SueldoDiario + $AliVac) * $P_PAGOFINDC / 360), 2);
					} else {
						$AliVac = round(($SueldoDiario * $P_PAGOVACA / 360), 2);
						$AliFin = round((($SueldoDiario + $AliVac) * $P_PAGOFIN / 360), 2);
					}
					$SueldoIntegral = $SueldoNormal + ($AliVac * 30) + ($AliFin * 30);
					$SueldoIntegralParcial = $SueldoNormal + ($AliVac * 30);
					//	actualizo sueldo
					$sql = "UPDATE rh_sueldos
							SET
								Sueldo = '".$SueldoBasico."',
								SueldoNormal = '".$SueldoNormal."',
								SueldoIntegral = '".$SueldoIntegral."',
								SueldoIntegralParcial = '".$SueldoIntegralParcial."',
								AliVac = '".$AliVac."',
								AliFin = '".$AliFin."',
								UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
								UltimaFecha = NOW()
							WHERE
								CodPersona = '".$_ARGS['_PERSONA']."' AND
								Periodo = '".$_ARGS['_PERIODONOMINA']."'";
					execute($sql);
				}
			}
		}
		//	-----------------
		mysql_query("COMMIT");
	}
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		if ($detalles_personas != "") {
			$personas = split(";char:tr;", $detalles_personas);
			foreach ($personas as $CodPersona) {
				//	eliminar
				$sql = "DELETE FROM pr_tiponominaempleado
						WHERE
							CodOrganismo = '".$fCodOrganismo."' AND
							CodTipoNom = '".$fCodTipoNom."' AND
							Periodo = '".$fPeriodo."' AND
							CodTipoProceso = '".$fCodTipoProceso."' AND
							CodPersona = '".$CodPersona."'";
				execute($sql);
				//	eliminar
				$sql = "DELETE FROM pr_tiponominaempleadoconcepto
						WHERE
							CodOrganismo = '".$fCodOrganismo."' AND
							CodTipoNom = '".$fCodTipoNom."' AND
							Periodo = '".$fPeriodo."' AND
							CodTipoProceso = '".$fCodTipoProceso."' AND
							CodPersona = '".$CodPersona."'";
				execute($sql);
				//	liquidaciones
				if ($fCodTipoProceso == "PRS") {
					##	elimino liquidacion
					$sql = "DELETE FROM pr_liquidacionempleado
							WHERE
								CodPersona = '".$CodPersona."' AND
								EstadoPago = 'PE'";
					execute($sql);
				}
				elseif ($fCodTipoProceso == "APR") {
					##	consulto todos los adelantos
					$sql = "SELECT SUM(TotalNeto)
							FROM pr_tiponominaempleado
							WHERE
								CodOrganismo = '".$fCodOrganismo."' AND
								CodTipoNom = '".$fCodTipoNom."' AND
								Periodo = '".$fPeriodo."' AND
								CodTipoProceso = '".$fCodTipoProceso."' AND
								CodPersona = '".$CodPersona."' AND
								EstadoPago = 'PE'
							GROUP BY CodPersona";
					$TotalDescuento = getVar3($sql);
					##	actualizo
					$sql = "UPDATE pr_liquidacionempleado
							SET
								TotalDescuento = ".floatval($TotalDescuento).",
								UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
								UltimaFecha = NOW()
							WHERE
								CodPersona = '".$CodPersona."' AND
								EstadoPago = 'PE'";
					execute($sql);
					##	actualizo
					$sql = "UPDATE pr_liquidacionempleado
							SET
								TotalNeto = TotalIngresos - TotalEgresos - TotalDescuento,
								TotalPrestaciones = (TotalIngresos - TotalEgresos - TotalDescuento) + MontoIntereses
							WHERE
								CodPersona = '".$CodPersona."' AND
								EstadoPago = 'PE'";
					execute($sql);
				}
			}
		}
		//	-----------------
		mysql_query("COMMIT");
	}
}
//	fideicomiso
elseif ($modulo == "fideicomiso") {
	//	actualizar acumulado
	if ($accion == "acumulado") {
		mysql_query("BEGIN");
		//	-----------------
		//	inserto/actualizo
		$sql = "INSERT INTO pr_acumuladofideicomiso
				SET
					CodPersona = '".$CodPersona."',
					CodOrganismo = '".$CodOrganismo."',
					PeriodoInicial = '".$PeriodoInicial."',
					AcumuladoInicialDias = '".setNumero($AcumuladoInicialDias)."',
					AcumuladoDiasAdicionalInicial = '".setNumero($AcumuladoDiasAdicionalInicial)."',
					AcumuladoInicialProv = '".setNumero($AcumuladoInicialProv)."',
					AcumuladoInicialFide = '".setNumero($AcumuladoInicialFide)."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				ON DUPLICATE KEY UPDATE
					PeriodoInicial = '".$PeriodoInicial."',
					AcumuladoInicialDias = '".setNumero($AcumuladoInicialDias)."',
					AcumuladoDiasAdicionalInicial = '".setNumero($AcumuladoDiasAdicionalInicial)."',
					AcumuladoInicialProv = '".setNumero($AcumuladoInicialProv)."',
					AcumuladoInicialFide = '".setNumero($AcumuladoInicialFide)."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
//	tipo de proeso
elseif ($modulo == "tipo_proceso") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-----------------
		//	inserto
		$sql = "INSERT INTO pr_tipoproceso
				SET
					CodTipoProceso = '".$CodTipoProceso."',
					Descripcion = '".changeUrl($Descripcion)."',
					FlagAdelanto = '".$FlagAdelanto."',
					FlagRetroactivo = '".$FlagRetroactivo."',
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
		$sql = "UPDATE pr_tipoproceso
				SET
					Descripcion = '".changeUrl($Descripcion)."',
					FlagAdelanto = '".$FlagAdelanto."',
					FlagRetroactivo = '".$FlagRetroactivo."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodTipoProceso = '".$CodTipoProceso."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	eliminar registro
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		//	elimino
		$sql = "DELETE FROM pr_tipoproceso WHERE CodTipoProceso = '".$registro."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
//	conceptos asignacion
elseif ($modulo == "conceptos_asignacion") {
	mysql_query("BEGIN");
	//	-----------------
	$empleados = split(";char:tr;", $detalles_empleados);
	foreach ($empleados as $registro) {
		list($CodPersona, $PeriodoDesde, $PeriodoHasta, $FlagManual, $Monto, $Cantidad, $Procesos, $Estado) = split(";char:td;", $registro);
		##	inserto
		$sql = "INSERT INTO pr_empleadoconcepto
				SET
					CodPersona = '".$CodPersona."',
					CodConcepto = '".$CodConcepto."',
					TipoAplicacion = '".$TipoAplicacion."',
					PeriodoDesde = '".$PeriodoDesde."',
					PeriodoHasta = '".$PeriodoHasta."',
					FlagManual = '".$FlagManual."',
					Monto = '".setNumero($Monto)."',
					Cantidad = '".setNumero($Cantidad)."',
					FlagTipoProceso = '".$FlagTipoProceso."',
					Procesos = '".trim($Procesos)."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				ON DUPLICATE KEY UPDATE
					TipoAplicacion = '".$TipoAplicacion."',
					PeriodoDesde = '".$PeriodoDesde."',
					PeriodoHasta = '".$PeriodoHasta."',
					FlagManual = '".$FlagManual."',
					Monto = '".setNumero($Monto)."',
					Cantidad = '".setNumero($Cantidad)."',
					FlagTipoProceso = '".$FlagTipoProceso."',
					Procesos = '".trim($Procesos)."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
	}
	//	-----------------
	mysql_query("COMMIT");
}
//	ajuste salarial (grado salarial)
elseif ($modulo == "ajuste_salarial") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-----------------
		//	codigo
		$Secuencia = getCodigo("pr_ajustesalarial", "Secuencia", 2, "CodOrganismo", $CodOrganismo, "Periodo", $Periodo);
		$Secuencia = intval($Secuencia);
		//	inserto
		$sql = "INSERT INTO pr_ajustesalarial
				SET
					CodOrganismo = '".$CodOrganismo."',
					Periodo = '".$Periodo."',
					Secuencia = '".$Secuencia."',
					Descripcion = '".changeUrl($Descripcion)."',
					NroResolucion = '".changeUrl($NroResolucion)."',
					NroGaceta = '".changeUrl($NroGaceta)."',
					PreparadoPor = '".$PreparadoPor."',
					FechaPreparacion = '".formatFechaAMD($FechaPreparacion)."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		//	ajustes
		if ($detalles_ajustes != "") {
			$ajustes = split(";char:tr;", $detalles_ajustes);
			foreach ($ajustes as $ajuste) {
				list($_CodNivel, $_SueldoBasico, $_Porcentaje, $_Monto, $_SueldoNuevo) = split(";char:td;", $ajuste);
				//	inserto
				$sql = "INSERT INTO pr_ajustesalarialajustes
						SET
							CodOrganismo = '".$CodOrganismo."',
							Periodo = '".$Periodo."',
							Secuencia = '".$Secuencia."',
							CodNivel = '".$_CodNivel."',
							SueldoBasico = '".$_SueldoBasico."',
							Porcentaje = '".$_Porcentaje."',
							Monto = '".$_Monto."',
							SueldoPromedio = '".$_SueldoNuevo."',
							Estado = '".$Estado."',
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()";
				execute($sql);
			}
		}
		//	-----------------
		mysql_query("COMMIT");
	}
	//	modificar registro
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "UPDATE pr_ajustesalarial
				SET
					Descripcion = '".changeUrl($Descripcion)."',
					NroResolucion = '".changeUrl($NroResolucion)."',
					NroGaceta = '".changeUrl($NroGaceta)."',
					PreparadoPor = '".$PreparadoPor."',
					FechaPreparacion = '".formatFechaAMD($FechaPreparacion)."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					Periodo = '".$Periodo."' AND
					Secuencia = '".$Secuencia."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		//	ajustes
		if ($detalles_ajustes != "") {
			$sql = "DELETE FROM pr_ajustesalarialajustes
					WHERE
						CodOrganismo = '".$CodOrganismo."' AND
						Periodo = '".$Periodo."' AND
						Secuencia = '".$Secuencia."'";
			execute($sql);
			##
			$ajustes = split(";char:tr;", $detalles_ajustes);
			foreach ($ajustes as $ajuste) {
				list($_CodNivel, $_SueldoBasico, $_Porcentaje, $_Monto, $_SueldoNuevo) = split(";char:td;", $ajuste);
				//	inserto
				$sql = "INSERT INTO pr_ajustesalarialajustes
						SET
							CodOrganismo = '".$CodOrganismo."',
							Periodo = '".$Periodo."',
							Secuencia = '".$Secuencia."',
							CodNivel = '".$_CodNivel."',
							SueldoBasico = '".$_SueldoBasico."',
							Porcentaje = '".$_Porcentaje."',
							Monto = '".$_Monto."',
							SueldoPromedio = '".$_SueldoNuevo."',
							Estado = '".$Estado."',
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()";
				execute($sql);
			}
		}
		//	-----------------
		mysql_query("COMMIT");
	}
	//	aprobar
	elseif ($accion == "aprobar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "UPDATE pr_ajustesalarial
				SET
					Estado = 'AP',
					AprobadoPor = '".$AprobadoPor."',
					FechaAprobado = '".formatFechaAMD($FechaAprobado)."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					Periodo = '".$Periodo."' AND
					Secuencia = '".$Secuencia."'";
		execute($sql);
		//	actualizo
		$sql = "UPDATE pr_ajustesalarialajustes
				SET
					Estado = 'AP',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					Periodo = '".$Periodo."' AND
					Secuencia = '".$Secuencia."'";
		execute($sql);
		//	ajustes
		$ajustes = split(";char:tr;", $detalles_ajustes);
		foreach ($ajustes as $ajuste) {
			list($_CodNivel, $_SueldoBasico, $_Porcentaje, $_Monto, $_SueldoNuevo) = split(";char:td;", $ajuste);
			$_SueldoNuevo = floatval($_SueldoNuevo);
			##	
			$sql = "UPDATE rh_nivelsalarial
					SET
						SueldoMinimo = '".$_SueldoNuevo."',
						SueldoMaximo = '".$_SueldoNuevo."',
						SueldoPromedio = '".$_SueldoNuevo."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()
					WHERE CodNivel = '".$_CodNivel."'";
			execute($sql);
			##	
			$_Secuencia = getCodigo("rh_nivelsalarialajustes", "Secuencia", 6, "CodNivel", $_CodNivel);
			$_Secuencia = intval($_Secuencia);
			$sql = "INSERT INTO rh_nivelsalarialajustes
					SET
						CodNivel = '".$_CodNivel."',
						Secuencia = '".$_Secuencia."',
						SueldoPromedio = '".$_SueldoNuevo."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
			##	
			$sql = "SELECT * FROM rh_nivelsalarial WHERE CodNivel = '$_CodNivel'";
			$field_nivel = getRecord($sql);
			##	
			$sql = "UPDATE rh_puestos p
					SET p.NivelSalarial = $_SueldoNuevo
					WHERE
						CategoriaCargo = '$field_nivel[CategoriaCargo]' AND
						Grado = '$field_nivel[Grado]' AND
						Paso = '$field_nivel[Paso]'";
			execute($sql);
			##	
			$sql = "UPDATE mastempleado e
					SET
						e.SueldoAnterior = e.SueldoActual,
						e.SueldoActual = $_SueldoNuevo
					WHERE
						e.Paso = '$field_nivel[Paso]' AND
						e.CodCargo = (SELECT p.CodCargo
									  FROM rh_puestos p
									  WHERE
										p.CategoriaCargo = '$field_nivel[CategoriaCargo]' AND
										p.Grado = '$field_nivel[Grado]' AND
										p.CodCargo = e.CodCargo)";
			execute($sql);
		}
		//	-----------------
		mysql_query("COMMIT");
	}
	//	anular
	elseif ($accion == "anular") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "UPDATE pr_ajustesalarial
				SET
					Estado = 'AN',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					Periodo = '".$Periodo."' AND
					Secuencia = '".$Secuencia."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	actualizo
		$sql = "UPDATE pr_ajustesalarialajustes
				SET
					Estado = 'AN',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					Periodo = '".$Periodo."' AND
					Secuencia = '".$Secuencia."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		//	-----------------
		mysql_query("COMMIT");
	}
}
//	ajuste salarial (empleados)
elseif ($modulo == "ajuste_salarial_emp") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-----------------
		//	codigo
		$Secuencia = getCodigo("pr_ajustesalarialemp", "Secuencia", 2, "CodOrganismo", $CodOrganismo, "Periodo", $Periodo);
		$Secuencia = intval($Secuencia);
		
		//	inserto
		$sql = "INSERT INTO pr_ajustesalarialemp
				SET
					CodOrganismo = '".$CodOrganismo."',
					Periodo = '".$Periodo."',
					Secuencia = '".$Secuencia."',
					Descripcion = '".changeUrl($Descripcion)."',
					NroResolucion = '".changeUrl($NroResolucion)."',
					NroGaceta = '".changeUrl($NroGaceta)."',
					PreparadoPor = '".$PreparadoPor."',
					FechaPreparacion = '".formatFechaAMD($FechaPreparacion)."',
					CodTipoNom = '".$CodTipoNom."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		
		//	ajustes
		if ($detalles_ajustes != "") {
			$ajustes = split(";char:tr;", $detalles_ajustes);
			foreach ($ajustes as $ajuste) {
				list($_CodPersona, $_SueldoBasico, $_Porcentaje, $_Monto, $_SueldoNuevo) = split(";char:td;", $ajuste);
				//	inserto
				$sql = "INSERT INTO pr_ajustesalarialajustesemp
						SET
							CodOrganismo = '".$CodOrganismo."',
							Periodo = '".$Periodo."',
							Secuencia = '".$Secuencia."',
							CodPersona = '".$_CodPersona."',
							SueldoBasico = '".$_SueldoBasico."',
							Porcentaje = '".$_Porcentaje."',
							Monto = '".$_Monto."',
							SueldoPromedio = '".$_SueldoNuevo."',
							Estado = '".$Estado."',
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()";
				execute($sql);
			}
		}
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	modificar registro
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "UPDATE pr_ajustesalarialemp
				SET
					Descripcion = '".changeUrl($Descripcion)."',
					NroResolucion = '".changeUrl($NroResolucion)."',
					NroGaceta = '".changeUrl($NroGaceta)."',
					PreparadoPor = '".$PreparadoPor."',
					FechaPreparacion = '".formatFechaAMD($FechaPreparacion)."',
					CodTipoNom = '".$CodTipoNom."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					Periodo = '".$Periodo."' AND
					Secuencia = '".$Secuencia."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	ajustes
		if ($detalles_ajustes != "") {
			$sql = "DELETE FROM pr_ajustesalarialajustesemp
					WHERE
						CodOrganismo = '".$CodOrganismo."' AND
						Periodo = '".$Periodo."' AND
						Secuencia = '".$Secuencia."'";
			execute($sql);
			##
			$ajustes = split(";char:tr;", $detalles_ajustes);
			foreach ($ajustes as $ajuste) {
				list($_CodPersona, $_SueldoBasico, $_Porcentaje, $_Monto, $_SueldoNuevo) = split(";char:td;", $ajuste);
				//	inserto
				$sql = "INSERT INTO pr_ajustesalarialajustesemp
						SET
							CodOrganismo = '".$CodOrganismo."',
							Periodo = '".$Periodo."',
							Secuencia = '".$Secuencia."',
							CodPersona = '".$_CodPersona."',
							SueldoBasico = '".$_SueldoBasico."',
							Porcentaje = '".$_Porcentaje."',
							Monto = '".$_Monto."',
							SueldoPromedio = '".$_SueldoNuevo."',
							Estado = '".$Estado."',
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()";
				execute($sql);
			}
		}
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	aprobar
	elseif ($accion == "aprobar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "UPDATE pr_ajustesalarialemp
				SET
					Estado = 'AP',
					AprobadoPor = '".$AprobadoPor."',
					FechaAprobado = '".formatFechaAMD($FechaAprobado)."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					Periodo = '".$Periodo."' AND
					Secuencia = '".$Secuencia."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	actualizo
		$sql = "UPDATE pr_ajustesalarialajustesemp
				SET
					Estado = 'AP',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					Periodo = '".$Periodo."' AND
					Secuencia = '".$Secuencia."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	ajustes
		$ajustes = split(";char:tr;", $detalles_ajustes);
		foreach ($ajustes as $ajuste) {
			list($_CodPersona, $_SueldoBasico, $_Porcentaje, $_Monto, $_SueldoNuevo) = split(";char:td;", $ajuste);
			$_Estado = getVar2("mastempleado", "Estado", array("CodPersona"), array($_CodPersona));
			if ($_Estado == "I") $_SueldoJubilacion = ", MontoJubilacion = ".floatval($_SueldoNuevo)."";
			else $_SueldoJubilacion = "";
			//	actualizo
			$sql = "UPDATE mastempleado
					SET
						SueldoAnterior = SueldoActual,
						SueldoActual = ".floatval($_SueldoNuevo)."
						$_SueldoJubilacion
					WHERE CodPersona = '".$_CodPersona."'";
			$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		}
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	anular
	elseif ($accion == "anular") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "UPDATE pr_ajustesalarialemp
				SET
					Estado = 'AN',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					Periodo = '".$Periodo."' AND
					Secuencia = '".$Secuencia."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	actualizo
		$sql = "UPDATE pr_ajustesalarialajustesemp
				SET
					Estado = 'AN',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					Periodo = '".$Periodo."' AND
					Secuencia = '".$Secuencia."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		//	-----------------
		mysql_query("COMMIT");
	}
}
//	control de procesos (prenomina)
elseif ($modulo == "prenomina_procesos") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-----------------
		//	inserto
		$sql = "INSERT INTO pr_procesoperiodoprenomina
				SET
					CodOrganismo = '".$CodOrganismo."',
					CodTipoNom = '".$CodTipoNom."',
					Periodo = '".$Periodo."',
					CodTipoProceso = '".$CodTipoProceso."',
					FechaDesde = '".formatFechaAMD($FechaDesde)."',
					FechaHasta = '".formatFechaAMD($FechaHasta)."',
					CreadoPor = '".$CreadoPor."',
					FechaCreado = '".formatFechaAMD($FechaCreado)."',
					FlagProcesado = 'N',
					FlagMensual = '".$FlagMensual."',
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
		$sql = "UPDATE pr_procesoperiodo
				SET
					FechaDesde = '".formatFechaAMD($FechaDesde)."',
					FechaHasta = '".formatFechaAMD($FechaHasta)."',
					FlagMensual = '".$FlagMensual."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					CodTipoNom = '".$CodTipoNom."' AND
					Periodo = '".$Periodo."' AND
					CodTipoProceso = '".$CodTipoProceso."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		//	-----------------
		mysql_query("COMMIT");
	}
}
//	control de liquidaciones
elseif ($modulo == "prestaciones_control") {
	//	nuevo
	if ($accion == "intereses") {
		mysql_query("BEGIN");
		//	-----------------
		$MontoIntereses = 0;
		//	intereses
		$intereses = explode(";", $detalles_intereses);
		foreach ($intereses as $interes) {
			list($Periodo, $Porcentaje, $MontoBase, $Monto) = explode("|", $interes);
			##	inserto
			$sql = "INSERT INTO pr_liquidacionempleadointereses
					SET
						CodPersona = '".$CodPersona."',
						Secuencia = '".$Secuencia."',
						Periodo = '".$Periodo."',
						Porcentaje = '".$Porcentaje."',
						MontoBase = '".$MontoBase."',
						Monto = '".$Monto."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()
					ON DUPLICATE KEY UPDATE
						Porcentaje = '".$Porcentaje."',
						MontoBase = '".$MontoBase."',
						Monto = '".$Monto."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
			##	
			$MontoIntereses += $Monto;
		}
		##	actualizo prestacion
		$sql = "UPDATE pr_liquidacionempleado
				SET
					MontoIntereses = ".floatval($MontoIntereses).",
					TotalPrestaciones = TotalNeto + ".floatval($MontoIntereses)."
				WHERE
					CodPersona = '".$CodPersona."' AND
					Secuencia = '".$Secuencia."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
	//	actualizacion masiva
	elseif ($accion == "intereses-actualizacion-masiva") {
		mysql_query("BEGIN");
		//	-----------------
		list($AnioActual, $MesActual, $DiaActual) = split("[/.-]", substr($Ahora, 0, 10));
		$FechaActual = "$AnioActual-$MesActual-$DiaActual";
		$PeriodoActual = "$AnioActual-$MesActual";
		$CodTipoProceso = "PRS";
		$CodConcepto = "0110";
		##	consulto los empleados
		$sql = "SELECT
					le.*,
					e.CodEmpleado,
					e.Fegreso,
					p.NomCompleto AS NomPersona,
					p.Ndocumento
				FROM
					pr_liquidacionempleado le
					INNER JOIN mastpersonas p ON (p.CodPersona = le.CodPersona)
					INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
				WHERE le.EstadoPago = 'PE'";
		$field = getRecords($sql);
		foreach($field as $f) {
			list($AnioEgreso, $MesEgreso, $DiaEgreso) = explode("-", $f['Fegreso']);
			$AnioInicial = intval($AnioEgreso);
			$MesInicial = intval($MesEgreso) + 1;
			if ($MesInicial > 12) {
				++$AnioInicial;
				$MesInicial = "01";
			}
			elseif ($MesInicial < 10) $MesInicial = "0".$MesInicial;
			$PeriodoInicial = $AnioInicial."-".$MesInicial;
			##	
			$Actualizar = false;
			$MontoIntereses = 0;
			$sql = "SELECT
						ti.Periodo,
						ti.Porcentaje,
						tnec.Periodo AS PeriodoNomina,
						tnec.Cantidad,
						tnec.Monto
					FROM
						masttasainteres ti
						LEFT JOIN pr_tiponominaempleadoconcepto tnec ON (tnec.Periodo = ti.Periodo AND
																		 tnec.CodTipoNom = '".$f['CodTipoNom']."' AND
																		 tnec.CodPersona = '".$f['CodPersona']."' AND
																		 tnec.CodOrganismo = '".$f['CodOrganismo']."' AND
																		 tnec.CodTipoProceso = '".$CodTipoProceso."' AND
																		 tnec.CodConcepto = '".$CodConcepto."')
					WHERE ti.Periodo >= '".$PeriodoInicial."'
					ORDER BY Periodo DESC";
			$field_periodos = getRecords($sql);
			foreach($field_periodos as $fp) {
				list($AnioPeriodo, $MesPeriodo) = explode("-", $fp['Periodo']);
				$DiasAnio = getFechaDias("01-01-$AnioPeriodo", "31-12-$AnioPeriodo");
				$Monto = round(($f['TotalNeto'] * $fp['Porcentaje'] / 100 * 30 / $DiasAnio), 2);
				if ($fp['PeriodoNomina'] == "" || $fp['Porcentaje'] != $fp['Cantidad']) {
					##	actualizo total payrroll
					$sql = "INSERT INTO pr_tiponominaempleado
							SET
								CodTipoNom = '".$f['CodTipoNom']."',
								Periodo = '".$fp['Periodo']."',
								CodPersona = '".$f['CodPersona']."',
								CodOrganismo = '".$f['CodOrganismo']."',
								CodTipoProceso = '".$f['CodTipoProceso']."',
								SueldoBasico = '".$f['SueldoBasico']."',
								TotalIngresos = '".$Monto."',
								TotalNeto = '".$Monto."',
								GeneradoPor = '".$_SESSION["CODPERSONA_ACTUAL"]."',
								FechaGeneracion = NOW(),
								EstadoPago = 'PE',
								UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
								UltimaFecha = NOW()
							ON DUPLICATE KEY UPDATE
								SueldoBasico = '".$f['SueldoBasico']."',
								TotalIngresos = '".$Monto."',
								TotalNeto = '".$Monto."',
								GeneradoPor = '".$_SESSION["CODPERSONA_ACTUAL"]."',
								FechaGeneracion = NOW(),
								EstadoPago = 'PE',
								UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
								UltimaFecha = NOW()";
					execute($sql);
					##	actualizo concepto payrroll
					$sql = "INSERT INTO pr_tiponominaempleadoconcepto
							SET
								CodTipoNom = '".$f['CodTipoNom']."',
								Periodo = '".$fp['Periodo']."',
								CodPersona = '".$f['CodPersona']."',
								CodOrganismo = '".$f['CodOrganismo']."',
								CodTipoProceso = '".$f['CodTipoProceso']."',
								CodConcepto = '".$CodConcepto."',
								Monto = '".$Monto."',
								Cantidad = '".$fp['Porcentaje']."',
								UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
								UltimaFecha = NOW()
							ON DUPLICATE KEY UPDATE
								Monto = '".$Monto."',
								Cantidad = '".$fp['Porcentaje']."',
								UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
								UltimaFecha = NOW()";
					execute($sql);
					##	inserto
					$sql = "INSERT INTO pr_liquidacionempleadointereses
							SET
								CodPersona = '".$f['CodPersona']."',
								Secuencia = '".$f['Secuencia']."',
								Periodo = '".$fp['Periodo']."',
								Porcentaje = '".$fp['Porcentaje']."',
								MontoBase = '".$f['TotalNeto']."',
								Monto = '".$Monto."',
								UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
								UltimaFecha = NOW()
							ON DUPLICATE KEY UPDATE
								Porcentaje = '".$fp['Porcentaje']."',
								MontoBase = '".$f['TotalNeto']."',
								Monto = '".$Monto."',
								UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
								UltimaFecha = NOW()";
					execute($sql);
					##	
					$MontoIntereses += $Monto;
					$Actualizar = true;
				}
			}
			if ($Actualizar) {
				##	actualizo prestacion
				$sql = "UPDATE pr_liquidacionempleado
						SET
							MontoIntereses = ".floatval($MontoIntereses).",
							TotalPrestaciones = TotalNeto + ".floatval($MontoIntereses)."
						WHERE
							CodPersona = '".$f['CodPersona']."' AND
							Secuencia = '".$f['Secuencia']."'";
				execute($sql);
			}
		}
		//	-----------------
		mysql_query("COMMIT");
	}
}
?>