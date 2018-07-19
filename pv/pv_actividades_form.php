<?php
if ($opcion == "nuevo") {
	$field['Estado'] = 'A';
	##
	$_titulo = "Nuevo Registro";
	$accion = "nuevo";
	$disabled_modificar = "";
	$disabled_ver = "";
	$display_submit = "";
	$label_submit = "Guardar";
	$focus = "CodActividad";
}
elseif ($opcion == "modificar" || $opcion == "ver") {
	##	consulto datos generales
	$sql = "SELECT
				a.*,
				py.IdSubPrograma,
				sp.IdPrograma,
				p.IdSubSector,
				ss.CodSector
			FROM pv_actividades a
			INNER JOIN pv_proyectos py ON (py.IdProyecto = a.IdProyecto)
			INNER JOIN pv_subprogramas sp ON (sp.IdSubPrograma = py.IdSubPrograma)
			INNER JOIN pv_programas p ON (p.IdPrograma = sp.IdPrograma)
			INNER JOIN pv_subsector ss ON (ss.IdSubSector = p.IdSubSector)
			WHERE a.IdActividad = '".$sel_registros."'";
	$field = getRecord($sql);
	##
	if ($opcion == "modificar") {
		$_titulo = "Modificar Registro";
		$accion = "modificar";
		$disabled_modificar = "disabled";
		$disabled_ver = "";
		$display_submit = "";
		$label_submit = "Modificar";
		$focus = "Denominacion";
	}
	##
	elseif ($opcion == "ver") {
		$_titulo = "Ver Registro";
		$accion = "";
		$disabled_modificar = "disabled";
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

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=pv_actividades_lista" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('pv_actividades_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
<input type="hidden" name="fCodSector" id="fCodSector" value="<?=$fCodSector?>" />
<input type="hidden" name="fIdSubSector" id="fIdSubSector" value="<?=$fIdSubSector?>" />
<input type="hidden" name="fIdPrograma" id="fIdPrograma" value="<?=$fIdPrograma?>" />
<input type="hidden" name="fIdSubPrograma" id="fIdSubPrograma" value="<?=$fIdSubPrograma?>" />
<input type="hidden" name="fIdProyecto" id="fIdProyecto" value="<?=$fIdProyecto?>" />

<table width="<?=$_width?>" class="tblForm">
	<tr>
    	<td colspan="2" class="divFormCaption">Datos Generales</td>
    </tr>
    <tr>
		<td class="tagForm" width="125">Id.:</td>
		<td>
        	<input type="text" name="IdActividad" id="IdActividad" value="<?=$field['IdActividad']?>" style="width:65px; font-weight:bold;" readonly="readonly" />
		</td>
	</tr>
    <tr>
		<td class="tagForm">* C&oacute;digo:</td>
		<td>
        	<input type="text" name="CodActividad" id="CodActividad" value="<?=$field['CodActividad']?>" style="width:65px;" maxlength="2" <?=$disabled_modificar?> />
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Sub-Sector:</td>
		<td>
			<select name="IdSubSector" id="IdSubSector" style="width:404px;" <?=$disabled_modificar?> onChange="loadSelect($('#IdPrograma'), 'tabla=pv_programas&IdSubSector='+$('#IdSubSector').val(), 1, ['IdSubPrograma','IdProyecto']);">
				<option value="">&nbsp;</option>
				<?=loadSelect2('pv_subsector','IdSubSector','Denominacion',$field['IdSubSector'],0,NULL,NULL,'CodClaSectorial')?>
			</select>
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Programa:</td>
		<td>
			<select name="IdPrograma" id="IdPrograma" style="width:404px;" <?=$disabled_modificar?> onChange="loadSelect($('#IdSubPrograma'), 'tabla=pv_subprogramas&IdPrograma='+$('#IdPrograma').val(), 1, ['IdProyecto']);">
				<option value="">&nbsp;</option>
				<?=loadSelect2('pv_programas','IdPrograma','Denominacion',$field['IdPrograma'],0,['IdSubSector'],[$field['IdSubSector']],'CodPrograma')?>
			</select>
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Sub-Programa:</td>
		<td>
			<select name="IdSubPrograma" id="IdSubPrograma" style="width:404px;" <?=$disabled_modificar?> onChange="loadSelect($('#IdProyecto'), 'tabla=pv_proyectos&IdSubPrograma='+$('#IdSubPrograma').val(), 1);">
				<option value="">&nbsp;</option>
				<?=loadSelect2('pv_subprogramas','IdSubPrograma','Denominacion',$field['IdSubPrograma'],0,['IdPrograma'],[$field['IdPrograma']],'CodSubPrograma')?>
			</select>
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Proyecto:</td>
		<td>
			<select name="IdProyecto" id="IdProyecto" style="width:404px;" <?=$disabled_modificar?>>
				<option value="">&nbsp;</option>
				<?=loadSelect2('pv_proyectos','IdProyecto','Denominacion',$field['IdProyecto'],0,['IdSubPrograma'],[$field['IdSubPrograma']],'CodProyecto')?>
			</select>
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Denominaci&oacute;n:</td>
		<td>
        	<input type="text" name="Denominacion" id="Denominacion" value="<?=$field['Denominacion']?>" style="width:398px;" <?=$disabled_ver?> />
		</td>
	</tr>
	<tr>
		<td class="tagForm">&nbsp;</td>
		<td>
            <input type="checkbox" name="FlagObra" id="FlagObra" value="S" <?=chkOpt($field['FlagObra'], "S");?> <?=$disabled_ver?> /> Obra
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