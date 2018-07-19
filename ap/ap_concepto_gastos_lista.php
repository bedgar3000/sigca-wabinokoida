<?php
if ($filtrar == "default") {
	$fEstado = "A";
	$fOrderBy = "NomGastoGrupo, CodConceptoGasto";
	$maxlimit = $_SESSION["MAXLIMIT"];
}
if ($fCodGastoGrupo != "") { $cCodGastoGrupo = "checked"; $filtro.=" AND (cg.CodGastoGrupo = '".$fCodGastoGrupo."')"; } else $dCodGastoGrupo = "disabled";
if ($fCodPartida != "") { $cCodPartida = "checked"; $filtro.=" AND (cg.CodPartida = '".$fCodPartida."')"; } else $dCodPartida = "visibility:hidden;";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (cg.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (cg.CodConceptoGasto LIKE '%".$fBuscar."%' OR
					  cg.Descripcion LIKE '%".$fBuscar."%' OR
					  cg.CodPartida LIKE '%".$fBuscar."%' OR
					  cg.CodCuenta LIKE '%".$fBuscar."%' OR
					  cg.CodCuentaPub20 LIKE '%".$fBuscar."%' OR
					  cgg.Descripcion LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
//	------------------------------------
if ($_PARAMETRO['CONTPUB20'] == "S") $display_cuenta = "display:none;"; else $display_cuenta20 = "display:none;";
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo">Maestro de Concepto de Gastos</td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=ap_concepto_gastos_lista" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="sel_registros" id="sel_registros" />

<div class="divBorder" style="width:900px;">
<table width="900" class="tblFiltro">
    <tr>
		<td align="right" width="100">Grupo de Gasto: </td>
		<td>
       		<input type="checkbox" <?=$cCodGastoGrupo?> onclick="chkFiltro(this.checked, 'fCodGastoGrupo');" />
            <select name="fCodGastoGrupo" id="fCodGastoGrupo" style="width:270px;" <?=$dCodGastoGrupo?>>
                <option value=""></option>
                <?=loadSelect("ap_conceptogastogrupo", "CodGastoGrupo", "Descripcion", $fCodGastoGrupo, 0)?>
            </select>
		</td>
		<td align="right" width="100">Partida: </td>
		<td class="gallery clearfix">
            <input type="checkbox" <?=$cCodPartida?> onclick="chkListado(this.checked, 'aCodPartida', 'fCodPartida');" />            
            <input type="text" name="fCodPartida" id="fCodPartida" style="width:95px;" class="disabled" value="<?=$fCodPartida?>" readonly="readonly" />
            <a id="aCodPartida" href="../lib/listas/listado_clasificador_presupuestario.php?filtrar=default&cod=fCodPartida&iframe=true&width=950&height=525" rel="prettyPhoto[iframe1]" style=" <?=$dCodPartida?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
	</tr>
    <tr>
		<td align="right">Buscar:</td>
		<td>
			<input type="checkbox" <?=$cBuscar?> onclick="chkFiltro(this.checked, 'fBuscar');" />
			<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:264px;" <?=$dBuscar?> />
		</td>
		<td align="right">Estado: </td>
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
<table width="900" class="tblBotones">
    <tr>
        <td><div id="rows"></div></td>
        <td align="right">
            <input type="button" id="btNuevo" value="Nuevo" style="width:75px; <?=$btNuevo?>" onclick="cargarPagina(this.form, 'gehen.php?anz=ap_concepto_gastos_form&opcion=nuevo');" />
            
            <input type="button" id="btModificar" value="Modificar" style="width:75px; <?=$btModificar?>" onclick="cargarOpcion2(this.form, 'gehen.php?anz=ap_concepto_gastos_form&opcion=modificar', 'SELF', '', $('#sel_registros').val());" />
            
            <input type="button" id="btEliminar" value="Eliminar" style="width:75px; <?=$btEliminar?>" onclick="opcionRegistro2(this.form, this.form.sel_registros.value, 'concepto_gastos', 'eliminar');" />
            
            <input type="button" id="btVer" value="Ver" style="width:75px; <?=$btVer?>" onclick="cargarOpcion2(this.form, 'gehen.php?anz=ap_concepto_gastos_form&opcion=ver', 'SELF', '', $('#sel_registros').val());" />
        </td>
    </tr>
</table>
<div style="overflow:scroll; width:900px; height:350px;">
<table width="1400" class="tblLista">
    <thead>
    <tr>
        <th scope="col" width="50" onclick="order('CodConceptoGasto')"><a href="javascript:">Cod.</a></th>
        <th scope="col" align="left" onclick="order('Descripcion')"><a href="javascript:">Descripci&oacute;n</a></th>
        <th scope="col" width="400" align="left" onclick="order('NomGastoGrupo, CodConceptoGasto')"><a href="javascript:">Grupo de Gasto</a></th>
        <th scope="col" width="85" onclick="order('CodPartida')"><a href="javascript:">Partida</a></th>
        <th scope="col" width="125" onclick="order('CodCuenta')"><a href="javascript:">Cuenta</a></th>
        <th scope="col" width="125" onclick="order('CodCuentaPub20')"><a href="javascript:">Cta. Pub. 20</a></th>
        <th scope="col" width="65" onclick="order('Estado')"><a href="javascript:">Estado</a></th>
    </tr>
    </thead>
    
    <tbody id="lista_registros">
    <?php
    //	consulto todos
    $sql = "SELECT cg.CodConceptoGasto
            FROM
				ap_conceptogastos cg
				INNER JOIN ap_conceptogastogrupo cgg ON (cgg.CodGastoGrupo = cg.CodGastoGrupo)
            WHERE 1 $filtro";
    $query = mysql_query($sql) or die ($sql.mysql_error());
    $rows_total = mysql_num_rows($query);
    
    //	consulto lista
    $sql = "SELECT
				cg.CodConceptoGasto,
				cg.Descripcion,
				cg.Estado,
				cg.CodPartida,
				cg.CodCuenta,
				cg.CodCuentaPub20,
				cgg.Descripcion AS NomGastoGrupo
            FROM
				ap_conceptogastos cg
				INNER JOIN ap_conceptogastogrupo cgg ON (cgg.CodGastoGrupo = cg.CodGastoGrupo)
            WHERE 1 $filtro
            ORDER BY $fOrderBy
            LIMIT ".intval($limit).", ".intval($maxlimit);
    $query = mysql_query($sql) or die ($sql.mysql_error());
    $rows_lista = mysql_num_rows($query);
    while ($field = mysql_fetch_array($query)) {
        $id = "$field[CodConceptoGasto]";
        ?>
        <tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
            <td align="center"><?=$field['CodConceptoGasto']?></td>
            <td><?=htmlentities($field['Descripcion'])?></td>
            <td><?=htmlentities($field['NomGastoGrupo'])?></td>
            <td align="center"><?=$field['CodPartida']?></td>
            <td align="center"><?=$field['CodCuenta']?></td>
            <td align="center"><?=$field['CodCuentaPub20']?></td>
            <td align="center"><?=printValoresGeneral("ESTADO", $field['Estado'])?></td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>
</div>
<table width="900">
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