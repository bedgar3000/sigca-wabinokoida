<?php
$igv = getVar3("SELECT FactorPorcentaje FROM mastimpuestos WHERE CodImpuesto = '$_PARAMETRO[COIVA]'");
if ($opcion == "nuevo") {
	$field_fp = getRecord("SELECT * FROM mastformapago LIMIT 0, 1");
	##	
	$field['Estado'] = 'PR';
	$field['CodCentroCosto'] = $_PARAMETRO['COCCOSTO'];
	$field['CentroCosto'] = getVar3("SELECT Codigo FROM ac_mastcentrocosto WHERE CodCentroCosto = '$_PARAMETRO[COCCOSTO]'");
	$field['FechaDocumento'] = $FechaActual;
	$field['FechaVencimiento'] = formatFechaAMD(fechaFin(formatFechaDMA($FechaActual), 30));
	$field['PreparadoPor'] = $_SESSION["CODPERSONA_ACTUAL"];
	$field['NomPreparadoPor'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
	$field['FechaPreparado'] = $Ahora;
	$field_detalle = [];
	##	
	$_titulo = "Cotizaci&oacute;n / Nuevo Registro";
	$accion = "nuevo";
	$disabled_modificar = "";
	$disabled_ver = "";
	$read_modificar = "";
	$display_modificar = "";
	$display_ver = "";
	$display_submit = "";
	$label_submit = "Guardar";
	$focus = "CodPersona";
}
elseif ($opcion == "modificar" || $opcion == "ver" || $opcion == "aprobar" || $opcion == "anular" || $opcion == "copiar") {
	##	consulto datos generales
	$sql = "SELECT
				c.*,
				cc.Codigo AS CentroCosto,
				p1.NomCompleto AS NombreCliente,
				p1.Direccion AS DireccionCliente,
				p2.NomCompleto AS NomPreparadoPor,
				p3.NomCompleto AS NomAprobadoPor
			FROM co_cotizacion c
			INNER JOIN ac_mastcentrocosto cc ON cc.CodCentroCosto = c.CodCentroCosto
			INNER JOIN mastpersonas p1 ON p1.CodPersona = c.CodPersonaCliente
			LEFT JOIN mastpersonas p2 ON p2.CodPersona = c.PreparadoPor
			LEFT JOIN mastpersonas p3 ON p3.CodPersona = c.AprobadoPor
			WHERE c.CodCotizacion = '$sel_registros'";
	$field = getRecord($sql);
	##	detalle
	$sql = "SELECT
				cd.*,
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
				(CASE WHEN cd.TipoDetalle = 'I' THEN i.MontoVentaUnitario ELSE 0 END) AS MontoVentaUnitario,
				CONCAT_WS('',do.CodTipoDocumento,do.NroDocumento) AS NroDocumento
			FROM co_cotizaciondet cd
			LEFT JOIN vw_lg_inventarioactual_item i ON (
				i.CodItem = cd.CodItem
				AND cd.TipoDetalle = 'I'
			)
			LEFT JOIN co_mastservicios s ON (
				s.CodServicio = cd.CodItem
				AND cd.TipoDetalle = 'S'
			)
			LEFT JOIN co_documento do ON do.CodDocumento = cd.CodDocumento
			WHERE cd.CodCotizacion = '$field[CodCotizacion]'";
	$field_detalle = getRecords($sql);
	##
	if ($opcion == "modificar") {
		$_titulo = "Cotizaci&oacute;n / Modificar Registro";
		$accion = "modificar";
		$disabled_modificar = "disabled";
		$disabled_ver = "";
		$read_modificar = "readonly";
		$display_modificar = "display:none;";
		$display_ver = "";
		$display_submit = "";
		$label_submit = "Modificar";
		$focus = "Comentarios";
	}
	##
	elseif ($opcion == "copiar") {
		$field['Estado'] = 'PR';
		$field['FechaDocumento'] = $FechaActual;
		$field['FechaVencimiento'] = formatFechaAMD(fechaFin(formatFechaDMA($FechaActual), 30));
		$field['PreparadoPor'] = $_SESSION["CODPERSONA_ACTUAL"];
		$field['NomPreparadoPor'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
		$field['FechaPreparado'] = $Ahora;
		##	
		$_titulo = "Cotizaci&oacute;n / Nuevo Registro";
		$accion = "nuevo";
		$disabled_modificar = "";
		$disabled_ver = "";
		$read_modificar = "";
		$display_modificar = "";
		$display_ver = "";
		$display_submit = "";
		$label_submit = "Guardar";
		$focus = "CodPersona";
	}
	##
	elseif ($opcion == "aprobar") {
		$field['Estado'] = 'AP';
		$field['AprobadoPor'] = $_SESSION["CODPERSONA_ACTUAL"];
		$field['NomAprobadoPor'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
		$field['FechaAprobado'] = $Ahora;
		##	
		$_titulo = "Cotizaci&oacute;n / Aprobar Registro";
		$accion = "aprobar";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$read_modificar = "readonly";
		$display_modificar = "display:none;";
		$display_ver = "display:none;";
		$display_submit = "";
		$label_submit = "Aprobar";
		$focus = "btSubmit";
	}
	##
	elseif ($opcion == "anular") {
		$_titulo = "Cotizaci&oacute;n / Anular Registro";
		$accion = "anular";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$read_modificar = "readonly";
		$display_modificar = "display:none;";
		$display_ver = "display:none;";
		$display_submit = "";
		$label_submit = "Anular";
		$focus = "btSubmit";
	}
	##
	elseif ($opcion == "ver") {
		$_titulo = "Cotizaci&oacute;n / Ver Registro";
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
$_width = 900;
if ($origen == 'framemain') $action = '../framemain.php';
elseif (!empty($origen)) $action = "gehen.php?anz=$origen";
else $action = "gehen.php?anz=co_cotizacion_lista";
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="<?=$action?>" method="POST" enctype="multipart/form-data" onsubmit="return formSubmitWithReturn('co_cotizacion_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
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
	<input type="hidden" name="CodCotizacion" id="CodCotizacion" value="<?=$field['CodCotizacion']?>" />
	<input type="hidden" name="igv" id="igv" value="<?=$igv?>" />

	<table style="width:100%; min-width:<?=$_width?>px;" class="tblForm">
		<tr>
	    	<td colspan="4" class="divFormCaption">Datos Generales</td>
	    </tr>
	    <tr>
			<td class="tagForm">* Organismo:</td>
			<td>
				<select name="CodOrganismo" id="CodOrganismo" style="width:350px;" <?=$disabled_modificar?>>
					<?=getOrganismos($field['CodOrganismo'], 3)?>
				</select>
			</td>
			<td class="tagForm">Nro. Cotizaci&oacute;n:</td>
			<td>
	        	<input type="text" name="NroCotizacion" id="NroCotizacion" value="<?=$field['NroCotizacion']?>" style="width:125px; font-weight:bold;" readonly />
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Establecimiento:</td>
			<td>
				<select name="CodEstablecimiento" id="CodEstablecimiento" style="width:350px;" <?=$disabled_ver?>>
					<?=loadSelect2('co_establecimientofiscal','CodEstablecimiento','Descripcion',$field['CodEstablecimiento'])?>
				</select>
			</td>
			<td class="tagForm">Estado:</td>
			<td>
				<input type="hidden" name="Estado" id="Estado" value="<?=$field['Estado']?>">
	        	<input type="text" value="<?=mb_strtoupper(printValores('cotizacion-estado',$field['Estado']))?>" style="width:125px; font-weight:bold;" disabled />
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Cliente:</td>
			<td class="gallery clearfix">
				<input type="text" name="CodPersonaCliente" id="CodPersonaCliente" value="<?=$field['CodPersonaCliente']?>" style="width:66px;" readonly />
				<input type="text" name="NombreCliente" id="NombreCliente" value="<?=htmlentities($field['NombreCliente'])?>" style="width:280px;" readonly />
				<a href="../lib/listas/gehen.php?anz=lista_personas&campo1=CodPersonaCliente&campo2=NombreCliente&campo3=DocFiscalCliente&campo4=DireccionCliente&campo5=CodPersonaVendedor&ventana=co_cotizacion&filtrar=default&FlagClasePersona=S&fEsCliente=S&concepto=80-0003&_APLICACION=<?=$_APLICACION?>&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" style=" <?=$display_modificar?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
			<td class="tagForm">Doc. Fiscal:</td>
			<td>
	        	<input type="text" name="DocFiscalCliente" id="DocFiscalCliente" value="<?=$field['DocFiscalCliente']?>" style="width:125px;" readonly />
			</td>
		</tr>
		<tr>
			<td class="tagForm" rowspan="2">Direcci&oacute;n:</td>
			<td rowspan="2">
				<textarea name="DireccionCliente" id="DireccionCliente" style="width:350px; height: 40px;" readonly><?=htmlentities($field['DireccionCliente'])?></textarea>
			</td>
			<td class="tagForm">* Fecha Documento:</td>
			<td>
				<input type="text" name="FechaDocumento" id="FechaDocumento" value="<?=formatFechaDMA($field['FechaDocumento'])?>" maxlength="10" style="width:75px;" class="datepicker" onkeyup="setFechaDMA(this);" <?=$disabled_ver?> />
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Fecha Vencimiento:</td>
			<td>
				<input type="text" name="FechaVencimiento" id="FechaVencimiento" value="<?=formatFechaDMA($field['FechaVencimiento'])?>" maxlength="10" style="width:75px;" class="datepicker" onkeyup="setFechaDMA(this);" <?=$disabled_ver?> />
			</td>
		</tr>
		<tr>
			<td class="tagForm">Vendedor:</td>
			<td>
				<select name="CodPersonaVendedor" id="CodPersonaVendedor" style="width:350px;" <?=$disabled_ver?>>
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
			<td class="tagForm" rowspan="3">Comentarios:</td>
			<td rowspan="3">
				<textarea name="Comentarios" id="Comentarios" style="width:350px;" rows="5" <?=$disabled_ver?>><?=htmlentities($field['Comentarios'])?></textarea>
			</td>
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
			<td class="tagForm">Monto Afecto:</td>
			<td>
				<input type="text" name="MontoAfecto" id="MontoAfecto" value="<?=number_format($field['MontoAfecto'],2,',','.')?>" style="width:125px; text-align: right;" class="currency" readonly />
			</td>
		</tr>
		<tr>
			<th style="text-align: right;">Usuarios</th>
			<th></th>
			<td class="tagForm">Monto No Afecto:</td>
			<td>
				<input type="text" name="MontoNoAfecto" id="MontoNoAfecto" value="<?=number_format($field['MontoNoAfecto'],2,',','.')?>" style="width:125px; text-align: right;" class="currency" readonly />
			</td>
		</tr>
		<tr>
			<td class="tagForm">Preparado Por:</td>
			<td>
				<input type="hidden" name="PreparadoPor" id="PreparadoPor" value="<?=$field['PreparadoPor']?>" />
				<input type="text" name="NomPreparadoPor" id="NomPreparadoPor" value="<?=$field['NomPreparadoPor']?>" style="width:240px;" readonly />
				<input type="text" name="FechaPreparado" id="FechaPreparado" value="<?=$field['FechaPreparado']?>" style="width:106px;" readonly />
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
			<td class="tagForm">Aprobado Por:</td>
			<td>
				<input type="hidden" name="AprobadoPor" id="AprobadoPor" value="<?=$field['AprobadoPor']?>" />
				<input type="text" name="NomAprobadoPor" id="NomAprobadoPor" value="<?=$field['NomAprobadoPor']?>" style="width:240px;" readonly />
				<input type="text" name="FechaAprobado" id="FechaAprobado" value="<?=$field['FechaAprobado']?>" style="width:106px;" readonly />
			</td>
			<td class="tagForm">(+) Imp. Vtas.:</td>
			<td>
				<input type="text" name="MontoImpuesto" id="MontoImpuesto" value="<?=number_format($field['MontoImpuesto'],2,',','.')?>" style="width:125px; text-align: right;" class="currency" readonly />
			</td>
		</tr>
	    <tr>
			<td class="tagForm">&Uacute;ltima Modif.:</td>
			<td>
				<input type="text" value="<?=$field['UltimoUsuario']?>" style="width:146px;" disabled="disabled" />
				<input type="text" value="<?=$field['UltimaFecha']?>" style="width:145px" disabled="disabled" />
			</td>
			<td class="tagForm"><strong>Monto Total:</strong></td>
			<td>
				<input type="text" name="MontoTotal" id="MontoTotal" value="<?=number_format($field['MontoTotal'],2,',','.')?>" style="width:125px; text-align: right; font-weight: bold; font-size: 14px;" class="currency" readonly />
			</td>
		</tr>
	</table>

	<center>
		<input type="submit" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" id="btSubmit" />
		<input type="button" value="Cancelar" style="width:75px;" id="btCancelar" onclick="<?=$clkCancelar?>" />
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
					<input type="button" class="btLista" value="Descuento" onclick="modalDescuentoVentas();" <?=$disabled_ver?> />
				</td>
				<td align="right" class="gallery clearfix">
					<a id="a_detalle_item" href="../lib/listas/gehen.php?anz=lista_lg_items&filtrar=default&fFlagDisponible=S&ventana=listado_insertar_linea_cotizacion&detalle=detalle&modulo=ajax&accion=detalle_insertar&url=../../co/co_cotizacion_ajax.php&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe3]" style="display:none;"></a>
					<a id="a_detalle_servicio" href="../lib/listas/gehen.php?anz=lista_co_servicios&filtrar=default&ventana=listado_insertar_linea_cotizacion&detalle=detalle&modulo=ajax&accion=detalle_insertar&url=../../co/co_cotizacion_ajax.php&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe4]" style="display:none;"></a>

					<input type="button" class="btLista" id="btInsertarItem" value="Item" onclick="$('#a_detalle_item').click();" <?=$disabled_ver?> />
					<input type="button" class="btLista" id="btInsertarServicio" value="Servicio" onclick="$('#a_detalle_servicio').click();" <?=$disabled_ver?> />
					<input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'detalle'); setMontosVentas();" <?=$disabled_ver?> />
				</td>
			</tr>
		</tbody>
	</table>
	<div style="overflow:scroll; height:230px; width:100%; min-width:<?=$_width?>px; margin:auto;">
		<table class="tblLista" style="width:100%; min-width:2000px;">
			<thead>
				<tr>
					<th width="20">#</th>
					<th width="60">Item / Servicio</th>
					<th align="left">Descripci&oacute;n</th>
					<th width="20">I/S</th>
					<th width="20">Prec. Esp.</th>
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
					<th width="150">Pedido Relacionado</th>
					<th width="20">#</th>
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
							<input type="hidden" name="detalle_CodUnidadEqui[]" id="detalle_CodUnidadEqui<?=$id?>" value="<?=$f['CodUnidadEqui']?>">
							<input type="hidden" name="detalle_CantidadEqui[]" id="detalle_CantidadEqui<?=$id?>" value="<?=$f['CantidadEqui']?>">
							<input type="hidden" name="detalle_CodImpuesto[]" id="detalle_CodImpuesto<?=$id?>" value="<?=$f['CodImpuesto']?>">
							<input type="hidden" name="detalle_FactorImpuesto[]" id="detalle_FactorImpuesto<?=$id?>" value="<?=$f['FactorImpuesto']?>">
							<input type="hidden" name="detalle_MontoVenta[]" id="detalle_MontoVenta<?=$id?>" value="<?=$f['MontoVenta']?>">
							<input type="hidden" name="detalle_MontoVentaUnitario[]" id="detalle_MontoVentaUnitario<?=$id?>" value="<?=$f['MontoVentaUnitario']?>">
							<input type="hidden" name="detalle_PrecioEspecial[]" id="detalle_PrecioEspecial<?=$id?>" value="<?=$f['PrecioEspecial']?>">
							<input type="hidden" name="detalle_PrecioEspecialVta[]" id="detalle_PrecioEspecialVta<?=$id?>" value="<?=$f['PrecioEspecialVta']?>">
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
						<td align="center">
							<input type="checkbox" value="S" <?=chkFlag($f['FlagPrecioEspecial'])?> onclick="setFlagPrecioEspecial(this.checked, '<?=$id?>');" <?=$disabled_ver?> />
							<input type="hidden" name="detalle_FlagPrecioEspecial[]" id="detalle_FlagPrecioEspecial<?=$id?>" value="<?=$f['FlagPrecioEspecial']?>">
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
							<input type="text" name="detalle_CantidadPedida[]" value="<?=number_format($f['CantidadPedida'],5,',','.')?>" class="cell2 currency5" style="text-align:right;" onchange="setMontosVentas();" <?=$disabled_ver?>>
						</td>
						<td>
							<input type="text" name="detalle_PrecioUnit[]" id="detalle_PrecioUnit<?=$id?>" value="<?=number_format($f['PrecioUnit'],2,',','.')?>" class="cell currency" style="text-align:right;" onchange="setMontosVentas(true, '<?=$id?>');" <?=$disabled_ver?>>
						</td>
						<td>
							<input type="text" name="detalle_MontoTotal[]" value="<?=number_format($f['MontoTotal'],2,',','.')?>" class="cell2" style="text-align:right;" readonly>
						</td>
						<td align="center">
							<input type="checkbox" name="detalle_FlagExonIva[]" value="S" onchange="setMontosVentas();" <?=chkFlag($f['FlagExonIva'])?> <?=$disabled_ver?>  onclick="this.checked=!this.checked" />
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
							<input type="text" name="detalle_NroDocumento[]" value="<?=$f['NroDocumento']?>" style="text-align: cent{er;" class="cell2" readonly="readonly">
						</td>
						<td>
							<input type="text" name="detalle_SecDocumento[]" value="<?=$f['SecDocumento']?>" class="cell2" style="text-align:center;" readonly="readonly">
						</td>
						<td>
							<input type="hidden" name="detalle_Estado[]" value="<?=$f['Estado']?>">
							<input type="text" value="<?=mb_strtoupper(printValores('cotizacion-estado-detalle',$f['Estado']))?>" class="cell2" style="text-align:center;" readonly="readonly">
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
					<th colspan="9"></th>
					<th align="right" id="thPrecioUnit"><?=number_format($PrecioUnit,2,',','.')?></th>
					<th align="right" id="thMontoTotal"><?=number_format($MontoTotal,2,',','.')?></th>
					<th></th>
					<th align="right" id="thPrecioUnitOriginal"><?=number_format($PrecioUnitOriginal,2,',','.')?></th>
					<th align="right" id="thPrecioUnitFinal"><?=number_format($PrecioUnitFinal,2,',','.')?></th>
					<th align="right" id="thMontoTotalFinal"><?=number_format($MontoTotalFinal,2,',','.')?></th>
					<th colspan="4"></th>
				</tr>
			</tfoot>
		</table>
	</div>
	<input type="hidden" id="nro_detalle" value="<?=$nro_detalle?>" />
	<input type="hidden" id="can_detalle" value="<?=$nro_detalle?>" />
</form>
<div style="width:100%; min-width:<?=$_width?>px;" class="divMsj">Campos Obligatorios *</div>

<script type="text/javascript">
	function setFlagPrecioEspecial(checked, id) {
		var CodUnidad = $('#detalle_CodUnidad'+id).val();
		var CodUnidadVenta = $('#detalle_CodUnidadVenta'+id).val();
		var PrecioEspecial = $('#detalle_PrecioEspecial'+id).val();
		var PrecioEspecialVta = $('#detalle_PrecioEspecialVta'+id).val();
		var PrecioUnit = setNumero($('#detalle_PrecioUnit'+id).val());

		if (checked) {
			$('#detalle_FlagPrecioEspecial'+id).val('S');
			if (CodUnidad == CodUnidadVenta)
				$('#detalle_PrecioUnit'+id).val(PrecioEspecial).formatCurrency();
			else
				$('#detalle_PrecioUnit'+id).val(PrecioEspecialVta).formatCurrency();
		} else {
			$('#detalle_FlagPrecioEspecial'+id).val('N');
		}
		setMontosVentas();
	}
</script>
