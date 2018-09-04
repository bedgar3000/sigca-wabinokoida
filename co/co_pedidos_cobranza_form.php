<?php
include("../lib/fphp.php");
include("lib/fphp.php");
##	
$CodPersona = $_SESSION["CODPERSONA_ACTUAL"];
##	
$CodPersonaCajero = getVar3("SELECT CodPersona FROM co_cajeros WHERE CodPersona = '$CodPersona'");
$CodPersonaCobrador = getVar3("SELECT CodPersona FROM co_vendedor WHERE CodPersona = '$CodPersona'");
?>
<form name="frmcobranza" id="frmcobranza" autocomplete="off">
	<input type="hidden" name="cobranza_CodPersonaCajero" id="cobranza_CodPersonaCajero" value="<?=$CodPersonaCajero?>">
	<input type="hidden" name="cobranza_CodPersonaCobrador" id="cobranza_CodPersonaCobrador" value="<?=$CodPersonaCobrador?>">
	<table cellpadding="0" cellspacing="0" style="width: 100%;">
		<tr>
			<td width="50%">
				<table style="width:100%;" class="tblForm">
					<tr>
				    	<td colspan="2" class="divFormCaption">EFECTIVO</td>
				    </tr>
				    <tr>
						<td class="tagForm" style="font-weight: bold; font-size: 16px;">MONTO:</td>
						<td width="50">
							<input type="text" name="cobranza_MontoEfectivo" id="cobranza_MontoEfectivo" value="<?=$MontoTotal?>" style="width:165px; text-align: right;font-weight: bold; font-size: 16px; margin-right: 13px;" class="currency" onchange="setMontosCobranza();" />
						</td>
					</tr>
				</table>
			</td>
			<td width="50%">
				<table style="width:100%;" class="tblForm">
					<tr>
				    	<td colspan="2" class="divFormCaption">DOCUMENTO</td>
				    </tr>
				    <tr>
						<td class="tagForm" style="font-weight: bold; font-size: 16px;">MONTO TOTAL:</td>
						<td width="50">
							<input type="text" name="cobranza_MontoTotal" id="cobranza_MontoTotal" value="<?=$MontoTotal?>" style="width:165px; text-align: right;font-weight: bold; font-size: 16px; margin-right: 13px;" readonly />
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<input type="hidden" id="sel_cobranza" />
	<table style="width:100%; min-width:<?=$_width?>px;" class="tblBotones">
		<thead>
			<tr>
				<th class="divFormCaption" colspan="2">Tarjetas de Créditos / Débitos</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td align="right" class="gallery clearfix">
					<input type="button" class="btLista" value="Insertar" onclick="insertar2(this, 'cobranza', 'modulo=ajax&accion=cobranza_insertar', 'co_pedidos_ajax.php'); $('#cobranza_MontoEfectivo').val('0,00'); setMontosCobranza();" />
					<input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'cobranza');" />
				</td>
			</tr>
		</tbody>
	</table>
	<div style="overflow:scroll; height:150px; width:100%; margin:auto;">
		<table class="tblLista" style="width:100%; min-width: 1075px;">
			<thead>
				<tr>
					<th width="20">#</th>
					<th width="130">Tipo</th>
					<th width="130">Tarjeta</th>
					<th align="left">Banco</th>
					<th width="150">Importe</th>
					<th width="150">Cta. Cliente</th>
					<th width="150">Nro. Ref.</th>
				</tr>
			</thead>
			
			<tbody id="lista_cobranza">
			</tbody>
		</table>
	</div>
	<input type="hidden" id="nro_cobranza" value="0" />
	<input type="hidden" id="can_cobranza" value="0" />

	<table style="width:100%;" class="tblForm">
	    <tr>
	    	<td></td>
			<td class="tagForm" style="width:150px; font-weight: bold; font-size: 16px; color: #180E75;">TOTAL RECIBIDO:</td>
			<td width="50">
				<input type="text" name="cobranza_TotalRecibido" id="cobranza_TotalRecibido" value="<?=$MontoTotal?>" style="width:165px; text-align: right;font-weight: bold; font-size: 16px; color: #180E75;" readonly />
			</td>
		</tr>
	    <tr>
	    	<td></td>
			<td class="tagForm" style="width:300px; font-weight: bold; font-size: 16px; color: #9B0808;">VUELTO:</td>
			<td width="50">
				<input type="text" name="cobranza_Vuelto" id="cobranza_Vuelto" value="0,00" style="width:165px; text-align: right; font-weight: bold; font-size: 16px; color: #9B0808; margin-right: 13px;" readonly />
			</td>
		</tr>
	</table>
</form>

<script type="text/javascript">
	function setMontosCobranza() {
		var MontoEfectivo = setNumero($('#cobranza_MontoEfectivo').val());
		var MontoTotal = setNumero($('#cobranza_MontoTotal').val());
		var TotalRecibido = 0;
		var Vuelto = 0;

		TotalRecibido = MontoEfectivo;
		$('input[name="cobranza_Secuencia[]"]').each(function(idx) {
			var MontoLocal = setNumero($('input[name="cobranza_MontoLocal[]"]:eq('+idx+')').val());
			TotalRecibido += MontoLocal;
		});

		if (TotalRecibido == 0) Vuelto = 0;
		else Vuelto = TotalRecibido - MontoTotal;
		$('#cobranza_TotalRecibido').val(TotalRecibido).formatCurrency();
		$('#cobranza_Vuelto').val(Vuelto).formatCurrency();
	}
	function cobranza_tipo_pago(CodTipoPago, i) {
		$.post('co_pedidos_ajax.php', 'modulo=ajax&accion=cobranza_tipo_pago&CodTipoPago='+CodTipoPago, function(data) {
			if (data['FlagReqTipoTarjeta'] == 'S') {
				$('#cobranza_CodTipoTarjeta'+i).html(data['tipos_tarjeta']);
			} else {
				$('#cobranza_CodTipoTarjeta'+i).html('<option value="">&nbsp;</option>');
			}
			if (data['FlagReqBanco'] == 'S') {
				$('#cobranza_CodBanco'+i).html(data['bancos']);
			} else {
				$('#cobranza_CodBanco'+i).html('<option value="">&nbsp;</option>');
			}
			if (CodTipoPago == 'CH' || CodTipoPago == 'TR') {
				$('#cobranza_CtaBancaria'+i).prop('readonly', false).val('');
			} else {
				$('#cobranza_CtaBancaria'+i).prop('readonly', true).val('');
			}
	    }, 'json');
	}
</script>