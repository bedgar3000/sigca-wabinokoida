<?php
include("../../lib/fphp.php");
include("fphp.php");
	$__archivo = fopen("_"."$modulo-$accion.sql", "w+");
///////////////////////////////////////////////////////////////////////////////
//	PARA AJAX
///////////////////////////////////////////////////////////////////////////////
//	commodity
if ($modulo == "commodity") {
	$Descripcion = changeUrl($Descripcion);
	$detalles = changeUrl($detalles);
	
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	inserto
		$CommodityMast = getCodigo("lg_commoditymast", "CommodityMast", 3);
		$sql = "INSERT INTO lg_commoditymast (
							Clasificacion,
							CommodityMast,
							Descripcion,
							FlagObra,
							Estado,
							UltimoUsuario,
							UltimaFecha
				) VALUES (
							'".$Clasificacion."',
							'".$CommodityMast."',
							'".$Descripcion."',
							'".$FlagObra."',
							'".$Estado."',
							'".$_SESSION["USUARIO_ACTUAL"]."',
							NOW()
				)";
		execute($sql);
		
		//	detalles
		if ($detalles != "") {
			$linea = split(";", $detalles);	$_Linea=0;
			foreach ($linea as $registro) {	$_Linea++;
				list($_Codigo, $_CommoditySub, $_Descripcion, $_cod_partida, $_CodCuenta, $_CodCuentaPub20, $_CodClasificacion, $_CodUnidad, $_CodigoSNC, $_Estado) = split("[|]", $registro);
				$_Codigo = $CommodityMast.$_CommoditySub;
				
				//	inserto
				$sql = "INSERT INTO lg_commoditysub (
									CommodityMast,
									CommoditySub,
									Codigo,
									Descripcion,
									CodUnidad,
									cod_partida,
									CodCuenta,
									CodCuentaPub20,
									CodClasificacion,
									CodigoSNC,
									Estado,
									UltimoUsuario,
									UltimaFecha
						) VALUES (
									'".$CommodityMast."',
									'".$_CommoditySub."',
									'".$_Codigo."',
									'".$_Descripcion."',
									'".$_CodUnidad."',
									'".$_cod_partida."',
									'".$_CodCuenta."',
									'".$_CodCuentaPub20."',
									'".$_CodClasificacion."',
									'".$_CodigoSNC."',
									'".$_Estado."',
									'".$_SESSION["USUARIO_ACTUAL"]."',
									NOW()
						)";
				execute($sql);
			}
		}
		mysql_query("COMMIT");
	}
	
	//	modificar
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		//	actualizo
		$sql = "UPDATE lg_commoditymast
				SET
					Clasificacion = '".$Clasificacion."',
					Descripcion = '".$Descripcion."',
					FlagObra = '".$FlagObra."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CommodityMast = '".$CommodityMast."'";
		execute($sql);
		
		//	detalles
		if ($eliminados_detalle != "") {
			$linea = split(";", $eliminados_detalle);	$_Linea=0;
			foreach ($linea as $_Codigo) {
				$sql = "DELETE FROM lg_commoditysub WHERE Codigo = '".$_Codigo."'";
				$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			}
		}
		if ($detalles != "") {
			$linea = split(";", $detalles);	$_Linea=0;
			foreach ($linea as $registro) {	$_Linea++;
				list($_Codigo, $_CommoditySub, $_Descripcion, $_cod_partida, $_CodCuenta, $_CodCuentaPub20, $_CodClasificacion, $_CodUnidad, $_CodigoSNC, $_Estado) = split("[|]", $registro);
				if ($_Codigo == "") $_Codigo = $CommodityMast.$_CommoditySub;
				$sql = "INSERT INTO lg_commoditysub
						SET
							CommodityMast = '".$CommodityMast."',
							CommoditySub = '".$_CommoditySub."',
							Codigo = '".$_Codigo."',
							Descripcion = '".$_Descripcion."',
							CodUnidad = '".$_CodUnidad."',
							cod_partida = '".$_cod_partida."',
							CodCuenta = '".$_CodCuenta."',
							CodCuentaPub20 = '".$_CodCuentaPub20."',
							CodClasificacion = '".$_CodClasificacion."',
							CodigoSNC = '".$_CodigoSNC."',
							Estado = '".$_Estado."',
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()
						ON DUPLICATE KEY UPDATE
							Descripcion = '".$_Descripcion."',
							CodUnidad = '".$_CodUnidad."',
							cod_partida = '".$_cod_partida."',
							CodCuenta = '".$_CodCuenta."',
							CodCuentaPub20 = '".$_CodCuentaPub20."',
							CodClasificacion = '".$_CodClasificacion."',
							CodigoSNC = '".$_CodigoSNC."',
							Estado = '".$_Estado."',
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()";
				execute($sql);
			}
		}
		mysql_query("COMMIT");
	}
	
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	eliminar
		$sql = "DELETE FROM lg_commoditysub WHERE CommodityMast = '".$registro."'";
		$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		$sql = "DELETE FROM lg_commoditymast WHERE CommodityMast = '".$registro."'";
		$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		mysql_query("COMMIT");
	}
}

//	almacen
elseif ($modulo == "almacen") {
	$Comentarios = changeUrl($Comentarios);
	$FechaDocumento = formatFechaAMD($FechaDocumento);
	$AnioDocumento = substr($FechaDocumento, 0, 4);
	//	despacho
	if ($accion == "despacho") {
		mysql_query("BEGIN");
		//	periodo
		$Periodo = substr($FechaDocumento, 0, 7);
		if (!periodoAbierto($CodOrganismo, $Periodo)) die("No se puede generar ninguna transacci&oacute;n porque no se ha abierto el periodo $Periodo.");
		//	inserto transaccion
		##	genero el nuevo codigo
		$NroDocumento = getCodigo_3("lg_transaccion", "NroDocumento", "CodOrganismo", "CodDocumento", $CodOrganismo, $CodDocumento, 6);
		$NroInterno = getCodigo("lg_transaccion", "NroInterno", 6, "Anio", $AnioDocumento, "CodOrganismo", $CodOrganismo, "CodDocumento", $CodDocumento);
		##	inserto
		$sql = "INSERT INTO lg_transaccion
				SET
					CodOrganismo = '".$CodOrganismo."',
					CodDocumento = '".$CodDocumento."',
					NroDocumento = '".$NroDocumento."',
					NroInterno = '".$NroInterno."',
					CodTransaccion = '".$CodTransaccion."',
					FechaDocumento = '".$FechaDocumento."',
					Periodo = '".$Periodo."',
					CodAlmacen = '".$CodAlmacen."',
					CodCentroCosto = '".$CodCentroCosto."',
					CodDocumentoReferencia = '".$CodDocumentoReferencia."',
					NroDocumentoReferencia = '".$NroDocumentoReferencia."',
					IngresadoPor = '".$IngresadoPor."',
					RecibidoPor = '".$RecibidoPor."',
					EjecutadoPor = '".$IngresadoPor."',
					FechaEjecucion = NOW(),
					Comentarios = '".$Comentarios."',
					FlagManual = '".$FlagManual."',
					FlagPendiente = '".$FlagPendiente."',
					ReferenciaAnio = '".$Anio."',
					ReferenciaNroDocumento = '".$ReferenciaNroDocumento."',
					DocumentoReferencia = '".$DocumentoReferencia."',
					DocumentoReferenciaInterno = '".$DocumentoReferenciaInterno."',
					CodDependencia = '".$CodDependencia."',
					Anio = '".$AnioDocumento."',
					Estado = 'CO',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		//	inserto detalles
		$_Secuencia = 0;
		$detalle = split(";char:tr;", $detalles);
		foreach ($detalle as $linea) {
			$_Secuencia++;
			list($_CodItem, $_Descripcion, $_CodUnidad, $_StockActual, $_CantidadPedida, $_CantidadPendiente, $_CantidadRecibida, $_PrecioUnit, $_Total, $_CodCentroCosto, $_ReferenciaCodDocumento, $_ReferenciaNroDocumento, $_ReferenciaNroInterno, $_ReferenciaSecuencia) = split(";char:td;", $linea);
			##	inserto detalle
			$sql = "INSERT INTO lg_transacciondetalle
					SET
						CodOrganismo = '".$CodOrganismo."',
						CodDocumento = '".$CodDocumento."',
						NroDocumento = '".$NroDocumento."',
						Secuencia = '".$_Secuencia."',
						CodItem = '".$_CodItem."',
						Descripcion = '".$_Descripcion."',
						CodUnidad = '".$_CodUnidad."',
						CantidadPedida = '".$_CantidadPedida."',
						CantidadRecibida = '".$_CantidadRecibida."',
						PrecioUnit = '".$_PrecioUnit."',
						Total = '".$_Total."',
						ReferenciaAnio = '".$Anio."',
						ReferenciaCodDocumento = '".$_ReferenciaCodDocumento."',
						ReferenciaNroDocumento = '".$_ReferenciaNroDocumento."',
						ReferenciaNroInterno = '".$_ReferenciaNroInterno."',
						ReferenciaSecuencia = '".$_ReferenciaSecuencia."',
						CodCentroCosto = '".$_CodCentroCosto."',
						Estado = 'CO',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
			//	actualizo requerimientos
			##	si se scompleto el despacho
			if ($_CantidadPendiente == $_CantidadRecibida) {
				##	completo detalle del requerimiento
				$sql = "UPDATE lg_requerimientosdet
						SET Estado = 'CO'
						WHERE
							CodRequerimiento = '".$CodRequerimiento."' AND
							Secuencia = '".$_ReferenciaSecuencia."'";
				execute($sql);
				##	si se completaron los detalles
				$sql = "SELECT *
						FROM lg_requerimientosdet
						WHERE
							CodRequerimiento = '".$CodRequerimiento."' AND
							Estado = 'PE'";
				$query_pendientes = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
				if (mysql_num_rows($query_pendientes) == 0) {
					##	completo requerimiento
					$sql = "UPDATE lg_requerimientos
							SET Estado = 'CO'
							WHERE CodRequerimiento = '".$CodRequerimiento."'";
					execute($sql);
				}
			}
			##	actualizo cantidad pendiente
			$sql = "UPDATE lg_requerimientosdet
					SET CantidadRecibida = (CantidadRecibida + ".floatval($_CantidadRecibida).")
					WHERE
						CodRequerimiento = '".$CodRequerimiento."' AND
						Secuencia = '".$_ReferenciaSecuencia."'";
			execute($sql);
		}
		echo "|$NroDocumento|$NroInterno";
		mysql_query("COMMIT");
	}
	//	recepcion
	elseif ($accion == "recepcion") {
		mysql_query("BEGIN");
		//-------------------
		//	errores
		##	periodo
		$Periodo = substr($FechaDocumento, 0, 7);
		if (!periodoAbierto($CodOrganismo, $Periodo)) die("No se puede generar ninguna transacci&oacute;n porque no se ha abierto el periodo $Periodo.");
		##	documento
		$sql = "SELECT *
				FROM ap_documentos
				WHERE
					Anio = '".$Anio."' AND
					CodProveedor = '".$CodProveedor."' AND
					DocumentoClasificacion = '".$CodTransaccion."' AND
					DocumentoReferencia = '".$DocumentoReferencia."'";
		$query_documento = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		if (mysql_num_rows($query_documento) != 0) die("<strong>Doc. Ref / G. Remisión</strong> ya se encuentra registrado");
		
		//	inserto transaccion
		##	genero el nuevo codigo
		$NroDocumento = getCodigo_3("lg_transaccion", "NroDocumento", "CodOrganismo", "CodDocumento", $CodOrganismo, $CodDocumento, 6);
		$NroInterno = getCodigo("lg_transaccion", "NroInterno", 6, "Anio", $AnioDocumento, "CodOrganismo", $CodOrganismo, "CodDocumento", $CodDocumento);
		##	inserto
		$sql = "INSERT INTO lg_transaccion
				SET
					CodOrganismo = '".$CodOrganismo."',
					CodDocumento = '".$CodDocumento."',
					NroDocumento = '".$NroDocumento."',
					NroInterno = '".$NroInterno."',
					CodTransaccion = '".$CodTransaccion."',
					FechaDocumento = '".$FechaDocumento."',
					Periodo = '".$Periodo."',
					CodAlmacen = '".$CodAlmacen."',
					CodCentroCosto = '".$CodCentroCosto."',
					CodDocumentoReferencia = '".$CodDocumentoReferencia."',
					NroDocumentoReferencia = '".$NroDocumentoReferencia."',
					IngresadoPor = '".$IngresadoPor."',
					RecibidoPor = '".$RecibidoPor."',
					EjecutadoPor = '".$IngresadoPor."',
					FechaEjecucion = NOW(),
					Comentarios = '".$Comentarios."',
					FlagManual = '".$FlagManual."',
					FlagPendiente = '".$FlagPendiente."',
					ReferenciaAnio = '".$Anio."',
					ReferenciaNroDocumento = '".$ReferenciaNroDocumento."',
					DocumentoReferencia = '".$DocumentoReferencia."',
					DocumentoReferenciaInterno = '".$DocumentoReferenciaInterno."',
					CodDependencia = '".$CodDependencia."',
					Anio = '".$AnioDocumento."',
					Estado = 'CO',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		
		//	inserto documento
		$sql = "INSERT INTO ap_documentos
				SET 
					Anio = '".$Anio."',
					CodOrganismo = '".$CodOrganismo."',
					CodProveedor = '".$CodProveedor."',
					DocumentoClasificacion = '".$CodTransaccion."',
					DocumentoReferencia = '".$DocumentoReferencia."',
					Fecha = NOW(),
					ReferenciaTipoDocumento = 'OC',
					ReferenciaNroDocumento = '".$ReferenciaNroDocumento."',
					Estado = '".$Estado."',
					TransaccionTipoDocumento = '".$CodDocumento."',
					TransaccionNroDocumento = '".$NroDocumento."',
					Comentarios = '".$Comentarios."',
					CodCentroCosto = '".$CodCentroCosto."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		
		//	inserto detalles
		$_Secuencia = 0;
		$detalle = split(";char:tr;", $detalles);
		foreach ($detalle as $linea) {
			$_Secuencia++;
			list($_CodItem, $_Descripcion, $_CodUnidad, $_StockActual, $_CantidadPedida, $_CantidadPendiente, $_CantidadRecibida, $_FlagExonerado, $_PrecioUnit, $_Total, $_CodUnidadCompra, $_CantidadPedidaCompra, $_CantidadCompra, $_PrecioUnitCompra, $_CodCentroCosto, $_ReferenciaCodDocumento, $_ReferenciaNroDocumento, $_ReferenciaNroInterno, $_ReferenciaSecuencia) = split(";char:td;", $linea);
			$_Cantidad = $_CantidadCompra / $_CantidadPedida * $_CantidadRecibida;
			##	inserto detalle
			$sql = "INSERT INTO lg_transacciondetalle
					SET
						CodOrganismo = '".$CodOrganismo."',
						CodDocumento = '".$CodDocumento."',
						NroDocumento = '".$NroDocumento."',
						Secuencia = '".$_Secuencia."',
						CodItem = '".$_CodItem."',
						Descripcion = '".$_Descripcion."',
						CodUnidad = '".$_CodUnidad."',
						CantidadPedida = '".$_CantidadPedida."',
						CantidadRecibida = '".$_CantidadRecibida."',
						PrecioUnit = '".$_PrecioUnit."',
						Total = '".$_Total."',
						ReferenciaAnio = '".$Anio."',
						ReferenciaCodDocumento = '".$_ReferenciaCodDocumento."',
						ReferenciaNroDocumento = '".$_ReferenciaNroDocumento."',
						ReferenciaNroInterno = '".$_ReferenciaNroInterno."',
						ReferenciaSecuencia = '".$_ReferenciaSecuencia."',
						CodCentroCosto = '".$_CodCentroCosto."',
						CodUnidadCompra = '".$_CodUnidadCompra."',
						CantidadCompra = '".$_CantidadCompra."',
						PrecioUnitCompra = '".$_PrecioUnitCompra."',
						Estado = 'CO',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
			
			##	
			$_PrecioCantidad = $_CantidadCompra * $_PrecioUnitCompra;
			if ($_FlagExonerado == "S") {
				$MontoNoAfecto += $_PrecioCantidad;
			} else {
				$MontoAfecto += $_PrecioCantidad;
			}
			
			##	inserto documento detalle
			$sql = "INSERT INTO ap_documentosdetalle
					SET
						Anio = '".$Anio."',
						CodProveedor = '".$CodProveedor."',
						DocumentoClasificacion = '".$CodTransaccion."',
						DocumentoReferencia = '".$DocumentoReferencia."',
						Secuencia = '".$_Secuencia."',
						ReferenciaSecuencia = '".$_ReferenciaSecuencia."',
						CodItem = '".$_CodItem."',
						Descripcion = '".$_Descripcion."',
						Cantidad = '".$_CantidadCompra."',
						PrecioUnit = '".$_PrecioUnitCompra."',
						PrecioCantidad = '".$_PrecioCantidad."',
						Total = '".$_Total."',
						CodCentroCosto = '".$_CodCentroCosto."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
			
			//	actualizo orden
			##	si se scompleto el despacho
			if ($_CantidadPendiente == $_CantidadRecibida) {
				##	completo detalle de la orden
				$sql = "UPDATE lg_ordencompradetalle
						SET Estado = 'CO'
						WHERE
							Anio = '".$Anio."' AND
							CodOrganismo = '".$CodOrganismo."' AND
							NroOrden = '".$NroOrden."' AND
							Secuencia = '".$_ReferenciaSecuencia."'";
				execute($sql);
				
				##	si se completaron los detalles
				$sql = "SELECT *
						FROM lg_ordencompradetalle
						WHERE
							Anio = '".$Anio."' AND
							CodOrganismo = '".$CodOrganismo."' AND
							NroOrden = '".$NroOrden."' AND
							Estado = 'PE'";
				$query_pendientes = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
				if (mysql_num_rows($query_pendientes) == 0) {
					##	completo requerimiento
					$sql = "UPDATE lg_ordencompra
							SET Estado = 'CO'
							WHERE
								Anio = '".$Anio."' AND
								CodOrganismo = '".$CodOrganismo."' AND
								NroOrden = '".$NroOrden."'";
					execute($sql);
				}
			}
			##	actualizo cantidad pendiente
			$sql = "UPDATE lg_ordencompradetalle
					SET CantidadRecibida = (CantidadRecibida + ".floatval($_CantidadRecibida).")
					WHERE
						Anio = '".$Anio."' AND
						CodOrganismo = '".$CodOrganismo."' AND
						NroOrden = '".$NroOrden."' AND
						Secuencia = '".$_ReferenciaSecuencia."'";
			execute($sql);
		}	
		##	consulto los montos de la orden de compra
		$sql = "SELECT
					MontoAfecto,
					MontoNoAfecto,
					MontoIGV
				FROM lg_ordencompra
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					NroOrden = '".$ReferenciaNroDocumento."'";
		$query_afecto = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		if (mysql_num_rows($query_afecto) != 0) $field_afecto = mysql_fetch_array($query_afecto);
		
		##	actualizo montos del documento
		if ($MontoAfecto != $field_afecto['MontoAfecto']) {
			$MontoImpuestos = round(($MontoAfecto * $field_afecto['MontoIGV'] / $field_afecto['MontoAfecto']), 2);
		} else {
			$MontoAfecto = $field_afecto['MontoAfecto'];
			$MontoNoAfecto = $field_afecto['MontoNoAfecto'];
			$MontoImpuestos = $field_afecto['MontoIGV'];
		}
		$MontoTotal = $MontoAfecto + $MontoNoAfecto + $MontoImpuestos;
		
		##	actualizo montos del documento
		$sql = "UPDATE ap_documentos
				SET
					MontoAfecto = '".$MontoAfecto."',
					MontoNoAfecto = '".$MontoNoAfecto."',
					MontoImpuestos = '".$MontoImpuestos."',
					MontoTotal = '".$MontoTotal."',
					MontoPendiente = '".$MontoTotal."'
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					CodProveedor = '".$CodProveedor."' AND
					DocumentoClasificacion = '".$CodTransaccion."' AND
					DocumentoReferencia = '".$DocumentoReferencia."'";
		execute($sql);
		echo "|$NroDocumento|$NroInterno";
		//-------------------
		mysql_query("COMMIT");
	}
	//	pasar requerimiento para compras
	elseif ($accion == "dirigir-compras") {
		mysql_query("BEGIN");
		$sql = "UPDATE lg_requerimientosdet
				SET FlagCompraAlmacen = 'C'
				WHERE
					CodRequerimiento = '".$registro."' AND
					Estado = 'PE' AND
					FlagCompraAlmacen = 'A'";
		execute($sql);
		mysql_query("COMMIT");
	}
	//	pasar linea para compras
	elseif ($accion == "dirigir-compras-detalle") {
		mysql_query("BEGIN");
		$detalle = split(";char:tr;", $registro);
		foreach ($detalle as $linea) {
			list($CodRequerimiento, $Secuencia) = split("[.]", $linea);
			$sql = "UPDATE lg_requerimientosdet
					SET FlagCompraAlmacen = 'X'
					WHERE
						CodRequerimiento = '".$CodRequerimiento."' AND
						Secuencia = '".$Secuencia."'";
			execute($sql);
		}
		mysql_query("COMMIT");
	}
	//	cerrar linea
	elseif ($accion == "cerrar-detalle") {
		mysql_query("BEGIN");
		$detalle = split(";char:tr;", $registro);
		foreach ($detalle as $linea) {
			list($CodRequerimiento, $Secuencia) = split("[.]", $linea);			
			//	modifico detalles
			$sql = "UPDATE lg_requerimientosdet
					SET
						Estado = 'CE',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()
					WHERE
						CodRequerimiento = '".$CodRequerimiento."' AND
						Secuencia = '".$Secuencia."'";
			execute($sql);
			##
			//	consulto si no quedan pendientes en el requerimiento
			$sql = "SELECT Estado
					FROM lg_requerimientosdet
					WHERE
						CodRequerimiento = '".$CodRequerimiento."' AND
						Estado = 'PE'";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			if (mysql_num_rows($query) == 0) {
				//	consulto si se completaron algunas lineas en el requerimiento
				$sql = "SELECT Estado
						FROM lg_requerimientosdet
						WHERE
							CodRequerimiento = '".$CodRequerimiento."' AND
							Estado = 'CO'";
				$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
				if (mysql_num_rows($query) != 0) {
					$sql = "UPDATE lg_requerimientos
							SET Estado = 'CO'
							WHERE CodRequerimiento = '".$CodRequerimiento."'";
					execute($sql);
				} else {
					$sql = "UPDATE lg_requerimientos
							SET Estado = 'CE'
							WHERE CodRequerimiento = '".$CodRequerimiento."'";
					execute($sql);
				}
			}
		}
		mysql_query("COMMIT");
	}
	//	cerrar linea
	elseif ($accion == "cerrar-detalle-compras") {
		mysql_query("BEGIN");
		$detalle = split(";char:tr;", $registro);
		foreach ($detalle as $linea) {
			list($Anio, $CodOrganismo, $NroOrden, $Secuencia) = split("[.]", $linea);
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
		}
		mysql_query("COMMIT");
	}
	//	cerrar linea
	elseif ($accion == "cerrar-detalle-requerimiento") {
		mysql_query("BEGIN");
		$detalle = split(";char:tr;", $registro);
		foreach ($detalle as $linea) {
			list($CodRequerimiento, $Secuencia) = split("[.]", $registro);
			//	verifico los detalles
			$sql = "SELECT Estado
					FROM lg_requerimientosdet
					WHERE
						CodRequerimiento = '".$CodRequerimiento."' AND
						Secuencia = '".$Secuencia."' AND
						Estado = 'PE'";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			if (mysql_num_rows($query) == 0) die("Solo se pueden cerrar lineas en Estado <strong>Pendiente</strong>");
			##
			//	modifico detalles
			$sql = "UPDATE lg_requerimientosdet
					SET
						Estado = 'CE',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()
					WHERE
						CodRequerimiento = '".$CodRequerimiento."' AND
						Secuencia = '".$Secuencia."'";
			execute($sql);
			##
			//	consulto si no quedan pendientes en el requerimiento
			$sql = "SELECT Estado
					FROM lg_requerimientosdet
					WHERE
						CodRequerimiento = '".$CodRequerimiento."' AND
						Estado = 'PE'";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			if (mysql_num_rows($query) == 0) {
				//	consulto si se completaron algunas lineas en el requerimiento
				$sql = "SELECT Estado
						FROM lg_requerimientosdet
						WHERE
							CodRequerimiento = '".$CodRequerimiento."' AND
							Estado = 'CO'";
				$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
				if (mysql_num_rows($query) != 0) {
					$sql = "UPDATE lg_requerimientos
							SET Estado = 'CO'
							WHERE CodRequerimiento = '".$CodRequerimiento."'";
					execute($sql);
				} else {
					$sql = "UPDATE lg_requerimientos
							SET Estado = 'CE'
							WHERE CodRequerimiento = '".$CodRequerimiento."'";
					execute($sql);
				}
			}
		}
		mysql_query("COMMIT");
	}
}

