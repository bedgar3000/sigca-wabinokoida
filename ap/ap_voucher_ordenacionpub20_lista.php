<?php
list($AnioActual, $MesActual, $DiaActual) = split("[/.-]", substr($Ahora, 0, 10));
$FechaActual = "$AnioActual-$MesActual-$DiaActual";
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "NroRegistro";
}
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (op.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fCodProveedor != "") { $cCodProveedor = "checked"; $filtro.=" AND (op.CodProveedor = '".$fCodProveedor."')"; } else $dCodProveedor = "visibility:hidden;";
if ($fFechaOrdenPagoD != "" || $fFechaOrdenPagoH != "") {
	$cFechaOrdenPago = "checked";
	if ($fFechaOrdenPagoD != "") $filtro.=" AND (op.FechaOrdenPago >= '".formatFechaAMD($fFechaOrdenPagoD)."')";
	if ($fFechaOrdenPagoH != "") $filtro.=" AND (op.FechaOrdenPago <= '".formatFechaAMD($fFechaOrdenPagoH)."')";
} else $dFechaOrdenPago = "disabled";
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (op.CodTipoDocumento LIKE '%".$fBuscar."%' OR
					  op.NroDocumento LIKE '%".$fBuscar."%' OR
					  op.NroOrden LIKE '%".$fBuscar."%' OR
					  op.Anio LIKE '%".$fBuscar."%' OR
					  op.MontoTotal LIKE '%".setNumero($fBuscar)."%' OR
					  op.FechaOrdenPago LIKE '%".formatFechaAMD($fBuscar)."%' OR
					  CONCAT(op.CodTipoDocumento, '-', op.NroDocumento) LIKE '%".$fBuscar."%' OR
					  p1.NomCompleto LIKE '%".$fBuscar."%' OR
					  p2.NomCompleto LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
//	------------------------------------
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo">Generar Voucher de Orden de Pago</td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=ap_voucher_ordenacionpub20_lista" method="post" autocomplete="off">
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
			<input type="checkbox" <?=$cFechaOrdenPago?> onclick="chkCampos(this.checked, 'fFechaOrdenPagoD', 'fFechaOrdenPagoH');" />
			<input type="text" name="fFechaOrdenPagoD" id="fFechaOrdenPagoD" value="<?=$fFechaOrdenPagoD?>" <?=$dFechaOrdenPago?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" /> -
            <input type="text" name="fFechaOrdenPagoH" id="fFechaOrdenPagoH" value="<?=$fFechaOrdenPagoH?>" <?=$dFechaOrdenPago?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" />
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
			<input type="button" id="btGenerar" value="Generar" style="width:75px;" onclick="cargarOpcion2(this.form, '', 'FUNCTION', '', $('#sel_registros').val(), 'vouchers_abrir($(\'#sel_registros\').val(), \'ap_generar_vouchers_ordenacion\');');" />
            
			<input type="button" id="btVer" value="Ver" style="width:75px;" onclick="cargarOpcion2(this.form, 'gehen.php?anz=ap_obligacion_form&opcion=ver&origen=ap_voucher_ordenacion_lista', 'SELF', '', $('#sel_registros').val());" />
		</td>
	</tr>
</table>

<div style="overflow:scroll; width:1050px; height:300px;">
<table width="1300" class="tblLista">
	<thead>
    <tr>
		<th scope="col" width="35" onclick="order('Anio')">A&Ntilde;o</th>
		<th scope="col" width="75" onclick="order('NroOrden')">Nro. Orden</th>
		<th scope="col" onclick="order('NomProveedor')">Proveedor</th>
		<th scope="col" colspan="2" onclick="order('Documento')">Documento</th>
		<th scope="col" width="70" onclick="order('FechaOrdenPago')">Fecha</th>
		<th scope="col" width="100" onclick="order('MontoTotal')">Monto Total</th>
		<th scope="col" width="300" onclick="order('NomAprobadoPor')">Aprobado Por</th>
    </tr>
    </thead>
    
    <tbody id="lista_registros">
	<?php
	//	consulto todos
	$sql = "SELECT
				op.CodProveedor,
				op.CodTipoDocumento,
				op.NroDocumento
			FROM
				ap_ordenpago op
				INNER JOIN mastpersonas p1 ON (op.CodProveedor = p1.CodPersona)
				INNER JOIN mastpersonas p2 ON (op.AprobadoPor = p2.CodPersona)
				INNER JOIN ap_tipodocumento td ON (op.CodTipoDocumento = td.CodTipoDocumento)
			WHERE
				(op.Estado = 'PE' OR op.Estado = 'PA' OR op.Estado = 'GE') AND
				op.FlagContPendienteOrdPub20 = 'S' AND
				td.FlagProvision = 'S'
				$filtro";
	$query = mysql_query($sql) or die ($sql.mysql_error());
	$rows_total = mysql_num_rows($query);
	
	//	consulto lista
	$sql = "SELECT
				op.Anio,
				op.CodOrganismo,
				op.NroOrden,
				op.CodProveedor,
				op.CodTipoDocumento,
				op.NroDocumento,
				CONCAT(op.CodTipoDocumento, '-', op.NroDocumento) AS Documento,
				op.FechaOrdenPago,
				op.MontoTotal,
				p1.NomCompleto AS NomProveedor,
				p2.NomCompleto AS NomAprobadoPor
			FROM
				ap_ordenpago op
				INNER JOIN mastpersonas p1 ON (op.CodProveedor = p1.CodPersona)
				INNER JOIN mastpersonas p2 ON (op.AprobadoPor = p2.CodPersona)
				INNER JOIN ap_tipodocumento td ON (op.CodTipoDocumento = td.CodTipoDocumento)
			WHERE
				(op.Estado = 'PE' OR op.Estado = 'PA' OR op.Estado = 'GE') AND
				op.FlagContPendienteOrdPub20 = 'S' AND
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
			<td align="center"><?=$field['Anio']?></td>
			<td align="center"><?=$field['NroOrden']?></td>
			<td><?=htmlentities($field['NomProveedor'])?></td>
			<td width="15" align="center"><?=$field['CodTipoDocumento']?></td>
			<td width="130"><?=$field['NroDocumento']?></td>
			<td align="center"><?=formatFechaDMA($field['FechaOrdenPago'])?></td>
			<td align="right"><strong><?=number_format($field['MontoTotal'], 2, ',', '.')?></strong></td>
			<td><?=htmlentities($field['NomAprobadoPor'])?></td>
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