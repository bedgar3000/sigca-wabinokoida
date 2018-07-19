<?php
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$fEjercicio = $AnioActual;
	$fEstado = 'PR';
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "CodAjuste";
}
//	------------------------------------
if ($lista == "listar") {
	$_titulo = "Ajustes de Presupuesto";
	$_btNuevo = "";
	$_btModificar = "";
	$_btAprobar = "display:none;";
}
elseif ($lista == "aprobar") {
	$fEstado = "PR";
	##	
	$_titulo = "Ajustes de Presupuesto (Aprobar)";
	$_btNuevo = "display:none;";
	$_btModificar = "display:none;";
	$_btAprobar = "";
}
//	------------------------------------
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (p.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fCodDependencia != "") { $cCodDependencia = "checked"; $filtro.=" AND (ued.CodDependencia = '".$fCodDependencia."')"; } else $dCodDependencia = "disabled";
if ($fCodUnidadEjec != "") { $cCodUnidadEjec = "checked"; $filtro.=" AND (cp.CodUnidadEjec = '".$fCodUnidadEjec."')"; } else $dCodUnidadEjec = "disabled";
if ($fIdSubSector != "") { $cIdSubSector = "checked"; $filtro.=" AND (pr.IdSubSector = '".$fIdSubSector."')"; } else $dIdSubSector = "disabled";
if ($fIdPrograma != "") { $cIdPrograma = "checked"; $filtro.=" AND (spr.IdPrograma = '".$fIdPrograma."')"; } else $dIdPrograma = "disabled";
if ($fIdSubPrograma != "") { $cIdSubPrograma = "checked"; $filtro.=" AND (py.IdSubPrograma = '".$fIdSubPrograma."')"; } else $dIdSubPrograma = "disabled";
if ($fIdProyecto != "") { $cIdProyecto = "checked"; $filtro.=" AND (a.IdProyecto = '".$fIdProyecto."')"; } else $dIdProyecto = "disabled";
if ($fIdActividad != "") { $cIdActividad = "checked"; $filtro.=" AND (cp.IdActividad = '".$fIdActividad."')"; } else $dIdActividad = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (aj.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fEjercicio != "") { $cEjercicio = "checked"; $filtro.=" AND (p.Ejercicio = '".$fEjercicio."')"; } else $dEjercicio = "disabled";
//	------------------------------------
$_width = 700;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=pv_ajustes_lista" method="post" autocomplete="off">
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
			<td align="right">Estado: </td>
			<td>
				<?php
				if ($lista == 'listar') {
					?>
		            <input type="checkbox" <?=$cEstado?> onclick="chkFiltro(this.checked, 'fEstado');" />
		            <select name="fEstado" id="fEstado" style="width:100px;" <?=$dEstado?>>
		                <option value="">&nbsp;</option>
		                <?=loadSelectValores("ajustes-estado", $fEstado, 0)?>
		            </select>
					<?php
				} else {
					?>
		            <input type="checkbox" <?=$cEstado?> onclick="this.checked=!this.checked;" />
		            <select name="fEstado" id="fEstado" style="width:100px;" <?=$dEstado?>>
		                <?=loadSelectValores("ajustes-estado", $fEstado, 1)?>
		            </select>
					<?php
				}
				?>
			</td>
	        <td>&nbsp;</td>
		</tr>
		<tr>
			<td align="right" width="100">Ejercicio:</td>
			<td>
				<input type="checkbox" <?=$cEjercicio?> onclick="chkCampos(this.checked, 'fEjercicio');" />
				<input type="text" name="fEjercicio" id="fEjercicio" value="<?=$fEjercicio?>" style="width:47px;" maxlength="4" <?=$dEjercicio?> />
			</td>
	        <td>&nbsp;</td>
	        <td>&nbsp;</td>
	        <td>&nbsp;</td>
	        <td>&nbsp;</td>
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
        <td align="right" class="gallery clearfix">
            <input type="button" value="Nuevo" style="width:75px; <?=$_btNuevo?>" class="insert" onclick="cargarPagina(this.form, 'gehen.php?anz=pv_ajustes_form&opcion=nuevo&action=pv_ajustes_lista');" />
            <input type="button" value="Modificar" style="width:75px; <?=$_btModificar?>" class="update" onclick="cargarOpcionValidar3(this.form, $('#sel_registros').val(), 'pv_ajustes_ajax.php', 'modulo=validar&accion=modificar', 'gehen.php?anz=pv_ajustes_form&opcion=modificar&action=pv_ajustes_lista', 'SELF', '');" />
            <input type="button" value="Aprobar" style="width:75px; <?=$_btAprobar?>" class="update" onclick="cargarOpcionValidar3(this.form, $('#sel_registros').val(), 'pv_ajustes_ajax.php', 'modulo=validar&accion=aprobar', 'gehen.php?anz=pv_ajustes_form&opcion=aprobar&action=pv_ajustes_lista', 'SELF', '');" />
            <input type="button" value="Anular" style="width:75px;" class="update" onclick="cargarOpcionValidar3(this.form, $('#sel_registros').val(), 'pv_ajustes_ajax.php', 'modulo=validar&accion=anular', 'gehen.php?anz=pv_ajustes_form&opcion=anular&action=pv_ajustes_lista', 'SELF', '');" /> |
            <input type="button" value="Ver" style="width:75px;" class="ver" onclick="cargarOpcion2(this.form, 'gehen.php?anz=pv_ajustes_form&opcion=ver&action=pv_ajustes_lista', 'SELF', '', $('#sel_registros').val());" />

            <a href="pagina.php?iframe=true" rel="prettyPhoto[iframe1]" style="display:none;" id="a_imprimir"></a>
            <input type="button" id="btImprimir" value="Imprimir" style="width:75px;" onclick="abrirIFrame(this.form, 'a_imprimir', 'pv_ajustes_pdf.php?', '100%', '100%', $('#sel_registros').val());" />
        </td>
    </tr>
