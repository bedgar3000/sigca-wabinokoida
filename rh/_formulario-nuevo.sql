UPDATE rh_contratos
				SET
					CodFormato = 'DE',
					FechaDesde = '2009-02-16',
					FechaHasta = '2017-07-31',
					FlagFirma = 'S',
					FechaFirma = '2017-07-11',
					FechaContrato = '2017-07-11',
					Contrato = '',
					Comentarios = '',
					Estado = 'VI',
					UltimoUsuario = 'TEST',
					UltimaFecha = NOW()
				WHERE CodContrato = '0000020000';

