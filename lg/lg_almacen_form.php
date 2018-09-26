<?php
if ($opcion == "nuevo") {
	$field['CodOrganismo'] = $_SESSION["ORGANISMO_ACTUAL"];
	$field['Estado'] = 'A';
	##
	$_titulo = "Almacenes / Nuevo Registro";
	$accion = "nuevo";
	$disabled_ver = "";
	$disabled_transito = "disabled";
	$read_modificar = "";
	$display_ver = "";
	$display_submit = "";
	$label_submit = "Guardar";
	$focus = "CodAlmacen";
}
elseif ($opcion == "modificar" || $opcion == "ver") {
	##	consulto datos generales
	$sql = "SELECT
				a.*,
				p.NomCompleto AS NomPersona
			FROM lg_almacenmast a
			LEFT JOIN mastpersonas p ON p.CodPersona = a.CodPersona
			WHERE a.CodAlmacen = '$sel_registros'";
	$field = getRecord($sql);
	##
	if ($opcion == "modificar") {
		$_titulo = "Almacenes / Modificar Registro";
		$accion = "modificar";
		$disabled_ver = "";
		$disabled_transito = (($field['TipoAlmacen'] != 'T') ? "disabled" : "");
		$read_modificar = "readonly";
		$display_ver = "";
		$display_submit = "";
		$label_submit = "Modificar";
		$focus = "Descripcion";
	}
	##
	elseif ($opcion == "ver") {
		$_titulo = "Almacenes / Ver Registro";
		$accion = "";
		$disabled_ver = "disabled";
		$disabled_transito = "disabled";
		$read_modificar = "readonly";
		$display_ver = "display:none;";
		$display_submit = "display:none;";
		$label_submit = "";
		$focus = "btCancelar";
	}
}
$clkCancelar = "document.getElementById('frmentrada').submit();";
//	------------------------------------
$_width = 900;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lg_almacen_lista" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('lg_almacen_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
	<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
	<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />

	<table style="width:100%; max-width:<?=$_width?>px;" class="tblForm">
		<tr>
	    	<td colspan="4" class="divFormCaption">Datos Generales</td>
	    </tr>
	    <tr>
			<td class="tagForm">* Código:</td>
			<td>
	        	<input type="text" name="CodAlmacen" id="CodAlmacen" value="<?=$field['CodAlmacen']?>" style="width:145px; font-weight:bold;" maxlength="6" <?=$read_modificar?> />
			</td>
			<td class="tagForm">* Tipo de Almacén:</td>
			<td>
				<select name="TipoAlmacen" id="TipoAlmacen" style="width:145px;" <?=$disabled_ver?> onchange="setTipoAlmacen(this.value);">
					<option value="">&nbsp;</option>
					<?=loadSelectValores('almacen-tipo',$field['TipoAlmacen'])?>
				</select>
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Descripción:</td>
			<td>
	        	<input type="text" name="Descripcion" id="Descripcion" value="<?=$field['Descripcion']?>" style="width:304px;" maxlength="255" <?=$disabled_ver?> />
			</td>
			<td class="tagForm">* Almacén Transito:</td>
			<td>
				<select name="AlmacenTransito" id="AlmacenTransito" style="width:145px;" <?=$disabled_transito?>>
					<option value="">&nbsp;</option>
					<?=loadSelect2('lg_almacenmast','CodAlmacen','CodAlmacen',$field['AlmacenTransito'],0,['TipoAlmacen'],['P'])?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Organismo:</td>
			<td>
				<select name="CodOrganismo" id="CodOrganismo" style="width:304px;" <?=$disabled_ver?> onChange=" loadSelect($('#fCodDependencia'), 'tabla=mastdependencias&CodOrganismo='+$(this).val(), 1);">
					<?=loadSelect2('mastorganismos','CodOrganismo','Organismo',$field['CodOrganismo'])?>
				</select>
			</td>
			<td class="tagForm">&nbsp;</td>
			<td>
	            <input type="checkbox" name="FlagProduccion" id="FlagProduccion" value="S" <?=chkOpt($field['FlagProduccion'], "S");?> <?=$disabled_ver?> /> Almacén de Producción
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Dependencia:</td>
			<td>
				<select name="CodDependencia" id="CodDependencia" style="width:304px;" <?=$disabled_ver?>>
					<option value="">&nbsp;</option>
					<?=loadSelect2('mastdependencias','CodDependencia','Dependencia',$field['CodDependencia'],0,['CodOrganismo'],[$field['CodOrganismo']])?>
				</select>
			</td>
			<td class="tagForm">&nbsp;</td>
			<td>
	            <input type="checkbox" name="FlagVenta" id="FlagVenta" value="S" <?=chkOpt($field['FlagVenta'], "S");?> <?=$disabled_ver?> /> Almacén de Venta
			</td>
		</tr>
		<tr>
			<td class="tagForm">Empleado:</td>
			<td class="gallery clearfix">
				<input type="text" name="CodPersona" id="CodPersona" value="<?=$field['CodPersona']?>" style="width:75px;" readonly />
				<input type="text" name="NomPersona" id="NomPersona" value="<?=$field['NomPersona']?>" style="width:225px;" disabled />
				<a href="../lib/listas/gehen.php?anz=lista_personas&campo1=CodPersona&campo2=NomPersona&ventana=&filtrar=default&FlagClasePersona=S&fEsEmpleado=S&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" style=" <?=$display_ver?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
			<td class="tagForm">&nbsp;</td>
			<td>
	            <input type="checkbox" name="FlagCommodity" id="FlagCommodity" value="S" <?=chkOpt($field['FlagCommodity'], "S");?> <?=$disabled_ver?> /> Almacén para Commodities
			</td>
		</tr>
		<tr>
			<td class="tagForm" rowspan="2">Dirección:</td>
			<td rowspan="2">
				<textarea name="Direccion" id="Direccion" style="width:304px;" <?=$disabled_ver?>><?=$field['Direccion']?></textarea>
			</td>
			<td class="tagForm">Cta. Inventario (ONCOP):</td>
			<td class="gallery clearfix">
				<input type="text" name="CuentaInventario" id="CuentaInventario" value="<?=$field['CuentaInventario']?>" style="width:145px;" readonly />
				<a href="../lib/listas/gehen.php?anz=lista_plan_cuentas&filtrar=default&campo1=CuentaInventario&ventana=&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe2]" style=" <?=$display_ver?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
		</tr>
		<tr>
			<td class="tagForm">Cta. Inventario (Pub.20):</td>
			<td class="gallery clearfix">
				<input type="text" name="CuentaInventarioPub20" id="CuentaInventarioPub20" value="<?=$field['CuentaInventarioPub20']?>" style="width:145px;" readonly />
				<a href="../lib/listas/gehen.php?anz=lista_plan_cuentas20&filtrar=default&campo1=CuentaInventarioPub20&ventana=&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe3]" style=" <?=$display_ver?>">
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

<script type="text/javascript">
	function setTipoAlmacen(TipoAlmacen) {
		if (TipoAlmacen == 'T') $('#AlmacenTransito').val('').prop('disabled',false);
		else $('#AlmacenTransito').val('').prop('disabled',true);
	}
</script>