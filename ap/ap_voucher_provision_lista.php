<?php
list($AnioActual, $MesActual, $DiaActual) = split("[/.-]", substr($Ahora, 0, 10));
$FechaActual = "$AnioActual-$MesActual-$DiaActual";
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "NroRegistro";
}
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (o.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fFechaRegistroD != "" || $fFechaRegistroH != "") {
	$cFechaRegistro = "checked";
	if ($fFechaRegistroD != "") $filtro.=" AND (o.FechaRegistro >= '".formatFechaAMD($fFechaRegistroD)."')";
	if ($fFechaRegistroH != "") $filtro.=" AND (o.FechaRegistro <= '".formatFechaAMD($fFechaRegistroH)."')";
} else $dFechaRegistro = "disabled";
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (o.CodTipoDocumento LIKE '%".$fBuscar."%' OR
					  o.NroDocumento LIKE '%".$fBuscar."%' OR
					  o.NroRegistro LIKE '%".$fBuscar."%' OR
					  o.MontoObligacion LIKE '%".setNumero($fBuscar)."%' OR
					  o.FechaRegistro LIKE '%".formatFechaAMD($fBuscar)."%' OR
					  CONCAT(o.CodTipoDocumento, '-', o.NroDocumento) LIKE '%".$fBuscar."%' OR
					  p1.NomCompleto LIKE '%".$fBuscar."%' OR
					  p2.NomCompleto LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
//	------------------------------------
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo">Generar Voucher de Provisi&oacute;n</td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=ap_voucher_provision_lista" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="sel_registros" id="sel_registros" />
<div class="divBorder" style="width:1050px;">
<table width="1050" class="tblFiltro">
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
	<tr>
		<td align="right">Proveedor: </td>
		<td class="gallery clearfix">
            <input type="checkbox" <?=$cCodProveedor?> onclick="chkFiltroLista_3(this.checked, 'fCodProveedor', 'fNomProveedor', '', 'btProveedor');" />
            
            <input type="hidden" name="fCodProveedor" id="fCodProveedor" value="<?=$fCodProveedor?>" />
			<input type="text" name="fNomProveedor" id="fNomProveedor" style="width:270px;" class="disabled" value="<?=$fNomProveedor?>" readonly />
            <a href="../lib/listas/listado_personas.php?filtrar=default&cod=fCodProveedor&nom=fNomProveedor&iframe=true&width=950&height=525" rel="prettyPhoto[iframe1]" id="btProveedor" style=" <?=$dCodProveedor?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
		<td align="right">Fecha Documento: </td>
		<td>
			<input type="checkbox" <?=$cFechaRegistro?> onclick="chkCampos(this.checked, 'fFechaRegistroD', 'fFechaRegistroH');" />
			<input type="text" name="fFechaRegistroD" id="fFechaRegistroD" value="<?=$fFechaRegistroD?>" <?=$dFechaRegistro?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" /> -
            <input type="text" name="fFechaRegistroH" id="fFechaRegistroH" value="<?=$fFechaRegistroH?>" <?=$dFechaRegistro?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" />
        </td>
	</tr>
</table>
</div>
<center><input type="submit" value="Buscar"></center><br />

<center>
<table width="1050" class="tblBotones">
	<tr>
		<td><div id="rows"></div></td>
		<td align="right" class="gallery clearfix">
        	<a id="aVoucher" href="pagina.php?iframe=true" rel="prettyPhoto[iframe2]" style="display:none;"></a>
			<input type="button" id="btGenerar" value="Generar" style="width:75px;" onclick="cargarOpcion2(this.form, '', 'FUNCTION', '', $('#sel_registros').val(), 'vouchers_abrir($(\'#sel_registros\').val(), \'ap_generar_vouchers_provision_voucher\');');" />
            
			<input type="button" id="btVer" value="Ver" style="width:75px;" onclick="cargarOpcion2(this.form, 'gehen.php?anz=ap_obligacion_form&opcion=ver&origen=ap_voucher_provision_lista', 'SELF', '', $('#sel_registros').val());" />
		</td>
	</tr>
</table>

<div style="overflow:scroll; width:1050px; height:300px;">
<table width="1100" class="tblLista">
	<thead>
    <tr>
		<th scope="col" width="55" onclick="order('NroRegistro')">Registro</th>
		<th scope="col" onclick="order('NomProveedor')">Proveedor</th>
		<th scope="col" colspan="2" onclick="order('Documento')">Documento</th>
		<th scope="col" width="70" onclick="order('FechaRegistro')">Fecha</th>
		<th scope="col" width="100" onclick="order('MontoObligacion')">Monto Obligaci&oacute;n</th>
		<th scope="col" width="300" onclick="order('NomPreparadoPor')">Preparado Por</th>
    </tr>
    </thead>
    
    <tbody id="lista_registros">
	<?php
	//	consulto todos
	$sql = "SELECT
				o.CodOrganismo,
				o.CodProveedor,
				o.CodTipoDocumento
			FROM
				ap_obligaciones o
				INNER JOIN mastpersonas p1 ON (o.CodProveedor = p1.CodPersona)
				INNER JOIN mastpersonas p2 ON (o.IngresadoPor = p2.CodPersona)
				INNER JOIN ap_tipodocumento td ON (o.CodTipoDocumento = td.CodTipoDocumento)
			WHERE
				(o.Estado = 'AP' OR o.Estado = 'PA') AND
				o.FlagContabilizacionPendiente = 'S' AND
				td.FlagProvision = 'S'
				$filtro";
	$query = mysql_query($sql) or die ($sql.mysql_error());
	$rows_total = mysql_num_rows($query);
	
	//	consulto lista
	$sql = "SELECT
				o.CodOrganismo,
				o.CodProveedor,
				o.CodTipoDocumento,
				o.NroDocumento,
				CONCAT(o.CodTipoDocumento, '-', o.NroDocumento) AS Documento,
				o.NroRegistro,
				o.FechaRegistro,
				o.MontoObligacion,
				p1.NomCompleto AS NomProveedor,
				p2.NomCompleto AS NomPreparadoPor
			FROM
				ap_obligaciones o
				INNER JOIN mastpersonas p1 ON (o.CodProveedor = p1.CodPersona)
				INNER JOIN mastpersonas p2 ON (o.IngresadoPor = p2.CodPersona)
				INNER JOIN ap_tipodocumento td ON (o.CodTipoDocumento = td.CodTipoDocumento)
			WHERE
				(o.Estado = 'AP' OR o.Estado = 'PA') AND
				o.FlagContabilizacionPendiente = 'S' AND
				td.FlagProvision = 'S'
				$filtro
			ORDER BY $fOrderBy
			LIMIT ".intval($limit).", ".intval($maxlimit);
	$query = mysql_query($sql) or die ($sql.mysql_error());
	$rows_lista = mysql_num_rows($query);
	while ($field = mysql_fetch_array($query)) {
		$id = "$field[CodOrganismo]"."_"."$field[CodProveedor]"."_"."$field[CodTipoDocumento]"."_"."$field[NroDocumento]";
		?>
		<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
			<td align="center"><?=$field['NroRegistro']?></td>
			<td><?=htmlentities($field['NomProveedor'])?></td>
			<td width="15" align="center"><?=$field['CodTipoDocumento']?></td>
			<td width="130"><?=$field['NroDocumento']?></td>
			<td align="center"><?=formatFechaDMA($field['FechaRegistro'])?></td>
			<td align="right"><strong><?=number_format($field['MontoObligacion'], 2, ',', '.')?></strong></td>
			<td><?=htmlentities($field['NomPreparadoPor'])?></td>
		</tr>
		<?php
	}
	?>
    </tbody>
</table>
</div>
<table width="1050">

	<tr>
    	<td>
        	Mostrar: 
            <select name="maxlimit" style="width:50px;" onchange="this.form.submit();">
                <?=loadSelectGeneral("MAXLIMIT", $maxlimit, 0)?>
            </select>
        </td>
        <td align="right">
        	<?=paginacion(intval($rows_total), intval($rows_lista), intval($maxlimit), intval($limit));?>
        </td>
    </tr>
</table>
</center>
</form>