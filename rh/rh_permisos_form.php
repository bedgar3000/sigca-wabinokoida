<?php
if ($opcion == "nuevo") {
	##	horario
	$DiaSemana = getWeekDay($FechaDesde);
	$sql = "SELECT
				hld.*
			FROM
				rh_horariolaboraldet hld
				INNER JOIN mastempleado e ON (e.CodHorario = hld.CodHorario)
			WHERE
				e.CodPersona = '".$_SESSION["CODPERSONA_ACTUAL"]."' AND
				hld.Dia = '".$DiaSemana."' AND
				hld.FlagLaborable = 'S'";
	$field_horario = getRecord($sql);
	$field['HoraDesde'] = formatHora12($field_horario['Entrada1']);
	if ($field_horario['Salida2'] == '00:00:00') {
		$field['HoraHasta'] = formatHora12($field_horario['Salida1']);
	} else {
		$field['HoraHasta'] = formatHora12($field_horario['Salida2']);
		$Turno2 = getDiffHora($field_horario['Entrada2'], $field_horario['Salida2']);
	}
	$Turno1 = getDiffHora($field_horario['Entrada1'], $field_horario['Salida1']);
	//	aprobador
	##	consulto responsable de la dependencia
	$sql = "SELECT
				d.CodPersona,
				p.NomCompleto,
				e.CodEmpleado,
				e.CodCargo
			FROM
				mastdependencias d
				INNER JOIN mastpersonas p ON (p.CodPersona = d.CodPersona)
				INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
			WHERE d.CodDependencia = '".$_SESSION["DEPENDENCIA_ACTUAL"]."'";
	$field_responsable = getRecord($sql);
	##	verifico que no sea la misma persona
	if ($_SESSION["CODPERSONA_ACTUAL"] == $field_responsable['CodPersona']) {
		##	consulto a quien reporta
		$sql = "SELECT
					p.CodPersona,
					p.NomCompleto,
					e.CodEmpleado
				FROM
					rh_cargoreporta cr
					INNER JOIN mastempleado e ON (e.CodCargoTemp = cr.CargoReporta)
					INNER JOIN mastpersonas p ON (p.CodPersona = e.CodPersona)
				WHERE
					cr.CodCargo = '".$field_responsable['CodCargo']."' AND
					e.Estado = 'A'
				ORDER BY cr.Secuencia
				LIMIT 0, 1";
		$field_aprobador = getRecord($sql);
		if (!count($field_aprobador)) {
			$sql = "SELECT
						p.CodPersona,
						p.NomCompleto,
						e.CodEmpleado
					FROM
						rh_cargoreporta cr
						INNER JOIN mastempleado e ON (e.CodCargo = cr.CargoReporta)
						INNER JOIN mastpersonas p ON (p.CodPersona = e.CodPersona)
					WHERE
						cr.CodCargo = '".$field_responsable['CodCargo']."' AND
						e.Estado = 'A'
					ORDER BY cr.Secuencia
					LIMIT 0, 1";
			$field_aprobador = getRecord($sql);
		}
	} else $field_aprobador = $field_responsable;
	##	defaults
	$field['FechaIngreso'] = $FechaActual;
	$field['PeriodoContable'] = $PeriodoActual;
	$field['TotalTiempo'] = sumarHoras($Turno1, $Turno2);
	list($horas, $minutos) = explode(":", $field['TotalTiempo']);
	$field['TotalFecha'] = 1;
	$field['TotalDias'] = 1;
	$field['TotalHoras'] = $horas;
	$field['TotalMinutos'] = $minutos;
	$field['FechaDesde'] = $FechaActual;
	$field['FechaHasta'] = $FechaActual;
	$field['FlagRemunerado'] = "S";
	$field['Estado'] = "P";
	$field['CodEmpleado'] = $_SESSION["CODEMPLEADO_ACTUAL"];
	$field['CodPersona'] = $_SESSION["CODPERSONA_ACTUAL"];
	$field['NomPersona'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
	$field['CodAprobador'] = $field_aprobador['CodEmpleado'];
	$field['Aprobador'] = $field_aprobador['CodPersona'];
	$field['NomAprobador'] = $field_aprobador['NomCompleto'];
	$field['CodHorario'] = $field_horario['CodHorario'];
	##
	$titulo = "Nuevo Permiso";
	$accion = "nuevo";
	$disabled_nuevo = "disabled";
	$disabled_modificar = "";
	$disabled_ver = "";
	$disabled_aprobar = "disabled";
	$disabled_fecha = "disabled";
	$disabled_hora = "";
	$display_modificar = "";
	$display_ver = "";
	$display_submit = "";
	$label_submit = "Guardar";
	$clkCancelar = "document.getElementById('frmentrada').submit();";
	$focus = "TipoFalta";
}
elseif ($opcion == "modificar" || $opcion == "ver" || $opcion == "aprobar" || $opcion == "anular") {
	//	consulto datos generales
	$sql = "SELECT
				pm.*,
				p1.NomCompleto AS NomPersona,
				p2.NomCompleto AS NomAprobador,
				e1.CodHorario
			FROM 
				rh_permisos pm
				INNER JOIN mastpersonas p1 ON (p1.CodPersona = pm.CodPersona)
				INNER JOIN mastpersonas p2 ON (p2.CodPersona = pm.Aprobador)
				INNER JOIN mastempleado e1 ON (e1.CodPersona = p1.CodPersona)
			WHERE pm.CodPermiso = '".$sel_registros."'";
	$field = getRecord($sql);
	$field['HoraDesde'] = formatHora12($field['HoraDesde']);
	$field['HoraHasta'] = formatHora12($field['HoraHasta']);
	
	if ($opcion == "modificar") {
		$titulo = "Modificar Permiso";
		$accion = "modificar";
		$disabled_nuevo = "";
		$disabled_modificar = "disabled";
		$disabled_ver = "";
		$disabled_aprobar = "disabled";
		if ($field['FechaDesde'] != $field['FechaHasta']) $disabled_hora = "disabled";
		$display_modificar = "display:none;";
		$display_ver = "";
		$display_submit = "";
		$label_submit = "Modificar";
		$clkCancelar = "document.getElementById('frmentrada').submit();";
		$focus = "TipoFalta";
	}
	
	elseif ($opcion == "ver") {
		$titulo = "Ver Permiso";
		$accion = "";
		$disabled_nuevo = "disabled";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$disabled_aprobar = "disabled";
		$disabled_hora = "disabled";
		$display_modificar = "display:none;";
		$display_ver = "display:none;";
		$display_submit = "display:none;";
		$label_submit = "";
		$clkCancelar = "document.getElementById('frmentrada').submit();";
		$focus = "btsubmit";
	}
	
	elseif ($opcion == "aprobar") {
		##	defaults
		$field['FechaAprobado'] = $FechaActual;
		##
		$titulo = "Aprobar Permiso";
		$accion = "aprobar";
		$disabled_nuevo = "disabled";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$disabled_aprobar = "";
		$disabled_hora = "disabled";
		$display_modificar = "display:none;";
		$display_ver = "display:none;";
		$display_submit = "";
		$label_submit = "Aprobar";
		$clkCancelar = "document.getElementById('frmentrada').submit();";
		$focus = "ObsAprobado";
	}
	
	elseif ($opcion == "anular") {
		$titulo = "Anular Permiso";
		$accion = "anular";
		$disabled_nuevo = "disabled";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$disabled_aprobar = "disabled";
		$disabled_hora = "disabled";
		$display_modificar = "display:none;";
		$display_ver = "display:none;";
		$display_submit = "";
		$label_submit = "Anular";
		$clkCancelar = "document.getElementById('frmentrada').submit();";
		$focus = "btSubmit";
	}
}
//	------------------------------------
$_width = 800;
if (!empty(trim($return))) $action = "gehen.php?anz=$return"; else $action = "../framemain.php";
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$titulo?></td>
		<td align="right"><a class="cerrar" href="document.getElementById('frmentrada').submit();">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="<?=$action?>" method="POST" enctype="multipart/form-data" onsubmit="return form(this, '<?=$accion?>');" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
<input type="hidden" name="fCodDependencia" id="fCodDependencia" value="<?=$fCodDependencia?>" />
<input type="hidden" name="fFechaIngresoD" id="fFechaIngresoD" value="<?=$fFechaIngresoD?>" />
<input type="hidden" name="fFechaIngresoH" id="fFechaIngresoH" value="<?=$fFechaIngresoH?>" />
<input type="hidden" name="fTipoFalta" id="fTipoFalta" value="<?=$fTipoFalta?>" />
<input type="hidden" name="fTipoPermiso" id="fTipoPermiso" value="<?=$fTipoPermiso?>" />

<table width="<?=$_width?>" class="tblForm">
	<tr>
    	<td colspan="4" class="divFormCaption">Datos del Permiso</td>
    </tr>
	<tr>
		<td class="tagForm">Permiso:</td>
		<td>
            <input type="text" id="CodPermiso" value="<?=$field['CodPermiso']?>" style="width:65px;" class="codigo" disabled="disabled" />
		</td>
		<td class="tagForm">Estado:</td>
		<td>
            <input type="hidden" id="Estado" value="<?=$field['Estado']?>" />
            <input type="text" value="<?=strtoupper(printValores("ESTADO-PERMISOS", $field['Estado']))?>" style="width:65px;" class="codigo" disabled="disabled" />
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Empleado:</td>
		<td class="gallery clearfix">
            <input type="hidden" id="CodPersona" value="<?=$field['CodPersona']?>" />
            <input type="hidden" id="CodEmpleado" value="<?=$field['CodEmpleado']?>" />
            <input type="hidden" id="CodHorario" value="<?=$field['CodHorario']?>" />
            <input type="text" id="NomPersona" value="<?=$field['NomPersona']?>" style="width:295px;" disabled />
            <a href="../lib/listas/listado_empleados.php?filtrar=default&ventana=permisos&cod=CodEmpleado&nom=NomPersona&campo3=CodPersona&campo4=CodHorario&validar=no&iframe=true&width=950&height=425" rel="prettyPhoto[iframe1]" id="btEmpleado" style=" <?=$display_ver?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
		<td class="tagForm">Fecha de Registro:</td>
		<td>
            <input type="text" id="FechaIngreso" value="<?=formatFechaDMA($field['FechaIngreso'])?>" style="width:65px;" class="codigo" disabled="disabled" />
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Aprobador:</td>
		<td class="gallery clearfix">
            <input type="hidden" id="Aprobador" value="<?=$field['Aprobador']?>" style="width:75px;" disabled />
            <input type="hidden" id="CodAprobador" value="<?=$field['CodAprobador']?>" style="width:75px;" disabled />
            <input type="text" id="NomAprobador" value="<?=$field['NomAprobador']?>" style="width:295px;" disabled />
            <a href="../lib/listas/listado_empleados.php?filtrar=default&cod=CodAprobador&nom=NomAprobador&campo3=Aprobador&validar=no&iframe=true&width=950&height=425" rel="prettyPhoto[iframe2]" id="btAprobador" style=" <?=$display_ver?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
		<td class="tagForm">Fecha de Aprobaci&oacute;n:</td>
		<td>
            <input type="text" id="FechaAprobado" value="<?=formatFechaDMA($field['FechaAprobado'])?>" style="width:65px;" class="codigo" disabled="disabled" />
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Motivo de Ausencia:</td>
		<td>
            <select id="TipoPermiso" style="width:300px;" <?=$disabled_ver?>>
                <option value="">&nbsp;</option>
                <?=getMiscelaneos($field['TipoPermiso'], 'PERMISOS')?>
            </select>
		</td>
		<td class="tagForm">Periodo:</td>
		<td>
            <input type="text" id="PeriodoContable" value="<?=$field['PeriodoContable']?>" style="width:45px;" class="codigo" disabled="disabled" />
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Tipo de Ausencia:</td>
		<td colspan="3">
            <select id="TipoFalta" style="width:300px;" <?=$disabled_ver?>>
                <option value="">&nbsp;</option>
                <?=getMiscelaneos($field['TipoFalta'], 'TIPOFALTAS')?>
            </select>
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Fecha:</td>
		<td>
			<input type="text" id="FechaDesde" value="<?=formatFechaDMA($field['FechaDesde'])?>" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFecha(this);" <?=$disabled_ver?> onchange="getTotalFecha();" /> -
			<input type="text" id="FechaHasta" value="<?=formatFechaDMA($field['FechaHasta'])?>" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFecha(this);" <?=$disabled_ver?> onchange="getTotalFecha();" />
			<span style="color:#555; font-style:italic;">(dd-mm-aaaa)</span>
		</td>
		<td class="tagForm">Dias:</td>
		<td>
            <input type="text" id="TotalFecha" value="<?=$field['TotalFecha']?>" style="width:35px;" disabled />
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Hora:</td>
		<td>
			<input type="text" id="HoraDesde" value="<?=($field['HoraDesde'])?>" maxlength="8" style="width:60px;" <?=$disabled_hora?> onchange="getTotalTiempo();" /> -
			<input type="text" id="HoraHasta" value="<?=($field['HoraHasta'])?>" maxlength="8" style="width:60px;" <?=$disabled_hora?> onchange="getTotalTiempo();" />
			<span style="color:#555; font-style:italic;">(hh:mm am/pm)</span>
		</td>
		<td class="tagForm">Tiempo:</td>
		<td>
            <input type="text" id="TotalTiempo" value="<?=$field['TotalTiempo']?>" style="width:35px;" disabled />
		</td>
	</tr>
    <tr>
		<td class="tagForm"></td>
		<td colspan="3">
			<input type="text" id="TotalDias" value="<?=$field['TotalDias']?>" style="width:35px;" disabled /> Dias
            &nbsp; &nbsp; &nbsp; 
			<input type="text" id="TotalHoras" value="<?=$field['TotalHoras']?>" style="width:35px;" disabled /> Horas
            &nbsp; &nbsp; &nbsp; 
			<input type="text" id="TotalMinutos" value="<?=$field['TotalMinutos']?>" style="width:35px;" disabled /> Minutos
		</td>
	</tr>
    <tr>
		<td class="tagForm">Descripci&oacute;n del Motivo:</td>
		<td colspan="3">
        	<textarea id="ObsMotivo" style="width:95%; height:50px;" <?=$disabled_ver?>><?=htmlentities($field['ObsMotivo'])?></textarea>
		</td>
	</tr>
	<tr>
		<td class="tagForm"></td>
		<td colspan="3">
            <input type="checkbox" name="FlagRemunerado" id="FlagRemunerado" value="S" <?=chkOpt($field['FlagRemunerado'], "S");?> <?=$disabled_aprobar?> /> ¿Remunerado? 
            &nbsp; &nbsp; &nbsp; 
            <input type="checkbox" name="FlagJustificativo" id="FlagJustificativo" value="S" <?=chkOpt($field['FlagJustificativo'], "S");?> <?=$disabled_aprobar?> /> ¿Entregar Justificativo? 
            &nbsp; &nbsp; &nbsp; 
            <input type="checkbox" name="FlagExento" id="FlagExento" value="S" <?=chkOpt($field['FlagExento'], "S");?> <?=$disabled_aprobar?> /> ¿Exento? 
		</td>
	</tr>
    <tr>
		<td class="tagForm">Obs. Aprobaci&oacute;n:</td>
		<td colspan="3">
        	<textarea id="ObsAprobado" style="width:95%; height:50px;" <?=$disabled_aprobar?>><?=htmlentities($field['ObsAprobado'])?></textarea>
		</td>
	</tr>
    <tr>
		<td class="tagForm">&Uacute;ltima Modif.:</td>
		<td>
			<input type="text" size="30" value="<?=$field['UltimoUsuario']?>" disabled="disabled" />
			<input type="text" size="25" value="<?=$field['UltimaFecha']?>" disabled="disabled" />
		</td>
	</tr>
</table>

<center>
	<input type="submit" id="btSubmit" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" />
	<input type="button" value="Cancelar" style="width:75px;" onclick="<?=$clkCancelar?>" />
</center>

</form>

<div style="width:<?=$_width?>px" class="divMsj">Campos Obligatorios *</div>

<script type="text/javascript" language="javascript">
function form(form, accion) {
	bloqueo(true);
	//	valido
	var error = "";
	if ($("#CodPersona").val() == "" || $("#Aprobador").val() == "" || $("#TipoFalta").val() == "" || $("#TipoPermiso").val() == "" || $("#FechaDesde").val() == "" || $("#FechaHasta").val() == "" || $("#HoraDesde").val() == "" || $("#HoraHasta").val() == "") error = "Debe llenar los campos obligatorios";
	else if (!valFecha($("#FechaDesde").val()) || !valFecha($("#FechaHasta").val())) error = "Formatos de fechas incorrectas";
	else if (formatFechaAMD($("#FechaDesde").val()) > formatFechaAMD($("#FechaHasta").val())) error = "Formatos de fechas incorrectas";
	else if (!valHora($("#HoraDesde").val()) || !valHora($("#HoraHasta").val())) error = "Formatos de horas incorrectas";
	else if (formatHora($("#HoraDesde").val()) > formatHora($("#HoraHasta").val())) error = "Formatos de horas incorrectas";
	
	//	valido errores
	if (error != "") {
		cajaModal(error, "error", 400);
	} else {
		//	formulario
		var post = getForm(form);
		//	ajax
		$.ajax({
			type: "POST",
			url: "rh_permisos_ajax.php",
			data: "modulo=permisos&accion="+accion+"&"+post,
			async: false,
			success: function(resp) {
				if (resp.trim() != "") cajaModal(resp, "error", 400);
				else {
					if (accion == "aprobar") form.action = form.action + "&imprimir=rh_permisos_pdf&_CodPermiso=" + $("#CodPermiso").val();
					form.submit();
				}
			}
		});
	}
	return false;
}

function getTotalFecha() {
	var FechaDesde = $('#FechaDesde').val();
	var FechaHasta = $('#FechaHasta').val();
	if (FechaDesde == FechaHasta) {
		$('#HoraDesde').prop('disabled', false);
		$('#HoraHasta').prop('disabled', false);
	} else {
		$('#HoraDesde').prop('disabled', true);
		$('#HoraHasta').prop('disabled', true);
	}
	$.ajax({
		type: "POST",
		url: "rh_permisos_ajax.php",
		data: "modulo=ajax&accion=getTotalFecha&FechaDesde="+FechaDesde+"&FechaHasta="+FechaHasta+"&CodHorario="+$('#CodHorario').val(),
		async: true,
		success: function(resp) {
			var datos = resp.split("|");
			$('#TotalFecha').val(datos[0]);
			$('#TotalDias').val(datos[0]);
			$('#TotalTiempo').val(datos[1]);
			$('#HoraDesde').val(datos[2]);
			$('#HoraHasta').val(datos[3]);
			$('#TotalHoras').val(datos[4]);
			$('#TotalMinutos').val(datos[5]);
		}
	});
}

function getTotalTiempo() {
	var FechaDesde = $('#FechaDesde').val();
	var HoraDesde = $('#HoraDesde').val();
	var HoraHasta = $('#HoraHasta').val();
	$.ajax({
		type: "POST",
		url: "rh_permisos_ajax.php",
		data: "modulo=ajax&accion=getTotalTiempo&HoraDesde="+HoraDesde+"&HoraHasta="+HoraHasta+"&FechaDesde="+FechaDesde+"&CodHorario="+$('#CodHorario').val(),
		async: true,
		success: function(resp) {
			var datos = resp.split("|");
			$('#TotalTiempo').val(datos[0]);
			$('#TotalFecha').val(datos[1]);
			$('#TotalDias').val(datos[1]);
			$('#TotalHoras').val(datos[2]);
			$('#TotalMinutos').val(datos[3]);
		}
	});
}
</script>