<?php
if ($opcion == "nuevo") {
	$field['Estado'] = 'A';
	##
	$_titulo = "Establecimientos Fiscales / Nuevo Registro";
	$accion = "nuevo";
	$disabled_modificar = "";
	$disabled_ver = "";
	$read_modificar = "";
	$display_ver = "";
	$display_submit = "";
	$label_submit = "Guardar";
	$focus = "Descripcion";
}
elseif ($opcion == "modificar" || $opcion == "ver") {
	##	consulto datos generales
	$sql = "SELECT *
			FROM co_establecimientofiscal
			WHERE CodEstablecimiento = '$sel_registros'";
	$field = getRecord($sql);
	##
	if ($opcion == "modificar") {
		$_titulo = "Establecimientos Fiscales / Modificar Registro";
		$accion = "modificar";
		$disabled_modificar = "disabled";
		$disabled_ver = "";
		$read_modificar = "readonly";
		$display_ver = "";
		$display_submit = "";
		$label_submit = "Modificar";
		$focus = "Descripcion";
	}
	##
	elseif ($opcion == "ver") {
		$_titulo = "Establecimientos Fiscales / Ver Registro";
		$accion = "";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$read_modificar = "readonly";
		$display_ver = "display:none;";
		$display_submit = "display:none;";
		$label_submit = "";
		$focus = "btCancelar";
	}
}
$clkCancelar = "document.getElementById('frmentrada').submit();";
//	------------------------------------
$_width = 450;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=co_establecimientofiscal_lista" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('co_establecimientofiscal_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
	<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
	<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />

	<table style="width:100%; max-width:<?=$_width?>px;" class="tblForm">
		<tr>
	    	<td colspan="2" class="divFormCaption">Datos Generales</td>
	    </tr>
	    <tr>
			<td class="tagForm" width="100">C&oacute;digo:</td>
			<td>
	        	<input type="text" name="CodEstablecimiento" id="CodEstablecimiento" value="<?=$field['CodEstablecimiento']?>" style="width:100px; font-weight:bold;" readonly />
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Organismo:</td>
			<td>
				<select name="CodOrganismo" id="CodOrganismo" style="width:295px;" <?=$disabled_modificar?>>
					<?=getOrganismos($field['CodOrganismo'], 3)?>
				</select>
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Descripci&oacute;n:</td>
			<td>
	        	<input type="text" name="Descripcion" id="Descripcion" value="<?=$field['Descripcion']?>" style="width:295px;" maxlength="255" <?=$disabled_ver?> />
			</td>
		</tr>
	    <tr>
			<td class="tagForm">Almacén:</td>
			<td>
				<select name="CodAlmacen" id="CodAlmacen" style="width:295px;" <?=$disabled_ver?> onchange="set_direccion_almacen();">
					<option value="">&nbsp;</option>
					<?=loadSelect2('lg_almacenmast','CodAlmacen','Descripcion',$field['CodAlmacen'],0)?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="tagForm">Dirección:</td>
			<td>
				<textarea name="Direccion" id="Direccion" style="width:295px;" rows="5" <?=$disabled_ver?>><?=$field['Direccion']?></textarea>
			</td>
		</tr>
		<tr>
			<td class="tagForm">Estado:</td>
			<td>
	            <input type="radio" name="Estado" id="Activo" value="A" <?=chkOpt($field['Estado'], "A");?> <?=$disabled_ver?> /> Activo
	            &nbsp; &nbsp;
	            <input type="radio" name="Estado" id="Inactivo" value="I" <?=chkOpt($field['Estado'], "I");?> <?=$disabled_ver?> /> Inactivo
			</td>
		</tr>
	    <tr>
			<td class="tagForm">&Uacute;ltima Modif.:</td>
			<td>
				<input type="text" value="<?=$field['UltimoUsuario']?>" style="width:145px;" disabled="disabled" />
				<input type="text" value="<?=$field['UltimaFecha']?>" style="width:145px" disabled="disabled" />
			</td>
		</tr>
	</table>

	<center>
		<input type="submit" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" id="btSubmit" />
		<input type="button" value="Cancelar" style="width:75px;" id="btCancelar" onclick="<?=$clkCancelar?>" />
	</center>
</form>
<div style="width:100%; max-width:<?=$_width?>px;" class="divMsj">Campos Obligatorios *</div>

<script type="text/javascript">
	function set_direccion_almacen() {
		$.post('co_establecimientofiscal_ajax.php', 'modulo=ajax&accion=set_direccion_almacen&'+$('form').serialize(), function(data) {
			$('#Direccion').val(data['Direccion']);
	    },'json');
	}
</script>