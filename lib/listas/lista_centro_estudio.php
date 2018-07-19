<?php
if (!$ventana) $ventana = "selLista";
//	------------------------------------
if ($filtrar == "default") {
	$fEstado = "A";
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "Descripcion";
}
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (ce.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro.=" AND (ce.CodCentroEstudio LIKE '%".$fBuscar."%' OR
					ce.Descripcion LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($FlagEstudio != "") $filtro.=" AND (ce.FlagEstudio = '".$FlagEstudio."')";
if ($FlagCurso != "") $filtro.=" AND (ce.FlagCurso = '".$FlagCurso."')";
//	------------------------------------
?>
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lista_centro_estudio" method="post">
<input type="hidden" name="registro" id="registro" />
<input type="hidden" name="campo1" id="campo1" value="<?=$campo1?>" />
<input type="hidden" name="campo2" id="campo2" value="<?=$campo2?>" />
<input type="hidden" name="campo3" id="campo3" value="<?=$campo3?>" />
<input type="hidden" name="campo4" id="campo4" value="<?=$campo4?>" />
<input type="hidden" name="campo5" id="campo5" value="<?=$campo5?>" />
<input type="hidden" name="campo6" id="campo6" value="<?=$campo6?>" />
<input type="hidden" name="campo7" id="campo7" value="<?=$campo7?>" />
<input type="hidden" name="campo8" id="campo8" value="<?=$campo8?>" />
<input type="hidden" name="campo9" id="campo9" value="<?=$campo9?>" />
<input type="hidden" name="campo10" id="campo10" value="<?=$campo10?>" />
<input type="hidden" name="ventana" id="ventana" value="<?=$ventana?>" />
<input type="hidden" name="detalle" id="detalle" value="<?=$detalle?>" />
<input type="hidden" name="modulo" id="modulo" value="<?=$modulo?>" />
<input type="hidden" name="accion" id="accion" value="<?=$accion?>" />
<input type="hidden" name="url" id="url" value="<?=$url?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />

<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
	<tr>
		<td align="right" width="125">Buscar:</td>
        <td>
            <input type="checkbox" <?=$cBuscar?> onclick="chkFiltro(this.checked, 'fBuscar');" />
            <input type="text" name="fBuscar" id="fBuscar" style="width:200px;" value="<?=$fBuscar?>" <?=$dBuscar?> />
		</td>
		<td align="right" width="125">Estado Reg.:</td>
		<td>
            <input type="checkbox" <?=$cEstado?> onclick="this.checked=!this.checked;" />
            <select name="fEstado" id="fEstado" style="width:100px;" <?=$dEstado?>>
                <option value="">&nbsp;</option>
                <?=loadSelectGeneral("ESTADO", $fEstado, 0)?>
            </select>
		</td>
        <td align="right"><input type="submit" value="Buscar"></td>
	</tr>
</table>
</div>
<div class="sep"></div>

<center>
<div style="overflow:scroll; height:315px; width:100%; min-width:<?=$_width?>px;">
<table class="tblLista" style="width:100%; min-width:800px;">
	<thead>
    <tr>
        <th width="75" onclick="order('CodCentroEstudio')">C&oacute;digo</th>
        <th align="left" onclick="order('Descripcion')">Descripci&oacute;n</th>
        <th width="25" onclick="order('FlagEstudio')">C.E.</th>
        <th width="25" onclick="order('FlagCurso')">C.C.</th>
        <th width="60" onclick="order('Estado')">Estado</th>
    </tr>
    </thead>
    
    <tbody>
	<?php
	//	consulto todos	
	$sql = "SELECT *
			FROM rh_centrosestudios ce
			WHERE ce.Estado = 'A' $filtro";
	$rows_total = getNumRows3($sql);
	//	consulto lista
	$sql = "SELECT *
			FROM rh_centrosestudios ce
			WHERE 1 $filtro
			ORDER BY $fOrderBy
			LIMIT ".intval($limit).", ".intval($maxlimit);
	$field = getRecords($sql);
	$rows_lista = count($field);
	foreach($field as $f) {
		?>
        <tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['CodCentroEstudio']?>','<?=$f['Descripcion']?>'], ['<?=$campo1?>','<?=$campo2?>']);">
			<td align="center"><?=$f['CodCentroEstudio']?></td>
			<td><?=htmlentities($f['Descripcion'])?></td>
			<td align="center"><?=printFlag($f['FlagEstudio'])?></td>
			<td align="center"><?=printFlag($f['FlagCurso'])?></td>
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