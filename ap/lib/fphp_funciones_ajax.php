<?php
include("../../lib/fphp.php");
include("fphp.php");
//	--------------------------

//	--------------------------

//	consulto si se puede modificar una obligacion
if ($accion == "obligacion_modificar") {
	list($CodOrganismo, $CodProveedor, $CodTipoDocumento, $NroDocumento) = split("[_]", $codigo);
	$sql = "SELECT Estado
			FROM ap_obligaciones
			WHERE
				CodOrganismo = '".$CodOrganismo."' AND
				CodProveedor = '".$CodProveedor."' AND
				CodTipoDocumento = '".$CodTipoDocumento."' AND
				NroDocumento = '".$NroDocumento."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) {
		$field = mysql_fetch_array($query);
		if ($field['Estado'] != "PR") die("No se puede modificar esta obligación");
	} else die("No se encuentra el registro");
}

//	consulto si se puede anular una obligacion
elseif ($accion == "obligacion_anular") {
	list($CodOrganismo, $CodProveedor, $CodTipoDocumento, $NroDocumento) = split("[_]", $codigo);
	$sql = "SELECT Estado
			FROM ap_obligaciones
			WHERE
				CodOrganismo = '".$CodOrganismo."' AND
				CodProveedor = '".$CodProveedor."' AND
				CodTipoDocumento = '".$CodTipoDocumento."' AND
				NroDocumento = '".$NroDocumento."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) {
		$field = mysql_fetch_array($query);
		if ($field['Estado'] == "AN") die("La obligación ya se encuentra <strong>Anulada</strong>");
		elseif ($field['Estado'] == "AP") die("No se puede anular una obligación <strong>Aprobada</strong><br />Debe anular primero la <strong>Orden de Pago</strong> generada");
		elseif ($field['Estado'] == "PA") die("No se puede anular una obligación <strong>Pagada</strong><br />Debe anular primero el <strong>Pago</strong> y despues la <strong>Orden de Pago</strong> generada");
	} else die("No se encuentra el registro");
}

//	muestro los detalles de los documentos seleccionados en la facturacion de logisticas
elseif ($accion == "mostrarDocumentosObligacion") {
	//	documentos
	if ($detalles_documento != "") {
		$linea_documento = split(";", $detalles_documento);	$_Linea=0;
		foreach ($linea_documento as $registro) {	$_Linea++;
			list($_Anio, $_DocumentoReferencia) = split("[.]", $registro);
			if ($grupo != $_DocumentoReferencia) {
				$grupo = $_DocumentoReferencia;
				?><tr class="trListaBody2"><td colspan="7">Documento: <?=$_DocumentoReferencia?></td></tr><?php
			}
			//	consulto
			$sql = "SELECT * 
					FROM ap_documentosdetalle 
					WHERE 
						Anio = '".$_Anio."' AND
						CodProveedor = '".$CodProveedor."' AND
						DocumentoClasificacion = '".$DocumentoClasificacion."' AND
						DocumentoReferencia = '".$_DocumentoReferencia."'
					ORDER BY Secuencia";
			$query_det = mysql_query($sql) or die ($sql.mysql_error());
			$rows_det = mysql_num_rows($query_det); $suma_rows_det += $rows_det;
			while ($field_det = mysql_fetch_array($query_det)) { $i++;
				if ($field_det['CodItem'] != "") $coddetalle = $field_det['CodItem']; else $coddetalle = $field_det['CommoditySub'];
				$total = $field_det['Cantidad'] * $field_det['PrecioUnit'];
				?>
				<tr class="trListaBody">
					<td align="center"><?=$i?></td>
					<td align="center"><?=$coddetalle?></td>
					<td><?=($field_det['Descripcion'])?></td>
					<td align="center"><?=$field_det['CodCentroCosto']?></td>
					<td align="right"><?=number_format($field_det['Cantidad'], 2, ',', '.')?></td>
					<td align="right"><?=number_format($field_det['PrecioUnit'], 2, ',', '.')?></td>
					<td align="right"><?=number_format($total, 2, ',', '.')?></td>
				</tr>
				<?php
			}
		}
	}
}

