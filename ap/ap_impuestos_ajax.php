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
		$FlagSustraendo = (!empty($FlagSustraendo)?'S':'N');
		$FactorPorcentaje = setNumero($FactorPorcentaje);
		$SustraendoUT = setNumero($SustraendoUT);
		$iCodCuenta = (!empty($CodCuenta)?"CodCuenta = '$CodCuenta',":"CodCuenta = NULL,");
		$iCodCuentaPub20 = (!empty($CodCuentaPub20)?"CodCuentaPub20 = '$CodCuentaPub20',":"CodCuentaPub20 = NULL,");
		$iCodCuentaRetVta = (!empty($CodCuentaRetVta)?"CodCuentaRetVta = '$CodCuentaRetVta',":"CodCuentaRetVta = NULL,");
		$iCodCuentaRetVtaPub20 = (!empty($CodCuentaRetVtaPub20)?"CodCuentaRetVtaPub20 = '$CodCuentaRetVtaPub20',":"CodCuentaRetVtaPub20 = NULL,");
		$iCodCuentaProvVta = (!empty($CodCuentaProvVta)?"CodCuentaProvVta = '$CodCuentaProvVta',":"CodCuentaProvVta = NULL,");
		$iCodCuentaProvVtaPub20 = (!empty($CodCuentaProvVtaPub20)?"CodCuentaProvVtaPub20 = '$CodCuentaProvVtaPub20',":"CodCuentaProvVtaPub20 = NULL,");
		##	valido
		if (!trim($CodImpuesto) || !trim($Descripcion) || !trim($CodRegimenFiscal) || !trim($FactorPorcentaje) || !trim($FlagProvision) || !trim($FlagImponible) || !trim($TipoComprobante)) die("Debe llenar los campos (*) obligatorios.");
		elseif ($FlagSustraendo == 'S' && !trim($SustraendoUT)) die('Debe ingresar el valor del sustraendo');
		elseif (!is_unique('mastimpuestos','CodImpuesto',$CodImpuesto)) die('Código ya ingresado');
		##	inserto
		$sql = "INSERT INTO mastimpuestos
				SET
					CodImpuesto = '$CodImpuesto',
					Descripcion = '$Descripcion',
					CodRegimenFiscal = '$CodRegimenFiscal',
					Signo = '$Signo',
					FactorPorcentaje = '$FactorPorcentaje',
					FlagProvision = '$FlagProvision',
					FlagImponible = '$FlagImponible',
					TipoComprobante = '$TipoComprobante',
					FlagSustraendo = '$FlagSustraendo',
					SustraendoUT = '$SustraendoUT',
					$icod_partida
					$iCodCuenta
					$iCodCuentaPub20
					$iCodCuentaRetVta
					$iCodCuentaRetVtaPub20
					$iCodCuentaProvVta
					$iCodCuentaProvVtaPub20
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
		$FlagSustraendo = (!empty($FlagSustraendo)?'S':'N');
		$FactorPorcentaje = setNumero($FactorPorcentaje);
		$SustraendoUT = setNumero($SustraendoUT);
		$iCodCuenta = (!empty($CodCuenta)?"CodCuenta = '$CodCuenta',":"CodCuenta = NULL,");
		$iCodCuentaPub20 = (!empty($CodCuentaPub20)?"CodCuentaPub20 = '$CodCuentaPub20',":"CodCuentaPub20 = NULL,");
		$iCodCuentaRetVta = (!empty($CodCuentaRetVta)?"CodCuentaRetVta = '$CodCuentaRetVta',":"CodCuentaRetVta = NULL,");
		$iCodCuentaRetVtaPub20 = (!empty($CodCuentaRetVtaPub20)?"CodCuentaRetVtaPub20 = '$CodCuentaRetVtaPub20',":"CodCuentaRetVtaPub20 = NULL,");
		$iCodCuentaProvVta = (!empty($CodCuentaProvVta)?"CodCuentaProvVta = '$CodCuentaProvVta',":"CodCuentaProvVta = NULL,");
		$iCodCuentaProvVtaPub20 = (!empty($CodCuentaProvVtaPub20)?"CodCuentaProvVtaPub20 = '$CodCuentaProvVtaPub20',":"CodCuentaProvVtaPub20 = NULL,");
		##	valido
		if (!trim($CodImpuesto) || !trim($Descripcion) || !trim($CodRegimenFiscal) || !trim($FactorPorcentaje) || !trim($FlagProvision) || !trim($FlagImponible) || !trim($TipoComprobante)) die("Debe llenar los campos (*) obligatorios.");
		elseif ($FlagSustraendo == 'S' && !trim($SustraendoUT)) die('Debe ingresar el valor del sustraendo');
		##	actualizo
		$sql = "UPDATE mastimpuestos
				SET
					Descripcion = '$Descripcion',
					CodRegimenFiscal = '$CodRegimenFiscal',
					Signo = '$Signo',
					FactorPorcentaje = '$FactorPorcentaje',
					FlagProvision = '$FlagProvision',
					FlagImponible = '$FlagImponible',
					TipoComprobante = '$TipoComprobante',
					FlagSustraendo = '$FlagSustraendo',
					SustraendoUT = '$SustraendoUT',
					$icod_partida
					$iCodCuenta
					$iCodCuentaPub20
					$iCodCuentaRetVta
					$iCodCuentaRetVtaPub20
					$iCodCuentaProvVta
					$iCodCuentaProvVtaPub20
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodImpuesto = '$CodImpuesto'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "DELETE FROM mastimpuestos WHERE CodImpuesto = '$registro'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
?>