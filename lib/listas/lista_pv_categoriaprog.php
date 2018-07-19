<?php
if (!$ventana) $ventana = "selLista";
//	------------------------------------
if ($filtrar == "default") {
	if (!$fCodDependencia) $FlagDependencia = 'N';
	if ($fCodOrganismo) $fCodOrganismo = ($fCodOrganismo?$fCodOrganismo:$_SESSION["FILTRO_ORGANISMO_ACTUAL"]); 
	else $fCodOrganismo = ($_SESSION["fCodOrganismo"]?$_SESSION["fCodOrganismo"]:$_SESSION["FILTRO_ORGANISMO_ACTUAL"]);
	if ($fCodDependencia) $fCodDependencia = ($fCodDependencia?$fCodDependencia:$_SESSION["fCodDependencia"]); 
	else $fCodDependencia = ($_SESSION["fCodDependencia"]?$_SESSION["fCodDependencia"]:$_SESSION["fCodDependencia"]);
	$fIdSubSector = ($_SESSION["fIdSubSector"]?$_SESSION["fIdSubSector"]:'');
	$fEstado = 'A';
}
$_SESSION["fCodOrganismo"] = $fCodOrganismo;
$_SESSION["fCodDependencia"] = $fCodDependencia;
$_SESSION["fIdSubSector"] = $fIdSubSector;
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
$_titulo = "Indice de Categor&iacute;as Program&aacute;ticas";
$_width = 900;

if ($ventana == "selListaOpener") {
	?>
	<table width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td class="titulo"><?=$_titulo?></td>
			<td align="right"><a class="cerrar" href="javascript:" onclick="window.close();">[cerrar]</a></td>
		</tr>
	</table><hr width="100%" color="#333333" />
	<?php
}
?>

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lista_pv_categoriaprog" method="post">
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
<input type="hidden" name="FlagOrganismo" id="FlagOrganismo" value="<?=$FlagOrganismo?>" />
<input type="hidden" name="FlagDependencia" id="FlagDependencia" value="<?=$FlagDependencia?>" />

