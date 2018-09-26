<?php
if (!$ventana) $ventana = "selLista";
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$fEjercicio = $AnioActual;
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "CodPresupuesto";
}
$_SESSION["fCodOrganismo"] = $fCodOrganismo;
//	------------------------------------
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (pp.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fCodDependencia != "") { $cCodDependencia = "checked"; $filtro.=" AND (pp.CodDependencia = '".$fCodDependencia."')"; } else $dCodDependencia = "disabled";
if ($fCodUnidadEjec != "") { $cCodUnidadEjec = "checked"; $filtro.=" AND (pp.CodUnidadEjec = '".$fCodUnidadEjec."')"; } else $dCodUnidadEjec = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (pp.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fEjercicio != "") { $cEjercicio = "checked"; $filtro.=" AND (pp.Ejercicio = '".$fEjercicio."')"; } else $dEjercicio = "disabled";
//	------------------------------------
$_titulo = "Formulaci&oacute;n Presupuestaria";
$_width = 900;
?>
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lista_ha_presupuesto" method="post">
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
<input type="hidden" name="FlagOrganismo" id="FlagOrganismo" value="<?=$FlagOrganismo?>" />

<!--FILTRO-->
<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
	<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
		<tr>
			<td align="right" width="125">Organismo:</td>
			<td>
				<input type="checkbox" <?=$cCodOrganismo?> onclick="this.checked=!this.checked;" />
				<?php
				if ($FlagOrganismo == 'S') {
					?>
					<select name="fCodOrganismo" id="fCodOrganismo" style="width:225px;" <?=$dCodOrganismo?>>
						<?=loadSelect2('mastorganismos','CodOrganismo','Organismo',$fCodOrganismo,1);?>
					</select>
					<?php
				} else {
					?>
					<select name="fCodOrganismo" id="fCodOrganismo" style="width:225px;" <?=$dCodOrganismo?> onChange="loadSelect($('#fCodUnidadEjec'), 'tabla=pv_unidadejecutora&CodOrganismo='+$(this).val(), 1); loadSelect($('#fCodDependencia'), 'tabla=dependencia_filtro&opcion='+$(this).val(), 1);">
						<?=getOrganismos($fCodOrganismo, 3);?>
					</select>
					<?php
				}
				?>
			</td>
			<td align="right">Estado: </td>
			<td>
				<?php
				if ($lista == 'listar') {
					?>
		            <input type="checkbox" <?=$cEstado?> onclick="chkFiltro(this.checked, 'fEstado');" />
		            <select name="fEstado" id="fEstado" style="width:100px;" <?=$dEstado?>>
		                <option value="">&nbsp;</option>
		                <?=loadSelectGeneral("presupuesto-hacienda-estado", $fEstado, 0)?>
		            </select>
					<?php
				} else {
					?>
		            <input type="checkbox" <?=$cEstado?> onclick="this.checked=!this.checked;" />
		            <select name="fEstado" id="fEstado" style="width:100px;" <?=$dEstado?>>
		                <?=loadSelectGeneral("presupuesto-hacienda-estado", $fEstado, 1)?>
		            </select>
					<?php
				}
				?>
			</td>
	        <td>&nbsp;</td>
		</tr>
		<tr>
			<td align="right">Dependencia: </td>
			<td>
	            <input type="checkbox" <?=$cCodDependencia?> onclick="chkFiltro(this.checked, 'fCodDependencia');" />
				<select name="fCodDependencia" id="fCodDependencia" style="width:225px;" <?=$dCodDependencia?>>
					<option value="">&nbsp;</option>
					<?=getDependencias($fCodDependencia, $fCodOrganismo, 0);?>
				</select>
			</td>
			<td align="right">Ejercicio:</td>
			<td>
				<input type="checkbox" <?=$cEjercicio?> onclick="chkCampos(this.checked, 'fEjercicio');" />
				<input type="text" name="fEjercicio" id="fEjercicio" value="<?=$fEjercicio?>" style="width:47px;" maxlength="4" <?=$dEjercicio?> />
			</td>
	        <td>&nbsp;</td>
		</tr>
		<tr>
			<td align="right">Unidad Ejecutora: </td>
			<td>
	            <input type="checkbox" <?=$cCodUnidadEjec?> onclick="chkFiltro(this.checked, 'fCodUnidadEjec');" />
				<select name="fCodUnidadEjec" id="fCodUnidadEjec" style="width:225px;" <?=$dCodUnidadEjec?>>
					<option value="">&nbsp;</option>
					<?=loadSelect2('pv_unidadejecutora','CodUnidadEjec','Denominacion',$fCodUnidadEjec,10,['CodOrganismo'],[$fCodOrganismo]);?>
				</select>
			</td>
	        <td>&nbsp;</td>
	        <td>&nbsp;</td>
	        <td align="right"><input type="submit" value="Buscar"></td>
		</tr>
	</table>
