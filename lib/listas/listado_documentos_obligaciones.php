<?php
session_start();
if (!isset($_SESSION['USUARIO_ACTUAL']) || !isset($_SESSION['ORGANISMO_ACTUAL'])) header("Location: ../../index.php");
//	------------------------------------
include("../fphp.php");
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
<script type="text/javascript" src="../../ap/js/fscript.js" charset="utf-8"></script>
<script type="text/javascript" src="../../ap/js/funciones.js" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
// 	funcion para seleccionar de una lista un registro y colocar su valor en la ventana que lo llamo
function obligacion_documentos_insertar(registro) {
	var accion = "obligacion_documentos_insertar";
	var detalle = "documento";
	var php_ajax = "../../ap/ap_obligacion_ajax.php";
	//
	var nro = "nro_" + detalle;
	var can = "can_" + detalle;
	var sel = "sel_" + detalle;
	var lista = "lista_" + detalle;
	var nrodetalle = new Number(opener.document.getElementById(nro).value); nrodetalle++;
	var candetalle = new Number(opener.document.getElementById(can).value); candetalle++;
	var nro_distribucion = new Number(opener.document.getElementById("nro_distribucion").value);
	var CodProveedor = opener.document.getElementById("CodProveedor").value;
	var CodOrganismo = opener.document.getElementById("CodOrganismo").value;
	var CodTipoServicio = opener.document.getElementById("CodTipoServicio").value;
	var Monto = new Number(setNumero(document.getElementById("Monto_"+registro).value));
	var MontoPendiente = new Number(document.getElementById("MontoPendiente_"+registro).value);
	var MontoPagado = new Number(document.getElementById("MontoPagado_"+registro).value);
	var MontoTotal = new Number(document.getElementById("MontoTotal_"+registro).value);
	//	ajax
	$.ajax({
		type: "POST",
		url: php_ajax,
		data: "modulo=ajax&accion="+accion+"&registro="+registro+"&CodProveedor="+CodProveedor+"&CodOrganismo="+CodOrganismo+"&CodTipoServicio="+CodTipoServicio+"&Monto="+Monto+"&MontoPendiente="+MontoPendiente+"&MontoPagado="+MontoPagado+"&MontoTotal="+MontoTotal+"&nrodetalle="+nrodetalle+"&candetalle="+candetalle+"&nro_distribucion="+nro_distribucion,
		async: true,
		success: function(resp) {
			opener.$("#"+nro).val(nrodetalle);
			opener.$("#"+can).val(candetalle);
			var idtr = detalle + "_" + registro;
			if (opener.document.getElementById(idtr)) cajaModal("Documento ya insertado", "error_lista", 400);
			else {
				var partes = resp.split("||");
				//	documentos relacionados
				var newTr = opener.document.createElement("tr");
				newTr.className = "trListaBody";
				newTr.setAttribute("onclick", "mClk(this, '"+sel+"');");
				newTr.id = idtr
				opener.document.getElementById(lista).appendChild(newTr);
				opener.document.getElementById(idtr).innerHTML = partes[1];
				//	distribucion
				opener.$("#lista_distribucion").append(partes[2]);
				opener.$("#nro_distribucion").val(partes[3]);
				opener.$("#can_distribucion").val(partes[3]);
				//	desbloqueo lista de retenciones
				opener.$("#btInsertarImpuesto").removeAttr("disabled");
				opener.$("#btQuitarImpuesto").removeAttr("disabled");
				//	actualizar montos de la obligacion
				actualizar_documento_totales_opener(opener.document.getElementById("frm_documento"));
				actualizar_montos_obligacion_opener();
				window.close();
			}
		}
	});
}
//	actualizo totales de los documentos
function actualizar_documento_totales_opener(frm_documento) {
	var documento_total = new Number();
	var documento_afecto = new Number();
	var documento_impuesto = new Number();
	var documento_noafecto = new Number();
	//	distribucion
	var documento_impuesto = new Number();
	for(var i=0; n=frm_documento.elements[i]; i++) {
		if (n.name == "MontoTotal") {
			var MontoTotal = new Number(setNumero(n.value));
			documento_total += MontoTotal;
		}
		else if (n.name == "MontoAfecto") {
			var MontoAfecto = new Number(setNumero(n.value));
			documento_afecto += MontoAfecto;
		}
		if (n.name == "MontoImpuestos") {
			var MontoImpuestos = new Number(setNumero(n.value));
			documento_impuesto += MontoImpuestos;
		}
		if (n.name == "MontoNoAfecto") {
			var MontoNoAfecto = new Number(setNumero(n.value));
			documento_noafecto += MontoNoAfecto;
		}
	}
	opener.$("#documento_total").val(setNumeroFormato(documento_total, 2, ".", ","));
	opener.$("#documento_afecto").val(setNumeroFormato(documento_afecto, 2, ".", ","));
	opener.$("#documento_impuesto").val(setNumeroFormato(documento_impuesto, 2, ".", ","));
	opener.$("#documento_noafecto").val(setNumeroFormato(documento_noafecto, 2, ".", ","));
}
//	actualizo montos de la obligacion
function actualizar_montos_obligacion_opener() {
	var MontoAfecto = obtener_obligacion_afecto(opener.document.getElementById("frm_distribucion"));
	var MontoNoAfecto = obtener_obligacion_noafecto(opener.document.getElementById("frm_distribucion"));
	var MontoImpuesto = obtener_obligacion_impuestos(opener.document.getElementById("frm_documento"));
	var MontoBruto = MontoAfecto + MontoNoAfecto;
	actualizar_afecto_retenciones(MontoAfecto, MontoNoAfecto, MontoImpuesto, opener.document.getElementById("frm_impuesto"));
	var MontoImpuestoOtros = obtener_obligacion_retenciones(opener.document.getElementById("frm_impuesto"));
	var MontoObligacion = MontoAfecto + MontoNoAfecto + MontoImpuesto + MontoImpuestoOtros;
	opener.$("#MontoAfecto").val(setNumeroFormato(MontoAfecto, 2, ".", ","));
	opener.$("#MontoNoAfecto").val(setNumeroFormato(MontoNoAfecto, 2, ".", ","));
	opener.$("#MontoImpuesto").val(setNumeroFormato(MontoImpuesto, 2, ".", ","));
	opener.$("#MontoImpuestoOtros").val(setNumeroFormato(MontoImpuestoOtros, 2, ".", ","));
	opener.$("#MontoObligacion").val(setNumeroFormato(MontoObligacion, 2, ".", ","));
	opener.$("#MontoPagar").val(setNumeroFormato(MontoObligacion, 2, ".", ","));
	opener.$("#MontoPendiente").val(setNumeroFormato(MontoObligacion, 2, ".", ","));
	opener.$("#impuesto_total").val(setNumeroFormato(MontoImpuestoOtros, 2, ".", ","));
	opener.$("#distribucion_total").val(setNumeroFormato(MontoBruto, 2, ".", ","));
}
</script>
</head>

