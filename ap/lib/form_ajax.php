<?php
include("../../lib/fphp.php");
include("fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion-".$AnioActual.$MesActual.$DiaActual."_".$HorasActual.$MinutosActual.$SegundosActual.".sql", "w+");
//	$__archivo = fopen("_"."$modulo-$accion.sql", "w+");
///////////////////////////////////////////////////////////////////////////////
//	PARA AJAX
///////////////////////////////////////////////////////////////////////////////
//	generacion de vouchers
if ($modulo == "generar_vouchers") {
	mysql_query("BEGIN");
	$Creditos = setNumero($Creditos);
	$Debitos = setNumero($Debitos);
	##
	if (formatFechaAMD($FechaVoucher) == "") die("No puede generar el voucher sin la fecha");
	elseif ($Periodo == "") die("No puede generar el voucher sin el periodo");
	else {
		$sql = "SELECT Estado
				FROM ac_controlcierremensual
				WHERE
					TipoRegistro = 'AB' AND
					CodOrganismo = '".$CodOrganismo."' AND
					Periodo = '".$Periodo."'";
		$PeriodoEstado = getVar3($sql);
		if ($PeriodoEstado == "") die("El Periodo <strong>$Periodo</strong> no se ha creado");
	}
	//	genero nuevo voucher
	$NroVoucher = getCodigo("ac_vouchermast", "NroVoucher", 4, "CodOrganismo", $CodOrganismo, "Periodo", $Periodo, "CodVoucher", $CodVoucher, "CodContabilidad", $CodContabilidad);
	$NroInterno = getCodigo("ac_vouchermast", "NroInterno", 10);
	$Voucher = "$CodVoucher-$NroVoucher";
	
	//	inserto voucher
	$sql = "INSERT INTO ac_vouchermast
			SET
				CodOrganismo = '".$CodOrganismo."',
				Periodo = '".$Periodo."',
				Voucher = '".$Voucher."',
				CodContabilidad = '".$CodContabilidad."',
				Prefijo = '".$CodVoucher."',
				NroVoucher = '".$NroVoucher."',
				CodVoucher = '".$CodVoucher."',
				CodDependencia = '".$CodDependencia."',
				CodSistemaFuente = '".$CodSistemaFuente."',
				Creditos = '".$Creditos."',
				Debitos = '".$Debitos."',
				Lineas = '".$Lineas."',
				PreparadoPor = '".$_SESSION["CODPERSONA_ACTUAL"]."',
				FechaPreparacion = '".formatFechaAMD($FechaPreparacion)."',
				AprobadoPor = '".$_SESSION["CODPERSONA_ACTUAL"]."',
				FechaAprobacion = '".formatFechaAMD($FechaAprobacion)."',
				TituloVoucher = '".$ComentariosVoucher."',
				ComentariosVoucher = '".$ComentariosVoucher."',
				FechaVoucher = '".formatFechaAMD($FechaVoucher)."',
				NroInterno = '".$NroInterno."',
				FlagTransferencia = 'N',
				Estado = 'MA',
				CodLibroCont = '".$CodLibroCont."',
				UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
				UltimaFecha = NOW()";
	execute($sql);
	
	//	inserto los detalles
	$linea = split(";char:tr;", $detalles);
	foreach ($linea as $registro) {
		list($_Linea, $_CodCuenta, $_Descripcion, $_MontoVoucher, $_CodPersona, $_ReferenciaTipoDocumento, $_ReferenciaNroDocumento, $_CodCentroCosto, $_FechaVoucher) = split(';char:td;', $registro);
		//	inserto detalle
		$sql = "INSERT INTO ac_voucherdet
				SET
					CodOrganismo = '".$CodOrganismo."',
					Periodo = '".$Periodo."',
					Voucher = '".$Voucher."',
					CodContabilidad = '".$CodContabilidad."',
					Linea = '".$_Linea."',
					CodCuenta = '".$_CodCuenta."',
					MontoVoucher = '".$_MontoVoucher."',
					MontoPost = '".$_MontoVoucher."',
					CodPersona = '".$_CodPersona."',
					FechaVoucher = '".formatFechaAMD($_FechaVoucher)."',
					CodCentroCosto = '".$_CodCentroCosto."',
					ReferenciaTipoDocumento = '".$_ReferenciaTipoDocumento."',
					ReferenciaNroDocumento = '".$_ReferenciaNroDocumento."',
					NroCheque = '".$CodTipoPago."-".$NroPago."',
					Descripcion = '".$_Descripcion."',
					Estado = 'MA',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
	}
	
	//	si genere el voucher desde generar voucher de transacciones bancarias
	if ($accion == "transacciones") {
		//	actualizo transaccion banco
		$sql = "UPDATE ap_bancotransaccion
				SET
					Voucher = '".$Voucher."',
					VoucherPeriodo = '".$Periodo."',
					FlagContabilizacionPendiente = 'N',
					Estado = 'CO'
				WHERE NroTransaccion = '".$NroTransaccion."'";
		execute($sql);
	}
	
	//	si genere el voucher desde generar voucher de transacciones bancarias
	elseif ($accion == "transacciones-pub20") {
		//	actualizo transaccion banco
		$sql = "UPDATE ap_bancotransaccion
				SET
					VoucherPub20 = '".$Voucher."',
					VoucherPeriodoPub20 = '".$Periodo."',
					FlagContPendientePub20 = 'N',
					Estado = 'CO'
				WHERE NroTransaccion = '".$NroTransaccion."'";
		execute($sql);
	}
	
	//	si genere el voucher desde generar voucher de obligaciones
	elseif ($accion == "provision") {
		//	actualizo obligacion
		$sql = "UPDATE ap_obligaciones
				SET
					FlagContabilizacionPendiente = 'N',
					Voucher = '".$Voucher."',
					VoucherPeriodo = '".$Periodo."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodProveedor = '".$CodProveedor."' AND
					CodTipoDocumento = '".$CodTipoDocumento."' AND
					NroDocumento = '".$NroDocumento."'";
		execute($sql);
	}
	
	//	si genere el voucher desde generar voucher de obligaciones
	elseif ($accion == "provision-pub20") {
		//	actualizo obligacion
		$sql = "UPDATE ap_obligaciones
				SET
					FlagContPendientePub20 = 'N',
					VoucherPub20 = '".$Voucher."',
					VoucherPeriodoPub20 = '".$Periodo."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodProveedor = '".$CodProveedor."' AND
					CodTipoDocumento = '".$CodTipoDocumento."' AND
					NroDocumento = '".$NroDocumento."'";
		execute($sql);
	}
	
	//	si genere el voucher desde generar voucher de obligaciones
	elseif ($accion == "ordenacion-pub20") {
		//	actualizo orden de pago
		$sql = "UPDATE ap_ordenpago
				SET
					FlagContPendienteOrdPub20 = 'N',
					Voucher = '".$Voucher."',
					VoucherPeriodo = '".$Periodo."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodProveedor = '".$CodProveedor."' AND
					CodTipoDocumento = '".$CodTipoDocumento."' AND
					NroDocumento = '".$NroDocumento."'";
		execute($sql);
	}
	
	//	si genere el voucher desde generar voucher de pagos
	elseif ($accion == "pagos") {
		//	actualizo pago
		$sql = "UPDATE ap_pagos
				SET
					FlagContabilizacionPendiente = 'N',
					VoucherPago = '".$Voucher."',
					VoucherPeriodo = '".$Periodo."'
				WHERE
					NroProceso = '".$NroProceso."' AND
					CodTipoPago = '".$CodTipoPago."' AND
					NroCuenta = '".$NroCuenta."'";
		execute($sql);
	}
	
	//	si genere el voucher desde generar voucher de pagos
	elseif ($accion == "pagos-pub20") {
		//	actualizo pago
		$sql = "UPDATE ap_pagos
				SET
					FlagContPendientePub20 = 'N',
					VoucherPagoPub20 = '".$Voucher."',
					PeriodoPagoPub20 = '".$Periodo."'
				WHERE
					NroProceso = '".$NroProceso."' AND
					CodTipoPago = '".$CodTipoPago."' AND
					NroCuenta = '".$NroCuenta."'";
		execute($sql);
	}
	mysql_query("COMMIT");
}

