<?php
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$fEjercicio = $AnioActual;
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "Ejercicio,CategoriaProg";
}
//	------------------------------------
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (ue.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fCodDependencia != "") { $cCodDependencia = "checked"; $filtro.=" AND (ued.CodDependencia = '".$fCodDependencia."')"; } else $dCodDependencia = "disabled";
if ($fCodUnidadEjec != "") { $cCodUnidadEjec = "checked"; $filtro.=" AND (cp.CodUnidadEjec = '".$fCodUnidadEjec."')"; } else $dCodUnidadEjec = "disabled";
if ($fIdSubSector != "") { $cIdSubSector = "checked"; $filtro.=" AND (pr.IdSubSector = '".$fIdSubSector."')"; } else $dIdSubSector = "disabled";
if ($fIdPrograma != "") { $cIdPrograma = "checked"; $filtro.=" AND (spr.IdPrograma = '".$fIdPrograma."')"; } else $dIdPrograma = "disabled";
if ($fIdSubPrograma != "") { $cIdSubPrograma = "checked"; $filtro.=" AND (py.IdSubPrograma = '".$fIdSubPrograma."')"; } else $dIdSubPrograma = "disabled";
if ($fIdProyecto != "") { $cIdProyecto = "checked"; $filtro.=" AND (a.IdProyecto = '".$fIdProyecto."')"; } else $dIdProyecto = "disabled";
if ($fIdActividad != "") { $cIdActividad = "checked"; $filtro.=" AND (cp.IdActividad = '".$fIdActividad."')"; } else $dIdActividad = "disabled";
if ($fEjercicio != "") { $cEjercicio = "checked"; $filtro.=" AND (fmdt.Ejercicio = '".$fEjercicio."')"; } else $dEjercicio = "disabled";
//	------------------------------------
$_titulo = "Generar Anteproyecto de Presupuesto";
$_width = 700;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=pv_proyectopresupuesto_generar" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />

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
			<td align="right" width="100">Ejercicio:</td>
			<td>
				<input type="checkbox" <?=$cEjercicio?> onclick="chkCampos(this.checked, 'fEjercicio');" />
				<input type="text" name="fEjercicio" id="fEjercicio" value="<?=$fEjercicio?>" style="width:47px;" maxlength="4" <?=$dEjercicio?> />
			</td>
	        <td align="right"><input type="submit" value="Buscar"></td>
		</tr>
	</table>
</div>
<div class="sep"></div>

<!--REGISTROS-->
<input type="hidden" name="sel_registros" id="sel_registros" />
<table class="tblBotones" style="width:100%; min-width:<?=$_width?>px; margin:auto;">
    <tr>
        <td><div id="rows"></div></td>
        <td align="right">
            <input type="button" value="Generar" style="width:75px;" class="insert" onclick="cargarOpcion2(this.form, 'gehen.php?anz=pv_proyectopresupuesto_form&opcion=generar-anteproyecto&action=pv_proyectopresupuesto_generar', 'SELF', '', $('#sel_registros').val());" />
        </td>
    </tr>
</table>

<div style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px; margin:auto;">
	<table class="tblLista" style="width:100%; min-width:100%;">
		<thead>
		    <tr>
		        <th width="75" onclick="order('Ejercicio,CategoriaProg')">Ejercicio</th>
		        <th width="125" onclick="order('CategoriaProg')">Cat. Program&aacute;tica</th>
		        <th align="left" onclick="order('Actividad')">Denominaci&oacute;n</th>
		        <th align="left" onclick="order('UnidadEjecutora')">Unidad Ejecutora</th>
		        <th width="100" align="right" onclick="order('Monto')">Monto Total</th>
		    </tr>
	    </thead>
	    
	    <tbody id="lista_registros">
		<?php
		//	consulto todos
		$sql = "SELECT
				    fmdt.Ejercicio,
					fmdt.CategoriaProg
				FROM
					vw_004formulacionmetasdist fmdt
				    INNER JOIN pv_categoriaprog cp ON (cp.CategoriaProg = fmdt.CategoriaProg)
				    INNER JOIN pv_actividades a ON (a.IdActividad = cp.IdActividad)
					INNER JOIN pv_proyectos py ON (py.IdProyecto = a.IdProyecto)
					INNER JOIN pv_subprogramas spr ON (spr.IdSubPrograma = py.IdSubPrograma)
					INNER JOIN pv_programas pr ON (pr.IdPrograma = spr.IdPrograma)
				    INNER JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cp.CodUnidadEjec)
				    LEFT JOIN pv_unidadejecutoradep ued ON (ued.CodUnidadEjec = ue.CodUnidadEjec)
				WHERE fmdt.Estado = 'AP' $filtro
				GROUP BY Ejercicio, CategoriaProg";
		$rows_total = getNumRows3($sql);
		//	consulto lista
		$sql = "SELECT
				    fmdt.Ejercicio,
					fmdt.CategoriaProg,
				    cp.IdActividad,
				    a.CodActividad,
				    a.Denominacion AS Actividad,
				    cp.CodUnidadEjec,
				    ue.CodOrganismo,
				    ue.Denominacion AS UnidadEjecutora,
				    SUM(fmdt.Monto) AS Monto
				FROM
					vw_004formulacionmetasdist fmdt
				    INNER JOIN pv_categoriaprog cp ON (cp.CategoriaProg = fmdt.CategoriaProg)
				    INNER JOIN pv_actividades a ON (a.IdActividad = cp.IdActividad)
					INNER JOIN pv_proyectos py ON (py.IdProyecto = a.IdProyecto)
					INNER JOIN pv_subprogramas spr ON (spr.IdSubPrograma = py.IdSubPrograma)
					INNER JOIN pv_programas pr ON (pr.IdPrograma = spr.IdPrograma)
				    INNER JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cp.CodUnidadEjec)
				    LEFT JOIN pv_unidadejecutoradep ued ON (ued.CodUnidadEjec = ue.CodUnidadEjec)
				WHERE fmdt.Estado = 'AP' $filtro
				GROUP BY Ejercicio, CategoriaProg
				ORDER BY $fOrderBy
				LIMIT ".intval($limit).", ".intval($maxlimit);
		$field = getRecords($sql);
		$rows_lista = count($field);
		foreach($field as $f) {
			$id = $f['Ejercicio'].'_'.$f['CategoriaProg'];
			?>
			<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
				<td align="center"><?=$f['Ejercicio']?></td>
				<td align="center"><?=$f['CategoriaProg']?></td>
				<td><?=htmlentities($f['Actividad'])?></td>
				<td><?=htmlentities($f['UnidadEjecutora'])?></td>
				<td align="right"><?=number_format($f['Monto'],2,',','.')?></td>
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