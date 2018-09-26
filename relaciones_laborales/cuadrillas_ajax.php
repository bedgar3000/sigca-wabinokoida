<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion-".$AnioActual.$MesActual.$DiaActual."_".$HorasActual.$MinutosActual.$SegundosActual.".sql", "w+");
//	$__archivo = fopen("_"."$modulo-$accion".".sql", "w+");
##############################################################################/
##	Actividads (NUEVO, MODIFICAR, ELIMINAR, AJAX)
##############################################################################/
if ($modulo == "formulario") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		##	-----------------
		##	valido
		if (!trim($CodCiudad) || !trim($CodComunidad) || !trim($Denominacion)) die("Debe llenar los campos (*) obligatorios.");
		##	codigo
		$CodCuadrilla = codigo('rh_cuadrillas','CodCuadrilla',4);
		##	inserto
		$sql = "INSERT INTO rh_cuadrillas
				SET
					CodCuadrilla = '".$CodCuadrilla."',
					CodCiudad = '".$CodCiudad."',
					CodComunidad = '".$CodComunidad."',
					Denominacion = '".$Denominacion."',
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
		if (!trim($Denominacion)) die("Debe llenar los campos (*) obligatorios.");
		##	actualizo
		$sql = "UPDATE rh_cuadrillas
				SET
					CodCiudad = '".$CodCiudad."',
					CodComunidad = '".$CodComunidad."',
					Denominacion = '".$Denominacion."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodCuadrilla = '".$CodCuadrilla."'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "DELETE FROM rh_cuadrillas WHERE CodCuadrilla = '".$registro."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
?>