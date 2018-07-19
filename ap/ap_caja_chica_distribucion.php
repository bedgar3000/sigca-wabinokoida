<?php
list($AnioActual, $MesActual, $DiaActual) = split("[/.-]", substr($Ahora, 0, 10));
$FechaActual = "$AnioActual-$MesActual-$DiaActual";
//	------------------------------------
$titulo = "Distribuci&oacute;n del Gasto";
$_width = 900;
?>
<center>
<form name="frm_conceptos" id="frm_conceptos" autocomplete="off">
<input type="hidden" id="id_conceptos" value="<?=$id_conceptos?>" />
<input type="hidden" id="sel_conceptos" />
<table width="<?=$_width?>" class="tblBotones">
	<thead>
    	<th class="divFormCaption" colspan="2">Conceptos del Gasto</th>
    </thead>
    <tbody>
    <tr>
        <td class="gallery clearfix">
            <input type="button" class="btLista" id="btSelCC" value="Sel. C.C." <?=$disabled_conceptos?> />
        </td>
        <td align="right">
            <input type="button" class="btLista" value="Insertar" onClick="window.open('../lib/listas/listado_concepto_gastos.php?filtrar=default&ventana=caja_chica_distribucion_insertar&detalle=conceptos&CodCentroCosto=<?=$CodCentroCosto?>', 'listado_concepto_gastos', 'toolbar=no, menubar=no, location=no, scrollbars=yes, width=950, height=550');" <?=$disabled_conceptos?> />
            <input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'conceptos');" <?=$disabled_conceptos?> />
        </td>
    </tr>
    </tbody>
</table>
<div style="overflow:scroll; width:<?=$_width?>px; height:280px;">
<table width="100%" class="tblLista">
    <thead>
    <tr>
        <th scope="col" width="15">#</th>
        <th scope="col" align="left">Concepto</th>
        <th scope="col" width="35">C.C.</th>
        <th scope="col" width="70">Partida</th>
        <th scope="col" width="90">Cuenta</th>
        <th scope="col" width="90">Cuenta (Pub. 20)</th>
        <th scope="col" width="100" align="right">Monto</th>
    </tr>
    </thead>
    
    <tbody id="lista_conceptos">
    <?php
	$nro_conceptos = 0;
	$detalles = split(";", $Distribucion);
	foreach ($detalles as $detalle) {	$nro_conceptos++;
		list($_CodConceptoGasto, $_CodCentroCosto, $_CodPartida, $_CodCuenta, $_CodCuentaPub20, $_Monto) = split("[|]", $detalle);
		$_NomConceptoGasto = getValorCampo("ap_conceptogastos", "CodConceptoGasto", "Descripcion", $_CodConceptoGasto);
		$id = $nro_conceptos;
		$TotalDistribucion += $_Monto;
		?>
        <tr class="trListaBody" onclick="clk($(this), 'conceptos', 'conceptos_<?=$id?>');" id="conceptos_<?=$id?>">
			<th>
				<?=$nro_conceptos?>
			</th>
			<td>
                <input type="hidden" name="CodConceptoGasto" value="<?=$_CodConceptoGasto?>" />
                <textarea name="NomConceptoGasto" style="height:25px;" class="cell2" readonly><?=htmlentities($_NomConceptoGasto)?></textarea>
			</td>
			<td>
                <input type="text" name="CodCentroCosto" id="CodCentroCosto_<?=$id?>" style="text-align:center;" class="cell2" value="<?=$CodCentroCosto?>" readonly />
			</td>
			<td>
                <input type="text" name="CodPartida" id="CodPartida_<?=$id?>" style="text-align:center;" class="cell2" value="<?=$_CodPartida?>" readonly />
			</td>
			<td>
                <input type="text" name="CodCuenta" id="CodCuenta_<?=$id?>" style="text-align:center;" class="cell2" value="<?=$_CodCuenta?>" readonly />
			</td>
			<td>
                <input type="text" name="CodCuentaPub20" id="CodCuentaPub20_<?=$id?>" style="text-align:center;" class="cell2" value="<?=$_CodCuentaPub20?>" readonly />
			</td>
			<td>
                <input type="text" name="Monto" id="Monto_<?=$id?>" value="<?=number_format($_Monto, 2, ',', '.')?>" style="text-align:right;" class="cell currency" onchange="caja_chica_distribucion_totales();" <?=$disabled_conceptos?> />
			</td>
		</tr>
		<?php
	}
	$PorDistribuir = $MontoBruto - $TotalDistribucion;
    ?>
    </tbody>
    
    <tfoot>
    <tr>
        <td colspan="6" align="right">Total Distribuci&oacute;n:</td>
        <th>
        	<input type="text" id="TotalDistribucion" value="<?=number_format($TotalDistribucion, 2, ',', '.')?>" style="text-align:right;" class="cell2 currency" readonly />
        </th>
    </tr>
    <tr>
        <td colspan="6" align="right">Total Gasto:</td>
        <th>
        	<input type="text" id="MontoBruto" value="<?=number_format($MontoBruto, 2, ',', '.')?>" style="text-align:right;" class="cell2 currency" readonly />
        </th>
    </tr>
    <tr>
        <td colspan="6" align="right">Por Distribuir:</td>
        <th>
        	<input type="text" id="PorDistribuir" value="<?=number_format($PorDistribuir, 2, ',', '.')?>" style="text-align:right;" class="cell2 currency" readonly />
        </th>
    </tr>
    </tfoot>
