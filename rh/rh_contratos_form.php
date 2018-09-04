<?php
if ($opcion == "nuevo") {
	$sql = "SELECT
				c.*,
				p.CodPersona,
				p.NomCompleto,
				p.Ndocumento,
				e.CodEmpleado,
				e.CodOrganismo,
				e.Fingreso,
				o.Organismo
			FROM
				rh_contratos c
				INNER JOIN mastpersonas p ON (p.CodPersona = c.CodPersona)
				INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
				INNER JOIN mastorganismos o On (o.CodOrganismo = e.CodOrganismo)
			WHERE c.CodContrato = '$sel_registros'";
	$field = getRecord($sql);
	$field['TipoContrato'] = 'DE';
	$field['FechaDesde'] = $field['Fingreso'];
	$field['Estado'] = 'VI';
	$field['FlagFirma'] = 'S';
	##
	$_titulo = "Nuevo Contrato";
	$accion = "nuevo";
	$disabled_ver = "";
	$disabled_determinado = "";
	$disabled_firma = "";
	$display_submit = "";
	$label_submit = "Guardar";
	$focus = "CodOrganismo";
}
elseif ($opcion == "modificar" || $opcion == "renovar" || $opcion == "ver") {
	$sql = "SELECT
				c.*,
				p.CodPersona,
				p.NomCompleto,
				p.Ndocumento,
				e.CodEmpleado,
				e.CodOrganismo,
				e.Fingreso,
				o.Organismo,
				tc.TipoContrato
			FROM
				rh_contratos c
				INNER JOIN mastpersonas p ON (p.CodPersona = c.CodPersona)
				INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
				INNER JOIN mastorganismos o On (o.CodOrganismo = e.CodOrganismo)
				LEFT JOIN rh_formatocontrato fc ON (fc.CodFormato = c.CodFormato)
				LEFT JOIN rh_tipocontrato tc ON (tc.TipoContrato = fc.TipoContrato)
			WHERE c.CodContrato = '$sel_registros'";
	$field = getRecord($sql);
	##	modificar
	if ($opcion == "modificar") {
		$_titulo = "Modificar Contrato";
		$accion = "nuevo";
		$disabled_ver = "";
		if ($field['TipoContrato'] == 'DE') $disabled_determinado = ""; else $disabled_determinado = "disabled";
		if ($field['FlagFirma'] == 'S') $disabled_firma = ""; else $disabled_firma = "disabled";
		$display_submit = "";
		$label_submit = "Modificar";
		$focus = "CodOrganismo";
	}
	##	renovar
	elseif ($opcion == "renovar") {
		$field['Estado'] = 'VI';
		$field['FlagFirma'] = 'S';
		$field['FechaDesde'] = formatFechaAMD(fechaFin(formatFechaDMA($field['FechaHasta']), 1));
		$field['FechaHasta'] = '';
		$field['FechaFirma'] = '';
		$field['FechaContrato'] = '';
		$field['Contrato'] = '';
		##	
		$_titulo = "Renovar Contrato";
		$accion = "renovar";
		$disabled_ver = "";
		if ($field['TipoContrato'] == 'DE') $disabled_determinado = ""; else $disabled_determinado = "disabled";
		$disabled_firma = "";
		$display_submit = "";
		$label_submit = "Renovar";
		$focus = "CodOrganismo";
	}
	##	ver
	elseif ($opcion == "ver") {
		$_titulo = "Ver Contrato";
		$accion = "";
		$disabled_ver = "disabled";
		$disabled_determinado = "disabled";
		$disabled_firma = "disabled";
		$display_submit = "display:none;";
		$label_submit = "";
		$focus = "CodOrganismo";
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

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=rh_contratos_lista" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('rh_contratos_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
<input type="hidden" name="ficha" id="ficha" value="<?=$ficha?>" />
<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
<input type="hidden" name="fCodDependencia" id="fCodDependencia" value="<?=$fCodDependencia?>" />
<input type="hidden" name="fTipoContrato" id="fTipoContrato" value="<?=$fTipoContrato?>" />
<input type="hidden" name="fCodFormato" id="fCodFormato" value="<?=$fCodFormato?>" />
<input type="hidden" name="fFechaDesde" id="fFechaDesde" value="<?=$fFechaDesde?>" />
<input type="hidden" name="fFechaHasta" id="fFechaHasta" value="<?=$fFechaHasta?>" />
<input type="hidden" name="fFechaFirmaD" id="fFechaFirmaD" value="<?=$fFechaFirmaD?>" />
<input type="hidden" name="fFechaFirmaH" id="fFechaFirmaH" value="<?=$fFechaFirmaH?>" />
<input type="hidden" name="CodContrato" id="CodContrato" value="<?=$field['CodContrato']?>" />
<input type="hidden" name="Secuencia" id="Secuencia" value="<?=$field['Secuencia']?>" />

<table width="<?=$_width?>" class="tblForm">
	<tr>
    	<td colspan="2" class="divFormCaption">Datos Generales</td>
    </tr>
    <tr>
		<td class="tagForm" width="150">Empleado:</td>
		<td>
			<input type="hidden" name="CodPersona" id="CodPersona" value="<?=$field['CodPersona']?>" />
        	<input type="text" name="CodEmpleado" id="CodEmpleado" value="<?=$field['CodEmpleado']?>" style="width:45px; font-weight:bold;" disabled />
        	<input type="text" name="NomCompleto" id="NomCompleto" value="<?=$field['NomCompleto']?>" style="width:226px; font-weight:bold;" disabled />
		</td>
	</tr>
    <tr>
		<td class="tagForm">Organismo:</td>
		<td>
        	<input type="text" name="Organismo" id="Organismo" value="<?=$field['Organismo']?>" style="width:275px; font-weight:bold;" disabled />
			<input type="hidden" name="CodOrganismo" id="CodOrganismo" value="<?=$field['CodOrganismo']?>" />
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Tipo de Contrato:</td>
		<td>
			<select name="TipoContrato" id="TipoContrato" style="width:143px;" <?=$disabled_ver?> onChange="loadSelect($('#CodFormato'), 'tabla=rh_formatocontrato&TipoContrato='+$(this).val(), 1); setTipoContrato(this.value);">
				<?=loadSelect2('rh_tipocontrato','TipoContrato','Descripcion',$field['TipoContrato'])?>
			</select>
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Formato de Contrato:</td>
		<td>
			<select name="CodFormato" id="CodFormato" style="width:143px;" <?=$disabled_ver?>>
				<option value="">&nbsp;</option>
				<?=loadSelect2('rh_formatocontrato','CodFormato','Documento',$field['CodFormato'],0,['TipoContrato'],[$field['TipoContrato']])?>
			</select>
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Vigencia de Contrato:</td>
		<td>
        	<input type="text" name="FechaDesde" id="FechaDesde" value="<?=formatFechaDMA($field['FechaDesde'])?>" style="width:65px;" maxlength="10" class="datepicker" <?=$disabled_ver?> /> -
        	<input type="text" name="FechaHasta" id="FechaHasta" value="<?=formatFechaDMA($field['FechaHasta'])?>" style="width:65px;" maxlength="10" class="datepicker" <?=$disabled_determinado?> />
		</td>
	</tr>
	<tr>
		<td class="tagForm">&nbsp;</td>
		<td>
            <input type="checkbox" name="FlagFirma" id="FlagFirma" value="S" <?=chkOpt($field['FlagFirma'], "S");?> <?=$disabled_ver?> onchange="$('#FechaFirma').attr('disabled', !this.checked).val('');" /> Firma?
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Fecha de Firma:</td>
		<td>
        	<input type="text" name="FechaFirma" id="FechaFirma" value="<?=formatFechaDMA($field['FechaFirma'])?>" style="width:65px;" maxlength="10" class="datepicker" <?=$disabled_firma?> />
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Fecha de Contrato:</td>
		<td>
        	<input type="text" name="FechaContrato" id="FechaContrato" value="<?=formatFechaDMA($field['FechaContrato'])?>" style="width:65px;" maxlength="10" class="datepicker" <?=$disabled_ver?> />
		</td>
	</tr>
    <tr>
		<td class="tagForm">Contrato:</td>
		<td>
        	<input type="text" name="Contrato" id="Contrato" value="<?=$field['Contrato']?>" style="width:65px;" maxlength="10" <?=$disabled_ver?> />
		</td>
	</tr>
    <tr>
		<td class="tagForm">Observaci&oacute;n:</td>
		<td>
        	<textarea name="Comentarios" id="Comentarios" style="width:275px; height:50px;" <?=$disabled_ver?>><?=$field['Comentarios']?></textarea>
		</td>
	</tr>
	<tr>
		<td class="tagForm">Estado:</td>
		<td>
            <input type="radio" name="Estado" id="Vigente" value="VI" <?=chkOpt($field['Estado'], "VI");?> <?=$disabled_ver?> /> Vigente
            &nbsp; &nbsp;
            <input type="radio" name="Estado" id="Vencido" value="VE" <?=chkOpt($field['Estado'], "VE");?> <?=$disabled_ver?> /> Vencido
		</td>
	</tr>
    <tr>
		<td class="tagForm">&Uacute;ltima Modif.:</td>
		<td>
			<input type="text" value="<?=$field['UltimoUsuario']?>" style="width:140px;" disabled="disabled" />
			<input type="text" value="<?=$field['UltimaFecha']?>" style="width:110px" disabled="disabled" />
		</td>
	</tr>
</table>

<center>
<input type="submit" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" id="btSubmit" />
<input type="button" value="Cancelar" style="width:75px;" id="btCancelar" onclick="<?=$clkCancelar?>" />
</center>
</form>
<div style="width:<?=$_width?>px" class="divMsj">Campos Obligatorios *</div>

<script type="text/javascript">
	function setTipoContrato(TipoContrato) {
		if (TipoContrato == 'DE') $('#FechaHasta').attr('disabled', false).val('');
		else $('#FechaHasta').attr('disabled', true).val('');
	}
</script>