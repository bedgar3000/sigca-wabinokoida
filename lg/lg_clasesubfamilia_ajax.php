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
		if (!trim($Descripcion) || !trim($CodLinea) || !trim($CodFamilia)) die("Debe llenar los campos (*) obligatorios.");
		##	codigo
		$CodSubFamilia = codigo('lg_clasesubfamilia','CodSubFamilia',6,['CodLinea','CodFamilia'],[$CodLinea,$CodFamilia]);
		##	inserto
		$sql = "INSERT INTO lg_clasesubfamilia
				SET
					CodSubFamilia = '$CodSubFamilia',
					Descripcion = '$Descripcion',
					CodLinea = '$CodLinea',
					CodFamilia = '$CodFamilia',
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
		list($CodLinea, $CodFamilia, $CodSubFamilia) = explode('_', $sel_registros);
		##	valido
		if (!trim($Descripcion)) die("Debe llenar los campos (*) obligatorios.");
		##	actualizo
		$sql = "UPDATE lg_clasesubfamilia
				SET
					Descripcion = '$Descripcion',
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE
					CodLinea = '$CodLinea'
					AND CodFamilia = '$CodFamilia'
					AND CodSubFamilia = '$CodSubFamilia'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		list($CodLinea, $CodFamilia, $CodSubFamilia) = explode('_', $registro);
		//	actualizo
		$sql = "DELETE FROM lg_clasesubfamilia
				WHERE
					CodLinea = '$CodLinea'
					AND CodFamilia = '$CodFamilia'
					AND CodSubFamilia = '$CodSubFamilia'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
?>