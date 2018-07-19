<?php
if ($opcion == "nuevo") {
	$field['Estado'] = 'A';
	##
	$_titulo = "Servicios / Nuevo Registro";
	$accion = "nuevo";
	$disabled_ver = "";
	$display_ver = "";
	$display_modificar = "";
	$display_submit = "";
	$label_submit = "Guardar";
	$focus = "CodInterno";
}
elseif ($opcion == "modificar" || $opcion == "ver") {
	##	consulto datos generales
	$sql = "SELECT
				s.*,
				sp.CodInterno AS NroPadre
			FROM co_mastservicios s
			LEFT JOIN co_mastservicios sp ON sp.CodServicio = s.CodPadre
			WHERE s.CodServicio = '".$sel_registros."'";
	$field = getRecord($sql);
	##
	if ($opcion == "modificar") {
		$_titulo = "Servicios / Modificar Registro";
		$accion = "modificar";
		$disabled_ver = "";
		$display_ver = "";
		$display_modificar = "display:none;";
		$display_submit = "";
		$label_submit = "Modificar";
		$focus = "Descripcion";
	}
	##
	elseif ($opcion == "ver") {
		$_titulo = "Servicios / Ver Registro";
		$accion = "";
		$disabled_ver = "disabled";
		$display_ver = "display:none;";
		$display_modificar = "display:none;";
		$display_submit = "display:none;";
		$label_submit = "";
		$focus = "btCancelar";
	}
}
$clkCancelar = "document.getElementById('frmentrada').submit();";
//	------------------------------------
$_width = 600;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=co_servicios_lista" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('co_servicios_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
<input type="hidden" name="fDigitos" id="fDigitos" value="<?=$fDigitos?>" />

<table width="<?=$_width?>" class="tblForm">
	<tr>
    	<td colspan="4" class="divFormCaption">Datos Generales</td>
    </tr>
    <tr>
		<td class="tagForm" width="125">Id.:</td>
		<td>
        	<input type="text" name="CodServicio" id="CodServicio" value="<?=$field['CodServicio']?>" style="width:125px; font-weight:bold;" readonly="readonly" />
		</td>
		<td class="tagForm" width="125">Padre:</td>
		<td class="gallery clearfix">
			<input type="hidden" name="CodPadre" id="CodPadre" value="<?=$field['CodPadre']?>">
        	<input type="text" name="NroPadre" id="NroPadre" value="<?=$field['NroPadre']?>" style="width:125px; font-weight:bold;" readonly />
			<a href="../lib/listas/gehen.php?anz=lista_co_servicios&campo1=CodPadre&campo2=NroPadre&ventana=maestro&fDigitos=<?=$fDigitos?>&filtrar=default&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" style="<?=$display_modificar?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
		</td>
	</tr>
    <tr>
		<td class="tagForm">* C&oacute;digo:</td>
		<td colspan="3">
        	<input type="text" name="CodInterno" id="CodInterno" value="<?=$field['CodInterno']?>" maxlength="10" style="width:125px;" <?=$disabled_ver?> />
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Descripci&oacute;n:</td>
		<td colspan="3">
        	<input type="text" name="Descripcion" id="Descripcion" value="<?=$field['Descripcion']?>" style="width:95%;" maxlength="255" <?=$disabled_ver?> />
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Clasificaci&oacute;n:</td>
		<td>
			<select name="CodClasificacion" id="CodClasificacion" style="width:125px;" <?=$disabled_ver?>>
				<?=getMiscelaneos($field['CodClasificacion'], "CLASIFSERV", 0)?>
			</select>
		</td>
		<td class="tagForm">* Precio Venta:</td>
		<td>
        	<input type="text" name="PrecioVenta" id="PrecioVenta" value="<?=number_format($field['PrecioVenta'],2,',','.')?>" style="width:125px; text-align: right;" class="currency" <?=$disabled_ver?> />
		</td>
	</tr>
	<tr>
		<td class="tagForm">Partida:</td>
		<td class="gallery clearfix">
			<input type="text" name="PartidaIngreso" id="PartidaIngreso" value="<?=$field['PartidaIngreso']?>" style="width:125px;" readonly />
			<a href="../lib/listas/gehen.php?anz=lista_partidas&filtrar=default&campo1=PartidaIngreso&campo2=CodCuentaOncop&campo3=CodCuentaPub20&ventana=cuentas&FlagTipoCuenta=S&fcod_tipocuenta=3&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe2]" style=" <?=$display_ver?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
		</td>
		<td class="tagForm">&nbsp;</td>
		<td>
            <input type="checkbox" name="FlagExoneradoIva" id="FlagExoneradoIva" value="S" <?=chkOpt($field['FlagExoneradoIva'], "S");?> <?=$disabled_ver?> /> Exonerado IVA
		</td>
	</tr>
	<tr>
		<td class="tagForm">Cuenta:</td>
		<td class="gallery clearfix">
			<input type="text" name="CodCuentaOncop" id="CodCuentaOncop" value="<?=$field['CodCuentaOncop']?>" style="width:125px;" readonly />
			<a href="../lib/listas/gehen.php?anz=lista_plan_cuentas&filtrar=default&campo1=CodCuentaOncop&ventana=&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe3]" style=" <?=$display_ver?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
		</td>
		<td class="tagForm">&nbsp;</td>
		<td>
            <input type="checkbox" name="FlagAfectoDescuento" id="FlagAfectoDescuento" value="S" <?=chkOpt($field['FlagAfectoDescuento'], "S");?> <?=$disabled_ver?> /> Afecto Descuento
		</td>
	</tr>
	<tr>
		<td class="tagForm">Cuenta (Pub.20):</td>
		<td class="gallery clearfix" colspan="3">
			<input type="text" name="CodCuentaPub20" id="CodCuentaPub20" value="<?=$field['CodCuentaPub20']?>" style="width:125px;" readonly />
			<a href="../lib/listas/gehen.php?anz=lista_plan_cuentas20&filtrar=default&campo1=CodCuentaPub20&ventana=&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe4]" style=" <?=$display_ver?>">
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
			<input type="text" value="<?=$field['UltimoUsuario']?>" style="width:125px;" disabled="disabled" />
			<input type="text" value="<?=$field['UltimaFecha']?>" style="width:125px" disabled="disabled" />
		</td>
	</tr>
</table>

<center>
<input type="submit" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" id="btSubmit" />
<input type="button" value="Cancelar" style="width:75px;" id="btCancelar" onclick="<?=$clkCancelar?>" />
</center>
</form>
<div style="width:<?=$_width?>px" class="divMsj">Campos Obligatorios *</div>