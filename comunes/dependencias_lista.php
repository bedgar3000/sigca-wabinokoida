<?php
//	------------------------------------
if ($filtrar == "default") {
	$fEstado = "A";
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "CodDependencia";
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (d.CodDependencia LIKE '%".$fBuscar."%' OR
					  d.Dependencia LIKE '%".$fBuscar."%' OR
					  o.Organismo LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (d.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (d.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
//	------------------------------------
$_titulo = "Maestro de Dependencias";
$_width = 900;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=dependencias_lista" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />

<!--FILTRO-->
<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
	<tr>
		<td align="right" width="100">Organismo:</td>
		<td>
            <input type="checkbox" <?=$cCodOrganismo?> onclick="chkFiltro(this.checked, 'fCodOrganismo');" />
            <select name="fCodOrganismo" id="fCodOrganismo" style="width:256px;" <?=$dCodOrganismo?>>
                <option value="">&nbsp;</option>
                <?=loadSelect2("mastorganismos","CodOrganismo","Organismo",$fCodOrganismo)?>
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
		<td colspan="3">
			<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
			<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:250px;" <?=$dBuscar?> />
		</td>
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
            <input type="button" value="Nuevo" style="width:75px;" class="insert" onclick="cargarPagina(this.form, 'gehen.php?anz=dependencias_form&opcion=nuevo');" />
            <input type="button" value="Modificar" style="width:75px;" class="update" onclick="cargarOpcion2(this.form, 'gehen.php?anz=dependencias_form&opcion=modificar', 'SELF', '', $('#sel_registros').val());" />
            <input type="button" value="Eliminar" style="width:75px;" class="delete" onclick="opcionRegistro3(this.form, $('#sel_registros').val(), 'formulario', 'eliminar', 'dependencias_ajax.php');" />
            <input type="button" value="Ver" style="width:75px;" class="ver" onclick="cargarOpcion2(this.form, 'gehen.php?anz=dependencias_form&opcion=ver', 'SELF', '', $('#sel_registros').val());" />
        </td>
    </tr>
</table>

<div style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px;">
<table class="tblLista" style="width:100%; min-width:1300px;">
	<thead>
    <tr>
        <th width="75" onclick="order('CodDependencia')">C&oacute;digo</th>
        <th align="left" onclick="order('Dependencia')">Descripci&oacute;n</th>
        <th align="left" onclick="order('Organismo,Dependencia')">Organismo</th>
        <th width="90" onclick="order('Estado')">Estado</th>
    </tr>
    </thead>
    
    <tbody id="lista_registros">
	<?php
	//	consulto todos
	$sql = "SELECT d.CodDependencia
			FROM
				mastdependencias d
				INNER JOIN mastorganismos o ON (o.CodOrganismo = d.CodOrganismo)
			WHERE 1 $filtro";
	$rows_total = getNumRows3($sql);
	//	consulto lista
	$sql = "SELECT
				d.*,
				o.Organismo
			FROM
				mastdependencias d
				INNER JOIN mastorganismos o ON (o.CodOrganismo = d.CodOrganismo)
			WHERE 1 $filtro
			ORDER BY $fOrderBy
			LIMIT ".intval($limit).", ".intval($maxlimit);
	$field = getRecords($sql);
	$rows_lista = count($field);
	foreach($field as $f) {
		$id = $f['CodDependencia'];
		?>
		<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
			<td align="center"><?=$f['CodDependencia']?></td>
			<td><?=htmlentities($f['Dependencia'])?></td>
			<td><?=htmlentities($f['Organismo'])?></td>
			<td align="center"><?=printValoresGeneral("ESTADO", $f['Estado'])?></td>
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