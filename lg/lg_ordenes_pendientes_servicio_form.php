<?php
//	consulto cotizacion
$sql = "SELECT
			c.CodOrganismo,
			c.CodProveedor,
			c.NomProveedor,
			c.CodFormaPago,
			c.Numero,
			pr.CodTipoServicio,
			pr.CodTipoPago,
			r.CodCentroCosto,
			r.CodDependencia,
			r.CodAlmacen,
			r.Comentarios,
            r.Ejercicio,
            r.CodPresupuesto,
            r.CodFuente,
            pv.CategoriaProg
		FROM
			lg_cotizacion c
			INNER JOIN mastproveedores pr ON (c.CodProveedor = pr.CodProveedor)
			INNER JOIN lg_requerimientos r ON (c.CodRequerimiento = r.CodRequerimiento)
            LEFT JOIN pv_presupuesto pv ON (pv.CodOrganismo = r.CodOrganismo AND pv.CodPresupuesto = r.CodPresupuesto)
		WHERE c.NroCotizacionProv = '".$registro."'
		GROUP BY c.NroCotizacionProv";
$query_orden = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
if (mysql_num_rows($query_orden)) $field_orden = mysql_fetch_array($query_orden);
//	valores default
$field_orden['CodOrganismo'] = $_SESSION["ORGANISMO_ACTUAL"];
$field_orden['PreparadaPor'] = $_SESSION["CODPERSONA_ACTUAL"];
$field_orden['NomPreparadaPor'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
$field_orden['Anio'] = substr($Ahora, 0, 4);
$field_orden['FechaDocumento'] = substr($Ahora, 0, 10);
$field_orden['FechaPreparacion'] = substr($Ahora, 0, 10);
$field_orden['PlazoEntrega'] = $_PARAMETRO['DIAENTOC'];
$field_orden['FechaEntrega'] = formatFechaAMD(getFechaFin(formatFechaDMA(substr($Ahora, 0, 10)), $_PARAMETRO['DIAENTOC']));
$field_orden['DiasPago'] = $field_orden['PlazoEntrega'];
$field_orden['FechaValidoDesde'] = substr($Ahora, 0, 10);
$field_orden['FechaValidoHasta'] = $field_orden['FechaEntrega'];
##
if (!afectaTipoServicio($field_orden['CodTipoServicio'])) { $dFlagExonerado = "disabled"; $cFlagExonerado = "checked"; }
$FactorImpuesto = getPorcentajeIVA($field_orden['CodTipoServicio']);
$CodCentroCosto = getVar3("SELECT CodCentrocosto FROM ac_mastcentrocosto WHERE Codigo = '$_PARAMETRO[CCOSTOCOMPRA]'");
//	------------------------------------
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo">Generar Orden de Servicio</td>
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

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lg_ordenes_pendientes_lista" method="POST" onsubmit="return orden_servicio(this, 'nuevo');">
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
<input type="hidden" name="fCodFormaPago" id="fCodFormaPago" value="<?=$fCodFormaPago?>" />
<input type="hidden" name="fCodProveedor" id="fCodProveedor" value="<?=$fCodProveedor?>" />
<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
<input type="hidden" id="NroCotizacionProv" value="<?=$registro?>" />
<input type="hidden" id="Numero" value="<?=$field_orden['Numero']?>" />
<input type="hidden" id="Anio" value="<?=$field_orden['Anio']?>" />
<input type="hidden" id="NroOrden" />
<input type="hidden" id="GenerarPendiente" value="S" />
<input type="hidden" id="AnioOrden" value="<?=$field_orden['Anio']?>" />
<input type="hidden" id="FlagCotizacion" value="S" />

<div id="tab1" style="display:block;">
	<table width="1100" class="tblForm">
		<tr>
			<td class="tagForm" width="150">N&uacute;mero:</td>
			<td>
	        	<input type="text" id="NroInterno" style="width:100px;" class="codigo" disabled="disabled" />
				<input type="text" id="FechaDocumento" value="<?=formatFechaDMA($field_orden['FechaDocumento'])?>" maxlength="10" style="width:60px;" class="datepicker codigo" onkeyup="setFechaDMA(this);" onchange="setPresupuesto($('#CodOrganismo').val(), $(this).val(), $('#CodPresupuesto'), $('#Anio')); setMontosOrdenServicio(document.getElementById('frm_detalles'));" <?=$disabled_ver?> />
			</td>
			<td class="tagForm" width="150">Estado:</td>
			<td>
				<input type="hidden" id="Estado" value="PR" />
				<input type="text" style="width:100px;" class="codigo" value="<?=printValoresGeneral("ESTADO-SERVICIO", "PR")?>" disabled="disabled" />
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Organismo:</td>
			<td>
				<select id="CodOrganismo" style="width:300px;">
					<?=getOrganismos($field_orden['CodOrganismo'], 3)?>
				</select>
			</td>
	    	<td colspan="2" class="divFormCaption">Monto del Servicio</td>
		</tr>
	    <tr>
			<td class="tagForm">* Dependencia:</td>
			<td>
				<select id="CodDependencia" style="width:300px;">
					<?=getDependencias($field_orden['CodDependencia'], $field_orden['CodOrganismo'], 3)?>
				</select>
			</td>
	        <td class="tagForm">Monto Afecto:</td>
			<td>
	        	<input type="text" id="MontoOriginal" value="<?=number_format($MontoOriginal, 2, ',', '.')?>" style="width:150px; text-align:right;" class="disabled" disabled="disabled" />
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Centro de Costo:</td>
			<td>
				<select id="CodCentroCosto" style="width:300px;">
					<?=loadSelectDependiente("ac_mastcentrocosto", "CodCentroCosto", "Descripcion", "CodDependencia", $field_orden['CodCentroCosto'], $field_orden['CodDependencia'], 0)?>
				</select>
			</td>
	        <td class="tagForm">Monto No Afecto:</td>
			<td>
	        	<input type="text" id="MontoNoAfecto" value="<?=number_format($MontoNoAfecto, 2, ',', '.')?>" style="width:150px; text-align:right;" class="disabled" disabled="disabled" />
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Proveedor:</td>
			<td class="gallery clearfix">
	            <input type="text" id="CodProveedor" style="width:50px;" value="<?=$field_orden['CodProveedor']?>" disabled="disabled" />
				<input type="text" id="NomProveedor" style="width:235px;" value="<?=$field_orden['NomProveedor']?>" disabled="disabled" />
	            <a href="../lib/listas/listado_personas.php?filtrar=default&cod=CodProveedor&nom=NomProveedor&EsProveedor=S&ventana=selListadoOrdenServicioPersona&iframe=true&width=950&height=525" rel="prettyPhoto[iframe1]" id="btProveedor" style=" <?=$display_modificar?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
	        </td>
	        <td class="tagForm">(+/-) Impuestos:</td>
			<td>
	        	<input type="text" id="MontoIva" value="<?=number_format($MontoIva, 2, ',', '.')?>" style="width:150px; text-align:right;" class="disabled" disabled="disabled" />
	        </td>
		</tr>
	    <tr>
			<td class="tagForm">* Tipo de Servicio:</td>
			<td>
	        	<input type="hidden" id="FactorImpuesto" value="<?=$FactorImpuesto?>" />
	            <select id="CodTipoServicio" style="width:150px;">
	                <?=loadSelect("masttiposervicio", "CodTipoServicio", "Descripcion", $field_orden['CodTipoServicio'], 1)?>
	            </select>
	        </td>
	        <td class="tagForm">Monto Total:</td>
			<td>
	        	<input type="text" id="TotalMontoIva" value="<?=number_format($TotalMontoIva, 2, ',', '.')?>" style="width:150px; text-align:right; font-size:12px; font-weight:bold;" class="disabled" disabled="disabled" />
	        </td>
		</tr>
	    <tr>
			<td class="tagForm">* Forma de Pago:</td>
			<td>
	            <select id="CodFormaPago" style="width:150px;">
	                <?=loadSelect("mastformapago", "CodFormaPago", "Descripcion", $field_orden['CodFormaPago'], 0)?>
	            </select>
	        </td>
	        <td class="tagForm">Monto Pagado:</td>
			<td>
	        	<input type="text" id="MontoGastado" value="<?=number_format($MontoGastado, 2, ',', '.')?>" style="width:150px; text-align:right;" class="disabled" disabled="disabled" />
	        </td>
		</tr>
	    <tr>
			<td class="tagForm">* Tipo de Pago:</td>
			<td>
	            <select id="CodTipoPago" style="width:150px;">
	                <?=loadSelect("masttipopago", "CodTipoPago", "TipoPago", $field_orden['CodTipoPago'], 0)?>
	            </select>
	        </td>
	        <td class="tagForm">Monto Pendiente:</td>
			<td>
	        	<input type="text" id="MontoPendiente" value="<?=number_format($MontoPendiente, 2, ',', '.')?>" style="width:150px; text-align:right; font-size:12px; font-weight:bold;" class="disabled" disabled="disabled" />
	        </td>
		</tr>
	    <tr>
			<td class="tagForm">* Plazo de Entrega:</td>
			<td>
	        	<input type="text" id="PlazoEntrega" value="<?=$field_orden['PlazoEntrega']?>" maxlength="10" style="width:20px;" /> <em>(dias)</em>
	        	<input type="text" id="FechaEntrega" value="<?=formatFechaDMA($field_orden['FechaEntrega'])?>" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" />
	        </td>
	    	<td colspan="2" class="divFormCaption">Informaci&oacute;n Adicional</td>
		</tr>
	    <tr>
			<td class="tagForm">* Dias para Pagar:</td>
			<td>
	        	<input type="text" id="DiasPago" value="<?=$field_orden['DiasPago']?>" maxlength="10" style="width:20px;" <?=$disabled_ver?> /> <em>(dias)</em>
	        </td>
	        <td class="tagForm">Ingresado Por:</td>
	        <td>
	            <input type="hidden" id="PreparadaPor" value="<?=$field_orden['PreparadaPor']?>" />
	            <input type="text" id="NomPreparadaPor" value="<?=htmlentities($field_orden['NomPreparadaPor'])?>" style="width:200px;" disabled="disabled" />
	            <input type="text" id="FechaPreparacion" value="<?=formatFechaDMA(substr($field_orden['FechaPreparacion'], 0, 10))?>" style="width:60px;" disabled="disabled" />
	        </td>
		</tr>
	    <tr>
			<td class="tagForm">* Desde:</td>
			<td>
	        	<input type="text" id="FechaValidoDesde" value="<?=formatFechaDMA($field_orden['FechaValidoDesde'])?>" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" <?=$disabled_ver?> />
	        </td>
	        <td class="tagForm">Revisado Por:</td>
	        <td>
	            <input type="hidden" id="RevisadoPor" value="<?=$field_orden['RevisadaPor']?>" />
	            <input type="text" id="NomRevisadoPor" value="<?=htmlentities($field_orden['NomRevisadaPor'])?>" style="width:200px;" disabled="disabled" />
	            <input type="text" id="FechaRevision" value="<?=formatFechaDMA(substr($field_orden['FechaRevision'], 0, 10))?>" style="width:60px;" disabled="disabled" />
	        </td>
		</tr>
	    <tr>
			<td class="tagForm">* Hasta:</td>
			<td>
	        	<input type="text" id="FechaValidoHasta" value="<?=formatFechaDMA($field_orden['FechaValidoHasta'])?>" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" <?=$disabled_ver?> />
	        </td>
	        <td class="tagForm">Aprobado Por:</td>
	        <td>
	            <input type="hidden" id="AprobadaPor" value="<?=$field_orden['AprobadaPor']?>" />
	            <input type="text" id="NomAprobadaPor" value="<?=htmlentities($field_orden['NomAprobadaPor'])?>" style="width:200px;" disabled="disabled" />
	            <input type="text" id="FechaAprobacion" value="<?=formatFechaDMA(substr($field_orden['FechaAprobacion'], 0, 10))?>" style="width:60px;" disabled="disabled" />
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
	            <a href="../lib/listas/gehen.php?anz=lista_pv_presupuesto&filtrar=default&FlagCategoriaProg=S&fCodOrganismo=<?=$field_orden['CodOrganismo']?>&fEjercicio=<?=$field_orden['Ejercicio']?>&fCodDependencia=<?=$field_orden['CodDependencia']?>&campo1=Ejercicio&campo2=CodPresupuesto&campo3=CategoriaProg&ventana=lg_requerimiento&iframe=true&width=100%&height=425" rel="prettyPhoto[iframe4]" style="display:none;" id="btPresupuesto">
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
	            <select name="CodFuente" id="CodFuente" style="width:250px;" onchange="$('.CodFuente').val(this.value);" <?=$disabled_ver?>>
	                <?=loadSelect2("pv_fuentefinanciamiento","CodFuente","Denominacion",$field_orden['CodFuente'],11)?>
	            </select>
	        </td>
	        <td colspan="2"></td>
	    </tr>
		<tr>
	    	<td colspan="4" class="divFormCaption">Observaciones</td>
	    </tr>
		<tr>
			<td class="tagForm">Descripci&oacute;n:</td>
			<td colspan="3"><textarea id="Descripcion" style="width:95%; height:30px;"><?=htmlentities($field_orden['Comentarios'])?></textarea></td>
		</tr>
		<tr>
			<td class="tagForm">Descripci&oacute;n Detallada:</td>
			<td colspan="3"><textarea id="DescAdicional" style="width:95%; height:30px;"></textarea></td>
		</tr>
		<tr>
			<td class="tagForm">Observaciones:</td>
			<td colspan="3"><textarea id="Observaciones" style="width:95%; height:50px;"></textarea></td>
		</tr>
		<tr>
			<td class="tagForm">Razon Rechazo:</td>
			<td colspan="3"><textarea id="MotRechazo" style="width:95%; height:30px;" disabled></textarea></td>
		</tr>
		<tr>
			<td class="tagForm">&Uacute;ltima Modif.:</td>
			<td>
				<input type="text" size="30" class="disabled" disabled="disabled" />
				<input type="text" size="25" class="disabled" disabled="disabled" />
			</td>
		</tr>
	</table>
	<center> 
	<input type="submit" value="Guardar" />
	<input type="button" value="Cancelar" onclick="this.form.submit();" />
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
	            <a id="aSelCCosto" href="../lib/listas/listado_centro_costos.php?filtrar=default&cod=CodCentroCosto&nom=NomCentroCosto&ventana=selListadoLista&seldetalle=sel_detalles&filtroDependencia=S&iframe=true&width=1050&height=500" rel="prettyPhoto[iframe2]" style="display:none;"></a>
	            <input type="button" class="btLista" id="btSelCCosto" value="Sel. C.Costo" onclick="validarAbrirLista('sel_detalles', 'aSelCCosto');" />
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
					c.Cantidad AS CantidadPedida,
					c.PrecioUnit,
					c.DescuentoPorcentaje,
					c.DescuentoFijo,
					c.FlagExonerado,
					c.Total,
					c.FechaLimite,
					c.CotizacionSecuencia,
					c.CodUnidadCompra,
					c.CantidadCompra,
					c.FlagExonerado,
					rd.CodItem,
					rd.CommoditySub,
					rd.Descripcion,
					rd.CodUnidad,
					rd.CodCuenta,
					rd.CodCuentaPub20,
					rd.cod_partida,
					rd.Comentarios,
					rd.CodRequerimiento,
					rd.Secuencia,
					rd.CantidadPedida AS CantidadRequerimiento,
					rd.Activo AS NroActivo,
                    rd.CodPresupuesto,
                    rd.CodFuente,
                    rd.Ejercicio,
                    rd.CodCentroCosto,
                    pv.CategoriaProg,
                    cc.Codigo AS NomCentroCosto
				FROM
					lg_cotizacion c
					INNER JOIN lg_requerimientosdet rd ON (c.CodRequerimiento = rd.CodRequerimiento AND
														   c.Secuencia = rd.Secuencia)
                    LEFT JOIN pv_presupuesto pv ON (pv.CodOrganismo = rd.CodOrganismo AND pv.CodPresupuesto = rd.CodPresupuesto)
                    LEFT JOIN ac_mastcentrocosto cc ON (cc.CodCentroCosto = rd.CodCentroCosto)
				WHERE
					c.NroCotizacionProv = '".$registro."' AND
					c.FlagAsignado = 'S'
				ORDER BY c.Secuencia";
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
					<textarea name="Descripcion" style="height:30px;" class="cell" readonly="readonly"><?=htmlentities($field_detalles['Descripcion'])?></textarea>
				</td>
				<td align="center">
	            	<input type="text" name="CantidadPedida" class="cell" style="text-align:right;" value="<?=number_format($field_detalles['CantidadCompra'], 2, ',', '.')?>" onBlur="numeroBlur(this);" onFocus="numeroFocus(this);" onchange="setMontosOrdenServicio(this.form);" />
	                <input type="hidden" name="CodUnidadRec" value="<?=$field_detalles['CodUnidad']?>" />
	                <input type="hidden" name="CantidadRec" value="<?=$field_detalles['CantidadPedida']?>" />
	            </td>
				<td align="center">
	            	<input type="text" name="PrecioUnit" class="cell" style="text-align:right;" value="<?=number_format($field_detalles['PrecioUnit'], 2, ',', '.')?>" onBlur="numeroBlur(this);" onFocus="numeroFocus(this);" onchange="setMontosOrdenServicio(this.form);" />
	            </td>
				<td align="center">
	            	<input type="checkbox" name="FlagExonerado" class="FlagExonerado" onchange="setMontosOrdenServicio(this.form);" <?=chkFlag($field_detalles['FlagExonerado'])?> <?=$dFlagExonerado?> />
	            </td>
				<td align="center">
	            	<input type="text" name="Total" class="cell2" style="text-align:right;" value="<?=number_format($field_detalles['Total'], 2, ',', '.')?>" readonly="readonly" />
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
	            	<input type="text" name="FechaEsperadaTermino" value="<?=formatFechaDMA($field_detalles['FechaLimite'])?>" maxlength="10" style="text-align:center;" class="datepicker cell" onkeyup="setFechaDMA(this);" />
	            </td>
				<td align="center">
	            	<input type="text" name="FechaTermino" value="<?=formatFechaDMA($field_detalles['FechaLimite'])?>" maxlength="10" style="text-align:center;" class="datepicker cell" onkeyup="setFechaDMA(this);" />
	            </td>
				<td align="right">
					<?=number_format($field_detalles['CantidadRecibida'], 2, ',', '.')?>
				</td>
    			<td align="center">
                    <input type="text" name="NomCentroCosto" id="NomCentroCosto_<?=$nrodetalles?>" class="cell2" style="text-align:center;" maxlength="4" value="<?=$field_detalles['NomCentroCosto']?>" readonly />
    				<input type="hidden" name="CodCentroCosto" id="CodCentroCosto_<?=$nrodetalles?>" value="<?=$field_detalles['CodCentroCosto']?>" />
    			</td>
				<td align="center">
					<input type="text" name="NroActivo" value="<?=($field_detalles['NroActivo'])?>" class="cell" style="text-align:center;" />
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
					<textarea name="Comentarios" style="height:30px;" class="cell"><?=htmlentities($field_detalles['Comentarios'])?></textarea>
					<input type="hidden" name="CodRequerimiento" value="<?=$field_detalles['CodRequerimiento']?>" />
					<input type="hidden" name="Secuencia" value="<?=$field_detalles['Secuencia']?>" />
					<input type="hidden" name="CotizacionSecuencia" value="<?=$field_detalles['CotizacionSecuencia']?>" />
					<input type="hidden" name="CantidadRequerimiento" value="<?=$field_detalles['CantidadRequerimiento']?>" />
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
	<table width="1500" class="tblLista">
		<thead>
		<tr>
	        <th width="100">C&oacute;digo</th>
	        <th>Raz&oacute;n Social</th>
	        <th width="75">Cantidad</th>
	        <th width="100">Precio Unit.</th>
	        <th width="100">Precio Unit./Imp.</th>
	        <th width="100">Monto Total</th>
	        <th width="30">Asig.</th>
	        <th width="75">Dias Entrega</th>
	        <th width="75">Fecha Entrega</th>
	        <th width="100">Cotizaci&oacute;n #</th>
	        <th width="300">Observaciones</th>
	    </tr>
	    </thead>
	    
	</table>
	</div>
	<div style="width:1100px;" class="divFormCaption">Requerimientos</div>
	<div style="overflow:scroll; width:1100px; height:200px;">
	<table width="1500" class="tblLista">
		<thead>
		<tr>
	        <th width="75">Requerimiento</th>
	        <th width="50">Req. Linea</th>
	        <th width="75">Cantidad</th>
	        <th width="75">Fecha Pedida</th>
	        <th width="75">Fecha Aprobaci&oacute;n</th>
	        <th>Comentarios</th>
	        <th width="300">Preparado Por</th>
	    </tr>
	    </thead>
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
	        <th width="75">Fecha</th>
	        <th>Comentarios</th>
	        <th width="100">Monto Afecto</th>
	        <th width="100">Monto Total</th>
	        <th width="75">Estado</th>
	        <th width="100">Voucher</th>
	    </tr>
	    </thead>
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
	        	<a id="a_disponibilidad" href="pagina.php?iframe=true" rel="prettyPhoto[iframe3]" style="display:none;"></a>
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
	    </tbody>
	</table>
	</div>
	</form>
	</center>
</div>

<script language="javascript">
$(document).ready(function(){
	setMontosOrdenServicio(document.getElementById("frm_detalles"));
	mostrarTabDistribucionOrden(false);
});
</script>