<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
	<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
		<tr>
			<td align="right" width="125">Organismo:</td>
			<td>
				<input type="checkbox" <?=$cCodOrganismo?> onclick="this.checked=!this.checked;" />
				<select name="fCodOrganismo" id="fCodOrganismo" style="width:200px;" <?=$dCodOrganismo?> onChange="loadSelect($('#fCodUnidadEjec'), 'tabla=pv_unidadejecutora&CodOrganismo='+$(this).val(), 1); loadSelect($('#fCodDependencia'), 'tabla=dependencia_filtro&opcion='+$(this).val(), 1);">
					<?php
					if ($FlagOrganismo == 'S') echo loadSelect2('mastorganismos','CodOrganismo','Organismo',$fCodOrganismo,1);
					else echo getOrganismos($fCodOrganismo, 3);
					?>
				</select>
			</td>
			<td align="right">Sub-Sector:</td>
			<td>
				<input type="checkbox" <?=$cIdSubSector?> onclick="chkCampos(this.checked, 'fIdSubSector');" onChange="loadSelect($('#fIdPrograma'), 'tabla=pv_programas&IdSubSector='+$('#fIdSubSector').val(), 1, ['fIdSubPrograma','fIdProyecto','fIdActividad']);" />
				<select name="fIdSubSector" id="fIdSubSector" style="width:200px;" <?=$dIdSubSector?> onChange="loadSelect($('#fIdPrograma'), 'tabla=pv_programas&IdSubSector='+$('#fIdSubSector').val(), 1, ['fIdSubPrograma','fIdProyecto','fIdActividad']);">
					<option value="">&nbsp;</option>
					<?=loadSelect2('pv_subsector','IdSubSector','Denominacion',$fIdSubSector,0,NULL,NULL,'CodClaSectorial')?>
				</select>
			</td>
			<td align="right">Proyecto:</td>
			<td>
				<input type="checkbox" <?=$cIdProyecto?> onclick="chkCampos(this.checked, 'fIdProyecto');" onChange="loadSelect($('#fIdActividad'), 'tabla=pv_actividades&IdProyecto='+$('#fIdProyecto').val(), 1);" />
				<select name="fIdProyecto" id="fIdProyecto" style="width:200px;" <?=$dIdProyecto?> onChange="loadSelect($('#fIdActividad'), 'tabla=pv_actividades&IdProyecto='+$('#fIdProyecto').val(), 1);">
					<option value="">&nbsp;</option>
					<?=loadSelect2('pv_proyectos','IdProyecto','Denominacion',$fIdProyecto,0,['IdSubPrograma'],[$fIdSubPrograma],'CodProyecto')?>
				</select>
			</td>
	        <td>&nbsp;</td>
		</tr>
		<tr>
			<td align="right">Dependencia: </td>
			<td>
				<?php
				if ($FlagDependencia == 'S') {
					?>
		            <input type="checkbox" <?=$cCodDependencia?> onclick="this.checked=!this.checked;" />
					<select name="fCodDependencia" id="fCodDependencia" style="width:200px;" <?=$dCodDependencia?>>
						<?=loadSelect2('mastdependencias','CodDependencia','Dependencia',$fCodDependencia,1)?>
					</select>
					<?php
				} else {
					?>
		            <input type="checkbox" <?=$cCodDependencia?> onclick="chkFiltro(this.checked, 'fCodDependencia');" />
					<select name="fCodDependencia" id="fCodDependencia" style="width:200px;" <?=$dCodDependencia?>>
						<option value="">&nbsp;</option>
						<?=getDependencias($fCodDependencia, $fCodOrganismo, 0)?>
					</select>
					<?php
				}
				?>
			</td>
			<td align="right">Programa:</td>
			<td>
				<input type="checkbox" <?=$cIdPrograma?> onclick="chkCampos(this.checked, 'fIdPrograma');" onChange="loadSelect($('#fIdSubPrograma'), 'tabla=pv_subprogramas&IdPrograma='+$('#fIdPrograma').val(), 1, ['fIdProyecto','fIdActividad']);" />
				<select name="fIdPrograma" id="fIdPrograma" style="width:200px;" <?=$dIdPrograma?> onChange="loadSelect($('#fIdSubPrograma'), 'tabla=pv_subprogramas&IdPrograma='+$('#fIdPrograma').val(), 1, ['fIdProyecto','fIdActividad']);">
					<option value="">&nbsp;</option>
					<?=loadSelect2('pv_programas','IdPrograma','Denominacion',$fIdPrograma,0,['IdSubSector'],[$fIdSubSector],'CodPrograma')?>
				</select>
			</td>
			<td align="right">Actividad:</td>
			<td>
				<input type="checkbox" <?=$cIdActividad?> onclick="chkCampos(this.checked, 'fIdActividad');" />
				<select name="fIdActividad" id="fIdActividad" style="width:200px;" <?=$dIdActividad?>>
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
				<select name="fCodUnidadEjec" id="fCodUnidadEjec" style="width:200px;" <?=$dCodUnidadEjec?>>
					<option value="">&nbsp;</option>
					<?=loadSelect2('pv_unidadejecutora','CodUnidadEjec','Denominacion',$fCodUnidadEjec,10,['CodOrganismo'],[$fCodOrganismo]);?>
				</select>
			</td>
			<td align="right" width="100">Sub-Programa:</td>
			<td>
				<input type="checkbox" <?=$cIdSubPrograma?> onclick="chkCampos(this.checked, 'fIdSubPrograma');" onChange="loadSelect($('#fIdProyecto'), 'tabla=pv_proyectos&IdSubPrograma='+$('#fIdSubPrograma').val(), 1, ['fIdActividad']);" />
				<select name="fIdSubPrograma" id="fIdSubPrograma" style="width:200px;" <?=$dIdSubPrograma?> onChange="loadSelect($('#fIdProyecto'), 'tabla=pv_proyectos&IdSubPrograma='+$('#fIdSubPrograma').val(), 1, ['fIdActividad']);">
					<option value="">&nbsp;</option>
					<?=loadSelect2('pv_subprogramas','IdSubPrograma','Denominacion',$fIdSubPrograma,0,['IdPrograma'],[$fIdPrograma],'CodSubPrograma')?>
				</select>
			</td>
			<td align="right">Estado: </td>
			<td>
	            <input type="checkbox" <?=$cEstado?> onclick="this.checked=!this.checked;" />
	            <select name="fEstado" id="fEstado" style="width:100px;" <?=$dEstado?>>
	                <?=loadSelectGeneral("ESTADO", $fEstado, 1)?>
	            </select>
			</td>
	        <td align="right"><input type="submit" value="Buscar"></td>
		</tr>
	</table>
