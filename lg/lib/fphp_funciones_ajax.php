<?php
include("../../lib/fphp.php");
include("fphp.php");
//	--------------------------

//	--------------------------
if ($accion == "commodity_detalle_insertar") {
	?>
    <th><?=$nrodetalle?></th>
    <td align="center">
        <input type="hidden" name="Codigo" id="Codigo_<?=$nrodetalle?>" />
        <input type="text" name="CommoditySub" id="CommoditySub_<?=$nrodetalle?>" style="text-align:center;" maxlength="3" class="cell" />
    </td>
    <td align="center">
        <input type="text" name="Descripcion" maxlength="255" class="cell" />
    </td>
    <td align="center">
        <input type="text" name="cod_partida" id="cod_partida_<?=$nrodetalle?>" value="<?=$field_detalle['cod_partida']?>" style="text-align:center;" maxlength="12" class="cell" onChange="getDescripcionLista2('accion=getDescripcionPartidaCuenta', this, $('#NomPartida_<?=$nrodetalle?>'), $('#CodCuenta_<?=$nrodetalle?>'), $('#NomCuenta_<?=$nrodetalle?>'), $('#CodCuentaPub20_<?=$nrodetalle?>'), $('#NomCuentaPub20_<?=$nrodetalle?>'));" />
        <input type="hidden" name="NomPartida" id="NomPartida_<?=$nrodetalle?>" />
    </td>
    <td align="center">
        <input type="text" name="CodCuenta" id="CodCuenta_<?=$nrodetalle?>" style="text-align:center;" maxlength="13" class="cell" onChange="getDescripcionLista2('accion=getDescripcionCuenta', this, $('#NomCuenta_<?=$nrodetalle?>'));" />
        <input type="hidden" name="NomCuenta" id="NomCuenta_<?=$nrodetalle?>" />
    </td>
    <td align="center">
        <input type="text" name="CodCuentaPub20" id="CodCuentaPub20_<?=$nrodetalle?>" style="text-align:center;" maxlength="13" class="cell" onChange="getDescripcionLista2('accion=getDescripcionCuenta20', this, $('#NomCuentaPub20_<?=$nrodetalle?>'));" />
        <input type="hidden" name="NomCuentaPub20" id="NomCuentaPub20_<?=$nrodetalle?>" />
    </td>
    <td align="center">
        <input type="text" name="CodClasificacion" id="CodClasificacion_<?=$nrodetalle?>" style="text-align:center;" maxlength="6" class="cell" onChange="getDescripcionLista2('accion=getDescripcionClasificacionActivo', this, $('#NomClasificacion_<?=$nrodetalle?>'));" />
        <input type="hidden" name="NomClasificacion" id="NomClasificacion_<?=$nrodetalle?>" />
    </td>
    <td align="center">
        <select name="CodUnidad" class="cell">
            <?=loadSelect("mastunidades", "CodUnidad", "Descripcion", "", 0)?>
        </select>
    </td>
    <td align="center">
        <input type="text" name="CodigoSNC" value="<?=($field_detalle['CodigoSNC'])?>" maxlength="10" class="cell" <?=$disabled_ver?> />
    </td>
    <td align="center">
        <select name="Estado" class="cell">
            <?=loadSelectGeneral("ESTADO", "A", 0)?>
        </select>
    </td>
    <?php
}

//	--------------------------
elseif($accion == "setDirigidoA") {
	$sql = "SELECT
				c.ReqAlmacenCompra,
				c.TipoRequerimiento,
				c.CodAlmacen,
				c.FlagCajaChica,
				a.FlagCommodity				
			FROM
				lg_clasificacion c
				INNER JOIN lg_almacenmast a ON (a.CodAlmacen = c.CodAlmacen)
			WHERE c.Clasificacion = '".$Clasificacion."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) {
		$field = mysql_fetch_array($query);
		echo "|".$field['ReqAlmacenCompra']."|".$field['TipoRequerimiento']."|".$field['CodAlmacen']."|".$field['FlagCajaChica']."|".$field['FlagCommodity'];
	}
	echo "|";
	loadSelectAlmacen($field['CodAlmacen'], $field['FlagCommodity'], 0);
}

//	--------------------------
elseif($accion == "setFlagCommodity") {
	echo getValorCampo("lg_almacenmast", "CodAlmacen", "FlagCommodity", $CodAlmacen);
}

//	--------------------------
elseif($accion == "requerimiento_modificar") {
	$sql = "SELECT Estado FROM lg_requerimientos WHERE CodRequerimiento = '".$codigo."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	if ($field['Estado'] != "PR") die("Solo se pueden modificar los requerimientos <strong>En Preparación</strong>");
}

//	--------------------------
elseif($accion == "requerimiento_anular") {
	//	verifico requerimiento
	$sql = "SELECT Estado FROM lg_requerimientos WHERE CodRequerimiento = '".$codigo."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	if ($field['Estado'] == "CO") die("No se pueden anular requerimientos <strong>Completados</strong>");
	elseif($field['Estado'] == "CE") die("No se pueden anular requerimientos <strong>Cerrados</strong>");
	//elseif($field['Estado'] == "RE") die("No se pueden anular requerimientos <strong>Rechazados</strong>");
	elseif($field['Estado'] == "AN") die("El requerimiento ya se encuentra <strong>Anulado</strong>");
	
	//	verifico los detalles
	$sql = "SELECT Estado FROM lg_requerimientosdet WHERE CodRequerimiento = '".$codigo."' AND Estado = 'CO'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) die("No se pueden anular requerimientos con lineas en Estado <strong>Completado</strong>");
	
	//	verifico los detalles
	$sql = "SELECT Estado FROM lg_requerimientosdet WHERE CodRequerimiento = '".$codigo."' AND Estado = 'CE'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) die("No se pueden anular requerimientos con lineas en Estado <strong>Cerradas</strong>");
}

//	--------------------------
elseif($accion == "requerimiento_cerrar") {
	//	verifico requerimiento
	$sql = "SELECT Estado FROM lg_requerimientos WHERE CodRequerimiento = '".$codigo."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	if ($field['Estado'] == "CO") die("No se pueden cerrar requerimientos <strong>Completados</strong>");
	elseif($field['Estado'] == "AN") die("No se pueden cerrar requerimientos <strong>Anulados</strong>");
	elseif($field['Estado'] == "RE") die("No se pueden cerrar requerimientos <strong>Rechazados</strong>");
	elseif($field['Estado'] == "CE") die("El requerimiento ya se encuentra <strong>Cerrado</strong>");
	
	//	verifico los detalles
	$sql = "SELECT Estado FROM lg_requerimientosdet WHERE CodRequerimiento = '".$codigo."' AND Estado = 'CO'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) die("No se pueden cerrar requerimientos con lineas en Estado <strong>Completado</strong>");
}

