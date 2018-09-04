<?php
if ($opcion == "nuevo") {
	$field['CodOrganismo'] = ($fCodOrganismo?$fCodOrganismo:$_SESSION["FILTRO_ORGANISMO_ACTUAL"]);
	$field['Ejercicio'] = $AnioActual;
	$field['Estado'] = 'A';
	##
	$_titulo = "Financiamientos Aprobados / Nuevo Registro";
	$accion = "nuevo";
	$disabled_ver = "";
	$disabled_modificar = "";
	$display_submit = "";
	$label_submit = "Guardar";
	$focus = "Ejercicio";
}
elseif ($opcion == "modificar" || $opcion == "ver") {
	##	consulto datos generales
	$sql = "SELECT *
			FROM pv_financiamiento
			WHERE CodFinanciamiento = '".$sel_registros."'";
	$field = getRecord($sql);
	##
	if ($opcion == "modificar") {
		$_titulo = "Financiamientos Aprobados / Modificar Registro";
		$accion = "modificar";
		$disabled_ver = "";
		$disabled_modificar = "disabled";
		$display_submit = "";
		$label_submit = "Modificar";
		$focus = "NroGaceta";
	}
	##
	elseif ($opcion == "ver") {
		$_titulo = "Financiamientos Aprobados / Ver Registro";
		$accion = "";
		$disabled_ver = "disabled";
		$disabled_modificar = "disabled";
		$display_submit = "display:none;";
		$label_submit = "";
		$focus = "btCancelar";
	}
}
$clkCancelar = "document.getElementById('frmentrada').submit();";
//	------------------------------------
$_width = 500;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=pv_financiamiento_lista" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('pv_financiamiento_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
<input type="hidden" name="fEjercicio" id="fEjercicio" value="<?=$fEjercicio?>" />
<input type="hidden" name="CodFinanciamiento" id="CodFinanciamiento" value="<?=$field['CodFinanciamiento']?>" />

<table width="<?=$_width?>" class="tblForm">
	<tr>
    	<td colspan="2" class="divFormCaption">Datos Generales</td>
    </tr>
    <tr>
		<td class="tagForm">* Organismo:</td>
		<td>
			<select name="CodOrganismo" id="CodOrganismo" style="width:275px;" class=" <?=$disabled_modificar?>">
				<?=getOrganismos($field['CodOrganismo'], 3)?>
			</select>
		</td>
	</tr>
    <tr>
		<td class="tagForm" width="125">* Ejercicio:</td>
		<td>
        	<input type="text" name="Ejercicio" id="Ejercicio" value="<?=$field['Ejercicio']?>" style="width:65px; font-weight:bold;" maxlength="4" <?=$disabled_modificar?> />
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Monto Aprobado:</td>
		<td>
			<input type="text" name="MontoAprobado" id="MontoAprobado" value="<?=number_format($field['MontoAprobado'],2,',','.')?>" style="width:100px; font-weight:bold; text-align:right;" class="currency" <?=$disabled_ver?> />
		</td>
	</tr>
    <tr>
		<td class="tagForm">Gaceta:</td>
		<td>
			<input type="text" name="NroGaceta" id="NroGaceta" value="<?=$field['NroGaceta']?>" style="width:210px;" maxlength="20" <?=$disabled_ver?> />
			<input type="text" name="FechaGaceta" id="FechaGaceta" value="<?=formatFechaDMA($field['FechaGaceta'])?>" class="datepicker" style="width:60px;" maxlength="10" <?=$disabled_ver?> />
		</td>
	</tr>
	<tr>
		<td class="tagForm">Estado:</td>
		<td>
            <input type="radio" name="Estado" id="Activo" value="A" <?=chkOpt($field['Estado'], "A");?> <?=$disabled_ver?> /> Activo
            &nbsp; &nbsp;
            <input type="radio" name="Estado" id="Inactivo" value="I" <?=chkOpt($field['Estado'], "I");?> <?=$disabled_ver?> /> Inactivo
		</td>
	</tr>
    <tr>
		<td class="tagForm">&Uacute;ltima Modif.:</td>
		<td>
			<input type="text" value="<?=$field['UltimoUsuario']?>" style="width:150px;" disabled="disabled" />
			<input type="text" value="<?=$field['UltimaFecha']?>" style="width:100px" disabled="disabled" />
		</td>
	</tr>
