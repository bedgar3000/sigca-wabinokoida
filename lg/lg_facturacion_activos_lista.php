<?php
list($AnioActual, $MesActual, $DiaActual) = split("[/.-]", substr($Ahora, 0, 10));
$FechaActual = "$AnioActual-$MesActual-$DiaActual";
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "CodOrganismo, Anio, NroOrden, NroInterno, Secuencia, NroSecuencia";
}
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (af.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (af.Anio LIKE '%".$fBuscar."%' OR
					  af.NroInterno LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
//	------------------------------------
$_width = 1000;
$_sufijo = "facturacion_activos";
$_titulo = "Facturaci&oacute;n de Activos";
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
		<td align="right" width="135">Buscar:</td>
		<td>
			<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
			<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:195px;" <?=$dBuscar?> />
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
        <td align="right">
            <input type="button" id="btModificar" value="Facturar" style="width:75px;" onclick="cargarOpcion2(this.form, 'gehen.php?anz=lg_<?=$_sufijo?>_form', 'SELF', '', $('#sel_registros').val());" />
        </td>
    </tr>
</table>

<div style="overflow:scroll; width:<?=$_width?>px; height:300px;">
<table width="1700" class="tblLista">
	<thead>
    <tr>
        <th scope="col" width="15">#</th>
        <th scope="col" width="75">Commodity</th>
        <th scope="col" align="left">Descripci&oacute;n</th>
        <th scope="col" width="100">Nro. Serie</th>
        <th scope="col" width="75">Fecha Ingreso</th>
        <th scope="col" width="250" align="left">Modelo</th>
        <th scope="col" width="150">Cod. Barra</th>
        <th scope="col" width="45">Ubic.</th>
        <th scope="col" width="45">C.C.</th>
        <th scope="col" width="100">Nro. Placa</th>
        <th scope="col" width="150">Marca</th>
        <th scope="col" width="75">Color</th>
    </tr>
    </thead>
    
    <tbody id="lista_registros">
	<?php
	//	consulto lista
	$sql = "SELECT
				af.*,
				oc.NomProveedor,
				m.Descripcion AS NomMarca,
				md.Descripcion AS NomColor
			FROM
				lg_activofijo af
				INNER JOIN lg_ordencompra oc ON (oc.CodOrganismo = af.CodOrganismo AND
												 oc.Anio = af.Anio AND
												 oc.NroOrden = af.NroOrden)
				
				LEFT JOIN lg_marcas m ON (m.CodMarca = af.CodMarca)
				LEFT JOIN mastmiscelaneosdet md ON (md.CodDetalle = af.Color AND
													md.CodMaestro = 'COLOR')
			WHERE
				af.Estado = 'PE' AND
				af.FlagFacturado = 'N'
				$filtro
			ORDER BY $fOrderBy";
	$query = mysql_query($sql) or die ($sql.mysql_error());
	$rows_lista = mysql_num_rows($query);
	while ($field = mysql_fetch_array($query)) {
		$id = $field['CodOrganismo']."_".$field['Anio']."_".$field['NroOrden']."_".$field['Secuencia']."_".$field['NroSecuencia'];
		$Agrupador1 = $field['CodOrganismo'].$field['Anio'].$field['NroOrden'];
		if ($Grupo1 != $Agrupador1) {			
			$Grupo1 = $Agrupador1;
			$i = 0;
			?>
			<tr class="trListaBody3">
            	<td></td>
				<td colspan="9">
					O/C: <?=$field['Anio']?>-<?=$field['NroOrden']?> &nbsp; &nbsp; &nbsp; &nbsp;
					<?=htmlentities($field['NomProveedor'])?>
                </td>
			</tr>
			<?php
		}
		?>
		<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
			<th align="center"><?=++$i?></th>
			<td align="center"><?=$field['CommoditySub']?></td>
			<td><?=htmlentities($field['Descripcion'])?></td>
			<td align="center"><?=$field['NroSerie']?></td>
			<td align="center"><?=formatFechaAMD($field['FechaIngreso'])?></td>
			<td><?=htmlentities($field['Modelo'])?></td>
			<td align="center"><?=$field['CodBarra']?></td>
			<td align="center"><?=$field['CodUbicacion']?></td>
			<td align="center"><?=$field['CodCentroCosto']?></td>
			<td align="center"><?=$field['NroPlaca']?></td>
			<td align="center"><?=htmlentities($field['NomMarca'])?></td>
			<td align="center"><?=htmlentities($field['NomColor'])?></td>
		</tr>
		<?php
	}
	?>
    </tbody>
</table>
</div>
</center>
</form>