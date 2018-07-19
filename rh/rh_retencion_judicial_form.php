<?php
if ($opcion == "nuevo") {
	$field['Estado'] = "A";
	if (!$fCodOrganismo) $fCodOrganismo = $_SESSION["ORGANISMO_ACTUAL"];
	##
	$titulo = "Nueva Retenci&oacute;n";
	$accion = "nuevo";
	$disabled_nuevo = "disabled";
	$disabled_modificar = "";
	$disabled_ver = "";
	$display_modificar = "";
	$display_ver = "";
	$label_submit = "Guardar";
	$focus = "Observaciones";
	$clkCancelar = "document.getElementById('frmentrada').submit();";
}
elseif ($opcion == "modificar" || $opcion == "ver") {
	//	consulto datos generales
	list($CodOrganismo, $CodRetencion) = explode("_", $sel_registros);
	$sql = "SELECT
				rj.*,
				p1.NomCompleto AS NomPersona,
				e1.CodEmpleado,
				p2.NomCompleto AS NomDemandante
			FROM
				rh_retencionjudicial rj
				INNER JOIN mastpersonas p1 ON (p1.CodPersona = rj.CodPersona)
				INNER JOIN mastempleado e1 ON (e1.CodPersona = p1.CodPersona)
				INNER JOIN mastpersonas p2 ON (p2.CodPersona = rj.Demandante)
			WHERE
				rj.CodOrganismo = '".$CodOrganismo."' AND
				rj.CodRetencion = '".$CodRetencion."'";
	$field = getRecord($sql);
	##
	if ($opcion == "modificar") {
		$titulo = "Modificar Retenci&oacute;n";
		$accion = "modificar";
		$disabled_nuevo = "";
		$disabled_modificar = "disabled";
		$disabled_ver = "";
		$display_modificar = "display:none;";
		$display_ver = "";
		$label_submit = "Modificar";
		$focus = "DescripCargo";
		$clkCancelar = "document.getElementById('frmentrada').submit();";
	}
	##
	elseif ($opcion == "ver") {
		$titulo = "Ver Retenci&oacute;n";
		$accion = "";
		$disabled_nuevo = "disabled";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$display_modificar = "display:none;";
		$display_ver = "display:none;";
		$label_submit = "";
		$focus = "btCancelar";
		$clkCancelar = "document.getElementById('frmentrada').submit();";
	}
}
//	------------------------------------
$_width = 900;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=rh_retencion_judicial_lista" method="POST" enctype="multipart/form-data" onsubmit="return retencion_judicial(this, '<?=$accion?>');" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
<input type="hidden" name="fFechaResolucionD" id="fFechaResolucionD" value="<?=$fFechaResolucionD?>" />
<input type="hidden" name="fFechaResolucionH" id="fFechaResolucionH" value="<?=$fFechaResolucionH?>" />

