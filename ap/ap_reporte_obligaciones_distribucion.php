<?php
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION['ORGANISMO_ACTUAL'];
	$fPeriodoDesde = "$AnioActual-$MesActual";
	$fPeriodoHasta = "$AnioActual-$MesActual";
}
//	------------------------------------
$_titulo = "Causadas";
$_sufijo = "reporte_obligaciones_distribucion";
$_width = 900;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="ap_reporte_obligaciones_distribucion_proveedor_pdf.php" method="post" autocomplete="off" target="iReporte">
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
		<td align="right">Estado: </td>
		<td>
        	<input type="checkbox" onclick="chkFiltro(this.checked, 'fEstado');" />
            <select name="fEstado" id="fEstado" style="width:123px;" disabled>
                <option value="">&nbsp;</option>
                <?=loadSelectValores("ESTADO-CAUSADO", "", 0)?>
            </select>
        </td>
	</tr>
	<tr>
		<td align="right">Proveedor:</td>
		<td class="gallery clearfix">
            <input type="checkbox" onClick="ckLista(this.checked, ['fCodProveedor','fNomProveedor'], ['btCodProveedor'])" />
            <input type="text" name="fCodProveedor" id="fCodProveedor" style="width:40px;" class="disabled" readonly />
			<input type="text" name="fNomProveedor" id="fNomProveedor" style="width:220px;" class="disabled" readonly />
            <a href="../lib/listas/listado_personas.php?filtrar=default&cod=fCodProveedor&nom=fNomProveedor&iframe=true&width=950&height=390" rel="prettyPhoto[iframe1]" id="btCodProveedor" style="visibility:hidden;">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
		<td align="right">Periodo: </td>
		<td>
			<input type="checkbox" checked onclick="chkFiltro_2(this.checked, 'fPeriodoDesde', 'fPeriodoHasta');" />
			<input type="text" name="fPeriodoDesde" id="fPeriodoDesde" value="<?=$fPeriodoDesde?>" maxlength="7" style="width:50px;" /> -
			<input type="text" name="fPeriodoHasta" id="fPeriodoHasta" value="<?=$fPeriodoHasta?>" maxlength="7" style="width:50px;" />
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
            	<a href="#" onclick="cargarPagina(document.getElementById('frmentrada'), 'ap_reporte_obligaciones_distribucion_proveedor_pdf.php'); mostrarTab('tab', 1, 2);">
                	Distribuci&oacute;n x Proveedores
                </a>
            </li>
            <li id="li2" onclick="currentTab('tab', this);">
            	<a href="#" onclick="cargarPagina(document.getElementById('frmentrada'), 'ap_reporte_obligaciones_distribucion_causadas_pdf.php'); mostrarTab('tab', 2, 2);">
                	Consolidado x Partidas
                </a>
            </li>
            </ul>
            </div>
        </td>
    </tr>
</table>

<div id="tab1" style="display:block;"></div>

<div id="tab2" style="display:none;">
<div class="divBorder" style="width:<?=$_width?>px;">
<table width="<?=$_width?>" class="tblFiltro">
	<tr>
		<td align="right">&nbsp;</td>
		<td>
			<input type="checkbox" name="fFlagMostrar" id="fFlagMostrar" value="S" /> Mostrar Obligaciones
        </td>
	</tr>
</table>
</div>
</div>

<center>
<iframe name="iReporte" id="iReporte" style="border:solid 1px #CDCDCD; width:<?=$_width?>px; height:400px;"></iframe>
</center>
</form>