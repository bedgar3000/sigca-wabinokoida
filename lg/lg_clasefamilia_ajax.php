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
		$iCuentaInventario = (!empty($CuentaInventario)?"CuentaInventario = '$CuentaInventario',":'');
		$iCuentaGasto = (!empty($CuentaGasto)?"CuentaGasto = '$CuentaGasto',":'');
		$iCuentaInventarioPub20 = (!empty($CuentaInventarioPub20)?"CuentaInventarioPub20 = '$CuentaInventarioPub20',":'');
		$iCuentaGastoPub20 = (!empty($CuentaGastoPub20)?"CuentaGastoPub20 = '$CuentaGastoPub20',":'');
		$iCuentaVentas = (!empty($CuentaVentas)?"CuentaVentas = '$CuentaVentas',":'');
		$iCuentaVentasPub20 = (!empty($CuentaVentasPub20)?"CuentaVentasPub20 = '$CuentaVentasPub20',":'');
		$iCuentaTransito = (!empty($CuentaTransito)?"CuentaTransito = '$CuentaTransito',":'');
		$iCuentaTransitoPub20 = (!empty($CuentaTransitoPub20)?"CuentaTransitoPub20 = '$CuentaTransitoPub20',":'');
		$iPartidaPresupuestal = (!empty($PartidaPresupuestal)?"PartidaPresupuestal = '$PartidaPresupuestal',":'');
		##	valido
		if (!trim($Descripcion) || !trim($CodLinea)) die("Debe llenar los campos (*) obligatorios.");
		##	codigo
		$CodFamilia = codigo('lg_clasefamilia','CodFamilia',6,['CodLinea'],[$CodLinea]);
		##	inserto
		$sql = "INSERT INTO lg_clasefamilia
				SET
					CodFamilia = '$CodFamilia',
					Descripcion = '$Descripcion',
					CodLinea = '$CodLinea',
					$iCuentaInventario
					$iCuentaGasto
					$iCuentaVentas
					$iCuentaTransito
					$iCuentaInventarioPub20
					$iCuentaGastoPub20
					$iCuentaVentasPub20
					$iCuentaTransitoPub20
					$iPartidaPresupuestal
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
		list($CodLinea, $CodFamilia) = explode('_', $sel_registros);
		$iCuentaInventario = (!empty($CuentaInventario)?"CuentaInventario = '$CuentaInventario',":'');
		$iCuentaGasto = (!empty($CuentaGasto)?"CuentaGasto = '$CuentaGasto',":'');
		$iCuentaInventarioPub20 = (!empty($CuentaInventarioPub20)?"CuentaInventarioPub20 = '$CuentaInventarioPub20',":'');
		$iCuentaGastoPub20 = (!empty($CuentaGastoPub20)?"CuentaGastoPub20 = '$CuentaGastoPub20',":'');
		$iCuentaVentas = (!empty($CuentaVentas)?"CuentaVentas = '$CuentaVentas',":'');
		$iCuentaVentasPub20 = (!empty($CuentaVentasPub20)?"CuentaVentasPub20 = '$CuentaVentasPub20',":'');
		$iCuentaTransito = (!empty($CuentaTransito)?"CuentaTransito = '$CuentaTransito',":'');
		$iCuentaTransitoPub20 = (!empty($CuentaTransitoPub20)?"CuentaTransitoPub20 = '$CuentaTransitoPub20',":'');
		$iPartidaPresupuestal = (!empty($PartidaPresupuestal)?"PartidaPresupuestal = '$PartidaPresupuestal',":'');
		##	valido
		if (!trim($Descripcion)) die("Debe llenar los campos (*) obligatorios.");
		##	actualizo
		$sql = "UPDATE lg_clasefamilia
				SET
					Descripcion = '$Descripcion',
					$iCuentaInventario
					$iCuentaGasto
					$iCuentaVentas
					$iCuentaTransito
					$iCuentaInventarioPub20
					$iCuentaGastoPub20
					$iCuentaVentasPub20
					$iCuentaTransitoPub20
					$iPartidaPresupuestal
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE
					CodLinea = '$CodLinea'
					AND CodFamilia = '$CodFamilia'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		list($CodLinea, $CodFamilia) = explode('_', $registro);
		//	actualizo
		$sql = "DELETE FROM lg_clasefamilia
				WHERE
					CodLinea = '$CodLinea'
					AND CodFamilia = '$CodFamilia'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
?>