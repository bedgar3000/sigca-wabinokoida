<?php
session_start();
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion.sql", "w+");
//	fwrite($__archivo, $sql.";\n\n");
///////////////////////////////////////////////////////////////////////////////
//	CENTROS DE ESTUDIOS (NUEVO, MODIFICAR, ELIMINAR)
///////////////////////////////////////////////////////////////////////////////
if ($modulo == "centro_estudio") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-----------------
		//	inserto
		$CodCentroEstudio = codigo("rh_centrosestudios", "CodCentroEstudio", 3);
		$sql = "INSERT INTO rh_centrosestudios
				SET
					CodCentroEstudio = '".$CodCentroEstudio."',
					Descripcion = '".changeUrl($Descripcion)."',
					Ubicacion = '".changeUrl($Ubicacion)."',
					FlagEstudio = '".$FlagEstudio."',
					FlagCurso = '".$FlagCurso."',
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
		$sql = "UPDATE rh_centrosestudios
				SET
					Descripcion = '".changeUrl($Descripcion)."',
					Ubicacion = '".changeUrl($Ubicacion)."',
					FlagEstudio = '".$FlagEstudio."',
					FlagCurso = '".$FlagCurso."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodCentroEstudio = '".$CodCentroEstudio."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "DELETE FROM rh_centrosestudios WHERE CodCentroEstudio = '".$registro."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
?>