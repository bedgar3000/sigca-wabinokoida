<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion-".".sql", "w+");
##############################################################################/
if ($modulo == "formulario") {
	//	nuevo
	if ($accion == "nuevo" || $accion == "generar") {
		mysql_query("BEGIN");
		##	-----------------
		$field_tipo_documento = getRecord("SELECT * FROM co_tipodocumento WHERE CodTipoDocumento = '$CodTipoDocumento'");
		##	
		$FechaDocumento = formatFechaAMD($FechaDocumento);
		$FechaVencimiento = formatFechaAMD($FechaVencimiento);
		$MontoAfecto = setNumero($MontoAfecto);
		$MontoNoAfecto = setNumero($MontoNoAfecto);
		$MontoDcto = setNumero($MontoDcto);
		$MontoImpuesto = setNumero($MontoImpuesto);
		$MontoTotal = setNumero($MontoTotal);
		$Anio = substr($FechaDocumento, 0, 4);
		$VoucherPeriodo = substr($FechaDocumento, 0, 7);
		$FlagContabilizacionPendiente = (($_PARAMETRO['CONTONCO'] == 'S') ? 'S' : 'N');
		$FlagContabilizacionPendientePub20 = (($_PARAMETRO['CONTPUB20'] == 'S') ? 'S' : 'N');
		$iCodPersonaVendedor = (!empty($CodPersonaVendedor)?"CodPersonaVendedor = '$CodPersonaVendedor',":'');
		$iCodAlmacen = (!empty($CodAlmacen)?"CodAlmacen = '$CodAlmacen',":'');
		$iFlagCotizacion = (($accion == 'generar')?"FlagCotizacion = 'S',":'');
		##	valido
		if (!trim($CodOrganismo) || !trim($CodEstablecimiento) || !trim($CodPersonaCliente) || !trim($FechaDocumento) || !trim($FechaVencimiento) || !trim($CodCentroCosto) || !trim($CodFormaPago)) die("Debe llenar los campos (*) obligatorios.");
		##	codigo
		$CodDocumento = codigo('co_documento','CodDocumento',10);
		##	inserto
		$sql = "INSERT INTO co_documento
				SET
					CodDocumento = '$CodDocumento',
					CodOrganismo = '$CodOrganismo',
					CodTipoDocumento = '$CodTipoDocumento',
					CodEstablecimiento = '$CodEstablecimiento',
					CodCentroCosto = '$CodCentroCosto',
					FormaFactura = '$FormaFactura',
					CodPersonaCliente = '$CodPersonaCliente',
					DocFiscalCliente = '$DocFiscalCliente',
					NombreCliente = '$NombreCliente',
					DireccionCliente = '$DireccionCliente',
					CodPersonaCobrar = '$CodPersonaCliente',
					FechaDocumento = '$FechaDocumento',
					FechaVencimiento = '$FechaVencimiento',
					Anio = '$Anio',
					TipoVenta = '$TipoVenta',
					CodFormaPago = '$CodFormaPago',
					$iCodPersonaVendedor
					MonedaDocumento = '$MonedaDocumento',
					MontoAfecto = '$MontoAfecto',
					MontoNoAfecto = '$MontoNoAfecto',
					MontoDcto = '$MontoDcto',
					MontoImpuesto = '$MontoImpuesto',
					MontoTotal = '$MontoTotal',
					$iCodAlmacen
					Comentarios = '$Comentarios',
					CodRutaDespacho = '$CodRutaDespacho',
					PreparadoPor = '$PreparadoPor',
					FechaPreparado = '$FechaPreparado',
					$iFlagCotizacion
					VoucherPeriodo = '$VoucherPeriodo',
					FlagContabilizacionPendiente = '$FlagContabilizacionPendiente',
					FlagContabilizacionPendientePub20 = '$FlagContabilizacionPendientePub20',
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
			$detalle_PrecioUnitFinal[$i] = setNumero($detalle_PrecioUnitFinal[$i]);
			$detalle_MontoTotal[$i] = setNumero($detalle_MontoTotal[$i]);
			$detalle_MontoTotalFinal[$i] = setNumero($detalle_MontoTotalFinal[$i]);
			$detalle_PrecioUnitOriginal[$i] = setNumero($detalle_PrecioUnitOriginal[$i]);
			$detalle_PorcentajeDcto1[$i] = setNumero($detalle_PorcentajeDcto1[$i]);
			$detalle_PorcentajeDcto2[$i] = setNumero($detalle_PorcentajeDcto2[$i]);
			$detalle_PorcentajeDcto3[$i] = setNumero($detalle_PorcentajeDcto3[$i]);
			$detalle_MontoDcto[$i] = setNumero($detalle_MontoDcto[$i]);
			$detalle_FlagExonIva[$i] = (!empty($detalle_FlagExonIva[$i])?'S':'N');
			if (floatval($detalle_PrecioUnit[$i]) <> floatval($detalle_PrecioUnitOriginal[$i]))
				$FlagPrecioModificado = 'S';
			else
				$FlagPrecioModificado = 'N';
			##	valido
			if (!trim($detalle_CantidadPedida[$i])) die("La Cantidad Pedida no puede ser cero.");
			elseif (!trim($detalle_PrecioUnit[$i])) die("El Precio Unitario no puede ser cero.");
			elseif ($detalle_TipoDetalle[$i] == 'I') {
				$sql = "SELECT * FROM vw_lg_inventarioactual_item WHERE CodItem = '$detalle_CodItem[$i]'";
				$field_inv = getRecord($sql);
				##	
				if ($detalle_CodUnidadVenta[$i] == $detalle_CodUnidad[$i]) $TransaccionCantidad = $detalle_CantidadPedida[$i];
				else $TransaccionCantidad = $detalle_CantidadPedida[$i] * $field_inv['CantidadEqui'];
				##	
				if ($TransaccionCantidad > $field_inv['StockActual']) die("El item <strong>$detalle_CodInterno[$i] - $detalle_Descripcion[$i]</strong> no tiene Stock para cubrir la Cantidad Pedida");
			}
			##	inserto
			$sql = "INSERT INTO co_documentodet
					SET
						CodDocumento = '$CodDocumento',
						Secuencia = '$Secuencia',
						CodCentroCosto = '$CodCentroCosto',
						$iCodAlmacen
						TipoDetalle = '$detalle_TipoDetalle[$i]',
						CodItem = '$detalle_CodItem[$i]',
						Descripcion = '$detalle_Descripcion[$i]',
						CodUnidad = '$detalle_CodUnidad[$i]',
						CodUnidadVenta = '$detalle_CodUnidadVenta[$i]',
						CantidadPedida = '$detalle_CantidadPedida[$i]',
						PrecioUnit = '$detalle_PrecioUnit[$i]',
						PrecioUnitFinal = '$detalle_PrecioUnitFinal[$i]',
						MontoTotal = '$detalle_MontoTotal[$i]',
						MontoTotalFinal = '$detalle_MontoTotalFinal[$i]',
						PrecioUnitOriginal = '$detalle_PrecioUnitOriginal[$i]',
						FlagExonIva = '$detalle_FlagExonIva[$i]',
						PorcentajeDcto1 = '$detalle_PorcentajeDcto[$i]',
						MontoDcto = '$detalle_MontoDcto[$i]',
						FlagPrecioModificado = '$FlagPrecioModificado',
						Estado = '$detalle_Estado[$i]',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()";
			execute($sql);
			if ($accion == 'generar')
			{
				##	detalle
				$sql = "UPDATE co_cotizaciondet
						SET
							Estado = 'CO',
							CodDocumento = '$CodDocumento',
							SecDocumento = '$Secuencia',
							UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
							UltimaFecha = NOW()
						WHERE
							CodCotizacion = '$detalle_CodCotizacion[$i]'
							AND Secuencia = '$detalle_Secuencia[$i]'";
				execute($sql);
				##	
				$sql = "SELECT * 
						FROM co_cotizaciondet 
						WHERE 
							CodCotizacion = '$detalle_CodCotizacion[$i]' 
							AND Estado <> 'CO'";
				$field_estado = getRecords($sql);
				if (!count($field_estado))
				{
					$sql = "UPDATE co_cotizacion
							SET
								Estado = 'CO',
								UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
								UltimaFecha = NOW()
							WHERE CodCotizacion = '$detalle_CodCotizacion[$i]'";
					execute($sql);
				}
			}
		}
		##	impuesto
		$sql = "SELECT * FROM mastimpuestos WHERE CodImpuesto = '$_PARAMETRO[COIVA]'";
		$field_igv = getRecord($sql);
		if ($MontoImpuesto > 0) {
			$sql = "INSERT INTO co_documentoimpuesto
					SET
						CodDocumento = '$CodDocumento',
						Secuencia = '1',
						CodImpuesto = '$field_igv[CodImpuesto]',
						CodRegimenFiscal = '$field_igv[CodRegimenFiscal]',
						Porcentaje = '$field_igv[FactorPorcentaje]',
						Monto = '$MontoImpuesto',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()";
			execute($sql);
		}
		if ($_PARAMETRO['PEDAAUTOAP'] == 'S') 
		{
			$_POST['CodTipoDocumento'] = $CodTipoDocumento;
			$_POST['CodDocumento'] = $CodDocumento;
			$_POST['Estado'] = 'AP';
			$_POST['AprobadoPor'] = $_SESSION["CODPERSONA_ACTUAL"];
			$_POST['FechaAprobado'] = $Ahora;
			$NroDocumento = pedidos_aprobar();
			##	
			$message = "|Se ha generado el documento <strong>$field_tipo_documento[Descripcion]</strong> Nro. <strong>$CodTipoDocumento-$NroDocumento</strong>";
		} else $message = "|";
		##	-----------------
		mysql_query("COMMIT");
		##	
		die($message);
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
		$iCodPersonaVendedor = (!empty($CodPersonaVendedor)?"CodPersonaVendedor = '$CodPersonaVendedor',":'');
		$iCodAlmacen = (!empty($CodAlmacen)?"CodAlmacen = '$CodAlmacen',":'');
		##	valido
		if (!trim($CodEstablecimiento) || !trim($FechaDocumento) || !trim($FechaVencimiento) || !trim($CodCentroCosto) || !trim($CodFormaPago)) die("Debe llenar los campos (*) obligatorios.");
		##	actualizo
		$sql = "UPDATE co_documento
				SET
					CodEstablecimiento = '$CodEstablecimiento',
					CodCentroCosto = '$CodCentroCosto',
					FechaDocumento = '$FechaDocumento',
					FechaVencimiento = '$FechaVencimiento',
					Anio = '$Anio',
					$iCodPersonaVendedor
					MonedaDocumento = '$MonedaDocumento',
					MontoAfecto = '$MontoAfecto',
					MontoNoAfecto = '$MontoNoAfecto',
					MontoDcto = '$MontoDcto',
					MontoImpuesto = '$MontoImpuesto',
					MontoTotal = '$MontoTotal',
					$iCodAlmacen
					Comentarios = '$Comentarios',
					CodRutaDespacho = '$CodRutaDespacho',
					PreparadoPor = '$PreparadoPor',
					FechaPreparado = '$FechaPreparado',
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodDocumento = '$CodDocumento'";
		execute($sql);
		##	detalle
		if (count($detalle_Secuencia))
		{
			$sql = "DELETE FROM co_documentodet
					WHERE
						CodDocumento = '$CodDocumento'
						AND Secuencia NOT IN (".implode(",",$detalle_Secuencia).")";
		}
		else
		{
			$sql = "DELETE FROM co_documentodet WHERE CodDocumento = '$CodDocumento'";
		}
		execute($sql);
		$Secuencia = 0;
		for ($i=0; $i < count($detalle_Secuencia); $i++)
		{
			if (!$detalle_Secuencia[$i]) 
				$detalle_Secuencia[$i] = codigo('co_documentodet','Secuencia',11,['CodDocumento'],[$CodDocumento]);
			$detalle_CantidadPedida[$i] = setNumero($detalle_CantidadPedida[$i]);
			$detalle_PrecioUnit[$i] = setNumero($detalle_PrecioUnit[$i]);
			$detalle_PrecioUnitFinal[$i] = setNumero($detalle_PrecioUnitFinal[$i]);
			$detalle_MontoTotal[$i] = setNumero($detalle_MontoTotal[$i]);
			$detalle_MontoTotalFinal[$i] = setNumero($detalle_MontoTotalFinal[$i]);
			$detalle_PrecioUnitOriginal[$i] = setNumero($detalle_PrecioUnitOriginal[$i]);
			$detalle_PorcentajeDcto1[$i] = setNumero($detalle_PorcentajeDcto1[$i]);
			$detalle_PorcentajeDcto2[$i] = setNumero($detalle_PorcentajeDcto2[$i]);
			$detalle_PorcentajeDcto3[$i] = setNumero($detalle_PorcentajeDcto3[$i]);
			$detalle_MontoDcto[$i] = setNumero($detalle_MontoDcto[$i]);
			$detalle_FlagExonIva[$i] = (!empty($detalle_FlagExonIva[$i])?'S':'N');
			if (floatval($detalle_PrecioUnit[$i]) <> floatval($detalle_PrecioUnitOriginal[$i]))
				$FlagPrecioModificado = 'S';
			else
				$FlagPrecioModificado = 'N';
			##	valido
			if (!trim($detalle_CantidadPedida[$i])) die("La Cantidad Pedida no puede ser cero.");
			elseif (!trim($detalle_PrecioUnit[$i])) die("El Precio Unitario no puede ser cero.");
			##	inserto
			$sql = "REPLACE INTO co_documentodet
					SET
						CodDocumento = '$CodDocumento',
						Secuencia = '$detalle_Secuencia[$i]',
						CodCentroCosto = '$CodCentroCosto',
						$iCodAlmacen
						TipoDetalle = '$detalle_TipoDetalle[$i]',
						CodItem = '$detalle_CodItem[$i]',
						Descripcion = '$detalle_Descripcion[$i]',
						CodUnidad = '$detalle_CodUnidad[$i]',
						CodUnidadVenta = '$detalle_CodUnidadVenta[$i]',
						CantidadPedida = '$detalle_CantidadPedida[$i]',
						PrecioUnit = '$detalle_PrecioUnit[$i]',
						PrecioUnitFinal = '$detalle_PrecioUnitFinal[$i]',
						MontoTotal = '$detalle_MontoTotal[$i]',
						MontoTotalFinal = '$detalle_MontoTotalFinal[$i]',
						PrecioUnitOriginal = '$detalle_PrecioUnitOriginal[$i]',
						FlagExonIva = '$detalle_FlagExonIva[$i]',
						PorcentajeDcto1 = '$detalle_PorcentajeDcto[$i]',
						MontoDcto = '$detalle_MontoDcto[$i]',
						FlagPrecioModificado = '$FlagPrecioModificado',
						Estado = '$detalle_Estado[$i]',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	impuesto
		$sql = "SELECT * FROM mastimpuestos WHERE CodImpuesto = '$_PARAMETRO[COIVA]'";
		$field_igv = getRecord($sql);
		if ($MontoImpuesto > 0) {
			$sql = "INSERT INTO co_documentoimpuesto
					SET
						CodDocumento = '$CodDocumento',
						Secuencia = '1',
						CodImpuesto = '$field_igv[CodImpuesto]',
						CodRegimenFiscal = '$field_igv[CodRegimenFiscal]',
						Porcentaje = '$field_igv[FactorPorcentaje]',
						Monto = '$MontoImpuesto',
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
		pedidos_aprobar();
		##	-----------------
		mysql_query("COMMIT");
	}
	//	anular
	elseif ($accion == "anular") {
		mysql_query("BEGIN");
		##	-----------------
		$field_documento = getRecord("SELECT * FROM co_documento WHERE CodDocumento = '$CodDocumento'");
		##	
		if ($field_documento['Estado'] == 'AP') $NuevoEstado = 'PR';
		elseif ($field_documento['Estado'] == 'PR') $NuevoEstado = 'AN';
		else die('No puede anular un pedido <strong>'.printValores('documento1-estado',$field_documento['Estado']).'</strong>');
		##	actualizo
		$sql = "UPDATE co_documento
				SET
					Estado = '$NuevoEstado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodDocumento = '$CodDocumento'";
		execute($sql);
		##	detalle
		$sql = "UPDATE co_documentodet
				SET
					Estado = '$NuevoEstado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodDocumento = '$CodDocumento'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	facturar
	elseif ($accion == "facturar") {
		mysql_query("BEGIN");
		##	-----------------
		$message = '';
		$field_tipo_documento = getRecord("SELECT * FROM co_tipodocumento WHERE CodTipoDocumento = '$CodTipoDocumento'");
		$field_documento = getRecord("SELECT * FROM co_documento WHERE CodDocumento = '$CodDocumento'");
		$field_documentodet = getRecords("SELECT * FROM co_documentodet WHERE CodDocumento = '$CodDocumento' ORDER BY Secuencia");
		$field_documento_items = getRecords("SELECT * FROM co_documentodet WHERE CodDocumento = '$CodDocumento' AND TipoDetalle  = 'I' ORDER BY Secuencia");
		##	
		$FechaDocumento = formatFechaAMD($FechaDocumento);
		$FechaVencimiento = formatFechaAMD($FechaVencimiento);
		$MontoAfecto = setNumero($MontoAfecto);
		$MontoNoAfecto = setNumero($MontoNoAfecto);
		$MontoDcto = setNumero($MontoDcto);
		$MontoImpuesto = setNumero($MontoImpuesto);
		$MontoTotal = setNumero($MontoTotal);
		$Anio = substr($FechaDocumento, 0, 4);
		$VoucherPeriodo = substr($FechaDocumento, 0, 7);
		$FlagContabilizacionPendiente = (($_PARAMETRO['CONTONCO'] == 'S') ? 'S' : 'N');
		$FlagContabilizacionPendientePub20 = (($_PARAMETRO['CONTPUB20'] == 'S') ? 'S' : 'N');
		$iCodPersonaVendedor = (!empty($CodPersonaVendedor)?"CodPersonaVendedor = '$CodPersonaVendedor',":'');
		$iCodAlmacen = (!empty($CodAlmacen)?"CodAlmacen = '$CodAlmacen',":"CodAlmacen = NULL,");
		$iCodCotizacion = (!empty($field_documento['CodCotizacion'])?"CodCotizacion = '$field_documento[CodCotizacion]',":"CodCotizacion = NULL,");
		##	-----------------
		##	DOCUMENTO (FACTURACION)
		##	codigo
		$CodDocumento = codigo('co_documento','CodDocumento',10);
		list($NroDocumento, $UltNroEmitido) = correlativo_documento($field_documento['CodOrganismo'], $CodTipoDocumento, $NroSerie);
		##	correlativo
		correlativo_documento_update($UltNroEmitido, $field_documento['CodOrganismo'], $CodTipoDocumento, $NroSerie);
		##	inserto
		$sql = "INSERT INTO co_documento
				SET
					CodDocumento = '$CodDocumento',
					CodOrganismo = '$field_documento[CodOrganismo]',
					CodTipoDocumento = '$CodTipoDocumento',
					NroDocumento = '$NroDocumento',
					CodEstablecimiento = '$field_documento[CodEstablecimiento]',
					CodCentroCosto = '$field_documento[CodCentroCosto]',
					FormaFactura = '$FormaFactura',
					CodPersonaCliente = '$CodPersonaCliente',
					DocFiscalCliente = '$DocFiscalCliente',
					NombreCliente = '$NombreCliente',
					DireccionCliente = '$DireccionCliente',
					CodPersonaCobrar = '$CodPersonaCliente',
					FechaDocumento = '$FechaDocumento',
					FechaVencimiento = '$FechaVencimiento',
					Anio = '$Anio',
					TipoVenta = '$TipoVenta',
					CodFormaPago = '$CodFormaPago',
					$iCodPersonaVendedor
					MonedaDocumento = '$MonedaDocumento',
					MontoAfecto = '$MontoAfecto',
					MontoNoAfecto = '$MontoNoAfecto',
					MontoDcto = '$MontoDcto',
					MontoImpuesto = '$MontoImpuesto',
					MontoTotal = '$MontoTotal',
					$iCodAlmacen
					Comentarios = '$Comentarios',
					CodRutaDespacho = '$CodRutaDespacho',
					CodPedido = '$field_documento[CodDocumento]',
					$iCodCotizacion
					ComercialNroPedido = '$field_documento[CodTipoDocumento]$field_documento[NroDocumento]',
					ComercialFechaReq = '$field_documento[FechaVencimiento]',
					PreparadoPor = '$_SESSION[CODPERSONA_ACTUAL]',
					FechaPreparado = NOW(),
					AprobadoPor = '$_SESSION[CODPERSONA_ACTUAL]',
					FechaAprobado = NOW(),
					FlagCotizacion = '$field_documento[FlagCotizacion]',
					FlagPedido = 'S',
					FlagDocumento = 'S',
					VoucherPeriodo = '$VoucherPeriodo',
					FlagContabilizacionPendiente = '$FlagContabilizacionPendiente',
					FlagContabilizacionPendientePub20 = '$FlagContabilizacionPendientePub20',
					Estado = 'FA',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()";
		execute($sql);
		##	
		$message .= "|Se ha generado el documento <strong>$field_tipo_documento[Descripcion]</strong> Nro. <strong>$CodTipoDocumento-$NroDocumento</strong>";
		##	detalle
		$Secuencia = 0;
		for ($i=0; $i < count($detalle_Secuencia); $i++)
		{
			$detalle_CantidadPedida[$i] = setNumero($detalle_CantidadPedida[$i]);
			$detalle_PrecioUnit[$i] = setNumero($detalle_PrecioUnit[$i]);
			$detalle_PrecioUnitFinal[$i] = setNumero($detalle_PrecioUnitFinal[$i]);
			$detalle_MontoTotal[$i] = setNumero($detalle_MontoTotal[$i]);
			$detalle_MontoTotalFinal[$i] = setNumero($detalle_MontoTotalFinal[$i]);
			$detalle_PrecioUnitOriginal[$i] = setNumero($detalle_PrecioUnitOriginal[$i]);
			$detalle_PorcentajeDcto[$i] = setNumero($detalle_PorcentajeDcto[$i]);
			$detalle_MontoDcto[$i] = setNumero($detalle_MontoDcto[$i]);
			$detalle_FlagExonIva[$i] = (!empty($detalle_FlagExonIva[$i])?'S':'N');
			if (floatval($detalle_PrecioUnit[$i]) <> floatval($detalle_PrecioUnitOriginal[$i]))
				$FlagPrecioModificado = 'S';
			else
				$FlagPrecioModificado = 'N';
			if ($detalle_Secuencia[$i]) 
			{
				$Secuencia = $detalle_Secuencia[$i];
				$DocRelSecuencia = $Secuencia;
			}
			else
			{
				++$Secuencia;
				$DocRelSecuencia = NULL;
			}
			$DocRelNro = $CodDocumento;
			$iDocRelSecuencia = (!empty($DocRelSecuencia)?"DocRelSecuencia = '$DocRelSecuencia',":'');
			##	valido
			if (!trim($detalle_CantidadPedida[$i])) die("La Cantidad Pedida no puede ser cero.");
			elseif (!trim($detalle_PrecioUnit[$i])) die("El Precio Unitario no puede ser cero.");
			elseif ($detalle_TipoDetalle[$i] == 'I') {
				$sql = "SELECT * FROM lg_itemalmaceninv WHERE CodItem = '$detalle_CodItem[$i]'";
				$field_inv = getRecord($sql);
				if ($field_inv['StockActual'] < $detalle_CantidadPedida[$i]) die("El item <strong>$detalle_CodInterno[$i] - $detalle_Descripcion[$i]</strong> no tiene Stock para cubrir la Cantidad Pedida");
			}
			##	inserto
			$sql = "INSERT INTO co_documentodet
					SET
						CodDocumento = '$CodDocumento',
						Secuencia = '$Secuencia',
						CodCentroCosto = '$field_documento[CodCentroCosto]',
						$iCodAlmacen
						TipoDetalle = '$detalle_TipoDetalle[$i]',
						CodItem = '$detalle_CodItem[$i]',
						Descripcion = '$detalle_Descripcion[$i]',
						CodUnidad = '$detalle_CodUnidad[$i]',
						CodUnidadVenta = '$detalle_CodUnidadVenta[$i]',
						CantidadPedida = '$detalle_CantidadPedida[$i]',
						PrecioUnit = '$detalle_PrecioUnit[$i]',
						PrecioUnitFinal = '$detalle_PrecioUnitFinal[$i]',
						MontoTotal = '$detalle_MontoTotal[$i]',
						MontoTotalFinal = '$detalle_MontoTotalFinal[$i]',
						PrecioUnitOriginal = '$detalle_PrecioUnitOriginal[$i]',
						FlagExonIva = '$detalle_FlagExonIva[$i]',
						PorcentajeDcto1 = '$detalle_PorcentajeDcto[$i]',
						MontoDcto = '$detalle_MontoDcto[$i]',
						DocRelNro = '$DocRelNro',
						FlagPrecioModificado = '$FlagPrecioModificado',
						$iDocRelSecuencia
						Estado = 'PR',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	impuesto
		$sql = "SELECT * FROM mastimpuestos WHERE CodImpuesto = '$_PARAMETRO[COIVA]'";
		$field_igv = getRecord($sql);
		if ($MontoImpuesto > 0) {
			$sql = "INSERT INTO co_documentoimpuesto
					SET
						CodDocumento = '$CodDocumento',
						Secuencia = '1',
						CodImpuesto = '$field_igv[CodImpuesto]',
						CodRegimenFiscal = '$field_igv[CodRegimenFiscal]',
						Porcentaje = '$field_igv[FactorPorcentaje]',
						Monto = '$MontoImpuesto',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	TRANSACCION
		if ($_PARAMETRO['COVTADESP'] == 'S')
		{
			##	genero el nuevo codigo
			$TransaccionNroDocumento = codigo('lg_transaccion','NroDocumento',6,['CodOrganismo','CodDocumento'],[$field_documento['CodOrganismo'],'NS']);
			$TransaccionNroInterno = codigo('lg_transaccion','NroDocumento',6,['Anio','CodOrganismo','CodDocumento'],[$field_documento['Anio'],$field_documento['CodOrganismo'],'NS']);
			##	inserto
			$sql = "INSERT INTO lg_transaccion
					SET
						CodOrganismo = '$field_documento[CodOrganismo]',
						CodDocumento = 'NS',
						NroDocumento = '$TransaccionNroDocumento',
						NroInterno = '$TransaccionNroInterno',
						CodTransaccion = '$_PARAMETRO[COTIPOTR]',
						FechaDocumento = '$FechaActual',
						Periodo = '$PeriodoActual',
						CodAlmacen = '$CodAlmacen',
						CodCentroCosto = '$CodCentroCosto',
						CodDocumentoReferencia = '$CodTipoDocumento',
						NroDocumentoReferencia = '$NroDocumento',
						IngresadoPor = '$_SESSION[CODPERSONA_ACTUAL]',
						RecibidoPor = '$_SESSION[CODPERSONA_ACTUAL]',
						EjecutadoPor = '$_SESSION[CODPERSONA_ACTUAL]',
						FechaEjecucion = NOW(),
						Comentarios = '$Comentarios',
						FlagManual = 'N',
						FlagPendiente = 'S',
						ReferenciaAnio = '$Anio',
						ReferenciaNroDocumento = '$CodDocumento',
						DocumentoReferencia = '$NroDocumento',
						DocumentoReferenciaInterno = '$NroDocumento',
						CodDependencia = '$_PARAMETRO[CODEPVTA]',
						Anio = '$Anio',
						FlagDocumentoFiscal = 'S',
						Estado = 'CO',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()";
			execute($sql);
			##	
			$message .= "<br>Se ha generado la transación Nro. <strong>NS-$TransaccionNroInterno</strong>";
			##	detalle
			$_Secuencia = 0;
			foreach ($field_documento_items as $row)
			{
				++$_Secuencia;
				##	
				$sql = "SELECT * FROM vw_lg_inventarioactual_item WHERE CodItem = '$row[CodItem]'";
				$field_item = getRecord($sql);
				##	
				if ($row['CodUnidadVenta'] == $row['CodUnidad']) $TransaccionCantidad = $row['CantidadPedida'];
				else $TransaccionCantidad = $row['CantidadPedida'] * $field_item['CantidadEqui'];
				##	
				if ($TransaccionCantidad > $field_item['StockActual']) die("El item <strong>$field_item[CodInterno] - $row[Descripcion]</strong> no tiene Stock para cubrir la Cantidad Pedida");
				##	inserto
				$sql = "INSERT INTO lg_transacciondetalle
						SET
							CodOrganismo = '$field_documento[CodOrganismo]',
							CodDocumento = 'NS',
							NroDocumento = '$TransaccionNroDocumento',
							Secuencia = '$_Secuencia',
							CodItem = '$row[CodItem]',
							Descripcion = '$row[Descripcion]',
							CodUnidad = '$row[CodUnidad]',
							CantidadPedida = '$TransaccionCantidad',
							CantidadRecibida = '$TransaccionCantidad',
							PrecioUnit = 0.00,
							Total = 0.00,
							ReferenciaAnio = '$Anio',
							ReferenciaCodDocumento = '$CodTipoDocumento',
							ReferenciaNroDocumento = '$CodDocumento',
							ReferenciaNroInterno = '$NroDocumento',
							ReferenciaSecuencia = '$row[Secuencia]',
							CodCentroCosto = '$CodCentroCosto',
							Estado = 'CO',
							UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
							UltimaFecha = NOW()";
				execute($sql);
			}
			##	actualizo documento
			$sql = "UPDATE co_documento
					SET FlagDespacho = 'N'
					WHERE
						CodDocumento = '$CodDocumento'
						OR CodDocumento = '$field_documento[CodDocumento]'";
			execute($sql);
		}
		else
		{
			##	actualizo documento
			$sql = "UPDATE co_documento
					SET FlagDespacho = 'N'
					WHERE CodDocumento = '$field_documento[CodDocumento]'";
			execute($sql);
		}
		##	COBRANZA
		if ($_PARAMETRO['COBRANZAVENTA'] == 'S' && $FlagCredito != 'S')
		{
			$icobranza_CodPersonaCajero = (!empty($cobranza_CodPersonaCajero)?"CodPersonaCajero = '$CodPersonaCajero',":"CodPersonaCajero = NULL,");
			$icobranza_CodPersonaCobrador = (!empty($cobranza_CodPersonaCobrador)?"CodPersonaCobrador = '$CodPersonaCobrador',":"CodPersonaCobrador = NULL,");
			$cobranza_MontoEfectivo = setNumero($cobranza_MontoEfectivo);
			$cobranza_Vuelto = abs(setNumero($cobranza_Vuelto)) * -1;
			##	actualizo monto pagado
			$sql = "UPDATE co_documento
					SET
						MontoPagado = '$MontoTotal',
						Estado = 'CO'
					WHERE CodDocumento = '$CodDocumento'";
			execute($sql);
			##	codigo
			$CodCobranza = codigo('co_cobranza','CodCobranza',10);
			$NroCobranza = codigo('co_cobranza','NroCobranza',5,['CodOrganismo'],[$field_documento['CodOrganismo']]);
			##	inserto
			$sql = "INSERT INTO co_cobranza
					SET
						CodCobranza = '$CodCobranza',
						NroCobranza = '$NroCobranza',
						CodOrganismo = '$field_documento[CodOrganismo]',
						FechaCobranza = '$FechaActual',
						CodPersonaCliente = '$field_documento[CodPersonaCliente]',
						$icobranza_CodPersonaCajero
						$icobranza_CodPersonaCobrador
						PreparadoPor = '$_SESSION[CODPERSONA_ACTUAL]',
						FechaPreparado = NOW(),
						Estado = 'AP',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()";
			execute($sql);
			##	
			$message .= "<br>Se ha generado la cobranza Nro. <strong>$NroCobranza</strong>";
			##	
			$Secuencia = 0;
			$MontoCobranza = 0;
			if ($cobranza_MontoEfectivo)
			{
				++$Secuencia;
				$sql = "INSERT INTO co_cobranzadet
						SET
							CodCobranza = '$CodCobranza',
							Secuencia = '$Secuencia',
							CodTipoPago = 'EF',
							MontoLocal = '$cobranza_MontoEfectivo',
							Estado = 'AP',
							UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
							UltimaFecha = NOW()";
				execute($sql);
				$MontoCobranza += $cobranza_MontoEfectivo;
				if (abs($cobranza_Vuelto) > 0)
				{	
					++$Secuencia;
					$sql = "INSERT INTO co_cobranzadet
							SET
								CodCobranza = '$CodCobranza',
								Secuencia = '$Secuencia',
								CodTipoPago = 'EF',
								MontoLocal = '$cobranza_Vuelto',
								Estado = 'AP',
								UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
								UltimaFecha = NOW()";
					execute($sql);
				}
				$MontoCobranza += $cobranza_Vuelto;
			}
			##	detalle
			for ($i=0; $i < count($cobranza_Secuencia); $i++)
			{
				++$Secuencia;
				##	
				$cobranza_MontoLocal[$i] = setNumero($cobranza_MontoLocal[$i]);
				$iCodTipoTarjeta = (!empty($cobranza_CodTipoTarjeta[$i])?"CodTipoTarjeta = '$cobranza_CodTipoTarjeta[$i]',":"CodTipoTarjeta = NULL,");
				$iCodBanco = (!empty($cobranza_CodBanco[$i])?"CodBanco = '$cobranza_CodBanco[$i]',":"CodBanco = NULL,");
				if ($cobranza_CodTipoPago[$i] == 'TC' || $cobranza_CodTipoPago[$i] == 'TD' || $cobranza_CodTipoPago[$i] == 'TR')
				{
					$cobranza_DocReferencia[$i] = "$cobranza_CodTipoPago[$i]-$cobranza_CtaBancaria[$i]";
				}
				##	valido
				if (!trim($cobranza_MontoLocal[$i])) die("El monto a pagar no puede ser cero.");
				##	inserto
				$sql = "INSERT INTO co_cobranzadet
						SET
							CodCobranza = '$CodCobranza',
							Secuencia = '$Secuencia',
							CodTipoPago = '$cobranza_CodTipoPago[$i]',
							$iCodTipoTarjeta
							$iCodBanco
							CtaBancaria = '$cobranza_CtaBancaria[$i]',
							DocReferencia = '$cobranza_DocReferencia[$i]',
							MontoLocal = '$cobranza_MontoLocal[$i]',
							Estado = 'AP',
							UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
							UltimaFecha = NOW()";
				execute($sql);
				##	
				$MontoCobranza += $cobranza_MontoLocal[$i];
			}
			##	inserto
			$sql = "INSERT INTO co_documentocobranza
					SET
						CodCobranza = '$CodCobranza',
						CodDocumento = '$CodDocumento',
						MontoPagado = '$MontoCobranza',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	DOCUMENTO (PEDIDO)
		##	actualizo
		$sql = "UPDATE co_documento
				SET
					ComercialNroPedido = '$CodTipoDocumento$NroDocumento',
					ComercialFechaReq = '$FechaVencimiento',
					Estado = 'FA',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodDocumento = '$field_documento[CodDocumento]'";
		execute($sql);
		##	detalle
		$sql = "UPDATE co_documentodet
				SET
					DocRelNro = '$CodDocumento',
					DocRelSecuencia = Secuencia,
					Estado = 'FA',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodDocumento = '$field_documento[CodDocumento]'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
		##	
		die($message.'|'.$CodDocumento);
	}
}
elseif ($modulo == "validar") {
	//	modificar
	if($accion == "modificar") {
		$sql = "SELECT Estado FROM co_documento WHERE CodDocumento = '$codigo'";
		$Estado = getVar3($sql);
		if ($Estado != 'PR') die('No puede modificar un pedido <strong>'.printValores('documento1-estado',$Estado).'</strong>');
	}
	//	aprobar
	elseif($accion == "aprobar") {
		$sql = "SELECT Estado FROM co_documento WHERE CodDocumento = '$codigo'";
		$Estado = getVar3($sql);
		if ($Estado != 'PR') die('No puede aprobar un pedido <strong>'.printValores('documento1-estado',$Estado).'</strong>');
	}
	//	facturar
	elseif($accion == "facturar") {
		$sql = "SELECT Estado FROM co_documento WHERE CodDocumento = '$codigo'";
		$Estado = getVar3($sql);
		if ($Estado != 'AP') die('No puede facturar un pedido <strong>'.printValores('documento1-estado',$Estado).'</strong>');
	}
	//	anular
	elseif($accion == "anular") {
		$sql = "SELECT Estado FROM co_documento WHERE CodDocumento = '$codigo'";
		$Estado = getVar3($sql);
		if ($Estado != 'PR' && $Estado != 'AP') die('No puede anular un pedido <strong>'.printValores('documento1-estado',$Estado).'</strong>');
	}
}
elseif ($modulo == "ajax") {
	if ($accion == "detalle_insertar") {
		$id = $nro_detalles;
		$Cantidad = setNumero($Cantidad);
		if (!empty($CodItem))
		{
			$sql = "SELECT
						i.*,
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
						*,
						'S' AS TipoDetalle,
						CodServicio AS CodItem,
						'UNI' AS CodUnidad,
						'UNI' AS CodUnidadComp,
						'0' AS StockActual,
						'0' AS StockActualEqui,
						FlagExoneradoIva AS FlagExonIva,
						'1' AS Unidades
					FROM co_mastservicios
					WHERE CodServicio = '$CodServicio'";
		}
		##	
		$field = getRecords($sql);
		foreach ($field as $f)
		{
			$MontoTotal = $f['PrecioVenta'] * $Cantidad;
			$PrecioUnitFinal = $f['PrecioVenta'] / $igvp;
			$MontoTotalFinal = $PrecioUnitFinal * $Cantidad;
			?>
			<tr class="trListaBody" onclick="clk($(this), 'detalle', 'detalle_<?=$id?>');" id="detalle_<?=$id?>">
				<th>
					<input type="hidden" name="detalle_Secuencia[]" id="detalle_Secuencia<?=$id?>" value="0">
					<input type="hidden" name="detalle_TipoDetalle[]" value="<?=$f['TipoDetalle']?>">
					<input type="hidden" name="detalle_CodCotizacion[]" value="">
					<input type="hidden" name="detalle_CodUnidadEqui[]" id="detalle_CodUnidadEqui<?=$id?>" value="<?=$f['CodUnidadEqui']?>">
					<input type="hidden" name="detalle_CantidadEqui[]" id="detalle_CantidadEqui<?=$id?>" value="<?=$f['CantidadEqui']?>">
					<input type="hidden" name="detalle_CodImpuesto[]" id="detalle_CodImpuesto<?=$id?>" value="<?=$f['CodImpuesto']?>">
					<input type="hidden" name="detalle_FactorImpuesto[]" id="detalle_FactorImpuesto<?=$id?>" value="<?=$f['FactorImpuesto']?>">
					<input type="hidden" name="detalle_MontoVenta[]" id="detalle_MontoVenta<?=$id?>" value="<?=$f['MontoVenta']?>">
					<input type="hidden" name="detalle_MontoVentaUnitario[]" id="detalle_MontoVentaUnitario<?=$id?>" value="<?=$f['MontoVentaUnitario']?>">
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
				<td><input type="text" name="detalle_CodAlmacen[]" value="<?=$_PARAMETRO['COVTAALMACEN']?>" style="text-align: center;" class="cell2" readonly></td>
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
					<input type="hidden" name="detalle_Estado[]" value="PR">
					<input type="text" value="<?=mb_strtoupper(printValores('documento-estado-detalle','PR'))?>" class="cell2" style="text-align:center;" readonly="readonly">
				</td>
			</tr>
			<?php
		}
	}
	elseif ($accion == "detalle_factura_insertar") {
		$id = $nro_detalles;
		$Cantidad = setNumero($Cantidad);
		if (!empty($CodItem))
		{
			$sql = "SELECT
						i.*,
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
						*,
						'S' AS TipoDetalle,
						CodServicio AS CodItem,
						'UNI' AS CodUnidad,
						'UNI' AS CodUnidadComp,
						'0' AS StockActual,
						'0' AS StockActualEqui,
						FlagExoneradoIva AS FlagExonIva,
						'1' AS Unidades
					FROM co_mastservicios
					WHERE CodServicio = '$CodServicio'";
		}
		##	
		$field = getRecords($sql);
		foreach ($field as $f)
		{
			$MontoTotal = $f['PrecioVenta'] * $Cantidad;
			$PrecioUnitFinal = $f['PrecioVenta'] / $igvp;
			$MontoTotalFinal = $PrecioUnitFinal * $Cantidad;
			?>
			<tr class="trListaBody" onclick="clk($(this), 'detalle', 'detalle_<?=$id?>');" id="detalle_<?=$id?>">
				<th>
					<input type="hidden" name="detalle_Secuencia[]" id="detalle_Secuencia<?=$id?>" value="0">
					<input type="hidden" name="detalle_TipoDetalle[]" value="<?=$f['TipoDetalle']?>">
					<input type="hidden" name="detalle_CodCotizacion[]" value="">
					<input type="hidden" name="detalle_CodUnidadEqui[]" id="detalle_CodUnidadEqui<?=$id?>" value="<?=$f['CodUnidadEqui']?>">
					<input type="hidden" name="detalle_CantidadEqui[]" id="detalle_CantidadEqui<?=$id?>" value="<?=$f['CantidadEqui']?>">
					<input type="hidden" name="detalle_CodImpuesto[]" id="detalle_CodImpuesto<?=$id?>" value="<?=$f['CodImpuesto']?>">
					<input type="hidden" name="detalle_FactorImpuesto[]" id="detalle_FactorImpuesto<?=$id?>" value="<?=$f['FactorImpuesto']?>">
					<input type="hidden" name="detalle_MontoVenta[]" id="detalle_MontoVenta<?=$id?>" value="<?=$f['MontoVenta']?>">
					<input type="hidden" name="detalle_MontoVentaUnitario[]" id="detalle_MontoVentaUnitario<?=$id?>" value="<?=$f['MontoVentaUnitario']?>">
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
				<td><input type="text" name="detalle_CodAlmacen[]" value="<?=$_PARAMETRO['COVTAALMACEN']?>" style="text-align: center;" class="cell2" readonly></td>
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
			</tr>
			<?php
		}
	}
	elseif ($accion == "cobranza_insertar") {
		$id = $nro_detalle;
		?>
		<tr class="trListaBody" onclick="clk($(this), 'cobranza', 'cobranza_<?=$id?>');" id="cobranza_<?=$id?>">
			<th>
				<input type="hidden" name="cobranza_Secuencia[]" value="0">
				<?=$id?>
			</th>
			<td>
				<select name="cobranza_CodTipoPago[]" id="cobranza_CodTipoPago<?=$id?>" class="cell" onchange="cobranza_tipo_pago(this.value, '<?=$id?>');">
					<option value=''>&nbsp;</option>
					<?=loadSelect2('co_tipopago','CodTipoPago','Descripcion')?>
				</select>
			</td>
			<td>
				<select name="cobranza_CodTipoTarjeta[]" id="cobranza_CodTipoTarjeta<?=$id?>" class="cell">
					<option value=''>&nbsp;</option>
				</select>
			</td>
			<td>
				<select name="cobranza_CodBanco[]" id="cobranza_CodBanco<?=$id?>" class="cell">
					<option value=''>&nbsp;</option>
				</select>
			</td>
            <td>
				<input type="text" name="cobranza_MontoLocal[]" value="0,00" class="cell currency" style="text-align:right; font-weight: bold;" onchange="setMontosCobranza();">
            </td>
            <td>
				<input type="text" name="cobranza_CtaBancaria[]" id="cobranza_CtaBancaria<?=$id?>" value="" class="cell" maxlength="20" readonly>
            </td>
            <td>
				<input type="text" name="cobranza_DocReferencia[]" value="" class="cell" maxlength="30">
            </td>
		</tr>
		<?php
	}
	elseif ($accion == "cobranza_tipo_pago") {
		$sql = "SELECT * FROM co_tipopago WHERE CodTipoPago = '$CodTipoPago'";
		$field_tipo_pago = getRecord($sql);
		##	tipos de tarjeta
		$field_tipo_tarjeta = [];
		$tipos_tarjeta = '';
		if ($field_tipo_pago['FlagReqTipoTarjeta'] == 'S')
		{
			$sql = "SELECT * FROM co_tipotarjeta WHERE CodTipoPago = '$field_tipo_pago[CodTipoPago]'";
			$field_tipo_tarjeta = getRecords($sql);
			##	
			$tipos_tarjeta .= '<option value="">&nbsp;</option>';
			foreach ($field_tipo_tarjeta as $f)
			{
				$tipos_tarjeta .= '<option value="'.$f['CodTipoTarjeta'].'">'.$f['Descripcion'].'</option>';
			}
		}
		##	bancos
		$field_bancos = [];
		$bancos = '';
		if ($field_tipo_pago['FlagReqBanco'] == 'S')
		{
			$sql = "SELECT * FROM mastbancos";
			$field_bancos = getRecords($sql);
			##	
			$bancos .= '<option value="">&nbsp;</option>';
			foreach ($field_bancos as $f)
			{
				$bancos .= '<option value="'.$f['CodBanco'].'">'.$f['Banco'].'</option>';
			}
		}

		die(json_encode([
			'FlagReqTipoTarjeta' => $field_tipo_pago['FlagReqTipoTarjeta'],
			'FlagReqBanco' => $field_tipo_pago['FlagReqBanco'],
			'tipos_tarjeta' => $tipos_tarjeta,
			'bancos' => $bancos,
		]));
	}
	elseif ($accion == "setFormaPago") {
		$sql = "SELECT * FROM mastformapago WHERE CodFormaPago = '$CodFormaPago'";
		$field = getRecord($sql);
		##	
		die(json_encode([
			'FlagCredito' => $field['FlagCredito'],
			'DiasVence' => $field['DiasVence'],
		]));
	}
	elseif ($accion == "setDiasVence") {
		$sql = "SELECT * FROM mastformapago WHERE CodFormaPago = '$CodFormaPago'";
		$field = getRecord($sql);
		##	
		die(json_encode([
			'FechaVencimiento' => fechaFin($FechaDocumento, $field['DiasVence']),
		]));
	}
}

function pedidos_aprobar() {
	extract($_POST);
	extract($_GET);
	##	-----------------
	##	codigo
	list($NroDocumento, $UltNroEmitido) = correlativo_documento($CodOrganismo, $CodTipoDocumento);
	##	correlativo
	correlativo_documento_update($UltNroEmitido, $CodOrganismo, $CodTipoDocumento);
	##	actualizo
	$sql = "UPDATE co_documento
			SET
				NroDocumento = '$NroDocumento',
				AprobadoPor = '$AprobadoPor',
				FechaAprobado = '$FechaAprobado',
				Estado = 'AP',
				UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
				UltimaFecha = NOW()
			WHERE CodDocumento = '$CodDocumento'";
	execute($sql);
	##	detalle
	$sql = "UPDATE co_documentodet
			SET
				Estado = 'PE',
				UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
				UltimaFecha = NOW()
			WHERE CodDocumento = '$CodDocumento'";
	execute($sql);
	##	
	return $NroDocumento;
}
?>