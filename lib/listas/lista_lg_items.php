<?php
if (empty($ventana)) $ventana = "selLista";
if (!empty($accion_selector)) $accion = $accion_selector;
if (!empty($modulo_selector)) $modulo = $modulo_selector;
if (!empty($_APLICACION))
{
	if ($_APLICACION == 'LG') $concepto = '03-0017';
}
if (!empty($concepto)) list ($_SHOW, $_ADMIN, $_INSERT, $_UPDATE, $_DELETE) = opcionesPermisos('', $concepto);
else {
	$_SHOW = 'N';
	$_ADMIN = 'N';
	$_INSERT = 'N';
	$_UPDATE = 'N';
	$_DELETE = 'N';
}
//	------------------------------------
if (isset($selector)) 
{
	$fBuscar = $Descripcion;
}
if ($filtrar == "default") {
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fEstado = "A";
	$fOrderBy = "CodItem";
}
if ($fFlagDisponible != '') $filtro .= " AND i.FlagDisponible = '$fFlagDisponible'";
if ($fBuscar != "") { 
	$cBuscar = "checked"; 
	$filtro.=" AND (i.CodItem LIKE '%$fBuscar%'
					OR i.CodInterno LIKE '%$fBuscar%'
					OR i.Descripcion LIKE '%$fBuscar%'
					OR i.CodLinea LIKE '%$fBuscar%'
					OR i.CodFamilia LIKE '%$fBuscar%'
					OR i.CodSubFamilia LIKE '%$fBuscar%'
					OR i.PartidaPresupuestal LIKE '%$fBuscar%'
					OR i.NomMarca LIKE '%$fBuscar%'
					OR i.NomTipoItem LIKE '%$fBuscar%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (i.Estado = '$fEstado')"; } else $dEstado = "disabled";
if ($fCodLinea != "") { $cCodLinea = "checked"; $filtro.=" AND (i.CodLinea = '$fCodLinea')"; } else $dCodLinea = "disabled";
if ($fCodFamilia != "") { $cCodFamilia = "checked"; $filtro.=" AND (i.CodFamilia = '$fCodFamilia')"; } else $dCodFamilia = "disabled";
if ($fCodSubFamilia != "") { $cCodSubFamilia = "checked"; $filtro.=" AND (i.CodSubFamilia = '$fCodSubFamilia')"; } else $dCodSubFamilia = "disabled";
if ($fCodTipoItem != "") { $cCodTipoItem = "checked"; $filtro.=" AND (i.CodTipoItem = '$fCodTipoItem')"; } else $dCodTipoItem = "disabled";
if ($fCodMarca != "") { $cCodMarca = "checked"; $filtro.=" AND (i.CodMarca = '$fCodMarca')"; } else $dCodMarca = "disabled";
if ($fCodImpuesto != "") { $cCodImpuesto = "checked"; $filtro.=" AND (i.CodImpuesto = '$fCodImpuesto')"; } else $dCodImpuesto = "disabled";
if ($fPrecioUnitarioD != "" || $fPrecioUnitarioH != "") {
	$cPrecioUnitario = "checked";
	if ($fPrecioUnitarioD != "") $filtro.=" AND (i.PrecioUnitario >= '".setNumero($fPrecioUnitarioD)."')";
	if ($fPrecioUnitarioH != "") $filtro.=" AND (i.PrecioUnitario <= '".setNumero($fPrecioUnitarioH)."')";
} else $dPrecioUnitario = "disabled";
if ($fStockActualD != "" || $fStockActualH != "") {
	$cStockActual = "checked";
	if ($fStockActualD != "") $filtro.=" AND (i.StockActual >= '".setNumero($fStockActualD)."')";
	if ($fStockActualH != "") $filtro.=" AND (i.StockActual <= '".setNumero($fStockActualH)."')";
} else $dStockActual = "disabled";
//	------------------------------------
$_titulo = "Maestro de Items";
$_width = 900;
?>
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lista_lg_items" method="post">
	<input type="hidden" name="registro" id="registro" />
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
	<input type="hidden" name="campo11" id="campo11" value="<?=$campo11?>" />
	<input type="hidden" name="campo12" id="campo12" value="<?=$campo12?>" />
	<input type="hidden" name="ventana" id="ventana" value="<?=$ventana?>" />
	<input type="hidden" name="detalle" id="detalle" value="<?=$detalle?>" />
	<input type="hidden" name="modulo" id="modulo" value="<?=$modulo?>" />
	<input type="hidden" name="modulo_selector" id="modulo_selector" value="<?=$modulo?>" />
	<input type="hidden" name="accion" id="accion" value="<?=$accion?>" />
	<input type="hidden" name="accion_selector" id="accion_selector" value="<?=$accion?>" />
	<input type="hidden" name="url" id="url" value="<?=$url?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
	<input type="hidden" name="fFlagDisponible" id="fFlagDisponible" value="<?=$fFlagDisponible?>" />
	<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />

	<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
		<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
			<tr>
				<td align="right">Linea: </td>
				<td>
					<input type="checkbox" <?=$cCodLinea?> onclick="chkFiltro(this.checked, 'fCodLinea');" />
					<select name="fCodLinea" id="fCodLinea" style="width:193px;" <?=$dCodLinea?> onChange="loadSelect($('#fCodFamilia'), 'tabla=lg_clasefamilia&CodLinea='+$('#fCodLinea').val(), 0, ['fCodSubFamilia']);">
						<option value="">&nbsp;</option>
						<?=loadSelect2('lg_claselinea','CodLinea','Descripcion',$fCodLinea,10)?>
					</select>
				</td>
				<td align="right">Buscar:</td>
				<td>
					<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
					<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:193px;" <?=$dBuscar?> />
				</td>
				<td align="right">Impuesto: </td>
				<td>
		            <input type="checkbox" <?=$cCodImpuesto?> onclick="chkFiltro(this.checked, 'fCodImpuesto');" />
		            <select name="fCodImpuesto" id="fCodImpuesto" style="width:193px;" <?=$dCodImpuesto?>>
		            	<option value="">&nbsp;</option>
		                <?=loadSelect2('mastimpuestos','CodImpuesto','Descripcion',$fCodImpuesto,0,['CodRegimenFiscal'],['I'])?>
		            </select>
				</td>
		        <td align="right">&nbsp;</td>
			</tr>
			<tr>
				<td align="right">Familia:</td>
				<td>
					<input type="checkbox" <?=$cCodFamilia?> onclick="chkFiltro(this.checked, 'fCodFamilia');" />
					<select name="fCodFamilia" id="fCodFamilia" style="width:193px;" <?=$dCodFamilia?> onChange="loadSelect($('#fCodSubFamilia'), 'tabla=lg_clasesubfamilia&CodLinea='+$('#fCodLinea').val()+'&CodFamilia='+$('#fCodFamilia').val(), 0);">
						<option value="">&nbsp;</option>
						<?=loadSelect2('lg_clasefamilia','CodFamilia','Descripcion',$fCodFamilia,10,['CodLinea'],[$fCodLinea])?>
					</select>
				</td>
				<td align="right">Tipo de Item:</td>
				<td>
		            <input type="checkbox" <?=$cCodTipoItem?> onclick="chkFiltro(this.checked, 'fCodTipoItem');" />
		            <select name="fCodTipoItem" id="fCodTipoItem" style="width:193px;" <?=$dCodTipoItem?>>
		            	<option value="">&nbsp;</option>
		                <?=loadSelect2('lg_tipoitem','CodTipoItem','Descripcion',$fCodTipoItem)?>
		            </select>
				</td>
				<td align="right">Marca:</td>
				<td>
		            <input type="checkbox" <?=$cCodMarca?> onclick="chkFiltro(this.checked, 'fCodMarca');" />
		            <select name="fCodMarca" id="fCodMarca" style="width:193px;" <?=$dCodMarca?>>
		            	<option value="">&nbsp;</option>
		                <?=loadSelect2('lg_marcas','CodMarca','Descripcion',$fCodMarca)?>
		            </select>
				</td>
		        <td align="right">&nbsp;</td>
			</tr>
			<tr>
				<td align="right">Sub-Familia:</td>
				<td>
					<input type="checkbox" <?=$cCodSubFamilia?> onclick="chkFiltro(this.checked, 'fCodSubFamilia');" />
					<select name="fCodSubFamilia" id="fCodSubFamilia" style="width:193px;" <?=$dCodSubFamilia?>>
						<option value="">&nbsp;</option>
						<?=loadSelect2('lg_clasesubfamilia','CodSubFamilia','Descripcion',$fCodSubFamilia,10,['CodLinea','CodFamilia'],[$fCodLinea,$CodFamilia])?>
					</select>
				</td>
				<td align="right">Stock Actual: </td>
				<td>
					<input type="checkbox" <?=$cStockActual?> onclick="chkCampos2(this.checked, ['fStockActualD','fStockActualH']);" />
					<input type="text" name="fStockActualD" id="fStockActualD" value="<?=$fStockActualD?>" <?=$dStockActual?> style="width:91px; text-align: right;" class="currency5" /> -
		            <input type="text" name="fStockActualH" id="fStockActualH" value="<?=$fStockActualH?>" <?=$dStockActual?> style="width:91px; text-align: right;" class="currency5" />
				</td>
				<td align="right">Precio: </td>
				<td>
					<input type="checkbox" <?=$cPrecioUnitario?> onclick="chkCampos2(this.checked, ['fPrecioUnitarioD','fPrecioUnitarioH']);" />
					<input type="text" name="fPrecioUnitarioD" id="fPrecioUnitarioD" value="<?=$fPrecioUnitarioD?>" <?=$dPrecioUnitario?> style="width:90px; text-align: right;" class="currency" /> -
		            <input type="text" name="fPrecioUnitarioH" id="fPrecioUnitarioH" value="<?=$fPrecioUnitarioH?>" <?=$dPrecioUnitario?> style="width:90px; text-align: right;" class="currency" />
				</td>
		        <td align="right"><input type="submit" value="Buscar"></td>
			</tr>
		</table>
	</div>
	<div class="sep"></div>

	<?php
	if (!empty($_APLICACION))
	{
		?>
		<table class="tblBotones" style="width:100%; min-width:<?=$_width?>px;">
		    <tr>
		        <td><div id="rows"></div></td>
		        <td align="right">
	            	<input type="button" value="Nuevo" style="width:75px;" class="insert" onclick="cargarPagina(this.form, '../../lg/gehen.php?anz=lg_item_form&opcion=nuevo&selector=true&action=../lib/listas/gehen.php?anz=lista_lg_items');" />
	            	<input type="button" value="Ver Precios" style="width:75px;" class="ver" onclick="window.open('../../co/gehen.php?anz=co_precios_lista&accion=ver&filtrar=default&concepto=03-0017','co_precios_lista','width=900, height=2000, toolbar=no, menubar=no, location=no, scrollbars=yes, left=0, top=0, resizable=no')" />
		        </td>
		    </tr>
		</table>
		<?php
	}
	?>

	<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px;">
		<table class="tblLista" style="width:100%; min-width:1750px;">
			<thead>
			    <tr>
					<th width="65" onclick="order('CodItem')">Item</th>
					<th width="50" onclick="order('CodInterno')">Cod. Interno</th>
					<th align="left" onclick="order('Descripcion')">Descripci&oacute;n</th>
					<th width="30" onclick="order('CodUnidad')">Uni.</th>
					<th width="30" onclick="order('CodUnidadComp')">Uni. Comp.</th>
					<th width="65" onclick="order('StockActual')">Stock Actual</th>
					<th width="65" onclick="order('StockActualEqui')">Stock Actual (Venta)</th>
					<th width="65" onclick="order('MontoVentaUnitario')">Precio Unit.</th>
					<th width="65" onclick="order('MontoVenta')">Precio Und. Vta.</th>
					<th width="50" onclick="order('CodLinea')">Linea</th>
					<th width="50" onclick="order('CodFamilia')">Familia</th>
					<th width="50" onclick="order('CodSubFamilia')">Sub-Familia</th>
					<th width="150" onclick="order('NomTipoItem')">Tipo de Item</th>
					<th width="150" onclick="order('NomMarca')">Marca</th>
					<th width="75" align="right" onclick="order('FactorImpuesto')">Impuesto</th>
					<th width="80" onclick="order('PartidaPresupuestal')">Partida</th>
			    </tr>
		    </thead>
		    
		    <tbody>
			<?php
			//	consulto todos
			$sql = "SELECT i.*
					FROM vw_lg_inventarioactual_item i
					WHERE 1 $filtro";
			$rows_total = getNumRows3($sql);
			//	consulto lista
			$sql = "SELECT
						i.*,
						(i.Ingresos - i.Egresos) StockActual,
						((i.Ingresos - i.Egresos) / i.CantidadEqui) AS StockActualEqui,
						(SELECT MAX(k.PrecioUnitario)
						 FROM lg_kardex k
						 INNER JOIN lg_tipotransaccion tt ON (
							tt.CodTransaccion = k.CodTransaccion
							AND tt.TipoMovimiento = 'I'
						 )
						 INNER JOIN lg_transaccion t ON (
							t.CodDocumento = k.CodDocumento
							AND t.NroDocumento = k.NroDocumento
							AND t.ReferenciaAnio = '$AnioActual'
						 )
						 WHERE k.CodItem = i.CodItem) AS PrecioCostoUnitario,
						(SELECT u.Valor FROM lg_itemunidades u WHERE u.CodItem = i.CodItem LIMIT 1) AS CantidadEquivalente
					FROM vw_lg_inventarioactual_item i
					WHERE 1 $filtro
					ORDER BY $fOrderBy
					LIMIT ".intval($limit).", ".intval($maxlimit);
			$field = getRecords($sql);
			$rows_lista = count($field);
			foreach($field as $f) {
				$id = $f['CodItem'];
				if ($ventana == 'listado_insertar_linea') {
					?>
		            <tr class="trListaBody" onClick="listado_insertar_linea('<?=$detalle?>','modulo=<?=$modulo?>&accion=<?=$accion?>&CodItem=<?=$f['CodItem']?>','<?=$f['CodItem']?>','<?=$url?>');">
		            <?php
				}
				elseif ($ventana == 'listado_insertar_linea_cotizacion') {
					?>
		            <tr class="trListaBody" onClick="<?=$ventana?>('<?=$detalle?>','modulo=<?=$modulo?>&accion=<?=$accion?>&CodItem=<?=$f['CodItem']?>','<?=$f['CodItem']?>','<?=$url?>');">
		            <?php
				}
				elseif ($ventana == 'listado_insertar_linea_precios') {
					?>
		            <tr class="trListaBody" onClick="<?=$ventana?>('<?=$detalle?>','modulo=<?=$modulo?>&accion=<?=$accion?>&CodItem=<?=$f['CodItem']?>','<?=$f['CodItem']?>','<?=$url?>');">
		            <?php
				}
				elseif ($ventana == 'CodInterno') {
					?>
		            <tr class="trListaBody" onClick="selLista(['<?=$f['CodItem']?>','<?=$f['Descripcion']?>','<?=$f['CodInterno']?>','<?=$f['CodUnidad']?>','<?=$f['CodUnidadComp']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>','<?=$campo4?>','<?=$campo5?>']);">
		            <?php

				}
				elseif ($ventana == 'co_precios') {
					$PrecioCostoUnitario = floatval($f['PrecioCostoUnitario']);
					$CantidadEquivalente = floatval($f['CantidadEqui']);
					$PrecioCostoVenta = $PrecioCostoUnitario * $CantidadEquivalente;
					?>
		            <tr class="trListaBody" onClick="selListaPrecios(['<?=$f['CodItem']?>','<?=$f['Descripcion']?>','<?=$f['CodInterno']?>','<?=$f['CodUnidad']?>','<?=$f['CodUnidadComp']?>','<?=number_format($PrecioCostoUnitario,2,',','.')?>','<?=number_format($PrecioCostoVenta,2,',','.')?>','<?=$f['FlagImpuestoVentas']?>','<?=$f['CodImpuesto']?>','<?=$f['FactorImpuesto']?>','<?=$f['CantidadEqui']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>','<?=$campo4?>','<?=$campo5?>','<?=$campo6?>','<?=$campo7?>','<?=$campo8?>','<?=$campo9?>','<?=$campo10?>','<?=$campo11?>']);">
		            <?php
				}
				elseif ($ventana == 'orden_compra_detalles_insertar') {
					?>
					<tr class="trListaBody" onClick="orden_compra_detalles_insertar('<?=$f["CodItem"]?>', 'item');" id="<?=$f['CodItem']?>">
					<?php
				}
				else {
					?>
		            <tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['CodItem']?>','<?=$f['Descripcion']?>','<?=$f['CodUnidad']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>']);">
		            <?php
				}
				?>
					<td align="center"><?=$f['CodItem']?></td>
					<td align="center"><?=$f['CodInterno']?></td>
					<td><?=htmlentities($f['Descripcion'])?></td>
					<td align="center"><?=$f['CodUnidad']?></td>
					<td align="center"><?=$f['CodUnidadComp']?></td>
					<td align="right"><?=number_format($f['StockActual'],2,',','.')?></td>
					<td align="right"><?=number_format($f['StockActualEqui'],2,',','.')?></td>
					<td align="right"><?=number_format($f['MontoVentaUnitario'],2,',','.')?></td>
					<td align="right"><?=number_format($f['MontoVenta'],2,',','.')?></td>
					<td align="center"><?=$f['CodLinea']?></td>
					<td align="center"><?=$f['CodFamilia']?></td>
					<td align="center"><?=$f['CodSubFamilia']?></td>
					<td><?=htmlentities($f['NomTipoItem'])?></td>
					<td><?=htmlentities($f['NomMarca'])?></td>
					<td align="right"><?=number_format($f['FactorImpuesto'],2,',','.')?></td>
					<td align="center"><?=$f['PartidaPresupuestal']?></td>
				</tr>
				<?php
			}
			?>
		    </tbody>
		</table>
	</div>
	<table style="width:100%; min-width:<?=$_width?>px;">
		<tr>
	    	<td>
	        	Mostrar: 
	            <select name="maxlimit" style="width:50px;" onchange="this.form.submit();">
	                <?=loadSelectGeneral("MAXLIMIT", $maxlimit, 0)?>
	            </select>
	        </td>
	        <td align="right">
	        	<?=paginacion(intval($rows_total), intval($rows_lista), intval($maxlimit), intval($limit));?>
	        </td>
	    </tr>
	</table>
</form>

<script type="text/javascript">
	function listado_insertar_linea_cotizacion(detalle, data, id, url) {
		$.post('form_item_cantidad.php', data, function(form) {
			$("#cajaModal").dialog({
				buttons: {
					"Aceptar": function() {
						$(this).dialog("close");
						insertar_linea_cotizacion(detalle, data, id, url);
					},
					"Cancelar": function() {
						$(this).dialog("close");
					}
				}
			});
			$("#cajaModal").dialog({ title: "<img src='../../imagenes/info.png' width='24' align='absmiddle' />Agregar Item", width: 500 });
			$("#cajaModal").html(form);
			$('#cajaModal').dialog('open');
			inicializar();
	    });
	}

	function insertar_linea_cotizacion(detalle, data, id, url) {
		var nro_detalles = parent.$("#nro_"+detalle);
		var can_detalles = parent.$("#can_"+detalle);
		var lista_detalles = parent.$("#lista_"+detalle);
		var nro = new Number(nro_detalles.val());	nro++;
		var can = new Number(can_detalles.val());	can++;
		if (!id) var idtr = detalle+"_"+nro; else var idtr = detalle+"_"+id;
		//	ajax
		$.ajax({
			type: "POST",
			url: url,
			data: "nro_detalles="+nro+"&can_detalles="+can+"&"+data+"&Cantidad="+$('#Cantidad').val(),
			async: false,
			success: function(resp) {
				nro_detalles.val(nro);
				can_detalles.val(can);
				lista_detalles.append(resp);
				inicializarParent();
				parent.setMontosVentas();
			}
		});
	}

	function listado_insertar_linea_precios(detalle, data, id, url) {
		var nro_detalles = parent.$("#nro_"+detalle);
		var can_detalles = parent.$("#can_"+detalle);
		var lista_detalles = parent.$("#lista_"+detalle);
		var nro = new Number(nro_detalles.val());	nro++;
		var can = new Number(can_detalles.val());	can++;
		if (!id) var idtr = detalle+"_"+nro; else var idtr = detalle+"_"+id;
		//	ajax
		$.ajax({
			type: "POST",
			url: url,
			data: "nro_detalles="+nro+"&can_detalles="+can+"&"+data+"&Cantidad="+$('#Cantidad').val(),
			async: false,
			success: function(resp) {
				if (parent.document.getElementById(idtr)) cajaModal("Registro ya insertado", "error_lista", 400);
				else {
					nro_detalles.val(nro);
					can_detalles.val(can);
					lista_detalles.append(resp);
					inicializarParent();
				}
			}
		});
	}
	
	function selListaPrecios(valores, inputs) {
		if (inputs) {
			for(var i=0; i<inputs.length; i++) {
				if (parent.$("#"+inputs[i]).length > 0) parent.$("#"+inputs[i]).val(valores[i]);
			}
		}
		parent.calcularPrecio();
		parent.$.prettyPhoto.close();
	}
</script>