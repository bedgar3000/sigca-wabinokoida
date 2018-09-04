<?php
session_start();
set_time_limit(-1);
ini_set('memory_limit','128M');

//	FUNCION PARA CARGAR SELECTS 
function loadSelectValores($tabla, $codigo, $opt=0) {
	switch ($tabla) {
		case "tipo-relacion":
			$c[0] = "I"; $v[0] = "Interna";
			$c[1] = "E"; $v[1] = "Externa";
			break;
			
		case "tipo-habilidad":
			$c[0] = "H"; $v[0] = "Habilidad";
			$c[1] = "D"; $v[1] = "Destreza";
			$c[2] = "C"; $v[2] = "Conocimiento";
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
		case "":
			$c[0] = ""; $v[0] = "";
			break;
	}
	
	$i=0;
	foreach ($c as $cod) {
		if ($cod == $codigo) return $v[$i];
		$i++;
	}
}
?>