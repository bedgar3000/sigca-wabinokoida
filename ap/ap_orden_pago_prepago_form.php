<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo">Confirmar/Rechazar Pre-Pago</td>
		<td align="right"><a class="cerrar" href="#" onclick="document.getElementById('frmentrada').submit()">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=ap_orden_pago_prepago_lista" method="POST" onsubmit="return validar_prepago(this, '<?=$accion?>');">
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
<input type="hidden" name="fCodProveedor" id="fCodProveedor" value="<?=$fCodProveedor?>" />
<input type="hidden" name="fNomProveedor" id="fNomProveedor" value="<?=$fNomProveedor?>" />
<input type="hidden" name="fCodTipoDocumento" id="fCodTipoDocumento" value="<?=$fCodTipoDocumento?>" />
<input type="hidden" name="fNroDocumento" id="fNroDocumento" value="<?=$fNroDocumento?>" />
<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
<input type="hidden" name="fFechaOrdenPagod" id="fFechaOrdenPagod" value="<?=$fFechaOrdenPagod?>" />
<input type="hidden" name="fFechaOrdenPagoh" id="fFechaOrdenPagoh" value="<?=$fFechaOrdenPagoh?>" />
<input type="hidden" name="FlagPagoDiferido" id="FlagPagoDiferido" value="<?=$FlagPagoDiferido?>" />
<input type="hidden" name="fCodSistemaFuente" id="fCodSistemaFuente" value="<?=$fCodSistemaFuente?>" />
<input type="hidden" name="fCodBanco" id="fCodBanco" value="<?=$fCodBanco?>" />
<input type="hidden" name="fNroCuenta" id="fNroCuenta" value="<?=$fNroCuenta?>" />
<input type="hidden" name="fMontoTotald" id="fMontoTotald" value="<?=$fMontoTotald?>" />
<input type="hidden" name="fMontoTotalh" id="fMontoTotalh" value="<?=$fMontoTotalh?>" />
<input type="hidden" name="fordenar" id="fordenar" value="<?=$fordenar?>" />
<input type="hidden" name="Anio" id="Anio" value="<?=$Anio?>" />
<input type="hidden" name="CodOrganismo" id="CodOrganismo" value="<?=$CodOrganismo?>" />
<input type="hidden" name="NroOrden" id="NroOrden" value="<?=$NroOrden?>" />

<div class="divBorder" style="width:1100px;">
<table width="100%" class="tblFiltro">
    <tr>
        <td align="right" width="125">Fecha de Proceso:</td>
        <td>
        	<input type="text" name="FechaPago" id="FechaPago" value="<?=formatFechaDMA($FechaActual)?>" style="width:60px;" class="datepicker codigo" />
		</td>
        <td align="right">
        	<input type="submit" style="width:75px;" value="Aceptar" id="btAceptar" disabled />
        	<input type="button" style="width:75px;" value="Cancelar" onclick="document.getElementById('frmentrada').submit();" />
		</td>
    </tr>
</table>
</div>

<table width="1100" align="center" cellpadding="0" cellspacing="0">
    <tr>
        <td>
            <div class="header">
            <ul id="tab">
            <!-- CSS Tabs -->
            <li id="li1" onclick="currentTab('tab', this);" class="current"><a href="#" onclick="mostrarTab('tab', 1, 2);">Detallado x Obligaciones</a></li>
            <li id="li2" onclick="currentTab('tab', this);"><a href="#" onclick="mostrarTab('tab', 2, 2);">Total x Cuentas Bancarias</a></li>
            </ul>
            </div>
        </td>
    </tr>
</table>

<div id="tab1" style="display:block;">
<center>
<table width="1100" class="tblBotones">
	<tr>
		<td align="right">
			<input type="button" value="Saldo de Bancos" style="width:100px;" />
		</td>
	</tr>
</table>

