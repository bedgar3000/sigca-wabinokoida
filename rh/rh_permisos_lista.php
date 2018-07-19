<?php
if ($lista == "todos") {
	$titulo = "Listado de Permisos";
    $btNuevo = "";
    $btModificar = "";
	$btAprobar = "display:none;";
    $btAnular = "";
}
elseif ($lista == "aprobar") {
	$titulo = "Aprobar Permisos";
	$btNuevo = "display:none;";
	$btModificar = "display:none;";
    $btAprobar = "";
    $btAnular = "";
}
//	------------------------------------
if ($filtrar == "default") {
    $fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
    $fCodDependencia = $_SESSION["DEPENDENCIA_ACTUAL"];
    $fFechaIngresoD = "01-$MesActual-$AnioActual";
    $fFechaIngresoH = formatFechaDMA($FechaActual);
	$fEstado = "P";
	$fOrderBy = "CodPermiso";
	$maxlimit = $_SESSION["MAXLIMIT"];
}
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (pm.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (pm.CodPermiso LIKE '%".$fBuscar."%' OR
                      e1.CodEmpleado LIKE '%".$fBuscar."%' OR
                      p1.NomCompleto LIKE '%".$fBuscar."%' OR
                      md1.Descripcion LIKE '%".$fBuscar."%' OR
                      md2.Descripcion LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (e1.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fCodDependencia != "") { $cCodDependencia = "checked"; $filtro.=" AND (e1.CodDependencia = '".$fCodDependencia."')"; } else $dCodDependencia = "disabled";
if ($fTipoPermiso != "") { $cTipoPermiso = "checked"; $filtro.=" AND (pm.TipoPermiso = '".$fTipoPermiso."')"; } else $dTipoPermiso = "disabled";
if ($fTipoFalta != "") { $cTipoFalta = "checked"; $filtro.=" AND (pm.TipoFalta = '".$fTipoFalta."')"; } else $dTipoFalta = "disabled";
if ($fFechaIngresoD != "" || $fFechaIngresoH != "") {
	$cFechaIngreso = "checked";
	if ($fFechaIngresoD != "") $filtro.=" AND (pm.FechaIngreso >= '".formatFechaDMA($fFechaIngresoD)."')";
	if ($fFechaIngresoH != "") $filtro.=" AND (pm.FechaIngreso <= '".formatFechaDMA($fFechaIngresoH)."')";
} else $dFechaIngreso = "disabled";
//	------------------------------------
$width = 1000;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=rh_permisos_lista" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="sel_registros" id="sel_registros" value="<?=$_CodPermiso?>" />

<div class="divBorder" style="width:<?=$width?>px;">
<table width="<?=$width?>" class="tblFiltro">
	<tr>
		<td align="right" width="125">Organismo:</td>
		<td>
			<input type="checkbox" <?=$cCodOrganismo?> onclick="this.checked=!this.checked" />
			<select name="fCodOrganismo" id="fCodOrganismo" style="width:300px;" <?=$dCodOrganismo?> onChange="getOptionsSelect(this.value, 'dependencia_filtro', 'fCodDependencia', true);">
				<?=getOrganismos($fCodOrganismo, 3)?>
			</select>
		</td>
        <td align="right" width="125">Fecha Preparaci&oacute;n: </td>
        <td>
            <input type="checkbox" <?=$cFechaIngreso?> onclick="chkFiltro_2(this.checked, 'fFechaIngresoD', 'fFechaIngresoH');" />
            <input type="text" name="fFechaIngresoD" id="fFechaIngresoD" value="<?=$fFechaIngresoD?>" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFecha(this);" <?=$dFechaIngreso?> /> -
            <input type="text" name="fFechaIngresoH" id="fFechaIngresoH" value="<?=$fFechaIngresoH?>" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFecha(this);" <?=$dFechaIngreso?> />
        </td>
	</tr>
	<tr>
		<td align="right">Dependencia:</td>
		<td>
			<input type="checkbox" <?=$cCodDependencia?> onclick="chkFiltro(this.checked, 'fCodDependencia');" />
			<select name="fCodDependencia" id="fCodDependencia" style="width:300px;" <?=$dCodDependencia?>>
            	<option value="">&nbsp;</option>
				<?=getDependencias($fCodDependencia, $fCodOrganismo, 3)?>
			</select>
		</td>
        <td align="right">Estado: </td>
        <td>
            <?php
            if ($lista == "todos") {
                ?>
                <input type="checkbox" <?=$cEstado?> onclick="chkFiltro(this.checked, 'fEstado');" />
                <select name="fEstado" id="fEstado" style="width:143px;" <?=$dEstado?>>
                    <option value=""></option>
                    <?=loadSelectValores("ESTADO-PERMISOS", $fEstado)?>
                </select>
                <?php
            } else {
                ?>
                <input type="checkbox" <?=$cEstado?> onclick="this.checked=!this.checked;" />
                <select name="fEstado" id="fEstado" style="width:143px;" <?=$dEstado?>>
                    <?=loadSelectValores("ESTADO-PERMISOS", $fEstado, 1)?>
                </select>
                <?php
            }
            ?>
        </td>
	</tr>
	<tr>
        <td align="right">Tipo de Permiso:</td>
        <td>
            <input type="checkbox" <?=$cTipoPermiso?> onclick="chkFiltro(this.checked, 'fTipoPermiso');" />
            <select name="fTipoPermiso" id="fTipoPermiso" style="width:300px;" <?=$dTipoPermiso?>>
                <option value="">&nbsp;</option>
                <?=getMiscelaneos($fTipoPermiso, 'PERMISOS')?>
            </select>
        </td>
        <td align="right">Tipo de Falta: </td>
        <td>
            <input type="checkbox" <?=$cTipoFalta?> onclick="chkFiltro(this.checked, 'fTipoFalta');" />
            <select name="fTipoFalta" id="fTipoFalta" style="width:143px;" <?=$dTipoFalta?>>
                <option value=""></option>
                <?=getMiscelaneos($fTipoFalta, 'TIPOFALTAS')?>
            </select>
        </td>
	</tr>
    <tr>
        <td align="right">Buscar:</td>
        <td>
            <input type="checkbox" <?=$cBuscar?> onclick="chkFiltro(this.checked, 'fBuscar');" />
            <input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:295px;" <?=$dBuscar?> />
        </td>
    </tr>
</table>
</div>
<center><input type="submit" value="Buscar"></center><br />

<center>
<table width="<?=$width?>" class="tblBotones">
    <tr>
        <td><div id="rows"></div></td>
        <td align="right">
            <input type="button" id="btNuevo" value="Nuevo" style="width:75px; <?=$btNuevo?>" onclick="cargarPagina(this.form, 'gehen.php?anz=rh_permisos_form&opcion=nuevo&return=rh_permisos_lista');" /> |
            
            <input type="button" id="btModificar" value="Modificar" style="width:75px; <?=$btModificar?>" onclick="cargarOpcionValidar3(this.form, $('#sel_registros').val(), 'rh_permisos_ajax.php', 'modulo=ajax&accion=permisos_modificar', 'gehen.php?anz=rh_permisos_form&opcion=modificar&return=rh_permisos_lista', 'SELF', '');" />
            
            <input type="button" id="btVer" value="Ver" style="width:75px; <?=$btVer?>" onclick="cargarOpcion2(this.form, 'gehen.php?anz=rh_permisos_form&opcion=ver&return=rh_permisos_lista', 'SELF', '', $('#sel_registros').val());" />
            
            <input type="button" id="btAprobar" value="Aprobar" style="width:75px; <?=$btAprobar?>" onclick="cargarOpcionValidar3(this.form, $('#sel_registros').val(), 'rh_permisos_ajax.php', 'modulo=ajax&accion=permisos_aprobar', 'gehen.php?anz=rh_permisos_form&opcion=aprobar&return=rh_permisos_lista', 'SELF', '');" />
            
            <input type="button" id="btAnular" value="Anular" style="width:75px; <?=$btAnular?>" onclick="cargarOpcionValidar3(this.form, $('#sel_registros').val(), 'rh_permisos_ajax.php', 'modulo=ajax&accion=permisos_anular', 'gehen.php?anz=rh_permisos_form&opcion=anular&return=rh_permisos_lista', 'SELF', '');" /> |
            
            <input type="button" id="btImprimir" value="Imprimir" style="width:75px;" onclick="abrirReporteVal('a_reporte', 'rh_permisos_pdf', 0, 0, $('#sel_registros'), 0, document.getElementById('frmentrada'));" />
        </td>
    </tr>
</table>
<div style="overflow:scroll; width:<?=$width?>px; height:250px;">
<table width="1300" class="tblLista">
    <thead>
    <tr>
        <th scope="col" width="75" onclick="order('CodPermiso')"># Permiso</th>
        <th scope="col" width="50" onclick="order('CodEmpleado')">Empleado</th>
        <th scope="col" align="left" onclick="order('NomCompleto')">Nombre Completo</th>
        <th scope="col" width="190" onclick="order('NomTipoPermiso')">Tipo de Permiso</th>
        <th scope="col" width="110" onclick="order('NomTipoFalta')">Tipo de Falta</th>
        <th scope="col" width="75" onclick="order('FechaIngreso')">Fecha Preparaci&oacute;n</th>
        <th scope="col" width="60" onclick="order('Estado')">Estado</th>
        <th scope="col" width="300" align="left" onclick="order('NomAprobador')">Aprueba</th>
    </tr>
    </thead>
    
    <tbody id="lista_registros">
    <?php
    //	consulto todos
    $sql = "SELECT pm.CodPermiso
            FROM
                rh_permisos pm
                INNER JOIN mastpersonas p1 ON (p1.CodPersona = pm.CodPersona)
                INNER JOIN mastempleado e1 ON (e1.CodPersona = p1.CodPersona)
                INNER JOIN mastpersonas p2 ON (p2.CodPersona = pm.Aprobador)
                LEFT JOIN mastmiscelaneosdet md1 ON (md1.CodDetalle = pm.TipoPermiso AND
                                                     md1.CodMaestro = 'PERMISOS' AND
                                                     md1.CodAplicacion = 'RH')
                LEFT JOIN mastmiscelaneosdet md2 ON (md2.CodDetalle = pm.TipoFalta AND
                                                     md2.CodMaestro = 'TIPOFALTAS' AND
                                                     md2.CodAplicacion = 'RH')
            WHERE 1 $filtro";
    $query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
    $rows_total = mysql_num_rows($query);
    
    //	consulto lista
    $sql = "SELECT
                pm.CodPermiso,
                pm.FechaIngreso,
                pm.Estado,
                p1.NomCompleto,
                e1.CodEmpleado,
                p2.NomCompleto As NomAprobador,
                md1.Descripcion AS NomTipoPermiso,
                md2.Descripcion AS NomTipoFalta
            FROM
                rh_permisos pm
				INNER JOIN mastpersonas p1 ON (p1.CodPersona = pm.CodPersona)
                INNER JOIN mastempleado e1 ON (e1.CodPersona = p1.CodPersona)
                INNER JOIN mastpersonas p2 ON (p2.CodPersona = pm.Aprobador)
                LEFT JOIN mastmiscelaneosdet md1 ON (md1.CodDetalle = pm.TipoPermiso AND
                                                     md1.CodMaestro = 'PERMISOS' AND
                                                     md1.CodAplicacion = 'RH')
                LEFT JOIN mastmiscelaneosdet md2 ON (md2.CodDetalle = pm.TipoFalta AND
                                                     md2.CodMaestro = 'TIPOFALTAS' AND
                                                     md2.CodAplicacion = 'RH')
            WHERE 1 $filtro
            ORDER BY $fOrderBy
            LIMIT ".intval($limit).", ".intval($maxlimit);
    $query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
    $rows_lista = mysql_num_rows($query);
    while ($field = mysql_fetch_array($query)) {
        $id = $field['CodPermiso'];
        ?>
        <tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
            <td align="center"><?=$field['CodPermiso']?></td>
            <td align="center"><?=$field['CodEmpleado']?></td>
            <td><?=htmlentities($field['NomCompleto'])?></td>
            <td><?=htmlentities($field['NomTipoPermiso'])?></td>
            <td><?=htmlentities($field['NomTipoFalta'])?></td>
            <td align="center"><?=formatFechaDMA($field['FechaIngreso'])?></td>
            <td align="center"><?=printValores("ESTADO-PERMISOS", $field['Estado'])?></td>
            <td><?=htmlentities($field['NomAprobador'])?></td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>
</div>
<table width="<?=$width?>">
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

<?php
if ($imprimir == "rh_permisos_pdf") {
    ?>
    <script type="text/javascript" language="javascript">
    $(document).ready(function() {
        abrirReporteVal('a_reporte', 'rh_permisos_pdf', 0, 0, $('#sel_registros'), 0, document.getElementById('frmentrada'));
    }); 
    </script>
    <?php
}
?>