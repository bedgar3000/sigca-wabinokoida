<?php
$Ahora = ahora();
if ($filtrar == "default") {
	list($Anio, $Mes, $Dia) = split("[/.-]", substr($Ahora, 0, 10));
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$fFechaPagod = "01-$Mes-$Anio";
	$fFechaPagoh = "$Dia-$Mes-$Anio";
	$fEstado = "IM";
	$maxlimit = $_SESSION["MAXLIMIT"];
}
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (p.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fCodProveedor != "") { $cCodProveedor = "checked"; $filtro.=" AND (p.CodProveedor = '".$fCodProveedor."')"; } else $dCodProveedor = "visibility:hidden;";
if ($fNroProceso != "") { $cNroProceso = "checked"; $filtro.=" AND (p.NroProceso LIKE '%".$fNroProceso."%')"; } else $dNroProceso = "disabled";
if ($fNroPago != "") { $cNroPago = "checked"; $filtro.=" AND (p.NroPago LIKE '%".$fNroPago."%')"; } else $dNroPago = "disabled";
if ($fFechaPagod != "" || $fFechaPagoh != "") {
	$cFechaPago = "checked";
	if ($fFechaPagod != "") $filtro.=" AND (p.FechaPago >= '".formatFechaAMD($fFechaPagod)."')";
	if ($fFechaPagoh != "") $filtro.=" AND (p.FechaPago <= '".formatFechaAMD($fFechaPagoh)."')";
} else $dFechaPago = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (p.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fCodBanco != "") { $cCodBanco = "checked"; $filtro.=" AND (cb.CodBanco = '".$fCodBanco."')"; } else $dCodBanco = "disabled";
if ($fNroCuenta != "") { $cNroCuenta = "checked"; $filtro.=" AND (p.NroCuenta = '".$fNroCuenta."')"; } else $dNroCuenta = "disabled";
//	------------------------------------
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo">Lista de Pagos</td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=ap_pago_lista" method="post">
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="registro" id="registro" />
<div class="divBorder" style="width:1050px;">
<table width="1050" class="tblFiltro">
	<tr>
		<td align="right" width="125">Organismo:</td>
		<td>
			<input type="checkbox" <?=$cCodOrganismo?> onclick="this.checked=!this.checked" />
			<select name="fCodOrganismo" id="fCodOrganismo" style="width:265px;" <?=$dCodOrganismo?>>
				<?=getOrganismos($fCodOrganismo, 3)?>
			</select>
		</td>
		<td align="right" width="125">Nro. Pre-Pago:</td>
		<td>
			<input type="checkbox" <?=$cNroProceso?> onclick="chkFiltro(this.checked, 'fNroProceso');" />
			<input type="text" name="fNroProceso" id="fNroProceso" value="<?=$fNroProceso?>" maxlength="20" style="width:132px;" <?=$dNroProceso?> />
		</td>
	</tr>
	<tr>
		<td align="right">Proveedor: </td>
		<td class="gallery clearfix">
            <input type="checkbox" <?=$cCodProveedor?> onclick="chkFiltroLista_3(this.checked, 'fCodProveedor', 'fNomProveedor', '', 'btProveedor');" />
            
            <input type="text" name="fCodProveedor" id="fCodProveedor" style="width:50px;" value="<?=$fCodProveedor?>" readonly />
			<input type="text" name="fNomProveedor" id="fNomProveedor" style="width:200px;" value="<?=$fNomProveedor?>" readonly />
            <a href="../lib/listas/listado_personas.php?filtrar=default&cod=fCodProveedor&nom=fNomProveedor&iframe=true&width=950&height=525" rel="prettyPhoto[iframe1]" id="btProveedor" style=" <?=$dCodProveedor?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
		<td align="right">Nro. Pago:</td>
		<td>
			<input type="checkbox" <?=$cNroPago?> onclick="chkFiltro(this.checked, 'fNroPago');" />
			<input type="text" name="fNroPago" id="fNroPago" value="<?=$fNroPago?>" maxlength="20" style="width:132px;" <?=$dNroPago?> />
		</td>
	</tr>
	<tr>
		<td align="right">Banco:</td>
		<td>
			<input type="checkbox" <?=$cCodBanco?> onclick="chkCampos2(this.checked, ['fCodBanco','fNroCuenta']);" />
			<select name="fCodBanco" id="fCodBanco" style="width:150px;" <?=$dCodBanco?> onChange="getOptionsSelect(this.value, 'cuentas_bancarias', 'fNroCuenta', true)">
            	<option value="">&nbsp;</option>
                <?=loadSelect("mastbancos", "CodBanco", "Banco", $fCodBanco, 0)?>
			</select>
		</td>
		<td align="right">F.Orden: </td>
		<td>
			<input type="checkbox" <?=$cFechaPago?> onclick="chkFiltro_2(this.checked, 'fFechaPagod', 'fFechaPagoh');" />
			<input type="text" name="fFechaPagod" id="fFechaPagod" value="<?=$fFechaPagod?>" <?=$dFechaPago?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" />-
            <input type="text" name="fFechaPagoh" id="fFechaPagoh" value="<?=$fFechaPagoh?>" <?=$dFechaPago?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" />
        </td>
	</tr>
	<tr>
		<td align="right">Cta. Bancaria:</td>
		<td>
			<input type="checkbox" style="visibility:hidden;" />
			<select name="fNroCuenta" id="fNroCuenta" style="width:150px;" <?=$dCodBanco?>>
            	<option value="">&nbsp;</option>
                <?=loadSelectDependiente("ap_ctabancaria", "NroCuenta", "NroCuenta", "CodBanco", $fNroCuenta, $fCodBanco, 0)?>
			</select>
		</td>
		<td align="right">Estado:</td>
		<td>
            <input type="checkbox" onclick="this.checked=!this.checked;" checked="checked" />
            <select name="fEstado" id="fEstado" style="width:140px;">
                <?=loadSelectValores("ESTADO-PAGO2", $fEstado, 0)?>
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
		<td align="right">
			<input type="button" id="btImprimirSustento" value="Imprimir Sustento" style="width:100px;" onclick="cargarOpcion2(this.form, 'gehen.php?anz=ap_orden_pago_transferir_reportes', 'BLANK', 'height=800, width=1050, left=0, top=50, resizable=no', $('#registro').val());" />
            
			<input type="button" id="btVerVoucher" value="Ver Voucher" style="width:100px;" onclick="verVoucher('pago-ver');" />
            
			<input type="button" id="btVerPago" value="Ver Pago" style="width:100px;" onclick="cargarOpcion2(this.form, 'gehen.php?anz=ap_pago_form&opcion=ver&origen=ap_pago_lista', 'SELF', '', $('#registro').val());" /> | 
            
			<input type="button" id="btModificar" value="Modificar Pago" style="width:100px;" onclick="cargarOpcionValidar2(this.form, $('#registro').val(), 'accion=pago_modificar', 'gehen.php?anz=ap_pago_form&opcion=modificar&origen=ap_pago_lista', 'SELF', '');" />
            
			<input type="button" id="btAnular" value="Anular Pago" style="width:100px;" onclick="cargarOpcionValidar2(this.form, $('#registro').val(), 'accion=pago_anular', 'gehen.php?anz=ap_pago_form&opcion=anular&origen=ap_pago_lista', 'SELF', '');" />
		</td>
	</tr>