<div style="overflow:scroll; width:1100px; height:300px;">
<table width="2200" class="tblLista">
	<thead>
        <th scope="col" width="200">Tipo de Pago</th>
        <th scope="col">Pagar A</th>
        <th scope="col" width="75">Proveedor</th>
        <th scope="col" width="100">Total a Pagar</th>
        <th scope="col" width="150">Nro. Documento</th>
        <th scope="col" width="125">Doc. Fiscal</th>
        <th scope="col" width="100">Imponible</th>
        <th scope="col" width="100">No Afecto</th>
        <th scope="col" width="100">Monto Impuesto</th>
        <th scope="col" width="100">Monto Retenido</th>
        <th scope="col" width="100">Total Obligaci&oacute;n</th>
        <th scope="col" width="100">Monto Adelantos</th>
        <th scope="col" width="100">Monto Pago Parcial</th>
        <th scope="col" width="75">Fecha Documento</th>
        <th scope="col" width="125">Doc. Relacionado</th>
    </thead>
    
    <tbody>
    	<?php
		$MontoTotal = 0;
    	foreach ($registro as $orden) {
			list($Anio, $CodOrganismo, $NroOrden) = split("[_]", $orden);
			//	consulto datos generales
			$sql = "SELECT
						op.*,
						o.MontoObligacion,
						o.MontoAfecto,
						o.MontoNoAfecto,
						o.MontoImpuesto,
						o.MontoImpuestoOtros,
						o.MontoAdelanto,
						o.MontoPagoParcial,
						o.FechaDocumento,
						o.ReferenciaTipoDocumento,
						o.ReferenciaNroDocumento,
						op.FechaOrdenPago,
						tp.TipoPago,
						mp.DocFiscal,
						cb.Descripcion AS NomCuenta,
						cb.CtaBanco,
						cb.CodBanco,
						b.Banco,
						mo.Organismo
					FROM
						ap_ordenpago op
						INNER JOIN ap_obligaciones o ON (op.CodProveedor = o.CodProveedor AND
														 op.CodTipoDocumento = o.CodTipoDocumento AND
														 op.NroDocumento = o.NroDocumento)
						INNER JOIN masttipopago tp ON (tp.CodTipoPago = op.CodTipoPago)
						INNER JOIN mastpersonas mp ON (op.CodProveedor = mp.CodPersona)
						INNER JOIN mastorganismos mo ON (op.CodOrganismo = mo.CodOrganismo)
						LEFT JOIN ap_ctabancaria cb ON (op.NroCuenta = cb.NroCuenta)
						LEFT JOIN mastbancos b ON (cb.CodBanco = b.CodBanco)
					WHERE
						op.Anio = '".$Anio."' AND
						op.CodOrganismo = '".$CodOrganismo."' AND
						op.NroOrden = '".$NroOrden."'
					ORDER BY CodBanco";
			$field_orden = getRecord($sql);
			$MontoTotal += $field_orden['MontoTotal'];
			?>
		    <tr class="trListaBody2">
		        <td colspan="3"><?=htmlentities($field_orden['Organismo'])?></td>
		    </tr>
		    <tr class="trListaBody2">
		        <td><?=htmlentities($field_orden['Banco'])?></td>
		        <td colspan="2">Cuenta: <?=$field_orden['NroCuenta']?></td>
		    </tr>
			<tr class="trListaBody">
		        <td width="200">
		        	<input type="hidden" name="orden[]" value="<?=$orden?>" />
		        	<input type="hidden" name="monto[]" value="<?=$field_orden['MontoTotal']?>" />
		        	<?=htmlentities($field_orden['TipoPago'])?>
		        </td>
		        <td><?=htmlentities($field_orden['NomProveedorPagar'])?></td>
		        <td align="center"><?=$field_orden['CodProveedor']?></td>
		        <td align="right"><strong><?=number_format($field_orden['MontoTotal'], 2, ',', '.')?></strong></td>
		        <td align="center"><?=$field_orden['CodTipoDocumento']?>-<?=$field_orden['NroDocumento']?></td>
		        <td><?=$field_orden['DocFiscal']?></td>
		        <td align="right"><?=number_format($field_orden['MontoAfecto'], 2, ',', '.')?></td>
		        <td align="right"><?=number_format($field_orden['MontoNoAfecto'], 2, ',', '.')?></td>
		        <td align="right"><?=number_format($field_orden['MontoImpuesto'], 2, ',', '.')?></td>
		        <td align="right"><?=number_format($field_orden['MontoImpuestoOtros'], 2, ',', '.')?></td>
		        <td align="right"><?=number_format($field_orden['MontoObligacion'], 2, ',', '.')?></td>
		        <td align="right"><?=number_format($field_orden['MontoAdelanto'], 2, ',', '.')?></td>
		        <td align="right"><?=number_format($field_orden['MontoPagoParcial'], 2, ',', '.')?></td>
		        <td align="center"><?=formatFechaDMA($field_orden['FechaDocumento'])?></td>
		        <td align="center"></td>
		    </tr>
			<?php
    	}
    	?>
    </tbody>
</table>
<input type="hidden" name="MontoTotal" id="MontoTotal" value="<?=$MontoTotal?>">
</div>
</center>
</div>

