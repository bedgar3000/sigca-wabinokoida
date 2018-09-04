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
		##	valido
		if (!trim($CodTipoTarjeta) || !trim($Descripcion) || !trim($CodTipoPago)) die("Debe llenar los campos (*) obligatorios.");
		else {
			$sql = "SELECT * FROM co_tipotarjeta WHERE CodTipoTarjeta = '$CodTipoTarjeta'";
			$codigo = getRecords($sql);
			if (count($codigo)) die("Código ya ingresado");
		}
		##	inserto
		$sql = "INSERT INTO co_tipotarjeta
				SET
					CodTipoTarjeta = '$CodTipoTarjeta',
					Descripcion = '$Descripcion',
					CodTipoPago = '$CodTipoPago',
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
		##	valido
		if (!trim($Descripcion) || !trim($CodTipoPago)) die("Debe llenar los campos (*) obligatorios.");
		##	actualizo
		$sql = "UPDATE co_tipotarjeta
				SET
					Descripcion = '$Descripcion',
					CodTipoPago = '$CodTipoPago',
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodTipoTarjeta = '$CodTipoTarjeta'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "DELETE FROM co_tipotarjeta WHERE CodTipoTarjeta = '$registro'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
?>