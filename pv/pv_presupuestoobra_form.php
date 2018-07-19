<?php
if ($opcion == "nuevo") {
	$field['PreparadoPor'] = $_SESSION['CODPERSONA_ACTUAL'];
	$field['NomPreparadoPor'] = $_SESSION['NOMBRE_USUARIO_ACTUAL'];
	$field['FechaPreparado'] = $FechaActual;
	$field['Estado'] = 'PR';
	##
	$_titulo = "Formulaci&oacute;n Presupuestaria / Nuevo";
	$accion = "nuevo";
	$disabled_modificar = "";
	$disabled_ver = "";
	$display_modificar = "";
	$display_ver = "";
	$display_submit = "";
	$label_submit = "Guardar";
	$focus = "Denominacion";
	$getDistribucion = "getDistribucion();";
}
elseif ($opcion == "modificar" || $opcion == "ver" || $opcion == "aprobar" || $opcion == "generar" || $opcion == "anular") {
	list($CodOrganismo, $CodPresupuesto) = explode('_', $sel_registros);
	##	consulto datos generales
	$sql = "SELECT
				ppo.*,
				po.Ejercicio,
				po.CategoriaProg,
				o.Organismo,
				up1.NomCompleto AS NomPreparadoPor,
				up2.NomCompleto AS NomAprobadoPor,
				up3.NomCompleto AS NomGeneradoPor,
				up4.NomCompleto AS NomAnuladoPor
			FROM
				pv_presupuestoobra ppo
				INNER JOIN ob_planobras po ON (po.CodPlanObra = ppo.CodPlanObra)
				INNER JOIN mastorganismos o ON (o.CodOrganismo = ppo.CodOrganismo)
				LEFT JOIN mastpersonas up1 ON (up1.CodPersona = ppo.PreparadoPor)
				LEFT JOIN mastpersonas up2 ON (up2.CodPersona = ppo.AprobadoPor)
				LEFT JOIN mastpersonas up3 ON (up3.CodPersona = ppo.GeneradoPor)
				LEFT JOIN mastpersonas up4 ON (up4.CodPersona = ppo.AnuladoPor)
			WHERE
				ppo.CodOrganismo = '$CodOrganismo' AND
				ppo.CodPresupuesto = '$CodPresupuesto'";
	$field = getRecord($sql);
	##
	if ($opcion == "modificar") {
		$_titulo = "Formulaci&oacute;n Presupuestaria / Modificar";
		$accion = "modificar";
		$disabled_modificar = "disabled";
		$disabled_ver = "";
		$display_modificar = "display:none;";
		$display_ver = "";
		$display_submit = "";
		$label_submit = "Modificar";
		$focus = "Denominacion";
	}
	##
	elseif ($opcion == "aprobar") {
		$field['AprobadoPor'] = $_SESSION['CODPERSONA_ACTUAL'];
		$field['NomAprobadoPor'] = $_SESSION['NOMBRE_USUARIO_ACTUAL'];
		$field['FechaAprobado'] = $FechaActual;
		$field['Estado'] = 'AP';
		##	
		$_titulo = "Formulaci&oacute;n Presupuestaria / Aprobar";
		$accion = "aprobar";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$display_modificar = "display:none;";
		$display_ver = "display:none;";
		$display_submit = "";
		$label_submit = "Aprobar";
		$focus = "btSubmit";
	}
	##
	elseif ($opcion == "generar") {
		$field['GeneradoPor'] = $_SESSION['CODPERSONA_ACTUAL'];
		$field['NomGeneradoPor'] = $_SESSION['NOMBRE_USUARIO_ACTUAL'];
		$field['FechaGenerado'] = $FechaActual;
		$field['Estado'] = 'GE';
		##	
		$_titulo = "Formulaci&oacute;n Presupuestaria / Generar";
		$accion = "generar";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$display_modificar = "display:none;";
		$display_ver = "display:none;";
		$display_submit = "";
		$label_submit = "Generar";
		$focus = "btSubmit";
	}
	##
	elseif ($opcion == "anular") {
		$field['AnuladoPor'] = $_SESSION['CODPERSONA_ACTUAL'];
		$field['NomAnuladoPor'] = $_SESSION['NOMBRE_USUARIO_ACTUAL'];
		$field['FechaAnulado'] = $FechaActual;
		$field['Estado'] = 'AN';
		##	
		$_titulo = "Formulaci&oacute;n Presupuestaria / Anular";
		$accion = "anular";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$display_modificar = "display:none;";
		$display_ver = "display:none;";
		$display_submit = "";
		$label_submit = "Anular";
		$focus = "btSubmit";
	}
	##
	elseif ($opcion == "ver") {
		$_titulo = "Formulaci&oacute;n Presupuestaria / Ver";
		$accion = "";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
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

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=<?=$action?>" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('pv_presupuestoobra_ajax', 'modulo=formulario&accion=<?=$accion?>', this, <?=isset($FlagContinuar)?$FlagContinuar:'false'?>);" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
	<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
	<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
	<input type="hidden" name="fCategoriaProg" id="fCategoriaProg" value="<?=$fCategoriaProg?>" />
	<input type="hidden" name="fTipoObra" id="fTipoObra" value="<?=$fTipoObra?>" />
	<input type="hidden" name="fCodResponsable" id="fCodResponsable" value="<?=$fCodResponsable?>" />
	<input type="hidden" name="fCodEmpleado" id="fCodEmpleado" value="<?=$fCodEmpleado?>" />
	<input type="hidden" name="fNomPersona" id="fNomPersona" value="<?=$fNomPersona?>" />
	<input type="hidden" name="fFechaInicio" id="fFechaInicio" value="<?=$fFechaInicio?>" />
	<input type="hidden" name="fFechaFin" id="fFechaFin" value="<?=$fFechaFin?>" />
	<input type="hidden" name="fSituacion" id="fSituacion" value="<?=$fSituacion?>" />
	<input type="hidden" name="fEjercicio" id="fEjercicio" value="<?=$fEjercicio?>" />

	<div id="tab1" style="display:block;">
		<table width="<?=$_width?>" class="tblForm">
			<tr>
				<td colspan="4" class="divFormCaption">Datos Generales</td>
			</tr>
			<tr>
				<td class="tagForm" width="125">C&oacute;digo:</td>
				<td>
					<input type="text" name="CodPresupuesto" id="CodPresupuesto" value="<?=$field['CodPresupuesto']?>" style="width:100px; font-weight:bold;" readonly="readonly" />
				</td>
				<td class="tagForm" width="125">Estado:</td>
				<td>
					<input type="hidden" name="Estado" id="Estado" value="<?=$field['Estado']?>" />
					<input type="text" value="<?=strtoupper(printValores('presupuesto-obras-estado',$field['Estado']))?>" style="width:100px; font-weight:bold;" disabled />
				</td>
			</tr>
		    <tr>
				<td class="tagForm">* Plan de Obra:</td>
				<td class="gallery clearfix">
					<input type="text" name="CodPlanObra" id="CodPlanObra" value="<?=$field['CodPlanObra']?>" style="width:100px;" readonly="readonly" />
					<a href="../lib/listas/gehen.php?anz=lista_ob_planobras&filtrar=default&ventana=pv_presupuestoobra&campo1=CodPlanObra&campo2=CodOrganismo&campo3=CategoriaProg&campo4=Ejercicio&campo5=Organismo&campo6=Denominacion&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" id="aCategoriaProg" style=" <?=$display_modificar?>">
		            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
		            </a>
				</td>
				<td class="tagForm">Ejercicio:</td>
				<td>
					<input type="text" name="Ejercicio" id="Ejercicio" value="<?=$field['Ejercicio']?>" style="width:100px;" maxlength="4" disabled />
				</td>
			</tr>
		    <tr>
				<td class="tagForm">Organismo:</td>
				<td>
					<input type="hidden" name="CodOrganismo" id="CodOrganismo" value="<?=$field['CodOrganismo']?>" />
					<input type="text" name="Organismo" id="Organismo" value="<?=$field['Organismo']?>" style="width:300px;" disabled />
				</td>
				<td class="tagForm">Cat. Program&aacute;tica:</td>
				<td>
					<input type="text" name="CategoriaProg" id="CategoriaProg" value="<?=$field['CategoriaProg']?>" style="width:100px;" readonly="readonly" />
				</td>
			</tr>
		    <tr>
				<td class="tagForm" rowspan="4">* Denominaci&oacute;n:</td>
				<td rowspan="4">
					<textarea name="Denominacion" id="Denominacion" style="width:300px; height:82px;" <?=$disabled_ver?>><?=$field['Denominacion']?></textarea>
				</td>
				<td class="tagForm">Preparado Por:</td>
				<td>
					<input type="hidden" name="PreparadoPor" id="PreparadoPor" value="<?=$field['PreparadoPor']?>" />
					<input type="text" name="NomPreparadoPor" id="NomPreparadoPor" value="<?=$field['NomPreparadoPor']?>" style="width:200px;" readonly />
					<input type="text" name="FechaPreparado" id="FechaPreparado" value="<?=formatFechaDMA($field['FechaPreparado'])?>" style="width:65px;" maxlength="10" readonly />
				</td>
			</tr>
		    <tr>
				<td class="tagForm">Aprobado Por:</td>
				<td>
					<input type="hidden" name="AprobadoPor" id="AprobadoPor" value="<?=$field['AprobadoPor']?>" />
					<input type="text" name="NomAprobadoPor" id="NomAprobadoPor" value="<?=$field['NomAprobadoPor']?>" style="width:200px;" readonly />
					<input type="text" name="FechaAprobado" id="FechaAprobado" value="<?=formatFechaDMA($field['FechaAprobado'])?>" style="width:65px;" maxlength="10" readonly />
				</td>
			</tr>
		    <tr>
				<td class="tagForm">Generado Por:</td>
				<td>
					<input type="hidden" name="GeneradoPor" id="GeneradoPor" value="<?=$field['GeneradoPor']?>" />
					<input type="text" name="NomGeneradoPor" id="NomGeneradoPor" value="<?=$field['NomGeneradoPor']?>" style="width:200px;" readonly />
					<input type="text" name="FechaGenerado" id="FechaGenerado" value="<?=formatFechaDMA($field['FechaGenerado'])?>" style="width:65px;" maxlength="10" readonly />
				</td>
			</tr>
		    <tr>
				<td class="tagForm">Anulado Por:</td>
				<td>
					<input type="hidden" name="AnuladoPor" id="AnuladoPor" value="<?=$field['AnuladoPor']?>" />
					<input type="text" name="NomAnuladoPor" id="NomAnuladoPor" value="<?=$field['NomAnuladoPor']?>" style="width:200px;" readonly />
					<input type="text" name="FechaAnulado" id="FechaAnulado" value="<?=formatFechaDMA($field['FechaAnulado'])?>" style="width:65px;" maxlength="10" readonly />
				</td>
			</tr>
		    <tr>
				<td class="tagForm">&Uacute;ltima Modif.:</td>
				<td colspan="3">
					<input type="text" value="<?=$field['UltimoUsuario']?>" style="width:155px;" disabled="disabled" />
					<input type="text" value="<?=$field['UltimaFecha']?>" style="width:110px" disabled="disabled" />
				</td>
			</tr>
		</table>

		<input type="hidden" id="sel_detalle" />
		<table width="<?=$_width?>;" class="tblBotones">
			<thead>
				<tr>
					<th class="divFormCaption">DISTRIBUCI&Oacute;N DE LAS OBRAS</th>
				</tr>
			</thead>
		    <tbody>
			    <tr>
			        <td align="right" class="gallery clearfix">
						<a id="a_detalle" href="pagina.php?iframe=true&width=100%&height=430" rel="prettyPhoto[iframe2]" style="display:none;"></a>
			            <input type="button" style="width:85px;" value="Partida" onclick="insertar_partida('detalle');" <?=$disabled_ver?> />
			            <input type="button" style="width:85px;" value="Obras" onclick="insertar_commodity('detalle','');" <?=$disabled_ver?> /> |
			            <input type="button" style="width:85px;" value="Borrar" onclick="quitar(this, 'detalle'); setMontos();" <?=$disabled_ver?> />
			        </td>
			    </tr>
		    </tbody>
		</table>
		<div style="overflow:scroll; height:250px; width:<?=$_width?>px; margin:auto;">
			<table class="tblLista" style="width:100%; min-width:1000px;">
				<thead>
					<tr>
						<th width="80">Partida</th>
						<th align="left">Denominaci&oacute;n</th>
						<th width="80">C&oacute;digo</th>
						<th width="45">Uni.</th>
						<th width="75">Cantidad</th>
						<th width="100">Precio Unitario</th>
						<th width="100">Monto Total</th>
						<th width="33">F.F</th>
					</tr>
				</thead>
				
				<tbody id="lista_detalle">
					<?php
					$SubTotal=0;
					$SubTotal1=0;
					$SubTotal2=0;
					$TotalImpuestos=0;
					$TotalImpuestos1=0;
					$TotalImpuestos2=0;
					$sql = "SELECT fmd.*
							FROM pv_presupuestoobradet fmd
							WHERE
								fmd.CodOrganismo = '$field[CodOrganismo]' AND
								fmd.CodPresupuesto = '$field[CodPresupuesto]'
							ORDER BY cod_partida";
					$field_partida = getRecords($sql);
					foreach ($field_partida as $f) {
						$id = ($f['cod_partida']?$f['cod_partida']:$f['Commodity']);
						##	
						$MontoTotal = $f['Cantidad'] * $f['PrecioUnitario'];
						if ($opcion == "modificar") {
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
								<select name="detalle_CodUnidad[]" class="cell" <?=$disabled_ver?>>
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
							<td align="right"><input type="text" name="detalle_MontoTotal[]" value="<?=number_format($MontoTotal,2,',','.')?>" class="cell currency" style="text-align:right;" readonly /></td>
							<td>
								<select name="detalle_CodFuente[]" class="cell" onchange="setMontos();" <?=$disabled_ver?>>
									<?=loadSelectFromParametros2('pv_fuentefinanciamiento','CodFuente','Denominacion','FFOBRAS',$f['CodFuente'],10)?>
								</select>
							</td>
						</tr>
						<?php
						$SubTotal += ($f['Cantidad'] * $f['PrecioUnitario']);
					}
					$TotalGeneral = $SubTotal;
					?>
				</tbody>
			</table>
		</div>
		<table width="<?=$_width?>" class="tblBotones">
			<thead>
				<tr>
					<th class="divFormCaption" colspan="4">TOTAL GENERAL</th>
				</tr>
			</thead>
		    <tbody>
			    <tr>
			        <td width="150" align="right">
			        	<strong>Total Aprobado: </strong>
			        </td>
			        <td>
			        	<?php
			        	$sql = "SELECT SUM(fd.MontoAprobado)
								FROM
									pv_financiamiento f
									INNER JOIN pv_financiamientodetalle fd ON (fd.CodFinanciamiento = f.CodFinanciamiento)
								WHERE
									f.CodOrganismo = '$field[CodOrganismo]' AND
									f.Ejercicio = '$field[Ejercicio]' AND
									(fd.CodFuente = '04' OR fd.CodFuente = '08' OR fd.CodFuente = '09')";
			        	$MontoAprobado = getVar3($sql);
			        	?>
			        	<input type="text" name="MontoAprobado" id="MontoAprobado" value="<?=number_format($MontoAprobado,2,',','.')?>" style="width:125px; font-weight:bold; text-align:right;" readonly>
			        </td>
			        <td align="right">
			        	<strong>Sub-Total: </strong>
			        	<input type="text" name="SubTotal" id="SubTotal" value="<?=number_format($SubTotal,2,',','.')?>" style="width:125px; font-weight:bold; text-align:right;" readonly>
			        </td>
			    </tr>
			    <tr>
			        <td align="right">
			        	<strong>Total Distribuido: </strong>
			        </td>
			        <td>
			        	<?php
			        	$sql = "SELECT SUM(Monto) FROM vw_001presupuestoobradist WHERE Ejercicio = '$field[Ejercicio]' GROUP BY Ejercicio";
			        	$MontoDistribuido = getVar3($sql);
			        	$MontoDistribuidoInicial = $MontoDistribuido - $TotalGeneral;
			        	?>
			        	<input type="hidden" name="MontoDistribuidoInicial" id="MontoDistribuidoInicial" value="<?=$MontoDistribuidoInicial?>">
			        	<input type="text" name="MontoDistribuido" id="MontoDistribuido" value="<?=number_format($MontoDistribuido,2,',','.')?>" style="width:125px; font-weight:bold; text-align:right; color:#900;" readonly>
			        </td>
			        <td align="right">
			        	<strong>Total General: </strong>
			        	<input type="text" name="TotalGeneral" id="TotalGeneral" value="<?=number_format($TotalGeneral,2,',','.')?>" style="width:125px; font-weight:bold; text-align:right;" readonly>
			        </td>
			    </tr>
			    <tr>
			        <td align="right">
			        	<strong>Resta: </strong>
			        </td>
			        <td>
			        	<?php
			        	$TotalResta = $MontoAprobado - ($MontoDistribuido + $MontoPersonal);
			        	?>
			        	<input type="text" name="TotalResta" id="TotalResta" value="<?=number_format($TotalResta,2,',','.')?>" style="width:125px; font-weight:bold; text-align:right;" readonly>
			        </td>
			        <td>&nbsp;</td>
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
		<div style="overflow:scroll; height:300px; width:<?=$_width?>px; margin:auto;">
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
					$sql = "SELECT * FROM vw_001presupuestoobradist WHERE CategoriaProg = '$field[CategoriaProg]' AND Ejercicio = '$field[Ejercicio]' AND Monto > 0.00";
					$field_partida = getRecords($sql);
					foreach ($field_partida as $f) {
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
			cajaModal('Debe seleccionar el Plan de Obra');
		} else {
			var href = "../lib/listas/gehen.php?anz=lista_partidas&filtrar=default&ventana=listado_insertar_linea&FlagTipoCuenta=S&fcod_tipocuenta=4&FlagObra=S&detalle="+detalle+"&modulo=ajax&accion=partida_insertar&url=../../pv/pv_presupuestoobra_ajax.php&iframe=true&width=100%&height=100%";
			$('#a_'+detalle).attr('href',href);
			$('#a_'+detalle).click();
		}
	}
	function insertar_commodity(detalle) {
		if ($('#CategoriaProg').val() == '') {
			cajaModal('Debe seleccionar el Plan de Obra');
		} else {
			var href = "../lib/listas/gehen.php?anz=lista_commodities&filtrar=default&ventana=listado_insertar_linea&detalle="+detalle+"&FlagObra=S&modulo=ajax&accion=commodity_insertar&url=../../pv/pv_presupuestoobra_ajax.php&iframe=true&width=100%&height=100%";
			$('#a_'+detalle).attr('href',href);
			$('#a_'+detalle).click();
		}
	}
	function setMontos() {
		//	TOTAL GENERAL
		var SubTotal = 0;
		var TotalGeneral = 0;
		$('input[name="detalle_Cantidad[]"]').each(function(idx) {
			var Cantidad = setNumero($('input[name="detalle_Cantidad[]"]:eq('+idx+')').val());
			var PrecioUnitario = setNumero($('input[name="detalle_PrecioUnitario[]"]:eq('+idx+')').val());
			var MontoIva = setNumero($('input[name="detalle_MontoIva[]"]:eq('+idx+')').val());
			var MontoTotal = Cantidad * (PrecioUnitario + MontoIva);
			$('input[name="detalle_MontoTotal[]"]:eq('+idx+')').val(MontoTotal).formatCurrency();
			SubTotal += (Cantidad * PrecioUnitario);
			TotalGeneral = SubTotal;
		});
		$('#SubTotal').val(SubTotal).formatCurrency();
		$('#TotalGeneral').val(TotalGeneral).formatCurrency();
		//	-
		var MontoAprobado = setNumero($('#MontoAprobado').val());
		var MontoDistribuidoInicial = new Number($('#MontoDistribuidoInicial').val());
		var MontoDistribuido = MontoDistribuidoInicial + TotalGeneral;
		var TotalResta = MontoAprobado - MontoDistribuido;
		$('#MontoDistribuido').val(MontoDistribuido).formatCurrency();
		$('#TotalResta').val(TotalResta).formatCurrency();
	}
	function getDistribucion() {
		$('#lista_partida').html('Cargando...');
		//	
		$.ajax({
			type: "POST",
			url: "pv_presupuestoobra_ajax.php",
			data: "modulo=ajax&accion=getDistribucion&"+$('form').serialize(),
			async: false,
			success: function(data) {
				$('#lista_partida').html(data);
			}
		});
	}
</script>