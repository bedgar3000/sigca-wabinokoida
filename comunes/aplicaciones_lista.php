<?php
if ($filtrar == "default") {
	$fedoreg = "A";
	$maxlimit = $_SESSION["MAXLIMIT"];
}
if ($fedoreg != "") { $cedoreg = "checked"; $filtro.=" AND (a.Estado = '".$fedoreg."')"; } else $dedoreg = "disabled";
if ($fbuscar != "") {
	$cbuscar = "checked";
	$filtro.=" AND (a.CodAplicacion LIKE '%".$fbuscar."%' OR 
					a.Descripcion LIKE '%".$fbuscar."%' OR 
					a.PeriodoContable LIKE '%".$fbuscar."%')";
} else $dbuscar = "disabled";
//	------------------------------------
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo">Maestro de Aplicaciones</td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=aplicaciones_lista" method="post">
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="registro" id="registro" />
<div class="divBorder" style="width:700px;">
<table width="700" class="tblFiltro">
	<tr>
		<td align="right" width="125">Estado Reg.:</td>
		<td>
            <input type="checkbox" <?=$cedoreg?> onclick="chkFiltro(this.checked, 'fedoreg');" />
            <select name="fedoreg" id="fedoreg" style="width:100px;" <?=$dedoreg?>>
                <option value="">&nbsp;</option>
                <?=loadSelectGeneral("ESTADO", $fedoreg, 0)?>
            </select>
		</td>
		<td align="right" width="125">Buscar:</td>
        <td>
            <input type="checkbox" <?=$cbuscar?> onclick="chkFiltro(this.checked, 'fbuscar');" />
            <input type="text" name="fbuscar" id="fbuscar" style="width:200px;" value="<?=$fbuscar?>" <?=$dbuscar?> />
		</td>
	</tr>
</table>
</div>
<center><input type="submit" value="Buscar"></center><br />

<center>
<table width="700" class="tblBotones">
	<tr>
		<td><div id="rows"></div></td>
		<td align="right">
			<input type="button" class="btLista" id="btNuevo" value="Nuevo" onclick="cargarPagina(this.form, 'gehen.php?anz=aplicaciones_form&opcion=nuevo');" />
            
			<input type="button" class="btLista" id="btModificar" value="Modificar" onclick="cargarOpcion2(this.form, 'gehen.php?anz=aplicaciones_form&opcion=modificar', 'SELF', '', $('#registro').val());" />
            
			<input type="button" class="btLista" id="btEliminar" value="Eliminar" onclick="opcionRegistro2(this.form, this.form.registro.value, 'aplicaciones', 'eliminar');" />
            
			<input type="button" class="btLista" id="btVer" value="Ver" onclick="cargarOpcion2(this.form, 'gehen.php?anz=aplicaciones_form&opcion=ver', 'SELF', '', $('#registro').val());" />
		</td>
	</tr>
</table>

<div style="overflow:scroll; width:700px; height:300px;">
<table width="100%" class="tblLista">
	<thead>
	<tr>
		<th scope="col" width="75">Aplicaci&oacute;n</th>
		<th scope="col">Descripci&oacute;n</th>
		<th scope="col" width="75">Periodo Contable</th>
		<th scope="col" width="75">Estado</th>
	</tr>
    </thead>
    
    <tbody>
	<?php
	//	consulto todos	
	$sql = "SELECT a.*
			FROM mastaplicaciones a
			WHERE 1 $filtro";
	$query = mysql_query($sql) or die ($sql.mysql_error());
	$rows_total = mysql_num_rows($query);
	
	//	consulto lista
	$sql = "SELECT a.*
			FROM mastaplicaciones a
			WHERE 1 $filtro
			ORDER BY CodAplicacion
			LIMIT ".intval($limit).", $maxlimit";
	$query = mysql_query($sql) or die ($sql.mysql_error());
	$rows_lista = mysql_num_rows($query);
	while ($field = mysql_fetch_array($query)) {
		?>
		<tr class="trListaBody" onclick="mClk(this, 'registro');" id="<?=$field['CodAplicacion']?>">
			<td align="center"><?=$field['CodAplicacion']?></td>
			<td><?=($field['Descripcion'])?></td>
			<td align="center"><?=$field['PeriodoContable']?></td>
			<td align="center"><?=printValoresGeneral("ESTADO", $field['Estado'])?></td>
		</tr>
		<?php
	}
	?>
    </tbody>
</table>
</div>
<table width="700">
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