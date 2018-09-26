<?php
list($AnioActual, $MesActual, $DiaActual) = split("[/.-]", substr($Ahora, 0, 10));
$FechaActual = "$AnioActual-$MesActual-$DiaActual";
list($CodRequerimiento, $Secuencia, $CodOrganismo) = split("_", $sel_registros);
//	------------------------------------
//	requerimiento
$sql = "SELECT
			rd.CodItem,
			rd.CommoditySub,
			rd.CodCuenta,
			rd.CodCuentaPub20,
			rd.CantidadPedida,
			rd.Descripcion,
			rd.CodUnidad,
			rd.CodCentroCosto,
			(SELECT MIN(PrecioUnitIva)
			 FROM lg_cotizacion c
			 WHERE
			 	c.CodRequerimiento = rd.CodRequerimiento AND
				c.Secuencia = rd.Secuencia AND
				c.PrecioUnitIva <> 0.00) AS Minimo
		FROM lg_requerimientosdet rd
		WHERE
			rd.CodRequerimiento = '".$CodRequerimiento."' AND
			rd.Secuencia = '".$Secuencia."'";
$query_requerimiento = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
if (mysql_num_rows($query_requerimiento)) $field_requerimiento = mysql_fetch_array($query_requerimiento);
if ($field_requerimiento['CodItem'] != "") $Codigo = $field_requerimiento['CodItem'];
else $Codigo = $field_requerimiento['CommoditySub'];
//	------------------------------------
$clkCancelar = "document.getElementById('frmentrada').submit();";
$_width = 900;
$_sufijo = "cotizaciones_items_invitar_cotizar";
$_titulo = "Cotizaciones de los Proveedores Invitados";
//	------------------------------------
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lg_cotizaciones_items_invitar_lista" method="POST" enctype="multipart/form-data" onsubmit="return <?=$_sufijo?>_validar(this);" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
<input type="hidden" name="fOrderByItems" id="fOrderByItems" value="<?=$fOrderByItems?>" />
<input type="hidden" name="fOrderByCommodity" id="fOrderByCommodity" value="<?=$fOrderByCommodity?>" />
<input type="hidden" name="id_tab" id="id_tab" value="<?=$id_tab?>" />
<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
<input type="hidden" name="fCodDependencia" id="fCodDependencia" value="<?=$fCodDependencia?>" />
<input type="hidden" name="fCodCentroCosto" id="fCodCentroCosto" value="<?=$fCodCentroCosto?>" />
<input type="hidden" name="fCodClasificacion" id="fCodClasificacion" value="<?=$fCodClasificacion?>" />
<input type="hidden" id="CodRequerimiento" value="<?=$CodRequerimiento?>" />
<input type="hidden" id="Secuencia" value="<?=$Secuencia?>" />
<input type="hidden" id="CodOrganismo" value="<?=$CodOrganismo?>" />

<table width="<?=$_width?>" class="tblForm">
	<tr>
    	<td colspan="7" class="divFormCaption">Informaci&oacute;n del Item/Commodity</td>
    </tr>
	<tr>
		<td>
			<input type="hidden" id="CodItem" value="<?=$field_requerimiento['CodItem']?>" />
			<input type="hidden" id="CommoditySub" value="<?=$field_requerimiento['CommoditySub']?>" />
        	<input type="text" value="<?=$Codigo?>" style="width:75px;" class="codigo" disabled />
		</td>
		<td class="tagForm">Cuenta:</td>
		<td>
        	<input type="text" id="CodCuenta" value="<?=$field_requerimiento['CodCuenta']?>" style="width:90px;" disabled />
		</td>
		<td class="tagForm">Cuenta (Pub.20):</td>
		<td width="5">
        	<input type="text" id="CodCuentaPub20" value="<?=$field_requerimiento['CodCuentaPub20']?>" style="width:90px;" disabled />
		</td>
		<td class="tagForm">Cantidad:</td>
		<td width="5">
        	<input type="text" id="CantidadPedida" value="<?=number_format($field_requerimiento['CantidadPedida'], 2, ',', '.')?>" style="width:60px;" disabled />
		</td>
	</tr>
	<tr>
		<td colspan="5" rowspan="2">
        	<textarea id="Descripcion" style="width:99%; height:40px;" class="codigo" disabled><?=htmlentities($field_requerimiento['Descripcion'])?></textarea>
		</td>
		<td class="tagForm">Uni.:</td>
		<td>
        	<input type="text" id="CodUnidad" value="<?=$field_requerimiento['CodUnidad']?>" style="width:60px;" disabled />
		</td>
	</tr>
	<tr>
		<td class="tagForm">C.Costo:</td>
		<td>
        	<input type="text" id="CodCentroCosto" value="<?=$field_requerimiento['CodCentroCosto']?>" style="width:60px;" disabled />
		</td>
	</tr>
