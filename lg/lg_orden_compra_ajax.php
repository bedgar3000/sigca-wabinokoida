<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion".".sql", "w+");
///////////////////////////////////////////////////////////////////////////////
//	ORDEN DE COMPRA (NUEVO, MODIFICAR, REVISAR, APROBAR, ANULAR, CERRAR, CERRAR LINEAS)
///////////////////////////////////////////////////////////////////////////////
if ($modulo == "orden_compra") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-----------------
		$CodDependencia = getValorCampo("ac_mastcentrocosto", "CodCentroCosto", "CodDependencia", $_PARAMETRO["CCOSTOCOMPRA"]);
		$FaxProveedor = getValorCampo("mastpersonas", "CodPersona", "Fax", $CodProveedor);
		list($cod_partida, $CodCuenta, $CodCuentaPub20) = getPartidaCuentaFromIGV($_PARAMETRO["IGVCODIGO"]);
		list($DiaOrden, $MesOrden, $AnioOrden) = split("[./-]", $FechaOrden);
		$PeriodoOrden = "$AnioOrden-$MesOrden";
		//	inserto orden
		##	genero el nuevo codigo
		$NroOrden = getCodigo_3("lg_ordencompra", "NroOrden", "Anio", "CodOrganismo", $Anio, $CodOrganismo, 10);
		##	inserto
		$sql = "INSERT INTO lg_ordencompra
				SET
					Anio = '".$Anio."',
					CodOrganismo = '".$CodOrganismo."',
					NroOrden = '".$NroOrden."',
					Mes = '".$MesOrden."',
					Clasificacion = '".$Clasificacion."',
					CodDependencia = '".$CodDependencia."',
					CodProveedor = '".$CodProveedor."',
					NomProveedor = '".changeUrl($NomProveedor)."',
					FaxProveedor = '".changeUrl($FaxProveedor)."',
					CodAlmacen = '".$CodAlmacen."',
					FechaPrometida = '".formatFechaAMD($FechaPrometida)."',
					FechaOrden = '".formatFechaAMD($FechaOrden)."',
					PreparadaPor = '".$PreparadaPor."',
					FechaPreparacion = '".formatFechaAMD($FechaPreparacion)."',
					CodTipoServicio = '".$CodTipoServicio."',
					MontoBruto = '".setNumero($MontoBruto)."',
					MontoIGV = '".setNumero($MontoIGV)."',
					MontoOtros = '".setNumero($MontoOtros)."',
					MontoTotal = '".setNumero($MontoTotal)."',
					MontoPendiente = '".setNumero($MontoPendiente)."',
					MontoAfecto = '".setNumero($MontoAfecto)."',
					MontoNoAfecto = '".setNumero($MontoNoAfecto)."',
					CodFormaPago = '".$CodFormaPago."',
					CodAlmacenIngreso = '".$CodAlmacenIngreso."',
					NomContacto = '".changeUrl($NomContacto)."',
					FaxContacto = '".changeUrl($FaxContacto)."',
					PlazoEntrega = '".$PlazoEntrega."',
					DirEntrega = '".changeUrl($DirEntrega)."',
					InsEntrega = '".changeUrl($InsEntrega)."',
					Entregaren = '".changeUrl($Entregaren)."',
					Observaciones = '".changeUrl($Observaciones)."',
					ObsDetallada = '".changeUrl($ObsDetallada)."',
					TipoClasificacion = '".$TipoClasificacion."',
					CodCuenta = '".$CodCuenta."',
					CodCuentaPub20 = '".$CodCuentaPub20."',
					cod_partida = '".$cod_partida."',
					CodPresupuesto = '".$CodPresupuesto."',
					Ejercicio = '".$Ejercicio."',
					CodFuente = '".$CodFuente."',
					FactorImpuesto = '".$FactorImpuesto."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		
		//	inserto detalles
		$_Secuencia = 0;
		$detalle = split(";char:tr;", $detalles);
		foreach ($detalle as $linea) {
			list($_CodItem, $_CommoditySub, $_Descripcion, $_CodUnidad, $_CantidadPedida, $_PrecioUnit, $_DescuentoPorcentaje, $_DescuentoFijo, $_FlagExonerado, $_PrecioUnitTotal, $_Total, $_CategoriaProg, $_Ejercicio, $_CodPresupuesto, $_CodFuente, $_CodUnidadRec, $_CantidadRec, $_FechaPrometida, $_CodCentroCosto, $_cod_partida, $_CodCuenta, $_CodCuentaPub20, $_Comentarios, $_CodRequerimiento, $_RequerimientoSecuencia, $_CotizacionSecuencia, $_CantidadRequerimiento) = split(";char:td;", $linea);
			if ($_CodUnidad == $_CodUnidadRec) $_CantidadRec = $_CantidadPedida;
			$_PrecioCantidad = $_CantidadPedida * $_PrecioUnit;
			##	inserto
			$sql = "INSERT INTO lg_ordencompradetalle
					SET
						Anio = '".$Anio."',
						CodOrganismo = '".$CodOrganismo."',
						NroOrden = '".$NroOrden."',
						Secuencia = '".++$_Secuencia."',
						Mes = '".$MesOrden."',
						CodItem = '".$_CodItem."',
						CommoditySub = '".$_CommoditySub."',
						Descripcion = '".$_Descripcion."',
						CodUnidad = '".$_CodUnidad."',
						CantidadPedida = '".$_CantidadPedida."',
						CodUnidadRec = '".$_CodUnidadRec."',
						CantidadRec = '".$_CantidadRec."',
						PrecioUnit = '".$_PrecioUnit."',
						PrecioCantidad = '".$_PrecioCantidad."',
						Total = '".$_Total."',
						DescuentoPorcentaje = '".$_DescuentoPorcentaje."',
						DescuentoFijo = '".$_DescuentoFijo."',
						FlagExonerado = '".$_FlagExonerado."',
						CodCentroCosto = '".$_CodCentroCosto."',
						Comentarios = '".$_Comentarios."',
						FechaPrometida = '".$_FechaPrometida."',
						CodCuenta = '".$_CodCuenta."',
						CodCuentaPub20 = '".$_CodCuentaPub20."',
						cod_partida = '".$_cod_partida."',
						CodPresupuesto = '".$_CodPresupuesto."',
						Ejercicio = '".$_Ejercicio."',
						CodFuente = '".$_CodFuente."',
						Estado = 'PR',
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
							TipoOrden = 'OC',
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
			$sql = "INSERT INTO lg_distribucionoc
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
						CodTipoDocumento = 'OC',
						NroDocumento = '".$NroOrden."',
						Secuencia = '".$_Secuencia."',
						Linea = '1',
						Mes = '".$MesOrden."',
						CodCentroCosto = '".$CodCentroCosto."',
						cod_partida = '".$_cod_partida."',
						Monto = '".$_Monto."',
						Periodo = '".$PeriodoOrden."',
						CodPresupuesto = '".$CodPresupuesto."',
						Origen = 'OC',
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
					SUM(PrecioCantidad) AS Monto
				 FROM lg_ordencompradetalle
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
					(SELECT CodCentrocosto FROM ac_mastcentrocosto WHERE Codigo = '$_PARAMETRO[CCOSTOCOMPRA]') As CodCentroCosto,
					'".$_cod_partida_igv."' As cod_partida,
					'".$_CodCuenta_igv."' As CodCuenta,
					'".$_CodCuentaPub20_igv."' As CodCuentaPub20,
					'".setNumero($MontoIGV)."' As Monto
				)";
		$field_ocd = getRecords($sql);
		foreach ($field_ocd as $f) {
			##	inserto
			$sql = "INSERT INTO lg_distribucionoc
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
						CodTipoDocumento = 'OC',
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
						Origen = 'OC',
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
		//	valido que no cambio el a;o de la orden
		if ($AnioOrden != $Anio) die("No se puede modificar el año de la orden.");
		//	valores
		list($cod_partida, $CodCuenta, $CodCuentaPub20) = getPartidaCuentaFromIGV($_PARAMETRO["IGVCODIGO"]);
		list($DiaOrden, $MesOrden, $AnioOrden) = split("[./-]", $FechaOrden);
		$PeriodoOrden = "$AnioOrden-$MesOrden";
		//	actualizo orden
		##	actualizo
		$sql = "UPDATE lg_ordencompra
				SET
					Mes = '".$MesOrden."',
					Clasificacion = '".$Clasificacion."',
					CodAlmacen = '".$CodAlmacen."',
					FechaPrometida = '".formatFechaAMD($FechaPrometida)."',
					FechaOrden = '".formatFechaAMD($FechaOrden)."',
					MontoBruto = '".setNumero($MontoBruto)."',
					MontoIGV = '".setNumero($MontoIGV)."',
					MontoOtros = '".setNumero($MontoOtros)."',
					MontoTotal = '".setNumero($MontoTotal)."',
					MontoPendiente = '".setNumero($MontoPendiente)."',
					MontoAfecto = '".setNumero($MontoAfecto)."',
					MontoNoAfecto = '".setNumero($MontoNoAfecto)."',
					CodFormaPago = '".$CodFormaPago."',
					CodAlmacenIngreso = '".$CodAlmacenIngreso."',
					NomContacto = '".changeUrl($NomContacto)."',
					FaxContacto = '".changeUrl($FaxContacto)."',
					PlazoEntrega = '".$PlazoEntrega."',
					DirEntrega = '".changeUrl($DirEntrega)."',
					InsEntrega = '".changeUrl($InsEntrega)."',
					Entregaren = '".changeUrl($Entregaren)."',
					Observaciones = '".changeUrl($Observaciones)."',
					ObsDetallada = '".changeUrl($ObsDetallada)."',
					TipoClasificacion = '".$TipoClasificacion."',
					CodCuenta = '".$CodCuenta."',
					CodCuentaPub20 = '".$CodCuentaPub20."',
					cod_partida = '".$cod_partida."',
					CodPresupuesto = '".$CodPresupuesto."',
					FactorImpuesto = '".$FactorImpuesto."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo= '".$CodOrganismo."' AND
					NroOrden = '".$NroOrden."'";
		execute($sql);
		
		//	detalles
		##	elimino detalles
		$sql = "DELETE FROM lg_ordencompradetalle
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo= '".$CodOrganismo."' AND
					NroOrden = '".$NroOrden."'";
		$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		##	inserto detalles
		$_Secuencia = 0;
		$detalle = split(";char:tr;", $detalles);
		foreach ($detalle as $linea) {
			list($_CodItem, $_CommoditySub, $_Descripcion, $_CodUnidad, $_CantidadPedida, $_PrecioUnit, $_DescuentoPorcentaje, $_DescuentoFijo, $_FlagExonerado, $_PrecioUnitTotal, $_Total, $_CategoriaProg, $_Ejercicio, $_CodPresupuesto, $_CodFuente, $_CodUnidadRec, $_CantidadRec, $_FechaPrometida, $_CodCentroCosto, $_cod_partida, $_CodCuenta, $_CodCuentaPub20, $_Comentarios, $_CodRequerimiento, $_RequerimientoSecuencia, $_CotizacionSecuencia, $_CantidadRequerimiento) = split(";char:td;", $linea);
			if ($_CodUnidad == $_CodUnidadRec) $_CantidadRec = $_CantidadPedida;
			$_PrecioCantidad = $_CantidadPedida * $_PrecioUnit;
			##	inserto
			$sql = "INSERT INTO lg_ordencompradetalle
					SET
						Anio = '".$Anio."',
						CodOrganismo = '".$CodOrganismo."',
						NroOrden = '".$NroOrden."',
						Secuencia = '".++$_Secuencia."',
						Mes = '".$MesOrden."',
						CodItem = '".$_CodItem."',
						CommoditySub = '".$_CommoditySub."',
						Descripcion = '".$_Descripcion."',
						CodUnidad = '".$_CodUnidad."',
						CantidadPedida = '".$_CantidadPedida."',
						CodUnidadRec = '".$_CodUnidadRec."',
						CantidadRec = '".$_CantidadRec."',
						PrecioUnit = '".$_PrecioUnit."',
						PrecioCantidad = '".$_PrecioCantidad."',
						Total = '".$_Total."',
						DescuentoPorcentaje = '".$_DescuentoPorcentaje."',
						DescuentoFijo = '".$_DescuentoFijo."',
						FlagExonerado = '".$_FlagExonerado."',
						CodCentroCosto = '".$_CodCentroCosto."',
						Comentarios = '".$_Comentarios."',
						FechaPrometida = '".$_FechaPrometida."',
						CodCuenta = '".$_CodCuenta."',
						CodCuentaPub20 = '".$_CodCuentaPub20."',
						cod_partida = '".$_cod_partida."',
						CodPresupuesto = '".$_CodPresupuesto."',
						Ejercicio = '".$_Ejercicio."',
						CodFuente = '".$_CodFuente."',
						Estado = 'PR',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		
		//	distribucion
		##	elimino detalles
		$sql = "DELETE FROM lg_distribucionoc
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo= '".$CodOrganismo."' AND
					NroOrden = '".$NroOrden."'";
		$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		$sql = "DELETE FROM lg_distribucioncompromisos
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					CodProveedor = '".$CodProveedor."' AND
					CodTipoDocumento = 'OC' AND
					NroDocumento = '".$NroOrden."'";
		$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		//	inserto distribucion
		/*$_Secuencia = 0;
		$detalle = split(";char:tr;", $detalles_partida);
		foreach ($detalle as $linea) {
			list($_cod_partida, $_CodCuenta, $_CodCuentaPub20, $_Monto) = split(";char:td;", $linea);
			##	inserto
			$sql = "INSERT INTO lg_distribucionoc
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
						CodCentroCosto = '".$_PARAMETRO["CCOSTOCOMPRA"]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
			
			##	inserto
			$sql = "INSERT INTO lg_distribucioncompromisos
					SET
						Anio = '".$Anio."',
						CodOrganismo = '".$CodOrganismo."',
						CodProveedor = '".$CodProveedor."',
						CodTipoDocumento = 'OC',
						NroDocumento = '".$NroOrden."',
						Secuencia = '".$_Secuencia."',
						Linea = '1',
						Mes = '".$MesOrden."',
						CodCentroCosto = '".$_PARAMETRO["CCOSTOCOMPRA"]."',
						cod_partida = '".$_cod_partida."',
						Monto = '".$_Monto."',
						Periodo = '".$PeriodoOrden."',
						CodPresupuesto = '".$CodPresupuesto."',
						Origen = 'OC',
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
					SUM(PrecioCantidad) AS Monto
				 FROM lg_ordencompradetalle
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
					(SELECT CodCentrocosto FROM ac_mastcentrocosto WHERE Codigo = '$_PARAMETRO[CCOSTOCOMPRA]') As CodCentroCosto,
					'".$_cod_partida_igv."' As cod_partida,
					'".$_CodCuenta_igv."' As CodCuenta,
					'".$_CodCuentaPub20_igv."' As CodCuentaPub20,
					'".setNumero($MontoIGV)."' As Monto
				)";
		$field_ocd = getRecords($sql);
		foreach ($field_ocd as $f) {
			##	inserto
			$sql = "INSERT INTO lg_distribucionoc
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
						CodTipoDocumento = 'OC',
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
						Origen = 'OC',
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
		//	-----------------
		##	genero el nuevo codigo
		if ($NroInterno == "") $NroInterno = getCodigo_3("lg_ordencompra", "NroInterno", "Anio", "CodOrganismo", $Anio, $CodOrganismo, 10);
		//	modifico orden
		$sql = "UPDATE lg_ordencompra
				SET
					NroInterno = '".$NroInterno."',
					Estado = 'RV',
					RevisadaPor = '".$RevisadaPor."',
					FechaRevision = '".formatFechaAMD($FechaRevision)."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					NroOrden = '".$NroOrden."'";
		execute($sql);
		//	actualizo compromisos
		$sql = "UPDATE lg_distribucioncompromisos
				SET
					FechaEjecucion = '".formatFechaAMD($FechaRevision)."',
					Estado = 'CO',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					CodProveedor = '".$CodProveedor."' AND
					CodTipoDocumento = 'OC' AND
					NroDocumento = '".$NroOrden."'";
		execute($sql);
		echo "|Se ha generado la Orden de Compra <strong>Nro. $NroInterno</strong>";
		//	-----------------
		mysql_query("COMMIT");
	}
	//	aprobar
	elseif ($accion == "aprobar") {
		mysql_query("BEGIN");
		//	-----------------
		//	modifico orden
		$sql = "UPDATE lg_ordencompra
				SET
					Estado = 'AP',
					AprobadaPor = '".$AprobadaPor."',
					FechaAprobacion = '".formatFechaAMD($FechaAprobacion)."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					NroOrden = '".$NroOrden."'";
		execute($sql);
		//	modifico detalle
		$sql = "UPDATE lg_ordencompradetalle
				SET
					Estado = 'PE',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					NroOrden = '".$NroOrden."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
	//	anular
	elseif ($accion == "anular") {
		mysql_query("BEGIN");
		//	-----------------
		if ($Estado == "PR") {
			$EstadoOrden = "AN";
			$EstadoDetalle = "AN";
			$EstadoCompromiso = "AN";
		}
		elseif ($Estado != "PR") {
			$EstadoOrden = "PR";
			$EstadoDetalle = "PR";
			$EstadoCompromiso = "PE";
		}
		//	modifico orden
		$sql = "UPDATE lg_ordencompra
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
		$sql = "UPDATE lg_ordencompradetalle
				SET
					Estado = '".$EstadoDetalle."',
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
					CodTipoDocumento = 'OC' AND
					NroDocumento = '".$NroOrden."'";
		execute($sql);
		
		if ($EstadoOrden == "AN") {
			$sql = "SELECT *
					FROM lg_cotizacionordenes
					WHERE
						Anio = '".$Anio."' AND
						CodOrganismo = '".$CodOrganismo."' AND
						NroOrden = '".$NroOrden."' AND
						TipoOrden = 'OC'
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
						CodTipoDocumento = 'OC' AND
						NroDocumento = '".$NroOrden."'";
			execute($sql);
		}
		//	-----------------
		mysql_query("COMMIT");
	}
	//	cerrar
	elseif ($accion == "cerrar") {
		mysql_query("BEGIN");
		//	-----------------
		//	modifico orden
		$sql = "UPDATE lg_ordencompra
				SET
					Estado = 'CE',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					NroOrden = '".$NroOrden."'";
		execute($sql);
		
		//	modifico detalles
		$sql = "UPDATE lg_ordencompradetalle
				SET
					Estado = 'CE',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					NroOrden = '".$NroOrden."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
	//	cerrar linea
	elseif ($accion == "cerrar-detalle") {
		mysql_query("BEGIN");
		//	-----------------
		list($Anio, $CodOrganismo, $NroOrden, $Secuencia) = split("[.]", $registro);
		//	verifico los detalles
		$sql = "SELECT Estado
				FROM lg_ordencompradetalle
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					NroOrden = '".$NroOrden."' AND
					Secuencia = '".$Secuencia."' AND
					Estado = 'PE'";
		$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		if (mysql_num_rows($query) == 0) die("Solo se pueden cerrar lineas en Estado <strong>Pendiente</strong>");
		##
		//	modifico detalles
		$sql = "UPDATE lg_ordencompradetalle
				SET
					Estado = 'CE',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					NroOrden = '".$NroOrden."' AND
					Secuencia = '".$Secuencia."'";
		execute($sql);
		##
		//	consulto si no quedan pendientes en el requerimiento
		$sql = "SELECT Estado
				FROM lg_ordencompradetalle
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					NroOrden = '".$NroOrden."' AND
					Estado = 'PE'";
		$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		if (mysql_num_rows($query) == 0) {
			//	consulto si se completaron algunas lineas en el requerimiento
			$sql = "SELECT Estado
					FROM lg_ordencompradetalle
					WHERE
						Anio = '".$Anio."' AND
						CodOrganismo = '".$CodOrganismo."' AND
						NroOrden = '".$NroOrden."' AND
						Estado = 'CO'";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			if (mysql_num_rows($query) != 0) {
				$sql = "UPDATE lg_ordencompra
						SET Estado = 'CO'
						WHERE
							Anio = '".$Anio."' AND
							CodOrganismo = '".$CodOrganismo."' AND
							NroOrden = '".$NroOrden."'";
				execute($sql);
			} else {
				$sql = "UPDATE lg_ordencompra
						SET Estado = 'CE'
						WHERE
							Anio = '".$Anio."' AND
							CodOrganismo = '".$CodOrganismo."' AND
							NroOrden = '".$NroOrden."'";
				execute($sql);
			}
		}
		//	-----------------
		mysql_query("COMMIT");
	}
	//	modificacion restringida
	elseif ($accion == "modificacion_restringida") {
		mysql_query("BEGIN");
		//	-----------------
		list($DiaOrden, $MesOrden, $AnioOrden) = split("[./-]", $FechaOrden);
		$PeriodoOrden = "$AnioOrden-$MesOrden";
		//	valido que no cambio el a;o de la orden
		if ($AnioOrden != $Anio) die("No se puede modificar el año de la orden.");
		elseif ($MesOrden != $Mes) die("No se puede modificar el periodo de la orden.");
		//	actualizo orden
		##	actualizo
		$sql = "UPDATE lg_ordencompra
				SET
					FechaOrden = '".formatFechaAMD($FechaOrden)."',
					CodFormaPago = '".$CodFormaPago."',
					Clasificacion = '".$Clasificacion."',
					CodAlmacen = '".$CodAlmacen."',
					CodAlmacenIngreso = '".$CodAlmacenIngreso."',
					PlazoEntrega = '".$PlazoEntrega."',
					FechaPrometida = '".formatFechaAMD($FechaPrometida)."',
					NomContacto = '".changeUrl($NomContacto)."',
					FaxContacto = '".changeUrl($FaxContacto)."',
					Entregaren = '".changeUrl($Entregaren)."',
					DirEntrega = '".changeUrl($DirEntrega)."',
					InsEntrega = '".changeUrl($InsEntrega)."',
					Observaciones = '".changeUrl($Observaciones)."',
					ObsDetallada = '".changeUrl($ObsDetallada)."',
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
?>