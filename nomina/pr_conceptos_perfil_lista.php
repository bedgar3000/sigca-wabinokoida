<?php
if ($filtrar == "default") {
	$fEstado = "A";
	$fOrderBy = "CodPerfilConcepto";
	$maxlimit = $_SESSION["MAXLIMIT"];
}
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (cp.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (cp.CodPerfilConcepto LIKE '%".$fBuscar."%' OR
					  cp.Descripcion LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
//	------------------------------------
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo">Maestro de Perfil de Conceptos</td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=pr_conceptos_perfil_lista" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="sel_registros" id="sel_registros" />

<div class="divBorder" style="width:700px;">
<table width="700" class="tblFiltro">
    <tr>
		<td align="right" width="100">Buscar:</td>
		<td>
			<input type="checkbox" <?=$cBuscar?> onclick="chkFiltro(this.checked, 'fBuscar');" />
			<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:80%;" <?=$dBuscar?> />
		</td>
		<td align="right" width="100">Estado: </td>
		<td>
       		<input type="checkbox" <?=$cEstado?> onclick="chkFiltro(this.checked, 'fEstado');" />
            <select name="fEstado" id="fEstado" style="width:100px;" <?=$dEstado?>>
                <option value=""></option>
                <?=loadSelectGeneral("ESTADO", $fEstado, 0)?>
            </select>
		</td>
	</tr>
</table>
</div>
<center><input type="submit" value="Buscar"></center><br />

<center>
<table width="700" class="tblBotones">
    <tr>
        <td><div id="rows"></div></td>
        <td align="right">
            <input type="button" id="btNuevo" value="Nuevo" style="width:75px; <?=$btNuevo?>" onclick="cargarPagina(this.form, 'gehen.php?anz=pr_conceptos_perfil_form&opcion=nuevo');" />
            
            <input type="button" id="btModificar" value="Modificar" style="width:75px; <?=$btModificar?>" onclick="cargarOpcion2(this.form, 'gehen.php?anz=pr_conceptos_perfil_form&opcion=modificar', 'SELF', '', $('#sel_registros').val());" />
            
            <input type="button" id="btEliminar" value="Eliminar" style="width:75px; <?=$btEliminar?>" onclick="opcionRegistro2(this.form, this.form.sel_registros.value, 'conceptos_perfil', 'eliminar');" />
            
            <input type="button" id="btVer" value="Ver" style="width:75px; <?=$btVer?>" onclick="cargarOpcion2(this.form, 'gehen.php?anz=pr_conceptos_perfil_form&opcion=ver', 'SELF', '', $('#sel_registros').val());" /> |
            
            <input type="button" id="btPerfil" value="Detalle de Conceptos Vs. Cuenta" style=" <?=$btVer?>" onclick="cargarOpcion2(this.form, 'gehen.php?anz=pr_conceptos_perfil_detalle', 'SELF', '', $('#sel_registros').val());" />
        </td>
    </tr>
</table>
<div style="overflow:scroll; width:700px; height:350px;">
<table width="100%" class="tblLista">
    <thead>
    <tr>
        <th scope="col" width="50" onclick="order('CodConcepto')"><a href="javascript:">C&oacute;digo</a></th>
        <th scope="col" align="left" onclick="order('Descripcion')"><a href="javascript:">Descripci&oacute;n</a></th>
        <th scope="col" width="65" onclick="order('Estado')"><a href="javascript:">Estado</a></th>
    </tr>
    </thead>
    
    <tbody id="lista_registros">
    <?php
    //	consulto todos
    $sql = "SELECT cp.CodPerfilConcepto
            FROM pr_conceptoperfil cp
            WHERE 1 $filtro";
    $query = mysql_query($sql) or die ($sql.mysql_error());
    $rows_total = mysql_num_rows($query);
    
    //	consulto lista
    $sql = "SELECT
				cp.CodPerfilConcepto,
				cp.Descripcion,
				cp.Estado
            FROM pr_conceptoperfil cp
            WHERE 1 $filtro
            ORDER BY $fOrderBy
            LIMIT ".intval($limit).", ".intval($maxlimit);
    $query = mysql_query($sql) or die ($sql.mysql_error());
    $rows_lista = mysql_num_rows($query);
    while ($field = mysql_fetch_array($query)) {
        $id = "$field[CodPerfilConcepto]";
        ?>
        <tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
            <td align="center"><?=$field['CodPerfilConcepto']?></td>
            <td><?=htmlentities($field['Descripcion'])?></td>
            <td align="center"><?=printValoresGeneral("ESTADO", $field['Estado'])?></td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>
</div>
<table width="700">
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