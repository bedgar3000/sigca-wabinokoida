<?php
list($CodProveedor, $CodTipoDocumento, $NroDocumento, $TipoObligacion) = split("[_]", $registro);
//	consulto los datos del proveedor
$sql = "SELECT
			o.CodProveedor,
			o.CodTipoDocumento,
			o.NroDocumento,
			o.MontoObligacion,
			o.FechaRegistro,
			o.Periodo,
			p.NomCompleto AS Proveedor
		FROM
			pr_obligaciones o
			INNER JOIN mastpersonas p ON (p.CodPersona = o.CodProveedor)
		WHERE
			o.CodProveedor = '".$CodProveedor."' AND
			o.CodTipoDocumento = '".$CodTipoDocumento."' AND
			o.NroDocumento = '".$NroDocumento."'";
$query_proveedor = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
if (mysql_num_rows($query_proveedor) != 0) $field_proveedor = mysql_fetch_array($query_proveedor);
?>

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=pr_interfase_cuentas_por_pagar_verificar" method="post" onSubmit="return interfase_cuentas_por_pagar_verificar(this);">
<input type="hidden" id="CodOrganismo" value="<?=$CodOrganismo?>" />
<input type="hidden" id="CodTipoNom" value="<?=$CodTipoNom?>" />
<input type="hidden" id="Periodo" value="<?=$Periodo?>" />
<input type="hidden" id="CodTipoProceso" value="<?=$CodTipoProceso?>" />
<div class="divBorder" style="width:100%;">
<table width="100%" class="tblFiltro">
	<tr>
		<td align="right">Proveedor:</td>
		<td>
        	<input type="text" id="CodProveedor" style="width:40px;" value="<?=$field_proveedor['CodProveedor']?>" disabled />
        	<input type="text" id="Proveeedor" style="width:270px;" value="<?=htmlentities($field_proveedor['Proveedor'])?>" disabled />
        </td>
		<td align="right">Fecha:</td>
		<td><input type="text" id="Fecha" style="width:60px;" value="<?=formatFechaDMA($field_proveedor['FechaRegistro'])?>" disabled /></td>
	</tr>
	<tr>
		<td align="right">Documento:</td>
		<td>
        	<input type="text" id="CodTipoDocumento" style="width:20px;" value="<?=$field_proveedor['CodTipoDocumento']?>" disabled />
        	<input type="text" id="NroDocumento" style="width:115px;" value="<?=$field_proveedor['NroDocumento']?>" disabled />
        </td>
		<td align="right">Monto Obligaci&oacute;n:</td>
		<td><input type="text" id="MontoObligacion" style="width:100px; text-align:right;" class="codigo" value="<?=number_format($field_proveedor['MontoObligacion'], 2, ',', '.')?>" disabled /></td>
	</tr>
</table>
</div>
<center>
<input type="submit" value="Verificado" style="width:75px;" />
<input type="button" value="Cancelar" style="width:75px;" onClick="parent.$.prettyPhoto.close();" />
</center>
</form><br />

