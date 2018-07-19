<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion-".$AnioActual.$MesActual.$DiaActual."_".$HorasActual.$MinutosActual.$SegundosActual.".sql", "w+");
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
			##	elimino datos actuales y siguientes al periodo
			$sql = "DELETE FROM pr_acumuladofideicomisodetalle
					WHERE
						CodPersona = '".$_CodPersona[$i]."' AND  
						Periodo >= '".$fPeriodo."'";
			execute($sql);
			##	
			$sql = "DELETE FROM pr_fideicomisocalculo
					WHERE
						CodPersona = '".$_CodPersona[$i]."' AND  
						Periodo >= '".$fPeriodo."'";
			execute($sql);
			##	inserto acumulados
			$AnteriorProv = $_PREST_ACUMULADA[$i] - ($_PREST_ANTIG_MENSUAL[$i] + $_PREST_COMPL[$i]);
			$AnteriorFide = $_INTERES_ACUMULADO[$i] - $_INTERES_MENSUAL[$i];
			$sql = "INSERT INTO pr_acumuladofideicomisodetalle
					SET
						CodPersona = '".$_CodPersona[$i]."',
						Periodo = '".$fPeriodo."',
						CodOrganismo = '".$fCodOrganismo."',
						AnteriorProv = '".$AnteriorProv."',
						AnteriorFide = '".$AnteriorFide."',
						Transaccion = '".$_PREST_ANTIG_MENSUAL[$i]."',
						TransaccionFide = '".$_INTERES_MENSUAL[$i]."',
						Dias = '".$_DIAS[$i]."',
						Complemento = '".$_PREST_COMPL[$i]."',
						DiasAdicional = '".$_DiasAdicional[$i]."',
						FlagFraccionado = '".$_FlagFraccionado[$i]."',
						UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
						UltimaFecha = NOW()";
			execute($sql);
			##	inserto calculo
			$sql = "INSERT INTO pr_fideicomisocalculo
					SET
						Periodo = '".$fPeriodo."',
						CodPersona = '".$_CodPersona[$i]."',
						SueldoMensual = '".$_SUELDO_MENSUAL[$i]."',
						Bonificaciones = '".$_BONOS[$i]."',
						AliVac = '".$_ALIVAC[$i]."',
						AliFin = '".$_ALIFIN[$i]."',
						SueldoDiario = '".$_REMUN_DIARIA[$i]."',
						SueldoDiarioAli = '".$_SUELDO_ALIC[$i]."',
						Dias = '".$_DIAS[$i]."',
						PrestAntiguedad = '".$_PREST_ANTIG_MENSUAL[$i]."',
						DiasComplemento = '".$_DiasAdicional[$i]."',
						PrestComplemento = '".$_PREST_COMPL[$i]."',
						PrestAcumulada = '".$_PREST_ACUMULADA[$i]."',
						Tasa = '".$_TASA[$i]."',
						DiasMes = '".$_DIAS_MES[$i]."',
						InteresMensual = '".$_INTERES_MENSUAL[$i]."',
						InteresAcumulado = '".$_INTERES_ACUMULADO[$i]."',
						UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
						UltimaFecha = NOW()";
			execute($sql);
			##	actualizo concepto
			$sql = "INSERT INTO pr_tiponominaempleadoconcepto
					SET
						CodTipoNom = '".$fCodTipoNom."',
						Periodo = '".$fPeriodo."',
						CodPersona = '".$_CodPersona[$i]."',
						CodOrganismo = '".$fCodOrganismo."',
						CodTipoProceso = '".$_CodTipoProceso[$i]."',
						CodConcepto = '".$_PARAMETRO['PROVISION']."',
						Monto = '".$_PREST_ANTIG_MENSUAL[$i]."',
						Cantidad = '".$_DIAS[$i]."',
						UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
						UltimaFecha = NOW()
					ON DUPLICATE KEY UPDATE
						Monto = '".$_PREST_ANTIG_MENSUAL[$i]."',
						Cantidad = '".$_DIAS[$i]."',
						UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	-----------------
		mysql_query("COMMIT");
	}
}
?>