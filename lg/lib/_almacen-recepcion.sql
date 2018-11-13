INSERT INTO lg_transaccion
				SET
					CodOrganismo = '0001',
					CodDocumento = 'NI',
					NroDocumento = '000002',
					NroInterno = '000002',
					CodTransaccion = 'ROC',
					FechaDocumento = '2018-11-06',
					Periodo = '2018-11',
					CodAlmacen = 'ALWABI',
					CodCentroCosto = '0007',
					CodDocumentoReferencia = 'FA',
					NroDocumentoReferencia = '015',
					IngresadoPor = '000002',
					RecibidoPor = '000002',
					EjecutadoPor = '000002',
					FechaEjecucion = NOW(),
					Comentarios = '',
					FlagManual = 'N',
					FlagPendiente = 'N',
					ReferenciaAnio = '2018',
					ReferenciaNroDocumento = '0000000003',
					DocumentoReferencia = 'OC-0000000003-2018-1',
					DocumentoReferenciaInterno = '0000000003',
					CodDependencia = '0006',
					Anio = '2018',
					Estado = 'CO',
					UltimoUsuario = 'KMILANO',
					UltimaFecha = NOW();

INSERT INTO ap_documentos
				SET 
					Anio = '2018',
					CodOrganismo = '0001',
					CodProveedor = '000130',
					DocumentoClasificacion = 'ROC',
					DocumentoReferencia = 'OC-0000000003-2018-1',
					Fecha = NOW(),
					ReferenciaTipoDocumento = 'OC',
					ReferenciaNroDocumento = '0000000003',
					Estado = 'PR',
					TransaccionTipoDocumento = 'NI',
					TransaccionNroDocumento = '000002',
					Comentarios = '',
					CodCentroCosto = '0007',
					UltimoUsuario = 'KMILANO',
					UltimaFecha = NOW();

INSERT INTO lg_transacciondetalle
					SET
						CodOrganismo = '0001',
						CodDocumento = 'NI',
						NroDocumento = '000002',
						Secuencia = '1',
						CodItem = '0000000001',
						Descripcion = 'ACEITE DE SOYA DIANA 12X1 LT',
						CodUnidad = 'UNI',
						CantidadPedida = '24.00',
						CantidadRecibida = '24',
						PrecioUnit = '8.33',
						Total = '200',
						ReferenciaAnio = '2018',
						ReferenciaCodDocumento = 'OC',
						ReferenciaNroDocumento = '0000000003',
						ReferenciaNroInterno = '0000000003',
						ReferenciaSecuencia = '1',
						CodCentroCosto = '0007',
						CodUnidadCompra = 'CAJ',
						CantidadCompra = '2',
						PrecioUnitCompra = '100',
						Estado = 'CO',
						UltimoUsuario = 'KMILANO',
						UltimaFecha = NOW();

INSERT INTO ap_documentosdetalle
					SET
						Anio = '2018',
						CodProveedor = '000130',
						DocumentoClasificacion = 'ROC',
						DocumentoReferencia = 'OC-0000000003-2018-1',
						Secuencia = '1',
						ReferenciaSecuencia = '1',
						CodItem = '0000000001',
						Descripcion = 'ACEITE DE SOYA DIANA 12X1 LT',
						Cantidad = '2',
						PrecioUnit = '100',
						PrecioCantidad = '200',
						Total = '200',
						CodCentroCosto = '0007',
						UltimoUsuario = 'KMILANO',
						UltimaFecha = NOW();

UPDATE lg_ordencompradetalle
						SET Estado = 'CO'
						WHERE
							Anio = '2018' AND
							CodOrganismo = '0001' AND
							NroOrden = '0000000003' AND
							Secuencia = '1';

UPDATE lg_ordencompra
							SET Estado = 'CO'
							WHERE
								Anio = '2018' AND
								CodOrganismo = '0001' AND
								NroOrden = '0000000003';

UPDATE lg_ordencompradetalle
					SET CantidadRecibida = (CantidadRecibida + 24)
					WHERE
						Anio = '2018' AND
						CodOrganismo = '0001' AND
						NroOrden = '0000000003' AND
						Secuencia = '1';

UPDATE ap_documentos
				SET
					MontoAfecto = '200.00000',
					MontoNoAfecto = '0.00000',
					MontoImpuestos = '18.00000',
					MontoTotal = '218',
					MontoPendiente = '218'
				WHERE
					Anio = '2018' AND
					CodOrganismo = '0001' AND
					CodProveedor = '000130' AND
					DocumentoClasificacion = 'ROC' AND
					DocumentoReferencia = 'OC-0000000003-2018-1';

