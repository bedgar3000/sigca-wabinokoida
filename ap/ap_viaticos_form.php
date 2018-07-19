<?php
if ($opcion == "nuevo") {

	$sql = "SELECT MAX(Ejercicio) FROM pv_reformulacionmetas";
	$Ejercicio = getVar3($sql);
	$Ejercicio = ($Ejercicio?$Ejercicio:$AnioActual);

	$field['Estado'] = "PR";
	$field['FechaViaje'] = $FechaActual;
	$field['Periodo'] = $PeriodoActual;
	$field['Anio'] = $AnioActual;
	$field['CodOrganismo'] = $_SESSION['ORGANISMO_ACTUAL'];
	$field['CodDependencia'] = $_SESSION["DEPENDENCIA_ACTUAL"];
	$field['CodCentroCosto'] = $_SESSION["CCOSTO_ACTUAL"];
	$field['PreparadoPor'] = $_SESSION['CODPERSONA_ACTUAL'];
	$field['NomPreparadoPor'] = $_SESSION['NOMBRE_USUARIO_ACTUAL'];
	$field['FechaPreparado'] = $FechaActual;
	$field['DescripcionGral'] = $_PARAMETRO['RESVIAT'];
	##	presupuesto
	$sql = "SELECT p.*
			FROM pv_presupuesto p
			INNER JOIN pv_categoriaprog cp ON (cp.CategoriaProg = p.CategoriaProg)
			INNER JOIN pv_unidadejecutora ue On (ue.CodUnidadEjec = cp.CodUnidadEjec)
			WHERE p.CodOrganismo = '".$field['CodOrganismo']."' AND p.Ejercicio = '".$Ejercicio."' AND p.CategoriaProg = '".$_PARAMETRO['CATVIATDEF']."'";
	$field_presupuesto = getRecord($sql);
	$field['CodPresupuesto'] = $field_presupuesto['CodPresupuesto'];
	$field['Ejercicio'] = $field_presupuesto['Ejercicio'];
	$field['CategoriaProg'] = $_PARAMETRO['CATVIATDEF'];
	$field['CodFuente'] = $_PARAMETRO['FFMETASDEF'];
	##
	$titulo = "Nuevo Vi&aacute;tico";
	$accion = "nuevo";
	$disabled_nuevo = "disabled";
	$disabled_modificar = "";
	$disabled_ver = "";
	$disabled_conceptos = "";
	$display_modificar = "";
	$display_submit = "";
	$label_submit = "Guardar";
	$clkCancelar = "document.getElementById('frmentrada').submit();";
	$focus = "btAceptar";
	$viaticos_distribucion = "viaticos_distribucion();";
	$btEmpleado = "../lib/listas/listado_empleados.php?filtrar=default&cod=CodEmpleado&nom=NomEmpleado&campo3=CodPersona&campo4=CodOrganismo&campo5=CodDependencia&campo6=CodCentroCosto&campo7=CodCargo&ventana=viaticos&iframe=true&width=100%&height=100%";
}
elseif ($opcion == "modificar" || $opcion == "ver" || $opcion == "revisar" || $opcion == "anular" || $opcion == "relacionar" || $opcion == "copiar") {
	##	consulto datos generales
	list($CodOrganismo, $CodViatico) = explode("_", $sel_registros);
	$sql = "SELECT
				v.*,
				p1.NomCompleto AS NomEmpleado,
				e.CodEmpleado,
				p2.NomCompleto AS NomPreparadoPor,
				p3.NomCompleto AS NomRevisadoPor,
				p4.NomCompleto AS NomGeneradoPor,
				pv.CategoriaProg,
				pv.Ejercicio
			FROM
				ap_viaticos v
				INNER JOIN mastpersonas p1 ON (p1.CodPersona = v.CodPersona)
				LEFT JOIN mastpersonas p2 ON (p2.CodPersona = v.PreparadoPor)
				LEFT JOIN mastpersonas p3 ON (p3.CodPersona = v.RevisadoPor)
				LEFT JOIN mastpersonas p4 ON (p4.CodPersona = v.GeneradoPor)
				LEFT JOIN mastempleado e ON (e.CodPersona = v.CodPersona)
				LEFT JOIN pv_presupuesto pv ON (pv.CodOrganismo = v.CodOrganismo AND pv.CodPresupuesto = v.CodPresupuesto)
			WHERE
				v.CodOrganismo = '".$CodOrganismo."' AND
				v.CodViatico = '".$CodViatico."'";
	$field = getRecord($sql);
	##	modificar
	if ($opcion == "modificar") {
		$titulo = "Modificar Vi&aacute;tico";
		$accion = "modificar";
		$disabled_nuevo = "";
		$disabled_modificar = "disabled";
		$disabled_ver = "";
		$disabled_conceptos = "";
		$display_modificar = "display:none;";
		$display_submit = "";
		$label_submit = "Modificar";
		$clkCancelar = "document.getElementById('frmentrada').submit();";
		$focus = "btAceptar";
		$viaticos_distribucion = "viaticos_distribucion();";
		$btEmpleado = "";
	}
	##	ver
	elseif ($opcion == "ver") {
		$titulo = "Ver Vi&aacute;tico";
		$disabled_nuevo = "disabled";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$disabled_conceptos = "disabled";
		$display_modificar = "display:none;";
		$display_submit = "display:none;";
		$label_submit = "";
		$clkCancelar = "document.getElementById('frmentrada').submit();";
		$focus = "btCancelar";
		$viaticos_distribucion = "";
		$btEmpleado = "";
	}
	##	revisar
	elseif ($opcion == "revisar") {
		$field['RevisadoPor'] = $_SESSION['CODPERSONA_ACTUAL'];
		$field['NomRevisadoPor'] = $_SESSION['NOMBRE_USUARIO_ACTUAL'];
		$field['FechaRevisado'] = $FechaActual;
		##
		$titulo = "Revisar Vi&aacute;tico";
		$accion = "revisar";
		$disabled_nuevo = "disabled";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$disabled_conceptos = "disabled";
		$display_modificar = "display:none;";
		$display_submit = "";
		$label_submit = "Revisar";
		$clkCancelar = "document.getElementById('frmentrada').submit();";
		$focus = "btAceptar";
		$viaticos_distribucion = "";
		$btEmpleado = "";
	}
	##	anular
	elseif ($opcion == "anular") {
		$field['RevisadoPor'] = $_SESSION['CODPERSONA_ACTUAL'];
		$field['NomRevisadoPor'] = $_SESSION['NOMBRE_USUARIO_ACTUAL'];
		$field['FechaRevisado'] = $FechaActual;
		##
		$titulo = "Anular Vi&aacute;tico";
		$accion = "anular";
		$disabled_nuevo = "disabled";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$disabled_conceptos = "disabled";
		$display_modificar = "display:none;";
		$display_submit = "";
		$label_submit = "Anular";
		$clkCancelar = "document.getElementById('frmentrada').submit();";
		$focus = "btAceptar";
		$viaticos_distribucion = "";
		$btEmpleado = "";
	}
	##	relacionar
	elseif ($opcion == "relacionar") {
		$field['Estado'] = "PR";
		$field['FechaViaje'] = $FechaActual;
		$field['Periodo'] = $PeriodoActual;
		$field['Anio'] = $AnioActual;
		$field['PreparadoPor'] = $_SESSION['CODPERSONA_ACTUAL'];
		$field['NomPreparadoPor'] = $_SESSION['NOMBRE_USUARIO_ACTUAL'];
		$field['FechaPreparado'] = $FechaActual;
		$field['DescripcionGral'] = $_PARAMETRO['RESVIAT'];
		$field['CodViatico'] = "";
		$field['CodInterno'] = "";
		$field['CodViaticoRelacion'] = $CodViatico;
		$field['CodEmpleado'] = "";
		$field['CodPersona'] = "";
		$field['NomEmpleado'] = "";
		$field['CodOrganismo'] = "";
		$field['CodDependencia'] = "";
		$field['CodCargo'] = "";
		$field['CodCentroCosto'] = "";
		##
		$titulo = "Nuevo Vi&aacute;tico";
		$accion = "nuevo";
		$disabled_nuevo = "disabled";
		$disabled_modificar = "";
		$disabled_ver = "";
		$disabled_conceptos = "";
		$display_modificar = "";
		$display_submit = "";
		$label_submit = "Aceptar";
		$clkCancelar = "document.getElementById('frmentrada').submit();";
		$focus = "btAceptar";
		$viaticos_distribucion = "viaticos_distribucion();";
		if ($field['FlagPersonaExterna'] == 'S') $btEmpleado = "../lib/listas/listado_personas.php?filtrar=default&cod=CodPersona&nom=NomEmpleado&campo3=CodOrganismo&ventana=viaticos&EsOtros=S&iframe=true&width=825&height=400";
		else  $btEmpleado = "../lib/listas/listado_empleados.php?filtrar=default&cod=CodEmpleado&nom=NomEmpleado&campo3=CodPersona&campo4=CodOrganismo&campo5=CodDependencia&campo6=CodCentroCosto&campo7=CodCargo&ventana=viaticos&iframe=true&width=100%&height=100%";
	}
	##	copiar
	elseif ($opcion == "copiar") {
		$field['Estado'] = "PR";
		$field['FechaViaje'] = $FechaActual;
		$field['Periodo'] = $PeriodoActual;
		$field['Anio'] = $AnioActual;
		$field['PreparadoPor'] = $_SESSION['CODPERSONA_ACTUAL'];
		$field['NomPreparadoPor'] = $_SESSION['NOMBRE_USUARIO_ACTUAL'];
		$field['FechaPreparado'] = $FechaActual;
		$field['DescripcionGral'] = $_PARAMETRO['RESVIAT'];
		$field['CodViatico'] = "";
		$field['CodInterno'] = "";
		$field['CodViaticoRelacion'] = "";
		$field['CodEmpleado'] = "";
		$field['CodPersona'] = "";
		$field['NomEmpleado'] = "";
		$field['CodOrganismo'] = "";
		$field['CodDependencia'] = "";
		$field['CodCargo'] = "";
		$field['CodCentroCosto'] = "";
		$field['RevisadoPor'] = "";
		$field['NomRevisadoPor'] = "";
		$field['FechaRevisado'] = "";
		$field['GeneradoPor'] = "";
		$field['NomGeneradoPor'] = "";
		$field['FechaGenerado'] = "";
		$field['UltimoUsuario'] = "";
		$field['UltimaFecha'] = "";
		$field['ObligacionTipoDocumento'] = "";
		$field['ObligacionNroDocumento'] = "";
		##
		$titulo = "Nuevo Vi&aacute;tico";
		$accion = "nuevo";
		$disabled_nuevo = "disabled";
		$disabled_modificar = "";
		$disabled_ver = "";
		$disabled_conceptos = "";
		$display_modificar = "";
		$display_submit = "";
		$label_submit = "Aceptar";
		$clkCancelar = "document.getElementById('frmentrada').submit();";
		$focus = "btAceptar";
		$viaticos_distribucion = "viaticos_distribucion();";
		if ($field['FlagPersonaExterna'] == 'S') $btEmpleado = "../lib/listas/listado_personas.php?filtrar=default&cod=CodPersona&nom=NomEmpleado&campo3=CodOrganismo&ventana=viaticos&EsOtros=S&iframe=true&width=825&height=400";
		else  $btEmpleado = "../lib/listas/listado_empleados.php?filtrar=default&cod=CodEmpleado&nom=NomEmpleado&campo3=CodPersona&campo4=CodOrganismo&campo5=CodDependencia&campo6=CodCentroCosto&campo7=CodCargo&ventana=viaticos&iframe=true&width=100%&height=100%";
	}
}
if ($field['CodViaticoRelacion'] == "") $visibility_relacionado = "visibility:hidden;";
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
            <!-- CSS Tabs -->
            <li id="li1" onclick="currentTab('tab', this);" class="current"><a href="#" onclick="mostrarTab('tab', 1, 3);">Informaci&oacute;n General</a></li>
            <li id="li2" onclick="currentTab('tab', this);"><a href="#" onclick="mostrarTab('tab', 2, 3);">Conceptos</a></li>
            <li id="li3" onclick="currentTab('tab', this);"><a href="#" onclick="mostrarTab('tab', 3, 3); <?=$viaticos_distribucion?>">Distribuci&oacute;n</a></li>
            </ul>
            </div>
        </td>
    </tr>
