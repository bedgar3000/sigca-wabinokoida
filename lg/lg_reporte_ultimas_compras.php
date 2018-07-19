<?php
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION['ORGANISMO_ACTUAL'];
	$fCodDependencia = $_SESSION['DEPENDENCIA_ACTUAL'];
	$fFechaAprobacionD = "$AnioActual-$MesActual-01";
	$fFechaAprobacionH = $FechaActual;
}
//	------------------------------------
$_titulo = "Ultimas Compras Realizadas";
$_sufijo = "reporte_ultimas_compras";
$_width = 900;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="lg_reporte_ultimas_compras_item_pdf.php" method="post" autocomplete="off" target="iReporte">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />

<div class="divBorder" style="width:<?=$_width?>px;">
<table width="<?=$_width?>" class="tblFiltro">
	<tr>
		<td align="right" width="125">Organismo:</td>
		<td>
			<input type="checkbox" checked onclick="this.checked=!this.checked" />
			<select name="fCodOrganismo" id="fCodOrganismo" style="width:300px;" onchange="getOptionsSelect(this.value, 'dependencia', 'fCodDependencia', true, 'fCodCentroCosto');">
				<?=getOrganismos($fCodOrganismo, 3)?>
			</select>
		</td>
		<td align="right">F.Aprobaci&oacute;n: </td>
		<td>
			<input type="checkbox" checked onclick="this.checked=!this.checked" />
			<input type="text" name="fFechaAprobacionD" id="fFechaAprobacionD" value="<?=formatFechaDMA($fFechaAprobacionD)?>" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" />-
            <input type="text" name="fFechaAprobacionH" id="fFechaAprobacionH" value="<?=formatFechaDMA($fFechaAprobacionH)?>" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" />
        </td>
	</tr>
	<tr>
		<td align="right">Proveedor: </td>
		<td class="gallery clearfix">
            <input type="checkbox" onClick="ckLista(this.checked, ['fCodProveedor','fNomProveedor'], ['btCodProveedor'])" />            
            <input type="text" name="fCodProveedor" id="fCodProveedor" style="width:50px;" class="disabled" readonly />
			<input type="text" name="fNomProveedor" id="fNomProveedor" style="width:235px;" class="disabled" readonly />
            <a href="../lib/listas/listado_personas.php?filtrar=default&cod=fCodProveedor&nom=fNomProveedor&iframe=true&width=950&height=390" rel="prettyPhoto[iframe1]" id="btCodProveedor" style="visibility:hidden;">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
		<td align="right">Almacen:</td>
		<td>
			<input type="checkbox" onclick="chkFiltro(this.checked, 'fCodAlmacen')" />
			<select name="fCodAlmacen" id="fCodAlmacen" style="width:140px;" disabled>
				<option value="">&nbsp;</option>
				<?=loadSelect("lg_almacenmast", "CodAlmacen", "Descripcion", "", 0)?>
			</select>
		</td>
	</tr>
    <tr>
		<td align="right" colspan="3"></td>
		<td>
			<input type="checkbox" name="fVerDetalle" id="fVerDetalle" value="S" /> Ver Detalle
		</td>
	</tr>
</table>
</div>
<center><input type="submit" value="Buscar"></center><br />

<table width="<?=$_width?>" align="center" cellpadding="0" cellspacing="0">
    <tr>
        <td>
            <div class="header">
            <ul id="tab">
            <!-- CSS Tabs -->
            <li id="li1" onclick="currentTab('tab', this);" class="current">
            	<a href="#" onclick="cargarPagina(document.getElementById('frmentrada'), 'lg_reporte_ultimas_compras_item_pdf.php'); mostrarTab('tab', 1, 2);">
                	Stock
                </a>
            </li>
            <li id="li2" onclick="currentTab('tab', this);">
            	<a href="#" onclick="cargarPagina(document.getElementById('frmentrada'), 'lg_reporte_ultimas_compras_commodity_pdf.php'); mostrarTab('tab', 2, 2);">
                	Commotities
                </a>
            </li>
            </ul>
            </div>
        </td>
    </tr>
</table>

<center>
<div id="tab1" style="display:block;">
<div class="divBorder" style="width:<?=$_width?>px;">
<table width="<?=$_width?>" class="tblFiltro">
    <tr>
		<td align="right">Linea: </td>
		<td class="gallery clearfix">
            <input type="checkbox" onClick="ckLista(this.checked,['fCodLinea','fCodFamilia','fCodSubFamilia'],['btCodLinea'])" />
			<input type="text" name="fCodLinea" id="fCodLinea" style="width:75px;" class="disabled" readonly />
            <a href="../lib/listas/listado_familias.php?filtrar=default&campo1=fCodLinea&campo2=fCodFamilia&campo3=fCodSubFamilia&iframe=true&width=950&height=525" rel="prettyPhoto[iframe2]" id="btCodLinea" style="visibility:hidden">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
		<td align="right">Familia: </td>
		<td>
            <input type="checkbox" style="visibility:hidden" />
			<input type="text" name="fCodFamilia" id="fCodFamilia" style="width:75px;" class="disabled" readonly />
        </td>
		<td align="right">Sub-Familia: </td>
		<td>
            <input type="checkbox" style="visibility:hidden" />
			<input type="text" name="fCodSubFamilia" id="fCodSubFamilia" style="width:75px;" class="disabled" readonly />
        </td>
		<td align="right">Item: </td>
		<td class="gallery clearfix">
            <input type="checkbox" onClick="ckLista(this.checked,['fCodItem'],['btCodItem'])" />
			<input type="text" name="fCodItem" id="fCodItem" style="width:75px;" class="disabled" readonly />
            <a href="../lib/listas/listado_items.php?filtrar=default&cod=fCodItem&nom=&iframe=true&width=950&height=410" rel="prettyPhoto[iframe3]" id="btCodItem" style="visibility:hidden">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
	</tr>
</table>
</div>
</div>

<div id="tab2" style="display:none;">
<div class="divBorder" style="width:<?=$_width?>px;">
<table width="<?=$_width?>" class="tblFiltro">
    <tr>
		<td align="right" width="125">Commodity: </td>
		<td class="gallery clearfix">
            <input type="checkbox" onClick="ckLista(this.checked,['fCommoditySub'],['btCommoditySub'])" class="fVerDetalle" />
			<input type="text" name="fCommoditySub" id="fCommoditySub" style="width:65px;" class="disabled fVerDetalleR" readonly />
            <a href="../lib/listas/listado_commodities.php?filtrar=default&cod=fCommoditySub&nom=&iframe=true&width=950&height=410" rel="prettyPhoto[iframe7]" id="btCommoditySub" style="visibility:hidden" class="fVerDetalleV">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
	</tr>
</table>
</div>
</div>

<iframe name="iReporte" id="iReporte" style="border:solid 1px #CDCDCD; width:<?=$_width?>px; height:400px;"></iframe>
</center>

</form>