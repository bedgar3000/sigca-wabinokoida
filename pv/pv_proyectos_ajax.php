<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion-".$AnioActual.$MesActual.$DiaActual."_".$HorasActual.$MinutosActual.$SegundosActual.".sql", "w+");
##############################################################################/
##	Proyectos (NUEVO, MODIFICAR, ELIMINAR, AJAX)
##############################################################################/
if ($modulo == "formulario") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		##	-----------------
		##	valido
		if (!trim($IdSubPrograma) || !trim($CodProyecto) || !trim($Denominacion)) die("Debe llenar los campos (*) obligatorios.");
		##	codigo
		$IdProyecto = codigo('pv_proyectos','IdProyecto',4);
		##	inserto
		$sql = "INSERT INTO pv_proyectos
				SET
					IdProyecto = '".$IdProyecto."',
					IdSubPrograma = '".$IdSubPrograma."',
					CodProyecto = '".$CodProyecto."',
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
		$sql = "UPDATE pv_proyectos
				SET
					Denominacion = '".$Denominacion."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE IdProyecto = '".$IdProyecto."'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "DELETE FROM pv_proyectos WHERE IdProyecto = '".$registro."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
?>