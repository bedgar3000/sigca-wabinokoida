<?php
if ($opcion == "ver") {
	$disabled_ver = "disabled";
	$display_ver = "display:none;";
}
list($NroProceso, $Secuencia) = split("[_]", $registro);

$Glosa = "";
$sql = "SELECT op.Concepto
		FROM
			ap_pagos p
			INNER JOIN ap_ordenpago op ON (op.NroProceso = p.NroProceso AND op.Secuencia = p.Secuencia)
		WHERE
			p.NroProceso = '".$NroProceso."' AND
			p.Secuencia = '".$Secuencia."'";
$field_glosa = getRecords($sql);
foreach ($field_glosa as $fg) {
	if ($Glosa) $Glosa .= ", ";
	$Glosa .= $fg['Concepto'];
}

//	consulto datos generales de la transaccion
$sql = "SELECT
			p.CodOrganismo,
			p.CodProveedor,
			p.NroPago,
			p.CodTipoPago,
			p.MontoPago,
			p.NroOrden,
			p.Periodo,
			p.NroCuenta,
			p.FechaPago,
			b.Banco,
			mp3.CodPersona AS PreparadoPor,
			mp3.NomCompleto AS NomPreparadoPor,
			mp4.CodPersona AS AprobadoPor,
			mp4.NomCompleto AS NomAprobadoPor,
			(SELECT PrefVoucherPA FROM mastaplicaciones WHERE CodAplicacion = 'AP') AS CodVoucher,
			(SELECT CodSistemaFuente FROM mastaplicaciones WHERE CodAplicacion = 'AP') AS CodSistemaFuente
		FROM
			ap_pagos p
			INNER JOIN ap_ctabancaria cb ON (p.NroCuenta = cb.NroCuenta)
			INNER JOIN mastbancos b ON (cb.CodBanco = b.CodBanco)
			LEFT JOIN mastpersonas mp3 ON (p.GeneradoPor = mp3.CodPersona)
			LEFT JOIN mastpersonas mp4 ON (p.AprobadoPor = mp4.CodPersona)
		WHERE
			p.NroProceso = '".$NroProceso."' AND
			p.Secuencia = '".$Secuencia."'";
$query_mast = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
if (mysql_num_rows($query_mast) != 0) $field_mast = mysql_fetch_array($query_mast);

//	consulto si el periodo esta abierto
$sql = "SELECT Estado
		FROM ac_controlcierremensual
		WHERE
			TipoRegistro = 'AB' AND
			CodOrganismo = '".$field_mast['CodOrganismo']."' AND
			Periodo = '".substr($field_mast['FechaPago'], 0, 7)."'";
$query_periodo = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
if (mysql_num_rows($query_periodo) != 0) $field_periodo = mysql_fetch_array($query_periodo);

//	sistema fuente
$sql = "SELECT CodSistemaFuente FROM mastaplicaciones WHERE CodAplicacion = 'AP'";
$query_prefpa = mysql_query($sql) or die ($sql.mysql_error());
if (mysql_num_rows($query_prefpa) != 0) $field_prefpa = mysql_fetch_array($query_prefpa);
?>

