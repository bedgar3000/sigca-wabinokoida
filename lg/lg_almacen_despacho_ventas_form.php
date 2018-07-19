<?php
if ($opcion == "despacho-ventas") {
	##	documento
	$sql = "SELECT * FROM co_documento WHERE CodDocumento = '$sel_registros'";
	$field_documento = getRecord($sql);
	##	transaccion
	$sql = "SELECT * FROM lg_tipotransaccion WHERE CodTransaccion = '$_PARAMETRO[COTIPOTR]'";
	$field_transaccion = getRecord($sql);
	##	
	$field = [
		'CodOrganismo' => $field_documento['CodOrganismo'],
		'CodDependencia' => getVar3("SELECT CodDependencia FROM ac_mastcentrocosto WHERE CodCentroCosto = '$field_documento[CodCentroCosto]'"),
		'CodCentroCosto' => $field_documento['CodCentroCosto'],
		'CodAlmacen' => $field_documento['CodAlmacen'],
		'FechaDocumento' => $FechaActual,
		'CodTransaccion' => $field_transaccion['CodTransaccion'],
		'Transaccion' => $field_transaccion['Descripcion'],
		'CodDocumento' => $field_transaccion['TipoDocGenerado'],
		'NroDocumento' => '',
		'CodDocumentoReferencia' => $field_documento['CodTipoDocumento'],
		'NroDocumentoReferencia' => $field_documento['NroDocumento'],
		'ReferenciaNroDocumento' => $field_documento['CodDocumento'],
		'DocumentoReferencia' => $field_documento['NroDocumento'],
		'DocumentoReferenciaInterno' => $field_documento['NroDocumento'],
		'ReferenciaAnio' => $field_documento['Anio'],
		'Anio' => $field_documento['Anio'],
		'NotaEntrega' => '',
		'FlagManual' => 'N',
		'FlagPendiente' => 'S',
		'CodPersonaTrans' => '',
		'DocFiscalTrans' => '',
		'NombreTrans' => '',
		'CodChofer' => '',
		'NroBultos' => '',
		'IngresadoPor' => $_SESSION['CODPERSONA_ACTUAL'],
		'RecibidoPor' => '',
		'NomRecibidoPor' => '',
		'Estado' => 'CO',
	];
	##	
	$Secuencia = 0;
	$field_detalle = [];
	foreach ($detalle as $row)
	{
		list($_CodDocumento, $_Secuencia) = explode('_', $row);

		$sql = "SELECT
					dod.*,
					i.CodInterno,
					(dod.CantidadPedida - dod.CantidadEntregada) AS CantidadPendiente,
					i.StockActual,
					i.StockActualEqui
				FROM co_documentodet dod
				INNER JOIN vw_lg_inventarioactual_item i ON i.CodItem = dod.CodItem
				WHERE
					dod.CodDocumento = '$_CodDocumento'
					 AND dod.Secuencia = '$_Secuencia'";
		$field_documento_detalle = getRecord($sql);
		##	
		$field_detalle[] = [
			'Secuencia' => ++$Secuencia,
			'CodItem' => $field_documento_detalle['CodItem'],
			'CodInterno' => $field_documento_detalle['CodInterno'],
			'Descripcion' => $field_documento_detalle['Descripcion'],
			'CodUnidad' => $field_documento_detalle['CodUnidad'],
			'CodUnidadCompra' => $field_documento_detalle['CodUnidadVenta'],
			'StockActual' => $field_documento_detalle['StockActual'],
			'StockActualEqui' => $field_documento_detalle['StockActualEqui'],
			'CantidadPedida' => $field_documento_detalle['CantidadPendiente'],
			'CantidadRecibida' => $field_documento_detalle['CantidadPendiente'],
			'CantidadCompra' => $field_documento_detalle['CantidadPedida'],
			'PrecioUnit' => 0.00,
			'Total' => 0.00,
			'PrecioUnitCompra' => $field_documento_detalle['PrecioUnit'],
			'ReferenciaAnio' => $field['Anio'],
			'ReferenciaCodDocumento' => $field_documento['CodTipoDocumento'],
			'ReferenciaNroDocumento' => $field_documento['CodDocumento'],
			'ReferenciaNroInterno' => $field_documento['NroDocumento'],
			'ReferenciaSecuencia' => $field_documento_detalle['Secuencia'],
			'CodCentroCosto' => $field_documento['CodCentroCosto'],
		];
	}
	##	
	$_titulo = "Nueva Transacción";
	$accion = "despacho-ventas";
	$disabled_modificar = "";
	$disabled_ver = "";
	$disabled_descuento = "";
	$disabled_item = "";
	$disabled_servicio = "";
	$disabled_opciones = "";
	$read_modificar = "";
	$read_valoracion = "readonly";
	$display_modificar = "";
	$display_ver = "";
	$display_facturar = "display:none;";
	$display_submit = "";
	$label_submit = "Guardar";
	$focus = "btSubmit";
}
$clkCancelar = "document.getElementById('frmentrada').submit();";
//	------------------------------------
$_width = 900;
if ($origen == 'framemain') $action = '../framemain.php';
elseif (!empty($origen)) $action = "gehen.php?anz=$origen";
else $action = "gehen.php?anz=lg_almacen_despacho_ventas_lista";
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="<?=$action?>" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('lg_almacen_despacho_ventas_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
	<input type="hidden" name="fFechaDocumentoD" id="fFechaDocumentoD" value="<?=$fFechaDocumentoD?>" />
	<input type="hidden" name="fFechaDocumentoH" id="fFechaDocumentoH" value="<?=$fFechaDocumentoH?>" />
	<input type="hidden" name="fCodTipoDocumento" id="fCodTipoDocumento" value="<?=$fCodTipoDocumento?>" />
	<input type="hidden" name="fDocFiscalCliente" id="fDocFiscalCliente" value="<?=$fDocFiscalCliente?>" />
	<input type="hidden" name="fCodPersonaCliente" id="fCodPersonaCliente" value="<?=$fCodPersonaCliente?>" />
	<input type="hidden" name="fNombreCliente" id="fNombreCliente" value="<?=$fNombreCliente?>" />
	<input type="hidden" name="fCodAlmacen" id="fCodAlmacen" value="<?=$fCodAlmacen?>" />
	<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
	<input type="hidden" name="IngresadoPor" id="IngresadoPor" value="<?=$field['IngresadoPor']?>" />
	<input type="hidden" name="DocumentoReferencia" id="DocumentoReferencia" value="<?=$field['DocumentoReferencia']?>" />
	<input type="hidden" name="ReferenciaNroDocumento" id="ReferenciaNroDocumento" value="<?=$field['ReferenciaNroDocumento']?>" />
	<input type="hidden" name="DocumentoReferenciaInterno" id="DocumentoReferenciaInterno" value="<?=$field['DocumentoReferenciaInterno']?>" />
	<input type="hidden" name="NotaEntrega" id="NotaEntrega" value="<?=$field['NotaEntrega']?>" />
	<input type="hidden" name="FlagManual" id="FlagManual" value="<?=$field['FlagManual']?>" />
	<input type="hidden" name="FlagPendiente" id="FlagPendiente" value="<?=$field['FlagPendiente']?>" />
	<input type="hidden" name="Anio" id="Anio" value="<?=$field['Anio']?>" />
	<input type="hidden" name="ReferenciaAnio" id="ReferenciaAnio" value="<?=$field['ReferenciaAnio']?>" />

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
			            	<a href="#" onclick="mostrarTab('tab', 2, 2);">Items</a>
			            </li>
		            </ul>
	            </div>
	        </td>
	    </tr>
	</table>

	<div id="tab1" style="display:block;">
		<table style="width:100%; min-width:<?=$_width?>px;" class="tblForm">
			<tr>
		    	<td colspan="4" class="divFormCaption">DATOS GENERALES</td>
		    </tr>
		    <tr>
				<td class="tagForm">* Organismo:</td>
				<td>
					<select name="CodOrganismo" id="CodOrganismo" style="width:295px;" <?=$disabled_modificar?>>
						<?=getOrganismos($field['CodOrganismo'], 3)?>
					</select>
				</td>
				<td class="tagForm">Estado:</td>
				<td>
					<input type="hidden" name="Estado" id="Estado" value="<?=$field['Estado']?>">
		        	<input type="text" value="<?=mb_strtoupper(printValores('ESTADO-TRANSACCION',$field['Estado']))?>" style="width:82px; font-weight:bold;" disabled />
				</td>
			</tr>
		    <tr>
				<td class="tagForm">* Dependencia:</td>
				<td>
					<select name="CodDependencia" id="CodDependencia" style="width:295px;" <?=$disabled_ver?>>
						<option value="">&nbsp;</option>
						<?=loadSelect2('mastdependencias','CodDependencia','Dependencia',$field['CodDependencia'],0,['CodOrganismo'],[$field['CodOrganismo']])?>
					</select>
				</td>
				<td class="tagForm">* Transacción:</td>
				<td>
		        	<input type="text" name="CodTransaccion" id="CodTransaccion" value="<?=$field['CodTransaccion']?>" style="width:42px;" readonly />
		        	<input type="text" name="Transaccion" id="Transaccion" value="<?=$field['Transaccion']?>" style="width:240px;" readonly />
				</td>
			</tr>
		    <tr>
				<td class="tagForm">* Centro Costo:</td>
				<td>
					<select name="CodCentroCosto" id="CodCentroCosto" style="width:295px;" <?=$disabled_ver?>>
						<option value="">&nbsp;</option>
						<?=loadSelect2('ac_mastcentrocosto','CodCentroCosto','Descripcion',$field['CodCentroCosto'],0,['CodDependencia'],[$field['CodDependencia']])?>
					</select>
				</td>
				<td class="tagForm">* Doc. a Generar:</td>
				<td>
		        	<input type="text" name="CodDocumento" id="CodDocumento" value="<?=$field['CodDocumento']?>" style="width:42px;" readonly />
		        	<input type="text" name="NroDocumento" id="NroDocumento" value="<?=$field['NroDocumento']?>" style="width:240px;" readonly />
				</td>
			</tr>
		    <tr>
				<td class="tagForm">* Almacén:</td>
				<td>
					<select name="CodAlmacen" id="CodAlmacen" style="width:295px;" <?=$disabled_ver?>>
						<option value="">&nbsp;</option>
						<?=loadSelect2('lg_almacenmast','CodAlmacen','Descripcion',$field['CodAlmacen'])?>
					</select>
				</td>
				<td class="tagForm">* Doc. Referencia:</td>
				<td>
					<select name="CodDocumentoReferencia" id="CodDocumentoReferencia" style="width:42px;" <?=$disabled_ver?>>
						<option value="">&nbsp;</option>
						<?=loadSelect2('lg_tipodocumento','CodDocumento','Descripcion',$field['CodDocumentoReferencia'],10)?>
					</select>
		        	<input type="text" name="NroDocumentoReferencia" id="NroDocumentoReferencia" value="<?=$field['NroDocumentoReferencia']?>" style="width:240px;" maxlength="20" <?=$disabled_ver?> />
				</td>
			</tr>
			<tr>
				<td class="tagForm">* Fecha Emisión:</td>
				<td>
					<input type="text" name="FechaDocumento" id="FechaDocumento" value="<?=formatFechaDMA($field['FechaDocumento'])?>" maxlength="10" style="width:75px;" class="datepicker" onkeyup="setFechaDMA(this);" />
				</td>
				<td class="tagForm">Recibido Por:</td>
				<td class="gallery clearfix">
					<input type="hidden" name="RecibidoPor" id="RecibidoPor" value="<?=$field['RecibidoPor']?>" />
					<input type="text" name="NomRecibidoPor" id="NomRecibidoPor" value="<?=$field['NomRecibidoPor']?>" style="width:286px;" readonly />
					<a href="../lib/listas/gehen.php?anz=lista_personas&campo1=RecibidoPor&campo2=NomRecibidoPor&ventana=&filtrar=default&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" style=" <?=$display_ver?>">
		            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
		            </a>
				</td>
			</tr>
			<tr>
				<td class="tagForm" rowspan="5">Comentarios:</td>
				<td rowspan="5">
					<textarea name="Comentarios" id="Comentarios" style="width:295px;" rows="6" <?=$disabled_ver?>><?=$field['Comentarios']?></textarea>
				</td>
				<td class="tagForm">&nbsp;</td>
				<td>
		            <input type="checkbox" name="FlagImprimirGuia" id="FlagImprimirGuia" value="S" <?=chkOpt($field['FlagImprimirGuia'], "S");?> <?=$disabled_ver?> /> Imprimir Guia de Remisión
				</td>
			</tr>
			<tr>
		    	<th></th>
				<th>Información de Traslado</th>
			</tr>
		    <tr>
				<td class="tagForm">Transportista:</td>
				<td class="gallery clearfix">
					<input type="hidden" name="CodPersonaTrans" id="CodPersonaTrans" value="<?=$field['CodPersonaTrans']?>">
					<input type="text" name="DocFiscalTrans" id="DocFiscalTrans" value="<?=$field['DocFiscalTrans']?>" style="width:82px;" readonly>
					<input type="text" name="NombreTrans" id="NombreTrans" value="<?=htmlentities($field['NombreTrans'])?>" style="width:200px;" readonly />
					<a href="../lib/listas/gehen.php?anz=lista_personas&campo1=CodPersonaTrans&campo2=NombreTrans&campo3=DocFiscalTrans&ventana=DocFiscal&filtrar=default&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe2]" style=" <?=$display_ver?>">
		            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
		            </a>
				</td>
			</tr>
		    <tr>
				<td class="tagForm">Chofer:</td>
				<td>
					<select name="CodChofer" id="CodChofer" style="width:286px;" <?=$disabled_ver?>>
						<option value="">&nbsp;</option>
						<?=choferes($field['CodChofer'])?>
					</select>
				</td>
			</tr>
		    <tr>
				<td class="tagForm"># Bultos:</td>
				<td>
					<input type="text" name="NroBultos" id="NroBultos" value="<?=$field['NroBultos']?>" maxlength="11" style="width:42px;" <?=$disabled_ver?> />
				</td>
			</tr>
		    <tr>
				<td class="tagForm">&Uacute;ltima Modif.:</td>
				<td colspan="3">
					<input type="text" value="<?=$field['UltimoUsuario']?>" style="width:146px;" disabled="disabled" />
					<input type="text" value="<?=$field['UltimaFecha']?>" style="width:145px" disabled="disabled" />
				</td>
			</tr>
		</table>

		<center>
			<input type="submit" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" id="btSubmit" />
			<input type="button" value="Cancelar" style="width:75px;" id="btCancelar" onclick="<?=$clkCancelar?>" />
		</center>
	</div>

	<div id="tab2" style="display:none;">
		<table style="width:100%; min-width:<?=$_width?>px;" class="tblBotones">
			<thead>
				<tr>
					<th class="divFormCaption" colspan="2">ITEMS</th>
				</tr>
			</thead>
		</table>
		<div style="overflow:scroll; height:400px; width:100%; min-width:<?=$_width?>px; margin:auto;">
			<table class="tblLista" style="width:100%; min-width:1000px;">
				<thead>
					<tr>
						<th width="20">#</th>
						<th width="50">Item</th>
						<th style="min-width: 300px;" align="left">Descripción</th>
						<th width="40">Uni.</th>
						<th width="40">Uni. (Venta)</th>
						<th width="100">Stock Actual</th>
						<th width="100">Stock Actual (Venta)</th>
						<th width="100">Cant. Pedida</th>
						<th width="100">Cantidad</th>
						<th width="125">Precio Unit.</th>
						<th width="125">Total</th>
						<th width="125">Doc. Referencia</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$nro_detalle = 0;
					foreach ($field_detalle as $f)
					{
						++$nro_detalle;
						?>
						<tr class="trListaBody">
							<th>
								<input type="hidden" name="detalle_Secuencia[]" value="0">
								<input type="hidden" name="detalle_CodItem[]" value="<?=$f['CodItem']?>">
								<input type="hidden" name="detalle_Descripcion[]" value="<?=htmlentities($f['Descripcion'])?>">
								<input type="hidden" name="detalle_ReferenciaAnio[]" value="<?=$field['ReferenciaAnio']?>">
								<input type="hidden" name="detalle_ReferenciaCodDocumento[]" value="<?=$f['ReferenciaCodDocumento']?>">
								<input type="hidden" name="detalle_ReferenciaNroDocumento[]" value="<?=$f['ReferenciaNroDocumento']?>">
								<input type="hidden" name="detalle_ReferenciaSecuencia[]" value="<?=$f['ReferenciaSecuencia']?>">
								<input type="hidden" name="detalle_ReferenciaNroInterno[]" value="<?=$f['ReferenciaNroInterno']?>">
								<input type="hidden" name="detalle_CodCentroCosto[]" value="<?=$f['CodCentroCosto']?>">
								<input type="hidden" name="detalle_CantidadCompra[]" value="<?=$f['CantidadCompra']?>">
								<input type="hidden" name="detalle_PrecioUnitCompra[]" value="<?=$f['PrecioUnitCompra']?>">
								<?=$nro_detalle?>
							</th>
							<td align="center"><?=$f['CodInterno']?></td>
							<td><?=htmlentities($f['Descripcion'])?></td>
							<td>
								<select name="detalle_CodUnidad[]" id="detalle_CodUnidad<?=$id?>" class="cell" <?=$disabled_ver?>>
									<?=loadSelect2('mastunidades','CodUnidad','CodUnidad',$f['CodUnidad'],1)?>
								</select>
							</td>
							<td>
								<select name="detalle_CodUnidadCompra[]" id="detalle_CodUnidadCompra<?=$id?>" class="cell" <?=$disabled_ver?>>
									<?=loadSelect2('mastunidades','CodUnidad','CodUnidad',$f['CodUnidadCompra'],1)?>
								</select>
							</td>
							<td>
								<input type="text" name="detalle_StockActual[]" value="<?=number_format($f['StockActual'],5,',','.')?>" class="cell2" style="text-align:right;" readonly>
							</td>
							<td>
								<input type="text" name="detalle_StockActualEqui[]" value="<?=number_format($f['StockActualEqui'],5,',','.')?>" class="cell2" style="text-align:right;" readonly>
							</td>
							<td>
								<input type="text" name="detalle_CantidadPedida[]" value="<?=number_format($f['CantidadPedida'],5,',','.')?>" class="cell2 currency5" style="text-align:right;" readonly>
							</td>
							<td>
								<input type="text" name="detalle_CantidadRecibida[]" value="<?=number_format($f['CantidadRecibida'],5,',','.')?>" class="cell currency5" style="text-align:right;" <?=$disabled_ver?>>
							</td>
							<td>
								<input type="text" name="detalle_PrecioUnit[]" value="<?=number_format($f['PrecioUnit'],2,',','.')?>" class="cell2 currency" style="text-align:right;" <?=$read_valoracion?>>
							</td>
							<td>
								<input type="text" name="detalle_Total[]" value="<?=number_format($f['Total'],2,',','.')?>" class="cell2" style="text-align:right;" readonly>
							</td>
							<td align="center"><?=$f['ReferenciaCodDocumento']?>-<?=$f['ReferenciaNroInterno']?>-<?=$f['ReferenciaSecuencia']?></td>
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
</script>