<?php
list($CodOrganismo, $CodTipoNom, $Periodo, $CodTipoProceso) = explode('_', $sel_registros);
##	proceso
$sql = "SELECT *
		FROM pr_procesoperiodo
		WHERE
			CodOrganismo = '".$CodOrganismo."' AND
			CodTipoNom = '".$CodTipoNom."' AND
			Periodo = '".$Periodo."' AND
			CodTipoProceso = '".$CodTipoProceso."'";
$field_proceso = getRecord($sql);
##
$_titulo = "Agregar Complementos";
$accion = "horas";
$disabled_ver = "";
$display_submit = "";
$label_submit = "Guardar";
$focus = "btSubmit";
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

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=pr_complemento_sueldos_lista" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('pr_complemento_sueldos_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
	<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
	<input type="hidden" name="fCodTipoNom" id="fCodTipoNom" value="<?=$fCodTipoNom?>" />
	<input type="hidden" name="fPeriodo" id="fPeriodo" value="<?=$fPeriodo?>" />
	<input type="hidden" name="fCodTipoProceso" id="fCodTipoProceso" value="<?=$fCodTipoProceso?>" />

	<table width="<?=$_width?>" class="tblForm">
		<tr>
	    	<td colspan="4" class="divFormCaption">Datos del Proceso</td>
	    </tr>
	    <tr>
			<td class="tagForm">Organismo:</td>
			<td>
	        	<select name="CodOrganismo" id="CodOrganismo" style="width:275px;" class="disabled" <?=$disabled_ver?>>
					<?=loadSelect('mastorganismos','CodOrganismo','Organismo', $field_proceso['CodOrganismo'])?>
				</select>
			</td>
			<td class="tagForm">N&oacute;mina:</td>
			<td>
	        	<select name="CodTipoNom" id="CodTipoNom" style="width:275px;" class="disabled" <?=$disabled_ver?>>
					<?=loadSelect("tiponomina", "CodTipoNom", "Nomina", $field_proceso['CodTipoNom'], 1)?>
				</select>
			</td>
		</tr>
	    <tr>
			<td class="tagForm">Periodo:</td>
			<td>
				<input type="text" name="Periodo" id="Periodo" value="<?=$field_proceso['Periodo']?>" style="width:50px;" readonly />
			</td>
			<td class="tagForm">* Proceso:</td>
			<td>
				<select name="CodTipoProceso" id="CodTipoProceso" style="width:275px;" class="disabled" <?=$disabled_ver?>>
					<?=loadSelect("pr_tipoproceso", "CodTipoProceso", "Descripcion", $field_proceso['CodTipoProceso'], 1)?>
				</select>
			</td>
		</tr>
		<tr>
	    	<td colspan="4" class="divFormCaption">Datos del Empleado</td>
	    </tr>
	    <tr>
			<td class="tagForm">* Empleado:</td>
			<td class="gallery clearfix">
				<input type="hidden" name="CodPersona" id="CodPersona" value="<?=$field['CodPersona']?>" />
				<input type="text" name="CodEmpleado" id="CodEmpleado" value="<?=$field['CodEmpleado']?>" style="width:50px;" readonly />
				<input type="text" name="NomPersona" id="NomPersona" value="<?=$field['NomPersona']?>" style="width:221px;" disabled />
				<a href="../lib/listas/gehen.php?anz=lista_empleados&filtrar=default&ventana=pr_complemento_sueldos_horas&FlagNomina=S&fCodTipoNom=<?=$field_proceso['CodTipoNom']?>&campo1=CodPersona&campo2=NomPersona&campo3=CodEmpleado&campo4=Ndocumento&campo5=CodDependencia&campo6=CodCargo&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" style=" <?=$display_ver?>">
					<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
				</a>
			</td>
			<td class="tagForm">Documento:</td>
			<td>
				<input type="text" name="Ndocumento" id="Ndocumento" value="<?=$field_proceso['Ndocumento']?>" style="width:100px;" disabled />
			</td>
		</tr>
	    <tr>
			<td class="tagForm">Dependencia:</td>
			<td>
				<select name="CodDependencia" id="CodDependencia" style="width:275px;" disabled>
					<option value="">&nbsp;</option>
					<?=loadSelect("mastdependencias", "CodDependencia", "Dependencia", $field_proceso['CodDependencia'], 0)?>
				</select>
			</td>
			<td class="tagForm">Cargo:</td>
			<td>
				<select name="CodCargo" id="CodCargo" style="width:275px;" disabled>
					<option value="">&nbsp;</option>
					<?=loadSelect("rh_puestos", "CodCargo", "DescripCargo", $field_proceso['CodCargo'], 0)?>
				</select>
			</td>
		</tr>
	</table>

	<center>
		<input type="submit" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" id="btSubmit" />
		<input type="button" value="Cancelar" style="width:75px;" id="btCancelar" onclick="<?=$clkCancelar?>" />
	</center>

	<input type="hidden" id="sel_detalle" />
	<table width="<?=$_width?>" class="tblBotones">
		<thead>
			<tr>
				<th class="divFormCaption" colspan="2">Horas Extras</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td align="right">
					<input type="button" class="btLista" value="Insertar" onclick="insertar_detalle();" <?=$disabled_ver?> />
					<input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'detalle'); setMontos();" <?=$disabled_ver?> />
				</td>
			</tr>
		</tbody>
	</table>
	<div style="overflow:scroll; width:<?=$_width?>px; height:200px; margin:auto;">
		<table class="tblLista" style="width:100%; min-width:<?=$_width?>px;">
			<thead>
				<tr>
					<th width="20">#</th>
					<th width="60">Fecha</th>
					<th width="60">Hora Salida</th>
					<th width="60">HED</th>
					<th width="60">HEN</th>
					<th width="35">Tipo</th>
					<th width="60">Hora Entrada</th>
					<th width="60">Hora Salida</th>
					<th align="left">Observaciones</th>
				</tr>
			</thead>
			
			<tbody id="lista_detalle">
			</tbody>
		</table>
	</div>
	<table width="<?=$_width?>" align="center" class="tblBotones">
		<tr>
			<th>
				HORAS EXTRAS DIURNAS: <input type="text" name="TotalHED" id="TotalHED" style="text-align:right; font-weight:bold; width:25px;" readonly>
			</th>
			<th>
				HORAS EXTRAS NOCTURNAS: <input type="text" name="TotalHEN" id="TotalHEN" style="text-align:right; font-weight:bold; width:25px;" readonly>
			</th>
		</tr>
	</table>
	<input type="hidden" id="nro_detalle" value="<?=$nro_detalle?>" />
	<input type="hidden" id="can_detalle" value="<?=$nro_detalle?>" />

