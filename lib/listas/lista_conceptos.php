<?php
if (!$ventana) $ventana = "selLista";
//	------------------------------------
$fEstado = 'A';
if ($filtrar == "default") {
	$fEstado = "A";
	$fOrderBy = "Tipo, CodConcepto";
	$maxlimit = $_SESSION["MAXLIMIT"];
}
if ($fTipo != "") { $cTipo = "checked"; $filtro.=" AND (c.Tipo = '".$fTipo."')"; } else $dTipo = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (c.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (c.CodConcepto LIKE '%".$fBuscar."%' OR
					  c.Descripcion LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
//	------------------------------------
$_titulo = "Listado de Conceptos";
$_width = 900;

if ($ventana == "selListaOpener") {
	?>
	<table width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td class="titulo"><?=$_titulo?></td>
			<td align="right"><a class="cerrar" href="javascript:" onclick="window.close();">[cerrar]</a></td>
		</tr>
	</table><hr width="100%" color="#333333" />
	<?php
}
?>

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lista_conceptos" method="post">
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
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="FlagTipo" id="FlagTipo" value="<?=$FlagTipo?>" />
<input type="hidden" name="Ejercicio" id="Ejercicio" value="<?=$Ejercicio?>" />

<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
	<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
		<tr>
			<td align="right">Buscar:</td>
			<td>
				<input type="checkbox" <?=$cBuscar?> onclick="chkFiltro(this.checked, 'fBuscar');" />
				<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:264px;" <?=$dBuscar?> />
			</td>
			<td align="right">Tipo: </td>
			<td>
				<?php
				if ($FlagTipo == "S") {
					?>
		       		<input type="checkbox" <?=$cTipo?> onclick="this.checked=!this.checked;" />
		            <select name="fTipo" id="fTipo" style="width:100px;" <?=$dTipo?>>
		                <?=loadSelectGeneral("CONCEPTO-TIPO", $fTipo, 1)?>
		            </select>
					<?php
				} else {
					?>
		       		<input type="checkbox" <?=$cTipo?> onclick="chkFiltro(this.checked, 'fTipo');" />
		            <select name="fTipo" id="fTipo" style="width:100px;" <?=$dTipo?>>
		                <option value=""></option>
		                <?=loadSelectGeneral("CONCEPTO-TIPO", $fTipo, 0)?>
		            </select>
					<?php
				}
				?>
			</td>
	        <td align="right"><input type="submit" value="Buscar"></td>
		</tr>
	</table>
</div>
<div class="sep"></div>

<div class="scroll" style="overflow:scroll; height:285px; width:100%; min-width:<?=$_width?>px;">
	<table class="tblLista" style="width:100%; min-width:1000px;">
		<thead>
		    <tr>
		        <th width="35">Codigo</th>
		        <th align="left">Descripci&oacute;n</th>
		        <th width="75">Tipo</th>
		        <th width="35">Aut.</th>
		        <th width="35">Ret.</th>
		        <th width="35">Bon.</th>
		    </tr>
	    </thead>
	    
	    <tbody>
		<?php
		//	consulto todos
		$sql = "SELECT c.*
				FROM pr_concepto c
				WHERE 1 $filtro";
		$rows_total = getNumRows3($sql);
		//	consulto lista
	    $sql = "SELECT c.*
	            FROM pr_concepto c
	            WHERE 1 $filtro
	            ORDER BY $fOrderBy
	            LIMIT ".intval($limit).", ".intval($maxlimit);
		$field = getRecords($sql);
		$rows_lista = count($field);
		foreach($field as $f) {
			$id = $f['CodConcepto'];
			if ($ventana == "listado_insertar_linea") {
				?><tr class="trListaBody" onClick="<?=$ventana?>('<?=$detalle?>','modulo=<?=$modulo?>&accion=<?=$accion?>&CodConcepto=<?=$f['CodConcepto']?>&detalle=<?=$detalle?>','<?=$f['CodConcepto']?>','<?=$url?>');"><?php
			}
			elseif ($ventana == "pr_proyparametro") {
				?><tr class="trListaBody" onClick="listado_insertar_linea('<?=$detalle?>','modulo=<?=$modulo?>&accion=<?=$accion?>&CodConcepto=<?=$f['CodConcepto']?>&detalle=<?=$detalle?>&Ejercicio=<?=$Ejercicio?>','<?=$f['CodConcepto']?>','<?=$url?>');"><?php
			}
			else {
				?><tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['CodConcepto']?>','<?=$f['Descripcion']?>'], ['<?=$campo1?>','<?=$campo2?>']);"><?php
			}
			?>
	            <td align="center"><?=$f['CodConcepto']?></td>
	            <td><?=htmlentities($f['Descripcion'])?></td>
	            <td align="center"><?=printValoresGeneral("CONCEPTO-TIPO", $f['Tipo'])?></td>
	            <td align="center"><?=printFlag($f['FlagAutomatico'])?></td>
	            <td align="center"><?=printFlag($f['FlagRetencion'])?></td>
	            <td align="center"><?=printFlag($f['FlagBono'])?></td>
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