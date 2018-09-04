<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion.sql", "w+");
///////////////////////////////////////////////////////////////////////////////
//	OBLIGACIONES (NUEVO, MODIFICAR, REVISAR, APROBAR, ANULAR)
///////////////////////////////////////////////////////////////////////////////
//	obligacion
if ($modulo == "obligacion") {
	$MontoObligacion = setNumero($MontoObligacion);
	$MontoImpuestoOtros = setNumero($MontoImpuestoOtros);
	$MontoNoAfecto = setNumero($MontoNoAfecto);
	$MontoAfecto = setNumero($MontoAfecto);
	$MontoAdelanto = setNumero($MontoAdelanto);
	$MontoImpuesto = setNumero($MontoImpuesto);
	$MontoPagoParcial = setNumero($MontoPagoParcial);
	$Comentarios = changeUrl($Comentarios);
	$ComentariosAdicional = changeUrl($ComentariosAdicional);
	$MotivoAnulacion = changeUrl($MotivoAnulacion);
	$detalles_impuesto = changeUrl($detalles_impuesto);
	$detalles_documento = changeUrl($detalles_documento);
	$detalles_distribucion = changeUrl($detalles_distribucion);
	list($DiaObligacion, $MesObligacion, $AnioObligacion) = split("[./-]", $FechaRegistro);
	$Periodo = "$AnioObligacion-$MesObligacion";
	$Anio = $AnioObligacion;
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//--------------------
		//	verifico valores ingresados
		if (valObligacion($CodProveedor, $CodTipoDocumento, $NroDocumento)) die("Nro. de Obligacion Ya ingresado");
		//	obtengo el numero de las ordenes
		if ($ReferenciaDocumento == "") {
			$ReferenciaTipoDocumento = "";
			$ReferenciaNroDocumento = "";
			$linea_documento = split(";char:tr;", $detalles_documento);
			foreach ($linea_documento as $registro) {
				list($_Porcentaje, $_DocumentoClasificacion, $_DocumentoReferencia, $_Fecha, $_ReferenciaTipoDocumento, $_ReferenciaNroDocumento, $_MontoTotal, $_MontoAfecto, $_MontoImpuestos, $_MontoAfecto, $_MontoNoAfecto, $_Comentarios) = split(";char:td;", $registro);
				$ReferenciaTipoDocumento = $_ReferenciaTipoDocumento;
				if ($k == 0) $ReferenciaNroDocumento .= $_ReferenciaNroDocumento;
				else $ReferenciaNroDocumento .= "-".$_ReferenciaNroDocumento;
				$k++;
			}
		} else {
			list($ReferenciaTipoDocumento, $ReferenciaNroDocumento) = explode("-", $ReferenciaDocumento);
		}
		//	inserto obligacion
		if ($CodObra) $iCodObra = "CodObra = '".$CodObra."',"; else $iCodObra = "";
		if ($CodValuacion) $iCodValuacion = "CodValuacion = '".$CodValuacion."',"; else $iCodValuacion = "";
		$NroRegistro = getCodigo_2("ap_obligaciones", "NroRegistro", "CodOrganismo", $CodOrganismo, 6);
		$NroDocumento = codigo('ap_obligaciones','NroDocumento',10,['CodProveedor','CodTipoDocumento'],[$CodProveedor,$CodTipoDocumento]);
		$sql = "INSERT INTO ap_obligaciones
				SET
					CodProveedor = '".$CodProveedor."',
					CodTipoDocumento = '".$CodTipoDocumento."',
					NroDocumento = '".$NroDocumento."',
					CodOrganismo = '".$CodOrganismo."',
					CodProveedorPagar = '".$CodProveedorPagar."',
					NroControl = '".$NroControl."',
					NroFactura = '".$NroFactura."',
					NroCuenta = '".$NroCuenta."',
					CodTipoPago = '".$CodTipoPago."',
					CodTipoServicio = '".$CodTipoServicio."',
					ReferenciaTipoDocumento = '".$ReferenciaTipoDocumento."',
					ReferenciaNroDocumento = '".$ReferenciaNroDocumento."',
					MontoObligacion = '".($MontoObligacion)."',
					MontoImpuestoOtros = '".($MontoImpuestoOtros)."',
					MontoNoAfecto = '".($MontoNoAfecto)."',
					MontoAfecto = '".($MontoAfecto)."',
					MontoAdelanto = '".($MontoAdelanto)."',
					MontoImpuesto = '".($MontoImpuesto)."',
					MontoPagoParcial = '".($MontoPagoParcial)."',
					NroRegistro = '".$NroRegistro."',
					Comentarios = '".$Comentarios."',
					ComentariosAdicional = '".$ComentariosAdicional."',
					FechaRegistro = '".formatFechaAMD($FechaRegistro)."',
					FechaVencimiento = '".formatFechaAMD($FechaVencimiento)."',
					FechaRecepcion = '".formatFechaAMD($FechaRecepcion)."',
					FechaDocumento = '".formatFechaAMD($FechaDocumento)."',
					FechaProgramada = '".formatFechaAMD($FechaProgramada)."',
					FechaFactura = '".formatFechaAMD($FechaFactura)."',
					IngresadoPor = '".($IngresadoPor)."',
					FechaPreparacion = '".formatFechaAMD($FechaPreparacion)."',
					Periodo = '".$Periodo."',
					CodCentroCosto = '".$CodCentroCosto."',
					FlagGenerarPago = '".$FlagGenerarPago."',
					FlagAfectoIGV = '".$FlagAfectoIGV."',
					FlagDiferido = '".$FlagDiferido."',
					FlagPagoDiferido = '".$FlagPagoDiferido."',
					FlagCompromiso = '".$FlagCompromiso."',
					FlagPresupuesto = '".$FlagPresupuesto."',
					FlagPagoIndividual = '".$FlagPagoIndividual."',
					FlagCajaChica = '".$FlagCajaChica."',
					FlagDistribucionManual = '".$FlagDistribucionManual."',
					CodPresupuesto = '".$CodPresupuesto."',
					Ejercicio = '".$Ejercicio."',
					CodFuente = '".$CodFuente."',
					FlagNomina = '".$FlagNomina."',
					FlagFacturaPendiente = '".$FlagFacturaPendiente."',
					$iCodObra
					$iCodValuacion
					FlagAgruparIgv = '".$FlagAgruparIgv."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		//	impuestos
		if ($detalles_impuesto != "") {
			$linea_impuesto = split(";char:tr;", $detalles_impuesto);	$_Linea=0;
			foreach ($linea_impuesto as $registro) {	$_Linea++;
				list($_CodImpuesto, $_CodConcepto, $_Signo, $_FlagImponible, $_FlagProvision, $_CodCuenta, $_CodCuentaPub20, $_MontoAfecto, $_FactorPorcentaje, $_MontoSustraendo, $_MontoAfectoSustraendo, $_MontoImpuesto, $_CodRegimenFiscal) = split(";char:td;", $registro);
				if ($_CodRegimenFiscal == 'A') $_FlagProvision = 'A';
				//	inserto
				$sql = "INSERT INTO ap_obligacionesimpuesto
						SET
							CodProveedor = '".$CodProveedor."',
							CodTipoDocumento = '".$CodTipoDocumento."',
							NroDocumento = '".$NroDocumento."',
							Linea = '".$_Linea."',
							CodImpuesto = '".$_CodImpuesto."',
							CodConcepto = '".$_CodConcepto."',
							FactorPorcentaje = '".$_FactorPorcentaje."',
							MontoImpuesto = '".$_MontoImpuesto."',
							MontoAfecto = '".$_MontoAfecto."',
							CodCuenta = '".$_CodCuenta."',
							CodCuentaPub20 = '".$_CodCuentaPub20."',
							FlagProvision = '".$_FlagProvision."',
							MontoSustraendo = '".$_MontoSustraendo."',
							MontoAfectoSustraendo = '".$_MontoAfectoSustraendo."',
							CodRegimenFiscal = '".$_CodRegimenFiscal."',
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()";
				execute($sql);
			}
		}
		//	documentos
		if ($detalles_documento != "") {
			$linea_documento = split(";char:tr;", $detalles_documento);	$_Linea=0;
			foreach ($linea_documento as $registro) {	$_Linea++;
				list($_Porcentaje, $_DocumentoClasificacion, $_DocumentoReferencia, $_Fecha, $_ReferenciaTipoDocumento, $_ReferenciaNroDocumento, $_MontoTotal, $_MontoAfecto, $_MontoImpuestos, $_MontoAfecto, $_MontoNoAfecto, $_Comentarios) = split(";char:td;", $registro);
				//	consulto si existe el documento
				$sql = "SELECT *
						FROM ap_documentos
						WHERE
							Anio = '".$Anio."' AND
							CodProveedor = '".$CodProveedor."' AND
							DocumentoClasificacion = '".$_DocumentoClasificacion."' AND
							DocumentoReferencia = '".$_DocumentoReferencia."'";
				$field_documento = getRecord($sql);
				if (count($field_documento)) {
					//	actualizo documento
					$sql = "UPDATE ap_documentos
							SET 
								ObligacionTipoDocumento = '".$CodTipoDocumento."',
								ObligacionNroDocumento = '".$NroDocumento."',
								Estado = 'RV',
								UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
								UltimaFecha = NOW()
							WHERE
								Anio = '".$Anio."' AND
								CodProveedor = '".$CodProveedor."' AND
								DocumentoClasificacion = '".$_DocumentoClasificacion."' AND
								DocumentoReferencia = '".$_DocumentoReferencia."'";
					execute($sql);
				} else {
					//	inserto documento
					$secuencia_referencia = getCorrelativoSecuencia_2("ap_documentos", "ReferenciaTipoDocumento", "ReferenciaNroDocumento", $_ReferenciaTipoDocumento, $_ReferenciaNroDocumento);
					$sql = "INSERT INTO ap_documentos
							SET
								CodOrganismo = '".$CodOrganismo."',
								CodProveedor = '".$CodProveedor."',
								DocumentoClasificacion = '".$_DocumentoClasificacion."',
								DocumentoReferencia = '".$_DocumentoReferencia."',
								Fecha = NOW(),
								ReferenciaTipoDocumento = '".$_ReferenciaTipoDocumento."',
								ReferenciaNroDocumento = '".$_ReferenciaNroDocumento."',
								Estado = 'RV',
								ObligacionTipoDocumento = '".$CodTipoDocumento."',
								ObligacionNroDocumento = '".$NroDocumento."',
								MontoAfecto = '".$_MontoAfecto."',
								MontoNoAfecto = '".$_MontoNoAfecto."',
								MontoImpuestos = '".$_MontoImpuestos."',
								MontoTotal = '".$_MontoTotal."',
								MontoPendiente = '".$_MontoTotal."',
								Anio = '".$Anio."',
								UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
								UltimaFecha = NOW()";
					execute($sql);
					//	documentos detalle
					if ($_ReferenciaTipoDocumento == "OC") {
						$sql = "SELECT *
								FROM lg_ordencompradetalle
								WHERE
									Anio = '".$Anio."' AND
									CodOrganismo = '".$CodOrganismo."' AND
									NroOrden = '".$_ReferenciaNroDocumento."'";
					} else {
						$sql = "SELECT *, (CantidadRecibida * PrecioUnit) As PrecioCantidad
								FROM lg_ordenserviciodetalle
								WHERE
									Anio = '".$Anio."' AND
									CodOrganismo = '".$CodOrganismo."' AND
									NroOrden = '".$_ReferenciaNroDocumento."'";
					}
					$field_ordendetalle = getRecords($sql);
					foreach ($field_ordendetalle as $field_od) {
						$sql = "INSERT INTO ap_documentosdetalle
								SET
									CodProveedor = '".$CodProveedor."',
									DocumentoClasificacion = '".$_DocumentoClasificacion."',
									DocumentoReferencia = '$_ReferenciaTipoDocumento-$_ReferenciaNroDocumento-$secuencia_referencia',
									Secuencia = '".$field_od['Secuencia']."',
									CodItem = '".$field_od['CodItem']."',
									CommoditySub = '".$field_od['CommoditySub']."',
									Descripcion = '".$field_od['Descripcion']."',
									Cantidad = '".$field_od['CantidadPedida']."',
									PrecioUnit = '".$field_od['PrecioUnit']."',
									PrecioCantidad = '".$field_od['PrecioCantidad']."',
									Total = '".$field_od['Total']."',
									CodCentroCosto = '".$field_od['CodCentroCosto']."',
									Anio = '".$Anio."',
									UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
									UltimaFecha = NOW()";
						execute($sql);
					}
				}			
				//	verifico si la orden tiene activos fijos
				if ($_ReferenciaTipoDocumento == "OC") {
					$sql = "SELECT ocd.*
							FROM 
								lg_ordencompradetalle ocd
								INNER JOIN lg_commoditysub cs ON (ocd.CommoditySub = cs.Codigo)
								INNER JOIN lg_commoditymast cm ON (cs.CommodityMast = cm.CommodityMast)
							WHERE 
								(cm.Clasificacion = 'ACT' OR cm.Clasificacion = 'BME') AND
								ocd.Anio = '".$Anio."' AND
								ocd.CodOrganismo = '".$CodOrganismo."' AND
								ocd.NroOrden = '".$_ReferenciaNroDocumento."'";
					$field_comm = getRecord($sql);
					if (count($field_comm)) {
						$sql = "UPDATE lg_activofijo
								SET
									FlagFacturado = 'S',
									ObligacionTipoDocumento = '".$CodTipoDocumento."',
									ObligacionNroDocumento = '".$NroDocumento."',
									ObligacionFechaDocumento = '".formatFechaAMD($FechaRegistro)."',
									NroFactura = '".$NroControl."',
									FechaFactura = '".formatFechaAMD($FechaFactura)."'
								WHERE
									Anio = '".$Anio."' AND
									CodOrganismo = '".$CodOrganismo."' AND
									NroOrden = '".$_ReferenciaNroDocumento."'";
						execute($sql);
					}
				}
			}
		}
		//	distribucion
		if ($detalles_distribucion != "") {
			$linea_distribucion = split(";char:tr;", $detalles_distribucion);	$_Secuencia=0;
			foreach ($linea_distribucion as $registro) {	$_Secuencia++;
				list($_cod_partida, $_CodCuenta, $_CodCuentaPub20, $_CodCentroCosto, $_FlagNoAfectoIGV, $_Monto, $_CategoriaProg, $_Ejercicio, $_CodPresupuesto, $_CodFuente, $_TipoOrden, $_NroOrden, $_Referencia, $_Descripcion, $_CodPersona, $_NroActivo, $_FlagDiferido) = split(";char:td;", $registro);
				##	consulto si la cuenta requiere nro de activo
				$FlagReqActivo = getVar3("SELECT FlagReqActivo FROM ac_mastplancuenta WHERE CodCuenta = '".$_CodCuenta."'");
				$FlagReqActivo20 = getVar3("SELECT FlagReqActivo FROM ac_mastplancuenta20 WHERE CodCuenta = '".$_CodCuentaPub20."'");
				if ($FlagReqActivo20 == "S" && $_PARAMETRO['CONTPUB20'] == "S" && $_NroActivo == "") die("La cuenta <strong>".$_CodCuentaPub20."</strong> requiere el Nro. de Activo");
				if ($FlagReqActivo == "S" && $_PARAMETRO['CONTONCO'] == "S" && $_NroActivo == "") die("La cuenta <strong>".$_CodCuenta."</strong> requiere el Nro. de Activo");
				//	inserto distribucion x cuentas
				$sql = "INSERT INTO ap_obligacionescuenta
						SET
							CodProveedor = '".$CodProveedor."',
							CodTipoDocumento = '".$CodTipoDocumento."',
							NroDocumento = '".$NroDocumento."',
							Secuencia = '".$_Secuencia."',
							Linea = '1',
							Descripcion = '".$_Descripcion."',
							Monto = '".$_Monto."',
							CodCentroCosto = '".$_CodCentroCosto."',
							CodCuenta = '".$_CodCuenta."',
							CodCuentaPub20 = '".$_CodCuentaPub20."',
							cod_partida = '".$_cod_partida."',
							TipoOrden = '".$_TipoOrden."',
							NroOrden = '".$_NroOrden."',
							FlagNoAfectoIGV = '".$_FlagNoAfectoIGV."',
							Referencia = '".$_Referencia."',
							CodPersona = '".$_CodPersona."',
							NroActivo = '".$_NroActivo."',
							FlagDiferido = '".$_FlagDiferido."',
							CodOrganismo = '".$CodOrganismo."',
							Ejercicio = '".$_Ejercicio."',
							CodPresupuesto = '".$_CodPresupuesto."',
							CodFuente = '".$_CodFuente."',
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()";
				execute($sql);
			}
		}
		//	resumen
		if ($FlagNomina == "S") $Origen = "NO"; else $Origen = "OB";
		if ($FlagPresupuesto == "S" && $MontoImpuesto > 0 && $FlagAgruparIgv != 'S') {
			list($_cod_partida_igv, $_CodCuenta_igv, $_CodCuentaPub20_igv) = getPartidaCuentaFromIGV($_PARAMETRO["IGVCODIGO"]);
			$sql = "(SELECT
						SUM(Monto) AS Monto,
						cod_partida,
						CodCuenta,
						CodCuentaPub20,
						CodCentroCosto,
						Ejercicio, 
						CodPresupuesto, 
						CodFuente
					FROM ap_obligacionescuenta
					WHERE
						CodProveedor = '".$CodProveedor."' AND
						CodTipoDocumento = '".$CodTipoDocumento."' AND
						NroDocumento = '".$NroDocumento."'
					GROUP BY Ejercicio, CodPresupuesto, CodFuente, cod_partida, CodCuenta, CodCuentaPub20)
					UNION
					(SELECT
						'".($MontoImpuesto)."' AS Monto,
						'".$_cod_partida_igv."' AS cod_partida,
						'".$_CodCuenta_igv."' AS CodCuenta,
						'".$_CodCuentaPub20_igv."' AS CodCuentaPub20,
						'".$CodCentroCosto."' AS CodCentroCosto,
						'".$Ejercicio."' AS Ejercicio, 
						(SELECT pptod.CodPresupuesto
						 FROM pv_presupuestodet pptod
						 INNER JOIN pv_presupuesto ppto ON (
						 	ppto.CodOrganismo = pptod.CodOrganismo
						 	AND ppto.CodPresupuesto = pptod.CodPresupuesto
						 )
						 WHERE
						 	ppto.Ejercicio = '$Ejercicio'
						 	AND ppto.CodOrganismo = '$CodOrganismo'
						 	AND pptod.cod_partida = '$_cod_partida_igv') AS CodPresupuesto, 

						(SELECT pptod.CodFuente
						 FROM pv_presupuestodet pptod
						 INNER JOIN pv_presupuesto ppto ON (
						 	ppto.CodOrganismo = pptod.CodOrganismo
						 	AND ppto.CodPresupuesto = pptod.CodPresupuesto
						 )
						 WHERE
						 	ppto.Ejercicio = '$Ejercicio'
						 	AND ppto.CodOrganismo = '$CodOrganismo'
						 	AND pptod.cod_partida = '$_cod_partida_igv') AS CodFuente)";
		} 
		elseif ($MontoImpuesto > 0 && $FlagAgruparIgv != 'S') {
			$_cod_partida_igv = "";
			$_CodCuenta_igv = $_PARAMETRO['CTAOPIVAONCO'];
			$_CodCuentaPub20_igv = $_PARAMETRO['CTAOPIVAPUB20'];
			$sql = "(SELECT
						SUM(Monto) AS Monto,
						cod_partida,
						CodCuenta,
						CodCuentaPub20,
						CodCentroCosto,
						Ejercicio, 
						CodPresupuesto, 
						CodFuente
					FROM ap_obligacionescuenta
					WHERE
						CodProveedor = '".$CodProveedor."' AND
						CodTipoDocumento = '".$CodTipoDocumento."' AND
						NroDocumento = '".$NroDocumento."'
					GROUP BY Ejercicio, CodPresupuesto, CodFuente, cod_partida, CodCuenta, CodCuentaPub20)
					UNION
					(SELECT
						'".($MontoImpuesto)."' AS Monto,
						'".$_cod_partida_igv."' AS cod_partida,
						'".$_CodCuenta_igv."' AS CodCuenta,
						'".$_CodCuentaPub20_igv."' AS CodCuentaPub20,
						'".$CodCentroCosto."' AS CodCentroCosto,
						'".$Ejercicio."' AS Ejercicio, 
						(SELECT pptod.CodPresupuesto
						 FROM pv_presupuestodet pptod
						 INNER JOIN pv_presupuesto ppto ON (
						 	ppto.CodOrganismo = pptod.CodOrganismo
						 	AND ppto.CodPresupuesto = pptod.CodPresupuesto
						 )
						 WHERE
						 	ppto.Ejercicio = '$Ejercicio'
						 	AND ppto.CodOrganismo = '$CodOrganismo'
						 	AND pptod.cod_partida = '$_cod_partida_igv') AS CodPresupuesto, 

						(SELECT pptod.CodFuente
						 FROM pv_presupuestodet pptod
						 INNER JOIN pv_presupuesto ppto ON (
						 	ppto.CodOrganismo = pptod.CodOrganismo
						 	AND ppto.CodPresupuesto = pptod.CodPresupuesto
						 )
						 WHERE
						 	ppto.Ejercicio = '$Ejercicio'
						 	AND ppto.CodOrganismo = '$CodOrganismo'
						 	AND pptod.cod_partida = '$_cod_partida_igv') AS CodFuente)";
		}
		else {
			$sql = "SELECT
						SUM(Monto) AS Monto,
						cod_partida,
						CodCuenta,
						CodCuentaPub20,
						CodCentroCosto,
						Ejercicio, 
						CodPresupuesto, 
						CodFuente
					FROM ap_obligacionescuenta
					WHERE
						CodProveedor = '".$CodProveedor."' AND
						CodTipoDocumento = '".$CodTipoDocumento."' AND
						NroDocumento = '".$NroDocumento."'
					GROUP BY Ejercicio, CodPresupuesto, CodFuente, cod_partida, CodCuenta, CodCuentaPub20";
		}
		$field_cuentas = getRecords($sql);
		$_Secuencia = 0;
		foreach ($field_cuentas as $field_res) {
			if ($FlagCompromiso == "S") {	$_Secuencia++;
				##	valido
				list($_MontoAjustado, $_MontoCompromiso, $_PreCompromiso, $_CotizacionesAsignadas) = disponibilidadPartida2($field_res['Ejercicio'], $CodOrganismo, $field_res['cod_partida'], $field_res['CodPresupuesto'], $field_res['CodFuente']);
				$_MontoPendiente = $_PreCompromiso + $_CotizacionesAsignadas;
				$_MontoDisponible = $_MontoAjustado - $_MontoCompromiso;
				$_MontoDisponibleReal = $_MontoAjustado - ($_MontoCompromiso + $_MontoPendiente);
				##	
				if (($_MontoDisponible - $field_res['Monto']) < 0) die("Se encontr&oacute; la partida <strong>$field_res[cod_partida]</strong> sin Disponibilidad Presupuestaria");
				elseif ($field_res['CodCentroCosto'] == "") die("Debe seleccionar el Centro de Costo para todas las cuentas");
				//	inserto en distribucion compromisos
				$sql = "INSERT INTO lg_distribucioncompromisos
						SET
							CodOrganismo = '".$CodOrganismo."',
							CodProveedor = '".$CodProveedor."',
							CodTipoDocumento = '".$CodTipoDocumento."',
							NroDocumento = '".$NroDocumento."',
							Secuencia = '".$_Secuencia."',
							Linea = '1',
							CodCentroCosto = '".$field_res['CodCentroCosto']."',
							cod_partida = '".$field_res['cod_partida']."',
							Monto = '".$field_res['Monto']."',
							Anio = '".$Anio."',
							Periodo = '".$Periodo."',
							Mes = '".substr($Periodo, 5, 2)."',
							CodPresupuesto = '".$field_res['CodPresupuesto']."',
							Ejercicio = '".$field_res['Ejercicio']."',
							CodFuente = '".$field_res['CodFuente']."',
							Origen = '".$Origen."',
							Estado = 'PE',
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()";
				execute($sql);
			}
			//	inserto en la distribucion
			$sql = "INSERT INTO ap_distribucionobligacion
					SET
						CodProveedor = '".$CodProveedor."',
						CodTipoDocumento = '".$CodTipoDocumento."',
						NroDocumento = '".$NroDocumento."',
						CodCentroCosto = '".$field_res['CodCentroCosto']."',
						Monto = '".$field_res['Monto']."',
						CodCuenta = '".$field_res['CodCuenta']."',
						CodCuentaPub20 = '".$field_res['CodCuentaPub20']."',
						cod_partida = '".$field_res['cod_partida']."',
						Anio = '".$Anio."',
						Periodo = '".$Periodo."',
						CodOrganismo = '".$CodOrganismo."',
						CodPresupuesto = '".$field_res['CodPresupuesto']."',
						Ejercicio = '".$field_res['Ejercicio']."',
						CodFuente = '".$field_res['CodFuente']."',
						FlagCompromiso = '".$FlagCompromiso."',
						Origen = 'OB',
						Estado = 'PE',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		//	facturas
		for ($i=0; $i < count($facturas_NroControl); $i++) {
			$sql = "INSERT INTO ap_obligacionesfacturas
					SET
						CodProveedor = '$CodProveedor',
						CodTipoDocumento = '$CodTipoDocumento',
						NroDocumento = '$NroDocumento',
						Secuencia = '".($i+1)."',
						NroControl = '$facturas_NroControl[$i]',
						NroFactura = '$facturas_NroFactura[$i]',
						CodImpuesto = '$facturas_CodImpuesto[$i]',
						FechaFactura = '".formatFechaAMD($FechaFactura)."',
						MontoAfecto = '".setNumero($facturas_MontoAfecto[$i])."',
						MontoNoAfecto = '".setNumero($facturas_MontoNoAfecto[$i])."',
						MontoImpuesto = '".setNumero($facturas_MontoImpuesto[$i])."',
						MontoFactura = '".setNumero($facturas_MontoFactura[$i])."',
						FactorPorcentaje = '".setNumero($facturas_FactorPorcentaje[$i])."',
						MontoRetenido = '".setNumero($facturas_MontoRetenido[$i])."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		//	adelantos
		for ($i=0; $i < count($adelantos_CodAdelanto); $i++) {
			$sql = "INSERT INTO ap_obligacionesadelantos
					SET
						CodProveedor = '$CodProveedor',
						CodTipoDocumento = '$CodTipoDocumento',
						NroDocumento = '$NroDocumento',
						CodAdelanto = '$adelantos_CodAdelanto[$i]',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
			##	
			$sql = "UPDATE ap_gastoadelanto
					SET Estado = 'AC'
					WHERE CodAdelanto = '$adelantos_CodAdelanto[$i]'";
			execute($sql);
		}
		//	si viene de viatico
		if ($CodTipoDocumento == $_PARAMETRO['DOCVIAT']) {
			$sql = "UPDATE ap_viaticos
					SET
						ObligacionProveedor = '".$CodProveedor."',
						ObligacionTipoDocumento = '".$CodTipoDocumento."',
						ObligacionNroDocumento = '".$NroDocumento."',
						Estado = 'GE',
						GeneradoPor = '".$IngresadoPor."',
						FechaGenerado = '".formatFechaAMD($FechaPreparacion)."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()
					WHERE
						CodOrganismo = '".$CodOrganismo."' AND
						CodViatico = '".$CodViatico."'";
			execute($sql);
		}
		//	interfase ctas x pagar (bono de alimentacion)
		if ($opcion == 'interfase-bono-nuevo') {
			$sql = "UPDATE pr_obligacionesbono
					SET
						NroDocumento = '$NroDocumento',
						FlagTransferido = 'S',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()
					WHERE CodObligacionBono = '$CodObligacionBono'";
			execute($sql);
		}
		//	gastos directos
		elseif ($opcion == 'certificaciones-generar') {
			$sql = "UPDATE ap_certificaciones
					SET
						CodTipoDocumento = '$CodTipoDocumento',
						NroDocumento = '$NroDocumento',
						Estado = 'GE',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()
					WHERE CodCertificacion = '$CodCertificacion'";
			execute($sql);
		}
		//	gastos directos
		elseif ($opcion == 'generar-valuacion') {
			$sql = "UPDATE ob_valuaciones
					SET
						Estado = 'GE',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()
					WHERE CodValuacion = '$CodValuacion'";
			execute($sql);
		}
		//	adelanto de proveedores
		elseif ($opcion == 'adelanto-generar') {
			$sql = "UPDATE ap_gastoadelanto
					SET
						ObligacionTipoDocumento = '$CodTipoDocumento',
						ObligacionNroDocumento = '$NroDocumento',
						Estado = 'GE',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()
					WHERE CodAdelanto = '$CodAdelanto'";
			execute($sql);
		}
		//--------------------
		mysql_query("COMMIT");
	}
	//	modificar
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		
		//	actualizo obligacion
		$sql = "UPDATE ap_obligaciones
				SET
					CodProveedorPagar = '".$CodProveedorPagar."',
					NroFactura = '".$NroFactura."',
					NroControl = '".$NroControl."',
					NroCuenta = '".$NroCuenta."',
					CodTipoPago = '".$CodTipoPago."',
					CodTipoServicio = '".$CodTipoServicio."',
					MontoObligacion = '".($MontoObligacion)."',
					MontoImpuestoOtros = '".($MontoImpuestoOtros)."',
					MontoNoAfecto = '".($MontoNoAfecto)."',
					MontoAfecto = '".($MontoAfecto)."',
					MontoAdelanto = '".($MontoAdelanto)."',
					MontoImpuesto = '".($MontoImpuesto)."',
					MontoPagoParcial = '".($MontoPagoParcial)."',
					Comentarios = '".$Comentarios."',
					ComentariosAdicional = '".$ComentariosAdicional."',
					FechaRegistro = '".formatFechaAMD($FechaRegistro)."',
					FechaVencimiento = '".formatFechaAMD($FechaVencimiento)."',
					FechaRecepcion = '".formatFechaAMD($FechaRecepcion)."',
					FechaDocumento = '".formatFechaAMD($FechaDocumento)."',
					FechaProgramada = '".formatFechaAMD($FechaProgramada)."',
					FechaFactura = '".formatFechaAMD($FechaFactura)."',
					Periodo = '".$Periodo."',
					CodCentroCosto = '".$CodCentroCosto."',
					FlagGenerarPago = '".$FlagGenerarPago."',
					FlagAfectoIGV = '".$FlagAfectoIGV."',
					FlagDiferido = '".$FlagDiferido."',
					FlagPagoDiferido = '".$FlagPagoDiferido."',
					FlagCompromiso = '".$FlagCompromiso."',
					FlagPresupuesto = '".$FlagPresupuesto."',
					FlagPagoIndividual = '".$FlagPagoIndividual."',
					FlagCajaChica = '".$FlagCajaChica."',
					FlagDistribucionManual = '".$FlagDistribucionManual."',
					CodPresupuesto = '".$CodPresupuesto."',
					Ejercicio = '".$Ejercicio."',
					CodFuente = '".$CodFuente."',
					FlagNomina = '".$FlagNomina."',
					FlagFacturaPendiente = '".$FlagFacturaPendiente."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodProveedor = '".$CodProveedor."' AND
					CodTipoDocumento = '".$CodTipoDocumento."' AND
					NroDocumento = '".$NroDocumento."'";
		execute($sql);
		
		//	impuestos
		$sql = "DELETE FROM ap_obligacionesimpuesto
				WHERE
					CodProveedor = '".$CodProveedor."' AND
					CodTipoDocumento = '".$CodTipoDocumento."' AND
					NroDocumento = '".$NroDocumento."'";
		execute($sql);
		if ($detalles_impuesto != "") {
			$linea_impuesto = split(";char:tr;", $detalles_impuesto);	$_Linea=0;
			foreach ($linea_impuesto as $registro) {	$_Linea++;
				list($_CodImpuesto, $_CodConcepto, $_Signo, $_FlagImponible, $_FlagProvision, $_CodCuenta, $_CodCuentaPub20, $_MontoAfecto, $_FactorPorcentaje, $_MontoSustraendo, $_MontoAfectoSustraendo, $_MontoImpuesto, $_CodRegimenFiscal) = split(";char:td;", $registro);
				if ($_CodRegimenFiscal == 'A') $_FlagProvision = 'A';
				//	inserto
				$sql = "INSERT INTO ap_obligacionesimpuesto
						SET
							CodProveedor = '".$CodProveedor."',
							CodTipoDocumento = '".$CodTipoDocumento."',
							NroDocumento = '".$NroDocumento."',
							Linea = '".$_Linea."',
							CodImpuesto = '".$_CodImpuesto."',
							CodConcepto = '".$_CodConcepto."',
							FactorPorcentaje = '".$_FactorPorcentaje."',
							MontoImpuesto = '".$_MontoImpuesto."',
							MontoAfecto = '".$_MontoAfecto."',
							CodCuenta = '".$_CodCuenta."',
							CodCuentaPub20 = '".$_CodCuentaPub20."',
							FlagProvision = '".$_FlagProvision."',
							MontoSustraendo = '".$_MontoSustraendo."',
							MontoAfectoSustraendo = '".$_MontoAfectoSustraendo."',
							CodRegimenFiscal = '".$_CodRegimenFiscal."',
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()";
				execute($sql);
			}
		}
		
		//	distribucion
		$sql = "DELETE FROM ap_obligacionescuenta
				WHERE
					CodProveedor = '".$CodProveedor."' AND
					CodTipoDocumento = '".$CodTipoDocumento."' AND
					NroDocumento = '".$NroDocumento."'";
		execute($sql);
		if ($detalles_distribucion != "") {
			$linea_distribucion = split(";char:tr;", $detalles_distribucion);	$_Secuencia=0;
			foreach ($linea_distribucion as $registro) {	$_Secuencia++;
				list($_cod_partida, $_CodCuenta, $_CodCuentaPub20, $_CodCentroCosto, $_FlagNoAfectoIGV, $_Monto, $_CategoriaProg, $_Ejercicio, $_CodPresupuesto, $_CodFuente, $_TipoOrden, $_NroOrden, $_Referencia, $_Descripcion, $_CodPersona, $_NroActivo, $_FlagDiferido) = split(";char:td;", $registro);
				//	inserto distribucion x cuentas
				$sql = "INSERT INTO ap_obligacionescuenta
						SET
							CodProveedor = '".$CodProveedor."',
							CodTipoDocumento = '".$CodTipoDocumento."',
							NroDocumento = '".$NroDocumento."',
							Secuencia = '".$_Secuencia."',
							Linea = '1',
							Descripcion = '".$_Descripcion."',
							Monto = '".$_Monto."',
							CodCentroCosto = '".$_CodCentroCosto."',
							CodCuenta = '".$_CodCuenta."',
							CodCuentaPub20 = '".$_CodCuentaPub20."',
							cod_partida = '".$_cod_partida."',
							TipoOrden = '".$_TipoOrden."',
							NroOrden = '".$_NroOrden."',
							FlagNoAfectoIGV = '".$_FlagNoAfectoIGV."',
							Referencia = '".$_Referencia."',
							CodPersona = '".$_CodPersona."',
							NroActivo = '".$_NroActivo."',
							FlagDiferido = '".$_FlagDiferido."',
							CodOrganismo = '".$CodOrganismo."',
							Ejercicio = '".$_Ejercicio."',
							CodPresupuesto = '".$_CodPresupuesto."',
							CodFuente = '".$_CodFuente."',
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()";
				execute($sql);
			}
		}
		
		//	resumen
		if ($FlagNomina == "S") $Origen = "NO"; else $Origen = "OB";
		$sql = "DELETE FROM ap_distribucionobligacion
				WHERE
					CodProveedor = '".$CodProveedor."' AND
					CodTipoDocumento = '".$CodTipoDocumento."' AND
					NroDocumento = '".$NroDocumento."'";
		execute($sql);
		if ($FlagCompromiso == "S") {
			$sql = "DELETE FROM lg_distribucioncompromisos
					WHERE
						Anio = '".$Anio."' AND
						CodOrganismo = '".$CodOrganismo."' AND
						CodProveedor = '".$CodProveedor."' AND
						CodTipoDocumento = '".$CodTipoDocumento."' AND
						NroDocumento = '".$NroDocumento."'";
			execute($sql);
		}
		if ($FlagPresupuesto == "S" && $MontoImpuesto > 0 && $FlagAgruparIgv != 'S') {
			list($_cod_partida_igv, $_CodCuenta_igv, $_CodCuentaPub20_igv) = getPartidaCuentaFromIGV($_PARAMETRO["IGVCODIGO"]);
			$sql = "(SELECT
						SUM(Monto) AS Monto,
						cod_partida,
						CodCuenta,
						CodCuentaPub20,
						CodCentroCosto,
						Ejercicio, 
						CodPresupuesto, 
						CodFuente
					FROM ap_obligacionescuenta
					WHERE
						CodProveedor = '".$CodProveedor."' AND
						CodTipoDocumento = '".$CodTipoDocumento."' AND
						NroDocumento = '".$NroDocumento."'
					GROUP BY Ejercicio, CodPresupuesto, CodFuente, cod_partida, CodCuenta, CodCuentaPub20)
					UNION
					(SELECT
						'".($MontoImpuesto)."' AS Monto,
						'".$_cod_partida_igv."' AS cod_partida,
						'".$_CodCuenta_igv."' AS CodCuenta,
						'".$_CodCuentaPub20_igv."' AS CodCuentaPub20,
						'".$CodCentroCosto."' AS CodCentroCosto,
						'".$Ejercicio."' AS Ejercicio, 
						(SELECT pptod.CodPresupuesto
						 FROM pv_presupuestodet pptod
						 INNER JOIN pv_presupuesto ppto ON (
						 	ppto.CodOrganismo = pptod.CodOrganismo
						 	AND ppto.CodPresupuesto = pptod.CodPresupuesto
						 )
						 WHERE
						 	ppto.Ejercicio = '$Ejercicio'
						 	AND ppto.CodOrganismo = '$CodOrganismo'
						 	AND pptod.cod_partida = '$_cod_partida_igv') AS CodPresupuesto, 

						(SELECT pptod.CodFuente
						 FROM pv_presupuestodet pptod
						 INNER JOIN pv_presupuesto ppto ON (
						 	ppto.CodOrganismo = pptod.CodOrganismo
						 	AND ppto.CodPresupuesto = pptod.CodPresupuesto
						 )
						 WHERE
						 	ppto.Ejercicio = '$Ejercicio'
						 	AND ppto.CodOrganismo = '$CodOrganismo'
						 	AND pptod.cod_partida = '$_cod_partida_igv') AS CodFuente)";
		} 
		elseif ($MontoImpuesto > 0 && $FlagAgruparIgv != 'S') {
			$_cod_partida_igv = "";
			$_CodCuenta_igv = $_PARAMETRO['CTAOPIVAONCO'];
			$_CodCuentaPub20_igv = $_PARAMETRO['CTAOPIVAPUB20'];
			$sql = "(SELECT
						SUM(Monto) AS Monto,
						cod_partida,
						CodCuenta,
						CodCuentaPub20,
						CodCentroCosto,
						Ejercicio, 
						CodPresupuesto, 
						CodFuente
					FROM ap_obligacionescuenta
					WHERE
						CodProveedor = '".$CodProveedor."' AND
						CodTipoDocumento = '".$CodTipoDocumento."' AND
						NroDocumento = '".$NroDocumento."'
					GROUP BY Ejercicio, CodPresupuesto, CodFuente, cod_partida, CodCuenta, CodCuentaPub20)
					UNION
					(SELECT
						'".($MontoImpuesto)."' AS Monto,
						'".$_cod_partida_igv."' AS cod_partida,
						'".$_CodCuenta_igv."' AS CodCuenta,
						'".$_CodCuentaPub20_igv."' AS CodCuentaPub20,
						'".$CodCentroCosto."' AS CodCentroCosto,
						'".$Ejercicio."' AS Ejercicio, 
						(SELECT pptod.CodPresupuesto
						 FROM pv_presupuestodet pptod
						 INNER JOIN pv_presupuesto ppto ON (
						 	ppto.CodOrganismo = pptod.CodOrganismo
						 	AND ppto.CodPresupuesto = pptod.CodPresupuesto
						 )
						 WHERE
						 	ppto.Ejercicio = '$Ejercicio'
						 	AND ppto.CodOrganismo = '$CodOrganismo'
						 	AND pptod.cod_partida = '$_cod_partida_igv') AS CodPresupuesto, 

						(SELECT pptod.CodFuente
						 FROM pv_presupuestodet pptod
						 INNER JOIN pv_presupuesto ppto ON (
						 	ppto.CodOrganismo = pptod.CodOrganismo
						 	AND ppto.CodPresupuesto = pptod.CodPresupuesto
						 )
						 WHERE
						 	ppto.Ejercicio = '$Ejercicio'
						 	AND ppto.CodOrganismo = '$CodOrganismo'
						 	AND pptod.cod_partida = '$_cod_partida_igv') AS CodFuente)";
		}
		else {
			$sql = "SELECT
						SUM(Monto) AS Monto,
						cod_partida,
						CodCuenta,
						CodCuentaPub20,
						CodCentroCosto,
						Ejercicio, 
						CodPresupuesto, 
						CodFuente
					FROM ap_obligacionescuenta
					WHERE
						CodProveedor = '".$CodProveedor."' AND
						CodTipoDocumento = '".$CodTipoDocumento."' AND
						NroDocumento = '".$NroDocumento."'
					GROUP BY Ejercicio, CodPresupuesto, CodFuente, cod_partida, CodCuenta, CodCuentaPub20";
		}
		$field_cuentas = getRecords($sql);
		$_Secuencia = 0;
		foreach ($field_cuentas as $field_res) {
			if ($FlagCompromiso == "S") {	$_Secuencia++;
				//	inserto en distribucion compromisos
				$sql = "INSERT INTO lg_distribucioncompromisos
						SET
							CodOrganismo = '".$CodOrganismo."',
							CodProveedor = '".$CodProveedor."',
							CodTipoDocumento = '".$CodTipoDocumento."',
							NroDocumento = '".$NroDocumento."',
							Secuencia = '".$_Secuencia."',
							Linea = '1',
							CodCentroCosto = '".$field_res['CodCentroCosto']."',
							cod_partida = '".$field_res['cod_partida']."',
							Monto = '".$field_res['Monto']."',
							Anio = '".$Anio."',
							Periodo = '".$Periodo."',
							Mes = '".substr($Periodo, 5, 2)."',
							CodPresupuesto = '".$field_res['CodPresupuesto']."',
							Ejercicio = '".$field_res['Ejercicio']."',
							CodFuente = '".$field_res['CodFuente']."',
							Origen = '".$Origen."',
							Estado = 'PE',
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()";
				execute($sql);
			}
			
			//	inserto en la distribucion
			$sql = "INSERT INTO ap_distribucionobligacion
					SET
						CodProveedor = '".$CodProveedor."',
						CodTipoDocumento = '".$CodTipoDocumento."',
						NroDocumento = '".$NroDocumento."',
						CodCentroCosto = '".$field_res['CodCentroCosto']."',
						Monto = '".$field_res['Monto']."',
						CodCuenta = '".$field_res['CodCuenta']."',
						CodCuentaPub20 = '".$field_res['CodCuentaPub20']."',
						cod_partida = '".$field_res['cod_partida']."',
						Anio = '".$Anio."',
						Periodo = '".$Periodo."',
						CodPresupuesto = '".$field_res['CodPresupuesto']."',
						Ejercicio = '".$field_res['Ejercicio']."',
						CodFuente = '".$field_res['CodFuente']."',
						FlagCompromiso = '".$FlagCompromiso."',
						Origen = 'OB',
						Estado = 'PE',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		//	facturas
		$sql = "DELETE FROM ap_obligacionesfacturas WHERE CodProveedor = '$CodProveedor' AND CodTipoDocumento = '$CodTipoDocumento' AND NroDocumento = '$NroDocumento'";
		execute($sql);
		for ($i=0; $i < count($facturas_NroControl); $i++) {
			$sql = "INSERT INTO ap_obligacionesfacturas
					SET
						CodProveedor = '$CodProveedor',
						CodTipoDocumento = '$CodTipoDocumento',
						NroDocumento = '$NroDocumento',
						Secuencia = '".($i+1)."',
						NroControl = '$facturas_NroControl[$i]',
						NroFactura = '$facturas_NroFactura[$i]',
						CodImpuesto = '$facturas_CodImpuesto[$i]',
						FechaFactura = '".formatFechaAMD($FechaFactura)."',
						MontoAfecto = '".setNumero($facturas_MontoAfecto[$i])."',
						MontoNoAfecto = '".setNumero($facturas_MontoNoAfecto[$i])."',
						MontoImpuesto = '".setNumero($facturas_MontoImpuesto[$i])."',
						MontoFactura = '".setNumero($facturas_MontoFactura[$i])."',
						FactorPorcentaje = '".setNumero($facturas_FactorPorcentaje[$i])."',
						MontoRetenido = '".setNumero($facturas_MontoRetenido[$i])."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		mysql_query("COMMIT");
	}
	//	revisar
	elseif ($accion == "revisar") {
		mysql_query("BEGIN");
		//	actualizo obligacion
		$sql = "UPDATE ap_obligaciones
				SET
					Estado = 'RV',
					RevisadoPor = '".$RevisadoPor."',
					FechaRevision = '".formatFechaAMD($FechaRevision)."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodProveedor = '".$CodProveedor."' AND
					CodTipoDocumento = '".$CodTipoDocumento."' AND
					NroDocumento = '".$NroDocumento."'";
		execute($sql);
		//	actualizo distribucion
		if ($FlagCompromiso == "S") {
			$sql = "SELECT
						dc.CodOrganismo,
						dc.CodPresupuesto,
						dc.CodFuente,
						dc.cod_partida,
						dc.Monto,
						ppto.Ejercicio
					FROM
						lg_distribucioncompromisos dc
						LEFT JOIN pv_presupuesto ppto ON (
							ppto.CodOrganismo = dc.CodOrganismo
							AND ppto.CodPresupuesto = dc.CodPresupuesto
						)
					WHERE
						dc.Anio = '$Anio' AND
						dc.CodOrganismo = '$CodOrganismo' AND
						dc.CodProveedor = '$CodProveedor' AND
						dc.CodTipoDocumento = '$CodTipoDocumento' AND
						dc.NroDocumento = '$NroDocumento'";
			$field_distribucion = getRecords($sql);
			foreach ($field_distribucion as $f) {
				##	valido
				list($_MontoAjustado, $_MontoCompromiso, $_PreCompromiso, $_CotizacionesAsignadas) = disponibilidadPartida2($f['Ejercicio'], $f['CodOrganismo'], $f['cod_partida'], $f['CodPresupuesto'], $f['CodFuente']);
				$_MontoPendiente = $_PreCompromiso + $_CotizacionesAsignadas;
				$_MontoDisponible = $_MontoAjustado - $_MontoCompromiso;
				$_MontoDisponibleReal = $_MontoAjustado - ($_MontoCompromiso + $_MontoPendiente);
				##	
				if (($_MontoDisponible - $f['Monto']) < 0) die("Se encontr&oacute; la partida <strong>$f[cod_partida]</strong> sin Disponibilidad Presupuestaria <BR> $_MontoDisponible = $_MontoAjustado - $_MontoCompromiso;");
			}
			##	
			$sql = "UPDATE lg_distribucioncompromisos
					SET
						FechaEjecucion = '".formatFechaAMD($FechaRevision)."',
						Periodo = '".substr(formatFechaAMD($FechaRevision),0,7)."',
						Estado = 'CO',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()
					WHERE
						Anio = '".$Anio."' AND
						CodOrganismo = '".$CodOrganismo."' AND
						CodProveedor = '".$CodProveedor."' AND
						CodTipoDocumento = '".$CodTipoDocumento."' AND
						NroDocumento = '".$NroDocumento."'";
			execute($sql);
		}
		$sql = "UPDATE ap_distribucionobligacion
				SET
					FechaEjecucion = '".formatFechaAMD($FechaRevision)."',
					Periodo = '".substr(formatFechaAMD($FechaRevision),0,7)."',
					Estado = 'CA',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodProveedor = '".$CodProveedor."' AND
					CodTipoDocumento = '".$CodTipoDocumento."' AND
					NroDocumento = '".$NroDocumento."'";
		execute($sql);
		mysql_query("COMMIT");
	}
	//	aprobar
	elseif ($accion == "aprobar") {
		mysql_query("BEGIN");
		//	documentos
		$sql = "SELECT *
				FROM ap_documentos
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					CodProveedor = '".$CodProveedor."' AND
					ObligacionTipoDocumento = '".$CodTipoDocumento."' AND
					ObligacionNroDocumento = '".$NroDocumento."'";
		$fdocumentos = getRecords($sql);
		$linea=0;
		foreach ($fdocumentos as $field_documentos) {
			//	actualizo (orden)
			if ($field_documentos['ReferenciaTipoDocumento'] == "OC") {
				$sql = "UPDATE lg_ordencompra 
						SET
							MontoPendiente = (MontoPendiente - (".floatval($MontoAfecto)." + ".floatval($MontoNoAfecto)." + ".floatval($MontoImpuesto).")),
							MontoPagado = (MontoPagado + (".floatval($MontoAfecto)." + ".floatval($MontoNoAfecto)." + ".floatval($MontoImpuesto).")),
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()
						WHERE
							Anio = '".$Anio."' AND
							CodOrganismo = '".$CodOrganismo."' AND
							NroOrden = '".$field_documentos['ReferenciaNroDocumento']."'";
				execute($sql);
			} else {
				$sql = "UPDATE lg_ordenservicio
						SET
							MontoPendiente = (MontoPendiente - (".floatval($MontoAfecto)." + ".floatval($MontoNoAfecto)." + ".floatval($MontoImpuesto).")),
							MontoGastado = (MontoGastado + (".floatval($MontoAfecto)." + ".floatval($MontoNoAfecto)." + ".floatval($MontoImpuesto).")),
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()
						WHERE
							Anio = '".$Anio."' AND
							CodOrganismo = '".$CodOrganismo."' AND
							NroOrden = '".$field_documentos['ReferenciaNroDocumento']."'";
				execute($sql);
			}
		}
		//	actualizo obligacion
		$sql = "UPDATE ap_obligaciones
				SET
					Estado = 'AP',
					AprobadoPor = '".$AprobadoPor."',
					FechaAprobado = '".formatFechaAMD($FechaAprobado)."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodProveedor = '".$CodProveedor."' AND
					CodTipoDocumento = '".$CodTipoDocumento."' AND
					NroDocumento = '".$NroDocumento."'";
		execute($sql);
		//	inserto (orden de pago)
		$NroOrden = getCodigo("ap_ordenpago", "NroOrden", 10, "CodOrganismo", $CodOrganismo, "Anio", $Anio);
		$sql = "INSERT INTO ap_ordenpago
				SET
					Anio = '".$Anio."',
					CodOrganismo = '".$CodOrganismo."',
					NroOrden = '".$NroOrden."',
					CodAplicacion = 'AP',
					CodProveedor = '".$CodProveedor."',
					CodTipoDocumento = '".$CodTipoDocumento."',
					NroDocumento = '".$NroDocumento."',
					FechaDocumento = '".formatFechaAMD($FechaDocumento)."',
					FechaVencimiento = '".formatFechaAMD($FechaVencimiento)."',
					FechaOrdenPago = '".formatFechaAMD($FechaAprobado)."',
					FechaVencimientoReal = '".formatFechaAMD($FechaVencimiento)."',
					FechaProgramada = '".formatFechaAMD($FechaProgramada)."',
					FechaRevisado = '".formatFechaAMD($FechaRevision)."',
					CodProveedorPagar = '".$CodProveedorPagar."',
					NomProveedorPagar = '".changeUrl($NomProveedorPagar)."',
					Concepto = '".$Comentarios."',
					NroCuenta = '".$NroCuenta."',
					CodTipoPago = '".$CodTipoPago."',
					MontoTotal = '".$MontoObligacion."',
					NroRegistro = '".$NroRegistro."',
					FlagPagoDiferido = '".$FlagPagoDiferido."',
					CodCentroCosto = '".$CodCentroCosto."',
					CodSistemaFuente = '".$field_fuente['CodSistemaFuente']."',
					Periodo = '".substr(formatFechaAMD($FechaAprobado),0,7)."',
					FechaPreparado = '".formatFechaAMD($FechaPreparacion)."',
					PreparadoPor = '".$IngresadoPor."',
					RevisadoPor = '".$_PARAMETRO["FIRMAOP3"]."',
					AprobadoPor = '".$_PARAMETRO["FIRMAOP2"]."',
					Estado = 'PE',
					Ejercicio = '".$Ejercicio."',
					CodPresupuesto = '".$CodPresupuesto."',
					CodFuente = '".$CodFuente."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		//	inserto (orden de pago detalles)
		$sql = "SELECT *
				FROM ap_distribucionobligacion
				WHERE
					CodProveedor = '".$CodProveedor."' AND
					CodTipoDocumento = '".$CodTipoDocumento."' AND
					NroDocumento = '".$NroDocumento."'";
		$fdistribucion = getRecords($sql);
		$Linea=0;
		foreach ($fdistribucion as $field_distribucion) {
			$Linea++;
			$sql = "INSERT INTO ap_ordenpagodistribucion
					SET
						CodOrganismo = '".$CodOrganismo."',
						NroOrden = '".$NroOrden."',
						CodProveedor = '".$CodProveedor."',
						CodTipoDocumento = '".$CodTipoDocumento."',
						NroDocumento = '".$NroDocumento."',
						Linea = '".$Linea."',
						CodCentroCosto = '".$CodCentroCosto."',
						Monto = '".$field_distribucion['Monto']."',
						MontoPagado = '".$field_distribucion['Monto']."',
						CodCuenta = '".$field_distribucion['CodCuenta']."',
						CodCuentaPub20 = '".$field_distribucion['CodCuentaPub20']."',
						cod_partida = '".$field_distribucion['cod_partida']."',
						Anio = '".$Anio."',
						Periodo = '".substr(formatFechaAMD($FechaAprobado),0,7)."',
						CodPresupuesto = '".$field_distribucion['CodPresupuesto']."',
						Ejercicio = '".$field_distribucion['Ejercicio']."',
						CodFuente = '".$field_distribucion['CodFuente']."',
						Origen = 'OP',
						Estado = 'PE',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}

		if ($FlagPresupuesto != "S" && $MontoImpuesto > 0 && $FlagAgruparIgv != 'S') {
			$Linea=0;
			foreach ($fdistribucion as $field_distribucion) {
				$Linea++;
				##	
				if ($FlagPresupuesto != "S" && $MontoImpuesto > 0 && $FlagAgruparIgv != 'S') {
					if ($_PARAMETRO['CONTONCO'] == "S" && $FlagGenerarPago == "S") {
						$sql = "INSERT INTO ap_ordenpagocontabilidad
								SET
									Anio = '".$Anio."',
									CodOrganismo = '".$CodOrganismo."',
									NroOrden = '".$NroOrden."',
									CodContabilidad = 'T',
									Secuencia = '".$Linea."',
									CodCuenta = '".$field_distribucion['CodCuenta']."',
									Monto = '".$field_distribucion['Monto']."',
									UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
									UltimaFecha = NOW()";
						execute($sql);
					}
					if ($_PARAMETRO['CONTPUB20'] == "S" && $FlagGenerarPago == "S") {
						$sql = "INSERT INTO ap_ordenpagocontabilidad
								SET
									Anio = '".$Anio."',
									CodOrganismo = '".$CodOrganismo."',
									NroOrden = '".$NroOrden."',
									CodContabilidad = 'F',
									Secuencia = '".$Linea."',
									CodCuenta = '".$field_distribucion['CodCuentaPub20']."',
									Monto = '".$field_distribucion['Monto']."',
									UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
									UltimaFecha = NOW()";
						execute($sql);
					}
				}
			}
		} else {
			if ($_PARAMETRO['CONTONCO'] == "S" && $FlagGenerarPago == "S") {
				$Secuencia = 0;
				//	inserto (orden de pago contabilidad)
				$sql = "(SELECT
							td.CodCuentaProv AS CodCuenta,
							oc.ReferenciaTipoDocumento AS TipoOrden,
							oc.ReferenciaNroDocumento AS NroOrden,
							pc.Descripcion AS NomCuenta,
							(oc.MontoObligacion) AS MontoVoucher,
							pc.TipoSaldo,
							'01' AS Orden,
							'Haber' AS Columna
						 FROM
							ap_obligaciones oc
							INNER JOIN ap_tipodocumento td ON (oc.CodTipoDocumento = td.CodTipoDocumento)
							INNER JOIN ac_mastplancuenta pc ON (td.CodCuentaProv = pc.CodCuenta)
						 WHERE
							oc.CodProveedor = '".$CodProveedor."' AND
							oc.CodTipoDocumento = '".$CodTipoDocumento."' AND
							oc.NroDocumento = '".$NroDocumento."'
						 GROUP BY CodCuenta)
						UNION
						(SELECT
							(SELECT CodCuenta FROM mastimpuestos WHERE CodImpuesto = '".$_PARAMETRO["IGVCODIGO"]."') AS CodCuenta,
							oc.ReferenciaTipoDocumento AS TipoOrden,
							oc.ReferenciaNroDocumento AS NroOrden,
							(SELECT pc2.Descripcion
							 FROM
								mastimpuestos i2
								INNER JOIN ac_mastplancuenta pc2 ON (i2.CodCuenta = pc2.CodCuenta)
							 WHERE CodImpuesto = '".$_PARAMETRO["IGVCODIGO"]."') AS NomCuenta,
							oc.MontoImpuesto AS MontoVoucher,
							(SELECT pc2.TipoSaldo
							 FROM
								mastimpuestos i2
								INNER JOIN ac_mastplancuenta pc2 ON (i2.CodCuenta = pc2.CodCuenta)
							 WHERE CodImpuesto = '".$_PARAMETRO["IGVCODIGO"]."') AS TipoSaldo,
							'02' AS Orden,
							'Debe' AS Columna
						 FROM ap_obligaciones oc
						 WHERE
							oc.CodProveedor = '".$CodProveedor."' AND
							oc.CodTipoDocumento = '".$CodTipoDocumento."' AND
							oc.NroDocumento = '".$NroDocumento."' AND
							oc.MontoImpuesto > 0
						 GROUP BY CodCuenta)
						UNION
						(SELECT
							oc.CodCuenta,
							o.ReferenciaTipoDocumento AS TipoOrden,
							o.ReferenciaNroDocumento AS NroOrden,
							pc.Descripcion AS NomCuenta,
							ABS(SUM(oc.MontoImpuesto)) AS MontoVoucher,
							pc.TipoSaldo,
							'03' AS Orden,
							'Haber' AS Columna
						 FROM
							ap_obligacionesimpuesto oc
							INNER JOIN ap_obligaciones o ON (oc.CodProveedor = o.CodProveedor AND
															 oc.CodTipoDocumento = o.CodTipoDocumento AND
															 oc.NroDocumento = o.NroDocumento)
							INNER JOIN ac_mastplancuenta pc ON (oc.CodCuenta = pc.CodCuenta)
						 WHERE
							oc.FlagProvision = 'N' AND
							oc.CodProveedor = '".$CodProveedor."' AND
							oc.CodTipoDocumento = '".$CodTipoDocumento."' AND
							oc.NroDocumento = '".$NroDocumento."'
						 GROUP BY oc.CodCuenta)
						UNION
						(SELECT
							oc.CodCuenta,
							oc.TipoOrden,
							oc.NroOrden,
							pc.Descripcion AS NomCuenta,
							SUM(oc.Monto) AS MontoVoucher,
							pc.TipoSaldo,
							'04' AS Orden,
							'Debe' AS Columna
						 FROM
							ap_obligacionescuenta oc
							INNER JOIN ac_mastplancuenta pc ON (oc.CodCuenta = pc.CodCuenta)
						 WHERE
							oc.CodProveedor = '".$CodProveedor."' AND
							oc.CodTipoDocumento = '".$CodTipoDocumento."' AND
							oc.NroDocumento = '".$NroDocumento."'
						 GROUP BY oc.CodCuenta)
						ORDER BY CodCuenta";
				$fdet = getRecords($sql);
				$Secuencia=0;
				foreach ($fdet as $field_det) {
					$Secuencia++;
					if ($field_det['Orden'] == "01") {
						$sql = "SELECT ABS(SUM(oi1.MontoImpuesto)) AS MontoRetencion
								FROM
									ap_obligacionesimpuesto oi1
									INNER JOIN ap_obligaciones o1 ON (oi1.CodProveedor = o1.CodProveedor AND
																	  oi1.CodTipoDocumento = o1.CodTipoDocumento AND
																	  oi1.NroDocumento = o1.NroDocumento)
									INNER JOIN mastimpuestos i1 ON (oi1.CodImpuesto = i1.CodImpuesto)
									INNER JOIN ac_mastplancuenta pc1 ON (i1.CodCuenta = pc1.CodCuenta)
								WHERE
									oi1.FlagProvision = 'P' AND
									oi1.CodProveedor = '".$CodProveedor."' AND
									oi1.CodTipoDocumento = '".$CodTipoDocumento."' AND
									oi1.NroDocumento = '".$NroDocumento."'
								GROUP BY i1.FlagProvision, oi1.CodProveedor, oi1.CodTipoDocumento, oi1.NroDocumento";
						$field_orden1 = getRecord($sql);
						$Monto = $field_det['MontoVoucher'] + $field_orden1['MontoRetencion'];
					} else $Monto = $field_det['MontoVoucher'];
					
					if ($field_det['Columna'] == "Haber") {
						$Monto = abs($Monto) * (-1);
						$Debitos += $Monto;
					} else {
						$style = "";
						$Monto = abs($Monto);
						$Creditos += $Monto;
					}
					$sql = "INSERT INTO ap_ordenpagocontabilidad
							SET
								Anio = '".$Anio."',
								CodOrganismo = '".$CodOrganismo."',
								NroOrden = '".$NroOrden."',
								CodContabilidad = 'T',
								Secuencia = '".$Secuencia."',
								CodCuenta = '".$field_det['CodCuenta']."',
								Monto = '".$Monto."',
								UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
								UltimaFecha = NOW()";
					execute($sql);
				}
			}
			if ($_PARAMETRO['CONTPUB20'] == "S" && $FlagGenerarPago == "S") {
				$Secuencia = 0;
				//	impuestos FlagProvision=N
				$sql = "SELECT SUM(oi1.MontoImpuesto) AS Monto
						FROM
							ap_obligacionesimpuesto oi1
							INNER JOIN ap_obligaciones o1 ON (oi1.CodProveedor = o1.CodProveedor AND
															  oi1.CodTipoDocumento = o1.CodTipoDocumento AND
															  oi1.NroDocumento = o1.NroDocumento)
						WHERE
							oi1.FlagProvision = 'N' AND
							oi1.CodProveedor = '".$CodProveedor."' AND
							oi1.CodTipoDocumento = '".$CodTipoDocumento."' AND
							oi1.NroDocumento = '".$NroDocumento."'
						GROUP BY oi1.FlagProvision, oi1.CodProveedor, oi1.CodTipoDocumento, oi1.NroDocumento";
				$field_impueston = getRecord($sql);
				//	impuestos FlagProvision=P
				$sql = "SELECT SUM(oi1.MontoImpuesto) AS Monto
						FROM
							ap_obligacionesimpuesto oi1
							INNER JOIN ap_obligaciones o1 ON (oi1.CodProveedor = o1.CodProveedor AND
															  oi1.CodTipoDocumento = o1.CodTipoDocumento AND
															  oi1.NroDocumento = o1.NroDocumento)
						WHERE
							oi1.FlagProvision = 'P' AND
							oi1.CodProveedor = '".$CodProveedor."' AND
							oi1.CodTipoDocumento = '".$CodTipoDocumento."' AND
							oi1.NroDocumento = '".$NroDocumento."'
						GROUP BY oi1.FlagProvision, oi1.CodProveedor, oi1.CodTipoDocumento, oi1.NroDocumento";
				$field_impuestop = getRecord($sql);
				##
				$sql = "(SELECT
							td.CodCuentaProvPub20 AS CodCuenta,
							oc.ReferenciaTipoDocumento AS TipoOrden,
							oc.ReferenciaNroDocumento AS NroOrden,
							pc.Descripcion AS NomCuenta,
							(oc.MontoObligacion + ".abs($field_impueston['Monto'])." + ".abs($field_impuestop['Monto']).") AS MontoVoucher,
							pc.TipoSaldo,
							'01' AS Orden,
							'Debe' AS Columna
						 FROM
							ap_obligaciones oc
							INNER JOIN ap_tipodocumento td ON (oc.CodTipoDocumento = td.CodTipoDocumento)
							INNER JOIN ac_mastplancuenta20 pc ON (td.CodCuentaProvPub20 = pc.CodCuenta)
						 WHERE
							oc.CodProveedor = '".$CodProveedor."' AND
							oc.CodTipoDocumento = '".$CodTipoDocumento."' AND
							oc.NroDocumento = '".$NroDocumento."'
						 GROUP BY CodCuenta)
						UNION
						(SELECT
							p.CtaOrdPagoPub20 AS CodCuenta,
							oc.TipoOrden,
							oc.NroOrden,
							pc.Descripcion AS NomCuenta,
							(SUM(oc.Monto) - ".abs($field_impueston['Monto']).") AS MontoVoucher,
							pc.TipoSaldo,
							'02' AS Orden,
							'Haber' AS Columna
						 FROM
							ap_obligacionescuenta oc
							INNER JOIN pv_partida p ON (p.cod_partida = oc.cod_partida)
							INNER JOIN ac_mastplancuenta20 pc ON (pc.CodCuenta = p.CtaOrdPagoPub20)
							INNER JOIN ap_obligaciones o ON (o.CodProveedor = oc.CodProveedor AND
															 o.CodTipoDocumento = oc.CodTipoDocumento AND
															 o.NroDocumento = oc.NroDocumento)
							INNER JOIN ap_tipodocumento td ON (td.CodTipoDocumento = o.CodTipoDocumento AND td.FlagProvision = 'S')
						 WHERE
							oc.CodProveedor = '".$CodProveedor."' AND
							oc.CodTipoDocumento = '".$CodTipoDocumento."' AND
							oc.NroDocumento = '".$NroDocumento."'
						 GROUP BY CodCuenta)
						UNION
						(SELECT
							(SELECT pc2.CodCuenta
							 FROM
								mastimpuestos i2
								INNER JOIN pv_partida pv2 ON (pv2.cod_partida = i2.cod_partida)
								INNER JOIN ac_mastplancuenta20 pc2 ON (pc2.CodCuenta = pv2.CtaOrdPagoPub20)
							 WHERE CodImpuesto = '".$_PARAMETRO["IGVCODIGO"]."') AS CodCuenta,
							oc.ReferenciaTipoDocumento AS TipoOrden,
							oc.ReferenciaNroDocumento AS NroOrden,
							(SELECT pc2.Descripcion
							 FROM
								mastimpuestos i2
								INNER JOIN pv_partida pv2 ON (pv2.cod_partida = i2.cod_partida)
								INNER JOIN ac_mastplancuenta20 pc2 ON (pc2.CodCuenta = pv2.CtaOrdPagoPub20)
							 WHERE CodImpuesto = '".$_PARAMETRO["IGVCODIGO"]."') AS NomCuenta,
							oc.MontoImpuesto AS MontoVoucher,
							(SELECT pc2.TipoSaldo
							 FROM
								mastimpuestos i2
								INNER JOIN pv_partida pv2 ON (pv2.cod_partida = i2.cod_partida)
								INNER JOIN ac_mastplancuenta20 pc2 ON (pc2.CodCuenta = pv2.CtaOrdPagoPub20)
							 WHERE CodImpuesto = '".$_PARAMETRO["IGVCODIGO"]."') AS TipoSaldo,
							'03' AS Orden,
							'Haber' AS Columna
						 FROM ap_obligaciones oc
						 WHERE
							oc.CodProveedor = '".$CodProveedor."' AND
							oc.CodTipoDocumento = '".$CodTipoDocumento."' AND
							oc.NroDocumento = '".$NroDocumento."' AND
							oc.MontoImpuesto > 0
						 GROUP BY CodCuenta)
						UNION
						(SELECT
							oc.CodCuentaPub20 AS CodCuenta,
							o.ReferenciaTipoDocumento AS TipoOrden,
							o.ReferenciaNroDocumento AS NroOrden,
							pc.Descripcion AS NomCuenta,
							ABS(SUM(oc.MontoImpuesto)) AS MontoVoucher,
							pc.TipoSaldo,
							'04' AS Orden,
							'Haber' AS Columna
						 FROM
							ap_obligacionesimpuesto oc
							INNER JOIN ap_obligaciones o ON (oc.CodProveedor = o.CodProveedor AND
															 oc.CodTipoDocumento = o.CodTipoDocumento AND
															 oc.NroDocumento = o.NroDocumento)
							INNER JOIN ac_mastplancuenta20 pc ON (oc.CodCuentaPub20 = pc.CodCuenta)
						 WHERE
							oc.FlagProvision = 'N' AND
							oc.CodProveedor = '".$CodProveedor."' AND
							oc.CodTipoDocumento = '".$CodTipoDocumento."' AND
							oc.NroDocumento = '".$NroDocumento."'
						 GROUP BY CodCuenta)
						ORDER BY Columna DESC, Orden, CodCuenta";
				$fdet = getRecords($sql);
				foreach ($fdet as $field_det) {
					$Monto = $field_det['MontoVoucher'];
					if ($field_det['Columna'] == "Haber") {
						$style = " color:red;";
						$Monto = abs($Monto) * (-1);
						$Debitos += $Monto;
					} else {
						$style = "";
						$Monto = abs($Monto);
						$Creditos += $Monto;
					}
					$sql = "INSERT INTO ap_ordenpagocontabilidad
							SET
								Anio = '".$Anio."',
								CodOrganismo = '".$CodOrganismo."',
								NroOrden = '".$NroOrden."',
								CodContabilidad = 'F',
								Secuencia = '".++$Secuencia."',
								CodCuenta = '".$field_det['CodCuenta']."',
								Monto = '".$Monto."',
								UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
								UltimaFecha = NOW()";
					execute($sql);
				}
				if (!count($fdet)) {
					$sql = "SELECT
								oc.CodCuentaPub20 AS CodCuenta,
								oc.TipoOrden,
								oc.NroOrden,
								pc.Descripcion AS NomCuenta,
								SUM(oc.Monto) AS MontoVoucher,
								pc.TipoSaldo,
								'04' AS Orden,
								'Debe' AS Columna
							FROM
								ap_obligacionescuenta oc
								INNER JOIN ac_mastplancuenta20 pc ON (oc.CodCuentaPub20 = pc.CodCuenta)
							WHERE
								oc.CodProveedor = '".$CodProveedor."' AND
								oc.CodTipoDocumento = '".$CodTipoDocumento."' AND
								oc.NroDocumento = '".$NroDocumento."'
							GROUP BY CodCuenta";
					$Secuencia=0;
					$fdet = getRecords($sql);
					foreach ($fdet as $field_det) {
						$Secuencia++;
						$Monto = $field_det['MontoVoucher'];
						if ($field_det['Columna'] == "Haber") {
							$Monto = abs($Monto) * (-1);
							$Debitos += $Monto;
						} else {
							$style = "";
							$Monto = abs($Monto);
							$Creditos += $Monto;
						}
						$sql = "INSERT INTO ap_ordenpagocontabilidad
								SET
									Anio = '".$Anio."',
									CodOrganismo = '".$CodOrganismo."',
									NroOrden = '".$NroOrden."',
									CodContabilidad = 'F',
									Secuencia = '".$Secuencia."',
									CodCuenta = '".$field_det['CodCuenta']."',
									Monto = '".$Monto."',
									UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
									UltimaFecha = NOW()";
						execute($sql);
					}
				}
			}
			if ($FlagGenerarPago == "N") {
				//	actualizo obligacion
				$sql = "UPDATE ap_obligaciones
						SET
							Estado = 'PA',
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()
						WHERE
							CodProveedor = '".$CodProveedor."' AND
							CodTipoDocumento = '".$CodTipoDocumento."' AND
							NroDocumento = '".$NroDocumento."'";
				execute($sql);
				//	actualizo a pagada la orden
				$sql = "UPDATE ap_ordenpago
						SET
							Estado = 'PA',
							FlagContPendienteOrdPub20 = 'N'
						WHERE
							Anio = '".$Anio."' AND
							CodOrganismo = '".$CodOrganismo."' AND
							NroOrden = '".$NroOrden."'";
				execute($sql);
				//	actualizo a pagadas las partidas
				$sql = "UPDATE ap_ordenpagodistribucion
						SET
							Periodo = '".substr(formatFechaAMD($FechaAprobado),0,7)."',
							FechaEjecucion = '".formatFechaAMD($FechaAprobado)."',
							Estado = 'PA'
						WHERE
							Anio = '".$Anio."' AND
							CodOrganismo = '".$CodOrganismo."' AND
							NroOrden = '".$NroOrden."'";
				execute($sql);
			}
		}
		echo "|$NroOrden";
		mysql_query("COMMIT");
	}
	//	anular
	elseif ($accion == "anular") {
		mysql_query("BEGIN");
		list($AnioActual, $MesActual, $DiaActual) = split("[/.-]", substr($Ahora, 0, 10));
		$FechaActual = "$AnioActual-$MesActual-$DiaActual";
		$PeriodoActual = "$AnioActual-$MesActual";
		if ($Estado == "PR") {
			//	ELIMINO O ANULO
			if ($_PARAMETRO["OBLIGANUL"] == "S") {
				//	partidas
				$sql = "DELETE FROM ap_distribucionobligacion
						WHERE
							CodProveedor = '".$CodProveedor."' AND
							CodTipoDocumento = '".$CodTipoDocumento."' AND
							NroDocumento = '".$NroDocumento."'";
				execute($sql);
				//	impuestos
				$sql = "DELETE FROM ap_obligacionesimpuesto
						WHERE
							CodProveedor = '".$CodProveedor."' AND
							CodTipoDocumento = '".$CodTipoDocumento."' AND
							NroDocumento = '".$NroDocumento."'";
				execute($sql);
				//	cuentas/partidas
				$sql = "DELETE FROM ap_obligacionescuenta
						WHERE
							CodProveedor = '".$CodProveedor."' AND
							CodTipoDocumento = '".$CodTipoDocumento."' AND
							NroDocumento = '".$NroDocumento."'";
				execute($sql);
				//	obligacion
				$sql = "DELETE FROM ap_obligaciones
						WHERE
							CodProveedor = '".$CodProveedor."' AND
							CodTipoDocumento = '".$CodTipoDocumento."' AND
							NroDocumento = '".$NroDocumento."'";
				execute($sql);
			} else {
				//	partidas
				$sql = "UPDATE ap_distribucionobligacion
						SET
							Estado = 'AN',
							FechaAnulacion = NOW(),
							PeriodoAnulacion = NOW(),
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()
						WHERE
							CodProveedor = '".$CodProveedor."' AND
							CodTipoDocumento = '".$CodTipoDocumento."' AND
							NroDocumento = '".$NroDocumento."'";
				execute($sql);
				if ($FlagCompromiso == "S") {
					$sql = "UPDATE lg_distribucioncompromisos
							SET
								FechaAnulacion = NOW(),
								PeriodoAnulacion = NOW(),
								Estado = 'AN',
								UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
								UltimaFecha = NOW()
							WHERE
								Anio = '".substr($Periodo, 0, 4)."' AND
								CodOrganismo = '".$CodOrganismo."' AND
								CodProveedor = '".$CodProveedor."' AND
								CodTipoDocumento = '".$CodTipoDocumento."' AND
								NroDocumento = '".$NroDocumento."'";
					execute($sql);
				}
				//	obligacion
				$sql = "UPDATE ap_obligaciones
						SET
							Estado = 'AN',
							MotivoAnulacion = '".$MotivoAnulacion."',
							FechaAnulacion = NOW(),
							AnuladoPor = '".$_SESSION["CODPERSONA_ACTUAL"]."',
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()
						WHERE
							CodProveedor = '".$CodProveedor."' AND
							CodTipoDocumento = '".$CodTipoDocumento."' AND
							NroDocumento = '".$NroDocumento."'";
				execute($sql);
			}
			//	actualizo documento
			$sql = "UPDATE ap_documentos
					SET
						Estado = 'PR',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()
					WHERE
						Anio = '".substr($Periodo, 0, 4)."' AND
						CodOrganismo = '".$CodOrganismo."' AND
						CodProveedor = '".$CodProveedor."' AND
						ObligacionTipoDocumento = '".$CodTipoDocumento."' AND
						ObligacionNroDocumento = '".$NroDocumento."'";
			execute($sql);
			//	actualizo payroll
			$sql = "UPDATE pr_tiponominaempleado
					SET EstadoPago = 'PE'
					WHERE
						CodProveedor = '".$CodProveedor."' AND
						CodTipoDocumento = '".$CodTipoDocumento."' AND
						NroDocumento = '".$NroDocumento."'";
			execute($sql);
			//	si viene de viatico
			if ($CodTipoDocumento == $_PARAMETRO['DOCVIAT']) {
				list($ReferenciaTipoDocumento, $ReferenciaNroDocumento) = explode("-", $ReferenciaDocumento);
				$sql = "UPDATE ap_viaticos
						SET
							Estado = 'RV',
							GeneradoPor = '',
							FechaGenerado = '',
							ObligacionTipoDocumento = '',
							ObligacionNroDocumento = '',
							ObligacionProveedor = '',
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()
						WHERE
							CodOrganismo = '".$CodOrganismo."' AND
							CodViatico = '".$ReferenciaNroDocumento."'";
				execute($sql);
			}
			//	si viene de viatico
			elseif ($CodTipoDocumento == 'CC') {
				$sql = "UPDATE ap_cajachica
						SET
							AprobadoPor = '',
							FechaAprobacion = '',
							CodTipoDocumento = '',
							NroDocumento = '',
							NroDocumentoInterno = '',
							Estado = 'PR',
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()
						WHERE
							Periodo = '".substr($Periodo, 0, 4)."' AND
							CodOrganismo = '".$CodOrganismo."' AND
							CodTipoDocumento = '".$CodTipoDocumento."' AND
							NroDocumento = '".$NroDocumento."'";
				execute($sql);
			}
		}
		elseif ($Estado == "RV") {
			if ($FlagCompromiso == "S") {
				$sql = "UPDATE lg_distribucioncompromisos
						SET
							Estado = 'PE',
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()
						WHERE
							Anio = '".substr($Periodo, 0, 4)."' AND
							CodOrganismo = '".$CodOrganismo."' AND
							CodProveedor = '".$CodProveedor."' AND
							CodTipoDocumento = '".$CodTipoDocumento."' AND
							NroDocumento = '".$NroDocumento."'";
				execute($sql);
			}
			//	partidas
			$sql = "UPDATE ap_distribucionobligacion
					SET
						Estado = 'PE',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()
					WHERE
						CodProveedor = '".$CodProveedor."' AND
						CodTipoDocumento = '".$CodTipoDocumento."' AND
						NroDocumento = '".$NroDocumento."'";
			execute($sql);
			//	vouchers
			if ($FlagContPendientePub20 == "N" && $_PARAMETRO['CONTPUB20'] == "S") {
				//	genero nuevo voucher
				$CodVoucher = substr($VoucherPub20, 0, 2);
				$NroVoucher = getCodigo("ac_vouchermast", "NroVoucher", 4, "CodOrganismo", $CodOrganismo, "Periodo", $PeriodoActual, "CodVoucher", $CodVoucher, "CodContabilidad", "F");
				$NroInterno = getCodigo("ac_vouchermast", "NroInterno", 10);
				$Voucher = "$CodVoucher-$NroVoucher";
				//	voucher mast
				$sql = "INSERT INTO ac_vouchermast (
									CodOrganismo,
									Periodo,
									Voucher,
									CodContabilidad,
									Prefijo,
									NroVoucher,
									CodVoucher,
									CodDependencia,
									CodModeloVoucher,
									CodSistemaFuente,
									Creditos,
									Debitos,
									Lineas,
									PreparadoPor,
									FechaPreparacion,
									AprobadoPor,
									FechaAprobacion,
									TituloVoucher,
									ComentariosVoucher,
									FechaVoucher,
									NroInterno,
									FlagTransferencia,
									Estado,
									CodLibroCont,
									UltimoUsuario,
									UltimaFecha
						)
								SELECT
									CodOrganismo,
									NOW() AS Periodo,
									'$Voucher' AS Voucher,
									'F' AS CodContabilidad,
									'$CodVoucher' AS Prefijo,
									'$NroVoucher' AS NroVoucher,
									'$CodVoucher' AS CodVoucher,
									CodDependencia,
									CodModeloVoucher,
									CodSistemaFuente,
									Creditos,
									Debitos,
									Lineas,
									'".$_SESSION["CODPERSONA_ACTUAL"]."' AS PreparadoPor,
									NOW() AS FechaPreparacion,
									'".$_SESSION["CODPERSONA_ACTUAL"]."' AS AprobadoPor,
									NOW() AS FechaAprobacion,
									CONCAT('$MotivoAnulacion (', TituloVoucher, ')') AS TituloVoucher,
									CONCAT('$MotivoAnulacion (', ComentariosVoucher, ')') AS ComentariosVoucher,
									NOW() AS FechaVoucher,
									'$NroInterno' AS NroInterno,
									FlagTransferencia,
									Estado,
									CodLibroCont,
									'".$_SESSION["USUARIO_ACTUAL"]."' AS UltimoUsuario,
									NOW() AS UltimaFecha
								FROM ac_vouchermast
								WHERE
									CodOrganismo = '".$CodOrganismo."' AND
									Periodo = '".$VoucherPeriodoPub20."' AND
									Voucher = '".$VoucherPub20."' AND
									CodContabilidad = 'F'";
				execute($sql);
				//	voucher detalles
				$sql = "INSERT INTO ac_voucherdet (
									CodOrganismo,
									Periodo,
									Voucher,
									CodContabilidad,
									Linea,
									CodCuenta,
									MontoVoucher,
									MontoPost,
									CodPersona,
									NroCheque,
									FechaVoucher,
									CodCentroCosto,
									ReferenciaTipoDocumento,
									ReferenciaNroDocumento,
									Descripcion,
									Estado,
									UltimoUsuario,
									UltimaFecha
						)
								SELECT
									CodOrganismo,
									NOW() AS Periodo,
									'$Voucher' AS Voucher,
									'F' AS CodContabilidad,
									Linea,
									CodCuenta,
									(MontoVoucher*(-1)) AS MontoVoucher,
									(MontoPost*(-1)) AS MontoPost,
									CodPersona,
									NroCheque,
									NOW() AS FechaVoucher,
									CodCentroCosto,
									ReferenciaTipoDocumento,
									ReferenciaNroDocumento,
									CONCAT('$MotivoAnulacion (', Descripcion, ')') AS Descripcion,
									Estado,
									'".$_SESSION["USUARIO_ACTUAL"]."' AS UltimoUsuario,
									NOW() AS UltimaFecha
								FROM ac_voucherdet
								WHERE
									CodOrganismo = '".$CodOrganismo."' AND
									Periodo = '".$VoucherPeriodoPub20."' AND
									Voucher = '".$VoucherPub20."' AND
									CodContabilidad = 'F'";
				execute($sql);
			}
			//	obligacion
			$sql = "UPDATE ap_obligaciones
					SET
						Estado = 'PR',
						FlagContPendientePub20 = 'S',
						VoucherAnulPub20 = '".$Voucher."',
						PeriodoAnulPub20 = NOW(),
						MotivoAnulacion = '".$MotivoAnulacion."',
						FechaAnulacion = NOW(),
						AnuladoPor = '".$_SESSION["CODPERSONA_ACTUAL"]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()
					WHERE
						CodProveedor = '".$CodProveedor."' AND
						CodTipoDocumento = '".$CodTipoDocumento."' AND
						NroDocumento = '".$NroDocumento."'";
			execute($sql);
		}
		echo "|$CodProveedor"."_"."$CodTipoDocumento"."_"."$NroDocumento";
		mysql_query("COMMIT");
	}
}
//	ajax
elseif ($modulo == "ajax") {
	//	selector de persona
	if ($accion == "selListadoObligacionPersona") {	
		//	consulto los datos del proveedor
		$sql = "SELECT
					p.NomCompleto,
					pv.DiasPago,
					p.DocFiscal,
					p.Busqueda,
					pv.CodTipoDocumento,
					pv.CodTipoServicio,
					pv.CodTipoPago
				FROM
					mastpersonas p
					LEFT JOIN mastproveedores pv ON (p.CodPersona = pv.CodProveedor)
				WHERE p.CodPersona = '".$CodPersona."'";
		$query_proveedor = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		if (mysql_num_rows($query_proveedor) != 0) $field_proveedor = mysql_fetch_array($query_proveedor);
		//	consulto la cuenta bancaria por default
		$sql = "SELECT NroCuenta
				FROM ap_ctabancariadefault
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					CodTipoPago = '".$field_proveedor['CodTipoPago']."'";
		$query_cta = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		if (mysql_num_rows($query_cta) != 0) $field_cta = mysql_fetch_array($query_cta);
		//	porcentaje IVA
		$FactorImpuesto = getPorcentajeIVA($field_proveedor['CodTipoServicio']);
		//	valores
		echo "$field_proveedor[NomCompleto]|$field_proveedor[DiasPago]|$field_proveedor[DocFiscal]|$field_proveedor[Busqueda]|$field_proveedor[CodTipoDocumento]|$field_proveedor[CodTipoServicio]|$field_proveedor[CodTipoPago]|$field_cta[NroCuenta]|$FactorImpuesto|";
		//	impuestos
		$sql = "SELECT
					i.CodImpuesto,
					i.Descripcion,
					i.Signo,
					i.FlagImponible,
					i.FlagProvision,
					i.FactorPorcentaje,
					i.CodCuenta
				FROM
					masttiposervicio ts
					INNER JOIN masttiposervicioimpuesto tsi ON (ts.CodTipoServicio = tsi.CodTipoServicio)
					INNER JOIN mastimpuestos i ON (tsi.CodImpuesto = i.CodImpuesto AND i.CodRegimenFiscal = 'R')
				WHERE ts.CodTipoServicio = '".$field_proveedor['CodTipoServicio']."'";	$nrodetalle = 0;
		$query_impuestos = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		while($field_impuestos = mysql_fetch_array($query_impuestos)) {	$nrodetalle++;
			$MontoAfecto = 0;
			$MontoImpuesto = $MontoAfecto * $field_impuestos['FactorPorcentaje'] / 100;
			if ($field_impuestos['Signo'] == "N") $MontoImpuesto *= -1;
			?>
			<th><?=$nrodetalle?></th>
			<td>
				<input type="text" value="<?=$field_impuestos['Descripcion']?>" class="cell2" readonly="readonly" />
				<input type="hidden" name="CodImpuesto" value="<?=$field_impuestos['CodImpuesto']?>" />
				<input type="hidden" name="CodConcepto" />
				<input type="hidden" name="Signo" value="<?=$field_impuestos['Signo']?>" />
				<input type="hidden" name="FlagImponible" value="<?=$field_impuestos['FlagImponible']?>" />
				<input type="hidden" name="FlagProvision" value="<?=$field_impuestos['FlagProvision']?>" />
				<input type="hidden" name="CodCuenta" value="<?=$field_impuestos['CodCuenta']?>" />
			</td>
			<td><input type="text" name="MontoAfecto" value="<?=number_format($MontoAfecto, 2, ',', '.')?>" style="text-align:right;" class="cell2" readonly="readonly" /></td>
			<td><input type="text" name="FactorPorcentaje" value="<?=number_format($field_impuestos['FactorPorcentaje'], 2, ',', '.')?>" style="text-align:right;" class="cell2" readonly="readonly" /></td>
			<td><input type="text" name="MontoImpuesto" value="<?=number_format($MontoImpuesto, 2, ',', '.')?>" style="text-align:right;" class="cell2" readonly="readonly" /></td>
			<?php
		}
	}
	//	selector de impuestos/retenciones
	elseif ($accion == "obligacion_impuestos_insertar") {
		$sql = "SELECT
					i.CodImpuesto,
					i.Descripcion,
					i.Signo,
					i.FlagImponible,
					i.FlagProvision,
					i.FactorPorcentaje,
					i.CodCuenta,
					i.CodCuentaPub20,
					i.FlagSustraendo,
					i.SustraendoUT,
					i.CodRegimenFiscal
				FROM
					mastimpuestos i
				WHERE i.CodImpuesto = '".$CodImpuesto."'";
		$query_impuestos = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		if (mysql_num_rows($query_impuestos)) $field_impuestos = mysql_fetch_array($query_impuestos);
		if ($field_impuestos['FlagImponible'] == "I") $MontoAfecto = $Impuesto;
		elseif ($field_impuestos['FlagImponible'] == "N") $MontoAfecto = $Afecto;
		elseif ($field_impuestos['FlagImponible'] == "N") $MontoAfecto = $Afecto + $NoAfecto;
		elseif ($field_impuestos['FlagImponible'] == "T") $MontoAfecto = $Afecto + $Impuesto;
		$MontoImpuesto = $MontoAfecto * $field_impuestos['FactorPorcentaje'] / 100;
		if ($field_impuestos['Signo'] == "N") $Signo = "-";
		##	
		$MontoBruto = $Afecto + $NoAfecto;
		##	
		if (formatFechaAMD($FechaObligacion) >= '2016-02-22' && formatFechaAMD($FechaObligacion) <= '2017-02-28')
		{
			$sql = "SELECT * FROM mastunidadtributaria WHERE Fecha <= '2016-02-22' ORDER BY Fecha DESC LIMIT 0, 1";
		}
		else
		{
			$sql = "SELECT * FROM mastunidadtributaria WHERE Fecha <= '".formatFechaAMD($FechaObligacion)."' ORDER BY Fecha DESC LIMIT 0, 1";
		}
		$field_ut = getRecord($sql);
		##	
		$LimiteSustraendo = $field_impuestos['SustraendoUT'] * $field_ut['Valor'];
		$MontoSustraendo = 0;
		if ($field_impuestos['FlagSustraendo'] == "S" && $MontoAfecto >= $LimiteSustraendo) {
			$MontoAfectoSustraendo = $LimiteSustraendo;
			$MontoSustraendo = $LimiteSustraendo * $field_impuestos['FactorPorcentaje'] / 100;
			$MontoImpuesto = $MontoImpuesto - $MontoSustraendo;
		}
		?>
		<th><?=$nrodetalle?></th>
		<td>
			<input type="text" value="<?=$field_impuestos['Descripcion']?>" class="cell2" readonly="readonly" />
			<input type="hidden" name="CodImpuesto" value="<?=$field_impuestos['CodImpuesto']?>" />
			<input type="hidden" name="CodConcepto" />
			<input type="hidden" name="Signo" value="<?=$field_impuestos['Signo']?>" />
			<input type="hidden" name="FlagImponible" value="<?=$field_impuestos['FlagImponible']?>" />
			<input type="hidden" name="FlagProvision" value="<?=$field_impuestos['FlagProvision']?>" />
			<input type="hidden" name="CodCuenta" value="<?=$field_impuestos['CodCuenta']?>" />
			<input type="hidden" name="CodCuentaPub20" value="<?=$field_impuestos['CodCuentaPub20']?>" />
		</td>
		<td><input type="text" name="MontoAfecto" value="<?=number_format($MontoAfecto, 2, ',', '.')?>" style="text-align:right;" class="cell2" readonly="readonly" /></td>
		<td><input type="text" name="FactorPorcentaje" value="<?=number_format($field_impuestos['FactorPorcentaje'], 2, ',', '.')?>" style="text-align:right;" class="cell2" readonly="readonly" /></td>
		<td>
			<input type="hidden" name="MontoSustraendo" value="<?=number_format($MontoSustraendo, 2, ',', '.')?>" />
			<input type="hidden" name="MontoAfectoSustraendo" value="<?=number_format($MontoAfectoSustraendo, 2, ',', '.')?>" />
			<input type="text" name="MontoImpuesto" value="<?=$Signo?><?=number_format($MontoImpuesto, 2, ',', '.')?>" style="text-align:right;" class="cell2" readonly="readonly" />
			<input type="hidden" name="CodRegimenFiscal" value="<?=$field_impuestos['CodRegimenFiscal']?>" />
		</td>
		<?php
	}
	//	selector de documentos
	elseif ($accion == "obligacion_documentos_insertar") {
		list($TipoDoc, $Anio, $NroOrden, $DocumentoClasificacion, $DocumentoReferencia, $Referencia) = split("[-]", $registro);
		$orden = "$TipoDoc-$NroOrden";
		if (!afectaTipoServicio($CodTipoServicio)) $cFlagNoAfectoIGV = "checked";
		list($cod_partidaIGV, $CodCuentaIGV, $CodCuentaPub20IGV) = getPartidaCuentaFromIGV($_PARAMETRO["IGVCODIGO"]);
		//	si es un orden de compra
		if ($TipoDoc == "OC") {
			$clasificacion = "O.Compra";
			if ($DocumentoReferencia != "") {
				$sql = "SELECT
							oc.FechaPreparacion AS Fecha,
							d.MontoAfecto,
							d.MontoNoAfecto,
							(d.MontoAfecto + d.MontoNoAfecto) AS MontoBruto,
							d.MontoImpuestos AS MontoIGV,
							d.MontoTotal,
							oc.Observaciones AS Comentarios,
							oc.cod_partida,
							oc.CodProveedor,
							oc.Anio,
							d.ReferenciaTipoDocumento,
							d.DocumentoReferencia,
							d.DocumentoClasificacion,
							d.ReferenciaNroDocumento,
							i.FactorPorcentaje
						FROM
							lg_ordencompra oc
							INNER JOIN ap_documentos d ON (d.CodOrganismo = oc.CodOrganismo AND
														   d.ReferenciaNroDocumento = oc.NroOrden AND
														   d.Anio = oc.Anio,
														   d.ReferenciaTipoDocumento = 'OC' AND
														   d.CodProveedor = '".$CodProveedor."' AND
														   d.DocumentoClasificacion = '".$DocumentoClasificacion."' AND
														   d.DocumentoReferencia = '".$DocumentoReferencia."-".$Referencia."')
							INNER JOIN mastproveedores p ON (oc.CodProveedor = p.CodProveedor)
							INNER JOIN masttiposervicioimpuesto tsi ON (p.CodTipoServicio = tsi.CodTipoServicio)
							INNER JOIN mastimpuestos i ON (tsi.CodImpuesto = i.CodImpuesto)
						WHERE 
							oc.CodOrganismo = '".$CodOrganismo."' AND
							oc.NroOrden = '".$NroOrden."'";
			} 
			else {
				$sql = "SELECT
							oc.FechaPreparacion AS Fecha,
							oc.MontoAfecto,
							oc.MontoNoAfecto,
							oc.MontoBruto,
							oc.MontoIGV,
							oc.MontoTotal,
							oc.Observaciones AS Comentarios,
							oc.cod_partida,
							oc.CodProveedor,
							oc.Anio,
							oc.NroInterno,
							CONCAT('OC-', oc.Anio, '-', oc.NroInterno, '-', ((SELECT COUNT(*)
																			  FROM ap_documentos
																			  WHERE
																				Anio = oc.Anio AND
																				CodOrganismo = oc.CodOrganismo AND
																				ReferenciaTipoDocumento = 'OC' AND
																				ReferenciaNroDocumento = oc.NroOrden) + 1)) AS DocumentoReferencia,
							i.FactorPorcentaje
						FROM
							lg_ordencompra oc
							INNER JOIN mastproveedores p ON (oc.CodProveedor = p.CodProveedor)
							INNER JOIN masttiposervicioimpuesto tsi ON (p.CodTipoServicio = tsi.CodTipoServicio)
							INNER JOIN mastimpuestos i ON (tsi.CodImpuesto = i.CodImpuesto)
						WHERE 
							oc.Anio = '".$Anio."' AND
							oc.CodOrganismo = '".$CodOrganismo."' AND
							oc.NroOrden = '".$NroOrden."'";
			}
		}
		elseif ($TipoDoc == "OS") {
			$clasificacion = "O.Servicio";
			if ($DocumentoReferencia != "") {
				$sql = "SELECT 
							os.FechaPreparacion AS Fecha,
							d.MontoAfecto,
							d.MontoNoAfecto,
							(d.MontoAfecto + d.MontoNoAfecto) AS MontoBruto,
							d.MontoImpuestos AS MontoIGV,
							d.MontoTotal,
							d.Comentarios,
							os.cod_partida,
							os.CodProveedor,
							os.Anio,
							d.DocumentoReferencia,
							d.ReferenciaTipoDocumento,
							d.DocumentoClasificacion,
							i.FactorPorcentaje
						FROM 
							lg_ordenservicio os
							INNER JOIN ap_documentos d ON (os.Anio = d.Anio AND 
														   os.CodOrganismo = d.CodOrganismo AND
														   os.NroOrden = d.ReferenciaNroDocumento AND 
														   d.ReferenciaTipoDocumento = 'OS' AND
														   d.CodProveedor = '".$CodProveedor."' AND
														   d.DocumentoClasificacion = '".$DocumentoClasificacion."' AND
														   d.DocumentoReferencia = '".$DocumentoReferencia."-".$Referencia."')
							INNER JOIN mastproveedores p ON (os.CodProveedor = p.CodProveedor)
							INNER JOIN masttiposervicioimpuesto tsi ON (p.CodTipoServicio = tsi.CodTipoServicio)
							INNER JOIN mastimpuestos i ON (tsi.CodImpuesto = i.CodImpuesto)
						WHERE 
							os.Anio = '".substr($Ahora, 0, 4)."' AND
							os.CodOrganismo = '".$CodOrganismo."' AND
							os.NroOrden = '".$NroOrden."'";
			} 
			else {
				$sql = "SELECT 
							os.FechaPreparacion AS Fecha,
							os.MontoOriginal AS MontoAfecto,
							os.MontoNoAfecto,
							os.MontoOriginal AS MontoBruto,
							os.MontoIva AS MontoIGV,
							os.TotalMontoIva AS MontoTotal,
							os.Descripcion AS Comentarios,
							os.cod_partida,
							os.CodProveedor,
							os.Anio,
							CONCAT('OS-', os.Anio, '-', os.NroInterno, '-', ((SELECT COUNT(*)
																			  FROM ap_documentos
																			  WHERE
																				Anio = os.Anio AND
																				CodOrganismo = os.CodOrganismo AND
																				ReferenciaTipoDocumento = 'OS' AND
																				ReferenciaNroDocumento = os.NroOrden) + 1)) AS DocumentoReferencia,
							i.FactorPorcentaje
						FROM 
							lg_ordenservicio os
							INNER JOIN mastproveedores p ON (os.CodProveedor = p.CodProveedor)
							INNER JOIN masttiposervicioimpuesto tsi ON (p.CodTipoServicio = tsi.CodTipoServicio)
							INNER JOIN mastimpuestos i ON (tsi.CodImpuesto = i.CodImpuesto)
						WHERE 
							os.Anio = '".$Anio."' AND
							os.CodOrganismo = '".$CodOrganismo."' AND
							os.NroOrden = '".$NroOrden."'";
			}	
		}
		$query_documentos = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		if (mysql_num_rows($query_documentos)) $field_documentos = mysql_fetch_array($query_documentos);
		if ($Monto == $field_documentos['MontoTotal']) $porcentaje_monto = 1;
		else $porcentaje_monto = ($Monto * 100 / $field_documentos['MontoTotal']) / 100;
		$MontoAfecto = $field_documentos['MontoAfecto'] * $porcentaje_monto;
		$MontoNoAfecto = $field_documentos['MontoNoAfecto'] * $porcentaje_monto;
		$MontoImpuestos = $field_documentos['MontoIGV'] * $porcentaje_monto;
		$monto_bruto = $MontoAfecto + $MontoNoAfecto;
		$MontoTotal = $monto_bruto + $MontoImpuestos;
		echo "||";
		?>
		<!--Documentos Relacionados-->
		<th><?=$nrodetalle?></th>
		<td>
			<input type="text" value="<?=$clasificacion?>" class="cell2" readonly="readonly" />
			<input type="hidden" name="Porcentaje" value="<?=$porcentaje_monto?>" />
			<input type="hidden" name="DocumentoClasificacion" value="<?=$DocumentoClasificacion?>" />
		</td>
		<td>
			<input type="text" name="DocumentoReferencia" value="<?=$field_documentos['DocumentoReferencia']?>" style="text-align:center;" class="cell2" readonly="readonly" />
		</td>
		<td>
			<input type="text" name="Fecha" value="<?=formatFechaDMA($field_documentos['Fecha'])?>" style="text-align:center;" class="cell2" readonly="readonly" />
		</td>
		<td>
			<input type="text" name="ReferenciaTipoDocumento" value="<?=$TipoDoc?>" style="width:15%;" class="cell2" readonly="readonly" />
			<input type="text" name="ReferenciaNroDocumento" value="<?=$NroOrden?>" style="width:70%;" class="cell2" readonly="readonly" />
		</td>
		<td>
			<input type="text" name="MontoTotal" value="<?=number_format($MontoTotal, 2, ',', '.')?>" style="text-align:right;" class="cell2" readonly="readonly" />
		</td>
		<td>
			<input type="text" name="MontoAfecto" value="<?=number_format($MontoAfecto, 2, ',', '.')?>" style="text-align:right;" class="cell2" readonly="readonly" />
		</td>
		<td>
			<input type="text" name="MontoImpuestos" value="<?=number_format($MontoImpuestos, 2, ',', '.')?>" style="text-align:right;" class="cell2" readonly="readonly" />
		</td>
		<td>
			<input type="text" name="MontoNoAfecto" value="<?=number_format($MontoNoAfecto, 2, ',', '.')?>" style="text-align:right;" class="cell2" readonly="readonly" />
		</td>
		<td>
			<input type="text" name="Comentarios" value="<?=$field_documentos['Comentarios']?>" class="cell2" readonly="readonly" />
		</td>
		<?php
		echo "||";
		//	verifico si la orden tiene activos fijos
		$sql = "SELECT ocd.*
				FROM 
					lg_ordencompradetalle ocd
					INNER JOIN lg_commoditysub cs ON (ocd.CommoditySub = cs.Codigo)
					INNER JOIN lg_commoditymast cm ON (cs.CommodityMast = cm.CommodityMast)
				WHERE
					(cm.Clasificacion = 'BME' OR cm.Clasificacion = 'ACT') AND
					ocd.Anio = '".$Anio."' AND
					ocd.CodOrganismo = '".$CodOrganismo."' AND
					ocd.NroOrden = '".$NroOrden."'";
		$query_activo = mysql_query($sql) or die ($sql.mysql_error());
		$rows_activo = mysql_num_rows($query_activo);
		//	si es una orden de compra y no tiene activos fijos
		if ($TipoDoc == "OC" && $rows_activo == 0) {
			$sql = "SELECT
						doc.*,
						p.denominacion AS NomPartida,
						pc.Descripcion AS NomCuenta,
						pc20.Descripcion AS NomCuentaPub20,
						'OC' AS TipoOrden,
						oc.CodProveedor AS CodPersona,
						oc.Observaciones As Descripcion,
						cc.Codigo AS NomCentroCosto,
						pv.CategoriaProg
					FROM
						lg_distribucionoc doc
						INNER JOIN pv_partida p ON (doc.cod_partida = p.cod_partida)
						LEFT JOIN ac_mastplancuenta pc ON (doc.CodCuenta = pc.CodCuenta)
						LEFT JOIN ac_mastplancuenta20 pc20 ON (doc.CodCuentaPub20 = pc20.CodCuenta)
						INNER JOIN lg_ordencompra oc ON (doc.Anio = oc.Anio AND
														 doc.CodOrganismo = oc.CodOrganismo AND
														 doc.NroOrden = oc.NroOrden)
						LEFT JOIN ac_mastcentrocosto cc ON (doc.CodCentroCosto = cc.CodCentroCosto)
						LEFT JOIN pv_presupuesto pv ON (pv.CodOrganismo = doc.CodOrganismo AND pv.CodPresupuesto = doc.CodPresupuesto)
					WHERE
						doc.Anio = '".$Anio."' AND
						doc.CodOrganismo = '".$CodOrganismo."' AND
						doc.NroOrden = '".$NroOrden."' AND
						doc.cod_partida <> '".$cod_partidaIGV."'
					GROUP BY cod_partida
					ORDER BY Secuencia";
			$query_distribucion = mysql_query($sql) or die ($sql.mysql_error());
			while ($field_distribucion = mysql_fetch_array($query_distribucion)) {
				$nro_distribucion++;
				$Monto = $field_distribucion['Monto'] * $porcentaje_monto;
				?>
				<tr class="trListaBody distribucion_<?=$registro?>" onclick="mClk(this, 'sel_distribucion');" id="distribucion_<?=$nro_distribucion?>">
					<th><?=$nro_distribucion?></th>
					<td align="center" width="75">
						<input type="text" name="cod_partida" id="cod_partida_<?=$nro_distribucion?>" value="<?=$field_distribucion['cod_partida']?>" style="width:99%; text-align:center;" maxlength="12" class="cell2 cod_partida" readonly="readonly" />
					</td>
					<td align="center" width="225">
						<input type="text" name="NomPartida" id="NomPartida_<?=$nro_distribucion?>" value="<?=htmlentities($field_distribucion['NomPartida'])?>" style="width:99%;" class="cell2" readonly="readonly" />
					</td>
					<td align="center" width="80">
						<input type="text" name="CodCuenta" id="CodCuenta_<?=$nro_distribucion?>" value="<?=$field_distribucion['CodCuenta']?>" maxlength="13" style="width:99%; text-align:center;" class="cell2" readonly="readonly" />
					</td>
					<td align="center" width="220">
						<input type="text" name="NomCuenta" id="NomCuenta_<?=$nro_distribucion?>" value="<?=htmlentities($field_distribucion['NomCuenta'])?>" style="width:99%;" class="cell2" readonly="readonly" />
					</td>
					<td align="center" width="80">
						<input type="text" name="CodCuentaPub20" id="CodCuentaPub20_<?=$nro_distribucion?>" value="<?=$field_distribucion['CodCuentaPub20']?>" maxlength="13" style="width:99%; text-align:center;" class="cell2" readonly="readonly" />
					</td>
					<td align="center" width="220">
						<input type="text" name="NomCuentaPub20" id="NomCuentaPub20_<?=$nro_distribucion?>" value="<?=htmlentities($field_distribucion['NomCuentaPub20'])?>" style="width:99%;" class="cell2" readonly="readonly" />
					</td>
					<td align="center">
						<input type="text" name="CodCentroCosto" id="CodCentroCosto_<?=$nro_distribucion?>" value="<?=$field_distribucion['CodCentroCosto']?>" style="text-align:center;" class="cell" <?=$disabled_distribucion?> />
						<input type="hidden" name="NomCentroCosto" id="NomCentroCosto_<?=$nro_distribucion?>" value="<?=$field_distribucion['NomCentroCosto']?>" />
					</td>
					<td align="center">
						<input type="checkbox" name="FlagNoAfectoIGV" class="FlagNoAfectoIGV" <?=chkFlag($field_distribucion['FlagNoAfectoIGV'])?> onchange="actualizarMontosObligacion();" <?=$cFlagNoAfectoIGV?> disabled="disabled" />
					</td>
					<td align="center">
						<input type="text" name="Monto" value="<?=number_format($field_distribucion['Monto'], 2, ',', '.')?>" style="text-align:right;" class="cell2" onfocus="numeroFocus(this);" onblur="numeroBlur(this);" readonly="readonly" />
					</td>
		            <td align="center">
		                <input type="text" name="detallesCategoriaProg" id="detallesCategoriaProg_<?=$nrodetalles?>" class="cell2 CategoriaProg" style="text-align:center;" value="<?=$field_distribucion['CategoriaProg']?>" readonly />
		                <input type="hidden" name="detallesEjercicio" id="detallesEjercicio_<?=$nrodetalles?>" class="cell2 Ejercicio" style="text-align:center;" value="<?=$field_distribucion['Ejercicio']?>" readonly />
		                <input type="hidden" name="detallesCodPresupuesto" id="detallesCodPresupuesto_<?=$nrodetalles?>" class="cell2 CodPresupuesto" style="text-align:center;" value="<?=$field_distribucion['CodPresupuesto']?>" readonly />
		            </td>
		            <td>
		                <select name="detallesCodFuente" id="detallesCodFuente_<?=$nrodetalles?>" class="cell2 CodFuente" <?=$disabled_ver?>>
		                    <?=loadSelect2("pv_fuentefinanciamiento","CodFuente","Denominacion",$field_distribucion['CodFuente'],11)?>
		                </select>
		            </td>
					<td align="center" width="25">
						<input type="text" name="TipoOrden" value="<?=$field_distribucion['TipoOrden']?>" maxlength="2" style="width:99%; text-align:center;" class="cell2" readonly="readonly" />
					</td>
					<td align="center" width="85">
						<input type="text" name="NroOrden" value="<?=$field_distribucion['NroOrden']?>" maxlength="100" style="width:99%;" class="cell2" readonly="readonly" />
					</td>
					<td align="center">
						<input type="text" name="Referencia" value="<?=$field_distribucion['Referencia']?>" maxlength="25" class="cell2" readonly="readonly" />
					</td>
					<td align="center">
						<input type="text" name="Descripcion" value="<?=htmlentities($field_distribucion['Descripcion'])?>" maxlength="255" class="cell2" readonly="readonly" />
					</td>
					<td align="center">
						<input type="text" name="CodPersona" id="CodPersona_<?=$nro_distribucion?>" value="<?=$field_distribucion['CodPersona']?>" maxlength="6" style="text-align:center;" class="cell2" readonly="readonly" />
						<input type="hidden" name="NomPersona" id="NomPersona_<?=$nro_distribucion?>" value="<?=$field_distribucion['NomPersona']?>" />
					</td>
					<td align="center">
						<input type="text" name="NroActivo" id="NroActivo_<?=$nro_distribucion?>" value="<?=$field_distribucion['NroActivo']?>" maxlength="15" style="text-align:center;" class="cell2" readonly="readonly" />
					</td>
					<td align="center">
						<input type="checkbox" name="FlagDiferido" disabled="disabled" />
					</td>
				</tr>
				<?php
				$distribucion_total += $field_distribucion['Monto'];
			}
		}
		//	si es una orden de servicio o si tiene activos fijos
		elseif ($TipoDoc == "OS" || $rows_activo != 0) {
			//	si es una orden de servicio
			if ($TipoDoc == "OS") {
				if ($DocumentoReferencia != "") {
					$sql = "SELECT 
								osd.*,
								osd.PrecioUnit AS Monto,
								p.denominacion AS NomPartida,
								pc.Descripcion AS NomCuenta,
								os.CodProveedor,
								'OS' AS TipoOrden,
								pv.CategoriaProg
							FROM 
								ap_documentosdetalle dd
								INNER JOIN ap_documentos d ON (dd.Anio = d.Anio AND
															   dd.CodProveedor = d.CodProveedor AND
															   dd.DocumentoClasificacion = d.DocumentoClasificacion AND
															   dd.DocumentoReferencia = d.DocumentoReferencia)
								INNER JOIN lg_ordenserviciodetalle osd ON (d.Anio = osd.Anio AND
																		   d.CodOrganismo = osd.CodOrganismo AND
																		   d.ReferenciaNroDocumento = osd.NroOrden AND
																		   osd.CommoditySub = dd.CommoditySub)
								INNER JOIN lg_ordenservicio os ON (osd.Anio = os.Anio AND
																   osd.CodOrganismo = os.CodOrganismo AND
																   osd.NroOrden = os.NroOrden)
								LEFT JOIN pv_partida p ON (osd.cod_partida = p.cod_partida)
								LEFT JOIN ac_mastplancuenta pc ON (osd.CodCuenta = pc.CodCuenta)
								LEFT JOIN pv_presupuesto pv ON (pv.CodOrganismo = os.CodOrganismo AND pv.CodPresupuesto = os.CodPresupuesto)
							WHERE
								dd.Anio = '".$field_documentos['Anio']."' AND
								dd.CodProveedor = '".$field_documentos['CodProveedor']."' AND
								dd.DocumentoClasificacion = '".$field_documentos['DocumentoClasificacion']."' AND
								dd.DocumentoReferencia = '".$field_documentos['DocumentoReferencia']."'
							ORDER BY Secuencia";
				} else {
					$sql = "SELECT 
								osd.*,
								osd.PrecioUnit AS Monto,
								p.denominacion AS NomPartida,
								pc.Descripcion AS NomCuenta,
								pc20.Descripcion AS NomCuentaPub20,
								os.CodProveedor,
								'OS' AS TipoOrden,
								pv.CategoriaProg
							FROM 
								lg_ordenserviciodetalle osd
								INNER JOIN lg_ordenservicio os ON (osd.Anio = os.Anio AND
																   osd.CodOrganismo = os.CodOrganismo AND
																   osd.NroOrden = os.NroOrden)
								LEFT JOIN pv_partida p ON (osd.cod_partida = p.cod_partida)
								LEFT JOIN ac_mastplancuenta pc ON (osd.CodCuenta = pc.CodCuenta)
								LEFT JOIN ac_mastplancuenta20 pc20 ON (osd.CodCuentaPub20 = pc20.CodCuenta)
								LEFT JOIN pv_presupuesto pv ON (pv.CodOrganismo = os.CodOrganismo AND pv.CodPresupuesto = os.CodPresupuesto)
							WHERE 
								osd.Anio = '".$Anio."' AND
								osd.CodOrganismo = '".$CodOrganismo."' AND
								osd.NroOrden = '".$NroOrden."'
							ORDER BY Secuencia";
				}
			}
			//	si tiene activos fijos
			else {
				$sql = "SELECT 
							ocd.*,
							ocd.PrecioUnit AS Monto,
							p.denominacion AS NomPartida,
							pc.Descripcion AS NomCuenta,
							pc20.Descripcion AS NomCuentaPub20,
							oc.CodProveedor AS CodPersona,
							'OC' AS TipoOrden,
							cc.Codigo AS NomCentroCosto,
							pv.CategoriaProg
						FROM 
							lg_ordencompradetalle ocd
							INNER JOIN lg_ordencompra oc ON (ocd.Anio = oc.Anio AND
															 ocd.CodOrganismo = oc.CodOrganismo AND
															 ocd.NroOrden = oc.NroOrden)
							LEFT JOIN pv_partida p ON (ocd.cod_partida = p.cod_partida)
							LEFT JOIN ac_mastplancuenta pc ON (ocd.CodCuenta = pc.CodCuenta)
							LEFT JOIN ac_mastplancuenta20 pc20 ON (ocd.CodCuentaPub20 = pc20.CodCuenta)
							LEFT JOIN ac_mastcentrocosto cc ON (ocd.CodCentroCosto = cc.CodCentroCosto)
							LEFT JOIN pv_presupuesto pv ON (pv.CodOrganismo = oc.CodOrganismo AND pv.CodPresupuesto = oc.CodPresupuesto)
						WHERE 
							ocd.Anio = '".$Anio."' AND
							ocd.CodOrganismo = '".$CodOrganismo."' AND
							ocd.NroOrden = '".$NroOrden."'
						ORDER BY Secuencia";
			}
			$query_distribucion = mysql_query($sql) or die ($sql.mysql_error());
			while ($field_distribucion = mysql_fetch_array($query_distribucion)) {
				for ($i=1; $i<=$field_distribucion['CantidadPedida']; $i++) {
					$nro_distribucion++;
					$Monto = $field_distribucion['Monto'] * $porcentaje_monto;
					?>
					<tr class="trListaBody distribucion_<?=$registro?>" onclick="mClk(this, 'sel_distribucion');" id="distribucion_<?=$nro_distribucion?>">
						<th><?=$nro_distribucion?></th>
						<td align="center" width="75">
							<input type="text" name="cod_partida" id="cod_partida_<?=$nro_distribucion?>" value="<?=$field_distribucion['cod_partida']?>" style="width:99%; text-align:center;" maxlength="12" class="cell2 cod_partida" readonly="readonly" />
						</td>
						<td align="center" width="225">
							<input type="text" name="NomPartida" id="NomPartida_<?=$nro_distribucion?>" value="<?=($field_distribucion['NomPartida'])?>" style="width:99%;" class="cell2" readonly="readonly" />
						</td>
						<td align="center" width="80">
							<input type="text" name="CodCuenta" id="CodCuenta_<?=$nro_distribucion?>" value="<?=$field_distribucion['CodCuenta']?>" maxlength="13" style="width:99%; text-align:center;" class="cell2" readonly="readonly" />
						</td>
						<td align="center" width="220">
							<input type="text" name="NomCuenta" id="NomCuenta_<?=$nro_distribucion?>" value="<?=($field_distribucion['NomCuenta'])?>" style="width:99%;" class="cell2" readonly="readonly" />
						</td>
						<td align="center" width="80">
							<input type="text" name="CodCuentaPub20" id="CodCuentaPub20_<?=$nro_distribucion?>" value="<?=$field_distribucion['CodCuentaPub20']?>" maxlength="13" style="width:99%; text-align:center;" class="cell2" readonly="readonly" />
						</td>
						<td align="center" width="220">
							<input type="text" name="NomCuentaPub20" id="NomCuentaPub20_<?=$nro_distribucion?>" value="<?=($field_distribucion['NomCuentaPub20'])?>" style="width:99%;" class="cell2" readonly="readonly" />
						</td>
						<td align="center">
							<input type="text" name="CodCentroCosto" id="CodCentroCosto_<?=$nro_distribucion?>" value="<?=$field_distribucion['CodCentroCosto']?>" style="text-align:center;" class="cell" <?=$disabled_distribucion?> />
							<input type="hidden" name="NomCentroCosto" id="NomCentroCosto_<?=$nro_distribucion?>" value="<?=$field_distribucion['NomCentroCosto']?>" />
						</td>
						<td align="center">
							<input type="checkbox" name="FlagNoAfectoIGV" class="FlagNoAfectoIGV" <?=chkFlag($field_distribucion['FlagNoAfectoIGV'])?> onchange="actualizarMontosObligacion();" <?=$cFlagNoAfectoIGV?> disabled="disabled" />
						</td>
						<td align="center">
							<input type="text" name="Monto" value="<?=number_format($field_distribucion['Monto'], 2, ',', '.')?>" style="text-align:right;" class="cell2" onfocus="numeroFocus(this);" onblur="numeroBlur(this);" readonly="readonly" />
						</td>
			            <td align="center">
			                <input type="text" name="detallesCategoriaProg" id="detallesCategoriaProg_<?=$nrodetalles?>" class="cell2 CategoriaProg" style="text-align:center;" value="<?=$field_distribucion['CategoriaProg']?>" readonly />
			                <input type="hidden" name="detallesEjercicio" id="detallesEjercicio_<?=$nrodetalles?>" class="cell2 Ejercicio" style="text-align:center;" value="<?=$field_distribucion['Ejercicio']?>" readonly />
			                <input type="hidden" name="detallesCodPresupuesto" id="detallesCodPresupuesto_<?=$nrodetalles?>" class="cell2 CodPresupuesto" style="text-align:center;" value="<?=$field_distribucion['CodPresupuesto']?>" readonly />
			            </td>
			            <td>
			                <select name="detallesCodFuente" id="detallesCodFuente_<?=$nrodetalles?>" class="cell2 CodFuente" <?=$disabled_ver?>>
			                    <?=loadSelect2("pv_fuentefinanciamiento","CodFuente","Denominacion",$field_distribucion['CodFuente'],11)?>
			                </select>
			            </td>
						<td align="center" width="25">
							<input type="text" name="TipoOrden" value="<?=$field_distribucion['TipoOrden']?>" maxlength="2" style="width:99%; text-align:center;" class="cell2" readonly="readonly" />
						</td>
						<td align="center" width="85">
							<input type="text" name="NroOrden" value="<?=$field_distribucion['NroOrden']?>" maxlength="100" style="width:99%;" class="cell2" readonly="readonly" />
						</td>
						<td align="center">
							<input type="text" name="Referencia" value="<?=$field_distribucion['Referencia']?>" maxlength="25" class="cell2" readonly="readonly" />
						</td>
						<td align="center">
							<input type="text" name="Descripcion" value="<?=($field_distribucion['Descripcion'])?>" maxlength="255" class="cell2" readonly="readonly" />
						</td>
						<td align="center">
							<input type="text" name="CodPersona" id="CodPersona_<?=$nro_distribucion?>" value="<?=$field_distribucion['CodPersona']?>" maxlength="6" style="text-align:center;" class="cell2" readonly="readonly" />
							<input type="hidden" name="NomPersona" id="NomPersona_<?=$nro_distribucion?>" value="<?=$field_distribucion['NomPersona']?>" />
						</td>
						<td align="center">
							<input type="text" name="NroActivo" id="NroActivo_<?=$nro_distribucion?>" value="<?=$field_distribucion['NroActivo']?>" maxlength="15" style="text-align:center;" class="cell2" readonly="readonly" />
						</td>
						<td align="center">
							<input type="checkbox" name="FlagDiferido" disabled="disabled" />
						</td>
					</tr>
					<?php
					$distribucion_total += $field_distribucion['PrecioUnit'];
				}
			}
		}
		echo "||$nro_distribucion";
	}
	//	insertar linea en distribucion
	elseif ($accion == "obligacion_distribucion_insertar") {
		if (!afectaTipoServicio($CodTipoServicio)) { $cFlagNoAfectoIGV = "checked"; $dFlagNoAfectoIGV = "disabled"; }
		if ($FlagPresupuesto == "checked") $disabled_presupuesto = ""; else $disabled_presupuesto = "disabled";
		?>
		<th><?=$nrodetalle?></th>
		<td align="center" width="75">
			<input type="text" name="cod_partida" id="cod_partida_<?=$nrodetalle?>" style="width:99%; text-align:center;" maxlength="12" class="cell cod_partida" onChange="getDescripcionLista2('accion=getDescripcionPartidaDisponible&CodOrganismo='+$('CodOrganismo').val(), this, $('#NomPartida_<?=$nrodetalle?>'));" <?=$disabled_distribucion?> />
		</td>
		<td align="center" width="225">
			<input type="text" name="NomPartida" id="NomPartida_<?=$nrodetalle?>" style="width:99%;" class="cell2" readonly="readonly" />
		</td>
		<td align="center" width="80">
			<input type="text" name="CodCuenta" id="CodCuenta_<?=$nrodetalle?>" maxlength="13" style="width:99%; text-align:center;" class="cell" onChange="getDescripcionLista2('accion=getDescripcionCuenta', this, $('#NomCuenta_<?=$nrodetalle?>'));" />
		</td>
		<td align="center" width="220">
			<input type="text" name="NomCuenta" id="NomCuenta_<?=$nrodetalle?>" style="width:99%;" class="cell2" readonly="readonly" />
		</td>
		<td align="center" width="80">
			<input type="text" name="CodCuentaPub20" id="CodCuentaPub20_<?=$nrodetalle?>" maxlength="13" style="width:99%; text-align:center;" class="cell" onChange="getDescripcionLista2('accion=getDescripcionCuentaPub20', this, $('#NomCuentaPub20_<?=$nrodetalle?>'));" />
		</td>
		<td align="center" width="220">
			<input type="text" name="NomCuentaPub20" id="NomCuentaPub20_<?=$nrodetalle?>" style="width:99%;" class="cell2" readonly="readonly" />
		</td>
		<td align="center">
			<input type="text" name="CodCentroCosto" id="CodCentroCosto_<?=$nrodetalle?>" value="<?=$CodCentroCosto?>" style="text-align:center;" class="cell" onChange="getDescripcionLista2('accion=getDescripcionCCosto', this, $('#NomCentroCosto_<?=$nrodetalle?>'));" />
			<input type="hidden" name="NomCentroCosto" id="NomCentroCosto_<?=$nrodetalle?>" />
		</td>
		<td align="center">
			<input type="checkbox" name="FlagNoAfectoIGV" class="FlagNoAfectoIGV" <?=$cFlagNoAfectoIGV?> <?=$dFlagNoAfectoIGV?> onchange="actualizarMontosObligacion();" />
		</td>
		<td align="center">
			<input type="text" name="Monto" value="0,00" style="text-align:right;" class="cell" onfocus="numeroFocus(this);" onblur="numeroBlur(this);" onchange="actualizarMontosObligacion();" />
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
		<td align="center" width="25">
			<input type="text" name="TipoOrden" maxlength="2" style="width:99%; text-align:center;" class="cell" />
		</td>
		<td align="center" width="85">
			<input type="text" name="NroOrden" maxlength="100" style="width:99%;" class="cell" />
		</td>
		<td align="center">
			<input type="text" name="Referencia" class="cell" maxlength="25" />
		</td>
		<td align="center">
			<input type="text" name="Descripcion" class="cell" maxlength="255" />
		</td>
		<td align="center">
			<input type="text" name="CodPersona" id="CodPersona_<?=$nrodetalle?>" maxlength="6" style="text-align:center;" class="cell" onChange="getDescripcionLista2('accion=getDescripcionPersona', this, $('#NomPersona_<?=$nrodetalle?>'));" />
			<input type="hidden" name="NomPersona" id="NomPersona_<?=$nrodetalle?>" />
		</td>
		<td align="center">
			<input type="text" name="NroActivo" id="NroActivo_<?=$nrodetalle?>" maxlength="15" style="text-align:center;" class="cell2" readonly="readonly" />
		</td>
		<td align="center">
			<input type="checkbox" name="FlagDiferido" />
		</td>
		<?php
	}
	//	insertar linea en facturas
	elseif ($accion == "facturas_insertar") {
		$id = ++$nro_detalle;
		?>
		<tr class="trListaBody" onclick="clk($(this), 'facturas', 'facturas_<?=$id?>');" id="facturas_<?=$id?>">
			<th>
				<?=$nro_detalle?>
			</th>
			<td>
				<input type="text" name="facturas_NroControl[]" style="text-align:center;" class="cell" maxlength="20">
			</td>
			<td>
				<input type="text" name="facturas_NroFactura[]" style="text-align:center;" class="cell" maxlength="20">
			</td>
			<td>
				<input type="text" name="facturas_MontoAfecto[]" id="facturas_MontoAfecto<?=$id?>" style="text-align:right;" class="cell currency" onchange="getMontoImpuesto('<?=$id?>');">
			</td>
			<td>
				<input type="text" name="facturas_MontoNoAfecto[]" id="facturas_MontoNoAfecto<?=$id?>" style="text-align:right;" class="cell currency" onchange="getMontoImpuesto('<?=$id?>');">
			</td>
			<td>
				<input type="text" name="facturas_MontoImpuesto[]" id="facturas_MontoImpuesto<?=$id?>" style="text-align:right;" class="cell currency" onchange="setMontoImpuesto('<?=$id?>');">
			</td>
			<td>
				<input type="text" name="facturas_MontoFactura[]" id="facturas_MontoFactura<?=$id?>" style="text-align:right;" class="cell currency" readonly>
			</td>
			<td>
                <select name="facturas_CodImpuesto[]" id="facturas_CodImpuesto<?=$id?>" class="cell" onchange="getMontoRetencion(this.value, '<?=$id?>');">
                	<option value="">&nbsp;</option>
                	<?=loadSelect2('mastimpuestos','CodImpuesto','Descripcion')?>
                </select>
			</td>
			<td>
				<input type="text" name="facturas_FactorPorcentaje[]" id="facturas_FactorPorcentaje<?=$id?>" style="text-align:right;" class="cell currency" readonly>
			</td>
			<td>
				<input type="text" name="facturas_MontoRetenido[]" id="facturas_MontoRetenido<?=$id?>" style="text-align:right;" class="cell currency">
			</td>
		</tr>
		<?php
	}
	//	obtener porcentaje del impuesto
	elseif ($accion == "getMontoRetencion") {
		$sql = "SELECT * FROM mastimpuestos WHERE CodImpuesto = '$CodImpuesto'";
		$field = getRecord($sql);

		$Base = 0;
		if ($field['FlagImponible'] == 'I') $Base = $MontoImpuesto;
		elseif ($field['FlagImponible'] == 'N') $Base = $MontoAfecto;
		elseif ($field['FlagImponible'] == 'B') $Base = $MontoAfecto + $MontoNoAfecto;
		elseif ($field['FlagImponible'] == 'T') $Base = $MontoAfecto + $MontoImpuesto;
		$MontoRetenido = $Base * $field['FactorPorcentaje'] / 100;

		$jsondata = [
			'FactorPorcentaje' => $field['FactorPorcentaje'],
			'MontoRetenido' => $MontoRetenido,
		];
        echo json_encode($jsondata);
        exit();
	}
	//	obtener porcentaje del impuesto
	elseif ($accion == "getMontoImpuesto") {
		$FactorImpuesto = getPorcentajeIVA($CodTipoServicio);
		$MontoImpuesto = $MontoAfecto * $FactorImpuesto / 100;

		$jsondata = [
			'MontoImpuesto' => $MontoImpuesto,
		];
        echo json_encode($jsondata);
        exit();
	}
	//	insertar linea en adelantos
	elseif ($accion == "adelantos_insertar") {
		$id = ++$nro_detalle;
		$sql = "SELECT * FROM ap_gastoadelanto WHERE CodAdelanto = '$CodAdelanto'";
		$field = getRecords($sql);
		foreach ($field as $f) {
			?>
			<tr class="trListaBody" onclick="clk($(this), 'adelantos', 'adelantos_<?=$id?>');" id="adelantos_<?=$id?>">
				<th>
					<input type="hidden" name="adelantos_CodAdelanto[]" value="<?=$f['CodAdelanto']?>">
					<input type="hidden" name="adelantos_MontoTotal[]" value="<?=$f['MontoTotal']?>">
					<?=$nro_detalle?>
				</th>
				<td align="center"><?=formatFechaDMA($f['FechaDocumento'])?></td>
				<td align="center"><?=$f['CodProveedor']?></td>
				<td align="center"><?=printValores('adelanto-tipo',$f['TipoAdelanto'])?></td>
				<td align="center"><?=$f['NroAdelanto']?></td>
				<td align="right">
					<strong><?=number_format($f['MontoTotal'],2,',','.')?></strong>
				</td>
				<td><?=$f['Descripcion']?></td>
			</tr>
			<?php
		}
	}
}
?>