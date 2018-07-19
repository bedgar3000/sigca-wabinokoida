<?php
list($Anio, $CodOrganismo, $NroOrden) = split("[_]", $registro[0]);
##	
$sql = "SELECT
			SUM(MontoRetenciones) AS MontoRetenciones,
			SUM(MontoIva) AS MontoIva
		FROM ap_pagosparciales
		WHERE
			Estado <> 'AN' AND
			Anio = '".$Anio."' AND
			CodOrganismo = '".$CodOrganismo."' AND
			NroOrden = '".$NroOrden."'
		GROUP BY Anio, CodOrganismo, NroOrden";
$field_resumen = getRecord($sql);
##	consulto datos generales
$sql = "SELECT
			op.*,
			cb.CodBanco,
			cbb.SaldoActual,
			o.NroControl,
			o.MontoObligacion,
			(o.MontoImpuestoOtros - ".floatval($field_resumen['MontoRetenciones']).") AS MontoImpuestoOtros,
			o.MontoAfecto,
			o.MontoNoAfecto,
			o.MontoAdelanto,
			(o.MontoImpuesto - ".floatval($field_resumen['MontoIva']).") AS MontoImpuesto,
			o.MontoPagoParcial,
			(o.MontoObligacion - o.MontoPagoParcial - o.MontoAdelanto) AS SaldoPendiente,
			(o.MontoAfecto + o.MontoNoAfecto + o.MontoImpuesto - ABS(o.MontoImpuestoOtros)) AS NetoPagar,
			p.NomCompleto AS NomProveedor
		FROM
			ap_ordenpago op
			INNER JOIN ap_obligaciones o ON (o.CodProveedor = op.CodProveedor
											AND o.CodTipoDocumento = op.CodTipoDocumento
											AND o.NroDocumento = op.NroDocumento)
			INNER JOIN ap_ctabancaria cb ON (cb.NroCuenta = o.NroCuenta)
			INNER JOIN ap_ctabancariabalance cbb ON (cb.NroCuenta = cbb.NroCuenta)
			INNER JOIN mastpersonas p ON (p.CodPersona = op.CodProveedor)
		WHERE
			op.Anio = '".$Anio."' AND
			op.CodOrganismo = '".$CodOrganismo."' AND
			op.NroOrden = '".$NroOrden."'";
$field = getRecord($sql);
##	------------------------
$sql = "SELECT pp.*
		FROM ap_pagosparciales pp
		WHERE
			pp.Estado <> 'AN' AND
			pp.Anio = '".$Anio."' AND
			pp.CodOrganismo = '".$CodOrganismo."' AND
			pp.NroOrden = '".$NroOrden."'";
$field_pagos = getRecords($sql);
##	------------------------
$_titulo = "Pago Parcial";
$accion = "pago_parcial";
$label_submit = "Generar Pago";
$focus = "Denominacion";
$clkCancelar = "document.getElementById('frmentrada').submit();";
//	------------------------------------
if (!count($field_pagos)) $FlagPrimerPago = 'S'; else $FlagPrimerPago = 'N';
//	------------------------------------
$_width = 900;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<table align="center" cellpadding="0" cellspacing="0" style="width:<?=$_width?>px;">
    <tr>
        <td>
            <div class="header">
	            <ul id="tab">
		            <li id="li1" onclick="currentTab('tab', this);" class="current"><a href="#" onclick="mostrarTab('tab', 1, 3);">Informaci&oacute;n del Pago</a></li>
		            <li id="li2" onclick="currentTab('tab', this);"><a href="#" onclick="mostrarTab('tab', 2, 3);">Distribuci&oacute;n Pagos</a></li>
		            <li id="li3" onclick="currentTab('tab', this);"><a href="#" onclick="mostrarTab('tab', 3, 3);">Distribuci&oacute;n Presupuesto</a></li>
	            </ul>
            </div>
        </td>
    </tr>
