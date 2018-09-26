<?php
if ($opcion == "nuevo") {
	$field['CodPais'] = $_PARAMETRO["PAISDEFAULT"];
	$field['CodEstado'] = $_PARAMETRO["ESTADODEFAULT"];
	$field['CodMunicipio'] = $_PARAMETRO["MUNICIPIODEFAULT"];
	$field['Estado'] = "A";
	##
	$titulo = "Nuevo Registro";
	$accion = "nuevo";
	$disabled_nuevo = "disabled";
	$disabled_ver = "";
	$display_ver = "";
	$label_submit = "Guardar";
	$focus = "Descripcion";
	$clkCancelar = "document.getElementById('frmentrada').submit();";
}
elseif ($opcion == "modificar" || $opcion == "ver") {
	//	consulto datos generales
	$sql = "SELECT
				c.*,
				pq.CodMunicipio,
				m.CodEstado,
				e.CodPais
			FROM
				mastcomunidades c
				INNER JOIN mastparroquias pq ON (pq.CodParroquia = c.CodParroquia)
				INNER JOIN mastmunicipios m ON (m.CodMunicipio = pq.CodMunicipio)
				INNER JOIN mastestados e ON (e.CodEstado = m.CodEstado)
			WHERE cCodComunidad = '".$sel_registros."'";
	$field = getRecord($sql);
	
	if ($opcion == "modificar") {
		$titulo = "Modificar Registro";
		$accion = "modificar";
		$disabled_modificar = "disabled";
		$display_modificar = "display:none;";
		$label_submit = "Modificar";
		$clkCancelar = "document.getElementById('frmentrada').submit();";
		$focus = "TipCargo";
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

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=comunidades_lista" method="POST" enctype="multipart/form-data" onsubmit="return comunidades(this, '<?=$accion?>');" autocomplete="off">
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
        	<input type="text" id="CodComunidad" value="<?=$field['CodComunidad']?>" style="width:50px;" disabled />
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Pais:</td>
		<td>
        	<select id="CodPais" style="width:300px;" onchange="loadSelect($('#CodEstado'), 'tabla=estado&opcion='+$(this).val(), 1, ['CodEstado','CodMunicipio','CodParroquia']);" <?=$disabled_ver?>>
        		<option value="">&nbsp;</option>
        		<?=loadSelect2('mastpaises','CodPais','Pais',$field['CodPais'])?>
        	</select>
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Estado:</td>
		<td>
        	<select id="CodEstado" style="width:300px;" onchange="loadSelect($('#CodMunicipio'), 'tabla=municipio&opcion='+$(this).val(), 1, ['CodMunicipio','CodParroquia']);" <?=$disabled_ver?>>
        		<option value="">&nbsp;</option>
        		<?=loadSelectDependienteEstado($field['CodEstado'], $field['CodPais'], 0)?>
        	</select>
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Municipio:</td>
		<td>
        	<select id="CodMunicipio" style="width:300px;" onchange="loadSelect($('#CodParroquia'), 'tabla=parroquia&opcion='+$(this).val(), 1, ['CodParroquia']);" <?=$disabled_ver?>>
        		<option value="">&nbsp;</option>
        		<?=loadSelect2('mastmunicipios','CodMunicipio','Municipio',$field['CodMunicipio'],0,array('CodEstado'),array($field['CodEstado']))?>
        	</select>
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Parroquia:</td>
		<td>
        	<select id="CodParroquia" style="width:300px;" <?=$disabled_ver?>>
        		<option value="">&nbsp;</option>
        		<?=loadSelect2('mastparroquias','CodParroquia','Descripcion',$field['CodParroquia'],0,array('CodMunicipio'),array($field['CodMunicipio']))?>
        	</select>
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Descripci&oacute;n:</td>
		<td>
        	<input type="text" id="Descripcion" value="<?=$field['Descripcion']?>" style="width:295px;" maxlength="100" <?=$disabled_ver?> />
		</td>
	</tr>
	<tr>
		<td class="tagForm">Status:</td>
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
function comunidades(form, accion) {
	bloqueo(true);
	//	valido
	var error = "";
	if ($("#CodParroquia").val() == "" || $("#Descripcion").val().trim() == "") error = "Debe llenar los campos obligatorios";
	
	//	valido errores
	if (error != "") {
		cajaModal(error, "error", 400);
	} else {
		//	formulario
		var post = getForm(form);
		//	ajax
		$.ajax({
			type: "POST",
			url: "comunidades_ajax.php",
			data: "modulo=comunidades&accion="+accion+"&"+post,
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