//	--------------------------
elseif($accion == "mostrarTabDistribucionOrden") {
	//	obtengo detalles
	$MontoAfecto = 0;
	list($_cod_partida_igv, $_CodCuenta_igv, $_CodCuentaPub20_igv) = getPartidaCuentaFromIGV($_PARAMETRO["IGVCODIGO"]);
	$detalle = split(";char:tr;", $detalles);
	foreach ($detalle as $linea) {		
		list($_CantidadPedida, $_PrecioUnit, $_FlagExonerado, $_CategoriaProg, $_Ejercicio, $_CodPresupuesto, $_CodFuente, $_cod_partida, $_CodCuenta, $_CodCuentaPub20) = split(";char:td;", $linea);
		$PrecioCantidad = $_PrecioUnit * $_CantidadPedida;
		$_CUENTA[$_CodCuenta] = $_CodCuenta;
		$_CUENTA20[$_CodCuentaPub20] = $_CodCuentaPub20;
		$_PARTIDA[$_CodPresupuesto][$_CodFuente][$_cod_partida] = $_cod_partida;
		$_PARTIDA_CUENTA[$_CodPresupuesto][$_CodFuente][$_cod_partida] = $_CodCuenta;
		$_PARTIDA_CUENTA20[$_CodPresupuesto][$_CodFuente][$_cod_partida] = $_CodCuentaPub20;
		$_CUENTA_MONTO[$_CodCuenta] += $PrecioCantidad;
		$_CUENTA_MONTO20[$_CodCuentaPub20] += $PrecioCantidad;
		$_PARTIDA_MONTO[$_CodPresupuesto][$_CodFuente][$_cod_partida] += $PrecioCantidad;
		if ($_FlagExonerado == "N") $MontoAfecto += $PrecioCantidad;
			$_EJERCICIO[$_CodPresupuesto][$_CodFuente][$_cod_partida] = $_Ejercicio;
			$_PRESUPUESTO[$_CodPresupuesto][$_CodFuente][$_cod_partida] = $_CodPresupuesto;
			$_CATEGORIA[$_CodPresupuesto][$_CodFuente][$_cod_partida] = $_CategoriaProg;
			$_FUENTE[$_CodPresupuesto][$_CodFuente][$_cod_partida] = $_CodFuente;
	}
	$sql = "SELECT *
			FROM pv_presupuesto
			WHERE CodOrganismo = '$CodOrganismo' AND Ejercicio = '$Ejercicio' AND CategoriaProg = '$_PARAMETRO[IGVCATPROG]'";
	$field_presupuesto = getRecord($sql);

	$sql = "SELECT CodFuente
			FROM pv_presupuestodet
			WHERE CodOrganismo = '$CodOrganismo' AND CodPresupuesto = '$field_presupuesto[CodPresupuesto]' AND cod_partida = '$_cod_partida_igv';";
	$IgvFuente = getVar3($sql);

	$_CUENTA[$_CodCuenta_igv] = $_CodCuenta_igv;
	$_CUENTA20[$_CodCuentaPub20_igv] = $_CodCuentaPub20_igv;
	$_PARTIDA[$_CodPresupuesto][$IgvFuente][$_cod_partida_igv] = $_cod_partida_igv;
	$_PARTIDA_CUENTA[$_CodPresupuesto][$IgvFuente][$_cod_partida_igv] = $_CodCuenta_igv;
	$_PARTIDA_CUENTA20[$_CodPresupuesto][$IgvFuente][$_cod_partida_igv] = $_CodCuentaPub20_igv;
	$_CUENTA_MONTO[$_CodCuenta_igv] = $MontoIGV;
	$_CUENTA_MONTO20[$_CodCuentaPub20_igv] = $MontoIGV;
	$_PARTIDA_MONTO[$_CodPresupuesto][$IgvFuente][$_cod_partida_igv] = $MontoIGV;
		$_EJERCICIO[$_CodPresupuesto][$IgvFuente][$_cod_partida_igv] = $field_presupuesto['Ejercicio'];
		$_PRESUPUESTO[$_CodPresupuesto][$IgvFuente][$_cod_partida_igv] = $field_presupuesto['CodPresupuesto'];
		$_CATEGORIA[$_CodPresupuesto][$IgvFuente][$_cod_partida_igv] = $field_presupuesto['CategoriaProg'];
		$_FUENTE[$_CodPresupuesto][$IgvFuente][$_cod_partida_igv] = $IgvFuente;
	
	//	imprimo cuentas
	foreach ($_CUENTA as $CodCuenta) {
		if ($_CUENTA_MONTO[$CodCuenta] > 0) {
			$Descripcion = getValorCampo("ac_mastplancuenta", "CodCuenta", "Descripcion", $CodCuenta);
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
		if ($_CUENTA_MONTO20[$CodCuentaPub20] > 0) {
			$Descripcion = getValorCampo("ac_mastplancuenta20", "CodCuenta", "Descripcion", $CodCuentaPub20);
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
	//	imprimo partidas
	foreach ($_PARTIDA as $_CodPresupuesto => $Presupuesto) {
		foreach ($Presupuesto as $_CodFuente => $Partida) {
			$Fuente = getValorCampo("pv_fuentefinanciamiento", "CodFuente", "Denominacion", $_CodFuente);
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
			foreach ($Partida as $cod_partida) {
				list($MontoAjustado, $MontoCompromiso, $PreCompromiso, $CotizacionesAsignadas) = disponibilidadPartida2($_EJERCICIO[$_CodPresupuesto][$_CodFuente][$cod_partida], 
																														$CodOrganismo, $cod_partida, 
																														$_PRESUPUESTO[$_CodPresupuesto][$_CodFuente][$cod_partida], 
																														$_FUENTE[$_CodPresupuesto][$_CodFuente][$cod_partida]);
				$MontoPendiente = $PreCompromiso + $CotizacionesAsignadas;
				if ($Estado == 'PR') {
					$MontoPendiente -= $_PARTIDA_MONTO[$_CodPresupuesto][$_CodFuente][$cod_partida];
				}
				$MontoDisponible = $MontoAjustado - $MontoCompromiso;
				$MontoDisponibleReal = $MontoAjustado - ($MontoCompromiso + $MontoPendiente);
				##	valido
				if (($MontoDisponible - $_PARTIDA_MONTO[$_CodPresupuesto][$_CodFuente][$cod_partida]) <= 0) $style = "style='background-color:#F8637D;'";
				elseif(($MontoDisponibleReal - $_PARTIDA_MONTO[$_CodPresupuesto][$_CodFuente][$cod_partida]) <= 0) $style = "style='background-color:#FFC;'";
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

//	--------------------------
elseif($accion == "orden_compra_modificar") {
	list($Anio, $CodOrganismo, $NroOrden) = split("[.]", $codigo);
	$sql = "SELECT Estado
			FROM lg_ordencompra
			WHERE
				Anio = '".$Anio."' AND
				CodOrganismo = '".$CodOrganismo."' AND
				NroOrden = '".$NroOrden."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	if ($field['Estado'] != "PR") die("Solo se pueden modificar las ordenes <strong>En Preparación</strong>");
}

//	--------------------------
elseif($accion == "orden_compra_anular") {
	list($Anio, $CodOrganismo, $NroOrden) = split("[.]", $codigo);
	//	verifico requerimiento
	$sql = "SELECT Estado
			FROM lg_ordencompra
			WHERE
				Anio = '".$Anio."' AND
				CodOrganismo = '".$CodOrganismo."' AND
				NroOrden = '".$NroOrden."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	if ($field['Estado'] == "CO") die("No se pueden anular ordenes <strong>Completadas</strong>");
	elseif($field['Estado'] == "CE") die("No se pueden anular ordenes <strong>Cerradas</strong>");
	elseif($field['Estado'] == "RE") die("No se pueden anular ordenes <strong>Rechazadas</strong>");
	elseif($field['Estado'] == "AN") die("La orden ya se encuentra <strong>Anulada</strong>");
	
	//	verifico los detalles
	$sql = "SELECT Estado
			FROM lg_ordencompradetalle
			WHERE
				Anio = '".$Anio."' AND
				CodOrganismo = '".$CodOrganismo."' AND
				NroOrden = '".$NroOrden."' AND
				Estado = 'CO'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) die("No se pueden anular ordenes con lineas en Estado <strong>Completada</strong>");
	
	//	verifico los detalles
	$sql = "SELECT Estado
			FROM lg_ordencompradetalle
			WHERE
				Anio = '".$Anio."' AND
				CodOrganismo = '".$CodOrganismo."' AND
				NroOrden = '".$NroOrden."' AND
				Estado = 'CE'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) die("No se pueden anular ordenes con lineas en Estado <strong>Cerrada</strong>");
}

//	--------------------------
elseif($accion == "orden_compra_cerrar") {
	list($Anio, $CodOrganismo, $NroOrden) = split("[.]", $codigo);
	//	verifico requerimiento
	$sql = "SELECT Estado
			FROM lg_ordencompra
			WHERE
				Anio = '".$Anio."' AND
				CodOrganismo = '".$CodOrganismo."' AND
				NroOrden = '".$NroOrden."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	if ($field['Estado'] == "CO") die("No se pueden cerrar ordenes <strong>Completadas</strong>");
	elseif($field['Estado'] == "AN") die("No se pueden cerrar ordenes <strong>Anuladas</strong>");
	elseif($field['Estado'] == "RE") die("No se pueden cerrar ordenes <strong>Rechazadas</strong>");
	elseif($field['Estado'] == "CE") die("La orden ya se encuentra <strong>Cerrada</strong>");
	
	//	verifico los detalles
	$sql = "SELECT Estado
			FROM lg_ordencompradetalle
			WHERE
				Anio = '".$Anio."' AND
				CodOrganismo = '".$CodOrganismo."' AND
				NroOrden = '".$NroOrden."' AND
				Estado = 'CO'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) die("No se pueden cerrar ordenes con lineas en Estado <strong>Completada</strong>");
}

//	--------------------------
elseif($accion == "orden_servicio_modificar") {
	list($Anio, $CodOrganismo, $NroOrden) = split("[.]", $codigo);
	$sql = "SELECT Estado
			FROM lg_ordenservicio
			WHERE
				Anio = '".$Anio."' AND
				CodOrganismo = '".$CodOrganismo."' AND
				NroOrden = '".$NroOrden."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	if ($field['Estado'] != "PR") die("Solo se pueden modificar las ordenes <strong>En Preparación</strong>");
}

//	--------------------------
elseif($accion == "orden_servicio_anular") {
	list($Anio, $CodOrganismo, $NroOrden) = split("[.]", $codigo);
	//	verifico requerimiento
	$sql = "SELECT Estado
			FROM lg_ordenservicio
			WHERE
				Anio = '".$Anio."' AND
				CodOrganismo = '".$CodOrganismo."' AND
				NroOrden = '".$NroOrden."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	if ($field['Estado'] == "CO") die("No se pueden anular ordenes <strong>Completadas</strong>");
	elseif($field['Estado'] == "CE") die("No se pueden anular ordenes <strong>Cerradas</strong>");
	elseif($field['Estado'] == "RE") die("No se pueden anular ordenes <strong>Rechazadas</strong>");
	elseif($field['Estado'] == "AN") die("La orden ya se encuentra <strong>Anulada</strong>");
	
	//	verifico los detalles
	$sql = "SELECT FlagTerminado
			FROM lg_ordenserviciodetalle
			WHERE
				Anio = '".$Anio."' AND
				CodOrganismo = '".$CodOrganismo."' AND
				NroOrden = '".$NroOrden."' AND
				FlagTerminado = 'S'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) die("No se pueden anular ordenes con lineas en Estado <strong>Completada</strong>");
}

//	--------------------------
elseif($accion == "orden_servicio_cerrar") {
	list($Anio, $CodOrganismo, $NroOrden) = split("[.]", $codigo);
	//	verifico requerimiento
	$sql = "SELECT Estado
			FROM lg_ordenservicio
			WHERE
				Anio = '".$Anio."' AND
				CodOrganismo = '".$CodOrganismo."' AND
				NroOrden = '".$NroOrden."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	if ($field['Estado'] == "CO") die("No se pueden cerrar ordenes <strong>Completadas</strong>");
	elseif($field['Estado'] == "AN") die("No se pueden cerrar ordenes <strong>Anuladas</strong>");
	elseif($field['Estado'] == "RE") die("No se pueden cerrar ordenes <strong>Rechazadas</strong>");
	elseif($field['Estado'] == "CE") die("La orden ya se encuentra <strong>Cerrada</strong>");
	
	//	verifico los detalles
	$sql = "SELECT FlagTerminado
			FROM lg_ordenserviciodetalle
			WHERE
				Anio = '".$Anio."' AND
				CodOrganismo = '".$CodOrganismo."' AND
				NroOrden = '".$NroOrden."' AND
				FlagTerminado = 'S'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) die("No se pueden cerrar ordenes con lineas en Estado <strong>Completada</strong>");
}

//	--------------------------
elseif($accion == "almacen_despacho_detalles") {
	//	verifico requerimiento
	$sql = "SELECT
				rd.*,
				o.Organismo,
				r.Clasificacion,
				i.CodInterno,
				iai.StockActual,
				(rd.CantidadPedida - rd.CantidadRecibida) AS CantidadPendiente,
				(SELECT SUM(CantidadPedida - CantidadRecibida) 
				 FROM lg_ordencompradetalle 
				 WHERE CodItem = rd.CodItem AND Estado = 'PE'
				 GROUP BY CodItem) AS EnTransito
			FROM
				lg_requerimientosdet rd
				INNER JOIN mastorganismos o ON (rd.CodOrganismo = o.CodOrganismo)
				INNER JOIN lg_requerimientos r ON (rd.CodRequerimiento = r.CodRequerimiento)
				INNER JOIN lg_itemmast i ON (rd.CodItem = i.CodItem)
				LEFT JOIN lg_itemalmaceninv iai ON (r.CodAlmacen = iai.CodAlmacen AND rd.CodItem = iai.CodItem)
			WHERE
				rd.CodRequerimiento = '".$CodRequerimiento."' AND
				rd.Estado = 'PE' AND
				rd.FlagCompraAlmacen <> 'C'
			ORDER BY CodRequerimiento, CodItem, CommoditySub";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	while ($field = mysql_fetch_array($query)) {
		$id = "$field[CodRequerimiento].$field[Secuencia]";
		?>
        <tr class="trListaBody" onclick="mClkMulti(this);" id="row_<?=$id?>">
			<td align="center">
               	<input type="checkbox" name="Secuencia" id="<?=$id?>" value="<?=$id?>" style="display:none" />
                <input type="hidden" name="CodItem" value="<?=$field['CodItem']?>" />
                <input type="hidden" name="CodInterno" value="<?=$field['CodInterno']?>" />
                <input type="hidden" name="Descripcion" value="<?=$field['Descripcion']?>" />
                <input type="hidden" name="CodUnidad" value="<?=$field['CodUnidad']?>" />
                <input type="hidden" name="CantidadPedida" value="<?=$field['CantidadPedida']?>" />
                <input type="hidden" name="CantidadPendiente" value="<?=$field['CantidadPendiente']?>" />
                <input type="hidden" name="StockActual" value="<?=$field['StockActual']?>" />
                <input type="hidden" name="CodCentroCosto" value="<?=$field['CodCentroCosto']?>" />
                <input type="hidden" name="EnTransito" value="<?=$field['EnTransito']?>" />
                <input type="hidden" name="FlagCompraAlmacen" value="<?=$field['FlagCompraAlmacen']?>" />
				<?=$field['CodItem']?>
            </td>
			<td align="center">
				<?=$field['CodInterno']?>
            </td>
			<td>
				<?=$field['Descripcion']?>
            </td>
			<td align="center">
				<?=$field['CodUnidad']?>
            </td>
			<td align="right">
				<?=number_format($field['CantidadPedida'], 2, ',', '.')?>
            </td>
			<td align="right">
				<?=number_format($field['CantidadPendiente'], 2, ',', '.')?>
            </td>
			<td align="right">
				<?=number_format($field['StockActual'], 2, ',', '.')?>
            </td>
			<td align="center">
				<?=$field['CodCentroCosto']?>
            </td>
			<td align="right">
				<?=number_format($field['EnTransito'], 2, ',', '.')?>
            </td>
			<td align="center">
				<?=printFlag2($field['FlagCompraAlmacen'])?>
            </td>
		</tr>
        <?php
	}
}

//	--------------------------
elseif($accion == "almacen_recepcion_detalles") {
	//	consulto detalles
	list($Anio, $CodOrganismo, $NroOrden) = split("[.]", $registro);
	$sql = "SELECT
				ocd.*,
				(ocd.CantidadRec - ocd.CantidadRecibida) AS CantidadPendiente,
				(ocd.PrecioUnit / (ocd.CantidadRec / ocd.CantidadPedida)) AS PrecioUnitRec,
				iai.StockActual
			FROM
				lg_ordencompradetalle ocd
				INNER JOIN lg_ordencompra oc ON (ocd.CodOrganismo = oc.CodOrganismo AND ocd.NroOrden = oc.NroOrden AND ocd.Anio = oc.Anio)
				LEFT JOIN lg_itemalmaceninv iai ON (ocd.CodItem = iai.CodItem AND oc.CodAlmacen = iai.CodAlmacen)
			WHERE
				ocd.Anio = '".$Anio."' AND
				ocd.CodOrganismo = '".$CodOrganismo."' AND
				ocd.NroOrden = '".$NroOrden."' AND
				ocd.Estado = 'PE'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	while ($field = mysql_fetch_array($query)) {
		$id = "$field[Anio].$field[CodOrganismo].$field[NroOrden].$field[Secuencia]";
		?>
        <tr class="trListaBody" onclick="mClkMulti(this);" id="row_<?=$id?>">
			<td align="center">
               	<input type="checkbox" name="Secuencia" id="<?=$id?>" value="<?=$id?>" style="display:none" />
                <input type="hidden" name="CodItem" value="<?=$field['CodItem']?>" />
                <input type="hidden" name="Descripcion" value="<?=$field['Descripcion']?>" />
                <input type="hidden" name="CodUnidad" value="<?=$field['CodUnidadRec']?>" />
                <input type="hidden" name="StockActual" value="<?=$field['StockActual']?>" />
                <input type="hidden" name="CantidadPedida" value="<?=$field['CantidadRec']?>" />
                <input type="hidden" name="CantidadCompra" value="<?=$field['CantidadPedida']?>" />
                <input type="hidden" name="CantidadRecibida" value="<?=$field['CantidadRecibida']?>" />
                <input type="hidden" name="CantidadPendiente" value="<?=$field['CantidadPendiente']?>" />
                <input type="hidden" name="PrecioUnit" value="<?=$field['PrecioUnitRec']?>" />
                <input type="hidden" name="FlagExonerado" value="<?=$field['FlagExonerado']?>" />
                <input type="hidden" name="CodUnidadCompra" value="<?=$field['CodUnidad']?>" />
                <input type="hidden" name="PrecioUnitCompra" value="<?=$field['PrecioUnit']?>" />
                <input type="hidden" name="CodCentroCosto" value="<?=$field['CodCentroCosto']?>" />
				<?=$field['CodItem']?>
            </td>
			<td>
				<?=$field['Descripcion']?>
            </td>
			<td align="center">
				<?=$field['CodUnidad']?>
            </td>
			<td align="right">
				<?=number_format($field['StockActual'], 2, ',', '.')?>
            </td>
			<td align="right">
				<?=number_format($field['CantidadRec'], 2, ',', '.')?>
            </td>
			<td align="right">
				<?=number_format($field['CantidadRecibida'], 2, ',', '.')?>
            </td>
			<td align="right">
				<strong><?=number_format($field['CantidadPendiente'], 2, ',', '.')?></strong>
            </td>
			<td align="right">
				<?=number_format($field['PrecioUnitRec'], 2, ',', '.')?>
            </td>
			<td align="center">
				<?=$field['CodCentroCosto']?>
            </td>
		</tr>
        <?php
	}
}

//	--------------------------
elseif($accion == "transaccion_commodity_recepcion_detalles") {
	//	consulto detalles
	list($Anio, $CodOrganismo, $NroOrden) = split("[.]", $registro);
	$sql = "SELECT
				ocd.*,
				(ocd.CantidadRec - ocd.CantidadRecibida) AS CantidadPendiente,
				(ocd.PrecioUnit / (ocd.CantidadRec / ocd.CantidadPedida)) AS PrecioUnitRec,
				cs.CodClasificacion,
				cm.Clasificacion
			FROM
				lg_ordencompradetalle ocd
				INNER JOIN lg_ordencompra oc ON (ocd.CodOrganismo = oc.CodOrganismo AND ocd.NroOrden = oc.NroOrden AND ocd.Anio = oc.Anio)
				INNER JOIN lg_commoditysub cs ON (cs.Codigo = ocd.CommoditySub)
				INNER JOIN lg_commoditymast cm ON (cs.CommodityMast = cm.CommodityMast)
			WHERE
				ocd.Anio = '".$Anio."' AND
				ocd.CodOrganismo = '".$CodOrganismo."' AND
				ocd.NroOrden = '".$NroOrden."' AND
				ocd.Estado = 'PE'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	while ($field = mysql_fetch_array($query)) {
		$id = "$field[Anio].$field[CodOrganismo].$field[NroOrden].$field[Secuencia]";
		?>
        <tr class="trListaBody" onclick="mClkMulti(this);" id="row_<?=$id?>">
			<td align="center">
               	<input type="checkbox" name="Secuencia" id="<?=$id?>" value="<?=$id?>" style="display:none" />
                <input type="hidden" name="CommoditySub" value="<?=$field['CommoditySub']?>" />
                <input type="hidden" name="Descripcion" value="<?=$field['Descripcion']?>" />
                <input type="hidden" name="CodUnidad" value="<?=$field['CodUnidadRec']?>" />
                <input type="hidden" name="CantidadPedida" value="<?=$field['CantidadRec']?>" />
                <input type="hidden" name="CantidadRecibida" value="<?=$field['CantidadRecibida']?>" />
                <input type="hidden" name="CantidadPendiente" value="<?=$field['CantidadPendiente']?>" />
                <input type="hidden" name="PrecioUnit" value="<?=$field['PrecioUnitRec']?>" />
                <input type="hidden" name="FlagExonerado" value="<?=$field['FlagExonerado']?>" />
                <input type="hidden" name="CodCentroCosto" value="<?=$field['CodCentroCosto']?>" />
                <input type="hidden" name="CodClasificacion" value="<?=$field['CodClasificacion']?>" />
                <input type="hidden" name="CantidadCompra" value="<?=$field['CantidadPedida']?>" />
                <input type="hidden" name="CodUnidadCompra" value="<?=$field['CodUnidad']?>" />
                <input type="hidden" name="PrecioUnitCompra" value="<?=$field['PrecioUnit']?>" />
                <input type="hidden" name="Clasificacion" value="<?=$field['Clasificacion']?>" />
				<?=$field['CommoditySub']?>
            </td>
			<td>
				<?=$field['Descripcion']?>
            </td>
			<td align="center">
				<?=$field['CodUnidadRec']?>
            </td>
			<td align="right">
				<?=number_format($field['CantidadRec'], 2, ',', '.')?>
            </td>
			<td align="right">
				<?=number_format($field['CantidadRecibida'], 2, ',', '.')?>
            </td>
			<td align="right">
				<strong><?=number_format($field['CantidadPendiente'], 2, ',', '.')?></strong>
            </td>
			<td align="right">
				<?=number_format($field['PrecioUnitRec'], 2, ',', '.')?>
            </td>
			<td align="center">
				<?=$field['CodCentroCosto']?>
            </td>
		</tr>
        <?php
	}
}

//	--------------------------
elseif($accion == "transaccion_commodity_despacho_detalles") {
	list($Anio, $CodOrganismo, $NroOrden) = split("[.]", $registro);
	//	consulto detalles
	$sql = "SELECT
				r.CodInterno,
				rd.CodRequerimiento,
				rd.Secuencia,
				rd.CommoditySub,
				rd.Descripcion,
				rd.CodUnidad,
				rd.CantidadPedida,
				rd.CantidadRecibida,
				(rd.CantidadPedida - rd.CantidadRecibida) AS CantidadPendiente,
				ocd.CantidadRecibida AS StockActual,
				rd.CodCentroCosto
			FROM
				lg_ordencompra oc
				INNER JOIN lg_ordencompradetalle ocd ON (oc.Anio = ocd.Anio AND
														 oc.CodOrganismo = ocd.CodOrganismo AND
														 oc.NroOrden = ocd.NroOrden)
				INNER JOIN lg_requerimientosdet rd ON (rd.Anio = ocd.Anio AND
													   rd.CodOrganismo = ocd.CodOrganismo AND
													   rd.NroOrden = ocd.NroOrden AND
													   rd.OrdenSecuencia = ocd.Secuencia)
				INNER JOIN lg_requerimientos r ON (r.CodRequerimiento = rd.CodRequerimiento)
			WHERE
				oc.Anio = '".$Anio."' AND
				oc.CodOrganismo = '".$CodOrganismo."' AND
				oc.NroOrden = '".$NroOrden."' AND
				rd.CantidadPedida > rd.CantidadRecibida AND
				r.Clasificacion <> 'SER'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	while ($field = mysql_fetch_array($query)) {
		$id = "$field[CodRequerimiento].$field[Secuencia]";
		?>
        <tr class="trListaBody" onclick="mClkMulti(this);" id="row_<?=$id?>">
			<td align="center">
               	<input type="checkbox" name="Secuencia" id="<?=$id?>" value="<?=$id?>" style="display:none" />
                <input type="hidden" name="CommoditySub" value="<?=$field['CommoditySub']?>" />
                <input type="hidden" name="Descripcion" value="<?=$field['Descripcion']?>" />
                <input type="hidden" name="CodUnidad" value="<?=$field['CodUnidad']?>" />
                <input type="hidden" name="CantidadPedida" value="<?=$field['CantidadPedida']?>" />
                <input type="hidden" name="CantidadPendiente" value="<?=$field['CantidadPendiente']?>" />
                <input type="hidden" name="StockActual" value="<?=$field['StockActual']?>" />
                <input type="hidden" name="CodCentroCosto" value="<?=$field['CodCentroCosto']?>" />
                <input type="hidden" name="CodInterno" value="<?=$field['CodInterno']?>" />
                <input type="hidden" name="CodRequerimiento" value="<?=$field['CodRequerimiento']?>" />
				<?=$field['CodInterno']?>
            </td>
			<td align="center">
				<?=$field['Secuencia']?>
            </td>
			<td align="center">
				<?=$field['CommoditySub']?>
            </td>
			<td>
				<?=$field['Descripcion']?>
            </td>
			<td align="center">
				<?=$field['CodUnidad']?>
            </td>
			<td align="right">
				<?=number_format($field['CantidadPedida'], 2, ',', '.')?>
            </td>
			<td align="right">
				<?=number_format($field['CantidadRecibida'], 2, ',', '.')?>
            </td>
			<td align="right">
				<strong><?=number_format($field['CantidadPendiente'], 2, ',', '.')?></strong>
            </td>
			<td align="center">
				<?=$field['CodCentroCosto']?>
            </td>
		</tr>
        <?php
	}
}

//	--------------------------
elseif($accion == "periodoAbierto") {
	$Periodo = substr(ahora(), 0, 7);
	if (!periodoAbierto($CodOrganismo, $Periodo)) die("No se puede generar ninguna transacci&oacute;n porque no se ha abierto el periodo <strong>$Periodo</strong>.");
}

//	--------------------------
elseif($accion == "transaccion_almacen_modificar") {
	list($CodOrganismo, $CodDocumento, $NroDocumento) = split("[.]", $codigo);
	$sql = "SELECT Estado
			FROM lg_transaccion
			WHERE
				CodOrganismo = '".$CodOrganismo."' AND
				CodDocumento = '".$CodDocumento."' AND
				NroDocumento = '".$NroDocumento."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	if ($field['Estado'] != "PR") die("Solo se pueden modificar las transacciones <strong>Pendientes</strong>");
}

//	--------------------------
elseif($accion == "transaccion_almacen_reversa") {
	list($CodOrganismo, $CodDocumento, $NroDocumento) = split("[.]", $codigo);
	//	consulto estado
	$sql = "SELECT Estado, CodTransaccion
			FROM lg_transaccion
			WHERE
				CodOrganismo = '".$CodOrganismo."' AND
				CodDocumento = '".$CodDocumento."' AND
				NroDocumento = '".$NroDocumento."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	if ($field['Estado'] != "CO") die("Solo se pueden ejecutar las reversas en las transacciones <strong>Ejecutadas</strong>");
	elseif($field['CodTransaccion'] == $_PARAMETRO["TRANSANULREQ"]) die("No se puede ejecutar la reversa para el tipo de transaccion <strong>(".$field['CodTransaccion'].")</strong>");
	elseif($field['CodTransaccion'] == $_PARAMETRO["TRANSANULREC"]) die("No se puede ejecutar la reversa para el tipo de transaccion <strong>(".$field['CodTransaccion'].")</strong>");
	elseif($field['CodTransaccion'] == $_PARAMETRO["TRANSANULIN"]) die("No se puede ejecutar la reversa para el tipo de transaccion <strong>(".$field['CodTransaccion'].")</strong>");
	elseif($field['CodTransaccion'] == $_PARAMETRO["TRANSANULEG"]) die("No se puede ejecutar la reversa para el tipo de transaccion <strong>(".$field['CodTransaccion'].")</strong>");
}

//	--------------------------
elseif($accion == "transaccion_commodity_modificar") {
	list($CodOrganismo, $CodDocumento, $NroDocumento) = split("[.]", $codigo);
	$sql = "SELECT Estado
			FROM lg_commoditytransaccion
			WHERE
				CodOrganismo = '".$CodOrganismo."' AND
				CodDocumento = '".$CodDocumento."' AND
				NroDocumento = '".$NroDocumento."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	if ($field['Estado'] != "PR") die("Solo se pueden modificar las transacciones <strong>Pendientes</strong>");
}

//	--------------------------
elseif($accion == "transaccion_commodity_anular") {
	list($CodOrganismo, $CodDocumento, $NroDocumento) = split("[.]", $codigo);
	$sql = "SELECT Estado
			FROM lg_commoditytransaccion
			WHERE
				CodOrganismo = '".$CodOrganismo."' AND
				CodDocumento = '".$CodDocumento."' AND
				NroDocumento = '".$NroDocumento."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	if ($field['Estado'] == "AN") die("No puede Anular esta Transacci&oacute;n");
}

//	--------------------------
elseif($accion == "setActivosFijosAsociados") {
	$nro = 0;
	$detalle = split(";char:tr;", $detalles);
	foreach ($detalle as $linea) {
		list($_CommoditySub, $_Descripcion, $_CantidadPedida, $_CantidadPendiente, $_CantidadRecibida, $_Monto, $_CodClasificacion, $_CodCentroCosto, $_Secuencia) = split(";char:td;", $linea);
		
		//	obtengo el ultimo nro de secuencia ingresada
		$sql = "SELECT MAX(NroSecuencia) As NroSecuencia
				FROM lg_activofijo
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					Anio = '".substr($FechaDocumento, 6, 4)."' AND
					NroOrden = '".$ReferenciaNroDocumento."' AND
					Secuencia = '".$_Secuencia."'";
		$query_secuencia = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		if (mysql_num_rows($query_secuencia) != 0) {
			$field_secuencia = mysql_fetch_array($query_secuencia);
			$_NroSecuencia = $field_secuencia['NroSecuencia'];
		} else $_NroSecuencia = 0;
		
		?>
        <tr class="trListaBody2">
        	<td colspan="13">
            	<?=$_Descripcion?>
            </td>
        </tr>
        <?php
		for ($i=1; $i<=$_CantidadRecibida; $i++) {
			$_NroSecuencia++;
			$nro++;
			$id = "$_CommoditySub.$nro";
			?>
            <tr class="trListaBody" onclick="mClk(this, 'sel_activos');" id="activos_<?=$id?>">
                <th align="center">
                    <input type="hidden" name="Secuencia" value="<?=$_Secuencia?>" />
                    <input type="hidden" name="NroSecuencia" value="<?=$_NroSecuencia?>" />
                    <input type="hidden" name="CommoditySub" value="<?=$_CommoditySub?>" />
                    <input type="hidden" name="Descripcion" value="<?=$_Descripcion?>" />
                    <input type="hidden" name="CodClasificacion" value="<?=$_CodClasificacion?>" />
                    <input type="hidden" name="Monto" value="<?=$_Monto?>" />
                    <?=$i?>
                </th>
                <td align="center">
                	<input type="text" name="NroSerie" class="cell" maxlength="20" />
                </td>
                <td align="center">
                	<input type="text" name="FechaIngreso" maxlength="10" style="text-align:center;" class="datepicker cell" onkeyup="setFechaDMA(this);" />
                </td>
                <td align="center">
                	<input type="text" name="Modelo" class="cell" maxlength="50" />
                </td>
                <td align="center">
                	<input type="text" name="CodBarra" class="cell" maxlength="25" />
                </td>
                <td align="center">
                    <input type="text" name="CodUbicacion" id="CodUbicacion_<?=$id?>" class="cell ubicacion" style="text-align:center;" maxlength="4" value="<?=$CodUbicacion?>" />
                    <input type="hidden" name="NomUbicacion" id="NomUbicacion_<?=$id?>" />
                </td>
                <td align="center">
                    <input type="text" name="CodCentroCosto" id="CodCentroCosto_<?=$id?>" class="cell" style="text-align:center;" maxlength="4" value="<?=$_CodCentroCosto?>" />
                    <input type="hidden" name="NomCentroCosto" id="NomCentroCosto_<?=$id?>" />
                </td>
                <td align="center">
                	<input type="text" name="NroPlaca" class="cell" maxlength="15" />
                </td>
                <td align="center">
                	<select name="CodMarca" class="cell">
                    	<option value="">&nbsp;</option>
                        <?=loadSelect("lg_marcas", "CodMarca", "Descripcion", "", 0)?>
                    </select>
                </td>
                <td align="center">
                	<select name="Color" class="cell">
                    	<option value="">&nbsp;</option>
                        <?=getMiscelaneos("", "COLOR", 0)?>
                    </select>
                </td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="center"><?=printValores("ESTADO-TRANSACCION", "PR")?></td>
            </tr>
            <?php
		}
	}
}

//	--------------------------
elseif($accion == "transaccion_cajachica_recepcion_detalles") {
	list($CodRequerimiento) = split("[.]", $registro);
	//	verifico requerimiento
	$sql = "SELECT
				rd.CodRequerimiento,
				rd.Secuencia,
				rd.CodItem,
				rd.CommoditySub,
				rd.Descripcion,
				rd.CodUnidad,
				rd.CantidadPedida,
				rd.CantidadRecibida,
				rd.CantidadOrdenCompra,
				(rd.CantidadPedida - rd.CantidadOrdenCompra) AS CantidadPendiente,
				rd.CodCentroCosto,
				o.Organismo,
				r.Clasificacion
			FROM
				lg_requerimientosdet rd
				INNER JOIN mastorganismos o ON (rd.CodOrganismo = o.CodOrganismo)
				INNER JOIN lg_requerimientos r ON (rd.CodRequerimiento = r.CodRequerimiento)
			WHERE
				rd.CodRequerimiento = '".$CodRequerimiento."' AND
				rd.Estado = 'PE' AND
				(rd.CantidadPedida - rd.CantidadOrdenCompra) > 0
			ORDER BY CodRequerimiento, CodItem, CommoditySub";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	while ($field = mysql_fetch_array($query)) {
		$id = "$field[CodRequerimiento].$field[Secuencia]";
		if ($field['CodItem'] != "") $Codigo = $field['CodItem']; else $Codigo = $field['CommoditySub'];
		?>
        <tr class="trListaBody" onclick="mClkMulti(this);" id="row_<?=$id?>">
			<td align="center">
               	<input type="checkbox" name="Secuencia" id="<?=$id?>" value="<?=$id?>" style="display:none" />
                <input type="hidden" name="CodItem" value="<?=$field['CodItem']?>" />
                <input type="hidden" name="CommoditySub" value="<?=$field['CommoditySub']?>" />
                <input type="hidden" name="Descripcion" value="<?=$field['Descripcion']?>" />
                <input type="hidden" name="CodUnidad" value="<?=$field['CodUnidad']?>" />
                <input type="hidden" name="CantidadPedida" value="<?=$field['CantidadPedida']?>" />
                <input type="hidden" name="CantidadPendiente" value="<?=$field['CantidadPendiente']?>" />
                <input type="hidden" name="CodCentroCosto" value="<?=$field['CodCentroCosto']?>" />
				<?=$Codigo?>
            </td>
			<td>
				<?=$field['Descripcion']?>
            </td>
			<td align="center">
				<?=$field['CodUnidad']?>
            </td>
			<td align="right">
				<?=number_format($field['CantidadPedida'], 2, ',', '.')?>
            </td>
			<td align="right">
				<?=number_format($field['CantidadPendiente'], 2, ',', '.')?>
            </td>
			<td align="center">
				<?=$field['CodCentroCosto']?>
            </td>
		</tr>
        <?php
	}
}

