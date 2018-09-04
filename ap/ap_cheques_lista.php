<?php
if ($lista == "entregar") {
	$_titulo = "Entrega de Cheques";
	$fEstadoEntrega = "C";
	$display_cobranza = "display:none;";
	$lbl_entrega = "Entrega";
}
elseif ($lista == "devolver") {
	$_titulo = "Devoluci&oacute;n de Cheques";
	$fEstadoEntrega = "E";
	$display_cobranza = "display:none;";
	$lbl_entrega = "Devoluci&oacute;n";
}
elseif ($lista == "cobrar") {
	$_titulo = "Ingreso de Cheques Cobrados";
	$fEstadoEntrega = "E";
	$display_entrega = "display:none;";
}
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "NroCuenta";
	$fFechaPagoD = "01-$MesActual-$AnioActual";
	$fFechaPagoH = "$DiaActual-$MesActual-$AnioActual";
	$FechaCobranza = "$DiaActual-$MesActual-$AnioActual";
	$FechaEntregado = "$DiaActual-$MesActual-$AnioActual";
	$fFlagCobrado = "N";
}
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (p.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fCodBanco != "") { 
	$cCodBanco = "checked"; 
	$filtro.=" AND (cb.CodBanco = '".$fCodBanco."')"; 
	if ($fNroCuenta != "") $filtro.=" AND (p.NroCuenta = '".$fNroCuenta."')";
} else $dCodBanco = "disabled";
if ($fCodProveedor != "") { $cCodProveedor = "checked"; $filtro.=" AND (p.CodProveedor = '".$fCodPersona."')"; } else $dCodProveedor = "visibility:hidden;";
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (p.CodProveedor LIKE '%".$fBuscar."%' OR
					  p.NomProveedorPagar LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fEstadoEntrega != "") { $cEstadoEntrega = "checked"; $filtro.=" AND (p.EstadoEntrega = '".$fEstadoEntrega."')"; } else $dEstadoEntrega = "disabled";
if ($fFlagCobrado != "") { $cFlagCobrado = "checked"; $filtro.=" AND (p.FlagCobrado = '".$fFlagCobrado."')"; } else $dFlagCobrado = "disabled";
if ($fFechaPagoD != "" || $fFechaPagoH != "") {
	$cFechaPago = "checked";
	if ($fFechaPagoD != "") $filtro.=" AND (p.FechaPago >= '".formatFechaAMD($fFechaPagoD)."')";
	if ($fFechaPagoH != "") $filtro.=" AND (p.FechaPago <= '".formatFechaAMD($fFechaPagoH)."')";
} else $dFechaPagoD = "disabled";
//	------------------------------------
$_width = 900;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=ap_cheques_lista" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="fFlagCobrado" id="fFlagCobrado" value="<?=$fFlagCobrado?>" />

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
		<td align="right" width="100">Banco:</td>
		<td>
			<input type="checkbox" <?=$cCodBanco?> onclick="chkCampos2(this.checked, ['fCodBanco','fNroCuenta']);" />
			<select name="fCodBanco" id="fCodBanco" style="width:150px;" <?=$dCodBanco?> onChange="getOptionsSelect(this.value, 'cuentas_bancarias', 'fNroCuenta', true)">
            	<option value="">&nbsp;</option>
                <?=loadSelect("mastbancos", "CodBanco", "Banco", $fCodBanco, 0)?>
			</select>
		</td>
	</tr>
	<tr>
		<td align="right">Proveedor:</td>
		<td class="gallery clearfix">
            <input type="checkbox" <?=$cCodProveedor?> onClick="ckLista(this.checked, ['fCodProveedor','fNomProveedor'], ['btCodProveedor'])" />
            <input type="text" name="fCodProveedor" id="fCodProveedor" value="<?=$fCodProveedor?>" style="width:40px;" class="disabled" readonly />
			<input type="text" name="fNomProveedorPagar" id="fNomProveedorPagar" value="<?=$fNomProveedorPagar?>" style="width:220px;" class="disabled" readonly />
            <a href="../lib/listas/listado_personas.php?filtrar=default&cod=fCodProveedor&nom=fNomProveedorPagar&iframe=true&width=950&height=390" rel="prettyPhoto[iframe1]" id="btCodProveedor" style=" <?=$dCodProveedor?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
		<td align="right">Cta. Bancaria:</td>
		<td>
			<input type="checkbox" style="visibility:hidden;" />
			<select name="fNroCuenta" id="fNroCuenta" style="width:150px;" <?=$dCodBanco?>>
            	<option value="">&nbsp;</option>
                <?=loadSelectDependiente("ap_ctabancaria", "NroCuenta", "NroCuenta", "CodBanco", $fNroCuenta, $fCodBanco, 0)?>
			</select>
		</td>
	</tr>
	<tr>
		<td align="right">Buscar:</td>
		<td>
			<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
			<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:269px;" <?=$dBuscar?> />
		</td>
        <?php
		if ($lista == "cobrar") {
			?>
            <td align="right">Situaci&oacute;n: </td>
            <td>
                <input type="checkbox" checked="checked" onclick="this.checked=!this.checked;" />
                <select name="fFlagCobrado" id="fFlagCobrado" style="width:150px;">
                    <?=loadSelectValores("ESTADO-CHEQUE-COBRO", $fFlagCobrado, 0)?>
                </select>
            </td>
			<?php
		} else {
			?>
            <td align="right">Estado: </td>
            <td>
                <input type="checkbox" checked="checked" onclick="this.checked=!this.checked;" />
                <select name="fEstadoEntrega" id="fEstadoEntrega" style="width:150px;">
                    <?=loadSelectValores("ESTADO-CHEQUE", $fEstadoEntrega, 1)?>
                </select>
            </td>
			<?php
		}
		?>
	</tr>
	<tr>
		<td align="right">&nbsp;</td>
		<td>
			<input type="checkbox" name="fFlagVencidos" id="fFlagVencidos" <?=$cFlagVencidos?> /> Ver Solo Vencidos
		</td>
		<td align="right">F.Pago: </td>
		<td>
			<input type="checkbox" checked onclick="chkFiltro_2(this.checked, 'fFechaPagoD', 'fFechaPagoH');" />
			<input type="text" name="fFechaPagoD" id="fFechaPagoD" value="<?=$fFechaPagoD?>" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" /> -
            <input type="text" name="fFechaPagoH" id="fFechaPagoH" value="<?=$fFechaPagoH?>" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" />
        </td>
	</tr>
