<?php
include("../fphp.php");
//	------------------------------------
if (!isset($_SESSION['USUARIO_ACTUAL']) || !isset($_SESSION['ORGANISMO_ACTUAL'])) header("Location: ../../index.php");
//	------------------------------------
if ($filtrar == "default") {
	$fordenar = "p.CodPersona";
	$maxlimit = $_SESSION["MAXLIMIT"];
}
$filtro = "";
if ($fordenar != "") { $cordenar = "checked"; $orderby = "ORDER BY $fordenar"; } else $dordenar = "disabled";
if ($fbuscar != "") {
	$cbuscar = "checked";
	$filtro.=" AND (p.CodPersona LIKE '%".$fbuscar."%' OR 
					p.NomCompleto LIKE '%".$fbuscar."%' OR 
					p.Ndocumento LIKE '%".$fbuscar."%' OR 
					p.DocFiscal LIKE '%".$fbuscar."%')";
} else $dbuscar = "disabled";
//	------------------------------------
$filtro_flag = "";
if ($EsCliente == "S") { 
	$filtro_flag .= " AND (p.EsCliente = 'S' ";
}
if ($EsProveedor == "S") {
	if ($filtro_flag == "") $filtro_flag .= " AND (p.EsProveedor = 'S' ";
	else $filtro_flag .= " OR p.EsProveedor = 'S' ";
}
if ($EsEmpleado == "S") {
	if ($filtro_flag == "") $filtro_flag .= " AND (p.EsEmpleado = 'S' ";
	else $filtro_flag .= " OR p.EsEmpleado = 'S' ";
	$filtro_estado = " AND e.Estado = 'A'";
}
if ($EsOtros == "S") {
	if ($filtro_flag == "") $filtro_flag .= " AND (p.EsOtros = 'S' ";
	else $filtro_flag .= " OR p.EsOtros = 'S' ";
}
if ($filtro_flag != "") $filtro_flag = "$filtro_flag)";
//	------------------------------------
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
<link type="text/css" rel="stylesheet" href="../../css/custom-theme/jquery-ui-1.8.16.custom.css" charset="utf-8" />
<link type="text/css" rel="stylesheet" href="../../css/estilo.css" charset="utf-8" />
<link type="text/css" rel="stylesheet" href="../../css/prettyPhoto.css" media="screen" title="prettyPhoto main stylesheet" charset="utf-8" />
<script type="text/javascript" src="../../js/jquery-1.7.min.js" charset="utf-8"></script>
<script type="text/javascript" src="../../js/jquery-ui-1.8.16.custom.min.js" charset="utf-8"></script>
<script type="text/javascript" src="../../js/jquery.prettyPhoto.js" charset="utf-8"></script>
<script type="text/javascript" src="../../js/funciones.js" charset="utf-8"></script>
<script type="text/javascript" src="../../js/fscript.js" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
// 	funcion para seleccionar de una lista un registro y colocar su valor en la ventana que lo llamo
function selListadoObligacionPersona(CodPersona) {
	var CodOrganismo = parent.$("#CodOrganismo").val();
	$.ajax({
		type: "POST",
		url: "../../ap/ap_obligacion_ajax.php",
		data: "modulo=ajax&accion=selListadoObligacionPersona&CodPersona="+CodPersona+"&CodOrganismo="+CodOrganismo,
		async: false,
		success: function(resp) {
			var datos = resp.split("|");
			//	valores
			parent.$("#CodProveedor").val(CodPersona);
			parent.$("#NomCompleto").val(datos[0]);
			parent.$("#DiasPago").val(datos[1]);
			parent.$("#DocFiscal").val(datos[2]);
			parent.$("#Busqueda").val(datos[3]);
			parent.$("#CodProveedorPagar").val(CodPersona);
			parent.$("#NomProveedorPagar").val(datos[0]);
			parent.$("#CodTipoDocumento").val(datos[4]);
			parent.$("#CodTipoServicio").val(datos[5]);
			parent.$("#CodTipoPago").val(datos[6]);
			parent.$("#NroCuenta").val(datos[7]);
			parent.$("#FactorImpuesto").val(datos[8]);
			//	limpio las listas
			parent.$("#lista_documento").html("");
			parent.$("#lista_distribucion").html("");
			parent.$("#lista_impuesto").html("");
			parent.$("#FlagCompromiso").removeAttr("disabled").removeAttr("checked");
			parent.$("#FlagPresupuesto").removeAttr("disabled").attr("checked", "checked");
			parent.$("#FlagDistribucionManual").removeAttr("disabled").removeAttr("checked");
			//	si esta seleccionado pago directo
			if (parent.document.getElementById("FlagDistribucionManual").checked) {
				parent.$("#btInsertarDocumento").attr("disabled", "disabled");
				parent.$("#btQuitarDocumento").attr("disabled", "disabled");
				parent.$("#btInsertarDistribucion").removeAttr("disabled");
				parent.$("#btQuitarDistribucion").removeAttr("disabled");
				parent.$("#btSelPartida").removeAttr("disabled");
				parent.$("#btSelCuenta").removeAttr("disabled");
				parent.$("#btSelCuenta20").removeAttr("disabled");
				parent.$("#btSelCCosto").removeAttr("disabled");
				parent.$("#btSelPersona").removeAttr("disabled");
				parent.$("#btInsertarImpuesto").removeAttr("disabled");
				parent.$("#btQuitarImpuesto").removeAttr("disabled");
			} else {
				parent.$("#btInsertarDocumento").removeAttr("disabled");
				parent.$("#btQuitarDocumento").removeAttr("disabled");
				parent.$("#btInsertarDistribucion").attr("disabled", "disabled");
				parent.$("#btQuitarDistribucion").attr("disabled", "disabled");
				parent.$("#btSelPartida").attr("disabled", "disabled");
				parent.$("#btSelCuenta").attr("disabled", "disabled");
				parent.$("#btSelCuenta20").attr("disabled", "disabled");
				parent.$("#btSelCCosto").attr("disabled", "disabled");
				parent.$("#btSelPersona").attr("disabled", "disabled");
				parent.$("#btInsertarImpuesto").attr("disabled", "disabled");
				parent.$("#btQuitarImpuesto").attr("disabled", "disabled");
			}
			//	actualizo valores
			//	...
			parent.$.prettyPhoto.close();
		}
	});
}
</script>
</head>