//	orden de pago
elseif ($modulo == "orden_pago") {
	$Concepto = changeUrl($Concepto);
	$MotivoAnulacion = changeUrl($MotivoAnulacion);
	//	modificar
	if ($accion == "modificar") {
		mysql_query("BEGIN");
		//-------------------
		list($DiaOrden, $MesOrden, $AnioOrden) = split("[./-]", $FechaOrdenPago);
		if ("$AnioOrden-$MesOrden" != $Periodo) die("No se puede modificar el periodo de la orden.");
		$Periodo = "$AnioOrden-$MesOrden";
		$Anio = $AnioOrden;
		//	actualizo orden
		$sql = "UPDATE ap_ordenpago
				SET
					FechaOrdenPago = '".formatFechaAMD($FechaOrdenPago)."',
					CodTipoPago = '".$CodTipoPago."',
					NroCuenta = '".$NroCuenta."',
					PreparadoPor = '".$PreparadoPor."',
					FechaPreparado = '".formatFechaAMD($FechaPreparado)."',
					RevisadoPor = '".$RevisadoPor."',
					FechaRevisado = '".formatFechaAMD($FechaRevisado)."',
					AprobadoPor = '".$AprobadoPor."',
					Concepto = '".$Concepto."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					NroOrden = '".$NroOrden."'";
		execute($sql);
		//	actualizo obligacion
		$sql = "UPDATE ap_obligaciones
				SET
					IngresadoPor = '".$PreparadoPor."',
					FechaPreparacion = '".formatFechaAMD($FechaPreparado)."',
					RevisadoPor = '".$RevisadoPor."',
					FechaRevision = '".formatFechaAMD($FechaRevisado)."',
					Periodo = '".substr(formatFechaAMD($FechaRevisado),0,7)."',
					AprobadoPor = '".$AprobadoPor."',
					FechaAprobado = '".formatFechaAMD($FechaAprobado)."',
					CodTipoPago = '".$CodTipoPago."',
					NroCuenta = '".$NroCuenta."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodProveedor = '".$CodProveedor."' AND
					CodTipoDocumento = '".$CodTipoDocumento."' AND
					NroDocumento = '".$NroDocumento."'";
		execute($sql);
		//	actualizo orden
		$sql = "UPDATE ap_ordenpagodistribucion
				SET
					Periodo = '".substr(formatFechaAMD($FechaOrdenPago),0,7)."',
					FechaEjecucion = '".formatFechaAMD($FechaOrdenPago)."'
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					NroOrden = '".$NroOrden."'";
		execute($sql);
		//	
		$sql = "SELECT *
				FROM ap_ordenpago
				WHERE
					Anio = '$Anio'
					AND CodOrganismo = '$CodOrganismo'
					AND NroOrden = '$NroOrden'";
		$field_op = getRecord($sql);
		//	
		$sql = "UPDATE ap_pagos
				SET
					CodTipoPago = '$CodTipoPago',
					NroCuenta = '$NroCuenta'
				WHERE
					NroProceso = '$field_op[NroProceso]'
					AND Secuencia = '$field_op[Secuencia]'";
		execute($sql);
		//-------------------
		mysql_query("COMMIT");
	}
	//	pre-pago
	elseif ($accion == "prepago") {
		mysql_query("BEGIN");
		list($DiaPago, $MesPago, $AnioPago) = split("[./-]", $FechaPago);
		$Periodo = "$AnioPago-$MesPago";
		$Anio = $AnioPago;
		//	consulto orden
		$NroProceso = getCodigo("ap_pagos", "NroProceso", 6);
		$sql = "SELECT
					op.CodProveedor,
					op.CodTipoDocumento,
					op.NroDocumento,
					op.CodTipoPago,
					op.CodOrganismo,
					op.NroCuenta,
					op.NroOrden,
					op.Anio,
					op.NomProveedorPagar,
					op.FechaProgramada,
					op.MontoTotal,
					op.RevisadoPor,
					op.AprobadoPor,
					o.MontoObligacion,
					o.MontoImpuestoOtros
				FROM
					ap_ordenpago op
					INNER JOIN ap_obligaciones o ON (op.CodProveedor = o.CodProveedor AND
													 op.CodTipoDocumento = o.CodTipoDocumento AND
													 op.NroDocumento = o.NroDocumento)
				WHERE
					op.Anio = '".$Anio."' AND
					op.CodOrganismo = '".$CodOrganismo."' AND
					op.NroOrden = '".$NroOrden."'";
		$field_op = getRecord($sql);
		//	actualizo orden de pago
		$sql = "UPDATE ap_ordenpago
				SET
					Estado = 'GE',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodProveedor = '".$field_op['CodProveedor']."' AND
					CodTipoDocumento = '".$field_op['CodTipoDocumento']."' AND
					NroDocumento = '".$field_op['NroDocumento']."' AND
					Estado = 'PE'";
		execute($sql);
		//	inserto pago
		$sql = "INSERT INTO ap_pagos
				SET
					NroProceso = '".$NroProceso."',
					Secuencia = '1',
					CodTipoPago = '".$field_op['CodTipoPago']."',
					CodOrganismo = '".$field_op['CodOrganismo']."',
					NroCuenta = '".$field_op['NroCuenta']."',
					CodProveedor = '".$field_op['CodProveedor']."',
					NroOrden = '".$field_op['NroOrden']."',
					Anio = '".$field_op['Anio']."',
					NomProveedorPagar = '".$field_op['NomProveedorPagar']."',
					MontoPago = '".$field_op['MontoTotal']."',
					MontoRetenido = '".$field_op['MontoImpuestoOtros']."',
					FechaPago = '".formatFechaAMD($FechaPago)."',
					OrigenGeneracion = 'A',
					Estado = 'GE',
					EstadoEntrega = 'C',
					EstadoChequeManual = '',
					FlagContabilizacionPendiente = 'S',
					FlagNegociacion = 'N',
					FlagNoNegociable = 'N',
					FlagCobrado = 'N',
					FlagCertificadoImpresion = 'N',
					FlagPagoDiferido = 'N',
					Periodo = '".$Periodo."',
					GeneradoPor = '".$_SESSION["CODPERSONA_ACTUAL"]."',
					ConformadoPor = '".$field_op['RevisadoPor']."',
					AprobadoPor = '".$field_op['AprobadoPor']."',
					RevisadoPor = '".$_PARAMETRO['FIRMAOP3']."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		mysql_query("COMMIT");
	}
	//	pre-pago
	elseif ($accion == "preparar_prepago") {
		mysql_query("BEGIN");
		list($DiaPago, $MesPago, $AnioPago) = split("[./-]", $FechaPago);
		$Periodo = "$AnioPago-$MesPago";
		$Anio = $AnioPago;
		//	consulto orden
		$NroProceso = getCodigo("ap_pagos", "NroProceso", 6);
		$MontoPago = 0;
		$MontoRetenido = 0;
    	foreach ($orden as $registro) {
			list($Anio, $CodOrganismo, $NroOrden) = split("[_]", $registro);
			$sql = "SELECT
						op.CodProveedor,
						op.CodTipoDocumento,
						op.NroDocumento,
						op.CodTipoPago,
						op.CodOrganismo,
						op.NroCuenta,
						op.NroOrden,
						op.Anio,
						op.NomProveedorPagar,
						op.FechaProgramada,
						op.MontoTotal,
						op.RevisadoPor,
						op.AprobadoPor,
						o.MontoObligacion,
						o.MontoImpuestoOtros
					FROM
						ap_ordenpago op
						INNER JOIN ap_obligaciones o ON (op.CodProveedor = o.CodProveedor AND
														 op.CodTipoDocumento = o.CodTipoDocumento AND
														 op.NroDocumento = o.NroDocumento)
					WHERE
						op.Anio = '".$Anio."' AND
						op.CodOrganismo = '".$CodOrganismo."' AND
						op.NroOrden = '".$NroOrden."'";
			$field_op = getRecord($sql);
			//	actualizo orden de pago
			$sql = "UPDATE ap_ordenpago
					SET
						NroProceso = '".$NroProceso."',
						Secuencia = '1',
						Estado = 'GE',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()
					WHERE
						CodProveedor = '".$field_op['CodProveedor']."' AND
						CodTipoDocumento = '".$field_op['CodTipoDocumento']."' AND
						NroDocumento = '".$field_op['NroDocumento']."' AND
						Estado = 'PE'";
			execute($sql);
			$MontoPago += $field_op['MontoTotal'];
			$MontoRetenido += $field_op['MontoImpuestoOtros'];
		}
		//	inserto pago
		$sql = "INSERT INTO ap_pagos
				SET
					NroProceso = '".$NroProceso."',
					Secuencia = '1',
					CodTipoPago = '".$field_op['CodTipoPago']."',
					CodOrganismo = '".$field_op['CodOrganismo']."',
					NroCuenta = '".$field_op['NroCuenta']."',
					CodProveedor = '".$field_op['CodProveedor']."',
					NroOrden = '".$field_op['NroOrden']."',
					Anio = '".$field_op['Anio']."',
					NomProveedorPagar = '".$field_op['NomProveedorPagar']."',
					MontoPago = '".$MontoPago."',
					MontoRetenido = '".$MontoRetenido."',
					FechaPago = '".formatFechaAMD($FechaPago)."',
					OrigenGeneracion = 'A',
					Estado = 'GE',
					EstadoEntrega = 'C',
					EstadoChequeManual = '',
					FlagContabilizacionPendiente = 'S',
					FlagNegociacion = 'N',
					FlagNoNegociable = 'N',
					FlagCobrado = 'N',
					FlagCertificadoImpresion = 'N',
					FlagPagoDiferido = 'N',
					Periodo = '".$Periodo."',
					GeneradoPor = '".$_SESSION["CODPERSONA_ACTUAL"]."',
					RevisadoPor = '".getPersonaUnidadEjecutora($_PARAMETRO["CATADM"])."',
					ConformadoPor = '".getPersonaUnidadEjecutora($_PARAMETRO["CATADM"])."',
					AprobadoPor = '".getPersonaUnidadEjecutora($_PARAMETRO["CATMAX"])."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		mysql_query("COMMIT");
	}
	//	imprimir/transferir
	elseif ($accion == "transferir") {
		mysql_query("BEGIN");
		//	-----------------
		//	valido saldo
		if (setNumero($MontoPago) > setNumero($SaldoActual)) die("Sin disponibilidad financiera la cuenta bancaria.");
		//	valido nro de factura
		elseif ($FlagFacturaPendiente == 'S' && ($FechaFactura == '' || $NroControl == '' || $NroFactura == '')) die('Información de la Factura obligatoria.');
		//	
		list($DiaPago, $MesPago, $AnioPago) = split("[./-]", $FechaPago);
		$Periodo = "$AnioPago-$MesPago";
		$Anio = $AnioPago;
		//	consulto pagos
		if (!trim($NroPago)) $NroPago = getNroOrdenPago($CodTipoPago, $NroCuenta);

		$Glosa = "";
		$sql = "SELECT op.Concepto
				FROM
					ap_pagos p
					INNER JOIN ap_ordenpago op ON (op.NroProceso = p.NroProceso AND op.Secuencia = p.Secuencia)
				WHERE
					p.NroProceso = '".$NroProceso."' AND
					p.Secuencia = '".$Secuencia."'";
		$field_glosa = getRecords($sql);
		foreach ($field_glosa as $fg) {
			if ($Glosa) $Glosa .= ", ";
			$Glosa .= $fg['Concepto'];
		}

		$sql = "SELECT
					p.NroProceso,
					p.Secuencia,
					p.NroCuenta,
					p.CodTipoPago,
					p.CodProveedor,
					p.MontoPago,
					op.Anio,
					op.CodOrganismo,
					op.NroOrden,
					op.CodTipoDocumento,
					op.NroDocumento,
					op.Concepto,
					op.CodCentroCosto,
					op.Periodo
				FROM
					ap_pagos p
					INNER JOIN ap_ordenpago op ON (p.NroProceso = op.NroProceso AND
												   p.Secuencia = op.Secuencia)
				WHERE
					p.NroProceso = '".$NroProceso."' AND
					p.Secuencia = '".$Secuencia."'
				GROUP BY NroProceso";
		$fop = getRecords($sql);
		foreach ($fop as $field_op) {
			//	consulto
			$sql = "SELECT TipoTransaccion, FlagVoucher
					FROM ap_bancotipotransaccion
					WHERE CodTipoTransaccion = '".$_PARAMETRO["TRANSPAGO"]."'";
			$field_flag = getRecord($sql);
			if ($field_flag['TipoTransaccion'] == "I") $signo = "1";
			elseif ($field_flag['TipoTransaccion'] == "E") $signo = "-1";
			//	inserto transaccion
			$NroTransaccion = getCodigo("ap_bancotransaccion", "NroTransaccion", 5);
			$sql = "INSERT INTO ap_bancotransaccion
					SET
						NroTransaccion = '".$NroTransaccion."',
						Secuencia = '1',
						CodOrganismo = '".$field_op['CodOrganismo']."',
						CodTipoTransaccion = '".$_PARAMETRO["TRANSPAGO"]."',
						TipoTransaccion = '".$field_flag['TipoTransaccion']."',
						NroCuenta = '".$field_op['NroCuenta']."',
						CodTipoDocumento = '".$field_op['CodTipoDocumento']."',
						CodProveedor = '".$field_op['CodProveedor']."',
						CodCentroCosto = '".$field_op['CodCentroCosto']."',
						PreparadoPor = '".$_SESSION["CODPERSONA_ACTUAL"]."',
						FechaPreparacion = NOW(),
						FechaTransaccion = '".formatFechaAMD($FechaPago)."',
						PeriodoContable = '".$Periodo."',
						Monto = '".($field_op['MontoPago']*$signo)."',
						Comentarios = '".$Glosa."',
						PagoNroProceso = '".$field_op['NroProceso']."',
						PagoSecuencia = '".$field_op['Secuencia']."',
						NroPago = '".$NroPago."',
						FlagConciliacion = 'N',
						FlagAutomatico = 'S',
						Estado = 'AP',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}

		$sql = "SELECT
					p.NroProceso,
					p.Secuencia,
					p.NroCuenta,
					p.CodTipoPago,
					p.CodProveedor,
					p.MontoPago,
					op.Anio,
					op.CodOrganismo,
					op.NroOrden,
					op.CodTipoDocumento,
					op.NroDocumento,
					op.Concepto,
					op.CodCentroCosto,
					op.Periodo
				FROM
					ap_pagos p
					INNER JOIN ap_ordenpago op ON (p.NroProceso = op.NroProceso AND
												   p.Secuencia = op.Secuencia)
				WHERE
					p.NroProceso = '".$NroProceso."' AND
					p.Secuencia = '".$Secuencia."'
				ORDER BY Secuencia";
		$fop = getRecords($sql);
		foreach ($fop as $field_op) {
			if ($FlagPagoParcial == 'S') {
				$sql = "UPDATE ap_obligaciones
						SET MontoPagoParcial = MontoPagoParcial + ".setNumero($MontoPago)."
						WHERE
							CodProveedor = '".$field_op['CodProveedor']."' AND
							CodTipoDocumento = '".$field_op['CodTipoDocumento']."' AND
							NroDocumento = '".$field_op['NroDocumento']."'";
				execute($sql);
				##	
				$SaldoPendiente = getVar3("SELECT (MontoAfecto + MontoNoAfecto - MontoPagoParcial) FROM ap_obligaciones WHERE CodProveedor = '".$field_op['CodProveedor']."' AND CodTipoDocumento = '".$field_op['CodTipoDocumento']."' AND NroDocumento = '".$field_op['NroDocumento']."'");
				##	
			}
			##	
			if (($FlagPagoParcial == 'S' && $SaldoPendiente == 0) || $FlagPagoParcial != 'S') {
				//	actualizo obligacion
				$sql = "UPDATE ap_obligaciones
						SET
							FechaPago = '".formatFechaAMD($FechaPago)."',
							NroPago = '".$NroPago."',
							NroProceso = '".$NroProceso."',
							ProcesoSecuencia = '".$Secuencia."',
							Estado = 'PA',
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()
						WHERE
							CodProveedor = '".$field_op['CodProveedor']."' AND
							CodTipoDocumento = '".$field_op['CodTipoDocumento']."' AND
							NroDocumento = '".$field_op['NroDocumento']."'";
				execute($sql);
			}
			//	si viene de nomina
			$FlagNomina = getVar2("ap_obligaciones", "FlagNomina", array("CodProveedor","CodTipoDocumento","NroDocumento"), array($field_op['CodProveedor'],$field_op['CodTipoDocumento'],$field_op['NroDocumento']));
			if ($FlagNomina == "S") {
				//	consulto datos de nomina
				$sql = "SELECT
							CodTipoNom,
							Periodo,
							PeriodoNomina,
							CodTipoProceso
						FROM pr_obligaciones
						WHERE
							CodProveedor = '".$field_op['CodProveedor']."' AND
							CodTipoDocumento = '".$field_op['CodTipoDocumento']."' AND
							NroDocumento = '".$field_op['NroDocumento']."'";
				$field_proceso = getRecord($sql);
				//	actualizo pr obligacion
				$sql = "UPDATE pr_obligaciones
						SET
							FechaPago = '".formatFechaAMD($FechaPago)."',
							NroPago = '".$NroPago."',
							Estado = 'PA',
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()
						WHERE
							CodProveedor = '".$field_op['CodProveedor']."' AND
							CodTipoDocumento = '".$field_op['CodTipoDocumento']."' AND
							NroDocumento = '".$field_op['NroDocumento']."'";
				execute($sql);
				//	actualizo payroll
				$sql = "UPDATE pr_tiponominaempleado
						SET
							FechaPago = '".formatFechaAMD($FechaPago)."',
							EstadoPago = 'PA'
						WHERE
							CodProveedor = '".$field_op['CodProveedor']."' AND
							CodTipoDocumento = '".$field_op['CodTipoDocumento']."' AND
							NroDocumento = '".$field_op['NroDocumento']."'";
				execute($sql);
				##	prestaciones
				if ($field_proceso['CodTipoProceso'] == "PRS") {
					//	actualizo prestaciones
					$sql = "UPDATE pr_liquidacionempleado
							SET
								MontoPagado = MontoPagado + ".setNumero($MontoPago).",
								Fliquidacion = '".formatFechaAMD($FechaPago)."',
								FechaPago = '".formatFechaAMD($FechaPago)."',
								EstadoPago = 'PA'
							WHERE
								CodProveedor = '".$field_op['CodProveedor']."' AND
								CodTipoDocumento = '".$field_op['CodTipoDocumento']."' AND
								NroDocumento = '".$field_op['NroDocumento']."'";
					execute($sql);
					##	actualizo empleado
					$sql = "UPDATE mastempleado
							SET Fliquidacion = '".formatFechaAMD($FechaPago)."'
							WHERE
								CodPersona IN (SELECT CodPersona
											   FROM pr_tiponominaempleado
											   WHERE
													CodProveedor = '".$field_op['CodProveedor']."' AND
													CodTipoDocumento = '".$field_op['CodTipoDocumento']."' AND
													NroDocumento = '".$field_op['NroDocumento']."')";
					execute($sql);
				}
				elseif ($field_proceso['CodTipoProceso'] == "APR") {
					##	consulto todos los adelantos
					$sql = "SELECT SUM(TotalNeto)
							FROM pr_tiponominaempleado
							WHERE
								CodPersona = '".$field_op['CodProveedor']."' AND
								CodTipoProceso = '".$field_proceso['CodTipoProceso']."' AND
								EstadoPago = 'PA'
							GROUP BY CodPersona";
					$TotalDescuento = getVar3($sql);
					##	actualizo
					$sql = "UPDATE pr_liquidacionempleado
							SET
								TotalDescuento = ".floatval($TotalDescuento).",
								UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
								UltimaFecha = NOW()
							WHERE
								CodPersona = '".$field_op['CodProveedor']."' AND
								EstadoPago = 'PE'";
					execute($sql);
					##	actualizo
					$sql = "UPDATE pr_liquidacionempleado
							SET
								TotalNeto = TotalIngresos - TotalEgresos - TotalDescuento,
								TotalPrestaciones = (TotalIngresos - TotalEgresos - TotalDescuento) + MontoIntereses
							WHERE
								CodPersona = '".$field_op['CodProveedor']."' AND
								EstadoPago = 'PE'";
					execute($sql);
				}
				//	verifico si se completaron todos
				$sql = "SELECT Estado
						FROM pr_obligaciones
						WHERE
							CodTipoNom = '".$field_proceso['CodTipoNom']."' AND
							Periodo = '".$field_proceso['PeriodoNomina']."' AND
							CodTipoProceso = '".$field_proceso['CodTipoProceso']."' AND
							CodOrganismo = '".$field_op['CodOrganismo']."' AND
							Estado <> 'PA'";
				$field_tne_verifico = getRecord($sql);
				if (!count($field_tne_verifico)) {
					//	consulto los datos de nomina
					$sql = "SELECT
								CodTipoNom,
								PeriodoNomina AS Periodo,
								CodOrganismo,
								CodTipoProceso
							FROM pr_obligaciones
							WHERE
								CodProveedor = '".$field_op['CodProveedor']."' AND
								CodTipoDocumento = '".$field_op['CodTipoDocumento']."' AND
								NroDocumento = '".$field_op['NroDocumento']."'";
					$field_tne = getRecord($sql);
					//	actualizo estado
					$sql = "UPDATE pr_procesoperiodo
							SET
								FechaPago = '".formatFechaAMD($FechaPago)."',
								EstadoPago = 'PA',
								FlagPagado = 'S'
							WHERE
								CodTipoNom = '".$field_tne['CodTipoNom']."' AND
								Periodo = '".$field_tne['Periodo']."' AND
								CodOrganismo = '".$field_tne['CodOrganismo']."' AND
								CodTipoProceso = '".$field_tne['CodTipoProceso']."'";
					//execute($sql);
				}
				//	si es vacaciones
				if ($field_tne['CodTipoProceso'] == $_PARAMETRO['PROCESOBVC']) {
					//	inserto los dias pagados en vacaciones pagadas
					$sql = "SELECT
								tne.CodPersona,
								tne.CodProveedor,
								tne.Periodo,
								SUBSTRING(tne.Periodo, 1, 4) AS Anio,
								tne.CodTipoNom,
								tne.CodTipoDocumento,
								tne.NroDocumento,
								tne.FechaPago,
								tnec.Cantidad,
								pp.FechaDesde,
								pp.FechaHasta
							FROM
								pr_tiponominaempleado tne
								INNER JOIN pr_tiponominaempleadoconcepto tnec ON (tnec.CodTipoNom = tne.CodTipoNom AND
																				  tnec.Periodo = tne.Periodo AND
																				  tnec.CodPersona = tne.CodPersona AND
																				  tnec.CodOrganismo = tne.CodOrganismo AND
																				  tnec.CodTipoProceso = tne.CodTipoProceso)
								INNER JOIN pr_procesoperiodo pp ON (pp.CodTipoNom = tne.CodTipoNom AND
																	pp.Periodo = tne.Periodo AND
																	pp.CodOrganismo = tne.CodOrganismo AND
																	pp.CodTipoProceso = tne.CodTipoProceso)
							WHERE
								tne.CodProveedor = '".$field_op['CodProveedor']."' AND
								tne.CodTipoDocumento = '".$field_op['CodTipoDocumento']."' AND
								tne.NroDocumento = '".$field_op['NroDocumento']."' AND
								tnec.CodConcepto = '".$_PARAMETRO['CONCEPTOBVC']."'";
					$fd = getRecords($sql);
					foreach ($fd as $field_detalle) {
						$Anio = $field_detalle['Anio'] - 1;
						$_NroPeriodo = getVar2("rh_vacacionperiodo", "NroPeriodo", array("CodPersona","Anio"), array($field_detalle['CodPersona'],$Anio));
						if ($_NroPeriodo == "") die("No se han actualizado los periodos vacacionales del empleado.");
						$_Secuencia = getCodigo("rh_vacacionpago", "Secuencia", 2, "CodPersona", $field_detalle['CodPersona'], "NroPeriodo", $_NroPeriodo, "CodTipoNom", $field_detalle['CodTipoNom']);	$_Secuencia = intval($_Secuencia);
						//	inserto
						$sql = "INSERT INTO rh_vacacionpago
								SET
									CodPersona = '".$field_detalle['CodPersona']."',
									NroPeriodo = '".$_NroPeriodo."',
									Secuencia = '".$_Secuencia."',
									CodTipoNom = '".$field_detalle['CodTipoNom']."',
									DiasPago = '".$field_detalle['Cantidad']."',
									Periodo = '".$field_detalle['Periodo']."',
									CodConcepto = '".$_PARAMETRO['CONCEPTOBVC']."',
									FechaInicio = '".$field_detalle['FechaDesde']."',
									FechaFin = '".$field_detalle['FechaHasta']."',
									CodProveedor = '".$field_detalle['CodProveedor']."',
									CodTipoDocumento = '".$field_detalle['CodTipoDocumento']."',
									NroDocumento = '".$field_detalle['NroDocumento']."',
									FechaPago = '".$field_detalle['FechaPago']."',
									UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
									UltimaFecha = NOW()";
						execute($sql);
					}
					actualizarPeriodosVacacionales($field_detalle['CodPersona']);
				}
			}
			//	actualizo orden de pago
			if ($FlagPagoParcial == 'S' && $SaldoPendiente > 0) $EstadoOrden = 'PP'; else $EstadoOrden = 'PA';
			$sql = "UPDATE ap_ordenpago
					SET
						Estado = '".$EstadoOrden."',
						NroPago = '".$NroPago."',
						FechaTransferencia = '".formatFechaAMD($FechaPago)."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()
					WHERE
						Anio = '".$field_op['Anio']."' AND
						CodOrganismo = '".$field_op['CodOrganismo']."' AND
						NroOrden = '".$field_op['NroOrden']."'";
			execute($sql);
			//	actualizo orden de pago distribucion
			$sql = "UPDATE ap_ordenpagodistribucion
					SET
						FechaEjecucion = '".formatFechaAMD($FechaPago)."',
						Estado = 'PA',
						Periodo = '".$Periodo."',
						PagoNroProceso = '".$NroProceso."',
						PagoSecuencia = '".$Secuencia."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()
					WHERE
						Anio = '".$field_op['Anio']."' AND
						CodOrganismo = '".$field_op['CodOrganismo']."' AND
						NroOrden = '".$field_op['NroOrden']."' AND
						Estado = 'PE'";
			execute($sql);
			//	actualizo orden de pago distribucion
			$sql = "UPDATE ap_pagosparciales
					SET
						Estado = 'PA',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()
					WHERE
						Anio = '".$field_op['Anio']."' AND
						CodOrganismo = '".$field_op['CodOrganismo']."' AND
						NroOrden = '".$field_op['NroOrden']."' AND
						Estado = 'PE'";
			execute($sql);
			//	actualizo pagos
			$sql = "UPDATE ap_pagos
					SET
						FechaPago = '".formatFechaAMD($FechaPago)."',
						Periodo = '".$Periodo."',
						NroPago = '".$NroPago."',
						Estado = 'IM',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()
					WHERE
						NroProceso = '".$NroProceso."' AND
						Secuencia = '".$Secuencia."'";
			execute($sql);
			//	actualizo ultimo numero de pago
			$sql = "UPDATE ap_ctabancariatipopago
					SET
						UltimoNumero = '".$NroPago."',	
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()
					WHERE
						NroCuenta = '".$NroCuenta."' AND
						CodTipoPago = '".$CodTipoPago."'";
			execute($sql);
		}
		//	consulto e inserto las retenciones
		$sql = "SELECT
					op.CodOrganismo,
					op.Anio,
					op.NroOrden,
					op.CodProveedor,
					op.CodTipoDocumento,
					op.NroDocumento,
					oi.NroControl,
					oi.NroFactura,
					o.FechaRegistro,
					oi.FechaFactura,
					oi.MontoRetenido,
					oi.FactorPorcentaje AS Porcentaje,
					oi.MontoFactura,
					oi.MontoImpuesto,
					oi.MontoAfecto,
					oi.MontoNoAfecto,
					oi.CodImpuesto,
					i.TipoComprobante
				FROM
					ap_ordenpago op
					INNER JOIN ap_obligaciones o ON (o.CodProveedor = op.CodProveedor AND
													 o.CodTipoDocumento = op.CodTipoDocumento AND
													 o.NroDocumento = op.NroDocumento)
					INNER JOIN ap_obligacionesfacturas oi ON (o.CodProveedor = oi.CodProveedor AND
													  		  o.CodTipoDocumento = oi.CodTipoDocumento AND
													  		  o.NroDocumento = oi.NroDocumento)
					INNER JOIN mastimpuestos i ON (oi.CodImpuesto = i.CodImpuesto)
				WHERE
					op.NroProceso = '".$NroProceso."' AND
					op.Secuencia = '".$Secuencia."'
				ORDER BY TipoComprobante, oi.Secuencia";
		$fr = getRecords($sql);
		if (!count($fr)) {
			$sql = "SELECT
						op.CodOrganismo,
						op.Anio,
						op.NroOrden,
						op.CodProveedor,
						op.CodTipoDocumento,
						op.NroDocumento,
						o.NroControl,
						o.NroFactura,
						o.FechaRegistro,
						o.FechaFactura,
						oi.MontoImpuesto AS MontoRetenido,
						oi.FactorPorcentaje AS Porcentaje,
						(o.MontoAfecto + o.MontoNoAfecto + o.MontoImpuesto) AS MontoFactura,
						o.MontoImpuesto,
						o.MontoAfecto,
						o.MontoNoAfecto,
						i.CodImpuesto,
						i.TipoComprobante
					FROM
						ap_ordenpago op
						INNER JOIN ap_obligaciones o ON (o.CodProveedor = op.CodProveedor AND
														 o.CodTipoDocumento = op.CodTipoDocumento AND
														 o.NroDocumento = op.NroDocumento)
						INNER JOIN ap_obligacionesimpuesto oi ON (o.CodProveedor = oi.CodProveedor AND
														  		  o.CodTipoDocumento = oi.CodTipoDocumento AND
														  		  o.NroDocumento = oi.NroDocumento)
						INNER JOIN mastimpuestos i ON (oi.CodImpuesto = i.CodImpuesto)
					WHERE
						op.NroProceso = '".$NroProceso."' AND
						op.Secuencia = '".$Secuencia."'
					ORDER BY TipoComprobante";
			$fr = getRecords($sql);
		}
		$Grupo = '';
		foreach ($fr as $field_retenciones) {
			if ($FlagFacturaPendiente != 'S') {
				$NroDocumento = $field_retenciones['NroDocumento'];
				$NroControl = $field_retenciones['NroControl'];
				$NroFactura = $field_retenciones['NroFactura'];
				$FechaFactura = formatFechaDMA($field_retenciones['FechaFactura']);
			}
			//	consulto si existe la retencion 
			if ($Grupo != $field_retenciones['TipoComprobante']) $SecuenciaRetencion = 0;
			++$SecuenciaRetencion;
			$sql = "SELECT *
					FROM ap_retenciones
					WHERE
						CodOrganismo = '".$field_retenciones['CodOrganismo']."' AND
						NroOrden = '".$field_retenciones['NroOrden']."' AND
						AnioOrden = '".$field_retenciones['Anio']."' AND
						TipoComprobante = '".$field_retenciones['TipoComprobante']."' AND
						Secuencia = '".$SecuenciaRetencion."' AND 
						Estado <> 'AN'";
			$field_retencion = getRecord($sql);
			//	si no existe inserto nuevo comprobante
			if (!count($field_retencion)) {
				if ($Grupo != $field_retenciones['TipoComprobante']) {
					$Grupo = $field_retenciones['TipoComprobante'];
					$NroComprobante = getCodigo_3("ap_retenciones", "NroComprobante", "Anio", "TipoComprobante", $Anio, $field_retenciones['TipoComprobante'], 8);
				}
				$sql = "INSERT INTO ap_retenciones
						SET
							Anio = '".$Anio."',
							CodOrganismo = '".$field_retenciones['CodOrganismo']."',
							NroOrden = '".$field_retenciones['NroOrden']."',
							AnioOrden = '".$field_retenciones['Anio']."',
							NroComprobante = '".$NroComprobante."',
							Secuencia = '".$SecuenciaRetencion."',
							PeriodoFiscal = '".$Periodo."',
							CodImpuesto = '".$field_retenciones['CodImpuesto']."',
							FechaComprobante = '".formatFechaAMD($FechaPago)."',
							CodProveedor = '".$field_retenciones['CodProveedor']."',
							CodTipoDocumento = '".$field_retenciones['CodTipoDocumento']."',
							NroDocumento = '".$NroDocumento."',
							NroControl = '".$NroControl."',
							NroFactura = '".$NroFactura."',
							FechaFactura = '".formatFechaAMD($FechaFactura)."',
							MontoAfecto = '".$field_retenciones['MontoAfecto']."',
							MontoNoAfecto = '".$field_retenciones['MontoNoAfecto']."',
							MontoImpuesto = '".$field_retenciones['MontoImpuesto']."',
							MontoFactura = '".$field_retenciones['MontoFactura']."',
							Porcentaje = '".$field_retenciones['Porcentaje']."',
							MontoRetenido = '".$field_retenciones['MontoRetenido']."',
							TipoComprobante = '".$field_retenciones['TipoComprobante']."',
							PagoNroProceso = '".$NroProceso."',
							PagoSecuencia = '".$Secuencia."',
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()";
			} 
			elseif ($field_retencion['Estado'] == 'PA' && $FlagPagoParcial != 'S')  {
				$sql = "UPDATE ap_retenciones
						SET
							PagoNroProceso = '".$NroProceso."',
							PagoSecuencia = '".$Secuencia."'
						WHERE
							NroComprobante = '".$field_retencion['NroComprobante']."' AND
							Anio = '".$field_retencion['Anio']."' AND
							TipoComprobante = '".$field_retencion['TipoComprobante']."'";
			}
			execute($sql);
		}
		//	-----------------
		mysql_query("COMMIT");
	}
	//	anular
	elseif ($accion == "anular") {
		mysql_query("BEGIN");
		##	
		if ($Estado == 'GE') {
			##	
			$sql = "UPDATE ap_ordenpago
					SET
						Estado = 'PE',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()
					WHERE
						Anio = '".$Anio."' AND
						CodOrganismo = '".$CodOrganismo."' AND
						NroOrden = '".$NroOrden."'";
			execute($sql);
			##	
			$sql = "DELETE FROM ap_pagos
					WHERE
						Anio = '".$Anio."' AND
						CodOrganismo = '".$CodOrganismo."' AND
						NroOrden = '".$NroOrden."'";
			execute($sql);
			##	
			$sql = "DELETE FROM ap_retenciones
					WHERE
						AnioOrden = '".$Anio."' AND
						CodOrganismo = '".$CodOrganismo."' AND
						NroOrden = '".$NroOrden."'";
			execute($sql);
		}
		else {
			list($AnioActual, $MesActual, $DiaActual) = split("[/.-]", substr($Ahora, 0, 10));
			$FechaActual = "$AnioActual-$MesActual-$DiaActual";
			$PeriodoActual = "$AnioActual-$MesActual";
			//	vouchers
			if ($FlagContabilizacionPendiente == "N" && $_PARAMETRO['CONTONCO'] == "S") {
				//	genero nuevo voucher
				$CodVoucher1 = substr($VoucherProv, 0, 2);
				$NroVoucher1 = getCodigo("ac_vouchermast", "NroVoucher", 4, "CodOrganismo", $CodOrganismo, "Periodo", $PeriodoActual, "CodVoucher", $CodVoucher1, "CodContabilidad", "T");
				$NroInterno1 = getCodigo("ac_vouchermast", "NroInterno", 10);
				$Voucher1 = "$CodVoucher1-$NroVoucher1";
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
									'$Voucher1' AS Voucher,
									'T' AS CodContabilidad,
									'$CodVoucher1' AS Prefijo,
									'$NroVoucher1' AS NroVoucher,
									'$CodVoucher1' AS CodVoucher,
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
									'$NroInterno1' AS NroInterno,
									FlagTransferencia,
									Estado,
									CodLibroCont,
									'".$_SESSION["USUARIO_ACTUAL"]."' AS UltimoUsuario,
									NOW() AS UltimaFecha
								FROM ac_vouchermast
								WHERE
									CodOrganismo = '".$CodOrganismo."' AND
									Periodo = '".$PeriodoProv."' AND
									Voucher = '".$VoucherProv."' AND
									CodContabilidad = 'T'";
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
									'$Voucher1' AS Voucher,
									'T' AS CodContabilidad,
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
									Periodo = '".$PeriodoProv."' AND
									Voucher = '".$VoucherProv."' AND
									CodContabilidad = 'T'";
				execute($sql);
			}
			if ($FlagContPendienteOrdPub20 == "N" && $_PARAMETRO['CONTPUB20'] == "S") {
				//	genero nuevo voucher
				$CodVoucher2 = substr($VoucherOrdPago, 0, 2);
				$NroVoucher2 = getCodigo("ac_vouchermast", "NroVoucher", 4, "CodOrganismo", $CodOrganismo, "Periodo", $PeriodoActual, "CodVoucher", $CodVoucher2, "CodContabilidad", "F");
				$NroInterno2 = getCodigo("ac_vouchermast", "NroInterno", 10);
				$Voucher2 = "$CodVoucher2-$NroVoucher2";
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
									'$Voucher2' AS Voucher,
									'F' AS CodContabilidad,
									'$CodVoucher2' AS Prefijo,
									'$NroVoucher2' AS NroVoucher,
									'$CodVoucher2' AS CodVoucher,
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
									'$NroInterno2' AS NroInterno,
									FlagTransferencia,
									Estado,
									CodLibroCont,
									'".$_SESSION["USUARIO_ACTUAL"]."' AS UltimoUsuario,
									NOW() AS UltimaFecha
								FROM ac_vouchermast
								WHERE
									CodOrganismo = '".$CodOrganismo."' AND
									Periodo = '".$PeriodoOrdPago."' AND
									Voucher = '".$VoucherOrdPago."' AND
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
									'$Voucher2' AS Voucher,
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
									Periodo = '".$PeriodoOrdPago."' AND
									Voucher = '".$VoucherOrdPago."' AND
									CodContabilidad = 'F'";
				execute($sql);
			}
			//	actualizo obligacion
			$sql = "UPDATE ap_obligaciones
					SET
						VoucherAnulacion = '".$Voucher1."',
						PeriodoAnulacion = NOW(),
						FlagContabilizacionPendiente = 'S',
						Estado = 'RV',
						Voucher = '',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()
					WHERE
						CodProveedor = '".$CodProveedor."' AND
						CodTipoDocumento = '".$CodTipoDocumento."' AND
						NroDocumento = '".$NroDocumento."'";
			execute($sql);
			//	actualizo orden de pago
			$sql = "UPDATE ap_ordenpago
					SET
						Estado = 'AN',
						Voucher = '',
						MotivoAnulacion = '".$MotivoAnulacion."',
						AnuladoPor = '".$_SESSION["CODPERSONA_ACTUAL"]."',
						FechaAnulacion = NOW(),
						VoucherPagoAnulacion = '".$Voucher2."',
						PeriodoPagoAnulacion = NOW(),					
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()
					WHERE
						Anio = '".$Anio."' AND
						CodOrganismo = '".$CodOrganismo."' AND
						NroOrden = '".$NroOrden."'";
			execute($sql);
			//	documentos
			$sql = "SELECT *
					FROM ap_documentos
					WHERE
						Anio = '".$Anio."' AND
						CodOrganismo = '".$CodOrganismo."' AND
						CodProveedor = '".$CodProveedor."' AND
						ObligacionTipoDocumento = '".$CodTipoDocumento."' AND
						ObligacionNroDocumento = '".$NroDocumento."'";
			$query_documentos = mysql_query($sql) or die (getErrorSql(mysql_errno(), mysql_error(), $sql));	$linea=0;
			while ($field_documentos = mysql_fetch_array($query_documentos)) {
				//	actualizo (orden)
				if ($field_documentos['ReferenciaTipoDocumento'] == "OC") {
					$sql = "UPDATE lg_ordencompra 
							SET
								MontoPendiente = (MontoPendiente + (".floatval($field_documentos['MontoAfecto'])." + ".floatval($field_documentos['MontoNoAfecto'])." + ".floatval($field_documentos['MontoImpuestos']).")),
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
								MontoGastado = (MontoGastado + (".floatval($field_documentos['MontoAfecto'])." + ".floatval($field_documentos['MontoNoAfecto'])." + ".floatval($field_documentos['MontoImpuestos']).")),
								UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
								UltimaFecha = NOW()
							WHERE
								Anio = '".$Anio."' AND
								CodOrganismo = '".$CodOrganismo."' AND
								NroOrden = '".$field_documentos['ReferenciaNroDocumento']."'";
					execute($sql);
				}
			}
			//	actualizo orden distribucion
			$sql = "UPDATE ap_ordenpagodistribucion
					SET
						FechaAnulacion = NOW(),
						PeriodoAnulacion = NOW(),
						Estado = 'AN',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()
					WHERE
						Anio = '".$Anio."' AND
						CodOrganismo = '".$CodOrganismo."' AND
						NroOrden = '".$NroOrden."'";
			execute($sql);
			if ($FlagRetencion == 'S') {
				$sql = "UPDATE ap_retenciones
						SET Estado = 'AN'
						WHERE
							AnioOrden = '".$Anio."' AND
							CodOrganismo = '".$CodOrganismo."' AND
							NroOrden = '".$NroOrden."'";
				execute($sql);
			}
		}
		echo "|$Anio"."_"."$CodOrganismo"."_"."$NroOrden";
		mysql_query("COMMIT");
	}
}