</table>

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=ap_orden_pago_prepago_lista" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('ap_orden_pago_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
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
<input type="hidden" name="FlagPrimerPago" id="FlagPrimerPago" value="<?=$FlagPrimerPago?>" />

<div id="tab1" style="display:block;">
	<table width="<?=$_width?>" class="tblForm">
		<tr>
	    	<td colspan="4" class="divFormCaption">Informaci&oacute;n de la Obligaci&oacute;n</td>
	    </tr>
	    <tr>
			<td class="tagForm" width="125">Organismo:</td>
			<td>
				<input type="hidden" name="Anio" id="Anio" value="<?=$field['Anio']?>" />
				<input type="hidden" name="NroOrden" id="NroOrden" value="<?=$field['NroOrden']?>" />
	        	<select name="CodOrganismo" id="CodOrganismo" style="width:300px; height:18px; font-weight:bold;" class="disabled">
	            	<?=loadSelect("mastorganismos", "CodOrganismo", "Organismo", $field['CodOrganismo'], 1)?>
	            </select>
			</td>
			<td class="tagForm" width="125"><strong>Total Obligaci&oacute;n:</strong></td>
			<td>
	        	<input type="text" name="MontoObligacion" id="MontoObligacion" value="<?=number_format($field['MontoObligacion'], 2, ',', '.')?>" style="width:150px; text-align:right; font-weight:bold;" readonly />
	        </td>
		</tr>
	    <tr>
			<td class="tagForm">Proveedor:</td>
			<td>
	        	<input type="text" name="CodProveedor" id="CodProveedor" value="<?=$field['CodProveedor']?>" style="width:46px; font-weight:bold;" disabled="disabled" />
				<input type="text" name="NomProveedor" id="NomProveedor" value="<?=htmlentities($field['NomProveedor'])?>" style="width:250px; font-weight:bold;" disabled="disabled" />
	        </td>
			<td class="tagForm">Adelantos (-):</td>
			<td>
	        	<input type="text" name="MontoAdelanto" id="MontoAdelanto" value="<?=number_format($field['MontoAdelanto'], 2, ',', '.')?>" style="width:150px; text-align:right; font-weight:bold;" readonly />
	        </td>
		</tr>
	    <tr>
			<td class="tagForm">Tipo de Documento:</td>
			<td>
	        	<select name="CodTipoDocumento" id="CodTipoDocumento" style="width:300px; height:18px; font-weight:bold;" disabled>
	                <?=loadSelect("ap_tipodocumento", "CodTipoDocumento", "Descripcion", $field['CodTipoDocumento'], 11)?>
	            </select>
	        </td>
			<td class="tagForm">Pagos Parciales (-):</td>
			<td><input type="text" name="MontoPagoParcial" id="MontoPagoParcial" value="<?=number_format($field['MontoPagoParcial'], 2, ',', '.')?>" style="width:150px; text-align:right; font-weight:bold;" readonly /></td>
	    </tr>
	    <tr>
			<td class="tagForm">Nro. Documento:</td>
			<td><input type="text" name="NroControl" id="NroControl" value="<?=$field['NroControl']?>" style="width:150px; font-weight:bold;" disabled /></td>
			<td class="tagForm"><strong>Saldo Pendiente:</strong></td>
			<td>
	        	<input type="text" name="SaldoPendiente" id="SaldoPendiente" value="<?=number_format($field['SaldoPendiente'], 2, ',', '.')?>" style="width:150px; text-align:right; font-weight:bold;" readonly />
	        </td>
		</tr>
		<tr>
	    	<td colspan="4" class="divFormCaption">Informaci&oacute;n del Pago</td>
	    </tr>
	    <tr>
			<td class="tagForm">Fecha de Pago:</td>
			<td>
				<input type="text" name="FechaPago" id="FechaPago" value="<?=formatFechaDMA($FechaActual)?>" style="width:65px;" class="datepicker" maxlength="10" />
			</td>
			<td class="tagForm">&nbsp;</td>
			<td>
	        	<input type="checkbox" name="FlagPorcentual" id="FlagPorcentual" value="S" onclick="$('#Monto').val('0,00').prop('readonly', this.checked).focus(); $('#Porcentaje').val('0,00').prop('readonly', !this.checked).focus(); setMontos();" /> Monto Porcentual
	        </td>
		</tr>
	    <tr>
			<td class="tagForm">Banco:</td>
			<td>
				<select name="CodBanco" id="CodBanco" style="width:200px;" class="disabled">
					<?=loadSelect2('mastbancos','CodBanco','Banco',$field['CodBanco'],1)?>
				</select>
			</td>
			<td class="tagForm">Porcentaje:</td>
			<td>
	        	<input type="text" name="Porcentaje" id="Porcentaje" value="0,00" style="width:150px; text-align:right;" class="currency" onchange="setMontos();" readonly />
	        </td>
		</tr>
	    <tr>
			<td class="tagForm">Nro. Cuenta:</td>
			<td>
				<select name="NroCuenta" id="NroCuenta" style="width:200px;" class="disabled">
					<?=loadSelect2('ap_ctabancaria','NroCuenta','NroCuenta',$field['NroCuenta'],1)?>
				</select>
			</td>
			<td class="tagForm">Saldo a Pagar:</td>
			<td>
	        	<input type="text" name="Monto" id="Monto" value="0,00" style="width:150px; text-align:right;" class="currency" onchange="setMontos();" />
	        </td>
		</tr>
	    <tr>
			<td class="tagForm">Tipo de Pago:</td>
			<td>
				<select name="CodTipoPago" id="CodTipoPago" style="width:200px;" class="disabled">
					<?=loadSelect2('masttipopago','CodTipoPago','TipoPago',$field['CodTipoPago'],1)?>
				</select>
			</td>
			<td class="tagForm">Monto Impuesto:</td>
			<td>
	        	<input type="text" name="MontoImpuesto" id="MontoImpuesto" value="<?=number_format($field['MontoImpuesto'], 2, ',', '.')?>" style="width:150px; text-align:right;" readonly />
	        </td>
		</tr>
	    <tr>
			<td class="tagForm"><strong>Saldo en Banco:</strong></td>
			<td>
	        	<input type="text" name="SaldoActual" id="SaldoActual" value="<?=number_format($field['SaldoActual'], 2, ',', '.')?>" style="width:150px; text-align:right; font-weight:bold;" readonly />
	        </td>
			<td class="tagForm">Monto Retenciones:</td>
			<td>
	        	<input type="text" name="MontoImpuestoOtros" id="MontoImpuestoOtros" value="<?=number_format($field['MontoImpuestoOtros'], 2, ',', '.')?>" style="width:150px; text-align:right;" readonly />
	        </td>
		</tr>
	    <tr>
			<td class="tagForm">&nbsp;</td>
			<td class="tagForm">&nbsp;</td>
			<td class="tagForm">Neto a Pagar:</td>
			<td>
	        	<input type="text" name="NetoPagar" id="NetoPagar" value="0,00" style="width:150px; text-align:right;" readonly />
	        </td>
		</tr>
	</table>
