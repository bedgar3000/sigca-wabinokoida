<?php
if ($opcion == "nuevo") {
	$field['Estado'] = 'A';
	$field['Aplicacion'] = 'AP';
	##
	$_titulo = "Clasificación de Gastos / Nuevo Registro";
	$accion = "nuevo";
	$disabled_modificar = "";
	$disabled_ver = "";
	$read_modificar = "";
	$display_modificar = "";
	$display_ver = "";
	$display_submit = "";
	$label_submit = "Guardar";
	$focus = "CodClasificacion";
}
elseif ($opcion == "modificar" || $opcion == "ver") {
	##	consulto datos generales
	$sql = "SELECT *
			FROM ap_clasificaciongastos
			WHERE CodClasificacion = '$sel_registros'";
	$field = getRecord($sql);
	##	
	$sql = "SELECT
				ccg.*,
				cg.Descripcion
			FROM ap_conceptoclasificaciongastos ccg
			INNER JOIN ap_conceptogastos cg ON cg.CodConceptoGasto = ccg.CodConceptoGasto
			WHERE ccg.CodClasificacion = '$sel_registros'";
	$field_detalle = getRecords($sql);
	##
	if ($opcion == "modificar") {
		$_titulo = "Clasificación de Gastos / Modificar Registro";
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
		$_titulo = "Clasificación de Gastos / Ver Registro";
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
$_width = 700;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=ap_clasificaciongastos_lista" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('ap_clasificaciongastos_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
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
			<td class="tagForm" width="150">* Código:</td>
			<td>
	        	<input type="text" name="CodClasificacion" id="CodClasificacion" value="<?=$field['CodClasificacion']?>" style="width:75px; font-weight:bold;" maxlength="2" <?=$read_modificar?> />
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Descripción:</td>
			<td>
	        	<input type="text" name="Descripcion" id="Descripcion" value="<?=$field['Descripcion']?>" style="width:90%;" maxlength="50" <?=$disabled_ver?> />
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Aplicación:</td>
			<td>
				<select name="Aplicacion" id="Aplicacion" style="width:90%;" <?=$disabled_ver?>>
					<option value="">&nbsp;</option>
					<?=loadSelectValores('gastos-aplicacion',$field['Aplicacion'])?>
				</select>
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
			<td class="tagForm">Última Modif.:</td>
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

	<input type="hidden" id="sel_detalle" />
	<table style="width:100%; max-width:<?=$_width?>px;" class="tblBotones">
		<thead>
			<tr>
				<th class="divFormCaption" colspan="2">CONCEPTOS VÁLIDOS</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td align="right" class="gallery clearfix">
					<a id="a_detalle" href="../lib/listas/gehen.php?anz=lista_ap_conceptogastos&filtrar=default&ventana=listado_insertar_linea&detalle=detalle&modulo=ajax&accion=detalle_insertar&url=../../ap/ap_clasificaciongastos_ajax.php&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" style="display:none;"></a>
					<input type="button" class="btLista" value="Insertar" onclick="$('#a_detalle').click();" <?=$disabled_ver?> />
					<input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'detalle');" <?=$disabled_ver?> />
				</td>
			</tr>
		</tbody>
	</table>
	<div style="overflow:scroll; height:200px; width:100%; max-width:<?=$_width?>px; margin:auto;">
		<table class="tblLista" style="width:100%; max-width:<?=$_width?>px;">
			<thead>
				<tr>
					<th width="20">#</th>
					<th align="left">Concepto</th>
				</tr>
			</thead>
			
			<tbody id="lista_detalle">
				<?php
				$nro_detalle = 0;
				foreach ($field_detalle as $f)
				{
					$id = ++$nro_detalle;
					?>
					<tr class="trListaBody" onclick="clk($(this), 'detalle', 'detalle_<?=$id?>');" id="detalle_<?=$id?>">
						<th>
							<input type="hidden" name="detalle_CodConceptoGasto[]" value="<?=$f['CodConceptoGasto']?>">
							<?=$nro_detalle?>
						</th>
						<td><?=htmlentities($f['Descripcion'])?></td>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
	</div>
	<input type="hidden" id="nro_detalle" value="<?=$nro_detalle?>" />
	<input type="hidden" id="can_detalle" value="<?=$nro_detalle?>" />
</form>
<div style="width:100%; max-width:<?=$_width?>px;" class="divMsj">Campos Obligatorios *</div>
