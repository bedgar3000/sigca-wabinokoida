<?php
//	------------------------------------
if ($filtrar == "default") {
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "CodConcepto";
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (cgv.CodConcepto LIKE '%".$fBuscar."%' OR
					  cgv.Descripcion LIKE '%".$fBuscar."%' OR
					  md1.Descripcion LIKE '%".$fBuscar."%' OR
					  md2.Descripcion LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (cgv.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fCategoria != "") { $cCategoria = "checked"; $filtro.=" AND (cgv.Categoria = '".$fCategoria."')"; } else $dCategoria = "disabled";
if ($fArticulo != "") { $cArticulo = "checked"; $filtro.=" AND (cgv.Articulo = '".$fArticulo."')"; } else $dArticulo = "disabled";
//	------------------------------------
$_width = 900;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo">Conceptos de Vi&aacute;ticos</td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=ap_viatico_concepto_lista" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />

<!--FILTRO-->
<div class="divBorder" style="width:<?=$_width?>px;">
<table width="<?=$_width?>" class="tblFiltro">
	<tr>
		<td align="right">Categor&iacute;as: </td>
		<td>
            <input type="checkbox" <?=$cCategoria?> onclick="chkFiltro(this.checked, 'fCategoria');" />
            <select name="fCategoria" id="fCategoria" style="width:200px;" <?=$dCategoria?>>
                <option value="">&nbsp;</option>
                <?=getMiscelaneos($fCategoria, "CATVIAT", 0)?>
            </select>
        </td>
		<td align="right">Buscar:</td>
		<td>
			<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
			<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:195px;" <?=$dBuscar?> />
		</td>
	</tr>
	<tr>
		<td align="right">Art&iacute;culos: </td>
		<td>
            <input type="checkbox" <?=$cArticulo?> onclick="chkFiltro(this.checked, 'fArticulo');" />
            <select name="fArticulo" id="fArticulo" style="width:200px;" <?=$dArticulo?>>
                <option value="">&nbsp;</option>
                <?=getMiscelaneos($fArticulo, "ARTVIAT", 0)?>
            </select>
        </td>
		<td align="right">Estado: </td>
		<td>
            <input type="checkbox" <?=$cEstado?> onclick="chkFiltro(this.checked, 'fEstado');" />
            <select name="fEstado" id="fEstado" style="width:100px;" <?=$dEstado?>>
                <option value="">&nbsp;</option>
                <?=loadSelectGeneral("ESTADO", $fEstado, 0)?>
            </select>
        </td>
	</tr>
</table>
</div>
<center><input type="submit" value="Buscar"></center><br />

<!--REGISTROS-->
<center>
<input type="hidden" name="sel_registros" id="sel_registros" />
<table width="<?=$_width?>" class="tblBotones">
    <tr>
        <td><div id="rows"></div></td>
        <td align="right">
            <input type="button" id="btNuevo" value="Nuevo" style="width:75px; <?=$btNuevo?>" onclick="cargarPagina(this.form, 'gehen.php?anz=ap_viatico_concepto_form&opcion=nuevo');" />
            
            <input type="button" id="btModificar" value="Modificar" style="width:75px; <?=$btModificar?>" onclick="cargarOpcion2(this.form, 'gehen.php?anz=ap_viatico_concepto_form&opcion=modificar', 'SELF', '', $('#sel_registros').val());" />
            
            <input type="button" id="btEliminar" value="Eliminar" style="width:75px; <?=$btEliminar?>" onclick="opcionRegistro2(this.form, $('#sel_registros').val(), 'viatico_concepto', 'eliminar');" />
            
            <input type="button" id="btVer" value="Ver" style="width:75px; <?=$btVer?>" onclick="cargarOpcion2(this.form, 'gehen.php?anz=ap_viatico_concepto_form&opcion=ver', 'SELF', '', $('#sel_registros').val());" />
        </td>
    </tr>
</table>

<div style="overflow:scroll; width:<?=$_width?>px; height:300px;">
<table width="1200" class="tblLista">
	<thead>
    <tr>
        <th scope="col" width="50" onclick="order('CodConcepto')">Concepto</th>
        <th scope="col" align="left" onclick="order('Descripcion')">Descripci&oacute;n</th>
        <th scope="col" width="100" onclick="order('NomArticulo')">Art&iacute;culo</th>
        <th scope="col" width="75" onclick="order('Numeral')">Numeral</th>
        <th scope="col" width="150" onclick="order('NomCategoria')">Categor&iacute;a</th>
        <th scope="col" width="75" onclick="order('ValorUT')">Valor UT</th>
        <th scope="col" width="35" onclick="order('FlagMonto')">Mon.</th>
        <th scope="col" width="75" onclick="order('Estado')">Estado</th>
    </tr>
    </thead>
    
    <tbody id="lista_registros">
	<?php
	//	consulto todos
	$sql = "SELECT cgv.CodConcepto
			FROM
				ap_conceptogastoviatico cgv
				LEFT JOIN mastmiscelaneosdet md1 ON (md1.CodDetalle = cgv.Articulo AND
													 md1.CodMaestro = 'ARTVIAT' AND
													 md1.CodAplicacion = 'AP')
				LEFT JOIN mastmiscelaneosdet md2 ON (md2.CodDetalle = cgv.Categoria AND
													 md2.CodMaestro = 'CATVIAT' AND
													 md2.CodAplicacion = 'AP')
			WHERE 1 $filtro";
	$rows_total = getNumRows3($sql);
	
	//	consulto lista
	$sql = "SELECT
				cgv.CodConcepto,
				cgv.Descripcion,
				cgv.Articulo,
				cgv.Numeral,
				cgv.Categoria,
				cgv.ValorUT,
				cgv.FlagMonto,
				cgv.Estado,
				md1.Descripcion AS NomArticulo,
				md2.Descripcion AS NomCategoria
			FROM
				ap_conceptogastoviatico cgv
				LEFT JOIN mastmiscelaneosdet md1 ON (md1.CodDetalle = cgv.Articulo AND
													 md1.CodMaestro = 'ARTVIAT' AND
													 md1.CodAplicacion = 'AP')
				LEFT JOIN mastmiscelaneosdet md2 ON (md2.CodDetalle = cgv.Categoria AND
													 md2.CodMaestro = 'CATVIAT' AND
													 md2.CodAplicacion = 'AP')
			WHERE 1 $filtro
			ORDER BY $fOrderBy
			LIMIT ".intval($limit).", ".intval($maxlimit);
	$field = getRecords($sql);
	$rows_lista = count($field);
	foreach($field as $f) {
		$id = "$f[CodConcepto]";
		?>
		<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
			<td align="center"><?=$f['CodConcepto']?></td>
			<td><?=htmlentities($f['Descripcion'])?></td>
			<td align="center"><?=$f['NomArticulo']?></td>
			<td align="center"><?=$f['Numeral']?></td>
			<td align="center"><?=$f['NomCategoria']?></td>
			<td align="center"><?=number_format($f['ValorUT'], 2, ',', '.')?></td>
			<td align="center"><?=printFlag($f['FlagMonto'])?></td>
			<td align="center"><?=printValoresGeneral("ESTADO", $f['Estado'])?></td>
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