<?php
##  información general
$sql = "SELECT
            ob.*,
            p.NomCompleto AS NomProveedor
        FROM
            pr_obligacionesbono ob
            INNER JOIN mastpersonas p ON (p.CodPersona = ob.CodProveedor)
        WHERE ob.CodObligacionBono = '$CodObligacionBono[0]'";
$field = getRecord($sql);
//	------------------------------------
$_width = 700;
?>
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=pr_interfase_cuentas_por_pagar_verificar" method="post" autocomplete="off" onsubmit="return verificar();">
<input type="hidden" name="CodObligacionBono" id="CodObligacionBono" value="<?=$CodObligacionBono[0]?>" />
<input type="hidden" name="CodOrganismo" id="CodOrganismo" value="<?=$field['CodOrganismo']?>" />

<div class="divBorder" style="width:100%; min-width:<?=$_width?>px; margin:auto;">
    <table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
        <tr>
            <td align="right" width="150">Proveedor:</td>
            <td>
                <input type="text" value="<?=$field['CodProveedor']?>" style="width:50px;" disabled>
                <input type="text" value="<?=$field['NomProveedor']?>" style="width:250px;" disabled>
            </td>
            <td align="right" width="150">Fecha:</td>
            <td><input type="text" value="<?=formatFechaDMA($field['FechaRegistro'])?>" style="width:65px;" disabled></td>
        </tr>
        <tr>
            <td align="right">Documento:</td>
            <td>
                <input type="text" value="<?=$field['CodTipoDocumento']?>" style="width:25px;" disabled>
                <input type="text" value="<?=$field['NroControl']?>" style="width:150px;" disabled>
            </td>
            <td align="right">Monto Obligaci&oacute;n:</td>
            <td><input type="text" value="<?=number_format($field['MontoObligacion'],2,',','.')?>" style="width:150px; text-align:right; font-weight:bold;" disabled></td>
        </tr>
    </table>
</div>
<center>
    <input type="submit" value="Verificar">
    <input type="button" value="Cancelar" onclick="parent.$.prettyPhoto.close();">
</center>
<div class="sep"></div>

<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; margin:auto;">
    <table class="tblLista" style="width:100%; min-width:1150px;">
        <thead>
            <tr>
                <th width="50">Cat. Prog.</th>
                <th width="25">F.F.</th>
                <th width="75">Partida</th>
                <th align="left">Denominaci&oacute;n</th>
                <th width="100">Monto</th>
                <th width="100">Disponible</th>
                <th width="100">Diferencia</th>
            </tr>
        </thead>
        
        <tbody id="lista_bancos">
        <?php
        ##  cuentas
        $sql = "SELECT
                    obc.CodOrganismo,
                    obc.CodPresupuesto,
                    obc.CodFuente,
                    obc.cod_partida,
                    SUM(obc.Monto) AS Monto,
                    cp.CategoriaProg,
                    CONCAT(ss.CodSector, pr.CodPrograma, a.CodActividad) AS CatProg,
                    p.denominacion,
                    pv.Ejercicio
                FROM
                    pr_obligacionesbonocuenta obc
                    INNER JOIN pv_partida p ON (p.cod_partida = obc.cod_partida)
                    LEFT JOIN pv_presupuesto pv ON (pv.CodOrganismo = obc.CodOrganismo AND pv.CodPresupuesto = obc.CodPresupuesto)
                    LEFT JOIN pv_categoriaprog cp ON (cp.CategoriaProg = pv.CategoriaProg)
                    LEFT JOIN pv_actividades a ON (a.IdActividad = cp.IdActividad)
                    LEFT JOIN pv_proyectos py ON (py.IdProyecto = a.IdProyecto)
                    LEFT JOIN pv_subprogramas spr ON (spr.IdSubPrograma = py.IdSubPrograma)
                    LEFT JOIN pv_programas pr ON (pr.IdPrograma = spr.IdPrograma)
                    LEFT JOIN pv_subsector ss ON (ss.IdSubSector = pr.IdSubSector)
                WHERE obc.CodObligacionBono = '$CodObligacionBono[0]'
                GROUP BY CodOrganismo, CodPresupuesto, CodFuente, cod_partida
                ORDER BY CodOrganismo, CodPresupuesto, CodFuente, cod_partida";
        $field_cuentas = getRecords($sql);
        foreach ($field_cuentas as $fc) {
            list($MontoAjustado, $MontoCompromiso, $PreCompromiso, $CotizacionesAsignadas) = disponibilidadPartida2($fc['Ejercicio'], $fc['CodOrganismo'], $fc['cod_partida'], $fc['CodPresupuesto'], $fc['CodFuente']);
            $MontoAjustado = round(floatval($MontoAjustado), 2);
            $MontoCompromiso = round(floatval($MontoCompromiso), 2);
            $Disponible = $MontoAjustado - $MontoCompromiso;
            $Diferencia = round(floatval($Disponible), 2) - $fc['Monto'];
            ?>
            <tr class="trListaBody">
                <td align="center">
                    <input type="hidden" name="Ejercicio[]" value="<?=$fc['Ejercicio']?>">
                    <input type="hidden" name="CodPresupuesto[]" value="<?=$fc['CodPresupuesto']?>">
                    <input type="hidden" name="CodFuente[]" value="<?=$fc['CodFuente']?>">
                    <input type="hidden" name="cod_partida[]" value="<?=$fc['cod_partida']?>">
                    <input type="hidden" name="Monto[]" value="<?=$fc['Monto']?>">
                    <?=$fc['CatProg']?>
                </td>
                <td align="center"><?=$fc['CodFuente']?></td>
                <td align="center"><?=$fc['cod_partida']?></td>
                <td><?=htmlentities($fc['denominacion'])?></td>
                <td align="right"><?=number_format($fc['Monto'], 2, ',', '.')?></td>
                <td align="right"><?=number_format($Disponible, 2, ',', '.')?></td>
                <td align="right"><?=number_format($Diferencia, 2, ',', '.')?></td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
</div>
</form>

<script type="text/javascript" language="javascript">
    function verificar() {
        //  ajax
        $.ajax({
            type: "POST",
            url: "pr_interfase_cuentas_por_pagar_bono_ajax.php",
            data: "modulo=formulario&accion=verificar&"+$('form').serialize(),
            async: false,
            success: function(resp) {
                if (resp.trim() != "") cajaModal(resp, "error", 450);
                else {
                    var funct = "parent.$.prettyPhoto.close(); parent.document.getElementById('frmentrada').submit();";
                    cajaModal("Se verificaron las partidas exitosamente", "exito", 400, funct);
                }
            }
        });
        return false;
    }
</script>