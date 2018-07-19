<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion-".$AnioActual.$MesActual.$DiaActual."_".$HorasActual.$MinutosActual.$SegundosActual.".sql", "w+");
//	$__archivo = fopen("_"."$modulo-$accion".".sql", "w+");
##############################################################################/
##	Reformulación por Metas (NUEVO, MODIFICAR, APROBAR, ANULAR, AJAX)
##############################################################################/
if ($modulo == "formulario") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		##	-----------------
		##	valido
		if (!trim($CodMeta) || !trim($Ejercicio) || !trim($Descripcion)) die("Debe llenar los campos (*) obligatorios.");
		//elseif (setNumero($TotalResta) < 0) die("Total Distribuido no puede ser mayor al Total Aprobado");
		//elseif (setNumero($TotalResta1) < 0) die("Total Distribuido no puede ser mayor al Total Aprobado (SITUADO)");
		//elseif (setNumero($TotalResta2) < 0) die("Total Distribuido no puede ser mayor al Total Aprobado (INGRESOS PROPIOS)");
		##	inserto
		$sql = "INSERT INTO pv_reformulacionmetas
				SET
					CodMeta = '".$CodMeta."',
					Ejercicio = '".$Ejercicio."',
					Descripcion = '".$Descripcion."',
					Cantidad = '".$Cantidad."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		##	detalle
		$Secuencia = 0;
		for ($i=0; $i < count($detalle_cod_partida); $i++) {
			if ($detalle_Commodity[$i]) $Commodity = "Commodity = '".$detalle_Commodity[$i]."',"; else $Commodity = "";
			if ($detalle_CodUnidad[$i]) $CodUnidad = "CodUnidad = '".$detalle_CodUnidad[$i]."',"; else $CodUnidad = "";
			$sql = "INSERT INTO pv_reformulacionmetasdet
					SET
						CodMeta = '".$CodMeta."',
						Ejercicio = '".$Ejercicio."',
						Secuencia = '".++$Secuencia."',
						cod_partida = '".$detalle_cod_partida[$i]."',
						$Commodity
						$CodUnidad
						Descripcion = '".trim($detalle_Descripcion[$i])."',
						Cantidad = '".setNumero($detalle_Cantidad[$i])."',
						PrecioUnitario = '".setNumero($detalle_PrecioUnitario[$i])."',
						MontoIva = '".setNumero($detalle_MontoIva[$i])."',
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
		if (!trim($CodMeta) || !trim($Ejercicio) || !trim($Descripcion)) die("Debe llenar los campos (*) obligatorios.");
		//elseif (setNumero($TotalResta) < 0) die("Total Distribuido no puede ser mayor al Total Aprobado");
		##	actualizo
		$sql = "UPDATE pv_reformulacionmetas
				SET
					Descripcion = '".$Descripcion."',
					Cantidad = '".$Cantidad."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodMeta = '".$CodMeta."' AND
					Ejercicio = '".$Ejercicio."'";
		execute($sql);
		##	detalle
		execute("DELETE FROM pv_reformulacionmetasdet WHERE CodMeta = '".$CodMeta."' AND Ejercicio = '".$Ejercicio."'");
		$Secuencia = 0;
		for ($i=0; $i < count($detalle_cod_partida); $i++) {
			if ($detalle_Commodity[$i]) $Commodity = "Commodity = '".$detalle_Commodity[$i]."',"; else $Commodity = "";
			if ($detalle_CodUnidad[$i]) $CodUnidad = "CodUnidad = '".$detalle_CodUnidad[$i]."',"; else $CodUnidad = "";
			$sql = "INSERT INTO pv_reformulacionmetasdet
					SET
						CodMeta = '".$CodMeta."',
						Ejercicio = '".$Ejercicio."',
						Secuencia = '".++$Secuencia."',
						cod_partida = '".$detalle_cod_partida[$i]."',
						$Commodity
						$CodUnidad
						Descripcion = '".trim($detalle_Descripcion[$i])."',
						Cantidad = '".setNumero($detalle_Cantidad[$i])."',
						PrecioUnitario = '".setNumero($detalle_PrecioUnitario[$i])."',
						MontoIva = '".setNumero($detalle_MontoIva[$i])."',
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
		##	valido
		if (setNumero($TotalResta) < 0) die("Total Distribuido no puede ser mayor al Total Aprobado");
		elseif (setNumero($TotalResta) > 0) die("Total Distribuido debe ser igual al Total Aprobado");
		##	actualizo
		$sql = "UPDATE pv_reformulacionmetas
				SET
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodMeta = '".$CodMeta."' AND
					Ejercicio = '".$Ejercicio."'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
}
elseif ($modulo == "validar") {
	//	modificar
	if($accion == "modificar") {
		list($CodMeta, $Ejercicio) = explode('_', $codigo);
		$sql = "SELECT Estado FROM pv_reformulacionmetas WHERE CodMeta = '$CodMeta' AND Ejercicio = '$Ejercicio'";
		$Estado = getVar3($sql);
		if ($Estado != 'PR') die('No puede modificar una formulaci&oacute;n <strong>'.printValores('metas-estado',$Estado).'</strong>');
	}
	//	aprobar
	elseif($accion == "aprobar") {
		list($CodMeta, $Ejercicio) = explode('_', $codigo);
		$sql = "SELECT Estado FROM pv_reformulacionmetas WHERE CodMeta = '$CodMeta' AND Ejercicio = '$Ejercicio'";
		$Estado = getVar3($sql);
		if ($Estado != 'PR') die('No puede aprobar una formulaci&oacute;n <strong>'.printValores('metas-estado',$Estado).'</strong>');
	}
}
elseif($modulo == "ajax") {
	//	obtener objetivos x categoria programática
	if ($accion == "getObjetivos") {
		$sql = "SELECT * FROM pv_objetivospoa WHERE CategoriaProg = '$CategoriaProg'";
		$field = getRecords($sql);
		$CodObjetivo = '<option value="">&nbsp;</option>';
		foreach ($field as $f) {
			$CodObjetivo .= '<option value="'.$f['CodObjetivo'].'">'.$f['NroObjetivo'].'</option>';
		}
		##
		$CodOrganismo = getVar3("SELECT CodOrganismo FROM pv_categoriaprog WHERE CategoriaProg = '$CategoriaProg'");
		##	
    	$sql = "SELECT SUM(Monto) FROM vw_002reformulacionmetasdist WHERE Ejercicio = '$Ejercicio' GROUP BY Ejercicio";
    	$MontoDistribuido = getVar3($sql);
		##	SITUADO
		$sql = "SELECT SUM(fd.MontoAprobado)
				FROM
					pv_financiamiento f
					INNER JOIN pv_financiamientodetalle fd ON (fd.CodFinanciamiento = f.CodFinanciamiento)
				WHERE
					f.CodOrganismo = '$CodOrganismo' AND
					f.Ejercicio = '$Ejercicio' AND
					(fd.CodFuente = '02' OR fd.CodFuente = '03')";
		$MontoAprobado1 = getVar3($sql);
		$sql = "SELECT SUM(Monto)
				FROM vw_003formulacionpersonaldist
				WHERE Ejercicio = '$Ejercicio'
				GROUP BY Ejercicio";
		$MontoPersonal1 = getVar3($sql);
		##	
    	$sql = "SELECT SUM(Monto)
    			FROM vw_002reformulacionmetasdist
    			WHERE
    				Ejercicio = '$Ejercicio' AND
    				(CodFuente = '02' || CodFuente = '03')
    			GROUP BY Ejercicio";
    	$MontoDistribuido1 = getVar3($sql);
		##	INGRESOS PROPIOS
		$sql = "SELECT MontoProyecto
				FROM ha_presupuesto
				WHERE
					CodOrganismo = '$CodOrganismo' AND
					Ejercicio = '$Ejercicio' AND
					Estado = 'AP'";
		$MontoAprobado2 = getVar3($sql);
		$MontoPersonal2 = 0;
		##	
    	$sql = "SELECT SUM(Monto)
    			FROM vw_002reformulacionmetasdist
    			WHERE
    				Ejercicio = '$Ejercicio' AND
    				CodFuente = '01'
    			GROUP BY Ejercicio";
    	$MontoDistribuido2 = getVar3($sql);
    	##	TOTAL GENERAL
    	$MontoAprobado = $MontoAprobado1 + $MontoAprobado2;
    	$MontoPersonal = $MontoPersonal1 + $MontoPersonal2;
    	##	
		$jsondata = [
			'MontoAprobado' => floatval($MontoAprobado),
			'MontoDistribuido' => floatval($MontoDistribuido),
			'MontoPersonal' => floatval($MontoPersonal),
			'MontoAprobado1' => floatval($MontoAprobado1),
			'MontoDistribuido1' => floatval($MontoDistribuido1),
			'MontoPersonal1' => floatval($MontoPersonal1),
			'MontoAprobado2' => floatval($MontoAprobado2),
			'MontoDistribuido2' => floatval($MontoDistribuido2),
			'MontoPersonal2' => floatval($MontoPersonal2),
			'CodObjetivo' => $CodObjetivo,
		];

        echo json_encode($jsondata);
        exit();
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
				<td align="right"><input type="text" name="detalle_MontoIva[]" value="0,00" class="cell currency" style="text-align:right;" onchange="setMontos();" /></td>
				<td align="right"><input type="text" name="detalle_MontoTotal[]" value="0,00" class="cell currency" style="text-align:right;" readonly /></td>
				<td align="right"><input type="text" name="detalle_MontoFormulado[]" value="<?=number_format(0,2,',','.')?>" class="cell2" style="text-align:right;" readonly /></td>
				<td>
					<select name="detalle_CodFuente[]" class="cell" onchange="setMontos();">
						<?=loadSelectFromParametros2('pv_fuentefinanciamiento','CodFuente','Denominacion','FFMETAS','02',10)?>
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
				<td align="right"><input type="text" name="detalle_MontoIva[]" value="0,00" class="cell currency" style="text-align:right;" onchange="setMontos();" /></td>
				<td align="right"><input type="text" name="detalle_MontoTotal[]" value="0,00" class="cell currency" style="text-align:right;" readonly /></td>
				<td align="right"><input type="text" name="detalle_MontoFormulado[]" value="<?=number_format(0,2,',','.')?>" class="cell2" style="text-align:right;" readonly /></td>
				<td>
					<select name="detalle_CodFuente[]" class="cell" onchange="setMontos();">
						<?=loadSelectFromParametros2('pv_fuentefinanciamiento','CodFuente','Denominacion','FFMETAS','02',10)?>
					</select>
				</td>
			</tr>
			<?php
			
		}
	}
	//	distribucion presupuestaria
	elseif ($accion == "getDistribucion") {
		$FuenteDefault = $_PARAMETRO['FFMETASDEF'];
		$_PARTIDA = [];
		$_cod_partida = [];
		for ($i=0; $i < count($detalle_cod_partida); $i++) {
			$_PARTIDA[$detalle_CodFuente[$i]][$detalle_cod_partida[$i]] += (setNumero($detalle_Cantidad[$i]) * setNumero($detalle_PrecioUnitario[$i]));
		}
		if (setNumero($TotalImpuestos)) $_PARTIDA[$FuenteDefault][$_PARAMETRO['IVADEFAULT']] = setNumero($TotalImpuestos);
		//	
		$MontoTotal = 0;
		foreach ($_PARTIDA as $CodFuente => $_DETALLE) {
			?>
			<tr class="trListaBody3">
				<td colspan="3"><?=$CodFuente?> - <?=getVar3("SELECT Denominacion FROM pv_fuentefinanciamiento WHERE CodFuente = '$CodFuente'")?></td>
			</tr>
			<?php
			foreach ($_DETALLE as $cod_partida => $Monto) {
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
	//	obtener descripcion de la meta
	elseif ($accion == "getDescripcionMeta") {
		$Descripcion = getVar3("SELECT Descripcion FROM pv_metaspoa WHERE CodMeta = '$CodMeta'");
		echo $Descripcion;
	}
	//	
	elseif ($accion == "getMontoAprobado") {
		##
		$CodOrganismo = getVar3("SELECT CodOrganismo FROM pv_categoriaprog WHERE CategoriaProg = '$CategoriaProg'");
		##	
    	$sql = "SELECT SUM(Monto) FROM vw_002reformulacionmetasdist WHERE Ejercicio = '$Ejercicio' GROUP BY Ejercicio";
    	$MontoDistribuido = getVar3($sql);
		##	SITUADO
		$sql = "SELECT SUM(fd.MontoAprobado)
				FROM
					pv_financiamiento f
					INNER JOIN pv_financiamientodetalle fd ON (fd.CodFinanciamiento = f.CodFinanciamiento)
				WHERE
					f.CodOrganismo = '$CodOrganismo' AND
					f.Ejercicio = '$Ejercicio' AND
					(fd.CodFuente = '02' OR fd.CodFuente = '03')";
		$MontoAprobado1 = getVar3($sql);
		$sql = "SELECT SUM(Monto)
				FROM vw_003formulacionpersonaldist
				WHERE Ejercicio = '$Ejercicio'
				GROUP BY Ejercicio";
		$MontoPersonal1 = getVar3($sql);
		##	
    	$sql = "SELECT SUM(Monto)
    			FROM vw_002reformulacionmetasdist
    			WHERE
    				Ejercicio = '$Ejercicio' AND
    				(CodFuente = '02' || CodFuente = '03')
    			GROUP BY Ejercicio";
    	$MontoDistribuido1 = getVar3($sql);
		##	INGRESOS PROPIOS
		$sql = "SELECT MontoProyecto
				FROM ha_presupuesto
				WHERE
					CodOrganismo = '$CodOrganismo' AND
					Ejercicio = '$Ejercicio' AND
					Estado = 'AP'";
		$MontoAprobado2 = getVar3($sql);
		$MontoPersonal2 = 0;
		##	
    	$sql = "SELECT SUM(Monto)
    			FROM vw_002reformulacionmetasdist
    			WHERE
    				Ejercicio = '$Ejercicio' AND
    				CodFuente = '01'
    			GROUP BY Ejercicio";
    	$MontoDistribuido2 = getVar3($sql);
    	##	TOTAL GENERAL
    	$MontoAprobado = $MontoAprobado1 + $MontoAprobado2;
    	$MontoPersonal = $MontoPersonal1 + $MontoPersonal2;
    	##	
		$jsondata = [
			'MontoAprobado' => floatval($MontoAprobado),
			'MontoDistribuido' => floatval($MontoDistribuido),
			'MontoPersonal' => floatval($MontoPersonal),

			'MontoAprobado1' => floatval($MontoAprobado1),
			'MontoDistribuido1' => floatval($MontoDistribuido1),
			'MontoPersonal1' => floatval($MontoPersonal1),

			'MontoAprobado2' => floatval($MontoAprobado2),
			'MontoDistribuido2' => floatval($MontoDistribuido2),
			'MontoPersonal2' => floatval($MontoPersonal2),
		];

        echo json_encode($jsondata);
        exit();
	}
}
?>