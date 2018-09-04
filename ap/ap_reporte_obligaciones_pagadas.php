<?php
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION['ORGANISMO_ACTUAL'];
	$fFechaPagoD = "$AnioActual-$MesActual-01";
	$fFechaPagoH = "$AnioActual-$MesActual-$DiaActual";
}
//	------------------------------------
$_titulo = "Obligaciones Pagadas";
$_sufijo = "reporte_obligaciones_pagadas";
$_width = 900;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="ap_reporte_obligaciones_pagadas_pdf.php" method="post" autocomplete="off" target="iReporte">
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
		<td align="right">F.Obligaci&oacute;n: </td>
		<td>
			<input type="checkbox" onclick="chkFiltro_2(this.checked, 'fFechaRegistroD', 'fFechaRegistroH');" />
			<input type="text" name="fFechaRegistroD" id="fFechaRegistroD" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" disabled="disabled" /> -
            <input type="text" name="fFechaRegistroH" id="fFechaRegistroH" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" disabled="disabled" />
        </td>
	</tr>
	<tr>
		<td align="right">Tipo de Documento:</td>
		<td>
			<input type="checkbox" onclick="chkFiltro(this.checked, 'fCodTipoDocumento');" />
			<select name="fCodTipoDocumento" id="fCodTipoDocumento" style="width:275px;" disabled>
            	<option value="">&nbsp;</option>
				<?=loadSelect("ap_tipodocumento", "CodTipoDocumento", "Descripcion", "", 10)?>
			</select>
		</td>
		<td align="right">F.Orden: </td>
		<td>
			<input type="checkbox" onclick="chkFiltro_2(this.checked, 'fFechaOrdenPagoD', 'fFechaOrdenPagoH');" />
			<input type="text" name="fFechaOrdenPagoD" id="fFechaOrdenPagoD" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" disabled="disabled" /> -
            <input type="text" name="fFechaOrdenPagoH" id="fFechaOrdenPagoH" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" disabled="disabled" />
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
		<td align="right">F.Pago: </td>
		<td>
			<input type="checkbox" checked onclick="chkFiltro_2(this.checked, 'fFechaPagoD', 'fFechaPagoH');" />
			<input type="text" name="fFechaPagoD" id="fFechaPagoD" value="<?=formatFechaDMA($fFechaPagoD)?>" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" /> -
            <input type="text" name="fFechaPagoH" id="fFechaPagoH" value="<?=formatFechaDMA($fFechaPagoH)?>" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" />
        </td>
	</tr>
</table>
</div>
<center><input type="submit" value="Buscar"></center><br />

<center>
<iframe name="iReporte" id="iReporte" style="border:solid 1px #CDCDCD; width:<?=$_width?>px; height:400px;"></iframe>
</center>
</form>