</table>
</div>
<input type="hidden" id="nro_conceptos" value="<?=$nro_conceptos?>" />
<input type="hidden" id="can_conceptos" value="<?=$nro_conceptos?>" />

<table width="<?=$_width?>" class="tblBotones">
    <tbody>
    <tr>
        <td align="center">
            <input type="button" class="btLista" value="Aceptar" onClick="caja_chica_distribucion();" <?=$disabled_conceptos?> />
            <input type="button" class="btLista" value="Cancelar" onclick="parent.$.prettyPhoto.close();" <?=$disabled_conceptos?> />
        </td>
    </tr>
    </tbody>
</table>
</form>
</center>

<script type="text/javascript" language="javascript">
function caja_chica_distribucion() {
	var id_conceptos = $("#id_conceptos").val();
	var MontoTotal = 0;
	var error = "";
	//	detalles
	var detalles_conceptos = "";
	var frm_conceptos = document.getElementById("frm_conceptos");
	for(var i=0; n=frm_conceptos.elements[i]; i++) {
		if (n.name == "CodConceptoGasto") detalles_conceptos += n.value + "|";
		else if (n.name == "CodCentroCosto") detalles_conceptos += n.value + "|";
		else if (n.name == "CodPartida") detalles_conceptos += n.value + "|";
		else if (n.name == "CodCuenta") detalles_conceptos += n.value + "|";
		else if (n.name == "CodCuentaPub20") detalles_conceptos += n.value + "|";
		else if (n.name == "Monto") {
			var Monto = setNumero(n.value);
			MontoTotal += Monto;
			if (isNaN(Monto) || Monto == 0) { error = "Se encontraron Montos incorrectos en las lineas de la Distribuci&oacute;n"; break; }
			else detalles_conceptos += Monto + ";";
		}
	}
	var len = detalles_conceptos.length; len-=1;
	detalles_conceptos = detalles_conceptos.substr(0, len);
	var PorDistribuir = setNumero($("#PorDistribuir").val());
	if (PorDistribuir != 0) error = "El Monto Por Distribuir debe ser Cero";
	if (error != "") cajaModal(error, "error", 400);
	else {
		parent.$("#Distribucion_"+id_conceptos).val(detalles_conceptos);
		parent.$.prettyPhoto.close();
	}
}
function caja_chica_distribucion_totales() {
	var MontoBruto = setNumero($("#MontoBruto").val());
	var TotalDistribucion = 0;
	//	detalles
	var detalles_conceptos = "";
	var frm_conceptos = document.getElementById("frm_conceptos");
	for(var i=0; n=frm_conceptos.elements[i]; i++) {
		if (n.name == "Monto") TotalDistribucion += setNumero(n.value);
	}
	var PorDistribuir = MontoBruto - TotalDistribucion;
	$("#TotalDistribucion").val(TotalDistribucion).formatCurrency();
	$("#PorDistribuir").val(PorDistribuir).formatCurrency();
}
</script>