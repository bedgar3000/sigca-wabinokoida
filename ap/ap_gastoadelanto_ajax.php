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
		$FechaDocumento = formatFechaAMD($FechaDocumento);
		$FechaPago = formatFechaAMD($FechaPago);
		$MontoAfecto = setNumero($MontoAfecto);
		$MontoNoAfecto = setNumero($MontoNoAfecto);
		$MontoImpuestoVentas = setNumero($MontoImpuestoVentas);
		$MontoRetenciones = setNumero($MontoRetenciones);
		$MontoTotal = setNumero($MontoTotal);
		$SaldoAdelanto = setNumero($SaldoAdelanto);
		$Anio = (empty($Anio) ? substr($FechaPago,0,4) : $Anio);
		$Periodo = substr($FechaPago, 0, 7);
		$iTipoCompromiso = (!empty($TipoCompromiso)?"TipoCompromiso = '$TipoCompromiso',":'');
		$iNroCompromiso = (!empty($NroCompromiso)?"NroCompromiso = '$NroCompromiso',":'');
		$iNroOrden = (!empty($NroOrden)?"NroOrden = '$NroOrden',":'');
		##	valido
		if (!trim($CodOrganismo) || !trim($CodProveedor) || !trim($CodPagarA) || !trim($CodClasificacion) || !trim($TipoAdelanto) || !trim($FechaDocumento) || !trim($FechaPago) || !trim($CodTipoPago) || !trim($CodTipoServicio) || !trim($MontoTotal) || !trim($SaldoAdelanto)) die("Debe llenar los campos (*) obligatorios.");
		##	codigo
		$CodAdelanto = codigo('ap_gastoadelanto','CodAdelanto',10);
		$NroAdelanto = codigo('ap_gastoadelanto','NroAdelanto',10,['CodOrganismo'],[$CodOrganismo]);
		##	inserto
		$sql = "INSERT INTO ap_gastoadelanto
				SET
					CodAdelanto = '$CodAdelanto',
					CodOrganismo = '$CodOrganismo',
					NroAdelanto = '$NroAdelanto',
					CodClasificacion = '$CodClasificacion',
					FechaDocumento = '$FechaDocumento',
					Anio = '$Anio',
					Periodo = '$Periodo',
					CodTipoPago = '$CodTipoPago',
					CodProveedor = '$CodProveedor',
					CodPagarA = '$CodPagarA',
					TipoAdelanto = '$TipoAdelanto',
					$iTipoCompromiso
					$iNroCompromiso
					$iNroOrden
					CodTipoServicio = '$CodTipoServicio',
					MontoAfecto = '$MontoAfecto',
					MontoNoAfecto = '$MontoNoAfecto',
					MontoImpuestoVentas = '$MontoImpuestoVentas',
					MontoRetenciones = '$MontoRetenciones',
					MontoTotal = '$MontoTotal',
					SaldoAdelanto = '$SaldoAdelanto',
					Descripcion = '$Descripcion',
					FechaEsperadaPago = '$FechaPago',
					FechaPago = '$FechaPago',
					CodCentroCosto = '$CodCentroCosto',
					PreparadoPor = '$PreparadoPor',
					FechaPreparado = '$FechaPreparado',
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()";
		execute($sql);
		##	retenciones
		$Secuencia = 0;
		for ($i=0; $i < count($retencion_Secuencia); $i++)
		{
			++$Secuencia;
			$retencion_MontoAfecto[$i] = setNumero($retencion_MontoAfecto[$i]);
			$retencion_Factor[$i] = setNumero($retencion_Factor[$i]);
			$retencion_MontoImpuesto[$i] = setNumero($retencion_MontoImpuesto[$i]);
			##	valido
			if (!trim($retencion_Factor[$i])) die("(Retenciones) El Factor no puede ser cero.");
			elseif (!trim($retencion_MontoAfecto[$i])) die("(Retenciones) El Monto Afecto no puede ser cero.");
			elseif (!trim($retencion_MontoImpuesto[$i])) die("(Retenciones) El Monto Total no puede ser cero.");
			##	inserto
			$sql = "INSERT INTO ap_gastoadelantoimpuesto
					SET
						CodAdelanto = '$CodAdelanto',
						Secuencia = '$Secuencia',
						CodImpuesto = '$retencion_CodImpuesto[$i]',
						Factor = '$retencion_Factor[$i]',
						MontoAfecto = '$retencion_MontoAfecto[$i]',
						MontoImpuesto = '$retencion_MontoImpuesto[$i]',
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
		$FechaDocumento = formatFechaAMD($FechaDocumento);
		$FechaPago = formatFechaAMD($FechaPago);
		$MontoAfecto = setNumero($MontoAfecto);
		$MontoNoAfecto = setNumero($MontoNoAfecto);
		$MontoImpuestoVentas = setNumero($MontoImpuestoVentas);
		$MontoRetenciones = setNumero($MontoRetenciones);
		$MontoTotal = setNumero($MontoTotal);
		$SaldoAdelanto = setNumero($SaldoAdelanto);
		$Anio = (empty($Anio) ? substr($FechaPago,0,4) : $Anio);
		$Periodo = substr($FechaPago, 0, 7);
		$iTipoCompromiso = (!empty($TipoCompromiso)?"TipoCompromiso = '$TipoCompromiso',":'');
		$iNroCompromiso = (!empty($NroCompromiso)?"NroCompromiso = '$NroCompromiso',":'');
		$iNroOrden = (!empty($NroOrden)?"NroOrden = '$NroOrden',":'');
		##	valido
		if (!trim($CodPagarA) || !trim($CodClasificacion) || !trim($TipoAdelanto) || !trim($FechaDocumento) || !trim($FechaPago) || !trim($CodTipoPago) || !trim($CodTipoServicio) || !trim($MontoTotal) || !trim($SaldoAdelanto)) die("Debe llenar los campos (*) obligatorios.");
		##	actualizo
		$sql = "UPDATE ap_gastoadelanto
				SET
					CodClasificacion = '$CodClasificacion',
					Anio = '$Anio',
					Periodo = '$Periodo',
					CodTipoPago = '$CodTipoPago',
					CodPagarA = '$CodPagarA',
					TipoAdelanto = '$TipoAdelanto',
					$iTipoCompromiso
					$iNroCompromiso
					$iNroOrden
					CodTipoServicio = '$CodTipoServicio',
					MontoAfecto = '$MontoAfecto',
					MontoNoAfecto = '$MontoNoAfecto',
					MontoImpuestoVentas = '$MontoImpuestoVentas',
					MontoRetenciones = '$MontoRetenciones',
					MontoTotal = '$MontoTotal',
					SaldoAdelanto = '$SaldoAdelanto',
					Descripcion = '$Descripcion',
					FechaEsperadaPago = '$FechaPago',
					FechaPago = '$FechaPago',
					CodCentroCosto = '$CodCentroCosto',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodAdelanto = '$CodAdelanto'";
		execute($sql);
		##	retenciones
		if (count($retencion_Secuencia))
		{
			$sql = "DELETE FROM ap_gastoadelantoimpuesto
					WHERE
						CodAdelanto = '$CodAdelanto'
						AND Secuencia NOT IN (".implode(",",$retencion_Secuencia).")";
		}
		else
		{
			$sql = "DELETE FROM ap_gastoadelantoimpuesto WHERE CodAdelanto = '$CodAdelanto'";
		}
		execute($sql);
		$Secuencia = 0;
		for ($i=0; $i < count($retencion_Secuencia); $i++)
		{
			if (!$retencion_Secuencia[$i]) 
				$retencion_Secuencia[$i] = codigo('ap_gastoadelantoimpuesto','Secuencia',11,['CodAdelanto'],[$CodAdelanto]);
			$retencion_Factor[$i] = setNumero($retencion_Factor[$i]);
			$retencion_MontoAfecto[$i] = setNumero($retencion_MontoAfecto[$i]);
			$retencion_MontoImpuesto[$i] = setNumero($retencion_MontoImpuesto[$i]);
			##	valido
			if (!trim($retencion_Factor[$i])) die("(Retenciones) El Factor no puede ser cero.");
			elseif (!trim($retencion_MontoAfecto[$i])) die("(Retenciones) El Monto Afecto no puede ser cero.");
			elseif (!trim($retencion_MontoImpuesto[$i])) die("(Retenciones) El Monto Total no puede ser cero.");
			##	inserto
			$sql = "REPLACE INTO ap_gastoadelantoimpuesto
					SET
						CodAdelanto = '$CodAdelanto',
						Secuencia = '$retencion_Secuencia[$i]',
						CodImpuesto = '$retencion_CodImpuesto[$i]',
						Factor = '$retencion_Factor[$i]',
						MontoAfecto = '$retencion_MontoAfecto[$i]',
						MontoImpuesto = '$retencion_MontoImpuesto[$i]',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	-----------------
		mysql_query("COMMIT");
	}
	//	aprobar
	elseif ($accion == "aprobar") {
		mysql_query("BEGIN");
		##	-----------------
		$CodTipoDocumento = $_PARAMETRO['TIPODOCAP'];
		##	
		$sql = "SELECT
					ga.*,
					cbd.NroCuenta,
					td.CodCuentaAde,
					td.CodCuentaAdePub20
				FROM ap_gastoadelanto ga
				LEFT JOIN ap_ctabancariadefault cbd ON (
					cbd.CodOrganismo = ga.CodOrganismo
					AND cbd.CodTipoPago = ga.CodTipoPago
				)
				LEFT JOIN ap_tipodocumento td ON td.CodTipoDocumento = '$CodTipoDocumento'
				WHERE ga.CodAdelanto = '$CodAdelanto'";
		$field = getRecord($sql);
		##	
		if (empty($field['NroCuenta'])) die('No se encontró una cuenta por defecto para la obligación');
		##	actualizo
		$sql = "UPDATE ap_gastoadelanto
				SET
					AprobadoPor = '$AprobadoPor',
					FechaAprobado = '$FechaAprobado',
					Estado = 'AP',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodAdelanto = '$CodAdelanto'";
		execute($sql);
		##	obligación
		$NroRegistro = codigo('ap_obligaciones','NroRegistro',6,['CodOrganismo'],[$CodOrganismo]);
		$NroDocumento = codigo('ap_obligaciones','NroDocumento',10,['CodProveedor','CodTipoDocumento'],[$CodProveedor,$CodTipoDocumento]);
		$NroControl = $CodTipoDocumento . $field['NroAdelanto'] . $field['Anio'];
		##	
		$sql = "INSERT INTO ap_obligaciones
				SET
					CodProveedor = '$field[CodProveedor]',
					CodTipoDocumento = '$CodTipoDocumento',
					NroDocumento = '$NroDocumento',
					CodOrganismo = '$field[CodOrganismo]',
					CodProveedorPagar = '$field[CodPagarA]',
					NroControl = '$NroControl',
					NroFactura = '$NroControl',
					NroCuenta = '$field[NroCuenta]',
					CodTipoPago = '$field[CodTipoPago]',
					CodTipoServicio = '$field[CodTipoServicio]',
					ReferenciaTipoDocumento = '$CodTipoDocumento',
					ReferenciaNroDocumento = '$NroControl',
					MontoObligacion = '$field[SaldoAdelanto]',
					MontoImpuestoOtros = '$field[MontoRetenciones]',
					MontoNoAfecto = '$field[MontoNoAfecto]',
					MontoAfecto = '$field[MontoAfecto]',
					MontoAdelanto = 0.00,
					MontoImpuesto = '$field[MontoImpuestoVentas]',
					MontoPagoParcial = 0.00,
					NroRegistro = '$NroRegistro',
					Comentarios = '$field[Descripcion]',
					ComentariosAdicional = '$field[Descripcion]',
					FechaRegistro = '$FechaActual',
					FechaVencimiento = '$FechaActual',
					FechaRecepcion = '$FechaActual',
					FechaDocumento = '$FechaActual',
					FechaProgramada = '$FechaActual',
					FechaFactura = '$FechaActual',
					IngresadoPor = '$_SESSION[CODPERSONA_ACTUAL]',
					FechaPreparacion = '$FechaActual',
					Periodo = '$PeriodoActual',
					CodCentroCosto = '$field[CodCentroCosto]',
					FlagGenerarPago = 'S',
					FlagAfectoIGV = 'N',
					FlagDiferido = 'N',
					FlagPagoDiferido = 'N',
					FlagCompromiso = 'N',
					FlagPresupuesto = 'N',
					FlagPagoIndividual = 'N',
					FlagCajaChica = 'S',
					FlagDistribucionManual = 'N',
					FlagNomina = 'N',
					FlagFacturaPendiente = 'N',
					FlagAgruparIgv = 'N',
					Ejercicio = '$field[Anio]',
					Estado = 'PR',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()";
		execute($sql);
		##	
		$MontoBruto = $field['MontoAfecto'] + $field['MontoNoAfecto'];
		$sql = "INSERT INTO ap_obligacionescuenta
				SET
					CodProveedor = '$field[CodProveedor]',
					CodTipoDocumento = '$CodTipoDocumento',
					NroDocumento = '$NroDocumento',
					Secuencia = '1',
					Linea = '1',
					Descripcion = '$field[Descripcion]',
					Monto = '$MontoBruto',
					CodCentroCosto = '$field[CodCentroCosto]',
					CodCuenta = '$field[CodCuentaAde]',
					CodCuentaPub20 = '$field[CodCuentaAdePub20]',
					FlagNoAfectoIGV = 'S',
					Referencia = '$NroControl',
					CodPersona = '$field[CodProveedor]',
					CodOrganismo = '$field[CodOrganismo]',
					Ejercicio = '$field[Anio]',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	anular
	elseif ($accion == "anular") {
		mysql_query("BEGIN");
		##	-----------------
		$field_adelanto = getRecord("SELECT * FROM ap_gastoadelanto WHERE CodAdelanto = '$CodAdelanto'");
		##	
		if ($field_adelanto['Estado'] == 'AP') $NuevoEstado = 'PR';
		elseif ($field_adelanto['Estado'] == 'PR') $NuevoEstado = 'AN';
		else die('No puede anular un Adelanto <strong>'.printValores('adelanto-estado',$field_adelanto['Estado']).'</strong>');
		##	actualizo
		$sql = "UPDATE ap_gastoadelanto
				SET
					Estado = '$NuevoEstado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodAdelanto = '$CodAdelanto'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
}
elseif ($modulo == "validar") {
	//	modificar
	if($accion == "modificar") {
		$sql = "SELECT Estado FROM ap_gastoadelanto WHERE CodAdelanto = '$codigo'";
		$Estado = getVar3($sql);
		if ($Estado != 'PR') die('No puede modificar un adelanto <strong>'.printValores('adelanto-estado',$Estado).'</strong>');
	}
	//	aprobar
	elseif($accion == "aprobar") {
		$sql = "SELECT Estado FROM ap_gastoadelanto WHERE CodAdelanto = '$codigo'";
		$Estado = getVar3($sql);
		if ($Estado != 'PR') die('No puede aprobar un adelanto <strong>'.printValores('adelanto-estado',$Estado).'</strong>');
	}
	//	anular
	elseif($accion == "anular") {
		$sql = "SELECT Estado FROM ap_gastoadelanto WHERE CodAdelanto = '$codigo'";
		$Estado = getVar3($sql);
		if ($Estado != 'PR' && $Estado != 'AP') die('No puede anular un adelanto <strong>'.printValores('adelanto-estado',$Estado).'</strong>');
	}
}
elseif ($modulo == "ajax") {
	if ($accion == "retencion_insertar") {
		$id = $nro_detalles;
		##	
		$sql = "SELECT * FROM mastimpuestos WHERE CodImpuesto = '$CodImpuesto'";
		$field = getRecords($sql);
		foreach ($field as $f)
		{
			if ($f['FlagImponible'] == "I") $BaseImponible = $MontoImpuesto;
			elseif ($f['FlagImponible'] == "N") $BaseImponible = $MontoAfecto;
			elseif ($f['FlagImponible'] == "N") $BaseImponible = $MontoAfecto + $MontoNoAfecto;
			elseif ($f['FlagImponible'] == "T") $BaseImponible = $MontoAfecto + $MontoImpuesto;
			$Total = $BaseImponible * $f['FactorPorcentaje'] / 100;
			if ($f['Signo'] == "N") $Total = $Total * -1;
			?>
			<tr class="trListaBody" onclick="clk($(this), 'retencion', 'retencion_<?=$id?>');" id="retencion_<?=$id?>">
				<th>
					<input type="hidden" name="retencion_Secuencia[]" id="retencion_Secuencia<?=$id?>" value="0">
					<input type="hidden" name="retencion_CodImpuesto[]" value="<?=$f['CodImpuesto']?>">
					<input type="hidden" name="retencion_FlagImponible[]" value="<?=$f['FlagImponible']?>">
					<input type="hidden" name="retencion_Signo[]" value="<?=$f['Signo']?>">
					<?=$nro_detalles?>
				</th>
				<td align="center"><?=$f['CodImpuesto']?></td>
				<td><?=htmlentities($f['Descripcion'])?></td>
				<td>
					<input type="text" name="retencion_Factor[]" value="<?=number_format($f['FactorPorcentaje'],2,',','.')?>" class="cell2 " style="text-align:right;" readonly>
				</td>
				<td>
					<input type="text" name="retencion_MontoAfecto[]" value="<?=number_format($BaseImponible,2,',','.')?>" class="cell2 " style="text-align:right;" readonly>
				</td>
				<td>
					<input type="text" name="retencion_MontoImpuesto[]" value="<?=number_format($Total,2,',','.')?>" class="cell2 " style="text-align:right;" readonly>
				</td>
			</tr>
			<?php
		}
	}
	elseif ($accion == "setFactorPorcentaje") {
		$sql = "SELECT i.FactorPorcentaje
				FROM masttiposervicioimpuesto tsi
				INNER JOIN mastimpuestos i ON i.CodImpuesto = tsi.CodImpuesto
				WHERE
					tsi.CodTipoServicio = '$CodTipoServicio'
					AND i.CodRegimenFiscal = 'I'
				LIMIT 0, 1";
		$FactorPorcentaje = getVar3($sql);

		die(json_encode(['FactorPorcentaje' => floatval($FactorPorcentaje)]));
	}
}
?>