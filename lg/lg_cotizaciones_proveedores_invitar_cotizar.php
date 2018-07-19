<?php
list($AnioActual, $MesActual, $DiaActual) = split("[/.-]", substr($Ahora, 0, 10));
$FechaActual = "$AnioActual-$MesActual-$DiaActual";
//	------------------------------------
//	cotizacion
$sql = "SELECT *
		FROM lg_cotizacion
		WHERE NroCotizacionProv = '".$sel_registros."'
		GROUP BY CodProveedor";
$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
if (mysql_num_rows($query)) $field = mysql_fetch_array($query);
//	------------------------------------
$clkCancelar = "document.getElementById('frmentrada').submit();";
$_width = 1000;
$_sufijo = "cotizaciones_proveedores_invitar";
$_titulo = "Cotizaciones de los Proveedores Invitados";
//	------------------------------------
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lg_cotizaciones_proveedores_invitar_lista" method="POST" enctype="multipart/form-data" onsubmit="return cotizaciones_proveedores_invitar_cotizar(this);" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
<input type="hidden" name="fFechaInvitacionD" id="fFechaInvitacionD" value="<?=$fFechaInvitacionD?>" />
<input type="hidden" name="fFechaInvitacionH" id="fFechaInvitacionH" value="<?=$fFechaInvitacionH?>" />
<input type="hidden" name="fTotalD" id="fTotalD" value="<?=$fTotalD?>" />
<input type="hidden" name="fTotalH" id="fTotalH" value="<?=$fTotalH?>" />
<input type="hidden" name="fTotalH" id="fTotalH" value="<?=$fTotalH?>" />

<table width="<?=$_width?>" class="tblForm">
	<tr>
    	<td colspan="6" class="divFormCaption">Informaci&oacute;n General</td>
    </tr>
	<tr>
		<td class="tagForm">Fecha Invitaci&oacute;n:</td>
		<td>
        	<input type="text" id="FechaInvitacion" value="<?=formatFechaDMA($field['FechaInvitacion'])?>" style="width:60px;" class="datepicker" maxlength="10" />
		</td>
		<td class="tagForm">Proveedor:</td>
		<td>
        	<input type="hidden" id="CodProveedor" value="<?=$field['CodProveedor']?>" />
        	<input type="text" id="NomProveedor" value="<?=htmlentities($field['NomProveedor'])?>" style="width:275px;" disabled />
		</td>
		<td class="tagForm">Dcto.(%):</td>
		<td>
        	<input type="text" id="DescuentoPorcentaje" value="<?=number_format($field['DescuentoPorcentaje'], 2, ',', '.')?>" style="width:45px; text-align:right;" class="currency" onchange="cotizaciones_proveedores_descuento();" />
		</td>
	</tr>
	<tr>
		<td class="tagForm">Fecha Entrega:</td>
		<td>
        	<input type="text" id="FechaEntrega" value="<?=formatFechaDMA($field['FechaEntrega'])?>" style="width:60px;" class="datepicker" maxlength="10" onchange="obtenerFechaFin($(this), $('#FechaLimite'), '<?=$_PARAMETRO['DIASLIMCOT']?>');" />
		</td>
		<td class="tagForm">Cotizaci&oacute;n:</td>
		<td>
        	<input type="text" id="NumeroCotizacion" value="<?=$field['NumeroCotizacion']?>" style="width:60px;" maxlength="10" />
        	<input type="text" id="FechaDocumento" value="<?=formatFechaDMA($field['FechaDocumento'])?>" style="width:60px;" class="datepicker" maxlength="10" />
		</td>
		<td class="tagForm">Dcto.(Monto):</td>
		<td>
        	<input type="text" id="DescuentoFijo" value="<?=number_format($field['DescuentoFijo'], 2, ',', '.')?>" style="width:45px; text-align:right;" class="currency" onchange="cotizaciones_proveedores_descuento();" />
		</td>
	</tr>
	<tr>
		<td class="tagForm">Fecha Recepci&oacute;n:</td>
		<td>
        	<input type="text" id="FechaRecepcion" value="<?=formatFechaDMA($field['FechaRecepcion'])?>" style="width:60px;" class="datepicker" maxlength="10" />
		</td>
		<td class="tagForm">Forma de Pago:</td>
		<td>
			<select id="CodFormaPago" style="width:135px;">
				<?=loadSelect("mastformapago", "CodFormaPago", "Descripcion", $field['CodFormaPago'], 0)?>
			</select>
		</td>
		<td class="tagForm">Validez Oferta:</td>
		<td>
        	<input type="text" id="ValidezOferta" value="<?=$field['ValidezOferta']?>" style="width:45px;" maxlength="10" />
		</td>
	</tr>
	<tr>
		<td class="tagForm">Fecha L&iacute;mite:</td>
		<td>
        	<input type="text" id="FechaLimite" value="<?=formatFechaDMA($field['FechaLimite'])?>" style="width:60px;" class="datepicker" maxlength="10" />
		</td>
		<td class="tagForm">Fecha Apertura:</td>
		<td>
        	<input type="text" id="FechaApertura" value="<?=formatFechaDMA($field['FechaApertura'])?>" style="width:60px;" class="datepicker" maxlength="10" />
		</td>
		<td class="tagForm">Dias Entrega:</td>
		<td>
        	<input type="text" id="DiasEntrega" value="<?=$field['DiasEntrega']?>" style="width:45px;" maxlength="10" />
		</td>
	</tr>
