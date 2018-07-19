<?php
if ($opcion == "nuevo") {
	$field['Estado'] = 'A';
	##
	$_titulo = "Autorizaciones Fiscales / Nuevo Registro";
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
			FROM co_fiscalautorizacion
			WHERE CodAutorizacion = '$sel_registros'";
	$field = getRecord($sql);
	##
	if ($opcion == "modificar") {
		$_titulo = "Autorizaciones Fiscales / Modificar Registro";
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
		$_titulo = "Autorizaciones Fiscales / Ver Registro";
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
$_width = 600;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=co_fiscalautorizacion_lista" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('co_fiscalautorizacion_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
	<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
	<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
	<input type="hidden" name="CodAutorizacion" id="CodAutorizacion" value="<?=$field['CodAutorizacion']?>" />

	<table style="width:100%; max-width:<?=$_width?>px;" class="tblForm">
		<tr>
	    	<td colspan="2" class="divFormCaption">DATOS GENERALES</td>
	    </tr>
	    <tr>
			<td class="tagForm">* Organismo:</td>
			<td>
				<select name="CodOrganismo" id="CodOrganismo" style="width:295px;" <?=$disabled_modificar?>>
					<?=getOrganismos($field['CodOrganismo'], 3)?>
				</select>
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Nro. Autorizaci&oacute;n:</td>
			<td>
	        	<input type="text" name="NroAutorizacion" id="NroAutorizacion" value="<?=$field['NroAutorizacion']?>" maxlength="10" style="width:100px;" <?=$disabled_ver?> />
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Fecha Documento:</td>
			<td>
				<input type="text" name="FechaDocumento" id="FechaDocumento" value="<?=formatFechaDMA($field['FechaDocumento'])?>" maxlength="10" style="width:100px;" class="datepicker" onkeyup="setFechaDMA(this);" <?=$disabled_ver?> />
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Nro. Orden:</td>
			<td>
	        	<input type="text" name="NroOrden" id="NroOrden" value="<?=$field['NroOrden']?>" maxlength="10" style="width:100px;" <?=$disabled_ver?> />
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
				<th class="divFormCaption" colspan="2">DETALLE DE LAS AUTORIZACIONES</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td align="right">
					<input type="button" class="btLista" value="Insertar" onclick="insertar2(this, 'detalle', 'modulo=ajax&accion=detalle_insertar', 'co_fiscalautorizacion_ajax.php');" <?=$disabled_ver?> />
					<input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'detalle');" <?=$disabled_ver?> />
				</td>
			</tr>
		</tbody>
	</table>
	<div style="overflow:scroll; height:230px; width:100%; max-width:<?=$_width?>px; margin:auto;">
		<table class="tblLista" style="width:100%; min-width:<?=$_width-50?>px;">
			<thead>
				<tr>
					<th width="20">#</th>
					<th align="left">Tipo</th>
					<th width="75">Serie</th>
					<th width="75">Nro. Desde</th>
					<th width="75">Nro. Hasta</th>
					<th width="75">Ult. Número</th>
				</tr>
			</thead>
			
			<tbody id="lista_detalle">
				<?php
				$nro_detalle = 0;
				$sql = "SELECT *
						FROM co_fiscalautorizaciondet
						WHERE CodAutorizacion = '$field[CodAutorizacion]'
						ORDER BY Secuencia";
				$field_detalle = getRecords($sql);
				foreach ($field_detalle as $f)
				{
					$id = ++$nro_detalle;
					?>
					<tr class="trListaBody" onclick="clk($(this), 'detalle', 'detalle_<?=$id?>');" id="detalle_<?=$id?>">
						<th>
							<input type="hidden" name="detalle_Secuencia[]" id="detalle_Secuencia<?=$id?>" value="<?=$f['Secuencia']?>">
							<?=$id?>
						</th>
			            <td>
			                <select name="detalle_CodTipoDocumento[]" class="cell" <?=$disabled_ver?>>
				                <?=loadSelect2('co_tipodocumento','CodTipoDocumento','Descripcion',$f['CodTipoDocumento'],1)?>
				            </select>
			            </td>
			            <td>
			                <select name="detalle_CodSerie[]" class="cell" <?=$disabled_ver?>>
				                <?=loadSelect2('co_seriefiscal','CodSerie','NroSerie',$f['CodSerie'],1)?>
				            </select>
			            </td>
						<td>
							<input type="text" name="detalle_NroDesde[]" value="<?=$f['NroDesde']?>" class="cell" <?=$disabled_ver?>>
						</td>
						<td>
							<input type="text" name="detalle_NroHasta[]" value="<?=$f['NroHasta']?>" class="cell" <?=$disabled_ver?>>
						</td>
						<td>
							<input type="text" name="detalle_UltNroEmitido[]" value="<?=$f['UltNroEmitido']?>" class="cell" <?=$disabled_ver?>>
						</td>
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