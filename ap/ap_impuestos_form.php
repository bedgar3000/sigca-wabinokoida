<?php
if ($opcion == "nuevo") {
	$field['Signo'] = 'P';
	$field['Estado'] = 'A';
	##
	$_titulo = "Impuestos / Nuevo Registro";
	$accion = "nuevo";
	$disabled_ver = "";
	$disabled_sustraendo = "disabled";
	$read_modificar = "";
	$display_ver = "";
	$display_submit = "";
	$label_submit = "Guardar";
	$focus = "CodImpuesto";
}
elseif ($opcion == "modificar" || $opcion == "ver") {
	##	consulto datos generales
	$sql = "SELECT *
			FROM mastimpuestos
			WHERE CodImpuesto = '$sel_registros'";
	$field = getRecord($sql);
	##
	if ($opcion == "modificar") {
		$_titulo = "Impuestos / Modificar Registro";
		$accion = "modificar";
		$disabled_ver = "";
		$disabled_sustraendo = (($field['FlagSustraendo'] == 'S')?'':'disabled');
		$read_modificar = "readonly";
		$display_ver = "";
		$display_submit = "";
		$label_submit = "Modificar";
		$focus = "Descripcion";
	}
	##
	elseif ($opcion == "ver") {
		$_titulo = "Impuestos / Ver Registro";
		$accion = "";
		$disabled_ver = "disabled";
		$disabled_sustraendo = "disabled";
		$read_modificar = "readonly";
		$display_ver = "display:none;";
		$display_submit = "display:none;";
		$label_submit = "";
		$focus = "btCancelar";
	}
}
$clkCancelar = "document.getElementById('frmentrada').submit();";
//	------------------------------------
$_width = 800;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=ap_impuestos_lista" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('ap_impuestos_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
	<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
	<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
	<input type="hidden" name="fCodRegimenFiscal" id="fCodRegimenFiscal" value="<?=$fCodRegimenFiscal?>" />
	<input type="hidden" name="fFlagImponible" id="fFlagImponible" value="<?=$fFlagImponible?>" />
	<input type="hidden" name="fTipoComprobante" id="fTipoComprobante" value="<?=$fTipoComprobante?>" />
	<input type="hidden" name="fFlagProvision" id="fFlagProvision" value="<?=$fFlagProvision?>" />

	<table style="width:100%; max-width:<?=$_width?>px;" class="tblForm">
		<tr>
	    	<td colspan="4" class="divFormCaption">Datos Generales</td>
	    </tr>
	    <tr>
			<td class="tagForm">Código:</td>
			<td colspan="3">
	        	<input type="text" name="CodImpuesto" id="CodImpuesto" value="<?=$field['CodImpuesto']?>" style="width:145px; font-weight:bold;" maxlength="3" <?=$read_modificar?> />
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Descripción:</td>
			<td colspan="3">
	        	<input type="text" name="Descripcion" id="Descripcion" value="<?=$field['Descripcion']?>" style="width:95%;" maxlength="255" <?=$disabled_ver?> />
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Regimen Fiscal:</td>
			<td>
				<select name="CodRegimenFiscal" id="CodRegimenFiscal" style="width:145px;" <?=$disabled_ver?>>
					<option value="">&nbsp;</option>
					<?=loadSelect2('ap_regimenfiscal','CodRegimenFiscal','Descripcion',$field['CodRegimenFiscal'])?>
				</select>
			</td>
			<td class="tagForm">* Provisión En:</td>
			<td>
				<select name="FlagProvision" id="FlagProvision" style="width:145px;" <?=$disabled_ver?>>
					<option value="">&nbsp;</option>
					<?=loadSelectValores("IMPUESTO-PROVISION", $field['FlagProvision'])?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Imponible:</td>
			<td>
				<select name="FlagImponible" id="FlagImponible" style="width:145px;" <?=$disabled_ver?>>
					<option value="">&nbsp;</option>
					<?=loadSelectValores("IMPUESTO-IMPONIBLE", $field['FlagImponible'])?>
				</select>
			</td>
			<td class="tagForm">* Tipo de Comprobante:</td>
			<td>
				<select name="TipoComprobante" id="TipoComprobante" style="width:145px;" <?=$disabled_ver?>>
					<option value="">&nbsp;</option>
					<?=loadSelectValores("IMPUESTO-COMPROBANTE", $field['TipoComprobante'])?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Porcentaje:</td>
			<td>
				<input type="text" name="FactorPorcentaje" id="FactorPorcentaje" value="<?=number_format($field['FactorPorcentaje'],2,',','.')?>" style="width:145px; text-align: right;" class="currency" <?=$disabled_ver?> />
			</td>
			<td class="tagForm">Signo:</td>
			<td>
	            <input type="radio" name="Signo" id="Positivo" value="P" <?=chkOpt($field['Signo'], "P");?> <?=$disabled_ver?> /> Positivo
	            &nbsp; &nbsp;
	            <input type="radio" name="Signo" id="Negativo" value="N" <?=chkOpt($field['Signo'], "N");?> <?=$disabled_ver?> /> Negativo
			</td>
		</tr>
		<tr>
			<td class="tagForm">&nbsp;</td>
			<td>
	            <input type="checkbox" name="FlagSustraendo" id="FlagSustraendo" value="S" onclick="$('#SustraendoUT').val('0,00000').prop('disabled',!this.checked);" <?=chkOpt($field['FlagSustraendo'], "S");?> <?=$disabled_ver?> /> Sustraendo
			</td>
			<td class="tagForm">* Sustraendo:</td>
			<td>
				<input type="text" name="SustraendoUT" id="SustraendoUT" value="<?=number_format($field['SustraendoUT'],5,',','.')?>" style="width:145px; text-align: right;" class="currency5" <?=$disabled_sustraendo?> />
			</td>
		</tr>
		<tr>
			<td class="tagForm">Partida:</td>
			<td class="gallery clearfix" colspan="3">
	        	<input type="text" name="cod_partida" id="cod_partida" readonly="readonly" style="width:145px;" value="<?=$field['cod_partida']?>" />
				<a href="../lib/listas/gehen.php?anz=lista_partidas&campo1=cod_partida&filtrar=default&ventana=&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" style=" <?=$display_ver?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
	        </td>
		</tr>
		<tr>
			<td class="tagForm">Cuenta (ONCOP):</td>
			<td class="gallery clearfix">
				<input type="text" name="CodCuenta" id="CodCuenta" value="<?=$field['CodCuenta']?>" style="width:145px;" readonly />
				<a href="../lib/listas/gehen.php?anz=lista_plan_cuentas&filtrar=default&campo1=CodCuenta&ventana=&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe2]" style=" <?=$display_ver?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
			<td class="tagForm">Cuenta (Pub.20):</td>
			<td class="gallery clearfix">
				<input type="text" name="CodCuentaPub20" id="CodCuentaPub20" value="<?=$field['CodCuentaPub20']?>" style="width:145px;" readonly />
				<a href="../lib/listas/gehen.php?anz=lista_plan_cuentas20&filtrar=default&campo1=CodCuentaPub20&ventana=&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe3]" style=" <?=$display_ver?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
		</tr>
		<tr>
			<td class="tagForm">Cuenta Ret. Venta (ONCOP):</td>
			<td class="gallery clearfix">
				<input type="text" name="CodCuentaRetVta" id="CodCuentaRetVta" value="<?=$field['CodCuentaRetVta']?>" style="width:145px;" readonly />
				<a href="../lib/listas/gehen.php?anz=lista_plan_cuentas&filtrar=default&campo1=CodCuentaRetVta&ventana=&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe4]" style=" <?=$display_ver?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
			<td class="tagForm">Cuenta Ret. Venta (Pub.20):</td>
			<td class="gallery clearfix">
				<input type="text" name="CodCuentaRetVtaPub20" id="CodCuentaRetVtaPub20" value="<?=$field['CodCuentaRetVtaPub20']?>" style="width:145px;" readonly />
				<a href="../lib/listas/gehen.php?anz=lista_plan_cuentas20&filtrar=default&campo1=CodCuentaRetVtaPub20&ventana=&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe5]" style=" <?=$display_ver?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
		</tr>
		<tr>
			<td class="tagForm">Cuenta Prov. Venta (ONCOP):</td>
			<td class="gallery clearfix">
				<input type="text" name="CodCuentaProvVta" id="CodCuentaProvVta" value="<?=$field['CodCuentaProvVta']?>" style="width:145px;" readonly />
				<a href="../lib/listas/gehen.php?anz=lista_plan_cuentas&filtrar=default&campo1=CodCuentaProvVta&ventana=&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe6]" style=" <?=$display_ver?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
			<td class="tagForm">Cuenta Prov. Venta (Pub.20):</td>
			<td class="gallery clearfix">
				<input type="text" name="CodCuentaProvVtaPub20" id="CodCuentaProvVtaPub20" value="<?=$field['CodCuentaProvVtaPub20']?>" style="width:145px;" readonly />
				<a href="../lib/listas/gehen.php?anz=lista_plan_cuentas20&filtrar=default&campo1=CodCuentaProvVtaPub20&ventana=&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe7]" style=" <?=$display_ver?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
		</tr>
		<tr>
			<td class="tagForm">Estado:</td>
			<td colspan="3">
	            <input type="radio" name="Estado" id="Activo" value="A" <?=chkOpt($field['Estado'], "A");?> <?=$disabled_ver?> /> Activo
	            &nbsp; &nbsp;
	            <input type="radio" name="Estado" id="Inactivo" value="I" <?=chkOpt($field['Estado'], "I");?> <?=$disabled_ver?> /> Inactivo
			</td>
		</tr>
	    <tr>
			<td class="tagForm">&Uacute;ltima Modif.:</td>
			<td colspan="3">
				<input type="text" value="<?=$field['UltimoUsuario']?>" style="width:145px;" disabled="disabled" />
				<input type="text" value="<?=$field['UltimaFecha']?>" style="width:145px" disabled="disabled" />
			</td>
		</tr>
	</table>

	<center>
		<input type="submit" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" id="btSubmit" />
		<input type="button" value="Cancelar" style="width:75px;" id="btCancelar" onclick="<?=$clkCancelar?>" />
	</center>
</form>
<div style="width:100%; max-width:<?=$_width?>px;" class="divMsj">Campos Obligatorios *</div>