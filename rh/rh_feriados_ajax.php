<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion-".$AnioActual.$MesActual.$DiaActual."_".$HorasActual.$MinutosActual.$SegundosActual.".sql", "w+");
##############################################################################/
##	Feriados (NUEVO, MODIFICAR, ELIMINAR, AJAX)
##############################################################################/
if ($modulo == "formulario") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		##	-----------------
		##	valido
		if (!trim($DiaFeriado) || !trim($Descripcion) || ($FlagVariable != 'S' && !trim($AnioFeriado))) die("Debe llenar los campos (*) obligatorios.");
		##	codigo
		$CodFeriado = codigo('rh_feriados','CodFeriado',4);
		##	inserto
		$sql = "INSERT INTO rh_feriados
				SET
					CodFeriado = '".$CodFeriado."',
					AnioFeriado = '".$AnioFeriado."',
					DiaFeriado = '".$DiaFeriado."',
					FlagVariable = '".($FlagVariable?'S':'N')."',
					Descripcion = '".$Descripcion."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
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
		if (!trim($DiaFeriado) || !trim($Descripcion)) die("Debe llenar los campos (*) obligatorios.");
		##	actualizo
		$sql = "UPDATE rh_feriados
				SET
					AnioFeriado = '".$AnioFeriado."',
					DiaFeriado = '".$DiaFeriado."',
					FlagVariable = '".($FlagVariable?'S':'N')."',
					Descripcion = '".$Descripcion."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodFeriado = '".$CodFeriado."'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "DELETE FROM rh_feriados WHERE CodFeriado = '".$registro."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
?>