<?php
if (!$ventana) $ventana = "selLista";
//	------------------------------------
if ($filtrar == "default") {
	$fOrderBy = "TipoTransaccion, CodTipoTransaccion";
	$maxlimit = $_SESSION["MAXLIMIT"];
}
if ($fCodVoucher != "") { $cCodVoucher = "checked"; $filtro.=" AND (btt.CodVoucher = '".$fCodVoucher."')"; } else $dCodVoucher = "disabled";
if ($fTipoTransaccion != "") { $cTipoTransaccion = "checked"; $filtro.=" AND (btt.TipoTransaccion = '".$fTipoTransaccion."')"; } else $dTipoTransaccion = "disabled";
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (btt.CodTipoTransaccion LIKE '%".$fBuscar."%' OR
					  btt.Descripcion LIKE '%".$fBuscar."%' OR
					  btt.CodVoucher LIKE '%".$fBuscar."%' OR
					  btt.CodCuenta LIKE '%".$fBuscar."%' OR
					  btt.CodCuentaPub20 LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
//	------------------------------------
$_width = 900;
?>
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lista_ap_bancotipotransaccion" method="post">
<input type="hidden" name="registro" id="registro" />
<input type="hidden" name="campo1" id="campo1" value="<?=$campo1?>" />
<input type="hidden" name="campo2" id="campo2" value="<?=$campo2?>" />
<input type="hidden" name="campo3" id="campo3" value="<?=$campo3?>" />
<input type="hidden" name="campo4" id="campo4" value="<?=$campo4?>" />
<input type="hidden" name="campo5" id="campo5" value="<?=$campo5?>" />
<input type="hidden" name="campo6" id="campo6" value="<?=$campo6?>" />
<input type="hidden" name="campo7" id="campo7" value="<?=$campo7?>" />
<input type="hidden" name="campo8" id="campo8" value="<?=$campo8?>" />
<input type="hidden" name="campo9" id="campo9" value="<?=$campo9?>" />
<input type="hidden" name="campo10" id="campo10" value="<?=$campo10?>" />
<input type="hidden" name="ventana" id="ventana" value="<?=$ventana?>" />
<input type="hidden" name="detalle" id="detalle" value="<?=$detalle?>" />
<input type="hidden" name="seldetalle" id="seldetalle" value="<?=$seldetalle?>" />
<input type="hidden" name="modulo" id="modulo" value="<?=$modulo?>" />
<input type="hidden" name="accion" id="accion" value="<?=$accion?>" />
<input type="hidden" name="url" id="url" value="<?=$url?>" />
<input type="hidden" name="MontoAfecto" id="MontoAfecto" value="<?=$MontoAfecto?>" />
<input type="hidden" name="MontoImpuesto" id="MontoImpuesto" value="<?=$MontoImpuesto?>" />
<input type="hidden" name="MontoTotal" id="MontoTotal" value="<?=$MontoTotal?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />

<!--FILTRO-->
<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
	<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
		<tr>
			<td align="right">Tipo de Voucher:</td>
			<td>
				<input type="checkbox" <?=$cCodVoucher?> onclick="chkCampos(this.checked, 'fCodVoucher');" />
				<select name="fCodVoucher" id="fCodVoucher" style="width:250px;" <?=$dCodVoucher?>>
					<option value="">&nbsp;</option>
					<?=loadSelect2('ac_voucher','CodVoucher','Descripcion',$fCodVoucher,10)?>
				</select>
			</td>
			<td align="right">Tipo: </td>
			<td>
	            <input type="checkbox" <?=$cTipoTransaccion?> onclick="chkFiltro(this.checked, 'fTipoTransaccion');" />
				<select name="fTipoTransaccion" id="fTipoTransaccion" style="width:100px;" <?=$dTipoTransaccion?>>
					<option value="">&nbsp;</option>
					<?=loadSelectGeneral("TIPO-TRANSACCION-BANCARIA", $fTipoTransaccion, 0)?>
				</select>
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td align="right">Buscar: </td>
			<td>
				<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
				<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:250px;" <?=$dBuscar?> />
			</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
	        <td align="right"><input type="submit" value="Buscar"></td>
		</tr>
	</table>
</div>
<div class="sep"></div>

<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px; margin:auto;">
	<table class="tblLista" style="width:100%; min-width:1000px;">
		<thead>
		    <tr>
		        <th width="60" onclick="order('CodTipoTransaccion')">Tipo</th>
		        <th align="left" onclick="order('Descripcion')">Descripci&oacute;n</th>
		        <th width="75" onclick="order('TipoTransaccion, CodTipoTransaccion')">Tipo</th>
		        <th width="35" onclick="order('FlagVoucher')">Gr. Vou.</th>
		        <th width="35" onclick="order('CodVoucher')">Vou.</th>
		        <th width="125" align="left" onclick="order('CodCuenta')">Cuenta</th>
		        <th width="125" align="left" onclick="order('CodCuentaPub20')">Cuenta (Pub.20)</th>
		    </tr>
	    </thead>
	    
	    <tbody>
		<?php
		//	consulto todos
		$sql = "SELECT btt.*
	            FROM ap_bancotipotransaccion btt
				WHERE 1 $filtro";
		$rows_total = getNumRows3($sql);
		//	consulto lista
		$sql = "SELECT btt.*
	            FROM ap_bancotipotransaccion btt
				WHERE 1 $filtro
				ORDER BY $fOrderBy
				LIMIT ".intval($limit).", ".intval($maxlimit);
		$field = getRecords($sql);
		$rows_lista = count($field);
		foreach($field as $f) {
			$id = $f['CodTipoTransaccion'];
			if ($ventana == 'ap_bancotransaccion_insertar') 
			{
				?><tr class="trListaBody" onClick="listado_insertar_linea('<?=$detalle?>','modulo=<?=$modulo?>&accion=<?=$accion?>&CodTipoTransaccion=<?=$f['CodTipoTransaccion']?>&detalle=<?=$detalle?>','<?=$f['CodTipoTransaccion']?>','<?=$url?>');"><?php
			}
			else 
			{
				?><tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['CodTipoTransaccion']?>','<?=$f['Descripcion']?>'], ['<?=$campo1?>','<?=$campo2?>']);"><?php
			}
			?>
	            <td><?=$f['CodTipoTransaccion']?></td>
				<td><?=htmlentities($f['Descripcion'])?></td>
	            <td align="center"><?=printValoresGeneral("TIPO-TRANSACCION-BANCARIA", $f['TipoTransaccion'])?></td>
	            <td align="center"><?=printFlag($f['FlagVoucher'])?></td>
	            <td align="center"><?=$f['CodVoucher']?></td>
	            <td><?=$f['CodCuenta']?></td>
	            <td><?=$f['CodCuentaPub20']?></td>
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
</form>

<script type="text/javascript" language="javascript">
</script>