<?php
session_start();
set_time_limit(-1);
ini_set('memory_limit','128M');

//	FUNCION PARA CARGAR SELECTS 
function loadSelectValores($tabla, $codigo=NULL, $opt=0) {
	switch ($tabla) {			
		case "ACTUALIZAR-PERSONA":
			$c[] = "Persona"; $v[] = "Persona";
			$c[] = "Empleado"; $v[] = "Empleado";
			$c[] = "Proveedor"; $v[] = "Proveedor";
			$c[] = "Cliente"; $v[] = "Cliente";
			$c[] = "Otro"; $v[] = "Otro";
			break;
			
		case "TIPO-PERSONA":
			$c[] = "EsEmpleado"; $v[] = "Empleado";
			$c[] = "EsProveedor"; $v[] = "Proveedor";
			$c[] = "EsCliente"; $v[] = "Cliente";
			$c[] = "EsOtro"; $v[] = "Otro";
			break;
			
		case "EMPLEADOS-AGRUPADOR":
			$c[] = "CodDependencia"; $v[] = "Dependencia";
			$c[] = "CodTipoNom"; $v[] = "Tipo de Nómina";
			break;
			
		case "columna-foda":
			$c[] = "F"; $v[] = "Fortalezas";
			$c[] = "O"; $v[] = "Oportunidades";
			$c[] = "D"; $v[] = "Debilidades";
			$c[] = "A"; $v[] = "Amenazas";
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
		case "columna-foda":
			$c[] = "F"; $v[] = "Fortalezas";
			$c[] = "O"; $v[] = "Oportunidades";
			$c[] = "D"; $v[] = "Debilidades";
			$c[] = "A"; $v[] = "Amenazas";
			break;
	}
	
	$i=0;
	foreach ($c as $cod) {
		if ($cod == $codigo) return $v[$i];
		$i++;
	}
}
?>