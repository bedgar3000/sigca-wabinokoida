<?php
if ($opcion == "nuevo") {
	$field['Estado'] = 'A';
	$field['FlagVariable'] = 'S';
	$field['AnioFeriado'] = $AnioActual;
	##
	$_titulo = "Nuevo Registro";
	$accion = "nuevo";
	$disabled_ver = "";
	$disabled_anio = "";
	$display_submit = "";
	$label_submit = "Guardar";
	$focus = "DiaFeriado";
}
elseif ($opcion == "modificar" || $opcion == "ver") {
	##	consulto datos generales
	$sql = "SELECT *
			FROM rh_feriados
			WHERE CodFeriado = '".$sel_registros."'";
	$field = getRecord($sql);
	##
	if ($opcion == "modificar") {
		$_titulo = "Modificar Registro";
		$accion = "modificar";
		$disabled_ver = "";
		$disabled_anio = ($field['FlagVariable']=='S')?'':'disabled';
		$display_submit = "";
		$label_submit = "Modificar";
		$focus = "Descripcion";
	}
	##
	elseif ($opcion == "ver") {
		$_titulo = "Ver Registro";
		$accion = "";
		$disabled_ver = "disabled";
		$disabled_anio = "disabled";
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

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=rh_feriados_lista" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('rh_feriados_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
<input type="hidden" name="CodFeriado" id="CodFeriado" value="<?=$field['CodFeriado']?>" />

<table width="<?=$_width?>" class="tblForm">
	<tr>
    	<td colspan="2" class="divFormCaption">Datos Generales</td>
    </tr>
    <tr>
		<td class="tagForm" width="125">* A&ntilde;o:</td>
		<td>
        	<input type="text" name="AnioFeriado" id="AnioFeriado" value="<?=$field['AnioFeriado']?>" style="width:65px; font-weight:bold;" maxlength='4' <?=$disabled_anio?> />
		</td>
	</tr>
    <tr>
		<td class="tagForm">Dia:</td>
		<td>
        	<input type="text" name="DiaFeriado" id="DiaFeriado" value="<?=$field['DiaFeriado']?>" style="width:65px;" maxlength='5' <?=$disabled_ver?> /> (mes-dia) 00-00
		</td>
	</tr>
    <tr>
		<td class="tagForm">Descripci&oacute;n:</td>
		<td>
        	<input type="text" name="Descripcion" id="Descripcion" value="<?=$field['Descripcion']?>" style="width:95%;" maxlength='45' <?=$disabled_ver?> />
		</td>
	</tr>
	<tr>
		<td class="tagForm">&nbsp;</td>
		<td>
            <input type="checkbox" name="FlagVariable" id="FlagVariable" value="S" <?=chkOpt($field['FlagVariable'], "S");?> <?=$disabled_ver?> onclick="$('#AnioFeriado').attr('disabled', !this.checked).val('');" /> Variable todos los a&ntilde;os
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
			<input type="text" value="<?=$field['UltimoUsuario']?>" style="width:150px;" disabled="disabled" />
			<input type="text" value="<?=$field['UltimaFecha']?>" style="width:100px" disabled="disabled" />
		</td>
	</tr>
</table>

<center>
<input type="submit" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" id="btSubmit" />
<input type="button" value="Cancelar" style="width:75px;" id="btCancelar" onclick="<?=$clkCancelar?>" />
</center>
</form>
<div style="width:<?=$_width?>px" class="divMsj">Campos Obligatorios *</div>

<script type="text/javascript" language="javascript">
</script>