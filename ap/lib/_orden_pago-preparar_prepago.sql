UPDATE ap_ordenpago
						SET
							NroProceso = '000011',
							Secuencia = '1',
							Estado = 'GE',
							UltimoUsuario = 'KMILANO',
							UltimaFecha = NOW()
						WHERE
							CodProveedor = '000123'
							AND CodTipoDocumento = 'FPP'
							AND NroDocumento = '0000000001'
							AND Estado = 'PE';

INSERT INTO ap_pagos
					SET
						NroProceso = '000011',
						Secuencia = '1',
						CodTipoPago = '04',
						CodOrganismo = '0001',
						NroCuenta = '01750096120075021624',
						CodProveedor = '000123',
						NroOrden = '0000000013',
						Anio = '2018',
						NomProveedorPagar = 'AUTOMERCADO EXITO 2021 C.A.',
						MontoPago = '1369200',
						MontoRetenido = '0',
						FechaPago = '2018-10-05',
						OrigenGeneracion = 'A',
						Estado = 'GE',
						EstadoEntrega = 'C',
						EstadoChequeManual = '',
						FlagContabilizacionPendiente = 'S',
						FlagNegociacion = 'N',
						FlagNoNegociable = 'N',
						FlagCobrado = 'N',
						FlagCertificadoImpresion = 'N',
						FlagPagoDiferido = 'N',
						Periodo = '2018-10',
						GeneradoPor = '000002',
						RevisadoPor = '000006',
						ConformadoPor = '000004',
						AprobadoPor = '000004',
						UltimoUsuario = 'KMILANO',
						UltimaFecha = NOW();

