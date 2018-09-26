<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion.sql", "w+");
##############################################################################/
##############################################################################/
if ($modulo == "formulario") {
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
					CodPresupuesto = '".$CodPresupuesto."',
					cod_partida = '".$cod_partida."',
					CodFuente = '".$CodFuente."',
					CodTipoDocumento = '".$CodTipoDocumento."',
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
		##	eventos
		$sql = "SELECT * FROM rh_bonoalimentacion WHERE Anio = '".$Anio."' AND CodOrganismo = '".$CodOrganismo."' AND CodBonoAlim = '".$CodBonoAlim."'";
		$field_bono = getRecord($sql);
		##	eventos
		$sql = "SELECT * FROM rh_bonoalimentacioneventos WHERE Anio = '".$Anio."' AND CodOrganismo = '".$CodOrganismo."' AND CodBonoAlim = '".$CodBonoAlim."'";
		$field_evento = getRecords($sql);
		if (count($field_evento) && (formatFechaAMD($FechaInicio) != $field_bono['FechaInicio'] || formatFechaAMD($FechaFin) != $field_bono['FechaFin'])) die("No puede modificar este periodo porque ya tiene eventos ingresados.");
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
					CodPresupuesto = '".$CodPresupuesto."',
					cod_partida = '".$cod_partida."',
					CodFuente = '".$CodFuente."',
					CodTipoDocumento = '".$CodTipoDocumento."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					CodBonoAlim = '".$CodBonoAlim."'";
		execute($sql);
		//	empleados
		//if (!count($field_evento)) {
			$sql = "DELETE FROM rh_bonoalimentaciondet
					WHERE
						Anio = '".$Anio."' AND
						CodOrganismo = '".$CodOrganismo."' AND
						CodBonoAlim = '".$CodBonoAlim."'";
			execute($sql);
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
		//}
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
	//	procesar
	elseif ($accion == "procesar") {
		mysql_query("BEGIN");
		##	-----------------
		$sql = "SELECT *
				FROM rh_bonoalimentacion
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					CodBonoAlim = '".$CodBonoAlim."'";
		$field_bono = getRecord($sql);
		//	elimino eventos
		$sql = "DELETE FROM rh_bonoalimentacioneventos
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					CodBonoAlim = '".$CodBonoAlim."' AND
					CodPersona = '".$CodPersona."'";
		execute($sql);
		//	actualizo el detalle
		$_DiasPago = $field_bono['TotalDiasPago'];
		$_DiasFeriados = 0;
		$_DiasInactivos = 0;
		$fi = formatFechaAMD($FechaInicio);
		for($i=1;$i<=$field_bono['TotalDiasPeriodo'];$i++) {
			list($d, $m, $a) = explode('-', $fi);
			$DiaFeriado = $m.'-'.$d;
			$sql = "SELECT *
					FROM rh_feriados
					WHERE
						(AnioFeriado <> '' AND CONCAT(AnioFeriado, '-', DiaFeriado) = '".formatFechaAMD($fi)."') OR
						(AnioFeriado = '' AND DiaFeriado = '".$DiaFeriado."')";
			$field_feriado = getRecord($sql);
			##	
			$DiaSemana = getWeekDay($fi);
			$sql = "SELECT *
					FROM
						mastempleado e
						INNER JOIN rh_horariolaboraldet hld ON (hld.CodHorario = e.CodHorario AND 
																hld.Dia = '".$DiaSemana."' AND 
																hld.FlagLaborable = 'S')
					WHERE e.CodPersona = '".$CodPersona."'";
			$field_horario = getRecord($sql);
			##	
			if (count($field_horario) && !count($field_feriado)) { $Dia = 'X'; }
			elseif (count($field_horario) && count($field_feriado)) { $Dia = 'F'; $_DiasFeriados++; }
			else { $Dia = 'I'; $_DiasInactivos++; }
			$sql = "UPDATE rh_bonoalimentaciondet
					SET Dia".$i." = '".$Dia."'
					WHERE
						Anio = '".$Anio."' AND
						CodOrganismo = '".$CodOrganismo."' AND
						CodBonoAlim = '".$CodBonoAlim."' AND
						CodPersona = '".$CodPersona."'";
			execute($sql);
			##	
			$fi = obtenerFechaFin($fi, 2);
		}
		//	eventos
		$_Secuencia = 0;
		list($_HorasPar, $_MinutosPar) = split("[:]", $_PARAMETRO['UTDESC']);
		$eventos = split(";char:tr;", $detalles_eventos);
		foreach ($eventos as $linea) {
			list($_Tipo, $_Fecha, $_HoraSalida, $_HoraEntrada, $_TotalHoras, $_Motivo, $_TipoEvento, $_Observaciones) = split(";char:td;", $linea);
			if ($_EventoHoras[$_Fecha] != "") {
				$_EventoHoras[$_Fecha] = sumarHoras($_EventoHoras[$_Fecha], $_TotalHoras);
			} else {
				$_EventoHoras[$_Fecha] = $_TotalHoras;
			}
			$_EventoFecha[$_Fecha] = $_Fecha;
			$_EventoTipo[$_Fecha] = $_Tipo;
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
						Tipo = '".$_Tipo."',
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
			execute($sql);
		}
		//	sumo los descuentos
		$_DiasDescuento = 0;
		foreach ($_EventoFecha as $_Fecha) {
			list($_Horas, $_Minutos) = split("[:]", $_EventoHoras[$_Fecha]);
			if (($_Horas > $_HorasPar) || ($_Horas == $_HorasPar && $_Minutos >= $_MinutosPar) || $_EventoTipo[$_Fecha] == 'I') {
				if ($_EventoTipo[$_Fecha] == 'D') $_DiasDescuento++;
				else { $_DiasPago++; $_DiasFeriados--; }
				$_Dia = getFechaDias(formatFechaDMA($FechaInicio), formatFechaDMA($_Fecha)) + 1;
				//	actualizo detalle del bono alimenticio
				$sql = "UPDATE rh_bonoalimentaciondet
						SET Dia".$_Dia." = '".($_EventoTipo[$_Fecha]=='D'?'D':'A')."'
						WHERE
							Anio = '".$Anio."' AND
							CodOrganismo = '".$CodOrganismo."' AND
							CodBonoAlim = '".$CodBonoAlim."' AND
							CodPersona = '".$CodPersona."'";
				execute($sql);
			}
			elseif ($_Fecha && $_EventoTipo[$_Fecha] == 'D') {
				die("El tiempo total para el dia <strong>".formatFechaDMA($_Fecha)."</strong> es <strong>($_EventoHoras[$_Fecha])</strong> y no puede ser menor a <strong>$_PARAMETRO[UTDESC]</strong>");
			}
		}
		//	actualizo los descuentos
		//$_DiasPago = $field_bono['TotalDiasPeriodo'] - ($_DiasFeriados + $_DiasDescuento);
		$_DiasPago = $field_bono['TotalDiasPeriodo'] - ($_DiasDescuento);
		$_ValorPagar = $field_bono['ValorMes'];
		$_ValorDescuento = $field_bono['ValorDia'] * $_DiasDescuento;
		$_TotalPagar = $_ValorPagar - $_ValorDescuento;
		$sql = "UPDATE rh_bonoalimentaciondet
				SET
					DiasPago = '".$_DiasPago."',
					DiasFeriados = '".$_DiasFeriados."',
					DiasInactivos = '".$_DiasInactivos."',
					DiasDescuento = '".$_DiasDescuento."',
					ValorPagar = '".$_ValorPagar."',
					ValorDescuento = '".$_ValorDescuento."',
					TotalPagar = '".$_TotalPagar."'
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					CodBonoAlim = '".$CodBonoAlim."' AND
					CodPersona = '".$CodPersona."'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
}
elseif ($modulo == "ajax") {
	//	
	if ($accion == "getFechaEventos") {
		echo '<option value="">&nbsp;</option>';
		getFechaEventos($FechaInicio, $FechaFin, '', 0, $CodPersona, $Tipo);
	}
	//	
	elseif ($accion == "getHoraEventos") {
		$DiaSemana = getWeekDay($Fecha);
		$sql = "SELECT *
				FROM rh_horariolaboraldet
				WHERE
					CodHorario = '".$CodHorario."' AND
					Dia = '".$DiaSemana."'";
		$field = getRecord($sql);
		if ($field['Salida2'] != '00:00:00') $Salida = $field['Salida2']; else $Salida = $field['Salida1'];
		$Total = getDiffHoraEventos($CodHorario, $Fecha, $field['Entrada1'], $Salida);
		echo formatHora12($field['Entrada1'])."|".formatHora12($Salida)."|".$Total;
	}
	//	
	elseif ($accion == "bono_periodos_registrar_eventos_insertar") {
		?>
	    <tr class="trListaBody" onclick="mClk(this, 'sel_eventos');" id="eventos_<?=$secuencia?>_<?=$nrodetalle?>">
	    	<th>
				<?=$nrodetalle?>
	        </th>
	        <td>
	            <select name="Tipo" id="Tipo_<?=$secuencia?>_<?=$nrodetalle?>" class="cell" <?=$disabled?> onchange="getFechaEventos(this.value, '<?=$FechaInicio?>', '<?=$FechaFin?>', '<?=$CodPersona?>', '<?=$secuencia?>', '<?=$nrodetalle?>');">
	                <?=loadSelectValores('tipo-bono','D')?>
	            </select>
	        </td>
	        <td>
	            <select name="Fecha" id="Fecha_<?=$secuencia?>_<?=$nrodetalle?>" class="cell" onChange="getHoraEventos('<?=$secuencia?>_<?=$nrodetalle?>');">
	                <option value="">&nbsp;</option>
	                <?=getFechaEventos($FechaInicio, $FechaFin, "", 0, $CodPersona)?>
	            </select>
	        </td>
	        <td>
	            <input type="text" name="HoraSalida" id="HoraSalida_<?=$secuencia?>_<?=$nrodetalle?>" class="cell time" style="text-align:center;" maxlength="11" onChange="getDiffHoraEventos('<?=$secuencia?>_<?=$nrodetalle?>');" />
	        </td>
	        <td>
	            <input type="text" name="HoraEntrada" id="HoraEntrada_<?=$secuencia?>_<?=$nrodetalle?>" class="cell time" style="text-align:center;" maxlength="11" onChange="getDiffHoraEventos('<?=$secuencia?>_<?=$nrodetalle?>');" />
	        </td>
	        <td>
	            <input type="text" name="TotalHoras" id="TotalHoras_<?=$secuencia?>_<?=$nrodetalle?>" class="cell2" style="text-align:center;" value="0:0" readonly />
	        </td>
	        <td>
	            <select name="Motivo" class="cell">
	                <option value="">&nbsp;</option>
	                <?=getMiscelaneos('', "PERMISOS", 0)?>
	            </select>
	        </td>
	        <td>
	            <select name="TipoEvento" class="cell">
	                <option value="">&nbsp;</option>
	                <?=getMiscelaneos('', "TIPOFALTAS", 0)?>
	            </select>
	        </td>
	        <td>
	            <textarea name="Observaciones" class="cell" style="height:15px;"></textarea>
	        </td>
	    </tr>
	    <?php
	}
	//	
	elseif ($accion == "bono_periodos_registrar_eventos_control") {
		list($CodEvento, $CodPersona) = split("[_]", $registro);
		//	consulto el empleado
		$sql = "SELECT CodHorario FROM mastempleado WHERE CodPersona = '".$CodPersona."'";
		$query_empleado = mysql_query($sql) or die ($sql.mysql_error());
		if (mysql_num_rows($query_empleado) != 0) $field_empleado = mysql_fetch_array($query_empleado);
		
		//	consulto el evento
		$sql = "SELECT *
				FROM rh_controlasistencia
				WHERE
					CodPersona = '".$CodPersona."' AND
					CodEvento = '".$CodEvento."'";
		$query_evento = mysql_query($sql) or die ($sql.mysql_error());
		while ($field_evento = mysql_fetch_array($query_evento)) {
			if (trim($field_evento['Event_Puerta']) == "Interior") {
				$HoraEntrada = formatHora12($field_evento['HoraFormat'], true);
				$Hasta = $field_evento['HoraFormat'];
				$HoraSalida = "";
				$Desde = "";
			} else {
				$HoraSalida = formatHora12($field_evento['HoraFormat'], true);
				$Desde = $field_evento['HoraFormat'];
				##	consulto para ingresar la hora de entrada si tiene
				$sql = "SELECT *
						FROM rh_controlasistencia
						WHERE
							CodPersona = '".$CodPersona."' AND
							FechaFormat = '".$field_evento['FechaFormat']."' AND
							HoraFormat > '".$field_evento['HoraFormat']."' AND
							Event_Puerta = 'Interior'
						ORDER BY HoraFormat
						LIMIT 0, 1";
				$query_entrada = mysql_query($sql) or die ($sql.mysql_error());
				if (mysql_num_rows($query_entrada) != 0) {
					$field_entrada = mysql_fetch_array($query_entrada);
					$HoraEntrada = formatHora12($field_entrada['HoraFormat'], true);
					$Hasta = $field_entrada['HoraFormat'];
				} else {
					$HoraEntrada = "";
					$Hasta = "";
				}
			}
			$TotalHoras = getDiffHoraEventos($field_empleado['CodHorario'], formatFechaDMA($field_evento['FechaFormat']), $Desde, $Hasta);
			?>
			<tr class="trListaBody" onclick="mClk(this, 'sel_eventos');" id="eventos_<?=$secuencia?>_<?=$nrodetalle?>">
				<th>
					<?=$nrodetalle?>
				</th>
		        <td>
		            <select name="Tipo" id="Tipo_<?=$secuencia?>_<?=$nrodetalle?>" class="cell" <?=$disabled?> onchange="getFechaEventos(this.value, '<?=$FechaInicio?>', '<?=$FechaFin?>', '<?=$CodPersona?>', '<?=$secuencia?>', '<?=$nrodetalle?>');">
		                <?=loadSelectValores('tipo-bono','D')?>
		            </select>
		        </td>
				<td>
					<select name="Fecha" id="Fecha_<?=$nrodetalle?>" class="cell" onChange="getDiffHoraEventos('<?=$nrodetalle?>');">
						<option value="">&nbsp;</option>
						<?=getFechaEventos($FechaInicio, $FechaFin, formatFechaDMA($field_evento['FechaFormat']), 0)?>
					</select>
				</td>
				<td>
					<input type="text" name="HoraSalida" id="HoraSalida_<?=$nrodetalle?>" class="cell" style="text-align:center;" value="<?=$HoraSalida?>" maxlength="11" onChange="getDiffHoraEventos('<?=$nrodetalle?>');" />
				</td>
				<td>
					<input type="text" name="HoraEntrada" id="HoraEntrada_<?=$nrodetalle?>" class="cell" style="text-align:center;" value="<?=$HoraEntrada?>" maxlength="11" onChange="getDiffHoraEventos('<?=$nrodetalle?>');" />
				</td>
				<td>
					<input type="text" name="TotalHoras" id="TotalHoras_<?=$nrodetalle?>" class="cell2" style="text-align:center;" value="<?=$TotalHoras?>" readonly />
				</td>
				<td>
					<select name="TipoEvento" class="cell">
						<option value="">&nbsp;</option>
						<?=getMiscelaneos('', "PERMISOS", 0)?>
					</select>
				</td>
				<td>
					<select name="Motivo" class="cell">
						<option value="">&nbsp;</option>
						<?=getMiscelaneos('', "TIPOFALTAS", 0)?>
					</select>
				</td>
				<td>
					<textarea name="Observaciones" class="cell" style="height:15px;"></textarea>
				</td>
			</tr>
			<?php
		}
	}
	//	
	elseif ($accion == "getDiffHoraEventos") {
		echo getDiffHoraEventos($CodHorario, $Fecha, $Desde, $Hasta);
	}
}
?>