<table width="<?=$_width?>" class="tblForm">
	<tr>
    	<td colspan="4" class="divFormCaption">Datos Generales</td>
    </tr>
	<tr>
		<td class="tagForm" width="125">* Organismo:</td>
		<td>
			<select id="CodOrganismo" style="width:280px;" <?=$disabled_modificar?>>
				<?=getOrganismos($field['CodOrganismo'], 3)?>
			</select>
		</td>
		<td class="tagForm" width="150">Retenci&oacute;n:</td>
		<td>
        	<input type="text" id="CodRetencion" value="<?=$field['CodRetencion']?>" style="width:75px;" disabled="disabled" />
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Empleado:</td>
		<td class="gallery clearfix">
            <input type="hidden" id="CodPersona" value="<?=$field['CodPersona']?>" style="width:75px;" disabled />
            <input type="hidden" id="CodEmpleado" value="<?=$field['CodEmpleado']?>" style="width:75px;" disabled />
            <input type="text" id="NomPersona" value="<?=$field['NomPersona']?>" style="width:275px;" disabled />
            <a href="../lib/listas/listado_empleados.php?filtrar=default&ventana=selLista&campo1=CodPersona&campo2=NomPersona&campo3=CodEmpleado&iframe=true&width=950&height=430" rel="prettyPhoto[iframe1]" id="btEmpleado" style=" <?=$display_modificar?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
		<td class="tagForm">* Fecha de Resoluci&oacute;n:</td>
		<td>
        	<input type="text" id="FechaResolucion" value="<?=formatFechaDMA($field['FechaResolucion'])?>" style="width:75px;" maxlength="10" class="datepicker" <?=$disabled_ver?> />
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Demandante:</td>
		<td class="gallery clearfix">
        	<input type="hidden" id="Demandante" value="<?=$field['Demandante']?>" />
			<input type="text" id="NomDemandante" value="<?=htmlentities($field['NomDemandante'])?>" disabled="disabled" style="width:275px;" />
			<a href="../lib/listas/listado_personas.php?filtrar=default&ventana=selLista&campo1=Demandante&campo2=NomDemandante&EsEmpleado=N&EsProveedor=N&EsOtros=S&iframe=true&width=825&height=400" rel="prettyPhoto[iframe2]" style=" <?=$display_ver?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
		<td class="tagForm">* Tipo de Retenci&oacute;n:</td>
		<td>
            <select id="TipoRetencion" style="width:175px;" <?=$disabled_ver?>>
                <option value="">&nbsp;</option>
                <?=getMiscelaneos($field['TipoRetencion'], "RJUDICIAL", 0)?>
            </select>
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Tipo de Pago:</td>
		<td>
            <select id="CodTipoPago" style="width:125px;" <?=$disabled_ver?>>
                <option value="">&nbsp;</option>
                <?=loadSelect2("masttipopago", "CodTipoPago", "TipoPago", $field['CodTipoPago'])?>
            </select>
		</td>
		<td class="tagForm">* Expediente:</td>
		<td>
        	<input type="text" id="Expediente" value="<?=$field['Expediente']?>" style="width:170px;" maxlength="30" <?=$disabled_ver?> />
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Juzgado:</td>
		<td colspan="3">
        	<input type="text" id="Juzgado" value="<?=$field['Juzgado']?>" style="width:95%;" maxlength="255" <?=$disabled_ver?> />
		</td>
	</tr>
	<tr>
		<td class="tagForm">Comentarios:</td>
		<td colspan="3">
        	<textarea id="Observaciones" style="width:95%; height:50px;" <?=$disabled_ver?>><?=htmlentities($field['Observaciones'])?></textarea>
		</td>
	</tr>
	<tr>
		<td class="tagForm">Estado:</td>
		<td colspan="3">
            <input type="radio" name="Estado" id="Activo" value="A" <?=chkOpt($field['Estado'], "A");?> <?=$disabled_nuevo?> /> Activo
            &nbsp; &nbsp;
            <input type="radio" name="Estado" id="Inactivo" value="I" <?=chkOpt($field['Estado'], "I");?> <?=$disabled_nuevo?> /> Inactivo
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
<center>
<input type="submit" value="<?=$label_submit?>" style="width:75px; <?=$display_ver?>" />
<input type="button" value="Cancelar" style="width:75px;" id="btCancelar" onclick="<?=$clkCancelar?>" />
</center>
</form>
<div style="width:<?=$_width?>px" class="divMsj">Campos Obligatorios *</div>

<center>
<form name="frm_conceptos" id="frm_conceptos" method="post" autocomplete="off">
<input type="hidden" id="sel_conceptos" />
<table width="<?=$_width?>" class="tblBotones">
	<thead>
    	<th class="divFormCaption">Conceptos de N&oacute;mina</th>
    </thead>
    <tbody>
    <tr>
        <td align="right" class="gallery clearfix">
            <a id="a_conceptos" href="../lib/listas/listado_conceptos.php?filtrar=default&ventana=retencion_judicial&detalle=conceptos&iframe=true&width=825&height=425" rel="prettyPhoto[iframe3]" style="display:none;"></a>
            <input type="button" class="btLista" value="Insertar" <?=$disabled_ver?> onclick="$('#a_conceptos').click();" />
            <input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'conceptos');" <?=$disabled_ver?> />
        </td>
    </tr>
    </tbody>
