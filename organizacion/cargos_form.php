<?php
if ($opcion == "nuevo") {
	$field['Estado'] = "A";
	##
	$titulo = "Nuevo Cargo";
	$accion = "nuevo";
	$disabled_nuevo = "disabled";
	$disabled_modificar = "";
	$disabled_ver = "";
	$display_submit = "";
	$label_submit = "Guardar";
	$clkCancelar = "document.getElementById('frmentrada').submit();";
	$focus = "CodTipoCargo";
}
elseif ($opcion == "modificar" || $opcion == "ver") {
	//	consulto datos generales
	$sql = "SELECT *
			FROM rh_puestos
			WHERE CodCargo = '".$sel_registros."'";
	$field = getRecord($sql);
	##
	if ($opcion == "modificar") {
		$titulo = "Modificar Cargo";
		$accion = "modificar";
		$disabled_nuevo = "";
		$disabled_modificar = "disabled";
		$disabled_ver = "";
		$display_submit = "";
		$label_submit = "Modificar";
		$clkCancelar = "document.getElementById('frmentrada').submit();";
		$focus = "DescripCargo";
	}
	##
	elseif ($opcion == "ver") {
		$titulo = "Ver Cargo";
		$accion = "";
		$disabled_nuevo = "disabled";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$display_submit = "display:none;";
		$label_submit = "";
		$clkCancelar = "document.getElementById('frmentrada').submit();";
		$focus = "btCancelar";
	}
}
//	------------------------------------
$_width = 950;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<table width="<?=$_width?>" align="center" cellpadding="0" cellspacing="0">
    <tr>
        <td>
            <div class="header">
                <ul id="tab">
                    <li id="li1"><a href="#" onclick="mostrarTab('tab', 1, 13);">Informaci&oacute;n General</a></li>
                    <li id="li2"><a href="#" onclick="mostrarTab('tab', 2, 13);">Relaciones</a></li>
                    <li id="li3"><a href="#" onclick="mostrarTab('tab', 3, 13);">Competencias</a></li>
                    <li id="li4"><a href="#" onclick="mostrarTab('tab', 4, 13);">Funciones</a></li>
                    <li id="li5"><a href="#" onclick="mostrarTab('tab', 5, 13);">Formaci&oacute;n</a></li>
                    <li id="li6"><a href="#" onclick="mostrarTab('tab', 6, 13);">Experiencia Previa</a></li>
                    <li id="li7"><a href="#" onclick="mostrarTab('tab', 7, 13);">Riesgos de Trabajo</a></li>
                    <li id="li8"><a href="#" onclick="mostrarTab('tab', 8, 13);">Evaluaci&oacute;n - Reclutamiento</a></li>
                    <li id="li9"><a href="#" onclick="mostrarTab('tab', 9, 13);">Puestos Subordinados</a></li>
                    <li id="li10"><a href="#" onclick="mostrarTab('tab', 10, 13);">Otros Estudios</a></li>
                    <li id="li11"><a href="#" onclick="mostrarTab('tab', 11, 13);">Objetivos y/o Metas</a></li>
                    <li id="li12"><a href="#" onclick="mostrarTab('tab', 12, 13);">Ambiente de Trabajo</a></li>
                    <li id="li13"><a href="#" onclick="mostrarTab('tab', 13, 13);">Habilidades / Destrezas</a></li>
                </ul>
            </div>
        </td>
    </tr>
</table>

<div id="tab1" style="display:block;">
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=cargos_lista" method="POST" enctype="multipart/form-data" onsubmit="return formulario(this, '<?=$accion?>');" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
<input type="hidden" name="fCodGrupOcup" id="fCodGrupOcup" value="<?=$fCodGrupOcup?>" />
<input type="hidden" name="fCodTipoCargo" id="fCodTipoCargo" value="<?=$fCodTipoCargo?>" />
<input type="hidden" name="fCodSerieOcup" id="fCodSerieOcup" value="<?=$fCodSerieOcup?>" />
<input type="hidden" name="fCodNivelClase" id="fCodNivelClase" value="<?=$fCodNivelClase?>" />
<input type="hidden" name="fCategoriaCargo" id="fCategoriaCargo" value="<?=$fCategoriaCargo?>" />
<input type="hidden" name="fGrado" id="fGrado" value="<?=$fGrado?>" />
<input type="hidden" name="CodCargo" id="CodCargo" value="<?=$field['CodCargo']?>" />

