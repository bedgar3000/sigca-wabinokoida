<?php
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION['ORGANISMO_ACTUAL'];
	$fPeriodo = "$AnioActual-$MesActual";
}
//	------------------------------------
$_titulo = "Listado de Obligaciones";
$_sufijo = "reporte_obligaciones_lista";
$_width = 900;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="ap_reporte_obligaciones_lista_pdf.php" method="post" autocomplete="off" target="iReporte">
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
		<td align="right" width="125">Estado:</td>
		<td>
        	<input type="checkbox" onclick="chkFiltro(this.checked, 'fEstado');" />
            <select name="fEstado" id="fEstado" style="width:143px;" disabled>
                <option value="">&nbsp;</option>
                <?=loadSelectValores("ESTADO-OBLIGACIONES", "", 0)?>
            </select>
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
		<td align="right">F.Obligaci&oacute;n: </td>
		<td>
			<input type="checkbox" onclick="chkFiltro_2(this.checked, 'fFechaRegistroD', 'fFechaRegistroH');" />
			<input type="text" name="fFechaRegistroD" id="fFechaRegistroD" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" disabled="disabled" /> -
            <input type="text" name="fFechaRegistroH" id="fFechaRegistroH" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" disabled="disabled" />
        </td>
	</tr>
	<tr>
		<td align="right">Proveedor:</td>
		<td class="gallery clearfix">
            <input type="checkbox" onClick="ckLista(this.checked, ['fCodProveedor','fNomProveedor'], ['btCodProveedor'])" />
            <input type="text" name="fCodProveedor" id="fCodProveedor" style="width:40px;" class="disabled" readonly="readonly" />
			<input type="text" name="fNomProveedor" id="fNomProveedor" style="width:220px;" class="disabled" readonly="readonly" />
            <a href="../lib/listas/listado_personas.php?filtrar=default&cod=fCodProveedor&nom=fNomProveedor&iframe=true&width=950&height=390" rel="prettyPhoto[iframe1]" id="btCodProveedor" style="visibility:hidden;">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
		<td align="right">F.Aprobaci&oacute;n: </td>
		<td>
			<input type="checkbox" onclick="chkFiltro_2(this.checked, 'fFechaAprobacionD', 'fFechaAprobacionH');" />
			<input type="text" name="fFechaAprobacionD" id="fFechaAprobacionD" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" disabled="disabled" /> -
            <input type="text" name="fFechaAprobacionH" id="fFechaAprobacionH" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" disabled="disabled" />
        </td>
	</tr>
	<tr>
		<td align="right">Ingresado Por:</td>
		<td class="gallery clearfix">
            <input type="checkbox" onClick="ckLista(this.checked, ['fIngresadoPor','fCodEmpleado','fNomIngresadoPor'], ['btIngresadoPor'])" />
			<input type="hidden" name="fIngresadoPor" id="fIngresadoPor" />
            <input type="text" name="fCodEmpleado" id="fCodEmpleado" style="width:40px;" class="disabled" readonly="readonly" />
			<input type="text" name="fNomIngresadoPor" id="fNomIngresadoPor" style="width:220px;" class="disabled" readonly="readonly" />
            <a href="../lib/listas/listado_empleados.php?filtrar=default&cod=fCodEmpleado&nom=fNomIngresadoPor&campo3=fIngresadoPor&iframe=true&width=950&height=525" rel="prettyPhoto[iframe2]" id="btCodProveedor" style="visibility:hidden;">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
		<td align="right">F.Pago: </td>
		<td>
			<input type="checkbox" onclick="chkFiltro_2(this.checked, 'fFechaPagoD', 'fFechaPagoH');" />
			<input type="text" name="fFechaPagoD" id="fFechaPagoD" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" disabled="disabled" /> -
            <input type="text" name="fFechaPagoH" id="fFechaPagoH" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" disabled="disabled" />
        </td>
	</tr>
	<tr>
		<td align="right" width="150">Categoria Prog.:</td>
		<td class="gallery clearfix">
			<input type="checkbox" onclick="ckLista(this.checked, ['fCategoriaProg'], ['aCategoriaProg']);" />
			<input type="text" name="fCategoriaProg" id="fCategoriaProg" value="<?=$fCategoriaProg?>" style="width:150px;" readonly="readonly" />
			<a href="../lib/listas/gehen.php?anz=lista_pv_categoriaprog&filtrar=default&ventana=pv_metas&campo1=fCategoriaProg&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe10]" id="aCategoriaProg" style="visibility:hidden;">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
		</td>
		<td align="right">Periodo: </td>
		<td>
			<input type="checkbox" checked onclick="chkFiltro(this.checked, 'fPeriodo');" />
			<input type="text" name="fPeriodo" id="fPeriodo" value="<?=$fPeriodo?>" maxlength="7" style="width:60px;" />
        </td>
	</tr>
	<tr>
		<td align="right">&nbsp;</td>
		<td>
            <input type="checkbox" name="fFlagTotales" id="fFlagTotales" value="S" /> Totales x Persona
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
            	<a href="#" onclick="cargarPagina(document.getElementById('frmentrada'), 'ap_reporte_obligaciones_lista_pdf.php'); mostrarTab('tab', 1, 2);">
                	Listado de Obligaciones
                </a>
            </li>
            <li id="li2" onclick="currentTab('tab', this);">
            	<a href="#" onclick="cargarPagina(document.getElementById('frmentrada'), 'ap_reporte_obligaciones_adelanto_pdf.php'); mostrarTab('tab', 2, 2);">
                	Obligaciones Vs. Adelantos
                </a>
            </li>
            </ul>
            </div>
        </td>
    </tr>
</table>

<center>
<iframe name="iReporte" id="iReporte" style="border:solid 1px #CDCDCD; width:<?=$_width?>px; height:400px;"></iframe>
</center>
</form>