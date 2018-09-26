<?php
if (!$ventana) $ventana = "selLista";
//	------------------------------------
if ($filtrar == "default") {
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "CodPlan,NroObjetivo";
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (Descripcion LIKE '%".$fBuscar."%' OR
					  NroResolucion LIKE '%".$fBuscar."%' OR
					  NroGaceta LIKE '%".$fBuscar."%' OR
					  Desde LIKE '%".$fBuscar."%' OR
					  Hasta LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
//	------------------------------------
$_titulo = "Objetivos Estrat&eacute;gicos";
$_width = 800;
?>
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lista_po_objetivo_estrategico" method="post" autocomplete="off">
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

<!--FILTRO-->
<div class="divBorder" style="width:100%; max-width:<?=$_width?>px;">
<table class="tblFiltro" style="width:100%; max-width:<?=$_width?>px;">
	<tr>
		<td align="right" width="100">Buscar:</td>
		<td>
			<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
			<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:300px;" <?=$dBuscar?> />
			<input type="submit" value="Buscar">
		</td>
	</tr>
</table>
</div>
<div class="sep"></div>

<!--REGISTROS-->
<center>
<input type="hidden" name="sel_registros" id="sel_registros" />

<div style="overflow:scroll; width:100%; max-width:<?=$_width?>px; height:265px;">
<table class="tblLista" style="width:100%; min-width:1200px;">
	<thead>
    <tr>
        <th width="75">Nro.</th>
        <th align="left" width="400">Descripci&oacute;n</th>
        <th align="left">Denominaci&oacute;n</th>
    </tr>
    </thead>
    
    <tbody id="lista_registros">
	<?php
	$Grupo='';
	//	consulto todos
	$sql = "SELECT *
			FROM po_planestrategicoobjetivos peo
			INNER JOIN po_planestrategico pe ON (pe.CodPlan = peo.CodPlan)
			WHERE 1 $filtro";
	$rows_total = getNumRows3($sql);
	//	consulto lista
	$sql = "SELECT
				peo.*,
				pe.Descripcion AS NomPlan,
				pe.Desde,
				pe.Hasta
			FROM po_planestrategicoobjetivos peo
			INNER JOIN po_planestrategico pe ON (pe.CodPlan = peo.CodPlan)
			WHERE 1 $filtro
			ORDER BY $fOrderBy
			LIMIT ".intval($limit).", ".intval($maxlimit);
	$field = getRecords($sql);
	$rows_lista = count($field);
	foreach($field as $f) {
		$id = $f['NroObjetivo'];
		if ($Grupo != $f['CodPlan']) {
			$Grupo = $f['CodPlan'];
			?>
	        <tr class="trListaBody2">
				<td align="center"><?=$f['CodPlan']?></td>
				<td colspan="2"><?=htmlentities($f['NomPlan'])?></td>
			</tr>
			<?php
		}
		?>
        <tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['CodPlan']?>','<?=$f['NroObjetivo']?>','<?=$f['Denominacion']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>']);">
			<td align="center"><?=$f['NroObjetivo']?></td>
			<td><?=htmlentities($f['Denominacion'])?></td>
			<td><?=htmlentities($f['Descripcion'])?></td>
		</tr>
		<?php
	}
	?>
    </tbody>
</table>
</div>

<table style="width:100%; max-width:<?=$_width?>px;">
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