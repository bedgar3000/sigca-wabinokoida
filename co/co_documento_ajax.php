<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion-".".sql", "w+");
##############################################################################/
if ($modulo == "formulario") {
	//	nuevo
	if ($accion == "nuevo" || $accion == "nuevo-adelanto") {
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
		if ($accion == "nuevo-adelanto") $MontoAdelantoSaldo = setNumero($MontoTotal); else $MontoAdelantoSaldo = 0.00;
		$Anio = substr($FechaDocumento, 0, 4);
		$VoucherPeriodo = substr($FechaDocumento, 0, 7);
		$FlagContabilizacionPendiente = (($_PARAMETRO['CONTONCO'] == 'S') ? 'S' : 'N');
		$FlagContabilizacionPendientePub20 = (($_PARAMETRO['CONTPUB20'] == 'S') ? 'S' : 'N');
		$iCodPersonaVendedor = (!empty($CodPersonaVendedor)?"CodPersonaVendedor = '$CodPersonaVendedor',":'');
		$iCodAlmacen = (!empty($CodAlmacen)?"CodAlmacen = '$CodAlmacen',":'');
		$iDocOriginal = (!empty($DocOriginal)?"DocOriginal = '$DocOriginal',":'');
		$iCodPedido = (!empty($CodPedido)?"CodPedido = '$CodPedido',":'');
		##	valido
		if (!trim($CodOrganismo) || !trim($CodEstablecimiento) || !trim($CodPersonaCliente) || !trim($FechaDocumento) || !trim($FechaVencimiento) || !trim($CodFormaPago)) die("Debe llenar los campos (*) obligatorios.");
		elseif (!trim($CodCentroCosto)) die("Centro de Costo obligatorio.");
		##	codigo
		$CodDocumento = codigo('co_documento','CodDocumento',10);
		list($NroDocumento, $UltNroEmitido) = correlativo_documento($CodOrganismo, $CodTipoDocumento, $NroSerie);
		##	correlativo
		correlativo_documento_update($UltNroEmitido, $CodOrganismo, $CodTipoDocumento, $NroSerie);
		##	inserto
		$sql = "INSERT INTO co_documento
				SET
					CodDocumento = '$CodDocumento',
					CodOrganismo = '$CodOrganismo',
					CodTipoDocumento = '$CodTipoDocumento',
					NroDocumento = '$NroDocumento',
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
					MontoAdelantoSaldo = '$MontoAdelantoSaldo',
					$iCodAlmacen
					$iDocOriginal
					$iCodPedido
					NroPedidoRef = '$NroPedidoRef',
					Comentarios = '$Comentarios',
					CodRutaDespacho = '$CodRutaDespacho',
					PreparadoPor = '$PreparadoPor',
					FechaPreparado = '$FechaPreparado',
					FlagDocumento = 'S',
					VoucherPeriodo = '$VoucherPeriodo',
					FlagContabilizacionPendiente = '$FlagContabilizacionPendiente',
					FlagContabilizacionPendientePub20 = '$FlagContabilizacionPendientePub20',
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()";
		execute($sql);
		##	
		$message = "|Se ha generado el documento <strong>$field_tipo_documento[Descripcion]</strong> Nro. <strong>$CodTipoDocumento-$NroDocumento</strong>";
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
			$detalle_PorcentajeDcto[$i] = setNumero($detalle_PorcentajeDcto[$i]);
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
				$sql = "SELECT * FROM lg_itemalmaceninv WHERE CodItem = '$detalle_CodItem[$i]'";
				$field_inv = getRecord($sql);
				if ($field_inv['StockActual'] < $detalle_CantidadPedida[$i]) die("El item <strong>$detalle_CodInterno[$i] - $detalle_Descripcion[$i]</strong> no tiene Stock para cubrir la Cantidad Pedida");
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
			$TransaccionNroDocumento = codigo('lg_transaccion','NroDocumento',6,['CodOrganismo','CodDocumento'],[$CodOrganismo,'NS']);
			$TransaccionNroInterno = codigo('lg_transaccion','NroDocumento',6,['Anio','CodOrganismo','CodDocumento'],[$Anio,$CodOrganismo,'NS']);
			##	inserto
			$sql = "INSERT INTO lg_transaccion
					SET
						CodOrganismo = '$CodOrganismo',
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
							CodOrganismo = '$CodOrganismo',
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
					WHERE CodDocumento = '$CodDocumento'";
			execute($sql);
		}
		else
		{
			##	actualizo documento
			$sql = "UPDATE co_documento
					SET FlagDespacho = 'N'
					WHERE CodDocumento = '$CodDocumento'";
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
						CodOrganismo = '$CodOrganismo',
						FechaCobranza = '$FechaActual',
						CodPersonaCliente = '$CodPersonaCliente',
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
		##	-----------------
		mysql_query("COMMIT");
		##	
		die($message.'|'.$CodDocumento);
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
		if ($CodTipoDocumento == $_PARAMETRO['CODOCADE']) $MontoAdelantoSaldo = setNumero($MontoTotal); else $MontoAdelantoSaldo = 0.00;
		$Anio = substr($FechaDocumento, 0, 4);
		$iCodPersonaVendedor = (!empty($CodPersonaVendedor)?"CodPersonaVendedor = '$CodPersonaVendedor',":'');
		$iCodAlmacen = (!empty($CodAlmacen)?"CodAlmacen = '$CodAlmacen',":'');
		##	valido
		if (!trim($CodPersonaCliente) || !trim($FechaDocumento) || !trim($FechaVencimiento) || !trim($CodFormaPago)) die("Debe llenar los campos (*) obligatorios.");
		##	actualizo
		$sql = "UPDATE co_documento
				SET
					CodTipoDocumento = '$CodTipoDocumento',
					FechaDocumento = '$FechaDocumento',
					FechaVencimiento = '$FechaVencimiento',
					Anio = '$Anio',
					FormaFactura = '$FormaFactura',
					CodPersonaCliente = '$CodPersonaCliente',
					DocFiscalCliente = '$DocFiscalCliente',
					NombreCliente = '$NombreCliente',
					DireccionCliente = '$DireccionCliente',
					CodPersonaCobrar = '$CodPersonaCliente',
					TipoVenta = '$TipoVenta',
					CodFormaPago = '$CodFormaPago',
					$iCodPersonaVendedor
					MonedaDocumento = '$MonedaDocumento',
					MontoAfecto = '$MontoAfecto',
					MontoNoAfecto = '$MontoNoAfecto',
					MontoDcto = '$MontoDcto',
					MontoImpuesto = '$MontoImpuesto',
					MontoTotal = '$MontoTotal',
					MontoAdelantoSaldo = '$MontoAdelantoSaldo',
					$iCodAlmacen
					Comentarios = '$Comentarios',
					CodRutaDespacho = '$CodRutaDespacho',
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
			$detalle_PorcentajeDcto[$i] = setNumero($detalle_PorcentajeDcto[$i]);
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
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	impuesto
		$sql = "SELECT * FROM mastimpuestos WHERE CodImpuesto = '$_PARAMETRO[COIVA]'";
		$field_igv = getRecord($sql);
		if ($MontoImpuesto > 0) {
			$sql = "REPLACE INTO co_documentoimpuesto
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
	//	modificar
	elseif ($accion == "modificar-restringido") {
		mysql_query("BEGIN");
		##	-----------------
		$FechaDocumento = formatFechaAMD($FechaDocumento);
		$FechaVencimiento = formatFechaAMD($FechaVencimiento);
		$Anio = substr($FechaDocumento, 0, 4);
		$iCodPersonaVendedor = (!empty($CodPersonaVendedor)?"CodPersonaVendedor = '$CodPersonaVendedor',":'');
		##	valido
		if (!trim($FechaDocumento) || !trim($FechaVencimiento) || !trim($CodFormaPago)) die("Debe llenar los campos (*) obligatorios.");
		##	actualizo
		$sql = "UPDATE co_documento
				SET
					FechaDocumento = '$FechaDocumento',
					FechaVencimiento = '$FechaVencimiento',
					Anio = '$Anio',
					CodFormaPago = '$CodFormaPago',
					$iCodPersonaVendedor
					Comentarios = '$Comentarios',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodDocumento = '$CodDocumento'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	renumerar
	elseif ($accion == "renumerar") {
		mysql_query("BEGIN");
		##	-----------------
		##	valido
		if (!trim($NroDocumento)) die("El Nro. de Documento es Obligatorio");
		elseif (strlen($NroDocumento) != 10) die("El Nro. de Documento debe tener 10 digitos");
		##	actualizo
		$sql = "UPDATE co_documento
				SET
					NroDocumento = '$NroDocumento',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodDocumento = '$CodDocumento'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	transferir
	elseif ($accion == "transferir") {
		mysql_query("BEGIN");
		##	-----------------
		##	documento
		$sql = "SELECT * FROM co_documento WHERE Coddocumento = '$CodDocumento'";
		$field_documento = getRecord($sql);
		##	detalle
		$sql = "SELECT
					dod.*
				FROM co_documentodet dod
				WHERE dod.Coddocumento = '$CodDocumento'";
		$field_detalle = getRecords($sql);
		##	cobranza (codigo)
		$CodCobranza = codigo('co_cobranza','CodCobranza',10);
		$NroCobranza = codigo('co_cobranza','NroCobranza',5,['CodOrganismo'],[$field_documento['CodOrganismo']]);
		##	cobranza
		$sql = "INSERT INTO co_cobranza
				SET
					CodCobranza = '$CodCobranza',
					CodOrganismo = '$field_documento[CodOrganismo]',
					NroCobranza = '$NroCobranza',
					FechaCobranza = '$field_documento[FechaDocumento]',
					CodPersonaCliente = '$field_documento[CodPersonaCliente]',
					FechaPreparado = NOW(),
					PreparadoPor = '$_SESSION[CODPERSONA_ACTUAL]',
					FechaAprobado = NOW(),
					AprobadoPor = '$_SESSION[CODPERSONA_ACTUAL]',
					Estado = 'AP',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()";
		execute($sql);
		##	cobranza (detalle)
		$sql = "INSERT INTO co_cobranzadet
				SET
					CodCobranza = '$CodCobranza',
					Secuencia = '1',
					MonedaDocumento = 'L',
					MontoLocal = '$field_documento[MontoTotal]',
					DocReferencia = '$field_documento[CodTipoDocumento]$field_documento[NroDocumento]',
					Estado = 'AP',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()";
		execute($sql);
		##	inserto
		$sql = "INSERT INTO co_documentocobranza
				SET
					CodCobranza = '$CodCobranza',
					CodDocumento = '$field_documento[CodDocumento]',
					MontoPagado = '$field_documento[MontoTotal]',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()";
		execute($sql);
		##	actualizo
		$sql = "UPDATE co_documento
				SET
					MontoPagado = '$field_documento[MontoTotal]',
					Estado = 'CO',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodDocumento = '$field_documento[CodDocumento]'";
		execute($sql);
		##	OBLIGACIÓN
		$sql = "SELECT * FROM mastproveedores WHERE CodProveedor = '$field_documento[CodPersonaCliente]'";
		$field_proveedor = getRecord($sql);
		##	
		$CodTipoDocumento = $_PARAMETRO['CONCC'];
		$NroRegistro = getCodigo_2("ap_obligaciones", "NroRegistro", "CodOrganismo", $field_documento['CodOrganismo'], 6);
		$NroDocumento = codigo('ap_obligaciones','NroDocumento',10,['CodProveedor','CodTipoDocumento'],[$field_documento['CodPersonaCliente'],$CodTipoDocumento]);
		##	
		$sql = "SELECT NroCuenta
				FROM ap_ctabancariadefault
				WHERE
					CodOrganismo = '$field_documento[CodOrganismo]'
					AND CodTipoPago = '$field_proveedor[CodTipoPago]'";
		$NroCuenta = getVar3($sql);
		##	
		if (empty($field_proveedor['CodTipoPago'])) die('No se encontró un tipo de pago por defecto asociada al organismo');
		elseif (empty($NroCuenta)) die('No se encontró una cuenta bancaria por defecto asociada al organismo');
		elseif (empty($field_proveedor['CodTipoServicio'])) die('No se encontró un tipo de servicio por defecto asociada al organismo');
		##	inserto
		$sql = "INSERT INTO ap_obligaciones
				SET
					CodProveedor = '$field_documento[CodPersonaCliente]',
					CodTipoDocumento = '$CodTipoDocumento',
					NroDocumento = '$NroDocumento',
					CodOrganismo = '$field_documento[CodOrganismo]',
					CodProveedorPagar = '$field_documento[CodPersonaCliente]',
					NroControl = '$field_documento[CodTipoDocumento]$field_documento[NroDocumento]',
					NroFactura = '$field_documento[CodTipoDocumento]$field_documento[NroDocumento]',
					NroCuenta = '$NroCuenta',
					CodTipoPago = '$field_proveedor[CodTipoPago]',
					CodTipoServicio = '$field_proveedor[CodTipoServicio]',
					ReferenciaTipoDocumento = '$field_documento[CodTipoDocumento]$field_documento[NroDocumento]',
					ReferenciaNroDocumento = '$field_documento[CodTipoDocumento]$field_documento[NroDocumento]',
					MontoObligacion = '$field_documento[MontoTotal]',
					MontoImpuestoOtros = '0.00',
					MontoNoAfecto = '$field_documento[MontoNoAfecto]',
					MontoAfecto = '$field_documento[MontoAfecto]',
					MontoAdelanto = '0.00',
					MontoImpuesto = '$field_documento[MontoImpuesto]',
					MontoPagoParcial = '0.00',
					NroRegistro = '$NroRegistro',
					Comentarios = '$field_documento[Comentarios]',
					ComentariosAdicional = '$field_documento[Comentarios]',
					FechaRegistro = '$FechaActual',
					FechaVencimiento = '$FechaActual',
					FechaRecepcion = '$FechaActual',
					FechaDocumento = '$FechaActual',
					FechaProgramada = '$FechaActual',
					FechaFactura = '$FechaActual',
					IngresadoPor = '$_SESSION[CODPERSONA_ACTUAL]',
					FechaPreparacion = NOW(),
					Periodo = '$PeriodoActual',
					CodCentroCosto = '$field_documento[CodCentroCosto]',
					FlagGenerarPago = 'S',
					FlagAfectoIGV = 'N',
					FlagDiferido = 'N',
					FlagPagoDiferido = 'N',
					FlagCompromiso = 'N',
					FlagPresupuesto = 'N',
					FlagPagoIndividual = 'N',
					FlagCajaChica = 'N',
					FlagDistribucionManual = 'S',
					CodPresupuesto = '',
					Ejercicio = '$AnioActual',
					CodFuente = '',
					FlagNomina = 'N',
					FlagFacturaPendiente = 'N',
					FlagAgruparIgv = 'N',
					Estado = 'PR',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()";
		execute($sql);
		##	
		$Secuencia = 0;
		foreach ($field_detalle as $fd)
		{
			++$Secuencia;
			$sql = "INSERT INTO ap_obligacionescuenta
					SET
						CodProveedor = '$field_documento[CodPersonaCliente]',
						CodTipoDocumento = '$CodTipoDocumento',
						NroDocumento = '$NroDocumento',
						Secuencia = '$Secuencia',
						Linea = '1',
						Descripcion = '$fd[Descripcion]',
						Monto = '$fd[MontoTotalFinal]',
						CodCentroCosto = '$field_documento[CodCentroCosto]',
						CodCuenta = '$fd[CodCuenta]',
						CodCuentaPub20 = '$fd[CodCuentaPub20]',
						cod_partida = '',
						TipoOrden = '$field_documento[CodTipoDocumento]',
						NroOrden = '$field_documento[NroDocumento]',
						FlagNoAfectoIGV = '$fd[FlagExonIva]',
						Referencia = '$field_documento[CodTipoDocumento]$field_documento[NroDocumento]',
						CodPersona = '$field_documento[CodPersonaCliente]',
						NroActivo = '',
						FlagDiferido = 'N',
						CodOrganismo = '$field_documento[CodOrganismo]',
						Ejercicio = '$AnioActual',
						CodPresupuesto = '',
						CodFuente = '',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	-----------------
		mysql_query("COMMIT");
	}
	//	aprobar
	elseif ($accion == "aprobar") {
		pedidos_aprobar();
	}
	//	anular
	elseif ($accion == "anular") {
		mysql_query("BEGIN");
		##	-----------------
		$field_documento = getRecord("SELECT * FROM co_documento WHERE CodDocumento = '$CodDocumento'");
		##	
		if ($field_documento['Estado'] == 'PE') $NuevoEstado = 'AN';
		else die('No puede anular un documento <strong>'.printValores('documento2-estado',$field_documento['Estado']).'</strong>');
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
}
elseif ($modulo == "validar") {
	//	modificar
	if($accion == "modificar") {
		$sql = "SELECT Estado FROM co_documento WHERE CodDocumento = '$codigo'";
		$Estado = getVar3($sql);
		if ($Estado != 'PE') die('No puede modificar un documento <strong>'.printValores('documento2-estado',$Estado).'</strong>');
	}
	//	transferir
	elseif($accion == "transferir") {
		$sql = "SELECT Estado FROM co_documento WHERE CodDocumento = '$codigo'";
		$Estado = getVar3($sql);
		if ($Estado == 'AN') die('No puede transferir un documento <strong>'.printValores('documento2-estado',$Estado).'</strong>');
	}
	//	anular
	elseif($accion == "anular") {
		$sql = "SELECT Estado FROM co_documento WHERE CodDocumento = '$codigo'";
		$Estado = getVar3($sql);
		if ($Estado != 'PE') die('No puede anular un documento <strong>'.printValores('documento2-estado',$Estado).'</strong>');
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
					<input type="hidden" name="detalle_Estado[]" value="PE">
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
					<input type="text" name="detalle_PrecioUnit[]" value="<?=number_format($f['MontoVenta'],2,',','.')?>" class="cell currency" style="text-align:right;" onchange="setMontosVentas(true, '<?=$id?>');">
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
	elseif ($accion == "detalle_documento") {
		$sql = "SELECT
					dod.*,
					(CASE WHEN cd.TipoDetalle = 'I' THEN i.CodInterno ELSE s.CodInterno END) AS CodInterno,
					(CASE WHEN cd.TipoDetalle = 'I' THEN 0 ELSE 1 END) AS Unidades,
					(CASE WHEN cd.TipoDetalle = 'I' THEN i.StockActual ELSE 0 END) AS StockActual,
					(CASE WHEN cd.TipoDetalle = 'I' THEN i.StockActualEqui ELSE 0 END) AS StockActualEqui,
					(CASE WHEN cd.TipoDetalle = 'I' THEN i.CodUnidadEqui ELSE 'UNI' END) AS CodUnidadEqui,
					(CASE WHEN cd.TipoDetalle = 'I' THEN i.CantidadEqui ELSE 0 END) AS CantidadEqui,
					(CASE WHEN cd.TipoDetalle = 'I' THEN i.CodImpuesto
						  ELSE (CASE WHEN s.FlagExoneradoIva = 'S' THEN 0 ELSE '$_PARAMETRO[COIVA]' END) END) AS CodImpuesto,
					(CASE WHEN cd.TipoDetalle = 'I' THEN i.FactorImpuesto
						  ELSE (CASE WHEN s.FlagExoneradoIva = 'S' THEN 0 ELSE '$igv' END) END) AS FactorImpuesto,
					(CASE WHEN cd.TipoDetalle = 'I' THEN i.MontoVenta ELSE 0 END) AS MontoVenta,
					(CASE WHEN cd.TipoDetalle = 'I' THEN i.MontoVentaUnitario ELSE 0 END) AS MontoVentaUnitario
				FROM co_documentodet dod
				LEFT JOIN vw_lg_inventarioactual_item i ON (
					i.CodItem = dod.CodItem
					AND dod.TipoDetalle = 'I'
				)
				LEFT JOIN co_mastservicios s ON (
					s.CodServicio = dod.CodItem
					AND dod.TipoDetalle = 'S'
				)
				WHERE dod.CodDocumento = '$CodDocumento'";
		$field = getRecords($sql);
		foreach ($field as $f)
		{
			?>
			<tr class="trListaBody" onclick="clk($(this), 'detalle', 'detalle_<?=$id?>');" id="detalle_<?=$id?>">
				<th>
					<input type="hidden" name="detalle_Secuencia[]" id="detalle_Secuencia<?=$id?>" value="<?=$f['Secuencia']?>">
					<input type="hidden" name="detalle_TipoDetalle[]" value="<?=$f['TipoDetalle']?>">
					<input type="hidden" name="detalle_CodCotizacion[]" value="">
					<input type="hidden" name="detalle_Estado[]" value="PE">
					<input type="hidden" name="detalle_CodUnidadEqui[]" id="detalle_CodUnidadEqui<?=$id?>" value="<?=$f['CodUnidadEqui']?>">
					<input type="hidden" name="detalle_CantidadEqui[]" id="detalle_CantidadEqui<?=$id?>" value="<?=$f['CantidadEqui']?>">
					<input type="hidden" name="detalle_CodImpuesto[]" id="detalle_CodImpuesto<?=$id?>" value="<?=$f['CodImpuesto']?>">
					<input type="hidden" name="detalle_FactorImpuesto[]" id="detalle_FactorImpuesto<?=$id?>" value="<?=$f['FactorImpuesto']?>">
					<input type="hidden" name="detalle_MontoVenta[]" id="detalle_MontoVenta<?=$id?>" value="<?=$f['MontoVenta']?>">
					<input type="hidden" name="detalle_MontoVentaUnitario[]" id="detalle_MontoVentaUnitario<?=$id?>" value="<?=$f['MontoVentaUnitario']?>">
					<?=$f['Secuencia']?>
				</th>
				<td>
					<input type="hidden" name="detalle_CodItem[]" value="<?=$f['CodItem']?>">
					<input type="text" name="detalle_CodInterno[]" value="<?=$f['CodInterno']?>" class="cell2" style="text-align: center;" readonly>
				</td>
				<td>
					<input type="text" name="detalle_Descripcion[]" value="<?=$f['Descripcion']?>" class="cell2" readonly>
				</td>
				<td align="center"><?=$f['TipoDetalle']?></td>
				<td><input type="text" name="detalle_CodAlmacen[]" value="<?=$f['CodAlmacen']?>" style="text-align: center;" class="cell2" readonly></td>
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
					<input type="text" name="detalle_CantidadPedida[]" value="<?=number_format($f['CantidadPedida'],5,',','.')?>" class="cell currency5" style="text-align:right;" onchange="setMontosVentas();">
				</td>
				<td>
					<input type="text" name="detalle_PrecioUnit[]" value="<?=number_format(-$f['PrecioUnit'],2,',','.')?>" class="cell currency" style="text-align:right;" onchange="setMontosVentas(true, '<?=$id?>');">
				</td>
				<td>
					<input type="text" name="detalle_MontoTotal[]" value="<?=number_format(-$f['MontoTotal'],2,',','.')?>" class="cell2 " style="text-align:right;" readonly>
				</td>
				<td align="center">
					<input type="checkbox" name="detalle_FlagExonIva[]" value="S" <?=chkFlag($f['FlagExonIva'])?> onchange="setMontosVentas();" onclick="this.checked=!this.checked">
				</td>
				<td>
					<input type="text" name="detalle_PrecioUnitOriginal[]" id="detalle_PrecioUnitOriginal<?=$id?>" value="<?=number_format(-$f['PrecioUnitOriginal'],2,',','.')?>" class="cell2 " style="text-align:right;" readonly>
				</td>
				<td>
					<input type="text" name="detalle_PrecioUnitFinal[]" id="detalle_PrecioUnitFinal<?=$id?>" value="<?=number_format(-$f['PrecioUnitFinal'],5,',','.')?>" class="cell2 " style="text-align:right;" readonly>
				</td>
				<td>
					<input type="text" name="detalle_MontoTotalFinal[]" id="detalle_MontoTotalFinal<?=$id?>" value="<?=number_format(-$f['MontoTotalFinal'],2,',','.')?>" class="cell2 " style="text-align:right;" readonly>
				</td>
				<td>
					<input type="hidden" name="detalle_MontoDcto[]" id="detalle_MontoDcto<?=$id?>" value="<?=$f['MontoDcto']?>" class="">
					<input type="text" name="detalle_PorcentajeDcto[]" id="detalle_PorcentajeDcto<?=$id?>" value="<?=number_format($f['PorcentajeDcto1'],2,',','.')?>" class="cell2 currency" style="text-align:right;" readonly>
				</td>
			</tr>
			<?php
		}
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
?>