<?php
##	consulto datos generales
$sql = "SELECT
			do.*,
			td.Descripcion AS TipoDocumento,
			sf.CodSerie,
			sf.NroSerie,
			md1.Descripcion AS NomFormaFactura,
			cc.Codigo AS CentroCosto,
			p1.NomCompleto AS NombreCliente,
			p1.Direccion AS DireccionCliente,
			p1.Telefono1 As TelefonoCliente,
			p2.NomCompleto AS NomPreparadoPor,
			p3.NomCompleto AS NomAprobadoPor,
			rd.CodComunidad,
			cm.Descripcion AS Comunidad
		FROM co_documento do
		INNER JOIN ac_mastcentrocosto cc ON cc.CodCentroCosto = do.CodCentroCosto
		INNER JOIN co_tipodocumento td ON td.CodTipoDocumento = do.CodTipoDocumento
		INNER JOIN co_establecimientofiscal ef ON ef.CodOrganismo = do.CodOrganismo
		INNER JOIN co_seriefiscal sf ON (
			sf.CodOrganismo = ef.CodOrganismo
			AND sf.CodEstablecimiento = ef.CodEstablecimiento
		)
		INNER JOIN mastpersonas p1 ON p1.CodPersona = do.CodPersonaCliente

		LEFT JOIN mastpersonas p2 ON p2.CodPersona = do.PreparadoPor
		LEFT JOIN mastpersonas p3 ON p3.CodPersona = do.AprobadoPor
		LEFT JOIN mastmiscelaneosdet md1 ON (
			md1.CodDetalle = do.FormaFactura
			AND md1.CodMaestro = 'FORMAFACT'
		)
		LEFT JOIN co_rutadespacho rd ON rd.CodRutaDespacho = do.CodRutaDespacho
		LEFT JOIN mastcomunidades cm ON cm.CodComunidad = rd.CodComunidad
		WHERE do.CodDocumento = '$sel_registros'";
