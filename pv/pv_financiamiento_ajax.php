<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion-".$AnioActual.$MesActual.$DiaActual."_".$HorasActual.$MinutosActual.$SegundosActual.".sql", "w+");
##############################################################################/
##	Sectores (NUEVO, MODIFICAR, ELIMINAR, AJAX)
##############################################################################/
if ($modulo == "formulario") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		##	-----------------
		$TotalMontoAprobado = setNumero($TotalMontoAprobado);
		$MontoAprobado = setNumero($MontoAprobado);
		$FechaGaceta = formatFechaAMD($FechaGaceta);
		##	valido
		if (!trim($Ejercicio) || !trim($MontoAprobado)) die("Debe llenar los campos (*) obligatorios.");
		elseif ($MontoAprobado != $TotalMontoAprobado) die("El Monto Distribuido es distinto al Monto Aprobado");
		##	codigo
		$CodFinanciamiento = codigo('pv_financiamiento','CodFinanciamiento',4);
		##	inserto
		$sql = "INSERT INTO pv_financiamiento
				SET
					CodFinanciamiento = '$CodFinanciamiento',
					CodOrganismo = '$CodOrganismo',
					Ejercicio = '$Ejercicio',
					MontoAprobado = '$MontoAprobado',
					NroGaceta = '$NroGaceta',
					FechaGaceta = '$FechaGaceta',
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()";
		execute($sql);
		##	fuentes de financiamiento
		$Secuencia = 0;
		$_TotalMontoAprobado = 0;
		for ($i=0; $i < count($fuente_CodFuente); $i++) { 
			$fuente_MontoAprobado[$i] = setNumero($fuente_MontoAprobado[$i]);
			++$Secuencia;
			##	valido
			if (!trim($fuente_CodFuente[$i])) die("Debe seleccionar la Fuente de Financiamiento.");
			elseif (!trim($fuente_cod_partida[$i])) die("Debe seleccionar la Partida.");
			elseif (is_nan($fuente_MontoAprobado[$i])) die("Monto Aprobado incorrecto.");
			##	inserto
			$sql = "INSERT INTO pv_financiamientodetalle
					SET
						CodFinanciamiento = '$CodFinanciamiento',
						Secuencia = '$Secuencia',
						CodFuente = '$fuente_CodFuente[$i]',
						MontoAprobado = '$fuente_MontoAprobado[$i]',
						cod_partida = '$fuente_cod_partida[$i]',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()";
			execute($sql);
			$_TotalMontoAprobado += $fuente_MontoAprobado[$i];
		}
		##	-----------------
		mysql_query("COMMIT");
	}
	//	modificar
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		##	-----------------
		$TotalMontoAprobado = setNumero($TotalMontoAprobado);
		$MontoAprobado = setNumero($MontoAprobado);
		$FechaGaceta = formatFechaAMD($FechaGaceta);
		##	valido
		if (!trim($MontoAprobado)) die("Debe llenar los campos (*) obligatorios.");
		elseif ($MontoAprobado != $TotalMontoAprobado) die("El Monto Distribuido es distinto al Monto Aprobado");
		##	actualizo
		$sql = "UPDATE pv_financiamiento
				SET
					MontoAprobado = '$MontoAprobado',
					NroGaceta = '$NroGaceta',
					FechaGaceta = '$FechaGaceta',
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodFinanciamiento = '$CodFinanciamiento'";
		execute($sql);
		##	fuentes de financiamiento
		$Secuencia = 0;
		$_TotalMontoAprobado = 0;
		execute("DELETE FROM pv_financiamientodetalle WHERE CodFinanciamiento = '".$CodFinanciamiento."'");
		for ($i=0; $i < count($fuente_CodFuente); $i++) { 
			$fuente_MontoAprobado[$i] = setNumero($fuente_MontoAprobado[$i]);
			++$Secuencia;
			##	valido
			if (!trim($fuente_CodFuente[$i])) die("Debe seleccionar la Fuente de Financiamiento.");
			elseif (!trim($fuente_cod_partida[$i])) die("Debe seleccionar la Partida.");
			elseif (is_nan($fuente_MontoAprobado[$i])) die("Monto Aprobado incorrecto.");
			##	inserto
			$sql = "INSERT INTO pv_financiamientodetalle
					SET
						CodFinanciamiento = '$CodFinanciamiento',
						Secuencia = '$Secuencia',
						CodFuente = '$fuente_CodFuente[$i]',
						MontoAprobado = '$fuente_MontoAprobado[$i]',
						cod_partida = '$fuente_cod_partida[$i]',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()";
			execute($sql);
			$_TotalMontoAprobado += $fuente_MontoAprobado[$i];
		}
		##	-----------------
		mysql_query("COMMIT");
	}
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "DELETE FROM pv_financiamiento WHERE CodFinanciamiento = '".$registro."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
elseif ($modulo == "ajax") {
	if ($accion == "fuente_insertar") {
		$id = $nro_detalle;
		?>
		<tr class="trListaBody" onclick="clk($(this), 'fuente', 'fuente_<?=$id?>');" id="fuente_<?=$id?>">
			<th>
				<?=$id?>
			</th>
			<td>
				<select name="fuente_CodFuente[]" class="cell">
					<option value="">&nbsp;</option>
					<?=loadSelect2('pv_fuentefinanciamiento','CodFuente','Denominacion','',10)?>
				</select>
			</td>
			<td>
				<input type="text" name="fuente_MontoAprobado[]" value="0,00" style="text-align:right;" class="cell currency" onchange="setMontos();">
			</td>
            <td>
                <input type="text" name="fuente_cod_partida[]" id="fuente_cod_partida<?=$id?>" class="cell2" style="text-align:center;" readonly />
            </td>
		</tr>
		<?php
	}
}
?>