//	consulto si se puede modificar una orden
elseif ($accion == "orden_pago_modificar") {
	list($Anio, $CodOrganismo, $NroOrden) = split("[_]", $codigo);
	$sql = "SELECT Estado
			FROM ap_ordenpago
			WHERE
				Anio = '".$Anio."' AND
				CodOrganismo = '".$CodOrganismo."' AND
				NroOrden = '".$NroOrden."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) {
		$field = mysql_fetch_array($query);
		if ($field['Estado'] == "AN") die("No se puede modificar esta orden");
	} else die("No se encuentra el registro");
}

//	consulto si se puede anular una orden
elseif ($accion == "orden_pago_anular") {
	list($Anio, $CodOrganismo, $NroOrden) = split("[_]", $codigo);
	$sql = "SELECT Estado
			FROM ap_ordenpago
			WHERE
				Anio = '".$Anio."' AND
				CodOrganismo = '".$CodOrganismo."' AND
				NroOrden = '".$NroOrden."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) {
		$field = mysql_fetch_array($query);
		if ($field['Estado'] == "AN") die("La orden ya se encuentra <strong>Anulada</strong>");
		elseif ($field['Estado'] == "PA") die("No se puede anular una orden <strong>Pagada</strong><br />Debe anular primero el <strong>Pago</strong>");
	} else die("No se encuentra el registro");
}

//	consulto si se puede modificar un pago
elseif ($accion == "pago_modificar") {
	list($NroProceso, $Secuencia, $CodTipoPago) = split("[_]", $codigo);
	$sql = "SELECT Estado
			FROM ap_pagos
			WHERE
				NroProceso = '".$NroProceso."' AND
				Secuencia = '".$Secuencia."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) {
		$field = mysql_fetch_array($query);
		if ($field['Estado'] == "AN") die("No se puede modificar un pago <strong>Anulado</strong>");
	} else die("No se encuentra el registro");
}

//	consulto si se puede anular un pago
elseif ($accion == "pago_anular") {
	list($NroProceso, $Secuencia, $CodTipoPago) = split("[_]", $codigo);
	$sql = "SELECT Estado
			FROM ap_pagos
			WHERE
				NroProceso = '".$NroProceso."' AND
				Secuencia = '".$Secuencia."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) {
		$field = mysql_fetch_array($query);
		if ($field['Estado'] == "AN") die("El pago ya se encuentra <strong>Anulado</strong>");
	} else die("No se encuentra el registro");
}

//	
elseif ($accion == "documentos_prepago") {
	list($NroProceso, $Secuencia) = split("[.]", $registro);
	$sql = "SELECT
				p.Secuencia,
				p.NomProveedorPagar,
				p.MontoPago,
				p.MontoRetenido,
				(p.MontoPago + MontoRetenido) AS MontoPagar,
				mp.NomCompleto AS NomProveedor
			FROM
				ap_pagos p
				INNER JOIN mastpersonas mp ON (p.CodProveedor = mp.CodPersona)
			WHERE
				p.NroProceso = '".$NroProceso."' AND
				p.Secuencia = '".$Secuencia."'
			ORDER BY Secuencia";
	$sql = "SELECT
				p.Secuencia,
				p.NomProveedorPagar,
				op.MontoTotal AS MontoPago,
				o.MontoImpuestoOtros AS MontoRetenido,
				(op.MontoTotal + o.MontoImpuestoOtros) AS MontoPagar,
				mp.NomCompleto AS NomProveedor
			FROM
				ap_pagos p
				INNER JOIN ap_ordenpago op ON (op.NroProceso = p.NroProceso AND op.Secuencia = p.Secuencia)
				INNER JOIN ap_obligaciones o ON (o.CodProveedor = op.CodProveedor AND o.CodTipoDocumento = op.CodTipoDocumento AND o.NroDocumento = op.NroDocumento)
				INNER JOIN mastpersonas mp ON (p.CodProveedor = mp.CodPersona)
			WHERE
				p.NroProceso = '".$NroProceso."' AND
				p.Secuencia = '".$Secuencia."'
			ORDER BY Secuencia";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	while ($field = mysql_fetch_array($query)) {
		?>
		<tr class="trListaBody">
			<th align="center"><?=$field['Secuencia']?></th>
			<td><?=htmlentities($field['NomProveedorPagar'])?></td>
			<td><?=htmlentities($field['NomProveedor'])?></td>
			<td align="right"><?=number_format($field['MontoPagar'], 2, ',', '.')?></td>
			<td align="right"><?=number_format($field['MontoRetenido'], 2, ',', '.')?></td>
			<td align="right"><strong><?=number_format($field['MontoPago'], 2, ',', '.')?></strong></td>
		</tr>
		<?php
	}
}