</table>
<div style="overflow:scroll; width:<?=$_width?>px; height:200px;">
<table width="100%" class="tblLista">
	<thead>
    <tr>
        <th width="20">#</th>
        <th width="20">Concepto</th>
        <th align="left">Descripci&oacute;n</th>
        <th width="75">Tipo</th>
        <th width="100" align="right">Descuento</th>
        <th width="130">Aplica</th>
    </tr>
    </thead>
    
    <tbody id="lista_conceptos">
    	<?php
		$nro_conceptos = 0;
		$sql = "SELECT
					rjc.*,
					c.Descripcion AS NomConcepto
				FROM
					rh_retencionjudicialconceptos rjc
					INNER JOIN pr_concepto c ON (c.CodConcepto = rjc.CodConcepto)
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					CodRetencion = '".$CodRetencion."'
				ORDER BY CodConcepto";
		$field_conceptos = getRecords($sql);
		foreach ($field_conceptos as $f) {
			$id = $f['CodConcepto'];
			?>
            <tr class="trListaBody" onclick="clk($(this), 'conceptos', 'conceptos_<?=$id?>');" id="conceptos_<?=$id?>">
                <th>
					<?=++$nro_conceptos?>
                    <input type="hidden" name="CodConcepto[]" value="<?=$f['CodConcepto']?>" />
                </th>
                <td align="center">
                	<?=$f['CodConcepto']?>
                </td>
                <td>
                	<?=htmlentities($f['NomConcepto'])?>
                </td>
                <td>
                    <select name="TipoDescuento[]" class="cell" <?=$disabled_ver?> onchange="setTipoDescuento(this.value, '<?=$id?>');">
                        <?=loadSelectValores('tipo-descuento', $f['TipoDescuento'])?>
                    </select>
                </td>
                <td>
                    <input type="text" name="Descuento[]" value="<?=number_format($f['Descuento'], 2, ',', '.')?>" style="text-align:right;" class="cell currency" <?=$disabled_ver?> />
                </td>
                <td>
                    <select name="TipoSueldo[]" id="TipoSueldo_<?=$id?>" class="cell" <?=$disabled_ver?>>
                    	<option value="">&nbsp;</option>
                        <?=loadSelectValores('tipo-sueldo', $f['TipoSueldo'])?>
                    </select>
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

<script type="text/javascript" language="javascript">
function retencion_judicial(form, accion) {
	bloqueo(true);
	//	valido
	var error = "";
	if ($("#CodOrganismo").val() == "" || $("#CodPersona").val() == "" || $("#Demandante").val() == "" || $("#FechaResolucion").val() == "" || $("#TipoRetencion").val() == "" || $("#CodTipoPago").val() == "" || $("#Expediente").val() == "") error = "Debe llenar los campos obligatorios";
	else if(!valFecha($("#FechaResolucion").val())) error = "Formato <strong>Fecha de Resoluci&oacute;n</strong> incorrecta";
	
	//	valido errores
	if (error != "") {
		cajaModal(error, "error", 400);
	} else {
		var post = getForm(form);
		//	ajax
		$.ajax({
			type: "POST",
			url: "rh_retencion_judicial_ajax.php",
			data: "modulo=retencion_judicial&accion="+accion+"&"+post+"&"+$('#frm_conceptos').serialize(),
			async: false,
			success: function(resp) {
				if (resp.trim() != "") cajaModal(resp, "error", 400);
				else form.submit();
			}
		});
	}
	return false;
}
function setTipoDescuento(TipoDescuento, id) {
	if (TipoDescuento == "M") $('#TipoSueldo_'+id).val('').prop("disabled", true);
	else $('#TipoSueldo_'+id).val('').prop("disabled", false);
}
</script>