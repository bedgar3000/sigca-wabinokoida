<?php
//	------------------------------------
if ($filtrar == "default") {
	$fEstado = "A";
}
//	------------------------------------
$_titulo = "Listado de Items";
$_sufijo = "reporte_item";
$_width = 900;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="lg_reporte_item_pdf.php" method="post" autocomplete="off" target="iReporte">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />

<div class="divBorder" style="width:<?=$_width?>px;">
<table width="<?=$_width?>" class="tblFiltro">
	<tr>
		<td align="right" width="150">Buscar:</td>
		<td>
			<input type="checkbox" onclick="chkFiltro(this.checked, 'fBuscar');" />
			<input type="text" name="fBuscar" id="fBuscar" style="width:195px;" disabled />
		</td>
		<td align="right" width="150">Unidad de Medida:</td>
		<td>
			<input type="checkbox" onclick="chkFiltro(this.checked, 'fCodUnidad');" />
			<select name="fCodUnidad" id="fCodUnidad" style="width:200px;" disabled>
            	<option value="">&nbsp;</option>
				<?=loadSelect("mastunidades", "CodUnidad", "Descripcion", "", 0)?>
			</select>
		</td>
	</tr>
    <tr>
		<td align="right">Tipo de Item:</td>
		<td>
			<input type="checkbox" onclick="chkFiltro(this.checked, 'fCodTipoItem');" />
			<select name="fCodTipoItem" id="fCodTipoItem" style="width:200px;" disabled>
            	<option value="">&nbsp;</option>
				<?=loadSelect("lg_tipoitem", "CodTipoItem", "Descripcion", "", 0)?>
			</select>
		</td>
		<td align="right">Estado: </td>
		<td>
        	<input type="checkbox" checked onclick="chkFiltro(this.checked, 'fEstado');" />
            <select name="fEstado" id="fEstado" style="width:105px;">
                <option value=""></option>
                <?=loadSelectGeneral("ESTADO", "A", 0)?>
            </select>
		</td>
	</tr>
    <tr>
		<td align="right">Cta. Gasto: </td>
		<td class="gallery clearfix">
            <input type="checkbox" onClick="ckLista(this.checked,['fCtaGasto'],['btCtaGasto'])" />
			<input type="text" name="fCtaGasto" id="fCtaGasto" style="width:100px;" class="disabled" readonly />
            <a href="../lib/listas/listado_plan_cuentas.php?filtrar=default&cod=fCtaGasto&nom=&iframe=true&width=950&height=475" rel="prettyPhoto[iframe1]" id="btCtaGasto" style="visibility:hidden">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
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
		<td align="right">Cta. Gasto (Pub.20): </td>
		<td class="gallery clearfix">
            <input type="checkbox" onClick="ckLista(this.checked,['fCtaGastoPub20'],['btCtaGastoPub20'])" />
			<input type="text" name="fCtaGastoPub20" id="fCtaGastoPub20" style="width:100px;" class="disabled" readonly />
            <a href="../lib/listas/listado_plan_cuentas_pub20.php?filtrar=default&cod=fCtaGastoPub20&nom=&iframe=true&width=950&height=475" rel="prettyPhoto[iframe3]" id="btCtaGastoPub20" style="visibility:hidden">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
		<td align="right">Familia: </td>
		<td>
            <input type="checkbox" style="visibility:hidden" />
			<input type="text" name="fCodFamilia" id="fCodFamilia" style="width:100px;" class="disabled" readonly />
        </td>
	</tr>
    <tr>
		<td align="right">Partida: </td>
		<td class="gallery clearfix">
            <input type="checkbox" onClick="ckLista(this.checked,['fPartidaPresupuestal'],['btPartidaPresupuestal'])" />
			<input type="text" name="fPartidaPresupuestal" id="fPartidaPresupuestal" style="width:100px;" class="disabled" readonly />
            <a href="../lib/listas/listado_clasificador_presupuestario.php?filtrar=default&cod=fPartidaPresupuestal&nom=&iframe=true&width=950&height=475" rel="prettyPhoto[iframe4]" id="btPartidaPresupuestal" style="visibility:hidden">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
		<td align="right">Sub-Familia: </td>
		<td>
            <input type="checkbox" style="visibility:hidden" />
			<input type="text" name="fCodSubFamilia" id="fCodSubFamilia" style="width:100px;" class="disabled" readonly />
        </td>
	</tr>
    <tr>
    	<td colspan="2"></td>
		<td align="right">Ordenar: </td>
		<td>
        	<input type="checkbox" checked onclick="chkFiltro(this.checked, 'fOrderBy');" />
            <select name="fOrderBy" id="fOrderBy" style="width:105px;">
                <?=loadSelectValores("ITEM-ORDERBY", "A", 0)?>
            </select>
		</td>
	</tr>
</table>
</div>
<center><input type="submit" value="Buscar"></center><br />
</form>

<center><iframe name="iReporte" id="iReporte" style="border:solid 1px #CDCDCD; width:<?=$_width?>px; height:400px;"></iframe></center>