<body>
<!-- ui-dialog -->
<div id="cajaModal"></div>
<form name="frmentrada" id="frmentrada" action="listado_personas.php?" method="post">
<input type="hidden" name="registro" id="registro" />
<input type="hidden" name="cod" id="cod" value="<?=$cod?>" />
<input type="hidden" name="nom" id="nom" value="<?=$nom?>" />
<input type="hidden" name="campo1" id="campo1" value="<?=$campo1?>" />
<input type="hidden" name="campo2" id="campo2" value="<?=$campo2?>" />
<input type="hidden" name="campo3" id="campo3" value="<?=$campo3?>" />
<input type="hidden" name="campo4" id="campo4" value="<?=$campo4?>" />
<input type="hidden" name="ventana" id="ventana" value="<?=$ventana?>" />
<input type="hidden" name="seldetalle" id="seldetalle" value="<?=$seldetalle?>" />
<input type="hidden" name="detalle" id="detalle" value="<?=$detalle?>" />
<input type="hidden" name="EsEmpleado" id="EsEmpleado" value="<?=$EsEmpleado?>" />
<input type="hidden" name="EsProveedor" id="EsProveedor" value="<?=$EsProveedor?>" />
<input type="hidden" name="EsOtros" id="EsOtros" value="<?=$EsOtros?>" />
<input type="hidden" name="EsCliente" id="EsCliente" value="<?=$EsCliente?>" />
<input type="hidden" name="marco" id="marco" value="<?=$marco?>" />
<input type="hidden" name="CodRequerimiento" id="CodRequerimiento" value="<?=$CodRequerimiento?>" />
<input type="hidden" name="Secuencia" id="Secuencia" value="<?=$Secuencia?>" />
<div class="divBorder" style="min-width:900px; width:100%;">
<table style="min-width:900px; width:100%;" class="tblFiltro">
	<tr>
		<td align="right" width="125">Buscar:</td>
        <td>
            <input type="checkbox" <?=$cbuscar?> onclick="chkFiltro(this.checked, 'fbuscar');" />
            <input type="text" name="fbuscar" id="fbuscar" style="width:200px;" value="<?=$fbuscar?>" <?=$dbuscar?> />
		</td>
		<td align="right" width="125">Ordenar Por:</td>
		<td>
            <input type="checkbox" <?=$cordenar?> onclick="this.checked=!this.checked;" />
            <select name="fordenar" id="fordenar" style="width:100px;" <?=$dordenar?>>
                <?=loadSelectGeneral("ORDENAR-PERSONA", $fordenar, 0)?>
            </select>
		</td>
	</tr>
