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
		$NroDesde = setNumero($NroDesde);
		$NroHasta = setNumero($NroHasta);
		$UltNroEmitido = setNumero($UltNroEmitido);
		$iCodSerie = (!empty($CodSerie)?"CodSerie = '$CodSerie',":"CodSerie = NULL,");
		##	valido
		if (!trim($CodOrganismo) || !trim($Descripcion) || !trim($CodTipoDocumento) || trim($UltNroEmitido) == '' || !trim($NroDesde) || !trim($NroHasta)) die("Debe llenar los campos (*) obligatorios.");
		elseif (!integer($NroDesde)) die("Rango Desde debe contener números enteros");
		elseif (!integer($NroHasta)) die("Rango Hasta debe contener números enteros");
		elseif (!integer($UltNroEmitido)) die("Nro. Actual debe contener números enteros");
		elseif (intval($NroDesde) > intval($NroHasta)) die("Rango Desde no puede ser mayor a Rango Hasta");
		##	codigo
		$CodCorrelativo = codigo('co_correlativodocumento','CodCorrelativo',6);
		##	inserto
		$sql = "INSERT INTO co_correlativodocumento
				SET
					CodCorrelativo = '$CodCorrelativo',
					CodOrganismo = '$CodOrganismo',
					CodTipoDocumento = '$CodTipoDocumento',
					$iCodSerie
					Descripcion = '$Descripcion',
					NroDesde = '$NroDesde',
					NroHasta = '$NroHasta',
					UltNroEmitido = '$UltNroEmitido',
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
		$NroDesde = setNumero($NroDesde);
		$NroHasta = setNumero($NroHasta);
		$UltNroEmitido = setNumero($UltNroEmitido);
		##	valido
		if (!trim($Descripcion) || trim($UltNroEmitido) == '' || !trim($NroDesde) || !trim($NroHasta)) die("Debe llenar los campos (*) obligatorios.");
		elseif (!integer($NroDesde)) die("Rango Desde debe contener números enteros");
		elseif (!integer($NroHasta)) die("Rango Hasta debe contener números enteros");
		elseif (!integer($UltNroEmitido)) die("Nro. Actual debe contener números enteros");
		elseif (intval($NroDesde) > intval($NroHasta)) die("Rango Desde no puede ser mayor a Rango Hasta");
		##	actualizo
		$sql = "UPDATE co_correlativodocumento
				SET
					Descripcion = '$Descripcion',
					NroDesde = '$NroDesde',
					NroHasta = '$NroHasta',
					UltNroEmitido = '$UltNroEmitido',
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodCorrelativo = '$CodCorrelativo'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "DELETE FROM co_correlativodocumento WHERE CodCorrelativo = '$registro'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
?>