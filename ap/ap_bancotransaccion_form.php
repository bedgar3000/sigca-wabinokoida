<?php
if ($opcion == "nuevo") 
{
	$field['CodOrganismo'] = $fCodOrganismo?$fCodOrganismo:$_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$field['PeriodoContable'] = $PeriodoActual;
	$field['FechaTransaccion'] = $FechaActual;
	$field['PreparadoPor'] = $_SESSION['CODPERSONA_ACTUAL'];
	$field['NomPreparadoPor'] = $_SESSION['NOMBRE_USUARIO_ACTUAL'];
	$field['FechaPreparacion'] = $FechaActual;
	$field['Estado'] = 'PR';
	##
	$_titulo = "Transacciones Bancarias / Nuevo";
	$accion = "nuevo";
	$disabled_modificar = "";
	$disabled_ver = "";
	$disabled_presupuesto = "disabled";
	$display_submit = "";
	$label_submit = "Guardar";
	$focus = "Comentarios";
}
elseif ($opcion == "modificar" || $opcion == "ver" || $opcion == "actualizar" || $opcion == "desactualizar" || $opcion == "anular") 
{
	list($NroTransaccion, $Secuencia) = explode('_', $sel_registros);
	##	consulto datos generales
	$sql = "SELECT
				bt.*,
				p1.NomCompleto AS NomPreparadoPor
			FROM
				ap_bancotransaccion bt
				LEFT JOIN mastpersonas p1 ON p1.CodPersona = bt.PreparadoPor
			WHERE
				bt.NroTransaccion = '$NroTransaccion'
				AND bt.Secuencia = '$Secuencia'";
	$field = getRecord($sql);
	##
	if ($opcion == "modificar") {
		$_titulo = "Transacciones Bancarias / Modificar";
		$accion = "modificar";
		$disabled_modificar = "disabled";
		$disabled_presupuesto = ($field['FlagPresupuesto']=='S'?'':'disabled');
		$disabled_ver = "";
		$display_submit = "";
		$label_submit = "Guardar Cambios";
		$focus = "Comentarios";
	}
	##
	elseif ($opcion == "ver") {
		$_titulo = "Transacciones Bancarias / Ver";
		$accion = "";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$disabled_presupuesto = "disabled";
		$display_submit = "display:none;";
		$label_submit = "";
		$focus = "btCancelar";
	}
	##
	elseif ($opcion == "actualizar") {
		$field['ActualizadoPor'] = $_SESSION['CODPERSONA_ACTUAL'];
		$field['NomActualizadoPor'] = $_SESSION['NOMBRE_USUARIO_ACTUAL'];
		$field['FechaActualizado'] = $FechaActual;
		##	
		$_titulo = "Transacciones Bancarias / Actualizar";
		$accion = "actualizar";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$disabled_presupuesto = "disabled";
		$display_submit = "";
		$label_submit = "Actualizar";
		$focus = "btCancelar";
	}
	##
	elseif ($opcion == "desactualizar") {
		$_titulo = "Transacciones Bancarias / Desactualizar";
		$accion = "desactualizar";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$disabled_presupuesto = "disabled";
		$display_submit = "";
		$label_submit = "Desactualizar";
		$focus = "btCancelar";
	}
	##
	elseif ($opcion == "anular") {
		$_titulo = "Transacciones Bancarias / Anular";
		$accion = "anular";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$disabled_presupuesto = "disabled";
		$display_submit = "";
		$label_submit = "Anular";
		$focus = "btCancelar";
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
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=<?=$action?>" method="POST" enctype="multipart/form-data" onsubmit="return formulario('ap_bancotransaccion_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="opcion" id="opcion" value="<?=$opcion?>" />
	<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
	<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
	<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
	<input type="hidden" name="fCodTipoTransaccion" id="fCodTipoTransaccion" value="<?=$fCodTipoTransaccion?>" />
	<input type="hidden" name="fCodTipoDocumento" id="fCodTipoDocumento" value="<?=$fCodTipoDocumento?>" />
	<input type="hidden" name="fFechaTransaccionD" id="fFechaTransaccionD" value="<?=$fFechaTransaccionD?>" />
	<input type="hidden" name="fFechaTransaccionH" id="fFechaTransaccionH" value="<?=$fFechaTransaccionH?>" />
	<input type="hidden" name="fCodBanco" id="fCodBanco" value="<?=$fCodBanco?>" />
	<input type="hidden" name="fNroCuenta" id="fNroCuenta" value="<?=$fNroCuenta?>" />
	<input type="hidden" name="fFlagAutomatico" id="fFlagAutomatico" value="<?=$fFlagAutomatico?>" />
	<input type="hidden" name="GenerarVoucher" id="GenerarVoucher" value="" />

	<table width="<?=$_width?>" class="tblForm">
		<tr>
	    	<td colspan="4" class="divFormCaption">INFORMACI&Oacute;N GENERAL</td>
	    </tr>
	    <tr>
			<td class="tagForm" width="125">* Organismo:</td>
			<td>
				<select name="CodOrganismo" id="CodOrganismo" style="width:290px;" <?=$disabled_modificar?>>
					<?=getOrganismos($fCodOrganismo, 3);?>
				</select>
			</td>
			<td class="tagForm"># Transacci&oacute;n:</td>
			<td>
				<input type="text" name="NroTransaccion" id="NroTransaccion" value="<?=$field['NroTransaccion']?>" style="width:100px;" readonly>
			</td>
		</tr>
	    <tr>
			<td class="tagForm">Periodo:</td>
			<td>
				<input type="text" name="PeriodoContable" id="PeriodoContable" value="<?=$field['PeriodoContable']?>" style="width:100px;" readonly>
			</td>
			<td class="tagForm">Estado:</td>
			<td>
				<input type="hidden" name="Estado" id="Estado" value="<?=$field['Estado']?>" />
				<input type="text" value="<?=strtoupper(printValores('ESTADO-BANCARIO',$field['Estado']))?>" style="width:100px; font-weight:bold;" disabled />
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Fecha:</td>
			<td>
				<input type="text" name="FechaTransaccion" id="FechaTransaccion" value="<?=formatFechaDMA($field['FechaTransaccion'])?>" style="width:100px;" class="datepicker" <?=$disabled_ver?> onchange="$('#PeriodoContable').val($(this).val().substr(6, 4)+'-'+$(this).val().substr(3, 2));">
			</td>
			<td>&nbsp;</td>
			<td>
				<input type="checkbox" name="FlagPresupuesto" id="FlagPresupuesto" value="S" <?=chkFlag($field['FlagPresupuesto'])?> <?=$disabled_ver?> onchange="$('#btSelCodPartida').prop('disabled', !this.checked); $('.presupuesto').val('');"> Afecta Presupuesto
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Comentarios:</td>
			<td colspan="3">
	        	<textarea name="Comentarios" id="Comentarios" style="width:95%; height:50px;" <?=$disabled_ver?>><?=$field['Comentarios']?></textarea>
			</td>
		</tr>
		<tr>
			<td class="tagForm">Preparado Por:</td>
			<td>
				<input type="hidden" name="PreparadoPor" id="PreparadoPor" value="<?=$field['PreparadoPor']?>" />
				<input type="text" name="NomPreparadoPor" id="NomPreparadoPor" value="<?=$field['NomPreparadoPor']?>" style="width:220px;" disabled />
				<input type="text" name="FechaPreparacion" id="FechaPreparacion" value="<?=formatFechaDMA($field['FechaPreparacion'])?>" style="width:65px;" maxlength="10" readonly />
			</td>
			<td class="tagForm">Actualizado Por:</td>
			<td>
				<input type="hidden" name="ActualizadoPor" id="ActualizadoPor" value="<?=$field['ActualizadoPor']?>" />
				<input type="text" name="NomActualizadoPor" id="NomActualizadoPor" value="<?=$field['NomActualizadoPor']?>" style="width:220px;" disabled />
				<input type="text" name="FechaActualizado" id="FechaActualizado" value="<?=formatFechaDMA($field['FechaActualizado'])?>" style="width:65px;" maxlength="10" readonly />
			</td>
		</tr>
	    <tr>
			<td class="tagForm">&Uacute;ltima Modif.:</td>
			<td colspan="3">
				<input type="text" value="<?=$field['UltimoUsuario']?>" style="width:165px;" disabled="disabled" />
				<input type="text" value="<?=$field['UltimaFecha']?>" style="width:120px" disabled="disabled" />
			</td>
		</tr>
	</table>

	<input type="hidden" id="sel_detalle" />
	<table width="<?=$_width?>;" class="tblBotones">
		<thead>
			<tr>
				<th class="divFormCaption" colspan="2">TRANSACCIÓN BANCARIA</th>
			</tr>
		</thead>
	    <tbody>
		    <tr>
		        <td class="gallery clearfix">
					<a id="aSelCodPersona" href="../lib/listas/gehen.php?anz=lista_personas&filtrar=default&campo1=detalle_CodProveedor&ventana=selListadoListaParent&seldetalle=sel_detalle&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe2]" style="display:none;"></a>
					<input type="button" style="width:85px;" id="btSelCodPersona" value="Sel. Persona" onclick="validarAbrirLista('sel_detalle', 'aSelCodPersona');" <?=$disabled_ver?> />

					<a id="aSelCodCentroCosto" href="../lib/listas/gehen.php?anz=lista_centro_costos&filtrar=default&campo1=detalle_CodCentroCosto&campo2=detalle_NomCentroCosto&ventana=selListadoListaParent&seldetalle=sel_detalle&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe3]" style="display:none;"></a>
					<input type="button" style="width:85px;" id="btSelCodCentroCosto" value="Sel. C.Costo" onclick="validarAbrirLista('sel_detalle', 'aSelCodCentroCosto');" <?=$disabled_ver?> />

					<a id="aSelCodPartida" href="../lib/listas/gehen.php?anz=lista_pv_partida_presupuesto&filtrar=default&campo1=detalle_CodPartida&campo2=detalle_CodFuente&campo3=detalle_CodPresupuesto&campo4=detalle_CategoriaProg&ventana=selListadoListaParent&seldetalle=sel_detalle&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe4]" style="display:none;"></a>
					<input type="button" style="width:85px;" id="btSelCodPartida" value="Sel. Partida" onclick="validarAbrirLista('sel_detalle', 'aSelCodPartida');" <?=$disabled_presupuesto?> />
		        </td>
		        <td align="right" class="gallery clearfix">
		        	<a id="a_transacciones" href="../lib/listas/gehen.php?anz=lista_ap_bancotipotransaccion&filtrar=default&ventana=ap_bancotransaccion_insertar&detalle=detalle&modulo=ajax&accion=detalle_insertar&url=../../ap/ap_bancotransaccion_ajax.php&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" style="display:none;"></a>
		            <input type="button" style="width:85px;" value="Insertar" onclick="$('#a_transacciones').click();" <?=$disabled_modificar?> />
		            <input type="button" style="width:85px;" value="Borrar" onclick="quitar(this, 'detalle');" <?=$disabled_modificar?> />
		        </td>
		    </tr>
	    </tbody>
	</table>
	<div style="overflow:scroll; height:175px; width:<?=$_width?>px; margin:auto;">
		<table class="tblLista" style="width:100%; min-width:1600px;">
			<thead>
				<tr>
					<th width="25">#</th>
					<th align="left" colspan="2">Tipo de Transacci&oacute;n *</th>
					<th width="25">I/E</th>
					<th width="150">Cta. Bancaria *</th>
					<th width="125">Monto *</th>
					<th width="150">Documento</th>
					<th width="150">Doc. Referencia *</th>
					<th width="60">Persona *</th>
					<th width="60">C.C *</th>
					<th width="80">Partida</th>
					<th width="100">Cat. Prog.</th>
					<th width="35">F.F</th>
				</tr>
			</thead>
			
			<tbody id="lista_detalle">
				<?php
				$Secuencia = 0;
				$sql = "SELECT
							bt.*,
							btt.Descripcion AS NomTipoTransaccion,
							cc.Codigo AS NomCentroCosto
						FROM
							ap_bancotransaccion bt
							INNER JOIN ap_bancotipotransaccion btt ON btt.CodTipoTransaccion = bt.CodTipoTransaccion
							LEFT JOIN ac_mastcentrocosto cc ON (cc.CodCentroCosto = bt.CodCentroCosto)
						WHERE bt.NroTransaccion = '$NroTransaccion'
						ORDER BY Secuencia";
				$field_detalle = getRecords($sql);
				foreach ($field_detalle as $f) 
				{
					$id = $f['CodTipoTransaccion'];
					++$Secuencia;
					?>
					<tr class="trListaBody" id="detalle_<?=$id?>" onclick="clk($(this), 'detalle', 'detalle_<?=$id?>');">
						<th><?=$Secuencia?></th>
						<td align="center" width="50">
							<input type="hidden" name="detalle_Secuencia[]" value="<?=$f['Secuencia']?>" />
							<input type="hidden" name="detalle_CodTipoTransaccion[]" value="<?=$f['CodTipoTransaccion']?>" />
							<?=$f['CodTipoTransaccion']?>
						</td>
						<td><?=$f['NomTipoTransaccion']?></td>
						<td><input type="text" name="detalle_TipoTransaccion[]" value="<?=$f['TipoTransaccion']?>" class="cell" style="text-align:center;" readonly /></td>
						<td>
							<select name="detalle_NroCuenta[]" class="cell">
				                <?=loadSelect2("ap_ctabancaria","NroCuenta","NroCuenta",$f['NroCuenta'])?>
							</select>
						</td>
						<td><input type="text" name="detalle_Monto[]" value="<?=number_format($f['Monto'],2,',','.')?>" class="cell currency" style="text-align:right;" /></td>
						<td>
							<select name="detalle_CodTipoDocumento[]" class="cell">
								<option value="">&nbsp;</option>
				                <?=getMiscelaneos($f['CodTipoDocumento'],"TIPOTRBANC")?>
							</select>
						</td>
						<td><input type="text" name="detalle_CodigoReferenciaBanco[]" value="<?=$f['CodigoReferenciaBanco']?>" class="cell" maxlength="20" /></td>
						<td><input type="text" name="detalle_CodProveedor[]" id="detalle_CodProveedor_<?=$id?>" value="<?=$f['CodProveedor']?>" class="cell" style="text-align:center;" onchange="getDescripcionLista2('accion=getDescripcionPersona', $(this));" /></td>
						<td>
							<input type="hidden" name="detalle_CodCentroCosto[]" id="detalle_CodCentroCosto_<?=$id?>" value="<?=$f['CodCentroCosto']?>" />
							<input type="text" name="detalle_NomCentroCosto[]" id="detalle_NomCentroCosto_<?=$id?>" value="<?=$f['NomCentroCosto']?>" class="cell" style="text-align:center;" onchange="getDescripcionLista2('accion=getCCosto', $(this), $('detalle_CodCentroCosto_<?=$id?>'));" />
						</td>
						<td><input type="text" name="detalle_CodPartida[]" id="detalle_CodPartida_<?=$id?>" value="<?=$f['detalle_CodPartida']?>" class="cell presupuesto" style="text-align:center;" readonly /></td>
						<td>
							<input type="hidden" name="detalle_CodPresupuesto[]" id="detalle_CodPresupuesto_<?=$id?>" value="<?=$f['detalle_CodPresupuesto']?>" class="presupuesto" />
							<input type="text" name="detalle_CategoriaProg[]" id="detalle_CategoriaProg_<?=$id?>" value="<?=$f['CategoriaProg']?>" class="cell presupuesto" style="text-align:center;" readonly />
						</td>
						<td><input type="text" name="detalle_CodFuente[]" id="detalle_CodFuente_<?=$id?>" value="<?=$f['CodFuente']?>" class="cell presupuesto" style="text-align:center;" readonly /></td>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
	</div>
	<input type="hidden" id="nro_detalle" value="<?=$nro_detalle?>" />
	<input type="hidden" id="can_detalle" value="<?=$nro_detalle?>" />

	<center>
		<input type="submit" value="<?=$label_submit?>" style="width:100px; <?=$display_submit?>" id="btSubmit" />
		<input type="button" value="Cancelar" style="width:100px;" id="btCancelar" onclick="<?=$clkCancelar?>" />
	</center>
</form>
<div style="width:<?=$_width?>px" class="divMsj">Campos Obligatorios *</div>

<script type="text/javascript" language="javascript">
	//	valido formulario
	function formulario(url, data) {
		bloqueo(true);
		if (!form) var form = document.getElementById($('form').attr('id'));
		var idform = form.id;
		//	ajax
		$.ajax({
			type: "POST",
			url: url + ".php",
			data: data+"&"+$('#'+idform).serialize(),
			async: false,
			success: function(resp) {
				var datos = resp.split('|');
				if (datos[0].trim() != '') cajaModal(datos[0]);
				else {
					if ('<?=$accion?>' == 'actualizar') {
						$('#GenerarVoucher').val(datos[1]);
					}
					form.submit();
				}
			}
		});
		return false;
	}
</script>