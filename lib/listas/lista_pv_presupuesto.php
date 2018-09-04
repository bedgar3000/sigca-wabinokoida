<?php
if (!$ventana) $ventana = "selLista";
//	------------------------------------
if ($filtrar == "default") {
	if ($FlagCategoriaProg != 'S') {
		$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	}
	$sql = "SELECT MAX(Ejercicio) FROM pv_reformulacionmetas";
	$Ejercicio = getVar3($sql);
	$fEjercicio = ($Ejercicio?$AnioActual:$AnioActual);
	$fEstado = 'AP';
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "CodPresupuesto";
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
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (p.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fEjercicio != "") { $cEjercicio = "checked"; $filtro.=" AND (p.Ejercicio = '".$fEjercicio."')"; } else $dEjercicio = "disabled";
//	------------------------------------
$_titulo = "Formulaci&oacute;n de Presupuesto";
$_width = 700;
?>
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lista_pv_presupuesto" method="post">
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
<input type="hidden" name="FlagCategoriaProg" id="FlagCategoriaProg" value="<?=$FlagCategoriaProg?>" />
<input type="hidden" name="FlagOrganismo" id="FlagOrganismo" value="<?=$FlagOrganismo?>" />

<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
	<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
		<tr>
			<td align="right" width="125">Organismo:</td>
			<td>
				<input type="checkbox" <?=$cCodOrganismo?> onclick="this.checked=!this.checked;" />
				<select name="fCodOrganismo" id="fCodOrganismo" style="width:215px;" <?=$dCodOrganismo?> onChange="loadSelect($('#fCodUnidadEjec'), 'tabla=pv_unidadejecutora&CodOrganismo='+$(this).val(), 1); loadSelect($('#fCodDependencia'), 'tabla=dependencia_filtro&opcion='+$(this).val(), 1);">
					<?php 
					if ($FlagCategoriaProg == 'S' || $FlagOrganismo == 'S') loadSelect2('mastorganismos','CodOrganismo','Organismo',$fCodOrganismo,1);
					else getOrganismos($fCodOrganismo, 3);
					?>
				</select>
			</td>
			<td align="right">Sub-Sector:</td>
			<td>
				<input type="checkbox" <?=$cIdSubSector?> onclick="chkCampos(this.checked, 'fIdSubSector');" onChange="loadSelect($('#fIdPrograma'), 'tabla=pv_programas&IdSubSector='+$('#fIdSubSector').val(), 1, ['fIdSubPrograma','fIdProyecto','fIdActividad']);" />
				<select name="fIdSubSector" id="fIdSubSector" style="width:215px;" <?=$dIdSubSector?> onChange="loadSelect($('#fIdPrograma'), 'tabla=pv_programas&IdSubSector='+$('#fIdSubSector').val(), 1, ['fIdSubPrograma','fIdProyecto','fIdActividad']);">
					<option value="">&nbsp;</option>
					<?=loadSelect2('pv_subsector','IdSubSector','Denominacion',$fIdSubSector,0,NULL,NULL,'CodClaSectorial')?>
				</select>
			</td>
			<td align="right">Proyecto:</td>
			<td>
				<input type="checkbox" <?=$cIdProyecto?> onclick="chkCampos(this.checked, 'fIdProyecto');" onChange="loadSelect($('#fIdActividad'), 'tabla=pv_actividades&IdProyecto='+$('#fIdProyecto').val(), 1);" />
				<select name="fIdProyecto" id="fIdProyecto" style="width:215px;" <?=$dIdProyecto?> onChange="loadSelect($('#fIdActividad'), 'tabla=pv_actividades&IdProyecto='+$('#fIdProyecto').val(), 1);">
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
				if ($FlagCategoriaProg == 'S') {
					?>
		            <input type="checkbox" checked onclick="this.checked=!this.checked;" />
					<select name="fCodDependencia" id="fCodDependencia" style="width:215px;" <?=$dCodDependencia?>>
						<?=loadSelect2('mastdependencias','CodDependencia','Dependencia',$fCodDependencia,1);?>
					</select>
					<?php
				} else {
					?>
		            <input type="checkbox" <?=$cCodDependencia?> onclick="chkFiltro(this.checked, 'fCodDependencia');" />
					<select name="fCodDependencia" id="fCodDependencia" style="width:215px;" <?=$dCodDependencia?>>
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
				<select name="fIdPrograma" id="fIdPrograma" style="width:215px;" <?=$dIdPrograma?> onChange="loadSelect($('#fIdSubPrograma'), 'tabla=pv_subprogramas&IdPrograma='+$('#fIdPrograma').val(), 1, ['fIdProyecto','fIdActividad']);">
					<option value="">&nbsp;</option>
					<?=loadSelect2('pv_programas','IdPrograma','Denominacion',$fIdPrograma,0,['IdSubSector'],[$fIdSubSector],'CodPrograma')?>
				</select>
			</td>
			<td align="right">Actividad:</td>
			<td>
				<input type="checkbox" <?=$cIdActividad?> onclick="chkCampos(this.checked, 'fIdActividad');" />
				<select name="fIdActividad" id="fIdActividad" style="width:215px;" <?=$dIdActividad?>>
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
				<select name="fCodUnidadEjec" id="fCodUnidadEjec" style="width:215px;" <?=$dCodUnidadEjec?>>
					<option value="">&nbsp;</option>
					<?=loadSelect2('pv_unidadejecutora','CodUnidadEjec','Denominacion',$fCodUnidadEjec,10,['CodOrganismo'],[$fCodOrganismo]);?>
				</select>
			</td>
			<td align="right" width="100">Sub-Programa:</td>
			<td>
				<input type="checkbox" <?=$cIdSubPrograma?> onclick="chkCampos(this.checked, 'fIdSubPrograma');" onChange="loadSelect($('#fIdProyecto'), 'tabla=pv_proyectos&IdSubPrograma='+$('#fIdSubPrograma').val(), 1, ['fIdActividad']);" />
				<select name="fIdSubPrograma" id="fIdSubPrograma" style="width:215px;" <?=$dIdSubPrograma?> onChange="loadSelect($('#fIdProyecto'), 'tabla=pv_proyectos&IdSubPrograma='+$('#fIdSubPrograma').val(), 1, ['fIdActividad']);">
					<option value="">&nbsp;</option>
					<?=loadSelect2('pv_subprogramas','IdSubPrograma','Denominacion',$fIdSubPrograma,0,['IdPrograma'],[$fIdPrograma],'CodSubPrograma')?>
				</select>
			</td>
			<td align="right">Estado: </td>
			<td>
	            <input type="checkbox" <?=$cEstado?> onclick="this.checked=!this.checked;" />
	            <select name="fEstado" id="fEstado" style="width:100px;" <?=$dEstado?>>
	                <?=loadSelectGeneral("presupuesto-estado", $fEstado, 1)?>
	            </select>
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

<center>
<div class="scroll" style="overflow:scroll; height:225px; width:100%; min-width:<?=$_width?>px;">
	<table class="tblLista" style="width:100%; min-width:1000px;">
		<thead>
		    <tr>
		        <th width="75" onclick="order('CodPresupuesto')">C&oacute;digo</th>
		        <th width="125" onclick="order('CategoriaProg')">Cat. Program&aacute;tica</th>
		        <th align="left" onclick="order('UnidadEjecutora')">Unidad Ejecutora</th>
		        <th width="75" onclick="order('Ejercicio')">Ejercicio</th>
		        <th width="100" align="right" onclick="order('MontoAprobado')">Monto Presupuestado</th>
		        <th width="100" align="right" onclick="order('MontoAjustado')">Monto Ajustado</th>
		        <th width="100" onclick="order('Estado')">Estado</th>
		    </tr>
	    </thead>
	    
	    <tbody>
		<?php
		//	consulto todos
		$sql = "SELECT p.*
				FROM
					pv_presupuesto p
					INNER JOIN pv_categoriaprog cp ON (cp.CategoriaProg = p.CategoriaProg)
					INNER JOIN pv_actividades a ON (a.IdActividad = cp.IdActividad)
					INNER JOIN pv_proyectos py ON (py.IdProyecto = a.IdProyecto)
					INNER JOIN pv_subprogramas spr ON (spr.IdSubPrograma = py.IdSubPrograma)
					INNER JOIN pv_programas pr ON (pr.IdPrograma = spr.IdPrograma)
					INNER JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cp.CodUnidadEjec)
					INNER JOIN pv_unidadejecutoradep ued ON (ued.CodUnidadEjec = ue.CodUnidadEjec)
					INNER JOIN mastorganismos o ON (o.CodOrganismo = p.CodOrganismo)
				WHERE 1 $filtro
				GROUP BY CodOrganismo, CodPresupuesto";
		$rows_total = getNumRows3($sql);
		//	consulto lista
		$sql = "SELECT
					p.*,
					a.Denominacion AS Actividad,
					ue.Denominacion AS UnidadEjecutora,
					o.Organismo,
					CONCAT(ss.CodSector, pr.CodPrograma, a.CodActividad) AS CatProg,
					ss.CodClaSectorial,
					pr.CodPrograma,
					spr.CodSubPrograma,
					py.CodProyecto,
					a.CodActividad,
					ss.IdSubSector,
					pr.IdPrograma,
					spr.IdSubPrograma,
					py.IdProyecto,
					a.IdActividad,
					ue.CodUnidadEjec
				FROM
					pv_presupuesto p
					INNER JOIN pv_categoriaprog cp ON (cp.CategoriaProg = p.CategoriaProg)
					INNER JOIN pv_actividades a ON (a.IdActividad = cp.IdActividad)
					INNER JOIN pv_proyectos py ON (py.IdProyecto = a.IdProyecto)
					INNER JOIN pv_subprogramas spr ON (spr.IdSubPrograma = py.IdSubPrograma)
					INNER JOIN pv_programas pr ON (pr.IdPrograma = spr.IdPrograma)
					INNER JOIN pv_subsector ss ON (ss.IdSubSector = pr.IdSubSector)
					INNER JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cp.CodUnidadEjec)
					INNER JOIN pv_unidadejecutoradep ued ON (ued.CodUnidadEjec = ue.CodUnidadEjec)
					INNER JOIN mastorganismos o ON (o.CodOrganismo = p.CodOrganismo)
				WHERE 1 $filtro
				GROUP BY CodOrganismo, CodPresupuesto
				ORDER BY $fOrderBy
				LIMIT ".intval($limit).", ".intval($maxlimit);
		$field = getRecords($sql);
		$rows_lista = count($field);
		foreach($field as $f) {
			if ($ventana == 'pv_reformulacion') {
				?>
	        	<tr class="trListaBody" onClick="selLista(['<?=$f['CodPresupuesto']?>','<?=$f['Ejercicio']?>','<?=$f['CodOrganismo']?>','<?=$f['Organismo']?>','<?=$f['CodUnidadEjec']?>','<?=$f['UnidadEjecutora']?>','<?=$f['CategoriaProg']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>','<?=$campo4?>','<?=$campo5?>','<?=$campo6?>','<?=$campo7?>']);">
				<?php
			}
			elseif ($ventana == 'pv_ajustes') {
				?>
	        	<tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['CodPresupuesto']?>','<?=$f['Ejercicio']?>','<?=$f['CodOrganismo']?>','<?=$f['Organismo']?>','<?=$f['CodUnidadEjec']?>','<?=$f['UnidadEjecutora']?>','<?=$f['CategoriaProg']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>','<?=$campo4?>','<?=$campo5?>','<?=$campo6?>','<?=$campo7?>']);">
				<?php
			}
			elseif ($ventana == 'pv_ajuste') {
				?>
	        	<tr class="trListaBody" onClick="selLista(['<?=$f['Ejercicio']?>','<?=$f['CodPresupuesto']?>','<?=$f['CategoriaProg']?>','<?=$f['CodOrganismo']?>','<?=$f['Organismo']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>','<?=$campo4?>','<?=$campo5?>']);">
				<?php
			}
			elseif ($ventana == "selListadoListaParent") {
				?><tr class="trListaBody" onclick="selListadoListaParent('<?=$seldetalle?>',['<?=$campo1?>'],['<?=$f['CategoriaProg']?>']);" id="<?=$f['CategoriaProg']?>"><?php
			}
			elseif ($ventana == "selListadoListaParentRequerimiento") {
				?><tr class="trListaBody" onclick="selListadoListaParent('<?=$seldetalle?>',['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>'],['<?=$f['CategoriaProg']?>','<?=$f['Ejercicio']?>','<?=$f['CodPresupuesto']?>']);" id="<?=$f['CategoriaProg']?>"><?php
			}
			elseif ($ventana == "CategoriaProg") {
				?><tr class="trListaBody" onClick="selLista(['<?=$f['CodOrganismo']?>','<?=$f['Ejercicio']?>','<?=$f['CodPresupuesto']?>','<?=$f['CategoriaProg']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>','<?=$campo4?>']);"><?php
			}
			elseif ($ventana == "lg_requerimiento") {
				?><tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['Ejercicio']?>','<?=$f['CodPresupuesto']?>','<?=$f['CategoriaProg']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>']);"><?php
			}
			elseif ($ventana == "categorias") {
				?><tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['Ejercicio']?>','<?=$f['CodPresupuesto']?>','<?=$f['CategoriaProg']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>']);"><?php
			}
			elseif ($ventana == "lg_requerimiento_opener") {
				?><tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['Ejercicio']?>','<?=$f['CodPresupuesto']?>','<?=$f['CategoriaProg']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>']);"><?php
			}
			elseif ($ventana == "rh_bono_periodos") {
				?><tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['Ejercicio']?>','<?=$f['CodPresupuesto']?>','<?=$f['CategoriaProg']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>']);"><?php
			}
			elseif ($ventana == 'ob_obras') {
				?><tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['CategoriaProg']?>','<?=$f['IdSubSector']?>','<?=$f['IdPrograma']?>','<?=$f['IdSubPrograma']?>','<?=$f['IdProyecto']?>','<?=$f['IdActividad']?>','<?=$f['CodUnidadEjec']?>','<?=$f['Ejercicio']?>','<?=$f['CodPresupuesto']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>','<?=$campo4?>','<?=$campo5?>','<?=$campo6?>','<?=$campo7?>','<?=$campo8?>','<?=$campo9?>']);"><?php
			}
			else {
				?><tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['CodPresupuesto']?>','<?=$f['CodOrganismo']?>','<?=$f['Ejercicio']?>'], ['<?=$campo1?>','<?=$campo2?>']);"><?php
			}
			?>
				<td align="center"><?=$f['CodPresupuesto']?></td>
				<td align="center"><?=$f['CategoriaProg']?></td>
				<td><?=htmlentities($f['Actividad'])?></td>
				<td align="center"><?=$f['Ejercicio']?></td>
				<td align="right"><?=number_format($f['MontoAprobado'],2,',','.')?></td>
				<td align="right"><?=number_format($f['MontoAjustado'],2,',','.')?></td>
				<td align="center"><?=printValoresGeneral('presupuesto-estado',$f['Estado'])?></td>
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
	elseif ($ventana == 'lg_requerimiento') {
		?>
		function lg_requerimiento(valores, inputs) {
			if (inputs) {
				for(var i=0; i<inputs.length; i++) {
					if (parent.$("."+inputs[i]).length > 0) parent.$("."+inputs[i]).val(valores[i]);
					else if (parent.$("#"+inputs[i]).length > 0) parent.$("#"+inputs[i]).val(valores[i]);
				}
			}
			parent.$.prettyPhoto.close();
		}
		<?php
	}
	elseif ($ventana == 'lg_requerimiento_opener') {
		?>
		function lg_requerimiento_opener(valores, inputs) {
			if (inputs) {
				for(var i=0; i<inputs.length; i++) {
					if (opener.$("."+inputs[i]).length > 0) opener.$("."+inputs[i]).val(valores[i]);
					else if (opener.$("#"+inputs[i]).length > 0) opener.$("#"+inputs[i]).val(valores[i]);
				}
			}
			window.close();
		}
		<?php
	}
	elseif ($ventana == 'rh_bono_periodos') {
		?>
		function rh_bono_periodos(inputs, valores) {
			selLista(inputs, valores);
			parent.setHrefPartida();
			parent.$('#cod_partida').val('');
		}
		<?php
	}
	elseif ($ventana == 'categorias') {
		?>
		function categorias(valores, inputs) {
			if (inputs) {
				for(var i=0; i<inputs.length; i++) {
					if (parent.$("#"+inputs[i]).length > 0) parent.$("#"+inputs[i]).val(valores[i]);
					if (parent.$("."+inputs[i]).length > 0) parent.$("."+inputs[i]).val(valores[i]);
				}
			}
			parent.$.prettyPhoto.close();
		}
		<?php
	}
	elseif ($ventana == 'ob_obras') {
		?>
		function ob_obras(valores, inputs) {
			$.ajax({
				type: "POST",
				url: "../../ob/ob_obras_ajax.php",
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
	?>
</script>