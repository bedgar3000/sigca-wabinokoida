<?php
//	FUNCION PARA CARGAR SELECTS 
function loadSelectValores($tabla, $codigo, $opt=0) {
	switch ($tabla) {
		case "plan-obras-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "GE"; $v[] = "Generado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "plan-obras-tipo":
			$c[] = "PU"; $v[] = "Dominio Público";
			$c[] = "PR"; $v[] = "Dominio Privado";
			break;

		case "plan-obras-situacion":
			$c[] = "AI"; $v[] = "A Iniciar";
			$c[] = "EJ"; $v[] = "En Ejecución";
			$c[] = "TE"; $v[] = "Terminado";
			$c[] = "PA"; $v[] = "Paralizado";
			break;

		case "obras-documento":
			$c[] = "RE"; $v[] = "Resolución";
			$c[] = "DE"; $v[] = "Decreto";
			$c[] = "PC"; $v[] = "Puto de Cuenta";
			$c[] = "EX"; $v[] = "Expediente";
			$c[] = "CO"; $v[] = "Contrato";
			break;

		case "obras-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "GE"; $v[] = "Generado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "obras-situacion":
			$c[] = "AI"; $v[] = "A Iniciar";
			$c[] = "EJ"; $v[] = "En Ejecución";
			$c[] = "TE"; $v[] = "Terminado";
			$c[] = "PA"; $v[] = "Paralizado";
			break;

		case "valuacion-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "GE"; $v[] = "Generado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "cotizacion-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "AP"; $v[] = "Aprobada";
			$c[] = "CO"; $v[] = "Completada";
			$c[] = "AN"; $v[] = "Anulada";
			break;

		case "cotizacion-estado-detalle":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "CO"; $v[] = "Completado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "cotizacion-tipo-item":
			$c[] = "I"; $v[] = "Item";
			$c[] = "S"; $v[] = "Servicio";
			break;

		case "documento1-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "FA"; $v[] = "Facturado";
			$c[] = "AN"; $v[] = "Anulado";
			$c[] = "RE"; $v[] = "Rechazado";
			break;

		case "documento1-estado-detalle":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "FA"; $v[] = "Facturado";
			$c[] = "AN"; $v[] = "Anulado";
			$c[] = "RE"; $v[] = "Rechazado";
			break;

		case "documento2-estado":
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "CA"; $v[] = "Canjeado";
			$c[] = "CO"; $v[] = "Cobrado";
			$c[] = "AN"; $v[] = "Anulado";
			$c[] = "CT"; $v[] = "Castigado";
			break;

		case "documento2-estado-detalle":
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "CA"; $v[] = "Canjeado";
			$c[] = "CO"; $v[] = "Cobrado";
			$c[] = "AN"; $v[] = "Anulado";
			$c[] = "CT"; $v[] = "Castigado";
			break;

		case "documento3-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "FA"; $v[] = "Facturado";
			$c[] = "AN"; $v[] = "Anulado";
			$c[] = "RE"; $v[] = "Rechazado";
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "CA"; $v[] = "Canjeado";
			$c[] = "CO"; $v[] = "Cobrado";
			$c[] = "AN"; $v[] = "Anulado";
			$c[] = "CT"; $v[] = "Castigado";
			break;

		case "documento3-estado-detalle":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "FA"; $v[] = "Facturado";
			$c[] = "CA"; $v[] = "Canjeado";
			$c[] = "CO"; $v[] = "Cobrado";
			$c[] = "CT"; $v[] = "Castigado";
			$c[] = "AN"; $v[] = "Anulado";
			$c[] = "RE"; $v[] = "Rechazado";
			break;

		case "cobranza-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "cobranza-estado-detalle":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "concepto-caja-tipo":
			$c[] = "I"; $v[] = "Ingreso";
			$c[] = "E"; $v[] = "Egreso";
			break;

		case "cierre-caja-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "arqueo-caja-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "registro-ventas-estado":
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "CO"; $v[] = "Completado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "registro-ventas-sistema-fuente":
			$c[] = "DO"; $v[] = "Documentos";
			$c[] = "MA"; $v[] = "Manual";
			break;

		case "documento-pagos":
			$c[] = "PP"; $v[] = "Adelantos Pendientes de Pago";
			$c[] = "PA"; $v[] = "Pagados";
			break;

		case "servicios-digitos":
			$c[] = "2"; $v[] = "2";
			$c[] = "4"; $v[] = "4";
			$c[] = "6"; $v[] = "6";
			$c[] = "8"; $v[] = "8";
			$c[] = "10"; $v[] = "10";
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
		case "plan-obras-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "GE"; $v[] = "Generado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "plan-obras-tipo":
			$c[] = "PU"; $v[] = "Dominio Público";
			$c[] = "PR"; $v[] = "Dominio Privado";
			break;

		case "plan-obras-situacion":
			$c[] = "AI"; $v[] = "A Iniciar";
			$c[] = "EJ"; $v[] = "En Ejecución";
			$c[] = "TE"; $v[] = "Terminado";
			$c[] = "PA"; $v[] = "Paralizado";
			break;

		case "obras-documento":
			$c[] = "RE"; $v[] = "Resolución";
			$c[] = "DE"; $v[] = "Decreto";
			$c[] = "PC"; $v[] = "Puto de Cuenta";
			$c[] = "EX"; $v[] = "Expediente";
			$c[] = "CO"; $v[] = "Contrato";
			break;

		case "obras-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "GE"; $v[] = "Generado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "obras-situacion":
			$c[] = "AI"; $v[] = "A Iniciar";
			$c[] = "EJ"; $v[] = "En Ejecución";
			$c[] = "TE"; $v[] = "Terminado";
			$c[] = "PA"; $v[] = "Paralizado";
			break;

		case "valuacion-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "GE"; $v[] = "Generado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "cotizacion-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "AP"; $v[] = "Aprobada";
			$c[] = "CO"; $v[] = "Completada";
			$c[] = "AN"; $v[] = "Anulada";
			break;

		case "cotizacion-estado-detalle":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "CO"; $v[] = "Completado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "cotizacion-tipo-item":
			$c[] = "I"; $v[] = "Item";
			$c[] = "S"; $v[] = "Servicio";
			break;

		case "documento1-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "FA"; $v[] = "Facturado";
			$c[] = "AN"; $v[] = "Anulado";
			$c[] = "RE"; $v[] = "Rechazado";
			break;

		case "documento1-estado-detalle":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "FA"; $v[] = "Facturado";
			$c[] = "AN"; $v[] = "Anulado";
			$c[] = "RE"; $v[] = "Rechazado";
			break;

		case "documento2-estado":
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "CA"; $v[] = "Canjeado";
			$c[] = "CO"; $v[] = "Cobrado";
			$c[] = "AN"; $v[] = "Anulado";
			$c[] = "CT"; $v[] = "Castigado";
			break;

		case "documento2-estado-detalle":
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "CA"; $v[] = "Canjeado";
			$c[] = "CO"; $v[] = "Cobrado";
			$c[] = "AN"; $v[] = "Anulado";
			$c[] = "CT"; $v[] = "Castigado";
			break;

		case "cobranza-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "documento3-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "FA"; $v[] = "Facturado";
			$c[] = "AN"; $v[] = "Anulado";
			$c[] = "RE"; $v[] = "Rechazado";
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "CA"; $v[] = "Canjeado";
			$c[] = "CO"; $v[] = "Cobrado";
			$c[] = "AN"; $v[] = "Anulado";
			$c[] = "CT"; $v[] = "Castigado";
			break;

		case "documento3-estado-detalle":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "FA"; $v[] = "Facturado";
			$c[] = "CA"; $v[] = "Canjeado";
			$c[] = "CO"; $v[] = "Cobrado";
			$c[] = "CT"; $v[] = "Castigado";
			$c[] = "AN"; $v[] = "Anulado";
			$c[] = "RE"; $v[] = "Rechazado";
			break;

		case "cobranza-estado-detalle":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "concepto-caja-tipo":
			$c[] = "I"; $v[] = "Ingreso";
			$c[] = "E"; $v[] = "Egreso";
			break;

		case "cierre-caja-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "arqueo-caja-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "registro-ventas-estado":
			$c[] = "PE"; $v[] = "Pendiente";
			$c[] = "CO"; $v[] = "Completado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "registro-ventas-sistema-fuente":
			$c[] = "DO"; $v[] = "Documentos";
			$c[] = "MA"; $v[] = "Manual";
			break;

		case "documento-pagos":
			$c[] = "PP"; $v[] = "Adelantos Pendientes de Pago";
			$c[] = "PA"; $v[] = "Pagados";
			break;
	}
	
	$i=0;
	foreach ($c as $cod) {
		if ($cod == $codigo) return $v[$i];
		$i++;
	}
}

