<?php
if ($filtrar == "default") {
    $Periodo = $AnioActual;
}
//  ------------------------------------
$_titulo = "Calculo de Fideicomiso";
$_width = 768;
?>
<table width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td class="titulo"><?=$_titulo?></td>
        <td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
    </tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=pr_fideicomiso_procesar_calculo" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />

<!--FILTRO-->
<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
    <tr>
        <td align="right" width="125">Periodo:</td>
        <td><input type="text" name="Periodo" id="Periodo" style="width:35px; font-weight:bold; font-size:12px;" value="<?=$Periodo?>" /></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td align="right">Empleado:</td>
        <td class="gallery clearfix">
            <input type="hidden" name="CodPersona" id="CodPersona" value="<?=$CodPersona?>" />
            <input type="text" name="NomCompleto" id="NomCompleto" value="<?=$NomCompleto?>" style="width:250px;" readonly />
            <a href="../lib/listas/gehen.php?anz=lista_empleados&filtrar=default&ventana=fideicomiso_calculo_empleado_sel&iframe=true&width=950&height=430" rel="prettyPhoto[iframe1]">
                <img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
        <td align="right">Documento:</td>
        <td><input type="text" name="Ndocumento" id="Ndocumento" value="<?=$Ndocumento?>" style="width:100px;" readonly /></td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td align="right">Antiguedad:</td>
        <td>
            <input type="text" name="Anios" id="Anios" value="<?=$Anios?>" style="width:25px; text-align:right;" readonly /><i>Anios</i> &nbsp; &nbsp;
            <input type="text" name="Meses" id="Meses" value="<?=$Meses?>" style="width:25px; text-align:right;" readonly /><i>Meses</i> &nbsp; &nbsp;
            <input type="text" name="Dias" id="Dias" value="<?=$Dias?>" style="width:25px; text-align:right;" readonly /><i>Dias</i> &nbsp; &nbsp;
        </td>
        <td align="right">Fecha de Ingreso:</td>
        <td><input type="text" name="Fingreso" id="Fingreso" value="<?=$Fingreso?>" style="width:100px;" readonly /></td>
        <td align="right"><input type="submit" value="Buscar"></td>
    </tr>
</table>
</div>
<div class="sep"></div>

<!-- Highlight / Error-->
<center>
<div class="ui-widget" id="error" style="display:none;">
    <div class="ui-state-error ui-corner-all" style="width:100%; text-align:left;">
        <p>
            <span class="ui-icon ui-icon-alert" style="float: left;"></span>
            <strong>Se encontrar&oacute;n cambios en el calculo.</strong> Haz click en <strong>Procesar Calculo</strong> para guardar la nueva informaci&oacute;n.
        </p>
    </div>
</div>
</center>
<div class="sep"></div>

<!--REGISTROS-->
<center>
<input type="hidden" name="sel_registros" id="sel_registros" />
<table class="tblBotones" style="width:100%; min-width:<?=$_width?>px;">
    <tr>
        <td><div id="rows"></div></td>
        <td align="right" class="gallery clearfix">
            <input type="button" id="btProcesar" value="Procesar Calculo" style="width:100px;" onClick="procesar(this.form);" />
        </td>
    </tr>
</table>

