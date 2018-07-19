<?php
if ($opcion == "nuevo") {
	$field['Estado'] = 'A';
	$field['CodPais'] = $_PARAMETRO['PAISDEFAULT'];
	$field['CodEstado'] = $_PARAMETRO['ESTADODEFAULT'];
	$field['CodMunicipio'] = $_PARAMETRO['MUNICIPIODEFAULT'];
	$field['CodCiudad'] = $_PARAMETRO['CIUDADDEFAULT'];
	##
	$_titulo = "Nuevo Registro";
	$accion = "nuevo";
	$disabled_ver = "";
	$display_submit = "";
	$label_submit = "Guardar";
	$focus = "CodParroquia";
}
elseif ($opcion == "modificar" || $opcion == "ver") {
	##	consulto datos generales
	$sql = "SELECT
				c.*,
				cm.CodParroquia,
				pr.CodMunicipio,
				m.CodEstado,
				e.CodPais
			FROM
				rh_cuadrillas c
				INNER JOIN mastcomunidades cm ON (cm.CodComunidad = c.CodComunidad)
				INNER JOIN mastparroquias pr ON (pr.CodParroquia = cm.CodParroquia)
				INNER JOIN mastmunicipios m ON (m.CodMunicipio = pr.CodMunicipio)
				INNER JOIN mastestados e ON (e.CodEstado = m.CodEstado)
			WHERE c.CodCuadrilla = '".$sel_registros."'";
	$field = getRecord($sql);
	##
	if ($opcion == "modificar") {
		$_titulo = "Modificar Registro";
		$accion = "modificar";
		$disabled_ver = "";
		$display_submit = "";
		$label_submit = "Modificar";
		$focus = "Denominacion";
	}
	##
	elseif ($opcion == "ver") {
		$_titulo = "Ver Registro";
		$accion = "";
		$disabled_ver = "disabled";
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

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=cuadrillas_lista" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('cuadrillas_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
<input type="hidden" name="fCodPais" id="fCodPais" value="<?=$fCodPais?>" />
<input type="hidden" name="fCodEstado" id="fCodEstado" value="<?=$fCodEstado?>" />
<input type="hidden" name="fCodMunicipio" id="fCodMunicipio" value="<?=$fCodMunicipio?>" />
<input type="hidden" name="fCodCiudad" id="fCodCiudad" value="<?=$fCodCiudad?>" />
<input type="hidden" name="fCodParroquia" id="fCodParroquia" value="<?=$fCodParroquia?>" />
<input type="hidden" name="fCodComunidad" id="fCodComunidad" value="<?=$fCodComunidad?>" />

<table width="<?=$_width?>" class="tblForm">
	<tr>
    	<td colspan="2" class="divFormCaption">Datos Generales</td>
    </tr>
    <tr>
		<td class="tagForm" width="125">C&oacute;digo:</td>
		<td>
        	<input type="text" name="CodCuadrilla" id="CodCuadrilla" value="<?=$field['CodCuadrilla']?>" style="width:65px; font-weight:bold;" readonly="readonly" />
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Pais:</td>
		<td>
			<select name="CodPais" id="CodPais" style="width:250px;" <?=$disabled_ver?> onChange="loadSelect($('#CodEstado'), 'tabla=mastestados&CodPais='+$(this).val(), 1, ['CodMunicipio','CodParroquia','CodComunidad','CodCiudad']);">
				<option value="">&nbsp;</option>
				<?=loadSelect2('mastpaises','CodPais','Pais',$field['CodPais'],0)?>
			</select>
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Estado:</td>
		<td>
			<select name="CodEstado" id="CodEstado" style="width:250px;" <?=$disabled_ver?> onChange="loadSelect($('#CodMunicipio'), 'tabla=mastmunicipios&CodEstado='+$(this).val(), 1, ['CodParroquia','CodComunidad','CodCiudad']);">
				<option value="">&nbsp;</option>
				<?=loadSelect2('mastestados','CodEstado','Estado',$field['CodEstado'],0,['CodPais'],[$field['CodPais']])?>
			</select>
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Municipio:</td>
		<td>
			<select name="CodMunicipio" id="CodMunicipio" style="width:250px;" <?=$disabled_ver?> onChange="loadSelect($('#CodParroquia'), 'tabla=mastparroquias&CodMunicipio='+$(this).val(), 1, ['CodComunidad','CodCiudad']);">
				<option value="">&nbsp;</option>
				<?=loadSelect2('mastmunicipios','CodMunicipio','Municipio',$field['CodMunicipio'],0,['CodEstado'],[$field['CodEstado']])?>
			</select>
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Ciudad:</td>
		<td>
			<select name="CodCiudad" id="CodCiudad" style="width:250px;" <?=$disabled_ver?>>
				<option value="">&nbsp;</option>
				<?=loadSelect2('mastciudades','CodCiudad','Ciudad',$field['CodCiudad'],0,['CodMunicipio'],[$field['CodMunicipio']])?>
			</select>
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Parroquia:</td>
		<td>
			<select name="CodParroquia" id="CodParroquia" style="width:250px;" <?=$disabled_ver?> onChange="loadSelect($('#CodComunidad'), 'tabla=mastcomunidades&CodParroquia='+$(this).val(), 1);">
				<option value="">&nbsp;</option>
				<?=loadSelect2('mastparroquias','CodParroquia','Descripcion',$field['CodParroquia'],0,['CodMunicipio'],[$field['CodMunicipio']])?>
			</select>
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Comunidad:</td>
		<td>
			<select name="CodComunidad" id="CodComunidad" style="width:250px;" <?=$disabled_ver?>>
				<option value="">&nbsp;</option>
				<?=loadSelect2('mastcomunidades','CodComunidad','Descripcion',$field['CodComunidad'],0,['CodParroquia'],[$field['CodParroquia']])?>
			</select>
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Denominaci&oacute;n:</td>
		<td>
        	<input type="text" name="Denominacion" id="Denominacion" value="<?=$field['Denominacion']?>" style="width:398px;" maxlength="100" <?=$disabled_ver?> />
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
			<input type="text" value="<?=$field['UltimoUsuario']?>" style="width:140px;" disabled="disabled" />
			<input type="text" value="<?=$field['UltimaFecha']?>" style="width:110px" disabled="disabled" />
		</td>
	</tr>
</table>

<center>
<input type="submit" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" id="btSubmit" />
<input type="button" value="Cancelar" style="width:75px;" id="btCancelar" onclick="<?=$clkCancelar?>" />
</center>
</form>
<div style="width:<?=$_width?>px" class="divMsj">Campos Obligatorios *</div>