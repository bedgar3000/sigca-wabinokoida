<?php
include("../../lib/fphp.php");
include("fphp.php");
//	--------------------------

//	--------------------------
if ($accion == "fideicomiso_calculo_empleado_sel") {
	list($Anio, $Mes, $Dia) = split("[/.-]", substr($Ahora, 0, 10));
	//	consulto los datos del empleado
	$sql = "SELECT
				mp.CodPersona,
				mp.NomCompleto,
				mp.Ndocumento,
				me.CodEmpleado,
				me.Fingreso
			FROM
				mastpersonas mp
				INNER JOIN mastempleado me ON (mp.CodPersona = me.CodPersona)
			WHERE mp.CodPersona = '".$CodPersona."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	list($Anios, $Meses, $Dias) = getTiempo(formatFechaDMA($field['Fingreso']), "$Dia-$Mes-$Anio");
	echo "$field[CodEmpleado]|$field[CodPersona]|$field[NomCompleto]|$field[Ndocumento]|$Anios|$Meses|$Dias|".formatFechaDMA($field['Fingreso']);
}

/*elseif ($accion == "fideicomiso_procesar_calculo_mostrar") {
	//	consulto los datos del calculo de antiguedad
	?>
    <table width="2000" class="tblLista">
        <thead>
        <tr>
            <th width="15" scope="col">Pr.</th>
            <th width="60" scope="col">PERIODO</th>
            <th width="80" scope="col">SUELDO MENSUAL</th>
            <?php
			//	esto aplica para delta amacuro solamente
			if ($Periodo == 2011) {
				$filtro_concepto1 = " OR CodConcepto = '0064'";
			}
			//	FIN----------------------------------------
			
            $filtro_remuneraciones = "";
            //	consulto 
            $sql = "SELECT CodConcepto, Descripcion, Abreviatura
                    FROM pr_concepto
                    WHERE FlagBonoRemuneracion = 'S' $filtro_concepto1
                    ORDER BY CodConcepto";
            $query_conceptos = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
            while($field_conceptos = mysql_fetch_array($query_conceptos)) {
                $filtro_remuneraciones .= ", (SELECT tnec1.Monto
                                              FROM pr_tiponominaempleadoconcepto tnec1
                                              WHERE
                                                    tnec1.Periodo = afd.Periodo AND
                                                    tnec1.CodPersona = afd.CodPersona AND
                                                    tnec1.CodConcepto = '".$field_conceptos['CodConcepto']."') AS _".$field_conceptos['CodConcepto'];
                ?><th width="80" scope="col" title="<?=htmlentities($field_conceptos['Descripcion'])?>"><?=$field_conceptos['Abreviatura']?></th><?php
            }
            ?>
            <th width="60" scope="col">ALI. B. VAC.</th>
            <th width="60" scope="col">ALI. B. FIN AÑO</th>
            <th width="80" scope="col">REMUN. DIARIA</th>
            <th width="80" scope="col">SUELDO + ALICUOTAS</th>
            <th width="35" scope="col">DIAS</th>
            <th width="80" scope="col">PREST. ANTIG. MENSUAL</th>
            <th width="80" scope="col">PREST. COMPL. (2 DIAS)</th>
            <th width="80" scope="col">PREST. ACUMULADA</th>
            <th width="50" scope="col">TASA DE INTERES (%)</th>
            <th width="50" scope="col">DIAS DEL MES</th>
            <th width="80" scope="col">INTERES MENSUAL</th>
            <th width="80" scope="col">INTERES ACUMULADO</th>
            <th width="80" scope="col">ANTICIPO PRESTACION</th>
            <th>&nbsp;</th>
        </tr>
        </thead>
    
        <tbody>
        <?php
		$DiasAnio = getDiasAnio($Periodo);
		//	consulto datos adicionales del empleado
		$sql = "SELECT
					CodTipoNom,
					CodOrganismo,
					Fegreso,
					FechaFinNomina,
					SUBSTRING(FechaFinNomina, 1, 7) AS PeriodoFinNomina,
					SUBSTRING(FechaFinNomina, 9, 2) AS DiaFinNomina,
					SUBSTRING(Fegreso, 1, 7) AS PeriodoEgreso,
					Estado
				FROM mastempleado
				WHERE CodPersona = '".$CodPersona."'";
		$query_empleado = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
        if (mysql_num_rows($query_empleado) != 0) $field_empleado = mysql_fetch_array($query_empleado);
		list($_EgresoAnio, $_EgresoMes, $_EgresoDia) = split("[./-]", $field_empleado['Fegreso']);
		
		//	saldo inicial
		$sql = "SELECT
					AcumuladoInicialProv,
					AcumuladoInicialFide
				FROM pr_acumuladofideicomiso
				WHERE CodPersona = '".$CodPersona."'";
		$query_inicial = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
        if (mysql_num_rows($query_inicial) != 0) $field_inicial = mysql_fetch_array($query_inicial);
		
		//	saldo inicial
		$sql = "SELECT SUM(TransaccionFide) AS TransaccionFide
				FROM pr_acumuladofideicomisodetalle
				WHERE
					CodPersona = '".$CodPersona."' AND
					SUBSTRING(Periodo, 1, 4) < '".$Periodo."'";
		$query_inicial_fide = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
        if (mysql_num_rows($query_inicial_fide) != 0) $field_inicial_fide = mysql_fetch_array($query_inicial_fide);
		
		//	consulto periodos
        $sql = "SELECT
					afd.CodPersona,
                    afd.Periodo,
					afd.Dias,
					afd.Transaccion,
					afd.DiasAdicional,
					afd.Complemento,
					afd.AnteriorFide,
					afd.TransaccionFide,
					afd.FlagFraccionado,
					(SELECT (COALESCE(SUM(Transaccion),0) + COALESCE(SUM(Complemento),0)) + ".floatval($field_inicial['AcumuladoInicialProv'])."
					 FROM pr_acumuladofideicomisodetalle
					 WHERE
						CodPersona = afd.CodPersona AND
						Periodo < afd.Periodo) AS PrestAcumulada,
                    fc.Periodo AS PeriodoProcesado
                    $filtro_remuneraciones
                FROM
                    pr_acumuladofideicomisodetalle afd
                    LEFT JOIN pr_acumuladofideicomiso af ON (afd.CodPersona = af.CodPersona)
                    LEFT JOIN pr_fideicomisocalculo fc ON (fc.CodPersona = afd.CodPersona AND
                                                           fc.Periodo = afd.Periodo)
                WHERE
                    SUBSTRING(afd.Periodo, 1, 4) =  '".$Periodo."' AND
                    afd.CodPersona = '".$CodPersona."'
                ORDER BY Periodo";
        $query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
        $rows = mysql_num_rows($query);
        //	MUESTRO LA TABLA
        while ($field = mysql_fetch_array($query)) {
			list($a, $m) = split("[./-]", $field['Periodo']);
			$_AnioAnterior = $a;
			$_MesAnterior = intval($m);
			--$_MesAnterior;
			if ($_MesAnterior < 1) {
				$_MesAnterior = 12;
				--$_AnioAnterior;
			}
			if ($_MesAnterior < 10) $_MesAnterior = "0$_MesAnterior";
			$_PeriodoAnterior = "$_AnioAnterior-$_MesAnterior";
			
            if ($field['PeriodoProcesado'] != "") $Procesado = "S"; else $Procesado = "N";
			
			if ($field_empleado['PeriodoFinNomina'] == $field['Periodo']) $SueldoMensual = getVar2("rh_sueldos", "SueldoNormal", array("Periodo","CodPersona"), array($_PeriodoAnterior,$field['CodPersona']));
			else $SueldoMensual = getVar2("rh_sueldos", "SueldoNormal", array("Periodo","CodPersona"), array($field['Periodo'],$field['CodPersona']));
			if ($field['Dias'] > 0) $bold = "background:#CFCFCF;"; else $bold = "";
            ?>
            <tr class="trListaBody" style=" <?=$bold?>">
                <td align="center"><?=printFlag2($Procesado)?></td>
                <td align="center"><strong><?=$field['Periodo']?></strong></td>
                <td align="right"><?=number_format($SueldoMensual, 2, ',', '.')?></td>
                <?php
                $Bonificaciones = 0;
                $sql = "SELECT CodConcepto FROM pr_concepto WHERE FlagBonoRemuneracion = 'S' $filtro_concepto1 ORDER BY CodConcepto";
                $query_conceptos = mysql_query($sql) or die($sql.mysql_error());
                while($field_conceptos = mysql_fetch_array($query_conceptos)) {
                    $id = "_".$field_conceptos['CodConcepto'];
                    $Bonificaciones += $field[$id];
                    ?><td align="right"><?=number_format($field[$id], 2, ',', '.')?></td><?php
                }
				$SueldoDiario = round(($SueldoMensual / 30), 2);
				$AliVac = getVar2("rh_sueldos", "AliVac", array("Periodo","CodPersona"), array($field['Periodo'],$field['CodPersona']));
				$AliFin = getVar2("rh_sueldos", "AliFin", array("Periodo","CodPersona"), array($field['Periodo'],$field['CodPersona']));
				$RemuneracionDiaria = round((($SueldoMensual + $Bonificaciones) / 30), 2);
				$SueldoDiarioAli = $RemuneracionDiaria + $AliVac + $AliFin;
				$Complemento += $field['Complemento'];
				$Tasa = tasaInteres($field['Periodo']);
				if ($field_empleado['Estado'] == 'I' && $field_empleado['PeriodoFinNomina'] == $field['Periodo']) $DiasMes = intval($field_empleado['DiaFinNomina']);
				else $DiasMes = getDiasMes($field['Periodo']);
				$InteresMensual = round(($field['PrestAcumulada'] * $Tasa / 100 * $DiasMes / $DiasAnio), 2);
				$InteresMensualSum += $InteresMensual;
				$InteresAcumulado = $field_inicial['AcumuladoInicialFide'] + $field_inicial_fide['TransaccionFide'] + $InteresMensualSum;
                ?>
                <td align="right"><?=number_format($AliVac, 2, ',', '.')?></td>
                <td align="right"><?=number_format($AliFin, 2, ',', '.')?></td>
                <td align="right"><?=number_format($RemuneracionDiaria, 2, ',', '.')?></td>
                <td align="right"><?=number_format($SueldoDiarioAli, 2, ',', '.')?></td>
                <td align="right"><?=number_format($field['Dias'], 2, ',', '.')?></td>
                <td align="right"><?=number_format($field['Transaccion'], 2, ',', '.')?></td>
                <td align="right"><?=number_format($field['Complemento'], 2, ',', '.')?></td>
                <td align="right"><?=number_format($field['PrestAcumulada'], 2, ',', '.')?></td>
                <td align="right"><?=number_format($Tasa, 2, ',', '.')?></td>
                <td align="center"><?=$DiasMes?></td>
                <td align="right"><?=number_format($InteresMensual, 2, ',', '.')?></td>
                <td align="right"><?=number_format($InteresAcumulado, 2, ',', '.')?></td>
                <td>&nbsp;</td>
                <td>
                    <input type="hidden" name="Periodo" value="<?=$field['Periodo']?>" />
                    <input type="hidden" name="SueldoMensual" value="<?=$SueldoMensual?>" />
                    <input type="hidden" name="Bonificaciones" value="<?=$Bonificaciones?>" />
                    <input type="hidden" name="AliVac" value="<?=$AliVac?>" />
                    <input type="hidden" name="AliFin" value="<?=$AliFin?>" />
                    <input type="hidden" name="SueldoDiario" value="<?=$SueldoDiario?>" />
                    <input type="hidden" name="SueldoDiarioAli" value="<?=$SueldoDiarioAli?>" />
                    <input type="hidden" name="Dias" value="<?=$field['Dias']?>" />
                    <input type="hidden" name="PrestAntiguedad" value="<?=$field['Transaccion']?>" />
                    <input type="hidden" name="DiasComplemento" value="<?=$field['DiasAdicional']?>" />
                    <input type="hidden" name="PrestComplemento" value="<?=$field['Complemento']?>" />
                    <input type="hidden" name="PrestAcumulada" value="<?=$field['PrestAcumulada']?>" />
                    <input type="hidden" name="Tasa" value="<?=$Tasa?>" />
                    <input type="hidden" name="DiasMes" value="<?=$DiasMes?>" />
                    <input type="hidden" name="InteresMensual" value="<?=$InteresMensual?>" />
                    <input type="hidden" name="InteresAcumulado" value="<?=$InteresAcumulado?>" />
                    <input type="hidden" name="Anticipo" />
                </td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
	<?php
}*/

