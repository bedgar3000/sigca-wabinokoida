<?php
if ($opcion == "nuevo") {
	$field['Estado'] = 'A';
	##
	$_titulo = "Familias / Nuevo Registro";
	$accion = "nuevo";
	$disabled_modificar = "";
	$disabled_ver = "";
	$read_modificar = "";
	$display_modificar = "";
	$display_ver = "";
	$display_submit = "";
	$label_submit = "Guardar";
	$focus = "Descripcion";
}
elseif ($opcion == "modificar" || $opcion == "ver") {
	list($CodLinea, $CodFamilia) = explode('_', $sel_registros);
	##	consulto datos generales
	$sql = "SELECT *
			FROM lg_clasefamilia
			WHERE
				CodLinea = '$CodLinea'
				AND CodFamilia = '$CodFamilia'";
	$field = getRecord($sql);
	##
	if ($opcion == "modificar") {
		$_titulo = "Familias / Modificar Registro";
		$accion = "modificar";
		$disabled_modificar = "disabled";
		$disabled_ver = "";
		$read_modificar = "readonly";
		$display_modificar = "display:none;";
		$display_ver = "";
		$display_submit = "";
		$label_submit = "Modificar";
		$focus = "Descripcion";
	}
	##
	elseif ($opcion == "ver") {
		$_titulo = "Familias / Ver Registro";
		$accion = "";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
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
$_width = 700;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lg_clasefamilia_lista" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('lg_clasefamilia_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
	<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
	<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
	<input type="hidden" name="fCodLinea" id="fCodLinea" value="<?=$fCodLinea?>" />

	<table style="width:100%; max-width:<?=$_width?>px;" class="tblForm">
		<tr>
	    	<td colspan="4" class="divFormCaption">DATOS GENERALES</td>
	    </tr>
	    <tr>
			<td class="tagForm">* C&oacute;digo:</td>
			<td colspan="3">
	        	<input type="text" name="CodFamilia" id="CodFamilia" value="<?=$field['CodFamilia']?>" style="width:75px; font-weight:bold;" maxlength="6" readonly />
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Linea:</td>
			<td colspan="3">
				<select name="CodLinea" id="CodLinea" style="width:95%;" <?=$disabled_modificar?>>
					<option value="">&nbsp;</option>
					<?=loadSelect2('lg_claselinea','CodLinea','Descripcion',$field['CodLinea'],10)?>
				</select>
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Descripci&oacute;n:</td>
			<td colspan="3">
	        	<input type="text" name="Descripcion" id="Descripcion" value="<?=$field['Descripcion']?>" style="width:95%;" maxlength="255" <?=$disabled_ver?> />
			</td>
		</tr>
		<tr>
	    	<td colspan="4" class="divFormCaption">INFORMACIÓN CONTABLE</td>
	    </tr>
		<tr>
			<td class="tagForm">Cuenta. Inventario:</td>
			<td class="gallery clearfix">
	        	<input type="text" name="CuentaInventario" id="CuentaInventario" readonly="readonly" style="width:100px;" value="<?=$field['CuentaInventario']?>" />
				<a href="../lib/listas/gehen.php?anz=lista_plan_cuentas&filtrar=default&campo1=CuentaInventario&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe2]" style=" <?=$display_ver?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
			<td class="tagForm">Cuenta. Inventario (Pub.20):</td>
			<td class="gallery clearfix">
	        	<input type="text" name="CuentaInventarioPub20"  id="CuentaInventarioPub20" readonly="readonly" style="width:100px;" value="<?=$field['CuentaInventarioPub20']?>" />
				<a href="../lib/listas/gehen.php?anz=lista_plan_cuentas20&filtrar=default&campo1=CuentaInventarioPub20&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe3]" style=" <?=$display_ver?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
		</tr>
		<tr>
			<td class="tagForm">Cuenta. Gasto:</td>
			<td class="gallery clearfix">
	        	<input type="text" name="CuentaGasto" id="CuentaGasto" readonly="readonly" style="width:100px;" value="<?=$field['CuentaGasto']?>" />
				<a href="../lib/listas/gehen.php?anz=lista_plan_cuentas&filtrar=default&campo1=CuentaGasto&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe4]" style=" <?=$display_ver?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
			<td class="tagForm">Cuenta. Gasto (Pub.20):</td>
			<td class="gallery clearfix">
	        	<input type="text" name="CuentaGastoPub20" id="CuentaGastoPub20" readonly="readonly" style="width:100px;" value="<?=$field['CuentaGastoPub20']?>" />
				<a href="../lib/listas/gehen.php?anz=lista_plan_cuentas20&filtrar=default&campo1=CuentaGastoPub20&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe5]" style=" <?=$display_ver?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
		</tr>
		<tr>
			<td class="tagForm">Cuenta. Venta:</td>
			<td class="gallery clearfix">
	        	<input type="text" name="CuentaVentas" id="CuentaVentas" readonly="readonly" style="width:100px;" value="<?=$field['CuentaVentas']?>" />
				<a href="../lib/listas/gehen.php?anz=lista_plan_cuentas&filtrar=default&campo1=CuentaVentas&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe6]" style=" <?=$display_ver?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
			<td class="tagForm">Cuenta. Venta (Pub.20):</td>
			<td class="gallery clearfix">
	        	<input type="text" name="CuentaVentasPub20" id="CuentaVentasPub20" readonly="readonly" style="width:100px;" value="<?=$field['CuentaVentasPub20']?>" />
				<a href="../lib/listas/gehen.php?anz=lista_plan_cuentas20&filtrar=default&campo1=CuentaVentasPub20&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe7]" style=" <?=$display_ver?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
		</tr>
		<tr>
			<td class="tagForm">Cuenta. Transito:</td>
			<td class="gallery clearfix">
	        	<input type="text" name="CuentaTransito" id="CuentaTransito" readonly="readonly" style="width:100px;" value="<?=$field['CuentaTransito']?>" />
				<a href="../lib/listas/gehen.php?anz=lista_plan_cuentas&filtrar=default&campo1=CuentaTransito&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe8]" style=" <?=$display_ver?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
			<td class="tagForm">Cuenta. Transito (Pub.20):</td>
			<td class="gallery clearfix">
	        	<input type="text" name="CuentaTransitoPub20" id="CuentaTransitoPub20" readonly="readonly" style="width:100px;" value="<?=$field['CuentaTransitoPub20']?>" />
				<a href="../lib/listas/gehen.php?anz=lista_plan_cuentas20&filtrar=default&campo1=CuentaTransitoPub20&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe9]" style=" <?=$display_ver?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
		</tr>
		<tr>
			<td class="tagForm">Partida Presupuestaria:</td>
			<td class="gallery clearfix" colspan="3">
	        	<input type="text" name="PartidaPresupuestal" id="PartidaPresupuestal" readonly="readonly" style="width:100px;" value="<?=$field['PartidaPresupuestal']?>" />
				<a href="../lib/listas/gehen.php?anz=lista_partidas&campo1=PartidaPresupuestal&filtrar=default&ventana=&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe10]" style=" <?=$display_ver?>">
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