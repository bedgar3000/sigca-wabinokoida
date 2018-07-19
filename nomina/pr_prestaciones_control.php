<?php
//  ------------------------------------
if ($filtrar == "default") {
    $fCodOrganismo = $_SESSION["ORGANISMO_ACTUAL"];
    $fEstado = "PE";
    $fOrderBy = "CodOrganismo";
    $maxlimit = $_SESSION["MAXLIMIT"];
}
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (le.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fCodTipoNom != "") { $cCodTipoNom = "checked"; $filtro.=" AND (le.CodTipoNom = '".$fCodTipoNom."')"; } else $dCodTipoNom = "disabled";
if ($fCodDependencia != "") { $cCodDependencia = "checked"; $filtro.=" AND (le.CodDependencia = '".$fCodDependencia."')"; } else $dCodDependencia = "disabled";
if ($fFliquidacionD != "" || $fFliquidacionH != "") {
    $cFliquidacion = "checked";
    if ($fFliquidacionD != "") $filtro.=" AND (le.Fliquidacion >= '".formatFechaAMD($fFliquidacionD)."')";
    if ($fFliquidacionH != "") $filtro.=" AND (le.Fliquidacion <= '".formatFechaAMD($fFliquidacionH)."')";
} else $dFliquidacion = "disabled";
if ($fCodPersona != "") { $cCodPersona = "checked"; $filtro.=" AND (le.CodPersona = '".$fCodPersona."')"; } else $dCodPersona = "visibility:hidden;";
if ($fCodMotivoCes != "") { $cCodMotivoCes = "checked"; $filtro.=" AND (le.CodMotivoCes = '".$fCodMotivoCes."')"; } else $dCodMotivoCes = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (le.EstadoPago = '".$fEstado."')"; } else $dEstado = "disabled";
//  ------------------------------------
$_titulo = "Control de Prestaciones";
$_width = 900;
?>
<table width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td class="titulo"><?=$_titulo?></td>
        <td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
    </tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=pr_prestaciones_control" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />

<!--FILTRO-->
<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
    <tr>
        <td align="right" width="125">Organismo:</td>
        <td>
            <input type="checkbox" <?=$cCodOrganismo?> onclick="this.checked=!this.checked" />
            <select name="fCodOrganismo" id="fCodOrganismo" style="width:275px;" onChange="getOptionsSelect(this.value, 'dependencia_filtro', 'fCodDependencia', true);" <?=$dCodOrganismo?>>
                <?=getOrganismos($fCodOrganismo, 3)?>
            </select>
        </td>
        <td align="right" width="125">N&oacute;mina:</td>
        <td>
            <input type="checkbox" <?=$cCodTipoNom?> onclick="chkFiltro(this.checked, 'fCodTipoNom');" />
            <select name="fCodTipoNom" id="fCodTipoNom" style="width:143px;" <?=$dCodTipoNom?>>
                <option value="">&nbsp;</option>
                <?=loadSelect("tiponomina", "CodTipoNom", "Nomina", $fCodTipoNom, 0)?>
            </select>
        </td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td align="right">Dependencia:</td>
        <td>
            <input type="checkbox" <?=$cCodDependencia?> onclick="chkFiltro(this.checked, 'fCodDependencia');" />
            <select name="fCodDependencia" id="fCodDependencia" style="width:275px;" <?=$dCodDependencia?>>
                <option value="">&nbsp;</option>
                <?=getDependencias($fCodDependencia, $fCodOrganismo, 3)?>
            </select>
        </td>
        <td align="right">F. Liquidaci&oacute;n: </td>
        <td>
            <input type="checkbox" <?=$cFliquidacion?> onclick="chkFiltro_2(this.checked, 'fFliquidacionD', 'fFliquidacionH');" />
            <input type="text" name="fFliquidacionD" id="fFliquidacionD" value="<?=$fFliquidacionD?>" <?=$dFliquidacion?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFecha(this);" /> -
            <input type="text" name="fFliquidacionH" id="fFliquidacionH" value="<?=$fFliquidacionH?>" <?=$dFliquidacion?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFecha(this);" />
        </td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td align="right">Empleado: </td>
        <td class="gallery clearfix">
            <input type="checkbox" <?=$cCodPersona?> onclick="chkFiltroLista_3(this.checked, 'fCodEmpleado', 'fNomEmpleado', 'fCodPersona', 'btEmpleado');" />
            <input type="hidden" name="fCodPersona" id="fCodPersona" value="<?=$fCodPersona?>" />
            <input type="hidden" name="fCodEmpleado" id="fCodEmpleado" value="<?=$fCodEmpleado?>" />
            <input type="text" name="fNomEmpleado" id="fNomEmpleado" style="width:270px;" class="disabled" value="<?=$fNomEmpleado?>" readonly />
            <a href="../lib/listas/listado_empleados.php?filtrar=default&cod=fCodEmpleado&nom=fNomEmpleado&campo3=fCodPersona&iframe=true&width=950&height=525" rel="prettyPhoto[iframe1]" id="btEmpleado" style=" <?=$dCodPersona?>">
                <img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
        <td align="right">Motivo Cese: </td>
        <td>
            <input type="checkbox" <?=$cCodMotivoCes?> onclick="chkFiltro(this.checked, 'fCodMotivoCes');" />
            <select name="fCodMotivoCes" id="fCodMotivoCes" style="width:143px;" <?=$dCodMotivoCes?>>
                <option value="">&nbsp;</option>
                <?=loadSelect("rh_motivocese", "CodMotivoCes", "MotivoCese", $fCodMotivoCes, 0)?>
            </select>
        </td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td align="right">Estado: </td>
        <td>
            <input type="checkbox" <?=$cEstado?> onclick="chkFiltro(this.checked, 'fEstado');" />
            <select name="fEstado" id="fEstado" style="width:143px;" <?=$dEstado?>>
                <option value="">&nbsp;</option>
                <?=loadSelectValores("ESTADO-PRESTACIONES", $fEstado, 0)?>
            </select>
        </td>
        <td align="right"><input type="submit" value="Buscar"></td>
    </tr>
