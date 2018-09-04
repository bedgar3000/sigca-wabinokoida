<?php
if ($opcion == "nuevo") {
	$field['Estado'] = 'A';
	##
	$_titulo = "Sub-Familias / Nuevo Registro";
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
	list($CodLinea, $CodFamilia, $CodSubFamilia) = explode('_', $sel_registros);
	##	consulto datos generales
	$sql = "SELECT *
			FROM lg_clasesubfamilia
			WHERE
				CodLinea = '$CodLinea'
				AND CodFamilia = '$CodFamilia'
				AND CodSubFamilia = '$CodSubFamilia'";
	$field = getRecord($sql);
	##
	if ($opcion == "modificar") {
		$_titulo = "Sub-Familias / Modificar Registro";
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
		$_titulo = "Sub-Familias / Ver Registro";
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
$_width = 600;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lg_clasesubfamilia_lista" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('lg_clasesubfamilia_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
	<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
	<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
	<input type="hidden" name="fCodLinea" id="fCodLinea" value="<?=$fCodLinea?>" />
	<input type="hidden" name="sel_registros" id="sel_registros" value="<?=$sel_registros?>" />

	<table style="width:100%; max-width:<?=$_width?>px;" class="tblForm">
		<tr>
	    	<td colspan="4" class="divFormCaption">DATOS GENERALES</td>
	    </tr>
	    <tr>
			<td class="tagForm" width="125">* C&oacute;digo:</td>
			<td colspan="3">
	        	<input type="text" name="CodSubFamilia" id="CodSubFamilia" value="<?=$field['CodSubFamilia']?>" style="width:75px; font-weight:bold;" maxlength="6" readonly />
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Linea:</td>
			<td colspan="3">
				<select name="CodLinea" id="CodLinea" style="width:95%;" <?=$disabled_modificar?> onChange="loadSelect($('#CodFamilia'), 'tabla=lg_clasefamilia&CodLinea='+$('#CodLinea').val(), 1, ['CodFamilia']);">
					<option value="">&nbsp;</option>
					<?=loadSelect2('lg_claselinea','CodLinea','Descripcion',$field['CodLinea'],10)?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Familia:</td>
			<td colspan="3">
				<select name="CodFamilia" id="CodFamilia" style="width:95%;" <?=$disabled_modificar?>>
					<option value="">&nbsp;</option>
					<?=loadSelect2('lg_clasefamilia','CodFamilia','Descripcion',$field['CodFamilia'],10,['CodLinea'],[$CodLinea])?>
				</select>
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Descripci&oacute;n:</td>
			<td colspan="3">
	        	<input type="text" name="Descripcion" id="Descripcion" value="<?=$field['Descripcion']?>" style="width:95%;" maxlength="255" <?=$disabled_ver?> />
			</td>
		</tr>
		<tr>
			<td class="tagForm">Estado:</td>
			<td colspan="3">
	            <input type="radio" name="Estado" id="Activo" value="A" <?=chkOpt($field['Estado'], "A");?> <?=$disabled_ver?> /> Activo
	            &nbsp; &nbsp;
	            <input type="radio" name="Estado" id="Inactivo" value="I" <?=chkOpt($field['Estado'], "I");?> <?=$disabled_ver?> /> Inactivo
			</td>
		</tr>
	    <tr>
			<td class="tagForm">&Uacute;ltima Modif.:</td>
			<td colspan="3">
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