<?php
if ($opcion == "nuevo") {
	$field['Estado'] = 'A';
	$field['CodPais'] = $_PARAMETRO['PAISDEFAULT'];
	$field['CodEstado'] = $_PARAMETRO['ESTADODEFAULT'];
	$field['CodMunicipio'] = $_PARAMETRO['MUNICIPIODEFAULT'];
	##
	$_titulo = "Rutas de Despacho / Nuevo Registro";
	$accion = "nuevo";
	$disabled_ver = "";
	$display_ver = "";
	$display_submit = "";
	$label_submit = "Guardar";
	$focus = "Descripcion";
}
elseif ($opcion == "modificar" || $opcion == "ver") {
	##	consulto datos generales
	$sql = "SELECT
				rd.*,
				pr.CodParroquia,
				m.CodMunicipio,
				e.CodEstado,
				p.CodPais
			FROM co_rutadespacho rd
			INNER JOIN mastparroquias pr ON pr.CodParroquia = rd.CodParroquia
			INNER JOIN mastmunicipios m ON m.CodMunicipio = pr.CodMunicipio
			INNER JOIN mastestados e ON e.CodEstado = m.CodEstado
			INNER JOIN mastpaises p On p.CodPais = e.CodPais
			WHERE rd.CodRutaDespacho = '$sel_registros'";
	$field = getRecord($sql);
	##
	if ($opcion == "modificar") {
		$_titulo = "Rutas de Despacho / Modificar Registro";
		$accion = "modificar";
		$disabled_ver = "";
		$display_ver = "";
		$display_submit = "";
		$label_submit = "Modificar";
		$focus = "Descripcion";
	}
	##
	elseif ($opcion == "ver") {
		$_titulo = "Rutas de Despacho / Ver Registro";
		$accion = "";
		$disabled_ver = "disabled";
		$display_ver = "display:none;";
		$display_submit = "display:none;";
		$label_submit = "";
		$focus = "btCancelar";
	}
}
$clkCancelar = "document.getElementById('frmentrada').submit();";
//	------------------------------------
$_width = 800;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=co_rutadespacho_lista" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('co_rutadespacho_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
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
			<td colspan="3">
	        	<input type="text" name="CodRutaDespacho" id="CodRutaDespacho" value="<?=$field['CodRutaDespacho']?>" style="width:125px; font-weight:bold;" readonly="readonly" />
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Descripci&oacute;n:</td>
			<td colspan="3">
	        	<input type="text" name="Descripcion" id="Descripcion" value="<?=$field['Descripcion']?>" style="width:95%;" maxlength="255" <?=$disabled_ver?> />
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Pais:</td>
			<td>
				<select name="CodPais" id="CodPais" style="width:200px;" <?=$disabled_ver?> onChange="loadSelect($('#CodEstado'), 'tabla=mastestados&CodPais='+$(this).val(), 1, ['CodMunicipio','CodParroquia']);">
					<option value="">&nbsp;</option>
					<?=loadSelect2('mastpaises','CodPais','Pais',$field['CodPais'])?>
				</select>
			</td>
			<td class="tagForm">* Estado:</td>
			<td>
				<select name="CodEstado" id="CodEstado" style="width:200px;" <?=$disabled_ver?> onChange="loadSelect($('#CodMunicipio'), 'tabla=mastmunicipios&CodEstado='+$(this).val(), 1, ['CodParroquia']);">
					<option value="">&nbsp;</option>
					<?=loadSelect2('mastestados','CodEstado','Estado',$field['CodEstado'],0,['CodPais'],[$field['CodPais']])?>
				</select>
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Municipio:</td>
			<td>
				<select name="CodMunicipio" id="CodMunicipio" style="width:200px;" <?=$disabled_ver?> onChange="loadSelect($('#CodParroquia'), 'tabla=mastparroquias&CodMunicipio='+$(this).val(), 1);">
					<option value="">&nbsp;</option>
					<?=loadSelect2('mastmunicipios','CodMunicipio','Municipio',$field['CodMunicipio'],0,['CodEstado'],[$field['CodEstado']])?>
				</select>
			</td>
			<td class="tagForm">* Parroquia:</td>
			<td>
				<select name="CodParroquia" id="CodParroquia" style="width:200px;" <?=$disabled_ver?>>
					<option value="">&nbsp;</option>
					<?=loadSelect2('mastparroquias','CodParroquia','Descripcion',$field['CodParroquia'],0,['CodMunicipio'],[$field['CodMunicipio']])?>
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
				<input type="text" value="<?=$field['UltimoUsuario']?>" style="width:125px;" disabled="disabled" />
				<input type="text" value="<?=$field['UltimaFecha']?>" style="width:125px" disabled="disabled" />
			</td>
		</tr>
	</table>

	<center>
		<input type="submit" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" id="btSubmit" />
		<input type="button" value="Cancelar" style="width:75px;" id="btCancelar" onclick="<?=$clkCancelar?>" />
	</center>

	<input type="hidden" id="sel_detalle" />
	<table style="width:100%; max-width:<?=$_width?>px;" class="tblBotones">
		<thead>
			<tr>
				<th class="divFormCaption" colspan="2">DIRECCIONES</th>
			</tr>
		</thead>
		<tbody>
			<tr>
	            <td class="gallery clearfix">
	                <a id="a_detalle" href="gehen.php?iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" style="display:none;"></a>
	                <input type="button" style="width:85px;" value="Sel. Cliente" onclick="abrir_selector('detalle', ['CodPersona','NomPersona'], '../lib/listas/gehen.php?anz=lista_personas&ventana=&filtrar=default&FlagClasePersona=S&fEsCliente=S&iframe=true&width=100%&height=100%', 'detalle');" <?=$disabled_ver?> />
	            </td>
				<td align="right">
					<input type="button" class="btLista" value="Insertar" onclick="insertar2(this, 'detalle', 'modulo=ajax&accion=detalle_insertar', 'co_rutadespacho_ajax.php');" <?=$disabled_ver?> />
					<input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'detalle');" <?=$disabled_ver?> />
				</td>
			</tr>
		</tbody>
	</table>
	<div style="overflow:scroll; height:230px; width:100%; max-width:<?=$_width?>px; margin:auto;">
		<table class="tblLista" style="width:100%; min-width:<?=$_width-50?>px;">
			<thead>
				<tr>
					<th width="20">#</th>
					<th align="left" width="250">Cliente</th>
					<th align="left">Direcci&oacute;n</th>
					<th width="60">Estado</th>
				</tr>
			</thead>
			
			<tbody id="lista_detalle">
				<?php
				$nro_detalle = 0;
				$sql = "SELECT
							rdd.*,
							p.NomCompleto AS NomPersona
						FROM co_rutadespachodet rdd
						INNER JOIN mastpersonas p ON p.CodPersona = rdd.CodPersona
						WHERE rdd.CodRutaDespacho = '$field[CodRutaDespacho]'";
				$field_detalle = getRecords($sql);
				foreach ($field_detalle as $f)
				{
					$id = ++$nro_detalle;
					?>
					<tr class="trListaBody" onclick="clk($(this), 'detalle', 'detalle_<?=$id?>');" id="detalle_<?=$id?>">
						<th>
							<input type="hidden" name="detalle_Secuencia[]" id="detalle_Secuencia<?=$id?>" value="<?=$f['Secuencia']?>">
							<?=$id?>
						</th>
						<td>
							<input type="hidden" name="detalle_CodPersona[]" id="detalle_CodPersona<?=$id?>" value="<?=$f['CodPersona']?>">
							<input type="text" name="detalle_NomPersona[]" id="detalle_NomPersona<?=$id?>" value="<?=$f['NomPersona']?>" class="cell2" readonly="readonly">
						</td>
						<td>
							<input type="text" name="detalle_Direccion[]" value="<?=$f['Direccion']?>" class="cell">
						</td>
			            <td>
			                <select name="detalle_Estado[]" class="cell">
				                <?=loadSelectGeneral("ESTADO", $f['Estado'])?>
				            </select>
			            </td>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
	</div>
	<input type="hidden" id="nro_detalle" value="<?=$nro_detalle?>" />
	<input type="hidden" id="can_detalle" value="<?=$nro_detalle?>" />
</form>
<div style="width:100%; max-width:<?=$_width?>px;" class="divMsj">Campos Obligatorios *</div>