//	transaccion (almacen)
elseif ($modulo == "transaccion_almacen") {
	$Comentarios = changeUrl($Comentarios);
	$FechaDocumento = formatFechaAMD($FechaDocumento);
	$AnioDocumento = substr($FechaDocumento, 0, 4);
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	periodo
		$Periodo = substr($FechaDocumento, 0, 7);
		if ($CodTransaccion == 'ARE') {
			##	completo requerimiento
			$sql = "UPDATE lg_requerimientos SET Estado = 'AP' WHERE CodRequerimiento = '".$ReferenciaNroDocumento."'";
			execute($sql);
			//	detalles
			$detalle = split(";char:tr;", $detalles);
			foreach ($detalle as $linea) {
				list($_CodItem, $_Descripcion, $_CodUnidad, $_StockActual, $_CantidadRecibida, $_PrecioUnit, $_Total, $_CodCentroCosto, $_ReferenciaCodDocumento, $_ReferenciaNroDocumento, $_ReferenciaSecuencia) = split(";char:td;", $linea);
				##	descompleto detalle del requerimiento
				$sql = "UPDATE lg_requerimientosdet
						SET
							CantidadRecibida = (CantidadRecibida - ".floatval($_CantidadRecibida)."),
							Estado = 'PE'
						WHERE
							CodRequerimiento = '".$ReferenciaNroDocumento."' AND
							Secuencia = '".$_ReferenciaSecuencia."'";
				execute($sql);
			}
		}
		else {
			if (!periodoAbierto($CodOrganismo, $Periodo)) die("No se puede generar ninguna transacci&oacute;n porque no se ha abierto el periodo $Periodo.");
			//	inserto transaccion
			##	genero el nuevo codigo
			$NroDocumento = getCodigo_3("lg_transaccion", "NroDocumento", "CodOrganismo", "CodDocumento", $CodOrganismo, $CodDocumento, 6);
			##	inserto
			$sql = "INSERT INTO lg_transaccion
					SET
						CodOrganismo = '".$CodOrganismo."',
						CodDocumento = '".$CodDocumento."',
						NroDocumento = '".$NroDocumento."',
						CodTransaccion = '".$CodTransaccion."',
						FechaDocumento = '".$FechaDocumento."',
						Periodo = '".$Periodo."',
						CodAlmacen = '".$CodAlmacen."',
						CodCentroCosto = '".$CodCentroCosto."',
						CodDocumentoReferencia = '".$CodDocumentoReferencia."',
						NroDocumentoReferencia = '".$NroDocumentoReferencia."',
						IngresadoPor = '".$IngresadoPor."',
						RecibidoPor = '".$RecibidoPor."',
						Comentarios = '".$Comentarios."',
						FlagManual = '".$FlagManual."',
						FlagPendiente = '".$FlagPendiente."',
						ReferenciaAnio = '".$AnioDocumento."',
						ReferenciaNroDocumento = '".$ReferenciaNroDocumento."',
						DocumentoReferencia = '".$DocumentoReferencia."',
						DocumentoReferenciaInterno = '".$DocumentoReferenciaInterno."',
						CodDependencia = '".$CodDependencia."',
						Anio = '".$AnioDocumento."',
						Estado = '".$Estado."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
			//	inserto detalles
			$_Secuencia = 0;
			$detalle = split(";char:tr;", $detalles);
			foreach ($detalle as $linea) {
				$_Secuencia++;
				list($_CodItem, $_Descripcion, $_CodUnidad, $_StockActual, $_CantidadRecibida, $_PrecioUnit, $_Total, $_CodCentroCosto, $_ReferenciaCodDocumento, $_ReferenciaNroDocumento, $_ReferenciaSecuencia) = split(";char:td;", $linea);
				##	inserto detalle
				$sql = "INSERT INTO lg_transacciondetalle
						SET
							CodOrganismo = '".$CodOrganismo."',
							CodDocumento = '".$CodDocumento."',
							NroDocumento = '".$NroDocumento."',
							Secuencia = '".$_Secuencia."',
							CodItem = '".$_CodItem."',
							Descripcion = '".$_Descripcion."',
							CodUnidad = '".$_CodUnidad."',
							CantidadPedida = '".$_CantidadRecibida."',
							CantidadRecibida = '".$_CantidadRecibida."',
							PrecioUnit = '".$_PrecioUnit."',
							Total = '".$_Total."',
							ReferenciaAnio = '".$AnioDocumento."',
							ReferenciaCodDocumento = '".$_ReferenciaCodDocumento."',
							ReferenciaNroDocumento = '".$_ReferenciaNroDocumento."',
							ReferenciaSecuencia = '".$_ReferenciaSecuencia."',
							CodCentroCosto = '".$_CodCentroCosto."',
							Estado = '".$Estado."',
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()";
				execute($sql);
			}
		}
		//	si es una reversa de transacciones
		if ($CodDocumentoReversa != '') {
			//	anulo la transaccion reversada
			$sql = "UPDATE lg_transaccion
					SET Estado = 'AN'
					WHERE
						CodOrganismo = '".$CodOrganismo."' AND
						CodDocumento = '".$CodDocumentoReversa."' AND
						NroDocumento = '".$NroDocumentoReversa."'";
			execute($sql);
			//	anulo detalles
			$sql = "UPDATE lg_transacciondetalle
					SET Estado = 'AN'
					WHERE
						CodOrganismo = '".$CodOrganismo."' AND
						CodDocumento = '".$CodDocumentoReversa."' AND
						NroDocumento = '".$NroDocumentoReversa."'";
			execute($sql);
		}
		mysql_query("COMMIT");
	}
	//	modificar
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		//	periodo
		$Periodo = substr($FechaDocumento, 0, 7);
		if (!periodoAbierto($CodOrganismo, $Periodo)) die("No se puede generar ninguna transacci&oacute;n porque no se ha abierto el periodo $Periodo.");
		
		//	modifico transaccion
		$sql = "UPDATE lg_transaccion
				SET
					FechaDocumento = '".$FechaDocumento."',
					Periodo = '".$Periodo."',
					CodAlmacen = '".$CodAlmacen."',
					CodCentroCosto = '".$CodCentroCosto."',
					CodDocumentoReferencia = '".$CodDocumentoReferencia."',
					NroDocumentoReferencia = '".$NroDocumentoReferencia."',
					IngresadoPor = '".$IngresadoPor."',
					RecibidoPor = '".$RecibidoPor."',
					Comentarios = '".$Comentarios."',
					FlagManual = '".$FlagManual."',
					FlagPendiente = '".$FlagPendiente."',
					ReferenciaAnio = '".$AnioDocumento."',
					ReferenciaNroDocumento = '".$ReferenciaNroDocumento."',
					DocumentoReferencia = '".$DocumentoReferencia."',
					DocumentoReferenciaInterno = '".$DocumentoReferenciaInterno."',
					CodDependencia = '".$CodDependencia."',
					Anio = '".$AnioDocumento."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					CodDocumento = '".$CodDocumento."' AND
					NroDocumento = '".$NroDocumento."'";
		execute($sql);
		
		//	inserto detalles
		$sql = "DELETE FROM lg_transacciondetalle
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					CodDocumento = '".$CodDocumento."' AND
					NroDocumento = '".$NroDocumento."'";
		$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		$_Secuencia = 0;
		$detalle = split(";char:tr;", $detalles);
		foreach ($detalle as $linea) {
			$_Secuencia++;
			list($_CodItem, $_Descripcion, $_CodUnidad, $_StockActual, $_CantidadRecibida, $_PrecioUnit, $_Total, $_CodCentroCosto, $_ReferenciaCodDocumento, $_ReferenciaNroDocumento, $_ReferenciaSecuencia) = split(";char:td;", $linea);
			##	inserto detalle
			$sql = "INSERT INTO lg_transacciondetalle
					SET
						CodOrganismo = '".$CodOrganismo."',
						CodDocumento = '".$CodDocumento."',
						NroDocumento = '".$NroDocumento."',
						Secuencia = '".$_Secuencia."',
						CodItem = '".$_CodItem."',
						Descripcion = '".$_Descripcion."',
						CodUnidad = '".$_CodUnidad."',
						CantidadPedida = '".$_CantidadPedida."',
						CantidadRecibida = '".$_CantidadRecibida."',
						PrecioUnit = '".$_PrecioUnit."',
						Total = '".$_Total."',
						ReferenciaAnio = '".$AnioDocumento."',
						ReferenciaCodDocumento = '".$_ReferenciaCodDocumento."',
						ReferenciaNroDocumento = '".$_ReferenciaNroDocumento."',
						ReferenciaSecuencia = '".$_ReferenciaSecuencia."',
						CodCentroCosto = '".$_CodCentroCosto."',
						Estado = '".$Estado."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		mysql_query("COMMIT");
	}
	//	ejecutar
	elseif ($accion == "ejecutar") {
		mysql_query("BEGIN");
		##	genero el nuevo codigo
		$NroInterno = getCodigo("lg_transaccion", "NroInterno", 6, "Anio", $AnioDocumento, "CodOrganismo", $CodOrganismo, "CodDocumento", $CodDocumento);
		//	
		$sql = "UPDATE lg_transaccion
				SET
					NroInterno = '".$NroInterno."',
					Estado = 'CO',
					EjecutadoPor = '".$_SESSION["CODPERSONA_ACTUAL"]."',
					FechaEjecucion = NOW(),
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					CodDocumento = '".$CodDocumento."' AND
					NroDocumento = '".$NroDocumento."'";
		execute($sql);
		
		//	
		$sql = "UPDATE lg_transacciondetalle
				SET
					Estado = 'CO',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					CodDocumento = '".$CodDocumento."' AND
					NroDocumento = '".$NroDocumento."'";
		execute($sql);
		
		echo "|$NroInterno";
		mysql_query("COMMIT");
	}
}