</table>

<center>
<input type="submit" value="Aceptar" style="width:75px;" />
<input type="button" value="Cancelar" style="width:75px;" onclick="<?=$clkCancelar?>" />
</center>
</form>

<center>
<form name="frm_items" id="frm_items" autocomplete="off">
<input type="hidden" id="sel_items" />
<table width="<?=$_width?>" class="tblBotones">
	<thead>
    <tr>
    	<th class="divFormCaption" colspan="2">Items / Commodities</th>
	</tr>
    </thead>
</table>
<div style="overflow:scroll; width:<?=$_width?>px; height:250px;">
<table width="2750" class="tblLista">
    <thead>
    <tr>
        <th scope="col" width="15">&nbsp;</th>
        <th scope="col" width="100">Requerimiento</th>
        <th scope="col" width="20">#</th>
        <th scope="col" width="75">Item / Commodity</th>
        <th scope="col" width="400" align="left">Descripci&oacute;n</th>
        <th scope="col" width="40">Uni. Rec.</th>
        <th scope="col" width="50">Cant. Rec.</th>
        <th scope="col" width="40">Uni. Compra.</th>
        <th scope="col" width="50">Cant. Compra.</th>
        <th scope="col" width="75">P.Unit. S/Imp.</th>
        <th scope="col" width="25">Cot. Asig.</th>
        <th scope="col" width="25">Exon. Imp.</th>
        <th scope="col" width="75">P.Unit. C/Imp.</th>
        <th scope="col" width="50">% Desc.</th>
        <th scope="col" width="50">Desc. Fijo</th>
        <th scope="col" width="75">P.Unit. s/Imp. c/Desc.</th>
        <th scope="col" width="75">P.Unit. c/Imp. c/Desc.</th>
        <th scope="col" width="100">Total</th>
        <th scope="col" align="left">Observaciones</th>
    </tr>
    </thead>
    
    <tbody id="lista_items">
	<?php
	//	consulto lista
	$sql = "SELECT
				c.*,
				rd.CodItem,
				rd.CommoditySub,
				rd.Descripcion,
				rd.CodUnidad,
				rd.CantidadPedida,
				r.CodInterno,
				i.CodImpuesto,
				i.FactorPorcentaje
			FROM
				lg_requerimientosdet rd
				INNER JOIN lg_requerimientos r ON (rd.CodRequerimiento = r.CodRequerimiento)
				INNER JOIN lg_cotizacion c ON (rd.CodOrganismo = c.CodOrganismo AND 
											   rd.CodRequerimiento = c.CodRequerimiento AND 
											   rd.Secuencia = c.Secuencia)
				INNER JOIN mastpersonas p ON (c.CodProveedor = p.CodPersona)
				INNER JOIN mastproveedores mp ON (c.CodProveedor = mp.CodProveedor)
				LEFT JOIN masttiposervicioimpuesto tsi ON (mp.CodTipoServicio = tsi.CodTipoServicio)
				LEFT JOIN mastimpuestos i ON (tsi.CodImpuesto = i.CodImpuesto AND i.CodRegimenFiscal = 'I')
			WHERE c.NroCotizacionProv = '".$sel_registros."'";
	$query_items = mysql_query($sql) or die ($sql.mysql_error());
	$rows_lista = mysql_num_rows($query_items);
	$nro_items = 0;
	while ($field_items = mysql_fetch_array($query_items)) {
		$id = "items_".$field_items['CotizacionSecuencia'];
		if ($field_items['CodItem'] != "") $Codigo = $field_items['CodItem']; else $Codigo = $field_items['CommoditySub'];
		?>
		<tr class="trListaBody" onclick="clk($(this), 'items', '<?=$id?>');" id="<?=$id?>">
        	<th>
            	<?=++$nro_items?>
            </th>
			<td align="center">
            	<input type="hidden" name="CotizacionSecuencia" value="<?=$field_items['CotizacionSecuencia']?>" />
            	<input type="hidden" name="Codigo" value="<?=$Codigo?>" />
				<?=$field_items['CodInterno']?>
            </td>
			<td align="center">
				<?=$field_items['Secuencia']?>
            </td>
			<td align="center">
				<?=$Codigo?>
            </td>
            <td>
            	<textarea name="Descripcion" class="cell" style="height:15px;" readonly="readonly"><?=htmlentities($field_items['Descripcion'])?></textarea>
            </td>
            <td align="center">
				<input type="text" name="CodUnidad" id="CodUnidad_<?=$id?>" value="<?=$field_items['CodUnidad']?>" class="cell2 currency" style="text-align:center;" readonly />
            </td>
            <td align="center">
				<input type="text" name="Cantidad" id="Cantidad_<?=$id?>" value="<?=number_format($field_items['Cantidad'], 2, ',', '.')?>" class="cell currency" style="text-align:right;" />
            </td>
            <td align="center">
				<select name="CodUnidadCompra" id="CodUnidadCompra_<?=$id?>" class="cell">
                	<?=loadSelect("mastunidades", "CodUnidad", "CodUnidad", $field_items['CodUnidadCompra'], 0)?>
                </select>
            </td>
            <td align="center">
				<input type="text" name="CantidadCompra" id="CantidadCompra_<?=$id?>" value="<?=number_format($field_items['CantidadCompra'], 2, ',', '.')?>" class="cell currency" style="text-align:right;" onchange="cotizaciones_proveedores_totales('<?=$id?>');" />
            </td>
            <td align="center">
				<input type="text" name="PrecioUnitInicio" id="PrecioUnitInicio_<?=$id?>" value="<?=number_format($field_items['PrecioUnitInicio'], 2, ',', '.')?>" class="cell currency" style="text-align:right;" onchange="cotizaciones_proveedores_totales('<?=$id?>');" />
            </td>
            <td align="center">
				<input type="checkbox" name="FlagAsignado" id="FlagAsignado_<?=$id?>" class="FlagAsignado" <?=chkOpt("S", $field_items['FlagAsignado'])?> />
            </td>
            <td align="center">
				<input type="checkbox" name="FlagExonerado" id="FlagExonerado_<?=$id?>" value="<?=$field_items['FactorPorcentaje']?>" <?=chkOpt("S", $field_items['FlagExonerado'])?> onchange="cotizaciones_proveedores_totales('<?=$id?>');" />
            </td>
            <td align="center">
				<input type="text" name="PrecioUnitInicioIva" id="PrecioUnitInicioIva_<?=$id?>" value="<?=number_format($field_items['PrecioUnitInicioIva'], 2, ',', '.')?>" class="cell2 currency" style="text-align:right;" readonly />
            </td>
            <td align="center">
				<input type="text" name="DescuentoPorcentaje" id="DescuentoPorcentaje_<?=$id?>" value="<?=number_format($field_items['DescuentoPorcentaje'], 2, ',', '.')?>" class="cell currency DescuentoPorcentaje" style="text-align:right;" onchange="cotizaciones_proveedores_totales('<?=$id?>');" />
            </td>
            <td align="center">
				<input type="text" name="DescuentoFijo" id="DescuentoFijo_<?=$id?>" value="<?=number_format($field_items['DescuentoFijo'], 2, ',', '.')?>" class="cell currency DescuentoFijo" style="text-align:right;" onchange="cotizaciones_proveedores_totales('<?=$id?>');" />
            </td>
            <td align="center">
				<input type="text" name="PrecioUnit" id="PrecioUnit_<?=$id?>" value="<?=number_format($field_items['PrecioUnit'], 2, ',', '.')?>" class="cell2 currency" style="text-align:right;" readonly />
            </td>
            <td align="center">
				<input type="text" name="PrecioUnitIva" id="PrecioUnitIva_<?=$id?>" value="<?=number_format($field_items['PrecioUnitIva'], 2, ',', '.')?>" class="cell2 currency" style="text-align:right;" readonly />
            </td>
            <td align="center">
				<input type="text" name="Total" id="Total_<?=$id?>" value="<?=number_format($field_items['Total'], 2, ',', '.')?>" class="cell2 currency" style="text-align:right; font-weight:bold;" readonly />
            </td>
            <td align="center">
				<textarea name="Observaciones" class="cell" style="height:15px;"><?=htmlentities($field_items['Observaciones'])?></textarea>
            </td>
		</tr>
		<?php
	}
	?>
    </tbody>
</table>
</div>
<input type="hidden" id="nro_items" value="<?=$nro_items?>" />
<input type="hidden" id="can_items" value="<?=$nro_items?>" />
</form>
</center>

<div style="width:<?=$_width?>px" class="divMsj">Campos Obligatorios *</div>