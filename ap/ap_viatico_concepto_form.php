<?php
if ($opcion == "nuevo") {
	$field['Estado'] = "A";
	##
	$titulo = "Nuevo Registro";
	$accion = "nuevo";
	$label_submit = "Guardar";
	$disabled_nuevo = "disabled";
	$clkCancelar = "document.getElementById('frmentrada').submit();";
	$focus = "Descripcion";
}
elseif ($opcion == "modificar" || $opcion == "ver") {
	//	consulto datos generales
	$sql = "SELECT * FROM ap_conceptogastoviatico WHERE CodConcepto = '".$sel_registros."'";
	$field = getRecord($sql);
	
	if ($opcion == "modificar") {
		$titulo = "Modificar Registro";
		$accion = "modificar";
		$disabled_modificar = "disabled";
		$display_modificar = "display:none;";
		$display_ver = "";
		$label_submit = "Modificar";
		$clkCancelar = "document.getElementById('frmentrada').submit();";
		$focus = "Descripcion";
	}
	
	elseif ($opcion == "ver") {
		$titulo = "Ver Registro";
		$disabled_nuevo = "disabled";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$display_modificar = "display:none;";
		$display_ver = "display:none;";
		$display_submit = "display:none;";
		$clkCancelar = "document.getElementById('frmentrada').submit();";
	}
}
//	------------------------------------
$_width = 500;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=ap_viatico_concepto_lista" method="POST" enctype="multipart/form-data" onsubmit="return form(this, '<?=$accion?>');" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
<input type="hidden" name="fArticulo" id="fArticulo" value="<?=$fArticulo?>" />
<input type="hidden" name="fCategoria" id="fCategoria" value="<?=$fCategoria?>" />

<table width="<?=$_width?>" class="tblForm">
	<tr>
    	<td colspan="2" class="divFormCaption">Datos del Registro</td>
    </tr>
	<tr>
		<td class="tagForm">Concepto:</td>
		<td>
        	<input type="text" id="CodConcepto" value="<?=$field['CodConcepto']?>" style="width:50px;" disabled />
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Descripci&oacute;n:</td>
		<td>
        	<input type="text" id="Descripcion" value="<?=$field['Descripcion']?>" style="width:315px;" maxlength="255" <?=$disabled_ver?> />
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Categor&iacute;a:</td>
		<td>
            <select id="Categoria" style="width:200px;" <?=$disabled_ver?>>
                <option value="">&nbsp;</option>
                <?=getMiscelaneos($field['Categoria'], "CATVIAT", 0)?>
            </select>
		</td>
	</tr>
	<tr>
		<td class="tagForm">Art&iacute;culo:</td>
		<td>
            <select id="Articulo" style="width:200px;" <?=$disabled_ver?>>
                <option value="">&nbsp;</option>
                <?=getMiscelaneos($field['Articulo'], "ARTVIAT", 0)?>
            </select>
		</td>
	</tr>
	<tr>
		<td class="tagForm">Numeral:</td>
		<td>
        	<input type="text" id="Numeral" value="<?=$field['Numeral']?>" style="width:50px;" maxlength="2" <?=$disabled_ver?> />
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Valor UV:</td>
		<td>
        	<input type="text" id="ValorUT" value="<?=number_format($field['ValorUT'], 2, ',', '.')?>" style="width:93px; text-align:right;" class="currency" <?=$disabled_ver?> />
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Partida:</td>
		<td class="gallery clearfix">
			<input type="text" id="cod_partida" style="width:93px;" value="<?=$field['cod_partida']?>" disabled="disabled" />
            <a href="../lib/listas/listado_clasificador_presupuestario.php?filtrar=default&cod=cod_partida&campo3=CodCuenta&campo4=CodCuentaPub20&ventana=partida_cuentas&iframe=true&width=950&height=525" rel="prettyPhoto[iframe1]" style=" <?=$display_ver?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
		</td>
    </tr>
	<tr>
		<td class="tagForm">Cuenta Contable:</td>
		<td class="gallery clearfix">
			<input type="text" id="CodCuenta" style="width:93px;" value="<?=$field['CodCuenta']?>" disabled="disabled" />
            <a href="../lib/listas/listado_plan_cuentas.php?filtrar=default&cod=CodCuenta&iframe=true&width=950&height=525" rel="prettyPhoto[iframe2]" style=" <?=$display_ver?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
		</td>
    </tr>
	<tr>
		<td class="tagForm">Cuenta Pub. 20:</td>
		<td class="gallery clearfix">
			<input type="text" id="CodCuentaPub20" style="width:93px;" value="<?=$field['CodCuentaPub20']?>" disabled="disabled" />
            <a href="../lib/listas/listado_plan_cuentas_pub20.php?filtrar=default&cod=CodCuentaPub20&iframe=true&width=950&height=525" rel="prettyPhoto[iframe3]" style=" <?=$display_ver?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
		</td>
	</tr>
	<tr>
		<td class="tagForm">&nbsp;</td>
		<td>
            <input type="checkbox" name="FlagMonto" id="FlagMonto" value="S" <?=chkOpt($field['FlagMonto'], "S");?> <?=$disabled_ver?> onClick="$('#ValorUT').prop('disabled', $(this).prop('checked')).val('0,00');" /> Por Monto Directo
		</td>
	</tr>
	<tr>
		<td class="tagForm">&nbsp;</td>
		<td>
            <input type="checkbox" name="FlagCantidad" id="FlagCantidad" value="S" <?=chkOpt($field['FlagCantidad'], "S");?> <?=$disabled_ver?> /> Multiplicar Cantidad
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

<script type="text/javascript" language="javascript">
function form(form, accion) {
	bloqueo(true);
	//	valido
	var error = "";
	if ($("#Descripcion").val().trim() == "" || $("#Categoria").val().trim() == "" || $("#cod_partida").val().trim() == "") error = "Debe llenar los campos obligatorios";
	
	//	valido errores
	if (error != "") {
		cajaModal(error, "error", 400);
	} else {
		//	formulario
		var post = getForm(form);
		//	ajax
		$.ajax({
			type: "POST",
			url: "ap_viatico_concepto_ajax.php",
			data: "modulo=viatico_concepto&accion="+accion+"&"+post,
			async: false,
			success: function(resp) {
				if (resp.trim() != "") cajaModal(resp, "error", 400);
				else form.submit();
			}
		});
	}
	return false;
}
</script>