</div>

<div id="tab2" style="display:none;">
	<div style="width:<?=$_width?>px;" class="divFormCaption">Distribuci&oacute;n Pagos</div>
	<div style="overflow:scroll; width:<?=$_width?>px; height:241px; margin:auto;">
		<table style="width:100%; min-width:800px;" class="tblLista">
			<thead>
				<tr>
			        <th width="100">Monto Orden</th>
			        <th width="100">Impuesto</th>
			        <th width="100">Retenciones</th>
			        <th width="100">Monto Neto</th>
			        <th width="100">Saldo Pendiente</th>
			        <th width="100">Monto a Pagar</th>
			        <th width="100">Monto Pagado</th>
			        <th width="100">Monto Pendiente</th>
			    </tr>
		    </thead>
		    
		    <tbody id="lista_partidas">
			    <?php
				foreach ($field_pagos as $f) {
					?>
					<tr class="trListaBody">
						<td align="right"><?=number_format($f['MontoOrden'], 2, ',', '.')?></td>
						<td align="right"><?=number_format($f['MontoIva'], 2, ',', '.')?></td>
						<td align="right"><?=number_format($f['MontoRetenciones'], 2, ',', '.')?></td>
						<td align="right"><?=number_format($f['MontoNeto'], 2, ',', '.')?></td>
						<td align="right"><?=number_format($f['SaldoPendiente'], 2, ',', '.')?></td>
						<td align="right"><?=number_format($f['MontoPagar'], 2, ',', '.')?></td>
						<td align="right"><?=number_format($f['MontoPagado'], 2, ',', '.')?></td>
						<td align="right"><?=number_format($f['MontoPendiente'], 2, ',', '.')?></td>
					</tr>
					<?php
				}
				?>
		    </tbody>
		</table>
	</div>
