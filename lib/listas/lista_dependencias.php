<?php
$flagOrganismo = (isset($flagOrganismo)?$flagOrganismo:'N');
//	------------------------------------
if ($filtrar == "default") {
	$fEstado = "A";
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "Organismo,Dependencia";
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (d.CodDependencia LIKE '%".$fBuscar."%' OR
					  d.Dependencia LIKE '%".$fBuscar."%' OR
					  o.Organismo LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (d.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (d.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
//	------------------------------------
?>
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lista_dependencias" method="post">
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
		<td align="right" width="100">Organismo:</td>
		<td>
			<?php
			if ($flagOrganismo == 'S') {
				?>
	            <input type="checkbox" <?=$cCodOrganismo?> onclick="this.checked=!this.checked;" />
	            <select name="fCodOrganismo" id="fCodOrganismo" style="width:256px;" <?=$dCodOrganismo?>>
	                <?=loadSelect2("mastorganismos","CodOrganismo","Organismo",$fCodOrganismo,1)?>
	            </select>
				<?php
			} else {
				?>
	            <input type="checkbox" <?=$cCodOrganismo?> onclick="chkFiltro(this.checked, 'fCodOrganismo');" />
	            <select name="fCodOrganismo" id="fCodOrganismo" style="width:256px;" <?=$dCodOrganismo?>>
	                <option value="">&nbsp;</option>
	                <?=loadSelect2("mastorganismos","CodOrganismo","Organismo",$fCodOrganismo)?>
	            </select>
				<?php
			}
			?>
		</td>
		<td align="right" width="100">Estado: </td>
		<td>
            <input type="checkbox" <?=$cEstado?> onclick="chkFiltro(this.checked, 'fEstado');" />
            <select name="fEstado" id="fEstado" style="width:125px;" <?=$dEstado?>>
                <option value="">&nbsp;</option>
                <?=loadSelectGeneral("ESTADO", $fEstado, 0)?>
            </select>
		</td>
        <td>&nbsp;</td>
	</tr>
	<tr>
		<td align="right">Buscar:</td>
		<td colspan="3">
			<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
			<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:250px;" <?=$dBuscar?> />
		</td>
        <td align="right"><input type="submit" value="Buscar"></td>
	</tr>
</table>
</div>
<div class="sep"></div>

<center>
<div style="overflow:scroll; height:315px; width:100%; min-width:<?=$_width?>px;">
<table class="tblLista" style="width:100%; min-width:1700px;">
	<thead>
	<tr>
        <th width="75" onclick="order('CodDependencia')">C&oacute;digo</th>
        <th align="left" onclick="order('Dependencia')">Descripci&oacute;n</th>
        <th align="left" onclick="order('Organismo,Dependencia')">Organismo</th>
        <th width="90" onclick="order('Estado')">Estado</th>
    </tr>
    </thead>
    
    <tbody>
	<?php
	//	consulto todos
	$sql = "SELECT d.CodDependencia
			FROM
				mastdependencias d
				INNER JOIN mastorganismos o ON (o.CodOrganismo = d.CodOrganismo)
			WHERE 1 $filtro";
	$rows_total = getNumRows3($sql);
	//	consulto lista
	$sql = "SELECT
				d.*,
				o.Organismo
			FROM
				mastdependencias d
				INNER JOIN mastorganismos o ON (o.CodOrganismo = d.CodOrganismo)
			WHERE 1 $filtro
			ORDER BY $fOrderBy
			LIMIT ".intval($limit).", ".intval($maxlimit);
	$field = getRecords($sql);
	$rows_lista = count($field);
	foreach($field as $f) {
		if ($ventana == "listado_insertar_linea") {
			?>
        	<tr class="trListaBody" onclick="listado_insertar_linea('<?=$detalle?>', 'modulo=<?=$modulo?>&accion=<?=$accion?>&CodDependencia=<?=$f['CodDependencia']?>', '<?=$f['CodDependencia']?>', '<?=$url?>');">
        	<?php
		}
		elseif ($ventana == "dependencias") {
			?>
        	<tr class="trListaBody" onClick="selLista(['<?=$f['EntidadPadre']?>','<?=$f['EstructuraPadre']?>','<?=$f['Dependencia']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>']);">
        	<?php
		}
		elseif ($ventana == "at_prestamos") {
			?>
        	<tr class="trListaBody" onClick="sellista_at_prestamos(['<?=$f['CodDependencia']?>','<?=$f['Dependencia']?>'], ['<?=$campo1?>','<?=$campo2?>']);">
        	<?php
		}
		else {
			?>
        	<tr class="trListaBody" onClick="selLista(['<?=$f['CodDependencia']?>','<?=$f['Dependencia']?>'], ['<?=$campo1?>','<?=$campo2?>']);">
        	<?php
		}
		?>
			<td align="center"><?=$f['CodDependencia']?></td>
			<td><?=htmlentities($f['Dependencia'])?></td>
			<td><?=htmlentities($f['Organismo'])?></td>
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

<script type="text/javascript" language="javascript">
<?php
if ($ventana == "at_prestamos") {
	?>
	function sellista_at_prestamos(valores, inputs) {
		if (inputs) {
			for(var i=0; i<inputs.length; i++) {
				if (parent.$("#"+inputs[i]).length > 0) parent.$("#"+inputs[i]).val(valores[i]);
			}
		}
		parent.$('#lista_activos').html('');
		parent.$.prettyPhoto.close();
	}
	<?php
}
?>
</script>