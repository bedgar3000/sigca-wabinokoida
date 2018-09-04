<?php
list($CodPersona, $Secuencia) = split("[_]", $sel_registros);
$CodTipoProceso = "PRS";
$CodConcepto = "0110";
//	------------------------------------
##	
$sql = "SELECT
			le.*,
			e.CodEmpleado,
			e.Fegreso,
			p.NomCompleto AS NomPersona,
			p.Ndocumento,
			pt.DescripCargo,
			mc.MotivoCese
		FROM
			pr_liquidacionempleado le
			INNER JOIN mastpersonas p ON (p.CodPersona = le.CodPersona)
			INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
			INNER JOIN rh_puestos pt ON (pt.CodCargo = le.CodCargo)
			LEFT JOIN rh_motivocese mc ON (mc.CodMotivoCes = le.CodMotivoCes)
		WHERE
			le.CodPersona = '".$CodPersona."' AND
			le.Secuencia = '".$Secuencia."'";
$field = getRecord($sql);
list($Anios, $Meses, $Dias) = getTiempo(formatFechaDMA($field['Fingreso']), formatFechaDMA($field['Fegreso']));
$EgresoPartes = explode("-", $field['Fegreso']);
$AnioEgreso = $EgresoPartes[0];
$MesEgreso = $EgresoPartes[1];
$DiaEgreso = $EgresoPartes[2];
$AnioInicial = intval($AnioEgreso);
$MesInicial = intval($MesEgreso) + 1;
if ($MesInicial > 12) {
	++$AnioInicial;
	$MesInicial = "01";
}
elseif ($MesInicial < 10) $MesInicial = "0".$MesInicial;
$PeriodoInicial = $AnioInicial."-".$MesInicial;
//	------------------------------------
$_titulo = "Intereses Prestaciones Sociales";
$accion = "nuevo";
$label_submit = "Actualizar";
$_width = 800;
$_sufijo = "prestaciones_control";
$clkCancelar = "document.getElementById('frmentrada').submit();";
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=pr_<?=$_sufijo?>" method="POST" enctype="multipart/form-data" onsubmit="return prestaciones_control(this, 'intereses');" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
<input type="hidden" name="fCodTipoNom" id="fCodTipoNom" value="<?=$fCodTipoNom?>" />
<input type="hidden" name="fCodDependencia" id="fCodDependencia" value="<?=$fCodDependencia?>" />
<input type="hidden" name="fFliquidacionD" id="fFliquidacionD" value="<?=$fFliquidacionD?>" />
<input type="hidden" name="fFliquidacionH" id="fFliquidacionH" value="<?=$fFliquidacionH?>" />
<input type="hidden" name="fCodPersona" id="fCodPersona" value="<?=$fCodPersona?>" />
<input type="hidden" name="fCodEmpleado" id="fCodEmpleado" value="<?=$fCodEmpleado?>" />
<input type="hidden" name="fNomEmpleado" id="fNomEmpleado" value="<?=$fNomEmpleado?>" />
<input type="hidden" name="fCodMotivoCes" id="fCodMotivoCes" value="<?=$fCodMotivoCes?>" />
<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
<input type="hidden" id="Secuencia" value="<?=$field['Secuencia']?>" />

<table width="<?=$_width?>" class="tblForm">
	<tr>
    	<td colspan="4" class="divFormCaption">Datos del Empleado</td>
    </tr>
    <tr>
		<td class="tagForm" width="125">Empleado:</td>
		<td>
        	<input type="hidden" id="CodPersona" value="<?=$field['CodPersona']?>" />
            <input type="text" value="<?=$field['CodEmpleado']?>" style="width:45px;" disabled="disabled" />
            <input type="text" value="<?=htmlentities($field['NomPersona'])?>" style="width:245px;" disabled="disabled" />
		</td>
		<td class="tagForm">Sueldo B&aacute;sico:</td>
		<td>
            <input type="text" id="SueldoBasico" value="<?=number_format($field['SueldoBasico'], 2, ',', '.')?>" style="width:125px; text-align:right;" disabled="disabled" />
		</td>
	</tr>
    <tr>
		<td class="tagForm">Cargo:</td>
		<td>
            <input type="text" value="<?=htmlentities($field['DescripCargo'])?>" style="width:300px;" disabled="disabled" />
		</td>
		<td class="tagForm">Total Ingresos:</td>
		<td>
            <input type="text" value="<?=number_format($field['TotalIngresos'], 2, ',', '.')?>" style="width:125px; text-align:right;" disabled="disabled" />
		</td>
	</tr>
    <tr>
		<td class="tagForm">Motivo:</td>
		<td>
            <input type="text" value="<?=htmlentities($field['MotivoCese'])?>" style="width:300px;" disabled="disabled" />
		</td>
		<td class="tagForm">Total Descuentos:</td>
		<td>
            <input type="text" value="<?=number_format(($field['TotalEgresos']+$field['TotalDescuento']), 2, ',', '.')?>" style="width:125px; text-align:right;" disabled="disabled" />
		</td>
	</tr>
    <tr>
		<td class="tagForm">Fecha de Ingreso:</td>
		<td>
            <input type="text" value="<?=formatFechaDMA($field['Fingreso'])?>" style="width:75px;" disabled="disabled" />
		</td>
		<td class="tagForm">Total Neto:</td>
		<td>
            <input type="text" id="TotalNeto" value="<?=number_format($field['TotalNeto'], 2, ',', '.')?>" style="width:125px; text-align:right; font-weight:bolder;" disabled="disabled" />
		</td>
	</tr>
    <tr>
		<td class="tagForm">Fecha de Egreso:</td>
		<td>
            <input type="text" value="<?=formatFechaDMA($field['Fegreso'])?>" style="width:75px;" disabled="disabled" />
		</td>
		<td class="tagForm">Intereses:</td>
		<td>
            <input type="text" value="<?=number_format($field['MontoIntereses'], 2, ',', '.')?>" style="width:125px; text-align:right; font-weight:bolder; color:#900" disabled="disabled" />
		</td>
	</tr>
    <tr>
		<td class="tagForm">Tiempo de Servicio:</td>
		<td>
            <input type="text" value="<?=$Anios?>" style="width:20px;" disabled="disabled" />A&ntilde;os 
            <input type="text" value="<?=$Meses?>" style="width:20px;" disabled="disabled" />Meses 
            <input type="text" value="<?=$Dias?>" style="width:20px;" disabled="disabled" />Dias 
		</td>
		<td class="tagForm">Total Prestaciones:</td>
		<td>
            <input type="text" value="<?=number_format($field['MontoTotal'], 2, ',', '.')?>" style="width:125px; text-align:right; font-weight:bolder;" disabled="disabled" />
		</td>
	</tr>