//	commodity
elseif ($modulo == "almacen-commodity") {
	$Comentarios = changeUrl($Comentarios);
	$FechaDocumento = formatFechaAMD($FechaDocumento);
	//	recepcion
	if ($accion == "recepcion") {
		mysql_query("BEGIN");
		//-------------------
		//	errores
		##	periodo
		$AnioDocumento = substr($FechaDocumento, 0, 4);
		$Periodo = substr($FechaDocumento, 0, 7);
		if (!periodoAbierto($CodOrganismo, $Periodo)) die("No se puede generar ninguna transacci&oacute;n porque no se ha abierto el periodo $Periodo.");
		##	documento
		$sql = "SELECT Estado
				FROM ap_documentos
				WHERE
					Anio = '".$Anio."' AND
					CodProveedor = '".$CodProveedor."' AND
					DocumentoClasificacion = '".$CodTransaccion."' AND
					DocumentoReferencia = '".$DocumentoReferencia."'";
		$query_documento = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		if (mysql_num_rows($query_documento) != 0) die("<strong>Doc. Ref / G. Remisión</strong> ya se encuentra registrado");
		##	activos
		if ($FlagActivoFijo == "S") {
			$activo = split(";char:tr;", $activos);
			foreach ($activo as $linea) {
				list($_Secuencia, $_NroSecuencia, $_CommoditySub, $_Descripcion, $_CodClasificacion, $_Monto, $_NroSerie, $_FechaIngreso, $_Modelo, $_CodBarra, $_CodUbicacion, $_CodCentroCosto, $_NroPlaca, $_CodMarca, $_Color) = split(";char:td;", $linea);
				//	consulto
				$sql = "SELECT Estado
						FROM lg_activofijo
						WHERE
							CodOrganismo = '".$CodOrganismo."' AND
							Anio = '".$Anio."' AND
							NroOrden = '".$ReferenciaNroDocumento."' AND
							Secuencia = '".$_Secuencia."' AND
							NroSecuencia = '".$_NroSecuencia."'";
				$query_activo = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
				if (mysql_num_rows($query_activo) != 0) die("Se encontraron lineas en la ficha de <strong>Activos Asociados</strong> ya ingresados");
			}
		}
		//	consulto orden
		$sql = "SELECT
					NroInterno,
					FechaOrden
				FROM lg_ordencompra
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					NroOrden = '".$ReferenciaNroDocumento."'";
		$query_oc = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		if (mysql_num_rows($query_oc) != 0) $field_oc = mysql_fetch_array($query_oc);
		//	inserto transaccion
		##	genero el nuevo codigo
		$NroDocumento = getCodigo_3("lg_commoditytransaccion", "NroDocumento", "CodOrganismo", "CodDocumento", $CodOrganismo, $CodDocumento, 6);
		$NroInterno = getCodigo("lg_commoditytransaccion", "NroInterno", 6, "Anio", $AnioDocumento, "CodOrganismo", $CodOrganismo, "CodDocumento", $CodDocumento);
		##	inserto
		$sql = "INSERT INTO lg_commoditytransaccion
				SET
					CodOrganismo = '".$CodOrganismo."',
					CodDocumento = '".$CodDocumento."',
					NroDocumento = '".$NroDocumento."',
					NroInterno = '".$NroInterno."',
					CodTransaccion = '".$CodTransaccion."',
					FechaDocumento = '".$FechaDocumento."',
					Periodo = '".$Periodo."',
					CodAlmacen = '".$CodAlmacen."',
					CodCentroCosto = '".$CodCentroCosto."',
					CodDocumentoReferencia = '".$CodDocumentoReferencia."',
					NroDocumentoReferencia = '".$NroDocumentoReferencia."',
					IngresadoPor = '".$IngresadoPor."',
					RecibidoPor = '".$RecibidoPor."',
					EjecutadoPor = '".$EjecutadoPor."',
					FechaEjecucion = NOW(),
					Comentarios = '".$Comentarios."',
					ReferenciaAnio = '".$Anio."',
					ReferenciaNroDocumento = '".$ReferenciaNroDocumento."',
					DocumentoReferencia = '".$DocumentoReferencia."',
					DocumentoReferenciaInterno = '".$DocumentoReferenciaInterno."',
					CodUbicacion = '".$CodUbicacion."',
					NotaEntrega = '".$NotaEntrega."',
					FlagActivoFijo = '".$FlagActivoFijo."',
					CodDependencia = '".$CodDependencia."',
					Anio = '".$AnioDocumento."',
					FlagManual = '".$FlagManual."',
					FlagPendiente = '".$FlagPendiente."',
					Estado = 'CO',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		//	inserto documento
		$sql = "INSERT INTO ap_documentos
				SET 
					Anio = '".$Anio."',
					CodOrganismo = '".$CodOrganismo."',
					CodProveedor = '".$CodProveedor."',
					DocumentoClasificacion = '".$CodTransaccion."',
					DocumentoReferencia = '".$DocumentoReferencia."',
					Fecha = NOW(),
					ReferenciaTipoDocumento = 'OC',
					ReferenciaNroDocumento = '".$ReferenciaNroDocumento."',
					Estado = '".$Estado."',
					TransaccionTipoDocumento = '".$CodDocumento."',
					TransaccionNroDocumento = '".$NroDocumento."',
					Comentarios = '".$Comentarios."',
					CodCentroCosto = '".$CodCentroCosto."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		//	inserto detalles
		$_Secuencia = 0;
		$detalle = split(";char:tr;", $detalles);
		foreach ($detalle as $linea) {
			$_Secuencia++;
			list($_CommoditySub, $_Descripcion, $_CodUnidad, $_CantidadPedida, $_CantidadPendiente, $_CantidadRecibida, $_FlagExonerado, $_PrecioUnit, $_Total, $_CodUnidadCompra, $_CantidadPedidaCompra, $_CantidadCompra, $_PrecioUnitCompra, $_CodClasificacion, $_CodCentroCosto, $_ReferenciaCodDocumento, $_ReferenciaNroDocumento, $_ReferenciaNroInterno, $_ReferenciaSecuencia) = split(";char:td;", $linea);
			$_Descripcion = changeUrl($_Descripcion);
			##	
			$_PrecioCantidad = $_CantidadCompra * $_PrecioUnitCompra;
			if ($_FlagExonerado == "S") {
				$MontoNoAfecto += $_PrecioCantidad;
			} else {
				$MontoAfecto += $_PrecioCantidad;
			}
			##	inserto detalle
			$sql = "INSERT INTO lg_commoditytransacciondetalle
					SET
						CodOrganismo = '".$CodOrganismo."',
						CodDocumento = '".$CodDocumento."',
						NroDocumento = '".$NroDocumento."',
						Secuencia = '".$_Secuencia."',
						CommoditySub = '".$_CommoditySub."',
						Descripcion = '".$_Descripcion."',
						CodUnidad = '".$_CodUnidad."',
						CantidadKardex = '".$_CantidadRecibida."',
						Cantidad = '".$_CantidadRecibida."',
						PrecioUnit = '".$_PrecioUnit."',
						Total = '".$_Total."',
						ReferenciaAnio = '".$Anio."',
						ReferenciaCodDocumento = '".$_ReferenciaCodDocumento."',
						ReferenciaNroDocumento = '".$_ReferenciaNroDocumento."',
						ReferenciaNroInterno = '".$_ReferenciaNroInterno."',
						ReferenciaSecuencia = '".$_ReferenciaSecuencia."',
						CodAlmacen = '".$CodAlmacen."',
						CodCentroCosto = '".$_CodCentroCosto."',
						Anio = '".$AnioDocumento."',
						CodUnidadCompra = '".$_CodUnidadCompra."',
						CantidadCompra = '".$_CantidadCompra."',
						PrecioUnitCompra = '".$_PrecioUnitCompra."',
						Estado = 'CO',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
			##	inserto documento detalle
			$sql = "INSERT INTO ap_documentosdetalle
					SET
						Anio = '".$Anio."',
						CodProveedor = '".$CodProveedor."',
						DocumentoClasificacion = '".$CodTransaccion."',
						DocumentoReferencia = '".$DocumentoReferencia."',
						Secuencia = '".$_Secuencia."',
						ReferenciaSecuencia = '".$_ReferenciaSecuencia."',
						CommoditySub = '".$_CommoditySub."',
						Descripcion = '".$_Descripcion."',
						Cantidad = '".$_CantidadRecibida."',
						PrecioUnit = '".$_PrecioUnit."',
						PrecioCantidad = '".$_PrecioCantidad."',
						Total = '".$_Total."',
						CodCentroCosto = '".$_CodCentroCosto."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
			if ($_FlagExonerado == "S") $_SumMontoAfecto += $_PrecioCantidad;
			else $_SumMontoNoAfecto += $_PrecioCantidad;
			$_SumMontoTotal += $_Total;
			$_SumMontoImpuestos += ($_Total - $_PrecioCantidad);
			//	actualizo orden
			##	si se scompleto el despacho
			if ($_CantidadPendiente == $_CantidadRecibida) {
				##	completo detalle de la orden
				$sql = "UPDATE lg_ordencompradetalle
						SET Estado = 'CO'
						WHERE
							Anio = '".$Anio."' AND
							CodOrganismo = '".$CodOrganismo."' AND
							NroOrden = '".$NroOrden."' AND
							Secuencia = '".$_ReferenciaSecuencia."'";
				execute($sql);
				##	si se completaron los detalles
				$sql = "SELECT *
						FROM lg_ordencompradetalle
						WHERE
							Anio = '".$Anio."' AND
							CodOrganismo = '".$CodOrganismo."' AND
							NroOrden = '".$NroOrden."' AND
							Estado = 'PE'";
				$query_pendientes = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
				if (mysql_num_rows($query_pendientes) == 0) {
					##	completo orden
					$sql = "UPDATE lg_ordencompra
							SET Estado = 'CO'
							WHERE
								Anio = '".$Anio."' AND
								CodOrganismo = '".$CodOrganismo."' AND
								NroOrden = '".$NroOrden."'";
					execute($sql);
				}
			}
			##	actualizo cantidad pendiente
			$sql = "UPDATE lg_ordencompradetalle
					SET CantidadRecibida = (CantidadRecibida + ".floatval($_CantidadRecibida).")
					WHERE
						Anio = '".$Anio."' AND
						CodOrganismo = '".$CodOrganismo."' AND
						NroOrden = '".$NroOrden."' AND
						Secuencia = '".$_ReferenciaSecuencia."'";
			execute($sql);
		}
		##	consulto los montos de la orden de compra
		$sql = "SELECT
					MontoAfecto,
					MontoNoAfecto,
					MontoIGV
				FROM lg_ordencompra
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					NroOrden = '".$ReferenciaNroDocumento."'";
		$query_afecto = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		if (mysql_num_rows($query_afecto) != 0) $field_afecto = mysql_fetch_array($query_afecto);
		##	actualizo montos del documento
		if ($MontoAfecto != $field_afecto['MontoAfecto']) {
			$MontoImpuestos = round(($MontoAfecto * $field_afecto['MontoIGV'] / $field_afecto['MontoAfecto']), 2);
		} else {
			$MontoAfecto = $field_afecto['MontoAfecto'];
			$MontoNoAfecto = $field_afecto['MontoNoAfecto'];
			$MontoImpuestos = $field_afecto['MontoIGV'];
		}
		$MontoTotal = $MontoAfecto + $MontoNoAfecto + $MontoImpuestos;
		##	actualizo montos del documento
		$sql = "UPDATE ap_documentos
				SET
					MontoAfecto = '".$MontoAfecto."',
					MontoNoAfecto = '".$MontoNoAfecto."',
					MontoImpuestos = '".$MontoImpuestos."',
					MontoTotal = '".$MontoTotal."',
					MontoPendiente = '".$MontoTotal."'
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					CodProveedor = '".$CodProveedor."' AND
					DocumentoClasificacion = '".$CodTransaccion."' AND
					DocumentoReferencia = '".$DocumentoReferencia."'";
		execute($sql);
		//	si es una transaccion de activos fijoss
		if ($FlagActivoFijo == "S") {
			//	inserto activos
			$activo = split(";char:tr;", $activos);
			foreach ($activo as $linea) {
				list($_Secuencia, $_NroSecuencia, $_CommoditySub, $_Descripcion, $_CodClasificacion, $_Monto, $_NroSerie, $_FechaIngreso, $_Modelo, $_CodBarra, $_CodUbicacion, $_CodCentroCosto, $_NroPlaca, $_CodMarca, $_Color) = split(";char:td;", $linea);
				$_Descripcion = changeUrl($_Descripcion);
				##	inserto activo				
				$sql = "INSERT INTO lg_activofijo
						SET
							CodOrganismo = '".$CodOrganismo."',
							Anio = '".$Anio."',
							NroOrden = '".$ReferenciaNroDocumento."',
							NroInterno = '".$field_oc['NroInterno']."',
							Secuencia = '".$_Secuencia."',
							NroSecuencia = '".$_NroSecuencia."',
							CommoditySub = '".$_CommoditySub."',
							Descripcion = '".$_Descripcion."',
							CodCentroCosto = '".$_CodCentroCosto."',
							CodClasificacion = '".$_CodClasificacion."',
							CodBarra = '".$_CodBarra."',
							NroSerie = '".$_NroSerie."',
							Modelo = '".$_Modelo."',
							CodProveedor = '".$CodProveedor."',
							CodDocumento = '".$CodDocumento."',
							NroDocumento = '".$NroDocumento."',
							Monto = '".$_Monto."',
							CodUbicacion = '".$_CodUbicacion."',
							FechaIngreso = '".formatFechaAMD($_FechaIngreso)."',
							FlagFacturado = 'N',
							CodMarca = '".$_CodMarca."',
							Color = '".$_Color."',
							NroPlaca = '".$_NroPlaca."',
							NumeroOrdenFecha = '".$field_oc['FechaOrden']."',
							Estado = 'PE',
							Clasificacion = '".$Clasificacion."',
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()";
				execute($sql);
			}
		}
		echo "|$NroDocumento|$NroInterno";
		//-------------------
		mysql_query("COMMIT");
	}
	//	despacho
	elseif ($accion == "despacho") {
		mysql_query("BEGIN");
		//	errores
		##	periodo
		$AnioDocumento = substr($FechaDocumento, 0, 4);
		$Periodo = substr($FechaDocumento, 0, 7);
		if (!periodoAbierto($CodOrganismo, $Periodo)) die("No se puede generar ninguna transacci&oacute;n porque no se ha abierto el periodo $Periodo.");
		
		//	inserto transaccion
		##	genero el nuevo codigo
		$NroDocumento = getCodigo_3("lg_commoditytransaccion", "NroDocumento", "CodOrganismo", "CodDocumento", $CodOrganismo, $CodDocumento, 6);
		$NroInterno = getCodigo("lg_commoditytransaccion", "NroInterno", 6, "Anio", $AnioDocumento, "CodOrganismo", $CodOrganismo, "CodDocumento", $CodDocumento);
		##	inserto
		$sql = "INSERT INTO lg_commoditytransaccion
				SET
					CodOrganismo = '".$CodOrganismo."',
					CodDocumento = '".$CodDocumento."',
					NroDocumento = '".$NroDocumento."',
					NroInterno = '".$NroInterno."',
					CodTransaccion = '".$CodTransaccion."',
					FechaDocumento = '".$FechaDocumento."',
					Periodo = '".$Periodo."',
					CodAlmacen = '".$CodAlmacen."',
					CodCentroCosto = '".$CodCentroCosto."',
					CodDocumentoReferencia = '".$CodDocumentoReferencia."',
					NroDocumentoReferencia = '".$NroDocumentoReferencia."',
					IngresadoPor = '".$IngresadoPor."',
					RecibidoPor = '".$RecibidoPor."',
					EjecutadoPor = '".$EjecutadoPor."',
					FechaEjecucion = NOW(),
					Comentarios = '".$Comentarios."',
					ReferenciaAnio = '".$Anio."',
					ReferenciaNroDocumento = '".$ReferenciaNroDocumento."',
					DocumentoReferencia = '".$DocumentoReferencia."',
					DocumentoReferenciaInterno = '".$DocumentoReferenciaInterno."',
					CodUbicacion = '".$CodUbicacion."',
					FlagActivoFijo = '".$FlagActivoFijo."',
					CodDependencia = '".$CodDependencia."',
					Anio = '".$AnioDocumento."',
					FlagManual = '".$FlagManual."',
					FlagPendiente = '".$FlagPendiente."',
					Estado = 'CO',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		
		//	inserto detalles
		$_Secuencia = 0;
		$detalle = split(";char:tr;", $detalles);
		foreach ($detalle as $linea) {
			$_Secuencia++;
			list($_CommoditySub, $_Descripcion, $_CodUnidad, $_StockActual, $_CantidadPedida, $_CantidadPendiente, $_CantidadRecibida, $_PrecioUnit, $_Total, $_CodCentroCosto, $_ReferenciaCodDocumento, $_ReferenciaNroDocumento, $_ReferenciaNroInterno, $_ReferenciaSecuencia, $_CodRequerimiento) = split(";char:td;", $linea);
			$_Descripcion = changeUrl($_Descripcion);
			$_PrecioCantidad = $_CantidadRecibida * $_PrecioUnit;
			
			##	inserto detalle
			$sql = "INSERT INTO lg_commoditytransacciondetalle
					SET
						CodOrganismo = '".$CodOrganismo."',
						CodDocumento = '".$CodDocumento."',
						NroDocumento = '".$NroDocumento."',
						Secuencia = '".$_Secuencia."',
						CommoditySub = '".$_CommoditySub."',
						Descripcion = '".$_Descripcion."',
						CodUnidad = '".$_CodUnidad."',
						CantidadKardex = '".$_CantidadRecibida."',
						Cantidad = '".$_CantidadRecibida."',
						PrecioUnit = '".$_PrecioUnit."',
						Total = '".$_Total."',
						ReferenciaAnio = '".$Anio."',
						ReferenciaCodDocumento = '".$_ReferenciaCodDocumento."',
						ReferenciaNroDocumento = '".$_ReferenciaNroDocumento."',
						ReferenciaNroInterno = '".$_ReferenciaNroInterno."',
						ReferenciaSecuencia = '".$_ReferenciaSecuencia."',
						CodAlmacen = '".$CodAlmacen."',
						CodCentroCosto = '".$_CodCentroCosto."',
						Anio = '".$AnioDocumento."',
						Estado = 'CO',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
			
			//	actualizo requerimiento detalle
			$sql = "UPDATE lg_requerimientosdet
					SET CantidadRecibida = CantidadRecibida + '".$_CantidadRecibida."'
					WHERE
						CodRequerimiento = '".$_CodRequerimiento."' AND
						Secuencia = '".$_ReferenciaSecuencia."'";
			execute($sql);
		}
		echo "|$NroDocumento|$NroInterno";
		mysql_query("COMMIT");
	}
}

