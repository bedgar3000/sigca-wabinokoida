<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion-".$AnioActual.$MesActual.$DiaActual."_".$HorasActual.$MinutosActual.$SegundosActual.".sql", "w+");
//	$__archivo = fopen("_"."$modulo-$accion".".sql", "w+");
##############################################################################/
if ($modulo == "formulario") 
{
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		##	-----------------
		$FechaTransaccion = formatFechaAMD($FechaTransaccion);
		$FechaPreparacion = formatFechaAMD($FechaPreparacion);
		$Ejercicio = substr($FechaTransaccion,0,4);
		$FlagPresupuesto = $FlagPresupuesto?'S':'N';
		##	valido
		if (!trim($FechaTransaccion) || !trim($Comentarios)) die("Debe llenar los campos (*) obligatorios.");
		elseif (!validateDate($FechaTransaccion,'Y-m-d')) die("Formato de <strong>Fecha</strong> incorrecta");
		##	codigo
		$NroTransaccion = codigo('ap_bancotransaccion','NroTransaccion',5);
		##	detalle
		$Secuencia = 0;
		for ($i=0; $i < count($detalle_CodTipoTransaccion); $i++) 
		{
			++$Secuencia;
			$detalle_Monto[$i] = setNumero($detalle_Monto[$i]);
			$iCodTipoDocumento = (empty($detalle_CodTipoDocumento[$i])?"NULL":"'$detalle_CodTipoDocumento[$i]'");
			##	
			$sql = "SELECT * FROM ap_bancotipotransaccion WHERE CodTipoTransaccion = '$detalle_CodTipoTransaccion[$i]'";
			$field_btt = getRecord($sql);
			##	
			if (!trim($detalle_CodTipoTransaccion[$i]) || !trim($detalle_TipoTransaccion[$i]) || !trim($detalle_NroCuenta[$i]) || !trim($detalle_CodProveedor[$i]) || !trim($detalle_CodigoReferenciaBanco[$i]) || 
				($FlagPresupuesto == 'S' && (!trim($detalle_CodPartida[$i]) || !trim($detalle_CodPresupuesto[$i]) || !trim($detalle_CodFuente[$i])))) 
					die("Debe llenar los campos (*) obligatorios.");
			elseif (is_nan($detalle_Monto[$i]) || !$detalle_Monto[$i]) die("Monto incorrecto");
			##	
			if ($detalle_TipoTransaccion[$i] == "E") $detalle_Monto[$i] = abs($detalle_Monto[$i]) * -1;
			elseif ($detalle_TipoTransaccion[$i] == "I") $detalle_Monto[$i] = abs($detalle_Monto[$i]);
			##	
			$sql = "INSERT INTO ap_bancotransaccion
					SET
						NroTransaccion = '$NroTransaccion',
						Secuencia = '$Secuencia',
						CodOrganismo = '$CodOrganismo',
						CodTipoTransaccion = '$detalle_CodTipoTransaccion[$i]',
						TipoTransaccion = '$detalle_TipoTransaccion[$i]',
						NroCuenta = '$detalle_NroCuenta[$i]',
						CodTipoDocumento = $iCodTipoDocumento,
						CodProveedor = '$detalle_CodProveedor[$i]',
						CodCentroCosto = '$detalle_CodCentroCosto[$i]',
						PreparadoPor = '$PreparadoPor',
						FechaPreparacion = '$FechaPreparacion',
						FechaTransaccion = '$FechaTransaccion',
						PeriodoContable = '$PeriodoContable',
						Monto = '$detalle_Monto[$i]',
						FlagGeneraVoucher = '$field_btt[FlagVoucher]',
						FlagGeneraVoucherPub20 = '$field_btt[FlagVoucher]',
						CodigoReferenciaBanco = '$detalle_CodigoReferenciaBanco[$i]',
						CodigoReferenciaInterno = '$detalle_CodigoReferenciaBanco[$i]',
						Comentarios = '$Comentarios',
						FlagPresupuesto = '$FlagPresupuesto',
						CodPartida = '$detalle_CodPartida[$i]',
						CodPresupuesto = '$detalle_CodPresupuesto[$i]',
						CodFuente = '$detalle_CodFuente[$i]',
						FlagAutomatico = 'N',
						Ejercicio = '$Ejercicio',
						Estado = '$Estado',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
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
		$FechaTransaccion = formatFechaAMD($FechaTransaccion);
		$FechaPreparacion = formatFechaAMD($FechaPreparacion);
		$Ejercicio = substr($FechaTransaccion,0,4);
		$FlagPresupuesto = $FlagPresupuesto?'S':'N';
		##	valido
		if (!trim($FechaTransaccion) || !trim($Comentarios)) die("Debe llenar los campos (*) obligatorios.");
		elseif (!validateDate($FechaTransaccion,'Y-m-d')) die("Formato de <strong>Fecha</strong> incorrecta");
		##	detalle
		$Secuencia = 0;
		for ($i=0; $i < count($detalle_CodTipoTransaccion); $i++) 
		{
			++$Secuencia;
			$detalle_Monto[$i] = setNumero($detalle_Monto[$i]);
			$iCodTipoDocumento = (empty($detalle_CodTipoDocumento[$i])?"NULL":"'$detalle_CodTipoDocumento[$i]'");
			##	
			$sql = "SELECT * FROM ap_bancotipotransaccion WHERE CodTipoTransaccion = '$detalle_CodTipoTransaccion[$i]'";
			$field_btt = getRecord($sql);
			##	
			if (!trim($detalle_CodTipoTransaccion[$i]) || !trim($detalle_TipoTransaccion[$i]) || !trim($detalle_NroCuenta[$i]) || !trim($detalle_CodProveedor[$i]) || !trim($detalle_CodigoReferenciaBanco[$i]) || 
				($FlagPresupuesto == 'S' && (!trim($detalle_CodPartida[$i]) || !trim($detalle_CodPresupuesto[$i]) || !trim($detalle_CodFuente[$i])))) 
					die("Debe llenar los campos (*) obligatorios.");
			elseif (is_nan($detalle_Monto[$i]) || !$detalle_Monto[$i]) die("Monto incorrecto");
			##	
			if ($detalle_TipoTransaccion[$i] == "E") $detalle_Monto[$i] = abs($detalle_Monto[$i]) * -1;
			elseif ($detalle_TipoTransaccion[$i] == "I") $detalle_Monto[$i] = abs($detalle_Monto[$i]);
			##	
			$sql = "UPDATE ap_bancotransaccion
					SET
						NroCuenta = '$detalle_NroCuenta[$i]',
						CodTipoDocumento = $iCodTipoDocumento,
						CodProveedor = '$detalle_CodProveedor[$i]',
						CodCentroCosto = '$detalle_CodCentroCosto[$i]',
						FechaTransaccion = '$FechaTransaccion',
						PeriodoContable = '$PeriodoContable',
						Monto = '$detalle_Monto[$i]',
						FlagGeneraVoucher = '$field_btt[FlagVoucher]',
						FlagGeneraVoucherPub20 = '$field_btt[FlagVoucher]',
						CodigoReferenciaBanco = '$detalle_CodigoReferenciaBanco[$i]',
						CodigoReferenciaInterno = '$detalle_CodigoReferenciaBanco[$i]',
						Comentarios = '$Comentarios',
						FlagPresupuesto = '$FlagPresupuesto',
						CodPartida = '$detalle_CodPartida[$i]',
						CodPresupuesto = '$detalle_CodPresupuesto[$i]',
						CodFuente = '$detalle_CodFuente[$i]',
						Ejercicio = '$Ejercicio',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()
					WHERE
						NroTransaccion = '$NroTransaccion'
						AND Secuencia = '$detalle_Secuencia[$i]'";
			execute($sql);
		}
		##	-----------------
		mysql_query("COMMIT");
	}
	//	actualizar
	elseif ($accion == "actualizar") {
		mysql_query("BEGIN");
		##	-----------------
		$FechaActualizado = formatFechaAMD($FechaActualizado);
		##	
		$sql = "SELECT * FROM ap_bancotransaccion WHERE NroTransaccion = '$NroTransaccion' GROUP BY NroTransaccion";
		$field = getRecord($sql);
		if ($field['Estado'] != 'PR') die('No puede Actualizar una transacci&oacute;n en Estado <strong>'.printValores('ESTADO-BANCARIO',$field['Estado']).'</strong>');
		##	actualizar
		$sql = "UPDATE ap_bancotransaccion
				SET
					ActualizadoPor = '$ActualizadoPor',
					FechaActualizado = '$FechaActualizado',
					Estado = 'AP',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE NroTransaccion = '$NroTransaccion'";
		execute($sql);
		##	afecta presupuesto
		if ($field['FlagPresupuesto'] == 'S')
		{
			##	detalles
			for ($i=0; $i < count($detalle_CodTipoTransaccion); $i++) 
			{
				$sql = "SELECT * FROM ap_bancotransaccion WHERE NroTransaccion = '$NroTransaccion' AND Secuencia = '$detalle_Secuencia[$i]'";
				$field_bt = getRecord($sql);
				##	
				++$Secuencia;
				$Anio = substr($field_bt['PeriodoContable'], 0, 4);
				$Mes = substr($field_bt['PeriodoContable'], 6, 2);
				##	
				if ($field_bt['TipoTransaccion'] == "E") 
				{
					$Monto = abs($field_bt['Monto']);
					##	
					list($_MontoAjustado, $_MontoCompromiso, $_PreCompromiso, $_CotizacionesAsignadas) = disponibilidadPartida2($Anio, $field_bt['CodOrganismo'], $field_bt['CodPartida'], $field_bt['CodPresupuesto'], $field_bt['CodFuente']);
					$_MontoDisponible = $_MontoAjustado - $_MontoCompromiso;
					if (($_MontoDisponible - $Monto) < 0) die("Se encontr&oacute; la partida <strong>$field_bt[CodPartida]</strong> sin Disponibilidad Presupuestaria");
				}
				elseif ($field_bt['TipoTransaccion'] == "I") 
				{
					$Monto = abs($field_bt['Monto']) * -1;
				}
				##	compromiso
				$sql = "INSERT INTO lg_distribucioncompromisos
						SET
							Anio = '$Anio',
							CodOrganismo = '$field_bt[CodOrganismo]',
							CodProveedor = '$field_bt[CodProveedor]',
							CodTipoDocumento = '$field[CodTipoDocumento]',
							NroDocumento = '$field_bt[CodigoReferenciaBanco]',
							Secuencia = '$Secuencia',
							Linea = '1',
							Mes = '$Mes',
							CodCentroCosto = '$field_bt[CodCentroCosto]',
							cod_partida = '$field_bt[CodPartida]',
							Monto = '$Monto',
							Periodo = '$field[PeriodoContable]',
							CodPresupuesto = '$field[CodPresupuesto]',
							CodFuente = '$field[CodFuente]',
							Ejercicio = '$Anio',
							Origen = 'TB',
							FechaEjecucion = '$field[FechaTransaccion]',
							Estado = 'CO',
							UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
							UltimaFecha = NOW()";
				execute($sql);
				##	causado
				$sql = "INSERT INTO ap_distribucionobligacion
						SET
							CodProveedor = '$field_bt[CodProveedor]',
							CodTipoDocumento = '$field[CodTipoDocumento]',
							NroDocumento = '$field_bt[CodigoReferenciaBanco]',
							cod_partida = '$field_bt[CodPartida]',
							CodCentroCosto = '$field_bt[CodCentroCosto]',
							Monto = '$Monto',
							Periodo = '$field[PeriodoContable]',
							FlagCompromiso = 'S',
							Anio = '$Anio',
							CodOrganismo = '$field_bt[CodOrganismo]',
							CodPresupuesto = '$field[CodPresupuesto]',
							CodFuente = '$field[CodFuente]',
							Ejercicio = '$Anio',
							FechaEjecucion = '$field[FechaTransaccion]',
							Estado = 'CA',
							Origen = 'TB',
							UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
							UltimaFecha = NOW()";
				execute($sql);
				##	pagado
				$sql = "INSERT INTO ap_ordenpagodistribucion
						SET
							Anio = '$Anio',
							CodOrganismo = '$field_bt[CodOrganismo]',
							NroOrden = '$field_bt[CodigoReferenciaBanco]',
							Linea = '$Secuencia',
							CodProveedor = '$field_bt[CodProveedor]',
							CodTipoDocumento = '$field_bt[CodTipoDocumento]',
							NroDocumento = '$field_bt[CodigoReferenciaBanco]',
							Monto = '$Monto',
							MontoPagado = '$Monto',
							CodCentroCosto = '$field_bt[CodCentroCosto]',
							cod_partida = '$field_bt[CodPartida]',
							CodPresupuesto = '$field[CodPresupuesto]',
							CodFuente = '$field[CodFuente]',
							Ejercicio = '$Anio',
							FlagNoAfectoIGV = 'S',
							Periodo = '$field[PeriodoContable]',
							Origen = 'TB',
							FechaEjecucion = '$field[FechaTransaccion]',
							Estado = 'PA',
							UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
							UltimaFecha = NOW()";
				execute($sql);
			}
		}
		echo "|" . $field['FlagGeneraVoucher'];
		##	-----------------
		mysql_query("COMMIT");
	}
	//	desactualizar
	elseif ($accion == "desactualizar") {
		mysql_query("BEGIN");
		##	-----------------
		$FechaActualizado = formatFechaAMD($FechaActualizado);
		##	
		$sql = "SELECT * FROM ap_bancotransaccion WHERE NroTransaccion = '$NroTransaccion' GROUP BY NroTransaccion";
		$field = getRecord($sql);
		if ($field['Estado'] != 'AP' && $field['Estado'] != 'CO') die('No puede Desactualizar una transacci&oacute;n en Estado <strong>'.printValores('ESTADO-BANCARIO',$field['Estado']).'</strong>');
		elseif ($field['FlagAutomatico'] == 'S') die('No puede Desactualizar una transacci&oacute;n Autom&aacute;tica del Sistema');
		##	actualizar
		$sql = "UPDATE ap_bancotransaccion
				SET
					ActualizadoPor = '$ActualizadoPor',
					FechaActualizado = '$FechaActualizado',
					Estado = 'PR',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE NroTransaccion = '$NroTransaccion'";
		execute($sql);
		##	afecta presupuesto
		if ($field['FlagPresupuesto'] == 'S')
		{
			##	detalles
			for ($i=0; $i < count($detalle_CodTipoTransaccion); $i++) 
			{
				$sql = "SELECT * FROM ap_bancotransaccion WHERE NroTransaccion = '$NroTransaccion' AND Secuencia = '$detalle_Secuencia[$i]'";
				$field_bt = getRecord($sql);
				##	
				++$Secuencia;
				$Anio = substr($field_bt['PeriodoContable'], 0, 4);
				$Mes = substr($field_bt['PeriodoContable'], 6, 2);
				##	
				if ($field_bt['TipoTransaccion'] == "E") 
				{
					$Monto = abs($field_bt['Monto']);
				}
				elseif ($field_bt['TipoTransaccion'] == "I") 
				{
					$Monto = abs($field_bt['Monto']) * -1;
					##	
					list($_MontoAjustado, $_MontoCompromiso, $_PreCompromiso, $_CotizacionesAsignadas) = disponibilidadPartida2($Anio, $field_bt['CodOrganismo'], $field_bt['CodPartida'], $field_bt['CodPresupuesto'], $field_bt['CodFuente']);
					$_MontoDisponible = $_MontoAjustado - $_MontoCompromiso;
					if (($_MontoDisponible + $Monto) < 0) die("Se encontr&oacute; la partida <strong>$field_bt[CodPartida]</strong> sin Disponibilidad Presupuestaria");
				}
				##	compromiso
				$sql = "DELETE FROM lg_distribucioncompromisos
						WHERE
							Anio = '$Anio'
							AND CodOrganismo = '$field_bt[CodOrganismo]'
							AND CodProveedor = '$field_bt[CodProveedor]'
							AND CodTipoDocumento = '$field[CodTipoDocumento]'
							AND NroDocumento = '$field_bt[CodigoReferenciaBanco]'
							AND Secuencia = '$Secuencia'
							AND Linea = '1'";
				execute($sql);
				##	causado
				$sql = "DELETE FROM ap_distribucionobligacion
						WHERE
							CodProveedor = '$field_bt[CodProveedor]'
							AND CodTipoDocumento = '$field[CodTipoDocumento]'
							AND NroDocumento = '$field_bt[CodigoReferenciaBanco]'
							AND cod_partida = '$field_bt[CodPartida]'
							AND CodCentroCosto = '$field_bt[CodCentroCosto]'";
				execute($sql);
				##	pagado
				$sql = "DELETE FROM ap_ordenpagodistribucion
						WHERE
							Anio = '$Anio'
							AND CodOrganismo = '$field_bt[CodOrganismo]'
							AND NroOrden = '$field_bt[CodigoReferenciaBanco]'
							AND Linea = '$Secuencia'";
				execute($sql);
			}
		}
		##	VOUCHER DE ANULACION (ONCO)
		if ($field['FlagGeneraVoucher'] == 'S')
		{
			##	voucher
			$sql = "SELECT *
					FROM ac_vouchermast
					WHERE
						CodOrganismo = '$field[CodOrganismo]'
						AND Periodo = '$field[VoucherPeriodo]'
						AND Voucher = '$field[Voucher]'";
			$field_voucher = getRecords($sql);
			foreach ($field_voucher as $voucher)
			{
				##	codigo
				$NroVoucher = codigo('ac_vouchermast','NroVoucher',4,['CodOrganismo','Periodo','CodVoucher','CodContabilidad'],[$field['CodOrganismo'],$PeriodoActual,$field_voucher['CodVoucher'],$field_voucher['CodContabilidad']]);
				$NroInterno = codigo('ac_vouchermast','NroInterno',10);
				$Voucher = $voucher['CodVoucher'] . '-' . $NroVoucher;
				##	
				$sql = "INSERT INTO ac_vouchermast
						SET
							CodOrganismo = '$voucher[CodOrganismo]',
							Periodo = '$PeriodoActual',
							Voucher = '$Voucher',
							CodContabilidad = '$voucher[CodContabilidad]',
							Prefijo = '$voucher[Prefijo]',
							NroVoucher = '$voucher[NroVoucher]',
							CodVoucher = '$voucher[CodVoucher]',
							CodDependencia = '$voucher[CodDependencia]',
							CodModeloVoucher = '$voucher[CodModeloVoucher]',
							CodSistemaFuente = '$voucher[CodSistemaFuente]',
							Creditos = '$voucher[Creditos]',
							Debitos = '$voucher[Debitos]',
							Lineas = '$voucher[Lineas]',
							PreparadoPor = '$_SESSION[CODPERSONA_ACTUAL]',
							FechaPreparacion = '$FechaActual',
							AprobadoPor = '$_SESSION[CODPERSONA_ACTUAL]',
							FechaAprobacion = '$FechaActual',
							TituloVoucher = '(ANULACIÓN VOUCHER $voucher[Periodo]-$voucher[Voucher]) $voucher[TituloVoucher]',
							ComentariosVoucher = '(ANULACIÓN VOUCHER $voucher[Periodo]-$voucher[Voucher]) $voucher[ComentariosVoucher]',
							NroInterno = '$NroInterno',
							FlagTransferencia = '$voucher[FlagTransferencia]',
							Estado = 'CO',
							CodLibroCont = '$voucher[CodLibroCont]',
							UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
							UltimaFecha = NOW()";
				execute($sql);
			}
			##	voucher (detalles)
			$sql = "SELECT *
					FROM ac_voucherdet
					WHERE
						CodOrganismo = '$field[CodOrganismo]'
						AND Periodo = '$field[VoucherPeriodo]'
						AND Voucher = '$field[Voucher]'";
			$field_detalle = getRecord($sql);
			foreach ($field_detalle as $voucher)
			{
				$MontoVoucher = $voucher['MontoVoucher'] * -1;
				$MontoPost = $voucher['MontoPost'] * -1;
				$sql = "INSERT INTO ac_voucherdet
						SET
							CodOrganismo = '$voucher[CodOrganismo]',
							Periodo = '$PeriodoActual',
							Voucher = '$Voucher',
							Linea = '$voucher[Linea]',
							CodContabilidad = '$voucher[CodContabilidad]',
							CodCuenta = '$voucher[CodCuenta]',
							MontoVoucher = '$MontoVoucher',
							MontoPost = '$MontoPost',
							CodPersona = '$voucher[CodPersona]',
							NroCheque = '$voucher[NroCheque]',
							FechaVoucher = '$voucher[FechaVoucher]',
							CodCentroCosto = '$voucher[CodCentroCosto]',
							ReferenciaTipoDocumento = '$voucher[ReferenciaTipoDocumento]',
							ReferenciaNroDocumento = '$voucher[ReferenciaNroDocumento]',
							Descripcion = '$voucher[Descripcion]',
							NroPagoVoucher = '$voucher[NroPagoVoucher]',
							Estado = 'CO',
							UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
							UltimaFecha = NOW()";
				execute($sql);
			}
		}
		##	VOUCHER DE ANULACION (PUB.20)
		if ($field['FlagGeneraVoucherPub20'] == 'S')
		{
			##	voucher
			$sql = "SELECT *
					FROM ac_vouchermast
					WHERE
						CodOrganismo = '$field[CodOrganismo]'
						AND Periodo = '$field[VoucherPeriodoPub20]'
						AND Voucher = '$field[VoucherPub20]'";
			$field_voucher = getRecords($sql);
			foreach ($field_voucher as $voucher)
			{
				##	codigo
				$NroVoucher = codigo('ac_vouchermast','NroVoucher',4,['CodOrganismo','Periodo','CodVoucher','CodContabilidad'],[$field['CodOrganismo'],$PeriodoActual,$voucher['CodVoucher'],$voucher['CodContabilidad']]);
				$NroInterno = codigo('ac_vouchermast','NroInterno',10);
				$Voucher = $voucher['CodVoucher'] . '-' . $NroVoucher;
				##	
				$sql = "INSERT INTO ac_vouchermast
						SET
							CodOrganismo = '$voucher[CodOrganismo]',
							Periodo = '$PeriodoActual',
							Voucher = '$Voucher',
							CodContabilidad = '$voucher[CodContabilidad]',
							Prefijo = '$voucher[Prefijo]',
							NroVoucher = '$voucher[NroVoucher]',
							CodVoucher = '$voucher[CodVoucher]',
							CodDependencia = '$voucher[CodDependencia]',
							CodModeloVoucher = '$voucher[CodModeloVoucher]',
							CodSistemaFuente = '$voucher[CodSistemaFuente]',
							Creditos = '$voucher[Creditos]',
							Debitos = '$voucher[Debitos]',
							Lineas = '$voucher[Lineas]',
							PreparadoPor = '$_SESSION[CODPERSONA_ACTUAL]',
							FechaPreparacion = '$FechaActual',
							AprobadoPor = '$_SESSION[CODPERSONA_ACTUAL]',
							FechaAprobacion = '$FechaActual',
							TituloVoucher = '(ANULACIÓN VOUCHER $voucher[Periodo]-$voucher[Voucher]) $voucher[TituloVoucher]',
							ComentariosVoucher = '(ANULACIÓN VOUCHER $voucher[Periodo]-$voucher[Voucher]) $voucher[ComentariosVoucher]',
							NroInterno = '$NroInterno',
							FlagTransferencia = '$voucher[FlagTransferencia]',
							Estado = 'CO',
							CodLibroCont = '$voucher[CodLibroCont]',
							UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
							UltimaFecha = NOW()";
				execute($sql);
			}
			##	voucher (detalles)
			$sql = "SELECT *
					FROM ac_voucherdet
					WHERE
						CodOrganismo = '$field[CodOrganismo]'
						AND Periodo = '$field[VoucherPeriodo]'
						AND Voucher = '$field[Voucher]'";
			$field_detalle = getRecord($sql);
			foreach ($field_detalle as $voucher)
			{
				$MontoVoucher = $voucher['MontoVoucher'] * -1;
				$MontoPost = $voucher['MontoPost'] * -1;
				$sql = "INSERT INTO ac_voucherdet
						SET
							CodOrganismo = '$voucher[CodOrganismo]',
							Periodo = '$PeriodoActual',
							Voucher = '$Voucher',
							Linea = '$voucher[Linea]',
							CodContabilidad = '$voucher[CodContabilidad]',
							CodCuenta = '$voucher[CodCuenta]',
							MontoVoucher = '$MontoVoucher',
							MontoPost = '$MontoPost',
							CodPersona = '$voucher[CodPersona]',
							NroCheque = '$voucher[NroCheque]',
							FechaVoucher = '$voucher[FechaVoucher]',
							CodCentroCosto = '$voucher[CodCentroCosto]',
							ReferenciaTipoDocumento = '$voucher[ReferenciaTipoDocumento]',
							ReferenciaNroDocumento = '$voucher[ReferenciaNroDocumento]',
							Descripcion = '$voucher[Descripcion]',
							NroPagoVoucher = '$voucher[NroPagoVoucher]',
							Estado = 'CO',
							UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
							UltimaFecha = NOW()";
				execute($sql);
			}
		}
		##	
		echo "|" . $field['FlagGeneraVoucher'];
		##	-----------------
		mysql_query("COMMIT");
	}
	//	anular
	elseif ($accion == "anular") {
		mysql_query("BEGIN");
		##	-----------------
		$FechaActualizado = formatFechaAMD($FechaActualizado);
		##	
		$sql = "SELECT * FROM ap_bancotransaccion WHERE NroTransaccion = '$NroTransaccion' GROUP BY NroTransaccion";
		$field = getRecord($sql);
		if ($field['Estado'] != 'PR') die('No puede Anular una transacci&oacute;n en Estado <strong>'.printValores('ESTADO-BANCARIO',$field['Estado']).'</strong>');
		elseif ($field['FlagGeneraVoucher'] == 'S' && trim($field['Voucher'])) die('No puede Anular una transacci&oacute;n Autom&aacute;tica del Sistema');
		else
		{
			##	actualizar
			$sql = "UPDATE ap_bancotransaccion
					SET
						AnuladoPor = '$_SESSION[CODPERSONA_ACTUAL]',
						FechaAnulado = NOW(),
						Estado = 'AN',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()
					WHERE NroTransaccion = '$NroTransaccion'";
			execute($sql);	
		}
		##	-----------------
		die('fin');
		mysql_query("COMMIT");
	}
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		##	-----------------
		list($NroTransaccion, $Secuencia) = explode('_', $registro);
		##	
		$sql = "SELECT * FROM ap_bancotransaccion WHERE NroTransaccion = '$NroTransaccion' AND Secuencia = '$Secuencia'";
		$field = getRecord($sql);
		if ($field['Estado'] != 'PR') die('No puede eliminar una transacci&oacute;n en Estado <strong>'.printValores('ESTADO-BANCARIO',$field['Estado']).'</strong>');
		elseif (($field['FlagGeneraVoucher'] == 'S' && trim($field['Voucher']))
				|| ($field['FlagGeneraVoucherPub20'] == 'S' && trim($field['VoucherPub20']))) die('No puede eliminar una transacci&oacute;n con Vouchers Generados');
		##	
		$sql = "DELETE FROM ap_bancotransaccion 
				WHERE 
					NroTransaccion = '$NroTransaccion' 
					AND Secuencia = '$Secuencia'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
}
elseif ($modulo == "validar") 
{
	//	modificar
	if($accion == "modificar") 
	{
		list($NroTransaccion, $Secuencia) = explode('_', $codigo);
		$sql = "SELECT Estado FROM ap_bancotransaccion WHERE NroTransaccion = '$NroTransaccion' AND Secuencia = '$Secuencia'";
		$Estado = getVar3($sql);
		if ($Estado != 'PR') die('No puede modificar una transacci&oacute;n en Estado <strong>'.printValores('ESTADO-BANCARIO',$Estado).'</strong>');
	}
	//	actualizar
	elseif($accion == "actualizar") 
	{
		list($NroTransaccion, $Secuencia) = explode('_', $codigo);
		$sql = "SELECT Estado FROM ap_bancotransaccion WHERE NroTransaccion = '$NroTransaccion' AND Secuencia = '$Secuencia'";
		$Estado = getVar3($sql);
		if ($Estado != 'PR') die('No puede Actualizar una transacci&oacute;n en Estado <strong>'.printValores('ESTADO-BANCARIO',$Estado).'</strong>');
	}
	//	desactualizar
	elseif($accion == "desactualizar") 
	{
		list($NroTransaccion, $Secuencia) = explode('_', $codigo);
		$sql = "SELECT * FROM ap_bancotransaccion WHERE NroTransaccion = '$NroTransaccion' AND Secuencia = '$Secuencia'";
		$field = getRecord($sql);
		if ($field['Estado'] != 'AP' && $field['Estado'] != 'CO') die('No puede Desactualizar una transacci&oacute;n en Estado <strong>'.printValores('ESTADO-BANCARIO',$field['Estado']).'</strong>');
		elseif ($field['FlagAutomatico'] == 'S') die('No puede Desactualizar una transacci&oacute;n Autom&aacute;tica del Sistema');
	}
	//	anular
	elseif($accion == "anular") 
	{
		list($NroTransaccion, $Secuencia) = explode('_', $codigo);
		$sql = "SELECT * FROM ap_bancotransaccion WHERE NroTransaccion = '$NroTransaccion' AND Secuencia = '$Secuencia'";
		$field = getRecord($sql);
		if ($field['Estado'] != 'PR') die('No puede Anular una transacci&oacute;n en Estado <strong>'.printValores('ESTADO-BANCARIO',$field['Estado']).'</strong>');
		elseif ($field['FlagGeneraVoucher'] == 'S' && trim($field['Voucher'])) die('No puede Anular una transacci&oacute;n Autom&aacute;tica del Sistema');
	}
	//	generar voucher
	elseif($accion == "generar-voucher") 
	{
		list($NroTransaccion, $Secuencia) = explode('_', $codigo);
		$sql = "SELECT * FROM ap_bancotransaccion WHERE NroTransaccion = '$NroTransaccion' AND Secuencia = '$Secuencia'";
		$field = getRecord($sql);
		if ($field['Estado'] != 'AP') die('No puede Generar Voucher a una transacci&oacute;n en Estado <strong>'.printValores('ESTADO-BANCARIO',$field['Estado']).'</strong>');
		elseif ($field['FlagGeneraVoucher'] != 'S') die('El Tipo de Transacci&oacute;n no genera Voucher Contable');
		elseif ($_PARAMETRO['CONTPUB20'] == 'S' && $field['FlagContPendientePub20'] != 'S') die('No puede generar un Voucher Contable para esta Transacci&oacute;n');
	}
}
elseif($modulo == "ajax") 
{
	//	insertar tipo de transacción
	if ($accion == "detalle_insertar") 
	{
		$sql = "SELECT * FROM ap_bancotipotransaccion WHERE CodTipoTransaccion = '$CodTipoTransaccion'";
		$field = getRecords($sql);
		foreach ($field as $f) {
			$id = $f['CodTipoTransaccion'];
			?>
			<tr class="trListaBody" id="detalle_<?=$id?>" onclick="clk($(this), 'detalle', 'detalle_<?=$id?>');">
				<th><?=$nro_detalles?></th>
				<td align="center" width="50">
					<input type="hidden" name="detalle_Secuencia[]" />
					<input type="hidden" name="detalle_CodTipoTransaccion[]" value="<?=$f['CodTipoTransaccion']?>" />
					<?=$f['CodTipoTransaccion']?>
				</td>
				<td><?=$f['Descripcion']?></td>
				<td><input type="text" name="detalle_TipoTransaccion[]" value="<?=$f['TipoTransaccion']?>" class="cell" style="text-align:center;" readonly /></td>
				<td>
					<select name="detalle_NroCuenta[]" class="cell">
		            	<option value="">&nbsp;</option>
		                <?=loadSelect2("ap_ctabancaria","NroCuenta","NroCuenta")?>
					</select>
				</td>
				<td><input type="text" name="detalle_Monto[]" value="0,00" class="cell currency" style="text-align:right;" /></td>
				<td>
					<select name="detalle_CodTipoDocumento[]" class="cell">
		            	<option value="">&nbsp;</option>
						<?=getMiscelaneos("","TIPOTRBANC")?>
					</select>
				</td>
				<td><input type="text" name="detalle_CodigoReferenciaBanco[]" class="cell" maxlength="20" /></td>
				<td><input type="text" name="detalle_CodProveedor[]" id="detalle_CodProveedor_<?=$id?>" class="cell" style="text-align:center;" onchange="getDescripcionLista2('accion=getDescripcionPersona', $(this));" /></td>
				<td>
					<input type="hidden" name="detalle_CodCentroCosto[]" id="detalle_CodCentroCosto_<?=$id?>" />
					<input type="text" name="detalle_NomCentroCosto[]" id="detalle_NomCentroCosto_<?=$id?>" class="cell" style="text-align:center;" onchange="getDescripcionLista2('accion=getCCosto', $(this), $('detalle_CodCentroCosto_<?=$id?>'));" />
				</td>
				<td><input type="text" name="detalle_CodPartida[]" id="detalle_CodPartida_<?=$id?>" class="cell presupuesto" style="text-align:center;" readonly /></td>
				<td>
					<input type="hidden" name="detalle_CodPresupuesto[]" id="detalle_CodPresupuesto_<?=$id?>" class="presupuesto" />
					<input type="text" name="detalle_CategoriaProg[]" id="detalle_CategoriaProg_<?=$id?>" class="cell presupuesto" style="text-align:center;" readonly />
				</td>
				<td><input type="text" name="detalle_CodFuente[]" id="detalle_CodFuente_<?=$id?>" class="cell presupuesto" style="text-align:center;" readonly /></td>
			</tr>
			<?php
			
		}
	}
}
?>