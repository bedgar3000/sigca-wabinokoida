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
		$FlagCredito = (!empty($FlagCredito)?'S':'N');
		##	valido
		if (!trim($Descripcion)) die("Debe llenar los campos (*) obligatorios.");
		##	codigo
		$CodFormaPago = codigo('mastformapago','CodFormaPago',3);
		##	inserto
		$sql = "INSERT INTO mastformapago
				SET
					CodFormaPago = '$CodFormaPago',
					Descripcion = '$Descripcion',
					FlagCredito = '$FlagCredito',
					DiasVence = '$DiasVence',
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
		$FlagCredito = (!empty($FlagCredito)?'S':'N');
		##	valido
		if (!trim($Descripcion)) die("Debe llenar los campos (*) obligatorios.");
		##	actualizo
		$sql = "UPDATE mastformapago
				SET
					Descripcion = '$Descripcion',
					FlagCredito = '$FlagCredito',
					DiasVence = '$DiasVence',
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodFormaPago = '$CodFormaPago'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "DELETE FROM mastformapago WHERE CodFormaPago = '$registro'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
?>