//	transaccion (commodity)
elseif ($modulo == "transaccion_commodity") {
	$Comentarios = changeUrl($Comentarios);
	$FechaDocumento = formatFechaAMD($FechaDocumento);
	$AnioDocumento = substr($FechaDocumento, 0, 4);
	
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	periodo
		$Periodo = substr($FechaDocumento, 0, 7);
		if (!periodoAbierto($CodOrganismo, $Periodo)) die("No se puede generar ninguna transacci&oacute;n porque no se ha abierto el periodo $Periodo.");
		
		//	inserto transaccion
		##	genero el nuevo codigo
		$NroDocumento = getCodigo_3("lg_commoditytransaccion", "NroDocumento", "CodOrganismo", "CodDocumento", $CodOrganismo, $CodDocumento, 6);
		##	inserto
		$sql = "INSERT INTO lg_commoditytransaccion
				SET
					CodOrganismo = '".$CodOrganismo."',
					CodDocumento = '".$CodDocumento."',
					NroDocumento = '".$NroDocumento."',
					CodTransaccion = '".$CodTransaccion."',
					FechaDocumento = '".$FechaDocumento."',
					Periodo = '".$Periodo."',
					CodAlmacen = '".$CodAlmacen."',
					CodCentroCosto = '".$CodCentroCosto."',
					CodDocumentoReferencia = '".$CodDocumentoReferencia."',
					NroDocumentoReferencia = '".$NroDocumentoReferencia."',
					IngresadoPor = '".$IngresadoPor."',
					RecibidoPor = '".$RecibidoPor."',
					Comentarios = '".$Comentarios."',
					ReferenciaAnio = '".$AnioDocumento."',
					ReferenciaNroDocumento = '".$ReferenciaNroDocumento."',
					DocumentoReferencia = '".$DocumentoReferencia."',
					DocumentoReferenciaInterno = '".$DocumentoReferenciaInterno."',
					CodUbicacion = '".$CodUbicacion."',
					FlagActivoFijo = '".$FlagActivoFijo."',
					CodDependencia = '".$CodDependencia."',
					Anio = '".$AnioDocumento."',
					FlagManual = '".$FlagManual."',
					FlagPendiente = '".$FlagPendiente."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		
		//	inserto detalles
		$_Secuencia = 0;
		$detalle = split(";char:tr;", $detalles);
		foreach ($detalle as $linea) {
			$_Secuencia++;
			list($_CommoditySub, $_Descripcion, $_CodUnidad, $_StockActual, $_CantidadRecibida, $_PrecioUnit, $_Total, $_CodCentroCosto, $_ReferenciaCodDocumento, $_ReferenciaNroDocumento, $_ReferenciaSecuencia, $_CodRequerimiento) = split(";char:td;", $linea);
			$_Descripcion = changeUrl($_Descripcion);
			$_PrecioCantidad = $_CantidadRecibida * $_PrecioUnit;
			
			##	inserto detalle
			$sql = "INSERT INTO lg_commoditytransacciondetalle
					SET
						CodOrganismo = '".$CodOrganismo."',
						CodDocumento = '".$CodDocumento."',
						NroDocumento = '".$NroDocumento."',
						Secuencia = '".$_Secuencia."',
						CommoditySub = '".$_CommoditySub."',
						Descripcion = '".$_Descripcion."',
						CodUnidad = '".$_CodUnidad."',
						CantidadKardex = '".$_CantidadRecibida."',
						Cantidad = '".$_CantidadRecibida."',
						PrecioUnit = '".$_PrecioUnit."',
						Total = '".$_Total."',
						ReferenciaAnio = '".$AnioDocumento."',
						ReferenciaCodDocumento = '".$_ReferenciaCodDocumento."',
						ReferenciaNroDocumento = '".$_ReferenciaNroDocumento."',
						ReferenciaSecuencia = '".$_ReferenciaSecuencia."',
						CodAlmacen = '".$CodAlmacen."',
						CodCentroCosto = '".$_CodCentroCosto."',
						Anio = '".$AnioDocumento."',
						Estado = '".$Estado."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		mysql_query("COMMIT");
	}
	
	//	modificar
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		//	periodo
		$Periodo = substr($FechaDocumento, 0, 7);
		if (!periodoAbierto($CodOrganismo, $Periodo)) die("No se puede generar ninguna transacci&oacute;n porque no se ha abierto el periodo $Periodo.");
		
		//	modifico transaccion
		$sql = "UPDATE lg_commoditytransaccion
				SET
					FechaDocumento = '".$FechaDocumento."',
					Periodo = '".$Periodo."',
					CodAlmacen = '".$CodAlmacen."',
					CodCentroCosto = '".$CodCentroCosto."',
					CodDocumentoReferencia = '".$CodDocumentoReferencia."',
					NroDocumentoReferencia = '".$NroDocumentoReferencia."',
					RecibidoPor = '".$RecibidoPor."',
					Comentarios = '".$Comentarios."',
					ReferenciaAnio = '".$AnioDocumento."',
					ReferenciaNroDocumento = '".$ReferenciaNroDocumento."',
					DocumentoReferencia = '".$DocumentoReferencia."',
					DocumentoReferenciaInterno = '".$DocumentoReferenciaInterno."',
					CodUbicacion = '".$CodUbicacion."',
					FlagActivoFijo = '".$FlagActivoFijo."',
					CodDependencia = '".$CodDependencia."',
					Anio = '".$AnioDocumento."',
					FlagManual = '".$FlagManual."',
					FlagPendiente = '".$FlagPendiente."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					CodDocumento = '".$CodDocumento."' AND
					NroDocumento = '".$NroDocumento."'";
		execute($sql);
		
		//	inserto detalles
		$sql = "DELETE FROM lg_commoditytransacciondetalle
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					CodDocumento = '".$CodDocumento."' AND
					NroDocumento = '".$NroDocumento."'";
		$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		$_Secuencia = 0;
		$detalle = split(";char:tr;", $detalles);
		foreach ($detalle as $linea) {
			$_Secuencia++;
			list($_CommoditySub, $_Descripcion, $_CodUnidad, $_StockActual, $_CantidadRecibida, $_PrecioUnit, $_Total, $_CodCentroCosto, $_ReferenciaCodDocumento, $_ReferenciaNroDocumento, $_ReferenciaSecuencia, $_CodRequerimiento) = split(";char:td;", $linea);
			$_Descripcion = changeUrl($_Descripcion);
			$_PrecioCantidad = $_CantidadRecibida * $_PrecioUnit;
			
			##	inserto detalle
			$sql = "INSERT INTO lg_commoditytransacciondetalle
					SET
						CodOrganismo = '".$CodOrganismo."',
						CodDocumento = '".$CodDocumento."',
						NroDocumento = '".$NroDocumento."',
						Secuencia = '".$_Secuencia."',
						CommoditySub = '".$_CommoditySub."',
						Descripcion = '".$_Descripcion."',
						CodUnidad = '".$_CodUnidad."',
						CantidadKardex = '".$_CantidadRecibida."',
						Cantidad = '".$_CantidadRecibida."',
						PrecioUnit = '".$_PrecioUnit."',
						Total = '".$_Total."',
						ReferenciaAnio = '".$AnioDocumento."',
						ReferenciaCodDocumento = '".$_ReferenciaCodDocumento."',
						ReferenciaNroDocumento = '".$_ReferenciaNroDocumento."',
						ReferenciaSecuencia = '".$_ReferenciaSecuencia."',
						CodAlmacen = '".$CodAlmacen."',
						CodCentroCosto = '".$_CodCentroCosto."',
						Anio = '".$AnioDocumento."',
						Estado = '".$Estado."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		mysql_query("COMMIT");
	}
	
	//	ejecutar
	elseif ($accion == "ejecutar") {
		mysql_query("BEGIN");
		##	genero el nuevo codigo
		$NroInterno = getCodigo("lg_commoditytransaccion", "NroInterno", 6, "Anio", $AnioDocumento, "CodOrganismo", $CodOrganismo, "CodDocumento", $CodDocumento);
		
		//	
		$sql = "UPDATE lg_commoditytransaccion
				SET
					NroInterno = '".$NroInterno."',
					Estado = 'CO',
					EjecutadoPor = '".$_SESSION["CODPERSONA_ACTUAL"]."',
					FechaEjecucion = NOW(),
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					CodDocumento = '".$CodDocumento."' AND
					NroDocumento = '".$NroDocumento."'";
		execute($sql);
		
		//	
		$sql = "UPDATE lg_commoditytransacciondetalle
				SET
					Estado = 'CO',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					CodDocumento = '".$CodDocumento."' AND
					NroDocumento = '".$NroDocumento."'";
		execute($sql);
		
		echo "|$CodDocumento|$NroInterno";
		mysql_query("COMMIT");
	}
	
	//	ejecutar
	elseif ($accion == "anular") {
		mysql_query("BEGIN");
		##	
		$sql = "UPDATE lg_commoditytransaccion
				SET
					Estado = 'AN',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					CodDocumento = '".$CodDocumento."' AND
					NroDocumento = '".$NroDocumento."'";
		execute($sql);
		##	
		$sql = "UPDATE lg_commoditytransacciondetalle
				SET
					Estado = 'AN',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					CodDocumento = '".$CodDocumento."' AND
					NroDocumento = '".$NroDocumento."'";
		execute($sql);
		##	
		if ($CodTransaccion == $_PARAMETRO['TRANSRECEPCOM']) {
			$detalle = split(";char:tr;", $detalles);
			foreach ($detalle as $linea) {
				$_Secuencia++;
				list($_CommoditySub, $_Descripcion, $_CodUnidad, $_StockActual, $_CantidadRecibida, $_PrecioUnit, $_Total, $_CodCentroCosto, $_ReferenciaCodDocumento, $_ReferenciaNroDocumento, $_ReferenciaSecuencia, $_CodRequerimiento) = split(";char:td;", $linea);
				##	
				$sql = "UPDATE lg_ordencompradetalle
						SET
							Estado = 'PE',
							CantidadRecibida = (CantidadRecibida - ".floatval($_CantidadRecibida).")
						WHERE
							Anio = '".$AnioDocumento."' AND
							CodOrganismo = '".$CodOrganismo."' AND
							NroOrden = '".$_ReferenciaNroDocumento."' AND
							Secuencia = '".$_ReferenciaSecuencia."'";
				execute($sql);
			}
			$sql = "UPDATE lg_ordencompra
					SET Estado = 'AP'
					WHERE
						Anio = '".$AnioDocumento."' AND
						CodOrganismo = '".$CodOrganismo."' AND
						NroOrden = '".$ReferenciaNroDocumento."'";
			execute($sql);
			##	
			$sql = "SELECT *
					FROM ap_documentos
					WHERE
						CodOrganismo = '".$CodOrganismo."' AND
						TransaccionTipoDocumento = '".$CodDocumento."' AND
						TransaccionNroDocumento = '".$NroDocumento."'";
			$field_doc = getRecord($sql);
			##	
			if ($field_doc['Estado'] == 'RV') die('No se puede Anular esta Transacci&oacute;n. Se ha generado su obligaci&oacute;n');
			##	
			$sql = "DELETE FROM ap_documentos
					WHERE
						CodOrganismo = '".$CodOrganismo."' AND
						TransaccionTipoDocumento = '".$CodDocumento."' AND
						TransaccionNroDocumento = '".$NroDocumento."'";
			execute($sql);
			##	
			$sql = "DELETE FROM ap_documentosdetalle
					WHERE
						Anio = '".$field_doc['Anio']."' AND
						CodProveedor = '".$field_doc['CodProveedor']."' AND
						DocumentoClasificacion = '".$field_doc['DocumentoClasificacion']."' AND
						DocumentoReferencia = '".$field_doc['DocumentoReferencia']."'";
			execute($sql);
		}
		##	
		mysql_query("COMMIT");
	}
}

