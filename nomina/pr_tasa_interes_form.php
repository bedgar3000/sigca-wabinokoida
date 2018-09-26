<?php
if ($opcion == "nuevo") {
	$field['Periodo'] = $PeriodoActual;
	$field['Fecha'] = $FechaActual;
	$field['Estado'] = "A";
	if (!$fCodOrganismo) $fCodOrganismo = $_SESSION["ORGANISMO_ACTUAL"];
	##
	$titulo = "Nueva Tasa";
	$accion = "nuevo";
	$disabled_nuevo = "disabled";
	$disabled_modificar = "";
	$disabled_ver = "";
	$display_ver = "";
	$label_submit = "Guardar";
	$focus = "Porcentaje";
	$clkCancelar = "document.getElementById('frmentrada').submit();";
}
elseif ($opcion == "modificar" || $opcion == "ver") {
	//	consulto datos generales
	$sql = "SELECT * FROM masttasainteres WHERE Periodo = '".$sel_registros."'";
	$field = getRecord($sql);
	##
	if ($opcion == "modificar") {
		$titulo = "Modificar Tasa";
		$accion = "modificar";
		$disabled_nuevo = "";
		$disabled_modificar = "disabled";
		$disabled_ver = "";
		$display_ver = "";
		$label_submit = "Modificar";
		$focus = "DescripCargo";
		$clkCancelar = "document.getElementById('frmentrada').submit();";
	}
	##
	elseif ($opcion == "ver") {
		$titulo = "Ver Tasa";
		$accion = "";
		$disabled_nuevo = "disabled";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$display_ver = "display:none;";
		$label_submit = "";
		$focus = "btCancelar";
		$clkCancelar = "document.getElementById('frmentrada').submit();";
	}
}
//	------------------------------------
$_width = 400;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=pr_tasa_interes_lista" method="POST" enctype="multipart/form-data" onsubmit="return tasa_interes(this, '<?=$accion?>');" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="fAnio" id="fAnio" value="<?=$fAnio?>" />

<table width="<?=$_width?>" class="tblForm">
	<tr>
    	<td colspan="2" class="divFormCaption">Datos Generales</td>
    </tr>
	<tr>
		<td class="tagForm">* Periodo:</td>
		<td>
        	<input type="text" id="Periodo" value="<?=$field['Periodo']?>" style="width:75px;" <?=$disabled_modificar?> />
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Fecha:</td>
		<td>
        	<input type="text" id="Fecha" value="<?=formatFechaDMA($field['Fecha'])?>" style="width:75px;" maxlength="10" class="datepicker" <?=$disabled_ver?> />
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Porcentaje:</td>
		<td>
        	<input type="text" id="Porcentaje" value="<?=number_format($field['Porcentaje'], 2, ',', '.')?>" style="width:75px; text-align:right;" class="currency" <?=$disabled_ver?> />
		</td>
	</tr>
	<tr>
		<td class="tagForm">Estado:</td>
		<td>
            <input type="radio" name="Estado" id="Activo" value="A" <?=chkOpt($field['Estado'], "A");?> <?=$disabled_nuevo?> /> Activo
            &nbsp; &nbsp;
            <input type="radio" name="Estado" id="Inactivo" value="I" <?=chkOpt($field['Estado'], "I");?> <?=$disabled_nuevo?> /> Inactivo
		</td>
	</tr>
    <tr>
		<td class="tagForm">&Uacute;ltima Modif.:</td>
		<td>
			<input type="text" size="20" value="<?=$field['UltimoUsuario']?>" disabled="disabled" />
			<input type="text" size="15" value="<?=$field['UltimaFecha']?>" disabled="disabled" />
		</td>
	</tr>
</table>
<center>
<input type="submit" value="<?=$label_submit?>" style="width:75px; <?=$display_ver?>" />
<input type="button" value="Cancelar" style="width:75px;" id="btCancelar" onclick="<?=$clkCancelar?>" />
</center>
</form>
<div style="width:<?=$_width?>px" class="divMsj">Campos Obligatorios *</div>

<script type="text/javascript" language="javascript">
function tasa_interes(form, accion) {
	bloqueo(true);
	//	valido
	var error = "";
	if ($("#Periodo").val().trim() == "" || $("#Porcentaje").val().trim() == "") error = "Debe llenar los campos obligatorios";
	else if(!valPeriodo($("#Periodo").val())) error = "Formato <strong>Periodo</strong> incorrecto";
	else if(!valFecha($("#Fecha").val()) && $("#Fecha").val().trim() == "") error = "Formato <strong>Fecha</strong> incorrecta";
	
	//	valido errores
	if (error != "") {
		cajaModal(error, "error", 400);
	} else {
		var post = getForm(form);
		//	ajax
		$.ajax({
			type: "POST",
			url: "pr_tasa_interes_ajax.php",
			data: "modulo=tasa_interes&accion="+accion+"&"+post,
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