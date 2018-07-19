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
		$FlagEsFiscal = (!empty($FlagEsFiscal)?'S':'N');
		$FlagProvision = (!empty($FlagProvision)?'S':'N');
		$iCodCuentaOncop = (!empty($CodCuentaOncop)?"CodCuentaOncop = '$CodCuentaOncop',":'');
		$iCodCuentaPub20 = (!empty($CodCuentaPub20)?"CodCuentaPub20 = '$CodCuentaPub20',":'');
		$iCodCuentaDudosaOncop = (!empty($CodCuentaDudosaOncop)?"CodCuentaDudosaOncop = '$CodCuentaDudosaOncop',":'');
		$iCodCuentaDudosaPub20 = (!empty($CodCuentaDudosaPub20)?"CodCuentaDudosaPub20 = '$CodCuentaDudosaPub20',":'');
		$iCodCuentaProvOncop = (!empty($CodCuentaProvOncop)?"CodCuentaProvOncop = '$CodCuentaProvOncop',":'');
		$iCodCuentaProvPub20 = (!empty($CodCuentaProvPub20)?"CodCuentaProvPub20 = '$CodCuentaProvPub20',":'');
		$iCodCuentaAdeOncop = (!empty($CodCuentaAdeOncop)?"CodCuentaAdeOncop = '$CodCuentaAdeOncop',":'');
		$iCodCuentaAdePub20 = (!empty($CodCuentaAdePub20)?"CodCuentaAdePub20 = '$CodCuentaAdePub20',":'');
		##	valido
		if (!trim($CodTipoDocumento) || !trim($Descripcion) || !trim($CodClasificacion)) die("Debe llenar los campos (*) obligatorios.");
		else {
			$sql = "SELECT * FROM co_tipodocumento WHERE CodTipoDocumento = '$CodTipoDocumento'";
			$codigo = getRecords($sql);
			if (count($codigo)) die("Código ya ingresado");
		}
		##	inserto
		$sql = "INSERT INTO co_tipodocumento
				SET
					CodTipoDocumento = '$CodTipoDocumento',
					Descripcion = '$Descripcion',
					CodClasificacion = '$CodClasificacion',
					$iCodCuentaOncop
					$iCodCuentaPub20
					$iCodCuentaDudosaOncop
					$iCodCuentaDudosaPub20
					$iCodCuentaProvOncop
					$iCodCuentaProvPub20
					$iCodCuentaAdeOncop
					$iCodCuentaAdePub20
					FlagEsFiscal = '$FlagEsFiscal',
					FlagProvision = '$FlagProvision',
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
		$FlagEsFiscal = (!empty($FlagEsFiscal)?'S':'N');
		$FlagProvision = (!empty($FlagProvision)?'S':'N');
		$iCodCuentaOncop = (!empty($CodCuentaOncop)?"CodCuentaOncop = '$CodCuentaOncop',":'');
		$iCodCuentaPub20 = (!empty($CodCuentaPub20)?"CodCuentaPub20 = '$CodCuentaPub20',":'');
		$iCodCuentaDudosaOncop = (!empty($CodCuentaDudosaOncop)?"CodCuentaDudosaOncop = '$CodCuentaDudosaOncop',":'');
		$iCodCuentaDudosaPub20 = (!empty($CodCuentaDudosaPub20)?"CodCuentaDudosaPub20 = '$CodCuentaDudosaPub20',":'');
		$iCodCuentaProvOncop = (!empty($CodCuentaProvOncop)?"CodCuentaProvOncop = '$CodCuentaProvOncop',":'');
		$iCodCuentaProvPub20 = (!empty($CodCuentaProvPub20)?"CodCuentaProvPub20 = '$CodCuentaProvPub20',":'');
		$iCodCuentaAdeOncop = (!empty($CodCuentaAdeOncop)?"CodCuentaAdeOncop = '$CodCuentaAdeOncop',":'');
		$iCodCuentaAdePub20 = (!empty($CodCuentaAdePub20)?"CodCuentaAdePub20 = '$CodCuentaAdePub20',":'');
		##	valido
		if (!trim($Descripcion) || !trim($CodClasificacion)) die("Debe llenar los campos (*) obligatorios.");
		##	actualizo
		$sql = "UPDATE co_tipodocumento
				SET
					Descripcion = '$Descripcion',
					CodClasificacion = '$CodClasificacion',
					$iCodCuentaOncop
					$iCodCuentaPub20
					$iCodCuentaDudosaOncop
					$iCodCuentaDudosaPub20
					$iCodCuentaProvOncop
					$iCodCuentaProvPub20
					$iCodCuentaAdeOncop
					$iCodCuentaAdePub20
					FlagEsFiscal = '$FlagEsFiscal',
					FlagProvision = '$FlagProvision',
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodTipoDocumento = '$CodTipoDocumento'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "DELETE FROM co_tipodocumento WHERE CodTipoDocumento = '$registro'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
?>