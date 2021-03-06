<?php
if ($opcion == "nuevo") {
	$field_form['CodPais'] = $_PARAMETRO['PAISDEFAULT'];
	$field_form['CodEstado'] = $_PARAMETRO['ESTADODEFAULT'];
	##	
	$accion = "nuevo";
	$titulo = "Nuevo Registro";
	$cancelar = "document.getElementById('frmentrada').submit();";
	$flagactivo = "checked";
	$flagtitulo = "checked";
}
elseif ($opcion == "modificar" || $opcion == "ver") {
	//	consulto datos generales	
	$sql = "SELECT
				m.*,
				e.CodPais
			FROM mastmunicipios m
			INNER JOIN mastestados e ON (e.CodEstado = m.CodEstado)
			WHERE m.CodMunicipio = '".$registro."'";
	$query_form = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query_form)) $field_form = mysql_fetch_array($query_form);
	
	if ($opcion == "modificar") {
		$accion = "modificar";
		$titulo = "Modificar Registro";
		$cancelar = "document.getElementById('frmentrada').submit();";
		$disabled_modificar = "disabled";
	}
	
	elseif ($opcion == "ver") {
		$disabled_ver = "disabled";
		$disabled_modificar = "disabled";
		$titulo = "Ver Registro";
		$cancelar = "window.close();";
		$display_submit = "display:none;";
	}
	
	if ($field_form['Estado'] == "A") $flagactivo = "checked"; else $flaginactivo = "checked";
}
//	------------------------------------
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$cancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=municipios_lista" method="POST" onsubmit="return municipios(this, '<?=$accion?>');">
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="fedoreg" id="fedoreg" value="<?=$fedoreg?>" />
<input type="hidden" name="fordenar" id="fordenar" value="<?=$fordenar?>" />
<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
<input type="hidden" name="festado" id="festado" value="<?=$festado?>" />

<table width="700" class="tblForm">
	<tr>
		<td class="tagForm">C&oacute;digo:</td>
		<td>
        	<input type="text" id="CodMunicipio" value="<?=$field_form['CodMunicipio']?>" style="width:110px;" class="codigo" disabled="disabled" />
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Descripci&oacute;n:</td>
		<td>
        	<input type="text" id="Municipio" style="width:95%;" maxlength="100" value="<?=($field_form['Municipio'])?>" <?=$disabled_ver?> />
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Pais:</td>
		<td>
            <select name="CodPais" id="CodPais" style="width:250px;" <?=$disabled_ver?> onChange="loadSelect($('#CodEstado'), 'tabla=mastestados&CodPais='+$(this).val(), 1);">
                <?=loadSelect2("mastpaises", "CodPais", "Pais", $field_form['CodPais'])?>
            </select>
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Estado:</td>
		<td>
            <select name="CodEstado" id="CodEstado" style="width:250px;" <?=$disabled_ver?>>
                <?=loadSelect2("mastestados", "CodEstado", "Estado", $field_form['CodEstado'], 0, ['CodPais'], [$field_form['CodPais']])?>
            </select>
		</td>
	</tr>
	<tr>
		<td class="tagForm">Estado:</td>
		<td>
            <input type="radio" name="Estado" id="activo" value="A" <?=$flagactivo?> <?=$disabled_ver?> /> Activo
            <input type="radio" name="Estado" id="inactivo" value="I" <?=$flaginactivo?> <?=$disabled_ver?> /> Inactivo
		</td>
	</tr>
	<tr>
		<td class="tagForm">&Uacute;ltima Modif.:</td>
		<td>
			<input type="text" size="30" value="<?=$field_form['UltimoUsuario']?>" disabled="disabled" />
			<input type="text" size="25" value="<?=$field_form['UltimaFecha']?>" disabled="disabled" />
		</td>
	</tr>
</table>
<center>
<input type="submit" value="Guardar" style="width:80px; <?=$display_submit?>" />
<input type="button" value="Cancelar" style="width:80px;" onclick="<?=$cancelar?>" />
</center>
<br />
<div style="width:700px; <?=$display_submit?>" class="divMsj">(*) Campos Obligatorios</div>
</form>

<!-- JS	-->
<script type="text/javascript" charset="utf-8">
$(document).ready(function() {
	$("#Municipio").focus();
});
</script>