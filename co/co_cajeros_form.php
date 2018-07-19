<?php
if ($opcion == "nuevo") {
	$field['Estado'] = 'A';
	##
	$_titulo = "Cajeros / Nuevo Registro";
	$accion = "nuevo";
	$disabled_modificar = "";
	$disabled_ver = "";
	$disabled_vendedor = "disabled";
	$read_modificar = "";
	$display_modificar = "";
	$display_ver = "";
	$display_submit = "";
	$label_submit = "Guardar";
	$focus = "CodPersona";
}
elseif ($opcion == "modificar" || $opcion == "ver") {
	##	consulto datos generales
	$sql = "SELECT
				c.*,
				p.NomCompleto AS NomPersona,
				p.Ndocumento
			FROM co_cajeros c
			INNER JOIN mastpersonas p ON p.CodPersona = c.CodPersona
			WHERE c.CodCajero = '$sel_registros'";
	$field = getRecord($sql);
	##
	if ($opcion == "modificar") {
		$_titulo = "Cajeros / Modificar Registro";
		$accion = "modificar";
		$disabled_modificar = "disabled";
		$disabled_ver = "";
		$disabled_vendedor = (($field['FlagVendedor'] != 'S')?'disabled':'');
		$read_modificar = "readonly";
		$display_modificar = "display:none;";
		$display_ver = "";
		$display_submit = "";
		$label_submit = "Modificar";
		$focus = "EquipoVenta";
	}
	##
	elseif ($opcion == "ver") {
		$_titulo = "Cajeros / Ver Registro";
		$accion = "";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$disabled_vendedor = "disabled";
		$read_modificar = "readonly";
		$display_modificar = "display:none;";
		$display_ver = "display:none;";
		$display_submit = "display:none;";
		$label_submit = "";
		$focus = "btCancelar";
	}
}
$clkCancelar = "document.getElementById('frmentrada').submit();";
//	------------------------------------
$_width = 500;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=co_cajeros_lista" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('co_cajeros_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
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
			<td class="tagForm">C&oacute;digo:</td>
			<td>
	        	<input type="text" name="CodCajero" id="CodCajero" value="<?=$field['CodCajero']?>" style="width:75px; font-weight:bold;" readonly />
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Persona:</td>
			<td class="gallery clearfix">
				<input type="text" name="CodPersona" id="CodPersona" value="<?=$field['CodPersona']?>" style="width:75px;" readonly />
				<input type="text" name="NomPersona" id="NomPersona" value="<?=$field['NomPersona']?>" style="width:225px;" disabled />
				<a href="../lib/listas/gehen.php?anz=lista_personas&campo1=CodPersona&campo2=NomPersona&campo3=CodPersonaVendedor&campo4=FechaIngreso&ventana=co_cajeros&filtrar=default&FlagClasePersona=S&fEsEmpleado=S&fEsOtros=S&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" style=" <?=$display_modificar?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
		</tr>
		<tr>
			<td class="tagForm">Fecha Ingreso:</td>
			<td>
				<input type="text" name="FechaIngreso" id="FechaIngreso" value="<?=formatFechaDMA($field['FechaIngreso'])?>" maxlength="10" style="width:75px;" class="datepicker" onkeyup="setFechaDMA(this);" <?=$disabled_ver?> />
			</td>
		</tr>
	    <tr>
			<td class="tagForm">&nbsp;</td>
			<td>
	            <input type="checkbox" name="FlagSupervisor" id="FlagSupervisor" value="S" <?=chkOpt($field['FlagSupervisor'], "S");?> <?=$disabled_ver?> /> Supervisor
			</td>
		</tr>
	    <tr>
			<td class="tagForm">&nbsp;</td>
			<td>
	            <input type="checkbox" name="FlagVendedor" id="FlagVendedor" value="S" <?=chkOpt($field['FlagVendedor'], "S");?> <?=$disabled_ver?> onclick="$('#CodPersonaVendedor').prop('disabled', !checked).val('');" /> Vendedor
			</td>
		</tr>
		<tr>
			<td class="tagForm">Vendedor:</td>
			<td>
				<select name="CodPersonaVendedor" id="CodPersonaVendedor" style="width:304px;" <?=$disabled_vendedor?>>
					<option value="">&nbsp;</option>
					<?=vendedores($field['CodPersonaVendedor'])?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="tagForm">Estado:</td>
			<td>
	            <input type="radio" name="Estado" id="Activo" value="A" <?=chkOpt($field['Estado'], "A");?> <?=$disabled_ver?> /> Activo
	            &nbsp; &nbsp;
	            <input type="radio" name="Estado" id="Inactivo" value="I" <?=chkOpt($field['Estado'], "I");?> <?=$disabled_ver?> /> Inactivo
			</td>
		</tr>
	    <tr>
			<td class="tagForm">&Uacute;ltima Modif.:</td>
			<td>
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