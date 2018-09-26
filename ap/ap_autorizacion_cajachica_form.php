<?php
if ($opcion == "nuevo") {
	$field['Estado'] = "A";
	##
	$titulo = "Nuevo Registro";
	$accion = "nuevo";
	$label_submit = "Guardar";
	$disabled_nuevo = "disabled";
	$clkCancelar = "document.getElementById('frmentrada').submit();";
}
elseif ($opcion == "modificar" || $opcion == "ver") {
	list($CodOrganismo, $CodPersona) = split("[_]", $sel_registros);
	//	consulto datos generales
	$sql = "SELECT
				cca.CodOrganismo,
				cca.CodEmpleado AS CodPersona,
				cca.Monto,
				cca.Estado,
				cca.UltimoUsuario,
				cca.UltimaFecha,
				p.NomCompleto AS NomEmpleado,
				e.CodEmpleado
			FROM
				ap_cajachicaautorizacion cca
				INNER JOIN mastpersonas p ON (cca.CodEmpleado = p.CodPersona)
				INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
			WHERE
				cca.CodOrganismo = '".$CodOrganismo."' AND
				cca.CodEmpleado = '".$CodPersona."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query)) $field = mysql_fetch_array($query);
	
	if ($opcion == "modificar") {
		$titulo = "Modificar Registro";
		$accion = "modificar";
		$disabled_modificar = "disabled";
		$display_modificar = "display:none;";
		$label_submit = "Modificar";
		$clkCancelar = "document.getElementById('frmentrada').submit();";
	}
	
	elseif ($opcion == "ver") {
		$titulo = "Ver Registro";
		$disabled_nuevo = "disabled";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$display_modificar = "display:none;";
		$display_submit = "display:none;";
		$clkCancelar = "document.getElementById('frmentrada').submit();";
	}
}
//	------------------------------------
$_width = 600;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=ap_autorizacion_cajachica_lista" method="POST" enctype="multipart/form-data" onsubmit="return autorizacion_cajachica(this, '<?=$accion?>');" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
<input type="hidden" name="fCodEmpleado" id="fCodEmpleado" value="<?=$fCodEmpleado?>" />
<input type="hidden" name="fCodPersona" id="fCodPersona" value="<?=$fCodPersona?>" />
<input type="hidden" name="fNomEmpleado" id="fNomEmpleado" value="<?=$fNomEmpleado?>" />

<table width="<?=$_width?>" class="tblForm">
	<tr>
    	<td colspan="2" class="divFormCaption">Datos del Registro</td>
    </tr>
    <tr>
		<td class="tagForm" width="125">Organismo:</td>
		<td>
            <select id="CodOrganismo" style="width:280px;" <?=$disabled_modificar?>>
                <?=getOrganismos($fCodOrganismo, 3)?>
            </select>
		</td>
	</tr>
    <tr>
		<td class="tagForm">Empleado:</td>
		<td class="gallery clearfix">
            <input type="hidden" id="CodPersona" value="<?=$field['CodPersona']?>" style="width:75px;" disabled />
            <input type="hidden" id="CodEmpleado" value="<?=$field['CodEmpleado']?>" style="width:75px;" disabled />
            <input type="text" id="NomEmpleado" value="<?=$field['NomEmpleado']?>" style="width:275px;" disabled />
            <a href="../lib/listas/listado_empleados.php?filtrar=default&cod=CodEmpleado&nom=NomEmpleado&campo3=CodPersona&iframe=true&width=950&height=425" rel="prettyPhoto[iframe1]" id="btEmpleado" style=" <?=$display_modificar?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
	</tr>
	<tr>
		<td class="tagForm">Monto Autorizado:</td>
		<td>
        	<input type="text" id="Monto" value="<?=number_format($field['Monto'], 2, ',', '.')?>" style="width:100px; text-align:right;" class="currency" />
		</td>
	</tr>
	<tr>
		<td class="tagForm">Estado:</td>
		<td>
            <input type="radio" name="Estado" id="Activo" value="A" <?=chkOpt($field['Estado'], "A");?> <?=$disabled_nuevo?> /> Activo
            &nbsp; &nbsp;
            <input type="radio" name="Estado" id="Inactivo" value="I" <?=chkOpt($field['Estado'], "I");?> <?=$disabled_nuevo?> /> Inactivo
		</td>
	</tr>
    <tr>
		<td class="tagForm">&Uacute;ltima Modif.:</td>
		<td>
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