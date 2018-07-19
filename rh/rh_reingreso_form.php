<?php
list($AnioActual, $MesActual, $DiaActual) = split("[/.-]", substr($Ahora, 0, 10));
$FechaActual = "$AnioActual-$MesActual-$DiaActual";
if ($opcion == "nuevo") {
	$field['Estado'] = "PE";
	$field['CreadoPor'] = $_SESSION['CODPERSONA_ACTUAL'];
	$field['NomCreadoPor'] = $_SESSION['NOMBRE_USUARIO_ACTUAL'];
	$field['FechaCreado'] = $Ahora;
	$field['Fecha'] = $FechaActual;
	$field['Periodo'] = "$AnioActual-$MesActual";
	##
	$_titulo = "Nuevo Cese/Reingreso";
	$accion = "nuevo";
	$label_submit = "Procesar";
	$disabled_datos = "disabled";
	$disabled_conformado = "disabled";
	$disabled_aprobado = "disabled";
	$disabled_planilla = "disabled";
	$disabled_cese = "disabled";
	$visible_datos = "visibility:hidden;";
	$clkCancelar = "document.getElementById('frmentrada').submit();";
}
elseif ($opcion == "modificar" || $opcion == "ver" || $opcion == "conformar" || $opcion == "aprobar" || $opcion == "anular") {
	list($Tipo, $CodProceso) = split("[_]", $sel_registros);
	//	consulto datos generales
	$sql = "SELECT
				pc.*,
				p1.NomCompleto AS NomEmpleado,
				p1.Ndocumento,
				p1.Sexo,
				p1.Fnacimiento,
				e1.CodEmpleado,
				p2.NomCompleto AS NomCreadoPor,
				p3.NomCompleto AS NomConformadoPor,
				p4.NomCompleto AS NomAprobadoPor
			FROM
				rh_procesocesereing pc
				INNER JOIN mastpersonas p1 ON (p1.CodPersona = pc.CodPersona)
				INNER JOIN mastempleado e1 ON (e1.CodPersona = p1.CodPersona)
				LEFT JOIN mastpersonas p2 ON (p2.CodPersona = pc.CreadoPor)
				LEFT JOIN mastpersonas p3 ON (p3.CodPersona = pc.ConformadoPor)
				LEFT JOIN mastpersonas p4 ON (p4.CodPersona = pc.AprobadoPor)
			WHERE
				pc.Tipo = '".$Tipo."' AND
				pc.CodProceso = '".$CodProceso."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query)) $field = mysql_fetch_array($query);
	
	if ($opcion == "modificar") {
		$_titulo = "Modificar Cese/Reingreso";
		$accion = "modificar";
		if ($field['Tipo'] == "C") {
			$disabled_datos = "disabled";
			$visible_datos = "visibility:hidden;";
		}
		elseif ($field['Tipo'] == "R") $disabled_cese = "disabled";
		$disabled_conformado = "disabled";
		$disabled_aprobado = "disabled";
		$display_modificar = "display:none;";
		$label_submit = "Modificar";
		$clkCancelar = "document.getElementById('frmentrada').submit();";
	}
	
	elseif ($opcion == "conformar") {
		$field['ConformadoPor'] = $_SESSION['CODPERSONA_ACTUAL'];
		$field['NomConformadoPor'] = $_SESSION['NOMBRE_USUARIO_ACTUAL'];
		$field['FechaConformado'] = $Ahora;
		##
		$_titulo = "Conformar Cese/Reingreso";
		$accion = "conformar";
		$disabled_datos = "disabled";
		$disabled_planilla = "disabled";
		$disabled_cese = "disabled";
		$disabled_creado = "disabled";
		$disabled_aprobado = "disabled";
		$display_modificar = "display:none;";
		$visible_datos = "visibility:hidden;";
		$label_submit = "Conformar";
		$clkCancelar = "document.getElementById('frmentrada').submit();";
	}
	
	elseif ($opcion == "aprobar") {
		$field['AprobadoPor'] = $_SESSION['CODPERSONA_ACTUAL'];
		$field['NomAprobadoPor'] = $_SESSION['NOMBRE_USUARIO_ACTUAL'];
		$field['FechaAprobado'] = $Ahora;
		##
		$_titulo = "Aprobar Cese/Reingreso";
		$accion = "aprobar";
		$disabled_datos = "disabled";
		$disabled_planilla = "disabled";
		$disabled_cese = "disabled";
		$disabled_creado = "disabled";
		$disabled_conformado = "disabled";
		$display_modificar = "display:none;";
		$visible_datos = "visibility:hidden;";
		$label_submit = "Aprobar";
		$clkCancelar = "document.getElementById('frmentrada').submit();";
	}
	
	elseif ($opcion == "anular") {
		$_titulo = "Anular Cese/Reingreso";
		$accion = "anular";
		$disabled_datos = "disabled";
		$disabled_planilla = "disabled";
		$disabled_cese = "disabled";
		$disabled_creado = "disabled";
		$disabled_conformado = "disabled";
		$disabled_aprobado = "disabled";
		$display_modificar = "display:none;";
		$visible_datos = "visibility:hidden;";
		$label_submit = "Anular";
		$clkCancelar = "document.getElementById('frmentrada').submit();";
	}
	
	elseif ($opcion == "ver") {
		$_titulo = "Ver Cese/Reingreso";
		$disabled_datos = "disabled";
		$disabled_planilla = "disabled";
		$disabled_cese = "disabled";
		$disabled_creado = "disabled";
		$disabled_conformado = "disabled";
		$disabled_aprobado = "disabled";
		$display_modificar = "display:none;";
		$display_submit = "display:none;";
		$visible_datos = "visibility:hidden;";
		$clkCancelar = "document.getElementById('frmentrada').submit();";
	}
}
//	------------------------------------
$_width = 800;
$_sufijo = "reingreso";
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=<?=$action?>" method="POST" enctype="multipart/form-data" onsubmit="return <?=$_sufijo?>(this, '<?=$accion?>');" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
<input type="hidden" name="fCodDependencia" id="fCodDependencia" value="<?=$fCodDependencia?>" />
<input type="hidden" name="fFechaD" id="fFechaD" value="<?=$fFechaD?>" />
<input type="hidden" name="fFechaH" id="fFechaH" value="<?=$fFechaH?>" />
<input type="hidden" name="fCodEmpleado" id="fCodEmpleado" value="<?=$fCodEmpleado?>" />
<input type="hidden" name="fCodPersona" id="fCodPersona" value="<?=$fCodPersona?>" />
<input type="hidden" name="fNomEmpleado" id="fNomEmpleado" value="<?=$fNomEmpleado?>" />
<input type="hidden" name="fCodTipoNom" id="fCodTipoNom" value="<?=$fCodTipoNom?>" />
<input type="hidden" id="Fecha" value="<?=formatFechaDMA($field['Fecha'])?>" />