//	transaccion (caja chica)
elseif ($modulo == "transaccion-cajachica") {
	$Comentarios = changeUrl($Comentarios);
	$FechaDocumento = formatFechaAMD($FechaDocumento);
	
	//	recepcion
	if ($accion == "recepcion") {
		mysql_query("BEGIN");
		//	errores
		##	periodo
		$Anio = substr($FechaDocumento, 0, 4);
		$Periodo = substr($FechaDocumento, 0, 7);
		if (!periodoAbierto($CodOrganismo, $Periodo)) die("No se puede generar ninguna transacci&oacute;n porque no se ha abierto el periodo $Periodo.");
		
		//	consulto si es un requerimiento de tipo autoreposicion
		$sql = "SELECT Clasificacion FROM lg_requerimientos WHERE CodRequerimiento = '".$CodRequerimiento."'";
		$query_rau = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		if (mysql_num_rows($query_rau) != 0) $field_rau = mysql_fetch_array($query_rau);
		
		//	consulto orden
		$sql = "SELECT
					NroInterno,
					FechaOrden
				FROM lg_ordencompra
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					NroOrden = '".$ReferenciaNroDocumento."'";
		$query_oc = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		if (mysql_num_rows($query_oc) != 0) $field_oc = mysql_fetch_array($query_oc);
		
		//	si es dirigido a commoditys
		if ($FlagCommodity == "S") {
			##	activos
			if ($FlagActivoFijo == "S") {
				$activo = split(";char:tr;", $activos);
				foreach ($activo as $linea) {
					list($_Secuencia, $_NroSecuencia, $_CommoditySub, $_Descripcion, $_CodClasificacion, $_Monto, $_NroSerie, $_FechaIngreso, $_Modelo, $_CodBarra, $_CodUbicacion, $_CodCentroCosto, $_NroPlaca, $_CodMarca, $_Color) = split(";char:td;", $linea);
					//	consulto
					$sql = "SELECT Estado
							FROM lg_activofijo
							WHERE
								CodOrganismo = '".$CodOrganismo."' AND
								Anio = '".$Anio."' AND
								NroOrden = '".$ReferenciaNroDocumento."' AND
								Secuencia = '".$_Secuencia."' AND
								NroSecuencia = '".$_NroSecuencia."'";
					$query_activo = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
					if (mysql_num_rows($query_activo) != 0) die("Se encontraron lineas en la ficha de <strong>Activos Asociados</strong> ya ingresados");
				}
			}
			
			//	inserto transaccion
			##	genero el nuevo codigo
			$NroDocumento = getCodigo_3("lg_commoditytransaccion", "NroDocumento", "CodOrganismo", "CodDocumento", $CodOrganismo, $CodDocumento, 6);
			$NroInterno = getCodigo("lg_commoditytransaccion", "NroInterno", 6, "Anio", $Anio, "CodOrganismo", $CodOrganismo, "CodDocumento", $CodDocumento);
			##	inserto
			$sql = "INSERT INTO lg_commoditytransaccion (
								CodOrganismo,
								CodDocumento,
								NroDocumento,
								NroInterno,
								CodTransaccion,
								FechaDocumento,
								Periodo,
								CodAlmacen,
								CodCentroCosto,
								CodDocumentoReferencia,
								NroDocumentoReferencia,
								IngresadoPor,
								RecibidoPor,
								Comentarios,
								ReferenciaNroDocumento,
								DocumentoReferencia,
								DocumentoReferenciaInterno,
								CodUbicacion,
								FlagActivoFijo,
								CodDependencia,
								Anio,
								FlagManual,
								FlagPendiente,
								Estado,
								UltimoUsuario,
								UltimaFecha
					) VALUES (
								'".$CodOrganismo."',
								'".$CodDocumento."',
								'".$NroDocumento."',
								'".$NroInterno."',
								'".$CodTransaccion."',
								'".$FechaDocumento."',
								'".$Periodo."',
								'".$CodAlmacen."',
								'".$CodCentroCosto."',
								'".$CodDocumentoReferencia."',
								'".$NroDocumentoReferencia."',
								'".$IngresadoPor."',
								'".$RecibidoPor."',
								'".$Comentarios."',
								'".$ReferenciaNroDocumento."',
								'".$DocumentoReferencia."',
								'".$DocumentoReferenciaInterno."',
								'".$CodUbicacion."',
								'".$FlagActivoFijo."',
								'".$CodDependencia."',
								'".$Periodo."',
								'".$FlagManual."',
								'".$FlagPendiente."',
								'CO',
								'".$_SESSION["USUARIO_ACTUAL"]."',
								NOW()
					)";
			execute($sql);
		} else {
			//	inserto transaccion
			##	genero el nuevo codigo
			$NroDocumento = getCodigo_3("lg_transaccion", "NroDocumento", "CodOrganismo", "CodDocumento", $CodOrganismo, $CodDocumento, 6);
			$NroInterno = getCodigo("lg_transaccion", "NroInterno", 6, "Anio", $Anio, "CodOrganismo", $CodOrganismo, "CodDocumento", $CodDocumento);
			##	inserto
			$sql = "INSERT INTO lg_transaccion (
								CodOrganismo,
								CodDocumento,
								NroDocumento,
								NroInterno,
								CodTransaccion,
								FechaDocumento,
								Periodo,
								CodAlmacen,
								CodCentroCosto,
								CodDocumentoReferencia,
								NroDocumentoReferencia,
								IngresadoPor,
								RecibidoPor,
								Comentarios,
								FlagManual,
								FlagPendiente,
								ReferenciaNroDocumento,
								DocumentoReferencia,
								DocumentoReferenciaInterno,
								CodDependencia,
								Anio,
								Estado,
								UltimoUsuario,
								UltimaFecha
					) VALUES (
								'".$CodOrganismo."',
								'".$CodDocumento."',
								'".$NroDocumento."',
								'".$NroInterno."',
								'".$CodTransaccion."',
								'".$FechaDocumento."',
								'".$Periodo."',
								'".$CodAlmacen."',
								'".$CodCentroCosto."',
								'".$CodDocumentoReferencia."',
								'".$NroDocumentoReferencia."',
								'".$IngresadoPor."',
								'".$RecibidoPor."',
								'".$Comentarios."',
								'".$FlagManual."',
								'".$FlagPendiente."',
								'".$ReferenciaNroDocumento."',
								'".$DocumentoReferencia."',
								'".$DocumentoReferenciaInterno."',
								'".$CodDependencia."',
								'".$Anio."',
								'CO',
								'".$_SESSION["USUARIO_ACTUAL"]."',
								NOW()
					)";
			execute($sql);
		}
		
		//	inserto detalles
		$_Secuencia = 0;
		$detalle = split(";char:tr;", $detalles);
		foreach ($detalle as $linea) {
			$_Secuencia++;
			list($_CodItem, $_CommoditySub, $_Descripcion, $_CodUnidad, $_CantidadPedida, $_CantidadRecibida, $_PrecioUnit, $_Total, $_CodCentroCosto, $_ReferenciaCodDocumento, $_ReferenciaNroDocumento, $_ReferenciaNroInterno, $_ReferenciaSecuencia) = split(";char:td;", $linea);
			$_Descripcion = changeUrl($_Descripcion);
			$_PrecioCantidad = $_CantidadRecibida * $_PrecioUnit;
			
			//	si es dirigido a commoditys
			if ($FlagCommodity == "S") {
				##	inserto detalle
				$sql = "INSERT INTO lg_commoditytransacciondetalle (
									CodOrganismo,
									CodDocumento,
									NroDocumento,
									Secuencia,
									CommoditySub,
									Descripcion,
									CodUnidad,
									CantidadKardex,
									Cantidad,
									PrecioUnit,
									Total,
									ReferenciaCodDocumento,
									ReferenciaNroDocumento,
									ReferenciaNroInterno,
									ReferenciaSecuencia,
									CodAlmacen,
									CodCentroCosto,
									Anio,
									UltimoUsuario,
									UltimaFecha
						) VALUES (
									'".$CodOrganismo."',
									'".$CodDocumento."',
									'".$NroDocumento."',
									'".$_Secuencia."',
									'".$_CommoditySub."',
									'".$_Descripcion."',
									'".$_CodUnidad."',
									'".$_CantidadRecibida."',
									'".$_CantidadRecibida."',
									'".$_PrecioUnit."',
									'".$_Total."',
									'".$_ReferenciaCodDocumento."',
									'".$_ReferenciaNroDocumento."',
									'".$_ReferenciaNroInterno."',
									'".$_ReferenciaSecuencia."',
									'".$CodAlmacen."',
									'".$_CodCentroCosto."',
									'".$Periodo."',
									'".$_SESSION["USUARIO_ACTUAL"]."',
									NOW()
						)";
				execute($sql);
			} else {
				##	inserto detalle
				$sql = "INSERT INTO lg_transacciondetalle (
									CodOrganismo,
									CodDocumento,
									NroDocumento,
									Secuencia,
									CodItem,
									Descripcion,
									CodUnidad,
									CantidadPedida,
									CantidadRecibida,
									PrecioUnit,
									Total,
									ReferenciaCodDocumento,
									ReferenciaNroDocumento,
									ReferenciaNroInterno,
									ReferenciaSecuencia,
									CodCentroCosto,
									UltimoUsuario,
									UltimaFecha
						) VALUES (
									'".$CodOrganismo."',
									'".$CodDocumento."',
									'".$NroDocumento."',
									'".$_Secuencia."',
									'".$_CodItem."',
									'".$_Descripcion."',
									'".$_CodUnidad."',
									'".$_CantidadPedida."',
									'".$_CantidadRecibida."',
									'".$_PrecioUnit."',
									'".$_Total."',
									'".$_ReferenciaCodDocumento."',
									'".$_ReferenciaNroDocumento."',
									'".$_ReferenciaNroInterno."',
									'".$_ReferenciaSecuencia."',
									'".$_CodCentroCosto."',
									'".$_SESSION["USUARIO_ACTUAL"]."',
									NOW()
						)";
				execute($sql);
			}
			
			//	actualizo requerimientos (solo si es un requerimiento de autoreposicion)
			if ($field_rau['Clasificacion'] == $_PARAMETRO['REQRAU']) {
				##	si se scompleto el despacho
				if ($_CantidadPedida == $_CantidadRecibida) {
					##	completo detalle del requerimiento
					$sql = "UPDATE lg_requerimientosdet
							SET Estado = 'CO'
							WHERE
								CodRequerimiento = '".$CodRequerimiento."' AND
								Secuencia = '".$_ReferenciaSecuencia."'";
					execute($sql);
					
					##	si se completaron los detalles
					$sql = "SELECT *
							FROM lg_requerimientosdet
							WHERE
								CodRequerimiento = '".$CodRequerimiento."' AND
								Estado = 'PE'";
					$query_pendientes = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
					if (mysql_num_rows($query_pendientes) == 0) {
						##	completo requerimiento
						$sql = "UPDATE lg_requerimientos
								SET Estado = 'CO'
								WHERE CodRequerimiento = '".$CodRequerimiento."'";
						execute($sql);
					}
				}
				##	actualizo cantidad recibida
				$sql = "UPDATE lg_requerimientosdet
						SET CantidadRecibida = (CantidadRecibida + ".floatval($_CantidadRecibida).")
						WHERE
							CodRequerimiento = '".$CodRequerimiento."' AND
							Secuencia = '".$_ReferenciaSecuencia."'";
				execute($sql);
			}			
			##	actualizo cantidad pendiente
			$sql = "UPDATE lg_requerimientosdet
					SET CantidadOrdenCompra = (CantidadOrdenCompra + ".floatval($_CantidadRecibida).")
					WHERE
						CodRequerimiento = '".$CodRequerimiento."' AND
						Secuencia = '".$_ReferenciaSecuencia."'";
			execute($sql);
			
			//	si es dirigido a commoditys
			if ($FlagCommodity == "S") {
				//	consulto el stock
				$sql = "SELECT *
						FROM lg_commoditystock
						WHERE
							CommoditySub = '".$_CommoditySub."' AND
							CodAlmacen = '".$CodAlmacen."'";
				$query_stock = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
				if (mysql_num_rows($query_stock) == 0) {
					//	inserto
					$sql = "INSERT INTO lg_commoditystock (
										CodAlmacen,
										CommoditySub,
										Cantidad,
										PrecioUnitario,
										IngresadoPor,
										UltimoUsuario,
										UltimaFecha
							) VALUES (
										'".$CodAlmacen."',
										'".$_CommoditySub."',
										'".$_CantidadRecibida."',
										'".$_PrecioUnit."',
										'".$IngresadoPor."',
										'".$_SESSION["USUARIO_ACTUAL"]."',
										NOW()
							)";
					execute($sql);
				} else {
					//	actualizo
					$sql = "UPDATE lg_commoditystock
							SET
								Cantidad = Cantidad + ".floatval($_CantidadRecibida).",
								UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
								UltimaFecha = NOW()
							WHERE
								CommoditySub = '".$_CommoditySub."' AND
								CodAlmacen = '".$CodAlmacen."'";
					execute($sql);
				}
			} else {
				##	consulto el stock
				$sql = "SELECT *
						FROM lg_itemalmacen
						WHERE
							CodAlmacen = '".$CodAlmacen."' AND
							CodItem = '".$_CodItem."'";
				$query_almacen = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
				if (mysql_num_rows($query_almacen) == 0) {
					##	inserto item en almacen
					$sql = "INSERT INTO lg_itemalmacen (
										CodItem,
										CodAlmacen,
										Estado,
										UltimoUsuario,
										UltimaFecha
							) VALUES (
										'".$_CodItem."',
										'".$CodAlmacen."',
										'A',
										'".$_SESSION["USUARIO_ACTUAL"]."',
										NOW()
							)";
					execute($sql);
					
					##	inserto item en inventario
					$sql = "INSERT INTO lg_itemalmaceninv (
										CodAlmacen,
										CodItem,
										Proveedor,
										FechaIngreso,
										StockIngreso,
										StockActual,
										PrecioUnitario,
										DocReferencia,
										IngresadoPor,
										UltimoUsuario,
										UltimaFecha
							) VALUES (
										'".$CodAlmacen."',
										'".$_CodItem."',
										'".$CodProveedor."',
										NOW(),
										'".$_CantidadRecibida."',
										'".$_CantidadRecibida."',
										'".$_PrecioUnit."',
										'".$_ReferenciaCodDocumento."-".$_ReferenciaNroDocumento."',
										'".$_SESSION["CODPERSONA_ACTUAL"]."',
										'".$_SESSION["USUARIO_ACTUAL"]."',
										NOW()
							)";
					execute($sql);
				} else {
					##	actualizo item en inventario
					$sql = "UPDATE lg_itemalmaceninv
							SET
								StockActual = (StockActual + ".floatval($_CantidadRecibida)."),
								UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
								UltimaFecha = NOW()
							WHERE
								CodAlmacen = '".$CodAlmacen."' AND
								CodItem = '".$_CodItem."'";
					execute($sql);
				}
			}
		}
		//	si es dirigido a commoditys
		if ($FlagCommodity == "S") {
			//	si es una transaccion de activos fijoss
			if ($FlagActivoFijo == "S") {
				//	inserto activos
				$activo = split(";char:tr;", $activos);
				foreach ($activo as $linea) {
					list($_Secuencia, $_NroSecuencia, $_CommoditySub, $_Descripcion, $_CodClasificacion, $_Monto, $_NroSerie, $_FechaIngreso, $_Modelo, $_CodBarra, $_CodUbicacion, $_CodCentroCosto, $_NroPlaca, $_CodMarca, $_Color) = split(";char:td;", $linea);
					$_Descripcion = changeUrl($_Descripcion);
				
					##	inserto activo
					$sql = "INSERT INTO lg_activofijo (
										CodOrganismo,
										Anio,
										NroOrden,
										NroInterno,
										Secuencia,
										NroSecuencia,
										CommoditySub,
										Descripcion,
										CodCentroCosto,
										CodClasificacion,
										CodBarra,
										NroSerie,
										Modelo,
										CodProveedor,
										CodDocumento,
										NroDocumento,
										Monto,
										CodUbicacion,
										FechaIngreso,
										FlagFacturado,
										CodMarca,
										Color,
										NroPlaca,
										NumeroOrdenFecha,
										Estado,
										Clasificacion,
										UltimoUsuario,
										UltimaFecha
							) VALUES (
										'".$CodOrganismo."',
										'".$Anio."',
										'".$ReferenciaNroDocumento."',
										'".$field_oc['NroInterno']."',
										'".$_Secuencia."',
										'".$_NroSecuencia."',
										'".$_CommoditySub."',
										'".$_Descripcion."',
										'".$_CodCentroCosto."',
										'".$_CodClasificacion."',
										'".$_CodBarra."',
										'".$_NroSerie."',
										'".$_Modelo."',
										'".$CodProveedor."',
										'".$CodDocumento."',
										'".$NroDocumento."',
										'".$_Monto."',
										'".$_CodUbicacion."',
										'".formatFechaAMD($_FechaIngreso)."',
										'N',
										'".$_CodMarca."',
										'".$_Color."',
										'".$_NroPlaca."',
										'".$field_oc['FechaOrden']."',
										'PR',
										'".$Clasificacion."',
										'".$_SESSION["USUARIO_ACTUAL"]."',
										NOW()
							)";
					execute($sql);
				}
			}
		}
		mysql_query("COMMIT");
		die("|Se ha generado la Transacci&oacute;n <strong>Nro. $CodDocumento-$NroInterno</strong>");
	}
	
	//	despacho
	elseif ($accion == "despacho") {
		mysql_query("BEGIN");
		//	errores
		##	periodo
		$Anio = substr($FechaDocumento, 0, 4);
		$Periodo = substr($FechaDocumento, 0, 7);
		if (!periodoAbierto($CodOrganismo, $Periodo)) die("No se puede generar ninguna transacci&oacute;n porque no se ha abierto el periodo $Periodo.");
		
		//	si es dirigido a commoditys
		if ($FlagCommodity == "S") {
			//	inserto transaccion
			##	genero el nuevo codigo
			$NroDocumento = getCodigo_3("lg_commoditytransaccion", "NroDocumento", "CodOrganismo", "CodDocumento", $CodOrganismo, $CodDocumento, 6);
			$NroInterno = getCodigo("lg_commoditytransaccion", "NroInterno", 6, "Anio", $Anio, "CodOrganismo", $CodOrganismo, "CodDocumento", $CodDocumento);
			##	inserto
			$sql = "INSERT INTO lg_commoditytransaccion (
								CodOrganismo,
								CodDocumento,
								NroDocumento,
								NroInterno,
								CodTransaccion,
								FechaDocumento,
								Periodo,
								CodAlmacen,
								CodCentroCosto,
								CodDocumentoReferencia,
								NroDocumentoReferencia,
								IngresadoPor,
								RecibidoPor,
								Comentarios,
								ReferenciaNroDocumento,
								DocumentoReferencia,
								DocumentoReferenciaInterno,
								CodDependencia,
								Anio,
								FlagManual,
								FlagPendiente,
								Estado,
								UltimoUsuario,
								UltimaFecha
					) VALUES (
								'".$CodOrganismo."',
								'".$CodDocumento."',
								'".$NroDocumento."',
								'".$NroInterno."',
								'".$CodTransaccion."',
								'".$FechaDocumento."',
								'".$Periodo."',
								'".$CodAlmacen."',
								'".$CodCentroCosto."',
								'".$CodDocumentoReferencia."',
								'".$NroDocumentoReferencia."',
								'".$IngresadoPor."',
								'".$RecibidoPor."',
								'".$Comentarios."',
								'".$ReferenciaNroDocumento."',
								'".$DocumentoReferencia."',
								'".$DocumentoReferenciaInterno."',
								'".$CodDependencia."',
								'".$Periodo."',
								'".$FlagManual."',
								'".$FlagPendiente."',
								'CO',
								'".$_SESSION["USUARIO_ACTUAL"]."',
								NOW()
					)";
			execute($sql);
		} else {
			//	inserto transaccion
			##	genero el nuevo codigo
			$NroDocumento = getCodigo_3("lg_transaccion", "NroDocumento", "CodOrganismo", "CodDocumento", $CodOrganismo, $CodDocumento, 6);
			
			$NroInterno = getCodigo("lg_transaccion", "NroInterno", 6, "Anio", $Anio, "CodOrganismo", $CodOrganismo, "CodDocumento", $CodDocumento);
			##	inserto
			$sql = "INSERT INTO lg_transaccion (
								CodOrganismo,
								CodDocumento,
								NroDocumento,
								NroInterno,
								CodTransaccion,
								FechaDocumento,
								Periodo,
								CodAlmacen,
								CodCentroCosto,
								CodDocumentoReferencia,
								NroDocumentoReferencia,
								IngresadoPor,
								RecibidoPor,
								Comentarios,
								FlagManual,
								FlagPendiente,
								ReferenciaNroDocumento,
								DocumentoReferencia,
								DocumentoReferenciaInterno,
								CodDependencia,
								Anio,
								Estado,
								UltimoUsuario,
								UltimaFecha
					) VALUES (
								'".$CodOrganismo."',
								'".$CodDocumento."',
								'".$NroDocumento."',
								'".$NroInterno."',
								'".$CodTransaccion."',
								'".$FechaDocumento."',
								'".$Periodo."',
								'".$CodAlmacen."',
								'".$CodCentroCosto."',
								'".$CodDocumentoReferencia."',
								'".$NroDocumentoReferencia."',
								'".$IngresadoPor."',
								'".$RecibidoPor."',
								'".$Comentarios."',
								'".$FlagManual."',
								'".$FlagPendiente."',
								'".$ReferenciaNroDocumento."',
								'".$DocumentoReferencia."',
								'".$DocumentoReferenciaInterno."',
								'".$CodDependencia."',
								'".$Anio."',
								'CO',
								'".$_SESSION["USUARIO_ACTUAL"]."',
								NOW()
					)";
			execute($sql);
		}
		
		//	inserto detalles
		$_Secuencia = 0;
		$detalle = split(";char:tr;", $detalles);
		foreach ($detalle as $linea) {
			$_Secuencia++;
			list($_CodItem, $_CommoditySub, $_Descripcion, $_CodUnidad, $_CantidadPedida, $_CantidadRecibida, $_PrecioUnit, $_Total, $_CodCentroCosto, $_ReferenciaCodDocumento, $_ReferenciaNroDocumento, $_ReferenciaNroInterno, $_ReferenciaSecuencia) = split(";char:td;", $linea);
			$_Descripcion = changeUrl($_Descripcion);
			$_PrecioCantidad = $_CantidadRecibida * $_PrecioUnit;
			
			//	si es dirigido a commoditys
			if ($FlagCommodity == "S") {
				##	inserto detalle
				$sql = "INSERT INTO lg_commoditytransacciondetalle (
									CodOrganismo,
									CodDocumento,
									NroDocumento,
									Secuencia,
									CommoditySub,
									Descripcion,
									CodUnidad,
									CantidadKardex,
									Cantidad,
									PrecioUnit,
									Total,
									ReferenciaCodDocumento,
									ReferenciaNroDocumento,
									ReferenciaNroInterno,
									ReferenciaSecuencia,
									CodAlmacen,
									CodCentroCosto,
									Anio,
									UltimoUsuario,
									UltimaFecha
						) VALUES (
									'".$CodOrganismo."',
									'".$CodDocumento."',
									'".$NroDocumento."',
									'".$_Secuencia."',
									'".$_CommoditySub."',
									'".$_Descripcion."',
									'".$_CodUnidad."',
									'".$_CantidadRecibida."',
									'".$_CantidadRecibida."',
									'".$_PrecioUnit."',
									'".$_Total."',
									'".$_ReferenciaCodDocumento."',
									'".$_ReferenciaNroDocumento."',
									'".$_ReferenciaNroInterno."',
									'".$_ReferenciaSecuencia."',
									'".$CodAlmacen."',
									'".$_CodCentroCosto."',
									'".$Periodo."',
									'".$_SESSION["USUARIO_ACTUAL"]."',
									NOW()
						)";
				execute($sql);
			} else {
				##	inserto detalle
				$sql = "INSERT INTO lg_transacciondetalle (
									CodOrganismo,
									CodDocumento,
									NroDocumento,
									Secuencia,
									CodItem,
									Descripcion,
									CodUnidad,
									CantidadPedida,
									CantidadRecibida,
									PrecioUnit,
									Total,
									ReferenciaCodDocumento,
									ReferenciaNroDocumento,
									ReferenciaNroInterno,
									ReferenciaSecuencia,
									CodCentroCosto,
									UltimoUsuario,
									UltimaFecha
						) VALUES (
									'".$CodOrganismo."',
									'".$CodDocumento."',
									'".$NroDocumento."',
									'".$_Secuencia."',
									'".$_CodItem."',
									'".$_Descripcion."',
									'".$_CodUnidad."',
									'".$_CantidadPedida."',
									'".$_CantidadRecibida."',
									'".$_PrecioUnit."',
									'".$_Total."',
									'".$_ReferenciaCodDocumento."',
									'".$_ReferenciaNroDocumento."',
									'".$_ReferenciaNroInterno."',
									'".$_ReferenciaSecuencia."',
									'".$_CodCentroCosto."',
									'".$_SESSION["USUARIO_ACTUAL"]."',
									NOW()
						)";
				execute($sql);
			}
			
			//	actualizo requerimientos
			##	si se scompleto el despacho
			if ($_CantidadPedida == $_CantidadRecibida) {
				##	completo detalle del requerimiento
				$sql = "UPDATE lg_requerimientosdet
						SET Estado = 'CO'
						WHERE
							CodRequerimiento = '".$CodRequerimiento."' AND
							Secuencia = '".$_ReferenciaSecuencia."'";
				execute($sql);
				
				##	si se completaron los detalles
				$sql = "SELECT *
						FROM lg_requerimientosdet
						WHERE
							CodRequerimiento = '".$CodRequerimiento."' AND
							Estado = 'PE'";
				$query_pendientes = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
				if (mysql_num_rows($query_pendientes) == 0) {
					##	completo requerimiento
					$sql = "UPDATE lg_requerimientos
							SET Estado = 'CO'
							WHERE CodRequerimiento = '".$CodRequerimiento."'";
					execute($sql);
				}
			}
			##	actualizo cantidad pendiente
			$sql = "UPDATE lg_requerimientosdet
					SET CantidadRecibida = (CantidadRecibida + ".floatval($_CantidadRecibida).")
					WHERE
						CodRequerimiento = '".$CodRequerimiento."' AND
						Secuencia = '".$_ReferenciaSecuencia."'";
			execute($sql);
			
			//	
			
			//	si es dirigido a commoditys
			if ($FlagCommodity == "S") {
				//	consulto el stock
				$sql = "SELECT *
						FROM lg_commoditystock
						WHERE
							CommoditySub = '".$_CommoditySub."' AND
							CodAlmacen = '".$CodAlmacen."'";
				$query_stock = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
				if (mysql_num_rows($query_stock) == 0) {
					//	inserto
					$sql = "INSERT INTO lg_commoditystock (
										CodAlmacen,
										CommoditySub,
										Cantidad,
										PrecioUnitario,
										IngresadoPor,
										UltimoUsuario,
										UltimaFecha
							) VALUES (
										'".$CodAlmacen."',
										'".$_CommoditySub."',
										'-".$_CantidadRecibida."',
										'".$_PrecioUnit."',
										'".$IngresadoPor."',
										'".$_SESSION["USUARIO_ACTUAL"]."',
										NOW()
							)";
					execute($sql);
				} else {
					//	actualizo
					$sql = "UPDATE lg_commoditystock
							SET
								Cantidad = Cantidad - ".floatval($_CantidadRecibida).",
								UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
								UltimaFecha = NOW()
							WHERE
								CommoditySub = '".$_CommoditySub."' AND
								CodAlmacen = '".$CodAlmacen."'";
					execute($sql);
				}
			} else {
				##	consulto el stock
				$sql = "SELECT *
						FROM lg_itemalmacen
						WHERE
							CodAlmacen = '".$CodAlmacen."' AND
							CodItem = '".$_CodItem."'";
				$query_almacen = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
				if (mysql_num_rows($query_almacen) == 0) {
					##	inserto item en almacen
					$sql = "INSERT INTO lg_itemalmacen (
										CodItem,
										CodAlmacen,
										Estado,
										UltimoUsuario,
										UltimaFecha
							) VALUES (
										'".$_CodItem."',
										'".$CodAlmacen."',
										'A',
										'".$_SESSION["USUARIO_ACTUAL"]."',
										NOW()
							)";
					execute($sql);
					
					##	inserto item en inventario
					$sql = "INSERT INTO lg_itemalmaceninv (
										CodAlmacen,
										CodItem,
										Proveedor,
										FechaIngreso,
										StockIngreso,
										StockActual,
										PrecioUnitario,
										DocReferencia,
										IngresadoPor,
										UltimoUsuario,
										UltimaFecha
							) VALUES (
										'".$CodAlmacen."',
										'".$_CodItem."',
										'".$CodProveedor."',
										NOW(),
										'-".$_CantidadRecibida."',
										'-".$_CantidadRecibida."',
										'".$_PrecioUnit."',
										'".$_ReferenciaCodDocumento."-".$_ReferenciaNroDocumento."',
										'".$_SESSION["CODPERSONA_ACTUAL"]."',
										'".$_SESSION["USUARIO_ACTUAL"]."',
										NOW()
							)";
					execute($sql);
				} else {
					##	actualizo item en inventario
					$sql = "UPDATE lg_itemalmaceninv
							SET
								StockActual = (StockActual - ".floatval($_CantidadRecibida)."),
								UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
								UltimaFecha = NOW()
							WHERE
								CodAlmacen = '".$CodAlmacen."' AND
								CodItem = '".$_CodItem."'";
					execute($sql);
				}
			}
		}
		mysql_query("COMMIT");
		die("|Se ha generado la Transacci&oacute;n <strong>Nro. $CodDocumento-$NroInterno</strong>");
	}
}

