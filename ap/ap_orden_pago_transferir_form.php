<?php
$Ahora = ahora();
list($NroProceso, $Secuencia) = split("[.]", $registro);
//	consulto datos generales
$sql = "SELECT
			p.NroProceso,
			p.CodTipoPago,
			p.NroCuenta,
			p.FechaPago,
			p.MontoPago,
			tp.TipoPago,
			cb.CodCuenta,
			cb.CodCuentaPub20,
			cb.Descripcion AS NomCuentaBanco,
			pc.Descripcion AS NomCuentaContable,
			pc20.Descripcion AS NomCuentaContablePub20,
			cbb.SaldoActual,
			o.FlagFacturaPendiente,
			o.NroDocumento,
			op.FlagPagoParcial
		FROM
			ap_pagos p
			INNER JOIN ap_ordenpago op ON (op.NroProceso = p.NroProceso AND op.Secuencia = p.Secuencia)
			INNER JOIN ap_obligaciones o ON (o.CodProveedor = op.CodProveedor AND o.CodTipoDocumento = op.CodTipoDocumento AND o.NroDocumento = op.NroDocumento)
			INNER JOIN masttipopago tp ON (p.CodTipoPago = tp.CodTipoPago)
			INNER JOIN ap_ctabancaria cb ON (p.NroCuenta = cb.NroCuenta)
			INNER JOIN ap_ctabancariabalance cbb ON (cb.NroCuenta = cbb.NroCuenta)
			LEFT JOIN ac_mastplancuenta pc ON (cb.CodCuenta = pc.CodCuenta)
			LEFT JOIN ac_mastplancuenta20 pc20 ON (cb.CodCuentaPub20 = pc20.CodCuenta)
		WHERE
			p.NroProceso = '".$NroProceso."' AND
			p.Secuencia = '".$Secuencia."'
		GROUP BY p.NroProceso, p.Secuencia";
$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
if (mysql_num_rows($query)) $field = mysql_fetch_array($query);
//	------------------------------------
$display_factura = (($field['FlagFacturaPendiente']=='S')?'':'display:none;')
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo">Impresi&oacute;n/Transferencia de Pagos</td>
		<td align="right"><a class="cerrar" href="#" onclick="document.getElementById('frmentrada').submit()">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=ap_orden_pago_transferir_lista" method="POST" onsubmit="return transferir(this, '<?=$accion?>');">
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
<input type="hidden" name="fPeriodo" id="fPeriodo" value="<?=$fPeriodo?>" />
<input type="hidden" name="Secuencia" id="Secuencia" value="<?=$Secuencia?>" />
<input type="hidden" name="FlagFacturaPendiente" id="FlagFacturaPendiente" value="<?=$field['FlagFacturaPendiente']?>" />
<input type="hidden" name="NroDocumento" id="NroDocumento" value="<?=$field['NroDocumento']?>" />
<input type="hidden" name="FlagPagoParcial" id="FlagPagoParcial" value="<?=$field['FlagPagoParcial']?>" />