</div>
<div class="sep"></div>

<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px; margin:auto;">
	<table class="tblLista" style="width:100%; min-width:1400px;">
		<thead>
		    <tr>
		        <th width="75" onclick="order('CodPresupuesto')">C&oacute;digo</th>
		        <th align="left" onclick="order('Dependencia')">Dependencia</th>
		        <th align="left" onclick="order('UnidadEjecutora')">Unidad Ejecutora</th>
		        <th width="75" onclick="order('Ejercicio')">Ejercicio</th>
		        <th width="100" align="right" onclick="order('MontoProyecto')">Monto Total</th>
		        <th width="100" onclick="order('Estado')">Estado</th>
		    </tr>
	    </thead>
	    
	    <tbody>
		<?php
		//	consulto todos
		$sql = "SELECT pp.*
				FROM
					ha_presupuesto pp
					INNER JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = pp.CodUnidadEjec)
					INNER JOIN mastdependencias d ON (d.CodDependencia = pp.CodDependencia)
				WHERE 1 $filtro
				GROUP BY CodOrganismo, CodPresupuesto";
		$rows_total = getNumRows3($sql);
		//	consulto lista
		$sql = "SELECT
					pp.*,
					ue.Denominacion AS UnidadEjecutora,
					d.Dependencia
				FROM
					ha_presupuesto pp
					INNER JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = pp.CodUnidadEjec)
					INNER JOIN mastdependencias d ON (d.CodDependencia = pp.CodDependencia)
				WHERE 1 $filtro
				GROUP BY CodOrganismo, CodPresupuesto
				ORDER BY $fOrderBy
				LIMIT ".intval($limit).", ".intval($maxlimit);
		$field = getRecords($sql);
		$rows_lista = count($field);
		foreach($field as $f) {
			$id = $f['CodOrganismo'].'_'.$f['CodPresupuesto'];
			if ($ventana == 'ejercicio') {
				?><tr class="trListaBody" onClick="selLista(['<?=$f['CodOrganismo']?>','<?=$f['CodPresupuesto']?>','<?=$f['Ejercicio']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>']);"><?php
			}
			else {
				?><tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['CodOrganismo']?>','<?=$f['CodPresupuesto']?>'], ['<?=$campo1?>','<?=$campo2?>']);"><?php
			}
			?>
				<td align="center"><?=$f['CodPresupuesto']?></td>
				<td><?=htmlentities($f['Dependencia'])?></td>
				<td><?=htmlentities($f['UnidadEjecutora'])?></td>
				<td align="center"><?=$f['Ejercicio']?></td>
				<td align="right"><?=number_format($f['MontoProyecto'],2,',','.')?></td>
				<td align="center"><?=printValoresGeneral('presupuesto-hacienda-estado',$f['Estado'])?></td>
			</tr>
			<?php
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
</form>

<script type="text/javascript" language="javascript">
</script>