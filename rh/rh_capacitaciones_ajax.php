<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion-".$AnioActual.$MesActual.$DiaActual."_".$HorasActual.$MinutosActual.$SegundosActual.".sql", "w+");
##############################################################################/
##	Postulantes (NUEVO, MODIFICAR, ELIMINAR, AJAX)
##############################################################################/
if ($modulo == "formulario") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		##	-----------------
		##	valido
		if (!trim($CodOrganismo) || !trim($Solicitante) || !trim($CodCurso) || !trim($CodCentroEstudio) || !trim($TipoCurso) || !trim($Modalidad) || !trim($TipoCapacitacion) || !trim($CodCiudad) || !trim($FechaDesde) || !trim($Vacantes)) die("Debe llenar los campos (*) obligatorios.");
		elseif (setNumero($Participantes) > setNumero($Vacantes)) die("El n&uacute;mero de Participantes no puede ser mayor a las Vacantes disponibles");
		##	codigo
		$Capacitacion = codigo('rh_capacitacion','Capacitacion',6,['Anio','CodOrganismo'],[$Anio,$CodOrganismo]);
		##	inserto
		$sql = "INSERT INTO rh_capacitacion
				SET
					Anio = '".$Anio."',
					CodOrganismo = '".$CodOrganismo."',
					Capacitacion = '".$Capacitacion."',
					CodCurso = '".$CodCurso."',
					CodCentroEstudio = '".$CodCentroEstudio."',
					Vacantes = '".setNumero($Vacantes)."',
					FechaDesde = '".formatFechaAMD($FechaDesde)."',
					FechaHasta = '".formatFechaAMD($FechaHasta)."',
					Participantes = '".count($participantes_CodPersona)."',
					Aula = '".$Aula."',
					TipoCapacitacion = '".$TipoCapacitacion."',
					Expositor = '".$Expositor."',
					CodCiudad = '".$CodCiudad."',
					Solicitante = '".$Solicitante."',
					TelefonoContacto = '".$TelefonoContacto."',
					CostoEstimado = '".setNumero($CostoEstimado)."',
					MontoAsumido = '".setNumero($MontoAsumido)."',
					Modalidad = '".$Modalidad."',
					TipoCurso = '".$TipoCurso."',
					FlagCostos = '".(($FlagCostos)?'S':'N')."',
					Periodo = '".$Periodo."',
					Observaciones = '".$Observaciones."',
					Fundamentacion1 = '".$Fundamentacion1."',
					Fundamentacion2 = '".$Fundamentacion2."',
					Fundamentacion3 = '".$Fundamentacion3."',
					Fundamentacion4 = '".$Fundamentacion4."',
					Fundamentacion5 = '".$Fundamentacion5."',
					Fundamentacion6 = '".$Fundamentacion6."',
					Fundamentacion7 = '".$Fundamentacion7."',
					Estado = '".$Estado."',
					CreadoPor = '".$_SESSION["CODPERSONA_ACTUAL"]."',
					FechaCreado = NOW(),
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		##	participantes
		for ($i=0; $i < count($participantes_CodPersona); $i++) {
			$sql = "INSERT INTO rh_capacitacion_empleados
					SET
						Anio = '".$Anio."',
						CodOrganismo = '".$CodOrganismo."',
						Capacitacion = '".$Capacitacion."',
						CodPersona = '".$participantes_CodPersona[$i]."',
						CodDependencia = '".$participantes_CodDependencia[$i]."',
						NroAsistencias = '".$participantes_NroAsistencias[$i]."',
						HoraAsistencias = '".$participantes_HoraAsistencias[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	horario
		for ($i=0; $i < count($hora_Estado); $i++) {
			##	empleados
			for ($j=0; $j < count($participantes_CodPersona); $j++) {
				##	inserto hora
				$sql = "INSERT INTO rh_capacitacion_hora
						SET
							Anio = '".$Anio."',
							CodOrganismo = '".$CodOrganismo."',
							Capacitacion = '".$Capacitacion."',
							CodPersona = '".$participantes_CodPersona[$j]."',
							Secuencia = '".($i+1)."',
							Estado = '".$hora_Estado[$i]."',
							PeriodoInicio = '".formatFechaAMD($hora_FechaDesde[$i])."',
							PeriodoFin = '".formatFechaAMD($hora_FechaHasta[$i])."',
							FechaDesde = '".formatFechaAMD($hora_FechaDesde[$i])."',
							FechaHasta = '".formatFechaAMD($hora_FechaHasta[$i])."',
							Lunes = '".($hora_Lunes[$i]?'S':'N')."',
							HoraInicioLunes = '".formatHora24($hora_HoraInicioLunes[$i])."',
							HoraFinLunes = '".formatHora24($hora_HoraFinLunes[$i])."',
							Martes = '".($hora_Martes[$i]?'S':'N')."',
							HoraInicioMartes = '".formatHora24($hora_HoraInicioMartes[$i])."',
							HoraFinMartes = '".formatHora24($hora_HoraFinMartes[$i])."',
							Miercoles = '".($hora_Miercoles[$i]?'S':'N')."',
							HoraInicioMiercoles = '".formatHora24($hora_HoraInicioMiercoles[$i])."',
							HoraFinMiercoles = '".formatHora24($hora_HoraFinMiercoles[$i])."',
							Jueves = '".($hora_Jueves[$i]?'S':'N')."',
							HoraInicioJueves = '".formatHora24($hora_HoraInicioJueves[$i])."',
							HoraFinJueves = '".formatHora24($hora_HoraFinJueves[$i])."',
							Viernes = '".($hora_Viernes[$i]?'S':'N')."',
							HoraInicioViernes = '".formatHora24($hora_HoraInicioViernes[$i])."',
							HoraFinViernes = '".formatHora24($hora_HoraFinViernes[$i])."',
							Sabado = '".($hora_Sabado[$i]?'S':'N')."',
							HoraInicioSabado = '".formatHora24($hora_HoraInicioSabado[$i])."',
							HoraFinSabado = '".formatHora24($hora_HoraFinSabado[$i])."',
							Domingo = '".($hora_Domingo[$i]?'S':'N')."',
							HoraInicioDomingo = '".formatHora24($hora_HoraInicioDomingo[$i])."',
							HoraFinDomingo = '".formatHora24($hora_HoraFinDomingo[$i])."',
							TotalDias = '".$hora_TotalDias[$i]."',
							TotalHoras = '".$hora_TotalHoras[$i]."',
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()";
				execute($sql);
			}
		}
		##	-----------------
		mysql_query("COMMIT");
	}
	//	modificar
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		##	-----------------
		##	valido
		if (!trim($CodOrganismo) || !trim($Solicitante) || !trim($CodCurso) || !trim($CodCentroEstudio) || !trim($TipoCurso) || !trim($Modalidad) || !trim($TipoCapacitacion) || !trim($CodCiudad) || !trim($FechaDesde) || !trim($Vacantes)) die("Debe llenar los campos (*) obligatorios.");
		elseif (setNumero($Participantes) > setNumero($Vacantes)) die("El n&uacute;mero de Participantes no puede ser mayor a las Vacantes disponibles");
		##	actualizo
		$sql = "UPDATE rh_capacitacion
				SET
					CodCurso = '".$CodCurso."',
					CodCentroEstudio = '".$CodCentroEstudio."',
					Vacantes = '".setNumero($Vacantes)."',
					FechaDesde = '".formatFechaAMD($FechaDesde)."',
					FechaHasta = '".formatFechaAMD($FechaHasta)."',
					Participantes = '".count($participantes_CodPersona)."',
					Aula = '".$Aula."',
					TipoCapacitacion = '".$TipoCapacitacion."',
					Expositor = '".$Expositor."',
					CodCiudad = '".$CodCiudad."',
					Solicitante = '".$Solicitante."',
					TelefonoContacto = '".$TelefonoContacto."',
					CostoEstimado = '".setNumero($CostoEstimado)."',
					MontoAsumido = '".setNumero($MontoAsumido)."',
					Modalidad = '".$Modalidad."',
					TipoCurso = '".$TipoCurso."',
					FlagCostos = '".(($FlagCostos)?'S':'N')."',
					Observaciones = '".$Observaciones."',
					Fundamentacion1 = '".$Fundamentacion1."',
					Fundamentacion2 = '".$Fundamentacion2."',
					Fundamentacion3 = '".$Fundamentacion3."',
					Fundamentacion4 = '".$Fundamentacion4."',
					Fundamentacion5 = '".$Fundamentacion5."',
					Fundamentacion6 = '".$Fundamentacion6."',
					Fundamentacion7 = '".$Fundamentacion7."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					Capacitacion = '".$Capacitacion."'";
		execute($sql);
		##	participantes
		$sql = "DELETE FROM rh_capacitacion_empleados
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					Capacitacion = '".$Capacitacion."'";
		execute($sql);
		for ($i=0; $i < count($participantes_CodPersona); $i++) { 
			$sql = "INSERT INTO rh_capacitacion_empleados
					SET
						Anio = '".$Anio."',
						CodOrganismo = '".$CodOrganismo."',
						Capacitacion = '".$Capacitacion."',
						CodPersona = '".$participantes_CodPersona[$i]."',
						CodDependencia = '".$participantes_CodDependencia[$i]."',
						NroAsistencias = '".$participantes_NroAsistencias[$i]."',
						HoraAsistencias = '".$participantes_HoraAsistencias[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	horario
		$sql = "DELETE FROM rh_capacitacion_hora
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					Capacitacion = '".$Capacitacion."'";
		execute($sql);
		for ($i=0; $i < count($hora_Estado); $i++) {
			##	empleados
			for ($j=0; $j < count($participantes_CodPersona); $j++) {
				##	inserto hora
				$sql = "INSERT INTO rh_capacitacion_hora
						SET
							Anio = '".$Anio."',
							CodOrganismo = '".$CodOrganismo."',
							Capacitacion = '".$Capacitacion."',
							CodPersona = '".$participantes_CodPersona[$j]."',
							Secuencia = '".($i+1)."',
							Estado = '".$hora_Estado[$i]."',
							PeriodoInicio = '".formatFechaAMD($hora_FechaDesde[$i])."',
							PeriodoFin = '".formatFechaAMD($hora_FechaHasta[$i])."',
							FechaDesde = '".formatFechaAMD($hora_FechaDesde[$i])."',
							FechaHasta = '".formatFechaAMD($hora_FechaHasta[$i])."',
							Lunes = '".($hora_Lunes[$i]?'S':'N')."',
							HoraInicioLunes = '".formatHora24($hora_HoraInicioLunes[$i])."',
							HoraFinLunes = '".formatHora24($hora_HoraFinLunes[$i])."',
							Martes = '".($hora_Martes[$i]?'S':'N')."',
							HoraInicioMartes = '".formatHora24($hora_HoraInicioMartes[$i])."',
							HoraFinMartes = '".formatHora24($hora_HoraFinMartes[$i])."',
							Miercoles = '".($hora_Miercoles[$i]?'S':'N')."',
							HoraInicioMiercoles = '".formatHora24($hora_HoraInicioMiercoles[$i])."',
							HoraFinMiercoles = '".formatHora24($hora_HoraFinMiercoles[$i])."',
							Jueves = '".($hora_Jueves[$i]?'S':'N')."',
							HoraInicioJueves = '".formatHora24($hora_HoraInicioJueves[$i])."',
							HoraFinJueves = '".formatHora24($hora_HoraFinJueves[$i])."',
							Viernes = '".($hora_Viernes[$i]?'S':'N')."',
							HoraInicioViernes = '".formatHora24($hora_HoraInicioViernes[$i])."',
							HoraFinViernes = '".formatHora24($hora_HoraFinViernes[$i])."',
							Sabado = '".($hora_Sabado[$i]?'S':'N')."',
							HoraInicioSabado = '".formatHora24($hora_HoraInicioSabado[$i])."',
							HoraFinSabado = '".formatHora24($hora_HoraFinSabado[$i])."',
							Domingo = '".($hora_Domingo[$i]?'S':'N')."',
							HoraInicioDomingo = '".formatHora24($hora_HoraInicioDomingo[$i])."',
							HoraFinDomingo = '".formatHora24($hora_HoraFinDomingo[$i])."',
							TotalDias = '".$hora_TotalDias[$i]."',
							TotalHoras = '".$hora_TotalHoras[$i]."',
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()";
				execute($sql);
			}
		}
		##	-----------------
		mysql_query("COMMIT");
	}
	//	aprobar
	elseif ($accion == "aprobar") {
		mysql_query("BEGIN");
		##	-----------------
		$sql = "SELECT Vacantes
				FROM rh_capacitacion
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					Capacitacion = '".$Capacitacion."'";
		$Vacantes = getVar3($sql);
		##	valido
		if (setNumero($Participantes) > setNumero($Vacantes)) die("El n&uacute;mero de Participantes no puede ser mayor a las Vacantes disponibles");
		##	actualizo
		$sql = "UPDATE rh_capacitacion
				SET
					Estado = 'AP',
					AprobadoPor = '".$_SESSION["CODPERSONA_ACTUAL"]."',
					FechaAprobado = NOW(),
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					Capacitacion = '".$Capacitacion."'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	iniciar
	elseif ($accion == "iniciar") {
		mysql_query("BEGIN");
		##	-----------------
		$sql = "UPDATE rh_capacitacion
				SET
					Estado = 'IN',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					Capacitacion = '".$Capacitacion."'";
		execute($sql);
		##	gastos
		$Total = 0;
		for ($i=0; $i < count($gastos_Numero); $i++) {
			##	valido
			if (!trim($gastos_Numero[$i]) || !trim($gastos_Fecha[$i]) || !trim($gastos_SubTotal[$i])) die("Debe llenar los campos obligatorios en la Ficha Gastos");
			##	inserto
			$sql = "INSERT INTO rh_capacitacion_gastos
					SET
						Anio = '".$Anio."',
						CodOrganismo = '".$CodOrganismo."',
						Capacitacion = '".$Capacitacion."',
						Secuencia = '".($i+1)."',
						Numero = '".$gastos_Numero[$i]."',
						Fecha = '".formatFechaAMD($gastos_Fecha[$i])."',
						SubTotal = '".setNumero($gastos_SubTotal[$i])."',
						Impuestos = '".setNumero($gastos_Impuestos[$i])."',
						Total = '".setNumero($gastos_Total[$i])."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
			##	
			$Total += setNumero($gastos_Total[$i]);
		}
		$ImporteGastos = round(($Total / 3),2);
		##	actualizo
		$sql = "UPDATE rh_capacitacion_empleados
				SET
					ImporteGastos = '".$ImporteGastos."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					Capacitacion = '".$Capacitacion."'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	terminar
	elseif ($accion == "terminar") {
		mysql_query("BEGIN");
		##	-----------------
		$sql = "UPDATE rh_capacitacion
				SET
					Estado = 'TE',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					Capacitacion = '".$Capacitacion."'";
		execute($sql);
		##	participantes
		for ($i=0; $i < count($participantes_CodPersona); $i++) {
			##	valido
			if (setNumero($participantes_DiasAsistidos[$i]) > setNumero($participantes_NroAsistencias[$i])) die("<strong>Dias Asistidos</strong> no puede ser mayor a <strong>Total Dias</strong>");
			##	actualizo
			$idx = "participantes_FlagAprobado" . $participantes_CodPersona[$i];
			$sql = "UPDATE rh_capacitacion_empleados
					SET
						DiasAsistidos = '".setNumero($participantes_DiasAsistidos[$i])."',
						Nota = '".setNumero($participantes_Nota[$i])."',
						FlagAprobado = '".($_POST[$idx]?'S':'N')."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()
					WHERE
						Anio = '".$Anio."' AND
						CodOrganismo = '".$CodOrganismo."' AND
						Capacitacion = '".$Capacitacion."' AND
						CodPersona = '".$participantes_CodPersona[$i]."'";
			execute($sql);
		}
		##	-----------------
		mysql_query("COMMIT");
	}
}
elseif ($modulo == "ajax") {
	//	insertar linea
	if ($accion == "participantes_insertar") {
		$sql = "SELECT
					p.CodPersona,
					p.NomCompleto,
					e.CodEmpleado,
					e.CodDependencia
				FROM
					mastpersonas p
					INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
				WHERE p.CodPersona = '".$CodPersona."'";
		$field_participante = getRecords($sql);
		foreach ($field_participante as $f) {
			$id = $f['CodPersona'];
			?>
            <tr class="trListaBody" onclick="clk($(this), 'participantes', 'participantes_<?=$id?>');" id="participantes_<?=$id?>">
                <th><?=$nro_detalles?></th>
                <td align="center">
                	<input type="hidden" name="participantes_CodPersona[]" value="<?=$f['CodPersona']?>" />
                	<input type="hidden" name="participantes_CodDependencia[]" value="<?=$f['CodDependencia']?>" />
                    <?=$f['CodEmpleado']?>
                </td>
                <td>
                    <?=htmlentities($f['NomCompleto'])?>
                </td>
	            <td align="center">
	            	<input type="text" name="participantes_NroAsistencias[]" class="cell NroAsistencias" style="text-align:center;" readonly />
	            </td>
	            <td align="center">
	            	<input type="text" name="participantes_HoraAsistencias[]" class="cell HoraAsistencias" style="text-align:center;" readonly />
	            </td>
	            <td align="center">
	            	<input type="text" name="participantes_DiasAsistidos[]" value="<?=$f['DiasAsistidos']?>" class="cell" style="text-align:center;" disabled />
	            </td>
	            <td align="center">
	            	<input type="checkbox" name="participantes_FlagAprobado<?=$id?>" value="S" <?=chkFlag($f['FlagAprobado'])?> disabled />
	            </td>
	            <td align="center">
	            	<input type="text" name="participantes_Nota[]" value="<?=number_format(0,2,',','.')?>" class="cell currency" style="text-align:center;" disabled />
	            </td>
	            <td align="right">
	            	<input type="text" name="participantes_ImporteGastos[]" value="<?=number_format(0,2,',','.')?>" class="cell ImporteGastos" style="text-align:right;" readonly />
	            </td>
            </tr>
            <?php
		}
	}
	//	insertar linea
	elseif ($accion == "hora_insertar") {
		$id = $nro_detalle;
		?>
        <tr class="trListaBody" onclick="clk($(this), 'hora', 'hora_<?=$id?>');" id="hora_<?=$id?>">
            <th><?=$id?></th>
            <td>
            	<table border="1" width="100%">
				    <tr>
						<td class="tagForm" width="75">Estado:</td>
						<td>
							<select name="hora_Estado[]" style="width:75px;">
								<?=loadSelectGeneral('ESTADO','A')?>
							</select>
							<input type="hidden" name="hora_Lunes[]" id="hora_Lunes<?=$id?>" />
							<input type="hidden" name="hora_Martes[]" id="hora_Martes<?=$id?>" />
							<input type="hidden" name="hora_Miercoles[]" id="hora_Miercoles<?=$id?>" />
							<input type="hidden" name="hora_Jueves[]" id="hora_Jueves<?=$id?>" />
							<input type="hidden" name="hora_Viernes[]" id="hora_Viernes<?=$id?>" />
							<input type="hidden" name="hora_Sabado[]" id="hora_Sabado<?=$id?>" />
							<input type="hidden" name="hora_Domingo[]" id="hora_Domingo<?=$id?>" />
						</td>
						<td align="center">
							L <input type="checkbox" value="S" onclick="$('#hora_Lunes<?=$id?>').val(this.checked); $('#hora_HoraInicioLunes<?=$id?>').prop('readonly', !this.checked).val(''); $('#hora_HoraFinLunes<?=$id?>').prop('readonly', !this.checked).val(''); totalDiasHoras('<?=$id?>');" />
						</td>
						<td align="center">
							M <input type="checkbox" value="S" onclick="$('#hora_Martes<?=$id?>').val(this.checked); $('#hora_HoraInicioMartes<?=$id?>').prop('readonly', !this.checked).val(''); $('#hora_HoraFinMartes<?=$id?>').prop('readonly', !this.checked).val(''); totalDiasHoras('<?=$id?>');" />
						</td>
						<td align="center">
							M <input type="checkbox" value="S" onclick="$('#hora_Miercoles<?=$id?>').val(this.checked); $('#hora_HoraInicioMiercoles<?=$id?>').prop('readonly', !this.checked).val(''); $('#hora_HoraFinMiercoles<?=$id?>').prop('readonly', !this.checked).val(''); totalDiasHoras('<?=$id?>');" />
						</td>
						<td align="center">
							J <input type="checkbox" value="S" onclick="$('#hora_Jueves<?=$id?>').val(this.checked); $('#hora_HoraInicioJueves<?=$id?>').prop('readonly', !this.checked).val(''); $('#hora_HoraFinJueves<?=$id?>').prop('readonly', !this.checked).val(''); totalDiasHoras('<?=$id?>');" />
						</td>
						<td align="center">
							V <input type="checkbox" value="S" onclick="$('#hora_Viernes<?=$id?>').val(this.checked); $('#hora_HoraInicioViernes<?=$id?>').prop('readonly', !this.checked).val(''); $('#hora_HoraFinViernes<?=$id?>').prop('readonly', !this.checked).val(''); totalDiasHoras('<?=$id?>');" />
						</td>
						<td align="center">
							S <input type="checkbox" value="S" onclick="$('#hora_Sabado<?=$id?>').val(this.checked); $('#hora_HoraInicioSabado<?=$id?>').prop('readonly', !this.checked).val(''); $('#hora_HoraFinSabado<?=$id?>').prop('readonly', !this.checked).val(''); totalDiasHoras('<?=$id?>');" />
						</td>
						<td align="center">
							D <input type="checkbox" value="S" onclick="$('#hora_Domingo<?=$id?>').val(this.checked); $('#hora_HoraInicioDomingo<?=$id?>').prop('readonly', !this.checked).val(''); $('#hora_HoraFinDomingo<?=$id?>').prop('readonly', !this.checked).val(''); totalDiasHoras('<?=$id?>');" />
						</td>
						<td>Total</td>
					</tr>
				    <tr>
						<td class="tagForm">Desde:</td>
						<td valign="bottom">
							<input type="text" name="hora_FechaDesde[]" id="hora_FechaDesde<?=$id?>" maxlength="10" style="width:70px;" class="datepicker" onkeyup="setFechaDMA(this);" onchange="totalDiasHoras('<?=$id?>');" />
						</td>
						<td align="center">
							<input type="text" name="hora_HoraInicioLunes[]" id="hora_HoraInicioLunes<?=$id?>" maxlength="8" style="width:50px;" class="time" readonly onchange="totalDiasHoras('<?=$id?>');" />
						</td>
						<td align="center">
							<input type="text" name="hora_HoraInicioMartes[]" id="hora_HoraInicioMartes<?=$id?>" maxlength="8" style="width:50px;" class="time" readonly onchange="totalDiasHoras('<?=$id?>');" />
						</td>
						<td align="center">
							<input type="text" name="hora_HoraInicioMiercoles[]" id="hora_HoraInicioMiercoles<?=$id?>" maxlength="8" style="width:50px;" class="time" readonly onchange="totalDiasHoras('<?=$id?>');" />
						</td>
						<td align="center">
							<input type="text" name="hora_HoraInicioJueves[]" id="hora_HoraInicioJueves<?=$id?>" maxlength="8" style="width:50px;" class="time" readonly onchange="totalDiasHoras('<?=$id?>');" />
						</td>
						<td align="center">
							<input type="text" name="hora_HoraInicioViernes[]" id="hora_HoraInicioViernes<?=$id?>" maxlength="8" style="width:50px;" class="time" readonly onchange="totalDiasHoras('<?=$id?>');" />
						</td>
						<td align="center">
							<input type="text" name="hora_HoraInicioSabado[]" id="hora_HoraInicioSabado<?=$id?>" maxlength="8" style="width:50px;" class="time" readonly onchange="totalDiasHoras('<?=$id?>');" />
						</td>
						<td align="center">
							<input type="text" name="hora_HoraInicioDomingo[]" id="hora_HoraInicioDomingo<?=$id?>" maxlength="8" style="width:50px;" class="time" readonly onchange="totalDiasHoras('<?=$id?>');" />
						</td>
						<td valign="bottom">
							<input type="text" name="hora_TotalDias[]" id="hora_TotalDias<?=$id?>" style="width:50px;" class="hora_TotalDias" readonly /> <i>Dias</i>
						</td>
					</tr>
				    <tr>
						<td class="tagForm">Hasta:</td>
						<td valign="bottom">
							<input type="text" name="hora_FechaHasta[]" id="hora_FechaHasta<?=$id?>" maxlength="10" style="width:70px;" class="datepicker" onkeyup="setFechaDMA(this);" onchange="totalDiasHoras('<?=$id?>');" />
						</td>
						<td align="center">
							<input type="text" name="hora_HoraFinLunes[]" id="hora_HoraFinLunes<?=$id?>" maxlength="8" style="width:50px;" class="time" readonly onchange="totalDiasHoras('<?=$id?>');" />
						</td>
						<td align="center">
							<input type="text" name="hora_HoraFinMartes[]" id="hora_HoraFinMartes<?=$id?>" maxlength="8" style="width:50px;" class="time" readonly onchange="totalDiasHoras('<?=$id?>');" />
						</td>
						<td align="center">
							<input type="text" name="hora_HoraFinMiercoles[]" id="hora_HoraFinMiercoles<?=$id?>" maxlength="8" style="width:50px;" class="time" readonly onchange="totalDiasHoras('<?=$id?>');" />
						</td>
						<td align="center">
							<input type="text" name="hora_HoraFinJueves[]" id="hora_HoraFinJueves<?=$id?>" maxlength="8" style="width:50px;" class="time" readonly onchange="totalDiasHoras('<?=$id?>');" />
						</td>
						<td align="center">
							<input type="text" name="hora_HoraFinViernes[]" id="hora_HoraFinViernes<?=$id?>" maxlength="8" style="width:50px;" class="time" readonly onchange="totalDiasHoras('<?=$id?>');" />
						</td>
						<td align="center">
							<input type="text" name="hora_HoraFinSabado[]" id="hora_HoraFinSabado<?=$id?>" maxlength="8" style="width:50px;" class="time" readonly onchange="totalDiasHoras('<?=$id?>');" />
						</td>
						<td align="center">
							<input type="text" name="hora_HoraFinDomingo[]" id="hora_HoraFinDomingo<?=$id?>" maxlength="8" style="width:50px;" class="time" readonly onchange="totalDiasHoras('<?=$id?>');" />
						</td>
						<td valign="bottom">
							<input type="text" name="hora_TotalHoras[]" id="hora_TotalHoras<?=$id?>" style="width:50px;" class="hora_TotalHoras" readonly onfocus="totalDiasHoras('<?=$id?>');" /> <i>Horas</i>
						</td>
					</tr>
				</table>
            </td>
        </tr>
        <?php
	}
	//	insertar linea
	elseif ($accion == "gastos_insertar") {
		$id = $nro_detalle;
		?>
        <tr class="trListaBody" onclick="clk($(this), 'gastos', 'gastos_<?=$id?>');" id="gastos_<?=$id?>">
            <th><?=$id?></th>
            <td>
                <input type="text" name="gastos_Numero[]" class="cell" maxlength="15" />
            </td>
            <td>
                <input type="text" name="gastos_Fecha[]" class="cell datepicker" style="text-align:center;" maxlength="10" />
            </td>
            <td>
                <input type="text" name="gastos_SubTotal[]" id="gastos_SubTotal<?=$id?>" value="<?=number_format(0,2,',','.')?>" class="cell currency gastos_SubTotal" style="text-align:right;" onchange="setCostos('<?=$id?>');" />
            </td>
            <td>
                <input type="text" name="gastos_Impuestos[]" id="gastos_Impuestos<?=$id?>" value="<?=number_format(0,2,',','.')?>" class="cell currency gastos_Impuestos" style="text-align:right;" onchange="setCostos('<?=$id?>');" />
            </td>
            <td>
                <input type="text" name="gastos_Total[]" id="gastos_Total<?=$id?>" value="<?=number_format(0,2,',','.')?>" class="cell gastos_Total" style="text-align:right; font-weight:bold;" readonly />
            </td>
        </tr>
        <?php
	}
	//	obtener total
	elseif ($accion == "totalDiasHoras") {
		$diasLunes = (($Lunes=='true')?diasSemanaXFecha($FechaDesde, $FechaHasta, 1):0);
		$diasMartes = (($Martes=='true')?diasSemanaXFecha($FechaDesde, $FechaHasta, 2):0);
		$diasMiercoles = (($Miercoles=='true')?diasSemanaXFecha($FechaDesde, $FechaHasta, 3):0);
		$diasJueves = (($Jueves=='true')?diasSemanaXFecha($FechaDesde, $FechaHasta, 4):0);
		$diasViernes = (($Viernes=='true')?diasSemanaXFecha($FechaDesde, $FechaHasta, 5):0);
		$diasSabado = (($Sabado=='true')?diasSemanaXFecha($FechaDesde, $FechaHasta, 6):0);
		$diasDomingo = (($Domingo=='true')?diasSemanaXFecha($FechaDesde, $FechaHasta, 7):0);
		##	
		$TotalDias = $diasLunes + $diasMartes + $diasMiercoles + $diasJueves + $diasViernes + $diasSabado + $diasDomingo;
		##	
		$horasLunes = (($Lunes=='true' && $LunesI && $LunesF)?getDiffHora(formatHora24($LunesI), formatHora24($LunesF)):'00:00');
		$horasMartes = (($Martes=='true' && $MartesI && $MartesF)?getDiffHora(formatHora24($MartesI), formatHora24($MartesF)):'00:00');
		$horasMiercoles = (($Miercoles=='true' && $MiercolesI && $MiercolesF)?getDiffHora(formatHora24($MiercolesI), formatHora24($MiercolesF)):'00:00');
		$horasJueves = (($Jueves=='true' && $JuevesI && $JuevesF)?getDiffHora(formatHora24($JuevesI), formatHora24($JuevesF)):'00:00');
		$horasViernes = (($Viernes=='true' && $ViernesI && $ViernesF)?getDiffHora(formatHora24($ViernesI), formatHora24($ViernesF)):'00:00');
		$horasSabado = (($Sabado=='true' && $SabadoI && $SabadoF)?getDiffHora(formatHora24($SabadoI), formatHora24($SabadoF)):'00:00');
		$horasDomingo = (($Domingo=='true' && $DomingoI && $DomingoF)?getDiffHora(formatHora24($DomingoI), formatHora24($DomingoF)):'00:00');
		##	
		$horas[] = sumarHorasXDias($horasLunes, $diasLunes);
		$horas[] = sumarHorasXDias($horasMartes, $diasMartes);
		$horas[] = sumarHorasXDias($horasMiercoles, $diasMiercoles);
		$horas[] = sumarHorasXDias($horasJueves, $diasJueves);
		$horas[] = sumarHorasXDias($horasViernes, $diasViernes);
		$horas[] = sumarHorasXDias($horasSabado, $diasSabado);
		$horas[] = sumarHorasXDias($horasDomingo, $diasDomingo);
		$TotalHoras = sumarHorasArray($horas);
		##	
		die("$TotalDias|$TotalHoras");
	}
	//	validar
	elseif ($accion == "modificar") {
		list($Anio, $CodOrganismo, $Capacitacion) = explode("_", $codigo);
		$sql = "SELECT Estado
				FROM rh_capacitacion
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					Capacitacion = '".$Capacitacion."'";
		$Estado = getVar3($sql);
		if ($Estado != 'PE') die("Solo puede modificar capacitaciones <strong>Pendientes</strong>");
	}
	//	validar
	elseif ($accion == "aprobar") {
		list($Anio, $CodOrganismo, $Capacitacion) = explode("_", $codigo);
		$sql = "SELECT Estado
				FROM rh_capacitacion
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					Capacitacion = '".$Capacitacion."'";
		$Estado = getVar3($sql);
		if ($Estado != 'PE') die("Solo puede aprobar capacitaciones <strong>Pendientes</strong>");
	}
	//	validar
	elseif ($accion == "iniciar") {
		list($Anio, $CodOrganismo, $Capacitacion) = explode("_", $codigo);
		$sql = "SELECT Estado
				FROM rh_capacitacion
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					Capacitacion = '".$Capacitacion."'";
		$Estado = getVar3($sql);
		if ($Estado != 'AP') die("Solo puede iniciar capacitaciones <strong>Aprobadas</strong>");
	}
	//	validar
	elseif ($accion == "terminar") {
		list($Anio, $CodOrganismo, $Capacitacion) = explode("_", $codigo);
		$sql = "SELECT Estado
				FROM rh_capacitacion
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					Capacitacion = '".$Capacitacion."'";
		$Estado = getVar3($sql);
		if ($Estado != 'IN') die("Solo puede termina capacitaciones <strong>Iniciadas</strong>");
	}

}
?>