<form name="frm_partidas" id="frm_partidas">
<center>
<div style="overflow:scroll; width:100%; height:275px;">
<table class="tblLista" width="100%">
    <thead>
    <tr>
        <th width="45">Cat. Prog.</th>
        <th width="25">F.F.</th>
        <th width="65">Partida</th>
        <th align="left">Denominaci&oacute;n</th>
        <th width="75" align="right">Monto</th>
        <th width="75" align="right">Disponible</th>
        <th width="75" align="right">PreCompromiso</th>
        <th width="75" align="right">Diferencia</th>
    </tr>
    </thead>
    
    <tbody>
    <?php
    //	consulto lista
    $sql = "SELECT
				oc.cod_partida,
				SUM(oc.Monto) AS Monto,
				p.denominacion AS Partida,
				pv.CategoriaProg,
				pv.CodOrganismo,
				pv.CodPresupuesto,
				pv.Ejercicio,
				oc.CodFuente,
				CONCAT(ss.CodSector, pr.CodPrograma, a.CodActividad) AS CatProg
			FROM
				pr_obligacionescuenta oc
				INNER JOIN pv_partida p ON (p.cod_partida = oc.cod_partida)
				LEFT JOIN pv_presupuesto pv ON (pv.CodOrganismo = oc.CodOrganismo AND pv.CodPresupuesto = oc.CodPresupuesto)
				LEFT JOIN pv_categoriaprog cp ON (cp.CategoriaProg = pv.CategoriaProg)
				LEFT JOIN pv_actividades a ON (a.IdActividad = cp.IdActividad)
				LEFT JOIN pv_proyectos py ON (py.IdProyecto = a.IdProyecto)
				LEFT JOIN pv_subprogramas spr ON (spr.IdSubPrograma = py.IdSubPrograma)
				LEFT JOIN pv_programas pr ON (pr.IdPrograma = spr.IdPrograma)
				LEFT JOIN pv_subsector ss ON (ss.IdSubSector = pr.IdSubSector)
			WHERE
				oc.CodProveedor = '".$field_proveedor['CodProveedor']."' AND
				oc.CodTipoDocumento = '".$field_proveedor['CodTipoDocumento']."' AND
				oc.NroDocumento = '".$field_proveedor['NroDocumento']."'
			GROUP BY CodOrganismo, CodPresupuesto, CodFuente, cod_partida
			ORDER BY CodOrganismo, CodPresupuesto, CodFuente, cod_partida";
    $query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
    while ($field = mysql_fetch_array($query)) {
    	list($MontoAjustado, $MontoCompromiso, $PreCompromiso, $CotizacionesAsignadas) = disponibilidadPartida2($field['Ejercicio'], $field['CodOrganismo'], $field['cod_partida'], $field['CodPresupuesto'], $field['CodFuente']);
		$MontoAjustado = round(floatval($MontoAjustado), 2);
		$MontoCompromiso = round(floatval($MontoCompromiso), 2);
		$PreCompromisoObligacionesNomina = preCompromisoNomina($field['CodPresupuesto'], $field['CodFuente'], $field['cod_partida']);
		$Disponible = $MontoAjustado - $MontoCompromiso;
		$Diferencia = round(floatval($Disponible - $PreCompromisoObligacionesNomina), 2) - $field['Monto'];
        ?>
        <tr class="trListaBody">
            <td align="center">
            	<input type="text" name="CatProg" class="cell2" style="text-align:center;" value="<?=$field['CatProg']?>" />
            </td>
            <td align="center">
            	<input type="text" name="CodFuente" class="cell2" style="text-align:center;" value="<?=$field['CodFuente']?>" />
            </td>
            <td align="center">
            	<input type="text" name="cod_partida" class="cell2" style="text-align:center;" value="<?=$field['cod_partida']?>" />
            </td>
            <td>
            	<input type="text" class="cell2" value="<?=htmlentities($field['Partida'])?>" />
            </td>
            <td align="right">
            	<input type="hidden" name="Monto" value="<?=$field['Monto']?>" />
                <?=number_format($field['Monto'], 2, ',', '.')?>
            </td>
            <td align="right">
            	<input type="hidden" name="Disponible" value="<?=$Disponible?>" />
                <?=number_format($Disponible, 2, ',', '.')?>
            </td>
            <td align="right">
                <?=number_format($PreCompromisoObligacionesNomina, 2, ',', '.')?>
            </td>
            <td align="right">
            	<input type="hidden" name="Diferencia" value="<?=$Diferencia?>" />
                <?=number_format($Diferencia, 2, ',', '.')?>
            </td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>
</div>
</center>
</form>

<script type="text/javascript" language="javascript">
// 	interfase de cuentas por pagar (verificar)
function interfase_cuentas_por_pagar_verificar(form) {
	$(".div-progressbar").css("display", "block");
	var error = "";
	//	partidas
	var frm_partidas = document.getElementById("frm_partidas");
	for(var i=0; n=frm_partidas.elements[i]; i++) {
		if (n.name == "Diferencia") {
			var Diferencia = parseFloat(n.value);
			if (Diferencia < 0) { error = "Se encontraron partidas sin Disponibilidad"; break; }
		}
	}
	//	valido errores
	if (error != "") {
		cajaModal(error, "error", 400);
	} else {
		//	formulario
		var post = getForm(form);
		//	ajax
		$.ajax({
			type: "POST",
			url: "pr_interfase_cuentas_por_pagar_ajax.php",
			data: "modulo=interfase_cuentas_por_pagar&accion=verificar&"+post,
			async: false,
			success: function(resp) {
				if (resp.trim() != "") cajaModal(resp, "error", 400);
				else {
					var funct = "parent.$.prettyPhoto.close();";
					funct += "parent.document.getElementById('frmentrada').submit();";
					cajaModal("Presupuesto verificado exitosamente", "exito", 400, funct);
				}
			}
		});
	}
	return false;
}
</script>