</table>

<center>
<input type="submit" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" />
<input type="button" value="Cancelar" style="width:75px;" onclick="<?=$clkCancelar?>" />
</center>
</form>
<br />

<form name="frm_intereses" id="frm_intereses">
<div style="overflow:scroll; width:425px; height:300px; margin:auto;">
<table width="100%" class="tblLista">
    <thead>
    <tr>
        <th scope="col" width="15"></th>
        <th scope="col">Periodo</th>
        <th scope="col" width="100" align="right">Monto Base</th>
        <th scope="col" width="60" align="right">%</th>
        <th scope="col" width="100" align="right">Monto</th>
    </tr>
    </thead>
    
    <tbody>
    <?php
	$error = false;
    $sql = "SELECT
				ti.Periodo,
				ti.Porcentaje,
				lei.Periodo AS PeriodoInteres,
				lei.Monto,
				lei.MontoBase
			FROM
				masttasainteres ti
				LEFT JOIN pr_liquidacionempleadointereses lei ON (lei.Periodo = ti.Periodo AND
																  lei.CodPersona = '".$CodPersona."' AND
																  lei.Secuencia = '".$Secuencia."')
			WHERE ti.Periodo >= '".$PeriodoInicial."'
			ORDER BY Periodo DESC";
	$field_periodos = getRecords($sql);
	foreach($field_periodos as $f) {
		list($AnioPeriodo, $MesPeriodo) = explode("-", $f['Periodo']);
		$DiasAnio = getFechaDiasFull("01-01-$AnioPeriodo", "31-12-$AnioPeriodo");
		$DiasMes = getDiasMes($f['Periodo']);
		##	adelantos
		$sql = "SELECT SUM(TotalNeto)
				FROM pr_tiponominaempleado
				WHERE
					CodPersona = '".$CodPersona."' AND
					Periodo < '".$f['Periodo']."' AND
					CodTipoProceso = 'APR' AND
					EstadoPago = 'PA'";
		$Adelantos = getVar3($sql);
		$MontoBase = $field['TotalIngresos'] - $field['TotalEgresos'] - floatval($Adelantos);
		$Monto = $MontoBase * $f['Porcentaje'] / 100 * $DiasMes / $DiasAnio;
		if ($f['Monto'] != $Monto || $f['MontoBase'] != $MontoBase) {
			$checked = "checked";
		} else {
			$checked = "";
		}
        ?>
        <tr class="trListaBody" style="color:<?=$color?>">
            <td align="center">
            	<input type="checkbox" name="Flag" <?=$checked?> />
                <input type="hidden" name="Periodo" value="<?=$f['Periodo']?>" />
                <input type="hidden" name="Porcentaje" value="<?=$f['Porcentaje']?>" />
                <input type="hidden" name="MontoBase" value="<?=round($MontoBase, 2)?>" />
                <input type="hidden" name="Monto" value="<?=round($Monto, 2)?>" />
			</td>
            <td align="center"><?=$f['Periodo']?></td>
            <td align="right"><?=number_format($MontoBase, 2, ',', '.')?></td>
            <td align="right"><?=number_format($f['Porcentaje'], 2, ',', '.')?></td>
            <td align="right"><?=number_format($Monto, 2, ',', '.')?></td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>
</div>
</form>

<script type="text/javascript" language="javascript">
$(document).ready(function() {
});
</script>