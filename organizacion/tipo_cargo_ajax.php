<?php
session_start();
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion.sql", "w+");
//	fwrite($__archivo, $sql.";\n\n");
///////////////////////////////////////////////////////////////////////////////
//	IDIOMAS (NUEVO, MODIFICAR, ELIMINAR)
///////////////////////////////////////////////////////////////////////////////
if ($modulo == "tipo_cargo") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-----------------
		//	inserto
		$CodTipoCargo = codigo("rh_tipocargo", "CodTipoCargo", 3);
		$sql = "INSERT INTO rh_tipocargo
				SET
					CodTipoCargo = '".$CodTipoCargo."',
					TipCargo = '".changeUrl($TipCargo)."',
					Definicion = '".changeUrl($Definicion)."',
					Funcion = '".changeUrl($Funcion)."',
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
		$sql = "UPDATE rh_tipocargo
				SET
					TipCargo = '".changeUrl($TipCargo)."',
					Definicion = '".changeUrl($Definicion)."',
					Funcion = '".changeUrl($Funcion)."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodTipoCargo = '".$CodTipoCargo."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "DELETE FROM rh_tipocargo WHERE CodTipoCargo = '".$registro."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
?>