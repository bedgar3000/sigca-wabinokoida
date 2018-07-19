<?php
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$fFechaTransaccionH = formatFechaDMA($FechaActual);
	$fOrderBy = "NroCuenta";
	$maxlimit = $_SESSION["MAXLIMIT"];
}
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (cb.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fFechaTransaccionH != "") { $cFechaTransaccion = "checked"; $filtro.=" AND (bt.FechaTransaccion <= '".formatFechaAMD($fFechaTransaccionH)."')"; } else $dFechaTransaccion = "disabled";
//	------------------------------------
$_titulo = "Saldo de Cuentas Bancarias";
$_width = 700;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=ap_banco_saldos_lista" method="post" autocomplete="off">
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
			<input type="checkbox" <?=$cCodOrganismo?> onclick="chkCampos2(this.checked,['fCodOrganismo']);" />
			<select name="fCodOrganismo" id="fCodOrganismo" style="width:300px;" <?=$dCodOrganismo?>>
                <option value="">&nbsp;</option>
				<?=getOrganismos($fCodOrganismo, 3)?>
			</select>
		</td>
		<td align="right">Hasta la fecha: </td>
		<td>
			<input type="checkbox" <?=$cFechaTransaccion?> onclick="chkCampos2(this.checked,['fFechaTransaccionH']);" />
			<input type="text" name="fFechaTransaccionH" id="fFechaTransaccionH" value="<?=$fFechaTransaccionH?>" <?=$dFechaTransaccion?> maxlength="10" style="width:60px;" class="datepicker" />
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
        <td align="right">
            <input type="button" value="Ver" style="width:75px;" class="ver" onclick="cargarOpcion2(this.form, 'gehen.php?anz=ap_banco_saldos_transacciones&filtrar=default', 'SELF', '', $('#sel_registros').val());" />
        </td>
    </tr>
</table>

<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px;">
<table class="tblLista" style="width:100%;">
	<thead>
    <tr>
        <th align="left" onclick="order('Banco')">Banco</th>
        <th width="100" onclick="order('NroCuenta')">Nro. Cuenta</th>
        <th align="left" onclick="order('Descripcion')">Descripci&oacute;n</th>
        <th width="150" align="right" onclick="order('Monto')">Monto</th>
    </tr>
    </thead>
    
    <tbody id="lista_registros">
	<?php
	//	consulto todos
	$sql = "SELECT
				cb.NroCuenta,
				cb.Descripcion,
				b.Banco,
				SUM(Monto) AS Monto
			FROM
				ap_bancotransaccion bt
				INNER JOIN ap_ctabancaria cb ON (cb.NroCuenta = bt.NroCuenta)
				INNER JOIN mastbancos b ON (b.CodBanco = cb.CodBanco)
			WHERE
				(bt.Estado = 'AP' OR
				 bt.Estado = 'CO')
				$filtro
			GROUP BY NroCuenta";
	$rows_total = getNumRows3($sql);
	//	consulto lista
	$sql = "SELECT
				cb.NroCuenta,
				cb.Descripcion,
				b.Banco,
				SUM(Monto) AS Monto
			FROM
				ap_bancotransaccion bt
				INNER JOIN ap_ctabancaria cb ON (cb.NroCuenta = bt.NroCuenta)
				INNER JOIN mastbancos b ON (b.CodBanco = cb.CodBanco)
			WHERE
				(bt.Estado = 'AP' OR
				 bt.Estado = 'CO')
				$filtro
			GROUP BY NroCuenta
			ORDER BY $fOrderBy
			LIMIT ".intval($limit).", ".intval($maxlimit);
	$field = getRecords($sql);
	$rows_lista = count($field);
	foreach($field as $f) {
		$id = $f['NroCuenta'];
		?>
		<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
			<td><?=htmlentities($f['Banco'])?></td>
			<td align="center"><?=$f['NroCuenta']?></td>
			<td><?=htmlentities($f['Descripcion'])?></td>
			<td align="right"><?=number_format($f['Monto'],2,',','.')?></td>
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