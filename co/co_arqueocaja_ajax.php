<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion-".".sql", "w+");
##############################################################################/
if ($modulo == "formulario") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		##	-----------------
		$Fecha = formatFechaAMD($Fecha);
		##	valido
		if (!trim($CodOrganismo) || !trim($NroCuenta) || !trim($DocReferencia) || !trim($Fecha)) die("Debe llenar los campos (*) obligatorios.");
		else if (!count($detalle_CodCobranza)) die("Debe seleccionar las cobranzas por asignarle Cuenta Bancaria");
		##	codigo
		$CodArqueo = codigo('co_arqueocaja','CodArqueo',10);
		$NroArqueo = codigo('co_arqueocaja','NroArqueo',10,['CodOrganismo'],[$CodOrganismo]);
		$NroTransaccionCxP = intval(codigo('co_arqueocaja','NroTransaccionCxP',30));
		##	inserto
		$sql = "INSERT INTO co_arqueocaja
				SET
					CodArqueo = '$CodArqueo',
					CodOrganismo = '$CodOrganismo',
					NroArqueo = '$NroArqueo',
					Fecha = '$Fecha',
					NroTransaccionCxP = '$NroTransaccionCxP',
					NroCuenta = '$NroCuenta',
					DocReferencia = '$DocReferencia',
					VoucherPeriodo = '$VoucherPeriodo',
					PreparadoPor = '$PreparadoPor',
					FechaPreparado = '$FechaPreparado',
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()";
		execute($sql);
		##	detalle
		for ($i=0; $i < count($detalle_CodCobranza); $i++)
		{
			##	actualizar
			$sql = "UPDATE co_cobranzadet
					SET
						CodArqueo = '$CodArqueo',
						ArqueoNro = '$NroArqueo',
						CtaBancariaPropia = '$NroCuenta',
						ArqueoDocReferencia = '$DocReferencia'
					WHERE
						CodCobranza = '$detalle_CodCobranza[$i]'
						AND Secuencia = '$detalle_Secuencia[$i]'";
			execute($sql);
		}
		##	-----------------
		mysql_query("COMMIT");
	}
	//	aprobar
	elseif ($accion == "aprobar") {
		mysql_query("BEGIN");
		##	-----------------
		##	
		$sql = "SELECT * FROM co_arqueocaja WHERE CodArqueo = '$CodArqueo'";
		$field_arqueo = getRecord($sql);
		##	
		$sql = "SELECT * FROM ap_bancotipotransaccion WHERE CodTipoTransaccion = '$_PARAMETRO[COICXARQ]'";
		$field_tipo = getRecord($sql);
		##	actualizo
		$sql = "UPDATE co_arqueocaja
				SET
					AprobadoPor = '$AprobadoPor',
					FechaAprobado = '$FechaAprobado',
					Estado = 'AP',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodArqueo = '$CodArqueo'";
		execute($sql);
		##	
		$sql = "SELECT
					cbd.*,
					do.CodTipoDocumento,
					do.NroDocumento,
					cbd.CtaBancariaPropia AS NroCuentaPropia,
					cb.NroCobranza,
					cb.FechaCobranza,
					cb.CodOrganismo,
					bco.Banco,
					tp.Descripcion AS TipoPago
				FROM co_cobranzadet cbd
				INNER JOIN co_cobranza cb ON cb.CodCobranza = cbd.CodCobranza
				INNER JOIN co_documentocobranza doco ON doco.CodCobranza = cb.CodCobranza
				INNER JOIN co_documento do ON do.CodDocumento = doco.CodDocumento
				LEFT JOIN mastbancos bco ON bco.CodBanco = cbd.CodBanco
				LEFT JOIN co_tipopago tp On tp.CodTipoPago = cbd.CodTipoPago
				WHERE cbd.CodArqueo = '$CodArqueo'
				ORDER BY CtaBancariaPropia, CodTipoPago, FechaCobranza, Secuencia";
		$field_detalle = getRecords($sql);
		$Secuencia = 0;
		foreach ($field_detalle as $f)
		{
			/*++$Secuencia;
			$NroTransaccion = codigo('ap_bancotransaccion','NroTransaccion',5);
			##	
			$sql = "INSERT INTO ap_bancotransaccion
					SET
						NroTransaccion = '$NroTransaccion',
						Secuencia = '$Secuencia',
						CodOrganismo = '$f[CodOrganismo]',
						CodTipoTransaccion = '$field_tipo[CodTipoTransaccion]',
						TipoTransaccion = '$field_tipo[TipoTransaccion]',
						NroCuenta = '$f[CtaBancariaPropia]',
						PreparadoPor = '$AprobadoPor',
						FechaPreparacion = '$FechaAprobado',
						FechaTransaccion = '$FechaAprobado',
						PeriodoContable = '$FechaAprobado',
						Monto = '$f[MontoLocal]',
						FlagGeneraVoucher = '$field_tipo[FlagVoucher]',
						FlagGeneraVoucherPub20 = '$field_tipo[FlagVoucher]',
						CodigoReferenciaBanco = '$f[DocReferencia]',
						CodigoReferenciaInterno = '$f[CodTipoDocumento]-$f[NroDocumento]',
						Comentarios = 'Ingreso x Arqueo de Caja $field_arqueo[NroArqueo]',
						FlagPresupuesto = 'N',
						FlagAutomatico = 'S',
						Estado = 'CO',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()";
			execute($sql);
			##	
			$sql = "UPDATE co_cobranzadet
					SET
						Estado = 'CO',
						CodArqueo = '$field_arqueo[CodArqueo]',
						ArqueoNro = '$field_arqueo[NroArqueo]'
					WHERE CodCobranza = '$f[CodCobranza]'";
			execute($sql);*/
		}
		##	-----------------
		mysql_query("COMMIT");
	}
	//	anular
	elseif ($accion == "anular") {
		mysql_query("BEGIN");
		##	-----------------
		$field = getRecord("SELECT * FROM co_arqueocaja WHERE CodArqueo = '$CodArqueo'");
		##	
		if ($field['Estado'] != 'PR') die('No puede anular un arqueo <strong>'.printValores('arqueo-caja-estado',$field['Estado']).'</strong>');
		##	actualizo
		$sql = "UPDATE co_arqueocaja
				SET
					Estado = 'AN',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodArqueo = '$CodArqueo'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
}
elseif ($modulo == "validar") {
	//	modificar
	if($accion == "modificar") {
		$sql = "SELECT Estado FROM co_arqueocaja WHERE CodArqueo = '$codigo'";
		$Estado = getVar3($sql);
		if ($Estado != 'PR') die('No puede modificar un arqueo <strong>'.printValores('arqueo-caja-estado',$Estado).'</strong>');
	}
	//	aprobar
	elseif($accion == "aprobar") {
		$sql = "SELECT Estado FROM co_arqueocaja WHERE CodArqueo = '$codigo'";
		$Estado = getVar3($sql);
		if ($Estado != 'PR') die('No puede aprobar un arqueo <strong>'.printValores('arqueo-caja-estado',$Estado).'</strong>');
	}
	//	anular
	elseif($accion == "anular") {
		$sql = "SELECT Estado FROM co_arqueocaja WHERE CodArqueo = '$codigo'";
		$Estado = getVar3($sql);
		if ($Estado != 'PR') die('No puede anular un arqueo <strong>'.printValores('arqueo-caja-estado',$Estado).'</strong>');
	}
}
elseif ($modulo == "ajax") {
	if ($accion == "detalle_filtrar") {
		$filtro = '';
		if ($fCodOrganismo) $filtro .= " AND cb.CodOrganismo = '$fCodOrganismo'";
		if ($fFechaCobranzaD) $filtro .= " AND cb.FechaCobranza >= '".formatFechaAMD($fFechaCobranzaD)."'";
		if ($fFechaCobranzaH) $filtro .= " AND cb.FechaCobranza <= '".formatFechaAMD($fFechaCobranzaH)."'";
		if ($fCodPersonaCajero) $filtro .= " AND cb.CodPersonaCajero = '$fCodPersonaCajero'";
		if ($fCodPersonaCobrador) $filtro .= " AND cb.CodPersonaCobrador = '$fCodPersonaCobrador'";

		$nro_detalle = 0;
		$Grupo1 = '';
		$Grupo2 = '';
		$sql = "SELECT
					cbd.*,
					(CASE WHEN cbd.CtaBancariaPropia THEN cbd.CtaBancariaPropia ELSE 'LOCAL' END) AS NroCuentaPropia,
					cb.NroCobranza,
					cb.FechaCobranza,
					bco.Banco,
					tp.Descripcion AS TipoPago
				FROM co_cobranzadet cbd
				INNER JOIN co_cobranza cb ON cb.CodCobranza = cbd.CodCobranza
				LEFT JOIN mastbancos bco ON bco.CodBanco = cbd.CodBanco
				LEFT JOIN co_tipopago tp On tp.CodTipoPago = cbd.CodTipoPago
				WHERE 1 $filtro
				ORDER BY CtaBancariaPropia, CodTipoPago, FechaCobranza, Secuencia";
		$field_detalle = getRecords($sql);
		foreach ($field_detalle as $f)
		{
			$id = ++$nro_detalle;
			##	
			if ($Grupo1 != $f['NroCuentaPropia'])
			{
				?>
				<tr class="trListaBody2">
					<td colspan="3">
						<?=htmlentities($f['NroCuentaPropia'])?>
					</td>
					<td>
						<input type="text" name="detalle_TotalCuenta[]" id="CTA_<?=$f['NroCuentaPropia']?>" value="0,00" class="cell2 CTA_<?=$f['NroCuentaPropia']?>" data-cta="<?=$f['NroCuentaPropia']?>" style="text-align:right; font-weight: bold;" readonly>
					</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<?php
				$Grupo1 = $f['NroCuentaPropia'];
				$Grupo2 = '';

			}
			if ($Grupo2 != $f['CodTipoPago'])
			{
				?>
				<tr class="trListaBody3">
					<td colspan="3">
						<?=htmlentities($f['TipoPago'])?>
					</td>
					<td>
						<input type="text" name="detalle_TotalTipoPago[]" id="TP_<?=$f['CodTipoPago']?>" value="0,00" class="cell2 CTA_<?=$f['NroCuentaPropia']?> TP_<?=$f['CodTipoPago']?>" data-tp="<?=$f['CodTipoPago']?>" style="text-align:right; font-weight: bold;" readonly>
					</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<?php
				$Grupo2 = $f['CodTipoPago'];
			}
			?>
			<tr class="trListaBody" onclick="clkMulti($(this), 'detalle_<?=$id?>'); setMontosArqueo();">
				<td align="center">
					<input type="checkbox" name="detalle[]" id="detalle_<?=$id?>" value="<?=$id?>" style="display:none;" data-cta="<?=$f['NroCuentaPropia']?>" data-tp="<?=$f['CodTipoPago']?>" />
					<input type="hidden" name="detalle_CodCobranza[]" value="<?=$f['CodCobranza']?>" disabled>
					<input type="hidden" name="detalle_Secuencia[]" value="<?=$f['Secuencia']?>" disabled>
					<input type="hidden" name="detalle_MontoLocal[]" value="<?=$f['MontoLocal']?>" class="CTA_<?=$f['NroCuentaPropia']?> TP_<?=$f['CodTipoPago']?>">
					<?=formatFechaDMA($f['FechaCobranza'])?>
				</td>
				<td align="center"><?=$f['NroCobranza']?></td>
				<td align="center"><?=printValoresGeneral("monedas", $f['MonedaDocumento'])?></td>
				<td align="right"><?=number_format($f['MontoLocal'],2,',','.')?></td>
				<td align="center"><?=$f['ArqueoNro']?></td>
				<td align="center"><?=$f['ArqueoDocReferencia']?></td>
				<td align="center"><?=$f['CtaBancaria']?></td>
				<td><?=$f['Banco']?></td>
			</tr>
			<?php
		}
	}
}
?>