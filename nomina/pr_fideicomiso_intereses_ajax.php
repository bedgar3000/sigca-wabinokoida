<?php
include("../lib/fphp.php");
include("lib/fphp.php");
	$__archivo = fopen("_"."$modulo-$accion-".$AnioActual.$MesActual.$DiaActual."_".$HorasActual.$MinutosActual.$SegundosActual.".sql", "w+");
##############################################################################/
##	DEPOSITOS DE ANTIGUEDAD (ACTUALIZAR)
##############################################################################/
if ($modulo == "formulario") {
	//	nuevo
	if ($accion == "actualizar") {
		mysql_query("BEGIN");
		##	-----------------
		##	valido
		if (!count($_CodPersona)) die("Debe seleccionar por lo menos una persona.");
		##	recorro registros seleccionados
		for($i=0; $i<count($_CodPersona); $i++) {
			##	inserto acumulados
			$AnteriorFide = $_INTERES_ACUMULADO[$i] - $_INTERES_MENSUAL[$i];
			$sql = "UPDATE pr_acumuladofideicomisodetalle
					SET
						AnteriorFide = '".$AnteriorFide."',
						TransaccionFide = '".$_INTERES_MENSUAL[$i]."',
						UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
						UltimaFecha = NOW()
					WHERE
						CodPersona = '".$_CodPersona[$i]."' AND
						Periodo = '".$fPeriodo."'";
			execute($sql);
			##	inserto calculo
			$sql = "UPDATE pr_fideicomisocalculo
					SET
						Tasa = '".$_TASA[$i]."',
						DiasMes = '".$_DIAS_MES[$i]."',
						InteresMensual = '".$_INTERES_MENSUAL[$i]."',
						InteresAcumulado = '".$_INTERES_ACUMULADO[$i]."',
						UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
						UltimaFecha = NOW()
					WHERE
						Periodo = '".$fPeriodo."' AND
						CodPersona = '".$_CodPersona[$i]."'";
			execute($sql);
		}
		##	-----------------
		die('fin');
		mysql_query("COMMIT");
	}
}
?>