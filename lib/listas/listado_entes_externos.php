<?php
//	------------------------------------
if ($filtrar == "default") {
	$fOrderBy = "CodOrganismo,CodDependencia";
	$maxlimit = $_SESSION["MAXLIMIT"];
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro1.=" AND (oe.CodOrganismo LIKE '%".$fBuscar."%' OR
					 oe.Organismo LIKE '%".$fBuscar."%')";
	$filtro2.=" AND (oe.CodOrganismo LIKE '%".$fBuscar."%' OR
					 oe.Organismo LIKE '%".$fBuscar."%' OR
					 de.CodDependencia LIKE '%".$fBuscar."%' OR
					 de.Dependencia LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
//	------------------------------------
$_width = 500;
?>
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=listado_entes_externos" method="post">
<input type="hidden" name="campo1" id="campo1" value="<?=$campo1?>" />
<input type="hidden" name="campo2" id="campo2" value="<?=$campo2?>" />
<input type="hidden" name="campo3" id="campo3" value="<?=$campo3?>" />
<input type="hidden" name="campo4" id="campo4" value="<?=$campo4?>" />
<input type="hidden" name="ventana" id="ventana" value="<?=$ventana?>" />
<input type="hidden" name="seldetalle" id="seldetalle" value="<?=$seldetalle?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />

<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
	<tr>
		<td align="right" width="125">Buscar:</td>
		<td>
			<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
			<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:75%;" <?=$dBuscar?> />
		</td>
        <td align="right"><input type="submit" value="Buscar"></td>
	</tr>
</table>
</div><br />

<center>
<table class="tblBotones" style="width:100%; min-width:<?=$_width?>px;">
	<tr>
		<td><div id="rows"></div></td>
	</tr>
</table>

<div style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:310px;">
<table class="tblLista" style="width:100%; min-width:<?=$_width?>px;">
	<thead>
    <tr>
        <th scope="col" width="60" onclick="order('CodOrganismo,CodDependencia')">C&oacute;digo</th>
        <th scope="col" align="left" onclick="order('Organismo,Dependencia')">Descripci&oacute;n</th>
    </tr>
    </thead>
    
    <tbody id="lista_registros">
	<?php
	//	consulto todos
	$sql = "(SELECT
				oe.CodOrganismo,
				oe.Organismo,
				'' AS CodDependencia,
				'' AS Dependencia,
				oe.Estado,
				'Organismo' AS Tipo
			 FROM pf_organismosexternos oe
			 WHERE oe.Estado = 'A' AND oe.FlagSujetoControl = 'S' $filtro1 $filtro)
			UNION
			(SELECT
				oe.CodOrganismo,
				oe.Organismo,
				de.CodDependencia,
				de.Dependencia,
				de.Estado,
				'Dependencia' AS Tipo
			 FROM
			 	pf_dependenciasexternas de
				INNER JOIN pf_organismosexternos oe ON (de.CodOrganismo = oe.CodOrganismo)
			 WHERE oe.Estado = 'A' AND de.Estado = 'A' AND oe.FlagSujetoControl = 'S' $filtro2 $filtro)";
	$rows_total = getNumRows3($sql);
	
	//	consulto lista
	$sql = "(SELECT
				oe.CodOrganismo,
				oe.Organismo,
				'' AS CodDependencia,
				'' AS Dependencia,
				oe.Estado,
				'Organismo' AS Tipo
			 FROM pf_organismosexternos oe
			 WHERE oe.Estado = 'A' AND oe.FlagSujetoControl = 'S' $filtro1 $filtro)
			UNION
			(SELECT
				oe.CodOrganismo,
				oe.Organismo,
				de.CodDependencia,
				de.Dependencia,
				de.Estado,
				'Dependencia' AS Tipo
			 FROM
			 	pf_dependenciasexternas de
				INNER JOIN pf_organismosexternos oe ON (de.CodOrganismo = oe.CodOrganismo)
			 WHERE oe.Estado = 'A' AND de.Estado = 'A' AND oe.FlagSujetoControl = 'S' $filtro2 $filtro)
			ORDER BY $fOrderBy
			LIMIT ".intval($limit).", ".intval($maxlimit);
	$field = getRecords($sql);
	$rows_lista = count($field);
	foreach($field as $f) {
		$id = $f['CodOrganismo'].$f['CodDependencia'];
		##
		if ($f['Tipo'] == 'Organismo') {
			?>
			<tr class="trListaBody" onclick="selLista(['<?=$f['CodOrganismo']?>','<?=htmlentities($f["Organismo"])?>','<?=$f['CodDependencia']?>','<?=htmlentities($f["Dependencia"])?>'],['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>','<?=$campo4?>']);" id="<?=$id?>">
				<td align="center"><strong><?=$f['CodOrganismo']?></strong></td>
				<td><strong><?=htmlentities($f['Organismo'])?></strong></td>
			</tr>
			<?php
		}
		elseif ($f['Tipo'] == 'Dependencia') {
			?>
			<tr class="trListaBody" onclick="selLista(['<?=$f['CodOrganismo']?>','<?=htmlentities($f["Organismo"])?>','<?=$f['CodDependencia']?>','<?=htmlentities($f["Dependencia"])?>'],['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>','<?=$campo4?>']);" id="<?=$id?>">
				<td align="center"><?=$f['CodDependencia']?></td>
				<td><?=htmlentities($f['Dependencia'])?></td>
			</tr>
			<?php
		}
	}
	?>
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