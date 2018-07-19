<?php
if (empty($accion)) $accion = '';
//	------------------------------------
if ($filtrar == "default") {
	$fEstado = 'A';
	$fTipoDetalle = 'I';
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "CodItem";
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (pr.CodItem LIKE '%$fBuscar%'
					  OR i.Descripcion LIKE '%$fBuscar%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (pr.Estado = '$fEstado')"; } else $dEstado = "disabled";
if ($fTipoDetalle != "") { $cTipoDetalle = "checked"; $filtro.=" AND (pr.TipoDetalle = '$fTipoDetalle')"; } else $dTipoDetalle = "disabled";
//	------------------------------------
$_titulo = "Lista de Precios";
$_width = 700;
?>

	<table width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td class="titulo"><?=$_titulo?></td>
			<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
		</tr>
	</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=co_precios_lista" method="post" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />

	<!--FILTRO-->
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
		            <input type="checkbox" <?=$cEstado?> onclick="chkFiltro(this.checked, 'fEstado');" />
		            <select name="fEstado" id="fEstado" style="width:100px;" <?=$dEstado?>>
		                <option value="">&nbsp;</option>
		                <?=loadSelectGeneral("ESTADO", $fEstado, 0)?>
		            </select>
				</td>
				<td align="right">Tipo Detalle: </td>
				<td>
		            <input type="checkbox" <?=$cTipoDetalle?> onclick="this.checked=!this.checked;" />
		            <select name="fTipoDetalle" id="fTipoDetalle" style="width:100px;" <?=$dTipoDetalle?>>
		                <?=loadSelectValores("cotizacion-tipo-item", $fTipoDetalle, 0)?>
		            </select>
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
	        <td align="right">
	        	<?php if ($accion != 'ver') { ?>
		            <input type="button" value="Nuevo" style="width:75px;" class="insert" onclick="cargarPagina(this.form, 'gehen.php?anz=co_precios_form&opcion=nuevo');" />
		            <input type="button" value="Modificar" style="width:75px;" class="update" onclick="cargarOpcion2(this.form, 'gehen.php?anz=co_precios_form&opcion=modificar', 'SELF', '', $('#sel_registros').val());" />
		            <input type="button" value="Eliminar" style="width:75px;" class="delete" onclick="opcionRegistro3(this.form, $('#sel_registros').val(), 'formulario', 'eliminar', 'co_precios_ajax.php');" />
	        	<?php } ?>
	            <input type="button" value="Ver" style="width:75px;" class="ver" onclick="cargarOpcion2(this.form, 'gehen.php?anz=co_precios_form&opcion=ver', 'SELF', '', $('#sel_registros').val());" />
	        </td>
	    </tr>
	</table>

	<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px;">
		<table class="tblLista" style="width:100%; min-width:1750px;">
			<thead>
			    <tr>
			        <th width="60" onclick="order('CodItem')"><?=($fTipoDetalle == 'I')?'Item':'Servicio'?></th>
			        <th style="min-width: 250px;" align="left" onclick="order('Item')">Descripci&oacute;n</th>
			        <th width="35" onclick="order('CodUnidad')">Uni.</th>
			        <th width="75" onclick="order('FechaVigDesde')">Fec. Vig. Desde</th>
			        <th width="75" onclick="order('FechaVigHasta')">Fec. Vig. Hasta</th>
			        <th width="90" onclick="order('MontoVenta')">Monto Venta</th>
			        <th width="90" onclick="order('PrecioMayor')">Precio Mayor</th>
			        <th width="90" onclick="order('PrecioMenor')">Precio Menor</th>
			        <th width="90" onclick="order('PrecioEspecial')">Precio Especial</th>
			        <th width="90" onclick="order('PrecioCosto')">Precio Costo</th>
			        <th width="90" onclick="order('CantidadMayor')">Cantidad Mayor</th>
			        <th width="90" onclick="order('PorcentajeDcto1')">% Desc. 1</th>
			        <th width="90" onclick="order('PorcentajeDcto2')">% Desc. 2</th>
			        <th width="90" onclick="order('PorcentajeDcto3')">% Desc. 3</th>
			        <th width="75" onclick="order('Estado')">Estado</th>
			    </tr>
		    </thead>
		    
		    <tbody id="lista_registros">
			<?php
			//	consulto todos
			$sql = "SELECT *
					FROM co_precios pr
					LEFT JOIN lg_itemmast i ON (
						i.CodItem = pr.CodItem
						AND pr.TipoDetalle = 'I'
					)
					LEFT JOIN co_mastservicios s ON (
						s.CodServicio = pr.CodItem
						AND pr.TipoDetalle = 'S'
					)
					WHERE 1 $filtro";
			$rows_total = getNumRows3($sql);
			//	consulto lista
			$sql = "SELECT
						pr.*,
						(CASE WHEN pr.TipoDetalle = 'I' THEN i.Descripcion ELSE s.Descripcion END) AS Item,
						s.CodInterno
					FROM co_precios pr
					LEFT JOIN lg_itemmast i ON (
						i.CodItem = pr.CodItem
						AND pr.TipoDetalle = 'I'
					)
					LEFT JOIN co_mastservicios s ON (
						s.CodServicio = pr.CodItem
						AND pr.TipoDetalle = 'S'
					)
					WHERE 1 $filtro
					ORDER BY $fOrderBy
					LIMIT ".intval($limit).", ".intval($maxlimit);
			$field = getRecords($sql);
			$rows_lista = count($field);
			foreach($field as $f) {
				$id = $f['CodPrecio'];
				?>
				<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
					<td align="center"><?=($fTipoDetalle == 'I')?$f['CodItem']:$f['CodInterno']?></td>
					<td><?=htmlentities($f['Item'])?></td>
					<td align="center"><?=$f['CodUnidad']?></td>
					<td align="center"><?=formatFechaDMA($f['FechaVigDesde'])?></td>
					<td align="center"><?=formatFechaDMA($f['FechaVigHasta'])?></td>
					<td align="right"><?=number_format($f['MontoVenta'],2,',','.')?></td>
					<td align="right"><?=number_format($f['PrecioMayor'],2,',','.')?></td>
					<td align="right"><?=number_format($f['PrecioMenor'],2,',','.')?></td>
					<td align="right"><?=number_format($f['PrecioEspecial'],2,',','.')?></td>
					<td align="right"><?=number_format($f['PrecioCosto'],2,',','.')?></td>
					<td align="right"><?=number_format($f['CantidadMayor'],2,',','.')?></td>
					<td align="right"><?=number_format($f['PorcentajeDcto1'],2,',','.')?></td>
					<td align="right"><?=number_format($f['PorcentajeDcto2'],2,',','.')?></td>
					<td align="right"><?=number_format($f['PorcentajeDcto3'],2,',','.')?></td>
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