<table width="<?=$_width?>" class="tblForm">
	<tr>
    	<td colspan="2" class="divFormCaption">Datos Generales</td>
    </tr>
	<tr>
		<td class="tagForm" width="150">* Tipo de Cargo:</td>
		<td>
            <select id="CodTipoCargo" style="width:125px;" <?=$disabled_ver?> onchange="loadSelect($('#CodNivelClase'), 'tabla=rh_nivelclasecargo&CodTipoCargo='+$(this).val(), 1); getDescripCargo();">
                <option value="">&nbsp;</option>
                <?=loadSelect2("rh_tipocargo", "CodTipoCargo", "TipCargo", $field['CodTipoCargo'], 0)?>
            </select>
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Nivel:</td>
		<td>
            <select id="CodNivelClase" style="width:125px;" <?=$disabled_ver?> onchange="getDescripCargo();">
                <option value="">&nbsp;</option>
                <?=loadSelect2("rh_nivelclasecargo", "CodNivelClase", "NivelClase", $field['CodNivelClase'], 0, array('CodTipoCargo'), array($field['CodTipoCargo']))?>
            </select>
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Grupo Ocupacional:</td>
		<td>
            <select id="CodGrupOcup" style="width:275px;" <?=$disabled_ver?> onchange="loadSelect($('#CodSerieOcup'), 'tabla=rh_serieocupacional&CodGrupOcup='+$(this).val(), 1);">
                <option value="">&nbsp;</option>
                <?=loadSelect2("rh_grupoocupacional", "CodGrupOcup", "GrupoOcup", $field['CodGrupOcup'], 0)?>
            </select>
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Serie Ocupacional:</td>
		<td>
            <select id="CodSerieOcup" style="width:275px;" <?=$disabled_ver?> onchange="getDescripCargo();">
                <option value="">&nbsp;</option>
                <?=loadSelect2("rh_serieocupacional", "CodSerieOcup", "SerieOcup", $field['CodSerieOcup'], 0, array('CodGrupOcup'), array($field['CodGrupOcup']))?>
            </select>
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Clasificaci&oacute;n:</td>
		<td>
        	<input type="text" id="CodDesc" value="<?=$field['CodDesc']?>" style="width:50px;" maxlength="4" <?=$disabled_ver?> />
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Descripci&oacute;n:</td>
		<td>
        	<input type="text" id="DescripCargo" value="<?=$field['DescripCargo']?>" style="width:95%;" maxlength="45" <?=$disabled_ver?> />
		</td>
	</tr>
	<tr>
		<td class="tagForm">Descripci&oacute;n Gen&eacute;rica:</td>
		<td>
        	<textarea id="DescGenerica" style="width:95%; height:50px;" <?=$disabled_ver?>><?=$field['DescGenerica']?></textarea>
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Categor&iacute;a:</td>
		<td>
            <select id="CategoriaCargo" style="width:125px;" <?=$disabled_ver?> onchange="loadSelect($('#Grado'), 'tabla=gradosalarial&CategoriaCargo='+$(this).val(), 1, ['Paso','NivelSalarial']); getSueldoPromedio($('#CategoriaCargo').val(),$('#Grado').val(),$('#Paso').val(),$('#NivelSalarial'));">
                <option value="">&nbsp;</option>
                <?=getMiscelaneos($field['CategoriaCargo'], "CATCARGO", 0)?>
            </select>
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Grado Salarial:</td>
		<td>
            <select id="Grado" style="width:55px;" <?=$disabled_ver?> onchange="loadSelect($('#Paso'), 'tabla=pasosalarial&CategoriaCargo='+$('#CategoriaCargo').val()+'&Grado='+$(this).val(), 1, ['NivelSalarial']); getSueldoPromedio($('#CategoriaCargo').val(),$('#Grado').val(),$('#Paso').val(),$('#NivelSalarial'));">
                <option value="">&nbsp;</option>
                <?=loadSelect2("rh_nivelsalarial", "Grado", "Grado", $field['Grado'], 30, array('CategoriaCargo'), array($field['CategoriaCargo']))?>
            </select>
            <select id="Paso" style="width:55px;" <?=$disabled_ver?> onchange="getSueldoPromedio($('#CategoriaCargo').val(),$('#Grado').val(),$('#Paso').val(),$('#NivelSalarial'));">
                <option value="">&nbsp;</option>
                <?=loadSelect2("rh_nivelsalarial", "Paso", "Paso", $field['Paso'], 0, array('CategoriaCargo','Grado'), array($field['CategoriaCargo'],$field['Grado']))?>
            </select>
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Sueldo B&aacute;sico:</td>
		<td>
        	<input type="text" id="NivelSalarial" value="<?=number_format($field['NivelSalarial'], 2, ',', '.')?>" style="width:120px; text-align:right;" class="currency" <?=$disabled_ver?> />
		</td>
	</tr>
	<tr>
		<td class="tagForm">Plantilla de Competencias:</td>
		<td>
            <select id="Plantilla" style="width:275px;" <?=$disabled_ver?>>
                <option value="">&nbsp;</option>
                <?=loadSelect2("rh_evaluacionfactoresplantilla", "Plantilla", "Descripcion", $field['Plantilla'], 0)?>
            </select>
		</td>
	</tr>
	<tr>
		<td class="tagForm">Estado:</td>
		<td>
            <input type="radio" name="Estado" id="Activo" value="A" <?=chkOpt($field['Estado'], "A");?> <?=$disabled_nuevo?> /> Activo
            &nbsp; &nbsp;
            <input type="radio" name="Estado" id="Inactivo" value="I" <?=chkOpt($field['Estado'], "I");?> <?=$disabled_nuevo?> /> Inactivo
		</td>
	</tr>
    <tr>
		<td class="tagForm">&Uacute;ltima Modif.:</td>
		<td>
			<input type="text" size="30" value="<?=$field['UltimoUsuario']?>" disabled="disabled" />
			<input type="text" size="25" value="<?=$field['UltimaFecha']?>" disabled="disabled" />
		</td>
	</tr>
</table>

<center>
<input type="submit" style="display:none;" />
</center>
</form>
</div>

<div id="tab2" style="display:none;">
<center>
<form name="frm_cargoreporta" id="frm_cargoreporta" autocomplete="off">
<input type="hidden" id="sel_cargoreporta" />
<table width="<?=$_width?>" class="tblBotones">
	<thead>
    	<th class="divFormCaption">¿A quienes reporta?</th>
    </thead>
    <tbody>
    <tr>
        <td align="right" class="gallery clearfix">
            <a id="a_cargoreporta" href="../lib/listas/listado_puestos.php?filtrar=default&ventana=cargos&detalle=cargoreporta&iframe=true&width=925&height=425" rel="prettyPhoto[iframe1]" style="display:none;"></a>
            <input type="button" class="btLista" value="Insertar" <?=$disabled_detalles?> onclick="$('#a_cargoreporta').click();" />
            <input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'cargoreporta');" <?=$disabled_detalles?> />
        </td>
    </tr>
    </tbody>
</table>
<div style="overflow:scroll; width:<?=$_width?>px; height:134px;">
<table width="100%" class="tblLista">
	<thead>
    <tr>
        <th width="20">#</th>
        <th width="60">C&oacute;digo</th>
        <th align="left">Cargo Superior</th>
    </tr>
    </thead>
    
    <tbody id="lista_cargoreporta">
    	<?php
		$sql = "SELECT
					cr.CargoReporta,
					p.CodDesc,
					p.DescripCargo
				FROM
					rh_cargoreporta cr
					INNER JOIN rh_puestos p ON (p.CodCargo = cr.CargoReporta)
				WHERE cr.CodCargo = '".$sel_registros."'
				ORDER BY p.CategoriaCargo, p.Grado DESC";
		$field_cargoreporta = getRecords($sql);
		foreach ($field_cargoreporta as $f) {
			$id = $f['CargoReporta'];
			?>
            <tr class="trListaBody" onclick="clk($(this), 'cargoreporta', 'cargoreporta_<?=$id?>');" id="cargoreporta_<?=$id?>">
                <th>
					<?=++$nro_cargoreporta?>
                    <input type="hidden" name="cargoreporta_CargoReporta[]" value="<?=$f['CargoReporta']?>" />
                </th>
                <td align="center"><?=$f['CodDesc']?></td>
                <td><?=htmlentities($f['DescripCargo'])?></td>
            </tr>
            <?php
		}
		?>
    </tbody>
</table>
</div>
<input type="hidden" id="nro_cargoreporta" value="<?=$nro_cargoreporta?>" />
<input type="hidden" id="can_cargoreporta" value="<?=$nro_cargoreporta?>" />
</form>

