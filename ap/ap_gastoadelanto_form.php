<?php
if ($opcion == "nuevo") {
	$field['Estado'] = 'PR';
	$field['CodCentroCosto'] = getVar3("SELECT CodCentroCosto FROM ac_mastcentrocosto WHERE Codigo = '$_PARAMETRO[CCOSTOCXP]'");
	$field['CentroCosto'] = $_PARAMETRO['CCOSTOCXP'];
	$field['FechaDocumento'] = $FechaActual;
	$field['FechaEsperadaPago'] = $FechaActual;
	$field['FechaPago'] = $FechaActual;
	$field['PreparadoPor'] = $_SESSION["CODPERSONA_ACTUAL"];
	$field['NomPreparadoPor'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
	$field['FechaPreparado'] = $Ahora;
	$field['CodClasificacion'] = 'AP';
	$field['TipoAdelanto'] = 'P';
	$field['CodTipoServicio'] = 'SOIVA';
	$sql = "SELECT i.FactorPorcentaje
			FROM masttiposervicioimpuesto tsi
			INNER JOIN mastimpuestos i ON i.CodImpuesto = tsi.CodImpuesto
			WHERE
				tsi.CodTipoServicio = '$field[CodTipoServicio]'
				AND i.CodRegimenFiscal = 'I'
			LIMIT 0, 1";
	$field['FactorPorcentaje'] = floatval(getVar3($sql));
	##
	$_titulo = "Adelantos de Proveedores / Nuevo Registro";
	$accion = "nuevo";
	$disabled_modificar = "";
	$disabled_ver = "";
	$read_modificar = "";
	$display_modificar = "";
	$display_ver = "";
	$display_submit = "";
	$label_submit = "Guardar";
	$focus = "CodPersona";
}
elseif ($opcion == "modificar" || $opcion == "ver" || $opcion == "aprobar" || $opcion == "anular") {
	##	consulto datos generales
	$sql = "SELECT
				ga.*,
				cc.Codigo AS CentroCosto,
				p1.NomCompleto AS NomProveedor,
				-- p2.NomCompleto AS NomPagarA,
				p3.NomCompleto AS NomPreparadoPor,
				p4.NomCompleto AS NomAprobadoPor,
				i.FactorPorcentaje
			FROM ap_gastoadelanto ga
			INNER JOIN ac_mastcentrocosto cc ON cc.CodCentroCosto = ga.CodCentroCosto
			INNER JOIN mastpersonas p1 ON p1.CodPersona = ga.CodProveedor
			INNER JOIN mastpersonas p2 ON p2.CodPersona = ga.CodPagarA
			LEFT JOIN mastpersonas p3 ON p3.CodPersona = ga.PreparadoPor
			LEFT JOIN mastpersonas p4 ON p4.CodPersona = ga.AprobadoPor
			LEFT JOIN masttiposervicioimpuesto tsi ON tsi.CodTipoServicio = ga.CodTipoServicio
			LEFT JOIN mastimpuestos i ON (
				i.CodImpuesto = tsi.CodImpuesto
				AND i.CodRegimenFiscal = 'I'
			)
			WHERE ga.CodAdelanto = '$sel_registros'";
	$field = getRecord($sql);
	##	
	$sql = "SELECT
				gai.*,
				i.Descripcion,
				i.FlagImponible,
				i.Signo
			FROM ap_gastoadelantoimpuesto gai
			INNER JOIN mastimpuestos i ON i.CodImpuesto = gai.CodImpuesto
			WHERE gai.CodAdelanto = '$sel_registros'
			ORDER BY Secuencia";
	$field_retencion = getRecords($sql);
	##
	if ($opcion == "modificar") {
		$_titulo = "Adelantos de Proveedores / Modificar Registro";
		$accion = "modificar";
		$disabled_modificar = "disabled";
		$disabled_ver = "";
		$read_modificar = "readonly";
		$display_modificar = "display:none;";
		$display_ver = "";
		$display_submit = "";
		$label_submit = "Modificar";
		$focus = "Comentarios";
	}
	##
	elseif ($opcion == "aprobar") {
		$field['Estado'] = 'AP';
		$field['AprobadoPor'] = $_SESSION["CODPERSONA_ACTUAL"];
		$field['NomAprobadoPor'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
		$field['FechaAprobado'] = $Ahora;
		##	
		$_titulo = "Adelantos de Proveedores / Aprobar Registro";
		$accion = "aprobar";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$read_modificar = "readonly";
		$display_modificar = "display:none;";
		$display_ver = "display:none;";
		$display_submit = "";
		$label_submit = "Aprobar";
		$focus = "btSubmit";
	}
	##
	elseif ($opcion == "anular") {
		$_titulo = "Adelantos de Proveedores / Anular Registro";
		$accion = "anular";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$read_modificar = "readonly";
		$display_modificar = "display:none;";
		$display_ver = "display:none;";
		$display_submit = "";
		$label_submit = "Anular";
		$focus = "btSubmit";
	}
	##
	elseif ($opcion == "ver") {
		$_titulo = "Adelantos de Proveedores / Ver Registro";
		$accion = "";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$read_modificar = "readonly";
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
if ($origen == 'framemain') $action = '../framemain.php';
elseif (!empty($origen)) $action = "gehen.php?anz=$origen";
else $action = "gehen.php?anz=ap_gastoadelanto_lista";
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="<?=$action?>" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('ap_gastoadelanto_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
	<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
	<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
	<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
	<input type="hidden" name="fFechaDocumentoD" id="fFechaDocumentoD" value="<?=$fFechaDocumentoD?>" />
	<input type="hidden" name="fFechaDocumentoH" id="fFechaDocumentoH" value="<?=$fFechaDocumentoH?>" />
	<input type="hidden" name="fCodProveedor" id="fCodProveedor" value="<?=$fCodProveedor?>" />
	<input type="hidden" name="fNomProveedor" id="fNomProveedor" value="<?=htmlentities($fNomProveedor)?>" />
	<input type="hidden" name="fDocFiscalProveedor" id="fDocFiscalProveedor" value="<?=$fDocFiscalProveedor?>" />
	<input type="hidden" name="fTipoAdelanto" id="fTipoAdelanto" value="<?=$fTipoAdelanto?>" />
	<input type="hidden" name="CodAdelanto" id="CodAdelanto" value="<?=$field['CodAdelanto']?>" />

	<table style="width:100%; min-width:<?=$_width?>px;" class="tblForm">
		<tr>
	    	<td colspan="6" class="divFormCaption">DATOS GENERALES</td>
	    </tr>
	    <tr>
			<td class="tagForm" width="150">* Organismo:</td>
			<td>
				<select name="CodOrganismo" id="CodOrganismo" style="width:325px;" <?=$disabled_modificar?>>
					<?=getOrganismos($field['CodOrganismo'], 3)?>
				</select>
			</td>
			<td class="tagForm">Nro. Adelanto:</td>
			<td>
	        	<input type="text" name="NroAdelanto" id="NroAdelanto" value="<?=$field['NroAdelanto']?>" style="width:100px; font-weight:bold;" readonly />
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Proveedor:</td>
			<td class="gallery clearfix">
				<input type="hidden" name="DocFiscalProveedor" id="DocFiscalProveedor" value="<?=$field['DocFiscalProveedor']?>">
				<input type="text" name="CodProveedor" id="CodProveedor" value="<?=$field['CodProveedor']?>" style="width:66px;" readonly />
				<input type="text" name="NomProveedor" id="NomProveedor" value="<?=htmlentities($field['NomProveedor'])?>" style="width:255px;" readonly />
				<a href="../lib/listas/gehen.php?anz=lista_personas&campo1=CodProveedor&campo2=NomProveedor&campo3=DocFiscalProveedor&campo4=CodPagarA&campo5=NomPagarA&campo6=DocFiscalPagarA&campo7=CodTipoPago&campo8=CodTipoServicio&ventana=selListaGastoAdelanto&filtrar=default&FlagClasePersona=S&fEsProveedor=S&fEsOtros=S&concepto=07-0001&_APLICACION=<?=$_APLICACION?>&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" style=" <?=$display_modificar?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
			<td class="tagForm">Estado:</td>
			<td>
				<input type="hidden" name="Estado" id="Estado" value="<?=$field['Estado']?>">
	        	<input type="text" value="<?=mb_strtoupper(printValores('adelanto-estado',$field['Estado']))?>" style="width:100px; font-weight:bold;" disabled />
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Pagar A:</td>
			<td class="gallery clearfix">
				<input type="hidden" name="DocFiscalPagarA" id="DocFiscalPagarA" value="<?=$field['DocFiscalPagarA']?>">
				<input type="text" name="CodPagarA" id="CodPagarA" value="<?=$field['CodPagarA']?>" style="width:66px;" readonly />
				<input type="text" name="NomPagarA" id="NomPagarA" value="<?=htmlentities($field['NomPagarA'])?>" style="width:255px;" readonly />
				<a href="../lib/listas/gehen.php?anz=lista_personas&campo1=CodPagarA&campo2=NomPagarA&campo3=DocFiscalPagarA&ventana=filtro&filtrar=default&FlagClasePersona=S&fEsProveedor=S&fEsOtros=S&concepto=07-0001&_APLICACION=<?=$_APLICACION?>&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe2]" style=" <?=$display_modificar?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
			<td class="tagForm">* Fecha Documento:</td>
			<td>
				<input type="text" name="FechaDocumento" id="FechaDocumento" value="<?=formatFechaDMA($field['FechaDocumento'])?>" maxlength="10" style="width:100px;" class="datepicker" onkeyup="setFechaDMA(this);" <?=$disabled_ver?> />
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Clasificación:</td>
			<td>
				<select name="CodClasificacion" id="CodClasificacion" style="width:325px;" <?=$disabled_ver?>>
					<option value="">&nbsp;</option>
					<?=clasificacion_adelanto($field['CodClasificacion'])?>
				</select>
			</td>
			<td class="tagForm">* Fecha Pago:</td>
			<td>
				<input type="text" name="FechaPago" id="FechaPago" value="<?=formatFechaDMA($field['FechaPago'])?>" maxlength="10" style="width:100px;" class="datepicker" onkeyup="setFechaDMA(this);" <?=$disabled_ver?> />
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Tipo de Adelanto:</td>
			<td>
				<select name="TipoAdelanto" id="TipoAdelanto" style="width:325px;" <?=$disabled_ver?>>
					<option value="">&nbsp;</option>
					<?=loadSelectValores("adelanto-tipo", $field['TipoAdelanto'])?>
				</select>
			</td>
			<td class="tagForm">Compromiso:</td>
			<td class="gallery clearfix">
				<a id="a_compromiso" href="../lib/listas/gehen.php?ventana=ap_gastoadelanto&anz=lista_compromisos&filtrar=default&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe3]" style="display:none;"></a>

				<input type="hidden" name="Anio" id="Anio" value="<?=$field['Anio']?>">
				<input type="hidden" name="NroOrden" id="NroOrden" value="<?=$field['NroOrden']?>">
				<input type="text" name="TipoCompromiso" id="TipoCompromiso" value="<?=$field['TipoCompromiso']?>" style="width:30px;" readonly />
				<input type="text" name="NroCompromiso" id="NroCompromiso" value="<?=$field['NroCompromiso']?>" style="width:100px;" readonly />

				<a href="javascript:" onclick="abrirListaCompromisos();" style=" <?=$display_ver?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
		</tr>
		<tr>
			<td class="tagForm" rowspan="3">Descripción:</td>
			<td rowspan="3">
				<textarea name="Descripcion" id="Descripcion" style="width:325px;" rows="3" <?=$disabled_ver?>><?=htmlentities($field['Descripcion'])?></textarea>
			</td>
			<td class="tagForm">Oblig. Rel.:</td>
			<td>
	        	<input type="text" name="ObligacionTipoDocumento" id="ObligacionTipoDocumento" value="<?=$field['ObligacionTipoDocumento']?>" style="width:30px;" readonly />
	        	<input type="text" name="ObligacionNroDocumento" id="ObligacionNroDocumento" value="<?=$field['ObligacionNroDocumento']?>" style="width:100px;" readonly />
			</td>
		</tr>
		<tr>
			<th style="text-align: right;">Información para el Pago</th>
			<th></th>
		</tr>
		<tr>
			<td class="tagForm">* Tipo de Pago:</td>
			<td>
				<select name="CodTipoPago" id="CodTipoPago" style="width:125px;" <?=$disabled_ver?>>
					<option value="">&nbsp;</option>
					<?=loadSelect2('masttipopago','CodTipoPago','TipoPago',$field['CodTipoPago'])?>
				</select>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td class="tagForm">* Tipo de Servicio:</td>
			<td>
				<select name="CodTipoServicio" id="CodTipoServicio" style="width:125px;" onchange="setFactorPorcentaje(this.value);" <?=$disabled_ver?>>
					<option value="">&nbsp;</option>
					<?=loadSelect2('masttiposervicio','CodTipoServicio','Descripcion',$field['CodTipoServicio'])?>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="2" rowspan="12">
				<center>
				<input type="hidden" id="sel_retencion" />
				<table style="width:90%;" class="tblBotones">
					<thead>
						<tr>
							<th class="divFormCaption" colspan="2" style="text-align: center;">Otros Impuestos / Retenciones</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td align="right" class="gallery clearfix">
								<a id="a_retencion" href="../lib/listas/gehen.php?anz=lista_impuestos&filtrar=default&ventana=listado_insertar_linea_adelanto&detalle=retencion&modulo=ajax&accion=retencion_insertar&url=../../ap/ap_gastoadelanto_ajax.php&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe4]" style="display:none;"></a>

								<input type="button" class="btLista" value="Intertar" onclick="abrirListaImpuesto();" <?=$disabled_ver?> />
								<input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'retencion'); setTotales();" <?=$disabled_ver?> />
							</td>
						</tr>
					</tbody>
				</table>
				<div style="overflow:scroll; height:230px; width:90%; margin:auto;">
					<table class="tblLista" style="width:100%;">
						<thead>
							<tr>
								<th width="20" style="text-align: center;">#</th>
								<th width="50">Impuesto</th>
								<th style="text-align:left">Descripci&oacute;n</th>
								<th width="50" style="text-align:right">Factor</th>
								<th width="100" style="text-align:right">Base Imponible</th>
								<th width="100" style="text-align:right">Monto Impuesto</th>
							</tr>
						</thead>
						
						<tbody id="lista_retencion">
							<?php
							$nro_retencion = 0;
							foreach ($field_retencion as $f)
							{
								$id = ++$nro_retencion;
								?>
								<tr class="trListaBody" onclick="clk($(this), 'retencion', 'retencion_<?=$id?>');" id="retencion_<?=$id?>">
									<th style="text-align: center;">
										<input type="hidden" name="retencion_Secuencia[]" id="retencion_Secuencia<?=$id?>" value="<?=$f['Secuencia']?>">
										<input type="hidden" name="retencion_CodImpuesto[]" value="<?=$f['CodImpuesto']?>">
										<?=$nro_retencion?>
										<input type="hidden" name="retencion_FlagImponible[]" value="<?=$f['FlagImponible']?>">
										<input type="hidden" name="retencion_Signo[]" value="<?=$f['Signo']?>">
									</th>
									<td align="center"><?=$f['CodImpuesto']?></td>
									<td><?=htmlentities($f['Descripcion'])?></td>
									<td>
										<input type="text" name="retencion_Factor[]" id="retencion_Factor<?=$id?>" value="<?=number_format($f['Factor'],2,',','.')?>" class="cell2 " style="text-align:right;" readonly>
									</td>
									<td>
										<input type="text" name="retencion_MontoAfecto[]" id="retencion_MontoAfecto<?=$id?>" value="<?=number_format($f['MontoAfecto'],2,',','.')?>" class="cell2 " style="text-align:right;" readonly>
									</td>
									<td>
										<input type="text" name="retencion_MontoImpuesto[]" id="retencion_MontoImpuesto<?=$id?>" value="<?=number_format($f['MontoImpuesto'],2,',','.')?>" class="cell2 " style="text-align:right;" readonly>
									</td>
								</tr>
								<?php
							}
							?>
						</tbody>
					</table>
				</div>
				<input type="hidden" id="nro_retencion" value="<?=$nro_retencion?>" />
				<input type="hidden" id="can_retencion" value="<?=$nro_retencion?>" />
				</center>
			</td>
			<td class="tagForm">Monto Afecto:</td>
			<td>
				<input type="text" name="MontoAfecto" id="MontoAfecto" value="<?=number_format($field['MontoAfecto'],2,',','.')?>" style="width:125px; text-align: right;" class="currency" onchange="setTotales();" <?=$disabled_ver?> />
			</td>
		</tr>
		<tr>
			<td class="tagForm">Monto No Afecto:</td>
			<td>
				<input type="text" name="MontoNoAfecto" id="MontoNoAfecto" value="<?=number_format($field['MontoNoAfecto'],2,',','.')?>" style="width:125px; text-align: right;" class="currency" onchange="setTotales();" <?=$disabled_ver?> />
			</td>
		</tr>
		<tr>
			<td class="tagForm">Impuesto a las Ventas:</td>
			<td>
				<input type="hidden" name="FactorPorcentaje" id="FactorPorcentaje" value="<?=$field['FactorPorcentaje']?>">
				<input type="text" name="MontoImpuestoVentas" id="MontoImpuestoVentas" value="<?=number_format($field['MontoImpuestoVentas'],2,',','.')?>" style="width:125px; text-align: right;" class="currency" onchange="setTotales(1);" <?=$disabled_ver?> />
			</td>
		</tr>
		<tr>
			<td class="tagForm">Otros Imptos./Retenciones:</td>
			<td>
				<input type="text" name="MontoRetenciones" id="MontoRetenciones" value="<?=number_format($field['MontoRetenciones'],2,',','.')?>" style="width:125px; text-align: right;" class="currency" readonly />
			</td>
		</tr>
		<tr>
			<td class="tagForm">Monto Total:</td>
			<td>
				<input type="text" name="MontoTotal" id="MontoTotal" value="<?=number_format($field['MontoTotal'],2,',','.')?>" style="width:125px; text-align: right;" class="currency" readonly />
			</td>
		</tr>
		<tr>
			<td class="tagForm">Saldo a Pagar:</td>
			<td>
				<input type="text" name="SaldoAdelanto" id="SaldoAdelanto" value="<?=number_format($field['SaldoAdelanto'],2,',','.')?>" style="width:125px; text-align: right;" class="currency" readonly />
			</td>
		</tr>
		<tr>
			<th style="text-align: right;">Información Contable</th>
			<th></th>
		</tr>
		<tr>
			<td class="tagForm">* C.Costo:</td>
			<td class="gallery clearfix">
				<input type="hidden" name="CodCentroCosto" id="CodCentroCosto" value="<?=$field['CodCentroCosto']?>">
				<input type="text" name="CentroCosto" id="CentroCosto" value="<?=$field['CentroCosto']?>" style="width:100px;" readonly />
				<a href="../lib/listas/gehen.php?anz=lista_centro_costos&campo1=CodCentroCosto&campo2=CentroCosto&ventana=codigo&filtrar=default&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe5]" style=" <?=$display_ver?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
		</tr>
		<tr>
			<th style="text-align: right;">Usuarios</th>
			<th></th>
		</tr>
		<tr>
			<td class="tagForm">Preparado Por:</td>
			<td>
				<input type="hidden" name="PreparadoPor" id="PreparadoPor" value="<?=$field['PreparadoPor']?>" />
				<input type="text" name="NomPreparadoPor" id="NomPreparadoPor" value="<?=$field['NomPreparadoPor']?>" style="width:140px;" readonly />
				<input type="text" name="FechaPreparado" id="FechaPreparado" value="<?=$field['FechaPreparado']?>" style="width:106px;" readonly />
			</td>
		</tr>
		<tr>
			<td class="tagForm">Aprobado Por:</td>
			<td>
				<input type="hidden" name="AprobadoPor" id="AprobadoPor" value="<?=$field['AprobadoPor']?>" />
				<input type="text" name="NomAprobadoPor" id="NomAprobadoPor" value="<?=$field['NomAprobadoPor']?>" style="width:140px;" readonly />
				<input type="text" name="FechaAprobado" id="FechaAprobado" value="<?=$field['FechaAprobado']?>" style="width:106px;" readonly />
			</td>
		</tr>
	    <tr>
			<td class="tagForm">&Uacute;ltima Modif.:</td>
			<td>
				<input type="text" value="<?=$field['UltimoUsuario']?>" style="width:101px;" disabled="disabled" />
				<input type="text" value="<?=$field['UltimaFecha']?>" style="width:145px" disabled="disabled" />
			</td>
		</tr>
	</table>

	<center>
		<input type="submit" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" id="btSubmit" />
		<input type="button" value="Cancelar" style="width:75px;" id="btCancelar" onclick="<?=$clkCancelar?>" />
	</center>
</form>
<div style="width:100%; min-width:<?=$_width?>px;" class="divMsj">Campos Obligatorios *</div>

<script type="text/javascript">
	function abrirListaImpuesto() {
		var MontoAfecto = setNumero($('#MontoAfecto').val());
		var MontoNoAfecto = setNumero($('#MontoNoAfecto').val());
		var MontoImpuesto = setNumero($('#MontoImpuestoVentas').val());
		var MontoTotal = setNumero($('#MontoTotal').val());
		var href = "../lib/listas/gehen.php?anz=lista_impuestos&filtrar=default&ventana=listado_insertar_linea_adelanto&detalle=retencion&modulo=ajax&accion=retencion_insertar&url=../../ap/ap_gastoadelanto_ajax.php&MontoAfecto="+MontoAfecto+"&MontoNoAfecto="+MontoNoAfecto+"&MontoImpuesto="+MontoImpuesto+"&iframe=true&width=100%&height=100%";
		$('#a_retencion').attr('href', href);
		$('#a_retencion').click();
	}
	function setFactorPorcentaje(CodTipoServicio) {
		$.post('ap_gastoadelanto_ajax.php', "modulo=ajax&accion=setFactorPorcentaje&CodTipoServicio="+CodTipoServicio, function(data) {
			$('#FactorPorcentaje').val(data['FactorPorcentaje']);
			setTotales();
	    }, 'json');

	}
	function setTotales(FlagImpuesto) {
		var FactorPorcentaje = $('#FactorPorcentaje').val();
		var MontoAfecto = setNumero($('#MontoAfecto').val());
		var MontoNoAfecto = setNumero($('#MontoNoAfecto').val());
		if (FlagImpuesto) var MontoImpuestoVentas = setNumero($('#MontoImpuestoVentas').val());
		else var MontoImpuestoVentas = MontoAfecto * FactorPorcentaje / 100;
		var MontoRetenciones = 0;
		//	retenciones
		$('input[name="retencion_Secuencia[]"]').each(function(idx) {
			var retencion_FlagImponible = $('input[name="retencion_FlagImponible[]"]:eq('+idx+')').val();
			var retencion_Signo = $('input[name="retencion_Signo[]"]:eq('+idx+')').val();
			var retencion_Factor = setNumero($('input[name="retencion_Factor[]"]:eq('+idx+')').val());
			var retencion_MontoAfecto = 0;
			//	
			if (retencion_FlagImponible == "I") retencion_MontoAfecto = MontoImpuestoVentas;
			else if (retencion_FlagImponible == "N") retencion_MontoAfecto = MontoAfecto;
			else if (retencion_FlagImponible == "N") retencion_MontoAfecto = MontoAfecto + MontoNoAfecto;
			else if (retencion_FlagImponible == "T") retencion_MontoAfecto = MontoAfecto + MontoImpuestoVentas;
			retencion_MontoImpuesto = retencion_MontoAfecto * retencion_Factor / 100;
			if (retencion_Signo == "N") retencion_MontoImpuesto = retencion_MontoImpuesto * -1;
			MontoRetenciones = MontoRetenciones + retencion_MontoImpuesto;

			$('input[name="retencion_MontoImpuesto[]"]:eq('+idx+')').val(retencion_MontoImpuesto).formatCurrency();
		});
		//	
		MontoRetenciones = MontoRetenciones * -1;
		var MontoTotal = MontoAfecto + MontoNoAfecto + MontoImpuestoVentas;
		var SaldoAdelanto = MontoTotal - MontoRetenciones;

		$('#MontoImpuestoVentas').val(MontoImpuestoVentas).formatCurrency();
		$('#MontoRetenciones').val(MontoRetenciones).formatCurrency();
		$('#MontoTotal').val(MontoTotal).formatCurrency();
		$('#SaldoAdelanto').val(SaldoAdelanto).formatCurrency();
	}
	function abrirListaCompromisos() {
		var CodOrganismo = $('#CodOrganismo').val();
		var CodProveedor = $('#CodProveedor').val();
		var NomProveedor = $('#NomProveedor').val();

		if (CodProveedor == '') cajaModal('Debe seleccionar un Proveedor');
		else {
			var href = "../lib/listas/gehen.php?anz=lista_compromisos&fCodOrganismo="+CodOrganismo+"&fCodProveedor="+CodProveedor+"&fNomProveedor="+NomProveedor+"&ventana=ap_gastoadelanto&fFlagProveedor=S&fFlagOrganismo=S&filtrar=default&iframe=true&width=100%&height=100%";

			$('#a_compromiso').attr('href', href);
			$('#a_compromiso').click();
		}
	}
</script>