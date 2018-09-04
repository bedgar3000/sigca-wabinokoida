<?php
$FechaActual = "$AnioActual-$MesActual-$DiaActual";
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$fCodDependencia = $_SESSION["DEPENDENCIA_ACTUAL"];
	$fEdoReg = "A";
	$fSitTra = "A";
    $fPeriodo = $PeriodoActual;
	$fOrderBy = "CodEmpleado";
	$maxlimit = $_SESSION["MAXLIMIT"];
}
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (e.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fCodDependencia != "") { $cCodDependencia = "checked"; $filtro.=" AND (e.CodDependencia = '".$fCodDependencia."')"; } else $dCodDependencia = "disabled";
if ($fCodCentroCosto != "") { $cCodCentroCosto = "checked"; $filtro.=" AND (e.CodCentroCosto = '".$fCodCentroCosto."')"; } else $dCodCentroCosto = "disabled";
if ($fCodTipoNom != "") { $cCodTipoNom = "checked"; $filtro.=" AND (e.CodTipoNom = '".$fCodTipoNom."')"; } else $dCodTipoNom = "disabled";
if ($fCodTipoTrabajador != "") { $cCodTipoTrabajador = "checked"; $filtro.=" AND (e.CodTipoTrabajador = '".$fCodTipoTrabajador."')"; } else $dCodTipoTrabajador = "disabled";
if ($fEdoReg != "") { $cEdoReg = "checked"; $filtro.=" AND (p.Estado = '".$fEdoReg."')"; } else $dEdoReg = "disabled";
if ($fSitTra != "") { $cSitTra = "checked"; $filtro.=" AND (e.Estado = '".$fSitTra."')"; } else $dSitTra = "disabled";
if ($fFingresoD != "" || $fFingresoH != "") {
	$cFingreso = "checked";
	if ($fFingresoD != "") $filtro.=" AND (e.Fingreso >= '".formatFechaAMD($fFingresoD)."')";
	if ($fFingresoH != "") $filtro.=" AND (e.Fingreso <= '".formatFechaAMD($fFingresoH)."')";
} else $dFingreso = "disabled";
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (e.CodEmpleado LIKE '%".$fBuscar."%' OR
					  p.NomCompleto LIKE '%".$fBuscar."%' OR
					  p.Ndocumento LIKE '%".$fBuscar."%' OR
					  pt.DescripCargo LIKE '%".$fBuscar."%' OR
					  d.Dependencia LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
//	------------------------------------
$_titulo = "Constancia de Trabajo";
$_sufijo = "reporte_constancia";
$_width = 900;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=<?=$_sufijo?>" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="sel_registros" id="sel_registros" />

<div class="divBorder" style="width:<?=$_width?>px;">
<table width="<?=$_width?>" class="tblFiltro">
	<tr>
		<td align="right" width="125">Organismo:</td>
		<td>
			<input type="checkbox" <?=$cCodOrganismo?> onclick="this.checked=!this.checked" />
			<select name="fCodOrganismo" id="fCodOrganismo" style="width:300px;" <?=$dCodOrganismo?> onChange="getOptionsSelect(this.value, 'dependencia_filtro', 'fCodDependencia', true, 'fCodCentroCosto');">
				<?=getOrganismos($fCodOrganismo, 3)?>
			</select>
		</td>
		<td align="right" width="125">Edo. Reg: </td>
		<td>
        	<input type="checkbox" <?=$cEdoReg?> onclick="this.checked=!this.checked;" />
            <select name="fEdoReg" id="fEdoReg" style="width:143px;" <?=$dEdoReg?>>
                <?=loadSelectGeneral("ESTADO", $fEdoReg, 1)?>
            </select>
		</td>
	</tr>
	<tr>
		<td align="right">Dependencia:</td>
		<td>
			<input type="checkbox" <?=$cCodDependencia?> onclick="chkFiltro(this.checked, 'fCodDependencia');" />
			<select name="fCodDependencia" id="fCodDependencia" style="width:300px;" onChange="getOptionsSelect(this.value, 'centro_costo', 'fCodCentroCosto', true);" <?=$dCodDependencia?>>
            	<option value="">&nbsp;</option>
				<?=getDependencias($fCodDependencia, $fCodOrganismo, 3)?>
			</select>
		</td>
		<td align="right">Sit. Tra.: </td>
		<td>
        	<input type="checkbox" <?=$cSitTra?> onclick="this.checked=!this.checked;" />
            <select name="fSitTra" id="fSitTra" style="width:143px;" <?=$dSitTra?>>
                <?=loadSelectGeneral("ESTADO", $fSitTra, 1)?>
            </select>
		</td>
	</tr>
	<tr>
		<td align="right">Centro de Costo:</td>
		<td>
			<input type="checkbox" <?=$cCodCentroCosto?> onclick="chkFiltro(this.checked, 'fCodCentroCosto');" />
			<select name="fCodCentroCosto" id="fCodCentroCosto" style="width:300px;" <?=$dCodCentroCosto?>>
            	<option value="">&nbsp;</option>
				<?=loadSelect("ac_mastcentrocosto", "CodCentroCosto", "Descripcion", $fCodCentroCosto, 0)?>
			</select>
		</td>
		<td align="right">Fecha de Ingreso: </td>
		<td>
			<input type="checkbox" <?=$cFingreso?> onclick="chkFiltro_2(this.checked, 'fFingresoD', 'fFingresoH');" />
			<input type="text" name="fFingresoD" id="fFingresoD" value="<?=$fFingresoD?>" <?=$dFingreso?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFecha(this);" /> -
            <input type="text" name="fFingresoH" id="fFingresoH" value="<?=$fFingresoH?>" <?=$dFingreso?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFecha(this);" />
        </td>
	</tr>
	<tr>
		<td align="right">Tipo de Nomina:</td>
		<td>
			<input type="checkbox" <?=$cCodTipoNom?> onclick="chkFiltro(this.checked, 'fCodTipoNom');" />
			<select name="fCodTipoNom" id="fCodTipoNom" style="width:300px;" <?=$dCodTipoNom?>>
            	<option value="">&nbsp;</option>
				<?=loadSelect("tiponomina", "CodTipoNom", "Nomina", $fCodTipoNom, 0)?>
			</select>
		</td>
		<td align="right">Buscar:</td>
		<td>
			<input type="checkbox" <?=$cBuscar?> onclick="chkFiltro(this.checked, 'fBuscar');" />
			<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:250px;" <?=$dBuscar?> />
		</td>
	</tr>
	<tr>
		<td align="right">Tipo de Trabajador:</td>
		<td>
			<input type="checkbox" <?=$cCodTipoTrabajador?> onclick="chkFiltro(this.checked, 'fCodTipoTrabajador');" />
			<select name="fCodTipoTrabajador" id="fCodTipoTrabajador" style="width:300px;" <?=$dCodTipoTrabajador?>>
            	<option value="">&nbsp;</option>
				<?=loadSelect("rh_tipotrabajador", "CodTipoTrabajador", "TipoTrabajador", $fCodTipoTrabajador, 0)?>
			</select>
		</td>
		<td align="right">&nbsp;</td>
		<td>
            <input type="checkbox" name="FlagSueldo" id="FlagSueldo" value="S" /> Mostrar Sueldo
            &nbsp; &nbsp; &nbsp; 
            <input type="checkbox" name="FlagSueldoIntegral" id="FlagSueldoIntegral" value="S" /> Mostrar Sueldo Integral
		</td>
	</tr>
</table>
</div>
<center><input type="submit" value="Buscar"></center><br />

<center>
<table width="<?=$_width?>" class="tblBotones">
    <tr>
        <td><div id="rows"></div></td>
        <td>
            Periodo: 
            <input type="text" name="fPeriodo" id="fPeriodo" value="<?=$fPeriodo?>" style="width:50px;" />
        </td>
        <td align="right">
            <input type="button" id="btImprimir" value="Imprimir" style="width:80px;" onclick="abrirReporteVal('a_reporte', 'reporte_constancia_pdf', '', '', $('#sel_registros'), 0, this.form)" />
        </td>
    </tr>
</table>
<div style="overflow:scroll; width:<?=$_width?>px; height:350px;">
<table width="1600" class="tblLista">
    <thead>
    <tr>
        <th width="25" onclick="order('EdoReg')">Es. Reg.</th>
        <th width="25" onclick="order('SitTra')">Sit. Tra.</th>
        <th width="25" onclick="order('FlagFaltaGrave')">Fal. Gr.</th>
        <th width="60" onclick="order('CodEmpleado')">C&oacute;digo</th>
        <th width="300" align="left" onclick="order('NomCompleto')">Nombre Completo</th>
        <th width="75" align="right" onclick="order('LENGTH(Ndocumento), Ndocumento')">Nro. Documento</th>
        <th width="75" onclick="order('Fingreso')">Fecha de Ingreso</th>
        <th width="500" align="left" onclick="order('DescripCargo')">Cargo</th>
        <th align="left" onclick="order('Dependencia')">Dependencia</th>
    </tr>
    </thead>
    
    <tbody id="lista_registros">
		<?php
        //	consulto todos
        $sql = "SELECT p.CodPersona
                FROM
                    mastpersonas p
                    INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
                    INNER JOIN rh_puestos pt ON (pt.CodCargo = e.CodCargo)
                    INNER JOIN mastdependencias d ON (d.CodDependencia = e.CodDependencia)
                    LEFT JOIN rh_motivocese mc ON (e.CodMotivoCes = mc.CodMotivoCes)
                WHERE 1 $filtro";
        $query = mysql_query($sql) or die ($sql.mysql_error());
        $rows_total = mysql_num_rows($query);
        
        //	consulto lista
        $sql = "SELECT
                    p.CodPersona,
                    p.NomCompleto,
                    p.Ndocumento,
                    p.Estado AS EdoReg,
                    e.CodEmpleado,
                    e.Fingreso,
                    e.Estado AS SitTra,
                    pt.DescripCargo,
                    d.Dependencia,
                    mc.FlagFaltaGrave
                FROM
                    mastpersonas p
                    INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
                    INNER JOIN rh_puestos pt ON (pt.CodCargo = e.CodCargo)
                    INNER JOIN mastdependencias d ON (d.CodDependencia = e.CodDependencia)
                    LEFT JOIN rh_motivocese mc ON (e.CodMotivoCes = mc.CodMotivoCes)
                WHERE 1 $filtro
                ORDER BY $fOrderBy
                LIMIT ".intval($limit).", ".intval($maxlimit);
        $query = mysql_query($sql) or die ($sql.mysql_error());
        $rows_lista = mysql_num_rows($query);
        while ($field = mysql_fetch_array($query)) {
            $id = "$field[CodPersona]";
            ?>
            <tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
                <td align="center"><?=printEstado($field['EdoReg'])?></td>
                <td align="center"><?=printEstado($field['SitTra'])?></td>
                <td align="center"><?=printWarning($field['FlagFaltaGrave'])?></td>
                <td align="center"><?=$field['CodEmpleado']?></td>
                <td><?=htmlentities($field['NomCompleto'])?></td>
                <td align="right"><?=number_format($field['Ndocumento'], 0, '', '.')?></td>
                <td align="center"><?=formatFechaDMA($field['Fingreso'])?></td>
                <td><?=htmlentities($field['DescripCargo'])?></td>
                <td><?=htmlentities($field['Dependencia'])?></td>
            </tr>
            <?php
        }
        ?>
        </tbody>
</table>
</div>
<table width="<?=$_width?>">
    <tr>
        <td>
            Mostrar: 
            <select name="maxlimit" style="width:50px;" onchange="this.form.submit();">
                <?=loadSelectGeneral("MAXLIMIT", $maxlimit, 0)?>
            </select>
        </td>
        <td align="right">
            <?=paginacion(intval($rows_total), intval($rows_lista), intval($maxlimit), intval($limit));?>
        </td>
    </tr>
</table>
</center>
</form>

<div class="gallery clearfix">
    <a href="pagina.php?iframe=true" rel="prettyPhoto[iframe1]" style="display:none;" id="a_reporte"></a>
</div>