<?php
if (!$ventana) $ventana = "selLista";
//	------------------------------------
if ($filtrar == "default") {
	$fedoreg = "A";
	$maxlimit = $_SESSION["MAXLIMIT"];
}
if ($fedoreg != "") { $cedoreg = "checked"; $filtro.=" AND (cc.Estado = '".$fedoreg."')"; } else $dedoreg = "disabled";
if ($fbuscar != "") {
	$cbuscar = "checked";
	$filtro.=" AND (cc.CodCentroCosto LIKE '%".$fbuscar."%' OR 
					cc.Descripcion LIKE '%".$fbuscar."%' OR 
					cc.Abreviatura LIKE '%".$fbuscar."%')";
} else $dbuscar = "disabled";
if ($fgrupocc != "") { $cgrupocc = "checked"; $filtro.=" AND (cc.CodGrupoCentroCosto = '".$fgrupocc."')"; } else $dgrupocc = "disabled";
if ($fsubgrupocc != "") { $csubgrupocc = "checked"; $filtro.=" AND (cc.CodSubGrupoCentroCosto = '".$fsubgrupocc."')"; } else $dsubgrupocc = "disabled";
if ($fCodOrganismo) { $cCodOrganismo = "checked"; $filtro.=" AND (d.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
//	------------------------------------
?>
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lista_centro_costos" method="post">
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
<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />

<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
	<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
		<tr>
			<td align="right" width="125">Grupo C.C:</td>
	        <td>
	            <input type="checkbox" <?=$cgrupocc?> onclick="chkFiltro(this.checked, 'fgrupocc');" />
	            <select name="fgrupocc" id="fgrupocc" style="width:200px;" onchange="getOptionsSelect(this.value, 'subgrupocc', 'fsubgrupocc', 200, 1);" <?=$dgrupocc?>>
	                <option value="">&nbsp;</option>
	                <?=loadSelect("ac_grupocentrocosto", "CodGrupoCentroCosto", "Descripcion", $fgrupocc, 0)?>
	            </select>
			</td>
			<td align="right" width="125">Estado Reg.:</td>
			<td>
	            <input type="checkbox" <?=$cedoreg?> onclick="chkFiltro(this.checked, 'fedoreg');" />
	            <select name="fedoreg" id="fedoreg" style="width:100px;" <?=$dedoreg?>>
	                <option value="">&nbsp;</option>
	                <?=loadSelectGeneral("ESTADO", $fedoreg, 0)?>
	            </select>
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td align="right">Sub-Grupo C.C:</td>
	        <td>
	            <input type="checkbox" <?=$csubgrupocc?> onclick="chkFiltro(this.checked, 'fsubgrupocc');" />
	            <select name="fsubgrupocc" id="fsubgrupocc" style="width:200px;" <?=$dsubgrupocc?>>
	            	<option value="">&nbsp;</option>
	                <?=loadSelectDependiente("ac_subgrupocentrocosto", "CodSubGrupoCentroCosto", "Descripcion", "CodGrupoCentroCosto", $fsubgrupocc, $fgrupocc, 0)?>
	            </select>
			</td>
			<td align="right">Buscar:</td>
	        <td>
	            <input type="checkbox" <?=$cbuscar?> onclick="chkFiltro(this.checked, 'fbuscar');" />
	            <input type="text" name="fbuscar" id="fbuscar" style="width:200px;" value="<?=$fbuscar?>" <?=$dbuscar?> />
			</td>
			<td align="right"><input type="submit" value="Buscar"></td>
		</tr>
	</table>
</div>
<div class="sep"></div>

<center>
<div style="overflow:scroll; height:260px; width:100%; min-width:<?=$_width?>px;">
<table class="tblLista" style="width:100%; min-width:<?=$_width?>px;">
	<thead>
		<tr>
			<th width="50">C.Costo</th>
			<th>Descripci&oacute;n</th>
			<th width="200">Sub-Grupo</th>
			<th width="100">Abreviatura</th>
	    </tr>
    </thead>
    
    <tbody>
	<?php
	//	consulto todos
	$sql = "SELECT
				cc.*,
				gcc.Descripcion AS NomGrupoCentroCosto,
				sgcc.Descripcion AS NomSubGrupoCentroCosto
			FROM
				ac_mastcentrocosto cc
				INNER JOIN ac_grupocentrocosto gcc ON (cc.CodGrupoCentroCosto = gcc.CodGrupoCentroCosto)
				INNER JOIN ac_subgrupocentrocosto sgcc ON (cc.CodGrupoCentroCosto = sgcc.CodGrupoCentroCosto AND
														   cc.CodSubGrupoCentroCosto = sgcc.CodSubGrupoCentroCosto)
				INNER JOIN mastdependencias d ON (d.CodDependencia = cc.CodDependencia)
			WHERE 1 $filtro";
	$rows_total = getNumRows3($sql);
	//	consulto lista
	$sql = "SELECT
				cc.*,
				gcc.Descripcion AS NomGrupoCentroCosto,
				sgcc.Descripcion AS NomSubGrupoCentroCosto
			FROM
				ac_mastcentrocosto cc
				INNER JOIN ac_grupocentrocosto gcc ON (cc.CodGrupoCentroCosto = gcc.CodGrupoCentroCosto)
				INNER JOIN ac_subgrupocentrocosto sgcc ON (cc.CodGrupoCentroCosto = sgcc.CodGrupoCentroCosto AND
														   cc.CodSubGrupoCentroCosto = sgcc.CodSubGrupoCentroCosto)
				INNER JOIN mastdependencias d ON (d.CodDependencia = cc.CodDependencia)
			WHERE 1 $filtro
			ORDER BY CodCentroCosto
			LIMIT ".intval($limit).", ".intval($maxlimit);
	$field = getRecords($sql);
	$rows_lista = count($field);
	foreach($field as $f) {
		$id = "$f[CodCentroCosto]";
		if ($grupo != $f['CodGrupoCentroCosto']) {
			$grupo = $f['CodGrupoCentroCosto'];
			?>
            <tr class="trListaBody2">
                <td colspan="2"><?=$f['NomGrupoCentroCosto']?></td>
            </tr>
            <?php
		}
		if ($ventana == 'codigo') 
		{
			?><tr class="trListaBody" onClick="selLista(['<?=$f['CodCentroCosto']?>','<?=$f['Codigo']?>'], ['<?=$campo1?>','<?=$campo2?>']);"><?php
		}
		elseif ($ventana == 'abreviatura') 
		{
			?><tr class="trListaBody" onClick="selLista(['<?=$f['CodCentroCosto']?>','<?=$f['Abreviatura']?>'], ['<?=$campo1?>','<?=$campo2?>']);"><?php
		}
		elseif ($ventana == "selListadoListaParent") 
		{
			?><tr class="trListaBody" onclick="<?=$ventana?>('<?=$seldetalle?>',['<?=$campo1?>','<?=$campo2?>'],['<?=$f['CodCentroCosto']?>','<?=$f['Codigo']?>']);" id="<?=$f['CodCentroCosto']?>"><?php
		}
		else 
		{
			?><tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['CodCentroCosto']?>','<?=$f['Descripcion']?>','<?=$f['Codigo']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>']);"><?php
		}
		?>
			<td align="center"><?=$f['Codigo']?></td>
			<td><?=htmlentities($f['Descripcion'])?></td>
			<td><?=htmlentities($f['NomSubGrupoCentroCosto'])?></td>
			<td><?=$f['Abreviatura']?></td>
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