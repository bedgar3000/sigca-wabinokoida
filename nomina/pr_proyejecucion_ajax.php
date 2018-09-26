<?php
include("../lib/fphp.php");
include("lib/fphp.php");
include("lib/funciones_globales_proyeccion.php");
//	$__archivo = fopen("_"."$modulo-$accion-".$AnioActual.$MesActual.$DiaActual."_".$HorasActual.$MinutosActual.$SegundosActual.".sql", "w+");
//	$__archivo = fopen("_"."$modulo-$accion".".sql", "w+");
##############################################################################/
##	Ajuste Salarias para la Proyeccción de Gastos (NUEVO, MODIFICAR)
##############################################################################/
if ($modulo == "formulario") {
	if ($accion == "calcular") {
		mysql_query("BEGIN");
		##	-----------------
		foreach ($personas as $Secuencia) {
			$Desde = new DateTime($PeriodoDesde.'-01');
			$Hasta = new DateTime($PeriodoHasta.'-'.getDiasMes($PeriodoHasta));
			$Diferencia = $Desde->diff($Hasta);
			##	
			$sql = "DELETE FROM pr_proyejecucion
					WHERE
						CodParametro = '$CodParametro' AND
						CodRecurso = '$CodRecurso' AND
						Secuencia = '$Secuencia'";
			execute($sql);
			##	empleado
			$sql = "SELECT
						prd.*,
						pr.CodOrganismo,
						pr.CodTipoNom,
						pr.Ejercicio,
						p.Ndocumento,
						p.Sexo,
						p.Fnacimiento,
						e.CodEmpleado,
						e.Fingreso
					FROM
						pr_proyrecursosdet prd
						INNER JOIN pr_proyrecursos pr ON (pr.CodRecurso = prd.CodRecurso)
						LEFT JOIN mastpersonas p ON (p.CodPersona = prd.CodPersona)
						LEFT JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
					WHERE
						prd.CodRecurso = '$CodRecurso' AND
						prd.Secuencia = '$Secuencia'";
			$field_empleado = getRecord($sql);
			##	establezco valores globales del concepto
			$_ARGS['FlagEjecucion'] = 'S';
			$_ARGS['CodRecurso'] = $field_empleado['CodRecurso'];
			$_ARGS['Secuencia'] = $field_empleado['Secuencia'];
			$_ARGS['CodOrganismo'] = $field_empleado['CodOrganismo'];
			$_ARGS['Ejercicio'] = $field_empleado['Ejercicio'];
			$_ARGS['CodPersona'] = $field_empleado['CodPersona'];
			$_ARGS['CodEmpleado'] = $field_empleado['CodEmpleado'];
			$_ARGS['CodTipoNom'] = $field_empleado['CodTipoNom'];
			$_ARGS['Sexo'] = $field_empleado['Sexo'];
			$_ARGS['FechaNacimiento'] = $field_empleado['Fnacimiento'];
			$_ARGS['FechaIngreso'] = $field_empleado['Fingreso'];
			$_ARGS['Sexo'] = $field_empleado['Sexo'];
			$_ARGS['_SUELDO_BASICO'] = SUELDO_BASICO();
			$_ARGS['_SUELDO_BASICO_DIARIO'] = round(($_ARGS['_SUELDO_BASICO'] / 30), 2);
			$_ARGS['_MESES'] = ( $Diferencia->y * 12 ) + $Diferencia->m + 1;
			##	conceptos
			$sql = "SELECT
						ppmd.CodConcepto,
						ppmd.Formula,
						ppmd.FlagParametrizable,
						ppmd.CodParametro,
						prp.Valor,
						c.Tipo
					FROM
						pr_proyparametrodet ppmd
						INNER JOIN pr_concepto c On (c.CodConcepto = ppmd.CodConcepto)
						LEFT JOIN pr_proyrecursosparametros prp ON (prp.CodParametro = ppmd.CodParametro AND prp.CodConcepto = ppmd.CodConcepto AND prp.CodRecurso = '$CodRecurso' AND prp.Secuencia = '$Secuencia')
					WHERE ppmd.CodParametro = '$CodParametro'
					ORDER BY CodConcepto";
			$field_conceptos = getRecords($sql);
			foreach ($field_conceptos as $fc) {
				##	establezco valores globales del concepto
				$_ARGS['CodConcepto'] = $fc['CodConcepto'];
				$_ARGS['CodParametro'] = $fc['CodParametro'];
				$_ARGS['Valor'] = $fc['Valor'];
				$_ARGS['FlagParametrizable'] = $fc['FlagParametrizable'];
				##	obtengo valor de la formula
				extract($_ARGS);
				$_CANTIDAD = 0;
				$_MONTO = 0;
				eval($fc['Formula']);
				##	inserto
				$sql = "INSERT INTO pr_proyejecucion
						SET
							CodParametro = '$CodParametro',
							CodConcepto = '$fc[CodConcepto]',
							CodRecurso = '$CodRecurso',
							Secuencia = '$Secuencia',
							Cantidad = ".floatval($_CANTIDAD).",
							Monto = ".floatval($_MONTO).",
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()";
				execute($sql);
			}
		}
		##	-----------------
		mysql_query("COMMIT");
	}
	elseif ($accion == "quitar") {
		mysql_query("BEGIN");
		##	-----------------
		if ($boton == '<') {
			foreach ($personas as $Secuencia) {
				$sql = "DELETE FROM pr_proyejecucion
						WHERE
							CodParametro = '$CodParametro' AND
							CodRecurso = '$CodRecurso' AND
							Secuencia = '$Secuencia'";
				execute($sql);
			}
		} else {
			$sql = "DELETE FROM pr_proyejecucion
					WHERE
						CodParametro = '$CodParametro' AND
						CodRecurso = '$CodRecurso'";
			execute($sql);
		}
		##	-----------------
		mysql_query("COMMIT");
	}
}
?>