<?php
if ($filtrar == "default") {
	$fEstado = "A";
	$fOrderBy = "Tipo, CodConcepto";
	$maxlimit = $_SESSION["MAXLIMIT"];
}
if ($fTipo != "") { $cTipo = "checked"; $filtro.=" AND (c.Tipo = '".$fTipo."')"; } else $dTipo = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (c.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (c.CodConcepto LIKE '%".$fBuscar."%' OR
					  c.Descripcion LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
//	------------------------------------
$_width = 900;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo">Maestro de Conceptos</td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=pr_conceptos_lista" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="sel_registros" id="sel_registros" />

<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
    <table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
        <tr>
    		<td align="right">Buscar:</td>
    		<td>
    			<input type="checkbox" <?=$cBuscar?> onclick="chkFiltro(this.checked, 'fBuscar');" />
    			<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:264px;" <?=$dBuscar?> />
    		</td>
    		<td align="right">Tipo: </td>
    		<td>
           		<input type="checkbox" <?=$cTipo?> onclick="chkFiltro(this.checked, 'fTipo');" />
                <select name="fTipo" id="fTipo" style="width:100px;" <?=$dTipo?>>
                    <option value=""></option>
                    <?=loadSelectValores("CONCEPTO-TIPO", $fTipo, 0)?>
                </select>
    		</td>
            <td>&nbsp;</td>
    	</tr>
        <tr>
        	<td colspan="2"></td>
    		<td align="right">Estado: </td>
    		<td>
           		<input type="checkbox" <?=$cEstado?> onclick="chkFiltro(this.checked, 'fEstado');" />
                <select name="fEstado" id="fEstado" style="width:100px;" <?=$dEstado?>>
                    <option value=""></option>
                    <?=loadSelectGeneral("ESTADO", $fEstado, 0)?>
                </select>
    		</td>
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
            <input type="button" id="btNuevo" value="Nuevo" style="width:75px; <?=$btNuevo?>" onclick="cargarPagina(this.form, 'gehen.php?anz=pr_conceptos_form&opcion=nuevo');" />
            
            <input type="button" id="btModificar" value="Modificar" style="width:75px; <?=$btModificar?>" onclick="cargarOpcionValidar2(this.form, $('#sel_registros').val(), 'accion=conceptos_modificar', 'gehen.php?anz=pr_conceptos_form&opcion=modificar', 'SELF', '');" />
            
            <input type="button" id="btEliminar" value="Eliminar" style="width:75px; <?=$btEliminar?>" onclick="opcionRegistro3(this.form, this.form.sel_registros.value, 'conceptos', 'eliminar', 'pr_conceptos_ajax.php');" />
            
            <input type="button" id="btVer" value="Ver" style="width:75px; <?=$btVer?>" onclick="cargarOpcion2(this.form, 'gehen.php?anz=pr_conceptos_form&opcion=ver', 'SELF', '', $('#sel_registros').val());" />
        </td>
    </tr>
</table>
<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px;">
    <table class="tblLista" style="width:100%; min-width:<?=$_width?>px;">
        <thead>
        <tr>
            <th scope="col" width="50" onclick="order('CodConcepto')"><a href="javascript:">C&oacute;digo</a></th>
            <th scope="col" align="left" onclick="order('Descripcion')"><a href="javascript:">Descripci&oacute;n</a></th>
            <th scope="col" width="75" onclick="order('Tipo, CodConcepto')"><a href="javascript:">Tipo</a></th>
            <th scope="col" width="25" onclick="order('FlagAutomatico')"><a href="javascript:">Aut.</a></th>
            <th scope="col" width="25" onclick="order('FlagRetencion')"><a href="javascript:">Ret.</a></th>
            <th scope="col" width="25" onclick="order('FlagBono')"><a href="javascript:">Bon.</a></th>
            <th scope="col" width="65" onclick="order('Estado')"><a href="javascript:">Estado</a></th>
        </tr>
        </thead>
        
        <tbody id="lista_registros">
        <?php
        //	consulto todos
        $sql = "SELECT c.CodConcepto
                FROM pr_concepto c
                WHERE 1 $filtro";
        $query = mysql_query($sql) or die ($sql.mysql_error());
        $rows_total = mysql_num_rows($query);
        
        //	consulto lista
        $sql = "SELECT
    				c.CodConcepto,
    				c.Descripcion,
    				c.Tipo,
    				c.Estado,
    				c.FlagAutomatico,
    				c.FlagBono,
    				c.FlagRetencion
                FROM pr_concepto c
                WHERE 1 $filtro
                ORDER BY $fOrderBy
                LIMIT ".intval($limit).", ".intval($maxlimit);
        $query = mysql_query($sql) or die ($sql.mysql_error());
        $rows_lista = mysql_num_rows($query);
        while ($field = mysql_fetch_array($query)) {
            $id = "$field[CodConcepto]";
            ?>
            <tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
                <td align="center"><?=$field['CodConcepto']?></td>
                <td><?=htmlentities($field['Descripcion'])?></td>
                <td align="center"><?=printValores("CONCEPTO-TIPO", $field['Tipo'])?></td>
                <td align="center"><?=printFlag($field['FlagAutomatico'])?></td>
                <td align="center"><?=printFlag($field['FlagRetencion'])?></td>
                <td align="center"><?=printFlag($field['FlagBono'])?></td>
                <td align="center"><?=printValoresGeneral("ESTADO", $field['Estado'])?></td>
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