</div>

<div id="tab3" style="display:none;">
	<div style="width:<?=$_width?>px;" class="divFormCaption">Distribuci&oacute;n Presupuestaria</div>
	<div style="overflow:scroll; width:<?=$_width?>px; height:241px; margin:auto;">
		<table width="100%" class="tblLista">
			<thead>
				<tr>
			        <th width="60">Cat. Prog.</th>
			        <th width="25">F.F</th>
			        <th width="75">Partida</th>
			        <th>Descripci&oacute;n</th>
			        <th width="100">Monto</th>
			    </tr>
		    </thead>
		    
		    <tbody id="lista_partidas">
		    <?php
		    $filtro = "";
		    if ($FlagPrimerPago != 'S') $filtro = " AND opd.cod_partida <> '".$_PARAMETRO['IVADEFAULT']."'";
			$sql = "SELECT
						opd.*,
						p.denominacion,
						pv.CategoriaProg,
						CONCAT(ss.CodSector, pr.CodPrograma, a.CodActividad) AS CatProg
					FROM
						ap_ordenpagodistribucion opd
						INNER JOIN pv_partida p ON (opd.cod_partida = p.cod_partida)
						LEFT JOIN pv_presupuesto pv On (opd.CodOrganismo = opd.CodOrganismo AND opd.CodPresupuesto = pv.CodPresupuesto)
						LEFT JOIN pv_categoriaprog cp ON (cp.CategoriaProg = pv.CategoriaProg)
						LEFT JOIN pv_actividades a ON (a.IdActividad = cp.IdActividad)
						LEFT JOIN pv_proyectos py ON (py.IdProyecto = a.IdProyecto)
						LEFT JOIN pv_subprogramas spr ON (spr.IdSubPrograma = py.IdSubPrograma)
						LEFT JOIN pv_programas pr ON (pr.IdPrograma = spr.IdPrograma)
						LEFT JOIN pv_subsector ss ON (ss.IdSubSector = pr.IdSubSector)
					WHERE
						opd.Anio = '".$Anio."' AND
						opd.CodOrganismo = '".$CodOrganismo."' AND
						opd.NroOrden = '".$NroOrden."' $filtro
					GROUP BY CodPresupuesto, CodFuente, cod_partida
					ORDER BY CodPresupuesto, CodFuente, cod_partida";
			$field_partidas = getRecords($sql);
			foreach ($field_partidas as $f) {
				if ((!count($field_pagos) && $_PARAMETRO['IVADEFAULT'] == $f['cod_partida']) || $FlagPrimerPago == 'S') {
					$Monto = $f['Monto'];
				} else {
					$sql = "SELECT SUM(MontoPagado)
							FROM ap_ordenpagodistribucion
							WHERE
								Anio = '".$Anio."' AND
								CodOrganismo = '".$CodOrganismo."' AND
								NroOrden = '".$NroOrden."' AND
								cod_partida = '".$f['cod_partida']."' AND
								CodPresupuesto = '".$f['CodPresupuesto']."' AND
								CodFuente = '".$f['CodFuente']."'";
					$Monto = $f['Monto'] - floatval(getVar3($sql));
				}
				?>
				<tr class="trListaBody">
		        	<td align="center"><?=$f['CatProg']?></td>
		        	<td align="center"><?=$f['CodFuente']?></td>
					<td align="center"><?=$f['cod_partida']?></td>
					<td><?=htmlentities($f['denominacion'])?></td>
					<td align="right">
						<input type="hidden" name="partidas_CodPresupuesto[]" id="partidas_CodPresupuesto<?=$id?>" value="<?=$f['CodPresupuesto']?>">
						<input type="hidden" name="partidas_CodFuente[]" id="partidas_CodFuente<?=$id?>" value="<?=$f['CodFuente']?>">
						<input type="hidden" name="partidas_cod_partida[]" id="partidas_cod_partida<?=$id?>" value="<?=$f['cod_partida']?>">
						<input type="hidden" name="partidas_MontoPendiente[]" id="partidas_MontoPendiente<?=$id?>" value="<?=$Monto?>">
						<input type="text" name="partidas_Monto[]" id="partidas_Monto<?=$id?>" value="<?=number_format($Monto, 2, ',', '.')?>" style="text-align:right;" class="cell currency">
					</td>
				</tr>
				<?php
			}
			?>
		    </tbody>
		</table>
	</div>