//	pagos (modificacion restringida)
elseif ($modulo == "pago") {
	$MotivoAnulacion = changeUrl($MotivoAnulacion);
	//	modificar
	if ($accion == "modificar") {
		mysql_query("BEGIN");
		//-------------------
		list($Anio, $Mes, $Dia) = split("[/.-]", substr($Ahora, 0, 10));
		list($d, $m, $a) = split("[/.-]", $FechaPago);
		if ("$a-$m" != $Periodo) die("No se puede modificar el periodo del pago.");
		//	actualizo orden
		$sql = "UPDATE ap_pagos
				SET
					GeneradoPor = '".$GeneradoPor."',
					ConformadoPor = '".$ConformadoPor."',
					RevisadoPor = '".$RevisadoPor."',
					AprobadoPor = '".$AprobadoPor."',
					FechaPago = '".formatFechaAMD($FechaPago)."',
					NomProveedorPagar = '".changeUrl($NomProveedorPagar)."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					NroProceso = '".$NroProceso."' AND
					Secuencia = '".$Secuencia."'";
		execute($sql);
		//	actualizo retenciones
		$sql = "UPDATE ap_retenciones
				SET
					FechaComprobante = '".formatFechaAMD($FechaPago)."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					PagoNroProceso = '".$NroProceso."' AND
					PagoSecuencia = '".$Secuencia."' AND 
					Estado = 'PA'";
		execute($sql);
		//-------------------
		mysql_query("COMMIT");
	}
	//	anular
	elseif ($accion == "anular") {
		mysql_query("BEGIN");
		//-------------------
		list($AnioActual, $MesActual, $DiaActual) = split("[/.-]", substr($Ahora, 0, 10));
		$FechaActual = "$AnioActual-$MesActual-$DiaActual";
		$PeriodoActual = "$AnioActual-$MesActual";
		//	consulto pago
		$sql = "SELECT
					p.NroProceso,
					p.Secuencia,
					p.NroCuenta,
					p.CodTipoPago,
					p.CodProveedor,
					p.MontoPago,
					op.Anio,
					op.CodOrganismo,
					op.NroOrden,
					p.FlagContabilizacionPendiente,
					p.FlagContPendientePub20,
					op.CodTipoDocumento,
					op.NroDocumento,
					op.Concepto,
					op.CodCentroCosto
				FROM
					ap_pagos p
					INNER JOIN ap_ordenpago op ON (p.NroProceso = op.NroProceso AND
												   p.Secuencia = op.Secuencia)
				WHERE
					p.NroProceso = '".$NroProceso."' AND
					p.Secuencia = '".$Secuencia."'
				GROUP BY NroProceso";
		$field_ops = getRecords($sql);
		foreach($field_ops as $field_op) {
			//	consulto
			$sql = "SELECT TipoTransaccion, FlagVoucher 
					FROM ap_bancotipotransaccion 
					WHERE CodTipoTransaccion = '".$_PARAMETRO["TRANSANUL"]."'";
			$field_flag = getRecord($sql);
			if ($field_flag['TipoTransaccion'] == "I") $signo = "1";
			elseif ($field_flag['TipoTransaccion'] == "E") $signo = "-1";
			//	vouchers
			if ($field_op['FlagContabilizacionPendiente'] == "N" && $_PARAMETRO['CONTONCO'] == "S") {
				//	genero nuevo voucher
				$NroVoucher1 = getCodigo("ac_vouchermast", "NroVoucher", 4, "CodOrganismo", $CodOrganismo, "Periodo", $PeriodoActual, "CodVoucher", $CodVoucher, "CodContabilidad", "T");
				$NroInterno1 = getCodigo("ac_vouchermast", "NroInterno", 10);
				$Voucher1 = "$CodVoucher-$NroVoucher1";
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
									'".$PeriodoActual."' AS Periodo,
									'$Voucher1' AS Voucher,
									'T' AS CodContabilidad,
									'$CodVoucher' AS Prefijo,
									'$NroVoucher1' AS NroVoucher,
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
									'".$NroInterno1."' AS NroInterno,
									FlagTransferencia,
									Estado,
									CodLibroCont,
									'".$_SESSION["USUARIO_ACTUAL"]."' AS UltimoUsuario,
									NOW() AS UltimaFecha
								FROM ac_vouchermast
								WHERE
									CodOrganismo = '".$CodOrganismo."' AND
									Periodo = '".$Periodo."' AND
									Voucher = '".$VoucherPago."' AND
									CodContabilidad = 'T'";
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
									'".$PeriodoActual."' AS Periodo,
									'$Voucher1' AS Voucher,
									'T' AS CodContabilidad,
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
									Periodo = '".$Periodo."' AND
									Voucher = '".$VoucherPago."' AND
									CodContabilidad = 'T'";
				execute($sql);
			}
			//	vouchers
			if ($field_op['FlagContPendientePub20'] == "N" && $_PARAMETRO['CONTPUB20'] == "S") {
				//	genero nuevo voucher
				$NroVoucher2 = getCodigo("ac_vouchermast", "NroVoucher", 4, "CodOrganismo", $CodOrganismo, "Periodo", $PeriodoActual, "CodVoucher", $CodVoucherPub20, "CodContabilidad", "F");
				$NroInterno2 = getCodigo("ac_vouchermast", "NroInterno", 10);
				$Voucher2 = "$CodVoucherPub20-$NroVoucher2";
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
									'".$PeriodoActual."' AS Periodo,
									'$Voucher2' AS Voucher,
									'F' AS CodContabilidad,
									'$CodVoucherPub20' AS Prefijo,
									'$NroVoucher2' AS NroVoucher,
									'$CodVoucherPub20' AS CodVoucher,
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
									'".$NroInterno2."' AS NroInterno,
									FlagTransferencia,
									Estado,
									CodLibroCont,
									'".$_SESSION["USUARIO_ACTUAL"]."' AS UltimoUsuario,
									NOW() AS UltimaFecha
								FROM ac_vouchermast
								WHERE
									CodOrganismo = '".$CodOrganismo."' AND
									Periodo = '".$PeriodoPagoPub20."' AND
									Voucher = '".$VoucherPagoPub20."' AND
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
									'".$PeriodoActual."' AS Periodo,
									'$Voucher2' AS Voucher,
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
									Periodo = '".$PeriodoPagoPub20."' AND
									Voucher = '".$VoucherPagoPub20."' AND
									CodContabilidad = 'F'";
				execute($sql);
			}
			//	inserto transaccion
			$NroTransaccion = getCodigo("ap_bancotransaccion", "NroTransaccion", 5);
			$sql = "INSERT INTO ap_bancotransaccion
					SET
						NroTransaccion = '".$NroTransaccion."',
						Secuencia = '1',
						CodOrganismo = '".$field_op['CodOrganismo']."',
						CodTipoTransaccion = '".$_PARAMETRO["TRANSANUL"]."',
						TipoTransaccion = '".$field_flag['TipoTransaccion']."',
						NroCuenta = '".$field_op['NroCuenta']."',
						CodTipoDocumento = '".$field_op['CodTipoDocumento']."',
						CodProveedor = '".$field_op['CodProveedor']."',
						CodCentroCosto = '".$field_op['CodCentroCosto']."',
						PreparadoPor = '".$_SESSION["CODPERSONA_ACTUAL"]."',
						FechaPreparacion = NOW(),
						FechaTransaccion = NOW(),
						PeriodoContable = '".$PeriodoActual."',
						Monto = '".($field_op['MontoPago']*$signo)."',
						Comentarios = '".$field_op['Concepto']."',
						PagoNroProceso = '".$field_op['NroProceso']."',
						PagoSecuencia = '".$field_op['Secuencia']."',
						NroPago = '".$NroPago."',
						FlagConciliacion = 'N',
						FlagAutomatico = 'S',
						Estado = 'AP',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		$sql = "SELECT
					p.NroProceso,
					p.Secuencia,
					p.NroCuenta,
					p.CodTipoPago,
					p.CodProveedor,
					p.MontoPago,
					op.Anio,
					op.CodOrganismo,
					op.NroOrden,
					op.CodProveedor,
					op.CodTipoDocumento,
					op.NroDocumento,
					p.FlagContabilizacionPendiente,
					p.FlagContPendientePub20,
					op.CodTipoDocumento,
					op.NroDocumento,
					op.Concepto,
					op.CodCentroCosto,
					op.FlagPagoParcial
				FROM
					ap_pagos p
					INNER JOIN ap_ordenpago op ON (p.NroProceso = op.NroProceso AND
												   p.Secuencia = op.Secuencia)
				WHERE
					p.NroProceso = '".$NroProceso."' AND
					p.Secuencia = '".$Secuencia."'
				ORDER BY Secuencia";
		$field_ops = getRecords($sql);
		foreach($field_ops as $field_op) {
			//	actualizo obligacion
			$sql = "UPDATE ap_obligaciones
					SET
						Estado = 'AP',
						NroPago = '',
						NroProceso = '',
						ProcesoSecuencia = '',
						FechaPago = '',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()
					WHERE
						CodProveedor = '".$field_op['CodProveedor']."' AND
						CodTipoDocumento = '".$field_op['CodTipoDocumento']."' AND
						NroDocumento = '".$field_op['NroDocumento']."'";
			execute($sql);
			//	si viene de nomina
			$FlagNomina = getVar2("ap_obligaciones", "FlagNomina", array("CodProveedor","CodTipoDocumento","NroDocumento"), array($field_op['CodProveedor'],$field_op['CodTipoDocumento'],$field_op['NroDocumento']));
			if ($FlagNomina == "S") {
				//	actualizo pr obligacion
				$sql = "UPDATE pr_obligaciones
						SET
							FechaPago = '',
							NroPago = '',
							Estado = 'PE',
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()
						WHERE
							CodProveedor = '".$field_op['CodProveedor']."' AND
							CodTipoDocumento = '".$field_op['CodTipoDocumento']."' AND
							NroDocumento = '".$field_op['NroDocumento']."'";
				execute($sql);
				//	actualizo payroll
				$sql = "UPDATE pr_tiponominaempleado
						SET
							FechaPago = '',
							EstadoPago = 'TR'
						WHERE
							CodProveedor = '".$field_op['CodProveedor']."' AND
							CodTipoDocumento = '".$field_op['CodTipoDocumento']."' AND
							NroDocumento = '".$field_op['NroDocumento']."'";
				execute($sql);
				//	actualizo prestaciones
				$sql = "UPDATE pr_liquidacionempleado
						SET
							Fliquidacion = '',
							FechaPago = '',
							EstadoPago = 'TR'
						WHERE
							CodProveedor = '".$field_op['CodProveedor']."' AND
							CodTipoDocumento = '".$field_op['CodTipoDocumento']."' AND
							NroDocumento = '".$field_op['NroDocumento']."'";
				execute($sql);
				##	prestaciones
				if ($field_proceso['CodTipoProceso'] == "PRS") {
					$sql = "UPDATE mastempleado
							SET Fliquidacion = ''
							WHERE
								CodPersona IN (SELECT CodPersona
											   FROM pr_tiponominaempleado
											   WHERE
													CodProveedor = '".$field_op['CodProveedor']."' AND
													CodTipoDocumento = '".$field_op['CodTipoDocumento']."' AND
													NroDocumento = '".$field_op['NroDocumento']."')";
					execute($sql);
				}
				//	consulto de la obligacion de nomina
				$sql = "SELECT
							CodTipoNom,
							PeriodoNomina,
							Periodo,
							CodOrganismo,
							CodTipoProceso
						FROM pr_obligaciones
						WHERE
							CodProveedor = '".$field_op['CodProveedor']."' AND
							CodTipoDocumento = '".$field_op['CodTipoDocumento']."' AND
							NroDocumento = '".$field_op['NroDocumento']."'";
				$field_tne = getRecord($sql);
				//	actualizo estado
				$sql = "UPDATE pr_procesoperiodo
						SET
							FechaPago = '',
							EstadoPago = 'PE',
							FlagPagado = 'N'
						WHERE
							CodTipoNom = '".$field_tne['CodTipoNom']."' AND
							Periodo = '".$field_tne['PeriodoNomina']."' AND
							CodOrganismo = '".$field_tne['CodOrganismo']."' AND
							CodTipoProceso = '".$field_tne['CodTipoProceso']."'";
				execute($sql);
				//	si es vacaciones
				if ($field_tne['CodTipoProceso'] == $_PARAMETRO['PROCESOBVC']) {
					//	elimino los dias pagados en vacaciones pagadas
					$sql = "SELECT
								tne.CodPersona,
								tne.Periodo,
								SUBSTRING(tne.Periodo, 1, 4) AS Anio,
								tne.CodTipoNom,
								tne.CodTipoDocumento,
								tne.NroDocumento,
								tne.FechaPago,
								tnec.Cantidad,
								pp.FechaDesde,
								pp.FechaHasta
							FROM
								pr_tiponominaempleado tne
								INNER JOIN pr_tiponominaempleadoconcepto tnec ON (tnec.CodTipoNom = tne.CodTipoNom AND
																				  tnec.Periodo = tne.Periodo AND
																				  tnec.CodPersona = tne.CodPersona AND
																				  tnec.CodOrganismo = tne.CodOrganismo AND
																				  tnec.CodTipoProceso = tne.CodTipoProceso)
								INNER JOIN pr_procesoperiodo pp ON (pp.CodTipoNom = tne.CodTipoNom AND
																	pp.Periodo = tne.Periodo AND
																	pp.CodOrganismo = tne.CodOrganismo AND
																	pp.CodTipoProceso = tne.CodTipoProceso)
							WHERE
								tne.CodProveedor = '".$field_op['CodProveedor']."' AND
								tne.CodTipoDocumento = '".$field_op['CodTipoDocumento']."' AND
								tne.NroDocumento = '".$field_op['NroDocumento']."' AND
								tnec.CodConcepto = '".$_PARAMETRO['CONCEPTOBVC']."'";
					$field_detalles = getRecords($sql);
					foreach($field_detalles as $field_detalle) {
						//	elimino
						$sql = "DELETE FROM rh_vacacionpago
								WHERE
									CodPersona = '".$field_detalle['CodPersona']."' AND
									CodTipoDocumento = '".$field_op['CodTipoDocumento']."' AND
									NroDocumento = '".$field_op['NroDocumento']."'";
						execute($sql);
					}
				}
			}
			//	actualizo orden de pago
			$sql = "UPDATE ap_ordenpago
					SET
						Estado = 'PE',
						NroPago = '',
						FechaTransferencia = '0000-00-00',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()
					WHERE
						Anio = '".$field_op['Anio']."' AND
						CodOrganismo = '".$field_op['CodOrganismo']."' AND
						NroOrden = '".$field_op['NroOrden']."'";
			execute($sql);
			//	actualizo orden de pago distribucion
			$sql = "UPDATE ap_ordenpagodistribucion
					SET
						Estado = 'PE',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()
					WHERE
						Anio = '".$field_op['Anio']."' AND
						CodOrganismo = '".$field_op['CodOrganismo']."' AND
						NroOrden = '".$field_op['NroOrden']."'";
			execute($sql);
			//	actualizo pagos
			$sql = "UPDATE ap_pagos
					SET
						FechaAnulacion = NOW(),
						MotivoAnulacion = '".$MotivoAnulacion."',
						VoucherAnulacion = '".$Voucher1."',
						PeriodoAnulacion = '".$PeriodoActual."',
						VoucherAnulPub20 = '".$Voucher2."',
						PeriodoAnulPub20 = '".$PeriodoActual."',
						AnuladoPor = '".$_SESSION["CODPERSONA_ACTUAL"]."',
						Estado = 'AN',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()
					WHERE
						NroProceso = '".$NroProceso."' AND
						Secuencia = '".$Secuencia."'";
			execute($sql);
			##	pagos parciales
			if ($field_op['FlagPagoParcial'] == 'S') {
				$sql = "UPDATE ap_pagosparciales
						SET
							Estado = 'AN',
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()
						WHERE
							Anio = '".$field_op['Anio']."' AND
							CodOrganismo = '".$field_op['CodOrganismo']."' AND
							NroOrden = '".$field_op['NroOrden']."'";
				execute($sql);
				##	
				$sql = "UPDATE ap_obligaciones
						SET
							MontoPagoParcial = MontoPagoParcial - ".floatval($field_op['MontoPago']).",
							UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
							UltimaFecha = NOW()
						WHERE
							CodProveedor = '$field_op[CodProveedor]' AND
							CodTipoDocumento = '$field_op[CodTipoDocumento]' AND
							NroDocumento = '$field_op[NroDocumento]'";
				execute($sql);
			}
			//	anulo retenciones
			if ($FlagAnularRetencion == 'S') {
				$sql = "UPDATE ap_retenciones
						SET
							Estado = 'AN',
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()
						WHERE
							CodOrganismo = '".$field_op['CodOrganismo']."' AND
							Anio = '".$field_op['Anio']."' AND
							NroOrden = '".$field_op['NroOrden']."' AND
							Estado = 'PA'";
				execute($sql);
			}
		}
		echo "|$NroProceso"."_"."$Secuencia";
		//-------------------
		mysql_query("COMMIT");
	}
}

//	
elseif ($modulo == "registro_compra") {
	//	importar
	if ($accion == "importar") {
		$nrocp = 0;
		$nrocf = 0;
		//	eliminar los registros del periodo actual
		$sql = "DELETE FROM ap_registrocompras
				WHERE
					Periodo = '".$Periodo."' AND
					(SistemaFuente = 'CP' OR SistemaFuente = 'CC')";
		$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		
		//	inserto las obligaciones
		if ($FlagCP == "S") {
			$sql = "SELECT
						o.CodProveedor,
						o.CodTipoDocumento,
						o.NroDocumento,
						mp.NomCompleto,
						mp.DocFiscal,
						o.CodOrganismo,
						o.FechaRegistro,
						o.Voucher,
						o.Periodo,
						o.NroRegistro,
						o.NroControl,
						'N' AS FlagCajaChica,
						o.Comentarios,
						o.MontoAfecto,
						o.MontoNoAfecto,
						o.MontoImpuestoOtros,
						o.MontoObligacion,
						o.MontoImpuesto
					FROM
						ap_obligaciones o
						INNER JOIN mastpersonas mp ON (o.CodProveedor = mp.CodPersona)
						INNER JOIN ap_tipodocumento td ON (o.CodTipoDocumento = td.CodTipoDocumento)
					WHERE
						o.Periodo = '".$Periodo."' AND
						o.CodOrganismo = '".$CodOrganismo."' AND
						(o.Estado = 'AP' OR o.Estado = 'PA') AND
						td.FlagFiscal = 'S'";
			$query_obligaciones = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field_obligaciones = mysql_fetch_array($query_obligaciones)) {	$nrocp++;
				//	consulto el monto de los impuestos 
				$sql = "SELECT SUM(oi.MontoImpuesto) AS MontoImpuesto
						FROM
							ap_obligacionesimpuesto oi
							INNER JOIN mastimpuestos i ON (oi.CodImpuesto = i.CodImpuesto)
						WHERE
							oi.CodProveedor = '".$field_obligaciones['CodProveedor']."' AND
							oi.CodTipoDocumento = '".$field_obligaciones['CodTipoDocumento']."' AND
							oi.NroDocumento = '".$field_obligaciones['NroDocumento']."' AND
							i.CodRegimenFiscal = 'R' AND
							i.TipoComprobante = 'IVA'";
				$query_impuestos = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
				if (mysql_num_rows($query_impuestos) != 0) $field_impuestos = mysql_fetch_array($query_impuestos);
				
				//	inserto el registro de compras
				$sql = "INSERT INTO ap_registrocompras (
									Periodo,
									SistemaFuente,
									Secuencia,
									CodProveedor,
									CodTipoDocumento,
									NroDocumento,
									NomProveedor,
									RifProveedor,
									CodOrganismo,
									FechaDocumento,
									Voucher,
									VoucherPeriodo,
									NroRegistro,
									NroDocumentoInterno,
									EstadoDocumento,
									Comentarios,									
									MontoImponible,
									FiscalImponible,
									ImponibleGravado,									
									MontoImpuestoVentas,
									MontoCreditoFiscal,
									FiscalImpuestoVentas,
									IGVGravado,									
									MontoObligacion,
									FiscalObligacion,
									MontoNoAfecto,
									FiscalNoAfecto,
									FiscalImpuestoRetenido,
									UltimoUsuario,
									UltimaFecha
						) VALUES (
									'".$Periodo."',
									'CP',
									'".$nrocp."',
									'$field_obligaciones[CodProveedor]',
									'$field_obligaciones[CodTipoDocumento]',
									'$field_obligaciones[NroDocumento]',
									'$field_obligaciones[NomCompleto]',
									'$field_obligaciones[DocFiscal]',
									'$field_obligaciones[CodOrganismo]',
									'$field_obligaciones[FechaRegistro]',
									'$field_obligaciones[Voucher]',
									'$field_obligaciones[Periodo]',
									'$field_obligaciones[NroRegistro]',
									'$field_obligaciones[NroControl]',
									'IN',
									'$field_obligaciones[Comentarios]',									
									'$field_obligaciones[MontoAfecto]',
									'$field_obligaciones[MontoAfecto]',
									'$field_obligaciones[MontoAfecto]',									
									'$field_obligaciones[MontoImpuesto]',
									'$field_obligaciones[MontoImpuesto]',
									'$field_obligaciones[MontoImpuesto]',
									'$field_obligaciones[MontoImpuesto]',									
									'$field_obligaciones[MontoObligacion]',
									'$field_obligaciones[MontoObligacion]',
									'$field_obligaciones[MontoNoAfecto]',
									'$field_obligaciones[MontoNoAfecto]',
									'$field_impuestos[MontoImpuesto]',
									'".$_SESSION["USUARIO_ACTUAL"]."',
									NOW()
						)";
				execute($sql);
			}
		}
		
		//	inserto caja chica
		if ($FlagCC == "S") {
			$sql = "SELECT
						cc.CodOrganismo,
						cc.FlagCajaChica,
						cc.NroCajaChica,
						cc.Descripcion,
						o.Voucher,
						o.Periodo,
						o.NroRegistro,
						ccd.CodTipoDocumento,
						ccd.NroDocumento,
						ccd.CodProveedor,
						ccd.NomProveedor,
						ccd.DocFiscal,
						ccd.NroRecibo,
						ccd.FechaDocumento,
						ccd.MontoAfecto,
						ccd.MontoNoAfecto,
						ccd.MontoImpuesto,
						ccd.MontoRetencion,
						ccd.MontoPagado AS MontoObligacion
					FROM
						ap_cajachicadetalle ccd
						INNER JOIN ap_cajachica cc ON (ccd.FlagCajaChica = cc.FlagCajaChica AND
													   ccd.NroCajaChica = cc.NroCajaChica)
						INNER JOIN ap_obligaciones o ON (cc.CodBeneficiario = o.CodProveedor AND
														 cc.CodTipoDocumento = o.CodTipoDocumento AND
														 cc.NroDocumento = o.NroDocumento)
						INNER JOIN mastpersonas mp ON (cc.CodBeneficiario = mp.CodPersona)
					WHERE
						o.Periodo = '".$Periodo."' AND
						o.CodOrganismo = '".$CodOrganismo."' AND
						ccd.CodRegimenFiscal = 'I' AND
						(o.Estado = 'AP' OR o.Estado = 'PA')";
			$query_cajachica = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field_cajachica = mysql_fetch_array($query_cajachica)) {	$nrocf++;
				//	consulto el monto de los impuestos 
				$sql = "SELECT SUM(oi.MontoImpuesto) AS MontoImpuesto
						FROM
							ap_obligacionesimpuesto oi
							INNER JOIN mastimpuestos i ON (oi.CodImpuesto = i.CodImpuesto)
						WHERE
							oi.CodProveedor = '".$field_obligaciones['CodProveedor']."' AND
							oi.CodTipoDocumento = '".$field_obligaciones['CodTipoDocumento']."' AND
							oi.NroDocumento = '".$field_obligaciones['NroDocumento']."' AND
							i.CodRegimenFiscal = 'R' AND
							i.TipoComprobante = 'IVA'";
				$query_impuestos = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
				if (mysql_num_rows($query_impuestos) != 0) $field_impuestos = mysql_fetch_array($query_impuestos);
				
				//	inserto el registro de compras
				$sql = "INSERT INTO ap_registrocompras (
									Periodo,
									SistemaFuente,
									Secuencia,
									CodProveedor,
									CodTipoDocumento,
									NroDocumento,
									NomProveedor,
									RifProveedor,
									CodOrganismo,
									FechaDocumento,
									Voucher,
									VoucherPeriodo,
									NroRegistro,
									NroDocumentoInterno,
									EstadoDocumento,
									Comentarios,									
									MontoImponible,
									FiscalImponible,
									ImponibleGravado,									
									MontoImpuestoVentas,
									MontoCreditoFiscal,
									FiscalImpuestoVentas,
									IGVGravado,									
									MontoObligacion,
									FiscalObligacion,
									MontoNoAfecto,
									FiscalNoAfecto,
									FiscalImpuestoRetenido,
									FlagCajaChica,
									NroCajaChica,
									UltimoUsuario,
									UltimaFecha
						) VALUES (
									'".$Periodo."',
									'CC',
									'".$nrocf."',
									'$field_cajachica[CodProveedor]',
									'$field_cajachica[CodTipoDocumento]',
									'$field_cajachica[NroDocumento]',
									'$field_cajachica[NomProveedor]',
									'$field_cajachica[DocFiscal]',
									'$field_cajachica[CodOrganismo]',
									'$field_cajachica[FechaDocumento]',
									'$field_cajachica[Voucher]',
									'$field_cajachica[Periodo]',
									'$field_cajachica[NroRegistro]',
									'$field_cajachica[NroRecibo]',
									'IN',
									'$field_cajachica[Descripcion]',									
									'$field_cajachica[MontoAfecto]',
									'$field_cajachica[MontoAfecto]',
									'$field_cajachica[MontoAfecto]',									
									'$field_cajachica[MontoImpuesto]',
									'$field_cajachica[MontoImpuesto]',
									'$field_cajachica[MontoImpuesto]',
									'$field_cajachica[MontoImpuesto]',									
									'$field_cajachica[MontoObligacion]',
									'$field_cajachica[MontoObligacion]',
									'$field_cajachica[MontoNoAfecto]',
									'$field_cajachica[MontoNoAfecto]',
									'$field_impuestos[MontoImpuesto]',
									'$field_cajachica[FlagCajaChica]',
									'$field_cajachica[NroCajaChica]',
									'".$_SESSION["USUARIO_ACTUAL"]."',
									NOW()
						)";
				execute($sql);
			}
		}
		echo "|$nrocp|$nrocf";
	}
	
	//	eliminar
	elseif ($accion == "eliminar") {
		list($Periodo, $SistemaFuente, $Secuencia) = split("[.]", $registro);
		//	eliminar
		$sql = "DELETE FROM ap_registrocompras
				WHERE
					Periodo = '".$Periodo."' AND
					SistemaFuente = '".$SistemaFuente."' AND
					Secuencia = '".$Secuencia."'";
		$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	}
}

