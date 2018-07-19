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
	$opt_modificar = 3;
	$label_submit = "Guardar";
	$focus = "Denominacion";
}
elseif ($opcion == "modificar" || $opcion == "ver") {
	##	consulto datos generales
	$sql = "SELECT
	            ue.*,
	            p.NomCompleto As NomPersona,
	            cc.Codigo AS CodigoCC,
	            cc.Descripcion AS NomCentroCosto
			FROM
			    pv_unidadejecutora ue
			    LEFT JOIN mastpersonas p ON (p.codPersona = ue.CodPersona)
			    LEFT JOIN ac_mastcentrocosto cc ON (cc.CodCentroCosto = ue.CodCentroCosto)
			WHERE ue.CodUnidadEjec = '".$sel_registros."'";
	$field = getRecord($sql);
	##
	if ($opcion == "modificar") {
		$_titulo = "Modificar Registro";
		$accion = "modificar";
		$disabled_modificar = "disabled";
		$disabled_ver = "";
		$display_submit = "";
		$opt_modificar = 1;
		$label_submit = "Modificar";
		$focus = "Denominacion";
	}
	##
	elseif ($opcion == "ver") {
		$_titulo = "Ver Registro";
		$accion = "";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$display_submit = "display:none;";
		$opt_modificar = 0;
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
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=pv_unidadejecutora_lista" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('pv_unidadejecutora_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
	<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
	<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
	<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
	<input type="hidden" name="fCodDependencia" id="fCodDependencia" value="<?=$fCodDependencia?>" />
	<table width="<?=$_width?>" class="tblForm">
		<tr>
			<td colspan="2" class="divFormCaption">Datos Generales</td>
		</tr>
		<tr>
			<td class="tagForm" width="125">C&oacute;digo:</td>
			<td>
				<input type="text" name="CodUnidadEjec" id="CodUnidadEjec" value="<?=$field['CodUnidadEjec']?>" style="width:65px; font-weight:bold;" readonly="readonly" />
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Denominaci&oacute;n:</td>
			<td>
				<input type="text" name="Denominacion" id="Denominacion" value="<?=$field['Denominacion']?>" style="width:264px;" <?=$disabled_ver?> />
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Organismo:</td>
			<td>
				<select name="CodOrganismo" id="CodOrganismo" style="width:265px;" <?=$disabled_ver?> onChange="setHref(this.value);">
					<?=getOrganismos($field['CodOrganismo'], $opt_modificar);?>
				</select>
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Persona:</td>
			<td class="gallery clearfix">
				<input type="text" name="CodPersona" id="CodPersona" value="<?=$field['CodPersona']?>" style="width:50px;" readonly />
				<input type="text" name="NomPersona" id="NomPersona" value="<?=$field['NomPersona']?>" style="width:212px;" disabled />
				<a href="../lib/listas/gehen.php?anz=lista_personas&filtrar=default&campo1=CodPersona&campo2=NomPersona&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" style=" <?=$display_ver?>">
					<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
				</a>
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* C.Costo:</td>
			<td class="gallery clearfix">
				<input type="hidden" name="CodCentroCosto" id="CodCentroCosto" value="<?=$field['CodCentroCosto']?>" />
				<input type="text" name="CodigoCC" id="CodigoCC" value="<?=$field['CodigoCC']?>" style="width:50px;" disabled />
				<input type="text" name="NomCentroCosto" id="NomCentroCosto" value="<?=$field['NomCentroCosto']?>" style="width:212px;" disabled />
				<a href="../lib/listas/gehen.php?anz=lista_centro_costos&filtrar=default&campo1=CodCentroCosto&campo2=NomCentroCosto&campo3=CodigoCC&fCodOrganismo=<?=$field['CodOrganismo']?>&iframe=true&width=100%&height=430" rel="prettyPhoto[iframe2]" style=" <?=$display_ver?>" id="aCodCentroCosto">
					<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
				</a>
			</td>
		</tr>
		<tr>
			<td class="tagForm">&nbsp;</td>
			<td>
				<input type="checkbox" name="FlagEjecutorUnico" id="FlagEjecutorUnico" value="S" <?=chkOpt($field['FlagEjecutorUnico'], "S");?> <?=$disabled_ver?> onchange="setFlagEjecutorUnico(this.checked);" /> Ejecutor &Uacute;nico
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
	</table>
	<center>
	<input type="submit" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" id="btSubmit" />
	<input type="button" value="Cancelar" style="width:75px;" id="btCancelar" onclick="<?=$clkCancelar?>" />
	</center>

	<input type="hidden" id="sel_dep" />
	<table width="<?=$_width?>" class="tblBotones">
		<thead>
			<tr>
				<th class="divFormCaption">Dependencias</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td align="right" class="gallery clearfix">
					<a id="a_dep" href="../lib/listas/gehen.php?anz=lista_dependencias&filtrar=default&ventana=listado_insertar_linea&detalle=dep&modulo=ajax&accion=dep_insertar&url=../../pv/pv_unidadejecutora_ajax.php&fCodOrganismo=<?=$field['CodOrganismo']?>&flagOrganismo=S&iframe=true&width=950&height=430" rel="prettyPhoto[iframe3]" style="display:none;"></a>
					<input type="button" id="btInsertar" class="btLista" value="Insertar" onclick="$('#a_dep').click();" <?=$disabled_ver?> />
					<input type="button" id="btBorrar" class="btLista" value="Borrar" onclick="quitar(this, 'dep');" <?=$disabled_ver?> />
				</td>
			</tr>
		</tbody>
	</table>
	<div style="overflow:scroll; height:150px; width:<?=$_width?>px; margin:auto;">
		<table class="tblLista" style="width:1300px;">
			<thead>
				<tr>
					<th width="20">#</th>
					<th width="75">C&oacute;digo</th>
					<th align="left">Dependencia</th>
				</tr>
			</thead>
			
			<tbody id="lista_dep">
				<?php
				$nro_dep = 0;
				$sql = "SELECT
							ued.*,
							d.Dependencia
						FROM
							pv_unidadejecutoradep ued
							INNER JOIN mastdependencias d ON (d.CodDependencia = ued.CodDependencia)
						WHERE ued.CodUnidadEjec = '".$field['CodUnidadEjec']."'";
				$field_dep = getRecords($sql);
				foreach ($field_dep as $f) {
					$id = $f['CodDependencia'];
					?>
					<tr class="trListaBody" onclick="clk($(this), 'dep', 'dep_<?=$id?>');" id="dep_<?=$id?>">
						<th>
							<input type="hidden" name="dep_CodDependencia[]" value="<?=$id?>" />
							<input type="hidden" name="dep_Dependencia[]" value="<?=$f['Dependencia']?>" />
							<?=++$nro_dep;?>
						</th>
						<td align="center"><?=$f['CodDependencia']?></td>
						<td><?=htmlentities($f['Dependencia'])?></td>
					</tr>
				<?php
				}
				?>
			</tbody>
		</table>
	</div>
	<input type="hidden" id="nro_dep" value="<?=$nro_dep?>" />
	<input type="hidden" id="can_dep" value="<?=$nro_dep?>" />
</form>
<div style="width:<?=$_width?>px" class="divMsj">Campos Obligatorios *</div>

<script type="text/javascript" language="javascript">
	function setHref(fCodOrganismo) {
		$('#a_dep').attr('href', '../lib/listas/gehen.php?anz=lista_dependencias&filtrar=default&ventana=listado_insertar_linea&detalle=dep&modulo=ajax&accion=dep_insertar&url=../../pv/pv_unidadejecutora_ajax.php&fCodOrganismo='+fCodOrganismo+'&flagOrganismo=S&iframe=true&width=950&height=430');
		$('#lista_dep').html('');
		$('#nro_dep').val('0');
		$('#can_dep').val('0');
		//	
		$('#aCodCentroCosto').attr('href', "../lib/listas/gehen.php?anz=lista_centro_costos&filtrar=default&campo1=CodCentroCosto&campo2=NomCentroCosto&campo3=CodigoCC&fCodOrganismo="+fCodOrganismo+"&iframe=true&width=100%&height=430");
		$('#CodCentroCosto').val('');
		$('#NomCentroCosto').val('');
		$('#CodigoCC').val('');
	}
	function setFlagEjecutorUnico(checked) {
		$('#lista_dep').html('');
		$('#btInsertar').attr('disabled', !checked);
		$('#btBorrar').attr('disabled', !checked);
	}
</script>