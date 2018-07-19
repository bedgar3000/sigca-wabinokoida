<?php
if (empty($ventana)) $ventana = "selLista";
if (empty($fFlagVenta)) $fFlagVenta = "";
//	------------------------------------
if ($filtrar == "default") {
	$fEstado = 'A';
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "CodAlmacen";
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (a.CodAlmacen LIKE '%$fBuscar%'
					  OR a.Descripcion LIKE '%$fBuscar%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (a.Estado = '$fEstado')"; } else $dEstado = "disabled";
if ($fFlagVenta != "") $filtro.=" AND (a.FlagVenta = '$fFlagVenta')";
//	------------------------------------
$_titulo = "Maestro de Almacenes";
$_width = 900;
?>
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lista_lg_almacen" method="post">
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
	<input type="hidden" name="fFlagVenta" id="fFlagVenta" value="<?=$fFlagVenta?>" />

	<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
		<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
			<tr>
				<td align="right" width="100">Buscar:</td>
				<td>
					<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
					<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:169px;" <?=$dBuscar?> />
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
	<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px;">
		<table class="tblLista" style="width:100%; min-width:<?=$_width?>px;">
			<thead>
			    <tr>
			        <th width="60" onclick="order('CodAlmacen')">C&oacute;digo</th>
			        <th style="min-width: 250px;" align="left" onclick="order('Descripcion')">Descripci&oacute;n</th>
			        <th width="40" onclick="order('FlagVenta')">Venta</th>
			        <th width="40" onclick="order('FlagProduccion')">Prod.</th>
			        <th width="40" onclick="order('FlagCommodity')">Com.</th>
			        <th width="40" onclick="order('NomTipoAlmacen')">Tipo</th>
			    </tr>
		    </thead>
		    
		    <tbody>
			<?php
			//	consulto todos
			$sql = "SELECT *
					FROM lg_almacenmast a
					LEFT JOIN mastmiscelaneosdet md ON (
						md.CodDetalle = a.TipoAlmacen
						AND md.CodMaestro = 'TIPOALMACEN'
					)
					WHERE 1 $filtro";
			$rows_total = getNumRows3($sql);
			//	consulto lista
			$sql = "SELECT
						a.*,
						md.Descripcion AS NomTipoAlmacen
					FROM lg_almacenmast a
					LEFT JOIN mastmiscelaneosdet md ON (
						md.CodDetalle = a.TipoAlmacen
						AND md.CodMaestro = 'TIPOALMACEN'
					)
					WHERE 1 $filtro
					ORDER BY $fOrderBy
					LIMIT ".intval($limit).", ".intval($maxlimit);
			$field = getRecords($sql);
			$rows_lista = count($field);
			foreach($field as $f) {
				$id = $f['CodAlmacen'];
				if ($ventana == 'listado_insertar_linea') {
					?>
		            <tr class="trListaBody" onClick="listado_insertar_linea('<?=$detalle?>','modulo=<?=$modulo?>&accion=<?=$accion?>&CodAlmacen=<?=$f['CodAlmacen']?>','<?=$f['CodAlmacen']?>','<?=$url?>');">
		            <?php
				}
				else {
					?>
		            <tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['CodAlmacen']?>','<?=$f['Descripcion']?>'], ['<?=$campo1?>','<?=$campo2?>']);">
		            <?php

				}
				?>
					<td align="center"><?=$f['CodAlmacen']?></td>
					<td><?=htmlentities($f['Descripcion'])?></td>
					<td align="center"><?=printFlag2($f['FlagVenta'])?></td>
					<td align="center"><?=printFlag2($f['FlagProduccion'])?></td>
					<td align="center"><?=printFlag2($f['FlagCommodity'])?></td>
					<td align="center"><?=$f['NomTipoAlmacen']?></td>
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
</form>