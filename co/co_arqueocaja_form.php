<?php
if ($opcion == "nuevo") {
	$fFechaCobranzaD = $FechaActual;
	$fFechaCobranzaH = $FechaActual;
	$fCodOrganismo = (!empty($fCodOrganismo) ? $fCodOrganismo : $_SESSION["ORGANISMO_ACTUAL"]);
	##	
	$field['Estado'] = 'PR';
	$field['CodOrganismo'] = $fCodOrganismo;
	$field['Fecha'] = $FechaActual;
	$field['VoucherPeriodo'] = $PeriodoActual;
	$field['FechaPreparacion'] = $FechaActual;
	$field['PreparadoPor'] = $_SESSION["CODPERSONA_ACTUAL"];
	$field['NomPreparadoPor'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
	$field['FechaPreparado'] = $Ahora;
	##	
	$sql = "SELECT
				cbd.*,
				(CASE WHEN cbd.CtaBancariaPropia THEN cbd.CtaBancariaPropia ELSE 'LOCAL' END) AS NroCuentaPropia,
				cb.NroCobranza,
				cb.FechaCobranza,
				bco.Banco,
				tp.Descripcion AS TipoPago
			FROM co_cobranzadet cbd
			INNER JOIN co_cobranza cb ON cb.CodCobranza = cbd.CodCobranza
			LEFT JOIN mastbancos bco ON bco.CodBanco = cbd.CodBanco
			LEFT JOIN co_tipopago tp On tp.CodTipoPago = cbd.CodTipoPago
			WHERE
				cb.CodOrganismo = '$fCodOrganismo'
				AND cb.FechaCobranza >= '$fFechaCobranzaD'
				AND cb.FechaCobranza <= '$fFechaCobranzaH'
			ORDER BY CtaBancariaPropia, CodTipoPago, FechaCobranza, Secuencia";
	$field_detalle = getRecords($sql);
	##	
	$_titulo = "Arqueo de Caja / Nuevo Registro";
	$accion = "nuevo";
	$disabled_modificar = "";
	$disabled_ver = "";
	$read_modificar = "";
	$display_modificar = "";
	$display_ver = "";
	$display_submit = "";
	$label_submit = "Asignar Datos a Cobranzas";
	$focus = "btCancelar";
}
elseif ($opcion == "ver" || $opcion == "aprobar" || $opcion == "anular") {
	##	consulto datos generales
	$sql = "SELECT
				ac.*,
				p1.NomCompleto AS NomPreparadoPor,
				p2.NomCompleto AS NomAprobadoPor
			FROM co_arqueocaja ac
			LEFT JOIN mastpersonas p1 ON p1.CodPersona = ac.PreparadoPor
			LEFT JOIN mastpersonas p2 ON p2.CodPersona = ac.AprobadoPor
			WHERE ac.CodArqueo = '$sel_registros'";
	$field = getRecord($sql);
	##	
	$sql = "SELECT
				cbd.*,
				cbd.CtaBancariaPropia AS NroCuentaPropia,
				cb.NroCobranza,
				cb.FechaCobranza,
				bco.Banco,
				tp.Descripcion AS TipoPago
			FROM co_cobranzadet cbd
			INNER JOIN co_cobranza cb ON cb.CodCobranza = cbd.CodCobranza
			LEFT JOIN mastbancos bco ON bco.CodBanco = cbd.CodBanco
			LEFT JOIN co_tipopago tp On tp.CodTipoPago = cbd.CodTipoPago
			WHERE cbd.CodArqueo = '$field[CodArqueo]'
			ORDER BY CtaBancariaPropia, CodTipoPago, FechaCobranza, Secuencia";
	$field_detalle = getRecords($sql);
	##
	if ($opcion == "aprobar") {
		$field['Estado'] = 'AP';
		$field['AprobadoPor'] = $_SESSION["CODPERSONA_ACTUAL"];
		$field['NomAprobadoPor'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
		$field['FechaAprobado'] = $Ahora;
		##	
		$_titulo = "Arqueo de Caja / Aprobar Registro";
		$accion = "aprobar";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$read_modificar = "readonly";
		$display_modificar = "display:none;";
		$display_ver = "display:none;";
		$display_submit = "";
		$label_submit = "Aprobar";
		$focus = "btSubmit";
	}
	##
	elseif ($opcion == "anular") {
		$_titulo = "Arqueo de Caja / Anular Registro";
		$accion = "anular";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$read_modificar = "readonly";
		$display_modificar = "display:none;";
		$display_ver = "display:none;";
		$display_submit = "";
		$label_submit = "Anular";
		$focus = "btSubmit";
	}
	##
	elseif ($opcion == "ver") {
		$_titulo = "Arqueo de Caja / Ver Registro";
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
$sql = "SELECT * FROM co_fondocaja WHERE CodPersonaCajero = '$field[CodPersonaCajero]'";
$field_fondo = getRecord($sql);
$FondoCaja = floatval($field_fondo['Monto']);
$clkCancelar = "document.getElementById('frmentrada').submit();";
//	------------------------------------
$_width = 900;
if ($origen == 'framemain') $action = '../framemain.php';
elseif (!empty($origen)) $action = "gehen.php?anz=$origen";
else $action = "gehen.php?anz=co_arqueocaja_lista";
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="<?=$action?>" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('co_arqueocaja_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
	<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
	<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
	<input type="hidden" name="fFechaD" id="fFechaD" value="<?=$fFechaD?>" />
	<input type="hidden" name="fFechaH" id="fFechaH" value="<?=$fFechaH?>" />
	<input type="hidden" name="CodArqueo" id="CodArqueo" value="<?=$field['CodArqueo']?>" />

	<table style="width:100%; min-width:<?=$_width?>px;" class="tblForm">
		<tr>
	    	<td colspan="4" class="divFormCaption">Datos Generales</td>
	    </tr>
	    <tr>
			<td class="tagForm" width="150">* Organismo:</td>
			<td>
				<select name="CodOrganismo" id="CodOrganismo" style="width:295px;" <?=$disabled_modificar?>>
					<?=getOrganismos($field['CodOrganismo'], 3)?>
				</select>
			</td>
			<td class="tagForm" width="155">Nro. Arqueo:</td>
			<td>
	        	<input type="text" name="NroArqueo" id="NroArqueo" value="<?=$field['NroArqueo']?>" style="width:100px; font-weight:bold;" readonly />
			</td>
		</tr>
	    <tr>
			<th style="text-align: left; padding-left: 75px;" colspan="2">Datos para Asignar a las Cobranzas</th>
			<td class="tagForm">Estado:</td>
			<td>
				<input type="hidden" name="Estado" id="Estado" value="<?=$field['Estado']?>">
	        	<input type="text" value="<?=mb_strtoupper(printValores('cierre-caja-estado',$field['Estado']))?>" style="width:100px; font-weight:bold;" disabled />
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Cta. Bancaria:</td>
			<td>
				<select name="NroCuenta" id="NroCuenta" style="width:295px;" <?=$disabled_ver?>>
					<option value="">&nbsp;</option>
					<?=loadSelect2('ap_ctabancaria','NroCuenta','NroCuenta',$field['NroCuenta'])?>
				</select>
			</td>
			<td class="tagForm">* Fecha:</td>
			<td>
				<input type="text" name="Fecha" id="Fecha" value="<?=formatFechaDMA($field['Fecha'])?>" maxlength="10" style="width:100px;" class="datepicker" onkeyup="setFechaDMA(this);" <?=$disabled_ver?> />
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Doc. Referencia:</td>
			<td>
	        	<input type="text" name="DocReferencia" id="DocReferencia" value="<?=$field['DocReferencia']?>" style="width:295px;" maxlength="255" <?=$disabled_ver?> />
			</td>
			<td class="tagForm">Voucher Caja:</td>
			<td>
				<input type="text" name="VoucherPeriodo" id="VoucherPeriodo" value="<?=$field['VoucherPeriodo']?>" maxlength="7" style="width:100px;" />
				<input type="text" name="VoucherNro" id="VoucherNro" value="<?=$field['VoucherNro']?>" style="width:100px;" readonly />
			</td>
		</tr>
		<tr>
			<th style="text-align: right;">Usuarios</th>
			<td></td>
			<td class="tagForm">Voucher Anulación:</td>
			<td>
				<input type="text" name="VoucherAnulacion" id="VoucherAnulacion" value="<?=$field['VoucherAnulacion']?>" style="width:204px;" readonly />
			</td>
		</tr>
		<tr>
			<td class="tagForm">Preparado Por:</td>
			<td>
				<input type="hidden" name="PreparadoPor" id="PreparadoPor" value="<?=$field['PreparadoPor']?>" />
				<input type="text" name="NomPreparadoPor" id="NomPreparadoPor" value="<?=$field['NomPreparadoPor']?>" style="width:185px;" readonly />
				<input type="text" name="FechaPreparado" id="FechaPreparado" value="<?=$field['FechaPreparado']?>" style="width:106px;" readonly />
			</td>
			<td class="tagForm">Transacción CxP:</td>
			<td>
				<input type="text" name="NroTransaccionCxP" id="NroTransaccionCxP" value="<?=$field['NroTransaccionCxP']?>" style="width:204px;" readonly />
			</td>
		</tr>
		<tr>
			<td class="tagForm">Aprobado Por:</td>
			<td>
				<input type="hidden" name="AprobadoPor" id="AprobadoPor" value="<?=$field['AprobadoPor']?>" />
				<input type="text" name="NomAprobadoPor" id="NomAprobadoPor" value="<?=$field['NomAprobadoPor']?>" style="width:185px;" readonly />
				<input type="text" name="FechaAprobado" id="FechaAprobado" value="<?=$field['FechaAprobado']?>" style="width:106px;" readonly />
			</td>
		</tr>
		<tr>
			<td class="tagForm">&Uacute;ltima Modif.:</td>
			<td>
				<input type="text" value="<?=$field['UltimoUsuario']?>" style="width:146px;" disabled="disabled" />
				<input type="text" value="<?=$field['UltimaFecha']?>" style="width:145px" disabled="disabled" />
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<center>
					<input type="submit" value="&nbsp; &nbsp; <?=$label_submit?> &nbsp; &nbsp;" style="<?=$display_submit?>" id="btSubmit" />
					<input type="button" value="Cancelar" style="width:75px;" id="btCancelar" onclick="<?=$clkCancelar?>" />
				</center>
			</td>
		</tr>
	</table>
	<div class="sep"></div>

	<div style="width:100%; min-width:<?=$_width?>px; <?=$display_ver?>" class="divBorder">
		<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
			<tr>
				<td align="right" width="124">Organismo:</td>
				<td>
					<input type="checkbox" checked onclick="this.checked=!this.checked;" />
					<select name="fCodOrganismo" id="fCodOrganismo" style="width:295px;" <?=$dCodOrganismo?>>
						<?=getOrganismos($fCodOrganismo, 3);?>
					</select>
				</td>
				<td align="right" width="100">Cajero:</td>
				<td>
					<input type="checkbox" onclick="chkFiltro(this.checked, 'fCodPersonaCajero');" />
					<select name="fCodPersonaCajero" id="fCodPersonaCajero" style="width:295px;" disabled>
						<option value="">&nbsp;</option>
						<?=cajeros()?>
					</select>
				</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td align="right">Fecha Cobranza:</td>
				<td>
					<input type="checkbox" checked onclick="chkCampos2(this.checked, ['fFechaD','fFechaH']);" />
					<input type="text" name="fFechaCobranzaD" id="fFechaCobranzaD" value="<?=formatFechaDMA($fFechaCobranzaD)?>" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" /> -
		            <input type="text" name="fFechaCobranzaH" id="fFechaCobranzaH" value="<?=formatFechaDMA($fFechaCobranzaH)?>" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" />
		        </td>
				<td align="right">Cobrador:</td>
				<td>
					<input type="checkbox" onclick="chkFiltro(this.checked, 'fCodPersonaCobrador');" />
					<select name="fCodPersonaCobrador" id="fCodPersonaCobrador" style="width:295px;" disabled>
						<option value="">&nbsp;</option>
						<?=cajeros()?>
					</select>
				</td>
		        <td align="right"><input type="button" value="Buscar" onclick="detalle_filtrar();"></td>
			</tr>
		</table>
	</div>
	<div class="sep"></div>

	<input type="hidden" id="sel_detalle" />
	<table style="width:100%; min-width:<?=$_width?>px;" class="tblBotones">
		<thead>
			<tr>
				<th class="divFormCaption" colspan="2">DETALLES</th>
			</tr>
		</thead>
	</table>
	<div style="overflow:scroll; height:230px; width:100%; min-width:<?=$_width?>px; margin:auto;">
		<table class="tblLista" style="width:100%; min-width:1200px;">
			<thead>
				<tr>
					<th width="75">Fecha</th>
					<th width="75">Cobranza #</th>
					<th width="50">Moneda Pago</th>
					<th width="125">Monto</th>
					<th width="100">Arqueo Doc.</th>
					<th width="150">Doc. Referencia</th>
					<th width="150">Cta. Cliente</th>
					<th align="left">Bco. Cliente</th>
				</tr>
			</thead>
			
			<tbody id="lista_detalle">
				<?php
				$nro_detalle = 0;
				$Grupo1 = '';
				$Grupo2 = '';
				foreach ($field_detalle as $f)
				{
					$id = ++$nro_detalle;
					##	
					if ($Grupo1 != $f['NroCuentaPropia'])
					{
						?>
						<tr class="trListaBody2">
							<td colspan="3">
								<?=htmlentities($f['NroCuentaPropia'])?>
							</td>
							<td>
								<input type="text" name="detalle_TotalCuenta[]" id="CTA_<?=$f['NroCuentaPropia']?>" value="0,00" class="cell2 CTA_<?=$f['NroCuentaPropia']?>" data-cta="<?=$f['NroCuentaPropia']?>" style="text-align:right; font-weight: bold;" readonly>
							</td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<?php
						$Grupo1 = $f['NroCuentaPropia'];
						$Grupo2 = '';

					}
					if ($Grupo2 != $f['CodTipoPago'])
					{
						?>
						<tr class="trListaBody3">
							<td colspan="3">
								<?=htmlentities($f['TipoPago'])?>
							</td>
							<td>
								<input type="text" name="detalle_TotalTipoPago[]" id="TP_<?=$f['CodTipoPago']?>" value="0,00" class="cell2 CTA_<?=$f['NroCuentaPropia']?> TP_<?=$f['CodTipoPago']?>" data-tp="<?=$f['CodTipoPago']?>" style="text-align:right; font-weight: bold;" readonly>
							</td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<?php
						$Grupo2 = $f['CodTipoPago'];
					}
					?>
					<?php if ($opcion == 'nuevo') { ?>
						<tr class="trListaBody" onclick="clkMulti($(this), 'detalle_<?=$id?>'); setMontosArqueo();">
					<?php } else { ?>
						<tr class="trListaBody">
					<?php } ?>
						<td align="center">
							<input type="checkbox" name="detalle[]" id="detalle_<?=$id?>" value="<?=$id?>" style="display:none;" data-cta="<?=$f['NroCuentaPropia']?>" data-tp="<?=$f['CodTipoPago']?>" <?php echo ($opcion != 'nuevo')?'checked':'' ?> />
							<input type="hidden" name="detalle_CodCobranza[]" value="<?=$f['CodCobranza']?>" disabled>
							<input type="hidden" name="detalle_Secuencia[]" value="<?=$f['Secuencia']?>" disabled>
							<input type="hidden" name="detalle_MontoLocal[]" value="<?=$f['MontoLocal']?>" class="CTA_<?=$f['NroCuentaPropia']?> TP_<?=$f['CodTipoPago']?>">
							<?=formatFechaDMA($f['FechaCobranza'])?>
						</td>
						<td align="center"><?=$f['NroCobranza']?></td>
						<td align="center"><?=printValoresGeneral("monedas", $f['MonedaDocumento'])?></td>
						<td align="right"><?=number_format($f['MontoLocal'],2,',','.')?></td>
						<td align="center"><?=$f['ArqueoNro']?></td>
						<td align="center"><?=$f['ArqueoDocReferencia']?></td>
						<td align="center"><?=$f['CtaBancaria']?></td>
						<td><?=$f['Banco']?></td>
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
<div style="width:100%; min-width:<?=$_width?>px;" class="divMsj">Campos Obligatorios *</div>

<script type="text/javascript">
	function setMontosArqueo() {
		var i = 0;
		var TotalCuenta = 0;
		$('input[name="detalle_TotalCuenta[]"]').each(function(idxTC) {
			var detalle_TotalCuenta = $(this);
			var cta = $(this).attr('data-cta');

			$('input[name="detalle_TotalTipoPago[]"].CTA_'+cta).each(function(idxTP) {
				var detalle_TotalTipoPago = $(this);
				var tp = $(this).data('tp');

				var TotalTipoPago = 0;
				$('input[name="detalle_MontoLocal[]"].CTA_'+cta+'.TP_'+tp).each(function(idx) {
					var detalle_MontoLocal = $(this);
					var MontoLocal = new Number(detalle_MontoLocal.val());
					var detalle = $('input[name="detalle[]"]:eq('+i+')').prop('checked');
					if (detalle) TotalTipoPago = TotalTipoPago + MontoLocal;

					$('input[name="detalle_CodCobranza[]"]:eq('+i+')').prop('disabled', !detalle);
					$('input[name="detalle_Secuencia[]"]:eq('+i+')').prop('disabled', !detalle);
					++i;
				});
				detalle_TotalTipoPago.val(TotalTipoPago).formatCurrency();
				TotalCuenta = TotalCuenta + TotalTipoPago;
			});

			detalle_TotalCuenta.val(TotalCuenta).formatCurrency();
		});
	}
	function detalle_filtrar() {
		$.post('co_arqueocaja_ajax.php', "modulo=ajax&accion=detalle_filtrar&"+$('#frmentrada').serialize(), function(data) {
			$('#lista_detalle').html(data);
			var nro = $('input[name="detalle[]"]').length;
			$('#nro_detalle').val(nro);
			$('#can_detalle').val(nro);
	    });
	}
	<?php if ($opcion != 'nuevo') { ?>
		$(document).ready(function() {
			setMontosArqueo();
		});
	<?php } ?>
</script>