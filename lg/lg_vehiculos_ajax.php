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
		$iCodChofer = (!empty($CodChofer)?"CodChofer = '$CodChofer',":'');
		$iCodEmpresa = (!empty($CodEmpresa)?"CodEmpresa = '$CodEmpresa',":'');
		##	valido
		if (!required($Placa)) die("Debe llenar los campos (*) obligatorios.");
		elseif (!alpha_dash($Placa)) die("Formato Placa incorrecto");
		elseif (!is_unique('lg_vehiculos', 'Placa', $Placa)) die('Placa ya ingresada');
		##	codigo
		$CodVehiculo = codigo('lg_vehiculos','CodVehiculo',6);
		##	inserto
		$sql = "INSERT INTO lg_vehiculos
				SET
					CodVehiculo = '$CodVehiculo',
					Placa = '$Placa',
					$iCodChofer
					$iCodEmpresa
					Modelo = '$Modelo',
					Marca = '$Marca',
					Anio = '$Anio',
					Clase = '$Clase',
					Color = '$Color',
					Tipo = '$Tipo',
					Uso = '$Uso',
					SerialMotor = '$SerialMotor',
					SerialCarroceria = '$SerialCarroceria',
					Capacidad = '$Capacidad',
					Peso = '$Peso',
					FlagVehiculoPropio = '$FlagVehiculoPropio',
					Comentarios = '$Comentarios',
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
		$iCodChofer = (!empty($CodChofer)?"CodChofer = '$CodChofer',":'');
		$iCodEmpresa = (!empty($CodEmpresa)?"CodEmpresa = '$CodEmpresa',":'');
		##	valido
		##	inserto
		$sql = "UPDATE lg_vehiculos
				SET
					$iCodChofer
					$iCodEmpresa
					Modelo = '$Modelo',
					Marca = '$Marca',
					Anio = '$Anio',
					Clase = '$Clase',
					Color = '$Color',
					Tipo = '$Tipo',
					Uso = '$Uso',
					SerialMotor = '$SerialMotor',
					SerialCarroceria = '$SerialCarroceria',
					Capacidad = '$Capacidad',
					Peso = '$Peso',
					FlagVehiculoPropio = '$FlagVehiculoPropio',
					Comentarios = '$Comentarios',
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodVehiculo = '$CodVehiculo'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "DELETE FROM lg_vehiculos WHERE CodVehiculo = '$registro'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
?>