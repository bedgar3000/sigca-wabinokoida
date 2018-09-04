<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion-".$AnioActual.$MesActual.$DiaActual."_".$HorasActual.$MinutosActual.$SegundosActual.".sql", "w+");
//	$__archivo = fopen("_"."$modulo-$accion".".sql", "w+");
##############################################################################/
##	Plan de Obras (NUEVO, MODIFICAR, APROBAR, GENERAR, ANULAR, AJAX)
##############################################################################/
if ($modulo == "formulario") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		##	-----------------
		##	valido
		if (!trim($CodPlanObra) || !trim($Denominacion)) die("Debe llenar los campos (*) obligatorios.");
		elseif (setNumero($TotalResta) < 0) die("Total Distribuido no puede ser mayor al Total Aprobado");
		##	codigo
		$CodPresupuesto = codigo('pv_presupuestoobra','CodPresupuesto',4,['CodOrganismo'],[$CodOrganismo]);
		##	inserto
		$sql = "INSERT INTO pv_presupuestoobra
				SET
					CodOrganismo = '".$CodOrganismo."',
					CodPresupuesto = '".$CodPresupuesto."',
					CodPlanObra = '".$CodPlanObra."',
					Denominacion = '".$Denominacion."',
					PreparadoPor = '".$PreparadoPor."',
					FechaPreparado = '".formatFechaAMD($FechaPreparado)."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		##	detalle
		$Secuencia = 0;
		for ($i=0; $i < count($detalle_cod_partida); $i++) {
			if ($detalle_Commodity[$i]) $Commodity = "Commodity = '".$detalle_Commodity[$i]."',"; else $Commodity = "";
			if ($detalle_CodUnidad[$i]) $CodUnidad = "CodUnidad = '".$detalle_CodUnidad[$i]."',"; else $CodUnidad = "";
			$sql = "INSERT INTO pv_presupuestoobradet
					SET
						CodOrganismo = '".$CodOrganismo."',
						CodPresupuesto = '".$CodPresupuesto."',
						Secuencia = '".++$Secuencia."',
						cod_partida = '".$detalle_cod_partida[$i]."',
						$Commodity
						$CodUnidad
						Descripcion = '".trim($detalle_Descripcion[$i])."',
						Cantidad = '".setNumero($detalle_Cantidad[$i])."',
						PrecioUnitario = '".setNumero($detalle_PrecioUnitario[$i])."',
						CodFuente = '".$detalle_CodFuente[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
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
		##	valido
		if (!trim($Denominacion)) die("Debe llenar los campos (*) obligatorios.");
		elseif (setNumero($TotalResta) < 0) die("Total Distribuido no puede ser mayor al Total Aprobado");
		##	actualizo
		$sql = "UPDATE pv_presupuestoobra
				SET
					Denominacion = '".$Denominacion."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					CodPresupuesto = '".$CodPresupuesto."'";
		execute($sql);
		##	detalle
		execute("DELETE FROM pv_presupuestoobradet WHERE CodOrganismo = '".$CodOrganismo."' AND CodPresupuesto = '".$CodPresupuesto."'");
		$Secuencia = 0;
		for ($i=0; $i < count($detalle_cod_partida); $i++) {
			if ($detalle_Commodity[$i]) $Commodity = "Commodity = '".$detalle_Commodity[$i]."',"; else $Commodity = "";
			if ($detalle_CodUnidad[$i]) $CodUnidad = "CodUnidad = '".$detalle_CodUnidad[$i]."',"; else $CodUnidad = "";
			$sql = "INSERT INTO pv_presupuestoobradet
					SET
						CodOrganismo = '".$CodOrganismo."',
						CodPresupuesto = '".$CodPresupuesto."',
						Secuencia = '".++$Secuencia."',
						cod_partida = '".$detalle_cod_partida[$i]."',
						$Commodity
						$CodUnidad
						Descripcion = '".trim($detalle_Descripcion[$i])."',
						Cantidad = '".setNumero($detalle_Cantidad[$i])."',
						PrecioUnitario = '".setNumero($detalle_PrecioUnitario[$i])."',
						CodFuente = '".$detalle_CodFuente[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
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
		##	actualizo
		$sql = "UPDATE pv_presupuestoobra
				SET
					AprobadoPor = '".$AprobadoPor."',
					FechaAprobado = '".formatFechaAMD($FechaAprobado)."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodPresupuesto = '".$CodPresupuesto."'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	generar
	elseif ($accion == "generar") {
		mysql_query("BEGIN");
		##	-----------------
		##	actualizo
		$sql = "UPDATE pv_presupuestoobra
				SET
					GeneradoPor = '".$GeneradoPor."',
					FechaGenerado = '".formatFechaAMD($FechaGenerado)."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodPresupuesto = '".$CodPresupuesto."'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	anular
	elseif ($accion == "anular") {
		mysql_query("BEGIN");
		##	-----------------
		##	actualizo
		$sql = "UPDATE pv_presupuestoobra
				SET
					AnuladoPor = '".$AnuladoPor."',
					FechaAnulado = '".formatFechaAMD($FechaAnulado)."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodPresupuesto = '".$CodPresupuesto."'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
}
elseif ($modulo == "validar") {
	//	modificar
	if($accion == "modificar") {
		list($CodOrganismo, $CodPresupuesto) = explode('_', $codigo);
		$sql = "SELECT Estado FROM pv_presupuestoobra WHERE CodOrganismo = '$CodOrganismo' AND CodPresupuesto = '$CodPresupuesto'";
		$Estado = getVar3($sql);
		if ($Estado != 'PR') die('No puede modificar un presupuesto de obra <strong>'.printValores('presupuesto-obras-estado',$Estado).'</strong>');
	}
	//	aprobar
	elseif($accion == "aprobar") {
		list($CodOrganismo, $CodPresupuesto) = explode('_', $codigo);
		$sql = "SELECT Estado FROM pv_presupuestoobra WHERE CodOrganismo = '$CodOrganismo' AND CodPresupuesto = '$CodPresupuesto'";
		$Estado = getVar3($sql);
		if ($Estado != 'PR') die('No puede aprobar un presupuesto de obra <strong>'.printValores('presupuesto-obras-estado',$Estado).'</strong>');
	}
	//	generar
	elseif($accion == "generar") {
		list($CodOrganismo, $CodPresupuesto) = explode('_', $codigo);
		$sql = "SELECT Estado FROM pv_presupuestoobra WHERE CodOrganismo = '$CodOrganismo' AND CodPresupuesto = '$CodPresupuesto'";
		$Estado = getVar3($sql);
		if ($Estado != 'AP') die('No puede generar un presupuesto de obra <strong>'.printValores('presupuesto-obras-estado',$Estado).'</strong>');
	}
	//	anular
	elseif($accion == "anular") {
		list($CodOrganismo, $CodPresupuesto) = explode('_', $codigo);
		$sql = "SELECT Estado FROM pv_presupuestoobra WHERE CodOrganismo = '$CodOrganismo' AND CodPresupuesto = '$CodPresupuesto'";
		$Estado = getVar3($sql);
		if ($Estado != 'PR') die('No puede anular un presupuesto de obra <strong>'.printValores('presupuesto-obras-estado',$Estado).'</strong>');
	}
}
elseif($modulo == "ajax") {
	//	obtener dependencias x unidad ejecutora
	if ($accion == "getDependencias") {
		$sql = "SELECT * FROM pv_unidadejecutoradep WHERE CodUnidadEjec = '$CodUnidadEjec'";
		$field = getRecords($sql);
		foreach ($field as $f) {
			?><option value="<?=$f['CodDependencia']?>"><?=$f['Dependencia']?></option><?php
		}
	}
	//	insertar partida
	elseif ($accion == "partida_insertar") {
		$sql = "SELECT * FROM pv_partida WHERE cod_partida = '$cod_partida'";
		$field = getRecords($sql);
		foreach ($field as $f) {
			$id = $f['cod_partida'];
			?>
			<tr class="trListaBody" id="detalle_<?=$id?>" onclick="clk($(this), 'detalle', 'detalle_<?=$id?>');">
				<td align="center">
					<input type="hidden" name="detalle_cod_partida[]" value="<?=$id?>" />
					<?=$f['cod_partida']?>
				</td>
				<td><input type="text" name="detalle_Descripcion[]" value="<?=htmlentities($f['denominacion'])?>" class="cell" readonly /></td>
				<td align="center">
					<input type="hidden" name="detalle_Commodity[]" />
				</td>
				<td>
					<select name="detalle_CodUnidad[]" class="cell">
						<option value="">&nbsp;</option>
					</select>
				</td>
				<td align="right"><input type="text" name="detalle_Cantidad[]" value="0,00" class="cell currency" style="text-align:right;" onchange="setMontos();" /></td>
				<td align="right"><input type="text" name="detalle_PrecioUnitario[]" value="0,00" class="cell currency" style="text-align:right;" onchange="setMontos();" /></td>
				<td align="right"><input type="text" name="detalle_MontoTotal[]" value="0,00" class="cell currency" style="text-align:right;" readonly /></td>
				<td>
					<select name="detalle_CodFuente[]" class="cell" onchange="setMontos();">
						<?=loadSelectFromParametros2('pv_fuentefinanciamiento','CodFuente','Denominacion','FFOBRAS','04',10)?>
					</select>
				</td>
			</tr>
			<?php
			
		}
	}
	//	insertar commodity
	elseif ($accion == "commodity_insertar") {
		$sql = "SELECT * FROM lg_commoditysub WHERE Codigo = '$Codigo'";
		$field = getRecords($sql);
		foreach ($field as $f) {
			$id = $f['Codigo'];
			?>
			<tr class="trListaBody" id="detalle_<?=$id?>" onclick="clk($(this), 'detalle', 'detalle_<?=$id?>');">
				<td align="center">
					<input type="hidden" name="detalle_cod_partida[]" value="<?=$f['cod_partida']?>" />
					<?=$f['cod_partida']?>
				</td>
				<td><input type="text" name="detalle_Descripcion[]" value="<?=htmlentities($f['Descripcion'])?>" class="cell" /></td>
				<td align="center">
					<input type="hidden" name="detalle_Commodity[]" value="<?=$f['Codigo']?>" />
					<?=$f['Codigo']?>
				</td>
				<td>
					<select name="detalle_CodUnidad[]" class="cell">
						<?=loadSelect2('mastunidades','CodUnidad','CodUnidad',$f['CodUnidad'])?>
					</select>
				</td>
				<td align="right"><input type="text" name="detalle_Cantidad[]" value="0,00" class="cell currency" style="text-align:right;" onchange="setMontos();" /></td>
				<td align="right"><input type="text" name="detalle_PrecioUnitario[]" value="0,00" class="cell currency" style="text-align:right;" onchange="setMontos();" /></td>
				<td align="right"><input type="text" name="detalle_MontoTotal[]" value="0,00" class="cell currency" style="text-align:right;" readonly /></td>
				<td>
					<select name="detalle_CodFuente[]" class="cell" onchange="setMontos();">
						<?=loadSelectFromParametros2('pv_fuentefinanciamiento','CodFuente','Denominacion','FFOBRAS','04',10)?>
					</select>
				</td>
			</tr>
			<?php
			
		}
	}
	//	distribucion presupuestaria
	elseif ($accion == "getDistribucion") {
		$_PARTIDA = [];
		$_cod_partida = [];
		for ($i=0; $i < count($detalle_cod_partida); $i++) {
			$_PARTIDA[$detalle_cod_partida[$i]] += (setNumero($detalle_Cantidad[$i]) * setNumero($detalle_PrecioUnitario[$i]));
		}
		//	
		$MontoTotal = 0;
		foreach ($_PARTIDA as $cod_partida => $Monto) {
			$denominacion = getVar3("SELECT denominacion FROM pv_partida WHERE cod_partida = '$cod_partida'");
			?>
			<tr class="trListaBody">
				<td align="center"><?=$cod_partida?></td>
				<td><?=htmlentities($denominacion)?></td>
				<td align="right"><?=number_format($Monto,2,',','.')?></td>
			</tr>
			<?php
			$MontoTotal += $Monto;
		}
		if ($MontoTotal) {
			?>
			<tr class="trListaBody2">
				<td align="center" colspan="2">TOTAL</th>
				<td align="right"><?=number_format($MontoTotal,2,',','.')?></td>
			</tr>
			<?php
		}
	}
	//	
	elseif ($accion == "getMontoAprobado") {
		##	
    	$sql = "SELECT SUM(Monto) FROM vw_001presupuestoobradist WHERE Ejercicio = '$Ejercicio' GROUP BY Ejercicio";
    	$MontoDistribuido = getVar3($sql);
		##	SITUADO
		$sql = "SELECT SUM(fd.MontoAprobado)
				FROM
					pv_financiamiento f
					INNER JOIN pv_financiamientodetalle fd ON (fd.CodFinanciamiento = f.CodFinanciamiento)
				WHERE
					f.CodOrganismo = '$CodOrganismo' AND
					f.Ejercicio = '$Ejercicio' AND
					(fd.CodFuente = '04' OR fd.CodFuente = '08' OR fd.CodFuente = '09')";
		$MontoAprobado = getVar3($sql);
    	##	
		$jsondata = [
			'MontoAprobado' => floatval($MontoAprobado),
			'MontoDistribuido' => floatval($MontoDistribuido),
		];

        echo json_encode($jsondata);
        exit();
	}
}
?>