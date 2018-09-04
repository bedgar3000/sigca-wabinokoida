<?php
list($AnioActual, $MesActual, $DiaActual) = split("[/.-]", substr($Ahora, 0, 10));
$FechaActual = "$AnioActual-$MesActual-$DiaActual";
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$fCodDependencia = $_SESSION["CODDEPENDENCIA_ACTUAL"];
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderByItems = "CodInterno,Secuencia";
	$fOrderByCommodity = "CodInterno,Secuencia";
	$id_tab = 0;
}
$filtro = "";
$filtro1 = "";
$filtro2 = "";
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (rd.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fCodDependencia != "") { $cCodDependencia = "checked"; $filtro.=" AND (r.CodDependencia = '".$fCodDependencia."')"; } else $dCodDependencia = "disabled";
if ($fCodCentroCosto != "") { $cCodCentroCosto = "checked"; $filtro.=" AND (rd.CodCentroCosto = '".$fCodCentroCosto."')"; } else $dCodCentroCosto = "disabled";
if ($fClasificacion != "") { $cClasificacion = "checked"; $filtro.=" AND (r.Clasificacion = '".$fClasificacion."')"; } else $dClasificacion = "disabled";
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro1 .= " AND (r.CodInterno LIKE '%".$fBuscar."%' OR
					   rd.Secuencia LIKE '%".$fBuscar."%' OR
					   rd.CodItem LIKE '%".$fBuscar."%' OR
					   rd.Descripcion LIKE '%".$fBuscar."%' OR
					   rd.CodUnidad LIKE '%".$fBuscar."%' OR
					   rd.CotizacionRegistros LIKE '%".$fBuscar."%' OR
					   rd.CodCentroCosto LIKE '%".$fBuscar."%' OR
					   rd.Comentarios LIKE '%".$fBuscar."%' OR
					   i.CodLinea LIKE '%".$fBuscar."%' OR
					   i.CodFamilia LIKE '%".$fBuscar."%' OR
					   CONCAT(i.CodLinea, ' - ', i.CodFamilia) LIKE '%".$fBuscar."%' OR
					   c2.NomProveedor LIKE '%".$fBuscar."%' OR
					   c.NomProveedor LIKE '%".$fBuscar."%')";
	$filtro2 .= " AND (r.CodInterno LIKE '%".$fBuscar."%' OR
					   rd.Secuencia LIKE '%".$fBuscar."%' OR
					   rd.CodItem LIKE '%".$fBuscar."%' OR
					   rd.Descripcion LIKE '%".$fBuscar."%' OR
					   rd.CodUnidad LIKE '%".$fBuscar."%' OR
					   rd.CotizacionRegistros LIKE '%".$fBuscar."%' OR
					   rd.CodCentroCosto LIKE '%".$fBuscar."%' OR
					   rd.Comentarios LIKE '%".$fBuscar."%' OR
					   c2.NomProveedor LIKE '%".$fBuscar."%' OR
					   c.NomProveedor LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
$i=0;
$display_tab[0] = "display:none;";
$display_tab[1] = "display:none;";
foreach($display_tab as $_tab) {
	if ($id_tab == $i) { $display_tab[$i] = "display:block;"; $current_tab[$i] = "current"; }
	else { $display_tab[$i] = "display:none;"; $current_tab[$i] = ""; }
	++$i;
}
//	------------------------------------
$_width = 1000;
$_sufijo = "cotizaciones_items_invitar";
$_titulo = "Invitar/Cotizar Proveedores";
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
<input type="hidden" name="fOrderByItems" id="fOrderByItems" value="<?=$fOrderByItems?>" />
<input type="hidden" name="fOrderByCommodity" id="fOrderByCommodity" value="<?=$fOrderByCommodity?>" />
<input type="hidden" name="id_tab" id="id_tab" value="<?=$id_tab?>" />
<input type="hidden" name="sel_registros" id="sel_registros" />

