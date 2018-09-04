<?php
list($AnioActual, $MesActual, $DiaActual) = split("[/.-]", substr($Ahora, 0, 10));
$FechaActual = "$AnioActual-$MesActual-$DiaActual";
$PeriodoActual = "$AnioActual-$MesActual";
//	------------------------------------
if ($filtrar == "default") {
	$CodOrganismo = $_SESSION["ORGANISMO_ACTUAL"];
	$CodTipoNom = $_SESSION["NOMINA_ACTUAL"];
	$Estado = "A";
	$Procesos = "[TODOS]";
	$FlagTipoProceso = "N";
	$FlagManual = "N";
}
if ($FlagTipoProceso != "S") $visible_procesos = "visibility:hidden;";
if ($FlagManual != "S") $disabled_manual = "disabled";
$Monto = setNumero($Monto);
$Cantidad = setNumero($Cantidad);
//	------------------------------------
$_titulo = "Asignaci&oacute;n de Conceptos Permanentes";
$_width = 1000;
$_sufijo = "conceptos_permanentes";
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=pr_<?=$_sufijo?>" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" id="TipoAplicacion" value="P" />

<div class="divBorder" style="width:<?=$_width?>px;">
<table width="<?=$_width?>" class="tblFiltro">
	<tr>
		<td align="right" width="125">Organismo:</td>
		<td style="padding-left:25px;">
			<select name="CodOrganismo" id="CodOrganismo" style="width:275px;">
				<?=getOrganismos($CodOrganismo, 3)?>
			</select>
		</td>
		<td align="right" width="125">N&oacute;mina:</td>
		<td style="padding-left:25px;">
			<select name="CodTipoNom" id="CodTipoNom" style="width:250px;" onChange="getOptionsSelect(this.value, 'loadNominaPeriodos', 'Periodo', 1);">
            	<option value="">&nbsp;</option>
				<?=loadSelect("tiponomina", "CodTipoNom", "Nomina", $CodTipoNom, 0)?>
			</select>
		</td>
	</tr>
	<tr>
		<td align="right">Mes de Ingreso:</td>
		<td style="padding-left:25px;">
			<select name="MesIngreso" id="MesIngreso" style="width:100px;">
            	<option value="">&nbsp;</option>
				<?=loadSelectGeneral("MES-NOMBRE", $MesIngreso, 0)?>
			</select>
		</td>
		<td align="right">&nbsp;</td>
		<td>
            <input type="checkbox" name="SitTra" id="SitTra" value="A" <?=chkOpt("A", $SitTra)?> /> Mostrar empleados inactivos
		</td>
	</tr>
    <tr><td colspan="4"><hr /></td></tr>
	<tr>
		<td align="right">* Concepto:</td>
		<td class="gallery clearfix" style="padding-left:25px;">
            <input type="hidden" name="CodConcepto" id="CodConcepto" value="<?=$CodConcepto?>" />
            <input type="text" name="NomConcepto" id="NomConcepto" value="<?=htmlentities($NomConcepto)?>" style="width:270px;" class="disabled" readonly />
            <a href="../lib/listas/listado_conceptos.php?filtrar=default&cod=CodConcepto&nom=NomConcepto&ventana=empleados_conceptos&iframe=true&width=100%&height=425" rel="prettyPhoto[iframe1]" id="btConcepto">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
		<td align="right">&nbsp;</td>
		<td>
        	<input type="checkbox" name="FlagManual" id="FlagManual" value="S" <?=chkFlag($FlagManual)?> onclick="$('.Cantidad').val('0,00').prop('disabled', !this.checked); $('.Monto').val('0,00').prop('disabled', !this.checked); $('.FlagManual').prop('checked', this.checked);" />
        	Monto Manual
        </td>
	</tr>
	<tr>
		<td align="right">Procesos:</td>
		<td class="gallery clearfix">
        	<input type="checkbox" name="FlagTipoProceso" id="FlagTipoProceso" value="S" <?=chkFlag($FlagTipoProceso)?> onclick="setFlagTipoProceso(this.checked);" />
            <input type="text" name="Procesos" id="Procesos" value="<?=htmlentities($Procesos)?>" style="width:270px;" class="disabled" readonly />
            <a href="../lib/listas/listado_tipo_proceso_multiple.php?filtrar=default&cod=Procesos&ventana=empleados_conceptos&iframe=true&width=725&height=440" rel="prettyPhoto[iframe2]" id="btProcesos" style=" <?=$visible_procesos?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
		<td align="right">Cantidad:</td>
		<td style="padding-left:25px;">
        	<input type="text" name="Cantidad" id="Cantidad" value="<?=number_format($Cantidad, 2, ',', '.')?>" style="width:100px; text-align:right;" class="currency Cantidad" onChange="$('.Cantidad').val(this.value);" <?=$disabled_manual?> />
		</td>
	</tr>
    <tr>
		<td align="right">Desde:</td>
		<td style="padding-left:25px;">
			<select name="PeriodoDesde" id="PeriodoDesde" style="width:75px;" onChange="$('.PeriodoDesde').val(this.value);">
            	<option value="">&nbsp;</option>
				<?=loadNominaPeriodos($CodTipoNom, $PeriodoDesde)?>
            </select>
		</td>
		<td align="right">Monto:</td>
		<td style="padding-left:25px;">
        	<input type="text" name="Monto" id="Monto" value="<?=number_format($Monto, 2, ',', '.')?>" style="width:100px; text-align:right;" class="currency Monto" onChange="$('.Monto').val(this.value);" <?=$disabled_manual?> />
		</td>
	</tr>
	<tr>
		<td align="right">Hasta:</td>
		<td style="padding-left:25px;">
			<select name="PeriodoHasta" id="PeriodoHasta" style="width:75px;" onChange="$('.PeriodoHasta').val(this.value);">
            	<option value="">&nbsp;</option>
            </select>
		</td>
		<td align="right">Estado:</td>
		<td style="padding-left:25px;">
            <select name="Estado" id="Estado" style="width:75px;" onChange="$('.Estado').val(this.value);">
                <?=loadSelectGeneral("ESTADO", $Estado, 0)?>
            </select>
		</td>
	</tr>