</table>

<div style="overflow:scroll; width:1050px; height:300px;">
<table width="1650" class="tblLista">
	<thead>
		<th scope="col" width="100">Cta. Bancaria</th>
		<th scope="col" width="90">N&uacute;mero</th>
		<th scope="col">Pagar A</th>
		<th scope="col" width="100">Monto</th>
		<th scope="col" width="75">Fecha</th>
		<th scope="col" width="75">Estado</th>
		<th scope="col" width="75">Pre-Pago</th>
		<th scope="col" width="20">#</th>
		<th scope="col" width="125">Tipo de Pago</th>
		<th scope="col" width="125">Origen</th>
		<?php
		if ($_PARAMETRO['CONTORDENDIS'] == "T") {
			?><th width="125" scope="col">Voucher</th><?php
		}
		elseif ($_PARAMETRO['CONTORDENDIS'] == "F") {
			?><th width="125" scope="col">Voucher (Pub.20)</th><?php
		}
		?>
		<th scope="col" width="125">Voucher Anulaci&oacute;n</th>
    </thead>
    
    <tbody>
	<?php
	//	consulto todos
	$sql = "SELECT
				p.*,
				tp.TipoPago,
				sf.Descripcion AS Origen
			FROM
				ap_pagos p
				INNER JOIN masttipopago tp ON (p.CodTipoPago = tp.CodTipoPago)
				INNER JOIN ap_ctabancaria cb ON (cb.NroCuenta = p.NroCuenta)
				LEFT JOIN ac_sistemafuente sf ON (p.CodSistemaFuente = sf.CodSistemaFuente)
			WHERE 1 $filtro
			ORDER BY NroProceso, Secuencia";
	$query = mysql_query($sql) or die ($sql.mysql_error());
	$rows_total = mysql_num_rows($query);
	
	//	consulto lista
	$sql = "SELECT
				p.*,
				tp.TipoPago,
				sf.Descripcion AS Origen
			FROM
				ap_pagos p
				INNER JOIN masttipopago tp ON (p.CodTipoPago = tp.CodTipoPago)
				INNER JOIN ap_ctabancaria cb ON (cb.NroCuenta = p.NroCuenta)
				LEFT JOIN ac_sistemafuente sf ON (p.CodSistemaFuente = sf.CodSistemaFuente)
			WHERE 1 $filtro
			ORDER BY NroProceso, Secuencia
			LIMIT ".intval($limit).", ".intval($maxlimit);
	$query = mysql_query($sql) or die ($sql.mysql_error());
	$rows_lista = mysql_num_rows($query);
	while ($field = mysql_fetch_array($query)) {
		$id = "$field[NroProceso]"."_"."$field[Secuencia]"."_"."$field[CodTipoPago]";
		?>
		<tr class="trListaBody" onclick="mClk(this, 'registro');" id="<?=$id?>">
			<td align="center"><?=$field['NroCuenta']?></td>
			<td align="center"><?=$field['NroPago']?></td>
			<td><?=htmlentities($field['NomProveedorPagar'])?></td>
			<td align="right"><strong><?=number_format($field['MontoPago'], 2, ',', '.')?></strong></td>
			<td align="center"><?=formatFechaDMA($field['FechaPago'])?></td>
			<td align="center"><?=printValores("ESTADO-PAGO", $field['Estado'])?></td>
			<td align="center"><?=$field['NroProceso']?></td>
			<td align="center"><?=$field['Secuencia']?></td>
			<td align="center"><?=($field['TipoPago'])?></td>
			<td align="center"><?=($field['CodSistemaFuente'])?></td>
			<?php
			if ($_PARAMETRO['CONTORDENDIS'] == "T") {
				?><td align="center"><?=$field['VoucherPeriodo']?>-<?=$field['VoucherPago']?></td><?php
			}
			elseif ($_PARAMETRO['CONTORDENDIS'] == "F") {
				?><td align="center"><?=$field['PeriodoPagoPub20']?>-<?=$field['VoucherPagoPub20']?></td><?php
			}
			?>
			<td align="center"><?=$field['VoucherAnulacion']?></td>
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

<div class="gallery clearfix">
    <a id="aVoucher" href="pagina.php?iframe=true" rel="prettyPhoto[iframe2]" style="display:none;"></a>
</div>


<?php
//	muestro vouchers
if ($mostrar == "vouchers") {
	?>
    <script type="text/javascript">
	$(document).ready(function() {
		var url = "gehen.php?anz=<?=$accion?>&registro=<?=$registro?>&accion=ver&origen=pago-anulacion&iframe=true&width=100%&height=100%";
		$("#aVoucher").attr("href", url);
		document.getElementById("aVoucher").click();
    });
    </script>
    <?php
}
?>