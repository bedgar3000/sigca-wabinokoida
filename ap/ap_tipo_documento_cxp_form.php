<?php
if ($opcion == "nuevo") {
	$field['Estado'] = "A";
	$field['FlagProvision'] = "S";
	##
	$titulo = "Nuevo Registro";
	$accion = "nuevo";
	$label_submit = "Guardar";
	$disabled_nuevo = "disabled";
	$disabled_fiscal = "disabled";
	$visible_adelanto = "visibility:hidden;";
	$clkCancelar = "document.getElementById('frmentrada').submit();";
}
elseif ($opcion == "modificar" || $opcion == "ver") {
	//	consulto datos generales
	$sql = "SELECT * FROM ap_tipodocumento WHERE CodTipoDocumento = '".$registro."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query)) $field = mysql_fetch_array($query);
	
	if ($opcion == "modificar") {
		$titulo = "Modificar Registro";
		$accion = "modificar";
		$disabled_modificar = "disabled";
		if ($field['FlagFiscal'] != "S") $disabled_fiscal = "disabled";
		if ($field['FlagProvision'] != "S") $visible_provision = "visibility:hidden;";
		if ($field['FlagAdelanto'] != "S") $visible_adelanto = "visibility:hidden;";
		$label_submit = "Modificar";
		$clkCancelar = "document.getElementById('frmentrada').submit();";
	}
	
	elseif ($opcion == "ver") {
		$titulo = "Ver Registro";
		$disabled_nuevo = "disabled";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$disabled_fiscal = "disabled";
		$display_submit = "display:none;";
		$visible_provision = "visibility:hidden;";
		$visible_adelanto = "visibility:hidden;";
		$clkCancelar = "document.getElementById('frmentrada').submit();";
	}
}
//	------------------------------------
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=ap_tipo_documento_cxp_lista" method="POST" enctype="multipart/form-data" onsubmit="return tipo_documento_cxp(this, '<?=$accion?>');" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
<input type="hidden" name="fCodRegimenFiscal" id="fCodRegimenFiscal" value="<?=$fCodRegimenFiscal?>" />
<input type="hidden" name="fCodVoucher" id="fCodVoucher" value="<?=$fCodVoucher?>" />

