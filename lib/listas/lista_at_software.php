<?php
//	------------------------------------
if ($filtrar == "default") {
	$fEstado = "A";
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "NomCategoria,TipoSoftware,CodSoftware";
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (s.CodSoftware LIKE '%".$fBuscar."%' OR
					  s.Descripcion LIKE '%".$fBuscar."%' OR
					  ts.Descripcion LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (s.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fCodTipoSoftware != "") { $cCodTipoSoftware = "checked"; $filtro.=" AND (ts.CodTipoSoftware = '".$fCodTipoSoftware."')"; } else $dCodTipoSoftware = "disabled";
if ($fCategoria != "") { $cCategoria = "checked"; $filtro.=" AND (ts.Categoria = '".$fCategoria."')"; } else $dCategoria = "disabled";
//	------------------------------------
$_titulo = "Maestro de Software";
$_width = 900;
?>
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lista_at_software" method="post">
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
		<td align="right" width="125">Categor&iacute;a:</td>
		<td>
			<input type="checkbox" <?=$cCategoria?> onclick="chkCampos(this.checked, 'fCategoria');" />
        	<select name="fCategoria" id="fCategoria" style="width:175px;" onChange="loadSelect($('#fCodTipoSoftware'), 'tabla=at_tiposoftware&Categoria='+$('#fCategoria').val(), 1);" <?=$dCategoria?>>
            	<option value="">&nbsp;</option>
                <?=getMiscelaneos($fCategoria, 'CATSOFT')?>
            </select>
		</td>
		<td align="right" width="100">Buscar:</td>
		<td>
			<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
			<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:169px;" <?=$dBuscar?> />
		</td>
        <td>&nbsp;</td>
	</tr>
	<tr>
		<td align="right">Tipo de Software:</td>
		<td>
			<input type="checkbox" <?=$cCodTipoSoftware?> onclick="chkCampos(this.checked, 'fCodTipoSoftware');" />
        	<select name="fCodTipoSoftware" id="fCodTipoSoftware" style="width:175px;" <?=$dCodTipoSoftware?>>
            	<option value="">&nbsp;</option>
                <?=loadSelect2('at_tiposoftware','CodTipoSoftware','Descripcion',$fCodTipoSoftware,0,array('Categoria'),array($fCategoria))?>
            </select>
		</td>
		<td align="right">Estado: </td>
		<td>
            <input type="checkbox" <?=$cEstado?> onclick="this.checked=!this.checked;" />
            <select name="fEstado" id="fEstado" style="width:100px;" <?=$dEstado?>>
                <?=loadSelectGeneral("ESTADO", $fEstado, 1)?>
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
        <th width="75" onclick="order('CodSoftware')">C&oacute;digo</th>
        <th align="left" onclick="order('Descripcion')">Descripci&oacute;n</th>
        <th width="275" align="left" onclick="order('NomCategoria,CodTipoSoftware,CodSoftware')">Categor&iacute;a</th>
        <th width="300" align="left" onclick="order('CodTipoSoftware,CodSoftware')">Tipo de Software</th>
        <th width="35" onclick="order('FlagInstalable')">Inst.</th>
        <th width="90" onclick="order('Estado')">Estado</th>
    </tr>
    </thead>
    
    <tbody>
	<?php
	//	consulto todos
	$sql = "SELECT s.CodSoftware
			FROM
				at_software s
				INNER JOIN at_tiposoftware ts ON (ts.CodTipoSoftware = s.CodTipoSoftware)
				LEFT JOIN mastmiscelaneosdet md ON (md.CodDetalle = ts.Categoria AND
													md.CodMaestro = 'CATSOFT' AND
													md.CodAplicacion = 'AT')
			WHERE 1 $filtro";
	$rows_total = getNumRows3($sql);
	//	consulto lista
	$sql = "SELECT
				s.*,
				ts.Descripcion AS TipoSoftware,
				ts.Categoria,
				md.Descripcion AS NomCategoria
			FROM
				at_software s
				INNER JOIN at_tiposoftware ts ON (ts.CodTipoSoftware = s.CodTipoSoftware)
				LEFT JOIN mastmiscelaneosdet md ON (md.CodDetalle = ts.Categoria AND
													md.CodMaestro = 'CATSOFT' AND
													md.CodAplicacion = 'AT')
			WHERE 1 $filtro
			ORDER BY $fOrderBy
			LIMIT ".intval($limit).", ".intval($maxlimit);
	$field = getRecords($sql);
	$rows_lista = count($field);
	foreach($field as $f) {
		$id = $f['CodSoftware'];
		if ($ventana == 'listado_insertar_linea') {
			?>
            <tr class="trListaBody" onClick="listado_insertar_linea('<?=$detalle?>','modulo=<?=$modulo?>&accion=<?=$accion?>&CodSoftware=<?=$f['CodSoftware']?>','<?=$f['CodSoftware']?>','<?=$url?>');">
            <?php
		} 
		else {
			?>
            <tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['CodSoftware']?>','<?=$f['Descripcion']?>'], ['<?=$campo1?>','<?=$campo2?>']);">
            <?php

		}
		?>
			<td align="center"><?=$f['CodSoftware']?></td>
			<td><?=htmlentities($f['Descripcion'])?></td>
			<td><?=htmlentities($f['NomCategoria'])?></td>
			<td><?=htmlentities($f['TipoSoftware'])?></td>
			<td align="center"><?=printFlag($f['FlagInstalable'])?></td>
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