<body>
<!-- ui-dialog -->
<div id="cajaModal"></div>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo">Listado de Documentos del Proveedor</td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<table width="1000" align="center" cellpadding="0" cellspacing="0">
    <tr>
        <td>
            <div class="header">
            <ul id="tab">
            <!-- CSS Tabs -->
            <li id="li1" onclick="currentTab('tab', this);" style="display:none;"><a href="#" onclick="mostrarTab('tab', 1, 3);">Documentos Recibidos</a></li>
            <li id="li2" onclick="currentTab('tab', this);" class="current"><a href="#" onclick="mostrarTab('tab', 2, 3);">O.Compra</a></li>
            <li id="li3" onclick="currentTab('tab', this);"><a href="#" onclick="mostrarTab('tab', 3, 3);">O.Servicio</a></li>
            </ul>
            </div>
        </td>
    </tr>
</table>

<form name="frmentrada" id="frmentrada" action="listado_documentos_obligaciones.php?" method="post">
<input type="hidden" id="CodOrganismo" value="<?=$CodOrganismo?>" />
<input type="hidden" id="CodProveedor" value="<?=$CodProveedor?>" />
<center>

<div id="tab1" style="display:none;">
<div style="overflow:scroll; width:1000px; height:225px;">
<table width="1900" class="tblLista">
	<thead>
	<tr>
		<th scope="col" width="90">Nro. Orden</th>
		<th scope="col" width="75">Fecha Preparaci&oacute;n</th>
		<th scope="col">Descripcion</th>
		<th scope="col" width="100">Monto A Pagar</th>
		<th scope="col" width="100">Monto Pendiente</th>
		<th scope="col" width="100">Monto Pagado</th>
		<th scope="col" width="100">Monto Total</th>
		<th scope="col" width="175">Almacen</th>
		<th scope="col" width="175">Almacen Ingreso</th>
		<th scope="col" width="125">Forma de Pago</th>
		<th scope="col" width="100">Estado</th>
	</tr>
	</thead>
	<?php
	//	consulto lista
	$sql = "(SELECT 
				d.*,
				oc.Estado,
				a1.Descripcion AS NomAlmacen,
				a2.Descripcion AS NomAlmacenIngreso,
				fp.Descripcion AS NomFormaPago
			FROM 
				ap_documentos d
				INNER JOIN lg_ordencompra oc ON (d.ReferenciaNrodocumento = oc.NroOrden)
				INNER JOIN lg_almacenmast a1 ON (oc.CodAlmacen = a1.Codalmacen)
				LEFT JOIN lg_almacenmast a2 ON (oc.CodAlmacenIngreso = a2.Codalmacen)
				LEFT JOIN mastformapago fp ON (oc.CodFormaPago = fp.CodFormaPago)
			WHERE
				d.ReferenciaTipoDocumento = 'OC' AND
				d.CodProveedor = '".$CodProveedor."' AND
				d.Estado = 'PR')
			UNION
			(SELECT 
				d.*,
				os.Estado,
				'' AS NomAlmacen,
				'' AS NomAlmacenIngreso,
				'' AS NomFormaPago
			FROM 
				ap_documentos d
				INNER JOIN lg_ordenservicio os ON (d.ReferenciaNrodocumento = os.NroOrden)
			WHERE
				d.ReferenciaTipoDocumento = 'OS' AND
				d.CodProveedor = '".$CodProveedor."' AND
				d.Estado = 'PR')";
	$query_documentos = mysql_query($sql) or die ($sql.mysql_error());
	while ($field_documentos = mysql_fetch_array($query_documentos)) {
		$iddoc = $field_documentos['ReferenciaTipoDocumento']."-".$field_documentos['ReferenciaNroDocumento']."-".$field_documentos['DocumentoClasificacion']."-".$field_documentos['DocumentoReferencia'];
		?>
		<tr class="trListaBody" id="<?=$idoc?>">
			<td align="center" ondblclick="obligacion_documentos_insertar('<?=$iddoc?>');">
				<?=$field_documentos['ReferenciaNroDocumento']?>
            </td>
			<td align="center" ondblclick="obligacion_documentos_insertar('<?=$iddoc?>');">
				<?=formatFechaDMA($field_documentos['Fecha'])?>
            </td>
			<td ondblclick="obligacion_documentos_insertar('<?=$iddoc?>');">
				<?=htmlentities($field_documentos['Comentarios'])?>
            </td>
			<td align="right">
            	<input type="hidden" name="Monto" id="Monto_<?=$iddoc?>" value="<?=number_format($field_documentos['MontoPendiente'], 2, ',', '.')?>" />
                <?=number_format($field_documentos['MontoPendiente'], 2, ',', '.')?>
			</td>
			<td align="right" ondblclick="obligacion_documentos_insertar('<?=$iddoc?>');">
            	<input type="hidden" name="MontoPendiente" id="MontoPendiente_<?=$iddoc?>" value="<?=$field_documentos['MontoPendiente']?>" />
				<?=number_format($field_documentos['MontoPendiente'], 2, ',', '.')?>
			</td>
			<td align="right" ondblclick="obligacion_documentos_insertar('<?=$iddoc?>');">
            	<input type="hidden" name="MontoPagado" id="MontoPagado_<?=$iddoc?>" value="<?=$field_documentos['MontoPagado']?>" />                
				<?=number_format($field_documentos['MontoPagado'], 2, ',', '.')?>
			</td>
			<td align="right" ondblclick="obligacion_documentos_insertar('<?=$iddoc?>');">
            	<input type="hidden" name="MontoTotal" id="MontoTotal_<?=$iddoc?>" value="<?=$field_documentos['MontoTotal']?>" />                
				<?=number_format($field_documentos['MontoTotal'], 2, ',', '.')?>
			</td>
			<td ondblclick="obligacion_documentos_insertar('<?=$iddoc?>');">
				<?=htmlentities($field_documentos['NomAlmacen'])?>
            </td>
			<td ondblclick="obligacion_documentos_insertar('<?=$iddoc?>');">
				<?=htmlentities($field_documentos['NomAlmacenIngreso'])?>
            </td>
			<td align="center" ondblclick="obligacion_documentos_insertar('<?=$iddoc?>');">
				<?=htmlentities($field_documentos['NomFormaPago'])?>
            </td>
			<td align="center" ondblclick="obligacion_documentos_insertar('<?=$iddoc?>');">
				<?=printValoresGeneral("ESTADO-ORDENES", $field_documentos['Estado'])?>
            </td>
		</tr>
		<?php
	}
	?>