elseif($accion == "procesos_control_modificar") {
	list($CodOrganismo, $CodTipoNom, $Periodo, $CodTipoProceso) = split("[_]", $codigo);
	$sql = "SELECT
				Estado,
				FlagPagado
			FROM pr_procesoperiodo
			WHERE
				CodOrganismo = '".$CodOrganismo."' AND
				CodTipoNom = '".$CodTipoNom."' AND
				Periodo = '".$Periodo."' AND
				CodTipoProceso = '".$CodTipoProceso."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	if ($field['Estado'] != "A" || $field['FlagPagado'] == "S") die("Solo se pueden modificar los Periodos <strong>Activos</strong> y <strong>Abiertos</strong>");
}

elseif($accion == "ajuste_salarial_modificar") {
	list($CodOrganismo, $Periodo, $Secuencia) = split("[_]", $codigo);
	$Estado = getVar2("pr_ajustesalarial", "Estado", array("CodOrganismo","Periodo","Secuencia"), array($CodOrganismo,$Periodo,$Secuencia));
	if ($Estado != "PE") die("No puede Modificar este Ajuste");
}

elseif($accion == "ajuste_salarial_aprobar") {
	list($CodOrganismo, $Periodo, $Secuencia) = split("[_]", $codigo);
	$Estado = getVar2("pr_ajustesalarial", "Estado", array("CodOrganismo","Periodo","Secuencia"), array($CodOrganismo,$Periodo,$Secuencia));
	if ($Estado != "PE") die("No puede Aprobar este Ajuste");
}

