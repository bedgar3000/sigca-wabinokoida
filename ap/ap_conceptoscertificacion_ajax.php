<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion-".$AnioActual.$MesActual.$DiaActual."_".$HorasActual.$MinutosActual.$SegundosActual.".sql", "w+");
##############################################################################/
##	Conceptos de Compromisos Directos (NUEVO, MODIFICAR, ELIMINAR, AJAX)
##############################################################################/
if ($modulo == "formulario") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		##	-----------------
		##	valido
		if (!trim($Descripcion) || !trim($Categoria) || !trim($cod_partida) || (!trim($CodCuenta) && !trim($CodCuentaPub20))) die("Debe llenar los campos (*) obligatorios.");
		##	codigo
		$CodConcepto = codigo('ap_conceptoscertificacion','CodConcepto',3);
		##	inserto
		$Cuenta = ($CodCuenta?"CodCuenta = '".$CodCuenta."',":"CodCuenta = NULL,");
		$CuentaPub20 = ($CodCuentaPub20?"CodCuentaPub20 = '".$CodCuentaPub20."',":"CodCuentaPub20 = NULL,");
		$sql = "INSERT INTO ap_conceptoscertificacion
				SET
					CodConcepto = '".$CodConcepto."',
					Descripcion = '".$Descripcion."',
					Categoria = '".$Categoria."',
					cod_partida = '".$cod_partida."',
					$Cuenta
					$CuentaPub20
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
		if (!trim($Descripcion) || !trim($Categoria) || !trim($cod_partida) || (!trim($CodCuenta) && !trim($CodCuentaPub20))) die("Debe llenar los campos (*) obligatorios.");
		##	actualizo
		$Cuenta = ($CodCuenta?"CodCuenta = '".$CodCuenta."',":"CodCuenta = NULL,");
		$CuentaPub20 = ($CodCuentaPub20?"CodCuentaPub20 = '".$CodCuentaPub20."',":"CodCuentaPub20 = NULL,");
		$sql = "UPDATE ap_conceptoscertificacion
				SET
					Descripcion = '".$Descripcion."',
					Categoria = '".$Categoria."',
					cod_partida = '".$cod_partida."',
					$Cuenta
					$CuentaPub20
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodConcepto = '".$CodConcepto."'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "DELETE FROM ap_conceptoscertificacion WHERE CodConcepto = '".$registro."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
?>