<?php
if ($opcion == "nuevo") {
	$sql = "SELECT MAX(Ejercicio) FROM pv_formulacionmetas";
	$Ejercicio = getVar3($sql);
	$field['Ejercicio'] = ($Ejercicio?$Ejercicio:$AnioActual);
	$field['Estado'] = 'PR';
	$field['CategoriaProg'] = ($fCategoriaProg?$fCategoriaProg:'');
	$sql = "SELECT ue.Denominacion
			FROM
				pv_categoriaprog cp
				INNER JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cp.CodUnidadEjec)
			WHERE cp.CategoriaProg = '$field[CategoriaProg]'";
	$field['UnidadEjecutora'] = getVar3($sql);
	##
	$_titulo = "Formulaci&oacute;n por Metas / Nuevo";
	$accion = "nuevo";
	$disabled_modificar = "";
	$disabled_ver = "";
	$read_modificar = "";
	$opt_modificar = 0;
	$display_modificar = "";
	$display_submit = "";
	$label_submit = "Guardar";
	$focus = "Descripcion";
	$getDistribucion = "getDistribucion();";
}
elseif ($opcion == "modificar" || $opcion == "ver" || $opcion == "aprobar" || $opcion == "generar-reformulacion") {
	list($CodMeta, $Ejercicio) = explode('_', $sel_registros);
	##	consulto datos generales
	$sql = "SELECT
				fm.*,
				op.CodObjetivo,
				cp.CategoriaProg,
				ue.Denominacion AS UnidadEjecutora
			FROM
				pv_formulacionmetas fm
				INNER JOIN pv_metaspoa mp ON (mp.CodMeta = fm.CodMeta)
				INNER JOIN pv_objetivospoa op ON (op.CodObjetivo = mp.CodObjetivo)
				INNER JOIN pv_categoriaprog cp ON (cp.CategoriaProg = op.CategoriaProg)
				INNER JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cp.CodUnidadEjec)
			WHERE
				fm.CodMeta = '$CodMeta' AND
				fm.Ejercicio = '$Ejercicio'";
	$field = getRecord($sql);
	##
	if ($opcion == "modificar") {
		$_titulo = "Formulaci&oacute;n por Metas / Modificar";
		$accion = "modificar";
		$disabled_modificar = "disabled";
		$disabled_ver = "";
		$read_modificar = "readonly";
		$opt_modificar = 1;
		$display_modificar = "display:none;";
		$display_submit = "";
		$label_submit = "Modificar";
		$focus = "Descripcion";
		$getDistribucion = "getDistribucion();";
	}
	##
	elseif ($opcion == "aprobar") {
		$field['Estado'] = 'AP';
		##	
		$_titulo = "Formulaci&oacute;n por Metas / Aprobar";
		$accion = "aprobar";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$read_modificar = "readonly";
		$opt_modificar = 1;
		$display_modificar = "display:none;";
		$display_submit = "";
		$label_submit = "Aprobar";
		$focus = "btSubmit";
		$getDistribucion = "";
	}
	##
	elseif ($opcion == "generar-reformulacion") {
		$field['Estado'] = 'PR';
		##	
		$_titulo = "Reformulaci&oacute;n por Metas / Generar";
		$accion = "generar-reformulacion";
		$disabled_modificar = "disabled";
		$disabled_ver = "";
		$read_modificar = "readonly";
		$opt_modificar = 1;
		$display_modificar = "display:none;";
		$display_submit = "";
		$label_submit = "Generar";
		$focus = "btSubmit";
		$getDistribucion = "getDistribucion();";
	}
	##
	elseif ($opcion == "ver") {
		$_titulo = "Formulaci&oacute;n por Metas / Ver";
		$accion = "";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$read_modificar = "readonly";
		$opt_modificar = 1;
		$display_modificar = "display:none;";
		$display_submit = "display:none;";
		$label_submit = "";
		$focus = "btCancelar";
		$getDistribucion = "";
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

<table align="center" cellpadding="0" cellspacing="0" style="width:<?=$_width?>px;">
    <tr>
        <td>
            <div class="header">
	            <ul id="tab">
		            <li id="li1" onclick="currentTab('tab', this);" class="current"><a href="#" onclick="mostrarTab('tab', 1, 2);">Informaci&oacute;n General</a></li>
		            <li id="li2" onclick="currentTab('tab', this);"><a href="#" onclick="mostrarTab('tab', 2, 2); <?=$getDistribucion?>">Distribuci&oacute;n Presupuesto</a></li>
	            </ul>
            </div>
        </td>
    </tr>
</table>

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=<?=$action?>" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('pv_formulacionmetas_ajax', 'modulo=formulario&accion=<?=$accion?>', this, <?=isset($FlagContinuar)?$FlagContinuar:'false'?>);" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
	<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
	<input type="hidden" name="fCategoriaProg" id="fCategoriaProg" value="<?=$fCategoriaProg?>" />
	<input type="hidden" name="fCodObjetivo" id="fCodObjetivo" value="<?=$fCodObjetivo?>" />

	<div id="tab1" style="display:block;">
		<table width="<?=$_width?>" class="tblForm">
			<tr>
		    	<td colspan="4" class="divFormCaption">Datos Generales</td>
		    </tr>
		    <tr>
				<td class="tagForm" width="125">* Ejercicio:</td>
				<td>
					<input type="text" name="Ejercicio" id="Ejercicio" value="<?=$field['Ejercicio']?>" style="width:50px;" <?=$read_modificar?>>
				</td>
				<td class="tagForm" width="125">Estado:</td>
				<td>
					<input type="hidden" name="Estado" id="Estado" value="<?=$field['Estado']?>" />
					<input type="text" value="<?=strtoupper(printValores('metas-estado',$field['Estado']))?>" style="width:100px; font-weight:bold;" disabled />
				</td>
			</tr>
		    <tr>
				<td class="tagForm">* Cat. Program&aacute;tica:</td>
				<td colspan="3" class="gallery clearfix">
					<input type="text" name="CategoriaProg" id="CategoriaProg" value="<?=$field['CategoriaProg']?>" style="width:100px;" readonly="readonly" />
					<input type="text" name="UnidadEjecutora" id="UnidadEjecutora" value="<?=$field['UnidadEjecutora']?>" style="width:344px;" disabled />
					<a href="../lib/listas/gehen.php?anz=lista_pv_categoriaprog&filtrar=default&ventana=pv_formulacionmetas&campo1=CategoriaProg&campo2=UnidadEjecutora&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" id="aCategoriaProg" style=" <?=$display_modificar?>">
		            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
		            </a>
				</td>
			</tr>
		    <tr>
				<td class="tagForm">* Nro. Objetivo:</td>
				<td colspan="3">
					<select name="CodObjetivo" id="CodObjetivo" style="width:100px;" <?=$disabled_modificar?> onChange="loadSelect($('#CodMeta'), 'tabla=pv_metaspoa&CodObjetivo='+$(this).val(), 1);">
						<option value="">&nbsp;</option>
						<?=loadSelect2('pv_objetivospoa','CodObjetivo','NroObjetivo',$field['CodObjetivo'],0,['CategoriaProg'],[$field['CategoriaProg']])?>
					</select>
				</td>
			</tr>
		    <tr>
				<td class="tagForm">* Nro. Meta:</td>
				<td colspan="3">
					<select name="CodMeta" id="CodMeta" style="width:100px;" class=" <?=$disabled_ver?>" onchange="getDescripcionMeta();">
						<?=loadSelect2('pv_metaspoa','CodMeta','NroMeta',$field['CodMeta'],$opt_modificar,['CodObjetivo'],[$field['CodObjetivo']])?>
					</select>
				</td>
			</tr>
		    <tr>
				<td class="tagForm">* Descripci&oacute;n:</td>
				<td colspan="3">
		        	<textarea name="Descripcion" id="Descripcion" style="width:450px; height:50px;" <?=$disabled_ver?>><?=$field['Descripcion']?></textarea>
				</td>
			</tr>
		    <tr>
				<td class="tagForm">* Cantidad:</td>
				<td>
					<input type="text" name="Cantidad" id="Cantidad" value="<?=$field['Cantidad']?>" style="width:50px;" <?=$disabled_ver?>>
				</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		    <tr>
				<td class="tagForm">&Uacute;ltima Modif.:</td>
				<td colspan="3">
					<input type="text" value="<?=$field['UltimoUsuario']?>" style="width:150px;" disabled="disabled" />
					<input type="text" value="<?=$field['UltimaFecha']?>" style="width:110px" disabled="disabled" />
				</td>
			</tr>
		</table>

		<input type="hidden" id="sel_detalle" />
		<table width="<?=$_width?>;" class="tblBotones">
			<thead>
				<tr>
					<th class="divFormCaption">DISTRIBUCI&Oacute;N DE LAS METAS</th>
				</tr>
			</thead>
		    <tbody>
			    <tr>
			        <td align="right" class="gallery clearfix">
						<a id="a_detalle" href="pagina.php?iframe=true&width=100%&height=430" rel="prettyPhoto[iframe2]" style="display:none;"></a>
			            <input type="button" style="width:85px;" value="Partida" onclick="insertar_partida('detalle');" <?=$disabled_ver?> /> |
			            <input type="button" style="width:85px;" value="Activos" onclick="insertar_commodity('detalle','ACT');" <?=$disabled_ver?> />
			            <input type="button" style="width:85px;" value="Bienes" onclick="insertar_commodity('detalle','BME');" <?=$disabled_ver?> />
			            <input type="button" style="width:85px;" value="Materiales" onclick="insertar_commodity('detalle','CDR');" <?=$disabled_ver?> />
			            <input type="button" style="width:85px;" value="Servicios" onclick="insertar_commodity('detalle','SER');" <?=$disabled_ver?> /> |
			            <input type="button" style="width:85px;" value="Borrar" onclick="quitar(this, 'detalle'); setMontos();" <?=$disabled_ver?> />
			        </td>
			    </tr>
		    </tbody>
		</table>
		<div style="overflow:scroll; height:250px; width:<?=$_width?>px; margin:auto;">
			<table class="tblLista" style="width:100%; min-width:1100px;">
				<thead>
					<tr>
						<th width="80">Partida</th>
						<th align="left">Denominaci&oacute;n</th>
						<th width="80">C&oacute;digo</th>
						<th width="45">Uni.</th>
						<th width="75">Cantidad</th>
						<th width="100">Precio Unitario</th>
						<th width="100">Impuesto</th>
						<th width="100">Monto Total</th>
						<th width="33">F.F</th>
					</tr>
				</thead>
				
				<tbody id="lista_detalle">
					<?php
					$sql = "SELECT
								fmd.*
							FROM
								pv_formulacionmetasdet fmd
							WHERE
								fmd.CodMeta = '$CodMeta' AND
								fmd.Ejercicio = '$Ejercicio'
							ORDER BY cod_partida";
					$field_partida = getRecords($sql);
					foreach ($field_partida as $f) {
						$id = ($f['cod_partida']?$f['cod_partida']:$f['Commodity']);
						$MontoTotal = $f['Cantidad'] * ($f['PrecioUnitario'] + $f['MontoIva']);
						if ($opcion == "modificar" || $opcion == "generar-reformulacion") {
							if ($f['Commodity']) $readonly = ""; else $readonly = "readonly";
						}
						?>
						<tr class="trListaBody" id="detalle_<?=$id?>" onclick="clk($(this), 'detalle', 'detalle_<?=$id?>');">
							<td align="center">
								<input type="hidden" name="detalle_cod_partida[]" value="<?=$id?>" />
								<?=$f['cod_partida']?>
							</td>
							<td><input type="text" name="detalle_Descripcion[]" value="<?=htmlentities($f['Descripcion'])?>" class="cell" <?=$readonly?> /></td>
							<td align="center">
								<input type="hidden" name="detalle_Commodity[]" value="<?=$f['Commodity']?>" />
								<?=$f['Commodity']?>
							</td>
							<td>
								<select name="detalle_CodUnidad[]" class="cell">
									<?php
									if ($f['Commodity']) {
										echo loadSelect2('mastunidades','CodUnidad','CodUnidad',$f['CodUnidad']);
									} else {
										?><option value="">&nbsp;</option><?php
									}
									?>
								</select>
							</td>
							<td align="right"><input type="text" name="detalle_Cantidad[]" value="<?=number_format($f['Cantidad'],2,',','.')?>" class="cell currency" style="text-align:right;" onchange="setMontos();" <?=$disabled_ver?> /></td>
							<td align="right"><input type="text" name="detalle_PrecioUnitario[]" value="<?=number_format($f['PrecioUnitario'],2,',','.')?>" class="cell currency" style="text-align:right;" onchange="setMontos();" <?=$disabled_ver?> /></td>
							<td align="right"><input type="text" name="detalle_MontoIva[]" value="<?=number_format($f['MontoIva'],2,',','.')?>" class="cell currency" style="text-align:right;" onchange="setMontos();" <?=$disabled_ver?> /></td>
							<td align="right"><input type="text" name="detalle_MontoTotal[]" value="<?=number_format($MontoTotal,2,',','.')?>" class="cell currency" style="text-align:right;" readonly /></td>
							<td>
								<select name="detalle_CodFuente[]" class="cell">
									<?=loadSelectFromParametros2('pv_fuentefinanciamiento','CodFuente','Denominacion','FFMETAS',$f['CodFuente'],10)?>
								</select>
							</td>
						</tr>
						<?php
						$SubTotal += ($f['Cantidad'] * $f['PrecioUnitario']);
						$TotalImpuestos += ($f['Cantidad'] * $f['MontoIva']);
					}
					$TotalGeneral = $SubTotal + $TotalImpuestos;
					?>
				</tbody>
			</table>
		</div>
		<table width="<?=$_width?>" class="tblBotones">
		    <tbody>
			    <tr>
			        <td align="right">
			        	<strong>Sub-Total: </strong>
			        	<input type="text" name="SubTotal" id="SubTotal" value="<?=number_format($SubTotal,2,',','.')?>" style="width:125px; font-weight:bold; text-align:right;" readonly>
			        </td>
			    </tr>
			    <tr>
			        <td align="right">
			        	<strong>Total Impuestos: </strong>
			        	<input type="text" name="TotalImpuestos" id="TotalImpuestos" value="<?=number_format($TotalImpuestos,2,',','.')?>" style="width:125px; font-weight:bold; text-align:right;" readonly>
			        </td>
			    </tr>
			    <tr>
			        <td align="right">
			        	<strong>Total General: </strong>
			        	<input type="text" name="TotalGeneral" id="TotalGeneral" value="<?=number_format($TotalGeneral,2,',','.')?>" style="width:125px; font-weight:bold; text-align:right;" readonly>
			        </td>
			    </tr>
		    </tbody>
		</table>
		<input type="hidden" id="nro_detalle" value="<?=$nro_detalle?>" />
		<input type="hidden" id="can_detalle" value="<?=$nro_detalle?>" />
	</div>

	<div id="tab2" style="display:none;">
		<input type="hidden" id="sel_partida" />
		<table width="<?=$_width?>" class="tblBotones">
			<thead>
				<tr>
					<th class="divFormCaption">Distribuci&oacute;n del Presupuesto</th>
				</tr>
			</thead>
		</table>
		<div style="overflow:scroll; height:400px; width:<?=$_width?>px; margin:auto;">
			<table class="tblLista" style="width:100%;">
				<thead>
					<tr>
						<th width="80">Partida</th>
						<th align="left">Denominaci&oacute;n</th>
						<th width="100">Monto</th>
					</tr>
				</thead>
				
				<tbody id="lista_partida">
					<?php
					$MontoTotal = 0;
					$Grupo = 0;
					$sql = "SELECT
								fmdt.*,
								ff.Denominacion AS FuenteFinanciamiento
							FROM
								vw_004formulacionmetasdist fmdt
								INNER JOIN pv_fuentefinanciamiento ff ON (ff.CodFuente = fmdt.CodFuente)
							WHERE
								fmdt.CodMeta = '$field[CodMeta]' AND
								fmdt.Ejercicio = '$field[Ejercicio]' AND
								fmdt.Monto > 0.00
							ORDER BY CodFuente, cod_partida";
					$field_partida = getRecords($sql);
					foreach ($field_partida as $f) {
						if ($Grupo <> $f['CodFuente']) {
							$Grupo = $f['CodFuente'];
							?>
							<tr class="trListaBody3">
								<td colspan="3"><?=htmlentities($f['FuenteFinanciamiento'])?></td>
							</tr>
							<?php
						}
						$MontoTotal += $f['Monto'];
						?>
						<tr class="trListaBody">
							<td align="center"><?=$f['cod_partida']?></td>
							<td><?=htmlentities($f['denominacion'])?></td>
							<td align="right"><?=number_format($f['Monto'],2,',','.')?></td>
						</tr>
					<?php } ?>
					<tr class="trListaBody2">
						<td align="center" colspan="2">TOTAL</th>
						<td align="right"><?=number_format($MontoTotal,2,',','.')?></td>
					</tr>
				</tbody>
			</table>
		</div>
		<input type="hidden" id="nro_partida" value="<?=$nro_partida?>" />
		<input type="hidden" id="can_partida" value="<?=$nro_partida?>" />
	</div>

	<center>
		<input type="submit" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" id="btSubmit" />
		<input type="button" value="Cancelar" style="width:75px;" id="btCancelar" onclick="<?=$clkCancelar?>" />
	</center>
</form>
<div style="width:<?=$_width?>px" class="divMsj">Campos Obligatorios *</div>

<script type="text/javascript" language="javascript">
	function insertar_partida(detalle) {
		if ($('#CategoriaProg').val() == '') {
			cajaModal('Debe seleccionar la Categor&iacute;a Program&aacute;tica');
		} else {
			var href = "../lib/listas/gehen.php?anz=lista_partidas&filtrar=default&ventana=pv_formulacionmetas&FlagTipoCuenta=S&fcod_tipocuenta=4&FlagMetas=S&detalle="+detalle+"&modulo=ajax&accion=partida_insertar&url=../../pv/pv_formulacionmetas_ajax.php&iframe=true&width=100%&height=100%";
			$('#a_'+detalle).attr('href',href);
			$('#a_'+detalle).click();
		}
	}
	function insertar_commodity(detalle,Clasificacion) {
		if ($('#CategoriaProg').val() == '') {
			cajaModal('Debe seleccionar la Categor&iacute;a Program&aacute;tica');
		} else {
			var href = "../lib/listas/gehen.php?anz=lista_commodities&filtrar=default&ventana=pv_formulacionmetas&detalle="+detalle+"&fClasificacion="+Clasificacion+"&FlagClasificacion=S&modulo=ajax&accion=commodity_insertar&url=../../pv/pv_formulacionmetas_ajax.php&iframe=true&width=100%&height=100%";
			$('#a_'+detalle).attr('href',href);
			$('#a_'+detalle).click();
		}
	}
	function setMontos() {
		var SubTotal = 0;
		var TotalImpuestos = 0;
		var TotalGeneral = 0;
		$('input[name="detalle_Cantidad[]"]').each(function(idx) {
			var Cantidad = setNumero($('input[name="detalle_Cantidad[]"]:eq('+idx+')').val());
			var PrecioUnitario = setNumero($('input[name="detalle_PrecioUnitario[]"]:eq('+idx+')').val());
			var MontoIva = setNumero($('input[name="detalle_MontoIva[]"]:eq('+idx+')').val());
			var MontoTotal = Cantidad * (PrecioUnitario + MontoIva);
			$('input[name="detalle_MontoTotal[]"]:eq('+idx+')').val(MontoTotal).formatCurrency();
			SubTotal += (Cantidad * PrecioUnitario);
			TotalImpuestos += (Cantidad * MontoIva);
			TotalGeneral = SubTotal + TotalImpuestos;
		});
		$('#SubTotal').val(SubTotal).formatCurrency();
		$('#TotalImpuestos').val(TotalImpuestos).formatCurrency();
		$('#TotalGeneral').val(TotalGeneral).formatCurrency();
	}
	function getDistribucion() {
		$('#lista_partida').html('Cargando...');
		//	
		$.ajax({
			type: "POST",
			url: "pv_formulacionmetas_ajax.php",
			data: "modulo=ajax&accion=getDistribucion&"+$('form').serialize(),
			async: false,
			success: function(data) {
				$('#lista_partida').html(data);
			}
		});
	}
	function getDescripcionMeta() {
		$('#Descripcion').val('');
		$.ajax({
			type: "POST",
			url: "pv_formulacionmetas_ajax.php",
			data: "modulo=ajax&accion=getDescripcionMeta&"+$('form').serialize(),
			async: false,
			success: function(data) {
				$('#Descripcion').val(data);
			}
		});
	}
</script>