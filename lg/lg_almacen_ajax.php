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
		$FlagProduccion = (!empty($FlagProduccion)?'S':'N');
		$FlagVenta = (!empty($FlagVenta)?'S':'N');
		$FlagCommodity = (!empty($FlagCommodity)?'S':'N');
		$iAlmacenTransito = (!empty($AlmacenTransito)?"'$AlmacenTransito'":"NULL");
		$iCodPersona = (!empty($CodPersona)?"'$CodPersona'":"NULL");
		$iCuentaInventario = (!empty($CuentaInventario)?"'$CuentaInventario'":"NULL");
		$iCuentaInventarioPub20 = (!empty($CuentaInventarioPub20)?"'$CuentaInventarioPub20'":"NULL");
		##	valido
		if (!trim($CodAlmacen) || !trim($Descripcion) || !trim($CodOrganismo) || !trim($CodDependencia) || !trim($TipoAlmacen)) die("Debe llenar los campos (*) obligatorios.");
		elseif (!trim($AlmacenTransito) && $TipoAlmacen == 'T') die("Debe seleccionar el Almacén de Tránsito");
		elseif (!is_unique('lg_almacenmast','CodAlmacen',$CodAlmacen)) die('Código ya ingresado');
		##	inserto
		$sql = "INSERT INTO lg_almacenmast
				SET
					CodAlmacen = '$CodAlmacen',
					Descripcion = '$Descripcion',
					CodOrganismo = '$CodOrganismo',
					CodDependencia = '$CodDependencia',
					TipoAlmacen = '$TipoAlmacen',
					Direccion = '$Direccion',
					FlagVenta = '$FlagVenta',
					FlagProduccion = '$FlagProduccion',
					FlagCommodity = '$FlagCommodity',
					AlmacenTransito = $iAlmacenTransito,
					CodPersona = $iCodPersona,
					CuentaInventario = $iCuentaInventario,
					CuentaInventarioPub20 = $iCuentaInventarioPub20,
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
		$FlagProduccion = (!empty($FlagProduccion)?'S':'N');
		$FlagVenta = (!empty($FlagVenta)?'S':'N');
		$FlagCommodity = (!empty($FlagCommodity)?'S':'N');
		$iAlmacenTransito = (!empty($AlmacenTransito)?"'$AlmacenTransito'":"NULL");
		$iCodPersona = (!empty($CodPersona)?"'$CodPersona'":"NULL");
		$iCuentaInventario = (!empty($CuentaInventario)?"'$CuentaInventario'":"NULL");
		$iCuentaInventarioPub20 = (!empty($CuentaInventarioPub20)?"'$CuentaInventarioPub20'":"NULL");
		##	valido
		if (!trim($Descripcion) || !trim($CodOrganismo) || !trim($CodDependencia) || !trim($TipoAlmacen)) die("Debe llenar los campos (*) obligatorios.");
		elseif (!trim($AlmacenTransito) && $TipoAlmacen == 'T') die("Debe seleccionar el Almacén de Tránsito");
		##	actualizo
		$sql = "UPDATE lg_almacenmast
				SET
					Descripcion = '$Descripcion',
					CodOrganismo = '$CodOrganismo',
					CodDependencia = '$CodDependencia',
					TipoAlmacen = '$TipoAlmacen',
					Direccion = '$Direccion',
					FlagVenta = '$FlagVenta',
					FlagProduccion = '$FlagProduccion',
					FlagCommodity = '$FlagCommodity',
					AlmacenTransito = $iAlmacenTransito,
					CodPersona = $iCodPersona,
					CuentaInventario = $iCuentaInventario,
					CuentaInventarioPub20 = $iCuentaInventarioPub20,
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodAlmacen = '$CodAlmacen'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "DELETE FROM lg_almacenmast WHERE CodAlmacen = '$registro'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
