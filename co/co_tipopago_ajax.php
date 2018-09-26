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
		$FlagEsTarjetaCredito = (!empty($FlagEsTarjetaCredito)?'S':'N');
		$FlagEsTarjetaDebito = (!empty($FlagEsTarjetaDebito)?'S':'N');
		$FlagReqDocumento = (!empty($FlagReqDocumento)?'S':'N');
		$FlagReqBanco = (!empty($FlagReqBanco)?'S':'N');
		##	valido
		if (!trim($CodTipoPago) || !trim($Descripcion)) die("Debe llenar los campos (*) obligatorios.");
		else {
			$sql = "SELECT * FROM co_tipopago WHERE CodTipoPago = '$CodTipoPago'";
			$codigo = getRecords($sql);
			if (count($codigo)) die("Código ya ingresado");
		}
		##	inserto
		$sql = "INSERT INTO co_tipopago
				SET
					CodTipoPago = '$CodTipoPago',
					Descripcion = '$Descripcion',
					FlagEsTarjetaCredito = '$FlagEsTarjetaCredito',
					FlagEsTarjetaDebito = '$FlagEsTarjetaDebito',
					FlagReqDocumento = '$FlagReqDocumento',
					FlagReqBanco = '$FlagReqBanco',
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
		$FlagEsTarjetaCredito = (!empty($FlagEsTarjetaCredito)?'S':'N');
		$FlagEsTarjetaDebito = (!empty($FlagEsTarjetaDebito)?'S':'N');
		$FlagReqDocumento = (!empty($FlagReqDocumento)?'S':'N');
		$FlagReqBanco = (!empty($FlagReqBanco)?'S':'N');
		##	valido
		if (!trim($Descripcion)) die("Debe llenar los campos (*) obligatorios.");
		##	actualizo
		$sql = "UPDATE co_tipopago
				SET
					Descripcion = '$Descripcion',
					FlagEsTarjetaCredito = '$FlagEsTarjetaCredito',
					FlagEsTarjetaDebito = '$FlagEsTarjetaDebito',
					FlagReqDocumento = '$FlagReqDocumento',
					FlagReqBanco = '$FlagReqBanco',
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodTipoPago = '$CodTipoPago'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "DELETE FROM co_tipopago WHERE CodTipoPago = '$registro'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
?>