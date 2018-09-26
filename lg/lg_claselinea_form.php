<?php
if ($opcion == "nuevo") {
	$field['Estado'] = 'A';
	##
	$_titulo = "Lineas / Nuevo Registro";
	$accion = "nuevo";
	$disabled_modificar = "";
	$disabled_ver = "";
	$read_modificar = "";
	$display_modificar = "";
	$display_ver = "";
	$display_submit = "";
	$label_submit = "Guardar";
	$focus = "Descripcion";
}
elseif ($opcion == "modificar" || $opcion == "ver") {
	##	consulto datos generales
	$sql = "SELECT *
			FROM lg_claselinea
			WHERE CodLinea = '$sel_registros'";
	$field = getRecord($sql);
	##
	if ($opcion == "modificar") {
		$_titulo = "Lineas / Modificar Registro";
		$accion = "modificar";
		$disabled_modificar = "disabled";
		$disabled_ver = "";
		$read_modificar = "readonly";
		$display_modificar = "display:none;";
		$display_ver = "";
		$display_submit = "";
		$label_submit = "Modificar";
		$focus = "Descripcion";
	}
	##
	elseif ($opcion == "ver") {
		$_titulo = "Lineas / Ver Registro";
		$accion = "";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$read_modificar = "readonly";
		$display_modificar = "display:none;";
		$display_ver = "display:none;";
		$display_submit = "display:none;";
		$label_submit = "";
		$focus = "btCancelar";
	}
}
$clkCancelar = "document.getElementById('frmentrada').submit();";
//	------------------------------------
$_width = 500;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lg_claselinea_lista" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('lg_claselinea_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
	<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
	<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />

	<table style="width:100%; max-width:<?=$_width?>px;" class="tblForm">
		<tr>
	    	<td colspan="4" class="divFormCaption">Datos Generales</td>
	    </tr>
	    <tr>
			<td class="tagForm">* C&oacute;digo:</td>
			<td>
	        	<input type="text" name="CodLinea" id="CodLinea" value="<?=$field['CodLinea']?>" style="width:75px; font-weight:bold;" maxlength="6" readonly />
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Descripci&oacute;n:</td>
			<td>
	        	<input type="text" name="Descripcion" id="Descripcion" value="<?=$field['Descripcion']?>" style="width:95%;" maxlength="255" <?=$disabled_ver?> />
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