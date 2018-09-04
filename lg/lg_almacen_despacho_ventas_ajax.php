<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion-".".sql", "w+");
##############################################################################/
if ($modulo == "formulario") {
	//	nuevo
	if ($accion == "nuevo" || $accion == "despacho-ventas") {
		mysql_query("BEGIN");
		##	-----------------
		$FechaDocumento = formatFechaAMD($FechaDocumento);
		$Anio = substr($FechaDocumento, 0, 4);
		$Periodo = substr($FechaDocumento, 0, 7);
		$FlagDocumentoFiscal = 'N';
		$FlagImprimirGuia = (!empty($FlagImprimirGuia)?'S':'N');
		if ($accion == 'despacho-ventas')
		{
			$FlagDocumentoFiscal = 'S';
		}
		##	valido
		if (!trim($CodCentroCosto) || !trim($CodTransaccion) || !trim($CodDocumento) || !trim($CodAlmacen) || !trim($CodDocumentoReferencia) || !trim($NroDocumentoReferencia) || !trim($FechaDocumento)) die("Debe llenar los campos (*) obligatorios.");
		elseif (!periodoAbierto($CodOrganismo, $Periodo)) die("No se puede generar ninguna transacción porque no se ha abierto el periodo $Periodo.");
		elseif (!count($detalle_Secuencia)) die("No se encontraron Items por Despachar");
		##	codigo
		$NroDocumento = codigo('lg_transaccion','NroDocumento',6,['CodOrganismo','CodDocumento'],[$CodOrganismo,$CodDocumento]);
		$NroInterno = codigo('lg_transaccion','NroDocumento',6,['CodOrganismo','CodDocumento','Anio'],[$CodOrganismo,$CodDocumento,$Anio]);
		##	inserto
		$sql = "INSERT INTO lg_transaccion
				SET
					CodOrganismo = '$CodOrganismo',
					CodDocumento = '$CodDocumento',
					NroDocumento = '$NroDocumento',
					NroInterno = '$NroInterno',
					Anio = '$Anio',
					CodTransaccion = '$CodTransaccion',
					FechaDocumento = '$FechaDocumento',
					Periodo = '$Periodo',
					CodAlmacen = '$CodAlmacen',
					CodCentroCosto = '$CodCentroCosto',
					CodDocumentoReferencia = '$CodDocumentoReferencia',
					NroDocumentoReferencia = '$NroDocumentoReferencia',
					IngresadoPor = '$IngresadoPor',
					RecibidoPor = '$RecibidoPor',
					Comentarios = '$Comentarios',
					MotRechazo = '$MotRechazo',
					FlagManual = '$FlagManual',
					FlagPendiente = '$FlagPendiente',
					FlagImprimirGuia = '$FlagImprimirGuia',
					ReferenciaAnio = '$ReferenciaAnio',
					ReferenciaNroDocumento = '$ReferenciaNroDocumento',
					DocumentoReferencia = '$DocumentoReferencia',
					DocumentoReferenciaInterno = '$DocumentoReferenciaInterno',
					NotaEntrega = '$NotaEntrega',
					CodDependencia = '$CodDependencia',
					FlagDocumentoFiscal = '$FlagDocumentoFiscal',
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()";
		execute($sql);
		##	detalle
		$Secuencia = 0;
		for ($i=0; $i < count($detalle_Secuencia); $i++)
		{
			++$Secuencia;
			$detalle_StockActual[$i] = setNumero($detalle_StockActual[$i]);
			$detalle_CantidadPedida[$i] = setNumero($detalle_CantidadPedida[$i]);
			$detalle_CantidadRecibida[$i] = setNumero($detalle_CantidadRecibida[$i]);
			$detalle_PrecioUnit[$i] = setNumero($detalle_PrecioUnit[$i]);
			$detalle_Total[$i] = setNumero($detalle_Total[$i]);
			##	valido
			if ($detalle_CantidadRecibida[$i] <= 0) die('Cantidad a despachar incorrecta');
			elseif ($detalle_CantidadRecibida[$i] > $detalle_StockActual[$i]) die('La cantidad a despachar no se existe en el Stock');
			elseif ($detalle_CantidadRecibida[$i] > $detalle_CantidadPedida[$i]) die('La cantidad a despachar no puede ser mayor a la cantidad del pedido');
			##	insertar
			$sql = "INSERT INTO lg_transacciondetalle
					SET
						CodOrganismo = '$CodOrganismo',
						CodDocumento = '$CodDocumento',
						NroDocumento = '$NroDocumento',
						Secuencia = '$Secuencia',
						CodItem = '$detalle_CodItem[$i]',
						Descripcion = '$detalle_Descripcion[$i]',
						CodUnidad = '$detalle_CodUnidad[$i]',
						CantidadPedida = '$detalle_CantidadPedida[$i]',
						CantidadRecibida = '$detalle_CantidadRecibida[$i]',
						PrecioUnit = '$detalle_PrecioUnit[$i]',
						Total = '$detalle_Total[$i]',
						CodUnidadCompra = '$detalle_CodUnidadCompra[$i]',
						CantidadCompra = '$detalle_CantidadCompra[$i]',
						PrecioUnitCompra = '$detalle_PrecioUnitCompra[$i]',
						ReferenciaAnio = '$detalle_ReferenciaAnio[$i]',
						ReferenciaCodDocumento = '$detalle_ReferenciaCodDocumento[$i]',
						ReferenciaNroDocumento = '$detalle_ReferenciaNroDocumento[$i]',
						ReferenciaSecuencia = '$detalle_ReferenciaSecuencia[$i]',
						ReferenciaNroInterno = '$detalle_ReferenciaNroInterno[$i]',
						CodCentroCosto = '$detalle_CodCentroCosto[$i]',
						Estado = '$Estado',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()";
			execute($sql);
			##	
			if ($accion == 'despacho-ventas')
			{
				$sql = "UPDATE co_documentodet
						SET CantidadEntregada = CantidadEntregada + '$detalle_CantidadRecibida[$i]'
						WHERE
							CodDocumento = '$detalle_ReferenciaNroDocumento[$i]'
							AND Secuencia = '$detalle_ReferenciaSecuencia[$i]'";
				execute($sql);
			}
		}
		if ($accion == 'despacho-ventas')
		{
			$sql = "SELECT *
					FROM co_documentodet dod
					WHERE
						dod.CodDocumento = '$ReferenciaNroDocumento'
						AND dod.TipoDetalle = 'I'
						AND (dod.CantidadPedida - dod.CantidadEntregada) > 0";
			$field_val = getRecords($sql);
			if (!count($field_val)) 
			{
				$sql = "UPDATE co_documento
						SET FlagDespacho = 'N'
						WHERE CodDocumento = '$ReferenciaNroDocumento'";
				execute($sql);
			}
		}
		##	-----------------
		mysql_query("COMMIT");
	}
}
elseif ($modulo == "validar") {
	##	despacho de ventas
	if ($accion == "despacho-ventas") {
		$sql = "SELECT * FROM co_documento WHERE CodDocumento = '$sel_registros'";
		$field = getRecord($sql);
		$Periodo = substr($field['FechaDocumento'],0,7);
		##	
		if ($field['Estado'] != 'CO' && $field['Estado'] != 'FA') 
		{
			$status = 'error';
			$message = 'Este pedido debe ser facturado';
		}
		elseif (!periodoAbierto($field['CodOrganismo'], $Periodo))
		{
			$status = 'error';
			$message = 'No se puede generar ninguna transacción porque no se ha abierto el periodo $Periodo.';

		}
		else 
		{
			$status = 'success';
			$message = '';
		}
		##	
		die(json_encode([
    		'status' => $status,
    		'message' => $message,
    	]));
	}
}
elseif ($modulo == "ajax") {
	if ($accion == "documento_detalle") {
		$i = 0;
		$sql = "SELECT
					dod.*,
					i.CodInterno,
					(dod.CantidadPedida - dod.CantidadEntregada) AS CantidadPendiente,
					i.StockActual,
					i.StockActualEqui
				FROM co_documentodet dod
				INNER JOIN vw_lg_inventarioactual_item i ON i.CodItem = dod.CodItem
				WHERE
					dod.CodDocumento = '$CodDocumento'
					AND dod.TipoDetalle = 'I'
					AND (dod.CantidadPedida - dod.CantidadEntregada) > 0";
		$field_detalle = getRecords($sql);
		foreach($field_detalle as $f) {
			$id = $f['CodDocumento'] . '_' . $f['Secuencia'];
			?>
			<?php if ($f['StockActual'] > 0) { ?> 
				<tr class="trListaBody" onclick="clkMulti($(this), '<?=$id?>');">
			<?php } else { ?>
				<tr class="trListaBody">
			<?php } ?>
				<th>
					<input type="checkbox" name="detalle[]" id="<?=$id?>" value="<?=$id?>" style="display:none" />
					<?=++$i?>
				</th>
				<td align="center"><?=$f['CodInterno']?></td>
				<td><?=htmlentities($f['Descripcion'])?></td>
				<td align="center"><?=$f['CodUnidad']?></td>
				<td align="right"><?=number_format($f['CantidadPedida'],2,',','.')?></td>
				<td align="right"><?=number_format($f['CantidadPendiente'],2,',','.')?></td>
				<td align="right"><?=number_format($f['StockActual'],2,',','.')?></td>
				<td align="right"><?=number_format($f['StockActualEqui'],2,',','.')?></td>
			</tr>
			<?php
		}
	}
}
?>