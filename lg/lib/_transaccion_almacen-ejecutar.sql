UPDATE lg_transaccion
				SET
					NroInterno = '000001',
					Estado = 'CO',
					EjecutadoPor = '000002',
					FechaEjecucion = NOW(),
					UltimoUsuario = 'KMILANO',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '0001' AND
					CodDocumento = 'NI' AND
					NroDocumento = '000001';

UPDATE lg_transacciondetalle
				SET
					Estado = 'CO',
					UltimoUsuario = 'KMILANO',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '0001' AND
					CodDocumento = 'NI' AND
					NroDocumento = '000001';

