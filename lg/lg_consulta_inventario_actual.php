<?php
//	------------------------------------
if ($filtrar == "default") {
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "CodItem";
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (CodItem LIKE '%$fBuscar%'
					  OR Descripcion LIKE '%$fBuscar%'
					  OR CodInterno LIKE '%$fBuscar%'
					  OR CodUnidad LIKE '%$fBuscar%'
					  OR CodLinea LIKE '%$fBuscar%'
					  OR CodFamilia LIKE '%$fBuscar%'
					  OR CodSubFamilia LIKE '%$fBuscar%')";
} else $dBuscar = "disabled";
if ($fCodAlmacen != "") { $cCodAlmacen = "checked"; $filtro.=" AND (CodAlmacen = '$fCodAlmacen')"; } else $dCodAlmacen = "disabled";
if ($fCodTipoItem != "") { $cCodTipoItem = "checked"; $filtro.=" AND (CodTipoItem = '$fCodTipoItem')"; } else $dCodTipoItem = "disabled";
if ($fCodMarca != "") { $cCodMarca = "checked"; $filtro.=" AND (CodMarca = '$fCodMarca')"; } else $dCodMarca = "disabled";
if ($fCodLinea != "") { $cCodLinea = "checked"; $filtro.=" AND (CodLinea = '$fCodLinea')"; } else $dCodLinea = "visibility: hidden;";
if ($fCodFamilia != "") { $cCodFamilia = "checked"; $filtro.=" AND (CodFamilia = '$fCodFamilia')"; }
if ($fCodSubFamilia != "") { $cCodSubFamilia = "checked"; $filtro.=" AND (CodSubFamilia = '$fCodSubFamilia')"; }
if ($fStockD != "" || $fStockH != "") {
	$cStock = "checked";
	if ($fStockD != "") $filtro.=" AND (StockActual >= '".setNumero($fStockD)."')";
	if ($fStockH != "") $filtro.=" AND (StockActual <= '".setNumero($fStockH)."')";
} else $dStock = "disabled";
if ($fPrecioUnitarioD != "" || $fPrecioUnitarioH != "") {
	$cPrecioUnitario = "checked";
	if ($fPrecioUnitarioD != "") $filtro.=" AND (PrecioUnitario >= '".setNumero($fPrecioUnitarioD)."')";
	if ($fPrecioUnitarioH != "") $filtro.=" AND (PrecioUnitario <= '".setNumero($fPrecioUnitarioH)."')";
} else $dPrecioUnitario = "disabled";
//	------------------------------------
$_titulo = "Inventario Actual";
$_width = 700;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lg_consulta_inventario_actual" method="post" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />

	<!--FILTRO-->
	<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
		<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
			<tr>
				<td align="right">Almacen:</td>
				<td>
					<input type="checkbox" <?=$cCodAlmacen?> onclick="chkFiltro(this.checked, 'fCodAlmacen');" />
					<select name="fCodAlmacen" id="fCodAlmacen" style="width:200px;" <?=$dCodAlmacen?>>
						<option value="">&nbsp;</option>
						<?=loadSelect2('lg_almacenmast','CodAlmacen','Descripcion',$fCodAlmacen)?>
					</select>
				</td>
				<td align="right">Marca:</td>
				<td>
					<input type="checkbox" <?=$cCodMarca?> onclick="chkFiltro(this.checked, 'fCodMarca');" />
					<select name="fCodMarca" id="fCodMarca" style="width:150px;" <?=$dCodMarca?>>
						<option value="">&nbsp;</option>
						<?=loadSelect2('lg_marcas','CodMarca','Descripcion',$fCodMarca)?>
					</select>
				</td>
				<td class="tagForm">Linea:</td>
				<td class="gallery clearfix">
					<input type="checkbox" id="cCodLinea" <?=$cCodLinea?> onclick="ckLista(this.checked, ['fCodLinea'], ['bCodLinea']);" />
		        	<input type="text" name="fCodLinea" id="fCodLinea" value="<?=$fCodLinea?>" style="width:125px;" readonly />
		            <a href="../lib/listas/listado_familias.php?filtrar=default&campo1=fCodLinea&campo2=fCodFamilia&campo3=fCodSubFamilia&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" style=" <?=$dCodLinea?>" id="bCodLinea">
		            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
		            </a>
				</td>
			</tr>
			<tr>
				<td align="right">Tipo de Item:</td>
				<td>
					<input type="checkbox" <?=$cCodTipoItem?> onclick="chkFiltro(this.checked, 'fCodTipoItem');" />
					<select name="fCodTipoItem" id="fCodTipoItem" style="width:200px;" <?=$dCodTipoItem?>>
						<option value="">&nbsp;</option>
						<?=loadSelect2('lg_tipoitem','CodTipoItem','Descripcion',$fCodTipoItem)?>
					</select>
				</td>
				<td align="right">Stock Actual:</td>
				<td>
					<input type="checkbox" <?=$cStock?> onclick="chkCampos2(this.checked, ['fStockD','fStockH']);" />
					<input type="text" name="fStockD" id="fStockD" value="<?=$fStockD?>" <?=$dStock?> style="width:69px; text-align: right;" class="currency" /> -
		            <input type="text" name="fStockH" id="fStockH" value="<?=$fStockH?>" <?=$dStock?> style="width:69px; text-align: right;" class="currency" />
		        </td>
				<td class="tagForm">Familia:</td>
				<td class="gallery clearfix">
					<input type="checkbox" id="cCodFamilia" style="visibility: hidden;" />
		        	<input type="text" name="fCodFamilia" id="fCodFamilia" value="<?=$fCodFamilia?>" style="width:125px;" readonly />
				</td>
			</tr>
			<tr>
				<td align="right" width="100">Buscar:</td>
				<td>
					<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
					<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:200px;" <?=$dBuscar?> />
				</td>
				<td align="right">Precio Unitario:</td>
				<td>
					<input type="checkbox" <?=$cPrecioUnitario?> onclick="chkCampos2(this.checked, ['fPrecioUnitarioD','fPrecioUnitarioH']);" />
					<input type="text" name="fPrecioUnitarioD" id="fPrecioUnitarioD" value="<?=$fPrecioUnitarioD?>" <?=$dPrecioUnitario?> style="width:69px; text-align: right;" class="currency" /> -
		            <input type="text" name="fPrecioUnitarioH" id="fPrecioUnitarioH" value="<?=$fPrecioUnitarioH?>" <?=$dPrecioUnitario?> style="width:69px; text-align: right;" class="currency" />
		        </td>
				<td class="tagForm">Sub-Familia:</td>
				<td class="gallery clearfix">
					<input type="checkbox" style="visibility: hidden;" />
		        	<input type="text" name="fCodSubFamilia" id="fCodSubFamilia" value="<?=$fCodSubFamilia?>" style="width:125px;" readonly />
				</td>
		        <td align="right"><input type="submit" value="Buscar"></td>
			</tr>
		</table>
	</div>
	<div class="sep"></div>

	<!--REGISTROS-->
	<input type="hidden" name="sel_registros" id="sel_registros" />
	<table class="tblBotones" style="width:100%; min-width:<?=$_width?>px;">
	    <tr>
	        <td><div id="rows"></div></td>
	    </tr>
	</table>

	<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px;">
		<table class="tblLista" style="width:100%; min-width:<?=$_width?>px;">
			<thead>
			    <tr>
			        <th width="60" onclick="order('CodItem')">Item</th>
			        <th style="min-width: 200px;" align="left" onclick="order('Descripción')">Descripción</th>
			        <th width="60" onclick="order('CodInterno')">Código</th>
			        <th width="30" onclick="order('CodUnidad')">Uni.</th>
			        <th width="75" onclick="order('StockActual')">Stock Actual</th>
			        <th width="30" onclick="order('CodUnidadConv')">Uni. (Equi.)</th>
			        <th width="75" onclick="order('StockActualConv')">Stock Actual (Equi.)</th>
			        <th width="100" onclick="order('PrecioUnitario')">Precio Unitario</th>
			        <th width="50" onclick="order('CodLinea')">Linea</th>
			        <th width="50" onclick="order('CodFamilia')">Familia</th>
			        <th width="50" onclick="order('CodSubFamilia')">Sub-Familia</th>
			    </tr>
		    </thead>
		    
		    <tbody id="lista_registros">
			<?php
			//	consulto todos
			$sql = "SELECT *
					FROM vw_lg_inventarioactual_item
					WHERE 1 $filtro";
			$rows_total = getNumRows3($sql);
			//	consulto lista
			$sql = "SELECT
						*,
						(Ingresos - Egresos) StockActual,
						((Ingresos - Egresos) / CantidadEqui) AS StockActualEqui
					FROM vw_lg_inventarioactual_item
					WHERE 1 $filtro
					ORDER BY $fOrderBy
					LIMIT ".intval($limit).", ".intval($maxlimit);
			$field = getRecords($sql);
			$rows_lista = count($field);
			foreach($field as $f) {
				$id = $f['CodCajero'];
				?>
				<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
					<td align="center"><?=$f['CodItem']?></td>
					<td><?=htmlentities($f['Descripcion'])?></td>
					<td align="center"><?=$f['CodInterno']?></td>
					<td align="center"><?=$f['CodUnidad']?></td>
					<td align="right"><?=number_format($f['StockActual'],5,',','.')?></td>
					<td align="center"><?=$f['CodUnidadEqui']?></td>
					<td align="right"><?=number_format($f['StockActualEqui'],5,',','.')?></td>
					<td align="right"><?=number_format($f['PrecioUnitario'],2,',','.')?></td>
					<td align="center"><?=$f['CodLinea']?></td>
					<td align="center"><?=$f['CodFamilia']?></td>
					<td align="center"><?=$f['CodSubFamilia']?></td>
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