<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion.sql", "w+");
##############################################################################/
##	Actividads (NUEVO, MODIFICAR, ELIMINAR, AJAX)
##############################################################################/
if ($modulo == "formulario") {
	//	agregar dias
	if ($accion == "dias") {
		mysql_query("BEGIN");
		##	-----------------
		##	valido
		if (!trim($CodPersona)) die("Debe llenar los campos (*) obligatorios.");
		##	
		execute("DELETE FROM pr_complementodias WHERE CodPersona = '".$CodPersona."' AND CodOrganismo = '".$CodOrganismo."' AND CodTipoNom = '".$CodTipoNom."' AND Periodo = '".$Periodo."' AND CodTipoProceso = '".$CodTipoProceso."'");
		$Anio = substr($Periodo, 0, 4);
		$Secuencia = codigo('pr_complementodias','Secuencia',6,['Anio'],[$Anio]);
		for ($i=0; $i < count($detalle_Fecha); $i++) {
			$Codigo = $Anio.str_repeat("0", 6-strlen($Secuencia)).$Secuencia;
			$sql = "INSERT INTO pr_complementodias
					SET
						Codigo = '".$Codigo."',
						CodPersona = '".$CodPersona."',
						Fecha = '".formatFechaAMD($detalle_Fecha[$i])."',
						HoraEntrada = '".formatHora24($detalle_HoraEntrada[$i],0)."',
						HoraSalida = '".formatHora24($detalle_HoraSalida[$i],0)."',
						TipoDia = '".$detalle_TipoDia[$i]."',
						Observaciones = '".$detalle_Observaciones[$i]."',
						Anio = '".$Anio."',
						Secuencia = '".$Secuencia."',
						CodOrganismo = '".$CodOrganismo."',
						CodTipoNom = '".$CodTipoNom."',
						Periodo = '".$Periodo."',
						CodTipoProceso = '".$CodTipoProceso."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
			++$Secuencia;
		}
		##	-----------------
		mysql_query("COMMIT");
	}
	//	agregar horas
	elseif ($accion == "horas") {
		mysql_query("BEGIN");
		##	-----------------
		##	valido
		if (!trim($CodPersona)) die("Debe llenar los campos (*) obligatorios.");
		##	
		execute("DELETE FROM pr_complementohoras WHERE CodPersona = '".$CodPersona."' AND CodOrganismo = '".$CodOrganismo."' AND CodTipoNom = '".$CodTipoNom."' AND Periodo = '".$Periodo."' AND CodTipoProceso = '".$CodTipoProceso."'");
		$Anio = substr($Periodo, 0, 4);
		$Secuencia = codigo('pr_complementohoras','Secuencia',6,['Anio'],[$Anio]);
		for ($i=0; $i < count($detalle_Fecha); $i++) {
			$Codigo = $Anio.str_repeat("0", 6-strlen($Secuencia)).$Secuencia;
			$sql = "INSERT INTO pr_complementohoras
					SET
						Codigo = '".$Codigo."',
						CodPersona = '".$CodPersona."',
						Fecha = '".formatFechaAMD($detalle_Fecha[$i])."',
						HoraEntrada = '".formatHora24($detalle_HoraEntrada[$i],0)."',
						HoraSalida = '".formatHora24($detalle_HoraSalida[$i],0)."',
						HoraSalidaReal = '".formatHora24($detalle_HoraSalidaReal[$i],0)."',
						HED = '".$detalle_HED[$i]."',
						HEN = '".$detalle_HEN[$i]."',
						TipoJornada = '".$detalle_TipoJornada[$i]."',
						Observaciones = '".$detalle_Observaciones[$i]."',
						Anio = '".$Anio."',
						Secuencia = '".$Secuencia."',
						CodOrganismo = '".$CodOrganismo."',
						CodTipoNom = '".$CodTipoNom."',
						Periodo = '".$Periodo."',
						CodTipoProceso = '".$CodTipoProceso."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
			++$Secuencia;
		}
		##	-----------------
		mysql_query("COMMIT");
	}
}
elseif ($modulo == "ajax") {
	if ($accion == "dia_insertar") {
		$id = $nro_detalle;
		?>
		<tr class="trListaBody" onclick="clk($(this), 'detalle', 'detalle_<?=$id?>');" id="detalle_<?=$id?>">
			<th>
				<?=$nro_detalle?>
			</th>
			<td>
				<input type="text" name="detalle_Fecha[]" style="text-align:center;" class="cell datepicker" maxlength="10" onchange="getHorario(this.value, '<?=$id?>'); getTotal();">
			</td>
			<td>
				<select name="detalle_TipoDia[]" class="cell" onchange="getTotal();">
					<?=getMiscelaneos('DD','TIPOJORDIA',20)?>
				</select>
			</td>
			<td>
				<input type="text" name="detalle_HoraEntrada[]" id="detalle_HoraEntrada<?=$id?>" style="text-align:center;" class="cell time" maxlength="8">
			</td>
			<td>
				<input type="text" name="detalle_HoraSalida[]" id="detalle_HoraSalida<?=$id?>" style="text-align:center;" class="cell time" maxlength="8">
			</td>
            <td>
                <textarea name="detalle_Observaciones[]" id="detalle_Observaciones" class="cell" style="height:18px;"></textarea>
            </td>
		</tr>
		<?php
	}
	elseif ($accion == "obtener_complementos_dias") {
		$nro_detalle = 0;
		$sql = "SELECT *
				FROM pr_complementodias
				WHERE
					CodOrganismo = '$CodOrganismo' AND
					CodTipoNom = '$CodTipoNom' AND
					Periodo = '$Periodo' AND
					CodTipoProceso = '$CodTipoProceso' AND
					CodPersona = '$CodPersona'";
		$field = getRecords($sql);
		foreach ($field as $f) {
			$id = ++$nro_detalle;
			?>
			<tr class="trListaBody" onclick="clk($(this), 'detalle', 'detalle_<?=$id?>');" id="detalle_<?=$id?>">
				<th>
					<?=$nro_detalle?>
				</th>
				<td>
					<input type="text" name="detalle_Fecha[]" value="<?=formatFechaDMA($f['Fecha'])?>" style="text-align:center;" class="cell datepicker" maxlength="10" onchange="getHorario(this.value, '<?=$id?>'); getTotal();">
				</td>
				<td>
					<select name="detalle_TipoDia[]" class="cell" onchange="getTotal();">
						<?=getMiscelaneos($f['TipoDia'],'TIPOJORDIA',20)?>
					</select>
				</td>
				<td>
					<input type="text" name="detalle_HoraEntrada[]" id="detalle_HoraEntrada<?=$id?>" value="<?=formatHora12($f['HoraEntrada'],0)?>" style="text-align:center;" class="cell time" maxlength="8">
				</td>
				<td>
					<input type="text" name="detalle_HoraSalida[]" id="detalle_HoraSalida<?=$id?>" value="<?=formatHora12($f['HoraSalida'],0)?>" style="text-align:center;" class="cell time" maxlength="8">
				</td>
	            <td>
	                <textarea name="detalle_Observaciones[]" id="detalle_Observaciones" class="cell" style="height:18px;"><?=htmlentities($f['Observaciones'])?></textarea>
	            </td>
			</tr>
			<?php
		}
	}
	elseif ($accion == "getHorario") {
		##	horario
		$CodHorario = getVar3("SELECT CodHorario FROM mastempleado WHERE CodPersona = '$CodPersona'");
		##	dia de la semana
		$Dia = getWeekDay($Fecha);
		##	horario laboral
		$sql = "SELECT
					hld.*,
					hl.FlagCorrido
				FROM
					rh_horariolaboraldet hld
					INNER JOIN rh_horariolaboral hl ON (hl.CodHorario = hld.CodHorario)
				WHERE hld.CodHorario = '$CodHorario' AND hld.Dia = '$Dia'";
		$field = getRecord($sql);
		$HoraEntrada = formatHora12($field['Entrada1'],0);
		if ($field['FlagCorrido'] == 'S') $HoraSalida = formatHora12($field['Salida1'],0);
		else $HoraSalida = formatHora12($field['Salida2'],0);
		##	
		$jsondata = [
			'HoraEntrada' => $HoraEntrada,
			'HoraSalida' => $HoraSalida,
		];
        echo json_encode($jsondata);
        exit();
	}
	elseif ($accion == "hora_insertar") {
		$id = $nro_detalle;
		?>
		<tr class="trListaBody" onclick="clk($(this), 'detalle', 'detalle_<?=$id?>');" id="detalle_<?=$id?>">
			<th>
				<?=$nro_detalle?>
			</th>
			<td>
				<input type="text" name="detalle_Fecha[]" style="text-align:center;" class="cell datepicker" maxlength="10" onchange="getHorario(this.value, '<?=$id?>'); getTotal();">
			</td>
			<td>
				<input type="text" name="detalle_HoraSalidaReal[]" id="detalle_HoraSalidaReal<?=$id?>" style="text-align:center;" class="cell time" maxlength="8" onchange="getTotalDia('<?=$id?>');">
			</td>
			<td>
				<input type="text" name="detalle_HED[]" id="detalle_HED<?=$id?>" style="text-align:center;" class="cell" onchange="getTotal();">
			</td>
			<td>
				<input type="text" name="detalle_HEN[]" id="detalle_HEN<?=$id?>" style="text-align:center;" class="cell" onchange="getTotal();">
			</td>
			<td>
				<select name="detalle_TipoJornada[]" id="detalle_TipoJornada<?=$id?>" class="cell" onchange="getTotal();">
					<?=getMiscelaneos('HD','TIPOJORHOR',20)?>
				</select>
			</td>
			<td>
				<input type="text" name="detalle_HoraEntrada[]" id="detalle_HoraEntrada<?=$id?>" style="text-align:center;" class="cell" maxlength="8" readonly>
			</td>
			<td>
				<input type="text" name="detalle_HoraSalida[]" id="detalle_HoraSalida<?=$id?>" style="text-align:center;" class="cell" maxlength="8" readonly>
			</td>
            <td>
                <textarea name="detalle_Observaciones[]" id="detalle_Observaciones" class="cell" style="height:18px;"></textarea>
            </td>
		</tr>
		<?php
	}
	elseif ($accion == "obtener_complementos_horas") {
		$nro_detalle = 0;
		$sql = "SELECT *
				FROM pr_complementohoras
				WHERE
					CodOrganismo = '$CodOrganismo' AND
					CodTipoNom = '$CodTipoNom' AND
					Periodo = '$Periodo' AND
					CodTipoProceso = '$CodTipoProceso' AND
					CodPersona = '$CodPersona'";
		$field = getRecords($sql);
		foreach ($field as $f) {
			$id = ++$nro_detalle;
			?>
			<tr class="trListaBody" onclick="clk($(this), 'detalle', 'detalle_<?=$id?>');" id="detalle_<?=$id?>">
				<th>
					<?=$nro_detalle?>
				</th>
				<td>
					<input type="text" name="detalle_Fecha[]" value="<?=formatFechaDMA($f['Fecha'])?>" style="text-align:center;" class="cell datepicker" maxlength="10" onchange="getHorario(this.value, '<?=$id?>'); getTotal();">
				</td>
				<td>
					<input type="text" name="detalle_HoraSalidaReal[]" id="detalle_HoraSalidaReal<?=$id?>" value="<?=formatHora12($f['HoraSalidaReal'],0)?>" style="text-align:center;" class="cell time" maxlength="8" onchange="getTotalDia('<?=$id?>');">
				</td>
				<td>
					<input type="text" name="detalle_HED[]" id="detalle_HED<?=$id?>" value="<?=$f['HED']?>" style="text-align:center;" class="cell" onchange="getTotal();">
				</td>
				<td>
					<input type="text" name="detalle_HEN[]" id="detalle_HEN<?=$id?>" value="<?=$f['HEN']?>" style="text-align:center;" class="cell" onchange="getTotal();">
				</td>
				<td>
					<select name="detalle_TipoJornada[]" id="detalle_TipoJornada<?=$id?>" class="cell" onchange="getTotal();">
						<?=getMiscelaneos($f['TipoJornada'],'TIPOJORHOR',20)?>
					</select>
				</td>
				<td>
					<input type="text" name="detalle_HoraEntrada[]" id="detalle_HoraEntrada<?=$id?>" value="<?=formatHora12($f['HoraEntrada'],0)?>" style="text-align:center;" class="cell" maxlength="8" readonly>
				</td>
				<td>
					<input type="text" name="detalle_HoraSalida[]" id="detalle_HoraSalida<?=$id?>" value="<?=formatHora12($f['HoraSalida'],0)?>" style="text-align:center;" class="cell" maxlength="8" readonly>
				</td>
	            <td>
	                <textarea name="detalle_Observaciones[]" id="detalle_Observaciones" class="cell" style="height:18px;"><?=htmlentities($f['Observaciones'])?></textarea>
	            </td>
			</tr>
			<?php
		}
	}
}
?>