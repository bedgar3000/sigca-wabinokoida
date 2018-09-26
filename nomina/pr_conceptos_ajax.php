<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion.sql", "w+");
##############################################################################/
##	ACTIVIDADES (NUEVO, MODIFICAR, ELIMINAR)
##############################################################################/
if ($modulo == "conceptos") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-----------------
		//	genero codigo
		$CodConcepto = getCodigo("pr_concepto", "CodConcepto", 4);
		//	inserto
		$sql = "INSERT INTO pr_concepto
				SET
					CodConcepto = '".$CodConcepto."',
					Descripcion = '".changeUrl($Descripcion)."',
					Tipo = '".$Tipo."',
					TextoImpresion = '".changeUrl($TextoImpresion)."',
					PlanillaOrden = '".$PlanillaOrden."',
					Formula = '".changeUrl($Formula)."',
					FormulaEditor = '".changeUrl($FormulaEditor)."',
					FlagFormula = '".$FlagFormula."',
					FlagAutomatico = '".$FlagAutomatico."',
					Abreviatura = '".changeUrl($Abreviatura)."',
					FlagBono = '".$FlagBono."',
					FlagRetencion = '".$FlagRetencion."',
					FlagObligacion = '".$FlagObligacion."',
					CodTipoDocumento = '".$CodTipoDocumento."',
					CodPersona = '".$CodPersona."',
					FlagBonoRemuneracion = '".$FlagBonoRemuneracion."',
					FlagRelacionIngreso = '".$FlagRelacionIngreso."',
					FlagJubilacion = '".$FlagJubilacion."',
					FlagDiferencia = '".$FlagDiferencia."',
					FlagBonoAlimentacion = '".$FlagBonoAlimentacion."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		//	tipos de nomina
		if ($detalles_nominas != "") {
			$nominas = split(";char:tr;", $detalles_nominas);
			foreach ($nominas as $_CodTipoNom) {
				//	inserto
				$sql = "INSERT INTO pr_conceptotiponomina
						SET
							CodConcepto = '".$CodConcepto."',
							CodTipoNom = '".$_CodTipoNom."'";
				execute($sql);
			}
		}
		//	tipos de proceso
		if ($detalles_procesos != "") {
			$procesos = split(";char:tr;", $detalles_procesos);
			foreach ($procesos as $_CodTipoProceso) {
				//	inserto
				$sql = "INSERT INTO pr_conceptoproceso
						SET
							CodConcepto = '".$CodConcepto."',
							CodTipoProceso = '".$_CodTipoProceso."'";
				execute($sql);
			}
		}
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	modificar registro
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizar
		$sql = "UPDATE pr_concepto
				SET
					Descripcion = '".changeUrl($Descripcion)."',
					Tipo = '".$Tipo."',
					TextoImpresion = '".changeUrl($TextoImpresion)."',
					PlanillaOrden = '".$PlanillaOrden."',
					Formula = '".changeUrl($Formula)."',
					FormulaEditor = '".changeUrl($FormulaEditor)."',
					FlagFormula = '".$FlagFormula."',
					FlagAutomatico = '".$FlagAutomatico."',
					Abreviatura = '".changeUrl($Abreviatura)."',
					FlagBono = '".$FlagBono."',
					FlagRetencion = '".$FlagRetencion."',
					FlagObligacion = '".$FlagObligacion."',
					CodPersona = '".$CodPersona."',
					CodTipoDocumento = '".$CodTipoDocumento."',
					FlagBonoRemuneracion = '".$FlagBonoRemuneracion."',
					FlagRelacionIngreso = '".$FlagRelacionIngreso."',
					FlagJubilacion = '".$FlagJubilacion."',
					FlagDiferencia = '".$FlagDiferencia."',
					FlagBonoAlimentacion = '".$FlagBonoAlimentacion."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodConcepto = '".$CodConcepto."'";
		$query_update = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		//	tipos de nomina
		$sql = "DELETE FROM pr_conceptotiponomina WHERE CodConcepto = '".$CodConcepto."'";
		$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		if ($detalles_nominas != "") {
			$nominas = split(";char:tr;", $detalles_nominas);
			foreach ($nominas as $_CodTipoNom) {
				//	inserto
				$sql = "INSERT INTO pr_conceptotiponomina
						SET
							CodConcepto = '".$CodConcepto."',
							CodTipoNom = '".$_CodTipoNom."'";
				$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			}
		}
		//	tipos de proceso
		$sql = "DELETE FROM pr_conceptoproceso WHERE CodConcepto = '".$CodConcepto."'";
		$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		if ($detalles_procesos != "") {
			$procesos = split(";char:tr;", $detalles_procesos);
			foreach ($procesos as $_CodTipoProceso) {
				//	inserto
				$sql = "INSERT INTO pr_conceptoproceso
						SET
							CodConcepto = '".$CodConcepto."',
							CodTipoProceso = '".$_CodTipoProceso."'";
				$query_insert = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			}
		}
		//	-----------------
		mysql_query("COMMIT");
	}
	
	//	eliminar registro
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		//	elimino
		$sql = "DELETE FROM pr_concepto WHERE CodConcepto = '".$registro."'";
		$query_delete = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		//	-----------------
		mysql_query("COMMIT");
	}
}
?>