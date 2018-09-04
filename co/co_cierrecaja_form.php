<?php
if ($opcion == "nuevo") {
	$field['Estado'] = 'PR';
	$field['FechaCierre'] = $FechaActual;
	$field['FechaPreparacion'] = $FechaActual;
	$field['PreparadoPor'] = $_SESSION["CODPERSONA_ACTUAL"];
	$field['NomPreparadoPor'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
	$field['FechaPreparado'] = $Ahora;
	$field_detalle = [];
	##
	$_titulo = "Cierre de Caja / Nuevo Registro";
	$accion = "nuevo";
	$disabled_modificar = "";
	$disabled_ver = "";
	$read_modificar = "";
	$display_modificar = "";
	$display_ver = "";
	$display_submit = "";
	$label_submit = "Guardar";
	$focus = "CodPersona";
}
elseif ($opcion == "modificar" || $opcion == "ver" || $opcion == "aprobar" || $opcion == "anular") {
	##	consulto datos generales
	$sql = "SELECT
				cc.*,
				p1.NomCompleto AS NomPreparadoPor,
				p2.NomCompleto AS NomAprobadoPor
			FROM co_cierrecaja cc
			LEFT JOIN mastpersonas p1 ON p1.CodPersona = cc.PreparadoPor
			LEFT JOIN mastpersonas p2 ON p2.CodPersona = cc.AprobadoPor
			WHERE cc.CodCierre = '$sel_registros'";
	$field = getRecord($sql);
	##	
	$sql = "SELECT
				ccd.*,
				p.NomCompleto AS NombreCliente
			FROM co_cierrecajadetalle ccd
			LEFT JOIN mastpersonas p ON p.CodPersona = ccd.CodPersonaCliente
			WHERE CodCierre = $field[CodCierre]";
	$field_detalle = getRecords($sql);
	##
	if ($opcion == "modificar") {
		$_titulo = "Cierre de Caja / Modificar Registro";
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
	elseif ($opcion == "aprobar") {
		$field['Estado'] = 'AP';
		$field['AprobadoPor'] = $_SESSION["CODPERSONA_ACTUAL"];
		$field['NomAprobadoPor'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
		$field['FechaAprobado'] = $Ahora;
		##	
		$_titulo = "Cierre de Caja / Aprobar Registro";
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
		$_titulo = "Cierre de Caja / Anular Registro";
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
		$_titulo = "Cierre de Caja / Ver Registro";
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
else $action = "gehen.php?anz=co_cierrecaja_lista";
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="<?=$action?>" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('co_cierrecaja_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
	<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
	<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
	<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
	<input type="hidden" name="fCodEstablecimiento" id="fCodEstablecimiento" value="<?=$fCodEstablecimiento?>" />
	<input type="hidden" name="fFechaCierreD" id="fFechaCierreD" value="<?=$fFechaCierreD?>" />
	<input type="hidden" name="fFechaCierreH" id="fFechaCierreH" value="<?=$fFechaCierreH?>" />
	<input type="hidden" name="fCodPersonaCajero" id="fCodPersonaCajero" value="<?=$fCodPersonaCajero?>" />
	<input type="hidden" name="CodCierre" id="CodCierre" value="<?=$field['CodCierre']?>" />

	<table style="width:100%; min-width:<?=$_width?>px;" class="tblForm">
		<tr>
	    	<td colspan="4" class="divFormCaption">Datos Generales</td>
	    </tr>
	    <tr>
			<td class="tagForm">* Organismo:</td>
			<td>
				<select name="CodOrganismo" id="CodOrganismo" style="width:295px;" <?=$disabled_modificar?>>
					<?=getOrganismos($field['CodOrganismo'], 3)?>
				</select>
			</td>
			<td class="tagForm">Nro. Cierre:</td>
			<td>
	        	<input type="text" name="NroCierre" id="NroCierre" value="<?=$field['NroCierre']?>" style="width:100px; font-weight:bold;" readonly />
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Establecimiento:</td>
			<td>
				<select name="CodEstablecimiento" id="CodEstablecimiento" style="width:295px;" <?=$disabled_modificar?>>
					<?=loadSelect2('co_establecimientofiscal','CodEstablecimiento','Descripcion',$field['CodEstablecimiento'])?>
				</select>
			</td>
			<td class="tagForm">Estado:</td>
			<td>
				<input type="hidden" name="Estado" id="Estado" value="<?=$field['Estado']?>">
	        	<input type="text" value="<?=mb_strtoupper(printValores('cierre-caja-estado',$field['Estado']))?>" style="width:100px; font-weight:bold;" disabled />
			</td>
		</tr>
		<tr>
			<td class="tagForm">Cajero:</td>
			<td>
				<select name="CodPersonaCajero" id="CodPersonaCajero" style="width:295px;" onchange="fondo_caja(this.value);" <?=$disabled_ver?>>
					<option value="">&nbsp;</option>
					<?=cajeros($field['CodPersonaCajero'])?>
				</select>
			</td>
			<td class="tagForm">* Fecha:</td>
			<td>
				<input type="text" name="FechaCierre" id="FechaCierre" value="<?=formatFechaDMA($field['FechaCierre'])?>" maxlength="10" style="width:75px;" class="datepicker" onkeyup="setFechaDMA(this);" <?=$disabled_ver?> />
			</td>
		</tr>
		<tr>
			<td class="tagForm" rowspan="3">Comentarios:</td>
			<td rowspan="3">
				<textarea name="Comentarios" id="Comentarios" style="width:295px; height:65px;" <?=$disabled_ver?>><?=htmlentities($field['Comentarios'])?></textarea>
			</td>
			<th style="text-align: right;">Total Cierre</th>
			<th></th>
		</tr>
	    <tr>
			<td class="tagForm">Efectivo:</td>
			<td>
				<input type="text" name="TotalEfectivo" id="TotalEfectivo" value="<?=number_format($field['TotalEfectivo'],2,',','.')?>" style="width:125px; text-align: right; font-weight: bold; font-size: 12px;" class="currency" readonly />
			</td>
		</tr>
	    <tr>
			<td class="tagForm">Fondo de Caja:</td>
			<td>
				<input type="text" name="FondoCaja" id="FondoCaja" value="<?=number_format($FondoCaja,2,',','.')?>" style="width:125px; text-align: right;" class="currency" readonly />
			</td>
		</tr>
		<tr>
			<th style="text-align: right;">Usuarios</th>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td class="tagForm">Preparado Por:</td>
			<td colspan="3">
				<input type="hidden" name="PreparadoPor" id="PreparadoPor" value="<?=$field['PreparadoPor']?>" />
				<input type="text" name="NomPreparadoPor" id="NomPreparadoPor" value="<?=$field['NomPreparadoPor']?>" style="width:185px;" readonly />
				<input type="text" name="FechaPreparado" id="FechaPreparado" value="<?=$field['FechaPreparado']?>" style="width:106px;" readonly />
			</td>
		</tr>
		<tr>
			<td class="tagForm">Aprobado Por:</td>
			<td colspan="3">
				<input type="hidden" name="AprobadoPor" id="AprobadoPor" value="<?=$field['AprobadoPor']?>" />
				<input type="text" name="NomAprobadoPor" id="NomAprobadoPor" value="<?=$field['NomAprobadoPor']?>" style="width:185px;" readonly />
				<input type="text" name="FechaAprobado" id="FechaAprobado" value="<?=$field['FechaAprobado']?>" style="width:106px;" readonly />
			</td>
		</tr>
	    <tr>
			<td class="tagForm">&Uacute;ltima Modif.:</td>
			<td colspan="3">
				<input type="text" value="<?=$field['UltimoUsuario']?>" style="width:146px;" disabled="disabled" />
				<input type="text" value="<?=$field['UltimaFecha']?>" style="width:145px" disabled="disabled" />
			</td>
		</tr>
	</table>

	<center>
		<input type="submit" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" id="btSubmit" />
		<input type="button" value="Cancelar" style="width:75px;" id="btCancelar" onclick="<?=$clkCancelar?>" />
	</center>

	<input type="hidden" id="sel_detalle" />
	<table style="width:100%; min-width:<?=$_width?>px;" class="tblBotones">
		<thead>
			<tr>
				<th class="divFormCaption" colspan="2">DETALLES</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="gallery clearfix">
					<a id="a_cobranza" href="gehen.php?anz=co_cobranza_form&opcion=ver&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" style="display:none;"></a>

					<input type="button" value="Ver Cobranza" style="width:80px;" onclick="ver_cobranza();" />
				</td>
				<td align="right">
					<input type="button" value="Importar Cobranza" id="btn-importar" style="width:115px;" onclick="importar_cobranza();" <?=$disabled_ver?> />

					<input type="button" class="btLista" value="Insertar" onclick="insertar2(this, 'detalle', 'modulo=ajax&accion=detalle_insertar', 'co_cierrecaja_ajax.php');" <?=$disabled_ver?> />
					<input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'detalle'); setMontosCierre();" <?=$disabled_ver?> />
				</td>
			</tr>
		</tbody>
	</table>
	<div style="overflow:scroll; height:230px; width:100%; min-width:<?=$_width?>px; margin:auto;">
		<table class="tblLista" style="width:100%; min-width:2000px;">
			<thead>
				<tr>
					<th width="20">#</th>
					<th width="100">Tipo de Pago</th>
					<th width="150" align="left">Concepto</th>
					<th width="50">Moneda</th>
					<th width="125">Importe</th>
					<th width="125">Importe Local</th>
					<th align="left">Comentarios</th>
					<th width="33">Tipo Doc.</th>
					<th width="60">Nro. Doc.</th>
					<th width="60">Proveedor</th>
					<th align="left">Razón Social</th>
					<th width="125">Importe Afecto</th>
					<th width="125">Impuesto</th>
					<th width="75">Referencia</th>
					<th width="20">#</th>
				</tr>
			</thead>
			
			<tbody id="lista_detalle">
				<?php
				$nro_detalle = 0;
				$TotalMontoLocal = 0;
				foreach ($field_detalle as $f)
				{
					$id = ++$nro_detalle;
					$TotalMontoLocal += $f['MontoLocal'];
					?>
					<tr class="trListaBody" onclick="clk($(this), 'detalle', 'detalle_<?=$id?>');" id="detalle_<?=$id?>">
						<th>
							<input type="hidden" name="detalle_Secuencia[]" id="detalle_Secuencia<?=$id?>" value="<?=$f['Secuencia']?>">
							<?=$nro_detalle?>
						</th>
						<td>
							<select name="detalle_CodTipoPago[]" class="cell" onchange="setMontosCierre();" <?=$disabled_ver?>>
								<?=loadSelect2('co_tipopago','CodTipoPago','Descripcion',$f['CodTipoPago'],1)?>
							</select>
						</td>
						<td>
							<select name="detalle_CodConceptoCaja[]" class="cell" <?=$disabled_ver?>>
								<?=co_conceptocaja($f['CodConceptoCaja'])?>
							</select>
						</td>
						<td>
							<select name="detalle_MonedaDocumento[]" class="cell" <?=$disabled_ver?>>
								<?=loadSelectGeneral("monedas", $f['MonedaDocumento'])?>
							</select>
						</td>
						<td>
							<input type="text" name="detalle_MontoLocal[]" value="<?=number_format($f['MontoLocal'],2,',','.')?>" class="cell currency" style="text-align:right;" onchange="setMontosCierre();" <?=$disabled_ver?>>
						</td>
						<td>
							<input type="text" name="detalle_MontoOriginal[]" value="<?=number_format($f['MontoOriginal'],2,',','.')?>" class="cell2" style="text-align:right;" readonly>
						</td>
						<td>
							<input type="text" name="detalle_Comentarios[]" value="<?=$f['Comentarios']?>" class="cell" <?=$disabled_ver?>>
						</td>
						<td>
							<select name="detalle_CodTipoDocumento[]" class="cell2">
								<?=loadSelect2('co_tipodocumento','CodTipoDocumento','Descripcion',$f['CodTipoDocumento'],11)?>
							</select>
						</td>
						<td>
							<input type="hidden" name="detalle_CodDocumento[]" value="<?=$f['CodDocumento']?>">
							<input type="text" name="detalle_NroDocumento[]" value="<?=$f['NroDocumento']?>" class="cell" style="text-align:center;" maxlength="10" readonly>
						</td>
						<td>
							<input type="text" name="detalle_CodPersonaCliente[]" value="<?=$f['CodPersonaCliente']?>" class="cell2" style="text-align:center;" readonly>
						</td>
						<td>
							<input type="text" name="detalle_NomPersonaCliente[]" value="<?=$f['NombreCliente']?>" class="cell2" disabled>
						</td>
						<td>
							<input type="text" name="detalle_MontoAfecto[]" value="<?=number_format($f['MontoAfecto'],2,',','.')?>" class="cell2" style="text-align:right;" readonly>
						</td>
						<td>
							<input type="text" name="detalle_MontoImpuesto[]" value="<?=number_format($f['MontoImpuesto'],2,',','.')?>" class="cell2" style="text-align:right;" readonly>
						</td>
						<td>
							<input type="hidden" name="detalle_RefCodCobranza[]" value="<?=$f['RefCodCobranza']?>">
							<input type="text" name="detalle_RefCobranza[]" value="<?=$f['RefCobranza']?>" class="cell2" style="text-align:center;" readonly="readonly">
						</td>
						<td>
							<input type="text" name="detalle_RefSecuencia[]" value="<?=$f['RefSecuencia']?>" class="cell2" style="text-align:center;" readonly="readonly">
						</td>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
	</div>
	<table style="width:100%; min-width:<?=$_width?>px;" class="tblBotones">
	    <tbody>
		    <tr>
		        <td width="600" align="right">
		        	<strong>Total Cierre: </strong>
		        	<input type="text" name="TotalMontoLocal" id="TotalMontoLocal" value="<?=number_format($TotalMontoLocal,2,',','.')?>" style="width:125px; font-weight:bold; text-align:right;" disabled>
		        </td>
		        <td></td>
		    </tr>
	    </tbody>
	</table>

	<input type="hidden" id="nro_detalle" value="<?=$nro_detalle?>" />
	<input type="hidden" id="can_detalle" value="<?=$nro_detalle?>" />
</form>
<div style="width:100%; min-width:<?=$_width?>px;" class="divMsj">Campos Obligatorios *</div>

<script type="text/javascript">
	function importar_cobranza() {
		var boton = $('#btn-importar');
		var detalle = 'detalle';
		var valores = 'modulo=ajax&accion=cobranza_insertar&FechaCierre='+$('#FechaCierre').val();
		var url = 'co_cierrecaja_ajax.php';
		boton.disabled = true;
		//	ajax
		$.ajax({
			type: "POST",
			url: url,
			data: "nro_detalle=0&can_detalle=0&"+valores,
			async: true,
			success: function(resp) {
				$('#lista_detalle').html(resp);
				inicializar();
				//	
				var nro_detalle = $('#lista_detalle tr').length;
				var can_detalle = nro_detalle;
				$('#nro_detalle').val(nro_detalle);
				$('#can_detalle').val(can_detalle);
				boton.disabled = false;
				setMontosCierre();
			}
		});
	}
	function fondo_caja(CodPersonaCajero) {
		$.post('co_cierrecaja_ajax.php', { 'CodPersonaCajero':CodPersonaCajero, 'modulo':'ajax', 'accion':'fondo_caja' }, function(data) {
			$('#FondoCaja').val(data).formatCurrency();
	    });
	}
	function setMontosCierre() {
		var TotalMontoOriginal = 0;
		var TotalMontoLocal = 0;
		var TotalEfectivo = 0;
		//	
		$('input[name="detalle_Secuencia[]"]').each(function(idx) {
			var detalle_CodTipoPago = $('select[name="detalle_CodTipoPago[]"]:eq('+idx+')').val();
			var detalle_MontoLocal = setNumero($('input[name="detalle_MontoLocal[]"]:eq('+idx+')').val());
			var detalle_MontoOriginal = setNumero($('input[name="detalle_MontoOriginal[]"]:eq('+idx+')').val());
			//	
			if (detalle_CodTipoPago == 'EF') TotalEfectivo += detalle_MontoLocal;
			TotalMontoOriginal += detalle_MontoOriginal;
			TotalMontoLocal += detalle_MontoLocal;
		});
		$('#TotalEfectivo').val(TotalEfectivo).formatCurrency();
		$('#TotalMontoLocal').val(TotalMontoLocal).formatCurrency();
	}
	function ver_cobranza() {
		var sel_detalle = $('#sel_detalle').val();
		var res = sel_detalle.split("_");
		var nro = res[1];
		var i = 0;
		var CodCobranza = '';

		$('input[name="detalle_Secuencia[]"]').each(function(idx) {
			var id = 'detalle_Secuencia' + nro;
			if ($('input[name="detalle_Secuencia[]"]:eq('+idx+')').attr('id') == id) {
				i = idx;
				CodCobranza = $('input[name="detalle_RefCodCobranza[]"]:eq('+idx+')').val();
			}
		});

		var href = "gehen.php?anz=co_cobranza_form&opcion=ver_modal&sel_registros="+CodCobranza+"&iframe=true&width=100%&height=100%";
		$('#a_cobranza').attr('href', href);
		$('#a_cobranza').click();
	}
</script>