</table>
</div>
<center><input type="submit" value="Buscar"></center><br />

<!--REGISTROS-->
<center>
<table width="<?=$_width?>" class="tblForm" style=" <?=$display_cobranza?>">
	<tr>
        <td class="tagForm" width="125">Fecha de Cobranza:</td>
		<td width="125"><input type="text" name="FechaCobranza" id="FechaCobranza" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" /></td>
        <td class="tagForm" width="125">Nro. Operaci&oacute;n:</td>
		<td><input type="text" name="NroPagoVoucher" id="NroPagoVoucher" style="width:100px;" maxlength="20" /></td>
	</tr>
</table>
<table width="<?=$_width?>" class="tblForm" style=" <?=$display_entrega?>">
	<tr>
        <td width="125" class="tagForm">Fecha de <?=$lbl_entrega?>:</td>
		<td><input type="text" name="FechaEntregado" id="FechaEntregado" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" /></td>
	</tr>
</table>

<input type="hidden" name="sel_registros" id="sel_registros" />
<table width="<?=$_width?>" class="tblBotones">
    <tr>
        <td>
        	<a class="link" href="#" onclick="selTodos('registros');">Todos</a> |
            <a class="link" href="#" onclick="selNinguno('registros');">Ninguno</a>
        </td>
        <td align="right">
        	<input type="button" id="btVer" value="Ver" style="width:75px; <?=$btVer?>" onclick="cargarOpcionMultiple2(this.form, 'gehen.php?anz=ap_pago_form&opcion=ver&origen=ap_cheques_lista', 'SELF', '', 'registros', 'sel_registros', 0)" />
            
            <input type="button" id="btProcesar" value="Procesar" style="width:75px;" onclick="cheques(this.form, '<?=$lista?>');" />
        </td>
    </tr>
</table>

<div style="overflow:scroll; width:<?=$_width?>px; height:300px;">
<table width="100%" class="tblLista">
	<thead>
    <tr>
        <th scope="col" width="80">Cheque</th>
        <th scope="col" align="left">Proveedor</th>
        <th scope="col" align="left" width="90">Doc. Fiscal</th>
        <th scope="col" width="75">Fecha Pago</th>
        <th scope="col" width="100" align="right">Monto</th>
        <th scope="col" width="75">Pre-Pago</th>
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
				INNER JOIN mastpersonas pe ON (pe.CodPersona = p.CodProveedor)
				INNER JOIN ap_ctabancaria cb ON (cb.NroCuenta = p.NroCuenta)
			WHERE
				p.Estado = 'IM' AND
				p.CodTipoPago = '02'
				$filtro";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	$rows_total = mysql_num_rows($query);
	
	//	consulto lista
	$sql = "SELECT
				p.NroProceso,
				p.Secuencia,
				p.NroPago,
				p.NomProveedorPagar,
				p.FechaPago,
				p.MontoPago,
				p.NroCuenta,
				pe.DocFiscal,
				cb.Descripcion AS DescCuenta
			FROM
				ap_pagos p
				INNER JOIN mastpersonas pe ON (pe.CodPersona = p.CodProveedor)
				INNER JOIN ap_ctabancaria cb ON (cb.NroCuenta = p.NroCuenta)
			WHERE
				p.Estado = 'IM' AND
				p.CodTipoPago = '02'
				$filtro
			ORDER BY $fOrderBy
			LIMIT ".intval($limit).", ".intval($maxlimit);
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	$rows_lista = mysql_num_rows($query);
	while ($field = mysql_fetch_array($query)) {
		$id = "$field[NroProceso]"."_"."$field[Secuencia]";
		if ($Grupo != $field['NroCuenta']) {
			$Grupo = $field['NroCuenta'];
			?>
			<tr class="trListaBody2">
				<td colspan="6"><?=htmlentities($field['NroCuenta'].' '.$field['DescCuenta'])?></td>
			</tr>
			<?php
			
		}
		?>
		<tr class="trListaBody" onclick="clkMulti($(this), '<?=$id?>');">
			<td align="center">
            	<input type="checkbox" name="registros" id="<?=$id?>" value="<?=$id?>" style="display:none" />
				<?=$field['NroPago']?>
            </td>
			<td><?=htmlentities($field['NomProveedorPagar'])?></td>
			<td><?=$field['DocFiscal']?></td>
			<td align="center"><?=formatFechaDMA($field['FechaPago'])?></td>
			<td align="right"><strong><?=number_format($field['MontoPago'], 2, ',', '.')?></strong></td>
			<td align="center"><?=$field['NroProceso']?></td>
		</tr>
		<?php
	}
	?>
    </tbody>
</table>
</div>
<table width="<?=$_width?>">

	<tr>
    	<td>
        	Mostrar: 
            <select name="maxlimit" id="maxlimit" style="width:50px;" onchange="this.form.submit();">
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