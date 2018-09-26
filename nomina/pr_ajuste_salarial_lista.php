<?php
$FechaActual = "$AnioActual-$MesActual-$DiaActual";
//	------------------------------------
if ($lista == "todos") {
	$_titulo = "Ajuste Salarial (Grado Salarial)";
	$btAprobar = "display:none;";
}
elseif ($lista == "aprobar") {
	$_titulo = "Aprobar Ajuste Salarial (Grado Salarial)";
	$btNuevo = "display:none;";
	$btModificar = "display:none;";
}
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["ORGANISMO_ACTUAL"];
	if ($lista == "todos") $fAnio = "$AnioActual";
	elseif ($lista == "aprobar") $fEstado = "PE";
	$fOrderBy = "CodOrganismo";
	$maxlimit = $_SESSION["MAXLIMIT"];
}
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (Periodo LIKE '%".$fBuscar."%' OR
					  Descripcion LIKE '%".$fBuscar."%' OR
					  NroGaceta LIKE '%".$fBuscar."%' OR
					  NroResolucion LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fAnio != "") { $cAnio = "checked"; $filtro.=" AND (SUBSTRING(Periodo, 1, 4) = '".$fAnio."')"; } else $dAnio = "disabled";
//	------------------------------------
$_width = 900;
$_sufijo = "ajuste_salarial";
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
			<select name="fCodOrganismo" id="fCodOrganismo" style="width:275px;" <?=$dCodOrganismo?>>
				<?=getOrganismos($fCodOrganismo, 3)?>
			</select>
		</td>
		<td align="right">A&ntilde;o:</td>
		<td>
			<input type="checkbox" <?=$cAnio?> onclick="chkFiltro(this.checked, 'fAnio');" />
			<input type="text" name="fAnio" id="fAnio" value="<?=$fAnio?>" style="width:35px;" maxlength="4" <?=$dAnio?> />
		</td>
	</tr>
    <tr>
		<td align="right">Buscar:</td>
		<td>
			<input type="checkbox" <?=$cBuscar?> onclick="chkFiltro(this.checked, 'fBuscar');" />
			<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:270px;" <?=$dBuscar?> />
		</td>
		<td align="right">Estado: </td>
		<td>
        	<?php
			if ($lista == "aprobar") {
				?>
                <input type="checkbox" <?=$cEstado?> onclick="this.checked=!this.checked;" />
                <select name="fEstado" id="fEstado" style="width:100px;" <?=$dEstado?>>
                    <?=loadSelectValores("ESTADO-AJUSTE", $fEstado, 1)?>
                </select>
                <?php
			} else {
				?>
                <input type="checkbox" <?=$cEstado?> onclick="chkFiltro(this.checked, 'fEstado');" />
                <select name="fEstado" id="fEstado" style="width:100px;" <?=$dEstado?>>
                    <option value=""></option>
                    <?=loadSelectValores("ESTADO-AJUSTE", $fEstado, 0)?>
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
<table width="<?=$_width?>" class="tblBotones">
    <tr>
        <td><div id="rows"></div></td>
        <td align="right">
            <input type="button" id="btNuevo" value="Nuevo" style="width:75px; <?=$btNuevo?>" onclick="cargarPagina(this.form, 'gehen.php?anz=pr_<?=$_sufijo?>_form&opcion=nuevo&action=pr_<?=$_sufijo?>_lista');" />
            
            <input type="button" id="btModificar" value="Modificar" style="width:75px; <?=$btModificar?>" onclick="cargarOpcionValidar2(this.form, $('#sel_registros').val(), 'accion=<?=$_sufijo?>_modificar', 'gehen.php?anz=pr_<?=$_sufijo?>_form&opcion=modificar&action=pr_<?=$_sufijo?>_lista', 'SELF', '');" />
            
            <input type="button" id="btVer" value="Ver" style="width:75px; <?=$btVer?>" onclick="cargarOpcion2(this.form, 'gehen.php?anz=pr_<?=$_sufijo?>_form&opcion=ver&action=pr_<?=$_sufijo?>_lista', 'SELF', '', $('#sel_registros').val());" /> | 
            
            <input type="button" id="btAprobar" value="Aprobar" style="width:75px; <?=$btAprobar?>" onclick="cargarOpcionValidar2(this.form, $('#sel_registros').val(), 'accion=<?=$_sufijo?>_aprobar', 'gehen.php?anz=pr_<?=$_sufijo?>_form&opcion=aprobar&action=pr_<?=$_sufijo?>_lista', 'SELF', '');" />
            
            <input type="button" id="btAnular" value="Anular" style="width:75px; <?=$btAnular?>" onclick="cargarOpcionValidar2(this.form, $('#sel_registros').val(), 'accion=<?=$_sufijo?>_anular', 'gehen.php?anz=pr_<?=$_sufijo?>_form&opcion=anular&origen=pr_<?=$_sufijo?>_lista', 'SELF', '');" />
        </td>
    </tr>
</table>
<div style="overflow:scroll; width:<?=$_width?>px; height:350px;">
<table width="100%" class="tblLista">
    <thead>
    <tr>
        <th scope="col" width="15"></th>
        <th scope="col" width="60" onclick="order('Periodo')">Per&iacute;odo</th>
        <th scope="col" align="left" onclick="order('Descripcion')">Descripci&oacute;n</th>
        <th scope="col" width="100" onclick="order('NroResolucion')">Nro. Resoluci&oacute;n</th>
        <th scope="col" width="100" onclick="order('NroGaceta')">Nro. Gaceta</th>
        <th scope="col" width="75" onclick="order('Estado')">Estado</th>
    </tr>
    </thead>
    
    <tbody id="lista_registros">
    <?php
    //	consulto todos
    $sql = "SELECT
				CodOrganismo,
				Periodo,
				Secuencia
            FROM pr_ajustesalarial
            WHERE 1 $filtro";
    $query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
    $rows_total = mysql_num_rows($query);
    
    //	consulto lista
    $sql = "SELECT
				CodOrganismo,
				Periodo,
				Secuencia,
				Descripcion,
				NroResolucion,
				NroGaceta,
				Estado
            FROM pr_ajustesalarial
            WHERE 1 $filtro
            ORDER BY $fOrderBy
            LIMIT ".intval($limit).", ".intval($maxlimit);
    $query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
    $rows_lista = mysql_num_rows($query);	$i=0;
    while ($field = mysql_fetch_array($query)) {
        $id = "$field[CodOrganismo]_$field[Periodo]_$field[Secuencia]";
        ?>
        <tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
            <th><?=++$i?></th>
            <td align="center"><?=$field['Periodo']?></td>
            <td><?=htmlentities($field['Descripcion'])?></td>
            <td align="center"><?=$field['NroResolucion']?></td>
            <td align="center"><?=$field['NroGaceta']?></td>
            <td align="center"><?=printValores("ESTADO-AJUSTE", $field['Estado'])?></td>
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