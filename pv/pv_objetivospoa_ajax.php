<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion-".$AnioActual.$MesActual.$DiaActual."_".$HorasActual.$MinutosActual.$SegundosActual.".sql", "w+");
##############################################################################/
##	Objetivos (NUEVO, MODIFICAR, ELIMINAR, AJAX)
##############################################################################/
if ($modulo == "formulario") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		##	-----------------
		##	valido
		if (!trim($CategoriaProg) || !trim($TipoObjetivo) || !trim($Descripcion)) die("Debe llenar los campos (*) obligatorios.");
		##	codigo
		$CodObjetivo = codigo('pv_objetivospoa','CodObjetivo',6);
		$NroObjetivo = codigo('pv_objetivospoa','NroObjetivo',6,['CategoriaProg'],[$CategoriaProg]);
		##	inserto
		$sql = "INSERT INTO pv_objetivospoa
				SET
					CodObjetivo = '".$CodObjetivo."',
					CategoriaProg = '".$CategoriaProg."',
					NroObjetivo = '".$NroObjetivo."',
					TipoObjetivo = '".$TipoObjetivo."',
					Descripcion = '".$Descripcion."',
					Responsable = '".$Responsable."',
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
		if (!trim($CategoriaProg) || !trim($TipoObjetivo) || !trim($Descripcion)) die("Debe llenar los campos (*) obligatorios.");
		##	actualizo
		$sql = "UPDATE pv_objetivospoa
				SET
					TipoObjetivo = '".$TipoObjetivo."',
					Descripcion = '".$Descripcion."',
					Responsable = '".$Responsable."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodObjetivo = '".$CodObjetivo."'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "DELETE FROM pv_objetivospoa WHERE CodObjetivo = '".$registro."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
?>