<?php
if ($opcion == "nuevo") {
	$field['Estado'] = 'A';
	##
	$_titulo = "Item / Nuevo Registro";
	$accion = "nuevo";
	$disabled_modificar = "";
	$disabled_ver = "";
	$disabled_impuesto = "disabled";
	$read_modificar = "";
	$display_modificar = "";
	$display_ver = "";
	$display_submit = "";
	$label_submit = "Guardar";
	$focus = "CodInterno";
}
elseif ($opcion == "modificar" || $opcion == "ver") {
	##	consulto datos generales
	$sql = "SELECT *
			FROM lg_itemmast
			WHERE CodItem = '$sel_registros'";
	$field = getRecord($sql);
	##
	if ($opcion == "modificar") {
		$_titulo = "Item / Modificar Registro";
		$accion = "modificar";
		$disabled_modificar = "disabled";
		$disabled_ver = "";
		$disabled_impuesto = (($field['FlagImpuestoVentas'] != 'S')?'disabled':'');
		$read_modificar = "readonly";
		$display_modificar = "display:none;";
		$display_ver = "";
		$display_submit = "";
		$label_submit = "Modificar";
		$focus = "Descripcion";
	}
	##
	elseif ($opcion == "ver") {
		$_titulo = "Item / Ver Registro";
		$accion = "";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$disabled_impuesto = "disabled";
		$read_modificar = "readonly";
		$display_modificar = "display:none;";
		$display_ver = "display:none;";
		$display_submit = "display:none;";
		$label_submit = "";
		$focus = "btCancelar";
	}
}
if (empty($action)) $action = "gehen.php?anz=lg_item_lista";
$clkCancelar = "document.getElementById('frmentrada').submit();";
//	------------------------------------
$_width = 700;
?>
<?php
if (empty($selector))
{
	?>
	<table width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td class="titulo"><?=$_titulo?></td>
			<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
		</tr>
	</table><hr width="100%" color="#333333" />
	<?php
}
?>

