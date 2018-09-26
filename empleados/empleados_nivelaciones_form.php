<?php
list($AnioActual, $MesActual, $DiaActual) = split("[/.-]", substr($Ahora, 0, 10));
if ($opcion == "contratar") {
	$CodPersona = $Postulante;
	//	consulto cargo del requerimiento
	$sql = "SELECT
				r.CodCargo,
				r.FechaDesde AS Fecha,
				r.CodOrganismo,
				r.CodDependencia,
				pt.NivelSalarial AS SueldoActual,
				pt.CategoriaCargo,
				pt.Paso,
				md.Descripcion AS NomCategoriaCargo
			FROM
				rh_requerimiento r
				INNER JOIN rh_puestos pt ON (pt.CodCargo = r.CodCargo)
				LEFT JOIN mastmiscelaneosdet md ON (md.CodDetalle = pt.CategoriaCargo AND
													md.CodMaestro = 'CATCARGO')
			WHERE
				r.CodOrganismo = '".$CodOrganismoReq."' AND
				r.Requerimiento = '".$Requerimiento."'";
	$query_req = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query_req)) $field_req = mysql_fetch_array($query_req);
	//	-------------------
	$_CodOrganismo = $field_req['CodOrganismo'];
	$_CodDependencia = $field_req['CodDependencia'];
	$_Fecha = $field_req['Fecha'];
	$_CodCargo = $field_req['CodCargo'];
	$_Paso = $field_req['Paso'];
	$_NomCategoriaCargo = $field_req['NomCategoriaCargo'];
	$_SueldoActual = $field_req['SueldoActual'];
	$clkCancelar = "parent.$.prettyPhoto.close();";
} else {
	$CodPersona = $registro;
	$clkCancelar = "document.getElementById('frmentrada').submit();";
}
//	consulto datos generales
$sql = "SELECT
			p.CodPersona,
			p.NomCompleto,
			e.CodEmpleado,
			e.CodOrganismo,
			e.CodDependencia,
			e.Fingreso,
			e.CodCargo AS CodCargoReal,
			e.CodCargo,
			e.CodCargoTemp,
			e.CodTipoNom,
			e.CodCentroCosto,
			e.Estado,
			e.Paso,
			e.PasoTemp,
			pt.Grado,
			pt.NivelSalarial AS NivelSalarial,
			ns.SueldoPromedio AS SueldoActual,
			pt2.NivelSalarial AS NivelSalarialTemp,
			ns2.SueldoPromedio AS SueldoActualTemp,
			pt2.Grado AS GradoTemp,
			pt.CategoriaCargo,
			pt2.CategoriaCargo AS CategoriaCargoTemp,
			md.Descripcion AS NomCategoriaCargo,
			md2.Descripcion AS NomCategoriaCargoTemp
		FROM
			mastpersonas p
			INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
			INNER JOIN rh_puestos pt ON (pt.CodCargo = e.CodCargo)
			LEFT JOIN rh_nivelsalarial ns ON (ns.CategoriaCargo = pt.CategoriaCargo AND ns.Grado = pt.Grado AND ns.Paso = e.Paso)
			LEFT JOIN mastmiscelaneosdet md ON (md.CodDetalle = pt.CategoriaCargo AND
												md.CodMaestro = 'CATCARGO')
			LEFT JOIN rh_puestos pt2 ON (pt2.CodCargo = e.CodCargoTemp)
			LEFT JOIN rh_nivelsalarial ns2 ON (ns2.CategoriaCargo = pt2.CategoriaCargo AND ns2.Grado = pt.Grado AND ns2.Paso = e.PasoTemp)
			LEFT JOIN mastmiscelaneosdet md2 ON (md2.CodDetalle = pt2.CategoriaCargo AND
												 md2.CodMaestro = 'CATCARGO')
		WHERE p.CodPersona = '".$CodPersona."'";
$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
if (mysql_num_rows($query)) $field = mysql_fetch_array($query);
if (!$_CodOrganismo) $_CodOrganismo = $field['CodOrganismo'];
if (!$_CodDependencia) $_CodDependencia = $field['CodDependencia'];
if (!$_CodCargo) $_CodCargo = $field['CodCargo'];
if (!$_NomCategoriaCargo) $_NomCategoriaCargo = $field['NomCategoriaCargo'];
if (!$_CategoriaCargo) $_CategoriaCargo = $field['CategoriaCargo'];
if (!$_Grado) $_Grado = $field['Grado'];
if (!$_SueldoActual) $_SueldoActual = $field['SueldoActual'];
if (!$_Paso) $_Paso = $field['Paso'];
//	------------------------------------
if ($field['CodCargoTemp'] != "" || $field['PasoTemp'] != "") {
	$field['CodCargo'] = $field['CodCargoTemp'];
	$field['SueldoActual'] = $field['SueldoActualTemp'];
	$field['NomCategoriaCargo'] = $field['NomCategoriaCargoTemp'];
	$field['CategoriaCargo'] = $field['CategoriaCargoTemp'];
	$field['Paso'] = $field['PasoTemp'];
	$field['Grado'] = $field['GradoTemp'];
}
?>
<?php
if ($opcion != "contratar") {
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo">Nivelaciones del Empleado</td>
		<td align="right"><a class="cerrar" href="#" onclick="document.getElementById('frmentrada').submit();">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />
<?php
}
?>

