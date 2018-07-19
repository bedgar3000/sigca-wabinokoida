<?php
if ($filtrar == "default") {
	$fEstado = "A";
	$fOrderBy = "TipoTransaccion, CodTipoTransaccion";
	$maxlimit = $_SESSION["MAXLIMIT"];
}
if ($fCodVoucher != "") { $cCodVoucher = "checked"; $filtro.=" AND (btt.CodVoucher = '".$fCodVoucher."')"; } else $dCodVoucher = "disabled";
if ($fTipoTransaccion != "") { $cTipoTransaccion = "checked"; $filtro.=" AND (btt.TipoTransaccion = '".$fTipoTransaccion."')"; } else $dTipoTransaccion = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (btt.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (btt.CodTipoTransaccion LIKE '%".$fBuscar."%' OR
					  btt.Descripcion LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
//	------------------------------------
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo">Maestro de Tipo de Transacciones Bancarias</td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=ap_tipo_transaccion_bancaria_lista" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="sel_registros" id="sel_registros" />

<div class="divBorder" style="width:900px;">
<table width="900" class="tblFiltro">
    <tr>
		<td align="right" width="100">Tipo de Voucher: </td>
		<td>
       		<input type="checkbox" <?=$cCodVoucher?> onclick="chkFiltro(this.checked, 'fCodVoucher');" />
            <select name="fCodVoucher" id="fCodVoucher" style="width:270px;" <?=$dCodVoucher?>>
                <option value=""></option>
                <?=loadSelect("ac_voucher", "CodVoucher", "Descripcion", $fCodVoucher, 0)?>
            </select>
		</td>
		<td align="right" width="100">Tipo: </td>
		<td>
       		<input type="checkbox" <?=$cTipoTransaccion?> onclick="chkFiltro(this.checked, 'fTipoTransaccion');" />
            <select name="fTipoTransaccion" id="fTipoTransaccion" style="width:100px;" <?=$dTipoTransaccion?>>
                <option value=""></option>
                <?=loadSelectValores("TIPO-TRANSACCION-BANCARIA", $fTipoTransaccion, 0)?>
            </select>
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
            <input type="button" id="btNuevo" value="Nuevo" style="width:75px; <?=$btNuevo?>" onclick="cargarPagina(this.form, 'gehen.php?anz=ap_tipo_transaccion_bancaria_form&opcion=nuevo');" />
            
            <input type="button" id="btModificar" value="Modificar" style="width:75px; <?=$btModificar?>" onclick="cargarOpcionValidar2(this.form, $('#sel_registros').val(), 'accion=tipo_transaccion_bancaria_modificar', 'gehen.php?anz=ap_tipo_transaccion_bancaria_form&opcion=modificar', 'SELF', '');" />
            
            <input type="button" id="btEliminar" value="Eliminar" style="width:75px; <?=$btEliminar?>" onclick="opcionRegistro2(this.form, this.form.sel_registros.value, 'tipo_transaccion_bancaria', 'eliminar');" />
            
            <input type="button" id="btVer" value="Ver" style="width:75px; <?=$btVer?>" onclick="cargarOpcion2(this.form, 'gehen.php?anz=ap_tipo_transaccion_bancaria_form&opcion=ver', 'SELF', '', $('#sel_registros').val());" />
        </td>
    </tr>
</table>
<div style="overflow:scroll; width:900px; height:350px;">
<table width="1000" class="tblLista">
    <thead>
    <tr>
        <th scope="col" width="60" onclick="order('CodTipoTransaccion')"><a href="javascript:">Tipo</a></th>
        <th scope="col" align="left" onclick="order('Descripcion')"><a href="javascript:">Descripci&oacute;n</a></th>
        <th scope="col" width="65" onclick="order('TipoTransaccion, CodTipoTransaccion')"><a href="javascript:">Tipo</a></th>
        <th scope="col" width="35" onclick="order('FlagVoucher')"><a href="javascript:">Gr. Vou.</a></th>
        <th scope="col" width="35" onclick="order('CodVoucher')"><a href="javascript:">Vou.</a></th>
        <th scope="col" width="125" onclick="order('CodCuenta')"><a href="javascript:">Cuenta</a></th>
        <th scope="col" width="125" onclick="order('CodCuentaPub20')"><a href="javascript:">Cuenta (Pub.20)</a></th>
        <th scope="col" width="65" onclick="order('Estado')"><a href="javascript:">Estado</a></th>
    </tr>
    </thead>
    
    <tbody id="lista_registros">
    <?php
    //	consulto todos
    $sql = "SELECT btt.CodTipoTransaccion
            FROM ap_bancotipotransaccion btt
            WHERE 1 $filtro";
    $query = mysql_query($sql) or die ($sql.mysql_error());
    $rows_total = mysql_num_rows($query);
    
    //	consulto lista
    $sql = "SELECT
				btt.CodTipoTransaccion,
				btt.Descripcion,
				btt.FlagVoucher,
				btt.CodVoucher,
				btt.CodCuenta,
				btt.CodCuentaPub20,
				btt.TipoTransaccion,
				btt.Estado
            FROM ap_bancotipotransaccion btt
            WHERE 1 $filtro
            ORDER BY $fOrderBy
            LIMIT ".intval($limit).", ".intval($maxlimit);
    $query = mysql_query($sql) or die ($sql.mysql_error());
    $rows_lista = mysql_num_rows($query);
    while ($field = mysql_fetch_array($query)) {
        $id = "$field[CodTipoTransaccion]";
        ?>
        <tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
            <td align="center"><?=$field['CodTipoTransaccion']?></td>
            <td><?=htmlentities($field['Descripcion'])?></td>
            <td align="center"><?=printValores("TIPO-TRANSACCION-BANCARIA", $field['TipoTransaccion'])?></td>
            <td align="center"><?=printFlag($field['FlagVoucher'])?></td>
            <td align="center"><?=$field['CodVoucher']?></td>
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