<?php
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fFechaPreparaciond = "01-$MesActual-$AnioActual";
	$fFechaPreparacionh = "$DiaActual-$MesActual-$AnioActual";
}
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (oc.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fCodProveedor != "") { $cCodProveedor = "checked"; $filtro.=" AND (oc.CodProveedor = '".$fCodProveedor."')"; } else $dCodProveedor = "visibility:hidden;";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (oc.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fBuscar != "") { 
	$cBuscar = "checked"; 
	$filtro.=" AND (oc.NroInterno LIKE '%".$fBuscar."%' OR 
					oc.FechaPreparacion LIKE '%".utf8_decode($fBuscar)."%' OR 
					oc.NomProveedor LIKE '%".utf8_decode($fBuscar)."%' OR 
					oc.Observaciones LIKE '%".utf8_decode($fBuscar)."%' OR 
					oc.Anio LIKE '%".utf8_decode($fBuscar)."%')";
} else $dBuscar = "disabled";
if ($fFechaAprobaciond != "" || $fFechaAprobacionh != "") {
	$cFechaAprobacion = "checked";
	if ($fFechaAprobaciond != "") $filtro.=" AND (oc.FechaAprobacion >= '".formatFechaAMD($fFechaAprobaciond)."')";
	if ($fFechaAprobacionh != "") $filtro.=" AND (oc.FechaAprobacion <= '".formatFechaAMD($fFechaAprobacionh)."')";
} else $dFechaAprobacion = "disabled";
if ($fFechaPreparaciond != "" || $fFechaPreparacionh != "") {
	$cFechaPreparacion = "checked";
	if ($fFechaPreparaciond != "") $filtro.=" AND (oc.FechaPreparacion >= '".formatFechaAMD($fFechaPreparaciond)."')";
	if ($fFechaPreparacionh != "") $filtro.=" AND (oc.FechaPreparacion <= '".formatFechaAMD($fFechaPreparacionh)."')";
} else $dFechaPreparacion = "disabled";
//	------------------------------------
$_titulo = "Cuadros Comparativos de Ofertas";
$_width = 900;
$_sufijo = "reporte_cuadros_comparativos";
//	------------------------------------
$i=0;
$display_tab[0] = "display:none;";
$display_tab[1] = "display:none;";
foreach($display_tab as $_tab) {
	if ($id_tab == $i) { $display_tab[$i] = "display:block;"; $current_tab[$i] = "current"; }
	else { $display_tab[$i] = "display:none;"; $current_tab[$i] = ""; }
	++$i;
}
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lg_<?=$_sufijo?>" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="sel_registros" id="sel_registros" />
<input type="hidden" name="id_tab" id="id_tab" value="<?=$id_tab?>" />
<input type="hidden" name="origen" id="origen" value="reporte_cuadros_comparativos" />

