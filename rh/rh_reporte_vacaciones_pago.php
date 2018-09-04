<?php
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$fCodDependencia = $_SESSION["DEPENDENCIA_ACTUAL"];
}
//	------------------------------------
$_titulo = "Pago de Vacaciones";
$_sufijo = "reporte_vacaciones_pago";
$_width = 900;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="rh_reporte_vacaciones_pago_pdf.php" method="post" autocomplete="off" target="iReporte">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />

<div class="divBorder" style="width:<?=$_width?>px;">
<table width="<?=$_width?>" class="tblFiltro">
	<tr>
		<td align="right" width="125">Organismo:</td>
		<td>
			<input type="checkbox" checked onclick="this.checked=!this.checked" />
			<select name="fCodOrganismo" id="fCodOrganismo" style="width:300px;" onChange="getOptionsSelect(this.value, 'dependencia_filtro', 'fCodDependencia', true, 'fCodCentroCosto');">
				<?=getOrganismos($fCodOrganismo, 3)?>
			</select>
		</td>
		<td align="right">Buscar:</td>
		<td>
			<input type="checkbox" onclick="chkFiltro(this.checked, 'fBuscar');" />
			<input type="text" name="fBuscar" id="fBuscar" style="width:200px;" disabled />
		</td>
	</tr>
	<tr>
		<td align="right">Dependencia:</td>
		<td>
			<input type="checkbox" checked onclick="chkFiltro(this.checked, 'fCodDependencia');" />
			<select name="fCodDependencia" id="fCodDependencia" style="width:300px;" onChange="getOptionsSelect(this.value, 'centro_costo', 'fCodCentroCosto', true);">
            	<option value="">&nbsp;</option>
				<?=getDependencias($fCodDependencia, $fCodOrganismo, 3)?>
			</select>
		</td>
		<td align="right">Tipo de N&oacute;mina:</td>
		<td>
			<input type="checkbox" onclick="chkFiltro(this.checked, 'fCodTipoNom');" />
			<select name="fCodTipoNom" id="fCodTipoNom" style="width:205px;" disabled>
            	<option value="">&nbsp;</option>
				<?=loadSelect("tiponomina", "CodTipoNom", "Nomina", "", 0)?>
			</select>
		</td>
	</tr>
	<tr>
		<td align="right">Centro de Costo:</td>
		<td>
			<input type="checkbox" onclick="chkFiltro(this.checked, 'fCodCentroCosto');" />
			<select name="fCodCentroCosto" id="fCodCentroCosto" style="width:300px;" disabled>
            	<option value="">&nbsp;</option>
				<?=loadSelect("ac_mastcentrocosto", "CodCentroCosto", "Descripcion", "", 0)?>
			</select>
		</td>
		<td align="right">Fecha: </td>
		<td>
			<input type="checkbox" onclick="chkFiltro_2(this.checked, 'fFechaD', 'fFechaH');" />
			<input type="text" name="fFechaD" id="fFechaD" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFecha(this);" disabled /> -
            <input type="text" name="fFechaH" id="fFechaH" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFecha(this);" disabled />
        </td>
	</tr>
    <tr>
		<td align="right">Empleado: </td>
		<td class="gallery clearfix">
            <input type="checkbox" onclick="chkFiltroLista_3(this.checked, 'fCodPersona', 'fNomPersona', 'fCodEmpleado', 'btPersona');" />
            <input type="hidden" name="fCodEmpleado" id="fCodEmpleado" />
            <input type="hidden" name="fCodPersona" id="fCodPersona" />
			<input type="text" name="fNomPersona" id="fNomPersona" class="disabled" style="width:295px;" readonly />
            <a href="../lib/listas/listado_empleados.php?filtrar=default&cod=fCodEmpleado&nom=fNomPersona&campo3=fCodPersona&iframe=true&width=950&height=525" rel="prettyPhoto[iframe1]" id="btPersona" style="visibility:hidden;">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
	</tr>
</table>
</div>
<center><input type="submit" value="Buscar"></center><br />
</form>

<center><iframe name="iReporte" id="iReporte" style="border:solid 1px #CDCDCD; width:<?=$_width?>px; height:400px;"></iframe></center>