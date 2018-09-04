<?php
if ($opcion == "nuevo") {
	$field['Estado'] = 'A';
	$field['CategoriaProg'] = ($fCategoriaProg?$fCategoriaProg:'');
	$sql = "SELECT ue.Denominacion
			FROM
				pv_categoriaprog cp
				INNER JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cp.CodUnidadEjec)
			WHERE cp.CategoriaProg = '$field[CategoriaProg]'";
	$field['UnidadEjecutora'] = getVar3($sql);
	##
	$_titulo = "Objetivos / Nuevo Registro";
	$accion = "nuevo";
	$disabled_ver = "";
	$display_modificar = "";
	$display_ver = "";
	$display_submit = "";
	$label_submit = "Guardar";
	$focus = "Descripcion";
}
elseif ($opcion == "modificar" || $opcion == "ver") {
	##	consulto datos generales
	$sql = "SELECT
				op.*,
				ue.Denominacion AS UnidadEjecutora,
				p.NomCompleto AS NomResponsable
			FROM
				pv_objetivospoa op
				INNER JOIN pv_categoriaprog cp ON (cp.CategoriaProg = op.CategoriaProg)
				INNER JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cp.CodUnidadEjec)
				LEFT JOIN mastpersonas p ON (p.CodPersona = op.Responsable)
			WHERE op.CodObjetivo = '".$sel_registros."'";
	$field = getRecord($sql);
	##
	if ($opcion == "modificar") {
		$_titulo = "Objetivos / Modificar Registro";
		$accion = "modificar";
		$disabled_ver = "";
		$display_modificar = "display:none;";
		$display_ver = "";
		$display_submit = "";
		$label_submit = "Modificar";
		$focus = "Descripcion";
	}
	##
	elseif ($opcion == "ver") {
		$_titulo = "Objetivos / Ver Registro";
		$accion = "";
		$disabled_ver = "disabled";
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

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=pv_objetivospoa_lista" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('pv_objetivospoa_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
<input type="hidden" name="fCategoriaProg" id="fCategoriaProg" value="<?=$fCategoriaProg?>" />
<input type="hidden" name="CodObjetivo" id="CodObjetivo" value="<?=$field['CodObjetivo']?>" />

<table width="<?=$_width?>" class="tblForm">
	<tr>
    	<td colspan="2" class="divFormCaption">Datos Generales</td>
    </tr>
    <tr>
		<td class="tagForm" width="125">Nro. Objetivo:</td>
		<td>
        	<input type="text" name="NroObjetivo" id="NroObjetivo" value="<?=$field['NroObjetivo']?>" style="width:65px; font-weight:bold;" readonly="readonly" />
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Tipo de Objetivo:</td>
		<td>
			<select name="TipoObjetivo" id="TipoObjetivo" style="width:200px;" <?=$disabled_ver?>>
				<option value="">&nbsp;</option>
				<?=getMiscelaneos($field['TipoObjetivo'], 'PVOBJPOA')?>
			</select>
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Cat. Program&aacute;tica:</td>
		<td class="gallery clearfix">
			<input type="text" name="CategoriaProg" id="CategoriaProg" value="<?=$field['CategoriaProg']?>" style="width:100px;" readonly="readonly" />
			<input type="text" name="UnidadEjecutora" id="UnidadEjecutora" value="<?=$field['UnidadEjecutora']?>" style="width:300px;" disabled />
			<a href="../lib/listas/gehen.php?anz=lista_pv_categoriaprog&filtrar=default&ventana=unidad_ejecutora&campo1=CategoriaProg&campo2=UnidadEjecutora&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" id="aCategoriaProg" style=" <?=$display_modificar?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
		</td>
	</tr>
    <tr>
		<td class="tagForm">Responsable:</td>
		<td class="gallery clearfix">
			<input type="text" name="Responsable" id="Responsable" value="<?=$field['Responsable']?>" style="width:50px;" readonly="readonly" />
			<input type="text" name="NomResponsable" id="NomResponsable" value="<?=$field['NomResponsable']?>" style="width:350px;" readonly="readonly" />
			<a href="../lib/listas/gehen.php?anz=lista_personas&filtrar=default&ventana=&campo1=Responsable&campo2=NomResponsable&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe2]" id="aPersona" style=" <?=$display_ver?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Descripci&oacute;n:</td>
		<td>
        	<textarea name="Descripcion" id="Descripcion" style="width:95%; height:50px;" <?=$disabled_ver?>><?=$field['Descripcion']?></textarea>
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