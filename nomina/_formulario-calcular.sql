SELECT CodCentroCosto FROM ac_mastcentrocosto WHERE Codigo = '0500';

SELECT
					ba.*,
					tn.Nomina,
					pv.CodCuenta,
					pv.CodCuentaPub20
				FROM
					rh_bonoalimentacion ba
					INNER JOIN tiponomina tn On (tn.CodTipoNom = ba.CodTipoNom)
					LEFT JOIN pv_partida pv ON (pv.cod_partida = ba.cod_partida)
				WHERE
					ba.Anio = '2018'
					AND ba.CodOrganismo = '0001'
					AND ba.CodBonoAlim = '001';

SELECT * FROM pr_obligacionesbono WHERE FlagTransferido = 'S';

DELETE FROM pr_obligacionesbono WHERE CodBonoAlim = '001' AND FlagTransferido <> 'S';

SELECT *
				FROM rh_bonoalimentaciondet
				WHERE Anio = '2018' AND CodOrganismo = '0001' AND CodBonoAlim = '001' 
				ORDER BY CodPersona;

SELECT MAX(Secuencia) FROM pr_obligacionesbono WHERE 1  AND Anio = '2018';

SELECT * FROM pr_obligacionesbono WHERE CodBonoAlim = '001' AND Periodo = '2018-01' AND FlagTransferido = 'S';

INSERT INTO pr_obligacionesbono
					SET
						CodObligacionBono = '2018000001',
						CodOrganismo = '0001',
						CodCentroCosto = '0010',
						FechaRegistro = '2018-02-05',
						Periodo = '2018-01',
						MontoObligacion = '279000.00',
						CodPresupuesto = '0001',
						CodFuente = '03',
						CalculadoPor = '000002',
						Comentarios = 'BONO DE ALIMENTACIÓN NÓMINA DE EMPLEADOS DEL 01-01-2018 AL 31-01-2018',
						ComentariosAdicional = 'BONO DE ALIMENTACIÓN NÓMINA DE EMPLEADOS DEL 01-01-2018 AL 31-01-2018',
						CodBonoAlim = '001',
						CodProveedor = '000002',
						CodTipoDocumento = 'BAE',
						NroControl = '000120180101BAE001',
						NroFactura = '000120180101BAE001',
						Anio = '2018',
						Secuencia = '000001',
						UltimoUsuario = 'EVELASQUEZ',
						UltimaFecha = NOW();

INSERT INTO pr_obligacionesbonocuenta
					SET
						CodObligacionBono = '2018000001',
						Secuencia = '1',
						CodCentroCosto = '0010',
						CodCuenta = '611020108',
						CodCuentaPub20 = '61300010408',
						cod_partida = '401.04.08.00',
						Monto = '279000.00',
						CodOrganismo = '0001',
						CodPresupuesto = '0001',
						CodFuente = '03',
						UltimoUsuario = 'EVELASQUEZ',
						UltimaFecha = NOW();