//	--------------------------
elseif($accion == "transaccion_cajachica_despacho_detalles") {
	list($CodRequerimiento) = split("[.]", $registro);
	//	verifico requerimiento
	$sql = "SELECT
				rd.CodRequerimiento,
				rd.Secuencia,
				rd.CodItem,
				rd.CommoditySub,
				rd.Descripcion,
				rd.CodUnidad,
				rd.CantidadPedida,
				rd.CantidadRecibida,
				rd.CantidadOrdenCompra,
				(rd.CantidadOrdenCompra - rd.CantidadRecibida) AS CantidadPendiente,
				rd.CodCentroCosto,
				o.Organismo,
				r.Clasificacion
			FROM
				lg_requerimientosdet rd
				INNER JOIN mastorganismos o ON (rd.CodOrganismo = o.CodOrganismo)
				INNER JOIN lg_requerimientos r ON (rd.CodRequerimiento = r.CodRequerimiento)
			WHERE
				rd.CodRequerimiento = '".$CodRequerimiento."' AND
				rd.Estado = 'PE' AND
				rd.CantidadOrdenCompra > rd.CantidadRecibida
			ORDER BY CodRequerimiento, CodItem, CommoditySub";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	while ($field = mysql_fetch_array($query)) {
		$id = "$field[CodRequerimiento].$field[Secuencia]";
		if ($field['CodItem'] != "") $Codigo = $field['CodItem']; else $Codigo = $field['CommoditySub'];
		?>
        <tr class="trListaBody" onclick="mClkMulti(this);" id="row_<?=$id?>">
			<td align="center">
               	<input type="checkbox" name="Secuencia" id="<?=$id?>" value="<?=$id?>" style="display:none" />
                <input type="hidden" name="CodItem" value="<?=$field['CodItem']?>" />
                <input type="hidden" name="CommoditySub" value="<?=$field['CommoditySub']?>" />
                <input type="hidden" name="Descripcion" value="<?=$field['Descripcion']?>" />
                <input type="hidden" name="CodUnidad" value="<?=$field['CodUnidad']?>" />
                <input type="hidden" name="CantidadPedida" value="<?=$field['CantidadPedida']?>" />
                <input type="hidden" name="CantidadPendiente" value="<?=$field['CantidadPendiente']?>" />
                <input type="hidden" name="CodCentroCosto" value="<?=$field['CodCentroCosto']?>" />
				<?=$Codigo?>
            </td>
			<td>
				<?=$field['Descripcion']?>
            </td>
			<td align="center">
				<?=$field['CodUnidad']?>
            </td>
			<td align="right">
				<?=number_format($field['CantidadPedida'], 2, ',', '.')?>
            </td>
			<td align="right">
				<?=number_format($field['CantidadPendiente'], 2, ',', '.')?>
            </td>
			<td align="center">
				<?=$field['CodCentroCosto']?>
            </td>
		</tr>
        <?php
	}
}

