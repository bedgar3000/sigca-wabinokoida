<?php
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION['ORGANISMO_ACTUAL'];
	$fCodDependencia = $_SESSION['DEPENDENCIA_ACTUAL'];
	$fPeriodoD = "$AnioActual-$MesActual-01";
	$fPeriodoH = "$AnioActual-$MesActual-$DiaActual";
}
//	------------------------------------
$_titulo = "Consumo por Dependencia";
$_sufijo = "reporte_consumo_dependencia";
$_width = 900;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="lg_reporte_consumo_dependencia_pdf.php" method="post" autocomplete="off" target="iReporte">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />

<div class="divBorder" style="width:<?=$_width?>px;">
<table width="<?=$_width?>" class="tblFiltro">
	<tr>
		<td align="right" width="125">Organismo:</td>
		<td>
			<input type="checkbox" checked onclick="this.checked=!this.checked" />
			<select name="fCodOrganismo" id="fCodOrganismo" style="width:275px;" onChange="getOptionsSelect(this.value, 'dependencia_filtro', 'fCodDependencia', true, 'fCodCentroCosto');">
				<?=getOrganismos($fCodOrganismo, 3)?>
			</select>
		</td>
		<td align="right" width="125">Item: </td>
		<td class="gallery clearfix">
            <input type="checkbox" onClick="ckLista(this.checked,['fCodItem'],['btCodItem'])" />
			<input type="text" name="fCodItem" id="fCodItem" style="width:100px;" class="disabled fVerDetalleR" readonly />
            <a href="../lib/listas/listado_items.php?filtrar=default&cod=fCodItem&nom=&iframe=true&width=950&height=410" rel="prettyPhoto[iframe1]" id="btCodItem" style="visibility:hidden">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
	</tr>
	<tr>
		<td align="right">Dependencia:</td>
		<td>
			<input type="checkbox" checked onclick="chkFiltro(this.checked, 'fCodDependencia');" />
			<select name="fCodDependencia" id="fCodDependencia" style="width:275px;" onChange="getOptionsSelect(this.value, 'centro_costo', 'fCodCentroCosto', true);">
            	<option value="">&nbsp;</option>
				<?=getDependencias($fCodDependencia, $fCodOrganismo, 3)?>
			</select>
		</td>
		<td align="right">Linea: </td>
		<td class="gallery clearfix">
            <input type="checkbox" onClick="ckLista(this.checked,['fCodLinea','fCodFamilia','fCodSubFamilia'],['btCodLinea'])" />
			<input type="text" name="fCodLinea" id="fCodLinea" style="width:100px;" class="disabled" readonly />
            <a href="../lib/listas/listado_familias.php?filtrar=default&campo1=fCodLinea&campo2=fCodFamilia&campo3=fCodSubFamilia&iframe=true&width=950&height=525" rel="prettyPhoto[iframe2]" id="btCodLinea" style="visibility:hidden">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
	</tr>
	<tr>
		<td align="right">Centro de Costo:</td>
		<td>
			<input type="checkbox" onclick="chkFiltro(this.checked, 'fCodCentroCosto');" />
			<select name="fCodCentroCosto" id="fCodCentroCosto" style="width:275px;" disabled>
            	<option value="">&nbsp;</option>
				<?=loadSelect("ac_mastcentrocosto", "CodCentroCosto", "Descripcion", $fCodCentroCosto, 0)?>
			</select>
		</td>
		<td align="right">Familia: </td>
		<td>
            <input type="checkbox" style="visibility:hidden" />
			<input type="text" name="fCodFamilia" id="fCodFamilia" style="width:100px;" class="disabled" readonly />
        </td>
	</tr>
    <tr>
		<td align="right">Periodo: </td>
		<td>
			<input type="checkbox" checked onclick="this.checked=!this.checked;" />
			<input type="text" name="fPeriodoD" id="fPeriodoD" value="<?=formatFechaDMA($fPeriodoD)?>" style="width:65px;" class="datepicker" maxlength="10" /> -
            <input type="text" name="fPeriodoH" id="fPeriodoH" value="<?=formatFechaDMA($fPeriodoH)?>" style="width:65px;" class="datepicker" maxlength="10" />
        </td>
		<td align="right">Sub-Familia: </td>
		<td>
            <input type="checkbox" style="visibility:hidden" />
			<input type="text" name="fCodSubFamilia" id="fCodSubFamilia" style="width:100px;" class="disabled" readonly />
        </td>
	</tr>
</table>
</div>
<center><input type="submit" value="Buscar"></center><br />

<center>
<iframe name="iReporte" id="iReporte" style="border:solid 1px #CDCDCD; width:<?=$_width?>px; height:400px;"></iframe>
</center>
</form>