<?php
if ($opcion == "nuevo") {
	$field['Estado'] = "PE";
	$field['CodEmpleado'] = $_SESSION["CODEMPLEADO_ACTUAL"];
	$field['Solicitante'] = $_SESSION["CODPERSONA_ACTUAL"];
	$field['NomSolicitante'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
	$field['CodOrganismo'] = $_SESSION["ORGANISMO_ACTUAL"];
	$field['Anio'] = $AnioActual;
	$field['Periodo'] = "$AnioActual-$MesActual";
	$field['CodPais'] = $_PARAMETRO["PAISDEFAULT"];
	$field['CodEstado'] = $_PARAMETRO["ESTADODEFAULT"];
	$field['CodMunicipio'] = $_PARAMETRO["MUNICIPIODEFAULT"];
	$field['CodCiudad'] = $_PARAMETRO["CIUDADDEFAULT"];
	$accion = "nuevo";
	$_titulo = "Agregar Capacitaci&oacute;n";
	$label_submit = "Guardar";
	$disabled_ver = "";
	$disabled_costos = "";
	$disabled_hora = "";
	$disabled_gastos = "disabled";
	$disabled_organismo = "";
	$disabled_terminar = "disabled";
	$display_submit = "";
	$display_tab5 = "display:none;";
	$display_tab6 = "display:none;";
	$display_tab7 = "display:none;";
	$visible_ver = "";
	$opt_organismo = 0;
	$focus = "";
}
elseif ($opcion == "modificar" || $opcion == "ver" || $opcion == "aprobar" || $opcion == "iniciar" || $opcion == "terminar") {
	list($Anio, $CodOrganismo, $Capacitacion) = explode("_", $sel_registros);
	//	consulto datos generales
	$sql = "SELECT
				c.*,
				(c.CostoEstimado - c.MontoAsumido) AS Saldo,
				p.NomCompleto AS NomSolicitante,
				cs.Descripcion AS NomCurso,
				ce.Descripcion AS NomCentroEstudio,
				cd.CodMunicipio,
				m.CodEstado,
				e.CodPais
			FROM
				rh_capacitacion c
				INNER JOIN mastpersonas p ON (p.CodPersona = c.Solicitante)
				INNER JOIN rh_cursos cs ON (cs.CodCurso = c.CodCurso)
				INNER JOIN rh_centrosestudios ce ON (ce.CodCentroEstudio = c.CodCentroEstudio)
				INNER JOIN mastciudades cd ON (cd.CodCiudad = c.CodCiudad)
				INNER JOIN mastmunicipios m ON (m.CodMunicipio = cd.CodMunicipio)
				INNER JOIN mastestados e ON (e.CodEstado = m.CodEstado)
			WHERE
				c.Anio = '".$Anio."' AND
				c.CodOrganismo = '".$CodOrganismo."' AND
				c.Capacitacion = '".$Capacitacion."'";
	$field = getRecord($sql);
	##
	if ($opcion == "modificar") {
		$_titulo = "Modificar Capacitaci&oacute;n";
		$accion = "modificar";
		$label_submit = "Modificar";
		$disabled_ver = "";
		if ($field['FlagCostos'] == "S") $disabled_costos = "disabled";
		$disabled_hora = "";
		$disabled_gastos = "disabled";
		$disabled_organismo = "";
		$disabled_terminar = "disabled";
		$display_submit = "";
		$display_tab5 = "display:none;";
		$display_tab6 = "display:none;";
		$display_tab7 = "display:none;";
		$visible_ver = "";
		$opt_organismo = 1;
		$focus = "";
	}
	elseif ($opcion == "ver") {
		$_titulo = "Ver Capacitaci&oacute;n";
		$accion = "";
		$label_submit = "Modificar";
		$disabled_ver = "disabled";
		$disabled_costos = "disabled";
		$disabled_hora = "disabled";
		$disabled_gastos = "disabled";
		$disabled_organismo = "disabled";
		$disabled_terminar = "disabled";
		$display_submit = "display:none;";
		if ($field['Estado'] == 'IN' || $field['Estado'] == 'TE') $display_tab5 = ""; else $display_tab5 = "display:none;";
		$display_tab6 = "display:none;";
		$display_tab7 = "display:none;";
		$visible_ver = "visibility:hidden;";
		$opt_organismo = 1;
		$focus = "btCancelar";
	}
	elseif ($opcion == "aprobar") {
		$_titulo = "Aprobar Capacitaci&oacute;n";
		$accion = "aprobar";
		$label_submit = "Aprobar";
		$disabled_ver = "disabled";
		$disabled_costos = "disabled";
		$disabled_hora = "disabled";
		$disabled_gastos = "disabled";
		$disabled_organismo = "";
		$disabled_terminar = "disabled";
		$display_submit = "";
		$display_tab5 = "display:none;";
		$display_tab6 = "display:none;";
		$display_tab7 = "display:none;";
		$visible_ver = "visibility:hidden;";
		$opt_organismo = 1;
		$focus = "btSubmit";
	}
	elseif ($opcion == "iniciar") {
		$_titulo = "Iniciar Capacitaci&oacute;n";
		$accion = "iniciar";
		$label_submit = "Iniciar";
		$disabled_ver = "disabled";
		$disabled_costos = "disabled";
		$disabled_hora = "disabled";
		$disabled_gastos = "";
		$disabled_organismo = "";
		$disabled_terminar = "disabled";
		$display_submit = "";
		$display_tab5 = "";
		$display_tab6 = "display:none;";
		$display_tab7 = "display:none;";
		$visible_ver = "visibility:hidden;";
		$opt_organismo = 1;
		$focus = "btSubmit";
	}
	elseif ($opcion == "terminar") {
		$_titulo = "Terminar Capacitaci&oacute;n";
		$accion = "terminar";
		$label_submit = "Terminar";
		$disabled_ver = "disabled";
		$disabled_costos = "disabled";
		$disabled_hora = "disabled";
		$disabled_gastos = "disabled";
		$disabled_organismo = "";
		$disabled_terminar = "";
		$display_submit = "";
		$display_tab5 = "";
		$display_tab6 = "display:none;";
		$display_tab7 = "display:none;";
		$visible_ver = "visibility:hidden;";
		$opt_organismo = 1;
		$focus = "btSubmit";
	}
}
$clkCancelar = "document.getElementById('frmentrada').submit();";
//	------------------------------------
$_width = 900;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<table align="center" cellpadding="0" cellspacing="0" style="width:<?=$_width?>px;">
    <tr>
        <td>
            <div class="header">
	            <ul id="tab">
		            <!-- CSS Tabs -->
		            <li id="li1" onclick="currentTab('tab', this);" class="current"><a href="#" onclick="mostrarTab('tab', 1, 7);">Capacitaci&oacute;n</a></li>
		            <li id="li2" onclick="currentTab('tab', this);"><a href="#" onclick="mostrarTab('tab', 2, 7);">Fundamentaci&oacute;n</a></li>
		            <li id="li3" onclick="currentTab('tab', this);"><a href="#" onclick="mostrarTab('tab', 3, 7);">Participantes</a></li>
		            <li id="li4" onclick="currentTab('tab', this);"><a href="#" onclick="mostrarTab('tab', 4, 7);">Horario</a></li>
		            <li id="li5" onclick="currentTab('tab', this);"><a href="#" onclick="mostrarTab('tab', 5, 7);" style=" <?=$display_tab5?>">Gastos</a></li>
		            <li id="li6" onclick="currentTab('tab', this);"><a href="#" onclick="mostrarTab('tab', 6, 7);" style=" <?=$display_tab6?>">Evaluaci&oacute;n del Curso</a></li>
		            <li id="li7" onclick="currentTab('tab', this);"><a href="#" onclick="mostrarTab('tab', 7, 7);" style=" <?=$display_tab7?>">Reporte de Evaluaci&oacute;n</a></li>
	            </ul>
            </div>
        </td>
    </tr>
</table>

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=<?=$origen?>" method="POST" enctype="multipart/form-data" onsubmit="return formulario(this, '<?=$accion?>');" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
<input type="hidden" name="fFechaD" id="fFechaD" value="<?=$fFechaD?>" />
<input type="hidden" name="fFechaH" id="fFechaH" value="<?=$fFechaH?>" />
<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
<input type="hidden" name="fCodCurso" id="fCodCurso" value="<?=$fCodCurso?>" />
<input type="hidden" name="fNomCurso" id="fNomCurso" value="<?=$fNomCurso?>" />
<input type="hidden" name="fTipoCurso" id="fTipoCurso" value="<?=$fTipoCurso?>" />
<input type="hidden" name="fTipoCapacitacion" id="fTipoCapacitacion" value="<?=$fTipoCapacitacion?>" />
<input type="hidden" name="fCodCentroEstudio" id="fCodCentroEstudio" value="<?=$fCodCentroEstudio?>" />
<input type="hidden" name="fNomCentroEstudio" id="fNomCentroEstudio" value="<?=$fNomCentroEstudio?>" />
<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
<input type="hidden" name="fModalidad" id="fModalidad" value="<?=$fModalidad?>" />
<input type="hidden" name="Anio" id="Anio" value="<?=$field['Anio']?>" />

<div id="tab1" style="display:block;">
<table width="<?=$_width?>" class="tblForm">
	<tr>
    	<td colspan="4" class="divFormCaption">Informaci&oacute;n General</td>
    </tr>
	<tr>
		<td class="tagForm" width="150">* Organismo:</td>
		<td>
			<select name="CodOrganismo" id="CodOrganismo" style="width:275px;" <?=$disabled_organismo?>>
				<?=getOrganismos($field['CodOrganismo'], $opt_organismo)?>
			</select>
		</td>
		<td class="tagForm" width="150">Nro. Capacitaci&oacute;n:</td>
		<td>
			<input type="text" name="Capacitacion" id="Capacitacion" style="width:75px;" class="codigo" value="<?=$field['Capacitacion']?>" readonly />
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Solicitante:</td>
		<td class="gallery clearfix">
            <input type="hidden" name="Solicitante" id="Solicitante" value="<?=$field['Solicitante']?>" />
            <input type="hidden" name="CodSolicitante" id="CodSolicitante" value="<?=$field['Solicitante']?>" />
			<input type="text" name="NomSolicitante" id="NomSolicitante" style="width:275px;" value="<?=htmlentities($field['NomSolicitante'])?>" disabled />
            <a href="../lib/listas/gehen.php?anz=lista_empleados&filtrar=default&campo1=Solicitante&campo2=NomSolicitante&campo3=CodSolicitante&iframe=true&width=100%&height=430" rel="prettyPhoto[iframe1]" id="btSolicitante" style=" <?=$visible_ver?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
		<td class="tagForm">Estado:</td>
		<td>
            <input type="hidden" name="Estado" id="Estado" value="<?=$field['Estado']?>" />
            <input type="text" style="width:75px;" class="codigo" value="<?=strtoupper(printValores("ESTADO-CAPACITACION", $field['Estado']))?>" disabled="disabled" />
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Curso:</td>
		<td class="gallery clearfix">
            <input type="hidden" name="CodCurso" id="CodCurso" value="<?=$field['CodCurso']?>" />
			<input type="text" name="NomCurso" id="NomCurso" style="width:275px;" value="<?=htmlentities($field['NomCurso'])?>" disabled />
            <a href="../lib/listas/gehen.php?anz=lista_cursos&filtrar=default&campo1=CodCurso&campo2=NomCurso&iframe=true&width=100%&height=410" rel="prettyPhoto[iframe2]" id="btCurso" style=" <?=$visible_ver?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
		<td class="tagForm">Periodo:</td>
		<td>
			<input type="text" name="Periodo" id="Periodo" style="width:75px;" class="codigo" value="<?=$field['Periodo']?>" readonly />
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Centro de Capacitaci&oacute;n:</td>
		<td class="gallery clearfix">
            <input type="hidden" name="CodCentroEstudio" id="CodCentroEstudio" value="<?=$field['CodCentroEstudio']?>" />
			<input type="text" name="NomCentroEstudio" id="NomCentroEstudio" style="width:275px;" value="<?=htmlentities($field['NomCentroEstudio'])?>" disabled />
            <a href="../lib/listas/gehen.php?anz=lista_centro_estudio&filtrar=default&campo1=CodCentroEstudio&campo2=NomCentroEstudio&iframe=true&width=100%&height=410" rel="prettyPhoto[iframe3]" id="btCentro" style=" <?=$visible_ver?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
		<td class="tagForm">* Tipo de Capacitaci&oacute;n:</td>
		<td>
            <select name="TipoCurso" id="TipoCurso" style="width:175px;" <?=$disabled_ver?>>
                <?=getMiscelaneos($field['TipoCurso'], "TIPOCURSO", 0)?>
            </select>
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Pais:</td>
		<td>
            <select name="CodPais" id="CodPais" style="width:175px;;" onchange="getOptionsSelect(this.value, 'estado', 'CodEstado', true, 'CodMunicipio', 'CodCiudad');" <?=$disabled_ver?>>
                <?=loadSelect("mastpaises", "CodPais", "Pais", $field['CodPais'], 0);?>
            </select>
		</td>
		<td class="tagForm">* Modalidad:</td>
		<td>
            <select name="Modalidad" id="Modalidad" style="width:175px;" <?=$disabled_ver?>>
                <?=getMiscelaneos($field['Modalidad'], "MODACAPAC", 0)?>
            </select>
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Estado:</td>
		<td>
            <select name="CodEstado" id="CodEstado" style="width:175px;;" onchange="getOptionsSelect(this.value, 'municipio', 'CodMunicipio', true, 'CodCiudad');" <?=$disabled_ver?>>
                <?=loadSelectDependienteEstado($field['CodEstado'], $field['CodPais'], 0);?>
            </select>
		</td>
		<td class="tagForm">* Origen:</td>
		<td>
            <select name="TipoCapacitacion" id="TipoCapacitacion" style="width:66px;" <?=$disabled_ver?>>
                <?=loadSelectValores("TIPO-CAPACITACION", $field['TipoCapacitacion'], 0)?>
            </select>
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Municipio:</td>
		<td>
            <select name="CodMunicipio" id="CodMunicipio" style="width:175px;;" onchange="getOptionsSelect(this.value, 'ciudad', 'CodCiudad', true);" <?=$disabled_ver?>>
                <?=loadSelectDependiente("mastmunicipios", "CodMunicipio", "Municipio", "CodEstado", $field['CodMunicipio'], $field['CodEstado'], 0);?>
            </select>
		</td>
		<td class="tagForm">* Vacantes:</td>
		<td>
			<input type="text" name="Vacantes" id="Vacantes" style="width:60px;" maxlength="3" value="<?=$field['Vacantes']?>" <?=$disabled_ver?> />
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Ciudad:</td>
		<td>
            <select name="CodCiudad" id="CodCiudad" style="width:175px;;" <?=$disabled_ver?>>
                <?=loadSelectDependiente("mastciudades", "CodCiudad", "Ciudad", "CodMunicipio", $field['CodCiudad'], $field['CodMunicipio'], 0);?>
            </select>
		</td>
		<td class="tagForm">Participantes:</td>
		<td>
			<input type="text" name="Participantes" id="Participantes" style="width:60px;" maxlength="10" value="<?=$field['Participantes']?>" readonly />
		</td>
	</tr>
    <tr>
		<td class="tagForm">Expositor:</td>
		<td>
			<input type="text" name="Expositor" id="Expositor" style="width:275px;" maxlength="50" value="<?=htmlentities($field['Expositor'])?>" <?=$disabled_ver?> />
		</td>
		<td class="tagForm">* Inicio:</td>
		<td>
        	<input type="text" name="FechaDesde" id="FechaDesde" value="<?=formatFechaDMA($field['FechaDesde'])?>" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" <?=$disabled_ver?> />
		</td>
	</tr>
    <tr>
		<td class="tagForm">Telefono Contacto:</td>
		<td>
			<input type="text" name="TelefonoContacto" id="TelefonoContacto" style="width:75px;" maxlength="15" value="<?=$field['TelefonoContacto']?>" class="phone" <?=$disabled_ver?> />
		</td>
		<td class="tagForm">* Fin:</td>
		<td>
        	<input type="text" name="FechaHasta" id="FechaHasta" value="<?=formatFechaDMA($field['FechaHasta'])?>" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" <?=$disabled_ver?> />
		</td>
	</tr>
    <tr>
		<td class="tagForm">Aula:</td>
		<td>
			<input type="text" name="Aula" id="Aula" style="width:75px;" maxlength="5" value="<?=htmlentities($field['Aula'])?>" <?=$disabled_ver?> />
		</td>
		<td class="tagForm">Dias:</td>
		<td>
			<input type="text" name="Dias" id="Dias" style="width:25px;" maxlength="4" value="<?=$field['Dias']?>" readonly />
		</td>
	</tr>
    <tr>
		<td>&nbsp;</td>
		<td>
        	<input type="checkbox" name="FlagCostos" id="FlagCostos" value="S" <?=chkFlag($field['FlagCostos']);?> onclick="setFlagCostos(this.checked);" <?=$disabled_ver?> />
            Capacitaci&oacute;n sin Costo
		</td>
		<td class="tagForm">Horas:</td>
		<td>
			<input type="text" name="Horas" id="Horas" style="width:25px;" maxlength="4" value="<?=$field['Horas']?>" readonly /> 
		</td>
	</tr>
	<tr>
		<td class="tagForm">Observaciones:</td>
		<td colspan="3">
			<textarea name="Observaciones" id="Observaciones" style="width:92%; height:35px;" <?=$disabled_ver?>><?=$field['Observaciones']?></textarea>
		</td>
	</tr>
	<tr>
    	<td colspan="2" class="divFormCaption">Costo Estimado Total</td>
    	<td colspan="2" class="divFormCaption">Costo Real</td>
    </tr>
    <tr>
		<td class="tagForm">Costo Estimado:</td>
		<td>
			<input type="text" name="CostoEstimado" id="CostoEstimado" style="width:100px; text-align:right;" value="<?=number_format($field['CostoEstimado'], 2, ',', '.')?>" onFocus="numeroFocus(this);" onBlur="numeroBlur(this);" onchange="setCostoEstimadoTotal();" <?=$disabled_costos?> />
		</td>
		<td class="tagForm">Sub-Total:</td>
		<td>
			<input type="text" name="SubTotal" id="SubTotal" style="width:100px; text-align:right;" value="<?=number_format($field['SubTotal'], 2, ',', '.')?>" readonly />
		</td>
	</tr>
    <tr>
		<td class="tagForm">Monto Asumido:</td>
		<td>
			<input type="text" name="MontoAsumido" id="MontoAsumido" style="width:100px; text-align:right;" value="<?=number_format($field['MontoAsumido'], 2, ',', '.')?>" onFocus="numeroFocus(this);" onBlur="numeroBlur(this);" onchange="setCostoEstimadoTotal();" <?=$disabled_costos?> />
		</td>
		<td class="tagForm">Impuestos:</td>
		<td>
			<input type="text" name="Impuestos" id="Impuestos" style="width:100px; text-align:right;" value="<?=number_format($field['Impuestos'], 2, ',', '.')?>" readonly />
		</td>
	</tr>
    <tr>
		<td class="tagForm">Saldo:</td>
		<td>
			<input type="text" name="Saldo" id="Saldo" style="width:100px; text-align:right;" value="<?=number_format($field['Saldo'], 2, ',', '.')?>" onFocus="numeroFocus(this);" onBlur="numeroBlur(this);" readonly />
		</td>
		<td class="tagForm">Total:</td>
		<td>
			<input type="text" name="Total" id="Total" style="width:100px; text-align:right;" value="<?=number_format($field['Total'], 2, ',', '.')?>" readonly />
		</td>
	</tr>
	<tr>
		<td class="tagForm">&Uacute;ltima Modif.:</td>
		<td colspan="3">
			<input type="text" size="30" value="<?=$field['UltimoUsuario']?>" disabled="disabled" />
			<input type="text" size="25" value="<?=$field['UltimaFecha']?>" disabled="disabled" />
		</td>
	</tr>
</table>
</div>

<div id="tab2" style="display:none;">
<table width="<?=$_width?>" class="tblForm">
	<tr>
    	<td class="divFormCaption">Fundamentaci&oacute;n de Requerimiento (Para ser llenado por el Jefe Inmediato)</td>
    </tr>
	<tr>
		<td>
			1. ¿Cu&aacute;l es el objetivo de la capacitaci&oacute;n?.
		</td>
	</tr>
	<tr>
		<td align="center">
			<textarea name="Fundamentacion1" id="Fundamentacion1" style="width:99%; height:40px;" <?=$disabled_ver?>><?=$field['Fundamentacion1']?></textarea>
		</td>
	</tr>
	<tr>
		<td>
			2. ¿En qu&eacute; medida la capacitaci&oacute;n va en relaci&oacute;n a los objetivos del &aacute;rea y de la organizaci&oacute;n?.
		</td>
	</tr>
	<tr>
		<td align="center">
			<textarea name="Fundamentacion2" id="Fundamentacion2" style="width:99%; height:40px;" <?=$disabled_ver?>><?=$field['Fundamentacion2']?></textarea>
		</td>
	</tr>
	<tr>
		<td>
			3. Dias y horarios mas factibles para el dictado.
		</td>
	</tr>
	<tr>
		<td align="center">
			<textarea name="Fundamentacion3" id="Fundamentacion3" style="width:99%; height:40px;" <?=$disabled_ver?>><?=$field['Fundamentacion3']?></textarea>
		</td>
	</tr>
	<tr>
		<td>
			4. ¿Qu&eacute; se espera despu&eacute;s de la capacitaci&oacute;n?
		</td>
	</tr>
	<tr>
		<td align="center">
			<textarea name="Fundamentacion4" id="Fundamentacion4" style="width:99%; height:40px;" <?=$disabled_ver?>><?=$field['Fundamentacion4']?></textarea>
		</td>
	</tr>
	<tr>
		<td>
			5. ¿C&oacute;mo hace hoy su trabajo?
		</td>
	</tr>
	<tr>
		<td align="center">
			<textarea name="Fundamentacion5" id="Fundamentacion5" style="width:99%; height:40px;" <?=$disabled_ver?>><?=$field['Fundamentacion5']?></textarea>
		</td>
	</tr>
	<tr>
		<td>
			6. ¿Hay colaboradores dentro de la empresa que pueden dictar este curso?
		</td>
	</tr>
	<tr>
		<td align="center">
			<textarea name="Fundamentacion6" id="Fundamentacion6" style="width:99%; height:40px;" <?=$disabled_ver?>><?=$field['Fundamentacion6']?></textarea>
		</td>
	</tr>
</table>
</div>

<div id="tab3" style="display:none;">
<input type="hidden" id="sel_participantes" />
<table class="tblBotones" style="width:<?=$_width?>px;">
	<thead>
    <tr>
    	<th class="divFormCaption">Lista de Participantes</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td align="right" class="gallery clearfix">
            <a id="a_participantes" href="../lib/listas/gehen.php?anz=lista_empleados&filtrar=default&ventana=listado_insertar_linea_capacitaciones&detalle=participantes&modulo=ajax&accion=participantes_insertar&url=../../rh/rh_capacitaciones_ajax.php&iframe=true&width=100%&height=430" rel="prettyPhoto[iframe4]" style="display:none;"></a>
            <input type="button" class="btLista" value="Insertar" onclick="$('#a_participantes').click();" <?=$disabled_ver?> />
            <input type="button" class="btLista" value="Borrar" onclick="quitarParticipantes(this, 'participantes'); totalizarHorario();" <?=$disabled_ver?> />
        </td>
    </tr>
    </tbody>
</table>
<div style="overflow:scroll; height:300px; width:<?=$_width?>px; margin:auto">
<table class="tblLista" style="width:100%;">
	<thead>
    <tr>
        <th width="15">&nbsp;</th>
        <th width="60">Empleado</th>
        <th align="left">Nombre Completo</th>
        <th width="55">Total Dias</th>
        <th width="55">Total Horas</th>
        <th width="55">Dias Asistidos</th>
        <th width="25">Apr.</th>
        <th width="55">Nota</th>
        <th width="100">Importe Gastos</th>
    </tr>
    </thead>
    
    <tbody id="lista_participantes">
    	<?php
		$nro_participantes = 0;
	    $sql = "SELECT
					ce.*,
					p.NomCompleto,
					e.CodEmpleado
				FROM
					rh_capacitacion_empleados ce
					INNER JOIN mastpersonas p ON (p.CodPersona = ce.CodPersona)
					INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
				WHERE
					ce.Anio = '".$field['Anio']."' AND
					ce.CodOrganismo = '".$field['CodOrganismo']."' AND
					ce.Capacitacion = '".$field['Capacitacion']."'
				ORDER BY CodEmpleado";
		$field_participantes = getRecords($sql);
		foreach ($field_participantes as $f) {
			$id = $f['CodPersona'];
			++$nro_participantes;
			?>
            <tr class="trListaBody" onclick="clk($(this), 'participantes', 'participantes_<?=$id?>');" id="participantes_<?=$id?>">
                <th><?=$nro_participantes?></th>
                <td align="center">
                	<input type="hidden" name="participantes_CodPersona[]" value="<?=$f['CodPersona']?>" />
                	<input type="hidden" name="participantes_CodDependencia[]" value="<?=$f['CodDependencia']?>" />
                    <?=$f['CodEmpleado']?>
                </td>
                <td>
                    <?=htmlentities($f['NomCompleto'])?>
                </td>
	            <td align="center">
	            	<input type="text" name="participantes_NroAsistencias[]" value="<?=$f['NroAsistencias']?>" class="cell NroAsistencias" style="text-align:center;" readonly />
	            </td>
	            <td align="center">
	            	<input type="text" name="participantes_HoraAsistencias[]" value="<?=$f['HoraAsistencias']?>" class="cell HoraAsistencias" style="text-align:center;" readonly />
	            </td>
	            <td align="center">
	            	<input type="text" name="participantes_DiasAsistidos[]" value="<?=$f['DiasAsistidos']?>" class="cell" style="text-align:center;" <?=$disabled_terminar?> />
	            </td>
	            <td align="center">
	            	<input type="checkbox" name="participantes_FlagAprobado<?=$id?>" value="S" <?=chkFlag($f['FlagAprobado'])?> <?=$disabled_terminar?> />
	            </td>
	            <td align="center">
	            	<input type="text" name="participantes_Nota[]" value="<?=number_format($f['Nota'],2,',','.')?>" class="cell currency" style="text-align:right;" <?=$disabled_terminar?> />
	            </td>
	            <td align="right">
	            	<input type="text" name="participantes_ImporteGastos[]" value="<?=number_format($f['ImporteGastos'],2,',','.')?>" class="cell ImporteGastos" style="text-align:right;" readonly />
	            </td>
            </tr>
            <?php
		}
		?>
    </tbody>
</table>
</div>
<input type="hidden" id="nro_participantes" value="<?=$nro_participantes?>" />
<input type="hidden" id="can_participantes" value="<?=$nro_participantes?>" />
</div>

<div id="tab4" style="display:none;">
<input type="hidden" id="sel_hora" />
<table class="tblBotones" style="width:<?=$_width?>px;">
	<thead>
    <tr>
    	<th class="divFormCaption">Detalle de los Horarios por Lapsos de Fecha</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td align="right">
            <input type="button" class="btLista" value="Insertar" onclick="insertar2(this, 'hora', 'modulo=ajax&accion=hora_insertar', 'rh_capacitaciones_ajax.php');" <?=$disabled_hora?> />
            <input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'hora'); totalizarHorario();" <?=$disabled_hora?> />
        </td>
    </tr>
    </tbody>