</div>

<center>
	<input type="submit" value="<?=$label_submit?>" style="width:80px; <?=$display_submit?>" id="btSubmit" />
	<input type="button" value="Cancelar" style="width:80px;" id="btCancelar" onclick="<?=$clkCancelar?>" />
</center>
</form>


<div style="width:<?=$_width?>px" class="divMsj">Campos Obligatorios *</div>

<script type="text/javascript">
	function setMontos() {
		var SaldoPendiente = new Number(setNumero($('#SaldoPendiente').val()) - setNumero($('#MontoImpuesto').val()) + Math.abs(setNumero($('#MontoImpuestoOtros').val())));

		if ($('#FlagPorcentual').prop('checked')) {
			var Porcentaje = new Number(setNumero($('#Porcentaje').val()));
			var Monto = SaldoPendiente * Porcentaje / 100;
		} else {
			var Monto = new Number(setNumero($('#Monto').val()));
			var Porcentaje = Monto * 100 / SaldoPendiente;
		}

		$('input[name="partidas_cod_partida[]"]').each(function(idx) {
			var cod_partida = $(this).val();
			var MontoPendiente = $('input[name="partidas_MontoPendiente[]"]:eq('+idx+')').val();
			if (cod_partida != "<?=$_PARAMETRO['IVADEFAULT']?>") {
				if (Monto > 0 || Porcentaje > 0) {
					var MontoPartida = MontoPendiente * Porcentaje / 100;
					$('input[name="partidas_Monto[]"]:eq('+idx+')').val(MontoPartida).formatCurrency();
				} else {
					$('input[name="partidas_Monto[]"]:eq('+idx+')').val(MontoPendiente).formatCurrency();
				}
			}
		});
		
		var MontoImpuesto = new Number(setNumero($('#MontoImpuesto').val()));
		var MontoImpuestoOtros = new Number(setNumero($('#MontoImpuestoOtros').val()));
		var NetoPagar = Monto + MontoImpuesto - Math.abs(MontoImpuestoOtros);
		$('#NetoPagar').val(NetoPagar).formatCurrency();

		$('#Monto').val(Monto).formatCurrency();
		$('#Porcentaje').val(Porcentaje).formatCurrency();
	}
</script>