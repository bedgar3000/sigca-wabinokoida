<?php
//	FUNCION PARA CARGAR SELECTS 
function loadSelectValores($tabla, $codigo=NULL, $opt=0) {
	switch ($tabla) {
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
	}
	
	$i=0;
	foreach ($c as $cod) {
		if ($cod == $codigo) return $v[$i];
		$i++;
	}
}

//	FUNCION PARA CARGAR SELECTS 
function loadSelectAniosVoucher($Anio=NULL, $opt=0) {
	$sql = "SELECT SUBSTRING(Periodo, 1, 4) AS Anio
			FROM ac_vouchermast
			GROUP BY Anio
			ORDER BY Anio DESC";
	$field = getRecords($sql);
	foreach ($field as $f) {
		if ($Anio == $f['Anio']) echo "<option value='".$f['Anio']."' selected>".$f['Anio']."</option>";
		else echo "<option value='".$f['Anio']."'>".$f['Anio']."</option>";
		$i++;
	}
}
?>