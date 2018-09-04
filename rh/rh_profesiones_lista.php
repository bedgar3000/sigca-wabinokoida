<?php
//	------------------------------------
if ($filtrar == "default") {
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "NomArea,NomGradoInstruccion,Descripcion";
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (p.CodProfesion LIKE '%".$fBuscar."%' OR
					  p.Descripcion LIKE '%".$fBuscar."%' OR
					  gi.Descripcion LIKE '%".$fBuscar."%' OR
					  md.Descripcion LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (p.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fCodGradoInstruccion != "") { $cCodGradoInstruccion = "checked"; $filtro.=" AND (p.CodGradoInstruccion = '".$fCodGradoInstruccion."')"; } else $dCodGradoInstruccion = "disabled";
if ($fArea != "") { $cArea = "checked"; $filtro.=" AND (p.Area = '".$fArea."')"; } else $dArea = "disabled";
//	------------------------------------
$_width = 900;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo">Profesiones</td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=rh_profesiones_lista" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />

<!--FILTRO-->
<div class="divBorder" style="width:<?=$_width?>px;">
<table width="<?=$_width?>" class="tblFiltro">
	<tr>
		<td align="right">Grado de Instrucci&oacute;n:</td>
		<td>
            <input type="checkbox" <?=$cCodGradoInstruccion?> onclick="chkFiltro(this.checked, 'fCodGradoInstruccion');" />
            <select name="fCodGradoInstruccion" id="fCodGradoInstruccion" style="width:225px;" <?=$dCodGradoInstruccion?>>
                <option value="">&nbsp;</option>
                <?=loadSelect2("rh_gradoinstruccion", "CodGradoInstruccion", "Descripcion", $fCodGradoInstruccion, 0)?>
            </select>
		</td>
		<td align="right">Buscar:</td>
		<td>
			<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
			<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:215px;" <?=$dBuscar?> />
		</td>
	</tr>
	<tr>
		<td align="right">Area:</td>
		<td>
            <input type="checkbox" <?=$cArea?> onclick="chkFiltro(this.checked, 'fArea');" />
            <select name="fArea" id="fArea" style="width:225px;" <?=$dArea?>>
                <option value="">&nbsp;</option>
                <?=getMiscelaneos($fArea, "AREA", 0)?>
            </select>
		</td>
		<td align="right">Estado: </td>
		<td>
            <input type="checkbox" <?=$cEstado?> onclick="chkFiltro(this.checked, 'fEstado');" />
            <select name="fEstado" id="fEstado" style="width:100px;" <?=$dEstado?>>
                <option value="">&nbsp;</option>
                <?=loadSelectGeneral("ESTADO", $fEstado, 0)?>
            </select>
        </td>
	</tr>
</table>
</div>
<center><input type="submit" value="Buscar"></center><br />

<!--REGISTROS-->
<center>
<input type="hidden" name="sel_registros" id="sel_registros" />
<table width="<?=$_width?>" class="tblBotones">
    <tr>
        <td><div id="rows"></div></td>
        <td align="right">
            <input type="button" id="btNuevo" value="Nuevo" style="width:75px; <?=$btNuevo?>" onclick="cargarPagina(this.form, 'gehen.php?anz=rh_profesiones_form&opcion=nuevo');" />
            
            <input type="button" id="btModificar" value="Modificar" style="width:75px; <?=$btModificar?>" onclick="cargarOpcion2(this.form, 'gehen.php?anz=rh_profesiones_form&opcion=modificar', 'SELF', '', $('#sel_registros').val());" />
            
            <input type="button" id="btEliminar" value="Eliminar" style="width:75px; <?=$btEliminar?>" onclick="opcionRegistro2(this.form, $('#sel_registros').val(), 'profesiones', 'eliminar');" />
            
            <input type="button" id="btVer" value="Ver" style="width:75px; <?=$btVer?>" onclick="cargarOpcion2(this.form, 'gehen.php?anz=rh_profesiones_form&opcion=ver', 'SELF', '', $('#sel_registros').val());" />
        </td>
    </tr>
</table>

<div style="overflow:scroll; width:<?=$_width?>px; height:300px;">
<table width="1300" class="tblLista">
	<thead>
    <tr>
        <th scope="col" width="50" onclick="order('CodProfesion')">Cod.</th>
        <th scope="col" align="left" onclick="order('Descripcion')">Descripci&oacute;n</th>
        <th scope="col" width="225" align="left" onclick="order('NomGradoInstruccion,NomArea,Descripcion')">Grado de Instrucci&oacute;n</th>
        <th scope="col" width="350" align="left" onclick="order('NomArea,NomGradoInstruccion,Descripcion')">Area de Instrucci&oacute;n</th>
        <th scope="col" width="75" onclick="order('Estado')">Estado</th>
    </tr>
    </thead>
    
    <tbody id="lista_registros">
	<?php
	//	consulto todos
	$sql = "SELECT p.CodProfesion
			FROM
				rh_profesiones p
				INNER JOIN rh_gradoinstruccion gi ON (gi.CodGradoInstruccion = p.CodGradoInstruccion)
				LEFT JOIN mastmiscelaneosdet md ON (md.CodDetalle = p.Area AND
													md.CodMaestro = 'AREA' AND
													md.CodAplicacion = 'RH')
			WHERE 1 $filtro";
	$rows_total = getNumRows3($sql);
	
	//	consulto lista
	$sql = "SELECT
				p.CodProfesion,
				p.Descripcion,
				p.Estado,
				gi.Descripcion AS NomGradoInstruccion,
				md.Descripcion AS NomArea
			FROM
				rh_profesiones p
				INNER JOIN rh_gradoinstruccion gi ON (gi.CodGradoInstruccion = p.CodGradoInstruccion)
				LEFT JOIN mastmiscelaneosdet md ON (md.CodDetalle = p.Area AND
													md.CodMaestro = 'AREA' AND
													md.CodAplicacion = 'RH')
			WHERE 1 $filtro
			ORDER BY $fOrderBy
			LIMIT ".intval($limit).", ".intval($maxlimit);
	$field = getRecords($sql);
	$rows_lista = count($field);
	foreach($field as $f) {
		$id = "$f[CodProfesion]";
		?>
		<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
			<td align="center"><?=$f['CodProfesion']?></td>
			<td><?=htmlentities($f['Descripcion'])?></td>
			<td><?=htmlentities($f['NomGradoInstruccion'])?></td>
			<td><?=htmlentities($f['NomArea'])?></td>
			<td align="center"><?=printValoresGeneral("ESTADO", $f['Estado'])?></td>
		</tr>
		<?php
	}
	?>
    </tbody>
</table>
</div>
<table width="<?=$_width?>">

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