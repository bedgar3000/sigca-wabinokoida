<?php
session_start();
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion.sql", "w+");
//	fwrite($__archivo, $sql.";\n\n");
///////////////////////////////////////////////////////////////////////////////
//	SUELDOS MINIMOS (NUEVO, MODIFICAR, ELIMINAR)
///////////////////////////////////////////////////////////////////////////////
if ($modulo == "sueldos_minimos") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-----------------
		##	inserto
		$Secuencia = codigo("mastsueldosmin", "Secuencia", 4);
		$sql = "INSERT INTO mastsueldosmin
				SET
					Secuencia = '".$Secuencia."',
					Periodo = '".$Periodo."',
					Fecha = '".formatFechaAMD($Fecha)."',
					NroResolucion = '".$NroResolucion."',
					NroGaceta = '".$NroGaceta."',
					Monto = '".setNumero($Monto)."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
	//	modificar
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		//	-----------------
		##	actualizar
		$sql = "UPDATE mastsueldosmin
				SET
					Periodo = '".$Periodo."',
					Fecha = '".formatFechaAMD($Fecha)."',
					NroResolucion = '".$NroResolucion."',
					NroGaceta = '".$NroGaceta."',
					Monto = '".setNumero($Monto)."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE Secuencia = '".$Secuencia."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "DELETE FROM mastsueldosmin WHERE Secuencia = '".$registro."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
?>