</div>
<div class="sep"></div>

<div class="scroll" style="overflow:scroll; height:285px; width:100%; min-width:<?=$_width?>px;">
	<table class="tblLista" style="width:100%; min-width:1000px;">
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
	    
	    <tbody>
		<?php
		$sql = "(SELECT
					cp.*,
					ss.CodClaSectorial,
					'' AS CodPrograma,
					'' AS CodSubPrograma,
					'' AS CodProyecto,
					'' AS CodActividad,
					ss.Denominacion,
					ue.Denominacion AS UnidadEjecutora,
					ss.IdSubSector,
					pg.IdPrograma,
					spg.IdSubPrograma,
					py.IdProyecto,
					a.IdActividad,
					ue.CodUnidadEjec
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
					ue.Denominacion AS UnidadEjecutora,
					ss.IdSubSector,
					pg.IdPrograma,
					spg.IdSubPrograma,
					py.IdProyecto,
					a.IdActividad,
					ue.CodUnidadEjec
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
					ue.Denominacion AS UnidadEjecutora,
					ss.IdSubSector,
					pg.IdPrograma,
					spg.IdSubPrograma,
					py.IdProyecto,
					a.IdActividad,
					ue.CodUnidadEjec
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
					ue.Denominacion AS UnidadEjecutora,
					ss.IdSubSector,
					pg.IdPrograma,
					spg.IdSubPrograma,
					py.IdProyecto,
					a.IdActividad,
					ue.CodUnidadEjec
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
					ue.Denominacion AS UnidadEjecutora,
					ss.IdSubSector,
					pg.IdPrograma,
					spg.IdSubPrograma,
					py.IdProyecto,
					a.IdActividad,
					ue.CodUnidadEjec
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
		$field = getRecords($sql);
		$rows_lista = count($field);
		foreach($field as $f) {
			$id = $f['CategoriaProg'];
			if ($f['CodActividad']) {
				if ($ventana == 'presupuesto') {
					?><tr class="trListaBody" onClick="selLista(['<?=$f['CategoriaProg']?>','<?=$f['IdSubSector']?>','<?=$f['IdPrograma']?>','<?=$f['IdSubPrograma']?>','<?=$f['IdProyecto']?>','<?=$f['IdActividad']?>','<?=$f['CodUnidadEjec']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>','<?=$campo4?>','<?=$campo5?>','<?=$campo6?>','<?=$campo7?>']);"><?php
				}
				if ($ventana == 'pv_proyectopresupuesto') {
					?><tr class="trListaBody" onClick="pv_proyectopresupuesto(['<?=$f['CategoriaProg']?>','<?=$f['IdSubSector']?>','<?=$f['IdPrograma']?>','<?=$f['IdSubPrograma']?>','<?=$f['IdProyecto']?>','<?=$f['IdActividad']?>','<?=$f['CodUnidadEjec']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>','<?=$campo4?>','<?=$campo5?>','<?=$campo6?>','<?=$campo7?>']);"><?php
				}
				elseif ($ventana == 'unidad_ejecutora') {
					?><tr class="trListaBody" onClick="selLista(['<?=$f['CategoriaProg']?>','<?=$f['UnidadEjecutora']?>'], ['<?=$campo1?>','<?=$campo2?>']);"><?php
				}
				elseif ($ventana == 'pv_metas') {
					?><tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['CategoriaProg']?>','<?=$f['UnidadEjecutora']?>'], ['<?=$campo1?>','<?=$campo2?>']);"><?php
				}
				elseif ($ventana == 'pv_formulacionmetas') {
					?><tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['CategoriaProg']?>','<?=$f['UnidadEjecutora']?>'], ['<?=$campo1?>','<?=$campo2?>']);"><?php
				}
				elseif ($ventana == 'pv_reformulacionmetas') {
					?><tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['CategoriaProg']?>','<?=$f['UnidadEjecutora']?>'], ['<?=$campo1?>','<?=$campo2?>']);"><?php
				}
				elseif ($ventana == 'ob_planobras') {
					?><tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['CategoriaProg']?>','<?=$f['IdSubSector']?>','<?=$f['IdPrograma']?>','<?=$f['IdSubPrograma']?>','<?=$f['IdProyecto']?>','<?=$f['IdActividad']?>','<?=$f['CodUnidadEjec']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>','<?=$campo4?>','<?=$campo5?>','<?=$campo6?>','<?=$campo7?>']);"><?php
				}
				elseif ($ventana == 'selListaOpener') {
					?><tr class="trListaBody" onClick="selListaOpener(['<?=$f['CategoriaProg']?>'], ['<?=$campo1?>']);"><?php
				}
				elseif ($ventana == "selListadoListaParent") {
					?><tr class="trListaBody" onclick="selListadoListaParent('<?=$seldetalle?>',['<?=$campo1?>'],['<?=$f['CategoriaProg']?>']);" id="<?=$f['CategoriaProg']?>"><?php
				}
				else {
					?><tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['CategoriaProg']?>'], ['<?=$campo1?>']);"><?php
				}
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

<script type="text/javascript" language="javascript">
	<?php
	if ($ventana == 'pv_ajustes') {
		?>
		function pv_ajustes(valores, inputs) {
			parent.$('#lista_partida').html('');
			if (inputs) {
				for(var i=0; i<inputs.length; i++) {
					if (parent.$("#"+inputs[i]).length > 0) parent.$("#"+inputs[i]).val(valores[i]);
				}
			}
			parent.$.prettyPhoto.close();
		}
		<?php
	}
	elseif ($ventana == 'pv_metas') {
		?>
		function pv_metas(valores, inputs) {
			//	ajax
			$.ajax({
				type: "POST",
				url: "../../pv/pv_metaspoa_ajax.php",
				data: "modulo=ajax&accion=getObjetivos&CategoriaProg="+valores[0],
				async: false,
				success: function(resp) {
					if (parent.$('#CodObjetivo').length) parent.$('#CodObjetivo').html(resp); 
					else if (parent.$('#fCodObjetivo').length) parent.$('#fCodObjetivo').html(resp);
					if (inputs) {
						for(var i=0; i<inputs.length; i++) {
							if (parent.$("#"+inputs[i]).length > 0) parent.$("#"+inputs[i]).val(valores[i]);
						}
					}
					parent.$.prettyPhoto.close();
				}
			});
		}
		<?php
	}
	elseif ($ventana == 'pv_formulacionmetas') {
		?>
		function pv_formulacionmetas(valores, inputs) {
			//	ajax
			$.ajax({
				type: "POST",
				url: "../../pv/pv_formulacionmetas_ajax.php",
				data: "modulo=ajax&accion=getObjetivos&CategoriaProg="+valores[0],
				async: false,
				success: function(resp) {
					if (parent.$('#CodObjetivo').length) parent.$('#CodObjetivo').html(resp); 
					else if (parent.$('#fCodObjetivo').length) parent.$('#fCodObjetivo').html(resp);
					if (parent.$('#CodMeta').length) parent.$('#CodMeta').html('<option value="">&nbsp;</option>'); 
					else if (parent.$('#fCodMeta').length) parent.$('#fCodMeta').html('<option value="">&nbsp;</option>');
					if (inputs) {
						for(var i=0; i<inputs.length; i++) {
							if (parent.$("#"+inputs[i]).length > 0) parent.$("#"+inputs[i]).val(valores[i]);
						}
					}
					parent.$.prettyPhoto.close();
				}
			});
		}
		<?php
	}
	elseif ($ventana == 'pv_reformulacionmetas') {
		?>
		function pv_reformulacionmetas(valores, inputs) {
			//	ajax
			$.ajax({
				type: "POST",
				url: "../../pv/pv_reformulacionmetas_ajax.php",
				data: "modulo=ajax&accion=getObjetivos&CategoriaProg="+valores[0]+"&Ejercicio="+parent.$('#Ejercicio').val(),
				async: false,
				dataType: "json",
				success: function(data) {
					if (parent.$('#CodObjetivo').length) parent.$('#CodObjetivo').html(data['CodObjetivo']); 
					else if (parent.$('#fCodObjetivo').length) parent.$('#fCodObjetivo').html(data['CodObjetivo']);
					if (parent.$('#CodMeta').length) parent.$('#CodMeta').html('<option value="">&nbsp;</option>'); 
					else if (parent.$('#fCodMeta').length) parent.$('#fCodMeta').html('<option value="">&nbsp;</option>');
					if (inputs) {
						for(var i=0; i<inputs.length; i++) {
							if (parent.$("#"+inputs[i]).length > 0) parent.$("#"+inputs[i]).val(valores[i]);
						}
					}

					if (parent.$('#CodObjetivo').length) {
						var TotalGeneral = setNumero(parent.$('#TotalGeneral').val());
						var MontoDistribuido = data['MontoDistribuido'] + TotalGeneral;
						parent.$('#MontoAprobado').val(data['MontoAprobado']).formatCurrency();
						parent.$('#MontoPersonal').val(data['MontoPersonal']).formatCurrency();
						parent.$('#MontoDistribuido').val(MontoDistribuido).formatCurrency();
						var MontoDistribuidoInicial = data['MontoDistribuido'] - TotalGeneral;
						parent.$('#MontoDistribuidoInicial').val(MontoDistribuidoInicial);
						var TotalResta = data['MontoAprobado'] - (MontoDistribuido + data['MontoPersonal']);
						parent.$('#TotalResta').val(TotalResta).formatCurrency();
						//	-
						var TotalGeneral1 = setNumero(parent.$('#TotalGeneral1').val());
						var MontoDistribuido1 = data['MontoDistribuido1'] + TotalGeneral1;
						parent.$('#MontoAprobado1').val(data['MontoAprobado1']).formatCurrency();
						parent.$('#MontoPersonal1').val(data['MontoPersonal1']).formatCurrency();
						parent.$('#MontoDistribuido1').val(MontoDistribuido1).formatCurrency();
						var MontoDistribuidoInicial1 = data['MontoDistribuido1'] - TotalGeneral1;
						parent.$('#MontoDistribuidoInicial1').val(MontoDistribuidoInicial1);
						var TotalResta1 = data['MontoAprobado1'] - (MontoDistribuido1 + data['MontoPersonal1']);
						parent.$('#TotalResta1').val(TotalResta1).formatCurrency();
						//	-
						var TotalGeneral2 = setNumero(parent.$('#TotalGeneral2').val());
						var MontoDistribuido2 = data['MontoDistribuido2'] + TotalGeneral2;
						parent.$('#MontoAprobado2').val(data['MontoAprobado2']).formatCurrency();
						parent.$('#MontoPersonal2').val(data['MontoPersonal2']).formatCurrency();
						parent.$('#MontoDistribuido2').val(MontoDistribuido2).formatCurrency();
						var MontoDistribuidoInicial2 = data['MontoDistribuido2'] - TotalGeneral2;
						parent.$('#MontoDistribuidoInicial2').val(MontoDistribuidoInicial2);
						var TotalResta2 = data['MontoAprobado2'] - (MontoDistribuido2 + data['MontoPersonal2']);
						parent.$('#TotalResta2').val(TotalResta2).formatCurrency();
					}

					parent.$.prettyPhoto.close();
				}
			});
		}
		<?php
	}
	elseif ($ventana == 'ob_planobras') {
		?>
		function ob_planobras(valores, inputs) {
			$.ajax({
				type: "POST",
				url: "../../ob/ob_planobras_ajax.php",
				data: "modulo=ajax&accion=getDependencias&CodUnidadEjec="+valores[6],
				async: false,
				success: function(data) {
					if (inputs) {
						for(var i=0; i<inputs.length; i++) {
							if (parent.$("#"+inputs[i]).length > 0) parent.$("#"+inputs[i]).val(valores[i]);
						}
					}
					parent.$('#CodDependencia').html(data);
					parent.$.prettyPhoto.close();
				}
			});
		}
		<?php
	}
	elseif ($ventana == 'pv_proyectopresupuesto') {
		?>
		function pv_proyectopresupuesto(valores, inputs) {
			//	ajax
			$.ajax({
				type: "POST",
				url: "../../pv/pv_proyectopresupuesto_ajax.php",
				data: "modulo=ajax&accion=getDependenciasxUnidadEjecutora&CodUnidadEjec="+valores[6],
				async: false,
				success: function(data) {
					if (inputs) {
						for(var i=0; i<inputs.length; i++) {
							if (parent.$("#"+inputs[i]).length > 0) parent.$("#"+inputs[i]).val(valores[i]);
						}
					}
					parent.$('#lista_dep').html(data);
					parent.$.prettyPhoto.close();
				}
			});
		}
		<?php
	}

	?>
</script>