//	conciliacion bancaria
elseif ($modulo == "conciliacion-bancaria") {
	//	nuevo
	if ($accion == "actualizar") {
		//	impuestos
		if ($registro != "") {
			$linea = split(";char:tr;", $registro);
			foreach ($linea as $transaccion) {
				list($NroTransaccion, $Secuencia) = split("[.]", $transaccion);
				//	actualizo
				$sql = "UPDATE ap_bancotransaccion
						SET 
							FlagConciliacion = 'S',
							FechaConciliacion = NOW(),
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()
						WHERE
							NroTransaccion = '".$NroTransaccion."' AND
							Secuencia = '".$Secuencia."'";
				execute($sql);
			}
		}
	}
}

//	tipos de documentos ctas. x pagar
elseif ($modulo == "tipo_documento_cxp") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-----------------
		//	inserto
		$sql = "INSERT INTO ap_tipodocumento
				SET
					CodTipoDocumento = '".$CodTipoDocumento."',
					Descripcion = '".changeUrl($Descripcion)."',
					Clasificacion = '".$Clasificacion."',
					CodRegimenFiscal = '".$CodRegimenFiscal."',
					CodVoucher = '".$CodVoucher."',
					CodVoucherOrdPago = '".$CodVoucherOrdPago."',
					FlagProvision = '".$FlagProvision."',
					CodCuentaProv = '".$CodCuentaProv."',
					CodCuentaProvPub20 = '".$CodCuentaProvPub20."',
					FlagAdelanto = '".$FlagAdelanto."',
					CodCuentaAde = '".$CodCuentaAde."',
					CodCuentaAdePub20 = '".$CodCuentaAdePub20."',
					FlagFiscal = '".$FlagFiscal."',
					CodFiscal = '".$CodFiscal."',
					FlagAutoNomina = '".$FlagAutoNomina."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	modificar registro
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizar
		$sql = "UPDATE ap_tipodocumento
				SET
					Descripcion = '".changeUrl($Descripcion)."',
					Clasificacion = '".$Clasificacion."',
					CodRegimenFiscal = '".$CodRegimenFiscal."',
					CodVoucher = '".$CodVoucher."',
					CodVoucherOrdPago = '".$CodVoucherOrdPago."',
					FlagProvision = '".$FlagProvision."',
					CodCuentaProv = '".$CodCuentaProv."',
					CodCuentaProvPub20 = '".$CodCuentaProvPub20."',
					FlagAdelanto = '".$FlagAdelanto."',
					CodCuentaAde = '".$CodCuentaAde."',
					CodCuentaAdePub20 = '".$CodCuentaAdePub20."',
					FlagFiscal = '".$FlagFiscal."',
					CodFiscal = '".$CodFiscal."',
					FlagAutoNomina = '".$FlagAutoNomina."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodTipoDocumento = '".$CodTipoDocumento."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	eliminar registro
	elseif ($accion == "eliminar") {
		//	elimino
		$sql = "DELETE FROM ap_tipodocumento WHERE CodTipoDocumento = '".$registro."'";
		$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	}
}

