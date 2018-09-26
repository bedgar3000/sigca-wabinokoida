<?php
if ($opcion == "nuevo") {
	$field['Estado'] = 'A';
	##
	$_titulo = "Nuevo Registro";
	$accion = "nuevo";
	$disabled_ver = "";
	$display_ver = "";
	$display_submit = "";
	$label_submit = "Guardar";
	$focus = "Descripcion";
}
elseif ($opcion == "modificar" || $opcion == "ver") {
	##	consulto datos generales
	$sql = "SELECT cc.*
			FROM ap_conceptoscertificacion cc
			WHERE cc.CodConcepto = '".$sel_registros."'";
	$field = getRecord($sql);
	##
	if ($opcion == "modificar") {
		$_titulo = "Modificar Registro";
		$accion = "modificar";
		$disabled_ver = "";
		$display_ver = "";
		$display_submit = "";
		$label_submit = "Modificar";
		$focus = "Descripcion";
	}
	##
	elseif ($opcion == "ver") {
		$_titulo = "Ver Registro";
		$accion = "";
		$disabled_ver = "disabled";
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

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=ap_conceptoscertificacion_lista" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('ap_conceptoscertificacion_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
<input type="hidden" name="fCodTipoDocumento" id="fCodTipoDocumento" value="<?=$fCodTipoDocumento?>" />

<table width="<?=$_width?>" class="tblForm">
	<tr>
    	<td colspan="2" class="divFormCaption">Datos Generales</td>
    </tr>
    <tr>
		<td class="tagForm" width="125">C&oacute;digo.:</td>
		<td>
        	<input type="text" name="CodConcepto" id="CodConcepto" value="<?=$field['CodConcepto']?>" style="width:50px; font-weight:bold;" readonly="readonly" />
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Descripci&oacute;n:</td>
		<td>
        	<input type="text" name="Descripcion" id="Descripcion" value="<?=$field['Descripcion']?>" style="width:300px;" maxlength="255" <?=$disabled_ver?> />
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Categoria:</td>
		<td>
			<select name="Categoria" id="Categoria" style="width:100px;" <?=$disabled_ver?>>
				<option value="">&nbsp;</option>
				<?=getMiscelaneos($field['Categoria'],'CATCERTIF')?>
			</select>
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Partida:</td>
		<td colspan="3" class="gallery clearfix">
			<input type="text" name="cod_partida" id="cod_partida" value="<?=$field['cod_partida']?>" style="width:100px;" readonly="readonly" />
			<a href="../lib/listas/gehen.php?anz=lista_partidas&filtrar=default&campo1=cod_partida&campo2=CodCuenta&campo3=CodCuentaPub20&fcod_tipocuenta=4&ventana=cuentas&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" id="acod_partida" style=" <?=$display_ver?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Cuenta (Pub.20):</td>
		<td colspan="3" class="gallery clearfix">
			<input type="text" name="CodCuentaPub20" id="CodCuentaPub20" value="<?=$field['CodCuentaPub20']?>" style="width:100px;" readonly="readonly" />
			<a href="../lib/listas/gehen.php?anz=lista_plan_cuentas20&filtrar=default&campo1=CodCuentaPub20&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe2]" id="aCodCuentaPub20" style=" <?=$display_ver?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Cuenta:</td>
		<td colspan="3" class="gallery clearfix">
			<input type="text" name="CodCuenta" id="CodCuenta" value="<?=$field['CodCuenta']?>" style="width:100px;" readonly="readonly" />
			<a href="../lib/listas/gehen.php?anz=lista_plan_cuentas&filtrar=default&campo1=CodCuenta&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe3]" id="aCodCuenta" style=" <?=$display_ver?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
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
			<input type="text" value="<?=$field['UltimoUsuario']?>" style="width:150px;" disabled="disabled" />
			<input type="text" value="<?=$field['UltimaFecha']?>" style="width:110px" disabled="disabled" />
		</td>
	</tr>
</table>

<center>
<input type="submit" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" id="btSubmit" />
<input type="button" value="Cancelar" style="width:75px;" id="btCancelar" onclick="<?=$clkCancelar?>" />
</center>
</form>
<div style="width:<?=$_width?>px" class="divMsj">Campos Obligatorios *</div>