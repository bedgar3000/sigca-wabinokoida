<?php
list($AnioActual, $MesActual, $DiaActual) = split("[/.-]", substr($Ahora, 0, 10));
$FechaActual = "$AnioActual-$MesActual-$DiaActual";
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "NroPago";
}
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (p.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fFechaPagoD != "" || $fFechaPagoH != "") {
	$cFechaPago = "checked";
	if ($fFechaPagoD != "") $filtro.=" AND (p.FechaPago >= '".formatFechaAMD($fFechaPagoD)."')";
	if ($fFechaPagoH != "") $filtro.=" AND (p.FechaPago <= '".formatFechaAMD($fFechaPagoH)."')";
} else $dFechaPago = "disabled";
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (p.NroCuenta LIKE '%".$fBuscar."%' OR
					  p.NroPago LIKE '%".$fBuscar."%' OR
					  p.MontoPago LIKE '%".setNumero($fBuscar)."%' OR
					  p.FechaPago LIKE '%".formatFechaAMD($fBuscar)."%' OR
					  mp.NomCompleto LIKE '%".$fBuscar."%' OR
					  tp.TipoPago LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
//	------------------------------------
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo">Generar Voucher de Pagos</td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=ap_voucher_pagospub20_lista" method="post" autocomplete="off">
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
		<td align="right">Fecha Pago: </td>
		<td>
			<input type="checkbox" <?=$cFechaPago?> onclick="chkCampos(this.checked, 'fFechaPagoD', 'fFechaPagoH');" />
			<input type="text" name="fFechaPagoD" id="fFechaPagoD" value="<?=$fFechaPagoD?>" <?=$dFechaPago?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" /> -
            <input type="text" name="fFechaPagoH" id="fFechaPagoH" value="<?=$fFechaPagoH?>" <?=$dFechaPago?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" />
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
			<input type="button" id="btGenerar" value="Generar" style="width:75px;" onclick="cargarOpcion2(this.form, '', 'FUNCTION', '', $('#sel_registros').val(), 'vouchers_abrir($(\'#sel_registros\').val(), \'ap_generar_vouchers_pagos_voucher_pub20\');');" />
            
			<input type="button" id="btVer" value="Ver" style="width:75px;" onclick="cargarOpcion2(this.form, 'gehen.php?anz=ap_pago_form&opcion=ver&origen=ap_voucher_pagospub20_lista', 'SELF', '', $('#sel_registros').val());" />
		</td>
	</tr>
</table>

<div style="overflow:scroll; width:1050px; height:300px;">
<table width="1100" class="tblLista">
	<thead>
    <tr>
        <th scope="col" width="125" onclick="order('NroCuenta')">Cta. Bancaria</th>
		<th scope="col" width="125" onclick="order('NroPago')">Cheque</th>
		<th scope="col" onclick="order('NomProveedor')">Proveedor</th>
		<th scope="col" width="125" onclick="order('MontoPago')">Monto</th>
		<th scope="col" width="75" onclick="order('FechaPago')">Fecha de Pago</th>
		<th scope="col" width="125" onclick="order('TipoPago')">Tipo de Pago</th>
    </tr>
    </thead>
    
    <tbody id="lista_registros">
	<?php
	//	consulto todos
	$sql = "SELECT
				p.NroProceso,
				p.Secuencia
			FROM
				ap_pagos p
				INNER JOIN mastpersonas mp ON (p.CodProveedor = mp.CodPersona)
				INNER JOIN masttipopago tp ON (p.CodTipoPago = tp.CodTipoPago)
			WHERE
				p.Estado = 'IM' AND
				p.FlagContPendientePub20 = 'S'
				$filtro";
	$query = mysql_query($sql) or die ($sql.mysql_error());
	$rows_total = mysql_num_rows($query);
	
	//	consulto lista
	$sql = "SELECT
				p.NroProceso,
				p.Secuencia,
				p.CodTipoPago,
				p.NroCuenta,
				p.NroPago,
				p.MontoPago,
				p.FechaPago,				
				mp.NomCompleto AS NomProveedor,
				tp.TipoPago
			FROM
				ap_pagos p
				INNER JOIN mastpersonas mp ON (p.CodProveedor = mp.CodPersona)
				INNER JOIN masttipopago tp ON (p.CodTipoPago = tp.CodTipoPago)
			WHERE
				p.Estado = 'IM' AND
				p.FlagContPendientePub20 = 'S'
				$filtro
			ORDER BY $fOrderBy
			LIMIT ".intval($limit).", ".intval($maxlimit);
	$query = mysql_query($sql) or die ($sql.mysql_error());
	$rows_lista = mysql_num_rows($query);
	while ($field = mysql_fetch_array($query)) {
		$id = "$field[NroProceso]"."_"."$field[Secuencia]";
		?>
		<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
			<td align="center"><?=$field['NroCuenta']?></td>
			<td align="center"><?=$field['NroPago']?></td>
			<td><?=htmlentities($field['NomProveedor'])?></td>
			<td align="right"><strong><?=number_format($field['MontoPago'], 2, ',', '.')?></strong></td>
			<td align="center"><?=formatFechaDMA($field['FechaPago'])?></td>
			<td><?=htmlentities($field['TipoPago'])?></td>
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