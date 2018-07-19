<?php
if (empty($ventana)) $ventana = "selLista";
//	------------------------------------
if ($filtrar == "default") {
	$fEstado = 'A';
	if (empty($fDigitos)) $fDigitos = 2;
	else {
		if ($fDigitos > 2) $fDigitos = $fDigitos - 2;
	}
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "CodInterno";
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (s.CodInterno LIKE '%".$fBuscar."%'
					  OR s.Descripcion LIKE '%".$fBuscar."%'
					  OR md.Descripcion LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (s.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fDigitos != "") { $cDigitos = "checked"; $filtro.=" AND (s.Digitos = '".$fDigitos."')"; } else $dDigitos = "disabled";
//	------------------------------------
$_titulo = "Maestro de Servicios";
$_width = 900;
?>
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lista_co_servicios" method="post">
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
	<input type="hidden" name="ventana" id="ventana" value="<?=$ventana?>" />
	<input type="hidden" name="detalle" id="detalle" value="<?=$detalle?>" />
	<input type="hidden" name="modulo" id="modulo" value="<?=$modulo?>" />
	<input type="hidden" name="accion" id="accion" value="<?=$accion?>" />
	<input type="hidden" name="url" id="url" value="<?=$url?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />

	<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
		<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
			<tr>
				<td align="right" width="100">Buscar:</td>
				<td>
					<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
					<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:169px;" <?=$dBuscar?> />
				</td>
				<td align="right">Digitos: </td>
				<td>
		            <input type="checkbox" <?=$cDigitos?> onclick="chkFiltro(this.checked, 'fDigitos');" />
		            <select name="fDigitos" id="fDigitos" style="width:100px;" <?=$dDigitos?>>
		                <option value="">&nbsp;</option>
		                <?=loadSelectGeneral("servicios-digitos",$fDigitos)?>
		            </select>
				</td>
				<td align="right">Estado: </td>
				<td>
		            <input type="checkbox" <?=$cEstado?> onclick="this.checked=!this.checked;" />
		            <select name="fEstado" id="fEstado" style="width:100px;" <?=$dEstado?>>
		                <?=loadSelectGeneral("ESTADO", $fEstado, 1)?>
		            </select>
				</td>
		        <td align="right"><input type="submit" value="Buscar"></td>
			</tr>
		</table>
	</div>
	<div class="sep"></div>
	<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px;">
		<table class="tblLista" style="width:100%; min-width:<?=$_width?>px;">
			<thead>
			    <tr>
			        <th width="125" onclick="order('CodInterno')">C&oacute;digo</th>
			        <th align="left" onclick="order('Descripcion')">Descripci&oacute;n</th>
			        <th width="125" onclick="order('NomClasificacion')">Clasificación</th>
			        <th width="45" onclick="order('FlagExoneradoIva')">Exon.</th>
			        <th width="45" onclick="order('FlagAfectoDescuento')">Desc.</th>
			        <th width="125" align="right" onclick="order('PrecioVenta')">Precio Venta</th>
			    </tr>
		    </thead>
		    
		    <tbody>
			<?php
			//	consulto todos
			$sql = "SELECT *
					FROM co_mastservicios S
					LEFT JOIN mastmiscelaneosdet md ON (
						md.CodDetalle = s.CodClasificacion
						AND md.CodMaestro = 'CLASIFSERV'
					)
					WHERE 1 $filtro";
			$rows_total = getNumRows3($sql);
			//	consulto lista
			$sql = "SELECT
						s.*,
						md.Descripcion AS NomClasificacion
					FROM co_mastservicios S
					LEFT JOIN mastmiscelaneosdet md ON (
						md.CodDetalle = s.CodClasificacion
						AND md.CodMaestro = 'CLASIFSERV'
					)
					WHERE 1 $filtro
					ORDER BY $fOrderBy
					LIMIT ".intval($limit).", ".intval($maxlimit);
			$field = getRecords($sql);
			$rows_lista = count($field);
			foreach($field as $f) {
				$id = $f['CodServicio'];
				if ($ventana == 'listado_insertar_linea') {
					?>
		            <tr class="trListaBody" onClick="listado_insertar_linea('<?=$detalle?>','modulo=<?=$modulo?>&accion=<?=$accion?>&CodServicio=<?=$f['CodServicio']?>','<?=$f['CodServicio']?>','<?=$url?>');">
		            <?php
				}
				elseif ($ventana == 'listado_insertar_linea_cotizacion') {
					?>
		            <tr class="trListaBody" onClick="<?=$ventana?>('<?=$detalle?>','modulo=<?=$modulo?>&accion=<?=$accion?>&CodServicio=<?=$f['CodServicio']?>','<?=$f['CodServicio']?>','<?=$url?>');">
		            <?php
				}
				elseif ($ventana == 'listado_insertar_linea_precios') {
					?>
		            <tr class="trListaBody" onClick="<?=$ventana?>('<?=$detalle?>','modulo=<?=$modulo?>&accion=<?=$accion?>&CodServicio=<?=$f['CodServicio']?>','<?=$f['CodServicio']?>','<?=$url?>');">
		            <?php
				}
				elseif ($ventana == 'CodInterno') {
					?>
		            <tr class="trListaBody" onClick="selLista(['<?=$f['CodServicio']?>','<?=$f['Descripcion']?>','<?=$f['CodInterno']?>','UNI','UNI'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>','<?=$campo4?>','<?=$campo5?>']);">
		            <?php

				}
				elseif ($ventana == 'maestro') {
					?>
		            <tr class="trListaBody" onClick="selLista(['<?=$f['CodServicio']?>','<?=$f['CodInterno']?>'], ['<?=$campo1?>','<?=$campo2?>']);">
		            <?php

				}
				else {
					?>
		            <tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['CodServicio']?>','<?=$f['Descripcion']?>'], ['<?=$campo1?>','<?=$campo2?>']);">
		            <?php

				}
				?>
					<td><?=$f['CodInterno']?></td>
					<td><?=htmlentities($f['Descripcion'])?></td>
					<td align="center"><?=htmlentities($f['NomClasificacion'])?></td>
					<td align="center"><?=printFlag2($f['FlagExoneradoIva'])?></td>
					<td align="center"><?=printFlag2($f['FlagAfectoDescuento'])?></td>
					<td align="right"><?=number_format($f['PrecioVenta'],2,',','.')?></td>
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
			$("#cajaModal").dialog({ title: "<img src='../../imagenes/info.png' width='24' align='absmiddle' />Agregar Servicio", width: 500 });
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
					parent.setMontosVentas();
				}
			}
		});
	}
</script>