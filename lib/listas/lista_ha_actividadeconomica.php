<?php
if (!$ventana) $ventana = "selLista";
//	------------------------------------
if ($filtrar == "default") {
	$fEstado = 'A';
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "CodActividad";
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (ae.CodActividad LIKE '%".$fBuscar."%' OR
					  ae.Descripcion LIKE '%".$fBuscar."%' OR
					  ga.Descripcion LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (ae.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fCodGrupoActividad != "") { $cCodGrupoActividad = "checked"; $filtro.=" AND (ae.CodGrupoActividad = '".$fCodGrupoActividad."')"; } else $dCodGrupoActividad = "disabled";
//	------------------------------------
?>
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lista_ha_actividadeconomica" method="post">
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

<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
	<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
		<tr>
			<td align="right" width="100">Grupo:</td>
			<td>
	            <input type="checkbox" <?=$cCodGrupoActividad?> onclick="chkFiltro(this.checked, 'fCodGrupoActividad');" />
	            <select name="fCodGrupoActividad" id="fCodGrupoActividad" style="width:200px;" <?=$dCodGrupoActividad?>>
	                <option value="">&nbsp;</option>
	                <?=loadSelect2("ha_grupoactividad", "CodGrupoActividad", "Descripcion", $fCodGrupoActividad, 0)?>
	            </select>
			</td>
			<td align="right" width="100">Estado: </td>
			<td>
	            <input type="checkbox" <?=$cEstado?> onclick="this.checked=!this.checked;" />
	            <select name="fEstado" id="fEstado" style="width:100px;" <?=$dEstado?>>
	                <?=loadSelectGeneral("ESTADO", $fEstado, 1)?>
	            </select>
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td align="right">Buscar:</td>
			<td>
				<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
				<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:200px;" <?=$dBuscar?> />
			</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
	        <td align="right"><input type="submit" value="Buscar"></td>
		</tr>
	</table>
</div>
<div class="sep"></div>

<div style="overflow:scroll; height:260px; width:100%; min-width:<?=$_width?>px;">
	<table class="tblLista" style="width:100%; min-width:<?=$_width?>px;">
		<thead>
			<tr>
		        <th width="75" onclick="order('CodActividad')">C&oacute;digo</th>
		        <th align="left" onclick="order('Descripcion')">Denominaci&oacute;n</th>
		        <th align="left" onclick="order('GrupoActividad')">Grupo</th>
		        <th width="100" align="right" onclick="order('PorcentajeAlicuota')">Porcentaje Alicuota</th>
		        <th width="100" align="right" onclick="order('MinimoTributable')">Minimo Tributable</th>
		        <th width="75" onclick="order('Estado')">Estado</th>
		    </tr>
	    </thead>
	    
	    <tbody>
		<?php
		//	consulto todos
		$sql = "SELECT *
				FROM
					ha_actividadeconomica ae
					INNER JOIN ha_grupoactividad ga ON (ga.CodGrupoActividad = ae.CodGrupoActividad)
				WHERE 1 $filtro";
		$rows_total = getNumRows3($sql);
		//	consulto lista
		$sql = "SELECT
					ae.*,
					ga.Descripcion AS GrupoActividad
				FROM
					ha_actividadeconomica ae
					INNER JOIN ha_grupoactividad ga ON (ga.CodGrupoActividad = ae.CodGrupoActividad)
				WHERE 1 $filtro
				ORDER BY $fOrderBy
				LIMIT ".intval($limit).", ".intval($maxlimit);
		$field = getRecords($sql);
		$rows_lista = count($field);
		foreach($field as $f) {
			$id = $f['CodActividad'];
			if ($grupo != $f['CodGrupoCentroCosto']) {
				$grupo = $f['CodGrupoCentroCosto'];
				?>
	            <tr class="trListaBody2">
	                <td colspan="2"><?=$f['NomGrupoCentroCosto']?></td>
	            </tr>
	            <?php
			}
			if ($ventana == 'listado_insertar_linea') 
			{
				?><tr class="trListaBody" onClick="<?=$ventana?>('<?=$detalle?>','modulo=<?=$modulo?>&accion=<?=$accion?>&CodActividad=<?=$f['CodActividad']?>&detalle=<?=$detalle?>','<?=$f['CodActividad']?>','<?=$url?>');"><?php
			}
			else 
			{
				?><tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['CodActividad']?>','<?=$f['Descripcion']?>'], ['<?=$campo1?>','<?=$campo2?>']);"><?php
			}
			?>
				<td align="center"><?=$f['CodActividad']?></td>
				<td><?=htmlentities($f['Descripcion'])?></td>
				<td nowrap="true"><?=htmlentities($f['GrupoActividad'])?></td>
				<td align="right"><?=number_format($f['PorcentajeAlicuota'],2,',','.')?></td>
				<td align="right"><?=number_format($f['MinimoTributable'],2,',','.')?></td>
				<td align="center"><?=printValoresGeneral('ESTADO',$f['Estado'])?></td>
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