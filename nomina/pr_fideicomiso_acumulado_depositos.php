<?php
list($AnioActual, $MesActual, $DiaActual) = split("[/.-]", substr($Ahora, 0, 10));
$FechaActual = "$AnioActual-$MesActual-$DiaActual";
//	------------------------------------
//	consulto datos generales
$sql = "SELECT
			p.CodPersona,
			p.NomCompleto,
			p.Ndocumento,
			e.CodEmpleado,
			e.Fingreso,
			af.PeriodoInicial,
			af.CodPersona AS PersonaIngresada,
			af.AcumuladoInicialDias,
			af.AcumuladoInicialProv,
			af.AcumuladoInicialFide,
			af.AcumuladoProvDias,
			af.AcumuladoProv,
			af.AcumuladoFide,
			af.AcumuladoDiasAdicionalInicial,
			af.AcumuladoDiasAdicional,
			(af.AcumuladoInicialDias + af.AcumuladoProvDias) AS TotalDias,
			(af.AcumuladoDiasAdicionalInicial + af.AcumuladoDiasAdicional) AS TotalDiasAdicional,
			(af.AcumuladoInicialProv + af.AcumuladoProv) AS TotalAntiguedad,
			(af.AcumuladoInicialFide + af.AcumuladoFide) AS TotalFideicomiso
		FROM
			mastpersonas p
			INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
			LEFT JOIN pr_acumuladofideicomiso af ON (af.CodPersona = p.CodPersona)
		WHERE p.CodPersona = '".$sel_registros."'";
$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
if (mysql_num_rows($query)) $field = mysql_fetch_array($query);
//	------------------------------------
$_titulo = "Dep&oacute;sitos de Antiguedad";
$_width = 700;
$clkCancelar = "parent.$.prettyPhoto.close();";
?>
<table width="<?=$_width?>" class="tblForm">
	<tr>
    	<td colspan="4" class="divFormCaption">Datos Generales</td>
    </tr>
	<tr>
		<td class="tagForm" width="125">Empleado:</td>
		<td>
            <input type="hidden" id="CodPersona" value="<?=$field['CodPersona']?>" />
            <input type="text" value="<?=$field['CodEmpleado']?>" style="width:55px;" disabled="disabled" />
            <input type="text" value="<?=htmlentities($field['NomCompleto'])?>" style="width:200px;" disabled="disabled" />
        </td>
		<td class="tagForm" width="125">Fecha de Ingreso:</td>
		<td>
        	<input type="text" value="<?=formatFechaDMA($field['Fingreso'])?>" style="width:60px;" disabled />
        </td>
	</tr>
    <tr>
		<td class="tagForm">Organismo:</td>
		<td>
            <select id="CodOrganismo" style="width:270px;" disabled>
                <?=loadSelect("mastorganismos", "CodOrganismo", "Organismo", $field['CodOrganismo'], 0)?>
            </select>
		</td>
		<td class="tagForm">Nro. Documento:</td>
		<td>
        	<input type="text" value="<?=number_format($field['Ndocumento'], 0, '', '.')?>" style="width:60px;" disabled />
        </td>
	</tr>
    <tr>
		<td class="tagForm">Dependencia:</td>
		<td>
            <select style="width:270px;" disabled>
                <?=loadSelect("mastdependencias", "CodDependencia", "Dependencia", $field['CodDependencia'], 0)?>
            </select>
		</td>
		<td class="tagForm">Periodo Inicial:</td>
		<td>
        	<input type="text" id="PeriodoInicial" value="<?=$field['PeriodoInicial']?>" style="width:60px;" disabled />
        </td>
	</tr>
</table>

<br />

<div style="overflow:scroll; width:<?=$_width?>px; height:350px;">
<table width="100%" class="tblLista">
    <thead>
    <tr>
        <th scope="col" width="15">&nbsp;</th>
        <th scope="col">Periodo</th>
        <th scope="col" width="75">Anterior Antiguedad</th>
        <th scope="col" width="35">Dias</th>
        <th scope="col" width="75">Transacci&oacute;n</th>
        <th scope="col" width="35">Dias Adic.</th>
        <th scope="col" width="75">Complemento</th>
        <th scope="col" width="75">Anterior Fideicomiso</th>
        <th scope="col" width="75">Interes</th>
    </tr>
    </thead>
    
    <tbody>
    <?php
    //	consulto lista
    $sql = "SELECT
				afd.Periodo,
				afd.Dias,
				afd.Transaccion,
				afd.DiasAdicional,
				afd.Complemento,
				afd.AnteriorFide,
				afd.TransaccionFide,
				(afd.AnteriorProv + (SELECT COALESCE(SUM(Complemento),0)
									 FROM pr_acumuladofideicomisodetalle
									 WHERE
										CodPersona = afd.CodPersona AND
										Periodo < afd.Periodo)) AS AnteriorProv
            FROM pr_acumuladofideicomisodetalle afd
            WHERE afd.CodPersona = '".$sel_registros."'
            ORDER BY Periodo";
    $query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
    $rows_lista = mysql_num_rows($query);	$i=0;
    while ($field = mysql_fetch_array($query)) {
        ?>
        <tr class="trListaBody">
            <th><?=++$i?></th>
            <td align="center"><?=$field['Periodo']?></td>
            <td align="right"><?=number_format($field['AnteriorProv'], 2, ',', '.')?></td>
            <td align="right"><?=number_format($field['Dias'], 2, ',', '.')?></td>
            <td align="right"><?=number_format($field['Transaccion'], 2, ',', '.')?></td>
            <td align="right"><?=number_format($field['DiasAdicional'], 2, ',', '.')?></td>
            <td align="right"><?=number_format($field['Complemento'], 2, ',', '.')?></td>
            <td align="right"><?=number_format($field['AnteriorFide'], 2, ',', '.')?></td>
            <td align="right"><?=number_format($field['TransaccionFide'], 2, ',', '.')?></td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>
</div>