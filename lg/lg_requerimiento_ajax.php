<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion.sql", "w+");
///////////////////////////////////////////////////////////////////////////////
//	REQUERIMIENTOS (NUEVO, MODIFICAR, REVISAR, CONFORMAR, APROBAR, RECHAZAR, ANULAR, CERRAR, CERRAR DETALLE)
///////////////////////////////////////////////////////////////////////////////
//	obligacion

//	requerimiento
if ($modulo == "requerimiento") {
	$Comentarios = changeUrl($Comentarios);
	$RazonRechazo = changeUrl($RazonRechazo);
	$NomProveedorSugerido = changeUrl($NomProveedorSugerido);
	$detalles = changeUrl($detalles);
	if ($accion == "conformar" && $_PARAMETRO['REQAPROB'] == 'S') $accion = "aprobar";
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-----------------
		//	valido errores
		if ($TipoRequerimiento == "01") {
			$i = 0;
			$detalle = split(";char:tr;", $detalles);
			foreach ($detalle as $linea) {
				list($_CodItem, $_CommoditySub, $_Descripcion, $_CodUnidad, $_CodCentroCosto, $_FlagExonerado, $_CantidadPedida, $_FlagCompraAlmacen, $_CodCuenta, $_cod_partida) = split(";char:td;", $linea);
				$var = "$_CodItem.$_CodCentroCosto";
				$item[$i] = $var;
				$j = 0;
				$x = 0;
				for($j=0; $j<=$i; $j++) {
					if ($var == $item[$j]) $x++;
					if ($x > 1) die("Se encontraron varias lineas del Item <strong>$_CodItem</strong> dirigido al Centro de Costo <strong>$_CodCentroCosto</strong>");
				}
				$i++;
			}
		}
		//	inserto requerimiento
		##	genero el nuevo codigo
		$CodRequerimiento = getCodigo("lg_requerimientos", "CodRequerimiento", 10);
		$Correlativo = getCodigo_3("lg_requerimientos", "Secuencia", "Anio", "CodDependencia", $Anio, $CodDependencia, 3);
		$Secuencia = intval($Correlativo);
		$CodInternoDependencia = getCodInternoDependencia($CodDependencia);
		$CodInterno = "$CodInternoDependencia-$Correlativo-$Anio";
		##	inserto
		$sql = "INSERT INTO lg_requerimientos
				SET
					CodRequerimiento = '".$CodRequerimiento."',
					CodInterno = '".$CodInterno."',
					CodOrganismo = '".$CodOrganismo."',
					CodDependencia = '".$CodDependencia."',
					CodCentroCosto = '".$CodCentroCosto."',
					CodAlmacen = '".$CodAlmacen."',
					Clasificacion = '".$Clasificacion."',
					Prioridad = '".$Prioridad."',
					TipoClasificacion = '".$TipoClasificacion."',
					FechaRequerida = '".formatFechaAMD($FechaRequerida)."',
					PreparadaPor = '".$PreparadaPor."',
					FechaPreparacion = '".formatFechaAMD($FechaPreparacion)."',
					Comentarios = '".$Comentarios."',
					Anio = '".$Anio."',
					Secuencia = '".$Secuencia."',
					FlagCajaChica = '".$FlagCajaChica."',
					ProveedorSugerido = '".$ProveedorSugerido."',
					ClasificacionOC = '".$ClasificacionOC."',
					ProveedorDocRef = '".$ProveedorDocRef."',
					Ejercicio = '".$Ejercicio."',
					CodPresupuesto = '".$CodPresupuesto."',
					CodFuente = '".$CodFuente."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		//	inserto detalles
		$_Secuencia = 0;
		$detalle = split(";char:tr;", $detalles);
		foreach ($detalle as $linea) {
			list($_CodItem, $_CommoditySub, $_Descripcion, $_CodUnidad, $_CodCentroCosto, $_FlagExonerado, $_CantidadPedida, $_FlagCompraAlmacen, $_CategoriaProg, $_Ejercicio, $_CodPresupuesto, $_CodFuente, $_Activo, $_CodCuenta, $_CodCuentaPub20, $_cod_partida) = split(";char:td;", $linea);
			##	consulto si el commodity requiere nro de activo
			//if ($Clasificacion == "SER" && $_Activo == "") die("El commodity <strong>$_CommoditySub - $_Descripcion</strong> requiere el Nro. de Activo");
			##	inserto
			$sql = "INSERT INTO lg_requerimientosdet
					SET
						CodRequerimiento = '".$CodRequerimiento."',
						Secuencia = '".++$_Secuencia."',
						CodOrganismo = '".$CodOrganismo."',
						CodItem = '".$_CodItem."',
						CommoditySub = '".$_CommoditySub."',
						Descripcion = '".$_Descripcion."',
						CodUnidad = '".$_CodUnidad."',
						CantidadPedida = '".$_CantidadPedida."',
						FlagExonerado = '".$_FlagExonerado."',
						FlagCompraAlmacen = '".$_FlagCompraAlmacen."',
						Ejercicio = '".$_Ejercicio."',
						CodPresupuesto = '".$_CodPresupuesto."',
						CodFuente = '".$_CodFuente."',
						CodCentroCosto = '".$_CodCentroCosto."',
						Anio = '".$Anio."',
						Estado = '".$Estado."',
						CodCuenta = '".$_CodCuenta."',
						CodCuentaPub20 = '".$_CodCuentaPub20."',
						cod_partida = '".$_cod_partida."',
						Activo = '".$_Activo."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		if ($detalles_anterior != "") {
			##	detalles seleccionados
			$detalle = split(";char:tr;", $detalles_anterior);
			foreach ($detalle as $linea) {
				list($_Requerimiento, $_CodItem, $_CommoditySub, $_Descripcion, $_CodUnidad, $_CodCentroCosto, $_FlagExonerado, $_CantidadPedida, $_CodCuenta, $_cod_partida, $_Comentarios) = split(";char:td;", $linea);
				list($_CodRequerimiento, $_Secuencia) = split("[.]", $_Requerimiento);
				##	actualizo
				$sql = "UPDATE lg_requerimientosdet 
						SET FlagCompraAlmacen = 'A'
						WHERE
							CodRequerimiento = '".$_CodRequerimiento."' AND
							Secuencia = '".$_Secuencia."'";
				execute($sql);
			}
		}
		echo "|Se ha generado el Requerimiento <strong>Nro. $CodInterno</strong>";
		//	-----------------
		mysql_query("COMMIT");
	}
	//	modificar
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		//	-----------------
		//	valido errores
		if ($TipoRequerimiento == "01") {
			$i = 0;
			$detalle = split(";char:tr;", $detalles);
			foreach ($detalle as $linea) {
				list($_CodItem, $_CommoditySub, $_Descripcion, $_CodUnidad, $_CodCentroCosto, $_FlagExonerado, $_CantidadPedida, $_FlagCompraAlmacen, $_CategoriaProg, $_Ejercicio, $_CodPresupuesto, $_CodFuente, $_Activo, $_CodCuenta, $_CodCuentaPub20, $_cod_partida) = split(";char:td;", $linea);
				$var = "$_CodItem.$_CodCentroCosto";
				$item[$i] = $var;
				$j = 0;
				$x = 0;
				for($j=0; $j<=$i; $j++) {
					if ($var == $item[$j]) $x++;
					if ($x > 1) die("Se encontraron varias lineas del Item <strong>$_CodItem</strong> dirigido al Centro de Costo <strong>$_CodCentroCosto</strong>");
				}
				$i++;
			}
		}
		
		//	modifico requerimiento
		$sql = "UPDATE lg_requerimientos
				SET
					CodCentroCosto = '".$CodCentroCosto."',
					CodAlmacen = '".$CodAlmacen."',
					Prioridad = '".$Prioridad."',
					FechaRequerida = '".formatFechaAMD($FechaRequerida)."',
					Comentarios = '".$Comentarios."',
					FlagCajaChica = '".$FlagCajaChica."',
					ProveedorSugerido = '".$ProveedorSugerido."',
					ClasificacionOC = '".$ClasificacionOC."',
					ProveedorDocRef = '".$ProveedorDocRef."',
					Ejercicio = '".$Ejercicio."',
					CodPresupuesto = '".$CodPresupuesto."',
					CodFuente = '".$CodFuente."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodRequerimiento = '".$CodRequerimiento."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	inserto detalles
		##	elimino
		$sql = "DELETE FROM lg_requerimientosdet WHERE CodRequerimiento = '".$CodRequerimiento."'";
		$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		$_Secuencia = 0;
		$detalle = split(";char:tr;", $detalles);
		foreach ($detalle as $linea) {
			list($_CodItem, $_CommoditySub, $_Descripcion, $_CodUnidad, $_CodCentroCosto, $_FlagExonerado, $_CantidadPedida, $_FlagCompraAlmacen, $_CategoriaProg, $_Ejercicio, $_CodPresupuesto, $_CodFuente, $_Activo, $_CodCuenta, $_CodCuentaPub20, $_cod_partida) = split(";char:td;", $linea);
			##	consulto si el commodity requiere nro de activo
			//if ($Clasificacion == "SER" && $_Activo == "") die("El commodity <strong>$_CommoditySub - $_Descripcion</strong> requiere el Nro. de Activo");
			##	inserto
			$sql = "INSERT INTO lg_requerimientosdet
					SET
						CodRequerimiento = '".$CodRequerimiento."',
						Secuencia = '".++$_Secuencia."',
						CodOrganismo = '".$CodOrganismo."',
						CodItem = '".$_CodItem."',
						CommoditySub = '".$_CommoditySub."',
						Descripcion = '".$_Descripcion."',
						CodUnidad = '".$_CodUnidad."',
						CantidadPedida = '".$_CantidadPedida."',
						FlagExonerado = '".$_FlagExonerado."',
						FlagCompraAlmacen = '".$_FlagCompraAlmacen."',
						Ejercicio = '".$_Ejercicio."',
						CodPresupuesto = '".$_CodPresupuesto."',
						CodFuente = '".$_CodFuente."',
						CodCentroCosto = '".$_CodCentroCosto."',
						Anio = '".$Anio."',
						Estado = '".$Estado."',
						CodCuenta = '".$_CodCuenta."',
						CodCuentaPub20 = '".$_CodCuentaPub20."',
						cod_partida = '".$_cod_partida."',
						Activo = '".$_Activo."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		}
		//	-----------------
		mysql_query("COMMIT");
	}
	//	revisar
	elseif ($accion == "revisar") {
		//	modifico requerimiento
		$sql = "UPDATE lg_requerimientos
				SET
					Estado = 'RV',
					RevisadaPor = '".$RevisadaPor."',
					FechaRevision = '".formatFechaAMD($FechaRevision)."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodRequerimiento = '".$CodRequerimiento."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	}
	//	conformar
	elseif ($accion == "conformar") {
		//	modifico requerimiento
		$sql = "UPDATE lg_requerimientos
				SET
					FlagCajaChica = '".$FlagCajaChica."',
					Estado = 'CN',
					ConformadaPor = '".$ConformadaPor."',
					FechaConformacion = '".formatFechaAMD($FechaConformacion)."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodRequerimiento = '".$CodRequerimiento."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	}
	//	aprobar
	elseif ($accion == "aprobar") {
		//	modifico requerimiento
		$sql = "UPDATE lg_requerimientos
				SET
					Estado = 'AP',
					AprobadaPor = '".$AprobadaPor."',
					FechaAprobacion = '".formatFechaAMD($FechaAprobacion)."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodRequerimiento = '".$CodRequerimiento."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		//	modifico requerimiento
		$sql = "UPDATE lg_requerimientosdet
				SET
					Estado = 'PE',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodRequerimiento = '".$CodRequerimiento."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		//	si selecciono proveedor sugerido
		if ($ProveedorSugerido != "") {
			//	numero de cotizacion proveedor
			$Numero = intval(getCodigo("lg_cotizacion", "Numero", 10));
			$NroCotizacionProv = getCodigo("lg_cotizacion", "NroCotizacionProv", 8);
			$NumeroInterno = getCodigo("lg_cotizacion", "NumeroInterno", 8, "Anio", date("Y"));
			$CodFormaPago = getValorCampo("mastproveedores", "CodProveedor", "CodFormaPago", $ProveedorSugerido);
			$NroInvitaciones = 1;
			$FechaLimite = getFechaFin(formatFechaDMA(substr(ahora(), 0, 10)), $_PARAMETRO['DIASLIMCOT']);
			//	consulto detalles
			$sql = "SELECT *
					FROM lg_requerimientosdet
					WHERE CodRequerimiento = '".$CodRequerimiento."'
					ORDER BY Secuencia";
			$query_det = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field_det = mysql_fetch_array($query_det)) {
				//	numero de invitacines y el numero de cotizacion
				$CotizacionNumero = getCodigo("lg_cotizacion", "CotizacionNumero", 8);
				##
				$CodFormaPago = getValorCampo("mastproveedores", "CodProveedor", "CodFormaPago", $ProveedorSugerido);
				//	inserto cotizacion
				$sql = "INSERT INTO lg_cotizacion
						SET
							CodOrganismo = '".$field_det['CodOrganismo']."',
							CodRequerimiento = '".$field_det['CodRequerimiento']."',
							Secuencia = '".$field_det['Secuencia']."',
							CotizacionNumero = '".$CotizacionNumero."',
							Numero = '".$Numero."',
							CodProveedor = '".$ProveedorSugerido."',
							NomProveedor = '".$NomProveedorSugerido."',
							CodFormaPago = '".$CodFormaPago."',
							Observaciones = '".($field_det['Comentarios'])."',
							Cantidad = '".$field_det['CantidadPedida']."',
							Estado = 'A',
							NroCotizacionProv = '".$NroCotizacionProv."',
							NumeroInterno = '".$NumeroInterno."',
							FlagAsignado = 'S',
							FlagExonerado = '".$field_det['FlagExonerado']."',
							FechaInvitacion = NOW(),
							FechaDocumento = NOW(),
							NumeroInvitacion = 'AUTOMATICO',
							FechaEntrega = '".formatFechaAMD($FechaLimite)."',
							FechaLimite = '".formatFechaAMD($FechaLimite)."',
							FlagUnidadCompra = 'N',
							CantidadCompra = '".$field_det['CantidadPedida']."',
							CodUnidadCompra = '".$field_det['CodUnidad']."',
							UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
							UltimaFecha = NOW()";
				$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			}
			//	
			$sql = "UPDATE lg_requerimientosdet
					SET CotizacionRegistros = '1'
					WHERE CodRequerimiento = '".$CodRequerimiento."'";
			$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		}
	}
	//	rechazar
	elseif ($accion == "rechazar") {
		//	modifico requerimiento
		$sql = "UPDATE lg_requerimientos
				SET
					Estado = 'RE',
					RazonRechazo = '".$RazonRechazo."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodRequerimiento = '".$CodRequerimiento."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	modifico detalles
		$sql = "UPDATE lg_requerimientosdet
				SET
					Estado = 'RE',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodRequerimiento = '".$CodRequerimiento."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	}
	//	anular
	elseif ($accion == "anular") {
		if ($Estado == "PR") {
			$EstadoRequerimiento = "AN";
			$EstadoDetalle = "AN";
		}
		elseif ($Estado != "PR") {
			$EstadoRequerimiento = "PR";
			$EstadoDetalle = "PR";
		}
		
		//	modifico requerimiento
		$sql = "UPDATE lg_requerimientos
				SET
					Estado = '".$EstadoRequerimiento."',
					RazonRechazo = '".$RazonRechazo."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodRequerimiento = '".$CodRequerimiento."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	modifico detalles
		$sql = "UPDATE lg_requerimientosdet
				SET
					Estado = '".$EstadoDetalle."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodRequerimiento = '".$CodRequerimiento."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	}
	//	cerrar
	elseif ($accion == "cerrar") {
		//	modifico requerimiento
		$sql = "UPDATE lg_requerimientos
				SET
					Estado = 'CE',
					RazonRechazo = '".$RazonRechazo."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodRequerimiento = '".$CodRequerimiento."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	modifico detalles
		$sql = "UPDATE lg_requerimientosdet
				SET
					Estado = 'CE',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodRequerimiento = '".$CodRequerimiento."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	}
	//	cerrar linea
	elseif ($accion == "cerrar-detalle") {
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
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
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
				$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			} else {
				$sql = "UPDATE lg_requerimientos
						SET Estado = 'CE'
						WHERE CodRequerimiento = '".$CodRequerimiento."'";
				$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			}
		}
	}
	//	modificacion restringida
	elseif ($accion == "modificacion_restringida") {
		mysql_query("BEGIN");
		//	-----------------
		//	valido que no tenga cotizaciones
		$sql = "SELECT * FROM lg_cotizacion WHERE CodRequerimiento = '$CodRequerimiento'";
		if (count(getRecord($sql))) die('No puede modificar a dirigir a Caja Chica este requerimientos porque ya tiene cotizaciones');
		//	modifico requerimiento
		$sql = "UPDATE lg_requerimientos
				SET
					Prioridad = '".$Prioridad."',
					FechaRequerida = '".formatFechaAMD($FechaRequerida)."',
					Comentarios = '".$Comentarios."',
					ProveedorSugerido = '".$ProveedorSugerido."',
					ClasificacionOC = '".$ClasificacionOC."',
					ProveedorDocRef = '".$ProveedorDocRef."',
					PreparadaPor = '".$PreparadaPor."',
					FechaPreparacion = '".formatFechaAMD($FechaPreparacion)."',
					RevisadaPor = '".$RevisadaPor."',
					FechaRevision = '".formatFechaAMD($FechaRevision)."',
					ConformadaPor = '".$ConformadaPor."',
					FechaConformacion = '".formatFechaAMD($FechaConformacion)."',
					AprobadaPor = '".$AprobadaPor."',
					FechaAprobacion = '".formatFechaAMD($FechaAprobacion)."',
					FlagCajaChica = '".$FlagCajaChica."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodRequerimiento = '".$CodRequerimiento."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
//	ajax
elseif ($modulo == "ajax") {
	//	insertar lineas
	if ($accion == "requerimiento_detalles_insertar") {
		if ($Tipo == "item") {
			$readonly = "readonly";
			$sql = "SELECT *, CtaGasto AS CodCuenta, CtaGastoPub20 AS CodCuentaPub20, PartidaPresupuestal AS cod_partida
					FROM lg_itemmast
					WHERE CodItem = '".$Codigo."'";
			$disabled_descripcion = "disabled";
			$CodItem = $Codigo;
		} else {
			$sql = "SELECT
						cs.*,
						cm.Clasificacion,
						cm.Descripcion AS NomCommodity
					FROM
						lg_commoditysub cs
						INNER JOIN lg_commoditymast cm ON (cs.CommodityMast = cm.CommodityMast)
					WHERE cs.Codigo = '".$Codigo."'";
			$CommoditySub = $Codigo;
		}
		$query = mysql_query($sql) or die($sql.mysql_error());
		if (mysql_num_rows($query) != 0) {
			$field_detalles = mysql_fetch_array($query);
			if ($Tipo == "item" ) $Descripcion = $field_detalles['Descripcion'];
			else $Descripcion = strtoupper($field_detalles['NomCommodity']."-".$field_detalles['Descripcion'])
			?>
	        <tr class="trListaBody" onclick="mClk(this, 'sel_detalles');" id="detalles_<?=$nrodetalle?>">
	            <th align="center">
	                <?=$nrodetalle?>
	            </th>
	            <td align="center">
	            	<?=$Codigo?>
	                <input type="hidden" name="CodItem" class="cell2" style="text-align:center;" value="<?=$CodItem?>" readonly />
	                <input type="hidden" name="CommoditySub" class="cell2" style="text-align:center;" value="<?=$CommoditySub?>" readonly />
	            </td>
	            <td align="center">
	                <textarea name="Descripcion" style="height:30px;" class="cell" onBlur="this.style.height='30px';" onFocus="this.style.height='60px';" <?=$disabled_descripcion?>><?=($Descripcion)?></textarea>
	            </td>
	            <td align="center">
					<select name="CodUnidad" class="cell">
						<?=loadSelect2('mastunidades','CodUnidad','Descripcion',$field_detalles['CodUnidad'],20)?>
					</select>
	            </td>
	            <td align="center">
	                <input type="text" name="CodCentroCosto" id="CodCentroCosto_<?=$nrodetalle?>" class="cell" style="text-align:center;" value="<?=$CodCentroCosto?>" />
	                <input type="hidden" name="NomCentroCosto" id="NomCentroCosto_<?=$nrodetalle?>" />
	            </td>
	            <td align="center">
	                <input type="checkbox" name="FlagExonerado" <?=chkFlag("N")?> />
	            </td>
	            <td align="center">
	                <input type="text" name="CantidadPedida" class="cell" style="text-align:right; font-weight:bold;" value="0,00" onBlur="numeroBlur(this);" onFocus="numeroFocus(this);" />
	            </td>
	            <td align="center">
	            	<input type="hidden" name="FlagCompraAlmacen" value="<?=$FlagCompraAlmacen?>" />
	                <?=printValoresGeneral("DIRIGIDO", $FlagCompraAlmacen)?>
	            </td>
				<td align="center">
	                <input type="text" name="detallesCategoriaProg" id="detallesCategoriaProg_<?=$nrodetalle?>" value="<?=$CategoriaProg?>" class="cell2 CategoriaProg" style="text-align:center;" readonly />
	                <input type="hidden" name="detallesEjercicio" id="detallesEjercicio_<?=$nrodetalle?>" value="<?=$Ejercicio?>" class="cell2 Ejercicio" style="text-align:center;" readonly />
	                <input type="hidden" name="detallesCodPresupuesto" id="detallesCodPresupuesto_<?=$nrodetalle?>" value="<?=$CodPresupuesto?>" class="cell2 CodPresupuesto" style="text-align:center;" readonly />
	            </td>
	            <td>
					<select name="detallesCodFuente" id="detallesCodFuente_<?=$nrodetalles?>" class="cell2 CodFuente">
						<?=loadSelect2("pv_fuentefinanciamiento","CodFuente","Denominacion",$CodFuente,10)?>
					</select>
	            </td>
				<td align="center">
	            	<input type="text" name="detallesCodPlanObra" id="detallesCodPlanObra_<?=$nrodetalle?>" class="cell2" style="text-align:center;" readonly />
	            </td>
	            <td align="center">
	                <input type="text" name="Activo" id="Activo_<?=$nrodetalle?>" class="cell" style="text-align:center;" />
	            </td>
	            <td align="center">
	                <?=printValoresGeneral("ESTADO-REQUERIMIENTO-DETALLE", 'PE')?>
	            </td>
				<td align="center">
	                <input type="hidden" name="CodCuenta" value="<?=$field_detalles['CodCuenta']?>" />
	                <input type="hidden" name="CodCuentaPub20" value="<?=$field_detalles['CodCuentaPub20']?>" />
	                <input type="hidden" name="cod_partida" value="<?=$field_detalles['cod_partida']?>" />
	            </td>
	            <td align="right">
	                0,00
	            </td>
	            <td align="right">
	                0,00
	            </td>
	            <td align="center">&nbsp;
	                
	            </td>
			</tr>
	       <?php
		}
	}
	//	distribucion
	elseif($accion == "mostrarTabDistribucionRequerimiento") {
		//	obtengo detalles
		$_TOTAL = 0;
		$detalle = split(";char:tr;", $detalles);
		foreach ($detalle as $linea) {
			list($_CantidadPedida, $_CategoriaProg, $_Ejercicio, $_CodPresupuesto, $_CodFuente, $_CodCuenta, $_CodCuentaPub20, $_cod_partida) = split(";char:td;", $linea);
			$_CUENTA[$_CodCuenta] = $_CodCuenta;
			$_CUENTA20[$_CodCuentaPub20] = $_CodCuentaPub20;
			$_CUENTA20[$_CodCuentaPub20] = $_CodCuentaPub20;
			$_PARTIDA[$_CodPresupuesto][$_CodFuente][$_cod_partida] = $_cod_partida;
			$_CUENTA_CANTIDAD[$_CodCuenta] += $_CantidadPedida;
			$_CUENTA_CANTIDAD20[$_CodCuentaPub20] += $_CantidadPedida;
			$_PARTIDA_CANTIDAD[$_CodPresupuesto][$_CodFuente][$_cod_partida] += $_CantidadPedida;
			$_CUENTA_NUMERO[$_CodCuenta] += 1;
			$_CUENTA_NUMERO20[$_CodCuentaPub20] += 1;
			$_PARTIDA_NUMERO[$_CodPresupuesto][$_CodFuente][$_cod_partida] += 1;
				$_EJERCICIO[$_CodPresupuesto][$_CodFuente][$_cod_partida] = $_Ejercicio;
				$_PRESUPUESTO[$_CodPresupuesto][$_CodFuente][$_cod_partida] = $_CodPresupuesto;
				$_CATEGORIA[$_CodPresupuesto][$_CodFuente][$_cod_partida] = $_CategoriaProg;
				$_FUENTE[$_CodPresupuesto][$_CodFuente][$_cod_partida] = $_CodFuente;
			$_TOTAL++;
		}
		//	imprimo cuentas
		foreach ($_CUENTA as $CodCuenta) {
			$Descripcion = getValorCampo("ac_mastplancuenta", "CodCuenta", "Descripcion", $CodCuenta);
			$Porcentaje = $_CUENTA_NUMERO[$CodCuenta] * 100 / $_TOTAL;
			if ($Descripcion != "") {
				?>
				<tr class="trListaBody">
					<td align="center">
						<?=$CodCuenta?>
					</td>
					<td>
						<?=$Descripcion?>
					</td>
					<td align="right">
						<?=number_format($Porcentaje, 2, ',', '.')?>
					</td>
				</tr>
				<?php
			}
		}
		echo "|";
		//	imprimo cuentas pub. 20
		foreach ($_CUENTA20 as $CodCuentaPub20) {
			$Descripcion = getValorCampo("ac_mastplancuenta20", "CodCuenta", "Descripcion", $CodCuentaPub20);
			$Porcentaje = $_CUENTA_NUMERO20[$CodCuentaPub20] * 100 / $_TOTAL;
			if ($Descripcion != "") {
				?>
				<tr class="trListaBody">
					<td align="center">
						<?=$CodCuentaPub20?>
					</td>
					<td>
						<?=$Descripcion?>
					</td>
					<td align="right">
						<?=number_format($Porcentaje, 2, ',', '.')?>
					</td>
				</tr>
				<?php
			}
		}
		echo "|";
		//	imprimo partidas
		foreach ($_PARTIDA as $_CodPresupuesto => $Presupuesto) {
			foreach ($Presupuesto as $_CodFuente => $Partida) {
				$Fuente = getValorCampo("pv_fuentefinanciamiento", "CodFuente", "Denominacion", $_CodFuente);
				$sql = "SELECT
							pv.CategoriaProg,
							ue.Denominacion AS UnidadEjecutora
						FROM pv_presupuesto pv
						INNER JOIN pv_categoriaprog cp ON (cp.CategoriaProg = pv.CategoriaProg)
						INNER JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cp.CodUnidadEjec)
						WHERE pv.CodOrganismo = '$CodOrganismo' AND pv.CodPresupuesto = '$_CodPresupuesto'";
				$field_categoria = getRecord($sql);
				?>
				<tr class="trListaBody2">
					<td colspan="3">
						<?=$field_categoria['CategoriaProg']?> - <?=$field_categoria['UnidadEjecutora']?>
					</td>
				</tr>
				<?php
				foreach ($Partida as $cod_partida) {
					list($MontoAjustado, $MontoCompromiso, $PreCompromiso, $CotizacionesAsignadas) = disponibilidadPartida2($Ejercicio, $CodOrganismo, $cod_partida, $_CodPresupuesto, $CodFuente);
					$MontoPendiente = $PreCompromiso + $CotizacionesAsignadas;
					$MontoDisponible = $MontoAjustado - $MontoCompromiso;
					$MontoDisponibleReal = $MontoAjustado - ($MontoCompromiso + $MontoPendiente);
					##	valido
					if ($MontoDisponible <= 0) $style = "style='font-weight:bold; background-color:#F8637D;'";
					elseif($MontoDisponibleReal <= 0) $style = "style='font-weight:bold; background-color:#FFC;'";
					else $style = "style='font-weight:bold; background-color:#D0FDD2;'";
					##	
					$Descripcion = getValorCampo("pv_partida", "cod_partida", "denominacion", $cod_partida);
					$Porcentaje = $_PARTIDA_NUMERO[$_CodPresupuesto][$_CodFuente][$cod_partida] * 100 / $_TOTAL;
					if ($Descripcion != "") {
						?>
						<tr class="trListaBody" <?=$style?>>
							<td align="center">
								<?=$_CodFuente?>
				            </td>
							<td align="center">
		                        <input type="hidden" name="CodPartida[]" value="<?=$cod_partida?>" />
		                        <input type="hidden" name="MontoAjustado[]" value="<?=$MontoAjustado?>" />
		                        <input type="hidden" name="MontoCompromiso[]" value="<?=$MontoCompromiso?>" />
		                        <input type="hidden" name="PreCompromiso[]" value="<?=$PreCompromiso?>" />
		                        <input type="hidden" name="CotizacionesAsignadas[]" value="<?=$CotizacionesAsignadas?>" />
		                        <input type="hidden" name="MontoDisponible[]" value="<?=$MontoDisponible?>" />
		                        <input type="hidden" name="MontoDisponibleReal[]" value="<?=$MontoDisponibleReal?>" />
		                        <input type="hidden" name="MontoPendiente[]" value="<?=$MontoPendiente?>" />
		                        <input type="hidden" name="partidasEjercicio[]" value="<?=$Ejercicio?>" />
		                        <input type="hidden" name="partidasCodPresupuesto[]" value="<?=$CodPresupuesto?>" />
		                        <input type="hidden" name="partidasCodFuente[]" value="<?=$_CodFuente?>" />
								<?=$cod_partida?>
							</td>
							<td>
								<?=$Descripcion?>
							</td>
							<td align="right">
								<?=number_format($Porcentaje, 2, ',', '.')?>
							</td>
						</tr>
						<?php
					}
				}
			}
		}
		/*foreach ($_PARTIDA as $CodFuente => $Partida) {
			$Fuente = getValorCampo("pv_fuentefinanciamiento", "CodFuente", "Denominacion", $CodFuente);
			?>
			<tr class="trListaBody2">
				<td colspan="3">
					<?=$CodFuente?> - <?=$Fuente?>
				</td>
			</tr>
			<?php
			foreach ($Partida as $cod_partida) {
				list($MontoAjustado, $MontoCompromiso, $PreCompromiso, $CotizacionesAsignadas) = disponibilidadPartida2($Ejercicio, $CodOrganismo, $cod_partida, $CodPresupuesto, $CodFuente);
				$MontoPendiente = $PreCompromiso + $CotizacionesAsignadas;
				$MontoDisponible = $MontoAjustado - $MontoCompromiso;
				$MontoDisponibleReal = $MontoAjustado - ($MontoCompromiso + $MontoPendiente);
				##	valido
				if ($MontoDisponible <= 0) $style = "style='font-weight:bold; background-color:#F8637D;'";
				elseif($MontoDisponibleReal <= 0) $style = "style='font-weight:bold; background-color:#FFC;'";
				else $style = "style='font-weight:bold; background-color:#D0FDD2;'";
				##	
				$Descripcion = getValorCampo("pv_partida", "cod_partida", "denominacion", $cod_partida);
				$Porcentaje = $_PARTIDA_NUMERO[$CodFuente][$cod_partida] * 100 / $_TOTAL;
				if ($Descripcion != "") {
					?>
					<tr class="trListaBody" <?=$style?>>
						<td align="center">
	                        <input type="hidden" name="CodPartida[]" value="<?=$cod_partida?>" />
	                        <input type="hidden" name="MontoAjustado[]" value="<?=$MontoAjustado?>" />
	                        <input type="hidden" name="MontoCompromiso[]" value="<?=$MontoCompromiso?>" />
	                        <input type="hidden" name="PreCompromiso[]" value="<?=$PreCompromiso?>" />
	                        <input type="hidden" name="CotizacionesAsignadas[]" value="<?=$CotizacionesAsignadas?>" />
	                        <input type="hidden" name="MontoDisponible[]" value="<?=$MontoDisponible?>" />
	                        <input type="hidden" name="MontoDisponibleReal[]" value="<?=$MontoDisponibleReal?>" />
	                        <input type="hidden" name="MontoPendiente[]" value="<?=$MontoPendiente?>" />
	                        <input type="hidden" name="partidasEjercicio[]" value="<?=$Ejercicio?>" />
	                        <input type="hidden" name="partidasCodPresupuesto[]" value="<?=$CodPresupuesto?>" />
	                        <input type="hidden" name="partidasCodFuente[]" value="<?=$CodFuente?>" />
							<?=$cod_partida?>
						</td>
						<td>
							<?="disponibilidadPartida2($Ejercicio, $CodOrganismo, $cod_partida, $CodPresupuesto, $CodFuente)"?>
						</td>
						<td align="right">
							<?=number_format($Porcentaje, 2, ',', '.')?>
						</td>
					</tr>
					<?php
				}
			}
		}*/
	}
}
?>