</table>

<center>
<input type="submit" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" id="btSubmit" />
<input type="button" value="Cancelar" style="width:75px;" id="btCancelar" onclick="<?=$clkCancelar?>" />
</center>

<input type="hidden" id="sel_fuente" />
<table width="<?=$_width?>" class="tblBotones">
	<thead>
		<tr>
			<th class="divFormCaption" colspan="2">Fuente de Financiamiento</th>
		</tr>
	</thead>
	<tbody>
		<tr>
            <td class="gallery clearfix">
                <a id="a_fuente" href="gehen.php?iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" style="display:none;"></a>
                <input type="button" style="width:85px;" value="Sel. Partida" onclick="abrir_selector('fuente', ['cod_partida'], '../lib/listas/gehen.php?anz=lista_partidas&ventana=&filtrar=default&FlagTipoCuenta=S&fcod_tipocuenta=3&iframe=true&width=100%&height=100%', 'fuente');" <?=$disabled_ver?> />
            </td>
			<td align="right">
				<input type="button" class="btLista" value="Insertar" onclick="insertar2(this, 'fuente', 'modulo=ajax&accion=fuente_insertar', 'pv_financiamiento_ajax.php');" <?=$disabled_ver?> />
				<input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'fuente'); setMontos();" <?=$disabled_ver?> />
			</td>
		</tr>
	</tbody>
</table>
<div style="overflow:scroll; height:230px; width:<?=$_width?>px; margin:auto;">
	<table class="tblLista" style="width:100%; min-width:<?=$_width + 100?>px;">
		<thead>
			<tr>
				<th width="20">#</th>
				<th align="left">Fuente de Financiamiento</th>
				<th width="100">Monto</th>
				<th width="100">Partida</th>
			</tr>
		</thead>
		
		<tbody id="lista_fuente">
			<?php
			$TotalMontoAprobado = 0;
			$nro_fuente = 0;
			$sql = "SELECT * FROM pv_financiamientodetalle WHERE CodFinanciamiento = '$field[CodFinanciamiento]'";
			$field_fuente = getRecords($sql);
			foreach ($field_fuente as $f) {
				$id = ++$nro_fuente;
				?>
				<tr class="trListaBody" onclick="clk($(this), 'fuente', 'fuente_<?=$id?>');" id="fuente_<?=$id?>">
					<th>
						<?=$nro_fuente?>
					</th>
					<td>
						<select name="fuente_CodFuente[]" class="cell">
							<?=loadSelect2('pv_fuentefinanciamiento','CodFuente','Denominacion',$f['CodFuente'],11)?>
						</select>
					</td>
					<td>
						<input type="text" name="fuente_MontoAprobado[]" value="<?=number_format($f['MontoAprobado'],2,',','.')?>" style="text-align:right;" class="cell currency" onchange="setMontos();">
					</td>
                    <td>
                        <input type="text" name="fuente_cod_partida[]" id="fuente_cod_partida<?=$id?>" value="<?=$f['cod_partida']?>" class="cell2" style="text-align:center;" readonly />
                    </td>
				</tr>
				<?php
				$TotalMontoAprobado += $f['MontoAprobado'];
			}
			?>
		</tbody>
		<tfoot>
			<tr>
				<th colspan="2">TOTAL:</th>
				<th><input type="text" name="TotalMontoAprobado" id="TotalMontoAprobado" value="<?=number_format($TotalMontoAprobado,2,',','.')?>" style="text-align:right; font-weight:bold;" class="cell" readonly></th>
			</tr>
		</tfoot>
	</table>
</div>
<input type="hidden" id="nro_fuente" value="<?=$nro_fuente?>" />
<input type="hidden" id="can_fuente" value="<?=$nro_fuente?>" />
</form>
<div style="width:<?=$_width?>px" class="divMsj">Campos Obligatorios *</div>


<script type="text/javascript" language="javascript">
	function setMontos() {
		var TotalMontoAprobado = 0;
		$('input[name="fuente_MontoAprobado[]"]').each(function(idx) {
			var MontoAprobado = setNumero($(this).val());
			TotalMontoAprobado += MontoAprobado;
		});
		$('#TotalMontoAprobado').val(TotalMontoAprobado).formatCurrency();
	}
</script>