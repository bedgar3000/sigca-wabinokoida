<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion-".$AnioActual.$MesActual.$DiaActual."_".$HorasActual.$MinutosActual.$SegundosActual.".sql", "w+");
##############################################################################/
##	CIERRE ANUAL
##############################################################################/
if ($modulo == "formulario") {
	//	nuevo
	if ($accion == "cierre") {
		mysql_query("BEGIN");
		##	-----------------
		$_CIERRE = array();
		$filtro = '';
		if ($fCodOrganismo != "") $filtro.=" AND (vd.CodOrganismo = '".$fCodOrganismo."')";
		if ($fCodContabilidad != "") $filtro.=" AND (vd.CodContabilidad = '".$fCodContabilidad."')";
		if ($fPeriodo != "") $filtro.=" AND (vd.Periodo LIKE '".$fPeriodo."-%')";
		$CodLibroCont = 'CO';
		$Descripcion23309 = getVar3("SELECT Descripcion FROM ac_mastplancuenta20 WHERE CodCuenta = '23309'");
		$Descripcion232990101 = getVar3("SELECT Descripcion FROM ac_mastplancuenta20 WHERE CodCuenta = '232990101'");
		##	VALIDO
		$sql = "SELECT *
				FROM ac_cierreanual
				WHERE
					CodOrganismo = '".$fCodOrganismo."' AND
					Periodo = '".$fPeriodo."' AND
					CodContabilidad = '".$fCodContabilidad."'";
		$field_cierre = getRecords($sql);
		if (count($field_cierre)) die('El Periodo seleccionado ya se encuentra cerrado.');
		##	ASIENTO 1
		if ($fCodContabilidad == 'F') {
			$sql = "SELECT
						pc.CodCuenta,
						pc.Descripcion,
						ABS(SUM(vd.MontoVoucher)) AS Monto
					FROM
						ac_voucherdet vd
						INNER JOIN ac_mastplancuenta20 pc ON (pc.CodCuenta = vd.CodCuenta)
					WHERE vd.Estado = 'MA' AND vd.CodCuenta = '51301' $filtro
					ORDER BY CodCuenta";
		}
		$field = getRecords($sql);
		$Monto51301 = 0;
		foreach($field as $f) {
			$TituloVoucher = "Para cancelar el saldo de la cuenta Ingresos, por transferencia a la cuenta No. 309-Ejecución del Presupuesto";
			$NroInterno = codigo('ac_vouchermast', 'NroInterno', 10);
			##	general
			$sql = "INSERT INTO ac_vouchermast
					SET
						CodOrganismo = '".$fCodOrganismo."',
						Periodo = '".$fPeriodo."-12',
						Voucher = '33-0001',
						CodContabilidad = '".$fCodContabilidad."',
						Prefijo = '33',
						NroVoucher = '0001',
						CodVoucher = '33',
						CodDependencia = '".$_PARAMETRO['DEPLOGCXP']."',
						Creditos = ".floatval($f['Monto']).",
						Debitos = ".floatval(-$f['Monto']).",
						Lineas = 2,
						PreparadoPor = '".$_SESSION['CODPERSONA_ACTUAL']."',
						FechaPreparacion = '".$FechaActual."',
						AprobadoPor = '".$_SESSION['CODPERSONA_ACTUAL']."',
						FechaAprobacion = '".$FechaActual."',
						TituloVoucher = '".utf8_decode($TituloVoucher)."',
						ComentariosVoucher = '".utf8_decode($TituloVoucher)."',
						FechaVoucher = '".$FechaActual."',
						NroInterno = '".$NroInterno."',
						Estado = 'MA',
						CodLibroCont = '".$CodLibroCont."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
			##	detalles
			$sql = "INSERT INTO ac_voucherdet
					SET
						CodOrganismo = '".$fCodOrganismo."',
						Periodo = '".$fPeriodo."-12',
						Voucher = '33-0001',
						Linea = 1,
						CodContabilidad = '".$fCodContabilidad."',
						CodCuenta = '".$f['CodCuenta']."',
						MontoVoucher = ".floatval($f['Monto']).",
						MontoPost = ".floatval($f['Monto']).",
						CodPersona = '".$CodPersona."',
						FechaVoucher = '".$FechaActual."',
						CodCentroCosto = '".$_PARAMETRO['CCOSTOVOUCHER']."',
						Descripcion = '".utf8_decode($f['Descripcion'])."',
						Estado = 'MA',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
			$sql = "INSERT INTO ac_voucherdet
					SET
						CodOrganismo = '".$fCodOrganismo."',
						Periodo = '".$fPeriodo."-12',
						Voucher = '33-0001',
						Linea = 2,
						CodContabilidad = '".$fCodContabilidad."',
						CodCuenta = '23309',
						MontoVoucher = ".floatval(-$f['Monto']).",
						MontoPost = ".floatval(-$f['Monto']).",
						CodPersona = '".$CodPersona."',
						FechaVoucher = '".$FechaActual."',
						CodCentroCosto = '".$_PARAMETRO['CCOSTOVOUCHER']."',
						Descripcion = '".utf8_decode($Descripcion23309)."',
						Estado = 'MA',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
			##	
			$Monto51301 += $f['Monto'];
			$_CIERRE[] = array(
				'CodOrganismo' => $fCodOrganismo, 
				'Periodo' => $fPeriodo, 
				'CodContabilidad' => $fCodContabilidad, 
				'Asiento' => 1, 
				'CodCuenta' => $f['CodCuenta'], 
				'Descripcion' => $f['Descripcion'], 
				'Monto' => floatval($f['Monto'])
			);
			$_CIERRE[] = array(
				'CodOrganismo' => $fCodOrganismo, 
				'Periodo' => $fPeriodo, 
				'CodContabilidad' => $fCodContabilidad, 
				'Asiento' => 1, 
				'CodCuenta' => '23309', 
				'Descripcion' => $Descripcion23309, 
				'Monto' => floatval(-$f['Monto'])
			);
		}
		##	ASIENTO 2
		if ($fCodContabilidad == 'F') {
			$sql = "SELECT
						pc.CodCuenta,
						pc.Descripcion,
						ABS(SUM(vd.MontoVoucher)) AS Monto
					FROM
						ac_voucherdet vd
						INNER JOIN ac_mastplancuenta20 pc ON (pc.CodCuenta = vd.CodCuenta)
					WHERE vd.Estado = 'MA' AND vd.CodCuenta = '51303' $filtro
					ORDER BY CodCuenta";
		}
		$field = getRecords($sql);
		$Monto51303 = 0;
		foreach($field as $f) {
			$TituloVoucher = "Para cancelar el saldo de la cuenta Gastos Presupuestarios, por transferencia a la cuenta No. 309-Ejecución del Presupuesto";
			$NroInterno = codigo('ac_vouchermast', 'NroInterno', 10);
			##	general
			$sql = "INSERT INTO ac_vouchermast
					SET
						CodOrganismo = '".$fCodOrganismo."',
						Periodo = '".$fPeriodo."-12',
						Voucher = '33-0002',
						CodContabilidad = '".$fCodContabilidad."',
						Prefijo = '33',
						NroVoucher = '0002',
						CodVoucher = '33',
						CodDependencia = '".$_PARAMETRO['DEPLOGCXP']."',
						Creditos = ".floatval($f['Monto']).",
						Debitos = ".floatval(-$f['Monto']).",
						Lineas = 2,
						PreparadoPor = '".$_SESSION['CODPERSONA_ACTUAL']."',
						FechaPreparacion = '".$FechaActual."',
						AprobadoPor = '".$_SESSION['CODPERSONA_ACTUAL']."',
						FechaAprobacion = '".$FechaActual."',
						TituloVoucher = '".utf8_decode($TituloVoucher)."',
						ComentariosVoucher = '".utf8_decode($TituloVoucher)."',
						FechaVoucher = '".$FechaActual."',
						NroInterno = '".$NroInterno."',
						Estado = 'MA',
						CodLibroCont = '".$CodLibroCont."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
			##	detalles
			$sql = "INSERT INTO ac_voucherdet
					SET
						CodOrganismo = '".$fCodOrganismo."',
						Periodo = '".$fPeriodo."-12',
						Voucher = '33-0002',
						Linea = 1,
						CodContabilidad = '".$fCodContabilidad."',
						CodCuenta = '".$f['CodCuenta']."',
						MontoVoucher = ".floatval($f['Monto']).",
						MontoPost = ".floatval($f['Monto']).",
						CodPersona = '".$CodPersona."',
						FechaVoucher = '".$FechaActual."',
						CodCentroCosto = '".$_PARAMETRO['CCOSTOVOUCHER']."',
						Descripcion = '".utf8_decode($f['Descripcion'])."',
						Estado = 'MA',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
			$sql = "INSERT INTO ac_voucherdet
					SET
						CodOrganismo = '".$fCodOrganismo."',
						Periodo = '".$fPeriodo."-12',
						Voucher = '33-0002',
						Linea = 2,
						CodContabilidad = '".$fCodContabilidad."',
						CodCuenta = '23309',
						MontoVoucher = ".floatval(-$f['Monto']).",
						MontoPost = ".floatval(-$f['Monto']).",
						CodPersona = '".$CodPersona."',
						FechaVoucher = '".$FechaActual."',
						CodCentroCosto = '".$_PARAMETRO['CCOSTOVOUCHER']."',
						Descripcion = '".utf8_decode($Descripcion23309)."',
						Estado = 'MA',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
			##	
			$Monto51303 += $f['Monto'];
			$_CIERRE[] = array(
				'CodOrganismo' => $fCodOrganismo, 
				'Periodo' => $fPeriodo, 
				'CodContabilidad' => $fCodContabilidad, 
				'Asiento' => 2, 
				'CodCuenta' => $f['CodCuenta'], 
				'Descripcion' => $f['Descripcion'], 
				'Monto' => floatval($f['Monto'])
			);
			$_CIERRE[] = array(
				'CodOrganismo' => $fCodOrganismo, 
				'Periodo' => $fPeriodo, 
				'CodContabilidad' => $fCodContabilidad, 
				'Asiento' => 2, 
				'CodCuenta' => '23309', 
				'Descripcion' => $Descripcion23309, 
				'Monto' => floatval(-$f['Monto'])
			);
		}
		##	ASIENTO 3
		if ($fCodContabilidad == 'F') {
			$sql = "SELECT ABS(SUM(vd.MontoVoucher)) AS Monto
					FROM ac_voucherdet vd
					WHERE vd.Estado = 'MA' AND vd.CodCuenta LIKE '61300%' $filtro";
		}
		$Monto = getVar3($sql);
		##	
		$TituloVoucher = "Para cancelar el saldo de la cuenta Ingresos Extraordinarios, por transferencia a la cuenta No. 309-Ejecución del Presupuesto";
		$NroInterno = codigo('ac_vouchermast', 'NroInterno', 10);
		##	general
		$sql = "INSERT INTO ac_vouchermast
				SET
					CodOrganismo = '".$fCodOrganismo."',
					Periodo = '".$fPeriodo."-12',
					Voucher = '33-0003',
					CodContabilidad = '".$fCodContabilidad."',
					Prefijo = '33',
					NroVoucher = '0003',
					CodVoucher = '33',
					CodDependencia = '".$_PARAMETRO['DEPLOGCXP']."',
					Creditos = ".floatval($Monto).",
					Debitos = ".floatval(-$f['Monto']).",
					Lineas = 2,
					PreparadoPor = '".$_SESSION['CODPERSONA_ACTUAL']."',
					FechaPreparacion = '".$FechaActual."',
					AprobadoPor = '".$_SESSION['CODPERSONA_ACTUAL']."',
					FechaAprobacion = '".$FechaActual."',
					TituloVoucher = '".utf8_decode($TituloVoucher)."',
					ComentariosVoucher = '".utf8_decode($TituloVoucher)."',
					FechaVoucher = '".$FechaActual."',
					NroInterno = '".$NroInterno."',
					Estado = 'MA',
					CodLibroCont = '".$CodLibroCont."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		##	detalles
		$sql = "INSERT INTO ac_voucherdet
				SET
					CodOrganismo = '".$fCodOrganismo."',
					Periodo = '".$fPeriodo."-12',
					Voucher = '33-0003',
					Linea = 1,
					CodContabilidad = '".$fCodContabilidad."',
					CodCuenta = '23309',
					MontoVoucher = ".floatval($Monto).",
					MontoPost = ".floatval($Monto).",
					CodPersona = '".$CodPersona."',
					FechaVoucher = '".$FechaActual."',
					CodCentroCosto = '".$_PARAMETRO['CCOSTOVOUCHER']."',
					Descripcion = '".utf8_decode($Descripcion23309)."',
					Estado = 'MA',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		if ($fCodContabilidad == 'F') {
			$sql = "SELECT
						pc.CodCuenta,
						pc.Descripcion,
						SUM(vd.MontoVoucher) AS Monto
					FROM
						ac_voucherdet vd
						INNER JOIN ac_mastplancuenta20 pc ON (pc.CodCuenta = vd.CodCuenta)
					WHERE vd.Estado = 'MA' AND vd.CodCuenta LIKE '61300%' $filtro
					GROUP BY CodCuenta
					ORDER BY CodCuenta";
		}
		$field = getRecords($sql);
		$_CIERRE[] = array(
			'CodOrganismo' => $fCodOrganismo, 
			'Periodo' => $fPeriodo, 
			'CodContabilidad' => $fCodContabilidad, 
			'Asiento' => 3, 
			'CodCuenta' => '23309', 
			'Descripcion' => $Descripcion23309, 
			'Monto' => floatval($Monto)
		);
		$Monto61300 = 0;
		$Linea = 1;
		foreach($field as $f) {
			$sql = "INSERT INTO ac_voucherdet
					SET
						CodOrganismo = '".$fCodOrganismo."',
						Periodo = '".$fPeriodo."-12',
						Voucher = '33-0003',
						Linea = ".++$Linea.",
						CodContabilidad = '".$fCodContabilidad."',
						CodCuenta = '".$f['CodCuenta']."',
						MontoVoucher = ".floatval(-$f['Monto']).",
						MontoPost = ".floatval(-$f['Monto']).",
						CodPersona = '".$CodPersona."',
						FechaVoucher = '".$FechaActual."',
						CodCentroCosto = '".$_PARAMETRO['CCOSTOVOUCHER']."',
						Descripcion = '".utf8_decode($f['Descripcion'])."',
						Estado = 'MA',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
			##	
			$Monto61300 += $f['Monto'];
			$_CIERRE[] = array(
				'CodOrganismo' => $fCodOrganismo, 
				'Periodo' => $fPeriodo, 
				'CodContabilidad' => $fCodContabilidad, 
				'Asiento' => 3, 
				'CodCuenta' => $f['CodCuenta'], 
				'Descripcion' => $f['Descripcion'], 
				'Monto' => floatval(-$f['Monto'])
			);
		}
		$sql = "UPDATE ac_vouchermast
				SET Lineas = ".intval($Linea)."
				WHERE
					CodOrganismo = '".$fCodOrganismo."' AND
					Periodo = '".$fPeriodo."-12' AND
					CodContabilidad = '".$fCodContabilidad."' AND
					Voucher = '33-0003'";
		execute($sql);
		##	ASIENTO 4
		$MontoDebe = $Monto51301 + $Monto51303 - $Monto61300;
		$MontoHaber = -$MontoDebe;
		##	
		$TituloVoucher = "Para cancelar la cuenta No. 309-Ejecución del Presupuesto";
		$NroInterno = codigo('ac_vouchermast', 'NroInterno', 10);
		##	general
		$sql = "INSERT INTO ac_vouchermast
				SET
					CodOrganismo = '".$fCodOrganismo."',
					Periodo = '".$fPeriodo."-12',
					Voucher = '33-0004',
					CodContabilidad = '".$fCodContabilidad."',
					Prefijo = '33',
					NroVoucher = '0004',
					CodVoucher = '33',
					CodDependencia = '".$_PARAMETRO['DEPLOGCXP']."',
					Creditos = ".floatval($MontoDebe).",
					Debitos = ".floatval($MontoHaber).",
					Lineas = 2,
					PreparadoPor = '".$_SESSION['CODPERSONA_ACTUAL']."',
					FechaPreparacion = '".$FechaActual."',
					AprobadoPor = '".$_SESSION['CODPERSONA_ACTUAL']."',
					FechaAprobacion = '".$FechaActual."',
					TituloVoucher = '".utf8_decode($TituloVoucher)."',
					ComentariosVoucher = '".utf8_decode($TituloVoucher)."',
					FechaVoucher = '".$FechaActual."',
					NroInterno = '".$NroInterno."',
					Estado = 'MA',
					CodLibroCont = '".$CodLibroCont."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		##	detalles
		$sql = "INSERT INTO ac_voucherdet
				SET
					CodOrganismo = '".$fCodOrganismo."',
					Periodo = '".$fPeriodo."-12',
					Voucher = '33-0004',
					Linea = 1,
					CodContabilidad = '".$fCodContabilidad."',
					CodCuenta = '23309',
					MontoVoucher = ".floatval($MontoDebe).",
					MontoPost = ".floatval($MontoDebe).",
					CodPersona = '".$CodPersona."',
					FechaVoucher = '".$FechaActual."',
					CodCentroCosto = '".$_PARAMETRO['CCOSTOVOUCHER']."',
					Descripcion = '".utf8_decode($Descripcion23309)."',
					Estado = 'MA',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		$sql = "INSERT INTO ac_voucherdet
				SET
					CodOrganismo = '".$fCodOrganismo."',
					Periodo = '".$fPeriodo."-12',
					Voucher = '33-0004',
					Linea = 2,
					CodContabilidad = '".$fCodContabilidad."',
					CodCuenta = '232990101',
					MontoVoucher = ".floatval($MontoHaber).",
					MontoPost = ".floatval($MontoHaber).",
					CodPersona = '".$CodPersona."',
					FechaVoucher = '".$FechaActual."',
					CodCentroCosto = '".$_PARAMETRO['CCOSTOVOUCHER']."',
					Descripcion = '".utf8_decode($Descripcion232990101)."',
					Estado = 'MA',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		$_CIERRE[] = array(
			'CodOrganismo' => $fCodOrganismo, 
			'Periodo' => $fPeriodo, 
			'CodContabilidad' => $fCodContabilidad, 
			'Asiento' => 4, 
			'CodCuenta' => '23309', 
			'Descripcion' => $Descripcion23309, 
			'Monto' => floatval($MontoDebe)
		);
		$_CIERRE[] = array(
			'CodOrganismo' => $fCodOrganismo, 
			'Periodo' => $fPeriodo, 
			'CodContabilidad' => $fCodContabilidad, 
			'Asiento' => 4, 
			'CodCuenta' => '232990101', 
			'Descripcion' => $Descripcion232990101,
			'Monto' => floatval($MontoHaber)
		);
		##	INSERTO EL CIERRE
		foreach ($_CIERRE as $c) {
			$sql = "INSERT INTO ac_cierreanual
					SET
						CodOrganismo = '".$c['CodOrganismo']."',
						Periodo = '".$c['Periodo']."',
						CodContabilidad = '".$c['CodContabilidad']."',
						Asiento = '".$c['Asiento']."',
						CodCuenta = '".$c['CodCuenta']."',
						Descripcion = '".$c['Descripcion']."',
						Monto = ".floatval($c['Monto']).",
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	INSERTO SALDOS INICIALES
		$sql = "SELECT
					bc.*,
					(bc.SaldoInicial + bc.SaldoBalance01 + bc.SaldoBalance02 + bc.SaldoBalance03 + bc.SaldoBalance04 + bc.SaldoBalance05 + bc.SaldoBalance06 + bc.SaldoBalance07 + bc.SaldoBalance08 + bc.SaldoBalance09 + bc.SaldoBalance10 + bc.SaldoBalance11 + bc.SaldoBalance12) AS SaldoInicial
				FROM ac_balancecuenta bc
				WHERE
					CodContabilidad = 'F' AND
					Anio = '".$fPeriodo."' AND
					(bc.SaldoInicial + bc.SaldoBalance01 + bc.SaldoBalance02 + bc.SaldoBalance03 + bc.SaldoBalance04 + bc.SaldoBalance05 + bc.SaldoBalance06 + bc.SaldoBalance07 + bc.SaldoBalance08 + bc.SaldoBalance09 + bc.SaldoBalance10 + bc.SaldoBalance11 + bc.SaldoBalance12) <> 0";
		$field_saldos = getRecords($sql);
		foreach ($field_saldos as $fs) {
			$sql = "UPDATE ac_balancecuenta
					SET
						SaldoFinal = '".$fs['SaldoInicial']."'
					WHERE
						CodOrganismo = '".$fs['CodOrganismo']."' AND
						Anio = '".$fPeriodo."' AND
						CodContabilidad = '".$fs['CodContabilidad']."' AND
						CodCuenta = '".$fs['CodCuenta']."';";
			execute($sql);
			##	
			$sql = "INSERT INTO ac_balancecuenta
					SET
						CodOrganismo = '".$fs['CodOrganismo']."',
						Anio = '".($fPeriodo+1)."',
						CodContabilidad = '".$fs['CodContabilidad']."',
						CodCuenta = '".$fs['CodCuenta']."',
						SaldoInicial = '".$fs['SaldoInicial']."'
					ON DUPLICATE KEY UPDATE
						SaldoInicial = '".$fs['SaldoInicial']."';";
			execute($sql);
		}
		##	-----------------
		echo "|Ha generado con éxito los Vouchers:<br />
				<strong>
					33-0001.<br />
					33-0002.<br />
					33-0003.<br />
					33-0004.<br />
				</strong>
				Para el periodo <strong>".$fPeriodo."-12</strong>.";
		mysql_query("COMMIT");
	}
}
?>