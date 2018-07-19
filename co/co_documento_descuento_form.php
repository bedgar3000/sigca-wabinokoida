<?php
include("../lib/fphp.php");
include("lib/fphp.php");
?>
<table style="width:100%;" class="tblForm">
	<tr>
		<td class="tagForm" width="100">Item:</td>
		<td colspan="3">
			<input type="hidden" id="descuento_FlagExonIva" value="<?=$FlagExonIva?>">
			<input type="hidden" id="descuento_CodItem" value="<?=$CodItem?>">
			<input type="text" id="descuento_CodInterno" value="<?=$CodInterno?>" style="width:75px;" disabled />
			<input type="text" id="descuento_Descripcion" value="<?=$Descripcion?>" style="width:234px;" disabled />
			<input type="text" id="descuento_CodUnidad" value="<?=$CodUnidad?>" style="width:50px;" disabled />
		</td>
	</tr>
	<tr>
		<td class="tagForm">Precio de Lista:</td>
		<td>
			<input type="hidden" id="descuento_PrecioUnitLista" value="<?=$PrecioUnit?>">
			<input type="text" id="descuento_PrecioUnit" value="<?=$PrecioUnit?>" style="width:100px; text-align:right;" disabled>
		</td>
		<td class="tagForm">Unidad Venta:</td>
		<td width="5">
			<?php if ($TipoDetalle == 'I') { ?>
				<select id="descuento_CodUnidadVenta" style="width: 100px;">
					<?=unidades_item($CodItem,$CodUnidadVenta,0)?>
				</select>
			<?php } else { ?>
				<select id="descuento_CodUnidadVenta" style="width: 100px;">
					<?=loadSelect2('mastunidades','CodUnidad','CodUnidad',$CodUnidadVenta,1)?>
				</select>
			<?php } ?>
		</td>
	</tr>
	<tr>
		<td class="tagForm">Descuento:</td>
		<td>
			<input type="text" id="descuento_MontoDcto" value="<?=$MontoDcto?>" class="currency" style="width:100px; text-align:right;" onchange="setMontosDescuentoVentas(true);">
			<input type="text" id="descuento_PorcentajeDcto" value="<?=$PorcentajeDcto?>" class="currency" style="width:35px; text-align:right;" onchange="setMontosDescuentoVentas(false);">%
		</td>
		<td class="tagForm">Cantidad:</td>
		<td>
			<input type="text" id="descuento_CantidadPedida" value="<?=$CantidadPedida?>" style="width:100px; text-align:right;" onchange="setMontosDescuentoVentas(true);">
		</td>
	</tr>
	<tr>
		<td class="tagForm">Precio de Venta:</td>
		<td>
			<input type="hidden" id="descuento_PrecioUnitFinal" value="<?=$PrecioUnit?>">
			<input type="hidden" id="descuento_MontoTotalFinal" value="<?=$MontoTotal?>">
			<input type="text" id="descuento_PrecioUnitOriginal" value="<?=$PrecioUnitOriginal?>" style="width:100px; text-align:right;" disabled>
		</td>
		<td class="tagForm">Importe Total:</td>
		<td>
			<input type="text" id="descuento_MontoTotal" value="<?=$MontoTotal?>" style="width:100px; text-align:right;" disabled>
		</td>
	</tr>
</table>

<script type="text/javascript">
</script>