<form name="frm_cargorelaciones" id="frm_cargorelaciones" autocomplete="off">
<input type="hidden" id="sel_cargorelaciones" />
<table width="<?=$_width?>" class="tblBotones">
	<thead>
    	<th class="divFormCaption">Relaciones Externas e Internas</th>
    </thead>
    <tbody>
    <tr>
        <td align="right">
            <input type="button" class="btLista" value="Insertar" onclick="insertar2(this, 'cargorelaciones', 'modulo=ajax&accion=cargorelaciones', 'cargos_ajax.php');" <?=$disabled_detalles?> />
            <input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'cargorelaciones');" <?=$disabled_detalles?> />
        </td>
    </tr>
    </tbody>
</table>
<div style="overflow:scroll; width:<?=$_width?>px; height:133px;">
<table width="100%" class="tblLista">
	<thead>
    <tr>
        <th width="20">#</th>
        <th width="60">Tipo</th>
        <th align="left">Ente Relacionado</th>
    </tr>
    </thead>
    
    <tbody id="lista_cargorelaciones">
    	<?php
		$sql = "SELECT
					TipoRelacion,
					EnteRelacionado
				FROM rh_cargorelaciones
				WHERE CodCargo = '".$sel_registros."'
				ORDER BY Secuencia";
		$field_cargorelaciones = getRecords($sql);
		foreach ($field_cargorelaciones as $f) {
			$id = ++$nro_cargorelaciones;
			?>
            <tr class="trListaBody" onclick="clk($(this), 'cargorelaciones', 'cargorelaciones_<?=$id?>');" id="cargorelaciones_<?=$id?>">
                <th><?=$nro_cargorelaciones?></th>
                <td>
                	<select name="cargorelaciones_TipoRelacion[]" class="cell" style="text-align:center;" <?=$disabled_detalles?>>
                    	<?=loadSelectValores("tipo-relacion", $f['TipoRelacion'])?>
                    </select>
                </td>
                <td>
                	<input type="text" name="cargorelaciones_EnteRelacionado[]" value="<?=htmlentities($f['EnteRelacionado'])?>" class="cell" <?=$disabled_detalles?> />
                </td>
            </tr>
            <?php
		}
		?>
    </tbody>
</table>
</div>
<input type="hidden" id="nro_cargorelaciones" value="<?=$nro_cargorelaciones?>" />
<input type="hidden" id="can_cargorelaciones" value="<?=$nro_cargorelaciones?>" />
</form>
</center>
</div>

<div id="tab3" style="display:none;">
<center>
</center>
</div>

<div id="tab4" style="display:none;">
<center>
<form name="frm_cargofunciones" id="frm_cargofunciones" autocomplete="off">
<input type="hidden" id="sel_cargofunciones" />
<table width="<?=$_width?>" class="tblBotones">
	<thead>
    	<th class="divFormCaption">Funciones del Cargo</th>
    </thead>
    <tbody>
    <tr>
        <td align="right">
            <input type="button" class="btLista" value="Insertar" onclick="insertar2(this, 'cargofunciones', 'modulo=ajax&accion=cargofunciones', 'cargos_ajax.php');" <?=$disabled_detalles?> />
            <input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'cargofunciones');" <?=$disabled_detalles?> />
        </td>
    </tr>
    </tbody>
</table>
<div style="overflow:scroll; width:<?=$_width?>px; height:320px;">
<table width="100%" class="tblLista">
    <tbody id="lista_cargofunciones">
    	<?php
		$sql = "SELECT
					Funcion,
					Descripcion,
					Estado
				FROM rh_cargofunciones
				WHERE CodCargo = '".$sel_registros."'
				ORDER BY Funcion DESC, CodFuncion";
		$field_cargofunciones = getRecords($sql);
		foreach ($field_cargofunciones as $f) {
			$id = ++$nro_cargofunciones;
			?>
            <tr class="trListaBody" onclick="clk($(this), 'cargofunciones', 'cargofunciones_<?=$id?>');" id="cargofunciones_<?=$id?>">
            	<td>
                	<table width="100%">
                        <tr>
                            <th rowspan="2" width="35"><?=$nro_cargofunciones?></th>
                            <td width="175">
                                <select name="cargofunciones_Funcion[]" class="cell" <?=$disabled_detalles?>>
                                    <?=getMiscelaneos($f['Funcion'], "FUNCION")?>
                                </select>
							</td>
                            <td></td>
                            <td width="75">
                                <select name="cargofunciones_Estado[]" class="cell" <?=$disabled_detalles?>>
                                    <?=loadSelectGeneral("ESTADO", $f['Estado'])?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                        	<td colspan="3">
                            	<textarea name="cargofunciones_Descripcion[]" class="cell" style="height:50px;" <?=$disabled_detalles?>><?=htmlentities($f['Descripcion'])?></textarea>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <?php
		}
		?>
    </tbody>
</table>
</div>
<input type="hidden" id="nro_cargofunciones" value="<?=$nro_cargofunciones?>" />
<input type="hidden" id="can_cargofunciones" value="<?=$nro_cargofunciones?>" />
</form>
</center>
</div>

<div id="tab5" style="display:none;">
<center>
<form name="frm_cargoformacion" id="frm_cargoformacion" autocomplete="off">
<input type="hidden" id="sel_cargoformacion" />
<table width="<?=$_width?>" class="tblBotones">
	<thead>
    	<th class="divFormCaption">Formaci&oacute;n Acad&eacute;mica</th>
    </thead>
    <tbody>
    <tr>
        <td align="right">
            <input type="button" class="btLista" value="Insertar" onclick="insertar2(this, 'cargoformacion', 'modulo=ajax&accion=cargoformacion', 'cargos_ajax.php');" <?=$disabled_detalles?> />
            <input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'cargoformacion');" <?=$disabled_detalles?> />
        </td>
    </tr>
    </tbody>
