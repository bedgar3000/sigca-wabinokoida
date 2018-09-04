<?php
list($AnioActual, $MesActual, $DiaActual) = split("[/.-]", substr($Ahora, 0, 10));
$FechaActual = "$AnioActual-$MesActual-$DiaActual";
//	------------------------------------
//	consulto datos generales
$sql = "SELECT
			p.CodPersona,
			p.NomCompleto,
			p.Ndocumento,
			e.CodEmpleado,
			e.Fingreso,
			af.CodPersona AS PersonaIngresada,
			af.PeriodoInicial,
			af.AcumuladoInicialDias,
			af.AcumuladoInicialProv,
			af.AcumuladoInicialFide,
			af.AcumuladoProvDias,
			af.AcumuladoProv,
			af.AcumuladoFide,
			af.AcumuladoDiasAdicionalInicial,
			af.AcumuladoDiasAdicional,
			af.UltimoUsuario,
			af.UltimaFecha,
			(af.AcumuladoInicialDias + af.AcumuladoProvDias) AS TotalDias,
			(af.AcumuladoDiasAdicionalInicial + af.AcumuladoDiasAdicional) AS TotalDiasAdicional,
			(af.AcumuladoInicialProv + af.AcumuladoProv) AS TotalAntiguedad,
			(af.AcumuladoInicialFide + af.AcumuladoFide) AS TotalFideicomiso
		FROM
			mastpersonas p
			INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
			LEFT JOIN pr_acumuladofideicomiso af ON (af.CodPersona = p.CodPersona)
		WHERE p.CodPersona = '".$sel_registros."'";
$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
if (mysql_num_rows($query)) $field = mysql_fetch_array($query);
//	------------------------------------
$_titulo = "Actualizar Acumulados";
$_width = 800;
$_sufijo = "fideicomiso_acumulado";
$label_submit = "Actualizar";
$clkCancelar = "document.getElementById('frmentrada').submit();";
//	modificar
if (!$field['PersonaIngresada']) {
	$field['PeriodoInicial'] = "$AnioActual-$MesActual";
}
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=pr_<?=$_sufijo?>_lista" method="POST" enctype="multipart/form-data" onsubmit="return <?=$_sufijo?>(this);" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
<input type="hidden" name="fEdoReg" id="fEdoReg" value="<?=$fEdoReg?>" />
<input type="hidden" name="fCodDependencia" id="fCodDependencia" value="<?=$fCodDependencia?>" />
<input type="hidden" name="fSitTra" id="fSitTra" value="<?=$fSitTra?>" />
<input type="hidden" name="fCodCentroCosto" id="fCodCentroCosto" value="<?=$fCodCentroCosto?>" />
<input type="hidden" name="fFingresoD" id="fFingresoD" value="<?=$fFingresoD?>" />
<input type="hidden" name="fFingresoH" id="fFingresoH" value="<?=$fFingresoH?>" />
<input type="hidden" name="fCodTipoNom" id="fCodTipoNom" value="<?=$fCodTipoNom?>" />
<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />

<table width="<?=$_width?>" class="tblForm">
	<tr>
    	<td colspan="4" class="divFormCaption">Datos Generales</td>
    </tr>
	<tr>
		<td class="tagForm" width="125">Empleado:</td>
		<td>
            <input type="hidden" id="CodPersona" value="<?=$field['CodPersona']?>" />
            <input type="text" value="<?=$field['CodEmpleado']?>" style="width:55px;" disabled="disabled" />
            <input type="text" value="<?=htmlentities($field['NomCompleto'])?>" style="width:200px;" disabled="disabled" />
        </td>
		<td class="tagForm" width="125">Fecha de Ingreso:</td>
		<td>
        	<input type="text" value="<?=formatFechaDMA($field['Fingreso'])?>" style="width:60px;" disabled />
        </td>
	</tr>
    <tr>
		<td class="tagForm">Organismo:</td>
		<td>
            <select id="CodOrganismo" style="width:270px;" disabled>
                <?=loadSelect("mastorganismos", "CodOrganismo", "Organismo", $field['CodOrganismo'], 0)?>
            </select>
		</td>
		<td class="tagForm">Nro. Documento:</td>
		<td>
        	<input type="text" value="<?=number_format($field['Ndocumento'], 0, '', '.')?>" style="width:60px;" disabled />
        </td>
	</tr>
    <tr>
		<td class="tagForm">Dependencia:</td>
		<td>
            <select style="width:270px;" disabled>
                <?=loadSelect("mastdependencias", "CodDependencia", "Dependencia", $field['CodDependencia'], 0)?>
            </select>
		</td>
		<td class="tagForm">* Periodo Inicial:</td>
		<td>
        	<input type="text" id="PeriodoInicial" value="<?=$field['PeriodoInicial']?>" style="width:60px;" maxlength="7" <?=$disabled_modificar?> />
        </td>
	</tr>
