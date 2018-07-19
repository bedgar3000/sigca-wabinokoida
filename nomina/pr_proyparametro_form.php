<?php
if ($opcion == "nuevo") {
	$field['Estado'] = 'A';
	$FlagEjecucion = 'N';
	##
	$_titulo = "Definici&oacute;n de Par&aacute;metros / Nuevo Registro";
	$accion = "nuevo";
	$disabled_modificar = "";
	$disabled_ver = "";
	$readonly_modificar = "";
	$display_modificar = "";
	$display_submit = "";
	$label_submit = "Guardar";
	$focus = "Ejercicio";
}
elseif ($opcion == "modificar" || $opcion == "ver" || $opcion == "aprobar" || $opcion == "generar-reformulacion") {
	##	consulto datos generales
	$sql = "SELECT
				ppm.CodParametro,
				ppm.CodRecurso,
				ppm.CodTipoProceso,
				ppm.Estado,
				ppr.Ejercicio,
				ppr.Numero,
				ppr.CodTipoNom
			FROM
				pr_proyparametro ppm
				INNER JOIN pr_proyrecursos ppr ON (ppr.CodRecurso = ppm.CodRecurso)
			WHERE ppm.CodParametro = '$sel_registros'";
	$field = getRecord($sql);
	##	consulto si tiene calculos
	$sql = "SELECT *
			FROM pr_proyejecucion
			WHERE CodParametro = '$sel_registros'";
	$field_ejecucion = getRecords($sql);
	if (count($field_ejecucion)) $FlagEjecucion = 'S'; else $FlagEjecucion = 'N';
	##
	if ($opcion == "modificar") {
		$_titulo = "Definici&oacute;n de Par&aacute;metros / Modificar";
		$accion = "modificar";
		$disabled_modificar = "disabled";
		$disabled_ver = "";
		$readonly_modificar = "readonly";
		$display_modificar = "display:none;";
		$display_submit = "";
		$label_submit = "Modificar";
		$focus = "Descripcion";
	}
	##
	elseif ($opcion == "ver") {
		$_titulo = "Definici&oacute;n de Par&aacute;metros / Ver";
		$accion = "";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$readonly_modificar = "readonly";
		$display_modificar = "display:none;";
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

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=pr_proyparametro_lista" method="POST" enctype="multipart/form-data" onsubmit="return formSubmitLocal('pr_proyparametro_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
	<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
	<input type="hidden" name="fCodTipoProceso" id="fCodTipoProceso" value="<?=$fCodTipoProceso?>" />
	<input type="hidden" name="fEjercicio" id="fEjercicio" value="<?=$fEjercicio?>" />
	<input type="hidden" name="CodParametro" id="CodParametro" value="<?=$field['CodParametro']?>" />
	
	<table width="<?=$_width?>" class="tblForm">
		<tr>
	    	<td colspan="2" class="divFormCaption">Datos Generales</td>
	    </tr>
	    <tr>
			<td class="tagForm" width="125">* Recurso:</td>
			<td class="gallery clearfix">
				<input type="hidden" name="CodRecurso" id="CodRecurso" value="<?=$field['CodRecurso']?>" />
				<input type="text" name="Ejercicio" id="Ejercicio" value="<?=$field['Ejercicio']?>" style="width:37px;" readonly>
				<input type="text" name="Numero" id="Numero" value="<?=$field['Numero']?>" style="width:19px;" readonly>
				<a href="../lib/listas/gehen.php?anz=lista_pr_proyrecursos&filtrar=default&ventana=&campo1=CodRecurso&campo2=Ejercicio&campo3=Numero&campo4=CodTipoNom&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" id="aCodRecurso" style=" <?=$display_modificar?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* N&oacute;mina:</td>
			<td>
				<select name="CodTipoNom" id="CodTipoNom" style="width:265px;" disabled>
					<option value="">&nbsp;</option>
					<?=loadSelect2('tiponomina','CodTipoNom','Nomina',$field['CodTipoNom'],0)?>
				</select>
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Proceso:</td>
			<td>
				<select name="CodTipoProceso" id="CodTipoProceso" style="width:265px;" <?=$disabled_modificar?>>
					<option value="">&nbsp;</option>
					<?=loadSelect2('pr_tipoproceso','CodTipoProceso','Descripcion',$field['CodTipoProceso'],0)?>
				</select>
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

	<center>
		<input type="submit" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" id="btSubmit" />
		<input type="button" value="Cancelar" style="width:75px;" id="btCancelar" onclick="<?=$clkCancelar?>" />
	</center>

	<input type="hidden" id="sel_conceptos" />
	<table width="<?=$_width?>;" class="tblBotones">
		<thead>
			<tr>
				<th class="divFormCaption">CONCEPTOS DE N&Oacute;MINA</th>
			</tr>
		</thead>
        <tbody>
            <tr>
                <td align="right" class="gallery clearfix">
                    <a id="a_conceptos" href="../lib/listas/gehen.php?anz=lista_conceptos&Ejercicio=<?=$field['Ejercicio']?>&filtrar=default&fTipo=I&ventana=pr_proyparametro&detalle=conceptos&modulo=ajax&accion=conceptos_insertar&url=../../nomina/pr_proyparametro_ajax.php&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe2]" style="display:none;"></a>
                    <input type="button" style="width:60px;" value="Insertar" onclick="$('#a_conceptos').click();" <?=$disabled_ver?> />
                    <input type="button" style="width:60px;" value="Borrar" onclick="quitar(this, 'conceptos');" <?=$disabled_ver?> />
                </td>
            </tr>
        </tbody>
	</table>
	<div style="overflow:scroll; height:400px; width:<?=$_width?>px; margin:auto;">
		<table class="tblLista" style="width:100%; min-width:100%;">
			<tbody id="lista_conceptos">
				<?php
				$nro_conceptos = 0;
				$sql = "SELECT
							ppmd.*,
							c.Descripcion As Concepto
						FROM
							pr_proyparametrodet ppmd
							INNER JOIN pr_concepto c ON (c.CodConcepto = ppmd.CodConcepto)
						WHERE ppmd.CodParametro = '$field[CodParametro]'
						ORDER BY CodConcepto";
				$field_conceptos = getRecords($sql);
				foreach ($field_conceptos as $f) {
					$id = $f['CodConcepto'];
					?>
					<tr class="trListaBody" onclick="clk($(this), 'conceptos', 'conceptos_<?=$id?>');" id="conceptos_<?=$id?>">
						<th width="25" align="center">
							<input type="hidden" name="conceptos_CodConcepto[]" value="<?=$f['CodConcepto']?>" />
							<?=++$nro_conceptos?>
						</th>
						<td>
							<table style="width:100%;">
								<tr>
									<th align="left">
										<div style="float:right;">
											<input type="checkbox" name="conceptos_FlagParametrizable<?=$f['CodConcepto']?>" value="S" <?=chkOpt($f['FlagParametrizable'],'S')?> <?=$disabled_ver?>>
											Parametrizable
										</div>
										<?=$f['CodConcepto']?> <?=htmlentities($f['Concepto'])?>
									</th>
								</tr>
								<tr>
									<td>
										<textarea name="conceptos_Formula[]" id="conceptos_Formula[]" class="cell" style="height:50px;" <?=$disabled_ver?>><?=htmlentities($f['Formula'])?></textarea>
									</td>
								</tr>
							</table>
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
<div style="width:<?=$_width?>px" class="divMsj">Campos Obligatorios *</div>

<script type="text/javascript" language="javascript">
	function setSueldoTotal(idx) {
		var SueldoTotal = 0;
		var SueldoActual = setNumero($('input[name="empleado_SueldoActual[]"]:eq('+idx+')').val());
		var Monto = setNumero($('input[name="empleado_Monto[]"]:eq('+idx+')').val());
		var Porcentaje = setNumero($('input[name="empleado_Porcentaje[]"]:eq('+idx+')').val());
		if (Monto > 0) SueldoTotal = SueldoActual + Monto;
		else SueldoTotal = SueldoActual + (SueldoActual * Porcentaje / 100);
		$('input[name="empleado_SueldoTotal[]"]:eq('+idx+')').val(SueldoTotal).formatCurrency();
	}
	function getEmpleados() {
		$('#lista_empleado').html('Cargando...');
		//	
		$.ajax({
			type: "POST",
			url: "pr_proyparametro_ajax.php",
			data: "modulo=ajax&accion=getEmpleados&"+$('form').serialize(),
			async: false,
			success: function(data) {
				$('#lista_empleado').html(data);
				inicializar();

				var nro_empleado = $('input[name="empleado_CodCargo[]"]').length;
				$('#nro_empleado').val(nro_empleado);
				$('#can_empleado').val(nro_empleado);
			}
		});
	}
	function formSubmitLocal(url, data) {
		bloqueo(1);
		var form = document.getElementById($('form').attr('id'));
		var idform = form.id;
		//	-
		if ('<?=$FlagEjecucion?>' == 'S') {
			$("#cajaModal").dialog({
				buttons: {
					"Si": function() {
						$(this).dialog("close");
						//	ajax
						$.ajax({
							type: "POST",
							url: url + ".php",
							data: data+"&"+$('#'+idform).serialize(),
							async: false,
							success: function(resp) {
								if (resp.trim() != '') cajaModal(resp.trim(), 'error', 400);
								else form.submit();
							}
						});
					},
					"No": function() {
						$(this).dialog("close");
						bloqueo(0);
					}
				}
			});
			cajaModalConfirm("Esta acci&oacute;n eliminar&aacute; los calculos realizados.<br>¿Est&aacute; seguro?", 400);
		} else {
			//	ajax
			$.ajax({
				type: "POST",
				url: url + ".php",
				data: data+"&"+$('#'+idform).serialize(),
				async: false,
				success: function(resp) {
					if (resp.trim() != '') cajaModal(resp.trim(), 'error', 400);
					else form.submit();
				}
			});
		}
		return false;
	}
</script>