</table>
<div style="overflow:scroll; width:<?=$_width?>px; height:125px;">
<table width="100%" class="tblLista">
	<thead>
    <tr>
        <th width="20">#</th>
        <th width="200" align="left">Grado de Instrucci&oacute;n</th>
        <th width="200" align="left">Area</th>
        <th align="left">Profesi&oacute;n</th>
    </tr>
    </thead>
    
    <tbody id="lista_cargoformacion">
    	<?php
		$sql = "SELECT *
				FROM rh_cargoformacion
				WHERE CodCargo = '".$sel_registros."'
				ORDER BY CodProfesion";
		$field_cargoformacion = getRecords($sql);
		foreach ($field_cargoformacion as $f) {
			$id = ++$nro_cargoformacion;
			?>
            <tr class="trListaBody" onclick="clk($(this), 'cargoformacion', 'cargoformacion_<?=$id?>');" id="cargoformacion_<?=$id?>">
                <th><?=$nro_cargoformacion?></th>
                <td>
                    <select name="cargoformacion_CodGradoInstruccion[]" id="CodGradoInstruccion_<?=$id?>" class="cell" onchange="loadSelect($('#CodProfesion_<?=$id?>'), 'tabla=profesion&CodGradoInstruccion='+$('#CodGradoInstruccion_<?=$id?>').val()+'&Area='+$('#Area_<?=$id?>').val(), 1);" <?=$disabled_detalles?>>
                        <?=loadSelect2("rh_gradoinstruccion", "CodGradoInstruccion", "Descripcion", $f['CodGradoInstruccion'])?>
                    </select>
                </td>
                <td>
                    <select name="cargoformacion_Area[]" id="Area_<?=$id?>" class="cell" onchange="loadSelect($('#CodProfesion_<?=$id?>'), 'tabla=profesion&CodGradoInstruccion='+$('#CodGradoInstruccion_<?=$id?>').val()+'&Area='+$('#Area_<?=$id?>').val(), 1);" <?=$disabled_detalles?>>
                        <?=getMiscelaneos($f['Area'], "AREA")?>
                    </select>
                </td>
                <td>
                    <select name="cargoformacion_CodProfesion[]" id="CodProfesion_<?=$id?>" class="cell" <?=$disabled_detalles?>>
                        <?=loadSelect2("rh_profesiones", "CodProfesion", "Descripcion", $f['CodProfesion'], 0, array('CodGradoInstruccion','Area'), array($f['CodGradoInstruccion'],$f['Area']))?>
                    </select>
                </td>
            </tr>
            <?php
		}
		?>
    </tbody>
</table>
</div>
<input type="hidden" id="nro_cargoformacion" value="<?=$nro_cargoformacion?>" />
<input type="hidden" id="can_cargoformacion" value="<?=$nro_cargoformacion?>" />
</form>

<form name="frm_cargoinformat" id="frm_cargoinformat" autocomplete="off">
<input type="hidden" id="sel_cargoinformat" />
<table width="<?=$_width?>" class="tblBotones">
    <thead>
        <th class="divFormCaption">Cursos de Inform&aacute;tica</th>
    </thead>
    <tbody>
    <tr>
        <td align="right" class="gallery clearfix">
            <a id="a_cargoinformat" href="../lib/listas/listado_miscelaneos.php?filtrar=default&ventana=cargos&detalle=cargoinformat&CodMaestro=INFORMAT&CodAplicacion=RH&iframe=true&width=525&height=350" rel="prettyPhoto[iframe2]" style="display:none;"></a>
            <input type="button" class="btLista" value="Insertar" <?=$disabled_detalles?> onclick="$('#a_cargoinformat').click();" />
            <input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'cargoinformat');" <?=$disabled_detalles?> />
        </td>
    </tr>
    </tbody>
</table>
<div style="overflow:scroll; width:<?=$_width?>px; height:125px;">
<table width="100%" class="tblLista">
    <thead>
    <tr>
        <th width="20">#</th>
        <th align="left">Curso</th>
        <th width="125">Nivel</th>
    </tr>
    </thead>
    
    <tbody id="lista_cargoinformat">
        <?php
        $sql = "SELECT
					ci.*,
					md.Descripcion AS NomInformatica
                FROM
					rh_cargoinformat ci
					INNER JOIN mastmiscelaneosdet md ON (md.CodDetalle = ci.Informatica AND
														 md.CodMaestro = 'INFORMAT' AND
														 md.CodAplicacion = 'RH')
                WHERE ci.CodCargo = '".$sel_registros."'
                ORDER BY Informatica";
        $field_cargoinformat = getRecords($sql);
        foreach ($field_cargoinformat as $f) {
            $id = $f['Informatica'];
			++$nro_cargoinformat;
            ?>
            <tr class="trListaBody" onclick="clk($(this), 'cargoinformat', 'cargoinformat_<?=$id?>');" id="cargoinformat_<?=$id?>">
                <th><?=$nro_cargoinformat?></th>
                <td>
                	<input type="hidden" name="cargoinformat_Informatica[]" value="<?=$f['Informatica']?>" />
                    <?=$f['NomInformatica']?>
                </td>
                <td>
                    <select name="cargoinformat_Nivel[]" class="cell" <?=$disabled_detalles?>>
                        <?=getMiscelaneos($f['Nivel'], "NIVEL")?>
                    </select>
                </td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>
</div>
<input type="hidden" id="nro_cargoinformat" value="<?=$nro_cargoinformat?>" />
<input type="hidden" id="can_cargoinformat" value="<?=$nro_cargoinformat?>" />
</form>

<form name="frm_cargoidioma" id="frm_cargoidioma" autocomplete="off">
<input type="hidden" id="sel_cargoidioma" />
<table width="<?=$_width?>" class="tblBotones">
	<thead>
    	<th class="divFormCaption">Dominio de Idiomas</th>
    </thead>
    <tbody>
    <tr>
        <td align="right">
            <input type="button" class="btLista" value="Insertar" onclick="insertar2(this, 'cargoidioma', 'modulo=ajax&accion=cargoidioma', 'cargos_ajax.php');" <?=$disabled_detalles?> />
            <input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'cargoidioma');" <?=$disabled_detalles?> />
        </td>
    </tr>
    </tbody>
</table>
<div style="overflow:scroll; width:<?=$_width?>px; height:125px;">
<table width="100%" class="tblLista">
	<thead>
    <tr>
        <th width="20">#</th>
        <th align="left">Idioma</th>
        <th width="125">Lectura</th>
        <th width="125">Oral</th>
        <th width="125">Escritura</th>
        <th width="125">General</th>
    </tr>
    </thead>
    
    <tbody id="lista_cargoidioma">
    	<?php
		$sql = "SELECT *
				FROM rh_cargoidioma
				WHERE CodCargo = '".$sel_registros."'
				ORDER BY CodIdioma";
		$field_cargoidioma = getRecords($sql);
		foreach ($field_cargoidioma as $f) {
			$id = ++$nro_cargoidioma;
			?>
            <tr class="trListaBody" onclick="clk($(this), 'cargoidioma', 'cargoidioma_<?=$id?>');" id="cargoidioma_<?=$id?>">
                <th><?=$nro_cargoidioma?></th>
                <td>
                    <select name="cargoidioma_CodIdioma[]" class="cell" <?=$disabled_detalles?>>
                        <?=loadSelect2("mastidioma", "CodIdioma", "DescripcionLocal", $f['CodIdioma'])?>
                    </select>
                </td>
                <td>
                    <select name="cargoidioma_NivelLectura[]" class="cell" <?=$disabled_detalles?>>
                        <?=getMiscelaneos($f['NivelLectura'], "NIVEL")?>
                    </select>
                </td>
                <td>
                    <select name="cargoidioma_NivelOral[]" class="cell" <?=$disabled_detalles?>>
                        <?=getMiscelaneos($f['NivelOral'], "NIVEL")?>
                    </select>
                </td>
                <td>
                    <select name="cargoidioma_NivelEscritura[]" class="cell" <?=$disabled_detalles?>>
                        <?=getMiscelaneos($f['NivelEscritura'], "NIVEL")?>
                    </select>
                </td>
                <td>
                    <select name="cargoidioma_NivelGeneral[]" class="cell" <?=$disabled_detalles?>>
                        <?=getMiscelaneos($f['NivelGeneral'], "NIVEL")?>
                    </select>
                </td>
            </tr>
            <?php
		}
		?>
    </tbody>