<table width="<?=$_width?>" align="center" cellpadding="0" cellspacing="0">
    <tr>
        <td>
            <div class="header">
            <ul id="tab">
            <!-- CSS Tabs -->
            <li id="li1" onclick="currentTab('tab', this);" class="current"><a href="#" onclick="mostrarTab('tab', 1, 2);">Informaci&oacute;n General</a></li>
            <li id="li2" onclick="currentTab('tab', this);"><a href="#" onclick="mostrarTab('tab', 2, 2);">Detalle</a></li>
            </ul>
            </div>
        </td>
    </tr>
</table>

<div id="tab1" style="display:block;">
<table width="<?=$_width?>" class="tblForm">
	<tr>
    	<td colspan="4" class="divFormCaption">Datos del Empleado</td>
    </tr>
    <tr>
		<td class="tagForm" width="125"><strong>Nro. Proceso:</strong></td>
		<td>
        	<input type="text" id="CodProceso" value="<?=$field['CodProceso']?>" style="width:50px;" class="codigo" disabled />
        </td>
		<td class="tagForm" width="125">Tipo:</td>
		<td>
            <select id="Tipo" style="width:105px;" disabled="disabled">
            	<option value="">&nbsp;</option>
                <?=loadSelectValores("TIPO-REINGRESO", $field['Tipo'], 0)?>
            </select>
		</td>
	</tr>
    <tr>
		<td class="tagForm">Empleado:</td>
		<td class="gallery clearfix">
            <input type="hidden" id="CodPersona" value="<?=$field['CodPersona']?>" />
            <input type="hidden" id="CodEmpleado" value="<?=$field['CodEmpleado']?>" />
            <input type="text" id="NomEmpleado" value="<?=htmlentities($field['NomEmpleado'])?>" style="width:270px;" disabled />
            <a href="../lib/listas/listado_empleados.php?filtrar=default&cod=CodEmpleado&nom=NomEmpleado&campo3=CodPersona&ventana=reingreso_empleado_sel&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" id="btEmpleado" style=" <?=$display_modificar?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
		<td class="tagForm">Nro. Documento:</td>
		<td>
        	<input type="text" id="Ndocumento" value="<?=$field['Ndocumento']?>" style="width:100px;" disabled />
		</td>
	</tr>
    <tr>
		<td class="tagForm">Organismo:</td>
		<td>
            <select id="CodOrganismo" style="width:275px;" class="datos" <?=$disabled_datos?>>
            	<option value="">&nbsp;</option>
                <?=loadSelect("mastorganismos", "CodOrganismo", "Organismo", $field['CodOrganismo'], 0)?>
            </select>
		</td>
		<td class="tagForm">Sexo:</td>
		<td>
            <select id="Sexo" style="width:105px;" disabled>
            	<option value="">&nbsp;</option>
                <?=loadSelectGeneral("SEXO", $field['Sexo'], 0)?>
            </select>
		</td>
	</tr>
    <tr>
		<td class="tagForm">Dependencia:</td>
		<td>
            <select id="CodDependencia" style="width:275px;" class="datos" <?=$disabled_datos?>>
            	<option value="">&nbsp;</option>
                <?=loadSelect("mastdependencias", "CodDependencia", "Dependencia", $field['CodDependencia'], 0)?>
            </select>
		</td>
		<td class="tagForm">Fecha de Nacimiento:</td>
		<td>
        	<input type="text" id="Fnacimiento" value="<?=formatFechaDMA($field['Fnacimiento'])?>" style="width:60px;" disabled />
		</td>
	</tr>
    <tr>
		<td class="tagForm">Cargo:</td>
        <td class="gallery clearfix">
            <input type="hidden" id="CodCargo" value="<?=$field['CodCargo']?>" />
            <input type="text" id="DescripCargo" value="<?=htmlentities($field['DescripCargo'])?>" style="width:270px;" disabled />
            <a href="../lib/listas/listado_cargos.php?filtrar=default&cod=CodCargo&nom=DescripCargo&campo3=SueldoActual&ventana=reingreso_cargo_sel&iframe=true&width=950&height=525" rel="prettyPhoto[iframe2]" id="btCargo" style=" <?=$visible_datos?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
		<td class="tagForm">Edad:</td>
		<td>
        	<input type="text" id="Edad" value="<?=$field['Edad']?>" style="width:20px;" disabled />
		</td>
	</tr>
    <tr>
		<td class="tagForm">Sueldo:</td>
		<td>
        	<input type="text" id="SueldoActual" value="<?=number_format($field['SueldoActual'], 2, ',', '.')?>" style="width:100px; text-align:right;" disabled />
		</td>
		<td class="tagForm">Fecha de Ingreso:</td>
		<td>
        	<input type="text" id="FechaIngreso" value="<?=formatFechaDMA($field['FechaIngreso'])?>" style="width:60px;" class="datepicker datos" <?=$disabled_datos?> />
		</td>
	</tr>
    <tr>
		<td class="tagForm">&Uacute;ltima Modif.:</td>
		<td>
			<input type="text" size="30" value="<?=$field['UltimoUsuario']?>" disabled="disabled" />
			<input type="text" size="25" value="<?=formatFechaDMA($field['UltimaFecha'])?>" disabled="disabled" />
		</td>
		<td class="tagForm">A&ntilde;os de Servicio:</td>
		<td>
        	<input type="text" id="AnioServicio" value="<?=$field['AnioServicio']?>" style="width:20px;" disabled />
		</td>
	</tr>