</form>
<div style="width:<?=$_width?>px" class="divMsj">Campos Obligatorios *</div>

<script type="text/javascript">
	function insertar_detalle() {
		if ($('#CodPersona').val() == '') cajaModal('Debe seleccionar el Empleado','error');
		else insertar2(this, 'detalle', 'modulo=ajax&accion=hora_insertar', 'pr_complemento_sueldos_ajax.php');
	}
	function getHorario(Fecha, id) {
		$.ajax({
			type: "POST",
			url: "pr_complemento_sueldos_ajax.php",
			data: "modulo=ajax&accion=getHorario&Fecha="+Fecha+"&CodPersona="+$('#CodPersona').val(),
			async: false,
			dataType: "json",
			success: function(data) {
				$('#detalle_HoraEntrada'+id).val(data['HoraEntrada']);
				$('#detalle_HoraSalida'+id).val(data['HoraSalida']);
			}
		});
	}
	function getTotal() {
		//	TOTAL GENERAL
		var TotalHED = 0;
		var TotalHEN = 0;
		$('input[name="detalle_HED[]"]').each(function(idx) {
			var HED = $(this).val().split(':');
			var Hora = new Number(HED[0]);
			TotalHED = TotalHED + Hora;

		});
		$('input[name="detalle_HEN[]"]').each(function(idx) {
			var HEN = $(this).val().split(':');
			var Hora = new Number(HEN[0]);
			TotalHEN = TotalHEN + Hora;
		});
		$('#TotalHED').val(TotalHED);
		$('#TotalHEN').val(TotalHEN);
	}
	function getTotalDia(id) {
		//	hora de inicio de calculo
		//var HoraSalida = formatHora($('#detalle_HoraSalida'+id).val());
		var HoraSalida = formatHora("05:00 pm");
		var HS = HoraSalida.split(':');
		var h1 = parseInt(HS[0]);
		var m1 = parseInt(HS[1]);
		//	hora de fin de calculo
		var HoraSalidaReal = formatHora($('#detalle_HoraSalidaReal'+id).val());
		var HSR = HoraSalidaReal.split(':');
		var h2 = parseInt(HSR[0]);
		var m2 = parseInt(HSR[1]);

		if (h2 >= 17 && h2 < 19) {
			//	restar horas
			var horas = h2 - h1;
			//	restar minuctos
			var minutos = m2 - m1;
			if (minutos < 0) {
				minutos = minutos + 60;
				horas = horas - 1;
			}
			//	totalizo
			$('#detalle_HED'+id).val(horas+':'+minutos);
		}
		else if (h2 < 8 || h2 >= 19) {
			h2 = 19;
			m2 = 0;
			//	restar horas
			var horas1 = h2 - h1;
			//	restar minuctos
			var minutos1 = m2 - m1;
			//	si los minutos son menos que 0
			if (minutos1 < 0) {
				minutos1 = minutos1 + 60;
				horas1 = horas1 - 1;
			}
			//	totalizo
			$('#detalle_HED'+id).val(horas1+':'+minutos1);

			h1 = 19;
			m1 = 0;
			h2 = parseInt(HSR[0]);
			m2 = parseInt(HSR[1]);
			//	restar horas
			var horas2 = h2 - h1;
			if (horas2 < 0) {
				horas2 = horas2 + 24;
			}
			//	restar minuctos
			var minutos2 = m2 - m1;
			if (minutos2 < 0) {
				minutos2 = minutos2 + 60;
				horas2 = horas2 - 1;
			}
			//	totalizo
			$('#detalle_HEN'+id).val(horas2+':'+minutos2);
		}
		else {
			//	totalizo
			$('#detalle_HED'+id).val('0:0');
			$('#detalle_HEN'+id).val('0:0');
		}

		getTotal();
	}
</script>