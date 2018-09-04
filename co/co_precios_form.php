<?php
if ($opcion == "nuevo") {
	$field['Estado'] = 'A';
	$field['FlagVigIndefinida'] = 'S';
	$field['TipoDetalle'] = $fTipoDetalle;
	##
	$_titulo = "Lista de Precios / Nuevo Registro";
	$accion = "nuevo";
	$disabled_modificar = "";
	$disabled_vigencia = "disabled";
	$disabled_ver = "";
	$read_modificar = "";
	$display_modificar = "";
	$display_ver = "";
	$display_submit = "";
	$label_submit = "Guardar";
	$focus = "CodPrecio";
}
elseif ($opcion == "modificar" || $opcion == "ver" || $opcion == "ver_window") {
	##	consulto datos generales
	$sql = "SELECT
				pr.*,
				(CASE WHEN pr.TipoDetalle = 'I' THEN i.Descripcion ELSE s.Descripcion END) AS Item,
				(CASE WHEN pr.TipoDetalle = 'I' THEN i.CodInterno ELSE s.CodInterno END) AS CodInterno,
				p.NomCompleto AS NomPersona
			FROM co_precios pr
			LEFT JOIN lg_itemmast i ON (
				i.CodItem = pr.CodItem
				AND pr.TipoDetalle = 'I'
			)
			LEFT JOIN co_mastservicios s ON (
				s.CodServicio = pr.CodItem
				AND pr.TipoDetalle = 'S'
			)
			LEFT JOIN mastpersonas p ON p.CodPersona = pr.CodPersona
			WHERE CodPrecio = '$sel_registros'";
	$field = getRecord($sql);
	##
	if ($opcion == "modificar") {
		$_titulo = "Lista de Precios / Modificar Registro";
		$accion = "modificar";
		$disabled_modificar = "disabled";
		$disabled_ver = "";
		$disabled_vigencia = (($field['FlagVigIndefinida'] == 'S')?'disabled':'');
		$read_modificar = "readonly";
		$display_modificar = "display:none;";
		$display_ver = "";
		$display_submit = "";
		$label_submit = "Modificar";
		$focus = "Descripcion";
	}
	##
	elseif ($opcion == "ver" || $opcion == "ver_window") {
		$_titulo = "Lista de Precios / Ver Registro";
		$accion = "";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$disabled_vigencia = "disabled";
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
<?php if ($accion != 'ver_window') { ?>
	<table width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td class="titulo"><?=$_titulo?></td>
			<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
		</tr>
	</table><hr width="100%" color="#333333" />
<?php } ?>

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=co_precios_lista" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('co_precios_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
	<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
	<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
	<input type="hidden" name="fTipoDetalle" id="fTipoDetalle" value="<?=$fTipoDetalle?>" />
	<input type="hidden" name="CodPrecio" id="CodPrecio" value="<?=$field['CodPrecio']?>" />
	<input type="hidden" name="FlagImpuestoVentas" id="FlagImpuestoVentas" value="<?=$field['FlagImpuestoVentas']?>" />
	<input type="hidden" name="CodImpuesto" id="CodImpuesto" value="<?=$field['CodImpuesto']?>" />
	<input type="hidden" name="FactorImpuesto" id="FactorImpuesto" value="<?=$field['FactorImpuesto']?>" />
	<input type="hidden" name="CantidadEqui" id="CantidadEqui" value="<?=$field['CantidadEqui']?>" />
	
	<table style="width:100%; max-width:<?=$_width?>px;" class="tblForm">
		<tr>
	    	<td colspan="4" class="divFormCaption">Datos Generales</td>
	    </tr>
	    <tr>
			<td class="tagForm">* Organismo:</td>
			<td>
				<select name="CodOrganismo" id="CodOrganismo" style="width:335px;" <?=$disabled_modificar?>>
					<?=getOrganismos($field['CodOrganismo'], 3)?>
				</select>
			</td>
			<td class="tagForm">* Tipo Detalle:</td>
			<td>
				<select name="TipoDetalle" id="TipoDetalle" style="width:75px;" <?=$disabled_modificar?>>
					<?=loadSelectValores("cotizacion-tipo-item", $field['TipoDetalle'], 1)?>
				</select>
			</td>
		</tr>
		<tr>
			<?php if ($field['TipoDetalle'] == 'I') { ?>
				<td class="tagForm">* Item:</td>
				<td class="gallery clearfix">
					<input type="hidden" name="CodItem" id="CodItem" value="<?=$field['CodItem']?>" />
					<input type="text" name="CodInterno" id="CodInterno" value="<?=$field['CodInterno']?>" style="width:75px;" readonly />
					<input type="text" name="Item" id="Item" value="<?=$field['Item']?>" style="width:256px;" disabled />
					<a href="../lib/listas/gehen.php?anz=lista_lg_items&filtrar=default&campo1=CodItem&campo2=Item&campo3=CodInterno&campo4=CodUnidad&campo5=CodUnidadVenta&campo6=PrecioCostoUnitario&campo7=PrecioCosto&campo8=FlagImpuestoVentas&campo9=CodImpuesto&campo10=FactorImpuesto&campo11=CantidadEqui&ventana=co_precios&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" style=" <?=$display_modificar?>">
		            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
		            </a>
				</td>
			<?php } elseif ($field['TipoDetalle'] == 'S') { ?>
				<td class="tagForm">* Servicio:</td>
				<td class="gallery clearfix">
					<input type="hidden" name="CodItem" id="CodItem" value="<?=$field['CodItem']?>" />
					<input type="text" name="CodInterno" id="CodInterno" value="<?=$field['CodInterno']?>" style="width:75px;" readonly />
					<input type="text" name="Item" id="Item" value="<?=$field['Item']?>" style="width:256px;" disabled />
					<a href="../lib/listas/gehen.php?anz=lista_co_servicios&filtrar=default&campo1=CodItem&campo2=Item&campo3=CodInterno&campo4=CodUnidad&campo5=CodUnidadVenta&ventana=CodInterno&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" style=" <?=$display_modificar?>">
		            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
		            </a>
				</td>
			<?php } ?>
			<td class="tagForm">* Uni.:</td>
			<td>
				<input type="text" name="CodUnidad" id="CodUnidad" value="<?=$field['CodUnidad']?>" style="width:75px;" readonly />
			</td>
		</tr>
		<tr>
			<td class="tagForm">Cliente:</td>
			<td class="gallery clearfix">
				<input type="text" name="CodPersona" id="CodPersona" value="<?=$field['CodPersona']?>" style="width:75px;" readonly />
				<input type="text" name="NomPersona" id="NomPersona" value="<?=$field['NomPersona']?>" style="width:256px;" disabled />
				<a href="../lib/listas/gehen.php?anz=lista_personas&campo1=CodPersona&campo2=NomPersona&ventana=&FlagClasePersona=S&fEsCliente=S&filtrar=default&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe2]" style=" <?=$display_ver?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
			<td class="tagForm">* Uni. Venta:</td>
			<td>
				<input type="text" name="CodUnidadVenta" id="CodUnidadVenta" value="<?=$field['CodUnidadVenta']?>" style="width:75px;" readonly />
			</td>
		</tr>
		<tr>
			<td class="tagForm">&nbsp;</td>
			<td>
	            <input type="checkbox" name="FlagVigIndefinida" id="FlagVigIndefinida" value="S" <?=chkOpt($field['FlagVigIndefinida'], "S");?> <?=$disabled_ver?> onclick="set_vigencia(this.checked);" /> Vigencia Indefinida
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Fecha Vig. Desde:</td>
			<td>
				<input type="text" name="FechaVigDesde" id="FechaVigDesde" value="<?=formatFechaDMA($field['FechaVigDesde'])?>" maxlength="10" style="width:75px;" class="datepicker" onkeyup="setFechaDMA(this);" <?=$disabled_vigencia?> />
			</td>
			<td class="tagForm">* Fecha Vig. Hasta:</td>
			<td>
				<input type="text" name="FechaVigHasta" id="FechaVigHasta" value="<?=formatFechaDMA($field['FechaVigHasta'])?>" maxlength="10" style="width:75px;" class="datepicker" onkeyup="setFechaDMA(this);" <?=$disabled_vigencia?> />
			</td>
		</tr>
		<tr>
			<td class="tagForm">* % Ganancia:</td>
			<td>
				<input type="text" name="PorcMargen" id="PorcMargen" value="<?=number_format($field['PorcMargen'],2,',','.')?>" style="width:75px; text-align: right;" class="currency" <?=$disabled_ver?> onchange="calcularPrecio();" />
			</td>
			<td class="tagForm">* Precio Costo Venta:</td>
			<td>
				<input type="text" name="PrecioCosto" id="PrecioCosto" value="<?=number_format($field['PrecioCosto'],2,',','.')?>" style="width:125px; text-align: right;" class="currency" <?=$disabled_ver?> onchange="calcularPrecio();" />
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Precio Costo Uni.:</td>
			<td>
				<input type="text" name="PrecioCostoUnitario" id="PrecioCostoUnitario" value="<?=number_format($field['PrecioCostoUnitario'],2,',','.')?>" style="width:125px; text-align: right;" class="currency" <?=$disabled_ver?> onchange="calcularPrecio();" />
			</td>
			<td class="tagForm">* Precio de Venta:</td>
			<td>
				<input type="text" name="PrecioMenor" id="PrecioMenor" value="<?=number_format($field['PrecioMenor'],2,',','.')?>" style="width:125px; text-align: right;" class="currency" <?=$disabled_ver?> />
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Precio Mayor:</td>
			<td>
				<input type="text" name="PrecioMayor" id="PrecioMayor" value="<?=number_format($field['PrecioMayor'],2,',','.')?>" style="width:125px; text-align: right;" class="currency" <?=$disabled_ver?> />
			</td>
			<td class="tagForm">* Precio Unitario:</td>
			<td>
				<input type="text" name="PrecioUnitario" id="PrecioUnitario" value="<?=number_format($field['PrecioUnitario'],2,',','.')?>" style="width:125px; text-align: right;" class="currency" <?=$disabled_ver?> />
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Precio Especial Uni.:</td>
			<td>
				<input type="text" name="PrecioEspecial" id="PrecioEspecial" value="<?=number_format($field['PrecioEspecial'],2,',','.')?>" style="width:125px; text-align: right;" class="currency" onchange="setPrecioEspecialVenta();" <?=$disabled_ver?> />
			</td>
			<td class="tagForm">* Porcentaje Dcto. 1:</td>
			<td>
				<input type="text" name="PorcentajeDcto1" id="PorcentajeDcto1" value="<?=number_format($field['PorcentajeDcto1'],2,',','.')?>" style="width:125px; text-align: right;" class="currency" <?=$disabled_ver?> />
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Precio Especial Vta.:</td>
			<td>
				<input type="text" name="PrecioEspecialVta" id="PrecioEspecialVta" value="<?=number_format($field['PrecioEspecialVta'],2,',','.')?>" style="width:125px; text-align: right;" class="currency" <?=$disabled_ver?> />
			</td>
			<td class="tagForm">* Porcentaje Dcto. 2:</td>
			<td>
				<input type="text" name="PorcentajeDcto2" id="PorcentajeDcto2" value="<?=number_format($field['PorcentajeDcto2'],2,',','.')?>" style="width:125px; text-align: right;" class="currency" <?=$disabled_ver?> />
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Cantidad Mayor:</td>
			<td>
				<input type="text" name="CantidadMayor" id="CantidadMayor" value="<?=number_format($field['CantidadMayor'],2,',','.')?>" style="width:125px; text-align: right;" class="currency" <?=$disabled_ver?> />
			</td>
			<td class="tagForm">* Porcentaje Dcto. 3:</td>
			<td>
				<input type="text" name="PorcentajeDcto3" id="PorcentajeDcto3" value="<?=number_format($field['PorcentajeDcto3'],2,',','.')?>" style="width:125px; text-align: right;" class="currency" <?=$disabled_ver?> />
			</td>
		</tr>
		<tr>
			<td class="tagForm">Estado:</td>
			<td colspan="3">
	            <input type="radio" name="Estado" id="Activo" value="A" <?=chkOpt($field['Estado'], "A");?> <?=$disabled_ver?> /> Activo
	            &nbsp; &nbsp;
	            <input type="radio" name="Estado" id="Inactivo" value="I" <?=chkOpt($field['Estado'], "I");?> <?=$disabled_ver?> /> Inactivo
			</td>
		</tr>
	    <tr>
			<td class="tagForm">&Uacute;ltima Modif.:</td>
			<td colspan="3">
				<input type="text" value="<?=$field['UltimoUsuario']?>" style="width:145px;" disabled="disabled" />
				<input type="text" value="<?=$field['UltimaFecha']?>" style="width:145px" disabled="disabled" />
			</td>
		</tr>
	</table>

	<center>
		<input type="submit" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" id="btSubmit" />
		<input type="button" value="Cancelar" style="width:75px;" id="btCancelar" onclick="<?=$clkCancelar?>" />
	</center>
</form>
<div style="width:100%; max-width:<?=$_width?>px;" class="divMsj">Campos Obligatorios *</div>

<script type="text/javascript">
	function calcularPrecio() {
		var FlagImpuestoVentas = $('#FlagImpuestoVentas').val();
		var FactorImpuesto = new Number($('#FactorImpuesto').val());
		var CantidadEqui = new Number($('#CantidadEqui').val());
		
		var PorcMargen = setNumero($('#PorcMargen').val());
		//var PrecioCosto = setNumero($('#PrecioCosto').val());
		var PrecioCostoUnitario = setNumero($('#PrecioCostoUnitario').val());
		
		if ('<?=$_PARAMETRO['LISTPRECIVA']?>' == 'S') {
			if (FlagImpuestoVentas == 'S') {
				var PrecioMenor = (PrecioCostoUnitario / (1 - (PorcMargen / 100))) + ((PrecioCostoUnitario / (1 - (PorcMargen / 100))) * FactorImpuesto / 100);
				//var PrecioUnitario = (PrecioCostoUnitario / (1 - (PorcMargen / 100))) + ((PrecioCostoUnitario / (1 - (PorcMargen / 100))) * 12 / 100);
			} else {
				var PrecioMenor = (PrecioCostoUnitario / (1 - (PorcMargen / 100)));
				//var PrecioUnitario = (PrecioCostoUnitario / (1 - (PorcMargen / 100)));
			}
		} else {
			var PrecioMenor = (PrecioCostoUnitario / (1 - (PorcMargen / 100)));
			//var PrecioUnitario = (PrecioCostoUnitario / (1 - (PorcMargen / 100)));
		}
		var PrecioCosto = PrecioMenor * CantidadEqui;
		//var PrecioMenor = PrecioCosto / (1 - (PorcMargen / 100));
		//var PrecioUnitario = PrecioCostoUnitario / (1 - (PorcMargen / 100));

		$('#PrecioMenor').val(PrecioMenor).formatCurrency();
		$('#PrecioCosto').val(PrecioCosto).formatCurrency();
		//$('#PrecioUnitario').val(PrecioUnitario).formatCurrency();
	}
	function setPrecioEspecialVenta() {
		var PrecioEspecial = setNumero($('#PrecioEspecial').val());
		var CantidadEqui = new Number($('#CantidadEqui').val());
		var PrecioEspecialVta = PrecioEspecial * CantidadEqui;
		$('#PrecioEspecialVta').val(PrecioEspecialVta).formatCurrency();
	}
	function set_vigencia(checked) {
		if (checked) {
			$('#FechaVigDesde').prop('disabled', true).val('');
			$('#FechaVigHasta').prop('disabled', true).val('');
		} else {
			$('#FechaVigDesde').prop('disabled', false).val('<?=date('01-m-Y')?>');
			$('#FechaVigHasta').prop('disabled', false).val('<?=date(dias_del_mes(date('Y-m')).'-m-Y')?>');
		}
	}
</script>