<!--FILTRO-->
<div class="divBorder" style="width:<?=$_width?>px;">
<table width="<?=$_width?>" class="tblFiltro">
	<tr>
		<td align="right" width="100">Organismo:</td>
		<td>
			<input type="checkbox" <?=$cCodOrganismo?> onclick="this.checked=!this.checked" />
			<select name="fCodOrganismo" id="fCodOrganismo" style="width:275px;" onChange="getOptionsSelect(this.value, 'dependencia_filtro', 'fCodDependencia', true, 'fCodCentroCosto');" <?=$dCodOrganismo?>>
				<?=getOrganismos($fCodOrganismo, 3)?>
			</select>
		</td>
		<td align="right" width="100">Clasificaci&oacute;n:</td>
		<td>
			<input type="checkbox" <?=$cClasificacion?> onclick="chkFiltro(this.checked, 'fClasificacion');" />
			<select name="fClasificacion" id="fClasificacion" style="width:275px;" <?=$dClasificacion?>>
            	<option value="">&nbsp;</option>
				<?=loadSelect("lg_clasificacion", "Clasificacion", "Descripcion", $fClasificacion, 0)?>
			</select>
		</td>
	</tr>
	<tr>
		<td align="right">Dependencia:</td>
		<td>
			<input type="checkbox" <?=$cCodDependencia?> onclick="chkFiltro(this.checked, 'fCodDependencia');" />
			<select name="fCodDependencia" id="fCodDependencia" style="width:275px;" onChange="getOptionsSelect(this.value, 'centro_costo', 'fCodCentroCosto', true);" <?=$dCodDependencia?>>
            	<option value="">&nbsp;</option>
				<?=getDependencias($fCodDependencia, $fCodOrganismo, 3)?>
			</select>
		</td>
		<td align="right">Buscar:</td>
		<td>
			<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
			<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:270px;" <?=$dBuscar?> />
		</td>
	</tr>
	<tr>
		<td align="right">Centro de Costo:</td>
		<td>
			<input type="checkbox" <?=$cCodCentroCosto?> onclick="chkFiltro(this.checked, 'fCodCentroCosto');" />
			<select name="fCodCentroCosto" id="fCodCentroCosto" style="width:275px;" <?=$dCodCentroCosto?>>
            	<option value="">&nbsp;</option>
				<?=loadSelectDependiente("ac_mastcentrocosto", "CodCentroCosto", "Descripcion", "CodDependencia", $fCodCentroCosto, $fCodDependencia, 0)?>
			</select>
		</td>
	</tr>
</table>
</div>
<center><input type="submit" value="Buscar"></center><br />

<table width="<?=$_width?>" align="center" cellpadding="0" cellspacing="0">
    <tr>
        <td>
            <div class="header">
            <ul id="tab">
            <!-- CSS Tabs -->
            <li id="li1" class="<?=$current_tab[0]?>" onclick="currentTab('tab', this); $('#id_tab').val('0');">
            	<a href="#" onclick="mostrarTab('tab', '1', 2)">Stock</a>
            </li>
            <li id="li2" class="<?=$current_tab[1]?>" onclick="currentTab('tab', this); $('#id_tab').val('1');">
            	<a href="#" onclick="mostrarTab('tab', '2', 2);">Commodities</a>
            </li>
            </ul>
            </div>
        </td>
    </tr>
</table>

<!--REGISTROS-->
<center>

<div id="tab1" style=" <?=$display_tab[0]?>;">
<input type="hidden" name="sel_items" id="sel_items" />
<table width="<?=$_width?>" class="tblBotones">
    <tr>
        <td><div id="rows"></div></td>
        <td align="right" class="gallery clearfix">
            <input type="button" id="btInvitarItem" value="Invitar" style="width:75px;" onclick="cargarOpcionMultiple2(this.form, 'gehen.php?anz=lg_cotizaciones_items_invitar_proveedores', 'SELF', '', 'items', 'sel_registros', 1)" />
            
            <input type="button" id="btCotizarItem" value="Cotizar" style="width:75px;" onclick="cargarOpcionMultiple2(this.form, 'gehen.php?anz=lg_cotizaciones_items_invitar_cotizar', 'SELF', '', 'items', 'sel_registros', 0)" /> |
            
            <input type="button" id="btImprimirItem" value="Cuadro Comparativo" style="width:125px;" onclick="cotizaciones_items_cuadro_abrir('items');" />
            
            <input type="button" id="btVerItem" value="Ver Requerimiento" style="width:125px;" onclick="cargarOpcionMultiple2(this.form, 'gehen.php?anz=lg_requerimiento_form&opcion=ver&origen=lg_cotizaciones_items_invitar_lista', 'SELF', '', 'items', 'sel_registros', 0)" />
        </td>
    </tr>
