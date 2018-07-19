<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion-".".sql", "w+");
##############################################################################/
if ($modulo == "formulario") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		##	-----------------
		$FlagSupervisor = (!empty($FlagSupervisor)?'S':'N');
		$FlagVendedor = (!empty($FlagVendedor)?'S':'N');
		$FechaIngreso = formatFechaAMD($FechaIngreso);
		$CodPersonaVendedor = (!empty($CodPersonaVendedor)?"'$CodPersonaVendedor'":"NULL");
		##	valido
		if (!trim($CodPersona)) die("Debe llenar los campos (*) obligatorios.");
		elseif (!trim($CodPersonaVendedor) && $FlagVendedor == 'S') die("Debe seleccionar el Vendedor");
		##	codigo
		$CodCajero = codigo('co_cajeros','CodCajero',6);
		##	inserto
		$sql = "INSERT INTO co_cajeros
				SET
					CodCajero = '$CodCajero',
					CodPersona = '$CodPersona',
					CodPersonaVendedor = $CodPersonaVendedor,
					FechaIngreso = '$FechaIngreso',
					FlagSupervisor = '$FlagSupervisor',
					FlagVendedor = '$FlagVendedor',
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	modificar
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		##	-----------------
		$FlagSupervisor = (!empty($FlagSupervisor)?'S':'N');
		$FlagVendedor = (!empty($FlagVendedor)?'S':'N');
		$FechaIngreso = formatFechaAMD($FechaIngreso);
		$CodPersonaVendedor = (!empty($CodPersonaVendedor)?"'$CodPersonaVendedor'":"NULL");
		##	valido
		if (!trim($CodPersonaVendedor) && $FlagVendedor == 'S') die("Debe seleccionar el Vendedor");
		##	actualizo
		$sql = "UPDATE co_cajeros
				SET
					CodPersonaVendedor = $CodPersonaVendedor,
					FechaIngreso = '$FechaIngreso',
					FlagSupervisor = '$FlagSupervisor',
					FlagVendedor = '$FlagVendedor',
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodCajero = '$CodCajero'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "DELETE FROM co_cajeros WHERE CodCajero = '$registro'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
?>