<?php
if (!$ventana) $ventana = "selLista";
//	------------------------------------
if ($filtrar == "default") {

	$sql = "SELECT MAX(Ejercicio) FROM pr_proyparametro";
	$Ejercicio = getVar3($sql);
	$fEjercicio = ($Ejercicio?$AnioActual:$AnioActual);
	
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "CodParametro";
	$fEstado = 'A';
}
//	------------------------------------
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (ppm.Ejercicio LIKE '%".$fBuscar."%' OR
					  ppm.Numero LIKE '%".$fBuscar."%' OR
					  tp.Descripcion LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (ppm.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fEjercicio != "") { $cEjercicio = "checked"; $filtro.=" AND (ppm.Ejercicio = '".$fEjercicio."')"; } else $dEjercicio = "disabled";
if ($fCodTipoProceso != "") { $cCodTipoProceso = "checked"; $filtro.=" AND (ppm.CodTipoProceso = '".$fCodTipoProceso."')"; } else $dCodTipoProceso = "disabled";
//	------------------------------------
$_titulo = "Planificaci&oacute;n de Par&aacute;metros";
$_width = 900;
?>
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lista_pr_proyparametro" method="post" autocomplete="off">
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
			<td align="right" width="100">Proceso:</td>
			<td>
	            <input type="checkbox" <?=$cCodTipoProceso?> onclick="chkFiltro(this.checked, 'fCodTipoProceso');" />
				<select name="fCodTipoProceso" id="fCodTipoProceso" style="width:275px;" <?=$dCodTipoProceso?>>
	                <option value="">&nbsp;</option>
					<?=loadSelect2('pr_tipoproceso','CodTipoproceso','Descripcion',$fCodTipoProceso)?>
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
		        <th width="60" onclick="order('Ejercicio')">Ejercicio</th>
		        <th width="30" onclick="order('Numero')">Nro.</th>
		        <th align="left" onclick="order('Nomina')">N&oacute;mina</th>
		        <th align="left" onclick="order('TipoProceso')">Proceso</th>
		        <th width="75" onclick="order('Estado')">Estado</th>
		    </tr>
	    </thead>
	    
	    <tbody>
		<?php
		//	consulto todos
		$sql = "SELECT ppm.*
				FROM
					pr_proyparametro ppm
					INNER JOIN pr_tipoproceso tp ON (tp.CodTipoProceso = ppm.CodTipoProceso)
					INNER JOIN pr_proyrecursos ppr ON (ppr.CodRecurso = ppm.CodRecurso)
					INNER JOIN tiponomina tn ON (tn.CodTipoNom = ppr.CodTipoNom)
				WHERE 1 $filtro";
		$rows_total = getNumRows3($sql);
		//	consulto lista
		$sql = "SELECT
					ppm.*,
					tp.Descripcion AS TipoProceso,
					ppr.Ejercicio,
					ppr.Numero,
					ppr.CodTipoNom,
					tn.Nomina
				FROM
					pr_proyparametro ppm
					INNER JOIN pr_tipoproceso tp ON (tp.CodTipoProceso = ppm.CodTipoProceso)
					INNER JOIN pr_proyrecursos ppr ON (ppr.CodRecurso = ppm.CodRecurso)
					INNER JOIN tiponomina tn ON (tn.CodTipoNom = ppr.CodTipoNom)
				WHERE 1 $filtro
				ORDER BY $fOrderBy
				LIMIT ".intval($limit).", ".intval($maxlimit);
		$field = getRecords($sql);
		$rows_lista = count($field);
		foreach($field as $f) {
			$id = $f['CodParametro'];
			if ($ventana == 'listado_insertar_linea') {
				?><tr class="trListaBody" onClick="<?=$ventana?>('<?=$detalle?>','modulo=<?=$modulo?>&accion=<?=$accion?>&CodParametro=<?=$f['CodParametro']?>','<?=$f['CodParametro']?>','<?=$url?>');"><?php
			}
			else {
				?><tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['CodParametro']?>','<?=$f['CodRecurso']?>','<?=$f['Ejercicio']?>','<?=$f['Numero']?>','<?=$f['CodTipoNom']?>','<?=$f['CodTipoProceso']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>','<?=$campo4?>','<?=$campo5?>','<?=$campo6?>']);"><?php
			}
			?>
				<td align="center"><?=$f['Ejercicio']?></td>
				<td align="center"><?=$f['Numero']?></td>
				<td><?=htmlentities($f['Nomina'])?></td>
				<td><?=htmlentities($f['TipoProceso'])?></td>
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