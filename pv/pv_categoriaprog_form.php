<?php
if ($opcion == "nuevo") {
	$field['CodOrganismo'] = ($fCodOrganismo?$fCodOrganismo:$_SESSION["FILTRO_ORGANISMO_ACTUAL"]);
	$field['Estado'] = 'A';
	##
	$_titulo = "Nuevo Registro";
	$accion = "nuevo";
	$disabled_ver = "";
	$disabled_modificar = "";
	$display_submit = "";
	$label_submit = "Guardar";
	$focus = "CodOrganismo";
}
elseif ($opcion == "modificar" || $opcion == "ver") {
	##	consulto datos generales
	$sql = "SELECT
				cp.*,
				a.IdProyecto,
				py.IdSubPrograma,
				spg.IdPrograma,
				pg.IdSubSector,
				cc.Codigo AS CodigoCC,
				cc.Descripcion AS NomCentroCosto
			FROM
				pv_categoriaprog cp
				INNER JOIN pv_actividades a ON (a.IdActividad = cp.IdActividad)
				INNER JOIN pv_proyectos py ON (py.IdProyecto = a.IdProyecto)
				INNER JOIN pv_subprogramas spg ON (spg.IdSubPrograma = py.IdSubPrograma)
				INNER JOIN pv_programas pg ON (pg.IdPrograma = spg.IdPrograma)
				INNER JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cp.CodUnidadEjec)
				LEFT JOIN ac_mastcentrocosto cc ON (cc.CodCentroCosto = ue.CodCentroCosto)
			WHERE cp.CategoriaProg = '".$sel_registros."'";
	$field = getRecord($sql);
	##
	if ($opcion == "modificar") {
		$_titulo = "Modificar Registro";
		$accion = "modificar";
		$disabled_ver = "";
		$disabled_modificar = "disabled";
		$display_submit = "";
		$label_submit = "Modificar";
		$focus = "Denominacion";
	}
	##
	elseif ($opcion == "ver") {
		$_titulo = "Ver Registro";
		$accion = "";
		$disabled_ver = "disabled";
		$disabled_modificar = "disabled";
		$display_submit = "display:none;";
		$label_submit = "";
		$focus = "btCancelar";
	}
}
$clkCancelar = "document.getElementById('frmentrada').submit();";
//	------------------------------------
$_width = 600;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=pv_categoriaprog_lista" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('pv_categoriaprog_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
	<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
	<input type="hidden" name="fCodDependencia" id="fCodDependencia" value="<?=$fCodDependencia?>" />
	<input type="hidden" name="fCodUnidadEjec" id="fCodUnidadEjec" value="<?=$fCodUnidadEjec?>" />
	<input type="hidden" name="fIdSubSector" id="fIdSubSector" value="<?=$fIdSubSector?>" />
	<input type="hidden" name="fIdPrograma" id="fIdPrograma" value="<?=$fIdPrograma?>" />
	<input type="hidden" name="fIdSubPrograma" id="fIdSubPrograma" value="<?=$fIdSubPrograma?>" />
	<input type="hidden" name="fIdProyecto" id="fIdProyecto" value="<?=$fIdProyecto?>" />
	<input type="hidden" name="fIdActividad" id="fIdActividad" value="<?=$fIdActividad?>" />

	<table width="<?=$_width?>" class="tblForm">
		<tr>
	    	<td colspan="2" class="divFormCaption">Datos Generales</td>
	    </tr>
	    <tr>
			<td class="tagForm" width="125">C&oacute;digo:</td>
			<td>
	        	<input type="text" name="CategoriaProg" id="CategoriaProg" value="<?=$field['CategoriaProg']?>" style="width:100px; font-weight:bold;" readonly="readonly" />
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Organismo:</td>
			<td>
				<select name="CodOrganismo" id="CodOrganismo" style="width:300px;" <?=$disabled_modificar?> onChange="loadSelect($('#CodUnidadEjec'), 'tabla=pv_unidadejecutora&CodOrganismo='+$('#CodOrganismo').val(), 1);">
					<?=getOrganismos($field['CodOrganismo'], 3);?>
				</select>
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Sub-Sector:</td>
			<td>
				<select name="IdSubSector" id="IdSubSector" style="width:300px;" <?=$disabled_modificar?> onChange="loadSelect($('#IdPrograma'), 'tabla=pv_programas&IdSubSector='+$('#IdSubSector').val(), 1, ['IdSubPrograma','IdProyecto','IdActividad']); setCategoriaProg();">
					<option value="">&nbsp;</option>
					<?=loadSelect2('pv_subsector','IdSubSector','Denominacion',$field['IdSubSector'],0,NULL,NULL,'CodClaSectorial');?>
				</select>
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Programa:</td>
			<td>
				<select name="IdPrograma" id="IdPrograma" style="width:300px;" <?=$disabled_modificar?> onChange="loadSelect($('#IdSubPrograma'), 'tabla=pv_subprogramas&IdPrograma='+$('#IdPrograma').val(), 1, ['IdProyecto','IdActividad']); setCategoriaProg();">
					<?=loadSelect2('pv_programas','IdPrograma','Denominacion',$field['IdPrograma'],0,['IdSubSector'],[$field['IdSubSector']],'CodPrograma');?>
				</select>
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Sub-Programa:</td>
			<td>
				<select name="IdSubPrograma" id="IdSubPrograma" style="width:300px;" <?=$disabled_modificar?> onChange="loadSelect($('#IdProyecto'), 'tabla=pv_proyectos&IdSubPrograma='+$('#IdSubPrograma').val(), 1, ['IdActividad']); setCategoriaProg();">
					<?=loadSelect2('pv_subprogramas','IdSubPrograma','Denominacion',$field['IdSubPrograma'],0,['IdPrograma'],[$field['IdPrograma']],'CodSubPrograma');?>
				</select>
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Proyecto:</td>
			<td>
				<select name="IdProyecto" id="IdProyecto" style="width:300px;" <?=$disabled_modificar?> onChange="loadSelect($('#IdActividad'), 'tabla=pv_actividades&IdProyecto='+$('#IdProyecto').val(), 1); setCategoriaProg();">
					<?=loadSelect2('pv_proyectos','IdProyecto','Denominacion',$field['IdProyecto'],0,['IdSubPrograma'],[$field['IdSubPrograma']],'CodProyecto');?>
				</select>
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Actividad:</td>
			<td>
				<select name="IdActividad" id="IdActividad" style="width:300px;" <?=$disabled_modificar?> onchange="setCategoriaProg();">
					<?=loadSelect2('pv_actividades','IdActividad','Denominacion',$field['IdActividad'],0,['IdProyecto'],[$field['IdProyecto']],'CodActividad');?>
				</select>
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Unidad Ejecutora:</td>
			<td>
				<select name="CodUnidadEjec" id="CodUnidadEjec" style="width:300px;" <?=$disabled_modificar?> onchange="getDependenciasxUnidadEjecutora(this.value); setCategoriaProg(); setCentroCosto(this.value);">
					<option value="">&nbsp;</option>
					<?=loadSelect2('pv_unidadejecutora','CodUnidadEjec','Denominacion',$field['CodUnidadEjec'],10,['CodOrganismo'],[$field['CodOrganismo']]);?>
				</select>
			</td>
		</tr>
	    <tr>
			<td class="tagForm">C.Costo:</td>
			<td>
				<input type="hidden" name="CodCentroCosto" id="CodCentroCosto" value="<?=$field['CodCentroCosto']?>" />
				<input type="text" name="CodigoCC" id="CodigoCC" value="<?=$field['CodigoCC']?>" style="width:50px;" disabled />
				<input type="text" name="NomCentroCosto" id="NomCentroCosto" value="<?=$field['NomCentroCosto']?>" style="width:245px;" disabled />
			</td>
		</tr>
		<tr>
			<td class="tagForm">Estado:</td>
			<td>
	            <input type="radio" name="Estado" id="Activo" value="A" <?=chkOpt($field['Estado'], "A");?> <?=$disabled_ver?> /> Activo
	            &nbsp; &nbsp;
	            <input type="radio" name="Estado" id="Inactivo" value="I" <?=chkOpt($field['Estado'], "I");?> <?=$disabled_ver?> /> Inactivo
			</td>
		</tr>
	    <tr>
			<td class="tagForm">&Uacute;ltima Modif.:</td>
			<td>
				<input type="text" value="<?=$field['UltimoUsuario']?>" style="width:150px;" disabled="disabled" />
				<input type="text" value="<?=$field['UltimaFecha']?>" style="width:100px" disabled="disabled" />
			</td>
		</tr>
		<tr>
			<td colspan="2" class="divFormCaption">Dependencias</td>
		</tr>
		<tr>
			<td colspan="2">
				<div style="overflow:scroll; height:100px; width:100%;">
					<table class="tblLista" style="width:100%;">
						<tbody id="lista_dep">
							<?php
							foreach ($field_dependencias as $fd) {
								?>
								<tr class="trListaBody">
									<td align="center" width="40"><?=$fd['CodDependencia']?></td>
									<td><?=htmlentities($fd['Dependencia'])?></td>
								</tr>
								<?php
							}
							?>
						</tbody>
					</table>
				</div>
			</td>
		</tr>
	</table>

	<center>
		<input type="submit" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" id="btSubmit" />
		<input type="button" value="Cancelar" style="width:75px;" id="btCancelar" onclick="<?=$clkCancelar?>" />
	</center>
