<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion-".$AnioActual.$MesActual.$DiaActual."_".$HorasActual.$MinutosActual.$SegundosActual.".sql", "w+");
//	$__archivo = fopen("_"."$modulo-$accion".".sql", "w+");
##############################################################################/
##	Formulación por Metas (NUEVO, MODIFICAR, APROBAR, ANULAR, AJAX)
##############################################################################/
if ($modulo == "formulario") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		##	-----------------
		##	valido
		if (!trim($CodMeta) || !trim($Ejercicio) || !trim($Descripcion)) die("Debe llenar los campos (*) obligatorios.");
		##	inserto
		$sql = "INSERT INTO pv_formulacionmetas
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
			$sql = "INSERT INTO pv_formulacionmetasdet
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
		##	actualizo
		$sql = "UPDATE pv_formulacionmetas
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
		execute("DELETE FROM pv_formulacionmetasdet WHERE CodMeta = '".$CodMeta."' AND Ejercicio = '".$Ejercicio."'");
		$Secuencia = 0;
		for ($i=0; $i < count($detalle_cod_partida); $i++) {
			if ($detalle_Commodity[$i]) $Commodity = "Commodity = '".$detalle_Commodity[$i]."',"; else $Commodity = "";
			if ($detalle_CodUnidad[$i]) $CodUnidad = "CodUnidad = '".$detalle_CodUnidad[$i]."',"; else $CodUnidad = "";
			$sql = "INSERT INTO pv_formulacionmetasdet
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
		##	actualizo
		$sql = "UPDATE pv_formulacionmetas
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
	//	generar
	elseif ($accion == "generar-reformulacion") {
		mysql_query("BEGIN");
		##	-----------------
		##	valido
		if (!trim($CodMeta) || !trim($Ejercicio) || !trim($Descripcion)) die("Debe llenar los campos (*) obligatorios.");
		##	inserto
		$sql = "INSERT INTO pv_reformulacionmetas
				SET
					CodMeta = '".$CodMeta."',
					Ejercicio = '".$Ejercicio."',
					Descripcion = '".$Descripcion."',
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
		##	actualizo
		$sql = "UPDATE pv_formulacionmetas
				SET
					Estado = 'RF',
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
		$sql = "SELECT Estado FROM pv_formulacionmetas WHERE CodMeta = '$CodMeta' AND Ejercicio = '$Ejercicio'";
		$Estado = getVar3($sql);
		if ($Estado != 'PR') die('No puede modificar una formulaci&oacute;n <strong>'.printValores('metas-estado',$Estado).'</strong>');
	}
	//	aprobar
	elseif($accion == "aprobar") {
		list($CodMeta, $Ejercicio) = explode('_', $codigo);
		$sql = "SELECT Estado FROM pv_formulacionmetas WHERE CodMeta = '$CodMeta' AND Ejercicio = '$Ejercicio'";
		$Estado = getVar3($sql);
		if ($Estado != 'PR') die('No puede aprobar una formulaci&oacute;n <strong>'.printValores('metas-estado',$Estado).'</strong>');
	}
	//	generar
	elseif($accion == "generar-reformulacion") {
		list($CodMeta, $Ejercicio) = explode('_', $codigo);
		$sql = "SELECT Estado FROM pv_formulacionmetas WHERE CodMeta = '$CodMeta' AND Ejercicio = '$Ejercicio'";
		$Estado = getVar3($sql);
		if ($Estado != 'GE') die('No puede generar una reformulaci&oacute;n <strong>'.printValores('metas-estado',$Estado).'</strong>');
	}
}
elseif($modulo == "ajax") {
	//	obtener objetivos x categoria programática
	if ($accion == "getObjetivos") {
		$sql = "SELECT * FROM pv_objetivospoa WHERE CategoriaProg = '$CategoriaProg'";
		$field = getRecords($sql);
		?><option value="">&nbsp;</option><?php
		foreach ($field as $f) {
			?><option value="<?=$f['CodObjetivo']?>"><?=$f['NroObjetivo']?></option><?php
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
				<td align="right"><input type="text" name="detalle_MontoIva[]" value="0,00" class="cell currency" style="text-align:right;" onchange="setMontos();" /></td>
				<td align="right"><input type="text" name="detalle_MontoTotal[]" value="0,00" class="cell currency" style="text-align:right;" readonly /></td>
				<td>
					<select name="detalle_CodFuente[]" class="cell">
						<?=loadSelectFromParametros2('pv_fuentefinanciamiento','CodFuente','Denominacion','FFMETAS','',10)?>
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
				<td>
					<select name="detalle_CodFuente[]" class="cell">
						<?=loadSelectFromParametros2('pv_fuentefinanciamiento','CodFuente','Denominacion','FFMETAS','',10)?>
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
}
?>