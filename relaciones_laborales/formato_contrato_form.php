<?php
if ($opcion == "nuevo") {
	$titulo = "Nuevo Registro";
	$accion = "nuevo";
	$label_submit = "Guardar";
	$disabled_nuevo = "disabled";
	$clkCancelar = "document.getElementById('frmentrada').submit();";
	$focus = "CodFormato";
}
elseif ($opcion == "modificar" || $opcion == "ver") {
	//	consulto datos generales
	$sql = "SELECT * FROM rh_formatocontrato WHERE CodFormato = '".$sel_registros."'";
	$field = getRecord($sql);
	
	if ($opcion == "modificar") {
		$titulo = "Modificar Registro";
		$accion = "modificar";
		$disabled_modificar = "disabled";
		$read_modificar = "readonly";
		$display_modificar = "display:none;";
		$label_submit = "Modificar";
		$clkCancelar = "document.getElementById('frmentrada').submit();";
		$focus = "Documento";
	}
	
	elseif ($opcion == "ver") {
		$titulo = "Ver Registro";
		$disabled_nuevo = "disabled";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$read_modificar = "readonly";
		$display_modificar = "display:none;";
		$display_ver = "display:none;";
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

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=formato_contrato_lista" method="POST" enctype="multipart/form-data" onsubmit="return form(this, '<?=$accion?>');" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
<input type="hidden" name="fTipoContrato" id="fTipoContrato" value="<?=$fTipoContrato?>" />
<input type="hidden" name="RutaPlantAnterior" id="RutaPlantAnterior" value="<?=$field['RutaPlant']?>" />

<table width="<?=$_width?>" class="tblForm">
	<tr>
    	<td colspan="2" class="divFormCaption">Datos del Registro</td>
    </tr>
	<tr>
		<td class="tagForm">* Formato:</td>
		<td>
        	<input type="text" name="CodFormato" id="CodFormato" value="<?=$field['CodFormato']?>" style="width:50px;" maxlength="2" class="<?=$disabled_modificar?>" <?=$read_modificar?> />
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Tipo de Contrato:</td>
		<td>
            <select id="TipoContrato" style="width:150px;" <?=$disabled_ver?>>
                <option value="">&nbsp;</option>
                <?=loadSelect2("rh_tipocontrato", "TipoContrato", "Descripcion", $field['TipoContrato'], 0)?>
            </select>
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Descripci&oacute;n:</td>
		<td>
        	<input type="text" id="Documento" value="<?=$field['Documento']?>" style="width:315px;" maxlength="100" <?=$disabled_ver?> />
		</td>
	</tr>
	<tr>
		<td class="tagForm">Plantilla:</td>
		<td>
        	<input type="file" name="RutaPlant" id="RutaPlant" onChange="$('#txtRutaPlant').val($(this).val());" style="display:none;" />
        	<input type="text" name="txtRutaPlant" id="txtRutaPlant" value="<?=$field['RutaPlant']?>" style="width:315px;" class="disabled" readonly />
            <a href="#" onClick="$('#RutaPlant').click();" style=" <?=$display_ver?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
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
	if ($("#CodFormato").val().trim() == "" || $("#Documento").val().trim() == "") error = "Debe llenar los campos obligatorios";
	
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
			data: "modulo=formato_contrato&accion="+accion+"&"+post,
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