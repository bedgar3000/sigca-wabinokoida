<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion-".$AnioActual.$MesActual.$DiaActual."_".$HorasActual.$MinutosActual.$SegundosActual.".sql", "w+");
//	$__archivo = fopen("_"."$modulo-$accion.sql", "w+");
##############################################################################/
##	INTERFASE DE CUENTAS POR PAGAR (BONO DE ALIMENTACIÓN)
##############################################################################/
if ($modulo == "formulario") {
	//	calcular
	if ($accion == "calcular") {
		mysql_query("BEGIN");
		##	-----------------
		##	centro de costo por default
		$CodCentroCosto = getVar3("SELECT CodCentroCosto FROM ac_mastcentrocosto WHERE Codigo = '".$_PARAMETRO["CCOSTOCXP"]."'");
		##	bono de alimentación
		$sql = "SELECT
					ba.*,
					tn.Nomina,
					pv.CodCuenta,
					pv.CodCuentaPub20
				FROM
					rh_bonoalimentacion ba
					INNER JOIN tiponomina tn On (tn.CodTipoNom = ba.CodTipoNom)
					LEFT JOIN pv_partida pv ON (pv.cod_partida = ba.cod_partida)
				WHERE
					ba.Anio = '$fAnio'
					AND ba.CodOrganismo = '$fCodOrganismo'
					AND ba.CodBonoAlim = '$fCodBonoAlim'";
		$field_bono = getRecord($sql);
		##	valido
		if (!trim($fCodOrganismo)) die("Debe seleccionar el Organismo");
		elseif (!trim($fCodTipoNom)) die("Debe seleccionar la N&oacute;mina");
		elseif (!trim($fMes)) die("Debe seleccionar el Periodo");
		elseif (!trim($fCodBonoAlim)) die("Debe seleccionar el Proceso");
		elseif (!$field_bono['CodPresupuesto']) die("No seleccion&oacute; una Categor&iacute;a Program&aacute;tica para este Proceso");
		elseif (!$field_bono['CodFuente']) die("No seleccion&oacute; una Fuente de Financiamiento para este Proceso");
		elseif (!$field_bono['CodTipoDocumento']) die("No seleccion&oacute; un Tipo de Documento para este Proceso");
		##	obligaciones transferidas
		$filtro_transferidas = '';
		$sql = "SELECT * FROM pr_obligacionesbono WHERE FlagTransferido = 'S'";
		$field_transferidas = getRecords($sql);
		foreach ($field_transferidas as $ft) {
			$filtro_transferidas .= " AND (CodPersona <> '$ft[CodPersona]')";
		}
		##	elimino las obligaciones no transferidas
		execute("DELETE FROM pr_obligacionesbono WHERE CodBonoAlim = '$fCodBonoAlim' AND FlagTransferido <> 'S'");
		##	cálculo
		$i = 0;
		$sql = "SELECT *
				FROM rh_bonoalimentaciondet
				WHERE Anio = '$fAnio' AND CodOrganismo = '$fCodOrganismo' AND CodBonoAlim = '$fCodBonoAlim' $filtro_transferidas
				ORDER BY CodPersona";
		$field_bono_detalle = getRecords($sql);
		foreach ($field_bono_detalle as $fbd) {
			$Secuencia = codigo('pr_obligacionesbono','Secuencia',6,['Anio'],[$fAnio]);
			$CodObligacionBono = $fAnio.$Secuencia;
			$Comentarios = "BONO DE ALIMENTACIÓN NÓMINA DE $field_bono[Nomina] DEL ".formatFechaDMA($field_bono['FechaInicio'])." AL ".formatFechaDMA($field_bono['FechaFin']);
			$NroControl = $fCodOrganismo.$fAnio.$fMes.$fCodTipoNom.$field_bono['CodTipoDocumento'].$fCodBonoAlim;
			##	
			$sql = "SELECT * FROM pr_obligacionesbono WHERE CodBonoAlim = '$fCodBonoAlim' AND Periodo = '$field_bono[Periodo]' AND FlagTransferido = 'S'";
			$field_valido = getRecords($sql);
			if (count($field_valido)) die('Error al calcular obligaciones ya transferidas');
			##	inserto
			$sql = "INSERT INTO pr_obligacionesbono
					SET
						CodObligacionBono = '$CodObligacionBono',
						CodOrganismo = '$fCodOrganismo',
						CodCentroCosto = '$CodCentroCosto',
						FechaRegistro = '$FechaActual',
						Periodo = '$field_bono[Periodo]',
						MontoObligacion = '$fbd[TotalPagar]',
						CodPresupuesto = '$field_bono[CodPresupuesto]',
						CodFuente = '$field_bono[CodFuente]',
						CalculadoPor = '$_SESSION[CODPERSONA_ACTUAL]',
						Comentarios = '$Comentarios',
						ComentariosAdicional = '$Comentarios',
						CodBonoAlim = '$fCodBonoAlim',
						CodProveedor = '$fbd[CodPersona]',
						CodTipoDocumento = '$field_bono[CodTipoDocumento]',
						NroControl = '$NroControl',
						NroFactura = '$NroControl',
						Anio = '$fAnio',
						Secuencia = '$Secuencia',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()";
			execute($sql);
			##	cuentas/partidas
			++$i;
			$sql = "INSERT INTO pr_obligacionesbonocuenta
					SET
						CodObligacionBono = '$CodObligacionBono',
						Secuencia = '$i',
						CodCentroCosto = '$CodCentroCosto',
						CodCuenta = '$field_bono[CodCuenta]',
						CodCuentaPub20 = '$field_bono[CodCuentaPub20]',
						cod_partida = '$field_bono[cod_partida]',
						Monto = '$fbd[TotalPagar]',
						CodOrganismo = '$fCodOrganismo',
						CodPresupuesto = '$field_bono[CodPresupuesto]',
						CodFuente = '$field_bono[CodFuente]',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	-----------------
		mysql_query("COMMIT");
	}
	//	consolidar
	elseif ($accion == "consolidar") {
		mysql_query("BEGIN");
		##	-----------------
		##	bono de alimentación
		$sql = "SELECT
					ba.*,
					tn.Nomina
				FROM
					rh_bonoalimentacion ba
					INNER JOIN tiponomina tn On (tn.CodTipoNom = ba.CodTipoNom)
				WHERE
					ba.Anio = '$fAnio'
					AND ba.CodOrganismo = '$fCodOrganismo'
					AND ba.CodBonoAlim = '$fCodBonoAlim'";
		$field_bono = getRecord($sql);
		##	consulto el proveedor del organismo
		$sql = "SELECT
					o.CodPersona,
					p.NomCompleto AS NomPersona
				FROM
					mastorganismos o
					INNER JOIN mastpersonas p ON (o.CodPersona = p.CodPersona)
				WHERE o.CodOrganismo = '".$fCodOrganismo."'";
		$field_proveedor = getRecord($sql);
		if (!count($field_proveedor)) die("Debe asociar una Persona al Organismo para Consolidar.");
		##	
		if (!count($CodObligacionBono)) die("Debe seleccionar las obligaciones a consolidar");
		$filtro = "";
		foreach ($CodObligacionBono as $_CodObligacionBono) {
			if ($filtro != "") $filtro .= " OR ";
			$filtro .= "(CodObligacionBono = '".$_CodObligacionBono."')";
		}
		$filtro = "AND ($filtro)";
		##	
		$sql = "SELECT * FROM pr_obligacionesbono WHERE (FlagTransferido = 'S' OR FlagVerificado = 'S' OR FlagConsolidado = 'S') $filtro ";
		$field_valido = getRecords($sql);
		if (count($field_valido)) die('No puede consolidar obligaciones Transferidas, Verificadas o Consolidadas');
		##	obtengo la obligación
		$sql = "SELECT
					CodOrganismo,
					CodCentroCosto,
					Periodo,
					SUM(MontoObligacion) AS MontoObligacion,
					CodPresupuesto,
					CodFuente,
					CodBonoAlim,
					CodTipoDocumento,
					NroControl,
					NroFactura,
					Anio
				FROM pr_obligacionesbono
				WHERE 1 $filtro
				GROUP BY CodTipoDocumento, NroControl";
		$field_obligacion = getRecord($sql);
		##	obtengo las cuentas
		$sql = "SELECT
					CodCentroCosto,
					CodCuenta,
					CodCuentaPub20,
					cod_partida,
					SUM(Monto) AS Monto,
					CodOrganismo,
					CodPresupuesto,
					CodFuente
				FROM pr_obligacionesbonocuenta
				WHERE 1 $filtro
				GROUP BY CodOrganismo, CodPresupuesto, CodFuente, cod_partida, CodCuenta, CodCuentaPub20, CodCentroCosto";
		$field_obligacion_cuentas = getRecords($sql);
		##	elimino las obligaciones seleccionadas
		execute("DELETE FROM pr_obligacionesbono WHERE 1 $filtro");
		##	
		$Secuencia = codigo('pr_obligacionesbono','Secuencia',6,['Anio'],[$field_obligacion['Anio']]);
		$CodObligacionBono = $field_obligacion['Anio'].$Secuencia;
		$Comentarios = "BONO DE ALIMENTACIÓN NÓMINA DE $field_bono[Nomina] DEL ".formatFechaDMA($field_bono['FechaInicio'])." AL ".formatFechaDMA($field_bono['FechaFin']);
		##	inserto
		$sql = "INSERT INTO pr_obligacionesbono
				SET
					CodObligacionBono = '$CodObligacionBono',
					CodOrganismo = '$field_obligacion[CodOrganismo]',
					CodCentroCosto = '$field_obligacion[CodCentroCosto]',
					FechaRegistro = '$FechaActual',
					Periodo = '$PeriodoActual',
					MontoObligacion = '$field_obligacion[MontoObligacion]',
					CodPresupuesto = '$field_obligacion[CodPresupuesto]',
					CodFuente = '$field_obligacion[CodFuente]',
					CalculadoPor = '$_SESSION[CODPERSONA_ACTUAL]',
					Comentarios = '$Comentarios',
					ComentariosAdicional = '$Comentarios',
					CodBonoAlim = '$fCodBonoAlim',
					CodProveedor = '$field_proveedor[CodPersona]',
					CodTipoDocumento = '$field_obligacion[CodTipoDocumento]',
					NroControl = '$field_obligacion[NroControl]',
					NroFactura = '$field_obligacion[NroControl]',
					Anio = '$field_obligacion[Anio]',
					Secuencia = '$Secuencia',
					FlagConsolidado = 'S',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()";
		execute($sql);
		$i = 0;
		foreach ($field_obligacion_cuentas as $foc) {
			##	cuentas/partidas
			++$i;
			$sql = "INSERT INTO pr_obligacionesbonocuenta
					SET
						CodObligacionBono = '$CodObligacionBono',
						Secuencia = '$i',
						CodCentroCosto = '$foc[CodCentroCosto]',
						CodCuenta = '$foc[CodCuenta]',
						CodCuentaPub20 = '$foc[CodCuentaPub20]',
						cod_partida = '$foc[cod_partida]',
						Monto = '$foc[Monto]',
						CodOrganismo = '$foc[CodOrganismo]',
						CodPresupuesto = '$foc[CodPresupuesto]',
						CodFuente = '$foc[CodFuente]',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	-----------------
		mysql_query("COMMIT");
	}
	//	verificar
	elseif ($accion == "verificar") {
		mysql_query("BEGIN");
		##	-----------------
		##	valido
		for ($i=0; $i < count($cod_partida); $i++) {
            list($MontoAjustado, $MontoCompromiso, $PreCompromiso, $CotizacionesAsignadas) = disponibilidadPartida2($Ejercicio[$i], $CodOrganismo, $cod_partida[$i], $CodPresupuesto[$i], $CodFuente[$i]);
            $MontoAjustado = round(floatval($MontoAjustado), 2);
            $MontoCompromiso = round(floatval($MontoCompromiso), 2);
            $Disponible = $MontoAjustado - $MontoCompromiso;
            $Diferencia = round(floatval($Disponible), 2) - $Monto[$i];
            if ($Diferencia < 0) die("Se encontraron partidas sin disponibilidad presupuestaria.");
		}
		##	actualizo estado
		$sql = "UPDATE pr_obligacionesbono
				SET
					FlagVerificado = 'S',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodObligacionBono = '$CodObligacionBono'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
}
elseif ($modulo == "validar") {
	//	verificar
	if ($accion == "verificar") {
		$sql = "SELECT * FROM pr_obligacionesbono WHERE CodObligacionBono = '$CodObligacionBono[0]'";
		$field = getRecord($sql);
		if ($field['FlagVerificado'] == 'S') die("La obligaci&oacute;n ya se encuentra verificada.");
		elseif ($field['FlagTransferido'] == 'S') die("La obligaci&oacute;n ya se encuentra transferida.");
	}
	//	generar
	elseif ($accion == "generar") {
		$sql = "SELECT * FROM pr_obligacionesbono WHERE CodObligacionBono = '$CodObligacionBono[0]'";
		$field = getRecord($sql);
		if ($field['FlagVerificado'] != 'S') die("La obligaci&oacute;n debe ser verificada.");
		elseif ($field['FlagTransferido'] == 'S') die("La obligaci&oacute;n ya se encuentra transferida.");
	}
}
?>