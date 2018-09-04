<?php
session_start();
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion.sql", "w+");
//	fwrite($__archivo, $sql.";\n\n");
///////////////////////////////////////////////////////////////////////////////
//	NIVELES DE TIPOS DE CARGO (NUEVO, MODIFICAR, ELIMINAR)
///////////////////////////////////////////////////////////////////////////////
if ($modulo == "nivel_tipo_cargo") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-----------------
		//	inserto
		$sql = "INSERT INTO rh_nivelclasecargo
				SET
					CodTipoCargo = '".$CodTipoCargo."',
					CodNivelClase = '".$CodNivelClase."',
					NivelClase = '".changeUrl($NivelClase)."',
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
		$sql = "UPDATE rh_nivelclasecargo
				SET
					NivelClase = '".changeUrl($NivelClase)."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodTipoCargo = '".$CodTipoCargo."' AND
					CodNivelClase = '".$CodNivelClase."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		list($CodTipoCargo, $CodNivelClase) = explode("_", $registro);
		$sql = "DELETE FROM rh_nivelclasecargo
				WHERE
					CodTipoCargo = '".$CodTipoCargo."' AND
					CodNivelClase = '".$CodNivelClase."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
?>