<?php
list($Anio, $Mes, $Dia) = split("[/.-]", substr($Ahora, 0, 10));
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$maxlimit = $_SESSION["MAXLIMIT"];
	##	obtengo el banco, la cuenta y el periodo
	$sql = "SELECT
				b.CodBanco,
				cb.NroCuenta,
				cb.PeriodoConciliacion
			FROM
				mastbancos b
				INNER JOIN ap_ctabancaria cb ON (b.CodBanco = cb.CodBanco)
			ORDER BY CodBanco, NroCuenta
			LIMIT 0, 1";
	$query_banco = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query_banco) != 0) $field_banco = mysql_fetch_array($query_banco);
	$fCodBanco = $field_banco['CodBanco'];
	$fNroCuenta = $field_banco['NroCuenta'];
	list($AnioInicial, $MesInicial, $DiaInicial) = split("[/.-]", $field_banco['PeriodoConciliacion']);
	$fFechaSaldoInicial = "01-$MesInicial-$AnioInicial";
	$fFechaConciliacion = "$Dia-$Mes-$Anio";
}
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (bt.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fCodBanco != "") {
	$cCodBanco = "checked";
	if ($fNroCuenta != "") $filtro .= " AND (bt.NroCuenta = '".$fNroCuenta."')";
} else $dCodBanco = "disabled";
if ($fFlagConciliacion != "") { $cFlagConciliacion = "checked"; $filtro.=" AND (bt.FlagConciliacion = '".$fFlagConciliacion."')"; } else $dFlagConciliacion = "disabled";
//	------------------------------------
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo">Conciliaci&oacute;n Bancaria</td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=ap_conciliacion_bancaria" method="post">
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="sel_registros" id="sel_registros" />
<div class="divBorder" style="width:1050px;">
<table width="1050" class="tblFiltro">
	<tr>
		<td align="right" width="125">Organismo:</td>
		<td>
			<input type="checkbox" <?=$cCodOrganismo?> onclick="this.checked=!this.checked" />
			<select name="fCodOrganismo" id="fCodOrganismo" style="width:300px;" <?=$dCodOrganismo?>>
				<?=getOrganismos($fCodOrganismo, 3)?>
			</select>
		</td>
		<td align="right" width="125">F.Saldo Inicial: </td>
		<td>
			<input type="checkbox" checked onclick="this.checked=!this.checked" />
			<input type="text" name="fFechaSaldoInicial" id="fFechaSaldoInicial" value="<?=$fFechaSaldoInicial?>" <?=$dFechaSaldoInicial?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" />
        </td>
	</tr>
    <tr>
		<td align="right">Banco:</td>
		<td>
			<input type="checkbox" <?=$cCodBanco?> onclick="this.checked=!this.checked" />
			<select name="fCodBanco" id="fCodBanco" style="width:300px;" onChange="getOptionsSelect(this.value, 'cuentas_bancarias', 'fNroCuenta', true)" <?=$dCodBanco?>>
                <?=loadSelect("mastbancos", "CodBanco", "Banco", $fCodBanco, 0)?>
			</select>
		</td>
		<td align="right">Fecha Conciliaci&oacute;n: </td>
		<td>
			<input type="checkbox" checked onclick="this.checked=!this.checked" />
			<input type="text" name="fFechaConciliacion" id="fFechaConciliacion" value="<?=$fFechaConciliacion?>" <?=$dFechaConciliacion?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" />
        </td>
    </tr>
    <tr>
		<td align="right">Cta. Bancaria:</td>
		<td>
			<input type="checkbox" style="visibility:hidden;" />
			<select name="fNroCuenta" id="fNroCuenta" style="width:300px;" onchange="getPeriodoConciliacion(this.value);" <?=$dCodBanco?>>
            	<?=loadSelect2("ap_ctabancaria", "NroCuenta", "NroCuenta", $fNroCuenta, 0, array('CodBanco'), array($fCodBanco))?>
			</select>
		</td>
		<td align="right">Conciliados:</td>
		<td>
			<input type="checkbox" <?=$cFlagConciliacion?> onclick="chkFiltro(this.checked, 'fFlagConciliacion');" />
			<select name="fFlagConciliacion" id="fFlagConciliacion" style="width:45px;" <?=$dFlagConciliacion?>>
            	<option value="">&nbsp;</option>
                <?=loadSelectGeneral("FLAG", $fFlagConciliacion, 0)?>
			</select>
		</td>
    </tr>
</table>
</div>
<center><input type="submit" value="Buscar"></center><br />

<center>
<table width="1050" class="tblBotones">
	<tr>
        <td>
        	<a class="link" href="#" onclick="selTodos('registros', 'Transaccion');">Todos</a> |
            <a class="link" href="#" onclick="selNinguno('registros', 'Transaccion');">Ninguno</a>
        </td>
		<td align="right">
			<input type="button" id="btVer" value="Ver" style="width:75px; <?=$btVer?>" onclick="cargarOpcionMultiple2(this.form, 'gehen.php?anz=ap_transacciones_bancarias_form&opcion=ver&origen=ap_conciliacion_bancaria', 'SELF', '', 'Transaccion', 'sel_registros', 0)" />
            
            <input type="button" id="btActualizar" value="Actualizar" style="width:75px;" onclick="opcionRegistroMultiple(this.form, 'Transaccion', 'conciliacion-bancaria', 'actualizar', true);" />
		</td>
	</tr>
