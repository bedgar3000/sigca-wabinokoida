<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion.sql", "w+");
///////////////////////////////////////////////////////////////////////////////
//	CAJA CHICA (NUEVO, MODIFICAR, APROBAR, ANULAR)
///////////////////////////////////////////////////////////////////////////////
//	caja chica
if ($modulo == "caja_chica") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-----------------
		if (setNumero($MontoTotal) > setNumero($MontoAutorizado)) die("El Monto Total excede al Monto Autorizado");
		//	genero codigo
		$NroCajaChica = getCodigo("ap_cajachica", "NroCajaChica", 4, "Periodo", $Periodo, "FlagCajaChica", $FlagCajaChica);
		//	inserto
		$sql = "INSERT INTO ap_cajachica
				SET
					FlagCajaChica = '".$FlagCajaChica."',
					Periodo = '".$Periodo."',
					NroCajaChica = '".$NroCajaChica."',
					CodOrganismo = '".$CodOrganismo."',
					CodDependencia = '".$CodDependencia."',
					CodResponsable = '".$CodBeneficiario."',
					CodClasificacion = '".$CodClasificacion."',
					CodCentroCosto = '".$CodCentroCosto."',
					CodBeneficiario = '".$CodBeneficiario."',
					CodPersonaPagar = '".$CodPersonaPagar."',
					NomPersonaPagar = '".$NomPersonaPagar."',
					CodTipoPago = '".$CodTipoPago."',
					PreparadoPor = '".$PreparadoPor."',
					FechaPreparacion = '".formatFechaAMD($FechaPreparacion)."',
					Descripcion = '".$Descripcion."',
					MontoAfecto = '".setNumero($MontoAfecto)."',
					MontoNoAfecto = '".setNumero($MontoNoAfecto)."',
					MontoImpuesto = '".setNumero($MontoImpuesto)."',
					MontoRetencion = '".setNumero($MontoRetencion)."',
					MontoTotal = '".setNumero($MontoTotal)."',
					MontoNeto = '".setNumero($MontoTotal)."',
					CodPresupuesto = '".$CodPresupuesto."',
					FlagReposicionFinal = '".($FlagReposicionFinal?'S':'N')."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		//	conceptos de gastos
		$MontoTotal = 0;
		$_Secuencia = 0;
		$i = 0;
		foreach($conceptos_CodConceptoGasto as $CodConceptoGasto) {
			$conceptos_MontoPagado[$i] = setNumero($conceptos_MontoPagado[$i]);
			$conceptos_MontoAfecto[$i] = setNumero($conceptos_MontoAfecto[$i]);
			$conceptos_MontoNoAfecto[$i] = setNumero($conceptos_MontoNoAfecto[$i]);
			$conceptos_MontoImpuesto[$i] = setNumero($conceptos_MontoImpuesto[$i]);
			$conceptos_MontoRetencion[$i] = setNumero($conceptos_MontoRetencion[$i]);
			$MontoTotal += $conceptos_MontoPagado[$i];
			$_MontoBruto = $conceptos_MontoAfecto[$i] + $conceptos_MontoNoAfecto[$i];
			$_MontoBruto = round($_MontoBruto, 2);
			if ($conceptos_CodRegimenFiscal[$i] == "N") $conceptos_FlagNoAfectoIGV[$i] = "S"; else $conceptos_FlagNoAfectoIGV[$i] = "N";
			##	
			if ($_PARAMETRO['UBACVIAT'] == 'UCAU')
				$UnidadTributaria = getVar3("SELECT Valor FROM mastunidadaritmetica WHERE Anio = '$_PARAMETRO[ANIOUBCVIAT]'");
			else
				$UnidadTributaria = getVar3("SELECT Valor FROM mastunidadtributaria WHERE Anio = '$_PARAMETRO[UTANIOVIAT]'");
			$Tope = 20 * $UnidadTributaria;
			##	
			if ($conceptos_MontoPagado[$i] > $Tope) die("El Monto del gasto: <strong>".$conceptos_Descripcion[$i]."</strong>, excede el monto máximo establecido en la ley <strong>(".number_format($UnidadTributaria,2,',','.')." * 20) = ".number_format($Tope,2,',','.')."</strong>");
			##	inserto
			$sql = "INSERT INTO ap_cajachicadetalle
					SET
						FlagCajaChica = '".$FlagCajaChica."',
						Periodo = '".$Periodo."',
						NroCajaChica = '".$NroCajaChica."',
						Secuencia = '".++$_Secuencia."',
						CodConceptoGasto = '".$conceptos_CodConceptoGasto[$i]."',
						Fecha = '".formatFechaAMD($conceptos_Fecha[$i])."',
						Descripcion = '".$conceptos_Descripcion[$i]."',
						CodRegimenFiscal = '".$conceptos_CodRegimenFiscal[$i]."',
						DocFiscal = '".$conceptos_DocFiscal[$i]."',
						CodProveedor = '".$conceptos_CodProveedor[$i]."',
						NomProveedor = '".$conceptos_NomProveedor[$i]."',
						MontoAfecto = '".$conceptos_MontoAfecto[$i]."',
						MontoNoAfecto = '".$conceptos_MontoNoAfecto[$i]."',
						MontoImpuesto = '".$conceptos_MontoImpuesto[$i]."',
						MontoRetencion = '".$conceptos_MontoRetencion[$i]."',
						MontoPagado = '".$conceptos_MontoPagado[$i]."',
						CodTipoServicio = '".$conceptos_CodTipoServicio[$i]."',
						Comentarios = '".$conceptos_Descripcion[$i]."',
						NroRecibo = '".$conceptos_NroRecibo[$i]."',
						CodTipoDocumento = '".$conceptos_CodTipoDocumento[$i]."',
						NroDocumento = '".$conceptos_NroDocumento[$i]."',
						FlagNoAfectoIGV = '".$conceptos_FlagNoAfectoIGV[$i]."',
						CodEmpleado = '".$CodBeneficiario."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
			##	distribucion
			if ($conceptos_Distribucion[$i] == "") {
				$conceptos_Distribucion[$i] = $conceptos_CodConceptoGasto[$i]."|".$CodCentroCosto."|".$conceptos_CodPartida[$i]."|".$conceptos_CodCuenta[$i]."|".$conceptos_CodCuentaPub20[$i]."|".$_MontoBruto;
			}
			$_MontoDistribuido = 0;
			$_Linea = 0;
			$distribucion = split(";", $conceptos_Distribucion[$i]);
			foreach ($distribucion as $detalle) {
				list($_dCodConceptoGasto, $_dCodCentroCosto, $_dCodPartida, $_dCodCuenta, $_dCodCuentaPub20, $_dMonto) = split("[|]", $detalle);
				$_MontoDistribuido += $_dMonto;
				//	inserto
				$sql = "INSERT INTO ap_cajachicadistribucion
						SET
							FlagCajaChica = '".$FlagCajaChica."',
							Periodo = '".$Periodo."',
							NroCajaChica = '".$NroCajaChica."',
							Secuencia = '".$_Secuencia."',
							Linea = '".++$_Linea."',
							CodConceptoGasto = '".$_dCodConceptoGasto."',
							Monto = '".$_dMonto."',
							CodOrganismo = '".$CodOrganismo."',
							CodCentroCosto = '".$_dCodCentroCosto."',
							CodPartida = '".$_dCodPartida."',
							CodCuenta = '".$_dCodCuenta."',
							CodCuentaPub20 = '".$_dCodCuentaPub20."',
							CodPresupuesto = '".$CodPresupuesto."',
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()";
				execute($sql);
			}
			$_MontoDistribuido = round($_MontoDistribuido, 2);
			if ($_MontoBruto != $_MontoDistribuido) die("Se encontraron inconsistencias en la Distribucion.<br><strong>($_Descripcion)</strong>");
			##	
			++$i;
		}
		//	-----------------
		mysql_query("COMMIT");
	}
	//	modificar registro
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		//	-----------------
		if (setNumero($MontoTotal) > setNumero($MontoAutorizado)) die("El Monto Total excede al Monto Autorizado");
		##	modifico
		$sql = "UPDATE ap_cajachica
				SET
					CodPersonaPagar = '".$CodPersonaPagar."',
					NomPersonaPagar = '".$NomPersonaPagar."',
					Descripcion = '".$Descripcion."',
					MontoAfecto = '".setNumero($MontoAfecto)."',
					MontoNoAfecto = '".setNumero($MontoNoAfecto)."',
					MontoImpuesto = '".setNumero($MontoImpuesto)."',
					MontoRetencion = '".setNumero($MontoRetencion)."',
					MontoTotal = '".setNumero($MontoTotal)."',
					MontoNeto = '".setNumero($MontoTotal)."',
					CodPresupuesto = '".$CodPresupuesto."',
					FlagReposicionFinal = '".($FlagReposicionFinal?'S':'N')."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					FlagCajaChica = '".$FlagCajaChica."' AND
					Periodo = '".$Periodo."' AND
					NroCajaChica = '".$NroCajaChica."'";
		execute($sql);
		//	conceptos de gastos
		##	elimino
		$sql = "DELETE FROM ap_cajachicadetalle
				WHERE
					FlagCajaChica = '".$FlagCajaChica."' AND
					Periodo = '".$Periodo."' AND
					NroCajaChica = '".$NroCajaChica."'";
		execute($sql);
		$sql = "DELETE FROM ap_cajachicadistribucion
				WHERE
					FlagCajaChica = '".$FlagCajaChica."' AND
					Periodo = '".$Periodo."' AND
					NroCajaChica = '".$NroCajaChica."'";
		execute($sql);
		##	
		$MontoTotal = 0;
		$_Secuencia = 0;
		$i = 0;
		foreach($conceptos_CodConceptoGasto as $CodConceptoGasto) {
			$conceptos_MontoPagado[$i] = setNumero($conceptos_MontoPagado[$i]);
			$conceptos_MontoAfecto[$i] = setNumero($conceptos_MontoAfecto[$i]);
			$conceptos_MontoNoAfecto[$i] = setNumero($conceptos_MontoNoAfecto[$i]);
			$conceptos_MontoImpuesto[$i] = setNumero($conceptos_MontoImpuesto[$i]);
			$conceptos_MontoRetencion[$i] = setNumero($conceptos_MontoRetencion[$i]);
			$MontoTotal += $conceptos_MontoPagado[$i];
			$_MontoBruto = $conceptos_MontoAfecto[$i] + $conceptos_MontoNoAfecto[$i];
			$_MontoBruto = round($_MontoBruto, 2);
			if ($conceptos_CodRegimenFiscal[$i] == "N") $conceptos_FlagNoAfectoIGV[$i] = "S"; else $conceptos_FlagNoAfectoIGV[$i] = "N";
			##	
			if ($_PARAMETRO['UBACVIAT'] == 'UCAU')
				$UnidadTributaria = getVar3("SELECT Valor FROM mastunidadaritmetica WHERE Anio = '$_PARAMETRO[ANIOUBCVIAT]'");
			else
				$UnidadTributaria = getVar3("SELECT Valor FROM mastunidadtributaria WHERE Anio = '$_PARAMETRO[UTANIOVIAT]'");
			$Tope = 20 * $UnidadTributaria;
			##	
			if ($conceptos_MontoPagado[$i] > $Tope) die("El Monto del gasto: <strong>".$conceptos_Descripcion[$i]."</strong>, excede el monto máximo establecido en la ley <strong>(".number_format($UnidadTributaria,2,',','.')." * 20) = ".number_format($Tope,2,',','.')."</strong>");
			##	inserto
			$sql = "INSERT INTO ap_cajachicadetalle
					SET
						FlagCajaChica = '".$FlagCajaChica."',
						Periodo = '".$Periodo."',
						NroCajaChica = '".$NroCajaChica."',
						Secuencia = '".++$_Secuencia."',
						CodConceptoGasto = '".$conceptos_CodConceptoGasto[$i]."',
						Fecha = '".formatFechaAMD($conceptos_Fecha[$i])."',
						Descripcion = '".$conceptos_Descripcion[$i]."',
						CodRegimenFiscal = '".$conceptos_CodRegimenFiscal[$i]."',
						DocFiscal = '".$conceptos_DocFiscal[$i]."',
						CodProveedor = '".$conceptos_CodProveedor[$i]."',
						NomProveedor = '".$conceptos_NomProveedor[$i]."',
						MontoAfecto = '".$conceptos_MontoAfecto[$i]."',
						MontoNoAfecto = '".$conceptos_MontoNoAfecto[$i]."',
						MontoImpuesto = '".$conceptos_MontoImpuesto[$i]."',
						MontoRetencion = '".$conceptos_MontoRetencion[$i]."',
						MontoPagado = '".$conceptos_MontoPagado[$i]."',
						CodTipoServicio = '".$conceptos_CodTipoServicio[$i]."',
						Comentarios = '".$conceptos_Descripcion[$i]."',
						NroRecibo = '".$conceptos_NroRecibo[$i]."',
						CodTipoDocumento = '".$conceptos_CodTipoDocumento[$i]."',
						NroDocumento = '".$conceptos_NroDocumento[$i]."',
						FlagNoAfectoIGV = '".$conceptos_FlagNoAfectoIGV[$i]."',
						CodEmpleado = '".$CodBeneficiario."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
			##	distribucion
			if ($conceptos_Distribucion[$i] == "") {
				$conceptos_Distribucion[$i] = $conceptos_CodConceptoGasto[$i]."|".$CodCentroCosto."|".$conceptos_CodPartida[$i]."|".$conceptos_CodCuenta[$i]."|".$conceptos_CodCuentaPub20[$i]."|".$_MontoBruto;
			}
			$_MontoDistribuido = 0;
			$_Linea = 0;
			$distribucion = split(";", $conceptos_Distribucion[$i]);
			foreach ($distribucion as $detalle) {
				list($_dCodConceptoGasto, $_dCodCentroCosto, $_dCodPartida, $_dCodCuenta, $_dCodCuentaPub20, $_dMonto) = split("[|]", $detalle);
				$_MontoDistribuido += $_dMonto;
				//	inserto
				$sql = "INSERT INTO ap_cajachicadistribucion
						SET
							FlagCajaChica = '".$FlagCajaChica."',
							Periodo = '".$Periodo."',
							NroCajaChica = '".$NroCajaChica."',
							Secuencia = '".$_Secuencia."',
							Linea = '".++$_Linea."',
							CodConceptoGasto = '".$_dCodConceptoGasto."',
							Monto = '".$_dMonto."',
							CodOrganismo = '".$CodOrganismo."',
							CodCentroCosto = '".$_dCodCentroCosto."',
							CodPartida = '".$_dCodPartida."',
							CodCuenta = '".$_dCodCuenta."',
							CodCuentaPub20 = '".$_dCodCuentaPub20."',
							CodPresupuesto = '".$CodPresupuesto."',
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()";
				execute($sql);
			}
			$_MontoDistribuido = round($_MontoDistribuido, 2);
			if ($_MontoBruto != $_MontoDistribuido) die("Se encontraron inconsistencias en la Distribucion.<br><strong>($_Descripcion)</strong>");
			##	
			++$i;
		}
		//	-----------------
		mysql_query("COMMIT");
	}
	//	aprobar
	elseif ($accion == "aprobar") {
		mysql_query("BEGIN");
		//	-----------------
		$sql = "SELECT *
				FROM ap_cajachica
				WHERE
					FlagCajaChica = '".$FlagCajaChica."' AND
					Periodo = '".$Periodo."' AND
					NroCajaChica = '".$NroCajaChica."'";
		$field_cc = getRecord($sql);
		##
		if ((setNumero($MontoTotal) < (setNumero($MontoAutorizado) * $_PARAMETRO['REPCC'] / 100)) && $field_cc['FlagReposicionFinal'] == "N") die("El Monto a Reembolsar es menor al $_PARAMETRO[REPCC]% del Monto Autorizado");
		//	genero nro para la obligacion
		$NroRegistro = getCodigo("ap_obligaciones", "NroRegistro", 6, "CodOrganismo", $CodOrganismo);		
		//	genero nro de documento para la obligacion
		$sql = "SELECT *
				FROM ap_obligaciones
				WHERE
					CodProveedor = '".$CodBeneficiario."' AND
					CodTipoDocumento = '".$CodClasificacion."' AND
					(NroDocumento = '00$NroCajaChica' OR NroDocumento LIKE '00$NroCajaChica-%')";
		$query_nro = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		if (mysql_num_rows($query_nro)) {
			$nro = mysql_num_rows($query_nro) + 1;
			$NroDocumento = "00$NroCajaChica-$nro";
		} else $NroDocumento = "00$NroCajaChica";
		//	modifico
		if ($field_cc['FlagReposicionFinal'] == "S") $FlagGenerarPago = "N"; else $FlagGenerarPago = "S";
		$sql = "UPDATE ap_cajachica
				SET
					AprobadoPor = '".$AprobadoPor."',
					FechaAprobacion = '".formatFechaAMD($FechaAprobacion)."',
					CodTipoDocumento = '".$CodClasificacion."',
					NroDocumento = '".$NroDocumento."',
					NroDocumentoInterno = '".$NroDocumento."',
					Estado = 'AP',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					FlagCajaChica = '".$FlagCajaChica."' AND
					Periodo = '".$Periodo."' AND
					NroCajaChica = '".$NroCajaChica."'";
		execute($sql);
		//	obligacion
		$sql = "INSERT INTO ap_obligaciones (
					CodProveedor,
					CodTipoDocumento,
					NroDocumento,
					NroControl,
					CodOrganismo,
					CodTipoPago,
					NroCuenta,
					CodTipoServicio,
					FechaRegistro,
					FechaVencimiento,
					FechaRecepcion,
					FechaDocumento,
					FechaProgramada,
					NroRegistro,
					MontoObligacion,
					MontoImpuesto,
					MontoImpuestoOtros,
					MontoNoAfecto,
					MontoAfecto,
					IngresadoPor,
					FechaPreparacion,
					RevisadoPor,
					FechaRevision,
					Comentarios,
					ComentariosAdicional,
					CodCentroCosto,
					CodProveedorPagar,
					CodResponsable,
					FlagCajaChica,
					Estado,
					UltimoUsuario,
					UltimaFecha,
					Periodo,
					CodPresupuesto,
					FlagGenerarPago
				)
				SELECT
					cc.CodBeneficiario,
					cc.CodClasificacion,
					'".$NroDocumento."' AS NroDocumento,
					'".$NroDocumento."' AS NroControl,
					cc.CodOrganismo,
					cc.CodTipoPago,
					cbd.NroCuenta,
					'".$_PARAMETRO[TSERVCC]."' AS CodTipoServicio,
					NOW(),
					cc.FechaAprobacion,
					cc.FechaPreparacion,
					cc.FechaPreparacion,
					cc.FechaAprobacion,
					'".$NroRegistro."' AS NroRegistro,
					'".setNumero($MontoTotal)."' AS MontoObligacion,
					'".setNumero($MontoImpuesto)."' AS MontoImpuesto,
					'".setNumero($MontoRetencion)."' AS MontoImpuestoOtros,
					'".setNumero($MontoNoAfecto)."' AS MontoNoAfecto,
					'".setNumero($MontoAfecto)."' AS MontoAfecto,
					cc.PreparadoPor,
					cc.FechaPreparacion,
					cc.PreparadoPor,
					cc.FechaPreparacion,
					cc.Descripcion,
					cc.Descripcion,
					cc.CodCentroCosto,
					cc.CodPersonaPagar,
					cc.CodResponsable,
					'S' AS FlagCajaChica,
					'PR' AS Estado,
					'".$_SESSION["USUARIO_ACTUAL"]."' AS UltimoUsuario,
					NOW() AS UltimaFecha,
					NOW() AS Periodo,
					'".$CodPresupuesto."' AS CodPresupuesto,
					'".$FlagGenerarPago."' AS FlagGenerarPago
				FROM
					ap_cajachica cc
					LEFT JOIN ap_ctabancariadefault cbd ON (cc.CodOrganismo = cbd.CodOrganismo AND cc.CodTipoPago = cbd.CodTipoPago)
				WHERE
					FlagCajaChica = '".$FlagCajaChica."' AND
					Periodo = '".$Periodo."' AND
					NroCajaChica = '".$NroCajaChica."'";
		execute($sql);
		//	obligacion x cuentas
		$sql = "SELECT
					ccd.*,
					cg.Descripcion,
					cc.MontoImpuesto
				FROM
					ap_cajachicadistribucion ccd
					INNER JOIN ap_conceptogastos cg ON (ccd.CodConceptoGasto = cg.CodConceptoGasto)
					INNER JOIN ap_cajachicadetalle cc ON (cc.FlagCajaChica = ccd.FlagCajaChica AND
													      cc.Periodo = ccd.Periodo AND
														  cc.NroCajaChica = ccd.NroCajaChica AND
														  cc.Secuencia = ccd.Secuencia)
				WHERE
					ccd.FlagCajaChica = '".$FlagCajaChica."' AND
					ccd.Periodo = '".$Periodo."' AND
					ccd.NroCajaChica = '".$NroCajaChica."'
				ORDER BY Secuencia, Linea";
		$query_distribucion = mysql_query($sql) or die($sql.mysql_error());
		while($field_distribucion = mysql_fetch_array($query_distribucion)) {
			if ($field_distribucion['MontoImpuesto'] > 0) $FlagNoAfectoIGV = "N"; else $FlagNoAfectoIGV = "S";
			$sql = "INSERT INTO ap_obligacionescuenta
					SET
						CodProveedor = '".$CodBeneficiario."',
						CodTipoDocumento = '".$CodClasificacion."',
						NroDocumento = '".$NroDocumento."',
						Secuencia = '".$field_distribucion['Secuencia']."',
						Linea = '".$field_distribucion['Linea']."',
						Descripcion = '".$field_distribucion['Descripcion']."',
						Monto = '".$field_distribucion['Monto']."',
						CodCentroCosto = '".$field_distribucion['CodCentroCosto']."',
						CodCuenta = '".$field_distribucion['CodCuenta']."',
						CodCuentaPub20 = '".$field_distribucion['CodCuentaPub20']."',
						cod_partida = '".$field_distribucion['CodPartida']."',
						FlagNoAfectoIGV = '".$FlagNoAfectoIGV."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		//	obligacion x impuestos
		$sql = "SELECT
					i.CodImpuesto,
					i.FactorPorcentaje,
					i.Signo,
					i.CodRegimenFiscal,
					i.FlagImponible,
					ccd.MontoAfecto,
					ccd.MontoImpuesto
				FROM
					mastimpuestos i
					INNER JOIN masttiposervicioimpuesto tsi ON (i.CodImpuesto = tsi.CodImpuesto)
					INNER JOIN ap_cajachicadetalle ccd ON (tsi.CodTipoServicio = ccd.CodTipoServicio)
				WHERE
					i.CodRegimenFiscal = 'R' AND
					ccd.FlagCajaChica = '".$FlagCajaChica."' AND
					ccd.Periodo = '".$Periodo."' AND
					ccd.NroCajaChica = '".$NroCajaChica."'";
		$query_impuestos = mysql_query($sql) or die($sql.mysql_error());	$_Linea = 0;
		while ($field_impuestos = mysql_fetch_array($query_impuestos)) {	$_Linea++;
			if ($field_impuestos['FlagImponible'] == "N") $_MontoAfecto = $field_impuestos['MontoAfecto'];
			elseif ($field_impuestos['FlagImponible'] == "I") $_MontoAfecto = $field_impuestos['MontoImpuesto'];
			$_MontoImpuesto = $_MontoAfecto * $field_impuestos['FactorPorcentaje'] / 100;
			if ($field_impuestos['Signo'] == "N") $_MontoRetencion *= (-1);
			#			
			$sql = "INSERT INTO ap_obligacionesimpuesto
					SET
						CodProveedor = '".$CodBeneficiario."',
						CodTipoDocumento = '".$CodClasificacion."',
						NroDocumento = '".$NroDocumento."',
						Linea = '".$_Linea."',
						CodImpuesto = '".$field_impuestos['CodImpuesto']."',
						FactorPorcentaje = '".$field_impuestos['FactorPorcentaje']."',
						MontoImpuesto = '".$_MontoImpuesto."',
						MontoAfecto = '".$_MontoAfecto."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		//	obligacion distribucion
		$Secuencia = 0;
		list($_cod_partida_igv, $_CodCuenta_igv, $_CodCuentaPub20_igv) = getPartidaCuentaFromIGV($_PARAMETRO["IGVCODIGO"]);
		$sql = "(SELECT
					cod_partida,
					CodCuenta,
					CodCuentaPub20,
					CodCentroCosto,
					SUM(Monto) AS Monto
				FROM ap_obligacionescuenta 
				WHERE
					CodProveedor = '".$CodBeneficiario."' AND
					CodTipoDocumento = '".$CodClasificacion."' AND
					NroDocumento = '".$NroDocumento."'
				GROUP BY cod_partida, CodCuenta, CodCentroCosto)
				UNION
				(SELECT
					'".$_cod_partida_igv."' AS cod_partida,
					'".$_CodCuenta_igv."' AS CodCuenta_igv,
					'".$_CodCuentaPub20_igv."' AS CodCuentaPub20_igv,
					'".$CodCentroCosto."' AS CodCentroCosto,
					'".setNumero($MontoImpuesto)."' AS Monto)
				ORDER BY cod_partida";
		$query_distribucion = mysql_query($sql) or die($sql.mysql_error());
		while ($field_distribucion = mysql_fetch_array($query_distribucion)) {
			//	compromisos
			$sql = "INSERT INTO lg_distribucioncompromisos
					SET
						Anio = NOW(),
						CodOrganismo = '".$CodOrganismo."',
						CodProveedor = '".$CodBeneficiario."',
						CodTipoDocumento = '".$CodClasificacion."',
						NroDocumento = '".$NroDocumento."',
						Secuencia = '".++$Secuencia."',
						Linea = '1',
						Mes = SUBSTRING(NOW(), 6, 2),
						CodCentroCosto = '".$field_distribucion['CodCentroCosto']."',
						cod_partida = '".$field_distribucion['cod_partida']."',
						Monto = '".$field_distribucion['Monto']."',
						Periodo = NOW(),
						Origen = 'OB',
						CodPresupuesto = '".$CodPresupuesto."',
						Estado = 'PE',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
			//	causado
			$sql = "INSERT INTO ap_distribucionobligacion
					SET
						CodProveedor = '".$CodBeneficiario."',
						CodTipoDocumento = '".$CodClasificacion."',
						NroDocumento = '".$NroDocumento."',
						CodCuenta = '".$field_distribucion['CodCuenta']."',
						CodCuentaPub20 = '".$field_distribucion['CodCuentaPub20']."',
						cod_partida = '".$field_distribucion['cod_partida']."',
						CodCentroCosto = '".$field_distribucion['CodCentroCosto']."',
						Monto = '".$field_distribucion['Monto']."',
						Periodo = NOW(),
						Estado = 'PE',
						Anio = NOW(),
						FlagCompromiso = 'S',
						Origen = 'OB',
						CodPresupuesto = '".$CodPresupuesto."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		//	-----------------
		mysql_query("COMMIT");
	}
	//	anular
	elseif ($accion == "anular") {
		mysql_query("BEGIN");
		//	-----------------
		//	modifico
		$sql = "UPDATE ap_cajachica
				SET
					Estado = 'AN',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					FlagCajaChica = '".$FlagCajaChica."' AND
					Periodo = '".$Periodo."' AND
					NroCajaChica = '".$NroCajaChica."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
//	validar
elseif ($modulo == "validar") {
	if ($accion == "modificar" || $accion == "aprobar" || $accion == "anular") {
		list($FlagCajaChica, $Periodo, $NroCajaChica) = split("[_]", $codigo);
		$sql = "SELECT Estado
				FROM ap_cajachica
				WHERE
					FlagCajaChica = '".$FlagCajaChica."' AND 
					Periodo = '".$Periodo."' AND 
					NroCajaChica = '".$NroCajaChica."'";
		$Estado = getVar3($sql);
		if ($Estado != 'PR') die("No puede $accion este registro");
	}
}
//	ajax
elseif ($modulo == "ajax") {
	//	concepto
	if ($accion == "caja_chica_conceptos_insertar") {
		list($CodRegimenFiscal, $NomRegimenFiscal) = getPrimeroDefault("ap_regimenfiscal", "CodRegimenFiscal", "Descripcion");
		list($CodTipoServicio, $NomTipoServicio) = getPrimeroDefault("masttiposervicio", "CodTipoServicio", "Descripcion", 0, "CodRegimenFiscal", $CodRegimenFiscal);
		if ($CodRegimenFiscal == "I") {
			$dMontoRetencion = "disabled";
		}
		//	
		$nro_conceptos = $nro_detalles;
		$sql = "SELECT
					CodConceptoGasto,
					Descripcion As NomConceptoGasto,
					CodPartida,
					CodCuenta,
					CodCuentaPub20
				FROM ap_conceptogastos
				WHERE CodConceptoGasto = '".$CodConceptoGasto."'";
		$query_detalle = mysql_query($sql) or die($sql.mysql_error());
		$query_conceptos = mysql_query($sql) or die ($sql.mysql_error());
		while ($field_conceptos = mysql_fetch_array($query_conceptos)) {
			$id = $nro_conceptos;
			?>
			<tr class="trListaBody" onclick="clk($(this), 'conceptos', 'conceptos_<?=$id?>');" id="conceptos_<?=$id?>">
				<th>
					<?=$nro_conceptos?>
				</th>
				<td>
					<input type="text" name="conceptos_Fecha[]" style="text-align:center;" class="cell datepicker" maxlength="10" />
				</td>
				<td>
					<input type="hidden" name="conceptos_CodConceptoGasto[]" id="CodConceptoGasto_<?=$id?>" value="<?=$field_conceptos['CodConceptoGasto']?>" />
					<textarea style="height:25px;" class="cell2" readonly="readonly"><?=htmlentities($field_conceptos['NomConceptoGasto'])?></textarea>
				</td>
				<td>
					<textarea name="conceptos_Descripcion[]" style="height:25px;" class="cell"></textarea>
				</td>
				<td>
					<input type="text" name="conceptos_MontoPagado[]" id="MontoPagado_<?=$id?>" value="0,00" style="text-align:right;" class="cell currency" onchange="cajaChicaMontoPagado('<?=$id?>');" onfocus="nroFocus($(this));" onblur="nroBlur($(this));" <?=$dMontoPagado?> />
				</td>
				<td>
					<select name="conceptos_CodRegimenFiscal[]" style="width:130px;" class="cell" onChange="getOptionsSelect(this.value, 'tipo-servicio', 'CodTipoServicio_<?=$id?>', 1); caja_chica_bloquear_montos($(this).val());">
						<?=loadSelect("ap_regimenfiscal", "CodRegimenFiscal", "Descripcion", $CodRegimenFiscal, 0)?>
					</select>
				</td>
				<td>
					<select name="conceptos_CodTipoServicio[]" id="CodTipoServicio_<?=$id?>" style="width:130px;" class="cell">
						<?=loadSelectDependiente("masttiposervicio", "CodTipoServicio", "Descripcion", "CodRegimenFiscal", $CodTipoServicio, $CodRegimenFiscal, 0)?>
					</select>
				</td>
				<td>
					<input type="text" name="conceptos_MontoAfecto[]" id="MontoAfecto_<?=$id?>" value="0,00" style="text-align:right;" class="cell currency" onchange="cajaChicaMontoAfecto('<?=$id?>');" onfocus="nroFocus($(this));" onblur="nroBlur($(this));" <?=$dMontoAfecto?> />
				</td>
				<td>
					<input type="text" name="conceptos_MontoNoAfecto[]" id="MontoNoAfecto_<?=$id?>" value="0,00" style="text-align:right;" class="cell currency" onchange="cajaChicaMontoNoAfecto('<?=$id?>');" onfocus="nroFocus($(this));" onblur="nroBlur($(this));" <?=$dMontoNoAfecto?> />
				</td>
				<td>
					<input type="text" name="conceptos_MontoImpuesto[]" id="MontoImpuesto_<?=$id?>" value="0,00" style="text-align:right;" class="cell currency" onfocus="nroFocus($(this));" onblur="nroBlur($(this));" <?=$dMontoImpuesto?> />
				</td>
				<td>
					<input type="text" name="conceptos_MontoRetencion[]" id="MontoRetencion_<?=$id?>" value="0,00" style="text-align:right;" class="cell currency" onfocus="nroFocus($(this));" onblur="nroBlur($(this));" <?=$dMontoRetencion?> />
				</td>
				<td width="45">
					<select name="conceptos_CodTipoDocumento[]" class="cell">
						<?=loadSelect("ap_tipodocumento", "CodTipoDocumento", "Descripcion", "FG", 10)?>
					</select>
				</td>
				<td width="125">
					<input type="text" name="conceptos_NroDocumento[]" class="cell" maxlength="20" />
				</td>
				<td>
					<input type="text" name="conceptos_NroRecibo[]" class="cell" maxlength="20" />
				</td>
				<td>
					<input type="text" name="conceptos_DocFiscal[]" id="DocFiscal_<?=$id?>" class="cell2" maxlength="15" readonly="readonly" />
				</td>
				<td>
					<input type="hidden" name="conceptos_CodProveedor[]" id="CodProveedor_<?=$id?>" />
					<input type="text" name="conceptos_NomProveedor[]" id="NomProveedor_<?=$id?>" class="cell2 iEditable" readonly="readonly" onfocus="caja_chica_habilitar_proveedor('<?=$id?>');" />
					
					<input type="hidden" name="conceptos_CodPartida[]" id="CodPartida_<?=$id?>" value="<?=$field_conceptos['CodPartida']?>" />
					<input type="hidden" name="conceptos_CodCuenta[]" id="CodCuenta_<?=$id?>" value="<?=$field_conceptos['CodCuenta']?>" />
					<input type="hidden" name="conceptos_CodCuentaPub20[]" id="CodCuentaPub20_<?=$id?>" value="<?=$field_conceptos['CodCuentaPub20']?>" />
					<input type="hidden" name="conceptos_Distribucion[]" id="Distribucion_<?=$id?>" />
				</td>
			</tr>
			<?php
		}
	}
	//	concepto (distribucion)
	elseif ($accion == "caja_chica_distribucion_insertar") {
		$nro_conceptos = $nro_detalles;
		$sql = "SELECT
					CodConceptoGasto,
					Descripcion As NomConceptoGasto,
					CodPartida,
					CodCuenta,
					CodCuentaPub20
				FROM ap_conceptogastos
				WHERE CodConceptoGasto = '".$CodConceptoGasto."'";
		$query_detalle = mysql_query($sql) or die($sql.mysql_error());
		$query_conceptos = mysql_query($sql) or die ($sql.mysql_error());
		while ($field_conceptos = mysql_fetch_array($query_conceptos)) {
			$id = $nro_conceptos;
			?>
			<tr class="trListaBody" onclick="clk($(this), 'conceptos', 'conceptos_<?=$id?>');" id="conceptos_<?=$id?>">
				<th>
					<?=$nro_detalles?>
				</th>
				<td>
					<input type="hidden" name="CodConceptoGasto" value="<?=$field_conceptos['CodConceptoGasto']?>" />
					<textarea name="NomConceptoGasto" style="height:25px;" class="cell2" readonly><?=htmlentities($field_conceptos['NomConceptoGasto'])?></textarea>
				</td>
				<td>
					<input type="text" name="CodCentroCosto" id="CodCentroCosto_<?=$id?>" style="text-align:center;" class="cell2" value="<?=$CodCentroCosto?>" readonly />
				</td>
				<td>
					<input type="text" name="CodPartida" id="CodPartida_<?=$id?>" style="text-align:center;" class="cell2" value="<?=$field_conceptos['CodPartida']?>" readonly />
				</td>
				<td>
					<input type="text" name="CodCuenta" id="CodCuenta_<?=$id?>" style="text-align:center;" class="cell2" value="<?=$field_conceptos['CodCuenta']?>" readonly />
				</td>
				<td>
					<input type="text" name="CodCuentaPub20" id="CodCuentaPub20_<?=$id?>" style="text-align:center;" class="cell2" value="<?=$field_conceptos['CodCuentaPub20']?>" readonly />
				</td>
				<td>
					<input type="text" name="Monto" id="Monto_<?=$id?>" value="0,00" style="text-align:right;" class="cell currency" onfocus="nroFocus($(this));" onblur="nroBlur($(this));" onchange="caja_chica_distribucion_totales();" <?=$disabled_conceptos?> />
				</td>
			</tr>
			<?php
		}
	}
	//	distribucion contable y presupuestaria
	elseif ($accion == "mostrarTabDistribucionCajaChica") {
		//	obtengo detalles
		$_TOTAL = 0;
		list($_cod_partida_igv, $_CodCuenta_igv, $_CodCuentaPub20_igv) = getPartidaCuentaFromIGV($_PARAMETRO["IGVCODIGO"]);
		$detalle = split(";char:tr;", $detalles_conceptos);
		foreach ($detalle as $linea) {
			list($_Monto, $_cod_partida, $_CodCuenta, $_CodCuentaPub20, $_Distribucion) = split(";char:td;", $linea);
			if ($_Distribucion != "") {
				$distribucion = split(";", $_Distribucion);
				foreach ($distribucion as $detalle) {
					list($_dCodConceptoGasto, $_dCodCentroCosto, $_dCodPartida, $_dCodCuenta, $_dCodCuentaPub20, $_dMonto) = split("[|]", $detalle);
					if ($_dCodPartida != "" || $_dCodCuenta != "" || $_CodCuentaPub20 != "") {
						$_CUENTA[$_dCodCuenta] = $_dCodCuenta;
						$_CUENTA20[$_dCodCuentaPub20] = $_dCodCuentaPub20;
						$_PARTIDA[$_dCodPartida] = $_dCodPartida;
						$_PARTIDA_CUENTA[$_dCodPartida] = $_dCodCuenta;
						$_PARTIDA_CUENTA20[$_dCodPartida] = $_dCodCuentaPub20;
						$_CUENTA_MONTO[$_dCodCuenta] += $_dMonto;
						$_CUENTA_MONTO20[$_dCodCuentaPub20] += $_dMonto;
						$_PARTIDA_MONTO[$_dCodPartida] += $_dMonto;
					}
				}
			} else {
				if ($_cod_partida != "" || $_CodCuenta != "" || $_CodCuentaPub20 != "") {
					$_CUENTA[$_CodCuenta] = $_CodCuenta;
					$_CUENTA20[$_CodCuentaPub20] = $_CodCuentaPub20;
					$_PARTIDA[$_cod_partida] = $_cod_partida;
					$_PARTIDA_CUENTA[$_cod_partida] = $_CodCuenta;
					$_PARTIDA_CUENTA20[$_cod_partida] = $_CodCuentaPub20;
					$_CUENTA_MONTO[$_CodCuenta] += $_Monto;
					$_CUENTA_MONTO20[$_CodCuentaPub20] += $_Monto;
					$_PARTIDA_MONTO[$_cod_partida] += $_Monto;
				}
			}
		}
		if ($MontoImpuesto > 0) {
			$_CUENTA[$_CodCuenta_igv] = $_CodCuenta_igv;
			$_CUENTA20[$_CodCuentaPub20_igv] = $_CodCuentaPub20_igv;
			$_PARTIDA[$_cod_partida_igv] = $_cod_partida_igv;
			$_PARTIDA_CUENTA[$_cod_partida_igv] = $_CodCuenta_igv;
			$_PARTIDA_CUENTA20[$_cod_partida_igv] = $_CodCuentaPub20_igv;
			$_CUENTA_MONTO[$_CodCuenta_igv] = $MontoImpuesto;
			$_CUENTA_MONTO20[$_CodCuentaPub20_igv] = $MontoImpuesto;
			$_PARTIDA_MONTO[$_cod_partida_igv] = $MontoImpuesto;
		}
		//	imprimo cuentas
		foreach ($_CUENTA as $CodCuenta) {
			$Descripcion = getValorCampo("ac_mastplancuenta", "CodCuenta", "Descripcion", $CodCuenta);
			if ($Descripcion != "") {
				?>
				<tr class="trListaBody">
					<td align="center">
						<?=$CodCuenta?>
					</td>
					<td>
						<?=$Descripcion?>
					</td>
					<td align="right">
						<?=number_format($_CUENTA_MONTO[$CodCuenta], 2, ',', '.')?>
					</td>
				</tr>
				<?php
			}
		}
		echo "|";
		//	imprimo cuentas
		foreach ($_CUENTA20 as $CodCuentaPub20) {
			$Descripcion = getValorCampo("ac_mastplancuenta20", "CodCuenta", "Descripcion", $CodCuentaPub20);
			if ($Descripcion != "") {
				?>
				<tr class="trListaBody">
					<td align="center">
						<?=$CodCuentaPub20?>
					</td>
					<td>
						<?=$Descripcion?>
					</td>
					<td align="right">
						<?=number_format($_CUENTA_MONTO20[$CodCuentaPub20], 2, ',', '.')?>
					</td>
				</tr>
				<?php
			}
		}
		echo "|";
		//	imprimo partidas
		foreach ($_PARTIDA as $cod_partida) {
			$Descripcion = getValorCampo("pv_partida", "cod_partida", "denominacion", $cod_partida);
			list($MontoAjustado, $MontoCompromiso, $PreCompromiso, $CotizacionesAsignadas) = disponibilidadPartida2($Anio, $CodOrganismo, $cod_partida, $CodPresupuesto);
			$MontoPendiente = $PreCompromiso + $CotizacionesAsignadas;
			$MontoDisponible = $MontoAjustado - $MontoCompromiso;
			$MontoDisponibleReal = $MontoAjustado - ($MontoCompromiso + $MontoPendiente);
			//	valido
			if ($_PARTIDA_MONTO[$cod_partida] > $MontoDisponible) $style = "style='background-color:#F8637D;'";
			elseif($_PARTIDA_MONTO[$cod_partida] > $MontoDisponibleReal) $style = "style='background-color:#FFC;'";
			else $style = "style='background-color:#D0FDD2;'";
			?>
			<tr class="trListaBody" <?=$style?>>
				<td align="center">
					<input type="hidden" name="partidas_cod_partida[]" value="<?=$cod_partida?>" />
					<input type="hidden" name="partidas_Monto[]" value="<?=$_PARTIDA_MONTO[$cod_partida]?>" />
					<input type="hidden" name="partidas_MontoAjustado[]" value="<?=$MontoAjustado?>" />
					<input type="hidden" name="partidas_MontoCompromiso[]" value="<?=$MontoCompromiso?>" />
					<input type="hidden" name="partidas_PreCompromiso[]" value="<?=$PreCompromiso?>" />
					<input type="hidden" name="partidas_CotizacionesAsignadas[]" value="<?=$CotizacionesAsignadas?>" />
					<input type="hidden" name="partidas_MontoDisponible[]" value="<?=$MontoDisponible?>" />
					<input type="hidden" name="partidas_MontoDisponibleReal[]" value="<?=$MontoDisponibleReal?>" />
					<?=$cod_partida?>
				</td>
				<td><?=$Descripcion?></td>
				<td align="right"><?=number_format($_PARTIDA_MONTO[$cod_partida], 2, ',', '.')?></td>
			</tr>
			<?php
		}
	}
}
?>