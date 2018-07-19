<?php
if ($opcion == "nuevo") {
	$field['CodOrganismo'] = (!empty($fCodOrganismo) ? $fCodOrganismo : $_SESSION["ORGANISMO_ACTUAL"]);
	$field['Estado'] = 'A';
	##
	$_titulo = "Maestro de Correlativo / Nuevo Registro";
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
			FROM co_correlativodocumento
			WHERE CodCorrelativo = '$sel_registros'";
	$field = getRecord($sql);
	##
	if ($opcion == "modificar") {
		$_titulo = "Maestro de Correlativo / Modificar Registro";
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
		$_titulo = "Maestro de Correlativo / Ver Registro";
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
$_width = 500;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=co_correlativodocumento_lista" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('co_correlativodocumento_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
	<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
	<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
	<input type="hidden" name="CodCorrelativo" id="CodCorrelativo" value="<?=$field['CodCorrelativo']?>" />

	<table style="width:100%; max-width:<?=$_width?>px;" class="tblForm">
		<tr>
	    	<td colspan="4" class="divFormCaption">Datos Generales</td>
	    </tr>
	    <tr>
			<td class="tagForm">* Organismo:</td>
			<td colspan="3">
				<select name="CodOrganismo" id="CodOrganismo" style="width:95%;" <?=$disabled_modificar?>>
					<?=getOrganismos($field['CodOrganismo'], 3)?>
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
			<td class="tagForm">* Tipo de Documento:</td>
			<td colspan="3">
				<select name="CodTipoDocumento" id="CodTipoDocumento" style="width:95%;" <?=$disabled_modificar?>>
					<option value="">&nbsp;</option>
					<?=loadSelect2('co_tipodocumento','CodTipoDocumento','Descripcion',$field['CodTipoDocumento'])?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="tagForm">Nro. Serie:</td>
			<td>
				<select name="CodSerie" id="CodSerie" style="width:100px;" <?=$disabled_modificar?>>
					<option value="">&nbsp;</option>
					<?=loadSelect2('co_seriefiscal','CodSerie','NroSerie',$field['CodSerie'])?>
				</select>
			</td>
			<td class="tagForm">* Nro. Actual:</td>
			<td>
	        	<input type="text" name="UltNroEmitido" id="UltNroEmitido" value="<?=$field['UltNroEmitido']?>" style="width:100px; text-align: right;" maxlength="10" <?=$disabled_ver?> />
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Rango Desde:</td>
			<td>
	        	<input type="text" name="NroDesde" id="NroDesde" value="<?=$field['NroDesde']?>" style="width:100px; text-align: right;" maxlength="10" <?=$disabled_ver?> />
			</td>
			<td class="tagForm">* Rango Hasta:</td>
			<td>
	        	<input type="text" name="NroHasta" id="NroHasta" value="<?=$field['NroHasta']?>" style="width:100px; text-align: right;" maxlength="10" <?=$disabled_ver?> />
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