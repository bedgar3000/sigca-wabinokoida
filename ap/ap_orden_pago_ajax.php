<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion-".$AnioActual.$MesActual.$DiaActual."_".$HorasActual.$MinutosActual.$SegundosActual.".sql", "w+");
//	$__archivo = fopen("_"."$modulo-$accion".".sql", "w+");
##############################################################################/
##	Orden de Pago (PAGOS PARCIALES)
##############################################################################/
if ($modulo == "formulario") {
	//	pago parcial
	if ($accion == "pago_parcial") {
		mysql_query("BEGIN");
		##	-----------------
		##	datos de la orden
		$sql = "SELECT
					op.*,
					cbb.SaldoActual
				FROM
					ap_ordenpago op
					INNER JOIN ap_ctabancariabalance cbb ON (cbb.NroCuenta = op.NroCuenta)
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					NroOrden = '".$NroOrden."'";
		$field_orden = getRecord($sql);
		##	valido
		if (!trim($Porcentaje) || !trim($Monto) || !trim($FechaPago)) die("Debe llenar los campos (*) obligatorios.");
		elseif (!validateDate($FechaPago,'d-m-Y')) die("Fecha de Pago Incorrecta.");
		elseif (is_nan(setNumero($Porcentaje)) || is_nan(setNumero($Monto)) || !setNumero($Porcentaje) || !setNumero($Monto)) die("Monto a Pagar Incorrecto.");
		elseif (setNumero($Monto) > $field_orden['SaldoActual']) die("Saldo en Banco Insuficiente.");
		##	
		$NroProceso = getCodigo("ap_pagos", "NroProceso", 6);
		##	
		$sql = "UPDATE ap_ordenpago
				SET
					NroProceso = '".$NroProceso."',
					Secuencia = '1',
					FlagPagoParcial = 'S',
					Estado = 'PG',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					NroOrden = '".$NroOrden."'";
		execute($sql);
		##	
		$sql = "INSERT INTO ap_pagos
				SET
					NroProceso = '".$NroProceso."',
					Secuencia = '1',
					CodTipoPago = '".$CodTipoPago."',
					CodOrganismo = '".$CodOrganismo."',
					NroCuenta = '".$NroCuenta."',
					CodProveedor = '".$field_orden['CodProveedor']."',
					NroOrden = '".$NroOrden."',
					Anio = '".$Anio."',
					NomProveedorPagar = '".$field_orden['NomProveedorPagar']."',
					MontoPago = '".setNumero($NetoPagar)."',
					MontoRetenido = '".setNumero($MontoImpuestoOtros)."',
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
					Periodo = '".substr(formatFechaAMD($FechaPago), 0, 7)."',
					GeneradoPor = '".$_SESSION["CODPERSONA_ACTUAL"]."',
					ConformadoPor = '".$field_orden['RevisadoPor']."',
					AprobadoPor = '".$field_orden['AprobadoPor']."',
					RevisadoPor = '".$_PARAMETRO['FIRMAOP3']."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		##	inserto
		$Secuencia = codigo("ap_pagosparciales", "Secuencia", 3, ['Anio','CodOrganismo','NroOrden'],[$Anio,$CodOrganismo,$NroOrden]);
		$MontoPendiente = setNumero($SaldoPendiente) - setNumero($NetoPagar);
		$MontoOrden = $field_orden['MontoTotal'] - setNumero($MontoImpuesto) + abs(setNumero($MontoImpuestoOtros));
		##	
		$sql = "INSERT INTO ap_pagosparciales
				SET
					Anio = '".$Anio."',
					CodOrganismo = '".$CodOrganismo."',
					NroOrden = '".$NroOrden."',
					Secuencia = '".$Secuencia."',
					PeriodoPago = '".substr(formatFechaAMD($FechaPago), 0, 7)."',
					MontoOrden = '".$MontoOrden."',
					MontoIva = '".setNumero($MontoImpuesto)."',
					MontoNeto = '".$field_orden['MontoTotal']."',
					MontoRetenciones = '".setNumero($MontoImpuestoOtros)."',
					SaldoPendiente = '".setNumero($SaldoPendiente)."',
					MontoPagar = '".setNumero($NetoPagar)."',
					MontoPagado = '".setNumero($MontoPagoParcial)."',
					MontoPendiente = '".$MontoPendiente."',
					Porcentaje = '".setNumero($Porcentaje)."',
					Estado = 'PE',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		##	Partidas
		$Secuencia = 0;
		$MontoPartidas = 0;
		for ($i=0; $i < count($partidas_cod_partida); $i++) {
			##	valido
			if (is_nan(setNumero($partidas_Monto[$i])) || !(setNumero($partidas_Monto[$i]))) die("Monto Partida Incorrecto");
			##	
			if ($FlagPrimerPago == 'S') {
				$sql = "UPDATE ap_ordenpagodistribucion
						SET
							MontoPagado = '".setNumero($partidas_Monto[$i])."',
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()
						WHERE
							Anio = '".$Anio."' AND
							CodOrganismo = '".$CodOrganismo."' AND
							NroOrden = '".$NroOrden."' AND
							CodPresupuesto = '".$partidas_CodPresupuesto[$i]."' AND
							CodFuente = '".$partidas_CodFuente[$i]."' AND
							cod_partida = '".$partidas_cod_partida[$i]."'";
			} else {
				$sql = "SELECT *
						FROM ap_ordenpagodistribucion
						WHERE
							Anio = '".$Anio."' AND
							CodOrganismo = '".$CodOrganismo."' AND
							NroOrden = '".$NroOrden."' AND
							CodPresupuesto = '".$partidas_CodPresupuesto[$i]."' AND
							CodFuente = '".$partidas_CodFuente[$i]."' AND
							cod_partida = '".$partidas_cod_partida[$i]."'
						ORDER BY Linea DESC
						LIMIT 0, 1";
				$field_dist = getRecord($sql);
				##	
				$Linea = intval(codigo('ap_ordenpagodistribucion','Linea',5,['Anio','CodOrganismo','NroOrden'],[$Anio,$CodOrganismo,$NroOrden]));
				##	
				$sql = "INSERT INTO ap_ordenpagodistribucion
						SET
							CodOrganismo = '".$CodOrganismo."',
							NroOrden = '".$NroOrden."',
							CodProveedor = '".$field_orden['CodProveedor']."',
							CodTipoDocumento = '".$field_orden['CodTipoDocumento']."',
							NroDocumento = '".$field_orden['NroDocumento']."',
							Linea = '".$Linea."',
							CodCentroCosto = '".$field_dist['CodCentroCosto']."',
							Monto = '0.00',
							MontoPagado = '".setNumero($partidas_Monto[$i])."',
							CodCuenta = '".$CodCuenta."',
							CodCuentaPub20 = '".$field_dist['CodCuentaPub20']."',
							cod_partida = '".$partidas_cod_partida[$i]."',
							Anio = '".$Anio."',
							Periodo = '".substr(formatFechaAMD($FechaPago),0,7)."',
							CodPresupuesto = '".$partidas_CodPresupuesto[$i]."',
							CodFuente = '".$partidas_CodFuente[$i]."',
							Ejercicio = '".$Anio."',
							Origen = 'OP',
							Estado = 'PE',
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()";
			}
			execute($sql);
			##	
			$MontoPartidas += setNumero($partidas_Monto[$i]);
		}
		$NetoPagar = round((floatval(setNumero($NetoPagar))),2);
		$MontoPartidas = round((floatval($MontoPartidas)),2);
		$MontoImpuestoOtros = round((floatval(setNumero($MontoImpuestoOtros))),2);

		$Neto = ($MontoPartidas - abs($MontoImpuestoOtros));
		$Neto = round((floatval($NetoPagar)),2);

		if ($NetoPagar <> $Neto) die("Error en la Distribuci&oacute;n de los Montos en las Partidas. ($NetoPagar <> $Neto)");
		##	-----------------
		mysql_query("COMMIT");
	}
}
/*elseif ($modulo == "validar") {
	//	modificar
	if($accion == "modificar") {
		list($CodOrganismo, $CodAjuste) = explode('_', $codigo);
		$sql = "SELECT Estado FROM pv_ajustes WHERE CodOrganismo = '$CodOrganismo' AND CodAjuste = '$CodAjuste'";
		$Estado = getVar3($sql);
		if ($Estado != 'PR') die('No puede modificar un ajuste <strong>'.printValores('ajustes-estado',$Estado).'</strong>');
	}
	//	aprobar
	elseif($accion == "aprobar") {
		list($CodOrganismo, $CodAjuste) = explode('_', $codigo);
		$sql = "SELECT Estado FROM pv_ajustes WHERE CodOrganismo = '$CodOrganismo' AND CodAjuste = '$CodAjuste'";
		$Estado = getVar3($sql);
		if ($Estado != 'PR') die('No puede aprobar un ajuste <strong>'.printValores('ajustes-estado',$Estado).'</strong>');
	}
	//	anular
	elseif($accion == "anular") {
		list($CodOrganismo, $CodAjuste) = explode('_', $codigo);
		$sql = "SELECT Estado FROM pv_ajustes WHERE CodOrganismo = '$CodOrganismo' AND CodAjuste = '$CodAjuste'";
		$Estado = getVar3($sql);
		if ($Estado == 'AN') die('No puede anular un ajuste <strong>'.printValores('ajustes-estado',$Estado).'</strong>');
	}
}
elseif ($modulo == "ajax") {
	//	insertar linea
	if($accion == "partida_insertar") {
		##	detalle
		$sql = "SELECT
					p.*,
					pd.MontoAprobado,
					pd.MontoAjustado,
					pd.MontoCompromiso
				FROM pv_partida p
				INNER JOIN pv_presupuestodet pd On (pd.cod_partida = p.cod_partida)
				WHERE
					pd.CodOrganismo = '$CodOrganismo' AND
					pd.CodPresupuesto = '$CodPresupuesto' AND
					pd.cod_partida = '$cod_partida'";
		$field = getRecord($sql);
		$MontoDisponible = $field['MontoAjustado'] - $field['MontoCompromiso'];
		$id = $field['cod_partida'];
		?>
		<tr class="trListaBody" id="<?=$detalle?>_<?=$id?>" onclick="clk($(this), '<?=$detalle?>', '<?=$detalle?>_<?=$id?>');">
			<td align="center">
				<input type="text" name="<?=$detalle?>_CategoriaProg[]" value="<?=$CategoriaProg?>" class="cell2" />
				<input type="hidden" name="<?=$detalle?>_Ejercicio[]" value="<?=$Ejercicio?>" />
				<input type="hidden" name="<?=$detalle?>_CodPresupuesto[]" value="<?=$CodPresupuesto?>" />
			</td>
            <td>
				<select name="<?=$detalle?>_CodFuente[]" class="cell2 CodFuente" <?=$disabled_ver?>>
					<?=loadSelect2("pv_fuentefinanciamiento","CodFuente","Denominacion",$CodFuente,10)?>
				</select>
            </td>
			<td align="center">
				<input type="hidden" name="<?=$detalle?>_cod_partida[]" value="<?=$id?>" />
				<?=$field['cod_partida']?>
			</td>
			<td><input type="text" value="<?=htmlentities($field['denominacion'])?>" class="cell2" readonly /></td>
			<td><input type="text" name="<?=$detalle?>_MontoAjuste[]" value="0,00" class="cell currency" style="text-align:right;" onchange="setMontos('<?=$detalle?>');" /></td>
			<td align="right"><?=number_format($field['MontoAprobado'],2,',','.')?></td>
			<td align="right"><?=number_format($field['MontoAjustado'],2,',','.')?></td>
			<td align="right"><?=number_format($field['MontoCompromiso'],2,',','.')?></td>
			<td align="right"><input type="text" name="<?=$detalle?>_MontoDisponible[]" value="<?=number_format($MontoDisponible,2,',','.')?>" class="cell2" style="text-align:right;" readonly /></td>
		</tr>
		<?php
	}
}*/
?>