//	impuestos
elseif ($modulo == "impuestos") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-----------------
		//	inserto
		$sql = "INSERT INTO mastimpuestos
				SET
					CodImpuesto = '".$CodImpuesto."',
					Descripcion = '".changeUrl($Descripcion)."',
					CodRegimenFiscal = '".$CodRegimenFiscal."',
					Signo = '".$Signo."',
					CodCuenta = '".$CodCuenta."',
					CodCuentaPub20 = '".$CodCuentaPub20."',
					cod_partida = '".$cod_partida."',
					FactorPorcentaje = '".setNumero($FactorPorcentaje)."',
					FlagProvision = '".($FlagProvision?$FlagProvision:'N')."',
					FlagImponible = '".($FlagImponible?$FlagImponible:'N')."',
					TipoComprobante = '".$TipoComprobante."',
					FlagGeneral = '".($FlagGeneral?$FlagGeneral:'N')."',
					FlagSustraendo = '".($FlagSustraendo?$FlagSustraendo:'N')."',
					SustraendoUT = '".setNumero($SustraendoUT)."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	modificar registro
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizar
		$sql = "UPDATE mastimpuestos
				SET
					Descripcion = '".changeUrl($Descripcion)."',
					CodRegimenFiscal = '".$CodRegimenFiscal."',
					Signo = '".$Signo."',
					CodCuenta = '".$CodCuenta."',
					CodCuentaPub20 = '".$CodCuentaPub20."',
					cod_partida = '".$cod_partida."',
					FactorPorcentaje = '".setNumero($FactorPorcentaje)."',
					FlagProvision = '".($FlagProvision?$FlagProvision:'N')."',
					FlagImponible = '".($FlagImponible?$FlagImponible:'N')."',
					TipoComprobante = '".$TipoComprobante."',
					FlagGeneral = '".($FlagGeneral?$FlagGeneral:'N')."',
					FlagSustraendo = '".($FlagSustraendo?$FlagSustraendo:'N')."',
					SustraendoUT = '".setNumero($SustraendoUT)."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodImpuesto = '".$CodImpuesto."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	eliminar registro
	elseif ($accion == "eliminar") {
		//	elimino
		$sql = "DELETE FROM mastimpuestos WHERE CodImpuesto = '".$registro."'";
		$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	}
}

