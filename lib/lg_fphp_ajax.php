<?php
include("fphp.php");
include("lg_fphp.php");
///////////////////////////////////////////////////////////////////////////////
//	PARA AJAX
///////////////////////////////////////////////////////////////////////////////
//	clasificaciones
if ($modulo == "clasificaciones") {
	//	nuevo
	if ($accion == "nuevo") {
		if ($codigo == "") $codigo = getCodigo("lg_clasificacion", "Clasificacion", 3);
		
		//	inserto
		$sql = "INSERT INTO lg_clasificacion (
							Clasificacion,
							Descripcion,
							ReqOrdenCompra,
							CodAlmacen,
							TipoRequerimiento,
							FlagRecepcionAlmacen,
							FlagRevision,
							ReqAlmacenCompra,
							FlagTransaccion,
							FlagCajaChica,
							Estado,
							UltimoUsuario,
							UltimaFecha
				) VALUES (
							'".$codigo."',
							'".($descripcion)."',
							'".$disponible."',
							'".$codalmacen."',
							'".$requerimiento."',
							'".$flagrecepcion."',
							'".$flagrevision."',
							'".$almacen_compra."',
							'".$flagtransaccion."',
							'".$flagcajachica."',
							'".$estado."',
							'".$_SESSION['USUARIO_ACTUAL']."',
							NOW()
				)";
		$query_insert = mysql_query($sql) or die($sql.mysql_error());
	}
	
	//	modificar
	elseif ($accion == "modificar") {
		//	actualizo
		$sql = "UPDATE lg_clasificacion
				SET
					Descripcion = '".($descripcion)."',
					ReqOrdenCompra = '".$disponible."',
					CodAlmacen = '".$codalmacen."',
					TipoRequerimiento = '".$requerimiento."',
					FlagRecepcionAlmacen = '".$flagrecepcion."',
					FlagRevision = '".$flagrevision."',
					ReqAlmacenCompra = '".$almacen_compra."',
					FlagTransaccion = '".$flagtransaccion."',
					FlagCajaChica = '".$flagcajachica."',
					Estado = '".$estado."',
					UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
					UltimaFecha = NOW()
				WHERE Clasificacion = '".$codigo."'";
		$query_update = mysql_query($sql) or die($sql.mysql_error());
	}
	
	//	borrar
	elseif ($accion == "eliminar") {
		//	elimino
		$sql = "DELETE FROM lg_clasificacion WHERE Clasificacion = '".$codigo."'";
		$query_delete = mysql_query($sql) or die($sql.mysql_error());
	}
}