<form name="frmentrada" id="frmentrada" method="POST" onsubmit="return vouchers_pago(this, 'pagos-pub20', 'li2');">
<input type="hidden" id="NroProceso" value="<?=$NroProceso?>" />
<input type="hidden" id="Secuencia" value="<?=$Secuencia?>" />
<input type="hidden" id="CodProveedor" value="<?=$field_mast['CodProveedor']?>" />
<input type="hidden" id="Anio" value="<?=substr($field_mast['Periodo'], 0, 4)?>" />
<input type="hidden" id="NroOrden" value="<?=$field_mast['NroOrden']?>" />
<input type="hidden" id="CodProveedor" value="<?=$field_mast['CodProveedor']?>" />
<input type="hidden" id="CodTipoDocumento" value="<?=$field_mast['CodTipoDocumento']?>" />
<input type="hidden" id="NroDocumento" value="<?=$field_mast['NroDocumento']?>" />
<input type="hidden" id="PeriodoEstado" value="<?=($field_periodo['Estado'])?>" />
<input type="hidden" id="CodTipoPago" value="<?=$field_mast['CodTipoPago']?>" />
<input type="hidden" id="NroPago" value="<?=$field_mast['NroPago']?>" />
<input type="hidden" id="NroCuenta" value="<?=$field_mast['NroCuenta']?>" />
<input type="hidden" id="CodDependencia" value="<?=$_SESSION['DEPENDENCIA_ACTUAL']?>" />
<input type="hidden" id="CodSistemaFuente" value="<?=$field_prefpa['CodSistemaFuente']?>" />
<table align="center">
	<tr>
    	<td valign="top">
            <table width="400" class="tblBotones">
                <tr><td align="right">&nbsp;</td></tr>
            </table>
            
            <table><tr><td><div style="overflow:scroll; width:400px; height:100px;">
            <table width="500" class="tblLista">
            	<thead>
                <tr>
                    <th width="75">Periodo</th>
                    <th width="75">Voucher</th>
                    <th width="75">Fecha</th>
                    <th width="75">Status</th>
                    <th>Organismo</th>
                </tr>
                </thead>
                
                <tbody id="lista1">
                </tbody>
            </table>
            </div></td></tr></table>
        </td>
        
        <td valign="top">
            <table width="550" class="tblBotones">
                <tr><td align="right">&nbsp;</td></tr>
            </table>
            
            <table><tr><td><div style="overflow:scroll; width:550px; height:100px;">
            <table width="700" class="tblLista">
            	<thead>
                <tr>
                    <th width="50">Linea</th>
                    <th>Errores Encontrados</th>
                    <th width="75">Periodo</th>
                    <th width="75">Voucher</th>
                    <th width="75">Organismo</th>
                </tr>
                </thead>
                
                <tbody id="lista_errores">
                </tbody>
            </table>
            </div></td></tr></table>
        </td>
    </tr>
    
    <tr>
    	<td colspan="2">
            <table width="960" class="tblForm">
                <tr>
                    <td class="tagForm" width="125">* Organismo:</td>
                    <td>
                        <select id="CodOrganismo" style="width:300px;" <?=$disabled_ver?>>
                            <?=loadSelect("mastorganismos", "CodOrganismo", "Organismo", $field_mast['CodOrganismo'], 1)?>
                        </select>
                    </td>
                    <td class="tagForm">Descripci&oacute;n:</td>
                    <td><input type="text" id="ComentariosVoucher" style="width:305px;" value="<?=htmlentities($Glosa)?>" <?=$disabled_ver?> /></td>
                </tr>
                <tr>
                    <td class="tagForm">* Fecha:</td>
                    <td><input type="text" id="FechaVoucher" value="<?=formatFechaDMA($field_mast['FechaPago'])?>" style="width:75px;" class="datepicker" onchange="setPeriodo(this.value);" /></td>
                    <td class="tagForm">Preparado Por:</td>
                    <td>
                        <input type="hidden" id="PreparadoPor" value="<?=$field_mast['PreparadoPor']?>" />
                        <input type="text" style="width:235px;" value="<?=htmlentities($field_mast['NomPreparadoPor'])?>" disabled />
                        <input type="text" id="FechaPreparacion" style="width:60px;" value="<?=formatFechaDMA($field_mast['FechaPago'])?>" disabled />
                    </td>
                </tr>
                <tr>
                    <td class="tagForm">Voucher:</td>
                    <td>
						<input type="text" id="Periodo" value="<?=$field_mast['Periodo']?>" style="width:50px;" disabled />
                        <select id="CodVoucher" <?=$disabled_ver?>>
                            <?=loadSelect("ac_voucher", "CodVoucher", "CodVoucher", $field_mast['CodVoucher'], 1)?>
                        </select>
                        <input type="text" id="NroVoucher" style="width:50px;" disabled="disabled" />
                    </td>
                    <td class="tagForm">Aprobado Por:</td>
                    <td>
                        <input type="hidden" id="AprobadoPor" value="<?=$field_mast['AprobadoPor']?>" />
                        <input type="text" style="width:235px;" value="<?=htmlentities($field_mast['NomAprobadoPor'])?>" disabled />
                        <input type="text" id="FechaAprobacion" style="width:60px;" value="<?=formatFechaDMA($field_mast['FechaPago'])?>" disabled />
                    </td>
                </tr>
                <tr>
                    <td class="tagForm">* Libro Contable:</td>
                    <td>
                        <select id="CodLibroCont" style="width:150px;" <?=$disabled_ver?>>
                            <?=loadSelect("ac_librocontable", "CodLibroCont", "Descripcion", "", 0)?>
                        </select>
                    </td>
                    <td class="tagForm">* Contabilidad:</td>
                    <td>
                        <select id="CodContabilidad" style="width:150px;">
                            <?=loadSelect("ac_contabilidades", "CodContabilidad", "Descripcion", "F", 1)?>
                        </select>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
	<tr>
    	<td valign="top" colspan="2">
            <table width="960" class="tblBotones">
                <tr>
                    <td align="right">
                        <input type="submit" class="btLista" value="Aceptar" id="btAceptar" style=" <?=$display_ver?>" />
                        <input type="button" class="btLista" value="Rechazar" onclick="javascript:window.close();" style=" <?=$display_ver?>" />
                    </td>
                </tr>
            </table>
            
            <table><tr><td><div style="overflow:scroll; width:960px; height:175px;">
            <table width="1100" class="tblLista">
            	<thead>
	                <tr>
	                    <th width="30">#</th>
	                    <th width="110">Cuenta</th>
	                    <th>Descripci&oacute;n</th>
	                    <th width="125">Monto</th>
	                    <th width="75">Persona</th>
	                    <th colspan="2">Documento</th>
	                    <th width="75">C.Costo</th>
	                    <th width="75">Fecha</th>
	                </tr>
                </thead>
                
                <tbody>
				<?php
				$_CodCuenta = array();
				$_Descripcion = array();
				$_MontoVoucher = array();
				$_CodPersona = array();
				$_ReferenciaNroDocumento = array();
				$_FechaVoucher = array();
				$_Columna = array();
				$_CodCentroCosto = array();
				$sql = "SELECT
							op.CodCentroCosto,
							op.FlagPagoParcial,
							o.CodProveedor,
							o.CodTipoDocumento,
							o.NroDocumento,
							op.NroOrden,
							td.Descripcion AS NomCuenta,
							td.FlagProvision,
							o.CodCuentaPub20 AS CodCuentaPago,
							o.Comentarios,
							o.FlagPresupuesto,
							o.MontoImpuesto,
							op.Anio,
							op.NroOrden,
							op.CodOrganismo,
							p.FechaPago,
							p.MontoPago,
							td.CodRegimenFiscal,
							o.FlagAgruparIgv,
							p.NroProceso,
							p.Secuencia
						FROM
							ap_pagos p
							INNER JOIN ap_ordenpago op ON (p.NroProceso = op.NroProceso AND p.Secuencia = op.Secuencia)
							INNER JOIN ap_tipodocumento td ON (op.CodTipoDocumento = td.CodTipoDocumento)
							INNER JOIN ap_obligaciones o ON (op.CodProveedor = o.CodProveedor AND
															 op.CodTipoDocumento = o.CodTipoDocumento AND
															 op.NroDocumento = o.NroDocumento)
						WHERE
							p.NroProceso = '".$NroProceso."' AND
							p.Secuencia = '".$Secuencia."'";
				$field_ordenes = getRecords($sql);
				foreach ($field_ordenes as $fo) {
					if ($fo['FlagPresupuesto'] != 'S' && ($fo['CodRegimenFiscal'] == 'I' || $fo['CodRegimenFiscal'] == 'M')) {
						$field_impuesto['Monto'] = 0;
						$field_impuesto2['Monto'] = 0;
					} else {
						//	impuestos que provisionan en el documento
						$sql = "SELECT ABS(SUM(oi1.MontoImpuesto)) AS Monto
								FROM
									ap_obligacionesimpuesto oi1
									INNER JOIN ap_obligaciones o1 ON (oi1.CodProveedor = o1.CodProveedor AND
																	  oi1.CodTipoDocumento = o1.CodTipoDocumento AND
																	  oi1.NroDocumento = o1.NroDocumento)
								WHERE
									oi1.FlagProvision = 'N' AND
									oi1.CodProveedor = '".$fo['CodProveedor']."' AND
									oi1.CodTipoDocumento = '".$fo['CodTipoDocumento']."' AND
									oi1.NroDocumento = '".$fo['NroDocumento']."'
								GROUP BY oi1.FlagProvision";
						$query_impuesto = mysql_query($sql) or die($sql.mysql_error());
						if (mysql_num_rows($query_impuesto) != 0) $field_impuesto = mysql_fetch_array($query_impuesto);
						if (mysql_num_rows($query_impuesto3) != 0) $field_impuesto3 = mysql_fetch_array($query_impuesto3);
						//	si el tipo de documento no provisiona
						if ($fo['FlagProvision'] == "N") {
							$sql = "SELECT ABS(SUM(oi1.MontoImpuesto)) AS Monto
									FROM ap_obligacionesimpuesto oi1
									WHERE
										oi1.CodProveedor = '".$fo['CodProveedor']."' AND
										oi1.CodTipoDocumento = '".$fo['CodTipoDocumento']."' AND
										oi1.NroDocumento = '".$fo['NroDocumento']."'";
							$query_impuesto2 = mysql_query($sql) or die($sql.mysql_error());
							if (mysql_num_rows($query_impuesto2) != 0) $field_impuesto2 = mysql_fetch_array($query_impuesto2);
						} else $field_impuesto2['Monto'] = 0.00;
					}
					//	impuestos que provisionan en el pago
					$sql = "SELECT ABS(SUM(oi1.MontoImpuesto)) AS Monto
							FROM
								ap_obligacionesimpuesto oi1
								INNER JOIN ap_obligaciones o1 ON (oi1.CodProveedor = o1.CodProveedor AND
																  oi1.CodTipoDocumento = o1.CodTipoDocumento AND
																  oi1.NroDocumento = o1.NroDocumento)
							WHERE
								oi1.FlagProvision = 'P' AND
								oi1.CodProveedor = '".$fo['CodProveedor']."' AND
								oi1.CodTipoDocumento = '".$fo['CodTipoDocumento']."' AND
								oi1.NroDocumento = '".$fo['NroDocumento']."'
							GROUP BY oi1.FlagProvision";
					$query_impuesto3 = mysql_query($sql) or die($sql.mysql_error());
					
					if ($fo['FlagProvision'] == "S") {
						if ($fo['FlagPagoParcial'] == 'S') {
							$MontoVoucher1 = "(".floatval($fo['MontoPago']).") AS MontoVoucher,"; 
							$MontoVoucher2 = "SUM(opd.MontoPagado) AS MontoVoucher,";

							$sql = "SELECT *
									FROM ap_pagosparciales
									WHERE 
										CodOrganismo = '$fo[CodOrganismo]' AND
										Anio = '$fo[Anio]' AND
										NroOrden = '$fo[NroOrden]' AND
										Estado <> 'AN'";
							$field_pagos_parciales = getRecords($sql);
							if (count($field_pagos_parciales) > 1) $FlagPrimerPagoParcial = 'N'; else $FlagPrimerPagoParcial = 'S';
						} else {
							$MontoVoucher1 = "(o.MontoObligacion) AS MontoVoucher,";
							$MontoVoucher2 = "(SUM(oc.Monto) - ".floatval($field_impuesto['Monto']).") AS MontoVoucher,";
						}

						if ($fo['FlagAgruparIgv'] == 'S') {
							$sql = "(SELECT
										cb.CodCuentaPub20 AS CodCuenta,
										o.ReferenciaTipoDocumento AS TipoOrden,
										o.ReferenciaNroDocumento AS NroOrden,
										pc.Descripcion AS NomCuenta,
										$MontoVoucher1
										pc.TipoSaldo,
		                                pc.FlagReqCC,
										'01' AS Orden,
										'Haber' AS Columna
									 FROM
										ap_obligaciones o
										INNER JOIN ap_ctabancaria cb ON (o.NroCuenta = cb.NroCuenta)
										INNER JOIN ac_mastplancuenta20 pc ON (cb.CodCuentaPub20 = pc.CodCuenta)
									 WHERE
										o.CodProveedor = '".$fo['CodProveedor']."' AND
										o.CodTipoDocumento = '".$fo['CodTipoDocumento']."' AND
										o.NroDocumento = '".$fo['NroDocumento']."'
									 GROUP BY CodCuenta)
									UNION
									(SELECT
										i.CodCuentaPub20 AS CodCuenta,
										o.ReferenciaTipoDocumento AS TipoOrden,
										o.ReferenciaNroDocumento AS NroOrden,
										pc.Descripcion AS NomCuenta,
										ABS(SUM(oc.MontoImpuesto)) AS MontoVoucher,
										pc.TipoSaldo,
		                                pc.FlagReqCC,
										'02' AS Orden,
										'Haber' AS Columna
									 FROM
										ap_obligacionesimpuesto oc
										INNER JOIN ap_obligaciones o ON (oc.CodProveedor = o.CodProveedor AND
																		 oc.CodTipoDocumento = o.CodTipoDocumento AND
																		 oc.NroDocumento = o.NroDocumento)
										INNER JOIN mastimpuestos i ON (oc.CodImpuesto = i.CodImpuesto)
										INNER JOIN ac_mastplancuenta20 pc ON (i.CodCuentaPub20 = pc.CodCuenta)
									 WHERE
										i.FlagProvision = 'P' AND
										oc.CodProveedor = '".$fo['CodProveedor']."' AND
										oc.CodTipoDocumento = '".$fo['CodTipoDocumento']."' AND
										oc.NroDocumento = '".$fo['NroDocumento']."'
									 GROUP BY CodCuenta)
									UNION
									(SELECT
										p.CtaOrdPagoPub20 AS CodCuenta,
										oc.TipoOrden,
										oc.NroOrden,
										pc.Descripcion AS NomCuenta,
										$MontoVoucher2
										pc.TipoSaldo,
		                                pc.FlagReqCC,
										'03' AS Orden,
										'Debe' AS Columna
									 FROM
										ap_obligacionescuenta oc
										INNER JOIN pv_partida p ON (p.cod_partida = oc.cod_partida)
										INNER JOIN ac_mastplancuenta20 pc ON (pc.CodCuenta = p.CtaOrdPagoPub20)
										INNER JOIN ap_obligaciones o ON (o.CodProveedor = oc.CodProveedor AND
																		 o.CodTipoDocumento = oc.CodTipoDocumento AND
																		 o.NroDocumento = oc.NroDocumento)

										LEFT JOIN ap_ordenpago op ON (op.CodProveedor = o.CodProveedor AND
																	  op.CodTipoDocumento = o.CodTipoDocumento AND
																	  op.NroDocumento = o.NroDocumento AND
																	  (op.Estado = 'PA' OR op.Estado = 'PP'))
										LEFT JOIN ap_pagos pg ON (op.NroProceso = pg.NroProceso AND op.Secuencia = pg.Secuencia)
										LEFT JOIN ap_ordenpagodistribucion opd ON (opd.Anio = op.Anio AND
																				   opd.CodOrganismo = op.CodOrganismo AND
																				   opd.NroOrden = op.NroOrden AND
																				   opd.CodPresupuesto = oc.CodPresupuesto AND
																				   opd.CodFuente = oc.CodFuente AND
																				   opd.cod_partida = oc.cod_partida AND
																				   opd.PagoNroProceso = pg.NroProceso AND
																				   opd.PagoSecuencia = pg.Secuencia)
									 WHERE
										oc.CodProveedor = '".$fo['CodProveedor']."' AND
										oc.CodTipoDocumento = '".$fo['CodTipoDocumento']."' AND
										oc.NroDocumento = '".$fo['NroDocumento']."'
									 GROUP BY CodCuenta)
									ORDER BY Columna DESC, Orden, CodCuenta";
						}
						elseif ($fo['FlagPagoParcial'] == 'S' && $FlagPrimerPagoParcial == 'N') {
							$sql = "(SELECT
										cb.CodCuentaPub20 AS CodCuenta,
										o.ReferenciaTipoDocumento AS TipoOrden,
										o.ReferenciaNroDocumento AS NroOrden,
										pc.Descripcion AS NomCuenta,
										$MontoVoucher1
										pc.TipoSaldo,
		                                pc.FlagReqCC,
										'01' AS Orden,
										'Haber' AS Columna
									 FROM
										ap_obligaciones o
										INNER JOIN ap_ctabancaria cb ON (o.NroCuenta = cb.NroCuenta)
										INNER JOIN ac_mastplancuenta20 pc ON (cb.CodCuentaPub20 = pc.CodCuenta)
									 WHERE
										o.CodProveedor = '".$fo['CodProveedor']."' AND
										o.CodTipoDocumento = '".$fo['CodTipoDocumento']."' AND
										o.NroDocumento = '".$fo['NroDocumento']."'
									 GROUP BY CodCuenta)
									UNION
									(SELECT
										p.CtaOrdPagoPub20 AS CodCuenta,
										oc.TipoOrden,
										oc.NroOrden,
										pc.Descripcion AS NomCuenta,
										$MontoVoucher2
										pc.TipoSaldo,
		                                pc.FlagReqCC,
										'03' AS Orden,
										'Debe' AS Columna
									 FROM
										ap_obligacionescuenta oc
										INNER JOIN pv_partida p ON (p.cod_partida = oc.cod_partida)
										INNER JOIN ac_mastplancuenta20 pc ON (pc.CodCuenta = p.CtaOrdPagoPub20)
										INNER JOIN ap_obligaciones o ON (o.CodProveedor = oc.CodProveedor AND
																		 o.CodTipoDocumento = oc.CodTipoDocumento AND
																		 o.NroDocumento = oc.NroDocumento)

										LEFT JOIN ap_ordenpago op ON (op.CodProveedor = o.CodProveedor AND
																	  op.CodTipoDocumento = o.CodTipoDocumento AND
																	  op.NroDocumento = o.NroDocumento AND
																	  (op.Estado = 'PA' OR op.Estado = 'PP'))
										LEFT JOIN ap_pagos pg ON (op.NroProceso = pg.NroProceso AND op.Secuencia = pg.Secuencia)
										LEFT JOIN ap_ordenpagodistribucion opd ON (opd.Anio = op.Anio AND
																				   opd.CodOrganismo = op.CodOrganismo AND
																				   opd.NroOrden = op.NroOrden AND
																				   opd.CodPresupuesto = oc.CodPresupuesto AND
																				   opd.CodFuente = oc.CodFuente AND
																				   opd.cod_partida = oc.cod_partida AND
																				   opd.PagoNroProceso = pg.NroProceso AND
																				   opd.PagoSecuencia = pg.Secuencia)
									 WHERE
										oc.CodProveedor = '".$fo['CodProveedor']."' AND
										oc.CodTipoDocumento = '".$fo['CodTipoDocumento']."' AND
										oc.NroDocumento = '".$fo['NroDocumento']."'
									 GROUP BY CodCuenta)
									ORDER BY Columna DESC, Orden, CodCuenta";
							$sql = "(SELECT
										cb.CodCuentaPub20 AS CodCuenta, 
										o.ReferenciaTipoDocumento AS TipoOrden, 
										o.ReferenciaNroDocumento AS NroOrden, 
										pc.Descripcion AS NomCuenta, 
										$MontoVoucher1 
										pc.TipoSaldo, 
		                                pc.FlagReqCC, 
										'01' AS Orden, 
										'Haber' AS Columna 
									 FROM 
										ap_obligaciones o 
										INNER JOIN ap_ctabancaria cb ON (o.NroCuenta = cb.NroCuenta) 
										INNER JOIN ac_mastplancuenta20 pc ON (cb.CodCuentaPub20 = pc.CodCuenta) 
									 WHERE 
										o.CodProveedor = '".$fo['CodProveedor']."' AND 
										o.CodTipoDocumento = '".$fo['CodTipoDocumento']."' AND 
										o.NroDocumento = '".$fo['NroDocumento']."' 
									 GROUP BY CodCuenta) 
									UNION 
									(SELECT 
										p.CtaOrdPagoPub20 AS CodCuenta, 
										'' AS TipoOrden, 
										opd.NroOrden, 
										pc.Descripcion AS NomCuenta, 
										$MontoVoucher2 
										pc.TipoSaldo, 
		                                pc.FlagReqCC, 
										'03' AS Orden, 
										'Debe' AS Columna 
									 FROM 
									 	ap_ordenpagodistribucion opd 
										INNER JOIN pv_partida p ON (p.cod_partida = opd.cod_partida) 
										INNER JOIN ac_mastplancuenta20 pc ON (pc.CodCuenta = p.CtaOrdPagoPub20) 
									 WHERE 
										opd.PagoNroProceso = '".$fo['NroProceso']."' 
										AND opd.PagoSecuencia = '".$fo['Secuencia']."' 
									 GROUP BY CodCuenta)
									ORDER BY Columna DESC, Orden, CodCuenta";
						}
						else {
							if ($fo['FlagPagoParcial'] == 'S')
							{
								$sql = "(SELECT
											cb.CodCuentaPub20 AS CodCuenta,
											o.ReferenciaTipoDocumento AS TipoOrden,
											o.ReferenciaNroDocumento AS NroOrden,
											pc.Descripcion AS NomCuenta,
											$MontoVoucher1
											pc.TipoSaldo,
			                                pc.FlagReqCC,
											'01' AS Orden,
											'Haber' AS Columna
										 FROM
											ap_obligaciones o
											INNER JOIN ap_ctabancaria cb ON (o.NroCuenta = cb.NroCuenta)
											INNER JOIN ac_mastplancuenta20 pc ON (cb.CodCuentaPub20 = pc.CodCuenta)
										 WHERE
											o.CodProveedor = '".$fo['CodProveedor']."' AND
											o.CodTipoDocumento = '".$fo['CodTipoDocumento']."' AND
											o.NroDocumento = '".$fo['NroDocumento']."'
										 GROUP BY CodCuenta)
										UNION
										(SELECT
											i.CodCuentaPub20 AS CodCuenta,
											o.ReferenciaTipoDocumento AS TipoOrden,
											o.ReferenciaNroDocumento AS NroOrden,
											pc.Descripcion AS NomCuenta,
											ABS(SUM(oc.MontoImpuesto)) AS MontoVoucher,
											pc.TipoSaldo,
			                                pc.FlagReqCC,
											'02' AS Orden,
											'Haber' AS Columna
										 FROM
											ap_obligacionesimpuesto oc
											INNER JOIN ap_obligaciones o ON (oc.CodProveedor = o.CodProveedor AND
																			 oc.CodTipoDocumento = o.CodTipoDocumento AND
																			 oc.NroDocumento = o.NroDocumento)
											INNER JOIN mastimpuestos i ON (oc.CodImpuesto = i.CodImpuesto)
											INNER JOIN ac_mastplancuenta20 pc ON (i.CodCuentaPub20 = pc.CodCuenta)
										 WHERE
											i.FlagProvision = 'P' AND
											oc.CodProveedor = '".$fo['CodProveedor']."' AND
											oc.CodTipoDocumento = '".$fo['CodTipoDocumento']."' AND
											oc.NroDocumento = '".$fo['NroDocumento']."'
										 GROUP BY CodCuenta)
										UNION
										(SELECT
											p.CtaOrdPagoPub20 AS CodCuenta,
											'oc' AS TipoOrden,
											'oc' AS NroOrden,
											pc.Descripcion AS NomCuenta,
											$MontoVoucher2
											pc.TipoSaldo,
			                                pc.FlagReqCC,
											'03' AS Orden,
											'Debe' AS Columna
										 FROM
										 	ap_ordenpago op
											LEFT JOIN ap_pagos pg ON (op.NroProceso = pg.NroProceso AND op.Secuencia = pg.Secuencia)
											LEFT JOIN ap_ordenpagodistribucion opd ON (opd.Anio = op.Anio AND
																					   opd.CodOrganismo = op.CodOrganismo AND
																					   opd.NroOrden = op.NroOrden AND
																					   opd.PagoNroProceso = pg.NroProceso AND
																					   opd.PagoSecuencia = pg.Secuencia AND
																					   opd.cod_partida <> '".$_PARAMETRO["IVADEFAULT"]."')
											INNER JOIN pv_partida p ON (p.cod_partida = opd.cod_partida)
											INNER JOIN ac_mastplancuenta20 pc ON (pc.CodCuenta = p.CtaOrdPagoPub20)
										 WHERE
											op.CodProveedor = '".$fo['CodProveedor']."' AND
											op.CodTipoDocumento = '".$fo['CodTipoDocumento']."' AND
											op.NroDocumento = '".$fo['NroDocumento']."' AND
											(op.Estado = 'PA' OR op.Estado = 'PP')
										 GROUP BY CodCuenta)
										UNION
										(SELECT
											(SELECT pc2.CodCuenta
											 FROM
												mastimpuestos i2
												INNER JOIN pv_partida pv2 ON (pv2.cod_partida = i2.cod_partida)
												INNER JOIN ac_mastplancuenta20 pc2 ON (pc2.CodCuenta = pv2.CtaOrdPagoPub20)
											 WHERE CodImpuesto = '".$_PARAMETRO["IGVCODIGO"]."') AS CodCuenta,
											oc.ReferenciaTipoDocumento AS TipoOrden,
											oc.ReferenciaNroDocumento AS NroOrden,
											(SELECT pc2.Descripcion
											 FROM
												mastimpuestos i2
												INNER JOIN pv_partida pv2 ON (pv2.cod_partida = i2.cod_partida)
												INNER JOIN ac_mastplancuenta20 pc2 ON (pc2.CodCuenta = pv2.CtaOrdPagoPub20)
											 WHERE CodImpuesto = '".$_PARAMETRO["IGVCODIGO"]."') AS NomCuenta,
											oc.MontoImpuesto AS MontoVoucher,
											(SELECT pc2.TipoSaldo
											 FROM
												mastimpuestos i2
												INNER JOIN pv_partida pv2 ON (pv2.cod_partida = i2.cod_partida)
												INNER JOIN ac_mastplancuenta20 pc2 ON (pc2.CodCuenta = pv2.CtaOrdPagoPub20)
											 WHERE CodImpuesto = '".$_PARAMETRO["IGVCODIGO"]."') AS TipoSaldo,
											(SELECT pc2.FlagReqCC
											 FROM
												mastimpuestos i2
												INNER JOIN pv_partida pv2 ON (pv2.cod_partida = i2.cod_partida)
												INNER JOIN ac_mastplancuenta20 pc2 ON (pc2.CodCuenta = pv2.CtaOrdPagoPub20)
											 WHERE CodImpuesto = '".$_PARAMETRO["IGVCODIGO"]."') AS FlagReqCC,
											'04' AS Orden,
											'Debe' AS Columna
										 FROM ap_obligaciones oc
										 WHERE
											oc.CodProveedor = '".$fo['CodProveedor']."' AND
											oc.CodTipoDocumento = '".$fo['CodTipoDocumento']."' AND
											oc.NroDocumento = '".$fo['NroDocumento']."' AND
											oc.MontoImpuesto > 0
										 GROUP BY CodCuenta)
										ORDER BY Columna DESC, Orden, CodCuenta";
							}
							else
							{
								$sql = "(SELECT
											cb.CodCuentaPub20 AS CodCuenta,
											o.ReferenciaTipoDocumento AS TipoOrden,
											o.ReferenciaNroDocumento AS NroOrden,
											pc.Descripcion AS NomCuenta,
											$MontoVoucher1
											pc.TipoSaldo,
			                                pc.FlagReqCC,
											'01' AS Orden,
											'Haber' AS Columna
										 FROM
											ap_obligaciones o
											INNER JOIN ap_ctabancaria cb ON (o.NroCuenta = cb.NroCuenta)
											INNER JOIN ac_mastplancuenta20 pc ON (cb.CodCuentaPub20 = pc.CodCuenta)
										 WHERE
											o.CodProveedor = '".$fo['CodProveedor']."' AND
											o.CodTipoDocumento = '".$fo['CodTipoDocumento']."' AND
											o.NroDocumento = '".$fo['NroDocumento']."'
										 GROUP BY CodCuenta)
										UNION
										(SELECT
											i.CodCuentaPub20 AS CodCuenta,
											o.ReferenciaTipoDocumento AS TipoOrden,
											o.ReferenciaNroDocumento AS NroOrden,
											pc.Descripcion AS NomCuenta,
											ABS(SUM(oc.MontoImpuesto)) AS MontoVoucher,
											pc.TipoSaldo,
			                                pc.FlagReqCC,
											'02' AS Orden,
											'Haber' AS Columna
										 FROM
											ap_obligacionesimpuesto oc
											INNER JOIN ap_obligaciones o ON (oc.CodProveedor = o.CodProveedor AND
																			 oc.CodTipoDocumento = o.CodTipoDocumento AND
																			 oc.NroDocumento = o.NroDocumento)
											INNER JOIN mastimpuestos i ON (oc.CodImpuesto = i.CodImpuesto)
											INNER JOIN ac_mastplancuenta20 pc ON (i.CodCuentaPub20 = pc.CodCuenta)
										 WHERE
											i.FlagProvision = 'P' AND
											oc.CodProveedor = '".$fo['CodProveedor']."' AND
											oc.CodTipoDocumento = '".$fo['CodTipoDocumento']."' AND
											oc.NroDocumento = '".$fo['NroDocumento']."'
										 GROUP BY CodCuenta)
										UNION
										(SELECT
											p.CtaOrdPagoPub20 AS CodCuenta,
											oc.TipoOrden,
											oc.NroOrden,
											pc.Descripcion AS NomCuenta,
											$MontoVoucher2
											pc.TipoSaldo,
			                                pc.FlagReqCC,
											'03' AS Orden,
											'Debe' AS Columna
										 FROM
											ap_obligacionescuenta oc
											INNER JOIN pv_partida p ON (p.cod_partida = oc.cod_partida)
											INNER JOIN ac_mastplancuenta20 pc ON (pc.CodCuenta = p.CtaOrdPagoPub20)
											INNER JOIN ap_obligaciones o ON (o.CodProveedor = oc.CodProveedor AND
																			 o.CodTipoDocumento = oc.CodTipoDocumento AND
																			 o.NroDocumento = oc.NroDocumento)

											LEFT JOIN ap_ordenpago op ON (op.CodProveedor = o.CodProveedor AND
																		  op.CodTipoDocumento = o.CodTipoDocumento AND
																		  op.NroDocumento = o.NroDocumento AND
																		  (op.Estado = 'PA' OR op.Estado = 'PP'))
											LEFT JOIN ap_pagos pg ON (op.NroProceso = pg.NroProceso AND op.Secuencia = pg.Secuencia)
											LEFT JOIN ap_ordenpagodistribucion opd ON (opd.Anio = op.Anio AND
																					   opd.CodOrganismo = op.CodOrganismo AND
																					   opd.NroOrden = op.NroOrden AND
																					   opd.CodPresupuesto = oc.CodPresupuesto AND
																					   opd.CodFuente = oc.CodFuente AND
																					   opd.cod_partida = oc.cod_partida AND
																					   opd.PagoNroProceso = pg.NroProceso AND
																					   opd.PagoSecuencia = pg.Secuencia)
										 WHERE
											oc.CodProveedor = '".$fo['CodProveedor']."' AND
											oc.CodTipoDocumento = '".$fo['CodTipoDocumento']."' AND
											oc.NroDocumento = '".$fo['NroDocumento']."'
										 GROUP BY CodCuenta)
										UNION
										(SELECT
											(SELECT pc2.CodCuenta
											 FROM
												mastimpuestos i2
												INNER JOIN pv_partida pv2 ON (pv2.cod_partida = i2.cod_partida)
												INNER JOIN ac_mastplancuenta20 pc2 ON (pc2.CodCuenta = pv2.CtaOrdPagoPub20)
											 WHERE CodImpuesto = '".$_PARAMETRO["IGVCODIGO"]."') AS CodCuenta,
											oc.ReferenciaTipoDocumento AS TipoOrden,
											oc.ReferenciaNroDocumento AS NroOrden,
											(SELECT pc2.Descripcion
											 FROM
												mastimpuestos i2
												INNER JOIN pv_partida pv2 ON (pv2.cod_partida = i2.cod_partida)
												INNER JOIN ac_mastplancuenta20 pc2 ON (pc2.CodCuenta = pv2.CtaOrdPagoPub20)
											 WHERE CodImpuesto = '".$_PARAMETRO["IGVCODIGO"]."') AS NomCuenta,
											oc.MontoImpuesto AS MontoVoucher,
											(SELECT pc2.TipoSaldo
											 FROM
												mastimpuestos i2
												INNER JOIN pv_partida pv2 ON (pv2.cod_partida = i2.cod_partida)
												INNER JOIN ac_mastplancuenta20 pc2 ON (pc2.CodCuenta = pv2.CtaOrdPagoPub20)
											 WHERE CodImpuesto = '".$_PARAMETRO["IGVCODIGO"]."') AS TipoSaldo,
											(SELECT pc2.FlagReqCC
											 FROM
												mastimpuestos i2
												INNER JOIN pv_partida pv2 ON (pv2.cod_partida = i2.cod_partida)
												INNER JOIN ac_mastplancuenta20 pc2 ON (pc2.CodCuenta = pv2.CtaOrdPagoPub20)
											 WHERE CodImpuesto = '".$_PARAMETRO["IGVCODIGO"]."') AS FlagReqCC,
											'04' AS Orden,
											'Debe' AS Columna
										 FROM ap_obligaciones oc
										 WHERE
											oc.CodProveedor = '".$fo['CodProveedor']."' AND
											oc.CodTipoDocumento = '".$fo['CodTipoDocumento']."' AND
											oc.NroDocumento = '".$fo['NroDocumento']."' AND
											oc.MontoImpuesto > 0
										 GROUP BY CodCuenta)
										ORDER BY Columna DESC, Orden, CodCuenta";
							}
						}
					} else {
						$sql = "(SELECT
									cb.CodCuentaPub20 AS CodCuenta,
									o.ReferenciaTipoDocumento AS TipoOrden,
									o.ReferenciaNroDocumento AS NroOrden,
									pc.Descripcion AS NomCuenta,
									(o.MontoObligacion) AS MontoVoucher,
									pc.TipoSaldo,
	                                pc.FlagReqCC,
									'01' AS Orden,
									'Haber' AS Columna
								 FROM
									ap_obligaciones o
									INNER JOIN ap_ctabancaria cb ON (o.NroCuenta = cb.NroCuenta)
									INNER JOIN ac_mastplancuenta20 pc ON (cb.CodCuentaPub20 = pc.CodCuenta)
								 WHERE
									o.CodProveedor = '".$fo['CodProveedor']."' AND
									o.CodTipoDocumento = '".$fo['CodTipoDocumento']."' AND
									o.NroDocumento = '".$fo['NroDocumento']."'
								 GROUP BY CodCuenta)
								UNION
								(SELECT
									i.CodCuentaPub20 AS CodCuenta,
									o.ReferenciaTipoDocumento AS TipoOrden,
									o.ReferenciaNroDocumento AS NroOrden,
									pc.Descripcion AS NomCuenta,
									ABS(SUM(oc.MontoImpuesto)) AS MontoVoucher,
									pc.TipoSaldo,
	                                pc.FlagReqCC,
									'02' AS Orden,
									'Haber' AS Columna
								 FROM
									ap_obligacionesimpuesto oc
									INNER JOIN ap_obligaciones o ON (oc.CodProveedor = o.CodProveedor AND
																	 oc.CodTipoDocumento = o.CodTipoDocumento AND
																	 oc.NroDocumento = o.NroDocumento)
									INNER JOIN mastimpuestos i ON (oc.CodImpuesto = i.CodImpuesto)
									INNER JOIN ac_mastplancuenta20 pc ON (i.CodCuentaPub20 = pc.CodCuenta)
								 WHERE
									i.FlagProvision = 'P' AND
									oc.CodProveedor = '".$fo['CodProveedor']."' AND
									oc.CodTipoDocumento = '".$fo['CodTipoDocumento']."' AND
									oc.NroDocumento = '".$fo['NroDocumento']."'
								 GROUP BY CodCuenta)
								UNION
								(SELECT
									(SELECT pc2.CodCuenta
									 FROM
										mastimpuestos i2
										INNER JOIN pv_partida pv2 ON (pv2.cod_partida = i2.cod_partida)
										INNER JOIN ac_mastplancuenta20 pc2 ON (pc2.CodCuenta = pv2.CtaOrdPagoPub20)
									 WHERE CodImpuesto = '".$_PARAMETRO["IGVCODIGO"]."') AS CodCuenta,
									oc.ReferenciaTipoDocumento AS TipoOrden,
									oc.ReferenciaNroDocumento AS NroOrden,
									(SELECT pc2.Descripcion
									 FROM
										mastimpuestos i2
										INNER JOIN pv_partida pv2 ON (pv2.cod_partida = i2.cod_partida)
										INNER JOIN ac_mastplancuenta20 pc2 ON (pc2.CodCuenta = pv2.CtaOrdPagoPub20)
									 WHERE CodImpuesto = '".$_PARAMETRO["IGVCODIGO"]."') AS NomCuenta,
									oc.MontoImpuesto AS MontoVoucher,
									(SELECT pc2.TipoSaldo
									 FROM
										mastimpuestos i2
										INNER JOIN pv_partida pv2 ON (pv2.cod_partida = i2.cod_partida)
										INNER JOIN ac_mastplancuenta20 pc2 ON (pc2.CodCuenta = pv2.CtaOrdPagoPub20)
									 WHERE CodImpuesto = '".$_PARAMETRO["IGVCODIGO"]."') AS TipoSaldo,
									(SELECT pc2.FlagReqCC
									 FROM
										mastimpuestos i2
										INNER JOIN pv_partida pv2 ON (pv2.cod_partida = i2.cod_partida)
										INNER JOIN ac_mastplancuenta20 pc2 ON (pc2.CodCuenta = pv2.CtaOrdPagoPub20)
									 WHERE CodImpuesto = '".$_PARAMETRO["IGVCODIGO"]."') AS FlagReqCC,
									'04' AS Orden,
									'Debe' AS Columna
								 FROM ap_obligaciones oc
								 WHERE
									oc.CodProveedor = '".$fo['CodProveedor']."' AND
									oc.CodTipoDocumento = '".$fo['CodTipoDocumento']."' AND
									oc.NroDocumento = '".$fo['NroDocumento']."' AND
									oc.MontoImpuesto > 0
								 GROUP BY CodCuenta)
								UNION
								(SELECT
									pc.CodCuenta,
									oc.TipoOrden,
									oc.NroOrden,
									pc.Descripcion AS NomCuenta,
									(SUM(oc.Monto) - ".floatval(abs($field_impuesto['Monto']+$field_impuesto2['Monto'])).") AS MontoVoucher,
									pc.TipoSaldo,
	                                pc.FlagReqCC,
									'05' AS Orden,
									'Debe' AS Columna
								 FROM
									ap_obligacionescuenta oc
									INNER JOIN ac_mastplancuenta20 pc ON (oc.CodCuentaPub20 = pc.CodCuenta)
								 WHERE
									oc.CodProveedor = '".$fo['CodProveedor']."' AND
									oc.CodTipoDocumento = '".$fo['CodTipoDocumento']."' AND
									oc.NroDocumento = '".$fo['NroDocumento']."'
								 GROUP BY CodCuenta)
								ORDER BY Columna DESC, Orden, CodCuenta";
					}
					$field_det = getRecords($sql);
					foreach ($field_det as $fd) {
						$CodCuenta = $fd['CodCuenta'];
						$_CodCuenta[$CodCuenta] = $CodCuenta;
						$_Descripcion[$CodCuenta] = $fd['NomCuenta'];
						$_MontoVoucher[$CodCuenta] += $fd['MontoVoucher'];
						$_CodPersona[$CodCuenta] = $fo['CodProveedor'];
						$_ReferenciaNroDocumento[$CodCuenta] = $fo['NroOrden'];
						$_FechaVoucher[$CodCuenta] = $fo['FechaPago'];
						$_Columna[$CodCuenta] = $fd['Columna'];
                    	if ($fd['FlagReqCC'] == "S") $_CodCentroCosto[$CodCuenta] = $_PARAMETRO['CCOSTOVOUCHER']; else $_CodCentroCosto[$CodCuenta] = $fo['CodCentroCosto'];
					}
				}

				foreach ($_CodCuenta as $CodCuenta) {
					if ($_Columna[$CodCuenta] == "Haber") {
						$style = " color:red;";
						$_MontoVoucher[$CodCuenta] = abs($_MontoVoucher[$CodCuenta]) * (-1);
						$Debitos += $_MontoVoucher[$CodCuenta];
					} else {
						$style = "";
						$_MontoVoucher[$CodCuenta] = abs($_MontoVoucher[$CodCuenta]);
						$Creditos += $_MontoVoucher[$CodCuenta];
					}
					?>
					<tr class="trListaBody">
                    	<td>
                            <input type="text" name="Linea" value="<?=++$Linea?>" class="cell2" style="text-align:center;" readonly />
                        </td>
                    	<td>
                        	<input type="text" name="CodCuenta" value="<?=$_CodCuenta[$CodCuenta]?>" class="cell2" readonly />
                        </td>
                    	<td>
                        	<input type="text" name="Descripcion" value="<?=htmlentities($_Descripcion[$CodCuenta])?>" class="cell2" readonly />
                        </td>
                    	<td>
                        	<input type="text" name="MontoVoucher" value="<?=number_format($_MontoVoucher[$CodCuenta], 2, ',', '.')?>" class="cell2" style="text-align:right; <?=$style?>" readonly />
                        </td>
                    	<td>
                        	<input type="text" name="CodPersona" value="<?=$_CodPersona[$CodCuenta]?>" class="cell2" style="text-align:center;" readonly />
                        </td>
                    	<td width="25">
                        	<input type="text" name="ReferenciaTipoDocumento" value="OP" class="cell2" readonly />
                        </td>
                    	<td width="125">
                        	<input type="text" name="ReferenciaNroDocumento" value="<?=$_ReferenciaNroDocumento[$CodCuenta]?>" class="cell2" readonly />
                        </td>
                    	<td>
                        	<input type="text" name="CodCentroCosto" value="<?=$_CodCentroCosto[$CodCuenta]?>" class="cell2" style="text-align:center;" readonly />
                        </td>
                    	<td>
                        	<input type="text" name="FechaVoucher" value="<?=formatFechaDMA($_FechaVoucher[$CodCuenta])?>" class="cell2" style="text-align:center;" readonly />
                        </td>
					</tr>
					<?php
				}
				?>


                </tbody>
            </table>
            </div></td></tr></table>
            
            <table>
                <tr>
                    <th width="140">Nro Lineas: <input type="text" id="Lineas" value="<?=$Linea?>" class="cell2" style="text-align:center; font-weight:bold; font-size:12px; width:20px;" readonly /></th>
                    <th width="75">&nbsp;</th>
                    <th width="150">&nbsp;</th>
                    <th width="75">Total:</th>
                    <th width="125">
                    	<input type="text" id="Creditos" value="<?=number_format($Creditos, 2, ',', '.')?>" class="cell2" style="text-align:right; font-weight:bold; font-size:12px;" readonly />
                    </th>
                    <th width="125">
                    	<input type="text" id="Debitos" value="<?=number_format($Debitos, 2, ',', '.')?>" class="cell2" style="text-align:right; font-weight:bold; font-size:12px; color:red;" readonly />
					</th>
                    <th width="125">&nbsp;</th>
                </tr>
			</table>
            
        </td>
    </tr>
</table>
</form>

<?php
if ($opcion != "ver") {
	?>
    <!-- JS	-->
    <script type="text/javascript" charset="utf-8">
	    $(document).ready(function() {
	        validarErroresVoucher();
	    });
	    function setPeriodo(FechaVoucher) {
	        var partes = FechaVoucher.split("-");
	        var Periodo = partes[2] + "-" + partes[1];
	        $('#Periodo').val(Periodo);
	    }
    </script>
    <?php
}
?>