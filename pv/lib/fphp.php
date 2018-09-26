<?php
//	FUNCION PARA CARGAR SELECTS 
function loadSelectValores($tabla, $codigo, $opt=0) {
	switch ($tabla) {
		case "proyecto-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "RV"; $v[] = "Revisado";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "AN"; $v[] = "Anulado";
			$c[] = "GE"; $v[] = "Generado";
			break;

		case "presupuesto-estado":
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "reformulacion-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "ajustes-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "metas-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "GE"; $v[] = "Generado";
			$c[] = "RF"; $v[] = "Reformulado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "metas-reformulacion-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "GE"; $v[] = "Generado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "presupuesto-obras-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "GE"; $v[] = "Generado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "presupuesto-hacienda-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "AN"; $v[] = "Anulado";
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
		case "proyecto-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "RV"; $v[] = "Revisado";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "AN"; $v[] = "Anulado";
			$c[] = "GE"; $v[] = "Generado";
			break;

		case "presupuesto-estado":
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "reformulacion-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "ajustes-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "metas-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "GE"; $v[] = "Generado";
			$c[] = "RF"; $v[] = "Reformulado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "metas-reformulacion-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "GE"; $v[] = "Generado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "presupuesto-obras-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "GE"; $v[] = "Generado";
			$c[] = "AN"; $v[] = "Anulado";
			break;

		case "presupuesto-hacienda-estado":
			$c[] = "PR"; $v[] = "En Preparación";
			$c[] = "AP"; $v[] = "Aprobado";
			$c[] = "AN"; $v[] = "Anulado";
			break;
	}
	
	$i=0;
	foreach ($c as $cod) {
		if ($cod == $codigo) return $v[$i];
		$i++;
	}
}
?>