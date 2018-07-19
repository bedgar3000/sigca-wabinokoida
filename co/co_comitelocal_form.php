<?php
if ($opcion == "nuevo") {
	$field['Estado'] = 'A';
	$field['CodPais'] = $_PARAMETRO['PAISDEFAULT'];
	$field['CodEstado'] = $_PARAMETRO['ESTADODEFAULT'];
	$field['CodMunicipio'] = $_PARAMETRO['MUNICIPIODEFAULT'];
	##
	$_titulo = "Cómite Local de Abastecimiento / Nuevo Registro";
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
				cl.*,
				pr.CodParroquia,
				m.CodMunicipio,
				e.CodEstado,
				pa.CodPais,
				p.NomCompleto,
				p.DocFiscal
			FROM co_comitelocal cl
			INNER JOIN mastparroquias pr ON pr.CodParroquia = cl.CodParroquia
			INNER JOIN mastmunicipios m ON m.CodMunicipio = pr.CodMunicipio
			INNER JOIN mastestados e ON e.CodEstado = m.CodEstado
			INNER JOIN mastpaises pa On pa.CodPais = e.CodPais
			LEFT JOIN mastpersonas p ON p.CodPersona = cl.CodPersona
			WHERE cl.CodComite = '$sel_registros'";
	$field = getRecord($sql);
	##
	if ($opcion == "modificar") {
		$_titulo = "Cómite Local de Abastecimiento / Modificar Registro";
		$accion = "modificar";
		$disabled_ver = "";
		$display_ver = "";
		$display_submit = "";
		$label_submit = "Modificar";
		$focus = "Descripcion";
	}
	##
	elseif ($opcion == "ver") {
		$_titulo = "Cómite Local de Abastecimiento / Ver Registro";
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

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=co_comitelocal_lista" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('co_comitelocal_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
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
			<td class="tagForm" width>Código:</td>
			<td colspan="3">
	        	<input type="text" name="CodComite" id="CodComite" value="<?=$field['CodComite']?>" style="width:75px; font-weight:bold;" readonly="readonly" />
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Nombre:</td>
			<td colspan="3">
	        	<input type="text" name="Descripcion" id="Descripcion" value="<?=$field['Descripcion']?>" style="width:98%;" maxlength="255" <?=$disabled_ver?> />
			</td>
		</tr>
	    <tr>
			<td class="tagForm">Persona:</td>
			<td class="gallery clearfix">
	        	<input type="text" name="DocFiscal" id="DocFiscal" value="<?=$field['DocFiscal']?>" style="width:75px;" maxlength="20" disabled />
	        	<input type="text" name="NomCompleto" id="NomCompleto" value="<?=$field['NomCompleto']?>" style="width:225px;" maxlength="100" disabled />
	        	<a href="../lib/listas/gehen.php?anz=lista_personas&filtrar=default&campo1=CodPersona&campo2=NomCompleto&campo3=DocFiscal&ventana=DocFiscal&_APLICACION=<?=$_APLICACION?>&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" style=" <?=$display_modificar?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
			<td class="tagForm">Código:</td>
			<td>
	        	<input type="text" name="CodPersona" id="CodPersona" value="<?=$field['CodPersona']?>" style="width:60px;" readonly />
			</td>
		</tr>
	    <tr>
			<td class="tagForm">Dirección:</td>
			<td colspan="3">
	        	<textarea name="Direccion" id="Direccion" style="width:98%;" <?=$disabled_ver?>><?=htmlentities($field['Direccion'])?></textarea>
			</td>
		</tr>
	    <tr>
			<td class="tagForm" width="125">* Pais:</td>
			<td>
				<select name="CodPais" id="CodPais" style="width:200px;" <?=$disabled_ver?> onChange="loadSelect($('#CodEstado'), 'tabla=mastestados&CodPais='+$(this).val(), 1, ['CodMunicipio','CodParroquia']);">
					<option value="">&nbsp;</option>
					<?=loadSelect2('mastpaises','CodPais','Pais',$field['CodPais'])?>
				</select>
			</td>
			<td class="tagForm" width="125">* Estado:</td>
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
			<td class="tagForm">* Nro. Circulo Lucha:</td>
			<td>
	        	<input type="text" name="NroCirculo" id="NroCirculo" value="<?=$field['NroCirculo']?>" style="width:75px;" maxlength="255" <?=$disabled_ver?> />
			</td>
			<td class="tagForm">* Nro. Familias:</td>
			<td>
	        	<input type="text" name="NroFamilias" id="NroFamilias" value="<?=$field['NroFamilias']?>" style="width:75px;" maxlength="255" <?=$disabled_ver?> />
			</td>
		</tr>
	    <tr>
			<td class="tagForm">Última Modif.:</td>
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
	                <a id="a_detalle" href="gehen.php?iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe2]" style="display:none;"></a>
	                <input type="button" style="width:85px;" value="Sel. Persona" onclick="abrir_selector('detalle', ['CodPersona','NomPersona','DocPersona','DirPersona','TelPersona','CelPersona'], '../lib/listas/gehen.php?anz=lista_personas&ventana=co_comitelocal&filtrar=default&FlagClasePersona=S&fEsOtros=S&_APLICACION=<?=$_APLICACION?>&iframe=true&width=100%&height=100%', 'detalle');" <?=$disabled_ver?> />
	            </td>
				<td align="right">
					<input type="button" class="btLista" value="Insertar" onclick="insertar2(this, 'detalle', 'modulo=ajax&accion=detalle_insertar', 'co_comitelocal_ajax.php');" <?=$disabled_ver?> />
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
					<th width="60">Cédula</th>
					<th align="left">Nombre Completo</th>
					<th align="left">Dirección</th>
					<th width="100">Teléfono</th>
					<th width="100">Celular</th>
				</tr>
			</thead>
			
			<tbody id="lista_detalle">
				<?php
				$nro_detalle = 0;
				$sql = "SELECT
							cld.*,
							p.NomCompleto AS NomPersona,
							p.Ndocumento AS DocPersona,
							p.Direccion AS DirPersona,
							p.Telefono1 AS TelPersona,
							p.Telefono2 AS CelPersona
						FROM co_comitelocaldet cld
						INNER JOIN mastpersonas p ON p.CodPersona = cld.CodPersona
						WHERE cld.CodComite = '$field[CodComite]'";
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
							<input type="text" name="detalle_DocPersona[]" id="detalle_DocPersona<?=$id?>" value="<?=$f['DocPersona']?>" class="cell2" disabled>
						</td>
						<td>
							<input type="hidden" name="detalle_CodPersona[]" id="detalle_CodPersona<?=$id?>" value="<?=$f['CodPersona']?>">
							<input type="text" name="detalle_NomPersona[]" id="detalle_NomPersona<?=$id?>" value="<?=$f['NomPersona']?>" class="cell2" disabled>
						</td>
						<td>
							<input type="text" name="detalle_DirPersona[]" id="detalle_DirPersona<?=$id?>" value="<?=$f['DirPersona']?>" class="cell2" disabled>
						</td>
						<td>
							<input type="text" name="detalle_TelPersona[]" id="detalle_TelPersona<?=$id?>" value="<?=$f['TelPersona']?>" class="cell2" disabled>
						</td>
						<td>
							<input type="text" name="detalle_CelPersona[]" id="detalle_CelPersona<?=$id?>" value="<?=$f['CelPersona']?>" class="cell2" disabled>
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