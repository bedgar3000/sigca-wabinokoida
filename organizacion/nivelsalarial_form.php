<?php
if ($opcion == "nuevo") {
	$field['Estado'] = 'A';
	##
	$_titulo = "Grados Salariales / Nuevo Registro";
	$accion = "nuevo";
	$disabled_modificar = "";
	$disabled_ver = "";
	$display_submit = "";
	$label_submit = "Guardar";
	$focus = "Grado";
}
elseif ($opcion == "modificar" || $opcion == "ver") {
	##	consulto datos generales
	$sql = "SELECT *
			FROM rh_nivelsalarial
			WHERE CodNivel = '".$sel_registros."'";
	$field = getRecord($sql);
	##
	if ($opcion == "modificar") {
		$_titulo = "Grados Salariales / Modificar Registro";
		$accion = "modificar";
		$disabled_ver = "";
		$disabled_modificar = "disabled";
		$display_submit = "";
		$label_submit = "Modificar";
		$focus = "Descripcion";
	}
	##
	elseif ($opcion == "ver") {
		$_titulo = "Grados Salariales / Ver Registro";
		$accion = "";
		$disabled_ver = "disabled";
		$disabled_modificar = "disabled";
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

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=nivelsalarial_lista" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('nivelsalarial_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
<input type="hidden" name="fCategoriaCargo" id="fCategoriaCargo" value="<?=$fCategoriaCargo?>" />
<input type="hidden" name="CodNivel" id="CodNivel" value="<?=$field['CodNivel']?>" />

<table width="<?=$_width?>" class="tblForm">
	<tr>
    	<td colspan="2" class="divFormCaption">Datos Generales</td>
    </tr>
    <tr>
		<td class="tagForm">* Grado:</td>
		<td>
            <select name="CategoriaCargo" id="CategoriaCargo" style="width:182px;" <?=$disabled_modificar?>>
                <?=getMiscelaneos($field['CategoriaCargo'], "CATCARGO")?>
            </select>
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Grado:</td>
		<td>
        	<input type="text" name="Grado" id="Grado" value="<?=$field['Grado']?>" style="width:50px;" maxlength="2" <?=$disabled_modificar?> />
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Paso:</td>
		<td>
        	<input type="text" name="Paso" id="Paso" value="<?=$field['Paso']?>" style="width:50px;" maxlength="2" <?=$disabled_modificar?> />
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Descripci&oacute;n:</td>
		<td>
        	<input type="text" name="Descripcion" id="Descripcion" value="<?=$field['Descripcion']?>" style="width:300px;" maxlength="45" <?=$disabled_ver?> />
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Sueldo M&iacute;nimo:</td>
		<td>
        	<input type="text" name="SueldoMinimo" id="SueldoMinimo" value="<?=number_format($field['SueldoMinimo'],2,',','.')?>" style="width:100px; text-align:right;" class="currency" <?=$disabled_ver?> onchange="$('#SueldoMaximo').val(this.value); setSueldoPromedio();" />
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Sueldo M&aacute;ximo:</td>
		<td>
        	<input type="text" name="SueldoMaximo" id="SueldoMaximo" value="<?=number_format($field['SueldoMaximo'],2,',','.')?>" style="width:100px; text-align:right;" class="currency" <?=$disabled_ver?> onchange="setSueldoPromedio();" />
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Sueldo Promedio:</td>
		<td>
        	<input type="text" name="SueldoPromedio" id="SueldoPromedio" value="<?=number_format($field['SueldoPromedio'],2,',','.')?>" style="width:100px; text-align:right;" readonly />
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

<script type="text/javascript">
	function setSueldoPromedio() {
		var SueldoMinimo = setNumero($('#SueldoMinimo').val());
		var SueldoMaximo = setNumero($('#SueldoMaximo').val());
		var SueldoPromedio = (SueldoMinimo + SueldoMaximo) / 2;
		$('#SueldoPromedio').val(SueldoPromedio).formatCurrency();
	}
</script>