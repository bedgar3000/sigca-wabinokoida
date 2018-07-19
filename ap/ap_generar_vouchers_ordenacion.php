<?php
//	consulto datos generales de la transaccion
list($CodOrganismo, $CodProveedor, $CodTipoDocumento, $NroDocumento, $CodVoucher) = split("[_]", $registro);
$sql = "SELECT 
			o.*,
			(o.MontoObligacion - o.MontoAdelanto) AS MontoPagar,
			(o.MontoObligacion - o.MontoAdelanto - MontoPagoParcial) AS MontoPendiente,
			mp3.CodPersona AS PreparadoPor,
			mp3.NomCompleto AS NomPreparadoPor,
			mp4.CodPersona AS AprobadoPor,
			mp4.NomCompleto AS NomAprobadoPor,
			cc.Abreviatura AS NomCentroCosto,
			td.CodVoucherOrdPago AS CodVoucher
		FROM 
			ap_obligaciones o
			INNER JOIN mastpersonas mp ON (o.CodProveedor = mp.CodPersona)
			INNER JOIN mastpersonas mp2 ON (o.CodProveedorPagar = mp2.CodPersona)
			INNER JOIN ap_tipodocumento td ON (o.CodTipoDocumento = td.CodTipoDocumento)
			LEFT JOIN mastpersonas mp3 ON (o.IngresadoPor = mp3.CodPersona)
			LEFT JOIN mastpersonas mp4 ON (o.AprobadoPor = mp4.CodPersona)
			LEFT JOIN ac_mastcentrocosto cc ON (cc.CodCentroCosto = o.CodCentroCosto)
		WHERE 
			o.CodOrganismo = '".$CodOrganismo."' AND
			o.CodProveedor = '".$CodProveedor."' AND
			o.CodTipoDocumento = '".$CodTipoDocumento."' AND
			o.NroDocumento = '".$NroDocumento."'";
$query_mast = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
if (mysql_num_rows($query_mast) != 0) $field_mast = mysql_fetch_array($query_mast);

//	consulto si el periodo esta abierto
$sql = "SELECT Estado
		FROM ac_controlcierremensual
		WHERE
			TipoRegistro = 'AB' AND
			CodOrganismo = '".$field_mast['CodOrganismo']."' AND
			Periodo = '".substr($field_mast['FechaAprobado'], 0, 7)."'";
$query_periodo = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
if (mysql_num_rows($query_periodo) != 0) $field_periodo = mysql_fetch_array($query_periodo);