<div class="divBorder" style="width:900px;">
<table width="900" class="tblFiltro">
	<tr>
    	<td colspan="2" class="divFormCaption">Datos del Empleado</td>
    </tr>
	<tr>
		<td align="right" width="125">Empleado:</td>
		<td>
        	<input type="text" id="CodEmpleado" style="width:60px;" class="codigo" value="<?=$field['CodEmpleado']?>" disabled />
		</td>
	</tr>
	<tr>
		<td align="right">Nombre Completo:</td>
		<td>
        	<input type="text" id="NomCompleto" style="width:500px;" class="codigo" value="<?=$field['NomCompleto']?>" disabled />
		</td>
	</tr>
</table>
</div><br />

<table width="900" align="center" cellpadding="0" cellspacing="0">
    <tr>
        <td>
            <div class="header">
            <ul id="tab">
            <!-- CSS Tabs -->
            <li id="li1" onclick="currentTab('tab', this);" class="current"><a href="#" onclick="mostrarTab('tab', 1, 2);">Nivelaci&oacute;n</a></li>
            <li id="li2" onclick="currentTab('tab', this);"><a href="#" onclick="mostrarTab('tab', 2, 2);">Historial de Nivelaciones</a></li>
            </ul>
            </div>
        </td>
    </tr>
</table>
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=empleados_lista" method="POST" enctype="multipart/form-data" autocomplete="off" onsubmit="return empleados_nivelaciones(this);">
<?=filtroEmpleados()?>
<input type="hidden" name="registro" id="registro" value="<?=$registro?>" />
<input type="hidden" id="CodOrganismoReq" value="<?=$CodOrganismoReq?>" />
<input type="hidden" id="Requerimiento" value="<?=$Requerimiento?>" />
<input type="hidden" id="TipoPostulante" value="<?=$TipoPostulante?>" />
<input type="hidden" id="Postulante" value="<?=$Postulante?>" />
<input type="hidden" id="CodPersona" value="<?=$CodPersona?>" />
<input type="hidden" id="Estado" value="<?=$field['Estado']?>" />
<input type="hidden" id="CodCargoReal" value="<?=$field['CodCargoReal']?>" />
<input type="hidden" name="opcion" id="opcion" value="<?=$opcion?>" />