</table>
<div style="overflow:scroll; width:<?=$_width?>px; height:300px;">
<table width="3000" class="tblLista">
	<thead>
    <tr>
    	<th scope="col" width="15"></th>
		<th scope="col" width="100" onclick="order('CodInterno,Secuencia', '', 'fOrderByItems');">Requerimiento</th>
		<th scope="col" width="20" onclick="order('Secuencia', '', 'fOrderByItems');">#</th>
		<th scope="col" width="80" onclick="order('CodItem', '', 'fOrderByItems');">Item</th>
		<th scope="col" width="500" align="left" onclick="order('Descripcion', '', 'fOrderByItems');">Descripci&oacute;n</th>
		<th scope="col" width="35" onclick="order('CodUnidad', '', 'fOrderByItems');">Uni.</th>
		<th scope="col" width="50" onclick="order('CantidadPedida', '', 'fOrderByItems');">Cant.</th>
		<th scope="col" width="25" onclick="order('CotizacionRegistros', '', 'fOrderByItems');">Inv.</th>
		<th scope="col" width="45" onclick="order('CodCentroCosto', '', 'fOrderByItems');">C.C.</th>
		<th scope="col" width="75" onclick="order('Prioridad', '', 'fOrderByItems');">Prioridad</th>
		<th scope="col" width="115" onclick="order('CodLinea,CodFamilia', '', 'fOrderByItems');">Linea-Familia</th>
		<th scope="col" align="left" onclick="order('Comentarios', '', 'fOrderByItems');">Comentario</th>
		<th scope="col" width="75" onclick="order('FechaRequerida', '', 'fOrderByItems');">Fecha Requerida</th>
		<th scope="col" width="300" align="left" onclick="order('NomProveedorAsignado', '', 'fOrderByItems');">Proveedor Asignado</th>
		<th scope="col" width="75" onclick="order('CotizacionFechaAsignacion', '', 'fOrderByItems');">Fecha Asignaci&oacute;n</th>
		<th scope="col" width="300" align="left" onclick="order('NomProveedorSugerido', '', 'fOrderByItems');">Proveedor Sugerido</th>
    </tr>
    </thead>
    
    <tbody id="lista_items">
	<?php
	//	consulto lista
	$sql = "SELECT
				rd.CodOrganismo,
				rd.CodRequerimiento,
				rd.Secuencia,
				rd.CodItem,
				rd.Descripcion,
				rd.CodUnidad,
				rd.CantidadPedida,
				rd.CodCentroCosto,
				rd.CotizacionRegistros,
				rd.CotizacionFechaAsignacion,
				rd.FlagExonerado,
				o.Organismo,
				d.Dependencia,
				r.CodInterno,
				r.Clasificacion,
				r.FechaRequerida,
				r.Prioridad,
				r.Comentarios,
				i.CodLinea,
				i.CodFamilia,
				c.CodProveedor,
				c.FechaDocumento,
				c.FlagAsignado,
				c.Numero,
				p.NomCompleto As NomProveedorSugerido,
				c2.NomProveedor AS NomProveedorAsignado
			FROM
				lg_requerimientosdet rd
				INNER JOIN lg_requerimientos r ON (rd.CodRequerimiento = r.CodRequerimiento)
				INNER JOIN lg_itemmast i ON (rd.CodItem = i.CodItem)
				INNER JOIN mastorganismos o ON (rd.CodOrganismo = o.CodOrganismo)
				INNER JOIN mastdependencias d ON (r.CodDependencia = d.CodDependencia)
				LEFT JOIN lg_cotizacion c ON (rd.CodOrganismo = c.CodOrganismo AND
											  rd.CodRequerimiento = c.CodRequerimiento AND
											  rd.Secuencia = c.Secuencia)
				LEFT JOIN lg_cotizacion c2 ON (rd.CodOrganismo = c2.CodOrganismo AND
											   rd.CodRequerimiento = c2.CodRequerimiento AND
											   rd.Secuencia = c2.Secuencia AND
											   c2.FlagAsignado = 'S')
				LEFT JOIN mastpersonas p ON (r.ProveedorSugerido = p.CodPersona)
			WHERE
				rd.Estado = 'PE' AND
				rd.FlagCompraAlmacen = 'C' AND
				r.FlagCajaChica <> 'S'
				$filtro $filtro1
			GROUP BY CodOrganismo, CodRequerimiento, Secuencia
			ORDER BY $fOrderByItems";
	$query_items = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	$rows_lista = mysql_num_rows($query_items);
	$nro_items = 0;
	while ($field_items = mysql_fetch_array($query_items)) {
		$id = $field_items['CodRequerimiento']."_".$field_items['Secuencia']."_".$field_items['CodOrganismo']."_".$field_items['FlagExonerado'];
		if (strlen($field_items['Comentarios']) > 150) $Comentarios = substr($field_items['Comentarios'], 0, 150)."...";
		else $Comentarios = $field_items['Comentarios'];
		?>
		<tr class="trListaBody" onclick="clkMulti($(this), '<?=$id?>');">
        	<th>
            	<input type="checkbox" name="items" id="<?=$id?>" value="<?=$id?>" style="display:none" />
            	<?=++$nro_items?>
            </th>
			<td align="center">
				<?=$field_items['CodInterno']?>
            </td>
			<td align="center"><?=$field_items['Secuencia']?></td>
			<td align="center"><?=$field_items['CodItem']?></td>
			<td><?=htmlentities($field_items['Descripcion'])?></td>
			<td align="center"><?=$field_items['CodUnidad']?></td>
			<td align="right"><?=number_format($field_items['CantidadPedida'], 2, ',', '.')?></td>
			<td align="center"><?=$field_items['CotizacionRegistros']?></td>
			<td align="center"><?=$field_items['CodCentroCosto']?></td>
			<td align="center"><?=printValores("PRIORIDAD", $field_items['Prioridad'])?></td>
			<td align="center"><?=$field_items['CodLinea']?>-<?=$field_items['CodFamilia']?></td>
			<td title="<?=htmlentities($field_items['Comentarios'])?>"><?=htmlentities($Comentarios)?></td>
			<td align="center"><?=formatFechaDMA($field_items['FechaRequerida'])?></td>
			<td><?=htmlentities($field_items['NomProveedorAsignado'])?></td>
			<td align="center"><?=formatFechaDMA($field_items['CotizacionFechaAsignacion'])?></td>
			<td><?=htmlentities($field_items['NomProveedorSugerido'])?></td>
		</tr>
		<?php
	}
	?>
    </tbody>