//	cuentas bancarias
elseif ($modulo == "cuentas_bancarias") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-----------------
		//	inserto
		$sql = "INSERT INTO ap_ctabancaria
				SET
					NroCuenta = '".$NroCuenta."',
					CodOrganismo = '".$CodOrganismo."',
					CodBanco = '".$CodBanco."',
					Descripcion = '".changeUrl($Descripcion)."',
					CtaBanco = '".$CtaBanco."',
					TipoCuenta = '".$TipoCuenta."',
					FechaApertura = '".formatFechaAMD($FechaApertura)."',
					PeriodoConciliacion = '".formatFechaAMD($FechaApertura)."',
					CodCuenta = '".$CodCuenta."',
					CodCuentaPub20 = '".$CodCuentaPub20."',
					Agencia = '".changeUrl($Agencia)."',
					Distrito = '".changeUrl($Distrito)."',
					Atencion = '".changeUrl($Atencion)."',
					Cargo = '".changeUrl($Cargo)."',
					FlagConciliacionBancaria = '".$FlagConciliacionBancaria."',
					FlagConciliacionCP = '".$FlagConciliacionCP."',
					FlagDebitoBancario = '".$FlagDebitoBancario."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		
		//	tipos de pago
		if ($detalles_tipopagos != "") {
			$tipopagos = split(";char:tr;", $detalles_tipopagos);
			foreach ($tipopagos as $_linea) {
				list($_CodTipoPago, $_UltimoNumero) = split(";char:td;", $_linea);
				//	inserto
				$sql = "INSERT INTO ap_ctabancariatipopago
						SET
							NroCuenta = '".$NroCuenta."',
							CodTipoPago = '".$_CodTipoPago."',
							UltimoNumero = '".$_UltimoNumero."',
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()";
				execute($sql);
			}
		}
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	modificar registro
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizar
		$sql = "UPDATE ap_ctabancaria
				SET
					CodOrganismo = '".$CodOrganismo."',
					CodBanco = '".$CodBanco."',
					Descripcion = '".changeUrl($Descripcion)."',
					CtaBanco = '".$CtaBanco."',
					TipoCuenta = '".$TipoCuenta."',
					FechaApertura = '".formatFechaAMD($FechaApertura)."',
					PeriodoConciliacion = '".formatFechaAMD($FechaApertura)."',
					CodCuenta = '".$CodCuenta."',
					CodCuentaPub20 = '".$CodCuentaPub20."',
					Agencia = '".changeUrl($Agencia)."',
					Distrito = '".changeUrl($Distrito)."',
					Atencion = '".changeUrl($Atencion)."',
					Cargo = '".changeUrl($Cargo)."',
					FlagConciliacionBancaria = '".$FlagConciliacionBancaria."',
					FlagConciliacionCP = '".$FlagConciliacionCP."',
					FlagDebitoBancario = '".$FlagDebitoBancario."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE NroCuenta = '".$NroCuenta."'";
		execute($sql);
		
		//	tipos de pago
		$sql = "DELETE FROM ap_ctabancariatipopago WHERE NroCuenta = '".$NroCuenta."'";
		$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		if ($detalles_tipopagos != "") {
			$tipopagos = split(";char:tr;", $detalles_tipopagos);
			foreach ($tipopagos as $_linea) {
				list($_CodTipoPago, $_UltimoNumero) = split(";char:td;", $_linea);
				//	inserto
				$sql = "INSERT INTO ap_ctabancariatipopago
						SET
							NroCuenta = '".$NroCuenta."',
							CodTipoPago = '".$_CodTipoPago."',
							UltimoNumero = '".$_UltimoNumero."',
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()";
				execute($sql);
			}
		}
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	eliminar registro
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		//	elimino
		$sql = "DELETE FROM ap_ctabancaria WHERE NroCuenta = '".$registro."'";
		$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		//	-----------------
		mysql_query("COMMIT");
	}
}

