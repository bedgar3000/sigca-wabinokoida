<?php
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$fEstado = 'A';
}
//	------------------------------------
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (cp.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fCodDependencia != "") { $cCodDependencia = "checked"; $filtro.=" AND (ued.CodDependencia = '".$fCodDependencia."')"; } else $dCodDependencia = "disabled";
if ($fCodUnidadEjec != "") { $cCodUnidadEjec = "checked"; $filtro.=" AND (cp.CodUnidadEjec = '".$fCodUnidadEjec."')"; } else $dCodUnidadEjec = "disabled";
if ($fIdSubSector != "") { $cIdSubSector = "checked"; $filtro.=" AND (pg.IdSubSector = '".$fIdSubSector."')"; } else $dIdSubSector = "disabled";
if ($fIdPrograma != "") { $cIdPrograma = "checked"; $filtro.=" AND (spg.IdPrograma = '".$fIdPrograma."')"; } else $dIdPrograma = "disabled";
if ($fIdSubPrograma != "") { $cIdSubPrograma = "checked"; $filtro.=" AND (py.IdSubPrograma = '".$fIdSubPrograma."')"; } else $dIdSubPrograma = "disabled";
if ($fIdProyecto != "") { $cIdProyecto = "checked"; $filtro.=" AND (a.IdProyecto = '".$fIdProyecto."')"; } else $dIdProyecto = "disabled";
if ($fIdActividad != "") { $cIdActividad = "checked"; $filtro.=" AND (cp.IdActividad = '".$fIdActividad."')"; } else $dIdActividad = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (cp.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
//	------------------------------------
$_titulo = "Categor&iacute;as Program&aacute;ticas";
$_width = 900;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=pv_categoriaprog_lista" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />

<!--FILTRO-->
<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
	<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
		<tr>
			<td align="right" width="125">Organismo:</td>
			<td>
				<input type="checkbox" <?=$cCodOrganismo?> onclick="this.checked=!this.checked;" />
				<select name="fCodOrganismo" id="fCodOrganismo" style="width:225px;" <?=$dCodOrganismo?> onChange="loadSelect($('#fCodUnidadEjec'), 'tabla=pv_unidadejecutora&CodOrganismo='+$(this).val(), 1); loadSelect($('#fCodDependencia'), 'tabla=dependencia_filtro&opcion='+$(this).val(), 1);">
					<?=getOrganismos($fCodOrganismo, 3);?>
				</select>
			</td>
			<td align="right">Sub-Sector:</td>
			<td>
				<input type="checkbox" <?=$cIdSubSector?> onclick="chkCampos(this.checked, 'fIdSubSector');" onChange="loadSelect($('#fIdPrograma'), 'tabla=pv_programas&IdSubSector='+$('#fIdSubSector').val(), 1, ['fIdSubPrograma','fIdProyecto','fIdActividad']);" />
				<select name="fIdSubSector" id="fIdSubSector" style="width:225px;" <?=$dIdSubSector?> onChange="loadSelect($('#fIdPrograma'), 'tabla=pv_programas&IdSubSector='+$('#fIdSubSector').val(), 1, ['fIdSubPrograma','fIdProyecto','fIdActividad']);">
					<option value="">&nbsp;</option>
					<?=loadSelect2('pv_subsector','IdSubSector','Denominacion',$fIdSubSector,0,NULL,NULL,'CodClaSectorial')?>
				</select>
			</td>
			<td align="right">Proyecto:</td>
			<td>
				<input type="checkbox" <?=$cIdProyecto?> onclick="chkCampos(this.checked, 'fIdProyecto');" onChange="loadSelect($('#fIdActividad'), 'tabla=pv_actividades&IdProyecto='+$('#fIdProyecto').val(), 1);" />
				<select name="fIdProyecto" id="fIdProyecto" style="width:225px;" <?=$dIdProyecto?> onChange="loadSelect($('#fIdActividad'), 'tabla=pv_actividades&IdProyecto='+$('#fIdProyecto').val(), 1);">
					<option value="">&nbsp;</option>
					<?=loadSelect2('pv_proyectos','IdProyecto','Denominacion',$fIdProyecto,0,['IdSubPrograma'],[$fIdSubPrograma],'CodProyecto')?>
				</select>
			</td>
	        <td>&nbsp;</td>
		</tr>
		<tr>
			<td align="right">Dependencia: </td>
			<td>
	            <input type="checkbox" <?=$cCodDependencia?> onclick="chkFiltro(this.checked, 'fCodDependencia');" />
				<select name="fCodDependencia" id="fCodDependencia" style="width:225px;" <?=$dCodDependencia?>>
					<option value="">&nbsp;</option>
					<?=getDependencias($fCodDependencia, $fCodOrganismo, 0);?>
				</select>
			</td>
			<td align="right">Programa:</td>
			<td>
				<input type="checkbox" <?=$cIdPrograma?> onclick="chkCampos(this.checked, 'fIdPrograma');" onChange="loadSelect($('#fIdSubPrograma'), 'tabla=pv_subprogramas&IdPrograma='+$('#fIdPrograma').val(), 1, ['fIdProyecto','fIdActividad']);" />
				<select name="fIdPrograma" id="fIdPrograma" style="width:225px;" <?=$dIdPrograma?> onChange="loadSelect($('#fIdSubPrograma'), 'tabla=pv_subprogramas&IdPrograma='+$('#fIdPrograma').val(), 1, ['fIdProyecto','fIdActividad']);">
					<option value="">&nbsp;</option>
					<?=loadSelect2('pv_programas','IdPrograma','Denominacion',$fIdPrograma,0,['IdSubSector'],[$fIdSubSector],'CodPrograma')?>
				</select>
			</td>
			<td align="right">Actividad:</td>
			<td>
				<input type="checkbox" <?=$cIdActividad?> onclick="chkCampos(this.checked, 'fIdActividad');" />
				<select name="fIdActividad" id="fIdActividad" style="width:225px;" <?=$dIdActividad?>>
					<option value="">&nbsp;</option>
					<?=loadSelect2('pv_actividades','IdActividad','Denominacion',$fIdActividad,0,['IdProyecto'],[$fIdProyecto],'CodActividad')?>
				</select>
			</td>
	        <td>&nbsp;</td>
		</tr>
		<tr>
			<td align="right">Unidad Ejecutora: </td>
			<td>
	            <input type="checkbox" <?=$cCodUnidadEjec?> onclick="chkFiltro(this.checked, 'fCodUnidadEjec');" />
				<select name="fCodUnidadEjec" id="fCodUnidadEjec" style="width:225px;" <?=$dCodUnidadEjec?>>
					<option value="">&nbsp;</option>
					<?=loadSelect2('pv_unidadejecutora','CodUnidadEjec','Denominacion',$fCodUnidadEjec,10,['CodOrganismo'],[$fCodOrganismo]);?>
				</select>
			</td>
			<td align="right" width="100">Sub-Programa:</td>
			<td>
				<input type="checkbox" <?=$cIdSubPrograma?> onclick="chkCampos(this.checked, 'fIdSubPrograma');" onChange="loadSelect($('#fIdProyecto'), 'tabla=pv_proyectos&IdSubPrograma='+$('#fIdSubPrograma').val(), 1, ['fIdActividad']);" />
				<select name="fIdSubPrograma" id="fIdSubPrograma" style="width:225px;" <?=$dIdSubPrograma?> onChange="loadSelect($('#fIdProyecto'), 'tabla=pv_proyectos&IdSubPrograma='+$('#fIdSubPrograma').val(), 1, ['fIdActividad']);">
					<option value="">&nbsp;</option>
					<?=loadSelect2('pv_subprogramas','IdSubPrograma','Denominacion',$fIdSubPrograma,0,['IdPrograma'],[$fIdPrograma],'CodSubPrograma')?>
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
            <input type="button" value="Nuevo" style="width:75px;" class="insert" onclick="cargarPagina(this.form, 'gehen.php?anz=pv_categoriaprog_form&opcion=nuevo');" />
            <input type="button" value="Modificar" style="width:75px;" class="update" onclick="cargarOpcion2(this.form, 'gehen.php?anz=pv_categoriaprog_form&opcion=modificar', 'SELF', '', $('#sel_registros').val());" />
            <input type="button" value="Eliminar" style="width:75px;" class="delete" onclick="opcionRegistro3(this.form, $('#sel_registros').val(), 'formulario', 'eliminar', 'pv_categoriaprog_ajax.php');" />
            <input type="button" value="Ver" style="width:75px;" class="ver" onclick="cargarOpcion2(this.form, 'gehen.php?anz=pv_categoriaprog_form&opcion=ver', 'SELF', '', $('#sel_registros').val());" />
        </td>
    </tr>
</table>

<div style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:300px;">
	<table class="tblLista" style="width:100%;">
		<thead>
		    <tr>
		        <th width="35">Sec.</th>
		        <th width="35">Pro.</th>
		        <th width="35">SubP.</th>
		        <th width="35">Proy.</th>
		        <th width="35">Act.</th>
		        <th align="left">Denominaci&oacute;n</th>
		        <th align="left">Unidad Ejecutora</th>
		        <th width="75">Estado</th>
		    </tr>
	    </thead>
	    
	    <tbody id="lista_registros">
		<?php
		$sql = "(SELECT
					cp.*,
					ss.CodClaSectorial,
					'' AS CodPrograma,
					'' AS CodSubPrograma,
					'' AS CodProyecto,
					'' AS CodActividad,
					ss.Denominacion,
					ue.Denominacion AS UnidadEjecutora
				 FROM
					pv_categoriaprog cp
					INNER JOIN pv_actividades a ON (a.IdActividad = cp.IdActividad)
					INNER JOIN pv_proyectos py ON (py.IdProyecto = a.IdProyecto)
					INNER JOIN pv_subprogramas spg ON (spg.IdSubPrograma = py.IdSubPrograma)
					INNER JOIN pv_programas pg ON (pg.IdPrograma = spg.IdPrograma)
					INNER JOIN pv_subsector ss ON (ss.IdSubSector = pg.IdSubSector)
					INNER JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cp.CodUnidadEjec)
					INNER JOIN pv_unidadejecutoradep ued ON (ued.CodUnidadEjec = ue.CodUnidadEjec)
				 WHERE 1 $filtro
				 GROUP BY CodClaSectorial)
				UNION
				(SELECT
					cp.*,
					ss.CodClaSectorial,
					pg.CodPrograma,
					'' AS CodSubPrograma,
					'' AS CodProyecto,
					'' AS CodActividad,
					pg.Denominacion,
					ue.Denominacion AS UnidadEjecutora
				 FROM
					pv_categoriaprog cp
					INNER JOIN pv_actividades a ON (a.IdActividad = cp.IdActividad)
					INNER JOIN pv_proyectos py ON (py.IdProyecto = a.IdProyecto)
					INNER JOIN pv_subprogramas spg ON (spg.IdSubPrograma = py.IdSubPrograma)
					INNER JOIN pv_programas pg ON (pg.IdPrograma = spg.IdPrograma)
					INNER JOIN pv_subsector ss ON (ss.IdSubSector = pg.IdSubSector)
					INNER JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cp.CodUnidadEjec)
					INNER JOIN pv_unidadejecutoradep ued ON (ued.CodUnidadEjec = ue.CodUnidadEjec)
				 WHERE pg.CodPrograma <> '00' $filtro
				 GROUP BY CodClaSectorial, CodPrograma)
				UNION
				(SELECT
					cp.*,
					ss.CodClaSectorial,
					pg.CodPrograma,
					spg.CodSubPrograma,
					'' AS CodProyecto,
					'' AS CodActividad,
					spg.Denominacion,
					ue.Denominacion AS UnidadEjecutora
				 FROM
					pv_categoriaprog cp
					INNER JOIN pv_actividades a ON (a.IdActividad = cp.IdActividad)
					INNER JOIN pv_proyectos py ON (py.IdProyecto = a.IdProyecto)
					INNER JOIN pv_subprogramas spg ON (spg.IdSubPrograma = py.IdSubPrograma)
					INNER JOIN pv_programas pg ON (pg.IdPrograma = spg.IdPrograma)
					INNER JOIN pv_subsector ss ON (ss.IdSubSector = pg.IdSubSector)
					INNER JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cp.CodUnidadEjec)
					INNER JOIN pv_unidadejecutoradep ued ON (ued.CodUnidadEjec = ue.CodUnidadEjec)
				 WHERE spg.CodSubPrograma <> '00'
				 GROUP BY CodClaSectorial, CodPrograma, CodSubPrograma)
				UNION
				(SELECT
					cp.*,
					ss.CodClaSectorial,
					pg.CodPrograma,
					spg.CodSubPrograma,
					py.CodProyecto,
					'' AS CodActividad,
					py.Denominacion,
					ue.Denominacion AS UnidadEjecutora
				 FROM
					pv_categoriaprog cp
					INNER JOIN pv_actividades a ON (a.IdActividad = cp.IdActividad)
					INNER JOIN pv_proyectos py ON (py.IdProyecto = a.IdProyecto)
					INNER JOIN pv_subprogramas spg ON (spg.IdSubPrograma = py.IdSubPrograma)
					INNER JOIN pv_programas pg ON (pg.IdPrograma = spg.IdPrograma)
					INNER JOIN pv_subsector ss ON (ss.IdSubSector = pg.IdSubSector)
					INNER JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cp.CodUnidadEjec)
					INNER JOIN pv_unidadejecutoradep ued ON (ued.CodUnidadEjec = ue.CodUnidadEjec)
				 WHERE py.CodProyecto <> '00' $filtro
				 GROUP BY CodClaSectorial, CodPrograma, CodSubPrograma, CodProyecto)
				UNION
				(SELECT
					cp.*,
					ss.CodClaSectorial,
					pg.CodPrograma,
					spg.CodSubPrograma,
					py.CodProyecto,
					a.CodActividad,
					a.Denominacion,
					ue.Denominacion AS UnidadEjecutora
				 FROM
					pv_categoriaprog cp
					INNER JOIN pv_actividades a ON (a.IdActividad = cp.IdActividad)
					INNER JOIN pv_proyectos py ON (py.IdProyecto = a.IdProyecto)
					INNER JOIN pv_subprogramas spg ON (spg.IdSubPrograma = py.IdSubPrograma)
					INNER JOIN pv_programas pg ON (pg.IdPrograma = spg.IdPrograma)
					INNER JOIN pv_subsector ss ON (ss.IdSubSector = pg.IdSubSector)
					INNER JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cp.CodUnidadEjec)
					INNER JOIN pv_unidadejecutoradep ued ON (ued.CodUnidadEjec = ue.CodUnidadEjec)
				 WHERE 1 $filtro
				 GROUP BY CodClaSectorial, CodPrograma, CodSubPrograma, CodProyecto, CodActividad)
				ORDER BY CodClaSectorial, CodPrograma, CodSubPrograma, CodProyecto, CodActividad";
		$field = getRecords($sql);
		$rows_lista = count($field);
		foreach($field as $f) {
			$id = $f['CategoriaProg'];
			if ($f['CodActividad']) {
				?><tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');"><?php
			} else {
				?><tr class="trListaBody3"><?php
			}
			?>
				<td align="center"><?=$f['CodClaSectorial']?></td>
				<td align="center"><?=$f['CodPrograma']?></td>
				<td align="center"><?=$f['CodSubPrograma']?></td>
				<td align="center"><?=$f['CodProyecto']?></td>
				<td align="center"><?=$f['CodActividad']?></td>
				<td><?=htmlentities($f['Denominacion'])?></td>
				<td><?=htmlentities($f['UnidadEjecutora'])?></td>
				<td align="center"><?=printValoresGeneral('ESTADO',$f['Estado'])?></td>
			</tr>
			<?php
		}
		?>
	    </tbody>
	</table>
</div>

</center>
</form>