</table>
</div>
</div>

<div id="tab2" style=" <?=$display_tab[1]?>;">
<input type="hidden" name="sel_commodity" id="sel_commodity" />
<table width="<?=$_width?>" class="tblBotones">
    <tr>
        <td><div id="rows"></div></td>
        <td align="right" class="gallery clearfix">
            <input type="button" id="btInvitarCommodity" value="Invitar" style="width:75px;" onclick="cargarOpcionMultiple2(this.form, 'gehen.php?anz=lg_cotizaciones_items_invitar_proveedores', 'SELF', '', 'commodity', 'sel_registros', 1)" />
            
            <input type="button" id="btCotizarCommodity" value="Cotizar" style="width:75px;" onclick="cargarOpcionMultiple2(this.form, 'gehen.php?anz=lg_cotizaciones_items_invitar_cotizar', 'SELF', '', 'commodity', 'sel_registros', 0)" /> |
            
            <input type="button" id="btImprimirCommodity" value="Cuadro Comparativo" style="width:125px;" onclick="cotizaciones_items_cuadro_abrir('commodity');" />
            
            <input type="button" id="btVerCommodity" value="Ver Requerimiento" style="width:125px;" onclick="cargarOpcionMultiple2(this.form, 'gehen.php?anz=lg_requerimiento_form&opcion=ver&origen=lg_cotizaciones_items_invitar_lista', 'SELF', '', 'commodity', 'sel_registros', 0)" />
        </td>
    </tr>
