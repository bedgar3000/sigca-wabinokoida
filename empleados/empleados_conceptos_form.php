<?php
//	------------------------------------
if ($opcion == "nuevo") {
	$field['Estado'] = "A";
	$field['Procesos'] = "[TODOS]";
	$field['FlagTipoProceso'] = "N";
	$field['PeriodoDesde'] = "$AnioActual-$MesActual";
	$field['FlagManual'] = "N";
	##
	$_titulo = "Nuevo Concepto";
	$accion = "nuevo";
	$label_submit = "Guardar";
	$disabled_nuevo = "disabled";
	$disabled_manual = "disabled";
	$visible_procesos = "visibility:hidden;";
	$focus = "PeriodoDesde";
	$clkCancelar = "document.getElementById('frmentrada').submit();";
}
elseif ($opcion == "modificar" || $opcion == "ver") {
	//	consulto datos generales
	list($CodPersona, $CodConcepto) = split("[_]", $sel_registros);
	$sql = "SELECT
				ec.*,
				c.Descripcion AS NomConcepto
            FROM
				pr_empleadoconcepto ec
				INNER JOIN pr_concepto c ON (c.CodConcepto = ec.CodConcepto)
            WHERE
				ec.CodPersona = '".$CodPersona."' AND
				ec.CodConcepto = '".$CodConcepto."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query)) $field = mysql_fetch_array($query);
	##
	if ($opcion == "modificar") {
		$_titulo = "Modificar Concepto";
		$accion = "modificar";
		$disabled_modificar = "disabled";
		if ($field['FlagTipoProceso'] == "N") $visible_procesos = "visibility:hidden;";
		if ($field['FlagManual'] == "N") $disabled_manual = "disabled";
		$label_submit = "Modificar";
		$focus = "Descripcion";
		$clkCancelar = "document.getElementById('frmentrada').submit();";
	}
	##
	elseif ($opcion == "ver") {
		$_titulo = "Ver Concepto";
		$disabled_nuevo = "disabled";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$disabled_manual = "disabled";
		$visible_procesos = "visibility:hidden;";
		$display_submit = "display:none;";
		$clkCancelar = "document.getElementById('frmentrada').submit();";
	}
}
//	------------------------------------
//	datos del empleado
$sql = "SELECT
			p.CodPersona,
			p.NomCompleto,
			e.CodEmpleado,
			e.CodTipoNom
		FROM
			mastpersonas p
			INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
		WHERE p.CodPersona = '".$CodPersona."'";
$query_empleado = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
if (mysql_num_rows($query_empleado)) $field_empleado = mysql_fetch_array($query_empleado);
//	------------------------------------
$_width = 700;
$_sufijo = "empleados_conceptos";
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=<?=$_sufijo?>_lista" method="POST" enctype="multipart/form-data" onsubmit="return <?=$_sufijo?>(this, '<?=$accion?>');" autocomplete="off">
<input type="hidden" name="ffOrderBy" id="ffOrderBy" value="<?=$ffOrderBy?>" />
<input type="hidden" name="CodPersona" id="CodPersona" value="<?=$CodPersona?>" />
<input type="hidden" name="sel_registros" id="sel_registros" />
<?=filtroEmpleados()?>

<div class="divBorder" style="width:<?=$_width?>px;">
<table width="<?=$_width?>" class="tblFiltro">
	<tr>
    	<td colspan="2" class="divFormCaption">Datos del Empleado</td>
    </tr>
	<tr>
		<td align="right" width="125">Empleado:</td>
		<td>
        	<input type="text" id="CodEmpleado" style="width:60px;" class="codigo" value="<?=$field_empleado['CodEmpleado']?>" disabled />
		</td>
	</tr>
	<tr>
		<td align="right">Nombre Completo:</td>
		<td>
        	<input type="text" id="NomCompleto" style="width:500px;" class="codigo" value="<?=$field_empleado['NomCompleto']?>" disabled />
		</td>
	</tr>
