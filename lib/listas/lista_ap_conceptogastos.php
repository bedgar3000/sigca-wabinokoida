<?php
//	------------------------------------
if ($filtrar == "default") {
	$fEstado = "A";
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "CodConceptoGasto";
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (cg.CodConceptoGasto LIKE '%".$fBuscar."%'
                      OR cg.Descripcion LIKE '%".$fBuscar."%'
                      OR cg.CodPartida LIKE '%".$fBuscar."%'
                      OR cg.CodCuenta LIKE '%".$fBuscar."%'
                      OR cg.CodCuentaPub20 LIKE '%".$fBuscar."%'
                      OR cgg.Descripcion LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (cg.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
//	------------------------------------
$_titulo = "Concepto de Gastos";
$_width = 900;
?>
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lista_ap_conceptogastos" method="post">
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
		<td align="right">Buscar:</td>
		<td>
			<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
			<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:169px;" <?=$dBuscar?> />
		</td>
		<td align="right">Grupo de Gasto:</td>
		<td>
			<input type="checkbox" <?=$cCodGastoGrupo?> onclick="chkCampos(this.checked, 'fCodGastoGrupo');" />
        	<select name="fCodGastoGrupo" id="fCodGastoGrupo" style="width:175px;" <?=$dCodGastoGrupo?>>
            	<option value="">&nbsp;</option>
                <?=loadSelect2('ap_conceptogastogrupo','CodGastoGrupo','Descripcion',$fCodGastoGrupo)?>
            </select>
		</td>
        <td align="right"><input type="submit" value="Buscar"></td>
	</tr>
</table>
</div>
<div class="sep"></div>

<center>
<div style="overflow:scroll; height:315px; width:100%; min-width:<?=$_width?>px;">
<table class="tblLista" style="width:100%; min-width:1300px;">
	<thead>
    <tr>
        <th width="75" onclick="order('CodConceptoGasto')">Código</th>
        <th align="left" onclick="order('Descripcion')">Descripción</th>
        <th width="300" align="left" onclick="order('NomGastoGrupo')">Grupo de Gasto</th>
    </tr>
    </thead>
    
    <tbody>
	<?php
	//	consulto todos
	$sql = "SELECT *
            FROM ap_conceptogastos cg
            INNER JOIN ap_conceptogastogrupo cgg ON (cgg.CodGastoGrupo = cg.CodGastoGrupo)
			WHERE 1 $filtro";
	$rows_total = getNumRows3($sql);
	//	consulto lista
	$sql = "SELECT
				cg.*,
				cgg.Descripcion AS NomGastoGrupo
			FROM ap_conceptogastos cg
            INNER JOIN ap_conceptogastogrupo cgg ON (cgg.CodGastoGrupo = cg.CodGastoGrupo)
			WHERE 1 $filtro
			ORDER BY $fOrderBy
			LIMIT ".intval($limit).", ".intval($maxlimit);
	$field = getRecords($sql);
	$rows_lista = count($field);
	foreach($field as $f) {
		$id = $f['CodConceptoGasto'];
		if ($ventana == 'listado_insertar_linea') {
			?>
            <tr class="trListaBody" onClick="listado_insertar_linea('<?=$detalle?>','modulo=<?=$modulo?>&accion=<?=$accion?>&CodConceptoGasto=<?=$f['CodConceptoGasto']?>','<?=$f['CodConceptoGasto']?>','<?=$url?>');">
            <?php
		} 
		else {
			?>
            <tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['CodConceptoGasto']?>','<?=htmlentities($f['Descripcion'])?>'], ['<?=$campo1?>','<?=$campo2?>']);">
            <?php
		}
		?>
			<td align="center"><?=$f['CodConceptoGasto']?></td>
			<td><?=htmlentities($f['Descripcion'])?></td>
			<td><?=htmlentities($f['NomGastoGrupo'])?></td>
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