</table>
<div style="overflow:scroll; width:<?=$_width?>px; height:300px;">
<table width="3000" class="tblLista">
	<thead>
    <tr>
    	<th scope="col" width="15"></th>
		<th scope="col" width="100" onclick="order('CodInterno,Secuencia', '', 'fOrderByCommodity');">Requerimiento</th>
		<th scope="col" width="20" onclick="order('Secuencia', '', 'fOrderByCommodity');">#</th>
		<th scope="col" width="80" onclick="order('CodItem', '', 'fOrderByCommodity');">Commodity</th>
		<th scope="col" width="500" align="left" onclick="order('Descripcion', '', 'fOrderByCommodity');">Descripci&oacute;n</th>
		<th scope="col" width="35" onclick="order('CodUnidad', '', 'fOrderByCommodity');">Uni.</th>
		<th scope="col" width="50" onclick="order('CantidadPedida', '', 'fOrderByCommodity');">Cant.</th>
		<th scope="col" width="25" onclick="order('CotizacionRegistros', '', 'fOrderByCommodity');">Inv.</th>
		<th scope="col" width="45" onclick="order('CodCentroCosto', '', 'fOrderByCommodity');">C.C.</th>
		<th scope="col" width="75" onclick="order('Prioridad', '', 'fOrderByCommodity');">Prioridad</th>
		<th scope="col" align="left" onclick="order('Comentarios', '', 'fOrderByCommodity');">Comentario</th>
		<th scope="col" width="75" onclick="order('FechaRequerida', '', 'fOrderByCommodity');">Fecha Requerida</th>
		<th scope="col" width="300" align="left" onclick="order('NomProveedorAsignado', '', 'fOrderByCommodity');">Proveedor Asignado</th>
		<th scope="col" width="75" onclick="order('CotizacionFechaAsignacion', '', 'fOrderByCommodity');">Fecha Asignaci&oacute;n</th>
		<th scope="col" width="300" align="left" onclick="order('NomProveedorSugerido', '', 'fOrderByCommodity');">Proveedor Sugerido</th>
    </tr>
    </thead>
    
    <tbody id="lista_commodity">
	<?php
	//	consulto lista
	$sql = "SELECT
				rd.CodOrganismo,
				rd.CodRequerimiento,
				rd.Secuencia,
				rd.CommoditySub,
				rd.Descripcion,
				rd.CodUnidad,
				rd.CantidadPedida,
				rd.CodCentroCosto,
				rd.CotizacionRegistros,
				rd.CotizacionFechaAsignacion,
				rd.FlagExonerado,
				o.Organismo,
				d.Dependencia,
				r.CodInterno,
				r.Clasificacion,
				r.FechaRequerida,
				r.Prioridad,
				r.Comentarios,
				c.CodProveedor,
				c.FechaDocumento,
				c.FlagAsignado,
				c.Numero,
				p.NomCompleto As NomProveedorSugerido,
				c2.NomProveedor AS NomProveedorAsignado
			FROM
				lg_requerimientosdet rd
				INNER JOIN lg_requerimientos r ON (rd.CodRequerimiento = r.CodRequerimiento)
				INNER JOIN lg_commoditysub cs ON (rd.CommoditySub = cs.Codigo)
				INNER JOIN mastorganismos o ON (rd.CodOrganismo = o.CodOrganismo)
				INNER JOIN mastdependencias d ON (r.CodDependencia = d.CodDependencia)
				LEFT JOIN lg_cotizacion c ON (rd.CodOrganismo = c.CodOrganismo AND
											  rd.CodRequerimiento = c.CodRequerimiento AND
											  rd.Secuencia = c.Secuencia)
				LEFT JOIN lg_cotizacion c2 ON (rd.CodOrganismo = c2.CodOrganismo AND
											   rd.CodRequerimiento = c2.CodRequerimiento AND
											   rd.Secuencia = c2.Secuencia AND
											   c2.FlagAsignado = 'S')
				LEFT JOIN mastpersonas p ON (r.ProveedorSugerido = p.CodPersona)
			WHERE
				rd.Estado = 'PE' AND
				rd.FlagCompraAlmacen = 'C' AND
				r.FlagCajaChica <> 'S'
				$filtro $filtro2
			GROUP BY CodOrganismo, CodRequerimiento, Secuencia
			ORDER BY $fOrderByCommodity";
	$query_commodity = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	$rows_lista = mysql_num_rows($query_commodity);
	$nro_commodity = 0;
	while ($field_commodity = mysql_fetch_array($query_commodity)) {
		$id = $field_commodity['CodRequerimiento']."_".$field_commodity['Secuencia']."_".$field_commodity['CodOrganismo']."_".$field_commodity['FlagExonerado'];
		if (strlen($field_commodity['Comentarios']) > 150) $Comentarios = substr($field_commodity['Comentarios'], 0, 150)."...";
		else $Comentarios = $field_commodity['Comentarios'];
		?><tr class="trListaBody" onclick="clkMulti($(this), '<?=$id?>');">
        	<th>
            	<input type="checkbox" name="commodity" id="<?=$id?>" value="<?=$id?>" style="display:none" />
            	<?=++$nro_commodity?>
            </th>
			<td align="center">
				<?=$field_commodity['CodInterno']?>
            </td>
			<td align="center"><?=$field_commodity['Secuencia']?></td>
			<td align="center"><?=$field_commodity['CommoditySub']?></td>
			<td><?=htmlentities($field_commodity['Descripcion'])?></td>
			<td align="center"><?=$field_commodity['CodUnidad']?></td>
			<td align="right"><?=number_format($field_commodity['CantidadPedida'], 2, ',', '.')?></td>
			<td align="center"><?=$field_commodity['CotizacionRegistros']?></td>
			<td align="center"><?=$field_commodity['CodCentroCosto']?></td>
			<td align="center"><?=printValores("PRIORIDAD", $field_commodity['Prioridad'])?></td>
			<td title="<?=htmlentities($field_commodity['Comentarios'])?>"><?=htmlentities($Comentarios)?></td>
			<td align="center"><?=formatFechaDMA($field_commodity['FechaRequerida'])?></td>
			<td><?=htmlentities($field_commodity['NomProveedorAsignado'])?></td>
			<td align="center"><?=formatFechaDMA($field_commodity['CotizacionFechaAsignacion'])?></td>
			<td><?=htmlentities($field_commodity['NomProveedorSugerido'])?></td>
		</tr>
		<?php
	}
	?>
    </tbody>
</table>
</div>
</div>
</center>
</form>

<div class="gallery clearfix">
    <a href="pagina.php?iframe=true" rel="prettyPhoto[iframe1]" style="display:none;" id="a_invitacion"></a>
    <a href="pagina.php?iframe=true" rel="prettyPhoto[iframe2]" style="display:none;" id="a_cuadro"></a>
</div>
<?php
//	muestro vouchers
if ($imprimir == "lg_cotizaciones_invitacion_pdf") {
	?>
    <script type="text/javascript">
	$(document).ready(function() {
		var url = "lg_cotizaciones_invitacion_pdf.php?origen=cotizaciones_items_invitar_lista&Numero=<?=$Numero?>&iframe=true&width=100%&height=100%";
		$("#a_invitacion").attr("href", url);
		document.getElementById("a_invitacion").click();
    });
    </script>
    <?php
}
?>