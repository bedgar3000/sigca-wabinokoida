<?php
$titulo = "Disponibilidad Presupuestaria de Obligaci&oacute;n";
$_width = 800;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$titulo?></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<table align="center" cellpadding="0" cellspacing="0" style="width:100%; min-width:100%;">
    <tr>
        <td>
            <div class="header">
            <ul id="tab">
            <!-- CSS Tabs -->
            <li id="li1" onclick="currentTab('tab', this);" class="current">
            	<a href="#" onclick="mostrarTab('tab', 1, 2);">Disponibilidad Presupuesto</a>
            </li>
            <li id="li2" onclick="currentTab('tab', this);">
            	<a href="#" onclick="mostrarTab('tab', 2, 2);">Distribuci&oacute;n Pre-Compromiso</a>
            </li>
            </ul>
            </div>
        </td>
    </tr>
</table>

<div id="tab1" style="display:block;">
<div style="overflow:scroll; width:100%; min-width:100%; height:300px;">
<table class="tblLista" style="width:100%; min-width:1450px;">
	<thead>
    <tr>
        <th width="75">Cat. Prog.</th>
        <th width="25">F.F.</th>
        <th width="80">Partida</th>
        <th align="left">Descripci&oacute;n</th>
        <th width="100" align="right">Ajustado</th>
        <th width="100" align="right">Comprometido</th>
        <th width="100" align="right">Disponible</th>
        <th width="100" align="right">Monto</th>
        <th width="100" align="right">Resta</th>
        <th width="100" align="right">Pre-Compromiso</th>
        <th width="100" align="right">Resta</th>
    </tr>
    </thead>
    
    <tbody id="lista_registros">
	<?php
    $i = 0;
	$detalle = split(";char:tr;", $detalles_partida);
	foreach ($detalle as $linea) {
		list($_cod_partida, $_Monto, $_MontoAjustado, $_MontoCompromiso, $_PreCompromiso, $_CotizacionesAsignadas, $_MontoDisponible, $_MontoDisponibleReal, $_MontoPendiente, $_CodFuente, $_CategoriaProg) = split(";char:td;", $linea);
		##	descripcion
		$Descripcion = getVar3("SELECT denominacion FROM pv_partida WHERE cod_partida = '".$_cod_partida."'");
		$Resta = $_MontoDisponible - $_Monto;
		$RestaReal = $_MontoDisponibleReal - $_Monto;
		##	valido
		$_Monto = round($_Monto, 2);
		$_MontoDisponible = round($_MontoDisponible, 2);
		$_MontoDisponibleReal = round($_MontoDisponibleReal, 2);
		if ($_Monto > $_MontoDisponible) $style = "style='background-color:#F8637D;'";
		elseif($_Monto > $_MontoDisponibleReal) $style = "style='background-color:#FFC;'";
		else $style = "style='background-color:#D0FDD2;'";
		?>
		<tr class="trListaBody" <?=$style?>>
			<td align="center"><?=substr($_CategoriaProg,0,2)?><?=substr($_CategoriaProg,4,2)?><?=substr($_CategoriaProg,10,2)?></td>
			<td align="center"><?=$_CodFuente?></td>
			<td align="center"><?=$_cod_partida?></td>
			<td><?=htmlentities($Descripcion)?></td>
			<td align="right"><?=number_format($_MontoAjustado,2,',','.')?></td>
			<td align="right"><?=number_format($_MontoCompromiso,2,',','.')?></td>
			<td align="right"><strong><?=number_format($_MontoDisponible,2,',','.')?></strong></td>
			<td align="right"><?=number_format($_Monto,2,',','.')?></td>
			<td align="right"><strong><?=number_format($Resta,2,',','.')?></strong></td>
			<td align="right"><?=number_format($_MontoPendiente,2,',','.')?></td>
			<td align="right"><strong><?=number_format($RestaReal,2,',','.')?></strong></td>
		</tr>
		<?php
		++$i;
	}
	?>
    </tbody>
</table>
</div>
</div>

<div id="tab2" style="display:none;">
<div style="overflow:scroll; width:100%; min-width:100%; height:300px;">
<table class="tblLista" style="width:100%; min-width:100%;">
	<thead>
    <tr>
        <th width="125">Documento</th>
        <th align="left">Descripci&oacute;n</th>
        <th width="100" align="right">Monto</th>
    </tr>
    </thead>
    
    <tbody id="lista_registros">
	<?php
    $i = 0;
	$detalle = split(";char:tr;", $detalles_partida);
	foreach ($detalle as $linea) {
		list($_cod_partida, $_Monto, $_MontoAjustado, $_MontoCompromiso, $_PreCompromiso, $_CotizacionesAsignadas, $_MontoDisponible, $_MontoDisponibleReal, $_MontoPendiente) = split(";char:td;", $linea);
		##	
		$Descripcion = getVar3("SELECT denominacion FROM pv_partida WHERE cod_partida = '".$_cod_partida."'");
		$field_documento = distribucionPartida($Anio, $CodOrganismo, $_cod_partida, $CodPresupuesto);
		foreach($field_documento as $fd) {
			if ($Grupo != $_cod_partida) {
				$Grupo = $_cod_partida;
				?>
				<tr class="trListaBody2">
					<th><?=$_cod_partida?></th>
					<th align="left"><?=$Descripcion?></th>
                    <th align="right"><?=number_format(($_MontoPendiente),2,',','.')?></th>
				</tr>
				<?php
			}
			?>
			<tr class="trListaBody">
				<td align="center"><?=$fd['Documento']?></td>
				<td><?=htmlentities($fd['Descripcion'])?></td>
				<td align="right"><?=number_format($fd['Monto'],2,',','.')?></td>
			</tr>
			<?php
		}
		++$i;
	}
	?>
    </tbody>
</table>
</div>
</div>

<table style="width:100%;">
	<tr>
    	<td width="35"><div style="background-color:#F8637D; width:25px; height:20px;"></div></td>
        <td>Sin disponibilidad presupuestaria</td>
    	<td width="35"><div style="background-color:#D0FDD2; width:25px; height:20px;"></div></td>
        <td>Disponibilidad presupuestaria</td>
    	<td width="35"><div style="background-color:#FFC; width:25px; height:20px;"></div></td>
        <td>Sin disponibilidad presupuestaria (+ Pre-Compromiso)</td>
	</tr>
</table>