</table>
</div>
<input type="hidden" id="nro_cargoidioma" value="<?=$nro_cargoidioma?>" />
<input type="hidden" id="can_cargoidioma" value="<?=$nro_cargoidioma?>" />
</form>
</center>
</div>

<div id="tab6" style="display:none;">
<center>
<form name="frm_cargoexperiencia" id="frm_cargoexperiencia" autocomplete="off">
<input type="hidden" id="sel_cargoexperiencia" />
<table width="<?=$_width?>" class="tblBotones">
	<thead>
    	<th class="divFormCaption">Experiencias Previas</th>
    </thead>
    <tbody>
    <tr>
        <td align="right" class="gallery clearfix">
            <a id="a_cargoexperiencia" href="../lib/listas/listado_puestos.php?filtrar=default&ventana=cargos&detalle=cargoexperiencia&iframe=true&width=925&height=425" rel="prettyPhoto[iframe3]" style="display:none;"></a>
            <input type="button" class="btLista" value="Insertar" <?=$disabled_detalles?> onclick="$('#a_cargoexperiencia').click();" />
            <input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'cargoexperiencia');" <?=$disabled_detalles?> />
        </td>
    </tr>
    </tbody>
</table>
<div style="overflow:scroll; width:<?=$_width?>px; height:320px;">
<table width="100%" class="tblLista">
	<thead>
    <tr>
        <th width="20">#</th>
        <th align="left">Cargo</th>
        <th align="left" width="200">Area</th>
        <th width="60">Meses</th>
        <th width="60">Necesario</th>
    </tr>
    </thead>
    
    <tbody id="lista_cargoexperiencia">
    	<?php
		$sql = "SELECT
					ce.*,
					p.DescripCargo AS NomCargoExperiencia
				FROM
					rh_cargoexperiencia ce
					INNER JOIN rh_puestos p ON (p.CodCargo = ce.CargoExperiencia)
				WHERE ce.CodCargo = '".$sel_registros."'
				ORDER BY Secuencia";
		$field_cargoexperiencia = getRecords($sql);
		foreach ($field_cargoexperiencia as $f) {
			$id = ++$nro_cargoexperiencia;
			?>
            <tr class="trListaBody" onclick="clk($(this), 'cargoexperiencia', 'cargoexperiencia_<?=$id?>');" id="cargoexperiencia_<?=$id?>">
                <th><?=$nro_cargoexperiencia?></th>
                <td>
                	<input type="hidden" name="cargoexperiencia_CargoExperiencia[]" value="<?=$f['CargoExperiencia']?>" />
                    <?=$f['NomCargoExperiencia']?>
                </td>
                <td>
                    <select name="cargoexperiencia_AreaExperiencia[]" class="cell" <?=$disabled_detalles?>>
                    	<option value="">&nbsp;</option>
                        <?=getMiscelaneos($f['AreaExperiencia'], "AREAEXP")?>
                    </select>
                </td>
                <td>
                    <input type="text" name="cargoexperiencia_Meses[]" value="<?=$f['Meses']?>" class="cell integer" maxlength="3" style="text-align:center;" <?=$disabled_detalles?> />
                </td>
                <td align="center">
                    <input type="checkbox" name="cargoexperiencia_FlagNecesario[]" value="S" <?=chkFlag($f['FlagNecesario'])?> <?=$disabled_detalles?> />
                </td>
            </tr>
            <?php
		}
		?>
    </tbody>
</table>
</div>
<input type="hidden" id="nro_cargoexperiencia" value="<?=$nro_cargoexperiencia?>" />
<input type="hidden" id="can_cargoexperiencia" value="<?=$nro_cargoexperiencia?>" />
</form>
</center>
</div>

<div id="tab7" style="display:none;">
<center>
<form name="frm_cargoriesgo" id="frm_cargoriesgo" autocomplete="off">
<input type="hidden" id="sel_cargoriesgo" />
<table width="<?=$_width?>" class="tblBotones">
	<thead>
    	<th class="divFormCaption">Riesgos de Trabajo</th>
    </thead>
    <tbody>
    <tr>
        <td align="right">
            <input type="button" class="btLista" value="Insertar" onclick="insertar2(this, 'cargoriesgo', 'modulo=ajax&accion=cargoriesgo', 'cargos_ajax.php');" <?=$disabled_detalles?> />
            <input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'cargoriesgo');" <?=$disabled_detalles?> />
        </td>
    </tr>
    </tbody>
</table>
<div style="overflow:scroll; width:<?=$_width?>px; height:320px;">
<table width="100%" class="tblLista">
	<thead>
    <tr>
        <th width="20">#</th>
        <th width="150">Tipo de Riesgo</th>
        <th align="left">Riesgo</th>
    </tr>
    </thead>
    
    <tbody id="lista_cargoriesgo">
    	<?php
		$sql = "SELECT
					TipoRiesgo,
					Riesgo
				FROM rh_cargoriesgo
				WHERE CodCargo = '".$sel_registros."'
				ORDER BY Tiporiesgo, Secuencia";
		$field_cargoriesgo = getRecords($sql);
		foreach ($field_cargoriesgo as $f) {
			$id = ++$nro_cargoriesgo;
			?>
            <tr class="trListaBody" onclick="clk($(this), 'cargoriesgo', 'cargoriesgo_<?=$id?>');" id="cargoriesgo_<?=$id?>">
                <th><?=$nro_cargoriesgo?></th>
                <td>
                	<select name="cargoriesgo_TipoRiesgo[]" class="cell" <?=$disabled_detalles?>>
                    	<?=getMiscelaneos($field['TipoRiesgo'], "TRIESGO", 0)?>
                    </select>
                </td>
                <td>
                	<textarea name="cargoriesgo_Riesgo[]" class="cell" style="height:30px;" <?=$disabled_detalles?>><?=htmlentities($f['Riesgo'])?></textarea>
                </td>
            </tr>
            <?php
		}
		?>
    </tbody>
