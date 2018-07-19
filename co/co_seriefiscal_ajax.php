<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion-".".sql", "w+");
##############################################################################/
if ($modulo == "formulario") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		##	-----------------
		##	valido
		if (!trim($CodOrganismo) || !trim($CodEstablecimiento)) die("Debe llenar los campos (*) obligatorios.");
		##	codigo
		$CodSerie = codigo('co_seriefiscal','CodSerie',6);
		$NroSerie = codigo('co_seriefiscal','CodSerie',3,['CodOrganismo'],[$CodOrganismo]);
		##	inserto
		$sql = "INSERT INTO co_seriefiscal
				SET
					CodSerie = '$CodSerie',
					CodOrganismo = '$CodOrganismo',
					CodEstablecimiento = '$CodEstablecimiento',
					NroSerie = '$NroSerie',
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
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
		if (!trim($CodOrganismo) || !trim($CodEstablecimiento)) die("Debe llenar los campos (*) obligatorios.");
		##	actualizo
		$sql = "UPDATE co_seriefiscal
				SET
					CodEstablecimiento = '$CodEstablecimiento',
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodSerie = '$CodSerie'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "DELETE FROM co_seriefiscal WHERE CodSerie = '$registro'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
?>