<div class="divBorder" style="width:<?=$_width?>px;">
<table width="<?=$_width?>" class="tblFiltro">
	<tr>
		<td align="right" width="125">Organismo:</td>
		<td>
			<input type="checkbox" <?=$cCodOrganismo?> onclick="this.checked=!this.checked" />
			<select name="fCodOrganismo" id="fCodOrganismo" style="width:300px;" <?=$dCodOrganismo?>>
				<?=getOrganismos($fCodOrganismo, 3)?>
			</select>
		</td>
		<td align="right">Estado:</td>
		<td>
        	<?php 
			if ($lista == "revisar" || $lista == "aprobar") {
				?>
				<input type="checkbox" onclick="this.checked=!this.checked;" checked="checked" />
                <select name="fEstado" id="fEstado" style="width:150px;">
                    <?=loadSelectGeneral("ESTADO-COMPRA", $fEstado, 1)?>
                </select>
                <?php
			} else {
				?>
                <input type="checkbox" <?=$cEstado?> onclick="chkFiltro(this.checked, 'fEstado');" />
                <select name="fEstado" id="fEstado" style="width:140px;" <?=$dEstado?>>
					<option value="">&nbsp;</option>
                    <?=loadSelectGeneral("ESTADO-COMPRA", $fEstado, 0)?>
                </select>
                <?php
			}
			?>
		</td>
	</tr>
    <tr>
		<td align="right">Proveedor: </td>
		<td class="gallery clearfix">
            <input type="checkbox" <?=$cCodProveedor?> onclick="chkFiltroLista_3(this.checked, 'fCodProveedor', 'fNomProveedor', '', 'btProveedor');" />
            
            <input type="text" name="fCodProveedor" id="fCodProveedor" style="width:50px;" class="disabled" value="<?=$fCodProveedor?>" readonly="readonly" />
			<input type="text" name="fNomProveedor" id="fNomProveedor" style="width:235px;" class="disabled" value="<?=$fNomProveedor?>" readonly="readonly" />
            <a href="../lib/listas/listado_personas.php?filtrar=default&cod=fCodProveedor&nom=fNomProveedor&iframe=true&width=950&height=525" rel="prettyPhoto[iframe1]" id="btProveedor" style=" <?=$dCodProveedor?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
		<td align="right">F.Aprobaci&oacute;n: </td>
		<td>
			<input type="checkbox" <?=$cFechaAprobacion?> onclick="chkFiltro_2(this.checked, 'fFechaAprobaciond', 'fFechaAprobacionh');" />
			<input type="text" name="fFechaAprobaciond" id="fFechaAprobaciond" value="<?=$fFechaAprobaciond?>" <?=$dFechaAprobacion?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" />-
            <input type="text" name="fFechaAprobacionh" id="fFechaAprobacionh" value="<?=$fFechaAprobacionh?>" <?=$dFechaAprobacion?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" />
        </td>
	</tr>
    <tr>
		<td align="right">Buscar:</td>
		<td>
			<input type="checkbox" <?=$cBuscar?> onclick="chkFiltro(this.checked, 'fBuscar');" />
			<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:295px;" <?=$dBuscar?> />
		</td>
		<td align="right">F.Preparaci&oacute;n: </td>
		<td>
			<input type="checkbox" <?=$cFechaPreparacion?> onclick="chkFiltro_2(this.checked, 'fFechaPreparaciond', 'fFechaPreparacionh');" />
			<input type="text" name="fFechaPreparaciond" id="fFechaPreparaciond" value="<?=$fFechaPreparaciond?>" <?=$dFechaPreparacion?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" />-
            <input type="text" name="fFechaPreparacionh" id="fFechaPreparacionh" value="<?=$fFechaPreparacionh?>" <?=$dFechaPreparacion?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" />
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
            	<a href="#" onclick="mostrarTab('tab', '1', 2)">Compras</a>
            </li>
            <li id="li2" class="<?=$current_tab[1]?>" onclick="currentTab('tab', this); $('#id_tab').val('1');">
            	<a href="#" onclick="mostrarTab('tab', '2', 2);">Servicios</a>
            </li>
            </ul>
            </div>
        </td>
    </tr>
</table>

<div id="tab1" style=" <?=$display_tab[0]?>;">
<center>
<table width="<?=$_width?>" class="tblBotones">
    <tr>
        <td><div id="rows"></div></td>
        <td align="right">
            <input type="button" id="btImprimir" value="Imprimir" style="width:80px;" onclick="abrirReporteVal('a_reporte', 'lg_cotizaciones_cuadro_comparativo_pdf', '', '', $('#sel_registros'), 0, this.form)" />
        </td>
    </tr>
