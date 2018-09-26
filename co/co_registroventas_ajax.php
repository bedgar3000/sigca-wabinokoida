<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion-".".sql", "w+");
##############################################################################/
if ($modulo == "formulario") {
	//	importar
	if ($accion == "importar") {
		mysql_query("BEGIN");
		##	-----------------
		list($Anio, $Mes) = explode('-', $Periodo);
		##	valido
		if (!trim($CodOrganismo) || !trim($Periodo)) die("Debe llenar los campos (*) obligatorios.");
		##	
		$sql = "DELETE FROM co_registroventas
				WHERE
					CodOrganismo = '$CodOrganismo'
					AND Periodo = '$Periodo'
					AND SistemaFuente = '$SistemaFuente'";
		execute($sql);
		##	
		$sql = "SELECT do.*
				FROM co_documento do
				INNER JOIN co_tipodocumento td ON td.CodTipoDocumento = do.CodTipoDocumento
				WHERE
					do.CodOrganismo = '$CodOrganismo'
					AND do.VoucherPeriodo = '$Periodo'
					AND td.FlagEsFiscal = 'S'";
		$field_documentos = getRecords($sql);
		foreach ($field_documentos as $f)
		{
			##	codigo
			$Secuencia = codigo('co_registroventas','Secuencia',6,['CodOrganismo','Periodo','SistemaFuente'],[$CodOrganismo,$Periodo,$SistemaFuente]);
			$CodRegistro = $CodOrganismo.$Anio.$Mes.$SistemaFuente.$Secuencia;
			##	inserto
			$sql = "INSERT INTO co_registroventas
					SET
						CodRegistro = '$CodRegistro',
						CodOrganismo = '$CodOrganismo',
						Periodo = '$Periodo',
						SistemaFuente = '$SistemaFuente',
						Secuencia = '$Secuencia',
						CodDocumento = '$f[CodDocumento]',
						CodTipoDocumento = '$f[CodTipoDocumento]',
						NroDocumento = '$f[NroDocumento]',
						CodPersonaCliente = '$f[CodPersonaCliente]',
						DocFiscalCliente = '$f[DocFiscalCliente]',
						NombreCliente = '$f[NombreCliente]',
						DireccionCliente = '$f[DireccionCliente]',
						FechaDocumento = '$f[FechaDocumento]',
						MonedaDocumento = '$f[MonedaDocumento]',
						MontoAfectoOriginal = '$f[MontoAfecto]',
						MontoNoAfectoOriginal = '$f[MontoNoAfecto]',
						MontoImpuestoVentasOriginal = '$f[MontoImpuesto]',
						MontoTotalOriginal = '$f[MontoTotal]',
						MontoAfecto = '$f[MontoAfecto]',
						MontoNoAfecto = '$f[MontoNoAfecto]',
						MontoImpuestoVentas = '$f[MontoImpuesto]',
						MontoTotal = '$f[MontoTotal]',
						FlagRegistroVentas = 'S',
						Estado = 'PE',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	-----------------
		mysql_query("COMMIT");
		die('|' . 'Se han importado ' . count($field_documentos) . ' registros.');
	}
	//	nuevo
	elseif ($accion == "nuevo") {
		mysql_query("BEGIN");
		##	-----------------
		$FlagRegistroVentas = (!empty($FlagRegistroVentas)?'S':'N');
		$FechaDocumento = formatFechaAMD($FechaDocumento);
		$MontoAfecto = setNumero($MontoAfecto);
		$MontoNoAfecto = setNumero($MontoNoAfecto);
		$MontoImpuestoVentas = setNumero($MontoImpuestoVentas);
		$MontoTotal = setNumero($MontoTotal);
		$MontoImpuestoRetenido = setNumero($MontoImpuestoRetenido);
		$Periodo = substr($FechaDocumento, 0, 7);
		list($Anio, $Mes) = explode('-', $Periodo);
		##	valido
		if (!trim($CodOrganismo) || !trim($CodPersonaCliente) || !trim($FechaDocumento) || !trim($CodTipoDocumento) || !trim($NroDocumento)) die("Debe llenar los campos (*) obligatorios.");
		elseif (!$MontoTotal) die('El Monto Total no puede ser cero.');
		elseif (!($MontoAfecto + $MontoNoAfecto)) die('El Monto Afecto + Monto No Afecto no puede ser cero.');
		elseif (!alpha_dash($NroDocumento)) die('Formato Nro. Documento incorrecto.');
		elseif (!alpha_dash($RefNroDocumento) && trim($RefNroDocumento)) die('Formato Nro. Referencia incorrecto.');
		##	codigo
		$Secuencia = codigo('co_registroventas','Secuencia',6,['CodOrganismo','Periodo','SistemaFuente'],[$CodOrganismo,$Periodo,$SistemaFuente]);
		$CodRegistro = $CodOrganismo.$Anio.$Mes.$SistemaFuente.$Secuencia;
		##	inserto
		$sql = "INSERT INTO co_registroventas
				SET
					CodRegistro = '$CodRegistro',
					CodOrganismo = '$CodOrganismo',
					Periodo = '$Periodo',
					SistemaFuente = '$SistemaFuente',
					Secuencia = '$Secuencia',
					CodTipoDocumento = '$CodTipoDocumento',
					NroDocumento = '$NroDocumento',
					CodPersonaCliente = '$CodPersonaCliente',
					DocFiscalCliente = '$DocFiscalCliente',
					NombreCliente = '$NombreCliente',
					DireccionCliente = '$DireccionCliente',
					FechaDocumento = '$FechaDocumento',
					MonedaDocumento = '$MonedaDocumento',
					MontoAfectoOriginal = '$MontoAfecto',
					MontoNoAfectoOriginal = '$MontoNoAfecto',
					MontoImpuestoVentasOriginal = '$MontoImpuestoVentas',
					MontoTotalOriginal = '$MontoTotal',
					MontoAfecto = '$MontoAfecto',
					MontoNoAfecto = '$MontoNoAfecto',
					MontoImpuestoVentas = '$MontoImpuestoVentas',
					MontoTotal = '$MontoTotal',
					MontoImpuestoRetenido = '$MontoImpuestoRetenido',
					FlagRegistroVentas = '$FlagRegistroVentas',
					Comentarios = '$Comentarios',
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
		$FlagRegistroVentas = (!empty($FlagRegistroVentas)?'S':'N');
		$FechaDocumento = formatFechaAMD($FechaDocumento);
		$MontoAfecto = setNumero($MontoAfecto);
		$MontoNoAfecto = setNumero($MontoNoAfecto);
		$MontoImpuestoVentas = setNumero($MontoImpuestoVentas);
		$MontoTotal = setNumero($MontoTotal);
		$MontoImpuestoRetenido = setNumero($MontoImpuestoRetenido);
		$Periodo = substr($FechaDocumento, 0, 7);
		list($Anio, $Mes) = explode('-', $Periodo);
		##	valido
		if (!trim($FechaDocumento) || !trim($CodTipoDocumento) || !trim($NroDocumento)) die("Debe llenar los campos (*) obligatorios.");
		elseif (!$MontoTotal) die('El Monto Total no puede ser cero.');
		elseif (!($MontoAfecto + $MontoNoAfecto)) die('El Monto Afecto + Monto No Afecto no puede ser cero.');
		elseif (!alpha_dash($NroDocumento)) die('Formato Nro. Documento incorrecto.');
		elseif (!alpha_dash($RefNroDocumento) && trim($RefNroDocumento)) die('Formato Nro. Referencia incorrecto.');
		##	actualizo
		$sql = "UPDATE co_registroventas
				SET
					Periodo = '$Periodo',
					CodTipoDocumento = '$CodTipoDocumento',
					NroDocumento = '$NroDocumento',
					FechaDocumento = '$FechaDocumento',
					MonedaDocumento = '$MonedaDocumento',
					MontoAfectoOriginal = '$MontoAfecto',
					MontoNoAfectoOriginal = '$MontoNoAfecto',
					MontoImpuestoVentasOriginal = '$MontoImpuestoVentas',
					MontoTotalOriginal = '$MontoTotal',
					MontoAfecto = '$MontoAfecto',
					MontoNoAfecto = '$MontoNoAfecto',
					MontoImpuestoVentas = '$MontoImpuestoVentas',
					MontoTotal = '$MontoTotal',
					MontoImpuestoRetenido = '$MontoImpuestoRetenido',
					FlagRegistroVentas = '$FlagRegistroVentas',
					Comentarios = '$Comentarios',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodRegistro = '$CodRegistro'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		$sql = "SELECT * FROM co_registroventas WHERE CodRegistro = '$registro'";
		$field = getRecord($sql);
		if ($field['SistemaFuente'] == 'MA')
		{
			//	elimino
			$sql = "DELETE FROM co_registroventas WHERE CodRegistro = '$registro'";
			execute($sql);	
		}
		else 
		{
			die('No se puede eliminar este registro.');
		}
		//	-----------------
		mysql_query("COMMIT");
	}
}
elseif ($modulo == "validar") {
	//	importar
	if($accion == "importar") {
		$sql = "SELECT *
				FROM co_registroventas
				WHERE
					CodOrganismo = '$CodOrganismo'
					AND Periodo = '$Periodo'
					AND SistemaFuente = '$SistemaFuente'";
		$field = getRecords($sql);
		if (count($field)) die('Existen '.count($field).' documentos para el Organismo y Periodo, si continua la información será reeemplazada.<br>Continuar de todas formas?');
	}
	//	modificar
	elseif($accion == "modificar") {
		$sql = "SELECT * FROM co_registroventas WHERE CodRegistro = '$codigo'";
		$field = getRecord($sql);
		if ($field['Estado'] != 'PE') die('No puede modificar un registro <strong>'.printValores('registro-ventas-estado',$field['Estado']).'</strong>');
		elseif ($field['SistemaFuente'] != 'MA') die('No puede modificar este registro');
	}
}
?>