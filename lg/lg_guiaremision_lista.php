<?php
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$fFechaDocumentoD = formatFechaDMA($PeriodoActual.'-01');
	$fFechaDocumentoH = formatFechaDMA($FechaActual);
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "NroSerie,NroGuia";
}
//	------------------------------------
if ($lista == "listar") {
	$_titulo = "Guias de Remisión";
	$_btNuevo = "";
	$_btModificar = "";
	$_btConfirmar = "display:none;";
	$_btAnular = "";
}
elseif ($lista == "confirmar") {
	$_titulo = "Guias de Remisión / Confirmar";
	$_btNuevo = "display:none;";
	$_btModificar = "display:none;";
	$_btConfirmar = "";
	$_btAnular = "";
}
//	------------------------------------
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (gr.NroGuia LIKE '%$fBuscar%'
					  OR gr.NroSerie LIKE '%$fBuscar%'
					  OR gr.NombreDestino LIKE '%$fBuscar%'
					  OR gr.NroFactura LIKE '%$fBuscar%'
					  OR CONCAT_WS('-',gr.RefCodTransaccion,gr.RefNroTransaccion) LIKE '%$fBuscar%'
					  OR gr.NombreTrans LIKE '%$fBuscar%'
					  OR tt.Descripcion LIKE '%$fBuscar%'
					  OR a.Descripcion LIKE '%$fBuscar%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (gr.Estado = '$fEstado')"; } else $dEstado = "disabled";
if ($fEstadoFacturacion != "") { $cEstadoFacturacion = "checked"; $filtro.=" AND (gr.EstadoFacturacion = '$fEstadoFacturacion')"; } else $dEstadoFacturacion = "disabled";
if ($fEstadoDespacho != "") { $cEstadoDespacho = "checked"; $filtro.=" AND (gr.EstadoDespacho = '$fEstadoDespacho')"; } else $dEstadoDespacho = "disabled";
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (gr.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fCodAlmacen != "") { $cCodAlmacen = "checked"; $filtro.=" AND (gr.CodAlmacen = '".$fCodAlmacen."')"; } else $dCodAlmacen = "disabled";
if ($fCodChofer != "") { $cCodChofer = "checked"; $filtro.=" AND (gr.CodChofer = '".$fCodChofer."')"; } else $dCodChofer = "disabled";
if ($fFechaDocumentoD != "" || $fFechaDocumentoH != "") {
	$cFechaDocumento = "checked";
	if ($fFechaDocumentoD != "") $filtro.=" AND (gr.FechaDocumento >= '".formatFechaAMD($fFechaDocumentoD)."')";
	if ($fFechaDocumentoH != "") $filtro.=" AND (gr.FechaDocumento <= '".formatFechaAMD($fFechaDocumentoH)."')";
} else $dFechaDocumento = "disabled";
//	------------------------------------
$_width = 900;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lg_guiaremision_lista" method="post" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />

	<!--FILTRO-->
	<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
		<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
			<tr>
				<td align="right">Organismo:</td>
				<td>
					<input type="checkbox" <?=$cCodOrganismo?> onclick="this.checked=!this.checked;" />
					<select name="fCodOrganismo" id="fCodOrganismo" style="width:275px;" <?=$dCodOrganismo?>>
						<?=getOrganismos($fCodOrganismo, 3);?>
					</select>
				</td>
				<td align="right">Buscar:</td>
				<td>
					<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
					<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:150px;" <?=$dBuscar?> />
				</td>
				<td align="right">Estado: </td>
				<td>
		            <input type="checkbox" <?=$cEstado?> onclick="chkFiltro(this.checked, 'fEstado');" />
		            <select name="fEstado" id="fEstado" style="width:100px;" <?=$dEstado?>>
		                <option value="">&nbsp;</option>
		                <?=loadSelectValores("guia-remision-estado", $fEstado, 0)?>
		            </select>
				</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td align="right">Chofer:</td>
				<td>
					<input type="checkbox" <?=$cCodChofer?> onclick="chkFiltro(this.checked, 'fCodChofer');" />
					<select name="fCodChofer" id="fCodChofer" style="width:275px;" <?=$dCodChofer?>>
						<option value="">&nbsp;</option>
						<?=choferes($fCodChofer)?>
					</select>
				</td>
				<td align="right">Almacen:</td>
				<td>
					<input type="checkbox" <?=$cCodAlmacen?> onclick="chkFiltro(this.checked, 'fCodAlmacen');" />
					<select name="fCodAlmacen" id="fCodAlmacen" style="width:150px;" <?=$dCodAlmacen?>>
						<option value="">&nbsp;</option>
						<?=loadSelect2('lg_almacenmast','CodAlmacen','Descripcion',$fCodAlmacen)?>
					</select>
				</td>
				<td align="right">Estado Fact.: </td>
				<td>
		            <input type="checkbox" <?=$cEstadoFacturacion?> onclick="chkFiltro(this.checked, 'fEstadoFacturacion');" />
		            <select name="fEstadoFacturacion" id="fEstadoFacturacion" style="width:100px;" <?=$dEstadoFacturacion?>>
		                <option value="">&nbsp;</option>
		                <?=loadSelectValores("guia-remision-estado-factura", $fEstadoFacturacion, 0)?>
		            </select>
				</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td align="right">Fecha:</td>
				<td>
					<input type="checkbox" <?=$cFechaDocumento?> onclick="chkCampos2(this.checked, ['fFechaDocumentoD','fFechaDocumentoH']);" />
					<input type="text" name="fFechaDocumentoD" id="fFechaDocumentoD" value="<?=$fFechaDocumentoD?>" <?=$dFechaDocumento?> maxlength="10" style="width:70px;" class="datepicker" onkeyup="setFechaDMA(this);" /> -
		            <input type="text" name="fFechaDocumentoH" id="fFechaDocumentoH" value="<?=$fFechaDocumentoH?>" <?=$dFechaDocumento?> maxlength="10" style="width:70px;" class="datepicker" onkeyup="setFechaDMA(this);" />
		        </td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td align="right">Estado Despacho: </td>
				<td>
		            <input type="checkbox" <?=$cEstadoDespacho?> onclick="chkFiltro(this.checked, 'fEstadoDespacho');" />
		            <select name="fEstadoDespacho" id="fEstadoDespacho" style="width:100px;" <?=$dEstadoDespacho?>>
		                <option value="">&nbsp;</option>
		                <?=loadSelectValores("guia-remision-estado-despacho", $fEstadoDespacho, 0)?>
		            </select>
				</td>
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
	        <td align="right" class="gallery clearfix">
	        	<a href="pagina.php?iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe2]" style="display:none;" id="a_reporte"></a>

	            <input type="button" value="Nuevo" style="width:75px; <?=$_btNuevo?>" class="insert" onclick="cargarPagina(this.form, 'gehen.php?anz=lg_guiaremision_form&opcion=nuevo&origen=lg_guiaremision_lista');" />
            	<input type="button" value="Modificar" style="width:75px; <?=$_btModificar?>" class="update" onclick="cargarOpcionValidar3(this.form, $('#sel_registros').val(), 'lg_guiaremision_ajax.php', 'modulo=validar&accion=modificar', 'gehen.php?anz=lg_guiaremision_form&opcion=modificar', 'SELF', '');" />
	            <input type="button" value="Despacho de Guias" style="width:110px; <?=$_btConfirmar?>" class="update" onclick="despachoValidar();" />
	            <input type="button" value="Confirmar Entrega" style="width:110px; <?=$_btConfirmar?>" class="update" onclick="cargarOpcionValidar3(this.form, $('#sel_registros').val(), 'lg_guiaremision_ajax.php', 'modulo=validar&accion=confirmar', 'gehen.php?anz=lg_guiaremision_form&opcion=confirmar', 'SELF', '');" />
	            <input type="button" value="Anular" style="width:75px; <?=$_btAnular?>" class="update" onclick="cargarOpcionValidar3(this.form, $('#sel_registros').val(), 'lg_guiaremision_ajax.php', 'modulo=validar&accion=anular', 'gehen.php?anz=lg_guiaremision_form&opcion=anular', 'SELF', '');" />
	            <input type="button" value="Ver" style="width:75px;" class="ver" onclick="cargarOpcion2(this.form, 'gehen.php?anz=lg_guiaremision_form&opcion=ver', 'SELF', '', $('#sel_registros').val());" />

	            <input type="button" value="Imprimir" style="width:75px;" class="ver" onclick="abrirReporteVal('a_reporte', 'lg_guiaremision_pdf', '', '', $('#sel_registros'), 0, this.form);" />
	        </td>
	    </tr>
	</table>

	<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px;">
		<table class="tblLista" style="width:100%; min-width:1500px;">
			<thead>
			    <tr>
			        <th width="50" onclick="order('NroSerie')">Serie</th>
			        <th width="75" onclick="order('NroGuia')">Guia Remisión</th>
			        <th width="75" onclick="order('FechaDocumento')">Fecha Documento</th>
			        <th style="min-width: 200px;" align="left" onclick="order('NombreDestino')">Destinatario</th>
			        <th width="100" onclick="order('Estado')">Estado</th>
			        <th width="150" onclick="order('TipoTransaccion')">Transacción</th>
			        <th width="75" onclick="order('Transaccion')">Transacción</th>
			        <th width="75" onclick="order('CodAlmacen')">Almacen Origen</th>
			        <th width="100" onclick="order('EstadoFacturacion')">Estado Facturación</th>
			        <th width="75" onclick="order('NroFactura')">Factura</th>
			        <th style="min-width: 200px;" align="left" onclick="order('NombreTrans')">Transportista</th>
			    </tr>
		    </thead>
		    
		    <tbody id="lista_registros">
			<?php
			//	consulto todos
			$sql = "SELECT *
					FROM lg_guiaremision gr
					INNER JOIN lg_almacenmast a ON a.CodAlmacen = gr.CodAlmacen
					LEFT JOIN lg_tipotransaccion tt ON tt.CodTransaccion = gr.RefTipoTransaccion
					WHERE 1 $filtro";
			$rows_total = getNumRows3($sql);
			//	consulto lista
			$sql = "SELECT
						gr.*,
						CONCAT_WS('-', RefCodTransaccion, RefNroTransaccion) AS Transaccion,
						a.Descripcion AS AlmacenOrigen,
						tt.Descripcion AS TipoTransaccion
					FROM lg_guiaremision gr
					INNER JOIN lg_almacenmast a ON a.CodAlmacen = gr.CodAlmacen
					LEFT JOIN lg_tipotransaccion tt ON tt.CodTransaccion = gr.RefTipoTransaccion
					WHERE 1 $filtro
					ORDER BY $fOrderBy
					LIMIT ".intval($limit).", ".intval($maxlimit);
			$field = getRecords($sql);
			$rows_lista = count($field);
			foreach($field as $f) {
				$id = $f['CodGuia'];
				?>
				<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
					<td align="center"><?=$f['NroSerie']?></td>
					<td align="center"><?=$f['NroGuia']?></td>
					<td align="center"><?=formatFechaAMD($f['FechaDocumento'])?></td>
					<td><?=htmlentities($f['NombreDestino'])?></td>
					<td align="center"><?=printValores('guia-remision-estado',$f['Estado'])?></td>
					<td><?=htmlentities($f['TipoTransaccion'])?></td>
					<td align="center"><?=$f['Transaccion']?></td>
					<td align="center"><?=$f['CodAlmacen']?></td>
					<td align="center"><?=printValores('guia-remision-estado-factura',$f['EstadoFacturacion'])?></td>
					<td align="center"><?=$f['NroFactura']?></td>
					<td><?=htmlentities($f['NombreTrans'])?></td>
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
	function despachoValidar() {
		if ($('#sel_registros').val() == "") cajaModal("Debe seleccionar un registro");
		else {
			$.post('lg_guiaremision_ajax.php', "modulo=validar&accion=despacho&codigo="+$('#sel_registros').val(), function(data) {
				if (data.trim() != "") cajaModal(data.trim());
				else despachoModal($('#sel_registros').val());
		    });
		}
	}
	function despachoModal(CodGuia) {
		$.post('lg_guiaremision_despacho_form.php', 'CodGuia='+CodGuia, function(data) {
			$("#cajaModal").dialog({
				buttons: {
					"Aceptar": function() {
						$(this).dialog("close");
						despachoGuia();
					},
					"Cancelar": function() {
						$(this).dialog("close");
					}
				}
			});
			$("#cajaModal").dialog({ title: "<img src='../imagenes/info.png' width='24' align='absmiddle' />Fecha de Entrega", width: 350 });
			$("#cajaModal").html(data);
			$('#cajaModal').dialog('open');
			inicializar();
	    });
	}
	function despachoGuia() {
		$.post('lg_guiaremision_ajax.php', 'modulo=formulario&accion=despacho&'+$('#frmdespacho').serialize(), function(data) {
			if (data.trim() != "") cajaModal(data.trim());
			else document.getElementById('frmentrada').submit();
	    });
	}
</script>

<?php echo "aqui.... $FlagImprimirDocumento .... $CodImprimirDocumento"; ?>
<?php if ($FlagImprimirDocumento == 'S') { ?>
	<script type="text/javascript">
		$(document).ready(function() {
			abrirReporteVal3('a_reporte', 'lg_guiaremision_pdf', '', '', $('#sel_registros'), '<?=$CodImprimirDocumento?>', document.getElementById('frmentrada'));
		});
	</script>
<?php } ?>