elseif($accion == "ajuste_salarial_anular") {
	list($CodOrganismo, $Periodo, $Secuencia) = split("[_]", $codigo);
	$Estado = getVar2("pr_ajustesalarial", "Estado", array("CodOrganismo","Periodo","Secuencia"), array($CodOrganismo,$Periodo,$Secuencia));
	if ($Estado != "PE") die("No puede Anular este Ajuste");
}

elseif($accion == "ajuste_salarial_emp_modificar") {
	list($CodOrganismo, $Periodo, $Secuencia) = split("[_]", $codigo);
	$Estado = getVar2("pr_ajustesalarialemp", "Estado", array("CodOrganismo","Periodo","Secuencia"), array($CodOrganismo,$Periodo,$Secuencia));
	if ($Estado != "PE") die("No puede Modificar este Ajuste");
}

elseif($accion == "ajuste_salarial_emp_aprobar") {
	list($CodOrganismo, $Periodo, $Secuencia) = split("[_]", $codigo);
	$Estado = getVar2("pr_ajustesalarialemp", "Estado", array("CodOrganismo","Periodo","Secuencia"), array($CodOrganismo,$Periodo,$Secuencia));
	if ($Estado != "PE") die("No puede Aprobar este Ajuste");
}

elseif($accion == "ajuste_salarial_emp_anular") {
	list($CodOrganismo, $Periodo, $Secuencia) = split("[_]", $codigo);
	$Estado = getVar2("pr_ajustesalarialemp", "Estado", array("CodOrganismo","Periodo","Secuencia"), array($CodOrganismo,$Periodo,$Secuencia));
	if ($Estado != "PE") die("No puede Anular este Ajuste");
}

elseif($accion == "setFlagRetroactivo") {
	$sql = "SELECT FlagRetroactivo FROM pr_tipoproceso WHERE CodTipoProceso = '".$CodTipoProceso."'";
	$FlagRetroactivo = getVar3($sql);
	echo $FlagRetroactivo;
}
?>