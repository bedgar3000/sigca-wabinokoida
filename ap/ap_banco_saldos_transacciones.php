<?php
if ($filtrar == "default") {
	$fFechaTransaccion2D = '01-'.$MesActual.'-'.$AnioActual;
	$fFechaTransaccion2H = formatFechaDMA($FechaActual);
	$maxlimit2 = $_SESSION["MAXLIMIT"];
	$NroCuenta = $sel_registros;
}
if ($fCodTipoTransaccion2 != "") { $cCodTipoTransaccion = "checked"; $filtro.=" AND (bt.CodTipoTransaccion = '".$fCodTipoTransaccion2."')"; } else $dCodTipoTransaccion = "disabled";
if ($fFechaTransaccion2D != "" || $fFechaTransaccion2H != "") { 
	$cFechaTransaccion = "checked"; 
	$filtro.=" AND (bt.FechaTransaccion >= '".formatFechaAMD($fFechaTransaccion2D)."')"; 
	$filtro.=" AND (bt.FechaTransaccion <= '".formatFechaAMD($fFechaTransaccion2H)."')"; 
} else $dFechaTransaccion = "disabled";
//	------------------------------------
//	consulto cuenta
$sql = "SELECT 
			cb.NroCuenta,
			cb.Descripcion,
			cbb.SaldoActual
		FROM 
			ap_ctabancaria cb
			INNER JOIN ap_ctabancariabalance cbb ON (cbb.NroCuenta = cb.NroCuenta)
		WHERE cb.NroCuenta = '".$NroCuenta."'";
$field_cuenta = getRecord($sql);
//	------------------------------------
$_titulo = "Transacciones de Cuentas Bancarias";
$_width = 700;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="cargarPagina(document.getElementById('frmentrada'), 'gehen.php?anz=ap_banco_saldos_lista');">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=ap_banco_saldos_transacciones" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
<input type="hidden" name="fFechaTransaccionH" id="fFechaTransaccionH" value="<?=$fFechaTransaccionH?>" />

<!--FILTRO-->
<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
	<thead>
    	<th colspan="4">
        	Informaci&oacute;n de la Cuenta Bancaria
        </th>
    </thead>
	<tr>
		<td align="right" width="150">Cta. Bancaria:</td>
		<td>
			<input type="text" name="NroCuenta" id="NroCuenta" value="<?=$field_cuenta['NroCuenta']?>" style="width:80px;" readonly="readonly" />
			<input type="text" value="<?=$field_cuenta['Descripcion']?>" style="width:225px;" disabled="disabled" />
		</td>
		<td align="right" width="100">Saldo a la fecha: </td>
		<td>
			<input type="text" value="<?=formatFechaDMA($FechaActual)?>" style="width:60px;" disabled="disabled" />
			<input type="text" value="<?=number_format($field_cuenta['SaldoActual'],2,',','.')?>" style="width:100px; text-align:right; font-weight:bold;" disabled="disabled" />
        </td>
	</tr>
</table>
</div>
<hr />
<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
	<thead>
    	<th colspan="5">
        	Criterio de Selecci&oacute;n para Transaciones
        </th>
    </thead>
	<tr>
		<td align="right" width="150">Tipo de Transacci&oacute;n:</td>
		<td>
			<input type="checkbox" <?=$cCodTipoTransaccion?> onclick="chkCampos2(this.checked,['fCodTipoTransaccion2']);" />
			<select name="fCodTipoTransaccion2" id="fCodTipoTransaccion2" style="width:200px;" <?=$dCodTipoTransaccion?>>
                <option value="">&nbsp;</option>
				<?=loadSelect2('ap_bancotipotransaccion','CodTipoTransaccion','Descripcion',$fCodTipoTransaccion2)?>
			</select>
		</td>
		<td align="right">Fecha: </td>
		<td>
			<input type="checkbox" <?=$cFechaTransaccion?> onclick="chkCampos2(this.checked,['fFechaTransaccion2D','fFechaTransaccion2H']);" />
			<input type="text" name="fFechaTransaccion2D" id="fFechaTransaccion2D" value="<?=$fFechaTransaccion2D?>" <?=$dFechaTransaccion?> maxlength="10" style="width:60px;" class="datepicker" />
			<input type="text" name="fFechaTransaccion2H" id="fFechaTransaccion2H" value="<?=$fFechaTransaccion2H?>" <?=$dFechaTransaccion?> maxlength="10" style="width:60px;" class="datepicker" />
        </td>
        <td align="right"><input type="submit" value="Buscar"></td>
	</tr>
</table>
</div>
<div class="sep"></div>

