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
		if (!trim($CodOrganismo) || !trim($CodTipoPago) || !trim($NroCuenta)) die("Debe llenar los campos (*) obligatorios.");
		else {
			$sql = "SELECT * FROM ap_ctabancariadefault WHERE CodOrganismo = '$CodOrganismo' AND CodTipoPago = '$CodTipoPago'";
			$codigo = getRecords($sql);
			if (count($codigo)) die("Tipo de Pago ya posee una Cuenta Bancaria asociada al Organismo");
		}
		##	inserto
		$sql = "INSERT INTO ap_ctabancariadefault
				SET
					CodOrganismo = '$CodOrganismo',
					CodTipoPago = '$CodTipoPago',
					NroCuenta = '$NroCuenta',
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
		if (!trim($NroCuenta)) die("Debe llenar los campos (*) obligatorios.");
		##	actualizo
		$sql = "UPDATE ap_ctabancariadefault
				SET
					NroCuenta = '$NroCuenta',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '$CodOrganismo'
					AND CodTipoPago = '$CodTipoPago'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		list($CodOrganismo, $CodTipoPago) = explode('_', $registro);
		//	eliminar
		$sql = "DELETE FROM ap_ctabancariadefault
				WHERE
					CodOrganismo = '$CodOrganismo'
					AND CodTipoPago = '$CodTipoPago'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
?>