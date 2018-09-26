<?php
//	------------------------------------
if ($opcion == "nuevo") {
	$field['Estado'] = "PR";
	$field['CodOrganismo'] = $_SESSION["ORGANISMO_ACTUAL"];
	$field['CodDependencia'] = $_SESSION["DEPENDENCIA_ACTUAL"];
	$field['CodCentroCosto'] = $_SESSION["CCOSTO_ACTUAL"];
	$field['CodBeneficiario'] = $_SESSION["CODPERSONA_ACTUAL"];
	$field['NomBeneficiario'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
	$field['CodPersonaPagar'] = $_SESSION["CODPERSONA_ACTUAL"];
	$field['NomPersonaPagar'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
	$field['PreparadoPor'] = $_SESSION["CODPERSONA_ACTUAL"];
	$field['NomPreparadoPor'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
	$field['FlagCajaChica'] = "C";
	$field['CodClasificacion'] = "CC";
	$field['CodTipoPago'] = "02";
	$field['Periodo'] = $AnioActual;
	$field['FechaPreparacion'] = $FechaActual;
	//	presupuesto
	$sql = "SELECT CodPresupuesto 
			FROM pv_presupuesto 
			WHERE Ejercicio = '".$AnioActual."' AND CodOrganismo = '".$field['CodOrganismo']."'";
	$query_presupuesto = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query_presupuesto)) $field_presupuesto = mysql_fetch_array($query_presupuesto);
	$field['CodPresupuesto'] = $field_presupuesto['CodPresupuesto'];
	##
	$accion = "nuevo";
	$titulo = "Agregar Caja Chica";
	$label_submit = "Guardar";
	$disabled_nuevo = "disabled";
	$disabled_anular = "disabled";
	$disabled_aprobar = "disabled";
	$disabled_selects = "";
	$opt_ver = 0;
	$clkCancelar = "document.getElementById('frmentrada').submit();";
	$mostrarTabDistribucion = "mostrarTabDistribucionCajaChica();";
}
elseif ($opcion == "modificar" || $opcion == "ver" || $opcion == "aprobar" || $opcion == "anular") {
	list($FlagCajaChica, $Periodo, $NroCajaChica) = split("[_]", $sel_registros);
	//	consulto datos generales
	$sql = "SELECT
				cc.*,
				p1.NomCompleto AS NomBeneficiario,
				p2.NomCompleto AS NomPreparadoPor,
				p3.NomCompleto AS NomAprobadoPor
			FROM
				ap_cajachica cc
				INNER JOIN mastpersonas p1 ON (p1.CodPersona = cc.CodBeneficiario)
				LEFT JOIN mastpersonas p2 ON (p2.CodPersona = cc.PreparadoPor)
				LEFT JOIN mastpersonas p3 ON (p3.CodPersona = cc.AprobadoPor)
			WHERE
				cc.FlagCajaChica = '".$FlagCajaChica."' AND
				cc.Periodo = '".$Periodo."' AND
				cc.NroCajaChica = '".$NroCajaChica."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query)) $field = mysql_fetch_array($query);
	if ($opcion == "modificar") {
		$accion = "modificar";
		$titulo = "Actualizar Caja Chica";
		$disabled_modificar = "disabled";
		$disabled_anular = "disabled";
		$disabled_aprobar = "disabled";
		$disabled_selects = "";
		$display_modificar = "display:none;";
		$opt_ver = 0;
		$label_submit = "Modificar";
		$clkCancelar = "document.getElementById('frmentrada').submit();";
		$mostrarTabDistribucion = "mostrarTabDistribucionCajaChica();";
	}
	elseif ($opcion == "aprobar") {
		$field['AprobadoPor'] = $_SESSION["CODPERSONA_ACTUAL"];
		$field['NomAprobadoPor'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
		$field['FechaAprobacion'] = $FechaActual;
		##
		$accion = "aprobar";
		$titulo = "Aprobar Caja Chica";
		$disabled_nuevo = "disabled";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$disabled_anular = "disabled";
		$disabled_conceptos = "disabled";
		$disabled_selects = "";
		$display_modificar = "display:none;";
		$display_ver = "display:none;";
		$opt_ver = 1;
		$label_submit = "Aprobar";
		$clkCancelar = "document.getElementById('frmentrada').submit();";
	}
	elseif ($opcion == "anular") {
		$accion = "anular";
		$titulo = "Anular Caja Chica";
		$disabled_nuevo = "disabled";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$disabled_anular = "disabled";
		$disabled_conceptos = "disabled";
		$disabled_aprobar = "disabled";
		$disabled_selects = "disabled";
		$display_modificar = "display:none;";
		$display_ver = "display:none;";
		$opt_ver = 1;
		$label_submit = "Anular";
		$clkCancelar = "document.getElementById('frmentrada').submit();";
	}
	elseif ($opcion == "ver") {
		$titulo = "Ver Registro";
		$disabled_nuevo = "disabled";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$disabled_anular = "disabled";
		$disabled_conceptos = "disabled";
		$disabled_aprobar = "disabled";
		$disabled_selects = "disabled";
		$display_modificar = "display:none;";
		$display_ver = "display:none;";
		$display_submit = "display:none;";
		$opt_ver = 1;
		$clkCancelar = "document.getElementById('frmentrada').submit();";
	}
}
//	
$MontoAutorizado = getVar("ap_cajachicaautorizacion", "Monto", "CodOrganismo", $field['CodOrganismo'], "CodEmpleado", $field['CodBeneficiario']);
//	------------------------------------
$_width = 900;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<center>
<div class="ui-widget" id="nocumple" style="display:none;">
    <div class="ui-state-error ui-corner-all" style="width:<?=$_width?>px; text-align:left;">
        <p>
        <span class="ui-icon ui-icon-alert" style="float:left;"></span>
        <strong>El empleado NO tiene Monto autorizado para generar Gastos de Caja Chica</strong>
        </p>
    </div>
    <br />
</div>
<div class="ui-widget" id="excede" style="display:none;">
    <div class="ui-state-highlight ui-corner-all" style="width:708px; text-align:left;">
        <p>
        <span class="ui-icon ui-icon-info" style="float: left;"></span>
        <strong>Los Gastos de Caja Chica exceden el <?=$_PARAMETRO['REPCC']?>% del Monto Autorizado</strong>
        </p>
    </div>
</div>
</center>

<table width="<?=$_width?>" align="center" cellpadding="0" cellspacing="0">
    <tr>
        <td>
            <div class="header">
            <ul id="tab">
            <!-- CSS Tabs -->
            <li id="li1" onclick="currentTab('tab', this);" class="current"><a href="#" onclick="mostrarTab('tab', 1, 3);">Informaci&oacute;n General</a></li>
            <li id="li2" onclick="currentTab('tab', this);"><a href="#" onclick="mostrarTab('tab', 2, 3);">Conceptos del Gasto</a></li>
            <li id="li3" onclick="currentTab('tab', this);"><a href="#" onclick="<?=$mostrarTabDistribucion?>mostrarTab('tab', 3, 3);">Dist. Presupuestaria</a></li>
            </ul>
            </div>
        </td>
    </tr>
</table>

<div id="tab1" style="display:block;">
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=ap_caja_chica_lista" method="POST" enctype="multipart/form-data" onsubmit="return caja_chica(this, '<?=$accion?>');" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
<input type="hidden" name="fCodDependencia" id="fCodDependencia" value="<?=$fCodDependencia?>" />
<input type="hidden" name="fCodCentroCosto" id="fCodCentroCosto" value="<?=$fCodCentroCosto?>" />
<input type="hidden" name="fFechaPreparacion" id="fFechaPreparacion" value="<?=$fFechaPreparacion?>" />
<input type="hidden" name="FlagCajaChica" id="FlagCajaChica" value="<?=$field['FlagCajaChica']?>" />
<input type="hidden" name="Anio" id="Anio" value="<?=$field['Periodo']?>" />
<input type="hidden" name="CodPresupuesto" id="CodPresupuesto" value="<?=$field['CodPresupuesto']?>" />

<table width="<?=$_width?>" class="tblForm">
	<tr>
    	<td colspan="4" class="divFormCaption">Datos de la Caja Chica</td>
    </tr>
    <tr>
		<td class="tagForm" width="125">Beneficiario:</td>
		<td class="gallery clearfix">
            <input type="hidden" name="CodBeneficiario" id="CodBeneficiario" value="<?=$field['CodBeneficiario']?>" />
            <input type="text" name="NomBeneficiario" id="NomBeneficiario" value="<?=$field['NomBeneficiario']?>" style="width:295px;" disabled />
            <a href="../lib/listas/gehen.php?anz=lista_empleados&filtrar=default&ventana=caja_chica_beneficiario&iframe=true&width=950&height=430" rel="prettyPhoto[iframe1]" style=" <?=$display_modificar?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
		<td class="tagForm" width="125">Caja Chica:</td>
		<td>
        	<input type="text" name="NroCajaChica" id="NroCajaChica" value="<?=$field['NroCajaChica']?>" style="width:40px;" class="codigo" readonly="readonly" /> -
            <input type="text" name="Periodo" id="Periodo" value="<?=$field['Periodo']?>" style="width:35px; text-align:center;" class="codigo" readonly="readonly" />
		</td>
	</tr>
    <tr>
		<td class="tagForm">Cheque a Nombre de:</td>
		<td class="gallery clearfix">
            <input type="hidden" name="CodPersonaPagar" id="CodPersonaPagar" value="<?=$field['CodPersonaPagar']?>" />
            <input type="text" name="NomPersonaPagar" id="NomPersonaPagar" value="<?=$field['NomPersonaPagar']?>" style="width:295px;" readonly="readonly" />
            <a href="../lib/listas/gehen.php?anz=lista_empleados&filtrar=default&ventana=&cod=CodPersonaPagar&nom=NomPersonaPagar&iframe=true&width=950&height=430" rel="prettyPhoto[iframe2]" style=" <?=$display_ver?>" class="aEditable">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
		<td class="tagForm">Estado:</td>
		<td>
        	<input type="hidden" name="Estado" id="Estado" value="<?=$field['Estado']?>" />
        	<input type="text" value="<?=strtoupper(printValores("ESTADO-CAJACHICA", $field['Estado']))?>" style="width:93px;" class="codigo" disabled />
		</td>
	</tr>
    <tr>
		<td class="tagForm">Organismo:</td>
		<td>
            <select name="CodOrganismo" id="CodOrganismo" style="width:300px;" class="iEditable" onChange="loadSelect($('#CodDependencia'), 'tabla=dependencia_filtro&opcion='+$('#CodOrganismo').val(), 1, 'CodCentrocosto');" <?=$disabled_selects?>>
                <?=loadSelect2("mastorganismos", "CodOrganismo", "Organismo", $field['CodOrganismo'], 0)?>
            </select>
		</td>
        <td colspan="2" class="divFormCaption">Montos Totales</td>
	</tr>
    <tr>
		<td class="tagForm">Dependencia:</td>
		<td>
            <select name="CodDependencia" id="CodDependencia" style="width:300px;" class="iEditable" onChange="loadSelect($('#CodCentroCosto'), 'tabla=centro_costo&opcion='+$('#CodDependencia').val(), 1);" <?=$disabled_selects?>>
                <?=loadSelect2("mastdependencias","CodDependencia","Dependencia",$field['CodDependencia'],$opt_ver,array("CodOrganismo"),array($field['CodOrganismo']))?>
            </select>
		</td>
		<td class="tagForm">Monto Afecto:</td>
		<td>
        	<input type="text" name="MontoAfecto" id="MontoAfecto" value="<?=number_format($field['MontoAfecto'], 2, ',', '.')?>" style="width:100px; text-align:right;" class="currency" readonly="readonly" />
		</td>
	</tr>
    <tr>
		<td class="tagForm">Centro de Costo:</td>
		<td>
            <select name="CodCentroCosto" id="CodCentroCosto" style="width:300px;" class="iEditable" <?=$disabled_selects?>>
                <?=loadSelect2("ac_mastcentrocosto","CodCentroCosto","Descripcion",$field['CodCentroCosto'],$opt_ver,array("CodDependencia"),array($field['CodDependencia']))?>
            </select>
		</td>
		<td class="tagForm">Monto No Afecto:</td>
		<td>
        	<input type="text" name="MontoNoAfecto" id="MontoNoAfecto" value="<?=number_format($field['MontoNoAfecto'], 2, ',', '.')?>" style="width:100px; text-align:right;" class="currency" readonly="readonly" />
		</td>
	</tr>
    <tr>
        <td class="tagForm">Preparado Por:</td>
        <td>
            <input type="hidden" name="PreparadoPor" id="PreparadoPor" value="<?=$field['PreparadoPor']?>" />
            <input type="text" name="NomPreparadoPor" id="NomPreparadoPor" value="<?=htmlentities($field['NomPreparadoPor'])?>" style="width:225px;" disabled="disabled" />
            <input type="text" name="FechaPreparacion" id="FechaPreparacion" value="<?=formatFechaDMA($field['FechaPreparacion'])?>" style="width:60px;" readonly="readonly" />
        </td>
		<td class="tagForm">Monto Bruto:</td>
		<td>
        	<input type="text" name="MontoBruto" id="MontoBruto" value="<?=number_format($field['MontoBruto'], 2, ',', '.')?>" style="width:100px; text-align:right;" class="currency" readonly="readonly" />
		</td>
    </tr>
    <tr>
        <td class="tagForm">Aprobado Por:</td>
        <td>
            <input type="hidden" name="AprobadoPor" id="AprobadoPor" value="<?=$field['AprobadoPor']?>" />
            <input type="text" name="NomAprobadoPor" id="NomAprobadoPor" value="<?=htmlentities($field['NomAprobadoPor'])?>" style="width:225px;" disabled="disabled" />
            <input type="text" name="FechaAprobacion" id="FechaAprobacion" value="<?=formatFechaDMA($field['FechaAprobacion'])?>" style="width:60px;" disabled="disabled" />
        </td>
		<td class="tagForm">Monto Impuesto:</td>
		<td>
        	<input type="text" name="MontoImpuesto" id="MontoImpuesto" value="<?=number_format($field['MontoImpuesto'], 2, ',', '.')?>" style="width:100px; text-align:right;" class="currency" readonly="readonly" />
		</td>
	</tr>
    <tr>
        <td class="tagForm">Obligaci&oacute;n:</td>
		<td>
        	<input type="text" name="CodTipoDocumento" id="CodTipoDocumento" value="<?=$field['CodTipoDocumento']?>" style="width:20px;" readonly="readonly" />
        	<input type="text" name="NroDocumento" id="NroDocumento" value="<?=$field['NroDocumento']?>" style="width:95px;" readonly="readonly" />
            &nbsp; &nbsp; &nbsp; 
            Reposicion Final
            <input type="checkbox" name="FlagReposicionFinal" id="FlagReposicionFinal" value="S" <?=chkFlag($field['FlagReposicionFinal'])?> <?=$disabled_ver?> />
		</td>
		<td class="tagForm">Monto Retenci&oacute;n:</td>
		<td>
        	<input type="text" name="MontoRetencion" id="MontoRetencion" value="<?=number_format($field['MontoRetencion'], 2, ',', '.')?>" style="width:100px; text-align:right;" class="currency" readonly="readonly" />
		</td>
	</tr>
    <tr>
        <td class="tagForm">Nro. Doc. Interno:</td>
		<td>
        	<input type="text" name="NroDocumentoInterno" id="NroDocumentoInterno" value="<?=$field['NroDocumentoInterno']?>" style="width:125px;" readonly="readonly" />
		</td>
		<td class="tagForm">Monto Total:</td>
		<td>
        	<input type="text" name="MontoTotal" id="MontoTotal" value="<?=number_format($field['MontoTotal'], 2, ',', '.')?>" style="width:100px; text-align:right;" class="currency codigo" readonly="readonly" />
		</td>
	</tr>
    <tr>
		<td class="tagForm">Tipo de Pago:</td>
		<td>
            <select name="CodTipoPago" id="CodTipoPago" style="width:130px;" <?=$disabled_selects?>>
                <?=loadSelect("masttipopago", "CodTipoPago", "TipoPago", $field['CodTipoPago'], 1)?>
            </select>
		</td>
		<td class="tagForm"><strong>Monto a Reembolsar</strong>:</td>
		<td>
        	<input type="text" name="MontoNeto" id="MontoNeto" value="<?=number_format($field['MontoNeto'], 2, ',', '.')?>" style="width:100px; text-align:right;" class="currency codigo" readonly="readonly" />
		</td>
	</tr>
    <tr>
		<td class="tagForm">Clasificaci&oacute;n:</td>
		<td>
            <select name="CodClasificacion" id="CodClasificacion" style="width:130px;" <?=$disabled_selects?>>
                <?=loadSelect("ap_clasificaciongastos", "CodClasificacion", "Descripcion", $field['CodClasificacion'], 1)?>
            </select>
		</td>
		<td class="tagForm"><strong>Monto Autorizado</strong>:</td>
		<td>
        	<input type="text" name="MontoAutorizado" id="MontoAutorizado" value="<?=number_format($MontoAutorizado, 2, ',', '.')?>" style="width:100px; text-align:right;" class="currency codigo" readonly="readonly" />
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Descripci&oacute;n:</td>
		<td colspan="3">
        	<textarea name="Descripcion" id="Descripcion" style="width:95%; height:50px;" class="iEditable" <?=$disabled_ver?>><?=htmlentities($field['Descripcion'])?></textarea>
		</td>
	</tr>
    <tr>
		<td class="tagForm">Motivo Rechazo:</td>
		<td colspan="3">
        	<textarea name="RazonRechazo" id="RazonRechazo" style="width:95%; height:50px;" <?=$disabled_anular?>><?=htmlentities($field['RazonRechazo'])?></textarea>
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
<form name="frm_conceptos" id="frm_conceptos" autocomplete="off">
<input type="hidden" id="sel_conceptos" />
<table width="<?=$_width?>" class="tblBotones">
	<thead>
    	<th class="divFormCaption" colspan="2">Conceptos del Gasto</th>
    </thead>
    <tbody>
    <tr>
        <td class="gallery clearfix">
        	<a id="aSelPersona" href="../lib/listas/listado_personas.php?filtrar=default&ventana=caja_chica_persona&cod=CodProveedor&nom=NomProveedor&campo3=DocFiscal&seldetalle=sel_conceptos&iframe=true&width=850&height=400" rel="prettyPhoto[iframe3]" style="display:none;"></a>           
            <input type="button" class="btLista bEditable" id="btSelPersona" value="Sel. Persona" onclick="validarAbrirLista('sel_conceptos', 'aSelPersona');" <?=$disabled_conceptos?> />
        </td>
        <td align="right" class="gallery clearfix">
            <a id="a_conceptos" href="../lib/listas/listado_concepto_gastos.php?filtrar=default&ventana=caja_chica_conceptos_insertar&detalle=conceptos&iframe=true&width=925&height=420" rel="prettyPhoto[iframe4]" style="display:none;"></a>
            
            <a id="a_distribucion" href="pagina.php?iframe=true" rel="prettyPhoto[iframe5]" style="display:none;"></a>
            <input type="button" class="btLista bEditable" value="Insertar" <?=$disabled_conceptos?> onclick="$('#a_conceptos').click();" />
            <input type="button" class="btLista bEditable" value="Borrar" onclick="quitar(this, 'conceptos'); caja_chica_totales();" <?=$disabled_conceptos?> /> |
            <input type="button" class="btLista bEditable" value="Distribución" onclick="caja_chica_distribucion_abrir();" <?=$disabled_conceptos?> />
        </td>
    </tr>
    </tbody>
</table>
<div style="overflow:scroll; width:<?=$_width?>px; height:310px;">
<table width="2100" class="tblLista">
    <thead>
    <tr>
        <th scope="col" width="15">&nbsp;</th>
        <th scope="col" width="60">Fecha</th>
        <th scope="col" width="250" align="left">Concepto</th>
        <th scope="col" align="left">Descripci&oacute;n</th>
        <th scope="col" width="75">Monto Pagado</th>
        <th scope="col" width="100">Tipo Impuesto</th>
        <th scope="col" width="100">Tipo Servicio</th>
        <th scope="col" width="75">Monto Afecto</th>
        <th scope="col" width="75">Monto No Afecto</th>
        <th scope="col" width="75">Monto Impuesto</th>
        <th scope="col" width="75">Monto Retenci&oacute;n</th>
        <th scope="col" colspan="2">Documento</th>
        <th scope="col" width="75">Factura</th>
        <th scope="col" width="75">Doc. Fiscal</th>
        <th scope="col" width="250">Persona</th>
    </tr>
    </thead>
    
    <tbody id="lista_conceptos">
    <?php
	$nro_conceptos = 0;
	$sql = "SELECT
				ccd.*,
				cg.Descripcion AS NomConceptoGasto,
				cg.CodPartida,
				cg.CodCuenta,
				cg.CodCuentaPub20
			FROM
				ap_cajachicadetalle ccd
				INNER JOIN ap_conceptogastos cg ON (ccd.CodConceptoGasto = cg.CodConceptoGasto)
			WHERE
				ccd.FlagCajaChica = '".$FlagCajaChica."' AND
				ccd.Periodo = '".$Periodo."' AND
				ccd.NroCajaChica = '".$NroCajaChica."'
			ORDER BY Fecha, Secuencia";
	$query_conceptos = mysql_query($sql) or die ($sql.mysql_error());
	while ($field_conceptos = mysql_fetch_array($query_conceptos)) {	$nro_conceptos++;
		$id = $nro_conceptos;
		$_Distribucion = "";
		$sql = "SELECT *
				FROM ap_cajachicadistribucion
				WHERE
					FlagCajaChica = '".$FlagCajaChica."' AND
					Periodo = '".$Periodo."' AND
					NroCajaChica = '".$NroCajaChica."' AND
					Secuencia = '".$field_conceptos['Secuencia']."'";
		$query_distribucion = mysql_query($sql) or die ($sql.mysql_error());
		while ($field_distribucion = mysql_fetch_array($query_distribucion)) {
			if ($_Distribucion != "") $_Distribucion .= ";";
			$_Distribucion .= "$field_distribucion[CodConceptoGasto]|$field_distribucion[CodCentroCosto]|$field_distribucion[CodPartida]|$field_distribucion[CodCuenta]|$field_distribucion[CodCuentaPub20]|$field_distribucion[Monto]";
		}
		?>
        <tr class="trListaBody" onclick="clk($(this), 'conceptos', 'conceptos_<?=$id?>');" id="conceptos_<?=$id?>">
			<th>
				<?=$nro_conceptos?>
			</th>
            <td>
            	<input type="text" name="conceptos_Fecha[]" style="text-align:center;" class="cell datepicker iEditable" value="<?=formatFechaDMA($field_conceptos['Fecha'])?>" maxlength="10" <?=$disabled_conceptos?> />
            </td>
			<td>
                <input type="hidden" name="conceptos_CodConceptoGasto[]" id="CodConceptoGasto_<?=$id?>" value="<?=$field_conceptos['CodConceptoGasto']?>" />
                <textarea style="height:25px;" class="cell2" readonly="readonly"><?=htmlentities($field_conceptos['NomConceptoGasto'])?></textarea>
			</td>
			<td>
                <textarea name="conceptos_Descripcion[]" style="height:25px;" class="cell iEditable" <?=$disabled_conceptos?>><?=htmlentities($field_conceptos['Descripcion'])?></textarea>
			</td>
			<td>
                <input type="text" name="conceptos_MontoPagado[]" id="MontoPagado_<?=$id?>" value="<?=number_format($field_conceptos['MontoPagado'], 2, ',', '.')?>" style="text-align:right;" class="cell currency iEditable" onchange="cajaChicaMontoPagado('<?=$id?>');" <?=$disabled_conceptos?> />
			</td>
            <td>
                <select name="conceptos_CodRegimenFiscal[]" style="width:130px;" class="cell iEditable" onChange="getOptionsSelect(this.value, 'tipo-servicio', 'CodTipoServicio_<?=$id?>', 1); caja_chica_bloquear_montos($(this).val());">
                    <?=loadSelect("ap_regimenfiscal", "CodRegimenFiscal", "Descripcion", $field_conceptos['CodRegimenFiscal'], 0)?>
                </select>
            </td>
            <td>
                <select name="conceptos_CodTipoServicio[]" id="CodTipoServicio_<?=$id?>" style="width:130px;" class="cell iEditable">
                	<?=loadSelectDependiente("masttiposervicio", "CodTipoServicio", "Descripcion", "CodRegimenFiscal", $field_conceptos['CodTipoServicio'], $field_conceptos['CodRegimenFiscal'], 0)?>
                </select>
            </td>
			<td>
                <input type="text" name="conceptos_MontoAfecto[]" id="MontoAfecto_<?=$id?>" value="<?=number_format($field_conceptos['MontoAfecto'], 2, ',', '.')?>" style="text-align:right;" class="cell currency iEditable" onchange="cajaChicaMontoAfecto('<?=$id?>');" <?=$disabled_conceptos?> />
			</td>
			<td>
                <input type="text" name="conceptos_MontoNoAfecto[]" id="MontoNoAfecto_<?=$id?>" value="<?=number_format($field_conceptos['MontoNoAfecto'], 2, ',', '.')?>" style="text-align:right;" class="cell currency iEditable" onchange="cajaChicaMontoNoAfecto('<?=$id?>');" <?=$disabled_conceptos?> />
			</td>
			<td>
                <input type="text" name="conceptos_MontoImpuesto[]" id="MontoImpuesto_<?=$id?>" value="<?=number_format($field_conceptos['MontoImpuesto'], 2, ',', '.')?>" style="text-align:right;" class="cell currency" <?=$disabled_conceptos?> />
			</td>
			<td>
                <input type="text" name="conceptos_MontoRetencion[]" id="MontoRetencion_<?=$id?>" value="<?=number_format($field_conceptos['MontoRetencion'], 2, ',', '.')?>" style="text-align:right;" class="cell currency iEditable" <?=$disabled_conceptos?> />
			</td>
            <td width="45">
                <select name="conceptos_CodTipoDocumento[]" class="cell iEditable">
                    <?=loadSelect("ap_tipodocumento", "CodTipoDocumento", "Descripcion", $field_conceptos['CodTipoDocumento'], 10)?>
                </select>
            </td>
			<td width="125">
                <input type="text" name="conceptos_NroDocumento[]" class="cell iEditable" value="<?=$field_conceptos['NroDocumento']?>" maxlength="20" <?=$disabled_conceptos?> />
			</td>
			<td>
                <input type="text" name="conceptos_NroRecibo[]" class="cell iEditable" value="<?=$field_conceptos['NroRecibo']?>" maxlength="20" <?=$disabled_conceptos?> />
			</td>
			<td>
                <input type="text" name="conceptos_DocFiscal[]" id="DocFiscal_<?=$id?>" class="cell2" value="<?=$field_conceptos['DocFiscal']?>" maxlength="20" readonly="readonly" />
			</td>
			<td>
                <input type="hidden" name="conceptos_CodProveedor[]" id="CodProveedor_<?=$id?>" value="<?=$field_conceptos['CodProveedor']?>" />
                <input type="text" name="conceptos_NomProveedor[]" id="NomProveedor_<?=$id?>" class="cell2 iEditable" value="<?=$field_conceptos['NomProveedor']?>" readonly="readonly" onfocus="caja_chica_habilitar_proveedor('<?=$id?>', '<?=$opcion?>');" />
                <input type="hidden" name="conceptos_CodPartida[]" id="CodPartida_<?=$id?>" value="<?=$field_conceptos['CodPartida']?>" />
                <input type="hidden" name="conceptos_CodCuenta[]" id="CodCuenta_<?=$id?>" value="<?=$field_conceptos['CodCuenta']?>" />
                <input type="hidden" name="conceptos_CodCuentaPub20[]" id="CodCuentaPub20_<?=$id?>" value="<?=$field_conceptos['CodCuentaPub20']?>" />
                <input type="hidden" name="conceptos_Distribucion[]" id="Distribucion_<?=$id?>" value="<?=$_Distribucion?>" />
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
<div style="width:<?=$_width?>px;" class="divFormCaption">Distribuci&oacute;n Contable</div>
<div style="overflow:scroll; width:<?=$_width?>px; height:150px;">
<table width="100%" class="tblLista">
	<thead>
	<tr>
        <th scope="col" width="125">Cuenta</th>
        <th scope="col">Descripci&oacute;n</th>
        <th scope="col" width="100">Monto</th>
    </tr>
    </thead>
    
    <tbody id="lista_cuentas">
    <?php
	list($_cod_partida_igv, $_CodCuenta_igv, $_CodCuentaPub20_igv) = getPartidaCuentaFromIGV($_PARAMETRO["IGVCODIGO"]);
	$nrocuentas = 0;
	$sql = "(SELECT
				ccd.CodCuenta,
				pc.Descripcion,
				SUM(ccd.Monto) AS Monto
			FROM
				ap_cajachicadistribucion ccd
				INNER JOIN ac_mastplancuenta pc ON (ccd.CodCuenta = pc.CodCuenta)
			WHERE
				ccd.FlagCajaChica = '".$FlagCajaChica."' AND
				ccd.Periodo = '".$Periodo."' AND
				ccd.NroCajaChica = '".$NroCajaChica."'
			GROUP BY CodCuenta)
			UNION
			(SELECT
				'".$_CodCuenta_igv."' AS CodCuenta,
				'".getVar("ac_mastplancuenta", "Descripcion", "CodCuenta", $_CodCuenta_igv)."' AS Descripcion,
				'".$field['MontoImpuesto']."' AS Monto)
			ORDER BY CodCuenta";
	$query_cuentas = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	while ($field_cuentas = mysql_fetch_array($query_cuentas)) {
		$nrocuentas++;
		?>
		<tr class="trListaBody">
			<td align="center">
				<?=$field_cuentas['CodCuenta']?>
            </td>
			<td>
				<?=$field_cuentas['Descripcion']?>
            </td>
			<td align="right">
				<?=number_format($field_cuentas['Monto'], 2, ',', '.')?>
            </td>
		</tr>
		<?php
	}
	?>
    </tbody>
</table>
</div>

<div style="width:<?=$_width?>px;" class="divFormCaption">Distribuci&oacute;n Contable (Pub. 20)</div>
<div style="overflow:scroll; width:<?=$_width?>px; height:150px;">
<table width="100%" class="tblLista">
	<thead>
	<tr>
        <th scope="col" width="125">Cuenta</th>
        <th scope="col">Descripci&oacute;n</th>
        <th scope="col" width="100">Monto</th>
    </tr>
    </thead>
    
    <tbody id="lista_cuentas20">
    <?php
	$nrocuentas = 0;
	$sql = "(SELECT
				ccd.CodCuentaPub20,
				pc.Descripcion,
				SUM(ccd.Monto) AS Monto
			FROM
				ap_cajachicadistribucion ccd
				INNER JOIN ac_mastplancuenta20 pc ON (ccd.CodCuentaPub20 = pc.CodCuenta)
			WHERE
				ccd.FlagCajaChica = '".$FlagCajaChica."' AND
				ccd.Periodo = '".$Periodo."' AND
				ccd.NroCajaChica = '".$NroCajaChica."'
			GROUP BY CodCuentaPub20)
			UNION
			(SELECT
				'".$_CodCuentaPub20_igv."' AS CodCuentaPub20,
				'".getVar("ac_mastplancuenta20", "Descripcion", "CodCuenta", $_CodCuentaPub20_igv)."' AS Descripcion,
				'".$field['MontoImpuesto']."' AS Monto)
			ORDER BY CodCuentaPub20";
	$query_cuentas = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	while ($field_cuentas = mysql_fetch_array($query_cuentas)) {
		$nrocuentas++;
		?>
		<tr class="trListaBody">
			<td align="center">
				<?=$field_cuentas['CodCuentaPub20']?>
            </td>
			<td>
				<?=$field_cuentas['Descripcion']?>
            </td>
			<td align="right">
				<?=number_format($field_cuentas['Monto'], 2, ',', '.')?>
            </td>
		</tr>
		<?php
	}
	?>
    </tbody>
</table>
</div>

<div style="width:<?=$_width?>px;" class="divFormCaption">Distribuci&oacute;n Presupuestaria</div>
<table class="tblBotones" style="width:<?=$_width?>px;">
	<tr>
    	<td width="35"><div style="background-color:#F8637D; width:25px; height:20px;"></div></td>
        <td>Sin disponibilidad presupuestaria</td>
    	<td width="35"><div style="background-color:#D0FDD2; width:25px; height:20px;"></div></td>
        <td>Disponibilidad presupuestaria</td>
    	<td width="35"><div style="background-color:#FFC; width:25px; height:20px;"></div></td>
        <td>Disponibilidad presupuestaria (Tiene ordenes pendientes)</td>
		<td align="right" class="gallery clearfix">
        	<a id="a_disponibilidad" href="pagina.php?iframe=true" rel="prettyPhoto[iframe6]" style="display:none;"></a>
			<input type="button" value="Disponibilidad Presupuestaria" onclick="abrir_disponibilidad();" />
		</td>
	</tr>
</table>
<div style="overflow:scroll; width:<?=$_width?>px; height:150px;">
<form name="frm_partidas" id="frm_partidas">
<table width="100%" class="tblLista">
	<thead>
	<tr>
        <th scope="col" width="125">Partida</th>
        <th scope="col">Descripci&oacute;n</th>
        <th scope="col" width="100">Monto</th>
    </tr>
    </thead>
    
    <tbody id="lista_partidas">
    <?php
	$nropartidas = 0;
	$sql = "(SELECT
				ccd.CodPartida,
				pc.denominacion,
				SUM(ccd.Monto) AS Monto
			FROM
				ap_cajachicadistribucion ccd
				INNER JOIN pv_partida pc ON (ccd.CodPartida = pc.cod_partida)
			WHERE
				ccd.FlagCajaChica = '".$FlagCajaChica."' AND
				ccd.Periodo = '".$Periodo."' AND
				ccd.NroCajaChica = '".$NroCajaChica."'
			GROUP BY CodPartida)
			UNION
			(SELECT
				'".$_cod_partida_igv."' AS CodPartida,
				'".getVar("pv_partida", "denominacion", "cod_partida", $_cod_partida_igv)."' AS denominacion,
				'".$field['MontoImpuesto']."' AS Monto)
			ORDER BY CodPartida";
	$query_partidas = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	while ($field_partidas = mysql_fetch_array($query_partidas)) {
		$nropartidas++;
		list($MontoAjustado, $MontoCompromiso, $PreCompromiso, $CotizacionesAsignadas) = disponibilidadPartida2($field['Periodo'], $field['CodOrganismo'], $field_partidas['CodPartida'], $field['CodPresupuesto']);
		$MontoPendiente = $PreCompromiso + $CotizacionesAsignadas;
		$MontoDisponible = $MontoAjustado - $MontoCompromiso;
		$MontoDisponibleReal = $MontoAjustado - ($MontoCompromiso + $MontoPendiente);
		if ($field['Estado'] == "PR" && $field['NroCajaChica'] != "") $MontoPendiente -= $field_partidas['Monto'];
		//	valido
		if ($field['Estado'] == "PR" && $field_partidas['Monto'] > $MontoDisponible) $style = "style='font-weight:bold; background-color:#F8637D;'";
		elseif($field['Estado'] == "PR" && $field_partidas['Monto'] > $MontoDisponibleReal) $style = "style='font-weight:bold; background-color:#FFC;'";
		else $style = "style='font-weight:bold; background-color:#D0FDD2;'";
		?>
		<tr class="trListaBody" <?=$style?>>
			<td align="center">
                <input type="hidden" name="partidas_cod_partida[]" value="<?=$field_partidas['CodPartida']?>" />
                <input type="hidden" name="partidas_Monto[]" value="<?=$field_partidas['Monto']?>" />
                <input type="hidden" name="partidas_MontoAjustado[]" value="<?=$MontoAjustado?>" />
                <input type="hidden" name="partidas_MontoCompromiso[]" value="<?=$MontoCompromiso?>" />
                <input type="hidden" name="partidas_PreCompromiso[]" value="<?=$PreCompromiso?>" />
                <input type="hidden" name="partidas_CotizacionesAsignadas[]" value="<?=$CotizacionesAsignadas?>" />
                <input type="hidden" name="partidas_MontoDisponible[]" value="<?=$MontoDisponible?>" />
                <input type="hidden" name="partidas_MontoDisponibleReal[]" value="<?=$MontoDisponibleReal?>" />
				<?=$field_partidas['CodPartida']?>
            </td>
			<td>
				<?=htmlentities($field_partidas['denominacion'])?>
            </td>
			<td align="right">
				<?=number_format($field_partidas['Monto'], 2, ',', '.')?>
            </td>
		</tr>
		<?php
	}
	?>
    </tbody>