</table>
</div>
<div class="sep"></div>

<!--REGISTROS-->
<center>
<input type="hidden" name="sel_registros" id="sel_registros" />
<table class="tblBotones" style="width:100%; min-width:<?=$_width?>px;">
    <tr>
        <td><div id="rows"></div></td>
        <td align="right" class="gallery clearfix">
            <input type="button" id="btIntereses" value="Intereses" style="width:80px;" onclick="cargarOpcionValidar2(this.form, $('#sel_registros').val(), 'accion=', 'gehen.php?anz=pr_prestaciones_intereses', 'SELF', '');" />
            <input type="button" id="btImprimir" value="Imprimir" style="width:80px;" onclick="abrirReporteVal('a_reporte', 'pr_prestaciones_pdf', '', '', $('#sel_registros'));" />
        </td>
    </tr>
</table>

<div style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:250px;">
    <table class="tblLista" style="width:100%; min-width:2150px;">
        <thead>
            <tr>
                <th width="15"></th>
                <th width="50" onclick="order('CodEmpleado')">Cod.</th>
                <th align="left" onclick="order('NomCompleto')">Nombre Completo</th>
                <th width="75" onclick="order('Fliquidacion')">F.Liquidaci&oacute;n</th>
                <th width="100" align="right" onclick="order('TotalIngresos')">Ingresos</th>
                <th width="100" align="right" onclick="order('TotalEgresos')">Egresos</th>
                <th width="100" align="right" onclick="order('TotalDescuento')">Adelantos</th>
                <th width="100" align="right" onclick="order('TotalNeto')">Total Neto</th>
                <th width="100" align="right" onclick="order('MontoIntereses')">Intereses</th>
                <th width="100" align="right" onclick="order('TotalPrestaciones')">Monto Total</th>
                <th width="100" align="right" onclick="order('MontoPagado')">Monto Pagado</th>
                <th width="100" align="right">Monto Pendiente</th>
                <th width="300" align="left" onclick="order('NomProcesadoPor')">Procesado Por</th>
                <th width="60" onclick="order('PeriodoVoucher')">Periodo</th>
                <th width="60" onclick="order('Voucher')">Voucher</th>
                <th width="200" align="left" onclick="order('MotivoCese')">Motivo</th>
            </tr>
        </thead>
        
        <tbody id="lista_registros">
        <?php
        //  consulto todos
        $sql = "SELECT
                    le.CodPersona,
                    le.Secuencia
                FROM
                    pr_liquidacionempleado le
                    INNER JOIN mastempleado e ON (e.CodPersona = le.CodPersona)
                    INNER JOIN mastpersonas p1 ON (p1.CodPersona = le.CodPersona)
                    INNER JOIN mastpersonas p2 ON (p2.CodPersona = le.ProcesadoPor)
                    LEFT JOIN rh_motivocese cs ON (cs.CodMotivoCes = le.CodMotivoCes)
                WHERE 1 $filtro";
        $rows_total = getNumRows3($sql);
        //  consulto lista
        $sql = "SELECT
                    le.*,
                    e.CodEmpleado,
                    p1.NomCompleto,
                    p2.NomCompleto AS NomProcesadoPor,
                    (SELECT SUM(tne2.TotalNeto)
                     FROM pr_tiponominaempleado tne2
                     WHERE
                        tne2.CodTipoProceso = 'PRS' AND
                        tne2.EstadoPago = 'PA' AND
                        tne2.CodTipoNom = le.CodTipoNom AND
                        tne2.CodOrganismo = le.CodOrganismo AND
                        tne2.CodPersona = le.CodPersona AND
                        tne2.SecuenciaLiquidacion = le.Secuencia) AS MontoPagado
                FROM
                    pr_liquidacionempleado le
                    INNER JOIN mastempleado e ON (e.CodPersona = le.CodPersona)
                    INNER JOIN mastpersonas p1 ON (p1.CodPersona = le.CodPersona)
                    INNER JOIN mastpersonas p2 ON (p2.CodPersona = le.ProcesadoPor)
                    LEFT JOIN rh_motivocese cs ON (cs.CodMotivoCes = le.CodMotivoCes)
                WHERE 1 $filtro
                ORDER BY $fOrderBy
                LIMIT ".intval($limit).", ".intval($maxlimit);
        $field = getRecords($sql);
        $rows_lista = count($field);
        foreach($field as $f) {
            $id = $f['CodPersona'].'_'.$f['Secuencia'];
            $MontoPendiente = $f['TotalPrestaciones'] - $f['MontoPagado'];
            ?>
            <tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
                <th><?=++$i?></th>
                <td align="center"><?=$f['CodEmpleado']?></td>
                <td><?=htmlentities($f['NomCompleto'])?></td>
                <td align="center"><?=formatFechaDMA($f['Fliquidacion'])?></td>
                <td align="right"><?=number_format($f['TotalIngresos'], 2, ',', '.')?></td>
                <td align="right"><?=number_format($f['TotalEgresos'], 2, ',', '.')?></td>
                <td align="right"><?=number_format($f['TotalDescuento'], 2, ',', '.')?></td>
                <td align="right"><?=number_format($f['TotalNeto'], 2, ',', '.')?></td>
                <td align="right"><?=number_format($f['MontoIntereses'], 2, ',', '.')?></td>
                <td align="right"><?=number_format($f['TotalPrestaciones'], 2, ',', '.')?></td>
                <td align="right"><?=number_format($f['MontoPagado'], 2, ',', '.')?></td>
                <td align="right"><?=number_format($MontoPendiente, 2, ',', '.')?></td>
                <td><?=htmlentities($f['NomProcesadoPor'])?></td>
                <td align="center"><?=$f['PeriodoVoucher']?></td>
                <td align="center"><?=$f['Voucher']?></td>
                <td><?=htmlentities($f['MotivoCese'])?></td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
</div>

<table style="width:100%; min-width:<?=$_width?>px;">
    <tr>
        <td>
            Mostrar: 
            <select name="maxlimit" style="width:50px;" onchange="this.form.submit();">
                <?=loadSelectGeneral("MAXLIMIT", $maxlimit, 0)?>
            </select>
        </td>
        <td align="right">
            <?=paginacion(intval($rows_total), intval($rows_lista), intval($maxlimit), intval($limit));?>
        </td>
    </tr>
</table>
</center>
</form>

<div class="gallery clearfix">
    <a href="pagina.php?iframe=true" rel="prettyPhoto[iframe2]" style="display:none;" id="a_reporte"></a>
</div>