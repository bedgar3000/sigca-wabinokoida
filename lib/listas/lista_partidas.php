<?php
if (!$ventana) $ventana = "selLista";
//	------------------------------------
if ($filtrar == "default") {
	$fEstado = "A";
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "cod_partida";

	$fBuscar = ($fBuscar?$fBuscar:$_SESSION["fBuscar"]);
	$fcod_tipocuenta = ($fcod_tipocuenta?$fcod_tipocuenta:$_SESSION["fcod_tipocuenta"]);
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (pv.cod_partida LIKE '%".$fBuscar."%' OR
					  pv.denominacion LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (pv.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fcod_tipocuenta != "") { $ccod_tipocuenta = "checked"; $filtro.=" AND (tc.cod_tipocuenta = '".$fcod_tipocuenta."')"; } else $dcod_tipocuenta = "disabled";
if ($FlagMetas == 'S') {
	$filtro .= " AND (pv.cod_partida >= '402.' AND pv.cod_partida <= '499.')";
}
if ($FlagObra == 'S') {
	$filtro .= " AND (pv.cod_partida LIKE '404.%')";
}
if ($FlagProyeccionNomina == 'S') {
	$filtro .= " AND (pv.cod_partida LIKE '401.%' OR pv.cod_partida LIKE '407.%' OR pv.cod_partida LIKE '411.%')";
}
##	
$_SESSION["fBuscar"] = $fBuscar;
$_SESSION["fcod_tipocuenta"] = $fcod_tipocuenta;
//	------------------------------------
$_titulo = "Clasificador Presupuestario";
$_width = 900;
?>
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lista_partidas" method="post" autocomplete="off">
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
<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
<input type="hidden" name="FlagTipoCuenta" id="FlagTipoCuenta" value="<?=$FlagTipoCuenta?>" />
<input type="hidden" name="FlagGenerar" id="FlagGenerar" value="<?=$FlagGenerar?>" />
<input type="hidden" name="FlagMetas" id="FlagMetas" value="<?=$FlagMetas?>" />
<input type="hidden" name="FlagObra" id="FlagObra" value="<?=$FlagObra?>" />
<input type="hidden" name="FlagProyeccionNomina" id="FlagProyeccionNomina" value="<?=$FlagProyeccionNomina?>" />
<input type="hidden" name="seldetalle" id="seldetalle" value="<?=$seldetalle?>" />
<input type="hidden" name="CategoriaProg" id="CategoriaProg" value="<?=$CategoriaProg?>" />
<input type="hidden" name="Ejercicio" id="Ejercicio" value="<?=$Ejercicio?>" />
<input type="hidden" name="CodOrganismo" id="CodOrganismo" value="<?=$CodOrganismo?>" />
<input type="hidden" name="CodPresupuesto" id="CodPresupuesto" value="<?=$CodPresupuesto?>" />
<input type="hidden" name="CodFuente" id="CodFuente" value="<?=$CodFuente?>" />
<input type="hidden" name="Monto" id="Monto" value="<?=$Monto?>" />

<div class="divBorder" style="width:100%; min-width:<?=$_width?>px; margin:auto;">
	<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
		<tr>
			<td align="right" width="125">Tipo de Cuenta:</td>
			<td>
				<?php
				if ($FlagTipoCuenta == 'S') {
					?>
					<input type="checkbox" <?=$ccod_tipocuenta?> onclick="this.checked=!this.checked;" />
		        	<select name="fcod_tipocuenta" id="fcod_tipocuenta" style="width:155px;" <?=$dcod_tipocuenta?>>
		                <?=loadSelect2('pv_tipocuenta','cod_tipocuenta','descp_tipocuenta',$fcod_tipocuenta,1)?>
		            </select>
					<?php
				} else {
					?>
					<input type="checkbox" <?=$ccod_tipocuenta?> onclick="chkCampos(this.checked, 'fcod_tipocuenta');" />
		        	<select name="fcod_tipocuenta" id="fcod_tipocuenta" style="width:155px;" <?=$dcod_tipocuenta?>>
		            	<option value="">&nbsp;</option>
		                <?=loadSelect2('pv_tipocuenta','cod_tipocuenta','descp_tipocuenta',$fcod_tipocuenta)?>
		            </select>
					<?php
				}
				?>
			</td>
			<td align="right" width="100">Buscar:</td>
			<td>
				<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
				<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:149px;" <?=$dBuscar?> />
			</td>
	        <td align="right"><input type="submit" value="Buscar"></td>
		</tr>
	</table>
</div>
<div class="sep"></div>

<div class="scroll" style="overflow:scroll; height:315px; width:100%; min-width:<?=$_width?>px; margin:auto;">
	<table class="tblLista" style="width:100%; min-width:1300px;">
		<thead>
		    <tr>
		        <th width="75" onclick="order('cod_partida')">C&oacute;digo</th>
		        <th align="left" onclick="order('denominacion')">Descripci&oacute;n</th>
		        <th width="90" onclick="order('descp_tipocuenta')">Tipo de Cuenta</th>
		        <th width="90" onclick="order('Estado')">Estado</th>
		    </tr>
	    </thead>
	    
	    <tbody>
		<?php
		//	consulto todos
		$sql = "SELECT pv.cod_partida
				FROM pv_partida pv
				INNER JOIN pv_tipocuenta tc ON (tc.cod_tipocuenta = pv.cod_tipocuenta)
				WHERE 1 $filtro";
		$rows_total = getNumRows3($sql);
		//	consulto lista
		$sql = "SELECT
					pv.*,
					tc.descp_tipocuenta
				FROM pv_partida pv
				INNER JOIN pv_tipocuenta tc ON (tc.cod_tipocuenta = pv.cod_tipocuenta)
				WHERE 1 $filtro
				ORDER BY $fOrderBy
				LIMIT ".intval($limit).", ".intval($maxlimit);
		$field = getRecords($sql);
		$rows_lista = count($field);
		foreach($field as $f) {
			$id = $f['cod_partida'];
			$idtr = $f['cod_tipocuenta'].$f['partida1'].$f['generica'].$f['especifica'].$f['subespecifica'];
			if ($f['tipo'] == 'T') {
				if ($f['partida1']=='00' && $f['generica']=='00' && $f['especifica']=='00' && $f['subespecifica']=='00') $background="background-color:#B6B6B6;";
				elseif ($f['generica']=='00' && $f['especifica']=='00' && $f['subespecifica']=='00') $background="background-color:#C7C7C7;";
				elseif ($f['especifica']=='00' && $f['subespecifica']=='00') $background="background-color:#DEDEDE;";
				else $background="";
				?>
	            <tr class="trListaBody" style="font-weight:bold; <?=$background?>">
	            <?php
			}
			else {
				if ($ventana == 'listado_insertar_linea') {
					?><tr class="trListaBody" onClick="<?=$ventana?>('<?=$detalle?>','modulo=<?=$modulo?>&accion=<?=$accion?>&cod_partida=<?=$f['cod_partida']?>','<?=$f['cod_partida']?>','<?=$url?>');"><?php
				}
				elseif ($ventana == 'ob_obras') {
					?><tr class="trListaBody" onClick="listado_insertar_linea('<?=$detalle?>','modulo=<?=$modulo?>&accion=<?=$accion?>&cod_partida=<?=$f['cod_partida']?>&CodOrganismo=<?=$CodOrganismo?>&CodPresupuesto=<?=$CodPresupuesto?>&CodFuente=<?=$CodFuente?>','<?=$f['cod_partida']?>','<?=$url?>');"><?php
				}
				elseif ($ventana == 'pv_formulacionmetas') {
					?><tr class="trListaBody" onClick="listado_insertar_linea('<?=$detalle?>','modulo=<?=$modulo?>&accion=<?=$accion?>&cod_partida=<?=$f['cod_partida']?>&detalle=<?=$detalle?>','<?=$f['cod_partida']?>','<?=$url?>');"><?php
				}
				elseif ($ventana == 'ap_certificaciones') {
					?><tr class="trListaBody" onClick="listado_insertar_linea('<?=$detalle?>','modulo=<?=$modulo?>&accion=<?=$accion?>&cod_partida=<?=$f['cod_partida']?>&detalle=<?=$detalle?>&CategoriaProg=<?=$CategoriaProg?>&CodPresupuesto=<?=$CodPresupuesto?>&Ejercicio=<?=$Ejercicio?>&CodFuente=<?=$CodFuente?>&Monto=<?=$Monto?>','<?=$f['cod_partida']?>','<?=$url?>');"><?php
				}
				elseif ($ventana == 'proyectopresupuesto_insertar' || $ventana == 'reformulacion_insertar') {
					?><tr class="trListaBody" onClick="<?=$ventana?>('<?=$detalle?>','modulo=<?=$modulo?>&accion=<?=$accion?>&cod_partida=<?=$f['cod_partida']?>','<?=$f['cod_partida']?>');"><?php
				} 
				elseif ($ventana == 'ha_presupuesto') {
					?><tr class="trListaBody" onClick="<?=$ventana?>('<?=$detalle?>','modulo=<?=$modulo?>&accion=<?=$accion?>&cod_partida=<?=$f['cod_partida']?>','<?=$f['cod_partida']?>', $('#<?=$idtr?>'));" id="<?=$idtr?>"><?php
				}  
				elseif ($ventana == 'pv_ajustes') {
					?><tr class="trListaBody" onClick="listado_insertar_linea('<?=$detalle?>','modulo=<?=$modulo?>&accion=<?=$accion?>&cod_partida=<?=$f['cod_partida']?>&CodOrganismo=<?=$CodOrganismo?>&CodPresupuesto=<?=$CodPresupuesto?>&detalle=<?=$detalle?>','<?=$f['cod_partida']?>','<?=$url?>');"><?php
				}
				elseif ($ventana == "selListadoListaParent") {
					?><tr class="trListaBody" onclick="selListadoListaParent('<?=$seldetalle?>',['<?=$campo1?>'],['<?=$f['cod_partida']?>']);" id="<?=$f['cod_partida']?>"><?php
				}
				elseif ($ventana == "selListaOpener") {
					?><tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['cod_partida']?>','<?=$f['denominacion']?>'], ['<?=$campo1?>','<?=$campo2?>']);"><?php
				}
				elseif ($ventana == "cuentas") {
					?><tr class="trListaBody" onClick="selLista(['<?=$f['cod_partida']?>','<?=$f['CodCuenta']?>','<?=$f['CodCuentaPub20']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>']);"><?php
				}
				else {
					?><tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['cod_partida']?>','<?=$f['denominacion']?>'], ['<?=$campo1?>','<?=$campo2?>']);"><?php
				}
			}
			?>
				<td align="center"><?=$f['cod_partida']?></td>
				<td><?=htmlentities($f['denominacion'])?></td>
				<td><?=htmlentities($f['descp_tipocuenta'])?></td>
				<td align="center"><?=printValoresGeneral("ESTADO", $f['Estado'])?></td>
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

<script type="text/javascript" language="javascript">
	<?php
	if ($ventana == 'proyectopresupuesto_insertar') {
		?>
		function proyectopresupuesto_insertar(detalle, data, id) {
			//	lista
			var nro_detalles = parent.$("#nro_"+detalle);
			var can_detalles = parent.$("#can_"+detalle);
			var lista_detalles = parent.$("#lista_"+detalle);
			var nro = new Number(nro_detalles.val());	nro++;
			var can = new Number(can_detalles.val());	can++;
			var idtr = detalle+"_"+id;
			//	ajax
			$.ajax({
				type: "POST",
				url: "../../pv/pv_proyectopresupuesto_ajax.php",
				data: "nro_detalles="+nro+"&can_detalles="+can+"&"+data+"&FlagGenerar="+$('#FlagGenerar').val(),
				async: false,
				success: function(resp) {
					var data = resp.split('|');
					//if (parent.document.getElementById(idtr)) cajaModal("Registro ya insertado", "error_lista", 400);
					//else {
						data[0] = data[0].trim();
						data[1] = data[1].trim();
						data[2] = data[2].trim();
						data[3] = data[3].trim();
						data[4] = data[4].trim();
						data[5] = data[5].trim();
						data[6] = data[6].trim();
						data[7] = data[7].trim();
						//	tipo decuenta
						if (!parent.document.getElementById(detalle+"_"+data[7])) {
							var flag = false;
							nro_detalles.val(nro);
							can_detalles.val(can);
							++nro; ++can;
							parent.$('#lista_partida tr').each(function() {
								if ($(this).attr('id') > 'partida_'+data[7]) {
									$(this).before(data[3]);
									flag = true;
									return false;
								}
							});
							if (!flag) lista_detalles.append(data[3]);
						}
						//	partida
						if (!parent.document.getElementById(detalle+"_"+data[6])) {
							var flag = false;
							nro_detalles.val(nro);
							can_detalles.val(can);
							++nro; ++can;
							parent.$('#lista_partida tr').each(function() {
								if ($(this).attr('id') > 'partida_'+data[6]) {
									$(this).before(data[2]);
									flag = true;
									return false;
								}
							});
							if (!flag) lista_detalles.append(data[2]);
						}
						//	generica
						if (!parent.document.getElementById(detalle+"_"+data[5])) {
							var flag = false;
							nro_detalles.val(nro);
							can_detalles.val(can);
							++nro; ++can;
							parent.$('#lista_partida tr').each(function() {
								if ($(this).attr('id') > 'partida_'+data[5]) {
									$(this).before(data[1]);
									flag = true;
									return false;
								}
							});
							if (!flag) lista_detalles.append(data[1]);
						}
						//	detalle
						var flag = false;
						nro_detalles.val(nro);
						can_detalles.val(can);
						parent.$('#lista_partida tr').each(function() {
							if ($(this).attr('id') > 'partida_'+data[4]) {
								$(this).before(data[0]);
								flag = true;
								return false;
							}
						});
						if (!flag) lista_detalles.append(data[0]);
						inicializarParent();
						//parent.$.prettyPhoto.close();
					//}
				}
			});
		}
		<?php
	}
	elseif ($ventana == 'reformulacion_insertar') {
		?>
		function reformulacion_insertar(detalle, data, id) {
			//	lista
			var nro_detalles = parent.$("#nro_"+detalle);
			var can_detalles = parent.$("#can_"+detalle);
			var lista_detalles = parent.$("#lista_"+detalle);
			var nro = new Number(nro_detalles.val());	nro++;
			var can = new Number(can_detalles.val());	can++;
			var idtr = detalle+"_"+id;
			//	ajax
			$.ajax({
				type: "POST",
				url: "../../pv/pv_reformulacion_ajax.php",
				data: "nro_detalles="+nro+"&can_detalles="+can+"&"+data,
				async: false,
				success: function(resp) {
					var data = resp.split('|');
					if (parent.document.getElementById(idtr)) cajaModal("Registro ya insertado", "error_lista", 400);
					else {
						data[0] = data[0].trim();
						data[1] = data[1].trim();
						data[2] = data[2].trim();
						data[3] = data[3].trim();
						data[4] = data[4].trim();
						data[5] = data[5].trim();
						data[6] = data[6].trim();
						data[7] = data[7].trim();
						//	tipo decuenta
						if (!parent.document.getElementById(detalle+"_"+data[7])) {
							var flag = false;
							nro_detalles.val(nro);
							can_detalles.val(can);
							++nro; ++can;
							parent.$('#lista_partida tr').each(function() {
								if ($(this).attr('id') > 'partida_'+data[7]) {
									$(this).before(data[3]);
									flag = true;
									return false;
								}
							});
							if (!flag) lista_detalles.append(data[3]);
						}
						//	partida
						if (!parent.document.getElementById(detalle+"_"+data[6])) {
							var flag = false;
							nro_detalles.val(nro);
							can_detalles.val(can);
							++nro; ++can;
							parent.$('#lista_partida tr').each(function() {
								if ($(this).attr('id') > 'partida_'+data[6]) {
									$(this).before(data[2]);
									flag = true;
									return false;
								}
							});
							if (!flag) lista_detalles.append(data[2]);
						}
						//	generica
						if (!parent.document.getElementById(detalle+"_"+data[5])) {
							var flag = false;
							nro_detalles.val(nro);
							can_detalles.val(can);
							++nro; ++can;
							parent.$('#lista_partida tr').each(function() {
								if ($(this).attr('id') > 'partida_'+data[5]) {
									$(this).before(data[1]);
									flag = true;
									return false;
								}
							});
							if (!flag) lista_detalles.append(data[1]);
						}
						//	detalle
						var flag = false;
						nro_detalles.val(nro);
						can_detalles.val(can);
						parent.$('#lista_partida tr').each(function() {
							if ($(this).attr('id') > 'partida_'+data[4]) {
								$(this).before(data[0]);
								flag = true;
								return false;
							}
						});
						if (!flag) lista_detalles.append(data[0]);
						inicializarParent();
						//parent.$.prettyPhoto.close();
					}
				}
			});
		}
		<?php
	}
	elseif ($ventana == 'ha_presupuesto') {
		?>
		function ha_presupuesto(detalle, data, id, tr) {
			//	lista
			var nro_detalles = parent.$("#nro_"+detalle);
			var can_detalles = parent.$("#can_"+detalle);
			var lista_detalles = parent.$("#lista_"+detalle);
			var nro = new Number(nro_detalles.val());	nro++;
			var can = new Number(can_detalles.val());	can++;
			var idtr = detalle+"_"+id;
			//	ajax
			$.ajax({
				type: "POST",
				url: "../../ha/ha_presupuesto_ajax.php",
				data: "nro_detalles="+nro+"&can_detalles="+can+"&"+data,
				async: false,
				success: function(resp) {
					var data = resp.split('|');
					if (parent.document.getElementById(idtr)) cajaModal("Registro ya insertado", "error_lista", 400);
					else {
						data[0] = data[0].trim();
						data[1] = data[1].trim();
						data[2] = data[2].trim();
						data[3] = data[3].trim();
						data[4] = data[4].trim();
						data[5] = data[5].trim();
						data[6] = data[6].trim();
						data[7] = data[7].trim();
						//	tipo decuenta
						if (!parent.document.getElementById(detalle+"_"+data[7])) {
							var flag = false;
							nro_detalles.val(nro);
							can_detalles.val(can);
							++nro; ++can;
							parent.$('#lista_partida tr').each(function() {
								if ($(this).attr('id') > 'partida_'+data[7]) {
									$(this).before(data[3]);
									flag = true;
									return false;
								}
							});
							if (!flag) lista_detalles.append(data[3]);
						}
						//	partida
						if (!parent.document.getElementById(detalle+"_"+data[6])) {
							var flag = false;
							nro_detalles.val(nro);
							can_detalles.val(can);
							++nro; ++can;
							parent.$('#lista_partida tr').each(function() {
								if ($(this).attr('id') > 'partida_'+data[6]) {
									$(this).before(data[2]);
									flag = true;
									return false;
								}
							});
							if (!flag) lista_detalles.append(data[2]);
						}
						//	generica
						if (!parent.document.getElementById(detalle+"_"+data[5])) {
							var flag = false;
							nro_detalles.val(nro);
							can_detalles.val(can);
							++nro; ++can;
							parent.$('#lista_partida tr').each(function() {
								if ($(this).attr('id') > 'partida_'+data[5]) {
									$(this).before(data[1]);
									flag = true;
									return false;
								}
							});
							if (!flag) lista_detalles.append(data[1]);
						}
						//	detalle
						var flag = false;
						nro_detalles.val(nro);
						can_detalles.val(can);
						parent.$('#lista_partida tr').each(function() {
							if ($(this).attr('id') > 'partida_'+data[4]) {
								$(this).before(data[0]);
								flag = true;
								return false;
							}
						});
						if (!flag) lista_detalles.append(data[0]);
						inicializarParent();
						tr.remove();
						//parent.$.prettyPhoto.close();
					}
				}
			});
		}
		<?php
	}
	?>
</script>