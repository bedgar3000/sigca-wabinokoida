<?php
list($AnioActual, $MesActual, $DiaActual) = split("[/.-]", substr($Ahora, 0, 10));
$FechaActual = "$AnioActual-$MesActual-$DiaActual";
//	------------------------------------
if ($filtrar == "default") {
	$CodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$Periodo = "$AnioActual-$MesActual";
	$id_tab = 0;
}
$i=0;
$display_tab[0] = "display:none;";
$display_tab[1] = "display:none;";
foreach($display_tab as $_tab) {
	if ($id_tab == $i) { $display_tab[$i] = "display:block;"; $current_tab[$i] = "current"; }
	else { $display_tab[$i] = "display:none;"; $current_tab[$i] = ""; }
	++$i;
}
//	------------------------------------
$_width = 1000;
$_sufijo = "cierre_mensual";
$_titulo = "Cierre Mensual";
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lg_<?=$_sufijo?>" method="post" autocomplete="off" onsubmit="cierre_mensual(this);">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="id_tab" id="id_tab" value="<?=$id_tab?>" />
<input type="hidden" name="sel_registros" id="sel_registros" />

<!--FILTRO-->
<div class="divBorder" style="width:<?=$_width-2?>px;">
<table width="100%" class="tblFiltro">
	<tr>
		<td align="right" width="100">Periodo:</td>
		<td width="300">
			<input type="checkbox" checked onclick="this.checked=!this.checked" />
			<input type="text" name="Periodo" id="Periodo" value="<?=$Periodo?>" style="width:50px;" />
		</td>
		<td align="right" width="100">Organismo:</td>
		<td>
			<input type="checkbox" checked onclick="this.checked=!this.checked" />
			<select name="CodOrganismo" id="CodOrganismo" style="width:275px;">
				<?=getOrganismos($CodOrganismo, 3)?>
			</select>
		</td>
        <td align="right"><input type="submit" value="Generar" style="width:100px;" /></td>
	</tr>
</table>
</div>
</form>

<table width="<?=$_width?>" align="center" cellpadding="0" cellspacing="0">
    <tr>
        <td>
            <div class="header">
            <ul id="tab">
            <!-- CSS Tabs -->
            <li id="li1" class="<?=$current_tab[0]?>" onclick="currentTab('tab', this); $('#id_tab').val('0');">
            	<a href="#" onclick="mostrarTab('tab', '1', 3)">Datos para Precio Promedio</a>
            </li>
            <li id="li2" class="<?=$current_tab[1]?>" onclick="currentTab('tab', this); $('#id_tab').val('1');">
            	<a href="#" onclick="mostrarTab('tab', '2', 3);">Errores Detectados</a>
            </li>
            <li id="li3" class="<?=$current_tab[2]?>" onclick="currentTab('tab', this); $('#id_tab').val('2');">
            	<a href="#" onclick="mostrarTab('tab', '3', 3);">Soporte de Cambio de Precios</a>
            </li>
            </ul>
            </div>
        </td>
    </tr>
</table>

<!--REGISTROS-->
<center>

