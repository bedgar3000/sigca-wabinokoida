<?php
session_start();
include("../../lib/fphp.php");
include("fphp.php");
//	--------------------------
if ($accion == "empleado_vacaciones_periodo_sel") {
	//	empleado
	$sql = "SELECT CodTipoNom FROM mastempleado WHERE CodPersona = '".$CodPersona."'";
	$query_empleado = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query_empleado) != 0) $field_empleado = mysql_fetch_array($query_empleado);
	
	//	consulto
	$sql = "SELECT *
			FROM rh_vacacionutilizacion
			WHERE
				CodPersona = '".$CodPersona."' AND
				NroPeriodo = '".$NroPeriodo."' AND
				CodTipoNom = '".$field_empleado['CodTipoNom']."'
			ORDER BY Secuencia";
	$query_utilizacion = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));	$i=0;
	$rows_utilizacion = mysql_num_rows($query_utilizacion);
	echo "$rows_utilizacion|";
	while ($field_utilizacion = mysql_fetch_array($query_utilizacion)) {
		++$i;
		?>
		<tr class="trListaBody" onclick="mClk(this, 'sel_utilizacion');" id="utilizacion_<?=$i?>">
			<th>
				<input type="hidden" name="NroPeriodo" value="<?=$field_utilizacion['NroPeriodo']?>" />
				<input type="hidden" name="Anio" id="Anio_<?=$i?>" value="<?=$field_utilizacion['Anio']?>" />
				<input type="hidden" name="CodSolicitud" id="CodSolicitud_<?=$i?>" value="<?=$field_utilizacion['CodSolicitud']?>" />
				<?=$i?>
			</th>
			<td>
				<select name="TipoUtilizacion" class="cell" disabled="disabled">
					<?=loadSelectValores("TIPO-VACACIONES", $field_utilizacion['TipoUtilizacion'], 0)?>
				</select>
			</td>
			<td>
				<input type="text" name="DiasUtiles" id="DiasUtiles_utilizacion_<?=$i?>" style="text-align:right;" class="cell" value="<?=number_format($field_utilizacion['DiasUtiles'], 2, ',', '.')?>" onFocus="numeroFocus(this);" onBlur="numeroBlur(this);" onchange="obtenerFechaTerminoVacacionUtilizacion('utilizacion_<?=$i?>');" disabled="disabled" />
			</td>
			<td>
				<input type="text" name="FechaInicio" id="FechaInicio_utilizacion_<?=$i?>" maxlength="10" style="text-align:center;" class="cell datepicker" value="<?=formatFechaDMA($field_utilizacion['FechaInicio'])?>" onchange="obtenerFechaTerminoVacacionUtilizacion('utilizacion_<?=$i?>');" disabled="disabled" />
			</td>
			<td>
				<input type="text" name="FechaFin" id="FechaFin_utilizacion_<?=$i?>" maxlength="10" style="text-align:center;" class="cell datepicker" value="<?=formatFechaDMA($field_utilizacion['FechaFin'])?>" onkeyup="setFechaDMA(this);" disabled="disabled" />
			</td>
		</tr>
		<?php
	}
	echo "|";
	
	//	pagos
	$sql = "SELECT
				vp.*,
				c.Descripcion AS NomConcepto
			FROM
				rh_vacacionpago vp
				INNER JOIN pr_concepto c ON (c.CodConcepto = vp.CodConcepto)
			WHERE
				vp.CodPersona = '".$CodPersona."' AND
				vp.NroPeriodo = '".$NroPeriodo."' AND
				vp.CodTipoNom = '".$field_empleado['CodTipoNom']."'
			ORDER BY Secuencia";
	$query_pagos = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));	$i=0;
	$rows_pagos = mysql_num_rows($query_pagos);
	echo "$rows_pagos|";
	while ($field_pagos = mysql_fetch_array($query_pagos)) {
		++$i;
		?>
		<tr class="trListaBody" onclick="mClk(this, 'sel_pagos');" id="pagos_<?=$i?>">
			<th>
				<input type="hidden" name="NroPeriodo" value="<?=$field_pagos['NroPeriodo']?>" />
				<?=$i?>
			</th>
			<td>
            	<input type="hidden" name="CodConcepto" value="<?=$field_pagos['CodConcepto']?>" />
                <?=htmlentities($field_pagos['NomConcepto'])?>
			</td>
			<td>
				<input type="text" name="DiasUtiles" id="DiasUtiles_pagos_<?=$i?>" style="text-align:right;" class="cell2" value="<?=number_format($field_pagos['DiasPago'], 2, ',', '.')?>" readonly="readonly" />
			</td>
			<td>
				<input type="text" name="FechaInicio" id="FechaInicio_pagos_<?=$i?>" style="text-align:center;" class="cell2" value="<?=formatFechaDMA($field_pagos['FechaInicio'])?>" readonly="readonly" />
			</td>
			<td>
				<input type="text" name="FechaFin" id="FechaFin_pagos_<?=$i?>" style="text-align:center;" class="cell2" value="<?=formatFechaDMA($field_pagos['FechaFin'])?>" readonly="readonly" />
			</td>
		</tr>
		<?php
	}
}
//	--------------------------