</table>
</div>
<input type="hidden" id="nro_cargoriesgo" value="<?=$nro_cargoriesgo?>" />
<input type="hidden" id="can_cargoriesgo" value="<?=$nro_cargoriesgo?>" />
</form>
</center>
</div>

<div id="tab8" style="display:none;">
<center>
<form name="frm_cargoevaluacion" id="frm_cargoevaluacion" autocomplete="off">
<input type="hidden" id="sel_cargoevaluacion" />
<table width="<?=$_width?>" class="tblBotones">
	<thead>
    	<th class="divFormCaption">Evaluaci&oacute;n - Reclutamiento</th>
    </thead>
    <tbody>
    <tr>
        <td align="right" class="gallery clearfix">
            <a id="a_cargoevaluacion" href="../lib/listas/listado_evaluaciones.php?filtrar=default&ventana=cargos&detalle=cargoevaluacion&iframe=true&width=725&height=425" rel="prettyPhoto[iframe4]" style="display:none;"></a>
            <input type="button" class="btLista" value="Insertar" <?=$disabled_detalles?> onclick="$('#a_cargoevaluacion').click();" />
            <input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'cargoevaluacion');" <?=$disabled_detalles?> />
        </td>
    </tr>
    </tbody>
</table>
<div style="overflow:scroll; width:<?=$_width?>px; height:320px;">
<table width="100%" class="tblLista">
	<thead>
    <tr>
        <th width="20">#</th>
        <th align="left">Evaluaci&oacute;n</th>
        <th width="100">Etapa</th>
        <th width="100">Factor</th>
        <th width="100">Estado</th>
    </tr>
    </thead>
    
    <tbody id="lista_cargoevaluacion">
    	<?php
		$sql = "SELECT
					ce.*,
					e.Descripcion AS NomEvaluacion
				FROM
					rh_cargoevaluacion ce
					INNER JOIN rh_evaluacion e ON (e.Evaluacion = ce.Evaluacion)
				WHERE ce.CodCargo = '".$sel_registros."'
				ORDER BY Evaluacion";
		$field_cargoevaluacion = getRecords($sql);
		foreach ($field_cargoevaluacion as $f) {
			$id = $f['Evaluacion'];
			++$nro_cargoevaluacion;
			?>
            <tr class="trListaBody" onclick="clk($(this), 'cargoevaluacion', 'cargoevaluacion_<?=$id?>');" id="cargoevaluacion_<?=$id?>">
                <th><?=$nro_cargoevaluacion?></th>
                <td>
                	<input type="hidden" name="cargoevaluacion_Evaluacion[]" value="<?=$f['Evaluacion']?>" />
                    <?=$f['NomEvaluacion']?>
                </td>
                <td>
                    <input type="text" name="cargoevaluacion_Etapa[]" value="<?=$f['Etapa']?>" class="cell integer" maxlength="3" style="text-align:center;" <?=$disabled_detalles?> />
                </td>
                <td>
                    <input type="text" name="cargoevaluacion_Factor[]" value="<?=$f['Factor']?>" class="cell integer" maxlength="3" style="text-align:center;" <?=$disabled_detalles?> />
                </td>
                <td>
                    <select name="cargoevaluacion_Estado[]" class="cell" <?=$disabled_detalles?>>
                        <?=loadSelectGeneral("ESTADO", $f['Estado'])?>
                    </select>
                </td>
            </tr>
            <?php
		}
		?>
    </tbody>
</table>
</div>
<input type="hidden" id="nro_cargoevaluacion" value="<?=$nro_cargoevaluacion?>" />
<input type="hidden" id="can_cargoevaluacion" value="<?=$nro_cargoevaluacion?>" />
</form>
</center>
</div>

<div id="tab9" style="display:none;">
<center>
<form name="frm_cargosub" id="frm_cargosub" autocomplete="off">
<input type="hidden" id="sel_cargosub" />
<table width="<?=$_width?>" class="tblBotones">
	<thead>
    	<th class="divFormCaption">Puestos Subordinados</th>
    </thead>
    <tbody>
    <tr>
        <td align="right" class="gallery clearfix">
            <a id="a_cargosub" href="../lib/listas/listado_puestos.php?filtrar=default&ventana=cargos&detalle=cargosub&iframe=true&width=925&height=425" rel="prettyPhoto[iframe5]" style="display:none;"></a>
            <input type="button" class="btLista" value="Insertar" <?=$disabled_detalles?> onclick="$('#a_cargosub').click();" />
            <input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'cargosub');" <?=$disabled_detalles?> />
        </td>
    </tr>
    </tbody>
</table>
<div style="overflow:scroll; width:<?=$_width?>px; height:320px;">
<table width="100%" class="tblLista">
	<thead>
    <tr>
        <th width="20">#</th>
        <th width="60">C&oacute;digo</th>
        <th align="left">Cargo Subordinado</th>
        <th width="60">Cantidad</th>
    </tr>
    </thead>
    
    <tbody id="lista_cargosub">
    	<?php
		$sql = "SELECT
					cr.CargoSubordinado,
					cr.Cantidad,
					p.CodDesc,
					p.DescripCargo
				FROM
					rh_cargosub cr
					INNER JOIN rh_puestos p ON (p.CodCargo = cr.CargoSubordinado)
				WHERE cr.CodCargo = '".$sel_registros."'
				ORDER BY CodDesc";
		$field_cargosub = getRecords($sql);
		foreach ($field_cargosub as $f) {
			$id = $f['CargoSubordinado'];
			?>
            <tr class="trListaBody" onclick="clk($(this), 'cargosub', 'cargosub_<?=$id?>');" id="cargosub_<?=$id?>">
                <th>
					<?=++$nro_cargosub?>
                    <input type="hidden" name="cargosub_CargoSubordinado[]" value="<?=$f['CargoSubordinado']?>" />
                </th>
                <td align="center"><?=$f['CodDesc']?></td>
                <td><?=htmlentities($f['DescripCargo'])?></td>
                <td>
                	<input type="text" name="cargosub_Cantidad[]" value="<?=$f['Cantidad']?>" class="cell integer" maxlength="4" style="text-align:center;" <?=$disabled_detalles?> />
				</td>
            </tr>
            <?php
		}
		?>
    </tbody>
</table>
</div>
<input type="hidden" id="nro_cargosub" value="<?=$nro_cargosub?>" />
<input type="hidden" id="can_cargosub" value="<?=$nro_cargosub?>" />
</form>
</center>
</div>

