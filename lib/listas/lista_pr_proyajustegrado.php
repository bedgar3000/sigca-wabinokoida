<?php
if (!$ventana) $ventana = "selLista";
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];

	$sql = "SELECT MAX(Ejercicio) FROM pr_proyajustegrado";
	$Ejercicio = getVar3($sql);
	$fEjercicio = ($Ejercicio?$AnioActual:$AnioActual);
	
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "CodAjuste";
}
//	------------------------------------
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (pag.Ejercicio LIKE '%".$fBuscar."%' OR
					  pag.Numero LIKE '%".$fBuscar."%' OR
					  pag.PeriodoDesde LIKE '%".$fBuscar."%' OR
					  pag.PeriodoHasta LIKE '%".$fBuscar."%' OR
					  pag.Descripcion LIKE '%".$fBuscar."%' OR
					  o.Organismo LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (pag.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (pag.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fEjercicio != "") { $cEjercicio = "checked"; $filtro.=" AND (pag.Ejercicio = '".$fEjercicio."')"; } else $dEjercicio = "disabled";
//	------------------------------------
$_titulo = "Ajustes por Grado Salarial";
$_width = 900;
?>
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lista_pr_proyajustegrado" method="post" autocomplete="off">
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

<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
	<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
		<tr>
			<td align="right" width="150">Organismo:</td>
			<td>
				<input type="checkbox" <?=$cCodOrganismo?> onclick="this.checked=!this.checked;" />
				<select name="fCodOrganismo" id="fCodOrganismo" style="width:275px;" <?=$dCodOrganismo?>>
					<?=getOrganismos($fCodOrganismo, 3);?>
				</select>
			</td>
			<td align="right" width="150">Estado: </td>
			<td>
	            <input type="checkbox" <?=$cEstado?> onclick="chkFiltro(this.checked, 'fEstado');" />
	            <select name="fEstado" id="fEstado" style="width:107px;" <?=$dEstado?>>
	                <option value="">&nbsp;</option>
	                <?=loadSelectGeneral("ESTADO", $fEstado, 0)?>
	            </select>
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td align="right">Buscar: </td>
			<td>
				<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
				<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:275px;" <?=$dBuscar?> />
			</td>
			<td align="right">Ejercicio: </td>
			<td>
				<input type="checkbox" <?=$cEjercicio?> onclick="chkCampos(this.checked, 'fEjercicio');" />
				<input type="text" name="fEjercicio" id="fBuscar" value="<?=$fEjercicio?>" style="width:45px;" maxlength="4" <?=$dEjercicio?> />
			</td>
	        <td align="right"><input type="submit" value="Buscar"></td>
		</tr>
	</table>
</div>
<div class="sep"></div>

<div class="scroll" style="overflow:scroll; height:315px; width:100%; min-width:<?=$_width?>px; margin:auto;">
	<table class="tblLista" style="width:100%; min-width:1200px;">
		<thead>
		    <tr>
		        <th width="300" align="left" onclick="order('Organismo,Ejercicio,Numero')">Organismo</th>
		        <th width="60" onclick="order('Ejercicio,Numero')">Ejercicio</th>
		        <th width="35" onclick="order('Numero')">Nro.</th>
		        <th width="75" onclick="order('PeriodoDesde')">Desde</th>
		        <th width="75" onclick="order('PeriodoHasta')">Hasta</th>
		        <th align="left" onclick="order('Descripcion')">Descripci&oacute;n</th>
		        <th width="100" onclick="order('Estado')">Estado</th>
		    </tr>
	    </thead>
	    
	    <tbody>
		<?php
		//	consulto todos
		$sql = "SELECT pag.*
				FROM
					pr_proyajustegrado pag
					INNER JOIN mastorganismos o ON (o.CodOrganismo = pag.CodOrganismo)
				WHERE 1 $filtro";
		$rows_total = getNumRows3($sql);
		//	consulto lista
		$sql = "SELECT
					pag.*,
					o.Organismo
				FROM
					pr_proyajustegrado pag
					INNER JOIN mastorganismos o ON (o.CodOrganismo = pag.CodOrganismo)
				WHERE 1 $filtro
				ORDER BY $fOrderBy
				LIMIT ".intval($limit).", ".intval($maxlimit);
		$field = getRecords($sql);
		$rows_lista = count($field);
		foreach($field as $f) {
			$id = $f['CodAjuste'];
			if ($ventana == 'listado_insertar_linea') {
				?><tr class="trListaBody" onClick="<?=$ventana?>('<?=$detalle?>','modulo=<?=$modulo?>&accion=<?=$accion?>&CodAjuste=<?=$f['CodAjuste']?>','<?=$f['CodAjuste']?>','<?=$url?>');"><?php
			}
			else {
				?><tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['CodAjuste']?>','<?=$f['PeriodoDesde']?>','<?=$f['PeriodoHasta']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>']);"><?php
			}
			?>
				<td><?=htmlentities($f['Organismo'])?></td>
				<td align="center"><?=$f['Ejercicio']?></td>
				<td align="center"><?=$f['Numero']?></td>
				<td align="center"><?=$f['PeriodoDesde']?></td>
				<td align="center"><?=$f['PeriodoHasta']?></td>
				<td><?=htmlentities($f['Descripcion'])?></td>
				<td align="center"><?=printValoresGeneral('ESTADO',$f['Estado'])?></td>
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