//	
elseif ($accion == "mostrarTabDistribucionObligacion") {
	//	obtengo detalles
	$_TOTAL = 0;
	$detalle = split(";char:tr;", $detalles);
	foreach ($detalle as $linea) {
		list($_cod_partida, $_CodCuenta, $_CodCuentaPub20, $_Monto, $_CategoriaProg, $_Ejercicio, $_CodPresupuesto, $_CodFuente) = split(";char:td;", $linea);
		if ($_codpartida != "" || $_CodCuenta != "" || $_CodCuentaPub20 != "") {
			$_CUENTA[$_CodCuenta] = $_CodCuenta;
			$_CUENTA20[$_CodCuentaPub20] = $_CodCuentaPub20;
			$_PARTIDA[$_CodPresupuesto][$_CodFuente][$_cod_partida] = $_cod_partida;
			$_PARTIDA_CUENTA[$_CodPresupuesto][$_CodFuente][$_cod_partida] = $_CodCuenta;
			$_PARTIDA_CUENTA20[$_CodPresupuesto][$_CodFuente][$_cod_partida] = $_CodCuentaPub20;
			$_CUENTA_MONTO[$_CodCuenta] += $_Monto;
			$_CUENTA_MONTO20[$_CodCuentaPub20] += $_Monto;
			$_PARTIDA_MONTO[$_CodPresupuesto][$_CodFuente][$_cod_partida] += $_Monto;
			$_EJERCICIO[$_CodPresupuesto][$_CodFuente][$_cod_partida] = $_Ejercicio;
			$_PRESUPUESTO[$_CodPresupuesto][$_CodFuente][$_cod_partida] = $_CodPresupuesto;
			$_CATEGORIA[$_CodPresupuesto][$_CodFuente][$_cod_partida] = $_CategoriaProg;
			$_FUENTE[$_CodPresupuesto][$_CodFuente][$_cod_partida] = $_CodFuente;
		}
	}
	if ($FlagPresupuesto == "S" && $MontoImpuesto > 0 && $FlagAgruparIgv != 'S') {
		list($_cod_partida_igv, $_CodCuenta_igv, $_CodCuentaPub20_igv) = getPartidaCuentaFromIGV($_PARAMETRO["IGVCODIGO"]);
		$sql = "SELECT *
				FROM pv_presupuesto
				WHERE CodOrganismo = '$CodOrganismo' AND Ejercicio = '$Ejercicio' AND CategoriaProg = '$_PARAMETRO[IGVCATPROG]'";
		$field_presupuesto = getRecord($sql);

		$sql = "SELECT CodFuente
				FROM pv_presupuestodet
				WHERE CodOrganismo = '$CodOrganismo' AND CodPresupuesto = '$field_presupuesto[CodPresupuesto]' AND cod_partida = '$_cod_partida_igv';";
		$IgvFuente = getVar3($sql);

		$_IgvCodPresupuesto = $field_presupuesto['CodPresupuesto'];
	}
	elseif ($MontoImpuesto > 0 && $FlagAgruparIgv != 'S') {
		$_cod_partida_igv = "";
		$_CodCuenta_igv = $_PARAMETRO['CTAOPIVAONCO'];
		$_CodCuentaPub20_igv = $_PARAMETRO['CTAOPIVAPUB20'];
	}

	if (($_cod_partida_igv || $_cod_partida_igv || $_CodCuentaPub20_igv) && $FlagAgruparIgv != 'S') {
		$_CUENTA[$_CodCuenta_igv] = $_CodCuenta_igv;
		$_CUENTA20[$_CodCuentaPub20_igv] = $_CodCuentaPub20_igv;
		$_PARTIDA[$_IgvCodPresupuesto][$IgvFuente][$_cod_partida_igv] = $_cod_partida_igv;
		$_PARTIDA_CUENTA[$_IgvCodPresupuesto][$IgvFuente][$_cod_partida_igv] = $_CodCuenta_igv;
		$_PARTIDA_CUENTA20[$_IgvCodPresupuesto][$IgvFuente][$_cod_partida_igv] = $_CodCuentaPub20_igv;
		$_CUENTA_MONTO[$_CodCuenta_igv] = $MontoImpuesto;
		$_CUENTA_MONTO20[$_CodCuentaPub20_igv] = $MontoImpuesto;
		$_PARTIDA_MONTO[$_IgvCodPresupuesto][$IgvFuente][$_cod_partida_igv] = $MontoImpuesto;
		$_EJERCICIO[$_IgvCodPresupuesto][$IgvFuente][$_cod_partida_igv] = $field_presupuesto['Ejercicio'];
		$_PRESUPUESTO[$_IgvCodPresupuesto][$IgvFuente][$_cod_partida_igv] = $field_presupuesto['CodPresupuesto'];
		$_CATEGORIA[$_IgvCodPresupuesto][$IgvFuente][$_cod_partida_igv] = $field_presupuesto['CategoriaProg'];
		$_FUENTE[$_IgvCodPresupuesto][$IgvFuente][$_cod_partida_igv] = $IgvFuente;
	}
	//	imprimo cuentas
	foreach ($_CUENTA as $CodCuenta) {
		$Descripcion = getValorCampo("ac_mastplancuenta", "CodCuenta", "Descripcion", $CodCuenta);
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
					<?=number_format($_CUENTA_MONTO[$CodCuenta], 2, ',', '.')?>
				</td>
			</tr>
			<?php
		}
	}
	echo "|";
	//	imprimo cuentas
	foreach ($_CUENTA20 as $CodCuentaPub20) {
		$Descripcion = getValorCampo("ac_mastplancuenta20", "CodCuenta", "Descripcion", $CodCuentaPub20);
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
					<?=number_format($_CUENTA_MONTO20[$CodCuentaPub20], 2, ',', '.')?>
				</td>
			</tr>
			<?php
		}
	}
	echo "|";
	if ($FlagPresupuesto == "S") {
		//	imprimo partidas
		foreach ($_PARTIDA as $_CodPresupuesto => $Presupuesto) {
			$sql = "SELECT
						pv.CategoriaProg,
						ue.Denominacion AS UnidadEjecutora,
						CONCAT(ss.CodSector, pr.CodPrograma, a.CodActividad) AS CatProg
					FROM pv_presupuesto pv
					INNER JOIN pv_categoriaprog cp ON (cp.CategoriaProg = pv.CategoriaProg)
					INNER JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cp.CodUnidadEjec)
					LEFT JOIN pv_actividades a ON (a.IdActividad = cp.IdActividad)
					LEFT JOIN pv_proyectos py ON (py.IdProyecto = a.IdProyecto)
					LEFT JOIN pv_subprogramas spr ON (spr.IdSubPrograma = py.IdSubPrograma)
					LEFT JOIN pv_programas pr ON (pr.IdPrograma = spr.IdPrograma)
					LEFT JOIN pv_subsector ss ON (ss.IdSubSector = pr.IdSubSector)
					WHERE pv.CodOrganismo = '$CodOrganismo' AND pv.CodPresupuesto = '$_CodPresupuesto'";
			$field_categoria = getRecord($sql);
			?>
			<tr class="trListaBody2">
				<td colspan="3">
					<?=$field_categoria['CatProg']?> - <?=$field_categoria['UnidadEjecutora']?>
				</td>
			</tr>
			<?php
			foreach ($Presupuesto as $_CodFuente => $Partida) {
				foreach ($Partida as $cod_partida) {
					list($MontoAjustado, $MontoCompromiso, $PreCompromiso, $CotizacionesAsignadas) = disponibilidadPartida2($_EJERCICIO[$_CodPresupuesto][$_CodFuente][$cod_partida], 
																															$CodOrganismo, $cod_partida, 
																															$_PRESUPUESTO[$_CodPresupuesto][$_CodFuente][$cod_partida], 
																															$_FUENTE[$_CodPresupuesto][$_CodFuente][$cod_partida]);
					if ($Estado == 'PR' && $NroDocumento != '') {
						$PreCompromiso -= $_PARTIDA_MONTO[$_CodPresupuesto][$_CodFuente][$cod_partida];
					}
					$MontoPendiente = $PreCompromiso + $CotizacionesAsignadas;
					if ($FlagCompromiso != 'S') {
						$MontoCompromiso -= $_PARTIDA_MONTO[$_CodPresupuesto][$_CodFuente][$cod_partida];
					}
					$MontoDisponible = $MontoAjustado - $MontoCompromiso;
					$MontoDisponibleReal = $MontoAjustado - ($MontoCompromiso + $MontoPendiente);
					##	valido
					if (($MontoDisponible - $_PARTIDA_MONTO[$_CodPresupuesto][$_CodFuente][$cod_partida]) < 0) $style = "style='background-color:#F8637D;'";
					elseif(($MontoDisponibleReal - $_PARTIDA_MONTO[$_CodPresupuesto][$_CodFuente][$cod_partida]) < 0) $style = "style='background-color:#FFC;'";
					else $style = "style='background-color:#D0FDD2;'";
					##	
					$Descripcion = getValorCampo("pv_partida", "cod_partida", "denominacion", $cod_partida);
					?>
					<tr class="trListaBody" <?=$style?>>
						<td align="center">
							<?=$_CodFuente?>
						</td>
						<td align="center">
		                    <input type="hidden" name="cod_partida" value="<?=$cod_partida?>" />
		                    <input type="hidden" name="CodCuenta" value="<?=$CodCuenta?>" />
		                    <input type="hidden" name="CodCuentaPub20" value="<?=$CodCuentaPub20?>" />
		                    <input type="hidden" name="Monto" value="<?=$_PARTIDA_MONTO[$_CodPresupuesto][$_CodFuente][$cod_partida]?>" />
		                    <input type="hidden" name="MontoAjustado" value="<?=$MontoAjustado?>" />
		                    <input type="hidden" name="MontoCompromiso" value="<?=$MontoCompromiso?>" />
		                    <input type="hidden" name="PreCompromiso" value="<?=$PreCompromiso?>" />
		                    <input type="hidden" name="CotizacionesAsignadas" value="<?=$CotizacionesAsignadas?>" />
		                    <input type="hidden" name="MontoDisponible" value="<?=$MontoDisponible?>" />
		                    <input type="hidden" name="MontoDisponibleReal" value="<?=$MontoDisponibleReal?>" />
		                    <input type="hidden" name="MontoPendiente" value="<?=$MontoPendiente?>" />
							<input type="hidden" name="partidasCodFuente" value="<?=$CodFuente?>" />
							<input type="hidden" name="partidasCategoriaProg" value="<?=$_CATEGORIA[$_CodPresupuesto][$_CodFuente][$cod_partida]?>" />
							<?=$cod_partida?>
						</td>
						<td>
							<?=$Descripcion?>
						</td>
						<td align="right">
							<?=number_format($_PARTIDA_MONTO[$_CodPresupuesto][$_CodFuente][$cod_partida], 2, ',', '.')?>
						</td>
					</tr>
					<?php
				}
			}
		}
	}
}

