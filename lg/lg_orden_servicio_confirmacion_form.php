<?php
list($Anio, $CodOrganismo, $NroOrden, $Secuencia) = explode('_', $confirmar[0]);
##	orden de servicio
$sql = "SELECT
			os.*,
			o.Organismo,
			cc.codigo AS NomCentroCosto
		FROM
			lg_ordenservicio os
			INNER JOIN mastorganismos o ON (o.CodOrganismo = os.CodOrganismo)
			INNER JOIN ac_mastcentrocosto cc On (cc.CodCentrocosto = os.CodCentrocosto)
		WHERE
			os.Anio = '$Anio'
			AND os.CodOrganismo = '$CodOrganismo'
			AND os.NroOrden = '$NroOrden'";
$field_os = getRecord($sql);
##	
$_titulo = "Confirmar Realizaci&oacute;n de Servicios";
$accion = "confirmar";
$display_submit = "";
$label_submit = "Confirmar";
$focus = "CodActividad";
$clkCancelar = "document.getElementById('frmentrada').submit();";
//	------------------------------------
$_width = 800;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lg_orden_servicio_confirmacion_lista" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('lg_orden_servicio_ajax', 'modulo=orden_servicio&accion=<?=$accion?>');" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
<input type="hidden" name="fCodDependencia" id="fCodDependencia" value="<?=$fCodDependencia?>" />
<input type="hidden" name="fCodCentroCosto" id="fCodCentroCosto" value="<?=$fCodCentroCosto?>" />
<input type="hidden" name="fCodProveedor" id="fCodProveedor" value="<?=$fCodProveedor?>" />
<input type="hidden" name="fProveedor" id="fProveedor" value="<?=$fProveedor?>" />
<input type="hidden" name="fFechaPreparacionD" id="fFechaPreparacionD" value="<?=$fFechaPreparacionD?>" />
<input type="hidden" name="fFechaPreparacionH" id="fFechaPreparacionH" value="<?=$fFechaPreparacionH?>" />
<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />

<table width="<?=$_width?>" class="tblForm">
	<tr>
    	<td colspan="4" class="divFormCaption">Orden de Servicio</td>
    </tr>
    <tr>
		<td class="tagForm">Organismo:</td>
		<td>
        	<input type="text" value="<?=$field_os['Organismo']?>" style="width:300px; font-weight:bold;" disabled />
		</td>
		<td class="tagForm">Nro. Orden:</td>
		<td>
        	<input type="text" value="<?=$field_os['NroOrden']?>" style="width:70px; font-weight:bold;" readonly="readonly" />
        	<input type="text" value="<?=$field_os['Anio']?>" style="width:35px; font-weight:bold;" readonly="readonly" />
		</td>
	</tr>
    <tr>
		<td class="tagForm">Proveedor:</td>
		<td>
        	<input type="text" value="<?=$field_os['CodProveedor']?>" style="width:45px; font-weight:bold;" disabled />
        	<input type="text" value="<?=$field_os['NomProveedor']?>" style="width:251px; font-weight:bold;" disabled />
		</td>
		<td class="tagForm">Fecha Confirmaci&oacute;n:</td>
		<td>
        	<input type="text" value="<?=formatFechaDMA($FechaActual)?>" style="width:70px;" class="datepicker" onchange="$('input[name=\'FechaConfirmacion[]\']').val(this.value);" />
		</td>
	</tr>
</table>

<div style="overflow:scroll; height:250px; width:<?=$_width?>px; margin:auto;">
	<table class="tblLista" style="width:100%; min-width:<?=$_width?>px;">
		<thead>
			<tr>
				<th width="60">Commodity</th>
				<th align="left">Descripci&oacute;n</th>
				<th width="70">Fecha Confirmaci&oacute;n</th>
				<th width="60">Cantidad</th>
				<th width="60">Cant. Pendiente</th>
				<th width="60">Cant. Recibida</th>
				<th width="60">Cant. Total</th>
			</tr>
		</thead>
		
		<tbody id="lista_detalle">
			<?php
			foreach ($confirmar as $servicio) {
				list($Anio, $CodOrganismo, $NroOrden, $Secuencia) = explode('_', $servicio);
				##	
				$sql = "SELECT
							*,
							(CantidadPedida - CantidadRecibida) AS CantidadPendiente
						FROM lg_ordenserviciodetalle
						WHERE
							Anio = '$Anio'
							AND CodOrganismo = '$CodOrganismo'
							AND NroOrden = '$NroOrden'
							AND Secuencia = '$Secuencia'";
				$field_osd = getRecord($sql);
				?>
				<tr class="trListaBody">
					<td align="center">
						<input type="hidden" name="Anio[]" value="<?=$Anio?>">
						<input type="hidden" name="CodOrganismo[]" value="<?=$CodOrganismo?>">
						<input type="hidden" name="NroOrden[]" value="<?=$NroOrden?>">
						<input type="hidden" name="Secuencia[]" value="<?=$Secuencia?>">
						<?=$field_osd['CommoditySub']?>
					</td>
					<td><?=htmlentities($field_osd['Descripcion'])?></td>
					<td><input type="text" name="FechaConfirmacion[]" value="<?=formatFechaDMA($FechaActual)?>" class="cell datepicker" style="text-align:center;" /></td>
					<td align="right"><input type="text" name="CantidadPorRecibir[]" value="<?=number_format($field_osd['CantidadPendiente'],2,',','.')?>" class="cell currency" style="text-align:right;" /></td>
					<td align="right"><input type="text" name="CantidadPendiente[]" value="<?=number_format($field_osd['CantidadPendiente'],2,',','.')?>" class="cell" style="text-align:right;" readonly /></td>
					<td align="right"><input type="text" name="CantidadRecibida[]" value="<?=number_format($field_osd['CantidadRecibida'],2,',','.')?>" class="cell" style="text-align:right;" readonly /></td>
					<td align="right"><input type="text" name="CantidadPedida[]" value="<?=number_format($field_osd['CantidadPedida'],2,',','.')?>" class="cell" style="text-align:right;" readonly /></td>
				</tr>
				<?php
			}
			?>
		</tbody>
	</table>
</div>

<center>
	<input type="submit" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" id="btSubmit" />
	<input type="button" value="Cancelar" style="width:75px;" id="btCancelar" onclick="<?=$clkCancelar?>" />
</center>
</form>
<div style="width:<?=$_width?>px" class="divMsj">Campos Obligatorios *</div>