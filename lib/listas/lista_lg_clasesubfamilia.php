<?php
if (empty($ventana)) $ventana = "selLista";
//	------------------------------------
if ($filtrar == "default") {
	$fEstado = 'A';
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "CodLinea,CodFamilia,CodSubFamilia";
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (cs.CodSubFamilia LIKE '%$fBuscar%'
					  OR cs.Descripcion LIKE '%$fBuscar%'
					  OR cf.CodFamilia LIKE '%$fBuscar%'
					  OR cf.Descripcion LIKE '%$fBuscar%'
					  OR cl.CodLinea LIKE '%$fBuscar%'
					  OR cl.Descripcion LIKE '%$fBuscar%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (cs.Estado = '$fEstado')"; } else $dEstado = "disabled";
if ($fCodFamilia != "") { $cCodFamilia = "checked"; $filtro.=" AND (cs.CodFamilia = '$fCodFamilia')"; } else $dCodFamilia = "disabled";
if ($fCodLinea != "") { $cCodLinea = "checked"; $filtro.=" AND (cs.CodLinea = '$fCodLinea')"; } else $dCodLinea = "disabled";
//	------------------------------------
$_titulo = "Sub-Familias";
$_width = 900;
?>
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lista_lg_clasesubfamilia" method="post">
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
	<input type="hidden" name="campo11" id="campo11" value="<?=$campo11?>" />
	<input type="hidden" name="campo12" id="campo12" value="<?=$campo12?>" />
	<input type="hidden" name="campo13" id="campo13" value="<?=$campo13?>" />
	<input type="hidden" name="campo14" id="campo14" value="<?=$campo14?>" />
	<input type="hidden" name="campo15" id="campo15" value="<?=$campo15?>" />
	<input type="hidden" name="ventana" id="ventana" value="<?=$ventana?>" />
	<input type="hidden" name="detalle" id="detalle" value="<?=$detalle?>" />
	<input type="hidden" name="modulo" id="modulo" value="<?=$modulo?>" />
	<input type="hidden" name="accion" id="accion" value="<?=$accion?>" />
	<input type="hidden" name="url" id="url" value="<?=$url?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />

	<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
		<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
			<tr>
				<td align="right">Linea:</td>
				<td>
					<input type="checkbox" <?=$cCodLinea?> onclick="chkFiltro(this.checked, 'fCodLinea');" />
					<select name="fCodLinea" id="fCodLinea" style="width:300px;" <?=$dCodLinea?> onChange="loadSelect($('#fCodFamilia'), 'tabla=lg_clasefamilia&CodLinea='+$('#fCodLinea').val(), 1, ['fCodFamilia']);">
						<option value="">&nbsp;</option>
						<?=loadSelect2('lg_claselinea','CodLinea','Descripcion',$fCodLinea,10)?>
					</select>
				</td>
				<td align="right">Buscar:</td>
				<td>
					<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
					<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:150px;" <?=$dBuscar?> />
				</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td align="right">Familia:</td>
				<td>
					<input type="checkbox" <?=$cCodFamilia?> onclick="chkFiltro(this.checked, 'fCodFamilia');" />
					<select name="fCodFamilia" id="fCodFamilia" style="width:300px;" <?=$dCodFamilia?>>
						<option value="">&nbsp;</option>
						<?=loadSelect2('lg_clasefamilia','CodFamilia','Descripcion',$fCodFamilia,10,['CodLinea'],[$fCodLinea])?>
					</select>
				</td>
				<td align="right">Estado: </td>
				<td>
		            <input type="checkbox" <?=$cEstado?> onclick="chkFiltro(this.checked, 'fEstado');" />
		            <select name="fEstado" id="fEstado" style="width:150px;" <?=$dEstado?>>
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
			        <th width="60" onclick="order('CodSubFamilia')">Sub-Familia</th>
			        <th style="min-width: 200px;" align="left" onclick="order('Descripcion')">Nombre de la Sub-Familia</th>
			        <th width="60" onclick="order('CodFamilia,CodSubFamilia')">Familia</th>
			        <th style="min-width: 200px;" align="left" onclick="order('CodFamilia,CodSubFamilia')">Nombre de la Familia</th>
			        <th width="60" onclick="order('CodLinea,CodFamilia,CodSubFamilia')">Linea</th>
			        <th style="min-width: 200px;" align="left" onclick="order('Linea,CodFamilia,CodSubFamilia')">Nombre de la Linea</th>
			        <th width="75" onclick="order('Estado')">Estado</th>
			    </tr>
		    </thead>
		    
		    <tbody>
			<?php
			//	consulto todos
			$sql = "SELECT *
					FROM lg_clasesubfamilia cs
					INNER JOIN lg_clasefamilia cf ON (
						cf.CodFamilia = cs.CodFamilia
						AND cf.CodLinea = cs.CodLinea
					)
					INNER JOIN lg_claselinea cl ON cl.CodLinea = cf.CodLinea
					WHERE 1 $filtro";
			$rows_total = getNumRows3($sql);
			//	consulto lista
			$sql = "SELECT
						cs.*,
						cf.Descripcion AS Familia,
						cf.CuentaInventario,
						cf.CuentaGasto,
						cf.CuentaVentas,
						cf.CuentaTransito,
						cf.CuentaInventarioPub20,
						cf.CuentaGastoPub20,
						cf.CuentaVentasPub20,
						cf.CuentaTransitoPub20,
						cf.PartidaPresupuestal,
						cl.Descripcion AS Linea
					FROM lg_clasesubfamilia cs
					INNER JOIN lg_clasefamilia cf ON (
						cf.CodFamilia = cs.CodFamilia
						AND cf.CodLinea = cs.CodLinea
					)
					INNER JOIN lg_claselinea cl ON cl.CodLinea = cf.CodLinea
					WHERE 1 $filtro
					ORDER BY $fOrderBy
					LIMIT ".intval($limit).", ".intval($maxlimit);
			$field = getRecords($sql);
			$rows_lista = count($field);
			foreach($field as $f) {
				$id = $f['CodLinea'] . '_' . $f['CodFamilia'] . '_' . $f['CodSubFamilia'];
				if($ventana == 'items') {
					?>
		            <tr class="trListaBody" onClick="selLista(['<?=$f['CodLinea']?>','<?=$f['CodFamilia']?>','<?=$f['CodSubFamilia']?>','<?=$f['CuentaInventario']?>','<?=$f['CuentaGasto']?>','<?=$f['CuentaVentas']?>','<?=$f['CuentaTransito']?>','<?=$f['CuentaInventarioPub20']?>','<?=$f['CuentaGastoPub20']?>','<?=$f['CuentaVentasPub20']?>','<?=$f['CuentaTransitoPub20']?>','<?=$f['PartidaPresupuestal']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>','<?=$campo4?>','<?=$campo5?>','<?=$campo6?>','<?=$campo7?>','<?=$campo8?>','<?=$campo9?>','<?=$campo10?>','<?=$campo11?>','<?=$campo12?>']);">
		            <?php
				}
				elseif($ventana == 'items_window') {
					?>
		            <tr class="trListaBody" onClick="selListaOpener(['<?=$f['CodLinea']?>','<?=$f['CodFamilia']?>','<?=$f['CodSubFamilia']?>','<?=$f['CuentaInventario']?>','<?=$f['CuentaGasto']?>','<?=$f['CuentaVentas']?>','<?=$f['CuentaTransito']?>','<?=$f['CuentaInventarioPub20']?>','<?=$f['CuentaGastoPub20']?>','<?=$f['CuentaVentasPub20']?>','<?=$f['CuentaTransitoPub20']?>','<?=$f['PartidaPresupuestal']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>','<?=$campo4?>','<?=$campo5?>','<?=$campo6?>','<?=$campo7?>','<?=$campo8?>','<?=$campo9?>','<?=$campo10?>','<?=$campo11?>','<?=$campo12?>']);">
		            <?php
				}
				else {
					?>
		            <tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['CodLinea']?>','<?=$f['CodFamilia']?>','<?=$f['CodSubFamilia']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>']);">
		            <?php
				}
				?>
					<td align="center"><?=$f['CodSubFamilia']?></td>
					<td><?=htmlentities($f['Descripcion'])?></td>
					<td align="center"><?=$f['CodFamilia']?></td>
					<td><?=htmlentities($f['Familia'])?></td>
					<td align="center"><?=$f['CodLinea']?></td>
					<td><?=htmlentities($f['Linea'])?></td>
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
</form>

<script type="text/javascript">
</script>