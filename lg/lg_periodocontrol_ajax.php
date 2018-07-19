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
		$FlagTransaccion = (!empty($FlagTransaccion)?'S':'N');
		##	valido
		if (!trim($CodOrganismo) || !trim($Periodo)) die("Debe llenar los campos (*) obligatorios.");
		else {
			$sql = "SELECT * FROM lg_periodocontrol
					WHERE
						CodOrganismo = '$CodOrganismo'
						AND Periodo = '$Periodo'";
			$codigo = getRecords($sql);
			if (count($codigo)) die("Periodo ya ingresado para el organismo");
		}
		##	inserto
		$sql = "INSERT INTO lg_periodocontrol
				SET
					CodOrganismo = '$CodOrganismo',
					Periodo = '$Periodo',
					FlagTransaccion = '$FlagTransaccion',
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
		$FlagTransaccion = (!empty($FlagTransaccion)?'S':'N');
		##	valido
		##	actualizo
		$sql = "UPDATE lg_periodocontrol
				SET
					FlagTransaccion = '$FlagTransaccion',
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '$CodOrganismo'
					AND Periodo = '$Periodo'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		list($CodOrganismo, $Periodo) = explode('_', $registro);
		//	actualizo
		$sql = "DELETE FROM lg_periodocontrol
				WHERE
					CodOrganismo = '$CodOrganismo'
					AND Periodo = '$Periodo'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
?>