//	cotizaciones
elseif ($modulo == "cotizaciones") {
	//	invitar
	if ($accion == "cotizaciones_invitar_proveedor") {
		//	busco errores
		//	proveedores
		$proveedor = split(";", $proveedores);
		foreach ($proveedor as $registro_proveedor) {
			list($codproveedor, $codformapago) = split("[|]", $registro_proveedor);
			
			//	requerimientos
			$requerimiento = split(";", $requerimientos);
			foreach ($requerimiento as $registro_requerimiento) {
				list($idrequerimiento, $cantidad, $flagexonerado) = split("[|]", $registro_requerimiento);
				list($codorganismo, $codrequerimiento, $secuencia) = split("[.]", $idrequerimiento);
				//	verifico si el proveedor ya tiene una invitacion de uno de los requerimientos
				$sql = "SELECT NomProveedor
						FROM lg_cotizacion
						WHERE
							CodRequerimiento = '".$codrequerimiento."' AND
							Secuencia = '".$secuencia."' AND
							CodProveedor = '".$codproveedor."'";
				$query = mysql_query($sql) or die($sql.mysql_error());
				if (mysql_num_rows($query) != 0) {
					$field = mysql_fetch_array($query);
					die("¡ERROR: $field[NomProveedor] ya tiene una invitacion para uno de los requerimientos!");
				}
			}
		}
		
		$numero = intval(getCodigo("lg_cotizacion", "Numero", 10));
		//	inserto la invitacion
		//	proveedores
		$proveedor = split(";", $proveedores);
		foreach ($proveedor as $registro_proveedor) {
			list($codproveedor, $codformapago) = split("[|]", $registro_proveedor);
			$nomproveedor = getValorCampo("mastpersonas", "CodPersona", "NomCompleto", $codproveedor);
			
			//	numero de cotizacion proveedor
			$cotizacion_numero_proveedor = getCodigo("lg_cotizacion", "NroCotizacionProv", 8);
			$NumeroInterno = getCodigo("lg_cotizacion", "NumeroInterno", 8, "Anio", date("Y"));
			
			//	requerimientos
			$requerimiento = split(";", $requerimientos);
			foreach ($requerimiento as $registro_requerimiento) {
				list($idrequerimiento, $cantidad, $flagexonerado) = split("[|]", $registro_requerimiento);
				list($codorganismo, $codrequerimiento, $secuencia) = split("[.]", $idrequerimiento);
				
				//	numero de invitacines y el numero de cotizacion
				$nroinvitaciones = count($proveedor);
				$cotizacion_numero = getCodigo("lg_cotizacion", "CotizacionNumero", 8);
				
				//	inserto cotizacion
				$sql = "INSERT INTO lg_cotizacion (
									CodOrganismo,
									CodRequerimiento,
									Secuencia,
									CotizacionNumero,
									Numero,
									CodProveedor,
									NomProveedor,
									CodFormaPago,
									FechaInvitacion,
									FechaDocumento,
									FechaLimite,
									FechaEntrega,
									Condiciones,
									Observaciones,
									Cantidad,
									Estado,
									NroCotizacionProv,
									FlagAsignado,
									FlagExonerado,
									NumeroInvitacion,
									NumeroInterno,
									Anio,
									UltimoUsuario,
									UltimaFecha
						) VALUES (
									'".$codorganismo."',
									'".$codrequerimiento."',
									'".$secuencia."',
									'".$cotizacion_numero."',
									'".$numero."',
									'".$codproveedor."',
									'".($nomproveedor)."',
									'".$codformapago."',
									NOW(),
									NOW(),
									'".formatFechaAMD($flimite)."',
									'".formatFechaAMD($flimite)."',
									'".($condiciones)."',
									'".($observaciones)."',
									'".$cantidad."',
									'PE',
									'".$cotizacion_numero_proveedor."',
									'N',
									'".$flagexonerado."',
									'".$cotizacion_numero_proveedor."',
									'".$NumeroInterno."',
									'".date("Y")."',
									'".$_SESSION['USUARIO_ACTUAL']."',
									NOW()
						)";
				$query_insert = mysql_query($sql) or die ($sql.mysql_error());
				
				//	actualizo numero de invitaciones
				$sql = "UPDATE lg_requerimientosdet 
						SET
							CotizacionSecuencia = '".mysql_insert_id()."',
							CotizacionCantidad = '".$cantidad."',
							CotizacionProveedor = '".$codproveedor."',
							CotizacionFormaPago = '".$codformapago."',
							CotizacionRegistros = (CotizacionRegistros + 1)							
						WHERE
							CodRequerimiento = '".$codrequerimiento."' AND 
							Secuencia = '".$secuencia."'";
				$query_update = mysql_query($sql) or die ($sql.mysql_error());
			}
		}
		echo "|$numero";
	}
	
	//	invitar/cotizar
	elseif ($accion == "cotizaciones_invitar_cotizar") {
		$numero = intval(getCodigo("lg_cotizacion", "Numero", 10));
		//	inserto las cotizaciones
		//	proveedores
		$proveedor = split(";", $detalle);
		foreach ($proveedor as $registro_proveedor) {
			list($codproveedor, $flagasig, $cant, $pu, $flagexon, $pu_igv, $descp, $descf, $pu_total, $total, $comparar, $flagmejor, $codformapago, $finvitacion, $flimite, $condiciones, $observaciones, $dias, $validez, $nrocotizacion) = split("[|]", $registro_proveedor);
			$nomproveedor = getValorCampo("mastpersonas", "CodPersona", "NomCompleto", $codproveedor);
			
			if ($flagasig == "S" && $flagmejor == "N") {
				if ($observaciones != "") $observaciones .= "\n$obs";
				else $observaciones = $obs;
			}
			
			//	fecha de entrega
			$fentrega = getFechaFin(date("d-m-Y"), $dias);
			
			//	precio unitario con descuento
			if ($descp != 0) $pu_desc = $pu - ($pu * $descp / 100); else $pu_desc = $pu - $descf;
			
			//	precio x cantidad
			$precio_cantidad = $pu_desc * $cant;
			
			//	verifico si el proveedor ya tiene una invitacion de uno de los requerimientos
			$sql = "SELECT *
					FROM lg_cotizacion
					WHERE
						CodRequerimiento = '".$codrequerimiento."' AND
						Secuencia = '".$secuencia."' AND
						CodProveedor = '".$codproveedor."'";
			$query = mysql_query($sql) or die($sql.mysql_error());
			if (mysql_num_rows($query) != 0) {
				$field = mysql_fetch_array($query);
				
				//	actualizo cotizacion
				$sql = "UPDATE lg_cotizacion
						SET
							FechaInvitacion = '".formatFechaAMD($finvitacion)."',
							FechaDocumento = '".formatFechaAMD($finvitacion)."',
							FechaLimite = '".formatFechaAMD($flimite)."',
							FechaEntrega = '".formatFechaAMD($fentrega)."',
							CodFormaPago = '".$codformapago."',
							PrecioUnitInicio = '".$pu."',
							PrecioUnitInicioIva = '".$pu_igv."',
							PrecioUnit = '".$pu_desc."',
							PrecioUnitIva = '".$pu_total."',
							PrecioCantidad = '".$precio_cantidad."',
							Total = '".$total."',
							ValidezOferta = '".$validez."',
							DiasEntrega = '".$dias."',
							Cantidad = '".$cant."',
							DescuentoFijo = '".$descf."',
							DescuentoPorcentaje = '".$descp."',
							Condiciones = '".($condiciones)."',
							Observaciones = '".($observaciones)."',
							NumeroCotizacion = '".$nrocotizacion."',
							FlagAsignado = '".$flagasig."',
							FlagExonerado = '".$flagexon."',
							UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
							UltimaFecha = NOW()							
						WHERE CotizacionSecuencia = '".$field['CotizacionSecuencia']."'";
				$query_update = mysql_query($sql) or die ($sql.mysql_error());
				
				if ($flagasig == "S") {
					//	actualizo datos de la cotizacion asignada
					$sql = "UPDATE lg_requerimientosdet
							SET
								CotizacionCantidad = '".$cant."',
								CotizacionPrecioUnitInicio = '".$pu."',
								CotizacionPrecioUnit = '".$precio_unitario."',
								CotizacionPrecioUnitIva = '".$precio_unitario_iva."',
								CotizacionFechaAsignacion = '".date("Y-m-d")."'
							WHERE
								CodRequerimiento = '".$codrequerimiento."' AND
								Secuencia = '".$secuencia."'";
					$query_update = mysql_query($sql) or die ($sql.mysql_error());
				}
			} else {
				//	numero de cotizacion proveedor
				$NumeroInterno = getCodigo("lg_cotizacion", "NumeroInterno", 8, "Anio", date("Y"));
				$cotizacion_numero_proveedor = getCodigo("lg_cotizacion", "NroCotizacionProv", 8);
				
				//	numero de invitacines y el numero de cotizacion
				$nroinvitaciones = count($proveedor);
				$cotizacion_numero = getCodigo("lg_cotizacion", "CotizacionNumero", 8);
				
				//	inserto cotizacion
				$sql = "INSERT INTO lg_cotizacion (
									CodOrganismo,
									CodRequerimiento,
									Secuencia,
									CotizacionNumero,
									Numero,
									FechaInvitacion,
									FechaDocumento,
									FechaLimite,
									FechaEntrega,
									CodProveedor,
									NomProveedor,
									CodFormaPago,
									PrecioUnitInicio,
									PrecioUnitInicioIva,
									PrecioUnit,
									PrecioUnitIva,
									PrecioCantidad,
									Total,
									ValidezOferta,
									DiasEntrega,
									Cantidad,
									DescuentoFijo,
									DescuentoPorcentaje,
									Condiciones,
									Observaciones,
									Estado,
									NroCotizacionProv,
									NumeroInvitacion,
									NumeroCotizacion,
									Anio,
									NumeroInterno,
									FlagAsignado,
									FlagExonerado,
									UltimoUsuario,
									UltimaFecha
						) VALUES (
									'".$codorganismo."',
									'".$codrequerimiento."',
									'".$secuencia."',
									'".$cotizacion_numero."',
									'".$numero."',
									'".formatFechaAMD($finvitacion)."',
									'".formatFechaAMD($finvitacion)."',
									'".formatFechaAMD($flimite)."',
									'".formatFechaAMD($fentrega)."',
									'".$codproveedor."',
									'".($nomproveedor)."',
									'".$codformapago."',
									'".$pu."',
									'".$pu_igv."',
									'".$pu_desc."',
									'".$pu_total."',
									'".$precio_cantidad."',
									'".$total."',
									'".$validez."',
									'".$dias."',
									'".$cant."',
									'".$descf."',
									'".$descp."',
									'".($condiciones)."',
									'".($observaciones)."',
									'PE',
									'".$cotizacion_numero_proveedor."',
									'".$cotizacion_numero_proveedor."',
									'".$nrocotizacion."',
									'".date("Y")."',
									'".$NumeroInterno."',
									'".$flagasig."',
									'".$flagexon."',
									'".$_SESSION['USUARIO_ACTUAL']."',
									NOW()
						)";
				$query_insert = mysql_query($sql) or die ($sql.mysql_error());
				
				if ($flagasig == "S") {
					//	actualizo numero de invitaciones
					$sql = "UPDATE lg_requerimientosdet
							SET
								CotizacionCantidad = '".$cant."',
								CotizacionPrecioUnitInicio = '".$pu."',
								CotizacionPrecioUnit = '".$precio_unitario."',
								CotizacionPrecioUnitIva = '".$precio_unitario_iva."',
								CotizacionFechaAsignacion = '".formatFechaAMD($fasignacion)."',
								CotizacionProveedor = '".$codproveedor."',
								CotizacionFormaPago = '".$codformapago."',
								CotizacionRegistros = (CotizacionRegistros + 1)
							WHERE
								CodRequerimiento = '".$codrequerimiento."' AND
								Secuencia = '".$secuencia."'";
					$query_update = mysql_query($sql) or die ($sql.mysql_error());
				} else {
					//	actualizo numero de invitaciones
					$sql = "UPDATE lg_requerimientosdet
							SET CotizacionRegistros = (CotizacionRegistros + 1)
							WHERE
								CodRequerimiento = '".$codrequerimiento."' AND
								Secuencia = '".$secuencia."'";
					$query_update = mysql_query($sql) or die ($sql.mysql_error());
				}
			}
		}
		echo "|$numero";
	}
	
	//	invitar/cotizar
	elseif ($accion == "cotizaciones_invitaciones_cotizar") {
		//	inserto las cotizaciones
		//	proveedores
		$proveedor = split(";", $detalle);
		foreach ($proveedor as $registro_proveedor) {
			list($cotizacion_secuencia, $cant, $pu, $flagasig, $flagexon, $pu_igv, $descp, $descf, $pu_desc, $pu_total, $total, $observaciones) = split("[|]", $registro_proveedor);
			
			//	fecha de entrega
			$fentrega = getFechaFin(date("d-m-Y"), $dias);
			
			//	precio x cantidad
			$precio_cantidad = $pu_desc * $cant;
			
			//	actualizo cotizacion
			$sql = "UPDATE lg_cotizacion
					SET
						FechaApertura = '".formatFechaAMD($fapertura)."',
						FechaRecepcion = '".formatFechaAMD($frecepcion)."',
						FechaDocumento = '".formatFechaAMD($fcotizacion)."',
						FechaEntrega = '".formatFechaAMD($fentrega)."',
						CodFormaPago = '".$codformapago."',
						PrecioUnitInicio = '".$pu."',
						PrecioUnitInicioIva = '".$pu_igv."',
						PrecioUnit = '".$pu_desc."',
						PrecioUnitIva = '".$pu_total."',
						PrecioCantidad = '".$precio_cantidad."',
						Total = '".$total."',
						ValidezOferta = '".$validez."',
						DiasEntrega = '".$dias."',
						Cantidad = '".$cant."',
						DescuentoFijo = '".$descf."',
						DescuentoPorcentaje = '".$descp."',
						Observaciones = '".($observaciones)."',
						FlagAsignado = '".$flagasig."',
						FlagExonerado = '".$flagexon."',
						NumeroCotizacion = '".$nrocotizacionprov."',
						UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
						UltimaFecha = NOW()							
					WHERE CotizacionSecuencia = '".$cotizacion_secuencia."'";
			$query_update = mysql_query($sql) or die ($sql.mysql_error());
			
			if ($flagasig == "S") {
				$sql = "SELECT CodRequerimiento, Secuencia
						FROM lg_cotizacion
						WHERE CotizacionSecuencia = '".$cotizacion_secuencia."'";
				$query_cot = mysql_query($sql) or die ($sql.mysql_error());
				if (mysql_num_rows($query_cot) != 0) $field_cot = mysql_fetch_array($query_cot);
				
				//	actualizo requerimiento detalle
				$sql = "UPDATE lg_requerimientosdet
						SET
							CotizacionCantidad = '".$cant."',
							CotizacionPrecioUnitInicio = '".$pu."',
							CotizacionPrecioUnit = '".$pu_desc."',
							CotizacionPrecioUnitIva = '".$pu_total."',
							CotizacionFechaAsignacion = '".date("Y-m-d")."',
							CotizacionProveedor = '".$codproveedor."',
							CotizacionFormaPago = '".$codformapago."'
						WHERE
							CodRequerimiento = '".$field_cot['CodRequerimiento']."' AND
							Secuencia = '".$field_cot['Secuencia']."'";
				$query_update = mysql_query($sql) or die ($sql.mysql_error());
			}
		}
	}
	
	//	eliminar invitacion
	elseif ($accion = "eliminar") {
		$sql = "DELETE FROM lg_cotizacion WHERE NroCotizacionProv = '".$registro."'";
		$query_delete = mysql_query($sql) or die($sql.mysql_error());
	}
}

//	cierre mensual
elseif ($modulo == "cierre_mensual") {
	/*
	//	obtengo el valor del periodo anterior
	list(intval($a), intval($m)) = split("[-]", $periodo);
	$m--; if ($m == 0) { $a--; $m = 12; }
	if ($m < 10) $mes = "0$m"; else $mes = "$m";
	$periodo_anterior = "$a-$mes";
	
	//	verifico si existe cierre mensual para el periodo inmediato anterior del organismo
	$sql = "SELECT
				cm.CodOrganismo,
				cm.Codalmacen,
				cm.CodItem,
				cm.StockNuevo,
				i.Descripcion
			FROM
				lg_cierremensual cm
				INNER JOIN lg_itemmast i ON (cm.CodItem = i.CodItem)
			WHERE
				cm.Periodo = '".$periodo_anterior."' AND
				cm.CodOrganismo = '".$organismo."' AND
				cm.StockNuevo > 0 AND
				cm.Precio = 0";
	$query_anterior = mysql_query($sql) or die($sql.mysql_error());
	if (mysql_num_rows($query_anterior) != 0) $field_anterior = mysql_fetch_array($query_anterior);
	
	//	consulto si el periodo se encuentra abierto para el organismo
	*/
}
?>