<div id="tab10" style="display:none;">
<center>
<form name="frm_cargocursos" id="frm_cargocursos" autocomplete="off">
<input type="hidden" id="sel_cargocursos" />
<table width="<?=$_width?>" class="tblBotones">
	<thead>
    	<th class="divFormCaption">Otros Estudios</th>
    </thead>
    <tbody>
    <tr>
        <td align="right" class="gallery clearfix">
            <a id="a_cargocursos" href="../lib/listas/listado_cursos.php?filtrar=default&ventana=cargos&detalle=cargocursos&iframe=true&width=925&height=425" rel="prettyPhoto[iframe6]" style="display:none;"></a>
            <input type="button" class="btLista" value="Insertar" <?=$disabled_detalles?> onclick="$('#a_cargocursos').click();" />
            <input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'cargocursos');" <?=$disabled_detalles?> />
        </td>
    </tr>
    </tbody>
</table>
<div style="overflow:scroll; width:<?=$_width?>px; height:320px;">
<table width="100%" class="tblLista">
    <tbody id="lista_cargocursos">
    	<?php
		$sql = "SELECT
					cc.*,
					c.Descripcion
				FROM
					rh_cargocursos cc
					INNER JOIN rh_cursos c ON (c.CodCurso = cc.Curso)
				WHERE cc.CodCargo = '".$sel_registros."'
				ORDER BY Descripcion";
		$field_cargocursos = getRecords($sql);
		foreach ($field_cargocursos as $f) {
			$id = $f['CodCurso'];
			?>
            <tr class="trListaBody" onclick="clk($(this), 'cargocursos', 'cargocursos_<?=$id?>');" id="cargocursos_<?=$id?>">
            	<td>
                	<table width="100%">
                    	<tr>
                            <th width="30" rowspan="2">
                                <?=++$nro_cargocursos?>
                                <input type="hidden" name="cargocursos_Curso[]" value="<?=$f['Curso']?>" />
                            </th>
                            <th width="50">
								Curso:
                            </th>
                            <td>
								<?=htmlentities($f['Descripcion'])?>
                            </td>
                            <th width="50">
								Horas:
                            </th>
                            <td width="50">
                                <input type="text" name="cargocursos_TotalHoras[]" value="<?=$f['TotalHoras']?>" class="cell integer" maxlength="4" style="text-align:center; width:90%;" <?=$disabled_detalles?> />
                            </td>
                            <th width="50">
								A&ntilde;os:
                            </th>
                            <td width="50">
                                <input type="text" name="cargocursos_AniosVigencia[]" value="<?=$f['AniosVigencia']?>" class="cell integer" maxlength="2" style="text-align:center;" <?=$disabled_detalles?> />
                            </td>
                        </tr>
                        <tr>
                            <th width="65">
								Observaciones:
                            </th>
                            <td colspan="5">
                                <textarea name="cargocursos_Observaciones[]" class="cell" style="height:30px;" <?=$disabled_detalles?>><?=htmlentities($f['Observaciones'])?></textarea>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <?php
		}
		?>
    </tbody>
</table>
</div>
<input type="hidden" id="nro_cargocursos" value="<?=$nro_cargocursos?>" />
<input type="hidden" id="can_cargocursos" value="<?=$nro_cargocursos?>" />
</form>
</center>
</div>

<div id="tab11" style="display:none;">
<center>
<form name="frm_cargometas" id="frm_cargometas" autocomplete="off">
<input type="hidden" id="sel_cargometas" />
<table width="<?=$_width?>" class="tblBotones">
	<thead>
    	<th class="divFormCaption">Objetivos y/o Metas</th>
    </thead>
    <tbody>
    <tr>
        <td align="right">
            <input type="button" class="btLista" value="Insertar" onclick="insertar2(this, 'cargometas', 'modulo=ajax&accion=cargometas', 'cargos_ajax.php');" <?=$disabled_detalles?> />
            <input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'cargometas');" <?=$disabled_detalles?> />
        </td>
    </tr>
    </tbody>
</table>
<div style="overflow:scroll; width:<?=$_width?>px; height:320px;">
<table width="100%" class="tblLista">
	<thead>
    <tr>
        <th width="20">#</th>
        <th align="left">Descripci&oacute;n</th>
        <th width="100">Factor</th>
    </tr>
    </thead>
    
    <tbody id="lista_cargometas">
    	<?php
		$sql = "SELECT
					Descripcion,
					FactorParticipacion
				FROM rh_cargometas
				WHERE CodCargo = '".$sel_registros."'
				ORDER BY Secuencia";
		$field_cargometas = getRecords($sql);
		foreach ($field_cargometas as $f) {
			$id = ++$nro_cargometas;
			?>
            <tr class="trListaBody" onclick="clk($(this), 'cargometas', 'cargometas_<?=$id?>');" id="cargometas_<?=$id?>">
                <th><?=$nro_cargometas?></th>
                <td>
                	<input type="text" name="cargometas_Descripcion[]" value="<?=htmlentities($f['Descripcion'])?>" class="cell" maxlength="50" <?=$disabled_detalles?> />
                </td>
                <td>
                	<input type="text" name="cargometas_FactorParticipacion[]" value="<?=$f['FactorParticipacion']?>" class="cell integer" maxlength="8" style="text-align:center;" <?=$disabled_detalles?> />
                </td>
            </tr>
            <?php
		}
		?>
    </tbody>
</table>
</div>
<input type="hidden" id="nro_cargometas" value="<?=$nro_cargometas?>" />
<input type="hidden" id="can_cargometas" value="<?=$nro_cargometas?>" />
</form>
</center>
</div>

<div id="tab12" style="display:none;">
<center>
<form name="frm_cargoambiente" id="frm_cargoambiente" autocomplete="off">
<input type="hidden" id="sel_cargoambiente" />
<table width="<?=$_width?>" class="tblBotones">
	<thead>
    	<th class="divFormCaption">Ambiente de Trabajo</th>
    </thead>
    <tbody>
    <tr>
        <td align="right">
            <input type="button" class="btLista" value="Insertar" onclick="insertar2(this, 'cargoambiente', 'modulo=ajax&accion=cargoambiente', 'cargos_ajax.php');" <?=$disabled_detalles?> />
            <input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'cargoambiente');" <?=$disabled_detalles?> />
        </td>
    </tr>
    </tbody>
