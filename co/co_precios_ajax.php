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
		$FlagVigIndefinida = (!empty($FlagVigIndefinida)?'S':'N');
		$FechaVigDesde = formatFechaAMD($FechaVigDesde);
		$FechaVigHasta = formatFechaAMD($FechaVigHasta);
		$PrecioMayor = getNumero($PrecioMayor);
		$PrecioMenor = getNumero($PrecioMenor);
		$PrecioEspecial = getNumero($PrecioEspecial);
		$PrecioEspecialVta = getNumero($PrecioEspecialVta);
		$PrecioCosto = getNumero($PrecioCosto);
		$CantidadMayor = getNumero($CantidadMayor);
		$PorcentajeDcto1 = getNumero($PorcentajeDcto1);
		$PorcentajeDcto2 = getNumero($PorcentajeDcto2);
		$PorcentajeDcto3 = getNumero($PorcentajeDcto3);
		$PorcMargen = getNumero($PorcMargen);
		$PrecioCostoUnitario = getNumero($PrecioCostoUnitario);
		$PrecioUnitario = getNumero($PrecioUnitario);
		$CodPersona = (!empty($CodPersona)?"'$CodPersona'":"NULL");
		##	valido
		if (!trim($CodOrganismo) || !trim($CodItem) || !trim($CodUnidad) || !trim($TipoDetalle) || !trim($PrecioMayor) || !trim($PrecioMenor) || !trim($PrecioEspecial) || !trim($PrecioCosto) || !trim($CantidadMayor) || !trim($PorcentajeDcto1) || !trim($PorcentajeDcto2) || !trim($PorcentajeDcto3)) die("Debe llenar los campos (*) obligatorios.");
		elseif ((!trim($FechaVigDesde) || !trim($FechaVigHasta)) && $FlagVigIndefinida != 'S') die("Debe ingresar el Periodo de Vigencia");
		elseif (($FechaVigDesde > $FechaVigHasta) && $FlagVigIndefinida != 'S') die("Periodo de Vigencia incorrecto");
		else {
			$sql = "SELECT *
					FROM co_precios
					WHERE
						TipoDetalle = '$TipoDetalle'
						AND CodItem = '$CodItem'";
			$field_valido = getRecords($sql);
			if (count($field_valido)) die('Precio ya ingresado para este Item');
		}
		##	codigo
		$CodPrecio = codigo('co_precios','CodPrecio',10);
		##	inserto
		$sql = "INSERT INTO co_precios
				SET
					CodPrecio = '$CodPrecio',
					CodOrganismo = '$CodOrganismo',
					CodItem = '$CodItem',
					CodUnidad = '$CodUnidad',
					CodUnidadVenta = '$CodUnidadVenta',
					TipoDetalle = '$TipoDetalle',
					CodPersona = $CodPersona,
					FlagVigIndefinida = '$FlagVigIndefinida',
					FechaVigDesde = '$FechaVigDesde',
					FechaVigHasta = '$FechaVigHasta',
					MontoVenta = '$PrecioMenor',
					PrecioMayor = '$PrecioMayor',
					PrecioMenor = '$PrecioMenor',
					PrecioEspecial = '$PrecioEspecial',
					PrecioEspecialVta = '$PrecioEspecialVta',
					PrecioCosto = '$PrecioCosto',
					CantidadMayor = '$CantidadMayor',
					PrecioUnitario = '$PrecioUnitario',
					PrecioCostoUnitario = '$PrecioCostoUnitario',
					PorcMargen = '$PorcMargen',
					PorcentajeDcto1 = '$PorcentajeDcto1',
					PorcentajeDcto2 = '$PorcentajeDcto2',
					PorcentajeDcto3 = '$PorcentajeDcto3',
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
		$FechaVigDesde = formatFechaAMD($FechaVigDesde);
		$FechaVigHasta = formatFechaAMD($FechaVigHasta);
		$MontoVenta = getNumero($MontoVenta);
		$PrecioMayor = getNumero($PrecioMayor);
		$PrecioMenor = getNumero($PrecioMenor);
		$PrecioEspecial = getNumero($PrecioEspecial);
		$PrecioEspecialVta = getNumero($PrecioEspecialVta);
		$PrecioCosto = getNumero($PrecioCosto);
		$CantidadMayor = getNumero($CantidadMayor);
		$PorcentajeDcto1 = getNumero($PorcentajeDcto1);
		$PorcentajeDcto2 = getNumero($PorcentajeDcto2);
		$PorcentajeDcto3 = getNumero($PorcentajeDcto3);
		$PorcMargen = getNumero($PorcMargen);
		$PrecioCostoUnitario = getNumero($PrecioCostoUnitario);
		$PrecioUnitario = getNumero($PrecioUnitario);
		$CodPersona = (!empty($CodPersona)?"'$CodPersona'":"NULL");
		##	valido
		if (!trim($PrecioMayor) || !trim($PrecioMenor) || !trim($PrecioEspecial) || !trim($PrecioCosto) || !trim($CantidadMayor) || !trim($PorcentajeDcto1) || !trim($PorcentajeDcto2) || !trim($PorcentajeDcto3)) die("Debe llenar los campos (*) obligatorios.");
		elseif ((!trim($FechaVigDesde) || !trim($FechaVigHasta)) && $FlagVigIndefinida != 'S') die("Debe ingresar el Periodo de Vigencia");
		elseif (($FechaVigDesde > $FechaVigHasta) && $FlagVigIndefinida != 'S') die("Periodo de Vigencia incorrecto");
		##	actualizo
		$sql = "UPDATE co_precios
				SET
					CodPersona = $CodPersona,
					FlagVigIndefinida = '$FlagVigIndefinida',
					FechaVigDesde = '$FechaVigDesde',
					FechaVigHasta = '$FechaVigHasta',
					MontoVenta = '$PrecioMenor',
					PrecioMayor = '$PrecioMayor',
					PrecioMenor = '$PrecioMenor',
					PrecioEspecial = '$PrecioEspecial',
					PrecioEspecialVta = '$PrecioEspecialVta',
					PrecioCosto = '$PrecioCosto',
					CantidadMayor = '$CantidadMayor',
					PrecioUnitario = '$PrecioUnitario',
					PrecioCostoUnitario = '$PrecioCostoUnitario',
					PorcMargen = '$PorcMargen',
					PorcentajeDcto1 = '$PorcentajeDcto1',
					PorcentajeDcto2 = '$PorcentajeDcto2',
					PorcentajeDcto3 = '$PorcentajeDcto3',
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodPrecio = '$CodPrecio'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "DELETE FROM co_precios WHERE CodPrecio = '$registro'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
?>