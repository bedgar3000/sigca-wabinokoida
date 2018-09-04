<?php
if ($opcion == "confirmar") {
	list($CodRequerimiento, $Secuencia) = explode("_", $sel_confirmar);
	$sql = "SELECT
				rd.CodRequerimiento,
				rd.Secuencia,
				rd.CantidadPedida,
				rd.CantidadRecibida,
				(rd.CantidadPedida - rd.CantidadRecibida) AS CantidadPendiente,
				rd.CommoditySub,
				rd.Descripcion,
				r.CodInterno
			FROM
				lg_requerimientosdet rd
				INNER JOIN lg_requerimientos r ON (r.CodRequerimiento = rd.CodRequerimiento)
			WHERE
				rd.CodRequerimiento = '".$CodRequerimiento."' AND
				rd.Secuencia = '".$Secuencia."'";
	$field = getRecord($sql);
	##
	$field['ConfirmadaPor'] = $_SESSION["CODPERSONA_ACTUAL"];
	$field['NomConfirmadaPor'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
	$field['FechaConfirmadaPor'] = substr($Ahora,0,10);
	##
	$titulo = "Confirmar Servicio";
	$accion = "confirmar";
	$readonly_ver = "";
	$display_submit = "";
	$label_submit = "Confirmar";
	$clkCancelar = "document.getElementById('frmentrada').submit();";
	$focus = "btSubmit";
}
//	------------------------------------
$_width = 550;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lg_cajachica_confirmar_lista" method="POST" enctype="multipart/form-data" onsubmit="return formulario(this, '<?=$accion?>');" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
<input type="hidden" name="fCodDependencia" id="fCodDependencia" value="<?=$fCodDependencia?>" />
<input type="hidden" name="fCodCentroCosto" id="fCodCentroCosto" value="<?=$fCodCentroCosto?>" />
<input type="hidden" name="fFechaPreparacionD" id="fFechaPreparacionD" value="<?=$fFechaPreparacionD?>" />
<input type="hidden" name="fFechaPreparacionH" id="fFechaPreparacionH" value="<?=$fFechaPreparacionH?>" />
<input type="hidden" name="fFechaAprobacionD" id="fFechaAprobacionD" value="<?=$fFechaAprobacionD?>" />
<input type="hidden" name="fFechaAprobacionH" id="fFechaAprobacionH" value="<?=$fFechaAprobacionH?>" />
<input type="hidden" name="CantidadPendiente" id="CantidadPendiente" value="<?=$field['CantidadPendiente']?>" />
<input type="hidden" name="CodRequerimiento" id="CodRequerimiento" value="<?=$field['CodRequerimiento']?>" />

<table width="<?=$_width?>" class="tblForm">
	<tr>
    	<td colspan="4" class="divFormCaption">Datos Generales</td>
    </tr>
	<tr>
		<td class="tagForm" width="125">Requerimiento:</td>
		<td>
        	<input type="text" value="<?=$field['CodInterno']?>" style="width:80px;" class="codigo" readonly />
        	<input type="text" name="Secuencia" id="Secuencia" value="<?=$field['Secuencia']?>" style="width:20px; text-align:center;" class="codigo" readonly />
		</td>
		<td class="tagForm" width="100">Commodity:</td>
		<td>
	        <input type="text" value="<?=$field['CommoditySub']?>" style="width:50px;" class="codigo" disabled />
		</td>
	</tr>
	<tr>
		<td class="tagForm">Descripci&oacute;n</td>
		<td colspan="3">
        	<textarea style="width:90%; height:50px;" class="codigo" disabled><?=htmlentities($field['Descripcion'])?></textarea>
		</td>
	</tr>
	<tr>
		<td class="tagForm">Confirmada Por:</td>
		<td colspan="3">
        	<input type="hidden" name="ConfirmadaPor" id="ConfirmadaPor" value="<?=$field['ConfirmadaPor']?>" />
        	<input type="text" value="<?=$field['NomConfirmadaPor']?>" style="width:200px;" disabled />
        	<input type="text" name="FechaConfirmadaPor" id="FechaConfirmadaPor" value="<?=formatFechaDMA($field['FechaConfirmadaPor'])?>" style="width:65px;" />
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Cantidad:</td>
		<td colspan="3">
        	<input type="text" name="CantidadRecibida" id="CantidadRecibida" value="<?=number_format($field['CantidadPendiente'], 2, ',', '.')?>" style="width:75px; text-align:right;" class="currency" />
		</td>
	</tr>
    <tr>
		<td class="tagForm">&Uacute;ltima Modif.:</td>
		<td colspan="3">
			<input type="text" size="30" value="<?=$field['UltimoUsuario']?>" disabled="disabled" />
			<input type="text" size="25" value="<?=$field['UltimaFecha']?>" disabled="disabled" />
		</td>
	</tr>
</table>

<center>
<input type="submit" value="<?=$label_submit?>" id="btSubmit" style="width:75px; <?=$display_submit?>" />
<input type="button" value="Cancelar" id="btCancelar" style="width:75px;" onclick="<?=$clkCancelar?>" />
</center>
</form>
<br />
<div style="width:<?=$_width?>px" class="divMsj">Campos Obligatorios *</div>

<script type="text/javascript" language="javascript">
function formulario(form, accion) {
	bloqueo(true);
	//	valido
	var error = "";
	if ($("#CantidadRecibida").val().trim() == "") error = "Debe llenar los campos obligatorios";
	else if (setNumero($("#CantidadRecibida").val()) == 0 || isNaN(setNumero($("#CantidadRecibida").val()))) error = "Formato de la <strong>Cantidad</strong> incorrecta";
	//	valido errores
	if (error != "") {
		cajaModal(error, "error", 400);
	} else {
		//	ajax
		$.ajax({
			type: "POST",
			url: "lg_cajachica_confirmar_ajax.php",
			data: "modulo=formulario&accion="+accion+"&"+$('#frmentrada').serialize(),
			async: false,
			success: function(resp) {
				if (resp.trim() != "") cajaModal(resp, "error", 400);
				else form.submit();
			}
		});
	}
	return false;
}
</script>