<div id="tab1" style=" <?=$display_tab[0]?>;">
<div style="overflow:scroll; width:<?=$_width?>px; height:300px;">
<table width="100%" class="tblLista">
	<thead>
    <tr>
		<th scope="col" width="70">Item</th>
		<th scope="col" width="70">Almacen</th>
		<th scope="col" width="65">Fecha</th>
		<th scope="col" width="75">Transacci&oacute;n</th>
		<th scope="col" width="15">#</th>
		<th scope="col">Transacci&oacute;n</th>
		<th scope="col" width="100">Orden de Compra</th>
		<th scope="col" width="50">Cant.</th>
		<th scope="col" width="25">Uni.</th>
		<th scope="col" width="75" align="right">Precio Unit.</th>
		<th scope="col" width="75" align="right">Total</th>
    </tr>
    </thead>
    
    <tbody>
	<?php
	/*//	consulto lista
	$sql = "SELECT
				td.CodOrganismo,
				td.CodDocumento,
				td.NroDocumento,
				td.Secuencia,
				td.CodItem,
				td.Descripcion,
				td.CodUnidad,
				td.CantidadRecibida,
				td.PrecioUnit,
				td.Total,
				td.ReferenciaCodDocumento,
				t.CodDocumento,
				t.NroInterno,
				t.CodTransaccion,
				t.FechaDocumento,
				t.CodAlmacen,
				t.Periodo,
				tt.Descripcion AS NomTransaccion,
				oc.Anio,
				oc.NroInterno AS NroInternoOrden
			FROM
				lg_transacciondetalle td
				INNER JOIN lg_transaccion t ON (t.CodOrganismo = td.CodOrganismo AND
												t.CodDocumento = td.CodDocumento AND
												t.NroDocumento = td.NroDocumento)
				INNER JOIN lg_ordencompradetalle ocd ON (ocd.Anio = t.Anio AND
														 ocd.CodOrganismo = t.CodOrganismo AND
														 ocd.NroOrden = td.ReferenciaNroDocumento AND
														 ocd.Secuencia = td.ReferenciaSecuencia)
				INNER JOIN lg_ordencompra oc ON (oc.Anio = ocd.Anio AND
												 oc.CodOrganismo = ocd.CodOrganismo AND
												 oc.NroOrden = ocd.NroOrden)
				INNER JOIN lg_tipotransaccion tt ON (tt.CodTransaccion = t.CodTransaccion)
			WHERE
				t.CodOrganismo = '".$CodOrganismo."' AND
				t.Periodo = '".$Periodo."' AND
				t.Estado = 'CO' AND
				(t.CodTransaccion = 'ROC' OR 
				 t.CodTransaccion = 'ARO' OR 
				 t.CodTransaccion = 'DRO' OR 
				 t.CodTransaccion = 'MIT' OR 
				 t.CodTransaccion = 'TRT')
			ORDER BY CodItem, FechaDocumento";
	$field = getRecords($sql);
	foreach($field as $f) {
		if ($Grupo != $f['CodItem']) {
			$Grupo = $f['CodItem'];
			$sql = "SELECT COUNT(*)
					FROM
						lg_transacciondetalle td
						INNER JOIN lg_transaccion t ON (t.CodOrganismo = td.CodOrganismo AND
														t.CodDocumento = td.CodDocumento AND
														t.NroDocumento = td.NroDocumento)
						INNER JOIN lg_ordencompradetalle ocd ON (ocd.Anio = t.Anio AND
																 ocd.CodOrganismo = t.CodOrganismo AND
																 ocd.NroOrden = td.ReferenciaNroDocumento AND
																 ocd.Secuencia = td.ReferenciaSecuencia)
						INNER JOIN lg_ordencompra oc ON (oc.Anio = ocd.Anio AND
														 oc.CodOrganismo = ocd.CodOrganismo AND
														 oc.NroOrden = ocd.NroOrden)
						INNER JOIN lg_tipotransaccion tt ON (tt.CodTransaccion = t.CodTransaccion)
					WHERE
						td.CodItem = '".$f['CodItem']."' AND
						t.CodOrganismo = '".$f['CodOrganismo']."' AND
						t.Periodo = '".$f['Periodo']."' AND
						t.Estado = 'CO' AND
						(t.CodTransaccion = 'ROC' OR 
						 t.CodTransaccion = 'ARO' OR 
						 t.CodTransaccion = 'DRO' OR 
						 t.CodTransaccion = 'MIT' OR 
						 t.CodTransaccion = 'TRT')";
			$Count = getVar3($sql);
			$tdCodItem = "<td align='center' rowspan='$Count'>$f[CodItem]</td>";
		} else $tdCodItem = "";
		?>
        <tr class="trListaBody">
        	<?=$tdCodItem?>
        	<td align="center"><?=$f['CodAlmacen']?></td>
			<td align="center"><?=formatFechaDMA($f['FechaDocumento'])?></td>
        	<td align="center"><?=$f['CodDocumento']?> - <?=$f['NroInterno']?></td>
        	<td align="center"><?=$f['Secuencia']?></td>
        	<td><?=$f['CodTransaccion']?> - <?=htmlentities($f['NomTransaccion'])?></td>
        	<td align="center"><?=$f['ReferenciaCodDocumento']?> - <?=$f['NroInternoOrden']?></td>
        	<td align="right"><?=number_format($f['CantidadRecibida'], 2, ',', '.')?></td>
        	<td align="center"><?=$f['CodUnidad']?></td>
        	<td align="right"><?=number_format($f['PrecioUnit'], 2, ',', '.')?></td>
        	<td align="right"><?=number_format($f['Total'], 2, ',', '.')?></td>
        </tr>
        <?php
	}*/
	?>
    </tbody>
</table>
</div>
</div>

<div id="tab2" style=" <?=$display_tab[1]?>;">
</div>

<div id="tab3" style=" <?=$display_tab[2]?>;">
</div>
</center>