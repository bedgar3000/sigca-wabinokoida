<?php
list($AnioActual, $MesActual, $DiaActual) = split("[/.-]", substr($Ahora, 0, 10));
$FechaActual = "$AnioActual-$MesActual-$DiaActual";
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$fCodDependencia = $_SESSION["DEPENDENCIA_ACTUAL"];
	$fEdoReg = "A";
	$fSitTra = "A";
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
$_titulo = "Acumulados del Fideicomiso";
$_width = 900;
$_sufijo = "fideicomiso_acumulado";
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=pr_<?=$_sufijo?>_lista" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
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
        	<input type="checkbox" <?=$cEdoReg?> onclick="chkFiltro(this.checked, 'fEdoReg');" />
            <select name="fEdoReg" id="fEdoReg" style="width:143px;" <?=$dEdoReg?>>
                <option value=""></option>
                <?=loadSelectGeneral("ESTADO", $fEdoReg, 0)?>
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
        	<input type="checkbox" <?=$cSitTra?> onclick="chkFiltro(this.checked, 'fSitTra');" />
            <select name="fSitTra" id="fSitTra" style="width:143px;" <?=$dSitTra?>>
                <option value=""></option>
                <?=loadSelectGeneral("ESTADO", $fSitTra, 0)?>
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
	</tr>
</table>
</div>
<center><input type="submit" value="Buscar"></center><br />

<center>
<table width="<?=$_width?>" class="tblBotones">
    <tr>
        <td><div id="rows"></div></td>
        <td align="right">
            <input type="button" id="btAgregar" value="Actualizar Acumulado" style="width:125px;" onclick="cargarOpcion2(this.form, 'gehen.php?anz=pr_<?=$_sufijo?>_form', 'SELF', '', $('#sel_registros').val());" />
            
            <input type="button" id="btVerDepositos" value="Ver Dep&oacute;sitos" style="width:100px;" onclick="abrirIFrame(this.form, 'a_ver', 'gehen.php?anz=pr_fideicomiso_acumulado_depositos', 710, 500, $('#sel_registros').val());" />
        </td>
    </tr>
</table>
<div style="overflow:scroll; width:<?=$_width?>px; height:350px;">
<table width="1350" class="tblLista">
    <thead>
    <tr>
        <th rowspan="2" width="15">&nbsp;</th>
        <th rowspan="2" width="50">C&oacute;digo</th>
        <th rowspan="2" align="left">Nombre Completo</th>
        <th rowspan="2" width="75">Fecha de Ingreso</th>
        <th colspan="4">Inicial</th>
        <th colspan="4">Dep&oacute;sitos</th>
        <th colspan="4">Total Acumulado</th>
    </tr>
    <tr>
        <th width="35">Dias</th>
        <th width="35">Dias Adic.</th>
        <th width="75">Antiguedad</th>
        <th width="75">Fideicomiso</th>
        <th width="35">Dias</th>
        <th width="35">Dias Adic.</th>
        <th width="75">Antiguedad</th>
        <th width="75">Fideicomiso</th>
        <th width="35">Dias</th>
        <th width="35">Dias Adic.</th>
        <th width="75">Antiguedad</th>
        <th width="75">Fideicomiso</th>
    </tr>
    </thead>
    
    <tbody id="lista_registros">
    <?php
    //	consulto todos
    $sql = "SELECT p.CodPersona
            FROM
				mastpersonas p
				INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
				LEFT JOIN pr_acumuladofideicomiso af ON (af.CodPersona = p.CodPersona)
            WHERE 1 $filtro";
    $query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
    $rows_total = mysql_num_rows($query);
    
    //	consulto lista
    $sql = "SELECT
				p.CodPersona,
				p.NomCompleto,
				e.CodEmpleado,
				e.Fingreso,
				af.AcumuladoInicialDias,
				af.AcumuladoInicialProv,
				af.AcumuladoInicialFide,
				af.AcumuladoProvDias,
				af.AcumuladoProv,
				af.AcumuladoFide,
				af.AcumuladoDiasAdicionalInicial,
				af.AcumuladoDiasAdicional,
				(af.AcumuladoInicialDias + af.AcumuladoProvDias) AS TotalDias,
				(af.AcumuladoDiasAdicionalInicial + af.AcumuladoDiasAdicional) AS TotalDiasAdicional,
				(af.AcumuladoInicialProv + af.AcumuladoProv) AS TotalAntiguedad,
				(af.AcumuladoInicialFide + af.AcumuladoFide) AS TotalFideicomiso
            FROM
				mastpersonas p
				INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
				LEFT JOIN pr_acumuladofideicomiso af ON (af.CodPersona = p.CodPersona)
            WHERE 1 $filtro
            ORDER BY $fOrderBy
            LIMIT ".intval($limit).", ".intval($maxlimit);
    $query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
    $rows_lista = mysql_num_rows($query);	$i=0;
    while ($field = mysql_fetch_array($query)) {
        $id = "$field[CodPersona]";
		$TotalDias = $field['AcumuladoInicialDias'] + $field['AcumuladoProvDias'];
		$TotalAntiguedad = $field['AcumuladoInicialProv'] + $field['AcumuladoProv'];
		$TotalFideicomiso = $field['AcumuladoInicialFide'] + $field['AcumuladoFide'];
        ?>
        <tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
            <th><?=++$i?></th>
            <td align="center"><?=$field['CodEmpleado']?></td>
            <td><?=htmlentities($field['NomCompleto'])?></td>
            <td align="center"><?=formatFechaDMA($field['Fingreso'])?></td>
            <td align="right"><?=number_format($field['AcumuladoInicialDias'], 2, ',', '.')?></td>
            <td align="right"><?=number_format($field['AcumuladoDiasAdicionalInicial'], 2, ',', '.')?></td>
            <td align="right"><?=number_format($field['AcumuladoInicialProv'], 2, ',', '.')?></td>
            <td align="right"><?=number_format($field['AcumuladoInicialFide'], 2, ',', '.')?></td>
            <td align="right"><?=number_format($field['AcumuladoProvDias'], 2, ',', '.')?></td>
            <td align="right"><?=number_format($field['AcumuladoDiasAdicional'], 2, ',', '.')?></td>
            <td align="right"><?=number_format($field['AcumuladoProv'], 2, ',', '.')?></td>
            <td align="right"><?=number_format($field['AcumuladoFide'], 2, ',', '.')?></td>
            <td align="right"><strong><?=number_format($field['TotalDias'], 2, ',', '.')?></strong></td>
            <td align="right"><strong><?=number_format($field['TotalDiasAdicional'], 2, ',', '.')?></strong></td>
            <td align="right"><strong><?=number_format($field['TotalAntiguedad'], 2, ',', '.')?></strong></td>
            <td align="right"><strong><?=number_format($field['TotalFideicomiso'], 2, ',', '.')?></strong></td>
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
    <a href="pagina.php?iframe=true" rel="prettyPhoto[iframe1]" style="display:none;" id="a_ver"></a>
</div>