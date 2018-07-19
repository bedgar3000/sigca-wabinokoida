<?php
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$fCodEstablecimiento = getVar3("SELECT CodEstablecimiento FROM co_establecimientofiscal WHERE CodOrganismo = '$fCodOrganismo' LIMIT 1");
	$fFechaDocumentoD = formatFechaDMA($PeriodoActual.'-01');
	$fFechaDocumentoH = formatFechaDMA($FechaActual);
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "FechaDocumento";
}
//	------------------------------------
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (ef.Descripcion LIKE '%$fBuscar%'
					  OR cd.CodInterno LIKE '%$fBuscar%'
					  OR cd.Descripcion LIKE '%$fBuscar%'
					  OR cd.Comentarios LIKE '%$fBuscar%'
					  OR c.NroCotizacion LIKE '%$fBuscar%'
					  OR c.NombreCliente LIKE '%$fBuscar%'
					  OR c.DocFiscalCliente LIKE '%$fBuscar%')";
} else $dBuscar = "disabled";
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (c.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fCodEstablecimiento != "") { $cCodEstablecimiento = "checked"; $filtro.=" AND (c.CodEstablecimiento = '".$fCodEstablecimiento."')"; } else $dCodEstablecimiento = "disabled";
if ($fFechaDocumentoD != "" || $fFechaDocumentoH != "") {
	$cFechaDocumento = "checked";
	if ($fFechaDocumentoD != "") $filtro.=" AND (c.FechaDocumento >= '".formatFechaAMD($fFechaDocumentoD)."')";
	if ($fFechaDocumentoH != "") $filtro.=" AND (c.FechaDocumento <= '".formatFechaAMD($fFechaDocumentoH)."')";
} else $dFechaDocumento = "disabled";
if ($fCodPersonaCliente != "") { $cCodPersonaCliente = "checked"; $filtro.=" AND (c.CodPersonaCliente = '".$fCodPersonaCliente."')"; } else $dCodPersonaCliente = "visibility:hidden;";
if ($fCodAlmacen != "") { $cCodAlmacen = "checked"; $filtro.=" AND (c.CodAlmacen = '".$fCodAlmacen."')"; } else $dCodAlmacen = "disabled";
//	------------------------------------
$_titulo = "Generaci&oacute;n de Pedidos";
$_width = 700;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=co_cotizacion_generar_lista" method="post" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />

	<!--FILTRO-->
	<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
		<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
			<tr>
				<td align="right" width="100">Organismo:</td>
				<td>
					<input type="checkbox" <?=$cCodOrganismo?> onclick="this.checked=!this.checked;" />
					<select name="fCodOrganismo" id="fCodOrganismo" style="width:225px;" <?=$dCodOrganismo?>>
						<?=getOrganismos($fCodOrganismo, 3);?>
					</select>
				</td>
				<td align="right" width="100">Cliente:</td>
				<td class="gallery clearfix">
					<input type="checkbox" <?=$cCodPersonaCliente?> onclick="ckLista(this.checked, ['fCodPersonaCliente','fNombreCliente','fDocFiscalCliente'], ['aCodPersonaCliente']);" />
					<input type="hidden" name="fDocFiscalCliente" id="fDocFiscalCliente" value="<?=$fDocFiscalCliente?>">
					<input type="hidden" name="fCodPersonaCliente" id="fCodPersonaCliente" value="<?=$fCodPersonaCliente?>" />
					<input type="text" name="fNombreCliente" id="fNombreCliente" value="<?=htmlentities($fNombreCliente)?>" style="width:225px;" readonly />
					<a href="../lib/listas/gehen.php?anz=lista_personas&campo1=fCodPersonaCliente&campo2=fNombreCliente&campo3=fDocFiscalCliente&ventana=&filtrar=default&FlagClasePersona=S&fEsCliente=S&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" style=" <?=$dCodPersonaCliente?>" id="aCodPersonaCliente">
		            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
		            </a>
				</td>
				<td align="right" width="100">Buscar:</td>
				<td>
					<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
					<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:100px;" <?=$dBuscar?> />
				</td>
		        <td></td>
			</tr>
			<tr>
				<td align="right">Establecimiento:</td>
				<td>
					<input type="checkbox" <?=$cCodEstablecimiento?> onclick="this.checked=!this.checked;" />
					<select name="fCodEstablecimiento" id="fCodEstablecimiento" style="width:225px;" <?=$dCodEstablecimiento?>>
						<?=loadSelect2('co_establecimientofiscal','CodEstablecimiento','Descripcion',$fCodEstablecimiento)?>
					</select>
				</td>
				<td align="right">Fecha:</td>
				<td>
					<input type="checkbox" <?=$cFechaDocumento?> onclick="chkCampos2(this.checked, ['fFechaDocumentoD','fFechaDocumentoH']);" />
					<input type="text" name="fFechaDocumentoD" id="fFechaDocumentoD" value="<?=$fFechaDocumentoD?>" <?=$dFechaDocumento?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" /> -
		            <input type="text" name="fFechaDocumentoH" id="fFechaDocumentoH" value="<?=$fFechaDocumentoH?>" <?=$dFechaDocumento?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" />
		        </td>
		        <td></td>
		        <td></td>
		        <td align="right"><input type="submit" value="Buscar"></td>
			</tr>
		</table>
	</div>
	<div class="sep"></div>

	<!--REGISTROS-->
	<input type="hidden" name="sel_registros" id="sel_registros" />
	<table class="tblBotones" style="width:100%; min-width:<?=$_width?>px;">
	    <tr>
	        <td><div id="rows"></div></td>
	        <td align="right">
            	<input type="button" value="Generar Pedido" style="width:100px;" onclick="cargarOpcionPedido(this.form, 'gehen.php?anz=co_pedidos_form&opcion=generar&origen=co_cotizacion_generar_lista', 'SELF', '', 'registros[]', 'sel_registros', 1)" />
	        </td>
	    </tr>
	</table>

	<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px;">
		<table class="tblLista" style="width:100%; min-width:2000px;">
			<thead>
			    <tr>
			        <th style="min-width: 150px;" align="left">Establecimiento</th>
			        <th width="100">N&uacute;mero</th>
			        <th width="75">Fecha</th>
			        <th width="35">#</th>
			        <th width="100">Item/ Servicio</th>
			        <th style="min-width: 300px;" align="left">Descripci&oacute;n</th>
			        <th width="35">Uni.</th>
			        <th width="35">Uni. Venta</th>
			        <th width="75">Stock Actual</th>
			        <th width="75">Cantidad Pedida</th>
			        <th width="150">Precio Unit.</th>
			        <th width="150">Monto</th>
			        <th style="min-width: 400px;" align="left">Comentarios</th>
			    </tr>
		    </thead>
		    
		    <tbody id="lista_registros">
			<?php
			//	consulto todos
			$sql = "SELECT *
					FROM co_cotizaciondet cd
					INNER JOIN co_cotizacion c ON c.CodCotizacion = cd.CodCotizacion
					INNER JOIN co_establecimientofiscal ef ON ef.CodEstablecimiento = c.CodEstablecimiento
					LEFT JOIN lg_itemmast i ON (
						i.CodItem = cd.CodItem
						AND cd.TipoDetalle = 'I'
					)
					LEFT JOIN co_mastservicios s ON (
						s.CodServicio = cd.CodItem
						AND cd.TipoDetalle = 'S'
					)
					LEFT JOIN lg_itemalmaceninv iai ON (
						iai.CodItem = i.CodItem
						AND cd.TipoDetalle = 'I'
					)
					WHERE cd.Estado = 'PE' $filtro";
			$rows_total = getNumRows3($sql);
			//	consulto lista
			$sql = "SELECT
						cd.*,
						c.NroCotizacion,
						c.FechaDocumento,
						c.CodPersonaCliente,
						c.NombreCliente,
						c.DocFiscalCliente,
						c.Comentarios,
						ef.Descripcion AS Establecimiento,
						i.StockActual,
						i.StockActualEqui,
						(CASE WHEN cd.TipoDetalle = 'I' THEN i.CodInterno ELSE s.CodInterno END) AS CodInterno
					FROM co_cotizaciondet cd
					INNER JOIN co_cotizacion c ON c.CodCotizacion = cd.CodCotizacion
					INNER JOIN co_establecimientofiscal ef ON ef.CodEstablecimiento = c.CodEstablecimiento
					LEFT JOIN vw_lg_inventarioactual_item i ON (
						i.CodItem = cd.CodItem
						AND cd.TipoDetalle = 'I'
					)
					LEFT JOIN co_mastservicios s ON (
						s.CodServicio = cd.CodItem
						AND cd.TipoDetalle = 'S'
					)
					WHERE cd.Estado = 'PE' $filtro
					ORDER BY CodPersonaCliente, FechaDocumento, CodCotizacion, Secuencia
					LIMIT ".intval($limit).", ".intval($maxlimit);
			$field = getRecords($sql);
			$rows_lista = count($field);
			$Grupo = '';
			foreach($field as $f) {
				$id = $f['CodCotizacion'] . '_' . $f['Secuencia'];
				?>
				<?php if ($Grupo != $f['CodPersonaCliente']) { ?>
					<?php $Grupo = $f['CodPersonaCliente']; ?>
					<tr class="trListaBody2">
						<td colspan="13"><?=$f['DocFiscalCliente'] . ' ' . htmlentities($f['NombreCliente'])?></td>
					</tr>
				<?php } ?>
				<tr class="trListaBody" onclick="clkMulti($(this), '<?=$id?>');">
					<td>
						<input type="checkbox" name="registros[]" id="<?=$id?>" value="<?=$id?>" style="display:none" />
						<?=htmlentities($f['Establecimiento'])?>
					</td>
					<td align="center"><?=$f['NroCotizacion']?></td>
					<td align="center"><?=formatFechaAMD($f['FechaDocumento'])?></td>
					<td align="center"><?=$f['Secuencia']?></td>
					<td align="center"><?=$f['CodInterno']?></td>
					<td><?=htmlentities($f['Descripcion'])?></td>
					<td align="center"><?=$f['CodUnidad']?></td>
					<td align="center"><?=$f['CodUnidadVenta']?></td>
					<td align="right"><?=number_format($f['StockActual'],2,',','.')?></td>
					<td align="right"><?=number_format($f['CantidadPedida'],2,',','.')?></td>
					<td align="right"><?=number_format($f['PrecioUnit'],2,',','.')?></td>
					<td align="right"><?=number_format($f['MontoTotal'],2,',','.')?></td>
					<td><?=htmlentities($f['Comentarios'])?></td>
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
	function cargarOpcionPedido(form, pagina, target, param, nombre, idregistro, multi, frmentrada) {
		//	lineas
		var error = "";
		var registro = "";
		var lineas = new Number(0);
		for(i=0; n=form.elements[i]; i++) {
			if (n.name == nombre && n.checked) { registro += n.value + ";"; lineas++; }
		}
		var len = registro.length; len--;
		registro = registro.substr(0, len);
		document.getElementById(idregistro).value = registro;
		
		if (!frmentrada) frmentrada = form;
		
		if (lineas == 0) cajaModal("Debe seleccionar por lo menos un registro", "error", 400);
		else if (!multi && lineas > 1) cajaModal("No puede seleccionar más de un registro", "error", 400);
		else {
			$.post('co_cotizacion_ajax.php', 'modulo=validar&accion=generar&'+$('form').serialize(), function(data) {
				if (data['status'] == 'error') {
					cajaModal(data['message']);
				}
				else if (data['status'] == 'warning') {
					$("#cajaModal").dialog({
						buttons: {
							"Aceptar": function() {
								$(this).dialog("close");
								if (target == "SELF") cargarPagina(frmentrada, pagina);
								else if (target == "BLANK") {
									pagina = pagina + "&registro=" + registro;
									window.open(pagina, "wOpcion", "toolbar=no, menubar=no, location=no, scrollbars=yes, " + param);
								}
							},
							"Cancelar": function() {
								$(this).dialog("close");
							}
						}
					});
					cajaModalConfirm(data['message'], 500);
				}
				else {
					if (target == "SELF") cargarPagina(frmentrada, pagina);
					else if (target == "BLANK") {
						pagina = pagina + "&registro=" + registro;
						window.open(pagina, "wOpcion", "toolbar=no, menubar=no, location=no, scrollbars=yes, " + param);
					}
				}
		    },'json');
		}
	}
</script>