</table>

<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px;">
	<table class="tblLista" style="width:100%; min-width:1200px;">
		<thead>
	    <tr>
	        <th width="50" onclick="order('CodAjuste')">C&oacute;digo</th>
	        <th width="60" onclick="order('CodPresupuesto')">Presupuesto</th>
	        <th width="50" onclick="order('Ejercicio')">Ejercicio</th>
	        <th width="105" onclick="order('CategoriaProg')">Cat. Program&aacute;tica</th>
	        <th width="300" align="left" onclick="order('UnidadEjecutora')">Unidad Ejecutora</th>
	        <th align="left" onclick="order('Descripcion')">Descripci&oacute;n</th>
	        <th width="90" onclick="order('Estado')">Estado</th>
	    </tr>
	    </thead>
	    
	    <tbody id="lista_registros">
		<?php
		//	consulto todos
		$sql = "SELECT *
				FROM
					pv_ajustes aj
					INNER JOIN pv_presupuesto p ON (p.CodOrganismo = aj.CodOrganismo AND p.CodPresupuesto = aj.CodPresupuesto)
					INNER JOIN pv_categoriaprog cp ON (cp.CategoriaProg = p.CategoriaProg)
					INNER JOIN pv_actividades a ON (a.IdActividad = cp.IdActividad)
					INNER JOIN pv_proyectos py ON (py.IdProyecto = a.IdProyecto)
					INNER JOIN pv_subprogramas spr ON (spr.IdSubPrograma = py.IdSubPrograma)
					INNER JOIN pv_programas pr ON (pr.IdPrograma = spr.IdPrograma)
					INNER JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cp.CodUnidadEjec)
					INNER JOIN pv_unidadejecutoradep ued ON (ued.CodUnidadEjec = ue.CodUnidadEjec)
				WHERE 1 $filtro
				GROUP BY aj.CodOrganismo, aj.CodAjuste";
		$rows_total = getNumRows3($sql);
		//	consulto lista
		$sql = "SELECT
					aj.*,
					p.Ejercicio,
					p.CodPresupuesto,
					p.CategoriaProg,
					ue.Denominacion AS UnidadEjecutora
				FROM
					pv_ajustes aj
					INNER JOIN pv_presupuesto p ON (p.CodOrganismo = aj.CodOrganismo AND p.CodPresupuesto = aj.CodPresupuesto)
					INNER JOIN pv_categoriaprog cp ON (cp.CategoriaProg = p.CategoriaProg)
					INNER JOIN pv_actividades a ON (a.IdActividad = cp.IdActividad)
					INNER JOIN pv_proyectos py ON (py.IdProyecto = a.IdProyecto)
					INNER JOIN pv_subprogramas spr ON (spr.IdSubPrograma = py.IdSubPrograma)
					INNER JOIN pv_programas pr ON (pr.IdPrograma = spr.IdPrograma)
					INNER JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cp.CodUnidadEjec)
					INNER JOIN pv_unidadejecutoradep ued ON (ued.CodUnidadEjec = ue.CodUnidadEjec)
				WHERE 1 $filtro
				GROUP BY aj.CodOrganismo, aj.CodAjuste
				ORDER BY $fOrderBy
				LIMIT ".intval($limit).", ".intval($maxlimit);
		$field = getRecords($sql);
		$rows_lista = count($field);
		foreach($field as $f) {
			$id = $f['CodOrganismo'].'_'.$f['CodAjuste'];
			?>
			<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
				<td align="center"><?=$f['CodAjuste']?></td>
				<td align="center"><?=$f['CodPresupuesto']?></td>
				<td align="center"><?=$f['Ejercicio']?></td>
				<td align="center"><?=$f['CategoriaProg']?></td>
				<td><?=htmlentities($f['UnidadEjecutora'])?></td>
				<td><?=htmlentities($f['Descripcion'])?></td>
				<td align="center"><?=printValores('ajustes-estado',$f['Estado'])?></td>
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

<script type="text/javascript">
	<?php
	if ($FlagImprimir == 'S') {
		?>
		$(document).ready(function() {
			$("#a_imprimir").attr("href", 'pv_ajustes_pdf.php?sel_registros=<?=$CodOrganismo?>_<?=$CodAjuste?>&iframe=true&width=100%&height=100%');
			document.getElementById('a_imprimir').click();
	    });
		<?php
	}
	?>
</script>