<form name="frmentrada" id="frmentrada" action="<?=$action?>" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('lg_item_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
	<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
	<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
	<input type="hidden" name="fCodAlmacen" id="fCodAlmacen" value="<?=$fCodAlmacen?>" />
	<input type="hidden" name="fPrecioUnitarioD" id="fPrecioUnitarioD" value="<?=$fPrecioUnitarioD?>" />
	<input type="hidden" name="fPrecioUnitarioH" id="fPrecioUnitarioH" value="<?=$fPrecioUnitarioH?>" />
	<input type="hidden" name="fCodLinea" id="fCodLinea" value="<?=$fCodLinea?>" />
	<input type="hidden" name="fCodFamilia" id="fCodFamilia" value="<?=$fCodFamilia?>" />
	<input type="hidden" name="fCodSubFamilia" id="fCodSubFamilia" value="<?=$fCodSubFamilia?>" />
	<input type="hidden" name="fCodTipoItem" id="fCodTipoItem" value="<?=$fCodTipoItem?>" />
	<input type="hidden" name="fCodMarca" id="fCodMarca" value="<?=$fCodMarca?>" />

	<input type="hidden" name="campo1" id="campo1" value="<?=$campo1?>" />
	<input type="hidden" name="campo2" id="campo2" value="<?=$campo2?>" />
	<input type="hidden" name="campo3" id="campo3" value="<?=$campo3?>" />
	<input type="hidden" name="campo4" id="campo4" value="<?=$campo4?>" />
	<input type="hidden" name="campo5" id="campo5" value="<?=$campo5?>" />
	<input type="hidden" name="campo6" id="campo6" value="<?=$campo6?>" />
	<input type="hidden" name="campo7" id="campo7" value="<?=$campo7?>" />
	<input type="hidden" name="campo8" id="campo8" value="<?=$campo8?>" />
	<input type="hidden" name="campo9" id="campo9" value="<?=$campo9?>" />
	<input type="hidden" name="campo10" id="campo10" value="<?=$campo10?>" />
	<input type="hidden" name="ventana" id="ventana" value="<?=$ventana?>" />
	<input type="hidden" name="detalle" id="detalle" value="<?=$detalle?>" />
	<input type="hidden" name="url" id="url" value="<?=$url?>" />
	<input type="hidden" name="fFlagDisponible" id="fFlagDisponible" value="<?=$fFlagDisponible?>" />
	<input type="hidden" name="selector" id="selector" value="<?=$selector?>" />	

	<table style="width:100%; max-width:<?=$_width?>px;" align="center" cellpadding="0" cellspacing="0">
	    <tr>
	        <td>
	            <div class="header">
		            <ul id="tab">
			            <!-- CSS Tabs -->
			            <li id="li1" onclick="currentTab('tab', this);" class="current">
			            	<a href="#" onclick="mostrarTab('tab', 1, 3);">Datos Generales</a>
			            </li>
			            <li id="li2" onclick="currentTab('tab', this);">
			            	<a href="#" onclick="mostrarTab('tab', 2, 3);">Información Adicional</a>
			            </li>
			            <li id="li3" onclick="currentTab('tab', this);">
			            	<a href="#" onclick="mostrarTab('tab', 3, 3);">Otros Datos</a>
			            </li>
		            </ul>
	            </div>
	        </td>
	    </tr>
	</table>

	<div id="tab1" style="display:block;">
		<table style="width:100%; max-width:<?=$_width?>px;" class="tblForm">
			<tr>
		    	<td colspan="4" class="divFormCaption">DATOS GENERALES</td>
		    </tr>
		    <tr>
				<td class="tagForm">Item:</td>
				<td>
		        	<input type="text" name="CodItem" id="CodItem" value="<?=$field['CodItem']?>" style="width:125px; font-weight:bold;" readonly="readonly" />
				</td>
				<td class="tagForm">* Tipo de Item:</td>
				<td>
					<select name="CodTipoItem" id="CodTipoItem" style="width:200px;" <?=$disabled_ver?>>
						<option value="">&nbsp;</option>
						<?=loadSelect2('lg_tipoitem','CodTipoItem','Descripcion',$field['CodTipoItem'])?>
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
				<td class="tagForm">* Unidad de Medida:</td>
				<td>
					<select name="CodUnidad" id="CodUnidad" style="width:125px;" <?=$disabled_ver?>>
						<option value="">&nbsp;</option>
						<?=loadSelect("mastunidades", "CodUnidad", "Descripcion", $field['CodUnidad'], 0)?>
					</select>
				</td>
		        <td class="tagForm">* Linea:</td>
		        <td class="gallery clearfix">
		            <input type="text" name="CodLinea" id="CodLinea" style="width:125px;" value="<?=$field['CodLinea']?>" readonly />
		            <?php if (empty($selector)) { ?>
						<a href="../lib/listas/gehen.php?anz=lista_lg_clasesubfamilia&filtrar=default&campo1=CodLinea&campo2=CodFamilia&campo3=CodSubFamilia&campo4=CtaInventario&campo5=CtaGasto&campo6=CtaVenta&campo7=CtaTransito&campo8=CtaInventarioPub20&campo9=CtaGastoPub20&campo10=CtaVentaPub20&campo11=CtaTransitoPub20&campo12=PartidaPresupuestal&ventana=items&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" style=" <?=$display_ver?>">
		            		<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
		            	</a>
		            <?php } else { ?>
						<a href="javascript:" onClick="window.open('../lib/listas/gehen.php?anz=lista_lg_clasesubfamilia&filtrar=default&campo1=CodLinea&campo2=CodFamilia&campo3=CodSubFamilia&campo4=CtaInventario&campo5=CtaGasto&campo6=CtaVenta&campo7=CtaTransito&campo8=CtaInventarioPub20&campo9=CtaGastoPub20&campo10=CtaVentaPub20&campo11=CtaTransitoPub20&campo12=PartidaPresupuestal&ventana=items_window', 'lista_lg_clasesubfamilia', 'toolbar=no, menubar=no, location=no, scrollbars=yes, width=950, height=550');" style=" <?=$display_ver?>">
		            		<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
		            	</a>
		            <?php } ?>
		        </td>
			</tr>
			<tr>
				<td class="tagForm">* Unidad de Compra:</td>
				<td>
					<select name="CodUnidadComp" id="CodUnidadComp" style="width:125px;" <?=$disabled_ver?>>
						<option value="">&nbsp;</option>
						<?=loadSelect("mastunidades", "CodUnidad", "Descripcion", $field['CodUnidadComp'], 0)?>
					</select>
					<input type="text" name="CantidadComp" id="CantidadComp" style="width:50px; text-align: right;" maxlength="11" value="<?=$field['CantidadComp']?>" <?=$disabled_ver?> />
				</td>
		        <td class="tagForm">* Familia:</td>
		        <td>
		            <input type="text" name="CodFamilia" id="CodFamilia" style="width:125px;" value="<?=$field['CodFamilia']?>" readonly="readonly" />
		        </td>
			</tr>
			<tr>
				<td class="tagForm">* Unidad de Embalaje:</td>
				<td>
					<select name="CodUnidadEmb" id="CodUnidadEmb" style="width:125px;" <?=$disabled_ver?>>
						<option value="">&nbsp;</option>
						<?=loadSelect("mastunidades", "CodUnidad", "Descripcion", $field['CodUnidadEmb'], 0)?>
					</select>
					<input type="text" name="CantidadEmb" id="CantidadEmb" style="width:50px; text-align: right;" maxlength="11" value="<?=$field['CantidadEmb']?>" <?=$disabled_ver?> />
				</td>
		        <td class="tagForm">* Sub-Familia:</td>
		        <td>
		            <input type="text" name="CodSubFamilia" id="CodSubFamilia" style="width:125px;" value="<?=$field['CodSubFamilia']?>" readonly="readonly" />
		        </td>
			</tr>
			<tr>
		    	<td colspan="4" class="divFormCaption">Caracter&iacute;sticas</td>
		    </tr>
			<tr>
				<td class="tagForm">* Cod. Interno:</td>
				<td><input type="text" name="CodInterno" id="CodInterno" style="width:125px;" maxlength="10" value="<?=$field['CodInterno']?>" <?=$disabled_ver?> /></td>
				<td class="tagForm">Imagen del Item:</td>
				<td>
		        	<input type="text" name="Imagen" id="Imagen" style="width:200px;" maxlength="25" value="<?=$field['Imagen']?>" <?=$disabled_ver?> />
		        </td>
			</tr>
			<tr>
				<td class="tagForm">C&oacute;digo SNC:</td>
				<td><input type="text" name="CodigoSNC" id="CodigoSNC" style="width:125px;" maxlength="10" value="<?=$field['CodigoSNC']?>" <?=$disabled_ver?> /></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>
					<input type="checkbox" name="FlagLotes" id="FlagLotes" value="S" <?=chkFlag($field['FlagLotes'])?> <?=$disabled_ver?> /> Se maneja por lotes <br />
					<input type="checkbox" name="FlagItem" id="FlagItem" value="S" <?=chkFlag($field['FlagItem'])?> <?=$disabled_ver?> /> Considerado como KIT <br />
					<input type="checkbox" name="FlagKit" id="FlagKit" value="S" <?=chkFlag($field['FlagKit'])?> <?=$disabled_ver?> /> Tiene # de Serie x Item
				</td>
				<td>&nbsp;</td>
				<td>
					<input type="checkbox" name="FlagImpuestoVentas" id="FlagImpuestoVentas" value="S" <?=chkFlag($field['FlagImpuestoVentas'])?> <?=$disabled_ver?> onclick="$('#CodImpuesto').prop('disabled',!this.checked).val('');" /> Afecto Imp. Ventas <br />
					<input type="checkbox" name="FlagAuto" id="FlagAuto" value="S" <?=chkFlag($field['FlagAuto'])?> <?=$disabled_ver?> /> Auto-Requisicionamiento <br />
					<input type="checkbox" name="FlagDisponible" id="FlagDisponible" value="S" <?=chkFlag($field['FlagDisponible'])?> <?=$disabled_ver?> /> Disponible para Ventas
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
	</div>

	<div id="tab2" style="display:none;">
		<table style="width:100%; max-width:<?=$_width?>px;" class="tblForm">
			<tr>
		    	<td colspan="4" class="divFormCaption">Caracter&iacute;sticas</td>
		    </tr>
			<tr>
				<td class="tagForm">Marca:</td>
				<td colspan="3">
					<select name="CodMarca" id="CodMarca" style="width:200px;" <?=$disabled_ver?>>
						<option value="">&nbsp;</option>
						<?=loadSelect("lg_marcas", "CodMarca", "Descripcion", $field['CodMarca'], 0)?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="tagForm">Color:</td>
				<td>
					<select name="Color" id="Color" style="width:200px;" <?=$disabled_ver?>>
						<option value="">&nbsp;</option>
						<?=getMiscelaneos($field['Color'], "COLOR", 0)?>
					</select>
				</td>
				<td class="tagForm">Peso:</td>
				<td><input type="text" name="Peso" id="Peso" class="currency5" style="width:100px; text-align:right;" value="<?=number_format($field['Peso'],5,',','.')?>" <?=$disabled_ver?> /></td>
			</tr>
			<tr>
				<td class="tagForm">* Procedencia:</td>
				<td>
					<select name="CodProcedencia" id="CodProcedencia" style="width:200px;" <?=$disabled_ver?>>
						<?=loadSelect("lg_procedencias", "CodProcedencia", "Descripcion", $field['CodProcedencia'], 0)?>
					</select>
				</td>
				<td class="tagForm">Volumen:</td>
				<td><input type="text" name="Volumen" id="Volumen" class="currency5" style="width:100px; text-align:right;" value="<?=number_format($field['Volumen'],5,',','.')?>" <?=$disabled_ver?> /></td>
			</tr>
			<tr>
				<td class="tagForm">Cod. Barra:</td>
				<td><input type="text" name="CodBarra" id="CodBarra" style="width:200px;" maxlength="25" value="<?=$field['CodBarra']?>" <?=$disabled_ver?> /></td>
				<td class="tagForm">Dias Venc.:</td>
				<td><input type="text" name="DiasVencimiento" id="DiasVencimiento" style="width:100px;" maxlength="11" value="<?=$field['DiasVencimiento']?>" <?=$disabled_ver?> /></td>
			</tr>
			<tr>
				<td class="tagForm">Stock Minimo:</td>
				<td><input type="text" name="StockMin" id="StockMin" class="currency5" style="width:100px; text-align:right;" value="<?=number_format($field['StockMin'],5,',','.')?>" <?=$disabled_ver?> /></td>
				<td class="tagForm">Moneda:</td>
				<td>
					<select name="Moneda" id="Moneda" style="width:100px;" <?=$disabled_ver?>>
						<option value="">&nbsp;</option>
						<?=loadSelectGeneral('monedas',$field['Moneda'])?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="tagForm">Stock Máximo:</td>
				<td><input type="text" name="StockMax" id="StockMax" class="currency5" style="width:100px; text-align:right;" value="<?=number_format($field['StockMax'],5,',','.')?>" <?=$disabled_ver?> /></td>
				<td class="tagForm">Precio Referencia:</td>
				<td><input type="text" name="PrecioUnitario" id="PrecioUnitario" class="currency" style="width:100px; text-align:right;" value="<?=number_format($field['PrecioUnitario'],2,',','.')?>" <?=$disabled_ver?> /></td>
			</tr>
			<tr>
		    	<td colspan="4" class="divFormCaption">Informaci&oacute;n Contable</td>
		    </tr>
			<tr>
				<td class="tagForm">Cta. Inventario:</td>
				<td class="gallery clearfix">
		        	<input type="text" name="CtaInventario" id="CtaInventario" readonly="readonly" style="width:100px;" value="<?=$field['CtaInventario']?>" />
		            <?php if (empty($selector)) { ?>
						<a href="../lib/listas/gehen.php?anz=lista_plan_cuentas&filtrar=default&campo1=CtaInventario&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe2]" style=" <?=$display_ver?>">
			            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
			            </a>
		            <?php } else { ?>
						<a href="javascript:" onClick="window.open('../lib/listas/gehen.php?anz=lista_plan_cuentas&filtrar=default&campo1=CtaInventario&ventana=selListaOpener', 'lista_lg_clasesubfamilia', 'toolbar=no, menubar=no, location=no, scrollbars=yes, width=950, height=550');" style=" <?=$display_ver?>">
		            		<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
		            	</a>
		            <?php } ?>
				</td>
				<td class="tagForm">Cta. Inventario (Pub.20):</td>
				<td class="gallery clearfix">
			        <input type="text" name="CtaInventarioPub20"  id="CtaInventarioPub20" readonly="readonly" style="width:100px;" value="<?=$field['CtaInventarioPub20']?>" />
		            <?php if (empty($selector)) { ?>
						<a href="../lib/listas/gehen.php?anz=lista_plan_cuentas20&filtrar=default&campo1=CtaInventarioPub20&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe3]" style=" <?=$display_ver?>">
			            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
			            </a>
		            <?php } else { ?>
						<a href="javascript:" onClick="window.open('../lib/listas/gehen.php?anz=lista_plan_cuentas20&filtrar=default&campo1=CtaInventarioPub20&ventana=selListaOpener', 'lista_lg_clasesubfamilia', 'toolbar=no, menubar=no, location=no, scrollbars=yes, width=950, height=550');" style=" <?=$display_ver?>">
		            		<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
		            	</a>
		            <?php } ?>
				</td>
			</tr>
			<tr>
				<td class="tagForm">Cta. Gasto:</td>
				<td class="gallery clearfix">
		        	<input type="text" name="CtaGasto" id="CtaGasto" readonly="readonly" style="width:100px;" value="<?=$field['CtaGasto']?>" />
		            <?php if (empty($selector)) { ?>
						<a href="../lib/listas/gehen.php?anz=lista_plan_cuentas&filtrar=default&campo1=CtaGasto&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe4]" style=" <?=$display_ver?>">
			            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
			            </a>
		            <?php } else { ?>
						<a href="javascript:" onClick="window.open('../lib/listas/gehen.php?anz=lista_plan_cuentas&filtrar=default&campo1=CtaGasto&ventana=selListaOpener', 'lista_lg_clasesubfamilia', 'toolbar=no, menubar=no, location=no, scrollbars=yes, width=950, height=550');" style=" <?=$display_ver?>">
		            		<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
		            	</a>
		            <?php } ?>
				</td>
				<td class="tagForm">Cta. Gasto (Pub.20):</td>
				<td class="gallery clearfix">
		        	<input type="text" name="CtaGastoPub20" id="CtaGastoPub20" readonly="readonly" style="width:100px;" value="<?=$field['CtaGastoPub20']?>" />
		            <?php if (empty($selector)) { ?>
						<a href="../lib/listas/gehen.php?anz=lista_plan_cuentas20&filtrar=default&campo1=CtaGastoPub20&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe5]" style=" <?=$display_ver?>">
			            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
			            </a>
		            <?php } else { ?>
						<a href="javascript:" onClick="window.open('../lib/listas/gehen.php?anz=lista_plan_cuentas20&filtrar=default&campo1=CtaGastoPub20&ventana=selListaOpener', 'lista_lg_clasesubfamilia', 'toolbar=no, menubar=no, location=no, scrollbars=yes, width=950, height=550');" style=" <?=$display_ver?>">
		            		<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
		            	</a>
		            <?php } ?>
				</td>
			</tr>
			<tr>
				<td class="tagForm">Cta. Venta:</td>
				<td class="gallery clearfix">
		        	<input type="text" name="CtaVenta" id="CtaVenta" readonly="readonly" style="width:100px;" value="<?=$field['CtaVenta']?>" />
		            <?php if (empty($selector)) { ?>
						<a href="../lib/listas/gehen.php?anz=lista_plan_cuentas&filtrar=default&campo1=CtaVenta&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe6]" style=" <?=$display_ver?>">
			            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
			            </a>
		            <?php } else { ?>
						<a href="javascript:" onClick="window.open('../lib/listas/gehen.php?anz=lista_plan_cuentas&filtrar=default&campo1=CtaVenta&ventana=selListaOpener', 'lista_lg_clasesubfamilia', 'toolbar=no, menubar=no, location=no, scrollbars=yes, width=950, height=550');" style=" <?=$display_ver?>">
		            		<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
		            	</a>
		            <?php } ?>
				</td>
				<td class="tagForm">Cta. Venta (Pub.20):</td>
				<td class="gallery clearfix">
		        	<input type="text" name="CtaVentaPub20" id="CtaVentaPub20" readonly="readonly" style="width:100px;" value="<?=$field['CtaVentaPub20']?>" />
		            <?php if (empty($selector)) { ?>
						<a href="../lib/listas/gehen.php?anz=lista_plan_cuentas&filtrar=default&campo1=CtaVentaPub20&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe7]" style=" <?=$display_ver?>">
			            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
			            </a>
		            <?php } else { ?>
						<a href="javascript:" onClick="window.open('../lib/listas/gehen.php?anz=lista_plan_cuentas&filtrar=default&campo1=CtaVentaPub20&ventana=selListaOpener', 'lista_lg_clasesubfamilia', 'toolbar=no, menubar=no, location=no, scrollbars=yes, width=950, height=550');" style=" <?=$display_ver?>">
		            		<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
		            	</a>
		            <?php } ?>
				</td>
			</tr>
			<tr>
				<td class="tagForm">Cta. Transito:</td>
				<td class="gallery clearfix">
		        	<input type="text" name="CtaTransito" id="CtaTransito" readonly="readonly" style="width:100px;" value="<?=$field['CtaTransito']?>" />
		            <?php if (empty($selector)) { ?>
						<a href="../lib/listas/gehen.php?anz=lista_plan_cuentas&filtrar=default&campo1=CtaTransito&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe8]" style=" <?=$display_ver?>">
			            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
			            </a>
		            <?php } else { ?>
						<a href="javascript:" onClick="window.open('../lib/listas/gehen.php?anz=lista_plan_cuentas&filtrar=default&campo1=CtaTransito&ventana=selListaOpener', 'lista_lg_clasesubfamilia', 'toolbar=no, menubar=no, location=no, scrollbars=yes, width=950, height=550');" style=" <?=$display_ver?>">
		            		<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
		            	</a>
		            <?php } ?>
				</td>
				<td class="tagForm">Cta. Transito (Pub.20):</td>
				<td class="gallery clearfix">
		        	<input type="text" name="CtaTransitoPub20" id="CtaTransitoPub20" readonly="readonly" style="width:100px;" value="<?=$field['CtaTransitoPub20']?>" />
		            <?php if (empty($selector)) { ?>
						<a href="../lib/listas/gehen.php?anz=lista_plan_cuentas&filtrar=default&campo1=CtaTransitoPub20&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe9]" style=" <?=$display_ver?>">
			            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
			            </a>
		            <?php } else { ?>
						<a href="javascript:" onClick="window.open('../lib/listas/gehen.php?anz=lista_plan_cuentas&filtrar=default&campo1=CtaTransitoPub20&ventana=selListaOpener', 'lista_lg_clasesubfamilia', 'toolbar=no, menubar=no, location=no, scrollbars=yes, width=950, height=550');" style=" <?=$display_ver?>">
		            		<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
		            	</a>
		            <?php } ?>
				</td>
			</tr>
			<tr>
				<td class="tagForm">Partida Presupuestaria:</td>
				<td class="gallery clearfix">
		        	<input type="text" name="PartidaPresupuestal" id="PartidaPresupuestal" readonly="readonly" style="width:100px;" value="<?=$field['PartidaPresupuestal']?>" />
		            <?php if (empty($selector)) { ?>
						<a href="../lib/listas/gehen.php?anz=lista_partidas&campo1=PartidaPresupuestal&filtrar=default&ventana=&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe10]" style=" <?=$display_ver?>">
			            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
			            </a>
		            <?php } else { ?>
						<a href="javascript:" onClick="window.open('../lib/listas/gehen.php?anz=lista_partidas&campo1=PartidaPresupuestal&filtrar=default&ventana=selListaOpener', 'lista_lg_clasesubfamilia', 'toolbar=no, menubar=no, location=no, scrollbars=yes, width=950, height=550');" style=" <?=$display_ver?>">
		            		<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
		            	</a>
		            <?php } ?>
		        </td>
				<td class="tagForm">Impuesto:</td>
				<td>
					<select name="CodImpuesto" id="CodImpuesto" style="width:125px;" <?=$disabled_impuesto?>>
						<option value="">&nbsp;</option>
						<?=loadSelect2("mastimpuestos","CodImpuesto","Descripcion",$field['CodImpuesto'],0,['CodRegimenFiscal'],['I'])?>
					</select>
				</td>
			</tr>
		</table>
		
	</div>

	<div id="tab3" style="display:none;">
		<input type="hidden" id="sel_unidades" />
		<table style="width:100%; max-width:<?=$_width?>px;" class="tblBotones">
			<thead>
				<tr>
					<th class="divFormCaption" colspan="2">UNIDADES EQUIVALENTES</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td align="right">
						<input type="button" class="btLista" value="Insertar" onclick="insertar2(this, 'unidades', 'modulo=ajax&accion=unidades_insertar', 'lg_item_ajax.php');" <?=$disabled_ver?> />
						<input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'unidades');" <?=$disabled_ver?> />
					</td>
				</tr>
			</tbody>
		</table>
		<div style="overflow:scroll; height:230px; width:100%; max-width:<?=$_width?>px; margin:auto;">
			<table class="tblLista" style="width:100%; min-width:<?=$_width-50?>px;">
				<thead>
					<tr>
						<th width="20">#</th>
						<th width="200">Una unidad de</th>
						<th>Equivale a</th>
						<th width="200">Unidades de</th>
					</tr>
				</thead>
				
				<tbody id="lista_unidades">
					<?php
					$nro_unidades = 0;
					$sql = "SELECT *
							FROM lg_itemunidades
							WHERE CodItem = '$field[CodItem]'";
					$field_unidades = getRecords($sql);
					foreach ($field_unidades as $f)
					{
						$id = ++$nro_unidades;
						?>
						<tr class="trListaBody" onclick="clk($(this), 'unidades', 'unidades_<?=$id?>');" id="unidades_<?=$id?>">
							<th><?=$id?></th>
							<td>
								<select name="unidades_CodUnidad[]" class="cell" <?=$disabled_ver?>>
									<option value="">&nbsp;</option>
									<?=loadSelect("mastunidades", "CodUnidad", "Descripcion", $f['CodUnidad'], 0)?>
								</select>
							</td>
							<td>
								<input type="text" name="unidades_Valor[]" value="<?=number_format($f['Valor'],5,',','.')?>" style="text-align: right;" class="cell currency5">
							</td>
				            <td>
								<select name="unidades_CodUnidadConv[]" class="cell" <?=$disabled_ver?>>
									<option value="">&nbsp;</option>
									<?=loadSelect("mastunidades", "CodUnidad", "Descripcion", $f['CodUnidadConv'], 0)?>
								</select>
				            </td>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>
		</div>
		<input type="hidden" id="nro_unidades" value="<?=$nro_unidades?>" />
		<input type="hidden" id="can_unidades" value="<?=$nro_unidades?>" />
	</div>

	<center>
		<input type="submit" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" id="btSubmit" />
		<input type="button" value="Cancelar" style="width:75px;" id="btCancelar" onclick="<?=$clkCancelar?>" />
	</center>
</form>
<div style="width:100%; max-width:<?=$_width?>px;" class="divMsj">Campos Obligatorios *</div>