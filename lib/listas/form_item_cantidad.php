<?php
include("../../lib/fphp.php");
include("../lib/fphp.php");
##	
if (!empty($CodItem))
{
	$sql = "SELECT
				*,
				'I' AS TipoDetalle,
				'0' AS Unidades
			FROM lg_itemmast
			WHERE CodItem = '$CodItem'";
}
else
{
	$sql = "SELECT
				*,
				'S' AS TipoDetalle,
				CodServicio AS CodItem,
				'UNI' AS CodUnidad,
				'UNI' AS CodUnidadComp,
				'1' AS Unidades
			FROM co_mastservicios
			WHERE CodServicio = '$CodServicio'";
}
$field = getRecord($sql);
?>
<table style="width:100%;" class="tblForm">
	<tr>
		<td class="tagForm" width="100">Item:</td>
		<td colspan="3">
			<input type="hidden" value="<?=$field['CodItem']?>">
			<input type="text" value="<?=$field['CodInterno']?>" style="width:75px;" disabled />
			<input type="text" value="<?=$field['Descripcion']?>" style="width:234px;" disabled />
			<input type="text" value="<?=$field['CodUnidad']?>" style="width:50px;" disabled />
		</td>
	</tr>
	<tr>
		<td class="tagForm">Cantidad:</td>
		<td>
			<input type="text" name="Cantidad" id="Cantidad" value="" style="width:100px; text-align:right;" autofocus="autofocus">
		</td>
		<td class="tagForm">Unidad Venta:</td>
		<td width="5">
			<select id="descuento_CodUnidadVenta" style="width: 50px;">
				<?=loadSelect2('mastunidades','CodUnidad','CodUnidad',$field['CodUnidadComp'],1)?>
			</select>
		</td>
	</tr>
</table>