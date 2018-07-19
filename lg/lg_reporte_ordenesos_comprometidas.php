<?php
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION['ORGANISMO_ACTUAL'];
	$fCodDependencia = $_SESSION['DEPENDENCIA_ACTUAL'];
	$fFechaPreparacionD = "$AnioActual-$MesActual-01";
	$fFechaPreparacionH = "$AnioActual-$MesActual-$DiaActual";
}
//	------------------------------------
$_titulo = "Ordenes Comprometidas";
$_sufijo = "reporte_ordenesos_comprometidas";
$_width = 900;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="lg_reporte_ordenesos_comprometidas_distribucion_pdf.php" method="post" autocomplete="off" target="iReporte">
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
		<td align="right" width="125">F.Preparaci&oacute;n: </td>
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
            <input type="text" name="fCodProveedor" id="fCodProveedor" style="width:50px;" class="disabled" readonly />
			<input type="text" name="fNomProveedor" id="fNomProveedor" style="width:210px;" class="disabled" readonly />
            <a href="../lib/listas/listado_personas.php?filtrar=default&cod=fCodProveedor&nom=fNomProveedor&iframe=true&width=950&height=390" rel="prettyPhoto[iframe1]" id="btCodProveedor" style="visibility:hidden;">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
		<td align="right" width="125">F.Aprobaci&oacute;n: </td>
		<td>
			<input type="checkbox" onclick="chkFiltro_2(this.checked, 'fFechaAprobacionD', 'fFechaAprobacionH');" />
			<input type="text" name="fFechaAprobacionD" id="fFechaAprobacionD" value="<?=formatFechaDMA($fFechaAprobacionD)?>" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" disabled />-
            <input type="text" name="fFechaAprobacionH" id="fFechaAprobacionH" value="<?=formatFechaDMA($fFechaAprobacionH)?>" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" disabled />
        </td>
	</tr>
	<tr>
		<td align="right">Estado:</td>
		<td>
        	<input type="checkbox" onclick="chkFiltro(this.checked, 'fEstadoMast');" />
            <select name="fEstadoMast" id="fEstadoMast" style="width:140px;" disabled>
                <option value="">&nbsp;</option>
                <?=loadSelectValores("ESTADO-COMPROMISO", "", 0)?>
            </select>
		</td>
		<td align="right">Monto: </td>
		<td>
			<input type="checkbox" onclick="chkFiltro_2(this.checked, 'fMontoTotalD', 'fMontoTotalH');" />
			<input type="text" name="fMontoTotalD" id="fMontoTotalD" style="width:60px; text-align:right;" class="currency" disabled />-
            <input type="text" name="fMontoTotalH" id="fMontoTotalH" style="width:60px; text-align:right;" class="currency" disabled />
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
            	<a href="#" onclick="cargarPagina(document.getElementById('frmentrada'), 'lg_reporte_ordenesos_comprometidas_distribucion_pdf.php'); mostrarTab('tab', 1, 2);">
                	Distribuci&oacute;n
                </a>
            </li>
            <li id="li2" onclick="currentTab('tab', this);">
            	<a href="#" onclick="cargarPagina(document.getElementById('frmentrada'), 'lg_reporte_ordenesos_comprometidas_compromisos_pdf.php'); mostrarTab('tab', 2, 2);">
                	Compromisos
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
		<td align="right">Partida: </td>
		<td class="gallery clearfix">
            <input type="checkbox" onClick="ckLista(this.checked,['fcod_partida'],['btcod_partida'])" />
			<input type="text" name="fcod_partida" id="fcod_partida" style="width:100px;" class="disabled" readonly />
            <a href="../lib/listas/listado_clasificador_presupuestario.php?filtrar=default&cod=fcod_partida&nom=&iframe=true&width=950&height=475" rel="prettyPhoto[iframe5]" id="btcod_partida" style="visibility:hidden">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
		<td align="right">Cta. Gasto: </td>
		<td class="gallery clearfix">
            <input type="checkbox" onClick="ckLista(this.checked,['fCodCuenta'],['btCodCuenta'])" />
			<input type="text" name="fCodCuenta" id="fCodCuenta" style="width:100px;" class="disabled" readonly />
            <a href="../lib/listas/listado_plan_cuentas.php?filtrar=default&cod=fCodCuenta&nom=&iframe=true&width=950&height=475" rel="prettyPhoto[iframe4]" id="btCodCuenta" style="visibility:hidden">
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
<div id="tab2" style="display:none;">
</div>
<iframe name="iReporte" id="iReporte" style="border:solid 1px #CDCDCD; width:<?=$_width?>px; height:400px;"></iframe>
</center>
</form>