<?php
include("fphp.php");
if ($accion == "getDiasHabiles") {
	echo getDiasHabiles($FechaInicio, $FechaFin);
}

elseif ($accion == "getDescripcionPersona") {
	$filtro_flag = "";	
	if ($flagcliente == "S") { $filtro_flag .= " AND (EsCliente = 'S' "; }
	if ($flagproveedor == "S") {
		if ($filtro_flag == "") $filtro_flag .= " AND (EsProveedor = 'S' ";
		else $filtro_flag .= " OR EsProveedor = 'S' ";
	}
	if ($flagempleado == "S") {
		if ($filtro_flag == "") $filtro_flag .= " AND ((EsEmpleado = 'S' AND Estado = 'A') ";
		else $filtro_flag .= " OR (EsEmpleado = 'S' AND Estado = 'A') ";
	}
	if ($flagotros == "S") {
		if ($filtro_flag == "") $filtro_flag .= " AND (EsOtros = 'S' ";
		else $filtro_flag .= " OR EsOtros = 'S' ";
	}
	if ($filtro_flag != "") $filtro_flag = "$filtro_flag)";
	$sql = "SELECT CodPersona, NomCompleto FROM mastpersonas WHERE CodPersona = '".$codigo."' $filtro_flag";
	$query = mysql_query($sql) or die($sql.mysql_error());
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	echo "|$field[CodPersona]|$field[NomCompleto]";
}

elseif ($accion == "getDescripcionEmpleado") {
	$sql = "SELECT
				p.CodPersona,
				p.NomCompleto,
				e.CodEmpleado
			FROM
				mastpersonas p
				INNER JOIN mastempleado e ON (p.CodPersona = e.CodPersona)
			WHERE e.CodEmpleado = '".$codigo."'";
	$query = mysql_query($sql) or die($sql.mysql_error());
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	echo "|$field[CodEmpleado]|$field[NomCompleto]|$field[CodPersona]";
}

elseif ($accion == "getDescripcionCuenta") {
	$sql = "SELECT CodCuenta, Descripcion FROM ac_mastplancuenta WHERE CodCuenta= '".$codigo."'";
	$query = mysql_query($sql) or die($sql.mysql_error());
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	echo "|$field[CodCuenta]|$field[Descripcion]";
}

elseif ($accion == "getDescripcionCuentaPub20") {
	$sql = "SELECT CodCuenta, Descripcion FROM ac_mastplancuenta20 WHERE CodCuenta= '".$codigo."'";
	$query = mysql_query($sql) or die($sql.mysql_error());
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	echo "|$field[CodCuenta]|$field[Descripcion]";
}

elseif ($accion == "getDescripcionPartida") {
	$sql = "SELECT cod_partida, denominacion FROM pv_partida WHERE cod_partida = '".$codigo."'";
	$query = mysql_query($sql) or die($sql.mysql_error());
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	echo "|$field[cod_partida]|$field[denominacion]";
}

elseif ($accion == "getDescripcionPartidaCuenta") {
	$sql = "SELECT
				p.cod_partida,
				p.denominacion AS NomPartida,
				pc.CodCuenta,
				pc.Descripcion AS NomCuenta
			FROM
				pv_partida p
				INNER JOIN ac_mastplancuenta pc ON (p.CodCuenta = pc.CodCuenta)
			WHERE p.cod_partida = '".$codigo."'";
	$query = mysql_query($sql) or die($sql.mysql_error());
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	echo "|$field[cod_partida]|$field[NomPartida]|$field[CodCuenta]|$field[NomCuenta]";
}

elseif ($accion == "getDescripcionPartidaDisponible") {
	$sql = "SELECT p.cod_partida, p.denominacion
			FROM
				pv_partida p
				INNER JOIN pv_presupuestodet pvd ON (pvd.cod_partida = p.cod_partida)
				INNER JOIN pv_presupuesto pv ON (pv.Organismo = pvd.Organismo AND
												 pv.CodPresupuesto = pvd.CodPresupuesto AND
												 pv.EjercicioPpto = '".substr($Ahora, 0, 4)."')
			WHERE
				p.cod_partida = '".$codigo."' AND
				pv.Organismo = '".$CodOrganismo."' AND
				pv.EjercicioPpto = '".substr($Ahora, 0, 4)."'";
	$query = mysql_query($sql) or die($sql.mysql_error());
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	echo "|$field[cod_partida]|$field[denominacion]";
}

elseif ($accion == "getDescripcionCCosto") {
	$sql = "SELECT CodCentroCosto, Descripcion FROM ac_mastcentrocosto WHERE CodCentroCosto = '".$codigo."'";
	$query = mysql_query($sql) or die($sql.mysql_error());
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	echo "|$field[CodCentroCosto]|$field[Descripcion]";
}

elseif ($accion == "getCCosto") {
	$sql = "SELECT CodCentroCosto, Codigo, Descripcion FROM ac_mastcentrocosto WHERE Codigo = '".$codigo."'";
	$query = mysql_query($sql) or die($sql.mysql_error());
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	echo "|$field[Codigo]|$field[CodCentroCosto]|$field[Descripcion]";
}

elseif ($accion == "getDescripcionClasificacionActivo") {
	$sql = "SELECT CodClasificacion, Descripcion FROM af_clasificacionactivo WHERE CodClasificacion = '".$codigo."'";
	$query = mysql_query($sql) or die($sql.mysql_error());
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	echo "|$field[CodClasificacion]|$field[Descripcion]";
}

elseif ($accion == "sellistado_desarrollo_carreras") {
	//	consulto datos generales
	$sql = "SELECT
				p.CodPersona,
				p.NomCompleto,
				p.Ndocumento,
				e.CodEmpleado,
				e.Fingreso,
				e.CodCargo,
				e.CodCargoTemp,
				pu1.DescripCargo AS NomCargo,
				pu2.DescripCargo AS NomCargoTemp,
				pu1.Grado AS Grado,
				pu2.Grado AS GradoTemp
			FROM
				mastpersonas p
				INNER JOIN mastempleado e ON (p.CodPersona = e.CodPersona)
				INNER JOIN rh_puestos pu1 ON (e.CodCargo = pu1.CodCargo)
				LEFT JOIN rh_puestos pu2 ON (e.CodCargoTemp = pu2.CodCargo)
			WHERE p.CodPersona = '".$CodPersona."'";
	$query_datos = mysql_query($sql) or die($sql.mysql_error());
	if (mysql_num_rows($query_datos)) $field_datos = mysql_fetch_array($query_datos);
	if ($field_datos['CodCargoTemp']) $CodCargo = $field_datos['CodCargoTemp']; else $CodCargo = $field_datos['CodCargo'];
	if ($field_datos['NomCargoTemp']) $DescripCargo = $field_datos['NomCargoTemp']; else $DescripCargo = $field_datos['NomCargo'];
	$Fingreso = formatFechaDMA($field_datos['Fingreso']);
	echo "$field_datos[CodEmpleado]|$field_datos[NomCompleto]|$field_datos[Ndocumento]|$Fingreso|$DescripCargo|$field_datos[Grado]|$CodCargo|.|";
	
	//	nivel academico
	$sql = "SELECT
				ei.Secuencia,
				ei.FechaGraduacion,
				ei.CodGradoInstruccion,
				ei.Area,
				ei.CodProfesion,
				ei.Nivel,
				ei.CodCentroEstudio,
				gi.Descripcion AS NomGradoInstruccion,
				md.Descripcion AS NomArea,
				p.Descripcion AS NomProfesion
			FROM
				rh_empleado_instruccion ei
				INNER JOIN rh_gradoinstruccion gi ON (ei.CodGradoInstruccion = gi.CodGradoInstruccion)
				LEFT JOIN mastmiscelaneosdet md ON (ei.Area = md.CodDetalle AND CodMaestro = 'AREA')
				LEFT JOIN rh_profesiones p ON (ei.CodProfesion = p.CodProfesion)
			WHERE ei.CodPersona = '".$CodPersona."'
			ORDER BY FechaGraduacion DESC, Secuencia";
	$query_nivel = mysql_query($sql) or die($sql.mysql_error());
	while ($field_nivel = mysql_fetch_array($query_nivel)) {
		$nronivel++;
		if ($field_nivel['CodProfesion'] != "") $Profesion = $field_nivel['NomProfesion'];
		else $Profesion = $field_nivel['NomGradoInstruccion']." EN ".$field_nivel['NomArea'];
		?>
		<tr class="trListaBody" onclick="mClk(this, 'sel_nivel');" id="nivel_<?=$nronivel?>">
			<td align="center">
                <input type="hidden" name="Secuencia" value="<?=$field_nivel['Secuencia']?>" />
                <input type="hidden" name="CodGradoInstruccion" value="<?=$field_nivel['CodGradoInstruccion']?>" />
                <input type="hidden" name="Area" value="<?=$field_nivel['Area']?>" />
                <input type="hidden" name="CodProfesion" value="<?=$field_nivel['CodProfesion']?>" />
                <input type="hidden" name="Nivel" value="<?=$field_nivel['Nivel']?>" />
                <input type="hidden" name="CodCentroEstudio" value="<?=$field_nivel['CodCentroEstudio']?>" />
                <input type="hidden" name="FechaGraduacion" value="<?=$field_nivel['FechaGraduacion']?>" />
				<?=$nronivel?>
			</td>
			<td>
				<?=$Profesion?>
			</td>
			<td align="center">
				<?=formatFechaDMA($field_nivel['FechaGraduacion'])?>
			</td>
		</tr>
		<?php
	}
	
	echo "|.|";
	
	//	cursos realizados en el area
	$sql = "SELECT
				ec.*,
				c.Descripcion AS NomCurso
			FROM
				rh_empleado_cursos ec
				INNER JOIN rh_cursos c ON (ec.CodCurso = c.CodCurso)
			WHERE
				ec.CodPersona = '".$CodPersona."' AND
				ec.FlagArea = 'S'
			ORDER BY FechaCulminacion DESC, Secuencia";
	$query_cursosa = mysql_query($sql) or die($sql.mysql_error());
	while ($field_cursosa = mysql_fetch_array($query_cursosa)) {
		$nrocursosa++;
		if ($field_cursosa['CodProfesion'] != "") $Profesion = $field_cursosa['NomProfesion'];
		else $Profesion = $field_cursosa['NomGradoInstruccion']." EN ".$field_cursosa['NomArea'];
		?>
		<tr class="trListaBody" onclick="mClk(this, 'sel_cursosa');" id="cursosa_<?=$nrocursosa?>">
			<td align="center">
                <input type="hidden" name="Secuencia" value="<?=$field_cursosa['Secuencia']?>" />
                <input type="hidden" name="CodCurso" value="<?=$field_cursosa['CodCurso']?>" />
                <input type="hidden" name="TipoCurso" value="<?=$field_cursosa['TipoCurso']?>" />
                <input type="hidden" name="CodCentroEstudio" value="<?=$field_cursosa['CodCentroEstudio']?>" />
                <input type="hidden" name="FechaCulminacion" value="<?=$field_cursosa['FechaCulminacion']?>" />
                <input type="hidden" name="TotalHoras" value="<?=$field_cursosa['TotalHoras']?>" />
                <input type="hidden" name="AniosVigencia" value="<?=$field_cursosa['AniosVigencia']?>" />
                <input type="hidden" name="FlagInstitucional" value="<?=$field_cursosa['FlagInstitucional']?>" />
                <input type="hidden" name="FlagPago" value="<?=$field_cursosa['FlagPago']?>" />
                <input type="hidden" name="FlagArea" value="<?=$field_cursosa['FlagArea']?>" />
				<?=$nrocursosa?>
			</td>
			<td>
				<?=$field_cursosa['NomCurso']?>
			</td>
			<td align="center">
				<?=($field_cursosa['FechaCulminacion'])?>
			</td>
		</tr>
		<?php
	}
	
	echo "|.|";
	
	//	cursos realizados en formacion general
	$sql = "SELECT
				ec.*,
				c.Descripcion AS NomCurso
			FROM
				rh_empleado_cursos ec
				INNER JOIN rh_cursos c ON (ec.CodCurso = c.CodCurso)
			WHERE
				ec.CodPersona = '".$CodPersona."' AND
				ec.FlagArea = 'N'
			ORDER BY FechaCulminacion DESC, Secuencia";
	$query_cursosfg = mysql_query($sql) or die($sql.mysql_error());
	while ($field_cursosfg = mysql_fetch_array($query_cursosfg)) {
		$nrocursosfg++;
		?>
		<tr class="trListaBody" onclick="mClk(this, 'sel_cursosfg');" id="cursosfg_<?=$nrocursosfg?>">
			<td align="center">
                <input type="hidden" name="Secuencia" value="<?=$field_cursosfg['Secuencia']?>" />
                <input type="hidden" name="CodCurso" value="<?=$field_cursosfg['CodCurso']?>" />
                <input type="hidden" name="TipoCurso" value="<?=$field_cursosfg['TipoCurso']?>" />
                <input type="hidden" name="CodCentroEstudio" value="<?=$field_cursosfg['CodCentroEstudio']?>" />
                <input type="hidden" name="FechaCulminacion" value="<?=$field_cursosfg['FechaCulminacion']?>" />
                <input type="hidden" name="TotalHoras" value="<?=$field_cursosfg['TotalHoras']?>" />
                <input type="hidden" name="AniosVigencia" value="<?=$field_cursosfg['AniosVigencia']?>" />
                <input type="hidden" name="FlagInstitucional" value="<?=$field_cursosfg['FlagInstitucional']?>" />
                <input type="hidden" name="FlagPago" value="<?=$field_cursosfg['FlagPago']?>" />
                <input type="hidden" name="FlagArea" value="<?=$field_cursosfg['FlagArea']?>" />
				<?=$nrocursosfg?>
			</td>
			<td>
				<?=$field_cursosfg['NomCurso']?>
			</td>
			<td align="center">
				<?=($field_cursosfg['FechaCulminacion'])?>
			</td>
		</tr>
		<?php
	}
	
	echo "|.|";
	
	//	competencias conductuales adquiridas
	$sql = "SELECT
				ef.Competencia,
				ef.Descripcion,
				ef.ValorRequerido,
				ef.ValorMinimo,
				ef.Estado,
				ee.Calificacion,
				fv.Explicacion,
				fv.Explicacion2
			FROM
				rh_evaluacionfactores ef
				INNER JOIN rh_empleado_evaluacion ee ON (ef.Competencia = ee.Competencia)
				INNER JOIN rh_evaluacionempleado eve ON (ee.CodOrganismo = eve.CodOrganismo AND
														 ee.Periodo = eve.Periodo AND
														 ee.Secuencia = eve.Secuencia AND
														 ee.CodPersona = eve.CodPersona AND
														 ee.Evaluador = eve.Evaluador)
				LEFT JOIN rh_factorvalor fv ON (ee.Competencia = fv.Competencia AND
												ee.Calificacion = fv.Grado)
			WHERE
				ef.Estado = 'A' AND
				ee.CodPersona = '".$CodPersona."' AND
				ee.Calificacion >= ef.ValorMinimo AND
				eve.Estado = 'EV'
			ORDER BY Competencia";
	$query_competenciasca = mysql_query($sql) or die($sql.mysql_error());
	while ($field_competenciasca = mysql_fetch_array($query_competenciasca)) {
		$nrocompetenciasca++;
		?>
		<tr class="trListaBody" onclick="mClk(this, 'sel_competenciasca');" id="competenciasca_<?=$nrocompetenciasca?>">
			<td align="center">
               	<input type="hidden" name="Secuencia" value="<?=$nrocompetenciasca?>" />
                <input type="hidden" name="Competencia" value="<?=$field_competenciasca['Competencia']?>" />
                <input type="hidden" name="FlagAdquiridas" value="S" />
				<?=$nrocompetenciasca?>
			</td>
			<td>
				<strong><?=$field_competenciasca['Descripcion']?></strong><br />
                <?=$field_competenciasca['Explicacion']?><br />
                <?=$field_competenciasca['Explicacion2']?>
			</td>
		</tr>
		<?php
	}
	
	echo "|.|";
	
	//	competencias conductuales por adquirir
	$sql = "SELECT
				ef.Competencia,
				ef.Descripcion,
				ef.ValorRequerido,
				ef.ValorMinimo,
				ef.Estado,
				ee.Calificacion,
				fv.Explicacion,
				fv.Explicacion2
			FROM
				rh_evaluacionfactores ef
				INNER JOIN rh_empleado_evaluacion ee ON (ef.Competencia = ee.Competencia)
				INNER JOIN rh_evaluacionempleado eve ON (ee.CodOrganismo = eve.CodOrganismo AND
														 ee.Periodo = eve.Periodo AND
														 ee.Secuencia = eve.Secuencia AND
														 ee.CodPersona = eve.CodPersona AND
														 ee.Evaluador = eve.Evaluador)
				LEFT JOIN rh_factorvalor fv ON (ee.Competencia = fv.Competencia AND
												ee.Calificacion = fv.Grado)
			WHERE
				ef.Estado = 'A' AND
				ee.CodPersona = '".$CodPersona."' AND
				ee.Calificacion < ef.ValorMinimo AND
				eve.Estado = 'EV'
			ORDER BY Competencia";
	$query_competenciascgpa = mysql_query($sql) or die($sql.mysql_error());
	while ($field_competenciascgpa = mysql_fetch_array($query_competenciascgpa)) {
		$nrocompetenciascgpa++;
		?>
		<tr class="trListaBody" onclick="mClk(this, 'sel_competenciascgpa');" id="competenciascgpa_<?=$nrocompetenciascgpa?>">
			<td align="center">
               	<input type="hidden" name="Secuencia" value="<?=$nrocompetenciascgpa?>" />
                <input type="hidden" name="Competencia" value="<?=$field_competenciascgpa['Competencia']?>" />
                <input type="hidden" name="FlagAdquiridas" value="N" />
				<?=$nrocompetenciascgpa?>
			</td>
			<td>
				<strong><?=$field_competenciascgpa['Descripcion']?></strong><br />
                <?=$field_competenciascgpa['Explicacion']?><br />
                <?=$field_competenciascgpa['Explicacion2']?>
			</td>
		</tr>
		<?php
	}
	
	echo "|.|";
	
	//	habllidades y destrezas tecnicas adquiridas
	$sql = "SELECT
				ed.Descripcion
			FROM
				rh_empleado_desempenio ed
				INNER JOIN rh_evaluacionempleado ee ON (ed.CodOrganismo = ee.CodOrganismo AND
														ed.Periodo = ee.Periodo AND
														ed.Secuencia = ee.Secuencia AND
														ed.CodPersona = ee.CodPersona AND
														ed.Evaluador = ee.Evaluador)
			WHERE
				ed.CodPersona = '".$CodPersona."' AND
				ed.Tipo = 'F' AND
				ee.Estado = 'EV'
			ORDER BY ed.SecuenciaDesempenio";
	$query_fortalezasa = mysql_query($sql) or die($sql.mysql_error());
	while ($field_fortalezasa = mysql_fetch_array($query_fortalezasa)) {
		$nrofortalezasa++;
		?>
		<tr class="trListaBody" onclick="mClk(this, 'sel_fortalezasa');" id="fortalezasa_<?=$nrofortalezasa?>">
			<td align="center">
				<?=$nrofortalezasa?>
			</td>
			<td>
				<?=$field_fortalezasa['Descripcion']?>
			</td>
		</tr>
		<?php
	}
	
	echo "|.|";
	
	//	habllidades y destrezas tecnicas por adquirir
	$sql = "SELECT
				ed.Descripcion
			FROM
				rh_empleado_desempenio ed
				INNER JOIN rh_evaluacionempleado ee ON (ed.CodOrganismo = ee.CodOrganismo AND
														ed.Periodo = ee.Periodo AND
														ed.Secuencia = ee.Secuencia AND
														ed.CodPersona = ee.CodPersona AND
														ed.Evaluador = ee.Evaluador)
			WHERE
				ed.CodPersona = '".$CodPersona."' AND
				ed.Tipo = 'D' AND
				ee.Estado = 'EV'
			ORDER BY ed.SecuenciaDesempenio";
	$query_fortalezaspa = mysql_query($sql) or die($sql.mysql_error());
	while ($field_fortalezaspa = mysql_fetch_array($query_fortalezaspa)) {
		$nrofortalezaspa++;
		?>
		<tr class="trListaBody" onclick="mClk(this, 'sel_fortalezaspa');" id="fortalezaspa_<?=$nrofortalezaspa?>">
			<td align="center">
				<?=$nrofortalezaspa?>
			</td>
			<td>
				<?=$field_fortalezaspa['Descripcion']?>
			</td>
		</tr>
		<?php
	}
	
	echo "|.|";
	
	//	capacitacion requeridas para desarrollar competencias conductuales
	$sql = "SELECT
				en.Necesidad,
				en.Objetivo,
				en.Prioridad,
				c.Descripcion AS NomCurso
			FROM
				rh_empleado_necesidad en
				INNER JOIN rh_cursos c ON (en.CodCurso = c.CodCurso)
				INNER JOIN rh_evaluacionempleado ee ON (en.CodOrganismo = ee.CodOrganismo AND
														en.Periodo = ee.Periodo AND
														en.Secuencia = ee.Secuencia AND
														en.CodPersona = ee.CodPersona AND
														en.Evaluador = ee.Evaluador)
			WHERE
				en.CodPersona = '".$CodPersona."' AND
				ee.Estado = 'EV'
			ORDER BY en.SecuenciaDesempenio";
	$query_capacitacioncc = mysql_query($sql) or die($sql.mysql_error());
	while ($field_capacitacioncc = mysql_fetch_array($query_capacitacioncc)) {
		$nrocapacitacioncc++;
		?>
		<tr class="trListaBody" onclick="mClk(this, 'sel_capacitacioncc');" id="capacitacioncc_<?=$nrocapacitacioncc?>">
			<td align="center">
				<?=$nrocapacitacioncc?>
            </td>
            <td>
                <strong><?=$field_capacitacioncc['Necesidad']?></strong><br />
                <?=$field_capacitacioncc['Objetivo']?>
            </td>
            <td>
                <?=$field_capacitacioncc['NomCurso']?>
            </td>
            <td align="center">
                <?=strtoupper(printValoresGeneral("PRIORIDAD", $field_capacitacioncc['Prioridad']))?>
            </td>
		</tr>
		<?php
	}
}