<div style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:425px;">
<table class="tblLista" style="width:100%; min-width:1500px;">
    <thead>
        <tr>
            <th width="50">PERIODO</th>
            <th width="80">SUELDO MENSUAL</th>
            <th width="80">BONOS</th>
            <th width="60">ALI. B. VAC.</th>
            <th width="60">ALI. B. FIN AÑO</th>
            <th width="80">REMUN. DIARIA</th>
            <th width="80">SUELDO + ALICUOTAS</th>
            <th width="50">DIAS</th>
            <th width="100">PREST. ANTIG. MENSUAL</th>
            <th width="100">PREST. COMPL. (2 DIAS)</th>
            <th width="100">PREST. ACUMULADA</th>
            <th width="50">TASA DE INTERES (%)</th>
            <th width="50">DIAS DEL MES</th>
            <th width="100">INTERES MENSUAL</th>
            <th width="100">INTERES ACUMULADO</th>
            <th width="100">ANTICIPO PRESTACION</th>
        </tr>
    </thead>
    
    <tbody id="lista_registros">
        <?php
        ##  empleado
        $sql = "SELECT
                    e.CodPersona,
                    e.Estado,
                    e.Fingreso,
                    e.CodTipoNom,
                    e.CodOrganismo,
                    SUBSTRING(e.Fingreso,1,4) AS AnioIngreso,
                    SUBSTRING(e.Fingreso,1,7) AS PeriodoIngreso,
                    SUBSTRING(e.Fingreso,6,2) AS MesIngreso,
                    SUBSTRING(e.Fingreso,9,2) AS DiaIngreso,
                    e.Fegreso,
                    SUBSTRING(e.Fegreso,1,4) AS AnioEgreso,
                    SUBSTRING(e.Fegreso,1,7) AS PeriodoEgreso,
                    SUBSTRING(e.Fegreso,6,2) AS MesEgreso,
                    SUBSTRING(e.Fegreso,9,2) AS DiaEgreso,
                    e.FechaFinNomina,
                    SUBSTRING(e.FechaFinNomina,1,4) AS AnioFin,
                    SUBSTRING(e.FechaFinNomina,1,7) AS PeriodoFin,
                    SUBSTRING(e.FechaFinNomina,6,2) AS MesFin,
                    SUBSTRING(e.FechaFinNomina,9,2) AS DiaFin,
                    pt1.Grado,
                    pt2.Grado AS GradoTemp
                FROM
                    mastempleado e
                    LEFT JOIN rh_puestos pt1 ON (pt1.CodCargo = e.CodCargo)
                    LEFT JOIN rh_puestos pt2 ON (pt2.CodCargo = e.CodCargoTemp)
                WHERE e.CodPersona = '".$CodPersona."'";
        $field_empleado = getRecord($sql);
        list($AntAnios, $AntMeses, $AntDias) = getTiempo(formatFechaDMA($field_empleado['Fingreso']), formatFechaDMA($FechaActual));
        $AnioComplementoInicial = $field_empleado['AnioIngreso'] + 2;
        $AcumuladoSueldoAlicuotas = 0;
        $MesAntiguedad = 0;
        $AnioAntiguedad = 0;
        ##  acumulado
        $sql = "SELECT * FROM pr_acumuladofideicomiso WHERE CodPersona = '".$CodPersona."'";
        $field_acumulado = getRecord($sql);
        $AcumuladoDias = floatval($field_acumulado['AcumuladoInicialDias']);
        $AcumuladoAntiguedadInicial = floatval($field_acumulado['AcumuladoInicialProv']);
        $AcumuladoAntiguedad = floatval($field_acumulado['AcumuladoInicialProv']);
        $AcumuladoAntiguedadAnterior = floatval($field_acumulado['AcumuladoInicialProv']);
        $AcumuladoDiasAdicional = floatval($field_acumulado['AcumuladoDiasAdicionalInicial']);
        $AcumuladoInteresInicial = floatval($field_acumulado['AcumuladoInicialFide']);
        $AcumuladoInteres = floatval($field_acumulado['AcumuladoInicialFide']);
        $SumaAntiguedad = 0;
        $SumaInteres = 0;
        ##  
        $Anio = "";
        $Error = false;
        //  consulto lista
        $sql = "SELECT
                    s.Periodo,
                    s.SueldoNormal,
                    SUBSTRING(s.Periodo,1,4) AS Anio,
                    SUBSTRING(s.Periodo,6,2) AS Mes,
                    afd.Dias,
                    afd.Complemento,
                    ti.Porcentaje AS Tasa
                FROM
                    rh_sueldos s
                    LEFT JOIN pr_acumuladofideicomisodetalle afd ON (s.CodPersona = afd.CodPersona AND
                                                                     s.Periodo = afd.Periodo)
                    LEFT JOIN masttasainteres ti ON (ti.Periodo = s.Periodo)
                WHERE
                    s.CodPersona = '".$CodPersona."' AND
                    s.Periodo >= '2011-01'
                ORDER BY Periodo";
        $field = getRecords($sql);
        foreach($field as $f) {
            $DiasPeriodo = getDiasMes($f['Periodo']);
            if ($field_empleado['Estado'] == 'I' && $f['Periodo'] == $field_empleado['PeriodoFin']) 
                $DiasTrabajados = intval($field_empleado['DiaFin']); 
            elseif ($f['Periodo'] == $field_empleado['PeriodoIngreso'])
                $DiasTrabajados = 30 - $field_empleado['DiaIngreso'] + 1;
            else 
                $DiasTrabajados = $DiasPeriodo;
            if ($DiasTrabajados == $DiasPeriodo) $DiasParaDiario = 30; else $DiasParaDiario = $DiasTrabajados;
            ##  acumulado
            if ($Anio != $f['Anio']) {
                $Anio = $f['Anio'];
                if ($f['Anio'] >= $AnioComplementoInicial) $AcumuladoDiasAdicional += 2;
                if ($f['Anio'] == $Periodo || !$Periodo) {
                    ?>
                    <tr class="trListaBody2">
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td align="right"><?=number_format($AcumuladoDias,2,',','.')?></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td align="right"><?=number_format($AcumuladoAntiguedad,2,',','.')?></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td align="right"><?=number_format($AcumuladoInteres,2,',','.')?></td>
                        <td>&nbsp;</td>
                    </tr>
                    <?php
                }
            }
            $SueldoDiario = round(($f['SueldoNormal'] / $DiasParaDiario), 2);
            ##  Bonos
            unset($field_bonos);
            if ($f['Periodo'] <= '2011-12') $filtro_bonos = " OR c.CodConcepto = '0064'"; else $filtro_bonos = "";
            $sql = "SELECT SUM(tnec.Monto) AS Monto
                    FROM
                        pr_tiponominaempleadoconcepto tnec
                        INNER JOIN pr_concepto c ON (tnec.CodConcepto = c.CodConcepto AND
                                                     ((c.Tipo = 'I' AND c.FlagBonoRemuneracion = 'S') $filtro_bonos))
                    WHERE
                        tnec.CodPersona = '".$field_empleado['CodPersona']."' AND
                        tnec.Periodo = '".$f['Periodo']."'
                    GROUP BY tnec.Periodo";
            $Bonos = floatval(getVar3($sql));
            ##  dias alicuota vacacional
            /*$sql = "SELECT Cantidad
                    FROM pr_tiponominaempleadoconcepto
                    WHERE
                        CodTipoNom = '".$field_empleado['CodTipoNom']."' AND
                        Periodo = '".$f['Periodo']."' AND
                        CodPersona = '".$field_empleado['CodPersona']."' AND
                        CodOrganismo = '".$field_empleado['CodOrganismo']."'AND
                        CodConcepto = '".$_PARAMETRO['ALIVAC']."'
                    LIMIT 0, 1";
            $DiasAliVac = getVar3($sql);
            ##  dias alicuota vacacional
            $sql = "SELECT Cantidad
                    FROM pr_tiponominaempleadoconcepto
                    WHERE
                        CodTipoNom = '".$field_empleado['CodTipoNom']."' AND
                        Periodo = '".$f['Periodo']."' AND
                        CodPersona = '".$field_empleado['CodPersona']."' AND
                        CodOrganismo = '".$field_empleado['CodOrganismo']."' AND
                        CodConcepto = '".$_PARAMETRO['ALIFIN']."'
                    LIMIT 0, 1";
            $DiasAliFin = getVar3($sql);*/
            ##  
            if ($field_empleado['CodPersona'] == '000043' && $f['Periodo'] >= '2012-06') {
                $DiasAliVac = 40;
                $DiasAliFin = 90;
            } else {
                $DiasAliVac = 105;
                $DiasAliFin = 150;
            }
            ##  Alicuota Vacacional
            $AliVac = round(($SueldoDiario * $DiasAliVac / 360), 2);
            ##  Alicuota Fin de Año
            $AliFin = round((($SueldoDiario + $AliVac) * $DiasAliFin / 360), 2);
            ##  Remuneracion Diaria
            $BonosDiario = round(($Bonos / 30), 2);
            if ($DiasParaDiario == 30) $RemuneracionDiaria = round((($f['SueldoNormal'] + $Bonos) / 30), 2); 
            else $RemuneracionDiaria = $SueldoDiario + $BonosDiario;
            ##  Sueldo + Alicuotas
            $SueldoAlicuotas = $AliVac + $AliFin + $RemuneracionDiaria;
            ##  Dias
            if ($f['Anio'] < '2014') $Dias = $f['Dias'];
            elseif ($f['Mes'] == '03' || $f['Mes'] == '06' || $f['Mes'] == '09' || $f['Mes'] == '12') {
                if ($AnioAntiguedad > 0) $Dias = 15;
                elseif ($MesAntiguedad >= 3) $Dias = 15;
                else {
                    $MesesTri = $MesAntiguedad + 1;
                    $Dias = $MesesTri * 5;
                }
            }
            elseif($field_empleado['PeriodoFin'] == $f['Periodo']) {
                if ($f['Mes'] == '01' || $f['Mes'] == '04' || $f['Mes'] == '07' || $f['Mes'] == '10') $Dias = 5;
                elseif ($f['Mes'] == '02' || $f['Mes'] == '05' || $f['Mes'] == '08' || $f['Mes'] == '11') $Dias = 10;
                else $Dias = 15;
            }
            else $Dias = 0;
            $AcumuladoDias += $Dias;
            ##  Antiguedad
            $Antiguedad = $SueldoAlicuotas * $Dias;
            $DiasAdicional = 0;
            if ($f['Anio'] == '2011') {
                if (($f['Mes'] == $field_empleado['MesIngreso'] || $f['Mes'] < $field_empleado['MesIngreso']) || ($f['Mes'] == '06' && $field_empleado['Fingreso'] < '1997-07-01')) {
                    $AcumuladoSueldoAlicuotas = 0;
                } else {
                    $AcumuladoSueldoAlicuotas += $SueldoAlicuotas;
                }
                ##  Complemento
                $Complemento = $f['Complemento']; 
            } else {
                ##  Complemento
                if ((($f['Mes'] == $field_empleado['MesIngreso'] && $field_empleado['Fingreso'] >= '1997-07-01') || ($f['Mes'] == '06' && $field_empleado['Fingreso'] < '1997-07-01')) && $AntAnios >= 2) {
                    $DiasAdicional = 2;
                    $AcumuladoSueldoAlicuotas += $SueldoAlicuotas;
                    if ($AcumuladoDiasAdicional > 30) $AcumuladoDiasAdicional = 30;
                    $Complemento = round(($AcumuladoSueldoAlicuotas / 12 * $AcumuladoDiasAdicional),2);
                    $AcumuladoSueldoAlicuotas = 0;
                } else {
                    $Complemento = 0;
                    $AcumuladoSueldoAlicuotas += $SueldoAlicuotas;
                }
            }
            $AcumuladoAntiguedad += ($Antiguedad + $Complemento);
            ##  Intereses
            if ($field_empleado['PeriodoFin'] == $f['Periodo']) $DiasMes = intval($field_empleado['DiaFin']);
            elseif ($f['Periodo'] == $field_empleado['PeriodoIngreso']) $DiasMes = $DiasTrabajados;
            else $DiasMes = getDiasMes($f['Periodo']);
            if (getDiasMes($f['Anio'].'-02') == '28') $DiasAnio = 365; else $DiasAnio = 366;
            $Interes = round(($AcumuladoAntiguedadAnterior * $f['Tasa'] / 100 * $DiasMes / $DiasAnio),2);
            $AcumuladoInteres += $Interes;
            ##  
            if ($f['Periodo'] <= '2012-05' && $Dias < 5 && $field_empleado['PeriodoFin'] == $f['Periodo']) $FlagFraccionado = 'S';
            elseif ($f['Periodo'] > '2012-05' && $Dias < 15 && $field_empleado['PeriodoFin'] == $f['Periodo']) $FlagFraccionado = 'S';
            else $FlagFraccionado = 'N';
            $Antiguedad = round($Antiguedad,2);
            $Interes = round($Interes,2);
            ##  
            if (!$Error) {
                $sql = "SELECT *
                        FROM pr_fideicomisocalculo
                        WHERE
                            CodPersona = '".$CodPersona."' AND
                            Periodo = '".$f['Periodo']."'";
                $field_calculo = getRecord($sql);
                if (count($field_calculo)) {
                    if ($field_calculo['PrestAntiguedad'] != $Antiguedad) $Error = true;
                    elseif ($field_calculo['InteresMensual'] != $Interes) $Error = true;
                } else $Error = true;
            }
            ##  
            if ($f['Anio'] == $Periodo || !$Periodo) {
                if ($Error) $style = "color:red;"; else $style = "";
                ?>
                <tr class="trListaBody" style=" <?=$style?>">
                    <th>
                        <input type="hidden" name="_Periodo[]" value="<?=$f['Periodo']?>" />
                        <input type="hidden" name="_SueldoMensual[]" value="<?=$f['SueldoNormal']?>" />
                        <input type="hidden" name="_Bonificaciones[]" value="<?=$Bonos?>" />
                        <input type="hidden" name="_AliVac[]" value="<?=$AliVac?>" />
                        <input type="hidden" name="_AliFin[]" value="<?=$AliFin?>" />
                        <input type="hidden" name="_SueldoDiario[]" value="<?=$RemuneracionDiaria?>" />
                        <input type="hidden" name="_SueldoDiarioAli[]" value="<?=$SueldoAlicuotas?>" />
                        <input type="hidden" name="_Dias[]" value="<?=$Dias?>" />
                        <input type="hidden" name="_PrestAntiguedad[]" value="<?=$Antiguedad?>" />
                        <input type="hidden" name="_PrestComplemento[]" value="<?=$Complemento?>" />
                        <input type="hidden" name="_PrestAcumulada[]" value="<?=$AcumuladoAntiguedad?>" />
                        <input type="hidden" name="_Tasa[]" value="<?=floatval($f['Tasa'])?>" />
                        <input type="hidden" name="_DiasMes[]" value="<?=$DiasMes?>" />
                        <input type="hidden" name="_InteresMensual[]" value="<?=$Interes?>" />
                        <input type="hidden" name="_InteresAcumulado[]" value="<?=$AcumuladoInteres?>" />
                        <input type="hidden" name="_DiasComplemento[]" value="<?=$DiasAdicional?>" />
                        <input type="hidden" name="_FlagFraccionado[]" value="<?=$FlagFraccionado?>" />
                        <input type="hidden" name="_DiasAliVac[]" value="<?=$DiasAliVac?>" />
                        <input type="hidden" name="_DiasAliFin[]" value="<?=$DiasAliFin?>" />
                        <?=$f['Periodo']?>
                    </th>
                    <td align="right"><?=number_format($f['SueldoNormal'],2,',','.')?></td>
                    <td align="right"><?=number_format($Bonos,2,',','.')?></td>
                    <td align="right"><?=number_format($AliVac,2,',','.')?></td>
                    <td align="right"><?=number_format($AliFin,2,',','.')?></td>
                    <td align="right"><?=number_format($RemuneracionDiaria,2,',','.')?></td>
                    <td align="right"><?=number_format($SueldoAlicuotas,2,',','.')?></td>
                    <td align="right"><?=number_format($Dias,2,',','.')?></td>
                    <td align="right"><?=number_format($Antiguedad,2,',','.')?></td>
                    <td align="right"><?=number_format($Complemento,2,',','.')?></td>
                    <td align="right"><?=number_format($AcumuladoAntiguedad,2,',','.')?></td>
                    <td align="right"><?=number_format($f['Tasa'],2,',','.')?></td>
                    <td align="right"><?=number_format($DiasMes,2,',','.')?></td>
                    <td align="right"><?=number_format($Interes,2,',','.')?></td>
                    <td align="right"><?=number_format($AcumuladoInteres,2,',','.')?></td>
                    <td>&nbsp;</td>
                </tr>
                <?php
            }
            ##  valores
            $AnteriorProv = $AcumuladoAntiguedadInicial + $SumaAntiguedad;
            $AnteriorFide = $AcumuladoInteresInicial + $SumaInteres;
            $SumaAntiguedad += $Antiguedad;
            $SumaInteres += $Interes;
            $AcumuladoAntiguedadAnterior = $AcumuladoAntiguedad;
            ++$MesAntiguedad;
            if ($MesAntiguedad % 12 == 0) ++$AnioAntiguedad;
        }
        ?>
    </tbody>
</table>
</div>
</center>
</form>

<script type="text/javascript" language="javascript">
    $(document).ready(function() {
        <?php
        if ($Error) {
            ?> $("#error").css("display", "block"); <?php
        }
        ?>
    });

    function procesar(form) {
        bloqueo(true);
        //  valido
        var error = "";
        //  valido errores
        if (error != "") {
            cajaModal(error, "error", 400);
        } else {
            //  ajax
            $.ajax({
                type: "POST",
                url: "pr_fideicomiso_procesar_calculo_ajax.php",
                data: "modulo=formulario&accion=procesar&"+$('#frmentrada').serialize(),
                async: false,
                success: function(resp) {
                    if (resp.trim() != '') cajaModal(resp.trim(), 'error', 400);
                    else form.submit();
                }
            });
        }
        return false;
    }
</script>