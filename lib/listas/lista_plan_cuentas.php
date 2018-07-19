<?php
if (!$ventana) $ventana = "selLista";
//	------------------------------------
if ($filtrar == "default") {
	$fEstado = "A";
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "CodCuenta";
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (pc.CodCuenta LIKE '%".$fBuscar."%' OR
					  pc.Descripcion LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (pc.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
//	------------------------------------
$_titulo = "Plan de Cuentas";
$_width = 900;
?>
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lista_plan_cuentas" method="post" autocomplete="off">
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
<input type="hidden" name="seldetalle" id="seldetalle" value="<?=$seldetalle?>" />

<div class="divBorder" style="width:100%; min-width:<?=$_width?>px; margin:auto;">
	<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
		<tr>
			<td align="right" width="100">Buscar:</td>
			<td>
				<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
				<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:300px;" <?=$dBuscar?> />
			</td>
	        <td align="right"><input type="submit" value="Buscar"></td>
		</tr>
	</table>
</div>
<div class="sep"></div>

<div class="scroll" style="overflow:scroll; height:315px; width:100%; min-width:<?=$_width?>px; margin:auto;">
	<table class="tblLista" style="width:100%; min-width:<?=$_width?>px;">
		<thead>
		    <tr>
		        <th width="100" onclick="order('CodCuenta')">C&oacute;digo</th>
		        <th align="left" onclick="order('Descripcion')">Descripci&oacute;n</th>
		    </tr>
	    </thead>
	    
	    <tbody>
		<?php
		//	consulto todos
		$sql = "SELECT pc.*
				FROM ac_mastplancuenta pc
				WHERE 1 $filtro";
		$rows_total = getNumRows3($sql);
		//	consulto lista
		$sql = "SELECT pc.*
				FROM ac_mastplancuenta pc
				WHERE 1 $filtro
				ORDER BY $fOrderBy
				LIMIT ".intval($limit).", ".intval($maxlimit);
		$field = getRecords($sql);
		$rows_lista = count($field);
		foreach($field as $f) {
			$id = $f['CodCuenta'];
			if ($ventana == "selListaOpener") {
				?>
				<tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['CodCuenta']?>','<?=$f['Descripcion']?>'], ['<?=$campo1?>','<?=$campo2?>']);">
				<?php
			}
			else {
				?>
				<tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['CodCuenta']?>','<?=$f['Descripcion']?>'], ['<?=$campo1?>','<?=$campo2?>']);">
				<?php
			}
			?>
				<td><?=$f['CodCuenta']?></td>
				<td><?=htmlentities($f['Descripcion'])?></td>
			</tr>
			<?php
		}
		?>
	    </tbody>
	</table>
</div>
<table style="width:100%; min-width:<?=$_width?>px; margin:auto;">
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