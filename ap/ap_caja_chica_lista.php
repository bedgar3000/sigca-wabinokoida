<?php
if ($lista == "todos") {
	$titulo = "Listado de Caja Chica";
	$btAprobar = "display:none;";
}
elseif ($lista == "aprobar") {
	$titulo = "Aprobar Caja Chica";
	$btNuevo = "display:none;";
	$btModificar = "display:none;";
}
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["ORGANISMO_ACTUAL"];
	$fEstado = "PR";
	$fOrderBy = "CodOrganismo";
	$maxlimit = $_SESSION["MAXLIMIT"];
}
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (cc.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (cc.FlagCajaChica LIKE '%".$fBuscar."%' OR
					  cc.Periodo LIKE '%".$fBuscar."%' OR
					  cc.NroCajaChica LIKE '%".$fBuscar."%' OR
					  cc.Descripcion LIKE '%".$fBuscar."%' OR
					  cc.MontoTotal LIKE '%".$fBuscar."%' OR
					  cc.FechaPreparacion LIKE '%".$fBuscar."%' OR
					  cc.FechaAprobacion LIKE '%".$fBuscar."%' OR
					  cc.CodCentroCosto LIKE '%".$fBuscar."%' OR
					  cc.CodTipoDocumento LIKE '%".$fBuscar."%' OR
					  cc.NroDocumento LIKE '%".$fBuscar."%' OR
					  CONCAT(cc.CodTipoDocumento, '-', cc.NroDocumento) LIKE '%".$fBuscar."%' OR
					  d.Dependencia LIKE '%".$fBuscar."%' OR
					  p.NomCompleto LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (cc.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fCodDependencia != "") { $cCodDependencia = "checked"; $filtro.=" AND (cc.CodDependencia = '".$fCodDependencia."')"; } else $dCodDependencia = "disabled";
if ($fCodCentroCosto != "") { $cCodCentroCosto = "checked"; $filtro.=" AND (cc.CodCentroCosto = '".$fCodCentroCosto."')"; } else $dCodCentroCosto = "disabled";
if ($fFechaPreparacionD != "" || $fFechaPreparacionH != "") {
	$cFechaPreparacion = "checked";
	if ($fFechaPreparacionD != "") $filtro.=" AND (cc.FechaPreparacion >= '".formatFechaAMD($fFechaPreparacionD)."')";
	if ($fFechaPreparacionH != "") $filtro.=" AND (cc.FechaPreparacion <= '".formatFechaAMD($fFechaPreparacionH)."')";
} else $dFechaPreparacion = "disabled";
//	------------------------------------
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=ap_caja_chica_lista" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="sel_registros" id="sel_registros" />

<div class="divBorder" style="width:1000px;">
<table width="1000" class="tblFiltro">
	<tr>
		<td align="right" width="125">Organismo:</td>
		<td>
			<input type="checkbox" <?=$cCodOrganismo?> onclick="this.checked=!this.checked" />
			<select name="fCodOrganismo" id="fCodOrganismo" style="width:300px;" <?=$dCodOrganismo?> onChange="getOptionsSelect(this.value, 'dependencia_filtro', 'fCodDependencia', true, 'fCodCentroCosto');">
				<?=getOrganismos($fCodOrganismo, 3)?>
			</select>
		</td>
		<td align="right" width="125">Buscar:</td>
		<td>
			<input type="checkbox" <?=$cBuscar?> onclick="chkFiltro(this.checked, 'fBuscar');" />
			<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:250px;" <?=$dBuscar?> />
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
		<td align="right">Fecha Preparaci&oacute;n: </td>
		<td>
			<input type="checkbox" <?=$cFechaPreparacion?> onclick="chkFiltro_2(this.checked, 'fFechaPreparacionD', 'fFechaPreparacionH');" />
			<input type="text" name="fFechaPreparacionD" id="fFechaPreparacionD" value="<?=$fFechaPreparacionD?>" <?=$dFechaPreparacion?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFecha(this);" <?=$dFechaPreparacion?> /> -
            <input type="text" name="fFechaPreparacionH" id="fFechaPreparacionH" value="<?=$fFechaPreparacionH?>" <?=$dFechaPreparacion?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFecha(this);" <?=$dFechaPreparacion?> />
        </td>
	</tr>
	<tr>
		<td align="right">Centro de Costo:</td>
		<td>
			<input type="checkbox" <?=$cCodCentroCosto?> onclick="chkFiltro(this.checked, 'fCodCentroCosto');" />
			<select name="fCodCentroCosto" id="fCodCentroCosto" style="width:300px;" <?=$dCodCentroCosto?>>
            	<option value="">&nbsp;</option>
				<?=loadSelect2("ac_mastcentrocosto", "CodCentroCosto", "Descripcion", $fCodCentroCosto, 0, array('CodDependencia'), array($fCodDependencia))?>
			</select>
		</td>
		<td align="right">Estado: </td>
		<td>
        	<?php
			if ($lista == 'aprobar') {
				?>
                <input type="checkbox" <?=$cEstado?> onclick="this.checked=!this.checked;" />
                <select name="fEstado" id="fEstado" style="width:143px;" <?=$dEstado?>>
                    <?=loadSelectValores("ESTADO-CAJACHICA", $fEstado, 1)?>
                </select>
                <?php
			} else {
				?>
                <input type="checkbox" <?=$cEstado?> onclick="chkFiltro(this.checked, 'fEstado');" />
                <select name="fEstado" id="fEstado" style="width:143px;" <?=$dEstado?>>
                    <option value=""></option>
                    <?=loadSelectValores("ESTADO-CAJACHICA", $fEstado, 0)?>
                </select>
                <?php
			}
			?>
		</td>
	</tr>
</table>
</div>
<center><input type="submit" value="Buscar"></center><br />

<center>
<table width="1000" class="tblBotones">
    <tr>
        <td><div id="rows"></div></td>
        <td align="right">
            <input type="button" id="btNuevo" value="Nuevo" style="width:75px; <?=$btNuevo?>" onclick="cargarPagina(this.form, 'gehen.php?anz=ap_caja_chica_form&opcion=nuevo');" />
            <input type="button" value="Modificar" style="width:75px; <?=$btModificar?>" class="update" onclick="cargarOpcionValidar3(this.form, $('#sel_registros').val(), 'ap_caja_chica_ajax.php', 'modulo=validar&accion=modificar', 'gehen.php?anz=ap_caja_chica_form&opcion=modificar', 'SELF', '');" />
            <input type="button" value="Aprobar" style="width:75px; <?=$btAprobar?>" class="update" onclick="cargarOpcionValidar3(this.form, $('#sel_registros').val(), 'ap_caja_chica_ajax.php', 'modulo=validar&accion=aprobar', 'gehen.php?anz=ap_caja_chica_form&opcion=aprobar', 'SELF', '');" />
            <input type="button" value="Anular" style="width:75px; <?=$btAnular?>" class="update" onclick="cargarOpcionValidar3(this.form, $('#sel_registros').val(), 'ap_caja_chica_ajax.php', 'modulo=validar&accion=anular', 'gehen.php?anz=ap_caja_chica_form&opcion=anular', 'SELF', '');" />
            <input type="button" id="btVer" value="Ver" style="width:75px; <?=$btVer?>" onclick="cargarOpcion2(this.form, 'gehen.php?anz=ap_caja_chica_form&opcion=ver', 'SELF', '', $('#sel_registros').val());" />
            <input type="button" id="btImprimir" value="Imprimir" style="width:75px;" onclick="abrirReporte(document.getElementById('frmentrada'), 'a_reporte', 'ap_caja_chica_pdf')" />
        </td>
    </tr>
</table>
<div style="overflow:scroll; width:1000px; height:350px;">
<table width="2100" class="tblLista">
    <thead>
    <tr>
        <th scope="col" width="20" onclick="order('FlagCajaChica')" title="Caja Chica / Reporte de Gasto">C/R</th>
        <th scope="col" width="50" onclick="order('Periodo')">Periodo</th>
        <th scope="col" width="50" onclick="order('NroCajaChica')">N&uacute;mero</th>
        <th scope="col" align="left" onclick="order('Descripcion')">Descripci&oacute;n</th>
        <th scope="col" width="100" onclick="order('MontoTotal')">Monto Total</th>
        <th scope="col" width="75" onclick="order('FechaPreparacion')">Fecha Preparaci&oacute;n</th>
        <th scope="col" width="75" onclick="order('FechaAprobacion')">Fecha Aprobaci&oacute;n</th>
        <th scope="col" width="100" onclick="order('Estado')">Estado</th>
        <th scope="col" width="40" onclick="order('CodCentroCosto')">C.C.</th>
        <th scope="col" colspan="2" onclick="order('NroObligacion')">Obligaci&oacute;n</th>
        <th scope="col" width="275" align="left" onclick="order('NomBeneficiario')">Beneficiario</th>
        <th scope="col" width="400" align="left" onclick="order('Dependencia')">Dependencia</th>
    </tr>
    </thead>
    
    <tbody id="lista_registros">
    <?php
    //	consulto todos
    $sql = "SELECT
				cc.FlagCajaChica,
				cc.Periodo,
				cc.NroCajaChica
            FROM
				ap_cajachica cc
				INNER JOIN mastpersonas p ON (p.CodPersona = cc.CodBeneficiario)
				INNER JOIN mastdependencias d ON (d.CodDependencia = cc.CodDependencia)
            WHERE 1 $filtro";
    $query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
    $rows_total = mysql_num_rows($query);
    
    //	consulto lista
    $sql = "SELECT
				cc.FlagCajaChica,
				cc.Periodo,
				cc.NroCajaChica,
				cc.Descripcion,
				cc.MontoTotal,
				cc.FechaPreparacion,
				cc.FechaAprobacion,
				cc.Estado,
				cc.CodCentroCosto,
				CONCAT(cc.CodTipoDocumento, '-', cc.NroDocumento) AS NroObligacion,
				cc.CodOrganismo,
				d.Dependencia,
				p.NomCompleto AS NomBeneficiario
            FROM
				ap_cajachica cc
				INNER JOIN mastpersonas p ON (p.CodPersona = cc.CodBeneficiario)
				INNER JOIN mastdependencias d ON (d.CodDependencia = cc.CodDependencia)
            WHERE 1 $filtro
            ORDER BY $fOrderBy
            LIMIT ".intval($limit).", ".intval($maxlimit);
    $query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
    $rows_lista = mysql_num_rows($query);
    while ($field = mysql_fetch_array($query)) {
        $id = $field['FlagCajaChica'].'_'.$field['Periodo'].'_'.$field['NroCajaChica'];
        ?>
        <tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
            <td align="center"><?=$field['FlagCajaChica']?></td>
            <td align="center"><?=$field['Periodo']?></td>
            <td align="center"><?=$field['NroCajaChica']?></td>
            <td><?=htmlentities($field['Descripcion'])?></td>
            <td align="right"><strong><?=number_format($field['MontoTotal'], 2, ',', '.')?></strong></td>
            <td align="center"><?=formatFechaDMA($field['FechaPreparacion'])?></td>
            <td align="center"><?=formatFechaDMA($field['FechaAprobacion'])?></td>
            <td align="center"><?=printValores("ESTADO-CAJACHICA", $field['Estado'])?></td>
            <td align="center"><?=$field['CodCentroCosto']?></td>
            <td align="center" width="25"><?=$field['CodTipoDocumento']?></td>
            <td align="center" width="125"><?=$field['NroDocumento']?></td>
            <td><?=htmlentities($field['NomBeneficiario'])?></td>
            <td><?=htmlentities($field['Dependencia'])?></td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>
</div>
<table width="1000">
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