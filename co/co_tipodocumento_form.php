<?php
if ($opcion == "nuevo") {
	$field['Estado'] = 'A';
	##
	$_titulo = "Tipos de Documento / Nuevo Registro";
	$accion = "nuevo";
	$disabled_ver = "";
	$read_modificar = "";
	$display_ver = "";
	$display_submit = "";
	$label_submit = "Guardar";
	$focus = "CodTipoDocumento";
}
elseif ($opcion == "modificar" || $opcion == "ver") {
	##	consulto datos generales
	$sql = "SELECT *
			FROM co_tipodocumento
			WHERE CodTipoDocumento = '$sel_registros'";
	$field = getRecord($sql);
	##
	if ($opcion == "modificar") {
		$_titulo = "Tipos de Documento / Modificar Registro";
		$accion = "modificar";
		$disabled_ver = "";
		$read_modificar = "readonly";
		$display_ver = "";
		$display_submit = "";
		$label_submit = "Modificar";
		$focus = "Descripcion";
	}
	##
	elseif ($opcion == "ver") {
		$_titulo = "Tipos de Documento / Ver Registro";
		$accion = "";
		$disabled_ver = "disabled";
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

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=co_tipodocumento_lista" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('co_tipodocumento_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
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
			<td class="tagForm" width="175">C&oacute;digo:</td>
			<td>
	        	<input type="text" name="CodTipoDocumento" id="CodTipoDocumento" value="<?=$field['CodTipoDocumento']?>" style="width:145px; font-weight:bold;" maxlength="3" <?=$read_modificar?> />
			</td>
			<td class="tagForm">&nbsp;</td>
			<td>
	            <input type="checkbox" name="FlagProvision" id="FlagProvision" value="S" <?=chkOpt($field['FlagProvision'], "S");?> <?=$disabled_ver?> /> Provisión
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Clasificaci&oacute;n:</td>
			<td>
				<select name="CodClasificacion" id="CodClasificacion" style="width:145px;" <?=$disabled_ver?>>
					<option value="">&nbsp;</option>
					<?=getMiscelaneos($field['CodClasificacion'], "COCLASDOC", 0)?>
				</select>
			</td>
			<td class="tagForm">&nbsp;</td>
			<td>
	            <input type="checkbox" name="FlagEsFiscal" id="FlagEsFiscal" value="S" <?=chkOpt($field['FlagEsFiscal'], "S");?> <?=$disabled_ver?> /> Es Fiscal
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Descripci&oacute;n:</td>
			<td colspan="3">
	        	<input type="text" name="Descripcion" id="Descripcion" value="<?=$field['Descripcion']?>" style="width:95%;" maxlength="255" <?=$disabled_ver?> />
			</td>
		</tr>
		<tr>
			<td class="tagForm">Cuenta (ONCOP):</td>
			<td class="gallery clearfix">
				<input type="text" name="CodCuentaOncop" id="CodCuentaOncop" value="<?=$field['CodCuentaOncop']?>" style="width:145px;" readonly />
				<a href="../lib/listas/gehen.php?anz=lista_plan_cuentas&filtrar=default&campo1=CodCuentaOncop&ventana=&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" style=" <?=$display_ver?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
			<td class="tagForm">Cuenta (Pub.20):</td>
			<td class="gallery clearfix">
				<input type="text" name="CodCuentaPub20" id="CodCuentaPub20" value="<?=$field['CodCuentaPub20']?>" style="width:145px;" readonly />
				<a href="../lib/listas/gehen.php?anz=lista_plan_cuentas20&filtrar=default&campo1=CodCuentaPub20&ventana=&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe2]" style=" <?=$display_ver?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
		</tr>
		<tr>
			<td class="tagForm">Cuenta Dudosa (ONCOP):</td>
			<td class="gallery clearfix">
				<input type="text" name="CodCuentaDudosaOncop" id="CodCuentaDudosaOncop" value="<?=$field['CodCuentaDudosaOncop']?>" style="width:145px;" readonly />
				<a href="../lib/listas/gehen.php?anz=lista_plan_cuentas&filtrar=default&campo1=CodCuentaDudosaOncop&ventana=&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe3]" style=" <?=$display_ver?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
			<td class="tagForm">Cuenta Dudosa (Pub.20):</td>
			<td class="gallery clearfix">
				<input type="text" name="CodCuentaDudosaPub20" id="CodCuentaDudosaPub20" value="<?=$field['CodCuentaDudosaPub20']?>" style="width:145px;" readonly />
				<a href="../lib/listas/gehen.php?anz=lista_plan_cuentas20&filtrar=default&campo1=CodCuentaDudosaPub20&ventana=&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe4]" style=" <?=$display_ver?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
		</tr>
		<tr>
			<td class="tagForm">Cuenta Provisión (ONCOP):</td>
			<td class="gallery clearfix">
				<input type="text" name="CodCuentaProvOncop" id="CodCuentaProvOncop" value="<?=$field['CodCuentaProvOncop']?>" style="width:145px;" readonly />
				<a href="../lib/listas/gehen.php?anz=lista_plan_cuentas&filtrar=default&campo1=CodCuentaProvOncop&ventana=&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe5]" style=" <?=$display_ver?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
			<td class="tagForm">Cuenta Provisión (Pub.20):</td>
			<td class="gallery clearfix">
				<input type="text" name="CodCuentaProvPub20" id="CodCuentaProvPub20" value="<?=$field['CodCuentaProvPub20']?>" style="width:145px;" readonly />
				<a href="../lib/listas/gehen.php?anz=lista_plan_cuentas20&filtrar=default&campo1=CodCuentaProvPub20&ventana=&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe6]" style=" <?=$display_ver?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
		</tr>
		<tr>
			<td class="tagForm">Cuenta Adelanto (ONCOP):</td>
			<td class="gallery clearfix">
				<input type="text" name="CodCuentaAdeOncop" id="CodCuentaAdeOncop" value="<?=$field['CodCuentaAdeOncop']?>" style="width:145px;" readonly />
				<a href="../lib/listas/gehen.php?anz=lista_plan_cuentas&filtrar=default&campo1=CodCuentaAdeOncop&ventana=&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe7]" style=" <?=$display_ver?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
			<td class="tagForm">Cuenta Adelanto (Pub.20):</td>
			<td class="gallery clearfix">
				<input type="text" name="CodCuentaAdePub20" id="CodCuentaAdePub20" value="<?=$field['CodCuentaAdePub20']?>" style="width:145px;" readonly />
				<a href="../lib/listas/gehen.php?anz=lista_plan_cuentas20&filtrar=default&campo1=CodCuentaAdePub20&ventana=&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe8]" style=" <?=$display_ver?>">
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