elseif ($accion == "actuacion_fiscal_auditores_insertar") {
	$sql = "SELECT
				p.CodPersona,
				p.NomCompleto,
				e.CodEmpleado,
				pu.DescripCargo
			FROM
				mastpersonas p
				INNER JOIN mastempleado e ON (p.CodPersona = e.CodPersona)
				INNER JOIN rh_puestos pu ON (e.CodCargo = pu.CodCargo)
			WHERE p.CodPersona = '".$CodPersona."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query)) $field = mysql_fetch_array($query);
	?>
    <td align="center">
        <input type="hidden" name="CodPersona" value="<?=$field['CodPersona']?>" />
        <input type="radio" name="FlagCoordinador" value="S" />
    </td>
    <td align="center">
        <?=$field['CodEmpleado']?>
    </td>
    <td>
        <?=$field['NomCompleto']?>
    </td>
    <td>
        <?=$field['DescripCargo']?>
    </td>
	<?php
}

elseif ($accion == "selListadoProrrogas") {
	?>
    <table width="1075" class="tblLista">
        <thead>
        <tr>
            <th scope="col" colspan="2" style="background-color:#FFF;">&nbsp;</th>
            <th scope="col" colspan="3">Planificaci&oacute;n Inicial</th>
            <th scope="col" colspan="4">Planificaci&oacute;n Real</th>
            <th scope="col" colspan="3">Ejecuci&oacute;n</th>
            <th scope="col" colspan="2" style="background-color:#FFF;">&nbsp;</th>
        </tr>
        <tr>
            <th scope="col" width="20">Est.</th>
            <th scope="col" align="left">Actividad</th>
            <th scope="col" width="30">Dias</th>
            <th scope="col" width="60">Inicio</th>
            <th scope="col" width="60">Fin</th>
            <th scope="col" width="30">Prorr. Acu.</th>
            <th scope="col" width="30">Prorr.</th>
            <th scope="col" width="60">Inicio Real</th>
            <th scope="col" width="60">Fin Real</th>
            <th scope="col" width="30">Dias Ejec.</th>
            <th scope="col" width="60">Fecha Cierre</th>
            <th scope="col" width="60">Fecha Registro</th>
            <th scope="col" width="25">Au. Ar.</th>
            <th scope="col" width="25">No Afe.</th>
        </tr>
        </thead>
        
        <tbody id="lista_actividades">
        <?php
        $nroactividades = 0;
        if ($opcion != "nuevo") {
            $sql = "SELECT
                        afd.*,
                        a.CodFase,
                        a.Descripcion AS NomActividad,
                        a.FlagNoAfectoPlan,
                        a.FlagAutoArchivo,
                        f.Descripcion AS NomFase,
						(SELECT SUM(Prorroga)
						 FROM pf_prorroga
						 WHERE
							Estado = 'AP' AND
							CodActividad = afd.CodActividad AND
							CodActuacion = afd.CodActuacion AND
							CodActividad NOT IN (SELECT afd2.CodActividad
											 	 FROM
											 		pf_actuacionfiscaldetalle afd2
													INNER JOIN pf_actividades a ON (afd2.CodActividad = a.CodActividad)
											 	 WHERE
													afd2.Estado = 'EJ' AND
													a.FlagNoAfectoPlan = 'N'
												)
						) AS ProrrogaAcu
                    FROM
                        pf_actuacionfiscaldetalle afd
                        INNER JOIN pf_actividades a ON (afd.CodActividad = a.CodActividad)
                        INNER JOIN pf_fases f ON (a.CodFase = f.CodFase)
                    WHERE
						afd.CodActuacion = '".$CodActuacion."' AND
						a.Estado = 'A'
                    ORDER BY CodActividad";
            $query_actividades = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
            while ($field_actividades = mysql_fetch_array($query_actividades)) {
                $nroactividades++;
                if ($grupo != $field_actividades['CodFase']) {
                    if ($nroactividades > 1)  {
                        ?>
                        <tr>
                            <th colspan="2">&nbsp;</th>
                            <th align="center">
                                <span style="font-weight:bold;"><?=$fase_duracion?></span>
                            </th>
                            <th colspan="2">&nbsp;</th>
                            <th align="center">
                                <span style="font-weight:bold;"><?=$fase_prorroga?></span>
                            </th>
                        </tr>
                        <?php
                        $fase_duracion = 0;
                        $fase_prorroga = 0;
                    }
                    $grupo = $field_actividades['CodFase'];
                    ?>
                    <tr class="trListaBody2">
                        <td colspan="2"><?=$field_actividades['CodFase']?> <?=$field_actividades['NomFase']?></td>
                    </tr>
                    <?php
                }
                ?>
                <tr class="trListaBody" id="<?=$field_actividades['CodActividad']?>">
                    <td align="center"><?=printEstadoActuacion($field_actividades['Estado'])?></td>
                    <td>
                        <input type="hidden" name="CodFase" value="<?=$field_actividades['CodFase']?>" />
                        <input type="hidden" name="NomFase" value="<?=($field_actividades['NomFase'])?>" />
                        <input type="hidden" name="CodActividad" value="<?=$field_actividades['CodActividad']?>" />
                        <input type="hidden" name="Descripcion" value="<?=($field_actividades['NomActividad'])?>" />
                        <input type="hidden" name="FlagAutoArchivo" value="<?=$field_actividades['FlagAutoArchivo']?>" />
                        <input type="hidden" name="FlagNoAfectoPlan" value="<?=$field_actividades['FlagNoAfectoPlan']?>" />
                  		<input type="hidden" name="Estado" value="<?=$field_actividades['Estado']?>" />
                        <?=($field_actividades['NomActividad'])?>
                    </td>
                    <td align="center">
                        <?=$field_actividades['Duracion']?>
                        <input type="hidden" name="Duracion" value="<?=($field_actividades['Duracion'])?>" />
                    </td>
                    <td align="center">
                        <?=formatFechaDMA($field_actividades['FechaInicio'])?>
                        <input type="hidden" name="FechaInicio" value="<?=formatFechaDMA($field_actividades['FechaInicio'])?>" />
                    </td>
                    <td align="center">
                        <?=formatFechaDMA($field_actividades['FechaTermino'])?>
                        <input type="hidden" name="FechaTermino" value="<?=formatFechaDMA($field_actividades['FechaTermino'])?>" />
                    </td>
                    <td align="center">
                        <?=intval($field_actividades['Prorroga'])?>
                        <input type="hidden" name="ProrrogaAcu" value="<?=($field_actividades['Prorroga'])?>" />
                    </td>
                    <td align="center">
                        <?php
                        if ($field_actividades['Estado'] == "EJ") {
                            ?><input type="text" name="Prorroga" value="0" class="cell" style="text-align:center;" onchange="setFechaActividadesProrroga(this.value, '<?=$field_actividades['CodActividad']?>');" /><?php
                        } else {
							?><input type="hidden" name="Prorroga" value="0" /><?php
                            echo 0;
                        }
                        ?>
                    </td>
                    <td align="center">
                        <?=formatFechaDMA($field_actividades['FechaInicioReal'])?>
                        <input type="hidden" name="FechaInicioReal" value="<?=formatFechaDMA($field_actividades['FechaInicioReal'])?>" />
                    </td>
                    <td align="center">
                        <?=formatFechaDMA($field_actividades['FechaTerminoReal'])?>
                        <input type="hidden" name="FechaTerminoReal" value="<?=formatFechaDMA($field_actividades['FechaTerminoReal'])?>" />
                    </td>
                    <td align="center">
                        <?=($field_actividades['DiasCierre'])?>
                        <input type="hidden" name="DiasCierre" value="<?=($field_actividades['DiasCierre'])?>" />
                    </td>
                    <td align="center">
                        <?=formatFechaDMA($field_actividades['FechaTerminoCierre'])?>
                        <input type="hidden" name="FechaTerminoCierre" value="<?=formatFechaDMA($field_actividades['FechaTerminoCierre'])?>" />
                    </td>
                    <td align="center">
                        <?=formatFechaDMA($field_actividades['FechaRegistroCierre'])?>
                        <input type="hidden" name="FechaRegistroCierre" value="<?=formatFechaDMA($field_actividades['FechaRegistroCierre'])?>" />
                        <input type="hidden" name="DiasAdelanto" value="<?=($field_actividades['DiasAdelanto'])?>" />
                    </td>
                    <td align="center"><?=printFlag($field_actividades['FlagAutoArchivo'])?></td>
                    <td align="center"><?=printFlag($field_actividades['FlagNoAfectoPlan'])?></td>
                </tr>
                <?php
                if ($field_actividades['FlagNoAfectoPlan'] == "N") {
                    $total_duracion += $field_actividades['Duracion'];
                    $fase_duracion += $field_actividades['Duracion'];
					if ($field_actividades['Estado'] == "EJ") {
						$total_prorroga += $field_actividades['Prorroga'] + $field_actividades['ProrrogaAcu'];
						$fase_prorroga += $field_actividades['Prorroga'] + $field_actividades['ProrrogaAcu'];
					} else {
						$total_prorroga += $field_actividades['Prorroga'];
						$fase_prorroga += $field_actividades['Prorroga'];
					}
					$FechaTerminoReal = $field_actividades['FechaTerminoReal'];
                }
            }
            ?>
            <tr>
                <th colspan="2">&nbsp;</th>
                <th align="center">
                    <span style="font-weight:bold;"><?=$fase_duracion?></span>
                </th>
                <th colspan="2">&nbsp;</th>
                <th align="center">
                    <span style="font-weight:bold;"><?=$fase_prorroga?></span>
                </th>
            </tr>
        <?php
        }
        ?>
        </tbody>
        
        <tfoot>
            <tr>
                <td colspan="13">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
                <td align="center">
                    <span id="total_duracion" style="font-weight:bold;"><?=$total_duracion?></span>
                </td>
                <td colspan="2">&nbsp;</td>
                <td align="center">
                    <span id="total_prorroga" style="font-weight:bold;"><?=$total_prorroga?></span>
                </td>
            </tr>
        </tfoot>
    </table>
    <?php
}