//	tipo de transaccion bancaria
elseif ($modulo == "tipo_transaccion_bancaria") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-----------------
		//	inserto
		$sql = "INSERT INTO ap_bancotipotransaccion
				SET
					CodTipoTransaccion = '".$CodTipoTransaccion."',
					Descripcion = '".changeUrl($Descripcion)."',
					TipoTransaccion = '".$TipoTransaccion."',
					FlagVoucher = '".$FlagVoucher."',
					CodVoucher = '".$CodVoucher."',
					CodCuenta = '".$CodCuenta."',
					CodCuentaPub20 = '".$CodCuentaPub20."',
					FlagTransaccion = '".$FlagTransaccion."',
					FlagDeposito = '".$FlagDeposito."',
					FlagNotaCredito = '".$FlagNotaCredito."',
					FlagOtroIngreso = '".$FlagOtroIngreso."',
					FlagOtroAjuste = '".$FlagOtroAjuste."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	modificar registro
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizar
		$sql = "UPDATE ap_bancotipotransaccion
				SET
					Descripcion = '".changeUrl($Descripcion)."',
					TipoTransaccion = '".$TipoTransaccion."',
					FlagVoucher = '".$FlagVoucher."',
					CodVoucher = '".$CodVoucher."',
					CodCuenta = '".$CodCuenta."',
					CodCuentaPub20 = '".$CodCuentaPub20."',
					FlagTransaccion = '".$FlagTransaccion."',
					FlagDeposito = '".$FlagDeposito."',
					FlagNotaCredito = '".$FlagNotaCredito."',
					FlagOtroIngreso = '".$FlagOtroIngreso."',
					FlagOtroAjuste = '".$FlagOtroAjuste."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodTipoTransaccion = '".$CodTipoTransaccion."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	eliminar registro
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		$FlagTransaccion = getValorCampo("ap_bancotipotransaccion", "CodTipoTransaccion", "FlagTransaccion", $registro);
		if ($FlagTransaccion == "S") die("No puede eliminar este registro.<br /><strong>Transacci&oacute;n del Sistema</strong>");
		else {
			//	elimino
			$sql = "DELETE FROM ap_bancotipotransaccion WHERE CodTipoTransaccion = '".$registro."'";
			$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		}
		//	-----------------
		mysql_query("COMMIT");
	}
}

//	conceptos de gastos
elseif ($modulo == "concepto_gastos") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-----------------
		//	genero codigo
		$CodConceptoGasto = getCodigo("ap_conceptogastos", "CodConceptoGasto", 4);
		
		//	inserto
		$sql = "INSERT INTO ap_conceptogastos
				SET
					CodConceptoGasto = '".$CodConceptoGasto."',
					Descripcion = '".changeUrl($Descripcion)."',
					CodGastoGrupo = '".$CodGastoGrupo."',
					CodPartida = '".$CodPartida."',
					CodCuenta = '".$CodCuenta."',
					CodCuentaPub20 = '".$CodCuentaPub20."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	modificar registro
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizar
		$sql = "UPDATE ap_conceptogastos
				SET
					Descripcion = '".changeUrl($Descripcion)."',
					CodGastoGrupo = '".$CodGastoGrupo."',
					CodPartida = '".$CodPartida."',
					CodCuenta = '".$CodCuenta."',
					CodCuentaPub20 = '".$CodCuentaPub20."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodConceptoGasto = '".$CodConceptoGasto."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	eliminar registro
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		//	elimino
		$sql = "DELETE FROM ap_conceptogastos WHERE CodConceptoGasto = '".$registro."'";
		$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		//	-----------------
		mysql_query("COMMIT");
	}
}

