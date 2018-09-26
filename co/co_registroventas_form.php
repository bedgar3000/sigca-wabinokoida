<?php
if ($opcion == "nuevo") {
	$field['CodOrganismo'] = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$field['Periodo'] = $PeriodoActual;
	$field['SistemaFuente'] = 'MA';
	$field['FechaDocumento'] = $FechaActual;
	$field['FlagRegistroVentas'] = 'S';
	$field['Estado'] = 'PE';
	##
	$_titulo = "Nuevo Documento en Registro de Ventas";
	$accion = "nuevo";
	$disabled_modificar = "";
	$disabled_ver = "";
	$read_modificar = "";
	$display_modificar = "";
	$display_ver = "";
	$display_submit = "";
	$label_submit = "Guardar";
	$focus = "btSubmit";
}
elseif ($opcion == "modificar" || $opcion == "ver") {
	##	consulto datos generales
	$sql = "SELECT *
			FROM co_registroventas rv
			WHERE rv.CodRegistro = '$sel_registros'";
	$field = getRecord($sql);
	##
	if ($opcion == "modificar") {
		$_titulo = "Modificar Documento en Registro de Ventas";
		$accion = "modificar";
		$disabled_modificar = "disabled";
		$disabled_ver = "";
		$read_modificar = "readonly";
		$display_modificar = "display:none;";
		$display_ver = "";
		$display_submit = "";
		$label_submit = "Modificar";
		$focus = "Comentarios";
	}
	##
	elseif ($opcion == "ver") {
		$_titulo = "Documento en Registro de Ventas";
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
$_width = 800;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=co_registroventas_lista" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('co_registroventas_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
	<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
	<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
	<input type="hidden" name="CodRegistro" id="CodRegistro" value="<?=$field['CodRegistro']?>" />
	<input type="hidden" name="CodDocumento" id="CodDocumento" value="<?=$field['CodDocumento']?>" />
	<input type="hidden" name="Estado" id="Estado" value="<?=$field['Estado']?>" />

	<table style="width:100%; max-width:<?=$_width?>px;" class="tblForm">
		<tr>
	    	<td colspan="4" class="divFormCaption">DATOS GENERALES</td>
	    </tr>
	    <tr>
			<td class="tagForm">* Organismo:</td>
			<td>
				<select name="CodOrganismo" id="CodOrganismo" style="width:295px;" <?=$disabled_modificar?>>
					<?=getOrganismos($field['CodOrganismo'], 3)?>
				</select>
			</td>
			<td class="tagForm">Periodo:</td>
			<td>
	        	<input type="text" name="Periodo" id="Periodo" value="<?=$field['Periodo']?>" style="width:100px; font-weight:bold;" readonly />
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Cliente:</td>
			<td class="gallery clearfix">
				<input type="hidden" name="DocFiscalCliente" id="DocFiscalCliente" value="<?=$field['DocFiscalCliente']?>">
				<input type="hidden" name="DireccionCliente" id="DireccionCliente" value="<?=$field['DireccionCliente']?>">
				<input type="text" name="CodPersonaCliente" id="CodPersonaCliente" value="<?=$field['CodPersonaCliente']?>" style="width:66px;" readonly />
				<input type="text" name="NombreCliente" id="NombreCliente" value="<?=htmlentities($field['NombreCliente'])?>" style="width:225px;" readonly />
				<a href="../lib/listas/gehen.php?anz=lista_personas&campo1=CodPersonaCliente&campo2=NombreCliente&campo3=DocFiscalCliente&campo4=DireccionCliente&ventana=lg_guiaremision&filtrar=default&FlagClasePersona=S&fEsCliente=S&concepto=80-0003&_APLICACION=<?=$_APLICACION?>&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" style="<?=$display_modificar?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
			<td class="tagForm">Sistema:</td>
			<td>
				<input type="hidden" name="SistemaFuente" id="SistemaFuente" value="<?=$field['SistemaFuente']?>">
	        	<input type="text" value="<?=mb_strtoupper(printValores('registro-ventas-sistema-fuente',$field['SistemaFuente']))?>" style="width:100px; font-weight:bold;" readonly />
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Documento:</td>
			<td>
				<select name="CodTipoDocumento" id="CodTipoDocumento" style="width:42px;" <?=$disabled_ver?>>
					<option value="">&nbsp;</option>
					<?=loadSelect2('co_tipodocumento','CodTipoDocumento','Descripcion',$field['CodTipoDocumento'],10)?>
				</select>
				<input type="text" name="NroDocumento" id="NroDocumento" value="<?=$field['NroDocumento']?>" style="width:100px;" maxlength="10" <?=$disabled_ver?> />
			</td>
			<td class="tagForm">Referencia:</td>
			<td>
				<select name="RefTipoDocumento" id="RefTipoDocumento" style="width:42px;" <?=$disabled_ver?>>
					<option value="">&nbsp;</option>
					<?=loadSelect2('co_tipodocumento','CodTipoDocumento','Descripcion',$field['RefTipoDocumento'],10)?>
				</select>
				<input type="text" name="RefNroDocumento" id="RefNroDocumento" value="<?=$field['RefNroDocumento']?>" style="width:100px;" maxlength="10" <?=$disabled_ver?> />
			</td>
		</tr>
		<tr>
			<th></th>
			<th>Datos del Documento</th>
			<td class="tagForm">* Moneda:</td>
			<td>
				<select name="MonedaDocumento" id="MonedaDocumento" style="width:146px;" <?=$disabled_ver?>>
					<?=loadSelectGeneral("monedas", $field['MonedaDocumento'])?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Fecha Documento:</td>
			<td>
				<input type="text" name="FechaDocumento" id="FechaDocumento" value="<?=formatFechaDMA($field['FechaDocumento'])?>" maxlength="10" style="width:75px;" class="datepicker" onkeyup="setFechaDMA(this);" onchange="setPeriodoFromFecha(this.value, $('#Periodo'))" <?=$disabled_ver?> />
			</td>
			<td class="tagForm">Monto Afecto:</td>
			<td>
				<input type="text" name="MontoAfecto" id="MontoAfecto" value="<?=number_format($field['MontoAfecto'],2,',','.')?>" style="width:146px; text-align: right;" class="currency" onchange="setMontoTotalRegistroVentas();" <?=$disabled_ver?> />
			</td>
		</tr>
		<tr>
			<td class="tagForm">Voucher:</td>
			<td>
	        	<input type="text" name="Voucher" id="Voucher" value="<?=$field['Voucher']?>" style="width:146px;" readonly />
			</td>
			<td class="tagForm">Monto No Afecto:</td>
			<td>
				<input type="text" name="MontoNoAfecto" id="MontoNoAfecto" value="<?=number_format($field['MontoNoAfecto'],2,',','.')?>" style="width:146px; text-align: right;" class="currency" onchange="setMontoTotalRegistroVentas();" <?=$disabled_ver?> />
			</td>
		</tr>
	    <tr>
			<td class="tagForm">&nbsp;</td>
			<td>
	            <input type="checkbox" name="FlagRegistroVentas" id="FlagRegistroVentas" value="S" <?=chkOpt($field['FlagRegistroVentas'], "S");?> <?=$disabled_ver?> /> Incluir en el Registro de Ventas
			</td>
			<td class="tagForm">Imp. Vtas.:</td>
			<td>
				<input type="text" name="MontoImpuestoVentas" id="MontoImpuestoVentas" value="<?=number_format($field['MontoImpuestoVentas'],2,',','.')?>" style="width:146px; text-align: right;" class="currency" onchange="setMontoTotalRegistroVentas();" <?=$disabled_ver?> />
			</td>
		</tr>
		<tr>
			<td class="tagForm" rowspan="2">Comentarios:</td>
			<td rowspan="2">
				<textarea name="Comentarios" id="Comentarios" style="width:295px; height: 35px;" <?=$disabled_ver?>><?=htmlentities($field['Comentarios'])?></textarea>
			</td>
			<td class="tagForm"><strong>Monto Total:</strong></td>
			<td>
				<input type="text" name="MontoTotal" id="MontoTotal" value="<?=number_format($field['MontoTotal'],2,',','.')?>" style="width:146px; text-align: right; font-weight: bold;" class="currency" readonly />
			</td>
		</tr>
	    <tr>
			<td class="tagForm">Imp. Retenido:</td>
			<td>
				<input type="text" name="MontoImpuestoRetenido" id="MontoImpuestoRetenido" value="<?=number_format($field['MontoImpuestoRetenido'],2,',','.')?>" style="width:146px; text-align: right;" class="currency" <?=$disabled_ver?> />
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

	<center class="gallery clearfix">
		<input type="submit" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" id="btSubmit" />
		<input type="button" value="Cancelar" style="width:75px;" id="btCancelar" onclick="<?=$clkCancelar?>" />
		<?php if ($field['CodDocumento']) { ?>
			<input type="button" value="Ver Doc. Origen" style="width:90px;" onclick="$('#a_documento').click();" />
			<a href="gehen.php?anz=co_documento_form&opcion=ver-modal&sel_registros=<?=$field['CodDocumento']?>&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe2]" style="display:none;" id="a_documento"></a>
		<?php } ?>
	</center>
</form>
<div style="width:100%; max-width:<?=$_width?>px;" class="divMsj">Campos Obligatorios *</div>

<script type="text/javascript">
	function setMontoTotalRegistroVentas() {
		var MontoAfecto = setNumero($('#MontoAfecto').val());
		var MontoNoAfecto = setNumero($('#MontoNoAfecto').val());
		var MontoImpuestoVentas = setNumero($('#MontoImpuestoVentas').val());
		var MontoImpuestoRetenido = setNumero($('#MontoImpuestoRetenido').val());
		var MontoTotal = MontoAfecto + MontoNoAfecto + MontoImpuestoVentas;
		$('#MontoTotal').val(MontoTotal).formatCurrency();
	}
</script>