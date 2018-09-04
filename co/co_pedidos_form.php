<?php
$igv = getVar3("SELECT FactorPorcentaje FROM mastimpuestos WHERE CodImpuesto = '$_PARAMETRO[COIVA]'");
$igvp = $igv / 100 + 1;
if ($opcion == "nuevo") {
	$field_fp = getRecord("SELECT * FROM mastformapago LIMIT 0, 1");
	##	
	$field['CodOrganismo'] = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$field['Estado'] = 'PR';
	$field['CodCentroCosto'] = $_PARAMETRO['COCCOSTO'];
	$field['CodAlmacen'] = $_PARAMETRO['COVTAALMACEN'];
	$field['CodTipoDocumento'] = 'PE';
	$field['CentroCosto'] = getVar3("SELECT Codigo FROM ac_mastcentrocosto WHERE CodCentroCosto = '$_PARAMETRO[COCCOSTO]'");
	$field['CodFormaPago'] = $field_fp['CodFormaPago'];
	$field['FechaDocumento'] = $FechaActual;
	$field['FechaVencimiento'] = formatFechaAMD(fechaFin(formatFechaDMA($FechaActual), $field_fp['DiasVence']));
	$field['ComercialFechaReq'] = addDias($FechaActual,1,'month');
	$field['PreparadoPor'] = $_SESSION["CODPERSONA_ACTUAL"];
	$field['NomPreparadoPor'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
	$field['FechaPreparado'] = $Ahora;
	list($NroDocumento, $UltNroEmitido) = correlativo_documento($field['CodOrganismo'], $field['CodTipoDocumento']);
	$field_detalle = [];
	$field_transacciones = [];
	##
	$_titulo = "Pedidos / Nuevo Registro";
	$accion = "nuevo";
	$disabled_modificar = "";
	$disabled_ver = "";
	$disabled_descuento = "";
	$disabled_item = "";
	$disabled_servicio = "";
	$disabled_opciones = "";
	$read_modificar = "";
	$read_generar = "";
	$display_modificar = "";
	$display_ver = "";
	$display_facturar = "display:none;";
	$display_submit = "";
	$change_generar = 'setMontosVentas();';
	$check_generar = "";
	$label_submit = "Guardar";
	$focus = "CodPersona";
}
elseif ($opcion == "modificar" || $opcion == "ver" || $opcion == "aprobar" || $opcion == "anular") {
	##	consulto datos generales
	$sql = "SELECT
				c.*,
				cc.Codigo AS CentroCosto,
				p1.NomCompleto AS NombreCliente,
				p1.Direccion AS DireccionCliente,
				p2.NomCompleto AS NomPreparadoPor,
				p3.NomCompleto AS NomAprobadoPor
			FROM co_documento c
			INNER JOIN ac_mastcentrocosto cc ON cc.CodCentroCosto = c.CodCentroCosto
			INNER JOIN mastpersonas p1 ON p1.CodPersona = c.CodPersonaCliente
			LEFT JOIN mastpersonas p2 ON p2.CodPersona = c.PreparadoPor
			LEFT JOIN mastpersonas p3 ON p3.CodPersona = c.AprobadoPor
			WHERE c.CodDocumento = '$sel_registros'";
	$field = getRecord($sql);
	##	
	$sql = "SELECT
				dod.*,
				(CASE WHEN dod.TipoDetalle = 'I' THEN i.CodInterno ELSE s.CodInterno END) AS CodInterno,
				(CASE WHEN dod.TipoDetalle = 'I' THEN 0 ELSE 1 END) AS Unidades,
				(CASE WHEN dod.TipoDetalle = 'I' THEN i.StockActual ELSE 0 END) AS StockActual,
				(CASE WHEN dod.TipoDetalle = 'I' THEN i.StockActualEqui ELSE 0 END) AS StockActualEqui,
				(CASE WHEN dod.TipoDetalle = 'I' THEN i.CodUnidadEqui ELSE 'UNI' END) AS CodUnidadEqui,
				(CASE WHEN dod.TipoDetalle = 'I' THEN i.CantidadEqui ELSE 0 END) AS CantidadEqui,
				(CASE WHEN dod.TipoDetalle = 'I' THEN i.CodImpuesto
					  ELSE (CASE WHEN s.FlagExoneradoIva = 'S' THEN 0 ELSE '$_PARAMETRO[COIVA]' END) END) AS CodImpuesto,
				(CASE WHEN dod.TipoDetalle = 'I' THEN i.FactorImpuesto
					  ELSE (CASE WHEN s.FlagExoneradoIva = 'S' THEN 0 ELSE '$igv' END) END) AS FactorImpuesto,
				(CASE WHEN dod.TipoDetalle = 'I' THEN i.MontoVenta ELSE 0 END) AS MontoVenta,
				(CASE WHEN dod.TipoDetalle = 'I' THEN i.MontoVentaUnitario ELSE 0 END) AS MontoVentaUnitario
			FROM co_documentodet dod
			LEFT JOIN vw_lg_inventarioactual_item i ON (
				i.CodItem = dod.CodItem
				AND dod.TipoDetalle = 'I'
			)
			LEFT JOIN co_mastservicios s ON (
				s.CodServicio = dod.CodItem
				AND dod.TipoDetalle = 'S'
			)
			WHERE dod.CodDocumento = '$field[CodDocumento]'";
	$field_detalle = getRecords($sql);
	##	
	$sql = "SELECT
				td.CodItem,
				td.Descripcion,
				td.CantidadPedida,
				td.CantidadRecibida,
				t.CodDocumento,
				t.NroDocumento,
				t.NroInterno,
				t.CodAlmacen
			FROM lg_transacciondetalle td
			INNER JOIN lg_transaccion t ON (
				t.CodOrganismo = td.CodOrganismo
				AND t.CodDocumento = td.CodDocumento
				AND t.NroDocumento = td.NroDocumento
			)
			WHERE
				t.CodOrganismo = '$field[CodOrganismo]'
				AND t.CodTransaccion = '$_PARAMETRO[COTIPOTR]'
				AND t.CodDocumentoReferencia = '$field[CodTipoDocumento]'
				AND t.ReferenciaNroDocumento = '$field[CodDocumento]'";
	$field_transacciones = getRecords($sql);
	##
	if ($opcion == "modificar") {
		$_titulo = "Pedidos / Modificar Registro";
		$accion = "modificar";
		$disabled_modificar = "disabled";
		$disabled_ver = "";
		$disabled_descuento = "";
		$disabled_item = "";
		$disabled_servicio = "";
		$disabled_opciones = "";
		$read_modificar = "readonly";
		$read_generar = "";
		$display_modificar = "display:none;";
		$display_ver = "";
		$display_facturar = "display:none;";
		$display_submit = "";
		$change_generar = 'setMontosVentas();';
		$check_generar = "";
		$label_submit = "Modificar";
		$focus = "Comentarios";
	}
	##
	elseif ($opcion == "aprobar") {
		$field['Estado'] = 'AP';
		$field['AprobadoPor'] = $_SESSION["CODPERSONA_ACTUAL"];
		$field['NomAprobadoPor'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
		$field['FechaAprobado'] = $Ahora;
		##	
		$_titulo = "Pedidos / Aprobar Registro";
		$accion = "aprobar";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$disabled_descuento = "disabled";
		$disabled_item = "disabled";
		$disabled_servicio = "disabled";
		$disabled_opciones = "disabled";
		$read_modificar = "readonly";
		$read_generar = "";
		$display_modificar = "display:none;";
		$display_ver = "display:none;";
		$display_facturar = "display:none;";
		$display_submit = "";
		$change_generar = '';
		$label_submit = "Aprobar";
		$focus = "btSubmit";
	}
	##
	elseif ($opcion == "anular") {
		$_titulo = "Pedidos / Anular Registro";
		$accion = "anular";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$disabled_descuento = "disabled";
		$disabled_item = "disabled";
		$disabled_servicio = "disabled";
		$disabled_opciones = "disabled";
		$read_modificar = "readonly";
		$read_generar = "";
		$display_modificar = "display:none;";
		$display_ver = "display:none;";
		$display_facturar = "display:none;";
		$display_submit = "";
		$change_generar = '';
		$check_generar = "this.checked = !this.checked";
		$label_submit = "Anular";
		$focus = "btSubmit";
	}
	##
	elseif ($opcion == "ver") {
		$_titulo = "Pedidos / Ver Registro";
		$accion = "";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$disabled_descuento = "disabled";
		$disabled_item = "disabled";
		$disabled_servicio = "disabled";
		$disabled_opciones = "disabled";
		$read_modificar = "readonly";
		$read_generar = "";
		$display_modificar = "display:none;";
		$display_ver = "display:none;";
		$display_facturar = "display:none;";
		$display_submit = "display:none;";
		$change_generar = '';
		$check_generar = "this.checked = !this.checked";
		$label_submit = "";
		$focus = "btCancelar";
	}
}
elseif ($opcion == "generar") {
	$filtro_detalle = '';
	foreach ($registros as $row)
	{
		list($CodCotizacion, $Secuencia) = explode('_', $row);
		if ($filtro_detalle) $filtro_detalle .= " OR ";
		$filtro_detalle .= " (cd.CodCotizacion = '$CodCotizacion' AND Secuencia = '$Secuencia')";
	}
	##	consulto datos generales
	$sql = "SELECT
				c.CodOrganismo,
				c.CodEstablecimiento,
				c.CodPersonaCliente,
				c.CodPersonaCliente AS CodPersonaCobrar,
				c.NombreCliente,
				c.DocFiscalCliente,
				c.DireccionCliente,
				c.FechaVencimiento,
				c.FechaVencimiento AS ComercialFechaReq,
				c.CodCentroCosto,
				c.CodPersonaVendedor,
				c.Anio,
				c.MonedaDocumento,
				cl.CodFormaPago,
				cl.CodTipoPago,
				'PE' AS CodTipoDocumento,
				'$_PARAMETRO[COVTAALMACEN]' AS CodAlmacen,
				cl.CodRutaDespacho,
				cl.FormaFactura,
				cl.TipoVenta,
				cc.Codigo AS CentroCosto,
				fp.DiasVence,
				'PR' AS Estado,
				'$FechaActual' AS FechaDocumento,
				'$_SESSION[CODPERSONA_ACTUAL]' AS PreparadoPor,
				'$_SESSION[NOMBRE_USUARIO_ACTUAL]' AS NomPreparadoPor,
				'$Ahora' AS FechaPreparado,
				SUM(CASE WHEN cd.FlagExonIva <> 'S' THEN (cd.MontoTotal + cd.MontoDcto) / $igvp ELSE 0 END) AS MontoAfecto,
				SUM(CASE WHEN cd.FlagExonIva = 'S' THEN (cd.MontoTotal + cd.MontoDcto) ELSE 0 END) AS MontoNoAfecto,
				SUM(cd.MontoDcto) AS MontoDcto
			FROM co_cotizaciondet cd
			INNER JOIN co_cotizacion c ON c.CodCotizacion = cd.CodCotizacion
			INNER JOIN mastcliente cl ON cl.CodPersona = c.CodPersonaCliente
			INNER JOIN ac_mastcentrocosto cc ON cc.CodCentroCosto = c.CodCentroCosto
			LEFT JOIN mastformapago fp ON fp.CodFormaPago = cl.CodFormaPago
			WHERE $filtro_detalle
			GROUP BY CodOrganismo, CodEstablecimiento, CodPersonaCliente";
	$field = getRecord($sql);
	##	
	$sql = "SELECT
				cd.CodCotizacion,
				cd.Secuencia,
				cd.TipoDetalle,
				cd.CodItem,
				cd.Descripcion,
				cd.CodUnidad,
				cd.CodUnidadVenta,
				cd.CantidadPedida,
				cd.PrecioUnit,
				cd.PrecioUnitOriginal,
				cd.PrecioUnitFinal,
				cd.MontoTotalFinal,
				cd.MontoTotal,
				cd.PorcentajeDcto,
				cd.MontoDcto,
				cd.FlagExonIva,
				'PR' AS Estado,
				'$_PARAMETRO[COVTAALMACEN]' AS CodAlmacen,
				(CASE WHEN cd.TipoDetalle = 'I' THEN i.CodInterno ELSE s.CodInterno END) AS CodInterno,
				(CASE WHEN cd.TipoDetalle = 'I' THEN 0 ELSE 1 END) AS Unidades,
				(CASE WHEN cd.TipoDetalle = 'I' THEN i.StockActual ELSE 0 END) AS StockActual,
				(CASE WHEN cd.TipoDetalle = 'I' THEN i.StockActualEqui ELSE 0 END) AS StockActualEqui,
				(CASE WHEN cd.TipoDetalle = 'I' THEN i.CodUnidadEqui ELSE 'UNI' END) AS CodUnidadEqui,
				(CASE WHEN cd.TipoDetalle = 'I' THEN i.CantidadEqui ELSE 0 END) AS CantidadEqui,
				(CASE WHEN cd.TipoDetalle = 'I' THEN i.CodImpuesto
					  ELSE (CASE WHEN s.FlagExoneradoIva = 'S' THEN 0 ELSE '$_PARAMETRO[COIVA]' END) END) AS CodImpuesto,
				(CASE WHEN cd.TipoDetalle = 'I' THEN i.FactorImpuesto
					  ELSE (CASE WHEN s.FlagExoneradoIva = 'S' THEN 0 ELSE '$igv' END) END) AS FactorImpuesto,
				(CASE WHEN cd.TipoDetalle = 'I' THEN i.MontoVenta ELSE 0 END) AS MontoVenta,
				(CASE WHEN cd.TipoDetalle = 'I' THEN i.MontoVentaUnitario ELSE 0 END) AS MontoVentaUnitario
			FROM co_cotizaciondet cd
			LEFT JOIN vw_lg_inventarioactual_item i ON (
				i.CodItem = cd.CodItem
				AND cd.TipoDetalle = 'I'
			)
			LEFT JOIN co_mastservicios s ON (
				s.CodServicio = cd.CodItem
				AND cd.TipoDetalle = 'S'
			)
			WHERE $filtro_detalle
			ORDER BY CodCotizacion, Secuencia";
	$field_detalle = getRecords($sql);
	##	
	$field['Estado'] = 'PR';
	$field['CodCentroCosto'] = $_PARAMETRO['COCCOSTO'];
	$field['CentroCosto'] = getVar3("SELECT Codigo FROM ac_mastcentrocosto WHERE CodCentroCosto = '$_PARAMETRO[COCCOSTO]'");
	$field['CodAlmacen'] = $_PARAMETRO['COVTAALMACEN'];
	$field['FechaDocumento'] = $FechaActual;
	$field['FechaVencimiento'] = formatFechaAMD(fechaFin(formatFechaDMA($FechaActual), $field['DiasVence']));
	$field['ComercialFechaReq'] = addDias($FechaActual,1,'month');
	$field['PreparadoPor'] = $_SESSION["CODPERSONA_ACTUAL"];
	$field['NomPreparadoPor'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
	$field['FechaPreparado'] = $Ahora;
	$field['MontoImpuesto'] = $field['MontoAfecto'] * $igv / 100;
	$field['MontoTotal'] = $field['MontoAfecto'] + $field['MontoNoAfecto'] - $field['MontoDcto'] + $field['MontoImpuesto'];
	##
	$_titulo = "Generaci&oacute;n Autom&aacute;tica de Pedidos";
	$accion = "generar";
	$disabled_modificar = "";
	$disabled_ver = "";
	$disabled_descuento = "disabled";
	$disabled_item = "disabled";
	$disabled_servicio = "disabled";
	$disabled_opciones = "disabled";
	$read_modificar = "";
	$read_generar = "readonly";
	$display_modificar = "display:none;";
	$display_ver = "display:none;";
	$display_facturar = "display:none;";
	$display_submit = "";
	$change_generar = '';
	$check_generar = "this.checked = !this.checked";
	$label_submit = "Generar";
	$focus = "CodPersona";
}
$clkCancelar = "document.getElementById('frmentrada').submit();";
//	------------------------------------
$_width = 900;
if ($origen == 'framemain') $action = '../framemain.php';
elseif (!empty($origen)) $action = "gehen.php?anz=$origen";
else $action = "gehen.php?anz=co_pedidos_lista";
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="<?=$action?>" method="POST" enctype="multipart/form-data" onsubmit="return formSubmitWithReturn('co_pedidos_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
	<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
	<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
	<input type="hidden" name="fCodEstablecimiento" id="fCodEstablecimiento" value="<?=$fCodEstablecimiento?>" />
	<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
	<input type="hidden" name="fFechaDocumentoD" id="fFechaDocumentoD" value="<?=$fFechaDocumentoD?>" />
	<input type="hidden" name="fFechaDocumentoH" id="fFechaDocumentoH" value="<?=$fFechaDocumentoH?>" />
	<input type="hidden" name="fCodPersonaCliente" id="fCodPersonaCliente" value="<?=$fCodPersonaCliente?>" />
	<input type="hidden" name="fNombreCliente" id="fNombreCliente" value="<?=htmlentities($fNombreCliente)?>" />
	<input type="hidden" name="fDocFiscalCliente" id="fDocFiscalCliente" value="<?=$fDocFiscalCliente?>" />
	<input type="hidden" name="CodDocumento" id="CodDocumento" value="<?=$field['CodDocumento']?>" />
	<input type="hidden" name="CodTipoDocumento" id="CodTipoDocumento" value="<?=$field['CodTipoDocumento']?>" />
	<input type="hidden" name="TipoVenta" id="TipoVenta" value="<?=$field['TipoVenta']?>" />
	<input type="hidden" name="CodRutaDespacho" id="CodRutaDespacho" value="<?=$field['CodRutaDespacho']?>" />
	<input type="hidden" name="CodPersonaCobrar" id="CodPersonaCobrar" value="<?=$field['CodPersonaCobrar']?>" />
	<input type="hidden" name="igv" id="igv" value="<?=$igv?>" />

	<table style="width:100%; min-width:<?=$_width?>px;" align="center" cellpadding="0" cellspacing="0">
	    <tr>
	        <td>
	            <div class="header">
		            <ul id="tab">
			            <!-- CSS Tabs -->
			            <li id="li1" onclick="currentTab('tab', this);" class="current">
			            	<a href="#" onclick="mostrarTab('tab', 1, 2);">Información General</a>
			            </li>
			            <li id="li2" onclick="currentTab('tab', this);">
			            	<a href="#" onclick="mostrarTab('tab', 2, 2);">Transacciones de Almacén</a>
			            </li>
		            </ul>
	            </div>
	        </td>
	    </tr>
	</table>

	<div id="tab1" style="display:block;">
		<table style="width:100%; min-width:<?=$_width?>px;" class="tblForm">
			<tr>
		    	<td colspan="4" class="divFormCaption">INFORMACIÓN GENERAL</td>
		    </tr>
		    <tr>
				<td class="tagForm">* Organismo:</td>
				<td>
					<select name="CodOrganismo" id="CodOrganismo" style="width:295px;" <?=$disabled_modificar?>>
						<?=getOrganismos($field['CodOrganismo'], 3)?>
					</select>
				</td>
				<td class="tagForm">Nro. Pedido:</td>
				<td>
		        	<input type="text" name="NroDocumento" id="NroDocumento" value="<?=$field['NroDocumento']?>" style="width:100px; font-weight:bold;" readonly />
				</td>
			</tr>
		    <tr>
				<td class="tagForm">* Establecimiento:</td>
				<td>
					<select name="CodEstablecimiento" id="CodEstablecimiento" style="width:295px;" <?=$disabled_ver?>>
						<?=loadSelect2('co_establecimientofiscal','CodEstablecimiento','Descripcion',$field['CodEstablecimiento'])?>
					</select>
				</td>
				<td class="tagForm">Estado:</td>
				<td>
					<input type="hidden" name="Estado" id="Estado" value="<?=$field['Estado']?>">
		        	<input type="text" value="<?=mb_strtoupper(printValores('documento1-estado',$field['Estado']))?>" style="width:100px; font-weight:bold;" disabled />
				</td>
			</tr>
			<tr>
				<td class="tagForm">* Cliente:</td>
				<td class="gallery clearfix">
					<input type="hidden" name="DocFiscalCliente" id="DocFiscalCliente" value="<?=$field['DocFiscalCliente']?>">
					<input type="text" name="CodPersonaCliente" id="CodPersonaCliente" value="<?=$field['CodPersonaCliente']?>" style="width:66px;" readonly />
					<input type="text" name="NombreCliente" id="NombreCliente" value="<?=htmlentities($field['NombreCliente'])?>" style="width:225px;" readonly />
					<a href="../lib/listas/gehen.php?anz=lista_personas&campo1=CodPersonaCliente&campo2=NombreCliente&campo3=DocFiscalCliente&campo4=DireccionCliente&campo5=FormaFactura&campo6=NomFormaFactura&campo7=TipoVenta&campo8=CodFormaPago&campo9=CodRutaDespacho&campo10=CodPersonaCobrar&campo11=CodPersonaVendedor&ventana=co_pedidos&filtrar=default&FlagClasePersona=S&fEsCliente=S&concepto=80-0003&_APLICACION=<?=$_APLICACION?>&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" style=" <?=$display_modificar?>">
		            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
		            </a>
				</td>
				<td class="tagForm">* Fecha Emisión:</td>
				<td>
					<input type="text" name="FechaDocumento" id="FechaDocumento" value="<?=formatFechaDMA($field['FechaDocumento'])?>" maxlength="10" style="width:75px;" class="datepicker" onkeyup="setFechaDMA(this);" onchange="setDiasVence($('#CodFormaPago').val());" <?=$disabled_ver?> />
				</td>
			</tr>
			<tr>
				<td class="tagForm">Direcci&oacute;n:</td>
				<td>
					<input type="text" name="DireccionCliente" id="DireccionCliente" value="<?=htmlentities($field['DireccionCliente'])?>" style="width:295px;" readonly />
				</td>
				<td class="tagForm">* Fecha Requerida:</td>
				<td>
					<input type="text" name="FechaVencimiento" id="FechaVencimiento" value="<?=formatFechaDMA($field['FechaVencimiento'])?>" maxlength="10" style="width:75px;" class="datepicker" onkeyup="setFechaDMA(this);" <?=$disabled_ver?> />
				</td>
			</tr>
		    <tr>
				<td class="tagForm">Vendedor:</td>
				<td>
					<select name="CodPersonaVendedor" id="CodPersonaVendedor" style="width:295px;" <?=$disabled_ver?>>
						<option value="">&nbsp;</option>
						<?=vendedores($field['CodPersonaVendedor'])?>
					</select>
				</td>
				<td class="tagForm">* C.Costo:</td>
				<td class="gallery clearfix">
					<input type="hidden" name="CodCentroCosto" id="CodCentroCosto" value="<?=$field['CodCentroCosto']?>">
					<input type="text" name="CentroCosto" id="CentroCosto" value="<?=$field['CentroCosto']?>" style="width:75px;" readonly />
					<a href="../lib/listas/gehen.php?anz=lista_centro_costos&campo1=CodCentroCosto&campo2=CentroCosto&ventana=codigo&filtrar=default&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe2]" style=" <?=$display_ver?>">
		            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
		            </a>
				</td>
			</tr>
			<tr>
				<td class="tagForm" rowspan="5">Comentarios:</td>
				<td rowspan="5">
					<textarea name="Comentarios" id="Comentarios" style="width:295px;" rows="6" <?=$disabled_ver?>><?=$field['Comentarios']?></textarea>
				</td>
				<td class="tagForm">* Forma de Pago:</td>
				<td>
					<select name="CodFormaPago" id="CodFormaPago" style="width:125px;" <?=$disabled_ver?> onchange="setDiasVence(this.value);">
						<?=loadSelect2('mastformapago','CodFormaPago','Descripcion',$field['CodFormaPago'],0)?>
					</select>
				</td>
			</tr>
		    <tr>
				<td class="tagForm">Almacén:</td>
				<td>
					<input type="text" name="CodAlmacen" id="CodAlmacen" value="<?=$field['CodAlmacen']?>" style="width:125px;" readonly />
				</td>
			</tr>
			<tr>
				<td class="tagForm">Forma Factura:</td>
				<td>
					<input type="hidden" name="NomFormaFactura" id="NomFormaFactura" value="<?=$field['NomFormaFactura']?>">
					<select name="CodFormaFactura" id="CodFormaFactura" style="width:125px;" <?=$disabled_ver?>>
						<?=getMiscelaneos($field['CodFormaFactura'], "FORMAFACT")?>
					</select>
				</td>
			</tr>
		    <tr>
				<th style="text-align: right;">Informaci&oacute;n Monetaria</th>
				<th></th>
			</tr>
		    <tr>
				<td class="tagForm">* Moneda:</td>
				<td>
					<select name="MonedaDocumento" id="MonedaDocumento" style="width:125px;" <?=$disabled_ver?>>
						<?=loadSelectGeneral("monedas", $field['MonedaDocumento'])?>
					</select>
				</td>
			</tr>
			<tr>
				<th style="text-align: right;">Usuarios</th>
				<th></th>
				<td class="tagForm">Monto Afecto:</td>
				<td>
					<input type="text" name="MontoAfecto" id="MontoAfecto" value="<?=number_format($field['MontoAfecto'],2,',','.')?>" style="width:125px; text-align: right;" class="currency" readonly />
				</td>
			</tr>
			<tr>
				<td class="tagForm">Preparado Por:</td>
				<td>
					<input type="hidden" name="PreparadoPor" id="PreparadoPor" value="<?=$field['PreparadoPor']?>" />
					<input type="text" name="NomPreparadoPor" id="NomPreparadoPor" value="<?=$field['NomPreparadoPor']?>" style="width:185px;" readonly />
					<input type="text" name="FechaPreparado" id="FechaPreparado" value="<?=$field['FechaPreparado']?>" style="width:106px;" readonly />
				</td>
				<td class="tagForm">Monto No Afecto:</td>
				<td>
					<input type="text" name="MontoNoAfecto" id="MontoNoAfecto" value="<?=number_format($field['MontoNoAfecto'],2,',','.')?>" style="width:125px; text-align: right;" class="currency" readonly />
				</td>
			</tr>
			<tr>
				<td class="tagForm">Aprobado Por:</td>
				<td>
					<input type="hidden" name="AprobadoPor" id="AprobadoPor" value="<?=$field['AprobadoPor']?>" />
					<input type="text" name="NomAprobadoPor" id="NomAprobadoPor" value="<?=$field['NomAprobadoPor']?>" style="width:185px;" readonly />
					<input type="text" name="FechaAprobado" id="FechaAprobado" value="<?=$field['FechaAprobado']?>" style="width:106px;" readonly />
				</td>
				<td class="tagForm">
					<?php
					$PorcentajeDcto = $field['MontoDcto'] * 100 / ($field['MontoAfecto'] + $field['MontoNoAfecto'] + $field['MontoImpuesto']);
					?>
					(-) Monto Dcto.
					(<input type="text" id="PorcentajeDcto" value="<?=number_format($PorcentajeDcto,2,',','.')?>%" class="cell2 currency" style="width: 45px; text-align: right;" disabled="disabled" />):
				</td>
				<td>
					<input type="text" name="MontoDcto" id="MontoDcto" value="<?=number_format($field['MontoDcto'],2,',','.')?>" style="width:125px; text-align: right;" class="currency" readonly />
				</td>
			</tr>
		    <tr>
				<td class="tagForm">&Uacute;ltima Modif.:</td>
				<td>
					<input type="text" value="<?=$field['UltimoUsuario']?>" style="width:146px;" disabled="disabled" />
					<input type="text" value="<?=$field['UltimaFecha']?>" style="width:145px" disabled="disabled" />
				</td>
				<td class="tagForm">(+) Imp. Vtas.:</td>
				<td>
					<input type="text" name="MontoImpuesto" id="MontoImpuesto" value="<?=number_format($field['MontoImpuesto'],2,',','.')?>" style="width:125px; text-align: right;" class="currency" readonly />
				</td>
			</tr>
		    <tr>
				<td class="tagForm"></td>
				<td>
				</td>
				<td class="tagForm"><strong>Monto Total:</strong></td>
				<td>
					<input type="text" name="MontoTotal" id="MontoTotal" value="<?=number_format($field['MontoTotal'],2,',','.')?>" style="width:125px; text-align: right; font-weight: bold; font-size: 14px;" class="currency" readonly />
				</td>
			</tr>
		</table>

		<center class="gallery clearfix">
			<a id="a_linea_credito" href="gehen.php?anz=co_cliente_linea_credito&CodPersonaCliente=&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe3]" style="display:none;"></a>

			<input type="submit" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" id="btSubmit" />
			<input type="button" value="Cancelar" style="width:75px;" id="btCancelar" onclick="<?=$clkCancelar?>" /> |
			<input type="button" value="Ver Linea de Crédito" style="width: 120px;" onclick="verLineaCredito();" />
		</center>

		<input type="hidden" id="sel_detalle" />
		<table style="width:100%; min-width:<?=$_width?>px;" class="tblBotones">
			<thead>
				<tr>
					<th class="divFormCaption" colspan="2">DETALLES</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<input type="button" class="btLista" value="Descuento" onclick="modalDescuentoVentas();" <?=$disabled_descuento?> />
					</td>
					<td align="right" class="gallery clearfix">
						<a id="a_detalle_item" href="../lib/listas/gehen.php?anz=lista_lg_items&filtrar=default&ventana=listado_insertar_linea_cotizacion&detalle=detalle&modulo=ajax&accion=detalle_insertar&url=../../co/co_pedidos_ajax.php&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe4]" style="display:none;"></a>
						<a id="a_detalle_servicio" href="../lib/listas/gehen.php?anz=lista_co_servicios&filtrar=default&ventana=listado_insertar_linea_cotizacion&detalle=detalle&modulo=ajax&accion=detalle_insertar&url=../../co/co_pedidos_ajax.php&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe5]" style="display:none;"></a>
						<a id="a_inventario_almacen" href="gehen.php?anz=co_inventario_almacen&CodItem=&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe6]" style="display:none;"></a>

						<input type="button" class="btLista" id="btInsertarItem" value="Item" onclick="$('#a_detalle_item').click();" <?=$disabled_item?> />
						<input type="button" class="btLista" id="btInsertarServicio" value="Servicio" onclick="$('#a_detalle_servicio').click();" <?=$disabled_servicio?> />
						<input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'detalle'); setMontosVentas();" <?=$disabled_opciones?> /> |
						<input type="button" style="width: 125px;" value="Ver Inventario Almacén" onclick="verInventarioAlmacen();" />
					</td>
				</tr>
			</tbody>
		</table>
		<div style="overflow:scroll; height:230px; width:100%; min-width:<?=$_width?>px; margin:auto;">
			<table class="tblLista" style="width:100%; min-width:1900px;">
				<thead>
					<tr>
						<th width="20">#</th>
						<th width="60">Item / Servicio</th>
						<th align="left">Descripci&oacute;n</th>
						<th width="20">I/S</th>
						<th width="100">Almacén</th>
						<th width="60">Stock Actual</th>
						<th width="60">Stock Actual (Venta)</th>
						<th width="40">Uni.</th>
						<th width="40">Uni. Venta</th>
						<th width="75">Cant. Pedida</th>
						<th width="125">Precio Unit.</th>
						<th width="125">Monto Total</th>
						<th width="45">Exon. Imp.</th>
						<th width="125">Precio Unit. s/Dcto.</th>
						<th width="125">Precio Unit.</th>
						<th width="125">Monto</th>
						<th width="40">Dcto. %</th>
						<th width="100">Estado</th>
					</tr>
				</thead>
				<tbody id="lista_detalle">
					<?php
					$nro_detalle = 0;
					$PrecioUnit = 0;
					$MontoTotal = 0;
					$PrecioUnitOriginal = 0;
					$PrecioUnitFinal = 0;
					$MontoTotalFinal = 0;
					foreach ($field_detalle as $f)
					{
						$id = ++$nro_detalle;
						?>
						<tr class="trListaBody" onclick="clk($(this), 'detalle', 'detalle_<?=$id?>');" id="detalle_<?=$id?>">
							<th>
								<input type="hidden" name="detalle_Secuencia[]" id="detalle_Secuencia<?=$id?>" value="<?=$f['Secuencia']?>">
								<input type="hidden" name="detalle_TipoDetalle[]" value="<?=$f['TipoDetalle']?>">
								<input type="hidden" name="detalle_CodCotizacion[]" value="<?=$f['CodCotizacion']?>">
								<input type="hidden" name="detalle_CodUnidadEqui[]" id="detalle_CodUnidadEqui<?=$id?>" value="<?=$f['CodUnidadEqui']?>">
								<input type="hidden" name="detalle_CantidadEqui[]" id="detalle_CantidadEqui<?=$id?>" value="<?=$f['CantidadEqui']?>">
								<input type="hidden" name="detalle_CodImpuesto[]" id="detalle_CodImpuesto<?=$id?>" value="<?=$f['CodImpuesto']?>">
								<input type="hidden" name="detalle_FactorImpuesto[]" id="detalle_FactorImpuesto<?=$id?>" value="<?=$f['FactorImpuesto']?>">
								<input type="hidden" name="detalle_MontoVenta[]" id="detalle_MontoVenta<?=$id?>" value="<?=$f['MontoVenta']?>">
								<input type="hidden" name="detalle_MontoVentaUnitario[]" id="detalle_MontoVentaUnitario<?=$id?>" value="<?=$f['MontoVentaUnitario']?>">
								<?=$nro_detalle?>
							</th>
							<td>
								<input type="hidden" name="detalle_CodItem[]" value="<?=$f['CodItem']?>">
								<input type="text" name="detalle_CodInterno[]" value="<?=$f['CodInterno']?>" class="cell2" style="text-align: center;" readonly>
							</td>
							<td>
								<input type="text" name="detalle_Descripcion[]" value="<?=$f['Descripcion']?>" class="cell2" readonly>
							</td>
							<td align="center"><?=$f['TipoDetalle']?></td>
							<td>
								<input type="text" name="detalle_CodAlmacen[]" value="<?=$f['CodAlmacen']?>" style="text-align:center;" class="cell2" readonly>
							</td>
							<td>
								<input type="text" name="detalle_StockActual[]" value="<?=number_format($f['StockActual'],5,',','.')?>" class="cell2" style="text-align:right;" readonly>
							</td>
							<td>
								<input type="text" name="detalle_StockActualEqui[]" value="<?=number_format($f['StockActualEqui'],5,',','.')?>" class="cell2" style="text-align:right;" readonly>
							</td>
							<td>
								<select name="detalle_CodUnidad[]" id="detalle_CodUnidad<?=$id?>" class="cell" <?=$disabled_ver?>>
									<?=loadSelect2('mastunidades','CodUnidad','CodUnidad',$f['CodUnidad'],1)?>
								</select>
							</td>
							<td>
								<?php if ($f['TipoDetalle'] == 'I') { ?>
									<select name="detalle_CodUnidadVenta[]" id="detalle_CodUnidadVenta<?=$id?>" class="cell" onchange="cambiarUnidad('<?=$id?>');" <?=$disabled_ver?>>
										<?=unidades_item($f['CodItem'],$f['CodUnidadVenta'],0)?>
									</select>
								<?php } else { ?>
									<select name="detalle_CodUnidadVenta[]" id="detalle_CodUnidadVenta<?=$id?>" class="cell" <?=$disabled_ver?>>
										<?=loadSelect2('mastunidades','CodUnidad','CodUnidad',$f['CodUnidadVenta'],1)?>
									</select>
								<?php } ?>
							</td>
							<td>
								<input type="text" name="detalle_CantidadPedida[]" value="<?=number_format($f['CantidadPedida'],5,',','.')?>" class="cell2 currency5" style="text-align:right;" onchange="setMontosVentas();" <?=$disabled_ver?> <?=$read_generar?>>
							</td>
							<td>
								<input type="text" name="detalle_PrecioUnit[]" id="detalle_PrecioUnit<?=$id?>" value="<?=number_format($f['PrecioUnit'],2,',','.')?>" class="cell currency" style="text-align:right;" onchange="setMontosVentas(true, '<?=$id?>');" <?=$disabled_ver?> <?=$read_generar?>>
							</td>
							<td>
								<input type="text" name="detalle_MontoTotal[]" value="<?=number_format($f['MontoTotal'],2,',','.')?>" class="cell2" style="text-align:right;" readonly>
							</td>
							<td align="center">
								<input type="checkbox" name="detalle_FlagExonIva[]" value="S" onchange="<?=$change_generar?>" onclick="<?=$check_generar?>" <?=chkFlag($f['FlagExonIva'])?> <?=$disabled_ver?> />
							</td>
							<td>
								<input type="text" name="detalle_PrecioUnitOriginal[]" id="detalle_PrecioUnitOriginal<?=$id?>" value="<?=number_format($f['PrecioUnitOriginal'],2,',','.')?>" class="cell2" style="text-align:right;" readonly>
							</td>
							<td>
								<input type="text" name="detalle_PrecioUnitFinal[]" id="detalle_PrecioUnitFinal<?=$id?>" value="<?=number_format($f['PrecioUnitFinal'],5,',','.')?>" class="cell2" style="text-align:right;" readonly>
							</td>
							<td>
								<input type="text" name="detalle_MontoTotalFinal[]" id="detalle_MontoTotalFinal<?=$id?>" value="<?=number_format($f['MontoTotalFinal'],2,',','.')?>" class="cell2" style="text-align:right;" readonly>
							</td>
							<td>
								<input type="hidden" name="detalle_MontoDcto[]" id="detalle_MontoDcto<?=$id?>" value="<?=number_format($f['MontoDcto'],2,',','.')?>" class="">
								<input type="text" name="detalle_PorcentajeDcto[]" id="detalle_PorcentajeDcto<?=$id?>" value="<?=number_format($f['PorcentajeDcto'],2,',','.')?>" class="cell2" style="text-align:right;" readonly>
							</td>
							<td>
								<input type="hidden" name="detalle_Estado[]" value="<?=$f['Estado']?>">
								<input type="text" value="<?=mb_strtoupper(printValores('documento3-estado-detalle',$f['Estado']))?>" class="cell2" style="text-align:center;" readonly="readonly">
							</td>
						</tr>
						<?php
						$PrecioUnit += $f['PrecioUnit'];
						$MontoTotal += $f['MontoTotal'];
						$PrecioUnitOriginal += $f['PrecioUnitOriginal'];
						$PrecioUnitFinal += $f['PrecioUnitFinal'];
						$MontoTotalFinal += $f['MontoTotalFinal'];
					}
					?>
				</tbody>

				<tfoot>
					<tr>
						<th colspan="10"></th>
						<th align="right" id="thPrecioUnit"><?=number_format($PrecioUnit,2,',','.')?></th>
						<th align="right" id="thMontoTotal"><?=number_format($MontoTotal,2,',','.')?></th>
						<th></th>
						<th align="right" id="thPrecioUnitOriginal"><?=number_format($PrecioUnitOriginal,2,',','.')?></th>
						<th align="right" id="thPrecioUnitFinal"><?=number_format($PrecioUnitFinal,2,',','.')?></th>
						<th align="right" id="thMontoTotalFinal"><?=number_format($MontoTotalFinal,2,',','.')?></th>
						<th colspan="2"></th>
					</tr>
				</tfoot>
			</table>
		</div>
		<input type="hidden" id="nro_detalle" value="<?=$nro_detalle?>" />
		<input type="hidden" id="can_detalle" value="<?=$nro_detalle?>" />
	</div>

	<div id="tab2" style="display:none;">
		<table style="width:100%; min-width:<?=$_width?>px;" class="tblBotones">
			<thead>
				<tr>
					<th class="divFormCaption" colspan="2">TRANSACCIONES DE ALMACÉN</th>
				</tr>
			</thead>
		</table>
		<div style="overflow:scroll; height:350px; width:100%; min-width:<?=$_width?>px; margin:auto;">
			<table class="tblLista" style="width:100%; min-width:<?=$_width?>px;">
				<thead>
					<tr>
						<th width="20">#</th>
						<th width="60">Código</th>
						<th align="left">Descripci&oacute;n</th>
						<th width="75">Cantidad</th>
						<th width="150">Transacción</th>
						<th width="150">Almacén</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$nro_detalle = 0;
					foreach ($field_transacciones as $f)
					{
						++$nro_detalle;
						?>
						<tr class="trListaBody">
							<th><?=$nro_detalle?></th>
							<td align="center"><?=$f['CodItem']?></td>
							<td><?=htmlentities($f['Descripcion'])?></td>
							<td align="right"><?=number_format($f['CantidadPedida'],5,',','.')?></td>
							<td align="center"><?=$f['CodDocumento']?>-<?=$f['NroInterno']?></td>
							<td align="center"><?=$f['CodAlmacen']?></td>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>
		</div>
	</div>
</form>
<div style="width:100%; min-width:<?=$_width?>px;" class="divMsj">Campos Obligatorios *</div>

<script type="text/javascript">
	function verLineaCredito() {
		if ($('#CodPersonaCliente').val()) {
			var url = "gehen.php?anz=co_cliente_linea_credito&CodPersona="+$('#CodPersonaCliente').val()+"&iframe=true&width=100%&height=350";
			$('#a_linea_credito').attr('href', url);
			$('#a_linea_credito').click();
		} else {
			cajaModal('Debe seleccionar el Cliente');
		}
	}

	function setDiasVence(CodFormaPago) {
		$.post('co_pedidos_ajax.php', 'modulo=ajax&accion=setDiasVence&CodFormaPago='+CodFormaPago+'&FechaDocumento='+$('#FechaDocumento').val(), function(data) {
			$('#FechaVencimiento').val(data['FechaVencimiento']);
	    }, 'json');
	}
</script>