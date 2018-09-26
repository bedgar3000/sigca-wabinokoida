<?php
list($AnioActual, $MesActual, $DiaActual) = split("[/.-]", substr($Ahora, 0, 10));
$FechaActual = "$AnioActual-$MesActual-$DiaActual";
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "NroTransaccion";
}
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (bt.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fFechaTransaccionD != "" || $fFechaTransaccionH != "") {
	$cFechaTransaccion = "checked";
	if ($fFechaTransaccionD != "") $filtro.=" AND (bt.FechaTransaccion >= '".formatFechaAMD($fFechaTransaccionD)."')";
	if ($fFechaTransaccionH != "") $filtro.=" AND (bt.FechaTransaccion <= '".formatFechaAMD($fFechaTransaccionH)."')";
} else $dFechaTransaccion = "disabled";
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (bt.NroTransaccion LIKE '%".$fBuscar."%' OR
					  bt.FechaTransaccion LIKE '%".formatFechaAMD($fBuscar)."%' OR	
					  bt.Comentarios LIKE '%".$fBuscar."%' OR					  
					  bt.CodigoReferenciaBanco LIKE '%".$fBuscar."%'
					  bt.CodigoReferenciaInterno LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
//	------------------------------------
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo">Generar Voucher de Transacciones Bancarias (Pub. 20)</td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=ap_voucher_bancariapub20_lista" method="post" autocomplete="off">
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
		<td align="right">Fecha Transacci&oacute;n: </td>
		<td>
			<input type="checkbox" <?=$cFechaTransaccion?> onclick="chkCampos(this.checked, 'fFechaTransaccionD', 'fFechaTransaccionH');" />
			<input type="text" name="fFechaTransaccionD" id="fFechaTransaccionD" value="<?=$fFechaTransaccionD?>" <?=$dFechaTransaccion?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" /> -
            <input type="text" name="fFechaTransaccionH" id="fFechaTransaccionH" value="<?=$fFechaTransaccionH?>" <?=$dFechaTransaccion?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" />
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
			<input type="button" id="btGenerar" value="Generar" style="width:75px;" onclick="cargarOpcion2(this.form, '', 'FUNCTION', '', $('#sel_registros').val(), 'vouchers_abrir($(\'#sel_registros\').val(), \'ap_generar_transacciones_bancarias_voucher_pub20\');');" />
            
			<input type="button" id="btVer" value="Ver" style="width:75px;" onclick="cargarOpcion2(this.form, 'gehen.php?anz=ap_obligacion_form&opcion=ver&origen=ap_voucher_provision_lista', 'SELF', '', $('#sel_registros').val());" />
		</td>
	</tr>
</table>

<div style="overflow:scroll; width:1050px; height:300px;">
<table width="1100" class="tblLista">
	<thead>
    <tr>
		<th scope="col" width="55" onclick="order('NroTransaccion')">N&uacute;mero</th>
		<th scope="col" width="75" onclick="order('FechaTransaccion')">Fecha</th>
		<th scope="col" onclick="order('Comentarios')">Comentario</th>
		<th scope="col" width="75" onclick="order('CodigoReferenciaBanco')">Doc. Referencia Banco</th>
		<th scope="col" width="75" onclick="order('CodigoReferenciaInterno')">Nro. Documento</th>
    </tr>
    </thead>
    
    <tbody id="lista_registros">
	<?php
	//	consulto todos
	$sql = "SELECT bt.NroTransaccion
			FROM
				ap_bancotransaccion bt
				INNER JOIN ap_bancotipotransaccion btt ON (bt.CodTipoTransaccion = btt.CodTipoTransaccion)
			WHERE
				(bt.Estado = 'AP' OR bt.Estado = 'CO') AND
				bt.FlagGeneraVoucher = 'S' AND
				bt.FlagContPendientePub20 = 'S'
				$filtro
			GROUP BY NroTransaccion";
	$query = mysql_query($sql) or die ($sql.mysql_error());
	$rows_total = mysql_num_rows($query);
	
	//	consulto lista
	$sql = "SELECT
				bt.NroTransaccion,
				bt.FechaTransaccion,
				bt.Comentarios,
				bt.CodigoReferenciaBanco,
				bt.CodigoReferenciaInterno,
				bt.FechaTransaccion,
				btt.CodVoucher
			FROM
				ap_bancotransaccion bt
				INNER JOIN ap_bancotipotransaccion btt ON (bt.CodTipoTransaccion = btt.CodTipoTransaccion)
			WHERE
				(bt.Estado = 'AP' OR bt.Estado = 'CO') AND
				bt.FlagGeneraVoucher = 'S' AND
				bt.FlagContPendientePub20 = 'S'
				$filtro
			GROUP BY NroTransaccion
			ORDER BY $fOrderBy
			LIMIT ".intval($limit).", ".intval($maxlimit);
	$query = mysql_query($sql) or die ($sql.mysql_error());
	$rows_lista = mysql_num_rows($query);
	while ($field = mysql_fetch_array($query)) {
		$id = "$field[NroTransaccion]"."_"."$field[Secuencia]"."_"."$field[CodVoucher]";
		?>
		<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
			<td align="center"><?=$field['NroTransaccion']?></td>
			<td align="center"><?=formatFechaDMA($field['FechaTransaccion'])?></td>
			<td><?=htmlentities($field['Comentarios'])?></td>
			<td><?=$field['CodigoReferenciaBanco']?></td>
			<td><?=$field['CodigoReferenciaInterno']?></td>
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