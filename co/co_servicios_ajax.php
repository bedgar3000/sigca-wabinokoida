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
		$FlagExoneradoIva = (!empty($FlagExoneradoIva)?'S':'N');
		$FlagAfectoDescuento = (!empty($FlagAfectoDescuento)?'S':'N');
		$PrecioVenta = setNumero($PrecioVenta);
		$Digitos = strlen($CodInterno);
		$DigitosPadre = strlen($NroPadre);
		$iPartidaIngreso = (!empty($PartidaIngreso)?"PartidaIngreso = '$PartidaIngreso',":'');
		$iCodCuentaOncop = (!empty($CodCuentaOncop)?"CodCuentaOncop = '$CodCuentaOncop',":'');
		$iCodCuentaPub20 = (!empty($CodCuentaPub20)?"CodCuentaPub20 = '$CodCuentaPub20',":'');
		$iCodPadre = (!empty($CodPadre)?"CodPadre = '$CodPadre',":'');
		##	valido
		if (!trim($CodInterno) || !trim($Descripcion) || !trim($CodClasificacion) || trim($PrecioVenta) == '') die("Debe llenar los campos (*) obligatorios.");
		elseif ($Digitos <> ($DigitosPadre + 2)) die("El Código debe tener ".($DigitosPadre + 2)." digitos");
		elseif ($NroPadre <> substr($CodInterno, 0, $DigitosPadre) && !empty($CodPadre)) die("´Codigo no concuerda con el del servicio padre.");
		elseif (!is_unique('co_mastservicios', 'CodInterno', $CodInterno)) die("Código ya ingresado");
		//elseif ($Digitos <> strlen($CodInterno)) die("El C&oacute;digo debe tener exactamente $Digitos digitos");
		##	codigo
		$CodServicio = codigo('co_mastservicios','CodServicio',6);
		##	inserto
		$sql = "INSERT INTO co_mastservicios
				SET
					CodServicio = '$CodServicio',
					CodInterno = '$CodInterno',
					Descripcion = '$Descripcion',
					Digitos = '$Digitos',
					CodClasificacion = '$CodClasificacion',
					$iPartidaIngreso
					$iCodCuentaOncop
					$iCodCuentaPub20
					FlagExoneradoIva = '$FlagExoneradoIva',
					FlagAfectoDescuento = '$FlagAfectoDescuento',
					PrecioVenta = '$PrecioVenta',
					$iCodPadre
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
		$FlagExoneradoIva = (!empty($FlagExoneradoIva)?'S':'N');
		$FlagAfectoDescuento = (!empty($FlagAfectoDescuento)?'S':'N');
		$PrecioVenta = setNumero($PrecioVenta);
		$Digitos = strlen($CodInterno);
		$DigitosPadre = strlen($NroPadre);
		$iPartidaIngreso = (!empty($PartidaIngreso)?"PartidaIngreso = '$PartidaIngreso',":'');
		$iCodCuentaOncop = (!empty($CodCuentaOncop)?"CodCuentaOncop = '$CodCuentaOncop',":'');
		$iCodCuentaPub20 = (!empty($CodCuentaPub20)?"CodCuentaPub20 = '$CodCuentaPub20',":'');
		$iCodPadre = (!empty($CodPadre)?"CodPadre = '$CodPadre',":'');
		##	valido
		if (!trim($CodInterno) || !trim($Descripcion) || !trim($CodClasificacion) || trim($PrecioVenta) == '') die("Debe llenar los campos (*) obligatorios.");
		elseif ($Digitos <> ($DigitosPadre + 2)) die("El Código debe tener ".($DigitosPadre + 2)." digitos");
		elseif ($NroPadre <> substr($CodInterno, 0, $DigitosPadre) && !empty($CodPadre)) die("´Codigo no concuerda con el del servicio padre.");
		elseif (!is_unique('co_mastservicios','CodInterno',$CodInterno,'CodServicio',$CodServicio)) die("Código ya ingresado");
		//elseif ($Digitos <> strlen($CodInterno)) die("El C&oacute;digo debe tener exactamente $Digitos digitos");
		##	actualizo
		$sql = "UPDATE co_mastservicios
				SET
					CodInterno = '$CodInterno',
					Descripcion = '$Descripcion',
					Digitos = '$Digitos',
					CodClasificacion = '$CodClasificacion',
					$iPartidaIngreso
					$iCodCuentaOncop
					$iCodCuentaPub20
					FlagExoneradoIva = '$FlagExoneradoIva',
					FlagAfectoDescuento = '$FlagAfectoDescuento',
					PrecioVenta = '$PrecioVenta',
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodServicio = '$CodServicio'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "DELETE FROM co_mastservicios WHERE CodServicio = '$registro'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
?>