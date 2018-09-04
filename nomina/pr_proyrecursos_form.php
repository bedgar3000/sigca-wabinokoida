<?php
if ($opcion == "nuevo") {
	$field['CodOrganismo'] = ($fCodOrganismo?$fCodOrganismo:$_SESSION["FILTRO_ORGANISMO_ACTUAL"]);

	$sql = "SELECT MAX(Ejercicio) FROM pr_proyrecursos";
	$Ejercicio = getVar3($sql);
	$field['Ejercicio'] = ($fEjercicio?$fEjercicio:$Ejercicio);
	
	$field['Estado'] = 'PR';
	$FlagEjecucion = 'N';
	##
	$_titulo = "Planificaci&oacute;n de Recursos / Nuevo Registro";
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
			FROM pr_proyrecursos
			WHERE CodRecurso = '$sel_registros'";
	$field = getRecord($sql);
	##	consulto si tiene calculos
	$sql = "SELECT *
			FROM pr_proyejecucion
			WHERE CodRecurso = '$sel_registros'";
	$field_ejecucion = getRecords($sql);
	if (count($field_ejecucion)) $FlagEjecucion = 'S'; else $FlagEjecucion = 'N';
	##
	if ($opcion == "modificar") {
		$_titulo = "Planificaci&oacute;n de Recursos / Modificar";
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
		$_titulo = "Planificaci&oacute;n de Recursos / Ver";
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
$_width = 900;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=pr_proyrecursos_lista" method="POST" enctype="multipart/form-data" onsubmit="return formSubmitLocal('pr_proyrecursos_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
	<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
	<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
	<input type="hidden" name="fEjercicio" id="fEjercicio" value="<?=$fEjercicio?>" />
	<input type="hidden" name="CodRecurso" id="CodRecurso" value="<?=$field['CodRecurso']?>" />
	
	<table width="<?=$_width?>" class="tblForm">
		<tr>
	    	<td colspan="4" class="divFormCaption">Datos Generales</td>
	    </tr>
	    <tr>
			<td class="tagForm">* Organismo:</td>
			<td>
				<select name="CodOrganismo" id="CodOrganismo" style="width:300px;" <?=$disabled_modificar?> onchange="getEmpleados(); $('#a_empleado').attr('href', '../lib/listas/gehen.php?anz=lista_empleados&filtrar=default&ventana=pr_proyrecursos&detalle=empleado&modulo=ajax&accion=empleado_insertar&url=../../nomina/pr_proyrecursos_ajax.php&fCodOrganismo='+$('#CodOrganismo').val()+'&fCodTipoNom='+$('#CodTipoNom').val()+'&FlagOrganismo=S&FlagNomina=S&iframe=true&width=100%&height=100%');">
					<?=getOrganismos($fCodOrganismo, 3)?>
				</select>
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* N&oacute;mina:</td>
			<td>
				<select name="CodTipoNom" id="CodTipoNom" style="width:300px;" <?=$disabled_modificar?> onchange="getEmpleados(); $('#a_empleado').attr('href', '../lib/listas/gehen.php?anz=lista_empleados&filtrar=default&ventana=pr_proyrecursos&detalle=empleado&modulo=ajax&accion=empleado_insertar&url=../../nomina/pr_proyrecursos_ajax.php&fCodOrganismo='+$('#CodOrganismo').val()+'&fCodTipoNom='+$('#CodTipoNom').val()+'&FlagOrganismo=S&FlagNomina=S&iframe=true&width=100%&height=100%');">
					<option value="">&nbsp;</option>
					<?=loadSelect2('tiponomina','CodTipoNom','Nomina',$field['CodTipoNom'],0)?>
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
			<td class="tagForm">* Descripci&oacute;n:</td>
			<td>
	        	<textarea name="Descripcion" id="Descripcion" style="width:300px; height:50px;" <?=$disabled_ver?>><?=$field['Descripcion']?></textarea>
			</td>
		</tr>
		<tr>
			<td class="tagForm">Estado:</td>
			<td>
				<input type="hidden" name="Estado" id="Estado" value="<?=$field['Estado']?>" />
				<input type="text" value="<?=mb_strtoupper(printValores('proypresupuestaria-estado',$field['Estado']),'UTF-8')?>" style="width:100px; font-weight:bold;" disabled>
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

	<input type="hidden" id="sel_empleado" />
	<table width="<?=$_width?>;" class="tblBotones">
		<thead>
			<tr>
				<th class="divFormCaption" colspan="2">EMPLEADOS</th>
			</tr>
		</thead>
            <tbody>
	            <tr>
	                <td class="gallery clearfix">
	                    <a id="a_selector" href="gehen.php?iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" style="display:none;"></a>
	                    <input type="button" style="width:85px;" value="Sel. Cargo" onclick="abrir_selector('empleado', ['CodCargo','DescripCargo'], '../lib/listas/gehen.php?anz=lista_cargos&ventana=pr_proyrecursos_sel&filtrar=default&iframe=true&width=100%&height=100%', 'selector');" <?=$disabled_ver?> />
	                    <input type="button" style="width:85px;" value="Sel. Cat. Prog." onclick="selCategoriaProg('empleado', ['CategoriaProg'], '../lib/listas/gehen.php?anz=lista_pv_categoriaprog&ventana=&filtrar=default&FlagOrganismo=S&fCodOrganismo=<?=$field['CodOrganismo']?>&iframe=true&width=100%&height=100%', 'selector');" <?=$disabled_ver?> />
	                </td>
	                <td align="right" class="gallery clearfix">
	                    <a id="a_cargo" href="../lib/listas/gehen.php?anz=lista_cargos&filtrar=default&ventana=pr_proyrecursos&detalle=empleado&modulo=ajax&accion=cargo_insertar&url=../../nomina/pr_proyrecursos_ajax.php&CodOrganismo=<?=$field['CodOrganismo']?>&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe2]" style="display:none;"></a>
	                    <a id="a_empleado" href="../lib/listas/gehen.php?anz=lista_empleados&filtrar=default&ventana=pr_proyrecursos&detalle=empleado&modulo=ajax&accion=empleado_insertar&url=../../nomina/pr_proyrecursos_ajax.php&fCodOrganismo=<?=$field['CodOrganismo']?>&fCodTipoNom=<?=$field['CodTipoNom']?>&FlagOrganismo=S&FlagNomina=S&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe3]" style="display:none;"></a>
	                    <input type="button" style="width:85px;" value="Insertar Cargo" onclick="$('#a_cargo').click();" <?=$disabled_ver?> />
	                    <input type="button" style="width:100px;" value="Insertar Empleado" onclick="$('#a_empleado').click();" <?=$disabled_ver?> /> |
	                    <input type="button" style="width:60px;" value="Borrar" onclick="quitar(this, 'empleado');" <?=$disabled_ver?> />
	                </td>
	            </tr>
            </tbody>
	</table>
	<div style="overflow:scroll; height:400px; width:<?=$_width?>px; margin:auto;">
		<table class="tblLista" style="width:100%; min-width:1600px;">
			<thead>
				<tr>
					<th width="25">#</th>
					<th width="50">Empleado</th>
					<th align="left">Nombre Completo</th>
					<th width="75" align="right">Documento</th>
					<th width="350" align="left">Cargo</th>
					<th width="50">Grado</th>
					<th width="50">Paso</th>
					<th width="350" align="left">Dependencia</th>
					<th width="100">Cat. Prog.</th>
					<th width="100">Tipo</th>
				</tr>
			</thead>
			
			<tbody id="lista_empleado">
				<?php
				$nro_empleado = 0;
				$sql = "SELECT
							pyrd.*,
							p.NomCompleto,
							p.Ndocumento,
							e.CodEmpleado,
							e.SueldoActual,
							d.Dependencia,
							pt.DescripCargo
						FROM
							pr_proyrecursosdet pyrd
							INNER JOIN rh_puestos pt ON (pt.CodCargo = pyrd.CodCargo)
							INNER JOIN mastdependencias d On (d.CodDependencia = pyrd.CodDependencia)
							LEFT JOIN mastempleado e ON (e.CodPersona = pyrd.CodPersona)
							LEFT JOIN mastpersonas p ON (p.CodPersona = e.CodPersona)
						WHERE pyrd.CodRecurso = '$field[CodRecurso]'
						ORDER BY DescripCargo, CodDependencia, CodCargo";
				$field_empleado = getRecords($sql);
				foreach ($field_empleado as $f) {
					$id = ++$nro_empleado;
					?>
					<tr class="trListaBody" onclick="clk($(this), 'empleado', 'empleado_<?=$id?>');" id="empleado_<?=$id?>">
						<th align="center">
							<input type="hidden" name="empleado_CodPersona[]" value="<?=$f['CodPersona']?>" />
							<?=$nro_empleado?>
						</th>
						<td align="center"><?=$f['CodPersona']?></td>
						<td><?=htmlentities($f['NomCompleto'])?></td>
						<td align="right"><?=number_format($f['Ndocumento'],0,'','.')?></td>
                        <td>
                            <input type="hidden" name="empleado_CodCargo[]" id="empleado_CodCargo<?=$id?>" value="<?=$f['CodCargo']?>" />
                            <input type="hidden" name="empleado_CategoriaCargo[]" id="empleado_CategoriaCargo<?=$id?>" value="<?=$f['CategoriaCargo']?>" />
                            <input type="text" name="empleado_DescripCargo[]" id="empleado_DescripCargo<?=$id?>" value="<?=$f['DescripCargo']?>" class="cell2" disabled />
                        </td>
                        <td>
                        	<input type="text" name="empleado_Grado[]" id="empleado_Grado<?=$id?>" value="<?=$f['Grado']?>" class="cell2" style="text-align:center;" readonly />
                        </td>
                        <td>
                        	<select name="empleado_Paso[]" id="empleado_Paso<?=$id?>" class="cell">
                        		<?=loadSelect2('rh_nivelsalarial','Paso','Paso',$f['Paso'],0,['CategoriaCargo','Grado'],[$f['CategoriaCargo'],$f['Grado']])?>
                        	</select>
                        </td>
                        <td>
                        	<select name="empleado_CodDependencia[]" id="empleado_CodDependencia<?=$id?>" class="cell">
                        		<?=loadSelect2('mastdependencias','CodDependencia','Dependencia',$f['CodDependencia'],0,['CodOrganismo'],[$field['CodOrganismo']])?>
                        	</select>
                        </td>
		                <td>
		                	<input type="text" name="empleado_CategoriaProg[]" id="empleado_CategoriaProg<?=$id?>" value="<?=$f['CategoriaProg']?>" class="cell2" style="text-align:center;" readonly />
		                </td>
                        <td>
                        	<select name="empleado_Tipo[]" class="cell">
                        		<?=loadSelectValores('proyeccion-tipo',$field['Tipo'])?>
                        	</select>
                        </td>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
	</div>
	<input type="hidden" id="nro_empleado" value="<?=$nro_empleado?>" />
	<input type="hidden" id="can_empleado" value="<?=$nro_empleado?>" />
</form>
<div style="width:<?=$_width?>px" class="divMsj">Campos Obligatorios *</div>

<script type="text/javascript" language="javascript">
	function setSueldoTotal(idx) {
		var SueldoTotal = 0;
		var SueldoActual = setNumero($('input[name="empleado_SueldoActual[]"]:eq('+idx+')').val());
		var Monto = setNumero($('input[name="empleado_Monto[]"]:eq('+idx+')').val());
		var Porcentaje = setNumero($('input[name="empleado_Porcentaje[]"]:eq('+idx+')').val());
		if (Monto > 0) SueldoTotal = SueldoActual + Monto;
		else {
			SueldoTotal = SueldoActual + (SueldoActual * Porcentaje / 100);
		}
		$('input[name="empleado_SueldoTotal[]"]:eq('+idx+')').val(SueldoTotal).formatCurrency();
	}
	function getEmpleados() {
		$('#lista_empleado').html('Cargando...');
		//	
		$.ajax({
			type: "POST",
			url: "pr_proyrecursos_ajax.php",
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
	function selCategoriaProg(detalle, inputs, href, selector) {
	    var sel_detalle = $('#sel_'+detalle).val();
	    if (!selector) var selector = $('#a_' + detalle); else var selector = $('#a_' + selector);
	    var id = sel_detalle.split('_');
	    var CodDependencia = $('#empleado_CodDependencia'+id[1]).val();
	    var campos = "&";

	    var j = 0;
	    for(var i=0; i<inputs.length; i++) {
	        ++j;
	        campos = campos + "campo" + j + "=" + detalle+"_"+inputs[i]+id[1] + "&";
	    }

	    if (sel_detalle == '') cajaModal('Debe seleccionar un registro', 'error');
	    else {
	        var url = href.split('?');
	        var href = url[0] + '?FlagDependencia=S&fCodDependencia=' + CodDependencia + '&' + campos + url[1];
	        selector.attr('href', href);
	        selector.click();
	    }
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
			cajaModalConfirm("Esta acci&oacute;n eliminar&aacute; los calculos realizados para estos recursos.<br>¿Est&aacute; seguro?", 400);
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