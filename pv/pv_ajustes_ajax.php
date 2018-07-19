<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion".".sql", "w+");
##############################################################################/
##	Ajustes de Presupuesto (NUEVO, MODIFICAR, APROBAR, AJAX)
##############################################################################/
if ($modulo == "formulario") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		##	-----------------
		##	valido
		if (!trim($CodOrganismo) || !trim($CodPresupuesto) || !trim($Fecha) || !trim($Periodo) || !trim($Descripcion) || !trim($Tipo)) die("Debe llenar los campos (*) obligatorios.");
		elseif ($Tipo == "CA" && !trim($CodFuente)) die("Debe llenar los campos (*) obligatorios.");
		elseif (($Tipo == 'TP' || $Tipo == 'TC' || $Tipo == 'RT') && setNumero($TotalDebitos) != setNumero($TotalCreditos)) die("Balance Total D&eacute;bitos no puede ser distinta a Total Cr&eacute;ditos");
		elseif (!validateDate($Fecha,'d-m-Y')) die("Formato <strong>Fecha</strong> incorrecta");
		elseif (!validateDate($Periodo,'Y-m')) die("Formato <strong>Periodo</strong> incorrecto");
		##	codigo
		$CodAjuste = codigo('pv_ajustes','CodAjuste',4,['CodOrganismo'],[$CodOrganismo]);
		##	inserto
		$sql = "INSERT INTO pv_ajustes
				SET
					CodOrganismo = '".$CodOrganismo."',
					CodAjuste = '".$CodAjuste."',
					CodPresupuesto = '".$CodPresupuesto."',
					Periodo = '".$Periodo."',
					Fecha = '".formatFechaAMD($Fecha)."',
					MontoAprobado = '".setNumero($MontoAprobado)."',
					TotalDebitos = '".setNumero($TotalDebitos)."',
					TotalCreditos = '".setNumero($TotalCreditos)."',
					Descripcion = '".$Descripcion."',
					Tipo = '".$Tipo."',
					".(($Tipo=='CA')?"CodFuente = '$CodFuente',":"")."
					NroGaceta = '".$NroGaceta."',
					FechaGaceta = '".formatFechaAMD($FechaGaceta)."',
					NroResolucion = '".$NroResolucion."',
					FechaResolucion = '".formatFechaAMD($FechaResolucion)."',
					PreparadoPor = '".$PreparadoPor."',
					FechaPreparado = '".formatFechaAMD($FechaPreparado)."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		##	Partidas Cedentes
		$Secuencia = 0;
		for ($i=0; $i < count($partidac_cod_partida); $i++) {
			$Porcentaje = floatval(setNumero($partidac_MontoAjuste[$i]) * 100 / setNumero($partidac_MontoDisponible[$i]));
			$MontoParametro = setNumero($partidac_MontoDisponible[$i]) * 80 / 100;
			##	valido
			if (is_nan(setNumero($partidac_MontoAjuste[$i])) || !(setNumero($partidac_MontoAjuste[$i]))) die("Monto Ajuste incorrecto");
			elseif ($Tipo == 'TP' && setNumero($partidac_MontoAjuste[$i]) > setNumero($partidac_MontoDisponible[$i])) die("El Monto de la partida Cedente <strong>$partidac_cod_partida[$i]</strong> no puede exceder de <strong>".number_format(setNumero($partidac_MontoDisponible[$i]),2,',','.')."</strong> (80% del Monto Aprobado).");
			elseif (setNumero($partidac_MontoAjuste[$i]) > setNumero($partidac_MontoDisponible[$i])) die("El Monto de la partida Cedente <strong>$partidac_cod_partida[$i]</strong> no puede exceder de <strong>".number_format(setNumero($partidac_MontoDisponible[$i]),2,',','.')."</strong>.");
			else {
				$sql = "SELECT *
						FROM pv_ajustesdet
						WHERE
							CodOrganismo = '$CodOrganismo'
							AND CodAjuste = '$CodAjuste'
							AND cod_partida = '$partidac_cod_partida[$i]'
							AND CodPresupuesto = '$partidac_CodPresupuesto[$i]'
							AND CodFuente = '$partidac_CodFuente[$i]'";
				$field_detalle = getRecord($sql);
				if (count($field_detalle)) die("Se encontr&oacute; la partida receptora: $partidac_CategoriaProg[$i] $partidac_CodFuente[$i] $partidac_cod_partida[$i] repetida");
			}
			$sql = "INSERT INTO pv_ajustesdet
					SET
						CodOrganismo = '".$CodOrganismo."',
						CodAjuste = '".$CodAjuste."',
						cod_partida = '".$partidac_cod_partida[$i]."',
						MontoDisponible = '".setNumero($partidac_MontoDisponible[$i])."',
						MontoAjuste = '".setNumero($partidac_MontoAjuste[$i])."',
						CodPresupuesto = '".$partidac_CodPresupuesto[$i]."',
						CodFuente = '".$partidac_CodFuente[$i]."',
						Tipo = 'D',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	Partidas Receptoras
		$Secuencia = 0;
		for ($i=0; $i < count($partidar_cod_partida); $i++) {
			if (is_nan(setNumero($partidar_MontoAjuste[$i])) || !(setNumero($partidar_MontoAjuste[$i]))) die("Monto Ajuste incorrecto");
			else {
				$sql = "SELECT *
						FROM pv_ajustesdet
						WHERE
							CodOrganismo = '$CodOrganismo'
							AND CodAjuste = '$CodAjuste'
							AND cod_partida = '$partidar_cod_partida[$i]'
							AND CodPresupuesto = '$partidar_CodPresupuesto[$i]'
							AND CodFuente = '$partidar_CodFuente[$i]'";
				$field_detalle = getRecord($sql);
				if (count($field_detalle)) die("Se encontr&oacute; la partida receptora: $partidar_CategoriaProg[$i] $partidar_CodFuente[$i] $partidar_cod_partida[$i] repetida");
			}
			$sql = "INSERT INTO pv_ajustesdet
					SET
						CodOrganismo = '".$CodOrganismo."',
						CodAjuste = '".$CodAjuste."',
						cod_partida = '".$partidar_cod_partida[$i]."',
						CodPresupuesto = '".$partidar_CodPresupuesto[$i]."',
						CodFuente = '".$partidar_CodFuente[$i]."',
						MontoDisponible = '".setNumero($partidar_MontoDisponible[$i])."',
						MontoAjuste = '".setNumero($partidar_MontoAjuste[$i])."',
						Tipo = 'I',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	-----------------
		mysql_query("COMMIT");
	}
	//	modificar
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		##	-----------------
		##	valido
		if (!trim($CodOrganismo) || !trim($CodPresupuesto) || !trim($Fecha) || !trim($Periodo) || !trim($Descripcion) || !trim($Tipo)) die("Debe llenar los campos (*) obligatorios.");
		elseif ($Tipo == "CA" && !trim($CodFuente)) die("Debe llenar los campos (*) obligatorios.");
		elseif (($Tipo == 'TP' || $Tipo == 'TC' || $Tipo == 'RT') && setNumero($TotalDebitos) != setNumero($TotalCreditos)) die("Balance Total D&eacute;bitos no puede ser distinta a Total Cr&eacute;ditos");
		elseif (!validateDate($Fecha,'d-m-Y')) die("Formato <strong>Fecha</strong> incorrecta");
		elseif (!validateDate($Periodo,'Y-m')) die("Formato <strong>Periodo</strong> incorrecto");
		##	actualizo
		$sql = "UPDATE pv_ajustes
				SET
					Periodo = '".$Periodo."',
					Fecha = '".formatFechaAMD($Fecha)."',
					MontoAprobado = '".setNumero($MontoAprobado)."',
					TotalDebitos = '".setNumero($TotalDebitos)."',
					TotalCreditos = '".setNumero($TotalCreditos)."',
					Descripcion = '".$Descripcion."',
					".(($Tipo=='CA')?"CodFuente = '$CodFuente',":"")."
					NroGaceta = '".$NroGaceta."',
					FechaGaceta = '".formatFechaAMD($FechaGaceta)."',
					NroResolucion = '".$NroResolucion."',
					FechaResolucion = '".formatFechaAMD($FechaResolucion)."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					CodAjuste = '".$CodAjuste."'";
		execute($sql);
		##	vacio detalle
		$sql = "DELETE FROM pv_ajustesdet WHERE CodOrganismo = '".$CodOrganismo."' AND CodAjuste = '".$CodAjuste."'";
		execute($sql);
		##	Partidas Cedentes
		$Secuencia = 0;
		for ($i=0; $i < count($partidac_cod_partida); $i++) {
			$Porcentaje = floatval(setNumero($partidac_MontoAjuste[$i]) * 100 / setNumero($partidac_MontoDisponible[$i]));
			$MontoParametro = setNumero($partidac_MontoDisponible[$i]) * 80 / 100;
			##	valido
			if (is_nan(setNumero($partidac_MontoAjuste[$i])) || !(setNumero($partidac_MontoAjuste[$i]))) die("Monto Ajuste incorrecto");
			elseif ($Tipo == 'TP' && $Porcentaje > 80) die("El Monto de la partida Cedente <strong>$partidac_cod_partida[$i]</strong> no puede exceder de <strong>".number_format($MontoParametro,2,',','.')."</strong> (80% del Monto Disponible).");
			else {
				$sql = "SELECT *
						FROM pv_ajustesdet
						WHERE
							CodOrganismo = '$CodOrganismo'
							AND CodAjuste = '$CodAjuste'
							AND cod_partida = '$partidac_cod_partida[$i]'
							AND CodPresupuesto = '$partidac_CodPresupuesto[$i]'
							AND CodFuente = '$partidac_CodFuente[$i]'";
				$field_detalle = getRecord($sql);
				if (count($field_detalle)) die("Se encontr&oacute; la partida receptora: $partidac_CategoriaProg[$i] $partidac_CodFuente[$i] $partidac_cod_partida[$i] repetida");
			}
			$sql = "INSERT INTO pv_ajustesdet
					SET
						CodOrganismo = '".$CodOrganismo."',
						CodAjuste = '".$CodAjuste."',
						cod_partida = '".$partidac_cod_partida[$i]."',
						MontoDisponible = '".setNumero($partidac_MontoDisponible[$i])."',
						MontoAjuste = '".setNumero($partidac_MontoAjuste[$i])."',
						CodPresupuesto = '".$partidac_CodPresupuesto[$i]."',
						CodFuente = '".$partidac_CodFuente[$i]."',
						Tipo = 'D',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	Partidas Receptoras
		$Secuencia = 0;
		for ($i=0; $i < count($partidar_cod_partida); $i++) {
			if (is_nan(setNumero($partidar_MontoAjuste[$i])) || !(setNumero($partidar_MontoAjuste[$i]))) die("Monto Ajuste incorrecto");
			else {
				$sql = "SELECT *
						FROM pv_ajustesdet
						WHERE
							CodOrganismo = '$CodOrganismo'
							AND CodAjuste = '$CodAjuste'
							AND cod_partida = '$partidar_cod_partida[$i]'
							AND CodPresupuesto = '$partidar_CodPresupuesto[$i]'
							AND CodFuente = '$partidar_CodFuente[$i]'";
				$field_detalle = getRecord($sql);
				if (count($field_detalle)) die("Se encontr&oacute; la partida receptora: $partidar_CategoriaProg[$i] $partidar_CodFuente[$i] $partidar_cod_partida[$i] de repetida");
			}
			$sql = "INSERT INTO pv_ajustesdet
					SET
						CodOrganismo = '".$CodOrganismo."',
						CodAjuste = '".$CodAjuste."',
						cod_partida = '".$partidar_cod_partida[$i]."',
						MontoDisponible = '".setNumero($partidar_MontoDisponible[$i])."',
						MontoAjuste = '".setNumero($partidar_MontoAjuste[$i])."',
						CodPresupuesto = '".$partidar_CodPresupuesto[$i]."',
						CodFuente = '".$partidar_CodFuente[$i]."',
						Tipo = 'I',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	-----------------
		mysql_query("COMMIT");
	}
	//	aprobar
	elseif ($accion == "aprobar") {
		mysql_query("BEGIN");
		##	-----------------
		$sql = "SELECT
					aj.*,
					p.Ejercicio
				FROM
					pv_ajustes aj
					INNER JOIN pv_presupuesto p ON (p.CodOrganismo = aj.CodOrganismo AND p.CodPresupuesto = aj.CodPresupuesto)
				WHERE
					aj.CodOrganismo = '".$CodOrganismo."' AND
					aj.CodAjuste = '".$CodAjuste."'";
		$field = getRecord($sql);
		##	valido
		if ($field['Tipo'] == 'CA' && $field['MontoAprobado'] != $field['TotalCreditos']) die("Monto Aprobado no puede ser distinto a Total Cr&eacute;ditos");
		##	actualizar
		$sql = "UPDATE pv_ajustes
				SET
					Estado = '".$Estado."',
					AprobadoPor = '".$AprobadoPor."',
					FechaAprobado = '".formatFechaAMD($FechaAprobado)."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					CodAjuste = '".$CodAjuste."'";
		execute($sql);
		##	Partidas Cedentes
		$ErrorCedentes = "";
		for ($i=0; $i < count($partidac_cod_partida); $i++) {
			$sql = "SELECT
						ajd.*,
						pd.MontoAjustado,
						pd.MontoCompromiso,
						ppto.CategoriaProg
					FROM
						pv_ajustesdet ajd
						INNER JOIN pv_ajustes aj ON (aj.CodOrganismo = ajd.CodOrganismo AND aj.CodAjuste = ajd.CodAjuste)
						LEFT JOIN pv_presupuestodet pd ON (pd.CodOrganismo = ajd.CodOrganismo AND pd.CodPresupuesto = ajd.CodPresupuesto AND pd.cod_partida = ajd.cod_partida AND pd.CodFuente = ajd.CodFuente)
						LEFT JOIN pv_presupuesto ppto ON (ppto.CodOrganismo = pd.CodOrganismo AND ppto.CodPresupuesto = pd.CodPresupuesto)
					WHERE
						ajd.CodOrganismo = '".$CodOrganismo."' AND
						ajd.CodAjuste = '".$CodAjuste."' AND
						ajd.CodPresupuesto = '".$partidac_CodPresupuesto[$i]."' AND
						ajd.CodFuente = '".$partidac_CodFuente[$i]."' AND
						ajd.cod_partida = '".$partidac_cod_partida[$i]."'";
			$field_detalle = getRecord($sql);
			$MontoDisponibleReal = floatval($field_detalle['MontoAjustado'] - $field_detalle['MontoCompromiso']);
			##	valido
			$field_detalle['MontoAjuste'] = round($field_detalle['MontoAjuste'],2);
			$MontoDisponibleReal = round($MontoDisponibleReal,2);
			if ($field_detalle['MontoAjuste'] > $MontoDisponibleReal) {
				$ErrorCedentes .= "<strong>$field_detalle[CategoriaProg]-$partidac_CodFuente[$i]-$partidac_cod_partida[$i]</strong>: ".number_format($field_detalle['MontoAjuste'],2,',','.')." > ".number_format($MontoDisponibleReal,2,',','.').".<br>";
				//die("Monto de Partida Cedente <strong>$field_detalle[CategoriaProg]-$partidac_CodFuente[$i]-$partidac_cod_partida[$i]</strong> (".number_format($field_detalle['MontoAjuste'],2,',','.').") no puede ser mayor al Monto Disponible (".number_format($MontoDisponibleReal,2,',','.').").");
			}
			##	actualizo
			$sql = "UPDATE pv_presupuestodet
					SET
						MontoAjustado = MontoAjustado - ".floatval($field_detalle['MontoAjuste']).",
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()
					WHERE
						CodOrganismo = '".$field['CodOrganismo']."' AND
						CodPresupuesto = '".$partidac_CodPresupuesto[$i]."' AND
						CodFuente = '".$partidac_CodFuente[$i]."' AND
						cod_partida = '".$partidac_cod_partida[$i]."'";
			//execute($sql);
		}
		##	Partidas Receptoras
		for ($i=0; $i < count($partidar_cod_partida); $i++) {
			if ($Tipo == 'RI') {
				$Secuencia = codigo('pv_compromisos','Secuencia',11,['CodPresupuesto','cod_partida'],[$field['CodPresupuesto'],$partidar_cod_partida[$i]]);
				##	
				$sql = "INSERT INTO pv_compromisos
						SET
							CodPresupuesto = '".$partidar_CodPresupuesto[$i]."',
							Ejercicio = '".$partidar_Ejercicio[$i]."',
							CodFuente = '".$partidar_CodFuente[$i]."',
							cod_partida = '".$partidar_cod_partida[$i]."',
							Secuencia = '".$Secuencia."',
							Anio = '".$field['Ejercicio']."',
							CodOrganismo = '".$field['CodOrganismo']."',
							Periodo = '".$field['Periodo']."',
							Fecha = '".$field['Fecha']."',
							Monto = -".floatval($field_detalle['MontoAjuste']).",
							Origen = 'PV',
							Estado = 'CO',
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()";
				execute($sql);
				$sql = "INSERT INTO pv_causados
						SET
							CodPresupuesto = '".$partidar_CodPresupuesto[$i]."',
							Ejercicio = '".$partidar_Ejercicio[$i]."',
							CodFuente = '".$partidar_CodFuente[$i]."',
							cod_partida = '".$partidar_cod_partida[$i]."',
							Secuencia = '".$Secuencia."',
							Anio = '".$field['Ejercicio']."',
							CodOrganismo = '".$field['CodOrganismo']."',
							Periodo = '".$field['Periodo']."',
							Fecha = '".$field['Fecha']."',
							Monto = -".floatval($field_detalle['MontoAjuste']).",
							Origen = 'PV',
							Estado = 'CA',
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()";
				execute($sql);
				$sql = "INSERT INTO pv_pagados
						SET
							CodPresupuesto = '".$partidar_CodPresupuesto[$i]."',
							Ejercicio = '".$partidar_Ejercicio[$i]."',
							CodFuente = '".$partidar_CodFuente[$i]."',
							cod_partida = '".$partidar_cod_partida[$i]."',
							Secuencia = '".$Secuencia."',
							Anio = '".$field['Ejercicio']."',
							CodOrganismo = '".$field['CodOrganismo']."',
							Periodo = '".$field['Periodo']."',
							Fecha = '".$field['Fecha']."',
							Monto = -".floatval($field_detalle['MontoAjuste']).",
							Origen = 'PV',
							Estado = 'PA',
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()";
				execute($sql);
			} else {
				$sql = "SELECT
							ajd.*,
							(pd.MontoAjustado - pd.MontoCompromiso) AS MontoDisponibleReal
						FROM
							pv_ajustesdet ajd
							INNER JOIN pv_ajustes aj ON (aj.CodOrganismo = ajd.CodOrganismo AND aj.CodAjuste = ajd.CodAjuste)
							LEFT JOIN pv_presupuestodet pd ON (pd.CodOrganismo = aj.CodOrganismo AND pd.CodPresupuesto = aj.CodPresupuesto AND pd.cod_partida = ajd.cod_partida)
						WHERE
							ajd.CodOrganismo = '".$CodOrganismo."' AND
							ajd.CodAjuste = '".$CodAjuste."' AND
							ajd.CodPresupuesto = '".$partidar_CodPresupuesto[$i]."' AND
							ajd.CodFuente = '".$partidar_CodFuente[$i]."' AND
							ajd.cod_partida = '".$partidar_cod_partida[$i]."'";
				$field_detalle = getRecord($sql);
				##	actualizo
				$sql = "INSERT INTO pv_presupuestodet
						SET
							CodOrganismo = '".$field['CodOrganismo']."',
							CodPresupuesto = '".$partidar_CodPresupuesto[$i]."',
							CodFuente = '".$partidar_CodFuente[$i]."',
							cod_partida = '".$partidar_cod_partida[$i]."',
							MontoAjustado = ".floatval($field_detalle['MontoAjuste']).",
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()
						ON DUPLICATE KEY UPDATE
							MontoAjustado = MontoAjustado + ".floatval($field_detalle['MontoAjuste']).",
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()";
				execute($sql);
			}
		}
		$sql = "UPDATE pv_presupuestodet pv
				SET pv.MontoAjustado = pv.MontoAprobado + COALESCE((SELECT SUM(aj.MontoAjuste)
																			 FROM pv_ajustesdet aj
																			 INNER JOIN pv_ajustes a ON (a.CodOrganismo = aj.CodOrganismo AND a.CodAjuste = aj.CodAjuste)
																			 WHERE
																					aj.CodPresupuesto = pv.CodPresupuesto
																					AND aj.CodFuente = pv.CodFuente
																					AND aj.cod_partida = pv.cod_partida
																					AND aj.Tipo = 'I'
																					AND a.Estado = 'AP'),0) - 
														  COALESCE((SELECT SUM(aj.MontoAjuste)
																			 FROM pv_ajustesdet aj
																			 INNER JOIN pv_ajustes a ON (a.CodOrganismo = aj.CodOrganismo AND a.CodAjuste = aj.CodAjuste)
																			 WHERE
																					aj.CodPresupuesto = pv.CodPresupuesto
																					AND aj.CodFuente = pv.CodFuente
																					AND aj.cod_partida = pv.cod_partida
																					AND aj.Tipo = 'D'
																					AND a.Estado = 'AP'),0);";
		execute($sql);
		##	
		if ($ErrorCedentes) die("Montos de Partidas a Ceder no pueden ser mayor al Monto Disponible:<br>".$ErrorCedentes);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	anular
	elseif ($accion == "anular") {
		mysql_query("BEGIN");
		##	-----------------
		$field = getRecord("SELECT * FROM pv_ajustes WHERE CodOrganismo = '".$CodOrganismo."' AND CodAjuste = '".$CodAjuste."'");
		if ($Estado == 'AP') {
			##	Partidas Cedentes
			for ($i=0; $i < count($partidac_cod_partida); $i++) {
				$sql = "SELECT
							ajd.*,
							(pd.MontoAjustado - pd.MontoCompromiso) AS MontoDisponibleReal
						FROM
							pv_ajustesdet ajd
							INNER JOIN pv_ajustes aj ON (aj.CodOrganismo = ajd.CodOrganismo AND aj.CodAjuste = ajd.CodAjuste)
							INNER JOIN pv_presupuestodet pd ON (pd.CodOrganismo = aj.CodOrganismo AND pd.CodPresupuesto = aj.CodPresupuesto AND pd.cod_partida = ajd.cod_partida)
						WHERE
							ajd.CodOrganismo = '".$CodOrganismo."' AND
							ajd.CodAjuste = '".$CodAjuste."' AND
							ajd.CodPresupuesto = '".$partidac_CodPresupuesto[$i]."' AND
							ajd.CodFuente = '".$partidac_CodFuente[$i]."' AND
							ajd.cod_partida = '".$partidac_cod_partida[$i]."'";
				$field_detalle = getRecord($sql);
				##	actualizo
				$sql = "UPDATE pv_presupuestodet
						SET
							MontoAjustado = MontoAjustado + ".floatval($field_detalle['MontoAjuste']).",
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()
						WHERE
							CodOrganismo = '".$field['CodOrganismo']."' AND
							CodPresupuesto = '".$partidac_CodPresupuesto[$i]."' AND
							CodFuente = '".$partidac_CodFuente[$i]."' AND
							cod_partida = '".$partidac_cod_partida[$i]."'";
				execute($sql);
			}
			##	Partidas Receptoras
			for ($i=0; $i < count($partidar_cod_partida); $i++) {
				$sql = "SELECT
							ajd.*,
							(pd.MontoAjustado - pd.MontoCompromiso) AS MontoDisponibleReal
						FROM
							pv_ajustesdet ajd
							INNER JOIN pv_ajustes aj ON (aj.CodOrganismo = ajd.CodOrganismo AND aj.CodAjuste = ajd.CodAjuste)
							INNER JOIN pv_presupuestodet pd ON (pd.CodOrganismo = aj.CodOrganismo AND pd.CodPresupuesto = aj.CodPresupuesto AND pd.cod_partida = ajd.cod_partida)
						WHERE
							ajd.CodOrganismo = '".$CodOrganismo."' AND
							ajd.CodAjuste = '".$CodAjuste."' AND
							ajd.CodPresupuesto = '".$partidar_CodPresupuesto[$i]."' AND
							ajd.CodFuente = '".$partidar_CodFuente[$i]."' AND
							ajd.cod_partida = '".$partidar_cod_partida[$i]."'";
				$field_detalle = getRecord($sql);
				##	valido
				if ($field_detalle['MontoAjuste'] > $field_detalle['MontoDisponibleReal']) die("No se puede anular el ajuste de la partida <strong>$partidar_cod_partida[$i]</strong>. El monto a Anular es mayor que el Monto Disponible");
				##	actualizo
				$sql = "UPDATE pv_presupuestodet
						SET
							MontoAjustado = MontoAjustado - ".floatval($field_detalle['MontoAjuste']).",
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()
						WHERE
							CodOrganismo = '".$field['CodOrganismo']."' AND
							CodPresupuesto = '".$partidar_CodPresupuesto[$i]."' AND
							CodFuente = '".$partidar_CodFuente[$i]."' AND
							cod_partida = '".$partidar_cod_partida[$i]."'";
				execute($sql);
			}
			##	
			$Estado = 'PR';
		}
		elseif ($Estado == 'PR') $Estado = 'AN';
		##	actualizar
		$sql = "UPDATE pv_ajustes
				SET
					Estado = '".$Estado."',
					AnuladoPor = '".$_SESSION['CODPERSONA_ACTUAL']."',
					FechaAnulado = NOW(),
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					CodAjuste = '".$CodAjuste."'";
		execute($sql);
		##	detalle
		$sql = "UPDATE pv_ajustesdet
				SET
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					CodAjuste = '".$CodAjuste."'";
		execute($sql);
		##	
		$sql = "UPDATE pv_presupuestodet pv
				SET pv.MontoAjustado = pv.MontoAprobado + COALESCE((SELECT SUM(aj.MontoAjuste)
																			 FROM pv_ajustesdet aj
																			 INNER JOIN pv_ajustes a ON (a.CodOrganismo = aj.CodOrganismo AND a.CodAjuste = aj.CodAjuste)
																			 WHERE
																					aj.CodPresupuesto = pv.CodPresupuesto
																					AND aj.CodFuente = pv.CodFuente
																					AND aj.cod_partida = pv.cod_partida
																					AND aj.Tipo = 'I'
																					AND a.Estado = 'AP'),0) - 
														  COALESCE((SELECT SUM(aj.MontoAjuste)
																			 FROM pv_ajustesdet aj
																			 INNER JOIN pv_ajustes a ON (a.CodOrganismo = aj.CodOrganismo AND a.CodAjuste = aj.CodAjuste)
																			 WHERE
																					aj.CodPresupuesto = pv.CodPresupuesto
																					AND aj.CodFuente = pv.CodFuente
																					AND aj.cod_partida = pv.cod_partida
																					AND aj.Tipo = 'D'
																					AND a.Estado = 'AP'),0);";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
}
elseif ($modulo == "validar") {
	//	modificar
	if($accion == "modificar") {
		list($CodOrganismo, $CodAjuste) = explode('_', $codigo);
		$sql = "SELECT Estado FROM pv_ajustes WHERE CodOrganismo = '$CodOrganismo' AND CodAjuste = '$CodAjuste'";
		$Estado = getVar3($sql);
		if ($Estado != 'PR') die('No puede modificar un ajuste <strong>'.printValores('ajustes-estado',$Estado).'</strong>');
	}
	//	aprobar
	elseif($accion == "aprobar") {
		list($CodOrganismo, $CodAjuste) = explode('_', $codigo);
		$sql = "SELECT Estado FROM pv_ajustes WHERE CodOrganismo = '$CodOrganismo' AND CodAjuste = '$CodAjuste'";
		$Estado = getVar3($sql);
		if ($Estado != 'PR') die('No puede aprobar un ajuste <strong>'.printValores('ajustes-estado',$Estado).'</strong>');
	}
	//	anular
	elseif($accion == "anular") {
		list($CodOrganismo, $CodAjuste) = explode('_', $codigo);
		$sql = "SELECT Estado FROM pv_ajustes WHERE CodOrganismo = '$CodOrganismo' AND CodAjuste = '$CodAjuste'";
		$Estado = getVar3($sql);
		if ($Estado == 'AN') die('No puede anular un ajuste <strong>'.printValores('ajustes-estado',$Estado).'</strong>');
	}
}
elseif ($modulo == "ajax") {
	//	insertar linea
	if($accion == "partida_insertar") {
		##	detalle
		$sql = "SELECT
					p.*,
					pd.MontoAprobado,
					pd.MontoAjustado,
					pd.MontoCompromiso
				FROM pv_partida p
				INNER JOIN pv_presupuestodet pd On (pd.cod_partida = p.cod_partida)
				WHERE
					pd.CodOrganismo = '$CodOrganismo' AND
					pd.CodPresupuesto = '$CodPresupuesto' AND
					pd.CodFuente = '$CodFuente' AND
					pd.cod_partida = '$cod_partida'";
		$field = getRecord($sql);
		$MontoDisponible = $field['MontoAjustado'] - $field['MontoCompromiso'];
		##	
		if ($Tipo == 'TP') {
			$sql = "SELECT SUM(MontoAjuste)
					FROM
						pv_ajustesdet ad
						INNER JOIN pv_ajustes a ON (
							a.CodOrganismo = ad.CodOrganismo
							AND a.CodAjuste = ad.CodAjuste
						)
					WHERE
						ad.CodOrganismo = '$CodOrganismo'
						AND ad.CodPresupuesto = '$CodPresupuesto'
						AND ad.CodFuente = '$CodFuente'
						AND ad.cod_partida = '$cod_partida'
						AND ad.Tipo = 'D'
						AND a.Tipo = 'TP'";
			$MontoAjustes = getVar3($sql);
			$MontoAprobado80 = $field['MontoAprobado'] * 80 / 100;
			$MontoDisponible80 = $MontoAprobado80 - $MontoAjustes;
			if ($MontoDisponible > $MontoDisponible80) $MontoDisponible = $MontoDisponible80;
		}
		//$MontoDisponible80 = $MontoAprobado80 - $field['MontoCompromiso'];
		//if ($Tipo == 'TP') $MontoDisponible = $MontoDisponible80;
		$id = str_replace('.', '', $field['cod_partida'].$nro_detalles);
		?>
		<tr class="trListaBody" id="<?=$detalle?>_<?=$id?>" onclick="clk($(this), '<?=$detalle?>', '<?=$detalle?>_<?=$id?>');">
			<td align="center">
				<input type="text" name="<?=$detalle?>_CategoriaProg[]" id="<?=$detalle?>_CategoriaProg_<?=$id?>" value="<?=$CategoriaProg?>" class="cell2" />
				<input type="hidden" name="<?=$detalle?>_Ejercicio[]" id="<?=$detalle?>_Ejercicio_<?=$id?>" value="<?=$Ejercicio?>" />
				<input type="hidden" name="<?=$detalle?>_CodPresupuesto[]" id="<?=$detalle?>_CodPresupuesto_<?=$id?>" value="<?=$CodPresupuesto?>" />
			</td>
            <td>
				<select name="<?=$detalle?>_CodFuente[]" class="cell2 CodFuente" <?=$disabled_ver?>>
					<?=loadSelect2("pv_fuentefinanciamiento","CodFuente","Denominacion",$CodFuente,10)?>
				</select>
            </td>
			<td align="center">
				<input type="hidden" name="<?=$detalle?>_cod_partida[]" value="<?=$field['cod_partida']?>" />
				<?=$field['cod_partida']?>
			</td>
			<td><input type="text" value="<?=htmlentities($field['denominacion'])?>" class="cell2" readonly /></td>
			<td><input type="text" name="<?=$detalle?>_MontoAjuste[]" value="0,00" class="cell currency" style="text-align:right;" onchange="setMontos('<?=$detalle?>');" /></td>
			<td align="right"><?=number_format($field['MontoAprobado'],2,',','.')?></td>
			<td align="right"><?=number_format($field['MontoAjustado'],2,',','.')?></td>
			<td align="right"><?=number_format($field['MontoCompromiso'],2,',','.')?></td>
			<td align="right"><input type="text" name="<?=$detalle?>_MontoDisponible[]" value="<?=number_format($MontoDisponible,2,',','.')?>" class="cell2" style="text-align:right;" readonly /></td>
		</tr>
		<?php
	}
}
?>