</table>
</div>

<div id="tab2" style="display:none;">
<table width="<?=$_width?>" class="tblForm">
	<tr>
    	<td colspan="4" class="divFormCaption">Datos del Cese/Reingreso</td>
    </tr>
    <tr>
		<td class="tagForm"><strong>Estado:</strong></td>
		<td>
        	<input type="hidden" id="Estado" value="<?=$field['Estado']?>" />
        	<input type="text" value="<?=strtoupper(printValores("ESTADO-REINGRESO", $field['Estado']))?>" style="width:100px;" class="codigo" disabled />
        </td>
		<td class="tagForm"><strong>Periodo:</strong></td>
		<td>
        	<input type="text" id="Periodo" value="<?=$field['Periodo']?>" style="width:45px;" class="codigo" disabled />
        </td>
	</tr>
    <tr>
		<td class="tagForm" width="125">Procesado Por:</td>
		<td>
        	<input type="hidden" id="CreadoPor" value="<?=$field['CreadoPor']?>" />
        	<input type="text" id="NomCreadoPor" value="<?=htmlentities($field['NomCreadoPor'])?>" style="width:270px;" disabled />
		</td>
		<td class="tagForm" width="125">Fecha:</td>
		<td>
        	<input type="text" id="FechaCreado" value="<?=formatFechaDMA($field['FechaCreado'])?>" style="width:100px;" disabled />
		</td>
	</tr>
    <tr>
		<td class="tagForm">Observaciones:</td>
		<td colspan="3">
        	<textarea id="ObsCreado" style="width:99%; height:30px;" <?=$disabled_creado?>><?=htmlentities($field['ObsCreado'])?></textarea>
        </td>
	</tr>
    <tr>
		<td class="tagForm" width="125">Conformado Por:</td>
		<td>
        	<input type="hidden" id="ConformadoPor" value="<?=$field['ConformadoPor']?>" />
        	<input type="text" id="NomConformadoPor" value="<?=htmlentities($field['NomConformadoPor'])?>" style="width:270px;" disabled />
		</td>
		<td class="tagForm" width="125">Fecha:</td>
		<td>
        	<input type="text" id="FechaConformado" value="<?=formatFechaDMA($field['FechaConformado'])?>" style="width:100px;" disabled />
		</td>
	</tr>
    <tr>
		<td class="tagForm">Observaciones:</td>
		<td colspan="3">
        	<textarea id="ObsConformado" style="width:99%; height:30px;" <?=$disabled_conformado?>><?=htmlentities($field['ObsConformado'])?></textarea>
        </td>
	</tr>
    <tr>
		<td class="tagForm" width="125">Aprobado Por:</td>
		<td>
        	<input type="hidden" id="AprobadoPor" value="<?=$field['AprobadoPor']?>" />
        	<input type="text" id="NomAprobadoPor" value="<?=htmlentities($field['NomAprobadoPor'])?>" style="width:270px;" disabled />
		</td>
		<td class="tagForm" width="125">Fecha:</td>
		<td>
        	<input type="text" id="FechaAprobado" value="<?=formatFechaDMA($field['FechaAprobado'])?>" style="width:100px;" disabled />
		</td>
	</tr>
    <tr>
		<td class="tagForm">Observaciones:</td>
		<td colspan="3">
        	<textarea id="ObsAprobado" style="width:99%; height:30px;" <?=$disabled_aprobado?>><?=htmlentities($field['ObsAprobado'])?></textarea>
        </td>
	</tr>
	<tr>
    	<td colspan="4" class="divFormCaption">Planilla</td>
    </tr>
	<tr>
		<td class="tagForm">Tipo de N&oacute;mina:</td>
		<td>
            <select id="CodTipoNom" style="width:200px;" class="planilla" <?=$disabled_planilla?>>
            	<option value="">&nbsp;</option>
                <?=loadSelect("tiponomina", "CodTipoNom", "Nomina", $field['CodTipoNom'], 0);?>
            </select>
		</td>
		<td class="tagForm">Tipo de Trabajador:</td>
		<td>
            <select id="CodTipoTrabajador" style="width:200px;" class="planilla" <?=$disabled_planilla?>>
            	<option value="">&nbsp;</option>
                <?=loadSelect("rh_tipotrabajador", "CodTipoTrabajador", "TipoTrabajador", $field['CodTipoTrabajador'], 0);?>
            </select>
		</td>
	</tr>
    <tr>
    	<td>&nbsp;</td>
    	<td>&nbsp;</td>
		<td class="tagForm">Nro. Resoluci&oacute;n:</td>
		<td>
            <input type="text" id="NroResolucion" style="width:95%;" class="planilla" value="<?=$field['NroResolucion']?>" <?=$disabled_planilla?> />
		</td>
	</tr>
	<tr>
    	<td colspan="4" class="divFormCaption">Cese</td>
    </tr>
    <tr>
		<td class="tagForm" width="125">Estado:</td>
		<td>
            <input type="radio" name="SitTra" id="SitTraA" value="A" <?=chkOpt($field['SitTra'], "A");?> disabled /> Activo
			&nbsp; &nbsp; 
            <input type="radio" name="SitTra" id="SitTraI" value="I" <?=chkOpt($field['SitTra'], "I");?> disabled /> Inactivo
		</td>
		<td class="tagForm" width="125">Motivo del Cese:</td>
		<td>
            <select id="CodMotivoCes" style="width:200px;" class="cese" <?=$disabled_cese?>>
            	<option value="">&nbsp;</option>
                <?=loadSelect("rh_motivocese", "CodMotivoCes", "MotivoCese", $field['CodMotivoCes'], 0);?>
            </select>
		</td>
	</tr>
    <tr>
		<td class="tagForm">Fecha de Cese:</td>
		<td>
            <input type="text" id="FechaEgreso" style="width:60px;" class="datepicker cese" maxlength="10" value="<?=formatFechaDMA($field['FechaEgreso'])?>" <?=$disabled_cese?> />
		</td>
		<td class="tagForm">Explicaci&oacute;n:</td>
		<td>
            <input type="text" id="ObsCese" style="width:95%;" class="cese" value="<?=$field['ObsCese']?>" <?=$disabled_cese?> />
		</td>
	</tr>
</table>
</div>

<center>
<input type="submit" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" />
<input type="button" value="Cancelar" style="width:75px;" onclick="<?=$clkCancelar?>" />
</center>

</form>

<div style="width:<?=$_width?>px" class="divMsj">Campos Obligatorios *</div>