<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion.sql", "w+");
##############################################################################/
##	ENTERAR IMPUESTOS (ENTERAR)
##############################################################################/
if ($modulo == "formulario") {
	//	enterar
	if ($accion == "enterar") {
		mysql_query("BEGIN");
		##	-----------------
		##	actualizo
		foreach($registros as $registro) {
			list($Anio, $TipoComprobante, $NroComprobante, $Estado, $Comprobante) = explode('_', $registro);
			if ($Estado != 'PA') die('Error al Enterar el Comprobante Nro. <strong>'.$Comprobante.'</strong>');
			$sql = "UPDATE ap_retenciones 
					SET 
						Estado = 'EN', 
						FechaEnterado = '".formatFechaAMD($FechaEnterado)."', 
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."', 
						UltimaFecha = NOW() 
					WHERE 
						Anio = '".$Anio."' AND 
						TipoComprobante = '".$TipoComprobante."' AND 
						NroComprobante = '".$NroComprobante."'";
			execute($sql);
		}
		##	-----------------
		mysql_query("COMMIT");
	}
}
?>