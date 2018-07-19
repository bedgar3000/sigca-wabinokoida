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
		$FechaDocumento = formatFechaAMD($FechaDocumento);
		$FechaVencimiento = formatFechaAMD($FechaVencimiento);
		$MontoAfecto = setNumero($MontoAfecto);
		$MontoNoAfecto = setNumero($MontoNoAfecto);
		$MontoDcto = setNumero($MontoDcto);
		$MontoImpuesto = setNumero($MontoImpuesto);
		$MontoTotal = setNumero($MontoTotal);
		$Anio = substr($FechaDocumento, 0, 4);
		$iCodPersonaVendedor = (!empty($CodPersonaVendedor)?"CodPersonaVendedor = '$CodPersonaVendedor',":"CodPersonaVendedor = NULL,");
		##	valido
		if (!trim($CodOrganismo) || !trim($CodEstablecimiento) || !trim($CodPersonaCliente) || !trim($FechaDocumento) || !trim($FechaVencimiento)) die("Debe llenar los campos (*) obligatorios.");
		##	codigo
		$CodCotizacion = codigo('co_cotizacion','CodCotizacion',10);
		##	inserto
		$sql = "INSERT INTO co_cotizacion
				SET
					CodCotizacion = '$CodCotizacion',
					CodOrganismo = '$CodOrganismo',
					CodEstablecimiento = '$CodEstablecimiento',
					FechaDocumento = '$FechaDocumento',
					FechaVencimiento = '$FechaVencimiento',
					Anio = '$Anio',
					CodPersonaCliente = '$CodPersonaCliente',
					DocFiscalCliente = '$DocFiscalCliente',
					NombreCliente = '$NombreCliente',
					DireccionCliente = '$DireccionCliente',
					CodCentroCosto = '$CodCentroCosto',
					MonedaDocumento = '$MonedaDocumento',
					MontoAfecto = '$MontoAfecto',
					MontoNoAfecto = '$MontoNoAfecto',
					MontoDcto = '$MontoDcto',
					MontoImpuesto = '$MontoImpuesto',
					MontoTotal = '$MontoTotal',
					Comentarios = '$Comentarios',
					PreparadoPor = '$PreparadoPor',
					FechaPreparado = '$FechaPreparado',
					$iCodPersonaVendedor
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()";
		execute($sql);
		##	detalle
		$Secuencia = 0;
		for ($i=0; $i < count($detalle_Secuencia); $i++)
		{
			++$Secuencia;
			$detalle_CantidadPedida[$i] = setNumero($detalle_CantidadPedida[$i]);
			$detalle_PrecioUnit[$i] = setNumero($detalle_PrecioUnit[$i]);
			$detalle_MontoTotal[$i] = setNumero($detalle_MontoTotal[$i]);
			$detalle_PrecioUnitOriginal[$i] = setNumero($detalle_PrecioUnitOriginal[$i]);
			$detalle_PorcentajeDcto[$i] = setNumero($detalle_PorcentajeDcto[$i]);
			$detalle_MontoDcto[$i] = setNumero($detalle_MontoDcto[$i]);
			$detalle_PrecioUnitFinal[$i] = setNumero($detalle_PrecioUnitFinal[$i]);
			$detalle_MontoTotalFinal[$i] = setNumero($detalle_MontoTotalFinal[$i]);
			$detalle_FlagExonIva[$i] = (!empty($detalle_FlagExonIva[$i])?'S':'N');
			##	valido
			if (!trim($detalle_CantidadPedida[$i])) die("La Cantidad Pedida no puede ser cero.");
			elseif (!trim($detalle_PrecioUnit[$i])) die("El Precio Unitario no puede ser cero.");
			##	inserto
			$sql = "INSERT INTO co_cotizaciondet
					SET
						CodCotizacion = '$CodCotizacion',
						Secuencia = '$Secuencia',
						TipoDetalle = '$detalle_TipoDetalle[$i]',
						CodItem = '$detalle_CodItem[$i]',
						Descripcion = '$detalle_Descripcion[$i]',
						CodUnidad = '$detalle_CodUnidad[$i]',
						CodUnidadVenta = '$detalle_CodUnidadVenta[$i]',
						PrecioUnit = '$detalle_PrecioUnit[$i]',
						CantidadPedida = '$detalle_CantidadPedida[$i]',
						MontoTotal = '$detalle_MontoTotal[$i]',
						PrecioUnitOriginal = '$detalle_PrecioUnitOriginal[$i]',
						PrecioUnitFinal = '$detalle_PrecioUnitFinal[$i]',
						MontoTotalFinal = '$detalle_MontoTotalFinal[$i]',
						FlagExonIva = '$detalle_FlagExonIva[$i]',
						PorcentajeDcto = '$detalle_PorcentajeDcto[$i]',
						MontoDcto = '$detalle_MontoDcto[$i]',
						FlagPrecioEspecial = '$detalle_FlagPrecioEspecial[$i]',
						Estado = '$detalle_Estado[$i]',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	-----------------
		mysql_query("COMMIT");
	}
	//	modificar
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		##	-----------------
		$FechaDocumento = formatFechaAMD($FechaDocumento);
		$FechaVencimiento = formatFechaAMD($FechaVencimiento);
		$MontoAfecto = setNumero($MontoAfecto);
		$MontoNoAfecto = setNumero($MontoNoAfecto);
		$MontoDcto = setNumero($MontoDcto);
		$MontoImpuesto = setNumero($MontoImpuesto);
		$MontoTotal = setNumero($MontoTotal);
		$Anio = substr($FechaDocumento, 0, 4);
		$iCodPersonaVendedor = (!empty($CodPersonaVendedor)?"CodPersonaVendedor = '$CodPersonaVendedor',":"CodPersonaVendedor = NULL,");
		##	valido
		if (!trim($CodEstablecimiento) || !trim($CodPersonaCliente) || !trim($FechaDocumento) || !trim($FechaVencimiento)) die("Debe llenar los campos (*) obligatorios.");
		##	actualizo
		$sql = "UPDATE co_cotizacion
				SET
					CodEstablecimiento = '$CodEstablecimiento',
					FechaDocumento = '$FechaDocumento',
					FechaVencimiento = '$FechaVencimiento',
					Anio = '$Anio',
					CodPersonaCliente = '$CodPersonaCliente',
					DocFiscalCliente = '$DocFiscalCliente',
					NombreCliente = '$NombreCliente',
					DireccionCliente = '$DireccionCliente',
					CodCentroCosto = '$CodCentroCosto',
					MonedaDocumento = '$MonedaDocumento',
					MontoAfecto = '$MontoAfecto',
					MontoNoAfecto = '$MontoNoAfecto',
					MontoDcto = '$MontoDcto',
					MontoImpuesto = '$MontoImpuesto',
					MontoTotal = '$MontoTotal',
					Comentarios = '$Comentarios',
					$iCodPersonaVendedor
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodCotizacion = '$CodCotizacion'";
		execute($sql);
		##	detalle
		if (count($detalle_Secuencia))
		{
			$sql = "DELETE FROM co_cotizaciondet
					WHERE
						CodCotizacion = '$CodCotizacion'
						AND Secuencia NOT IN (".implode(",",$detalle_Secuencia).")";
		}
		else
		{
			$sql = "DELETE FROM co_cotizaciondet WHERE CodCotizacion = '$CodCotizacion'";
		}
		execute($sql);
		$Secuencia = 0;
		for ($i=0; $i < count($detalle_Secuencia); $i++)
		{
			if (!$detalle_Secuencia[$i]) 
				$detalle_Secuencia[$i] = codigo('co_cotizaciondet','Secuencia',11,['CodCotizacion'],[$CodCotizacion]);
			$detalle_CantidadPedida[$i] = setNumero($detalle_CantidadPedida[$i]);
			$detalle_PrecioUnit[$i] = setNumero($detalle_PrecioUnit[$i]);
			$detalle_MontoTotal[$i] = setNumero($detalle_MontoTotal[$i]);
			$detalle_PrecioUnitOriginal[$i] = setNumero($detalle_PrecioUnitOriginal[$i]);
			$detalle_PorcentajeDcto[$i] = setNumero($detalle_PorcentajeDcto[$i]);
			$detalle_MontoDcto[$i] = setNumero($detalle_MontoDcto[$i]);
			$detalle_PrecioUnitFinal[$i] = setNumero($detalle_PrecioUnitFinal[$i]);
			$detalle_MontoTotalFinal[$i] = setNumero($detalle_MontoTotalFinal[$i]);
			$detalle_FlagExonIva[$i] = (!empty($detalle_FlagExonIva[$i])?'S':'N');
			##	valido
			if (!trim($detalle_CantidadPedida[$i])) die("La Cantidad Pedida no puede ser cero.");
			elseif (!trim($detalle_PrecioUnit[$i])) die("El Precio Unitario no puede ser cero.");
			##	inserto
			$sql = "REPLACE INTO co_cotizaciondet
					SET
						CodCotizacion = '$CodCotizacion',
						Secuencia = '$detalle_Secuencia[$i]',
						TipoDetalle = '$detalle_TipoDetalle[$i]',
						CodItem = '$detalle_CodItem[$i]',
						Descripcion = '$detalle_Descripcion[$i]',
						CodUnidad = '$detalle_CodUnidad[$i]',
						CodUnidadVenta = '$detalle_CodUnidadVenta[$i]',
						PrecioUnit = '$detalle_PrecioUnit[$i]',
						CantidadPedida = '$detalle_CantidadPedida[$i]',
						MontoTotal = '$detalle_MontoTotal[$i]',
						PrecioUnitOriginal = '$detalle_PrecioUnitOriginal[$i]',
						PrecioUnitFinal = '$detalle_PrecioUnitFinal[$i]',
						MontoTotalFinal = '$detalle_MontoTotalFinal[$i]',
						FlagExonIva = '$detalle_FlagExonIva[$i]',
						PorcentajeDcto = '$detalle_PorcentajeDcto[$i]',
						MontoDcto = '$detalle_MontoDcto[$i]',
						FlagPrecioEspecial = '$detalle_FlagPrecioEspecial[$i]',
						Estado = '$detalle_Estado[$i]',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	-----------------
		mysql_query("COMMIT");
	}
	//	aprobar
	elseif ($accion == "aprobar") {
		mysql_query("BEGIN");
		##	-----------------
		$field = getRecord("SELECT * FROM co_cotizacion WHERE CodCotizacion = '$CodCotizacion'");
		##	codigo
		if (empty($field['NroCotizacion'])) 
			$NroCotizacion = codigo('co_cotizacion','NroCotizacion',7,['Anio'],[$field['Anio']]);
		else 
			$NroCotizacion = $field['NroCotizacion'];
		##	actualizo
		$sql = "UPDATE co_cotizacion
				SET
					AprobadoPor = '$AprobadoPor',
					FechaAprobado = '$FechaAprobado',
					NroCotizacion = '$NroCotizacion',
					Estado = 'AP',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodCotizacion = '$CodCotizacion'";
		execute($sql);
		##	detalle
		$sql = "UPDATE co_cotizaciondet
				SET
					Estado = 'PE',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodCotizacion = '$CodCotizacion'";
		execute($sql);
		##	
		$message = "|Se ha generado el documento Nro. <strong>$NroCotizacion</strong>";
		##	-----------------
		mysql_query("COMMIT");
		##	
		die($message);
	}
	//	anular
	elseif ($accion == "anular") {
		mysql_query("BEGIN");
		##	-----------------
		$field_cotizacion = getRecord("SELECT * FROM co_cotizacion WHERE CodCotizacion = '$CodCotizacion'");
		##	
		if ($field_cotizacion['Estado'] == 'AP') $NuevoEstado = 'PR';
		elseif ($field_cotizacion['Estado'] == 'PR') $NuevoEstado = 'AN';
		else die('No puede anular una cotización <strong>'.printValores('cotizacion-estado',$field_cotizacion['Estado']).'</strong>');
		##	actualizo
		$sql = "UPDATE co_cotizacion
				SET
					Estado = '$NuevoEstado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodCotizacion = '$CodCotizacion'";
		execute($sql);
		##	detalle
		$sql = "UPDATE co_cotizaciondet
				SET
					Estado = '$NuevoEstado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodCotizacion = '$CodCotizacion'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
}
elseif ($modulo == "validar") {
	//	modificar
	if($accion == "modificar") {
		$sql = "SELECT Estado FROM co_cotizacion WHERE CodCotizacion = '$codigo'";
		$Estado = getVar3($sql);
		if ($Estado != 'PR') die('No puede modificar una cotizaci&oacute;n <strong>'.printValores('cotizacion-estado',$Estado).'</strong>');
	}
	//	aprobar
	elseif($accion == "aprobar") {
		$sql = "SELECT Estado FROM co_cotizacion WHERE CodCotizacion = '$codigo'";
		$Estado = getVar3($sql);
		if ($Estado != 'PR') die('No puede aprobar una cotizaci&oacute;n <strong>'.printValores('cotizacion-estado',$Estado).'</strong>');
	}
	//	anular
	elseif($accion == "anular") {
		$sql = "SELECT Estado FROM co_cotizacion WHERE CodCotizacion = '$codigo'";
		$Estado = getVar3($sql);
		if ($Estado != 'PR' && $Estado != 'AP') die('No puede anular una cotizaci&oacute;n <strong>'.printValores('cotizacion-estado',$Estado).'</strong>');
	}
	//	generar pedido
	elseif ($accion == "generar") {
		$status = 'success';
		$items = [];
		$filtro_detalle = '';
		foreach ($registros as $row)
		{
			list($CodCotizacion, $Secuencia) = explode('_', $row);

			$sql = "SELECT
						cd.CantidadPedida,
						cd.CodUnidad,
						cd.CodUnidadVenta,
						i.CodInterno,
						cd.Descripcion,
						i.StockActual,
						i.StockActualEqui,
						i.CodUnidadEqui,
						i.CantidadEqui
					FROM co_cotizaciondet cd
					INNER JOIN vw_lg_inventarioactual_item i ON i.CodItem = cd.CodItem
					WHERE
						cd.CodCotizacion = '$CodCotizacion'
						AND cd.Secuencia = '$Secuencia'
						AND cd.TipoDetalle = 'I'";
			$field_detalle = getRecord($sql);
			if ($field_detalle) 
			{
				if ($field_detalle['CodUnidadVenta'] == $field_detalle['CodUnidad'])
				{
					$StockActual = $field_detalle['StockActual'];
				}
				elseif ($field_detalle['CodUnidadVenta'] == $field_detalle['CodUnidadEqui'])
				{
					$StockActual = $field_detalle['StockActualEqui'];
				}

				if ($StockActual < $field_detalle['CantidadPedida']) 
				{
					$items[] = '<p style="font-weight:bold;">' . $field_detalle['CodInterno'] . ' - ' . $field_detalle['Descripcion'] . '</p>';
					$status = 'warning';
				}
			}
		}
		die(json_encode([
    		'status' => $status,
    		'message' => 'Se encontraron los siguientes items sin Stock: ' . implode('',$items) . '¿Está seguro de continuar?',
    	]));
	}
}
elseif ($modulo == "ajax") {
	if ($accion == "detalle_insertar") {
		$id = $nro_detalles;
		$Cantidad = setNumero($Cantidad);
		$igv = getVar3("SELECT FactorPorcentaje FROM mastimpuestos WHERE CodImpuesto = '$_PARAMETRO[COIVA]'");
		$igvp = $igv / 100 + 1;
		if (!empty($CodItem))
		{
			$sql = "SELECT
						i.*,
						p.PrecioEspecial,
						p.PrecioEspecialVta,
						'I' AS TipoDetalle,
						(CASE WHEN i.FlagImpuestoVentas = 'S' THEN 'N' ELSE 'S' END) AS FlagExonIva,
						'0' AS Unidades
					FROM vw_lg_inventarioactual_item i
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
						p.MontoVenta AS MontoVenta,
						p.PrecioUnitario AS MontoVentaUnitario,
						p.PrecioEspecial,
						p.PrecioEspecialVta,
						'S' AS TipoDetalle,
						s.CodServicio AS CodItem,
						'UNI' AS CodUnidad,
						'UNI' AS CodUnidadComp,
						'0' AS StockActual,
						'0' AS StockActualEqui,
						s.FlagExoneradoIva AS FlagExonIva,
						'1' AS Unidades
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
			$MontoTotal = $f['MontoVenta'] * $Cantidad;
			$PrecioUnitFinal = $f['MontoVenta'] / $igvp;
			$MontoTotalFinal = $PrecioUnitFinal * $Cantidad;
			?>
			<tr class="trListaBody" onclick="clk($(this), 'detalle', 'detalle_<?=$id?>');" id="detalle_<?=$id?>">
				<th>
					<input type="hidden" name="detalle_Secuencia[]" id="detalle_Secuencia<?=$id?>" value="0">
					<input type="hidden" name="detalle_TipoDetalle[]" value="<?=$f['TipoDetalle']?>">
					<input type="hidden" name="detalle_CodUnidadEqui[]" id="detalle_CodUnidadEqui<?=$id?>" value="<?=$f['CodUnidadEqui']?>">
					<input type="hidden" name="detalle_CantidadEqui[]" id="detalle_CantidadEqui<?=$id?>" value="<?=$f['CantidadEqui']?>">
					<input type="hidden" name="detalle_CodImpuesto[]" id="detalle_CodImpuesto<?=$id?>" value="<?=$f['CodImpuesto']?>">
					<input type="hidden" name="detalle_FactorImpuesto[]" id="detalle_FactorImpuesto<?=$id?>" value="<?=$f['FactorImpuesto']?>">
					<input type="hidden" name="detalle_MontoVenta[]" id="detalle_MontoVenta<?=$id?>" value="<?=$f['MontoVenta']?>">
					<input type="hidden" name="detalle_MontoVentaUnitario[]" id="detalle_MontoVentaUnitario<?=$id?>" value="<?=$f['MontoVentaUnitario']?>">
					<input type="hidden" name="detalle_PrecioEspecial[]" id="detalle_PrecioEspecial<?=$id?>" value="<?=$f['PrecioEspecial']?>">
					<input type="hidden" name="detalle_PrecioEspecialVta[]" id="detalle_PrecioEspecialVta<?=$id?>" value="<?=$f['PrecioEspecialVta']?>">
					<?=$nro_detalles?>
				</th>
				<td>
					<input type="hidden" name="detalle_CodItem[]" value="<?=$f['CodItem']?>">
					<input type="text" name="detalle_CodInterno[]" value="<?=$f['CodInterno']?>" class="cell2" style="text-align: center;" readonly>
				</td>
				<td>
					<input type="text" name="detalle_Descripcion[]" value="<?=$f['Descripcion']?>" class="cell2" readonly>
				</td>
				<td align="center"><?=$f['TipoDetalle']?></td>
				<td align="center">
					<input type="checkbox" name="detalle_FlagPrecioEspecial[]" value="S" onclick="setFlagPrecioEspecial(this.checked, '<?=$id?>');" />
					<input type="hidden" name="detalle_FlagPrecioEspecial[]" id="detalle_FlagPrecioEspecial<?=$id?>" value="N">
				</td>
				<td>
					<input type="text" name="detalle_StockActual[]" value="<?=number_format($f['StockActual'],5,',','.')?>" class="cell2" style="text-align:right;" readonly>
				</td>
				<td>
					<input type="text" name="detalle_StockActualEqui[]" value="<?=number_format($f['StockActualEqui'],5,',','.')?>" class="cell2" style="text-align:right;" readonly>
				</td>
				<td>
					<select name="detalle_CodUnidad[]" id="detalle_CodUnidad<?=$id?>" class="cell">
						<?=loadSelect2('mastunidades','CodUnidad','CodUnidad',$f['CodUnidad'],1)?>
					</select>
				</td>
				<td>
					<?php if ($f['TipoDetalle'] == 'I') { ?>
						<select name="detalle_CodUnidadVenta[]" id="detalle_CodUnidadVenta<?=$id?>" class="cell" onchange="cambiarUnidad('<?=$id?>');">
							<?=unidades_item($f['CodItem'],$f['CodUnidadComp'],0)?>
						</select>
					<?php } else { ?>
						<select name="detalle_CodUnidadVenta[]" id="detalle_CodUnidadVenta<?=$id?>" class="cell">
							<?=loadSelect2('mastunidades','CodUnidad','CodUnidad',$f['CodUnidadComp'],1)?>
						</select>
					<?php } ?>
				</td>
				<td>
					<input type="text" name="detalle_CantidadPedida[]" value="<?=number_format($Cantidad,5,',','.')?>" class="cell currency5" style="text-align:right;" onchange="setMontosVentas();">
				</td>
				<td>
					<input type="text" name="detalle_PrecioUnit[]" id="detalle_PrecioUnit<?=$id?>" value="<?=number_format($f['MontoVenta'],2,',','.')?>" class="cell currency" style="text-align:right;" onchange="setMontosVentas(true, '<?=$id?>');">
				</td>
				<td>
					<input type="text" name="detalle_MontoTotal[]" value="<?=number_format($MontoTotal,2,',','.')?>" class="cell2 " style="text-align:right;" readonly>
				</td>
				<td align="center">
					<input type="checkbox" name="detalle_FlagExonIva[]" value="S" <?=chkFlag($f['FlagExonIva'])?> onchange="setMontosVentas();" onclick="this.checked=!this.checked">
				</td>
				<td>
					<input type="text" name="detalle_PrecioUnitOriginal[]" id="detalle_PrecioUnitOriginal<?=$id?>" value="<?=number_format($f['MontoVenta'],2,',','.')?>" class="cell2 " style="text-align:right;" readonly>
				</td>
				<td>
					<input type="text" name="detalle_PrecioUnitFinal[]" id="detalle_PrecioUnitFinal<?=$id?>" value="<?=number_format($PrecioUnitFinal,5,',','.')?>" class="cell2 " style="text-align:right;" readonly>
				</td>
				<td>
					<input type="text" name="detalle_MontoTotalFinal[]" id="detalle_MontoTotalFinal<?=$id?>" value="<?=number_format($MontoTotalFinal,2,',','.')?>" class="cell2 " style="text-align:right;" readonly>
				</td>
				<td>
					<input type="hidden" name="detalle_MontoDcto[]" id="detalle_MontoDcto<?=$id?>" value="0" class="">
					<input type="text" name="detalle_PorcentajeDcto[]" id="detalle_PorcentajeDcto<?=$id?>" value="0" class="cell2 " style="text-align:right;" readonly>
				</td>
				<td>
					<input type="text" name="detalle_NroDocumento[]" value="" class="cell2" readonly="readonly">
				</td>
				<td>
					<input type="text" name="detalle_SecDocumento[]" value="" class="cell2" style="text-align:center;" readonly="readonly">
				</td>
				<td>
					<input type="hidden" name="detalle_Estado[]" value="PR">
					<input type="text" value="<?=mb_strtoupper(printValores('cotizacion-estado-detalle','PR'))?>" class="cell2" style="text-align:center;" readonly="readonly">
				</td>
			</tr>
			<?php
		}
	}
}
?>