<?php
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION['ORGANISMO_ACTUAL'];
	$fCodDependencia = $_SESSION['DEPENDENCIA_ACTUAL'];
	$fFechaPreparacionD = "$AnioActual-$MesActual-01";
	$fFechaPreparacionH = $FechaActual;
}
//	------------------------------------
$_titulo = "Orden de Compra";
$_sufijo = "reporte_orden_compra";
$_width = 900;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="lg_reporte_orden_compra_detalle_pdf.php" method="post" autocomplete="off" target="iReporte">
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
		<td align="right">F.Preparaci&oacute;n: </td>
		<td>
			<input type="checkbox" checked onclick="chkFiltro_2(this.checked, 'fFechaPreparacionD', 'fFechaPreparacionH');" />
			<input type="text" name="fFechaPreparacionD" id="fFechaPreparacionD" value="<?=formatFechaDMA($fFechaPreparacionD)?>" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" />-
            <input type="text" name="fFechaPreparacionH" id="fFechaPreparacionH" value="<?=formatFechaDMA($fFechaPreparacionH)?>" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" />
        </td>
	</tr>
	<tr>
		<td align="right">Proveedor: </td>
		<td class="gallery clearfix">
            <input type="checkbox" onClick="ckLista(this.checked, ['fCodProveedor','fNomProveedor'], ['btCodProveedor'])" />            
            <input type="text" name="fCodProveedor" id="fCodProveedor" style="width:50px;" class="disabled" readonly="readonly" />
			<input type="text" name="fNomProveedor" id="fNomProveedor" style="width:235px;" class="disabled" readonly="readonly" />
            <a href="../lib/listas/listado_personas.php?filtrar=default&cod=fCodProveedor&nom=fNomProveedor&iframe=true&width=950&height=390" rel="prettyPhoto[iframe1]" id="btCodProveedor" style="visibility:hidden;">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
		<td align="right">Monto: </td>
		<td>
			<input type="checkbox" onclick="chkFiltro_2(this.checked, 'fMontoTotalD', 'fMontoTotalH');" />
			<input type="text" name="fMontoTotalD" id="fMontoTotalD" style="width:60px; text-align:right;" class="currency" disabled />-
            <input type="text" name="fMontoTotalH" id="fMontoTotalH" style="width:60px; text-align:right;" class="currency" disabled />
        </td>
	</tr>
    <tr>
		<td align="right">Almacen:</td>
		<td>
			<input type="checkbox" onclick="chkFiltro(this.checked, 'fCodAlmacen')" />
			<select name="fCodAlmacen" id="fCodAlmacen" style="width:300px;" disabled>
				<option value="">&nbsp;</option>
				<?=loadSelect("lg_almacenmast", "CodAlmacen", "Descripcion", "", 0)?>
			</select>
		</td>
		<td align="right">Estado:</td>
		<td>
        	<input type="checkbox" onclick="chkFiltro(this.checked, 'fEstadoMast');" />
            <select name="fEstadoMast" id="fEstadoMast" style="width:140px;" disabled>
                <option value="">&nbsp;</option>
                <?=loadSelectValores("ESTADO-COMPRA-FILTRO", "", 0)?>
            </select>
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
            	<a href="#" onclick="cargarPagina(document.getElementById('frmentrada'), 'lg_reporte_orden_compra_detalle_pdf.php'); mostrarTab('tab', 1, 2);">
                	Ordenes x Detalle
                </a>
            </li>
            <li id="li2" onclick="currentTab('tab', this);">
            	<a href="#" onclick="cargarPagina(document.getElementById('frmentrada'), 'lg_reporte_orden_compra_distribucion_pdf.php'); mostrarTab('tab', 2, 2);">
                	Ordenes x Distribuci&oacute;n
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
		<td align="right"></td>
		<td>
			<input type="checkbox" name="fVerDetalle" id="fVerDetalle" value="S" onclick="setVerDetalle(this.checked);" /> Ver Detalle
		</td>
		<td align="right">Dias Atraso:</td>
		<td>
			<input type="checkbox" onclick="chkFiltro(this.checked, 'fDiasAtraso');" class="fVerDetalle" disabled="disabled" />
			<input type="text" name="fDiasAtraso" id="fDiasAtraso" style="width:65px;" class="fVerDetalle" disabled />
        </td>
	</tr>
    <tr>
		<td align="right">Estado:</td>
		<td>
        	<input type="checkbox" onclick="chkFiltro(this.checked, 'fEstadoDet');" class="fVerDetalle" disabled="disabled" />
            <select name="fEstadoDet" id="fEstadoDet" style="width:140px;" class="fVerDetalle" disabled>
                <option value="">&nbsp;</option>
                <?=loadSelectGeneral("ESTADO-COMPRA-DETALLE", "", 0)?>
            </select>
		</td>
		<td align="right">Item: </td>
		<td class="gallery clearfix">
            <input type="checkbox" onClick="ckLista(this.checked,['fCodItem'],['btCodItem'])" class="fVerDetalle" disabled="disabled" />
			<input type="text" name="fCodItem" id="fCodItem" style="width:65px;" class="disabled fVerDetalleR" readonly />
            <a href="../lib/listas/listado_items.php?filtrar=default&cod=fCodItem&nom=&iframe=true&width=950&height=410" rel="prettyPhoto[iframe2]" id="btCodItem" style="visibility:hidden" class="fVerDetalleV">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
	</tr>
    <tr>
		<td align="right">Unidad:</td>
		<td>
			<input type="checkbox" onclick="chkFiltro(this.checked, 'fCodUnidad');" class="fVerDetalle" disabled="disabled" />
			<select name="fCodUnidad" id="fCodUnidad" style="width:140px;" class="fVerDetalle" disabled>
            	<option value="">&nbsp;</option>
				<?=loadSelect("mastunidades", "CodUnidad", "Descripcion", "", 0)?>
			</select>
		</td>
		<td align="right">Commodity: </td>
		<td class="gallery clearfix">
            <input type="checkbox" onClick="ckLista(this.checked,['fCommoditySub'],['btCommoditySub'])" class="fVerDetalle" disabled="disabled" />
			<input type="text" name="fCommoditySub" id="fCommoditySub" style="width:65px;" class="disabled fVerDetalleR" readonly />
            <a href="../lib/listas/listado_commodities.php?filtrar=default&cod=fCommoditySub&nom=&iframe=true&width=950&height=410" rel="prettyPhoto[iframe3]" id="btCommoditySub" style="visibility:hidden" class="fVerDetalleV">
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
		<td align="right"></td>
		<td>
			<input type="checkbox" name="fVerDistribucion" id="fVerDistribucion" value="S" onclick="setVerDetalle(this.checked);" /> Ver Distribucion
		</td>
		<td align="right">Cta. Gasto: </td>
		<td class="gallery clearfix">
            <input type="checkbox" onClick="ckLista(this.checked,['fCodCuenta'],['btCodCuenta'])" />
			<input type="text" name="fCodCuenta" id="fCodCuenta" style="width:100px;" class="disabled" readonly />
            <a href="../lib/listas/listado_plan_cuentas.php?filtrar=default&cod=fCodCuenta&nom=&iframe=true&width=950&height=475" rel="prettyPhoto[iframe4]" id="btCodCuenta" style="visibility:hidden">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
	</tr>
    <tr>
		<td align="right">Partida: </td>
		<td class="gallery clearfix">
            <input type="checkbox" onClick="ckLista(this.checked,['fcod_partida'],['btcod_partida'])" />
			<input type="text" name="fcod_partida" id="fcod_partida" style="width:100px;" class="disabled" readonly />
            <a href="../lib/listas/listado_clasificador_presupuestario.php?filtrar=default&cod=fcod_partida&nom=&iframe=true&width=950&height=475" rel="prettyPhoto[iframe5]" id="btcod_partida" style="visibility:hidden">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
		<td align="right">Cta. Gasto (Pub.20): </td>
		<td class="gallery clearfix">
            <input type="checkbox" onClick="ckLista(this.checked,['fCodCuentaPub20'],['btCodCuentaPub20'])" />
			<input type="text" name="fCodCuentaPub20" id="fCodCuentaPub20" style="width:100px;" class="disabled" readonly />
            <a href="../lib/listas/listado_plan_cuentas_pub20.php?filtrar=default&cod=fCodCuentaPub20&nom=&iframe=true&width=950&height=475" rel="prettyPhoto[iframe6]" id="btCodCuentaPub20" style="visibility:hidden">
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