</table>
<div style="overflow:scroll; height:300px; width:<?=$_width?>px; margin:auto;">
<table class="tblLista" style="width:100%;">
    <tbody id="lista_hora">
    	<?php
		$nro_hora = 0;
		$sql = "SELECT *
				FROM rh_capacitacion_hora
				WHERE
					Anio = '".$Anio."' AND
					CodOrganismo = '".$CodOrganismo."' AND
					Capacitacion = '".$Capacitacion."'
				GROUP BY Secuencia
				ORDER BY Secuencia";
		$field_hora = getRecords($sql);
		foreach ($field_hora as $f) {
			$id = ++$nro_hora;
			?>
            <tr class="trListaBody" onclick="clk($(this), 'hora', 'hora_<?=$id?>');" id="hora_<?=$id?>">
                <th><?=$id?></th>
                <td>
                	<table border="1" width="100%">
					    <tr>
							<td class="tagForm" width="75">Estado:</td>
							<td>
								<select name="hora_Estado[]" style="width:75px;" <?=$disabled_hora?>>
									<?=loadSelectGeneral('ESTADO',$f['Estado'])?>
								</select>
								<input type="hidden" name="hora_Lunes[]" id="hora_Lunes<?=$id?>" value="<?=$f['Lunes']?>" />
								<input type="hidden" name="hora_Martes[]" id="hora_Martes<?=$id?>" value="<?=$f['Martes']?>" />
								<input type="hidden" name="hora_Miercoles[]" id="hora_Miercoles<?=$id?>" value="<?=$f['Miercoles']?>" />
								<input type="hidden" name="hora_Jueves[]" id="hora_Jueves<?=$id?>" value="<?=$f['Jueves']?>" />
								<input type="hidden" name="hora_Viernes[]" id="hora_Viernes<?=$id?>" value="<?=$f['Viernes']?>" />
								<input type="hidden" name="hora_Sabado[]" id="hora_Sabado<?=$id?>" value="<?=$f['Sabado']?>" />
								<input type="hidden" name="hora_Domingo[]" id="hora_Domingo<?=$id?>" value="<?=$f['Domingo']?>" />
							</td>
							<td align="center">
								L <input type="checkbox" value="S" <?=chkFlag($f['Lunes'])?> <?=$disabled_hora?> onclick="$('#hora_Lunes<?=$id?>').val(this.checked); $('#hora_HoraInicioLunes<?=$id?>').prop('readonly', !this.checked).val(''); $('#hora_HoraFinLunes<?=$id?>').prop('readonly', !this.checked).val(''); totalDiasHoras('<?=$id?>');" />
							</td>
							<td align="center">
								M <input type="checkbox" value="S" <?=chkFlag($f['Martes'])?> <?=$disabled_hora?> onclick="$('#hora_Martes<?=$id?>').val(this.checked); $('#hora_HoraInicioMartes<?=$id?>').prop('readonly', !this.checked).val(''); $('#hora_HoraFinMartes<?=$id?>').prop('readonly', !this.checked).val(''); totalDiasHoras('<?=$id?>');" />
							</td>
							<td align="center">
								M <input type="checkbox" value="S" <?=chkFlag($f['Miercoles'])?> <?=$disabled_hora?> onclick="$('#hora_Miercoles<?=$id?>').val(this.checked); $('#hora_HoraInicioMiercoles<?=$id?>').prop('readonly', !this.checked).val(''); $('#hora_HoraFinMiercoles<?=$id?>').prop('readonly', !this.checked).val(''); totalDiasHoras('<?=$id?>');" />
							</td>
							<td align="center">
								J <input type="checkbox" value="S" <?=chkFlag($f['Jueves'])?> <?=$disabled_hora?> onclick="$('#hora_Jueves<?=$id?>').val(this.checked); $('#hora_HoraInicioJueves<?=$id?>').prop('readonly', !this.checked).val(''); $('#hora_HoraFinJueves<?=$id?>').prop('readonly', !this.checked).val(''); totalDiasHoras('<?=$id?>');" />
							</td>
							<td align="center">
								V <input type="checkbox" value="S" <?=chkFlag($f['Viernes'])?> <?=$disabled_hora?> onclick="$('#hora_Viernes<?=$id?>').val(this.checked); $('#hora_HoraInicioViernes<?=$id?>').prop('readonly', !this.checked).val(''); $('#hora_HoraFinViernes<?=$id?>').prop('readonly', !this.checked).val(''); totalDiasHoras('<?=$id?>');" />
							</td>
							<td align="center">
								S <input type="checkbox" value="S" <?=chkFlag($f['Sabado'])?> <?=$disabled_hora?> onclick="$('#hora_Sabado<?=$id?>').val(this.checked); $('#hora_HoraInicioSabado<?=$id?>').prop('readonly', !this.checked).val(''); $('#hora_HoraFinSabado<?=$id?>').prop('readonly', !this.checked).val(''); totalDiasHoras('<?=$id?>');" />
							</td>
							<td align="center">
								D <input type="checkbox" value="S" <?=chkFlag($f['Domingo'])?> <?=$disabled_hora?> onclick="$('#hora_Domingo<?=$id?>').val(this.checked); $('#hora_HoraInicioDomingo<?=$id?>').prop('readonly', !this.checked).val(''); $('#hora_HoraFinDomingo<?=$id?>').prop('readonly', !this.checked).val(''); totalDiasHoras('<?=$id?>');" />
							</td>
							<td>Total</td>
						</tr>
					    <tr>
							<td class="tagForm">Desde:</td>
							<td valign="bottom">
								<input type="text" name="hora_FechaDesde[]" id="hora_FechaDesde<?=$id?>" value="<?=formatFechaDMA($f['FechaDesde'])?>" maxlength="10" style="width:70px;" class="datepicker" onkeyup="setFechaDMA(this);" <?=$disabled_hora?> onchange="totalDiasHoras('<?=$id?>');" />
							</td>
							<td align="center">
								<input type="text" name="hora_HoraInicioLunes[]" id="hora_HoraInicioLunes<?=$id?>" value="<?=formatHora12($f['HoraInicioLunes'])?>" maxlength="8" style="width:50px;" class="time" <?=$disabled_hora?> <?=($f['hora_Lunes']!='S')?'readonly':''?> onchange="totalDiasHoras('<?=$id?>');" />
							</td>
							<td align="center">
								<input type="text" name="hora_HoraInicioMartes[]" id="hora_HoraInicioMartes<?=$id?>" value="<?=formatHora12($f['HoraInicioMartes'])?>" maxlength="8" style="width:50px;" class="time" <?=$disabled_hora?> <?=($f['hora_Martes']!='S')?'readonly':''?> onchange="totalDiasHoras('<?=$id?>');" />
							</td>
							<td align="center">
								<input type="text" name="hora_HoraInicioMiercoles[]" id="hora_HoraInicioMiercoles<?=$id?>" value="<?=formatHora12($f['HoraInicioMiercoles'])?>" maxlength="8" style="width:50px;" class="time" <?=$disabled_hora?> <?=($f['hora_Miercoles']!='S')?'readonly':''?> onchange="totalDiasHoras('<?=$id?>');" />
							</td>
							<td align="center">
								<input type="text" name="hora_HoraInicioJueves[]" id="hora_HoraInicioJueves<?=$id?>" value="<?=formatHora12($f['HoraInicioJueves'])?>" maxlength="8" style="width:50px;" class="time" <?=$disabled_hora?> <?=($f['hora_Jueves']!='S')?'readonly':''?> onchange="totalDiasHoras('<?=$id?>');" />
							</td>
							<td align="center">
								<input type="text" name="hora_HoraInicioViernes[]" id="hora_HoraInicioViernes<?=$id?>" value="<?=formatHora12($f['HoraInicioViernes'])?>" maxlength="8" style="width:50px;" class="time" <?=$disabled_hora?> <?=($f['hora_Viernes']!='S')?'readonly':''?> onchange="totalDiasHoras('<?=$id?>');" />
							</td>
							<td align="center">
								<input type="text" name="hora_HoraInicioSabado[]" id="hora_HoraInicioSabado<?=$id?>" value="<?=formatHora12($f['HoraInicioSabado'])?>" maxlength="8" style="width:50px;" class="time" <?=$disabled_hora?> <?=($f['hora_Sabado']!='S')?'readonly':''?> onchange="totalDiasHoras('<?=$id?>');" />
							</td>
							<td align="center">
								<input type="text" name="hora_HoraInicioDomingo[]" id="hora_HoraInicioDomingo<?=$id?>" value="<?=formatHora12($f['HoraInicioDomingo'])?>" maxlength="8" style="width:50px;" class="time" <?=$disabled_hora?> <?=($f['hora_Domingo']!='S')?'readonly':''?> onchange="totalDiasHoras('<?=$id?>');" />
							</td>
							<td valign="bottom">
								<input type="text" name="hora_TotalDias[]" id="hora_TotalDias<?=$id?>" value="<?=$f['TotalDias']?>" style="width:50px;" class="hora_TotalDias" readonly /> <i>Dias</i>
							</td>
						</tr>
					    <tr>
							<td class="tagForm">Hasta:</td>
							<td valign="bottom">
								<input type="text" name="hora_FechaHasta[]" id="hora_FechaHasta<?=$id?>" value="<?=formatFechaDMA($f['FechaHasta'])?>" maxlength="10" style="width:70px;" class="datepicker" onkeyup="setFechaDMA(this);" <?=$disabled_hora?> onchange="totalDiasHoras('<?=$id?>');" />
							</td>
							<td align="center">
								<input type="text" name="hora_HoraFinLunes[]" id="hora_HoraFinLunes<?=$id?>" value="<?=formatHora12($f['HoraFinLunes'])?>" maxlength="8" style="width:50px;" class="time" <?=$disabled_hora?> <?=($f['hora_Lunes']!='S')?'readonly':''?> onchange="totalDiasHoras('<?=$id?>');" />
							</td>
							<td align="center">
								<input type="text" name="hora_HoraFinMartes[]" id="hora_HoraFinMartes<?=$id?>" value="<?=formatHora12($f['HoraFinMartes'])?>" maxlength="8" style="width:50px;" class="time" <?=$disabled_hora?> <?=($f['hora_Martes']!='S')?'readonly':''?> onchange="totalDiasHoras('<?=$id?>');" />
							</td>
							<td align="center">
								<input type="text" name="hora_HoraFinMiercoles[]" id="hora_HoraFinMiercoles<?=$id?>" value="<?=formatHora12($f['HoraFinMiercoles'])?>" maxlength="8" style="width:50px;" class="time" <?=$disabled_hora?> <?=($f['hora_Miercoles']!='S')?'readonly':''?> onchange="totalDiasHoras('<?=$id?>');" />
							</td>
							<td align="center">
								<input type="text" name="hora_HoraFinJueves[]" id="hora_HoraFinJueves<?=$id?>" value="<?=formatHora12($f['HoraFinJueves'])?>" maxlength="8" style="width:50px;" class="time" <?=$disabled_hora?> <?=($f['hora_Jueves']!='S')?'readonly':''?> onchange="totalDiasHoras('<?=$id?>');" />
							</td>
							<td align="center">
								<input type="text" name="hora_HoraFinViernes[]" id="hora_HoraFinViernes<?=$id?>" value="<?=formatHora12($f['HoraFinViernes'])?>" maxlength="8" style="width:50px;" class="time" <?=$disabled_hora?> <?=($f['hora_Viernes']!='S')?'readonly':''?> onchange="totalDiasHoras('<?=$id?>');" />
							</td>
							<td align="center">
								<input type="text" name="hora_HoraFinSabado[]" id="hora_HoraFinSabado<?=$id?>" value="<?=formatHora12($f['HoraFinSabado'])?>" maxlength="8" style="width:50px;" class="time" <?=$disabled_hora?> <?=($f['hora_Sabado']!='S')?'readonly':''?> onchange="totalDiasHoras('<?=$id?>');" />
							</td>
							<td align="center">
								<input type="text" name="hora_HoraFinDomingo[]" id="hora_HoraFinDomingo<?=$id?>" value="<?=formatHora12($f['HoraFinDomingo'])?>" maxlength="8" style="width:50px;" class="time" <?=$disabled_hora?> <?=($f['hora_Domingo']!='S')?'readonly':''?> onchange="totalDiasHoras('<?=$id?>');" />
							</td>
							<td valign="bottom">
								<input type="text" name="hora_TotalHoras[]" id="hora_TotalHoras<?=$id?>" value="<?=$f['TotalHoras']?>" style="width:50px;" class="hora_TotalHoras" readonly onfocus="totalDiasHoras('<?=$id?>');" /> <i>Horas</i>
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
<input type="hidden" id="nro_hora" value="<?=$nro_hora?>" />
<input type="hidden" id="can_hora" value="<?=$nro_hora?>" />
</div>

<div id="tab5" style="display:none;">
<input type="hidden" id="sel_gastos" />
<table class="tblBotones" style="width:<?=$_width?>px">
	<thead>
    <tr>
    	<th class="divFormCaption">Gastos</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td align="right">
            <input type="button" class="btLista" value="Insertar" onclick="insertar2(this, 'gastos', 'modulo=ajax&accion=gastos_insertar', 'rh_capacitaciones_ajax.php');" <?=$disabled_gastos?> />
            <input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'gastos');" <?=$disabled_gastos?> />
        </td>
    </tr>
    </tbody>