elseif ($accion == "selListadoProrrogasValoracionJuridica") {
	?>
    <table width="1075" class="tblLista">
        <thead>
        <tr>
            <th scope="col" colspan="2" style="background-color:#FFF;">&nbsp;</th>
            <th scope="col" colspan="3">Planificaci&oacute;n Inicial</th>
            <th scope="col" colspan="4">Planificaci&oacute;n Real</th>
            <th scope="col" colspan="3">Ejecuci&oacute;n</th>
            <th scope="col" colspan="2" style="background-color:#FFF;">&nbsp;</th>
        </tr>
        <tr>
            <th scope="col" width="20">Est.</th>
            <th scope="col" align="left">Actividad</th>
            <th scope="col" width="30">Dias</th>
            <th scope="col" width="60">Inicio</th>
            <th scope="col" width="60">Fin</th>
            <th scope="col" width="30">Prorr. Acu.</th>
            <th scope="col" width="30">Prorr.</th>
            <th scope="col" width="60">Inicio Real</th>
            <th scope="col" width="60">Fin Real</th>
            <th scope="col" width="30">Dias Ejec.</th>
            <th scope="col" width="60">Fecha Cierre</th>
            <th scope="col" width="60">Fecha Registro</th>
            <th scope="col" width="25">Au. Ar.</th>
            <th scope="col" width="25">No Afe.</th>
        </tr>
        </thead>
        
        <tbody id="lista_actividades">
        <?php
        $nroactividades = 0;
        if ($opcion != "nuevo") {
            $sql = "SELECT
                        vjd.*,
                        a.CodFase,
                        a.Descripcion AS NomActividad,
                        a.FlagNoAfectoPlan,
                        a.FlagAutoArchivo,
                        f.Descripcion AS NomFase,
						(SELECT SUM(Prorroga)
						 FROM pf_valoracionjuridicaprorroga
						 WHERE
							Estado = 'AP' AND
							CodActividad = vjd.CodActividad AND
							CodValJur = vjd.CodValJur AND
							CodActividad NOT IN (SELECT afd2.CodActividad
											 	 FROM
											 		pf_valoracionjuridicadetalle afd2
													INNER JOIN pf_actividades a ON (afd2.CodActividad = a.CodActividad)
											 	 WHERE
													afd2.Estado = 'EJ' AND
													a.FlagNoAfectoPlan = 'N'
												)
						) AS ProrrogaAcu
                    FROM
                        pf_valoracionjuridicadetalle vjd
                        INNER JOIN pf_actividades a ON (vjd.CodActividad = a.CodActividad)
                        INNER JOIN pf_fases f ON (a.CodFase = f.CodFase)
                    WHERE
						vjd.CodValJur = '".$CodValJur."' AND
						a.Estado = 'A'
                    ORDER BY CodActividad";
            $query_actividades = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
            while ($field_actividades = mysql_fetch_array($query_actividades)) {
                $nroactividades++;
                if ($grupo != $field_actividades['CodFase']) {
                    if ($nroactividades > 1)  {
                        ?>
                        <tr>
                            <th colspan="2">&nbsp;</th>
                            <th align="center">
                                <span style="font-weight:bold;"><?=$fase_duracion?></span>
                            </th>
                            <th colspan="2">&nbsp;</th>
                            <th align="center">
                                <span style="font-weight:bold;"><?=$fase_prorroga?></span>
                            </th>
                        </tr>
                        <?php
                        $fase_duracion = 0;
                        $fase_prorroga = 0;
                    }
                    $grupo = $field_actividades['CodFase'];
                    ?>
                    <tr class="trListaBody2">
                        <td colspan="2"><?=$field_actividades['CodFase']?> <?=$field_actividades['NomFase']?></td>
                    </tr>
                    <?php
                }
                ?>
                <tr class="trListaBody" id="<?=$field_actividades['CodActividad']?>">
                    <td align="center"><?=printEstadoActuacion($field_actividades['Estado'])?></td>
                    <td>
                        <input type="hidden" name="CodFase" value="<?=$field_actividades['CodFase']?>" />
                        <input type="hidden" name="NomFase" value="<?=($field_actividades['NomFase'])?>" />
                        <input type="hidden" name="CodActividad" value="<?=$field_actividades['CodActividad']?>" />
                        <input type="hidden" name="Descripcion" value="<?=($field_actividades['NomActividad'])?>" />
                        <input type="hidden" name="FlagAutoArchivo" value="<?=$field_actividades['FlagAutoArchivo']?>" />
                        <input type="hidden" name="FlagNoAfectoPlan" value="<?=$field_actividades['FlagNoAfectoPlan']?>" />
                  		<input type="hidden" name="Estado" value="<?=$field_actividades['Estado']?>" />
                        <?=($field_actividades['NomActividad'])?>
                    </td>
                    <td align="center">
                        <?=$field_actividades['Duracion']?>
                        <input type="hidden" name="Duracion" value="<?=($field_actividades['Duracion'])?>" />
                    </td>
                    <td align="center">
                        <?=formatFechaDMA($field_actividades['FechaInicio'])?>
                        <input type="hidden" name="FechaInicio" value="<?=formatFechaDMA($field_actividades['FechaInicio'])?>" />
                    </td>
                    <td align="center">
                        <?=formatFechaDMA($field_actividades['FechaTermino'])?>
                        <input type="hidden" name="FechaTermino" value="<?=formatFechaDMA($field_actividades['FechaTermino'])?>" />
                    </td>
                    <td align="center">
                        <?=intval($field_actividades['Prorroga'])?>
                        <input type="hidden" name="ProrrogaAcu" value="<?=($field_actividades['Prorroga'])?>" />
                    </td>
                    <td align="center">
                        <?php
                        if ($field_actividades['Estado'] == "EJ" && $field_actividades['FlagNoAfectoPlan'] == "N") {
                            ?><input type="text" name="Prorroga" value="0" class="cell" style="text-align:center;" onchange="setFechaActividadesProrroga(this.value, '<?=$field_actividades['CodActividad']?>');" /><?php
                        } else {
							?><input type="hidden" name="Prorroga" value="0" /><?php
                            echo 0;
                        }
                        ?>
                    </td>
                    <td align="center">
                        <?=formatFechaDMA($field_actividades['FechaInicioReal'])?>
                        <input type="hidden" name="FechaInicioReal" value="<?=formatFechaDMA($field_actividades['FechaInicioReal'])?>" />
                    </td>
                    <td align="center">
                        <?=formatFechaDMA($field_actividades['FechaTerminoReal'])?>
                        <input type="hidden" name="FechaTerminoReal" value="<?=formatFechaDMA($field_actividades['FechaTerminoReal'])?>" />
                    </td>
                    <td align="center">
                        <?=($field_actividades['DiasCierre'])?>
                        <input type="hidden" name="DiasCierre" value="<?=($field_actividades['DiasCierre'])?>" />
                    </td>
                    <td align="center">
                        <?=formatFechaDMA($field_actividades['FechaTerminoCierre'])?>
                        <input type="hidden" name="FechaTerminoCierre" value="<?=formatFechaDMA($field_actividades['FechaTerminoCierre'])?>" />
                    </td>
                    <td align="center">
                        <?=formatFechaDMA($field_actividades['FechaRegistroCierre'])?>
                        <input type="hidden" name="FechaRegistroCierre" value="<?=formatFechaDMA($field_actividades['FechaRegistroCierre'])?>" />
                        <input type="hidden" name="DiasAdelanto" value="<?=($field_actividades['DiasAdelanto'])?>" />
                    </td>
                    <td align="center"><?=printFlag($field_actividades['FlagAutoArchivo'])?></td>
                    <td align="center"><?=printFlag($field_actividades['FlagNoAfectoPlan'])?></td>
                </tr>
                <?php
                if ($field_actividades['FlagNoAfectoPlan'] == "N") {
                    $total_duracion += $field_actividades['Duracion'];
                    $fase_duracion += $field_actividades['Duracion'];
					if ($field_actividades['Estado'] == "EJ") {
						$total_prorroga += $field_actividades['Prorroga'] + $field_actividades['ProrrogaAcu'];
						$fase_prorroga += $field_actividades['Prorroga'] + $field_actividades['ProrrogaAcu'];
					} else {
						$total_prorroga += $field_actividades['Prorroga'];
						$fase_prorroga += $field_actividades['Prorroga'];
					}
					$FechaTerminoReal = $field_actividades['FechaTerminoReal'];
                }
            }
            ?>
            <tr>
                <th colspan="2">&nbsp;</th>
                <th align="center">
                    <span style="font-weight:bold;"><?=$fase_duracion?></span>
                </th>
                <th colspan="2">&nbsp;</th>
                <th align="center">
                    <span style="font-weight:bold;"><?=$fase_prorroga?></span>
                </th>
            </tr>
        <?php
        }
        ?>
        </tbody>
        
        <tfoot>
            <tr>
                <td colspan="13">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
                <td align="center">
                    <span id="total_duracion" style="font-weight:bold;"><?=$total_duracion?></span>
                </td>
                <td colspan="2">&nbsp;</td>
                <td align="center">
                    <span id="total_prorroga" style="font-weight:bold;"><?=$total_prorroga?></span>
                </td>
            </tr>
        </tfoot>
    </table>
    <?php
}

elseif ($accion == "selListadoProrrogasDeterminacionValoracion") {
	?>
    <table width="1075" class="tblLista">
        <thead>
        <tr>
            <th scope="col" colspan="2" style="background-color:#FFF;">&nbsp;</th>
            <th scope="col" colspan="3">Planificaci&oacute;n Inicial</th>
            <th scope="col" colspan="4">Planificaci&oacute;n Real</th>
            <th scope="col" colspan="3">Ejecuci&oacute;n</th>
            <th scope="col" colspan="2" style="background-color:#FFF;">&nbsp;</th>
        </tr>
        <tr>
            <th scope="col" width="20">Est.</th>
            <th scope="col" align="left">Actividad</th>
            <th scope="col" width="30">Dias</th>
            <th scope="col" width="60">Inicio</th>
            <th scope="col" width="60">Fin</th>
            <th scope="col" width="30">Prorr. Acu.</th>
            <th scope="col" width="30">Prorr.</th>
            <th scope="col" width="60">Inicio Real</th>
            <th scope="col" width="60">Fin Real</th>
            <th scope="col" width="30">Dias Ejec.</th>
            <th scope="col" width="60">Fecha Cierre</th>
            <th scope="col" width="60">Fecha Registro</th>
            <th scope="col" width="25">Au. Ar.</th>
            <th scope="col" width="25">No Afe.</th>
        </tr>
        </thead>
        
        <tbody id="lista_actividades">
        <?php
        $nroactividades = 0;
        if ($opcion != "nuevo") {
            $sql = "SELECT
                        vjd.*,
                        a.CodFase,
                        a.Descripcion AS NomActividad,
                        a.FlagNoAfectoPlan,
                        a.FlagAutoArchivo,
                        f.Descripcion AS NomFase,
						(SELECT SUM(Prorroga)
						 FROM pf_determinacionvaloracionprorroga
						 WHERE
							Estado = 'AP' AND
							CodActividad = vjd.CodActividad AND
							CodValJurDet = vjd.CodValJurDet AND
							CodActividad NOT IN (SELECT afd2.CodActividad
											 	 FROM
											 		pf_determinacionvaloraciondetalle afd2
													INNER JOIN pf_actividades a ON (afd2.CodActividad = a.CodActividad)
											 	 WHERE
													afd2.Estado = 'EJ' AND
													a.FlagNoAfectoPlan = 'N'
												)
						) AS ProrrogaAcu
                    FROM
                        pf_determinacionvaloraciondetalle vjd
                        INNER JOIN pf_actividades a ON (vjd.CodActividad = a.CodActividad)
                        INNER JOIN pf_fases f ON (a.CodFase = f.CodFase)
                    WHERE
						vjd.CodValJurDet = '".$CodValJurDet."' AND
						a.Estado = 'A'
                    ORDER BY CodActividad";
            $query_actividades = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
            while ($field_actividades = mysql_fetch_array($query_actividades)) {
                $nroactividades++;
                if ($grupo != $field_actividades['CodFase']) {
                    if ($nroactividades > 1)  {
                        ?>
                        <tr>
                            <th colspan="2">&nbsp;</th>
                            <th align="center">
                                <span style="font-weight:bold;"><?=$fase_duracion?></span>
                            </th>
                            <th colspan="2">&nbsp;</th>
                            <th align="center">
                                <span style="font-weight:bold;"><?=$fase_prorroga?></span>
                            </th>
                        </tr>
                        <?php
                        $fase_duracion = 0;
                        $fase_prorroga = 0;
                    }
                    $grupo = $field_actividades['CodFase'];
                    ?>
                    <tr class="trListaBody2">
                        <td colspan="2"><?=$field_actividades['CodFase']?> <?=$field_actividades['NomFase']?></td>
                    </tr>
                    <?php
                }
                ?>
                <tr class="trListaBody" id="<?=$field_actividades['CodActividad']?>">
                    <td align="center"><?=printEstadoActuacion($field_actividades['Estado'])?></td>
                    <td>
                        <input type="hidden" name="CodFase" value="<?=$field_actividades['CodFase']?>" />
                        <input type="hidden" name="NomFase" value="<?=($field_actividades['NomFase'])?>" />
                        <input type="hidden" name="CodActividad" value="<?=$field_actividades['CodActividad']?>" />
                        <input type="hidden" name="Descripcion" value="<?=($field_actividades['NomActividad'])?>" />
                        <input type="hidden" name="FlagAutoArchivo" value="<?=$field_actividades['FlagAutoArchivo']?>" />
                        <input type="hidden" name="FlagNoAfectoPlan" value="<?=$field_actividades['FlagNoAfectoPlan']?>" />
                  		<input type="hidden" name="Estado" value="<?=$field_actividades['Estado']?>" />
                        <?=($field_actividades['NomActividad'])?>
                    </td>
                    <td align="center">
                        <?=$field_actividades['Duracion']?>
                        <input type="hidden" name="Duracion" value="<?=($field_actividades['Duracion'])?>" />
                    </td>
                    <td align="center">
                        <?=formatFechaDMA($field_actividades['FechaInicio'])?>
                        <input type="hidden" name="FechaInicio" value="<?=formatFechaDMA($field_actividades['FechaInicio'])?>" />
                    </td>
                    <td align="center">
                        <?=formatFechaDMA($field_actividades['FechaTermino'])?>
                        <input type="hidden" name="FechaTermino" value="<?=formatFechaDMA($field_actividades['FechaTermino'])?>" />
                    </td>
                    <td align="center">
                        <?=intval($field_actividades['Prorroga'])?>
                        <input type="hidden" name="ProrrogaAcu" value="<?=($field_actividades['Prorroga'])?>" />
                    </td>
                    <td align="center">
                        <?php
                        if ($field_actividades['Estado'] == "EJ" && $field_actividades['FlagNoAfectoPlan'] == "N") {
                            ?><input type="text" name="Prorroga" value="0" class="cell" style="text-align:center;" onchange="setFechaActividadesProrroga(this.value, '<?=$field_actividades['CodActividad']?>');" /><?php
                        } else {
							?><input type="hidden" name="Prorroga" value="0" /><?php
                            echo 0;
                        }
                        ?>
                    </td>
                    <td align="center">
                        <?=formatFechaDMA($field_actividades['FechaInicioReal'])?>
                        <input type="hidden" name="FechaInicioReal" value="<?=formatFechaDMA($field_actividades['FechaInicioReal'])?>" />
                    </td>
                    <td align="center">
                        <?=formatFechaDMA($field_actividades['FechaTerminoReal'])?>
                        <input type="hidden" name="FechaTerminoReal" value="<?=formatFechaDMA($field_actividades['FechaTerminoReal'])?>" />
                    </td>
                    <td align="center">
                        <?=($field_actividades['DiasCierre'])?>
                        <input type="hidden" name="DiasCierre" value="<?=($field_actividades['DiasCierre'])?>" />
                    </td>
                    <td align="center">
                        <?=formatFechaDMA($field_actividades['FechaTerminoCierre'])?>
                        <input type="hidden" name="FechaTerminoCierre" value="<?=formatFechaDMA($field_actividades['FechaTerminoCierre'])?>" />
                    </td>
                    <td align="center">
                        <?=formatFechaDMA($field_actividades['FechaRegistroCierre'])?>
                        <input type="hidden" name="FechaRegistroCierre" value="<?=formatFechaDMA($field_actividades['FechaRegistroCierre'])?>" />
                        <input type="hidden" name="DiasAdelanto" value="<?=($field_actividades['DiasAdelanto'])?>" />
                    </td>
                    <td align="center"><?=printFlag($field_actividades['FlagAutoArchivo'])?></td>
                    <td align="center"><?=printFlag($field_actividades['FlagNoAfectoPlan'])?></td>
                </tr>
                <?php
                if ($field_actividades['FlagNoAfectoPlan'] == "N") {
                    $total_duracion += $field_actividades['Duracion'];
                    $fase_duracion += $field_actividades['Duracion'];
					if ($field_actividades['Estado'] == "EJ") {
						$total_prorroga += $field_actividades['Prorroga'] + $field_actividades['ProrrogaAcu'];
						$fase_prorroga += $field_actividades['Prorroga'] + $field_actividades['ProrrogaAcu'];
					} else {
						$total_prorroga += $field_actividades['Prorroga'];
						$fase_prorroga += $field_actividades['Prorroga'];
					}
					$FechaTerminoReal = $field_actividades['FechaTerminoReal'];
                }
            }
            ?>
            <tr>
                <th colspan="2">&nbsp;</th>
                <th align="center">
                    <span style="font-weight:bold;"><?=$fase_duracion?></span>
                </th>
                <th colspan="2">&nbsp;</th>
                <th align="center">
                    <span style="font-weight:bold;"><?=$fase_prorroga?></span>
                </th>
            </tr>
        <?php
        }
        ?>
        </tbody>
        
        <tfoot>
            <tr>
                <td colspan="13">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
                <td align="center">
                    <span id="total_duracion" style="font-weight:bold;"><?=$total_duracion?></span>
                </td>
                <td colspan="2">&nbsp;</td>
                <td align="center">
                    <span id="total_prorroga" style="font-weight:bold;"><?=$total_prorroga?></span>
                </td>
            </tr>
        </tfoot>
    </table>
    <?php
}

