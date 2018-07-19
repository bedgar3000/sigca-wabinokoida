<?php
if ($opcion == "nuevo") {
	$accion = "nuevo";
	$titulo = "Nuevo Requerimiento";
	$label_submit = "Guardar";

	$sql = "SELECT MAX(Ejercicio) FROM pv_reformulacionmetas";
	$Ejercicio = getVar3($sql);
	$Ejercicio = ($Ejercicio?$Ejercicio:$AnioActual);

	$field_requerimiento['Estado'] = "PR";
	$field_requerimiento['CodOrganismo'] = $_SESSION["ORGANISMO_ACTUAL"];
	$field_requerimiento['CodDependencia'] = $_SESSION["DEPENDENCIA_ACTUAL"];
	$field_requerimiento['CodCentroCosto'] = $_SESSION["CCOSTO_ACTUAL"];
	$field_requerimiento['PreparadaPor'] = $_SESSION["CODPERSONA_ACTUAL"];
	$field_requerimiento['NomPreparadaPor'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
	$field_requerimiento['FechaRequerida'] = formatFechaAMD(getFechaFin(formatFechaDMA(substr($Ahora, 0, 10)), $_PARAMETRO['DIASDEFREQ']));
	$field_requerimiento['FechaPreparacion'] = substr($Ahora, 0, 10);
	$field_requerimiento['TipoRequerimiento'] = "01";
	$field_requerimiento['Clasificacion'] = "STO";
	$field_requerimiento['CodAlmacen'] = setAlmacenFromClasificacion($field_requerimiento['Clasificacion']);
	$field_requerimiento['FlagCommodity'] = getValorCampo("lg_almacenmast", "CodAlmacen", "FlagCommodity", $field_requerimiento['CodAlmacen']);
	$field_requerimiento['TipoClasificacion'] = "A";
	$field_requerimiento['CodFuente'] = $_PARAMETRO['FFMETASDEF'];
	$disabled_anular = "disabled";
	$display_proveedor = "visibility:hidden;";
	$display_rechazar = "visibility:hidden;";
	$disabled_proveedor = "disabled";
	$disabled_cajachica = "disabled";
	$disabled_item = "";
	$disabled_commodity = "disabled";
	$mostrarTabDistribucion = "mostrarTabDistribucionRequerimiento(true);";
	$optClasificacion = 0;
	##	presupuesto
	$sql = "SELECT p.*
			FROM pv_presupuesto p
			INNER JOIN pv_categoriaprog cp ON (cp.CategoriaProg = p.CategoriaProg)
			INNER JOIN pv_unidadejecutora ue On (ue.CodUnidadEjec = cp.CodUnidadEjec)
			WHERE p.CodOrganismo = '".$field_requerimiento['CodOrganismo']."' AND p.Ejercicio = '".$Ejercicio."' AND ue.CodCentroCosto = '".$field_requerimiento['CodCentroCosto']."'";
	$field_presupuesto = getRecord($sql);
	$field_requerimiento['CodPresupuesto'] = $field_presupuesto['CodPresupuesto'];
	$field_requerimiento['Ejercicio'] = $field_presupuesto['Ejercicio'];
	$field_requerimiento['CategoriaProg'] = $field_presupuesto['CategoriaProg'];
}
elseif ($opcion == "modificar" || $opcion == "ver" || $opcion == "revisar" || $opcion == "conformar" || $opcion == "aprobar" || $opcion == "anular" || $opcion == "cerrar" || $opcion == "modificacion_restringida") {
	if ($origen == "lg_cotizaciones_items_invitar_lista") list($CodRequerimiento, $Secuencia) = split("[_]", $sel_registros);
	else list($CodRequerimiento, $Secuencia) = split("[.]", $registro);
	//	consulto datos generales
	$sql = "SELECT
				r.*,
				c.TipoRequerimiento,
				c.FlagCajaChica AS VerCajaChica,
				cc.Abreviatura AS NomCentroCosto,
				p1.NomCompleto AS NomPreparadaPor,
				p2.NomCompleto AS NomRevisadaPor,
				p3.NomCompleto AS NomConformadaPor,
				p4.NomCompleto AS NomAprobadaPor,
				p5.NomCompleto AS NomProveedor,
				pv.CategoriaProg
			FROM
				lg_requerimientos r
				INNER JOIN lg_clasificacion c ON (r.Clasificacion = c.clasificacion)
				INNER JOIN ac_mastcentrocosto cc ON (r.CodCentroCosto = cc.CodCentroCosto)
				LEFT JOIN pv_presupuesto pv On (pv.codOrganismo = r.CodOrganismo AND pv.CodPresupuesto = r.CodPresupuesto)
				LEFT JOIN mastpersonas p1 ON (r.PreparadaPor = p1.CodPersona)
				LEFT JOIN mastpersonas p2 ON (r.RevisadaPor = p2.CodPersona)
				LEFT JOIN mastpersonas p3 ON (r.ConformadaPor = p3.CodPersona)
				LEFT JOIN mastpersonas p4 ON (r.AprobadaPor = p4.CodPersona)
				LEFT JOIN mastpersonas p5 ON (r.ProveedorSugerido = p5.CodPersona)
			WHERE CodRequerimiento = '".$CodRequerimiento."'";
	$query_requerimiento = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query_requerimiento)) $field_requerimiento = mysql_fetch_array($query_requerimiento);
	##
	if ($opcion == "modificar") {
		$titulo = "Modificar Requerimiento";
		$accion = "modificar";
		$disabled_modificar = "disabled";
		$display_modificar = "display:none;";
		$label_submit = "Modificar";
		$display_rechazar = "display:none;";
		$mostrarTabDistribucion = "mostrarTabDistribucionRequerimiento(true);";
		$display_rechazar = "visibility:hidden;";
		$disabled_firma = "disabled";
		$display_firma = "display:none;";
	}
	elseif ($opcion == "ver") {
		$titulo = "Ver Requerimiento";
		$disabled_ver = "disabled";
		$display_ver = "display:none;";
		$display_submit = "display:none;";
		$disabled_modificar = "disabled";
		$display_modificar = "display:none;";
		$display_proveedor = "visibility:hidden;";
		$disabled_anular = "disabled";
		$display_rechazar = "display:none;";
		$mostrarTabDistribucion = "mostrarTab('tab', 5, 5);";
		$display_rechazar = "visibility:hidden;";
		$disabled_firma = "disabled";
		$display_firma = "display:none;";
	}
	elseif ($opcion == "revisar") {
		$field_requerimiento['RevisadaPor'] = $_SESSION["CODPERSONA_ACTUAL"];
		$field_requerimiento['NomRevisadaPor'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
		$field_requerimiento['FechaRevision'] = substr($Ahora, 0, 10);
		##
		$titulo = "Revisar Requerimiento";
		$accion = "revisar";
		$label_submit = "Revisar";
		$disabled_ver = "disabled";
		$display_ver = "display:none;";
		$disabled_modificar = "disabled";
		$display_modificar = "display:none;";
		$display_proveedor = "visibility:hidden;";
		$disabled_anular = "disabled";
		$display_rechazar = "display:none;";
		$mostrarTabDistribucion = "mostrarTab('tab', 5, 5);";
		$display_rechazar = "visibility:hidden;";
		$disabled_firma = "disabled";
		$display_firma = "display:none;";
	}
	elseif ($opcion == "conformar") {
		$field_requerimiento['ConformadaPor'] = $_SESSION["CODPERSONA_ACTUAL"];
		$field_requerimiento['NomConformadaPor'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
		$field_requerimiento['FechaConformacion'] = substr($Ahora, 0, 10);
		##
		$titulo = "Comformar Requerimiento";
		$accion = "conformar";
		$label_submit = "Conformar";
		$disabled_ver = "disabled";
		$display_ver = "display:none;";
		$disabled_modificar = "disabled";
		$display_modificar = "display:none;";
		$display_proveedor = "visibility:hidden;";
		$disabled_anular = "disabled";
		$display_rechazar = "display:none;";
		$mostrarTabDistribucion = "mostrarTab('tab', 5, 5);";
		$display_rechazar = "visibility:hidden;";
		$disabled_firma = "disabled";
		$display_firma = "display:none;";
	}
	elseif ($opcion == "aprobar") {
		$field_requerimiento['AprobadaPor'] = $_SESSION["CODPERSONA_ACTUAL"];
		$field_requerimiento['NomAprobadaPor'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
		$field_requerimiento['FechaAprobacion'] = substr($Ahora, 0, 10);
		##
		$titulo = "Aprobar Requerimiento";
		$accion = "aprobar";
		$label_submit = "Aprobar";
		$disabled_ver = "disabled";
		$display_ver = "display:none;";
		$disabled_modificar = "disabled";
		$display_modificar = "display:none;";
		$display_proveedor = "visibility:hidden;";
		$mostrarTabDistribucion = "mostrarTab('tab', 5, 5);";
		$disabled_firma = "disabled";
		$display_firma = "display:none;";
	}
	elseif ($opcion == "anular") {
		$titulo = "Anular Requerimiento";
		$accion = "anular";
		$label_submit = "Anular";
		$disabled_ver = "disabled";
		$display_ver = "display:none;";
		$disabled_modificar = "disabled";
		$display_modificar = "display:none;";
		$display_proveedor = "visibility:hidden;";
		$display_rechazar = "display:none;";
		$mostrarTabDistribucion = "mostrarTab('tab', 5, 5);";
		$display_rechazar = "visibility:hidden;";
		$disabled_firma = "disabled";
		$display_firma = "display:none;";
	}
	elseif ($opcion == "cerrar") {
		$titulo = "Anular Requerimiento";
		$accion = "cerrar";
		$label_submit = "Cerrar";
		$disabled_ver = "disabled";
		$display_ver = "display:none;";
		$disabled_modificar = "disabled";
		$display_modificar = "display:none;";
		$display_proveedor = "visibility:hidden;";
		$display_rechazar = "display:none;";
		$mostrarTabDistribucion = "mostrarTab('tab', 5, 5);";
		$display_rechazar = "visibility:hidden;";
		$disabled_firma = "disabled";
		$display_firma = "display:none;";
	}
	elseif ($opcion == "modificacion_restringida") {
		$titulo = "Modificaci&oacute;n Restringida de Requerimiento";
		$accion = "modificacion_restringida";
		$disabled_modificar = "disabled";
		$disabled_restringido = "disabled";
		$display_modificar = "display:none;";
		$label_submit = "Modificar";
		$display_rechazar = "display:none;";
		$mostrarTabDistribucion = "mostrarTab('tab', 5, 5);";
		$display_rechazar = "visibility:hidden;";
		$disabled_firma = "";
		$display_firma = "";
	}
	$optClasificacion = 0;
	$disabled_item = $disabled_ver;
	$disabled_commodity = $disabled_ver;
	$disabled_proveedor = $disabled_ver;
	if (($field_requerimiento['VerCajaChica'] == "S" && $opcion == "conformar") || $opcion == "modificacion_restringida") $disabled_cajachica = ""; else $disabled_cajachica = "disabled";
	if ($field_requerimiento['TipoRequerimiento'] == "01") $disabled_commodity = "disabled";
	else $disabled_item = "disabled";
	//	consulto cotizaciones
	$sql = "SELECT * FROM lg_cotizacion WHERE CodRequerimiento = '$CodRequerimiento'";
	if (count(getRecord($sql))) $disabled_cajachica = 'disabled';
}
//	------------------------------------
if (!empty($origen)) $action = "gehen.php?anz=$origen"; else $action = "../framemain.php";
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="document.getElementById('frmentrada').submit()">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<table width="1100" align="center" cellpadding="0" cellspacing="0">
    <tr>
        <td>
            <div class="header">
            <ul id="tab">
            <!-- CSS Tabs -->
            <li id="li1" onclick="currentTab('tab', this);" class="current"><a href="#" onclick="mostrarTab('tab', 1, 5);">Informaci&oacute;n General</a></li>
            <li id="li2" onclick="currentTab('tab', this);"><a href="#" onclick="mostrarTab('tab', 2, 5);">Items/Commodities</a></li>
            <li id="li3" onclick="currentTab('tab', this);"><a href="#" onclick="mostrarTab('tab', 3, 5);">Cotizaciones</a></li>
            <li id="li4" onclick="currentTab('tab', this);"><a href="#" onclick="mostrarTab('tab', 4, 5);">Avances</a></li>
            <li id="li5" onclick="currentTab('tab', this);">
            	<a href="#" onclick="<?=$mostrarTabDistribucion?>">Distribuci&oacute;n Presupuestaria/Contables</a>
            </li>
            </ul>
            </div>
        </td>
    </tr>
</table>

<form name="frmentrada" id="frmentrada" action="<?Php echo $action; ?>" method="POST" onsubmit="return requerimiento(this, '<?=$accion?>');">
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
<input type="hidden" name="fTipoClasificacion" id="fTipoClasificacion" value="<?=$fTipoClasificacion?>" />
<input type="hidden" name="fCodDependencia" id="fCodDependencia" value="<?=$fCodDependencia?>" />
<input type="hidden" name="fClasificacion" id="fClasificacion" value="<?=$fClasificacion?>" />
<input type="hidden" name="fCodCentroCosto" id="fCodCentroCosto" value="<?=$fCodCentroCosto?>" />
<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
<input type="hidden" name="fFechaPreparaciond" id="fFechaPreparaciond" value="<?=$fFechaPreparaciond?>" />
<input type="hidden" name="fFechaPreparacionh" id="fFechaPreparacionh" value="<?=$fFechaPreparacionh?>" />
<input type="hidden" name="fOrderByItems" id="fOrderByItems" value="<?=$fOrderByItems?>" />
<input type="hidden" name="fOrderByCommodity" id="fOrderByCommodity" value="<?=$fOrderByCommodity?>" />
<input type="hidden" name="id_tab" id="id_tab" value="<?=$id_tab?>" />
<input type="hidden" id="CodRequerimiento" value="<?=$field_requerimiento['CodRequerimiento']?>" />
<input type="hidden" id="TipoRequerimiento" value="<?=$field_requerimiento['TipoRequerimiento']?>" />
<input type="hidden" id="FlagCommodity" value="<?=$field_requerimiento['FlagCommodity']?>" />
<input type="hidden" name="Anio" id="Anio" value="<?=substr($Ahora, 0, 4)?>" />
<input type="hidden" id="TipoClasificacion" value="<?=$field_requerimiento['TipoClasificacion']?>" />

<div id="tab1" style="display:block;">
	<table width="1100" class="tblForm">
		<tr>
	    	<td colspan="4" class="divFormCaption">Informaci&oacute;n General</td>
	    </tr>
		<tr>
			<td class="tagForm" width="150">* Organismo:</td>
			<td>
				<select name="CodOrganismo" id="CodOrganismo" style="width:300px;" onchange="getOptionsSelect(this.value, 'dependencia', 'CodDependencia', true, 'CodCentroCosto'); $('#CodPresupuesto').val(''); $('#CategoriaProg').val('');" <?=$disabled_ver?>>
	            	<?php
					if ($opcion == 'nuevo') echo getOrganismos($field_requerimiento['CodOrganismo'], 3);
					else loadSelect2('mastorganismos','CodOrganismo','Organismo',$field_requerimiento['CodOrganismo'],1);
					?>
				</select>
			</td>
			<td class="tagForm">N&uacute;mero:</td>
			<td>
	        	<input type="text" id="CodInterno" style="width:100px;" class="codigo" value="<?=$field_requerimiento['CodInterno']?>" disabled="disabled" />
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Dependencia:</td>
			<td>
				<select id="CodDependencia" style="width:300px;" onchange="getOptionsSelect(this.value, 'centro_costo', 'CodCentroCosto', true); setHrefPresupuesto();" <?=$disabled_modificar?>>
					<?=getDependencias($field_requerimiento['CodDependencia'], $field_requerimiento['CodOrganismo'], 3);?>
				</select>
			</td>
			<td class="tagForm">Estado:</td>
			<td>
				<input type="hidden" id="Estado" value="<?=$field_requerimiento['Estado']?>" />
				<input type="text" style="width:100px;" class="codigo" value="<?=printValores("ESTADO-REQUERIMIENTO", $field_requerimiento['Estado'])?>" disabled="disabled" />
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Centro de Costo:</td>
			<td>
				<select id="CodCentroCosto" style="width:300px;" <?=$disabled_ver?> <?=$disabled_restringido?>>
					<?=loadSelectDependiente("ac_mastcentrocosto", "CodCentroCosto", "Descripcion", "CodDependencia", $field_requerimiento['CodCentroCosto'], $field_requerimiento['CodDependencia'], 0)?>
				</select>
			</td>
			<td class="tagForm">Dirigido A:</td>
			<td>
				<input type="radio" name="TipoClasificacion" id="FlagCompras" value="C" disabled="disabled" <?=chkOpt($field_requerimiento['TipoClasificacion'], "C")?> /> Compras
				<input type="radio" name="TipoClasificacion" id="FlagAlmacen" value="A" disabled="disabled" <?=chkOpt($field_requerimiento['TipoClasificacion'], "A")?> /> Almac&eacute;n
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Clasificaci&oacute;n:</td>
			<td>
				<select id="Clasificacion" style="width:200px;" onchange="setDirigidoA(this.value);" <?=$disabled_ver?>>
					<?=loadSelectClasificacion($field_requerimiento['Clasificacion'], $optClasificacion)?>
				</select>
			</td>
			<td class="tagForm">Preparada por:</td>
			<td class="gallery clearfix">
				<input type="hidden" id="PreparadaPor" value="<?=$field_requerimiento['PreparadaPor']?>" />
				<input type="text" id="NomPreparadaPor" value="<?=$field_requerimiento['NomPreparadaPor']?>" style="width:195px;" disabled="disabled" />
				<input type="text" id="FechaPreparacion" value="<?=formatFechaDMA($field_requerimiento['FechaPreparacion'])?>" class="datepicker" style="width:60px;" <?=$disabled_firma?> />
				<a href="../lib/listas/listado_empleados.php?filtrar=default&cod=PreparadaPor&nom=NomPreparadaPor&iframe=true&width=950&height=425" rel="prettyPhoto[iframe1]" style=" <?=$display_firma?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Almac&eacute;n:</td>
			<td>
				<select id="CodAlmacen" style="width:200px;" onchange="setFlagCommodity(this.value);" <?=$disabled_ver?> <?=$disabled_restringido?>>
	            	<?=loadSelectAlmacen($field_requerimiento['CodAlmacen'], $field_requerimiento['FlagCommodity'], 0)?>
					<?php //loadSelect("lg_almacenmast", "CodAlmacen", "Descripcion", $field_requerimiento['CodAlmacen'], 0)?>
				</select>
			</td>
			<td class="tagForm">Revisada por:</td>
			<td class="gallery clearfix">
				<input type="hidden" id="RevisadaPor" value="<?=$field_requerimiento['RevisadaPor']?>" />
				<input type="text" id="NomRevisadaPor" value="<?=$field_requerimiento['NomRevisadaPor']?>" style="width:195px;" disabled="disabled" />
				<input type="text" id="FechaRevision" value="<?=formatFechaDMA($field_requerimiento['FechaRevision'])?>" class="datepicker" style="width:60px;" <?=$disabled_firma?> />
				<a href="../lib/listas/listado_empleados.php?filtrar=default&cod=RevisadaPor&nom=NomRevisadaPor&iframe=true&width=950&height=425" rel="prettyPhoto[iframe2]" style=" <?=$display_firma?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Prioridad:</td>
			<td>
				<select id="Prioridad" style="width:200px;" <?=$disabled_ver?>>
					<?=loadSelectGeneral("PRIORIDAD", $field_requerimiento['Prioridad'], 0)?>
				</select>
			</td>
			<td class="tagForm">Conformada por:</td>
			<td class="gallery clearfix">
				<input type="hidden" id="ConformadaPor" value="<?=$field_requerimiento['ConformadaPor']?>" />
				<input type="text" id="NomConformadaPor" value="<?=$field_requerimiento['NomConformadaPor']?>" style="width:195px;" disabled="disabled" />
				<input type="text" id="FechaConformacion" value="<?=formatFechaDMA($field_requerimiento['FechaConformacion'])?>" class="datepicker" style="width:60px;" <?=$disabled_firma?> />
				<a href="../lib/listas/listado_empleados.php?filtrar=default&cod=ConformadaPor&nom=NomConformadaPor&iframe=true&width=950&height=425" rel="prettyPhoto[iframe3]" style=" <?=$display_firma?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
		</tr>
	    <tr>
		 	<td class="tagForm">* Fecha Requerida:</td>
			<td><input type="text" id="FechaRequerida" value="<?=formatFechaDMA($field_requerimiento['FechaRequerida'])?>" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" <?=$disabled_ver?> /></td>
			<td class="tagForm">Aprobada por:</td>
			<td class="gallery clearfix">
				<input type="hidden" id="AprobadaPor" value="<?=$field_requerimiento['AprobadaPor']?>" />
				<input type="text" id="NomAprobadaPor" value="<?=$field_requerimiento['NomAprobadaPor']?>" style="width:195px;" disabled="disabled" />
				<input type="text" id="FechaAprobacion" value="<?=formatFechaDMA($field_requerimiento['FechaAprobacion'])?>" class="datepicker" style="width:60px;" <?=$disabled_firma?> />
				<a href="../lib/listas/listado_empleados.php?filtrar=default&cod=AprobadaPor&nom=NomAprobadaPor&iframe=true&width=950&height=425" rel="prettyPhoto[iframe4]" style=" <?=$display_firma?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
		</tr>
	    <tr>
	    	<td>&nbsp;</td>
	    	<td colspan="3">
	        	<input type="checkbox" name="FlagCajaChica" id="FlagCajaChica" value="S" <?=chkFlag($field_requerimiento['FlagCajaChica'])?> <?=$disabled_cajachica?> onclick="setDirigidoACC(this.checked);" /> Requerimiento para Caja Chica
	        </td>
	    </tr>
		<tr>
			<td class="tagForm">Comentarios:</td>
			<td colspan="3"><textarea id="Comentarios" style="width:95%; height:50px;" <?=$disabled_ver?>><?=($field_requerimiento['Comentarios'])?></textarea></td>
		</tr>
		<tr>
			<td class="tagForm">Razon Rechazo:</td>
			<td colspan="3"><textarea id="RazonRechazo" style="width:95%; height:30px;" <?=$disabled_anular?>><?=($field_requerimiento['RazonRechazo'])?></textarea></td>
		</tr>
		<tr>
			<td class="tagForm">&Uacute;ltima Modif.:</td>
			<td>
				<input type="text" size="30" value="<?=$field_requerimiento['UltimoUsuario']?>" disabled="disabled" />
				<input type="text" size="25" value="<?=$field_requerimiento['UltimaFecha']?>" disabled="disabled" />
			</td>
		</tr>  
	</table>
	<table width="1100" class="tblForm">
		<tr>
			<td colspan="2" class="divFormCaption">Informaci&oacute;n para Compras</td>
			<td colspan="2" class="divFormCaption">Presupuesto</td>
		</tr>
		<tr>
			<td class="tagForm" width="150">Clasificaci&oacute;n:</td>
			<td>
				<select id="ClasificacionOC" style="width:150px;" <?=$disabled_proveedor?>>
	            	<option value="">&nbsp;</option>
					<?=loadSelectValores("COMPRA-CLASIFICACION", $field_requerimiento['ClasificacionOC'], 0)?>
				</select>
			</td>
			<td class="tagForm" width="150">Presupuesto:</td>
			<td class="gallery clearfix">
				<input type="text" name="Ejercicio" id="Ejercicio" value="<?=$field_requerimiento['Ejercicio']?>" style="width:48px;" class="Ejercicio" readonly />
				<input type="text" name="CodPresupuesto" id="CodPresupuesto" value="<?=$field_requerimiento['CodPresupuesto']?>" style="width:48px;" class="CodPresupuesto" readonly />
				<a href="../lib/listas/gehen.php?anz=lista_pv_presupuesto&filtrar=default&FlagCategoriaProg=S&fCodOrganismo=<?=$field_requerimiento['CodOrganismo']?>&fEjercicio=<?=$field_requerimiento['Ejercicio']?>&fCodDependencia=<?=$field_requerimiento['CodDependencia']?>&campo1=Ejercicio&campo2=CodPresupuesto&campo3=CategoriaProg&ventana=lg_requerimiento&iframe=true&width=100%&height=425" rel="prettyPhoto[iframe14]" style=" <?=$display_ver?>" id="btPresupuesto">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
		</tr>
		<tr>
		 	<td class="tagForm">Proveedor Sugerido:</td>
	        <td class="gallery clearfix">
				<input type="hidden" id="ProveedorSugerido" value="<?=$field_requerimiento['ProveedorSugerido']?>" />
				<input type="text" id="NomProveedorSugerido" value="<?=$field_requerimiento['NomProveedor']?>" style="width:190px;" disabled="disabled" />
				<a href="../lib/listas/listado_personas.php?filtrar=default&cod=ProveedorSugerido&nom=NomProveedorSugerido&ventana=requerimiento&iframe=true&width=100%&height=425" rel="prettyPhoto[iframe5]" style=" <?=$display_proveedor?>" id="btProveedorSugerido">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
			<td class="tagForm">Cat. Prog.:</td>
			<td><input type="text" name="CategoriaProg" id="CategoriaProg" value="<?=$field_requerimiento['CategoriaProg']?>" style="width:100px;" class="CategoriaProg" readonly /></td>
		</tr>
		<tr>
		 	<td class="tagForm">Doc. Ref. Proveedor:</td>
			<td><input type="text" id="ProveedorDocRef" style="width:150px;" value="<?=$field_requerimiento['ProveedorDocRef']?>" disabled /></td>
			<td class="tagForm">Fuente de Financiamiento:</td>
			<td>
				<select name="CodFuente" id="CodFuente" style="width:250px;" onchange="$('.CodFuente').val(this.value);" <?=$disabled_ver?>>
					<?=loadSelect2("pv_fuentefinanciamiento","CodFuente","Denominacion",$field_requerimiento['CodFuente'],10)?>
				</select>
			</td>
		</tr>
	</table>
	<center> 
	<input type="submit" value="<?=$label_submit?>" style=" <?=$display_submit?>" />
	<input type="button" value="Cancelar" onclick="this.form.submit();" />
	<input type="button" value="Rechazar" onclick="requerimiento_rechazar(this.form);" style=" <?=$display_rechazar?>" />
	</center>
	<div style="width:1100px" class="divMsj">Campos Obligatorios *</div>
</div>
</form>

<div id="tab2" style="display:none;">
	<form name="frm_detalles" id="frm_detalles">
	<input type="hidden" name="sel_detalles" id="sel_detalles" />
	<table width="1100" class="tblBotones">
		<tr>
	    	<td class="gallery clearfix">
	            <a id="aSelCCosto" href="../lib/listas/listado_centro_costos.php?filtrar=default&cod=CodCentroCosto&nom=NomCentroCosto&ventana=selListadoLista&seldetalle=sel_detalles&filtroDependencia=S&iframe=true&width=1050&height=500" rel="prettyPhoto[iframe6]" style="display:none;"></a>
	            <a id="aSelActivo" href="../lib/listas/listado_activos.php?filtrar=default&campo1=Activo&ventana=selListadoListaParent&seldetalle=sel_detalles&iframe=true&width=1050&height=400" rel="prettyPhoto[iframe7]" style="display:none;"></a>
	            <a id="aSelCategoriaProg" href="../lib/listas/gehen.php?anz=lista_pv_presupuesto&filtrar=default&campo1=detallesCategoriaProg&campo2=detallesEjercicio&campo3=detallesCodPresupuesto&ventana=selListadoListaParentRequerimiento&seldetalle=sel_detalles&iframe=true&width=100%&height=400" rel="prettyPhoto[iframe13]" style="display:none;"></a>
	            <a id="aSelObra" href="../lib/listas/gehen.php?anz=lista_ob_planobras&filtrar=default&campo1=detallesCodPlanObra&campo2=detallesCategoriaProg&campo3=detallesEjercicio&ventana=selListadoListaParent&seldetalle=sel_detalles&iframe=true&width=100%&height=400" rel="prettyPhoto[iframe12]" style="display:none;"></a>
	            <input type="button" class="btLista" id="btSelCCosto" value="Sel. C.Costo" onclick="validarAbrirListaCC('sel_detalles', 'aSelCCosto');" <?=$disabled_ver?> <?=$disabled_restringido?> />
				<input type="button" class="btLista" id="btSelActivo" value="Sel. Activo" onclick="validarAbrirLista('sel_detalles', 'aSelActivo');" <?=$disabled_ver?> <?=$disabled_restringido?> />
				<input type="button" style="width:90px;" id="btSelCategoriaProg" value="Sel. Presupuesto" onclick="validarAbrirLista('sel_detalles', 'aSelCategoriaProg');" <?=$disabled_ver?> <?=$disabled_restringido?> />
				<input type="button" class="btLista" id="btSelObra" value="Sel. Obra" onclick="validarAbrirLista('sel_detalles', 'aSelObra');" <?=$disabled_ver?> <?=$disabled_restringido?> />
	        </td>
			<td align="right" class="gallery clearfix">
	        	<a id="aItem" href="../lib/listas/listado_items.php?filtrar=default&ventana=requerimiento_detalles_insertar&iframe=true&width=100%&height=425" rel="prettyPhoto[iframe8]" style="display:none;"></a>
	        	<a id="aCommodity" href="../lib/listas/listado_commodities.php?iframe=true&width=950&height=400" rel="prettyPhoto[iframe9]" style="display:none;"></a>
				<input type="button" class="btLista" value="Item" id="btItem" onclick="document.getElementById('aItem').click();" <?=$disabled_item?> <?=$disabled_restringido?> />
				<input type="button" class="btLista" value="Commodity" id="btCommodity" onclick="abrirRequerimientoListadoCommodities();" <?=$disabled_commodity?> <?=$disabled_restringido?> />
				<input type="button" class="btLista" value="Borrar" onclick="quitarLinea(this, 'detalles');" <?=$disabled_ver?> <?=$disabled_restringido?> />
			</td>
		</tr>
	</table>
	<center>
	<div style="overflow:scroll; width:1100px; height:400px;">
	<table width="1750" class="tblLista">
		<thead>
			<tr>
		        <th width="25">#</th>
		        <th width="80">C&oacute;digo</th>
		        <th>Descripci&oacute;n</th>
		        <th width="40">Uni.</th>
		        <th width="60">C.Costo</th>
		        <th width="30">Ex.</th>
		        <th width="60">Cantidad</th>
		        <th width="75">Dirigido A</th>
		        <th width="90">Cat. Prog.</th>
		        <th width="32">F.F.</th>
		        <th width="90">Obra</th>
		        <th width="90">Activo</th>
		        <th width="90">Estado</th>
		        <th width="125">Doc. Referencia</th>
		        <th width="60">Cant. Compra</th>
		        <th width="60">Cant. Recibida</th>
		        <th width="75">Fecha Cotizaci&oacute;n</th>
		    </tr>
	    </thead>
	    
	    <tbody id="lista_detalles">
	    <?php
		$nrodetalles = 0;
		$sql = "SELECT r.*, pv.CategoriaProg
				FROM lg_requerimientosdet r
				LEFT JOIN pv_presupuesto pv On (pv.codOrganismo = r.CodOrganismo AND pv.CodPresupuesto = r.CodPresupuesto)
				WHERE r.CodRequerimiento = '".$CodRequerimiento."'";
		$query_detalles = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		while ($field_detalles = mysql_fetch_array($query_detalles)) {
			$nrodetalles++;
			if ($field_detalles['CodItem'] != "") {
				$disabled_descripcion = "readonly";
				$Codigo = $field_detalles['CodItem'];
				$CommoditySub = "";
			} else {
				$disabled_descripcion = "";
				$CodItem = "";
				$Codigo = $field_detalles['CommoditySub'];
			}
			$disabled_descripcion = $disabled_ver;
			?>
			<tr class="trListaBody" onclick="mClk(this, 'sel_detalles');" id="detalles_<?=$nrodetalles?>">
				<td align="center">
					<?=$nrodetalles?>
	            </td>
				<td align="center">
	            	<?=$Codigo?>
	                <input type="hidden" name="CodItem" class="cell2" style="text-align:center;" value="<?=$field_detalles['CodItem']?>" readonly />
	                <input type="hidden" name="CommoditySub" class="cell2" style="text-align:center;" value="<?=$field_detalles['CommoditySub']?>" readonly />
	            </td>
				<td align="center">
					<textarea name="Descripcion" style="height:30px;" class="cell" onBlur="this.style.height='30px';" onFocus="this.style.height='60px';" <?=$disabled_descripcion?> <?=$disabled_restringido?>><?=($field_detalles['Descripcion'])?></textarea>
				</td>
				<td align="center">
					<select name="CodUnidad" class="cell">
						<?=loadSelect2('mastunidades','CodUnidad','Descripcion',$field_detalles['CodUnidad'],20)?>
					</select>
	            </td>
				<td align="center">
					<input type="text" name="CodCentroCosto" id="CodCentroCosto_<?=$nrodetalles?>" class="cell" style="text-align:center;" value="<?=$field_detalles['CodCentroCosto']?>" <?=$disabled_ver?> <?=$disabled_restringido?> />
					<input type="hidden" name="NomCentroCosto" id="NomCentroCosto_<?=$nrodetalles?>" value="<?=($field_detalles['NomCentroCosto'])?>" />
				</td>
				<td align="center">
	            	<input type="checkbox" name="FlagExonerado" <?=chkFlag($field_requerimiento['FlagExonerado'])?> <?=$disabled_ver?> <?=$disabled_restringido?> />
	            </td>
				<td align="center">
	            	<input type="text" name="CantidadPedida" class="cell" style="text-align:right; font-weight:bold;" value="<?=number_format($field_detalles['CantidadPedida'], 2, ',', '.')?>" onBlur="numeroBlur(this);" onFocus="numeroFocus(this);" <?=$disabled_ver?> <?=$disabled_restringido?> />
	            </td>
				<td align="center">
	            	<input type="hidden" name="FlagCompraAlmacen" value="<?=$field_detalles['FlagCompraAlmacen']?>" />
					<?=printValoresGeneral("DIRIGIDO", $field_detalles['FlagCompraAlmacen'])?>
	            </td>
				<td align="center">
	                <input type="text" name="detallesCategoriaProg" id="detallesCategoriaProg_<?=$nrodetalles?>" class="cell2 CategoriaProg" style="text-align:center;" value="<?=$field_detalles['CategoriaProg']?>" readonly />
	                <input type="hidden" name="detallesEjercicio" id="detallesEjercicio_<?=$nrodetalles?>" class="cell2 Ejercicio" style="text-align:center;" value="<?=$field_detalles['Ejercicio']?>" readonly />
	                <input type="hidden" name="detallesCodPresupuesto" id="detallesCodPresupuesto_<?=$nrodetalles?>" class="cell2 CodPresupuesto" style="text-align:center;" value="<?=$field_detalles['CodPresupuesto']?>" readonly />
	            </td>
	            <td>
					<select name="detallesCodFuente" id="detallesCodFuente_<?=$nrodetalles?>" class="cell2 CodFuente" <?=$disabled_ver?>>
						<?=loadSelect2("pv_fuentefinanciamiento","CodFuente","Denominacion",$field_detalles['CodFuente'],10)?>
					</select>
	            </td>
				<td align="center">
	            	<input type="text" name="detallesCodPlanObra" id="detallesCodPlanObra_<?=$nrodetalles?>" value="<?=$field_detalles['CodPlanObra']?>" class="cell2" style="text-align:center;" readonly />
	            </td>
				<td align="center">
	            	<input type="text" name="Activo" id="Activo_<?=$nrodetalles?>" value="<?=$field_detalles['Activo']?>" class="cell2" style="text-align:center;" readonly />
	            </td>
				<td align="center">
					<?=printValoresGeneral("ESTADO-REQUERIMIENTO-DETALLE", $field_detalles['Estado'])?>
	            </td>
				<td align="center">
					<?=$field_detalles['DocReferencia']?>
					<input type="hidden" name="CodCuenta" value="<?=$field_detalles['CodCuenta']?>" />
					<input type="hidden" name="CodCuentaPub20" value="<?=$field_detalles['CodCuentaPub20']?>" />
					<input type="hidden" name="cod_partida" value="<?=$field_detalles['cod_partida']?>" />
				</td>
				<td align="right">
					<?=number_format($field_detalles['CantidadOrdenCompra'], 2, ',', '.')?>
	            </td>
				<td align="right">
					<?=number_format($field_detalles['CantidadRecibida'], 2, ',', '.')?>
	            </td>
				<td align="center">
					<?=formatFechaDMA($field_detalles['CotizacionFechaAsignacion'])?>
	            </td>
			</tr>
			<?php
		}
		?>
	    </tbody>
	</table>
	</div>
	</center>
	<input type="hidden" id="nro_detalles" value="<?=$nrodetalles?>" />
	<input type="hidden" id="can_detalles" value="<?=$nrodetalles?>" />
	</form>
</div>

<div id="tab3" style="display:none;">
	<center>
	<div style="width:1100px;" class="divFormCaption">Cotizaciones</div>
	<div style="overflow:scroll; width:1100px; height:400px;">
	<table width="100%" class="tblLista">
		<thead>
		<tr>
	        <th width="100">C&oacute;digo</th>
	        <th>Raz&oacute;n Social</th>
	        <th width="75">Cantidad</th>
	        <th width="100">Precio Unit.</th>
	        <th width="100">Monto Total</th>
	        <th width="30">Asig.</th>
	        <th width="75">Fecha</th>
	        <th width="100">Cotizaci&oacute;n #</th>
	        <th width="100">Invitaci&oacute;n #</th>
	    </tr>
	    </thead>
	    
	    <tbody>
	    <?php
		$sql = "SELECT
					c.*,
					rd.CodItem,
					rd.CommoditySub,
					rd.Descripcion,
					rd.Secuencia
				FROM
					lg_cotizacion c
					INNER JOIN lg_requerimientosdet rd ON (c.CodRequerimiento = rd.CodRequerimiento AND
														   c.Secuencia = rd.Secuencia)
				WHERE c.CodRequerimiento = '".$CodRequerimiento."'
				ORDER BY CodItem, CommoditySub, Descripcion, CodProveedor";
		$query_cotizaciones = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		while ($field_cotizaciones = mysql_fetch_array($query_cotizaciones)) {
			if ($field_cotizaciones['CodItem'] != "") $Codigo = $field_cotizaciones['CodItem'];
			else $Codigo = $field_cotizaciones['CommoditySub'];
			if ($agrupa != $field_cotizaciones['Descripcion']) {
				$agrupa = $field_cotizaciones['Descripcion'];
				?>
				<tr class="trListaBody2">
	                <td align="center"><?=$Codigo?></td>
	                <td colspan="4"><?=($field_cotizaciones['Descripcion'])?></td>
				</tr>
				<?php
			}
			?>
	        <tr class="trListaBody">
				<td align="right"><?=$field_cotizaciones['CodProveedor']?></td>
				<td><?=($field_cotizaciones['NomProveedor'])?></td>
				<td align="right"><?=number_format($field_cotizaciones['Cantidad'], 2, ',', '.')?></td>
				<td align="right"><?=number_format($field_cotizaciones['PrecioUnit'], 2, ',', '.')?></td>
				<td align="right"><?=number_format($field_cotizaciones['Total'], 2, ',', '.')?></td>
				<td align="center"><?=printFlag($field_cotizaciones['FlagAsignado'])?></td>
				<td align="center"><?=formatFechaDMA($field_cotizaciones['FechaDocumento'])?></td>
				<td align="center"><?=$field_cotizaciones['NumeroCotizacion']?></td>
				<td align="center"><?=$field_cotizaciones['CotizacionNumero']?></td>
			</tr>
	        <?php
	    }
		?>
	    </tbody>
	</table>
	</div>
	</center>
</div>

<div id="tab4" style="display:none;">
	<center>
	<div style="width:1100px;" class="divFormCaption">Avances</div>
	<div style="overflow:scroll; width:1100px; height:400px;">
	<table width="100%" class="tblLista">
		<thead>
		<tr>
	        <th width="100">Item</th>
	        <th>Descripci&oacute;n</th>
	        <th width="75">Cantidad</th>
	        <th width="100">Transacci&oacute;n</th>
	        <th width="150">Almacen</th>
	    </tr>
	    </thead>
	    
	    <tbody>
	    <?php
		$sql = "SELECT
					t.CodTransaccion,
					td.CodDocumento,
					td.NroDocumento,
					td.CodItem,
					td.Descripcion,
					td.CantidadRecibida,
					a.Descripcion AS NomAlmacen
				FROM 
					lg_transacciondetalle td 
					INNER JOIN lg_transaccion t ON (td.CodOrganismo = t.CodOrganismo AND
													td.CodDocumento = t.CodDocumento AND
													td.NroDocumento = t.NroDocumento AND
													td.ReferenciaCodDocumento = 'RQ')
					LEFT JOIN lg_almacenmast a ON (t.CodAlmacen = a.CodAlmacen)
				WHERE td.ReferenciaNroDocumento = '".$CodRequerimiento."'";
		$query_avances = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		while ($field_avances = mysql_fetch_array($query_avances)) {
			?>
			<tr class="trListaBody">
				<td align="center"><?=$field_avances['CodItem']?></td>
				<td><?=($field_avances['Descripcion'])?></td>
				<td align="right"><?=number_format($field_avances['CantidadRecibida'], 2, ',', '.')?></td>
				<td align="center"><?=$field_avances['CodDocumento']?>-<?=$field_avances['NroDocumento']?></td>
				<td align="center"><?=($field_avances['NomAlmacen'])?></td>
			</tr>
			<?php
		}
		?>
	    </tbody>
	</table>
	</div>
	</center>
</div>

<div id="tab5" style="display:none;">
	<center>
	<div style="width:1100px;" class="divFormCaption">Distribuci&oacute;n Contable</div>
	<div style="overflow:scroll; width:1100px; height:150px;">
	<table width="100%" class="tblLista">
		<thead>
		<tr>
	        <th width="100">Cuenta</th>
	        <th>Descripci&oacute;n</th>
	        <th width="75">%</th>
	    </tr>
	    </thead>
	    
	    <tbody id="lista_cuentas">
	    <?php
		$nrocuentas = 0;
		$sql = "SELECT
					rd.CodCuenta,
					c.Descripcion,
					(SELECT COUNT(*)
					 FROM lg_requerimientosdet
					 WHERE
					 	CodCuenta = rd.CodCuenta AND
						CodRequerimiento = rd.CodRequerimiento) AS Numero,
					(SELECT COUNT(*)
					 FROM lg_requerimientosdet
					 WHERE CodRequerimiento = rd.CodRequerimiento) AS Total
				FROM
					lg_requerimientosdet rd
					INNER JOIN ac_mastplancuenta c ON (rd.CodCuenta = c.CodCuenta)
				WHERE rd.CodRequerimiento = '".$CodRequerimiento."'
				GROUP BY CodCuenta";
		$query_cuentas = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		while ($field_cuentas = mysql_fetch_array($query_cuentas)) {
			$nrocuentas++;
			$Porcentaje = $field_cuentas['Numero'] * 100 / $field_cuentas['Total'];
			?>
			<tr class="trListaBody">
				<td align="center">
					<?=$field_cuentas['CodCuenta']?>
	            </td>
				<td>
					<?=htmlentities($field_cuentas['Descripcion'])?>
	            </td>
				<td align="right">
					<?=number_format($Porcentaje, 2, ',', '.')?>
	            </td>
			</tr>
			<?php
		}
		?>
	    </tbody>
	</table>
	</div>
	<div style="width:1100px;" class="divFormCaption">Distribuci&oacute;n Contable (Pub. 20)</div>
	<div style="overflow:scroll; width:1100px; height:150px;">
	<table width="100%" class="tblLista">
		<thead>
		<tr>
	        <th width="100">Cuenta</th>
	        <th>Descripci&oacute;n</th>
	        <th width="75">%</th>
	    </tr>
	    </thead>
	    
	    <tbody id="lista_cuentas20">
	    <?php
		$nrocuentas20 = 0;
		$sql = "SELECT
					rd.CodCuentaPub20,
					c.Descripcion,
					(SELECT COUNT(*)
					 FROM lg_requerimientosdet
					 WHERE
					 	CodCuentaPub20 = rd.CodCuentaPub20 AND
						CodRequerimiento = rd.CodRequerimiento) AS Numero,
					(SELECT COUNT(*)
					 FROM lg_requerimientosdet
					 WHERE CodRequerimiento = rd.CodRequerimiento) AS Total
				FROM
					lg_requerimientosdet rd
					INNER JOIN ac_mastplancuenta20 c ON (rd.CodCuentaPub20 = c.CodCuenta)
				WHERE rd.CodRequerimiento = '".$CodRequerimiento."'
				GROUP BY CodCuentaPub20";
		$query_cuentas20 = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		while ($field_cuentas20 = mysql_fetch_array($query_cuentas20)) {
			$nrocuentas20++;
			$Porcentaje = $field_cuentas20['Numero'] * 100 / $field_cuentas20['Total'];
			?>
			<tr class="trListaBody">
				<td align="center">
					<?=$field_cuentas20['CodCuentaPub20']?>
	            </td>
				<td>
					<?=htmlentities($field_cuentas20['Descripcion'])?>
	            </td>
				<td align="right">
					<?=number_format($Porcentaje, 2, ',', '.')?>
	            </td>
			</tr>
			<?php
		}
		?>
	    </tbody>
	</table>
	</div>
	<div style="width:1100px;" class="divFormCaption">Distribuci&oacute;n Presupuestaria</div>
	<form name="frm_partidas" id="frm_partidas">
	<table width="1100" class="tblBotones" style=" <?=($lista!='caja_chica' && $opcion!='conformar')?'display:none;':''?>">
	    <tr>
	    	<td width="35"><div style="background-color:#F8637D; width:25px; height:20px;"></div></td>
	        <td>Sin disponibilidad presupuestaria</td>
	    	<td width="35"><div style="background-color:#D0FDD2; width:25px; height:20px;"></div></td>
	        <td>Disponibilidad presupuestaria</td>
	    	<td width="35"><div style="background-color:#FFC; width:25px; height:20px;"></div></td>
	        <td>Disponibilidad presupuestaria (Tiene ordenes pendientes)</td>
			<td align="right" class="gallery clearfix">
	        	<a id="a_disponibilidad" href="pagina.php?iframe=true" rel="prettyPhoto[iframe10]" style="display:none;"></a>
				<input type="button" value="Disponibilidad Presupuestaria" onclick="verDisponibilidadPresupuestariaRequerimiento();" />
			</td>
		</tr>
	</table>
	<div style="overflow:scroll; width:1100px; height:150px;">
	<table width="100%" class="tblLista">
		<thead>
		<tr>
	        <th width="30">F.F.</th>
	        <th width="100">Partida</th>
	        <th>Descripci&oacute;n</th>
	        <th width="75">%</th>
	    </tr>
	    </thead>
	    
	    <tbody id="lista_partidas">
	    <?php
		$nropartidas = 0;
		$Grupo = "";
		$sql = "SELECT
					rd.cod_partida,
					rd.CodFuente,
					rd.Ejercicio,
					rd.CodPresupuesto,
					rd.CodOrganismo,
					c.denominacion,
					rd.CodFuente,
					pv.CategoriaProg,
					CONCAT(ss.CodSector, pr.CodPrograma, a.CodActividad) AS CatProg,
					ue.Denominacion AS UnidadEjecutora,
					(SELECT COUNT(*)
					 FROM lg_requerimientosdet
					 WHERE
					 	cod_partida = rd.cod_partida AND
						CodRequerimiento = rd.CodRequerimiento) AS Numero,
					(SELECT COUNT(*)
					 FROM lg_requerimientosdet
					 WHERE CodRequerimiento = rd.CodRequerimiento) AS Total
				FROM
					lg_requerimientosdet rd
					INNER JOIN pv_partida c ON (rd.cod_partida = c.cod_partida)
					LEFT JOIN pv_presupuesto pv ON (pv.CodOrganismo = rd.CodOrganismo AND pv.CodPresupuesto = rd.CodPresupuesto)
					LEFT JOIN pv_categoriaprog cp ON (cp.CategoriaProg = pv.CategoriaProg)
					LEFT JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cp.CodUnidadEjec)
					LEFT JOIN pv_actividades a ON (a.IdActividad = cp.IdActividad)
					LEFT JOIN pv_proyectos py ON (py.IdProyecto = a.IdProyecto)
					LEFT JOIN pv_subprogramas spr On (spr.IdSubPrograma = py.IdSubPrograma)
					LEFT JOIN pv_programas pr ON (pr.IdPrograma = spr.IdPrograma)
					LEFT JOIN pv_subsector ss On (ss.IdSubSector = pr.IdSubSector)
				WHERE rd.CodRequerimiento = '".$CodRequerimiento."'
				GROUP BY rd.CodOrganismo, rd.CodFuente, cod_partida";
		$query_partidas = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		while ($field_partidas = mysql_fetch_array($query_partidas)) {
			list($MontoAjustado, $MontoCompromiso, $PreCompromiso, $CotizacionesAsignadas) = disponibilidadPartida2($field_partidas['Ejercicio'], $field_partidas['CodOrganismo'], $field_partidas['cod_partida'], $field_partidas['CodPresupuesto'], $field_partidas['CodFuente']);
			$MontoPendiente = $PreCompromiso + $CotizacionesAsignadas;
			$MontoDisponible = $MontoAjustado - $MontoCompromiso;
			$MontoDisponibleReal = $MontoAjustado - ($MontoCompromiso + $MontoPendiente);
			##	valido
			if ($MontoDisponible <= 0) $style = "style='font-weight:bold; background-color:#F8637D;'";
			elseif($MontoDisponibleReal <= 0) $style = "style='font-weight:bold; background-color:#FFC;'";
			else $style = "style='font-weight:bold; background-color:#D0FDD2;'";
			##	
			$nropartidas++;
			$Porcentaje = $field_partidas['Numero'] * 100 / $field_partidas['Total'];
			##	
			if ($Grupo != $field_partidas['CatProg']) {
				$Grupo = $field_partidas['CatProg'];
				?>
				<tr class="trListaBody2">
					<td colspan="3">
						<?=$field_partidas['CatProg']?> - <?=$field_partidas['UnidadEjecutora']?>
					</td>
				</tr>
				<?php
			}
			?>
			<tr class="trListaBody" <?=$style?>>
				<td align="center">
					<?=$field_partidas['CodFuente']?>
	            </td>
				<td align="center">
	                <input type="hidden" name="CodPartida[]" value="<?=$field_partidas['cod_partida']?>" />
	                <input type="hidden" name="MontoAjustado[]" value="<?=$MontoAjustado?>" />
	                <input type="hidden" name="MontoCompromiso[]" value="<?=$MontoCompromiso?>" />
	                <input type="hidden" name="PreCompromiso[]" value="<?=$PreCompromiso?>" />
	                <input type="hidden" name="CotizacionesAsignadas[]" value="<?=$CotizacionesAsignadas?>" />
	                <input type="hidden" name="MontoDisponible[]" value="<?=$MontoDisponible?>" />
	                <input type="hidden" name="MontoDisponibleReal[]" value="<?=$MontoDisponibleReal?>" />
					<input type="hidden" name="MontoPendiente[]" value="<?=$MontoPendiente?>" />
	                        <input type="hidden" name="partidasEjercicio[]" value="<?=$field_partidas['Ejercicio']?>" />
	                        <input type="hidden" name="partidasCodPresupuesto[]" value="<?=$field_partidas['CodPresupuesto']?>" />
	                        <input type="hidden" name="partidasCodFuente[]" value="<?=$field_partidas['CodFuente']?>" />
					<?=$field_partidas['cod_partida']?>
	            </td>
				<td>
					<?=htmlentities($field_partidas['denominacion'])?>
	            </td>
				<td align="right">
					<?=number_format($Porcentaje, 2, ',', '.')?>
	            </td>
			</tr>
			<?php
		}
		?>
	    </tbody>
	</table>
	</div>
	</form>
	</center>
</div>

<script type="text/javascript" charset="utf-8">
	//	requerimiento
	function requerimiento_rechazar(form) {
		var RazonRechazo = $("#RazonRechazo").val();
		if (RazonRechazo.trim() == "") {
			$("#cajaModal").dialog({
				buttons: {
					"Si": function() {
						$(this).dialog("close");
						requerimiento(form, 'rechazar');
					},
					"No": function() {
						$(this).dialog("close");
					}
				}
			});	
			$("#cajaModal").dialog({ 
				title: "<img src='../imagenes/info.png' width='24' align='absmiddle' />Confirmación", 
				width: 400
			});
			$("#cajaModal").html("El campo <strong>Razón Rechazo</strong> esta vacio.<br />¿Continuar de todas formas?");
			$('#cajaModal').dialog('open');
		} else {
			requerimiento(form, 'rechazar');
		}
	}
	//	
	function mostrarTabDistribucionRequerimiento(boo) {
		//	detalles
		var detalles = "";
		var frm_detalles = document.getElementById("frm_detalles");
		for(var i=0; n=frm_detalles.elements[i]; i++) {
			if (n.name == "CantidadPedida") {
				var CantidadPedida = parseFloat(setNumero(n.value));
				if (isNaN(CantidadPedida) || CantidadPedida <= 0) CantidadPedida = 0;
				detalles += CantidadPedida + ";char:td;";
			}
			else if (n.name == "detallesCategoriaProg") detalles += n.value + ";char:td;";
			else if (n.name == "detallesEjercicio") detalles += n.value + ";char:td;";
			else if (n.name == "detallesCodPresupuesto") detalles += n.value + ";char:td;";
			else if (n.name == "detallesCodFuente") detalles += n.value + ";char:td;";
			else if (n.name == "CodCuenta") detalles += n.value + ";char:td;";
			else if (n.name == "CodCuentaPub20") detalles += n.value + ";char:td;";
			else if (n.name == "cod_partida") detalles += n.value + ";char:tr;";
		}
		var len = detalles.length; len-=9;
		detalles = detalles.substr(0, len);
		//	
		if (detalles != "") {
			//	ajax
			$.ajax({
				type: "POST",
				url: "lg_requerimiento_ajax.php",
				data: "modulo=ajax&accion=mostrarTabDistribucionRequerimiento&"+$('#frmentrada').serialize()+"&detalles="+detalles,
				async: false,
				success: function(resp) {
					var partes = resp.split("|");
					$("#lista_cuentas").html(partes[0]);
					$("#lista_cuentas20").html(partes[1]);
					$("#lista_partidas").html(partes[2]);
					if (boo) mostrarTab("tab", 5, 5);
				}
			});
		} else {
			$("#lista_cuentas").html("");
			$("#lista_cuentas20").html("");
			$("#lista_partidas").html("");
			mostrarTab("tab", 5, 5);
		}
	}
	//
	function abrirRequerimientoListadoCommodities() {
		var href = $("#aCommodity").attr("href");
		var Clasificacion = $("#Clasificacion").val();
		href = "../lib/listas/listado_commodities.php?filtrar=default&ventana=requerimiento_detalles_insertar&PorClasificacion=S&fClasificacion="+Clasificacion+"&iframe=true&width=950&height=415";
		$("#aCommodity").attr("href", href);
		document.getElementById('aCommodity').click();
	}
	//
	function validarAbrirListaCC(sel, aSel) {
		if ($("#"+sel).val().trim() == "") cajaModal("Debe seleccionar una linea", "error", 400);
		else {
			$('#aSelCCosto').attr('href','../lib/listas/listado_centro_costos.php?filtrar=default&cod=CodCentroCosto&nom=NomCentroCosto&ventana=selListadoLista&seldetalle=sel_detalles&filtroDependencia=S&CodDependencia='+$('#CodDependencia').val()+'&iframe=true&width=1050&height=500');
			document.getElementById(aSel).click();
		}
	}
	//	
	function verDisponibilidadPresupuestariaRequerimiento() {
		var href = "gehen.php?anz=lg_requerimiento_distribucion&"+$('#frm_partidas').serialize()+"&iframe=true&width=100%&height=430";
		$('#a_disponibilidad').attr('href', href);
		$('#a_disponibilidad').click();
	}
	//	
	function setHrefPresupuesto() {
		$.ajax({
			type: "POST",
			url: "../lib/fphp_ajax.php",
			data: "accion=getPresupuestoxDependencia&CodOrganismo="+$('#CodOrganismo').val()+"&CodDependencia="+$('#CodDependencia').val()+"&Ejercicio="+$('#Ejercicio').val()+"&CodCentroCosto="+$('#CodCentroCosto').val(),
			async: false,
			dataType: "json",
			success: function(data) {
				$('.CodPresupuesto').val(data['CodPresupuesto']);
				$('.CategoriaProg').val(data['CategoriaProg']);
				if (data['Ejercicio']) $('.Ejercicio').val(data['Ejercicio']);
				$('#btPresupuesto').attr('href', '../lib/listas/gehen.php?anz=lista_pv_presupuesto&filtrar=default&FlagCategoriaProg=S&fCodOrganismo='+$('#CodOrganismo').val()+'&fEjercicio='+$('#Ejercicio').val()+'&fCodDependencia='+$('#CodDependencia').val()+'&campo1=CodOrganismo&campo2=Ejercicio&campo3=CodPresupuesto&campo4=CategoriaProg&ventana=CategoriaProg&iframe=true&width=100%&height=425');
				$('#aSelCategoriaProg').attr('href', '../lib/listas/gehen.php?anz=lista_pv_presupuesto&filtrar=default&FlagCategoriaProg=S&fCodOrganismo='+$('#CodOrganismo').val()+'&fEjercicio='+$('#Ejercicio').val()+'&fCodDependencia='+$('#CodDependencia').val()+'&campo1=detallesCategoriaProg&campo2=detallesejercicio&campo3=detallesCodPresupuesto&ventana=selListadoListaParentRequerimiento&seldetalle=sel_detalles&iframe=true&width=100%&height=425');
			}
		});
	}
</script>