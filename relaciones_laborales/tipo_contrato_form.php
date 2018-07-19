<?php
if ($opcion == "nuevo") {
	$field['Estado'] = "A";
	$field['FlagNomina'] = "N";
	$field['FlagVencimiento'] = "N";
	##
	$titulo = "Nuevo Registro";
	$accion = "nuevo";
	$label_submit = "Guardar";
	$disabled_nuevo = "disabled";
	$clkCancelar = "document.getElementById('frmentrada').submit();";
	$focus = "TipoContrato";
}
elseif ($opcion == "modificar" || $opcion == "ver") {
	//	consulto datos generales
	$sql = "SELECT * FROM rh_tipocontrato WHERE TipoContrato = '".$sel_registros."'";
	$field = getRecord($sql);
	
	if ($opcion == "modificar") {
		$titulo = "Modificar Registro";
		$accion = "modificar";
		$disabled_modificar = "disabled";
		$display_modificar = "display:none;";
		$label_submit = "Modificar";
		$clkCancelar = "document.getElementById('frmentrada').submit();";
		$focus = "Descripcion";
	}
	
	elseif ($opcion == "ver") {
		$titulo = "Ver Registro";
		$disabled_nuevo = "disabled";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$display_modificar = "display:none;";
		$display_submit = "display:none;";
		$clkCancelar = "document.getElementById('frmentrada').submit();";
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

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=tipo_contrato_lista" method="POST" enctype="multipart/form-data" onsubmit="return form(this, '<?=$accion?>');" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />

<table width="<?=$_width?>" class="tblForm">
	<tr>
    	<td colspan="2" class="divFormCaption">Datos del Registro</td>
    </tr>
	<tr>
		<td class="tagForm">Tipo:</td>
		<td>
        	<input type="text" id="TipoContrato" value="<?=$field['TipoContrato']?>" style="width:50px;" maxlength="2" <?=$disabled_modificar?> />
		</td>
	</tr>
	<tr>
		<td class="tagForm">Descripci&oacute;n:</td>
		<td>
        	<input type="text" id="Descripcion" value="<?=$field['Descripcion']?>" style="width:315px;" maxlength="40" <?=$disabled_ver?> />
		</td>
	</tr>
	<tr>
		<td class="tagForm">En N&oacute;mina?:</td>
		<td>
            <input type="radio" name="FlagNomina" id="FlagNominaS" value="S" <?=chkOpt($field['FlagNomina'], "S");?> <?=$disabled_ver?> /> Si
            &nbsp; &nbsp;
            <input type="radio" name="FlagNomina" id="FlagNominaN" value="N" <?=chkOpt($field['FlagNomina'], "N");?> <?=$disabled_ver?> /> No
		</td>
	</tr>
	<tr>
		<td class="tagForm">Tiene Vencimiento?:</td>
		<td>
            <input type="radio" name="FlagVencimiento" id="FlagVencimientoS" value="S" <?=chkOpt($field['FlagVencimiento'], "S");?> <?=$disabled_ver?> /> Si
            &nbsp; &nbsp;
            <input type="radio" name="FlagVencimiento" id="FlagVencimientoN" value="N" <?=chkOpt($field['FlagVencimiento'], "N");?> <?=$disabled_ver?> /> No
		</td>
	</tr>
	<tr>
		<td class="tagForm">Estado:</td>
		<td>
            <input type="radio" name="Estado" id="Activo" value="A" <?=chkOpt($field['Estado'], "A");?> <?=$disabled_nuevo?> /> Activo
            &nbsp; &nbsp;
            <input type="radio" name="Estado" id="Inactivo" value="I" <?=chkOpt($field['Estado'], "I");?> <?=$disabled_nuevo?> /> Inactivo
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
<input type="button" value="Cancelar" style="width:75px;" onclick="<?=$clkCancelar?>" />
</center>

</form>

<div style="width:<?=$_width?>px" class="divMsj">Campos Obligatorios *</div>

<script type="text/javascript" language="javascript">
function form(form, accion) {
	//	valido
	var error = "";
	if ($("#TipoContrato").val().trim() == "" || $("#Descripcion").val().trim() == "") error = "Debe llenar los campos obligatorios";
	
	//	valido errores
	if (error != "") {
		cajaModal(error, "error", 400);
	} else {
		//	formulario
		var post = getForm(form);
		//	ajax
		$.ajax({
			type: "POST",
			url: "lib/form_ajax.php",
			data: "modulo=tipo_contrato&accion="+accion+"&"+post,
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