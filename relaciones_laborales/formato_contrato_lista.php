<?php
//	------------------------------------
//	si en el formulario el usuario cambio la plantilla
if ($txtRutaPlant != $RutaPlantAnterior) {
	//	elimino la foto anterior
	if ($RutaPlantAnterior != "") unlink('../'.$_PARAMETRO["PATHFORM"].$RutaPlantAnterior);
	//	copio la foto
	list($im, $_error) = copiarFoto("RutaPlant", $CodFormato, '../'.$_PARAMETRO["PATHFORM"]);
	//	actualizo el campo foto
	$sql = "UPDATE rh_formatocontrato SET RutaPlant = '".$im."' WHERE CodFormato = '".$CodFormato."'";
	$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
}
//	------------------------------------
if ($filtrar == "default") {
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "CodFormato";
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (fc.CodFormato LIKE '%".$fBuscar."%' OR
					  fc.Documento LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fTipoContrato != "") { $cTipoContrato = "checked"; $filtro.=" AND (fc.TipoContrato = '".$fTipoContrato."')"; } else $dTipoContrato = "disabled";
//	------------------------------------
$_width = 650;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo">Formatos de Contrato</td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=formato_contrato_lista" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />

<!--FILTRO-->
<div class="divBorder" style="width:<?=$_width?>px;">
<table width="<?=$_width?>" class="tblFiltro">
	<tr>
		<td align="right">Buscar:</td>
		<td>
			<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
			<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:195px;" <?=$dBuscar?> />
		</td>
		<td align="right">Tipo de Contrato: </td>
		<td>
            <input type="checkbox" <?=$cTipoContrato?> onclick="chkFiltro(this.checked, 'fTipoContrato');" />
            <select name="fTipoContrato" id="fTipoContrato" style="width:150px;" <?=$dTipoContrato?>>
                <option value="">&nbsp;</option>
                <?=loadSelect2("rh_tipocontrato", "TipoContrato", "Descripcion", $fTipoContrato, 0)?>
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
            <input type="button" id="btNuevo" value="Nuevo" style="width:75px; <?=$btNuevo?>" onclick="cargarPagina(this.form, 'gehen.php?anz=formato_contrato_form&opcion=nuevo');" />
            
            <input type="button" id="btModificar" value="Modificar" style="width:75px; <?=$btModificar?>" onclick="cargarOpcion2(this.form, 'gehen.php?anz=formato_contrato_form&opcion=modificar', 'SELF', '', $('#sel_registros').val());" />
            
            <input type="button" id="btEliminar" value="Eliminar" style="width:75px; <?=$btEliminar?>" onclick="opcionRegistro2(this.form, $('#sel_registros').val(), 'formato_contrato', 'eliminar');" />
            
            <input type="button" id="btVer" value="Ver" style="width:75px; <?=$btVer?>" onclick="cargarOpcion2(this.form, 'gehen.php?anz=formato_contrato_form&opcion=ver', 'SELF', '', $('#sel_registros').val());" />
        </td>
    </tr>
</table>

<div style="overflow:scroll; width:<?=$_width?>px; height:300px;">
<table width="100%" class="tblLista">
	<thead>
    <tr>
        <th scope="col" width="50" onclick="order('CodFormato')">Tipo</th>
        <th scope="col" align="left" onclick="order('Documento')">Descripci&oacute;n</th>
        <th scope="col" width="200" align="left" onclick="order('NomTipoContrato')">Tipo de Contrato</th>
    </tr>
    </thead>
    
    <tbody id="lista_registros">
	<?php
	//	consulto todos
	$sql = "SELECT fc.CodFormato
			FROM
				rh_formatocontrato fc
				INNER JOIN rh_tipocontrato tc ON (tc.TipoContrato = fc.TipoContrato)
			WHERE 1 $filtro";
	$rows_total = getNumRows3($sql);
	
	//	consulto lista
	$sql = "SELECT
				fc.CodFormato,
				fc.Documento,
				tc.Descripcion AS NomTipoContrato
			FROM
				rh_formatocontrato fc
				INNER JOIN rh_tipocontrato tc ON (tc.TipoContrato = fc.TipoContrato)
			WHERE 1 $filtro
			ORDER BY $fOrderBy
			LIMIT ".intval($limit).", ".intval($maxlimit);
	$field = getRecords($sql);
	$rows_lista = count($field);
	foreach($field as $f) {
		$id = "$f[CodFormato]";
		?>
		<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
			<td align="center"><?=$f['CodFormato']?></td>
			<td><?=htmlentities($f['Documento'])?></td>
			<td><?=htmlentities($f['NomTipoContrato'])?></td>
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