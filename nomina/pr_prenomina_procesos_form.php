<?php
list($AnioActual, $MesActual, $DiaActual) = split("[/.-]", substr($Ahora, 0, 10));
$FechaActual = "$AnioActual-$MesActual-$DiaActual";
//	------------------------------------
if ($opcion == "nuevo") {
	$field['Estado'] = "A";
	$field['CodOrganismo'] = $_SESSION["ORGANISMO_ACTUAL"];
	$field['CodTipoNom'] = $_SESSION['NOMINA_ACTUAL'];
	$field['CreadoPor'] = $_SESSION["CODPERSONA_ACTUAL"];
	$field['NomCreadoPor'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
	$field['FechaCreado'] = $Ahora;
	$field['Periodo'] = "$AnioActual-$MesActual";
	##
	$_titulo = "Iniciar Periodo";
	$accion = "nuevo";
	$label_submit = "Guardar";
	$disabled_nuevo = "disabled";
	$clkCancelar = "document.getElementById('frmentrada').submit();";
}
elseif ($opcion == "modificar" || $opcion == "ver" || $opcion == "aprobar") {
	list($CodOrganismo, $CodTipoNom, $Periodo, $CodTipoProceso) = split("[_]", $sel_registros);
	//	consulto datos generales
	$sql = "SELECT
				pp.*,
				p1.NomCompleto AS NomCreadoPor,
				p2.NomCompleto AS NomProcesadoPor
			FROM
				pr_procesoperiodoprenomina pp
				INNER JOIN mastpersonas p1 ON (p1.CodPersona = pp.CreadoPor)
				LEFT JOIN mastpersonas p2 ON (p2.CodPersona = pp.ProcesadoPor)
			WHERE
				pp.CodOrganismo = '".$CodOrganismo."' AND
				pp.CodTipoNom = '".$CodTipoNom."' AND
				pp.Periodo = '".$Periodo."' AND
				pp.CodTipoProceso = '".$CodTipoProceso."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query)) $field = mysql_fetch_array($query);
	##
	if ($opcion == "modificar") {
		$_titulo = "Modificar Proceso";
		$accion = "modificar";
		$disabled_modificar = "disabled";
		$display_modificar = "display:none;";
		$label_submit = "Modificar";
		$clkCancelar = "document.getElementById('frmentrada').submit();";
	}
	##
	elseif ($opcion == "ver") {
		$_titulo = "Ver Proceso";
		$disabled_nuevo = "disabled";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$display_modificar = "display:none;";
		$display_submit = "display:none;";
		$clkCancelar = "document.getElementById('frmentrada').submit();";
	}
}
//	------------------------------------
$_width = 800;
$_sufijo = "prenomina_procesos";
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=pr_<?=$_sufijo?>_lista" method="POST" enctype="multipart/form-data" onsubmit="return <?=$_sufijo?>(this, '<?=$accion?>');" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
<input type="hidden" name="fCodTipoNom" id="fCodTipoNom" value="<?=$fCodTipoNom?>" />
<input type="hidden" name="fCodTipoDocumento" id="fCodTipoDocumento" value="<?=$fCodTipoDocumento?>" />
<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
<input type="hidden" name="fPeriodo" id="fPeriodo" value="<?=$fPeriodo?>" />

<table width="<?=$_width?>" class="tblForm">
	<tr>
    	<td colspan="4" class="divFormCaption">Datos del Proceso</td>
    </tr>
    <tr>
		<td class="tagForm" width="125">* Organismo:</td>
		<td>
            <select id="CodOrganismo" style="width:280px;" <?=$disabled_modificar?>>
                <?=getOrganismos($field['CodOrganismo'], 3)?>
            </select>
		</td>
		<td class="tagForm" width="125">* Tipo de N&oacute;mina:</td>
		<td>
            <select id="CodTipoNom" style="width:180px;" onChange="getOptionsSelect(this.value, 'loadNominaPeriodos', 'Periodo', 1);" <?=$disabled_modificar?>>
            	<option value="">&nbsp;</option>
                <?=loadSelect("tiponomina", "CodTipoNom", "Nomina", $field['CodTipoNom'], 0)?>
            </select>
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Periodo:</td>
		<td>
            <select id="Periodo" style="width:75px;" <?=$disabled_modificar?>>
            	<option value="">&nbsp;</option>
				<?=loadNominaPeriodos($field['CodTipoNom'], $field['Periodo'])?>
            </select>
		</td>
		<td class="tagForm">* Tipo de Proceso:</td>
		<td>
            <select id="CodTipoProceso" style="width:180px;" <?=$disabled_modificar?>>
            	<option value="">&nbsp;</option>
                <?=loadSelect("pr_tipoproceso", "CodTipoProceso", "Descripcion", $field['CodTipoProceso'], 0)?>
            </select>
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Desde:</td>
		<td>
        	<input type="text" id="FechaDesde" value="<?=formatFechaDMA($field['FechaDesde'])?>" class="datepicker" style="width:70px;" <?=$disabled_ver?> />
		</td>
		<td class="tagForm">* Hasta:</td>
		<td>
        	<input type="text" id="FechaHasta" value="<?=formatFechaDMA($field['FechaHasta'])?>" class="datepicker" style="width:70px;" <?=$disabled_ver?> />
		</td>
	</tr>
	<tr>
		<td class="tagForm">Estado:</td>
		<td>
            <input type="radio" name="Estado" id="Activo" value="A" <?=chkOpt($field['Estado'], "A");?> <?=$disabled_nuevo?> /> Activo
            &nbsp; &nbsp;
            <input type="radio" name="Estado" id="Inactivo" value="I" <?=chkOpt($field['Estado'], "I");?> <?=$disabled_nuevo?> /> Inactivo
		</td>
		<td class="tagForm">&nbsp;</td>
		<td>
            <input type="checkbox" id="FlagMensual" <?=chkOpt($field['FlagMensual'], "S");?> <?=$disabled_ver?> /> Proceso Mensual?
		</td>
	</tr>
    <tr>
		<td class="tagForm">&Uacute;ltima Modif.:</td>
		<td>
			<input type="text" size="30" value="<?=$field['UltimoUsuario']?>" disabled="disabled" />
			<input type="text" size="25" value="<?=$field['UltimaFecha']?>" disabled="disabled" />
		</td>
	</tr>
	<tr>
    	<td colspan="4" class="divFormCaption">Datos de la Planilla</td>
    </tr>
	<tr>
		<td class="tagForm">Procesado Por:</td>
		<td>
            <input type="hidden" id="ProcesadoPor" value="<?=$field['ProcesadoPor']?>" />
            <input type="text" id="NomProcesadoPor" value="<?=htmlentities($field['NomProcesadoPor'])?>" style="width:245px;" disabled="disabled" />
        </td>
		<td class="tagForm">Fecha de Proceso:</td>
		<td>
        	<input type="text" id="FechaProceso" value="<?=formatFechaDMA($field['FechaProceso'])?>" style="width:100px;" disabled="disabled" />
        </td>
	</tr>
	<tr>
		<td class="tagForm">Creado Por:</td>
		<td>
            <input type="hidden" id="CreadoPor" value="<?=$field['CreadoPor']?>" />
            <input type="text" id="NomCreadoPor" value="<?=htmlentities($field['NomCreadoPor'])?>" style="width:245px;" disabled="disabled" />
        </td>
		<td class="tagForm">Fecha de Creaci&oacute;n:</td>
		<td>
            <input type="text" id="FechaCreado" value="<?=formatFechaDMA($field['FechaCreado'])?>" style="width:100px;" disabled="disabled" />
        </td>
	</tr>
</table>

<center>
<input type="submit" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" />
<input type="button" value="Cancelar" style="width:75px;" onclick="<?=$clkCancelar?>" />
</center>
</form>

<div style="width:<?=$_width?>px" class="divMsj">Campos Obligatorios *</div>