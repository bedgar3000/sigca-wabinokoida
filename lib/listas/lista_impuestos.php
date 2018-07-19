<?php
if (!$ventana) $ventana = "selLista";
//	------------------------------------
if ($filtrar == "default") {
	$fEstado = "A";
	$fOrderBy = "CodImpuesto";
	$maxlimit = $_SESSION["MAXLIMIT"];
}
if ($fCodRegimenFiscal != "") { $cCodRegimenFiscal = "checked"; $filtro.=" AND (i.CodRegimenFiscal = '".$fCodRegimenFiscal."')"; } else $dCodRegimenFiscal = "disabled";
if ($fFlagProvision != "") { $cFlagProvision = "checked"; $filtro.=" AND (i.FlagProvision = '".$fFlagProvision."')"; } else $dFlagProvision = "disabled";
if ($fFlagImponible != "") { $cFlagImponible = "checked"; $filtro.=" AND (i.FlagImponible = '".$fFlagImponible."')"; } else $dFlagImponible = "disabled";
if ($fTipoComprobante != "") { $cTipoComprobante = "checked"; $filtro.=" AND (i.TipoComprobante = '".$fTipoComprobante."')"; } else $dTipoComprobante = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (i.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (i.CodImpuesto LIKE '%".$fBuscar."%' OR
					  i.Descripcion LIKE '%".$fBuscar."%' OR
					  rf.Descripcion LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
//	------------------------------------
$_width = 900;
?>
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lista_impuestos" method="post">
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
<input type="hidden" name="MontoAfecto" id="MontoAfecto" value="<?=$MontoAfecto?>" />
<input type="hidden" name="MontoNoAfecto" id="MontoNoAfecto" value="<?=$MontoNoAfecto?>" />
<input type="hidden" name="MontoImpuesto" id="MontoImpuesto" value="<?=$MontoImpuesto?>" />
<input type="hidden" name="MontoTotal" id="MontoTotal" value="<?=$MontoTotal?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />

<!--FILTRO-->
<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
	<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
		<tr>
			<td align="right">Regimen Fiscal: </td>
			<td>
				<input type="checkbox" <?=$cCodRegimenFiscal?> onclick="chkCampos(this.checked, 'fCodRegimenFiscal');" />
				<select name="fCodRegimenFiscal" id="fCodRegimenFiscal" style="width:160px;" <?=$dCodRegimenFiscal?>>
	                <option value="">&nbsp;</option>
					 <?=loadSelect("ap_regimenfiscal", "CodRegimenFiscal", "Descripcion", $fCodRegimenFiscal, 0)?>
				</select>
			</td>
			<td align="right">FlagImponible: </td>
			<td>
	            <input type="checkbox" <?=$cFlagImponible?> onclick="chkCampos(this.checked, 'fFlagImponible');" />
	            <select name="fFlagImponible" id="fFlagImponible" style="width:160px;" <?=$dFlagImponible?>>
	                <option value="">&nbsp;</option>
	                <?=loadSelectGeneral("IMPUESTO-IMPONIBLE", $fFlagImponible, 0)?>
	            </select>
			</td>
			<td align="right">Tipo: </td>
			<td>
	            <input type="checkbox" <?=$cTipoComprobante?> onclick="chkFiltro(this.checked, 'fTipoComprobante');" />
	            <select name="fTipoComprobante" id="fTipoComprobante" style="width:133px;" <?=$dTipoComprobante?>>
	                <option value="">&nbsp;</option>
	                <?=loadSelectGeneral("IMPUESTO-COMPROBANTE", $fTipoComprobante, 0)?>
	            </select>
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td align="right">Provisi&oacute;n En: </td>
			<td>
	            <input type="checkbox" <?=$cFlagProvision?> onclick="chkFiltro(this.checked, 'fFlagProvision');" />
				<select name="fFlagProvision" id="fFlagProvision" style="width:160px;" <?=$dFlagProvision?>>
					<option value="">&nbsp;</option>
					<?=loadSelectGeneral("IMPUESTO-PROVISION", $fFlagProvision, 0)?>
				</select>
			</td>
			<td align="right">Buscar: </td>
			<td>
				<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
				<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:160px;" <?=$dBuscar?> />
			</td>
			<td align="right">Estado: </td>
			<td>
	            <input type="checkbox" <?=$cEstado?> onclick="this.checked=!this.checked;" />
	            <select name="fEstado" id="fEstado" style="width:133px;" <?=$dEstado?>>
	                <?=loadSelectGeneral("ESTADO", $fEstado, 1)?>
	            </select>
			</td>
	        <td align="right"><input type="submit" value="Buscar"></td>
		</tr>
	</table>
</div>
<div class="sep"></div>

<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px; margin:auto;">
	<table class="tblLista" style="width:100%; min-width:1000px;">
		<thead>
		    <tr>
		        <th width="60" onclick="order('CodImpuesto')">Impuesto</th>
		        <th align="left" onclick="order('Descripcion')">Descripci&oacute;n</th>
		        <th width="65" onclick="order('TipoComprobante')">Tipo</th>
		        <th width="150" onclick="order('NomRegimenFiscal')">Regimen Fiscal</th>
		        <th width="50" onclick="order('FactorPorcentaje')">%</th>
		        <th width="175" onclick="order('FlagProvision')">Provisi&oacute;n En</th>
		        <th width="100" onclick="order('FlagImponible')">Imponible</th>
		        <th width="50" onclick="order('Signo')">Signo</th>
		        <th width="75" onclick="order('Estado')">Estado</th>
		    </tr>
	    </thead>
	    
	    <tbody>
		<?php
		//	consulto todos
		$sql = "SELECT i.*
				FROM
					mastimpuestos i
					INNER JOIN ap_regimenfiscal rf ON (rf.CodRegimenFiscal = i.CodRegimenFiscal)
				WHERE 1 $filtro";
		$rows_total = getNumRows3($sql);
		//	consulto lista
		$sql = "SELECT
					i.*,
					rf.Descripcion AS NomRegimenFiscal
				FROM
					mastimpuestos i
					INNER JOIN ap_regimenfiscal rf ON (rf.CodRegimenFiscal = i.CodRegimenFiscal)
				WHERE 1 $filtro
				ORDER BY $fOrderBy
				LIMIT ".intval($limit).", ".intval($maxlimit);
		$field = getRecords($sql);
		$rows_lista = count($field);
		foreach($field as $f) {
			$id = $f['CodImpuesto'];
			if ($ventana == "ob_valuaciones") {
				?><tr class="trListaBody" onClick="<?=$ventana?>('<?=$detalle?>','modulo=<?=$modulo?>&accion=<?=$accion?>&CodImpuesto=<?=$f['CodImpuesto']?>&MontoAfecto=<?=$MontoAfecto?>&MontoImpuesto=<?=$MontoImpuesto?>&MontoTotal=<?=$MontoTotal?>','<?=$f['CodImpuesto']?>','<?=$url?>');"><?php
			}
			elseif ($ventana == "listado_insertar_linea_adelanto") {
				?><tr class="trListaBody" onClick="<?=$ventana?>('<?=$detalle?>','modulo=<?=$modulo?>&accion=<?=$accion?>&CodImpuesto=<?=$f['CodImpuesto']?>','<?=$f['CodImpuesto']?>','<?=$url?>');"><?php
			}
			else {
				?><tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['CodImpuesto']?>'], ['<?=$campo1?>']);"><?php
			}
			?>
	            <td align="center"><?=$f['CodImpuesto']?></td>
	            <td><?=htmlentities($f['Descripcion'])?></td>
	            <td align="center"><?=printValoresGeneral("IMPUESTO-COMPROBANTE", $f['TipoComprobante'])?></td>
	            <td><?=htmlentities($f['NomRegimenFiscal'])?></td>
	            <td align="right"><strong><?=number_format($f['FactorPorcentaje'], 2, ',', '.')?></strong></td>
	            <td><?=printValoresGeneral("IMPUESTO-PROVISION", $f['FlagProvision'])?></td>
	            <td><?=printValoresGeneral("IMPUESTO-IMPONIBLE", $f['FlagImponible'])?></td>
	            <td align="center"><?=printSigno($f['Signo'])?></td>
	            <td align="center"><?=printValoresGeneral("ESTADO", $f['Estado'])?></td>
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
					parent.$("#FechaInicio").val(data['FechaInicio']);
					parent.$("#FechaFin").val(data['FechaFin']);
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
	elseif ($ventana == "ob_valuaciones") {
		?>
		function ob_valuaciones(detalle, data, id, url) {
			//	lista
			var nro_detalles = parent.$("#nro_"+detalle);
			var can_detalles = parent.$("#can_"+detalle);
			var lista_detalles = parent.$("#lista_"+detalle);
			var nro = new Number(nro_detalles.val());	nro++;
			var can = new Number(can_detalles.val());	can++;
			if (!id) var idtr = detalle+"_"+nro; else var idtr = detalle+"_"+id;
			if (!url) var url = "../fphp_funciones_ajax.php";
			//	ajax
			$.ajax({
				type: "POST",
				url: url,
				data: "nro_detalles="+nro+"&can_detalles="+can+"&"+data,
				async: false,
				success: function(resp) {
					if (parent.document.getElementById(idtr)) cajaModal("Registro ya insertado", "error_lista", 400);
					else {
						nro_detalles.val(nro);
						can_detalles.val(can);
						lista_detalles.append(resp);

						setRetenciones();

						parent.$.prettyPhoto.close();
						inicializarParent();
					}
				}
			});
		}
		function setValSubTotal2() {
			var ValSubTotal1 = setNumero(parent.$('#ValSubTotal1').val());
			var ValDev5RetLab = setNumero(parent.$('#ValDev5RetLab').val());
			var ValDev20RetFiel = setNumero(parent.$('#ValDev20RetFiel').val());
			var ValRetFiel20 = setNumero(parent.$('#ValRetFiel20').val());
			var ValAmortAnticipo = setNumero(parent.$('#ValAmortAnticipo').val());
			var ValAtraso = setNumero(parent.$('#ValAtraso').val());
			var ValHabiles = setNumero(parent.$('#ValHabiles').val());
			var ValMultaRescision = setNumero(parent.$('#ValMultaRescision').val());
			var ValContratoPor = setNumero(parent.$('#ValContratoPor').val());
			var ValMontoContrato = setNumero(parent.$('#ValMontoContrato').val());
			var ValSubTotal2 = ValSubTotal1 - (ValDev5RetLab + ValDev20RetFiel + ValRetFiel20 + ValAmortAnticipo + ValAtraso + ValHabiles + ValMultaRescision + ValContratoPor + ValMontoContrato);
			parent.$("#ValSubTotal2").val(ValSubTotal2).formatCurrency();
			var ValRetenciones = setNumero(parent.$('#ValRetenciones').val());
			var ValMontoNeto = ValSubTotal2 - ValRetenciones;
			parent.$("#ValMontoNeto").val(ValMontoNeto).formatCurrency();
			//	
			var ValDev5RetLabAnt = setNumero(parent.$('#ValDev5RetLabAnt').val());
			var ValDev5RetLabTotal = ValDev5RetLabAnt + ValDev5RetLab;
			parent.$("#ValDev5RetLabTotal").val(ValDev5RetLabTotal).formatCurrency();
			var ValDev20RetFielAnt = setNumero(parent.$('#ValDev20RetFielAnt').val());
			var ValDev20RetFielTotal = ValDev20RetFielAnt + ValDev20RetFiel;
			parent.$("#ValDev20RetFielTotal").val(ValDev20RetFielTotal).formatCurrency();
			var ValRetFiel20Ant = setNumero(parent.$('#ValRetFiel20Ant').val());
			var ValRetFiel20Total = ValRetFiel20Ant + ValRetFiel20;
			parent.$("#ValRetFiel20Total").val(ValRetFiel20Total).formatCurrency();
			var ValAmortAnticipoAnt = setNumero(parent.$('#ValAmortAnticipoAnt').val());
			var ValAmortAnticipoTotal = ValAmortAnticipoAnt + ValAmortAnticipo;
			parent.$("#ValAmortAnticipoTotal").val(ValAmortAnticipoTotal).formatCurrency();
			var ValAtrasoAnt = setNumero(parent.$('#ValAtrasoAnt').val());
			var ValAtrasoTotal = ValAtrasoAnt + ValAtraso;
			parent.$("#ValAtrasoTotal").val(ValAtrasoTotal).formatCurrency();
			var ValHabilesAnt = setNumero(parent.$('#ValHabilesAnt').val());
			var ValHabilesTotal = ValHabilesAnt + ValHabiles;
			parent.$("#ValHabilesTotal").val(ValHabilesTotal).formatCurrency();
			var ValMultaRescisionAnt = setNumero(parent.$('#ValMultaRescisionAnt').val());
			var ValMultaRescisionTotal = ValMultaRescisionAnt + ValMultaRescision;
			parent.$("#ValMultaRescisionTotal").val(ValMultaRescisionTotal).formatCurrency();
			var ValContratoPorAnt = setNumero(parent.$('#ValContratoPorAnt').val());
			var ValContratoPorTotal = ValContratoPorAnt + ValContratoPor;
			parent.$("#ValContratoPorTotal").val(ValContratoPorTotal).formatCurrency();
			var ValMontoContratoAnt = setNumero(parent.$('#ValMontoContratoAnt').val());
			var ValMontoContratoTotal = ValMontoContratoAnt + ValMontoContrato;
			parent.$("#ValMontoContratoTotal").val(ValMontoContratoTotal).formatCurrency();

			var ValSubTotal2Ant = setNumero(parent.$('#ValSubTotal2Ant').val());
			var ValSubTotal2Total = ValSubTotal2Ant + ValSubTotal2;
			parent.$("#ValSubTotal2Total").val(ValSubTotal2).formatCurrency();
			var ValRetencionesAnt = setNumero(parent.$('#ValRetencionesAnt').val());
			var ValRetencionesTotal = ValRetencionesAnt + ValRetenciones;
			parent.$("#ValRetencionesTotal").val(ValRetenciones).formatCurrency();
			var ValMontoNetoAnt = setNumero(parent.$('#ValMontoNetoAnt').val());
			var ValMontoNetoTotal = ValMontoNetoAnt + ValMontoNeto;
			parent.$("#ValMontoNetoTotal").val(ValMontoNeto).formatCurrency();
		}
		function setRetenciones() {
			//	TOTAL GENERAL
			var ValRetenciones = 0;
			var ValAmortAnticipo = 0;
			var ValDev20RetFiel = 0;
			parent.$('input[name="retenciones_Monto[]"]').each(function(idx) {
				var Monto = setNumero($(this).val());
				var CodImpuesto = parent.$('input[name="retenciones_CodImpuesto[]"]:eq('+idx+')').val();
				if (CodImpuesto == 'R10') {
					ValAmortAnticipo += Monto;
				}
				else if (CodImpuesto == 'R11') {
					ValDev20RetFiel += Monto;
				}
				else {
					ValRetenciones += Monto;
				}
			});
			parent.$('#ValRetenciones').val(ValRetenciones).formatCurrency();
			parent.$('#ValAmortAnticipo').val(ValAmortAnticipo).formatCurrency();
			parent.$('#ValDev20RetFiel').val(ValDev20RetFiel).formatCurrency();
			setValSubTotal2();
		}
		<?php
	}
	elseif ($ventana == "listado_insertar_linea_adelanto") {
		?>
		function listado_insertar_linea_adelanto(detalle, data, id, url) {
			//	lista
			var nro_detalles = parent.$("#nro_"+detalle);
			var can_detalles = parent.$("#can_"+detalle);
			var lista_detalles = parent.$("#lista_"+detalle);
			var nro = new Number(nro_detalles.val());	nro++;
			var can = new Number(can_detalles.val());	can++;
			if (!id) var idtr = detalle+"_"+nro; else var idtr = detalle+"_"+id;
			if (!url) var url = "../fphp_funciones_ajax.php";
			//	ajax
			$.ajax({
				type: "POST",
				url: url,
				data: "nro_detalles="+nro+"&can_detalles="+can+"&"+data+"&MontoAfecto="+$('#MontoAfecto').val()+"&MontoNoAfecto="+$('#MontoNoAfecto').val()+"&MontoImpuesto="+$('#MontoImpuesto').val(),
				async: false,
				success: function(resp) {
					if (parent.document.getElementById(idtr)) cajaModal("Registro ya insertado", "error_lista", 400);
					else {
						nro_detalles.val(nro);
						can_detalles.val(can);
						lista_detalles.append(resp);
						parent.setTotales();
						parent.$.prettyPhoto.close();
						inicializarParent();
					}
				}
			});
		}
		<?php
	}
	?>
</script>