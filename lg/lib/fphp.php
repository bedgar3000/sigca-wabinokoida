<?php
//	FUNCION PARA CARGAR SELECTS 
function loadSelectValores($tabla, $codigo, $opt) {
	switch ($tabla) {
		case "ESTADO-REQUERIMIENTO":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "RV"; $v[] = "Revisado";
			$c[] = "CN"; $v[] = "Conformado";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "AN"; $v[] = "Anulado";
			$c[] = "RE"; $v[] = "Rechazado";
			$c[] = "CE"; $v[] = "Cerrado";
			$c[] = "CO"; $v[] = "Completado";
			break;
			
		case "ESTADO-REQUERIMIENTO-CC":
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "CO"; $v[] = "Completado";
			$c[] = "AP/CO"; $v[] = "Aprobado / Completado";
			break;

		case "ESTADO-REQUERIMIENTO2":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "RV"; $v[] = "Revisado";
			$c[] = "CN"; $v[] = "Conformado";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "AN"; $v[] = "Anulado";
			$c[] = "RE"; $v[] = "Rechazado";
			$c[] = "CE"; $v[] = "Cerrado";
			$c[] = "CO"; $v[] = "Completado";
			$c[] = "AP/CO"; $v[] = "Aprobado / Completado";
			break;
		
		case "ESTADO-REQUERIMIENTO-DETALLE":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "AN"; $v[] = "Anulado";
			$c[] = "CE"; $v[] = "Cerrado";
			$c[] = "CO"; $v[] = "Completado";
			break;
			
		case "ESTADO-TRANSACCION":
			$c[] = "PR"; $v[] = "Pendiente";
			$c[] = "CO"; $v[] = "Ejecutado";
			$c[] = "AN"; $v[] = "Anulado";
			break;
			
		case "ESTADO-COMPROMISO":
			$c[] = "RV"; $v[] = "Revisada";
			$c[] = "AP"; $v[] = "Aprobada";
			$c[] = "CO"; $v[] = "Completada";
			break;
			
		case "COMPRA-CLASIFICACION":
			$c[] = "L"; $v[] = "O/C Local";
			$c[] = "F"; $v[] = "O/C Foráneo";
			break;
		
		case "DIRIGIDO":
			$c[] = "C"; $v[] = "Compras";
			$c[] = "A"; $v[] = "Almacen";
			break;
		
		case "ITEM-ORDERBY":
			$c[] = "CodItem"; $v[] = "Código";
			$c[] = "CodInterno"; $v[] = "Código Interno";
			$c[] = "Descripcion"; $v[] = "Descripción";
			$c[] = "CodUnidad"; $v[] = "Unidad";
			$c[] = "NomTipoItem"; $v[] = "Tipo";
			$c[] = "NomProcedencia"; $v[] = "Procedencia";
			$c[] = "CodLinea,CodFamilia,CodSubFamilia"; $v[] = "Familia";
			$c[] = "PartidaPresupuestal"; $v[] = "Partida";
			$c[] = "CtaGasto"; $v[] = "Cta. Gasto";
			$c[] = "CtaGastoPub20"; $v[] = "Cta. Gasto (Pub.20)";
			break;
			
		case "ESTADO-COMPRA-FILTRO":
			$c[] = "PR"; $v[] = "En Preparacion";
			$c[] = "RV"; $v[] = "Revisada";
			$c[] = "AP"; $v[] = "Aprobada";
			$c[] = "AN"; $v[] = "Anulada";
			$c[] = "RE"; $v[] = "Rechazada";
			$c[] = "CE"; $v[] = "Cerrada";
			$c[] = "CO"; $v[] = "Completada";
			$c[] = "AP/CO"; $v[] = "Aprobada/Completada";
			break;
			
		case "ESTADO-COMPRA-DETALLE":
			$c[] = "PR"; $v[] = "En Preparacion";
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "RE"; $v[] = "Rechazada";
			$c[] = "CE"; $v[] = "Cerrada";
			$c[] = "CO"; $v[] = "Completada";
			break;

		case "guia-remision-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "CN"; $v[] = "Confirmado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "guia-remision-estado-factura":
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "FA"; $v[] = "Facturado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "guia-remision-estado-despacho":
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "DE"; $v[] = "Despachado";
			$c[] = "CO"; $v[] = "Completado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "co_documento-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "FA"; $v[] = "Facturado";
			$c[] = "AN"; $v[] = "Anulado";
			$c[] = "RE"; $v[] = "Rechazado";
			$c[] = "CA"; $v[] = "Canjeado";
			$c[] = "CO"; $v[] = "Cobrado";
			$c[] = "CT"; $v[] = "Castigado";
			break;

		case "co_documento-estado-detalle":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "FA"; $v[] = "Facturado";
			$c[] = "AN"; $v[] = "Anulado";
			$c[] = "RE"; $v[] = "Rechazado";
			$c[] = "CA"; $v[] = "Canjeado";
			$c[] = "CO"; $v[] = "Cobrado";
			$c[] = "RE"; $v[] = "Castigado";
			break;
			
		case "almacen-tipo":
			$c[] = "P"; $v[] = "Principal";
			$c[] = "T"; $v[] = "Tránsito";
			$c[] = "V"; $v[] = "Venta";
			break;
	}
	
	$i = 0;
	switch ($opt) {
		case 0:
			foreach ($c as $cod) {
				if ($cod == $codigo) echo "<option value='".$cod."' selected>".$v[$i]."</option>";
				else echo "<option value='".$cod."'>".$v[$i]."</option>";
				$i++;
			}
			break;
			
		case 1:
			foreach ($c as $cod) {
				if ($cod == $codigo) echo "<option value='".$cod."' selected>".$v[$i]."</option>";
				$i++;
			}
			break;
	}
}

