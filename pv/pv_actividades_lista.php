<?php
//	------------------------------------
if ($filtrar == "default") {
	$fEstado = 'A';
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "CodSector,CodSubSector,CodPrograma,CodSubPrograma,CodProyecto,CodActividad";
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (a.IdActividad LIKE '%".$fBuscar."%' OR
					  a.Denominacion LIKE '%".$fBuscar."%' OR
					  a.CodActividad LIKE '%".$fBuscar."%' OR
					  s.Denominacion LIKE '%".$fBuscar."%' OR
					  ss.Denominacion LIKE '%".$fBuscar."%' OR
					  p.Denominacion LIKE '%".$fBuscar."%' OR
					  sp.Denominacion LIKE '%".$fBuscar."%' OR
					  py.Denominacion LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (py.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fCodSector != "") { $cCodSector = "checked"; $filtro.=" AND (ss.CodSector = '".$fCodSector."')"; } else $dCodSector = "disabled";
if ($fIdSubSector != "") { $cIdSubSector = "checked"; $filtro.=" AND (p.IdSubSector = '".$fIdSubSector."')"; } else $dIdSubSector = "disabled";
if ($fIdPrograma != "") { $cIdPrograma = "checked"; $filtro.=" AND (sp.IdPrograma = '".$fIdPrograma."')"; } else $dIdPrograma = "disabled";
if ($fIdSubPrograma != "") { $cIdSubPrograma = "checked"; $filtro.=" AND (py.IdSubPrograma = '".$fIdSubPrograma."')"; } else $dIdSubPrograma = "disabled";
if ($fIdProyecto != "") { $cIdProyecto = "checked"; $filtro.=" AND (py.IdProyecto = '".$fIdProyecto."')"; } else $dIdProyecto = "disabled";
//	------------------------------------
$_titulo = "Actividades";
$_width = 700;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=pv_actividades_lista" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />

<!--FILTRO-->
<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
	<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
		<tr>
			<td align="right">Sub-Sector:</td>
			<td>
				<input type="checkbox" <?=$cIdSubSector?> onclick="chkCampos(this.checked, 'fIdSubSector');" onChange="loadSelect($('#fIdPrograma'), 'tabla=pv_programas&IdSubSector='+$('#fIdSubSector').val(), 1, ['fIdSubPrograma','fIdProyecto']);" />
				<select name="fIdSubSector" id="fIdSubSector" style="width:225px;" <?=$dIdSubSector?> onChange="loadSelect($('#fIdPrograma'), 'tabla=pv_programas&IdSubSector='+$('#fIdSubSector').val(), 1, ['fIdSubPrograma','fIdProyecto']);">
					<option value="">&nbsp;</option>
					<?=loadSelect2('pv_subsector','IdSubSector','Denominacion',$fIdSubSector,0,NULL,NULL,'CodClaSectorial')?>
				</select>
			</td>
			<td align="right" width="100">Sub-Programa:</td>
			<td>
				<input type="checkbox" <?=$cIdSubPrograma?> onclick="chkCampos(this.checked, 'fIdSubPrograma');" />
				<select name="fIdSubPrograma" id="fIdSubPrograma" style="width:225px;" <?=$dIdSubPrograma?> onChange="loadSelect($('#fIdProyecto'), 'tabla=pv_proyectos&IdSubPrograma='+$('#fIdSubPrograma').val(), 1);">
					<option value="">&nbsp;</option>
					<?=loadSelect2('pv_subprogramas','IdSubPrograma','Denominacion',$fIdSubPrograma,0,['IdPrograma'],[$fIdPrograma],'CodSubPrograma')?>
				</select>
			</td>
			<td align="right" width="100">Buscar:</td>
			<td>
				<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
				<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:95px;" <?=$dBuscar?> />
			</td>
	        <td>&nbsp;</td>
		</tr>
		<tr>
			<td align="right">Programa:</td>
			<td>
				<input type="checkbox" <?=$cIdPrograma?> onclick="chkCampos(this.checked, 'fIdPrograma');" onChange="loadSelect($('#fIdSubPrograma'), 'tabla=pv_subprogramas&IdPrograma='+$('#fIdPrograma').val(), 1, ['fIdProyecto']);" />
				<select name="fIdPrograma" id="fIdPrograma" style="width:225px;" <?=$dIdPrograma?> onChange="loadSelect($('#fIdSubPrograma'), 'tabla=pv_subprogramas&IdPrograma='+$('#fIdPrograma').val(), 1, ['fIdProyecto']);">
					<option value="">&nbsp;</option>
					<?=loadSelect2('pv_programas','IdPrograma','Denominacion',$fIdPrograma,0,['IdSubSector'],[$fIdSubSector],'CodPrograma')?>
				</select>
			</td>
			<td align="right">Proyecto:</td>
			<td>
				<input type="checkbox" <?=$cIdProyecto?> onclick="chkCampos(this.checked, 'fIdProyecto');" />
				<select name="fIdProyecto" id="fIdProyecto" style="width:225px;" <?=$dIdProyecto?>>
					<option value="">&nbsp;</option>
					<?=loadSelect2('pv_proyectos','IdProyecto','Denominacion',$fIdProyecto,0,['IdSubPrograma'],[$fIdSubPrograma],'CodProyecto')?>
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
	        <td align="right"><input type="submit" value="Buscar"></td>
		</tr>
	</table>
</div>
<div class="sep"></div>

<!--REGISTROS-->
<center>
<input type="hidden" name="sel_registros" id="sel_registros" />
<table class="tblBotones" style="width:100%; min-width:<?=$_width?>px;">
    <tr>
        <td><div id="rows"></div></td>
        <td align="right">
            <input type="button" value="Nuevo" style="width:75px;" class="insert" onclick="cargarPagina(this.form, 'gehen.php?anz=pv_actividades_form&opcion=nuevo');" />
            <input type="button" value="Modificar" style="width:75px;" class="update" onclick="cargarOpcion2(this.form, 'gehen.php?anz=pv_actividades_form&opcion=modificar', 'SELF', '', $('#sel_registros').val());" />
            <input type="button" value="Eliminar" style="width:75px;" class="delete" onclick="opcionRegistro3(this.form, $('#sel_registros').val(), 'formulario', 'eliminar', 'pv_actividades_ajax.php');" />
            <input type="button" value="Ver" style="width:75px;" class="ver" onclick="cargarOpcion2(this.form, 'gehen.php?anz=pv_actividades_form&opcion=ver', 'SELF', '', $('#sel_registros').val());" />
        </td>
    </tr>
</table>

<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px;">
	<table class="tblLista" style="width:100%; min-width:2600px;">
		<thead>
	    <tr>
	        <th width="75" onclick="order('IdActividad')">Id.</th>
	        <th width="75" onclick="order('CodActividad')">C&oacute;digo</th>
	        <th align="left" onclick="order('Denominacion')">Denominaci&oacute;n</th>
	        <th width="300" align="left" onclick="order('Sector,SubSector,Programa,SubPrograma,Proyecto,Denominacion')">Sector</th>
	        <th width="300" align="left" onclick="order('SubSector,Programa,SubPrograma,Proyecto,Denominacion')">Sub-Sector</th>
	        <th width="300" align="left" onclick="order('Programa,SubPrograma,Proyecto,Denominacion')">Programa</th>
	        <th width="300" align="left" onclick="order('SubPrograma,Proyecto,Denominacion')">Sub-Programa</th>
	        <th width="300" align="left" onclick="order('Proyecto,Denominacion')">Proyecto</th>
	        <th width="50" onclick="order('FlagObra')">Obra</th>
	        <th width="75" onclick="order('Estado')">Estado</th>
	    </tr>
	    </thead>
	    
	    <tbody id="lista_registros">
		<?php
		//	consulto todos
		$sql = "SELECT a.*
				FROM pv_actividades a
				INNER JOIN pv_proyectos py ON (py.IdProyecto = a.IdProyecto)
				INNER JOIN pv_subprogramas sp ON (sp.IdSubPrograma = py.IdSubPrograma)
				INNER JOIN pv_programas p ON (p.IdPrograma = sp.IdPrograma)
				INNER JOIN pv_subsector ss ON (ss.IdSubSector = p.IdSubSector)
				INNER JOIN pv_sector s ON (s.CodSector = ss.CodSector)
				WHERE 1 $filtro";
		$rows_total = getNumRows3($sql);
		//	consulto lista
		$sql = "SELECT
					a.*,
					s.Denominacion AS Sector,
					s.CodSector,
					ss.Denominacion AS SubSector,
					ss.CodSubSector,
					p.Denominacion AS Programa,
					p.CodPrograma,
					sp.Denominacion AS SubPrograma,
					sp.CodSubPrograma,
					py.Denominacion AS Proyecto,
					py.CodProyecto
				FROM pv_actividades a
				INNER JOIN pv_proyectos py ON (py.IdProyecto = a.IdProyecto)
				INNER JOIN pv_subprogramas sp ON (sp.IdSubPrograma = py.IdSubPrograma)
				INNER JOIN pv_programas p ON (p.IdPrograma = sp.IdPrograma)
				INNER JOIN pv_subsector ss ON (ss.IdSubSector = p.IdSubSector)
				INNER JOIN pv_sector s ON (s.CodSector = ss.CodSector)
				WHERE 1 $filtro
				ORDER BY $fOrderBy
				LIMIT ".intval($limit).", ".intval($maxlimit);
		$field = getRecords($sql);
		$rows_lista = count($field);
		foreach($field as $f) {
			$id = $f['IdActividad'];
			?>
			<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
				<td align="center"><?=$f['IdActividad']?></td>
				<td align="center"><?=$f['CodActividad']?></td>
				<td><?=htmlentities($f['Denominacion'])?></td>
				<td><?=$f['CodSector']?>-<?=htmlentities($f['Sector'])?></td>
				<td><?=$f['CodSubSector']?>-<?=htmlentities($f['SubSector'])?></td>
				<td><?=$f['CodPrograma']?>-<?=htmlentities($f['Programa'])?></td>
				<td><?=$f['CodSubPrograma']?>-<?=htmlentities($f['SubPrograma'])?></td>
				<td><?=$f['CodProyecto']?>-<?=htmlentities($f['Proyecto'])?></td>
				<td align="center"><?=printFlag($f['FlagObra'])?></td>
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
</center>
</form>