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
	$_titulo = "Metas / Nuevo Registro";
	$accion = "nuevo";
	$disabled_modificar = "";
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
				mp.*,
				op.CategoriaProg,
				ue.Denominacion AS UnidadEjecutora,
				p.NomCompleto AS NomResponsable
			FROM
				pv_metaspoa mp
				INNER JOIN pv_objetivospoa op ON (op.CodObjetivo = mp.CodObjetivo)
				INNER JOIN pv_categoriaprog cp ON (cp.CategoriaProg = op.CategoriaProg)
				INNER JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cp.CodUnidadEjec)
				LEFT JOIN mastpersonas p ON (p.CodPersona = op.Responsable)
			WHERE mp.CodMeta = '".$sel_registros."'";
	$field = getRecord($sql);
	##
	if ($opcion == "modificar") {
		$_titulo = "Metas / Modificar Registro";
		$accion = "modificar";
		$disabled_modificar = "disabled";
		$disabled_ver = "";
		$display_modificar = "display:none;";
		$display_ver = "";
		$display_submit = "";
		$label_submit = "Modificar";
		$focus = "Descripcion";
	}
	##
	elseif ($opcion == "ver") {
		$_titulo = "Metas / Ver Registro";
		$accion = "";
		$disabled_modificar = "disabled";
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

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=pv_metaspoa_lista" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('pv_metaspoa_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
<input type="hidden" name="fCategoriaProg" id="fCategoriaProg" value="<?=$fCategoriaProg?>" />
<input type="hidden" name="fCodObjetivo" id="fCodObjetivo" value="<?=$fCodObjetivo?>" />
<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
<input type="hidden" name="CodMeta" id="CodMeta" value="<?=$field['CodMeta']?>" />

<table width="<?=$_width?>" class="tblForm">
	<tr>
    	<td colspan="2" class="divFormCaption">Datos Generales</td>
    </tr>
    <tr>
		<td class="tagForm" width="150">Nro. Meta:</td>
		<td>
        	<input type="text" name="NroMeta" id="NroMeta" value="<?=$field['NroMeta']?>" style="width:100px; font-weight:bold;" readonly="readonly" />
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Cat. Program&aacute;tica:</td>
		<td class="gallery clearfix">
			<input type="text" name="CategoriaProg" id="CategoriaProg" value="<?=$field['CategoriaProg']?>" style="width:100px;" readonly="readonly" />
			<input type="text" name="UnidadEjecutora" id="UnidadEjecutora" value="<?=$field['UnidadEjecutora']?>" style="width:292px;" disabled />
			<a href="../lib/listas/gehen.php?anz=lista_pv_categoriaprog&filtrar=default&ventana=pv_metas&campo1=CategoriaProg&campo2=UnidadEjecutora&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" id="aCategoriaProg" style=" <?=$display_modificar?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Nro. Objetivo:</td>
		<td>
			<select name="CodObjetivo" id="CodObjetivo" style="width:100px;" <?=$disabled_modificar?>>
				<option value="">&nbsp;</option>
				<?=loadSelect2('pv_objetivospoa','CodObjetivo','NroObjetivo',$field['CodObjetivo'],0,['CategoriaProg'],[$field['CategoriaProg']])?>
			</select>
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Descripci&oacute;n:</td>
		<td>
        	<textarea name="Descripcion" id="Descripcion" style="width:95%; height:50px;" <?=$disabled_ver?>><?=$field['Descripcion']?></textarea>
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Medios de Verificaci&oacute;n:</td>
		<td>
        	<input type="text" name="MedioVerificacion1" id="MedioVerificacion1" value="<?=$field['MedioVerificacion1']?>" style="width:95%;" <?=$disabled_ver?>>
		</td>
	</tr>
    <tr>
		<td>&nbsp;</td>
		<td>
        	<input type="text" name="MedioVerificacion2" id="MedioVerificacion2" value="<?=$field['MedioVerificacion2']?>" style="width:95%;" <?=$disabled_ver?>>
		</td>
	</tr>
	<tr>
		<td class="tagForm">Indicadores:</td>
		<td>
			<?php $detalle = "indicadores"; ?>
			<input type="hidden" id="sel_<?=$detalle?>" />
			<table width="98%">
			    <tbody>
				    <tr>
				        <td align="right" class="gallery clearfix">
				            <input type="button" style="font-weight:bold; width:30px;" value="+" onclick="insertar2(this, '<?=$detalle?>', 'modulo=ajax&accion=insertar_<?=$detalle?>', 'pv_metaspoa_ajax.php');" <?=$disabled_ver?> />
				            <input type="button" style="font-weight:bold; width:30px;" value="-" onclick="quitar(this, '<?=$detalle?>');" <?=$disabled_ver?> />
				            &nbsp; 
				        </td>
				    </tr>
			    </tbody>
			</table>
			<div style="overflow:scroll; height:80px; min-width:300px; width:98%; margin:auto;">
				<table class="tblLista" style="width:100%; min-width:300px;">
					<tbody id="lista_<?=$detalle?>">
					<?php
					$i = 0;
					$sql = "SELECT * FROM pv_metaspoaindicadores WHERE CodMeta = '".$field['CodMeta']."'";
					$field_indicadores = getRecords($sql);
					foreach ($field_indicadores as $f) {
						$id = ++$i;
						?>
						<tr class="trListaBody" id="<?=$detalle?>_<?=$id?>" onclick="clk($(this), '<?=$detalle?>', '<?=$detalle?>_<?=$id?>');">
							<th width="15"><?=$id?></th>
							<td><input type="text" name="<?=$detalle?>_Descripcion[]" value="<?=$f['Descripcion']?>" class="cell" /></td>
						</tr>
						<?php
					}
					?>
					</tbody>
				</table>
			</div>
			<input type="hidden" id="nro_<?=$detalle?>" value="<?=$i?>" />
			<input type="hidden" id="can_<?=$detalle?>" value="<?=$i?>" />
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