</table>
</div>
</div>

<div id="tab2" style="display:block;">
<div style="overflow:scroll; width:1000px; height:225px;">
<table width="1900" class="tblLista">
	<thead>
	<tr>
		<th scope="col" width="90">Nro. Orden</th>
		<th scope="col" width="75">Fecha Preparaci&oacute;n</th>
		<th scope="col">Descripcion</th>
		<th scope="col" width="100">Monto A Pagar</th>
		<th scope="col" width="100">Monto Pendiente</th>
		<th scope="col" width="100">Monto Pagado</th>
		<th scope="col" width="100">Monto Total</th>
		<th scope="col" width="175">Almacen</th>
		<th scope="col" width="175">Almacen Ingreso</th>
		<th scope="col" width="125">Forma de Pago</th>
		<th scope="col" width="100">Estado</th>
	</tr>
	</thead>
	<?php
	//	consulto lista
	$sql = "SELECT 
				oc.*,
				a1.Descripcion AS NomAlmacen,
				a2.Descripcion AS NomAlmacenIngreso,
				fp.Descripcion AS NomFormaPago,
				(SELECT SUM(MontoTotal)
				 FROM ap_documentos
				 WHERE
					ReferenciaTipoDocumento = 'OC' AND
					Anio = oc.Anio AND
					CodOrganismo = oc.CodOrganismo AND
					ReferenciaNroDocumento = oc.NroOrden AND
					Estado = 'RV') AS MontoDocumento
			FROM 
				lg_ordencompra oc
				INNER JOIN lg_almacenmast a1 ON (oc.CodAlmacen = a1.Codalmacen)
				LEFT JOIN lg_almacenmast a2 ON (oc.CodAlmacenIngreso = a2.Codalmacen)
				LEFT JOIN mastformapago fp ON (oc.CodFormaPago = fp.CodFormaPago)
			WHERE
				oc.Estado = 'AP' AND
				oc.CodProveedor = '".$CodProveedor."'";
	$query_compras = mysql_query($sql) or die ($sql.mysql_error());
	while ($field_compras = mysql_fetch_array($query_compras)) {
		$iddoc = "OC-".$field_compras['Anio']."-".$field_compras['NroOrden']."-OCD";
		$MontoPendiente = $field_compras['MontoTotal'] - $field_compras['MontoDocumento'];
		if ($MontoPendiente > 0) {
			?>
			<tr class="trListaBody" id="<?=$idoc?>">
				<td align="center" ondblclick="obligacion_documentos_insertar('<?=$iddoc?>');">
					<?=$field_compras['NroOrden']?>
				</td>
				<td align="center" ondblclick="obligacion_documentos_insertar('<?=$iddoc?>');">
					<?=formatFechaDMA($field_compras['FechaPreparacion'])?>
				</td>
				<td ondblclick="obligacion_documentos_insertar('<?=$iddoc?>');">
					<?=htmlentities($field_compras['Observaciones'])?>
				</td>
				<td align="right">
					<input type="text" name="Monto" id="Monto_<?=$iddoc?>" value="<?=number_format($MontoPendiente, 2, ',', '.')?>" style="text-align:right;" class="cell" onfocus="numeroFocus(this);" onblur="numeroBlur(this);" />
				</td>
				<td align="right" ondblclick="obligacion_documentos_insertar('<?=$iddoc?>');">
					<input type="hidden" name="MontoPendiente" id="MontoPendiente_<?=$iddoc?>" value="<?=$MontoPendiente?>" />
					<?=number_format($MontoPendiente, 2, ',', '.')?>
				</td>
				<td align="right" ondblclick="obligacion_documentos_insertar('<?=$iddoc?>');">
					<input type="hidden" name="MontoPagado" id="MontoPagado_<?=$iddoc?>" value="<?=$field_compras['MontoPagado']?>" />
					<?=number_format($field_compras['MontoPagado'], 2, ',', '.')?>
				</td>
				<td align="right" ondblclick="obligacion_documentos_insertar('<?=$iddoc?>');">
					<input type="hidden" name="MontoTotal" id="MontoTotal_<?=$iddoc?>" value="<?=$field_compras['MontoTotal']?>" />
					<?=number_format($field_compras['MontoTotal'], 2, ',', '.')?>
				</td>
				<td ondblclick="obligacion_documentos_insertar('<?=$iddoc?>');">
					<?=htmlentities($field_compras['NomAlmacen'])?>
				</td>
				<td ondblclick="obligacion_documentos_insertar('<?=$iddoc?>');">
					<?=htmlentities($field_compras['NomAlmacenIngreso'])?>
				</td>
				<td align="center" ondblclick="obligacion_documentos_insertar('<?=$iddoc?>');">
					<?=htmlentities($field_compras['NomFormaPago'])?>
				</td>
				<td align="center" ondblclick="obligacion_documentos_insertar('<?=$iddoc?>');">
					<?=printValoresGeneral("ESTADO-ORDENES", $field_compras['Estado'])?>
				</td>
			</tr>
			<?php
		}
	}
	?>
