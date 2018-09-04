<?php
if (!$ventana) $ventana = "selLista";
//	------------------------------------
if ($filtrar == "default") {
	$fEstado = 'A';
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "CodProyecto,CodActividad";
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (a.CodActividad LIKE '%".$fBuscar."%' OR
					  a.Descripcion LIKE '%".$fBuscar."%' OR
					  p.Descripcion LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (a.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fCodProyecto != "") { $cCodProyecto = "checked"; $filtro.=" AND (a.CodProyecto = '".$fCodProyecto."')"; } else $dCodProyecto = "visibility:hidden;";
if ($fTipoActividad != "") { $cTipoActividad = "checked"; $filtro.=" AND (a.TipoActividad = '".$fTipoActividad."')"; } else $dTipoActividad = "disabled";
//	------------------------------------
$_titulo = "Actividades";
$_width = 700;
?>
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lista_po_actividades" method="post" autocomplete="off">
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
	<input type="hidden" name="FlagProyecto" id="FlagProyecto" value="<?=$FlagProyecto?>" />
	<input type="hidden" name="CodProyecto" id="CodProyecto" value="<?=$CodProyecto?>" />
	<input type="hidden" name="CodRecurso" id="CodRecurso" value="<?=$CodRecurso?>" />

    <!--FILTRO-->
    <div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
        <table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
			<tr>
				<td align="right" width="100">Proyecto:</td>
				<td class="gallery clearfix">
		            <?php
		            if ($FlagProyecto == 'S') {
		            	?><input type="checkbox" <?=$cCodProyecto?> onclick="this.checked=!this.checked;" /><?php
		            } else {
		            	?><input type="checkbox" <?=$cCodProyecto?> onclick="chkListado(this.checked, 'btProyecto', 'fCodProyecto', 'fNomProyecto');" /><?php
		            }
		            ?>
		            <input type="text" name="fCodProyecto" id="fCodProyecto" value="<?=$fCodProyecto?>" style="width:40px;" readonly />
					<input type="text" name="fNomProyecto" id="fNomProyecto" style="width:255px;" value="<?=htmlentities($fNomProyecto)?>" readonly />
		        </td>
				<td align="right">Tipo de Actividad: </td>
				<td>
		            <input type="checkbox" <?=$cTipoActividad?> onclick="chkFiltro(this.checked, 'fTipoActividad');" />
		            <select name="fTipoActividad" id="fTipoActividad" style="width:150px;" <?=$dTipoActividad?>>
		                <option value="">&nbsp;</option>
		                <?=getMiscelaneos($fTipoActividad,'TIPOACTPO')?>
		            </select>
				</td>
			</tr>
			<tr>
				<td align="right" width="100">Buscar:</td>
				<td>
					<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
					<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:304px;" <?=$dBuscar?> />
				</td>
				<td align="right">Estado: </td>
				<td>
		            <input type="checkbox" <?=$cEstado?> onclick="this.checked=!this.checked;" />
		            <select name="fEstado" id="fEstado" style="width:150px;" <?=$dEstado?>>
		                <?=loadSelectGeneral("ESTADO", $fEstado, 1)?>
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

		<div style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px;">
		<table class="tblLista" style="width:100%; min-width:1300px;">
			<thead>
			    <tr>
			        <th width="75" onclick="order('CodActividad')">C&oacute;digo</th>
			        <th align="left" onclick="order('Descripcion')">Descripci&oacute;n</th>
			        <th width="400" align="left" onclick="order('Proyecto,Descripcion')">Proyecto</th>
			        <th width="200" align="left" onclick="order('NomTipoActividad')">Tipo de Actividad</th>
			        <th width="50" onclick="order('FlagAplicaEvaluacion')">Eval.</th>
			        <th width="75" onclick="order('FormaEvaluacion')">Forma Eval.</th>
			        <th width="75" onclick="order('Estado')">Estado</th>
			    </tr>
		    </thead>
		    
		    <tbody id="lista_registros">
		        <?php
				//	consulto todos
				$sql = "SELECT *
						FROM po_actividades a
						INNER JOIN po_proyectos p ON (p.CodProyecto = a.CodProyecto)
						LEFT JOIN mastmiscelaneosdet md ON (md.CodDetalle = a.TipoActividad AND
															md.CodMaestro = 'TIPOACTPO' AND
															md.CodAplicacion = 'PO')
						WHERE 1 $filtro";
				$rows_total = getNumRows3($sql);
				//	consulto lista
				$sql = "SELECT
							a.*,
							p.Descripcion AS Proyecto,
							md.Descripcion AS NomTipoActividad
						FROM po_actividades a
						INNER JOIN po_proyectos p ON (p.CodProyecto = a.CodProyecto)
						LEFT JOIN mastmiscelaneosdet md ON (md.CodDetalle = a.TipoActividad AND
															md.CodMaestro = 'TIPOACTPO' AND
															md.CodAplicacion = 'PO')
						WHERE 1 $filtro
						ORDER BY $fOrderBy
						LIMIT ".intval($limit).", ".intval($maxlimit);
				$field = getRecords($sql);
				$rows_lista = count($field);
				foreach($field as $f) {
					$id = $f['CodActividad'];
					if ($ventana == "actividad_poa") {
						?><tr class="trListaBody" onClick="actividad_poa('<?=$f['CodActividad']?>','<?=$f['CodProyecto']?>');"><?php
					} else {
						?><tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['CodActividad']?>','<?=$f['Descripcion']?>'], ['<?=$campo1?>','<?=$campo2?>']);"><?php
					}
		            ?>
						<td align="center"><?=$f['CodActividad']?></td>
						<td><?=htmlentities($f['Descripcion'])?></td>
						<td><?=htmlentities($f['Proyecto'])?></td>
						<td><?=htmlentities($f['NomTipoActividad'])?></td>
						<td align="center"><?=printFlag($f['FlagAplicaEvaluacion'])?></td>
						<td align="center"><?=printValoresGeneral('forma-evaluacion-poa',$f['FormaEvaluacion'])?></td>
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

<script type="text/javascript">
	<?php
	if ($ventana == 'actividad_poa') {
		?>
		function actividad_poa(CodActividad, codigo_proyecto) {
			var codigo_actividad = CodActividad;
			var CodProyecto = $('#CodProyecto').val();
			if (CodProyecto.length > 5) var CodActividad = CodProyecto.substr(0, 4) + CodActividad;

			var detalle = 'actividades' + CodProyecto;
			var idtr = detalle + '_' + CodActividad;
			var url = '../../po/po_poa_ajax.php';
			var data = 'modulo=ajax&accion=actividades_insertar&CodActividad='+CodActividad+'&CodProyecto='+CodProyecto+'&codigo_actividad='+codigo_actividad+'&CodRecurso='+$('#CodRecurso').val();
			//	lista
			var nro_detalles = parent.$("#nro_"+detalle);
			var can_detalles = parent.$("#can_"+detalle);
			var lista_detalles = parent.$("#lista_"+detalle);
			var nro = new Number(nro_detalles.val());	nro++;
			var can = new Number(can_detalles.val());	can++;
			//	ajax
			$.ajax({
				type: "POST",
				url: url,
				data: "nro_actividades="+nro+"&can_detalles="+can+"&"+data,
				async: false,
				success: function(resp) {
					if (parent.document.getElementById(idtr)) cajaModal("Registro ya insertado", "error_lista", 400);
					else {
						nro_detalles.val(nro);
						can_detalles.val(can);
						lista_detalles.append(resp);
						parent.$.prettyPhoto.close();
						inicializarParent();
					}
				}
			});
		}
		<?php
	}
	?>
</script>