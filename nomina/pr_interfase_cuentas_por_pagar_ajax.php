<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion-".$AnioActual.$MesActual.$DiaActual."_".$HorasActual.$MinutosActual.$SegundosActual.".sql", "w+");
//	$__archivo = fopen("_"."$modulo-$accion.sql", "w+");
///////////////////////////////////////////////////////////////////////////////
//	INTERFASE DE CUENTAS POR PAGAR (CALCULAR, CONSOLIDAR, VERIFICAR, GENERAR)
///////////////////////////////////////////////////////////////////////////////
//	interfase cuentas por pagar
if ($modulo == "interfase_cuentas_por_pagar") {
	//	calcular
	if ($accion == "calcular") {
		mysql_query("BEGIN");
		//	-----------------
    	$CodCentroCosto = getVar3("SELECT CodCentroCosto FROM ac_mastcentrocosto WHERE Codigo = '".$_PARAMETRO["CCOSTOCXP"]."'");
		list($AnioActual, $MesActual, $DiaActual) = split("[/.-]", substr($Ahora, 0, 10));
		$PeriodoActual = "$AnioActual-$MesActual";
		list($PeriodoAnio, $PeriodoMes) = split("[-]", $Periodo);
		$Ejercicio = $PeriodoAnio;
		//	consulto para obtener las tranmsferidas
		$filtro_transferidos1 = "";
		$filtro_transferidos2 = "";
		$filtro_transferidos3 = "";
		$sql = "SELECT po.CodProveedor, po.CodTipoDocumento
				FROM
					pr_obligaciones po
					INNER JOIN ap_obligaciones ao ON (po.CodProveedor = ao.CodProveedor AND
													  po.CodTipoDocumento = ao.CodTipoDocumento AND
													  po.NroDocumento = ao.NroDocumento)
				WHERE
					po.CodOrganismo = '".$CodOrganismo."' AND
					po.CodTipoNom = '".$CodTipoNom."' AND
					po.PeriodoNomina = '".$Periodo."' AND
					po.CodTipoProceso = '".$CodTipoProceso."' AND
					po.TipoObligacion = '".$TipoObligacion."' AND
					po.FlagTransferido = 'S' AND
					ao.Estado <> 'AN'";
		$field_proveedores = getRecords($sql);
		foreach($field_proveedores as $f) {
			if ($TipoObligacion == "01" || $TipoObligacion == "02") $filtro_transferidos1 .= " AND (tnec.CodPersona <> '".$f['CodProveedor']."')";
			if ($TipoObligacion == "03") $filtro_transferidos2 .= " AND ((c.CodPersona <> '".$f['CodProveedor']."' AND c.CodTipoDocumento = '".$f['CodTipoDocumento']."') OR (c.CodPersona = '".$f['CodProveedor']."' AND c.CodTipoDocumento <> '".$f['CodTipoDocumento']."')) ";
			if ($TipoObligacion == "04") $filtro_transferidos3 .= " AND (rj.Demandante <> '".$f['CodProveedor']."')";
		}
		//	consulto para eliminar las obligaciones de cxp y las no transferidas
		$sql = "(SELECT
					po.CodProveedor,
					po.CodTipoDocumento,
					po.NroDocumento
				 FROM
					pr_obligaciones po
					INNER JOIN ap_obligaciones ao ON (po.CodProveedor = ao.CodProveedor AND
													  po.CodTipoDocumento = ao.CodTipoDocumento AND
													  po.NroDocumento = ao.NroDocumento)
				 WHERE
					po.CodOrganismo = '".$CodOrganismo."' AND
					po.CodTipoNom = '".$CodTipoNom."' AND
					po.PeriodoNomina = '".$Periodo."' AND
					po.CodTipoProceso = '".$CodTipoProceso."' AND
					po.TipoObligacion = '".$TipoObligacion."' AND
					po.FlagTransferido = 'S' AND
					ao.Estado = 'AN')
				UNION
				(SELECT
					po.CodProveedor,
					po.CodTipoDocumento,
					po.NroDocumento
				 FROM pr_obligaciones po
				 WHERE
					po.CodOrganismo = '".$CodOrganismo."' AND
					po.CodTipoNom = '".$CodTipoNom."' AND
					po.PeriodoNomina = '".$Periodo."' AND
					po.CodTipoProceso = '".$CodTipoProceso."' AND
					po.TipoObligacion = '".$TipoObligacion."' AND
					po.FlagTransferido = 'N')";
		$field_obligacionescxp = getRecords($sql);
		foreach($field_obligacionescxp as $f) {
			//	obligacion
			$sql = "DELETE FROM ap_obligaciones
					WHERE
						CodProveedor = '".$f['CodProveedor']."' AND
						CodTipoDocumento = '".$f['CodTipoDocumento']."' AND
						NroDocumento = '".$f['NroDocumento']."'";
			execute($sql);
			//	cuentas
			$sql = "DELETE FROM ap_obligacionescuenta
					WHERE
						CodProveedor = '".$f['CodProveedor']."' AND
						CodTipoDocumento = '".$f['CodTipoDocumento']."' AND
						NroDocumento = '".$f['NroDocumento']."'";
			execute($sql);
			//	impuestos/retenciones
			$sql = "DELETE FROM ap_obligacionesimpuesto
					WHERE
						CodProveedor = '".$f['CodProveedor']."' AND
						CodTipoDocumento = '".$f['CodTipoDocumento']."' AND
						NroDocumento = '".$f['NroDocumento']."'";
			execute($sql);
			//	causados
			$sql = "DELETE FROM ap_distribucionobligacion
					WHERE
						CodProveedor = '".$f['CodProveedor']."' AND
						CodTipoDocumento = '".$f['CodTipoDocumento']."' AND
						NroDocumento = '".$f['NroDocumento']."'";
			execute($sql);
			//	compromisos
			$sql = "DELETE FROM lg_distribucioncompromisos
					WHERE
						CodProveedor = '".$f['CodProveedor']."' AND
						CodTipoDocumento = '".$f['CodTipoDocumento']."' AND
						NroDocumento = '".$f['NroDocumento']."'";
			execute($sql);
			//	elimino las obligaciones
			$sql = "DELETE FROM pr_obligaciones
					WHERE
						CodProveedor = '".$f['CodProveedor']."' AND
						CodTipoDocumento = '".$f['CodTipoDocumento']."' AND
						NroDocumento = '".$f['NroDocumento']."'";
			execute($sql);
			//	elimino las obligaciones x cuentas
			$sql = "DELETE FROM pr_obligacionescuenta
					WHERE
						CodProveedor = '".$f['CodProveedor']."' AND
						CodTipoDocumento = '".$f['CodTipoDocumento']."' AND
						NroDocumento = '".$f['NroDocumento']."'";
			execute($sql);
			//	elimino las obligaciones x retenciones
			$sql = "DELETE FROM pr_obligacionesretenciones
					WHERE
						CodProveedor = '".$f['CodProveedor']."' AND
						CodTipoDocumento = '".$f['CodTipoDocumento']."' AND
						NroDocumento = '".$f['NroDocumento']."'";
			execute($sql);
			//	actualizo el payroll
			$sql = "UPDATE pr_tiponominaempleado
					SET EstadoPago = 'PE'
					WHERE
						CodProveedor = '".$f['CodProveedor']."' AND
						CodTipoDocumento = '".$f['CodTipoDocumento']."' AND
						NroDocumento = '".$f['NroDocumento']."'";
			execute($sql);
		}
		##	tipo de proceso
		$sql = "SELECT Descripcion FROM pr_tipoproceso WHERE CodTipoProceso = '".$CodTipoProceso."'";
		$NomTipoProceso = getVar3($sql);
		//	obtengo el tipo de documento
		$sql = "SELECT CodTipoDocumento
				FROM pr_tiponominaproceso
				WHERE
					CodTipoNom = '".$CodTipoNom."' AND
					CodTipoProceso = '".$CodTipoProceso."'";
		$CodTipoDocumento = getVar3($sql);
		if ($CodTipoDocumento == '') die("No se encontró un <strong>Tipo de Documento</strong> asociado al Proceso:<br /><strong>($CodTipoProceso) $NomTipoProceso</strong>");
		//	obtengo la obligaciones a insertar
		if ($_PARAMETRO['INTERFASEAP'] == "S") {
			if ($TipoObligacion == "01" || $TipoObligacion == "02") {
				if ($TipoObligacion == "01") $CodTipoPago = "01";
				elseif ($TipoObligacion == "02") $CodTipoPago = "02";
				$sql = "SELECT
							tn.Nomina,
							tp.Descripcion AS NomProceso,
							mp1.CodPersona AS CodProveedor,
							mp1.NomCompleto AS NomProveedor,
							'".$CodTipoDocumento."' AS CodTipoDocumento,
							me.CodTipoPago,
							'".$_PARAMETRO['TIPOSERVCXP']."' AS CodTipoServicio,
							SUM(tnec.Monto) AS MontoIngreso,
							'".$_PARAMETRO['CTANOMINA']."' AS CodCuenta,
							'".$_PARAMETRO['CTANOMINAPUB20']."' AS CodCuentaPub20,
							'S' AS FlagCompromiso,
							'S' AS FlagPresupuesto,
							'N' AS FlagDistribucionManual,
							ppto.CodPresupuesto,
							ppto.Ejercicio
						FROM
							pr_tiponominaempleadoconcepto tnec
							INNER JOIN pr_tiponominaempleado tne ON (tne.CodTipoNom = tnec.CodTipoNom AND
																	 tne.Periodo = tnec.Periodo AND
																	 tne.CodOrganismo = tnec.CodOrganismo AND
																	 tne.CodTipoProceso = tnec.CodTipoProceso AND
																	 tne.CodPersona = tnec.CodPersona)
							INNER JOIN tiponomina tn ON (tnec.CodTipoNom = tn.CodTipoNom)
							INNER JOIN pr_tipoproceso tp ON (tnec.CodTipoProceso = tp.CodTipoProceso)
							INNER JOIN mastorganismos o ON (tnec.CodOrganismo = o.CodOrganismo)
							INNER JOIN mastpersonas mp1 ON (tnec.CodPersona = mp1.CodPersona)
							INNER JOIN mastempleado me ON (mp1.CodPersona = me.CodPersona)
							INNER JOIN mastpersonas mp2 ON (o.CodPersona = mp2.CodPersona)
							INNER JOIN mastproveedores p ON (mp2.CodPersona = p.CodProveedor)    
							INNER JOIN pr_concepto c ON (tnec.CodConcepto = c.CodConcepto)
							INNER JOIN pr_conceptoperfil cp ON (tn.CodPerfilConcepto = cp.CodPerfilConcepto)
							INNER JOIN pr_conceptoperfildetalle cpd ON (cp.CodPerfilConcepto = cpd.CodPerfilConcepto AND
																		tnec.CodTipoProceso = cpd.CodTipoProceso AND
																		tnec.CodConcepto = cpd.CodConcepto)
							LEFT JOIN pv_presupuesto ppto ON (ppto.CodOrganismo = tnec.CodOrganismo AND ppto.CategoriaProg = me.CategoriaProg AND ppto.Ejercicio = '".$Ejercicio."')
						WHERE
							tnec.CodTipoNom = '".$CodTipoNom."' AND
							tnec.Periodo = '".$Periodo."' AND
							tnec.CodOrganismo = '".$CodOrganismo."' AND
							tnec.CodTipoProceso = '".$CodTipoProceso."' AND
							tne.EstadoPago = 'PE' AND
							me.CodTipoPago = '".$CodTipoPago."' AND
							c.Tipo = 'I' $filtro_transferidos1
						GROUP BY tnec.CodPersona";
			}
			elseif ($TipoObligacion == "03") {
				$sql = "SELECT
							tn.Nomina,
							tp.Descripcion AS NomProceso,
							c.CodPersona AS CodProveedor,
							mp2.NomCompleto AS NomProveedor,
							c.CodTipoDocumento,
							p.CodTipoPago,
							'".$_PARAMETRO['TIPOSERVCXP']."' AS CodTipoServicio,
							SUM(tnec.Monto) AS MontoIngreso,
							cpd.CuentaHaber AS CodCuenta,
							cpd.CuentaHaberPub20 AS CodCuentaPub20,
							'S' AS FlagCompromiso,
							'S' AS FlagPresupuesto,
							'N' AS FlagDistribucionManual,
							ppto.CodPresupuesto,
							ppto.Ejercicio
						FROM
							pr_tiponominaempleadoconcepto tnec
							INNER JOIN pr_tiponominaempleado tne ON (tne.CodTipoNom = tnec.CodTipoNom AND
																	 tne.Periodo = tnec.Periodo AND
																	 tne.CodOrganismo = tnec.CodOrganismo AND
																	 tne.CodTipoProceso = tnec.CodTipoProceso AND
																	 tne.CodPersona = tnec.CodPersona)
							INNER JOIN tiponomina tn ON (tnec.CodTipoNom = tn.CodTipoNom)
							INNER JOIN pr_tipoproceso tp ON (tnec.CodTipoProceso = tp.CodTipoProceso)
							INNER JOIN mastorganismos o ON (tnec.CodOrganismo = o.CodOrganismo)
							INNER JOIN mastpersonas mp1 ON (tnec.CodPersona = mp1.CodPersona)
							INNER JOIN mastempleado me ON (mp1.CodPersona = me.CodPersona)
							INNER JOIN pr_concepto c ON (tnec.CodConcepto = c.CodConcepto)
							INNER JOIN pr_conceptoperfil cp ON (tn.CodPerfilConcepto = cp.CodPerfilConcepto)
							INNER JOIN pr_conceptoperfildetalle cpd ON (cp.CodPerfilConcepto = cpd.CodPerfilConcepto AND
																		tnec.CodTipoProceso = cpd.CodTipoProceso AND
																		tnec.CodConcepto = cpd.CodConcepto)
							LEFT JOIN pv_presupuesto ppto ON (ppto.CodOrganismo = tnec.CodOrganismo AND ppto.CategoriaProg = me.CategoriaProg AND ppto.Ejercicio = '".$Ejercicio."')
							INNER JOIN mastpersonas mp2 ON (c.CodPersona = mp2.CodPersona)
							INNER JOIN mastproveedores p ON (mp2.CodPersona = p.CodProveedor)
						WHERE
							tnec.CodTipoNom = '".$CodTipoNom."' AND
							tnec.Periodo = '".$Periodo."' AND
							tnec.CodOrganismo = '".$CodOrganismo."' AND
							tnec.CodTipoProceso = '".$CodTipoProceso."' AND
							c.Tipo = 'A' $filtro_transferidos2
						GROUP BY c.CodPersona, c.CodTipoDocumento";
			}
			elseif ($TipoObligacion == "04") {
				$sql = "SELECT
							tn.Nomina,
							tp.Descripcion AS NomProceso,
							rj.Demandante AS CodProveedor,
							mp2.NomCompleto AS NomProveedor,
							'".$_PARAMETRO['TIPODOCCXP']."' AS CodTipoDocumento,
							p.CodTipoPago,
							'".$_PARAMETRO['TIPOSERVCXP']."' AS CodTipoServicio,
							SUM(tnec.Monto) AS MontoIngreso,
							'".$_PARAMETRO['CTANOMINA']."' AS CodCuenta,
							'".$_PARAMETRO['CTANOMINAPUB20']."' AS CodCuentaPub20,
							'N' AS FlagCompromiso,
							'N' AS FlagPresupuesto,
							'S' AS FlagDistribucionManual,
							ppto.CodPresupuesto,
							ppto.Ejercicio
						FROM
							pr_tiponominaempleadoconcepto tnec
							INNER JOIN pr_tiponominaempleado tne ON (tne.CodTipoNom = tnec.CodTipoNom AND
																	 tne.Periodo = tnec.Periodo AND
																	 tne.CodOrganismo = tnec.CodOrganismo AND
																	 tne.CodTipoProceso = tnec.CodTipoProceso AND
																	 tne.CodPersona = tnec.CodPersona)
							INNER JOIN tiponomina tn ON (tnec.CodTipoNom = tn.CodTipoNom)
							INNER JOIN pr_tipoproceso tp ON (tnec.CodTipoProceso = tp.CodTipoProceso)
							INNER JOIN mastorganismos o ON (tnec.CodOrganismo = o.CodOrganismo)
							INNER JOIN mastpersonas mp1 ON (tnec.CodPersona = mp1.CodPersona)
							INNER JOIN mastempleado me ON (mp1.CodPersona = me.CodPersona)
							INNER JOIN rh_retencionjudicial rj ON (tnec.CodPersona = rj.CodPersona AND
																   tnec.CodOrganismo = rj.CodOrganismo)
							INNER JOIN rh_retencionjudicialconceptos rjc ON (rj.CodRetencion = rjc.CodRetencion AND
																			 tnec.CodOrganismo = rjc.CodOrganismo AND
																			 tnec.CodConcepto = rjc.CodConcepto)
							INNER JOIN mastpersonas mp2 ON (rj.Demandante = mp2.CodPersona)
							INNER JOIN mastproveedores p ON (mp2.CodPersona = p.CodProveedor)
							INNER JOIN pr_concepto c ON (tnec.CodConcepto = c.CodConcepto)
							INNER JOIN pr_conceptoperfil cp ON (tn.CodPerfilConcepto = cp.CodPerfilConcepto)
							INNER JOIN pr_conceptoperfildetalle cpd ON (cp.CodPerfilConcepto = cpd.CodPerfilConcepto AND
																		tnec.CodTipoProceso = cpd.CodTipoProceso AND
																		tnec.CodConcepto = cpd.CodConcepto)
							LEFT JOIN pv_presupuesto ppto ON (ppto.CodOrganismo = tnec.CodOrganismo AND ppto.CategoriaProg = me.CategoriaProg AND ppto.Ejercicio = '".$Ejercicio."')
						WHERE
							tnec.CodTipoNom = '".$CodTipoNom."' AND
							tnec.Periodo = '".$Periodo."' AND
							tnec.CodOrganismo = '".$CodOrganismo."' AND
							tnec.CodTipoProceso = '".$CodTipoProceso."' $filtro_transferidos
						GROUP BY rj.Demandante";
			}
		}
		$field_obligaciones = getRecords($sql);
		foreach($field_obligaciones as $f) {
			$MontoDescuento = 0;
			$MontoRetencion = 0;
			//	obtengo retenciones y descuentos
			if ($TipoObligacion == "01" || $TipoObligacion == "02") {
				//	descuento
				$sql = "SELECT SUM(tnec2.Monto) AS MontoDescuento
						FROM
							pr_tiponominaempleadoconcepto tnec2
							INNER JOIN tiponomina tn2 ON (tnec2.CodTipoNom = tn2.CodTipoNom)
							INNER JOIN pr_tipoproceso tp2 ON (tnec2.CodTipoProceso = tp2.CodTipoProceso)
							INNER JOIN mastorganismos o2 ON (tnec2.CodOrganismo = o2.CodOrganismo)
							INNER JOIN mastpersonas mp21 ON (tnec2.CodPersona = mp21.CodPersona)
							INNER JOIN mastempleado me2 ON (mp21.CodPersona = me2.CodPersona)
							INNER JOIN mastpersonas mp22 ON (o2.CodPersona = mp22.CodPersona)
							INNER JOIN mastproveedores p2 ON (mp22.CodPersona = p2.CodProveedor)
							INNER JOIN pr_concepto c2 ON (tnec2.CodConcepto = c2.CodConcepto)
							INNER JOIN pr_conceptoperfil cp2 ON (tn2.CodPerfilConcepto = cp2.CodPerfilConcepto)
							INNER JOIN pr_conceptoperfildetalle cpd2 ON (cp2.CodPerfilConcepto = cpd2.CodPerfilConcepto AND
																		 tnec2.CodTipoProceso = cpd2.CodTipoProceso AND
																		 tnec2.CodConcepto = cpd2.CodConcepto)
						WHERE
							me2.CodTipoPago = '".$CodTipoPago."' AND
							tnec2.CodTipoNom = '".$CodTipoNom."' AND
							tnec2.Periodo = '".$Periodo."' AND
							tnec2.CodOrganismo = '".$CodOrganismo."' AND
							tnec2.CodTipoProceso = '".$CodTipoProceso."' AND
							tnec2.CodPersona = '".$f['CodProveedor']."' AND
							c2.Tipo = 'D' AND
							c2.FlagRetencion = 'N'
						GROUP BY tnec2.CodOrganismo";
				$MontoDescuento = getVar3($sql);
				//	retenciones
				$sql = "SELECT SUM(tnec2.Monto) AS MontoRetencion
						FROM
							pr_tiponominaempleadoconcepto tnec2
							INNER JOIN tiponomina tn2 ON (tnec2.CodTipoNom = tn2.CodTipoNom)
							INNER JOIN pr_tipoproceso tp2 ON (tnec2.CodTipoProceso = tp2.CodTipoProceso)
							INNER JOIN mastorganismos o2 ON (tnec2.CodOrganismo = o2.CodOrganismo)
							INNER JOIN mastpersonas mp21 ON (tnec2.CodPersona = mp21.CodPersona)
							INNER JOIN mastempleado me2 ON (mp21.CodPersona = me2.CodPersona)
							INNER JOIN mastpersonas mp22 ON (o2.CodPersona = mp22.CodPersona)
							INNER JOIN mastproveedores p2 ON (mp22.CodPersona = p2.CodProveedor)
							INNER JOIN pr_concepto c2 ON (tnec2.CodConcepto = c2.CodConcepto)
							INNER JOIN pr_conceptoperfil cp2 ON (tn2.CodPerfilConcepto = cp2.CodPerfilConcepto)
							INNER JOIN pr_conceptoperfildetalle cpd2 ON (cp2.CodPerfilConcepto = cpd2.CodPerfilConcepto AND
																		 tnec2.CodTipoProceso = cpd2.CodTipoProceso AND
																		 tnec2.CodConcepto = cpd2.CodConcepto)
						WHERE
							me2.CodTipoPago = '".$CodTipoPago."' AND
							tnec2.CodTipoNom = '".$CodTipoNom."' AND
							tnec2.Periodo = '".$Periodo."' AND
							tnec2.CodOrganismo = '".$CodOrganismo."' AND
							tnec2.CodTipoProceso = '".$CodTipoProceso."' AND
							tnec2.CodPersona = '".$f['CodProveedor']."' AND
							c2.Tipo = 'D' AND
							c2.FlagRetencion = 'S'
						GROUP BY tnec2.CodOrganismo";
				$MontoRetencion = getVar3($sql);
			}
			//	obtengo algunos valores a insertar
			$NroDocumento = $CodOrganismo.$PeriodoAnio.$PeriodoMes.$CodTipoNom.$CodTipoProceso.$TipoObligacion;
			##	valido nro de documento
			$sql = "SELECT COUNT(*)
					FROM ap_obligaciones
					WHERE
						CodProveedor = '".$f['CodProveedor']."' AND
						CodTipoDocumento = '".$f['CodTipoDocumento']."' AND
						NroDocumento LIKE '".$NroDocumento."%'";
			$_nro = getVar3($sql);
			if ($_nro > 0) $NroDocumento .= "-".(++$_nro);
			##
			$NroCuenta = getCuentaBancariaDefault($CodOrganismo, $f['CodTipoPago']);
			$Comentarios = "PERIODO $Periodo NOMINA DE $f[Nomina] $f[NomProceso]";
			$MontoNoAfecto = $f['MontoIngreso'] - $MontoDescuento;
			$MontoObligacion = $MontoNoAfecto - $MontoRetencion;
			//	inserto la obligacion
			$sql = "INSERT INTO pr_obligaciones
					SET
						TipoObligacion = '".$TipoObligacion."',
						CodOrganismo = '".$CodOrganismo."',
						CodTipoNom = '".$CodTipoNom."',
						Periodo = '".$PeriodoActual."',
						PeriodoNomina = '".$Periodo."',
						CodTipoProceso = '".$CodTipoProceso."',
						CodProveedor = '".$f['CodProveedor']."',
						CodTipoDocumento = '".$f['CodTipoDocumento']."',
						NroDocumento = '".$NroDocumento."',
						NroControl = '".$NroDocumento."',
						NroCuenta = '".$NroCuenta."',
						CodTipoPago = '".$f['CodTipoPago']."',
						CodTipoServicio = '".$f['CodTipoServicio']."',
						FechaRegistro = NOW(),
						CodProveedorPagar = '".$f['CodProveedor']."',
						NomProveedorPagar = '".$f['NomProveedor']."',
						Comentarios = '".$Comentarios."',
						ComentariosAdicional = '".$Comentarios."',
						MontoObligacion = '".$MontoObligacion."',
						MontoNoAfecto = '".$MontoNoAfecto."',
						MontoImpuestoOtros = '".abs(($MontoNoAfecto-$MontoObligacion))."',
						CodCuenta = '".$f['CodCuenta']."',
						CodCuentaPub20 = '".$f['CodCuentaPub20']."',
						CodCentroCosto = '".$CodCentroCosto."',
						FlagCompromiso = '".$f['FlagCompromiso']."',
						FlagPresupuesto = '".$f['FlagPresupuesto']."',
						FlagDistribucionManual = '".$f['FlagDistribucionManual']."',
						CodPresupuesto = '".$f['CodPresupuesto']."',
						CodFuente = '".$_PARAMETRO['FFMETASDEF']."',
						UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
						UltimaFecha = NOW()";
			execute($sql);
			//	actualizo el payroll
			$sql = "UPDATE pr_tiponominaempleado
					SET
						CodProveedor = '".$f['CodProveedor']."',
						CodTipoDocumento = '".$f['CodTipoDocumento']."',
						NroDocumento = '".$NroDocumento."'
					WHERE
						CodTipoNom = '".$CodTipoNom."' AND
						Periodo = '".$Periodo."' AND
						CodPersona = '".$f['CodProveedor']."' AND
						CodOrganismo = '".$CodOrganismo."' AND
						CodTipoProceso = '".$CodTipoProceso."'";
			execute($sql);
			if ($CodTipoProceso == 'PRS') {
				//	actualizo prestaciones
				$sql = "UPDATE pr_liquidacionempleado
						SET
							CodProveedor = '".$f['CodProveedor']."',
							CodTipoDocumento = '".$f['CodTipoDocumento']."',
							NroDocumento = '".$NroDocumento."'
						WHERE
							CodPersona = '".$f['CodProveedor']."' AND
							Periodo = '".$Periodo."'";
				execute($sql);
			}
		}
		//	consulto las partidas a insertar
		if ($TipoObligacion == "01" || $TipoObligacion == "02") {
			$sql = "SELECT
						mp1.CodPersona AS CodProveedor,
						'".$CodTipoDocumento."' AS CodTipoDocumento,
						'01' AS Ficha,
						SUM(tnec.Monto) AS MontoIngreso,
						cpd.cod_partida,
						pv.CodCuenta,
						cpd.CuentaDebe,
						cpd.CuentaHaber,
						pv.CodCuentaPub20,
						cpd.CuentaDebePub20,
						cpd.CuentaHaberPub20,
							me.CategoriaProg AS CategoriaProgEmpleado,
							cpd.FlagCategoriaProg,
							pv1.CodPresupuesto AS CodPresupuestoEmpleado,
							cpd.CategoriaProg AS CategoriaProgConcepto,
							pv2.CodPresupuesto AS CodPresupuestoConcepto,
							tnec.CodOrganismo
					FROM
						pr_tiponominaempleadoconcepto tnec
						INNER JOIN pr_tiponominaempleado tne ON (tne.CodTipoNom = tnec.CodTipoNom AND
																 tne.Periodo = tnec.Periodo AND
																 tne.CodOrganismo = tnec.CodOrganismo AND
																 tne.CodTipoProceso = tnec.CodTipoProceso AND
																 tne.CodPersona = tnec.CodPersona)
						INNER JOIN tiponomina tn ON (tnec.CodTipoNom = tn.CodTipoNom)
						INNER JOIN pr_tipoproceso tp ON (tnec.CodTipoProceso = tp.CodTipoProceso)
						INNER JOIN mastorganismos o ON (tnec.CodOrganismo = o.CodOrganismo)
						INNER JOIN mastpersonas mp1 ON (tnec.CodPersona = mp1.CodPersona)
						INNER JOIN mastempleado me ON (mp1.CodPersona = me.CodPersona)
						INNER JOIN mastpersonas mp2 ON (o.CodPersona = mp2.CodPersona)
						INNER JOIN mastproveedores p ON (mp2.CodPersona = p.CodProveedor)    
						INNER JOIN pr_concepto c ON (tnec.CodConcepto = c.CodConcepto)
						INNER JOIN pr_conceptoperfil cp ON (tn.CodPerfilConcepto = cp.CodPerfilConcepto)
						INNER JOIN pr_conceptoperfildetalle cpd ON (cp.CodPerfilConcepto = cpd.CodPerfilConcepto AND
																	tnec.CodTipoProceso = cpd.CodTipoProceso AND
																	tnec.CodConcepto = cpd.CodConcepto)
						LEFT JOIN pv_partida pv ON (cpd.cod_partida = pv.cod_partida)
						LEFT JOIN pv_presupuesto pv1 ON (pv1.CodOrganismo = me.CodOrganismo AND pv1.CategoriaProg = me.CategoriaProg AND pv1.Ejercicio = '$Ejercicio')
						LEFT JOIN pv_presupuestodet pvd1 ON (pvd1.CodOrganismo = pv1.CodOrganismo AND pvd1.CodPresupuesto = pv1.CodPresupuesto AND pvd1.cod_partida = cpd.cod_partida AND pvd1.CodFuente = '".$_PARAMETRO['FFMETASDEF']."')
						LEFT JOIN pv_presupuesto pv2 ON (pv2.CodOrganismo = me.CodOrganismo AND pv2.CategoriaProg = cpd.CategoriaProg AND pv2.Ejercicio = '$Ejercicio')
						LEFT JOIN pv_presupuestodet pvd2 ON (pvd2.CodOrganismo = pv2.CodOrganismo AND pvd2.CodPresupuesto = pv2.CodPresupuesto AND pvd2.cod_partida = cpd.cod_partida AND pvd1.CodFuente = '".$_PARAMETRO['FFMETASDEF']."')
					WHERE
						tnec.CodTipoNom = '".$CodTipoNom."' AND
						tnec.Periodo = '".$Periodo."' AND
						tnec.CodOrganismo = '".$CodOrganismo."' AND
						tnec.CodTipoProceso = '".$CodTipoProceso."' AND
						tne.EstadoPago = 'PE' AND
						me.CodTipoPago = '".$CodTipoPago."' AND
						c.Tipo = 'I' $filtro_transferidos1 $filtro_personas
					GROUP BY tnec.CodPersona, tnec.CodOrganismo, CodPresupuestoEmpleado, CodPresupuestoConcepto, cpd.cod_partida";
		}
		elseif ($TipoObligacion == "03") {
			$sql = "SELECT
						c.CodPersona AS CodProveedor,
						c.CodTipoDocumento,
						'03' AS Ficha,
						SUM(tnec.Monto) AS MontoIngreso,
						cpd.cod_partida,
						pv.CodCuenta,
						cpd.CuentaDebe,
						cpd.CuentaHaber,
						pv.CodCuentaPub20,
						cpd.CuentaDebePub20,
						cpd.CuentaHaberPub20,
							me.CategoriaProg AS CategoriaProgEmpleado,
							cpd.FlagCategoriaProg,
							pv1.CodPresupuesto AS CodPresupuestoEmpleado,
							cpd.CategoriaProg AS CategoriaProgConcepto,
							pv2.CodPresupuesto AS CodPresupuestoConcepto,
							tnec.CodOrganismo
					FROM
						pr_tiponominaempleadoconcepto tnec
						INNER JOIN tiponomina tn ON (tnec.CodTipoNom = tn.CodTipoNom)
						INNER JOIN pr_tipoproceso tp ON (tnec.CodTipoProceso = tp.CodTipoProceso)
						INNER JOIN mastorganismos o ON (tnec.CodOrganismo = o.CodOrganismo)
						INNER JOIN mastpersonas mp1 ON (tnec.CodPersona = mp1.CodPersona)
						INNER JOIN mastempleado me ON (mp1.CodPersona = me.CodPersona)
						INNER JOIN pr_concepto c ON (tnec.CodConcepto = c.CodConcepto)
						INNER JOIN pr_conceptoperfil cp ON (tn.CodPerfilConcepto = cp.CodPerfilConcepto)
						INNER JOIN pr_conceptoperfildetalle cpd ON (cp.CodPerfilConcepto = cpd.CodPerfilConcepto AND
																	tnec.CodTipoProceso = cpd.CodTipoProceso AND
																	tnec.CodConcepto = cpd.CodConcepto)
						INNER JOIN mastpersonas mp2 ON (c.CodPersona = mp2.CodPersona)
						INNER JOIN mastproveedores p ON (mp2.CodPersona = p.CodProveedor)
						LEFT JOIN pv_partida pv ON (cpd.cod_partida = pv.cod_partida)
						LEFT JOIN pv_presupuesto pv1 ON (pv1.CodOrganismo = me.CodOrganismo AND pv1.CategoriaProg = me.CategoriaProg AND pv1.Ejercicio = '$Ejercicio')
						LEFT JOIN pv_presupuestodet pvd1 ON (pvd1.CodOrganismo = pv1.CodOrganismo AND pvd1.CodPresupuesto = pv1.CodPresupuesto AND pvd1.cod_partida = cpd.cod_partida AND pvd1.CodFuente = '".$_PARAMETRO['FFMETASDEF']."')
						LEFT JOIN pv_presupuesto pv2 ON (pv2.CodOrganismo = me.CodOrganismo AND pv2.CategoriaProg = cpd.CategoriaProg AND pv2.Ejercicio = '$Ejercicio')
						LEFT JOIN pv_presupuestodet pvd2 ON (pvd2.CodOrganismo = pv2.CodOrganismo AND pvd2.CodPresupuesto = pv2.CodPresupuesto AND pvd2.cod_partida = cpd.cod_partida AND pvd1.CodFuente = '".$_PARAMETRO['FFMETASDEF']."')
					WHERE
						tnec.CodTipoNom = '".$CodTipoNom."' AND
						tnec.Periodo = '".$Periodo."' AND
						tnec.CodOrganismo = '".$CodOrganismo."' AND
						tnec.CodTipoProceso = '".$CodTipoProceso."' AND
						c.Tipo = 'A' $filtro_transferidos2 $filtro_personas
					GROUP BY c.CodPersona, tnec.CodOrganismo, CodPresupuestoEmpleado, CodPresupuestoConcepto, cpd.cod_partida";
		}
		elseif ($TipoObligacion == "04") {
			$sql = "SELECT
						rj.Demandante AS CodProveedor,
						'".$_PARAMETRO['TIPODOCCXP']."' AS CodTipoDocumento,
						SUM(tnec.Monto) AS MontoIngreso,
						cpd.cod_partida,
						pv.CodCuenta,
						cpd.CuentaDebe,
						cpd.CuentaHaber,
						pv.CodCuentaPub20,
						cpd.CuentaDebePub20,
						cpd.CuentaHaberPub20,
							me.CategoriaProg AS CategoriaProgEmpleado,
							cpd.FlagCategoriaProg,
							pv1.CodPresupuesto AS CodPresupuestoEmpleado,
							cpd.CategoriaProg AS CategoriaProgConcepto,
							pv2.CodPresupuesto AS CodPresupuestoConcepto,
							tnec.CodOrganismo
					FROM
						pr_tiponominaempleadoconcepto tnec
						INNER JOIN tiponomina tn ON (tnec.CodTipoNom = tn.CodTipoNom)
						INNER JOIN pr_tipoproceso tp ON (tnec.CodTipoProceso = tp.CodTipoProceso)
						INNER JOIN mastorganismos o ON (tnec.CodOrganismo = o.CodOrganismo)
						INNER JOIN mastpersonas mp1 ON (tnec.CodPersona = mp1.CodPersona)
						INNER JOIN mastempleado me ON (mp1.CodPersona = me.CodPersona)
						INNER JOIN rh_retencionjudicial rj ON (tnec.CodPersona = rj.CodPersona AND
															   tnec.CodOrganismo = rj.CodOrganismo)
						INNER JOIN rh_retencionjudicialconceptos rjc ON (rj.CodRetencion = rjc.CodRetencion AND
																		 tnec.CodOrganismo = rjc.CodOrganismo AND
																		 tnec.CodConcepto = rjc.CodConcepto)
						INNER JOIN mastpersonas mp2 ON (rj.Demandante = mp2.CodPersona)
						INNER JOIN mastproveedores p ON (mp2.CodPersona = p.CodProveedor)
						INNER JOIN pr_concepto c ON (tnec.CodConcepto = c.CodConcepto)
						INNER JOIN pr_conceptoperfil cp ON (tn.CodPerfilConcepto = cp.CodPerfilConcepto)
						INNER JOIN pr_conceptoperfildetalle cpd ON (cp.CodPerfilConcepto = cpd.CodPerfilConcepto AND
																	tnec.CodTipoProceso = cpd.CodTipoProceso AND
																	tnec.CodConcepto = cpd.CodConcepto)
						LEFT JOIN pv_partida pv ON (cpd.cod_partida = pv.cod_partida)
						LEFT JOIN pv_presupuesto pv1 ON (pv1.CodOrganismo = me.CodOrganismo AND pv1.CategoriaProg = me.CategoriaProg AND pv1.Ejercicio = '$Ejercicio')
						LEFT JOIN pv_presupuestodet pvd1 ON (pvd1.CodOrganismo = pv1.CodOrganismo AND pvd1.CodPresupuesto = pv1.CodPresupuesto AND pvd1.cod_partida = cpd.cod_partida AND pvd1.CodFuente = '".$_PARAMETRO['FFMETASDEF']."')
						LEFT JOIN pv_presupuesto pv2 ON (pv2.CodOrganismo = me.CodOrganismo AND pv2.CategoriaProg = cpd.CategoriaProg AND pv2.Ejercicio = '$Ejercicio')
						LEFT JOIN pv_presupuestodet pvd2 ON (pvd2.CodOrganismo = pv2.CodOrganismo AND pvd2.CodPresupuesto = pv2.CodPresupuesto AND pvd2.cod_partida = cpd.cod_partida AND pvd1.CodFuente = '".$_PARAMETRO['FFMETASDEF']."')
					WHERE
						tnec.CodTipoNom = '".$CodTipoNom."' AND
						tnec.Periodo = '".$Periodo."' AND
						tnec.CodOrganismo = '".$CodOrganismo."' AND
						tnec.CodTipoProceso = '".$CodTipoProceso."' $filtro_transferidos3 $filtro_personas
					GROUP BY rj.Demandante, tnec.CodOrganismo, CodPresupuestoEmpleado, CodPresupuestoConcepto, cpd.cod_partida";
		}
		$field_partidas = getRecords($sql);	$Linea = 0;
		foreach($field_partidas as $f) {	$Linea++;
			$MontoDescuento = 0;
			$MontoAdelanto = 0;
			//	obtengo retenciones y descuentos
			if ($TipoObligacion == "01" || $TipoObligacion == "02") {
				//	descuento
				$sql = "SELECT SUM(tnec2.Monto) AS MontoDescuento
						FROM
							pr_tiponominaempleadoconcepto tnec2
							INNER JOIN tiponomina tn2 ON (tnec2.CodTipoNom = tn2.CodTipoNom)
							INNER JOIN pr_tipoproceso tp2 ON (tnec2.CodTipoProceso = tp2.CodTipoProceso)
							INNER JOIN mastorganismos o2 ON (tnec2.CodOrganismo = o2.CodOrganismo)
							INNER JOIN mastpersonas mp21 ON (tnec2.CodPersona = mp21.CodPersona)
							INNER JOIN mastempleado me2 ON (mp21.CodPersona = me2.CodPersona)
							INNER JOIN mastpersonas mp22 ON (o2.CodPersona = mp22.CodPersona)
							INNER JOIN mastproveedores p2 ON (mp22.CodPersona = p2.CodProveedor)
							INNER JOIN pr_concepto c2 ON (tnec2.CodConcepto = c2.CodConcepto)
							INNER JOIN pr_conceptoperfil cp2 ON (tn2.CodPerfilConcepto = cp2.CodPerfilConcepto)
							INNER JOIN pr_conceptoperfildetalle cpd2 ON (cp2.CodPerfilConcepto = cpd2.CodPerfilConcepto AND
																		 tnec2.CodTipoProceso = cpd2.CodTipoProceso AND
																		 tnec2.CodConcepto = cpd2.CodConcepto)
						WHERE
							tnec2.CodTipoNom = '".$CodTipoNom."' AND
							tnec2.Periodo = '".$Periodo."' AND
							tnec2.CodOrganismo = '".$CodOrganismo."' AND
							tnec2.CodTipoProceso = '".$CodTipoProceso."' AND
							tnec2.CodPersona = '".$f['CodProveedor']."' AND
							cpd2.cod_partida = '".$f['cod_partida']."' AND
							me2.CodTipoPago = '".$CodTipoPago."' AND
							c2.Tipo = 'D' AND
							c2.FlagRetencion = 'N'
						GROUP BY tnec2.CodOrganismo";
				$MontoDescuento = getVar3($sql);
				//	si el proceso es fin de mes
				if ($CodTipoProceso == "FIN") {
					$sql = "SELECT SUM(tnec.Monto) AS MontoAdelanto
							FROM
								pr_tiponominaempleadoconcepto tnec
								INNER JOIN tiponomina tn ON (tnec.CodTipoNom = tn.CodTipoNom)
								INNER JOIN pr_tipoproceso tp ON (tnec.CodTipoProceso = tp.CodTipoProceso)
								INNER JOIN mastorganismos o ON (tnec.CodOrganismo = o.CodOrganismo)
								INNER JOIN mastpersonas mp1 ON (tnec.CodPersona = mp1.CodPersona)
								INNER JOIN mastempleado me ON (mp1.CodPersona = me.CodPersona)
								INNER JOIN mastpersonas mp2 ON (o.CodPersona = mp2.CodPersona)
								INNER JOIN mastproveedores p ON (mp2.CodPersona = p.CodProveedor)    
								INNER JOIN pr_concepto c ON (tnec.CodConcepto = c.CodConcepto)
								INNER JOIN pr_conceptoperfil cp ON (tn.CodPerfilConcepto = cp.CodPerfilConcepto)
								INNER JOIN pr_conceptoperfildetalle cpd ON (cp.CodPerfilConcepto = cpd.CodPerfilConcepto AND
																			tnec.CodTipoProceso = cpd.CodTipoProceso AND
																			tnec.CodConcepto = cpd.CodConcepto)
								LEFT JOIN pv_partida pv ON (cpd.cod_partida = pv.cod_partida)
							WHERE
								tnec.CodTipoNom = '".$CodTipoNom."' AND
								tnec.Periodo = '".$Periodo."' AND
								tnec.CodOrganismo = '".$CodOrganismo."' AND
								tnec.CodPersona = '".$f['CodProveedor']."' AND
								tnec.CodTipoProceso = 'ADE' AND
								cpd.cod_partida = '".$f['cod_partida']."' AND
								me.CodTipoPago = '".$CodTipoPago."' AND
								c.Tipo = 'I'
							GROUP BY o.CodPersona, cpd.cod_partida";
					$MontoAdelanto = getVar3($sql);
				}
			}
			//	valido las cuentas
			if ($TipoObligacion == "04") {
				if ($f['CuentaDebe'] != "") $CodCuenta = $f['CuentaDebe'];
				else $CodCuenta = $f['CuentaHaber'];
				if ($f['CuentaDebePub20'] != "") $CodCuentaPub20 = $f['CuentaDebePub20'];
				else $CodCuentaPub20 = $f['CuentaHaberPub20'];
			} else {
				$CodCuenta = $f['CodCuenta'];
				$CodCuentaPub20 = $f['CodCuentaPub20'];
			}
			//	montos
			$cod_partida = $f['cod_partida'];
			
			$NroDocumento = $CodOrganismo.$PeriodoAnio.$PeriodoMes.$CodTipoNom.$CodTipoProceso.$TipoObligacion;
			##	valido nro de documento
			$sql = "SELECT COUNT(*)
					FROM ap_obligaciones
					WHERE
						CodProveedor = '".$f['CodProveedor']."' AND
						CodTipoDocumento = '".$f['CodTipoDocumento']."' AND
						NroDocumento LIKE '".$NroDocumento."%'";
			$_nro = getVar3($sql);
			if ($_nro > 0) $NroDocumento .= "-".(++$_nro);
			//--$NroDocumento = $CodOrganismo.$PeriodoAnio.$PeriodoMes.$CodTipoNom.$CodTipoProceso.$TipoObligacion;
			$Monto = floatval($f['MontoIngreso']) - floatval($MontoDescuento) - floatval($MontoAdelanto);
			//	valido
			$sql = "SELECT *
					FROM pr_obligaciones
					WHERE
						CodProveedor = '".$f['CodProveedor']."' AND
						CodTipoDocumento = '".$f['CodTipoDocumento']."' AND
						NroDocumento = '".$NroDocumento."'";
			$field_obligacionpr = getRecord($sql);
			if (count($field_obligacionpr) == 0) die('Error al insertar la cuenta');
			//	inserto la cuenta
			if ($f['FlagCategoriaProg'] == 'S') {
				$CodPresupuesto = $f['CodPresupuestoConcepto'];
			} else {
				$CodPresupuesto = $f['CodPresupuestoEmpleado'];
			}
			//if (!$CodPresupuesto) die('Se encontraron registros sin presupuesto aprobado');
			$sql = "INSERT INTO pr_obligacionescuenta
					SET
						CodProveedor = '".$f['CodProveedor']."',
						CodTipoDocumento = '".$f['CodTipoDocumento']."',
						NroDocumento = '".$NroDocumento."',
						Linea = '".$Linea."',
						Descripcion = '".$f['Descripcion']."',
						Monto = '".$Monto."',
						CodCentroCosto = '".$CodCentroCosto."',
						CodCuenta = '".$CodCuenta."',
						CodCuentaPub20 = '".$CodCuentaPub20."',
						cod_partida = '".$f['cod_partida']."',
						CodOrganismo = '".$f['CodOrganismo']."',
						CodPresupuesto = '".$CodPresupuesto."',
						CodFuente = '".$_PARAMETRO['FFMETASDEF']."',
						FlagNoAfectoIGV = 'N',
						UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		//	consulto las retenciones a insertar
		if ($TipoObligacion == "01" || $TipoObligacion == "02") {
			$sql = "SELECT
						mp1.CodPersona AS CodProveedor,
						'".$CodTipoDocumento."' AS CodTipoDocumento,
						'01' AS Ficha,
						SUM(tnec.Monto) AS MontoImpuesto,
						c.CodConcepto AS CodRetencion,
						cpd.CuentaHaber AS CodCuenta,
						cpd.CuentaHaberPub20 AS CodCuentaPub20
					 FROM
						pr_tiponominaempleadoconcepto tnec
						INNER JOIN mastorganismos o ON (tnec.CodOrganismo = o.CodOrganismo)
						INNER JOIN mastpersonas mp1 ON (tnec.CodPersona = mp1.CodPersona)
						INNER JOIN mastempleado me ON (mp1.CodPersona = me.CodPersona)
						INNER JOIN mastpersonas mp2 ON (o.CodPersona = mp2.CodPersona)
						INNER JOIN mastproveedores p ON (mp2.CodPersona = p.CodProveedor)
						INNER JOIN pr_concepto c ON (tnec.CodConcepto = c.CodConcepto)
						INNER JOIN tiponomina tn ON (tnec.CodTipoNom = tn.CodTipoNom)
						INNER JOIN pr_conceptoperfil cp ON (tn.CodPerfilConcepto = cp.CodPerfilConcepto)
						INNER JOIN pr_conceptoperfildetalle cpd ON (cp.CodPerfilConcepto = cpd.CodPerfilConcepto AND
																	tnec.CodTipoProceso = cpd.CodTipoProceso AND
																	tnec.CodConcepto = cpd.CodConcepto)
					 WHERE
						tnec.CodTipoNom = '".$CodTipoNom."' AND
						tnec.Periodo = '".$Periodo."' AND
						tnec.CodOrganismo = '".$CodOrganismo."' AND

						tnec.CodTipoProceso = '".$CodTipoProceso."' AND
						me.CodTipoPago = '".$CodTipoPago."' AND
						c.Tipo = 'D' AND
						c.FlagRetencion = 'S' $filtro_transferidos1
					 GROUP BY mp1.CodPersona, CodRetencion";
			$field_retenciones = getRecords($sql);	$Linea = 0;
			foreach($field_retenciones as $f) {	$Linea++;
				$NroDocumento = $CodOrganismo.$PeriodoAnio.$PeriodoMes.$CodTipoNom.$CodTipoProceso.$TipoObligacion;
				##	valido nro de documento
				$sql = "SELECT COUNT(*)
						FROM ap_obligaciones
						WHERE
							CodProveedor = '".$f['CodProveedor']."' AND
							CodTipoDocumento = '".$f['CodTipoDocumento']."' AND
							NroDocumento LIKE '".$NroDocumento."%'";
				$_nro = getVar3($sql);
				if ($_nro > 0) $NroDocumento .= "-".(++$_nro);
				//--$NroDocumento = $CodOrganismo.$PeriodoAnio.$PeriodoMes.$CodTipoNom.$CodTipoProceso.$TipoObligacion;
				//	valido
				$sql = "SELECT *
						FROM pr_obligaciones
						WHERE
							CodProveedor = '".$f['CodProveedor']."' AND
							CodTipoDocumento = '".$f['CodTipoDocumento']."' AND
							NroDocumento = '".$NroDocumento."'";
				$field_obligacion = getRecord($sql);
				if (count($field_obligacion) > 0) {
					//	inserto las retenciones
					$sql = "INSERT INTO pr_obligacionesretenciones
							SET
								CodProveedor = '".$f['CodProveedor']."',
								CodTipoDocumento = '".$f['CodTipoDocumento']."',
								NroDocumento = '".$NroDocumento."',
								Linea = '".$Linea."',
								CodConcepto = '".$f['CodRetencion']."',
								MontoImpuesto = '".$f['MontoImpuesto']."',
								MontoAfecto = '".$f['MontoAfecto']."',
								CodCuenta = '".$f['CodCuenta']."',
								CodCuentaPub20 = '".$f['CodCuentaPub20']."',
								UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
								UltimaFecha = NOW()";
					execute($sql);
				}
			}
		}
		//	-----------------
		mysql_query("COMMIT");
	}
	//	consolidar
	elseif ($accion == "consolidar") {
		mysql_query("BEGIN");
		//	-----------------
		list($AnioActual, $MesActual, $DiaActual) = split("[/.-]", substr($Ahora, 0, 10));
		$PeriodoActual = "$AnioActual-$MesActual";
		list($PeriodoAnio, $PeriodoMes) = split("[-]", $Periodo);
		
		//	consulto el proveedor del organismo
		$sql = "SELECT
					o.CodPersona,
					p.NomCompleto AS NomPersona
				FROM
					mastorganismos o
					INNER JOIN mastpersonas p ON (o.CodPersona = p.CodPersona)
				WHERE o.CodOrganismo = '".$CodOrganismo."'";
		$query_proveedor = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		if (mysql_num_rows($query_proveedor) != 0) $field_proveedor = mysql_fetch_array($query_proveedor);
		else die("Debe asociar una Persona al Organismo para Consolidar.");

		if ($detalles_bancos != "") $detalles_obligacion = $detalles_bancos;
		elseif ($detalles_cheques != "") $detalles_obligacion = $detalles_cheques;
		elseif ($detalles_terceros != "") $detalles_obligacion = $detalles_terceros;
		
		$filtro = "";
		$detalles = split(";", $detalles_obligacion);
		foreach ($detalles as $detalle) {
			list($CodProveedor, $CodTipoDocumento, $NroDocumento, $TipoObligacion) = split("[_]", $detalle);
			//	verifico si la obligacion transferida a cxp esta anulada
			$sql = "SELECT FlagTransferido
					FROM pr_obligaciones
					WHERE
						CodProveedor = '".$CodProveedor."' AND
						CodTipoDocumento = '".$CodTipoDocumento."' AND
						NroDocumento = '".$NroDocumento."' AND
						FlagTransferido = 'S'";
			$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			if (mysql_num_rows($query) != 0) die("Algunas obligaciones seleccionadas deben ser calculadas nuevamente.");
			##
			if ($filtro != "") $filtro .= " OR ";
			$filtro .= "(CodProveedor = '".$CodProveedor."' AND
						 CodTipoDocumento = '".$CodTipoDocumento."' AND
						 NroDocumento = '".$NroDocumento."')";
		}
		
		//	consulto la tabla general
		$sql = "SELECT
					TipoObligacion,
					CodOrganismo,
					CodTipoNom,
					Periodo,
					PeriodoNomina,
					CodTipoProceso,
					CodTipoDocumento,
					NroDocumento,
					CodTipoPago,
					CodTipoServicio,
					CodProveedorPagar,
					NomProveedorPagar,
					Comentarios,
					ComentariosAdicional,
					CodCuenta,
					CodCuentaPub20,
					CodCentroCosto,
					SUM(MontoObligacion) AS MontoObligacion,
					SUM(MontoImpuestoOtros) AS MontoImpuestoOtros,
					SUM(MontoNoAfecto) AS MontoNoAfecto
				FROM pr_obligaciones
				WHERE $filtro
				GROUP BY CodTipoDocumento, NroDocumento";
		$query = mysql_query($sql) or die($sql.mysql_error());
		while($field = mysql_fetch_array($query)) {
			//	elimino las seleccionadas
			$sql = "DELETE FROM pr_obligaciones WHERE $filtro";
			execute($sql);
			
			//	consulto el numero de obligaciones que he consolidado
			$sql = "SELECT *
					FROM pr_obligaciones
					WHERE
						CodProveedor = '".$field_proveedor['CodPersona']."' AND
						CodTipoDocumento = '".$field['CodTipoDocumento']."' AND
						NroDocumento LIKE '".$field['NroDocumento']."-%'";
			$query_numero = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			$rows = intval(mysql_num_rows($query_numero));	$rows++;
			
			//	obtengo algunos valores a insertar
			$NroDocumento = $field['NroDocumento']."-".$rows;
			$NroCuenta = getCuentaBancariaDefault($CodOrganismo, $field['CodTipoPago']);
			
			//	inserto la obligacion
			$sql = "INSERT INTO pr_obligaciones
					SET
						TipoObligacion = '".$field['TipoObligacion']."',
						CodOrganismo = '".$field['CodOrganismo']."',
						CodTipoNom = '".$field['CodTipoNom']."',
						Periodo = '".$field['Periodo']."',
						PeriodoNomina = '".$field['PeriodoNomina']."',
						CodTipoProceso = '".$field['CodTipoProceso']."',
						CodProveedor = '".$field_proveedor['CodPersona']."',
						CodTipoDocumento = '".$field['CodTipoDocumento']."',
						NroDocumento = '".$NroDocumento."',
						NroControl = '".$NroDocumento."',
						NroCuenta = '".$NroCuenta."',
						CodTipoPago = '".$field['CodTipoPago']."',
						CodTipoServicio = '".$field['CodTipoServicio']."',
						FechaRegistro = NOW(),
						CodProveedorPagar = '".$field_proveedor['CodPersona']."',
						NomProveedorPagar = '".$field_proveedor['NomPersona']."',
						Comentarios = '".$field['Comentarios']."',
						ComentariosAdicional = '".$field['ComentariosAdicional']."',
						MontoObligacion = '".$field['MontoObligacion']."',
						MontoNoAfecto = '".$field['MontoNoAfecto']."',
						MontoImpuestoOtros = '".$field['MontoImpuestoOtros']."',
						CodCuenta = '".$field['CodCuenta']."',
						CodCuentaPub20 = '".$field['CodCuentaPub20']."',
						CodCentroCosto = '".$field['CodCentroCosto']."',
						UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
						UltimaFecha = NOW()";
			execute($sql);
			
			//	actualizo el payroll
			$sql = "UPDATE pr_tiponominaempleado
					SET
						CodProveedor = '".$field_proveedor['CodPersona']."',
						CodTipoDocumento = '".$field['CodTipoDocumento']."',
						NroDocumento = '".$NroDocumento."'
					WHERE 1 AND $filtro";
			execute($sql);
			
			//	actualizo prestaciones
			$sql = "UPDATE pr_liquidacionempleado
					SET
						CodProveedor = '".$field_proveedor['CodPersona']."',
						CodTipoDocumento = '".$field['CodTipoDocumento']."',
						NroDocumento = '".$NroDocumento."'
					WHERE 1 AND $filtro";
			execute($sql);
		}
		
		//	consulto las cuentas
		$sql = "SELECT
					CodTipoDocumento,
					NroDocumento,
					CodCentroCosto,
					CodCuenta,
					CodCuentaPub20,
					cod_partida,
					FlagNoAfectoIGV,
					SUM(Monto) AS Monto,
					CodOrganismo,
					CodPresupuesto,
					CodFuente
				FROM pr_obligacionescuenta
				WHERE $filtro
				GROUP BY CodOrganismo, CodPresupuesto, CodFuente, cod_partida, CodCuentaPub20, CodCuenta, CodTipoDocumento, NroDocumento
				ORDER BY CodOrganismo, CodPresupuesto, CodFuente, cod_partida, CodCuentaPub20, CodCuenta, CodTipoDocumento, NroDocumento";
		$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));	$i=0;
		while($field = mysql_fetch_array($query)) {	$i++;
			//	elimino las seleccionadas
			$sql = "DELETE FROM pr_obligacionescuenta WHERE $filtro";
			execute($sql);
		
			//	inserto la obligacion x cuenta
			$sql = "INSERT INTO pr_obligacionescuenta
					SET
						CodProveedor = '".$field_proveedor['CodPersona']."',
						CodTipoDocumento = '".$field['CodTipoDocumento']."',
						NroDocumento = '".$NroDocumento."',
						Linea = '".$i."',
						CodCentroCosto = '".$field['CodCentroCosto']."',
						CodCuenta = '".$field['CodCuenta']."',
						CodCuentaPub20 = '".$field['CodCuentaPub20']."',
						cod_partida = '".$field['cod_partida']."',
						FlagNoAfectoIGV = '".$field['FlagNoAfectoIGV']."',
						Monto = '".$field['Monto']."',
						CodOrganismo = '".$field['CodOrganismo']."',
						CodPresupuesto = '".$field['CodPresupuesto']."',
						CodFuente = '".$field['CodFuente']."',
						UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		
		//	consulto las retenciones
		$sql = "SELECT
					CodTipoDocumento,
					NroDocumento,
					CodConcepto,
					SUM(MontoImpuesto) AS MontoImpuesto,
					SUM(MontoAfecto) AS MontoAfecto,
					CodCuenta,
					CodCuentaPub20,
					FlagProvision
				FROM pr_obligacionesretenciones
				WHERE MontoImpuesto > 0 AND ($filtro)
				GROUP BY CodCuentaPub20, CodCuenta, CodTipoDocumento, NroDocumento";
		$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));	$i=0;
		while($field = mysql_fetch_array($query)) {	$i++;
			//	elimino las seleccionadas
			$sql = "DELETE FROM pr_obligacionesretenciones WHERE $filtro";
			execute($sql);
		
			//	inserto la obligacion retenciones
			$sql = "INSERT INTO pr_obligacionesretenciones
					SET
						CodProveedor = '".$field_proveedor['CodPersona']."',
						CodTipoDocumento = '".$field['CodTipoDocumento']."',
						NroDocumento = '".$NroDocumento."',
						Linea = '".$i."',
						CodConcepto = '".$field['CodConcepto']."',
						MontoImpuesto = '".$field['MontoImpuesto']."',
						MontoAfecto = '".$field['MontoAfecto']."',
						CodCuenta = '".$field['CodCuenta']."',
						CodCuentaPub20 = '".$field['CodCuentaPub20']."',
						FlagProvision = '".$field['FlagProvision']."',
						UltimoUsuario = '".$_SESSION['USUARIO_ACTUAL']."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		//	-----------------
		mysql_query("COMMIT");
	}
	//	verificar
	elseif ($accion == "verificar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "UPDATE pr_obligaciones
				SET
					FlagVerificado = 'S',
					VerificadoPor = '".$_SESSION['CODPERSONA_ACTUAL']."',
					FechaVerificado = NOW()
				WHERE
					CodProveedor = '".$CodProveedor."' AND
					CodTipoDocumento = '".$CodTipoDocumento."' AND
					NroDocumento = '".$NroDocumento."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
	//	generar
	elseif ($accion == "generar") {
		mysql_query("BEGIN");
		//--------------------
		$MontoObligacion = setNumero($MontoObligacion);
		$MontoImpuestoOtros = setNumero($MontoImpuestoOtros);
		$MontoNoAfecto = setNumero($MontoNoAfecto);
		$MontoAfecto = setNumero($MontoAfecto);
		$MontoAdelanto = setNumero($MontoAdelanto);
		$MontoImpuesto = setNumero($MontoImpuesto);
		$MontoPagoParcial = setNumero($MontoPagoParcial);
		$Comentarios = changeUrl($Comentarios);
		$ComentariosAdicional = changeUrl($ComentariosAdicional);
		$MotivoAnulacion = changeUrl($MotivoAnulacion);
		$detalles_impuesto = changeUrl($detalles_impuesto);
		$detalles_documento = changeUrl($detalles_documento);
		$detalles_distribucion = changeUrl($detalles_distribucion);
		list($DiaObligacion, $MesObligacion, $AnioObligacion) = split("[./-]", $FechaRegistro);
		$Periodo = "$AnioObligacion-$MesObligacion";
		$Anio = $AnioObligacion;
		//	verifico valores ingresados
		if (valObligacion($CodProveedor, $CodTipoDocumento, $NroDocumento)) die("Nro. de Obligacion Ya ingresado");
		
		//	obtengo el numero de las ordenes
		$ReferenciaTipoDocumento = $CodTipoDocumento;
		$ReferenciaNroDocumento = $NroDocumento;
		
		//	inserto obligacion
		if (!$CodPresupuesto) $CodPresupuesto = getVar3("SELECT CodPresupuesto FROM pr_obligacionescuenta WHERE CodProveedor = '".$CodProveedor."' AND CodTipoDocumento = '".$CodTipoDocumento."' AND NroDocumento = '".$NroDocumento."' GROUP BY CodPresupuesto LIMIT 0, 1");
		$NroRegistro = getCodigo_2("ap_obligaciones", "NroRegistro", "CodOrganismo", $CodOrganismo, 6);
		$sql = "INSERT INTO ap_obligaciones
				SET
					CodProveedor = '".$CodProveedor."',
					CodTipoDocumento = '".$CodTipoDocumento."',
					NroDocumento = '".$NroDocumento."',
					CodOrganismo = '".$CodOrganismo."',
					CodProveedorPagar = '".$CodProveedorPagar."',
					NroControl = '".$NroControl."',
					NroFactura = '".$NroFactura."',
					NroCuenta = '".$NroCuenta."',
					CodTipoPago = '".$CodTipoPago."',
					CodTipoServicio = '".$CodTipoServicio."',
					ReferenciaTipoDocumento = '".$ReferenciaTipoDocumento."',
					ReferenciaNroDocumento = '".$ReferenciaNroDocumento."',
					MontoObligacion = '".($MontoObligacion)."',
					MontoImpuestoOtros = '".($MontoImpuestoOtros)."',
					MontoNoAfecto = '".($MontoNoAfecto)."',
					MontoAfecto = '".($MontoAfecto)."',
					MontoAdelanto = '".($MontoAdelanto)."',
					MontoImpuesto = '".($MontoImpuesto)."',
					MontoPagoParcial = '".($MontoPagoParcial)."',
					NroRegistro = '".$NroRegistro."',
					Comentarios = '".$Comentarios."',
					ComentariosAdicional = '".$ComentariosAdicional."',
					FechaRegistro = '".formatFechaAMD($FechaRegistro)."',
					FechaVencimiento = '".formatFechaAMD($FechaVencimiento)."',
					FechaRecepcion = '".formatFechaAMD($FechaRecepcion)."',
					FechaDocumento = '".formatFechaAMD($FechaDocumento)."',
					FechaProgramada = '".formatFechaAMD($FechaProgramada)."',
					FechaFactura = '".formatFechaAMD($FechaFactura)."',
					IngresadoPor = '".($IngresadoPor)."',
					FechaPreparacion = '".formatFechaAMD($FechaPreparacion)."',
					Periodo = '".$Periodo."',
					CodCentroCosto = '".$CodCentroCosto."',
					FlagGenerarPago = '".$FlagGenerarPago."',
					FlagAfectoIGV = '".$FlagAfectoIGV."',
					FlagDiferido = '".$FlagDiferido."',
					FlagPagoDiferido = '".$FlagPagoDiferido."',
					FlagCompromiso = '".$FlagCompromiso."',
					FlagPresupuesto = '".$FlagPresupuesto."',
					FlagPagoIndividual = '".$FlagPagoIndividual."',
					FlagCajaChica = '".$FlagCajaChica."',
					FlagDistribucionManual = '".$FlagDistribucionManual."',
					CodPresupuesto = '".$CodPresupuesto."',
					Ejercicio = '".(!$Ejercicio?$Periodo:'')."',
					CodFuente = '".(!$CodFuente?$_PARAMETRO['FFMETASDEF']:'')."',
					FlagNomina = '".$FlagNomina."',
					FlagFacturaPendiente = '".$FlagFacturaPendiente."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		
		//	actualizo pr_obligacion
		$sql = "UPDATE pr_obligaciones
				SET
					NroRegistro = '".$NroRegistro."',
					FlagTransferido = 'S'
				WHERE
					CodProveedor = '".$CodProveedor."' AND
					CodTipoDocumento = '".$CodTipoDocumento."' AND
					NroDocumento = '".$NroDocumento."'";
		execute($sql);
		
		//	actualizo payroll
		$sql = "UPDATE pr_tiponominaempleado
				SET EstadoPago = 'TR'
				WHERE
					CodProveedor = '".$CodProveedor."' AND
					CodTipoDocumento = '".$CodTipoDocumento."' AND
					NroDocumento = '".$NroDocumento."'";
		execute($sql);
		
		//	actualizo prestaciones
		$sql = "UPDATE pr_liquidacionempleado
				SET EstadoPago = 'TR'
				WHERE
					CodProveedor = '".$CodProveedor."' AND
					CodTipoDocumento = '".$CodTipoDocumento."' AND
					NroDocumento = '".$NroDocumento."'";
		execute($sql);
		
		//	impuestos
		if ($detalles_impuesto != "") {
			$linea_impuesto = split(";char:tr;", $detalles_impuesto);	$_Linea=0;
			foreach ($linea_impuesto as $registro) {	$_Linea++;
				list($_CodImpuesto, $_CodConcepto, $_Signo, $_FlagImponible, $_FlagProvision, $_CodCuenta, $_CodCuentaPub20, $_MontoAfecto, $_FactorPorcentaje, $_MontoImpuesto) = split(";char:td;", $registro);
				//	inserto
				$sql = "INSERT INTO ap_obligacionesimpuesto
						SET
							CodProveedor = '".$CodProveedor."',
							CodTipoDocumento = '".$CodTipoDocumento."',
							NroDocumento = '".$NroDocumento."',
							Linea = '".$_Linea."',
							CodImpuesto = '".$_CodImpuesto."',
							CodConcepto = '".$_CodConcepto."',
							FactorPorcentaje = '".$_FactorPorcentaje."',
							MontoImpuesto = '".$_MontoImpuesto."',
							MontoAfecto = '".$_MontoAfecto."',
							CodCuenta = '".$_CodCuenta."',
							CodCuentaPub20 = '".$_CodCuentaPub20."',
							FlagProvision = '".$_FlagProvision."',
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()";
				execute($sql);
			}
		}
		
		//	distribucion
		if ($detalles_distribucion != "") {
			$linea_distribucion = split(";char:tr;", $detalles_distribucion);	$_Secuencia=0;
			foreach ($linea_distribucion as $registro) {	$_Secuencia++;
				list($_cod_partida, $_CodCuenta, $_CodCuentaPub20, $_CodCentroCosto, $_FlagNoAfectoIGV, $_Monto, $_CategoriaProg, $_Ejercicio, $_CodPresupuesto, $_CodFuente, $_TipoOrden, $_NroOrden, $_Referencia, $_Descripcion, $_CodPersona, $_NroActivo, $_FlagDiferido) = split(";char:td;", $registro);
				//	inserto distribucion x cuentas
				$sql = "INSERT INTO ap_obligacionescuenta
						SET
							CodProveedor = '".$CodProveedor."',
							CodTipoDocumento = '".$CodTipoDocumento."',
							NroDocumento = '".$NroDocumento."',
							Secuencia = '".$_Secuencia."',
							Linea = '1',
							Descripcion = '".$_Descripcion."',
							Monto = '".$_Monto."',
							CodCentroCosto = '".$_CodCentroCosto."',
							CodCuenta = '".$_CodCuenta."',
							CodCuentaPub20 = '".$_CodCuentaPub20."',
							cod_partida = '".$_cod_partida."',
							TipoOrden = '".$_TipoOrden."',
							NroOrden = '".$_NroOrden."',
							FlagNoAfectoIGV = '".$_FlagNoAfectoIGV."',
							Referencia = '".$_Referencia."',
							CodPersona = '".$_CodPersona."',
							NroActivo = '".$_NroActivo."',
							FlagDiferido = '".$_FlagDiferido."',
							CodOrganismo = '".$CodOrganismo."',
							Ejercicio = '".$_Ejercicio."',
							CodPresupuesto = '".$_CodPresupuesto."',
							CodFuente = '".$_CodFuente."',
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()";
				execute($sql);
			}
		}
		
		//	resumen
		$Origen = "NO";
		$sql = "SELECT
					SUM(Monto) AS Monto,
					cod_partida,
					CodCuenta,
					CodCuentaPub20,
					CodCentroCosto,
					Ejercicio, 
					CodPresupuesto, 
					CodFuente
				FROM ap_obligacionescuenta
				WHERE
					CodProveedor = '".$CodProveedor."' AND
					CodTipoDocumento = '".$CodTipoDocumento."' AND
					NroDocumento = '".$NroDocumento."'
				GROUP BY Ejercicio, CodPresupuesto, CodFuente, cod_partida, CodCuenta, CodCuentaPub20";
		$query_res = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));	$_Secuencia = 0;
		while ($field_res = mysql_fetch_array($query_res)) {
			$_Secuencia++;
			//	inserto en distribucion compromisos
			if ($field_res['CodCentroCosto'] == "") die("Debe seleccionar el Centro de Costo para todas las cuentas");
			//	inserto en distribucion compromisos
			if ($FlagCompromiso == "S") {
				$sql = "INSERT INTO lg_distribucioncompromisos
						SET
							CodOrganismo = '".$CodOrganismo."',
							CodProveedor = '".$CodProveedor."',
							CodTipoDocumento = '".$CodTipoDocumento."',
							NroDocumento = '".$NroDocumento."',
							Secuencia = '".$_Secuencia."',
							Linea = '1',
							CodCentroCosto = '".$field_res['CodCentroCosto']."',
							cod_partida = '".$field_res['cod_partida']."',
							Monto = '".$field_res['Monto']."',
							Anio = '".$Anio."',
							Periodo = '".$Periodo."',
							Mes = '".substr($Periodo, 5, 2)."',
							CodPresupuesto = '".$field_res['CodPresupuesto']."',
							Ejercicio = '".$field_res['Ejercicio']."',
							CodFuente = '".$field_res['CodFuente']."',
							Origen = '".$Origen."',
							Estado = 'PE',
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()";
				execute($sql);
			}
			
			//	inserto en la distribucion
			$sql = "INSERT INTO ap_distribucionobligacion
					SET
						CodProveedor = '".$CodProveedor."',
						CodTipoDocumento = '".$CodTipoDocumento."',
						NroDocumento = '".$NroDocumento."',
						CodCentroCosto = '".$field_res['CodCentroCosto']."',
						Monto = '".$field_res['Monto']."',
						CodCuenta = '".$field_res['CodCuenta']."',
						CodCuentaPub20 = '".$field_res['CodCuentaPub20']."',
						cod_partida = '".$field_res['cod_partida']."',
						Anio = '".$Anio."',
						Periodo = '".$Periodo."',
						CodOrganismo = '".$CodOrganismo."',
						CodPresupuesto = '".$field_res['CodPresupuesto']."',
						Ejercicio = '".$field_res['Ejercicio']."',
						CodFuente = '".$field_res['CodFuente']."',
						FlagCompromiso = '".$FlagCompromiso."',
						Origen = 'OB',
						Estado = 'PE',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		//--------------------
		mysql_query("COMMIT");
	}
}
?>