</table>

<center>
<input type="submit" value="Aceptar" style="width:75px;" />
<input type="button" value="Cancelar" style="width:75px;" onclick="<?=$clkCancelar?>" />
</center>
</form>

<center>
<form name="frm_proveedores" id="frm_proveedores" autocomplete="off">
<input type="hidden" id="borrar_proveedores" />
<input type="hidden" id="sel_proveedores" />
<table width="<?=$_width?>" class="tblBotones">
	<thead>
    <tr>
    	<th class="divFormCaption" colspan="2">Proveedores Invitados</th>
	</tr>
    </thead>
    <tbody>
    <tr>
        <td class="gallery clearfix">
            <input type="button" style="width:125px;" value="Disp. Presupuestaria" />
            <input type="button" style="width:125px;" value="Ultimas Cotizaciones" />
            <a href="pagina.php?iframe=true" rel="prettyPhoto[iframe1]" style="display:none;" id="a_imprimir"></a>
            <input type="button" style="width:125px;" value="Imprimir Invitaci&oacute;n" onclick="abrirIFrame(document.getElementById('frmentrada'), 'a_imprimir', 'lg_cotizaciones_invitacion_pdf.php?origen=cotizaciones_items_invitar_cotizar&CodProveedor='+$('#sel_proveedores').val().substr(12), '100%', '100%', $('#sel_proveedores').val());" />
        </td>
        <td align="right" class="gallery clearfix">
            <a id="a_proveedores" href="../lib/listas/listado_personas.php?filtrar=default&ventana=cotizaciones_proveedores_cotizar_insertar&detalle=proveedores&EsProveedor=S&CodRequerimiento=<?=$CodRequerimiento?>&Secuencia=<?=$Secuencia?>&iframe=true&width=925&height=475" rel="prettyPhoto[iframe2]" style="display:none;"></a>
            <input type="button" value="Insertar" class="btLista" onclick="$('#a_proveedores').click();" />
            <input type="button" value="Borrar" class="btLista" onclick="<?=$_sufijo?>_borrar(); quitar(this, 'proveedores');" />
        </td>
    </tr>
    </tbody>
