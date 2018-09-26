<?php
if ($opcion == "nuevo") {
	$field['Periodo'] = $PeriodoActual;
	$field['Fecha'] = $FechaActual;
	##
	$titulo = "Nuevo Registro";
	$accion = "nuevo";
	$readonly_modificar = "";
	$readonly_ver = "";
	$display_submit = "";
	$label_submit = "Guardar";
	$clkCancelar = "document.getElementById('frmentrada').submit();";
	$focus = "Periodo";
}
elseif ($opcion == "modificar" || $opcion == "ver") {
	//	consulto datos generales
	$sql = "SELECT *
			FROM mastsueldosmin
			WHERE Secuencia = '".$sel_registros."'";
	$field = getRecord($sql);
	##
	if ($opcion == "modificar") {
		$titulo = "Modificar Registro";
		$accion = "modificar";
		$readonly_modificar = "readonly";
		$readonly_ver = "";
		$display_submit = "";
		$label_submit = "Modificar";
		$clkCancelar = "document.getElementById('frmentrada').submit();";
		$focus = "Monto";
	}
	##
	elseif ($opcion == "ver") {
		$titulo = "Ver Registro";
		$accion = "";
		$readonly_modificar = "disabled";
		$readonly_ver = "disabled";
		$display_submit = "display:none;";
		$label_submit = "";
		$clkCancelar = "document.getElementById('frmentrada').submit();";
		$focus = "btCancelar";
	}
}
//	------------------------------------
$_width = 500;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=rh_sueldos_minimos_lista" method="POST" enctype="multipart/form-data" onsubmit="return formulario(this, '<?=$accion?>');" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
<input type="hidden" name="Secuencia" id="Secuencia" value="<?=$field['Secuencia']?>" />

<table width="<?=$_width?>" class="tblForm">
	<tr>
    	<td colspan="2" class="divFormCaption">Datos Generales</td>
    </tr>
	<tr>
		<td class="tagForm">* Periodo:</td>
		<td>
        	<input type="text" name="Periodo" id="Periodo" value="<?=$field['Periodo']?>" style="width:50px;" maxlength="7" <?=$readonly_modificar?> />
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Sueldo M&iacute;nimo:</td>
		<td>
        	<input type="text" name="Monto" id="Monto" value="<?=number_format($field['Monto'], 2, ',', '.')?>" style="width:75px; text-align:right;" class="currency" <?=$readonly_ver?> />
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Fecha de Resoluci&oacute;n:</td>
		<td>
        	<input type="text" name="Fecha" id="Fecha" value="<?=formatFechaDMA($field['Fecha'])?>" class="datepicker" style="width:75px;" maxlength="10" <?=$readonly_ver?> />
		</td>
	</tr>
	<tr>
		<td class="tagForm">Resoluci&oacute;n:</td>
		<td>
        	<input type="text" name="NroResolucion" id="NroResolucion" value="<?=$field['NroResolucion']?>" style="width:125px;" maxlength="20" <?=$readonly_ver?> />
		</td>
	</tr>
	<tr>
		<td class="tagForm">Gaceta:</td>
		<td>
        	<input type="text" name="NroGaceta" id="NroGaceta" value="<?=$field['NroGaceta']?>" style="width:125px;" maxlength="20" <?=$readonly_ver?> />
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
<input type="submit" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" />
<input type="button" value="Cancelar" id="btCancelar" style="width:75px;" onclick="<?=$clkCancelar?>" />
</center>
</form>
<br />
<div style="width:<?=$_width?>px" class="divMsj">Campos Obligatorios *</div>

<script type="text/javascript" language="javascript">
function formulario(form, accion) {
	bloqueo(true);
	//	valido
	var error = "";
	if ($("#Periodo").val().trim() == "" || $("#Fecha").val().trim() == "" || $("#Monto").val().trim() == "") error = "Debe llenar los campos obligatorios";
	else if (!valPeriodo($("#Periodo").val())) error = "Formato del <strong>Periodo</strong> incorrecto";
	else if (!valFecha($("#Fecha").val())) error = "Formato de la <strong>Fecha de Resoluci&oacute;n</strong> incorrecta";
	else if (setNumero($("#Monto").val()) == 0 || isNaN(setNumero($("#Monto").val()))) error = "Formato del <strong>Sueldo M&iacute;nimo</strong> incorrecto";
	//	valido errores
	if (error != "") {
		cajaModal(error, "error", 400);
	} else {
		//	ajax
		$.ajax({
			type: "POST",
			url: "rh_sueldos_minimos_ajax.php",
			data: "modulo=sueldos_minimos&accion="+accion+"&"+$('#frmentrada').serialize(),
			async: false,
			success: function(resp) {
				if (resp.trim() != "") cajaModal(resp, "error", 400);
				else form.submit();
			}
		});
	}
	return false;
}
</script>