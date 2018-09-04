<?php
if ($opcion == "nuevo") {
	$field['Estado'] = "A";
	$field['CodPais'] = $_PARAMETRO["PAISDEFAULT"];
	$field['CodEstado'] = $_PARAMETRO["ESTADODEFAULT"];
	$field['CodMunicipio'] = $_PARAMETRO["MUNICIPIODEFAULT"];
	##
	$titulo = "Nuevo Registro";
	$accion = "nuevo";
	$label_submit = "Guardar";
	$focus = "Descripcion";
	$disabled_nuevo = "disabled";
	$disabled_ver = "";
	$display_submit = "";
}
elseif ($opcion == "modificar" || $opcion == "ver") {
	//	consulto datos generales
	$sql = "SELECT
				pr.*,
				m.CodEstado,
				e.CodPais
			FROM 
				mastparroquias pr
				INNER JOIN mastmunicipios m ON (m.CodMunicipio = pr.CodMunicipio)
				INNER JOIN mastestados e ON (e.CodEstado = m.CodEstado)
			WHERE pr.CodParroquia = '".$sel_registros."'";
	$field = getRecord($sql);
	##
	if ($opcion == "modificar") {
		$titulo = "Modificar Registro";
		$accion = "modificar";
		$label_submit = "Modificar";
		$focus = "Descripcion";
		$disabled_nuevo = "";
		$disabled_ver = "";
		$display_submit = "";
	}
	##
	elseif ($opcion == "ver") {
		$titulo = "Ver Registro";
		$accion = "";
		$label_submit = "";
		$focus = "btCancelar";
		$disabled_nuevo = "disabled";
		$disabled_ver = "disabled";
		$display_submit = "display:none;";
	}
}
$clkCancelar = "document.getElementById('frmentrada').submit();";
//	------------------------------------
$_width = 500;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=parroquias_lista" method="POST" enctype="multipart/form-data" onsubmit="return form(this, '<?=$accion?>');" autocomplete="off">
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
		<td class="tagForm">C&oacute;digo:</td>
		<td>
        	<input type="text" id="CodParroquia" value="<?=$field['CodParroquia']?>" style="width:50px;" disabled />
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Pais:</td>
		<td>
        	<select id="CodPais" style="width:300px;" onchange="loadSelect($('#CodEstado'), 'tabla=estado&opcion='+$(this).val(), 1, ['CodEstado','CodMunicipio']);" <?=$disabled_ver?>>
        		<option value="">&nbsp;</option>
        		<?=loadSelect2('mastpaises','CodPais','Pais',$field['CodPais'])?>
        	</select>
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Estado:</td>
		<td>
        	<select id="CodEstado" style="width:300px;" onchange="loadSelect($('#CodMunicipio'), 'tabla=municipio&opcion='+$(this).val(), 1, ['CodMunicipio']);" <?=$disabled_ver?>>
        		<option value="">&nbsp;</option>
        		<?=loadSelectDependienteEstado($field['CodEstado'], $field['CodPais'], 0)?>
        	</select>
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Municipio:</td>
		<td>
        	<select id="CodMunicipio" style="width:300px;" <?=$disabled_ver?>>
        		<option value="">&nbsp;</option>
        		<?=loadSelect2('mastmunicipios','CodMunicipio','Municipio',$field['CodMunicipio'],0,array('CodEstado'),array($field['CodEstado']))?>
        	</select>
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Descripci&oacute;n:</td>
		<td>
        	<input type="text" id="Descripcion" value="<?=htmlentities($field['Descripcion'])?>" style="width:295px;" maxlength="100" <?=$disabled_ver?> />
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
<input type="button" id="btCancelar" value="Cancelar" style="width:75px;" onclick="<?=$clkCancelar?>" />
</center>

</form>

<div style="width:<?=$_width?>px" class="divMsj">Campos Obligatorios *</div>

<script type="text/javascript" language="javascript">
function form(form, accion) {
	bloqueo(true);
	//	valido
	var error = "";
	if ($("#Descripcion").val().trim() == "" && $("#CodMunicipio").val() == "") error = "Debe llenar los campos obligatorios";
	
	//	valido errores
	if (error != "") {
		cajaModal(error, "error", 400);
	} else {
		//	formulario
		var post = getForm(form);
		//	ajax
		$.ajax({
			type: "POST",
			url: "parroquias_ajax.php",
			data: "modulo=parroquias&accion="+accion+"&"+post,
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