//	facturacion de activos
elseif ($modulo == "facturacion_activos") {
	mysql_query("BEGIN");
	//	-----------------
	$sql = "SELECT 
				NroControl,
				FechaFactura
			FROM ap_obligaciones
			WHERE
				CodProveedor = '".$CodProveedor."' AND
				CodTipoDocumento = '".$CodTipoDocumento."' AND
				NroDocumento = '".$NroDocumento."'";
	$field_factura = getRecord($sql);
	//	actualizar
	$sql = "UPDATE lg_activofijo
			SET
				FlagFacturado = 'S',
				ObligacionTipoDocumento = '".$CodTipoDocumento."',
				ObligacionNroDocumento = '".$NroDocumento."',
				ObligacionFechaDocumento = '".formatFechaAMD($FechaRegistro)."',
				NroFactura = '".$field_factura['NroControl']."',
				FechaFactura = '".$field_factura['FechaFactura']."',
				UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
				UltimaFecha = NOW()
			WHERE
				CodOrganismo = '".$CodOrganismo."' AND
				Anio = '".$Anio."' AND
				NroOrden = '".$NroOrden."' AND
				Secuencia = '".$Secuencia."' AND
				NroSecuencia = '".$NroSecuencia."'";
	execute($sql);
	//	-----------------
	mysql_query("COMMIT");
}

