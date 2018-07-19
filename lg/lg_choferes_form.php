<?php
if ($opcion == "nuevo") {
	$field['Estado'] = 'A';
	$field['CodPais'] = $_PARAMETRO['PAISDEFAULT'];
	$field['CodEstado'] = $_PARAMETRO['ESTADODEFAULT'];
	$field['CodMunicipio'] = $_PARAMETRO['MUNICIPIODEFAULT'];
	$field['CodCiudad'] = $_PARAMETRO['CIUDADDEFAULT'];
	##
	$_titulo = "Choferes / Nuevo Registro";
	$accion = "nuevo";
	$disabled_modificar = "";
	$disabled_ver = "";
	$read_modificar = "";
	$display_modificar = "";
	$display_ver = "";
	$display_submit = "";
	$label_submit = "Guardar";
	$focus = "CodPersona";
}
elseif ($opcion == "modificar" || $opcion == "ver") {
	##	consulto datos generales
	$sql = "SELECT
				c.*,
				p.Nombres,
				p.Apellido1,
				p.Apellido2,
				p.Ndocumento,
				p.TipoLicencia,
				p.Nlicencia,
				p.ExpiraLicencia,
				p.Direccion,
				p.Telefono1,
				p.Telefono2,
				p.EstadoCivil,
				p.CiudadDomicilio AS CodCiudad,
				p.Sexo,
				p.Fnacimiento,
				m.CodMunicipio,
				e.CodEstado,
				pi.CodPais
			FROM lg_choferes c
			INNER JOIN mastpersonas p ON p.CodPersona = c.CodPersona
			LEFT JOIN mastciudades c ON (c.CodCiudad = p.CiudadDomicilio)
			LEFT JOIN mastmunicipios m ON (m.CodMunicipio = c.CodMunicipio)
			LEFT JOIN mastestados e ON (e.CodEstado = m.CodEstado)
			LEFT JOIN mastpaises pi ON (pi.CodPais = e.CodPais)
			WHERE c.CodChofer = '$sel_registros'";
	$field = getRecord($sql);
	##
	if ($opcion == "modificar") {
		$_titulo = "Choferes / Modificar Registro";
		$accion = "modificar";
		$disabled_modificar = "disabled";
		$disabled_ver = "";
		$read_modificar = "readonly";
		$display_modificar = "display:none;";
		$display_ver = "";
		$display_submit = "";
		$label_submit = "Modificar";
		$focus = "EquipoVenta";
	}
	##
	elseif ($opcion == "ver") {
		$_titulo = "Choferes / Ver Registro";
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
$_width = 700;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lg_choferes_lista" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('lg_choferes_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
	<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
	<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />

	<table style="width:100%; max-width:<?=$_width?>px;" class="tblForm">
		<tr>
	    	<td colspan="4" class="divFormCaption">Datos Generales</td>
	    </tr>
	    <tr>
			<td class="tagForm" width="125">C&oacute;digo:</td>
			<td>
	        	<input type="text" name="CodChofer" id="CodChofer" value="<?=$field['CodChofer']?>" style="width:75px; font-weight:bold;" readonly />
			</td>
			<td class="tagForm" width="125">* Documento:</td>
			<td class="gallery clearfix">
				<input type="text" name="Ndocumento" id="Ndocumento" value="<?=$field['Ndocumento']?>" style="width:75px;" onchange="get_persona(['CodPersona','Apellido1','Apellido2','Nombres','EstadoCivil','Telefono1','Telefono2','Direccion','CodCiudad','CodMunicipio','CodEstado','CodPais','TipoLicencia','Nlicencia','ExpiraLicencia','Fnacimiento','Sexo'])" <?=$disabled_ver?> />
				<a href="../lib/listas/gehen.php?anz=lista_personas&campo1=CodPersona&campo2=Ndocumento&campo3=Apellido1&campo4=Apellido2&campo5=Nombres&campo6=EstadoCivil&campo7=Telefono1&campo8=Telefono2&campo9=Direccion&campo10=CodCiudad&campo11=CodMunicipio&campo12=CodEstado&campo13=CodPais&campo14=TipoLicencia&campo15=Nlicencia&campo16=ExpiraLicencia&campo17=Fnacimiento&campo18=Sexo&ventana=lg_choferes&filtrar=default&FlagClasePersona=S&fEsEmpleado=S&fEsOtros=S&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" style=" <?=$display_modificar?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
		</tr>
		<tr>
			<td class="tagForm">* 1er. Apellido:</td>
			<td>
				<input type="text" name="Apellido1" id="Apellido1" value="<?=htmlentities($field['Apellido1'])?>" style="width:175px;" <?=$disabled_ver?> />
			</td>
			<td class="tagForm">2do. Apellido:</td>
			<td>
				<input type="text" name="Apellido2" id="Apellido2" value="<?=htmlentities($field['Apellido2'])?>" style="width:175px;" <?=$disabled_ver?> />
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Nombres:</td>
			<td>
				<input type="text" name="Nombres" id="Nombres" value="<?=htmlentities($field['Nombres'])?>" style="width:175px;" <?=$disabled_ver?> />
			</td> 
			<td class="tagForm">Persona:</td>
			<td>
	        	<input type="text" name="CodPersona" id="CodPersona" value="<?=$field['CodPersona']?>" style="width:75px;" readonly />
			</td>
		</tr>
		<tr>
			<td class="tagForm">* F. Nacimiento:</td>
			<td>
				<input type="text" name="Fnacimiento" id="Fnacimiento" value="<?=formatFechaDMA($field['Fnacimiento'])?>" style="width:75px;" class="datepicker" <?=$disabled_ver?> />
			</td> 
			<td class="tagForm">* Sexo:</td>
			<td>
	            <select name="Sexo" id="Sexo" style="width:175px;" <?=$disabled_ver?>>
	            	<option value="">&nbsp;</option>
	                <?=loadSelectGeneral("SEXO", $field['Sexo'], 0)?>
	            </select>
			</td>
		</tr>
		<tr>
			<td class="tagForm">Estado Civil:</td>
			<td>
				<select name="EstadoCivil" id="EstadoCivil" style="width:175px;" <?=$disabled_ver?>>
					<option value="">&nbsp;</option>
					<?=getMiscelaneos($field['EstadoCivil'],"EDOCIVIL")?>
				</select>
			</td>
			<td class="tagForm">Tipo Licencia:</td>
			<td>
				<select name="TipoLicencia" id="TipoLicencia" style="width:175px;" <?=$disabled_ver?>>
					<option value="">&nbsp;</option>
					<?=getMiscelaneos($field['TipoLicencia'],"TIPOLIC")?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="tagForm">Teléfono:</td>
			<td>
				<input type="text" name="Telefono1" id="Telefono1" value="<?=$field['Telefono1']?>" maxlength="15" style="width:175px;" <?=$disabled_ver?> />
			</td>
			<td class="tagForm">Nro. Licencia:</td>
			<td>
				<input type="text" name="Nlicencia" id="Nlicencia" value="<?=$field['Nlicencia']?>" maxlength="15" style="width:175px;" <?=$disabled_ver?> />
			</td>
		</tr>
		<tr>
			<td class="tagForm">Celular:</td>
			<td>
				<input type="text" name="Telefono2" id="Telefono2" value="<?=$field['Telefono2']?>" maxlength="15" style="width:175px;" <?=$disabled_ver?> />
			</td>
			<td class="tagForm">Fecha Expiración:</td>
			<td>
				<input type="text" name="ExpiraLicencia" id="ExpiraLicencia" value="<?=formatFechaDMA($field['ExpiraLicencia'])?>" maxlength="10" style="width:75px;" class="datepicker" onkeyup="setFechaDMA(this);" <?=$disabled_ver?> />
			</td>
		</tr>
		<tr>
			<td class="tagForm">Dirección:</td>
			<td colspan="3">
				<textarea name="Direccion" id="Direccion" style="width:100%;" <?=$disabled_ver?>><?=htmlentities($field['Direccion'])?></textarea>
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Pais:</td>
			<td>
	            <select name="CodPais" id="CodPais" style="width:175px;" onchange="getOptionsSelect(this.value, 'estado', 'CodEstado', true, 'CodMunicipio', 'CodCiudad');" <?=$disabled_ver?>>
	            	<option value="">&nbsp;</option>
	                <?=loadSelect("mastpaises", "CodPais", "Pais", $field['CodPais'], 0);?>
	            </select>
			</td>
			<td class="tagForm">* Estado:</td>
			<td>
	            <select name="CodEstado" id="CodEstado" style="width:175px;" onchange="getOptionsSelect(this.value, 'municipio', 'CodMunicipio', true, 'CodCiudad');" <?=$disabled_ver?>>
	                <?=loadSelectDependienteEstado($field['CodEstado'], $field['CodPais'], 0);?>
	            </select>
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Municipio:</td>
			<td>
	            <select name="CodMunicipio" id="CodMunicipio" style="width:175px;" onchange="getOptionsSelect(this.value, 'ciudad', 'CodCiudad', true);" <?=$disabled_ver?>>
	                <?=loadSelectDependiente("mastmunicipios", "CodMunicipio", "Municipio", "CodEstado", $field['CodMunicipio'], $field['CodEstado'], 0);?>
	            </select>
			</td>
			<td class="tagForm">* Ciudad:</td>
			<td>
	            <select name="CodCiudad" id="CodCiudad" style="width:175px;" <?=$disabled_ver?>>
	                <?=loadSelectDependiente("mastciudades", "CodCiudad", "Ciudad", "CodMunicipio", $field['CodCiudad'], $field['CodMunicipio'], 0);?>
	            </select>
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
			url: "lg_choferes_ajax.php",
			data: 'modulo=ajax&accion=get_persona&Ndocumento='+$('#Ndocumento').val(),
			async: true,
			success: function(resp) {
				var data = resp.split("|");

				$('#CodEstado').empty().append(data[17]);
				$('#CodMunicipio').empty().append(data[18]);
				$('#CodCiudad').empty().append(data[19]);

				if (inputs) {
					for(var i=0; i<inputs.length; i++) {
						if ($("#"+inputs[i]).length > 0) $("#"+inputs[i]).val(data[i]);
					}
				}
			}
		});
	}
</script>