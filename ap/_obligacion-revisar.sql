UPDATE ap_obligaciones
				SET
					Estado = 'RV',
					RevisadoPor = '000002',
					FechaRevision = '2018-10-05',
					UltimoUsuario = 'KMILANO',
					UltimaFecha = NOW()
				WHERE
					CodProveedor = '000123' AND
					CodTipoDocumento = 'FPP' AND
					NroDocumento = '0000000001';

UPDATE lg_distribucioncompromisos
					SET
						FechaEjecucion = '2018-10-05',
						Periodo = '2018-10',
						Estado = 'CO',
						UltimoUsuario = 'KMILANO',
						UltimaFecha = NOW()
					WHERE
						Anio = '2018' AND
						CodOrganismo = '0001' AND
						CodProveedor = '000123' AND
						CodTipoDocumento = 'FPP' AND
						NroDocumento = '0000000001';

UPDATE ap_distribucionobligacion
				SET
					FechaEjecucion = '2018-10-05',
					Periodo = '2018-10',
					Estado = 'CA',
					UltimoUsuario = 'KMILANO',
					UltimaFecha = NOW()
				WHERE
					CodProveedor = '000123' AND
					CodTipoDocumento = 'FPP' AND
					NroDocumento = '0000000001';