//	cotizaciones
elseif ($modulo == "cotizaciones") {
	//	invitar a cotizar
	if ($accion == "cotizaciones_items_invitar_proveedores") {
		mysql_query("BEGIN");
		//-------------------
		$Numero = intval(getCodigo("lg_cotizacion", "Numero", 10));
		$proveedores = split(";char:tr;", $detalles_proveedores);
		foreach ($proveedores as $proveedor) {
			list($_CodProveedor, $_NomProveedor, $_CodFormaPago) = split(";char:td;", $proveedor);
			##
			$NroCotizacionProv = getCodigo("lg_cotizacion", "NroCotizacionProv", 8);
			$NumeroInterno = getCodigo("lg_cotizacion", "NumeroInterno", 8, "Anio", date("Y"));
			$requerimientos = split(";", $detalles_requerimientos);
			foreach ($requerimientos as $requerimiento) {
				list($_CodRequerimiento, $_Secuencia, $_CodOrganismo, $_FlagExonerado) = split("_", $requerimiento);
				##
				$CotizacionNumero = getCodigo("lg_cotizacion", "CotizacionNumero", 8);
				$CantidadRequerimiento = getVar("lg_requerimientosdet", "CantidadPedida", "CodRequerimiento", $_CodRequerimiento, "Secuencia", $_Secuencia);
				$CodUnidad = getVar("lg_requerimientosdet", "CodUnidad", "CodRequerimiento", $_CodRequerimiento, "Secuencia", $_Secuencia);
				//	valido
				if (getNumRows("lg_cotizacion", "CodRequerimiento", $_CodRequerimiento, "Secuencia", $_Secuencia, "CodProveedor", $_CodProveedor) > 0) die("<strong>$_NomProveedor</strong> ya posee una invitaci&oacute;n para uno de los requerimientos.");
				//	inserto
				$sql = "INSERT INTO lg_cotizacion
						SET
							Numero = '".$Numero."',
							NroCotizacionProv = '".$NroCotizacionProv."',
							NumeroInvitacion = '".$NroCotizacionProv."',
							Anio = NOW(),
							NumeroInterno = '".$NumeroInterno."',
							CotizacionNumero = '".$CotizacionNumero."',
							Cantidad = '".$CantidadRequerimiento."',
							FechaApertura = NOW(),
							FechaDocumento = NOW(),
							FechaRecepcion = NOW(),
							FechaEntrega = NOW(),
							CodOrganismo = '".$_CodOrganismo."',
							CodRequerimiento = '".$_CodRequerimiento."',
							Secuencia = '".$_Secuencia."',
							CodProveedor = '".$_CodProveedor."',
							NomProveedor = '".changeUrl($_NomProveedor)."',
							FlagAsignado = 'N',
							FlagExonerado = '".$_FlagExonerado."',
							CodFormaPago = '".$_CodFormaPago."',
							FechaInvitacion = '".formatFechaAMD($FechaInvitacion)."',
							FechaLimite = '".formatFechaAMD($FechaLimite)."',
							Condiciones = '".changeUrl($Condiciones)."',
							Observaciones = '".changeUrl($Observaciones)."',
							Especificaciones = '".changeUrl($Especificaciones)."',
							FlagUnidadCompra = 'N',
							CodUnidadCompra = '".$CodUnidad."',
							CantidadCompra = '".$CantidadRequerimiento."',
							Estado = 'PE',
							InvitadoPor = '".$_SESSION['CODPERSONA_ACTUAL']."',
							UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
							UltimaFecha = NOW()";
				execute($sql);
				//	actualizo
				$sql = "UPDATE lg_requerimientosdet
						SET
							CotizacionSecuencia = '".mysql_insert_id()."',
							CotizacionCantidad = '".$CantidadRequerimiento."',
							CotizacionProveedor = '".$_CodProveedor."',
							CotizacionFormaPago = '".$_CodFormaPago."',
							CotizacionRegistros = (CotizacionRegistros + 1)
						WHERE
							CodRequerimiento = '".$_CodRequerimiento."' AND
							Secuencia = '".$_Secuencia."'";
				execute($sql);	
			}
		}
		echo "|".$Numero;
		//-------------------
		mysql_query("COMMIT");
	}
	//	cotizar x items
	elseif ($accion == "cotizaciones_items_invitar_cotizar") {
		mysql_query("BEGIN");
		//-------------------
		$filtro_delete = "";
		$Numero = intval(getCodigo("lg_cotizacion", "Numero", 10));
		$proveedores = split(";char:tr;", $detalles_proveedores);
		foreach ($proveedores as $proveedor) {
			list($_CotizacionSecuencia, $_CodProveedor, $_NomProveedor, $_FlagAsignado, $_CodUnidad, $_Cantidad, $_CodUnidadCompra, $_CantidadCompra, $_PrecioUnitInicio, $_FlagExonerado, $_PrecioUnitInicioIva, $_DescuentoPorcentaje, $_DescuentoFijo, $_PrecioUnitIva, $_Total, $_PrecioUnitFinal, $_FlagMejorPrecio, $_CodFormaPago, $_FechaInvitacion, $_FechaEntrega, $_FechaRecepcion, $_FechaLimite, $_Condiciones, $_Observaciones, $_DiasEntrega, $_ValidezOferta, $_NumeroCotizacion, $_FechaDocumento) = split(";char:td;", $proveedor);
			##
			$NroCotizacionProv = getCodigo("lg_cotizacion", "NroCotizacionProv", 8);
			$NumeroInterno = getCodigo("lg_cotizacion", "NumeroInterno", 8, "Anio", date("Y"));
			##
			$CotizacionNumero = getCodigo("lg_cotizacion", "CotizacionNumero", 8);
			$CantidadRequerimiento = getVar("lg_requerimientosdet", "CantidadPedida", "CodRequerimiento", $CodRequerimiento, "Secuencia", $Secuencia);
			if ($_FlagAsignado == "S") $_FechaAsignacion = substr($Ahora, 0, 10); else $_FechaAsignacion = "";
			if ($_CodUnidad != $_CodUnidadCompra) $_FlagUnidadCompra = "S"; else $_FlagUnidadCompra = "N";
			$_PrecioUnit = $_PrecioUnitInicio - $_DescuentoFijo - ($_PrecioUnitInicio * $_DescuentoPorcentaje / 100);
			//	valido
			if ($_CotizacionSecuencia != "") {
				//	actualizo
				$sql = "UPDATE lg_cotizacion
						SET
							FlagAsignado = '".$_FlagAsignado."',
							Cantidad = '".$_Cantidad."',
							PrecioUnitInicio = '".$_PrecioUnitInicio."',
							FlagExonerado = '".$_FlagExonerado."',
							PrecioUnitInicioIva = '".$_PrecioUnitInicioIva."',
							DescuentoPorcentaje = '".$_DescuentoPorcentaje."',
							DescuentoFijo = '".$_DescuentoFijo."',
							PrecioUnit = '".$_PrecioUnit."',
							PrecioUnitIva = '".$_PrecioUnitIva."',
							Total = '".$_Total."',
							PrecioCantidad = '".($_CantidadCompra*$_PrecioUnit)."',
							CodFormaPago = '".$_CodFormaPago."',
							FechaInvitacion = '".$_FechaInvitacion."',
							FechaEntrega = '".$_FechaEntrega."',
							FechaRecepcion = '".$_FechaRecepcion."',
							FechaLimite = '".$_FechaLimite."',
							FechaDocumento = '".$_FechaDocumento."',
							Condiciones = '".changeUrl($_Condiciones)."',
							Observaciones = '".changeUrl($_Observaciones).changeUrl($Observaciones)."',
							DiasEntrega = '".$_DiasEntrega."',
							ValidezOferta = '".$_ValidezOferta."',
							NumeroCotizacion = '".changeUrl($_NumeroCotizacion)."',
							FlagMejorPrecio = '".$_FlagMejorPrecio."',
							FlagUnidadCompra = '".$_FlagUnidadCompra."',
							CodUnidadCompra = '".$_CodUnidadCompra."',
							CantidadCompra = '".$_CantidadCompra."',
							CotizadoPor = '".$_SESSION['CODPERSONA_ACTUAL']."',
							Estado = 'PE',
							UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
							UltimaFecha = NOW()
						WHERE CotizacionSecuencia = '".$_CotizacionSecuencia."'";
				execute($sql);
				//	actualizo
				$sql = "UPDATE lg_requerimientosdet
						SET
							CotizacionSecuencia = '".$_CotizacionSecuencia."',
							CotizacionCantidad = '".$CantidadRequerimiento."',
							CotizacionProveedor = '".$_CodProveedor."',
							CotizacionFormaPago = '".$_CodFormaPago."',
							CotizacionPrecioUnitInicio = '".$_PrecioUnitInicio."',
							CotizacionPrecioUnit = '".$_PrecioUnitInicio."',
							CotizacionPrecioUnitIva = '".$_PrecioUnitIva."',
							CotizacionFechaAsignacion = '".$_FechaAsignacion."'
						WHERE
							CodRequerimiento = '".$CodRequerimiento."' AND
							Secuencia = '".$Secuencia."'";
				execute($sql);
			} else {
				$_CotizacionSecuencia = mysql_insert_id();
				//	inserto
				$sql = "INSERT INTO lg_cotizacion
						SET
							Numero = '".$Numero."',
							NroCotizacionProv = '".$NroCotizacionProv."',
							NumeroInvitacion = '".$NroCotizacionProv."',
							Anio = NOW(),
							NumeroInterno = '".$NumeroInterno."',
							CotizacionNumero = '".$CotizacionNumero."',
							FechaApertura = NOW(),
							FechaDocumento = '".$_FechaDocumento."',
							FechaEntrega = '".$_FechaEntrega."',
							FechaRecepcion = '".$_FechaRecepcion."',
							CodOrganismo = '".$CodOrganismo."',
							CodRequerimiento = '".$CodRequerimiento."',
							Secuencia = '".$Secuencia."',
							CodProveedor = '".$_CodProveedor."',
							NomProveedor = '".changeUrl($_NomProveedor)."',
							FlagAsignado = '".$_FlagAsignado."',
							Cantidad = '".$_Cantidad."',
							PrecioUnitInicio = '".$_PrecioUnitInicio."',
							FlagExonerado = '".$_FlagExonerado."',
							PrecioUnitInicioIva = '".$_PrecioUnitInicioIva."',
							DescuentoPorcentaje = '".$_DescuentoPorcentaje."',
							DescuentoFijo = '".$_DescuentoFijo."',
							PrecioUnit = '".$_PrecioUnit."',
							PrecioUnitIva = '".$_PrecioUnitIva."',
							Total = '".$_Total."',
							PrecioCantidad = '".($_CantidadCompra*$_PrecioUnit)."',
							CodFormaPago = '".$_CodFormaPago."',
							FechaInvitacion = '".$_FechaInvitacion."',
							FechaLimite = '".$_FechaLimite."',
							Condiciones = '".changeUrl($_Condiciones)."',
							Observaciones = '".changeUrl($Observaciones)."',
							DiasEntrega = '".$_DiasEntrega."',
							ValidezOferta = '".$_ValidezOferta."',
							NumeroCotizacion = '".changeUrl($_NumeroCotizacion)."',
							FlagMejorPrecio = '".$_FlagMejorPrecio."',
							FlagUnidadCompra = '".$_FlagUnidadCompra."',
							CodUnidadCompra = '".$_CodUnidadCompra."',
							CantidadCompra = '".$_CantidadCompra."',
							InvitadoPor = '".$_SESSION['CODPERSONA_ACTUAL']."',
							CotizadoPor = '".$_SESSION['CODPERSONA_ACTUAL']."',
							Estado = 'PE',
							UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
							UltimaFecha = NOW()";
				execute($sql);
				//	actualizo
				$sql = "UPDATE lg_requerimientosdet
						SET
							CotizacionSecuencia = '".mysql_insert_id()."',
							CotizacionCantidad = '".$CantidadRequerimiento."',
							CotizacionProveedor = '".$_CodProveedor."',
							CotizacionFormaPago = '".$_CodFormaPago."',
							CotizacionPrecioUnitInicio = '".$_PrecioUnitInicio."',
							CotizacionPrecioUnit = '".$_PrecioUnitInicio."',
							CotizacionPrecioUnitIva = '".$_PrecioUnitIva."',
							CotizacionFechaAsignacion = '".$_FechaAsignacion."',
							CotizacionRegistros = CotizacionRegistros + 1
						WHERE
							CodRequerimiento = '".$CodRequerimiento."' AND
							Secuencia = '".$Secuencia."'";
				execute($sql);
			}
		}
		//	si borro alguna linea
		if ($borrar_proveedores != "") {
			$proveedoresx = split("[|]", $borrar_proveedores);
			foreach ($proveedoresx as $CodProveedor) {
				//	elimino
				$sql = "DELETE FROM lg_cotizacion
						WHERE
							CodRequerimiento = '".$CodRequerimiento."' AND
							Secuencia = '".$Secuencia."' AND
							CodProveedor = '".$CodProveedor."'";
				$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			}
		}
		//-------------------
		mysql_query("COMMIT");
	}
	//	cotizar x proveedors
	elseif ($accion == "cotizaciones_proveedores_invitar_cotizar") {
		mysql_query("BEGIN");
		//-------------------
		$filtro_delete = "";
		$Numero = intval(getCodigo("lg_cotizacion", "Numero", 10));
		$items = split(";char:tr;", $detalles_items);
		foreach ($items as $item) {
			list($_CotizacionSecuencia, $_CodUnidad, $_Cantidad, $_CodUnidadCompra, $_CantidadCompra, $_PrecioUnitInicio, $_FlagAsignado, $_FlagExonerado, $_PrecioUnitInicioIva, $_DescuentoPorcentaje, $_DescuentoFijo, $_PrecioUnit, $_PrecioUnitIva, $_Total, $_Observaciones) = split(";char:td;", $item);
			##
			$NroCotizacionProv = getCodigo("lg_cotizacion", "NroCotizacionProv", 8);
			$NumeroInterno = getCodigo("lg_cotizacion", "NumeroInterno", 8, "Anio", date("Y"));
			##
			$CotizacionNumero = getCodigo("lg_cotizacion", "CotizacionNumero", 8);
			$CantidadRequerimiento = getVar("lg_requerimientosdet", "CantidadPedida", "CodRequerimiento", $CodRequerimiento, "Secuencia", $Secuencia);
			if ($_FlagAsignado == "S") $_FechaAsignacion = substr($Ahora, 0, 10); else $_FechaAsignacion = "";
			if ($_CodUnidad != $_CodUnidadCompra) $_FlagUnidadCompra = "S"; else $_FlagUnidadCompra = "N";
			//	actualizo
			$sql = "UPDATE lg_cotizacion
					SET
						FlagAsignado = '".$_FlagAsignado."',
						Cantidad = '".$_Cantidad."',
						PrecioUnitInicio = '".$_PrecioUnitInicio."',
						FlagExonerado = '".$_FlagExonerado."',
						PrecioUnitInicioIva = '".$_PrecioUnitInicioIva."',
						DescuentoPorcentaje = '".$_DescuentoPorcentaje."',
						DescuentoFijo = '".$_DescuentoFijo."',
						PrecioUnit = '".$_PrecioUnit."',
						PrecioUnitIva = '".$_PrecioUnitIva."',
						Total = '".$_Total."',
						PrecioCantidad = '".($_CantidadCompra*$_PrecioUnit)."',
						CodFormaPago = '".$CodFormaPago."',
						FechaInvitacion = '".formatFechaAMD($FechaInvitacion)."',
						FechaLimite = '".formatFechaAMD($FechaLimite)."',
						FechaEntrega = '".formatFechaAMD($FechaEntrega)."',
						FechaRecepcion = '".formatFechaAMD($FechaRecepcion)."',
						FechaApertura = '".formatFechaAMD($FechaApertura)."',
						FechaDocumento = '".formatFechaAMD($FechaDocumento)."',
						Observaciones = '".changeUrl($_Observaciones)."',
						DiasEntrega = '".$DiasEntrega."',
						ValidezOferta = '".$ValidezOferta."',
						NumeroCotizacion = '".changeUrl($NumeroCotizacion)."',
						FlagUnidadCompra = '".$_FlagUnidadCompra."',
						CodUnidadCompra = '".$_CodUnidadCompra."',
						CantidadCompra = '".$_CantidadCompra."',
						CotizadoPor = '".$_SESSION['CODPERSONA_ACTUAL']."',
						Estado = 'PE',
						UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
						UltimaFecha = NOW()
					WHERE CotizacionSecuencia = '".$_CotizacionSecuencia."'";
			execute($sql);
			//	actualizo
			$sql = "UPDATE lg_requerimientosdet
					SET
						CotizacionSecuencia = '".$_CotizacionSecuencia."',
						CotizacionCantidad = '".$_CantidadCompra."',
						CotizacionFormaPago = '".$CodFormaPago."',
						CotizacionPrecioUnitInicio = '".$_PrecioUnitInicio."',
						CotizacionPrecioUnit = '".$_PrecioUnitInicio."',
						CotizacionPrecioUnitIva = '".$_PrecioUnitIva."',
						CotizacionFechaAsignacion = '".$_FechaAsignacion."'
					WHERE CotizacionSecuencia = '".$_CotizacionSecuencia."'";
			execute($sql);
		
		}
		//-------------------
		mysql_query("COMMIT");
	}
	//	eliminar
	elseif ($accion == "eliminar") {
		$sql = "DELETE FROM lg_cotizacion WHERE NroCotizacionProv = '".$registro."'";
		$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	}
}

