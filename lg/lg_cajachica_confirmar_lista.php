<?php
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$tb = 1;
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (r.CodInterno LIKE '%".$fBuscar."%' OR
					  rd.Secuencia LIKE '%".$fBuscar."%' OR
					  rd.CommoditySub LIKE '%".$fBuscar."%' OR
					  rd.Descripcion LIKE '%".$fBuscar."%' OR
					  rd.CodUnidad LIKE '%".$fBuscar."%' OR
					  rd.CodCentroCosto LIKE '%".$fBuscar."%' OR
					  p.NomCompleto LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (r.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fCodDependencia != "") { $cCodDependencia = "checked"; $filtro.=" AND (r.CodDependencia = '".$fCodDependencia."')"; } else $dCodDependencia = "disabled";
if ($fCodCentroCosto != "") { $cCodCentroCosto = "checked"; $filtro.=" AND (rd.CodCentroCosto = '".$fCodCentroCosto."')"; } else $dCodCentroCosto = "disabled";
if ($fFechaPreparacionD != "" || $fFechaPreparacionH != "") {
	$cFechaPreparacion = "checked"; 
	if ($fFechaPreparacionD != "") $filtro.=" AND (r.FechaPreparacion >= '".formatFechaAMD($fFechaPreparacionD)."')"; 
	if ($fFechaPreparacionH != "") $filtro.=" AND (r.FechaPreparacion <= '".formatFechaAMD($fFechaPreparacionH)."')"; 
} else $dFechaPreparacion = "disabled";
if ($fFechaAprobacionD != "" || $fFechaAprobacionH != "") {
	$cFechaAprobacion = "checked"; 
	if ($fFechaAprobacionD != "") $filtro.=" AND (r.FechaAprobacion >= '".formatFechaAMD($fFechaAprobacionD)."')"; 
	if ($fFechaAprobacionH != "") $filtro.=" AND (r.FechaAprobacion <= '".formatFechaAMD($fFechaAprobacionH)."')"; 
} else $dFechaAprobacion = "disabled";
//	------------------------------------
$_width = 900;
//	------------------------------------
$display = "";
for($i=1; $i<=2; $i++) {
	if ($tb == $i) {
		$display .= "\$display_tab".$i." = 'display:block;';";
		$display .= "\$current_li".$i." = 'current';";
	}
	else {
		$display .= "\$display_tab".$i." = 'display:none;';";
		$display .= "\$current_li".$i." = '';";
	}
}
eval($display);
//	------------------------------------
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo">Confirmaci&oacute;n de Servicios de Caja Chica </td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lg_cajachica_confirmar_lista" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="tb" id="tb" value="<?=$tb?>" />

<!--FILTRO-->
<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
	<tr>
		<td align="right">Organismo:</td>
		<td>
			<input type="checkbox" <?=$cCodOrganismo?> onclick="this.checked=!this.checked" />
			<select name="fCodOrganismo" id="fCodOrganismo" style="width:300px;" onchange="getOptionsSelect(this.value, 'dependencia', 'fCodDependencia', 1, 'fCodCentroCosto');" <?=$dCodOrganismo?>>
				<?=getOrganismos($fCodOrganismo, 3)?>
			</select>
		</td>
		<td align="right">Buscar:</td>
		<td>
			<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
			<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:295px;" <?=$dBuscar?> />
		</td>
	</tr>
    <tr>
		<td align="right">Dependencia:</td>
		<td>
			<input type="checkbox" <?=$cCodDependencia?> onclick="chkFiltro(this.checked, 'fCodDependencia')" />
			<select name="fCodDependencia" id="fCodDependencia" style="width:300px;" onchange="getOptionsSelect(this.value, 'centro_costo', 'fCodCentroCosto', 1);" <?=$dCodDependencia?>>
				<option value="">&nbsp;</option>
				<?=getDependencias($fCodDependencia, $fCodOrganismo, 3);?>
			</select>
		</td>
		<td align="right">F.Preparaci&oacute;n: </td>
		<td>
			<input type="checkbox" <?=$cFechaPreparacion?> onclick="chkFiltro_2(this.checked, 'fFechaPreparacionD', 'fFechaPreparacionH');" />
			<input type="text" name="fFechaPreparacionD" id="fFechaPreparacionD" value="<?=$fFechaPreparacionD?>" <?=$dFechaPreparacion?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" /> -
            <input type="text" name="fFechaPreparacionH" id="fFechaPreparacionH" value="<?=$fFechaPreparacionH?>" <?=$dFechaPreparacion?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" />
        </td>
	</tr>
	<tr>
		<td align="right">Centro de Costo:</td>
		<td>
			<input type="checkbox" <?=$cCodCentroCosto?> onclick="chkFiltro(this.checked, 'fCodCentroCosto')" />
			<select name="fCodCentroCosto" id="fCodCentroCosto" style="width:300px;" <?=$dCodCentroCosto?>>
				<option value="">&nbsp;</option>
				<?=loadSelectDependiente("ac_mastcentrocosto", "CodCentroCosto", "Descripcion", "CodDependencia", $fCodCentroCosto, $fCodDependencia, 0)?>
			</select>
		</td>
		<td align="right">F.Aprobaci&oacute;n: </td>
		<td>
			<input type="checkbox" <?=$cFechaAprobacion?> onclick="chkFiltro_2(this.checked, 'fFechaAprobacionD', 'fFechaAprobacionH');" />
			<input type="text" name="fFechaAprobacionD" id="fFechaAprobacionD" value="<?=$fFechaAprobacionD?>" <?=$dFechaAprobacion?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" /> -
            <input type="text" name="fFechaAprobacionH" id="fFechaAprobacionH" value="<?=$fFechaAprobacionH?>" <?=$dFechaAprobacion?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" />
        </td>
		<td align="right">
        	<input type="submit" value="Buscar">
        </td>
	</tr>
</table>
</div>
<br />

<center>
<table cellpadding="0" cellspacing="0" style="width:100%; min-width:<?=$_width?>px;">
    <tr>
        <td>
            <div class="header">
            <ul id="tab">
            <!-- CSS Tabs -->
            <li id="li1" onclick="currentTab('tab', this);" class="<?=$current_li1?>">
            	<a href="#" onclick="mostrarTab('tab', '1', 2); $('#tb').val('1');">Requerimientos por Confirmar</a>
            </li>
            <li id="li2" onclick="currentTab('tab', this);" class="<?=$current_li2?>">
            	<a href="#" onclick="mostrarTab('tab', '2', 2); $('#tb').val('2');">Confirmaciones ya Realizadas</a>
            </li>
            </ul>
            </div>
        </td>
    </tr>
</table>

<!--REGISTROS-->
<div id="tab1" style=" <?=$display_tab1?>">
<input type="hidden" name="sel_confirmar" id="sel_confirmar" />
<table class="tblBotones" style="width:100%; min-width:<?=$_width?>px;">
    <tr>
        <td><div id="rows"></div></td>
        <td align="right">
            <input type="button" value="Confirmar Servicio" class="update" onclick="cargarOpcion2(this.form, 'gehen.php?anz=lg_cajachica_confirmar_form&opcion=confirmar', 'SELF', '', $('#sel_registros').val());" />
        </td>
    </tr>
</table>
<div style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px;">
<table class="tblLista" style="width:100%; min-width:1700px;">
	<thead>
    <tr>
        <th width="100">Requerimiento</th>
        <th width="25">#</th>
        <th width="75">Commodity</th>
        <th>Descripci&oacute;n</th>
        <th width="25">Uni.</th>
        <th width="50">Cant. Pedida</th>
        <th width="50">Cant. Recibida</th>
        <th width="50">Cant. Pendiente</th>
        <th width="50">C.Costo</th>
        <th width="75">Fecha Aprobaci&oacute;n</th>
        <th width="75">Fecha Preparaci&oacute;n</th>
        <th width="300">Aprobado Por</th>
    </tr>
    </thead>
    
    <tbody id="lista_registros">
	<?php
	//	consulto lista
	$sql = "SELECT
				rd.CodRequerimiento,
				rd.Secuencia,
				rd.CommoditySub,
				rd.Descripcion,
				rd.CodUnidad,
				rd.CantidadPedida,
				rd.CantidadRecibida,				
				(rd.CantidadPedida - rd.CantidadRecibida) AS CantidadPendiente,
				rd.CodCentroCosto,
				r.CodInterno,
				r.FechaAprobacion,
				r.FechaPreparacion,
				p.NomCompleto AS NomAprobadaPor
			FROM
				lg_requerimientosdet rd
				INNER JOIN lg_requerimientos r ON (r.CodRequerimiento = rd.CodRequerimiento)
				LEFT JOIN mastpersonas p ON (p.CodPersona = r.AprobadaPor)
			WHERE
				r.FlagCajaChica = 'S' AND
				r.Clasificacion = 'SER' AND
				(rd.CantidadPedida - rd.CantidadRecibida) > 0
				$filtro
			ORDER BY CodInterno, Secuencia";
	$field = getRecords($sql);
	$rows_lista = count($field);
	$i = 0;
	foreach($field as $f) {
		$id = $f['CodRequerimiento'].'_'.$f['Secuencia'];
		##
		?>
		<tr class="trListaBody" onclick="clk($(this), 'confirmar', '<?=$id?>');">
			<td><?=$f['CodInterno']?></td>
			<td align="center"><?=$f['Secuencia']?></td>
			<td align="center"><?=$f['CommoditySub']?></td>
			<td><?=htmlentities($f['Descripcion'])?></td>
			<td align="center"><?=$f['CodUnidad']?></td>
			<td align="right"><?=number_format($f['CantidadPedida'], 2, ',', '.')?></td>
			<td align="right"><?=number_format($f['CantidadRecibida'], 2, ',', '.')?></td>
			<td align="right"><?=number_format($f['CantidadPendiente'], 2, ',', '.')?></td>
			<td align="center"><?=$f['CodCentroCosto']?></td>
			<td align="center"><?=formatFechaDMA($f['FechaPreparacion'])?></td>
			<td align="center"><?=formatFechaDMA($f['FechaAprobacion'])?></td>
			<td><?=htmlentities($f['NomAprobadaPor'])?></td>
		</tr>
		<?php
	}
	?>
    </tbody>
</table>
</div>
</div>

<div id="tab2" style=" <?=$display_tab2?>">
<input type="hidden" name="sel_desconfirmar" id="sel_desconfirmar" />
<table class="tblBotones" style="width:100%; min-width:<?=$_width?>px;">
    <tr>
        <td><div id="rows"></div></td>
        <td align="right" class="gallery clearfix">
            <input type="button" value="Desconfirmar Servicio" class="update" onclick="opcionRegistro3(this.form, $('#sel_desconfirmar').val(), 'formulario', 'desconfirmar', 'lg_cajachica_confirmar_ajax.php');" /> |
            <input type="button" value="Imprimir Acta" class="ver" onclick="abrirIFrame2($('#frmentrada'), $('#a_frame'), 'lg_cajachica_confirmar_pdf.php?', $('#sel_desconfirmar').val());"  />
            <a href="pagina.php?iframe=true" rel="prettyPhoto[iframe1]" style="display:none;" id="a_frame"></a>
        </td>
    </tr>
</table>
<div style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px;">
<table class="tblLista" style="width:100%; min-width:1400px">
	<thead>
    <tr>
        <th width="100">Requerimiento</th>
        <th width="25">#</th>
        <th width="75">Commodity</th>
        <th>Descripci&oacute;n</th>
        <th width="25">Uni.</th>
        <th width="50">Cant. Recibida</th>
        <th width="50">C.Costo</th>
        <th width="75">Fecha Confirmaci&oacute;n</th>
        <th width="300">Confirmada Por</th>
    </tr>
    </thead>
    
    <tbody id="lista_registros">
	<?php
	//	consulto lista
	$sql = "SELECT
				ccc.CodRequerimiento,
				ccc.Secuencia,
				ccc.Numero,
				ccc.Anio,
				ccc.NroConfirmacion,
				ccc.CantidadRecibida,
				ccc.FechaConfirmadaPor,
				r.CodInterno,
				rd.CommoditySub,
				rd.Descripcion,
				rd.CodUnidad,
				rd.CodCentroCosto,
				p.NomCompleto AS NomConfirmadaPor
			FROM
				lg_cajachicaconfirmacion ccc
				INNER JOIN lg_requerimientos r ON (r.CodRequerimiento = ccc.CodRequerimiento)
				INNER JOIN lg_requerimientosdet rd ON (rd.CodRequerimiento = ccc.CodRequerimiento AND
													   rd.Secuencia = ccc.Secuencia)
				INNER JOIN mastpersonas p ON (p.CodPersona = ccc.ConfirmadaPor)
			WHERE
				1
				$filtro";
	$field = getRecords($sql);
	$i = 0;
	foreach($field as $f) {
		$id = $f['CodRequerimiento'].'_'.$f['Secuencia'].'_'.$f['Numero'];
		##
		?>
		<tr class="trListaBody" onclick="clk($(this), 'desconfirmar', '<?=$id?>');">
			<td><?=$f['CodInterno']?></td>
			<td align="center"><?=$f['Secuencia']?></td>
			<td align="center"><?=$f['CommoditySub']?></td>
			<td><?=htmlentities($f['Descripcion'])?></td>
			<td align="center"><?=$f['CodUnidad']?></td>
			<td align="right"><?=number_format($f['CantidadRecibida'], 2, ',', '.')?></td>
			<td align="center"><?=$f['CodCentroCosto']?></td>
			<td align="center"><?=formatFechaDMA($f['FechaConfirmadaPor'])?></td>
			<td><?=htmlentities($f['NomConfirmadaPor'])?></td>
		</tr>
		<?php
	}
	?>
    </tbody>
</table>
</div>
</div>
</center>
</form>