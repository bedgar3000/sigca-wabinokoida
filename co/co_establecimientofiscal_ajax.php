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
		$iCodAlmacen = (!empty($CodAlmacen)?"CodAlmacen = '$CodAlmacen',":"CodAlmacen = NULL,");
		##	valido
		if (!trim($Descripcion) || !trim($CodOrganismo)) die("Debe llenar los campos (*) obligatorios.");
		##	codigo
		$CodEstablecimiento = codigo('co_establecimientofiscal','CodEstablecimiento',3);
		##	inserto
		$sql = "INSERT INTO co_establecimientofiscal
				SET
					CodEstablecimiento = '$CodEstablecimiento',
					CodOrganismo = '$CodOrganismo',
					Descripcion = '$Descripcion',
					$iCodAlmacen
					Direccion = '$Direccion',
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
		$iCodAlmacen = (!empty($CodAlmacen)?"CodAlmacen = '$CodAlmacen',":"CodAlmacen = NULL,");
		##	valido
		if (!trim($Descripcion)) die("Debe llenar los campos (*) obligatorios.");
		##	actualizo
		$sql = "UPDATE co_establecimientofiscal
				SET
					Descripcion = '$Descripcion',
					Direccion = '$Direccion',
					$iCodAlmacen
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodEstablecimiento = '$CodEstablecimiento'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "DELETE FROM co_establecimientofiscal WHERE CodEstablecimiento = '$registro'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
elseif ($modulo == "ajax") {
	if ($accion == "set_direccion_almacen") {
		$sql = "SELECT * FROM lg_almacenmast WHERE CodAlmacen = '$CodAlmacen'";
		$field = getRecord($sql);
		if(empty($field['Direccion'])) $field['Direccion'] = '';

		die(json_encode(['Direccion' => $field['Direccion']]));
	}
}
?>