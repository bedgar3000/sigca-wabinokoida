<?php
if ($lista == "todos") {
	$_titulo = "Listar Cese/Reingreso";
	$btConformar = "display:none;";
	$btAprobar = "display:none;";
}
elseif ($lista == "conformar") {
	$fEstado = "PE";
	$_titulo = "Conformar Cese/Reingreso";
	$btNuevo = "display:none;";
	$btModificar = "display:none;";
	$btAprobar = "display:none;";
}
elseif ($lista == "aprobar") {
	$fEstado = "CN";
	$_titulo = "Aprobar Cese/Reingreso";
	$btNuevo = "display:none;";
	$btModificar = "display:none;";
	$btConformar = "display:none;";
}
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["ORGANISMO_ACTUAL"];
	$fOrderBy = "Tipo, CodProceso";
	$maxlimit = $_SESSION["MAXLIMIT"];
	if ($lista == "todos") $fEstado = "PE";
}
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (pc.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (pc.Tipo LIKE '%".$fBuscar."%' OR
					  pc.CodProceso LIKE '%".$fBuscar."%' OR
					  pc.Fecha LIKE '%".$fBuscar."%' OR
					  e.CodEmpleado LIKE '%".$fBuscar."%' OR
					  p.NomCompleto LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (pc.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fCodDependencia != "") { $cCodDependencia = "checked"; $filtro.=" AND (pc.CodDependencia = '".$fCodDependencia."')"; } else $dCodDependencia = "disabled";
if ($fCodTipoNom != "") { $cCodTipoNom = "checked"; $filtro.=" AND (pc.CodTipoNom = '".$fCodTipoNom."')"; } else $dCodTipoNom = "disabled";
if ($fCodPersona != "") { $cCodPersona = "checked"; $filtro.=" AND (pc.CodPersona = '".$fCodPersona."')"; } else $dCodPersona = "visibility:hidden;";
if ($fFechaD != "" || $fFechaH != "") {
	$cFecha = "checked";
	if ($fFechaD != "") $filtro.=" AND (pc.Fecha >= '".$fFechaD."')";
	if ($fFechaH != "") $filtro.=" AND (pc.Fecha <= '".$fFechaH."')";
} else $dFecha = "disabled";
//	------------------------------------
$_width = 900;
$_sufijo = "reingreso";
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=rh_<?=$_sufijo?>_lista" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="sel_registros" id="sel_registros" />

<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
    <table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
    	<tr>
    		<td align="right">Organismo:</td>
    		<td>
    			<input type="checkbox" <?=$cCodOrganismo?> onclick="this.checked=!this.checked" />
    			<select name="fCodOrganismo" id="fCodOrganismo" style="width:275px;" <?=$dCodOrganismo?> onChange="getOptionsSelect(this.value, 'dependencia_filtro', 'fCodDependencia', true, 'fCodCentroCosto');">
    				<?=getOrganismos($fCodOrganismo, 3)?>
    			</select>
    		</td>
            <td align="right">Empleado:</td>
            <td class="gallery clearfix">
                <input type="checkbox" <?=$cCodPersona?> onclick="chkFiltroLista_3(this.checked, 'fCodEmpleado', 'fNomEmpleado', 'fCodPersona', 'btEmpleado');" />
                <input type="hidden" name="fCodPersona" id="fCodPersona" value="<?=$fCodPersona?>" />
                <input type="hidden" name="fCodEmpleado" id="fCodEmpleado" value="<?=$fCodEmpleado?>" />
                <input type="text" name="fNomEmpleado" id="fNomEmpleado" style="width:250px;" class="disabled" value="<?=$fNomEmpleado?>" readonly />
                <a href="../lib/listas/listado_empleados.php?filtrar=default&cod=fCodEmpleado&nom=fNomEmpleado&campo3=fCodPersona&iframe=true&width=950&height=525" rel="prettyPhoto[iframe1]" id="btEmpleado" style=" <?=$dCodPersona?>">
                    <img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
                </a>
            </td>
            <td align="right" width="125">Fecha: </td>
            <td>
                <input type="checkbox" <?=$cFecha?> onclick="chkFiltro_2(this.checked, 'fFechaD', 'fFechaH');" />
                <input type="text" name="fFechaD" id="fFechaD" value="<?=$fFechaD?>" style="width:60px;" maxlength="10" class="datepicker" onkeyup="setFecha(this);" <?=$dFecha?> />
                
                <input type="text" name="fFechaH" id="fFechaH" value="<?=$fFechaH?>" style="width:60px;" maxlength="10" class="datepicker" onkeyup="setFecha(this);" <?=$dFecha?> />
            </td>
            <td>&nbsp;</td>
    	</tr>
    	<tr>
    		<td align="right">Dependencia:</td>
    		<td>
    			<input type="checkbox" <?=$cCodDependencia?> onclick="chkFiltro(this.checked, 'fCodDependencia');" />
    			<select name="fCodDependencia" id="fCodDependencia" style="width:275px;" onChange="getOptionsSelect(this.value, 'centro_costo', 'fCodCentroCosto', true);" <?=$dCodDependencia?>>
                	<option value="">&nbsp;</option>
    				<?=getDependencias($fCodDependencia, $fCodOrganismo, 3)?>
    			</select>
    		</td>
            <td align="right">Buscar:</td>
            <td>
                <input type="checkbox" <?=$cBuscar?> onclick="chkFiltro(this.checked, 'fBuscar');" />
                <input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:250px;" <?=$dBuscar?> />
            </td>
            <td align="right">Estado: </td>
            <td>
                <?php
                if ($lista == "todos") {
                    ?>
                    <input type="checkbox" <?=$cEstado?> onclick="chkFiltro(this.checked, 'fEstado');" />
                    <select name="fEstado" id="fEstado" style="width:123px;" <?=$dEstado?>>
                        <option value=""></option>
                        <?=loadSelectValores("ESTADO-REINGRESO", $fEstado, 0)?>
                    </select>
                    <?php
                }
                else {
                    ?>
                    <input type="checkbox" <?=$cEstado?> onclick="this.checked=!this.checked;" />
                    <select name="fEstado" id="fEstado" style="width:123px;" <?=$dEstado?>>
                        <?=loadSelectValores("ESTADO-REINGRESO", $fEstado, 1)?>
                    </select>
                    <?php
                }
                ?>
            </td>
            <td>&nbsp;</td>
    	</tr>
        <tr>
            <td align="right">N&oacute;mina:</td>
            <td>
                <input type="checkbox" <?=$cCodTipoNom?> onclick="chkFiltro(this.checked, 'fCodTipoNom');" />
                <select name="fCodTipoNom" id="fCodTipoNom" style="width:275px;" <?=$dCodTipoNom?>>
                    <option value="">&nbsp;</option>
                    <?=loadSelect2('tiponomina','CodTipoNom','Nomina',$fCodTipoNom)?>
                </select>
            </td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td width="25" align="right"><input type="submit" value="Buscar"></td>
    	</tr>
    </table>
</div>
<div class="sep"></div>

<center>
<table class="tblBotones" style="width:100%; min-width:<?=$_width?>px;">
    <tr>
        <td><div id="rows"></div></td>
        <td align="right">
            <input type="button" id="btNuevo" value="Nuevo" style="width:75px; <?=$btNuevo?>" onclick="cargarPagina(this.form, 'gehen.php?anz=rh_<?=$_sufijo?>_form&opcion=nuevo&action=rh_<?=$_sufijo?>_lista');" />
            <input type="button" id="btModificar" value="Modificar" style="width:75px; <?=$btModificar?>" onclick="cargarOpcionValidar2(this.form, $('#sel_registros').val(), 'accion=<?=$_sufijo?>_modificar', 'gehen.php?anz=rh_<?=$_sufijo?>_form&opcion=modificar&action=rh_<?=$_sufijo?>_lista', 'SELF', '');" />
            <input type="button" id="btConformar" value="Conformar" style="width:75px; <?=$btConformar?>" onclick="cargarOpcionValidar2(this.form, $('#sel_registros').val(), 'accion=<?=$_sufijo?>_conformar', 'gehen.php?anz=rh_<?=$_sufijo?>_form&opcion=conformar&action=rh_<?=$_sufijo?>_lista', 'SELF', '');" />
            <input type="button" id="btAprobar" value="Aprobar" style="width:75px; <?=$btAprobar?>" onclick="cargarOpcionValidar2(this.form, $('#sel_registros').val(), 'accion=<?=$_sufijo?>_aprobar', 'gehen.php?anz=rh_<?=$_sufijo?>_form&opcion=aprobar&action=rh_<?=$_sufijo?>_lista', 'SELF', '');" />
            <input type="button" id="btAnular" value="Anular" style="width:75px; <?=$btAnular?>" onclick="cargarOpcionValidar2(this.form, $('#sel_registros').val(), 'accion=<?=$_sufijo?>_anular', 'gehen.php?anz=rh_<?=$_sufijo?>_form&opcion=anular&action=rh_<?=$_sufijo?>_lista', 'SELF', '');" />
            <input type="button" id="btVer" value="Ver" style="width:75px; <?=$btVer?>" onclick="cargarOpcion2(this.form, 'gehen.php?anz=rh_<?=$_sufijo?>_form&opcion=ver&action=rh_<?=$_sufijo?>_lista', 'SELF', '', $('#sel_registros').val());" />
        </td>
    </tr>
</table>
<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px;">
    <table class="tblLista" style="width:100%; min-width:<?=$_width?>px;">
        <thead>
            <tr>
                <th width="15">#</th>
                <th width="75" onclick="order('CodProceso')">Nro. Proceso</th>
                <th width="50" onclick="order('CodEmpleado')">Empleado</th>
                <th align="left" onclick="order('NomCompleto')">Nombre Completo</th>
                <th width="75" onclick="order('Tipo, CodProceso')">Tipo</th>
                <th width="75" onclick="order('Fecha')">Fecha</th>
                <th width="75" onclick="order('Estado')">Estado</th>
            </tr>
        </thead>
        
        <tbody id="lista_registros">
        <?php
        //	consulto todos
        $sql = "SELECT
    				pc.Tipo,
    				pc.CodProceso
                FROM
    				rh_procesocesereing pc
    				INNER JOIN mastpersonas p ON (p.CodPersona = pc.CodPersona)
    				INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
                WHERE 1 $filtro";
        $query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
        $rows_total = mysql_num_rows($query);
        
        //	consulto lista
        $sql = "SELECT
    				pc.Tipo,
    				pc.CodProceso,
    				pc.Fecha,
    				pc.Estado,
    				e.CodEmpleado,
    				p.NomCompleto
                FROM
    				rh_procesocesereing pc
    				INNER JOIN mastpersonas p ON (p.CodPersona = pc.CodPersona)
    				INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
                WHERE 1 $filtro
                ORDER BY $fOrderBy
                LIMIT ".intval($limit).", ".intval($maxlimit);
        $query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
        $rows_lista = mysql_num_rows($query);	$i=0;
        while ($field = mysql_fetch_array($query)) {
            $id = "$field[Tipo]_$field[CodProceso]";
            ?>
            <tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
                <th><?=++$i?></th>
                <td align="center"><?=$field['CodProceso']?></td>
                <td align="center"><?=$field['CodEmpleado']?></td>
                <td><?=htmlentities($field['NomCompleto'])?></td>
                <td align="center"><?=printValores("TIPO-REINGRESO", $field['Tipo'])?></td>
                <td align="center"><?=formatFechaDMA($field['Fecha'])?></td>
                <td align="center"><?=printValores("ESTADO-REINGRESO", $field['Estado'])?></td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
</div>
<table style="width:100%; min-width:<?=$_width?>px;">
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