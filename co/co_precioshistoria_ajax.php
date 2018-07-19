<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion-".".sql", "w+");
##############################################################################/
if ($modulo == "formulario") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		##	-----------------
		##	codigo
		$Correlativo = codigo('co_precioshistoria','Correlativo',10);
		##	detalle
		for ($i=0; $i < count($detalle_CodItem); $i++)
		{
			if ($detalle_TipoDetalle[$i] == 'I')
			{
				$sql = "SELECT * FROM lg_itemmast WHERE CodItem = '$detalle_CodItem[$i]'";
				$field_item = getRecord($sql);
			}
			else
			{
				$sql = "SELECT *, 'UNI' AS CodUnidad FROM co_mastservicios WHERE CodServicio = '$detalle_CodItem[$i]'";
				$field_item = getRecord($sql);
			}
			##	
			$detalle_MontoVenta[$i] = setNumero($detalle_MontoVenta[$i]);
			$detalle_PrecioMayor[$i] = setNumero($detalle_PrecioMayor[$i]);
			$detalle_PrecioMenor[$i] = setNumero($detalle_PrecioMenor[$i]);
			$detalle_PrecioEspecial[$i] = setNumero($detalle_PrecioEspecial[$i]);
			$detalle_PrecioCosto[$i] = setNumero($detalle_PrecioCosto[$i]);
			$detalle_PorcVarPrecioMayor[$i] = setNumero($detalle_PorcVarPrecioMayor[$i]);
			$detalle_PorcVarPrecioMenor[$i] = setNumero($detalle_PorcVarPrecioMenor[$i]);
			$detalle_PorcVarPrecioEspecial[$i] = setNumero($detalle_PorcVarPrecioEspecial[$i]);
			$detalle_FechaDesde[$i] = formatFechaAMD($detalle_FechaDesde);
			$detalle_FechaHasta[$i] = formatFechaAMD($detalle_FechaHasta);
			if (floatval($detalle_MontoVenta[$i]) > floatval($detalle_MontoVentaAnt[$i])) $TipoAccion = 'A'; else $TipoAccion = 'D';
			##	valido
			##	codigo
			$CodPrecioHistoria = codigo('co_precioshistoria','CodPrecioHistoria',10);
			$Secuencia = codigo('co_precioshistoria','Secuencia',11,['CodOrganismo','CodItem','TipoDetalle'],[$CodOrganismo,$detalle_CodItem[$i],$detalle_TipoDetalle[$i]]);
			##	inserto
			$sql = "INSERT INTO co_precioshistoria
					SET
						CodPrecioHistoria = '$CodPrecioHistoria',
						CodOrganismo = '$CodOrganismo',
						TipoDetalle = '$detalle_TipoDetalle[$i]',
						CodItem = '$detalle_CodItem[$i]',
						Secuencia = '$Secuencia',
						TipoAccion = '$TipoAccion',
						CodUnidadVta = '$detalle_CodUnidadVenta[$i]',
						FechaDesde = '$detalle_FechaDesde[$i]',
						FechaHasta = '$detalle_FechaHasta[$i]',
						MontoVentaAnt = '$detalle_MontoVentaAnt[$i]',
						PrecioMayorAnt = '$detalle_PrecioMayorAnt[$i]',
						PrecioMenorAnt = '$detalle_PrecioMenorAnt[$i]',
						PrecioEspecialAnt = '$detalle_PrecioEspecialAnt[$i]',
						PrecioCostoAnt = '$detalle_PrecioCostoAnt[$i]',
						MontoVenta = '$detalle_MontoVenta[$i]',
						PrecioMayor = '$detalle_PrecioMayor[$i]',
						PrecioMenor = '$detalle_PrecioMenor[$i]',
						PrecioEspecial = '$detalle_PrecioEspecial[$i]',
						PrecioCosto = '$detalle_PrecioCosto[$i]',
						PorcVarPrecioMayor = '$detalle_PorcVarPrecioMayor[$i]',
						PorcVarPrecioMenor = '$detalle_PorcVarPrecioMenor[$i]',
						PorcVarPrecioEspecial = '$detalle_PorcVarPrecioEspecial[$i]',
						Correlativo = '$Correlativo',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()";
			execute($sql);
			##	PRECIOS
			$sql = "SELECT *
					FROM co_precios
					WHERE
						CodOrganismo = '$CodOrganismo'
						AND CodItem = '$detalle_CodItem[$i]'
						AND TipoDetalle = '$detalle_TipoDetalle[$i]'";
			$field_precios = getRecord($sql);
			if (empty($field_precios['CodPrecio'])) 
			{
				$field_precios['CodPrecio'] = codigo('co_precios','CodPrecio',10);
				$field_precios['CantidadMayor'] = 0;
				$field_precios['PorcentajeDcto1'] = 0;
				$field_precios['PorcentajeDcto2'] = 0;
				$field_precios['PorcentajeDcto3'] = 0;
			}
			##	actualizo
			$sql = "REPLACE INTO co_precios
					SET
						CodPrecio = '$field_precios[CodPrecio]',
						CodOrganismo = '$CodOrganismo',
						CodItem = '$detalle_CodItem[$i]',
						CodUnidad = '$field_item[CodUnidad]',
						TipoDetalle = '$detalle_TipoDetalle[$i]',
						FechaVigDesde = '$detalle_FechaDesde[$i]',
						FechaVigHasta = '$detalle_FechaHasta[$i]',
						MontoVenta = '$detalle_MontoVenta[$i]',
						PrecioMayor = '$detalle_PrecioMayor[$i]',
						PrecioMenor = '$detalle_PrecioMenor[$i]',
						PrecioEspecial = '$detalle_PrecioEspecial[$i]',
						PrecioCosto = '$detalle_PrecioCosto[$i]',
						CantidadMayor = '$field_precios[CantidadMayor]',
						PorcentajeDcto1 = '$field_precios[PorcentajeDcto1]',
						PorcentajeDcto2 = '$field_precios[PorcentajeDcto2]',
						PorcentajeDcto3 = '$field_precios[PorcentajeDcto3]',
						Estado = 'A',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()";
			execute($sql);
			##	ITEM
			$sql = "UPDATE lg_itemmast
					SET
						PrecioUnitario = '$detalle_MontoVenta[$i]',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	-----------------
		die('OK');
		mysql_query("COMMIT");
	}
}
elseif ($modulo == "ajax") {
	if ($accion == "detalle_insertar") {
		$id = $nro_detalles;
		if (!empty($CodItem))
		{
			$sql = "SELECT
						i.*,
						p.MontoVenta,
						p.PrecioMayor,
						p.PrecioMenor,
						p.PrecioEspecial,
						p.PrecioCosto,
						'I' AS TipoDetalle
					FROM lg_itemmast i
					LEFT JOIN lg_itemalmaceninv iai ON iai.CodItem = i.CodItem
					LEFT JOIN co_precios p ON (
						p.CodItem = i.CodItem
						AND p.TipoDetalle = 'I'
					)
					WHERE i.CodItem = '$CodItem'";
		}
		else
		{
			$sql = "SELECT
						s.*,
						p.MontoVenta,
						p.PrecioMayor,
						p.PrecioMenor,
						p.PrecioEspecial,
						p.PrecioCosto,
						'S' AS TipoDetalle,
						CodServicio AS CodItem,
						'UNI' AS CodUnidad,
						'UNI' AS CodUnidadComp
					FROM co_mastservicios s
					LEFT JOIN co_precios p ON (
						p.CodItem = s.CodServicio
						AND p.TipoDetalle = 'S'
					)
					WHERE s.CodServicio = '$CodServicio'";
		}
		##	
		$field = getRecords($sql);
		foreach ($field as $f)
		{
			$id = (($f['TipoDetalle'] == 'I') ? $CodItem : $CodServicio);
			?>
			<tr class="trListaBody" onclick="clk($(this), 'detalle', 'detalle_<?=$id?>');" id="detalle_<?=$id?>">
				<td align="center">
					<input type="hidden" name="detalle_CodItem[]" value="<?=$f['CodItem']?>">
					<input type="hidden" name="detalle_TipoDetalle[]" value="<?=$f['TipoDetalle']?>">
					<?=$f['CodInterno']?>
				</td>
				<td><?=htmlentities($f['Descripcion'])?></td>
				<td align="center"><?=$f['TipoDetalle']?></td>
				<td><input type="text" name="detalle_CodAlmacen[]" value="<?=$_PARAMETRO['COVTAALMACEN']?>" style="text-align: center;" class="cell2" readonly></td>
				<td>
					<?php if ($f['TipoDetalle'] == 'I') { ?>
						<select name="detalle_CodUnidadVenta[]" class="cell">
							<?=unidades_item($f['CodItem'],$f['CodUnidadComp'],0)?>
						</select>
					<?php } else { ?>
						<select name="detalle_CodUnidadVenta[]" class="cell">
							<?=loadSelect2('mastunidades','CodUnidad','CodUnidad',$f['CodUnidadComp'],1)?>
						</select>
					<?php } ?>
				</td>
				<td>
					<input type="hidden" name="detalle_MontoVentaAnt[]" value="<?=$f['MontoVenta']?>">
					<input type="text" name="detalle_MontoVenta[]" value="<?=number_format($f['MontoVenta'],2,',','.')?>" class="cell currency" style="text-align:right;">
				</td>
				<td>
					<input type="hidden" name="detalle_PrecioMayorAnt[]" value="<?=$f['PrecioMayor']?>">
					<input type="text" name="detalle_PrecioMayor[]" value="<?=number_format($f['PrecioMayor'],2,',','.')?>" class="cell currency" style="text-align:right;">
				</td>
				<td>
					<input type="hidden" name="detalle_PrecioMenorAnt[]" value="<?=$f['PrecioMenor']?>">
					<input type="text" name="detalle_PrecioMenor[]" value="<?=number_format($f['PrecioMenor'],2,',','.')?>" class="cell currency" style="text-align:right;">
				</td>
				<td>
					<input type="hidden" name="detalle_PrecioEspecialAnt[]" value="<?=$f['PrecioEspecial']?>">
					<input type="text" name="detalle_PrecioEspecial[]" value="<?=number_format($f['PrecioEspecial'],2,',','.')?>" class="cell currency" style="text-align:right;">
				</td>
				<td>
					<input type="hidden" name="detalle_PrecioCostoAnt[]" value="<?=$f['PrecioCosto']?>">
					<input type="text" name="detalle_PrecioCosto[]" value="<?=number_format($f['PrecioCosto'],2,',','.')?>" class="cell currency" style="text-align:right;">
				</td>
				<td>
					<input type="text" name="detalle_PorcVarPrecioMayor[]" value="0,00" class="cell currency" style="text-align:right;">
				</td>
				<td>
					<input type="text" name="detalle_PorcVarPrecioMenor[]" value="0,00" class="cell currency" style="text-align:right;">
				</td>
				<td>
					<input type="text" name="detalle_PorcVarPrecioEspecial[]" value="0,00" class="cell currency" style="text-align:right;">
				</td>
				<td>
					<input type="text" name="detalle_FechaDesde[]" value="<?=formatFechaDMA($f['FechaVigDesde'])?>" class="cell datepicker" style="text-align:center;">
				</td>
				<td>
					<input type="text" name="detalle_FechaHasta[]" value="<?=formatFechaDMA($f['FechaVigHasta'])?>" class="cell datepicker" style="text-align:center;">
				</td>
			</tr>
			<?php
		}
	}
}
?>