//	
elseif ($accion == "cajaChicaMontoPagado") {
	//	obtengo impuesto
	$sql = "SELECT i.FactorPorcentaje
			FROM
				masttiposervicio ts
				INNER JOIN masttiposervicioimpuesto tsi ON (tsi.CodTipoServicio = ts.CodTipoServicio)
				INNER JOIN mastimpuestos i ON (i.CodImpuesto = tsi.CodImpuesto)
			WHERE ts.CodTipoServicio = '".$CodTipoServicio."'";
	$query_impuesto = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query_impuesto)) $field_impuesto = mysql_fetch_array($query_impuesto);
	$FactorPorcentaje = $field_impuesto['FactorPorcentaje'];
	//	montos
	if ($FactorPorcentaje > 0) {
		$MontoAfecto = $MontoPagado / (($FactorPorcentaje / 100) + 1);
		$MontoNoAfecto = 0.00;
		$MontoImpuesto = $MontoAfecto * $FactorPorcentaje / 100;
	} else {
		$MontoAfecto = 0.00;
		$MontoNoAfecto = $MontoPagado;
		$MontoImpuesto = 0.00;
	}
	echo "$MontoAfecto|$MontoNoAfecto|$MontoImpuesto";
}

//	
elseif ($accion == "cajaChicaMontoAfecto") {
	//	obtengo impuesto
	$sql = "SELECT i.FactorPorcentaje
			FROM
				masttiposervicio ts
				INNER JOIN masttiposervicioimpuesto tsi ON (tsi.CodTipoServicio = ts.CodTipoServicio)
				INNER JOIN mastimpuestos i ON (i.CodImpuesto = tsi.CodImpuesto)
			WHERE ts.CodTipoServicio = '".$CodTipoServicio."'";
	$query_impuesto = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query_impuesto)) $field_impuesto = mysql_fetch_array($query_impuesto);
	$FactorPorcentaje = $field_impuesto['FactorPorcentaje'];
	//	montos
	if ($FactorPorcentaje > 0) {
		$MontoImpuesto = $MontoAfecto * $FactorPorcentaje / 100;
	} else {
		$MontoAfecto = 0.00;
		$MontoImpuesto = 0.00;
	}
	$MontoPagado = $MontoAfecto + $MontoNoAfecto + $MontoImpuesto;
	echo "$MontoPagado|$MontoImpuesto";
}

