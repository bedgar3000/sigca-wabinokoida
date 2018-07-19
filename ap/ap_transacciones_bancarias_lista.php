<?php
if ($lista == "todos") {
	$titulo = "Transacciones Bancarias";
	$btActualizar = "display:none;";
	$btDesactualizar = "display:none;";
}
elseif ($lista == "actualizar") {
	$titulo = "Actualizar Transacciones Bancarias";
	$btNuevo = "display:none;";
	$btModificar = "display:none;";
}
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$fFechaTransaccionD = "01-$MesActual-$AnioActual";
	$fFechaTransaccionH = formatFechaDMA($FechaActual);
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "NroTransaccion";
	$fEstado = "PR";
}
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (bt.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fCodTipoTransaccion != "") { $cCodTipoTransaccion = "checked"; $filtro.=" AND (bt.CodTipoTransaccion = '".$fCodTipoTransaccion."')"; } else $dCodTipoTransaccion = "disabled";
if ($fCodTipoDocumento != "") { $cCodTipoDocumento = "checked"; $filtro.=" AND (bt.CodTipoDocumento = '".$fCodTipoDocumento."')"; } else $dCodTipoDocumento = "disabled";
if ($fCodBanco != "") { $cCodBanco = "checked"; $filtro.=" AND (b.CodBanco = '".$fCodBanco."')"; } else $dCodBanco = "disabled";
if ($fNroCuenta != "") $filtro.=" AND (bt.NroCuenta = '".$fNroCuenta."')";
if ($fFechaTransaccionD != "" || $fFechaTransaccionH != "") {
	$cFechaTransaccion = "checked";
	if ($fFechaTransaccionD != "") $filtro.=" AND (bt.FechaTransaccion >= '".formatFechaAMD($fFechaTransaccionD)."')";
	if ($fFechaTransaccionH != "") $filtro.=" AND (bt.FechaTransaccion <= '".formatFechaAMD($fFechaTransaccionH)."')";
} else $dFechaTransaccion = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (bt.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (bt.NroTransaccion LIKE '%".$fBuscar."%' OR
					  bt.Secuencia LIKE '%".$fBuscar."%' OR
					  bt.FechaTransaccion LIKE '%".formatFechaAMD($fBuscar)."%' OR
					  bt.Descripcion LIKE '%".$fBuscar."%' OR
					  bt.Monto LIKE '%".number_format($fBuscar, 2, ',', '.')."%' OR
					  bt.NroCuenta LIKE '%".$fBuscar."%' OR
					  bt.PeriodoContable LIKE '%".$fBuscar."%' OR
					  bt.Voucher LIKE '%".$fBuscar."%' OR
					  bt.CodigoReferenciaInterno LIKE '%".$fBuscar."%' OR
					  bt.CodigoReferenciaBanco LIKE '%".$fBuscar."%' OR
					  bt.NroPago LIKE '%".$fBuscar."%' OR
					  bt.Comentarios LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
//	------------------------------------
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=ap_transacciones_bancarias_lista" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="sel_registros" id="sel_registros" />
<div class="divBorder" style="width:1050px;">
<table width="1050" class="tblFiltro">
	<tr>
		<td align="right" width="135">Organismo:</td>
		<td>
			<input type="checkbox" <?=$cCodOrganismo?> onclick="this.checked=!this.checked" />
			<select name="fCodOrganismo" id="fCodOrganismo" style="width:300px;" <?=$dCodOrganismo?>>
				<?=getOrganismos($fCodOrganismo, 3)?>
			</select>
		</td>
		<td align="right">Fecha: </td>
		<td>
			<input type="checkbox" <?=$cFechaTransaccion?> onclick="chkCampos(this.checked, 'fFechaTransaccionD', 'fFechaTransaccionH');" />
			<input type="text" name="fFechaTransaccionD" id="fFechaTransaccionD" value="<?=$fFechaTransaccionD?>" <?=$dFechaTransaccion?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" /> -
            <input type="text" name="fFechaTransaccionH" id="fFechaTransaccionH" value="<?=$fFechaTransaccionH?>" <?=$dFechaTransaccion?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" />
        </td>
	</tr>
	<tr>
		<td align="right">Tipo de Transacci&oacute;n:</td>
		<td>
			<input type="checkbox" <?=$cCodTipoTransaccion?> onclick="chkCampos(this.checked, 'fCodTipoTransaccion');" />
			<select name="fCodTipoTransaccion" id="fCodTipoTransaccion" style="width:300px;" <?=$dCodTipoTransaccion?>>
            	<option value="">&nbsp;</option>
                <?=loadSelect("ap_bancotipotransaccion", "CodTipoTransaccion", "Descripcion", $fCodTipoTransaccion, 0)?>
			</select>
		</td>
		<td align="right">Buscar:</td>
		<td>
			<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
			<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:195px;" <?=$dBuscar?> />
		</td>
	</tr>
	<tr>
		<td align="right">Tipo Documento:</td>
		<td>
			<input type="checkbox" <?=$cCodTipoDocumento?> onclick="chkCampos(this.checked, 'fCodTipoDocumento');" />
			<select name="fCodTipoDocumento" id="fCodTipoDocumento" style="width:300px;" <?=$dCodTipoDocumento?>>
            	<option value="">&nbsp;</option>
                <?=loadSelect("ap_tipodocumento", "CodTipoDocumento", "Descripcion", $fCodTipoDocumento, 0)?>
			</select>
		</td>
		<td align="right">Banco:</td>
		<td>
			<input type="checkbox" <?=$cCodBanco?> onclick="chkCampos(this.checked, 'fCodBanco', 'fNroCuenta');" />
			<select name="fCodBanco" id="fCodBanco" style="width:200px;" onchange="getOptionsSelect(this.value, 'cuentas_bancarias', 'fNroCuenta', 1);" <?=$dCodBanco?>>
            	<option value="">&nbsp;</option>
                <?=loadSelect("mastbancos", "CodBanco", "Banco", $fCodBanco, 0)?>
			</select>
		</td>
	</tr>
	<tr>
		<td align="right">Estado:</td>
		<td>
            <input type="checkbox" <?=$cEstado?> onclick="chkCampos(this.checked, 'fEstado');" />
            <select name="fEstado" id="fEstado" style="width:105px;" <?=$dEstado?>>
            	<option value="">&nbsp;</option>
                <?=loadSelectValores("ESTADO-TRANSACCION-BANCARIA", $fEstado, 0)?>
            </select>
		</td>
		<td align="right">Cta. Bancaria:</td>
		<td>
			<input type="checkbox" style="visibility:hidden;" />
			<select name="fNroCuenta" id="fNroCuenta" style="width:200px;" <?=$dCodBanco?>>
            	<option value="">&nbsp;</option>
                <?=loadSelectDependiente("ap_ctabancaria", "NroCuenta", "NroCuenta", "CodBanco", $fNroCuenta, $fCodBanco, 0)?>
			</select>
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
			<input type="button" value="Nuevo" style="width:75px; <?=$btNuevo?>" class="insert" onclick="cargarPagina(this.form, 'gehen.php?anz=ap_transacciones_bancarias_form&opcion=nuevo');" />
			<input type="button" value="Modificar" style="width:75px; <?=$btModificar?>" class="update" onclick="cargarOpcionValidar2(this.form, $('#sel_registros').val(), 'accion=transacciones_bancarias_modificar', 'gehen.php?anz=ap_transacciones_bancarias_form&opcion=modificar&origen=ap_transacciones_bancarias_lista', 'SELF', '');" />
			<input type="button" value="Ver" style="width:75px; <?=$btVer?>" class="ver" onclick="cargarOpcion2(this.form, 'gehen.php?anz=ap_transacciones_bancarias_form&opcion=ver&origen=ap_transacciones_bancarias_lista', 'SELF', '', 'sel_registros');" />
			<input type="button" value="Eliminar" style="width:75px; <?=$btEliminar?>" class="delete" onclick="opcionRegistro2(this.form, $('#sel_registros').val(), 'transacciones_bancarias', 'eliminar');" /> | 
            <a href="pagina.php?iframe=true" rel="prettyPhoto[iframe1]" style="display:none;" id="aImprimir"></a>
            <input type="button" value="Imprimir" style="width:75px; <?=$btImprimir?>" class="ver" onclick="abrirReporteVal('aImprimir', 'ap_transacciones_bancarias_pdf', '100%', '100%', $('#sel_registros'));" />
			<input type="button" value="Actualizar" style="width:85px; <?=$btActualizar?>" class="update" onclick="cargarOpcionValidar2(this.form, $('#sel_registros').val(), 'accion=transacciones_bancarias_actualizar', 'gehen.php?anz=ap_transacciones_bancarias_form&opcion=actualizar&origen=ap_transacciones_bancarias_lista', 'SELF', '');" />
			<input type="button" value="Desactualizar" style="width:85px; <?=$btDesactualizar?>" class="update" onclick="cargarOpcionValidar2(this.form, $('#sel_registros').val(), 'accion=transacciones_bancarias_desactualizar', 'gehen.php?anz=ap_transacciones_bancarias_form&opcion=desactualizar&origen=ap_transacciones_bancarias_lista', 'SELF', '');" />
		</td>
	</tr>
</table>

<div style="overflow:scroll; width:1050px; height:300px;">
<table width="2000" class="tblLista">
	<thead>
    <tr>
        <th scope="col" width="60" onclick="order('NroTransaccion')">N&uacute;mero</th>
        <th scope="col" width="25" onclick="order('Secuencia')">#</th>
        <th scope="col" width="75" onclick="order('FechaTransaccion')">Fecha</th>
        <th scope="col" width="200" onclick="order('NomTipoTransaccion')">Transacci&oacute;n</th>
        <th scope="col" width="100" align="right" onclick="order('Monto')">Monto</th>
        <th scope="col" width="125" onclick="order('NroCuenta')">Cta. Bancaria</th>
        <th scope="col" width="60" onclick="order('PeriodoContable')">Periodo</th>
        <th scope="col" width="60" onclick="order('Voucher')">Voucher</th>
        <th scope="col" width="100" onclick="order('Estado')">Estado</th>
        <th scope="col" width="125" onclick="order('CodigoReferenciaInterno')">Nro. Documento</th>
        <th scope="col" width="125" onclick="order('CodigoReferenciaBanco')">Doc. Referencia Banco</th>
        <th scope="col" width="125" onclick="order('NroPago')">Cheque</th>
        <th scope="col" align="left" onclick="order('Comentarios')">Comentarios</th>
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
				INNER JOIN  ap_ctabancaria cb ON (cb.NroCuenta = bt.NroCuenta)
				INNER JOIN mastbancos b ON (b.CodBanco = cb.CodBanco)
			WHERE 1 $filtro";
	$query = mysql_query($sql) or die ($sql.mysql_error());
	$rows_total = mysql_num_rows($query);
	
	//	consulto lista
	$sql = "SELECT
				bt.NroTransaccion,
				bt.Secuencia,
				bt.FechaTransaccion,
				bt.Monto,
				bt.NroCuenta,
				bt.PeriodoContable,
				bt.Voucher,
				bt.Estado,
				bt.CodigoReferenciaInterno,
				bt.CodigoReferenciaBanco,
				bt.NroPago,
				btt.Descripcion AS NomTipoTransaccion
			FROM
				ap_bancotransaccion bt
				INNER JOIN ap_bancotipotransaccion btt ON (btt.CodTipoTransaccion = bt.CodTipoTransaccion)
				INNER JOIN  ap_ctabancaria cb ON (cb.NroCuenta = bt.NroCuenta)
				INNER JOIN mastbancos b ON (b.CodBanco = cb.CodBanco)
			WHERE 1 $filtro
			ORDER BY $fOrderBy
			LIMIT ".intval($limit).", ".intval($maxlimit);
	$query = mysql_query($sql) or die ($sql.mysql_error());
	$rows_lista = mysql_num_rows($query);
	while ($field = mysql_fetch_array($query)) {
		$id = "$field[NroTransaccion]_$field[Secuencia]";
		?>
		<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
			<td align="center"><?=$field['NroTransaccion']?></td>
			<td align="center"><?=$field['Secuencia']?></td>
			<td align="center"><?=formatFechaDMA($field['FechaTransaccion'])?></td>
			<td><?=htmlentities($field['NomTipoTransaccion'])?></td>
			<td align="right"><strong><?=number_format($field['Monto'], 2, ',', '.')?></strong></td>
			<td align="center"><strong><?=$field['NroCuenta']?></strong></td>
			<td align="center"><?=$field['PeriodoContable']?></td>
			<td align="center"><?=$field['Voucher']?></td>
			<td align="center"><?=printValores("ESTADO-TRANSACCION-BANCARIA", $field['Estado'])?></td>
			<td align="center"><?=$field['CodigoReferenciaInterno']?></td>
			<td align="center"><?=$field['CodigoReferenciaBanco']?></td>
			<td align="center"><?=$field['NroPago']?></td>
			<td><?=htmlentities($field['Comentarios'])?></td>
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

<?php
if ($imprimir != "") {
	?>
    <script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		abrirReporteVal("aImprimir", "ap_transacciones_bancarias_pdf", '100%', '100%', $("#sel_registros"), "<?=$imprimir?>");
	});
    </script>
    <?php
}
?>