</table>
<div style="overflow:scroll; width:<?=$_width?>px; height:320px;">
<table width="100%" class="tblLista">
    <tbody id="lista_cargoambiente">
    	<?php
		$sql = "SELECT Ambiente
				FROM rh_cargoambiente
				WHERE CodCargo = '".$sel_registros."'
				ORDER BY Secuencia";
		$field_cargoambiente = getRecords($sql);
		foreach ($field_cargoambiente as $f) {
			$id = ++$nro_cargoambiente;
			?>
            <tr class="trListaBody" onclick="clk($(this), 'cargoambiente', 'cargoambiente_<?=$id?>');" id="cargoambiente_<?=$id?>">
                <th width="30"><?=$nro_cargoambiente?></th>
                <td>
                	<textarea name="cargoambiente_Ambiente[]" class="cell" style="height:30px;" <?=$disabled_detalles?>><?=htmlentities($f['Ambiente'])?></textarea>
                </td>
            </tr>
            <?php
		}
		?>
    </tbody>
</table>
</div>
<input type="hidden" id="nro_cargoambiente" value="<?=$nro_cargoambiente?>" />
<input type="hidden" id="can_cargoambiente" value="<?=$nro_cargoambiente?>" />
</form>
</center>
</div>

<div id="tab13" style="display:none;">
<center>
<form name="frm_cargohabilidades" id="frm_cargohabilidades" autocomplete="off">
<input type="hidden" id="sel_cargohabilidades" />
<table width="<?=$_width?>" class="tblBotones">
	<thead>
    	<th class="divFormCaption">Habilidades / Destrezas</th>
    </thead>
    <tbody>
    <tr>
        <td align="right">
            <input type="button" class="btLista" value="Insertar" onclick="insertar2(this, 'cargohabilidades', 'modulo=ajax&accion=cargohabilidades', 'cargos_ajax.php');" <?=$disabled_detalles?> />
            <input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'cargohabilidades');" <?=$disabled_detalles?> />
        </td>
    </tr>
    </tbody>
</table>
<div style="overflow:scroll; width:<?=$_width?>px; height:320px;">
<table width="100%" class="tblLista">
	<thead>
    <tr>
        <th width="20">#</th>
        <th width="100">Tipo</th>
        <th align="left">Descripci&oacute;n</th>
    </tr>
    </thead>
    
    <tbody id="lista_cargohabilidades">
    	<?php
		$sql = "SELECT
					Tipo,
					Descripcion
				FROM rh_cargohabilidades
				WHERE CodCargo = '".$sel_registros."'
				ORDER BY Tipo, Secuencia";
		$field_cargohabilidades = getRecords($sql);
		foreach ($field_cargohabilidades as $f) {
			$id = ++$nro_cargohabilidades;
			?>
            <tr class="trListaBody" onclick="clk($(this), 'cargohabilidades', 'cargohabilidades_<?=$id?>');" id="cargohabilidades_<?=$id?>">
                <th width="30"><?=$nro_cargohabilidades?></th>
                <td>
                	<select name="cargohabilidades_Tipo[]" class="cell" <?=$disabled_detalles?>>
                    	<?=loadSelectValores("tipo-habilidad", $f['Tipo'])?>
                    </select>
                </td>
                <td>
                	<textarea name="cargohabilidades_Descripcion[]" class="cell" style="height:30px;" <?=$disabled_detalles?>><?=htmlentities($f['Descripcion'])?></textarea>
                </td>
            </tr>
            <?php
		}
		?>
    </tbody>
</table>
</div>
<input type="hidden" id="nro_cargohabilidades" value="<?=$nro_cargohabilidades?>" />
<input type="hidden" id="can_cargohabilidades" value="<?=$nro_cargohabilidades?>" />
</form>
</center>
</div>

<center>
<input type="button" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" onclick="formulario(document.getElementById('frmentrada'), '<?=$accion?>');" />
<input type="button" value="Cancelar" id="btCancelar" style="width:75px;" onclick="<?=$clkCancelar?>" />
</center>
<div style="width:<?=$_width?>px" class="divMsj">Campos Obligatorios *</div>

<script type="text/javascript" language="javascript">
function formulario(form, accion) {
	bloqueo(true);
	//	valido
	var error = "";
	if ($("#CodTipoCargo").val() == "" || $("#CodNivelClase").val() == "" || $("#CodGrupOcup").val() == "" || $("#CodSerieOcup").val() == "" || $("#CodDesc").val() == "" || $("#DescripCargo").val() == "" || $("#CategoriaCargo").val() == "" || $("#Grado").val() == "" || $("#NivelSalarial").val() == "" || $("#Paso").val() == "") error = "Debe llenar los campos obligatorios";
	//	valido errores
	if (error != "") {
		cajaModal(error, "error", 400);
	} else {
		//	formulario
		var post = getForm(form);
		//	ajax
		$.ajax({
			type: "POST",
			url: "cargos_ajax.php",
			data: "modulo=cargos&accion="+accion+"&"+post+"&"+$('#frm_cargoreporta').serialize()+"&"+$('#frm_cargorelaciones').serialize()+"&"+$('#frm_cargofunciones').serialize()+"&"+$('#frm_cargoformacion').serialize()+"&"+$('#frm_cargoidioma').serialize()+"&"+$('#frm_cargoinformat').serialize()+"&"+$('#frm_cargoexperiencia').serialize()+"&"+$('#frm_cargoriesgo').serialize()+"&"+$('#frm_cargoevaluacion').serialize()+"&"+$('#frm_cargosub').serialize()+"&"+$('#frm_cargocursos').serialize()+"&"+$('#frm_cargometas').serialize()+"&"+$('#frm_cargoambiente').serialize()+"&"+$('#frm_cargohabilidades').serialize(),
			async: false,
			success: function(resp) {
				if (resp.trim() != "") cajaModal(resp, "error", 400);
				else form.submit();
			}
		});
	}
	return false;
}
function getDescripCargo() {
	var TipoCargo = $("#CodTipoCargo :selected").text();
	var SerieOcup = $("#CodSerieOcup :selected").text();
	var NivelClase = $("#CodNivelClase :selected").text();
	if (TipoCargo != '' && SerieOcup != '' && NivelClase != '') var DescripCargo = TipoCargo + " DE " + SerieOcup + " " + NivelClase;
	else var DescripCargo = '';
	$("#DescripCargo").val(DescripCargo).toUpperCase();
}
function getSueldoPromedio(CategoriaCargo, Grado, Paso, iNivelSalarial) {
	if (CategoriaCargo != '' && Grado != '' && Paso != '') {
		$.ajax({
			type: "POST",
			url: "../lib/fphp_funciones_ajax.php",
			data: "accion=getSueldoPromedio&CategoriaCargo="+CategoriaCargo+"&Grado="+Grado+"&Paso="+Paso,
			async: false,
			success: function(datos) {
				$(iNivelSalarial).val(datos).formatCurrency();
			}
		});
	} else {
		$(iNivelSalarial).val('0').formatCurrency();
	}
}
</script>