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
$_titulo = "Aniversarios";
$_sufijo = "reporte_aniversarios";
$_width = 900;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="reporte_aniversarios_lista_pdf.php" method="post" autocomplete="off" target="iReporte">
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
		<td align="right">Mes de Ingreso: </td>
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
		<td align="right">A&ntilde;os: </td>
		<td colspan="3">
			<input type="checkbox" onclick="chkFiltro_2(this.checked, 'fEdadD', 'fEdadH');" />
			<input type="text" name="fEdadD" id="fEdadD" maxlength="3" style="width:30px;" disabled /> -
            <input type="text" name="fEdadH" id="fEdadH" maxlength="3" style="width:30px;" disabled />
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
            	<a href="#" onclick="cargarPagina(document.getElementById('frmentrada'), 'reporte_aniversarios_lista_pdf.php'); mostrarTab('tab', 1, 3);">
                	Listado x Mes
                </a>
            </li>
            <li id="li2" onclick="currentTab('tab', this);">
            	<a href="#" onclick="cargarPagina(document.getElementById('frmentrada'), 'reporte_aniversarios_cuadro_pdf.php'); mostrarTab('tab', 2, 3);">
                	Cuadro Demostrativo
                </a>
            </li>
            <li id="li3" onclick="currentTab('tab', this);">
            	<a href="#" onclick="cargarPagina(document.getElementById('frmentrada'), 'reporte_aniversarios_publica_pdf.php'); mostrarTab('tab', 3, 3);">
                	Cuadro p/ Jubilaci&oacute;n
                </a>
            </li>
            </ul>
            </div>
        </td>
    </tr>
</table>

<center>
<div id="tab1" style="display:block;"></div>

<div id="tab2" style="display:none;">
<div class="divBorder" style="width:<?=$_width?>px;">
<table width="<?=$_width?>" class="tblFiltro">
	<tr>
		<td align="right" width="150">Hasta:</td>
		<td>
        	<input type="checkbox" checked onclick="chkFiltro(this.checked, 'fVence');" />
			<input type="text" name="fVence1" id="fVence1" value="<?=formatFechaDMA($fVence)?>" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFecha(this);" />
		</td>
        <td width="200">
        	<input type="checkbox" name="FlagMostrar" id="FlagMostrar" value="S" />Mostrar Todos
        </td>
	</tr>
</table>
</div>
</div>

<div id="tab3" style="display:none;">
<div class="divBorder" style="width:<?=$_width?>px;">
<table width="<?=$_width?>" class="tblFiltro">
	<tr>
		<td align="right" width="150">Hasta:</td>
		<td>
        	<input type="checkbox" checked onclick="chkFiltro(this.checked, 'fVence');" />
			<input type="text" name="fVence2" id="fVence2" value="<?=formatFechaDMA($fVence)?>" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFecha(this);" />
		</td>
	</tr>
</table>
</div>
</div>

<iframe name="iReporte" id="iReporte" style="border:solid 1px #CDCDCD; width:<?=$_width?>px; height:400px;"></iframe>
</center>
</form>