//	cuentas bancarias
elseif ($modulo == "transacciones_bancarias") {
	$PeriodoContable = substr(formatFechaAMD($FechaTransaccion), 0, 7);
	$Anio = substr($PeriodoContable, 0, 4);
	$Mes = substr($PeriodoContable, 5, 2);
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-----------------
		//	genero codigo
		$NroTransaccion = getCodigo("ap_bancotransaccion", "NroTransaccion", 5);
		//	inserto
		$_Secuencia = 0;
		$detalles = split(";char:tr;", $detalles_transacciones);
		foreach ($detalles as $_linea) {
			list($_CodTipoTransaccion, $_TipoTransaccion, $_NroCuenta, $_Monto, $_CodTipoDocumento, $_CodigoReferenciaBanco, $_CodProveedor, $_CodCentroCosto, $_CodPartida, $_CategoriaProg, $_Ejercicio, $_CodPresupuesto, $_CodFuente) = split(";char:td;", $_linea);
			//	verifico disponibilidad
			if ($FlagPresupuesto == "S" && $_TipoTransaccion == "E") {
				list($MontoAjustado, $MontoCompromiso, $Pre, $Cotizaciones, $CajaChica) = disponibilidadPartida2($Anio, $CodOrganismo, $_CodPartida, $_CodPresupuesto, $_CodFuente);
				$MontoDisponible = $MontoAjustado - $MontoCompromiso;
				$MontoFinal = $MontoDisponible - abs($_Monto);
				if ($MontoFinal < 0) die("Se encontr&oacute; la partida $_CodPartida sin disponibilidad presupuestaria");
			}
			//	consulto si genra voucher
			$_FlagGeneraVoucher = getValorCampo("ap_bancotipotransaccion", "CodTipoTransaccion", "FlagVoucher", $_CodTipoTransaccion);
			//	si el tipo es 
			if ($_TipoTransaccion == "E") $_Monto = abs($_Monto) * -1;
			elseif ($_TipoTransaccion == "I") $_Monto = abs($_Monto);
			//	inserto
			$sql = "INSERT INTO ap_bancotransaccion
					SET
						NroTransaccion = '".$NroTransaccion."',
						CodOrganismo = '".$CodOrganismo."',
						FechaTransaccion = '".formatFechaAMD($FechaTransaccion)."',
						PeriodoContable = '".$PeriodoContable."',
						PreparadoPor = '".$PreparadoPor."',
						FechaPreparacion = '".formatFechaAMD($FechaPreparacion)."',
						Comentarios = '".changeUrl($Comentarios)."',
						FlagPresupuesto = '".$FlagPresupuesto."',
						CodPresupuesto = '".$_CodPresupuesto."',
						CodFuente = '".$_CodFuente."',
						Estado = '".$Estado."',
						Secuencia = '".++$_Secuencia."',
						CodTipoTransaccion = '".$_CodTipoTransaccion."',
						TipoTransaccion = '".$_TipoTransaccion."',
						NroCuenta = '".$_NroCuenta."',
						Monto = '".$_Monto."',
						CodTipoDocumento = '".$_CodTipoDocumento."',
						CodigoReferenciaBanco = '".$_CodigoReferenciaBanco."',
						CodigoReferenciaInterno = '".$_CodigoReferenciaBanco."',
						CodProveedor = '".$_CodProveedor."',
						CodCentroCosto = '".$_CodCentroCosto."',
						CodPartida = '".$_CodPartida."',
						FlagGeneraVoucher = '".$_FlagGeneraVoucher."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		//	-----------------
		mysql_query("COMMIT");
	}
	//	modificar registro
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		//	-----------------
		//	inserto
		$sql = "DELETE FROM ap_bancotransaccion WHERE NroTransaccion = '".$NroTransaccion."'";
		execute($sql);
		$_Secuencia = 0;
		$detalles = split(";char:tr;", $detalles_transacciones);
		foreach ($detalles as $_linea) {
			list($_CodTipoTransaccion, $_TipoTransaccion, $_NroCuenta, $_Monto, $_CodTipoDocumento, $_CodigoReferenciaBanco, $_CodProveedor, $_CodCentroCosto, $_CodPartida, $_CategoriaProg, $_Ejercicio, $_CodPresupuesto, $_CodFuente) = split(";char:td;", $_linea);
			//	verifico disponibilidad
			if ($FlagPresupuesto == "S" && $_TipoTransaccion == "E") {
				list($MontoAjustado, $MontoCompromiso, $Pre, $Cotizaciones, $CajaChica) = disponibilidadPartida2($Anio, $CodOrganismo, $_CodPartida, $_CodPresupuesto, $_CodFuente);
				$MontoDisponible = $MontoAjustado - $MontoCompromiso;
				$MontoFinal = $MontoDisponible - abs($_Monto);
				if ($MontoFinal < 0) die("Se encontr&oacute; la partida $_CodPartida sin disponibilidad presupuestaria");
			}
			//	consulto si genra voucher
			$_FlagGeneraVoucher = getValorCampo("ap_bancotipotransaccion", "CodTipoTransaccion", "FlagVoucher", $_CodTipoTransaccion);
			//	si el tipo es 
			if ($_TipoTransaccion == "E") $_Monto = abs($_Monto) * -1;
			elseif ($_TipoTransaccion == "I") $_Monto = abs($_Monto);
			//	inserto
			$sql = "INSERT INTO ap_bancotransaccion
					SET
						NroTransaccion = '".$NroTransaccion."',
						CodOrganismo = '".$CodOrganismo."',
						FechaTransaccion = '".formatFechaAMD($FechaTransaccion)."',
						PeriodoContable = '".$PeriodoContable."',
						PreparadoPor = '".$PreparadoPor."',
						FechaPreparacion = '".formatFechaAMD($FechaPreparacion)."',
						Comentarios = '".changeUrl($Comentarios)."',
						FlagPresupuesto = '".$FlagPresupuesto."',
						CodPresupuesto = '".$CodPresupuesto."',
						CodFuente = '".$_CodFuente."',
						Estado = '".$Estado."',
						Secuencia = '".++$_Secuencia."',
						CodTipoTransaccion = '".$_CodTipoTransaccion."',
						TipoTransaccion = '".$_TipoTransaccion."',
						NroCuenta = '".$_NroCuenta."',
						Monto = '".$_Monto."',						
						CodTipoDocumento = '".$_CodTipoDocumento."',
						CodigoReferenciaBanco = '".$_CodigoReferenciaBanco."',
						CodigoReferenciaInterno = '".$_CodigoReferenciaBanco."',						
						CodProveedor = '".$_CodProveedor."',
						CodCentroCosto = '".$_CodCentroCosto."',
						CodPartida = '".$_CodPartida."',
						FlagGeneraVoucher = '".$_FlagGeneraVoucher."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		//	-----------------
		mysql_query("COMMIT");
	}
	//	actualizar
	elseif ($accion == "actualizar") {
		mysql_query("BEGIN");
		//	-----------------
		//	inserto
		$sql = "UPDATE ap_bancotransaccion
				SET
					Estado = 'AP',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE NroTransaccion = '".$NroTransaccion."'";
		execute($sql);
		$_Secuencia = 1;
		$detalles = split(";char:tr;", $detalles_transacciones);
		foreach ($detalles as $_linea) {
			list($_CodTipoTransaccion, $_TipoTransaccion, $_NroCuenta, $_Monto, $_CodTipoDocumento, $_CodigoReferenciaBanco, $_CodProveedor, $_CodCentroCosto, $_CodPartida, $_CategoriaProg, $_Ejercicio, $_CodPresupuesto, $_CodFuente) = split(";char:td;", $_linea);
			//	si el tipo es 
			if ($_TipoTransaccion == "E") $_Monto = abs($_Monto) * -1;
			elseif ($_TipoTransaccion == "I") $_Monto = abs($_Monto);
			//	verifico disponibilidad
			if ($FlagPresupuesto == "S") {
				if ($_TipoTransaccion == "E") {
					list($MontoAjustado, $MontoCompromiso, $Pre, $Cotizaciones, $CajaChica) = disponibilidadPartida2($Anio, $CodOrganismo, $_CodPartida, $_CodPresupuesto, $_CodFuente);
					$MontoDisponible = $MontoAjustado - $MontoCompromiso;
					$MontoFinal = $MontoDisponible - abs($_Monto);
					if ($MontoFinal < 0) die("Se encontr&oacute; la partida $_CodPartida sin disponibilidad presupuestaria");
					$_MontoPresupuesto = abs($_Monto);
				}
				elseif ($_TipoTransaccion == "I") $_MontoPresupuesto = abs($_Monto) * -1;
				//	compromisos
				$sql = "INSERT INTO lg_distribucioncompromisos
						SET
							Anio = '".$Anio."',
							CodOrganismo = '".$CodOrganismo."',
							CodProveedor = '".$_CodProveedor."',
							CodTipoDocumento = '".$_CodTipoDocumento."',
							NroDocumento = '".$_CodigoReferenciaBanco."',
							Secuencia = '".$_Secuencia."',
							Linea = '1',
							Mes = '".$Mes."',
							CodCentroCosto = '".$_CodCentroCosto."',
							cod_partida = '".$_CodPartida."',
							Monto = '".$_MontoPresupuesto."',
							Periodo = '".$PeriodoContable."',
							CodPresupuesto = '".$CodPresupuesto."',
							CodFuente = '".$_CodFuente."',
							Ejercicio = '".$_Ejercicio."',
							Origen = 'TB',
							FechaEjecucion = '".formatFechaAMD($FechaTransaccion)."',
							Estado = 'CO',
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()";
				execute($sql);
				//	causados
				$sql = "INSERT INTO ap_distribucionobligacion
						SET
							CodProveedor = '".$_CodProveedor."',
							CodTipoDocumento = '".$_CodTipoDocumento."',
							NroDocumento = '".$_CodigoReferenciaBanco."',
							cod_partida = '".$_CodPartida."',
							CodCentroCosto = '".$_CodCentroCosto."',
							Monto = '".$_MontoPresupuesto."',
							Periodo = '".$PeriodoContable."',
							FlagCompromiso = 'S',
							Anio = '".$Anio."',
							CodOrganismo = '".$CodOrganismo."',
							CodPresupuesto = '".$CodPresupuesto."',
							CodFuente = '".$_CodFuente."',
							Ejercicio = '".$_Ejercicio."',
							FechaEjecucion = '".formatFechaAMD($FechaTransaccion)."',
							Estado = 'CA',
							Origen = 'TB',
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()";
				execute($sql);
				//	pagadas
				$sql = "INSERT INTO ap_ordenpagodistribucion
						SET
							Anio = '".$Anio."',
							CodOrganismo = '".$CodOrganismo."',
							NroOrden = '".$_CodigoReferenciaBanco."',
							Linea = '".$_Secuencia."',
							CodProveedor = '".$_CodProveedor."',
							CodTipoDocumento = '".$_CodTipoDocumento."',
							NroDocumento = '".$_CodigoReferenciaBanco."',
							Monto = '".$_MontoPresupuesto."',
							MontoPagado = '".$_MontoPresupuesto."',
							CodCentroCosto = '".$_CodCentroCosto."',
							cod_partida = '".$_CodPartida."',
							CodPresupuesto = '".$CodPresupuesto."',
							CodFuente = '".$_CodFuente."',
							Ejercicio = '".$_Ejercicio."',
							FlagNoAfectoIGV = 'S',
							Periodo = '".$PeriodoContable."',
							Origen = 'TB',
							FechaEjecucion = '".formatFechaAMD($FechaTransaccion)."',
							Estado = 'PA',
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()";
				execute($sql);
			}
		}
		//	-----------------
		mysql_query("COMMIT");
	}
	//	actualizar
	elseif ($accion == "desactualizar") {
		mysql_query("BEGIN");
		//	-----------------
		//	valido contabilidad
		$sql = "SELECT FlagContabilizacionPendiente, FlagContPendientePub20
				FROM ap_bancotransaccion
				WHERE NroTransaccion = '".$NroTransaccion."'";
		$field_cont = getRecord($sql);
		if ($field_cont['FlagContabilizacionPendiente'] == 'N' || $field_cont['FlagContPendientePub20'] == 'N') die("No se puede desactualizar la transacci&oacute;n porque tiene vouchers contables generados.");
		//	inserto
		$sql = "UPDATE ap_bancotransaccion
				SET
					Estado = 'PR',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE NroTransaccion = '".$NroTransaccion."'";
		$query_update = mysql_query($sql) or die ($sql.mysql_error());
		$_Secuencia = 0;
		$detalles = split(";char:tr;", $detalles_transacciones);
		foreach ($detalles as $_linea) {
			list($_CodTipoTransaccion, $_TipoTransaccion, $_NroCuenta, $_Monto, $_CodTipoDocumento, $_CodigoReferenciaBanco, $_CodProveedor, $_CodCentroCosto, $_CodPartida, $_CategoriaProg, $_Ejercicio, $_CodPresupuesto, $_CodFuente) = split(";char:td;", $_linea);
			//	si el tipo es 
			if ($_TipoTransaccion == "E") $_Monto = abs($_Monto) * -1;
			elseif ($_TipoTransaccion == "I") $_Monto = abs($_Monto);
			//	verifico disponibilidad
			if ($FlagPresupuesto == "S") {
				if ($_TipoTransaccion == "I") {
					list($MontoAjustado, $MontoCompromiso, $Pre, $Cotizaciones, $CajaChica) = disponibilidadPartida2($Anio, $CodOrganismo, $_CodPartida, $_CodPresupuesto, $_CodFuente);
					$MontoDisponible = $MontoAjustado - $MontoCompromiso;
					$MontoFinal = $MontoDisponible - abs($_Monto);
					if ($MontoFinal < 0) die("Se encontr&oacute; la partida $_CodPartida sin disponibilidad presupuestaria");
				}
				//	compromisos
				$sql = "DELETE FROM lg_distribucioncompromisos
						WHERE
							Anio = '".$Anio."' AND
							CodOrganismo = '".$CodOrganismo."' AND 
							CodProveedor = '".$_CodProveedor."' AND
							CodTipoDocumento = '".$_CodTipoDocumento."' AND
							NroDocumento = '".$_CodigoReferenciaBanco."'";
				$query_insert = mysql_query($sql) or die ($sql.mysql_error());
				//	causados
				$sql = "DELETE FROM ap_distribucionobligacion
						WHERE
							CodProveedor = '".$_CodProveedor."' AND
							CodTipoDocumento = '".$_CodTipoDocumento."' AND
							NroDocumento = '".$_CodigoReferenciaBanco."'";
				$query_insert = mysql_query($sql) or die ($sql.mysql_error());
				//	pagadas
				$sql = "DELETE FROM ap_ordenpagodistribucion
						WHERE
							Anio = '".$Anio."' AND
							CodOrganismo = '".$CodOrganismo."' AND
							NroOrden = '".$_CodigoReferenciaBanco."'";
				$query_insert = mysql_query($sql) or die ($sql.mysql_error());
			}
		}
		//	-----------------
		mysql_query("COMMIT");
	}
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		list($NroTransaccion, $Secuencia) = explode("_", $registro);
		//	consulto el estado
		$sql = "SELECT Estado FROM ap_bancotransaccion WHERE NroTransaccion = '".$NroTransaccion."' AND Secuencia = '".$Secuencia."'";
		$Estado = getVar3($sql);
		if ($Estado != "PR") die("Solo se pueden eliminar las Transacciones PENDIENTES");
		else {
			//	eliminar
			$sql = "DELETE FROM ap_bancotransaccion WHERE NroTransaccion = '".$NroTransaccion."' AND Secuencia = '".$Secuencia."'";
			execute($sql);
		}
		//	-----------------
		mysql_query("COMMIT");
	}
}

//	autorizacion de caja chica
elseif ($modulo == "autorizacion_cajachica") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-----------------
		$Monto = setNumero($Monto);
		##	
		if ($_PARAMETRO['UBACVIAT'] == 'UCAU')
			$UnidadTributaria = getVar3("SELECT Valor FROM mastunidadaritmetica WHERE Anio = '$_PARAMETRO[ANIOUBCVIAT]'");
		else
			$UnidadTributaria = getVar3("SELECT Valor FROM mastunidadtributaria WHERE Anio = '$_PARAMETRO[UTANIOVIAT]'");
		$Tope = 200 * $UnidadTributaria;
		if ($Monto > $Tope) die("El Monto Autorizado excede el monto máximo establecido en la ley <strong>(".number_format($UnidadTributaria,2,',','.')." * 200) = ".number_format($Tope,2,',','.')."</strong>");
		//	inserto
		$sql = "INSERT INTO ap_cajachicaautorizacion
				SET
					CodOrganismo = '$CodOrganismo',
					CodEmpleado = '$CodPersona',
					Monto = '$Monto',
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	modificar registro
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		//	-----------------
		$Monto = setNumero($Monto);
		##	
		if ($_PARAMETRO['UBACVIAT'] == 'UCAU')
			$UnidadTributaria = getVar3("SELECT Valor FROM mastunidadaritmetica WHERE Anio = '$_PARAMETRO[ANIOUBCVIAT]'");
		else
			$UnidadTributaria = getVar3("SELECT Valor FROM mastunidadtributaria WHERE Anio = '$_PARAMETRO[UTANIOVIAT]'");
		$Tope = 200 * $UnidadTributaria;
		if ($Monto > $Tope) die("El Monto Autorizado excede el monto máximo establecido en la ley <strong>(".number_format($UnidadTributaria,2,',','.')." * 200) = ".number_format($Tope,2,',','.')."</strong>");
		//	actualizar
		$sql = "UPDATE ap_cajachicaautorizacion
				SET
					Monto = '$Monto',
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '$CodOrganismo' AND
					CodEmpleado = '$CodPersona'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	eliminar registro
	elseif ($accion == "eliminar") {
		list($CodOrganismo, $CodPersona) = split("[_]", $registro);
		//	elimino
		$sql = "DELETE FROM ap_cajachicaautorizacion
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					CodEmpleado = '".$CodPersona."'";
		$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	}
}

//	cheques
elseif ($modulo == "cheques") {
	//	entregar
	if ($accion == "entregar") {
		mysql_query("BEGIN");
		//	-----------------
		$detalles_pagos = explode(";char:tr;", $registro);
		foreach ($detalles_pagos as $_linea) {
			list($NroProceso, $Secuencia) = explode("_", $_linea);
			##	actualizo
			$sql = "UPDATE ap_pagos
					SET
						FechaEntregado = '".formatFechaAMD($FechaEntregado)."',
						EstadoEntrega = 'E'
					WHERE
						NroProceso = '".$NroProceso."' AND
						Secuencia = '".$Secuencia."'";
			execute($sql);
		}
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	devolver
	elseif ($accion == "devolver") {
		mysql_query("BEGIN");
		//	-----------------
		$detalles_pagos = explode(";char:tr;", $registro);
		foreach ($detalles_pagos as $_linea) {
			list($NroProceso, $Secuencia) = explode("_", $_linea);
			##	actualizo
			$sql = "UPDATE ap_pagos
					SET
						FechaEntregado = '".formatFechaAMD($FechaEntregado)."',
						EstadoEntrega = 'C'
					WHERE
						NroProceso = '".$NroProceso."' AND
						Secuencia = '".$Secuencia."'";
			execute($sql);
		}
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	cobrar
	elseif ($accion == "cobrar") {
		mysql_query("BEGIN");
		//	-----------------
		$detalles_pagos = explode(";char:tr;", $registro);
		foreach ($detalles_pagos as $_linea) {
			list($NroProceso, $Secuencia) = explode("_", $_linea);
			if ($fFlagCobrado == "S") $FlagCobrado = "N"; else $FlagCobrado = "S";
			##	consulto datos de voucher
			$sql = "SELECT *
					FROM ap_pagos
					WHERE
						NroProceso = '".$NroProceso."' AND
						Secuencia = '".$Secuencia."'";
			$field_pago = getRecord($sql);
			##	actualizo
			$sql = "UPDATE ap_pagos
					SET
						FechaCobranza = '".formatFechaAMD($FechaCobranza)."',
						NroPagoVoucher = '".$NroPagoVoucher."',
						FlagCobrado = '".$FlagCobrado."'
					WHERE
						NroProceso = '".$NroProceso."' AND
						Secuencia = '".$Secuencia."'";
			execute($sql);
			##	actualizo
			$sql = "UPDATE ac_voucherdet
					SET NroPagoVoucher = '".$NroPagoVoucher."'
					WHERE
						CodOrganismo = '".$field_pago['CodOrganismo']."' AND
						((Periodo = '".$field_pago['PeriodoPagoPub20']."' AND
						  Voucher = '".$field_pago['VoucherPagoPub20']."' AND
						  CodContabilidad = 'F') OR
						 (Periodo = '".$field_pago['VoucherPeriodo']."' AND
						  Voucher = '".$field_pago['VoucherPago']."' AND
						  CodContabilidad = 'T'))";
			execute($sql);
		}
		//	-----------------
		mysql_query("COMMIT");
	}
}
?>