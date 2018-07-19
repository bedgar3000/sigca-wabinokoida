<?php
if (!$ventana) $ventana = "selLista";
//	------------------------------------
if ($filtrar == "default") {
	$fEstado = "A";
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "cs.Codigo";

	$fBuscar = ($fBuscar?$fBuscar:$_SESSION["fBuscar"]);
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (cs.Codigo LIKE '%".$fBuscar."%' OR
					  cs.Descripcion LIKE '%".$fBuscar."%' OR
					  cs.CodClasificacion LIKE '%".$fBuscar."%' OR
					  cs.cod_partida LIKE '%".$fBuscar."%' OR
					  c.Descripcion LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (cs.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fClasificacion != "") { $cClasificacion = "checked"; $filtro.=" AND (cc.Clasificacion = '".$fClasificacion."')"; } else $dClasificacion = "disabled";
if ($fCommodityMast != "") { $cCommodityMast = "checked"; $filtro.=" AND (cs.CommodityMast = '".$fCommodityMast."')"; } else $dCommodityMast = "disabled";
if ($fcod_partida != "") { $ccod_partida = "checked"; $filtro.=" AND (cs.cod_partida = '".$fcod_partida."')"; } else $dcod_partida = "visibility:hidden;";
if ($FlagObra) $filtro.=" AND (c.FlagObra = '".$FlagObra."')";
##	
$_SESSION["fBuscar"] = $fBuscar;
//	------------------------------------
$_titulo = "Lista de Commodities";
$_width = 900;
?>
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lista_commodities" method="post" autocomplete="off">
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
<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
<input type="hidden" name="fEjercicio" id="fEjercicio" value="<?=$fEjercicio?>" />
<input type="hidden" name="fCategoriaProg" id="fCategoriaProg" value="<?=$fCategoriaProg?>" />
<input type="hidden" name="FlagClasificacion" id="FlagClasificacion" value="<?=$FlagClasificacion?>" />
<input type="hidden" name="FlagObra" id="FlagObra" value="<?=$FlagObra?>" />

<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
	<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
		<tr>
			<td align="right" width="100">Commodity:</td>
			<td>
				<input type="checkbox" <?=$cCommodityMast?> onclick="chkCampos(this.checked, 'fCommodityMast');" />
				<select name="fCommodityMast" id="fCommodityMast" style="width:300px;" <?=$dCommodityMast?>>
					<option value="">&nbsp;</option>
					<?=loadSelect2("lg_commoditymast", "CommodityMast", "Descripcion", $fCommodityMast, 10)?>
				</select>
			</td>
			<td align="right" width="100">Partida:</td>
			<td class="gallery clearfix">
				<input type="checkbox" <?=$ccod_partida?> onclick="ckLista(this.checked, ['fcod_partida'], ['acod_partida']);" />
				<input type="text" name="fcod_partida" id="fcod_partida" value="<?=$fcod_partida?>" style="width:100px;" readonly="readonly" />
				<a href="javascript:" onclick="window.open('gehen.php?anz=lista_partidas_presupuesto&filtrar=default&ventana=selListaOpener&campo1=fcod_partida','lista','menubar=no,status=no');" id="acod_partida" style=" <?=$dcod_partida?>">
	            	<img src="../../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
	        <td>&nbsp;</td>
		</tr>
		<tr>
			<td align="right">Clasificaci&oacute;n:</td>
			<td>
				<?php
				if ($FlagClasificacion == 'S') {
					?>
					<input type="checkbox" <?=$cClasificacion?> onclick="this.checked=!this.checked;" />
					<select name="fClasificacion" id="fClasificacion" style="width:300px;" <?=$dClasificacion?>>
						<?=loadSelect2("lg_commodityclasificacion", "Clasificacion", "Descripcion", $fClasificacion, 1)?>
					</select>
					<?php
				} else {
					?>
					<input type="checkbox" <?=$cClasificacion?> onclick="chkCampos(this.checked, 'fClasificacion');" />
					<select name="fClasificacion" id="fClasificacion" style="width:300px;" <?=$dClasificacion?>>
						<option value="">&nbsp;</option>
						<?=loadSelect2("lg_commodityclasificacion", "Clasificacion", "Descripcion", $fClasificacion, 0)?>
					</select>
					<?php
				}
				?>
			</td>
			<td align="right">Buscar:</td>
			<td>
				<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
				<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:250px;" <?=$dBuscar?> />
			</td>
	        <td align="right"><input type="submit" value="Buscar"></td>
		</tr>
	</table>
</div>
<div class="sep"></div>

<div class="scroll" style="overflow:scroll; height:315px; width:100%; min-width:<?=$_width?>px; margin:auto;">
	<table class="tblLista" style="width:100%; min-width:1200px;">
		<thead>
		    <tr>
		        <th width="75" onclick="order('Codigo')">C&oacute;digo</th>
		        <th align="left" onclick="order('Descripcion')">Descripci&oacute;n</th>
		        <th width="90" onclick="order('cod_partida,Codigo')">Partida</th>
		        <th width="100" onclick="order('CodClasificacion,Codigo')">Clasificaci&oacute;n</th>
		        <th width="350" align="left" onclick="order('NomCommodity,Codigo')">Commodity</th>
		    </tr>
	    </thead>
	    
	    <tbody>
		<?php
		//	consulto todos
		$sql = "SELECT cs.*
				FROM
					lg_commoditysub cs
					INNER JOIN lg_commoditymast c ON (cs.CommodityMast = c.CommodityMast)
					INNER JOIN lg_commodityclasificacion cc ON (c.Clasificacion = cc.Clasificacion)
					-- INNER JOIN pv_presupuestodet pd ON (pd.cod_partida = cs.cod_partida)
					-- INNER JOIN pv_presupuesto p ON (p.CodOrganismo = pd.CodOrganismo AND p.CodPresupuesto = pd.CodPresupuesto)
				WHERE 1 $filtro
				GROUP BY cs.Codigo";
		$rows_total = getNumRows3($sql);
		//	consulto lista
		$sql = "SELECT
					cs.*,
					c.Descripcion AS NomCommodity,
					cc.Descripcion AS NomClasificacion
				FROM
					lg_commoditysub cs
					INNER JOIN lg_commoditymast c ON (cs.CommodityMast = c.CommodityMast)
					INNER JOIN lg_commodityclasificacion cc ON (c.Clasificacion = cc.Clasificacion)
					-- INNER JOIN pv_presupuestodet pd ON (pd.cod_partida = cs.cod_partida)
					-- INNER JOIN pv_presupuesto p ON (p.CodOrganismo = pd.CodOrganismo AND p.CodPresupuesto = pd.CodPresupuesto)
				WHERE 1 $filtro
				GROUP BY cs.Codigo
				ORDER BY $fOrderBy
				LIMIT ".intval($limit).", ".intval($maxlimit);
		$field = getRecords($sql);
		$rows_lista = count($field);
		foreach($field as $f) {
			$id = $f['Codigo'];
			if ($ventana == 'listado_insertar_linea') {
				?><tr class="trListaBody" onClick="<?=$ventana?>('<?=$detalle?>','modulo=<?=$modulo?>&accion=<?=$accion?>&Codigo=<?=$f['Codigo']?>','<?=$f['Codigo']?>','<?=$url?>');"><?php
			}
			elseif ($ventana == 'pv_formulacionmetas') {
				?><tr class="trListaBody" onClick="listado_insertar_linea('<?=$detalle?>','modulo=<?=$modulo?>&accion=<?=$accion?>&Codigo=<?=$f['Codigo']?>&detalle=<?=$detalle?>','<?=$f['Codigo']?>','<?=$url?>');"><?php
			} 
			else {
				?><tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['Codigo']?>','<?=$f['Descripcion']?>'], ['<?=$campo1?>','<?=$campo2?>']);"><?php
			}
			?>
				<td align="center"><?=$f['Codigo']?></td>
				<td><?=htmlentities($f['Descripcion'])?></td>
				<td align="center"><?=$f['cod_partida']?></td>
				<td align="center"><?=$f['CodClasificacion']?></td>
				<td><?=htmlentities($f['NomCommodity'])?></td>
			</tr>
			<?php
		}
		?>
	    </tbody>
	</table>
</div>
<table style="width:100%; min-width:<?=$_width?>px; margin:auto;">
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
</form>