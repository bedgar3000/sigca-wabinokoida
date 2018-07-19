<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion-".$AnioActual.$MesActual.$DiaActual."_".$HorasActual.$MinutosActual.$SegundosActual.".sql", "w+");
//	$__archivo = fopen("_"."$modulo-$accion".".sql", "w+");
///////////////////////////////////////////////////////////////////////////////
//	ORDEN DE SERVICIO (NUEVO, MODIFICAR, REVISAR, APROBAR, ANULAR, CERRAR, CONFIRMAR, DESCONFIRMAR)
///////////////////////////////////////////////////////////////////////////////
//	orden de servicio
if ($modulo == "orden_servicio") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-----------------
		list($cod_partida, $CodCuenta, $CodCuentaPub20) = getPartidaCuentaFromIGV($_PARAMETRO["IGVCODIGO"]);
		list($DiaOrden, $MesOrden, $AnioOrden) = split("[./-]", $FechaDocumento);
		$PeriodoOrden = "$AnioOrden-$MesOrden";
		//	inserto orden
		##	genero el nuevo codigo
		$NroOrden = getCodigo_3("lg_ordenservicio", "NroOrden", "Anio", "CodOrganismo", $Anio, $CodOrganismo, 10);
		##	inserto
		$sql = "INSERT INTO lg_ordenservicio
				SET
					Anio = '".$Anio."',
					CodOrganismo = '".$CodOrganismo."',
					NroOrden = '".$NroOrden."',
					Mes = '".$MesOrden."',
					CodDependencia = '".$CodDependencia."',
					CodProveedor = '".$CodProveedor."',
					NomProveedor = '".changeUrl($NomProveedor)."',
					CodFormaPago = '".$CodFormaPago."',
					FechaDocumento = '".formatFechaAMD($FechaDocumento)."',
					DiasPago = '".$DiasPago."',
					CodTipoPago = '".$CodTipoPago."',
					CodTipoServicio = '".$CodTipoServicio."',
					PlazoEntrega = '".$PlazoEntrega."',
					FechaEntrega = '".formatFechaAMD($FechaEntrega)."',
					MontoOriginal = '".setNumero($MontoOriginal)."',
					MontoNoAfecto = '".setNumero($MontoNoAfecto)."',
					MontoIva = '".setNumero($MontoIva)."',
					TotalMontoIva = '".setNumero($TotalMontoIva)."',
					MontoPendiente = '".setNumero($MontoPendiente)."',
					Descripcion = '".changeUrl($Descripcion)."',
					DescAdicional = '".changeUrl($DescAdicional)."',
					Observaciones = '".changeUrl($Observaciones)."',
					FechaValidoDesde = '".formatFechaAMD($FechaValidoDesde)."',
					FechaValidoHasta = '".formatFechaAMD($FechaValidoHasta)."',
					CodCentroCosto = '".$CodCentroCosto."',
					PreparadaPor = '".$PreparadaPor."',
					FechaPreparacion = '".formatFechaAMD($FechaPreparacion)."',
					CodCuenta = '".$CodCuenta."',
					CodCuentaPub20 = '".$CodCuentaPub20."',
					cod_partida = '".$cod_partida."',
					CodPresupuesto = '".$CodPresupuesto."',
					Ejercicio = '".$Ejercicio."',
					CodFuente = '".$CodFuente."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		
		//	inserto detalles
		$_Secuencia = 0;
		$detalle = split(";char:tr;", $detalles);
		foreach ($detalle as $linea) {
			list($_CommoditySub, $_Descripcion, $_CantidadPedida, $_CodUnidadRec, $_CantidadRec, $_PrecioUnit, $_FlagExonerado, $_Total, $_CategoriaProg, $_Ejercicio, $_CodPresupuesto, $_CodFuente, $_FechaEsperadaTermino, $_FechaTermino, $_CodCentroCosto, $_NroActivo, $_FlagTerminado, $_cod_partida, $_CodCuenta, $_CodCuentaPub20, $_Comentarios, $_CodRequerimiento, $_RequerimientoSecuencia, $_CotizacionSecuencia, $_CantidadRequerimiento) = split(";char:td;", $linea);
			//if ($_NroActivo == "") die("El commodity <strong>$_CommoditySub - $_Descripcion</strong> requiere el Nro. de Activo");
			##	inserto
			$sql = "INSERT INTO lg_ordenserviciodetalle
					SET
						Anio = '".$Anio."',
						CodOrganismo = '".$CodOrganismo."',
						NroOrden = '".$NroOrden."',
						Secuencia = '".++$_Secuencia."',
						Mes = '".$MesOrden."',
						CommoditySub = '".$_CommoditySub."',
						Descripcion = '".$_Descripcion."',
						CantidadPedida = '".$_CantidadPedida."',
						CodUnidadRec = '".$_CodUnidadRec."',
						CantidadRec = '".$_CantidadRec."',
						PrecioUnit = '".$_PrecioUnit."',
						Total = '".$_Total."',
						FechaEsperadaTermino = '".$_FechaEsperadaTermino."',
						FechaTermino = '".$_FechaTermino."',
						CodCentroCosto = '".$_CodCentroCosto."',
						NroActivo = '".$_NroActivo."',
						FlagExonerado = '".$_FlagExonerado."',
						FlagTerminado = '".$_FlagTerminado."',
						Comentarios = '".$_Comentarios."',
						cod_partida = '".$_cod_partida."',
						CodCuenta = '".$_CodCuenta."',
						CodCuentaPub20 = '".$_CodCuentaPub20."',
						CodPresupuesto = '".$_CodPresupuesto."',
						Ejercicio = '".$_Ejercicio."',
						CodFuente = '".$_CodFuente."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
			
			//	si la orden la estoy generando desde cotizaciones
			if ($GenerarPendiente == "S") {
				//	actualizo cotizacion
				$sql = "UPDATE lg_cotizacion
						SET Estado = 'AD'
						WHERE CotizacionSecuencia = '".$_CotizacionSecuencia."'";
				execute($sql);
				
				if ($_CantidadRec == $_CantidadRequerimiento) $UpdateEstado = "Estado='CO',"; else $UpdateEstado = "";
				//	actualizo detalle del requerimiento
				$sql = "UPDATE lg_requerimientosdet
						SET
							$UpdateEstado
							Anio = '".$AnioOrden."',
							NroOrden = '".$NroOrden."',
							OrdenSecuencia = '".$_Secuencia."',
							CantidadOrdenCompra = '".$_CantidadRec."'
						WHERE
							CodRequerimiento = '".$_CodRequerimiento."' AND
							Secuencia = '".$_RequerimientoSecuencia."'";
				execute($sql);
				
				//	verifico si completo todos los detalles del requerimeinto
				$sql = "SELECT *
						FROM lg_requerimientosdet
						WHERE
							CodRequerimiento = '".$_CodRequerimiento."' AND
							Estado = 'PE'";
				$query_requerimiento = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
				if (mysql_num_rows($query_requerimiento) == 0) {
					//	completo requerimiento
					$sql = "UPDATE lg_requerimientos
							SET Estado = 'CO'
							WHERE CodRequerimiento = '".$_CodRequerimiento."'";
					execute($sql);
				}
				
				//	inserto en relacion
				$sql = "INSERT INTO lg_cotizacionordenes
						SET
							CotizacionSecuencia = '".$_CotizacionSecuencia."',
							CodRequerimiento = '".$_CodRequerimiento."',
							SecuenciaRequerimiento = '".$_RequerimientoSecuencia."',
							CodOrganismo = '".$CodOrganismo."',
							Anio = '".$AnioOrden."',
							NroOrden = '".$NroOrden."',
							SecuenciaOrden = '".$_Secuencia."',
							TipoOrden = 'OS',
							CantidadOrden = '".$_CantidadPedida."',
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()";
				execute($sql);
			}
		}
		
		//	si la orden la estoy generando desde cotizaciones
		if ($GenerarPendiente == "S") {
			//	consulto los requerimientos 
			$sql = "SELECT c.CotizacionSecuencia
					FROM
						lg_cotizacion c
						INNER JOIN lg_requerimientosdet rd ON (c.CodRequerimiento = rd.CodRequerimiento AND
															   c.Secuencia = rd.Secuencia)
					WHERE
						c.Numero = '".$Numero."' AND
						c.FlagAsignado = 'N' AND
						c.Estado = 'PE' AND
						rd.Estado = 'CO'
					ORDER BY c.Secuencia";
			$query_estado = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field_estado = mysql_fetch_array($query_estado)) {
				//	actualizo cotizacion
				$sql = "UPDATE lg_cotizacion
						SET Estado = 'NA'
						WHERE CotizacionSecuencia = '".$field_estado['CotizacionSecuencia']."'";
				execute($sql);
			}
		}
		
		//	inserto distribucion
		/*$_Secuencia = 0;
		$detalle = split(";char:tr;", $detalles_partida);
		foreach ($detalle as $linea) {
			list($_cod_partida, $_CodCuenta, $_CodCuentaPub20, $_Monto) = split(";char:td;", $linea);
			##	inserto
			$sql = "INSERT INTO lg_distribucionos
					SET
						Anio = '".$Anio."',
						CodOrganismo = '".$CodOrganismo."',
						NroOrden = '".$NroOrden."',
						Secuencia = '".++$_Secuencia."',
						Mes = '".$MesOrden."',
						cod_partida = '".$_cod_partida."',
						CodCuenta = '".$_CodCuenta."',
						CodCuentaPub20 = '".$_CodCuentaPub20."',
						Monto = '".$_Monto."',
						CodCentroCosto = '".$CodCentroCosto."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
			
			##	inserto
			$sql = "INSERT INTO lg_distribucioncompromisos
					SET
						Anio = '".$Anio."',
						CodOrganismo = '".$CodOrganismo."',
						CodProveedor = '".$CodProveedor."',
						CodTipoDocumento = 'OS',
						NroDocumento = '".$NroOrden."',
						Secuencia = '".$_Secuencia."',
						Linea = '1',
						Mes = '".$MesOrden."',
						CodCentroCosto = '".$CodCentroCosto."',
						cod_partida = '".$_cod_partida."',
						Monto = '".$_Monto."',
						Periodo = '".$PeriodoOrden."',
						CodPresupuesto = '".$CodPresupuesto."',
						Origen = 'OS',
						Estado = 'PE',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}*/


		list($_cod_partida_igv, $_CodCuenta_igv, $_CodCuentaPub20_igv) = getPartidaCuentaFromIGV($_PARAMETRO["IGVCODIGO"]);
		##	
		$sql = "SELECT *
				FROM pv_presupuesto
				WHERE CodOrganismo = '$CodOrganismo' AND Ejercicio = '$Ejercicio' AND CategoriaProg = '$_PARAMETRO[IGVCATPROG]'";
		$field_presupuesto = getRecord($sql);
		##	
		$sql = "SELECT CodFuente
				FROM pv_presupuestodet
				WHERE CodOrganismo = '$CodOrganismo' AND CodPresupuesto = '$field_presupuesto[CodPresupuesto]' AND cod_partida = '$_cod_partida_igv';";
		$IgvFuente = getVar3($sql);
		##	
		$_Secuencia = 0;
		$sql = "(
				 SELECT
					Anio,
					NroOrden,
					Ejercicio,
					CodPresupuesto,
					CodOrganismo,
					CodFuente,
					CodCentroCosto,
					cod_partida,
					CodCuenta,
					CodCuentaPub20,
					SUM(CantidadPedida * PrecioUnit) AS Monto
				 FROM lg_ordenserviciodetalle
				 WHERE Anio = '".$Anio."' AND CodOrganismo = '".$CodOrganismo."' AND NroOrden = '".$NroOrden."'
				 GROUP BY Ejercicio, CodPresupuesto, CodFuente, cod_partida
				)
				UNION
				(
				 SELECT
					'".$Anio."' AS Anio,
					'".$NroOrden."' As NroOrden,
					'".$field_presupuesto['Ejercicio']."' As Ejercicio,
					'".$field_presupuesto['CodPresupuesto']."' As CodPresupuesto,
					'".$CodOrganismo."' As CodOrganismo,
					'".$IgvFuente."' As CodFuente,
					'".$CodCentroCosto."' As CodCentroCosto,
					'".$_cod_partida_igv."' As cod_partida,
					'".$_CodCuenta_igv."' As CodCuenta,
					'".$_CodCuentaPub20_igv."' As CodCuentaPub20,
					'".setNumero($MontoIva)."' As Monto
				)";
		$field_ocd = getRecords($sql);
		foreach ($field_ocd as $f) {
			##	inserto
			$sql = "INSERT INTO lg_distribucionos
					SET
						Anio = '".$f['Anio']."',
						CodOrganismo = '".$f['CodOrganismo']."',
						NroOrden = '".$f['NroOrden']."',
						Secuencia = '".++$_Secuencia."',
						Mes = '".$MesOrden."',
						cod_partida = '".$f['cod_partida']."',
						CodCuenta = '".$f['CodCuenta']."',
						CodCuentaPub20 = '".$f['CodCuentaPub20']."',
						Monto = '".$f['Monto']."',
						CodCentroCosto = '".$f['CodCentroCosto']."',
						Ejercicio = '".$f['Ejercicio']."',
						CodPresupuesto = '".$f['CodPresupuesto']."',
						CodFuente = '".$f['CodFuente']."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
			
			##	inserto
			$sql = "INSERT INTO lg_distribucioncompromisos
					SET
						Anio = '".$f['Anio']."',
						CodOrganismo = '".$f['CodOrganismo']."',
						CodProveedor = '".$CodProveedor."',
						CodTipoDocumento = 'OS',
						NroDocumento = '".$f['NroOrden']."',
						Secuencia = '".$_Secuencia."',
						Linea = '1',
						Mes = '".$MesOrden."',
						CodCentroCosto = '".$f['CodCentroCosto']."',
						cod_partida = '".$f['cod_partida']."',
						Monto = '".$f['Monto']."',
						Periodo = '".$PeriodoOrden."',
						Ejercicio = '".$f['Ejercicio']."',
						CodPresupuesto = '".$f['CodPresupuesto']."',
						CodFuente = '".$f['CodFuente']."',
						Origen = 'OS',
						Estado = 'PE',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		//	-----------------
		mysql_query("COMMIT");
	}
	//	modificar
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		//	-----------------
		list($cod_partida, $CodCuenta, $CodCuentaPub20) = getPartidaCuentaFromIGV($_PARAMETRO["IGVCODIGO"]);
		list($DiaOrden, $MesOrden, $AnioOrden) = split("[./-]", $FechaDocumento);
		$PeriodoOrden = "$AnioOrden-$MesOrden";
		//	valido que no cambio el a;o de la orden
		if ($AnioOrden != $Anio) die("No se puede modificar el año de la orden.");
		//	actualizo orden
		##	actualizo
		$sql = "UPDATE lg_ordenservicio
				SET
					Mes = '".$MesOrden."',
					CodDependencia = '".$CodDependencia."',
					CodFormaPago = '".$CodFormaPago."',
					FechaDocumento = '".formatFechaAMD($FechaDocumento)."',
					DiasPago = '".$DiasPago."',
					CodTipoPago = '".$CodTipoPago."',
					PlazoEntrega = '".$PlazoEntrega."',
					FechaEntrega = '".formatFechaAMD($FechaEntrega)."',
					MontoOriginal = '".setNumero($MontoOriginal)."',
					MontoNoAfecto = '".setNumero($MontoNoAfecto)."',
					MontoIva = '".setNumero($MontoIva)."',
					TotalMontoIva = '".setNumero($TotalMontoIva)."',
					MontoPendiente = '".setNumero($MontoPendiente)."',
					Descripcion = '".changeUrl($Descripcion)."',
					DescAdicional = '".changeUrl($DescAdicional)."',
					Observaciones = '".changeUrl($Observaciones)."',
					FechaValidoDesde = '".formatFechaAMD($FechaValidoDesde)."',
					FechaValidoHasta = '".formatFechaAMD($FechaValidoHasta)."',
					CodCentroCosto = '".$CodCentroCosto."',
					CodCuenta = '".$CodCuenta."',
					CodCuentaPub20 = '".$CodCuentaPub20."',
					cod_partida = '".$cod_partida."',
					CodPresupuesto = '".$CodPresupuesto."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo= '".$CodOrganismo."' AND
					NroOrden = '".$NroOrden."'";
		execute($sql);
		//	detalles
		##	elimino detalles
		$sql = "DELETE FROM lg_ordenserviciodetalle
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo= '".$CodOrganismo."' AND
					NroOrden = '".$NroOrden."'";
		execute($sql);
		##	inserto detalles
		$_Secuencia = 0;
		$detalle = split(";char:tr;", $detalles);
		foreach ($detalle as $linea) {
			list($_CommoditySub, $_Descripcion, $_CantidadPedida, $_CodUnidadRec, $_CantidadRec, $_PrecioUnit, $_FlagExonerado, $_Total, $_CategoriaProg, $_Ejercicio, $_CodPresupuesto, $_CodFuente, $_FechaEsperadaTermino, $_FechaTermino, $_CodCentroCosto, $_NroActivo, $_FlagTerminado, $_cod_partida, $_CodCuenta, $_CodCuentaPub20, $_Comentarios, $_CodRequerimiento, $_RequerimientoSecuencia, $_CotizacionSecuencia, $_CantidadRequerimiento) = split(";char:td;", $linea);
			//if ($_NroActivo == "") die("El commodity <strong>$_CommoditySub - $_Descripcion</strong> requiere el Nro. de Activo");
			##	inserto
			$sql = "INSERT INTO lg_ordenserviciodetalle
					SET
						Anio = '".$Anio."',
						CodOrganismo = '".$CodOrganismo."',
						NroOrden = '".$NroOrden."',
						Secuencia = '".++$_Secuencia."',
						Mes = '".$MesOrden."',
						CommoditySub = '".$_CommoditySub."',
						Descripcion = '".$_Descripcion."',
						CantidadPedida = '".$_CantidadPedida."',
						CodUnidadRec = '".$_CodUnidadRec."',
						CantidadRec = '".$_CantidadRec."',
						PrecioUnit = '".$_PrecioUnit."',
						Total = '".$_Total."',
						FechaEsperadaTermino = '".$_FechaEsperadaTermino."',
						FechaTermino = '".$_FechaTermino."',
						CodCentroCosto = '".$_CodCentroCosto."',
						NroActivo = '".$_NroActivo."',
						FlagExonerado = '".$_FlagExonerado."',
						FlagTerminado = '".$_FlagTerminado."',
						Comentarios = '".$_Comentarios."',
						cod_partida = '".$_cod_partida."',
						CodCuenta = '".$_CodCuenta."',
						CodCuentaPub20 = '".$_CodCuentaPub20."',
						CodPresupuesto = '".$_CodPresupuesto."',
						Ejercicio = '".$_Ejercicio."',
						CodFuente = '".$_CodFuente."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		//	distribucion
		##	elimino detalles
		$sql = "DELETE FROM lg_distribucionos
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo= '".$CodOrganismo."' AND
					NroOrden = '".$NroOrden."'";
		execute($sql);
		$sql = "DELETE FROM lg_distribucioncompromisos
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					CodProveedor = '".$CodProveedor."' AND
					CodTipoDocumento = 'OS' AND
					NroDocumento = '".$NroOrden."'";
		execute($sql);


		list($_cod_partida_igv, $_CodCuenta_igv, $_CodCuentaPub20_igv) = getPartidaCuentaFromIGV($_PARAMETRO["IGVCODIGO"]);
		##	
		$sql = "SELECT *
				FROM pv_presupuesto
				WHERE CodOrganismo = '$CodOrganismo' AND Ejercicio = '$Ejercicio' AND CategoriaProg = '$_PARAMETRO[IGVCATPROG]'";
		$field_presupuesto = getRecord($sql);
		##	
		$sql = "SELECT CodFuente
				FROM pv_presupuestodet
				WHERE CodOrganismo = '$CodOrganismo' AND CodPresupuesto = '$field_presupuesto[CodPresupuesto]' AND cod_partida = '$_cod_partida_igv';";
		$IgvFuente = getVar3($sql);
		##	
		$_Secuencia = 0;
		$sql = "(
				 SELECT
					Anio,
					NroOrden,
					Ejercicio,
					CodPresupuesto,
					CodOrganismo,
					CodFuente,
					CodCentroCosto,
					cod_partida,
					CodCuenta,
					CodCuentaPub20,
					SUM(CantidadPedida * PrecioUnit) AS Monto
				 FROM lg_ordenserviciodetalle
				 WHERE Anio = '".$Anio."' AND CodOrganismo = '".$CodOrganismo."' AND NroOrden = '".$NroOrden."'
				 GROUP BY Ejercicio, CodPresupuesto, CodFuente, cod_partida
				)
				UNION
				(
				 SELECT
					'".$Anio."' AS Anio,
					'".$NroOrden."' As NroOrden,
					'".$field_presupuesto['Ejercicio']."' As Ejercicio,
					'".$field_presupuesto['CodPresupuesto']."' As CodPresupuesto,
					'".$CodOrganismo."' As CodOrganismo,
					'".$IgvFuente."' As CodFuente,
					'".$CodCentroCosto."' As CodCentroCosto,
					'".$_cod_partida_igv."' As cod_partida,
					'".$_CodCuenta_igv."' As CodCuenta,
					'".$_CodCuentaPub20_igv."' As CodCuentaPub20,
					'".setNumero($MontoIva)."' As Monto
				)";
		$field_ocd = getRecords($sql);
		foreach ($field_ocd as $f) {
			##	inserto
			$sql = "INSERT INTO lg_distribucionos
					SET
						Anio = '".$f['Anio']."',
						CodOrganismo = '".$f['CodOrganismo']."',
						NroOrden = '".$f['NroOrden']."',
						Secuencia = '".++$_Secuencia."',
						Mes = '".$MesOrden."',
						cod_partida = '".$f['cod_partida']."',
						CodCuenta = '".$f['CodCuenta']."',
						CodCuentaPub20 = '".$f['CodCuentaPub20']."',
						Monto = '".$f['Monto']."',
						CodCentroCosto = '".$f['CodCentroCosto']."',
						Ejercicio = '".$f['Ejercicio']."',
						CodPresupuesto = '".$f['CodPresupuesto']."',
						CodFuente = '".$f['CodFuente']."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
			
			##	inserto
			$sql = "INSERT INTO lg_distribucioncompromisos
					SET
						Anio = '".$f['Anio']."',
						CodOrganismo = '".$f['CodOrganismo']."',
						CodProveedor = '".$CodProveedor."',
						CodTipoDocumento = 'OS',
						NroDocumento = '".$f['NroOrden']."',
						Secuencia = '".$_Secuencia."',
						Linea = '1',
						Mes = '".$MesOrden."',
						CodCentroCosto = '".$f['CodCentroCosto']."',
						cod_partida = '".$f['cod_partida']."',
						Monto = '".$f['Monto']."',
						Periodo = '".$PeriodoOrden."',
						Ejercicio = '".$f['Ejercicio']."',
						CodPresupuesto = '".$f['CodPresupuesto']."',
						CodFuente = '".$f['CodFuente']."',
						Origen = 'OS',
						Estado = 'PE',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		//	-----------------
		mysql_query("COMMIT");
	}
	//	revisar
	elseif ($accion == "revisar") {
		mysql_query("BEGIN");
		##	genero el nuevo codigo
		if ($NroInterno == "") $NroInterno = getCodigo_3("lg_ordenservicio", "NroInterno", "Anio", "CodOrganismo", $Anio, $CodOrganismo, 10);
		//	modifico orden
		$sql = "UPDATE lg_ordenservicio
				SET
					NroInterno = '".$NroInterno."',
					Estado = 'RV',
					RevisadaPor = '".$_SESSION["CODPERSONA_ACTUAL"]."',
					FechaRevision = NOW(),
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo= '".$CodOrganismo."' AND
					NroOrden = '".$NroOrden."'";
		execute($sql);
		//	actualizo compromisos
		$sql = "UPDATE lg_distribucioncompromisos
				SET
					Periodo = '".formatFechaAMD($FechaRevision)."',
					FechaEjecucion = '".formatFechaAMD($FechaRevision)."',
					Estado = 'CO',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					CodProveedor = '".$CodProveedor."' AND
					CodTipoDocumento = 'OS' AND
					NroDocumento = '".$NroOrden."'";
		execute($sql);
		echo "|Se ha generado la Orden de Servicio <strong>Nro. $NroInterno</strong>";
		mysql_query("COMMIT");
	}
	//	aprobar
	elseif ($accion == "aprobar") {
		mysql_query("BEGIN");
		//	modifico orden
		$sql = "UPDATE lg_ordenservicio
				SET
					Estado = 'AP',
					AprobadaPor = '".$_SESSION["CODPERSONA_ACTUAL"]."',
					FechaAprobacion = NOW(),
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo= '".$CodOrganismo."' AND
					NroOrden = '".$NroOrden."'";
		execute($sql);
		mysql_query("COMMIT");
	}
	//	anular
	elseif ($accion == "anular") {
		mysql_query("BEGIN");
		//	-----------------
		if ($Estado == "PR") {
			$EstadoOrden = "AN";
			$EstadoDetalle = "S";
			$EstadoCompromiso = "AN";
		}
		elseif ($Estado != "PR") {
			$EstadoOrden = "PR";
			$EstadoDetalle = "N";
			$EstadoCompromiso = "PE";
		}
		//	modifico orden
		$sql = "UPDATE lg_ordenservicio
				SET
					FechaAnulacion = NOW(),
					Estado = '".$EstadoOrden."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					NroOrden = '".$NroOrden."'";
		execute($sql);
		//	modifico detalles
		$sql = "UPDATE lg_ordenserviciodetalle
				SET
					FlagTerminado = '".$EstadoDetalle."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					NroOrden = '".$NroOrden."'";
		execute($sql);
		//	modifico compromisos
		$sql = "UPDATE lg_distribucioncompromisos
				SET
					Estado = '".$EstadoCompromiso."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					CodProveedor = '".$CodProveedor."' AND
					CodTipoDocumento = 'OS' AND
					NroDocumento = '".$NroOrden."'";
		execute($sql);
		if ($EstadoOrden == "AN") {
			$sql = "SELECT *
					FROM lg_cotizacionordenes
					WHERE
						Anio = '".$Anio."' AND
						CodOrganismo = '".$CodOrganismo."' AND
						NroOrden = '".$NroOrden."' AND
						TipoOrden = 'OS'
					ORDER BY SecuenciaOrden";
			$query_oc = mysql_query($sql) or die ($sql.mysql_error());
			while ($field_oc = mysql_fetch_array($query_oc)) {
				//
				$sql = "UPDATE lg_requerimientos SET Estado = 'AP' WHERE CodRequerimiento = '".$field_oc['CodRequerimiento']."'";
				execute($sql);
				//
				$sql = "UPDATE lg_requerimientosdet
						SET
							Estado = 'PE',
							CantidadOrdenCompra = CantidadOrdenCompra - ".floatval($field_oc['CantidadOrden']).",
							NroOrden = '',
							OrdenSecuencia = ''
						WHERE
							CodRequerimiento = '".$field_oc['CodRequerimiento']."' AND
							Secuencia = '".$field_oc['SecuenciaRequerimiento']."'";
				execute($sql);
				//
				$sql = "UPDATE lg_cotizacion SET Estado = 'PE' WHERE CotizacionSecuencia = '".$field_oc['CotizacionSecuencia']."'";
				execute($sql);
			}
			//	modifico compromisos
			$sql = "UPDATE lg_distribucioncompromisos
					SET
						FechaAnulacion = NOW(),
						PeriodoAnulacion = NOW(),
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()
					WHERE
						Anio = '".$Anio."' AND
						CodOrganismo = '".$CodOrganismo."' AND
						CodProveedor = '".$CodProveedor."' AND
						CodTipoDocumento = 'OS' AND
						NroDocumento = '".$NroOrden."'";
			execute($sql);
		}
		//	-----------------
		mysql_query("COMMIT");
	}
	//	cerrar
	elseif ($accion == "cerrar") {
		mysql_query("BEGIN");
		//	modifico orden
		$sql = "UPDATE lg_ordenservicio
				SET
					Estado = 'CE',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE NroOrden = '".$NroOrden."'";
		execute($sql);
		//	modifico detalles
		$sql = "UPDATE lg_ordenserviciodetalle
				SET
					FlagTerminado = 'S',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE NroOrden = '".$NroOrden."'";
		execute($sql);
		mysql_query("COMMIT");
	}
	//	confirmar
	elseif ($accion == "confirmar") {
		mysql_query("BEGIN");
		//	-----------------
		$Grupo = '';
		for ($i=0; $i < count($NroOrden); $i++) { 
			$FechaConfirmacion[$i] = formatFechaAMD($FechaConfirmacion[$i]);
			$CantidadPorRecibir[$i] = setNumero($CantidadPorRecibir[$i]);
			##	estado
			if (setNumero($CantidadPorRecibir[$i]) == floatval($CantidadPendiente[$i])) {
				$FlagTerminado = "S";
				$FechaTermino = "NOW()";
				$Estado = "CO";
			}
			else {
				$FlagTerminado = "N";
				$FechaTermino = "";
				$Estado = "AP";
			}
			##	confirmación
			$Orden = $Anio[$i].$CodOrganismo[$i].$NroOrden[$i];
			if ($Grupo != $Orden) {
				$Grupo = $Orden;
				if ($i > 0) die("No puede confirmar servicios de distintas ordenes");
				$NroConfirmacion = codigo("lg_confirmacionservicio", "NroConfirmacion", 4);
				$NroInterno = codigo("lg_confirmacionservicio", "NroInterno", 4, ['Anio'], [$Anio[$i]]);
				$DocumentoReferencia = $NroOrden[$i].'-'.$NroConfirmacion;
			}
			$sql = "INSERT INTO lg_confirmacionservicio
					SET
						Anio = '$Anio[$i]',
						CodOrganismo = '$CodOrganismo[$i]',
						NroOrden = '$NroOrden[$i]',
						Secuencia = '$Secuencia[$i]',
						NroConfirmacion = '$NroConfirmacion',
						DocumentoReferencia = '$DocumentoReferencia',
						NroInterno = '$NroInterno',
						FechaConfirmacion = '$FechaConfirmacion[$i]',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()";
			execute($sql);
			##	detalle orden de servicio
			$sql = "UPDATE lg_ordenserviciodetalle
					SET
						ConfirmadoPor = '$_SESSION[CODPERSONA_ACTUAL]',
						FechaConfirmacion = '$FechaConfirmacion[$i]',
						FlagTerminado = '$FlagTerminado',
						FechaTermino = '$FechaTermino',
						CantidadRecibida = (CantidadRecibida + $CantidadPorRecibir[$i]),
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()
					WHERE
						Anio = '$Anio[$i]'
						AND CodOrganismo = '$CodOrganismo[$i]'
						AND NroOrden = '$NroOrden[$i]'
						AND Secuencia = '$Secuencia[$i]'";
			execute($sql);
			##	orden de servicio
			$sql = "SELECT *
					FROM lg_ordenservicio
					WHERE
						Anio = '$Anio[$i]'
						AND CodOrganismo = '$CodOrganismo[$i]'
						AND NroOrden = '$NroOrden[$i]'";
			$field_os = getRecord($sql);
			##	orden de servicio
			$sql = "SELECT *
					FROM lg_ordenserviciodetalle
					WHERE
						Anio = '$Anio[$i]'
						AND CodOrganismo = '$CodOrganismo[$i]'
						AND NroOrden = '$NroOrden[$i]'
						AND FlagTerminado <> 'S'";
			$field_valido = getRecords($sql);
			if (!count($field_valido)) {
				$sql = "UPDATE lg_ordenservicio
						SET Estado = 'CO'
						WHERE
							Anio = '$Anio[$i]'
							AND CodOrganismo = '$CodOrganismo[$i]'
							AND NroOrden = '$NroOrden[$i]'";
				execute($sql);
			}
			##	calculo
			$sql = "SELECT
						osd.*,
						os.CodProveedor,
						os.MontoOriginal,
						os.TotalMontoIva,
						os.MontoIva
					FROM
						lg_ordenserviciodetalle osd
						INNER JOIN lg_ordenservicio os ON (os.Anio = osd.Anio
														   AND os.CodOrganismo = osd.CodOrganismo
														   AND os.NroOrden = osd.NroOrden)
					WHERE
						osd.Anio = '$Anio[$i]'
						AND osd.CodOrganismo = '$CodOrganismo[$i]'
						AND osd.NroOrden = '$NroOrden[$i]'
						AND osd.Secuencia = '$Secuencia[$i]'";
			$field_osd = getRecord($sql);
			if ($field_osd['FlagExonerado'] == 'S') {
				$MontoAfecto = 0.00;
				$MontoNoAfecto = $CantidadPorRecibir[$i] * $field_osd['PrecioUnit'];
			} else {
				$MontoNoAfecto = 0.00;
				$MontoAfecto = $CantidadPorRecibir[$i] * $field_osd['PrecioUnit'];
			}
			$MontoImpuesto = $MontoAfecto * $field_osd['MontoIva'] / $field_osd['MontoOriginal'];
			$Total = $MontoAfecto + $MontoNoAfecto + $MontoImpuesto;
			$PrecioCantidad = $MontoAfecto + $MontoNoAfecto;
			##	documento
			$sql = "INSERT INTO ap_documentos
					SET
						Anio = '$Anio[$i]',
						CodOrganismo = '$CodOrganismo[$i]',
						CodProveedor = '$field_osd[CodProveedor]',
						DocumentoClasificacion = '$_PARAMETRO[DOCREFOS]',
						DocumentoReferencia = '$DocumentoReferencia',
						Fecha = '$FechaConfirmacion[$i]',
						ReferenciaTipoDocumento = 'OS',
						ReferenciaNroDocumento = '$NroOrden[$i]',
						MontoAfecto = ".floatval($MontoAfecto).",
						MontoNoAfecto = ".floatval($MontoNoAfecto).",
						MontoImpuestos = ".floatval($MontoImpuesto).",
						MontoTotal = ".floatval($Total).",
						MontoPendiente = ".floatval($field_osd['TotalMontoIva'])." - ".floatval($Total).",
						Estado = 'PR',
						TransaccionTipoDocumento = 'OS',
						TransaccionNroDocumento = '$NroConfirmacion',
						Comentarios = '$field_os[Descripcion]',
						CodCentroCosto = '$field_osd[CodCentroCosto]',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()
					ON DUPLICATE KEY UPDATE
						Comentarios = '$field_os[Descripcion]',
						MontoAfecto = MontoAfecto + ".floatval($MontoAfecto).",
						MontoNoAfecto = MontoNoAfecto + ".floatval($MontoNoAfecto).",
						MontoImpuestos = MontoImpuestos + ".floatval($MontoImpuesto).",
						MontoTotal = MontoTotal + ".floatval($Total).",
						MontoPendiente = MontoPendiente - ".floatval($Total).",
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()";
			execute($sql);
			##	documento detalle
			$sql = "INSERT INTO ap_documentosdetalle
					SET
						Anio = '$Anio[$i]',
						CodProveedor = '$field_osd[CodProveedor]',
						DocumentoClasificacion = '$_PARAMETRO[DOCREFOS]',
						DocumentoReferencia = '$DocumentoReferencia',
						Secuencia = '$Secuencia[$i]',
						ReferenciaSecuencia = '$Secuencia[$i]',
						CommoditySub = '$field_osd[CommoditySub]',
						Descripcion = '$field_osd[Descripcion]',
						Cantidad = $CantidadPorRecibir[$i],
						PrecioUnit = $field_osd[PrecioUnit],
						PrecioCantidad = $PrecioCantidad,
						Total = $Total,
						CodCentroCosto = '$field_osd[CodCentroCosto]',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()";
			execute($sql);
		}
		$_SESSION['ImprimirActa'] = true;
		$_SESSION['NroConfirmacion'] = $NroConfirmacion;
		//	-----------------
		mysql_query("COMMIT");
	}
	//	confirmar
	elseif ($accion == "desconfirmar") {
		mysql_query("BEGIN");
		//	-----------------
		foreach ($confirmadas as $Documento) {
			list($Anio, $CodOrganismo, $NroOrden, $Secuencia, $CodProveedor, $DocumentoClasificacion, $DocumentoReferencia) = explode('_', $Documento);
			//	documento detalle
			$sql = "SELECT *
					FROM ap_documentosdetalle
					WHERE
						Anio = '$Anio'
						AND CodProveedor = '$CodProveedor'
						AND DocumentoClasificacion = '$DocumentoClasificacion'
						AND DocumentoReferencia = '$DocumentoReferencia'
						AND ReferenciaSecuencia = '$Secuencia'";
			$field_dd = getRecord($sql);
			##	actualizo servicio detalle
			$sql = "UPDATE lg_ordenserviciodetalle
					SET
						ConfirmadoPor = '',
						FechaConfirmacion = '',
						FlagTerminado = 'N',
						FechaTermino = '',
						CantidadRecibida = (CantidadRecibida - $field_dd[Cantidad])
					WHERE
						Anio = '$Anio'
						AND CodOrganismo = '$CodOrganismo'
						AND NroOrden = '$NroOrden'
						AND Secuencia = '$Secuencia'";
			execute($sql);
			##	actualizo servicio
			$sql = "UPDATE lg_ordenservicio
					SET
						Estado = 'AP',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()
					WHERE
						Anio = '$Anio'
						AND CodOrganismo = '$CodOrganismo'
						AND NroOrden = '$NroOrden'";
			execute($sql);
			##	elimino confirmación
			$sql = "DELETE FROM lg_confirmacionservicio
					WHERE
						DocumentoReferencia = '$DocumentoReferencia'
						AND Secuencia = '$Secuencia'";
			execute($sql);
			##	elimino documento detalle
			$sql = "DELETE FROM ap_documentosdetalle
					WHERE
						Anio = '$Anio'
						AND CodProveedor = '$CodProveedor'
						AND DocumentoClasificacion = '$DocumentoClasificacion'
						AND DocumentoReferencia = '$DocumentoReferencia'
						AND ReferenciaSecuencia = '$Secuencia'";
			execute($sql);
		}
		foreach ($confirmadas as $Documento) {
			list($Anio, $CodOrganismo, $NroOrden, $Secuencia, $CodProveedor, $DocumentoClasificacion, $DocumentoReferencia) = explode('_', $Documento);
			##	documento
			$sql = "SELECT *
					FROM ap_documentos
					WHERE
						Anio = '$Anio'
						AND CodProveedor = '$CodProveedor'
						AND DocumentoClasificacion = '$DocumentoClasificacion'
						AND DocumentoReferencia = '$DocumentoReferencia'";
			$field_d = getRecord($sql);
			if ($field_d['Estado'] == "RV") die("No se puede desconfirmar un documento <strong>Facturado</strong>");
			##	documento
			$sql = "SELECT *
					FROM ap_documentosdetalle
					WHERE
						Anio = '$Anio'
						AND CodProveedor = '$CodProveedor'
						AND DocumentoClasificacion = '$DocumentoClasificacion'
						AND DocumentoReferencia = '$DocumentoReferencia'";
			$field_valido = getRecords($sql);
			if (!count($field_valido)) {
				##	elimino documento
				$sql = "DELETE FROM ap_documentos
						WHERE
							Anio = '$Anio'
							AND CodProveedor = '$CodProveedor'
							AND DocumentoClasificacion = '$DocumentoClasificacion'
							AND DocumentoReferencia = '$DocumentoReferencia'";
				execute($sql);
			}
		}
		//	-----------------
		mysql_query("COMMIT");
	}
	//	confirmar
	elseif ($accion == "_confirmar") {
		mysql_query("BEGIN");
		//	valores
		$FechaTermino = formatFechaAMD($FechaTermino);
		$PorRecibirTotal = setNumero($PorRecibirTotal);
		$CantidadTotal = setNumero($CantidadTotal);
		$SaldoTotal = setNumero($SaldoTotal);
		//	confirmo estado
		if (floatval($CantidadTotal) > floatval($SaldoTotal)) {
			$FlagTerminado = "N";
		}
		else {
			$FlagTerminado = "S";
		}
		//	inserto confirmacion
		$NroConfirmacion = getCodigo("lg_confirmacionservicio", "NroConfirmacion", 4);
		$NroInterno = codigo("lg_confirmacionservicio", "NroInterno", 4, array('Anio'), array($Anio));
		$DocumentoReferencia = "$NroOrden-$NroConfirmacion";
		$sql = "INSERT INTO lg_confirmacionservicio (
							Anio,
							CodOrganismo,
							NroOrden,
							Secuencia,
							NroConfirmacion,
							DocumentoReferencia,
							NroInterno,
							UltimoUsuario,
							UltimaFecha
				) VALUES (
							'".$Anio."',
							'".$CodOrganismo."',
							'".$NroOrden."',
							'".$Secuencia."',
							'".$NroConfirmacion."',
							'".$DocumentoReferencia."',
							'".$NroInterno."',
							'".$_SESSION["USUARIO_ACTUAL"]."',
							NOW()
				)";
		execute($sql);
		//	modifico servicio detalle
		$sql = "UPDATE lg_ordenserviciodetalle
				SET
					ConfirmadoPor = '".$_SESSION['CODPERSONA_ACTUAL']."',
					FechaConfirmacion = NOW(),
					FlagTerminado = '".$FlagTerminado."',
					FechaTermino = '".$FechaTermino."',
					CantidadRecibida = (CantidadRecibida + ".floatval($PorRecibirTotal)."),
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					NroOrden = '".$NroOrden."' AND
					Secuencia = '".$Secuencia."'";
		execute($sql);
		//	actualizo estado de la orden si confirme todos los servicios
		$sql = "SELECT *
				FROM lg_ordenserviciodetalle
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					NroOrden = '".$NroOrden."' AND
					FlagTerminado <> 'S'";
		$field_osd = getRecords($sql);
		if (count($field_osd) == 0) {
			$sql = "UPDATE lg_ordenservicio
					SET Estado = 'CO'
					WHERE
						Anio = '".$Anio."' AND
						CodOrganismo = '".$CodOrganismo."' AND
						NroOrden = '".$NroOrden."'";
			execute($sql);
		}
		//	recalculo
		if (afectaTipoServicio($CodTipoServicio)) $FactorImpuesto = getPorcentajeIVA($CodTipoServicio);
		else $FactorImpuesto = 0;
		$PrecioCantidad = $PrecioUnit * $PorRecibirTotal;
		if ($FlagExonerado == "S") {
			$MontoAfecto = 0;
			$MontoNoAfecto = $PrecioCantidad;
		} else {
			$MontoAfecto = $PrecioCantidad;
			$MontoNoAfecto = 0;
		}
		##	consulto los montos de la orden de compra
		$sql = "SELECT
					MontoOriginal AS MontoAfecto,
					MontoNoAfecto,
					MontoIva AS MontoIGV
				FROM lg_ordenservicio
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					NroOrden = '".$NroOrden."'";
		$field_afecto = getRecord($sql);
		##	actualizo montos del documento
		if ($MontoAfecto != $field_afecto['MontoAfecto']) {
			$MontoImpuestos = round(($MontoAfecto * $field_afecto['MontoIGV'] / $field_afecto['MontoAfecto']), 2);
		} else {
			$MontoAfecto = $field_afecto['MontoAfecto'];
			$MontoNoAfecto = $field_afecto['MontoNoAfecto'];
			$MontoImpuestos = $field_afecto['MontoIGV'];
		}
		$MontoTotal = $MontoAfecto + $MontoNoAfecto + $MontoImpuestos;
		//	inserto el documento
		$sql = "INSERT INTO ap_documentos (
							Anio,
							CodOrganismo,
							CodProveedor,
							DocumentoClasificacion,
							DocumentoReferencia,
							Fecha,
							ReferenciaTipoDocumento,
							ReferenciaNroDocumento,
							MontoAfecto,
							MontoNoAfecto,
							MontoImpuestos,
							MontoTotal,
							MontoPendiente,
							Estado,
							TransaccionTipoDocumento,
							TransaccionNroDocumento,
							Comentarios,
							CodCentroCosto,
							UltimoUsuario,
							UltimaFecha
				) VALUES (
							'".$Anio."',
							'".$CodOrganismo."',
							'".$CodProveedor."',
							'".$_PARAMETRO['DOCREFOS']."',
							'".$DocumentoReferencia."',
							NOW(),
							'OS',
							'".$NroOrden."',
							'".$MontoAfecto."',
							'".$MontoNoAfecto."',
							'".$MontoImpuestos."',
							'".$MontoTotal."',
							'".$MontoTotal."',
							'PR',
							'OS',
							'".$NroConfirmacion."',
							'".$Descripcion."',
							'".$CodCentroCosto."',
							'".$_SESSION["USUARIO_ACTUAL"]."',
							NOW()
				)";
		execute($sql);
		//	inserto el documento detalle
		$sql = "INSERT INTO ap_documentosdetalle (
							Anio,
							CodProveedor,
							DocumentoClasificacion,
							DocumentoReferencia,
							Secuencia,
							ReferenciaSecuencia,
							CommoditySub,
							Descripcion,
							Cantidad,
							PrecioUnit,
							PrecioCantidad,
							Total,
							CodCentroCosto,
							UltimoUsuario,
							UltimaFecha
				) VALUES (
							'".$Anio."',
							'".$CodProveedor."',
							'".$_PARAMETRO['DOCREFOS']."',
							'".$DocumentoReferencia."',
							'1',
							'".$Secuencia."',
							'".$CommoditySub."',
							'".$Descripcion."',
							'".$PorRecibirTotal."',
							'".$PrecioUnit."',
							'".$PrecioCantidad."',
							'".$MontoTotal."',
							'".$CodCentroCosto."',
							'".$_SESSION["USUARIO_ACTUAL"]."',
							NOW()
				)";
		execute($sql);
		echo "|$NroInterno|$Anio.$CodProveedor.".$_PARAMETRO['DOCREFOS'].".$DocumentoReferencia";
		mysql_query("COMMIT");
	}
	//	desconfirmar
	elseif ($accion == "_desconfirmar") {
		mysql_query("BEGIN");
		//	-----------------
		list($Anio, $CodProveedor, $DocumentoClasificacion, $DocumentoReferencia) = split("[.]", $registro);
		//	consulto documentos
		$sql = "SELECT *
				FROM ap_documentos
				WHERE
					Anio = '".$Anio."' AND
					CodProveedor = '".$CodProveedor."' AND
					DocumentoClasificacion = '".$DocumentoClasificacion."' AND
					DocumentoReferencia = '".$DocumentoReferencia."'";
		$field_doc = getRecord($sql);
		if ($field_doc['Estado'] == "RV") die("No se puede desconfirmar un documento <strong>Facturado</strong>");
		//	consultom documentos detalle
		$sql = "SELECT *
				FROM ap_documentosdetalle
				WHERE
					Anio = '".$Anio."' AND
					CodProveedor = '".$CodProveedor."' AND
					DocumentoClasificacion = '".$DocumentoClasificacion."' AND
					DocumentoReferencia = '".$DocumentoReferencia."'";
		$field_detalle = getRecord($sql);
		//	actualizo servicio detalle
		$sql = "UPDATE lg_ordenserviciodetalle
				SET
					FlagTerminado = 'N',
					CantidadRecibida = (CantidadRecibida - ".floatval($field_detalle['Cantidad'])."),
					FechaTermino = '',
					UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
					UltimaFecha = NOW()
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$field_doc['CodOrganismo']."' AND
					NroOrden = '".$field_doc['ReferenciaNroDocumento']."' AND
					Secuencia = '".$field_detalle['ReferenciaSecuencia']."'";
		execute($sql);
		//	actualizo servicio
		$sql = "UPDATE lg_ordenservicio
				SET
					Estado = 'AP',
					UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
					UltimaFecha = NOW()
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$field_doc['CodOrganismo']."' AND
					NroOrden = '".$field_doc['ReferenciaNroDocumento']."'";
		execute($sql);
		//	elimino documentos
		$sql = "DELETE FROM ap_documentos
				WHERE
					Anio = '".$Anio."' AND
					CodProveedor = '".$CodProveedor."' AND
					DocumentoClasificacion = '".$DocumentoClasificacion."' AND
					DocumentoReferencia = '".$DocumentoReferencia."'";
		execute($sql);
		//	elimino documentos
		$sql = "DELETE FROM ap_documentosdetalle
				WHERE
					Anio = '".$Anio."' AND
					CodProveedor = '".$CodProveedor."' AND
					DocumentoClasificacion = '".$DocumentoClasificacion."' AND
					DocumentoReferencia = '".$DocumentoReferencia."'";
		execute($sql);
		//	elimino documentos
		$sql = "DELETE FROM lg_confirmacionservicio WHERE NroConfirmacion = '".$field_doc['TransaccionNroDocumento']."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
	//	modificacion restringida
	elseif ($accion == "modificacion_restringida") {
		mysql_query("BEGIN");
		//	-----------------
		list($DiaOrden, $MesOrden, $AnioOrden) = split("[./-]", $FechaDocumento);
		$PeriodoOrden = "$AnioOrden-$MesOrden";
		//	valido que no cambio el a;o de la orden
		if ($AnioOrden != $Anio) die("No se puede modificar el año de la orden.");
		elseif ($MesOrden != $Mes) die("No se puede modificar el periodo de la orden.");
		//	actualizo orden
		##	actualizo
		$sql = "UPDATE lg_ordenservicio
				SET
					FechaDocumento = '".formatFechaAMD($FechaDocumento)."',
					CodFormaPago = '".$CodFormaPago."',
					CodTipoPago = '".$CodTipoPago."',
					PlazoEntrega = '".$PlazoEntrega."',
					FechaEntrega = '".formatFechaAMD($FechaEntrega)."',
					DiasPago = '".$DiasPago."',
					FechaValidoDesde = '".formatFechaAMD($FechaValidoDesde)."',
					FechaValidoHasta = '".formatFechaAMD($FechaValidoHasta)."',
					Descripcion = '".changeUrl($Descripcion)."',
					DescAdicional = '".changeUrl($DescAdicional)."',
					Observaciones = '".changeUrl($Observaciones)."',
					PreparadaPor = '".$PreparadaPor."',
					FechaPreparacion = '".formatFechaAMD($FechaPreparacion)."',
					RevisadaPor = '".$RevisadaPor."',
					FechaRevision = '".formatFechaAMD($FechaRevision)."',
					AprobadaPor = '".$AprobadaPor."',
					FechaAprobacion = '".formatFechaAMD($FechaAprobacion)."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo= '".$CodOrganismo."' AND
					NroOrden = '".$NroOrden."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
//	ajax
elseif ($modulo == "ajax") {
	if ($accion == "orden_servicio_detalles_insertar") {
		$NomCentroCosto = getVar3("SELECT Codigo FROM ac_mastcentrocosto WHERE CodCentroCosto = '$CodCentroCosto'");
		if (!afectaTipoServicio($CodTipoServicio)) { $dFlagExonerado = "disabled"; $cFlagExonerado = "checked"; }
		$FechaEsperadaTermino = formatFechaAMD(getFechaFin(formatFechaDMA(substr($Ahora, 0, 10)), $_PARAMETRO['DIAENTOC']));
		$sql = "SELECT
					cs.*,
					cm.Clasificacion,
					cm.Descripcion AS NomCommodity
				FROM
					lg_commoditysub cs
					INNER JOIN lg_commoditymast cm ON (cs.CommodityMast = cm.CommodityMast)
				WHERE cs.Codigo = '".$Codigo."'";
		$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		if (mysql_num_rows($query) != 0) {
			$field_detalles = mysql_fetch_array($query);
			$Descripcion = strtoupper($field_detalles['NomCommodity']."-".$field_detalles['Descripcion'])
			?>
	        <tr class="trListaBody" onclick="mClk(this, 'sel_detalles');" id="detalles_<?=$nrodetalles?>">
				<th align="center">
					<?=$nrodetalles?>
	            </th>
				<td align="center">
	            	<?=$field_detalles['Codigo']?>
	                <input type="hidden" name="CodItem" />
	                <input type="hidden" name="CommoditySub" class="cell2" style="text-align:center;" value="<?=$field_detalles['Codigo']?>" readonly />
	            </td>
				<td align="center">
					<textarea name="Descripcion" style="height:30px;" class="cell"><?=($Descripcion)?></textarea>
				</td>
				<td align="center">
	            	<input type="text" name="CantidadPedida" class="cell" style="text-align:right;" value="0,00" onBlur="numeroBlur(this);" onFocus="numeroFocus(this);" onchange="setMontosOrdenServicio(this.form);" />
	                <input type="hidden" name="CodUnidadRec" value="" />
	                <input type="hidden" name="CantidadRec" value="" />
	            </td>
				<td align="center">
	            	<input type="text" name="PrecioUnit" class="cell" style="text-align:right;" value="0,00" onBlur="numeroBlur(this);" onFocus="numeroFocus(this);" onchange="setMontosOrdenServicio(this.form);" />
	            </td>
				<td align="center">
	            	<input type="checkbox" name="FlagExonerado" class="FlagExonerado" onchange="setMontosOrdenServicio(this.form);" <?=$cFlagExonerado?> <?=$dFlagExonerado?> />
	            </td>
				<td align="center">
	            	<input type="text" name="Total" class="cell2" style="text-align:right;" value="0,00" readonly="readonly" />
	            </td>
	            <td align="center">
	                <input type="text" name="detallesCategoriaProg" id="detallesCategoriaProg_<?=$nrodetalles?>" class="cell2 CategoriaProg" style="text-align:center;" value="<?=$CategoriaProg?>" readonly />
	                <input type="hidden" name="detallesEjercicio" id="detallesEjercicio_<?=$nrodetalles?>" class="cell2 Ejercicio" style="text-align:center;" value="<?=$Ejercicio?>" readonly />
	                <input type="hidden" name="detallesCodPresupuesto" id="detallesCodPresupuesto_<?=$nrodetalles?>" class="cell2 CodPresupuesto" style="text-align:center;" value="<?=$CodPresupuesto?>" readonly />
	            </td>
	            <td>
	                <select name="detallesCodFuente" id="detallesCodFuente_<?=$nrodetalles?>" class="cell2 CodFuente" <?=$disabled_ver?>>
	                    <?=loadSelect2("pv_fuentefinanciamiento","CodFuente","Denominacion",$CodFuente,10)?>
	                </select>
	            </td>
				<td align="center">
	            	<input type="text" name="FechaEsperadaTermino" value="<?=formatFechaDMA($FechaEsperadaTermino)?>" maxlength="10" style="text-align:center;" class="datepicker cell" onkeyup="setFechaDMA(this);" />
	            </td>
				<td align="center">
	            	<input type="text" name="FechaTermino" value="<?=formatFechaDMA($FechaEsperadaTermino)?>" maxlength="10" style="text-align:center;" class="datepicker cell" onkeyup="setFechaDMA(this);" />
	            </td>
				<td align="right">
					0,00
				</td>
				<td align="center">
					<input type="text" name="CodCentroCosto" id="CodCentroCosto_<?=$nrodetalles?>" maxlength="4" class="cell" style="text-align:center;" value="<?=$CodCentroCosto?>" />
					<input type="hidden" name="NomCentroCosto" id="NomCentroCosto_<?=$nrodetalles?>" />
				</td>
				<td align="center">
					<input type="text" name="NroActivo" id="NroActivo_<?=$nrodetalles?>" value="<?=($field_detalles['NroActivo'])?>" style="text-align:center;" class="cell" />
				</td>
				<td align="center">
	            	<input type="checkbox" name="FlagTerminado" <?=chkFlag("N")?> disabled="disabled" />
	            </td>
				<td align="center">
					<?=$field_detalles['cod_partida']?>
					<input type="hidden" name="cod_partida" value="<?=$field_detalles['cod_partida']?>" />
				</td>
				<td align="center">
					<?=$field_detalles['CodCuenta']?>
					<input type="hidden" name="CodCuenta" value="<?=$field_detalles['CodCuenta']?>" />
				</td>
				<td align="center">
					<?=$field_detalles['CodCuentaPub20']?>
					<input type="hidden" name="CodCuentaPub20" value="<?=$field_detalles['CodCuentaPub20']?>" />
				</td>
				<td align="center">
					<textarea name="Comentarios" style="height:30px;" class="cell"></textarea>
					<input type="hidden" name="CodRequerimiento" />
					<input type="hidden" name="Secuencia" />
					<input type="hidden" name="CotizacionSecuencia" />
					<input type="hidden" name="CantidadRequerimiento" />
				</td>
			</tr>
	       <?php
		}
	}
}
?>