elseif ($accion == "selListadoProrrogasPotestadInvestigativa") {
	?>
    <table width="1075" class="tblLista">
        <thead>
        <tr>
            <th scope="col" colspan="2" style="background-color:#FFF;">&nbsp;</th>
            <th scope="col" colspan="3">Planificaci&oacute;n Inicial</th>
            <th scope="col" colspan="4">Planificaci&oacute;n Real</th>
            <th scope="col" colspan="3">Ejecuci&oacute;n</th>
            <th scope="col" colspan="2" style="background-color:#FFF;">&nbsp;</th>
        </tr>
        <tr>
            <th scope="col" width="20">Est.</th>
            <th scope="col" align="left">Actividad</th>
            <th scope="col" width="30">Dias</th>
            <th scope="col" width="60">Inicio</th>
            <th scope="col" width="60">Fin</th>
            <th scope="col" width="30">Prorr. Acu.</th>
            <th scope="col" width="30">Prorr.</th>
            <th scope="col" width="60">Inicio Real</th>
            <th scope="col" width="60">Fin Real</th>
            <th scope="col" width="30">Dias Ejec.</th>
            <th scope="col" width="60">Fecha Cierre</th>
            <th scope="col" width="60">Fecha Registro</th>
            <th scope="col" width="25">Au. Ar.</th>
            <th scope="col" width="25">No Afe.</th>
        </tr>
        </thead>
        
        <tbody id="lista_actividades">
        <?php
        $nroactividades = 0;
        if ($opcion != "nuevo") {
            $sql = "SELECT
                        vjd.*,
                        a.CodFase,
                        a.Descripcion AS NomActividad,
                        a.FlagNoAfectoPlan,
                        a.FlagAutoArchivo,
                        f.Descripcion AS NomFase,
						(SELECT SUM(Prorroga)
						 FROM pf_potestadprorroga
						 WHERE
							Estado = 'AP' AND
							CodActividad = vjd.CodActividad AND
							CodPotestad = vjd.CodPotestad AND
							CodActividad NOT IN (SELECT afd2.CodActividad
											 	 FROM
											 		pf_potestaddetalle afd2
													INNER JOIN pf_actividades a ON (afd2.CodActividad = a.CodActividad)
											 	 WHERE
													afd2.Estado = 'EJ' AND
													a.FlagNoAfectoPlan = 'N'
												)
						) AS ProrrogaAcu
                    FROM
                        pf_potestaddetalle vjd
                        INNER JOIN pf_actividades a ON (vjd.CodActividad = a.CodActividad)
                        INNER JOIN pf_fases f ON (a.CodFase = f.CodFase)
                    WHERE
						vjd.CodPotestad = '".$CodPotestad."' AND
						a.Estado = 'A'
                    ORDER BY CodActividad";
            $query_actividades = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
            while ($field_actividades = mysql_fetch_array($query_actividades)) {
                $nroactividades++;
                if ($grupo != $field_actividades['CodFase']) {
                    if ($nroactividades > 1)  {
                        ?>
                        <tr>
                            <th colspan="2">&nbsp;</th>
                            <th align="center">
                                <span style="font-weight:bold;"><?=$fase_duracion?></span>
                            </th>
                            <th colspan="2">&nbsp;</th>
                            <th align="center">
                                <span style="font-weight:bold;"><?=$fase_prorroga?></span>
                            </th>
                        </tr>
                        <?php
                        $fase_duracion = 0;
                        $fase_prorroga = 0;
                    }
                    $grupo = $field_actividades['CodFase'];
                    ?>
                    <tr class="trListaBody2">
                        <td colspan="2"><?=$field_actividades['CodFase']?> <?=$field_actividades['NomFase']?></td>
                    </tr>
                    <?php
                }
                ?>
                <tr class="trListaBody" id="<?=$field_actividades['CodActividad']?>">
                    <td align="center"><?=printEstadoActuacion($field_actividades['Estado'])?></td>
                    <td>
                        <input type="hidden" name="CodFase" value="<?=$field_actividades['CodFase']?>" />
                        <input type="hidden" name="NomFase" value="<?=($field_actividades['NomFase'])?>" />
                        <input type="hidden" name="CodActividad" value="<?=$field_actividades['CodActividad']?>" />
                        <input type="hidden" name="Descripcion" value="<?=($field_actividades['NomActividad'])?>" />
                        <input type="hidden" name="FlagAutoArchivo" value="<?=$field_actividades['FlagAutoArchivo']?>" />
                        <input type="hidden" name="FlagNoAfectoPlan" value="<?=$field_actividades['FlagNoAfectoPlan']?>" />
                  		<input type="hidden" name="Estado" value="<?=$field_actividades['Estado']?>" />
                        <?=($field_actividades['NomActividad'])?>
                    </td>
                    <td align="center">
                        <?=$field_actividades['Duracion']?>
                        <input type="hidden" name="Duracion" value="<?=($field_actividades['Duracion'])?>" />
                    </td>
                    <td align="center">
                        <?=formatFechaDMA($field_actividades['FechaInicio'])?>
                        <input type="hidden" name="FechaInicio" value="<?=formatFechaDMA($field_actividades['FechaInicio'])?>" />
                    </td>
                    <td align="center">
                        <?=formatFechaDMA($field_actividades['FechaTermino'])?>
                        <input type="hidden" name="FechaTermino" value="<?=formatFechaDMA($field_actividades['FechaTermino'])?>" />
                    </td>
                    <td align="center">
                        <?=intval($field_actividades['Prorroga'])?>
                        <input type="hidden" name="ProrrogaAcu" value="<?=($field_actividades['Prorroga'])?>" />
                    </td>
                    <td align="center">
                        <?php
                        if ($field_actividades['Estado'] == "EJ" && $field_actividades['FlagNoAfectoPlan'] == "N") {
                            ?><input type="text" name="Prorroga" value="0" class="cell" style="text-align:center;" onchange="setFechaActividadesProrroga(this.value, '<?=$field_actividades['CodActividad']?>');" /><?php
                        } else {
							?><input type="hidden" name="Prorroga" value="0" /><?php
                            echo 0;
                        }
                        ?>
                    </td>
                    <td align="center">
                        <?=formatFechaDMA($field_actividades['FechaInicioReal'])?>
                        <input type="hidden" name="FechaInicioReal" value="<?=formatFechaDMA($field_actividades['FechaInicioReal'])?>" />
                    </td>
                    <td align="center">
                        <?=formatFechaDMA($field_actividades['FechaTerminoReal'])?>
                        <input type="hidden" name="FechaTerminoReal" value="<?=formatFechaDMA($field_actividades['FechaTerminoReal'])?>" />
                    </td>
                    <td align="center">
                        <?=($field_actividades['DiasCierre'])?>
                        <input type="hidden" name="DiasCierre" value="<?=($field_actividades['DiasCierre'])?>" />
                    </td>
                    <td align="center">
                        <?=formatFechaDMA($field_actividades['FechaTerminoCierre'])?>
                        <input type="hidden" name="FechaTerminoCierre" value="<?=formatFechaDMA($field_actividades['FechaTerminoCierre'])?>" />
                    </td>
                    <td align="center">
                        <?=formatFechaDMA($field_actividades['FechaRegistroCierre'])?>
                        <input type="hidden" name="FechaRegistroCierre" value="<?=formatFechaDMA($field_actividades['FechaRegistroCierre'])?>" />
                        <input type="hidden" name="DiasAdelanto" value="<?=($field_actividades['DiasAdelanto'])?>" />
                    </td>
                    <td align="center"><?=printFlag($field_actividades['FlagAutoArchivo'])?></td>
                    <td align="center"><?=printFlag($field_actividades['FlagNoAfectoPlan'])?></td>
                </tr>
                <?php
                if ($field_actividades['FlagNoAfectoPlan'] == "N") {
                    $total_duracion += $field_actividades['Duracion'];
                    $fase_duracion += $field_actividades['Duracion'];
					if ($field_actividades['Estado'] == "EJ") {
						$total_prorroga += $field_actividades['Prorroga'] + $field_actividades['ProrrogaAcu'];
						$fase_prorroga += $field_actividades['Prorroga'] + $field_actividades['ProrrogaAcu'];
					} else {
						$total_prorroga += $field_actividades['Prorroga'];
						$fase_prorroga += $field_actividades['Prorroga'];
					}
					$FechaTerminoReal = $field_actividades['FechaTerminoReal'];
                }
            }
            ?>
            <tr>
                <th colspan="2">&nbsp;</th>
                <th align="center">
                    <span style="font-weight:bold;"><?=$fase_duracion?></span>
                </th>
                <th colspan="2">&nbsp;</th>
                <th align="center">
                    <span style="font-weight:bold;"><?=$fase_prorroga?></span>
                </th>
            </tr>
        <?php
        }
        ?>
        </tbody>
        
        <tfoot>
            <tr>
                <td colspan="13">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
                <td align="center">
                    <span id="total_duracion" style="font-weight:bold;"><?=$total_duracion?></span>
                </td>
                <td colspan="2">&nbsp;</td>
                <td align="center">
                    <span id="total_prorroga" style="font-weight:bold;"><?=$total_prorroga?></span>
                </td>
            </tr>
        </tfoot>
    </table>
    <?php
}

elseif ($accion == "selListadoProrrogasDeterminacionResponsabilidad") {
	?>
    <table width="1075" class="tblLista">
        <thead>
        <tr>
            <th scope="col" colspan="2" style="background-color:#FFF;">&nbsp;</th>
            <th scope="col" colspan="3">Planificaci&oacute;n Inicial</th>
            <th scope="col" colspan="4">Planificaci&oacute;n Real</th>
            <th scope="col" colspan="3">Ejecuci&oacute;n</th>
            <th scope="col" colspan="2" style="background-color:#FFF;">&nbsp;</th>
        </tr>
        <tr>
            <th scope="col" width="20">Est.</th>
            <th scope="col" align="left">Actividad</th>
            <th scope="col" width="30">Dias</th>
            <th scope="col" width="60">Inicio</th>
            <th scope="col" width="60">Fin</th>
            <th scope="col" width="30">Prorr. Acu.</th>
            <th scope="col" width="30">Prorr.</th>
            <th scope="col" width="60">Inicio Real</th>
            <th scope="col" width="60">Fin Real</th>
            <th scope="col" width="30">Dias Ejec.</th>
            <th scope="col" width="60">Fecha Cierre</th>
            <th scope="col" width="60">Fecha Registro</th>
            <th scope="col" width="25">Au. Ar.</th>
            <th scope="col" width="25">No Afe.</th>
        </tr>
        </thead>
        
        <tbody id="lista_actividades">
        <?php
        $nroactividades = 0;
        if ($opcion != "nuevo") {
            $sql = "SELECT
                        vjd.*,
                        a.CodFase,
                        a.Descripcion AS NomActividad,
                        a.FlagNoAfectoPlan,
                        a.FlagAutoArchivo,
                        f.Descripcion AS NomFase,
						(SELECT SUM(Prorroga)
						 FROM pf_prorroga
						 WHERE
							Estado = 'AP' AND
							CodActividad = vjd.CodActividad AND
							CodDeterminacion = vjd.CodDeterminacion AND
							CodActividad NOT IN (SELECT afd2.CodActividad
											 	 FROM
											 		pf_determinaciondetalle afd2
													INNER JOIN pf_actividades a ON (afd2.CodActividad = a.CodActividad)
											 	 WHERE
													afd2.Estado = 'EJ' AND
													a.FlagNoAfectoPlan = 'N'
												)
						) AS ProrrogaAcu
                    FROM
                        pf_determinaciondetalle vjd
                        INNER JOIN pf_actividades a ON (vjd.CodActividad = a.CodActividad)
                        INNER JOIN pf_fases f ON (a.CodFase = f.CodFase)
                    WHERE
						vjd.CodDeterminacion = '".$CodDeterminacion."' AND
						a.Estado = 'A'
                    ORDER BY CodActividad";
            $query_actividades = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
            while ($field_actividades = mysql_fetch_array($query_actividades)) {
                $nroactividades++;
                if ($grupo != $field_actividades['CodFase']) {
                    if ($nroactividades > 1)  {
                        ?>
                        <tr>
                            <th colspan="2">&nbsp;</th>
                            <th align="center">
                                <span style="font-weight:bold;"><?=$fase_duracion?></span>
                            </th>
                            <th colspan="2">&nbsp;</th>
                            <th align="center">
                                <span style="font-weight:bold;"><?=$fase_prorroga?></span>
                            </th>
                        </tr>
                        <?php
                        $fase_duracion = 0;
                        $fase_prorroga = 0;
                    }
                    $grupo = $field_actividades['CodFase'];
                    ?>
                    <tr class="trListaBody2">
                        <td colspan="2"><?=$field_actividades['CodFase']?> <?=$field_actividades['NomFase']?></td>
                    </tr>
                    <?php
                }
                ?>
                <tr class="trListaBody" id="<?=$field_actividades['CodActividad']?>">
                    <td align="center"><?=printEstadoActuacion($field_actividades['Estado'])?></td>
                    <td>
                        <input type="hidden" name="CodFase" value="<?=$field_actividades['CodFase']?>" />
                        <input type="hidden" name="NomFase" value="<?=($field_actividades['NomFase'])?>" />
                        <input type="hidden" name="CodActividad" value="<?=$field_actividades['CodActividad']?>" />
                        <input type="hidden" name="Descripcion" value="<?=($field_actividades['NomActividad'])?>" />
                        <input type="hidden" name="FlagAutoArchivo" value="<?=$field_actividades['FlagAutoArchivo']?>" />
                        <input type="hidden" name="FlagNoAfectoPlan" value="<?=$field_actividades['FlagNoAfectoPlan']?>" />
                  		<input type="hidden" name="Estado" value="<?=$field_actividades['Estado']?>" />
                        <?=($field_actividades['NomActividad'])?>
                    </td>
                    <td align="center">
                        <?=$field_actividades['Duracion']?>
                        <input type="hidden" name="Duracion" value="<?=($field_actividades['Duracion'])?>" />
                    </td>
                    <td align="center">
                        <?=formatFechaDMA($field_actividades['FechaInicio'])?>
                        <input type="hidden" name="FechaInicio" value="<?=formatFechaDMA($field_actividades['FechaInicio'])?>" />
                    </td>
                    <td align="center">
                        <?=formatFechaDMA($field_actividades['FechaTermino'])?>
                        <input type="hidden" name="FechaTermino" value="<?=formatFechaDMA($field_actividades['FechaTermino'])?>" />
                    </td>
                    <td align="center">
                        <?=intval($field_actividades['Prorroga'])?>
                        <input type="hidden" name="ProrrogaAcu" value="<?=($field_actividades['Prorroga'])?>" />
                    </td>
                    <td align="center">
                        <?php
                        if ($field_actividades['Estado'] == "EJ" && $field_actividades['FlagNoAfectoPlan'] == "N") {
                            ?><input type="text" name="Prorroga" value="0" class="cell" style="text-align:center;" onchange="setFechaActividadesProrroga(this.value, '<?=$field_actividades['CodActividad']?>');" /><?php
                        } else {
							?><input type="hidden" name="Prorroga" value="0" /><?php
                            echo 0;
                        }
                        ?>
                    </td>
                    <td align="center">
                        <?=formatFechaDMA($field_actividades['FechaInicioReal'])?>
                        <input type="hidden" name="FechaInicioReal" value="<?=formatFechaDMA($field_actividades['FechaInicioReal'])?>" />
                    </td>
                    <td align="center">
                        <?=formatFechaDMA($field_actividades['FechaTerminoReal'])?>
                        <input type="hidden" name="FechaTerminoReal" value="<?=formatFechaDMA($field_actividades['FechaTerminoReal'])?>" />
                    </td>
                    <td align="center">
                        <?=($field_actividades['DiasCierre'])?>
                        <input type="hidden" name="DiasCierre" value="<?=($field_actividades['DiasCierre'])?>" />
                    </td>
                    <td align="center">
                        <?=formatFechaDMA($field_actividades['FechaTerminoCierre'])?>
                        <input type="hidden" name="FechaTerminoCierre" value="<?=formatFechaDMA($field_actividades['FechaTerminoCierre'])?>" />
                    </td>
                    <td align="center">
                        <?=formatFechaDMA($field_actividades['FechaRegistroCierre'])?>
                        <input type="hidden" name="FechaRegistroCierre" value="<?=formatFechaDMA($field_actividades['FechaRegistroCierre'])?>" />
                        <input type="hidden" name="DiasAdelanto" value="<?=($field_actividades['DiasAdelanto'])?>" />
                    </td>
                    <td align="center"><?=printFlag($field_actividades['FlagAutoArchivo'])?></td>
                    <td align="center"><?=printFlag($field_actividades['FlagNoAfectoPlan'])?></td>
                </tr>
                <?php
                if ($field_actividades['FlagNoAfectoPlan'] == "N") {
                    $total_duracion += $field_actividades['Duracion'];
                    $fase_duracion += $field_actividades['Duracion'];
					if ($field_actividades['Estado'] == "EJ") {
						$total_prorroga += $field_actividades['Prorroga'] + $field_actividades['ProrrogaAcu'];
						$fase_prorroga += $field_actividades['Prorroga'] + $field_actividades['ProrrogaAcu'];
					} else {
						$total_prorroga += $field_actividades['Prorroga'];
						$fase_prorroga += $field_actividades['Prorroga'];
					}
					$FechaTerminoReal = $field_actividades['FechaTerminoReal'];
                }
            }
            ?>
            <tr>
                <th colspan="2">&nbsp;</th>
                <th align="center">
                    <span style="font-weight:bold;"><?=$fase_duracion?></span>
                </th>
                <th colspan="2">&nbsp;</th>
                <th align="center">
                    <span style="font-weight:bold;"><?=$fase_prorroga?></span>
                </th>
            </tr>
        <?php
        }
        ?>
        </tbody>
        
        <tfoot>
            <tr>
                <td colspan="13">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
                <td align="center">
                    <span id="total_duracion" style="font-weight:bold;"><?=$total_duracion?></span>
                </td>
                <td colspan="2">&nbsp;</td>
                <td align="center">
                    <span id="total_prorroga" style="font-weight:bold;"><?=$total_prorroga?></span>
                </td>
            </tr>
        </tfoot>
    </table>
    <?php
}