<div id="tab1" style="display:block;">
<table width="900" class="tblForm">
	<tr>
    	<td colspan="4" class="divFormCaption">Condiciones Anteriores</td>
    </tr>
	<tr>
		<td class="tagForm" width="100">Organismo:</td>
		<td>
            <select id="CodOrganismoAnt" style="width:275px;" class="disabled" disabled>
                <?=getOrganismos($field['CodOrganismo'], 1);?>
            </select>
		</td>
		<td class="tagForm" width="100">Cargo:</td>
		<td>
            <select id="CodCargoAnt" style="width:275px;" class="disabled" disabled>
                <?=loadSelect("rh_puestos", "CodCargo", "DescripCargo", $field['CodCargo'], 1);?>
            </select>
		</td>
        
	</tr>
	<tr>
		<td class="tagForm">Dependencia:</td>
		<td>
            <select id="CodDependenciaAnt" style="width:275px;" class="disabled" disabled>
                <?=getDependencias($field['CodDependencia'], $field['CodOrganismo'], 1);?>
            </select>
		</td>
		<td class="tagForm">Categor&iacute;a:</td>
		<td>
        	<input type="text" style="width:100px;" class="disabled" value="<?=$field['NomCategoriaCargo']?>" disabled="disabled" />
		</td>
	</tr>
	<tr>
		<td class="tagForm">Centro Costo:</td>
		<td>
            <select id="CodCentroCostoAnt" style="width:275px;" class="disabled" disabled>
                <?=loadSelect2("ac_mastcentrocosto", "CodCentroCosto", "Descripcion", $field['CodCentroCosto'], 1);?>
            </select>
		</td>
		<td class="tagForm">Paso:</td>
		<td>
            <select id="PasoAnt" style="width:55px;" disabled>
                <?=loadSelect2("rh_nivelsalarial", "Paso", "Paso", $field['Paso'], 0, array('CategoriaCargo','Grado'), array($field['CategoriaCargo'],$field['Grado']))?>
            </select>
		</td>
	</tr>
	<tr>
		<td class="tagForm">Tipo de N&oacute;mina:</td>
		<td>
            <select id="CodTipoNomAnt" style="width:200px;" class="disabled" disabled>
                <?=loadSelect("tiponomina", "CodTipoNom", "Nomina", $field['CodTipoNom'], 1);?>
            </select>
		</td>
		<td class="tagForm">Sueldo B&aacute;sico:</td>
		<td>
        	<input type="text" style="width:100px; text-align:right;" class="disabled" value="<?=number_format($field['SueldoActual'], 2, ',', '.')?>" disabled="disabled" />
		</td>
	</tr>
	<tr>
    	<td colspan="4" class="divFormCaption">Nuevas Condiciones</td>
    </tr>
	<tr>
		<td class="tagForm">Organismo:</td>
		<td>
            <select id="CodOrganismo" style="width:275px;" onChange="loadSelect($('#fCodDependencia'), 'tabla=dependencia_filtro&opcion='+$('#CodOrganismo').val(), 1, 'CodCentrocosto');" <?=$disabled_modificar?>>
                <?=getOrganismos($_CodOrganismo, 0);?>
            </select>
		</td>
		<td class="tagForm">Cargo:</td>
		<td>
            <select id="CodCargo" style="width:275px;" onchange="getPasoSueldo($(this).val(), $('#CategoriaCargo'), $('#Paso'), $('#SueldoActual'));">
                <?=loadSelect("rh_puestos", "CodCargo", "DescripCargo", $_CodCargo, 0);?>
            </select>
		</td>
        
	</tr>
	<tr>
		<td class="tagForm">Dependencia:</td>
		<td>
            <select id="CodDependencia" style="width:275px;" onChange="loadSelect($('#CodCentroCosto'), 'tabla=centro_costo&opcion='+$('#CodDependencia').val(), 1);">
                <?=getDependencias($_CodDependencia, $_CodOrganismo, 0);?>
            </select>
		</td>
		<td class="tagForm">Categor&iacute;a:</td>
		<td>
        	<input type="text" id="CategoriaCargo" style="width:100px;" class="disabled" value="<?=$_NomCategoriaCargo?>" disabled="disabled" />
		</td>
	</tr>
	<tr>
		<td class="tagForm">Centro de Costo:</td>
		<td>
            <select id="CodCentroCosto" style="width:275px;">
                <?=loadSelect2("ac_mastcentrocosto", "CodCentroCosto", "Descripcion", $field['CodCentroCosto'], 0, array('CodDependencia'), array($field['CodDependencia']));?>
            </select>
		</td>
		<td class="tagForm">Paso:</td>
		<td>
            <select id="Paso" style="width:55px;" onchange="getSueldoxPaso($('#CodCargo').val(),$('#Paso').val(),$('#SueldoActual'));">
                <?=loadSelect2("rh_nivelsalarial", "Paso", "Paso", $_Paso, 0, array('CategoriaCargo','Grado'), array($_CategoriaCargo,$field['Grado']))?>
            </select>
		</td>
	</tr>
	<tr>
		<td class="tagForm">Tipo de N&oacute;mina:</td>
		<td>
            <select id="CodTipoNom" style="width:200px;">
                <?=loadSelect("tiponomina", "CodTipoNom", "Nomina", $field['CodTipoNom'], 0);?>
            </select>
		</td>
		<td class="tagForm">Sueldo B&aacute;sico:</td>
		<td>
        	<input type="text" id="SueldoActual" style="width:100px; text-align:right;" class="disabled" value="<?=number_format($_SueldoActual, 2, ',', '.')?>" disabled="disabled" />
		</td>
	</tr>
	<tr>
    	<td colspan="4" class="divFormCaption">Sustento</td>
    </tr>
    <tr>
        <td class="tagForm">* Fecha:</td>
		<td>
        	<input type="text" id="Fecha" style="width:60px;" class="datepicker" value="<?=formatFechaDMA($_Fecha)?>" maxlength="10" />
		</td>
        <td class="tagForm">* Tipo de Acci&oacute;n:</td>
		<td>
            <select id="TipoAccion" style="width:275px;">
            	<option value="">&nbsp;</option>
                <?=getMiscelaneos("", "TIPOACCION", 0);?>
            </select>
		</td>
    </tr>
    <tr>
        <td class="tagForm">Motivo:</td>
		<td>
        	<input type="text" id="Motivo" style="width:273px;" maxlength="255" />
		</td>
        <td class="tagForm">Documento:</td>
		<td>
        	<input type="text" id="Documento" style="width:270px;" maxlength="255" />
		</td>
    </tr>
    <tr>
		<td class="tagForm">Responsable:</td>
		<td class="gallery clearfix" colspan="3">
            <input type="hidden" id="Responsable" />
            <input type="hidden" id="CodEmpleadoResp" />
			<input type="text" id="NomResponsable" style="width:275px;" class="disabled" disabled />
            <a href="../lib/listas/listado_empleados.php?filtrar=default&cod=CodEmpleadoResp&nom=NomResponsable&campo3=Responsable&iframe=true&width=950&height=525" rel="prettyPhoto[iframe1]">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
	</tr>
    
</table>
<center>
<input type="submit" value="Procesar" />
<input type="button" value="Cancelar" onclick="<?=$clkCancelar?>" />
</center>
</div>

<div id="tab2" style="display:none;">
<center>
<iframe style="border-left:solid 1px #CDCDCD; border-right:solid 1px #CDCDCD; border-bottom:solid 1px #CDCDCD; border-top:0; width:900px; height:400px;" src="empleados_nivelaciones_historial_pdf.php?CodPersona=<?=$CodPersona?>"></iframe>
</center>
</div>
</form>

<div style="width:900px" class="divMsj">Campos Obligatorios *</div>