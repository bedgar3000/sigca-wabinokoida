<?php
if (!$ventana) $ventana = "selLista";
//	------------------------------------
if ($filtrar == "default") {
	$fEstado = 'A';
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "CodConcepto";
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (cc.CodConcepto LIKE '%".$fBuscar."%'
					  OR cc.Descripcion LIKE '%".$fBuscar."%'
					  OR cc.Categoria LIKE '%".$fBuscar."%'
					  OR cc.cod_partida LIKE '%".$fBuscar."%'
					  OR cc.CodCuenta LIKE '%".$fBuscar."%'
					  OR cc.CodCuentaPub20 LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (cc.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fCategoria != "") { $cCategoria = "checked"; $filtro.=" AND (cc.Categoria = '".$fCategoria."')"; } else $dCategoria = "disabled";
if ($fcod_partida != "") { $ccod_partida = "checked"; $filtro.=" AND (cc.cod_partida = '".$fcod_partida."')"; } else $dcod_partida = "visibility:hidden;";
//	------------------------------------
$_titulo = "Conceptos de Compromisos Directos";
$_width = 900;
?>
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lista_ap_conceptoscertificacion" method="post" autocomplete="off">
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
<input type="hidden" name="seldetalle" id="seldetalle" value="<?=$seldetalle?>" />
<input type="hidden" name="Ejercicio" id="Ejercicio" value="<?=$Ejercicio?>" />
<input type="hidden" name="CodPresupuesto" id="CodPresupuesto" value="<?=$CodPresupuesto?>" />
<input type="hidden" name="CategoriaProg" id="CategoriaProg" value="<?=$CategoriaProg?>" />
<input type="hidden" name="CodFuente" id="CodFuente" value="<?=$CodFuente?>" />
<input type="hidden" name="Monto" id="Monto" value="<?=$Monto?>" />

<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
	<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
		<tr>
			<td align="right" width="150">Categoria:</td>
			<td>
				<input type="checkbox" <?=$cCategoria?> onclick="chkCampos(this.checked, 'fCategoria');" />
				<select name="fCategoria" id="fCategoria" style="width:250px;" <?=$dCategoria?>>
					<option value="">&nbsp;</option>
					<?=getMiscelaneos($fCategoria,'CATCERTIF')?>
				</select>
			</td>
			<td align="right" width="100">Estado: </td>
			<td>
	            <input type="checkbox" <?=$cEstado?> onclick="this.checked=!this.checked;" />
	            <select name="fEstado" id="fEstado" style="width:100px;" <?=$dEstado?>>
	                <?=loadSelectGeneral("ESTADO", $fEstado, 1)?>
	            </select>
			</td>
	        <td>&nbsp;</td>
		</tr>
		<tr>
			<td align="right">Buscar:</td>
			<td>
				<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
				<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:250px;" <?=$dBuscar?> />
			</td>
			<td align="right">Partida: </td>
			<td>
	            <input type="checkbox" <?=$ccod_partida?> onClick="ckLista(this.checked,['fcod_partida'],['btcod_partida'])" />
				<input type="text" name="fcod_partida" id="fcod_partida" value="<?=$fcod_partida?>" style="width:100px;" class="disabled" readonly />
				<a href="javascript:" onclick="window.open('gehen.php?anz=lista_partidas&filtrar=default&campo1=fcod_partida&fcod_tipocuenta=4&ventana=selListaOpener','lista_partidas','width=2000, height=2000, toolbar=no, menubar=no, location=no, scrollbars=yes, left=0, top=0, resizable=no')" style=" <?=$dcod_partida?>" id="btcod_partida">
              		<img src="../../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
	        </td>
	        <td align="right"><input type="submit" value="Buscar"></td>
		</tr>
	</table>
</div>
<div class="sep"></div>

<div class="scroll" style="overflow:scroll; height:315px; width:100%; min-width:<?=$_width?>px; margin:auto;">
	<table class="tblLista" style="width:100%; min-width:<?=$_width?>px;">
		<thead>
		    <tr>
		        <th width="75" onclick="order('CodConcepto')">C&oacute;digo</th>
		        <th align="left" onclick="order('Descripcion')">Descripci&oacute;n</th>
		        <th width="125" onclick="order('Categoria')">Categor&iacute;a</th>
		        <th width="75" onclick="order('cod_partida')">Partida</th>
		        <th width="100" onclick="order('CodCuentaPub20')">Cuenta (Pub.20)</th>
		        <th width="100" onclick="order('CodCuenta')">Cuenta</th>
		        <th width="75" onclick="order('Estado')">Estado</th>
		    </tr>
	    </thead>
	    
	    <tbody>
		<?php
		//	consulto todos
		$sql = "SELECT cc.*
				FROM
					ap_conceptoscertificacion cc
					LEFT JOIN mastmiscelaneosdet md ON (md.CodDetalle = cc.Categoria AND md.CodMaestro = 'CATCERTIF')
				WHERE 1 $filtro";
		$rows_total = getNumRows3($sql);
		//	consulto lista
		$sql = "SELECT
					cc.*,
					md.Descripcion AS NomCategoria
				FROM
					ap_conceptoscertificacion cc
					LEFT JOIN mastmiscelaneosdet md ON (md.CodDetalle = cc.Categoria AND md.CodMaestro = 'CATCERTIF')
				WHERE 1 $filtro
				ORDER BY $fOrderBy
				LIMIT ".intval($limit).", ".intval($maxlimit);
		$field = getRecords($sql);
		$rows_lista = count($field);
		foreach($field as $f) {
			$id = $f['CodConcepto'];
			##	
			if ($ventana == "listado_insertar_linea") {
				?><tr class="trListaBody" onClick="<?=$ventana?>('<?=$detalle?>','modulo=<?=$modulo?>&accion=<?=$accion?>&CodConcepto=<?=$f['CodConcepto']?>','<?=$f['CodConcepto']?>','<?=$url?>');"><?php
			}
			elseif ($ventana == "ap_certificaciones") {
				?><tr class="trListaBody" onClick="listado_insertar_linea('<?=$detalle?>','modulo=<?=$modulo?>&accion=<?=$accion?>&CodConcepto=<?=$f['CodConcepto']?>&CategoriaProg=<?=$CategoriaProg?>&CodPresupuesto=<?=$CodPresupuesto?>&Ejercicio=<?=$Ejercicio?>&CodFuente=<?=$CodFuente?>&Monto=<?=$Monto?>','<?=$f['CodConcepto']?>','<?=$url?>');"><?php
			}
			else {
				?><tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['CodConcepto']?>','<?=$f['Descripcion']?>'], ['<?=$campo1?>','<?=$campo2?>']);"><?php
			}
			?>
				<td align="center"><?=$f['CodConcepto']?></td>
				<td><?=htmlentities($f['Descripcion'])?></td>
				<td align="center"><?=htmlentities($f['NomCategoria'])?></td>
				<td align="center"><?=$f['cod_partida']?></td>
				<td><?=$f['CodCuentaPub20']?></td>
				<td><?=$f['CodCuenta']?></td>
				<td align="center"><?=printValoresGeneral('ESTADO',$f['Estado'])?></td>
			</tr>
			<?php
		}
		?>
	    </tbody>
	</table>
</div>
<table style="width:100%; min-width:<?=$_width?>px; margin:auto;">
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
</form>