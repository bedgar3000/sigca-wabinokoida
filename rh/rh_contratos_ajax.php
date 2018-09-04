<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion-".$AnioActual.$MesActual.$DiaActual."_".$HorasActual.$MinutosActual.$SegundosActual.".sql", "w+");
	$__archivo = fopen("_"."$modulo-$accion".".sql", "w+");
##############################################################################/
##	Actividads (NUEVO, MODIFICAR, ELIMINAR, AJAX)
##############################################################################/
if ($modulo == "formulario") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		##	-----------------
		$FlagFirma = ($FlagFirma?'S':'N');
		##	valido
		if (!trim($CodPersona) || !trim($CodOrganismo) || !trim($CodFormato) || !trim($FechaDesde) || !trim($FechaContrato) || ($TipoContrato == 'DE' && !trim($FechaHasta)) || ($FlagFirma == 'S' && !trim($FechaFirma))) die("Debe llenar los campos (*) obligatorios.");
		elseif(!validateDate($FechaDesde,'d-m-Y')) die("Fecha de Inicio Vigencia de Contrato incorrecta");
		elseif(!validateDate($FechaContrato,'d-m-Y')) die("Fecha de Contrato incorrecta");
		elseif($TipoContrato == 'DE' && !validateDate($FechaHasta,'d-m-Y')) die("Fecha de Culminaci&oacute;n Vigencia de Contrato incorrecta");
		elseif($TipoContrato == 'DE' && formatFechaAMD($FechaDesde) > formatFechaAMD($FechaHasta)) die("Vigencia de Contrato incorrecta");
		elseif($FlagFirma == 'S' && !validateDate($FechaFirma,'d-m-Y')) die("Fecha de Firma de Contrato incorrecta");
		##	actualizo
		$sql = "UPDATE rh_contratos
				SET
					CodFormato = '".$CodFormato."',
					FechaDesde = '".formatFechaAMD($FechaDesde)."',
					FechaHasta = '".formatFechaAMD($FechaHasta)."',
					FlagFirma = '".$FlagFirma."',
					FechaFirma = '".formatFechaAMD($FechaFirma)."',
					FechaContrato = '".formatFechaAMD($FechaContrato)."',
					Contrato = '".$Contrato."',
					Comentarios = '".$Comentarios."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodContrato = '$CodContrato'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	renovar
	elseif ($accion == "renovar") {
		mysql_query("BEGIN");
		##	-----------------
		$FlagFirma = ($FlagFirma?'S':'N');
		##	valido
		if (!trim($CodPersona) || !trim($CodOrganismo) || !trim($CodFormato) || !trim($FechaDesde) || !trim($FechaContrato) || ($TipoContrato == 'DE' && !trim($FechaHasta)) || ($FlagFirma == 'S' && !trim($FechaFirma))) die("Debe llenar los campos (*) obligatorios.");
		elseif(!validateDate($FechaDesde,'d-m-Y')) die("Fecha de Inicio Vigencia de Contrato incorrecta");
		elseif(!validateDate($FechaContrato,'d-m-Y')) die("Fecha de Contrato incorrecta");
		elseif($TipoContrato == 'DE' && !validateDate($FechaHasta,'d-m-Y')) die("Fecha de Culminaci&oacute;n Vigencia de Contrato incorrecta");
		elseif($TipoContrato == 'DE' && formatFechaAMD($FechaDesde) > formatFechaAMD($FechaHasta)) die("Vigencia de Contrato incorrecta");
		elseif($FlagFirma == 'S' && !validateDate($FechaFirma,'d-m-Y')) die("Fecha de Firma de Contrato incorrecta");
		##	actualizo
		$sql = "UPDATE rh_contratos
				SET
					Estado = 'TE',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodContrato = '$CodContrato'";
		execute($sql);
		##	inserto
		$Secuencia = codigo('rh_contratos','Secuencia',4,['CodPersona'],[$CodPersona]);
		$CodContrato = $CodPersona.$Secuencia;
		$sql = "INSERT INTO rh_contratos
				SET
					CodContrato = '".$CodContrato."',
					CodPersona = '".$CodPersona."',
					Secuencia = '".$Secuencia."',
					CodOrganismo = '".$CodOrganismo."',
					CodFormato = '".$CodFormato."',
					FechaDesde = '".formatFechaAMD($FechaDesde)."',
					FechaHasta = '".formatFechaAMD($FechaHasta)."',
					FlagFirma = '".$FlagFirma."',
					FechaFirma = '".formatFechaAMD($FechaFirma)."',
					FechaContrato = '".formatFechaAMD($FechaContrato)."',
					Contrato = '".$Contrato."',
					Comentarios = '".$Comentarios."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
}
?>