</table>
</form>
</div>
</center>
</div>

<center>
<input type="button" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" class="bEditable" onclick="caja_chica(document.getElementById('frmentrada'), '<?=$accion?>');" />
<input type="button" value="Cancelar" style="width:75px;" onclick="<?=$clkCancelar?>" />
</center>
<div style="width:<?=$_width?>px" class="divMsj">Campos Obligatorios *</div>

<script type="text/javascript" language="javascript">
$(document).ready(function() {
	<?php
	if ($MontoAutorizado > 0) $boo = true; else $boo = false;
	if ($opcion == "nuevo" || $opcion == "modificar") { ?>caja_chica_habilitar(<?=$boo?>);<?php }
	?>
});
function caja_chica(form, accion) {
	$(".div-progressbar").css("display", "block");
	//	valido
	var error = "";
	var warning = "";
	if ($("#Descripcion").val().trim() == "") error = "Debe llenar los campos obligatorios";
	if (error == "") {
		//	detalles
		var detalles_conceptos = "";
		var frm_conceptos = document.getElementById("frm_conceptos");
		for(var i=0; n=frm_conceptos.elements[i]; i++) {
			if (n.name == "conceptos_Fecha[]") {
				if (!valFecha(n.value)) { error = "Formato de fecha incorrecta en las lineas de Conceptos"; break; }
			}
			else if (n.name == "conceptos_MontoPagado[]") {
				var MontoPagado = setNumero(n.value);
				if (isNaN(MontoPagado) || MontoPagado == 0) error = "Se encontraron Montos Pagados incorrectos en las lineas de Conceptos";
			}
			else if (n.name == "conceptos_MontoAfecto[]") {
				var MontoAfecto = setNumero(n.value);
				if (isNaN(MontoAfecto)) error = "Se encontraron Montos Afectos incorrectos en las lineas de Conceptos";
			}
			else if (n.name == "conceptos_MontoNoAfecto[]") {
				var MontoNoAfecto = setNumero(n.value);
				if (isNaN(MontoNoAfecto)) error = "Se encontraron Montos No Afectos incorrectos en las lineas de Conceptos";
			}
			else if (n.name == "conceptos_MontoImpuesto[]") {
				var MontoImpuesto = setNumero(n.value);
				if (isNaN(MontoImpuesto)) error = "Se encontraron Montos Impuestos incorrectos en las lineas de Conceptos";
			}
			else if (n.name == "conceptos_MontoRetencion[]") {
				var MontoRetencion = setNumero(n.value);
				if (isNaN(MontoRetencion)) error = "Se encontraron Montos de Retenci&oacute;n incorrectos en las lineas de Conceptos";
			}
		}
		//	detalles partidas
		var detalles_partidas = "";
		var frm_partidas = document.getElementById("frm_partidas");
		for(var i=0; n=frm_partidas.elements[i]; i++) {
			if (n.name == "partidas_cod_partida[]") var cod_partida = n.value;
			else if (n.name == "partidas_Monto[]") var Monto = parseFloat(n.value);
			else if (n.name == "partidas_MontoDisponible[]") {
				var MontoDisponible = parseFloat(n.value);
				if (Monto > MontoDisponible && accion == "aprobar") { error = "Sin disponibilidad presupuestaria la partida <strong>" + cod_partida + "</strong>"; break; }
				else if (Monto > MontoDisponible && accion != "anular") { warning = "Se encontrar&oacute;n partidas sin disponibilidad presupuestaria.<br />Revise la pesta&ntilde;a <strong>Dist. Presupuestaria</strong> para mayor informaci&oacute;n.<br /><br /><strong>Continuar de todas formas?</strong>"; }
			}
			else if (n.name == "partidas_MontoDisponibleReal[]") {
				var MontoDisponibleReal = parseFloat(n.value);
				if (Monto > MontoDisponibleReal && accion != "anular") { if (!warning) warning = "Se encontrar&oacute;n partidas con Pre-Compromiso que exceden la disponibilidad presupuestaria.<br />Revise la pesta&ntilde;a <strong>Dist. Presupuestaria</strong> para mayor informaci&oacute;n.<br /><br /><strong>Continuar de todas formas?</strong>"; }
			}
		}
	}
	//	valido errores
	if (error != "") { cajaModal(error, "error", 400); }
	else {
		if (warning) {
			$("#cajaModal").dialog({
				buttons: {
					"Si": function() {
						$(this).dialog("close");
						caja_chica_ajax(accion);
					},
					"No": function() {
						$(".div-progressbar").css("display", "none");
						$(this).dialog("close");
					}
				}
			});	
			$("#cajaModal").dialog({ 
				title: "<img src='../imagenes/info.png' width='24' align='absmiddle' />Confirmación", 
				width: 400
			});
			$("#cajaModal").html(warning);
			$('#cajaModal').dialog('open');
		} else caja_chica_ajax(accion);
	}
	return false;
}
function caja_chica_ajax(accion) {
	//	ajax
	$.ajax({
		type: "POST",
		url: "ap_caja_chica_ajax.php",
		data: "modulo=caja_chica&accion="+accion+"&"+$('#frmentrada').serialize()+"&"+$('#frm_conceptos').serialize()+"&"+$('#frm_partidas').serialize(),
		async: false,
		success: function(resp) {
			if (resp.trim() != "") cajaModal(resp, "error", 400);
			else document.getElementById('frmentrada').submit();
		}
	});
}
function caja_chica_distribucion_abrir() {
	var sel_conceptos = $("#sel_conceptos").val();
	if (sel_conceptos == "") cajaModal("Debe seleccionar una linea", "error", 400);
	else {
		var partes = sel_conceptos.split("_");
		var Distribucion = $("#Distribucion_"+partes[1]).val();
		var CodConceptoGasto = $("#CodConceptoGasto_"+partes[1]).val();
		var MontoAfecto = setNumero($("#MontoAfecto_"+partes[1]).val());
		var MontoNoAfecto = setNumero($("#MontoNoAfecto_"+partes[1]).val());
		var MontoBruto = MontoAfecto + MontoNoAfecto;
		var CodPartida = $("#CodPartida_"+partes[1]).val();
		var CodCuenta = $("#CodCuenta_"+partes[1]).val();
		var CodCuentaPub20 = $("#CodCuentaPub20_"+partes[1]).val();
		var CodCentroCosto = $("#CodCentroCosto").val();
		if (Distribucion == "") {
			Distribucion = CodConceptoGasto + "|" + CodCentroCosto + "|" + CodPartida + "|" + CodCuenta + "|" + CodCuentaPub20 + "|" + MontoBruto;
		}
		var href = "gehen.php?anz=ap_caja_chica_distribucion&Distribucion="+Distribucion+"&MontoBruto="+MontoBruto+"&CodCentroCosto="+CodCentroCosto+"&id_conceptos="+partes[1]+"&iframe=true&width=925&height=400";
		$("#a_distribucion").attr("href", href);
		document.getElementById("a_distribucion").click();
	}
}
function mostrarTabDistribucionCajaChica() {
	var Periodo = $("#Periodo").val();
	var FlagCajaChica = $("#FlagCajaChica").val();
	var NroCajaChica = $("#NroCajaChica").val();
	var CodPresupuesto = $("#CodPresupuesto").val();
	var CodOrganismo = $("#CodOrganismo").val();
	var MontoImpuesto = setNumero($("#MontoImpuesto").val());
	//	detalles
	var detalles_conceptos = "";
	var frm_conceptos = document.getElementById("frm_conceptos");
	for(var i=0; n=frm_conceptos.elements[i]; i++) {
		if (n.name == "conceptos_MontoAfecto[]") var MontoAfecto = setNumero(n.value);
		else if (n.name == "conceptos_MontoNoAfecto[]") {
			var MontoNoAfecto = setNumero(n.value);
			var Monto = MontoAfecto + MontoNoAfecto;
			detalles_conceptos += Monto + ";char:td;";
		}
		else if (n.name == "conceptos_CodPartida[]") detalles_conceptos += n.value + ";char:td;";
		else if (n.name == "conceptos_CodCuenta[]") detalles_conceptos += n.value + ";char:td;";
		else if (n.name == "conceptos_CodCuentaPub20[]") detalles_conceptos += n.value + ";char:td;";
		else if (n.name == "conceptos_Distribucion[]") detalles_conceptos += n.value + ";char:tr;";
	}
	var len = detalles_conceptos.length; len-=9;
	detalles_conceptos = detalles_conceptos.substr(0, len);
	if (detalles_conceptos != "") {
		//	ajax
		$.ajax({
			type: "POST",
			url: "ap_caja_chica_ajax.php",
			data: "modulo=ajax&accion=mostrarTabDistribucionCajaChica&detalles_conceptos="+detalles_conceptos+"&Periodo="+Periodo+"&CodPresupuesto="+CodPresupuesto+"&CodOrganismo="+CodOrganismo+"&NroCajaChica="+NroCajaChica+"&FlagCajaChica="+FlagCajaChica+"&MontoImpuesto="+MontoImpuesto,
			async: false,
			success: function(resp) {
				var partes = resp.split("|");
				$("#lista_cuentas").html(partes[0]);
				$("#lista_cuentas20").html(partes[1]);
				$("#lista_partidas").html(partes[2]);
				mostrarTab("tab", 3, 3);
			}
		});
	} else {
		$("#lista_cuentas").html('');
		$("#lista_cuentas20").html('');
		$("#lista_partidas").html('');
		mostrarTab("tab", 3, 3);
	}
}
function cajaChicaMontoPagado(id) {
	var CodTipoServicio = $("#CodTipoServicio_"+id).val();
	var MontoPagado = setNumero($("#MontoPagado_"+id).val());
	if (!isNaN(MontoPagado)) {
		//	ajax
		$.ajax({
			type: "POST",
			url: "lib/fphp_funciones_ajax.php",
			data: "accion=cajaChicaMontoPagado&CodTipoServicio="+CodTipoServicio+"&MontoPagado="+MontoPagado,
			async: false,
			success: function(resp) {
				var datos = resp.split("|");
				//$("#MontoAfecto_"+id).val(datos[0]).formatCurrency();
				//$("#MontoNoAfecto_"+id).val(datos[1]).formatCurrency();
				//$("#MontoImpuesto_"+id).val(datos[2]).formatCurrency();
				caja_chica_totales();
			}
		});
	}
}
function cajaChicaMontoAfecto(id) {
	var CodTipoServicio = $("#CodTipoServicio_"+id).val();
	var MontoAfecto = setNumero($("#MontoAfecto_"+id).val());
	var MontoNoAfecto = setNumero($("#MontoNoAfecto_"+id).val());
	if (!isNaN(MontoAfecto)) {
		//	ajax
		$.ajax({
			type: "POST",
			url: "lib/fphp_funciones_ajax.php",
			data: "accion=cajaChicaMontoAfecto&CodTipoServicio="+CodTipoServicio+"&MontoAfecto="+MontoAfecto+"&MontoNoAfecto="+MontoNoAfecto,
			async: false,
			success: function(resp) {
				var datos = resp.split("|");
				$("#MontoPagado_"+id).val(datos[0]).formatCurrency();
				$("#MontoImpuesto_"+id).val(datos[1]).formatCurrency();
				caja_chica_totales();
			}
		});
	}
}
function cajaChicaMontoNoAfecto(id) {
	var MontoAfecto = setNumero($("#MontoAfecto_"+id).val());
	var MontoImpuesto = setNumero($("#MontoImpuesto_"+id).val());
	var MontoNoAfecto = setNumero($("#MontoNoAfecto_"+id).val());
	if (!isNaN(MontoNoAfecto)) {
		$("#MontoPagado_"+id).val(MontoAfecto+MontoNoAfecto+MontoImpuesto).formatCurrency();
		caja_chica_totales();
	}
}
function caja_chica_totales() {
	var MontoPagado = 0;
	var MontoAfecto = 0;
	var MontoNoAfecto = 0;
	var MontoImpuesto = 0;
	var MontoRetencion = 0;
	//	detalles
	var detalles_conceptos = "";
	var frm_conceptos = document.getElementById("frm_conceptos");
	for(var i=0; n=frm_conceptos.elements[i]; i++) {
		if (n.name == "conceptos_MontoPagado[]") MontoPagado += setNumero(n.value);
		else if (n.name == "conceptos_MontoAfecto[]") MontoAfecto += setNumero(n.value);
		else if (n.name == "conceptos_MontoNoAfecto[]") MontoNoAfecto += setNumero(n.value);
		else if (n.name == "conceptos_MontoImpuesto[]") MontoImpuesto += setNumero(n.value);
		else if (n.name == "conceptos_MontoRetencion[]") MontoRetencion += setNumero(n.value);
	}
	var MontoBruto = MontoAfecto + MontoNoAfecto;
	var MontoTotal = MontoBruto + MontoImpuesto + MontoRetencion;
	$("#MontoAfecto").val(MontoAfecto).formatCurrency();
	$("#MontoNoAfecto").val(MontoNoAfecto).formatCurrency();
	$("#MontoBruto").val(MontoBruto).formatCurrency();
	$("#MontoImpuesto").val(MontoImpuesto).formatCurrency();
	$("#MontoRetencion").val(MontoRetencion).formatCurrency();
	$("#MontoTotal").val(MontoTotal).formatCurrency();
	$("#MontoNeto").val(MontoTotal).formatCurrency();
}
function caja_chica_bloquear_montos(CodRegimenFiscal) {
	var id = $("#sel_conceptos").val();
	var MontoPagado = $("#MontoPagado_"+id);
	var MontoAfecto = $("#MontoAfecto_"+id);
	var MontoNoAfecto = $("#MontoNoAfecto_"+id);
	var MontoImpuesto = $("#MontoImpuesto_"+id);
	var MontoRetencion = $("#MontoRetencion_"+id);
	if (CodRegimenFiscal == "I") {
		MontoPagado.prop("disabled", false).val("0,00");
		MontoAfecto.prop("disabled", false).val("0,00");
		MontoNoAfecto.prop("disabled", false).val("0,00");
		MontoImpuesto.prop("disabled", false).val("0,00");
		MontoRetencion.prop("disabled", true).val("0,00");
	}
	else if (CodRegimenFiscal == "M") {
		MontoPagado.prop("disabled", true).val("0,00");
		MontoAfecto.prop("disabled", false).val("0,00");
		MontoNoAfecto.prop("disabled", false).val("0,00");
		MontoImpuesto.prop("disabled", false).val("0,00");
		MontoRetencion.prop("disabled", true).val("0,00");
	}
	else if (CodRegimenFiscal == "N") {
		MontoPagado.prop("disabled", false).val("0,00");
		MontoAfecto.prop("disabled", true).val("0,00");
		MontoNoAfecto.prop("disabled", true).val("0,00");
		MontoImpuesto.prop("disabled", true).val("0,00");
		MontoRetencion.prop("disabled", true).val("0,00");
	}
	else if (CodRegimenFiscal == "R") {
		MontoPagado.prop("disabled", true).val("0,00");
		MontoAfecto.prop("disabled", false).val("0,00");
		MontoNoAfecto.prop("disabled", false).val("0,00");
		MontoImpuesto.prop("disabled", true).val("0,00");
		MontoRetencion.prop("disabled", true).val("0,00");
	}
}
function caja_chica_habilitar(boo) {
	$(".iEditable").prop("disabled", !boo);
	$(".bEditable").prop("disabled", !boo);
	if (boo) {
		$(".aEditable").css("visibility", "visible");
		$("#nocumple").css("display", "none");
	} else {
		$(".aEditable").css("visibility", "hidden");
		$("#nocumple").css("display", "block");
	}
}
function caja_chica_habilitar_proveedor(id, opcion) {
	if (opcion != "ver") {
		var CodProveedor = $("#CodProveedor_"+id);
		var NomProveedor = $("#NomProveedor_"+id);
		if (CodProveedor.val() != "") {
			NomProveedor.prop("readonly", false);
			NomProveedor.removeClass("cell2");
			NomProveedor.addClass("cell");
		} else {
			NomProveedor.prop("readonly", true);
			NomProveedor.removeClass("cell");
			NomProveedor.addClass("cell2");
		}
	}
}
function abrir_disponibilidad() {
	var href = "gehen.php?anz=ap_caja_chica_disponibilidad&"+$('#frmentrada').serialize()+"&"+$('#frm_partidas').serialize()+"&iframe=true&width=950&height=430";
	$('#a_disponibilidad').attr('href', href);
	$('#a_disponibilidad').click();
}
</script>