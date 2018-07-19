<?php
if (!$ventana) $ventana = "selLista";
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "CodObra";
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (o.CategoriaProg LIKE '%".$fBuscar."%' OR
					  ppto.Ejercicio LIKE '%".$fBuscar."%' OR
					  o.Nombre LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (o.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (d.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fCategoriaProg != "") { $cCategoriaProg = "checked"; $filtro.=" AND (ppto.CategoriaProg = '".$fCategoriaProg."')"; } else $dCategoriaProg = "visibility:hidden;";
if ($fFechaInicio != "") { 
	$cFecha = "checked"; 
	if ($fFechaInicio != "") $filtro.=" AND ('".formatFechaAMD($fFechaInicio)."' >= o.FechaInicio AND '".formatFechaAMD($fFechaInicio)."' <= o.FechaFin)";
	if ($fFechaFin != "") $filtro.=" AND ('".formatFechaAMD($fFechaFin)."' >= o.FechaInicio AND '".formatFechaAMD($fFechaFin)."' <= o.FechaFin)";
} else $dFecha = "disabled";
if ($fEjercicio != "") { $cEjercicio = "checked"; $filtro.=" AND (ppto.Ejercicio = '".$fEjercicio."')"; } else $dEjercicio = "disabled";
if ($fCodDependencia != "") { $cCodDependencia = "checked"; $filtro.=" AND (o.CodDependencia = '".$fCodDependencia."')"; } else $dCodDependencia = "disabled";
if ($fSituacion != "") { $cSituacion = "checked"; $filtro.=" AND (o.Situacion = '".$fSituacion."')"; } else $dSituacion = "disabled";
if ($fCodResponsable != "") { $cCodResponsable = "checked"; $filtro.=" AND (o.CodResponsable = '".$fCodResponsable."')"; } else $dCodResponsable = "visibility:hidden;";
//	------------------------------------
$_width = 900;
?>
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lista_ob_obras" method="post">
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
			<td align="right" width="125">Organismo:</td>
			<td>
				<input type="checkbox" <?=$cCodOrganismo?> onclick="this.checked=!this.checked;" onChange="loadSelect($('#fCodDependencia'), 'tabla=dependencia_filtro&opcion='+$(this).val(), 1); $('#aCategoriaProg').attr('href', '../lib/listas/gehen.php?anz=lista_pv_categoriaprog&filtrar=default&ventana=pv_metas&campo1=fCategoriaProg&FlagOrganismo=S&fCodOrganismo='+this.value+'&iframe=true&width=100%&height=100%'); $('#fCategoriaProg').val('');" />
				<select name="fCodOrganismo" id="fCodOrganismo" style="width:225px;" <?=$dCodOrganismo?> onChange="loadSelect($('#fCodDependencia'), 'tabla=dependencia_filtro&opcion='+$(this).val(), 1); $('#aCategoriaProg').attr('href', '../lib/listas/gehen.php?anz=lista_pv_categoriaprog&filtrar=default&ventana=pv_metas&campo1=fCategoriaProg&FlagOrganismo=S&fCodOrganismo='+this.value+'&iframe=true&width=100%&height=100%'); $('#fCategoriaProg').val('');">
					<?=getOrganismos($fCodOrganismo, 3);?>
				</select>
			</td>
			<td align="right">Estado: </td>
			<td>
	            <input type="checkbox" <?=$cEstado?> onclick="this.checked=!this.checked;" />
	            <select name="fEstado" id="fEstado" style="width:110px;" <?=$dEstado?>>
	                <?=loadSelectGeneral("obras-estado", $fEstado, 1)?>
	            </select>
			</td>
			<td align="right">Buscar: </td>
			<td>
				<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
				<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:242px;" <?=$dBuscar?> />
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td align="right">Dependencia: </td>
			<td>
	            <input type="checkbox" <?=$cCodDependencia?> onclick="chkFiltro(this.checked, 'fCodDependencia'); $('#fCategoriaProg').val('');" />
				<select name="fCodDependencia" id="fCodDependencia" style="width:225px;" <?=$dCodDependencia?> onchange="$('#aCategoriaProg').attr('href', '../lib/listas/gehen.php?anz=lista_pv_categoriaprog&filtrar=default&ventana=pv_metas&campo1=fCategoriaProg&FlagOrganismo=S&fCodOrganismo=<?=$fCodOrganismo?>&FlagDependencia=S&fCodDependencia='+this.value+'&iframe=true&width=100%&height=100%'); $('#fCategoriaProg').val('');">
					<option value="">&nbsp;</option>
					<?=getDependencias($fCodDependencia, $fCodOrganismo, 0);?>
				</select>
			</td>
			<td align="right">Situaci&oacute;n: </td>
			<td>
	            <input type="checkbox" <?=$cSituacion?> onclick="chkFiltro(this.checked, 'fSituacion');" />
	            <select name="fSituacion" id="fSituacion" style="width:133px;" <?=$dSituacion?>>
	                <option value="">&nbsp;</option>
	                <?=loadSelectGeneral('obras-situacion',$fSituacion)?>
	            </select>
			</td>
			<td align="right">Responsable:</td>
			<td class="gallery clearfix">
				<input type="checkbox" <?=$cCodResponsable?> onclick="ckLista(this.checked, ['fCodResponsable','fCodEmpleado','fNomPersona'], ['aCodResponsable']);" />
	            <input type="hidden" name="fCodResponsable" id="fCodResponsable" value="<?=$fCodResponsable?>" />
				<input type="text" name="fCodEmpleado" id="fCodEmpleado" value="<?=$fCodEmpleado?>" style="width:50px;" readonly />
				<input type="text" name="fNomPersona" id="fNomPersona" value="<?=$fNomPersona?>" style="width:189px;" readonly />
				<a href="../lib/listas/gehen.php?anz=lista_empleados&filtrar=default&campo1=fCodResponsable&campo2=fNomPersona&campo3=fCodEmpleado&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe2]" id="aCodResponsable" style=" <?=$dCodResponsable?>">
					<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
				</a>
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td align="right">Categoria Prog.:</td>
			<td class="gallery clearfix">
				<input type="checkbox" <?=$cCategoriaProg?> onclick="ckLista(this.checked, ['fCategoriaProg'], ['aCategoriaProg']);" />
				<input type="text" name="fCategoriaProg" id="fCategoriaProg" value="<?=$fCategoriaProg?>" style="width:133px;" readonly="readonly" />
				<a href="../lib/listas/gehen.php?anz=lista_pv_categoriaprog&filtrar=default&ventana=pv_metas&campo1=fCategoriaProg&FlagOrganismo=S&fCodOrganismo=<?=$fCodOrganismo?>&FlagDependencia=S&fCodDependencia=<?=$fCodDependencia?>&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" id="aCategoriaProg" style=" <?=$dCategoriaProg?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
			<td align="right">Fecha: </td>
			<td>
				<input type="checkbox" <?=$cFecha?> onclick="chkCampos2(this.checked, ['fFechaInicio','fFechaFin']);" />
				<input type="text" name="fFechaInicio" id="fFechaInicio" value="<?=$fFechaInicio?>" style="width:65px;" maxlength="10" class="datepicker" <?=$dFecha?> />
				<input type="text" name="fFechaFin" id="fFechaFin" value="<?=$fFechaFin?>" style="width:65px;" maxlength="10" class="datepicker" <?=$dFecha?> />
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
		        <th width="60" onclick="order('Ejercicio')">Ejercicio</th>
		        <th width="125" onclick="order('CategoriaProg')">Cat. Prog.</th>
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
		$sql = "SELECT o.*
				FROM
					ob_obras o
					INNER JOIN mastdependencias d ON (d.CodDependencia = o.CodDependencia)
					INNER JOIN pv_presupuesto ppto ON (ppto.CodOrganismo = o.CodOrganismo AND ppto.CodPresupuesto = o.CodPresupuesto)
					INNER JOIN pv_categoriaprog cp ON (cp.CategoriaProg = ppto.CategoriaProg)
					INNER JOIN mastpersonas p ON (p.CodPersona = o.CodProveedor)
				WHERE 1 $filtro";
		$rows_total = getNumRows3($sql);
		//	consulto lista
		$sql = "SELECT
					o.*,
					ppto.Ejercicio,
					ppto.CategoriaProg,
					cp.CodUnidadEjec,
					p.NomCompleto AS NomProveedor
				FROM
					ob_obras o
					INNER JOIN mastdependencias d ON (d.CodDependencia = o.CodDependencia)
					INNER JOIN pv_presupuesto ppto ON (ppto.CodOrganismo = o.CodOrganismo AND ppto.CodPresupuesto = o.CodPresupuesto)
					INNER JOIN pv_categoriaprog cp ON (cp.CategoriaProg = ppto.CategoriaProg)
					INNER JOIN mastpersonas p ON (p.CodPersona = o.CodProveedor)
				WHERE 1 $filtro
				ORDER BY $fOrderBy
				LIMIT ".intval($limit).", ".intval($maxlimit);
		$field = getRecords($sql);
		$rows_lista = count($field);
		foreach($field as $f) {
			$id = $f['CodObra'];
			if ($ventana == "getObraValuacion") {
				?><tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['CodObra']?>']);"><?php
			}
			else {
				?><tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['CodObra']?>'], ['<?=$campo1?>']);"><?php
			}
			?>
				<td align="center"><?=$f['Ejercicio']?></td>
				<td align="center"><?=$f['CategoriaProg']?></td>
				<td><?=htmlentities($f['Nombre'])?></td>
				<td align="center"><?=formatFechaDMA($f['FechaInicio'])?></td>
				<td align="center"><?=formatFechaDMA($f['FechaFin'])?></td>
				<td align="center"><?=printValoresGeneral('obras-situacion',$f['Situacion'])?></td>
				<td align="center"><?=printValoresGeneral('obras-estado',$f['Estado'])?></td>
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
	if ($ventana == "getObraValuacion") {
		?>
		function getObraValuacion(CodObra) {
			//	ajax
			$.ajax({
				type: "POST",
				url: "../../ob/ob_valuaciones_ajax.php",
				data: "modulo=ajax&accion=getObraValuacion&CodObra="+CodObra,
				async: false,
				dataType: "json",
				success: function(data) {
					parent.$("#CodObra").val(data['CodObra']);
					parent.$("#CodOrganismo").val(data['CodOrganismo']);
					parent.$("#CodDependencia").val(data['CodDependencia']);
					parent.$("#CodUnidadEjec").val(data['CodUnidadEjec']);
					parent.$("#Nombre").val(data['Nombre']);
					parent.$("#CodPresupuesto").val(data['CodPresupuesto']);
					parent.$("#Ejercicio").val(data['Ejercicio']);
					parent.$("#CategoriaProg").val(data['CategoriaProg']);
					parent.$("#CodInterno").val(data['CodInterno']);
					parent.$("#FechaInicio").val(formatFechaDMA(data['FechaInicio']));
					parent.$("#FechaFin").val(formatFechaDMA(data['FechaFin']));
					parent.$("#CodProveedor").val(data['CodProveedor']);
					parent.$("#NomProveedor").val(data['NomProveedor']);
					parent.$("#Sector").val(data['Sector']);
					parent.$("#NroDocumento").val(data['NroDocumento']);
					parent.$("#lista_detalle").html(data['lista_detalle']);
					parent.$("#MontoOriginal").val(data['MontoOriginal']).formatCurrency();
					parent.$("#MontoIva").val(data['MontoIva']).formatCurrency();
					parent.$("#MontoTotal").val(data['MontoTotal']).formatCurrency();
					parent.$("#Aumentos").val(data['Aumentos']).formatCurrency();
					parent.$("#PartNoPrev").val(data['PartNoPrev']).formatCurrency();
					parent.$("#Disminuciones").val(data['Disminuciones']).formatCurrency();

					parent.$("#ValObraEjecutada").val(data['MontoOriginal']).formatCurrency();
					parent.$("#ValIva").val(data['MontoIva']).formatCurrency();
					parent.$("#ValSubTotal1").val(data['MontoTotal']).formatCurrency();
					var ValObraEjecutadaAnt = new Number(setNumero(parent.$("#ValObraEjecutadaAnt").val()));
					var ValObraEjecutadaTotal = data['MontoOriginal'] + ValObraEjecutadaAnt;
					parent.$("#ValObraEjecutadaTotal").val(ValObraEjecutadaTotal).formatCurrency();
					var ValIvaAnt = new Number(setNumero(parent.$("#ValIvaAnt").val()));
					var ValIvaTotal = data['MontoIva'] + ValObraEjecutadaAnt;
					parent.$("#ValIvaTotal").val(ValIvaTotal).formatCurrency();
					var ValSubTotal1Ant = new Number(setNumero(parent.$("#ValSubTotal1Ant").val()));
					var ValSubTotal1Total = data['MontoTotal'] + ValSubTotal1Ant;
					parent.$("#ValSubTotal1Total").val(ValSubTotal1Total).formatCurrency();

					parent.$("#ValSubTotal2").val(data['MontoTotal']).formatCurrency();
					var ValSubTotal2Ant = new Number(setNumero(parent.$("#ValSubTotal2Ant").val()));
					var ValSubTotal2Total = data['MontoTotal'] + ValSubTotal2Ant;
					parent.$("#ValSubTotal2Total").val(ValSubTotal2Total).formatCurrency();
					parent.$("#ValMontoNeto").val(data['MontoTotal']).formatCurrency();
					var ValMontoNetoAnt = new Number(setNumero(parent.$("#ValMontoNetoAnt").val()));
					var ValMontoNetoTotal = data['MontoTotal'] + ValMontoNetoAnt;
					parent.$("#ValMontoNetoTotal").val(ValMontoNetoTotal).formatCurrency();

					var MontoTotal = new Number(data['MontoTotal']);
					var Aumentos = new Number(data['Aumentos']);
					var PartNoPrev = new Number(data['PartNoPrev']);
					var Disminuciones = new Number(data['Disminuciones']);
					var MontoModificado = MontoTotal + Aumentos + PartNoPrev - Disminuciones;
					parent.$("#MontoModificado").val(MontoModificado).formatCurrency();

					parent.$("#PorcAnticipo").val(data['PorcAnticipo']).formatCurrency();
					parent.$("#MontoAnticipo").val(data['MontoAnticipo']).formatCurrency();

					parent.$.prettyPhoto.close();
				}
			});
		}
		<?php
	}
	?>
</script>