//	FUNCTION 
function co_nro_documento($campos=NULL, $valores=NULL) {
	$filtro = "";
	if ($campos) {
		for($i=0;$i<count($campos);$i++) {
			$filtro .= " AND $campos[$i] = '$valores[$i]'";
		}
	}
	$sql = "SELECT MAX(SUBSTRING(NroDocumento, 4, 7)) FROM co_documento WHERE 1 $filtro";
	$max = getVar3($sql);
	$codigo = intval($max) + 1;
	$codigo = (string) str_repeat("0", 7-strlen($codigo)).$codigo;
	return $codigo;
}

//	
function co_conceptocaja($CodConceptoCaja = NULL, $opt = 0) {
	$html = '';
	$filtro = '';
	if ($opt == 1) {
		$filtro .= " AND CodConceptoCaja = '$CodConceptoCaja'";
	}
	$sql = "SELECT * FROM co_conceptocaja WHERE 1 $filtro ORDER BY Tipo, CodConceptoCaja";
	$field = getRecords($sql);
	$grupo = '';
	foreach ($field as $f) 
	{
		if ($grupo != $f['Tipo'])
		{
			if ($grupo) $html .= '</optgroup>';
			$html .= '<optgroup label="'.printValores('concepto-caja-tipo',$f['Tipo']).'">';
			$grupo = $f['Tipo'];
		}

		if ($f['CodConceptoCaja'] == $CodConceptoCaja)
			$html .= '<option value="'.$f['CodConceptoCaja'].'" selected>'.$f['CodConceptoCaja'].' '.htmlentities($f['Descripcion']).'</option>';
		else
			$html .= '<option value="'.$f['CodConceptoCaja'].'">'.$f['CodConceptoCaja'].' '.htmlentities($f['Descripcion']).'</option>';
	}
	$html .= '</optgroup>';

	return $html;
}
?>