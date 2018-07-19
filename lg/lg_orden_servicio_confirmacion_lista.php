<?php
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["ORGANISMO_ACTUAL"];
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (os.NroInterno LIKE '%".$fBuscar."%' OR
					  os.NomProveedor LIKE '%".$fBuscar."%' OR
					  os.Descripcion LIKE '%".$fBuscar."%' OR
					  cc.Codigo LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (os.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fCodDependencia != "") { $cCodDependencia = "checked"; $filtro.=" AND (os.CodDependencia = '".$fCodDependencia."')"; } else $dCodDependencia = "disabled";
if ($fCodCentroCosto != "") { $cCodCentroCosto = "checked"; $filtro.=" AND (os.CodCentroCosto = '".$fCodCentroCosto."')"; } else $dCodCentroCosto = "disabled";
if ($fCodProveedor != "") { $cCodProveedor = "checked"; $filtro.=" AND (os.CodProveedor = '".$fCodProveedor."')"; } else $dCodProveedor = "visibility:hidden;";
if ($fFechaPreparacionD != "" || $fFechaPreparacionH != "") {
	$cFechaPreparacion = "checked";
	if ($fFechaPreparacionD != "") $filtro.=" AND (os.FechaPreparacion >= '".$fFechaPreparacionD."')";
	if ($fFechaPreparacionH != "") $filtro.=" AND (os.FechaPreparacion <= '".$fFechaPreparacionH."')";
} else $dFechaPreparacion = "disabled";
//	------------------------------------
$_titulo = "Confirmar Realizaci&oacute;n de Servicios";
$_width = 900;
##	
$i=0;
$display_tab[0] = "display:none;";
$display_tab[1] = "display:none;";
$display_tab[2] = "display:none;";
$display_tab[3] = "display:none;";
foreach($display_tab as $_tab) {
	if ($id_tab == $i) { $display_tab[$i] = "display:block;"; $current_tab[$i] = "current"; }
	else { $display_tab[$i] = "display:none;"; $current_tab[$i] = ""; }
	++$i;
}
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lg_orden_servicio_confirmacion_lista" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="id_tab" id="id_tab" value="<?=$id_tab?>" />

<!--FILTRO-->
<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
	<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
		<tr>
    		<td align="right" width="100">Organismo:</td>
    		<td>
    			<input type="checkbox" checked="checked" onclick="this.checked=!this.checked" />
    			<select name="fCodOrganismo" id="fCodOrganismo" style="width:300px;" onChange="loadSelect($('#fCodDependencia'), 'tabla=dependencia&opcion='+this.value, 1, ['fCodCentroCosto']);">
    				<?=getOrganismos($fCodOrganismo, 3)?>
    			</select>
    		</td>
			<td align="right" width="100">Buscar:</td>
			<td>
				<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
				<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:300px;" <?=$dBuscar?> />
			</td>
	        <td>&nbsp;</td>
		</tr>
		<tr>
			<td align="right">Dependencia:</td>
			<td>
				<input type="checkbox" <?=$cCodDependencia?> onclick="chkFiltro(this.checked, 'fCodDependencia')" />
				<select name="fCodDependencia" id="fCodDependencia" style="width:300px;" onChange="loadSelect($('#fCodCentroCosto'), 'tabla=centro_costo&opcion='+this.value, 1);" <?=$dCodDependencia?>>
					<option value="">&nbsp;</option>
					<?=getDependencias($fCodDependencia, $fCodOrganismo, 3)?>
				</select>
			</td>
			<td class="tagForm">Proveedor:</td>
			<td class="gallery clearfix">
				<input type="checkbox" id="cCodProveedor" <?=$cCodProveedor?> onclick="ckLista(this.checked, ['fCodProveedor','fProveedor'], ['bCodProveedor']);" />
	        	<input type="text" name="fCodProveedor" id="fCodProveedor" value="<?=$fCodProveedor?>" style="width:40px;" readonly />
	        	<input type="text" name="fProveedor" id="fProveedor" value="<?=$fProveedor?>" style="width:257px;" readonly />
	            <a href="../lib/listas/gehen.php?anz=lista_personas&filtrar=default&campo1=fCodProveedor&campo2=fProveedor&ventana=selLista&iframe=true&width=950&height=430" rel="prettyPhoto[iframe1]" id="bCodProveedor" style=" <?=$dCodProveedor?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
	        <td>&nbsp;</td>
		</tr>
		<tr>
			<td align="right">Centro de Costo:</td>
			<td>
				<input type="checkbox" <?=$cCodCentroCosto?> onclick="chkFiltro(this.checked, 'fCodCentroCosto')" />
				<select name="fCodCentroCosto" id="fCodCentroCosto" style="width:300px;" <?=$dCodCentroCosto?>>
					<option value="">&nbsp;</option>
					<?=loadSelect2("ac_mastcentrocosto","CodCentroCosto","Descripcion",0,['CodDependencia'],[$CodDependencia])?>
				</select>
			</td>
			<td align="right">Fecha: </td>
			<td>
				<input type="checkbox" <?=$cFechaPreparacion?> onclick="chkCampos2(this.checked, ['fFechaPreparacionD','fFechaPreparacionH']);" />
				<input type="text" name="fFechaPreparacionD" id="fFechaPreparacionD" value="<?=$fFechaPreparacionD?>" style="width:65px;" maxlength="10" class="datepicker" <?=$dFechaPreparacion?> /> -
				<input type="text" name="fFechaPreparacionH" id="fFechaPreparacionH" value="<?=$fFechaPreparacionH?>" style="width:65px;" maxlength="10" class="datepicker" <?=$dFechaPreparacion?> />
			</td>
	        <td align="right"><input type="submit" value="Buscar"></td>
		</tr>
	</table>
</div>
<div class="sep"></div>

<table style="width:100%; min-width:<?=$_width?>px;" align="center" cellpadding="0" cellspacing="0">
	<tr>
    	<td>
            <div class="header" style="width:100%; min-width:<?=$_width?>px;">
	            <ul id="tab">
		            <!-- CSS Tabs -->
		            <li id="li1" class="<?=$current_tab[0]?>" onclick="currentTab('tab', this); $('#id_tab').val('0');">
		            	<a href="#" onclick="mostrarTab('tab', 1, 2);">Ordenes Pendientes por Confirmar</a>
		            </li>
		            <li id="li2" class="<?=$current_tab[1]?>" onclick="currentTab('tab', this); $('#id_tab').val('1');">
		            	<a href="#" onclick="mostrarTab('tab', 2, 2);">Confirmaciones ya Realizadas</a>
		            </li>
	            </ul>
            </div>
        </td>
    </tr>
</table>

<!--REGISTROS-->
<div id="tab1" style=" <?=$display_tab[0]?>;">
	<table class="tblBotones" style="width:100%; min-width:<?=$_width?>px;">
	    <tr>
	        <td><div id="rows"></div></td>
	        <td align="right">
	            <input type="button" value="Confirmar Servicio" style="width:100px;" class="update" onclick="loadOpcion('gehen.php?anz=lg_orden_servicio_confirmacion_form', 'confirmar', 1);" />
	        </td>
	    </tr>
	</table>
	<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px;">
		<table class="tblLista" style="width:100%; min-width:1500px;">
			<thead>
			    <tr>
			        <th width="30">#</th>
			        <th width="80">Fecha</th>
			        <th width="70">Commodity</th>
			        <th align="left">Descripci&oacute;n</th>
			        <th width="50">C.C.</th>
			        <th width="50">Cantidad</th>
			        <th width="50">Cant. Recibida</th>
			        <th width="50">Cant. Pendiente</th>
			        <th width="100">Precio Unitario</th>
			        <th width="100">Monto Afecto</th>
			        <th width="100">Monto No Afecto</th>
			        <th width="100">Monto Impuesto</th>
			        <th width="100">Total</th>
			    </tr>
		    </thead>
		    
		    <tbody id="lista_confirmar">
			<?php
			$Grupo = '';
			//	consulto lista
			$sql = "SELECT
						osd.*,
						(osd.CantidadPedida - osd.CantidadRecibida) AS CantidadPendiente,
						os.CodProveedor,
						os.NomProveedor,
						os.FechaPreparacion,
						os.NroInterno,
						os.MontoOriginal,
						os.MontoIva,
						cc.Codigo AS NomCentroCosto
					FROM
						lg_ordenserviciodetalle osd
						INNER JOIN lg_ordenservicio os ON (os.Anio = osd.Anio
														   AND os.CodOrganismo = osd.CodOrganismo
														   AND os.NroOrden = osd.NroOrden)
						INNER JOIN ac_mastcentrocosto cc ON (cc.CodCentroCosto = osd.CodCentroCosto)
					WHERE
						osd.FlagTerminado <> 'S'
						AND os.Estado = 'AP'
						$filtro
					ORDER BY Anio, CodOrganismo, NroOrden, Secuencia";
			$field_confirmar = getRecords($sql);
			foreach($field_confirmar as $f) {
				$id = $f['Anio'].'_'.$f['CodOrganismo'].'_'.$f['NroOrden'].'_'.$f['Secuencia'];
				$Orden = $f['Anio'].'_'.$f['CodOrganismo'].'_'.$f['NroOrden'];
				##	
				if ($f['FlagExonerado'] == 'S') {
					$MontoAfecto = 0.00;
					$MontoNoAfecto = $f['CantidadPendiente'] * $f['PrecioUnit'];
				} else {
					$MontoNoAfecto = 0.00;
					$MontoAfecto = $f['CantidadPendiente'] * $f['PrecioUnit'];
				}
				$MontoImpuesto = $MontoAfecto * $f['MontoIva'] / $f['MontoOriginal'];
				$Total = $MontoAfecto + $MontoNoAfecto + $MontoImpuesto;
				##	
				if ($Grupo != $Orden) {
					$Grupo = $Orden;
					?>
					<tr class="trListaBody2">
						<td align="center">O/S</td>
						<td align="center"><?=$f['NroInterno']?></td>
	                	<td colspan="10"><?=htmlentities($f['NomProveedor'])?></td>
					</tr>
					<?php
				}
				?>
	            <tr class="trListaBody" onclick="clkMulti($(this), 'confirmar<?=$id?>');">
	                <td align="center">
	                    <input type="checkbox" name="confirmar[]" id="confirmar<?=$id?>" value="<?=$id?>" style="display:none;" />
	                    <?=$f['Secuencia']?>
	                </td>
	                <td align="center"><?=formatFechaDMA($f['FechaPreparacion'])?></td>
	                <td align="center"><?=$f['CommoditySub']?></td>
	                <td><?=htmlentities($f['Descripcion'])?></td>
	                <td align="center"><?=$f['NomCentroCosto']?></td>
	                <td align="right"><?=number_format($f['CantidadPedida'], 2, ',', '.')?></td>
	                <td align="right"><?=number_format($f['CantidadRecibida'], 2, ',', '.')?></td>
	                <td align="right"><?=number_format($f['CantidadPendiente'], 2, ',', '.')?></td>
	                <td align="right"><?=number_format($f['PrecioUnit'], 2, ',', '.')?></td>
	                <td align="right"><?=number_format($MontoAfecto, 2, ',', '.')?></td>
	                <td align="right"><?=number_format($MontoNoAfecto, 2, ',', '.')?></td>
	                <td align="right"><?=number_format($MontoImpuesto, 2, ',', '.')?></td>
	                <td align="right"><?=number_format($Total, 2, ',', '.')?></td>
	            </tr>
				<?php
			}
			?>
		    </tbody>
		</table>
	</div>
</div>

<div id="tab2" style=" <?=$display_tab[1]?>;">
	<table class="tblBotones" style="width:100%; min-width:<?=$_width?>px;">
	    <tr>
	        <td><div id="rows"></div></td>
	        <td align="right" class="gallery clearfix">
	        	<input type="button" value="Desconfirmar Servicio" style="width:125px;" class="update" onclick="executeOpcion('lg_orden_servicio_ajax.php', 'modulo=orden_servicio&accion=desconfirmar', 'confirmadas', 1);" />

	        	<a href="pagina.php?iframe=true" rel="prettyPhoto[iframe2]" style="display:none;" id="a_imprimir"></a>
	            <input type="button" id="btImprimir" value="Imprimir Acta" style="width:90px;" onclick="loadPopup('lg_orden_servicio_confirmar_pdf.php?', $('#a_imprimir'), 'confirmadas');" />
	        </td>
	    </tr>
	</table>
	<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px;">
		<table class="tblLista" style="width:100%; min-width:2000px;">
			<thead>
			    <tr>
			        <th width="30">#</th>
			        <th width="80">Fecha</th>
			        <th width="70">Commodity</th>
			        <th align="left">Descripci&oacute;n</th>
			        <th width="50">C.C.</th>
			        <th width="50">Cantidad</th>
			        <th width="100">Precio Unitario</th>
			        <th width="100">Monto Afecto</th>
			        <th width="100">Monto No Afecto</th>
			        <th width="100">Monto Impuesto</th>
			        <th width="100">Total a Pagar</th>
			        <th width="75">Estado</th>
			        <th width="125">Obligaci&oacute;n</th>
			        <th width="300">Confirmado Por</th>
			    </tr>
		    </thead>
		    
		    <tbody id="lista_confirmadas">
			<?php
			$Grupo = '';
			//	consulto lista
			$sql = "SELECT
						dd.*,
						d.ObligacionTipoDocumento,
						d.Estado,
						o.NroControl,
						os.CodOrganismo,
						os.NroOrden,
						os.NomProveedor,
						os.NroInterno,
						os.FechaPreparacion,
						os.MontoOriginal,
						os.MontoIva,
						cc.Codigo AS NomCentroCosto,
						p.NomCompleto AS NomConfirmadoPor
					FROM
						ap_documentosdetalle dd
						INNER JOIN ap_documentos d ON (d.CodProveedor = dd.CodProveedor
													   AND d.DocumentoClasificacion = dd.DocumentoClasificacion
													   AND d.DocumentoReferencia = dd.DocumentoReferencia
													   AND d.Anio = dd.Anio)
						INNER JOIN lg_ordenserviciodetalle osd ON (d.CodOrganismo = osd.CodOrganismo
																   AND d.ReferenciaNroDocumento = osd.NroOrden
																   AND dd.Secuencia = osd.Secuencia
																   AND dd.Anio = osd.Anio)
						INNER JOIN lg_ordenservicio os ON (os.Anio = osd.Anio
														   AND os.CodOrganismo = osd.CodOrganismo
														   AND os.NroOrden = osd.NroOrden)
						INNER JOIN ac_mastcentrocosto cc ON (cc.CodCentroCosto = osd.CodCentroCosto)
						LEFT JOIN ap_obligaciones o ON (o.CodProveedor = d.CodProveedor
														 AND o.CodTipoDocumento = d.ObligacionTipoDocumento
														 AND o.NroDocumento = d.ObligacionNroDocumento)
						LEFT JOIN mastpersonas p ON (p.CodPersona = osd.ConfirmadoPor)
					WHERE d.DocumentoClasificacion = '$_PARAMETRO[DOCREFOS]' $filtro
					ORDER BY Anio, CodOrganismo, NroOrden, CodProveedor, DocumentoClasificacion, DocumentoReferencia, Secuencia";
			$field_confirmadas = getRecords($sql);
			foreach($field_confirmadas as $f) {
				$id = $f['Anio'].'_'.$f['CodOrganismo'].'_'.$f['NroOrden'].'_'.$f['ReferenciaSecuencia'].'_'.$f['CodProveedor'].'_'.$f['DocumentoClasificacion'].'_'.$f['DocumentoReferencia'];
				$Orden = $f['Anio'].'_'.$f['CodOrganismo'].'_'.$f['NroOrden'];
				##	
				if ($f['FlagExonerado'] == 'S') {
					$MontoAfecto = 0.00;
					$MontoNoAfecto = $f['Cantidad'] * $f['PrecioUnit'];
				} else {
					$MontoNoAfecto = 0.00;
					$MontoAfecto = $f['Cantidad'] * $f['PrecioUnit'];
				}
				$MontoImpuesto = $MontoAfecto * $f['MontoIva'] / $f['MontoOriginal'];
				$Total = $MontoAfecto + $MontoNoAfecto + $MontoImpuesto;
				##	
				if ($Grupo != $Orden) {
					$Grupo = $Orden;
					?>
					<tr class="trListaBody2">
						<td align="center">O/S</td>
						<td align="center"><?=$f['NroInterno']?></td>
	                	<td colspan="12"><?=htmlentities($f['NomProveedor'])?></td>
					</tr>
					<?php
				}
				?>
	            <tr class="trListaBody" onclick="clkMulti($(this), 'confirmadas<?=$id?>');">
	                <td align="center">
	                    <input type="checkbox" name="confirmadas[]" id="confirmadas<?=$id?>" value="<?=$id?>" style="display:none;" />
	                    <?=$f['Secuencia']?>
	                </td>
	                <td align="center"><?=formatFechaDMA($f['FechaPreparacion'])?></td>
	                <td align="center"><?=$f['CommoditySub']?></td>
	                <td><?=htmlentities($f['Descripcion'])?></td>
	                <td align="center"><?=$f['NomCentroCosto']?></td>
	                <td align="right"><?=number_format($f['Cantidad'], 2, ',', '.')?></td>
	                <td align="right"><?=number_format($f['PrecioUnit'], 2, ',', '.')?></td>
	                <td align="right"><?=number_format($MontoAfecto, 2, ',', '.')?></td>
	                <td align="right"><?=number_format($MontoNoAfecto, 2, ',', '.')?></td>
	                <td align="right"><?=number_format($MontoImpuesto, 2, ',', '.')?></td>
	                <td align="right"><?=number_format($f['Total'], 2, ',', '.')?></td>
					<td align="center"><?=printValoresGeneral("ESTADO-DOCUMENTOS", $f['Estado'])?></td>
	                <td align="center"><?=$f['ObligacionTipoDocumento'].'-'.$f['NroControl']?></td>
	                <td><?=htmlentities($f['NomConfirmadoPor'])?></td>
	            </tr>
				<?php
			}
			?>
		    </tbody>
		</table>
	</div>
</div>
</form>

<script type="text/javascript">
</script>