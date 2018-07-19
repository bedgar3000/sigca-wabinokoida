<?php
//	------------------------------------
$fBuscar = ($fBuscar?$fBuscar:$_SESSION["fBuscar"]);
$fcod_tipocuenta = ($fcod_tipocuenta?$fcod_tipocuenta:$_SESSION["fcod_tipocuenta"]);
$_SESSION["fBuscar"] = $fBuscar;
$_SESSION["fcod_tipocuenta"] = $fcod_tipocuenta;
##	
if ($filtrar == "default") {
	$fEstado = "A";
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "cod_partida";
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (pv.cod_partida LIKE '%".$fBuscar."%' OR
					  pv.denominacion LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (pv.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fcod_tipocuenta != "") { $ccod_tipocuenta = "checked"; $filtro.=" AND (tc.cod_tipocuenta = '".$fcod_tipocuenta."')"; } else $dcod_tipocuenta = "disabled";
//	------------------------------------
$_titulo = "Clasificador Presupuestario";
$_width = 900;
?>
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lista_partidas_presupuesto" method="post" autocomplete="off">
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
<input type="hidden" name="modulo" id="modulo" value="<?=$modulo?>" />
<input type="hidden" name="accion" id="accion" value="<?=$accion?>" />
<input type="hidden" name="url" id="url" value="<?=$url?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
<input type="hidden" name="FlagTipoCuenta" id="FlagTipoCuenta" value="<?=$FlagTipoCuenta?>" />
<input type="hidden" name="CodOrganismo" id="CodOrganismo" value="<?=$CodOrganismo?>" />
<input type="hidden" name="CodPresupuesto" id="CodPresupuesto" value="<?=$CodPresupuesto?>" />

<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
	<tr>
		<td align="right" width="125">Tipo de Cuenta:</td>
		<td>
			<?php
			if ($FlagTipoCuenta == 'S') {
				?>
				<input type="checkbox" <?=$ccod_tipocuenta?> onclick="this.checked=!this.checked;" />
	        	<select name="fcod_tipocuenta" id="fcod_tipocuenta" style="width:155px;" <?=$dcod_tipocuenta?>>
	                <?=loadSelect2('pv_tipocuenta','cod_tipocuenta','descp_tipocuenta',$fcod_tipocuenta,1)?>
	            </select>
				<?php
			} else {
				?>
				<input type="checkbox" <?=$ccod_tipocuenta?> onclick="chkCampos(this.checked, 'fcod_tipocuenta');" />
	        	<select name="fcod_tipocuenta" id="fcod_tipocuenta" style="width:155px;" <?=$dcod_tipocuenta?>>
	            	<option value="">&nbsp;</option>
	                <?=loadSelect2('pv_tipocuenta','cod_tipocuenta','descp_tipocuenta',$fcod_tipocuenta)?>
	            </select>
				<?php
			}
			?>
		</td>
		<td align="right" width="100">Buscar:</td>
		<td>
			<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
			<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:149px;" <?=$dBuscar?> />
		</td>
        <td align="right"><input type="submit" value="Buscar"></td>
	</tr>
</table>
</div>
<div class="sep"></div>

<center>
<div style="overflow:scroll; height:315px; width:100%; min-width:<?=$_width?>px;">
<table class="tblLista" style="width:100%; min-width:1300px;">
	<thead>
    <tr>
        <th width="75" onclick="order('cod_partida')">C&oacute;digo</th>
        <th align="left" onclick="order('denominacion')">Descripci&oacute;n</th>
        <th width="90" onclick="order('descp_tipocuenta')">Tipo de Cuenta</th>
        <th width="90" onclick="order('Estado')">Estado</th>
    </tr>
    </thead>
    
    <tbody>
	<?php
	//	consulto todos
	$sql = "SELECT pv.cod_partida
			FROM
				pv_partida pv
				INNER JOIN pv_tipocuenta tc ON (tc.cod_tipocuenta = pv.cod_tipocuenta)
				INNER JOIN pv_presupuestodet pd ON (pd.cod_partida = pv.cod_partida)
			WHERE
				pd.CodOrganismo = '$CodOrganismo' AND
				pd.CodPresupuesto = '$CodPresupuesto'
				$filtro";
	$rows_total = getNumRows3($sql);
	//	consulto lista
	$sql = "SELECT
				pv.*,
				tc.descp_tipocuenta
			FROM
				pv_partida pv
				INNER JOIN pv_tipocuenta tc ON (tc.cod_tipocuenta = pv.cod_tipocuenta)
				INNER JOIN pv_presupuestodet pd ON (pd.cod_partida = pv.cod_partida)
			WHERE
				pd.CodOrganismo = '$CodOrganismo' AND
				pd.CodPresupuesto = '$CodPresupuesto'
				$filtro
			ORDER BY $fOrderBy
			LIMIT ".intval($limit).", ".intval($maxlimit);
	$field = getRecords($sql);
	$rows_lista = count($field);
	foreach($field as $f) {
		$id = $f['cod_partida'];
		if ($ventana == 'listado_insertar_linea') {
			?><tr class="trListaBody" onClick="<?=$ventana?>('<?=$detalle?>','modulo=<?=$modulo?>&accion=<?=$accion?>&cod_partida=<?=$f['cod_partida']?>','<?=$f['cod_partida']?>','<?=$url?>');"><?php
		} 
		elseif ($ventana == 'pv_ajustes') {
			?><tr class="trListaBody" onClick="listado_insertar_linea('<?=$detalle?>','modulo=<?=$modulo?>&accion=<?=$accion?>&cod_partida=<?=$f['cod_partida']?>&CodOrganismo=<?=$CodOrganismo?>&CodPresupuesto=<?=$CodPresupuesto?>&detalle=<?=$detalle?>','<?=$f['cod_partida']?>','<?=$url?>');"><?php
		} 
		else {
			?><tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['cod_partida']?>','<?=$f['denominacion']?>'], ['<?=$campo1?>','<?=$campo2?>']);"><?php
		}
		?>
			<td align="center"><?=$f['cod_partida']?></td>
			<td><?=htmlentities($f['denominacion'])?></td>
			<td><?=htmlentities($f['descp_tipocuenta'])?></td>
			<td align="center"><?=printValoresGeneral("ESTADO", $f['Estado'])?></td>
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