</table>
</div>
<center><input type="submit" value="Mostrar"></center>
</form><br />

<form name="frm_empleados" id="frm_empleados">
<center>
<table width="<?=$_width?>" class="tblBotones">
    <tr>
        <td>
        	<a class="link" href="#" onclick="selTodos('empleados');">Todos</a> |
            <a class="link" href="#" onclick="selNinguno('empleados');">Ninguno</a>
        </td>
        <td align="right">
            <input type="button" id="btActualizar" value="Asignar Concepto" onClick="conceptos_asignacion();" />
        </td>
    </tr>
</table>
<div style="overflow:scroll; width:<?=$_width?>px; height:300px;">
<table width="1060" class="tblLista">
    <thead>
    <tr>
        <th scope="col" width="15"></th>
        <th scope="col" width="55">Cod.</th>
        <th scope="col" align="left" colspan="2">Empleado</th>
        <th scope="col" width="65">Desde</th>
        <th scope="col" width="65">Hasta</th>
        <th scope="col" width="20">Man.</th>
        <th scope="col" width="75" align="right">Monto</th>
        <th scope="col" width="35" align="right">Cant.</th>
        <th scope="col" width="150">Procesos</th>
        <th scope="col" width="60">Estado</th>
    </tr>
    </thead>
    
    <tbody id="lista_empleados">
    <?php
	if ($CodConcepto != "") {
		$filtro = "";
		if ($MesIngreso != "") $filtro .= " AND (e.Fingreso LIKE '%-$MesIngreso-%')";
		if ($SitTra == "") $filtro .= " AND (e.Estado = 'A')";
		//	consulto lista
		$sql = "SELECT
					e.CodEmpleado,
					p.CodPersona,
					p.NomCompleto,
					ec.TipoAplicacion,
					ec.FlagManual,
					ec.PeriodoDesde,
					ec.PeriodoHasta,
					ec.Monto,
					ec.Cantidad,
					ec.Procesos,
					ec.FlagManual,
					ec.Estado
				FROM
					mastempleado e
					INNER JOIN mastpersonas p ON (p.CodPersona = e.CodPersona)
					LEFT JOIN pr_empleadoconcepto ec ON (ec.CodPersona = e.CodPersona AND ec.CodConcepto = '".$CodConcepto."')
				WHERE
					e.CodOrganismo = '".$CodOrganismo."' AND
					e.CodTipoNom = '".$CodTipoNom."'
					$filtro
				ORDER BY LENGTH(p.Ndocumento), p.Ndocumento";
		$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		$rows_lista = mysql_num_rows($query);	$i=0;
		while ($field = mysql_fetch_array($query)) {
			$id = "$field[CodPersona]";
			if ($field['PeriodoDesde'] == "") {
				if ($PeriodoDesde == "") $field['PeriodoDesde'] = $PeriodoActual;
				else $field['PeriodoDesde'] = $PeriodoDesde;
			}
			if ($field['PeriodoHasta'] == "") {
				if ($PeriodoHasta == "") $field['PeriodoHasta'] = $PeriodoActual;
				else $field['PeriodoHasta'] = $PeriodoHasta;
			}
			if ($field['FlagManual'] == "") $field['FlagManual'] = $FlagManual;
			if ($field['Monto'] == "") $field['Monto'] = $Monto;
			if ($field['Cantidad'] == "") $field['Cantidad'] = $Cantidad;
			if ($field['Procesos'] == "") $field['Procesos'] = $Procesos;
			if ($field['Estado'] == "") $field['Estado'] = $Estado;
			##
			if ($field['FlagManual'] != "S") $disabled_manual = "disabled"; else $disabled_manual = "";
			?>
			<tr class="trListaBody" id="tr<?=$id?>">
				<th onclick="clkMulti($('#tr<?=$id?>'), '<?=$id?>');">
					<input type="checkbox" name="empleados" id="<?=$id?>" value="<?=$id?>" style="display:none" />
					<input type="hidden" name="CodPersona" value="<?=$field['CodPersona']?>" />
					<?=++$i?>
				</th>
				<td onclick="clkMulti($('#tr<?=$id?>'), '<?=$id?>');" align="center">
					<?=$field['CodEmpleado']?>
                </td>
				<td onclick="clkMulti($('#tr<?=$id?>'), '<?=$id?>');">
					<?=htmlentities($field['NomCompleto'])?>
                </td>
				<td align="center" width="10"><?=$field['TipoAplicacion']?></td>
				<td align="center">
					<select name="PeriodoDesde" class="cell PeriodoDesde">
						<?=loadNominaPeriodos($CodTipoNom, $field['PeriodoDesde'])?>
					</select>
				</td>
				<td align="center">
					<select name="PeriodoHasta" class="cell PeriodoHasta">
						<option value="">&nbsp;</option>
					</select>
				</td>
				<td align="center">
					<input type="checkbox" name="FlagManual" class="FlagManual" <?=chkFlag($field['FlagManual'])?> onclick="$('#Cantidad<?=$id?>').val('0,00').prop('disabled', !this.checked); $('#Monto<?=$id?>').val('0,00').prop('disabled', !this.checked);" />
				</td>
				<td align="center">
					<input type="text" name="Monto" id="Monto<?=$id?>" value="<?=number_format($field['Monto'], 2, ',', '.')?>" style="text-align:right;" class="cell currency Monto" <?=$disabled_manual?> />
				</td>
				<td align="center">
					<input type="text" name="Cantidad" id="Cantidad<?=$id?>" value="<?=number_format($field['Cantidad'], 2, ',', '.')?>" style="text-align:right;" class="cell currency Cantidad" <?=$disabled_manual?> />
				</td>
				<td align="center">
					<input type="text" name="Procesos" style="text-align:center;" class="cell2 Procesos" value="<?=$field['Procesos']?>" readonly="readonly"  />
				</td>
				<td align="center">
					<select name="Estado" class="cell Estado">
						<?=loadSelectGeneral("ESTADO", $field['Estado'], 0)?>
					</select>
				</td>
			</tr>
			<?php
		}
	}
    ?>
    </tbody>
</table>
</div>
</center>
</form>