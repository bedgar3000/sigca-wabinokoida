<?php
$filtro = '';
//	------------------------------------
if ($ventana == 'obligacion_adelanto') {
	$fEstado = 'PA';
	$filtro .= " AND (ga.SaldoAdelanto > 0)";
	##	
	$sql = "SELECT * FROM mastpersonas WHERE CodPersona = '$fCodProveedor'";
	$field_pr = getRecord($sql);
	$fCodProveedor = $field_pr['CodPersona'];
	$fNombreProveedor = $field_pr['NomCompleto'];
	$fDocFiscalProveedor = $field_pr['DocFiscal'];
	$dCodProveedor = "visibility:hidden;";
	$ckCodProveedor = "this.checked=!this.checked;";
} else {
	$ckCodProveedor = "ckLista(this.checked, ['fCodProveedor','fNombreProveedor','fDocFiscalProveedor'], ['aCodProveedor']);";
}
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$fFechaDocumentoD = formatFechaDMA($AnioActual.'-01-01');
	$fFechaDocumentoH = formatFechaDMA($FechaActual);
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "FechaDocumento";
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (ga.NroAdelanto LIKE '%$fBuscar%'
					  OR p.NomCompleto LIKE '%$fBuscar%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (ga.Estado = '$fEstado')"; } else $dEstado = "disabled";
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (ga.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fFechaDocumentoD != "" || $fFechaDocumentoH != "") {
	$cFechaDocumento = "checked";
	if ($fFechaDocumentoD != "") $filtro.=" AND (ga.FechaDocumento >= '".formatFechaAMD($fFechaDocumentoD)."')";
	if ($fFechaDocumentoH != "") $filtro.=" AND (ga.FechaDocumento <= '".formatFechaAMD($fFechaDocumentoH)."')";
} else $dFechaDocumento = "disabled";
if ($fTipoAdelanto != "") { $cTipoAdelanto = "checked"; $filtro.=" AND (ga.TipoAdelanto = '$fTipoAdelanto')"; } else $dTipoAdelanto = "disabled";
if ($fCodProveedor != "") { $cCodProveedor = "checked"; $filtro.=" AND (ga.CodProveedor = '".$fCodProveedor."')"; } else $dCodProveedor = "visibility:hidden;";
if ($fCodCentroCosto != "") { $cCodCentroCosto = "checked"; $filtro.=" AND (ga.CodCentroCosto = '$fCodCentroCosto')"; } else $dCodCentroCosto = "disabled";
//	------------------------------------
$_titulo = "Lista de Adelantos";
$_width = 900;
?>
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lista_ap_gastoadelanto" method="post">
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

<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
	<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
		<tr>
			<td align="right" width="100">Organismo:</td>
			<td>
				<input type="checkbox" <?=$cCodOrganismo?> onclick="this.checked=!this.checked;" />
				<select name="fCodOrganismo" id="fCodOrganismo" style="width:225px;" <?=$dCodOrganismo?>>
					<?=getOrganismos($fCodOrganismo, 3);?>
				</select>
			</td>
			<td align="right" width="100">Proveedor:</td>
			<td class="gallery clearfix">
				<input type="checkbox" <?=$cCodProveedor?> onclick="<?=$ckCodProveedor?>" />
				<input type="hidden" name="fDocFiscalProveedor" id="fDocFiscalProveedor" value="<?=$fDocFiscalProveedor?>">
				<input type="hidden" name="fCodProveedor" id="fCodProveedor" value="<?=$fCodProveedor?>" />
				<input type="text" name="fNombreProveedor" id="fNombreProveedor" value="<?=htmlentities($fNombreProveedor)?>" style="width:225px;" readonly />
				<a href="../lib/listas/gehen.php?anz=lista_personas&campo1=fCodProveedor&campo2=fNombreProveedor&campo3=fDocFiscalProveedor&ventana=&filtrar=default&FlagClasePersona=S&fEsProveedor=S&fEsOtros=S&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" style=" <?=$dCodProveedor?>" id="aCodProveedor">
					<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
				</a>
			</td>
			<td align="right" width="100">Buscar:</td>
			<td>
				<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
				<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:100px;" <?=$dBuscar?> />
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td align="right">Centro de Costo:</td>
			<td>
				<input type="checkbox" <?=$cCodCentroCosto?> onclick="chkFiltro(this.checked, 'fCodCentroCosto');" />
				<select name="fCodCentroCosto" id="fCodCentroCosto" style="width:225px;" <?=$dCodCentroCosto?>>
					<option value="">&nbsp;</option>
					<?=loadSelect2('ac_mastcentrocosto','CodCentroCosto','Descripcion',$fCodCentroCosto)?>
				</select>
			</td>
			<td align="right">Fecha:</td>
			<td>
				<input type="checkbox" <?=$cFechaDocumento?> onclick="chkCampos2(this.checked, ['fFechaDocumentoD','fFechaDocumentoH']);" />
				<input type="text" name="fFechaDocumentoD" id="fFechaDocumentoD" value="<?=$fFechaDocumentoD?>" <?=$dFechaDocumento?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" /> -
				<input type="text" name="fFechaDocumentoH" id="fFechaDocumentoH" value="<?=$fFechaDocumentoH?>" <?=$dFechaDocumento?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" />
			</td>
			<td align="right">Estado: </td>
			<td>
				<?php if ($ventana != 'obligacion_adelanto') { ?>
					<input type="checkbox" <?=$cEstado?> onclick="chkFiltro(this.checked, 'fEstado');" />
					<select name="fEstado" id="fEstado" style="width:100px;" <?=$dEstado?>>
						<option value="">&nbsp;</option>
						<?=loadSelectGeneral("adelanto-estado", $fEstado, 0)?>
					</select>
				<?php } else { ?>
					<input type="checkbox" <?=$cEstado?> onclick="this.checked=!this.checked;" />
					<select name="fEstado" id="fEstado" style="width:100px;" <?=$dEstado?>>
						<?=loadSelectGeneral("adelanto-estado", $fEstado, 1)?>
					</select>
				<?php } ?>
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td align="right">Tipo Adelanto: </td>
			<td>
				<input type="checkbox" <?=$cTipoAdelanto?> onclick="chkFiltro(this.checked, 'fTipoAdelanto');" />
				<select name="fTipoAdelanto" id="fTipoAdelanto" style="width:100px;" <?=$dTipoAdelanto?>>
					<option value="">&nbsp;</option>
					<?=loadSelectGeneral("adelanto-tipo", $fTipoAdelanto, 0)?>
				</select>
			</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td align="right"><input type="submit" value="Buscar"></td>
		</tr>
	</table>
</div>
<div class="sep"></div>

<center>
<div class="scroll" style="overflow:scroll; height:315px; width:100%; min-width:<?=$_width?>px;">
	<table class="tblLista" style="width:100%; min-width:<?=$_width?>px;">
		<thead>
			<tr>
				<th width="75" onclick="order('NroAdelanto')">N&uacute;mero</th>
				<th style="min-width: 200px;" align="left" onclick="order('NomProveedor')">Proveedor</th>
				<th width="50" onclick="order('CentroCosto')">C.C</th>
				<th width="150" onclick="order('MontoTotal')">Monto Total</th>
				<th width="150" onclick="order('SaldoAdelanto')">Saldo Adelanto</th>
				<th width="75" onclick="order('FechaDocumento')">Fecha</th>
				<th width="100" onclick="order('Estado')">Estado</th>
			</tr>
		</thead>
    
		<tbody>
		<?php
		//	consulto todos
		$sql = "SELECT *
				FROM ap_gastoadelanto ga
				INNER JOIN ac_mastcentrocosto cc ON cc.CodCentrocosto = ga.CodCentroCosto
				WHERE 1 $filtro";
		$rows_total = getNumRows3($sql);
		//	consulto lista
		$sql = "SELECT
					ga.*,
					p.NomCompleto AS NomProveedor,
					cc.Codigo AS CentroCosto
				FROM ap_gastoadelanto ga
				INNER JOIN mastpersonas p ON p.CodPersona = ga.CodProveedor
				INNER JOIN ac_mastcentrocosto cc ON cc.CodCentrocosto = ga.CodCentroCosto
				WHERE 1 $filtro
				ORDER BY $fOrderBy
				LIMIT ".intval($limit).", ".intval($maxlimit);
		$field = getRecords($sql);
		$rows_lista = count($field);
		foreach($field as $f) {
			$id = $f['CodAdelanto'];
			if ($ventana == 'listado_insertar_linea') {
				?>
				<tr class="trListaBody" onClick="listado_insertar_linea('<?=$detalle?>','modulo=<?=$modulo?>&accion=<?=$accion?>&CodAdelanto=<?=$f['CodAdelanto']?>','<?=$f['CodAdelanto']?>','<?=$url?>');">
				<?php
			} 
			elseif ($ventana == 'obligacion_adelanto') {
				?>
				<tr class="trListaBody" onClick="obligacion_adelanto('<?=$detalle?>','modulo=<?=$modulo?>&accion=<?=$accion?>&CodAdelanto=<?=$f['CodAdelanto']?>','<?=$f['CodAdelanto']?>','<?=$url?>');">
				<?php
			} 
			else {
				?>
				<tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['CodAdelanto']?>','<?=htmlentities($f['Descripcion'])?>'], ['<?=$campo1?>','<?=$campo2?>']);">
				<?php
			}
			?>
				<td align="center"><?=$f['NroAdelanto']?></td>
				<td><?=htmlentities($f['NomProveedor'])?></td>
				<td align="center"><?=$f['CentroCosto']?></td>
				<td align="right"><?=number_format($f['MontoTotal'],2,',','.')?></td>
				<td align="right"><?=number_format($f['SaldoAdelanto'],2,',','.')?></td>
				<td align="center"><?=formatFechaAMD($f['FechaDocumento'])?></td>
				<td align="center"><?=printValoresGeneral('adelanto-estado',$f['Estado'])?></td>
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
</center>
</form>

<script>
	<?php if ($ventana == 'obligacion_adelanto') { ?> 
		function obligacion_adelanto(detalle, data, id, url) {
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
						parent.setMontoAdelantos();
						parent.$.prettyPhoto.close();
						inicializarParent();
					}
				}
			});
		}
	<?php } ?>
</script>