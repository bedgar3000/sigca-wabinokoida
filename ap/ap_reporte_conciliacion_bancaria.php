<?php
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION['ORGANISMO_ACTUAL'];
	$fPeriodo = "$AnioActual-$MesActual";
	##
	$sql = "SELECT CodBanco, NroCuenta FROM ap_ctabancaria GROUP BY CodBanco LIMIT 0, 1";
	$field_def = getRecord($sql);
	$fCodBanco = $field_def['CodBanco'];
	$fNroCuenta = $field_def['NroCuenta'];
}
//	------------------------------------
$_titulo = "Conciliaci&oacute;n Bancaria";
$_width = 900;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="ap_reporte_conciliacion_bancaria_pdf.php" method="post" autocomplete="off" target="iReporte">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />

<div class="divBorder" style="width:<?=$_width?>px;">
<table width="<?=$_width?>" class="tblFiltro">
	<tr>
		<td align="right" width="125">Organismo:</td>
		<td>
			<input type="checkbox" checked onclick="this.checked=!this.checked" />
			<select name="fCodOrganismo" id="fCodOrganismo" style="width:275px;">
				<?=getOrganismos($fCodOrganismo, 3)?>
			</select>
		</td>
		<td align="right" width="125">Banco:</td>
		<td>
			<input type="checkbox" checked="checked" onclick="this.checked=!this.checked;" />
			<select name="fCodBanco" id="fCodBanco" style="width:150px;" onChange="getOptionsSelect(this.value, 'cuentas_bancarias', 'fNroCuenta', true)">
            	<option value="">&nbsp;</option>
                <?=loadSelect("mastbancos", "CodBanco", "Banco", $fCodBanco, 0)?>
			</select>
		</td>
	</tr>
	<tr>
		<td align="right">Periodo: </td>
		<td>
			<input type="checkbox" checked onclick="this.checked=!this.checked;" />
			<input type="text" name="fPeriodo" id="fPeriodo" value="<?=$fPeriodo?>" maxlength="7" style="width:50px;" />
        </td>
		<td align="right">Cta. Bancaria:</td>
		<td>
			<input type="checkbox" style="visibility:hidden;" />
			<select name="fNroCuenta" id="fNroCuenta" style="width:150px;">
            	<option value="">&nbsp;</option>
                <?=loadSelect2("ap_ctabancaria", "NroCuenta", "NroCuenta", $fNroCuenta, 0, array("CodBanco"), array($fCodBanco))?>
			</select>
		</td>
	</tr>
</table>
</div>
<center><input type="submit" value="Buscar"></center><br />

<center>
<iframe name="iReporte" id="iReporte" style="border:solid 1px #CDCDCD; width:<?=$_width?>px; height:400px;"></iframe>
</center>
</form>