</table>
<div style="overflow:scroll; height:400px; max-width:<?=$_width?>px; margin:auto;">
<table class="tblLista" style="width:100%;">
	<thead>
    <tr>
        <th width="20">#</th>
        <th align="left">Comprobante</th>
        <th width="100">Fecha</th>
        <th width="100">Sub-Total</th>
        <th width="100">Impuestos</th>
        <th width="100">Total</th>
    </tr>
    </thead>
    
    <tbody id="lista_gastos">
    	<?php
		$nro_gastos = 0;
		$sql = "SELECT *
				FROM rh_capacitacion_gastos
				WHERE
					Anio = '".$field['Anio']."' AND
					CodOrganismo = '".$field['CodOrganismo']."' AND
					Capacitacion = '".$field['Capacitacion']."'
				ORDER BY Secuencia";
		$field_gastos = getRecords($sql);
		foreach ($field_gastos as $f) {
			$id = ++$nro_gastos;
			?>
            <tr class="trListaBody" onclick="clk($(this), 'gastos', 'gastos_<?=$id?>');" id="gastos_<?=$id?>">
                <th><?=$id?></th>
                <td>
                    <input type="text" name="gastos_Numero[]" value="<?=$f['Numero']?>" class="cell" maxlength="15" <?=$disabled_gastos?> />
                </td>
                <td>
                    <input type="text" name="gastos_Fecha[]" value="<?=formatFechaDMA($f['Fecha'])?>" class="cell datepicker" style="text-align:center;" maxlength="10" <?=$disabled_gastos?> />
                </td>
                <td>
                    <input type="text" name="gastos_SubTotal[]" id="gastos_SubTotal<?=$id?>" value="<?=number_format($f['SubTotal'],2,',','.')?>" class="cell currency gastos_SubTotal" style="text-align:right;" onchange="setCostos('<?=$id?>');" <?=$disabled_gastos?> />
                </td>
                <td>
                    <input type="text" name="gastos_Impuestos[]" id="gastos_Impuestos<?=$id?>" value="<?=number_format($f['Impuestos'],2,',','.')?>" class="cell currency gastos_Impuestos" style="text-align:right;" onchange="setCostos('<?=$id?>');" <?=$disabled_gastos?> />
                </td>
                <td>
                    <input type="text" name="gastos_Total[]" id="gastos_Total<?=$id?>" value="<?=number_format($f['Total'],2,',','.')?>" class="cell gastos_Total" style="text-align:right; font-weight:bold;" readonly />
                </td>
            </tr>
            <?php
		}
		?>
    </tbody>
