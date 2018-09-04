<?php
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION['ORGANISMO_ACTUAL'];
	$fCodDependencia = $_SESSION['DEPENDENCIA_ACTUAL'];
	$fFechaPreparacionD = "$AnioActual-$MesActual-01";
	$fFechaPreparacionH = $FechaActual;
}
//	------------------------------------
$_titulo = "Detallado por Requerimientos";
$_sufijo = "reporte_requerimiento_detallado";
$_width = 900;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="lg_reporte_requerimiento_detallado_pdf.php" method="post" autocomplete="off" target="iReporte">
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
		<td align="right">Dirigido a:</td>
		<td>
			<input type="checkbox" onclick="chkFiltro(this.checked, 'fTipoClasificacion')" />
			<select name="fTipoClasificacion" id="fTipoClasificacion" style="width:140px;" disabled>
				<option value="">&nbsp;</option>
				<?=loadSelectValores("DIRIGIDO", "", 0)?>
			</select>
		</td>
	</tr>
    <tr>
		<td align="right">Dependencia:</td>
		<td>
			<input type="checkbox" checked onclick="chkFiltro(this.checked, 'fCodDependencia')" />
			<select name="fCodDependencia" id="fCodDependencia" style="width:300px;">
				<option value="">&nbsp;</option>
				<?=getDependencias($fCodDependencia, $fCodOrganismo, 3);?>
			</select>
		</td>
		<td align="right">Clasificaci&oacute;n:</td>
		<td>
			<input type="checkbox" onclick="chkFiltro(this.checked, 'fClasificacion')" />
			<select name="fClasificacion" id="fClasificacion" style="width:140px;" disabled>
				<option value="">&nbsp;</option>
				<?=loadSelect("lg_clasificacion", "Clasificacion", "Descripcion", "", 0)?>
			</select>
		</td>
	</tr>
	<tr>
		<td align="right">Centro de Costo:</td>
		<td>
			<input type="checkbox" onclick="chkFiltro(this.checked, 'fCodCentroCosto')" />
			<select name="fCodCentroCosto" id="fCodCentroCosto" style="width:300px;" disabled>
				<option value="">&nbsp;</option>
				<?=loadSelectDependiente("ac_mastcentrocosto", "CodCentroCosto", "Descripcion", "CodDependencia", "", $fCodDependencia, 0)?>
			</select>
		</td>
		<td align="right">Estado:</td>
		<td>
        	<input type="checkbox" onclick="chkFiltro(this.checked, 'fEstado');" />
            <select name="fEstado" id="fEstado" style="width:140px;" disabled>
                <option value="">&nbsp;</option>
                <?=loadSelectValores("ESTADO-REQUERIMIENTO-DETALLE", "", 0)?>
            </select>
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
		<td align="right">F.Preparaci&oacute;n: </td>
		<td>
			<input type="checkbox" checked onclick="chkFiltro_2(this.checked, 'fFechaPreparacionD', 'fFechaPreparacionH');" />
			<input type="text" name="fFechaPreparacionD" id="fFechaPreparacionD" value="<?=formatFechaDMA($fFechaPreparacionD)?>" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" />-
            <input type="text" name="fFechaPreparacionH" id="fFechaPreparacionH" value="<?=formatFechaDMA($fFechaPreparacionH)?>" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" />
        </td>
	</tr>
    <tr>
		<td align="right">Buscar:</td>
		<td>
			<input type="checkbox" onclick="chkFiltro(this.checked, 'fBuscar');" />
			<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:295px;" disabled />
		</td>
	</tr>
</table>
</div>
<center><input type="submit" value="Buscar"></center><br />
</form>

<center><iframe name="iReporte" id="iReporte" style="border:solid 1px #CDCDCD; width:<?=$_width?>px; height:400px;"></iframe></center>