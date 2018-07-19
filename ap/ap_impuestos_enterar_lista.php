<?php
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$fEstado = 'PA';
	$fTipoComprobante = 'IVA';
	$fPeriodoFiscalD = $PeriodoActual;
	$fPeriodoFiscalH = $PeriodoActual;
	$FechaEnterado = formatFechaDMA($FechaActual);
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "Comprobante";
}
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (r.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fCodImpuesto != "") { $cCodImpuesto = "checked"; $filtro.=" AND (r.CodImpuesto = '".$fCodImpuesto."')"; } else $dCodImpuesto = "disabled";
if ($fCodProveedor != "") { $cCodProveedor = "checked"; $filtro.=" AND (r.CodProveedor = '".$fCodProveedor."')"; } else $dCodProveedor = "visibility:hidden;";
if ($fFechaComprobanteD != "" || $fFechaComprobanteH != "") { 
	$cFechaComprobante = "checked"; 
	if ($fFechaComprobanteD != "") $filtro.=" AND (r.FechaComprobante >= '".formatFechaAMD($fFechaComprobanteD)."')"; 
	if ($fFechaComprobanteH != "") $filtro.=" AND (r.FechaComprobante <= '".formatFechaAMD($fFechaComprobanteH)."')"; 
} else $dFechaComprobante = "disabled";
if ($fFechaFacturaD != "" || $fFechaFacturaH != "") { 
	$cFechaFactura = "checked"; 
	if ($fFechaFacturaD != "") $filtro.=" AND (r.FechaFactura >= '".formatFechaAMD($fFechaFacturaD)."')"; 
	if ($fFechaFacturaH != "") $filtro.=" AND (r.FechaFactura <= '".formatFechaAMD($fFechaFacturaH)."')"; 
} else $dFechaFactura = "disabled";
if ($fPeriodoFiscalD != "" || $fPeriodoFiscalH != "") { 
	$cPeriodoFiscal = "checked"; 
	if ($fPeriodoFiscalD != "") $filtro.=" AND (r.PeriodoFiscal >= '".$fPeriodoFiscalD."')"; 
	if ($fPeriodoFiscalH != "") $filtro.=" AND (r.PeriodoFiscal <= '".$fPeriodoFiscalH."')"; 
} else $dPeriodoFiscal = "disabled";
if ($fBuscar != "") { 
	$cBuscar = "checked"; 
	$filtro.=" AND (r.PeriodoFiscal LIKE '%".$fBuscar."%' 
					OR r.NroComprobante LIKE '%".$fBuscar."%' 
					OR r.NroDocumento LIKE '%".$fBuscar."%' 
					OR r.NroControl LIKE '%".$fBuscar."%' 
					OR r.PagoNroProceso LIKE '%".$fBuscar."%' 
					OR CONCAT(SUBSTRING(r.PeriodoFiscal, 1, 4), SUBSTRING(r.PeriodoFiscal, 6, 2), r.NroComprobante) LIKE '%".$fBuscar."%' 
					OR CONCAT(r.AnioOrden, '-', r.NroOrden) LIKE '%".$fBuscar."%' 
			)"; 
} else $dBuscar = "disabled";
if ($fTipoComprobante != "") { $cTipoComprobante = "checked"; $filtro.=" AND (r.TipoComprobante = '".$fTipoComprobante."')"; } else $dTipoComprobante = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (r.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
//	------------------------------------
$_titulo = "Enterar Impuestos";
$_width = 860;
if ($fEstado == 'PA') {
	$bEnterar = '';
	$bImprimir = 'disabled';
}
elseif ($fEstado == 'EN') {
	$bEnterar = 'disabled';
	$bImprimir = '';
}
else {
	$bEnterar = 'disabled';
	$bImprimir = 'disabled';
}
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=ap_impuestos_enterar_lista" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />

<!--FILTRO-->
<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
	<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
		<tr>
			<td align="right" width="100">Organismo:</td>
			<td>
				<input type="checkbox" <?=$cCodOrganismo?> onclick="this.checked=!this.checked" />
				<select name="fCodOrganismo" id="fCodOrganismo" style="width:260px;" <?=$dCodOrganismo?>>
					<?=getOrganismos($fCodOrganismo, 3)?>
				</select>
			</td>
			<td align="right" width="125">Fecha Comprobante: </td>
			<td>
				<input type="checkbox" <?=$cFechaComprobante?> onclick="chkCampos2(this.checked, ['fFechaComprobanteD','fFechaComprobanteH']);" />
				<input type="text" name="fFechaComprobanteD" id="fFechaComprobanteD" value="<?=$fFechaComprobanteD?>" <?=$dFechaComprobante?> maxlength="10" style="width:60px;" class="datepicker" <?=$dFechaComprobante?> /> -
	            <input type="text" name="fFechaComprobanteH" id="fFechaComprobanteH" value="<?=$fFechaComprobanteH?>" <?=$dFechaComprobante?> maxlength="10" style="width:60px;" class="datepicker" <?=$dFechaComprobante?> />
	        </td>
			<td align="right" width="100">Buscar: </td>
			<td>
				<input type="checkbox" <?=$cBuscar?> onclick="chkCampos2(this.checked, ['fBuscar']);" />
				<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" <?=$dBuscar?> style="width:125px;" <?=$dBuscar?> />
	        </td>
	        <td>&nbsp;</td>
		</tr>
		<tr>
			<td align="right">Impuesto: </td>
			<td>
	            <input type="checkbox" <?=$cCodImpuesto?> onclick="chkCampos2(this.checked, ['fCodImpuesto']);" />
	            <select name="fCodImpuesto" id="fCodImpuesto" style="width:260px;" <?=$dCodImpuesto?>>
	                <option value="">&nbsp;</option>
	                <?=loadSelect2("mastimpuestos","CodImpuesto","Descripcion",$fCodImpuesto)?>
	            </select>
	        </td>
			<td align="right">Fecha Factura: </td>
			<td>
				<input type="checkbox" <?=$cFechaFactura?> onclick="chkCampos2(this.checked, ['fFechaFacturaD','fFechaFacturaH']);" />
				<input type="text" name="fFechaFacturaD" id="fFechaFacturaD" value="<?=$fFechaFacturaD?>" <?=$dFechaFactura?> maxlength="10" style="width:60px;" class="datepicker" <?=$dFechaFactura?> /> -
	            <input type="text" name="fFechaFacturaH" id="fFechaFacturaH" value="<?=$fFechaFacturaH?>" <?=$dFechaFactura?> maxlength="10" style="width:60px;" class="datepicker" <?=$dFechaFactura?> />
	        </td>
			<td align="right">Comprobante: </td>
			<td>
	            <input type="checkbox" <?=$cTipoComprobante?> onclick="this.checked=!this.checked;" />
	            <select name="fTipoComprobante" id="fTipoComprobante" style="width:133px;" <?=$dTipoComprobante?>>
	                <?=loadSelectValores("IMPUESTO-COMPROBANTE", $fTipoComprobante, 0)?>
	            </select>
	        </td>
	        <td>&nbsp;</td>
		</tr>
		<tr>
			<td class="tagForm">Proveedor:</td>
			<td class="gallery clearfix">
				<input type="checkbox" id="cCodProveedor" <?=$cCodProveedor?> onclick="ckLista(this.checked, ['fCodProveedor','fProveedor'], ['bCodProveedor']);" />
	        	<input type="text" name="fCodProveedor" id="fCodProveedor" value="<?=$fCodProveedor?>" style="width:40px;" readonly />
	        	<input type="text" name="fProveedor" id="fProveedor" value="<?=$fProveedor?>" style="width:205px;" readonly />
	            <a href="../lib/listas/gehen.php?anz=lista_personas&filtrar=default&campo1=fCodProveedor&campo2=fProveedor&ventana=selLista&iframe=true&width=950&height=430" rel="prettyPhoto[iframe1]" id="bCodProveedor" style=" <?=$dCodProveedor?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
			<td align="right">Periodo Fiscal: </td>
			<td>
				<input type="checkbox" <?=$cPeriodoFiscal?> onclick="chkCampos2(this.checked, ['fPeriodoFiscalD','fPeriodoFiscalH']);" />
				<input type="text" name="fPeriodoFiscalD" id="fPeriodoFiscalD" value="<?=$fPeriodoFiscalD?>" <?=$dPeriodoFiscal?> maxlength="7" style="width:60px;" class="periodo" <?=$dPeriodoFiscal?> /> -
	            <input type="text" name="fPeriodoFiscalH" id="fPeriodoFiscalH" value="<?=$fPeriodoFiscalH?>" <?=$dPeriodoFiscal?> maxlength="7" style="width:60px;" class="periodo" <?=$dPeriodoFiscal?> />
	        </td>
			<td align="right">Estado: </td>
			<td>
	            <input type="checkbox" <?=$cEstado?> onclick="this.checked=!this.checked;" />
	            <select name="fEstado" id="fEstado" style="width:133px;" <?=$dEstado?> onchange="setEstado(this.value);">
	                <?=loadSelectValores("estado-retencion", $fEstado, 0)?>
	            </select>
	        </td>
	        <td align="right"><input type="submit" value="Buscar"></td>
		</tr>
	</table>
</div>
<div class="sep"></div>

<!--REGISTROS-->
<center>
<input type="hidden" name="sel_registros" id="sel_registros" />
<table class="tblBotones" style="width:100%; min-width:<?=$_width?>px;">
    <tr>
        <td><div id="rows"></div></td>
        <td align="center">
        	Fecha: 
        	<input type="text" name="FechaEnterado" id="FechaEnterado" value="<?=$FechaEnterado?>" maxlength="10" style="width:60px;" class="datepicker" />
        	<input type="button" id="bEnterar" value="Enterar" style="width:75px;" class="update" onclick="validar();" <?=$bEnterar?> />
        </td>
        <td align="right" class="gallery clearfix">
        	<a href="pagina.php?iframe=true" rel="prettyPhoto[iframe2]" style="display:none;" id="aimprimir"></a>
        	<input type="button" id="bImprimir" value="Imprimir" style="width:75px;" onclick="abrirReporte2($('#frmentrada'), $('#aimprimir'), 'ap_impuestos_enterar_pdf.php');" <?=$bImprimir?> />
        </td>
    </tr>
</table>

<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:250px;">
	<table class="tblLista" style="width:100%; min-width:2000px;">
		<thead>
	    <tr>
	    	<th width="20" onclick="selTodos2('registros');">#</th>
	        <th width="100" onclick="order('Comprobante')">Comprobante</th>
	        <th align="left" onclick="order('Proveedor')">Raz&oacute;n Social</th>
	        <th width="60" onclick="order('PeriodoFiscal')">Periodo Fiscal</th>
	        <th width="75" onclick="order('FechaComprobante')">Fecha Comprobante</th>
	        <th width="110" onclick="order('OrdenPago')">O/P</th>
	        <th width="75" onclick="order('PagoNroProceso,PagoSecuencia')">Nro. Pago</th>
	        <th width="150" onclick="order('NroControl')">Nro. Control</th>
	        <th width="150" onclick="order('NroFactura')">Nro. Factura</th>
	        <th width="75" onclick="order('FechaFactura')">Fecha Factura</th>
	        <th width="75" align="right" onclick="order('MontoAfecto')">Monto Imponible</th>
	        <th width="75" align="right" onclick="order('MontoNoAfecto')">Monto Exento</th>
	        <th width="75" align="right" onclick="order('MontoImpuesto')">Monto Impuesto</th>
	        <th width="100" align="right" onclick="order('MontoFactura')">Monto Factura</th>
	        <th width="35" align="right" onclick="order('FactorPorcentaje')">%</th>
	        <th width="75" align="right" onclick="order('MontoRetenido')">Monto Retenido</th>
	    </tr>
	    </thead>
	    
	    <tbody id="lista_registros">
		<?php
		//	consulto todos
		$sql = "SELECT
					r.Anio,
					r.TipoComprobante,
					r.NroComprobante
				FROM
					ap_retenciones r
					INNER JOIN mastpersonas p ON (p.CodPersona = r.CodProveedor)
				WHERE 1 $filtro";
		$rows_total = getNumRows3($sql);
		//	consulto lista
		$sql = "SELECT
					r.Anio,
					r.TipoComprobante,
					r.NroComprobante,
					CONCAT(SUBSTRING(r.PeriodoFiscal, 1, 4), SUBSTRING(r.PeriodoFiscal, 6, 2), r.NroComprobante) AS Comprobante,
					p.NomCompleto AS Proveedor,
					r.PeriodoFiscal,
					r.FechaComprobante,
					r.AnioOrden,
					r.NroOrden,
					CONCAT(r.AnioOrden, '-', r.NroOrden) AS OrdenPago,
					r.PagoNroProceso,
					r.PagoSecuencia,
					r.NroDocumento,
					r.NroControl,
					r.NroFactura,
					r.FechaFactura,
					r.MontoAfecto,
					r.MontoNoAfecto,
					r.MontoImpuesto,
					r.MontoFactura,
					r.Porcentaje,
					ABS(r.MontoRetenido) AS MontoRetenido,
					r.Estado
				FROM
					ap_retenciones r
					INNER JOIN mastpersonas p ON (p.CodPersona = r.CodProveedor)
				WHERE 1 $filtro
				ORDER BY $fOrderBy
				LIMIT ".intval($limit).", ".intval($maxlimit);
		$field = getRecords($sql);
		$rows_lista = count($field);
		$i = 0;
		$MontoAfecto = 0;
		$MontoNoAfecto = 0;
		$MontoImpuesto = 0;
		$MontoFactura = 0;
		$MontoRetenido = 0;
		foreach($field as $f) {
			$id = $f['Anio'].'_'.$f['TipoComprobante'].'_'.$f['NroComprobante'].'_'.$f['Estado'].'_'.$f['Comprobante'];
			?>
			<tr class="trListaBody" onclick="clkMulti($(this), '<?=$id?>');">
	        	<th>
	            	<input type="checkbox" name="registros[]" id="<?=$id?>" value="<?=$id?>" style="display:none;" />
	                <?=++$i?>
	            </th>
				<td align="center"><?=$f['Comprobante']?></td>
				<td><?=htmlentities($f['Proveedor'])?></td>
				<td align="center"><?=$f['PeriodoFiscal']?></td>
				<td align="center"><?=formatFechaDMA($f['FechaComprobante'])?></td>
				<td align="center"><?=$f['OrdenPago']?></td>
				<td align="center"><?=$f['PagoNroProceso']?></td>
				<td align="center"><?=$f['NroControl']?></td>
				<td align="center"><?=$f['NroFactura']?></td>
				<td align="center"><?=formatFechaDMA($f['FechaFactura'])?></td>
				<td align="right"><?=number_format($f['MontoAfecto'],2,',','.')?></td>
				<td align="right"><?=number_format($f['MontoNoAfecto'],2,',','.')?></td>
				<td align="right"><?=number_format($f['MontoImpuesto'],2,',','.')?></td>
				<td align="right"><?=number_format($f['MontoFactura'],2,',','.')?></td>
				<td align="right"><strong><?=number_format($f['Porcentaje'],2,',','.')?></strong></td>
				<td align="right"><strong><?=number_format($f['MontoRetenido'],2,',','.')?></strong></td>
			</tr>
			<?php
			$MontoAfecto += $f['MontoAfecto'];
			$MontoNoAfecto += $f['MontoNoAfecto'];
			$MontoImpuesto += $f['MontoImpuesto'];
			$MontoFactura += $f['MontoFactura'];
			$MontoRetenido += $f['MontoRetenido'];
		}
		?>
	    </tbody>
	</table>
</div>

<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
	<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
		<tr>
	        <td>&nbsp;</td>
			<td align="right" width="100">Total Imponible: </td>
			<td width="100"><input type="text" value="<?=number_format($MontoAfecto,2,',','.')?>" style="width:100px; text-align:right; font-weight:bold;" disabled /></td>
			<td align="right" width="100">Total Exento: </td>
			<td width="100"><input type="text" value="<?=number_format($MontoNoAfecto,2,',','.')?>" style="width:100px; text-align:right; font-weight:bold;" disabled /></td>
			<td align="right" width="100">Total Impuesto: </td>
			<td width="100"><input type="text" value="<?=number_format($MontoImpuesto,2,',','.')?>" style="width:100px; text-align:right; font-weight:bold;" disabled /></td>
			<td align="right" width="100">Total Factura: </td>
			<td width="100"><input type="text" value="<?=number_format($MontoFactura,2,',','.')?>" style="width:100px; text-align:right; font-weight:bold;" disabled /></td>
			<td align="right" width="100">Total Retenido: </td>
			<td width="100"><input type="text" value="<?=number_format($MontoRetenido,2,',','.')?>" style="width:100px; text-align:right; font-weight:bold;" disabled /></td>
		</tr>
	</table>
</div>

<table style="width:100%; min-width:<?=$_width?>px;">
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

<script type="text/javascript" language="javascript">
function validar() {
	if ($('#FechaEnterado').val().trim() == '') cajaModal('Debe ingresar la Fecha','error');
	else if (!valFecha($('#FechaEnterado').val())) cajaModal('Formato de Fecha incorrecta','error');
	else opcionRegistroMultiple2(document.getElementById('frmentrada'), 'registros', 'formulario', 'enterar', 'ap_impuestos_enterar_ajax.php', 1);
}
function setEstado(Estado) {
	if (Estado == 'PA') {
		$('#bEnterar').prop('disabled',false);
		$('#bImprimir').prop('disabled',true);
	}
	else if (Estado == 'EN') {
		$('#bEnterar').prop('disabled',true);
		$('#bImprimir').prop('disabled',false);
	}
	else {
		$('#bEnterar').prop('disabled',true);
		$('#bImprimir').prop('disabled',true);
	}
}
</script>