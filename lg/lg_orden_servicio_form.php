<?php
if ($opcion == "nuevo") {
	$accion = "nuevo";
	$titulo = "Nueva Orden de Servicio";
	$label_submit = "Guardar";

	$sql = "SELECT MAX(Ejercicio) FROM pv_reformulacionmetas";
	$Ejercicio = getVar3($sql);
	$Ejercicio = ($Ejercicio?$Ejercicio:$AnioActual);
	
	//	valores default
	$field_orden['Estado'] = "PR";
	$field_orden['CodOrganismo'] = $_SESSION["ORGANISMO_ACTUAL"];
	$field_orden['PreparadaPor'] = $_SESSION["CODPERSONA_ACTUAL"];
	$field_orden['NomPreparadaPor'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
	if ($_SESSION["DEPENDENCIA_ACTUAL"]) {
		$field_orden['CodDependencia'] = $_SESSION["DEPENDENCIA_ACTUAL"];
		$field_orden['CodCentroCosto'] = $_SESSION["CCOSTO_ACTUAL"];
	} else {
		$field_orden['CodCentroCosto'] = getVar3("SELECT CodCentroCosto FROM ac_mastcentrocosto WHERE Codigo = '$_PARAMETRO[CCOSTOCOMPRA]'");
		$field_orden['CodDependencia'] = getVar3("SELECT CodDependencia FROM ac_mastcentrocosto WHERE Codigo = '$_PARAMETRO[CCOSTOCOMPRA]'");
	}
	$field_orden['Anio'] = substr($Ahora, 0, 4);
	$field_orden['FechaDocumento'] = substr($Ahora, 0, 10);
	$field_orden['FechaPreparacion'] = substr($Ahora, 0, 10);
	$field_orden['PlazoEntrega'] = $_PARAMETRO['DIAENTOC'];
	$field_orden['FechaEntrega'] = formatFechaAMD(getFechaFin(formatFechaDMA(substr($Ahora, 0, 10)), $_PARAMETRO['DIAENTOC']));
	$field_orden['DiasPago'] = $field_orden['PlazoEntrega'];
	$field_orden['FechaValidoDesde'] = substr($Ahora, 0, 10);
	$field_orden['FechaValidoHasta'] = $field_orden['FechaEntrega'];
	##	presupuesto
	$sql = "SELECT p.*
			FROM pv_presupuesto p
			INNER JOIN pv_categoriaprog cp ON (cp.CategoriaProg = p.CategoriaProg)
			INNER JOIN pv_unidadejecutora ue On (ue.CodUnidadEjec = cp.CodUnidadEjec)
			WHERE p.CodOrganismo = '".$field_orden['CodOrganismo']."' AND p.Ejercicio = '".$Ejercicio."' AND ue.CodCentroCosto = '".$field_orden['CodCentroCosto']."'";
	$field_presupuesto = getRecord($sql);
	$field_orden['CodPresupuesto'] = $field_presupuesto['CodPresupuesto'];
	$field_orden['Ejercicio'] = $field_presupuesto['Ejercicio'];
	$field_orden['CategoriaProg'] = $field_presupuesto['CategoriaProg'];
	$field_orden['CodFuente'] = $_PARAMETRO['FFMETASDEF'];
	//	default
	$disabled_detalle = "disabled";
	$disabled_anular = "disabled";
	$display_rechazar = "visibility:hidden;";
}
elseif ($opcion == "modificar" || $opcion == "ver" || $opcion == "revisar" || $opcion == "aprobar" || $opcion == "anular" || $opcion == "cerrar" || $opcion == "modificacion_restringida") {
	list($Anio, $CodOrganismo, $NroOrden) = split("[.]", $registro);
	//	consulto datos generales	
	$sql = "SELECT
				os.*,
				mp.CodTipoServicio,
				ts.Descripcion AS NomTipoServicio,
				i.FactorPorcentaje,
				mp1.NomCompleto AS NomPreparadaPor,
				mp2.NomCompleto AS NomRevisadaPor,
				mp3.NomCompleto AS NomAprobadaPor,
				me1.CodEmpleado AS CodPreparadaPor,
				me2.CodEmpleado AS CodRevisadaPor,
				me3.CodEmpleado AS CodAprobadaPor,
				cc.Abreviatura AS NomCentroCosto,
				pv.CategoriaProg
			FROM
				lg_ordenservicio os
				INNER JOIN mastproveedores mp ON (os.CodProveedor = mp.CodProveedor)
				INNER JOIN ac_mastcentrocosto cc ON (os.CodCentroCosto = cc.CodCentroCosto)
				INNER JOIN masttiposervicio ts ON (mp.CodTipoServicio = ts.CodTipoServicio)
				LEFT JOIN masttiposervicioimpuesto tsi ON (ts.CodTipoServicio = tsi.CodTipoServicio)
				LEFT JOIN mastimpuestos i ON (tsi.CodImpuesto = i.CodImpuesto)
				LEFT JOIN mastpersonas mp1 ON (os.PreparadaPor = mp1.CodPersona)
				LEFT JOIN mastpersonas mp2 ON (os.RevisadaPor = mp2.CodPersona)
				LEFT JOIN mastpersonas mp3 ON (os.AprobadaPor = mp3.CodPersona)
				LEFT JOIN mastempleado me1 ON (me1.CodPersona = mp1.CodPersona)
				LEFT JOIN mastempleado me2 ON (me2.CodPersona = mp2.CodPersona)
				LEFT JOIN mastempleado me3 ON (me3.CodPersona = mp3.CodPersona)
				LEFT JOIN pv_presupuesto pv ON (pv.CodOrganismo = os.CodOrganismo AND pv.CodPresupuesto = os.CodPresupuesto)
			WHERE
				os.Anio = '".$Anio."' AND
				os.CodOrganismo = '".$CodOrganismo."' AND
				os.NroOrden = '".$NroOrden."'";
	$query_orden = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query_orden)) $field_orden = mysql_fetch_array($query_orden);
	##
	if ($opcion == "modificar") {
		$titulo = "Modificar Orden de Servicio";
		$accion = "modificar";
		$disabled_modificar = "disabled";
		$display_modificar = "display:none;";
		$label_submit = "Modificar";
		$display_rechazar = "display:none;";
		$display_rechazar = "visibility:hidden;";
	}
	elseif ($opcion == "ver") {
		$titulo = "Ver Orden de Servicio";
		$disabled_ver = "disabled";
		$display_ver = "display:none;";
		$display_submit = "display:none;";
		$disabled_modificar = "disabled";
		$display_modificar = "display:none;";
		$disabled_anular = "disabled";
		$display_rechazar = "display:none;";
		$display_rechazar = "visibility:hidden;";
		$disabled_restringida = "disabled";
		$display_restringida = "display:none;";
	}
	elseif ($opcion == "revisar") {
		$field_orden['RevisadaPor'] = $_SESSION["CODPERSONA_ACTUAL"];
		$field_orden['NomRevisadaPor'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
		$field_orden['FechaRevision'] = substr($Ahora, 0, 10);
		##
		$titulo = "Revisar Orden de Servicio";
		$accion = "revisar";
		$label_submit = "Revisar";
		$disabled_ver = "disabled";
		$display_ver = "display:none;";
		$disabled_modificar = "disabled";
		$display_modificar = "display:none;";
		$disabled_anular = "disabled";
		$display_rechazar = "display:none;";
		$display_rechazar = "visibility:hidden;";
		$disabled_restringida = "disabled";
		$display_restringida = "display:none;";
	}
	elseif ($opcion == "aprobar") {
		$field_orden['AprobadaPor'] = $_SESSION["CODPERSONA_ACTUAL"];
		$field_orden['NomAprobadaPor'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
		$field_orden['FechaAprobacion'] = substr($Ahora, 0, 10);
		##
		$titulo = "Aprobar Orden de Servicio";
		$accion = "aprobar";
		$label_submit = "Aprobar";
		$disabled_ver = "disabled";
		$disabled_anular = "disabled";
		$display_ver = "display:none;";
		$disabled_modificar = "disabled";
		$display_modificar = "display:none;";
		$disabled_restringida = "disabled";
		$display_restringida = "display:none;";
	}
	elseif ($opcion == "anular") {
		$titulo = "Anular Orden de Servicio";
		$accion = "anular";
		$label_submit = "Anular";
		$disabled_ver = "disabled";
		$display_ver = "display:none;";
		$disabled_modificar = "disabled";
		$display_modificar = "display:none;";
		$display_rechazar = "display:none;";
		$display_rechazar = "visibility:hidden;";
		$disabled_restringida = "disabled";
		$display_restringida = "display:none;";
	}
	elseif ($opcion == "cerrar") {
		$titulo = "Cerrar Orden de Servicio";
		$accion = "cerrar";
		$label_submit = "Cerrar";
		$disabled_ver = "disabled";
		$display_ver = "display:none;";
		$disabled_modificar = "disabled";
		$display_modificar = "display:none;";
		$display_proveedor = "visibility:hidden;";
		$display_rechazar = "display:none;";
		$display_rechazar = "visibility:hidden;";
		$disabled_restringida = "disabled";
		$display_restringida = "display:none;";
	}
	elseif ($opcion == "modificacion_restringida") {
		$titulo = "Modificaci&oacute;n Restringida de Orden de Servicio";
		$accion = "modificacion_restringida";
		$label_submit = "Modificar";
		$disabled_ver = "disabled";
		$display_ver = "display:none;";
		$display_submit = "";
		$disabled_modificar = "disabled";
		$display_modificar = "display:none;";
		$disabled_anular = "disabled";
		$display_rechazar = "display:none;";
		$display_rechazar = "visibility:hidden;";
	}
	$disabled_detalle = $disabled_ver;
	if (!afectaTipoServicio($field_orden['CodTipoServicio'])) { $dFlagExonerado = "disabled"; $cFlagExonerado = "checked"; }
	$FactorImpuesto = getPorcentajeIVA($field_orden['CodTipoServicio']);
}
//	------------------------------------
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
            <li id="li1" onclick="currentTab('tab', this);" class="current"><a href="#" onclick="mostrarTab('tab', 1, 6);">Informaci&oacute;n General</a></li>
            <li id="li2" onclick="currentTab('tab', this);"><a href="#" onclick="mostrarTab('tab', 2, 6);">Items/Commodities</a></li>
            <li id="li3" onclick="currentTab('tab', this);"><a href="#" onclick="mostrarTab('tab', 3, 6);">Cotizaciones</a></li>
            <li id="li4" onclick="currentTab('tab', this);"><a href="#" onclick="mostrarTab('tab', 4, 6);">Obligaciones</a></li>
            <li id="li5" onclick="currentTab('tab', this);"><a href="#" onclick="mostrarTab('tab', 5, 6);">Servicios Realizados</a></li>
            <li id="li6" onclick="currentTab('tab', this);"><a href="#" onclick="mostrarTab('tab', 6, 6);">Distribuci&oacute;n Presupuestaria/Contables</a></li>
            </ul>
            </div>
        </td>
    </tr>
</table>

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=<?=$origen?>" method="POST" onsubmit="return orden_servicio(this, '<?=$accion?>');">
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
<input type="hidden" name="fCodDependencia" id="fCodDependencia" value="<?=$fCodDependencia?>" />
<input type="hidden" name="fCodProveedor" id="fCodProveedor" value="<?=$fCodProveedor?>" />
<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
<input type="hidden" name="fFechaPreparaciond" id="fFechaPreparaciond" value="<?=$fFechaPreparaciond?>" />
<input type="hidden" name="fFechaPreparacionh" id="fFechaPreparacionh" value="<?=$fFechaPreparacionh?>" />
<input type="hidden" id="AnioOrden" value="<?=$field_orden['Anio']?>" />
<input type="hidden" id="Anio" value="<?=$field_orden['Anio']?>" />
<input type="hidden" id="Mes" value="<?=$field_orden['Mes']?>" />
<input type="hidden" id="NroOrden" value="<?=$field_orden['NroOrden']?>" />
<input type="hidden" id="FlagCotizacion" value="N" />

<div id="tab1" style="display:block;">
	<table width="1100" class="tblForm">
		<tr>
			<td class="tagForm" width="150">Orden:</td>
			<td>
	        	<input type="text" id="NroInterno" style="width:65px;" class="codigo" value="<?=$field_orden['NroInterno']?>" disabled="disabled" />
	            <input type="text" id="FechaDocumento" value="<?=formatFechaDMA($field_orden['FechaDocumento'])?>" maxlength="10" style="width:60px;" class="datepicker codigo" onkeyup="setFechaDMA(this);" onchange="setPresupuesto($('#CodOrganismo').val(), $(this).val(), $('#CodPresupuesto'), $('#Anio')); setMontosOrdenServicio(document.getElementById('frm_detalles'));" <?=$disabled_restringida?> />
			</td>
			<td class="tagForm" width="150">Estado:</td>
			<td>
				<input type="hidden" id="Estado" value="<?=$field_orden['Estado']?>" />
				<input type="text" style="width:100px;" class="codigo" value="<?=printValoresGeneral("ESTADO-SERVICIO", $field_orden['Estado'])?>" disabled="disabled" />
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Organismo:</td>
			<td>
				<select id="CodOrganismo" style="width:300px;" onchange="getOptionsSelect(this.value, 'dependencia_filtro', 'CodDependencia', true, 'CodCentroCosto'); $('#CodPresupuesto').val(''); $('#CategoriaProg').val('');" <?=$disabled_modificar?>>
					<?=getOrganismos($field_orden['CodOrganismo'], 3)?>
				</select>
			</td>
	    	<td colspan="2" class="divFormCaption">Monto del Servicio</td>
		</tr>
	    <tr>
			<td class="tagForm">* Dependencia:</td>
			<td>
				<select id="CodDependencia" style="width:300px;" onchange="getOptionsSelect(this.value, 'centro_costo', 'CodCentroCosto', true); setHrefPresupuesto();" <?=$disabled_ver?>>
					<?=getDependencias($field_orden['CodDependencia'], $field_orden['CodOrganismo'], 3)?>
				</select>
			</td>
	        <td class="tagForm">Monto Afecto:</td>
			<td>
	        	<input type="text" id="MontoOriginal" value="<?=number_format($field_orden['MontoOriginal'], 2, ',', '.')?>" style="width:150px; text-align:right;" disabled="disabled" />
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Centro de Costo:</td>
			<td>
				<select id="CodCentroCosto" style="width:300px;" <?=$disabled_ver?>>
					<?=loadSelectDependiente("ac_mastcentrocosto", "CodCentroCosto", "Descripcion", "CodDependencia", $field_orden['CodCentrocosto'], $field_orden['CodDependencia'], 0)?>
				</select>
			</td>
	        <td class="tagForm">Monto No Afecto:</td>
			<td>
	        	<input type="text" id="MontoNoAfecto" value="<?=number_format($field_orden['MontoNoAfecto'], 2, ',', '.')?>" style="width:150px; text-align:right;" disabled="disabled" />
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Proveedor:</td>
			<td class="gallery clearfix">
	            <input type="text" id="CodProveedor" style="width:50px;" value="<?=$field_orden['CodProveedor']?>" disabled="disabled" />
				<input type="text" id="NomProveedor" style="width:235px;" value="<?=$field_orden['NomProveedor']?>" disabled="disabled" />
	        	<a href="../lib/listas/gehen.php?anz=lista_personas&filtrar=default&campo1=CodProveedor&campo2=NomProveedor&fEsProveedor=S&FlagClasePersona=S&ventana=selListadoOrdenServicioPersona&_APLICACION=LG&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" id="btProveedor" style=" <?=$display_modificar?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
	        </td>
	        <td class="tagForm">(+/-) Impuestos:</td>
			<td>
	        	<input type="text" id="MontoIva" value="<?=number_format($field_orden['MontoIva'], 2, ',', '.')?>" style="width:150px; text-align:right;" onfocus="numeroFocus(this);" onblur="numeroBlur(this);" onchange="setNuevoMontoIvaOS();" <?=$disabled_ver?> />
	        </td>
		</tr>
	    <tr>
			<td class="tagForm">* Tipo de Servicio:</td>
			<td>
	        	<input type="hidden" id="FactorImpuesto" value="<?=$FactorImpuesto?>" />
	            <select id="CodTipoServicio" style="width:150px;" <?=$disabled_ver?>>
	                <?=loadSelect("masttiposervicio", "CodTipoServicio", "Descripcion", $field_orden['CodTipoServicio'], 1)?>
	            </select>
	        </td>
	        <td class="tagForm">Monto Total:</td>
			<td>
	        	<input type="text" id="TotalMontoIva" value="<?=number_format($field_orden['TotalMontoIva'], 2, ',', '.')?>" style="width:150px; text-align:right; font-size:12px; font-weight:bold;" disabled="disabled" />
	        </td>
		</tr>
	    <tr>
			<td class="tagForm">* Forma de Pago:</td>
			<td>
	            <select id="CodFormaPago" style="width:150px;" <?=$disabled_restringida?>>
	                <?=loadSelect("mastformapago", "CodFormaPago", "Descripcion", $field_orden['CodFormaPago'], 0)?>
	            </select>
	        </td>
	        <td class="tagForm">Monto Pagado:</td>
			<td>
	        	<input type="text" id="MontoGastado" value="<?=number_format($field_orden['MontoGastado'], 2, ',', '.')?>" style="width:150px; text-align:right;" disabled="disabled" />
	        </td>
		</tr>
	    <tr>
			<td class="tagForm">* Tipo de Pago:</td>
			<td>
	            <select id="CodTipoPago" style="width:150px;" <?=$disabled_restringida?>>
	                <?=loadSelect("masttipopago", "CodTipoPago", "TipoPago", $field_orden['CodTipoPago'], 0)?>
	            </select>
	        </td>
	        <td class="tagForm">Monto Pendiente:</td>
			<td>
	        	<input type="text" id="MontoPendiente" value="<?=number_format($field_orden['MontoPendiente'], 2, ',', '.')?>" style="width:150px; text-align:right; font-size:12px; font-weight:bold;" disabled="disabled" />
	        </td>
		</tr>
	    <tr>
			<td class="tagForm">* Plazo de Entrega:</td>
			<td>
	        	<input type="text" id="PlazoEntrega" value="<?=$field_orden['PlazoEntrega']?>" maxlength="10" style="width:20px;" <?=$disabled_restringida?> /> <em>(dias)</em>
	        	<input type="text" id="FechaEntrega" value="<?=formatFechaDMA($field_orden['FechaEntrega'])?>" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" <?=$disabled_restringida?> />
	        </td>
	    	<td colspan="2" class="divFormCaption">Informaci&oacute;n Adicional</td>
		</tr>
	    <tr>
			<td class="tagForm">* Dias para Pagar:</td>
			<td>
	        	<input type="text" id="DiasPago" value="<?=$field_orden['DiasPago']?>" maxlength="10" style="width:20px;" <?=$disabled_restringida?> /> <em>(dias)</em>
	        </td>
	        <td class="tagForm">Ingresado Por:</td>
	        <td class="gallery clearfix">
				<input type="hidden" id="PreparadaPor" value="<?=$field_orden['PreparadaPor']?>" />
				<input type="text" id="NomPreparadaPor" value="<?=$field_orden['NomPreparadaPor']?>" style="width:195px;" disabled="disabled" />
				<input type="text" id="FechaPreparacion" value="<?=formatFechaDMA($field_orden['FechaPreparacion'])?>" class="datepicker" style="width:60px;" <?=$disabled_restringida?> />
				<a href="../lib/listas/listado_empleados.php?filtrar=default&cod=PreparadaPor&nom=NomPreparadaPor&iframe=true&width=950&height=425" rel="prettyPhoto[iframe2]" style=" <?=$display_restringida?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Desde:</td>
			<td>
	        	<input type="text" id="FechaValidoDesde" value="<?=formatFechaDMA($field_orden['FechaValidoDesde'])?>" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" <?=$disabled_restringida?> />
	        </td>
	        <td class="tagForm">Revisado Por:</td>
	        <td class="gallery clearfix">
				<input type="hidden" id="RevisadaPor" value="<?=$field_orden['RevisadaPor']?>" />
				<input type="text" id="NomRevisadaPor" value="<?=$field_orden['NomRevisadaPor']?>" style="width:195px;" disabled="disabled" />
				<input type="text" id="FechaRevision" value="<?=formatFechaDMA($field_orden['FechaRevision'])?>" class="datepicker" style="width:60px;" <?=$disabled_restringida?> />
				<a href="../lib/listas/listado_empleados.php?filtrar=default&cod=RevisadaPor&nom=NomRevisadaPor&iframe=true&width=950&height=425" rel="prettyPhoto[iframe3]" style=" <?=$display_restringida?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Hasta:</td>
			<td>
	        	<input type="text" id="FechaValidoHasta" value="<?=formatFechaDMA($field_orden['FechaValidoHasta'])?>" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" <?=$disabled_restringida?> />
	        </td>
	        <td class="tagForm">Aprobado Por:</td>
	        <td class="gallery clearfix">
				<input type="hidden" id="AprobadaPor" value="<?=$field_orden['AprobadaPor']?>" />
				<input type="text" id="NomAprobadaPor" value="<?=$field_orden['NomAprobadaPor']?>" style="width:195px;" disabled="disabled" />
				<input type="text" id="FechaAprobacion" value="<?=formatFechaDMA($field_orden['FechaAprobacion'])?>" class="datepicker" style="width:60px;" <?=$disabled_restringida?> />
				<a href="../lib/listas/listado_empleados.php?filtrar=default&cod=AprobadaPor&nom=NomAprobadaPor&iframe=true&width=950&height=425" rel="prettyPhoto[iframe4]" style=" <?=$display_restringida?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
	    </tr>
        <tr>
            <td colspan="2" class="divFormCaption">Presupuesto</td>
            <td colspan="2"></td>
        </tr>
        <tr>
            <td class="tagForm" width="150">Presupuesto:</td>
            <td class="gallery clearfix">
                <input type="text" name="Ejercicio" id="Ejercicio" value="<?=$field_orden['Ejercicio']?>" style="width:48px;" class="Ejercicio" readonly />
                <input type="text" name="CodPresupuesto" id="CodPresupuesto" value="<?=$field_orden['CodPresupuesto']?>" style="width:48px;" class="CodPresupuesto" readonly />
                <a href="../lib/listas/gehen.php?anz=lista_pv_presupuesto&filtrar=default&FlagCategoriaProg=S&fCodOrganismo=<?=$field_orden['CodOrganismo']?>&fEjercicio=<?=$field_orden['Ejercicio']?>&fCodDependencia=<?=$field_orden['CodDependencia']?>&campo1=Ejercicio&campo2=CodPresupuesto&campo3=CategoriaProg&ventana=lg_requerimiento&iframe=true&width=100%&height=425" rel="prettyPhoto[iframe10]" style=" <?=$display_ver?>" id="btPresupuesto">
                    <img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
                </a>
            </td>
            <td colspan="2"></td>
        </tr>
        <tr>
            <td class="tagForm">Cat. Prog.:</td>
            <td><input type="text" name="CategoriaProg" id="CategoriaProg" value="<?=$field_orden['CategoriaProg']?>" style="width:100px;" class="CategoriaProg" readonly /></td>
            <td colspan="2"></td>
        </tr>
        <tr>
            <td class="tagForm">Fuente de Financiamiento:</td>
            <td>
                <select name="CodFuente" id="CodFuente" style="width:250px;" onchange="$('.CodFuente').val(this.value); mostrarTabDistribucionOrden();" <?=$disabled_ver?>>
                    <?=loadSelect2("pv_fuentefinanciamiento","CodFuente","Denominacion",$field_orden['CodFuente'],10)?>
                </select>
            </td>
            <td colspan="2"></td>
        </tr>
		<tr>
	    	<td colspan="4" class="divFormCaption">Observaciones</td>
	    </tr>
		<tr>
			<td class="tagForm">Descripci&oacute;n:</td>
			<td colspan="3"><textarea id="Descripcion" style="width:95%; height:30px;" <?=$disabled_restringida?>><?=($field_orden['Descripcion'])?></textarea></td>
		</tr>
		<tr>
			<td class="tagForm">Descripci&oacute;n Detallada:</td>
			<td colspan="3"><textarea id="DescAdicional" style="width:95%; height:30px;" <?=$disabled_restringida?>><?=($field_orden['DescAdicional'])?></textarea></td>
		</tr>
		<tr>
			<td class="tagForm">Observaciones:</td>
			<td colspan="3"><textarea id="Observaciones" style="width:95%; height:50px;" <?=$disabled_restringida?>><?=($field_orden['Observaciones'])?></textarea></td>
		</tr>
		<tr>
			<td class="tagForm">Razon Rechazo:</td>
			<td colspan="3"><textarea id="MotRechazo" style="width:95%; height:30px;" <?=$disabled_anular?>><?=($field_orden['MotRechazo'])?></textarea></td>
		</tr>
		<tr>
			<td class="tagForm">&Uacute;ltima Modif.:</td>
			<td>
				<input type="text" size="30" value="<?=$field_orden['UltimoUsuario']?>" disabled="disabled" />
				<input type="text" size="25" value="<?=$field_orden['UltimaFecha']?>" disabled="disabled" />
			</td>
		</tr>
	</table>
	<center> 
	<input type="submit" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" />
	<input type="button" value="Cancelar" style="width:75px;" onclick="this.form.submit();" />
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
	            <a id="aSelCCosto" href="../lib/listas/listado_centro_costos.php?filtrar=default&cod=CodCentroCosto&nom=NomCentroCosto&ventana=selListadoLista&seldetalle=sel_detalles&filtroDependencia=S&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe5]" style="display:none;"></a>
	            <a id="aSelActivo" href="../lib/listas/listado_activos.php?filtrar=default&campo1=NroActivo&ventana=selListadoListaParent&seldetalle=sel_detalles&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe6]" style="display:none;"></a>
	            <input type="button" class="btLista" id="btSelCCosto" value="Sel. C.Costo" onclick="validarAbrirLista('sel_detalles', 'aSelCCosto');" <?=$disabled_ver?> />
				<input type="button" class="btLista" id="btSelActivo" value="Sel. Activo" onclick="validarAbrirLista('sel_detalles', 'aSelActivo');" <?=$disabled_ver?> />
	        </td>
			<td align="right" class="gallery clearfix">
	        	<a id="aCommodity" href="../lib/listas/listado_commodities.php?filtrar=default&ventana=orden_servicio_detalles_insertar&fClasificacion=SER&PorClasificacion=S&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe7]" style="display:none;"></a>
				<input type="button" class="btLista" value="Commodity" id="btCommodity" onclick="document.getElementById('aCommodity').click();" <?=$disabled_detalle?> />
				<input type="button" class="btLista" value="Borrar" onclick="quitarLineaOrdenServicio(this, 'detalles', this.form);" <?=$disabled_ver?> />
			</td>
		</tr>
	</table>
	<center>
	<div style="overflow:scroll; width:1100px; height:450px;">
	<table width="2300" class="tblLista">
		<thead>
			<tr>
		        <th width="40">#</th>
		        <th width="90">C&oacute;digo</th>
		        <th>Descripci&oacute;n</th>
		        <th width="75">Cantidad Pedida</th>
		        <th width="100">P. Unit.</th>
		        <th width="50">Exon.</th>
		        <th width="100">Total</th>
		        <th width="90">Cat. Prog.</th>
		        <th width="32">F.F.</th>
		        <th width="75">Fecha Plan.</th>
		        <th width="75">Fecha Real</th>
		        <th width="75">Cantidad Recibida</th>
		        <th width="75">C. Costos</th>
		        <th width="75"># Activo</th>
		        <th width="75">Terminado</th>
		        <th width="100">Partida</th>
		        <th width="100">Cta. Contable</th>
		        <th width="100">Cta. Contable (Pub.20)</th>
		        <th width="400">Observaciones</th>
		    </tr>
	    </thead>
	    
	    <tbody id="lista_detalles">
	    <?php
		$nrodetalles = 0;
		$sql = "SELECT
					ocd.*,
					pv.CategoriaProg,
					cc.Codigo AS NomCentroCosto
				FROM
					lg_ordenserviciodetalle ocd
					LEFT JOIN pv_presupuesto pv ON (pv.CodOrganismo = ocd.CodOrganismo AND pv.CodPresupuesto = ocd.CodPresupuesto)
					LEFT JOIN ac_mastcentrocosto cc ON (cc.CodCentroCosto = ocd.CodCentroCosto)
				WHERE
					ocd.Anio = '".$Anio."' AND
					ocd.CodOrganismo = '".$CodOrganismo."' AND
					ocd.NroOrden = '".$NroOrden."'
				ORDER BY Secuencia";
		$query_detalles = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		while ($field_detalles = mysql_fetch_array($query_detalles)) {
			$nrodetalles++;
			?>
			<tr class="trListaBody" onclick="mClk(this, 'sel_detalles');" id="detalles_<?=$nrodetalles?>">
				<th align="center">
					<?=$nrodetalles?>
	            </th>
				<td align="center">
	            	<?=$field_detalles['CommoditySub']?>
	                <input type="hidden" name="CodItem" />
	                <input type="hidden" name="CommoditySub" value="<?=$field_detalles['CommoditySub']?>" />
	            </td>
				<td align="center">
					<textarea name="Descripcion" style="height:30px;" class="cell" readonly="readonly" <?=$disabled_ver?>><?=($field_detalles['Descripcion'])?></textarea>
				</td>
				<td align="center">
	            	<input type="text" name="CantidadPedida" class="cell" style="text-align:right;" value="<?=number_format($field_detalles['CantidadPedida'], 2, ',', '.')?>" onBlur="numeroBlur(this);" onFocus="numeroFocus(this);" onchange="setMontosOrdenServicio(this.form);" <?=$disabled_ver?> />
	                <input type="hidden" name="CodUnidadRec" value="<?=$field_detalles['CodUnidadRec']?>" />
	                <input type="hidden" name="CantidadRec" value="<?=$field_detalles['CantidadRec']?>" />
	            </td>
				<td align="center">
	            	<input type="text" name="PrecioUnit" class="cell" style="text-align:right;" value="<?=number_format($field_detalles['PrecioUnit'], 2, ',', '.')?>" onBlur="numeroBlur(this);" onFocus="numeroFocus(this);" onchange="setMontosOrdenServicio(this.form);" <?=$disabled_ver?> />
	            </td>
				<td align="center">
	            	<input type="checkbox" name="FlagExonerado" class="FlagExonerado" onchange="setMontosOrdenServicio(this.form);" <?=chkFlag($field_orden['FlagExonerado'])?> <?=$disabled_ver?> <?=$dFlagExonerado?> />
	            </td>
				<td align="center">
	            	<input type="text" name="Total" class="cell2" style="text-align:right;" value="<?=number_format($field_detalles['Total'], 2, ',', '.')?>" readonly="readonly" <?=$disabled_ver?> />
	            </td>
	            <td align="center">
	                <input type="text" name="detallesCategoriaProg" id="detallesCategoriaProg_<?=$nrodetalles?>" class="cell2 CategoriaProg" style="text-align:center;" value="<?=$field_detalles['CategoriaProg']?>" readonly />
	                <input type="hidden" name="detallesEjercicio" id="detallesEjercicio_<?=$nrodetalles?>" class="cell2 Ejercicio" style="text-align:center;" value="<?=$field_detalles['Ejercicio']?>" readonly />
	                <input type="hidden" name="detallesCodPresupuesto" id="detallesCodPresupuesto_<?=$nrodetalles?>" class="cell2 CodPresupuesto" style="text-align:center;" value="<?=$field_detalles['CodPresupuesto']?>" readonly />
	            </td>
	            <td>
	                <select name="detallesCodFuente" id="detallesCodFuente_<?=$nrodetalles?>" class="cell2 CodFuente" <?=$disabled_ver?>>
	                    <?=loadSelect2("pv_fuentefinanciamiento","CodFuente","Denominacion",$field_detalles['CodFuente'],11)?>
	                </select>
	            </td>
				<td align="center">
	            	<input type="text" name="FechaEsperadaTermino" value="<?=formatFechaDMA($field_detalles['FechaEsperadaTermino'])?>" maxlength="10" style="text-align:center;" class="datepicker cell" onkeyup="setFechaDMA(this);" <?=$disabled_ver?> />
	            </td>
				<td align="center">
	            	<input type="text" name="FechaTermino" value="<?=formatFechaDMA($field_detalles['FechaTermino'])?>" maxlength="10" style="text-align:center;" class="datepicker cell" onkeyup="setFechaDMA(this);" <?=$disabled_ver?> />
	            </td>
				<td align="right">
					<?=number_format($field_detalles['CantidadRecibida'], 2, ',', '.')?>
				</td>
				<td align="center">
					<input type="text" name="CodCentroCosto" id="CodCentroCosto_<?=$nrodetalles?>" maxlength="4" class="cell" style="text-align:center;" value="<?=$field_detalles['CodCentroCosto']?>" <?=$disabled_ver?> />
					<input type="hidden" name="NomCentroCosto" id="NomCentroCosto_<?=$nrodetalles?>" value="<?=($field_detalles['NomCentroCosto'])?>" />
				</td>
				<td align="center">
					<input type="text" name="NroActivo" id="NroActivo_<?=$nrodetalles?>" value="<?=($field_detalles['NroActivo'])?>" style="text-align:center;" class="cell" />
				</td>
				<td align="center">
	            	<input type="checkbox" name="FlagTerminado" <?=chkFlag($field_orden['FlagTerminado'])?> disabled />
	            </td>
				<td align="center">
					<?=$field_detalles['cod_partida']?>
					<input type="hidden" name="cod_partida" value="<?=$field_detalles['cod_partida']?>" />
				</td>
				<td align="center">
					<?=$field_detalles['CodCuenta']?>
					<input type="hidden" name="CodCuenta" value="<?=$field_detalles['CodCuenta']?>" />
				</td>
				<td align="center">
					<?=$field_detalles['CodCuentaPub20']?>
					<input type="hidden" name="CodCuentaPub20" value="<?=$field_detalles['CodCuentaPub20']?>" />
				</td>
				<td align="center">
					<textarea name="Comentarios" style="height:30px;" class="cell" <?=$disabled_ver?>><?=($field_detalles['Comentarios'])?></textarea>
					<input type="hidden" name="CodRequerimiento" />
					<input type="hidden" name="Secuencia" />
					<input type="hidden" name="CotizacionSecuencia" />
					<input type="hidden" name="CantidadRequerimiento" />
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
	<div style="overflow:scroll; width:1100px; height:200px;">
	<table width="1800" class="tblLista">
		<thead>
		<tr>
	        <th width="80">C&oacute;digo</th>
	        <th width="300" align="left">Raz&oacute;n Social</th>
	        <th width="50">Cant.</th>
	        <th width="75">Precio Unit.</th>
	        <th width="75">Precio Unit./Imp.</th>
	        <th width="75">Monto Total</th>
	        <th width="30">Asig.</th>
	        <th width="50">Dias Entrega</th>
	        <th width="75">Fecha Entrega</th>
	        <th width="100">Cotizaci&oacute;n #</th>
	        <th align="left">Observaciones</th>
	    </tr>
	    </thead>
	    
		<tbody>
	    <?php
		$sql = "SELECT
					c.CodProveedor,
					c.NomProveedor,
					c.Cantidad,
					c.PrecioUnit,
					c.PrecioUnitIva,
					c.Total,
					c.FlagAsignado,
					c.DiasEntrega,
					c.FechaEntrega,
					c.Observaciones,
					rd.CodItem,
					rd.CommoditySub,
					rd.Descripcion,
					rd.CodUnidad
				FROM
					lg_cotizacionordenes co
					INNER JOIN lg_cotizacion c ON (c.CodRequerimiento = co.CodRequerimiento AND
												   c.Secuencia = co.SecuenciaRequerimiento)
					INNER JOIN lg_requerimientosdet rd ON (rd.CodRequerimiento = co.CodRequerimiento AND
														   rd.Secuencia = co.SecuenciaRequerimiento)
				WHERE
					co.TipoOrden = 'OS' AND
					co.Anio = '".$Anio."' AND
					co.CodOrganismo = '".$CodOrganismo."' AND
					co.NroOrden = '".$NroOrden."'
				ORDER BY CodItem, CommoditySub, CodProveedor";
		$query_cotizaciones = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		while ($field_cotizaciones = mysql_fetch_array($query_cotizaciones)) {
			if ($field_cotizaciones['CodItem'] != "") $Codigo = $field_cotizaciones['CodItem'];
			else $Codigo = $field_cotizaciones['CommoditySub'];
			$Observaciones = substr($field_cotizaciones['Observaciones'], 0, 400);
			if ($agrupa != $field_cotizaciones['Descripcion']) {
				$agrupa = $field_cotizaciones['Descripcion'];
				?>
				<tr class="trListaBody2">
	                <td align="center"><?=$Codigo?></td>
	                <td colspan="10"><?=htmlentities($field_cotizaciones['Descripcion'])?></td>
				</tr>
				<?php
			}
			?>
	        <tr class="trListaBody">
	            <td align="right"><?=$field_cotizaciones['CodProveedor']?></td>
	            <td><?=htmlentities($field_cotizaciones['NomProveedor'])?></td>
	            <td align="right"><?=number_format($field_cotizaciones['Cantidad'], 2, ',', '.')?></td>
	            <td align="right"><?=number_format($field_cotizaciones['PrecioUnit'], 2, ',', '.')?></td>
	            <td align="right"><?=number_format($field_cotizaciones['PrecioUnitIva'], 2, ',', '.')?></td>
	            <td align="right"><?=number_format($field_cotizaciones['Total'], 2, ',', '.')?></td>
	            <td align="center"><?=printFlag($field_cotizaciones['FlagAsignado'])?></td>
	            <td align="center"><?=$field_cotizaciones['DiasEntrega']?></td>
	            <td align="center"><?=formatFechaDMA($field_cotizaciones['FechaEntrega'])?></td>
	            <td align="center"><?=$field_cotizaciones['NumeroCotizacion']?></td>
	            <td title="<?=htmlentities($field_cotizaciones['Observaciones'])?>"><?=htmlentities($Observaciones)?></td>
			</tr>
	        <?php
	    }
		?>
	    </tbody>
	</table>
	</div>
	<div style="width:1100px;" class="divFormCaption">Requerimientos</div>
	<div style="overflow:scroll; width:1100px; height:200px;">
	<table width="2000" class="tblLista">
		<thead>
		<tr>
	        <th width="100">Requerimiento</th>
	        <th width="15">#</th>
	        <th width="80">C&oacute;digo</th>
	        <th width="400" align="left">Descripci&oacute;n</th>
	        <th width="50">Cant.</th>
	        <th width="75">Fecha Pedida</th>
	        <th width="75">Fecha Aprobaci&oacute;n</th>
	        <th align="left">Comentarios</th>
	        <th width="300">Preparado Por</th>
	    </tr>
	    </thead>

		<tbody>
	    <?php
		$sql = "SELECT 
					rd.CodRequerimiento,
					rd.Secuencia,
					rd.CantidadOrdenCompra,
					r.Comentarios,
					rd.CommoditySub,
					rd.Descripcion,
					r.CodInterno,
					r.FechaPreparacion,
					r.FechaAprobacion,
					mp.NomCompleto AS PreparadoPor
				FROM 
					lg_requerimientosdet rd
					INNER JOIN lg_requerimientos r ON (rd.CodRequerimiento = r.CodRequerimiento)
					INNER JOIN mastpersonas mp ON (r.PreparadaPor = mp.CodPersona)
				WHERE
					rd.Anio = '".$Anio."' AND
					rd.CodOrganismo = '".$CodOrganismo."' AND
					rd.NroOrden = '".$NroOrden."' AND
					r.Clasificacion = 'SER'
				ORDER BY Secuencia";
		$query_requerimientosdet = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		while ($field_requerimientosdet = mysql_fetch_array($query_requerimientosdet)) {
			$Codigo = $field_requerimientosdet['CommoditySub'];
			$Comentarios = substr($field_requerimientosdet['Comentarios'], 0, 400);
			$Descripcion = substr($field_requerimientosdet['Descripcion'], 0, 250);
			?>
			<tr class="trListaBody">
				<td align="center"><?=$field_requerimientosdet['CodInterno']?></td>
				<td align="center"><?=$field_requerimientosdet['Secuencia']?></td>
	            <td align="center"><?=$Codigo?></td>
				<td><?=htmlentities($Descripcion)?></td>
				<td align="right"><?=number_format($field_requerimientosdet['CantidadOrdenCompra'], 2, ',', '.')?></td>
				<td align="center"><?=formatFechaDMA($field_requerimientosdet['FechaPreparacion'])?></td>
				<td align="center"><?=formatFechaDMA($field_requerimientosdet['FechaAprobacion'])?></td>
				<td title="<?=htmlentities($field_requerimientosdet['Comentarios'])?>"><?=htmlentities($Comentarios)?></td>
				<td><?=htmlentities($field_requerimientosdet['PreparadoPor'])?></td>
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
	<div style="overflow:scroll; width:1100px; height:450px;">
	<table width="100%" class="tblLista">
		<thead>
		<tr>
	        <th width="125">Documento</th>
	        <th width="60">Fecha</th>
	        <th align="left">Descripci&oacute;n</th>
	        <th width="100" align="right">Monto Afecto</th>
	        <th width="100" align="right">Monto Obligaci&oacute;n</th>
	        <th width="75">Estado</th>
	    </tr>
	    </thead>
	    
	    <tbody>
	    <?php
		$sql = "SELECT
					o.CodTipoDocumento,
					o.NroDocumento,
					o.MontoObligacion,
					o.Comentarios,
					o.FechaRegistro,
					o.MontoAfecto,
					o.Estado
				FROM
					ap_obligaciones o
					INNER JOIN ap_documentos d ON (d.CodProveedor = o.CodProveedor AND
												   d.ObligacionTipoDocumento = o.CodTipoDocumento AND
												   d.ObligacionNroDocumento = o.NroDocumento)
				WHERE
					d.Anio = '".$Anio."' AND
					d.CodOrganismo = '".$CodOrganismo."' AND
					d.ReferenciaNroDocumento = '".$NroOrden."' AND
					d.ReferenciaTipoDocumento = 'OS'";
		$query_avances = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		while ($field_avances = mysql_fetch_array($query_avances)) {
			?>
			<tr class="trListaBody">
				<td align="center"><?=$field_avances['CodTipoDocumento']?>-<?=$field_avances['NroDocumento']?></td>
				<td align="center"><?=formatFechaDMA($field_avances['FechaRegistro'])?></td>
				<td><?=htmlentities($field_avances['Comentarios'])?></td>
				<td align="right"><?=number_format($field_avances['MontoAfecto'], 2, ',', '.')?></td>
				<td align="right"><strong><?=number_format($field_avances['MontoObligacion'], 2, ',', '.')?></strong></td>
				<td align="center"><?=printValoresGeneral("ESTADO-OBLIGACIONES", $field_avances['Estado'])?></td>
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
	<div style="overflow:scroll; width:1100px; height:450px;">
	<table width="100%" class="tblLista">
		<thead>
		<tr>
	        <th width="125">Documento Referencia</th>
	        <th width="75">Commodity</th>
	        <th>Descripci&oacute;n</th>
	        <th width="100">Cantidad</th>
	        <th width="100">Precio Unitario</th>
	        <th width="100">Total</th>
	    </tr>
	    </thead>
	    
	    <tbody>
	    <?php
		$sql = "SELECT
					cs.Secuencia,
					cs.NroConfirmacion,
					cs.DocumentoReferencia,
					dd.CommoditySub,
					dd.Descripcion,
					dd.Cantidad,
					dd.PrecioUnit,
					dd.PrecioCantidad,
					dd.Total
				FROM
					lg_confirmacionservicio cs
					INNER JOIN lg_ordenserviciodetalle osd ON (cs.Anio = osd.Anio AND
															   cs.CodOrganismo = osd.CodOrganismo AND
															   cs.NroOrden = osd.NroOrden)
					INNER JOIN ap_documentosdetalle dd ON (cs.Anio = dd.Anio AND
														   cs.DocumentoReferencia = dd.DocumentoReferencia AND
														   dd.DocumentoClasificacion = 'SER')
				WHERE
					cs.Anio = '".$Anio."' AND
					cs.CodOrganismo = '".$CodOrganismo."' AND
					cs.NroOrden = '".$NroOrden."'
				GROUP BY DocumentoReferencia";
		$query_obligacion = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		$rows_obligacion = mysql_num_rows($query_obligacion);
		while ($field_obligacion = mysql_fetch_array($query_obligacion)) {
			?>
			<tr class="trListaBody">
				<td align="center"><?=$field_obligacion['DocumentoReferencia']?></td>
				<td align="center"><?=$field_obligacion['CommoditySub']?></td>
				<td><?=htmlentities($field_obligacion['Descripcion'])?></td>
				<td align="right"><?=number_format($field_obligacion['Cantidad'], 2, ',', '.')?></td>
				<td align="right"><?=number_format($field_obligacion['PrecioUnit'], 2, ',', '.')?></td>
				<td align="right"><?=number_format($field_obligacion['Total'], 2, ',', '.')?></td>
			</tr>
			<?php
			
		}

		?>
	    </tbody>
	</table>
	</div>
	</center>
</div>

<div id="tab6" style="display:none;">
	<center>
	<div style="width:1100px;" class="divFormCaption">Distribuci&oacute;n Contable</div>
	<div style="overflow:scroll; width:1100px; height:150px;">
		<table width="100%" class="tblLista">
			<thead>
			<tr>
		        <th width="100">Cuenta</th>
		        <th>Descripci&oacute;n</th>
		        <th width="100">Monto</th>
		    </tr>
		    </thead>
		    
		    <tbody id="lista_cuentas">
		    <?php
			$nrocuentas = 0;
			$sql = "(SELECT 
						do.CodCuenta AS Codigo,
						pc.Descripcion,
						SUM(do.CantidadPedida * do.PrecioUnit) AS Monto
					 FROM 
						lg_ordenserviciodetalle do
						INNER JOIN ac_mastplancuenta pc ON (do.CodCuenta = pc.CodCuenta)
					 WHERE
						do.Anio = '".$Anio."' AND
						do.CodOrganismo = '".$CodOrganismo."' AND
						do.NroOrden = '".$NroOrden."'
					 GROUP BY Codigo)
					UNION
					(SELECT 
						os.CodCuenta AS Codigo, 
						pc.Descripcion,
						os.MontoIva AS Monto
					 FROM 
						lg_ordenservicio os
						INNER JOIN ac_mastplancuenta pc ON (os.CodCuenta = pc.CodCuenta)
					 WHERE
						os.Anio = '".$Anio."' AND
						os.CodOrganismo = '".$CodOrganismo."' AND
						os.NroOrden = '".$NroOrden."' AND
						os.MontoIva <> 0
					 GROUP BY Codigo)
					ORDER BY Codigo";
			$query_cuentas = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field_cuentas = mysql_fetch_array($query_cuentas)) {
				?>
		        <tr class="trListaBody">
					<td align="center"><?=$field_cuentas['Codigo']?></td>
					<td><?=($field_cuentas['Descripcion'])?></td>
					<td align="right"><?=number_format($field_cuentas['Monto'], 2, ',', '.')?></td>
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
		        <th width="100">Monto</th>
		    </tr>
		    </thead>
		    
		    <tbody id="lista_cuentas20">
		    <?php
			$nrocuentas = 0;
			$sql = "(SELECT 
						do.CodCuentaPub20 AS Codigo,
						pc.Descripcion,
						SUM(do.CantidadPedida * do.PrecioUnit) AS Monto
					 FROM 
						lg_ordenserviciodetalle do
						INNER JOIN ac_mastplancuenta20 pc ON (do.CodCuentaPub20 = pc.CodCuenta)
					 WHERE
						do.Anio = '".$Anio."' AND
						do.CodOrganismo = '".$CodOrganismo."' AND
						do.NroOrden = '".$NroOrden."'
					 GROUP BY Codigo)
					UNION
					(SELECT 
						os.CodCuentaPub20 AS Codigo, 
						pc.Descripcion,
						os.MontoIva AS Monto
					 FROM 
						lg_ordenservicio os
						INNER JOIN ac_mastplancuenta20 pc ON (os.CodCuentaPub20 = pc.CodCuenta)
					 WHERE
						os.Anio = '".$Anio."' AND
						os.CodOrganismo = '".$CodOrganismo."' AND
						os.NroOrden = '".$NroOrden."' AND
						os.MontoIva <> 0
					 GROUP BY Codigo)
					ORDER BY Codigo";
			$query_cuentas = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			while ($field_cuentas = mysql_fetch_array($query_cuentas)) {
				?>
		        <tr class="trListaBody">
					<td align="center"><?=$field_cuentas['Codigo']?></td>
					<td><?=($field_cuentas['Descripcion'])?></td>
					<td align="right"><?=number_format($field_cuentas['Monto'], 2, ',', '.')?></td>
				</tr>
		        <?php
				
			}
			?>
		    </tbody>
		</table>
	</div>

	<div style="width:1100px;" class="divFormCaption">Distribuci&oacute;n Presupuestaria</div>
		<form name="frm_partidas" id="frm_partidas">
		<table width="1100" class="tblBotones">
			<tr>
		    	<td width="35"><div style="background-color:#F8637D; width:25px; height:20px;"></div></td>
		        <td>Sin disponibilidad presupuestaria</td>
		    	<td width="35"><div style="background-color:#D0FDD2; width:25px; height:20px;"></div></td>
		        <td>Disponibilidad presupuestaria</td>
		    	<td width="35"><div style="background-color:#FFC; width:25px; height:20px;"></div></td>
		        <td>Disponibilidad presupuestaria (Tiene ordenes pendientes)</td>
				<td align="right" class="gallery clearfix">
		        	<a id="a_disponibilidad" href="pagina.php?iframe=true" rel="prettyPhoto[iframe8]" style="display:none;"></a>
					<input type="button" value="Disponibilidad Presupuestaria" onclick="verDisponibilidadPresupuestaria();" />
				</td>
			</tr>
		</table>
		<div style="overflow:scroll; width:1100px; height:150px;">
			<table width="100%" class="tblLista">
				<thead>
				<tr>
					<th width="25">F.F.</th>
			        <th width="100">Partida</th>
			        <th>Descripci&oacute;n</th>
			        <th width="100">Monto</th>
			    </tr>
			    </thead>
			    
			    <tbody id="lista_partidas">
			    <?php
				$nropartidas = 0;
				$Grupo = '';
				$sql = "SELECT 
							do.cod_partida, 
							pc.denominacion,
							do.Monto,
							do.CodPresupuesto,
							do.Ejercicio,
							do.CodFuente,
							pc.CodCuenta,
							pc.CodCuentaPub20,
							ff.Denominacion AS Fuente,
							ue.Denominacion AS UnidadEjecutora,
							cp.CategoriaProg,
							CONCAT(ss.CodSector, pr.CodPrograma, a.CodActividad) AS CatProg
						FROM
							lg_distribucioncompromisos do
							INNER JOIN pv_partida pc ON (do.cod_partida = pc.cod_partida)
							LEFT JOIN pv_fuentefinanciamiento ff ON (ff.CodFuente = do.CodFuente)
							LEFT JOIN pv_presupuesto pv ON (pv.CodOrganismo = do.CodOrganismo AND pv.CodPresupuesto = do.CodPresupuesto)
							LEFT JOIN pv_categoriaprog cp ON (cp.CategoriaProg = pv.CategoriaProg)
							LEFT JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cp.CodUnidadEjec)
							LEFT JOIN pv_actividades a ON (a.IdActividad = cp.IdActividad)
							LEFT JOIN pv_proyectos py ON (py.IdProyecto = a.IdProyecto)
							LEFT JOIN pv_subprogramas spr ON (spr.IdSubPrograma = py.IdSubPrograma)
							LEFT JOIN pv_programas pr ON (pr.IdPrograma = spr.IdPrograma)
							LEFT JOIN pv_subsector ss ON (ss.IdSubSector = pr.IdSubSector)
						WHERE
							do.Anio = '".$Anio."' AND
							do.CodOrganismo = '".$CodOrganismo."' AND
							do.CodProveedor = '".$field_orden['CodProveedor']."' AND
							do.CodTipoDocumento = 'OS' AND
							do.NroDocumento = '".$NroOrden."'
						ORDER BY Ejercicio, CodPresupuesto, CodFuente, cod_partida";
				$query_partidas = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
				while ($field_partidas = mysql_fetch_array($query_partidas)) {
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
					list($MontoAjustado, $MontoCompromiso, $PreCompromiso, $CotizacionesAsignadas) = disponibilidadPartida2($field_partidas['Ejercicio'], $field_orden['CodOrganismo'], $field_partidas['cod_partida'], $field_partidas['CodPresupuesto'], $field_partidas['CodFuente']);
					if ($field_orden['Estado'] == 'PR') {
						$PreCompromiso -= $field_partidas['Monto'];
					}
					elseif ($field_orden['Estado'] == 'RV' || $field_orden['Estado'] == 'AP') {
						$MontoCompromiso -= $field_partidas['Monto'];
					}
					$MontoPendiente = $PreCompromiso + $CotizacionesAsignadas;
					$MontoDisponible = $MontoAjustado - $MontoCompromiso;
					$MontoDisponibleReal = $MontoAjustado - ($MontoCompromiso + $MontoPendiente);
					##	valido
					if (($MontoDisponible - $field_partidas['Monto']) <= 0) $style = "style='background-color:#F8637D;'";
					elseif(($MontoDisponibleReal - $field_partidas['Monto']) <= 0) $style = "style='background-color:#FFC;'";
					else $style = "style='background-color:#D0FDD2;'";
			        ?>
			        <tr class="trListaBody" <?=$style?>>
			            <td align="center"><?=$field_partidas['CodFuente']?></td>
			            <td align="center">
			                <input type="hidden" name="cod_partida" value="<?=$field_partidas['cod_partida']?>" />
			                <input type="hidden" name="CodCuenta" value="<?=$field_partidas['CodCuenta']?>" />
			                <input type="hidden" name="CodCuentaPub20" value="<?=$field_partidas['CodCuentaPub20']?>" />
			                <input type="hidden" name="Monto" value="<?=$field_partidas['Monto']?>" />
			                <input type="hidden" name="MontoAjustado" value="<?=$MontoAjustado?>" />
			                <input type="hidden" name="MontoCompromiso" value="<?=$MontoCompromiso?>" />
			                <input type="hidden" name="PreCompromiso" value="<?=$PreCompromiso?>" />
			                <input type="hidden" name="CotizacionesAsignadas" value="<?=$CotizacionesAsignadas?>" />
			                <input type="hidden" name="MontoDisponible" value="<?=$MontoDisponible?>" />
			                <input type="hidden" name="MontoDisponibleReal" value="<?=$MontoDisponibleReal?>" />
							<input type="hidden" name="MontoPendiente" value="<?=$MontoPendiente?>" />
							<input type="hidden" name="partidasCodFuente" value="<?=$field_partidas['CodFuente']?>" />
							<input type="hidden" name="partidasCategoriaProg" value="<?=$field_partidas['CategoriaProg']?>" />
							<?=$field_partidas['cod_partida']?>
			            </td>
			            <td><?=htmlentities($field_partidas['denominacion'])?></td>
			            <td align="right"><?=number_format($field_partidas['Monto'], 2, ',', '.')?></td>
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
	function orden_servicio_rechazar(form) {
		var MotRechazo = $("#MotRechazo").val();
		if (MotRechazo.trim() == "") {
			$("#cajaModal").dialog({
				buttons: {
					"Si": function() {
						$(this).dialog("close");
						orden_servicio(form, 'rechazar');
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
			orden_servicio(form, 'rechazar');
		}
	}
	function quitarLineaOrdenServicio(boton, detalle, form) {
		/*
		.- boton	-> referencia del boton (objeto)
		.- detalle	-> sufijo de los campos de la lista
		*/
		boton.disabled = true;
		var can = "can_" + detalle;
		var sel = "sel_" + detalle;	
		var lista = "lista_" + detalle;
		if (document.getElementById(sel).value == "") cajaModal("Debe seleccionar una linea", "error", 400);
		else {
			var candetalle = new Number(document.getElementById(can).value); candetalle--;
			document.getElementById(can).value = candetalle;
			var seldetalle = document.getElementById(sel).value;
			var listaDetalles = document.getElementById(lista);
			var tr = document.getElementById(seldetalle);
			listaDetalles.removeChild(tr);
			document.getElementById(sel).value = "";
			setMontosOrdenServicio(form);
		}
		boton.disabled = false;
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
				$('#btPresupuesto').attr('href', '../lib/listas/gehen.php?anz=lista_pv_presupuesto&filtrar=default&FlagCategoriaProg=S&fCodOrganismo='+$('#CodOrganismo').val()+'&fEjercicio='+$('#Ejercicio').val()+'&fCodDependencia='+$('#CodDependencia').val()+'&campo1=Ejercicio&campo2=CodPresupuesto&campo3=CategoriaProg&ventana=lg_requerimiento&iframe=true&width=100%&height=425');
				$('#aSelCategoriaProg').attr('href', '../lib/listas/gehen.php?anz=lista_pv_presupuesto&filtrar=default&FlagCategoriaProg=S&fCodOrganismo='+$('#CodOrganismo').val()+'&fEjercicio='+$('#Ejercicio').val()+'&fCodDependencia='+$('#CodDependencia').val()+'&campo1=detallesCategoriaProg&campo2=detallesEjercicio&campo3=detallesCodPresupuesto&ventana=selListadoListaParentRequerimiento&seldetalle=sel_detalles&iframe=true&width=100%&height=425');
			}
		});
	}
</script>