INSERT INTO ap_bancotransaccion
					SET
						NroTransaccion = '00011',
						Secuencia = '1',
						CodOrganismo = '0001',
						CodTipoTransaccion = 'PAG',
						TipoTransaccion = 'E',
						NroCuenta = '01750096120075021624',
						CodTipoDocumento = 'FPP',
						CodProveedor = '000123',
						CodCentroCosto = '0007',
						PreparadoPor = '000002',
						FechaPreparacion = NOW(),
						FechaTransaccion = '2018-10-05',
						PeriodoContable = '2018-10',
						Monto = '-1369200',
						Comentarios = 'PRUEBA APLICACION DE ADELANTO PARCIAL',
						PagoNroProceso = '000011',
						PagoSecuencia = '1',
						NroPago = '0000000007',
						FlagConciliacion = 'N',
						FlagAutomatico = 'S',
						Estado = 'AP',
						UltimoUsuario = 'KMILANO',
						UltimaFecha = NOW();

UPDATE ap_obligaciones
						SET
							FechaPago = '2018-10-05',
							NroPago = '0000000007',
							NroProceso = '000011',
							ProcesoSecuencia = '1',
							Estado = 'PA',
							UltimoUsuario = 'KMILANO',
							UltimaFecha = NOW()
						WHERE
							CodProveedor = '000123' AND
							CodTipoDocumento = 'FPP' AND
							NroDocumento = '0000000001';

UPDATE ap_ordenpago
					SET
						Estado = 'PA',
						NroPago = '0000000007',
						FechaTransferencia = '2018-10-05',
						UltimoUsuario = 'KMILANO',
						UltimaFecha = NOW()
					WHERE
						Anio = '2018' AND
						CodOrganismo = '0001' AND
						NroOrden = '0000000013';

UPDATE ap_ordenpagodistribucion
					SET
						FechaEjecucion = '2018-10-05',
						Estado = 'PA',
						Periodo = '2018-10',
						PagoNroProceso = '000011',
						PagoSecuencia = '1',
						UltimoUsuario = 'KMILANO',
						UltimaFecha = NOW()
					WHERE
						Anio = '2018' AND
						CodOrganismo = '0001' AND
						NroOrden = '0000000013' AND
						Estado = 'PE';

UPDATE ap_pagosparciales
					SET
						Estado = 'PA',
						UltimoUsuario = 'KMILANO',
						UltimaFecha = NOW()
					WHERE
						Anio = '2018' AND
						CodOrganismo = '0001' AND
						NroOrden = '0000000013' AND
						Estado = 'PE';

UPDATE ap_pagos
					SET
						FechaPago = '2018-10-05',
						Periodo = '2018-10',
						NroPago = '0000000007',
						Estado = 'IM',
						UltimoUsuario = 'KMILANO',
						UltimaFecha = NOW()
					WHERE
						NroProceso = '000011' AND
						Secuencia = '1';

UPDATE ap_ctabancariatipopago
					SET
						UltimoNumero = '0000000007',	
						UltimoUsuario = 'KMILANO',
						UltimaFecha = NOW()
					WHERE
						NroCuenta = '01750096120075021624' AND
						CodTipoPago = '04';

