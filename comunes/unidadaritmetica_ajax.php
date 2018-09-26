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
		$Valor = setNumero($Valor);
		$Fecha = formatFechaAMD($Fecha);
		##	valido
		if (!trim($Anio) || !trim($Fecha) || !trim($Valor)) die("Debe llenar los campos (*) obligatorios.");
		##	Secuencia
		$Secuencia = codigo('mastunidadaritmetica','Secuencia',6,['Anio'],[$Anio]);
		##	inserto
		$sql = "INSERT INTO mastunidadaritmetica
				SET
					Anio = '$Anio',
					Secuencia = '$Secuencia',
					Fecha = '$Fecha',
					Valor = '$Valor',
					GacetaOficial = '$GacetaOficial',
					NroDocumento = '$NroDocumento',
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
		$Valor = setNumero($Valor);
		$Fecha = formatFechaAMD($Fecha);
		##	valido
		if (!trim($Anio) || !trim($Fecha) || !trim($Valor)) die("Debe llenar los campos (*) obligatorios.");
		##	actualizo
		$sql = "UPDATE mastunidadaritmetica
				SET
					Fecha = '$Fecha',
					Valor = '$Valor',
					GacetaOficial = '$GacetaOficial',
					NroDocumento = '$NroDocumento',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE
					Anio = '$Anio'
					AND Secuencia = '$Secuencia'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		list($Anio, $Secuencia) = explode('_', $registro);
		//	elimino
		$sql = "DELETE FROM mastunidadaritmetica
				WHERE
					Anio = '$Anio'
					AND Secuencia = '$Secuencia'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
?>