//	--------------------------
elseif ($accion == "empleado_vacaciones_utilizacion_linea") {
	?>
    <tr class="trListaBody" onclick="mClk(this, 'sel_utilizacion');" id="utilizacion_<?=$nrodetalle?>">
    	<th>
        	<input type="hidden" name="NroPeriodo" value="<?=$NroPeriodo?>" />
				<input type="hidden" name="Anio" id="Anio_<?=$nrodetalle?>" />
				<input type="hidden" name="CodSolicitud" id="CodSolicitud_<?=$nrodetalle?>" />
			<?=$nrodetalle?>
        </th>
        <td>
        	<select name="TipoUtilizacion" class="cell">
            	<?=loadSelectValores("TIPO-VACACIONES", "G", 0)?>
            </select>
        </td>
        <td>
        	<input type="text" name="DiasUtiles" id="DiasUtiles_utilizacion_<?=$nrodetalle?>" style="text-align:right;" class="cell" value="0,00" onFocus="numeroFocus(this);" onBlur="numeroBlur(this);" onchange="obtenerFechaTerminoVacacionUtilizacion('utilizacion_<?=$nrodetalle?>');" />
        </td>
        <td>
        	<input type="text" name="FechaInicio" id="FechaInicio_utilizacion_<?=$nrodetalle?>" maxlength="10" style="text-align:center;" class="cell datepicker" onchange="obtenerFechaTerminoVacacionUtilizacion('utilizacion_<?=$nrodetalle?>');" />
        </td>
        <td>
        	<input type="text" name="FechaFin" id="FechaFin_utilizacion_<?=$nrodetalle?>" maxlength="10" style="text-align:center;" class="cell datepicker" onkeyup="setFechaDMA(this);" />
        </td>
    </tr>
    <?php
}
//	--------------------------

//	--------------------------
elseif ($accion == "obtenerFechaTerminoVacacion") {
	$FechaTermino = getFechaFinHabiles($FechaSalida, $NroDias);
	$FechaIncorporacion = getFechaFinHabiles($FechaSalida, $NroDias+1);
	echo "$FechaTermino|$FechaIncorporacion";
}
//	--------------------------

elseif($accion == "getPasoSueldo") {
	//	cargo
	$field_cargo = getRecord("SELECT * FROM rh_puestos WHERE CodCargo = '$CodCargo'");
	$CategoriaCargo = getVar3("SELECT Descripcion FROM mastmiscelaneosdet WHERE CodMaestro = 'CATCARGO' AND CodDetalle = '$field_cargo[CategoriaCargo]'");
	//	pasos
	$Pasos = '';
	$field_pasos = getRecords("SELECT * FROM rh_nivelsalarial WhERE CategoriaCargo = '$field_cargo[CategoriaCargo]' AND Grado = '$field_cargo[Grado]'");
	foreach ($field_pasos as $f) {
		$Pasos .= '<option value="'.$f['Paso'].'">'.$f['Paso'].'</option>';
	}
	$jsondata = [
		'CategoriaCargo' => $CategoriaCargo,
		'Pasos' => $Pasos,
		'Paso' => $field_cargo['Paso'],
		'NivelSalarial' => $field_cargo['NivelSalarial'],
	];

    echo json_encode($jsondata);
    exit();

}

elseif($accion == "getSueldoxPaso") {
	//	cargo
	$field_cargo = getRecord("SELECT * FROM rh_puestos WHERE CodCargo = '$CodCargo'");
	//	grado
	$SueldoPromedio = getVar3("SELECT SueldoPromedio FROM rh_nivelsalarial WHERE CategoriaCargo = '$field_cargo[CategoriaCargo]' AND Grado = '$field_cargo[Grado]' AND Paso = '$Paso'");
	//	
	$jsondata = [
		'SueldoPromedio' => floatval($SueldoPromedio),
	];

    echo json_encode($jsondata);
    exit();

}
?>