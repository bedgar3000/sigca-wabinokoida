<?php
if ($opcion == "nuevo") {
	$field['Estado'] = 'A';
	##
	$_titulo = "Asignación de Cuentas Bancarias por Defecto / Nuevo Registro";
	$accion = "nuevo";
	$disabled_modificar = "";
	$disabled_ver = "";
	$read_modificar = "";
	$display_modificar = "";
	$display_ver = "";
	$display_submit = "";
	$label_submit = "Guardar";
	$focus = "CodOrganismo";
}
elseif ($opcion == "modificar" || $opcion == "ver") {
	list($CodOrganismo, $CodTipoPago) = explode('_', $sel_registros);
	##	consulto datos generales
	$sql = "SELECT *
			FROM ap_ctabancariadefault
			WHERE
				CodOrganismo = '$CodOrganismo'
				AND CodTipoPago = '$CodTipoPago'";
	$field = getRecord($sql);
	##
	if ($opcion == "modificar") {
		$_titulo = "Asignación de Cuentas Bancarias por Defecto / Modificar Registro";
		$accion = "modificar";
		$disabled_modificar = "disabled";
		$disabled_ver = "";
		$read_modificar = "readonly";
		$display_modificar = "display:none;";
		$display_ver = "";
		$display_submit = "";
		$label_submit = "Modificar";
		$focus = "Descripcion";
	}
	##
	elseif ($opcion == "ver") {
		$_titulo = "Asignación de Cuentas Bancarias por Defecto / Ver Registro";
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
$_width = 500;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=ap_ctabancariadefault_lista" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('ap_ctabancariadefault_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
	<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
	<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />

	<table style="width:100%; max-width:<?=$_width?>px;" class="tblForm">
		<tr>
	    	<td colspan="2" class="divFormCaption">Datos Generales</td>
	    </tr>
		<tr>
			<td class="tagForm" width="125">* Organismo:</td>
			<td>
				<select name="CodOrganismo" id="CodOrganismo" style="width:95%;" <?=$disabled_ver?>>
					<option value="">&nbsp;</option>
					<?=getOrganismos($field['CodOrganismo'], 3)?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Tipo de Pago:</td>
			<td>
				<select name="CodTipoPago" id="CodTipoPago" style="width:95%;" <?=$disabled_ver?>>
					<option value="">&nbsp;</option>
					<?=loadSelect2('masttipopago','CodTipoPago','TipoPago',$field['CodTipoPago'])?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Cta. Bancaria:</td>
			<td>
				<select name="NroCuenta" id="NroCuenta" style="width:95%;" <?=$disabled_ver?>>
					<option value="">&nbsp;</option>
					<?=loadSelect2('ap_ctabancaria','NroCuenta','NroCuenta',$field['NroCuenta'])?>
				</select>
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