//	cierre mensual
elseif ($modulo == "cierre_mensual") {
	mysql_query("BEGIN");
	//-------------------
	//	1.	verificar si existe un cierre mensual para el periodo inmediato anterior del organismo
	//	2.	borrar los datos del periodo actual
	$sql = "DELETE FROM lg_cierremensual
			WHERE
				Periodo >= '".$Periodo."' AND
				CodOrganismo = '".$CodOrganismo."'";
	execute($sql);
	$sql = "DELETE FROM lg_cierremensualsustento
			WHERE
				Periodo >= '".$Periodo."' AND
				CodOrganismo = '".$CodOrganismo."'";
	execute($sql);
	$sql = "DELETE FROM lg_cierremensualx";
	execute($sql);
	//	3.	inserta datos del periodo anterior en el periodo actual
	##	
	$sql = "INSERT INTO lg_cierremensual (
				Periodo,
				CodOrganismo,
				CodAlmacen,
				CodItem,
				StockNuevo,
				Precio,
				UltimoUsuario,
				UltimaFecha
			)
			SELECT
				'".$Periodo."' AS Periodo,
				CodOrganismo,
				CodAlmacen,
				CodItem,
				StockNuevo,
				Precio,
				'".$_SESSION['USUARIO_ACTUAL']."' AS UltimoUsuario,
				NOW() AS UltimaFecha
			FROM lg_cierremensual
			WHERE
				Periodo = '".$PeriodoAnterior."' AND
				CodOrganismo = '".$CodOrganismo."'";
	execute($sql);
	##	
	$sql = "INSERT INTO lg_cierremensualx (
				CodItem,
				Precio
			)
			SELECT
				cm.CodItem,
				MAX(cm.Precio) AS Precio
			FROM
				lg_cierremensual cm
				INNER JOIN lg_almacenmast a ON (a.CodAlmacen = cm.CodAlmacen)
			WHERE
				cm.Periodo = '".$PeriodoAnterior."' AND
				cm.CodOrganismo = '".$CodOrganismo."' AND
				a.TipoAlmacen = 'P'
			GROUP BY CodItem";
	execute($sql);
	//	4.	actualizo a N campos nullos
	##	---------->
	//	5.6.	consolido kardex
	$sql = "(SELECT
				t.CodOrganismo,
				k.CodAlmacen,
				k.CodItem,
				k.PeriodoContable AS Periodo,
				SUM(CASE WHEN tt.TipoMovimiento = 'I' THEN k.Cantidad ELSE 0 END) AS Ingresos,
				SUM(CASE WHEN tt.TipoMovimiento = 'T' AND k.Cantidad > 0 THEN k.Cantidad ELSE 0 END) AS IngresoTraslado,
				SUM(CASE WHEN k.CodTransaccion = 'ROC' OR 
							  k.CodTransaccion = 'ARO' OR 
							  k.CodTransaccion = 'DRO' 
						 THEN k.Cantidad 
						 ELSE 0 
					END) AS IngresoROC,
				SUM(CASE WHEN tt.TipoMovimiento = 'T' AND -k.Cantidad < 0 THEN k.Cantidad ELSE 0 END) AS SalidaTraslado,
				SUM(CASE WHEN tt.TipoMovimiento = 'E' THEN -k.Cantidad ELSE 0 END) AS Egresos
			 FROM
				lg_kardex k
				INNER JOIN lg_transaccion t ON (t.CodOrganismo = k.ReferenciaCodOrganismo AND
												t.CodDocumento = k.CodDocumento AND
												t.NroDocumento = k.NroDocumento)
				INNER JOIN lg_almacenmast a ON (k.CodAlmacen = a.CodAlmacen)
				INNER JOIN lg_tipotransaccion tt ON (tt.CodTransaccion = t.CodTransaccion)
			 WHERE
				k.PeriodoContable = '".$Periodo."' AND
				a.CodOrganismo = '".$CodOrganismo."'
			 GROUP BY CodOrganismo, CodAlmacen, CodItem
			)
			UNION
			(SELECT
				CodOrganismo,
				CodAlmacen,
				CodItem,
				Periodo,
				(0.00) AS Ingresos,
				IngresoTraslado,
				IngresoROC,
				SalidaTraslado,
				SalidaREQ AS Egresos
			 FROM lg_cierremensual
			 WHERE
				Periodo = '".$Periodo."' AND
				CodOrganismo = '".$CodOrganismo."'
			)
			ORDER BY CodAlmacen, CodItem";
	$field_consolidado = getRecords($sql);
	foreach($field_consolidado as $f) {
		//	7.	inserto/actualizo item
		$sql = "INSERT INTO lg_cierremensual
				SET
					Periodo = '".$f['Periodo']."',
					CodOrganismo = '".$f['CodOrganismo']."',
					CodAlmacen = '".$f['CodAlmacen']."',
					CodItem = '".$f['CodItem']."',
					IngresoROC = '".$f['IngresoROC']."',
					IngresoOtros = '".$f['Ingresos']."',
					IngresoTraslado = '".$f['IngresoTraslado']."',
					SalidaREQ = '".$f['Egresos']."',
					SalidaOtros = '".$f['Egresos']."',
					SalidaTraslado = '".$f['SalidaTraslado']."',
					StockAnterior = '0.00',
					StockNuevo = '0.00',
					Precio = '0.00',
					UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
					UltimaFecha = NOW()
				ON DUPLICATE KEY UPDATE
					IngresoROC = '".$f['IngresoROC']."',
					IngresoOtros = '".$f['IngresoROC']."',
					IngresoTraslado = '".$f['IngresoTraslado']."',
					SalidaREQ = '".$f['Egresos']."',
					SalidaOtros = '".$f['Egresos']."',
					SalidaTraslado = '".$f['SalidaTraslado']."',
					StockAnterior = '0.00',
					StockNuevo = '0.00',
					Precio = '0.00',
					UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
					UltimaFecha = NOW()";
		execute($sql);
	}
	//	8.	actualizo stock nuevo
	$sql = "UPDATE lg_cierremensual
			SET StockNuevo = (StockAnterior + IngresoOtros + IngresoTraslado - SalidaOtros - SalidaTraslado)
			WHERE
				CodOrganismo = '".$CodOrganismo."' AND
				Periodo = '".$Periodo."'";
	execute($sql);
	//	9.	precio promedio
	$sql = "SELECT
				td.CodOrganismo,
				td.CodDocumento,
				td.NroDocumento,
				td.Secuencia,
				td.CodItem,
				td.Descripcion,
				td.CodUnidad,
				td.ReferenciaCodDocumento,
				t.NroInterno,
				t.CodTransaccion,
				t.FechaDocumento,
				t.CodAlmacen,
				t.Periodo,
				t.DocumentoReferencia,
				tt.Descripcion AS NomTransaccion,
				oc.Anio,
				oc.NroOrden,
				oc.CodProveedor,
				oc.NomProveedor,
				oc.NroInterno AS NroInternoOrden,
				ocd.CantidadRecibida,
				ocd.PrecioUnit,
				ocd.Total
			FROM
				lg_transacciondetalle td
				INNER JOIN lg_transaccion t ON (t.CodOrganismo = td.CodOrganismo AND
												t.CodDocumento = td.CodDocumento AND
												t.NroDocumento = td.NroDocumento)
				INNER JOIN lg_ordencompradetalle ocd ON (ocd.Anio = t.Anio AND
														 ocd.CodOrganismo = t.CodOrganismo AND
														 ocd.NroOrden = td.ReferenciaNroDocumento AND
														 ocd.Secuencia = td.ReferenciaSecuencia)
				INNER JOIN lg_ordencompra oc ON (oc.Anio = ocd.Anio AND
												 oc.CodOrganismo = ocd.CodOrganismo AND
												 oc.NroOrden = ocd.NroOrden)
				INNER JOIN lg_tipotransaccion tt ON (tt.CodTransaccion = t.CodTransaccion)
			WHERE
				t.CodOrganismo = '".$CodOrganismo."' AND
				t.Periodo = '".$Periodo."' AND
				t.Estado = 'CO' AND
				(t.CodTransaccion = 'ROC' OR 
				 t.CodTransaccion = 'ARO' OR 
				 t.CodTransaccion = 'DRO' OR 
				 t.CodTransaccion = 'MIT' OR 
				 t.CodTransaccion = 'TRT' OR 
				 t.FlagManual = 'S')
			ORDER BY CodItem, FechaDocumento";
	$field_promedio = getRecords($sql);
	foreach($field_promedio as $f) {
		//	15.	inserto sustento
		##	anterior
		if ($Grupo != $f['CodItem']) {
			$Grupo = $f['CodItem'];
			$CantidadAcumulada = 0;
			$MontoAcumulado = 0;
			$i = 0;
			$sql = "INSERT INTO lg_cierremensualsustento
					SET
						Periodo = '".$f['Periodo']."',
						CodOrganismo = '".$f['CodOrganismo']."',
						CodAlmacen = '".$f['CodAlmacen']."',
						CodItem = '".$f['CodItem']."',
						Secuencia = '0',
						DocumentoReferencia = 'Saldo Anterior',
						UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		$CantidadAcumulada += $f['CantidadRecibida'];
		$MontoAcumulado += ($f['CantidadRecibida'] * $f['PrecioUnit']);
		$PrecioPromedio = round(($MontoAcumulado / $CantidadAcumulada), 2);
		##	actual
		$sql = "INSERT INTO lg_cierremensualsustento
				SET
					Periodo = '".$f['Periodo']."',
					CodOrganismo = '".$f['CodOrganismo']."',
					CodAlmacen = '".$f['CodAlmacen']."',
					CodItem = '".$f['CodItem']."',
					Secuencia = '".++$i."',
					Cantidad = '".$f['CantidadRecibida']."',
					Precio = '".$f['PrecioUnit']."',
					CantidadAcumulada = ".floatval($CantidadAcumulada).",
					Monto = ".floatval($MontoAcumulado).",
					DocumentoReferencia = '".$f['DocumentoReferencia']."',
					FechaRecepcion = '".$f['FechaDocumento']."',
					TransaccionCodDocumento = '".$f['CodDocumento']."',
					TransaccionNroDocumento = '".$f['NroDocumento']."',
					TransaccionSecuencia = '".$f['Secuencia']."',
					UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
					UltimaFecha = NOW()";
		execute($sql);
		//	-->
		$sql = "INSERT INTO lg_cierremensualx 
				SET
					CodItem = '".$f['CodItem']."',
					Precio = ".floatval($PrecioPromedio)."
				ON DUPLICATE KEY UPDATE
					Precio = ".floatval($PrecioPromedio);
		execute($sql);
	}
	//	actualizo cierre mensual
	$sql = "UPDATE lg_cierremensual cm, lg_cierremensualx x
			SET cm.Precio = x.Precio
			WHERE
				cm.Periodo = '".$Periodo."' AND
				cm.CodOrganismo = '".$CodOrganismo."' AND
				cm.CodItem = x.CodItem";
	execute($sql);
	//	actualizo transacciones
	$sql = "UPDATE
				lg_transacciondetalle td,
				lg_transaccion t,
				lg_cierremensualx x
			SET
				td.PrecioUnit = x.Precio,
				td.Total = (td.CantidadRecibida * x.Precio)
			WHERE
				t.Periodo = '".$Periodo."' AND
				t.CodOrganismo = '".$CodOrganismo."' AND
				td.CodOrganismo = t.CodOrganismo AND
				td.CodDocumento = t.CodDocumento AND
				td.NroDocumento = t.NroDocumento AND
				td.CodItem = x.CodItem";
	execute($sql);
	//	actualizo kardex
	$sql = "UPDATE
				lg_kardex k,
				lg_transacciondetalle td,
				lg_transaccion t
			SET
				k.PrecioUnitario = td.PrecioUnit,
				k.MontoTotal = (td.CantidadRecibida * td.PrecioUnit)
			WHERE
				t.Periodo = '".$Periodo."' AND
				t.CodOrganismo = '".$CodOrganismo."' AND
				(td.CodOrganismo = t.CodOrganismo AND 
				 td.CodDocumento = t.CodDocumento AND 
				 td.NroDocumento = t.NroDocumento) AND
				(k.CodItem = td.CodItem AND 
				 k.CodAlmacen = t.CodAlmacen AND 
				 k.CodDocumento = t.CodDocumento AND 
				 k.NroDocumento = t.NroDocumento)";
	execute($sql);
	//	actualizo el maestro de items
	$sql = "UPDATE
				lg_itemmast i,
				lg_cierremensualx x
			SET i.PrecioUnitario = x.Precio
			WHERE x.CodItem = i.CodItem";
	execute($sql);
	//	tab: datos para precio promedio
	$tab1 = "";
	foreach($field_promedio as $f) {
		if ($Grupo != $f['CodItem']) {
			$Grupo = $f['CodItem'];
			$sql = "SELECT COUNT(*)
					FROM
						lg_transacciondetalle td
						INNER JOIN lg_transaccion t ON (t.CodOrganismo = td.CodOrganismo AND
														t.CodDocumento = td.CodDocumento AND
														t.NroDocumento = td.NroDocumento)
						INNER JOIN lg_ordencompradetalle ocd ON (ocd.Anio = t.Anio AND
																 ocd.CodOrganismo = t.CodOrganismo AND
																 ocd.NroOrden = td.ReferenciaNroDocumento AND
																 ocd.Secuencia = td.ReferenciaSecuencia)
						INNER JOIN lg_ordencompra oc ON (oc.Anio = ocd.Anio AND
														 oc.CodOrganismo = ocd.CodOrganismo AND
														 oc.NroOrden = ocd.NroOrden)
						INNER JOIN lg_tipotransaccion tt ON (tt.CodTransaccion = t.CodTransaccion)
					WHERE
						td.CodItem = '".$f['CodItem']."' AND
						t.CodOrganismo = '".$f['CodOrganismo']."' AND
						t.Periodo = '".$f['Periodo']."' AND
						t.Estado = 'CO' AND
						(t.CodTransaccion = 'ROC' OR 
						 t.CodTransaccion = 'ARO' OR 
						 t.CodTransaccion = 'DRO' OR 
						 t.CodTransaccion = 'MIT' OR 
						 t.CodTransaccion = 'TRT')";
			$Count = getVar3($sql);
			$tdCodItem = "<td align='center' rowspan='$Count'>$f[CodItem]</td>";
		} else $tdCodItem = "";
		$tab1 .= "
		<tr class='trListaBody'>
        	".$tdCodItem."
        	<td align='center'>".$f['CodAlmacen']."</td>
			<td align='center'>".formatFechaDMA($f['FechaDocumento'])."</td>
        	<td align='center'>".$f['CodDocumento']." - ".$f['NroInterno']."</td>
        	<td align='center'>".$f['Secuencia']."</td>
        	<td>".$f['CodTransaccion']." - ".htmlentities($f['NomTransaccion'])."</td>
        	<td align='center'>".$f['ReferenciaCodDocumento']." - ".$f['NroInternoOrden']."</td>
        	<td align='right'>".number_format($f['CantidadRecibida'], 2, ',', '.')."</td>
        	<td align='center'>".$f['CodUnidad']."</td>
        	<td align='right'>".number_format($f['PrecioUnit'], 2, ',', '.')."</td>
        	<td align='right'>".number_format($f['Total'], 2, ',', '.')."</td>
        </tr>";
	}
	//	tab: errores detectados
	##	stock sin precio
	$tab2_1 = "";
	$Grupo = "";
	$sql = "SELECT
				cm.CodAlmacen,
				cm.CodItem,
				cm.StockNuevo,
				i.Descripcion
			FROM
				lg_cierremensual cm
				INNER JOIN lg_itemmast i ON (i.CodItem = cm.CodItem)
			WHERE
				cm.Periodo = '".$Periodo."' AND
				cm.CodOrganismo = '".$CodOrganismo."' AND
				cm.StockNuevo > 0 AND
				cm.Precio = 0
			ORDER BY CodAlmacen, CodItem";
	$field_stock = getRecords($sql);
	foreach($field_stock as $f) {
		if ($Grupo != $f['CodAlmacen']) {
			$Grupo = $f['CodAlmacen'];
			$tab2_1 .= "
			<tr class='trListaBody2'>
				<td colspan='2'>".htmlentities($f['CodAlmacen'])."</td>
			</tr>";
		}
		$tab2_1 .= "
		<tr class='trListaBody'>
        	<td align='center'>".$f['CodItem']."</td>
        	<td>".htmlentities($f['Descripcion'])."</td>
        	<td align='right'>".number_format($f['StockNuevo'], 2, ',', '.')."</td>
        </tr>";
	}
	##	stock negativo
	$tab2_2 = "";
	$Grupo = "";
	$sql = "SELECT
				cm.CodAlmacen,
				cm.CodItem,
				cm.StockNuevo,
				i.Descripcion
			FROM
				lg_cierremensual cm
				INNER JOIN lg_itemmast i ON (i.CodItem = cm.CodItem)
			WHERE
				cm.Periodo = '".$Periodo."' AND
				cm.CodOrganismo = '".$CodOrganismo."' AND
				cm.StockNuevo < 0
			ORDER BY CodAlmacen, CodItem";
	$field_stock = getRecords($sql);
	foreach($field_stock as $f) {
		if ($Grupo != $f['CodAlmacen']) {
			$Grupo = $f['CodAlmacen'];
			$tab2_2 .= "
			<tr class='trListaBody2'>
				<td colspan='2'>".htmlentities($f['CodAlmacen'])."</td>
			</tr>";
		}
		$tab2_2 .= "
		<tr class='trListaBody'>
        	<td align='center'>".$f['CodItem']."</td>
        	<td>".htmlentities($f['Descripcion'])."</td>
        	<td align='right'>".number_format($f['StockNuevo'], 2, ',', '.')."</td>
        </tr>";
	}
	##	sin factura
	$tab2_3 = "";
	foreach($field_promedio as $f) {
		//	12.	verifico facturacion
		$sql = "SELECT COUNT(*)
				FROM
					ap_documentos d
					INNER JOIN ap_obligaciones o ON (o.CodProveedor = d.CodProveedor AND
													 o.CodTipoDocumento = d.ObligacionTipoDocumento AND
													 o.NroDocumento = d.ObligacionNroDocumento)
				WHERE
					d.Anio = '".$f['Anio']."' AND
					d.CodProveedor = '".$f['CodProveedor']."' AND
					d.DocumentoClasificacion = '".$f['CodTransaccion']."' AND
					d.ReferenciaTipoDocumento = '".$f['ReferenciaCodDocumento']."' AND
					d.ReferenciaNroDocumento = '".$f['NroOrden']."'";
		$factura = getVar3($sql);
		if ($factura == 0) {
			$tab2_3 .= "
			<tr class='trListaBody'>
				<td align='center'>".$f['NroInterno']."</td>
				<td align='center'>".$f['CodProveedor']."</td>
				<td>".htmlentities($f['NomProveedor'])."</td>
				<td align='center'>".$f['CodItem']."</td>
				<td align='right'>".number_format($f['CantidadRecibida'], 2, ',', '.')."</td>
			</tr>";
		}
	}
	echo "|".$tab1."|".$tab2_1."|".$tab2_2."|".$tab2_3;
	//-------------------
	mysql_query("COMMIT");
}
?>