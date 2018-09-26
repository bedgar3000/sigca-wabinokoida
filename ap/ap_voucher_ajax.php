<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion-".$AnioActual.$MesActual.$DiaActual."_".$HorasActual.$MinutosActual.$SegundosActual.".sql", "w+");
//	$__archivo = fopen("_"."$modulo-$accion".".sql", "w+");
##############################################################################/
if ($modulo == "formulario") 
{
	//	nuevo
	if ($accion == "ap_bancotransaccion") 
	{
		mysql_query("BEGIN");
		##	-----------------
		$FechaVoucher = formatFechaAMD($FechaVoucher);
		$FechaPreparacion = formatFechaAMD($FechaPreparacion);
		$FechaAprobacion = formatFechaAMD($FechaAprobacion);
		$Creditos = setNumero($Creditos);
		$Debitos = setNumero($Debitos);
		##	Periodo Contable
		$sql = "SELECT Estado
				FROM ac_controlcierremensual
				WHERE
					TipoRegistro = 'AB' AND
					CodOrganismo = '$CodOrganismo' AND
					Periodo = '$Periodo'";
		$EstadoPeriodo = getVar3($sql);
		##	valido
		if (!trim($ComentariosVoucher)) die("Debe llenar los campos (*) obligatorios.");
		elseif (($Creditos + $Debitos) != 0) die("Monto de cr&eacute;ditos y d&eacute;bitos deben ser igual.");
		elseif ($Creditos == 0 || $Debitos == 0) die("Monto de cr&eacute;ditos y d&eacute;bitos no puede ser cero.");
		elseif ($EstadoPeriodo == '') die("El Periodo <strong>$Periodo</strong> no se ha creado.");
		elseif ($EstadoPeriodo == 'C') die("El Periodo <strong>$Periodo</strong> est&aacute; cerrado.");
		//	else NroCheque = '".$CodTipoPago."-".$NroPago."',
		##	codigo
		$NroVoucher = codigo('ac_vouchermast','NroVoucher',4,['CodOrganismo','Periodo','CodVoucher','CodContabilidad'],[$CodOrganismo,$Periodo,$CodVoucher,$CodContabilidad]);
		$NroInterno = codigo('ac_vouchermast','NroInterno',10);
		$Voucher = $CodVoucher . '-' . $NroVoucher;
		##	transaccion bancaria
		if ($accion == "ap_bancotransaccion") 
		{
			$NroCheque = '';
			##	
			if ($CodContabilidad = 'F')
			{
				$iVoucher = 'VoucherPub20';
				$iVoucherPeriodo = 'VoucherPeriodoPub20';
				$iFlagContabilizacionPendiente = 'FlagContPendientePub20';
			}
			else
			{
				$iVoucher = 'Voucher';
				$iVoucherPeriodo = 'VoucherPeriodo';
				$iFlagContabilizacionPendiente = 'FlagContabilizacionPendiente';
			}
			$sql = "UPDATE ap_bancotransaccion
					SET
						$iVoucher = '$Voucher',
						$iVoucherPeriodo = '$Periodo',
						$iFlagContabilizacionPendiente = 'N',
						Estado = 'CO'
					WHERE NroTransaccion = '$NroTransaccion'";
			execute($sql);
		}
		##	inserto
		$sql = "INSERT INTO ac_vouchermast
				SET
					CodOrganismo = '$CodOrganismo',
					Periodo = '$Periodo',
					Voucher = '$Voucher',
					CodContabilidad = '$CodContabilidad',
					Prefijo = '$CodVoucher',
					NroVoucher = '$NroVoucher',
					CodVoucher = '$CodVoucher',
					CodDependencia = '$CodDependencia',
					CodSistemaFuente = '$CodSistemaFuente',
					Creditos = '$Creditos',
					Debitos = '$Debitos',
					Lineas = '$Lineas',
					PreparadoPor = '$PreparadoPor',
					FechaPreparacion = '$FechaPreparacion',
					AprobadoPor = '$_SESSION[CODPERSONA_ACTUAL]',
					FechaAprobacion = '$FechaAprobacion',
					TituloVoucher = '$ComentariosVoucher',
					ComentariosVoucher = '$ComentariosVoucher',
					FechaVoucher = '$FechaVoucher',
					NroInterno = '$NroInterno',
					FlagTransferencia = 'N',
					Estado = 'MA',
					CodLibroCont = '$CodLibroCont',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()";
		execute($sql);
		##	detalle
		$Linea = 0;
		for ($i=0; $i < count($detalle_CodCuenta); $i++) 
		{
			++$Linea;
			$detalle_MontoVoucher[$i] = setNumero($detalle_MontoVoucher[$i]);
			$detalle_FechaVoucher[$i] = formatFechaAMD($detalle_FechaVoucher[$i]);
			##	
			$sql = "INSERT INTO ac_voucherdet
					SET
						CodOrganismo = '$CodOrganismo',
						Periodo = '$Periodo',
						Voucher = '$Voucher',
						CodContabilidad = '$CodContabilidad',
						Linea = '$Linea',
						CodCuenta = '$detalle_CodCuenta[$i]',
						MontoVoucher = '$detalle_MontoVoucher[$i]',
						MontoPost = '$detalle_MontoVoucher[$i]',
						CodPersona = '$detalle_CodPersona[$i]',
						FechaVoucher = '$detalle_FechaVoucher[$i]',
						CodCentroCosto = '$detalle_CodCentroCosto[$i]',
						ReferenciaTipoDocumento = '$detalle_ReferenciaTipoDocumento[$i]',
						ReferenciaNroDocumento = '$detalle_ReferenciaNroDocumento[$i]',
						NroCheque = '$NroCheque',
						Descripcion = '$detalle_Descripcion[$i]',
						Estado = 'MA',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	-----------------
		mysql_query("COMMIT");
	}
}
?>