</table>

<div style="overflow:scroll; width:1050px; height:300px;">
<table width="1800" class="tblLista">
	<thead>
    <tr>
		<th scope="col" width="60">Nro.</th>
		<th scope="col" width="25">#</th>
		<th scope="col" width="200">Transacci&oacute;n</th>
		<th scope="col" width="125">Cargo</th>
		<th scope="col" width="125">Abono</th>
		<th scope="col" width="35">Conc</th>
		<th scope="col" width="80">Nro. Pago</th>
		<th scope="col" width="100">Doc. Referencia Banco</th>
		<th scope="col" width="75">Fecha Banco</th>
		<th scope="col">Comentario</th>
		<th scope="col" width="75">Estado</th>
	</tr>
    </thead>
    
    <tbody id="lista_registros">
	<?php
	//	consulto todos
	$sql = "SELECT
				bt.NroTransaccion,
				bt.Secuencia
			FROM
				ap_bancotransaccion bt
				INNER JOIN ap_bancotipotransaccion btt ON (btt.CodTipoTransaccion = bt.CodTipoTransaccion)
			WHERE
				bt.FechaTransaccion <= '".formatFechaAMD($fFechaConciliacion)."' AND
				(bt.FechaTransaccion >= '".formatFechaAMD($fFechaSaldoInicial)."' OR
				 bt.FechaConciliacion >= '".formatFechaAMD($fFechaSaldoInicial)."' OR
				 bt.FlagConciliacion = 'N')
				$filtro";
	$query = mysql_query($sql) or die ($sql.mysql_error());
	$rows_total = mysql_num_rows($query);
	
	//	consulto lista
	$sql = "SELECT
				bt.NroTransaccion,
				bt.Secuencia,
				bt.Monto,
				bt.FlagConciliacion,
				bt.NroPago,
				bt.CodigoReferenciaBanco,
				bt.FechaTransaccion,
				bt.Comentarios,
				bt.Estado,
				btt.Descripcion AS NomTipoTransaccion
			FROM
				ap_bancotransaccion bt
				INNER JOIN ap_bancotipotransaccion btt ON (btt.CodTipoTransaccion = bt.CodTipoTransaccion)
			WHERE
				bt.FechaTransaccion <= '".formatFechaAMD($fFechaConciliacion)."' AND
				(bt.FechaTransaccion >= '".formatFechaAMD($fFechaSaldoInicial)."' OR
				 bt.FechaConciliacion >= '".formatFechaAMD($fFechaSaldoInicial)."' OR
				 bt.FlagConciliacion = 'N')
				$filtro
			ORDER BY NroTransaccion, Secuencia
			LIMIT ".intval($limit).", ".intval($maxlimit);
	$query = mysql_query($sql) or die ($sql.mysql_error());
	$rows_lista = mysql_num_rows($query);
	while ($field = mysql_fetch_array($query)) {
		$id = "$field[NroTransaccion].$field[Secuencia]";
		if (strlen($field['Comentarios']) > 100) $Comentarios = substr($field['Comentarios'], 0, 100)."...";
		else $Comentarios = $field['Comentarios'];
		if ($field['Monto'] < 0) {
			$Cargo = $field['Monto'];
			$Abono = 0.00;
		} else {
			$Cargo = 0.00;
			$Abono = $field['Monto'];
		}
		?>
		<tr class="trListaBody" onclick="mClkMulti(this);" id="row_<?=$id?>">
			<td align="center">
            	<input type="checkbox" name="Transaccion" id="<?=$id?>" value="<?=$id?>" style="display:none;" />
				<?=$field['NroTransaccion']?>
            </td>
			<td align="center"><?=$field['Secuencia']?></td>
			<td><?=htmlentities($field['NomTipoTransaccion'])?></td>
			<td align="right"><?=number_format($Cargo, 2, ',', '.')?></td>
			<td align="right"><?=number_format($Abono, 2, ',', '.')?></td>
			<td align="center"><?=printFlag($field['FlagConciliacion'])?></td>
			<td align="center"><?=$field['NroPago']?></td>
			<td><?=$field['CodigoReferenciaBanco']?></td>
			<td align="center"><?=formatFechaDMA($field['FechaTransaccion'])?></td>
			<td title="<?=htmlentities($field['Comentarios'])?>"><?=$Comentarios?></td>
			<td align="center"><?=printValores("ESTADO-BANCARIO", $field['Estado'])?></td>
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
		<td><div id="rows"></div></td>
        <td align="right">
        	<?=paginacion(intval($rows_total), intval($rows_lista), intval($maxlimit), intval($limit));?>
        </td>
    </tr>
</table>
</center>
</form>