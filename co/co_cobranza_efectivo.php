<?php
include("../lib/fphp.php");
include("lib/fphp.php");
?>
<table style="width:100%;" class="tblForm">
	<tr>
		<td class="tagForm">x Cobrar:</td>
		<td width="5">
			<input type="text" id="efectivo_MontoPorCobrar" value="<?=$MontoPendiente?>" style="width:150px; text-align:right; font-weight: bold; font-size: 14px;" readonly>
		</td>
	</tr>
	<tr>
		<td class="tagForm">Recibido:</td>
		<td>
			<input type="text" id="efectivo_MontoRecibido" value="<?=$MontoPendiente?>" class="currency" style="width:150px; text-align:right; font-weight: bold; font-size: 14px;" onchange="setMontosEfectivoCobranza();">
		</td>
	</tr>
	<tr>
		<td class="tagForm">Vuelto:</td>
		<td>
			<input type="text" id="efectivo_MontoVuelto" value="0,00" class="currency" style="width:150px; text-align:right; font-weight: bold; font-size: 14px; color: red;" readonly>
		</td>
	</tr>
</table>

<script type="text/javascript">
	$("#efectivo_MontoRecibido").focus();

	function setMontosEfectivoCobranza() {
		var efectivo_MontoPorCobrar = setNumero($('#efectivo_MontoPorCobrar').val());
		var efectivo_MontoRecibido = setNumero($('#efectivo_MontoRecibido').val());
		var efectivo_MontoVuelto = efectivo_MontoPorCobrar - efectivo_MontoRecibido;

		$('#efectivo_MontoVuelto').val(efectivo_MontoVuelto).formatCurrency();
	}
</script>