</table>

<div id="tab1" style="display:block;">
	<form name="frmentrada" id="frmentrada" action="gehen.php?anz=<?=$origen?>" method="POST" enctype="multipart/form-data" onsubmit="return form(this, '<?=$accion?>');" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
	<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
	<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
	<input type="hidden" name="fCodDependencia" id="fCodDependencia" value="<?=$fCodDependencia?>" />
	<input type="hidden" name="fFechaPreparadoD" id="fFechaPreparadoD" value="<?=$fFechaPreparadoD?>" />
	<input type="hidden" name="fFechaPreparadoH" id="fFechaPreparadoH" value="<?=$fFechaPreparadoH?>" />
	<input type="hidden" name="fPeriodo" id="fPeriodo" value="<?=$fPeriodo?>" />
	<input type="hidden" id="Anio" value="<?=$field['Anio']?>" />

	<table width="<?=$_width?>" class="tblForm">
		<tr>
	    	<td colspan="4" class="divFormCaption">Informaci&oacute;n General</td>
	    </tr>
		<tr>
			<td class="tagForm" width="125">Vi&aacute;tico:</td>
			<td>
	        	<input type="text" id="CodViatico" value="<?=$field['CodViatico']?>" style="width:65px; font-weight:bold;" disabled />
	            &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
	            <span style=" <?=$visibility_relacionado?>">Relacionado: </span>
	            <input type="text" id="CodViaticoRelacion" value="<?=$field['CodViaticoRelacion']?>" style="width:65px; font-weight:bold; <?=$visibility_relacionado?>" disabled />
			</td>
			<td class="tagForm" width="100">Nro. Interno:</td>
			<td>
	        	<input type="text" id="CodInterno" value="<?=$field['CodInterno']?>" style="width:65px; font-weight:bold;" disabled />
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Fecha Viaje:</td>
			<td>
	        	<input type="text" id="Fecha" value="<?=formatFechaDMA($field['Fecha'])?>" style="width:65px;" class="datepicker" <?=$disabled_ver?> />
	            &nbsp; &nbsp; &nbsp; &nbsp; 
	            <input type="checkbox" name="FlagPersonaExterna" id="FlagPersonaExterna" value="S" onclick="setFlagPersonaExterna(this.checked);" <?=chkFlag($field['FlagPersonaExterna'])?> <?=$disabled_ver?> /> Persona Externa
			</td>
			<td class="tagForm">Periodo:</td>
			<td>
	        	<input type="text" id="Periodo" value="<?=$field['Periodo']?>" style="width:65px; font-weight:bold;" disabled />
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Persona:</td>
			<td class="gallery clearfix">
	            <input type="hidden" id="CodPersona" value="<?=$field['CodPersona']?>" style="width:75px;" disabled />
	            <input type="hidden" id="CodEmpleado" value="<?=$field['CodEmpleado']?>" style="width:75px;" disabled />
	            <input type="text" id="NomEmpleado" value="<?=$field['NomEmpleado']?>" style="width:275px;" disabled />
	            <a href="<?=$btEmpleado?>" rel="prettyPhoto[iframe1]" id="btEmpleado" style=" <?=$display_modificar?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
	        </td>
			<td class="tagForm">Estado:</td>
			<td>
	        	<input type="hidden" id="Estado" value="<?=$field['Estado']?>" />
	        	<input type="text" value="<?=strtoupper(printValores("ESTADO-VIATICOS",$field['Estado']))?>" style="width:100px; font-weight:bold;" disabled />
			</td>
		</tr>
	    <tr>
			<td class="tagForm">Organismo:</td>
			<td>
	            <select id="CodOrganismo" style="width:280px;" disabled="disabled">
	            	<option value="">&nbsp;</option>
	                <?=loadSelect2("mastorganismos","CodOrganismo","Organismo",$field['CodOrganismo'],0)?>
	            </select>
			</td>
			<td class="tagForm">Monto Total:</td>
			<td>
	        	<input type="text" id="Monto" value="<?=number_format($field['Monto'], 2, ',', '.')?>" style="width:100px; font-weight:bold; text-align:right;" disabled />
			</td>
		</tr>
	    <tr>
			<td class="tagForm">Dependencia:</td>
			<td>
				<select id="CodDependencia" style="width:280px;" <?=$disabled_ver?> onchange="loadSelect($('#CodCentroCosto'), 'tabla=ac_mastcentrocosto&CodDependencia='+$(this).val(), 1);">
	            	<option value="">&nbsp;</option>
					<?=loadSelect2("mastdependencias","CodDependencia","Dependencia",$field['CodDependencia'],0)?>
				</select>
			</td>
	        <td class="tagForm">Preparado Por:</td>
	        <td>
	            <input type="hidden" id="PreparadoPor" value="<?=$field['PreparadoPor']?>" />
	            <input type="text" id="NomPreparadoPor" value="<?=htmlentities($field['NomPreparadoPor'])?>" style="width:225px;" disabled="disabled" />
	            <input type="text" id="FechaPreparado" value="<?=formatFechaDMA(substr($field['FechaPreparado'],0,10))?>" style="width:60px;" disabled="disabled" />
	        </td>
		</tr>
	    <tr>
			<td class="tagForm">Centro de Costo:</td>
			<td>
				<select id="CodCentroCosto" style="width:280px;" <?=$disabled_ver?>>
	            	<option value="">&nbsp;</option>
					<?=loadSelect2("ac_mastcentrocosto","CodCentroCosto","Descripcion",$field['CodCentroCosto'],0)?>
				</select>
			</td>
	        <td class="tagForm">Revisado Por:</td>
	        <td>
	            <input type="hidden" id="RevisadoPor" value="<?=$field['RevisadoPor']?>" />
	            <input type="text" id="NomRevisadoPor" value="<?=htmlentities($field['NomRevisadoPor'])?>" style="width:225px;" disabled="disabled" />
	            <input type="text" id="FechaRevisado" value="<?=formatFechaDMA(substr($field['FechaRevisado'],0,10))?>" style="width:60px;" disabled="disabled" />
	        </td>
		</tr>
	    <tr>
			<td class="tagForm">Cargo:</td>
			<td>
				<select id="CodCargo" style="width:280px;" <?=$disabled_ver?>>
	            	<option value="">&nbsp;</option>
					<?=loadSelect2("rh_puestos","CodCargo","DescripCargo",$field['CodCargo'],0)?>
				</select>
			</td>
	        <td class="tagForm">Generado Por:</td>
	        <td>
	            <input type="hidden" id="GeneradoPor" value="<?=$field['GeneradoPor']?>" />
	            <input type="text" id="NomGeneradoPor" value="<?=htmlentities($field['NomGeneradoPor'])?>" style="width:225px;" disabled="disabled" />
	            <input type="text" id="FechaGenerado" value="<?=formatFechaDMA(substr($field['FechaGenerado'],0,10))?>" style="width:60px;" disabled="disabled" />
	        </td>
		</tr>
	    <tr>
	        <td colspan="2" class="divFormCaption">Presupuesto</td>
	    </tr>
	    <tr>
	        <td class="tagForm">Presupuesto:</td>
	        <td class="gallery clearfix">
	            <input type="text" name="Ejercicio" id="Ejercicio" value="<?=$field['Ejercicio']?>" style="width:48px;" class="Ejercicio" readonly />
	            <input type="text" name="CodPresupuesto" id="CodPresupuesto" value="<?=$field['CodPresupuesto']?>" style="width:48px;" class="CodPresupuesto" readonly />
	            <a href="../lib/listas/gehen.php?anz=lista_pv_presupuesto&filtrar=default&fCodOrganismo=<?=$field['CodOrganismo']?>&fEjercicio=<?=$field['Ejercicio']?>&fCodDependencia=<?=$field['CodDependencia']?>&campo1=Ejercicio&campo2=CodPresupuesto&campo3=CategoriaProg&ventana=lg_requerimiento&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe4]" style=" <?=$display_ver?>" id="btPresupuesto">
	                <img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
	        </td>
	        <td colspan="2">&nbsp;</td>
	    </tr>
	    <tr>
	        <td class="tagForm">Cat. Prog.:</td>
	        <td><input type="text" name="CategoriaProg" id="CategoriaProg" value="<?=$field['CategoriaProg']?>" style="width:100px;" class="CategoriaProg" readonly /></td>
	        <td colspan="2">&nbsp;</td>
	    </tr>
	    <tr>
	        <td class="tagForm">Fuente de Financiamiento:</td>
	        <td>
	            <select name="CodFuente" id="CodFuente" style="width:250px;" <?=$disabled_ver?>>
	                <?=loadSelect2("pv_fuentefinanciamiento","CodFuente","Denominacion",$field['CodFuente'],10)?>
	            </select>
	        </td>
	        <td colspan="2">&nbsp;</td>
	    </tr>
	    <tr>
	        <td class="tagForm">Obligaci&oacute;n:</td>
			<td colspan="3">
	        	<input type="text" name="ObligacionTipoDocumento" id="ObligacionTipoDocumento" value="<?=$field['ObligacionTipoDocumento']?>" style="width:20px;" readonly="readonly" />
	        	<input type="text" name="ObligacionNroDocumento" id="ObligacionNroDocumento" value="<?=$field['ObligacionNroDocumento']?>" style="width:95px;" readonly="readonly" />
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Resoluci&oacute;n:</td>
			<td colspan="3">
	        	<textarea id="DescripcionGral" style="width:95%; height:50px;" <?=$disabled_ver?>><?=$field['DescripcionGral']?></textarea>
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Motivo:</td>
			<td colspan="3">
	        	<textarea id="Motivo" style="width:95%; height:50px;" <?=$disabled_ver?>><?=$field['Motivo']?></textarea>
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
	<input type="submit" value="<?=$label_submit?>" id="btAceptar" style="width:75px; <?=$display_submit?>" />
	<input type="button" value="Cancelar" id="btCancelar" style="width:75px;" onclick="<?=$clkCancelar?>" />
	</center>
	</form>