//	--------------------------
elseif ($accion == "cotizaciones_proveedores_invitar_detalles") {
	$sql = "SELECT
				rd.CodItem,
				rd.CommoditySub,
				rd.Descripcion,
				rd.CodUnidad,
				rd.Secuencia,
				rd.CodCentroCosto,				
				c.Cantidad,
				c.PrecioUnit,
				c.PrecioUnitIva,
				c.PrecioCantidad,
				c.Total,
				c.FlagExonerado,
				r.CodInterno,
				r.FechaRequerida,
				r.Comentarios
			FROM
				lg_cotizacion c
				INNER JOIN lg_requerimientosdet rd ON (c.CodRequerimiento = rd.CodRequerimiento AND c.Secuencia = rd.Secuencia)
				INNER JOIN lg_requerimientos r ON (rd.CodRequerimiento = r.CodRequerimiento)
			WHERE c.NroCotizacionProv = '".$NroCotizacionProv."'";
	$query = mysql_query($sql) or die($sql.mysql_error());
	while ($field = mysql_fetch_array($query)) {
		if ($field['CodItem'] != "") $Codigo = $field['CodItem']; else $Codigo = $field['CommoditySub'];
		if (strlen($field['Comentarios']) > 200) $Comentarios = substr($field['Comentarios'], 0, 200)."...";
		else $Comentarios = $field['Comentarios'];
		?>
        <tr class="trListaBody">
            <td align="center"><?=$Codigo?></td>
            <td><?=htmlentities($field['Descripcion'])?></td>
            <td align="center"><?=$field['CodUnidad']?></td>
            <td align="right"><?=number_format($field['Cantidad'], 2, ',', '.')?></td>
            <td align="right"><?=number_format($field['PrecioUnit'], 2, ',', '.')?></td>
            <td align="right"><?=number_format($field['PrecioUnitIva'], 2, ',', '.')?></td>
            <td align="right"><?=number_format($field['PrecioCantidad'], 2, ',', '.')?></td>
            <td align="right"><?=number_format($field['Total'], 2, ',', '.')?></td>
            <td align="center"><?=printFlag($field['FlagExonerado'])?></td>
            <td align="center"><?=$field['CodInterno']?></td>
            <td align="center"><?=$field['Secuencia']?></td>
            <td align="center"><?=formatFechaDMA($field['FechaRequerida'])?></td>
            <td align="center"><?=$field['CodCentroCosto']?></td>
			<td title="<?=$field['Comentarios']?>"><?=htmlentities($Comentarios)?></td>
		</tr>
		<?php
	}
}

elseif ($accion == "commodity_eliminar_validar") {
	$requerimientos = getNumRows2("lg_requerimientosdet", array("CommoditySub"), array($Codigo));
	$compras = getNumRows2("lg_ordencompradetalle", array("CommoditySub"), array($Codigo));
	$transacciones = getNumRows2("lg_commoditytransacciondetalle", array("CommoditySub"), array($Codigo));
	if ($requerimientos > 0 || $compras > 0 || $transacciones > 0) die("No se puede eliminar este registro.<br />Existen movimientos de este commodity.");
}
?>