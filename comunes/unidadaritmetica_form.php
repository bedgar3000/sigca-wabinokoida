<?php
if ($opcion == "nuevo") {
	$field['Anio'] = $AnioActual;
	$field['Fecha'] = $FechaActual;
	##
	$_titulo = "Unidad Aritmetica Umbral / Nuevo Registro";
	$accion = "nuevo";
	$disabled_ver = "";
	$read_modificar = "";
	$display_ver = "";
	$display_submit = "";
	$label_submit = "Guardar";
	$focus = "Valor";
}
elseif ($opcion == "modificar" || $opcion == "ver") {
	list($Anio, $Secuencia) = explode('_', $sel_registros);
	##	consulto datos generales
	$sql = "SELECT *
			FROM mastunidadaritmetica
			WHERE
				Anio = '$Anio'
				AND Secuencia = '$Secuencia'";
	$field = getRecord($sql);
	##
	if ($opcion == "modificar") {
		$_titulo = "Unidad Aritmetica Umbral / Modificar Registro";
		$accion = "modificar";
		$disabled_ver = "";
		$read_modificar = "readonly";
		$display_ver = "";
		$display_submit = "";
		$label_submit = "Modificar";
		$focus = "GacetaOficial";
	}
	##
	elseif ($opcion == "ver") {
		$_titulo = "Unidad Aritmetica Umbral / Ver Registro";
		$accion = "";
		$disabled_ver = "disabled";
		$read_modificar = "readonly";
		$display_ver = "display:none;";
		$display_submit = "display:none;";
		$label_submit = "";
		$focus = "btCancelar";
	}
}
$clkCancelar = "document.getElementById('frmentrada').submit();";
//	------------------------------------
$_width = 550;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=unidadaritmetica_lista" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('unidadaritmetica_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
	<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
	<input type="hidden" name="Secuencia" id="Secuencia" value="<?=$field['Secuencia']?>" />

	<table style="width:100%; max-width:<?=$_width?>px;" class="tblForm">
		<tr>
	    	<td colspan="2" class="divFormCaption">Datos Generales</td>
	    </tr>
	    <tr>
			<td class="tagForm" width="125">* Anio:</td>
			<td>
	        	<input type="text" name="Anio" id="Anio" value="<?=$field['Anio']?>" style="width:75px; font-weight:bold;" maxlength="4" <?=$read_modificar?> />
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Fecha:</td>
			<td>
	        	<input type="text" name="Fecha" id="Fecha" value="<?=formatFechaDMA($field['Fecha'])?>" style="width:75px;" class="datepicker" maxlength="10" onchange="setAnio(this.value);" <?=$disabled_ver?> />
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Valor:</td>
			<td>
	        	<input type="text" name="Valor" id="Valor" value="<?=number_format($field['Valor'],2,',','.')?>" style="width:145px; text-align: right;" class="currency" <?=$disabled_ver?> />
			</td>
		</tr>
	    <tr>
			<td class="tagForm">Gaceta Oficial:</td>
			<td>
	        	<input type="text" name="GacetaOficial" id="GacetaOficial" value="<?=$field['GacetaOficial']?>" style="width:295px;" maxlength="255" <?=$disabled_ver?> />
			</td>
		</tr>
	    <tr>
			<td class="tagForm">Nro. Providencia:</td>
			<td>
	        	<input type="text" name="NroDocumento" id="NroDocumento" value="<?=$field['NroDocumento']?>" style="width:295px;" maxlength="255" <?=$disabled_ver?> />
			</td>
		</tr>
	    <tr>
			<td class="tagForm">&Uacute;ltima Modif.:</td>
			<td>
				<input type="text" value="<?=$field['UltimoUsuario']?>" style="width:145px;" disabled="disabled" />
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
	function setAnio(fecha) {
		var partes = fecha.split('-');
		var Anio = partes[2];
		$('#Anio').val(Anio);
	}
</script>