elseif ($accion == "setListaActividades") {
	$filtro_sql = "";
	if ($detalles != "") {
		$lineas = split(";", $detalles);	$i=0;
		foreach ($lineas as $linea) {
			if ($i == 0) $filtro_sql .= "AND (a.CodActividad = '".$linea."'";
			else $filtro_sql .= " OR a.CodActividad = '".$linea."'";
			$i++;
		}
		$filtro_sql .= " OR a.CodActividad = '".$CodActividad."')";
	} else {
		if ($CodActividad != "") $filtro_sql .= "AND (a.CodActividad = '".$CodActividad."')";
	}
	
	if (getDiaSemana($FechaInicio) == 0 || getDiaSemana($FechaInicio) == 6) {
		$FechaInicio = getFechaFinHabiles($FechaInicio, 2);
		echo $FechaInicio;
	}
	
	echo "||";
	
	$total_duracion = 0;
	$total_prorroga = 0;
	$fase_duracion = 0;
	$fase_prorroga = 0;
	$sql = "SELECT
				a.*,
				f.Descripcion AS NomFase
			FROM
				pf_actividades a
				INNER JOIN pf_fases f On (f.CodFase = a.CodFase)
				INNER JOIN pf_procesos p ON (f.CodProceso = p.CodProceso)
			WHERE 
				p.CodProceso = '".$CodProceso."' AND
				a.Estado = 'A'
				$filtro_sql
			ORDER BY CodFase, CodActividad";	echo $sql;
    $query = mysql_query($sql) or die ($sql.mysql_error());	$i=0;
    while ($field = mysql_fetch_array($query)) {	$i++;
		$FechaTermino = getFechaFinHabiles($FechaInicio, $field['Duracion']);
		$FlagAutoArchivo = printFlag2($field['FlagAutoArchivo']);
		$FlagNoAfectoPlan = printFlag2($field['FlagNoAfectoPlan']);
		
		if ($grupo != $field['CodFase']) {
			if ($i>1)  {
				?>
                <tr>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th><strong><span><?=$fase_duracion?></span></strong></th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th><strong><span><?=$fase_prorroga?></span></strong></th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                </tr>
                <?php
				$fase_duracion = 0;
				$fase_prorroga = 0;
			}
			$grupo = $field['CodFase'];
			?>
            <tr class="trListaBody2">
                <td colspan="13"><?=$field['CodFase']?> <?=($field['NomFase'])?></td>
            </tr>
            <?php
		}
		
		?>
        <tr class="trListaBody" onclick="mClk(this, 'sel_actividades');" id="<?=$field['CodActividad']?>">
        	<td align="center"><?=printEstadoActuacion("PR")?></td>
            <td>
	            <input type="hidden" name="CodFase" value="<?=$field['CodFase']?>" />
	            <input type="hidden" name="NomFase" value="<?=($field['NomFase'])?>" />
	            <input type="hidden" name="CodActividad" value="<?=$field['CodActividad']?>" />
	            <input type="hidden" name="Descripcion" value="<?=($field['Descripcion'])?>" />
                <input type="hidden" name="FlagAutoArchivo" value="<?=$field['FlagAutoArchivo']?>" />
                <input type="hidden" name="FlagNoAfectoPlan" value="<?=$field['FlagNoAfectoPlan']?>" />
				<?=($field['Descripcion'])?>
			</td>
            <td align="center"><input type="text" name="Duracion" style="text-align:center;" value="<?=$field['Duracion']?>" class="cell" onBlur="this.className='cell';" onFocus="this.className='cellFocus';" onchange="setFechaActividades();" /></td>
            <td align="center">
                <?=$FechaInicio?>
                <input type="hidden" name="FechaInicio" value="<?=$FechaInicio?>" />
            </td>
            <td align="center">
                <?=$FechaTermino?>
                <input type="hidden" name="FechaTermino" value="<?=$FechaTermino?>" />
            </td>
            <td align="center">
                0
                <input type="hidden" name="Prorroga" value="0" />
            </td>
            <td align="center">
                <?=$FechaInicio?>
                <input type="hidden" name="FechaInicioReal" value="<?=$FechaInicio?>" />
            </td>
            <td align="center">
                <?=$FechaTermino?>
                <input type="hidden" name="FechaTerminoReal" value="<?=$FechaTermino?>" />
            </td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td align="center"><?=$FlagAutoArchivo?></td>
            <td align="center"><?=$FlagNoAfectoPlan?></td>
        </tr>
        <?php
		$FechaInicio = getFechaFinHabiles($FechaTermino, 2);
		if ($field['FlagNoAfectoPlan'] == "N") {
			$total_duracion += $field['Duracion'];
			$fase_duracion += $field['Duracion'];
			$total_prorroga += $field['Prorroga'];
			$fase_prorroga += $field['Prorroga'];
			$fecha_termino_afecto = $FechaTermino;
		} else $noafecto += $field['Duracion'];
	}
	?>
    <tr>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th><strong><span><?=$fase_duracion?></span></strong></th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th><strong><span><?=$fase_prorroga?></span></strong></th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
    </tr>
    <?php
	echo "||$fecha_termino_afecto||".intval($total_duracion)."||".intval($total_prorroga)."||".intval($noafecto);
}

elseif ($accion == "selListadoOrdenCompraPersona") {
	//	consulto los datos del proveedor
	$sql = "SELECT
				p.NomCompleto,
				pv.CodTipoServicio,
				pv.CodFormaPago,
				pv.CodTipoDocumento
			FROM
				mastpersonas p
				LEFT JOIN mastproveedores pv ON (p.CodPersona = pv.CodProveedor)
			WHERE p.CodPersona = '".$CodPersona."'";
	$query_proveedor = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query_proveedor) != 0) $field_proveedor = mysql_fetch_array($query_proveedor);
	
	//	porcentaje IVA
	$FactorImpuesto = getPorcentajeIVA($field_proveedor['CodTipoServicio']);
	
	//	valores
	echo "$field_proveedor[NomCompleto]|$field_proveedor[CodTipoServicio]|$field_proveedor[CodFormaPago]|".number_format($FactorImpuesto,2)."|";
	loadSelect("masttiposervicio", "CodTipoServicio", "Descripcion", $field_proveedor['CodTipoServicio'], 1);
}

elseif ($accion == "selListadoOrdenServicioPersona") {
	//	consulto los datos del proveedor
	$sql = "SELECT
				p.NomCompleto,
				pv.CodTipoServicio,
				pv.CodFormaPago,
				pv.CodTipoPago,
				pv.CodTipoDocumento
			FROM
				mastpersonas p
				LEFT JOIN mastproveedores pv ON (p.CodPersona = pv.CodProveedor)
			WHERE p.CodPersona = '".$CodPersona."'";
	$query_proveedor = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query_proveedor) != 0) $field_proveedor = mysql_fetch_array($query_proveedor);
	
	//	porcentaje IVA
	$FactorImpuesto = getPorcentajeIVA($field_proveedor['CodTipoServicio']);
	
	//	valores
	echo "$field_proveedor[NomCompleto]|$field_proveedor[CodTipoServicio]|$field_proveedor[CodFormaPago]|$field_proveedor[CodTipoPago]|".number_format($FactorImpuesto,2)."|";
	loadSelect("masttiposervicio", "CodTipoServicio", "Descripcion", $field_proveedor['CodTipoServicio'], 1);
}

elseif ($accion == "getPorcentajeIVA") {
	$FactorPorcentaje = getPorcentajeIVA($CodTipoServicio);

	die(json_encode([
		'status' => 'success',
		'FactorPorcentaje' => number_format($FactorPorcentaje,2),
	]));
}

elseif ($accion == "afectaTipoServicio") {
	if (afectaTipoServicio($CodTipoServicio)) echo "S"; else echo "N";
}

elseif ($accion == "orden_compra_detalles_insertar") {
	$CodCentroCosto = getVar3("SELECT CodCentrocosto FROM ac_mastcentrocosto WHERE Codigo = '$_PARAMETRO[CCOSTOCOMPRA]'");
	if (!afectaTipoServicio($CodTipoServicio)) { $dFlagExonerado = "disabled"; $cFlagExonerado = "checked"; }
	$FechaPrometida = formatFechaAMD(getFechaFin(formatFechaDMA(substr($Ahora, 0, 10)), $_PARAMETRO['DIAENTOC']));
	if ($Tipo == "item") {
		$readonly = "readonly";
		$sql = "SELECT
					*,
					CtaGasto AS CodCuenta,
					CtaGastoPub20 AS CodCuentaPub20,
					PartidaPresupuestal AS cod_partida,
					CodUnidadComp AS CodUnidadCompra
				FROM lg_itemmast
				WHERE CodItem = '".$Codigo."'";
		$disabled_descripcion = "disabled";
		$CodItem = $Codigo;
	} else {
		$sql = "SELECT
					cs.*,
					cm.Clasificacion,
					cm.Descripcion AS NomCommodity,
					CodUnidad AS CodUnidadCompra
				FROM
					lg_commoditysub cs
					INNER JOIN lg_commoditymast cm ON (cs.CommodityMast = cm.CommodityMast)
				WHERE cs.Codigo = '".$Codigo."'";
		$CommoditySub = $Codigo;
	}
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) {
		$field_detalles = mysql_fetch_array($query);
		if ($Tipo == "item" ) $Descripcion = $field_detalles['Descripcion'];
		else $Descripcion = strtoupper($field_detalles['NomCommodity']."-".$field_detalles['Descripcion']);
		echo "$field_detalles[Clasificacion]|";
		?>
        <tr class="trListaBody" onclick="mClk(this, 'sel_detalles');" id="detalles_<?=$nrodetalles?>">
			<th align="center">
				<?=$nrodetalles?>
            </th>
			<td align="center">
            	<?=$Codigo?>
                <input type="hidden" name="CodItem" class="cell2" style="text-align:center;" value="<?=$field_detalles['CodItem']?>" readonly />
                <input type="hidden" name="CommoditySub" class="cell2" style="text-align:center;" value="<?=$field_detalles['Codigo']?>" readonly />
            </td>
			<td align="center">
				<textarea name="Descripcion" style="height:30px;" class="cell"><?=htmlentities($Descripcion)?></textarea>
			</td>
			<td align="center">
				<select name="CodUnidad" class="cell">
					<?=loadSelect2('mastunidades','CodUnidad','CodUnidad',$field_detalles['CodUnidadCompra'])?>
				</select>
            </td>
			<td align="center">
            	<input type="text" name="CantidadPedida" class="cell" style="text-align:right;" value="0,00" onBlur="numeroBlur(this);" onFocus="numeroFocus(this);" onchange="setMontosOrdenCompra(this.form);" />
            </td>
			<td align="center">
            	<input type="text" name="PrecioUnit" class="cell" style="text-align:right;" value="0,00" onBlur="numeroBlur(this);" onFocus="numeroFocus(this);" onchange="setMontosOrdenCompra(this.form);" />
            </td>
			<td align="center">
            	<input type="text" name="DescuentoPorcentaje" class="cell" style="text-align:right;" value="0,00" onBlur="numeroBlur(this);" onFocus="numeroFocus(this);" onchange="setMontosOrdenCompra(this.form);" />
            </td>
			<td align="center">
            	<input type="text" name="DescuentoFijo" class="cell" style="text-align:right;" value="0,00" onBlur="numeroBlur(this);" onFocus="numeroFocus(this);" onchange="setMontosOrdenCompra(this.form);" />
            </td>
			<td align="center">
            	<input type="checkbox" name="FlagExonerado" class="FlagExonerado" onchange="setMontosOrdenCompra(this.form);" <?=$dFlagExonerado?> <?=$cFlagExonerado?> />
            </td>
			<td align="center">
            	<input type="text" name="PrecioUnitTotal" class="cell2" style="text-align:right;" value="0,00" readonly="readonly" />
            </td>
			<td align="center">
            	<input type="text" name="Total" class="cell2" style="text-align:right;" value="0,00" readonly="readonly" />
            </td>
            <td align="center">
                <input type="text" name="detallesCategoriaProg" id="detallesCategoriaProg_<?=$nrodetalles?>" class="cell2 CategoriaProg" style="text-align:center;" value="<?=$CategoriaProg?>" readonly />
                <input type="hidden" name="detallesEjercicio" id="detallesEjercicio_<?=$nrodetalles?>" class="cell2 Ejercicio" style="text-align:center;" value="<?=$Ejercicio?>" readonly />
                <input type="hidden" name="detallesCodPresupuesto" id="detallesCodPresupuesto_<?=$nrodetalles?>" class="cell2 CodPresupuesto" style="text-align:center;" value="<?=$CodPresupuesto?>" readonly />
            </td>
            <td>
                <select name="detallesCodFuente" id="detallesCodFuente_<?=$nrodetalles?>" class="cell2 CodFuente" <?=$disabled_ver?>>
                    <?=loadSelect2("pv_fuentefinanciamiento","CodFuente","Denominacion",$CodFuente,10)?>
                </select>
            </td>
			<td align="center">
            	<select name="CodUnidadRec" class="cell">
                	<?=loadSelect2("mastunidades", "CodUnidad", "CodUnidad", $field_detalles['CodUnidad'], 0)?>
                </select>
            </td>
			<td align="center">
            	<input type="text" name="CantidadRec" class="cell" style="text-align:right;" value="0,00" onBlur="numeroBlur(this);" onFocus="numeroFocus(this);" />
            </td>
			<td align="center">
            	<input type="text" name="FechaPrometida" value="<?=formatFechaDMA($FechaPrometida)?>" maxlength="10" style="text-align:center;" class="datepicker cell" onkeyup="setFechaDMA(this);" />
            </td>
			<td align="right">
				0,00
			</td>
			<td align="center">
                <input type="text" name="NomCentroCosto" id="NomCentroCosto_<?=$nrodetalles?>" class="cell2" style="text-align:center;" maxlength="4" value="<?=$_PARAMETRO['CCOSTOCOMPRA']?>" readonly />
				<input type="hidden" name="CodCentroCosto" id="CodCentroCosto_<?=$nrodetalles?>" value="<?=$CodCentroCosto?>" />
			</td>
			<td align="center">
				<?=printValoresGeneral("ESTADO-COMPRA-DETALLE", "PR")?>
            </td>
			<td align="center">
				<?=$field_detalles['cod_partida']?>
				<input type="hidden" name="cod_partida" value="<?=$field_detalles['cod_partida']?>" />
			</td>
			<td align="center">
				<?=$field_detalles['CodCuenta']?>
				<input type="hidden" name="CodCuenta" value="<?=$field_detalles['CodCuenta']?>" />
			</td>
			<td align="center">
				<?=$field_detalles['CodCuentaPub20']?>
				<input type="hidden" name="CodCuentaPub20" value="<?=$field_detalles['CodCuentaPub20']?>" />
			</td>
			<td align="center">
				<textarea name="Comentarios" style="height:30px;" class="cell"></textarea>
				<input type="hidden" name="CodRequerimiento" />
				<input type="hidden" name="Secuencia" />
				<input type="hidden" name="CotizacionSecuencia" />
				<input type="hidden" name="CantidadRequerimiento" />
			</td>
		</tr>
       <?php
	}
}