</table>
<table width="<?=$_width?>" class="tblForm">
	<tr>
    	<td colspan="2" class="divFormCaption">Acumulado Inicial</td>
    	<td colspan="2" class="divFormCaption">Dep&oacute;sitos</td>
    	<td colspan="2" class="divFormCaption">Total Acumulado</td>
    </tr>
    <tr>
		<td class="tagForm" width="125">Dias:</td>
		<td>
        	<input type="text" id="AcumuladoInicialDias" value="<?=number_format($field['AcumuladoInicialDias'], 2, ',', '.')?>" style="width:80px; text-align:right;" class="currency" onChange="fideicomiso_acumulado_totales();" <?=$disabled_modificar?> />
        </td>
		<td class="tagForm" width="125">Dias:</td>
		<td>
        	<input type="text" id="AcumuladoProvDias" value="<?=number_format($field['AcumuladoProvDias'], 2, ',', '.')?>" style="width:80px; text-align:right;" class="currency" disabled />
        </td>
		<td class="tagForm" width="125"><strong>Dias:</strong></td>
		<td>
        	<input type="text" id="TotalDias" value="<?=number_format($field['TotalDias'], 2, ',', '.')?>" style="width:80px; text-align:right; font-weight:bold;" class="currency" disabled />
        </td>
	</tr>
    <tr>
		<td class="tagForm">Dias Adic.:</td>
		<td>
        	<input type="text" id="AcumuladoDiasAdicionalInicial" value="<?=number_format($field['AcumuladoDiasAdicionalInicial'], 2, ',', '.')?>" style="width:80px; text-align:right;" class="currency" onChange="fideicomiso_acumulado_totales();" <?=$disabled_modificar?> />
        </td>
		<td class="tagForm">Dias Adic.:</td>
		<td>
        	<input type="text" id="AcumuladoDiasAdicional" value="<?=number_format($field['AcumuladoDiasAdicional'], 2, ',', '.')?>" style="width:80px; text-align:right;" class="currency" disabled />
        </td>
		<td class="tagForm"><strong>Dias Adic.:</strong></td>
		<td>
        	<input type="text" id="TotalDiasAdicional" value="<?=number_format($field['TotalDiasAdicional'], 2, ',', '.')?>" style="width:80px; text-align:right; font-weight:bold;" class="currency" disabled />
        </td>
	</tr>
    <tr>
		<td class="tagForm">Antiguedad:</td>
		<td>
        	<input type="text" id="AcumuladoInicialProv" value="<?=number_format($field['AcumuladoInicialProv'], 2, ',', '.')?>" style="width:80px; text-align:right;" class="currency" onChange="fideicomiso_acumulado_totales();" <?=$disabled_modificar?> />
        </td>
		<td class="tagForm">Antiguedad:</td>
		<td>
        	<input type="text" id="AcumuladoProv" value="<?=number_format($field['AcumuladoProv'], 2, ',', '.')?>" style="width:80px; text-align:right;" class="currency" disabled />
        </td>
		<td class="tagForm"><strong>Antiguedad:</strong></td>
		<td>
        	<input type="text" id="TotalAntiguedad" value="<?=number_format($field['TotalAntiguedad'], 2, ',', '.')?>" style="width:80px; text-align:right; font-weight:bold;" class="currency" disabled />
        </td>
	</tr>
    <tr>
		<td class="tagForm">Fideicomiso:</td>
		<td>
        	<input type="text" id="AcumuladoInicialFide" value="<?=number_format($field['AcumuladoInicialFide'], 2, ',', '.')?>" style="width:80px; text-align:right;" class="currency" onChange="fideicomiso_acumulado_totales();" <?=$disabled_modificar?> />
        </td>
		<td class="tagForm">Fideicomiso:</td>
		<td>
        	<input type="text" id="AcumuladoFide" value="<?=number_format($field['AcumuladoFide'], 2, ',', '.')?>" style="width:80px; text-align:right;" class="currency" disabled />
        </td>
		<td class="tagForm"><strong>Fideicomiso:</strong></td>
		<td>
        	<input type="text" id="TotalFideicomiso" value="<?=number_format($field['TotalFideicomiso'], 2, ',', '.')?>" style="width:80px; text-align:right; font-weight:bold;" class="currency" disabled />
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
<input type="submit" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" />
<input type="button" value="Cancelar" style="width:75px;" onclick="<?=$clkCancelar?>" />
</center>
</form>

<div style="width:<?=$_width?>px" class="divMsj">Campos Obligatorios *</div>