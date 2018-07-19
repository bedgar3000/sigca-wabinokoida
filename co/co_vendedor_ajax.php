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
		$FechaIngreso = formatFechaAMD($FechaIngreso);
		##	valido
		if (!trim($CodPersona) || !trim($EquipoVenta)) die("Debe llenar los campos (*) obligatorios.");
		##	codigo
		$CodVendedor = codigo('co_vendedor','CodVendedor',6);
		##	inserto
		$sql = "INSERT INTO co_vendedor
				SET
					CodVendedor = '$CodVendedor',
					CodPersona = '$CodPersona',
					EquipoVenta = '$EquipoVenta',
					FechaIngreso = '$FechaIngreso',
					FlagSupervisor = '$FlagSupervisor',
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
		$FechaIngreso = formatFechaAMD($FechaIngreso);
		##	valido
		if (!trim($CodPersona) || !trim($EquipoVenta)) die("Debe llenar los campos (*) obligatorios.");
		##	actualizo
		$sql = "UPDATE co_vendedor
				SET
					EquipoVenta = '$EquipoVenta',
					FechaIngreso = '$FechaIngreso',
					FlagSupervisor = '$FlagSupervisor',
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodVendedor = '$CodVendedor'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "DELETE FROM co_vendedor WHERE CodVendedor = '$registro'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
?>