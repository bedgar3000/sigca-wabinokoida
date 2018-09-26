<?php
list($AnioActual, $MesActual, $DiaActual) = split("[/.-]", substr($Ahora, 0, 10));
$FechaActual = "$AnioActual-$MesActual-$DiaActual";
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fFechaInvitacionD = "01-01-$AnioActual";
	$fFechaInvitacionH = "$DiaActual-$MesActual-$AnioActual";
	$fOrderBy = "FechaInvitacion, NroCotizacionProv";
}
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (c.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fFechaInvitacionD != "" || $fFechaInvitacionH != "") {
	$cFechaInvitacion = "checked";
	if ($fFechaInvitacionD != "") $filtro.=" AND (c.FechaInvitacion >= '".formatFechaAMD($fFechaInvitacionD)."')";
	if ($fFechaInvitacionH != "") $filtro.=" AND (c.FechaInvitacion <= '".formatFechaAMD($fFechaInvitacionH)."')";
} else $dFechaInvitacion = "disabled";
if ($fTotalD != "" || $fTotalH != "") {
	$cTotal = "checked";
	if ($fTotalD != "") $filtro.=" AND (c.Total >= ".floatval(setNumero($fTotalD)).")";
	if ($fTotalH != "") $filtro.=" AND (c.Total <= ".floatval(setNumero($fTotalD)).")";
} else $dTotal = "disabled";
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (c.CodProveedor LIKE '%".$fBuscar."%' OR
					  c.NomProveedor LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
//	------------------------------------
$_width = 1000;
$_sufijo = "cotizaciones_proveedores_invitar";
$_titulo = "Listar Invitaciones de Proveedores";
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lg_<?=$_sufijo?>_lista" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />

<!--FILTRO-->
<div class="divBorder" style="width:<?=$_width?>px;">
<table width="<?=$_width?>" class="tblFiltro">
	<tr>
		<td align="right" width="100">Organismo:</td>
		<td>
			<input type="checkbox" <?=$cCodOrganismo?> onclick="this.checked=!this.checked" />
			<select name="fCodOrganismo" id="fCodOrganismo" style="width:275px;" <?=$dCodOrganismo?>>
				<?=getOrganismos($fCodOrganismo, 3)?>
			</select>
		</td>
		<td align="right">F.Invitaci&oacute;n: </td>
		<td>
			<input type="checkbox" <?=$cFechaInvitacion?> onclick="chkFiltro_2(this.checked, 'fFechaInvitacionD', 'fFechaInvitacionH');" />
			<input type="text" name="fFechaInvitacionD" id="fFechaInvitacionD" value="<?=$fFechaInvitacionD?>" <?=$dFechaInvitacion?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFecha(this);" <?=$dFechaInvitacion?> /> -
            <input type="text" name="fFechaInvitacionH" id="fFechaInvitacionH" value="<?=$fFechaInvitacionH?>" <?=$dFechaInvitacion?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFecha(this);" <?=$dFechaInvitacion?> />
        </td>
	</tr>
	<tr>
		<td align="right">Buscar:</td>
		<td>
			<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
			<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:270px;" <?=$dBuscar?> />
		</td>
		<td align="right">Por Monto:</td>
		<td>
			<input type="checkbox" <?=$cTotal?> onclick="chkFiltro_2(this.checked, 'fTotalD', 'fTotalH');" />
			<input type="text" name="fTotalD" id="fTotalD" value="<?=number_format($fTotalD, 2, ',', '.')?>" <?=$dTotal?> style="width:60px; text-align:right;" class="currency" <?=$dTotal?> /> -
            <input type="text" name="fTotalH" id="fTotalH" value="<?=number_format($fTotalH, 2, ',', '.')?>" <?=$dTotal?> style="width:60px; text-align:right;" class="currency" <?=$dTotal?> />
        </td>
	</tr>
</table>
</div>
<center><input type="submit" value="Buscar"></center><br />

<!--REGISTROS-->
<center>
<input type="hidden" name="sel_registros" id="sel_registros" />
<table width="<?=$_width?>" class="tblBotones">
    <tr>
        <td><div id="rows"></div></td>
        <td align="right" class="gallery clearfix">
            <input type="button" id="btCotizar" value="Cotizar" style="width:75px;" onclick="cargarOpcion2(this.form, 'gehen.php?anz=lg_cotizaciones_proveedores_invitar_cotizar', 'SELF', '', $('#sel_registros').val());" />
            
            <input type="button" id="btEliminar" value="Eliminar" style="width:75px;" onclick="opcionRegistro2(this.form, $('#sel_registros').val(), 'cotizaciones', 'eliminar');" /> |
            
            <a href="pagina.php?iframe=true" rel="prettyPhoto[iframe1]" style="display:none;" id="a_imprimir"></a>
            <input type="button" id="btImprimir" value="Imprimir" style="width:75px;" onclick="abrirIFrame(this.form, 'a_imprimir', 'lg_cotizaciones_invitacion_pdf.php?origen=cotizaciones_proveedores_invitar_lista', '100%', '100%', $('#sel_registros').val());" />
            
            <input type="button" id="btDisponibilidad" value="Disp. Presupuestaria" style="width:125px;" />
        </td>
    </tr>
</table>

<div style="overflow:scroll; width:<?=$_width?>px; height:200px;">
<table width="100%" class="tblLista">
	<thead>
    <tr>
		<th width="75">F. Invitaci&oacute;n</th>
		<th width="75"># Invitaci&oacute;n</th>
		<th width="60">Proveedor</th>
		<th align="left">Raz&oacute;n Social</th>
		<th width="100">Total Cotizado</th>
		<th width="50">L&iacute;neas</th>
    </tr>
    </thead>
    
    <tbody id="lista_registros">
	<?php
	//	consulto lista
	$sql = "SELECT
				c.NroCotizacionProv,
				c.FechaInvitacion,
				c.NumeroInvitacion,
				c.CodProveedor,
				c.NomProveedor,
				c.NumeroInterno,
				SUM(c.Total) AS Total,
				(SELECT COUNT(*)
				 FROM
					lg_requerimientosdet rd2
					LEFT JOIN lg_itemmast i2 ON (rd2.CodItem = i2.CodItem)
					LEFT JOIN lg_commoditysub cs2 ON (rd2.CommoditySub = cs2.Codigo)
					INNER JOIN mastorganismos o2 ON (rd2.CodOrganismo = o2.CodOrganismo)
					INNER JOIN lg_requerimientos r2 ON (rd2.CodRequerimiento = r2.CodRequerimiento)
					INNER JOIN mastdependencias d2 ON (r2.CodDependencia = d2.CodDependencia)
					INNER JOIN lg_cotizacion c2 ON (rd2.CodOrganismo = c2.CodOrganismo AND
													rd2.CodRequerimiento = c2.CodRequerimiento AND
													rd2.Secuencia = c2.Secuencia)
				 WHERE c2.NroCotizacionProv = c.NroCotizacionProv) AS Nrolineas
			FROM
				lg_cotizacion c
				INNER JOIN lg_requerimientosdet rd ON (c.CodOrganismo = rd.CodOrganismo AND
													   c.CodRequerimiento = rd.CodRequerimiento AND
													   c.Secuencia = rd.Secuencia)
			WHERE rd.Estado = 'PE' $filtro
			GROUP BY CodProveedor, NroCotizacionProv
			ORDER BY $fOrderBy";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	$rows_lista = mysql_num_rows($query);
	while ($field = mysql_fetch_array($query)) {
		$id = "$field[NroCotizacionProv]";
		?>
		<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>'); innerHtml($('#lista_detalles'), 'accion=cotizaciones_proveedores_invitar_detalles&NroCotizacionProv=<?=$id?>');">
			<td align="center"><?=formatFechaDMA($field['FechaInvitacion'])?></td>
            <td align="center"><?=$field['NumeroInterno']?></td>
            <td align="center"><?=$field['CodProveedor']?></td>
            <td><?=htmlentities($field['NomProveedor'])?></td>
            <td align="right"><strong><?=number_format($field['Total'], 2, ',', '.')?></strong></td>
            <td align="center"><?=$field['Nrolineas']?></td>
		</tr>
		<?php
	}
	?>
    </tbody>
</table>
</div>

<div style="overflow:scroll; width:<?=$_width?>px; height:200px;">
<table width="2500" class="tblLista">
	<thead>
	<tr>
		<th width="75">C&oacute;digo</th>
		<th width="500" align="left">Descripci&oacute;n</th>
		<th width="35">Uni.</th>
		<th width="50">Cant.</th>
		<th width="75">Precio Unit.</th>
		<th width="75">Precio Unit Iva.</th>
		<th width="75">Precio Cantidad</th>
		<th width="75">Total</th>
		<th width="25">Exon.</th>
		<th width="100">Requerimiento</th>
		<th width="15">#</th>
		<th width="100">F.Requerida</th>
		<th width="35">C.C.</th>
		<th align="left">Observaciones</th>
	</tr>
    </thead>
    
    <tbody id="lista_detalles">
    </tbody>
</table>
</div>
</center>
</form>