</div>

<div id="tab2" style="display:none;">
	<center>
	<form name="frm_conceptos" id="frm_conceptos" autocomplete="off">
	<input type="hidden" id="sel_conceptos" />
	<table width="<?=$_width?>" class="tblBotones">
		<thead>
	    	<th class="divFormCaption" colspan="2">Conceptos de Vi&aacute;ticos</th>
	    </thead>
	    <tbody>
	    <tr>
	        <td align="right" class="gallery clearfix">
	            <a id="a_conceptos" href="../lib/listas/listado_viatico_concepto.php?filtrar=default&ventana=viaticos_conceptos_insertar&detalle=conceptos&iframe=true&width=825&height=425" rel="prettyPhoto[iframe3]" style="display:none;"></a>
	            <input type="button" class="btLista" value="Insertar" onclick="$('#a_conceptos').click();" <?=$disabled_conceptos?> />
	            <input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'conceptos'); totalizar();" <?=$disabled_conceptos?> />
	        </td>
	    </tr>
	    </tbody>
	</table>
	<div style="overflow:scroll; width:<?=$_width?>px; height:310px;">
	<table width="100%" class="tblLista">
	    <thead>
	    <tr>
	        <th scope="col" width="15">&nbsp;</th>
	        <th scope="col" align="left">Concepto</th>
	        <th scope="col" width="75">Unid. Vi&aacute;tico</th>
	        <th scope="col" width="75">Unid. Tributaria</th>
	        <th scope="col" width="75">Monto Vi&aacute;tico</th>
	        <th scope="col" width="75">Dias Comisi&oacute;n</th>
	        <th scope="col" width="75">Monto Total</th>
	    </tr>
	    </thead>
	    
	    <tbody id="lista_conceptos">
	    <?php
		$nro_conceptos = 0;
		$sql = "SELECT
					vd.*,
					cgv.FlagMonto,
					cgv.FlagCantidad,
					pv.CategoriaProg
				FROM
					ap_viaticosdetalle vd
					INNER JOIN ap_conceptogastoviatico cgv ON (cgv.CodConcepto = vd.CodConcepto)
					LEFT JOIN pv_presupuesto pv ON (pv.CodOrganismo = vd.CodOrganismo AND pv.CodPresupuesto = vd.CodPresupuesto)
				WHERE
					vd.CodOrganismo = '".$CodOrganismo."' AND
					vd.CodViatico = '".$CodViatico."'
				ORDER BY Articulo, Numeral";
		$field_conceptos = getRecords($sql);
		foreach($field_conceptos as $f) {	$nro_conceptos++;
			$id = $nro_conceptos;
			if ($f['FlagMonto'] == "N" && $f['FlagCantidad'] == "S") {
				$dMontoViatico = "disabled";
				$dCantidadDias = "";
				$MontoViatico = $f['ValorUT'] * $UnidadTributaria;
				$MontoTotal = 0;
			}
			elseif ($f['FlagMonto'] == "S" && $f['FlagCantidad'] == "N") {
				$dMontoViatico = "";
				$dCantidadDias = "disabled";
			}
			else if ($f['FlagMonto'] == "N" && $f['FlagCantidad'] == "N") {
				$dMontoViatico = "disabled";
				$dCantidadDias = "disabled";
			}
			?>
	        <tr class="trListaBody" onclick="clk($(this), 'conceptos', 'conceptos_<?=$id?>');" id="conceptos_<?=$id?>">
				<th>
					<?=$nro_conceptos?>
	                <input type="hidden" name="Secuencia" value="<?=$nro_conceptos?>" />
	                <input type="hidden" name="FlagMonto" id="FlagMonto_<?=$id?>" value="<?=$f['FlagMonto']?>" />
	                <input type="hidden" name="FlagCantidad" id="FlagCantidad_<?=$id?>" value="<?=$f['FlagCantidad']?>" />
	                <input type="hidden" name="cod_partida" value="<?=$f['cod_partida']?>" />
	                <input type="hidden" name="CodCuenta" value="<?=$f['CodCuenta']?>" />
	                <input type="hidden" name="CodCuentaPub20" value="<?=$f['CodCuentaPub20']?>" />
					<input type="hidden" name="detalleCodPresupuesto" value="<?=$f['CodPresupuesto']?>" />
					<input type="hidden" name="detalleCodFuente" value="<?=$f['CodFuente']?>" />
					<input type="hidden" name="detalleCategoriaProg" value="<?=$f['CategoriaProg']?>" />
				</th>
				<td>
	                <input type="hidden" name="CodConcepto" id="CodConcepto_<?=$id?>" value="<?=$f['CodConcepto']?>" />
	                <textarea name="Descripcion" style="height:25px;" class="cell" <?=$disabled_conceptos?>><?=htmlentities($f['Descripcion'])?></textarea>
				</td>
				<td>
	                <input type="text" name="ValorUT" id="ValorUT_<?=$id?>" value="<?=number_format($f['ValorUT'], 2, ',', '.')?>" style="text-align:right;" class="cell" disabled />
				</td>
				<td>
	                <input type="text" name="UnidadTributaria" id="UnidadTributaria_<?=$id?>" value="<?=number_format($f['UnidadTributaria'], 2, ',', '.')?>" style="text-align:right;" class="cell" disabled />
				</td>
				<td>
	                <input type="text" name="MontoViatico" id="MontoViatico_<?=$id?>" value="<?=number_format($f['MontoViatico'], 2, ',', '.')?>" style="text-align:right;" class="cell currency" onchange="viaticos_calculo('<?=$id?>');" <?=$dMontoViatico?> <?=$disabled_conceptos?> />
				</td>
				<td>
	                <input type="text" name="CantidadDias" id="CantidadDias_<?=$id?>" value="<?=number_format($f['CantidadDias'], 2, ',', '.')?>" style="text-align:right;" class="cell currency" onchange="viaticos_calculo('<?=$id?>');" <?=$dCantidadDias?> <?=$disabled_conceptos?> />
				</td>
				<td>
	                <input type="text" name="MontoTotal" id="MontoTotal_<?=$id?>" value="<?=number_format($f['MontoTotal'], 2, ',', '.')?>" style="text-align:right;" class="cell" disabled />
				</td>
			</tr>
			<?php
		}
	    ?>
	    </tbody>
	</table>
	</div>
	<input type="hidden" id="nro_conceptos" value="<?=$nro_conceptos?>" />
	<input type="hidden" id="can_conceptos" value="<?=$nro_conceptos?>" />
	</form>
	</center>