</table>
<div style="overflow:scroll; width:<?=$_width?>px; height:175px;">
<table width="3100" class="tblLista">
    <thead>
    <tr>
        <th scope="col" width="15">&nbsp;</th>
        <th scope="col" align="left" width="300">Raz&oacute;n Social</th>
        <th scope="col" width="25">Cot. Asig.</th>
        <th scope="col" width="40">Uni. Rec.</th>
        <th scope="col" width="50">Cant. Rec.</th>
        <th scope="col" width="40">Uni. Compra.</th>
        <th scope="col" width="50">Cant. Compra.</th>
        <th scope="col" width="75">P.Unit. S/Imp.</th>
        <th scope="col" width="25">Exon. Imp.</th>
        <th scope="col" width="75">P.Unit. C/Imp.</th>
        <th scope="col" width="50">% Desc.</th>
        <th scope="col" width="50">Desc. Fijo</th>
        <th scope="col" width="75">P.Unit. Final</th>
        <th scope="col" width="100">Total</th>
        <th scope="col" width="100">Monto a Comparar</th>
        <th scope="col" width="25">Mejor Pre.</th>
        <th scope="col" width="125">Forma de Pago</th>
        <th scope="col" width="75">F. Invitación</th>
        <th scope="col" width="75">F. Entrega</th>
        <th scope="col" width="75">F. Recepci&oacute;n</th>
        <th scope="col" width="75">F. Limite</th>
        <th scope="col">Condiciones de Entrega</th>
        <th scope="col" width="400">Observaciones</th>
        <th scope="col" width="50">Dias Entega</th>
        <th scope="col" width="50">Validez Oferta</th>
        <th scope="col" width="100">Nro. Cotización</th>
        <th scope="col" width="75">Fecha Cotización</th>
        <th scope="col" width="100">Nro. Invitación</th>
    </tr>
    </thead>
    
    <tbody id="lista_proveedores">
	<?php
	//	consulto lista
	$sql = "SELECT
				c.*,
				rd.CodUnidad,
				i.CodImpuesto,
				i.FactorPorcentaje
			FROM
				lg_cotizacion c
				INNER JOIN lg_requerimientosdet rd ON (c.CodOrganismo = rd.CodOrganismo AND
													   c.CodRequerimiento = rd.CodRequerimiento AND
													   c.Secuencia = rd.Secuencia)
				INNER JOIN mastpersonas p ON (c.CodProveedor = p.CodPersona)
				INNER JOIN mastproveedores mp ON (c.CodProveedor = mp.CodProveedor)
				LEFT JOIN masttiposervicioimpuesto tsi ON (mp.CodTipoServicio = tsi.CodTipoServicio)
				LEFT JOIN mastimpuestos i ON (tsi.CodImpuesto = i.CodImpuesto)
			WHERE
				rd.CodRequerimiento = '".$CodRequerimiento."' AND
				rd.Secuencia = '".$Secuencia."' AND
				(i.CodRegimenFiscal = 'I' OR i.CodRegimenFiscal IS NULL)";
	$query_proveedores = mysql_query($sql) or die ($sql.mysql_error());
	$rows_lista = mysql_num_rows($query_proveedores);
	$nro_proveedores = 0;
	while ($field_proveedores = mysql_fetch_array($query_proveedores)) {
		$id = "proveedores_".$field_proveedores['CodProveedor'];
		?>
		<tr class="trListaBody" onclick="clk($(this), 'proveedores', '<?=$id?>');" id="<?=$id?>">
        	<th>
            	<?=++$nro_proveedores?>
            </th>
			<td>
            	<input type="hidden" name="CotizacionSecuencia" value="<?=$field_proveedores['CotizacionSecuencia']?>" />
            	<input type="hidden" name="CodProveedor" value="<?=$field_proveedores['CodProveedor']?>" />
            	<input type="hidden" name="NomProveedor" value="<?=htmlentities($field_proveedores['NomProveedor'])?>" />
				<?=htmlentities($field_proveedores['NomProveedor'])?>
            </td>
            <td align="center">
				<input type="checkbox" name="FlagAsignado" id="FlagAsignado_<?=$id?>" class="FlagAsignado" <?=chkOpt("S", $field_proveedores['FlagAsignado'])?> onclick="setFlagAsignado($(this));" />
            </td>
            <td align="center">
				<input type="text" name="CodUnidad" id="CodUnidad_<?=$id?>" value="<?=$field_proveedores['CodUnidad']?>" class="cell2 currency" style="text-align:center;" readonly="readonly" />
            </td>
            <td align="center">
				<input type="text" name="Cantidad" id="Cantidad_<?=$id?>" value="<?=number_format($field_proveedores['Cantidad'], 2, ',', '.')?>" class="cell currency" style="text-align:right;" />
            </td>
            <td align="center">
				<select name="CodUnidadCompra" id="CodUnidadCompra_<?=$id?>" class="cell">
                	<?=loadSelect("mastunidades", "CodUnidad", "CodUnidad", $field_proveedores['CodUnidadCompra'], 0)?>
                </select>
            </td>
            <td align="center">
				<input type="text" name="CantidadCompra" id="CantidadCompra_<?=$id?>" value="<?=number_format($field_proveedores['CantidadCompra'], 2, ',', '.')?>" class="cell currency" style="text-align:right;" onchange="cotizaciones_items_totales('<?=$id?>');" />
            </td>
            <td align="center">
				<input type="text" name="PrecioUnitInicio" id="PrecioUnitInicio_<?=$id?>" value="<?=number_format($field_proveedores['PrecioUnitInicio'], 2, ',', '.')?>" class="cell currency" style="text-align:right;" onchange="cotizaciones_items_totales('<?=$id?>');" />
            </td>
            <td align="center">
				<input type="checkbox" name="FlagExonerado" id="FlagExonerado_<?=$id?>" value="<?=$field_proveedores['FactorPorcentaje']?>" <?=chkOpt("S", $field_proveedores['FlagExonerado'])?> onchange="cotizaciones_items_totales('<?=$id?>');" />
            </td>
            <td align="center">
				<input type="text" name="PrecioUnitInicioIva" id="PrecioUnitInicioIva_<?=$id?>" value="<?=number_format($field_proveedores['PrecioUnitInicioIva'], 2, ',', '.')?>" class="cell2 currency" style="text-align:right;" readonly="readonly" />
            </td>
            <td align="center">
				<input type="text" name="DescuentoPorcentaje" id="DescuentoPorcentaje_<?=$id?>" value="<?=number_format($field_proveedores['DescuentoPorcentaje'], 2, ',', '.')?>" class="cell currency" style="text-align:right;" onchange="cotizaciones_items_totales('<?=$id?>');" />
            </td>
            <td align="center">
				<input type="text" name="DescuentoFijo" id="DescuentoFijo_<?=$id?>" value="<?=number_format($field_proveedores['DescuentoFijo'], 2, ',', '.')?>" class="cell currency" style="text-align:right;" onchange="cotizaciones_items_totales('<?=$id?>');" />
            </td>
            <td align="center">
				<input type="text" name="PrecioUnitIva" id="PrecioUnitIva_<?=$id?>" value="<?=number_format($field_proveedores['PrecioUnitIva'], 2, ',', '.')?>" class="cell2 currency" style="text-align:right;" readonly="readonly" />
            </td>
            <td align="center">
				<input type="text" name="Total" id="Total_<?=$id?>" value="<?=number_format($field_proveedores['Total'], 2, ',', '.')?>" class="cell2 currency" style="text-align:right; font-weight:bold;" readonly="readonly" />
            </td>
            <td align="center">
				<input type="text" name="PrecioUnitFinal" id="PrecioUnitFinal_<?=$id?>" value="<?=number_format($field_proveedores['PrecioUnitIva'], 2, ',', '.')?>" class="cell2 currency" style="text-align:right; font-weight:bold;" readonly="readonly" />
            </td>
            <td align="center">
				<input type="checkbox" name="FlagMejorPrecio" id="FlagMejorPrecio_<?=$id?>" <?=chkOpt("S", $field_proveedores['FlagMejorPrecio'])?> class="FlagMejorPrecio" onclick="this.checked=!this.checked;" />
            </td>
            <td align="center">
				<select name="CodFormaPago" class="cell">
                	<?=loadSelect("mastformapago", "CodFormaPago", "Descripcion", $field_proveedores['CodFormaPago'], 0)?>
                </select>
            </td>
            <td align="center">
				<input type="text" name="FechaInvitacion" value="<?=formatFechaDMA($field_proveedores['FechaInvitacion'])?>" class="cell datepicker" style="text-align:center;" />
            </td>
            <td align="center">
				<input type="text" name="FechaEntrega" value="<?=formatFechaDMA($field_proveedores['FechaEntrega'])?>" class="cell datepicker" style="text-align:center;" onchange="obtenerFechaFin($(this), $('#FechaLimite_<?=$id?>'), '<?=$_PARAMETRO['DIASLIMCOT']?>');" />
            </td>
            <td align="center">
				<input type="text" name="FechaRecepcion" value="<?=formatFechaDMA($field_proveedores['FechaRecepcion'])?>" class="cell datepicker" style="text-align:center;" />
            </td>
            <td align="center">
				<input type="text" name="FechaLimite" id="FechaLimite_<?=$id?>" value="<?=formatFechaDMA($field_proveedores['FechaLimite'])?>" class="cell2" style="text-align:center;" readonly="readonly" />
            </td>
            <td align="center">
				<textarea name="Condiciones" class="cell" style="height:15px;"><?=htmlentities($field_proveedores['Condiciones'])?></textarea>
            </td>
            <td align="center">
				<textarea name="Observaciones" class="cell" style="height:15px;"><?=htmlentities($field_proveedores['Observaciones'])?></textarea>
            </td>
            <td align="center">
				<input type="text" name="DiasEntrega" value="<?=$field_proveedores['DiasEntrega']?>" class="cell" />
            </td>
            <td align="center">
				<input type="text" name="ValidezOferta" value="<?=$field_proveedores['ValidezOferta']?>" class="cell" />
            </td>
            <td align="center">
				<input type="text" name="NumeroCotizacion" value="<?=$field_proveedores['NumeroCotizacion']?>" class="cell" maxlength="10" />
            </td>
            <td align="center">
            	<input type="text" name="FechaDocumento" value="<?=formatFechaDMA($field_proveedores['FechaDocumento'])?>" class="cell datepicker" style="text-align:center;" />
            </td>
            <td align="center">
				<?=$field_proveedores['NumeroInterno']?>
            </td>
		</tr>
		<?php
	}
	?>
    </tbody>
</table>
</div>
<input type="hidden" id="nro_proveedores" value="<?=$nro_proveedores?>" />
<input type="hidden" id="can_proveedores" value="<?=$nro_proveedores?>" />
</form>
</center>

<div style="width:<?=$_width?>px" class="divMsj">Campos Obligatorios *</div>

<div id="observaciones-form" title="Observaciones">
<table align="center" width="350">
	<tr>
    	<th>Explique por qu&eacute; no seleccion&oacute; el mejor Precio</th>
    </tr>
    <tr>
        <td>
            <textarea id="Observaciones" style="width:335px; height:50px;"></textarea>
        </td>
    </tr>
</table>
</div>

<script type="text/javascript" language="javascript">
$(document).ready(function() {
	$("#observaciones-form").dialog({
		autoOpen: false,
		modal: true,
		show: "fold",
		hide: "scale",
		width: 375,
		buttons: {
			"Aceptar": function() {
				$(this).dialog("close");
				<?=$_sufijo?>(document.getElementById('frmentrada'));
			}
		}
	});
});
</script>