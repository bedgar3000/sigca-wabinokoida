<?php
list($AnioActual, $MesActual, $DiaActual) = split("[/.-]", substr($Ahora, 0, 10));
$FechaActual = "$AnioActual-$MesActual-$DiaActual";
//	------------------------------------
if ($opcion == "nuevo") {
	$field['Estado'] = "A";
	$field['CodTipoNom'] = $_SESSION["NOMINA_ACTUAL"];
	##
	$_titulo = "Nuevo Registro";
	$accion = "nuevo";
	$label_submit = "Guardar";
	$focus = "NroAnio";
	$clkCancelar = "document.getElementById('frmentrada').submit();";
}
elseif ($opcion == "modificar" || $opcion == "ver" || $opcion == "aprobar") {
	list($CodTipoNom, $NroAnio) = explode("_", $sel_registros);
	//	consulto datos generales
	$sql = "SELECT
				*,
				(DiasDisfrutes + DiasAdicionales) AS DiasTotal
			FROM rh_vacaciontabla
			WHERE
				CodTipoNom = '".$CodTipoNom."' AND
				NroAnio = '".$NroAnio."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query)) $field = mysql_fetch_array($query);
	##
	if ($opcion == "modificar") {
		$_titulo = "Modificar Registro";
		$accion = "modificar";
		$disabled_modificar = "disabled";
		$label_submit = "Modificar";
		$focus = "DiasDisfrutes";
		$clkCancelar = "document.getElementById('frmentrada').submit();";
	}
	##
	elseif ($opcion == "ver") {
		$_titulo = "Ver Registro";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$display_submit = "display:none;";
		$clkCancelar = "document.getElementById('frmentrada').submit();";
	}
}
//	------------------------------------
$_width = 600;
$_sufijo = "disfrute_vacacionales";
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=rh_<?=$_sufijo?>_lista" method="POST" enctype="multipart/form-data" onsubmit="return <?=$_sufijo?>(this, '<?=$accion?>');" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />

<table width="<?=$_width?>" class="tblForm">
	<tr>
    	<td colspan="2" class="divFormCaption">Datos del Registro</td>
    </tr>
	<tr>
		<td class="tagForm">* Tipo de N&oacute;mina:</td>
		<td>
            <select id="CodTipoNom" style="width:200px;" <?=$disabled_modificar?>>
            	<option value="">&nbsp;</option>
                <?=loadSelect("tiponomina", "CodTipoNom", "Nomina", $field['CodTipoNom'], 0);?>
            </select>
		</td>
	</tr>
	<tr>
		<td class="tagForm" width="150">* A&ntilde;os:</td>
		<td>
            <input type="text" id="NroAnio" value="<?=$field['NroAnio']?>" style="width:50px;" class="codigo" maxlength="3" <?=$disabled_modificar?> />
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Dias de Disfrutes:</td>
		<td>
            <input type="text" id="DiasDisfrutes" value="<?=$field['DiasDisfrutes']?>" style="width:100px; text-align:right;" maxlength="3" onChange="$('#DiasTotal').val(setNumero($('#DiasDisfrutes').val())+setNumero($('#DiasAdicionales').val()));" <?=$disabled_ver?> />
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Dias Adicionales:</td>
		<td>
            <input type="text" id="DiasAdicionales" value="<?=$field['DiasAdicionales']?>" style="width:100px; text-align:right;" maxlength="3" onChange="$('#DiasTotal').val(setNumero($('#DiasDisfrutes').val())+setNumero($('#DiasAdicionales').val()));" <?=$disabled_ver?> />
		</td>
	</tr>
	<tr>
		<td class="tagForm">Total Disfrutes:</td>
		<td>
            <input type="text" id="DiasTotal" value="<?=$field['DiasTotal']?>" style="width:100px; text-align:right;" disabled />
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