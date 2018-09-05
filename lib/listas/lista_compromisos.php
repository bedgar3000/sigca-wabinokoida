<?php
if (empty($ventana)) $ventana = "selLista";
if (empty($fFlagProveedor)) $fFlagProveedor = "N";
if (empty($fFlagOrganismo)) $fFlagOrganismo = "N";
//	------------------------------------
if ($filtrar == "default") {
	$fEstado = 'AP';
	$fFechaOrdenD = formatFechaDMA($PeriodoActual.'-01');
	$fFechaOrdenH = formatFechaDMA($FechaActual);
}
//	------------------------------------
$filtro_oc = '';
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro_oc.=" AND (oc.CodOrganismo = '$fCodOrganismo')"; } else $dCodOrganismo = "disabled";
if ($fCodFormaPago != "") { $cCodFormaPago = "checked"; $filtro_oc.=" AND (oc.CodFormaPago = '$fCodFormaPago')"; } else $dCodFormaPago = "disabled";
if ($fCodProveedor != "") { $cCodProveedor = "checked"; $filtro_oc.=" AND (oc.CodProveedor = '$fCodProveedor')"; } else $dCodProveedor = "visibility:hidden;";
if ($fEstado != "") { $cEstado = "checked"; $filtro_oc.=" AND (oc.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fBuscar != "") { 
	$cBuscar = "checked"; 
	$filtro_oc.=" AND (oc.NroInterno LIKE '%$fBuscar%'
					OR oc.NomProveedor LIKE '%$fBuscar%'
					OR a1.Descripcion LIKE '%$fBuscar%'
					OR a2.Descripcion LIKE '%$fBuscar%'
					OR fp.Descripcion LIKE '%$fBuscar%'
					OR oc.Observaciones LIKE '%$fBuscar%'
					OR oc.Anio LIKE '%$fBuscar%')";
} else $dBuscar = "disabled";
if ($fFechaOrdenD != "" || $fFechaOrdenH != "") {
	$cFechaOrden = "checked";
	if ($fFechaOrdenD != "") $filtro_oc.=" AND (oc.FechaOrden >= '".formatFechaAMD($fFechaOrdenD)."')";
	if ($fFechaOrdenH != "") $filtro_oc.=" AND (oc.FechaOrden <= '".formatFechaAMD($fFechaOrdenH)."')";
} else $dFechaOrden = "disabled";
$filtro_os = '';
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro_os.=" AND (os.CodOrganismo = '$fCodOrganismo')"; } else $dCodOrganismo = "disabled";
if ($fCodFormaPago != "") { $cCodFormaPago = "checked"; $filtro_os.=" AND (os.CodFormaPago = '$fCodFormaPago')"; } else $dCodFormaPago = "disabled";
if ($fCodProveedor != "") { $cCodProveedor = "checked"; $filtro_os.=" AND (os.CodProveedor = '$fCodProveedor')"; } else $dCodProveedor = "visibility:hidden;";
if ($fEstado != "") { $cEstado = "checked"; $filtro_os.=" AND (os.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fBuscar != "") { 
	$cBuscar = "checked"; 
	$filtro_os.=" AND (os.NroInterno LIKE '%$fBuscar%'
					OR os.NomProveedor LIKE '%$fBuscar%'
					OR os.Descripcion LIKE '%$fBuscar%'
					OR os.DescAdicional LIKE '%$fBuscar%'
					OR os.MotRechazo LIKE '%$fBuscar%'
					OR os.Observaciones LIKE '%$fBuscar%'
					OR os.Anio LIKE '%$fBuscar%')";
} else $dBuscar = "disabled";
if ($fFechaOrdenD != "" || $fFechaOrdenH != "") {
	$cFechaOrden = "checked";
	if ($fFechaOrdenD != "") $filtro_os.=" AND (os.FechaPreparacion >= '".formatFechaAMD($fFechaOrdenD)."')";
	if ($fFechaOrdenH != "") $filtro_os.=" AND (os.FechaPreparacion <= '".formatFechaAMD($fFechaOrdenH)."')";
} else $dFechaOrden = "disabled";
//	------------------------------------
$_titulo = "Cotizaciones";
$_width = 900;
?>
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lista_compromisos" method="post">
	<input type="hidden" name="registro" id="registro" />
	<input type="hidden" name="campo1" id="campo1" value="<?=$campo1?>" />
	<input type="hidden" name="campo2" id="campo2" value="<?=$campo2?>" />
	<input type="hidden" name="campo3" id="campo3" value="<?=$campo3?>" />
	<input type="hidden" name="campo4" id="campo4" value="<?=$campo4?>" />
	<input type="hidden" name="campo5" id="campo5" value="<?=$campo5?>" />
	<input type="hidden" name="campo6" id="campo6" value="<?=$campo6?>" />
	<input type="hidden" name="campo7" id="campo7" value="<?=$campo7?>" />
	<input type="hidden" name="campo8" id="campo8" value="<?=$campo8?>" />
	<input type="hidden" name="campo9" id="campo9" value="<?=$campo9?>" />
	<input type="hidden" name="campo10" id="campo10" value="<?=$campo10?>" />
	<input type="hidden" name="ventana" id="ventana" value="<?=$ventana?>" />
	<input type="hidden" name="detalle" id="detalle" value="<?=$detalle?>" />
	<input type="hidden" name="modulo" id="modulo" value="<?=$modulo?>" />
	<input type="hidden" name="accion" id="accion" value="<?=$accion?>" />
	<input type="hidden" name="url" id="url" value="<?=$url?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
	<input type="hidden" name="fFlagProveedor" id="fFlagProveedor" value="<?=$fFlagProveedor?>" />
	<input type="hidden" name="fFlagOrganismo" id="fFlagOrganismo" value="<?=$fFlagOrganismo?>" />

	<!--FILTRO-->
	<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
		<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
			<tr>
				<td align="right">Organismo:</td>
				<td>
					<input type="checkbox" <?=$cCodOrganismo?> onclick="this.checked=!this.checked;" />
					<?php if ($fFlagOrganismo != 'S') { ?>
						<select name="fCodOrganismo" id="fCodOrganismo" style="width:275px;" <?=$dCodOrganismo?>>
							<?=getOrganismos($fCodOrganismo, 3);?>
						</select>
					<?php } else { ?>
						<select name="fCodOrganismo" id="fCodOrganismo" style="width:275px;" <?=$dCodOrganismo?>>
							<?=loadSelect2('mastorganismos','CodOrganismo','Organismo',$fCodOrganismo,1);?>
						</select>
					<?php } ?>
				</td>
				<td align="right">Forma de Pago:</td>
				<td>
					<input type="checkbox" <?=$cCodFormaPago?> onclick="chkFiltro(this.checked, 'fCodFormaPago');" />
					<select name="fCodFormaPago" id="fCodFormaPago" style="width:142px;" <?=$dCodFormaPago?>>
						<option value="">&nbsp;</option>
						<?=loadSelect2('mastformapago','CodFormaPago','Descripcion',$fCodFormaPago)?>
					</select>
				</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td align="right">Proveedor:</td>
				<td class="gallery clearfix">
					<?php if ($fFlagProveedor != 'S') { ?>
						<input type="checkbox" <?=$cCodProveedor?> onclick="ckLista(this.checked, ['fCodProveedor','fNomProveedor'], ['aCodProveedor']);" />
						<input type="hidden" name="fCodProveedor" id="fCodProveedor" value="<?=$fCodProveedor?>" />
						<input type="text" name="fNomProveedor" id="fNomProveedor" value="<?=$fNomProveedor?>" style="width:275px;" readonly />
			            <a href="javascript:" onclick="window.open('gehen.php?anz=lista_personas&campo1=fCodProveedor&campo2=fNomProveedor&ventana=selListaOpener&filtrar=default&FlagClasePersona=S&fEsProveedor=S','lista_at_linea','width=950, height=430, toolbar=no, menubar=no, location=no, scrollbars=yes, left=0, top=0, resizable=no')" style=" <?=$dCodProveedor?>" id="aCodProveedor">
			            	<img src="../../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
			            </a>
					<?php } else { ?>
						<input type="checkbox" <?=$cCodProveedor?> onclick="this.checked=!this.checked;" />
						<input type="hidden" name="fCodProveedor" id="fCodProveedor" value="<?=$fCodProveedor?>" />
						<input type="text" name="fNomProveedor" id="fNomProveedor" value="<?=$fNomProveedor?>" style="width:275px;" readonly />
					<?php } ?>
				</td>
				<td align="right">Estado: </td>
				<td>
		            <input type="checkbox" <?=$cEstado?> onclick="this.checked=!this.checked;" />
		            <select name="fEstado" id="fEstado" style="width:142px;" <?=$dEstado?>>
		            	<option value="">&nbsp;</option>
		                <?=loadSelectGeneral("ESTADO-COMPRA", $fEstado)?>
		            </select>
				</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td align="right">Buscar:</td>
				<td>
					<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
					<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:100px;" <?=$dBuscar?> />
				</td>
				<td align="right">Fecha:</td>
				<td>
					<input type="checkbox" <?=$cFechaOrden?> onclick="chkCampos2(this.checked, ['fFechaOrdenD','fFechaOrdenH']);" />
					<input type="text" name="fFechaOrdenD" id="fFechaOrdenD" value="<?=$fFechaOrdenD?>" <?=$dFechaOrden?> maxlength="10" style="width:65px;" class="datepicker" onkeyup="setFechaDMA(this);" /> -
		            <input type="text" name="fFechaOrdenH" id="fFechaOrdenH" value="<?=$fFechaOrdenH?>" <?=$dFechaOrden?> maxlength="10" style="width:65px;" class="datepicker" onkeyup="setFechaDMA(this);" />
		        </td>
		        <td align="right"><input type="submit" value="Buscar"></td>
			</tr>
		</table>
	</div>
	<div class="sep"></div>

	<!--REGISTROS-->
    <table style="width:100%; min-width:<?=$_width?>px;">
        <tr>
            <td>
                <div class="header">
                    <ul id="tab">
                        <!-- CSS Tabs -->
                        <li id="li1" onclick="currentTab('tab', this);" class="current">
                            <a href="#" onclick="mostrarTab('tab', 1, 2);">
                                Compras
                            </a>
                        </li>
                        <li id="li2" onclick="currentTab('tab', this);">
                            <a href="#" onclick="mostrarTab('tab', 2, 2);">
                                Servicios
                            </a>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
    </table>

    <div id="tab1" style="display:block;">
		<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px;">
			<table class="tblLista" style="width:100%; min-width:2300px;">
				<thead>
				    <tr>
						<th width="30">A&ntilde;o</th>
						<th width="75">Nro. Orden</th>
						<th width="75">Fecha Orden</th>
						<th width="100">Estado</th>
						<th width="400" align="left">Proveedor</th>
						<th width="100" align="right">Monto</th>
						<th width="100" align="right">Monto Pagado</th>
						<th width="100" align="right">Monto Pendiente</th>
						<th width="175">Almacen</th>
						<th width="175">Almacen Ingreso</th>
						<th width="100">Forma de Pago</th>
						<th width="75">Fecha Anulaci&oacute;n</th>
						<th align="left">Observaciones</th>
				    </tr>
			    </thead>
			    
			    <tbody>
				<?php
				//	consulto lista
				$sql = "SELECT
							oc.Anio,
							oc.CodOrganismo,
							oc.NroOrden,
							oc.NroInterno,
							oc.FechaOrden,
							oc.FechaOrden,
							oc.CodProveedor,
							oc.NomProveedor,
							oc.MontoTotal,
							oc.MontoPendiente,
							oc.MontoPagado,
							oc.Observaciones,
							oc.Estado,
							oc.FechaAnulacion,
							oc.MontoAfecto,
							oc.MontoNoAfecto,
							oc.MontoIGV,
							oc.MontoTotal,
							oc.CodTipoServicio,
							a1.Descripcion AS NomAlmacen,
							a2.Descripcion AS NomAlmacenIngreso,
							fp.Descripcion AS NomFormaPago,
							i.FactorPorcentaje
						FROM lg_ordencompra oc
						INNER JOIN lg_almacenmast a1 ON oc.CodAlmacen = a1.Codalmacen
						LEFT JOIN lg_almacenmast a2 ON oc.CodAlmacenIngreso = a2.Codalmacen
						LEFT JOIN mastformapago fp ON oc.CodFormaPago = fp.CodFormaPago
						LEFT JOIN masttiposervicio ts ON ts.CodTipoServicio = oc.CodTipoServicio
						LEFT JOIN masttiposervicioimpuesto tsi ON tsi.CodTipoServicio = ts.CodTipoServicio
						LEFT JOIN mastimpuestos i ON (
							i.CodImpuesto = tsi.CodImpuesto
							AND i.CodRegimenFiscal = 'I'
						)
						WHERE 1 $filtro_oc
						ORDER BY Anio, CodOrganismo, NroInterno";
				$field = getRecords($sql);
				$rows_lista = count($field);
				foreach($field as $f) {
					$id = 'OC_'.$f['Anio'].'_'.$f['CodOrganismo'].'_'.$f['NroOrden'];
					if ($ventana == 'listado_insertar_linea') {
						?>
			            <tr class="trListaBody" onClick="listado_insertar_linea('<?=$detalle?>','modulo=<?=$modulo?>&accion=<?=$accion?>&NroOrden=<?=$f['NroOrden']?>','<?=$f['NroOrden']?>','<?=$url?>');">
			            <?php
					}
					elseif($ventana == "compromisos") {
						?>
			            <tr class="trListaBody" onClick="selLista(['<?=$f['Anio']?>','<?=$f['CodOrganismo']?>','<?=$f['NroOrden']?>','OC','<?=$f['NroInterno']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>','<?=$campo4?>','<?=$campo5?>']);">
			            <?php
					}
					elseif($ventana == "ap_gastoadelanto") {
						?>
			            <tr class="trListaBody" onClick="selLista(
			            	[
				            	'<?=$f['Anio']?>',
				            	'<?=$f['CodOrganismo']?>',
				            	'<?=$f['NroOrden']?>',
				            	'OC',
				            	'<?=$f['NroInterno']?>',
				            	'<?=$f['CodTipoServicio']?>',
				            	'<?=number_format($f['MontoAfecto'],2,',','.')?>',
				            	'<?=number_format($f['MontoNoAfecto'],2,',','.')?>',
				            	'<?=number_format($f['FactorPorcentaje'],2,',','.')?>',
				            	'<?=number_format($f['MontoIGV'],2,',','.')?>',
				            	'0,00',
				            	'<?=number_format($f['MontoTotal'],2,',','.')?>',
				            	'<?=number_format($f['MontoTotal'],2,',','.')?>',
				            ],
			            	[
				            	'Anio',
				            	'CodOrganismo',
				            	'NroOrden',
				            	'TipoCompromiso',
				            	'NroCompromiso',
				            	'CodTipoServicio',
				            	'MontoAfecto',
				            	'MontoNoAfecto',
				            	'FactorPorcentaje',
				            	'MontoImpuestoVentas',
				            	'MontoRetenciones',
				            	'MontoTotal',
				            	'SaldoAdelanto',
			            	]);">
			            <?php
					}
					else {
						?>
			            <tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['Anio']?>','<?=$f['CodOrganismo']?>','<?=$f['NroOrden']?>','OC','<?=$f['NroInterno']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>','<?=$campo4?>','<?=$campo5?>']);">
			            <?php
					}
					?>
						<td align="center"><?=$f['Anio']?></td>
						<td align="center"><?=$f['NroInterno']?></td>
						<td align="center"><?=formatFechaDMA($f['FechaOrden'])?></td>
						<td align="center"><?=printValoresGeneral("ESTADO-COMPRA", $f['Estado'])?></td>
						<td><?=htmlentities($f['NomProveedor'])?></td>
						<td align="right"><strong><?=number_format($f['MontoTotal'], 2, ',', '.')?></strong></td>
						<td align="right"><?=number_format($f['MontoPagado'], 2, ',', '.')?></td>
						<td align="right"><?=number_format($f['MontoPendiente'], 2, ',', '.')?></td>
						<td><?=htmlentities($f['NomAlmacen'])?></td>
						<td><?=htmlentities($f['NomAlmacenIngreso'])?></td>
						<td align="center"><?=htmlentities($f['NomFormaPago'])?></td>
						<td align="center"><?=formatFechaDMA($f['FechaAnulacion'])?></td>
						<td><?=htmlentities(substr($f['Observaciones'],0,150))?></td>
					</tr>
					<?php
				}
				?>
			    </tbody>
			</table>
		</div>
	</div>

    <div id="tab2" style="display:none;">
		<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px;">
			<table class="tblLista" style="width:100%; min-width:3100px;">
				<thead>
				    <tr>
						<th width="30">Año</th>
						<th width="75">Nro. Orden</th>
						<th width="75">Fecha Orden</th>
						<th width="100">Estado</th>
						<th width="350" align="left">Proveedor</th>
						<th width="100" align="right">Monto</th>
						<th width="600" align="left">Descripción</th>
						<th width="75">Fecha Anulación</th>
						<th width="600" align="left">Descripción Adicional</th>
						<th width="400" align="left">Razón Rechazo</th>
						<th align="left">Observaciones</th>
				    </tr>
			    </thead>
			    
			    <tbody>
				<?php
				//	consulto todos
				$sql = "SELECT *
						FROM lg_ordenservicio os
						WHERE 1 $filtro_os";
				$rows_total = getNumRows3($sql);
				//	consulto lista
				$sql = "SELECT
							os.Anio,
							os.CodOrganismo,
							os.NroOrden,
							os.Descripcion,
							os.DescAdicional,
							os.MotRechazo,
							os.Observaciones,
							os.FechaPreparacion,
							os.FechaDocumento,
							os.NomProveedor,
							os.TotalMontoIva,
							os.Estado,
							os.NroInterno,
							os.FechaAnulacion,
							i.FactorPorcentaje
						FROM lg_ordenservicio os
						LEFT JOIN masttiposervicio ts ON ts.CodTipoServicio = os.CodTipoServicio
						LEFT JOIN masttiposervicioimpuesto tsi ON tsi.CodTipoServicio = ts.CodTipoServicio
						LEFT JOIN mastimpuestos i ON (
							i.CodImpuesto = tsi.CodImpuesto
							AND i.CodRegimenFiscal = 'I'
						)
						WHERE 1 $filtro_os
						ORDER BY Anio, CodOrganismo, NroInterno";
				$field = getRecords($sql);
				$rows_lista = count($field);
				foreach($field as $f) {
					$id = 'OS_'.$f['Anio'].'_'.$f['CodOrganismo'].'_'.$f['NroOrden'];
					if ($ventana == 'listado_insertar_linea') {
						?>
			            <tr class="trListaBody" onClick="listado_insertar_linea('<?=$detalle?>','modulo=<?=$modulo?>&accion=<?=$accion?>&NroOrden=<?=$f['NroOrden']?>','<?=$f['NroOrden']?>','<?=$url?>');">
			            <?php
					}
					elseif($ventana == "compromisos") {
						?>
			            <tr class="trListaBody" onClick="selLista(['<?=$f['Anio']?>','<?=$f['CodOrganismo']?>','<?=$f['NroOrden']?>','OC','<?=$f['NroInterno']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>','<?=$campo4?>','<?=$campo5?>']);">
			            <?php
					}
					elseif($ventana == "ap_gastoadelanto") {
						?>
			            <tr class="trListaBody" onClick="selLista(
			            	[
				            	'<?=$f['Anio']?>',
				            	'<?=$f['CodOrganismo']?>',
				            	'<?=$f['NroOrden']?>',
				            	'OS',
				            	'<?=$f['NroInterno']?>',
				            	'<?=$f['CodTipoServicio']?>',
				            	'<?=number_format($f['MontoOriginal'],2,',','.')?>',
				            	'<?=number_format($f['MontoNoAfecto'],2,',','.')?>',
				            	'<?=number_format($f['FactorPorcentaje'],2,',','.')?>',
				            	'<?=number_format($f['MontoIva'],2,',','.')?>',
				            	'0,00',
				            	'<?=number_format($f['TotalMontoIva'],2,',','.')?>',
				            	'<?=number_format($f['TotalMontoIva'],2,',','.')?>',
				            	'<?=$f['CodTipoPago']?>',
				            ],
			            	[
				            	'Anio',
				            	'CodOrganismo',
				            	'NroOrden',
				            	'TipoCompromiso',
				            	'NroCompromiso',
				            	'CodTipoServicio',
				            	'MontoAfecto',
				            	'MontoNoAfecto',
				            	'FactorPorcentaje',
				            	'MontoImpuestoVentas',
				            	'MontoRetenciones',
				            	'MontoTotal',
				            	'SaldoAdelanto',
				            	'CodTipoPago',
			            	]);">
			            <?php
					}
					else {
						?>
			            <tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['Anio']?>','<?=$f['CodOrganismo']?>','<?=$f['NroOrden']?>','OC','<?=$f['NroInterno']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>','<?=$campo4?>','<?=$campo5?>']);">
			            <?php
					}
					?>
						<td align="center"><?=$f['Anio']?></td>
						<td align="center"><?=$f['NroInterno']?></td>
						<td align="center"><?=formatFechaDMA($f['FechaPreparacion'])?></td>
						<td align="center"><?=printValoresGeneral("ESTADO-SERVICIO", $f['Estado'])?></td>
						<td><?=htmlentities($f['NomProveedor'])?></td>
						<td align="right"><strong><?=number_format($f['TotalMontoIva'], 2, ',', '.')?></strong></td>
						<td><?=htmlentities($f['Descripcion'])?></td>
						<td align="center"><?=formatFechaDMA($f['FechaAnulacion'])?></td>
						<td><?=htmlentities(substr($f['DescAdicional'],0,150))?></td>
						<td><?=htmlentities(substr($f['MotRechazo'],0,150))?></td>
						<td><?=htmlentities(substr($f['Observaciones'],0,150))?></td>
					</tr>
					<?php
				}
				?>
			    </tbody>
			</table>
		</div>
	</div>
</form>