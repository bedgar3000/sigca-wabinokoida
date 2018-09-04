<?php
if ($opcion == "nuevo") {
	$field['CodOrganismo'] = $_SESSION['ORGANISMO_ACTUAL'];
	$field['CodCentroCosto'] = $_PARAMETRO['COCCOSTO'];
	$field['EstadoFacturacion'] = 'PE';
	$field['EstadoDespacho'] = 'PE';
	$field['Estado'] = 'PR';
	$field['FechaDocumento'] = $FechaActual;
	$field['MotivoTraslado'] = 'VT';
	$field['NroSerie'] = getVar3("SELECT NroSerie FROM co_seriefiscal WHERE CodOrganismo = '$field[CodOrganismo]' LIMIT 0, 1");
	$field_detalle = [];
	##
	$_titulo = "Guia de Remisión / Nuevo Registro";
	$accion = "nuevo";
	$disabled_modificar = "";
	$disabled_ver = "";
	$disabled_detalle = "";
	$read_modificar = "";
	$display_modificar = "";
	$display_ver = "";
	$display_submit = "";
	$label_submit = "Guardar";
	$focus = "CodPersona";
}
elseif ($opcion == "modificar" || $opcion == "ver" || $opcion == "confirmar" || $opcion == "anular") {
	##	consulto datos generales
	$sql = "SELECT
				gr.*,
				tt.Descripcion AS TipoTransaccion
			FROM lg_guiaremision gr
			LEFT JOIN lg_tipotransaccion tt ON tt.CodTransaccion = gr.RefTipoTransaccion
			WHERE gr.CodGuia = '$sel_registros'";
	$field = getRecord($sql);
	##	detalle
	$sql = "SELECT
				grd.*,
				i.CodInterno
			FROM lg_guiaremisiondet grd
			INNER JOIN lg_itemmast i ON i.CodItem = grd.CodItem
			WHERE grd.CodGuia = '$field[CodGuia]'";
	$field_detalle = getRecords($sql);
	##
	if ($opcion == "modificar") {
		$_titulo = "Guia de Remisión / Modificar Registro";
		$accion = "modificar";
		$disabled_modificar = "disabled";
		$disabled_ver = "";
		$disabled_detalle = "";
		$read_modificar = "readonly";
		$display_modificar = "display:none;";
		$display_ver = "";
		$display_submit = "";
		$label_submit = "Modificar";
		$focus = "Comentarios";
	}
	##
	elseif ($opcion == "confirmar") {
		$field['FechaFinalEntrega'] = $FechaActual;
		##	
		$_titulo = "Guia de Remisión / Confirmar Registro";
		$accion = "confirmar";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$disabled_detalle = "";
		$read_modificar = "readonly";
		$display_modificar = "display:none;";
		$display_ver = "display:none;";
		$display_submit = "";
		$label_submit = "Confirmar";
		$focus = "btSubmit";
	}
	##
	elseif ($opcion == "anular") {
		$field['Estado'] = 'AN';
		##	
		$_titulo = "Guia de Remisión / Anular Registro";
		$accion = "anular";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$disabled_detalle = "disabled";
		$read_modificar = "readonly";
		$display_modificar = "display:none;";
		$display_ver = "display:none;";
		$display_submit = "";
		$label_submit = "Anular";
		$focus = "btSubmit";
	}
	##
	elseif ($opcion == "ver") {
		$_titulo = "Guia de Remisión / Ver Registro";
		$accion = "";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$disabled_detalle = "disabled";
		$read_modificar = "readonly";
		$display_modificar = "display:none;";
		$display_ver = "display:none;";
		$display_submit = "display:none;";
		$label_submit = "";
		$focus = "btCancelar";
	}
}
$igv = getVar3("SELECT FactorPorcentaje FROM mastimpuestos WHERE CodImpuesto = '$_PARAMETRO[COIVA]'");
$clkCancelar = "document.getElementById('frmentrada').submit();";
//	------------------------------------
$_width = 900;
if ($origen == 'framemain') $action = '../framemain.php';
elseif (!empty($origen)) $action = "gehen.php?anz=$origen";
else $action = "gehen.php?anz=lg_guiaremision_lista";
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="<?=$action?>" method="POST" enctype="multipart/form-data" onsubmit="return formSubmitGuiaRemision('lg_guiaremision_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
	<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
	<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
	<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
	<input type="hidden" name="fFechaDocumentoD" id="fFechaDocumentoD" value="<?=$fFechaDocumentoD?>" />
	<input type="hidden" name="fFechaDocumentoH" id="fFechaDocumentoH" value="<?=$fFechaDocumentoH?>" />
	<input type="hidden" name="fCodAlmacen" id="fCodAlmacen" value="<?=$fCodAlmacen?>" />
	<input type="hidden" name="fEstadoFacturacion" id="fEstadoFacturacion" value="<?=$fEstadoFacturacion?>" />
	<input type="hidden" name="fCodChofer" id="fCodChofer" value="<?=$fCodChofer?>" />
	<input type="hidden" name="fEstadoDespacho" id="fEstadoDespacho" value="<?=$fEstadoDespacho?>" />
	<input type="hidden" name="CodGuia" id="CodGuia" value="<?=$field['CodGuia']?>" />
	<input type="hidden" name="CodCentroCosto" id="CodCentroCosto" value="<?=$field['CodCentroCosto']?>" />
	<input type="hidden" name="FlagImprimirDocumento" id="FlagImprimirDocumento" value="N" />
	<input type="hidden" name="CodImprimirDocumento" id="CodImprimirDocumento" value="" />

	<table style="width:100%; min-width:<?=$_width?>px;" class="tblForm">
		<tr>
	    	<td colspan="6" class="divFormCaption">DATOS GENERALES</td>
	    </tr>
	    <tr>
			<td class="tagForm">* Organismo:</td>
			<td>
				<select name="CodOrganismo" id="CodOrganismo" style="width:295px;" <?=$disabled_modificar?>>
					<?=getOrganismos($field['CodOrganismo'], 3)?>
				</select>
			</td>
			<td class="tagForm"># Guia Rem.:</td>
			<td>
	        	<input type="text" name="NroSerie" id="NroSerie" value="<?=$field['NroSerie']?>" style="width:35px; font-weight:bold;" readonly />
	        	<input type="text" name="NroGuia" id="NroGuia" value="<?=$field['NroGuia']?>" style="width:86px; font-weight:bold;" readonly />
			</td>
			<td class="tagForm">* # Transacción:</td>
			<td class="gallery clearfix">
				<a id="a_transaccion" href="../lib/listas/gehen.php?anz=lista_lg_transaccion&campo1=CodOrganismo&campo2=RefCodTransaccion&campo3=RefNroTransaccion&campo4=RefTipoTransaccion&campo5=TipoTransaccion&campo6=CodAlmacen&campo7=AlmacenDireccion&campo8=NroFactura&campo9=FechaFactura&campo10=RefFechaTransaccion&FlagOrganismo=S&ventana=lg_guiaremision&filtrar=default&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" style="display:none;"></a>

				<input type="text" name="RefCodTransaccion" id="RefCodTransaccion" value="<?=$field['RefCodTransaccion']?>" style="width:35px;" readonly />
				<input type="text" name="RefNroTransaccion" id="RefNroTransaccion" value="<?=$field['RefNroTransaccion']?>" style="width:161px;" readonly />
				<a href="#" onclick="sel_transaccion();" style=" <?=$display_ver?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
		</tr>
	    <tr>
	    	<th></th>
			<th>Información Destinatario</th>
			<td class="tagForm">Estado:</td>
			<td>
				<input type="hidden" name="Estado" id="Estado" value="<?=$field['Estado']?>">
	        	<input type="text" value="<?=mb_strtoupper(printValores('guia-remision-estado',$field['Estado']))?>" style="width:125px; font-weight:bold;" disabled />
			</td>
			<td class="tagForm">Transacción:</td>
			<td>
				<input type="hidden" name="RefTipoTransaccion" id="RefTipoTransaccion" value="<?=$field['RefTipoTransaccion']?>">
				<input type="text" name="TipoTransaccion" id="TipoTransaccion" value="<?=htmlentities($field['TipoTransaccion'])?>" style="width:200px;" readonly />
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Sr(es).:</td>
			<td class="gallery clearfix">
				<input type="hidden" name="DocFiscalDestino" id="DocFiscalDestino" value="<?=$field['DocFiscalDestino']?>">
				<input type="text" name="CodPersonaDestino" id="CodPersonaDestino" value="<?=$field['CodPersonaDestino']?>" style="width:45px;" readonly />
				<input type="text" name="NombreDestino" id="NombreDestino" value="<?=htmlentities($field['NombreDestino'])?>" style="width:246px;" readonly />
				<a href="../lib/listas/gehen.php?anz=lista_personas&campo1=CodPersonaDestino&campo2=NombreDestino&campo3=DocFiscalDestino&campo4=DireccionDestino&ventana=lg_guiaremision&filtrar=default&concepto=03-0001&_APLICACION=<?=$_APLICACION?>&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe2]" style=" <?=$display_modificar?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
			<td class="tagForm">Estado Fact.:</td>
			<td>
				<input type="hidden" name="EstadoFacturacion" id="EstadoFacturacion" value="<?=$field['EstadoFacturacion']?>">
	        	<input type="text" id="NomEstadoFacturacion" value="<?=mb_strtoupper(printValores('guia-remision-estado-factura',$field['EstadoFacturacion']))?>" style="width:125px; font-weight:bold;" disabled />
			</td>
			<td class="tagForm">Fecha Transacción:</td>
			<td>
				<input type="text" name="RefFechaTransaccion" id="RefFechaTransaccion" value="<?=formatFechaDMA($field['RefFechaTransaccion'])?>" style="width:80px;" readonly />
			</td>
		</tr>
	    <tr>
			<td class="tagForm" rowspan="2">Dirección:</td>
			<td rowspan="2">
				<textarea name="DireccionDestino" id="DireccionDestino" style="width:295px; height: 40px;" readonly><?=htmlentities($field['DireccionDestino'])?></textarea>
			</td>
			<td class="tagForm">Estado Desp.:</td>
			<td>
				<input type="hidden" name="EstadoDespacho" id="EstadoDespacho" value="<?=$field['EstadoDespacho']?>">
	        	<input type="text" value="<?=mb_strtoupper(printValores('guia-remision-estado-despacho',$field['EstadoDespacho']))?>" style="width:125px; font-weight:bold;" disabled />
			</td>
			<td class="tagForm">* Almacen Origen:</td>
			<td>
				<select name="CodAlmacen" id="CodAlmacen" style="width:200px;" onchange="getDireccionAlmacen(this.value);" <?=$disabled_ver?>>
					<option value="">&nbsp;</option>
					<?=loadSelect2('lg_almacenmast','CodAlmacen','CodAlmacen',$field['CodAlmacen'])?>
				</select>
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Fecha Documento:</td>
			<td>
				<input type="text" name="FechaDocumento" id="FechaDocumento" value="<?=formatFechaDMA($field['FechaDocumento'])?>" maxlength="10" style="width:80px;" class="datepicker" onkeyup="setFechaDMA(this);" <?=$disabled_ver?> />
			</td>
			<td class="tagForm" rowspan="2">Dirección Almacen:</td>
			<td rowspan="2">
				<textarea name="AlmacenDireccion" id="AlmacenDireccion" style="width:200px; height: 40px;" readonly="readonly"><?=htmlentities($field['AlmacenDireccion'])?></textarea>
			</td>
		</tr>
		<tr>
	    	<th></th>
			<th>Información Transportista</th>
			<td class="tagForm"># Bultos:</td>
			<td>
				<input type="text" name="NroBultos" id="NroBultos" value="<?=$field['NroBultos']?>" maxlength="11" style="width:80px;" <?=$disabled_ver?> />
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Placa:</td>
			<td>
				<select name="CodVehiculoTrans" id="CodVehiculoTrans" style="width:295px;" onchange="getVehiculo(this.value)" <?=$disabled_ver?>>
					<option value="">&nbsp;</option>
					<?=loadSelect2('lg_vehiculos','CodVehiculo','Placa',$field['CodVehiculoTrans'])?>
				</select>
			</td>
			<?php if ($opcion == 'confirmar') { ?>
				<td class="tagForm">* Fecha de Entrega:</td>
				<td>
					<input type="text" name="FechaFinalEntrega" id="FechaFinalEntrega" value="<?=formatFechaDMA($field['FechaFinalEntrega'])?>" maxlength="10" style="width:80px;" class="datepicker" onkeyup="setFechaDMA(this);" <?=$disabled_detalle?> />
				</td>
			<?php } else { ?>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			<?php } ?>
			<td class="tagForm">Almacen Destino:</td>
			<td>
				<select name="CodAlmacenDestino" id="CodAlmacenDestino" style="width:200px;" <?=$disabled_ver?>>
					<option value="">&nbsp;</option>
					<?=loadSelect2('lg_almacenmast','CodAlmacen','CodAlmacen',$field['CodAlmacenDestino'])?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="tagForm">Vehiculo:</td>
			<td>
				<input type="text" name="Marca" id="Marca" value="<?=htmlentities($field['Marca'])?>" style="width:295px;" readonly />
			</td>
	    	<th></th>
			<th>Información Factura</th>
			<td class="tagForm">* Motivo Traslado:</td>
			<td>
				<select name="MotivoTraslado" id="MotivoTraslado" style="width:200px;" <?=$disabled_ver?>>
					<option value="">&nbsp;</option>
					<?=getMiscelaneos($field['MotivoTraslado'],'LGMOTRAS')?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="tagForm">Transportista:</td>
			<td>
				<input type="hidden" name="CodPersonaTrans" id="CodPersonaTrans" value="<?=$field['CodPersonaTrans']?>">
				<input type="hidden" name="DocFiscalTrans" id="DocFiscalTrans" value="<?=$field['DocFiscalTrans']?>">
				<input type="text" name="NombreTrans" id="NombreTrans" value="<?=htmlentities($field['NombreTrans'])?>" style="width:295px;" readonly />
			</td>
			<td class="tagForm">Nº Factura:</td>
			<td>
				<input type="text" name="NroFactura" id="NroFactura" value="<?=$field['NroFactura']?>" style="width:80px;" maxlength="20" <?=$disabled_ver?> />
				<input type="text" name="FechaFactura" id="FechaFactura" value="<?=formatFechaDMA($field['FechaFactura'])?>" maxlength="10" style="width:75px;" class="datepicker" onkeyup="setFechaDMA(this);" <?=$disabled_ver?> />
			</td>
			<td class="tagForm">Motivo Devolución:</td>
			<td>
				<select name="MotivoDevolucion" id="MotivoDevolucion" style="width:200px;" <?=$disabled_ver?>>
					<option value="">&nbsp;</option>
					<?=getMiscelaneos($field['MotivoDevolucion'],'LGMOTDEV')?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Chofer:</td>
			<td>
				<select name="CodChofer" id="CodChofer" style="width:295px;" <?=$disabled_ver?>>
					<option value="">&nbsp;</option>
					<?=choferes($field['CodChofer'])?>
				</select>
			</td>
			<td class="tagForm">&nbsp;</td>
  			<td>
  				<input type="checkbox" name="FlagFacturacionPrevia" id="FlagFacturacionPrevia" value="S" <?=chkOpt($field['FlagFacturacionPrevia'], "S");?> <?=$disabled_ver?> /> Fact. Previa
  			</td>
		</tr>
	    <tr>
			<td class="tagForm">&Uacute;ltima Modif.:</td>
			<td colspan="5">
				<input type="text" value="<?=$field['UltimoUsuario']?>" style="width:146px;" disabled="disabled" />
				<input type="text" value="<?=$field['UltimaFecha']?>" style="width:145px" disabled="disabled" />
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
				<td align="right" class="gallery clearfix">
					<a id="a_detalle_item" href="../lib/listas/gehen.php?anz=lista_lg_items&filtrar=default&fFlagDisponible=S&ventana=listado_insertar_linea&detalle=detalle&modulo=ajax&accion=detalle_insertar&url=../../lg/lg_guiaremision_ajax.php&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe3]" style="display:none;"></a>

					<input type="button" class="btLista" id="btInsertarItem" value="Item" onclick="$('#a_detalle_item').click();" <?=$disabled_ver?> />
					<input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'detalle'); setMontosVentas();" <?=$disabled_ver?> />
				</td>
			</tr>
		</tbody>
	</table>
	<div style="overflow:scroll; height:230px; width:100%; min-width:<?=$_width?>px; margin:auto;">
		<table class="tblLista" style="width:100%; min-width:800px;">
			<thead>
				<tr>
					<th width="20">#</th>
					<th width="60">Item</th>
					<th align="left">Descripci&oacute;n</th>
					<th width="40">Uni.</th>
					<th width="40">Uni. Venta</th>
					<th width="75">Cantidad</th>
					<th width="75">Cant. Recibida</th>
					<th width="75">Cant. Devuelta</th>
				</tr>
			</thead>
			
			<tbody id="lista_detalle">
				<?php
				$nro_detalle = 0;
				foreach ($field_detalle as $f)
				{
					$id = ++$nro_detalle;
					?>
					<tr class="trListaBody" onclick="clk($(this), 'detalle', 'detalle_<?=$id?>');" id="detalle_<?=$id?>">
						<th>
							<input type="hidden" name="detalle_Secuencia[]" id="detalle_Secuencia<?=$id?>" value="<?=$f['Secuencia']?>">
							<input type="hidden" name="detalle_RefCodTransaccion[]" value="<?=$f['RefCodTransaccion']?>">
							<input type="hidden" name="detalle_RefNroTransaccion[]" value="<?=$f['RefNroTransaccion']?>">
							<input type="hidden" name="detalle_RefSecTransaccion[]" value="<?=$f['RefSecTransaccion']?>">
							<?=++$nro_detalle?>
						</th>
						<td>
							<input type="hidden" name="detalle_CodItem[]" value="<?=$f['CodItem']?>">
							<input type="text" name="detalle_CodInterno[]" value="<?=$f['CodInterno']?>" class="cell2" style="text-align: center;" readonly>
						</td>
						<td>
							<input type="text" name="detalle_Descripcion[]" value="<?=$f['Descripcion']?>" class="cell2" readonly>
						</td>
						<td>
							<select name="detalle_CodUnidad[]" id="detalle_CodUnidad<?=$id?>" class="cell" <?=$disabled_ver?>>
								<?=loadSelect2('mastunidades','CodUnidad','CodUnidad',$f['CodUnidad'],1)?>
							</select>
						</td>
						<td>
							<input type="text" name="detalle_Cantidad[]" value="<?=number_format($f['Cantidad'],5,',','.')?>" class="cell currency5" style="text-align:right;" <?=$disabled_ver?>>
						</td>
						<td>
							<input type="text" name="detalle_CantidadRecibida[]" value="<?=number_format($f['CantidadRecibida'],5,',','.')?>" class="cell currency5" style="text-align:right;" <?=$disabled_detalle?>>
						</td>
						<td>
							<input type="text" name="detalle_CantidadDevuelta[]" value="<?=number_format($f['CantidadDevuelta'],5,',','.')?>" class="cell currency5" style="text-align:right;" <?=$disabled_detalle?>>
						</td>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
	</div>
	<input type="hidden" id="nro_detalle" value="<?=$nro_detalle?>" />
	<input type="hidden" id="can_detalle" value="<?=$nro_detalle?>" />
</form>
<div style="width:100%; min-width:<?=$_width?>px;" class="divMsj">Campos Obligatorios *</div>

<script type="text/javascript">
	function formSubmitGuiaRemision() {
		$.post('lg_guiaremision_ajax.php', 'modulo=formulario&accion=<?=$accion?>&'+$('#frmentrada').serialize(), function(resp) {
			var data = resp.split("|");

			if (data[0].trim() != '') cajaModal(data[0].trim(), 'error', 600);
			else {
				if ('<?=$accion?>' == 'nuevo') {
					$('#FlagImprimirDocumento').val('S');
					$('#CodImprimirDocumento').val(data[2]);
					cajaModal(data[1].trim(), 'success', 600, "document.getElementById('frmentrada').submit();");
				} else {
					document.getElementById('frmentrada').submit();
				}
			}
	    });
	    return false;
	}
	function getVehiculo(CodVehiculo) {
		$.post('lg_guiaremision_ajax.php', 'modulo=ajax&accion=getVehiculo&CodVehiculo='+CodVehiculo, function(data) {
			$('#Marca').val(data['NomMarca']);
			$('#CodPersonaTrans').val(data['CodEmpresa']);
			$('#DocFiscalTrans').val(data['DocFiscalEmpresa']);
			$('#NombreTrans').val(data['Empresa']);
			$('#CodChofer').val(data['CodChofer']);
	    },'json');
	}
	function getDireccionAlmacen(CodAlmacen) {
		$.post('lg_guiaremision_ajax.php', 'modulo=ajax&accion=getDireccionAlmacen&CodAlmacen='+CodAlmacen, function(data) {
			$('#AlmacenDireccion').val(data['Direccion']);
	    },'json');
	}
	function sel_transaccion() {
		var href = "../lib/listas/gehen.php?anz=lista_lg_transaccion&campo1=CodOrganismo&campo2=RefCodTransaccion&campo3=RefNroTransaccion&campo4=RefTipoTransaccion&campo5=TipoTransaccion&campo6=CodAlmacen&campo7=AlmacenDireccion&campo8=NroFactura&campo9=FechaFactura&FlagOrganismo=S&FlagDocumento=S&fCodDocumento=NS&CodOrganismo"+$('#CodOrganismo').val()+"&ventana=lg_guiaremision&filtrar=default&iframe=true&width=100%&height=100%";
		$('#a_transaccion').attr('href', href);
		$('#a_transaccion').click();
	}
</script>