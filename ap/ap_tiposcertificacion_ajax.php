<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion-".$AnioActual.$MesActual.$DiaActual."_".$HorasActual.$MinutosActual.$SegundosActual.".sql", "w+");
##############################################################################/
##	Tipos de Certificación (NUEVO, MODIFICAR, ELIMINAR, AJAX)
##############################################################################/
if ($modulo == "formulario") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		##	-----------------
		##	valido
		if (!trim($Descripcion) || !trim($CodTipoDocumento)) die("Debe llenar los campos (*) obligatorios.");
		##	codigo
		$CodTipoCertif = codigo('ap_tiposcertificacion','CodTipoCertif',2);
		##	inserto
		$sql = "INSERT INTO ap_tiposcertificacion
				SET
					CodTipoCertif = '".$CodTipoCertif."',
					Descripcion = '".$Descripcion."',
					CodTipoDocumento = '".$CodTipoDocumento."',
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
		if (!trim($Descripcion)) die("Debe llenar los campos (*) obligatorios.");
		##	actualizo
		$sql = "UPDATE ap_tiposcertificacion
				SET
					Descripcion = '".$Descripcion."',
					CodTipoDocumento = '".$CodTipoDocumento."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodTipoCertif = '".$CodTipoCertif."'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "DELETE FROM ap_tiposcertificacion WHERE CodTipoCertif = '".$registro."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
?>