<?php
if ($opcion == "nuevo") {
	$sql = "SELECT MAX(Ejercicio) FROM pv_reformulacionmetas";
	$Ejercicio = getVar3($sql);
	$field['Ejercicio'] = ($fEjercicio?$fEjercicio:$Ejercicio);
	$field['Estado'] = 'PR';
	$field['CategoriaProg'] = ($fCategoriaProg?$fCategoriaProg:'');
	$sql = "SELECT
				cp.CodOrganismo,
				ue.Denominacion AS UnidadEjecutora
			FROM
				pv_categoriaprog cp
				INNER JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cp.CodUnidadEjec)
			WHERE cp.CategoriaProg = '$field[CategoriaProg]'";
	$field_cp = getRecord($sql);
	$field['UnidadEjecutora'] = $field_cp['UnidadEjecutora'];
	$field['CodOrganismo'] = $field_cp['CodOrganismo'];
	##
	$_titulo = "Reformulaci&oacute;n por Metas / Crear";
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
elseif ($opcion == "modificar" || $opcion == "ver" || $opcion == "aprobar") {
	list($CodMeta, $Ejercicio) = explode('_', $sel_registros);
	##	consulto datos generales
	$sql = "SELECT
				fm.*,
				op.CodObjetivo,
				cp.CategoriaProg,
				cp.CodOrganismo,
				ue.Denominacion AS UnidadEjecutora
			FROM
				pv_reformulacionmetas fm
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
		$_titulo = "Reformulaci&oacute;n por Metas / Modificar";
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
		$_titulo = "Reformulaci&oacute;n por Metas / Aprobar";
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
	elseif ($opcion == "ver") {
		$_titulo = "Reformulaci&oacute;n por Metas / Ver";
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

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=<?=$action?>" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('pv_reformulacionmetas_ajax', 'modulo=formulario&accion=<?=$accion?>', this, <?=isset($FlagContinuar)?$FlagContinuar:'false'?>);" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
	<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
	<input type="hidden" name="fCategoriaProg" id="fCategoriaProg" value="<?=$fCategoriaProg?>" />
	<input type="hidden" name="fCodObjetivo" id="fCodObjetivo" value="<?=$fCodObjetivo?>" />
	<input type="hidden" name="fEjercicio" id="fEjercicio" value="<?=$fEjercicio?>" />

	<div id="tab1" style="display:block;">
		<table width="<?=$_width?>" class="tblForm">
			<tr>
		    	<td colspan="4" class="divFormCaption">Datos Generales</td>
		    </tr>
		    <tr>
				<td class="tagForm" width="125">* Ejercicio:</td>
				<td>
					<input type="text" name="Ejercicio" id="Ejercicio" value="<?=$field['Ejercicio']?>" style="width:50px;" <?=$read_modificar?> onchange="getMontoAprobado();">
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
					<a href="../lib/listas/gehen.php?anz=lista_pv_categoriaprog&filtrar=default&ventana=pv_reformulacionmetas&campo1=CategoriaProg&campo2=UnidadEjecutora&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" id="aCategoriaProg" style=" <?=$display_modificar?>">
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
			<table class="tblLista" style="width:100%; min-width:1200px;">
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
						<th width="100">Monto Formulado</th>
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
							FROM pv_reformulacionmetasdet fmd
							WHERE
								fmd.CodMeta = '$CodMeta' AND
								fmd.Ejercicio = '$Ejercicio'
							ORDER BY cod_partida";
					$field_partida = getRecords($sql);
					foreach ($field_partida as $f) {
						$id = ($f['cod_partida']?$f['cod_partida']:$f['Commodity']);
						##	
						$MontoTotal = $f['Cantidad'] * ($f['PrecioUnitario'] + $f['MontoIva']);
						$sql = "SELECT SUM(Monto)
								FROM vw_004formulacionmetasdist
								WHERE
									cod_partida = '$f[cod_partida]' AND
									Estado = 'RF'
								GROUP BY Ejercicio, CategoriaProg";
						$MontoFormulado = getVar3($sql);
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
							<td align="right"><input type="text" name="detalle_MontoIva[]" value="<?=number_format($f['MontoIva'],2,',','.')?>" class="cell currency" style="text-align:right;" onchange="setMontos();" <?=$disabled_ver?> /></td>
							<td align="right"><input type="text" name="detalle_MontoTotal[]" value="<?=number_format($MontoTotal,2,',','.')?>" class="cell currency" style="text-align:right;" readonly /></td>
							<td align="right"><input type="text" name="detalle_MontoFormulado[]" value="<?=number_format($MontoFormulado,2,',','.')?>" class="cell currency" style="text-align:right;" readonly /></td>
							<td>
								<select name="detalle_CodFuente[]" class="cell" onchange="setMontos();" <?=$disabled_ver?>>
									<?=loadSelectFromParametros2('pv_fuentefinanciamiento','CodFuente','Denominacion','FFMETAS',$f['CodFuente'],10)?>
								</select>
							</td>
						</tr>
						<?php
						$SubTotal += ($f['Cantidad'] * $f['PrecioUnitario']);
						$TotalImpuestos += ($f['Cantidad'] * $f['MontoIva']);
						if ($f['CodFuente'] == '02' || $f['CodFuente'] == '03') {
							$SubTotal1 += ($f['Cantidad'] * $f['PrecioUnitario']);
							$TotalImpuestos1 += ($f['Cantidad'] * $f['MontoIva']);
						}
						elseif ($f['CodFuente'] == '01') {
							$SubTotal2 += ($f['Cantidad'] * $f['PrecioUnitario']);
							$TotalImpuestos2 += ($f['Cantidad'] * $f['MontoIva']);
						}
					}
					$TotalGeneral = $SubTotal + $TotalImpuestos;
					$TotalGeneral1 = $SubTotal1 + $TotalImpuestos1;
					$TotalGeneral2 = $SubTotal2 + $TotalImpuestos2;
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
									(fd.CodFuente = '02' OR fd.CodFuente = '03')";
			        	$MontoAprobado1 = getVar3($sql);
			        	##	
			        	$sql = "SELECT MontoProyecto
								FROM ha_presupuesto
								WHERE
									CodOrganismo = '$field[CodOrganismo]' AND
									Ejercicio = '$field[Ejercicio]' AND
									Estado = 'AP'";
			        	$MontoAprobado2 = getVar3($sql);
			        	##	
			        	$MontoAprobado = $MontoAprobado1 + $MontoAprobado2;
			        	##	
						$sql = "SELECT SUM(Monto)
								FROM vw_003formulacionpersonaldist
								WHERE Ejercicio = '$field[Ejercicio]'
								GROUP BY Ejercicio";
						$MontoPersonal1 = getVar3($sql);
						$MontoPersonal2 = 0;
    					$MontoPersonal = $MontoPersonal1 + $MontoPersonal2;
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
			        	$sql = "SELECT SUM(Monto) FROM vw_002reformulacionmetasdist WHERE Ejercicio = '$field[Ejercicio]' GROUP BY Ejercicio";
			        	$MontoDistribuido = getVar3($sql);
			        	$MontoDistribuidoInicial = $MontoDistribuido - $TotalGeneral;
			        	?>
			        	<input type="hidden" name="MontoDistribuidoInicial" id="MontoDistribuidoInicial" value="<?=$MontoDistribuidoInicial?>">
			        	<input type="text" name="MontoDistribuido" id="MontoDistribuido" value="<?=number_format($MontoDistribuido,2,',','.')?>" style="width:125px; font-weight:bold; text-align:right; color:#900;" readonly>
			        </td>
			        <td align="right">
			        	<strong>Total Impuestos: </strong>
			        	<input type="text" name="TotalImpuestos" id="TotalImpuestos" value="<?=number_format($TotalImpuestos,2,',','.')?>" style="width:125px; font-weight:bold; text-align:right;" readonly>
			        </td>
			    </tr>
			    <tr>
			        <td align="right">
			        	<strong>Proyecci&oacute;n Personal: </strong>
			        </td>
			        <td>
			        	<input type="text" name="MontoPersonal" id="MontoPersonal" value="<?=number_format($MontoPersonal,2,',','.')?>" style="width:125px; font-weight:bold; text-align:right; color:#900;" readonly>
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
			        <td align="right">&nbsp;</td>
			    </tr>
		    </tbody>
		</table>
		<table width="<?=$_width?>" class="tblBotones">
			<thead>
				<tr>
					<th class="divFormCaption" colspan="4">SITUADO</th>
				</tr>
			</thead>
		    <tbody>
			    <tr>
			        <td width="150" align="right">
			        	<strong>Total Aprobado: </strong>
			        </td>
			        <td>
			        	<input type="text" name="MontoAprobado1" id="MontoAprobado1" value="<?=number_format($MontoAprobado1,2,',','.')?>" style="width:125px; font-weight:bold; text-align:right;" readonly>
			        </td>
			        <td align="right">
			        	<strong>Sub-Total: </strong>
			        	<input type="text" name="SubTotal1" id="SubTotal1" value="<?=number_format($SubTotal1,2,',','.')?>" style="width:125px; font-weight:bold; text-align:right;" readonly>
			        </td>
			    </tr>
			    <tr>
			        <td align="right">
			        	<strong>Total Distribuido: </strong>
			        </td>
			        <td>
			        	<?php
			        	$sql = "SELECT SUM(Monto)
				    			FROM vw_002reformulacionmetasdist
				    			WHERE
				    				Ejercicio = '$field[Ejercicio]' AND
				    				(CodFuente = '02' || CodFuente = '03')
				    			GROUP BY Ejercicio";
			        	$MontoDistribuido1 = getVar3($sql);
			        	$MontoDistribuidoInicial1 = $MontoDistribuido1 - $TotalGeneral1;
			        	?>
			        	<input type="hidden" name="MontoDistribuidoInicial1" id="MontoDistribuidoInicial1" value="<?=$MontoDistribuidoInicial1?>">
			        	<input type="text" name="MontoDistribuido1" id="MontoDistribuido1" value="<?=number_format($MontoDistribuido1,2,',','.')?>" style="width:125px; font-weight:bold; text-align:right; color:#900;" readonly>
			        </td>
			        <td align="right">
			        	<strong>Total Impuestos: </strong>
			        	<input type="text" name="TotalImpuestos1" id="TotalImpuestos1" value="<?=number_format($TotalImpuestos1,2,',','.')?>" style="width:125px; font-weight:bold; text-align:right;" readonly>
			        </td>
			    </tr>
			    <tr>
			        <td align="right">
			        	<strong>Proyecci&oacute;n Personal: </strong>
			        </td>
			        <td>
			        	<input type="text" name="MontoPersonal1" id="MontoPersonal1" value="<?=number_format($MontoPersonal1,2,',','.')?>" style="width:125px; font-weight:bold; text-align:right; color:#900;" readonly>
			        </td>
			        <td align="right">
			        	<strong>Total General: </strong>
			        	<input type="text" name="TotalGeneral1" id="TotalGeneral1" value="<?=number_format($TotalGeneral1,2,',','.')?>" style="width:125px; font-weight:bold; text-align:right;" readonly>
			        </td>
			    </tr>
			    <tr>
			        <td align="right">
			        	<strong>Resta: </strong>
			        </td>
			        <td>
			        	<?php
			        	$TotalResta1 = $MontoAprobado1 - ($MontoDistribuido1 + $MontoPersonal1);
			        	?>
			        	<input type="text" name="TotalResta1" id="TotalResta1" value="<?=number_format($TotalResta1,2,',','.')?>" style="width:125px; font-weight:bold; text-align:right;" readonly>
			        </td>
			        <td align="right">&nbsp;</td>
			    </tr>
		    </tbody>
		</table>
		<table width="<?=$_width?>" class="tblBotones">
			<thead>
				<tr>
					<th class="divFormCaption" colspan="4">INGRESOS PROPIOS</th>
				</tr>
			</thead>
		    <tbody>
			    <tr>
			        <td width="150" align="right">
			        	<strong>Total Aprobado: </strong>
			        </td>
			        <td>
			        	<input type="text" name="MontoAprobado2" id="MontoAprobado2" value="<?=number_format($MontoAprobado2,2,',','.')?>" style="width:125px; font-weight:bold; text-align:right;" readonly>
			        </td>
			        <td align="right">
			        	<strong>Sub-Total: </strong>
			        	<input type="text" name="SubTotal2" id="SubTotal2" value="<?=number_format($SubTotal2,2,',','.')?>" style="width:125px; font-weight:bold; text-align:right;" readonly>
			        </td>
			    </tr>
			    <tr>
			        <td align="right">
			        	<strong>Total Distribuido: </strong>
			        </td>
			        <td>
			        	<?php
			        	$sql = "SELECT SUM(Monto)
				    			FROM vw_002reformulacionmetasdist
				    			WHERE
				    				Ejercicio = '$field[Ejercicio]' AND
				    				CodFuente = '01'
				    			GROUP BY Ejercicio";
			        	$MontoDistribuido2 = getVar3($sql);
			        	$MontoDistribuidoInicial2 = $MontoDistribuido2 - $TotalGeneral2;
			        	?>
			        	<input type="hidden" name="MontoDistribuidoInicial2" id="MontoDistribuidoInicial2" value="<?=$MontoDistribuidoInicial2?>">
			        	<input type="text" name="MontoDistribuido2" id="MontoDistribuido2" value="<?=number_format($MontoDistribuido2,2,',','.')?>" style="width:125px; font-weight:bold; text-align:right; color:#900;" readonly>
			        </td>
			        <td align="right">
			        	<strong>Total Impuestos: </strong>
			        	<input type="text" name="TotalImpuestos2" id="TotalImpuestos2" value="<?=number_format($TotalImpuestos2,2,',','.')?>" style="width:125px; font-weight:bold; text-align:right;" readonly>
			        </td>
			    </tr>
			    <tr>
			        <td align="right">
			        	<strong>Proyecci&oacute;n Personal: </strong>
			        </td>
			        <td>
			        	<input type="text" name="MontoPersonal2" id="MontoPersonal2" value="<?=number_format($MontoPersonal2,2,',','.')?>" style="width:125px; font-weight:bold; text-align:right; color:#900;" readonly>
			        </td>
			        <td align="right">
			        	<strong>Total General: </strong>
			        	<input type="text" name="TotalGeneral2" id="TotalGeneral2" value="<?=number_format($TotalGeneral2,2,',','.')?>" style="width:125px; font-weight:bold; text-align:right;" readonly>
			        </td>
			    </tr>
			    <tr>
			        <td align="right">
			        	<strong>Resta: </strong>
			        </td>
			        <td>
			        	<?php
			        	$TotalResta2 = $MontoAprobado2 - ($MontoDistribuido2 + $MontoPersonal2);
			        	?>
			        	<input type="text" name="TotalResta2" id="TotalResta2" value="<?=number_format($TotalResta2,2,',','.')?>" style="width:125px; font-weight:bold; text-align:right;" readonly>
			        </td>
			        <td align="right">&nbsp;</td>
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
					$sql = "SELECT * FROM vw_002reformulacionmetasdist WHERE CodMeta = '$field[CodMeta]' AND Ejercicio = '$field[Ejercicio]' AND Monto > 0.00";
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
			cajaModal('Debe seleccionar la Categor&iacute;a Program&aacute;tica');
		} else {
			var href = "../lib/listas/gehen.php?anz=lista_partidas&filtrar=default&ventana=pv_formulacionmetas&FlagTipoCuenta=S&fcod_tipocuenta=4&FlagMetas=S&detalle="+detalle+"&modulo=ajax&accion=partida_insertar&url=../../pv/pv_reformulacionmetas_ajax.php&iframe=true&width=100%&height=100%";
			$('#a_'+detalle).attr('href',href);
			$('#a_'+detalle).click();
		}
	}
	function insertar_commodity(detalle,Clasificacion) {
		if ($('#CategoriaProg').val() == '') {
			cajaModal('Debe seleccionar la Categor&iacute;a Program&aacute;tica');
		} else {
			var href = "../lib/listas/gehen.php?anz=lista_commodities&filtrar=default&ventana=pv_formulacionmetas&detalle="+detalle+"&fClasificacion="+Clasificacion+"&FlagClasificacion=S&modulo=ajax&accion=commodity_insertar&url=../../pv/pv_reformulacionmetas_ajax.php&iframe=true&width=100%&height=100%";
			$('#a_'+detalle).attr('href',href);
			$('#a_'+detalle).click();
		}
	}
	function setMontos() {
		//	TOTAL GENERAL
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
		//	-
		var MontoPersonal = setNumero($('#MontoPersonal').val());
		var MontoAprobado = setNumero($('#MontoAprobado').val());
		var MontoDistribuidoInicial = new Number($('#MontoDistribuidoInicial').val());
		var MontoDistribuido = MontoDistribuidoInicial + TotalGeneral;
		var TotalResta = MontoAprobado - (MontoDistribuido + MontoPersonal);
		$('#MontoDistribuido').val(MontoDistribuido).formatCurrency();
		$('#TotalResta').val(TotalResta).formatCurrency();
		//	SITUADO
		var SubTotal1 = 0;
		var TotalImpuestos1 = 0;
		var TotalGeneral1 = 0;
		$('input[name="detalle_Cantidad[]"]').each(function(idx) {
			var CodFuente = $('select[name="detalle_CodFuente[]"]:eq('+idx+')').val();
			if (CodFuente == '02' || CodFuente == '03') {
				var Cantidad = setNumero($('input[name="detalle_Cantidad[]"]:eq('+idx+')').val());
				var PrecioUnitario = setNumero($('input[name="detalle_PrecioUnitario[]"]:eq('+idx+')').val());
				var MontoIva = setNumero($('input[name="detalle_MontoIva[]"]:eq('+idx+')').val());
				var MontoTotal = Cantidad * (PrecioUnitario + MontoIva);
				$('input[name="detalle_MontoTotal[]"]:eq('+idx+')').val(MontoTotal).formatCurrency();
				SubTotal1 += (Cantidad * PrecioUnitario);
				TotalImpuestos1 += (Cantidad * MontoIva);
				TotalGeneral1 = SubTotal1 + TotalImpuestos1;
			}
		});
		$('#SubTotal1').val(SubTotal1).formatCurrency();
		$('#TotalImpuestos1').val(TotalImpuestos1).formatCurrency();
		$('#TotalGeneral1').val(TotalGeneral1).formatCurrency();
		//	-
		var MontoPersonal1 = setNumero($('#MontoPersonal1').val());
		var MontoAprobado1 = setNumero($('#MontoAprobado1').val());
		var MontoDistribuidoInicial1 = new Number($('#MontoDistribuidoInicial1').val());
		var MontoDistribuido1 = MontoDistribuidoInicial1 + TotalGeneral1;
		var TotalResta1 = MontoAprobado1 - (MontoDistribuido1 + MontoPersonal1);
		$('#MontoDistribuido1').val(MontoDistribuido1).formatCurrency();
		$('#TotalResta1').val(TotalResta1).formatCurrency();
		//	INGRESOS PROPIOS
		var SubTotal2 = 0;
		var TotalImpuestos2 = 0;
		var TotalGeneral2 = 0;
		$('input[name="detalle_Cantidad[]"]').each(function(idx) {
			var CodFuente = $('select[name="detalle_CodFuente[]"]:eq('+idx+')').val();
			if (CodFuente == '01') {
				var Cantidad = setNumero($('input[name="detalle_Cantidad[]"]:eq('+idx+')').val());
				var PrecioUnitario = setNumero($('input[name="detalle_PrecioUnitario[]"]:eq('+idx+')').val());
				var MontoIva = setNumero($('input[name="detalle_MontoIva[]"]:eq('+idx+')').val());
				var MontoTotal = Cantidad * (PrecioUnitario + MontoIva);
				$('input[name="detalle_MontoTotal[]"]:eq('+idx+')').val(MontoTotal).formatCurrency();
				SubTotal2 += (Cantidad * PrecioUnitario);
				TotalImpuestos2 += (Cantidad * MontoIva);
				TotalGeneral2 = SubTotal2 + TotalImpuestos2;
			}
		});
		$('#SubTotal2').val(SubTotal2).formatCurrency();
		$('#TotalImpuestos2').val(TotalImpuestos2).formatCurrency();
		$('#TotalGeneral2').val(TotalGeneral2).formatCurrency();
		//	
		var MontoPersonal2 = setNumero($('#MontoPersonal2').val());
		var MontoAprobado2 = setNumero($('#MontoAprobado2').val());
		var MontoDistribuidoInicial2 = new Number($('#MontoDistribuidoInicial2').val());
		var MontoDistribuido2 = MontoDistribuidoInicial2 + TotalGeneral2;
		var TotalResta2 = MontoAprobado2 - (MontoDistribuido2 + MontoPersonal2);
		$('#MontoDistribuido2').val(MontoDistribuido2).formatCurrency();
		$('#TotalResta2').val(TotalResta2).formatCurrency();
	}
	function getDistribucion() {
		$('#lista_partida').html('Cargando...');
		//	
		$.ajax({
			type: "POST",
			url: "pv_reformulacionmetas_ajax.php",
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
			url: "pv_reformulacionmetas_ajax.php",
			data: "modulo=ajax&accion=getDescripcionMeta&"+$('form').serialize(),
			async: false,
			success: function(data) {
				$('#Descripcion').val(data);
			}
		});
	}
	function getMontoAprobado() {
		$.ajax({
			type: "POST",
			url: "pv_reformulacionmetas_ajax.php",
			data: "modulo=ajax&accion=getMontoAprobado&"+$('form').serialize(),
			async: false,
			dataType: "json",
			success: function(data) {
				//	TOTAL GENERAL
				var TotalGeneral = setNumero($('#TotalGeneral').val());
				var MontoDistribuido = data['MontoDistribuido'] + TotalGeneral;
				$('#MontoAprobado').val(data['MontoAprobado']).formatCurrency();
				$('#MontoDistribuido').val(MontoDistribuido).formatCurrency();
				var MontoDistribuidoInicial = data['MontoDistribuido'] - TotalGeneral;
				$('#MontoDistribuidoInicial').val(MontoDistribuidoInicial);
				var TotalResta = data['MontoAprobado'] - MontoDistribuido;
				$('#TotalResta').val(TotalResta).formatCurrency();
				//	SITUADO
				var TotalGeneral1 = setNumero($('#TotalGeneral1').val());
				var MontoDistribuido1 = data['MontoDistribuido1'] + TotalGeneral;
				$('#MontoAprobado1').val(data['MontoAprobado1']).formatCurrency();
				$('#MontoDistribuido1').val(MontoDistribuido1).formatCurrency();
				var MontoDistribuidoInicial1 = data['MontoDistribuido1'] - TotalGeneral1;
				$('#MontoDistribuidoInicial1').val(MontoDistribuidoInicial1);
				var TotalResta1 = data['MontoAprobado1'] - MontoDistribuido1;
				$('#TotalResta1').val(TotalResta1).formatCurrency();
				//	INGRESOS PROPIOS
				var TotalGeneral2 = setNumero($('#TotalGeneral2').val());
				var MontoDistribuido2 = data['MontoDistribuido2'] + TotalGeneral;
				$('#MontoAprobado2').val(data['MontoAprobado2']).formatCurrency();
				$('#MontoDistribuido2').val(MontoDistribuido2).formatCurrency();
				var MontoDistribuidoInicial2 = data['MontoDistribuido2'] - TotalGeneral2;
				$('#MontoDistribuidoInicial2').val(MontoDistribuidoInicial2);
				var TotalResta2 = data['MontoAprobado2'] - MontoDistribuido2;
				$('#TotalResta2').val(TotalResta2).formatCurrency();
			}
		});
	}
</script>