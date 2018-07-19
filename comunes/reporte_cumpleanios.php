<?php
$FechaActual = "$AnioActual-$MesActual-$DiaActual";
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$fEdoReg = "A";
	$fSitTra = "A";
	$fMes = "$MesActual";
	$FechaFundac = getVar3("SELECT FechaFundac FROM mastorganismos WHERE CodOrganismo = '".$fCodOrganismo."'");
	list($Anio, $Mes, $Dia) = split("[./-]", $FechaFundac);
	$fVence = "$AnioActual-$Mes-$Dia";
	$fOrderBy = "CodEmpleado";
	$maxlimit = $_SESSION["MAXLIMIT"];
}
//	------------------------------------
$_titulo = "Cumplea&ntilde;os";
$_sufijo = "reporte_cumpleanios";
$_width = 900;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="reporte_cumpleanios_pdf.php" method="post" autocomplete="off" target="iReporte">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />

<div class="divBorder" style="width:<?=$_width?>px;">
<table width="<?=$_width?>" class="tblFiltro">
	<tr>
		<td align="right" width="125">Organismo:</td>
		<td>
			<input type="checkbox" checked onclick="this.checked=!this.checked" />
			<select name="fCodOrganismo" id="fCodOrganismo" style="width:275px;" onChange="getOptionsSelect(this.value, 'dependencia_filtro', 'fCodDependencia', true, 'fCodCentroCosto');">
				<?=getOrganismos($fCodOrganismo, 3)?>
			</select>
		</td>
		<td align="right" width="125">Edo. Reg: </td>
		<td>
        	<input type="checkbox" checked onclick="this.checked=!this.checked;" />
            <select name="fEdoReg" id="fEdoReg" style="width:100px;">
                <?=loadSelectGeneral("ESTADO", $fEdoReg, 1)?>
            </select>
		</td>
	</tr>
	<tr>
		<td align="right">Dependencia:</td>
		<td>
			<input type="checkbox" onclick="chkFiltro(this.checked, 'fCodDependencia');" />
			<select name="fCodDependencia" id="fCodDependencia" style="width:275px;" onChange="getOptionsSelect(this.value, 'centro_costo', 'fCodCentroCosto', true);" disabled>
            	<option value="">&nbsp;</option>
				<?=getDependencias($fCodDependencia, $fCodOrganismo, 3)?>
			</select>
		</td>
		<td align="right">Sit. Tra.: </td>
		<td>
        	<input type="checkbox" checked onclick="this.checked=!this.checked;" />
            <select name="fSitTra" id="fSitTra" style="width:100px;">
                <?=loadSelectGeneral("ESTADO", $fSitTra, 1)?>
            </select>
		</td>
	</tr>
	<tr>
		<td align="right">Centro de Costo:</td>
		<td>
			<input type="checkbox" onclick="chkFiltro(this.checked, 'fCodCentroCosto');" />
			<select name="fCodCentroCosto" id="fCodCentroCosto" style="width:275px;" disabled>
            	<option value="">&nbsp;</option>
				<?=loadSelect("ac_mastcentrocosto", "CodCentroCosto", "Descripcion", $fCodCentroCosto, 0)?>
			</select>
		</td>
		<td align="right">Tipo de Nomina:</td>
		<td>
			<input type="checkbox" onclick="chkFiltro(this.checked, 'fCodTipoNom');" />
			<select name="fCodTipoNom" id="fCodTipoNom" style="width:150px;" disabled>
            	<option value="">&nbsp;</option>
				<?=loadSelect("tiponomina", "CodTipoNom", "Nomina", $fCodTipoNom, 0)?>
			</select>
		</td>
	</tr>
	<tr>
		<td align="right">Mes de Nacimiento: </td>
		<td>
			<input type="checkbox" checked onclick="chkFiltro(this.checked, 'fMes');" />
			<select name="fMes" id="fMes" style="width:100px;">
            	<option value="">&nbsp;</option>
				<?=loadSelectGeneral("MES-NOMBRE", $fMes, 0)?>
			</select>
        </td>
		<td align="right">Tipo de Trabajador:</td>
		<td>
			<input type="checkbox" onclick="chkFiltro(this.checked, 'fCodTipoTrabajador');" />
			<select name="fCodTipoTrabajador" id="fCodTipoTrabajador" style="width:150px;" disabled>
            	<option value="">&nbsp;</option>
				<?=loadSelect("rh_tipotrabajador", "CodTipoTrabajador", "TipoTrabajador", $fCodTipoTrabajador, 0)?>
			</select>
		</td>
	</tr>
	<tr>
		<td align="right">Edad: </td>
		<td colspan="3">
			<input type="checkbox" onclick="chkFiltro_2(this.checked, 'fEdadD', 'fEdadH');" />
			<input type="text" name="fEdadD" id="fEdadD" maxlength="3" style="width:30px;" disabled /> -
            <input type="text" name="fEdadH" id="fEdadH" maxlength="3" style="width:30px;" disabled />
        </td>
	</tr>
</table>
</div>
<center><input type="submit" value="Buscar"></center><br />

<center>
<iframe name="iReporte" id="iReporte" style="border:solid 1px #CDCDCD; width:<?=$_width?>px; height:400px;"></iframe>
</center>
</form>