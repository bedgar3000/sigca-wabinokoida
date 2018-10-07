<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion-".$AnioActual.$MesActual.$DiaActual."_".$HorasActual.$MinutosActual.$SegundosActual.".sql", "w+");
//	$__archivo = fopen("_"."$modulo-$accion.sql", "w+");
///////////////////////////////////////////////////////////////////////////////
//	VIATICOS (NUEVO, MODIFICAR, REVISAR, ANULAR)
///////////////////////////////////////////////////////////////////////////////
if ($modulo == "viaticos") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-----------------
		##	
		$CodCargoReal = getVar3("SELECT CodCargo FROM mastempleado WHERE CodPersona = '".$CodPersona."'");
		if ($CodCargoReal <> $CodCargo) $FlagCargoEncargado = 'S'; else $FlagCargoEncargado = 'N';
		##	inserto
		$CodViatico = codigo("ap_viaticos", "CodViatico", 6, array('CodOrganismo'), array($CodOrganismo));
		$CodInterno = codigo("ap_viaticos", "CodInterno", 6, array('Anio'), array($Anio));
		$sql = "INSERT INTO ap_viaticos
				SET
					CodOrganismo = '".$CodOrganismo."',
					CodViatico = '".$CodViatico."',
					Anio = '".$Anio."',
					CodInterno = '".$CodInterno."',
					Periodo = '".$Periodo."',
					CodPersona = '".$CodPersona."',
					CodCargo = '".$CodCargo."',
					DescripCargo = '".changeUrl($DescripCargo)."',
					CodDependencia = '".$CodDependencia."',
					NomDependencia = '".changeUrl($NomDependencia)."',
					CodCentroCosto = '".$CodCentroCosto."',
					DescripcionGral = '".changeUrl($DescripcionGral)."',
					Motivo = '".changeUrl($Motivo)."',
					Monto = '".setNumero($Monto)."',
					Fecha = '".formatFechaAMD($Fecha)."',
					PreparadoPor = '".$PreparadoPor."',
					FechaPreparado = '".formatFechaAMD($FechaPreparado)."',
					CodViaticoRelacion = '".$CodViaticoRelacion."',
					FlagPersonaExterna = '".$FlagPersonaExterna."',
					-- FlagCargoEncargado = '".$FlagCargoEncargado."',
					CodPresupuesto = '".$CodPresupuesto."',
					CodFuente = '".$CodFuente."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		##	detalles
		$_Secuencia = 0;
		$lineas = explode("||", $detalles);
		foreach($lineas as $columnas) {
			list($_cod_partida, $_CodCuenta, $_CodCuentaPub20, $_CodPresupuesto, $_CodFuente, $_CodConcepto, $_Descripcion, $_ValorUT, $_UnidadTributaria, $_MontoViatico, $_CantidadDias, $_MontoTotal) = explode("|", $columnas);
			##	concepto
			$field_concepto = getRecord("SELECT * FROM ap_conceptogastoviatico WHERE CodConcepto = '".$_CodConcepto."'");
			##	inserto
			$sql = "INSERT INTO ap_viaticosdetalle
					SET
						CodOrganismo = '".$CodOrganismo."',
						CodViatico = '".$CodViatico."',
						Secuencia = '".++$_Secuencia."',
						CodConcepto = '".$_CodConcepto."',
						Descripcion = '".changeUrl($_Descripcion)."',
						Articulo = '".$field_concepto['Articulo']."',
						Numeral = '".$field_concepto['Numeral']."',
						Categoria = '".$field_concepto['Categoria']."',
						ValorUT = '".$_ValorUT."',
						UnidadTributaria = '".$_UnidadTributaria."',
						MontoViatico = '".$_MontoViatico."',
						CantidadDias = '".$_CantidadDias."',
						MontoTotal = '".$_MontoTotal."',
						cod_partida = '".$_cod_partida."',
						CodCuenta = '".$_CodCuenta."',
						CodCuentaPub20 = '".$_CodCuentaPub20."',
						CodPresupuesto = '".$_CodPresupuesto."',
						CodFuente = '".$_CodFuente."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	distribucion
		$_Secuencia = 0;
		$sql = "SELECT
					cod_partida,
					CodCuenta,
					CodCuentaPub20,
					CodPresupuesto,
					CodFuente,
					SUM(MontoTotal) AS Monto
				FROM ap_viaticosdetalle
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					CodViatico = '".$CodViatico."'
				GROUP BY CodPresupuesto, CodFuente, cod_partida";
		$field_distribucion = getRecords($sql);
		foreach($field_distribucion as $f) {
			$sql = "INSERT INTO ap_viaticosdistribucion
					SET
						CodOrganismo = '".$CodOrganismo."',
						CodViatico = '".$CodViatico."',
						Secuencia = '".++$_Secuencia."',
						Monto = '".$f['Monto']."',
						cod_partida = '".$f['cod_partida']."',
						CodCuenta = '".$f['CodCuenta']."',
						CodCuentaPub20 = '".$f['CodCuentaPub20']."',
						CodPresupuesto = '".$f['CodPresupuesto']."',
						CodFuente = '".$f['CodFuente']."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		//	-----------------
		mysql_query("COMMIT");
	}
	//	modificar
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		//	-----------------
		if (substr($Periodo, 0, 4) != $Anio) die("No se puede cambiar el a&ntilde;o del vi&aacute;tico");
		##	
		$CodCargoReal = getVar3("SELECT CodCargo FROM mastempleado WHERE CodPersona = '".$CodPersona."'");
		if ($CodCargoReal <> $CodCargo) $FlagCargoEncargado = 'S'; else $FlagCargoEncargado = 'N';
		##	inserto
		$sql = "UPDATE ap_viaticos
				SET
					Periodo = '".$Periodo."',
					DescripcionGral = '".changeUrl($DescripcionGral)."',
					Motivo = '".changeUrl($Motivo)."',
					Monto = '".setNumero($Monto)."',
					Fecha = '".formatFechaAMD($Fecha)."',
					-- FlagCargoEncargado = '".$FlagCargoEncargado."',
						CodPresupuesto = '".$CodPresupuesto."',
						CodFuente = '".$CodFuente."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					CodViatico = '".$CodViatico."'";
		execute($sql);
		##	detalles
		$sql = "DELETE FROM ap_viaticosdetalle 
				WHERE 
					CodOrganismo = '".$CodOrganismo."' AND 
					CodViatico = '".$CodViatico."'";
		execute($sql);
		$_Secuencia = 0;
		$lineas = explode("||", $detalles);
		foreach($lineas as $columnas) {
			list($_cod_partida, $_CodCuenta, $_CodCuentaPub20, $_CodPresupuesto, $_CodFuente, $_CodConcepto, $_Descripcion, $_ValorUT, $_UnidadTributaria, $_MontoViatico, $_CantidadDias, $_MontoTotal) = explode("|", $columnas);
			##	concepto
			$field_concepto = getRecord("SELECT * FROM ap_conceptogastoviatico WHERE CodConcepto = '".$_CodConcepto."'");
			##	inserto
			$sql = "INSERT INTO ap_viaticosdetalle
					SET
						CodOrganismo = '".$CodOrganismo."',
						CodViatico = '".$CodViatico."',
						Secuencia = '".++$_Secuencia."',
						CodConcepto = '".$_CodConcepto."',
						Descripcion = '".changeUrl($_Descripcion)."',
						Articulo = '".$field_concepto['Articulo']."',
						Numeral = '".$field_concepto['Numeral']."',
						Categoria = '".$field_concepto['Categoria']."',
						ValorUT = '".$_ValorUT."',
						UnidadTributaria = '".$_UnidadTributaria."',
						MontoViatico = '".$_MontoViatico."',
						CantidadDias = '".$_CantidadDias."',
						MontoTotal = '".$_MontoTotal."',
						cod_partida = '".$_cod_partida."',
						CodCuenta = '".$_CodCuenta."',
						CodCuentaPub20 = '".$_CodCuentaPub20."',
						CodPresupuesto = '".$CodPresupuesto."',
						CodFuente = '".$CodFuente."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	distribucion
		$sql = "DELETE FROM ap_viaticosdistribucion
				WHERE 
					CodOrganismo = '".$CodOrganismo."' AND 
					CodViatico = '".$CodViatico."'";
		execute($sql);
		$_Secuencia = 0;
		$sql = "SELECT
					cod_partida,
					CodCuenta,
					CodCuentaPub20,
					CodPresupuesto,
					CodFuente,
					SUM(MontoTotal) AS Monto
				FROM ap_viaticosdetalle
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					CodViatico = '".$CodViatico."'
				GROUP BY cod_partida";
		$field_distribucion = getRecords($sql);
		foreach($field_distribucion as $f) {
			$sql = "INSERT INTO ap_viaticosdistribucion
					SET
						CodOrganismo = '".$CodOrganismo."',
						CodViatico = '".$CodViatico."',
						Secuencia = '".++$_Secuencia."',
						Monto = '".$f['Monto']."',
						cod_partida = '".$f['cod_partida']."',
						CodCuenta = '".$f['CodCuenta']."',
						CodCuentaPub20 = '".$f['CodCuentaPub20']."',
						CodPresupuesto = '".$f['CodPresupuesto']."',
						CodFuente = '".$f['CodFuente']."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		//	-----------------
		mysql_query("COMMIT");
	}
	//	revisar
	elseif ($accion == "revisar") {
		mysql_query("BEGIN");
		//	-----------------
		##	distribucion
		$sql = "SELECT
					vd.CodOrganismo,
					vd.cod_partida,
					vd.CodPresupuesto,
					vd.CodFuente,
					vd.Monto,
					ppto.Ejercicio
				FROM
					ap_viaticosdistribucion vd
				INNER JOIN pv_presupuesto ppto ON (
					ppto.CodOrganismo = vd.CodOrganismo
					AND ppto.CodPresupuesto = vd.CodPresupuesto
				)
				WHERE
					vd.CodOrganismo = '$CodOrganismo'
					AND vd.CodViatico = '$CodViatico'";
		$field_distribucion = getRecords($sql);
		##	validar
		foreach ($field_distribucion as $f) {
			list($_MontoAjustado, $_MontoCompromiso, $_PreCompromiso, $_CotizacionesAsignadas) = disponibilidadPartida2($f['Ejercicio'], $f['CodOrganismo'], $f['cod_partida'], $f['CodPresupuesto'], $f['CodFuente']);
			$_MontoPendiente = $_PreCompromiso + $_CotizacionesAsignadas;
			$_MontoDisponible = $_MontoAjustado - $_MontoCompromiso;
			$_MontoDisponibleReal = $_MontoAjustado - ($_MontoCompromiso + $_MontoPendiente);
			if (($_MontoDisponible - $f['Monto']) < 0) die("Se encontr&oacute; la partida <strong>$f[cod_partida]</strong> sin Disponibilidad Presupuestaria");
		}
		##	actualizo
		$sql = "UPDATE ap_viaticos
				SET
					Estado = 'RV',
					RevisadoPor = '".$RevisadoPor."',
					FechaRevisado = '".formatFechaAMD($FechaRevisado)."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					CodViatico = '".$CodViatico."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
	//	anular
	elseif ($accion == "anular") {
		mysql_query("BEGIN");
		//	-----------------
		if ($Estado == "PR") {
			$sql = "UPDATE ap_viaticos
					SET
						Estado = 'AN',
						MotivoAnulacion = '".$MotivoAnulacion."',
						AnuladoPor = '".$AnuladoPor."',
						FechaAnulado = '".formatFechaAMD($FechaAnulado)."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()
					WHERE
						CodOrganismo = '".$CodOrganismo."' AND
						CodViatico = '".$CodViatico."'";
		}
		elseif ($Estado == "RV") {
			$sql = "UPDATE ap_viaticos
					SET
						Estado = 'PR',
						RevisadoPor = '',
						FechaRevisado = '',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()
					WHERE
						CodOrganismo = '".$CodOrganismo."' AND
						CodViatico = '".$CodViatico."'";
		}
		else die("No se puede anular este registro");
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
elseif ($modulo == "ajax") {
	//	distribucion
	if ($accion == "viaticos_distribucion") {
		//	recorro valores
		$filas = explode("||", $detalles);
		foreach($filas as $columnas) {
			list($cod_partida, $CodCuenta, $CodCuentaPub20, $CodPresupuesto, $CodFuente, $CategoriaProg, $MontoTotal) = explode("|", $columnas);
			$partida[$CodPresupuesto][$CodFuente][$cod_partida] = $cod_partida;
			$partida_monto[$CodPresupuesto][$CodFuente][$cod_partida] += $MontoTotal;
			$cuenta[$CodCuenta] += $MontoTotal;
			$cuenta20[$CodCuentaPub20] += $MontoTotal;
		}
		$partidas = "";
		$cuentas = "";
		$cuentas20 = "";
		//	cuentas
		while ($Monto = current($cuenta)) {
			$cuentas .= '
				<tr class="trListaBody">
					<td align="center">'.key($cuenta).'</td>
					<td>'.getVar3("SELECT Descripcion FROM ac_mastplancuenta WHERE CodCuenta = '".key($cuenta)."'").'</td>
					<td align="right"><strong>'.number_format($Monto, 2, ',', '.').'</strong></td>
				</tr>
			';
			next($cuenta);
		}
		//	cuentas (pub.20)
		while ($Monto = current($cuenta20)) {
			$cuentas20 .= '
				<tr class="trListaBody">
					<td align="center">'.key($cuenta20).'</td>
					<td>'.getVar3("SELECT Descripcion FROM ac_mastplancuenta20 WHERE CodCuenta = '".key($cuenta20)."'").'</td>
					<td align="right"><strong>'.number_format($Monto, 2, ',', '.').'</strong></td>
				</tr>
			';
			next($cuenta20);
		}
		//	partidas
		foreach ($partida as $_CodPresupuesto => $Presupuesto) {
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
			$partidas .= '
			<tr class="trListaBody2">
				<td colspan="4">
					'.$field_categoria['CatProg'].' - '.$field_categoria['UnidadEjecutora'].'
				</td>
			</tr>';
			foreach ($Presupuesto as $_CodFuente => $Partida) {
				foreach ($Partida as $cod_partida) {
					$partidas .= '
						<tr class="trListaBody">
							<td align="center">'.$_CodFuente.'</td>
							<td align="center">'.$cod_partida.'</td>
							<td>'.getVar3("SELECT denominacion FROM pv_partida WHERE cod_partida = '".$cod_partida."'").'</td>
							<td align="right"><strong>'.number_format($partida_monto[$_CodPresupuesto][$_CodFuente][$cod_partida], 2, ',', '.').'</strong></td>
						</tr>
					';
				}
			}
		}
		$datos = array(
			'cuentas' => $cuentas,
			'cuentas20' => $cuentas20,
			'partidas' => $partidas
		);
		echo json_encode($datos);
	}
	//	inserto linea
	elseif ($accion == "viaticos_conceptos_insertar") {
		$nro_conceptos = $nro_detalles;
		##	
		/*if ($_PARAMETRO['UBACVIAT'] == 'UCAU')
			$UnidadTributaria = getVar3("SELECT Valor FROM mastunidadaritmetica WHERE Anio = '$_PARAMETRO[ANIOUBCVIAT]'");
		else
			$UnidadTributaria = getVar3("SELECT Valor FROM mastunidadtributaria WHERE Anio = '$_PARAMETRO[UTANIOVIAT]'");*/
		##
		#actualizaciones 14-09-2018
		if($_PARAMETRO['UBACVIAT']=='UT') {
			$UnidadTributaria = getUTN($_PARAMETRO['ANIOUBCVIAT'],'mastunidadtributaria', 'UT', 'Valor');
		}elseif($_PARAMETRO['UBACVIAT']=='UCAU'){
			$UnidadTributaria = getUTN($_PARAMETRO['ANIOUBCVIAT'],'mastunidadaritmetica', 'UCAU', 'Valor');
		}
		
		$field = getRecords("SELECT * FROM ap_conceptogastoviatico WHERE CodConcepto = '".$CodConcepto."'");
		foreach($field as $f) {
			$id = $nro_conceptos;
			if ($f['FlagMonto'] == "N" && $f['FlagCantidad'] == "S") {
				$dMontoViatico = "disabled";
				$dCantidadDias = "";
				$MontoViatico = $f['ValorUT'] * $UnidadTributaria;
				$MontoTotal = 0;
			}
			elseif ($f['FlagMonto'] == "S" && $f['FlagCantidad'] == "N") {
				$dMontoViatico = "";
				$dCantidadDias = "disabled";
				$MontoViatico = 0;
				$MontoTotal = 0;
				$UnidadTributaria = 0;
			}
			else if ($f['FlagMonto'] == "N" && $f['FlagCantidad'] == "N") {
				$dMontoViatico = "disabled";
				$dCantidadDias = "disabled";
				$MontoViatico = $f['ValorUT'] * $UnidadTributaria;
				$MontoTotal = $MontoViatico;
			}
			?>
			<tr class="trListaBody" onclick="clk($(this), 'conceptos', 'conceptos_<?=$id?>');" id="conceptos_<?=$id?>">
				<th>
					<?=$nro_conceptos?>
					<input type="hidden" name="Secuencia" value="<?=$nro_conceptos?>" />
					<input type="hidden" name="FlagMonto" id="FlagMonto_<?=$id?>" value="<?=$f['FlagMonto']?>" />
					<input type="hidden" name="FlagCantidad" id="FlagCantidad_<?=$id?>" value="<?=$f['FlagCantidad']?>" />
					<input type="hidden" name="cod_partida" value="<?=$f['cod_partida']?>" />
					<input type="hidden" name="CodCuenta" value="<?=$f['CodCuenta']?>" />
					<input type="hidden" name="CodCuentaPub20" value="<?=$f['CodCuentaPub20']?>" />
					<input type="hidden" name="detalleCodPresupuesto" value="<?=$CodPresupuesto?>" />
					<input type="hidden" name="detalleCodFuente" value="<?=$CodFuente?>" />
					<input type="hidden" name="detalleCategoriaProg" value="<?=$CategoriaProg?>" />
				</th>
				<td>
					<input type="hidden" name="CodConcepto" id="CodConcepto_<?=$id?>" value="<?=$f['CodConcepto']?>" />
					<textarea name="Descripcion" style="height:25px;" class="cell" <?=$disabled_conceptos?>><?=htmlentities($f['Descripcion'])?></textarea>
				</td>
				<td>
					<input type="text" name="ValorUT" id="ValorUT_<?=$id?>" value="<?=number_format($f['ValorUT'], 2, ',', '.')?>" style="text-align:right;" class="cell" disabled="disabled" />
				</td>
				<td>
					<input type="text" name="UnidadTributaria" id="UnidadTributaria_<?=$id?>" value="<?=number_format($UnidadTributaria, 2, ',', '.')?>" style="text-align:right;" class="cell" disabled="disabled" />
				</td>
				<td>
					<input type="text" name="MontoViatico" id="MontoViatico_<?=$id?>" value="<?=number_format($MontoViatico, 2, ',', '.')?>" style="text-align:right;" class="cell currency" onchange="viaticos_calculo('<?=$id?>');" <?=$dMontoViatico?> />
				</td>
				<td>
					<input type="text" name="CantidadDias" id="CantidadDias_<?=$id?>" value="0,00" style="text-align:right;" class="cell currency" onchange="viaticos_calculo('<?=$id?>');" <?=$dCantidadDias?> />
				</td>
				<td>
					<input type="text" name="MontoTotal" id="MontoTotal_<?=$id?>" value="<?=number_format($MontoTotal, 2, ',', '.')?>" style="text-align:right;" class="cell currency" disabled="disabled" />
				</td>
			</tr>
			<?php
		}
	}
	
}
elseif ($modulo == "validar") {
	//	valido para modificar
	if ($accion == "viaticos_modificar") {
		list($CodOrganismo, $CodViatico) = explode("_", $codigo);
		$sql = "SELECT Estado
				FROM ap_viaticos
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					Codviatico = '".$CodViatico."'";
		$Estado = getVar3($sql);
		if ($Estado != "PR") die("Solo se pueden modificar registros <strong>En Preparaci&oacute;n</strong>");
	}
	//	valido para anular
	elseif ($accion == "viaticos_anular") {
		list($CodOrganismo, $CodViatico) = explode("_", $codigo);
		$sql = "SELECT Estado
				FROM ap_viaticos
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					Codviatico = '".$CodViatico."'";
		$Estado = getVar3($sql);
		if ($Estado == "AN") die("Registro ya se encuentra anulado");
		elseif ($Estado == "GE") die("No se puede anular este registro.<br>Anule primero la <strong>Obligaci&oacute;n de Pago</strong>.");
	}
	//	valido para relacionar
	elseif ($accion == "viaticos_relacionar") {
		list($CodOrganismo, $CodViatico) = explode("_", $codigo);
		$sql = "SELECT CodViaticoRelacion
				FROM ap_viaticos
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					Codviatico = '".$CodViatico."'";
		$CodViaticoRelacion = getVar3($sql);
		if ($CodViaticoRelacion != "") die("Este registro se encuentra relacionado con otro vi&aacute;tico");
	}
	//	valido para relacionar
	elseif ($accion == "generar") {
		list($CodOrganismo, $CodViatico) = explode("_", $codigo);
		$sql = "SELECT Estado
				FROM ap_viaticos
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					Codviatico = '".$CodViatico."'";
		$Estado = getVar3($sql);
		if ($Estado != "RV") die("Solo se pueden generar vi&aacute;ticos <strong>Revisados</strong>");
	}
}
?>