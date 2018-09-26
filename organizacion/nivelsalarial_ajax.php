<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion-".$AnioActual.$MesActual.$DiaActual."_".$HorasActual.$MinutosActual.$SegundosActual.".sql", "w+");
##############################################################################/
##	Grado salarial (NUEVO, MODIFICAR, ELIMINAR, AJAX)
##############################################################################/
if ($modulo == "formulario") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		##	-----------------
		##	valido
		if (!trim($CategoriaCargo) || !trim($Grado) || !trim($Paso) || !trim($SueldoMinimo) || !trim($SueldoMaximo)) die("Debe llenar los campos (*) obligatorios.");
		elseif (is_nan(setNumero($SueldoMinimo))) die("Formato de Sueldo M&iacute;nimo incorrecto");
		elseif (is_nan(setNumero($SueldoMaximo))) die("Formato de Sueldo M&aacute;ximo incorrecto");
		elseif (setNumero($SueldoMinimo) > setNumero($SueldoMaximo)) die("Sueldo M&iacute;nimo no puede ser mayor a Sueldo M&aacute;ximo");
		else {
			$sql = "SELECT * FROM rh_nivelsalarial WHERE CategoriaCargo = '$CategoriaCargo' AND Grado = '$Grado' AND Paso = '$Paso'";
			$field = getRecord($sql);
			if (count($field)) die("Grado salarial ya ingresado");
		}
		##	codigo
		$CodNivel = codigo('rh_nivelsalarial','CodNivel',2);
		##	inserto
		$sql = "INSERT INTO rh_nivelsalarial
				SET
					CodNivel = '".$CodNivel."',
					CategoriaCargo = '".$CategoriaCargo."',
					Grado = '".$Grado."',
					Paso = '".$Paso."',
					Descripcion = '".$Descripcion."',
					SueldoMinimo = '".setNumero($SueldoMinimo)."',
					SueldoMaximo = '".setNumero($SueldoMaximo)."',
					SueldoPromedio = '".setNumero($SueldoPromedio)."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	modificar
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		##	-----------------
		##	valido
		if (!trim($SueldoMinimo) || !trim($SueldoMaximo)) die("Debe llenar los campos (*) obligatorios.");
		elseif (is_nan(setNumero($SueldoMinimo))) die("Formato de Sueldo M&iacute;nimo incorrecto");
		elseif (is_nan(setNumero($SueldoMaximo))) die("Formato de Sueldo M&aacute;ximo incorrecto");
		elseif (setNumero($SueldoMinimo) > setNumero($SueldoMaximo)) die("Sueldo M&iacute;nimo no puede ser mayor a Sueldo M&aacute;ximo");
		else {
			$sql = "SELECT * FROM rh_nivelsalarial WHERE CategoriaCargo = '$CategoriaCargo' AND Grado = '$Grado' AND Paso = '$Paso'";
			$field = getRecord($sql);
			if (count($field)) die("Grado salarial ya ingresado");
		}
		##	actualizo
		$sql = "UPDATE rh_nivelsalarial
				SET
					Descripcion = '".$Descripcion."',
					SueldoMinimo = '".setNumero($SueldoMinimo)."',
					SueldoMaximo = '".setNumero($SueldoMaximo)."',
					SueldoPromedio = '".setNumero($SueldoPromedio)."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodNivel = '".$CodNivel."'";
		execute($sql);
		##	ACTUALIZAR MASIVAMENTE LOS CARGOS Y EMPLEADOS
		$sql = "UPDATE rh_puestos SET Paso = '01';";
		execute($sql);
		$sql = "UPDATE rh_puestos p SET p.NivelSalarial = (SELECT ns.SueldoPromedio FROM rh_nivelsalarial ns WHERE ns.CategoriaCargo = p.CategoriaCargo AND ns.Grado = p.Grado AND ns.Paso = p.Paso);";
		execute($sql);
		$sql = "UPDATE mastempleado e
				SET e.SueldoActual = (SELECT ns.SueldoPromedio
									  FROM rh_puestos p
									  INNER JOIN rh_nivelsalarial ns ON (ns.CategoriaCargo = p.CategoriaCargo AND ns.Grado = p.Grado)
									  WHERE p.CodCargo = e.CodCargo AND ns.Paso = e.Paso);";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "DELETE FROM rh_nivelsalarial WHERE CodNivel = '".$registro."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
?>