//	consulto si se puede modificar
elseif ($accion == "transacciones_bancarias_modificar") {
	list($NroTransaccion, $Secuencia) = split("[_]", $codigo);
	$sql = "SELECT Estado
			FROM ap_bancotransaccion
			WHERE
				NroTransaccion = '".$NroTransaccion."' AND
				Secuencia = '".$Secuencia."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) {
		$field = mysql_fetch_array($query);
		if ($field['Estado'] != "PR") die("No se puede modificar este registro");
	} else die("No se encuentra el registro");
}

//	consulto si se puede modificar
elseif ($accion == "transacciones_bancarias_actualizar") {
	list($NroTransaccion, $Secuencia) = split("[_]", $codigo);
	$sql = "SELECT Estado
			FROM ap_bancotransaccion
			WHERE
				NroTransaccion = '".$NroTransaccion."' AND
				Secuencia = '".$Secuencia."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) {
		$field = mysql_fetch_array($query);
		if ($field['Estado'] != "PR") die("No se puede actualizar este registro");
	} else die("No se encuentra el registro");
}

//	consulto si se puede modificar
elseif ($accion == "transacciones_bancarias_desactualizar") {
	list($NroTransaccion, $Secuencia) = split("[_]", $codigo);
	$sql = "SELECT Estado
			FROM ap_bancotransaccion
			WHERE
				NroTransaccion = '".$NroTransaccion."' AND
				Secuencia = '".$Secuencia."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) {
		$field = mysql_fetch_array($query);
		if ($field['Estado'] != "AP") die("No se puede desactualizar este registro");
	} else die("No se encuentra el registro");
}

//	
elseif ($accion == "getPeriodoConciliacion") {
	##	periodo conciliacion
	$sql = "SELECT PeriodoConciliacion FROM ap_ctabancaria WHERE NroCuenta = '".$NroCuenta."'";
	$PeriodoConciliacion = getVar3($sql);
	##	saldo inicial
	if ($PeriodoConciliacion != "") {
		list($Anio, $Mes) = explode("-", $PeriodoConciliacion);
		$Fecha = "01-$Mes-$Anio";
		echo $Fecha;
	}
}

//	
elseif ($accion == "ctabancariadefault") {
	echo ctabancariadefault($CodOrganismo, $CodTipoPago);
}
?>