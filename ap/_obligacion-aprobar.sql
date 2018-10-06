UPDATE ap_obligaciones
				SET
					Estado = 'AP',
					AprobadoPor = '000002',
					FechaAprobado = '2018-10-05',
					UltimoUsuario = 'KMILANO',
					UltimaFecha = NOW()
				WHERE
					CodProveedor = '000123' AND
					CodTipoDocumento = 'FPP' AND
					NroDocumento = '0000000001';

INSERT INTO ap_ordenpago
				SET
					Anio = '2018',
					CodOrganismo = '0001',
					NroOrden = '0000000013',
					CodAplicacion = 'AP',
					CodProveedor = '000123',
					CodTipoDocumento = 'FPP',
					NroDocumento = '0000000001',
					FechaDocumento = '2018-10-05',
					FechaVencimiento = '2018-10-05',
					FechaOrdenPago = '2018-10-05',
					FechaVencimientoReal = '2018-10-05',
					FechaProgramada = '2018-10-05',
					FechaRevisado = '2018-10-05',
					CodProveedorPagar = '000123',
					NomProveedorPagar = 'AUTOMERCADO EXITO 2021 C.A.',
					Concepto = 'PRUEBA APLICACION DE ADELANTO PARCIAL',
					NroCuenta = '01750096120075021624',
					CodTipoPago = '04',
					MontoTotal = '1369200',
					NroRegistro = '000014',
					FlagPagoDiferido = 'N',
					CodCentroCosto = '0007',
					CodSistemaFuente = '',
					Periodo = '2018-10',
					FechaPreparado = '2018-10-05',
					PreparadoPor = '000002',
					RevisadoPor = '000006',
					AprobadoPor = '000004',
					Estado = 'PE',
					Ejercicio = '2018',
					CodPresupuesto = '0001',
					CodFuente = '02',
					UltimoUsuario = 'KMILANO',
					UltimaFecha = NOW();

INSERT INTO ap_ordenpagodistribucion
					SET
						CodOrganismo = '0001',
						NroOrden = '0000000013',
						CodProveedor = '000123',
						CodTipoDocumento = 'FPP',
						NroDocumento = '0000000001',
						Linea = '1',
						CodCentroCosto = '0007',
						Monto = '2445000.00000',
						MontoPagado = '2445000.00000',
						CodCuenta = '612011101',
						CodCuentaPub20 = '61300021101',
						cod_partida = '402.11.01.00',
						Anio = '2018',
						Periodo = '2018-10',
						CodPresupuesto = '0001',
						Ejercicio = '2018',
						CodFuente = '02',
						Origen = 'OP',
						Estado = 'PE',
						UltimoUsuario = 'KMILANO',
						UltimaFecha = NOW();

INSERT INTO ap_ordenpagodistribucion
					SET
						CodOrganismo = '0001',
						NroOrden = '0000000013',
						CodProveedor = '000123',
						CodTipoDocumento = 'FPP',
						NroDocumento = '0000000001',
						Linea = '2',
						CodCentroCosto = '0007',
						Monto = '293400.00000',
						MontoPagado = '293400.00000',
						CodCuenta = '6131701',
						CodCuentaPub20 = '61300031801',
						cod_partida = '403.18.01.00',
						Anio = '2018',
						Periodo = '2018-10',
						CodPresupuesto = '0001',
						Ejercicio = '2018',
						CodFuente = '02',
						Origen = 'OP',
						Estado = 'PE',
						UltimoUsuario = 'KMILANO',
						UltimaFecha = NOW();

INSERT INTO ap_ordenpagocontabilidad
							SET
								Anio = '2018',
								CodOrganismo = '0001',
								NroOrden = '0000000013',
								CodContabilidad = 'T',
								Secuencia = '1',
								CodCuenta = '2110401',
								Monto = '-2738400',
								UltimoUsuario = 'KMILANO',
								UltimaFecha = NOW();

INSERT INTO ap_ordenpagocontabilidad
							SET
								Anio = '2018',
								CodOrganismo = '0001',
								NroOrden = '0000000013',
								CodContabilidad = 'T',
								Secuencia = '2',
								CodCuenta = '612011101',
								Monto = '2445000',
								UltimoUsuario = 'KMILANO',
								UltimaFecha = NOW();

INSERT INTO ap_ordenpagocontabilidad
							SET
								Anio = '2018',
								CodOrganismo = '0001',
								NroOrden = '0000000013',
								CodContabilidad = 'T',
								Secuencia = '3',
								CodCuenta = '6131701',
								Monto = '293400',
								UltimoUsuario = 'KMILANO',
								UltimaFecha = NOW();

