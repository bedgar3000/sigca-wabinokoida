<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion-".$AnioActual.$MesActual.$DiaActual."_".$HorasActual.$MinutosActual.$SegundosActual.".sql", "w+");
///////////////////////////////////////////////////////////////////////////////
//	CONFIRMACION DE CAJA CHICA (CONFIRMAR)
///////////////////////////////////////////////////////////////////////////////
if ($modulo == "formulario") {
	$CantidadRecibida = setNumero($CantidadRecibida);
	//	confirmar requerimiento
	if ($accion == "confirmar") {
		mysql_query("BEGIN");
		//	-----------------
		##	inserto
		$Anio = substr(formatFechaAMD($FechaConfirmadaPor),0,4);
		$Numero = codigo("lg_cajachicaconfirmacion", "Numero", 2, array('CodRequerimiento','Secuencia'), array($CodRequerimiento,$Secuencia));
		$NroConfirmacion = codigo("lg_cajachicaconfirmacion", "NroConfirmacion", 4, array('Anio'), array($Anio));
		$sql = "INSERT INTO lg_cajachicaconfirmacion
				SET
					CodRequerimiento = '".$CodRequerimiento."',
					Secuencia = '".$Secuencia."',
					Numero = '".$Numero."',
					Anio = '".$Anio."',
					NroConfirmacion = '".$NroConfirmacion."',
					CantidadRecibida = '".$CantidadRecibida."',
					ConfirmadaPor = '".$ConfirmadaPor."',
					FechaConfirmadaPor = '".formatFechaAMD($FechaConfirmadaPor)."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		##	actualizo
		$sql = "UPDATE lg_requerimientosdet
				SET CantidadRecibida = CantidadRecibida + ".$CantidadRecibida."
				WHERE
					CodRequerimiento = '".$CodRequerimiento."' AND
					Secuencia = '".$Secuencia."'";
		execute($sql);
		##	si se completo
		if ($CantidadRecibida == $CantidadPendiente) {
			##	actualizo
			$sql = "UPDATE lg_requerimientosdet
					SET Estado = 'CO'
					WHERE
						CodRequerimiento = '".$CodRequerimiento."' AND
						Secuencia = '".$Secuencia."'";
			execute($sql);
			##	consulto
			$sql = "SELECT Estado
					FROM lg_requerimientos
					WHERE
						CodRequerimiento = '".$CodRequerimiento."' AND
						Estado = 'PE'";
			$field_estado = getRecords($sql);
			if (!count($field_estado)) {
				##	actualizo
				$sql = "UPDATE lg_requerimientos
						SET Estado = 'CO'
						WHERE CodRequerimiento = '".$CodRequerimiento."'";
				execute($sql);
			}
		}
		//	-----------------
		mysql_query("COMMIT");
	}
	//	desconfirmar requerimiento
	elseif ($accion == "desconfirmar") {
		mysql_query("BEGIN");
		//	-----------------
		list($CodRequerimiento, $Secuencia, $Numero) = explode("_", $registro);
		##	
		$sql = "SELECT CantidadRecibida
				FROM lg_cajachicaconfirmacion
				WHERE
					CodRequerimiento = '".$CodRequerimiento."' AND
					Secuencia = '".$Secuencia."' AND
					Numero = '".$Numero."'";
		$CantidadRecibida = getVar3($sql);
		##	eliminar
		$sql = "DELETE FROM lg_cajachicaconfirmacion
				WHERE
					CodRequerimiento = '".$CodRequerimiento."' AND
					Secuencia = '".$Secuencia."' AND
					Numero = '".$Numero."'";
		execute($sql);
		##	actualizo
		$sql = "UPDATE lg_requerimientosdet
				SET CantidadRecibida = CantidadRecibida - ".floatval($CantidadRecibida)."
				WHERE
					CodRequerimiento = '".$CodRequerimiento."' AND
					Secuencia = '".$Secuencia."'";
		execute($sql);
		##	actualizo
		$sql = "UPDATE lg_requerimientosdet
				SET Estado = 'PE'
				WHERE
					CodRequerimiento = '".$CodRequerimiento."' AND
					Secuencia = '".$Secuencia."'";
		execute($sql);
		##	actualizo
		$sql = "UPDATE lg_requerimientos SET Estado = 'AP' WHERE CodRequerimiento = '".$CodRequerimiento."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
?>