<table width="900" class="tblForm">
	<tr>
    	<td colspan="4" class="divFormCaption">Datos del Registro</td>
    </tr>
	<tr>
		<td class="tagForm" width="125">* C&oacute;digo:</td>
		<td>
            <input type="text" id="CodTipoDocumento" style="width:35px;" class="codigo" value="<?=$field['CodTipoDocumento']?>" maxlength="3" <?=$disabled_modificar?> />
		</td>
		<td class="tagForm">* Voucher (Provisi&oacute;n):</td>
		<td>
            <select id="CodVoucher" style="width:200px;" <?=$disabled_ver?>>
                <option value="">&nbsp;</option>
                <?=loadSelect("ac_voucher", "CodVoucher", "Descripcion", $field['CodVoucher'], 10)?>
            </select>
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Descripci&oacute;n:</td>
		<td>
            <input type="text" id="Descripcion" style="width:293px;" maxlength="100" value="<?=htmlentities($field['Descripcion'])?>" <?=$disabled_ver?> />
		</td>
		<td class="tagForm">Voucher (Ord. de Pago):</td>
		<td>
            <select id="CodVoucherOrdPago" style="width:200px;" <?=$disabled_ver?>>
                <option value="">&nbsp;</option>
                <?=loadSelect("ac_voucher", "CodVoucher", "Descripcion", $field['CodVoucherOrdPago'], 10)?>
            </select>
		</td>
	</tr>
	<tr>
		<td class="tagForm">* R&eacute;gimen Fiscal:</td>
		<td>
            <select id="CodRegimenFiscal" style="width:150px;" <?=$disabled_ver?>>
                <option value="">&nbsp;</option>
                <?=loadSelect("ap_regimenfiscal", "CodRegimenFiscal", "Descripcion", $field['CodRegimenFiscal'], 0)?>
            </select>
		</td>
		<td class="tagForm">* Clasificaci&oacute;n:</td>
		<td>
            <select id="Clasificacion" style="width:150px;" <?=$disabled_ver?>>
                <option value="">&nbsp;</option>
                <?=loadSelectValores("CLASIFICACION-CXP", $field['Clasificacion'], 0)?>
            </select>
		</td>
	</tr>
    <tr>
		<td class="tagForm">&nbsp;</td>
		<td>
            <input type="checkbox" id="FlagProvision" <?=chkFlag($field['FlagProvision'])?> onclick="setFlagProvision(this.checked);" <?=$disabled_ver?> /> Genera Voucher de Provisi&oacute;n
		</td>
		<td class="tagForm">Cta. Provisi&oacute;n:</td>
		<td class="gallery clearfix">
            <input type="text" id="CodCuentaProv" style="width:75px;" value="<?=$field['CodCuentaProv']?>" disabled />
            <a href="../lib/listas/listado_plan_cuentas.php?filtrar=default&cod=CodCuentaProv&iframe=true&width=950&height=525" rel="prettyPhoto[iframe1]" id="a_CodCuentaProv" style=" <?=$visible_provision?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
	</tr>
    <tr>
		<td class="tagForm">&nbsp;</td>
		<td>&nbsp;</td>
		<td class="tagForm">Cta. Provisi&oacute;n (Pub.20):</td>
		<td class="gallery clearfix">
            <input type="text" id="CodCuentaProvPub20" style="width:75px;" value="<?=$field['CodCuentaProvPub20']?>" disabled />
            <a href="../lib/listas/listado_plan_cuentas_pub20.php?filtrar=default&cod=CodCuentaProvPub20&iframe=true&width=950&height=525" rel="prettyPhoto[iframe3]" id="a_CodCuentaProvPub20" style=" <?=$visible_provision?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
	</tr>
    <tr>
		<td class="tagForm">&nbsp;</td>
		<td>
            <input type="checkbox" id="FlagAdelanto" <?=chkFlag($field['FlagAdelanto'])?> onclick="setFlagAdelanto(this.checked);" <?=$disabled_ver?> /> Se le considera Adelanto
		</td>
		<td class="tagForm">Cta. Adelanto:</td>
		<td class="gallery clearfix">
            <input type="text" id="CodCuentaAde" style="width:75px;" value="<?=$field['CodCuentaAde']?>" disabled />
            <a href="../lib/listas/listado_plan_cuentas.php?filtrar=default&cod=CodCuentaAde&iframe=true&width=950&height=525" rel="prettyPhoto[iframe2]" id="a_CodCuentaAde" style=" <?=$visible_adelanto?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
	</tr>
    <tr>
		<td class="tagForm">&nbsp;</td>
		<td>&nbsp;</td>
		<td class="tagForm">Cta. Adelanto (Pub.20):</td>
		<td class="gallery clearfix">
            <input type="text" id="CodCuentaAdePub20" style="width:75px;" value="<?=$field['CodCuentaAdePub20']?>" disabled />
            <a href="../lib/listas/listado_plan_cuentas_pub20.php?filtrar=default&cod=CodCuentaAdePub20&iframe=true&width=950&height=525" rel="prettyPhoto[iframe4]" id="a_CodCuentaAdePub20" style=" <?=$visible_adelanto?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
	</tr>
    <tr>
		<td class="tagForm">&nbsp;</td>
		<td>
            <input type="checkbox" id="FlagFiscal" <?=chkFlag($field['FlagFiscal'])?> onclick="chkFiltro(this.checked, 'CodFiscal');" <?=$disabled_ver?> /> Fiscal
		</td>
		<td class="tagForm">Cod. Fiscal:</td>
		<td>
            <input type="text" id="CodFiscal" style="width:35px;" value="<?=$field['CodFiscal']?>" maxlength="2" <?=$disabled_fiscal?> />
        </td>
	</tr>
    <tr>
		<td class="tagForm">&nbsp;</td>
		<td colspan="3">
            <input type="checkbox" id="FlagAutoNomina" <?=chkFlag($field['FlagAutoNomina'])?> <?=$disabled_ver?> /> Autom&aacute;tico de N&oacute;mina
		</td>
	</tr>
	<tr>
		<td class="tagForm">Estado:</td>
		<td colspan="3">
            <input type="radio" name="Estado" id="Activo" value="A" <?=chkOpt($field['Estado'], "A");?> <?=$disabled_nuevo?> /> Activo
            &nbsp; &nbsp; &nbsp; 
            <input type="radio" name="Estado" id="Inactivo" value="I" <?=chkOpt($field['Estado'], "I");?> <?=$disabled_nuevo?> /> Inactivo
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

<div style="width:900px" class="divMsj">Campos Obligatorios *</div>