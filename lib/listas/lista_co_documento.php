<?php
if (empty($ventana)) $ventana = "selLista";
if (empty($fFlagCobranza)) $fFlagCobranza = '';
//	------------------------------------
if ($filtrar == "default") {
	$fEstado = 'A';
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "CodTipoDocumento,NroDocumento";
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (do.CodTipoDocumento LIKE '%$fBuscar%'
					  OR do.NroDocumento LIKE '%$fBuscar%')";
} else $dBuscar = "disabled";
if ($fCodTipoDocumento != "") { $cCodTipoDocumento = "checked"; $filtro.=" AND (do.CodTipoDocumento = '$fCodTipoDocumento')"; } else $dCodTipoDocumento = "disabled";
if ($fCodPersonaCliente != "") { $filtro.=" AND (do.CodPersonaCliente = '".$fCodPersonaCliente."')"; }
if ($fFlagCobranza) { $filtro .= " AND do.FlagDocumento = 'S' AND do.Estado = 'PE'"; }
if ($ventana == 'documento') {
	$filtro .= " AND (td.CodClasificacion <> 'DC')";
}
//	------------------------------------
$_titulo = "Documentos Pendiente de Cobranza";
$_width = 900;
?>
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lista_co_documento" method="post">
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
	<input type="hidden" name="fFlagCobranza" id="fFlagCobranza" value="<?=$fFlagCobranza?>" />
	<input type="hidden" name="fCodPersonaCliente" id="fCodPersonaCliente" value="<?=$fCodPersonaCliente?>" />

	<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
		<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
			<tr>
				<td align="right">Tipo Documento: </td>
				<td>
		            <input type="checkbox" <?=$cCodTipoDocumento?> onclick="chkCampos(this.checked, 'fCodTipoDocumento');" />
		            <select name="fCodTipoDocumento" id="fCodTipoDocumento" style="width:200px;" <?=$dCodTipoDocumento?>>
		            	<option value="">&nbsp;</option>
		                <?=loadSelect2('co_tipodocumento','CodTipoDocumento','Descripcion')?>
		            </select>
				</td>
				<td align="right" width="100">Buscar:</td>
				<td>
					<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
					<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:200px;" <?=$dBuscar?> />
				</td>
		        <td align="right"><input type="submit" value="Buscar"></td>
			</tr>
		</table>
	</div>
	<div class="sep"></div>
	<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px;">
		<table class="tblLista" style="width:100%; min-width:1950px;">
			<thead>
			    <tr>
			        <th width="35" onclick="order('CodTipoDocumento')">Tipo</th>
			        <th width="75" onclick="order('NroDocumento')">N&uacute;mero</th>
			        <th width="75" onclick="order('FechaDocumento')">Fecha</th>
			        <th width="75" onclick="order('FechaVencimiento')">Fecha Venc.</th>
			        <th style="min-width: 250px;" align="left" onclick="order('NombreCliente')">Nombre del Cliente</th>
			        <th width="150" onclick="order('MontoTotal')">Monto Total</th>
			        <th style="min-width: 400px;" align="left" onclick="order('Comentarios')">Comentarios</th>
			        <th width="75" onclick="order('FechaVencimiento')">Fecha Requerida</th>
			        <th width="75" onclick="order('FechaAprobado')">Fecha Aprobación</th>
			        <th width="75" onclick="order('NomFormaFactura')">Forma Facturación</th>
			        <th width="75" onclick="order('NomTipoVenta')">Tipo Venta</th>
			    </tr>
		    </thead>
		    
		    <tbody>
			<?php
			//	consulto todos
			$sql = "SELECT *
					FROM co_documento do
					INNER JOIN co_tipodocumento td ON td.CodTipoDocumento = do.CodTipoDocumento
					LEFT JOIN co_cotizacion co ON co.CodCotizacion = do.CodCotizacion
					LEFT JOIN co_documento do2 On do2.CodDocumento = do.CodPedido
					LEFT JOIN mastmiscelaneosdet md1 ON (
						md1.CodDetalle = do.FormaFactura
						AND md1.CodMaestro = 'FORMAFACT'
					)
					LEFT JOIN mastmiscelaneosdet md2 ON (
						md2.CodDetalle = do.FormaFactura
						AND md2.CodMaestro = 'TIPOVENTA'
					)
					WHERE 1 $filtro
					GROUP BY do.CodDocumento";
			$rows_total = getNumRows3($sql);
			//	consulto lista
			$sql = "SELECT
						do.*,
						co.NroCotizacion,
						do2.CodTipoDocumento AS CodTipoDocumentoPedido,
						do2.CodDocumento AS CodPedidoOriginal,
						do2.NroDocumento AS NroPedido,
						md1.Descripcion AS NomFormaFactura,
						md2.Descripcion AS NomTipoVenta
					FROM co_documento do
					INNER JOIN co_tipodocumento td ON td.CodTipoDocumento = do.CodTipoDocumento
					LEFT JOIN co_cotizacion co ON co.CodCotizacion = do.CodCotizacion
					LEFT JOIN co_documento do2 On do2.CodPedido = do.CodDocumento
					LEFT JOIN mastmiscelaneosdet md1 ON (
						md1.CodDetalle = do.FormaFactura
						AND md1.CodMaestro = 'FORMAFACT'
					)
					LEFT JOIN mastmiscelaneosdet md2 ON (
						md2.CodDetalle = do.TipoVenta
						AND md2.CodMaestro = 'TIPOVENTA'
					)
					WHERE 1 $filtro
					GROUP BY do.CodDocumento
					ORDER BY $fOrderBy
					LIMIT ".intval($limit).", ".intval($maxlimit);
			$field = getRecords($sql);
			$rows_lista = count($field);
			foreach($field as $f) {
				$id = $f['CodDocumento'];
				if ($ventana == 'listado_insertar_linea') {
					?>
		            <tr class="trListaBody" onClick="listado_insertar_linea('<?=$detalle?>','modulo=<?=$modulo?>&accion=<?=$accion?>&CodDocumento=<?=$f['CodDocumento']?>','<?=$f['CodDocumento']?>','<?=$url?>');">
		            <?php
				}
				elseif ($ventana == 'listado_insertar_linea_cobranza') {
					?>
		            <tr class="trListaBody" onClick="<?=$ventana?>('<?=$detalle?>','modulo=<?=$modulo?>&accion=<?=$accion?>&CodDocumento=<?=$f['CodDocumento']?>','<?=$f['CodDocumento']?>','<?=$url?>');" id="documento_<?=$id?>">
		            <?php
				}
				elseif ($ventana == 'documento') {
					?>
		            <tr class="trListaBody" onClick="selListaDocumento(['<?=$f['CodPedidoOriginal']?>','<?=$f['CodTipoDocumentoPedido']?><?=$f['NroPedido']?>','<?=$f['CodDocumento']?>','<?=$f['NroDocumento']?>','<?=$f['CodCotizacion']?>','<?=$f['NroCotizacion']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>','<?=$campo4?>','<?=$campo5?>','<?=$campo6?>']);">
		            <?php
				}
				else {
					?>
		            <tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['CodDocumento']?>','<?=$f['Descripcion']?>'], ['<?=$campo1?>','<?=$campo2?>']);">
		            <?php
				}
				?>
					<td align="center"><?=$f['CodTipoDocumento']?></td>
					<td align="center"><?=$f['NroDocumento']?></td>
					<td align="center"><?=formatFechaAMD($f['FechaDocumento'])?></td>
					<td align="center"><?=formatFechaAMD($f['FechaVencimiento'])?></td>
					<td><?=htmlentities($f['NombreCliente'])?></td>
					<td align="right"><?=number_format($f['MontoTotal'],2,',','.')?></td>
					<td><?=htmlentities($f['Comentarios'])?></td>
					<td align="center"><?=formatFechaAMD($f['FechaVencimiento'])?></td>
					<td align="center"><?=formatFechaAMD(substr($f['FechaAprobado'],0,10))?></td>
					<td align="center"><?=$f['NomFormaFactura']?></td>
					<td align="center"><?=$f['NomTipoVenta']?></td>
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
	<?php if ($ventana == 'listado_insertar_linea_cobranza') { ?>
		function listado_insertar_linea_cobranza(detalle, data, id, url) {
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
				data: "nro_detalles="+nro+"&can_detalles="+can+"&"+data,
				async: false,
				success: function(resp) {
					if (parent.document.getElementById(idtr)) cajaModal("Registro ya insertado", "error_lista", 400);
					else {
						nro_detalles.val(nro);
						can_detalles.val(can);
						lista_detalles.append(resp);
						inicializarParent();
						parent.setMontosDocumentos();
						parent.$.prettyPhoto.close();
					}
				}
			});
		}
	<?php } elseif ($ventana == 'documento') { ?>
		function selListaDocumento(valores, inputs) {
			$.post('../../co/co_documento_ajax.php', 'modulo=ajax&accion=detalle_documento&CodDocumento='+valores[2], function(data) {
				if (inputs) {
					for(var i=0; i<inputs.length; i++) {
						if (parent.$("#"+inputs[i]).length > 0) parent.$("#"+inputs[i]).val(valores[i]);
					}
				}
				parent.$('#lista_detalle').html(data);
				parent.setMontosVentas();
				parent.$('#nro_detalle').val(parent.$('#lista_detalle').length);
				parent.$('#can_detalle').val(parent.$('#lista_detalle').length);
				parent.$.prettyPhoto.close();
		    });
		}
	<?php } ?>
</script>