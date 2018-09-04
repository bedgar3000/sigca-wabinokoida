<?php
include("../lib/fphp.php");
include("lib/fphp.php");
##	$__archivo = fopen("_"."$modulo-$accion-".$AnioActual.$MesActual.$DiaActual."_".$HorasActual.$MinutosActual.$SegundosActual.".sql", "w+");
##############################################################################/
##	DEPOSITOS DE ANTIGUEDAD (ACTUALIZAR)
##############################################################################/
if ($modulo == "formulario") {
	//	nuevo
	if ($accion == "procesar") {
		mysql_query("BEGIN");
		##	-----------------
		##	consulto empleado
		$sql = "SELECT * FROM mastempleado WHERE CodPersona = '".$CodPersona."'";
		$field_empleado = getRecord($sql);
		##	-----------------
		##	elimino calculo
		$sql = "DELETE FROM pr_fideicomisocalculo WHERE CodPersona = '".$CodPersona."' AND Periodo >= '".$Periodo."'";
		execute($sql);
		##	elimino calculo
		##	$sql = "DELETE FROM pr_acumuladofideicomisodetalle WHERE CodPersona = '".$CodPersona."' AND Periodo >= '".$Periodo."'";
		##	execute($sql);
		$sql = "UPDATE pr_acumuladofideicomisodetalle
				SET
					CodOrganismo = '".$field_empleado['CodOrganismo']."',
					AnteriorProv = 0,
					AnteriorFide = 0,
					Transaccion = 0,
					TransaccionFide = 0,
					UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
					UltimaFecha = NOW()
                 WHERE CodPersona = '".$CodPersona."' AND Periodo >= '".$Periodo."'";
		execute($sql);
		##	recorro registros
		for($i=0; $i<count($_Periodo); $i++) {
			##	inserto calculo
			$sql = "INSERT INTO pr_fideicomisocalculo
					SET
						Periodo = '".$_Periodo[$i]."',
						CodPersona = '".$CodPersona."',
						SueldoMensual = '".$_SueldoMensual[$i]."',
						Bonificaciones = '".$_Bonificaciones[$i]."',
						AliVac = '".$_AliVac[$i]."',
						AliFin = '".$_AliFin[$i]."',
						SueldoDiario = '".$_SueldoDiario[$i]."',
						SueldoDiarioAli = '".$_SueldoDiarioAli[$i]."',
						Dias = '".$_Dias[$i]."',
						PrestAntiguedad = '".$_PrestAntiguedad[$i]."',
						DiasComplemento = '".$_DiasComplemento[$i]."',
						PrestComplemento = '".$_PrestComplemento[$i]."',
						PrestAcumulada = '".$_PrestAcumulada[$i]."',
						Tasa = '".$_Tasa[$i]."',
						DiasMes = '".$_DiasMes[$i]."',
						InteresMensual = '".$_InteresMensual[$i]."',
						InteresAcumulado = '".$_InteresAcumulado[$i]."',
						UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
						UltimaFecha = NOW()";
			execute($sql);
			##	inserto acumulado
			$AnteriorProv = $_PrestAcumulada[$i] - ($_PrestAntiguedad[$i] + $_PrestComplemento[$i]);
			$AnteriorFide = $_InteresAcumulado[$i] - $_InteresMensual[$i];
			$sql = "INSERT INTO pr_acumuladofideicomisodetalle
					SET
						CodPersona = '".$CodPersona."',
						Periodo = '".$_Periodo[$i]."',
						CodOrganismo = '".$field_empleado['CodOrganismo']."',
						AnteriorProv = '".$AnteriorProv."',
						AnteriorFide = '".$AnteriorFide."',
						Transaccion = '".$_PrestAntiguedad[$i]."',
						TransaccionFide = '".$_InteresMensual[$i]."',
						Dias = '".$_Dias[$i]."',
						Complemento = '".$_PrestComplemento[$i]."',
						DiasAdicional = '".$_DiasComplemento[$i]."',
						FlagFraccionado = '".$_FlagFraccionado[$i]."',
						UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
						UltimaFecha = NOW()
					ON DUPLICATE KEY UPDATE
						CodOrganismo = '".$field_empleado['CodOrganismo']."',
						AnteriorProv = '".$AnteriorProv."',
						AnteriorFide = '".$AnteriorFide."',
						Transaccion = '".$_PrestAntiguedad[$i]."',
						TransaccionFide = '".$_InteresMensual[$i]."',
						Dias = '".$_Dias[$i]."',
						Complemento = '".$_PrestComplemento[$i]."',
						DiasAdicional = '".$_DiasComplemento[$i]."',
						FlagFraccionado = '".$_FlagFraccionado[$i]."',
						UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
						UltimaFecha = NOW()";
			execute($sql);
			##	consulto nómina
			$sql = "SELECT CodTipoProceso
					FROM pr_tiponominaempleadoconcepto
					WHERE
						CodPersona = '".$CodPersona."' AND
						Periodo = '".$_Periodo[$i]."' AND
						CodOrganismo = '".$field_empleado['CodOrganismo']."' AND
						CodConcepto = '0045'
					LIMIT 0, 1";
			$field_nomina = getRecord($sql);
			##	concepto (antiguedad)
			$sql = "INSERT INTO pr_tiponominaempleadoconcepto
					SET
						CodPersona = '".$CodPersona."',
						Periodo = '".$_Periodo[$i]."',
						CodOrganismo = '".$field_empleado['CodOrganismo']."',
						CodTipoNom = '".$field_empleado['CodTipoNom']."',
						CodTipoProceso = '".$field_nomina['CodTipoProceso']."',
						CodConcepto = '0045',
						Monto = '".$_PrestAntiguedad[$i]."',
						Cantidad = '".$_Dias[$i]."',
						UltimoUsuario = '',
						UltimaFecha = NOW()
					ON DUPLICATE KEY UPDATE
						Monto = '".$_PrestAntiguedad[$i]."',
						Cantidad = '".$_Dias[$i]."',
						UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
						UltimaFecha = NOW()";
			execute($sql);
			##	concepto (Ali.Vac)
			$sql = "INSERT INTO pr_tiponominaempleadoconcepto
					SET
						CodPersona = '".$CodPersona."',
						Periodo = '".$_Periodo[$i]."',
						CodOrganismo = '".$field_empleado['CodOrganismo']."',
						CodTipoNom = '".$field_empleado['CodTipoNom']."',
						CodTipoProceso = '".$field_nomina['CodTipoProceso']."',
						CodConcepto = '".$_PARAMETRO['ALIVAC']."',
						Monto = '".$_AliVac[$i]."',
						Cantidad = '".$_DiasAliVac[$i]."',
						UltimoUsuario = '',
						UltimaFecha = NOW()
					ON DUPLICATE KEY UPDATE
						Monto = '".$_AliVac[$i]."',
						Cantidad = '".$_DiasAliVac[$i]."',
						UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
						UltimaFecha = NOW()";
			execute($sql);
			##	concepto (Ali.Fin)
			$sql = "INSERT INTO pr_tiponominaempleadoconcepto
					SET
						CodPersona = '".$CodPersona."',
						Periodo = '".$_Periodo[$i]."',
						CodOrganismo = '".$field_empleado['CodOrganismo']."',
						CodTipoNom = '".$field_empleado['CodTipoNom']."',
						CodTipoProceso = '".$field_nomina['CodTipoProceso']."',
						CodConcepto = '".$_PARAMETRO['ALIFIN']."',
						Monto = '".$_AliFin[$i]."',
						Cantidad = '".$_DiasAliFin[$i]."',
						UltimoUsuario = '',
						UltimaFecha = NOW()
					ON DUPLICATE KEY UPDATE
						Monto = '".$_AliFin[$i]."',
						Cantidad = '".$_DiasAliFin[$i]."',
						UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	-----------------
		mysql_query("COMMIT");
	}
}
?>