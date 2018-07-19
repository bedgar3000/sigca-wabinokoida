<?php
$titulo = "Disponibilidad Presupuestaria de Caja Chica";
$_width = 800;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$titulo?></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<table align="center" cellpadding="0" cellspacing="0" style="width:100%; min-width:<?=$_width?>px;">
    <tr>
        <td>
            <div class="header">
            <ul id="tab">
            <!-- CSS Tabs -->
            <li id="li1" onclick="currentTab('tab', this);" class="current">
            	<a href="#" onclick="mostrarTab('tab', 1, 2);">Disponibilidad Presupuesto</a>
            </li>
            <li id="li2" onclick="currentTab('tab', this);">
            	<a href="#" onclick="mostrarTab('tab', 2, 2);">Distribuci&oacute;n Compromiso</a>
            </li>
            </ul>
            </div>
        </td>
    </tr>
</table>

<div id="tab1" style="display:block;">
<div style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:300px;">
<table class="tblLista" style="width:100%; min-width:1450px;">
	<thead>
    <tr>
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
	foreach($partidas_cod_partida as $cod_partida) {
		##	descripcion
		$Descripcion = getVar3("SELECT denominacion FROM pv_partida WHERE cod_partida = '".$partidas_cod_partida[$i]."'");
		$Resta = $partidas_MontoDisponible[$i] - $partidas_Monto[$i];
		$RestaReal = $partidas_MontoDisponibleReal[$i] - $partidas_Monto[$i];
		##	valido
		if ($partidas_Monto[$i] > $partidas_MontoDisponible[$i]) $style = "style='background-color:#F8637D;'";
		elseif($partidas_Monto[$i] > $partidas_MontoDisponibleReal[$i]) $style = "style='background-color:#FFC;'";
		else $style = "style='background-color:#D0FDD2;'";
		?>
		<tr class="trListaBody" <?=$style?>>
			<td align="center"><?=$partidas_cod_partida[$i]?></td>
			<td><?=htmlentities($Descripcion)?></td>
			<td align="right"><?=number_format($partidas_MontoAjustado[$i],2,',','.')?></td>
			<td align="right"><?=number_format($partidas_MontoCompromiso[$i],2,',','.')?></td>
			<td align="right"><strong><?=number_format($partidas_MontoDisponible[$i],2,',','.')?></strong></td>
			<td align="right"><?=number_format($partidas_Monto[$i],2,',','.')?></td>
			<td align="right"><strong><?=number_format($Resta,2,',','.')?></strong></td>
			<td align="right"><?=number_format(($partidas_PreCompromiso[$i]+$partidas_CotizacionesAsignadas[$i]),2,',','.')?></td>
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
<div style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:300px;">
<table class="tblLista" style="width:100%; min-width:<?=$_width?>px;">
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
	foreach($partidas_cod_partida as $cod_partida) {
		$Descripcion = getVar3("SELECT denominacion FROM pv_partida WHERE cod_partida = '".$partidas_cod_partida[$i]."'");
		$field_documento = distribucionPartida($Anio, $CodOrganismo, $cod_partida, $CodPresupuesto);
		foreach($field_documento as $fd) {
			if ($Grupo != $cod_partida) {
				$Grupo = $cod_partida;
				?>
				<tr class="trListaBody2">
					<th><?=$cod_partida?></th>
					<th align="left"><?=$Descripcion?></th>
                    <th align="right"><?=number_format(($partidas_PreCompromiso[$i]+$partidas_CotizacionesAsignadas[$i]),2,',','.')?></th>
				</tr>
				<?php
			}
			?>
			<tr class="trListaBody">
				<td align="center"><?=$fd['Origen']?>/<?=$fd['Documento']?></td>
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

<table style="width:<?=$_width?>px;">
	<tr>
    	<td width="35"><div style="background-color:#F8637D; width:25px; height:20px;"></div></td>
        <td>Sin disponibilidad presupuestaria</td>
    	<td width="35"><div style="background-color:#D0FDD2; width:25px; height:20px;"></div></td>
        <td>Disponibilidad presupuestaria</td>
    	<td width="35"><div style="background-color:#FFC; width:25px; height:20px;"></div></td>
        <td>Sin disponibilidad presupuestaria (+ Pre-Compromiso)</td>
	</tr>
</table>