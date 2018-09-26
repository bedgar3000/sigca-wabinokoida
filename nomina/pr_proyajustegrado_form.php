<?php
if ($opcion == "nuevo") {
	$field['CodOrganismo'] = $fCodOrganismo;

	$sql = "SELECT MAX(Ejercicio) FROM pr_proyajustegrado";
	$Ejercicio = getVar3($sql);
	$field['Ejercicio'] = ($fEjercicio?$fEjercicio:$Ejercicio);
	$field['PeriodoDesde'] = $field['Ejercicio'].'-01';
	$field['PeriodoHasta'] = $field['Ejercicio'].'-12';

	$field['Estado'] = 'A';
	##
	$_titulo = "Ajustes por Grado Salarial / Nuevo Registro";
	$accion = "nuevo";
	$disabled_modificar = "";
	$disabled_ver = "";
	$readonly_modificar = "";
	$display_submit = "";
	$label_submit = "Guardar";
	$focus = "Ejercicio";
}
elseif ($opcion == "modificar" || $opcion == "ver" || $opcion == "aprobar" || $opcion == "generar-reformulacion") {
	##	consulto datos generales
	$sql = "SELECT *
			FROM pr_proyajustegrado
			WHERE CodAjuste = '$sel_registros'";
	$field = getRecord($sql);
	##
	if ($opcion == "modificar") {
		$_titulo = "Ajustes por Grado Salarial / Modificar";
		$accion = "modificar";
		$disabled_modificar = "disabled";
		$disabled_ver = "";
		$readonly_modificar = "readonly";
		$display_submit = "";
		$label_submit = "Modificar";
		$focus = "Descripcion";
	}
	##
	elseif ($opcion == "ver") {
		$_titulo = "Ajustes por Grado Salarial / Ver";
		$accion = "";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$readonly_modificar = "readonly";
		$display_submit = "display:none;";
		$label_submit = "";
		$focus = "btCancelar";
	}
}
$clkCancelar = "document.getElementById('frmentrada').submit();";
//	------------------------------------
$_width = 650;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=pr_proyajustegrado_lista" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('pr_proyajustegrado_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
	<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
	<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
	<input type="hidden" name="fEjercicio" id="fEjercicio" value="<?=$fEjercicio?>" />
	<input type="hidden" name="CodAjuste" id="CodAjuste" value="<?=$field['CodAjuste']?>" />

	<div id="tab1" style="display:block;">
		<table width="<?=$_width?>" class="tblForm">
			<tr>
		    	<td colspan="4" class="divFormCaption">Datos Generales</td>
		    </tr>
		    <tr>
				<td class="tagForm">* Organismo:</td>
				<td>
					<select name="CodOrganismo" id="CodOrganismo" style="width:300px;" <?=$disabled_modificar?>>
						<?=getOrganismos($fCodOrganismo, 3);?>
					</select>
				</td>
			</tr>
		    <tr>
				<td class="tagForm" width="125">* Ejercicio:</td>
				<td>
					<input type="text" name="Ejercicio" id="Ejercicio" value="<?=$field['Ejercicio']?>" style="width:37px;" <?=$readonly_modificar?>>
					<input type="text" name="Numero" id="Numero" value="<?=$field['Numero']?>" style="width:19px;" disabled>
				</td>
			</tr>
		    <tr>
				<td class="tagForm" width="125">* Periodo:</td>
				<td>
					<input type="text" name="PeriodoDesde" id="PeriodoDesde" value="<?=$field['PeriodoDesde']?>" style="width:60px;" <?=$disabled_ver?>> -
					<input type="text" name="PeriodoHasta" id="PeriodoHasta" value="<?=$field['PeriodoHasta']?>" style="width:60px;" <?=$disabled_ver?>>
				</td>
			</tr>
		    <tr>
				<td class="tagForm">* Descripci&oacute;n:</td>
				<td>
		        	<textarea name="Descripcion" id="Descripcion" style="width:300px; height:50px;" <?=$disabled_ver?>><?=$field['Descripcion']?></textarea>
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
					<input type="text" value="<?=$field['UltimaFecha']?>" style="width:110px" disabled="disabled" />
				</td>
			</tr>
		</table>

		<input type="hidden" id="sel_grado" />
		<table width="<?=$_width?>;" class="tblBotones">
			<thead>
				<tr>
					<th class="divFormCaption">GRADOS SALARIALES</th>
				</tr>
			</thead>
		</table>
		<div style="overflow:scroll; height:400px; width:<?=$_width?>px; margin:auto;">
			<table class="tblLista" style="width:100%;">
				<thead>
					<tr>
						<th width="25">#</th>
						<th colspan="2">Grado</th>
						<th width="25">Paso</th>
						<th align="left">Descripci&oacute;n</th>
						<th width="80">Sueldo Actual</th>
						<th width="60">Monto</th>
						<th width="60">Porcentaje</th>
						<th width="80">Sueldo Total</th>
					</tr>
				</thead>
				
				<tbody id="lista_grado">
					<?php
					$nro_grado = 0;
					$i = 0;
					$Grupo = '';
					$sql = "SELECT
								pagd.SueldoActual,
								pagd.Monto,
								pagd.Porcentaje,
								pagd.SueldoTotal,
								ns.CategoriaCargo,
								ns.Grado,
								ns.Paso,
								ns.Descripcion,
								ns.SueldoPromedio AS SueldoGrado,
								md.Descripcion AS NomCategoriaCargo,
								pagd.CategoriaCargo AS Checked
							FROM
								rh_nivelsalarial ns
								LEFT JOIN pr_proyajustegradodet pagd ON (ns.CategoriaCargo = pagd.CategoriaCargo AND
																		 ns.Grado = pagd.Grado AND
																		 ns.Paso = pagd.Paso AND 
																		 pagd.CodAjuste = '$field[CodAjuste]')
								LEFT JOIN mastmiscelaneosdet md ON (md.CodDetalle = ns.CategoriaCargo AND
																	md.CodMaestro = 'CATCARGO')
							ORDER BY CategoriaCargo, Grado, Paso";
					$field_grado = getRecords($sql);
					foreach ($field_grado as $f) {
						$id = ++$nro_grado;
						/*$f['Checked'] = true;	//-
						$f['Monto'] = 100;
						$f['SueldoActual'] = $f['SueldoGrado'];
						$f['SueldoTotal'] = $f['SueldoActual'] + $f['Monto'];	//-*/
						if ($opcion != "ver") {
							$selChk = "$('#chk_tr".$id."').prop('checked', !$('#chk_tr".$id."').prop('checked'));";
							$disInput = "$('.tr".$id."').prop('disabled', !$('#chk_tr".$id."').prop('checked'));";
						}
						if ($opcion != "modificar" || !$f['Checked']) $disabled_detalle = "disabled"; else $disabled_detalle = "";
						if ($f['Checked']) $checked = "checked"; 
						else {
							$checked = "";
							$f['SueldoActual'] = $f['SueldoGrado'];
							$f['SueldoTotal'] = $f['SueldoGrado'];
						}
						if ($Grupo != $f['CategoriaCargo']) {
							$Grupo = $f['CategoriaCargo'];
							?>
							<tr class="trListaBody2">
								<td colspan="8">
									<input type="checkbox" onclick="chkGrados('<?=$f['CategoriaCargo']?>', this.checked);">
									<?=htmlentities($f['NomCategoriaCargo'])?>
								</td>
							</tr>
							<?php
						}
						?>
						<tr class="trListaBody">
							<th align="center" onclick="<?=$selChk?> <?=$disInput?>">
								<input type="hidden" name="grado_CategoriaCargo[]" value="<?=$f['CategoriaCargo']?>" class="tr<?=$id?>" <?=$disabled_detalle?> />
								<input type="hidden" name="grado_Grado[]" value="<?=$f['Grado']?>" class="tr<?=$id?>" <?=$disabled_detalle?> />
								<input type="hidden" name="grado_Paso[]" value="<?=$f['Paso']?>" class="tr<?=$id?>" <?=$disabled_detalle?> />
								<?=$nro_grado?>
							</th>
							<td align="center" width="25">
								<input type="checkbox" id="chk_tr<?=$id?>" <?=$checked?> <?=$disabled_ver?> onclick="<?=$disInput?>" class="chk<?=$f['CategoriaCargo']?>">
							</td>
							<td align="center" width="25"><?=$f['Grado']?></td>
							<td align="center"><?=$f['Paso']?></td>
							<td><?=$f['Descripcion']?></td>
							<td align="right"><input type="text" name="grado_SueldoActual[]" value="<?=number_format($f['SueldoActual'],2,',','.')?>" class="cell2 tr<?=$id?>" style="text-align:right;" readonly <?=$disabled_detalle?> /></td>
							<td align="right"><input type="text" name="grado_Monto[]" value="<?=number_format($f['Monto'],2,',','.')?>" class="cell currency tr<?=$id?> input<?=$f['CategoriaCargo']?>" style="text-align:right;" onchange="setSueldoTotal('<?=$i?>');" <?=$disabled_detalle?> /></td>
							<td align="right"><input type="text" name="grado_Porcentaje[]" value="<?=number_format($f['Porcentaje'],2,',','.')?>" class="cell currency tr<?=$id?> input<?=$f['CategoriaCargo']?>" style="text-align:right;" onchange="setSueldoTotal('<?=$i?>');" <?=$disabled_detalle?> /></td>
							<td align="right"><input type="text" name="grado_SueldoTotal[]" value="<?=number_format($f['SueldoTotal'],2,',','.')?>" class="cell2 tr<?=$id?>" style="text-align:right; font-weight:bold;" readonly <?=$disabled_detalle?> /></td>
						</tr>
						<?php
						++$i;
					}
					?>
				</tbody>
			</table>
		</div>
		<input type="hidden" id="nro_grado" value="<?=$nro_grado?>" />
		<input type="hidden" id="can_grado" value="<?=$nro_grado?>" />
	</div>

	<center>
		<input type="submit" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" id="btSubmit" />
		<input type="button" value="Cancelar" style="width:75px;" id="btCancelar" onclick="<?=$clkCancelar?>" />
	</center>
</form>
<div style="width:<?=$_width?>px" class="divMsj">Campos Obligatorios *</div>

<script type="text/javascript" language="javascript">
	function setSueldoTotal(idx) {
		var SueldoTotal = 0;
		var SueldoActual = setNumero($('input[name="grado_SueldoActual[]"]:eq('+idx+')').val());
		var Monto = setNumero($('input[name="grado_Monto[]"]:eq('+idx+')').val());
		var Porcentaje = setNumero($('input[name="grado_Porcentaje[]"]:eq('+idx+')').val());
		if (Monto > 0) SueldoTotal = SueldoActual + Monto;
		else SueldoTotal = SueldoActual + (SueldoActual * Porcentaje / 100);
		$('input[name="grado_SueldoTotal[]"]:eq('+idx+')').val(SueldoTotal).formatCurrency();
	}
	function chkGrados(categoria, boo) {
		$('.chk'+categoria).prop('checked', boo);
		$('.input'+categoria).prop('disabled', !boo);
	}
</script>