<table width="1050" class="tblForm">
	<tr>
    	<td colspan="2" class="divFormCaption">Informaci&oacute;n del Pre-Pago</td>
    	<td colspan="2" class="divFormCaption" style=" <?=$display_factura?>">Informaci&oacute;n del Documento</td>
    </tr>
	<tr>
		<td class="tagForm" width="150"><strong>Fecha de Pago:</strong></td>
		<td><input type="text" id="FechaPago" value="<?=formatFechaDMA(substr($Ahora, 0, 10))?>" style="width:65px;" class="datepicker codigo" onkeyup="setFechaDMA(this);" /></td>
		<td class="tagForm" width="150" style=" <?=$display_factura?>"><strong>Fecha de Factura:</strong></td>
		<td style=" <?=$display_factura?>"><input type="text" id="FechaFactura" value="<?=formatFechaDMA(substr($Ahora, 0, 10))?>" style="width:65px;" class="datepicker codigo" onkeyup="setFechaDMA(this);" /></td>
	</tr>
	<tr>
		<td class="tagForm">Nro. Pre-Pago:</td>
		<td><input type="text" id="NroProceso" style="width:65px;" class="disabled" value="<?=$NroProceso?>" disabled="disabled" /></td>
		<td style=" <?=$display_factura?>" class="tagForm">Nro. Control:</td>
		<td style=" <?=$display_factura?>"><input type="text" id="NroControl" style="width:65px;" /></td>
	</tr>
	<tr>
		<td class="tagForm">Nro. Pago:</td>
		<td><input type="text" name="NroPago" id="NroPago" style="width:65px;" maxlength="10" <?=($_PARAMETRO['NROPAGOMOD']=='S'?'':'disabled')?> /></td>
		<td style=" <?=$display_factura?>" class="tagForm">Nro. Factura:</td>
		<td style=" <?=$display_factura?>"><input type="text" id="NroFactura" style="width:65px;" /></td>
	</tr>
	<tr>
		<td class="tagForm">Tipo de Pago:</td>
		<td>
        	<input type="hidden" id="CodTipoPago" value="<?=$field['CodTipoPago']?>" />
        	<input type="text" style="width:150px;" class="disabled" value="<?=($field['TipoPago'])?>" disabled="disabled" /> 
            Monto a Pagar: 
            <input type="text" id="MontoPago" style="width:100px; text-align:right;" class="codigo" value="<?=number_format($field['MontoPago'], 2, ',', '.')?>" disabled="disabled" />
            Saldo en Banco: 
            <input type="text" id="SaldoActual" style="width:100px; text-align:right;" class="codigo" value="<?=number_format($field['SaldoActual'], 2, ',', '.')?>" disabled="disabled" />
		</td>
	</tr>
	<tr>
		<td class="tagForm">Cta. Bancaria:</td>
		<td>
        	<input type="text" id="NroCuenta" style="width:150px;" class="disabled" value="<?=$field['NroCuenta']?>" disabled="disabled" />
        	<input type="text" style="width:395px;" class="disabled" value="<?=htmlentities($field['NomCuentaBanco'])?>" disabled="disabled" />
		</td>
	</tr>
	<tr style="display:none;">
		<td class="tagForm">Cta. Contable:</td>
		<td>
        	<input type="text" id="CodCuenta" style="width:150px;" class="disabled" value="<?=$field['CodCuenta']?>" disabled="disabled" />
        	<input type="text" style="width:395px;" class="disabled" value="<?=htmlentities($field['NomCuentaContable'])?>" disabled="disabled" />
		</td>
	</tr>
	<tr>
		<td class="tagForm">Cta. Contable (Pub.20):</td>
		<td>
        	<input type="text" id="CodCuentaPub20" style="width:150px;" class="disabled" value="<?=$field['CodCuentaPub20']?>" disabled="disabled" />
        	<input type="text" style="width:395px;" class="disabled" value="<?=htmlentities($field['NomCuentaContablePub20'])?>" disabled="disabled" />
		</td>
	</tr>
</table>
<center> 
<input type="submit" value="Aceptar" style="width:75px;" />
<input type="button" value="Cancelar" style="width:75px;" onclick="this.form.submit();" />
</center>
</form>
<br />

<center>
<div style="width:1050px" class="divFormCaption">Documentos a Pagar del Pre-Pago</div>
<form name="frm_detalles" id="frm_detalles">
<div style="overflow:scroll; width:1050px; height:150px;">
<table width="100%" class="tblLista">
	<thead>
	<tr>
		<th width="25">#</th>
		<th>Pagar A</th>
		<th width="125">Doc. Fiscal</th>
		<th width="150">Nro. Documento</th>
		<th width="75">Fecha</th>
		<th width="75"># Registro</th>
		<th width="125">Neto a Pagar</th>
	</tr>
    </thead>
    
    <tbody id="lista_detalles">
    <?php
	$sql = "SELECT
				p.Secuencia,
				p.NomProveedorPagar,
				p.MontoPago,
				mp.DocFiscal,
				op.FechaOrdenPago,
				op.CodTipoDocumento,
				op.NroDocumento,
				op.NroRegistro,
				op.MontoTotal,
				op.Estado
			FROM
				ap_pagos p
				INNER JOIN ap_ordenpago op ON (p.NroProceso = op.NroProceso AND
											   p.Secuencia = op.Secuencia)
				INNER JOIN mastpersonas mp ON (p.CodProveedor = mp.CodPersona)
			WHERE
				p.NroProceso = '".$NroProceso."' AND
				p.Secuencia = '".$Secuencia."'
			ORDER BY Secuencia";
	$query_detalles = mysql_query($sql) or die ($sql.mysql_error());
	$i=0;
	while ($field_detalles = mysql_fetch_array($query_detalles)) {
		?>
		<tr class="trListaBody">
			<th align="center"><?=++$i?></th>
			<td><?=htmlentities($field_detalles['NomProveedorPagar'])?></td>
			<td><?=$field_detalles['DocFiscal']?></td>
			<td align="center"><?=$field_detalles['CodTipoDocumento']?>-<?=$field_detalles['NroDocumento']?></td>
			<td align="center"><?=formatFechaDMA($field_detalles['FechaOrdenPago'])?></td>
			<td align="center"><?=$field_detalles['NroRegistro']?></td>
			<td align="right"><strong><?=number_format($field_detalles['MontoTotal'], 2, ',', '.')?></strong></td>
		</tr>
		<?php
	}
	?>
    </tbody>
</table>
</div>
</form>
</center>