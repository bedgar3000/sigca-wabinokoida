<?php
$dFlagPresupuesto = "disabled";
$dFlagDistribucionManual = "disabled";
$dFechaPreparacion = "disabled";
$dFechaRevision = "disabled";
$dFechaAprobado = "disabled";
$dFlagAgruparIgv = "disabled";
if ($opcion == "nuevo") {
	$accion = "nuevo";
	$titulo = "Nueva Obligaci&oacute;n";
	$label_submit = "Guardar";

	$sql = "SELECT MAX(Ejercicio) FROM pv_reformulacionmetas";
	$Ejercicio = getVar3($sql);
	$Ejercicio = ($Ejercicio?$Ejercicio:$AnioActual);

	$field_obligacion['Estado'] = "PR";
	$field_obligacion['CodOrganismo'] = $_SESSION["ORGANISMO_ACTUAL"];
	$field_obligacion['IngresadoPor'] = $_SESSION["CODPERSONA_ACTUAL"];
	$field_obligacion['NomIngresadoPor'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
	$field_obligacion['FechaPreparacion'] = $FechaActual;
	$field_obligacion['CodCentroCosto'] = getVar3("SELECT CodCentroCosto FROM ac_mastcentrocosto WHERE Codigo = '".$_PARAMETRO["CCOSTOPR"]."'");
	$field_obligacion['FechaFactura'] = $FechaActual;
	$field_obligacion['FechaRegistro'] = $FechaActual;
	$field_obligacion['FechaDocumento'] = $FechaActual;
	$field_obligacion['FechaRecepcion'] = $FechaActual;
	$field_obligacion['FechaVencimiento'] = $FechaActual;
	$field_obligacion['FechaProgramada'] = $FechaActual;
	$field_obligacion['Periodo'] = "$AnioActual-$MesActual";
	$field_obligacion['FlagGenerarPago'] = "S";
	$field_obligacion['FlagCompromiso'] = "N";
	$field_obligacion['FlagPresupuesto'] = "S";
	$field_obligacion['FlagDistribucionManual'] = "N";
	$disabled_impuesto = "disabled";
	$disabled_documento = "disabled";
	$disabled_distribucion = "disabled";
	$disabled_facturas = "";
	$disabled_adelantos = "";
	$disabled_anular = "disabled";
	$dFlagCompromiso = "disabled";
	$mostrarTabDistribucion = "mostrarTabDistribucionObligacion();";
	$dFechaPreparacion = "";
	$Anio = "$AnioActual";
	##	presupuesto
	$sql = "SELECT p.*
			FROM pv_presupuesto p
			INNER JOIN pv_categoriaprog cp ON (cp.CategoriaProg = p.CategoriaProg)
			INNER JOIN pv_unidadejecutora ue On (ue.CodUnidadEjec = cp.CodUnidadEjec)
			WHERE p.CodOrganismo = '".$field_obligacion['CodOrganismo']."' AND p.Ejercicio = '".$Ejercicio."' AND ue.CodCentroCosto = '".$field_obligacion['CodCentroCosto']."'";
	$field_presupuesto = getRecord($sql);
	$field_obligacion['CodPresupuesto'] = $field_presupuesto['CodPresupuesto'];
	$field_obligacion['Ejercicio'] = $field_presupuesto['Ejercicio'];
	$field_obligacion['CategoriaProg'] = $field_presupuesto['CategoriaProg'];
	$field_obligacion['CodFuente'] = $_PARAMETRO['FFMETASDEF'];
	$action = "gehen.php?anz=$origen";
}
elseif ($opcion == "modificar" || $opcion == "ver" || $opcion == "revisar" || $opcion == "aprobar" || $opcion == "anular") {
	if ($origen == "ap_registro_compra_lista") {
		list($Periodo, $SistemaFuente, $Secuencia) = split("[.]", $registro);
		//	consulto datos generales
		$sql = "SELECT
					o.*,
					p1.DocFiscal,
					p1.NomCompleto,
					p1.Busqueda,
					pv.DiasPago,
					p2.NomCompleto AS NomProveedorPagar,
					td.FlagProvision,
					td.CodVoucher,
					pv1.CategoriaProg
				FROM
					ap_registrocompras rc
					INNER JOIN ap_obligaciones o ON (rc.CodProveedor = o.CodProveedor AND
													 rc.CodTipoDocumento = o.CodTipoDocumento AND
													 rc.NroDocumento = o.NroDocumento)
					INNER JOIN mastpersonas p1 ON (o.CodProveedor = p1.CodPersona)
					INNER JOIN ap_tipodocumento td ON (o.CodTipoDocumento = td.CodTipoDocumento)
					LEFT JOIN pv_presupuesto pv1 On (pv1.CodOrganismo = o.CodOrganismo AND pv1.CodPresupuesto = o.CodPresupuesto)
					LEFT JOIN mastproveedores pv ON (p1.CodPersona = pv.CodProveedor)
					LEFT JOIN mastpersonas p2 ON (o.CodProveedorPagar = p2.CodPersona)
				WHERE
					rc.Periodo = '".$Periodo."' AND
					rc.SistemaFuente = '".$SistemaFuente."' AND
					rc.Secuencia = '".$Secuencia."'";
		$query_obligacion = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		if (mysql_num_rows($query_obligacion)) $field_obligacion = mysql_fetch_array($query_obligacion);
	}
	else {
		if (!$registro) $registro = $sel_registros;
		list($CodOrganismo, $CodProveedor, $CodTipoDocumento, $NroDocumento) = split("[_]", $registro);
		//	consulto datos generales
		$sql = "SELECT
					o.*,
					p1.DocFiscal,
					p1.NomCompleto,
					p1.Busqueda,
					pv.DiasPago,
					p2.NomCompleto AS NomProveedorPagar,
					td.FlagProvision,
					td.CodVoucher,
					p3.NomCompleto AS NomIngresadoPor,
					p4.NomCompleto AS NomRevisadoPor,
					p5.NomCompleto AS NomAprobadoPor,
					pv1.CategoriaProg
				FROM
					ap_obligaciones o
					INNER JOIN mastpersonas p1 ON (o.CodProveedor = p1.CodPersona)
					INNER JOIN ap_tipodocumento td ON (o.CodTipoDocumento = td.CodTipoDocumento)
					LEFT JOIN pv_presupuesto pv1 On (pv1.CodOrganismo = o.CodOrganismo AND pv1.CodPresupuesto = o.CodPresupuesto)
					LEFT JOIN mastproveedores pv ON (p1.CodPersona = pv.CodProveedor)
					LEFT JOIN mastpersonas p2 ON (o.CodProveedorPagar = p2.CodPersona)
					LEFT JOIN mastpersonas p3 ON (o.IngresadoPor = p3.CodPersona)
					LEFT JOIN mastpersonas p4 ON (o.RevisadoPor = p4.CodPersona)
					LEFT JOIN mastpersonas p5 ON (o.AprobadoPor = p5.CodPersona)
				WHERE
					o.CodProveedor = '".$CodProveedor."' AND
					o.CodTipoDocumento = '".$CodTipoDocumento."' AND
					o.NroDocumento = '".$NroDocumento."'";
		$query_obligacion = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		if (mysql_num_rows($query_obligacion)) $field_obligacion = mysql_fetch_array($query_obligacion);
	}
	
	if ($opcion == "modificar") {
		$titulo = "Modificar Obligaci&oacute;n";
		$accion = "modificar";
		$disabled_modificar = "disabled";
		$display_modificar = "display:none;";
		$label_submit = "Modificar";
		$disabled_anular = "disabled";
		if ($field_obligacion['FlagNomina'] == "S") $disabled_impuesto = "disabled";
		$mostrarTabDistribucion = "mostrarTabDistribucionObligacion();";
		$dFechaPreparacion = "";
	}
	
	elseif ($opcion == "ver") {
		$titulo = "Ver Obligaci&oacute;n";
		$disabled_ver = "disabled";
		$display_ver = "display:none;";
		$display_submit = "display:none;";
		$disabled_modificar = "disabled";
		$display_modificar = "display:none;";
		$dFlagCompromiso = "disabled";
		$disabled_impuesto = "disabled";
		$disabled_documento = "disabled";
		$disabled_distribucion = "disabled";
		$disabled_facturas = "disabled";
		$disabled_adelantos = "disabled";
		$disabled_anular = "disabled";
		$mostrarTabDistribucion = "mostrarTab('tab', 4, 6);";
	}
	
	elseif ($opcion == "revisar") {
		$field_obligacion['RevisadoPor'] = $_SESSION["CODPERSONA_ACTUAL"];
		$field_obligacion['NomRevisadoPor'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
		$field_obligacion['FechaRevision'] = $FechaActual;
		##
		$titulo = "Revisar Obligaci&oacute;n";
		$accion = "revisar";
		$label_submit = "Revisar";
		$disabled_ver = "disabled";
		$display_ver = "display:none;";
		$disabled_modificar = "disabled";
		$display_modificar = "display:none;";
		$dFlagCompromiso = "disabled";
		$disabled_impuesto = "disabled";
		$disabled_documento = "disabled";
		$disabled_distribucion = "disabled";
		$disabled_facturas = "disabled";
		$disabled_adelantos = "disabled";
		$disabled_anular = "disabled";
		$mostrarTabDistribucion = "mostrarTab('tab', 4, 6);";
		$dFechaRevision = "";
	}
	
	elseif ($opcion == "aprobar") {
		$field_obligacion['AprobadoPor'] = $_SESSION["CODPERSONA_ACTUAL"];
		$field_obligacion['NomAprobadoPor'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
		$field_obligacion['FechaAprobado'] = $FechaActual;
		##
		$titulo = "Aprobar Obligaci&oacute;n";
		$accion = "aprobar";
		$label_submit = "Aprobar";
		$disabled_ver = "disabled";
		$display_ver = "display:none;";
		$disabled_modificar = "disabled";
		$display_modificar = "display:none;";
		$dFlagCompromiso = "disabled";
		$disabled_impuesto = "disabled";
		$disabled_documento = "disabled";
		$disabled_distribucion = "disabled";
		$disabled_facturas = "disabled";
		$disabled_adelantos = "disabled";
		$disabled_anular = "disabled";
		$mostrarTabDistribucion = "mostrarTab('tab', 4, 6);";
		$dFechaAprobado = "";
	}
	
	elseif ($opcion == "anular") {
		$titulo = "Anular Obligaci&oacute;n";
		$accion = "anular";
		$label_submit = "Anular";
		$disabled_ver = "disabled";
		$display_ver = "display:none;";
		$disabled_modificar = "disabled";
		$display_modificar = "display:none;";
		$dFlagCompromiso = "disabled";
		$disabled_impuesto = "disabled";
		$disabled_documento = "disabled";
		$disabled_distribucion = "disabled";
		$disabled_facturas = "disabled";
		$disabled_adelantos = "disabled";
		$mostrarTabDistribucion = "mostrarTab('tab', 4, 6);";
	}
	
	$disabled_documento = "disabled";
	if ($field_obligacion['FlagDistribucionManual'] != "S") {
		$disabled_distribucion = "disabled";
		$dFlagCompromiso = "disabled";
		$dFlagPresupuesto = "disabled";
		$dFlagDistribucionManual = "disabled";
	}
	if (!afectaTipoServicio($field_obligacion['CodTipoServicio'])) {
		$dFlagNoAfectoIGV = "disabled";
	}
	$FactorImpuesto = getPorcentajeIVA($field_obligacion['CodTipoServicio']);
	$Anio = substr($field_obligacion['Periodo'], 0, 4);
	$action = "gehen.php?anz=$origen";
}
elseif ($opcion == "interfase-bono-nuevo") {
	##	
	$sql = "SELECT
				ob.CodProveedor,
				ob.CodTipoDocumento,
				ob.CodOrganismo,
				cbd.NroCuenta,
				'02' AS CodTipoPago,
				ob.CodProveedor AS CodResponsable,
				'$FechaActual' AS FechaRegistro,
				'$FechaActual' AS FechaVencimiento,
				'S' AS FlagGenerarPago,
				'NING' AS CodTipoServicio,
				'BA' AS ReferenciaTipoDocumento,
				ob.CodObligacionBono AS ReferenciaNroDocumento,
				ob.MontoObligacion,
				'0.00' AS MontoImpuestoOtros,
				ob.MontoObligacion AS MontoNoAfecto,
				'0.00' AS MontoAfecto,
				'0.00' AS MontoAdelanto,
				'0.00' AS MontoImpuesto,
				'0.00' AS MontoPagoParcial,
				'S' AS FlagContabilizacionPendiente,
				'S' AS FlagContPendientePub20,
				ob.Comentarios,
				ob.ComentariosAdicional,
				ob.CodCentroCosto,
				'$FechaActual' AS FechaRecepcion,
				ob.CodProveedor AS CodProveedorPagar,
				'$FechaActual' AS FechaDocumento,
				'N' AS FlagAfectoIGV,
				'N' AS FlagDiferido,
				'N' AS FlagAdelanto,
				'N' AS FlagPagoDiferido,
				'PR' AS Estado,
				'S' AS FlagCompromiso,
				'S' AS FlagPresupuesto,
				'S' AS FlagObligacionAuto,
				'N' AS FlagObligacionDirecta,
				'N' AS FlagCajaChica,
				'N' AS FlagPagoIndividual,
				ob.NroControl,
				ob.NroFactura,
				'$FechaActual' AS FechaProgramada,
				'$FechaActual' AS FechaPreparacion,
				'$PeriodoActual' AS Periodo,
				'N' AS FlagDistribucionManual,
				'$FechaActual' AS FechaFactura,
				'N' AS FlagVerificado,
				ob.CodPresupuesto,
				'N' AS FlagNomina,
				'N' AS FlagFacturaPendiente,
				ob.CodFuente,
				ppto.Ejercicio,
				ppto.CategoriaProg,
				p1.DocFiscal,
				p1.NomCompleto,
				p1.Busqueda,
				pv.DiasPago,
				p1.NomCompleto AS NomProveedorPagar,
				td.FlagProvision,
				td.CodVoucher,
				cc.Codigo AS NomCentroCosto
			FROM
				pr_obligacionesbono ob
				INNER JOIN mastpersonas p1 ON (ob.CodProveedor = p1.CodPersona)
				INNER JOIN ap_tipodocumento td ON (ob.CodTipoDocumento = td.CodTipoDocumento)
				LEFT JOIN ac_mastcentrocosto cc ON (ob.CodCentroCosto = cc.CodCentroCosto)
				LEFT JOIN ap_ctabancariadefault cbd ON (cbd.CodOrganismo = ob.CodOrganismo AND cbd.CodTipoPago = '02')
				LEFT JOIN mastproveedores pv ON (p1.CodPersona = pv.CodProveedor)
				LEFT JOIN pv_presupuesto ppto ON (ppto.CodOrganismo = ob.CodOrganismo AND ppto.CodPresupuesto = ob.CodPresupuesto)
			WHERE ob.CodObligacionBono = '$CodObligacionBono[0]'";
	$field_bono = getRecord($sql);
	$field_obligacion = $field_bono;
	##	
	$field_obligacion['IngresadoPor'] = $_SESSION["CODPERSONA_ACTUAL"];
	$field_obligacion['NomIngresadoPor'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
	$disabled_impuesto = "disabled";
	$disabled_documento = "disabled";
	$disabled_distribucion = "disabled";
	$disabled_facturas = "disabled";
	$disabled_adelantos = "disabled";
	$disabled_anular = "disabled";
	$dFlagCompromiso = "disabled";
	$disabled_modificar = "disabled";
	$display_modificar = "display:none;";
	$disabled_anular = "disabled";
	$disabled_impuesto = "disabled";
	$mostrarTabDistribucion = "mostrarTabDistribucionObligacion();";
	$dFechaPreparacion = "";
	##	
	$accion = "nuevo";
	$titulo = "Nueva Obligaci&oacute;n (Bono de Alimentaci&oacute;n)";
	$label_submit = "Guardar";
	##	
	$action = "../nomina/gehen.php?anz=$origen";
}
elseif ($opcion == "certificaciones-generar") {
	##	
	$sql = "SELECT
				c.CodCertificacion,
				c.CodPersona AS CodProveedor,
				tc.CodTipoDocumento,
				c.CodOrganismo,
				cbd.NroCuenta,
				'02' AS CodTipoPago,
				c.CodPersona AS CodResponsable,
				'$FechaActual' AS FechaRegistro,
				'$FechaActual' AS FechaVencimiento,
				'S' AS FlagGenerarPago,
				'NING' AS CodTipoServicio,
				'BA' AS ReferenciaTipoDocumento,
				c.CodInterno AS ReferenciaNroDocumento,
				c.Monto As MontoObligacion,
				'0.00' AS MontoImpuestoOtros,
				c.Monto AS MontoNoAfecto,
				'0.00' AS MontoAfecto,
				'0.00' AS MontoAdelanto,
				'0.00' AS MontoImpuesto,
				'0.00' AS MontoPagoParcial,
				'S' AS FlagContabilizacionPendiente,
				'S' AS FlagContPendientePub20,
				c.Justificacion AS Comentarios,
				c.Justificacion AS ComentariosAdicional,
				'$FechaActual' AS FechaRecepcion,
				c.CodPersona AS CodProveedorPagar,
				'$FechaActual' AS FechaDocumento,
				'N' AS FlagAfectoIGV,
				'N' AS FlagDiferido,
				'N' AS FlagAdelanto,
				'N' AS FlagPagoDiferido,
				'PR' AS Estado,
				'S' AS FlagCompromiso,
				'S' AS FlagPresupuesto,
				'S' AS FlagObligacionAuto,
				'N' AS FlagObligacionDirecta,
				'N' AS FlagCajaChica,
				'N' AS FlagPagoIndividual,
				CONCAT(c.CodTipoCertif, '-', c.Anio, '-', c.CodInterno) AS NroControl,
				CONCAT(c.CodTipoCertif, '-', c.Anio, '-', c.CodInterno) AS NroFactura,
				'$FechaActual' AS FechaProgramada,
				'$FechaActual' AS FechaPreparacion,
				'$PeriodoActual' AS Periodo,
				'N' AS FlagDistribucionManual,
				'$FechaActual' AS FechaFactura,
				'N' AS FlagVerificado,
				c.CodPresupuesto,
				'N' AS FlagNomina,
				'N' AS FlagFacturaPendiente,
				c.CodFuente,
				ppto.Ejercicio,
				ppto.CategoriaProg,
				p.DocFiscal,
				p.NomCompleto,
				p.Busqueda,
				pv.DiasPago,
				p.NomCompleto AS NomProveedorPagar,
				td.FlagProvision,
				td.CodVoucher
			FROM
				ap_certificaciones c
				INNER JOIN mastpersonas p ON (c.CodPersona = p.CodPersona)
				INNER JOIN ap_tiposcertificacion tc ON (tc.CodTipoCertif = c.CodTipoCertif)
				INNER JOIN ap_tipodocumento td ON (tc.CodTipoDocumento = td.CodTipoDocumento)
				LEFT JOIN mastproveedores pv ON (c.CodPersona = pv.CodProveedor)
				LEFT JOIN ap_ctabancariadefault cbd ON (cbd.CodOrganismo = c.CodOrganismo AND cbd.CodTipoPago = '02')
				LEFT JOIN pv_presupuesto ppto ON (ppto.CodOrganismo = c.CodOrganismo AND ppto.CodPresupuesto = c.CodPresupuesto)
			WHERE c.CodCertificacion = '$sel_registros'";
	$field_bono = getRecord($sql);
	$field_obligacion = $field_bono;
	##	
	$field_obligacion['CodCentroCosto'] = getVar3("SELECT CodCentroCosto FROM ac_mastcentrocosto WHERE Codigo = '".$_PARAMETRO["CCOSTOPR"]."'");
	$field_obligacion['IngresadoPor'] = $_SESSION["CODPERSONA_ACTUAL"];
	$field_obligacion['NomIngresadoPor'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
	$disabled_impuesto = "disabled";
	$disabled_documento = "disabled";
	$disabled_distribucion = "disabled";
	$disabled_facturas = "disabled";
	$disabled_adelantos = "disabled";
	$disabled_anular = "disabled";
	$dFlagCompromiso = "disabled";
	$disabled_modificar = "disabled";
	$display_modificar = "display:none;";
	$disabled_anular = "disabled";
	$disabled_impuesto = "disabled";
	$mostrarTabDistribucion = "mostrarTabDistribucionObligacion();";
	$dFechaPreparacion = "";
	##	
	$accion = "nuevo";
	$titulo = "Nueva Obligaci&oacute;n (Gastos Directos)";
	$label_submit = "Guardar";
	##	

	##	
	$action = "gehen.php?anz=$origen";
}
elseif ($opcion == "generar-anticipo") {
	##	
	$sql = "SELECT
				o.CodObra,
				o.CodProveedor,
				'AC' AS CodTipoDocumento,
				O.CodOrganismo,
				cbd.NroCuenta,
				'02' AS CodTipoPago,
				o.CodProveedor AS CodResponsable,
				'$FechaActual' AS FechaRegistro,
				'$FechaActual' AS FechaVencimiento,
				'S' AS FlagGenerarPago,
				'NING' AS CodTipoServicio,
				'BO' AS ReferenciaTipoDocumento,
				o.CodObra AS ReferenciaNroDocumento,
				'0.00' AS MontoImpuestoOtros,
				'0.00' AS MontoAfecto,
				'0.00' AS MontoAdelanto,
				'0.00' AS MontoImpuesto,
				'0.00' AS MontoPagoParcial,
				'S' AS FlagContabilizacionPendiente,
				'S' AS FlagContPendientePub20,
				o.Nombre AS Comentarios,
				o.Nombre AS ComentariosAdicional,
				'$FechaActual' AS FechaRecepcion,
				o.CodProveedor AS CodProveedorPagar,
				'$FechaActual' AS FechaDocumento,
				'N' AS FlagAfectoIGV,
				'N' AS FlagDiferido,
				'N' AS FlagAdelanto,
				'N' AS FlagPagoDiferido,
				'PR' AS Estado,
				'N' AS FlagCompromiso,
				'N' AS FlagPresupuesto,
				'S' AS FlagObligacionAuto,
				'N' AS FlagObligacionDirecta,
				'N' AS FlagCajaChica,
				'N' AS FlagPagoIndividual,
				CONCAT(o.TipoDocumento, '-', o.AnioObra, '-', o.CodInterno) AS NroControl,
				CONCAT(o.TipoDocumento, '-', o.AnioObra, '-', o.CodInterno) AS NroFactura,
				'$FechaActual' AS FechaProgramada,
				'$FechaActual' AS FechaPreparacion,
				'$PeriodoActual' AS Periodo,
				'S' AS FlagDistribucionManual,
				'$FechaActual' AS FechaFactura,
				'N' AS FlagVerificado,
				o.CodPresupuesto,
				'N' AS FlagNomina,
				'N' AS FlagFacturaPendiente,
				o.CodFuente,
				ppto.Ejercicio,
				ppto.CategoriaProg,
				p.DocFiscal,
				p.NomCompleto,
				p.Busqueda,
				pv.DiasPago,
				p.NomCompleto AS NomProveedorPagar,
				td.FlagProvision,
				td.CodVoucher
			FROM
				ob_obras o
				INNER JOIN mastpersonas p ON (p.CodPersona = o.CodProveedor)
				INNER JOIN ap_tipodocumento td ON (td.CodTipoDocumento = 'AC')
				LEFT JOIN ap_ctabancariadefault cbd ON (cbd.CodOrganismo = o.CodOrganismo AND cbd.CodTipoPago = '02')
				LEFT JOIN pv_presupuesto ppto ON (ppto.CodOrganismo = o.CodOrganismo AND ppto.CodPresupuesto = o.CodPresupuesto)
				LEFT JOIN mastproveedores pv ON (pv.CodProveedor = o.CodProveedor)
			WHERE o.CodObra = '$sel_registros'";
	$field_obra = getRecord($sql);
	$field_obligacion = $field_obra;
	##	
    $sql = "SELECT of.MontoAnticipo AS Monto
			FROM
				ob_obrasfinanciero of
				INNER JOIN ob_obras o ON (o.CodObra = of.CodObra)
                LEFT JOIN ac_mastplancuenta20 pc20 ON (pc20.CodCuenta = '111280101')
				LEFT JOIN pv_presupuesto pv On (pv.CodOrganismo = o.CodOrganismo AND pv.CodPresupuesto = o.CodPresupuesto)
			WHERE of.CodObra = '$sel_registros'
			ORDER BY Secuencia";
	$field_obligacion['MontoObligacion'] = getVar3($sql);
	$field_obligacion['MontoNoAfecto'] = $field_obligacion['MontoObligacion'];
	##	
	$field_obligacion['CodCentroCosto'] = getVar3("SELECT CodCentroCosto FROM ac_mastcentrocosto WHERE Codigo = '".$_PARAMETRO["CCOSTOPR"]."'");
	$field_obligacion['IngresadoPor'] = $_SESSION["CODPERSONA_ACTUAL"];
	$field_obligacion['NomIngresadoPor'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
	$disabled_impuesto = "disabled";
	$disabled_documento = "disabled";
	$disabled_distribucion = "disabled";
	$disabled_facturas = "disabled";
	$disabled_adelantos = "disabled";
	$disabled_anular = "disabled";
	$dFlagCompromiso = "disabled";
	$disabled_modificar = "disabled";
	$display_modificar = "display:none;";
	$disabled_anular = "disabled";
	$disabled_impuesto = "disabled";
	$mostrarTabDistribucion = "mostrarTabDistribucionObligacion();";
	$dFechaPreparacion = "";
	##	
	$accion = "nuevo";
	$titulo = "Nueva Obligaci&oacute;n (Generar Anticipo)";
	$label_submit = "Guardar";
	##	
	$action = "../ob/gehen.php?anz=$return";
}
elseif ($opcion == "generar-valuacion") {
	##	
	$sql = "SELECT
				v.CodValuacion,
				o.CodObra,
				o.CodProveedor,
				'OV' AS CodTipoDocumento,
				O.CodOrganismo,
				cbd.NroCuenta,
				'02' AS CodTipoPago,
				o.CodProveedor AS CodResponsable,
				'$FechaActual' AS FechaRegistro,
				'$FechaActual' AS FechaVencimiento,
				'S' AS FlagGenerarPago,
				'NING' AS CodTipoServicio,
				'VA' AS ReferenciaTipoDocumento,
				o.CodObra AS ReferenciaNroDocumento,
				'0.00' AS MontoImpuestoOtros,
				'0.00' AS MontoAfecto,
				'0.00' AS MontoAdelanto,
				'0.00' AS MontoImpuesto,
				'0.00' AS MontoPagoParcial,
				'S' AS FlagContabilizacionPendiente,
				'S' AS FlagContPendientePub20,
				o.Nombre AS Comentarios,
				o.Nombre AS ComentariosAdicional,
				'$FechaActual' AS FechaRecepcion,
				o.CodProveedor AS CodProveedorPagar,
				'$FechaActual' AS FechaDocumento,
				'S' AS FlagAfectoIGV,
				'S' AS FlagAgruparIgv,
				'N' AS FlagDiferido,
				'N' AS FlagAdelanto,
				'N' AS FlagPagoDiferido,
				'PR' AS Estado,
				'N' AS FlagCompromiso,
				'S' AS FlagPresupuesto,
				'S' AS FlagObligacionAuto,
				'N' AS FlagObligacionDirecta,
				'N' AS FlagCajaChica,
				'N' AS FlagPagoIndividual,
				CONCAT(o.TipoDocumento, '-', o.AnioObra, '-', o.CodInterno) AS NroControl,
				CONCAT(o.TipoDocumento, '-', o.AnioObra, '-', o.CodInterno) AS NroFactura,
				'$FechaActual' AS FechaProgramada,
				'$FechaActual' AS FechaPreparacion,
				'$PeriodoActual' AS Periodo,
				'S' AS FlagDistribucionManual,
				'$FechaActual' AS FechaFactura,
				'N' AS FlagVerificado,
				o.CodPresupuesto,
				'N' AS FlagNomina,
				'N' AS FlagFacturaPendiente,
				o.CodFuente,
				ppto.Ejercicio,
				ppto.CategoriaProg,
				p.DocFiscal,
				p.NomCompleto,
				p.Busqueda,
				pv.DiasPago,
				p.NomCompleto AS NomProveedorPagar,
				td.FlagProvision,
				td.CodVoucher
			FROM
				ob_valuaciones v
				INNER JOIN ob_obras o ON (o.CodObra = v.CodObra)
				INNER JOIN mastpersonas p ON (p.CodPersona = o.CodProveedor)
				INNER JOIN ap_tipodocumento td ON (td.CodTipoDocumento = 'AC')
				LEFT JOIN ap_ctabancariadefault cbd ON (cbd.CodOrganismo = o.CodOrganismo AND cbd.CodTipoPago = '02')
				LEFT JOIN pv_presupuesto ppto ON (ppto.CodOrganismo = o.CodOrganismo AND ppto.CodPresupuesto = o.CodPresupuesto)
				LEFT JOIN mastproveedores pv ON (pv.CodProveedor = o.CodProveedor)
			WHERE v.CodValuacion = '$sel_registros'";
	$field_valuacion = getRecord($sql);
	$field_obligacion = $field_valuacion;
	##	
    $sql = "SELECT *
			FROM ob_valuacionesfinanciero vf
			WHERE vf.CodValuacion = '$sel_registros'
			ORDER BY Secuencia";
	$field_financiero = getRecord($sql);
	$field_obligacion['MontoAfecto'] = $field_financiero['ValObraEjecutada'];
	$field_obligacion['MontoNoAfecto'] = 0.00;
	$field_obligacion['MontoImpuesto'] = $field_financiero['ValIva'];
	$field_obligacion['MontoImpuestoOtros'] = getVar3("SELECT SUM(Monto) FROM ob_valuacionesretenciones WHERE CodValuacion = '$sel_registros' GROUP BY CodValuacion");
	$field_obligacion['MontoObligacion'] = $field_obligacion['MontoAfecto'] + $field_obligacion['MontoNoAfecto'] + $field_obligacion['MontoImpuesto'] - $field_obligacion['MontoImpuestoOtros'];
	##	
	$field_obligacion['CodCentroCosto'] = getVar3("SELECT CodCentroCosto FROM ac_mastcentrocosto WHERE Codigo = '".$_PARAMETRO["CCOSTOPR"]."'");
	$field_obligacion['IngresadoPor'] = $_SESSION["CODPERSONA_ACTUAL"];
	$field_obligacion['NomIngresadoPor'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
	$disabled_impuesto = "disabled";
	$disabled_documento = "disabled";
	$disabled_distribucion = "disabled";
	$disabled_facturas = "disabled";
	$disabled_adelantos = "disabled";
	$disabled_anular = "disabled";
	$dFlagCompromiso = "disabled";
	$disabled_modificar = "disabled";
	$display_modificar = "display:none;";
	$disabled_anular = "disabled";
	$disabled_impuesto = "disabled";
	$mostrarTabDistribucion = "mostrarTabDistribucionObligacion();";
	$dFechaPreparacion = "";
	##	
	$accion = "nuevo";
	$titulo = "Nueva Obligaci&oacute;n (Valuaci&oacute;n)";
	$label_submit = "Guardar";
	##	
	$action = "../ob/gehen.php?anz=$return";
}
elseif ($opcion == "viaticos-generar") {
	list($CodOrganismo, $CodViatico) = explode("_", $sel_registros);
	##	
	$sql = "SELECT
				v.CodViatico,
				v.CodPersona AS CodProveedor,
				'$_PARAMETRO[DOCVIAT]' AS CodTipoDocumento,
				v.CodOrganismo,
				cbd.NroCuenta,
				'02' AS CodTipoPago,
				v.CodPersona AS CodResponsable,
				'$FechaActual' AS FechaRegistro,
				'$FechaActual' AS FechaVencimiento,
				'S' AS FlagGenerarPago,
				'NING' AS CodTipoServicio,
				'BA' AS ReferenciaTipoDocumento,
				v.CodInterno AS ReferenciaNroDocumento,
				v.Monto As MontoObligacion,
				'0.00' AS MontoImpuestoOtros,
				v.Monto AS MontoNoAfecto,
				'0.00' AS MontoAfecto,
				'0.00' AS MontoAdelanto,
				'0.00' AS MontoImpuesto,
				'0.00' AS MontoPagoParcial,
				'S' AS FlagContabilizacionPendiente,
				'S' AS FlagContPendientePub20,
				v.Motivo AS Comentarios,
				v.Motivo AS ComentariosAdicional,
				'$FechaActual' AS FechaRecepcion,
				v.CodPersona AS CodProveedorPagar,
				'$FechaActual' AS FechaDocumento,
				'N' AS FlagAfectoIGV,
				'N' AS FlagDiferido,
				'N' AS FlagAdelanto,
				'N' AS FlagPagoDiferido,
				'PR' AS Estado,
				'S' AS FlagCompromiso,
				'S' AS FlagPresupuesto,
				'S' AS FlagObligacionAuto,
				'N' AS FlagObligacionDirecta,
				'N' AS FlagCajaChica,
				'N' AS FlagPagoIndividual,
				CONCAT('".$_PARAMETRO['DOCVIAT']."', v.Anio, '-', v.CodInterno) AS NroControl,
				CONCAT('".$_PARAMETRO['DOCVIAT']."', v.Anio, '-', v.CodInterno) AS NroFactura,
				'$FechaActual' AS FechaProgramada,
				'$FechaActual' AS FechaPreparacion,
				'$PeriodoActual' AS Periodo,
				'N' AS FlagDistribucionManual,
				'$FechaActual' AS FechaFactura,
				'N' AS FlagVerificado,
				v.CodPresupuesto,
				'N' AS FlagNomina,
				'N' AS FlagFacturaPendiente,
				v.CodFuente,
				ppto.Ejercicio,
				ppto.CategoriaProg,
				p.DocFiscal,
				p.NomCompleto,
				p.Busqueda,
				pv.DiasPago,
				p.NomCompleto AS NomProveedorPagar,
				td.FlagProvision,
				td.CodVoucher,
				ue.CodCentroCosto
			FROM
				ap_viaticos v
				INNER JOIN mastpersonas p ON (v.CodPersona = p.CodPersona)
				INNER JOIN ap_tipodocumento td ON (td.CodTipoDocumento = '$_PARAMETRO[DOCVIAT]')
				LEFT JOIN mastproveedores pv ON (v.CodPersona = pv.CodProveedor)
				LEFT JOIN ap_ctabancariadefault cbd ON (cbd.CodOrganismo = v.CodOrganismo AND cbd.CodTipoPago = '02')
				LEFT JOIN pv_presupuesto ppto ON (ppto.CodOrganismo = v.CodOrganismo AND ppto.CodPresupuesto = v.CodPresupuesto)
				LEFT JOIN pv_categoriaprog cp ON (cp.CategoriaProg = ppto.CategoriaProg)
				LEFT JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cp.CodUnidadEjec)
			WHERE
				v.CodOrganismo = '$CodOrganismo'
				AND v.CodViatico = '$CodViatico'";
	$field_bono = getRecord($sql);
	$field_obligacion = $field_bono;
	##	
	$field_obligacion['IngresadoPor'] = $_SESSION["CODPERSONA_ACTUAL"];
	$field_obligacion['NomIngresadoPor'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
	$disabled_impuesto = "disabled";
	$disabled_documento = "disabled";
	$disabled_distribucion = "disabled";
	$disabled_facturas = "disabled";
	$disabled_adelantos = "disabled";
	$disabled_anular = "disabled";
	$dFlagCompromiso = "disabled";
	$disabled_modificar = "disabled";
	$display_modificar = "display:none;";
	$disabled_anular = "disabled";
	$disabled_impuesto = "disabled";
	$mostrarTabDistribucion = "mostrarTabDistribucionObligacion();";
	$dFechaPreparacion = "";
	##	
	$accion = "nuevo";
	$titulo = "Nueva Obligaci&oacute;n (Vi&aacute;ticos)";
	$label_submit = "Guardar";
	##	

	##	
	$action = "gehen.php?anz=$origen";
}
elseif ($opcion == "adelanto-generar") {
	$CodAdelanto = $sel_registros;
	##	
	$sql = "SELECT
				ga.CodAdelanto,
				ga.CodProveedor,
				(CASE WHEN ga.CodClasificacion = 'AP' THEN 'APR' ELSE 'ACO' END) AS CodTipoDocumento,
				ga.CodOrganismo,
				cbd.NroCuenta,
				ga.CodTipoPago,
				'$FechaActual' AS FechaRegistro,
				'$FechaActual' AS FechaVencimiento,
				'S' AS FlagGenerarPago,
				ga.CodTipoServicio,
				ga.CodClasificacion AS ReferenciaTipoDocumento,
				CONCAT(ga.CodClasificacion, ga.NroAdelanto, SUBSTRING(ga.Periodo, 1, 4)) AS ReferenciaNroDocumento,
				(ga.MontoAfecto + ga.MontoNoAfecto) As MontoObligacion,
				'0.00' AS MontoImpuestoOtros,
				ga.MontoNoAfecto,
				ga.MontoAfecto,
				'0.00' AS MontoAdelanto,
				'0.00' AS MontoImpuesto,
				'0.00' AS MontoPagoParcial,
				'S' AS FlagContabilizacionPendiente,
				'S' AS FlagContPendientePub20,
				ga.Descripcion AS Comentarios,
				ga.Descripcion AS ComentariosAdicional,
				'$FechaActual' AS FechaRecepcion,
				'$FechaActual' AS FechaDocumento,
				'N' AS FlagAfectoIGV,
				'N' AS FlagDiferido,
				'S' AS FlagAdelanto,
				'N' AS FlagPagoDiferido,
				'PR' AS Estado,
				'N' AS FlagCompromiso,
				'N' AS FlagPresupuesto,
				'S' AS FlagObligacionAuto,
				'S' AS FlagObligacionDirecta,
				'S' AS FlagCajaChica,
				'N' AS FlagPagoIndividual,
				CONCAT(ga.CodClasificacion, ga.NroAdelanto, SUBSTRING(ga.Periodo, 1, 4)) AS NroControl,
				CONCAT(ga.CodClasificacion, ga.NroAdelanto, SUBSTRING(ga.Periodo, 1, 4)) AS NroFactura,
				'$FechaActual' AS FechaProgramada,
				'$FechaActual' AS FechaPreparacion,
				'$PeriodoActual' AS Periodo,
				'N' AS FlagDistribucionManual,
				'$FechaActual' AS FechaFactura,
				'N' AS FlagVerificado,
				'' AS CodPresupuesto,
				'N' AS FlagNomina,
				'N' AS FlagFacturaPendiente,
				'' AS CodFuente,
				SUBSTRING(ga.Periodo, 1, 4) AS Ejercicio,
				'' AS CategoriaProg,
				p.DocFiscal,
				p.NomCompleto,
				p.Busqueda,
				pv.DiasPago,
				ga.CodPagarA AS CodProveedorPagar,
				ga.NomPagarA AS NomProveedorPagar,
				td.FlagProvision,
				td.CodVoucher,
				ga.CodCentroCosto
			FROM
				ap_gastoadelanto ga
				INNER JOIN mastpersonas p ON (ga.CodProveedor = p.CodPersona)
				INNER JOIN ap_tipodocumento td ON (td.CodTipoDocumento = (CASE WHEN ga.CodClasificacion = 'AP' THEN 'APR' ELSE 'ACO' END))
				LEFT JOIN mastproveedores pv ON (ga.CodProveedor = pv.CodProveedor)
				LEFT JOIN ap_ctabancariadefault cbd ON (cbd.CodOrganismo = ga.CodOrganismo AND cbd.CodTipoPago = ga.CodTipoPago)
			WHERE ga.CodAdelanto = '$CodAdelanto'";
	$field_adelanto = getRecord($sql);
	$field_obligacion = $field_adelanto;
	##	
	$field_obligacion['IngresadoPor'] = $_SESSION["CODPERSONA_ACTUAL"];
	$field_obligacion['NomIngresadoPor'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
	$disabled_impuesto = "disabled";
	$disabled_documento = "disabled";
	$disabled_distribucion = "disabled";
	$disabled_facturas = "disabled";
	$disabled_adelantos = "disabled";
	$disabled_anular = "disabled";
	$dFlagCompromiso = "disabled";
	$disabled_modificar = "disabled";
	$display_modificar = "display:none;";
	$disabled_anular = "disabled";
	$disabled_impuesto = "disabled";
	$mostrarTabDistribucion = "mostrarTabDistribucionObligacion();";
	$dFechaPreparacion = "";
	##	
	$accion = "nuevo";
	$titulo = "Nueva Obligación (Adelanto)";
	$label_submit = "Guardar";
	##	

	##	
	$action = "gehen.php?anz=$origen";
}
//	------------------------------------
$_width = 1100;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="document.getElementById('frmentrada').submit()">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<table width="1100" align="center" cellpadding="0" cellspacing="0">
    <tr>
        <td>
            <div class="header">
            <ul id="tab">
            <!-- CSS Tabs -->
            <li id="li1" onclick="currentTab('tab', this);" class="current"><a href="#" onclick="mostrarTab('tab', 1, 6);">Informaci&oacute;n General</a></li>
            <li id="li2" onclick="currentTab('tab', this);"><a href="#" onclick="mostrarTab('tab', 2, 6);">Informaci&oacute;n Monetaria</a></li>
            <li id="li3" onclick="currentTab('tab', this);"><a href="#" onclick="mostrarTab('tab', 3, 6);">Dist. Contable y Presup.</a></li>
            <li id="li4" onclick="currentTab('tab', this);"><a href="#" onclick="<?=$mostrarTabDistribucion?>">Resumen Contable y Presup.</a></li>
            <li id="li5" onclick="currentTab('tab', this);"><a href="#" onclick="mostrarTab('tab', 5, 6);">Registro de Facturas</a></li>
            <li id="li6" onclick="currentTab('tab', this);"><a href="#" onclick="mostrarTab('tab', 6, 6);">Adelantos y Pagos Parciales</a></li>
            </ul>
            </div>
        </td>
    </tr>
</table>

<form name="frmentrada" id="frmentrada" action="<?=$action?>" method="POST" onsubmit="return obligacion(this, '<?=$accion?>');">
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
<input type="hidden" name="fCodProveedor" id="fCodProveedor" value="<?=$fCodProveedor?>" />
<input type="hidden" name="fNomProveedor" id="fNomProveedor" value="<?=$fNomProveedor?>" />
<input type="hidden" name="fCodTipoDocumento" id="fCodTipoDocumento" value="<?=$fCodTipoDocumento?>" />
<input type="hidden" name="fCodIngresadoPor" id="fCodIngresadoPor" value="<?=$fCodIngresadoPor?>" />
<input type="hidden" name="fNomIngresadoPor" id="fNomIngresadoPor" value="<?=$fNomIngresadoPor?>" />
<input type="hidden" name="fNroDocumento" id="fNroDocumento" value="<?=$fNroDocumento?>" />
<input type="hidden" name="fCodCentroCosto" id="fCodCentroCosto" value="<?=$fCodCentroCosto?>" />
<input type="hidden" name="fNomCentroCosto" id="fNomCentroCosto" value="<?=$fNomCentroCosto?>" />
<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
<input type="hidden" name="fFechaDocumentod" id="fFechaDocumentod" value="<?=$fFechaDocumentod?>" />
<input type="hidden" name="fFechaDocumentoh" id="fFechaDocumentoh" value="<?=$fFechaDocumentoh?>" />
<input type="hidden" name="fReferenciaNroDocumento" id="fReferenciaNroDocumento" value="<?=$fReferenciaNroDocumento?>" />
<input type="hidden" name="fFechaRegistrod" id="fFechaRegistrod" value="<?=$fFechaRegistrod?>" />
<input type="hidden" name="fFechaRegistroh" id="fFechaRegistroh" value="<?=$fFechaRegistroh?>" />
<input type="hidden" name="FlagPagoDiferido" id="FlagPagoDiferido" value="<?=$FlagPagoDiferido?>" />
<input type="hidden" name="FactorImpuesto" id="FactorImpuesto" value="<?=$FactorImpuesto?>" />
<input type="hidden" name="Periodo" id="Periodo" value="<?=$field_obligacion['Periodo']?>" />
<input type="hidden" name="Anio" id="Anio" value="<?=$Anio?>" />
<input type="hidden" name="PeriodoActual" id="PeriodoActual" value="<?=substr($Ahora, 0, 7)?>" />
<input type="hidden" name="FlagProvision" id="FlagProvision" value="<?=$field_obligacion['FlagProvision']?>" />
<input type="hidden" name="CodVoucher" id="CodVoucher" value="<?=$field_obligacion['CodVoucher']?>" />
<input type="hidden" id="VoucherPub20" value="<?=$field_obligacion['VoucherPub20']?>" />
<input type="hidden" id="VoucherPeriodoPub20" value="<?=$field_obligacion['VoucherPeriodoPub20']?>" />
<input type="hidden" id="FlagContPendientePub20" value="<?=$field_obligacion['FlagContPendientePub20']?>" />
<input type="hidden" id="CONTPUB20" value="<?=$_PARAMETRO['CONTPUB20']?>" />
<input type="hidden" id="CONTONCO" value="<?=$_PARAMETRO['CONTONCO']?>" />
<input type="hidden" id="FlagNomina" value="<?=$field_obligacion['FlagNomina']?>" />
<input type="hidden" name="NroDocumento" id="NroDocumento" value="<?=$field_obligacion['NroDocumento']?>" />
<input type="hidden" name="opcion" id="opcion" value="<?=$opcion?>" />
<input type="hidden" name="fCodTipoNom" id="fCodTipoNom" value="<?=$fCodTipoNom?>" />
<input type="hidden" name="fAnio" id="fAnio" value="<?=$fAnio?>" />
<input type="hidden" name="fMes" id="fMes" value="<?=$fMes?>" />
<input type="hidden" name="fCodBonoAlim" id="fCodBonoAlim" value="<?=$fCodBonoAlim?>" />
<input type="hidden" name="CodObligacionBono" id="CodObligacionBono" value="<?=$CodObligacionBono[0]?>" />
<input type="hidden" name="CodCertificacion" id="CodCertificacion" value="<?=$field_obligacion['CodCertificacion']?>" />
<input type="hidden" name="CodObra" id="CodObra" value="<?=$field_obligacion['CodObra']?>" />
<input type="hidden" name="CodValuacion" id="CodValuacion" value="<?=$field_obligacion['CodValuacion']?>" />
<input type="hidden" name="CodViatico" id="CodViatico" value="<?=$CodViatico?>" />
<input type="hidden" name="CodAdelanto" id="CodAdelanto" value="<?=$CodAdelanto?>" />

<div id="tab1" style="display:block;">
	<table width="1100" class="tblForm">
		<tr>
	    	<td colspan="4" class="divFormCaption">Informaci&oacute;n del Proveedor</td>
	    </tr>
	    <tr>
			<td class="tagForm" width="125">* Proveedor:</td>
			<td class="gallery clearfix">
	        	<input type="text" id="CodProveedor" value="<?=$field_obligacion['CodProveedor']?>" disabled="disabled" style="width:100px;" />
				<input type="text" id="NomCompleto" value='<?=($field_obligacion['NomCompleto'])?>' disabled="disabled" style="width:250px;" />
				<a href="../lib/listas/listado_personas.php?filtrar=default&cod=fCodProveedor&nom=fNomProveedor&EsEmpleado=S&EsProveedor=S&EsOtros=S&ventana=selListadoObligacionPersona&iframe=true&width=100%&height=400" rel="prettyPhoto[iframe1]" style=" <?=$display_modificar?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
	        </td>
			<td class="tagForm" width="125">Dias Pago:</td>
			<td><input type="text" id="DiasPago" style="width:50px;" value="<?=$field_obligacion['DiasPago']?>" <?=$disabled_ver?> /></td>
		</tr>
	    <tr>
			<td class="tagForm">R.I.F:</td>
			<td>
	        	<input type="text" id="DocFiscal" style="width:100px;" value="<?=$field_obligacion['DocFiscal']?>" disabled="disabled" />
	            <input type="text" id="Busqueda" style="width:250px;" value='<?=($field_obligacion['Busqueda'])?>' disabled="disabled" />
	        </td>
			<td class="tagForm">* Pagar A:</td>
			<td class="gallery clearfix">
	        	<input type="text" id="CodProveedorPagar" value="<?=$field_obligacion['CodProveedorPagar']?>" maxlength="6" style="width:50px;" onchange="getDescripcionLista('accion=getDescripcionPersona&flagproveedor=S&flagempleado=S&flagotros=S', this, 'nompagara');" disabled="disabled" />
				<input type="text" id="NomProveedorPagar" value='<?=($field_obligacion['NomProveedorPagar'])?>' style="width:250px;" disabled="disabled" />
				<a href="../lib/listas/listado_personas.php?filtrar=default&cod=CodProveedorPagar&nom=NomProveedorPagar&EsEmpleado=S&EsProveedor=S&EsOtros=S&iframe=true&width=825&height=400" rel="prettyPhoto[iframe2]" style=" <?=$display_ver?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
	        </td>
		</tr>
		<tr>
			<td class="tagForm">* Organismo:</td>
			<td>
	        	<select id="CodOrganismo" style="width:300px;" <?=$disabled_modificar?> onchange="ctabancariadefault($(this).val(), $('#CodTipoPago').val(), $('#NroCuenta'));">
	            	<?=loadSelect("mastorganismos", "CodOrganismo", "Organismo", $field_obligacion['CodOrganismo'], 0)?>
	            </select>
			</td>
			<td class="tagForm">* Centro Costo:</td>
			<td class="gallery clearfix">
	        	<input type="text" id="CodCentroCosto" value="<?=$field_obligacion['CodCentroCosto']?>" style="width:50px;" disabled="disabled" />
				<input type="hidden" id="NomCentroCosto" value="<?=($field_obligacion['NomCentroCosto'])?>" />
				<a href="../lib/listas/listado_centro_costos.php?filtrar=default&cod=CodCentroCosto&nom=NomCentroCosto&iframe=true&width=100%&height=425" rel="prettyPhoto[iframe3]" style=" <?=$display_ver?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
	        </td>
		</tr>
	    <tr>
			<td class="tagForm">* Tipo de Documento:</td>
			<td>
	        	<select id="CodTipoDocumento" style="width:300px;" onchange="getOptionsSelect(this.value, 'tipo_servicio_documento', 'CodTipoServicio', true); afectaTipoServicioObligacion($('#CodTipoServicio').val());" <?=$disabled_modificar?>>
	                <?php
					if ($opcion == "nuevo") {
						loadSelectTipoDocumentoObligacion($field_obligacion['CodTipoDocumento'], 0);
					} else {
						loadSelect("ap_tipodocumento", "CodTipoDocumento", "Descripcion", $field_obligacion['CodTipoDocumento'], 1);
					}
					?>
	            </select>
	        </td>
			<td class="tagForm">Nro. Registro:</td>
			<td><input type="text" id="NroRegistro" value="<?=$field_obligacion['NroRegistro']?>" style="width:100px;" class="codigo" disabled="disabled" /></td>
		</tr>
	    <tr>
			<td class="tagForm">* Nro. Control:</td>
			<td><input type="text" id="NroControl" maxlength="20" style="width:150px;" value="<?=$field_obligacion['NroControl']?>" <?=$disabled_ver?> /></td>
			<td class="tagForm">* Nro. Factura:</td>
			<td><input type="text" id="NroFactura" maxlength="20" value="<?=$field_obligacion['NroFactura']?>" style="width:150px;" <?=$disabled_ver?> /></td>
		</tr>
	    <tr>
			<td height="22" class="tagForm">Estado:</td>
			<td>
	       	  <input type="hidden" id="Estado" value="<?=$field_obligacion['Estado']?>" />
	        	<input type="text" style="width:100px;" class="codigo" value="<?=printValores("ESTADO-OBLIGACIONES", $field_obligacion['Estado'])?>" disabled="disabled" />
			</td>
	        <td class="tagForm">Fecha Factura:</td>
			<td>
				<input type="text" id="FechaFactura" value="<?=formatFechaDMA($field_obligacion['FechaFactura'])?>" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" <?=$disabled_ver?> />
				<input type="checkbox" id="FlagFacturaPendiente" <?=chkFlag($field_obligacion['FlagFacturaPendiente'])?> <?=$disabled_ver?> /> Factura Pendiente
			</td>
		</tr>
	</table>
	<table width="1100" class="tblForm">
	    <tr>
			<td width="50%" valign="top">
	        	<table width="100%">
	            	<tr><td colspan="2" class="divFormCaption">Fechas del Documento</td></tr>
	            	<tr>
	                	<td class="tagForm" width="125"><strong>Obligaci&oacute;n:</strong></td>
	                    <td><input type="text" id="FechaRegistro" value="<?=formatFechaDMA($field_obligacion['FechaRegistro'])?>" style="width:100px;" class="datepicker codigo" onkeyup="setFechaDMA(this);" onchange="setPresupuesto($('#CodOrganismo').val(), $(this).val(), $('#CodPresupuesto'), $('#Anio')); actualizarMontosObligacion();" <?=$disabled_ver?> /></td>
	                </tr>
	            	<tr>
	                	<td class="tagForm">Emisi&oacute;n:</td>
	                    <td><input type="text" id="FechaDocumento" value="<?=formatFechaDMA($field_obligacion['FechaDocumento'])?>" style="width:100px;" class="datepicker" onkeyup="setFechaDMA(this);" <?=$disabled_ver?> /></td>
	                </tr>
	            	<tr>
	                	<td class="tagForm">Recepci&oacute;n:</td>
	                    <td><input type="text" id="FechaRecepcion" value="<?=formatFechaDMA($field_obligacion['FechaRecepcion'])?>" style="width:100px;" class="datepicker" onkeyup="setFechaDMA(this);" <?=$disabled_ver?> /></td>
	                </tr>
	            	<tr>
	                	<td class="tagForm">Vencimiento:</td>
	                    <td><input type="text" id="FechaVencimiento" value="<?=formatFechaDMA($field_obligacion['FechaVencimiento'])?>" style="width:100px;" class="datepicker" onkeyup="setFechaDMA(this);" <?=$disabled_ver?> /></td>
	                </tr>
	            	<tr>
	                	<td class="tagForm">Prog. Pago:</td>
	                    <td><input type="text" id="FechaProgramada" value="<?=formatFechaDMA($field_obligacion['FechaProgramada'])?>" style="width:100px;" class="datepicker" onkeyup="setFechaDMA(this);" <?=$disabled_ver?> /></td>
	                </tr>
			        <tr>
			            <td colspan="2" class="divFormCaption">Presupuesto</td>
			        </tr>
			        <tr>
			            <td class="tagForm" width="150">Presupuesto:</td>
			            <td class="gallery clearfix">
			                <input type="text" name="Ejercicio" id="Ejercicio" value="<?=$field_obligacion['Ejercicio']?>" style="width:48px;" class="Ejercicio" readonly />
			                <input type="text" name="CodPresupuesto" id="CodPresupuesto" value="<?=$field_obligacion['CodPresupuesto']?>" style="width:48px;" class="CodPresupuesto" readonly />
			                <a href="../lib/listas/gehen.php?anz=lista_pv_presupuesto&filtrar=default&FlagCategoriaProg=S&fCodOrganismo=<?=$field_obligacion['CodOrganismo']?>&fEjercicio=<?=$field_obligacion['Ejercicio']?>&fCodDependencia=<?=$field_obligacion['CodDependencia']?>&campo1=Ejercicio&campo2=CodPresupuesto&campo3=CategoriaProg&ventana=lg_requerimiento&iframe=true&width=100%&height=425" rel="prettyPhoto[iframe13]" style=" <?=$display_ver?>" id="btPresupuesto">
			                    <img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
			                </a>
			            </td>
			        </tr>
			        <tr>
			            <td class="tagForm">Cat. Prog.:</td>
			            <td><input type="text" name="CategoriaProg" id="CategoriaProg" value="<?=$field_obligacion['CategoriaProg']?>" style="width:100px;" class="CategoriaProg" readonly /></td>
			        </tr>
			        <tr>
			            <td class="tagForm">Fuente de Financiamiento:</td>
			            <td>
			                <select name="CodFuente" id="CodFuente" style="width:250px;" onchange="$('.CodFuente').val(this.value);" <?=$disabled_ver?>>
			                    <?=loadSelect2("pv_fuentefinanciamiento","CodFuente","Denominacion",$field_obligacion['CodFuente'],10)?>
			                </select>
			            </td>
			        </tr>
	            </table>
	        </td>
	        
			<td width="50%" valign="top">
	        	<table width="100%">
	            	<tr><td colspan="2" class="divFormCaption">Informaci&oacute;n Adicional</td></tr>
	            	<tr>
	                	<td class="tagForm" width="125">* Tipo de Servicio:</td>
	                    <td>
	                        <select id="CodTipoServicio" style="width:150px;" onchange="afectaTipoServicioObligacion(this.value);" <?=$disabled_ver?>>
	                            <?=loadSelect("masttiposervicio", "CodTipoServicio", "Descripcion", $field_obligacion['CodTipoServicio'], 0)?>
	                        </select>
	                    </td>
	                </tr>
	            	<tr>
	                	<td class="tagForm">* Tipo de Pago:</td>
	                    <td>
	                        <select id="CodTipoPago" style="width:150px;" <?=$disabled_ver?> onchange="ctabancariadefault($('#CodOrganismo').val(), $(this).val(), $('#NroCuenta'));">
	                            <?=loadSelect("masttipopago", "CodTipoPago", "TipoPago", $field_obligacion['CodTipoPago'], 0)?>
	                        </select>
	                    </td>
	                </tr>
	                <tr>
	                    <td class="tagForm">Ingresado Por:</td>
	                    <td>
	                        <input type="hidden" id="IngresadoPor" value="<?=$field_obligacion['IngresadoPor']?>" />
	                        <input type="text" id="NomIngresadoPor" value="<?=htmlentities($field_obligacion['NomIngresadoPor'])?>" style="width:245px;" disabled="disabled" />
	                        <input type="text" id="FechaPreparacion" value="<?=formatFechaDMA($field_obligacion['FechaPreparacion'])?>" style="width:60px;" class="datepicker" <?=$dFechaPreparacion?>  />
	                    </td>
	                </tr>
	                <tr>
	                    <td class="tagForm">Revisado Por:</td>
	                    <td>
	                        <input type="hidden" id="RevisadoPor" value="<?=$field_obligacion['RevisadoPor']?>" />
	                        <input type="text" id="NomRevisadoPor" value="<?=htmlentities($field_obligacion['NomRevisadoPor'])?>" style="width:245px;" disabled="disabled" />
	                        <input type="text" id="FechaRevision" value="<?=formatFechaDMA($field_obligacion['FechaRevision'])?>" style="width:60px;" class="datepicker" <?=$dFechaRevision?> />
	                    </td>
	                </tr>
	                <tr>
	                    <td class="tagForm">Aprobador CxP:</td>
	                    <td>
	                        <input type="hidden" id="AprobadoPor" value="<?=$field_obligacion['AprobadoPor']?>" />
	                        <input type="text" id="NomAprobadoPor" value="<?=htmlentities($field_obligacion['NomAprobadoPor'])?>" style="width:245px;" disabled="disabled" />
	                        <input type="text" id="FechaAprobado" value="<?=formatFechaDMA($field_obligacion['FechaAprobado'])?>" style="width:60px;" class="datepicker" <?=$dFechaAprobado?> />
	                    </td>
	                </tr>
	            </table>
	        </td>
		</tr>
	</table>
	<table width="1100" class="tblForm">
		<tr>
			<td class="tagForm" width="125">Glosa del Voucher:</td>
			<td><input type="text" id="Comentarios" value="<?=($field_obligacion['Comentarios'])?>" style="width:95%;" <?=$disabled_ver?> /></td>
		</tr>
		<tr>
			<td class="tagForm">Comentarios Adicional:</td>
			<td><textarea id="ComentariosAdicional" style="width:95%; height:45px;" <?=$disabled_ver?>><?=($field_obligacion['ComentariosAdicional'])?></textarea></td>
		</tr>
		<tr>
			<td class="tagForm">Raz&oacute;n Anulaci&oacute;n:</td>
			<td><input type="text" id="MotivoAnulacion"value="<?=($field_obligacion['MotivoAnulacion'])?>" style="width:95%;" <?=$disabled_anular?> /></td>
		</tr>
		<tr>
			<td class="tagForm">&Uacute;ltima Modif.:</td>
			<td>
				<input type="text" size="30" value="<?=$field_obligacion['UltimoUsuario']?>" disabled="disabled" />
				<input type="text" size="25" value="<?=$field_obligacion['UltimaFecha']?>" disabled="disabled" />
			</td>
		</tr>
	</table>
	<center>
	<input type="submit" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" />
	<input type="button" value="Cancelar" style="width:75px;" onclick="this.form.submit();" />
	</center>
	<div style="width:1100px" class="divMsj">Campos Obligatorios *</div>
</div>

<div id="tab2" style="display:none;">
	<table width="1100" class="tblForm">
		<tr>
	    	<td colspan="4" class="divFormCaption">Informaci&oacute;n Monetaria</td>
	    </tr>
	    <tr>
			<td class="tagForm" width="150">Ref. Doc. Interno:</td>
			<td><input type="text" id="ReferenciaDocumento" style="width:195px;" value="<?=$field_obligacion['ReferenciaTipoDocumento']?>-<?=$field_obligacion['ReferenciaNroDocumento']?>" disabled="disabled" /></td>
			<td class="tagForm" width="150">Monto Afecto:</td>
			<td>
	        	<input type="text" id="MontoAfecto" value="<?=number_format($field_obligacion['MontoAfecto'], 2, ',', '.')?>" style="width:150px; text-align:right;" disabled="disabled" />
	        </td>
		</tr>
	    <tr>
			<td class="tagForm">* Cuenta Bancaria:</td>
			<td>
	        	<select id="NroCuenta" style="width:200px;" <?=$disabled_ver?>>
	                <?=loadSelect2("ap_ctabancaria", "NroCuenta", "NroCuenta", $field_obligacion['NroCuenta'], 0, [], [], 'Descripcion')?>
	            </select>
	        </td>
			<td class="tagForm">Monto No Afecto:</td>
			<td>
	        	<input type="text" id="MontoNoAfecto" value="<?=number_format($field_obligacion['MontoNoAfecto'], 2, ',', '.')?>" style="width:150px; text-align:right;" disabled="disabled" />
	        </td>
		</tr>
	    <tr>
			<td class="tagForm">&nbsp;</td>
			<td>
	        	<input type="checkbox" id="FlagCajaChica" <?=chkFlag($field_obligacion['FlagCajaChica'])?> <?=$disabled_ver?> /> Pago con Caja Chica (Efectivo)
	        </td>
			<td class="tagForm">Impuesto:</td>
			<td>
	        	<input type="text" id="MontoImpuesto" value="<?=number_format($field_obligacion['MontoImpuesto'], 2, ',', '.')?>" style="width:150px; text-align:right;" onfocus="numeroFocus(this);" onblur="numeroBlur(this);" onchange="cambiar_monto_impuesto();" />
	        </td>
		</tr>
	    <tr>
			<td class="tagForm">&nbsp;</td>
			<td>
	        	<input type="checkbox" id="FlagPagoIndividual" <?=chkFlag($field_obligacion['FlagPagoIndividual'])?> <?=$disabled_ver?> /> Preparar Pago Individual
	        </td>
			<td class="tagForm">Otros Impuestos/Retenciones:</td>
			<td>
	        	<input type="text" id="MontoImpuestoOtros" value="<?=number_format($field_obligacion['MontoImpuestoOtros'], 2, ',', '.')?>" style="width:150px; text-align:right;" disabled="disabled" />
	        </td>
		</tr>
	    <tr>
			<td class="tagForm">&nbsp;</td>
			<td>
	        	<input type="checkbox" id="FlagGenerarPago" <?=chkFlag($field_obligacion['FlagGenerarPago'])?> <?=$disabled_ver?> /> Preparar Pago (Autom&aacute;tico)
	        </td>
			<td class="tagForm"><strong>Total Obligaci&oacute;n:</strong></td>
			<td>
	        	<input type="text" id="MontoObligacion" value="<?=number_format($field_obligacion['MontoObligacion'], 2, ',', '.')?>" style="width:150px; text-align:right; font-size:12px; font-weight:bold;" class="codigo" disabled="disabled" />
	        </td>
		</tr>
	    <tr>
			<td class="tagForm">&nbsp;</td>
			<td>
	        	<input type="checkbox" id="FlagPagoDiferido" <?=chkFlag($field_obligacion['FlagPagoDiferido'])?> <?=$disabled_ver?> /> Diferir el Pago
	        </td>
			<td class="tagForm">Adelanto:</td>
			<td>
	        	<input type="text" id="MontoAdelanto" value="<?=number_format($field_obligacion['MontoAdelanto'], 2, ',', '.')?>" style="width:150px; text-align:right;" disabled="disabled" />
	        </td>
		</tr>
	    <tr>
			<td class="tagForm">&nbsp;</td>
			<td>
	        	<input type="checkbox" id="FlagDiferido" <?=chkFlag($field_obligacion['FlagDiferido'])?> <?=$disabled_ver?> /> Considerarlo como Diferido
	        </td>
			<td class="tagForm"><strong>Total a Pagar:</strong></td>
			<td>
	        	<?php
				$MontoPagar = $field_obligacion['MontoObligacion'] - $field_obligacion['MontoAdelanto'];
				?>
	        	<input type="text" id="MontoPagar" value="<?=number_format($MontoPagar, 2, ',', '.')?>" style="width:150px; text-align:right; font-size:12px; font-weight:bold;" class="codigo" disabled="disabled" />
	        </td>
		</tr>
	    <tr>
			<td class="tagForm">&nbsp;</td>
			<td>
	        	<input type="checkbox" id="FlagAfectoIGV" <?=chkFlag($field_obligacion['FlagAfectoIGV'])?> <?=$disabled_ver?> /> Afecto a Defracción de IGV
	        </td>
			<td class="tagForm">Pagos Parciales:</td>
			<td><input type="text" id="MontoPagoParcial" value="<?=number_format($field_obligacion['MontoPagoParcial'], 2, ',', '.')?>" style="width:150px; text-align:right;" disabled="disabled" /></td>
		</tr>
	    <tr>
			<td class="tagForm">&nbsp;</td>
			<td>
	        	<input type="checkbox" id="FlagCompromiso" <?=chkFlag($field_obligacion['FlagCompromiso'])?> <?=$dFlagCompromiso?> onchange="FlagCompromisoObligacion(this.checked);" /> Refiere Compromiso
	        </td>
			<td class="tagForm"><strong>Saldo Pendiente:</strong></td>
			<td>
	        	<?php
				$MontoPendiente = $MontoPagar - $field_obligacion['MontoPagoParcial'];
				?>
	        	<input type="text" id="MontoPendiente" value="<?=number_format($MontoPendiente, 2, ',', '.')?>" style="width:150px; text-align:right; font-size:12px; font-weight:bold;" onfocus="numeroFocus(this);" onblur="numeroBlur(this);" disabled="disabled" />
	        </td>
		</tr>
	    <tr>
			<td class="tagForm">&nbsp;</td>
			<td>
	        	<input type="checkbox" id="FlagPresupuesto" <?=chkFlag($field_obligacion['FlagPresupuesto'])?> <?=$dFlagPresupuesto?> onchange="FlagPresupuestoObligacion(this.checked);" /> Afecta Presupuesto
	        </td>
			<td class="tagForm">&nbsp;</td>
			<td>
	        	<input type="checkbox" name="FlagAgruparIgv" id="FlagAgruparIgv" <?=chkFlag($field_obligacion['FlagAgruparIgv'])?> <?=$dFlagAgruparIgv?> /> Agrupar Impuesto
	        </td>
		</tr>
	    <tr>
			<td class="tagForm">&nbsp;</td>
			<td>
	        	<input type="checkbox" id="FlagDistribucionManual" <?=chkFlag($field_obligacion['FlagDistribucionManual'])?> <?=$dFlagDistribucionManual?> onchange="setObligacionPagoDirecto(this.checked);" /> Pago Directo
	        </td>
			<td class="tagForm">&nbsp;</td>
			<td class="tagForm">&nbsp;</td>
		</tr>
	</table>
</div>
</form>

<div id="tab3" style="display:none;">
	<table width="1100" align="center">
		<tr>
	    	<td valign="top">
	        	<form name="frm_impuesto" id="frm_impuesto">
	            <input type="hidden" id="sel_impuesto" />
	            <div style="width:500px" class="divFormCaption">Retenciones/Impuestos</div>
	            <table width="500" class="tblBotones">
	                <tr>
	                    <td align="right" class="gallery clearfix">
	                        <a id="aInsertarImpuesto" href="../lib/listas/listado_impuestos.php?filtrar=default&ventana=obligacion_impuestos_insertar&FlagObligacion=S&CodRegimenFiscal=R&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe4]" style="display:none;"></a>
	                        <input type="button" class="btLista" value="Insertar" id="btInsertarImpuesto" onclick="document.getElementById('aInsertarImpuesto').click();" <?=$disabled_impuesto?> />
	                        <input type="button" class="btLista" value="Borrar" id="btQuitarImpuesto" onclick="quitarLineaImpuesto(this, 'impuesto');" <?=$disabled_impuesto?> />
	                    </td>
	                </tr>
	            </table>
				<?php
				if ($field_obligacion['FlagNomina'] == "S") {
					?>
	                <table><tr><td><div style="overflow:scroll; width:500px; height:150px;">
	                <table width="100%" class="tblLista">
	                    <thead>
	                    <tr>
	                        <th width="15">&nbsp;</th>
	                        <th align="left">Retencion</th>
	                        <th width="100" align="right">Monto</th>
	                    </tr>
	                    </thead>
	                    
	                    <tbody id="lista_impuesto">
	                    <?php
						$sql = "SELECT
									oi.*,
									c.Descripcion
	                            FROM
									ap_obligacionesimpuesto oi
									INNER JOIN pr_concepto c ON (oi.CodConcepto = c.CodConcepto)
	                            WHERE
	                                oi.CodProveedor = '".$field_obligacion['CodProveedor']."' AND
	                                oi.CodTipoDocumento = '".$field_obligacion['CodTipoDocumento']."' AND
	                                oi.NroDocumento = '".$field_obligacion['NroDocumento']."'";
	                    $query_impuestos = mysql_query($sql) or die ($sql.mysql_error());
	                    while ($field_impuestos = mysql_fetch_array($query_impuestos)) {	$nro_impuesto++;
	                        ?>
	                        <tr class="trListaBody" onclick="mClk(this, 'sel_impuesto');" id="impuesto_<?=$field_impuestos['CodImpuesto']?>">
	                            <th><?=$nro_impuesto?></th>
	                            <td>
	                                <input type="text" value="<?=$field_impuestos['Descripcion']?>" class="cell2" readonly="readonly" />
	                                <input type="hidden" name="CodImpuesto" />
	                                <input type="hidden" name="CodConcepto" value="<?=$field_impuestos['CodConcepto']?>" />
	                                <input type="hidden" name="Signo" value="N" />
	                                <input type="hidden" name="FlagImponible" value="N" />
	                                <input type="hidden" name="FlagProvision" value="<?=$field_impuestos['FlagProvision']?>" />
	                                <input type="hidden" name="CodCuenta" value="<?=$field_impuestos['CodCuenta']?>" />
	                                <input type="hidden" name="CodCuentaPub20" value="<?=$field_impuestos['CodCuentaPub20']?>" />
	                                <input type="hidden" name="MontoAfecto" value="<?=$field_impuestos['MontoAfecto']?>" />
	                                <input type="hidden" name="FactorPorcentaje" value="<?=$field_impuestos['FactorPorcentaje']?>" />
	                            </td>
	                            <td>
									<input type="hidden" name="MontoSustraendo" value="<?=$field_impuestos['MontoSustraendo']?>" />
									<input type="hidden" name="MontoAfectoSustraendo" value="<?=$field_impuestos['MontoAfectoSustraendo']?>" />
	                                <input type="text" name="MontoImpuesto" value="<?=number_format($field_impuestos['MontoImpuesto'], 2, ',', '.')?>" style="text-align:right;" class="cell2" readonly="readonly" />
	                                <input type="hidden" name="CodRegimenFiscal" value="<?=$field_impuestos['CodRegimenFiscal']?>" />
	                            </td>
	                        </tr>
	                        <?php
	                        $impuesto_total += $field_impuestos['MontoImpuesto'];
	                    }
	                    ?>
	                    </tbody>
	                    
	                    <tfoot>
	                    <tr>
	                        <th colspan="2">&nbsp;</th>
	                        <th>
	                            <input type="text" id="impuesto_total" value="<?=number_format($impuesto_total, 2, ',', '.')?>" style="text-align:right; font-weight:bold;" class="cell2" readonly="readonly" />
	                        </th>
	                    </tr>
	                    </tfoot>
	                </table>
	                </div></td></tr></table>
	                <?php
				} else {
					?>
	                <table><tr><td><div style="overflow:scroll; width:500px; height:150px;">
	                <table width="100%" class="tblLista">
	                    <thead>
		                    <tr>
		                        <th width="15">&nbsp;</th>
		                        <th align="left">Impuesto</th>
		                        <th width="100" align="right">Monto Afecto</th>
		                        <th width="50" align="right">Factor</th>
		                        <th width="100" align="right">Monto</th>
		                    </tr>
	                    </thead>
	                    
	                    <tbody id="lista_impuesto">
	                    <?php
	                    if ($opcion == "generar-valuacion") {
		                    $sql = "SELECT
		                    			vr.CodImpuesto,
		                                vr.BaseImponible AS MontoAfecto,
		                                i.FactorPorcentaje,
		                                vr.Monto AS MontoImpuesto,
		                                i.Descripcion,
		                                i.FlagImponible,
		                                i.FlagProvision,
		                                i.Signo,
		                                i.CodCuenta,
		                                i.CodCuentaPub20,
		                                i.CodRegimenFiscal
		                            FROM
		                                ob_valuacionesretenciones vr
		                                INNER JOIN mastimpuestos i ON (i.CodImpuesto = vr.CodImpuesto)
		                            WHERE
		                                vr.CodValuacion = '$sel_registros'";
	                    } else {
		                    $sql = "SELECT
		                                oi.*,
		                                i.Descripcion,
		                                i.FlagImponible,
		                                i.FlagProvision,
		                                i.Signo,
		                                i.CodCuenta,
		                                i.CodCuentaPub20,
		                                i.CodRegimenFiscal
		                            FROM
		                                ap_obligacionesimpuesto oi
		                                INNER JOIN mastimpuestos i ON (oi.CodImpuesto = i.CodImpuesto)
		                            WHERE
		                                oi.CodProveedor = '".$CodProveedor."' AND
		                                oi.CodTipoDocumento = '".$CodTipoDocumento."' AND
		                                oi.NroDocumento = '".$NroDocumento."'";
	                    }
	                    $query_impuestos = mysql_query($sql) or die ($sql.mysql_error());
	                    while ($field_impuestos = mysql_fetch_array($query_impuestos)) {	$nro_impuesto++;
	                        ?>
	                        <tr class="trListaBody" onclick="mClk(this, 'sel_impuesto');" id="impuesto_<?=$field_impuestos['CodImpuesto']?>">
	                            <th><?=$nro_impuesto?></th>
	                            <td>
	                                <input type="text" value="<?=$field_impuestos['Descripcion']?>" class="cell2" readonly="readonly" />
	                                <input type="hidden" name="CodImpuesto" value="<?=$field_impuestos['CodImpuesto']?>" />
	                                <input type="hidden" name="CodConcepto" />
	                                <input type="hidden" name="Signo" value="<?=$field_impuestos['Signo']?>" />
	                                <input type="hidden" name="FlagImponible" value="<?=$field_impuestos['FlagImponible']?>" />
	                                <input type="hidden" name="FlagProvision" value="<?=$field_impuestos['FlagProvision']?>" />
	                                <input type="hidden" name="CodCuenta" value="<?=$field_impuestos['CodCuenta']?>" />
	                                <input type="hidden" name="CodCuentaPub20" value="<?=$field_impuestos['CodCuentaPub20']?>" />
	                            </td>
	                            <td>
	                                <input type="text" name="MontoAfecto" value="<?=number_format($field_impuestos['MontoAfecto'], 2, ',', '.')?>" style="text-align:right;" class="cell2" readonly="readonly" />
	                            </td>
	                            <td>
	                                <input type="text" name="FactorPorcentaje" value="<?=number_format($field_impuestos['FactorPorcentaje'], 2, ',', '.')?>" style="text-align:right;" class="cell2" readonly="readonly" />
	                            </td>
	                            <td>
									<input type="hidden" name="MontoSustraendo" value="<?=number_format($field_impuestos['MontoSustraendo'], 2, ',', '.')?>" />
									<input type="hidden" name="MontoAfectoSustraendo" value="<?=number_format($field_impuestos['MontoAfectoSustraendo'], 2, ',', '.')?>" />
	                                <input type="text" name="MontoImpuesto" value="<?=number_format($field_impuestos['MontoImpuesto'], 2, ',', '.')?>" style="text-align:right;" class="cell2" readonly="readonly" />
	                                <input type="hidden" name="CodRegimenFiscal" value="<?=$field_impuestos['CodRegimenFiscal']?>" />
	                            </td>
	                        </tr>
	                        <?php
	                        $impuesto_total += $field_impuestos['MontoImpuesto'];
	                    }
	                    ?>
	                    </tbody>
	                    
	                    <tfoot>
	                    <tr>
	                        <th colspan="4">&nbsp;</th>
	                        <th>
	                            <input type="text" id="impuesto_total" value="<?=number_format($impuesto_total, 2, ',', '.')?>" style="text-align:right; font-weight:bold;" class="cell2" readonly="readonly" />
	                        </th>
	                    </tr>
	                    </tfoot>
	                </table>
	                </div></td></tr></table>
	                <?php
				}
				?>
	            <input type="hidden" id="nro_impuesto" value="<?=$nro_impuesto?>" />
	            <input type="hidden" id="can_impuesto" value="<?=$nro_impuesto?>" />
	            </form>
	        </td>
	        
	        <td valign="top">
	        	<form name="frm_documento" id="frm_documento">
	            <input type="hidden" id="sel_documento" />
	            <div style="width:100%" class="divFormCaption">Documentos Relacionados</div>
	            <table width="100%" class="tblBotones">
	                <tr>
	                    <td align="right">
	                        <input type="button" class="btLista" value="Insertar" id="btInsertarDocumento" onclick="window.open('../lib/listas/listado_documentos_obligaciones.php?CodProveedor='+$('#CodProveedor').val()+'&CodOrganismo='+$('#CodOrganismo').val(), 'listado_documentos_obligaciones', 'toolbar=no, menubar=no, location=no, scrollbars=yes, height=525, width=1050, left=50, top=50, resizable=yes');" <?=$disabled_documento?> />
	                        <input type="button" class="btLista" value="Borrar" id="btQuitarDocumento" onclick="quitarLineaObligacionDocumento(this, 'documento');" <?=$disabled_documento?> />
	                    </td>
	                </tr>
	            </table>
	            
	            <table><tr><td><div style="overflow:scroll; width:590px; height:150px;">
	            <table width="1500" class="tblLista">
	            	<thead>
	                <tr>
	                    <th width="15">&nbsp;</th>
	                    <th width="75">Clasificacion</th>
	                    <th width="125">Doc. Referencia</th>
	                    <th width="100">Fecha</th>
	                    <th width="125">O.C / O.S</th>
	                    <th width="100" align="right">Monto Total</th>
	                    <th width="100" align="right">Monto Afecto</th>
	                    <th width="100" align="right">Impuesto</th>
	                    <th width="100" align="right">Monto No Afecto</th>
	                    <th align="left">Comentarios</th>
	                </tr>
	                </thead>
	                
	                <tbody id="lista_documento">
	                <?php
					$sql = "SELECT *
							FROM ap_documentos
							WHERE
								CodProveedor = '".$field_obligacion['CodProveedor']."' AND
								ObligacionTipoDocumento = '".$field_obligacion['CodTipoDocumento']."' AND
								ObligacionNroDocumento = '".$field_obligacion['NroDocumento']."'";
					$query_documentos = mysql_query($sql) or die ($sql.mysql_error());	$nro_documento = 0;
					while ($field_documentos = mysql_fetch_array($query_documentos)) {	$nro_documento++;
						$iddoc = $field_documentos['ReferenciaTipoDocumento']."|".$field_documentos['ReferenciaNroDocumento']."|".$field_documentos['DocumentoClasificacion']."|".$field_documentos['DocumentoReferencia'];
						if ($field_documentos['ReferenciaTipoDocumento'] == "OC") $clasificacion = "O.Compra"; else $clasificacion = "O.Servicio";
						?>
	                    <tr class="trListaBody" id="documento_<?=$iddoc?>">
	                    	<th><?=$nro_documento?></th>
	                        <td>
	                        	<input type="text" value="<?=$clasificacion?>" class="cell2" readonly="readonly" />
	                            <input type="hidden" name="Porcentaje" />
	                        	<input type="hidden" name="DocumentoClasificacion" value="<?=$field_documentos['DocumentoClasificacion']?>" />
	                        </td>
	                        <td>
	                        	<input type="text" name="DocumentoReferencia" value="<?=$field_documentos['DocumentoReferencia']?>" style="text-align:center;" class="cell2" readonly="readonly" />
	                        </td>
	                        <td>
	                        	<input type="text" name="Fecha" value="<?=formatFechaDMA($field_documentos['Fecha'])?>" style="text-align:center;" class="cell2" readonly="readonly" />
	                        </td>
	                        <td>
	                            <input type="text" name="ReferenciaTipoDocumento" value="<?=$field_documentos['ReferenciaTipoDocumento']?>" style="width:15%;" class="cell2" readonly="readonly" />
	                            <input type="text" name="ReferenciaNroDocumento" value="<?=$field_documentos['ReferenciaNroDocumento']?>" style="width:70%;" class="cell2" readonly="readonly" />
	                        </td>
	                        <td>
	                        	<input type="text" name="MontoTotal" value="<?=number_format($field_documentos['MontoTotal'], 2, ',', '.')?>" style="text-align:right;" class="cell2" readonly="readonly" />
	                        </td>
	                        <td>
	                        	<input type="text" name="MontoAfecto" value="<?=number_format($field_documentos['MontoAfecto'], 2, ',', '.')?>" style="text-align:right;" class="cell2" readonly="readonly" />
	                        </td>
	                        <td>
	                        	<input type="text" name="MontoImpuestos" value="<?=number_format($field_documentos['MontoImpuestos'], 2, ',', '.')?>" style="text-align:right;" class="cell2" readonly="readonly" />
	                        </td>
	                        <td>
	                        	<input type="text" name="MontoNoAfecto" value="<?=number_format($field_documentos['MontoNoAfecto'], 2, ',', '.')?>" style="text-align:right;" class="cell2" readonly="readonly" />
	                        </td>
	                        <td>
	                        	<input type="text" name="Comentarios" value="<?=$field_documentos['Comentarios']?>" class="cell2" readonly="readonly" />
	                        </td>
						</tr>
	                    <?php
						$documento_afecto += $field_documentos['MontoAfecto'];
						$documento_noafecto += $field_documentos['MontoNoAfecto'];
						$documento_impuesto += $field_documentos['MontoImpuestos'];
					}
					//$documento_impuesto = $documento_afecto * $FactorImpuesto / 100;
					$documento_total = $documento_afecto + $documento_noafecto + $documento_impuesto;
					?>
	                </tbody>
	                
	                <tfoot id="foot_documento">
	                <tr>
	                    <th colspan="5">&nbsp;</th>
	                    <th>
	                       	<input type="text" id="documento_total" value="<?=number_format($documento_total, 2, ',', '.')?>" style="text-align:right; font-weight:bold;" class="cell2" readonly="readonly" />
	                    </th>
	                    <th>
	                       	<input type="text" id="documento_afecto" value="<?=number_format($documento_afecto, 2, ',', '.')?>" style="text-align:right; font-weight:bold;" class="cell2" readonly="readonly" />
	                    </th>
	                    <th>
	                       	<input type="text" id="documento_impuesto" value="<?=number_format($documento_impuesto, 2, ',', '.')?>" style="text-align:right; font-weight:bold;" class="cell2" readonly="readonly" />
	                    </th>
	                    <th>
	                       	<input type="text" id="documento_noafecto" value="<?=number_format($documento_noafecto, 2, ',', '.')?>" style="text-align:right; font-weight:bold;" class="cell2" readonly="readonly" />
	                    </th>
	                </tr>
	                </tfoot>
	            </table>
	            </div></td></tr></table>
	            <input type="hidden" id="nro_documento" value="<?=$nro_documento?>" />
	            <input type="hidden" id="can_documento" value="<?=$nro_documento?>" />
	            </form>
	        </td>
	    </tr>
		<tr>
	    	<td valign="top" colspan="2">
	        	<form name="frm_distribucion" id="frm_distribucion">
	            <input type="hidden" id="sel_distribucion" />
	            <div style="width:1100px" class="divFormCaption">Distribuci&oacute;n</div>
	            <table width="1100" class="tblBotones">
	                <tr>
	                    <td class="gallery clearfix">
	                    	<?php
	                    	if ($opcion == "interfase-bono-nuevo") $disabled_distribucion = 'disabled';
	                    	elseif ($field_obligacion['FlagNomina'] == "S") $disabled_distribucion = '';
	                    	?>
	                        <a id="aSelPartida" href="../lib/listas/listado_clasificador_presupuestario_disponible.php?ventana=iframe=true&width=100%&height=400" rel="prettyPhoto[iframe5]" style="display:none;"></a>
	                        <a id="aSelCuenta" href="../lib/listas/listado_plan_cuentas.php?filtrar=default&cod=CodCuenta&nom=NomCuenta&ventana=selListadoLista&seldetalle=sel_distribucion&iframe=true&width=915&height=400" rel="prettyPhoto[iframe6]" style="display:none;"></a>
	                        <a id="aSelCuenta20" href="../lib/listas/listado_plan_cuentas_pub20.php?filtrar=default&cod=CodCuentaPub20&nom=NomCuentaPub20&ventana=selListadoLista&seldetalle=sel_distribucion&iframe=true&width=915&height=400" rel="prettyPhoto[iframe7]" style="display:none;"></a>
	                        <a id="aSelCCosto" href="../lib/listas/listado_centro_costos.php?filtrar=default&cod=CodCentroCosto&nom=CodCentroCosto&ventana=selListadoLista&seldetalle=sel_distribucion&iframe=true&width=825&height=390" rel="prettyPhoto[iframe8]" style="display:none;"></a>
	                        <a id="aSelPersona" href="../lib/listas/listado_personas.php?filtrar=default&cod=CodPersona&nom=NomPersona&ventana=selListadoLista&seldetalle=sel_distribucion&iframe=true&width=825&height=400" rel="prettyPhoto[iframe9]" style="display:none;"></a>
	                        <a id="aSelActivo" href="../lib/listas/listado_activos.php?filtrar=default&campo1=NroActivo&ventana=selListadoListaParent&seldetalle=sel_distribucion&iframe=true&width=1050&height=400" rel="prettyPhoto[iframe10]" style="display:none;"></a>
	                        <a id="aSelCategoriaProg" href="../lib/listas/gehen.php?anz=lista_pv_presupuesto&filtrar=default&FlagCategoriaProg=S&campo1=detallesCategoriaProg&campo2=detallesEjercicio&campo3=detallesCodPresupuesto&ventana=selListadoListaParentRequerimiento&seldetalle=sel_detalles&iframe=true&width=100%&height=400" rel="prettyPhoto[iframe14]" style="display:none;"></a>
	                        <input type="button" class="btLista" id="btSelPartida" value="Sel. Partida" onclick="abrirListadoPartidasDisponiblesObligacion();" <?=$disabled_distribucion?> />
	                        <input type="button" class="btLista" id="btSelCuenta" value="Sel. Cuenta" onclick="validarAbrirLista('sel_distribucion', 'aSelCuenta');" <?=$disabled_distribucion?> />
	                        <input type="button" class="btLista" id="btSelCuenta20" value="Cta. Pub. 20" onclick="validarAbrirLista('sel_distribucion', 'aSelCuenta20');" <?=$disabled_distribucion?> />
	                        <input type="button" class="btLista" id="btSelCCosto" value="Sel. C.Costo" onclick="validarAbrirLista('sel_distribucion', 'aSelCCosto');" <?=$disabled_distribucion?> />
	                        <input type="button" class="btLista" id="btSelPersona" value="Sel. Persona" onclick="validarAbrirLista('sel_distribucion', 'aSelPersona');" <?=$disabled_distribucion?> />
	                        <input type="button" class="btLista" id="btSelActivo" value="Sel. Activo" onclick="validarAbrirLista('sel_distribucion', 'aSelActivo');" <?=$disabled_distribucion?> />
	                        <input type="button" style="width:90px;" id="btSelCategoriaProg" value="Sel. Presupuesto" onclick="validarAbrirLista('sel_detalles', 'aSelCategoriaProg');" <?=$disabled_distribucion?> />
	                    </td>
	                    <td align="right">
	                        <input type="button" class="btLista" id="btInsertarDistribucion" value="Insertar" onclick="insertarLinea3(this, 'obligacion_distribucion_insertar', 'distribucion', 'ap_obligacion_ajax.php', 'CodTipoServicio='+$('#CodTipoServicio').val()+'&CodCentroCosto='+$('#CodCentroCosto').val()+'&FlagPresupuesto='+$('#FlagPresupuesto').attr('checked')+'&CodPresupuesto='+$('#CodPresupuesto').val()+'&Ejercicio='+$('#Ejercicio').val()+'&CategoriaProg='+$('#CategoriaProg').val()+'&CodFuente='+$('#CodFuente').val());" <?=$disabled_distribucion?> />
	                        <input type="button" class="btLista" id="btQuitarDistribucion" value="Quitar" onclick="quitarLineaDistribucion(this, 'distribucion');" <?=$disabled_distribucion?> />
	                    </td>
	                </tr>
	            </table>
	            
	            <table><tr><td><div style="overflow:scroll; width:1100px; height:250px;">
	            <table width="2200" class="tblLista" id="tabla_distribucion">
	            	<thead>
		                <tr>
		                    <th width="15">#</th>
		                    <th align="left" colspan="2">Partida</th>
		                    <th align="left" colspan="2">Cta. Contable</th>
		                    <th align="left" colspan="2">Cta. Contable (Pub. 20)</th>
		                    <th width="40">C.C.</th>
		                    <th width="35">No Afe.</th>
		                    <th width="100">Monto</th>
					        <th width="90">Cat. Prog.</th>
					        <th width="32">F.F.</th>
		                    <th colspan="2">Nro. Documento</th>
		                    <th width="125">Referencia</th>
		                    <th align="left">Descripci&oacute;n</th>
		                    <th width="75">Persona</th>
		                    <th width="75">Activo</th>
		                    <th width="35">Dif.</th>
		                </tr>
	                </thead>
	                
	                <tbody id="lista_distribucion">
					<?php
					if ($opcion == "interfase-bono-nuevo") {
		                $sql = "SELECT 
		                            obc.*,
		                            'S' AS FlagNoAfectoIGV,
									CONCAT('BA-', obc.CodObligacionBono) AS Referencia,
		                            p.denominacion AS NomPartida,
		                            pc.Descripcion AS NomCuenta,
		                            pc20.Descripcion AS NomCuentaPub20,
		                            cc.Codigo AS NomCentroCosto, 
		                            pv.CategoriaProg,
		                            pv.Ejercicio
								FROM
									pr_obligacionesbonocuenta obc
		                            LEFT JOIN pv_partida p ON (obc.cod_partida = p.cod_partida)
		                            LEFT JOIN ac_mastplancuenta pc ON (obc.CodCuenta = pc.CodCuenta)
		                            LEFT JOIN ac_mastplancuenta20 pc20 ON (obc.CodCuentaPub20 = pc20.CodCuenta)
		                            LEFT JOIN ac_mastcentrocosto cc ON (obc.CodCentroCosto = cc.CodCentroCosto)
									LEFT JOIN pv_presupuesto pv On (pv.CodOrganismo = obc.CodOrganismo AND pv.CodPresupuesto = obc.CodPresupuesto)
								WHERE obc.CodObligacionBono = '$CodObligacionBono[0]'";
					}
					elseif ($opcion == "certificaciones-generar") {
		                $sql = "SELECT 
		                            cd.*,
		                            'S' AS FlagNoAfectoIGV,
									'$field_obligacion[NroControl]' AS Referencia,
		                            p.denominacion AS NomPartida,
		                            pc.Descripcion AS NomCuenta,
		                            pc20.Descripcion AS NomCuentaPub20,
		                            pv.CategoriaProg,
		                            pv.Ejercicio,
		                            '$field_obligacion[CodCentroCosto]' AS CodCentroCosto
								FROM
									ap_certificacionesdet cd
		                            LEFT JOIN pv_partida p ON (cd.cod_partida = p.cod_partida)
		                            LEFT JOIN ac_mastplancuenta pc ON (cd.CodCuenta = pc.CodCuenta)
		                            LEFT JOIN ac_mastplancuenta20 pc20 ON (cd.CodCuentaPub20 = pc20.CodCuenta)
									LEFT JOIN pv_presupuesto pv On (pv.CodOrganismo = cd.CodOrganismo AND pv.CodPresupuesto = cd.CodPresupuesto)
								WHERE cd.CodCertificacion = '$sel_registros'
								ORDER BY CodOrganismo, CodPresupuesto, CodFuente, cod_partida";
					}
					elseif ($opcion == "generar-anticipo") {
		                $sql = "SELECT
		                			of.CodObra,
		                			of.Secuencia,
		                            of.MontoAnticipo AS Monto,
		                            pc20.CodCuenta AS CodCuentaPub20,
		                            'S' AS FlagNoAfectoIGV,
									'$field_obligacion[NroControl]' AS Referencia,
		                            '' AS NomPartida,
		                            '' AS NomCuenta,
		                            pc20.Descripcion AS NomCuentaPub20,
		                            pv.CategoriaProg,
		                            pv.Ejercicio,
		                            '$field_obligacion[CodCentroCosto]' AS CodCentroCosto
								FROM
									ob_obrasfinanciero of
									INNER JOIN ob_obras o ON (o.CodObra = of.CodObra)
		                            LEFT JOIN ac_mastplancuenta20 pc20 ON (pc20.CodCuenta = '111280101')
									LEFT JOIN pv_presupuesto pv On (pv.CodOrganismo = o.CodOrganismo AND pv.CodPresupuesto = o.CodPresupuesto)
								WHERE of.CodObra = '$sel_registros'
								ORDER BY Secuencia";
					}
					elseif ($opcion == "generar-valuacion") {
		                $sql = "SELECT
		                			vp.*,
		                			pc.CodCuenta,
		                            pc20.CodCuenta AS CodCuentaPub20,
		                            'N' AS FlagNoAfectoIGV,
									'$field_obligacion[NroControl]' AS Referencia,
		                            pv.denominacion AS NomPartida,
		                            pc.Descripcion AS NomCuenta,
		                            pc20.Descripcion AS NomCuentaPub20,
		                            ppto.CategoriaProg,
		                            ppto.Ejercicio,
		                            '$field_obligacion[CodCentroCosto]' AS CodCentroCosto
								FROM
									ob_valuacionespresupuesto vp
									LEFT JOIN pv_partida pv ON (pv.cod_partida = vp.cod_partida)
		                            LEFT JOIN ac_mastplancuenta pc ON (pc.CodCuenta = pv.CodCuenta)
		                            LEFT JOIN ac_mastplancuenta20 pc20 ON (pc20.CodCuenta = pv.CodCuentaPub20)
									LEFT JOIN pv_presupuesto ppto On (ppto.CodOrganismo = vp.CodOrganismo AND ppto.CodPresupuesto = vp.CodPresupuesto)
								WHERE vp.CodValuacion = '$sel_registros'
								ORDER BY Secuencia";
					}
					elseif ($opcion == "viaticos-generar") {
						$sql = "SELECT
									vd.MontoTotal AS Monto,
									vd.CodCuenta,
									vd.CodCuentaPub20,
									vd.cod_partida,
									vd.Descripcion,
									vd.CodPresupuesto,
									vd.CodFuente,
									pv.denominacion As NomPartida,
									pc.Descripcion AS NomCuenta,
									pc20.Descripcion As NomCuentaPub20,
		                            ppto.CategoriaProg,
		                            ppto.Ejercicio,
		                            '$field_obligacion[CodCentroCosto]' AS CodCentroCosto,
		                            '$field_obligacion[CodProveedor]' AS CodPersona,
		                            '$_PARAMETRO[DOCVIAT]' AS TipoOrden,
		                            v.CodViatico AS NroOrden,
		                            CONCAT('$_PARAMETRO[DOCVIAT]', v.Anio, '-', v.CodInterno) AS Referencia
								FROM
									ap_viaticosdetalle vd
									INNER JOIN ap_viaticos v ON (v.CodOrganismo = vd.CodOrganismo AND v.CodViatico = vd.CodViatico)
									INNER JOIN pv_partida pv ON (pv.cod_partida = vd.cod_partida)
									LEFT JOIN ac_mastplancuenta pc ON (pc.CodCuenta = vd.CodCuenta)
									LEFT JOIN ac_mastplancuenta20 pc20 ON (pc20.CodCuenta = vd.CodCuentaPub20)
									LEFT JOIN pv_presupuesto ppto On (ppto.CodOrganismo = vd.CodOrganismo AND ppto.CodPresupuesto = vd.CodPresupuesto)
								WHERE
									vd.CodOrganismo = '$CodOrganismo' AND
									vd.CodViatico = '$CodViatico'";
					}
					elseif ($opcion == "adelanto-generar") {
						$sql = "SELECT
									(ga.MontoAfecto + ga.MontoNoAfecto) AS Monto,
									td.CodCuentaAde AS CodCuenta,
									td.CodCuentaAdePub20 AS CodCuentaPub20,
									'' AS cod_partida,
									ga.Descripcion,
									'' AS CodPresupuesto,
									'' AS CodFuente,
									'' AS NomPartida,
									pc.Descripcion AS NomCuenta,
									pc20.Descripcion As NomCuentaPub20,
		                            '' AS CategoriaProg,
		                            SUBSTRING(ga.Periodo, 1, 4) AS Ejercicio,
		                            ga.CodCentroCosto,
		                            ga.CodProveedor AS CodPersona,
		                            ga.CodClasificacion AS TipoOrden,
		                            ga.NroAdelanto AS NroOrden,
		                            CONCAT(ga.CodClasificacion, '-', ga.NroAdelanto) AS Referencia
								FROM ap_gastoadelanto ga
								INNER JOIN ap_tipodocumento td ON (
									td.CodTipoDocumento = (CASE WHEN ga.CodClasificacion = 'AP' THEN 'APR' ELSE 'ACO' END)
								)
								LEFT JOIN ac_mastplancuenta pc ON pc.CodCuenta = td.CodCuentaAde
								LEFT JOIN ac_mastplancuenta20 pc20 ON pc20.CodCuenta = td.CodCuentaAdePub20
								WHERE ga.CodAdelanto = '$CodAdelanto'";
					}
					else {
		                $sql = "SELECT 
		                            oc.*,
		                            p.denominacion AS NomPartida,
		                            pc.Descripcion AS NomCuenta,
		                            pc20.Descripcion AS NomCuentaPub20,
		                            cc.Codigo AS NomCentroCosto, 
		                            pv.CategoriaProg
		                        FROM 
		                            ap_obligacionescuenta oc
		                            LEFT JOIN pv_partida p ON (oc.cod_partida = p.cod_partida)
		                            LEFT JOIN ac_mastplancuenta pc ON (oc.CodCuenta = pc.CodCuenta)
		                            LEFT JOIN ac_mastplancuenta20 pc20 ON (oc.CodCuentaPub20 = pc20.CodCuenta)
		                            LEFT JOIN ac_mastcentrocosto cc ON (oc.CodCentroCosto = cc.CodCentroCosto)
									LEFT JOIN pv_presupuesto pv On (pv.CodOrganismo = oc.CodOrganismo AND pv.CodPresupuesto = oc.CodPresupuesto)
		                        WHERE
		                            oc.CodProveedor = '".$field_obligacion['CodProveedor']."' AND
		                            oc.CodTipoDocumento = '".$field_obligacion['CodTipoDocumento']."' AND
		                            oc.NroDocumento = '".$field_obligacion['NroDocumento']."'";
					}
	                $query_distribucion = mysql_query($sql) or die ($sql.mysql_error());	$nro_distribucion=0;
	                while ($field_distribucion = mysql_fetch_array($query_distribucion)) {
	                    $nro_distribucion++;
	                    ?>
	                    <tr class="trListaBody" onclick="mClk(this, 'sel_distribucion');" id="distribucion_<?=$nro_distribucion?>">
	                        <th><?=$nro_distribucion?></th>
	                        <td align="center" width="75">
	                            <input type="text" name="cod_partida" id="cod_partida_<?=$nro_distribucion?>" value="<?=$field_distribucion['cod_partida']?>" style="width:99%; text-align:center;" maxlength="12" class="cell cod_partida" onChange="getDescripcionLista2('accion=getDescripcionPartidaDisponible&CodOrganismo='+$('CodOrganismo').val(), this, $('#NomPartida_<?=$nro_distribucion?>'));" <?=$disabled_distribucion?> />
	                        </td>
	                        <td align="center" width="225">
	                            <input type="text" name="NomPartida" id="NomPartida_<?=$nro_distribucion?>" value="<?=($field_distribucion['NomPartida'])?>" style="width:99%;" class="cell2" readonly="readonly" />
	                        </td>
	                        <td align="center" width="80">
	                            <input type="text" name="CodCuenta" id="CodCuenta_<?=$nro_distribucion?>" value="<?=$field_distribucion['CodCuenta']?>" maxlength="13" style="width:99%; text-align:center;" class="cell" onChange="getDescripcionLista2('accion=getDescripcionCuenta', this, $('#NomCuenta_<?=$nrodetalle?>'));" <?=$disabled_distribucion?> />
	                        </td>
	                        <td align="center" width="220">
	                            <input type="text" name="NomCuenta" id="NomCuenta_<?=$nro_distribucion?>" value="<?=($field_distribucion['NomCuenta'])?>" style="width:99%;" class="cell2" readonly="readonly" />
	                        </td>
	                        <td align="center" width="80">
	                            <input type="text" name="CodCuentaPub20" id="CodCuentaPub20_<?=$nro_distribucion?>" value="<?=$field_distribucion['CodCuentaPub20']?>" maxlength="13" style="width:99%; text-align:center;" class="cell" onChange="getDescripcionLista2('accion=getDescripcionCuentaPub20', this, $('#NomCuentaPub20_<?=$nrodetalle?>'));" <?=$disabled_distribucion?> />
	                        </td>
	                        <td align="center" width="220">
	                            <input type="text" name="NomCuentaPub20" id="NomCuentaPub20_<?=$nro_distribucion?>" value="<?=($field_distribucion['NomCuentaPub20'])?>" style="width:99%;" class="cell2" readonly="readonly" />
	                        </td>
	                        <td align="center">
	                            <input type="text" name="CodCentroCosto" id="CodCentroCosto_<?=$nro_distribucion?>" value="<?=$field_distribucion['CodCentroCosto']?>" style="text-align:center;" class="cell" <?=$disabled_distribucion?> />
	                            <input type="hidden" name="NomCentroCosto" id="NomCentroCosto_<?=$nro_distribucion?>" value="<?=$field_distribucion['NomCentroCosto']?>" />
	                        </td>
	                        <td align="center">
	                            <input type="checkbox" name="FlagNoAfectoIGV" class="FlagNoAfectoIGV" <?=chkFlag($field_distribucion['FlagNoAfectoIGV'])?> onchange="actualizarMontosObligacion();" <?=$disabled_distribucion?> <?=$dFlagNoAfectoIGV?> />
	                        </td>
	                        <td align="center">
	                            <input type="text" name="Monto" value="<?=number_format($field_distribucion['Monto'], 2, ',', '.')?>" style="text-align:right;" class="cell" onfocus="numeroFocus(this);" onblur="numeroBlur(this);" onchange="actualizarMontosObligacion();" <?=$disabled_distribucion?> />
	                        </td>
				            <td align="center">
				                <input type="text" name="detallesCategoriaProg" id="detallesCategoriaProg_<?=$nrodetalles?>" class="cell2 CategoriaProg" style="text-align:center;" value="<?=$field_distribucion['CategoriaProg']?>" readonly />
				                <input type="hidden" name="detallesEjercicio" id="detallesEjercicio_<?=$nrodetalles?>" class="cell2 Ejercicio" style="text-align:center;" value="<?=$field_distribucion['Ejercicio']?>" readonly />
				                <input type="hidden" name="detallesCodPresupuesto" id="detallesCodPresupuesto_<?=$nrodetalles?>" class="cell2 CodPresupuesto" style="text-align:center;" value="<?=$field_distribucion['CodPresupuesto']?>" readonly />
				            </td>
				            <td>
				                <select name="detallesCodFuente" id="detallesCodFuente_<?=$nrodetalles?>" class="cell2 CodFuente" <?=$disabled_ver?>>
				                    <?=loadSelect2("pv_fuentefinanciamiento","CodFuente","Denominacion",$field_distribucion['CodFuente'],11)?>
				                </select>
				            </td>
	                        <td align="center" width="25">
	                            <input type="text" name="TipoOrden" value="<?=$field_distribucion['TipoOrden']?>" maxlength="2" style="width:99%; text-align:center;" class="cell" <?=$disabled_distribucion?> />
	                        </td>
	                        <td align="center" width="85">
	                            <input type="text" name="NroOrden" value="<?=$field_distribucion['NroOrden']?>" maxlength="100" style="width:99%;" class="cell" <?=$disabled_distribucion?> />
	                        </td>
	                        <td align="center">
	                            <input type="text" name="Referencia" value="<?=$field_distribucion['Referencia']?>" maxlength="25" class="cell" <?=$disabled_distribucion?> />
	                        </td>
	                        <td align="center">
	                            <input type="text" name="Descripcion" value="<?=($field_distribucion['Descripcion'])?>" maxlength="255" class="cell" <?=$disabled_distribucion?> />
	                        </td>
	                        <td align="center">
	                            <input type="text" name="CodPersona" id="CodPersona_<?=$nro_distribucion?>" value="<?=$field_distribucion['CodPersona']?>" maxlength="6" style="text-align:center;" class="cell" <?=$disabled_distribucion?> />
	                            <input type="hidden" name="NomPersona" id="NomPersona_<?=$nro_distribucion?>" value="<?=$field_distribucion['NomPersona']?>" />
	                        </td>
	                        <td align="center">
	                            <input type="text" name="NroActivo" id="NroActivo_<?=$nro_distribucion?>" value="<?=$field_distribucion['NroActivo']?>" maxlength="15" style="text-align:center;" class="cell2" readonly="readonly" />
	                        </td>
	                        <td align="center">
	                            <input type="checkbox" name="FlagDiferido" <?=$disabled_distribucion?> />
	                        </td>
	                    </tr>
	                    <?php
	                    $distribucion_total += $field_distribucion['Monto'];
	                }
	                ?>
	                </tbody>
	                
	                <tfoot id="foot_distribucion">
	                <tr>
	                    <th colspan="9">&nbsp;</th>
	                    <th>
	                       	<input type="text" id="distribucion_total" value="<?=number_format($distribucion_total, 2, ',', '.')?>" style="text-align:right; font-weight:bold;" class="cell2" readonly="readonly" />
	                    </th>
	                </tr>
	                </tfoot>
	            </table>
	            </div></td></tr></table>
	            <input type="hidden" id="nro_distribucion" value="<?=$nro_distribucion?>" />
	            <input type="hidden" id="can_distribucion" value="<?=$nro_distribucion?>" />
	            </form>
	        </td>
	    </tr>
	</table>
</div>

<div id="tab4" style="display:none;">
	<center>
	<div style="width:1100px;" class="divFormCaption">Distribuci&oacute;n Contable</div>
	<div style="overflow:scroll; width:1100px; height:150px;">
	<table width="100%" class="tblLista">
		<thead>
		<tr>
	        <th width="125">Cuenta</th>
	        <th>Descripci&oacute;n</th>
	        <th width="100">Monto</th>
	    </tr>
	    </thead>
	    
	    <tbody id="lista_cuentas">
	    <?php
		$nrocuentas = 0;
		$sql = "SELECT
					do.CodCuenta,
					pc.Descripcion,
					do.Monto
				FROM
					ap_distribucionobligacion do
					INNER JOIN ac_mastplancuenta pc ON (do.CodCuenta = pc.CodCuenta)
				WHERE
					do.CodProveedor = '".$field_obligacion['CodProveedor']."' AND
					do.CodTipoDocumento = '".$field_obligacion['CodTipoDocumento']."' AND
					do.NroDocumento = '".$field_obligacion['NroDocumento']."'
				ORDER BY CodCuenta";
		$query_cuentas = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		while ($field_cuentas = mysql_fetch_array($query_cuentas)) {
			$nrocuentas++;
			?>
			<tr class="trListaBody">
				<td align="center">
					<?=$field_cuentas['CodCuenta']?>
	            </td>
				<td>
					<?=$field_cuentas['Descripcion']?>
	            </td>
				<td align="right">
					<?=number_format($field_cuentas['Monto'], 2, ',', '.')?>
	            </td>
			</tr>
			<?php
		}
		?>
	    </tbody>
	</table>
	</div>

	<div style="width:1100px;" class="divFormCaption">Distribuci&oacute;n Contable (Pub. 20)</div>
	<div style="overflow:scroll; width:1100px; height:150px;">
	<table width="100%" class="tblLista">
		<thead>
		<tr>
	        <th width="125">Cuenta</th>
	        <th>Descripci&oacute;n</th>
	        <th width="100">Monto</th>
	    </tr>
	    </thead>
	    
	    <tbody id="lista_cuentas20">
	    <?php
		$nrocuentas = 0;
		$sql = "SELECT
					do.CodCuentaPub20,
					pc.Descripcion,
					do.Monto
				FROM
					ap_distribucionobligacion do
					INNER JOIN ac_mastplancuenta20 pc ON (do.CodCuentaPub20 = pc.CodCuenta)
				WHERE
					do.CodProveedor = '".$field_obligacion['CodProveedor']."' AND
					do.CodTipoDocumento = '".$field_obligacion['CodTipoDocumento']."' AND
					do.NroDocumento = '".$field_obligacion['NroDocumento']."'
				ORDER BY CodCuentaPub20";
		$query_cuentas = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		while ($field_cuentas = mysql_fetch_array($query_cuentas)) {
			$nrocuentas++;
			?>
			<tr class="trListaBody">
				<td align="center">
					<?=$field_cuentas['CodCuentaPub20']?>
	            </td>
				<td>
					<?=$field_cuentas['Descripcion']?>
	            </td>
				<td align="right">
					<?=number_format($field_cuentas['Monto'], 2, ',', '.')?>
	            </td>
			</tr>
			<?php
		}
		?>
	    </tbody>
	</table>
	</div>

	<div style="width:1100px;" class="divFormCaption">Distribuci&oacute;n Presupuestaria</div>
	<table width="1100" class="tblBotones">
	    <tr>
	    	<td width="35"><div style="background-color:#F8637D; width:25px; height:20px;"></div></td>
	        <td>Sin disponibilidad presupuestaria</td>
	    	<td width="35"><div style="background-color:#D0FDD2; width:25px; height:20px;"></div></td>
	        <td>Disponibilidad presupuestaria</td>
	    	<td width="35"><div style="background-color:#FFC; width:25px; height:20px;"></div></td>
	        <td>Disponibilidad presupuestaria (Tiene ordenes pendientes)</td>
			<td align="right" class="gallery clearfix">
	        	<a id="a_disponibilidad" href="pagina.php?iframe=true" rel="prettyPhoto[iframe11]" style="display:none;"></a>
				<input type="button" value="Disponibilidad Presupuestaria" onclick="verDisponibilidadPresupuestaria();" />
			</td>
		</tr>
	</table>
	<div style="overflow:scroll; width:1100px; height:150px;">
	<form name="frm_partidas" id="frm_partidas">
	<table width="100%" class="tblLista">
		<thead>
		<tr>
	        <th width="25">F.F</th>
	        <th width="100">Partida</th>
	        <th>Descripci&oacute;n</th>
	        <th width="100">Monto</th>
	    </tr>
	    </thead>
	    
	    <tbody id="lista_partidas">
	    <?php
		$nropartidas = 0;
		$Grupo = '';
		$sql = "SELECT
					do.cod_partida,
					p.denominacion,
					do.Monto,
					do.Ejercicio,
					do.CodPresupuesto,
					do.CodFuente,
					pv.CategoriaProg,
					ff.Denominacion AS Fuente,
					ue.Denominacion AS UnidadEjecutora,
					CONCAT(ss.CodSector, pr.CodPrograma, a.CodActividad) AS CatProg
				FROM
					ap_distribucionobligacion do
					INNER JOIN pv_partida p ON (do.cod_partida = p.cod_partida)
					LEFT JOIN pv_fuentefinanciamiento ff ON (ff.CodFuente = do.CodFuente)
					LEFT JOIN pv_presupuesto pv On (do.CodOrganismo = do.CodOrganismo AND do.CodPresupuesto = pv.CodPresupuesto)
					LEFT JOIN pv_categoriaprog cp ON (cp.CategoriaProg = pv.CategoriaProg)
					LEFT JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cp.CodUnidadEjec)
					LEFT JOIN pv_actividades a ON (a.IdActividad = cp.IdActividad)
					LEFT JOIN pv_proyectos py ON (py.IdProyecto = a.IdProyecto)
					LEFT JOIN pv_subprogramas spr ON (spr.IdSubPrograma = py.IdSubPrograma)
					LEFT JOIN pv_programas pr ON (pr.IdPrograma = spr.IdPrograma)
					LEFT JOIN pv_subsector ss ON (ss.IdSubSector = pr.IdSubSector)
				WHERE
					do.CodProveedor = '".$field_obligacion['CodProveedor']."' AND
					do.CodTipoDocumento = '".$field_obligacion['CodTipoDocumento']."' AND
					do.NroDocumento = '".$field_obligacion['NroDocumento']."'
				ORDER BY cod_partida";
		$query_partidas = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		while ($field_partidas = mysql_fetch_array($query_partidas)) {
			if ($Grupo != $field_partidas['CatProg']) {
				$Grupo = $field_partidas['CatProg'];
				?>
				<tr class="trListaBody2">
					<td colspan="3">
						<?=$field_partidas['CatProg']?> - <?=$field_partidas['UnidadEjecutora']?>
					</td>
				</tr>
				<?php
			}
			list($MontoAjustado, $MontoCompromiso, $PreCompromiso, $CotizacionesAsignadas) = disponibilidadPartida2($field_partidas['Ejercicio'], $field_obligacion['CodOrganismo'], $field_partidas['cod_partida'], $field_partidas['CodPresupuesto'], $field_partidas['CodFuente']);
			if ($opcion == 'nuevo') {
				$PreCompromiso -= $field_partidas['Monto'];
			}
			elseif ($field_obligacion['estado'] == 'RV' || $field_obligacion['estado'] == 'AP' || $field_obligacion['estado'] == 'PA') {
				$MontoCompromiso -= $field_partidas['Monto'];
			}
			else {
				//$MontoCompromiso -= $field_partidas['Monto'];
			}
			$MontoPendiente = $PreCompromiso + $CotizacionesAsignadas;
			$MontoDisponible = $MontoAjustado - $MontoCompromiso;
			$MontoDisponibleReal = $MontoAjustado - ($MontoCompromiso + $MontoPendiente);
			##	valido
			if (($MontoDisponible - $field_partidas['Monto']) <= 0) $style = "style='background-color:#F8637D;'";
			elseif(($MontoDisponibleReal - $field_partidas['Monto']) <= 0) $style = "style='background-color:#FFC;'";
			else $style = "style='background-color:#D0FDD2;'";
			?>
			<tr class="trListaBody" <?=$style?>>
            	<td align="center"><?=$field_partidas['CodFuente']?></td>
				<td align="center">
	                <input type="hidden" name="cod_partida" value="<?=$field_partidas['cod_partida']?>" />
                    <input type="hidden" name="CodCuenta" value="<?=$field_partidas['CodCuenta']?>" />
                    <input type="hidden" name="CodCuentaPub20" value="<?=$field_partidas['CodCuentaPub20']?>" />
	                <input type="hidden" name="Monto" value="<?=$field_partidas['Monto']?>" />
	                <input type="hidden" name="MontoAjustado" value="<?=$MontoAjustado?>" />
	                <input type="hidden" name="MontoCompromiso" value="<?=$MontoCompromiso?>" />
	                <input type="hidden" name="PreCompromiso" value="<?=$PreCompromiso?>" />
	                <input type="hidden" name="CotizacionesAsignadas" value="<?=$CotizacionesAsignadas?>" />
	                <input type="hidden" name="MontoDisponible" value="<?=$MontoDisponible?>" />
	                <input type="hidden" name="MontoDisponibleReal" value="<?=$MontoDisponibleReal?>" />
	                <input type="hidden" name="MontoPendiente" value="<?=$MontoPendiente?>" />
					<input type="hidden" name="partidasCodFuente" value="<?=$field_partidas['CodFuente']?>" />
					<input type="hidden" name="partidasCategoriaProg" value="<?=$field_partidas['CategoriaProg']?>" />
					<?=$field_partidas['cod_partida']?>
	            </td>
				<td>
					<?=htmlentities($field_partidas['denominacion'])?>
	            </td>
				<td align="right">
					<?=number_format($field_partidas['Monto'], 2, ',', '.')?>
	            </td>
			</tr>
			<?php
		}
		?>
	    </tbody>
	</table>
	</form>
	</div>
	</center>
</div>

<div id="tab5" style="display:none;">
	<form name="frmfacturas" id="frmfacturas" method="POST">
		<input type="hidden" id="sel_facturas" />
		<table width="<?=$_width?>" class="tblBotones">
			<thead>
				<tr>
					<th class="divFormCaption" colspan="2">Registro de Facturas</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td align="right">
						<input type="button" class="btLista" value="Insertar" onclick="insertar2(this, 'facturas', 'modulo=ajax&accion=facturas_insertar', 'ap_obligacion_ajax.php');" <?=$disabled_facturas?> />
						<input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'facturas'); setMontos();" <?=$disabled_facturas?> />
					</td>
				</tr>
			</tbody>
		</table>
		<div style="overflow:scroll; width:<?=$_width?>px; height:250px; margin:auto;">
			<table class="tblLista" style="width:100%; min-width:<?=$_width-50?>px;">
				<thead>
					<tr>
						<th width="20">#</th>
						<th width="125">Nro. Control</th>
						<th width="125">Nro. Factura</th>
						<th width="80" align="right">Monto Afecto</th>
						<th width="80" align="right">Monto No Afecto</th>
						<th width="80" align="right">Monto Impuesto</th>
						<th width="80" align="right">Monto Factura</th>
						<th>Impuesto / Retenci&oacute;n</th>
						<th width="50" align="right">%</th>
						<th width="80" align="right">Monto Retenido</th>
					</tr>
				</thead>
				
				<tbody id="lista_facturas">
					<?php
					$nro_facturas = 0;
					$sql = "SELECT *
							FROM ap_obligacionesfacturas
							WHERE
								CodProveedor = '".$field_obligacion['CodProveedor']."' AND
								CodTipoDocumento = '".$field_obligacion['CodTipoDocumento']."' AND
								NroDocumento = '".$field_obligacion['NroDocumento']."'";
					$field_facturas = getRecords($sql);
					foreach ($field_facturas as $f) {
						$id = ++$nro_facturas;
						?>
						<tr class="trListaBody" onclick="clk($(this), 'facturas', 'facturas_<?=$id?>');" id="facturas_<?=$id?>">
							<th>
								<?=$nro_facturas?>
							</th>
							<td>
								<input type="text" name="facturas_NroControl[]" value="<?=$f['NroControl']?>" style="text-align:center;" class="cell" maxlength="20">
							</td>
							<td>
								<input type="text" name="facturas_NroFactura[]" value="<?=$f['NroFactura']?>" style="text-align:center;" class="cell" maxlength="20">
							</td>
							<td>
								<input type="text" name="facturas_MontoAfecto[]" id="facturas_MontoAfecto<?=$id?>" value="<?=number_format($f['MontoAfecto'],2,',','.')?>" style="text-align:right;" class="cell currency" onchange="getMontoImpuesto('<?=$id?>');">
							</td>
							<td>
								<input type="text" name="facturas_MontoNoAfecto[]" id="facturas_MontoNoAfecto<?=$id?>" value="<?=number_format($f['MontoNoAfecto'],2,',','.')?>" style="text-align:right;" class="cell currency" onchange="getMontoImpuesto('<?=$id?>');">
							</td>
							<td>
								<input type="text" name="facturas_MontoImpuesto[]" id="facturas_MontoImpuesto<?=$id?>" value="<?=number_format($f['MontoImpuesto'],2,',','.')?>" style="text-align:right;" class="cell currency" onchange="setMontoImpuesto('<?=$id?>');">
							</td>
							<td>
								<input type="text" name="facturas_MontoFactura[]" id="facturas_MontoFactura<?=$id?>" value="<?=number_format($f['MontoFactura'],2,',','.')?>" style="text-align:right;" class="cell currency" readonly>
							</td>
							<td>
                                <select name="facturas_CodImpuesto[]" id="facturas_CodImpuesto<?=$id?>" class="cell" onchange="getMontoRetencion(this.value, '<?=$id?>');">
                                	<?=loadSelect2('mastimpuestos','CodImpuesto','Descripcion',$f['CodImpuesto'])?>
                                </select>
							</td>
							<td>
								<input type="text" name="facturas_FactorPorcentaje[]" id="facturas_FactorPorcentaje<?=$id?>" value="<?=number_format($f['FactorPorcentaje'],2,',','.')?>" style="text-align:right;" class="cell currency" readonly>
							</td>
							<td>
								<input type="text" name="facturas_MontoRetenido[]" id="facturas_MontoRetenido<?=$id?>" value="<?=number_format($f['MontoRetenido'],2,',','.')?>" style="text-align:right;" class="cell currency">
							</td>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>
		</div>
		<input type="hidden" id="nro_facturas" value="<?=$nro_facturas?>" />
		<input type="hidden" id="can_facturas" value="<?=$nro_facturas?>" />
	</form>
</div>

<div id="tab6" style="display:none;">
	<form name="frmadelantos" id="frmadelantos" method="POST">
		<input type="hidden" id="sel_adelantos" />
		<table width="<?=$_width?>" class="tblBotones">
			<thead>
				<tr>
					<th class="divFormCaption" colspan="2">Adelantos aplicados contra la Obligación</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td align="right" class="gallery clearfix">
						<a id="a_adelantos" href="../lib/listas/gehen.php?anz=lista_ap_gastoadelanto&filtrar=default&ventana=obligacion_adelanto&detalle=adelantos&modulo=ajax&accion=adelantos_insertar&url=../../ap/ap_obligacion_ajax.php&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe15]" style="display:none;"></a>
						<input type="button" class="btLista" value="Insertar" onclick="abrirListaAdelanto();" <?=$disabled_adelantos?> />
						<input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'adelantos');" <?=$disabled_adelantos?> />
					</td>
				</tr>
			</tbody>
		</table>
		<div style="overflow:scroll; width:<?=$_width?>px; height:250px; margin:auto;">
			<table class="tblLista" style="width:100%; min-width:1250px;">
				<thead>
					<tr>
						<th width="20">#</th>
						<th width="100">Fecha</th>
						<th width="100">Persona #</th>
						<th width="100">Tipo</th>
						<th width="100">Adelanto #</th>
						<th width="150" align="right">Monto</th>
						<th>Descripción</th>
					</tr>
				</thead>
				
				<tbody id="lista_adelantos">
					<?php
					$nro_adelantos = 0;
					$sql = "SELECT
								oa.*,
								ga.MontoTotal
							FROM ap_obligacionesadelantos oa
							INNER JOIN ap_gastoadelanto ga ON ga.CodAdelanto = oa.CodAdelanto
							WHERE
								CodProveedor = '$field_obligacion[CodProveedor]' AND
								CodTipoDocumento = '$field_obligacion[CodTipoDocumento]' AND
								NroDocumento = '$field_obligacion[NroDocumento]'";
					$field_adelantos = getRecords($sql);
					foreach ($field_adelantos as $f) {
						$id = ++$nro_adelantos;
						?>
						<tr class="trListaBody" onclick="clk($(this), 'adelantos', 'adelantos_<?=$id?>');" id="adelantos_<?=$id?>">
							<th>
								<input type="hidden" name="adelantos_CodAdelanto[]" value="<?=$f['CodAdelanto']?>">
								<input type="hidden" name="adelantos_MontoTotal[]" value="<?=$f['MontoTotal']?>">
								<?=$nro_detalle?>
							</th>
							<td align="center"><?=formatFechaDMA($f['FechaDocumento'])?></td>
							<td align="center"><?=$f['CodProveedor']?></td>
							<td align="center"><?=printValores('adelanto-tipo',$f['TipoAdelanto'])?></td>
							<td align="center"><?=$f['NroAdelanto']?></td>
							<td align="right">
								<strong><?=number_format($f['MontoTotal'],2,',','.')?></strong>
							</td>
							<td><?=$f['Descripcion']?></td>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>
		</div>
		<input type="hidden" id="nro_adelantos" value="<?=$nro_adelantos?>" />
		<input type="hidden" id="can_adelantos" value="<?=$nro_adelantos?>" />
	</form>
</div>

<script type="text/javascript" charset="utf-8">
	//	valido si el tipo de servioio es afecto a impuesto o no
	function afectaTipoServicioObligacion(CodTipoServicio) {
		$.ajax({
			type: "POST",
			url: "../lib/fphp_funciones_ajax.php",
			data: "accion=afectaTipoServicio&CodTipoServicio="+CodTipoServicio,
			async: false,
			success: function(resp) {
				//	activo/desactivo afecto de la distribucion
				if (resp.trim() == "S") {
					$(".FlagNoAfectoIGV").removeAttr("checked");
					if (document.getElementById("FlagDistribucionManual").checked) $(".FlagNoAfectoIGV").removeAttr("disabled");
				} else {
					$("#lista_impuesto").html("");
					$(".FlagNoAfectoIGV").attr("disabled", "disabled").attr("checked", "checked");
				}
				//	actualizo valores
				<?php
				if ($opcion != "generar-valuacion") {
					?>actualizarMontosObligacion();<?php
				}
				?>
			}
		});
	}
	//	check/uncheck afecta compromiso
	function FlagCompromisoObligacion(boo) {
		if (!document.getElementById("FlagPresupuesto").disabled && !document.getElementById("FlagDistribucionManual").disabled) {
			if (boo) {
				$("#FlagPresupuesto").attr("checked", "checked");
				$("#FlagDistribucionManual").attr("checked", "checked");
				setObligacionPagoDirecto(true);
			} else {
				$("#FlagDistribucionManual").prop("checked", boo);
				setObligacionPagoDirecto(false);
			}
		}
	}
	//	check/uncheck afecta presupuesto
	function FlagPresupuestoObligacion(boo) {
		if (boo) {
			$("#FlagCompromiso").removeAttr("disabled").removeAttr("checked");
			$("#btSelPartida").removeAttr("disabled");
			$(".cell.cod_partida").removeAttr("disabled");
			$("#FlagDistribucionManual").prop("checked", false);
			setObligacionPagoDirecto(false);
		} else {
			$("#FlagCompromiso").attr("disabled", "disabled").removeAttr("checked", "checked");
			$("#FlagDistribucionManual").attr("checked", "checked");
			$("#btSelPartida").attr("disabled", "disabled");
			$(".cell.cod_partida").attr("disabled", "disabled");
			setObligacionPagoDirecto(true);
		}
	}
	//	check/uncheck pago directo
	function setObligacionPagoDirecto(boo) {
		if ($("#CodProveedor").val().trim() != "") {
			//	limpio las listas
			$("#lista_documento").html("");
			$("#lista_distribucion").html("");
			$("#lista_impuesto").html("");
			$("#lista_adelantos").html("");
			//	si selecciono pago directo
			if (boo) {
				$("#btInsertarDocumento").attr("disabled", "disabled");
				$("#btQuitarDocumento").attr("disabled", "disabled");
				$("#btInsertarDistribucion").removeAttr("disabled");
				$("#btQuitarDistribucion").removeAttr("disabled");
				$("#btSelCuenta").removeAttr("disabled");
				$("#btSelCuenta20").removeAttr("disabled");
				$("#btSelCCosto").removeAttr("disabled");
				$("#btSelPersona").removeAttr("disabled");
				$("#btSelActivo").removeAttr("disabled");
				$("#btInsertarImpuesto").removeAttr("disabled");
				$("#btQuitarImpuesto").removeAttr("disabled");
				if ($("#FlagPresupuesto").attr("checked") == "checked") $("#btSelPartida").removeAttr("disabled");
			} else {
				$("#btInsertarDocumento").removeAttr("disabled");
				$("#btQuitarDocumento").removeAttr("disabled");
				$("#btInsertarDistribucion").attr("disabled", "disabled");
				$("#btQuitarDistribucion").attr("disabled", "disabled");
				$("#btSelPartida").attr("disabled", "disabled");
				$("#btSelCuenta").attr("disabled", "disabled");
				$("#btSelCuenta20").attr("disabled", "disabled");
				$("#btSelCCosto").attr("disabled", "disabled");
				$("#btSelPersona").attr("disabled", "disabled");
				$("#btSelActivo").attr("disabled", "disabled");
				$("#btInsertarImpuesto").attr("disabled", "disabled");
				$("#btQuitarImpuesto").attr("disabled", "disabled");
				$("#FlagCompromiso").removeAttr("checked");
				$("#FlagPresupuesto").attr("checked", "checked");
			}
			//	actualizo valores
			actualizarMontosObligacion();
		}
	}
	//	modificar monto del impuesto
	function cambiar_monto_impuesto() {
		var MontoAfecto = parseFloat(setNumero($("#MontoAfecto").val()));
		var MontoNoAfecto = parseFloat(setNumero($("#MontoNoAfecto").val()));
		var MontoImpuesto = parseFloat(setNumero($("#MontoImpuesto").val()));
		actualizar_afecto_retenciones(MontoAfecto, MontoNoAfecto, MontoImpuesto, document.getElementById("frm_impuesto"));
		var MontoImpuestoOtros = obtener_obligacion_retenciones(document.getElementById("frm_impuesto"));
		var MontoObligacion = MontoAfecto + MontoNoAfecto + MontoImpuesto + MontoImpuestoOtros;
		$("#MontoImpuestoOtros").val(setNumeroFormato(MontoImpuestoOtros, 2, ".", ","));
		$("#MontoObligacion").val(setNumeroFormato(MontoObligacion, 2, ".", ","));
		$("#MontoPagar").val(setNumeroFormato(MontoObligacion, 2, ".", ","));
		$("#MontoPendiente").val(setNumeroFormato(MontoObligacion, 2, ".", ","));
		$("#impuesto_total").val(setNumeroFormato(MontoImpuestoOtros, 2, ".", ","));
	}
	//	quitar linea de documentos
	function quitarLineaObligacionDocumento(boton, detalle) {
		/*
		.- boton	-> referencia del boton (objeto)
		.- detalle	-> sufijo de los campos de la lista
		*/
		boton.disabled = true;
		var can = "can_" + detalle;
		var sel = "sel_" + detalle;	
		var lista = "lista_" + detalle;
		var id = document.getElementById(sel).value;
		if (document.getElementById(sel).value == "") alert("¡Debe seleccionar una linea!");
		else {
			//	elimino la linea del documento
			var candetalle = new Number(document.getElementById(can).value); candetalle--;
			document.getElementById(can).value = candetalle;
			var seldetalle = document.getElementById(sel).value;
			var listaDetalles = document.getElementById(lista);
			var tr = document.getElementById(seldetalle);
			listaDetalles.removeChild(tr);
			//	elimino las lineas de la distribucion
			var idsel = document.getElementById(sel).value;
			var partes = idsel.split("_");
			var idTr = ".trListaBody.distribucion_" + partes[1];
			$(idTr).remove();
			document.getElementById(sel).value = "";
		}
		boton.disabled = false;
		if (candetalle == 0) setObligacionPagoDirecto(document.getElementById("FlagDistribucionManual").checked);
		else actualizarMontosObligacion();
	}
	//	quitar linea de distribucion
	function quitarLineaDistribucion(boton, detalle) {
		/*
		.- boton	-> referencia del boton (objeto)
		.- detalle	-> sufijo de los campos de la lista
		*/
		boton.disabled = true;
		var can = "can_" + detalle;
		var sel = "sel_" + detalle;	
		var lista = "lista_" + detalle;
		if (document.getElementById(sel).value == "") alert("¡Debe seleccionar una linea!");
		else {
			var candetalle = new Number(document.getElementById(can).value); candetalle--;
			document.getElementById(can).value = candetalle;
			var seldetalle = document.getElementById(sel).value;
			var listaDetalles = document.getElementById(lista);
			var tr = document.getElementById(seldetalle);
			listaDetalles.removeChild(tr);
			document.getElementById(sel).value = "";
		}
		boton.disabled = false;
		//	actualizar montos de la obligacion
		actualizarMontosObligacion();
	}
	//	abrir listado partidas disponibles
	function abrirListadoPartidasDisponiblesObligacion() {
		var CodOrganismo = $("#CodOrganismo").val();
		var CodPresupuesto = $("#CodPresupuesto").val();
		var CodFuente = $("#CodFuente").val();
		var pag = "../lib/listas/listado_clasificador_presupuestario_disponible.php?filtrar=default&cod=cod_partida&nom=NomPartida&campo3=CodCuenta&campo4=NomCuenta&campo5=CodCuentaPub20&campo6=NomCuentaPub20&ventana=selListadoLista&seldetalle=sel_distribucion&CodOrganismo="+CodOrganismo+"&CodPresupuesto="+CodPresupuesto+"&CodFuente="+CodFuente+"&iframe=true&width=100%&height=400";
		$("#aSelPartida").attr("href", pag);
		validarAbrirLista('sel_distribucion', 'aSelPartida');
	}
	//	
	function verDisponibilidadPresupuestaria() {
		//	detalles_partida
		var detalles_partida = "";
		var frm_partidas = document.getElementById("frm_partidas");
		for(var i=0; n=frm_partidas.elements[i]; i++) {
			if (n.name == "cod_partida") detalles_partida += n.value + ";char:td;";
			else if (n.name == "Monto") detalles_partida += n.value + ";char:td;";
			else if (n.name == "MontoAjustado") detalles_partida += n.value + ";char:td;";
			else if (n.name == "MontoCompromiso") detalles_partida += n.value + ";char:td;";
			else if (n.name == "PreCompromiso") detalles_partida += n.value + ";char:td;";
			else if (n.name == "CotizacionesAsignadas") detalles_partida += n.value + ";char:td;";
			else if (n.name == "MontoDisponible") detalles_partida += n.value + ";char:td;";
			else if (n.name == "MontoDisponibleReal") detalles_partida += n.value + ";char:td;";
			else if (n.name == "MontoPendiente") detalles_partida += n.value + ";char:td;";
			else if (n.name == "partidasCodFuente") detalles_partida += n.value + ";char:td;";
			else if (n.name == "partidasCategoriaProg") detalles_partida += n.value + ";char:tr;";
		}
		var len = detalles_partida.length; len-=9;
		detalles_partida = detalles_partida.substr(0, len);
		//	
		var href = "gehen.php?anz=ap_obligacion_distribucion&detalles_partida="+detalles_partida+"&Anio="+$('#Anio').val()+"&CodOrganismo="+$('#CodOrganismo').val()+"&CodPresupuesto="+$('#CodPresupuesto').val()+"&opcion=<?=$opcion?>"+"&iframe=true&width=100%&height=430";
		$('#a_disponibilidad').attr('href', href);
		$('#a_disponibilidad').click();
	}
	//	
	function getMontoRetencion(CodImpuesto, id) {
		var MontoAfecto = new Number(setNumero($('#facturas_MontoAfecto'+id).val()));
		var MontoNoAfecto = new Number(setNumero($('#facturas_MontoNoAfecto'+id).val()));
		var MontoImpuesto = new Number(setNumero($('#facturas_MontoImpuesto'+id).val()));

		$.ajax({
			type: "POST",
			url: "ap_obligacion_ajax.php",
			data: "modulo=ajax&accion=getMontoRetencion&CodImpuesto="+CodImpuesto+"&MontoAfecto="+MontoAfecto+"&MontoNoAfecto="+MontoNoAfecto+"&MontoImpuesto="+MontoImpuesto,
			async: false,
			dataType: "json",
			success: function(data) {
				$('#facturas_FactorPorcentaje'+id).val(data['FactorPorcentaje']).formatCurrency();
				$('#facturas_MontoRetenido'+id).val(data['MontoRetenido']).formatCurrency();
			}
		});
	}
	//	
	function getMontoImpuesto(id) {
		var MontoAfecto = new Number(setNumero($('#facturas_MontoAfecto'+id).val()));
		var MontoNoAfecto = new Number(setNumero($('#facturas_MontoNoAfecto'+id).val()));
		var CodTipoServicio = $('#CodTipoServicio').val();

		$.ajax({
			type: "POST",
			url: "ap_obligacion_ajax.php",
			data: "modulo=ajax&accion=getMontoImpuesto&CodTipoServicio="+CodTipoServicio+"&MontoAfecto="+MontoAfecto,
			async: false,
			dataType: "json",
			success: function(data) {
				$('#facturas_MontoImpuesto'+id).val(data['MontoImpuesto']).formatCurrency();
				var MontoFactura = MontoAfecto + MontoNoAfecto + data['MontoImpuesto'];
				$('#facturas_MontoFactura'+id).val(MontoFactura).formatCurrency();
				getMontoRetencion($('#facturas_CodImpuesto'+id).val(), id);
			}
		});
	}
	//	
	function setMontoImpuesto(id) {
		var MontoAfecto = new Number(setNumero($('#facturas_MontoAfecto'+id).val()));
		var MontoNoAfecto = new Number(setNumero($('#facturas_MontoNoAfecto'+id).val()));
		var MontoImpuesto = new Number(setNumero($('#facturas_MontoImpuesto'+id).val()));
		var MontoFactura = MontoAfecto + MontoNoAfecto + MontoImpuesto;
		$('#facturas_MontoFactura'+id).val(MontoFactura).formatCurrency();
		getMontoRetencion($('#facturas_CodImpuesto'+id).val(), id);
	}
	//	
	function abrirListaAdelanto() {
		var CodProveedor = $('#CodProveedor').val();
		if (CodProveedor == '') {
			cajaModal('¡Debe seleccionar el proveedor!');
		} else {
			var href = "../lib/listas/gehen.php?anz=lista_ap_gastoadelanto&filtrar=default&fCodProveedor="+CodProveedor+"&ventana=obligacion_adelanto&detalle=adelantos&modulo=ajax&accion=adelantos_insertar&url=../../ap/ap_obligacion_ajax.php&iframe=true&width=100%&height=100%";
			$('#a_adelantos').attr('href', href);
			$('#a_adelantos').click();
		}
	}
	//	
	function setMontoAdelantos() {
		$('input[name="adelantos_CodAdelanto[]"]').each(function(idx) {
			var adelantos_MontoTotal = new Number($('input[name="adelantos_MontoTotal[]"]:eq('+idx+')').val());
			MontoAdelanto += adelantos_MontoTotal;
		});
		$('#MontoAdelanto').val(MontoAdelanto).formatCurrency();
		actualizarMontosObligacion();
	}
	//	
	$(document).ready(function() {
		<?php
		if ($opcion == "interfase-bono-nuevo") {
			?>
			//getOptionsSelect("<?=$field_obligacion['CodTipoDocumento']?>", 'tipo_servicio_documento', 'CodTipoServicio', true);
			//afectaTipoServicioObligacion($('#CodTipoServicio').val());
			<?php
		}
		?>
	});
</script>