elseif ($accion == "almacen_detalles_insertar") {
	if ($FlagManual != "S") $dPrecioUnit = "disabled";
	##	consulto
	$sql = "SELECT
				i.CodItem,
				i.Descripcion,
				i.CodUnidad,
				iai.StockActual
			FROM
				lg_itemmast i
				LEFT JOIN lg_itemalmaceninv iai ON (iai.CodItem = i.CodItem AND
													iai.CodAlmacen = '".$CodAlmacen."')
			WHERE i.CodItem = '".$CodItem."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) {
		$field_detalle = mysql_fetch_array($query);
		?>
        <tr class="trListaBody" onclick="mClk(this, 'sel_detalles');" id="detalles_<?=$nrodetalles?>">
			<th align="center">
				<?=$nrodetalles?>
            </th>
			<td align="center">
                <input type="text" name="CodItem" class="cell2" style="text-align:center;" value="<?=$field_detalle['CodItem']?>" readonly />
            </td>
			<td align="center">
				<textarea name="Descripcion" style="height:30px;" class="cell" readonly="readonly"><?=($field_detalle['Descripcion'])?></textarea>
			</td>
			<td align="center">
            	<input type="text" name="CodUnidad" value="<?=$field_detalle['CodUnidad']?>" class="cell2" style="text-align:center;" readonly />		
            </td>
			<td align="center">
            	<input type="text" name="StockActual" class="cell2" style="text-align:right;" value="<?=number_format($field_detalle['StockActual'], 2, ',', '.')?>" readonly="readonly" />
            </td>
			<td align="center">
            	<input type="text" name="CantidadRecibida" class="cell" style="text-align:right;" value="0,00" onBlur="numeroBlur(this);" onFocus="numeroFocus(this);" onchange="setMontosAlmacen(this.form);" />
            </td>
			<td align="center">
            	<input type="text" name="PrecioUnit" class="cell PrecioUnit" style="text-align:right;" value="0,00" onBlur="numeroBlur(this);" onFocus="numeroFocus(this);" onchange="setMontosAlmacen(this.form);" <?=$dPrecioUnit?> />
            </td>
			<td align="center">
            	<input type="text" name="Total" id="Total_<?=$nrodetalles?>" class="cell2" style="text-align:right;" value="0,00" readonly="readonly" />
            </td>
			<td align="center">
            	<input type="hidden" name="CodCentroCosto" value="<?=$CodCentroCosto?>" />
            	<input type="hidden" name="ReferenciaCodDocumento" value="<?=$CodDocumentoReferencia?>" />
            	<input type="hidden" name="ReferenciaNroDocumento" value="<?=$NroDocumentoReferencia?>" />
            	<input type="hidden" name="ReferenciaSecuencia" value="<?=$nrodetalles?>" />
                <?=$CodDocumentoReferencia?>-<?=$NroDocumentoReferencia?>-<?=$nrodetalles?>
			</td>
		</tr>
       <?php
	}
}

elseif ($accion == "commodity_detalles_insertar") {
	if ($FlagManual != "S") $dPrecioUnit = "disabled";
	##	consulto
	$sql = "SELECT
				i.Codigo AS CommoditySub,
				i.Descripcion,
				i.CodUnidad,
				iai.Cantidad AS StockActual
			FROM
				lg_commoditysub i
				LEFT JOIN lg_commoditystock iai ON (iai.CommoditySub = i.CommoditySub AND
													iai.CodAlmacen = '".$CodAlmacen."')
			WHERE i.Codigo = '".$CommoditySub."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) {
		$field_detalle = mysql_fetch_array($query);
		?>
        <tr class="trListaBody" onclick="mClk(this, 'sel_detalles');" id="detalles_<?=$nrodetalles?>">
			<th align="center">
				<?=$nrodetalles?>
            </th>
			<td align="center">
                <input type="text" name="CommoditySub" class="cell2" style="text-align:center;" value="<?=$field_detalle['CommoditySub']?>" readonly />
            </td>
			<td align="center">
				<textarea name="Descripcion" style="height:30px;" class="cell" readonly="readonly"><?=($field_detalle['Descripcion'])?></textarea>
			</td>
			<td align="center">
            	<input type="text" name="CodUnidad" value="<?=$field_detalle['CodUnidad']?>" class="cell2" style="text-align:center;" readonly />		
            </td>
			<td align="center">
            	<input type="text" name="StockActual" class="cell2" style="text-align:right;" value="<?=number_format($field_detalle['StockActual'], 2, ',', '.')?>" readonly="readonly" />
            </td>
			<td align="center">
            	<input type="text" name="CantidadRecibida" class="cell" style="text-align:right;" value="0,00" onBlur="numeroBlur(this);" onFocus="numeroFocus(this);" onchange="setMontosAlmacen(this.form);" />
            </td>
			<td align="center">
            	<input type="text" name="PrecioUnit" class="cell PrecioUnit" style="text-align:right;" value="0,00" onBlur="numeroBlur(this);" onFocus="numeroFocus(this);" onchange="setMontosAlmacen(this.form);" <?=$dPrecioUnit?> />
            </td>
			<td align="center">
            	<input type="text" name="Total" class="cell2" style="text-align:right;" value="0,00" readonly="readonly" />
            </td>
			<td align="center">
            	<input type="hidden" name="CodCentroCosto" value="<?=$CodCentroCosto?>" />
            	<input type="hidden" name="ReferenciaCodDocumento" value="<?=$CodDocumentoReferencia?>" />
            	<input type="hidden" name="ReferenciaNroDocumento" value="<?=$NroDocumentoReferencia?>" />
            	<input type="hidden" name="ReferenciaSecuencia" value="<?=$nrodetalles?>" />
                <?=$CodDocumentoReferencia?>-<?=$NroDocumentoReferencia?>-<?=$nrodetalles?>
			</td>
		</tr>
       <?php
	}
}

elseif ($accion == "vacaciones_insertar_linea") {
	if ($UltimaFechaTermino != "") {
		$NroDias = $NroDias - $TotalDias;
		$FechaSalida = getFechaFinHabiles($UltimaFechaTermino, 2);
	}
	if ($NroDias > $Pendientes) {
		$Dias = $Pendientes;
		$FechaTermino = getFechaFinHabiles($FechaSalida, $Dias);
	} else {
		$Dias = $NroDias;
	}
	?>
	<tr class="trListaBody" onclick="mClk(this, 'sel_detalles');" id="<?=$NroPeriodo?>">
		<th>
           <input type="text" name="NroPeriodo" id="NroPeriodo_<?=$i?>" class="cell2" style="text-align:center;" value="<?=$NroPeriodo?>" readonly />
		</th>
		<td align="center">
           <input type="checkbox" name="FlagUtlizarPeriodo" checked="checked" disabled="disabled" />
		</td>
        <td align="center"><?=$Anio?> - <?=$Anio+1?></td>
		<td>
           <input type="text" name="NroDias" id="NroDias_<?=$i?>" class="cell" style="text-align:right;" value="<?=number_format($Dias, 2, ',', '.')?>" onchange="obtenerFechaTerminoVacacionDetalle('<?=$i?>');" onfocus="numeroFocus(this);" onblur="numeroBlur(this);" />
		</td>
		<td>
           <input type="text" name="FechaInicio" id="FechaInicio_<?=$i?>" maxlength="10" style="text-align:center;" class="cell datepicker" onkeyup="setFechaDMA(this);" value="<?=$FechaSalida?>" onchange="obtenerFechaTerminoVacacionDetalle('<?=$i?>');" />
		</td>
		<td>
           <input type="text" name="FechaFin" id="FechaFin_<?=$i?>" maxlength="10" style="text-align:center;" class="cell datepicker" onkeyup="setFechaDMA(this);" value="<?=$FechaTermino?>" />
		</td>
		<td>
           <input type="text" name="Derecho" id="Derecho_<?=$i?>" class="cell2" style="text-align:right;" value="<?=number_format($Derecho, 2, ',', '.')?>" readonly />
		</td>
		<td>
           <input type="text" name="TotalUtilizados" id="TotalUtilizados_<?=$i?>" class="cell2" style="text-align:right;" value="<?=number_format($TotalUtilizados, 2, ',', '.')?>" readonly />
		</td>
		<td>
           <input type="text" name="Pendientes" id="Pendientes_<?=$i?>" class="cell2" style="text-align:right;" value="<?=number_format($Pendientes, 2, ',', '.')?>" readonly />
		</td>
		<td>
			<textarea name="Observaciones" class="cell" style="height:20px;" disabled="disabled"></textarea>
			<input type="hidden" name="Secuencia" />
		</td>
	</tr>
   <?php
}

elseif ($accion == "selListadoVacacionPeriodo") {
	//	empleado
	$sql = "SELECT Fingreso, CodTipoNom
			FROM mastempleado
			WHERE CodPersona = '".$CodPersona."'";
	$query_empleado = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query_empleado)) $field_empleado = mysql_fetch_array($query_empleado);
	
	//	consutlo periodos
	$sql = "SELECT
				(SUM(vp.Derecho) - SUM(vp.DiasGozados) + SUM(vp.DiasInterrumpidos)) AS Pendientes,
				o.CodOrganismo,
				o.Organismo,
				d.CodDependencia,
				d.Dependencia
			FROM
				rh_vacacionperiodo vp
				LEFT JOIN mastpersonas p ON (p.CodPersona = vp.CodPersona)
				LEFT JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
				LEFT JOIN mastdependencias d ON (d.CodDependencia = e.CodDependencia)
				LEFT JOIN mastorganismos o ON (o.CodOrganismo = d.CodOrganismo)
			WHERE
				vp.CodPersona = '".$CodPersona."' AND
				vp.CodTipoNom = '".$field_empleado['CodTipoNom']."'
			GROUP BY vp.CodPersona";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	echo number_format($field['Pendientes'], 2, ',', '.')."|"."<option value='$field[CodOrganismo]'>$field[Organismo]</option>"."|"."<option value='$field[CodDependencia]'>$field[Dependencia]</option>|";
	//	---------------------
	$NroDias = $field['Pendientes'];
	
	//	obtengo los valores almacenados del empleado para el periodo
	$sql = "SELECT
				NroPeriodo,
				Anio,
				Mes,
				Derecho,
				PendientePeriodo,
				DiasGozados,
				DiasTrabajados,
				DiasInterrumpidos,
				DiasNoGozados,
				TotalUtilizados,
				Pendientes
			FROM rh_vacacionperiodo
			WHERE
				CodPersona = '".$CodPersona."' AND
				CodTipoNom = '".$field_empleado['CodTipoNom']."'";
	$query_periodo = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));	$i=0;
	$rows_periodo = mysql_num_rows($query_periodo);
	while ($field_periodo = mysql_fetch_array($query_periodo)) {
		$NroPeriodo[$i] = $field_periodo['NroPeriodo'];
		$Anio[$i] = $field_periodo['Anio'];
		$Mes[$i] = $field_periodo['Mes'];
		$Derecho[$i] = $field_periodo['Derecho'];
		$PendientePeriodo[$i] = $field_periodo['PendientePeriodo'];
		$DiasGozados[$i] = $field_periodo['DiasGozados'];
		$DiasTrabajados[$i] = $field_periodo['DiasTrabajados'];
		$DiasInterrumpidos[$i] = $field_periodo['DiasInterrumpidos'];
		$DiasNoGozados[$i] = $field_periodo['DiasNoGozados'];
		$TotalUtilizados[$i] = $field_periodo['TotalUtilizados'];
		$Pendientes[$i] = $field_periodo['Pendientes'];
		$i++;
	}
	
	//	tiempo de servicio
	list($AnioActual, $MesActual, $DiaActual) = split("[/.-]", substr($Ahora, 0, 10));
	list($AnioIngreso, $MesIngreso, $DiaIngreso) = split("[/.-]", $field_empleado['Fingreso']);
	list($Anios, $Meses, $Dias) = getTiempo(formatFechaDMA($field_empleado['Fingreso']), "$DiaActual-$MesActual-$AnioActual");
	$NroPeriodos = $rows_periodo;
	
	//	recorro los periodos y almaceno
	$FechaInicio = $FechaSalida;
	$Distribucion = $NroDias;
	$Quinquenios = 0;
	$Pendiente = 0;
	$Seleccionable = false;
	for($i=0; $i<$NroPeriodos; $i++) {
		$Anio[$i] = $AnioIngreso + $i;
		if ($NroPeriodo[$i] == "") {
			$NroPeriodo[$i] = $i + 1;
			$Mes[$i] = $MesIngreso;
			##	obtengo los dias de derecho
			if ($i > 0 && $i % 5 == 0) ++$Quinquenios;
			$Derecho[$i] = $_PARAMETRO['DERECHO'] + $i + $Quinquenios;
			$PendientePeriodo[$i] += $Pendientes[$i-1];
			$DiasGozados[$i] = 0;
			$DiasTrabajados[$i] = 0;
			$DiasInterrumpidos[$i] = 0;
			$TotalUtilizados[$i] = 0;
		}
		$Pendientes[$i] = $Derecho[$i] - $TotalUtilizados[$i];
		if ($Pendientes[$i] > 0 && $Distribucion > 0) {
			if ($Pendientes[$i] > $Distribucion) $Dias = $Distribucion; else $Dias = $Pendientes[$i];
			$Distribucion -= $Dias;
			$FechaFin = getFechaFinHabiles($FechaInicio, $Dias);
			?>
			<tr class="trListaBody" onclick="mClk(this, 'sel_detalles');" id="<?=$NroPeriodo[$i]?>">
				<th>
				   <input type="text" name="NroPeriodo" id="NroPeriodo_<?=$i?>" class="cell2" style="text-align:center;" value="<?=$NroPeriodo[$i]?>" readonly />
				</th>
				<td align="center">
				   <input type="checkbox" name="FlagUtlizarPeriodo" checked="checked" disabled="disabled" />
				</td>
				<td align="center"><?=$Anio[$i]?> - <?=$Anio[$i]+1?></td>
				<td>
				   <input type="text" name="NroDias" id="NroDias_<?=$i?>" class="cell" style="text-align:right;" value="<?=number_format($Dias, 2, ',', '.')?>" onchange="obtenerFechaTerminoVacacionDetalle('<?=$i?>');" onfocus="numeroFocus(this);" onblur="numeroBlur(this);" disabled="disabled" />
				</td>
				<td>
				   <input type="text" name="FechaInicio" id="FechaInicio_<?=$i?>" maxlength="10" style="text-align:center;" class="cell datepicker" onkeyup="setFechaDMA(this);" value="<?=$FechaInicio?>" onchange="obtenerFechaTerminoVacacionDetalle('<?=$i?>');" disabled="disabled" />
				</td>
				<td>
				   <input type="text" name="FechaFin" id="FechaFin_<?=$i?>" maxlength="10" style="text-align:center;" class="cell datepicker" onkeyup="setFechaDMA(this);" value="<?=$FechaFin?>" disabled="disabled" />
				</td>
				<td>
				   <input type="text" name="Derecho" id="Derecho_<?=$i?>" class="cell2" style="text-align:right;" value="<?=number_format($Derecho[$i], 2, ',', '.')?>" readonly />
				</td>
				<td>
				   <input type="text" name="TotalUtilizados" id="TotalUtilizados_<?=$i?>" class="cell2" style="text-align:right;" value="<?=number_format($TotalUtilizados[$i], 2, ',', '.')?>" readonly />
				</td>
				<td>
				   <input type="text" name="Pendientes" id="Pendientes_<?=$i?>" class="cell2" style="text-align:right;" value="<?=number_format($Pendientes[$i], 2, ',', '.')?>" readonly />
				</td>
				<td>
					<textarea name="Observaciones" class="cell" style="height:20px;" disabled="disabled"></textarea>
					<input type="hidden" name="Secuencia" />
				</td>
			</tr>
			<?php
			$FechaInicio = getFechaFinHabiles($FechaFin, 2);
		}
	}
	$FechaIncorporacion = getFechaFinHabiles($FechaFin, 2);
	echo "|$FechaFin|$FechaIncorporacion";
}