</table>
</div>
<center><input type="submit" value="Buscar"></center><br />

<center>
<table style="min-width:900px; width:100%;" class="tblBotones">
	<tr>
		<td><div id="rows"></div></td>
	</tr>
</table>

<div class="scroll" style="overflow:scroll; min-width:900px; width:100%; height:250px;">
<table style="min-width:900px; width:100%;" class="tblLista">
	<thead>
	<tr>
		<th scope="col" width="55">Persona</th>
		<th scope="col">Descripci&oacute;n</th>
		<th scope="col" width="25">Emp.</th>
		<th scope="col" width="25">Pro.</th>
		<th scope="col" width="25">Cli.</th>
		<th scope="col" width="25">Otr.</th>
		<th scope="col" width="100">Nro. Documento</th>
		<th scope="col" width="100">Doc. Fiscal</th>
	</tr>
    </thead>
    
    <tbody>
	<?php
	//	consulto todos	
	$sql = "SELECT p.CodPersona
			FROM
				mastpersonas p
				LEFT JOIN mastempleado e ON (p.CodPersona = e.CodPersona $filtro_estado)
			WHERE p.Estado = 'A' $filtro $filtro_flag";
	$query = mysql_query($sql) or die ($sql.mysql_error());
	$rows_total = mysql_num_rows($query);
	
	//	consulto lista
	$sql = "SELECT
				p.CodPersona,
				p.NomCompleto,
				p.EsEmpleado,
				p.EsProveedor,
				p.EsCliente,
				p.EsOtros,
				p.TipoPersona,
				p.Ndocumento,
				p.DocFiscal,
				p.Estado
			FROM
				mastpersonas p
				LEFT JOIN mastempleado e ON (p.CodPersona = e.CodPersona $filtro_estado)
			WHERE p.Estado = 'A' $filtro $filtro_flag
			$orderby
			LIMIT ".intval($limit).", $maxlimit";
	$query = mysql_query($sql) or die ($sql.mysql_error());
	$rows_lista = mysql_num_rows($query);
	while ($field = mysql_fetch_array($query)) {
		if ($ventana == "selListadoObligacionPersona") {
			?><tr class="trListaBody" onclick="selListadoObligacionPersona('<?=$field['CodPersona']?>');" id="<?=$field['CodPersona']?>"><?php 
		}
		elseif ($ventana == "selListadoOrdenCompraPersona") {
			?><tr class="trListaBody" onclick="selListadoOrdenCompraPersona('<?=$field['CodPersona']?>');" id="<?=$field['CodPersona']?>"><?php 
		}
		elseif ($ventana == "selListadoOrdenServicioPersona") {
			?><tr class="trListaBody" onclick="selListadoOrdenServicioPersona('<?=$field['CodPersona']?>');" id="<?=$field['CodPersona']?>"><?php 
		}
		elseif ($ventana == "selListadoLista") {
			?><tr class="trListaBody" onclick="selListadoLista('<?=$seldetalle?>', '<?=$field["CodPersona"]?>', '<?=$field["NomCompleto"]?>', '<?=$cod?>', '<?=$nom?>');" id="<?=$field['CodPersona']?>"><?php
		}
		elseif ($ventana == "caja_chica_persona") {
			?><tr class="trListaBody" onclick="selListadoLista('<?=$seldetalle?>', '<?=$field["CodPersona"]?>', '<?=$field["NomCompleto"]?>', '<?=$cod?>', '<?=$nom?>', '<?=$field["DocFiscal"]?>', '<?=$campo3?>');" id="<?=$field['CodPersona']?>"><?php
		}
		elseif ($ventana == "requerimiento") {
			?><tr class="trListaBody" onclick="selListado2('<?=$field["CodPersona"]?>', '<?=$field["NomCompleto"]?>', '<?=$cod?>', '<?=$nom?>', '<?=$field["DocFiscal"]?>', 'ProveedorDocRef');" id="<?=$field['CodPersona']?>"><?php
		}
		elseif ($ventana == "registro_compra_form") {
			?><tr class="trListaBody" onclick="selListado2('<?=$field['CodPersona']?>', '<?=($field["NomCompleto"])?>', '<?=$cod?>', '<?=$nom?>', '<?=($field["DocFiscal"])?>', '<?=$campo3?>');" id="<?=$field['CodPersona']?>"><?php 
		}
		elseif($ventana == "selListadoIFrame") {
			?><tr class="trListaBody" onclick="selListadoIFrame('<?=$marco?>', '<?=$field['CodPersona']?>', '<?=($field["NomCompleto"])?>', '<?=$cod?>', '<?=$nom?>');" id="<?=$field['CodPersona']?>"><?php
		}
		elseif ($ventana == "cotizaciones_proveedores_insertar") {
			?>
        	<tr class="trListaBody" onclick="listado_insertar_linea('<?=$detalle?>', 'accion=<?=$ventana?>&CodPersona=<?=$field['CodPersona']?>', '<?=$field['CodPersona']?>');">
        	<?php
		}
		elseif ($ventana == "cotizaciones_proveedores_cotizar_insertar") {
			?>
        	<tr class="trListaBody" onclick="listado_insertar_linea('<?=$detalle?>', 'accion=<?=$ventana?>&CodPersona=<?=$field['CodPersona']?>&CodRequerimiento=<?=$CodRequerimiento?>&Secuencia=<?=$Secuencia?>', '<?=$field['CodPersona']?>');">
        	<?php
		}
		elseif ($ventana == "selLista") {
			?><tr class="trListaBody" onclick="selLista(['<?=$field['CodPersona']?>','<?=htmlentities($field['NomCompleto'])?>'],['<?=$campo1?>','<?=$campo2?>']);" id="<?=$field['CodPersona']?>"><?php
		}
		elseif ($ventana == "viaticos") {
			?><tr class="trListaBody" onclick="selListado2('<?=$field['CodPersona']?>','<?=($field["NomCompleto"])?>','<?=$cod?>','<?=$nom?>','<?=$_SESSION["FILTRO_ORGANISMO_ACTUAL"]?>','<?=$campo3?>');" id="<?=$field['CodPersona']?>"><?php 
		}
		else {
			?><tr class="trListaBody" onclick="selListado2('<?=$field['CodPersona']?>', '<?=($field["NomCompleto"])?>', '<?=$cod?>', '<?=$nom?>');" id="<?=$field['CodPersona']?>"><?php 
		}
        ?>
			<td align="center"><?=$field['CodPersona']?></td>
			<td><?=htmlentities($field['NomCompleto'])?></td>
			<td align="center"><?=printFlag($field['EsEmpleado'])?></td>
			<td align="center"><?=printFlag($field['EsProveedor'])?></td>
			<td align="center"><?=printFlag($field['EsCliente'])?></td>
			<td align="center"><?=printFlag($field['EsOtros'])?></td>
			<td><?=$field['Ndocumento']?></td>
			<td><?=$field['DocFiscal']?></td>
		</tr>
		<?php
	}
	?>
    </tbody>
</table>
</div>
<table style="min-width:900px; width:100%;">
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
<script type="text/javascript" language="javascript">
	totalRegistros(parseInt(<?=$rows_total?>));
</script>
</body>
</html>