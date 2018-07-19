<?php
if ($opcion == "nuevo") {
	$field['CodOrganismo'] = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$field['FechaCobranza'] = $FechaActual;
	##	
	$_titulo = "Cobranza de Documentos";
	$accion = "nuevo";
	$disabled_modificar = "";
	$disabled_ver = "";
	$read_modificar = "";
	$display_modificar = "";
	$display_ver = "";
	$display_submit = "";
	$label_submit = "Procesar";
	$focus = "btSubmit";
}
elseif ($opcion == "modificar" || $opcion == "ver" || $opcion == "ver_modal" || $opcion == "aprobar") {
	##	consulto datos generales
	$sql = "SELECT
				co.*,
				p1.CodPersona AS CodPersonaCliente,
				p1.DocFiscal AS DocFiscalCliente,
				p1.NomCompleto AS NombreCliente
			FROM co_cobranza co
			INNER JOIN mastpersonas p1 ON p1.CodPersona = co.CodPersonaCliente
			WHERE co.CodCobranza = '$sel_registros'";
	$field = getRecord($sql);
	##	documentos
	$sql = "SELECT
				do.*,
				td.Descripcion AS TipoDocumento,
				sf.CodSerie,
				sf.NroSerie,
				md1.Descripcion AS NomFormaFactura,
				md2.Descripcion AS NomTipoVenta
			FROM co_documentocobranza doco
			INNER JOIN co_documento do ON do.CodDocumento = doco.CodDocumento
			INNER JOIN co_tipodocumento td ON td.CodTipoDocumento = do.CodTipoDocumento
			INNER JOIN co_establecimientofiscal ef ON ef.CodOrganismo = do.CodOrganismo
			INNER JOIN co_seriefiscal sf ON (
				sf.CodOrganismo = ef.CodOrganismo
				AND sf.CodEstablecimiento = ef.CodEstablecimiento
			)
			LEFT JOIN mastmiscelaneosdet md1 ON (
				md1.CodDetalle = do.FormaFactura
				AND md1.CodMaestro = 'FORMAFACT'
			)
			LEFT JOIN mastmiscelaneosdet md2 ON (
				md2.CodDetalle = do.TipoVenta
				AND md2.CodMaestro = 'TIPOVENTA'
			)
			WHERE doco.CodCobranza = '$field[CodCobranza]'";
	$field_documento = getRecords($sql);
	##	cobranzas
	$sql = "SELECT
				cod.*
			FROM co_cobranzadet cod
			WHERE cod.CodCobranza = '$field[CodCobranza]'";
	$field_cobranza = getRecords($sql);
	##
	if ($opcion == "modificar") {
		$_titulo = "Cobranza de Documentos / Modificar";
		$accion = "modificar";
		$disabled_modificar = "disabled";
		$disabled_ver = "";
		$read_modificar = "readonly";
		$display_modificar = "display:none;";
		$display_ver = "";
		$display_submit = "";
		$label_submit = "Modificar";
		$focus = "btSubmit";
	}
	##
	elseif ($opcion == "aprobar") {
		$field['Estado'] = 'AP';
		$field['AprobadoPor'] = $_SESSION["CODPERSONA_ACTUAL"];
		$field['NomAprobadoPor'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
		$field['FechaAprobado'] = $Ahora;
		##	
		$_titulo = "Cobranza de Documentos / Aprobar";
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
	elseif ($opcion == "ver" || $opcion == "ver_modal") {
		$_titulo = "Cobranza de Documentos / Ver";
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
$clkCancelar = "document.getElementById('frmentrada').submit();";
//	------------------------------------
$_width = 700;
if ($origen == 'framemain') $action = '../framemain.php';
elseif (!empty($origen)) $action = "gehen.php?anz=$origen";
else $action = "gehen.php?anz=co_cobranza_lista";
?>
<?php if ($opcion != 'ver_modal') { ?>
	<table width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td class="titulo"><?=$_titulo?></td>
			<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
		</tr>
	</table><hr width="100%" color="#333333" />
<?php } ?>

<form name="frmentrada" id="frmentrada" action="<?=$action?>" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('co_cobranza_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
	<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
	<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
	<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
	<input type="hidden" name="fFechaPreparadoD" id="fFechaPreparadoD" value="<?=$fFechaPreparadoD?>" />
	<input type="hidden" name="fFechaPreparadoH" id="fFechaPreparadoH" value="<?=$fFechaPreparadoH?>" />
	<input type="hidden" name="fDocFiscalCliente" id="fDocFiscalCliente" value="<?=$fDocFiscalCliente?>" />
	<input type="hidden" name="fCodPersonaCliente" id="fCodPersonaCliente" value="<?=$fCodPersonaCliente?>" />
	<input type="hidden" name="fNombreCliente" id="fNombreCliente" value="<?=htmlentities($fNombreCliente)?>" />
	<input type="hidden" name="fFechaCobranzaD" id="fFechaCobranzaD" value="<?=$fFechaCobranzaD?>" />
	<input type="hidden" name="fFechaCobranzaH" id="fFechaCobranzaH" value="<?=$fFechaCobranzaH?>" />
	<input type="hidden" name="fCodPersonaCajero" id="fCodPersonaCajero" value="<?=$fCodPersonaCajero?>" />
	<input type="hidden" name="CodCobranza" id="CodCobranza" value="<?=$field['CodCobranza']?>" />
	<input type="hidden" name="Estado" id="Estado" value="<?=$field['Estado']?>" />

	<!--FILTRO-->
	<table style="width:100%; min-width:<?=$_width?>px;" class="tblForm">
		<tr>
			<td align="right">* Organismo:</td>
			<td>
				<select name="CodOrganismo" id="CodOrganismo" style="width:275px;" <?=$disabled_ver?>>
					<?=getOrganismos($CodOrganismo, 3);?>
				</select>
			</td>
			<td align="right">Cajero:</td>
			<td>
				<select name="CodPersonaCajero" id="CodPersonaCajero" style="width:275px;" <?=$disabled_ver?>>
					<option value="">&nbsp;</option>
					<?=cajeros($field['CodPersonaCajero'])?>
				</select>
			</td>
			<td align="right">* Fecha Cobranza:</td>
			<td>
				<input type="text" name="FechaCobranza" id="FechaCobranza" value="<?=formatFechaDMA($field['FechaCobranza'])?>" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" <?=$disabled_ver?> />
	        </td>
		</tr>
		<tr>
			<td align="right">* Cliente:</td>
			<td class="gallery clearfix">
				<input type="hidden" name="DocFiscalCliente" id="DocFiscalCliente" value="<?=$field['DocFiscalCliente']?>">
				<input type="hidden" name="CodPersonaCliente" id="CodPersonaCliente" value="<?=$field['CodPersonaCliente']?>" />
				<input type="text" name="NombreCliente" id="NombreCliente" style="width:275px;" value="<?=htmlentities($field['NombreCliente'])?>" readonly />
				<a href="../lib/listas/gehen.php?anz=lista_personas&campo1=CodPersonaCliente&campo2=NombreCliente&campo3=DocFiscalCliente&campo4=CodPersonaCajero&campo5=CodPersonaCobrador&ventana=co_cobranza&filtrar=default&FlagClasePersona=S&fEsCliente=S&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" id="aCodPersonaCliente" style=" <?=$display_modificar?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
			<td align="right">Cobrador:</td>
			<td>
				<select name="CodPersonaCobrador" id="CodPersonaCobrador" style="width:275px;" <?=$disabled_ver?>>
					<option value="">&nbsp;</option>
					<?=cajeros($field['CodPersonaCobrador'])?>
				</select>
			</td>
		</tr>
	</table>

	<?php if ($opcion != 'ver_modal') { ?>
		<center>
			<input type="submit" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" id="btSubmit" />
			<input type="button" value="Cancelar" style="width:75px;" id="btCancelar" onclick="<?=$clkCancelar?>" />
		</center>
		<br>
	<?php } ?>

	<!--REGISTROS-->
	<input type="hidden" name="sel_documento" id="sel_documento" />
	<table class="tblBotones" style="width:100%; min-width:<?=$_width?>px;">
	    <tr>
	        <td>Documentos a Cancelar</td>
	        <td align="right" class="gallery clearfix">
				<a id="a_documento" href="../lib/listas/gehen.php?anz=lista_co_documento&filtrar=default&fCodPersonaCliente=&fFlagCobranza=S&ventana=listado_insertar_linea_cobranza&detalle=documento&modulo=ajax&accion=documento_insertar&url=../../co/co_cobranza_ajax.php&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe2]" style="display:none;"></a>
	            
	            <input type="button" value="Insertar" style="width:75px;" onclick="insertar_documento();" <?=$disabled_modificar?> />
	            <input type="button" value="Modificar" style="width:75px;" disabled="disabled" />
	            <input type="button" value="Eliminar" style="width:75px;" onclick="quitar(this, 'documento'); setMontosDocumentos();" <?=$disabled_modificar?> />
	            <input type="button" value="Ver Documento" style="width:100px;" disabled="disabled" />
	        </td>
	    </tr>
	</table>
	<div style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:200px;">
		<table class="tblLista" style="width:100%; min-width:2500px;">
			<thead>
			    <tr>
			        <th width="35">Tipo</th>
			        <th width="35">Serie</th>
			        <th width="90">Documento</th>
			        <th width="90"># Interno</th>
			        <th width="90">Fecha Doc.</th>
			        <th width="90">Fecha Venc.</th>
			        <th width="125">Monto Total</th>
			        <th width="125">Monto Pendiente</th>
			        <th width="125">Monto Cobrado</th>
			        <th width="125">Monto x Cobrar</th>
			        <th width="90">Recibo #</th>
			        <th style="min-width: 300px;" align="left">Comentario</th>
			    </tr>
		    </thead>
		    
		    <tbody id="lista_documento">
		    	<?php
		    	$MontoTotal = 0;
		    	$MontoPendiente = 0;
		    	$MontoCobrado = 0;
		    	$MontoPorCobrar = 0;
		    	$nro_documento = 0;
				foreach ($field_documento as $f)
				{
					++$nro_documento;
					$id = $f['CodDocumento'];
					if ($field['Estado'] == 'PR')
					{
						$MontoPendiente = $f['MontoTotal'] - $f['MontoPagado'];
						$MontoCobrado = $MontoPendiente;
						$MontoPorCobrar = $MontoPendiente - $MontoCobrado;
					}
					else
					{
						$MontoPendiente = $f['MontoTotal'];
						$MontoCobrado = $MontoPendiente;
						$MontoPorCobrar = $MontoPendiente - $MontoCobrado;
					}
					?>
					<tr class="trListaBody" onclick="clk($(this), 'documento', 'documento_<?=$id?>');" id="documento_<?=$id?>">
						<td align="center">
							<input type="hidden" name="documento_CodDocumento[]" value="<?=$f['CodDocumento']?>">
							<?=$f['CodTipoDocumento']?>
						</td>
						<td align="center"><?=$f['NroSerie']?></td>
						<td align="center"><?=$f['NroDocumento']?></td>
						<td align="center"><?=$f['NroDocumento']?></td>
						<td align="center"><?=formatFechaDMA($f['FechaDocumento'])?></td>
						<td align="center"><?=formatFechaDMA($f['FechaVencimiento'])?></td>
						<td>
							<input type="text" name="documento_MontoTotal[]" value="<?=number_format($f['MontoTotal'],2,',','.')?>" class="cell2" style="text-align:right;" readonly="readonly">
						</td>
						<td>
							<input type="text" name="documento_MontoPendiente[]" value="<?=number_format($MontoPendiente,2,',','.')?>" class="cell2" style="text-align:right;" readonly="readonly">
						</td>
						<td>
							<input type="text" name="documento_MontoCobrado[]" value="<?=number_format($MontoCobrado,2,',','.')?>" class="cell2" style="text-align:right;" readonly="readonly">
						</td>
						<td>
							<input type="text" name="documento_MontoPorCobrar[]" value="<?=number_format($MontoPorCobrar,2,',','.')?>" class="cell2" style="text-align:right;" readonly="readonly">
						</td>
						<td align="center"><?=$f['NroDocumento']?></td>
						<td><?=htmlentities($f['Comentarios'])?></td>
					</tr>
					<?php
			    	$MontoTotal += $f['MontoTotal'];
			    	$MontoPendiente += $MontoPendiente;
			    	$MontoCobrado += $MontoCobrado;
			    	$MontoPorCobrar += $MontoPorCobrar;
				}
				?>
		    </tbody>
		</table>
		<input type="hidden" id="nro_documento" value="<?=$nro_documento?>" />
		<input type="hidden" id="can_documento" value="<?=$nro_documento?>" />
	</div>
	<table class="tblBotones" style="width:100%; min-width:<?=$_width?>px;">
	    <tr>
	    	<td width="520" align="right" style="font-weight: bold; font-size: 12px;">Total:</td>
	    	<td width="140">
	    		<input type="text" name="MontoTotal" id="MontoTotal" class="cell2 currency" style="font-weight: bold; font-size: 12px;" value="<?=number_format($MontoTotal,2,',','.')?>" readonly>
	    	</td>
	    	<td width="140">
	    		<input type="text" name="MontoPendiente" id="MontoPendiente" class="cell2 currency" style="font-weight: bold; font-size: 12px;" value="<?=number_format($MontoPendiente,2,',','.')?>" readonly>
	    	</td>
	    	<td width="140">
	    		<input type="text" name="MontoCobrado" id="MontoCobrado" class="cell2 currency" style="font-weight: bold; font-size: 12px;" value="<?=number_format($MontoCobrado,2,',','.')?>" readonly>
	    	</td>
	    	<td width="140">
	    		<input type="text" name="MontoPorCobrar" id="MontoPorCobrar" class="cell2 currency" style="font-weight: bold; font-size: 12px;" value="<?=number_format($MontoPorCobrar,2,',','.')?>" readonly>
	    	</td>
	    	<td></td>
	    </tr>
	</table>
	<br>

	<input type="hidden" name="sel_cobranza" id="sel_cobranza" />
	<table class="tblBotones" style="width:100%; min-width:<?=$_width?>px;">
	    <tr>
	        <td>Forma de Pago</td>
	        <td align="right" class="gallery clearfix">
				<a id="a_adelanto" href="../lib/listas/gehen.php?anz=lista_co_documentoadelanto&filtrar=default&FlagCobranza=S&ventana=listado_insertar_linea&detalle=cobranza&modulo=ajax&accion=cobranza_insertar&url=../../co/co_cobranza_ajax.php&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe3]" style="display:none;"></a>

	            <input type="button" id="btCobranzaInsertar" value="Insertar" style="width:75px;" onclick="insertar_cobranza(this, 'cobranza', 'modulo=ajax&accion=cobranza_insertar', 'co_cobranza_ajax.php');" <?=$disabled_ver?> />
	            <input type="button" id="btCobranzaEfectivo" value="Efectivo" style="width:75px;" onclick="modalCobranzaEfectivo();" <?=$disabled_ver?> />
	            <input type="button" id="btCobranzaEfectivo" value="Adelanto" style="width:75px;" onclick="insertar_adelanto();" <?=$disabled_ver?> />
	            <input type="button" id="btCobranzaEliminar" value="Eliminar" style="width:75px;" onclick="quitar(this, 'cobranza'); setMontosCobranza();" <?=$disabled_ver?> />
	        </td>
	    </tr>
	</table>
	<div style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:200px;">
		<table class="tblLista" style="width:100%; min-width:<?=$_width?>px;">
			<thead>
			    <tr>
			        <th width="15">#</th>
			        <th width="135">Tipo Pago</th>
			        <th width="65">Moneda</th>
			        <th width="150">Monto</th>
			        <th width="125">Doc. Referencia</th>
			        <th width="125">Cuenta Cliente</th>
			        <th>Banco Cliente</th>
			        <th width="125">Tarjeta Crédito / Débito</th>
			        <th width="140">Cta. Bancaria Propia</th>
			    </tr>
		    </thead>
		    
		    <tbody id="lista_cobranza">
		    	<?php
		    	$TotalCobranza = 0;
		    	$nro_cobranza = 0;
				foreach ($field_cobranza as $f)
				{
					++$nro_cobranza;
					$id = $f['Secuencia'];
					?>
					<tr class="trListaBody" onclick="clk($(this), 'cobranza', 'cobranza_<?=$id?>');" id="cobranza_<?=$id?>">
						<th>
							<input type="hidden" name="cobranza_Secuencia[]" value="<?=$f['Secuencia']?>">
							<?=$id?>
						</th>
						<td>
							<select name="cobranza_CodTipoPago[]" class="cell" <?=$disabled_ver?>>
								<?=loadSelect2('co_tipopago','CodTipoPago','Descripcion',$f['CodTipoPago'],1)?>
							</select>
						</td>
						<td>
							<select name="cobranza_MonedaDocumento[]" class="cell" <?=$disabled_ver?>>
								<?=loadSelectGeneral("monedas",$f['MonedaDocumento'])?>
							</select>
						</td>
			            <td>
							<input type="text" name="cobranza_MontoLocal[]" value="<?=number_format($f['MontoLocal'],2,',','.')?>" class="cell currency" style="text-align:right; font-weight: bold;" onchange="setMontosCobranza();" <?=$disabled_ver?>>
			            </td>
			            <td>
							<input type="text" name="cobranza_DocReferencia[]" value="<?=$f['DocReferencia']?>" class="cell" maxlength="30" <?=$disabled_ver?>>
			            </td>
			            <td>
							<input type="text" name="cobranza_CtaBancaria[]" value="<?=$f['CtaBancaria']?>" class="cell" maxlength="20" <?=$disabled_ver?>>
			            </td>
						<td>
							<select name="cobranza_CodBanco[]" class="cell" <?=$disabled_ver?>>
								<option value=''>&nbsp;</option>
								<?=loadSelect2('mastbancos','CodBanco','Banco',$f['CodBanco'])?>
							</select>
						</td>
						<td>
							<select name="cobranza_CodTipoTarjeta[]" class="cell" <?=$disabled_ver?>>
								<option value=''>&nbsp;</option>
								<?=loadSelect2('co_tipotarjeta','CodTipoTarjeta','Descripcion',$f['CodTipoTarjeta'])?>
							</select>
						</td>
			            <td>
							<select name="cobranza_CtaBancariaPropia[]" class="cell" <?=$disabled_ver?>>
								<option value=''>&nbsp;</option>
								<?=loadSelect2('ap_ctabancaria','NroCuenta','NroCuenta',$f['NroCuenta'])?>
							</select>
			            </td>
					</tr>
					<?php
			    	$TotalCobranza += $f['MontoLocal'];
				}
				?>
		    </tbody>
		</table>
		<input type="hidden" id="nro_cobranza" value="<?=$nro_cobranza?>" />
		<input type="hidden" id="can_cobranza" value="<?=$nro_cobranza?>" />
	</div>
	<table class="tblBotones" style="width:100%; min-width:<?=$_width?>px;">
	    <tr>
	    	<td width="255" align="right" style="font-weight: bold; font-size: 12px;">Total:</td>
	    	<td width="167">
	    		<input type="text" name="TotalCobranza" id="TotalCobranza" class="cell2 currency" style="font-weight: bold; font-size: 12px;" value="<?=number_format($TotalCobranza,2,',','.')?>" readonly>
	    	</td>
	    	<td></td>
	    </tr>
	</table>
</form>

<script type="text/javascript">
	function insertar_documento() {
		var CodPersonaCliente = $('#CodPersonaCliente').val();
		if (CodPersonaCliente) {
			var href = '../lib/listas/gehen.php?anz=lista_co_documento&filtrar=default&fCodPersonaCliente='+CodPersonaCliente+'&fFlagCobranza=S&ventana=listado_insertar_linea_cobranza&detalle=documento&modulo=ajax&accion=documento_insertar&url=../../co/co_cobranza_ajax.php&iframe=true&width=100%&height=100%';
			$('#a_documento').attr('href', href);
			$('#a_documento').click();
		} else {
			cajaModal('Debe seleccionar el Cliente');
		}
	}
	function insertar_cobranza(btn, detalle, data, url) {
		var CodPersonaCliente = $('#CodPersonaCliente').val();
		if (CodPersonaCliente) {
			insertar2(btn, detalle, data, url);
		}
		else if (!CodPersonaCliente) {
			cajaModal('Debe seleccionar el Cliente');
		}
	}
	function insertar_adelanto() {
		var CodPersonaCliente = $('#CodPersonaCliente').val();
		if (CodPersonaCliente) {
			var href = '../lib/listas/gehen.php?anz=lista_co_documentoadelanto&filtrar=default&FlagCobranza=S&fCodPersonaCliente='+CodPersonaCliente+'&fFlagCobranza=S&ventana=listado_insertar_linea_cobranza&detalle=cobranza&modulo=ajax&accion=adelanto_insertar&url=../../co/co_cobranza_ajax.php&iframe=true&width=100%&height=100%';
			$('#a_adelanto').attr('href', href);
			$('#a_adelanto').click();
		} else {
			cajaModal('Debe seleccionar el Cliente');
		}
	}
	function setMontosDocumentos() {
		var MontoTotal = new Number(0);
		var MontoPendiente = new Number(0);
		var MontoCobrado = new Number(0);
		var MontoPorCobrar = new Number(0);
		//	
		$('input[name="documento_CodDocumento[]"]').each(function(idx) {
			var documento_MontoTotal = setNumero($('input[name="documento_MontoTotal[]"]:eq('+idx+')').val());
			var documento_MontoPendiente = setNumero($('input[name="documento_MontoPendiente[]"]:eq('+idx+')').val());
			var documento_MontoCobrado = setNumero($('input[name="documento_MontoCobrado[]"]:eq('+idx+')').val());
			var documento_MontoPorCobrar = setNumero($('input[name="documento_MontoPorCobrar[]"]:eq('+idx+')').val());
			MontoTotal += documento_MontoTotal;
			MontoPendiente += documento_MontoPendiente;
			MontoCobrado += documento_MontoCobrado;
			MontoPorCobrar += documento_MontoPorCobrar;
		});
		$('#MontoTotal').val(MontoTotal).formatCurrency();
		$('#MontoPendiente').val(MontoPendiente).formatCurrency();
		$('#MontoCobrado').val(MontoCobrado).formatCurrency();
		$('#MontoPorCobrar').val(MontoPorCobrar).formatCurrency();
	}
	function setMontosCobranza() {
		var MontoTotal = setNumero($('#MontoTotal').val());
		var MontoPendiente = setNumero($('#MontoPendiente').val());
		var MontoCobrado = setNumero($('#MontoCobrado').val());
		var MontoPorCobrar = setNumero($('#MontoPorCobrar').val());
		var MontoLocal = new Number(0);
		//	
		$('input[name="cobranza_Secuencia[]"]').each(function(idx) {
			var cobranza_MontoLocal = setNumero($('input[name="cobranza_MontoLocal[]"]:eq('+idx+')').val());
			MontoLocal += cobranza_MontoLocal;
		});
		$('#TotalCobranza').val(MontoLocal).formatCurrency();
	}
	function modalCobranzaEfectivo() {
		//	ajax
		$.post('co_cobranza_efectivo.php', $('#frmentrada').serialize(), function(data) {
			$("#cajaModal").dialog({
				buttons: {
					"Aceptar": function() {
						insertarCobranzaEfectivo();
						$(this).dialog("close");
					},
					"Cancelar": function() {
						$(this).dialog("close");
					}
				}
			});
			$("#cajaModal").dialog({ title: "<img src='../imagenes/info.png' width='24' align='absmiddle' />Cobranza Efectivo", width: 300 });
			$("#cajaModal").html(data);
			$('#cajaModal').dialog('open');
			inicializar();
	    });
	}
	function insertarCobranzaEfectivo() {
		var boton = document.getElementById('btCobranzaEfectivo');
		var detalle = 'cobranza';
		var valores = 'modulo=ajax&accion=cobranza_efectivo_insertar&MontoRecibido='+setNumero($('#efectivo_MontoRecibido').val())+'&MontoVuelto='+setNumero($('#efectivo_MontoVuelto').val())+'&MontoPorCobrar='+setNumero($('#efectivo_MontoPorCobrar').val());
		var url = 'co_cobranza_ajax.php';

		boton.disabled = true;
		var nro = "#nro_" + detalle;
		var can = "#can_" + detalle;
		var sel = "#sel_" + detalle;
		var lista = "#lista_" + detalle;
		var nro_detalle = new Number($(nro).val()); nro_detalle++;
		var can_detalle = new Number($(can).val()); can_detalle++;
		//	ajax
		$.ajax({
			type: "POST",
			url: url,
			data: "nro_detalle="+nro_detalle+"&can_detalle="+can_detalle+"&"+valores,
			async: true,
			success: function(resp) {
				$(nro).val(nro_detalle);
				$(can).val(can_detalle);
				$(lista).append(resp);
				boton.disabled = false;
				$('#btCobranzaInsertar').prop('disabled', true);
				$('#btCobranzaEliminar').prop('disabled', true);
				setMontosCobranza();
				inicializar();
			}
		});
	}
</script>