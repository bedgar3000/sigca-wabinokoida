<?php
session_start();
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion.sql", "w+");
//	fwrite($__archivo, $sql.";\n\n");
///////////////////////////////////////////////////////////////////////////////
//	TASA DE INTERES (NUEVO, MODIFICAR, ELIMINAR)
///////////////////////////////////////////////////////////////////////////////
if ($modulo == "tasa_interes") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-----------------
		//	inserto
		$sql = "INSERT INTO masttasainteres
				SET
					Periodo = '".$Periodo."',
					Porcentaje = '".setNumero($Porcentaje)."',
					Fecha = '".formatFechaAMD($Fecha)."',
					Estado = '".$Estado."',
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
		//	actualizo
		$sql = "UPDATE masttasainteres
				SET
					Porcentaje = '".setNumero($Porcentaje)."',
					Fecha = '".formatFechaAMD($Fecha)."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE Periodo = '".$Periodo."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
?>