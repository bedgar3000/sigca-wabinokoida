<?php
##	consulto datos generales
$sql = "SELECT
			do.*,
			td.Descripcion AS TipoDocumento
		FROM co_documento do
		INNER JOIN co_tipodocumento td ON td.CodTipoDocumento = do.CodTipoDocumento
		WHERE do.CodDocumento = '$sel_registros'";
$field = getRecord($sql);
##	
$_titulo = "Transferir a Ctas. x Pagar";
$accion = "transferir";
$label_submit = "Transferir";
$focus = "btSubmit";
$clkCancelar = "document.getElementById('frmentrada').submit();";
//	------------------------------------
$_width = 540;
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

	<table style="width:100%; max-width:<?=$_width?>px;" class="tblForm">
		<tr>
	    	<td colspan="4" class="divFormCaption">Datos Generales</td>
	    </tr>
	    <tr>
			<td class="tagForm" width="150">Organismo:</td>
			<td colspan="3">
				<select name="CodOrganismo" id="CodOrganismo" style="width:350px;" disabled="disabled">
					<?=getOrganismos($field['CodOrganismo'], 3)?>
				</select>
			</td>
		</tr>
	    <tr>
			<td class="tagForm">Documento:</td>
			<td colspan="3">
				<input type="hidden" name="CodTipoDocumento" id="CodTipoDocumento" value="<?=$field['CodTipoDocumento']?>">
				<input type="text" name="TipoDocumento" id="TipoDocumento" value="<?=$field['TipoDocumento']?>" style="width:350px; font-weight: bold; font-size: 14px;" readonly />
			</td>
		</tr>
		<tr>
			<td class="tagForm">Nro. Documento:</td>
			<td colspan="3">
				<input type="text" name="NroDocumento" id="NroDocumento" value="<?=$field['NroDocumento']?>" style="width:350px; font-weight: bold; font-size: 14px;" maxlength="10" readonly />
			</td>
		</tr>
		<tr>
			<td class="tagForm">Fecha Documento:</td>
			<td>
				<input type="text" name="FechaDocumento" id="FechaDocumento" value="<?=formatFechaDMA($field['FechaDocumento'])?>" maxlength="10" style="width:75px;" class="datepicker" onkeyup="setFechaDMA(this);" disabled />
			</td>
			<td class="tagForm">Fecha Vencimiento:</td>
			<td>
				<input type="text" name="FechaVencimiento" id="FechaVencimiento" value="<?=formatFechaDMA($field['FechaVencimiento'])?>" maxlength="10" style="width:75px;" class="datepicker" onkeyup="setFechaDMA(this);" disabled />
			</td>
		</tr>
		<tr>
			<td class="tagForm">Monto Pendiente:</td>
			<td colspan="3">
				<input type="text" name="MontoTotal" id="MontoTotal" value="<?=number_format($field['MontoTotal'],2,',','.')?>" style="width:125px; text-align: right;" class="currency" readonly />
			</td>
		</tr>
		<tr>
			<td class="tagForm">Monto a Transferir:</td>
			<td colspan="3">
				<input type="text" name="MontoTotal" id="MontoTotal" value="<?=number_format(abs($field['MontoTotal']),2,',','.')?>" style="width:125px; text-align: right;" class="currency" readonly />
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