</div>

<div id="tab3" style="display:none;">
	<center>
	<div style="width:<?=$_width-5?>px;" class="divFormCaption">Distribuci&oacute;n Contable</div>
	<div style="overflow:scroll; width:<?=$_width?>px; height:102px;">
	<table width="100%" class="tblLista">
	    <thead>
	    <tr>
	        <th width="100">C&oacute;digo</th>
	        <th align="left">Descripci&oacute;n</th>
	        <th width="125">Monto Total</th>
	    </tr>
	    </thead>
	    
	    <tbody id="lista_cuentas">
	    <?php
		$sql = "SELECT
					vd.CodCuenta As Codigo,
					vd.Monto,
					pc.Descripcion
				FROM
					ap_viaticosdistribucion vd
					INNER JOIN ac_mastplancuenta pc ON (pc.CodCuenta = vd.CodCuenta)
				WHERE
					vd.CodOrganismo = '".$CodOrganismo."' AND
					vd.CodViatico = '".$CodViatico."'
				ORDER BY Codigo";
		$field_cuentas = getRecords($sql);
		foreach($field_cuentas as $f) {
			?>
	        <tr class="trListaBody">
	            <td align="center"><?=$f['Codigo']?></td>
	            <td><?=$f['Descripcion']?></td>
	            <td align="right"><strong><?=number_format($f['Monto'], 2, ',', '.')?></strong></td>
	        </tr>
	        <?php
		}
	    ?>
	    </tbody>
	</table>
	</div>

	<div style="width:<?=$_width-5?>px;" class="divFormCaption">Distribuci&oacute;n Contable (Pub. 20)</div>
	<div style="overflow:scroll; width:<?=$_width?>px; height:102px;">
	<table width="100%" class="tblLista">
	    <thead>
	    <tr>
	        <th width="100">C&oacute;digo</th>
	        <th align="left">Descripci&oacute;n</th>
	        <th width="125">Monto Total</th>
	    </tr>
	    </thead>
	    
	    <tbody id="lista_cuentas20">
	    <?php
		$sql = "SELECT
					vd.CodCuentaPub20 As Codigo,
					vd.Monto,
					pc.Descripcion
				FROM
					ap_viaticosdistribucion vd
					INNER JOIN ac_mastplancuenta20 pc ON (pc.CodCuenta = vd.CodCuentaPub20)
				WHERE
					vd.CodOrganismo = '".$CodOrganismo."' AND
					vd.CodViatico = '".$CodViatico."'
				ORDER BY Codigo";
		$field_cuentas20 = getRecords($sql);
		foreach($field_cuentas20 as $f) {
			?>
	        <tr class="trListaBody">
	            <td align="center"><?=$f['Codigo']?></td>
	            <td><?=$f['Descripcion']?></td>
	            <td align="right"><strong><?=number_format($f['Monto'], 2, ',', '.')?></strong></td>
	        </tr>
	        <?php
		}
	    ?>
	    </tbody>
	</table>
	</div>

	<div style="width:<?=$_width-5?>px;" class="divFormCaption">Distribuci&oacute;n Presupuestaria</div>
	<div style="overflow:scroll; width:<?=$_width?>px; height:112px;">
	<table width="100%" class="tblLista">
	    <thead>
	    <tr>
	        <th width="25">F.F</th>
	        <th width="100">C&oacute;digo</th>
	        <th align="left">Descripci&oacute;n</th>
	        <th width="125">Monto Total</th>
	    </tr>
	    </thead>
	    
	    <tbody id="lista_partidas">
	    <?php
		$sql = "SELECT
					vd.cod_partida As Codigo,
					vd.Monto,
					vd.CodFuente,
					pc.denominacion AS Descripcion
				FROM
					ap_viaticosdistribucion vd
					INNER JOIN pv_partida pc ON (pc.cod_partida = vd.cod_partida)
				WHERE
					vd.CodOrganismo = '".$CodOrganismo."' AND
					vd.CodViatico = '".$CodViatico."'
				ORDER BY Codigo";
		$field_partidas = getRecords($sql);
		foreach($field_partidas as $f) {
			$sql = "SELECT
						pv.CategoriaProg,
						ue.Denominacion AS UnidadEjecutora,
						CONCAT(ss.CodSector, pr.CodPrograma, a.CodActividad) AS CatProg
					FROM pv_presupuesto pv
					INNER JOIN pv_categoriaprog cp ON (cp.CategoriaProg = pv.CategoriaProg)
					INNER JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cp.CodUnidadEjec)
					LEFT JOIN pv_actividades a ON (a.IdActividad = cp.IdActividad)
					LEFT JOIN pv_proyectos py ON (py.IdProyecto = a.IdProyecto)
					LEFT JOIN pv_subprogramas spr ON (spr.IdSubPrograma = py.IdSubPrograma)
					LEFT JOIN pv_programas pr ON (pr.IdPrograma = spr.IdPrograma)
					LEFT JOIN pv_subsector ss ON (ss.IdSubSector = pr.IdSubSector)
					WHERE pv.CodOrganismo = '$CodOrganismo' AND pv.CodPresupuesto = '$field[CodPresupuesto]'";
			$field_categoria = getRecord($sql);
			?>
			<tr class="trListaBody2">
				<td colspan="4">
					<?=$field_categoria['CatProg'].' - '.$field_categoria['UnidadEjecutora']?>
				</td>
			</tr>
	        <tr class="trListaBody">
	            <td align="center"><?=$f['CodFuente']?></td>
	            <td align="center"><?=$f['Codigo']?></td>
	            <td><?=$f['Descripcion']?></td>
	            <td align="right"><strong><?=number_format($f['Monto'], 2, ',', '.')?></strong></td>
	        </tr>
	        <?php
		}
	    ?>
	    </tbody>
	</table>
	</div>
	</center>
