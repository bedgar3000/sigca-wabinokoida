<?php
if (!$ventana) $ventana = "selLista";
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$sql = "SELECT MAX(Ejercicio) FROM ob_planobras";
	$Ejercicio = getVar3($sql);
	$fEjercicio = ($Ejercicio?$AnioActual:$AnioActual);
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "CodPlanObra";
}
$_SESSION["fCodOrganismo"] = $fCodOrganismo;
//	------------------------------------
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (po.CategoriaProg LIKE '%".$fBuscar."%' OR
					  po.Ejercicio LIKE '%".$fBuscar."%' OR
					  po.Denominacion LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (po.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (d.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fCategoriaProg != "") { $cCategoriaProg = "checked"; $filtro.=" AND (po.CategoriaProg = '".$fCategoriaProg."')"; } else $dCategoriaProg = "visibility:hidden;";
if ($fFechaInicio != "") { 
	$cFecha = "checked"; 
	if ($fFechaInicio != "") $filtro.=" AND ('".formatFechaAMD($fFechaInicio)."' >= po.FechaInicio AND '".formatFechaAMD($fFechaInicio)."' <= po.FechaFin)";
	if ($fFechaFin != "") $filtro.=" AND ('".formatFechaAMD($fFechaFin)."' >= po.FechaInicio AND '".formatFechaAMD($fFechaFin)."' <= po.FechaFin)";
} else $dFecha = "disabled";
if ($fEjercicio != "") { $cEjercicio = "checked"; $filtro.=" AND (po.Ejercicio = '".$fEjercicio."')"; } else $dEjercicio = "disabled";
if ($fTipoObra != "") { $cTipoObra = "checked"; $filtro.=" AND (po.TipoObra = '".$fTipoObra."')"; } else $dTipoObra = "disabled";
if ($fSituacion != "") { $cSituacion = "checked"; $filtro.=" AND (po.Situacion = '".$fSituacion."')"; } else $dSituacion = "disabled";
if ($fCodResponsable != "") { $cCodResponsable = "checked"; $filtro.=" AND (po.CodResponsable = '".$fCodResponsable."')"; } else $dCodResponsable = "visibility:hidden;";
//	------------------------------------
$_titulo = "Plan de Obras";
$_width = 900;
?>
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lista_ob_planobras" method="post">
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

<!--FILTRO-->
<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
	<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
		<tr>
			<td align="right">Organismo: </td>
			<td>
	            <input type="checkbox" <?=$cCodOrganismo?> onclick="this.checked=!this.checked;" />
	            <select name="fCodOrganismo" id="fCodOrganismo" style="width:242px;" <?=$dCodOrganismo?> onchange="$('#aCategoriaProg').attr('href','../lib/listas/gehen.php?anz=lista_pv_categoriaprog&filtrar=default&ventana=pv_metas&campo1=fCategoriaProg&FlagOrganismo=S&fCodOrganismo='+$(this).val()+'&iframe=true&width=100%&height=100%');">
	                <?=loadSelect2('mastorganismos','CodOrganismo','Organismo',$fCodOrganismo)?>
	            </select>
			</td>
			<td align="right">Estado: </td>
			<td>
				<?php
				if ($lista == "listar") {
					?>
		            <input type="checkbox" <?=$cEstado?> onclick="chkFiltro(this.checked, 'fEstado');" />
		            <select name="fEstado" id="fEstado" style="width:110px;" <?=$dEstado?>>
		                <option value="">&nbsp;</option>
		                <?=loadSelectGeneral("plan-obras-estado", $fEstado, 0)?>
		            </select>
					<?php
				} else {
					?>
		            <input type="checkbox" <?=$cEstado?> onclick="this.checked=!this.checked;" />
		            <select name="fEstado" id="fEstado" style="width:110px;" <?=$dEstado?>>
		                <?=loadSelectGeneral("plan-obras-estado", $fEstado, 1)?>
		            </select>
					<?php
				}
				?>
			</td>
			<td align="right">Buscar: </td>
			<td>
				<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
				<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:242px;" <?=$dBuscar?> />
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td align="right">Categoria Prog.:</td>
			<td>
				<input type="checkbox" <?=$cCategoriaProg?> onclick="ckLista(this.checked, ['fCategoriaProg'], ['aCategoriaProg']);" />
				<input type="text" name="fCategoriaProg" id="fCategoriaProg" value="<?=$fCategoriaProg?>" style="width:133px;" readonly="readonly" />
				<a href="javascript:" id="aCategoriaProg" style=" <?=$dCategoriaProg?>" onclick="window.open('gehen.php?anz=lista_pv_categoriaprog&filtrar=default&ventana=selListaOpener&campo1=fCategoriaProg&FlagOrganismo=S&fCodOrganismo=<?=$fCodOrganismo?>', 'lista_ob_planobras', 'toolbar=no, menubar=no, location=no, scrollbars=yes, width=5000, height=5000');">
	            	<img src="../../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
			<td align="right">Tipo de Obra: </td>
			<td>
	            <input type="checkbox" <?=$cTipoObra?> onclick="chkFiltro(this.checked, 'fTipoObra');" />
	            <select name="fTipoObra" id="fTipoObra" style="width:110px;" <?=$dTipoObra?>>
	                <option value="">&nbsp;</option>
	                <?=loadSelectGeneral('plan-obras-tipo',$fTipoObra)?>
	            </select>
			</td>
			<td align="right">Responsable:</td>
			<td>
				<input type="checkbox" <?=$cCodResponsable?> onclick="ckLista(this.checked, ['fCodResponsable','fCodEmpleado','fNomPersona'], ['aCodResponsable']);" />
	            <input type="hidden" name="fCodResponsable" id="fCodResponsable" value="<?=$fCodResponsable?>" />
				<input type="text" name="fCodEmpleado" id="fCodEmpleado" value="<?=$fCodEmpleado?>" style="width:50px;" readonly />
				<input type="text" name="fNomPersona" id="fNomPersona" value="<?=$fNomPersona?>" style="width:189px;" readonly />
				<a href="javascript:" id="aCodResponsable" style=" <?=$dCodResponsable?>" onclick="window.open('gehen.php?anz=lista_empleados&filtrar=default&ventana=selListaOpener&campo1=fCodResponsable&campo2=fNomPersona&campo3=fCodEmpleado&iframe=true&width=100%&height=100%', 'lista_empleados', 'toolbar=no, menubar=no, location=no, scrollbars=yes, width=5000, height=5000');">
					<img src="../../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
				</a>
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td align="right">Fecha: </td>
			<td>
				<input type="checkbox" <?=$cFecha?> onclick="chkCampos2(this.checked, ['fFechaInicio','fFechaFin']);" />
				<input type="text" name="fFechaInicio" id="fFechaInicio" value="<?=$fFechaInicio?>" style="width:65px;" maxlength="10" class="datepicker" <?=$dFecha?> />
				<input type="text" name="fFechaFin" id="fFechaFin" value="<?=$fFechaFin?>" style="width:65px;" maxlength="10" class="datepicker" <?=$dFecha?> />
			</td>
			<td align="right">Situaci&oacute;n: </td>
			<td>
	            <input type="checkbox" <?=$cSituacion?> onclick="chkFiltro(this.checked, 'fSituacion');" />
	            <select name="fSituacion" id="fSituacion" style="width:110px;" <?=$dSituacion?>>
	                <option value="">&nbsp;</option>
	                <?=loadSelectGeneral('plan-obras-situacion',$fSituacion)?>
	            </select>
			</td>
			<td align="right">Ejercicio: </td>
			<td>
				<input type="checkbox" <?=$cEjercicio?> onclick="chkCampos(this.checked, 'fEjercicio');" />
				<input type="text" name="fEjercicio" id="fEjercicio" value="<?=$fEjercicio?>" style="width:50px;" maxlength="4" <?=$dEjercicio?> />
			</td>
	        <td align="right"><input type="submit" value="Buscar"></td>
		</tr>
	</table>
</div>
<div class="sep"></div>

<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px; margin:auto;">
	<table class="tblLista" style="width:100%; min-width:1400px;">
		<thead>
		    <tr>
		        <th width="100" onclick="order('CodPlanObra')">C&oacute;digo</th>
		        <th width="75" onclick="order('Ejercicio')">Ejercicio</th>
		        <th width="125" onclick="order('CategoriaProg')">Cat. Prog.</th>
		        <th width="100" onclick="order('CodInterno')">N&uacute;mero</th>
		        <th align="left" onclick="order('Denominacion')">Denominaci&oacute;n</th>
		        <th width="65" onclick="order('FechaInicio')">Inicio</th>
		        <th width="65" onclick="order('FechaFin')">T&eacute;rmino</th>
		        <th width="100" onclick="order('Situacion')">Situaci&oacute;n</th>
		        <th width="100" onclick="order('Estado')">Estado</th>
		    </tr>
	    </thead>
	    
	    <tbody>
		<?php
		//	consulto todos
		$sql = "SELECT po.*
				FROM
					ob_planobras po
					INNER JOIN mastdependencias d ON (d.CodDependencia = po.CodDependencia)
					INNER JOIN mastorganismos o ON (o.CodOrganismo = d.CodOrganismo)
				WHERE 1 $filtro";
		$rows_total = getNumRows3($sql);
		//	consulto lista
		$sql = "SELECT
					po.*,
					d.CodOrganismo,
					o.Organismo
				FROM
					ob_planobras po
					INNER JOIN mastdependencias d ON (d.CodDependencia = po.CodDependencia)
					INNER JOIN mastorganismos o ON (o.CodOrganismo = d.CodOrganismo)
				WHERE 1 $filtro
				ORDER BY $fOrderBy
				LIMIT ".intval($limit).", ".intval($maxlimit);
		$field = getRecords($sql);
		$rows_lista = count($field);
		foreach($field as $f) {
			$id = $f['CodPlanObra'];
			if ($ventana == "pv_presupuestoobra") {
				?><tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['CodPlanObra']?>','<?=$f['CodOrganismo']?>','<?=$f['CategoriaProg']?>','<?=$f['Ejercicio']?>','<?=$f['Organismo']?>','<?=$f['Denominacion']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>','<?=$campo4?>','<?=$campo5?>','<?=$campo6?>']);"><?php
			}
			elseif ($ventana == "selListadoListaParent") {
				?><tr class="trListaBody" onclick="selListadoListaParent('<?=$seldetalle?>',['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>'],['<?=$f['CodPlanObra']?>','<?=$f['CategoriaProg']?>','<?=$f['Ejercicio']?>']);" id="<?=$f['CodPlanObra']?>"><?php
			}
			else {
				?><tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['CodPlanObra']?>'], ['<?=$campo1?>']);"><?php
			}
			?>
				<td align="center"><?=$f['CodPlanObra']?></td>
				<td align="center"><?=$f['Ejercicio']?></td>
				<td align="center"><?=$f['CategoriaProg']?></td>
				<td align="center"><?=$f['CodInterno']?></td>
				<td><?=htmlentities($f['Denominacion'])?></td>
				<td align="center"><?=formatFechaDMA($f['FechaInicio'])?></td>
				<td align="center"><?=formatFechaDMA($f['FechaFin'])?></td>
				<td align="center"><?=printValoresGeneral('plan-obras-situacion',$f['Situacion'])?></td>
				<td align="center"><?=printValoresGeneral('plan-obras-estado',$f['Estado'])?></td>
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
	<?php
	if ($ventana == 'pv_presupuestoobra') {
		?>
		function pv_presupuestoobra(valores, inputs) {
			//	ajax
			$.ajax({
				type: "POST",
				url: "../../pv/pv_presupuestoobra_ajax.php",
				data: "modulo=ajax&accion=getMontoAprobado&CodOrganismo="+valores[1]+"&CategoriaProg="+valores[2]+"&Ejercicio="+valores[3],
				async: false,
				dataType: "json",
				success: function(data) {
					if (inputs) {
						for(var i=0; i<inputs.length; i++) {
							if (parent.$("#"+inputs[i]).length > 0) parent.$("#"+inputs[i]).val(valores[i]);
						}
					}
					var TotalGeneral = setNumero(parent.$('#TotalGeneral').val());
					var MontoDistribuido = data['MontoDistribuido'] + TotalGeneral;
					parent.$('#MontoAprobado').val(data['MontoAprobado']).formatCurrency();
					parent.$('#MontoDistribuido').val(MontoDistribuido).formatCurrency();
					var MontoDistribuidoInicial = data['MontoDistribuido'] - TotalGeneral;
					parent.$('#MontoDistribuidoInicial').val(MontoDistribuidoInicial);
					var TotalResta = data['MontoAprobado'] - MontoDistribuido;
					parent.$('#TotalResta').val(TotalResta).formatCurrency();

					parent.$.prettyPhoto.close();
				}
			});
		}
		<?php
	}
	?>
</script>