<?php
if ($opcion == "nuevo") {
	$field['CodOrganismo'] = (!empty($fCodOrganismo)?$fCodOrganismo:$_SESSION["FILTRO_ORGANISMO_ACTUAL"]);
	$field['Periodo'] = date('Y-m');
	$field['Estado'] = 'A';
	##
	$_titulo = "Control de Periodos / Nuevo Registro";
	$accion = "nuevo";
	$disabled_ver = "";
	$read_modificar = "";
	$display_ver = "";
	$display_submit = "";
	$label_submit = "Guardar";
	$focus = "CodOrganismo";
}
elseif ($opcion == "modificar" || $opcion == "ver") {
	list($CodOrganismo, $Periodo) = explode('_', $sel_registros);
	##	consulto datos generales
	$sql = "SELECT *
			FROM lg_periodocontrol
			WHERE
				CodOrganismo = '$CodOrganismo'
				AND Periodo = '$Periodo'";
	$field = getRecord($sql);
	##
	if ($opcion == "modificar") {
		$_titulo = "Control de Periodos / Modificar Registro";
		$accion = "modificar";
		$disabled_ver = "";
		$read_modificar = "readonly";
		$display_ver = "";
		$display_submit = "";
		$label_submit = "Modificar";
		$focus = "Descripcion";
	}
	##
	elseif ($opcion == "ver") {
		$_titulo = "Control de Periodos / Ver Registro";
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
$_width = 600;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lg_periodocontrol_lista" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('lg_periodocontrol_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
	<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
	<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
	<input type="hidden" name="sel_registros" id="sel_registros" value="<?=$sel_registros?>" />

	<table style="width:100%; max-width:<?=$_width?>px;" class="tblForm">
		<tr>
	    	<td colspan="2" class="divFormCaption">Datos Generales</td>
	    </tr>
	    <tr>
			<td class="tagForm" width="100">* Organismo:</td>
			<td>
				<select name="CodOrganismo" id="CodOrganismo" style="width:350px;" <?=$disabled_modificar?>>
					<?=getOrganismos($field['CodOrganismo'], 3)?>
				</select>
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Periodo:</td>
			<td>
	        	<input type="text" name="Periodo" id="Periodo" value="<?=$field['Periodo']?>" style="width:75px; font-weight:bold;" maxlength="7" <?=$read_modificar?> />
			</td>
		</tr>
	    <tr>
			<td class="tagForm">&nbsp;</td>
			<td>
	            <input type="checkbox" name="FlagTransaccion" id="FlagTransaccion" value="S" <?=chkOpt($field['FlagTransaccion'], "S");?> <?=$disabled_ver?> /> Disponible para Transacción
			</td>
		</tr>
		<tr>
			<td class="tagForm">Estado:</td>
			<td>
	            <input type="radio" name="Estado" id="Activo" value="A" <?=chkOpt($field['Estado'], "A");?> <?=$disabled_ver?> /> Activo
	            &nbsp; &nbsp;
	            <input type="radio" name="Estado" id="Inactivo" value="I" <?=chkOpt($field['Estado'], "I");?> <?=$disabled_ver?> /> Inactivo
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