<div id="tab2" style="display:none;">
<center>
<div style="overflow:scroll; width:1100px; height:300px;">
<table width="100%" class="tblLista">
	<thead>
        <th scope="col" colspan="2">Cuenta Bancaria</th>
        <th scope="col" width="125">Monto</th>
        <th scope="col" width="125">Saldo en Banco</th>
        <th scope="col" width="125">Ordenes Generadas</th>
        <th scope="col" width="125">Disponible</th>
        <th scope="col" width="50"># Doc.</th>
    </thead>
    
    <tbody>
    <?php
	$display_error = "display:none;";
	//	saldo en cuenta
	$filtro = "";
	foreach ($registro as $orden) {
		list($Anio, $CodOrganismo, $NroOrden) = split("[_]", $orden);
		if ($filtro == "")
			$filtro .= "(op.Anio = '".$Anio."' AND
						 op.CodOrganismo = '".$CodOrganismo."' AND
						 op.NroOrden = '".$NroOrden."')";
		else
			$filtro .= " OR (op.Anio = '".$Anio."' AND
							 op.CodOrganismo = '".$CodOrganismo."' AND
							 op.NroOrden = '".$NroOrden."')";
	}
	$sql = "SELECT
				SUM(op.MontoTotal) AS MontoTotal,
				op.CodOrganismo,				
				op.NroCuenta,
				op.NroOrden,
				cb.Descripcion AS NomCuenta,
				cb.CtaBanco,
				cb.CodBanco,
				cbb.SaldoActual,
				b.Banco,
				mo.Organismo,
				(SELECT COUNT(*)
				 FROM ap_ordenpago
				 WHERE
				 	CodOrganismo = op.CodOrganismo AND
					NroOrden = op.NroOrden AND
					NroCuenta = op.NroCuenta) AS CantidadDocumentos
			FROM
				ap_ordenpago op
				INNER JOIN mastorganismos mo ON (op.CodOrganismo = mo.CodOrganismo)
				INNER JOIN ap_ctabancaria cb ON (op.NroCuenta = cb.NroCuenta)
				INNER JOIN ap_ctabancariabalance cbb ON (cb.NroCuenta = cbb.NroCuenta)
				INNER JOIN mastbancos b ON (cb.CodBanco = b.CodBanco)
			WHERE $filtro
			GROUP BY CodOrganismo, CodBanco, NroCuenta
			ORDER BY CodBanco";
	$query_cuenta = mysql_query($sql) or die($sql.mysql_error());
	while($field_cuenta = mysql_fetch_array($query_cuenta)) {
	    ##	ordenes generadas
	    $sql = "SELECT SUM(MontoTotal)
	    		FROM ap_ordenpago
	    		WHERE
	    			NroCuenta = '$field_cuenta[NroCuenta]' AND
	    			Estado = 'GE'";
	    $OrdenesGeneradas = floatval(getVar3($sql));
	    $Disponible = $field_cuenta['SaldoActual'] - $OrdenesGeneradas;
		?>
        <tr class="trListaBody2">
            <td colspan="2"><?=htmlentities($field_cuenta['Organismo'])?></td>
        </tr>
        <tr class="trListaBody">
			<td align="center" width="125"><?=$field_cuenta['NroCuenta']?></td>
			<td><?=htmlentities($field_cuenta['NomCuenta'])?></td>
			<td align="right"><strong><?=number_format($field_cuenta['MontoTotal'], 2, ',', '.')?></strong></td>
			<td align="right"><?=number_format($field_cuenta['SaldoActual'], 2, ',', '.')?></td>
			<td align="right"><?=number_format($OrdenesGeneradas, 2, ',', '.')?></td>
			<td align="right"><strong><?=number_format($Disponible, 2, ',', '.')?></strong></td>
			<td align="center"><?=$field_cuenta['CantidadDocumentos']?></td>
		</tr>
        <?php
		if ($field_cuenta['MontoTotal'] > $field_cuenta['SaldoActual']) $display_error = "display:block;";
		$resta = $field_cuenta['SaldoActual'] - $field_cuenta['MontoTotal'];
		$restaReal = $Disponible - $field_cuenta['MontoTotal'];
	}
	?>
    </tbody>
</table>
</div>
</center>
</div>
</form>

<center>
<div class="msjError" style="width:1095px; display:none;">
	Se encontraron Obligaciones sin Disponibilidad Financiera en Banco para Generar el Pre-Pago
</div>
</center>

<script>
$(document).ready(function() {
	if (<?=$restaReal?> >= 0) {
		$("#btAceptar").removeAttr("disabled");
	} else {
		$(".msjError").css("display", "block");
	}
});

//	orden de pago (pre-pago)
function preparar_prepago(form, accion) {
	$.ajax({
		type: "POST",
		url: "lib/form_ajax.php",
		data: "modulo=orden_pago&"+$('#'+form.id).serialize()+"&accion=preparar_prepago",
		async: false,
		success: function(resp) {
			var partes = resp.split("|");
			if (partes[0].trim() != "") cajaModal(resp, "error", 400);
			else if (partes[1].trim() != "") {
				$("#"+form.id).attr("action", "gehen.php?anz=ap_orden_pago_prepago_lista&lista=prepago&concepto=02-0002&_APLICACION=AP&mostrar=vouchers&accion=ap_vouchers_tab&origen=orden-adelanto-total&registro="+partes[1]);
				form.submit();
			} else form.submit();
		}
	});
	return false;
}

//	validar orden de pago
function validar_prepago(form, accion) {
	var MontoTotal = Number($('#MontoTotal').val());
	if (MontoTotal > 0) {
		preparar_prepago(form, accion);
	} else {
		$("#cajaModal").dialog({
			buttons: {
				"Aceptar": function() {
					$(this).dialog("close");
					bloqueo(true);
					preparar_prepago(form, accion);
				},
				"Cancelar": function() {
					$(this).dialog("close");
				}
			}
		});
		cajaModalConfirm("No Existe Monto Pendiente de Pago para la Orden u Obligación,<br> Desea Continuar?", 425);
	}
	return false;
}
</script>
