<?php
if (!$ventana) $ventana = "selLista";
//	------------------------------------
$fBuscar = ($fBuscar?$fBuscar:$_SESSION["fBuscar"]);
$_SESSION["fBuscar"] = $fBuscar;
##	
if ($filtrar == "default") {
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "cod_partida";
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (pv.cod_partida LIKE '%".$fBuscar."%' OR
					  pv.denominacion LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fCodOrganismo != "" || $FlagCategoriaProg == 'S') { $cCodOrganismo = "checked"; $filtro.=" AND (p.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fCodPresupuesto != "" || $FlagCategoriaProg == 'S') { $cCategoriaProg = "checked"; $filtro.=" AND (p.CategoriaProg = '".$fCategoriaProg."')"; } else $dCategoriaProg = "visibility:hidden;";
if ($fCodFuente != "") $filtro.=" AND (pd.CodFuente = '".$fCodFuente."')";
if ($fEjercicio != "") $filtro.=" AND (p.Ejercicio = '".$fEjercicio."')";
//	------------------------------------
$_titulo = "Clasificador Presupuestario";
$_width = 900;
?>
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lista_pv_partida_presupuesto" method="post" autocomplete="off">
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
<input type="hidden" name="seldetalle" id="seldetalle" value="<?=$seldetalle?>" />
<input type="hidden" name="modulo" id="modulo" value="<?=$modulo?>" />
<input type="hidden" name="accion" id="accion" value="<?=$accion?>" />
<input type="hidden" name="url" id="url" value="<?=$url?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
<input type="hidden" name="FlagTipoCuenta" id="FlagTipoCuenta" value="<?=$FlagTipoCuenta?>" />
<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
<input type="hidden" name="FlagCategoriaProg" id="FlagCategoriaProg" value="<?=$FlagCategoriaProg?>" />
<input type="hidden" name="CodOrganismo" id="CodOrganismo" value="<?=$CodOrganismo?>" />
<input type="hidden" name="CodPresupuesto" id="CodPresupuesto" value="<?=$CodPresupuesto?>" />
<input type="hidden" name="CodFuente" id="CodFuente" value="<?=$CodFuente?>" />
<input type="hidden" name="fCodFuente" id="fCodFuente" value="<?=$fCodFuente?>" />
<input type="hidden" name="Tipo" id="Tipo" value="<?=$Tipo?>" />

<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
	<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
		<tr>
			<td class="tagForm" width="100">Presupuesto:</td>
			<td>
				<?php
				if ($FlagCategoriaProg == 'S') {
					?>
					<input type="checkbox" checked onclick="this.checked=!this.checked;" />
					<input type="text" name="fEjercicio" id="fEjercicio" value="<?=$fEjercicio?>" style="width:48px;" class="Ejercicio" readonly />
					<input type="text" name="fCodPresupuesto" id="fCodPresupuesto" value="<?=$fCodPresupuesto?>" style="width:48px;" class="CodPresupuesto" readonly />
					<?php
				} else {
					?>
					<input type="checkbox" <?=$cCategoriaProg?> onclick="ckLista(this.checked, ['fEjercicio','fCodPresupuesto','fCategoriaProg'], ['btPresupuesto']);" />
					<input type="text" name="fEjercicio" id="fEjercicio" value="<?=$fEjercicio?>" style="width:48px;" class="Ejercicio" readonly />
					<input type="text" name="fCodPresupuesto" id="fCodPresupuesto" value="<?=$fCodPresupuesto?>" style="width:48px;" class="CodPresupuesto" readonly />
					<a href="#" onclick="window.open('gehen.php?anz=lista_pv_presupuesto&filtrar=default&campo1=fEjercicio&campo2=fCodPresupuesto&campo3=fCategoriaProg&ventana=lg_requerimiento_opener&iframe=true&width=100%&height=100%','lista','toolbar=no, menubar=no, location=no, scrollbars=yes, height=5000, width=5000, left=200, top=200, resizable=yes');" style=" <?=$dCategoriaProg?>" id="btPresupuesto">
		            	<img src="../../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
		            </a>
					<?php
				}
				?>
			</td>
			<td align="right" width="100">Buscar:</td>
			<td>
				<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
				<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:149px;" <?=$dBuscar?> />
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td class="tagForm">Cat. Prog.:</td>
			<td>
				<input type="checkbox" style="visibility:hidden;" />
				<input type="text" name="fCategoriaProg" id="fCategoriaProg" value="<?=$fCategoriaProg?>" style="width:100px;" class="CategoriaProg" readonly />
			</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
	        <td align="right"><input type="submit" value="Buscar"></td>
		</tr>
	</table>
</div>
<div class="sep"></div>

<center>
<div class="scroll" style="overflow:scroll; height:315px; width:100%; min-width:<?=$_width?>px;">
	<table class="tblLista" style="width:100%; min-width:1300px;">
		<thead>
		    <tr>
		        <th width="25" onclick="order('CodFuente')">F.F.</th>
		        <th width="75" onclick="order('cod_partida')">C&oacute;digo</th>
		        <th align="left" onclick="order('denominacion')">Descripci&oacute;n</th>
				<th width="100" onclick="order('MontoAprobado')">Monto Aprobado</th>
				<th width="100" onclick="order('MontoAjustado')">Monto Ajustado</th>
				<th width="100" onclick="order('MontoCompromiso')">Monto Compromiso</th>
				<th width="100" onclick="order('MontoDisponible')">Monto Disponible</th>
		    </tr>
	    </thead>
	    
	    <tbody>
		<?php
		if ($fCodPresupuesto)
		{
			//	consulto todos
			$sql = "SELECT pv.cod_partida
					FROM
						pv_partida pv
						INNER JOIN pv_presupuestodet pd ON (pd.cod_partida = pv.cod_partida)
						INNER JOIN pv_presupuesto p ON (p.CodOrganismo = pd.CodOrganismo AND p.CodPresupuesto = pd.CodPresupuesto)
					WHERE 1 $filtro";
			$rows_total = getNumRows3($sql);
			//	consulto lista
			$sql = "SELECT
						pv.*,
						pd.CodFuente,
						pd.MontoAprobado,
						pd.MontoAjustado,
						pd.MontoCompromiso,
						p.CategoriaProg,
						p.CodPresupuesto,
						p.CodOrganismo,
						pd.CodFuente
					FROM
						pv_partida pv
						INNER JOIN pv_presupuestodet pd ON (pd.cod_partida = pv.cod_partida)
						INNER JOIN pv_presupuesto p ON (p.CodOrganismo = pd.CodOrganismo AND p.CodPresupuesto = pd.CodPresupuesto)
					WHERE 1 $filtro
					ORDER BY $fOrderBy
					LIMIT ".intval($limit).", ".intval($maxlimit);
			$field = getRecords($sql);
			$rows_lista = count($field);
			foreach($field as $f) {
				$id = $f['cod_partida'];
				$MontoDisponible = $f['MontoAjustado'] - $f['MontoCompromiso'];
				if ($ventana == 'listado_insertar_linea') 
				{
					?><tr class="trListaBody" onClick="<?=$ventana?>('<?=$detalle?>','modulo=<?=$modulo?>&accion=<?=$accion?>&cod_partida=<?=$f['cod_partida']?>','<?=$f['cod_partida']?>','<?=$url?>');"><?php
				}
				elseif ($ventana == 'ob_obras') 
				{
					?><tr class="trListaBody" onClick="listado_insertar_linea('<?=$detalle?>','modulo=<?=$modulo?>&accion=<?=$accion?>&cod_partida=<?=$f['cod_partida']?>&CodOrganismo=<?=$CodOrganismo?>&CodPresupuesto=<?=$CodPresupuesto?>&CodFuente=<?=$CodFuente?>','<?=$f['cod_partida']?>','<?=$url?>');"><?php
				}
				elseif ($ventana == 'pv_ajustes') 
				{
					?><tr class="trListaBody" onClick="listado_insertar_linea('<?=$detalle?>','modulo=<?=$modulo?>&accion=<?=$accion?>&cod_partida=<?=$f['cod_partida']?>&CodFuente=<?=$f['CodFuente']?>&CodOrganismo=<?=$fCodOrganismo?>&CodPresupuesto=<?=$fCodPresupuesto?>&Ejercicio=<?=$fEjercicio?>&CategoriaProg=<?=$fCategoriaProg?>&detalle=<?=$detalle?>&Tipo=<?=$Tipo?>','<?=$f['cod_partida']?>','<?=$url?>');"><?php
				}
				elseif ($ventana == 'fuente') 
				{
					?><tr class="trListaBody" onClick="selLista(['<?=$f['cod_partida']?>','<?=$f['CodFuente']?>','<?=$f['denominacion']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>']);"><?php
				}
				elseif ($ventana == "selListadoListaParent") 
				{
					?><tr class="trListaBody" onclick="<?=$ventana?>('<?=$seldetalle?>',['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>','<?=$campo4?>','<?=$campo5?>'],['<?=$f['cod_partida']?>','<?=$f['CodFuente']?>','<?=$f['CodPresupuesto']?>','<?=$f['CategoriaProg']?>','<?=$f['CodOrganismo']?>']);" id="<?=$f['cod_partida']?>"><?php
				}
				else 
				{
					?><tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['cod_partida']?>','<?=$f['denominacion']?>'], ['<?=$campo1?>','<?=$campo2?>']);"><?php
				}
				?>
					<td align="center"><?=$f['CodFuente']?></td>
					<td align="center"><?=$f['cod_partida']?></td>
					<td><?=htmlentities($f['denominacion'])?></td>
					<td align="right"><?=number_format($f['MontoAprobado'],2,',','.')?></td>
					<td align="right"><?=number_format($f['MontoAjustado'],2,',','.')?></td>
					<td align="right"><?=number_format($f['MontoCompromiso'],2,',','.')?></td>
					<td align="right"><?=number_format($MontoDisponible,2,',','.')?></td>
				</tr>
				<?php
			}	
		}
		?>
	    </tbody>
	</table>
</div>
<table style="width:100%; min-width:<?=$_width?>px;">
	<tr>
    	<td>
        	Mostrar: 
            <select name="maxlimit" style="width:50px;" onchange="this.form.submit();">
                <?=loadSelectGeneral("MAXLIMIT", $maxlimit, 0)?>
            </select>
        </td>
        <td align="right">
        	<?=paginacion(intval($rows_total), intval($rows_lista), intval($maxlimit), intval($limit));?>
        </td>
    </tr>
</table>
</center>
</form>

<script type="text/javascript">
</script>