<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion-".".sql", "w+");
##############################################################################/
if ($modulo == "generar-vouchers")
{
	mysql_query("BEGIN");
	##	-----------------
	$Creditos = setNumero($Creditos);
	$Debitos = setNumero($Debitos);
	$FechaPreparacion = formatFechaAMD($FechaPreparacion);
	$FechaAprobacion = formatFechaAMD($FechaAprobacion);
	$FechaVoucher = formatFechaAMD($FechaVoucher);
	##	valido
	if (!trim($CodOrganismo) || !trim($FechaVoucher) || !trim($CodContabilidad) || !trim($CodLibroCont)) die("Debe llenar los campos (*) obligatorios.");
	##	codigo
	$NroVoucher = codigo('ac_vouchermast','NroVoucher',4,['CodOrganismo','Periodo','CodVoucher','CodContabilidad'],[$CodOrganismo,$Periodo,$CodVoucher,$CodContabilidad]);
	$NroInterno = codigo('ac_vouchermast','NroInterno',10);
	$Voucher = $CodVoucher . '-' . $NroVoucher;
	##	voucher
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
				AprobadoPor = '$AprobadoPor',
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
	for ($i=0; $i < count($detalle_Linea); $i++)
	{
		++$Linea;
		$detalle_MontoVoucher[$i] = setNumero($detalle_MontoVoucher[$i]);
		$detalle_FechaVoucher[$i] = formatFechaAMD($detalle_FechaVoucher[$i]);
		##	inserto
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
					NroCheque = '$detalle_NroCheque[$i]',
					NroPagoVoucher = '$detalle_NroPagoVoucher[$i]',
					Descripcion = '$detalle_FechaVoucher[$i]',
					Estado = 'MA',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()";
		execute($sql);
	}
	if ($accion == 'documento_pub20')
	{
		##	actualizo
		$sql = "UPDATE co_documento
				SET
					FlagContabilizacionPendientePub20 = 'N',
					VoucherPeriodo = '$Periodo',
					VoucherNro = '$Voucher',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodDocumento = '$CodDocumento'";
		execute($sql);
	}
	elseif ($accion == 'documento_oncop')
	{
		##	actualizo
		$sql = "UPDATE co_documento
				SET
					FlagContabilizacionPendiente = 'N',
					VoucherPeriodo = '$Periodo',
					VoucherNro = '$Voucher',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodDocumento = '$CodDocumento'";
		execute($sql);
	}
	elseif ($accion == 'cobranza_pub20')
	{
		##	actualizo
		$sql = "UPDATE co_arqueocaja
				SET
					FlagContabilizacionPendientePub20 = 'N',
					VoucherPeriodo = '$Periodo',
					VoucherNro = '$Voucher',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodArqueo = '$CodArqueo'";
		execute($sql);
	}
	elseif ($accion == 'cobranza_oncop')
	{
		##	actualizo
		$sql = "UPDATE co_arqueocaja
				SET
					FlagContabilizacionPendiente = 'N',
					VoucherPeriodo = '$Periodo',
					VoucherNro = '$Voucher',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodArqueo = '$CodArqueo'";
		execute($sql);
	}
	##	-----------------
	mysql_query("COMMIT");
}
elseif ($modulo == "ajax") {
	if ($accion == "cobranzas_detalle") {
		$i = 0;
		$sql = "SELECT
					ac.NroCuenta,
					ac.Fecha,
					b.Banco,
					cb.CodCuenta,
					cb.CodCuentaPub20,
					cod.MonedaDocumento,
					cod.MontoLocal,
					cod.ArqueoDocReferencia,
					tp.Descripcion AS TipoPago
				FROM co_cobranzadet cod
				INNER JOIN co_arqueocaja ac ON ac.CodArqueo = cod.CodArqueo
				INNER JOIN ap_ctabancaria cb ON cb.Nrocuenta = ac.NroCuenta
				INNER JOIN mastbancos b ON b.CodBanco = cb.CodBanco
				INNER JOIN co_tipopago tp ON tp.CodTipoPago = cod.CodTipoPago
				WHERE cod.CodArqueo = '$CodArqueo'";
		$field = getRecords($sql);
		foreach($field as $f) {
			?>
			<tr class="trListaBody">
				<th><?=++$i?></th>
				<td align="center"><?=$f['NroCuenta']?></td>
				<td><?=htmlentities($f['Banco'])?></td>
				<td align="center"><?=$f['CodCuentaPub20']?></td>
				<td align="center"><?=printValoresGeneral("monedas", $f['MonedaDocumento'])?></td>
				<td align="center"><?=$f['TipoPago']?></td>
				<td align="center"><?=formatFechaDMA($f['Fecha'])?></td>
				<td align="right"><?=number_format($f['MontoLocal'],2,',','.')?></td>
				<td align="center">&nbsp;</td>
				<td align="center"><?=$f['ArqueoDocReferencia']?></td>
			</tr>
			<?php
		}
	}
}
?>