//	sistema fuente
$sql = "SELECT CodSistemaFuente FROM mastaplicaciones WHERE CodAplicacion = 'AP'";
$query_prefpa = mysql_query($sql) or die ($sql.mysql_error());
if (mysql_num_rows($query_prefpa) != 0) $field_prefpa = mysql_fetch_array($query_prefpa);
?>
<form name="frmentrada" id="frmentrada" method="POST" onsubmit="return vouchers(this, 'ordenacion-pub20', 'li2');">
<input type="hidden" id="CodOrganismo" value="<?=$CodOrganismo?>" />
<input type="hidden" id="CodProveedor" value="<?=$CodProveedor?>" />
<input type="hidden" id="CodTipoDocumento" value="<?=$CodTipoDocumento?>" />
<input type="hidden" id="NroDocumento" value="<?=$NroDocumento?>" />
<input type="hidden" id="PeriodoEstado" value="<?=($field_periodo['Estado'])?>" />
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
                    <th width="75" scope="col">Periodo</th>
                    <th width="75" scope="col">Voucher</th>
                    <th width="75" scope="col">Fecha</th>
                    <th width="75" scope="col">Status</th>
                    <th scope="col">Organismo</th>
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
                    <th width="50" scope="col">Linea</th>
                    <th scope="col">Errores Encontrados</th>
                    <th width="75" scope="col">Periodo</th>
                    <th width="75" scope="col">Voucher</th>
                    <th width="75" scope="col">Organismo</th>
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
                        <select id="CodOrganismo" style="width:300px;">
                            <?=loadSelect("mastorganismos", "CodOrganismo", "Organismo", $field_mast['CodOrganismo'], 1)?>
                        </select>
                    </td>
                    <td class="tagForm">Descripci&oacute;n:</td>
                    <td><input type="text" id="ComentariosVoucher" style="width:305px;" value="<?=htmlentities($field_mast['Comentarios'])?>" /></td>
                </tr>
                <tr>
                    <td class="tagForm">* Fecha:</td>
                    <td><input type="text" id="FechaVoucher" value="<?=formatFechaDMA($field_mast['FechaAprobado'])?>" style="width:75px;" class="datepicker" onchange="setPeriodo(this.value);" /></td>
                    <td class="tagForm">Preparado Por:</td>
                    <td>
                        <input type="hidden" id="PreparadoPor" value="<?=$field_mast['PreparadoPor']?>" />
                        <input type="text" style="width:235px;" value="<?=htmlentities($field_mast['NomPreparadoPor'])?>" disabled />
                        <input type="text" id="FechaPreparacion" style="width:60px;" value="<?=formatFechaDMA($field_mast['FechaRevision'])?>" disabled />
                    </td>
                </tr>
                <tr>
                    <td class="tagForm">Voucher:</td>
                    <td>
						<input type="text" id="Periodo" value="<?=substr($field_mast['FechaAprobado'], 0, 7)?>" style="width:50px;" disabled />
                        <select id="CodVoucher">
                            <?=loadSelect("ac_voucher", "CodVoucher", "CodVoucher", $field_mast['CodVoucher'], 1)?>
                        </select>
                        <input type="text" id="NroVoucher" style="width:50px;" disabled="disabled" />
                    </td>
                    <td class="tagForm">Aprobado Por:</td>
                    <td>
                        <input type="hidden" id="AprobadoPor" value="<?=$field_mast['AprobadoPor']?>" />
                        <input type="text" style="width:235px;" value="<?=htmlentities($field_mast['NomAprobadoPor'])?>" disabled />
                        <input type="text" id="FechaAprobacion" style="width:60px;" value="<?=formatFechaDMA($field_mast['FechaAprobado'])?>" disabled />
                    </td>
                </tr>
                <tr>
                    <td class="tagForm">* Libro Contable:</td>
                    <td>
                        <select id="CodLibroCont" style="width:150px;">
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
                        <input type="submit" class="btLista" value="Aceptar" id="btAceptar" />
                        <input type="button" class="btLista" value="Rechazar" onclick="parent.$.prettyPhoto.close();" />
                    </td>

                </tr>
            </table>
            
            <table><tr><td><div style="overflow:scroll; width:960px; height:175px;">
            <table width="1100" class="tblLista">
            	<thead>
                <tr>
                    <th scope="col" width="30">#</th>
                    <th scope="col" width="110">Cuenta</th>
                    <th scope="col">Descripci&oacute;n</th>
                    <th scope="col" width="125">Monto</th>
                    <th scope="col" width="75">Persona</th>
                    <th scope="col" colspan="2">Documento</th>
                    <th scope="col" width="75">C.Costo</th>
                    <th scope="col" width="75">Fecha</th>
                </tr>
                </thead>
                
                <tbody>
                <?php
				//	impuestos FlagProvision=N
				$sql = "SELECT SUM(oi1.MontoImpuesto) AS Monto
						FROM
							ap_obligacionesimpuesto oi1
							INNER JOIN ap_obligaciones o1 ON (oi1.CodProveedor = o1.CodProveedor AND
															  oi1.CodTipoDocumento = o1.CodTipoDocumento AND
															  oi1.NroDocumento = o1.NroDocumento)
						WHERE
							oi1.FlagProvision = 'N' AND
							oi1.CodProveedor = '".$CodProveedor."' AND
							oi1.CodTipoDocumento = '".$CodTipoDocumento."' AND
							oi1.NroDocumento = '".$NroDocumento."'
						GROUP BY oi1.FlagProvision, oi1.CodProveedor, oi1.CodTipoDocumento, oi1.NroDocumento";
				$query_impueston = mysql_query($sql) or die ($sql.mysql_error());
				if (mysql_num_rows($query_impueston) != 0) $field_impueston = mysql_fetch_array($query_impueston);
				
				//	impuestos FlagProvision=P
				$sql = "SELECT SUM(oi1.MontoImpuesto) AS Monto
						FROM
							ap_obligacionesimpuesto oi1
							INNER JOIN ap_obligaciones o1 ON (oi1.CodProveedor = o1.CodProveedor AND
															  oi1.CodTipoDocumento = o1.CodTipoDocumento AND
															  oi1.NroDocumento = o1.NroDocumento)
						WHERE
							oi1.FlagProvision = 'P' AND
							oi1.CodProveedor = '".$CodProveedor."' AND
							oi1.CodTipoDocumento = '".$CodTipoDocumento."' AND
							oi1.NroDocumento = '".$NroDocumento."'
						GROUP BY oi1.FlagProvision, oi1.CodProveedor, oi1.CodTipoDocumento, oi1.NroDocumento";
				$query_impuestop = mysql_query($sql) or die ($sql.mysql_error());
				if (mysql_num_rows($query_impuestop) != 0) $field_impuestop = mysql_fetch_array($query_impuestop);
				
				//	impuestos FlagProvision=A
				$sql = "SELECT SUM(oi1.MontoImpuesto) AS Monto
						FROM
							ap_obligacionesimpuesto oi1
							INNER JOIN ap_obligaciones o1 ON (oi1.CodProveedor = o1.CodProveedor AND
															  oi1.CodTipoDocumento = o1.CodTipoDocumento AND
															  oi1.NroDocumento = o1.NroDocumento)
						WHERE
							oi1.FlagProvision = 'A' AND
							oi1.CodProveedor = '".$CodProveedor."' AND
							oi1.CodTipoDocumento = '".$CodTipoDocumento."' AND
							oi1.NroDocumento = '".$NroDocumento."'
						GROUP BY oi1.FlagProvision, oi1.CodProveedor, oi1.CodTipoDocumento, oi1.NroDocumento";
				$query_impuestoa = mysql_query($sql) or die ($sql.mysql_error());
				if (mysql_num_rows($query_impuestoa) != 0) $field_impuestoa = mysql_fetch_array($query_impuestoa);

				if ($field_mast['FlagAgruparIgv'] == 'S') {
				
					$sql = "(SELECT
								td.CodCuentaProvPub20 AS CodCuenta,
								oc.ReferenciaTipoDocumento AS TipoOrden,
								oc.ReferenciaNroDocumento AS NroOrden,
								pc.Descripcion AS NomCuenta,
								(oc.MontoObligacion + ".abs($field_impueston['Monto'])." + ".abs($field_impuestop['Monto'])." + ".abs($field_impuestoa['Monto']).") AS MontoVoucher,
								pc.TipoSaldo,
	                            pc.FlagReqCC,
								'01' AS Orden,
								'Debe' AS Columna
							 FROM
								ap_obligaciones oc
								INNER JOIN ap_tipodocumento td ON (oc.CodTipoDocumento = td.CodTipoDocumento)
								INNER JOIN ac_mastplancuenta20 pc ON (td.CodCuentaProvPub20 = pc.CodCuenta)
							 WHERE
								oc.CodProveedor = '".$CodProveedor."' AND
								oc.CodTipoDocumento = '".$CodTipoDocumento."' AND
								oc.NroDocumento = '".$NroDocumento."'
							 GROUP BY CodCuenta)
							UNION
							(SELECT
								p.CtaOrdPagoPub20 AS CodCuenta,
								oc.TipoOrden,
								oc.NroOrden,
								pc.Descripcion AS NomCuenta,
								(SUM(oc.Monto) - ".abs($field_impueston['Monto']).") AS MontoVoucher,
								pc.TipoSaldo,
	                            pc.FlagReqCC,
								'02' AS Orden,
								'Haber' AS Columna
							 FROM
								ap_obligacionescuenta oc
								INNER JOIN pv_partida p ON (p.cod_partida = oc.cod_partida)
								INNER JOIN ac_mastplancuenta20 pc ON (pc.CodCuenta = p.CtaOrdPagoPub20)
								INNER JOIN ap_obligaciones o ON (o.CodProveedor = oc.CodProveedor AND
																 o.CodTipoDocumento = oc.CodTipoDocumento AND
																 o.NroDocumento = oc.NroDocumento)
							 WHERE
								oc.CodProveedor = '".$CodProveedor."' AND
								oc.CodTipoDocumento = '".$CodTipoDocumento."' AND
								oc.NroDocumento = '".$NroDocumento."'
							 GROUP BY CodCuenta)
							UNION
							(SELECT
								oc.CodCuentaPub20 AS CodCuenta,
								o.ReferenciaTipoDocumento AS TipoOrden,
								o.ReferenciaNroDocumento AS NroOrden,
								pc.Descripcion AS NomCuenta,
								ABS(SUM(oc.MontoImpuesto)) AS MontoVoucher,
								pc.TipoSaldo,
	                            pc.FlagReqCC,
								'04' AS Orden,
								'Haber' AS Columna
							 FROM
								ap_obligacionesimpuesto oc
								INNER JOIN ap_obligaciones o ON (oc.CodProveedor = o.CodProveedor AND
																 oc.CodTipoDocumento = o.CodTipoDocumento AND
																 oc.NroDocumento = o.NroDocumento)
								INNER JOIN ac_mastplancuenta20 pc ON (oc.CodCuentaPub20 = pc.CodCuenta)
							 WHERE
								oc.FlagProvision = 'N' AND
								oc.CodProveedor = '".$CodProveedor."' AND
								oc.CodTipoDocumento = '".$CodTipoDocumento."' AND
								oc.NroDocumento = '".$NroDocumento."'
							 GROUP BY CodCuenta)
							ORDER BY Columna DESC, Orden, CodCuenta";
				} else {
					$sql = "(SELECT
								td.CodCuentaProvPub20 AS CodCuenta,
								oc.ReferenciaTipoDocumento AS TipoOrden,
								oc.ReferenciaNroDocumento AS NroOrden,
								pc.Descripcion AS NomCuenta,
								(oc.MontoObligacion + ".abs($field_impueston['Monto'])." + ".abs($field_impuestop['Monto'])." + ".abs($field_impuestoa['Monto']).") AS MontoVoucher,
								pc.TipoSaldo,
	                            pc.FlagReqCC,
								'01' AS Orden,
								'Debe' AS Columna
							 FROM
								ap_obligaciones oc
								INNER JOIN ap_tipodocumento td ON (oc.CodTipoDocumento = td.CodTipoDocumento)
								INNER JOIN ac_mastplancuenta20 pc ON (td.CodCuentaProvPub20 = pc.CodCuenta)
							 WHERE
								oc.CodProveedor = '".$CodProveedor."' AND
								oc.CodTipoDocumento = '".$CodTipoDocumento."' AND
								oc.NroDocumento = '".$NroDocumento."'
							 GROUP BY CodCuenta)
							UNION
							(SELECT
								p.CtaOrdPagoPub20 AS CodCuenta,
								oc.TipoOrden,
								oc.NroOrden,
								pc.Descripcion AS NomCuenta,
								(SUM(oc.Monto) - ".abs($field_impueston['Monto']).") AS MontoVoucher,
								pc.TipoSaldo,
	                            pc.FlagReqCC,
								'02' AS Orden,
								'Haber' AS Columna
							 FROM
								ap_obligacionescuenta oc
								INNER JOIN pv_partida p ON (p.cod_partida = oc.cod_partida)
								INNER JOIN ac_mastplancuenta20 pc ON (pc.CodCuenta = p.CtaOrdPagoPub20)
								INNER JOIN ap_obligaciones o ON (o.CodProveedor = oc.CodProveedor AND
																 o.CodTipoDocumento = oc.CodTipoDocumento AND
																 o.NroDocumento = oc.NroDocumento)
							 WHERE
								oc.CodProveedor = '".$CodProveedor."' AND
								oc.CodTipoDocumento = '".$CodTipoDocumento."' AND
								oc.NroDocumento = '".$NroDocumento."'
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
								'03' AS Orden,
								'Haber' AS Columna
							 FROM ap_obligaciones oc
							 WHERE
								oc.CodProveedor = '".$CodProveedor."' AND
								oc.CodTipoDocumento = '".$CodTipoDocumento."' AND
								oc.NroDocumento = '".$NroDocumento."' AND
								oc.MontoImpuesto > 0
							 GROUP BY CodCuenta)
							UNION
							(SELECT
								oc.CodCuentaPub20 AS CodCuenta,
								o.ReferenciaTipoDocumento AS TipoOrden,
								o.ReferenciaNroDocumento AS NroOrden,
								pc.Descripcion AS NomCuenta,
								ABS(SUM(oc.MontoImpuesto)) AS MontoVoucher,
								pc.TipoSaldo,
	                            pc.FlagReqCC,
								'04' AS Orden,
								'Haber' AS Columna
							 FROM
								ap_obligacionesimpuesto oc
								INNER JOIN ap_obligaciones o ON (oc.CodProveedor = o.CodProveedor AND
																 oc.CodTipoDocumento = o.CodTipoDocumento AND
																 oc.NroDocumento = o.NroDocumento)
								INNER JOIN ac_mastplancuenta20 pc ON (oc.CodCuentaPub20 = pc.CodCuenta)
							 WHERE
								oc.FlagProvision = 'N' AND
								oc.CodProveedor = '".$CodProveedor."' AND
								oc.CodTipoDocumento = '".$CodTipoDocumento."' AND
								oc.NroDocumento = '".$NroDocumento."'
							 GROUP BY CodCuenta)
							ORDER BY Columna DESC, Orden, CodCuenta";
				}
				$query_det = mysql_query($sql) or die ($sql.mysql_error());
				while($field_det = mysql_fetch_array($query_det)) {
					$Monto = $field_det['MontoVoucher'];
					if ($field_det['Columna'] == "Haber") {
						$style = " color:red;";
						$Monto = abs($Monto) * (-1);
						$Debitos += $Monto;
					} else {
						$style = "";
						$Monto = abs($Monto);
						$Creditos += $Monto;
					}
                    if ($field_det['FlagReqCC'] == "S") $_CodCentroCosto = $_PARAMETRO['CCOSTOVOUCHER']; else $_CodCentroCosto = "";
					?>
					<tr class="trListaBody">
                    	<td>
                        	<input type="text" name="Linea" value="<?=++$Linea?>" class="cell2" style="text-align:center;" readonly />
                        </td>
                    	<td>
                        	<input type="text" name="CodCuenta" value="<?=$field_det['CodCuenta']?>" class="cell2" readonly />
                        </td>
                    	<td>
                        	<input type="text" name="Descripcion" value="<?=htmlentities($field_det['NomCuenta'])?>" class="cell2" readonly />
                        </td>
                    	<td>
                        	<input type="text" name="MontoVoucher" value="<?=number_format($Monto, 2, ',', '.')?>" class="cell2" style="text-align:right; <?=$style?>" readonly />
                        </td>
                    	<td>
                        	<input type="text" name="CodPersona" value="<?=$CodProveedor?>" class="cell2" style="text-align:center;" readonly />
                        </td>
                    	<td width="25">
                        	<input type="text" name="ReferenciaTipoDocumento" value="<?=$CodTipoDocumento?>" class="cell2" readonly />
                        </td>
                    	<td width="125">
                        	<input type="text" name="ReferenciaNroDocumento" value="<?=$NroDocumento?>" class="cell2" readonly />
                        </td>
                    	<td>
                        	<input type="text" name="CodCentroCosto" value="<?=$_CodCentroCosto?>" class="cell2" style="text-align:center;" readonly />
                        </td>
                    	<td>
                        	<input type="text" name="FechaVoucher" value="<?=formatFechaDMA($field_mast['FechaAprobado'])?>" class="cell2" style="width:75px; text-align:center;" readonly />
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
                    <th scope="col" width="140">Nro Lineas: <input type="text" id="Lineas" value="<?=$Linea?>" class="cell2" style="text-align:center; font-weight:bold; font-size:12px; width:20px;" readonly /></th>
                    <th scope="col" width="75">&nbsp;</th>
                    <th scope="col" width="150">&nbsp;</th>
                    <th scope="col" width="75">Total:</th>
                    <th scope="col" width="125">
                    	<input type="text" id="Creditos" value="<?=number_format($Creditos, 2, ',', '.')?>" class="cell2" style="text-align:right; font-weight:bold; font-size:12px;" readonly />
                    </th>
                    <th scope="col" width="125">
                    	<input type="text" id="Debitos" value="<?=number_format($Debitos, 2, ',', '.')?>" class="cell2" style="text-align:right; font-weight:bold; font-size:12px; color:red;" readonly />
					</th>
                    <th scope="col" width="125">&nbsp;</th>
                </tr>
			</table>
            
        </td>
    </tr>
</table>
</form>

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