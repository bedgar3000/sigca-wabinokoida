<?php
if ($opcion == "nuevo") {
	$field['Estado'] = "A";
	##
	$titulo = "Nuevo Registro";
	$accion = "nuevo";
	$label_submit = "Guardar";
	$disabled_nuevo = "disabled";
	$clkCancelar = "document.getElementById('frmentrada').submit();";
	$focus = "CodBanco";
}
elseif ($opcion == "modificar" || $opcion == "ver") {
	//	consulto datos generales
	$sql = "SELECT *
			FROM ap_ctabancaria
			WHERE NroCuenta = '".$sel_registros."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query)) $field = mysql_fetch_array($query);
	
	if ($opcion == "modificar") {
		$titulo = "Modificar Registro";
		$accion = "modificar";
		$disabled_modificar = "disabled";
		$label_submit = "Modificar";
		$clkCancelar = "document.getElementById('frmentrada').submit();";
		$focus = "Descripcion";
	}
	
	elseif ($opcion == "ver") {
		$titulo = "Ver Registro";
		$disabled_nuevo = "disabled";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$disabled_tipopagos = "disabled";
		$display_ver = "display:none;";
		$display_submit = "display:none;";
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

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=ap_cuentas_bancarias_lista" method="POST" enctype="multipart/form-data" onsubmit="return cuentas_bancarias(this, '<?=$accion?>');" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
<input type="hidden" name="fCodBanco" id="fCodBanco" value="<?=$fCodBanco?>" />
<input type="hidden" name="fTipoCuenta" id="fTipoCuenta" value="<?=$fTipoCuenta?>" />

<table width="700" class="tblForm">
	<tr>
    	<td colspan="4" class="divFormCaption">Datos de la Cuenta</td>
    </tr>
	<tr>
		<td class="tagForm">* Organismo</td>
		<td colspan="3">
			<select id="CodOrganismo" style="width:300px;" <?=$disabled_ver?>>
				<?=loadSelect("mastorganismos", "CodOrganismo", "Organismo", $field['CodOrganismo'], 0)?>
			</select>
		</td>
    </tr>
	<tr>
		<td class="tagForm">* Banco</td>
		<td colspan="3">
			<select id="CodBanco" style="width:300px;" <?=$disabled_ver?>>
            	<option value="">&nbsp;</option>
				<?=loadSelect("mastbancos", "CodBanco", "Banco", $field['CodBanco'], 0)?>
			</select>
		</td>
    </tr>
	<tr>
		<td class="tagForm">* Nro. Cuenta:</td>
		<td colspan="3">
            <input type="text" id="NroCuenta" style="width:125px;" class="codigo" maxlength="20" value="<?=$field['NroCuenta']?>" <?=$disabled_modificar?> />
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Descripci&oacute;n:</td>
		<td colspan="3">
            <input type="text" id="Descripcion" style="width:90%;" maxlength="100" value="<?=htmlentities($field['Descripcion'])?>" <?=$disabled_ver?> />
		</td>
    </tr>
	<tr>
		<td class="tagForm">* Cta. Bancaria:</td>
		<td colspan="3">
            <input type="text" id="CtaBanco" style="width:125px;" maxlength="20" value="<?=$field['CtaBanco']?>" <?=$disabled_ver?> />
		</td>
    </tr>
    <tr>
		<td class="tagForm">Estado:</td>
		<td colspan="3">
			<input type="radio" name="Estado" id="Activo" value="A" <?=chkOpt($field['Estado'], "A")?> <?=$disabled_nuevo?> /> Activo
            &nbsp;&nbsp;
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
	<tr>
    	<td colspan="2" class="divFormCaption">Caracter&iacute;sticas de la Cuenta</td>
    	<td colspan="2" class="divFormCaption">Informacion para Cartas de Transferencia de Fondo</td>
    </tr>
	<tr>
		<td class="tagForm" width="150">* Tipo  Cta.:</td>
		<td>
			<select id="TipoCuenta" style="width:100px;" <?=$disabled_ver?>>
            	<option value="">&nbsp;</option>
				<?=getMiscelaneos($field['TipoCuenta'], "TIPOCTA", 0);?>
			</select>
		</td>
		<td class="tagForm" width="100">Agencia:</td>
		<td>
            <input type="text" id="Agencia" style="width:90%;" maxlength="100" value="<?=htmlentities($field['Agencia'])?>" <?=$disabled_ver?> />
		</td>
    </tr>
	<tr>
		<td class="tagForm">* Fecha Apertura:</td>
		<td>
        	<input type="text" id="FechaApertura" style="width:93px;" class="datepicker" maxlength="10" value="<?=formatFechaDMA($field['FechaApertura'])?>" onkeyup="setFechaDMA(this)" <?=$disabled_ver?> />
		</td>
		<td class="tagForm">Distrito:</td>
		<td>
            <input type="text" id="Distrito" style="width:90%;" maxlength="100" value="<?=htmlentities($field['Distrito'])?>" <?=$disabled_ver?> />
		</td>
    </tr>
	<tr>
		<td class="tagForm">* Cta. Contable:</td>
		<td class="gallery clearfix">
			<input type="text" id="CodCuenta" style="width:93px;" value="<?=$field['CodCuenta']?>" disabled="disabled" />
            <a href="../lib/listas/listado_plan_cuentas.php?filtrar=default&cod=CodCuenta&iframe=true&width=950&height=525" rel="prettyPhoto[iframe1]" style=" <?=$display_ver?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
		</td>
		<td class="tagForm">Atenci&oacute;n:</td>
		<td>
            <input type="text" id="Atencion" style="width:90%;" maxlength="100" value="<?=htmlentities($field['Atencion'])?>" <?=$disabled_ver?> />
		</td>
    </tr>
	<tr>
		<td class="tagForm">* Cta. Contable (Pub.20):</td>
		<td class="gallery clearfix">
			<input type="text" id="CodCuentaPub20" style="width:93px;" value="<?=$field['CodCuentaPub20']?>" disabled="disabled" />
            <a href="../lib/listas/listado_plan_cuentas_pub20.php?filtrar=default&cod=CodCuentaPub20&iframe=true&width=950&height=525" rel="prettyPhoto[iframe2]" style=" <?=$display_ver?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
		</td>
		<td class="tagForm">Cargo:</td>
		<td>
            <input type="text" id="Cargo" style="width:90%;" maxlength="100" value="<?=htmlentities($field['Cargo'])?>" <?=$disabled_ver?> />
		</td>
    </tr>
	<tr>
    	<td colspan="4" class="divFormCaption">Procesos Aplicables</td>
    </tr>
    <tr>
		<td class="tagForm">&nbsp;</td>
		<td colspan="3">
			<input type="checkbox" id="FlagConciliacionBancaria" value="A" <?=chkOpt($field['FlagConciliacionBancaria'], "S")?> <?=$disabled_ver?> /> Conciliaci&oacute;n Bancaria Contable
		</td>
	</tr>
    <tr>
		<td class="tagForm">&nbsp;</td>
		<td colspan="3">
			<input type="checkbox" id="FlagConciliacionCP" value="A" <?=chkOpt($field['FlagConciliacionCP'], "S")?> <?=$disabled_ver?> /> Conciliaci&oacute;n Resumida en CxP
		</td>
	</tr>
    <tr>
		<td class="tagForm">&nbsp;</td>
		<td colspan="3">
			<input type="checkbox" id="FlagDebitoBancario" value="A" <?=chkOpt($field['FlagDebitoBancario'], "S")?> <?=$disabled_ver?> /> ITF / D&eacute;bito Bancario
		</td>
	</tr>
</table>
<center>
<input type="submit" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" />
<input type="button" value="Cancelar" style="width:75px;" onclick="<?=$clkCancelar?>" />
</center>
</form>
<div style="width:700px" class="divMsj">Campos Obligatorios *</div>
<br />

<center>
<form name="frm_tipopagos" id="frm_tipopagos">
<input type="hidden" id="sel_tipopagos" />
<table width="700" class="tblBotones">
	<thead>
    	<th class="divFormCaption">Tipos de Pago</th>
    </thead>
    <tbody>
    <tr>
        <td align="right" class="gallery clearfix">
            <a id="a_tipopagos" href="../lib/listas/listado_tipo_pago.php?filtrar=default&ventana=cuentas_bancarias_tipopagos_insertar&detalle=tipopagos&iframe=true&width=475&height=400" rel="prettyPhoto[iframe3]" style="display:none;"></a>
            <input type="button" class="btLista" value="Insertar" <?=$disabled_tipopagos?> onclick="$('#a_tipopagos').click();" />
            <input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'tipopagos');" <?=$disabled_tipopagos?> />
        </td>
    </tr>
    </tbody>
</table>
<div style="overflow:scroll; width:700px; height:150px;">
<table width="100%" class="tblLista">
    <thead>
    <tr>
        <th scope="col" width="15">&nbsp;</th>
        <th scope="col" align="left">Tipo de Pago</th>
        <th scope="col" width="300">&Uacute;ltimo N&uacute;mero Generado</th>
    </tr>
    </thead>
    
    <tbody id="lista_tipopagos">
    <?php
	$sql = "SELECT
				cbtp.*,
				tp.TipoPago
			FROM
				ap_ctabancariatipopago cbtp
				INNER JOIN masttipopago tp ON (tp.CodTipoPago = cbtp.CodTipoPago)
			WHERE NroCuenta = '".$field['NroCuenta']."'
			ORDER BY TipoPago";
	$query_tipopagos = mysql_query($sql) or die ($sql.mysql_error());
	while ($field_tipopagos = mysql_fetch_array($query_tipopagos)) {	$nro_tipopagos++;
		?>
        <tr class="trListaBody" onclick="clk($(this), 'tipopagos', '<?=$field_tipopagos['CodTipoPago']?>');" id="tipopagos_<?=$field_tipopagos['CodTipoPago']?>">
			<th>
				<?=$nro_tipopagos?>
			</th>
			<td>
				<input type="hidden" name="CodTipoPago" value="<?=$field_tipopagos['CodTipoPago']?>" />
                <?=htmlentities($field_tipopagos['TipoPago'])?>
			</td>
			<td>
                <input type="text" name="UltimoNumero" style="text-align:right;" class="cell" value="<?=$field_tipopagos['UltimoNumero']?>" maxlength="10" <?=$disabled_tipopagos?> />
			</td>
		</tr>
		<?php
	}
    ?>
    </tbody>
</table>
</div>
<input type="hidden" id="nro_tipopagos" value="<?=$nro_tipopagos?>" />
<input type="hidden" id="can_tipopagos" value="<?=$nro_tipopagos?>" />
</form>
</center>