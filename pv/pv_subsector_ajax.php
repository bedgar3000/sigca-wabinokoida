<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion-".$AnioActual.$MesActual.$DiaActual."_".$HorasActual.$MinutosActual.$SegundosActual.".sql", "w+");
##############################################################################/
##	Sub-sectores (NUEVO, MODIFICAR, ELIMINAR, AJAX)
##############################################################################/
if ($modulo == "formulario") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		##	-----------------
		##	valido
		if (!trim($CodSector) || !trim($CodSubSector) || !trim($Denominacion)) die("Debe llenar los campos (*) obligatorios.");
		##	codigo
		$IdSubSector = codigo('pv_subsector','IdSubSector',3);
		$CodClaSectorial = $CodSector . $CodSubSector;
		##	inserto
		$sql = "INSERT INTO pv_subsector
				SET
					IdSubSector = '".$IdSubSector."',
					CodSector = '".$CodSector."',
					CodSubSector = '".$CodSubSector."',
					CodClaSectorial = '".$CodClaSectorial."',
					Descripcion = '".$Descripcion."',
					Denominacion = '".$Denominacion."',
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
		if (!trim($Denominacion)) die("Debe llenar los campos (*) obligatorios.");
		##	actualizo
		$sql = "UPDATE pv_subsector
				SET
					Descripcion = '".$Descripcion."',
					Denominacion = '".$Denominacion."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE IdSubSector = '".$IdSubSector."'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "DELETE FROM pv_subsector WHERE IdSubSector = '".$registro."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
?>