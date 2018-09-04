<?php
if ($opcion == "nuevo") {
	$field['Estado'] = 'A';
	##
	$_titulo = "Vehiculos / Nuevo Registro";
	$accion = "nuevo";
	$disabled_modificar = "";
	$disabled_ver = "";
	$read_modificar = "";
	$display_modificar = "";
	$display_ver = "";
	$display_submit = "";
	$label_submit = "Guardar";
	$focus = "Placa";
}
elseif ($opcion == "modificar" || $opcion == "ver") {
	##	consulto datos generales
	$sql = "SELECT
				v.*,
				p1.NomCompleto AS NomEmpresa,
				p1.DocFiscal AS DocFiscalEmpresa
			FROM lg_vehiculos v
			LEFT JOIN mastpersonas p1 ON p1.CodPersona = V.CodEmpresa
			WHERE v.CodVehiculo = '$sel_registros'";
	$field = getRecord($sql);
	##
	if ($opcion == "modificar") {
		$_titulo = "Vehiculos / Modificar Registro";
		$accion = "modificar";
		$disabled_modificar = "disabled";
		$disabled_ver = "";
		$read_modificar = "readonly";
		$display_modificar = "display:none;";
		$display_ver = "";
		$display_submit = "";
		$label_submit = "Modificar";
		$focus = "Comentarios";
	}
	##
	elseif ($opcion == "ver") {
		$_titulo = "Vehiculos / Ver Registro";
		$accion = "";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$read_modificar = "readonly";
		$display_modificar = "display:none;";
		$display_ver = "display:none;";
		$display_submit = "display:none;";
		$label_submit = "";
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
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lg_vehiculos_lista" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('lg_vehiculos_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
	<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
	<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
	<input type="hidden" name="CodVehiculo" id="CodVehiculo" value="<?=$field['CodVehiculo']?>" />

	<table style="width:100%; max-width:<?=$_width?>px;" class="tblForm">
		<tr>
	    	<td colspan="4" class="divFormCaption">Datos Generales</td>
	    </tr>
	    <tr>
			<td class="tagForm" width="150">* Placa:</td>
			<td>
	        	<input type="text" name="Placa" id="Placa" value="<?=$field['Placa']?>" maxlength="20" style="width:100px; font-weight:bold;" <?=$disabled_modificar?> />
			</td>
			<td class="tagForm" width="125">Chofer:</td>
			<td>
				<select name="CodChofer" id="CodChofer" style="width:225px;" <?=$disabled_ver?>>
					<option value="">&nbsp;</option>
					<?=choferes($field['CodChofer'])?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Empr. Transportista:</td>
			<td class="gallery clearfix">
				<input type="hidden" name="CodEmpresa" id="CodEmpresa" value="<?=$field['CodEmpresa']?>" />
				<input type="text" name="NomEmpresa" id="NomEmpresa" value="<?=htmlentities($field['NomEmpresa'])?>" style="width:225px;" readonly />
				<a href="../lib/listas/gehen.php?anz=lista_personas&campo1=CodEmpresa&campo2=NomEmpresa&campo3=DocFiscalEmpresa&ventana=DocFiscal&filtrar=default&concepto=80-0003&_APLICACION=<?=$_APLICACION?>&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" style=" <?=$display_ver?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
			<td class="tagForm">Doc. Fiscal:</td>
			<td>
				<input type="text" name="DocFiscalEmpresa" id="DocFiscalEmpresa" value="<?=htmlentities($field['DocFiscalEmpresa'])?>" style="width:100px;" disabled />
			</td>
		</tr>
		<tr>
			<td class="tagForm">Modelo:</td>
			<td>
				<input type="text" name="Modelo" id="Modelo" value="<?=htmlentities($field['Modelo'])?>" maxlength="255" style="width:225px;" <?=$disabled_ver?> />
			</td> 
			<td class="tagForm">Año:</td>
			<td>
				<input type="text" name="Anio" id="Anio" value="<?=htmlentities($field['Anio'])?>" maxlength="4" style="width:100px;" <?=$disabled_ver?> />
			</td>
		</tr>
		<tr>
			<td class="tagForm">Marca:</td>
			<td>
				<select name="Marca" id="Marca" style="width:225px;" <?=$disabled_ver?>>
					<option value="">&nbsp;</option>
					<?=getMiscelaneos($field['Marca'],"MARCAUTO")?>
				</select>
			</td>
			<td class="tagForm">Clase:</td>
			<td>
				<select name="Clase" id="Clase" style="width:225px;" <?=$disabled_ver?>>
					<option value="">&nbsp;</option>
					<?=getMiscelaneos($field['Clase'],"CLASEAUTO")?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="tagForm">Tipo:</td>
			<td>
				<select name="Tipo" id="Tipo" style="width:225px;" <?=$disabled_ver?>>
					<option value="">&nbsp;</option>
					<?=getMiscelaneos($field['Tipo'],"TIPOAUTO")?>
				</select>
			</td>
			<td class="tagForm">Uso:</td>
			<td>
				<select name="Uso" id="Uso" style="width:225px;" <?=$disabled_ver?>>
					<option value="">&nbsp;</option>
					<?=getMiscelaneos($field['Uso'],"USOAUTO")?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="tagForm">Color:</td>
			<td>
				<select name="Color" id="Color" style="width:225px;" <?=$disabled_ver?>>
					<option value="">&nbsp;</option>
					<?=getMiscelaneos($field['Color'],"COLOR")?>
				</select>
			</td>
			<td class="tagForm">Capacidad (Kgs.):</td>
			<td>
				<input type="text" name="Capacidad" id="Capacidad" value="<?=htmlentities($field['Capacidad'])?>" maxlength="4" style="width:100px;" <?=$disabled_ver?> />
			</td>
		</tr>
		<tr>
			<td class="tagForm">Serial (Motor):</td>
			<td>
				<input type="text" name="SerialMotor" id="SerialMotor" value="<?=htmlentities($field['SerialMotor'])?>" maxlength="255" style="width:225px;" <?=$disabled_ver?> />
			</td>
			<td class="tagForm">Serial (Carroceria):</td>
			<td>
				<input type="text" name="SerialCarroceria" id="SerialCarroceria" value="<?=htmlentities($field['SerialCarroceria'])?>" maxlength="255" style="width:225px;" <?=$disabled_ver?> />
			</td>
		</tr>
		<tr>
			<td class="tagForm">Peso:</td>
			<td>
				<input type="text" name="Peso" id="Peso" value="<?=number_format($field['Peso'],2,',','.')?>" style="width:100px; text-align: right;" class="currency" <?=$disabled_ver?> />
			</td>
			<td class="tagForm">&nbsp;</td>
			<td>
	            <input type="checkbox" name="FlagVehiculoPropio" id="FlagVehiculoPropio" value="S" <?=chkOpt($field['FlagVehiculoPropio'], "S");?> <?=$disabled_ver?> /> Vehiculo Propio
			</td>
		</tr>
		<tr>
			<td class="tagForm">Comentarios:</td>
			<td colspan="3">
				<textarea name="Comentarios" id="Comentarios" style="width:90%;" <?=$disabled_ver?>><?=htmlentities($field['Comentarios'])?></textarea>
			</td>
		</tr>
		<tr>
			<td class="tagForm">Estado:</td>
			<td colspan="3">
	            <input type="radio" name="Estado" id="Activo" value="A" <?=chkOpt($field['Estado'], "A");?> <?=$disabled_ver?> /> Activo
	            &nbsp; &nbsp;
	            <input type="radio" name="Estado" id="Inactivo" value="I" <?=chkOpt($field['Estado'], "I");?> <?=$disabled_ver?> /> Inactivo
			</td>
		</tr>
	    <tr>
			<td class="tagForm">&Uacute;ltima Modif.:</td>
			<td colspan="3">
				<input type="text" value="<?=$field['UltimoUsuario']?>" style="width:145px;" disabled="disabled" />
				<input type="text" value="<?=$field['UltimaFecha']?>" style="width:145px" disabled="disabled" />
			</td>
		</tr>
	</table>

	<center>
		<input type="submit" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" id="btSubmit" />
		<input type="button" value="Cancelar" style="width:75px;" id="btCancelar" onclick="<?=$clkCancelar?>" />
	</center>
</form>
<div style="width:100%; max-width:<?=$_width?>px;" class="divMsj">Campos Obligatorios *</div>

<script type="text/javascript">
	function get_persona(inputs) {
		$.ajax({
			type: "POST",
			url: "lg_vehiculos_ajax.php",
			data: 'modulo=ajax&accion=get_persona&Ndocumento='+$('#Ndocumento').val(),
			async: true,
			success: function(resp) {
				var data = resp.split("|");

				$('#CodEstado').empty().append(data[15]);
				$('#CodMunicipio').empty().append(data[16]);
				$('#CodCiudad').empty().append(data[17]);

				if (inputs) {
					for(var i=0; i<inputs.length; i++) {
						if ($("#"+inputs[i]).length > 0) $("#"+inputs[i]).val(data[i]);
					}
				}
			}
		});
	}
</script>