elseif ($accion == "requerimientos_cargo_selector") {
	//	evaluacion
	$sql = "SELECT
				e.Descripcion,
				e.Plantilla,
				ce.Etapa,
				ce.Evaluacion
			FROM
				rh_cargoevaluacion ce
				INNER JOIN rh_evaluacion e ON (e.Evaluacion = ce.Evaluacion)
			WHERE ce.CodCargo = '".$CodCargo."'";
    $query_evaluacion = mysql_query($sql) or die ($sql.mysql_error());	$nro_evaluacion=0;
    while ($field_evaluacion = mysql_fetch_array($query_evaluacion)) {	$nro_evaluacion++;
        ?>
        <tr class="trListaBody" onclick="mClk(this, 'sel_evaluacion');" id="evaluacion_<?=$field_evaluacion['Evaluacion']?>">
            <th>
            	<input type="hidden" name="Secuencia" value="<?=$nro_evaluacion?>" />
            	<input type="hidden" name="Evaluacion" value="<?=$field_evaluacion['Evaluacion']?>" />
            	<input type="hidden" name="Etapa" value="<?=$field_evaluacion['Etapa']?>" />
            	<input type="hidden" name="PlantillaEvaluacion" value="<?=$field_evaluacion['Plantilla']?>" />
				<?=$nro_evaluacion?>
            </th>
            <td>
                <?=$field_evaluacion['Descripcion']?>
            </td>
            <td align="center">
                <?=$field_evaluacion['Etapa']?>
            </td>
        </tr>
        <?php
    }
	echo "|$nro_evaluacion|";
	printBodyCompetenciasCargo($CodCargo, "E", 150, 8);
}

elseif ($accion == "insertar_linea_evaluacion") {
	//	evaluacion
	$sql = "SELECT * FROM rh_evaluacion WHERE Evaluacion = '".$Evaluacion."'";
    $query_evaluacion = mysql_query($sql) or die ($sql.mysql_error());
    while ($field_evaluacion = mysql_fetch_array($query_evaluacion)) {
        ?>
        <tr class="trListaBody" onclick="mClk(this, 'sel_evaluacion');" id="evaluacion_<?=$field_evaluacion['Evaluacion']?>">
            <th>
            	<input type="hidden" name="Secuencia" value="<?=$nro_detalles?>" />
            	<input type="hidden" name="Evaluacion" value="<?=$field_evaluacion['Evaluacion']?>" />
            	<input type="hidden" name="Etapa" value="<?=$field_evaluacion['Etapa']?>" />
            	<input type="hidden" name="PlantillaEvaluacion" value="<?=$field_evaluacion['Plantilla']?>" />
				<?=$nro_detalles?>
            </th>
            <td>
                <?=$field_evaluacion['Descripcion']?>
            </td>
            <td align="center">
                <?=$field_evaluacion['Etapa']?>
            </td>
        </tr>
        <?php
    }
}

elseif ($accion == "insertar_linea_postulante") {
	//	evaluacion
	$sql = "SELECT
				e.CodEmpleado,
				p.CodPersona,
				p.NomCompleto
			FROM
				mastpersonas p
				INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
			WHERE
				CodPersona = '".$CodPersona."'";
    $query_candidato = mysql_query($sql) or die ($sql.mysql_error());
    while ($field_candidato = mysql_fetch_array($query_candidato)) {
        ?>
        <tr class="trListaBody" onclick="mClk(this, 'sel_candidato');" id="I-<?=$field_candidato['CodPersona']?>">
            <th>
            	<input type="hidden" name="TipoPostulante" value="I" />
            	<input type="hidden" name="Postulante" value="<?=$field_candidato['CodPersona']?>" />
				<?=$nro_detalles?>
            </th>
            <td align="center">
                <?=$field_candidato['CodPersona']?>
            </td>
            <td>
                <?=$field_candidato['NomCompleto']?>
            </td>
        </tr>
        <?php
    }
}

//	obtener la edad a partir de una fecha
elseif ($accion == "getEdad") {
	list($Anios, $Meses, $Dias) = getEdad($FechaDesde, $FechaHasta);
	echo "$Anios|$Meses|$Dias";
}

//	eliminar
elseif ($accion == "unlink") {
	unlink($url);
}

//	inserto linea de participante en capacitaciones
elseif ($accion == "insertar_linea_participantes") {
	//	persona
	$sql = "SELECT
				p.CodPersona,
				p.NomCompleto,
				e.CodEmpleado,
				e.CodDependencia
			FROM
				mastpersonas p 
				INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
			WHERE p.CodPersona = '".$CodPersona."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
    while ($field = mysql_fetch_array($query)) {
        ?>
        <tr class="trListaBody" onclick="mClk(this, 'sel');" id="participantes_<?=$nro_detalles?>">
            <th>
            	<input type="hidden" name="CodPersona" value="<?=$field['CodPersona']?>" />
            	<input type="hidden" name="CodDependencia" value="<?=$field['CodDependencia']?>" />
				<?=$nro_detalles?>
            </th>
            <td align="center">
                <?=$field['CodEmpleado']?>
            </td>
            <td>
                <?=htmlentities($field['NomCompleto'])?>
            </td>
            <td align="center">
            	0
            </td>
            <td align="center">
            	0
            </td>
            <td align="center">
            	0
            </td>
            <td align="center">
            	<?=printFlag("N")?>
            </td>
            <td align="center">
            	0
            </td>
            <td align="right">
            	0,00
            </td>
        </tr>
        <?php
    }
}

//	obtener fecha fin a partir de una fecha inicial + dias
elseif ($accion == "obtenerFechaFin") {
	die(obtenerFechaFin($FechaInicial, $Dias));
}

//	inserto linea
elseif ($accion == "competencias_plantilla_insertar") {
	//	persona
	$sql = "SELECT Competencia, Descripcion FROM rh_evaluacionfactores WHERE Competencia = '".$Competencia."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
    while ($field = mysql_fetch_array($query)) {
        ?>
        <tr class="trListaBody" onclick="mClk(this, 'sel_competencias');" id="competencias_<?=$field['Competencia']?>">
            <th>
				<?=$nro_detalles?>
            </th>
            <td>
            	<input type="text" name="Competencia" class="cell" style="text-align:center;" value="<?=$field['Competencia']?>" />
            </td>
            <td>
            	<?=$field['Descripcion']?>
            </td>
            <td>
            	<input type="text" name="Peso" class="cell" style="text-align:center;" maxlength="4" />
            </td>
            <td>
            	<input type="text" name="FactorParticipacion" class="cell" style="text-align:center;" maxlength="4" />
            </td>
            <td align="center">
            	<input type="checkbox" name="FlagPotencial" <?=chkFlag('N')?> />
            </td>
            <td align="center">
            	<input type="checkbox" name="FlagCompetencia" <?=chkFlag('N')?> />
            </td>
            <td align="center">
            	<input type="checkbox" name="FlagConceptual" <?=chkFlag('N')?> />
            </td>
        </tr>
        <?php
    }
}

elseif ($accion == "getSueldoBasico") {
	$sql = "SELECT
				pt.NivelSalarial,
				md.Descripcion AS NomCategoriaCargo
			FROM
				rh_puestos pt
				LEFT JOIN mastmiscelaneosdet md ON (md.CodDetalle = pt.CategoriaCargo AND
													md.CodMaestro = 'CATCARGO')
			WHERE pt.CodCargo = '".$CodCargo."'";
	$query = mysql_query($sql) or die($sql.mysql_error());
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	echo "$field[NomCategoriaCargo]|".number_format($field['NivelSalarial'], 2, ',', '.');
}

//	inserto linea
elseif ($accion == "bono_periodos_empleados_insertar") {
	if ($CodPersona != "") $filtro .= " AND p.CodPersona = '".$CodPersona."'";
	if ($CodOrganismo != "") $filtro .= " AND e.CodOrganismo = '".$CodOrganismo."'";
	if ($CodTipoNom != "") $filtro .= " AND e.CodTipoNom = '".$CodTipoNom."'";
	if ($EdoReg != "") $filtro .= " AND p.Estado = '".$EdoReg."'";
	if ($SitTra != "") $filtro .= " AND e.Estado = '".$SitTra."'";
	//	persona
	$sql = "SELECT
				p.CodPersona,
				p.NomCompleto,
				p.Ndocumento,
				e.CodEmpleado,
				o.Organismo,
				d.Dependencia,
				pt.DescripCargo
			FROM
				mastpersonas p
				INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
				INNER JOIN mastorganismos o ON (o.CodOrganismo = e.CodOrganismo)
				INNER JOIN mastdependencias d ON (d.CodDependencia = e.CodDependencia)
				INNER JOIN rh_puestos pt ON (pt.CodCargo = e.CodCargo)
			WHERE 1 $filtro
			ORDER BY CodEmpleado";
	$query_empleados = mysql_query($sql) or die ($sql.mysql_error());
	while ($field_empleados = mysql_fetch_array($query_empleados)) {
        ?>
        <tr class="trListaBody" onclick="mClk(this, 'sel_empleados');" id="empleados_<?=$field_empleados['CodPersona']?>">
            <th>
				<?=$nro_detalles?>
            </th>
			<td align="center">
				<input type="hidden" name="CodPersona" value="<?=$field_empleados['CodPersona']?>" />
                <?=$field_empleados['CodEmpleado']?>
			</td>
			<td align="right">
                <?=$field_empleados['Ndocumento']?>
			</td>
			<td>
                <?=htmlentities($field_empleados['NomCompleto'])?>
			</td>
			<td>
                <?=htmlentities($field_empleados['DescripCargo'])?>
			</td>
			<td>
                <?=htmlentities($field_empleados['Dependencia'])?>
			</td>
        </tr>
        <?php
		$nro_detalles++;
    }
}

elseif ($accion == "getDiffHora") {
	echo getDiffHora($Desde, $Hasta);
}

//	insertar linea
elseif ($accion == "cuentas_bancarias_tipopagos_insertar") {
	$sql = "SELECT tp.*
			FROM masttipopago tp
			WHERE CodTipoPago = '".$CodTipoPago."'";
	$query_tipopagos = mysql_query($sql) or die ($sql.mysql_error());
	while ($field_tipopagos = mysql_fetch_array($query_tipopagos)) {
		?>
        <tr class="trListaBody" onclick="clk($(this), 'tipopagos', '<?=$field_tipopagos['CodTipoPago']?>');" id="tipopagos_<?=$field_tipopagos['CodTipoPago']?>">
			<th>
				<?=$nro_detalles?>
			</th>
			<td>
				<input type="hidden" name="CodTipoPago" value="<?=$field_tipopagos['CodTipoPago']?>" />
                <?=htmlentities($field_tipopagos['TipoPago'])?>
			</td>
			<td>
                <input type="text" name="UltimoNumero" style="text-align:right;" class="cell" value="<?=$field_tipopagos['UltimoNumero']?>" maxlength="10" />
			</td>
		</tr>
		<?php
	}
}

//	insertar linea
elseif ($accion == "conceptos_nominas_insertar") {
	$sql = "SELECT
				tn.CodTipoNom,
				tn.Nomina
			FROM tiponomina tn
			WHERE tn.CodTipoNom = '".$CodTipoNom."'";
	$query_nominas = mysql_query($sql) or die ($sql.mysql_error());
	while ($field_nominas = mysql_fetch_array($query_nominas)) {
		?>
		<tr class="trListaBody" onclick="clk($(this), 'nominas', '<?=$field_nominas['CodTipoNom']?>');" id="nominas_<?=$field_nominas['CodTipoNom']?>">
			<th>
				<?=$nro_detalles?>
			</th>
			<td>
				<input type="hidden" name="CodTipoNom" value="<?=$field_nominas['CodTipoNom']?>" />
				<?=htmlentities($field_nominas['Nomina'])?>
			</td>
		</tr>
		<?php
	}
}

//	insertar linea
elseif ($accion == "conceptos_procesos_insertar") {
	$sql = "SELECT
				tp.CodTipoProceso,
				tp.Descripcion AS NomTipoProceso
			FROM pr_tipoproceso tp
			WHERE tp.CodTipoProceso = '".$CodTipoProceso."'";
	$query_nominas = mysql_query($sql) or die ($sql.mysql_error());
	$query_procesos = mysql_query($sql) or die ($sql.mysql_error());
	while ($field_procesos = mysql_fetch_array($query_procesos)) {
		?>
		<tr class="trListaBody" onclick="clk($(this), 'procesos', '<?=$field_procesos['CodTipoProceso']?>');" id="procesos_<?=$field_procesos['CodTipoProceso']?>">
			<th>
				<?=$nro_detalles?>
			</th>
			<td>
				<input type="hidden" name="CodTipoProceso" value="<?=$field_procesos['CodTipoProceso']?>" />
				<?=htmlentities($field_procesos['NomTipoProceso'])?>
			</td>
		</tr>
		<?php
	}
}

//	obtener presupuesto
elseif ($accion == "setPresupuesto") {
	echo setPresupuesto($Organismo, $EjercicioPpto);
}

//	insertar linea
elseif ($accion == "transacciones_bancarias_tipo_insertar") {
	$sql = "SELECT 
				CodTipoTransaccion,
				Descripcion,
				TipoTransaccion
            FROM ap_bancotipotransaccion
            WHERE CodTipoTransaccion = '".$CodTipoTransaccion."'";
	$query_transacciones = mysql_query($sql) or die ($sql.mysql_error());
	while ($field_transacciones = mysql_fetch_array($query_transacciones)) {
		$id = $nro_detalles;
		?>
        <tr class="trListaBody" onclick="clk($(this), 'transacciones', 'transacciones_<?=$id?>');" id="transacciones_<?=$id?>">
			<th>
				<input type="hidden" name="Secuencia" value="<?=$nro_detalles?>" />
				<?=$nro_detalles?>
			</th>
			<td align="center" width="35">
				<input type="text" name="CodTipoTransaccion" class="cell2" style="text-align:center;" value="<?=$field_transacciones['CodTipoTransaccion']?>" readonly />
			</td>
			<td align="center">
				<input type="text" name="NomTipoTransaccion" class="cell2" value="<?=htmlentities($field_transacciones['Descripcion'])?>" readonly />
			</td>
			<td align="center">
				<input type="text" name="TipoTransaccion" class="cell2" style="text-align:center;" value="<?=$field_transacciones['TipoTransaccion']?>" readonly />
			</td>
			<td align="center">
                <select name="NroCuenta" class="cell">
                    <option value="">&nbsp;</option>
                    <?=loadSelect("ap_ctabancaria", "NroCuenta", "NroCuenta", "", 0)?>
                </select>
			</td>
			<td align="center">
                <input type="text" name="Monto" class="cell" style="text-align:right;" onblur="numeroBlur(this);" onfocus="numeroFocus(this);" />
			</td>
			<td align="center">
                <select name="CodTipoDocumento" class="cell">
                    <option value="">&nbsp;</option>
                    <?=loadSelect("ap_tipodocumento", "CodTipoDocumento", "Descripcion", "", 10)?>
                </select>
			</td>
			<td align="center">
                <input type="text" name="CodigoReferenciaBanco" class="cell" />
			</td>
			<td align="center">
                <input type="text" name="CodProveedor" id="CodProveedor_<?=$id?>" class="cell" style="text-align:center;" />
			</td>
			<td align="center">
                <input type="text" name="CodCentroCosto" id="CodCentroCosto_<?=$id?>" class="cell" style="text-align:center;" />
			</td>
			<td align="center">
                <input type="text" name="CodPartida" id="CodPartida_<?=$id?>" class="cell" style="text-align:center;" />
			</td>
	        <td align="center">
	            <input type="text" name="detallesCategoriaProg" id="detallesCategoriaProg_<?=$id?>" class="cell2 CategoriaProg" style="text-align:center;" readonly />
	            <input type="hidden" name="detallesEjercicio" id="detallesEjercicio_<?=$id?>" class="cell2 Ejercicio" style="text-align:center;" readonly />
	            <input type="hidden" name="detallesCodPresupuesto" id="detallesCodPresupuesto_<?=$id?>" class="cell2 CodPresupuesto" style="text-align:center;" readonly />
	        </td>
	        <td>
	            <select name="detallesCodFuente" id="detallesCodFuente_<?=$id?>" class="cell2 CodFuente" <?=$disabled_ver?>>
	                <?=loadSelect2("pv_fuentefinanciamiento","CodFuente","Denominacion",$_PARAMETRO['FFMETASDEF'],10)?>
	            </select>
	        </td>
		</tr>
		<?php
	}
}

