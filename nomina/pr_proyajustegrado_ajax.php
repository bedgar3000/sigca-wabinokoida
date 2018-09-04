<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion-".$AnioActual.$MesActual.$DiaActual."_".$HorasActual.$MinutosActual.$SegundosActual.".sql", "w+");
//	$__archivo = fopen("_"."$modulo-$accion".".sql", "w+");
##############################################################################/
##	Ajuste Salarias para la Proyeccción de Gastos (NUEVO, MODIFICAR)
##############################################################################/
if ($modulo == "formulario") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		##	-----------------
		##	valido
		$PeriodoDesdeA = substr($PeriodoDesde, 0, 4);
		$PeriodoHastaA = substr($PeriodoHasta, 0, 4);
		if (!trim($CodOrganismo) || !trim($Ejercicio) || !trim($PeriodoDesde) || !trim($PeriodoHasta) || !trim($Descripcion)) die("Debe llenar los campos (*) obligatorios.");
		elseif ($PeriodoDesde > $PeriodoHasta) die("Periodo incorrecto");
		elseif (!validateDate($PeriodoDesde, 'Y-m') || !validateDate($PeriodoHasta, 'Y-m')) die("Formato de periodos incorrecto (año-mes 0000-00)");
		elseif ($PeriodoDesdeA <> $Ejercicio || $PeriodoHastaA <> $Ejercicio) die("Periodo no pude ser distinto al Ejercicio");
		##	codigo
		$Numero = codigo('pr_proyajustegrado','Numero',2,['CodOrganismo','Ejercicio'],[$CodOrganismo,$Ejercicio]);
		$CodAjuste = $Ejercicio.$CodOrganismo.$Numero;
		##	inserto
		$sql = "INSERT INTO pr_proyajustegrado
				SET
					CodAjuste = '".$CodAjuste."',
					CodOrganismo = '".$CodOrganismo."',
					Ejercicio = '".$Ejercicio."',
					Numero = '".$Numero."',
					PeriodoDesde = '".$PeriodoDesde."',
					PeriodoHasta = '".$PeriodoHasta."',
					Descripcion = '".$Descripcion."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		##	grado
		$Secuencia = 0;
		for ($i=0; $i < count($grado_CategoriaCargo); $i++) {
			$sql = "INSERT INTO pr_proyajustegradodet
					SET
						CodAjuste = '".$CodAjuste."',
						Secuencia = '".++$Secuencia."',
						CategoriaCargo = '".$grado_CategoriaCargo[$i]."',
						Grado = '".$grado_Grado[$i]."',
						Paso = '".$grado_Paso[$i]."',
						SueldoActual = '".setNumero($grado_SueldoActual[$i])."',
						Porcentaje = '".setNumero($grado_Porcentaje[$i])."',
						Monto = '".setNumero($grado_Monto[$i])."',
						SueldoTotal = '".setNumero($grado_SueldoTotal[$i])."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	-----------------
		mysql_query("COMMIT");
	}
	//	modificar
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		##	-----------------
		##	valido
		$PeriodoDesdeA = substr($PeriodoDesde, 0, 4);
		$PeriodoHastaA = substr($PeriodoHasta, 0, 4);
		if (!trim($PeriodoDesde) || !trim($PeriodoHasta) || !trim($Descripcion)) die("Debe llenar los campos (*) obligatorios.");
		elseif ($PeriodoDesde > $PeriodoHasta) die("Periodo incorrecto");
		elseif (!validateDate($PeriodoDesde, 'Y-m') || !validateDate($PeriodoHasta, 'Y-m')) die("Formato de periodos incorrecto (año-mes 0000-00)");
		elseif ($PeriodoDesdeA <> $Ejercicio || $PeriodoHastaA <> $Ejercicio) die("Periodo no pude ser distinto al Ejercicio");
		##	actualizo
		$sql = "UPDATE pr_proyajustegrado
				SET
					PeriodoDesde = '".$PeriodoDesde."',
					PeriodoHasta = '".$PeriodoHasta."',
					Descripcion = '".$Descripcion."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodAjuste = '".$CodAjuste."'";
		execute($sql);
		##	grado
		execute("DELETE FROM pr_proyajustegradodet WHERE CodAjuste = '".$CodAjuste."'");
		$Secuencia = 0;
		for ($i=0; $i < count($grado_Monto); $i++) {
			$sql = "INSERT INTO pr_proyajustegradodet
					SET
						CodAjuste = '".$CodAjuste."',
						Secuencia = '".++$Secuencia."',
						CategoriaCargo = '".$grado_CategoriaCargo[$i]."',
						Grado = '".$grado_Grado[$i]."',
						Paso = '".$grado_Paso[$i]."',
						SueldoActual = '".setNumero($grado_SueldoActual[$i])."',
						Porcentaje = '".setNumero($grado_Porcentaje[$i])."',
						Monto = '".setNumero($grado_Monto[$i])."',
						SueldoTotal = '".setNumero($grado_SueldoTotal[$i])."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	-----------------
		mysql_query("COMMIT");
	}
}
?>