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
		$Monto = setNumero($Monto);
		##	valido
		if (!trim($CodPersonaCajero) || !trim($Monto)) die("Debe llenar los campos (*) obligatorios.");
		##	codigo
		$CodFondoCaja = codigo('co_fondocajahistorico','CodFondoCaja',3);
		##	inserto
		$sql = "INSERT INTO co_fondocaja
				SET
					CodFondoCaja = '$CodFondoCaja',
					CodPersonaCajero = '$CodPersonaCajero',
					Monto = '$Monto',
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()";
		execute($sql);
		##	historico
		$sql = "INSERT INTO co_fondocajahistorico
				SET
					CodFondoCaja = '$CodFondoCaja',
					Secuencia = '1',
					Fecha = NOW(),
					Monto = '$Monto',
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()";
		##	-----------------
		mysql_query("COMMIT");
	}
	//	modificar
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		##	-----------------
		$Monto = setNumero($Monto);
		##	valido
		if (!trim($Monto)) die("Debe llenar los campos (*) obligatorios.");
		##	actualizo
		$sql = "UPDATE co_fondocaja
				SET
					CodPersonaCajero = '$CodPersonaCajero',
					Monto = '$Monto',
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodFondoCaja = '$CodFondoCaja'";
		execute($sql);
		##	historico
		$Secuencia = codigo('co_fondocajahistorico','Secuencia',11,['CodFondoCaja'],[$CodFondoCaja]);
		$sql = "INSERT INTO co_fondocajahistorico
				SET
					CodFondoCaja = '$CodFondoCaja',
					Secuencia = '$Secuencia',
					Fecha = NOW(),
					Monto = '$Monto',
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()";
		##	-----------------
		mysql_query("COMMIT");
	}
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "DELETE FROM co_fondocaja WHERE CodFondoCaja = '$registro'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
?>