//	insertar linea
elseif ($accion == "cotizaciones_proveedores_insertar") {
	$sql = "SELECT
				p.CodPersona,
				p.NomCompleto,
				fp.Descripcion AS NomFormaPago
			FROM
				mastpersonas p
				LEFT JOIN mastproveedores pv ON (pv.CodProveedor = p.CodPersona)
				LEFT JOIN mastformapago fp ON (fp.CodFormaPago = pv.CodFormaPago)
			WHERE p.CodPersona = '".$CodPersona."'";
	$query = mysql_query($sql) or die ($sql.mysql_error());
	while ($field = mysql_fetch_array($query)) {
		?>
		<tr class="trListaBody" onclick="clk($(this), 'proveedores', '<?=$field['CodPersona']?>');" id="proveedores_<?=$field['CodPersona']?>">
			<th>
				<?=$nro_detalles?>
			</th>
			<td>
				<input type="hidden" name="CodPersona" value="<?=$field['CodPersona']?>" />
				<input type="hidden" name="NomPersona" value="<?=$field['NomCompleto']?>" />
				<?=htmlentities($field['NomCompleto'])?>
			</td>
			<td>
				<select name="CodFormaPago" class="cell">
                	<?=loadSelect("mastformapago", "CodFormaPago", "Descripcion", $field['CodFormaPago'], 0)?>
                </select>
			</td>
		</tr>
		<?php
	}
}

//	insertar linea
elseif ($accion == "cotizaciones_proveedores_cotizar_insertar") {
	//	requerimiento
	$sql = "SELECT
				rd.CodUnidad,
				rd.CantidadPedida
			FROM
				lg_requerimientosdet rd
			WHERE
				rd.CodRequerimiento = '".$CodRequerimiento."' AND
				rd.Secuencia = '".$Secuencia."'";
	$query_requerimiento = mysql_query($sql) or die ($sql.mysql_error());
	if (mysql_num_rows($query_requerimiento) != 0) $field_requerimiento = mysql_fetch_array($query_requerimiento);	
	//	proveedor
	$sql = "SELECT
				p.CodPersona,
				p.NomCompleto,
				mp.CodFormaPago,
				i.CodImpuesto,
				i.FactorPorcentaje
			FROM
				mastpersonas p
				INNER JOIN mastproveedores mp ON (mp.CodProveedor = p.CodPersona)
				LEFT JOIN masttiposervicioimpuesto tsi ON (mp.CodTipoServicio = tsi.CodTipoServicio)
				LEFT JOIN mastimpuestos i ON (tsi.CodImpuesto = i.CodImpuesto AND i.CodRegimenFiscal = 'I')
			WHERE p.CodPersona = '".$CodPersona."'";
	$query_proveedores = mysql_query($sql) or die ($sql.mysql_error());
	if (mysql_num_rows($query_proveedores) != 0) $field_proveedores = mysql_fetch_array($query_proveedores);
	//	linea
	$id = "proveedores_".$field_proveedores['CodPersona'];
	?>
	<tr class="trListaBody" onclick="clk($(this), 'proveedores', '<?=$id?>');" id="<?=$id?>">
		<th>
			<?=$nro_detalles?>
		</th>
		<td>
			<input type="hidden" name="CotizacionSecuencia" />
			<input type="hidden" name="CodProveedor" value="<?=$field_proveedores['CodPersona']?>" />
			<input type="hidden" name="NomProveedor" value="<?=htmlentities($field_proveedores['NomCompleto'])?>" />
			<?=htmlentities($field_proveedores['NomCompleto'])?>
		</td>
		<td align="center">
			<input type="checkbox" name="FlagAsignado" id="FlagAsignado_<?=$id?>" class="FlagAsignado" onclick="setFlagAsignado($(this));" />
		</td>
		<td align="center">
			<input type="text" name="CodUnidad" id="CodUnidad_<?=$id?>" value="<?=$field_requerimiento['CodUnidad']?>" class="cell2 currency" style="text-align:center;" readonly="readonly" />
		</td>
		<td align="center">
			<input type="text" name="Cantidad" id="Cantidad_<?=$id?>" value="<?=number_format($field_requerimiento['CantidadPedida'], 2, ',', '.')?>" class="cell currency" style="text-align:right;" onfocus="nroFocus($(this));" onblur="nroBlur($(this));" />
		</td>
		<td align="center">
			<select name="CodUnidadCompra" id="CodUnidadCompra_<?=$id?>" class="cell">
				<?=loadSelect("mastunidades", "CodUnidad", "CodUnidad", $field_requerimiento['CodUnidad'], 0)?>
			</select>
		</td>
		<td align="center">
			<input type="text" name="CantidadCompra" id="CantidadCompra_<?=$id?>" value="<?=number_format($field_requerimiento['CantidadPedida'], 2, ',', '.')?>" class="cell currency" style="text-align:right;" onchange="cotizaciones_items_totales('<?=$id?>');" onfocus="nroFocus($(this));" onblur="nroBlur($(this));" />
		</td>
		<td align="center">
			<input type="text" name="PrecioUnitInicio" id="PrecioUnitInicio_<?=$id?>" value="0,00" class="cell currency" style="text-align:right;" onchange="cotizaciones_items_totales('<?=$id?>');" onfocus="nroFocus($(this));" onblur="nroBlur($(this));" />
		</td>
		<td align="center">
			<input type="checkbox" name="FlagExonerado" id="FlagExonerado_<?=$id?>" value="<?=$field_proveedores['FactorPorcentaje']?>" onchange="cotizaciones_items_totales('<?=$id?>');" />
		</td>
		<td align="center">
			<input type="text" name="PrecioUnitInicioIva" id="PrecioUnitInicioIva_<?=$id?>" value="0,00" class="cell2 currency" style="text-align:right;" readonly="readonly" />
		</td>
		<td align="center">
			<input type="text" name="DescuentoPorcentaje" id="DescuentoPorcentaje_<?=$id?>" value="0,00" class="cell currency" style="text-align:right;" onchange="cotizaciones_items_totales('<?=$id?>');" onfocus="nroFocus($(this));" onblur="nroBlur($(this));" />
		</td>
		<td align="center">
			<input type="text" name="DescuentoFijo" id="DescuentoFijo_<?=$id?>" value="0,00" class="cell currency" style="text-align:right;" onchange="cotizaciones_items_totales('<?=$id?>');" onfocus="nroFocus($(this));" onblur="nroBlur($(this));" />
		</td>
		<td align="center">
			<input type="text" name="PrecioUnitIva" id="PrecioUnitIva_<?=$id?>" value="0,00" class="cell2 currency" style="text-align:right;" readonly="readonly" />
		</td>
		<td align="center">
			<input type="text" name="Total" id="Total_<?=$id?>" value="0,00" class="cell2 currency" style="text-align:right; font-weight:bold;" readonly="readonly" />
		</td>
		<td align="center">
			<input type="text" name="PrecioUnitFinal" id="PrecioUnitFinal_<?=$id?>" value="0,00" class="cell2 currency" style="text-align:right; font-weight:bold;" readonly="readonly" />
		</td>
		<td align="center">
			<input type="checkbox" name="FlagMejorPrecio" id="FlagMejorPrecio_<?=$id?>" class="FlagMejorPrecio" onclick="this.checked=!this.checked;" />
		</td>
		<td align="center">
			<select name="CodFormaPago" class="cell">
				<?=loadSelect("mastformapago", "CodFormaPago", "Descripcion", $field_proveedores['CodFormaPago'], 0)?>
			</select>
		</td>
		<td align="center">
			<input type="text" name="FechaInvitacion" value="<?=formatFechaDMA($FechaActual)?>" class="cell datepicker" style="text-align:center;" />
		</td>
		<td align="center">
			<input type="text" name="FechaEntrega" value="<?=formatFechaDMA($FechaActual)?>" class="cell datepicker" style="text-align:center;" onchange="obtenerFechaFin($(this), $('#FechaLimite_<?=$id?>'), '<?=$_PARAMETRO['DIASLIMCOT']?>');" />
		</td>
        <td align="center">
            <input type="text" name="FechaRecepcion" value="<?=formatFechaDMA($FechaActual)?>" class="cell datepicker" style="text-align:center;" />
        </td>
        <td align="center">
            <input type="text" name="FechaLimite" id="FechaLimite_<?=$id?>" value="<?=obtenerFechaFin(formatFechaDMA($FechaActual), $_PARAMETRO['DIASLIMCOT'])?>" class="cell2" style="text-align:center;" readonly="readonly" />
        </td>
		<td align="center">
			<textarea name="Condiciones" class="cell" style="height:15px;"></textarea>
		</td>
		<td align="center">
			<textarea name="Observaciones" class="cell" style="height:15px;"></textarea>
		</td>
		<td align="center">
			<input type="text" name="DiasEntrega" value="0" class="cell" />
		</td>
		<td align="center">
			<input type="text" name="ValidezOferta" value="0" class="cell" />
		</td>
		<td align="center">
			<input type="text" name="NumeroCotizacion" value="" class="cell" maxlength="10" />
		</td>
        <td align="center">
            <input type="text" name="FechaDocumento" value="<?=formatFechaDMA($FechaActual)?>" class="cell datepicker" style="text-align:center;" />
        </td>
        <td align="center"></td>
	</tr>
	<?php

}

//	cese/reingreso seleccionar empleado
elseif ($accion == "reingreso_empleado_sel") {
	list($AnioActual, $MesActual, $DiaActual) = split("[/.-]", substr($Ahora, 0, 10));
	$FechaActual = "$AnioActual-$MesActual-$DiaActual";
	$sql = "SELECT
				p.CodPersona,
				p.NomCompleto,
				p.Sexo,
				p.Ndocumento,
				p.Fnacimiento,
				e.CodOrganismo,
				e.CodDependencia,
				e.CodCargo,
				e.Fingreso,
				e.CodTipoNom,
				e.CodTipoTrabajador,
				e.Estado AS SitTra,
				pu.DescripCargo,
				pu.NivelSalarial
			FROM
				mastpersonas p
				INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
				INNER JOIN rh_puestos pu ON (pu.CodCargo = e.CodCargo)
			WHERE p.CodPersona = '".$CodPersona."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query)) {
		$field = mysql_fetch_array($query);
		list($Edad, $EdadMeses, $EdadDias) = getEdad(formatFechaDMA($field['Fnacimiento']), formatFechaDMA($FechaActual));
		list($AnioServicio, $MesesServicio, $DiasServicio) = getEdad(formatFechaDMA($field['Fingreso']), formatFechaDMA($FechaActual));
		if ($field['SitTra'] == "A") {
			$datos = "true";
			$cese = "false";
			$planilla = "false";
			$SitTraA = "false";
			$SitTraI = "true";
			$Tipo = "C";
			$btCargo = "hidden";
		} else {
			$datos = "false";
			$cese = "true";
			$planilla = "false";
			$SitTraA = "true";
			$SitTraI = "false";
			$Tipo = "R";
			$btCargo = "visible";
		}
		echo "
		parent.$('.datos').prop('disabled', $datos).val('');
		parent.$('.cese').prop('disabled', $cese).val('');
		parent.$('.planilla').prop('disabled', $planilla).val('');
		parent.$('#btCargo').css('visibility', '$btCargo');
		parent.$('#Tipo').val('$Tipo');
		parent.$('#CodPersona').val('$field[CodPersona]');
		parent.$('#CodEmpleado').val('$field[CodEmpleado]');
		parent.$('#NomEmpleado').val('".($field['NomCompleto'])."');
		parent.$('#CodOrganismo').val('$field[CodOrganismo]');
		parent.$('#CodDependencia').val('$field[CodDependencia]');
		parent.$('#CodCargo').val('$field[CodCargo]');
		parent.$('#DescripCargo').val('$field[DescripCargo]');
		parent.$('#SueldoActual').val('".number_format($field['NivelSalarial'], 2, ',', '.')."');
		parent.$('#Ndocumento').val('$field[Ndocumento]');
		parent.$('#Sexo').val('$field[Sexo]');
		parent.$('#Fnacimiento').val('".formatFechaDMA($field['Fnacimiento'])."');
		parent.$('#Edad').val('$Edad');
		parent.$('#AnioServicio').val('$AnioServicio');
		parent.$('#FechaIngreso').val('".formatFechaDMA($field['Fingreso'])."');
		parent.$('#CodTipoNom').val('$field[CodTipoNom]');
		parent.$('#CodTipoTrabajador').val('$field[CodTipoTrabajador]');
		parent.$('#SitTraA').prop('checked', $SitTraA);
		parent.$('#SitTraI').prop('checked', $SitTraI);
		parent.$.prettyPhoto.close();
		";
	}
}

//	asignacion de conceptos x empleado
elseif ($accion == "empleados_conceptos") {
	mysql_query("BEGIN");
	//	-----------------
	##	procesos
	$Procesos = "";
	$sql = "SELECT CodTipoProceso FROM pr_conceptoproceso WHERE CodConcepto = '".$CodConcepto."'";
	$query_procesos = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
    while ($field_procesos = mysql_fetch_array($query_procesos)) {
		if ($Procesos == "") $CodTipoProceso = $field_procesos['CodTipoProceso'];
		$Procesos .= "$field_procesos[CodTipoProceso] ";
	}
	if ($Procesos == "") { $Procesos = "[TODOS]"; $FlagTipoProceso = "N"; } else $FlagTipoProceso = "S";
	##
	echo "
	parent.$('#CodConcepto').val('$CodConcepto');
	parent.$('#NomConcepto').val('$NomConcepto');
	parent.$('#Procesos').val('$Procesos');
	parent.$('.Procesos').val('$Procesos');
	if ('$FlagTipoProceso' == 'S') {
		parent.$('#FlagTipoProceso').prop('checked', true);
		parent.$('#btProcesos').css('visibility', 'visible');
	}
	else {
		parent.$('#FlagTipoProceso').prop('checked', false);
		parent.$('#btProcesos').css('visibility', 'hidden');
	}
	parent.$.prettyPhoto.close();
	";
	//	-----------------
	mysql_query("COMMIT");
}

elseif ($accion == "getNivelSalarial") {
	$sql = "SELECT NivelSalarial FROM rh_puestos WHERE CategoriaCargo = '".$CategoriaCargo."' AND Grado = '".$Grado."'";
	$NivelSalarial = getVar3($sql);
	echo $NivelSalarial;
}

elseif ($accion == "getSueldoPromedio") {
	if (isset($CodCargo)) {
		$sql = "SELECT * FROM rh_puestos WHERE CodCargo = '$CodCargo'";
		$field = getRecord($sql);
		$CategoriaCargo = $field['CategoriaCargo'];
		$Grado = $field['Grado'];
	}
	$sql = "SELECT SueldoPromedio FROM rh_nivelsalarial WHERE CategoriaCargo = '".$CategoriaCargo."' AND Grado = '".$Grado."' AND Paso = '".$Paso."'";
	$SueldoPromedio = getVar3($sql);
	echo floatval($SueldoPromedio);
}
?>