</table>
</div>
</div>

<div id="tab3" style="display:none;">
<div style="overflow:scroll; width:1000px; height:225px;">
<table width="1500" class="tblLista">
	<thead>
	<tr>
		<th scope="col" width="90">Nro. Orden</th>
		<th scope="col" width="75">Fecha Preparaci&oacute;n</th>
		<th scope="col">Descripcion</th>
		<th scope="col" width="100">Monto A Pagar</th>
		<th scope="col" width="100">Monto Pendiente</th>
		<th scope="col" width="100">Monto Pagado</th>
		<th scope="col" width="100">Monto Total</th>
		<th scope="col" width="125">Forma de Pago</th>
		<th scope="col" width="100">Estado</th>
	</tr>
	</thead>
	<?php
	//	consulto lista
	$sql = "SELECT 
				os.*,
				fp.Descripcion AS NomFormaPago,
				(SELECT SUM(MontoTotal)
				 FROM ap_documentos
				 WHERE
					ReferenciaTipoDocumento = 'OS' AND
					Anio = os.Anio AND
					CodOrganismo = os.CodOrganismo AND
					ReferenciaNroDocumento = os.NroOrden AND
					Estado = 'RV') AS MontoDocumento
			FROM 
				lg_ordenservicio os
				LEFT JOIN mastformapago fp ON (os.CodFormaPago = fp.CodFormaPago)
			WHERE
				os.Estado = 'AP' AND
				os.CodProveedor = '".$CodProveedor."'";
	$query_servicios = mysql_query($sql) or die ($sql.mysql_error());
	while ($field_servicios = mysql_fetch_array($query_servicios)) {
		$iddoc = "OS-".$field_servicios['Anio']."-".$field_servicios['NroOrden']."-SER";
		$MontoPendiente = $field_servicios['TotalMontoIva'] - $field_servicios['MontoDocumento'];
		if ($MontoPendiente > 0) {
			?>
			<tr class="trListaBody" id="<?=$idoc?>">
				<td align="center" ondblclick="obligacion_documentos_insertar('<?=$iddoc?>');">
					<?=$field_servicios['NroOrden']?>
				</td>
				<td align="center" ondblclick="obligacion_documentos_insertar('<?=$iddoc?>');">
					<?=formatFechaDMA($field_servicios['FechaPreparacion'])?>
				</td>
				<td ondblclick="obligacion_documentos_insertar('<?=$iddoc?>');">
					<?=htmlentities($field_servicios['Descripcion'])?>
				</td>
				<td align="right">
					<input type="text" name="Monto" id="Monto_<?=$iddoc?>" value="<?=number_format($MontoPendiente, 2, ',', '.')?>" style="text-align:right;" class="cell" onfocus="numeroFocus(this);" onblur="numeroBlur(this);" />
				</td>
				<td align="right" ondblclick="obligacion_documentos_insertar('<?=$iddoc?>');">
					<input type="hidden" name="MontoPendiente" id="MontoPendiente_<?=$iddoc?>" value="<?=$MontoPendiente?>" />
					<?=number_format($MontoPendiente, 2, ',', '.')?>
				</td>
				<td align="right" ondblclick="obligacion_documentos_insertar('<?=$iddoc?>');">
					<input type="hidden" name="MontoPagado" id="MontoPagado_<?=$iddoc?>" value="<?=$field_servicios['MontoGastado']?>" />
					<?=number_format($field_servicios['MontoGastado'], 2, ',', '.')?>
				</td>
				<td align="right" ondblclick="obligacion_documentos_insertar('<?=$iddoc?>');">
					<input type="hidden" name="MontoTotal" id="MontoTotal_<?=$iddoc?>" value="<?=$field_servicios['TotalMontoIva']?>" />
					<?=number_format($field_servicios['TotalMontoIva'], 2, ',', '.')?>
				</td>
				<td align="center" ondblclick="obligacion_documentos_insertar('<?=$iddoc?>');">
					<?=htmlentities($field_servicios['NomFormaPago'])?>
				</td>
				<td align="center" ondblclick="obligacion_documentos_insertar('<?=$iddoc?>');">
					<?=printValoresGeneral("ESTADO-ORDENES", $field_servicios['Estado'])?>
				</td>
			</tr>
			<?php
		}
	}
	?>
</table>
</div>
</div>
</center>
</form>
</body>
</html>