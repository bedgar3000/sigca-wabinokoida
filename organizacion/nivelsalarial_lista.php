<?php
//	------------------------------------
if ($filtrar == "default") {
	$fEstado = 'A';
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "NomCategoriaCargo,Grado,Paso,Descripcion";
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (ns.Grado LIKE '%".$fBuscar."%'
					  OR ns.Paso LIKE '%".$fBuscar."%'
					  OR ns.Descripcion LIKE '%".$fBuscar."%'
					  OR md.Descripcion LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (ns.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fCategoriaCargo != "") { $cCategoriaCargo = "checked"; $filtro.=" AND (ns.CategoriaCargo = '".$fCategoriaCargo."')"; } else $dCategoriaCargo = "disabled";
//	------------------------------------
$_titulo = "Grados Salariales";
$_width = 700;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=nivelsalarial_lista" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />

<!--FILTRO-->
<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
	<tr>
		<td align="right" width="100">Categor&iacute;a:</td>
		<td>
            <input type="checkbox" <?=$cCategoriaCargo?> onclick="chkFiltro(this.checked, 'fCategoriaCargo');" />
            <select name="fCategoriaCargo" id="fCategoriaCargo" style="width:200px;" <?=$dCategoriaCargo?>>
                <option value="">&nbsp;</option>
                <?=getMiscelaneos($fCategoriaCargo, "CATCARGO", 0)?>
            </select>
		</td>
		<td align="right" width="100">Estado: </td>
		<td>
            <input type="checkbox" <?=$cEstado?> onclick="chkFiltro(this.checked, 'fEstado');" />
            <select name="fEstado" id="fEstado" style="width:125px;" <?=$dEstado?>>
                <option value="">&nbsp;</option>
                <?=loadSelectGeneral("ESTADO", $fEstado, 0)?>
            </select>
		</td>
        <td>&nbsp;</td>
	</tr>
	<tr>
		<td align="right">Buscar:</td>
		<td>
			<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
			<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:194px;" <?=$dBuscar?> />
		</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td align="right"><input type="submit" value="Buscar"></td>
	</tr>
</table>
</div>
<div class="sep"></div>

<!--REGISTROS-->
<center>
<input type="hidden" name="sel_registros" id="sel_registros" />
<table class="tblBotones" style="width:100%; min-width:<?=$_width?>px;">
    <tr>
        <td><div id="rows"></div></td>
        <td align="right">
            <input type="button" value="Nuevo" style="width:75px;" class="insert" onclick="cargarPagina(this.form, 'gehen.php?anz=nivelsalarial_form&opcion=nuevo');" />
            <input type="button" value="Modificar" style="width:75px;" class="update" onclick="cargarOpcion2(this.form, 'gehen.php?anz=nivelsalarial_form&opcion=modificar', 'SELF', '', $('#sel_registros').val());" />
            <input type="button" value="Eliminar" style="width:75px;" class="delete" onclick="opcionRegistro3(this.form, $('#sel_registros').val(), 'formulario', 'eliminar', 'nivelsalarial_ajax.php');" />
            <input type="button" value="Ver" style="width:75px;" class="ver" onclick="cargarOpcion2(this.form, 'gehen.php?anz=nivelsalarial_form&opcion=ver', 'SELF', '', $('#sel_registros').val());" />
        </td>
    </tr>
</table>

<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px;">
<table class="tblLista" style="width:100%; min-width:<?=$_width?>px;">
	<thead>
    <tr>
        <th width="200" onclick="order('NomCategoriaCargo,Grado,Paso,Descripcion')">Categoria</th>
        <th width="75" onclick="order('Grado,Paso,Descripcion')">Grado</th>
        <th width="75" onclick="order('Paso,Descripcion')">Paso</th>
        <th align="left" onclick="order('Descripcion')">Descripci&oacute;n</th>
        <th width="100" align="right" onclick="order('SueldoMinimo')">Sueldo M&iacute;nimo</th>
        <th width="100" align="right" onclick="order('SueldoMaximo')">Sueldo M&aacute;ximo</th>
        <th width="100" align="right" onclick="order('SueldoPromedio')">Sueldo Promedio</th>
        <th width="75" onclick="order('Estado')">Estado</th>
    </tr>
    </thead>
    
    <tbody id="lista_registros">
	<?php
	//	consulto todos
	$sql = "SELECT ns.*
			FROM
				rh_nivelsalarial ns
				LEFT JOIN mastmiscelaneosdet md ON (md.CodDetalle = ns.CategoriaCargo AND
													md.CodMaestro = 'CATCARGO' AND
													md.CodAplicacion = 'RH')
			WHERE 1 $filtro";
	$rows_total = getNumRows3($sql);
	//	consulto lista
	$sql = "SELECT
				ns.*,
				md.Descripcion AS NomCategoriaCargo
			FROM
				rh_nivelsalarial ns
				LEFT JOIN mastmiscelaneosdet md ON (md.CodDetalle = ns.CategoriaCargo AND
													md.CodMaestro = 'CATCARGO' AND
													md.CodAplicacion = 'RH')
			WHERE 1 $filtro
			ORDER BY $fOrderBy
			LIMIT ".intval($limit).", ".intval($maxlimit);
	$field = getRecords($sql);
	$rows_lista = count($field);
	foreach($field as $f) {
		$id = $f['CodNivel'];
		?>
		<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
			<td><?=$f['NomCategoriaCargo']?></td>
			<td align="center"><?=$f['Grado']?></td>
			<td align="center"><?=$f['Paso']?></td>
			<td><?=htmlentities($f['Descripcion'])?></td>
			<td align="right"><?=number_format($f['SueldoMinimo'],2,',','.')?></td>
			<td align="right"><?=number_format($f['SueldoMaximo'],2,',','.')?></td>
			<td align="right"><?=number_format($f['SueldoPromedio'],2,',','.')?></td>
			<td align="center"><?=printValoresGeneral('ESTADO',$f['Estado'])?></td>
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