</table>
</div><br />

<table width="<?=$_width?>" class="tblForm">
	<tr>
    	<td colspan="4" class="divFormCaption">Datos del Concepto</td>
    </tr>
    <tr>
		<td class="tagForm">* Concepto:</td>
		<td class="gallery clearfix">
            <input type="hidden" id="CodConcepto" value="<?=$field['CodConcepto']?>" />
            <input type="text" id="NomConcepto" value="<?=htmlentities($field['NomConcepto'])?>" style="width:270px;" disabled />
            <a href="../lib/listas/listado_conceptos.php?filtrar=default&cod=CodConcepto&nom=NomConcepto&ventana=empleados_conceptos&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" id="btConcepto" style=" <?=$display_modificar?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
		<td class="tagForm">&nbsp;</td>
		<td>
        	<input type="checkbox" id="FlagManual" <?=chkFlag($field['FlagManual'])?> onclick="$('#Cantidad').val('0,00').prop('disabled', !this.checked); $('#Monto').val('0,00').prop('disabled', !this.checked);" <?=$disabled_ver?> />
        	Monto Manual
        </td>
	</tr>
    <tr>
		<td class="tagForm">Procesos:</td>
		<td class="gallery clearfix">
        	<input type="checkbox" id="FlagTipoProceso" <?=chkFlag($field['FlagTipoProceso'])?> onclick="setFlagTipoProceso(this.checked);" <?=$disabled_ver?> />
            <input type="text" id="Procesos" value="<?=htmlentities($field['Procesos'])?>" style="width:246px;" disabled />
            <a href="../lib/listas/listado_tipo_proceso_multiple.php?filtrar=default&cod=Procesos&ventana=empleados_conceptos&iframe=true&width=725&height=440" rel="prettyPhoto[iframe2]" id="btProcesos" style=" <?=$visible_procesos?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
		<td class="tagForm">Monto:</td>
		<td>
        	<input type="text" id="Monto" value="<?=number_format($field['Monto'], 2, ',', '.')?>" style="width:100px; text-align:right;" class="currency" <?=$disabled_manual?> />
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Desde:</td>
		<td>
            <select id="PeriodoDesde" style="width:75px;" <?=$disabled_ver?>>
				<?=loadNominaPeriodos($field_empleado['CodTipoNom'], $field['PeriodoDesde'])?>
            </select>
		</td>
		<td class="tagForm">Cantidad:</td>
		<td>
        	<input type="text" id="Cantidad" value="<?=number_format($field['Cantidad'], 2, ',', '.')?>" style="width:100px; text-align:right;" class="currency" <?=$disabled_manual?> />
		</td>
	</tr>
	<tr>
		<td class="tagForm">Hasta:</td>
		<td>
            <select id="PeriodoHasta" style="width:75px;" <?=$disabled_ver?>>
            	<option value="">&nbsp;</option>
				<?=loadNominaPeriodos($field_empleado['CodTipoNom'], $field['PeriodoHasta'])?>
            </select>
		</td>
		<td class="tagForm">* Estado:</td>
		<td>
            <select id="Estado" style="width:75px;" <?=$disabled_ver?>>
                <?=loadSelectGeneral("ESTADO", $field['Estado'], 0)?>
            </select>
		</td>
	</tr>
    <tr>
		<td class="tagForm">&Uacute;ltima Modif.:</td>
		<td colspan="3">
			<input type="text" size="30" value="<?=$field['UltimoUsuario']?>" disabled="disabled" />
			<input type="text" size="25" value="<?=$field['UltimaFecha']?>" disabled="disabled" />
		</td>
	</tr>
</table>

<center>
<input type="submit" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" />
<input type="button" value="Cancelar" style="width:75px;" onclick="<?=$clkCancelar?>" />
</center>
</form>

<div style="width:<?=$_width?>px" class="divMsj">Campos Obligatorios *</div>