</table>
<div style="overflow:scroll; width:<?=$_width?>px; height:250px;">
<table width="100%" class="tblLista">
    <thead>
    <tr>
		<th scope="col" width="30">A&ntilde;o</th>
		<th scope="col" width="75" onclick="order('NroInterno')">Nro. Orden</th>
		<th scope="col" width="75">Fecha Orden</th>
		<th scope="col" width="400" align="left">Proveedor</th>
		<th scope="col" width="100" align="right">Monto</th>
		<th scope="col" width="100">Estado</th>
	</tr>
    </thead>
    
    <tbody id="lista_registros">
    <?php
	//	consulto lista
	$sql = "SELECT
				oc.Anio,
				oc.CodOrganismo,
				oc.NroOrden,
				oc.NroInterno,
				oc.FechaOrden,
				oc.CodProveedor,
				oc.NomProveedor,
				oc.MontoTotal,
				oc.Estado
			FROM
				lg_ordencompra oc
				INNER JOIN lg_requerimientosdet rd ON (rd.Anio = oc.Anio AND rd.NroOrden = oc.NroOrden)
				INNER JOIN lg_requerimientos r ON (r.CodRequerimiento = rd.CodRequerimiento)
			WHERE
				r.Clasificacion <> 'SER'
				$filtro
			GROUP BY Anio, CodOrganismo, NroOrden
			ORDER BY Anio, NroInterno";
    $query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
    while ($field = mysql_fetch_array($query)) {
		$id = "$field[Anio]_$field[CodOrganismo]_$field[NroOrden]_OC";
		if (strlen($field['Observaciones']) > 150) $Observaciones = substr($field['Observaciones'], 0, 150)."...";
		else $Observaciones = $field['Observaciones'];
        ?>
        <tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
			<td align="center"><?=$field['Anio']?></td>
			<td align="center"><?=$field['NroInterno']?></td>
			<td align="center"><?=formatFechaDMA($field['FechaOrden'])?></td>
			<td><?=htmlentities($field['NomProveedor'])?></td>
			<td align="right"><strong><?=number_format($field['MontoTotal'], 2, ',', '.')?></strong></td>
			<td align="center"><?=printValoresGeneral("ESTADO-COMPRA", $field['Estado'])?></td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>
</div>
</center>
</div>

<div id="tab2" style=" <?=$display_tab[1]?>;">
<center>
<table width="<?=$_width?>" class="tblBotones">
    <tr>
        <td><div id="rows"></div></td>
        <td align="right">
            <input type="button" id="btImprimir" value="Imprimir" style="width:80px;" onclick="abrirReporteVal('a_reporte', 'lg_cotizaciones_cuadro_comparativo_pdf', '', '', $('#sel_registros'), 0, this.form)" />
        </td>
    </tr>
</table>
<div style="overflow:scroll; width:<?=$_width?>px; height:250px;">
<table width="100%" class="tblLista">
    <thead>
    <tr>
		<th scope="col" width="30">A&ntilde;o</th>
		<th scope="col" width="75" onclick="order('NroInterno')">Nro. Orden</th>
		<th scope="col" width="75">Fecha Orden</th>
		<th scope="col" width="400" align="left">Proveedor</th>
		<th scope="col" width="100" align="right">Monto</th>
		<th scope="col" width="100">Estado</th>
	</tr>
    </thead>
    
    <tbody id="lista_registros">
    <?php
	//	consulto lista
	$sql = "SELECT
				oc.Anio,
				oc.CodOrganismo,
				oc.NroOrden,
				oc.NroInterno,
				oc.FechaDocumento AS FechaOrden,
				oc.CodProveedor,
				oc.NomProveedor,
				oc.TotalMontoIva AS MontoTotal,
				oc.Estado
			FROM
				lg_ordenservicio oc
				INNER JOIN lg_requerimientosdet rd ON (rd.Anio = oc.Anio AND rd.NroOrden = oc.NroOrden)
				INNER JOIN lg_requerimientos r ON (r.CodRequerimiento = rd.CodRequerimiento)
			WHERE
				r.Clasificacion = 'SER'
				$filtro
			GROUP BY Anio, CodOrganismo, NroOrden
			ORDER BY Anio, NroInterno";
    $query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
    while ($field = mysql_fetch_array($query)) {
		$id = "$field[Anio]_$field[CodOrganismo]_$field[NroOrden]_OS";
		if (strlen($field['Observaciones']) > 150) $Observaciones = substr($field['Observaciones'], 0, 150)."...";
		else $Observaciones = $field['Observaciones'];
        ?>
        <tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
			<td align="center"><?=$field['Anio']?></td>
			<td align="center"><?=$field['NroInterno']?></td>
			<td align="center"><?=formatFechaDMA($field['FechaOrden'])?></td>
			<td><?=htmlentities($field['NomProveedor'])?></td>
			<td align="right"><strong><?=number_format($field['MontoTotal'], 2, ',', '.')?></strong></td>
			<td align="center"><?=printValoresGeneral("ESTADO-COMPRA", $field['Estado'])?></td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>
</div>
</center>
</div>


</form>

<div class="gallery clearfix">
    <a href="pagina.php?iframe=true" rel="prettyPhoto[iframe2]" style="display:none;" id="a_reporte"></a>
</div>