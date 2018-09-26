<?php
list($AnioActual, $MesActual, $DiaActual) = split("[/.-]", substr($Ahora, 0, 10));
$FechaActual = "$AnioActual-$MesActual-$DiaActual";
//	------------------------------------
if ($opcion == "nuevo") {
	$field['Estado'] = "A";
	##
	$_titulo = "Nuevo Registro";
	$accion = "nuevo";
	$label_submit = "Guardar";
	$disabled_nuevo = "disabled";
	$focus = "CodTipoProceso";
	$clkCancelar = "document.getElementById('frmentrada').submit();";
}
elseif ($opcion == "modificar" || $opcion == "ver" || $opcion == "aprobar") {
	//	consulto datos generales
	$sql = "SELECT *
			FROM pr_tipoproceso
			WHERE CodTipoProceso = '".$sel_registros."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query)) $field = mysql_fetch_array($query);
	##
	if ($opcion == "modificar") {
		$_titulo = "Modificar Registro";
		$accion = "modificar";
		$disabled_modificar = "disabled";
		$label_submit = "Modificar";
		$focus = "Descripcion";
		$clkCancelar = "document.getElementById('frmentrada').submit();";
	}
	##
	elseif ($opcion == "ver") {
		$_titulo = "Ver Registro";
		$disabled_nuevo = "disabled";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$display_submit = "display:none;";
		$clkCancelar = "document.getElementById('frmentrada').submit();";
	}
}
//	------------------------------------
$_width = 500;
$_sufijo = "tipo_proceso";
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
<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />

<table width="<?=$_width?>" class="tblForm">
	<tr>
    	<td colspan="2" class="divFormCaption">Datos del Registro</td>
    </tr>
	<tr>
		<td class="tagForm" width="100">* C&oacute;digo:</td>
		<td>
            <input type="text" id="CodTipoProceso" value="<?=$field['CodTipoProceso']?>" style="width:50px;" class="codigo" maxlength="3" <?=$disabled_modificar?> />
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Descripci&oacute;n:</td>
		<td>
            <input type="text" id="Descripcion" value="<?=htmlentities($field['Descripcion'])?>" style="width:275px;" maxlength="100" <?=$disabled_ver?> />
		</td>
    </tr>
    <tr>
		<td class="tagForm">&nbsp;</td>
		<td>
            <input type="checkbox" id="FlagAdelanto" <?=chkOpt($field['FlagAdelanto'], "S");?> <?=$disabled_ver?> /> El Proceso es un Adelanto
		</td>
    </tr>
    <tr>
		<td class="tagForm">&nbsp;</td>
		<td>
            <input type="checkbox" id="FlagRetroactivo" <?=chkOpt($field['FlagRetroactivo'], "S");?> <?=$disabled_ver?> /> Pago de Retroactivo
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