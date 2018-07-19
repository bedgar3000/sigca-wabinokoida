<?php
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$fFechaDocumentoD = formatFechaDMA($PeriodoActual.'-01');
	$fFechaDocumentoH = formatFechaDMA($FechaActual);
}
//	------------------------------------
$filtro = '';
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (do.NroDocumento LIKE '%$fBuscar%'
					  OR do.NombreCliente LIKE '%$fBuscar%'
					  OR do.Comentarios LIKE '%$fBuscar%'
					  OR td.Descripcion LIKE '%$fBuscar%'
					  OR md.Descripcion LIKE '%$fBuscar%')";
} else $dBuscar = "disabled";
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (do.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fCodAlmacen != "") { $cCodAlmacen = "checked"; $filtro.=" AND (do.CodAlmacen = '".$fCodAlmacen."')"; } else $dCodAlmacen = "disabled";
if ($fCodTipoDocumento != "") { $cCodTipoDocumento = "checked"; $filtro.=" AND (do.CodTipoDocumento = '".$fCodTipoDocumento."')"; } else $dCodTipoDocumento = "disabled";
if ($fFechaDocumentoD != "" || $fFechaDocumentoH != "") {
	$cFechaDocumento = "checked";
	if ($fFechaDocumentoD != "") $filtro.=" AND (do.FechaDocumento >= '".formatFechaAMD($fFechaDocumentoD)."')";
	if ($fFechaDocumentoH != "") $filtro.=" AND (do.FechaDocumento <= '".formatFechaAMD($fFechaDocumentoH)."')";
} else $dFechaDocumento = "disabled";
if ($fCodPersonaCliente != "") { $cCodPersonaCliente = "checked"; $filtro.=" AND (do.CodPersonaCliente = '".$fCodPersonaCliente."')"; } else $dCodPersonaCliente = "visibility:hidden;";
//	------------------------------------
$_titulo = "Despacho de Ventas";
$_width = 900;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lg_almacen_despacho_ventas_lista" method="post" autocomplete="off">
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
					<select name="fCodOrganismo" id="fCodOrganismo" style="width:225px;" <?=$dCodOrganismo?>>
						<?=getOrganismos($fCodOrganismo, 3);?>
					</select>
				</td>
				<td align="right">Fecha:</td>
				<td>
					<input type="checkbox" <?=$cFechaDocumento?> onclick="chkCampos2(this.checked, ['fFechaDocumentoD','fFechaDocumentoH']);" />
					<input type="text" name="fFechaDocumentoD" id="fFechaDocumentoD" value="<?=$fFechaDocumentoD?>" <?=$dFechaDocumento?> maxlength="10" style="width:70px;" class="datepicker" onkeyup="setFechaDMA(this);" /> -
		            <input type="text" name="fFechaDocumentoH" id="fFechaDocumentoH" value="<?=$fFechaDocumentoH?>" <?=$dFechaDocumento?> maxlength="10" style="width:70px;" class="datepicker" onkeyup="setFechaDMA(this);" />
		        </td>
				<td align="right">Tipo Documento:</td>
				<td>
					<input type="checkbox" <?=$cCodTipoDocumento?> onclick="chkFiltro(this.checked, 'fCodTipoDocumento');" />
					<select name="fCodTipoDocumento" id="fCodTipoDocumento" style="width:150px;" <?=$dCodTipoDocumento?>>
						<option value="">&nbsp;</option>
						<?=loadSelect2('co_tipodocumento','CodTipoDocumento','Descripcion',$fCodTipoDocumento,10)?>
					</select>
				</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td align="right">Cliente:</td>
				<td class="gallery clearfix">
					<input type="checkbox" <?=$cCodPersonaCliente?> onclick="ckLista(this.checked, ['fCodPersonaCliente','fNombreCliente','fDocFiscalCliente'], ['aCodPersonaCliente']);" />
					<input type="hidden" name="fDocFiscalCliente" id="fDocFiscalCliente" value="<?=$fDocFiscalCliente?>">
					<input type="hidden" name="fCodPersonaCliente" id="fCodPersonaCliente" value="<?=$fCodPersonaCliente?>" />
					<input type="text" name="fNombreCliente" id="fNombreCliente" value="<?=htmlentities($fNombreCliente)?>" style="width:225px;" readonly />
					<a href="../lib/listas/gehen.php?anz=lista_personas&campo1=fCodPersonaCliente&campo2=fNombreCliente&campo3=fDocFiscalCliente&ventana=filtro&filtrar=default&FlagClasePersona=S&fEsCliente=S&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" style=" <?=$dCodPersonaCliente?>" id="aCodPersonaCliente">
		            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
		            </a>
				</td>
				<td align="right">Almacen:</td>
				<td>
					<input type="checkbox" <?=$cCodAlmacen?> onclick="chkFiltro(this.checked, 'fCodAlmacen');" />
					<select name="fCodAlmacen" id="fCodAlmacen" style="width:150px;" <?=$dCodAlmacen?>>
						<option value="">&nbsp;</option>
						<?=loadSelect2('lg_almacenmast','CodAlmacen','Descripcion',$fCodAlmacen)?>
					</select>
				</td>
				<td align="right">Buscar:</td>
				<td>
					<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
					<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:150px;" <?=$dBuscar?> />
				</td>
		        <td align="right"><input type="submit" value="Buscar"></td>
			</tr>
		</table>
	</div>
	<div class="sep"></div>

	<!--CABECERA-->
	<input type="hidden" name="sel_registros" id="sel_registros" />
	<table class="tblBotones" style="width:100%; min-width:<?=$_width?>px;">
	    <tr>
	        <td></td>
	        <td align="right">&nbsp;</td>
	    </tr>
	</table>
	<div style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:225px;">
		<table class="tblLista" style="width:100%; min-width:1400px;">
			<thead>
			    <tr>
			        <th width="125">Tipo</th>
			        <th width="75">Documento #</th>
			        <th width="75">Fecha Documento</th>
			        <th width="75">Fecha Requerida</th>
			        <th style="min-width: 200px;" align="left">Cliente</th>
			        <th width="75"># Items</th>
			        <th style="min-width: 200px;" align="left">Comentarios</th>
			        <th width="100">Forma Facturación</th>
			        <th width="100">Estado</th>
			    </tr>
		    </thead>
		    
		    <tbody id="lista_registros">
			<?php
			//	consulto lista
			$sql = "SELECT
						do.CodDocumento,
						do.NroDocumento,
						do.FechaDocumento,
						do.FechaVencimiento,
						do.NombreCliente,
						do.Comentarios,
						do.Estado,
						td.Descripcion AS TipoDocumento,
						md.Descripcion AS NomFormaFactura,
						(SELECT COUNT(*)
						 FROM co_documentodet dod2
						 WHERE dod2.CodDocumento = do.CodDocumento) AS Items
					FROM co_documento do
					INNER JOIN co_tipodocumento td ON td.CodTipoDocumento = do.CodTipoDocumento
					LEFT JOIN mastmiscelaneosdet md ON (
						md.CodDetalle = do.FormaFactura
						AND md.CodMaestro = 'FORMAFACT'
					)
					WHERE do.FlagDespacho = 'S' $filtro
					ORDER BY NroDocumento";
			$field = getRecords($sql);
			$rows_lista = count($field);
			foreach($field as $f) {
				$id = $f['CodDocumento'];
				?>
				<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>'); get_documento_detalle('<?=$f['CodDocumento']?>');">
					<td><?=htmlentities($f['TipoDocumento'])?></td>
					<td align="center"><?=$f['NroDocumento']?></td>
					<td align="center"><?=formatFechaAMD($f['FechaDocumento'])?></td>
					<td align="center"><?=formatFechaAMD($f['FechaVencimiento'])?></td>
					<td><?=htmlentities($f['NombreCliente'])?></td>
					<td align="center"><?=$f['Items']?></td>
					<td><?=htmlentities($f['Comentarios'])?></td>
					<td align="center"><?=htmlentities($f['NomFormaFactura'])?></td>
					<td align="center"><?=printValores('co_documento-estado',$f['Estado'])?></td>
				</tr>
				<?php
			}
			?>
		    </tbody>
		</table>
	</div>

	<!--DETALLE-->
	<input type="hidden" name="sel_detalle" id="sel_detalle" />
	<table class="tblBotones" style="width:100%; min-width:<?=$_width?>px;">
	    <tr>
	        <td></td>
	        <td align="right">
            	<input type="button" value="Despachar" style="width:75px;" onclick="cargarOpcionDespacho(this.form, 'gehen.php?anz=lg_almacen_despacho_ventas_form&opcion=despacho-ventas&origen=', 'SELF', '', 'detalle[]', 'sel_detalle', 1)" />
	        </td>
	    </tr>
	</table>
	<div style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:225px;">
		<table class="tblLista" style="width:100%; min-width:800px;">
			<thead>
			    <tr>
			        <th width="35">#</th>
			        <th width="60">Item</th>
			        <th style="min-width: 300px;" align="left">Descripci&oacute;n</th>
			        <th width="35">Uni.</th>
			        <th width="75">Cantidad Pedida</th>
			        <th width="75">Cantidad Pendiente</th>
			        <th width="75">Stock Actual</th>
			        <th width="75">Stock Actual (Venta)</th>
			    </tr>
		    </thead>
		    
		    <tbody id="lista_detalle">
		    </tbody>
		</table>
	</div>
</form>
<script type="text/javascript">
	function get_documento_detalle(CodDocumento) {
		$('#lista_detalle').html('Cargando...');
		$.post('lg_almacen_despacho_ventas_ajax.php', "modulo=ajax&accion=documento_detalle&CodDocumento="+CodDocumento, function(data) {
			$('#lista_detalle').html(data);
	    });
	}
	function cargarOpcionDespacho(form, pagina, target, param, nombre, idregistro, multi, frmentrada) {
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
			$.post('lg_almacen_despacho_ventas_ajax.php', 'modulo=validar&accion=despacho-ventas&'+$('form').serialize(), function(data) {
				if (data['status'] == 'error') {
					cajaModal(data['message']);
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