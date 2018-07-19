<?php
//	------------------------------------
if ($filtrar == "default") {
	$fEstado = "A";
}
//	------------------------------------
$_titulo = "Listado de Commodity";
$_sufijo = "reporte_commodity";
$_width = 900;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="lg_reporte_commodity_pdf.php" method="post" autocomplete="off" target="iReporte">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />

<div class="divBorder" style="width:<?=$_width?>px;">
<table width="<?=$_width?>" class="tblFiltro">
    <tr>
		<td align="right" width="150">Clasificaci&oacute;n:</td>
		<td>
			<input type="checkbox" onclick="chkFiltro(this.checked, 'fClasificacion');" />
			<select name="fClasificacion" id="fClasificacion" style="width:200px;" onchange="loadSelect($('#fCommodityMast'), 'tabla=lg_commoditymast&fClasificacion='+this.value, 1, ['fCommodityMast'])" disabled>
            	<option value="">&nbsp;</option>
                <?=loadSelect("lg_commodityclasificacion", "Clasificacion", "Descripcion", "", 0)?>
			</select>
		</td>
		<td align="right" width="150">Activo: </td>
		<td class="gallery clearfix">
            <input type="checkbox" onClick="ckLista(this.checked,['fCodClasificacion'],['btCodClasificacion'])" />
			<input type="text" name="fCodClasificacion" id="fCodClasificacion" style="width:100px;" class="disabled" readonly />
            <a href="../lib/listas/listado_clasificacion_activos.php?filtrar=default&cod=fCodClasificacion&nom=&iframe=true&width=700&height=375" rel="prettyPhoto[iframe1]" id="btCodClasificacion" style="visibility:hidden">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
	</tr>
    <tr>
		<td align="right">Commodity:</td>
		<td>
			<input type="checkbox" onclick="chkFiltro(this.checked, 'fCommodityMast');" />
			<select name="fCommodityMast" id="fCommodityMast" style="width:200px;" disabled>
            	<option value="">&nbsp;</option>
                <?=loadSelect2("lg_commoditymast", "CommodityMast", "Descripcion", "", 0, array('Clasificacion'), "");?>
			</select>
		</td>
		<td align="right">Cta. Gasto: </td>
		<td class="gallery clearfix">
            <input type="checkbox" onClick="ckLista(this.checked,['fCodCuenta'],['btCodCuenta'])" />
			<input type="text" name="fCodCuenta" id="fCodCuenta" style="width:100px;" class="disabled" readonly />
            <a href="../lib/listas/listado_plan_cuentas.php?filtrar=default&cod=fCodCuenta&nom=&iframe=true&width=950&height=475" rel="prettyPhoto[iframe2]" id="btCodCuenta" style="visibility:hidden">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
	</tr>
    <tr>
		<td align="right">Unidad de Medida:</td>
		<td>
			<input type="checkbox" onclick="chkFiltro(this.checked, 'fCodUnidad');" />
			<select name="fCodUnidad" id="fCodUnidad" style="width:200px;" disabled>
            	<option value="">&nbsp;</option>
				<?=loadSelect("mastunidades", "CodUnidad", "Descripcion", "", 0)?>
			</select>
		</td>
		<td align="right">Cta. Gasto (Pub.20): </td>
		<td class="gallery clearfix">
            <input type="checkbox" onClick="ckLista(this.checked,['fCodCuentaPub20'],['btCodCuentaPub20'])" />
			<input type="text" name="fCodCuentaPub20" id="fCodCuentaPub20" style="width:100px;" class="disabled" readonly />
            <a href="../lib/listas/listado_plan_cuentas_pub20.php?filtrar=default&cod=fCodCuentaPub20&nom=&iframe=true&width=950&height=475" rel="prettyPhoto[iframe3]" id="btCodCuentaPub20" style="visibility:hidden">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
	</tr>
    <tr>
		<td align="right">Buscar:</td>
		<td>
			<input type="checkbox" onclick="chkFiltro(this.checked, 'fBuscar');" />
			<input type="text" name="fBuscar" id="fBuscar" style="width:195px;" disabled />
		</td>
		<td align="right">Partida: </td>
		<td class="gallery clearfix">
            <input type="checkbox" onClick="ckLista(this.checked,['fcod_partida'],['btcod_partida'])" />
			<input type="text" name="fcod_partida" id="fcod_partida" style="width:100px;" class="disabled" readonly />
            <a href="../lib/listas/listado_clasificador_presupuestario.php?filtrar=default&cod=fcod_partida&nom=&iframe=true&width=950&height=475" rel="prettyPhoto[iframe4]" id="btcod_partida" style="visibility:hidden">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
	</tr>
    <tr>
		<td align="right">Estado: </td>
		<td colspan="3">
        	<input type="checkbox" checked onclick="chkFiltro(this.checked, 'fEstado');" />
            <select name="fEstado" id="fEstado" style="width:105px;">
                <option value=""></option>
                <?=loadSelectGeneral("ESTADO", "A", 0)?>
            </select>
		</td>
	</tr>
</table>
</div>
<center><input type="submit" value="Buscar"></center><br />
</form>

<center><iframe name="iReporte" id="iReporte" style="border:solid 1px #CDCDCD; width:<?=$_width?>px; height:400px;"></iframe></center>