</form>
<div style="width:<?=$_width?>px" class="divMsj">Campos Obligatorios *</div>

<script type="text/javascript" language="javascript">
	function getDependenciasxUnidadEjecutora(CodUnidadEjec) {
		//	ajax
		$.ajax({
			type: "POST",
			url: "pv_categoriaprog_ajax.php",
			data: "modulo=ajax&accion=getDependenciasxUnidadEjecutora&CodUnidadEjec="+CodUnidadEjec,
			async: false,
			success: function(resp) {
				$('#lista_dep').html(resp);
			}
		});
	}
	function setCategoriaProg() {
		//	ajax
		$.ajax({
			type: "POST",
			url: "pv_categoriaprog_ajax.php",
			data: "modulo=ajax&accion=setCategoriaProg&IdActividad="+$('#IdActividad').val()+"&CodUnidadEjec="+$('#CodUnidadEjec').val(),
			async: false,
			success: function(resp) {
				$('#CategoriaProg').val(resp);
			}
		});
	}
	function setCentroCosto(CodUnidadEjec) {
		//	ajax
		$.ajax({
			type: "POST",
			url: "pv_categoriaprog_ajax.php",
			data: "modulo=ajax&accion=setCentroCosto&CodUnidadEjec="+CodUnidadEjec,
			async: false,
			dataType: "json",
			success: function(data) {
				$('#CodCentroCosto').val(data['CodCentroCosto']);
				$('#NomCentroCosto').val(data['NomCentroCosto']);
				$('#CodigoCC').val(data['CodigoCC']);
			}
		});
	}
</script>