</div>

<div style="width:<?=$_width?>px" class="divMsj">Campos Obligatorios *</div>

<script type="text/javascript" language="javascript">
	//	valido y envio formulario
	function form(form, accion) {
		bloqueo(true);
		//	valido
		var error = "";
		if ($("#Fecha").val().trim() == "" || $("#CodPersona").val().trim() == "" || $("#DescripcionGral").val().trim() == "" || $("#Motivo").val().trim() == "") error = "Debe llenar los campos obligatorios";
		else if (!valFecha($('#Fecha').val())) error = "Formato del campo Fecha Viaje incorrecto";
		
		//	detalles
		var detalles = "";
		$("input, select, textarea, hidden", "#lista_conceptos").each(function () {
			if ($(this).attr('name') == 'cod_partida') detalles += $(this).val() + "|";
			else if ($(this).attr('name') == 'CodCuenta') detalles += $(this).val() + "|";
			else if ($(this).attr('name') == 'CodCuentaPub20') detalles += $(this).val() + "|";
			else if ($(this).attr('name') == 'detalleCodPresupuesto') detalles += $(this).val() + "|";
			else if ($(this).attr('name') == 'detalleCodFuente') detalles += $(this).val() + "|";
			else if ($(this).attr('name') == 'CodConcepto') detalles += $(this).val() + "|";
			else if ($(this).attr('name') == 'Descripcion') detalles += $(this).val() + "|";
			else if ($(this).attr('name') == 'ValorUT') detalles += setNumero($(this).val()) + "|";
			else if ($(this).attr('name') == 'UnidadTributaria') detalles += setNumero($(this).val()) + "|";
			else if ($(this).attr('name') == 'MontoViatico') detalles += setNumero($(this).val()) + "|";
			else if ($(this).attr('name') == 'CantidadDias') detalles += setNumero($(this).val()) + "|";
			else if ($(this).attr('name') == 'MontoTotal') {
				var MontoTotal = setNumero($(this).val());
				if (isNaN(MontoTotal) || MontoTotal == 0) { error = "Se encontraron lineas con Totales en Cero"; }
				else detalles += MontoTotal + "||";
			}
		});
		var len = detalles.length; len-=2;
		detalles = detalles.substr(0, len);
		if (accion == "revisar" && detalles == "") error = "Debe insertar los conceptos de vi&aacute;ticos";
		
		//	valido errores
		if (error != "") {
			cajaModal(error, "error", 400);
		} else {
			//	formulario
			var post = getForm(form);
			var NomDependencia = $('#CodDependencia option:selected').text();
			var DescripCargo = $('#CodCargo option:selected').text();
			post += "&NomDependencia="+NomDependencia+"&DescripCargo="+DescripCargo;
			//	ajax
			$.ajax({
				type: "POST",
				url: "ap_viaticos_ajax.php",
				data: "modulo=viaticos&accion="+accion+"&"+post+"&detalles="+detalles,
				async: false,
				success: function(resp) {
					if (resp.trim() != "") cajaModal(resp, "error", 400);
					else form.submit();
				}
			});
		}
		return false;
	}
	//	calculo el valor del concepto
	function viaticos_calculo(id) {
		//	valores
		var FlagMonto = $('#FlagMonto_'+id).val();
		var FlagCantidad = $('#FlagCantidad_'+id).val();
		var ValorUT = setNumero($('#ValorUT_'+id).val());
		var UnidadTributaria = setNumero($('#UnidadTributaria_'+id).val());
		var MontoViatico = setNumero($('#MontoViatico_'+id).val());
		var CantidadDias = setNumero($('#CantidadDias_'+id).val());
		var MontoTotal = 0;
		//	valido
		if (FlagMonto == "N" && FlagCantidad == "S") MontoTotal = MontoViatico * CantidadDias;
		else if (FlagMonto == "S" && FlagCantidad == "N") MontoTotal = MontoViatico;
		//	asigno total
		$('#MontoTotal_'+id).val(MontoTotal).formatCurrency();
		//	totalizo
		totalizar();
	}
	//	totalizar conceptos
	function totalizar() {
		var Monto = new Number();
		$("input[name='MontoTotal']", "#lista_conceptos").each(function () {
			var MontoTotal = setNumero($(this).val());
			Monto += MontoTotal;
		});
		$("#Monto").val(Monto).formatCurrency();
	}
	//	distribuir conceptos de gastos
	function viaticos_distribucion() {
		//	...
		$('#lista_cuentas').html('Cargando...');
		$('#lista_cuentas20').html('Cargando...');
		$('#lista_partidas').html('Cargando...');
		//	detalles
		var detalles = "";
		$("input, select, textarea, hidden", "#lista_conceptos").each(function () {
			if ($(this).attr('name') == 'cod_partida') detalles += $(this).val() + "|";
			else if ($(this).attr('name') == 'CodCuenta') detalles += $(this).val() + "|";
			else if ($(this).attr('name') == 'CodCuentaPub20') detalles += $(this).val() + "|";
			else if ($(this).attr('name') == 'detalleCodPresupuesto') detalles += $(this).val() + "|";
			else if ($(this).attr('name') == 'detalleCodFuente') detalles += $(this).val() + "|";
			else if ($(this).attr('name') == 'detalleCategoriaProg') detalles += $(this).val() + "|";
			else if ($(this).attr('name') == 'MontoTotal') detalles += setNumero($(this).val()) + "||";
		});
		var len = detalles.length; len-=2;
		detalles = detalles.substr(0, len);
		//	ajax
		$.ajax({
			dataType: "json",
			url: "ap_viaticos_ajax.php",
			data: "modulo=ajax&accion=viaticos_distribucion&detalles="+detalles+"&CodOrganismo="+$('#CodOrganismo').val(),
			async: false,
			success: function(resp) {
				$('#lista_cuentas').html(resp.cuentas);
				$('#lista_cuentas20').html(resp.cuentas20);
				$('#lista_partidas').html(resp.partidas);
			}
		});
	}
	//	activar selector de persona externa
	function setFlagPersonaExterna(boo) {
		$('#CodEmpleado').val('');
		$('#NomEmpleado').val('');
		$('#CodPersona').val('');
		$('#CodOrganismo').val('');
		$('#CodDependencia').val('');
		$('#CodCentroCosto').val('');
		$('#CodCargo').val('');
		if (boo) {
			$('#btEmpleado').attr('href','../lib/listas/listado_personas.php?filtrar=default&cod=CodPersona&nom=NomEmpleado&campo3=CodOrganismo&ventana=viaticos&EsOtros=S&iframe=true&width=825&height=400');
		} else {
			$('#btEmpleado').attr('href','../lib/listas/listado_empleados.php?filtrar=default&cod=CodEmpleado&nom=NomEmpleado&campo3=CodPersona&campo4=CodOrganismo&campo5=CodDependencia&campo6=CodCentroCosto&campo7=CodCargo&ventana=viaticos&iframe=true&width=100%&height=100%');
		}
	}
</script>