</table>
</div>
<input type="hidden" id="nro_gastos" value="<?=$nro_gastos?>" />
<input type="hidden" id="can_gastos" value="<?=$nro_gastos?>" />
</div>

<div id="tab6" style="display:none;">
</div>

<div id="tab7" style="display:none;">
</div>

<center>
<input type="submit" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" id="btSubmit" />
<input type="button" value="Cancelar" style="width:75px;" id="btCancelar" onclick="<?=$clkCancelar?>" />
</center>
</form>
<div style="width:<?=$_width?>px" class="divMsj">Campos Obligatorios *</div>

<script type="text/javascript" language="javascript">
	//	valido formulario
	function formulario(form, accion) {
		bloqueo(true);
		//	ajax
		$.ajax({
			type: "POST",
			url: "rh_capacitaciones_ajax.php",
			data: "modulo=formulario&accion="+accion+"&"+$('#frmentrada').serialize(),
			async: false,
			success: function(resp) {
				if (resp.trim() != '') cajaModal(resp.trim(), 'error', 400);
				else form.submit();
			}
		});
		return false;
	}
	function setCostoEstimadoTotal() {
		var CostoEstimado = setNumero($("#CostoEstimado").val());
		var MontoAsumido = setNumero($("#MontoAsumido").val());
		var Saldo = CostoEstimado - MontoAsumido;
		$("#Saldo").val(Saldo).formatCurrency();
	}
	function setFlagCostos(boo) {
		if (boo) {
			$("#CostoEstimado").attr("disabled", "disabled").val("0,00");
			$("#MontoAsumido").attr("disabled", "disabled").val("0,00");
		} else {
			$("#CostoEstimado").removeAttr("disabled").val("0,00");
			$("#MontoAsumido").removeAttr("disabled").val("0,00");
		}
		$("#Saldo").val("0,00");
	}
	function totalDiasHoras(id) {
		var FechaDesde = $('#hora_FechaDesde' + id).val();
		var FechaHasta = $('#hora_FechaHasta' + id).val();
		var Lunes = $('#hora_Lunes' + id).val();
		var Martes = $('#hora_Martes' + id).val();
		var Miercoles = $('#hora_Miercoles' + id).val();
		var Jueves = $('#hora_Jueves' + id).val();
		var Viernes = $('#hora_Viernes' + id).val();
		var Sabado = $('#hora_Sabado' + id).val();
		var Domingo = $('#hora_Domingo' + id).val();
		var LunesI = $('#hora_HoraInicioLunes' + id).val();
		var LunesF = $('#hora_HoraFinLunes' + id).val();
		var MartesI = $('#hora_HoraInicioMartes' + id).val();
		var MartesF = $('#hora_HoraFinMartes' + id).val();
		var MiercolesI = $('#hora_HoraInicioMiercoles' + id).val();
		var MiercolesF = $('#hora_HoraFinMiercoles' + id).val();
		var JuevesI = $('#hora_HoraInicioJueves' + id).val();
		var JuevesF = $('#hora_HoraFinJueves' + id).val();
		var ViernesI = $('#hora_HoraInicioViernes' + id).val();
		var ViernesF = $('#hora_HoraFinViernes' + id).val();
		var SabadoI = $('#hora_HoraInicioSabado' + id).val();
		var SabadoF = $('#hora_HoraFinSabado' + id).val();
		var DomingoI = $('#hora_HoraInicioDomingo' + id).val();
		var DomingoF = $('#hora_HoraFinDomingo' + id).val();
		//	ajax
		$.ajax({
			type: "POST",
			url: "rh_capacitaciones_ajax.php",
			data: "modulo=ajax&accion=totalDiasHoras&FechaDesde="+FechaDesde+"&FechaHasta="+FechaHasta+"&Lunes="+Lunes+"&Martes="+Martes+"&Miercoles="+Miercoles+"&Jueves="+Jueves+"&Viernes="+Viernes+"&Sabado="+Sabado+"&Domingo="+Domingo+"&LunesI="+LunesI+"&LunesF="+LunesF+"&MartesI="+MartesI+"&MartesF="+MartesF+"&MiercolesI="+MiercolesI+"&MiercolesF="+MiercolesF+"&JuevesI="+JuevesI+"&JuevesF="+JuevesF+"&MiercolesI="+MiercolesI+"&MiercolesF="+MiercolesF+"&JuevesI="+JuevesI+"&JuevesF="+JuevesF+"&ViernesI="+ViernesI+"&ViernesF="+ViernesF+"&SabadoI="+SabadoI+"&SabadoF="+SabadoF+"&DomingoI="+DomingoI+"&DomingoF="+DomingoF,
			async: false,
			success: function(resp) {
				var datos = resp.split('|');
				$('#hora_TotalDias' + id).val(datos[0]);
				$('#hora_TotalHoras' + id).val(datos[1]);
				totalizarHorario();
			}
		});
	}
	function totalizarHorario() {
		var TotalDias = 0;
		$(".hora_TotalDias").each(function(index) {
			TotalDias = TotalDias + parseInt($(this).val());
		});

		var TotalHoras = '';
		var totalh = 0;
		var totalm = 0;
		$(".hora_TotalHoras").each(function(index) {
			var h = $(this).val().split(':');
			totalh = totalh + parseInt(h[0]);
			totalm = totalm + parseInt(h[1]);
		});
		if (totalm >= 60) {
			var hsumar = totalm / 60;
			totalh = totalh + hsumar;
			totalm = totalm - (60 * hsumar);
		}
		TotalHoras = totalh + ':' + totalm;

		$('.NroAsistencias').val(TotalDias);
		$('.HoraAsistencias').val(TotalHoras);
		$('#Dias').val(TotalDias);
		$('#Horas').val(TotalHoras);
	}
	function setCostos(id) {
		var SubTotal = setNumero($('#gastos_SubTotal' + id).val());
		var Impuestos = setNumero($('#gastos_Impuestos' + id).val());
		var Total = SubTotal + Impuestos;
		$('#gastos_Total' + id).val(Total).formatCurrency();
		totalizarCostos();
	}
	function totalizarCostos() {
		var gastos_SubTotal = 0;
		$(".gastos_SubTotal").each(function(index) {
			gastos_SubTotal = gastos_SubTotal + setNumero($(this).val());
		});
		var gastos_Impuestos = 0;
		$(".gastos_Impuestos").each(function(index) {
			gastos_Impuestos = gastos_Impuestos + setNumero($(this).val());
		});
		var gastos_Total = 0;
		$(".gastos_Total").each(function(index) {
			gastos_Total = gastos_Total + setNumero($(this).val());
		});
		$('#SubTotal').val(gastos_SubTotal).formatCurrency();
		$('#Impuestos').val(gastos_Impuestos).formatCurrency();
		$('#Total').val(gastos_Total).formatCurrency();
		var ImporteGastos = gastos_Total / 3;
		$('.ImporteGastos').val(ImporteGastos.toFixed(2)).formatCurrency();
	}
	function quitarParticipantes(boton, detalle) {
		/*
		.- boton	-> referencia del boton (objeto)
		.- detalle	-> sufijo de los campos de la lista
		*/
		boton.disabled = true;
		var can = "#can_" + detalle;
		var sel = "#sel_" + detalle;	
		var lista = "#lista_" + detalle;
		if ($(sel).val() == "") cajaModal("Debe seleccionar una linea", "error", 400);
		else {
			var candetalle = parseInt($(can).val()); candetalle--;
			$(can).val(candetalle);
			$(sel).val("");
			$(lista+" .trListaBodySel").remove();
			$('#Participantes').val(candetalle);
		}
		boton.disabled = false;
	}
	$(document).ready(function() {
		totalizarHorario();
		totalizarCostos();
	});
</script>