INSERT INTO ap_obligaciones
				SET
					CodProveedor = '000123',
					CodTipoDocumento = 'FPP',
					NroDocumento = '0000000001',
					CodOrganismo = '0001',
					CodProveedorPagar = '000123',
					NroControl = '2018100502',
					NroFactura = '2018100502',
					NroCuenta = '01750096120075021624',
					CodTipoPago = '04',
					CodTipoServicio = 'IVA',
					ReferenciaTipoDocumento = '',
					ReferenciaNroDocumento = '',
					MontoObligacion = '2738400',
					MontoImpuestoOtros = '0',
					MontoNoAfecto = '0',
					MontoAfecto = '2445000',
					MontoAdelanto = '1369200',
					MontoImpuesto = '293400',
					MontoPagoParcial = '0',
					NroRegistro = '000014',
					Comentarios = 'PRUEBA APLICACION DE ADELANTO PARCIAL',
					ComentariosAdicional = 'PRUEBA APLICACION DE ADELANTO PARCIAL',
					FechaRegistro = '2018-10-05',
					FechaVencimiento = '2018-10-05',
					FechaRecepcion = '2018-10-05',
					FechaDocumento = '2018-10-05',
					FechaProgramada = '2018-10-05',
					FechaFactura = '2018-10-05',
					IngresadoPor = '000002',
					FechaPreparacion = '2018-10-05',
					Periodo = '2018-10',
					CodCentroCosto = '0007',
					FlagGenerarPago = 'S',
					FlagAfectoIGV = 'N',
					FlagDiferido = 'N',
					FlagPagoDiferido = 'N',
					FlagCompromiso = 'S',
					FlagPresupuesto = 'S',
					FlagPagoIndividual = 'N',
					FlagCajaChica = 'N',
					FlagDistribucionManual = 'S',
					CodPresupuesto = '0001',
					Ejercicio = '2018',
					CodFuente = '02',
					FlagNomina = '',
					FlagFacturaPendiente = 'N',
					
					
					FlagAgruparIgv = 'N',
					Estado = 'PR',
					UltimoUsuario = 'KMILANO',
					UltimaFecha = NOW();

INSERT INTO ap_obligacionescuenta
						SET
							CodProveedor = '000123',
							CodTipoDocumento = 'FPP',
							NroDocumento = '0000000001',
							Secuencia = '1',
							Linea = '1',
							Descripcion = '',
							Monto = '2445000',
							CodCentroCosto = '0007',
							CodCuenta = '612011101',
							CodCuentaPub20 = '61300021101',
							cod_partida = '402.11.01.00',
							TipoOrden = '',
							NroOrden = '',
							FlagNoAfectoIGV = 'N',
							Referencia = '',
							CodPersona = '',
							NroActivo = '',
							FlagDiferido = 'N',
							CodOrganismo = '0001',
							Ejercicio = '2018',
							CodPresupuesto = '0001',
							CodFuente = '02',
							UltimoUsuario = 'KMILANO',
							UltimaFecha = NOW();

INSERT INTO lg_distribucioncompromisos
						SET
							CodOrganismo = '0001',
							CodProveedor = '000123',
							CodTipoDocumento = 'FPP',
							NroDocumento = '0000000001',
							Secuencia = '1',
							Linea = '1',
							CodCentroCosto = '0007',
							cod_partida = '402.11.01.00',
							Monto = '2445000.00000',
							Anio = '2018',
							Periodo = '2018-10',
							Mes = '10',
							CodPresupuesto = '0001',
							Ejercicio = '2018',
							CodFuente = '02',
							Origen = 'OB',
							Estado = 'PE',
							UltimoUsuario = 'KMILANO',
							UltimaFecha = NOW();

INSERT INTO ap_distribucionobligacion
					SET
						CodProveedor = '000123',
						CodTipoDocumento = 'FPP',
						NroDocumento = '0000000001',
						CodCentroCosto = '0007',
						Monto = '2445000.00000',
						CodCuenta = '612011101',
						CodCuentaPub20 = '61300021101',
						cod_partida = '402.11.01.00',
						Anio = '2018',
						Periodo = '2018-10',
						CodOrganismo = '0001',
						CodPresupuesto = '0001',
						Ejercicio = '2018',
						CodFuente = '02',
						FlagCompromiso = 'S',
						Origen = 'OB',
						Estado = 'PE',
						UltimoUsuario = 'KMILANO',
						UltimaFecha = NOW();

INSERT INTO lg_distribucioncompromisos
						SET
							CodOrganismo = '0001',
							CodProveedor = '000123',
							CodTipoDocumento = 'FPP',
							NroDocumento = '0000000001',
							Secuencia = '2',
							Linea = '1',
							CodCentroCosto = '0007',
							cod_partida = '403.18.01.00',
							Monto = '293400',
							Anio = '2018',
							Periodo = '2018-10',
							Mes = '10',
							CodPresupuesto = '0001',
							Ejercicio = '2018',
							CodFuente = '02',
							Origen = 'OB',
							Estado = 'PE',
							UltimoUsuario = 'KMILANO',
							UltimaFecha = NOW();

INSERT INTO ap_distribucionobligacion
					SET
						CodProveedor = '000123',
						CodTipoDocumento = 'FPP',
						NroDocumento = '0000000001',
						CodCentroCosto = '0007',
						Monto = '293400',
						CodCuenta = '6131701',
						CodCuentaPub20 = '61300031801',
						cod_partida = '403.18.01.00',
						Anio = '2018',
						Periodo = '2018-10',
						CodOrganismo = '0001',
						CodPresupuesto = '0001',
						Ejercicio = '2018',
						CodFuente = '02',
						FlagCompromiso = 'S',
						Origen = 'OB',
						Estado = 'PE',
						UltimoUsuario = 'KMILANO',
						UltimaFecha = NOW();

INSERT INTO ap_obligacionesadelantos
					SET
						CodProveedor = '000123',
						CodTipoDocumento = 'FPP',
						NroDocumento = '0000000001',
						CodAdelanto = '0000000010',
						UltimoUsuario = 'KMILANO',
						UltimaFecha = NOW();

UPDATE ap_gastoadelanto
					SET Estado = 'AC'
					WHERE CodAdelanto = '0000000010';
