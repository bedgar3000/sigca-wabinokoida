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
		if (!trim($CodConceptoCaja) || !trim($Descripcion) || !trim($Tipo)) die("Debe llenar los campos (*) obligatorios.");
		else {
			$sql = "SELECT * FROM co_conceptocaja WHERE CodConceptoCaja = '$CodConceptoCaja'";
			$field_val = getRecords($sql);
			if (count($field_val)) die("Código ya ingresado");
		}
		##	inserto
		$sql = "INSERT INTO co_conceptocaja
				SET
					CodConceptoCaja = '$CodConceptoCaja',
					Descripcion = '$Descripcion',
					Tipo = '$Tipo',
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
		if (!trim($Descripcion) || !trim($Tipo)) die("Debe llenar los campos (*) obligatorios.");
		##	actualizo
		$sql = "UPDATE co_conceptocaja
				SET
					Descripcion = '$Descripcion',
					Tipo = '$Tipo',
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodConceptoCaja = '$CodConceptoCaja'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "DELETE FROM co_conceptocaja WHERE CodConceptoCaja = '$registro'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
?>