$field = getRecord($sql);
##	
$_titulo = "Documentos / Modificación Restringida";
$accion = "modificar-restringido";
$label_submit = "Modificar";
$focus = "Comentarios";
$clkCancelar = "document.getElementById('frmentrada').submit();";
//	------------------------------------
$_width = 900;
if ($origen == 'framemain') $action = '../framemain.php';
elseif (!empty($origen)) $action = "gehen.php?anz=$origen";
else $action = "gehen.php?anz=co_documento_lista";
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="<?=$action?>" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('co_documento_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
	<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
	<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
	<input type="hidden" name="fCodEstablecimiento" id="fCodEstablecimiento" value="<?=$fCodEstablecimiento?>" />
	<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
	<input type="hidden" name="fFechaDocumentoD" id="fFechaDocumentoD" value="<?=$fFechaDocumentoD?>" />
	<input type="hidden" name="fFechaDocumentoH" id="fFechaDocumentoH" value="<?=$fFechaDocumentoH?>" />
	<input type="hidden" name="fCodPersonaCliente" id="fCodPersonaCliente" value="<?=$fCodPersonaCliente?>" />
	<input type="hidden" name="fNombreCliente" id="fNombreCliente" value="<?=htmlentities($fNombreCliente)?>" />
	<input type="hidden" name="fDocFiscalCliente" id="fDocFiscalCliente" value="<?=$fDocFiscalCliente?>" />
	<input type="hidden" name="CodDocumento" id="CodDocumento" value="<?=$field['CodDocumento']?>" />
	<input type="hidden" name="TipoVenta" id="TipoVenta" value="<?=$field['TipoVenta']?>" />
	<input type="hidden" name="CodRutaDespacho" id="CodRutaDespacho" value="<?=$field['CodRutaDespacho']?>" />
	<input type="hidden" name="CodPersonaCobrar" id="CodPersonaCobrar" value="<?=$field['CodPersonaCobrar']?>" />
	<input type="hidden" name="CodCentroCosto" id="CodCentroCosto" value="<?=$field['CodCentroCosto']?>" />
	<input type="hidden" name="PreparadoPor" id="PreparadoPor" value="<?=$field['PreparadoPor']?>" />
	<input type="hidden" name="FechaPreparado" id="FechaPreparado" value="<?=$field['FechaPreparado']?>" />
	<input type="hidden" name="AprobadoPor" id="AprobadoPor" value="<?=$field['AprobadoPor']?>" />
	<input type="hidden" name="FechaAprobado" id="FechaAprobado" value="<?=$field['FechaAprobado']?>" />
	<input type="hidden" name="igv" id="igv" value="<?=$igv?>" />

	<table style="width:100%; max-width:<?=$_width?>px;" class="tblForm">
		<tr>
	    	<td colspan="4" class="divFormCaption">Datos Generales</td>
	    </tr>
	    <tr>
			<td class="tagForm">Organismo:</td>
			<td>
				<select name="CodOrganismo" id="CodOrganismo" style="width:295px;" disabled="disabled">
					<?=getOrganismos($field['CodOrganismo'], 3)?>
				</select>
			</td>
			<td class="tagForm">Estado:</td>
			<td>
				<input type="hidden" name="Estado" id="Estado" value="<?=$field['Estado']?>">
	        	<input type="text" value="<?=mb_strtoupper(printValores('documento2-estado',$field['Estado']))?>" style="width:125px; font-weight:bold;" disabled />
			</td>
		</tr>
	    <tr>
			<td class="tagForm">Establecimiento:</td>
			<td>
				<select name="CodEstablecimiento" id="CodEstablecimiento" style="width:295px;" disabled="disabled">
					<?=loadSelect2('co_establecimientofiscal','CodEstablecimiento','Descripcion',$field['CodEstablecimiento'])?>
				</select>
			</td>
			<td class="tagForm">Forma de Pago:</td>
			<td>
				<select name="CodFormaPago" id="CodFormaPago" style="width:125px;">
					<?=loadSelect2('mastformapago','CodFormaPago','Descripcion',$field['CodFormaPago'],0)?>
				</select>
			</td>
		</tr>
	    <tr>
			<td class="tagForm">Documento:</td>
			<td>
				<input type="hidden" name="CodTipoDocumento" id="CodTipoDocumento" value="<?=$field['CodTipoDocumento']?>">
				<input type="text" name="TipoDocumento" id="TipoDocumento" value="<?=$field['TipoDocumento']?>" style="width:295px; font-weight: bold; font-size: 14px;" readonly />
			</td>
			<td class="tagForm">Forma Factura:</td>
			<td>
				<input type="hidden" name="FormaFactura" id="FormaFactura" value="<?=$field['FormaFactura']?>">
				<input type="text" name="NomFormaFactura" id="NomFormaFactura" value="<?=$field['NomFormaFactura']?>" style="width:125px;" readonly />
			</td>
		</tr>
		<tr>
			<td class="tagForm">Nro. Documento:</td>
			<td>
				<input type="hidden" name="CodSerie" id="CodSerie" value="<?=$field['CodSerie']?>">
				<input type="text" name="NroSerie" id="NroSerie" value="<?=$field['NroSerie']?>" style="width:100px; font-weight: bold; font-size: 14px;" readonly />
				<input type="text" name="NroDocumento" id="NroDocumento" value="<?=$field['NroDocumento']?>" style="width:191px; font-weight: bold; font-size: 14px;" readonly />
			</td>
			<td class="tagForm">Fecha Emisión:</td>
			<td>
				<input type="text" name="FechaDocumento" id="FechaDocumento" value="<?=formatFechaDMA($field['FechaDocumento'])?>" maxlength="10" style="width:75px;" class="datepicker" onkeyup="setFechaDMA(this);" />
			</td>
		</tr>
		<tr>
			<td class="tagForm">Cliente:</td>
			<td>
				<input type="hidden" name="DocFiscalCliente" id="DocFiscalCliente" value="<?=$field['DocFiscalCliente']?>">
				<input type="text" name="CodPersonaCliente" id="CodPersonaCliente" value="<?=$field['CodPersonaCliente']?>" style="width:66px;" readonly />
				<input type="text" name="NombreCliente" id="NombreCliente" value="<?=htmlentities($field['NombreCliente'])?>" style="width:225px;" readonly />
			</td>
			<td class="tagForm">Fecha Vencimiento:</td>
			<td>
				<input type="text" name="FechaVencimiento" id="FechaVencimiento" value="<?=formatFechaDMA($field['FechaVencimiento'])?>" maxlength="10" style="width:75px;" class="datepicker" onkeyup="setFechaDMA(this);" />
			</td>
		</tr>
	    <tr>
			<td class="tagForm">Vendedor:</td>
			<td>
				<select name="CodPersonaVendedor" id="CodPersonaVendedor" style="width:295px;">
					<option value="">&nbsp;</option>
					<?=vendedores($field['CodPersonaVendedor'])?>
				</select>
			</td>
			<th style="text-align: right;">Informaci&oacute;n Monetaria</th>
			<th></th>
		</tr>
	    <tr>
			<td class="tagForm" rowspan="2">Comentarios:</td>
			<td rowspan="2">
				<textarea name="Comentarios" id="Comentarios" style="width:295px;" rows="2"><?=$field['Comentarios']?></textarea>
			</td>
			<td class="tagForm">Moneda:</td>
			<td>
				<select name="MonedaDocumento" id="MonedaDocumento" style="width:125px;" disabled="disabled">
					<?=loadSelectGeneral("monedas", $field['MonedaDocumento'])?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="tagForm"><strong>Monto Total:</strong></td>
			<td>
				<input type="text" name="MontoTotal" id="MontoTotal" value="<?=number_format($field['MontoTotal'],2,',','.')?>" style="width:125px; text-align: right; font-weight: bold; font-size: 14px;" class="currency" readonly />
			</td>
		</tr>
	    <tr>
			<td class="tagForm">&Uacute;ltima Modif.:</td>
			<td>
				<input type="text" value="<?=$field['UltimoUsuario']?>" style="width:146px;" disabled="disabled" />
				<input type="text" value="<?=$field['UltimaFecha']?>" style="width:145px" disabled="disabled" />
			</td>
		</tr>
	</table>

	<center>
		<input type="submit" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" id="btSubmit" />
		<input type="button" value="Cancelar" style="width:75px;" id="btCancelar" onclick="<?=$clkCancelar?>" />
	</center>
</form>
<div style="width:100%; max-width:<?=$_width?>px;" class="divMsj">Campos Obligatorios *</div>

<script type="text/javascript">
</script>