<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion".".sql", "w+");
##############################################################################/
##	Conceptos de Compromisos Directos (NUEVO, MODIFICAR, ELIMINAR, AJAX)
##############################################################################/
if ($modulo == "formulario") {
	//	nuevo
	if ($accion == "nuevo" || $accion == "generar-compromiso") {
		mysql_query("BEGIN");
		##	-----------------
		$Fecha = formatFechaAMD($Fecha);
		$FechaPreparado = formatFechaAMD($FechaPreparado);
		$Monto = setNumero($Monto);
		$Anio = substr($Fecha, 0, 4);
		$Periodo = substr($Fecha, 0, 7);
		##	valido
		if (!trim($CodOrganismo) || !trim($CodPersona) || !trim($CodTipoCertif) || !trim($Fecha) || !trim($NroInterno) || !trim($CodPresupuesto) || !trim($CodFuente)) die("Debe llenar los campos (*) obligatorios.");
		elseif (!validateDate($Fecha,'Y-m-d')) die('Formato de fecha incorrecta');
		elseif (!count($concepto_CodConcepto)) die("Debe insertar los Conceptos de Gastos");
		elseif (!$Monto) die('Se encontraron Montos incorrectos');
		##	codigo
		$CodInterno = codigo('ap_certificaciones','CodInterno',4,['Anio','CodTipoCertif'],[$Anio,$CodTipoCertif]);
		$CodCertificacion = $Anio.$CodTipoCertif.$CodInterno;
		##	inserto
		if ($accion == "generar-compromiso") $iCodObra = "CodObra = '$CodObra',"; else $iCodObra = "";
		$sql = "INSERT INTO ap_certificaciones
				SET
					CodCertificacion = '$CodCertificacion',
					CodOrganismo = '$CodOrganismo',
					CodPersona = '$CodPersona',
					CodTipoCertif = '$CodTipoCertif',
					Anio = '$Anio',
					Fecha = '$Fecha',
					CodInterno = '$CodInterno',
					NroInterno = '$NroInterno',
					Periodo = '$Periodo',
					Monto = '$Monto',
					Justificacion = '$Justificacion',
					PreparadoPor = '$PreparadoPor',
					FechaPreparado = '$FechaPreparado',
					CodPresupuesto = '$CodPresupuesto',
					CodFuente = '$CodFuente',
					$iCodObra
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()";
		execute($sql);
		##	conceptos
		$Secuencia = 0;
		for ($i=0; $i < count($concepto_CodConcepto); $i++) {
			++$Secuencia;
			$concepto_Monto[$i] = setNumero($concepto_Monto[$i]);
			##	valido
			if (!$concepto_cod_partida[$i]) die('Se encontraron Conceptos sin Partidas Presupuestarias');
			elseif (!$concepto_Monto[$i]) die('Se encontraron Montos incorrectos');
			##	
			if (trim($concepto_CodCuenta[$i])) $iCodCuenta = "CodCuenta = '$concepto_CodCuenta[$i]',";
			if (trim($concepto_CodCuentaPub20[$i])) $iCodCuentaPub20 = "CodCuentaPub20 = '$concepto_CodCuentaPub20[$i]',";
			$sql = "INSERT INTO ap_certificacionesdet
					SET
						CodCertificacion = '$CodCertificacion',
						Secuencia = '$Secuencia',
						CodConcepto = '$concepto_CodConcepto[$i]',
						Descripcion = '$concepto_Descripcion[$i]',
						Categoria = '$concepto_Categoria[$i]',
						Monto = '$concepto_Monto[$i]',
						cod_partida = '$concepto_cod_partida[$i]',
						$iCodCuenta
						$iCodCuentaPub20
						CodOrganismo = '$CodOrganismo',
						CodPresupuesto = '$concepto_CodPresupuesto[$i]',
						CodFuente = '$concepto_CodFuente[$i]',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	distribucion
		for ($i=0; $i < count($partida_CodPresupuesto); $i++) {
			list($_MontoAjustado, $_MontoCompromiso, $_PreCompromiso, $_CotizacionesAsignadas) = disponibilidadPartida2($Ejercicio, $CodOrganismo, $partida_cod_partida[$i], $partida_CodPresupuesto[$i], $partida_CodFuente[$i]);
			$_MontoPendiente = $_PreCompromiso + $_CotizacionesAsignadas;
			$_MontoDisponible = $_MontoAjustado - $_MontoCompromiso;
			$_MontoDisponibleReal = $_MontoAjustado - ($_MontoCompromiso + $_MontoPendiente);
			if (($_MontoDisponible - $partida_Monto[$i]) < 0) die("Se encontraron Partidas sin Disponibilidad Presupuestaria. if (($_MontoDisponibleReal - $partida_Monto[$i]) < 0)");
		}
		##	
		if ($accion == "generar-compromiso") {
			$sql = "UPDATE ob_obras
					SET
						Estado = 'GE',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()
					WHERE CodObra = '$CodObra'";
			execute($sql);
		}
		##	-----------------
		mysql_query("COMMIT");
	}
	//	modificar
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		##	-----------------
		$Fecha = formatFechaAMD($Fecha);
		$Monto = setNumero($Monto);
		$Anio = substr($Fecha, 0, 4);
		$Periodo = substr($Fecha, 0, 7);
		##	valido
		if (!trim($CodOrganismo) || !trim($CodPersona) || !trim($CodTipoCertif) || !trim($Fecha) || !trim($NroInterno) || !trim($CodPresupuesto) || !trim($CodFuente)) die("Debe llenar los campos (*) obligatorios.");
		elseif (!validateDate($Fecha,'Y-m-d')) die('Formato de fecha incorrecta');
		elseif (!count($concepto_CodConcepto)) die("Debe insertar los Conceptos de Gastos");
		elseif (!$Monto) die('Se encontraron Montos incorrectos');
		##	actualizo
		$sql = "UPDATE ap_certificaciones
				SET
					CodOrganismo = '$CodOrganismo',
					CodPersona = '$CodPersona',
					CodTipoCertif = '$CodTipoCertif',
					Anio = '$Anio',
					Fecha = '$Fecha',
					NroInterno = '$NroInterno',
					Periodo = '$Periodo',
					Monto = '$Monto',
					Justificacion = '$Justificacion',
					PreparadoPor = '$PreparadoPor',
					FechaPreparado = '$FechaPreparado',
					CodPresupuesto = '$CodPresupuesto',
					CodFuente = '$CodFuente',
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodCertificacion = '$CodCertificacion'";
		execute($sql);
		##	conceptos
		execute("DELETE FROM ap_certificacionesdet WHERE CodCertificacion = '$CodCertificacion'");
		$Secuencia = 0;
		for ($i=0; $i < count($concepto_CodConcepto); $i++) {
			$concepto_Monto[$i] = setNumero($concepto_Monto[$i]);
			##	valido
			if (!$concepto_cod_partida[$i]) die('Se encontraron Conceptos sin Partidas Presupuestarias');
			elseif (!$concepto_Monto[$i]) die('Se encontraron Montos incorrectos');
			##	
			++$Secuencia;
			$sql = "INSERT INTO ap_certificacionesdet
					SET
						CodCertificacion = '$CodCertificacion',
						Secuencia = '$Secuencia',
						CodConcepto = '$concepto_CodConcepto[$i]',
						Descripcion = '$concepto_Descripcion[$i]',
						Categoria = '$concepto_Categoria[$i]',
						Monto = '$concepto_Monto[$i]',
						cod_partida = '$concepto_cod_partida[$i]',
						CodCuenta = '$concepto_CodCuenta[$i]',
						CodCuentaPub20 = '$concepto_CodCuentaPub20[$i]',
						CodOrganismo = '$CodOrganismo',
						CodPresupuesto = '$concepto_CodPresupuesto[$i]',
						CodFuente = '$concepto_CodFuente[$i]',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	distribucion
		for ($i=0; $i < count($partida_CodPresupuesto); $i++) { 
			list($_MontoAjustado, $_MontoCompromiso, $_PreCompromiso, $_CotizacionesAsignadas) = disponibilidadPartida2($Ejercicio, $CodOrganismo, $partida_cod_partida[$i], $partida_CodPresupuesto[$i], $partida_CodFuente[$i]);
			$_MontoPendiente = $_PreCompromiso + $_CotizacionesAsignadas;
			$_MontoDisponible = $_MontoAjustado - $_MontoCompromiso;
			$_MontoDisponibleReal = $_MontoAjustado - ($_MontoCompromiso + $_MontoPendiente);
			if(($_MontoDisponible - $partida_Monto[$i]) < 0) die("Se encontraron Partidas sin Disponibilidad Presupuestaria");
		}
		##	-----------------
		mysql_query("COMMIT");
	}
	//	revisar
	elseif ($accion == "revisar") {
		mysql_query("BEGIN");
		##	-----------------
		$sql = "SELECT * FROM ap_certificaciones WHERE CodCertificacion = '$CodCertificacion'";
		$field = getRecord($sql);
		if ($field['Estado'] != 'PR') die("No puede Revisar un registro ".printValores('certificaciones-estado', $field['Estado']));
		##	actualizo
		$sql = "UPDATE ap_certificaciones
				SET
					Estado = 'RV',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodCertificacion = '$CodCertificacion'";
		execute($sql);
		##	
		if ($field['CodObra']) {
			$sql = "SELECT
						cd.*,
						ppto.Ejercicio,
						ue.CodCentroCosto
					FROM
						ap_certificacionesdet cd
						LEFT JOIN pv_presupuestodet pptod ON (
							pptod.CodOrganismo = cd.CodOrganismo
							AND pptod.CodPresupuesto = cd.CodPresupuesto
							AND pptod.CodFuente = cd.CodFuente
							AND pptod.cod_partida = cd.cod_partida
						)
						LEFT JOIN pv_presupuesto ppto ON (ppto.CodOrganismo = pptod.CodOrganismo AND ppto.CodPresupuesto = pptod.CodPresupuesto)
						LEFT JOIN pv_categoriaprog cp ON (cp.CategoriaProg = ppto.CategoriaProg)
						LEFT JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cp.CodUnidadEjec)
					WHERE CodCertificacion = '$CodCertificacion'";
			$field_det = getRecords($sql);
			foreach ($field_det as $f) {
				$Mes = substr($field['Periodo'], 6, 2);
				$sql = "INSERT INTO lg_distribucioncompromisos
						SET
							Anio = '$f[Ejercicio]',
							CodOrganismo = '$f[CodOrganismo]',
							CodProveedor = '$field[CodPersona]',
							CodTipoDocumento = 'BO',
							NroDocumento = '$f[CodCertificacion]',
							Secuencia = '$f[Secuencia]',
							Linea = '$f[Secuencia]',
							Mes = '$Mes',
							CodCentroCosto = '$f[CodCentroCosto]',
							cod_partida = '$f[cod_partida]',
							Monto = '$f[Monto]',
							Periodo = '$field[Periodo]',
							CodPresupuesto = '$f[CodPresupuesto]',
							Ejercicio = '$f[Ejercicio]',
							CodFuente = '$f[CodFuente]',
							FechaEjecucion = '$field[Fecha]',
							Origen = 'BO',
							Estado = 'CO',
							UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
							UltimaFecha = NOW()";
				execute($sql);
			}
		}
		##	-----------------
		mysql_query("COMMIT");
	}
	//	anular
	elseif ($accion == "anular") {
		mysql_query("BEGIN");
		##	-----------------
		$sql = "SELECT * FROM ap_certificaciones WHERE CodCertificacion = '$CodCertificacion'";
		$field = getRecord($sql);
		if ($field['Estado'] == 'AN' || $field['Estado'] == 'GE') die("No puede Anular un registro ".printValores('certificaciones-estado', $field['Estado']));
		if ($field['Estado'] == 'RV') $Estado = 'PR';
		elseif ($field['Estado'] == 'PR') $Estado = 'AN';
		##	actualizo
		$sql = "UPDATE ap_certificaciones
				SET
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodCertificacion = '$CodCertificacion'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
}
elseif ($modulo == "validar") {
	if ($accion == 'modificar') {
		$sql = "SELECT * FROM ap_certificaciones WHERE CodCertificacion = '$codigo'";
		$field = getRecord($sql);
		if ($field['Estado'] != 'PR') die("No puede Modificar un registro ".printValores('certificaciones-estado', $field['Estado']));
	}
	elseif ($accion == 'revisar') {
		$sql = "SELECT * FROM ap_certificaciones WHERE CodCertificacion = '$codigo'";
		$field = getRecord($sql);
		if ($field['Estado'] != 'PR') die("No puede Revisar un registro ".printValores('certificaciones-estado', $field['Estado']));
	}
	elseif ($accion == 'generar') {
		$sql = "SELECT * FROM ap_certificaciones WHERE CodCertificacion = '$codigo'";
		$field = getRecord($sql);
		if ($field['Estado'] != 'RV') die("No puede Generar un registro ".printValores('certificaciones-estado', $field['Estado']));
	}
	elseif ($accion == 'anular') {
		$sql = "SELECT * FROM ap_certificaciones WHERE CodCertificacion = '$codigo'";
		$field = getRecord($sql);
		if ($field['Estado'] == 'AN' || $field['Estado'] == 'GE') die("No puede Anular un registro ".printValores('certificaciones-estado', $field['Estado']));
	}
}
elseif ($modulo == "ajax") {
	//	insertar linea
	if ($accion == "concepto_insertar") {
		$sql = "SELECT
					cc.*,
					md.Descripcion AS NomCategoria
				FROM
					ap_conceptoscertificacion cc
					LEFT JOIN mastmiscelaneosdet md ON (md.CodDetalle = cc.Categoria AND md.CodMaestro = 'CATCERTIF')
				WHERE cc.CodConcepto = '$CodConcepto'";
		$field = getRecord($sql);
		$id = $field['CodConcepto'];
		?>
		<tr class="trListaBody" onclick="clk($(this), 'concepto', 'concepto_<?=$id?>');" id="concepto_<?=$id?>">
			<th>
				<input type="hidden" name="concepto_CodConcepto[]" value="<?=$id?>" />
				<?=$nro_detalles;?>
			</th>
			<td align="center"><?=$field['CodConcepto']?></td>
			<td><input type="text" name="concepto_Descripcion[]" value="<?=htmlentities($field['Descripcion'])?>" class="cell"></td>
			<td>
				<input type="hidden" name="concepto_CodPresupuesto[]" value="<?=$CodPresupuesto?>" class="CodPresupuesto" />
				<input type="hidden" name="concepto_Ejercicio[]" value="<?=$Ejercicio?>" class="Ejercicio" />
				<input type="text" name="concepto_CategoriaProg[]" value="<?=$CategoriaProg?>" class="cell CategoriaProg" style="text-align:center;">
			</td>
			<td>
				<select name="concepto_CodFuente[]" class="cell2 CodFuente" onchange="getDistribucion();">
					<?=loadSelect2("pv_fuentefinanciamiento","CodFuente","Denominacion",$CodFuente,10)?>
				</select>
			</td>
			<td align="center">
				<input type="hidden" name="concepto_cod_partida[]" value="<?=$field['cod_partida']?>" />
				<input type="hidden" name="concepto_CodCuenta[]" value="<?=$field['CodCuenta']?>" />
				<input type="hidden" name="concepto_CodCuentaPub20[]" value="<?=$field['CodCuentaPub20']?>" />
				<?=$field['cod_partida']?>
			</td>
			<td align="center">
				<input type="hidden" name="concepto_Categoria[]" value="<?=$field['Categoria']?>" />
				<?=htmlentities($field['NomCategoria'])?>
			</td>
			<td><input type="text" name="concepto_Monto[]" value="<?=$Monto?>" class="cell currency" style="text-align:right;" onchange="setTotal(); getDistribucion();"></td>
		</tr>
		<?php
	}
	//	insertar linea
	elseif ($accion == "partida_insertar") {
		$sql = "SELECT *
				FROM pv_partida
				WHERE cod_partida = '$cod_partida'";
		$field = getRecord($sql);
		$id = $field['cod_partida'];
		?>
		<tr class="trListaBody" onclick="clk($(this), 'concepto', 'concepto_<?=$id?>');" id="concepto_<?=$id?>">
			<th>
				<input type="hidden" name="concepto_CodConcepto[]" />
				<?=$nro_detalles;?>
			</th>
			<td align="center"><?=$field['CodConcepto']?></td>
			<td><input type="text" name="concepto_Descripcion[]" value="<?=htmlentities($field['denominacion'])?>" class="cell"></td>
			<td>
				<input type="hidden" name="concepto_CodPresupuesto[]" value="<?=$CodPresupuesto?>" class="CodPresupuesto" />
				<input type="hidden" name="concepto_Ejercicio[]" value="<?=$Ejercicio?>" class="Ejercicio" />
				<input type="text" name="concepto_CategoriaProg[]" value="<?=$CategoriaProg?>" class="cell CategoriaProg" style="text-align:center;">
			</td>
			<td>
				<select name="concepto_CodFuente[]" class="cell2 CodFuente" onchange="getDistribucion();">
					<?=loadSelect2("pv_fuentefinanciamiento","CodFuente","Denominacion",$CodFuente,10)?>
				</select>
			</td>
			<td align="center">
				<input type="hidden" name="concepto_cod_partida[]" value="<?=$field['cod_partida']?>" />
				<input type="hidden" name="concepto_CodCuenta[]" value="<?=$field['CodCuenta']?>" />
				<input type="hidden" name="concepto_CodCuentaPub20[]" value="<?=$field['CodCuentaPub20']?>" />
				<?=$field['cod_partida']?>
			</td>
			<td align="center">
				<input type="hidden" name="concepto_Categoria[]" />
			</td>
			<td><input type="text" name="concepto_Monto[]" value="<?=$Monto?>" class="cell currency" style="text-align:right;" onchange="setTotal(); getDistribucion();"></td>
		</tr>
		<?php
	}
	//	obtener distribucion
	elseif ($accion == "getDistribucion") {
		for ($i=0; $i < count($concepto_cod_partida); $i++) { 
			$idx = $concepto_CategoriaProg[$i] . $CodOrganismo . $concepto_CodPresupuesto[$i] . $concepto_CodFuente[$i] . $concepto_cod_partida[$i];
			##	
			if (!isset($partidas[$idx])) $partidas[$idx] = setNumero($concepto_Monto[$i]);
			else $partidas[$idx] += setNumero($concepto_Monto[$i]);
		}
		ksort($partidas);
		##	
		foreach ($partidas as $key => $Monto) {
			$CategoriaProg = substr($key, 0, 15);
			$CodOrganismo = substr($key, 15, 4);
			$CodPresupuesto = substr($key, 19, 4);
			$CodFuente = substr($key, 23, 2);
			$cod_partida = substr($key, 25, 12);
			$CatProg = substr($CategoriaProg, 0, 2) . substr($CategoriaProg, 4, 2) . substr($CategoriaProg, 10, 2);
			##	
			$denominacion = getVar3("SELECT denominacion FROM pv_partida WHERE cod_partida = '$cod_partida'");
			list($_MontoAjustado, $_MontoCompromiso, $_PreCompromiso, $_CotizacionesAsignadas) = disponibilidadPartida2($Ejercicio, $CodOrganismo, $cod_partida, $CodPresupuesto, $CodFuente);
			$_MontoPendiente = $_PreCompromiso + $_CotizacionesAsignadas;
			$_MontoDisponible = $_MontoAjustado - $_MontoCompromiso;
			$_MontoDisponibleReal = $_MontoAjustado - ($_MontoCompromiso + $_MontoPendiente);
			##	
			if (($_MontoDisponible - $Monto) < 0) $style = "style='background-color:#F8637D;'";
			elseif(($_MontoDisponibleReal - $Monto) < 0) $style = "style='background-color:#FFC;'";
			else $style = "style='background-color:#D0FDD2;'";
			?>
			<tr class="trListaBody" <?=$style?>>
				<td align="center">
					<?=$CatProg?>
					<input type="hidden" name="partida_CodPresupuesto[]" value="<?=$CodPresupuesto?>">
					<input type="hidden" name="partida_CodFuente[]" value="<?=$CodFuente?>">
					<input type="hidden" name="partida_cod_partida[]" value="<?=$cod_partida?>">
					<input type="hidden" name="partida_Monto[]" value="<?=$Monto?>">
				</td>
				<td align="center"><?=$CodFuente?></td>
				<td align="center"><?=$cod_partida?></td>
				<td><?=htmlentities($denominacion)?></td>
				<td align="right"><?=number_format($Monto,2,',','.')?></td>
			</tr>
			<?php
		}
	}
}
?>