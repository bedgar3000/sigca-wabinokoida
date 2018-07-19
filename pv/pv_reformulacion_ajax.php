<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion-".$AnioActual.$MesActual.$DiaActual."_".$HorasActual.$MinutosActual.$SegundosActual.".sql", "w+");
//	$__archivo = fopen("_"."$modulo-$accion".".sql", "w+");
##############################################################################/
##	Reformulación Presupuesto (NUEVO, MODIFICAR, REVISAR, APROBAR, GENERAR, AJAX)
##############################################################################/
if ($modulo == "formulario") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		##	-----------------
		##	valido
		if (!trim($CodOrganismo) || !trim($CodPresupuesto) || !trim($Fecha) || !trim($Periodo) || !trim($Descripcion)) die("Debe llenar los campos (*) obligatorios.");
		elseif (!validateDate($Fecha,'d-m-Y')) die("Formato <strong>Fecha</strong> incorrecta");
		elseif (!validateDate($Periodo,'Y-d')) die("Formato <strong>Periodo</strong> incorrecto");
		##	codigo
		$CodReformulacion = codigo('pv_reformulacion','CodReformulacion',4,['CodOrganismo'],[$CodOrganismo]);
		##	inserto
		$sql = "INSERT INTO pv_reformulacion
				SET
					CodOrganismo = '".$CodOrganismo."',
					CodReformulacion = '".$CodReformulacion."',
					CodPresupuesto = '".$CodPresupuesto."',
					Fecha = '".formatFechaAMD($Fecha)."',
					Periodo = '".$Periodo."',
					Descripcion = '".$Descripcion."',
					NroGaceta = '".$NroGaceta."',
					FechaGaceta = '".formatFechaAMD($FechaGaceta)."',
					NroResolucion = '".$NroResolucion."',
					FechaResolucion = '".formatFechaAMD($FechaResolucion)."',
					PreparadoPor = '".$PreparadoPor."',
					FechaPreparado = '".formatFechaAMD($FechaPreparado)."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		##	detalle
		$Secuencia = 0;
		for ($i=0; $i < count($cod_partida); $i++) {
			$sql = "INSERT INTO pv_reformulaciondet
					SET
						CodOrganismo = '".$CodOrganismo."',
						CodReformulacion = '".$CodReformulacion."',
						cod_partida = '".$cod_partida[$i]."',
						CodFuente = '".$CodFuente[$i]."',
						Estado = 'PR',
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
		if (!trim($CodOrganismo) || !trim($CodPresupuesto) || !trim($Fecha) || !trim($Periodo) || !trim($Descripcion)) die("Debe llenar los campos (*) obligatorios.");
		elseif (!validateDate($Fecha,'d-m-Y')) die("Formato <strong>Fecha</strong> incorrecta");
		elseif (!validateDate($Periodo,'Y-d')) die("Formato <strong>Periodo Fin</strong> incorrecto");
		##	actualizar
		$sql = "UPDATE pv_reformulacion
				SET
					Fecha = '".formatFechaAMD($Fecha)."',
					Periodo = '".$Periodo."',
					Descripcion = '".$Descripcion."',
					NroGaceta = '".$NroGaceta."',
					FechaGaceta = '".formatFechaAMD($FechaGaceta)."',
					NroResolucion = '".$NroResolucion."',
					FechaResolucion = '".formatFechaAMD($FechaResolucion)."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					CodReformulacion = '".$CodReformulacion."'";
		execute($sql);
		##	detalle
		$sql = "DELETE FROM pv_reformulaciondet WHERE CodOrganismo = '".$CodOrganismo."' AND CodReformulacion = '".$CodReformulacion."'";
		execute($sql);
		##	
		$Secuencia = 0;
		for ($i=0; $i < count($cod_partida); $i++) {
			$sql = "INSERT INTO pv_reformulaciondet
					SET
						CodOrganismo = '".$CodOrganismo."',
						CodReformulacion = '".$CodReformulacion."',
						cod_partida = '".$cod_partida[$i]."',
						CodFuente = '".$CodFuente[$i]."',
						Estado = 'PR',
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
		for ($i=0; $i < count($cod_partida); $i++) {
			$sql = "SELECT * FROM pv_presupuestodet WHERE CodOrganismo = '".$CodOrganismo."' AND CodPresupuesto = '".$CodPresupuesto."' AND CodFuente = '".$CodFuente[$i]."' AND cod_partida = '".$cod_partida[$i]."'";
			$field_re = getVar3($sql);
			if (count($field_re)) die("Partida <strong>$cod_partida[$i]</strong> ya formulada.");
		}
		##	actualizar
		$sql = "UPDATE pv_reformulacion
				SET
					Estado = '".$Estado."',
					AprobadoPor = '".$AprobadoPor."',
					FechaAprobado = '".formatFechaAMD($FechaAprobado)."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					CodReformulacion = '".$CodReformulacion."'";
		execute($sql);
		##	actualizar
		$sql = "UPDATE pv_reformulaciondet
				SET
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					CodReformulacion = '".$CodReformulacion."'";
		execute($sql);
		##	detalle
		$sql = "INSERT INTO pv_presupuestodet (
					CodOrganismo,
					CodPresupuesto,
					CodFuente,
					cod_partida,
					FlagReformulacion,
					Estado,
					UltimoUsuario,
					UltimaFecha
				)
				SELECT
					CodOrganismo,
					'$CodPresupuesto' AS CodPresupuesto,
					CodFuente,
					cod_partida,
					'S' AS FlagReformulacion,
					'AP' AS Estado,
					'".$_SESSION["USUARIO_ACTUAL"]."' AS UltimoUsuario,
					NOW() AS UltimaFecha
				FROM pv_reformulaciondet
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					CodReformulacion = '".$CodReformulacion."'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	anular
	elseif ($accion == "anular") {
		mysql_query("BEGIN");
		##	-----------------
		if ($Estado == 'AP') {
			$Estado = 'PR';
			##	consulto si partidas han sido ejecutadas
			$sql = "SELECT
						pd.cod_partida,
						pd.MontoCompromiso
					FROM
						pv_reformulaciondet rd
						INNER JOIN pv_reformulacion r ON (r.CodOrganismo = rd.CodOrganismo AND r.CodReformulacion = rd.CodReformulacion)
						INNER JOIN pv_presupuestodet pd ON (pd.CodOrganismo = r.CodOrganismo AND pd.CodPresupuesto = r.CodPresupuesto)
					WHERE
						r.CodOrganismo = '".$CodOrganismo."' AND
						r.CodReformulacion = '".$CodReformulacion."' AND
						pd.FlagReformulacion = 'S' AND
						pd.MontoCompromiso > 0.00";
			$field_ejecucion = getRecords($sql);
			if (count($field_ejecucion)) die("No se puede Anular esta reformulaci&oacute;n por contener partidas ya Ejecutadas");
		}
		elseif ($Estado == 'PR') $Estado = 'AN';
		##	actualizar
		$sql = "UPDATE pv_reformulacion
				SET
					Estado = '".$Estado."',
					AnuladoPor = '".$_SESSION['CODPERSONA_ACTUAL']."',
					FechaAnulado = NOW(),
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					CodReformulacion = '".$CodReformulacion."'";
		execute($sql);
		##	detalle
		$sql = "UPDATE pv_reformulaciondet
				SET
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					CodReformulacion = '".$CodReformulacion."'";
		execute($sql);
		##	elimino las agregadas
		##	-----------------
		mysql_query("COMMIT");
	}
}
elseif ($modulo == "validar") {
	//	modificar
	if($accion == "modificar") {
		list($CodOrganismo, $CodReformulacion) = explode('_', $codigo);
		$sql = "SELECT Estado FROM pv_reformulacion WHERE CodOrganismo = '$CodOrganismo' AND CodReformulacion = '$CodReformulacion'";
		$Estado = getVar3($sql);
		if ($Estado != 'PR') die('No puede modificar un proyecto <strong>'.printValores('proyecto-estado',$Estado).'</strong>');
	}
	//	aprobar
	elseif($accion == "aprobar") {
		list($CodOrganismo, $CodReformulacion) = explode('_', $codigo);
		$sql = "SELECT Estado FROM pv_reformulacion WHERE CodOrganismo = '$CodOrganismo' AND CodReformulacion = '$CodReformulacion'";
		$Estado = getVar3($sql);
		if ($Estado != 'PR') die('No puede aprobar un proyecto <strong>'.printValores('proyecto-estado',$Estado).'</strong>');
	}
	//	anular
	elseif($accion == "anular") {
		list($CodOrganismo, $CodReformulacion) = explode('_', $codigo);
		$sql = "SELECT Estado FROM pv_reformulacion WHERE CodOrganismo = '$CodOrganismo' AND CodReformulacion = '$CodReformulacion'";
		$Estado = getVar3($sql);
		if ($Estado == 'AN' || $Estado == 'GE') die('No puede anular un proyecto <strong>'.printValores('proyecto-estado',$Estado).'</strong>');
	}
}
elseif ($modulo == "ajax") {
	//	insertar linea
	if($accion == "partida_insertar") {
		##	detalle
		$sql = "SELECT * FROM pv_partida WHERE cod_partida = '$cod_partida'";
		$field_detalle = getRecord($sql);
		##	generica
		$cod_partida_generica = substr($cod_partida, 0, 7) . "00.00";
		$sql = "SELECT * FROM pv_partida WHERE cod_partida = '$cod_partida_generica'";
		$field_generica = getRecord($sql);
		##	partida
		$cod_partida_partida = substr($cod_partida, 0, 4) . "00.00.00";
		$sql = "SELECT * FROM pv_partida WHERE cod_partida = '$cod_partida_partida'";
		$field_partida = getRecord($sql);
		##	tipocuenta
		$cod_partida_tipocuenta = substr($cod_partida, 0, 1) . "00.00.00.00";
		$sql = "SELECT * FROM pv_partida WHERE cod_partida = '$cod_partida_tipocuenta'";
		$field_tipocuenta = getRecord($sql);
		##	---------------------------------
		$background="";
		$weight=(($FlagGenerar == 'S')?" color:red;":"");
		$detalle='detalle';
		$readonly = "";
		$id = $field_detalle['cod_partida'];
		?>
		<tr class="trListaBody" style="<?=$background.$weight?>" id="partida_<?=$id?>" onclick="clk($(this), 'partida', 'partida_<?=$id?>');">
			<td align="center">
				<select name="CodFuente[]" class="cell">
					<?=loadSelect2('pv_fuentefinanciamiento','CodFuente','Denominacion',$_PARAMETRO['FFMETASDEF'],20)?>
				</select>
			</td>
			<td align="center">
				<input type="hidden" name="cod_partida[]" value="<?=$id?>" <?=$readonly?> />
				<input type="hidden" name="tipo[]" value="<?=$field_detalle['tipo']?>" <?=$readonly?> />
				<?=$field_detalle['cod_partida']?>
			</td>
			<td><input type="text" value="<?=htmlentities($field_detalle['denominacion'])?>" class="cell2" style="<?=$weight?>" readonly /></td>
		</tr>|
		<?php
		##	---------------------------------
		$background="background-color:#DEDEDE;";
		$weight="font-weight:bold;";
		$detalle='generica';
		$readonly = "disabled";
		$id = $field_generica['cod_partida'];
		?>
		<tr class="trListaBody" style="<?=$background.$weight?>" id="partida_<?=$id?>">
			<td align="center">&nbsp;</td>
			<td align="center">
				<input type="hidden" name="cod_partida[]" value="<?=$id?>" <?=$readonly?> />
				<input type="hidden" name="tipo[]" value="<?=$field_generica['tipo']?>" <?=$readonly?> />
				<?=$field_generica['cod_partida']?>
			</td>
			<td><input type="text" value="<?=htmlentities($field_generica['denominacion'])?>" class="cell2" style="<?=$weight?>" readonly /></td>
		</tr>|
		<?php
		##	---------------------------------
		$background="background-color:#C7C7C7;";
		$weight="font-weight:bold;";
		$detalle='partida';
		$readonly = "disabled";
		$id = $field_partida['cod_partida'];
		?>
		<tr class="trListaBody" style="<?=$background.$weight?>" id="partida_<?=$id?>">
			<td align="center">&nbsp;</td>
			<td align="center">
				<input type="hidden" name="cod_partida[]" value="<?=$id?>" <?=$readonly?> />
				<input type="hidden" name="tipo[]" value="<?=$field_partida['tipo']?>" <?=$readonly?> />
				<?=$field_partida['cod_partida']?>
			</td>
			<td><input type="text" value="<?=htmlentities($field_partida['denominacion'])?>" class="cell2" style="<?=$weight?>" readonly /></td>
		</tr>|
		<?php
		##	---------------------------------
		$background="background-color:#B6B6B6;";
		$weight="font-weight:bold;";
		$disabled='disabled';
		$detalle='cuenta';
		$readonly = "disabled";
		$id = $field_tipocuenta['cod_partida'];
		?>
		<tr class="trListaBody" style="<?=$background.$weight?>" id="partida_<?=$id?>">
			<td align="center">&nbsp;</td>
			<td align="center">
				<input type="hidden" name="cod_partida[]" value="<?=$id?>" <?=$readonly?> />
				<input type="hidden" name="tipo[]" value="<?=$field_tipocuenta['tipo']?>" <?=$readonly?> />
				<?=$field_tipocuenta['cod_partida']?>
			</td>
			<td><input type="text" value="<?=htmlentities($field_tipocuenta['denominacion'])?>" class="cell2" style="<?=$weight?>" readonly /></td>
		</tr>|
		<?php
		echo $field_detalle['cod_partida'].'|';
		echo $field_generica['cod_partida'].'|';
		echo $field_partida['cod_partida'].'|';
		echo $field_tipocuenta['cod_partida'];
	}
}
?>