<!--REGISTROS-->
<center>
<div style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px;">
<table class="tblLista" style="width:100%; min-width:2000px;">
	<thead>
    <tr>
        <th width="75" onclick="order('FechaTransaccion','frmentrada','fOrderBy2')">Fecha</th>
        <th width="60" onclick="order('NroTransaccion','frmentrada','fOrderBy2')">Transacci&oacute;n</th>
        <th width="200" onclick="order('TipoTransaccion','frmentrada','fOrderBy2')">Tipo</th>
        <th width="150" align="right" onclick="order('Abonos','frmentrada','fOrderBy2')">Abonos</th>
        <th width="150" align="right" onclick="order('Cargos','frmentrada','fOrderBy2')">Cargos</th>
        <th width="60" onclick="order('PeriodoContable','frmentrada','fOrderBy2')">Periodo</th>
        <th width="100" onclick="order('NroPago','frmentrada','fOrderBy2')">Nro. Cheque</th>
        <th width="150" align="right" onclick="order('Monto','frmentrada','fOrderBy2')">Monto</th>
        <th width="150" onclick="order('CodigoReferenciaBanco','frmentrada','fOrderBy2')">Referencia Banco</th>
        <th align="left" onclick="order('Comentarios','frmentrada','fOrderBy2')">Comentarios</th>
    </tr>
    </thead>
    
    <tbody id="lista_registros">
	<?php
	//	consulto todos
	$sql = "SELECT
				bt.FechaTransaccion,
				bt.NroTransaccion,
				bt.Monto,
				(CASE WHEN bt.Monto <= 0 THEN bt.Monto END) AS Abonos,
				(CASE WHEN bt.Monto > 0 THEN bt.Monto END) AS Cargos,
				bt.PeriodoContable,
				bt.NroPago,
				bt.CodigoReferenciaBanco,
				bt.Comentarios,
				btt.Descripcion AS TipoTransaccion
			FROM
				ap_bancotransaccion bt
				INNER JOIN ap_bancotipotransaccion btt ON (btt.CodTipoTransaccion = bt.CodTipoTransaccion)
			WHERE
				(bt.Estado = 'AP' OR
				 bt.Estado = 'CO')
				$filtro";
	$rows_total = getNumRows3($sql);
	//	consulto lista
	$sql = "SELECT
				bt.FechaTransaccion,
				bt.NroTransaccion,
				bt.Monto,
				(CASE WHEN bt.Monto > 0 THEN bt.Monto END) AS Abonos,
				(CASE WHEN bt.Monto <= 0 THEN bt.Monto END) AS Cargos,
				bt.PeriodoContable,
				bt.NroPago,
				bt.CodigoReferenciaBanco,
				bt.Comentarios,
				btt.Descripcion AS TipoTransaccion
			FROM
				ap_bancotransaccion bt
				INNER JOIN ap_bancotipotransaccion btt ON (btt.CodTipoTransaccion = bt.CodTipoTransaccion)
			WHERE
				(bt.Estado = 'AP' OR
				 bt.Estado = 'CO')
				$filtro
			ORDER BY $fOrderBy
			LIMIT ".intval($limit).", ".intval($maxlimit);
	$field = getRecords($sql);
	$rows_lista = count($field);
	foreach($field as $f) {
		$id = $f['NroCuenta'];
		?>
		<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
			<td align="center"><?=formatFechaDMA($f['FechaTransaccion'])?></td>
			<td align="center"><?=$f['NroTransaccion']?></td>
			<td><?=htmlentities($f['TipoTransaccion'])?></td>
			<td align="right"><strong><?=number_format($f['Abonos'],2,',','.')?></strong></td>
			<td align="right"><strong><?=number_format($f['Cargos'],2,',','.')?></strong></td>
			<td align="center"><?=$f['PeriodoContable']?></td>
			<td align="center"><?=$f['NroPago']?></td>
			<td align="right"><strong><?=number_format($f['Monto'],2,',','.')?></strong></td>
			<td align="center"><?=$f['CodigoReferenciaBanco']?></td>
			<td><?=htmlentities($f['Comentarios'])?></td>
		</tr>
		<?php
	}
	?>
    </tbody>
</table>
</div>

<table style="width:100%; min-width:<?=$_width?>px;">
	<tr>
    	<td>
        	Mostrar: 
            <select name="maxlimit2" style="width:50px;" onchange="this.form.submit();">
                <?=loadSelectGeneral("MAXLIMIT", $maxlimit2, 0)?>
            </select>
        </td>
        <td align="right">
        	<?=paginacion(intval($rows_total), intval($rows_lista), intval($maxlimit2), intval($limit));?>
        </td>
    </tr>
</table>
</center>
</form>