//	FUNCION PARA IMPRIMIR EN UNA TABLA VALORES
function printValores($tabla, $codigo) {
	switch ($tabla) {
		case "ESTADO-REQUERIMIENTO":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "RV"; $v[] = "Revisado";
			$c[] = "CN"; $v[] = "Conformado";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "AN"; $v[] = "Anulado";
			$c[] = "RE"; $v[] = "Rechazado";
			$c[] = "CE"; $v[] = "Cerrado";
			$c[] = "CO"; $v[] = "Completado";
			break;
		
		case "ESTADO-REQUERIMIENTO-DETALLE":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "AN"; $v[] = "Anulado";
			$c[] = "CE"; $v[] = "Cerrado";
			$c[] = "CO"; $v[] = "Completado";
			break;
			
		case "ESTADO-TRANSACCION":
			$c[] = "PR"; $v[] = "Pendiente";
			$c[] = "CO"; $v[] = "Ejecutado";
			$c[] = "AN"; $v[] = "Anulado";
			break;
		
		case "DIRIGIDO":
			$c[] = "C"; $v[] = "Compras";
			$c[] = "A"; $v[] = "Almacen";
			break;
		
		case "COMPRA-CLASIFICACION":
			$c[] = "L"; $v[] = "O/C Local";
			$c[] = "F"; $v[] = "O/C Foráneo";
			break;
			
		case "PRIORIDAD":
			$c[] = "N"; $v[] = "Normal";
			$c[] = "U"; $v[] = "Urgente";
			$c[] = "M"; $v[] = "Muy Urgente";
			break;
		
		case "DIRIGIDO":
			$c[] = "C"; $v[] = "Compras";
			$c[] = "A"; $v[] = "Almacen";
			break;
		
		case "ESTADO-COMPROMISO":
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "CO"; $v[] = "Comprometido";
			break;
			
		case "ESTADO-COMPRA-DETALLE":
			$c[] = "PR"; $v[] = "En Preparacion";
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "RE"; $v[] = "Rechazada";
			$c[] = "CE"; $v[] = "Cerrada";
			$c[] = "CO"; $v[] = "Completada";
			break;

		case "guia-remision-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "CN"; $v[] = "Confirmado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "guia-remision-estado-factura":
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "FA"; $v[] = "Facturado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "guia-remision-estado-despacho":
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "DE"; $v[] = "Despachado";
			$c[] = "CO"; $v[] = "Completado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "co_documento-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "FA"; $v[] = "Facturado";
			$c[] = "AN"; $v[] = "Anulado";
			$c[] = "RE"; $v[] = "Rechazado";
			$c[] = "CA"; $v[] = "Canjeado";
			$c[] = "CO"; $v[] = "Cobrado";
			$c[] = "CT"; $v[] = "Castigado";
			break;

		case "co_documento-estado-detalle":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "FA"; $v[] = "Facturado";
			$c[] = "AN"; $v[] = "Anulado";
			$c[] = "RE"; $v[] = "Rechazado";
			$c[] = "CA"; $v[] = "Canjeado";
			$c[] = "CO"; $v[] = "Cobrado";
			$c[] = "RE"; $v[] = "Castigado";
			break;
			
		case "almacen-tipo":
			$c[] = "P"; $v[] = "Principal";
			$c[] = "T"; $v[] = "Tránsito";
			$c[] = "V"; $v[] = "Venta";
			break;
	}
	
	$i=0;
	foreach ($c as $cod) {
		if ($cod == $codigo) return $v[$i];
		$i++;
	}
}

//	FUNCION PARA CARGAR SELECTS
function loadSelectClasificacion($codigo, $opt) {
	switch ($opt) {
		case 0:
			$sql = "SELECT Clasificacion, Descripcion FROM lg_clasificacion WHERE Clasificacion <> 'RAU' ORDER BY Clasificacion";
			$query = mysql_query($sql) or die ($sql.mysql_error());
			while ($field = mysql_fetch_array($query)) {
				if ($field[0] == $codigo) { ?><option value="<?=$field[0]?>" selected="selected"><?=($field[1])?></option><?php }
				else { ?><option value="<?=$field[0]?>"><?=($field[1])?></option><?php }
			}
			break;
			
		case 1:
			$sql = "SELECT Clasificacion, Descripcion FROM lg_clasificacion WHERE Clasificacion = '".$codigo."' ORDER BY Clasificacion";
			$query = mysql_query($sql) or die ($sql.mysql_error());
			while ($field = mysql_fetch_array($query)) {
				?><option value="<?=$field[0]?>" selected="selected"><?=($field[1])?></option><?php
			}
			break;
	}
}

//	obtengo el almacen de la clasificacion seleccionada
function setAlmacenFromClasificacion($Clasificacion) {
	$sql = "SELECT CodAlmacen FROM lg_clasificacion WHERE Clasificacion = '".$Clasificacion."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
	return $field['CodAlmacen'];
}
?>