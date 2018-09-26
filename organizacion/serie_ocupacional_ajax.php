<?php
session_start();
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion.sql", "w+");
//	fwrite($__archivo, $sql.";\n\n");
///////////////////////////////////////////////////////////////////////////////
//	SERIE OCUPACIONAL (NUEVO, MODIFICAR, ELIMINAR)
///////////////////////////////////////////////////////////////////////////////
if ($modulo == "serie_ocupacional") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-----------------
		//	inserto
		$sql = "INSERT INTO rh_serieocupacional
				SET
					CodSerieOcup = '".$CodSerieOcup."',
					SerieOcup = '".changeUrl($SerieOcup)."',
					CodGrupOcup = '".$CodGrupOcup."',
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
		$sql = "UPDATE rh_serieocupacional
				SET
					SerieOcup = '".changeUrl($SerieOcup)."',
					CodGrupOcup = '".$CodGrupOcup."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodSerieOcup = '".$CodSerieOcup."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "DELETE FROM rh_serieocupacional WHERE CodSerieOcup = '".$registro."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
?>