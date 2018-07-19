<?php
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["ORGANISMO_ACTUAL"];
	$fCodTipoNom = $_SESSION["NOMINA_ACTUAL"];
	//	proceso por defecto
	$sql = "SELECT Periodo, CodTipoProceso
			FROM pr_procesoperiodo
			WHERE
				CodTipoNom = '".$fCodTipoNom."' AND
				CodOrganismo = '".$fCodOrganismo."' AND
				Estado = 'A' AND
				FlagAprobado = 'S'
			GROUP BY Periodo
			ORDER BY Periodo DESC
			LIMIT 0, 1";
	$query_def = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query_def) != 0) $field_def = mysql_fetch_array($query_def);
	$fPeriodo = $field_def['Periodo'];
	$fCodTipoProceso = $field_def['CodTipoProceso'];
}
//	------------------------------------
$_titulo = "Payroll de Pago x Periodo";
$_sufijo = "reporte_payroll_pago_periodo";
$_width = 900;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="pr_payroll_pdf.php" method="post" autocomplete="off" target="iReporte">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />

<div class="divBorder" style="width:<?=$_width?>px;">
<table width="<?=$_width?>" class="tblFiltro">
	<tr>
		<td align="right" width="125">Organismo:</td>
		<td>
			<input type="checkbox" checked onclick="this.checked=!this.checked" />
			<select name="fCodOrganismo" id="fCodOrganismo" style="width:275px;" onChange="loadSelect($('#fCodTipoNom'), 'tabla=loadControlNominas&CodOrganismo='+this.value, 1, destinos=['fCodTipoNom', 'fPeriodo', 'fCodTipoProceso']);">
				<?=getOrganismos($fCodOrganismo, 3)?>
			</select>
		</td>
		<td align="right" width="125">N&oacute;mina:</td>
		<td>
			<input type="checkbox" checked onclick="this.checked=!this.checked" />
			<select name="fCodTipoNom" id="fCodTipoNom" style="width:250px;" onChange="loadSelect($('#fPeriodo'), 'tabla=loadControlPeriodos&CodOrganismo='+$('#fCodOrganismo').val()+'&CodTipoNom='+this.value, 1, destinos=['fPeriodo', 'fCodTipoProceso']);">
				<?=loadControlNominas($fCodOrganismo, $fCodTipoNom)?>
			</select>
		</td>
	</tr>
	<tr>
		<td align="right">Periodo:</td>
		<td>
			<input type="checkbox" checked onclick="this.checked=!this.checked" />
            <select name="fPeriodo" id="fPeriodo" style="width:75px;" onChange="loadSelect($('#fCodTipoProceso'), 'tabla=loadControlProcesos&CodOrganismo='+$('#fCodOrganismo').val()+'&CodTipoNom='+$('#fCodTipoNom').val()+'&Periodo='+this.value, 1, destinos=['fCodTipoProceso']);">
            	<option value="">&nbsp;</option>
				<?=loadControlPeriodos($fCodOrganismo, $fCodTipoNom, $fPeriodo)?>
            </select>
		</td>
		<td align="right">Proceso:</td>
		<td>
			<input type="checkbox" checked onclick="this.checked=!this.checked" />
			<select name="fCodTipoProceso" id="fCodTipoProceso" style="width:250px;">
            	<option value="">&nbsp;</option>
				<?=loadControlProcesos($fCodOrganismo, $fCodTipoNom, $fPeriodo, $fCodTipoProceso)?>
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