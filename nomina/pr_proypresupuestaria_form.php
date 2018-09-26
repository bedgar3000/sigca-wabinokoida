<?php
if ($opcion == "nuevo") {
	$sql = "SELECT MAX(Ejercicio) FROM pr_proypresupuestaria";
	$Ejercicio = getVar3($sql);
	$Ejercicio = $Ejercicio?$Ejercicio:$AnioActual;

	$field['CodOrganismo'] = $fCodOrganismo;
	$field['Ejercicio'] = $fEjercicio?$fEjercicio:$Ejercicio;
	$field['Estado'] = 'AP';
	$field['CategoriaProg'] = ($fCategoriaProg?$fCategoriaProg:'');
	##
	$_titulo = "Proyección Presupuestaria / Nuevo";
	$accion = "nuevo";
	$disabled_modificar = "";
	$disabled_ver = "";
	$display_modificar = "";
	$display_submit = "";
	$label_submit = "Guardar";
	$focus = "Descripcion";
}
elseif ($opcion == "modificar" || $opcion == "ver" || $opcion == "aprobar") {
	##	consulto datos generales
	$sql = "SELECT *
			FROM pr_proypresupuestaria
			WHERE CodProyPresupuesto = '$sel_registros'";
	$field = getRecord($sql);
	##
	if ($opcion == "modificar") {
		$_titulo = "Proyección Presupuestaria / Modificar";
		$accion = "modificar";
		$disabled_modificar = "disabled";
		$disabled_ver = "";
		$display_modificar = "display:none;";
		$display_submit = "";
		$label_submit = "Modificar";
		$focus = "Descripcion";
	}
	##
	elseif ($opcion == "aprobar") {
		$field['Estado'] = 'GE';
		##	
		$_titulo = "Proyección Presupuestaria / Aprobar";
		$accion = "aprobar";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$display_modificar = "display:none;";
		$display_submit = "";
		$label_submit = "Aprobar";
		$focus = "btSubmit";
	}
	##
	elseif ($opcion == "ver") {
		$_titulo = "Proyección Presupuestaria / Ver";
		$accion = "";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$display_modificar = "display:none;";
		$display_submit = "display:none;";
		$label_submit = "";
		$focus = "btCancelar";
	}
}
$clkCancelar = "document.getElementById('frmentrada').submit();";
//	------------------------------------
$_width = 750;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=<?=$action?>" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('pr_proypresupuestaria_ajax', 'modulo=formulario&accion=<?=$accion?>', this, <?=isset($FlagContinuar)?$FlagContinuar:'false'?>);" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fEjercicio" id="fEjercicio" value="<?=$fEjercicio?>" />
	<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
	<input type="hidden" name="fCategoriaProg" id="fCategoriaProg" value="<?=$fCategoriaProg?>" />
	<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
	<input type="hidden" name="CodProyPresupuesto" id="CodProyPresupuesto" value="<?=$field['CodProyPresupuesto']?>" />

	<table width="<?=$_width?>" class="tblForm">
		<tr>
	    	<td colspan="4" class="divFormCaption">Datos Generales</td>
	    </tr>
	    <tr>
			<td class="tagForm" width="125">* Organismo:</td>
			<td>
				<select name="CodOrganismo" id="CodOrganismo" style="width:268px;"onchange="$('#aCategoriaProg').attr('href','../lib/listas/gehen.php?anz=lista_pv_categoriaprog&filtrar=default&ventana=&campo1=CategoriaProg&FlagOrganismo=S&fCodOrganismo='+$(this).val()+'&iframe=true&width=100%&height=100%'); $('#CategoriaProg').val('');" <?=$disabled_modificar?>>
					<?=getOrganismos($field['CodOrganismo'], 3)?>
				</select>
			</td>
			<td class="tagForm" width="125">Estado:</td>
			<td>
				<input type="hidden" name="Estado" id="Estado" value="<?=$field['Estado']?>" />
				<input type="text" value="<?=mb_strtoupper(printValores('proypresupuestaria-estado',$field['Estado']),'UTF-8')?>" style="width:100px; font-weight:bold;" disabled>
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Cat. Program&aacute;tica:</td>
			<td class="gallery clearfix">
				<input type="text" name="CategoriaProg" id="CategoriaProg" value="<?=$field['CategoriaProg']?>" style="width:100px;" readonly="readonly" />
				<a href="../lib/listas/gehen.php?anz=lista_pv_categoriaprog&filtrar=default&ventana=&campo1=CategoriaProg&FlagOrganismo=S&fCodOrganismo=<?=$field['CodOrganismo']?>&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" id="aCategoriaProg" style=" <?=$display_modificar?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
			<td class="tagForm" width="125">* Ejercicio:</td>
			<td>
				<input type="text" name="Ejercicio" id="Ejercicio" value="<?=$field['Ejercicio']?>" style="width:50px;" <?=$disabled_modificar?>>
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Descripci&oacute;n:</td>
			<td colspan="3">
	        	<textarea name="Descripcion" id="Descripcion" style="width:95%; height:50px;" <?=$disabled_ver?>><?=$field['Descripcion']?></textarea>
			</td>
		</tr>
	    <tr>
			<td class="tagForm">&Uacute;ltima Modif.:</td>
			<td colspan="3">
				<input type="text" value="<?=$field['UltimoUsuario']?>" style="width:150px;" disabled="disabled" />
				<input type="text" value="<?=$field['UltimaFecha']?>" style="width:110px" disabled="disabled" />
			</td>
		</tr>
	</table>

	<center>
		<input type="submit" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" id="btSubmit" />
		<input type="button" value="Cancelar" style="width:75px;" id="btCancelar" onclick="<?=$clkCancelar?>" />
	</center>

	<input type="hidden" id="sel_detalle" />
	<table width="<?=$_width?>;" class="tblBotones">
		<thead>
			<tr>
				<th class="divFormCaption">DISTRIBUCI&Oacute;N PRESUPUESTARIA</th>
			</tr>
		</thead>
	    <tbody>
		    <tr>
		        <td align="right" class="gallery clearfix">
					<a id="a_detalle" href="pagina.php?iframe=true&width=100%&height=430" rel="prettyPhoto[iframe2]" style="display:none;"></a>
		            <input type="button" style="width:85px;" value="Partida" onclick="insertar_partida('detalle');" <?=$disabled_ver?> /> |
					<input type="button" style="width:85px;" value="Borrar" onclick="quitar(this, 'detalle'); setMontos();" <?=$disabled_ver?> />
		        </td>
		    </tr>
	    </tbody>
	</table>
	<div style="overflow:scroll; height:250px; width:<?=$_width?>px; margin:auto;">
		<table class="tblLista" style="width:100%; min-width:700px;">
			<thead>
				<tr>
					<th width="80">Partida</th>
					<th align="left">Denominaci&oacute;n</th>
					<th width="100">Monto</th>
				</tr>
			</thead>
			
			<tbody id="lista_detalle">
				<?php
				$MontoTotal = 0;
				$sql = "SELECT ppd.*
						FROM pr_proypresupuestariadet ppd
						WHERE ppd.CodProyPresupuesto = '$field[CodProyPresupuesto]'
						ORDER BY cod_partida";
				$field_partida = getRecords($sql);
				foreach ($field_partida as $f) {
					$id = $f['cod_partida'];
					?>
					<tr class="trListaBody" id="detalle_<?=$id?>" onclick="clk($(this), 'detalle', 'detalle_<?=$id?>');">
						<td align="center">
							<input type="hidden" name="detalle_cod_partida[]" value="<?=$id?>" />
							<?=$f['cod_partida']?>
						</td>
						<td><input type="text" name="detalle_Descripcion[]" value="<?=htmlentities($f['Descripcion'])?>" class="cell" <?=$readonly?> /></td>
						<td align="right"><input type="text" name="detalle_Monto[]" value="<?=number_format($f['Monto'],2,',','.')?>" class="cell currency" style="text-align:right;" onchange="setMontos();" <?=$disabled_ver?> /></td>
					</tr>
					<?php
					$MontoTotal += $f['Monto'];
				}
				?>
			</tbody>
		</table>
	</div>
	<table width="<?=$_width?>" class="tblBotones">
	    <tbody>
		    <tr>
		        <td align="right" style="padding-right:14px;">
		        	<strong>Total: </strong>
		        	<input type="text" name="MontoTotal" id="MontoTotal" value="<?=number_format($MontoTotal,2,',','.')?>" style="width:115px; font-weight:bold; text-align:right;" readonly>
		        </td>
		    </tr>
	    </tbody>
	</table>
	<input type="hidden" id="nro_detalle" value="<?=$nro_detalle?>" />
	<input type="hidden" id="can_detalle" value="<?=$nro_detalle?>" />
</form>
<div style="width:<?=$_width?>px" class="divMsj">Campos Obligatorios *</div>

<script type="text/javascript" language="javascript">
	function insertar_partida(detalle) {
		if ($('#CategoriaProg').val() == '') {
			cajaModal('Debe seleccionar la Categor&iacute;a Program&aacute;tica');
		} else {
			var href = "../lib/listas/gehen.php?anz=lista_partidas&filtrar=default&ventana=listado_insertar_linea&FlagTipoCuenta=S&fcod_tipocuenta=4&FlagProyeccionNomina=S&detalle="+detalle+"&modulo=ajax&accion=partida_insertar&url=../../nomina/pr_proypresupuestaria_ajax.php&iframe=true&width=100%&height=100%";
			$('#a_'+detalle).attr('href',href);
			$('#a_'+detalle).click();
		}
	}
	function setMontos() {
		var MontoTotal = 0;
		$('input[name="detalle_Monto[]"]').each(function(idx) {
			var Monto = setNumero($('input[name="detalle_Monto[]"]:eq('+idx+')').val());
			MontoTotal += Monto;
		});
		$('#MontoTotal').val(MontoTotal).formatCurrency();
	}
</script>