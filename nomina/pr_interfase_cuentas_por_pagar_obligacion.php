<?php
//	------------------------------------
list($CodProveedor, $CodTipoDocumento, $NroDocumento, $TipoObligacion) = split("[_]", $sel_registros);
//	obligacion
$sql = "SELECT
			o.*,
			p1.DocFiscal,
			p1.NomCompleto,
			p1.Busqueda,
			pv.DiasPago,
			p2.NomCompleto AS NomProveedorPagar,
			td.FlagProvision,
			td.CodVoucher,
            ppto.CategoriaProg
		FROM
			pr_obligaciones o
			INNER JOIN mastpersonas p1 ON (o.CodProveedor = p1.CodPersona)
			INNER JOIN ap_tipodocumento td ON (o.CodTipoDocumento = td.CodTipoDocumento)
			LEFT JOIN mastproveedores pv ON (p1.CodPersona = pv.CodProveedor)
			LEFT JOIN mastpersonas p2 ON (o.CodProveedorPagar = p2.CodPersona)
            LEFT JOIN pv_presupuesto ppto ON (ppto.CodOrganismo = o.CodOrganismo AND ppto.CodPresupuesto = o.CodPresupuesto)
		WHERE
			o.CodProveedor = '".$CodProveedor."' AND
			o.CodTipoDocumento = '".$CodTipoDocumento."' AND
			o.NroDocumento = '".$NroDocumento."'";
$query_obligacion = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
if (mysql_num_rows($query_obligacion)) $field_obligacion = mysql_fetch_array($query_obligacion);
//	------------------------------------
$accion = "nuevo";
$titulo = "Nueva Obligaci&oacute;n";
$label_submit = "Guardar";
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
    $field_obligacion['Ejercicio'] = substr($field_obligacion['Periodo'], 0, 4);
$field_obligacion['Periodo'] = "$AnioActual-$MesActual";
$field_obligacion['FlagGenerarPago'] = "S";
$disabled_impuesto = "disabled";
$disabled_documento = "disabled";
$disabled_distribucion = "disabled";
$disabled_anular = "disabled";
$Anio = "$AnioActual";
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
            <li id="li1" onclick="currentTab('tab', this);" class="current"><a href="#" onclick="mostrarTab('tab', 1, 5);">Informaci&oacute;n General</a></li>
            <li id="li2" onclick="currentTab('tab', this);"><a href="#" onclick="mostrarTab('tab', 2, 5);">Informaci&oacute;n Monetaria</a></li>
            <li id="li3" onclick="currentTab('tab', this);"><a href="#" onclick="mostrarTab('tab', 3, 5);">Dist. Contable y Presup.</a></li>
            <li id="li4" onclick="currentTab('tab', this);"><a href="#" onclick="mostrarTab('tab', 4, 5)">Resumen Contable y Presup.</a></li>
            <li id="li5" onclick="currentTab('tab', this);"><a href="#" onclick="mostrarTab('tab', 5, 5);">Adelantos y Pagos Parciales</a></li>
            </ul>
            </div>
        </td>
    </tr>
</table>

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=pr_interfase_cuentas_por_pagar" method="POST" onsubmit="return interfase_cuentas_por_pagar_generar(this);">
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
<input type="hidden" name="fCodTipoNom" id="fCodTipoNom" value="<?=$fCodTipoNom?>" />
<input type="hidden" name="fPeriodo" id="fPeriodo" value="<?=$fPeriodo?>" />
<input type="hidden" name="fCodTipoProceso" id="fCodTipoProceso" value="<?=$fCodTipoProceso?>" />
<input type="hidden" name="id_tab" id="id_tab" value="<?=$id_tab?>" />
<input type="hidden" id="TipoObligacion" value="<?=$TipoObligacion?>" />
<input type="hidden" id="Anio" value="<?=$Anio?>" />
<input type="hidden" id="Periodo" value="<?=$PeriodoActual?>" />
<input type="hidden" id="PeriodoActual" value="<?=$PeriodoActual?>" />
<input type="hidden" id="CodPresupuesto" value="<?=$field_obligacion['CodPresupuesto']?>" />
<input type="hidden" id="CONTPUB20" value="<?=$_PARAMETRO['CONTPUB20']?>" />
<input type="hidden" id="CONTONCO" value="<?=$_PARAMETRO['CONTONCO']?>" />
<input type="hidden" id="FlagNomina" value="S" />
<input type="hidden" id="NroDocumento" value="<?=$field_obligacion['NroDocumento']?>" />

<div id="tab1" style="display:block;">
    <table width="1100" class="tblForm">
    	<tr>
        	<td colspan="4" class="divFormCaption">Informaci&oacute;n del Proveedor</td>
        </tr>
        <tr>
    		<td class="tagForm" width="125">* Proveedor:</td>
    		<td class="gallery clearfix">
            	<input type="text" id="CodProveedor" value="<?=$field_obligacion['CodProveedor']?>" disabled="disabled" style="width:100px;" />
    			<input type="text" id="NomCompleto" value="<?=htmlentities($field_obligacion['NomCompleto'])?>" disabled="disabled" style="width:250px;" />
            </td>
    		<td class="tagForm" width="125">Dias Pago:</td>
    		<td><input type="text" id="DiasPago" style="width:50px;" value="<?=$field_obligacion['DiasPago']?>" /></td>
    	</tr>
        <tr>
    		<td class="tagForm">R.I.F:</td>
    		<td>
            	<input type="text" id="DocFiscal" style="width:100px;" value="<?=$field_obligacion['DocFiscal']?>" disabled="disabled" />
                <input type="text" id="Busqueda" style="width:250px;" value="<?=($field_obligacion['Busqueda'])?>" disabled="disabled" />
            </td>
    		<td class="tagForm">* Pagar A:</td>
    		<td class="gallery clearfix">
            	<input type="text" id="CodProveedorPagar" value="<?=$field_obligacion['CodProveedorPagar']?>" maxlength="6" style="width:50px;" onchange="getDescripcionLista('accion=getDescripcionPersona&flagproveedor=S&flagempleado=S&flagotros=S', this, 'nompagara');" disabled="disabled" />
    			<input type="text" id="NomProveedorPagar" value="<?=($field_obligacion['NomProveedorPagar'])?>" style="width:250px;" disabled="disabled" />
            </td>
    	</tr>
    	<tr>
    		<td class="tagForm">* Organismo:</td>
    		<td>
            	<select id="CodOrganismo" style="width:300px;" disabled>
                	<?=loadSelect("mastorganismos", "CodOrganismo", "Organismo", $field_obligacion['CodOrganismo'], 1)?>
                </select>
    		</td>
    		<td class="tagForm">* Centro Costo:</td>
    		<td class="gallery clearfix">
            	<input type="text" id="CodCentroCosto" value="<?=$field_obligacion['CodCentroCosto']?>" style="width:50px;" onchange="getDescripcionLista('accion=getDescripcionCCosto', this, 'nomccosto');" disabled="disabled" />
    			<input type="hidden" id="NomCentroCosto" value="<?=($field_obligacion['CodCentroCosto'])?>" />
            </td>
    	</tr>
        <tr>
    		<td class="tagForm">* Tipo de Documento:</td>
    		<td>
            	<select id="CodTipoDocumento" style="width:300px;" disabled>
                    <?=loadSelect("ap_tipodocumento", "CodTipoDocumento", "Descripcion", $field_obligacion['CodTipoDocumento'], 1)?>
                </select>
            </td>
    		<td class="tagForm">Nro. Registro:</td>
    		<td><input type="text" id="NroRegistro" style="width:100px;" class="codigo" disabled="disabled" /></td>
    	</tr>
        <tr>
    		<td class="tagForm">* Nro. Control:</td>
    		<td><input type="text" id="NroControl" maxlength="20" style="width:150px;" value="<?=$field_obligacion['NroDocumento']?>" disabled /></td>
    		<td class="tagForm">* Nro. Factura:</td>
    		<td><input type="text" id="NroFactura" maxlength="20" value="<?=$field_obligacion['NroDocumento']?>" style="width:150px;" disabled /></td>
    	</tr>
        <tr>
    		<td height="22" class="tagForm">Estado:</td>
    		<td>
           	  <input type="hidden" id="Estado" value="<?=$field_obligacion['Estado']?>" />
            	<input type="text" style="width:100px;" class="codigo" value="<?=printValoresGeneral("ESTADO-OBLIGACIONES", $field_obligacion['Estado'])?>" disabled="disabled" />
    		</td>
            <td class="tagForm">Fecha Factura:</td>
    		<td><input type="text" id="FechaFactura" value="<?=formatFechaDMA($field_obligacion['FechaFactura'])?>" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" /></td>
    	</tr>
    </table>

    <table width="1100" class="tblForm">
        <tr>
    		<td width="50%" valign="top">
            	<table width="100%">
                	<tr><td colspan="2" class="divFormCaption">Fechas del Documento</td></tr>
                	<tr>
                    	<td class="tagForm" width="125"><strong>Obligaci&oacute;n:</strong></td>
                        <td><input type="text" id="FechaRegistro" value="<?=formatFechaDMA($field_obligacion['FechaRegistro'])?>" style="width:100px;" class="datepicker codigo" onkeyup="setFechaDMA(this);" onchange="setPresupuesto($('#CodOrganismo').val(), $(this).val(), $('#CodPresupuesto'), $('#Anio')); actualizarMontosObligacion();" /></td>
                    </tr>
                	<tr>
                    	<td class="tagForm">Emisi&oacute;n:</td>
                        <td><input type="text" id="FechaDocumento" value="<?=formatFechaDMA($field_obligacion['FechaDocumento'])?>" style="width:100px;" class="datepicker" onkeyup="setFechaDMA(this);" /></td>
                    </tr>
                	<tr>
                    	<td class="tagForm">Recepci&oacute;n:</td>
                        <td><input type="text" id="FechaRecepcion" value="<?=formatFechaDMA($field_obligacion['FechaRecepcion'])?>" style="width:100px;" class="datepicker" onkeyup="setFechaDMA(this);" /></td>
                    </tr>
                	<tr>
                    	<td class="tagForm">Vencimiento:</td>
                        <td><input type="text" id="FechaVencimiento" value="<?=formatFechaDMA($field_obligacion['FechaVencimiento'])?>" style="width:100px;" class="datepicker" onkeyup="setFechaDMA(this);" /></td>
                    </tr>
                	<tr>
                    	<td class="tagForm">Prog. Pago:</td>
                        <td><input type="text" id="FechaProgramada" value="<?=formatFechaDMA($field_obligacion['FechaProgramada'])?>" style="width:100px;" class="datepicker" onkeyup="setFechaDMA(this);" /></td>
                    </tr>
                </table>
            </td>
            
    		<td width="50%" valign="top">
            	<table width="100%">
                	<tr><td colspan="2" class="divFormCaption">Informaci&oacute;n Adicional</td></tr>
                	<tr>
                    	<td class="tagForm" width="125">* Tipo de Servicio:</td>
                        <td>
                            <select id="CodTipoServicio" style="width:150px;" disabled>
                                <?=loadSelect("masttiposervicio", "CodTipoServicio", "Descripcion", $field_obligacion['CodTipoServicio'], 0)?>
                            </select>
                        </td>
                    </tr>
                	<tr>
                    	<td class="tagForm">* Tipo de Pago:</td>
                        <td>
                            <select id="CodTipoPago" style="width:150px;">
                                <?=loadSelect("masttipopago", "CodTipoPago", "TipoPago", $field_obligacion['CodTipoPago'], 0)?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="tagForm">Ingresado Por:</td>
                        <td>
                            <input type="hidden" id="IngresadoPor" value="<?=$field_obligacion['IngresadoPor']?>" />
                            <input type="text" id="NomIngresadoPor" value="<?=htmlentities($field_obligacion['NomIngresadoPor'])?>" style="width:245px;" disabled="disabled" />
                            <input type="text" id="FechaPreparacion" value="<?=formatFechaDMA($field_obligacion['FechaPreparacion'])?>" style="width:60px;" disabled="disabled" />
                        </td>
                    </tr>
                    <tr>
                        <td class="tagForm">Revisado Por:</td>
                        <td>
                            <input type="hidden" id="RevisadoPor" value="<?=$field_obligacion['RevisadoPor']?>" />
                            <input type="text" id="NomRevisadoPor" value="<?=htmlentities($field_obligacion['NomRevisadoPor'])?>" style="width:245px;" disabled="disabled" />
                            <input type="text" id="FechaRevision" value="<?=formatFechaDMA($field_obligacion['FechaRevision'])?>" style="width:60px;" disabled="disabled" />
                        </td>
                    </tr>
                    <tr>
                        <td class="tagForm">Aprobador CxP:</td>
                        <td>
                            <input type="hidden" id="AprobadoPor" value="<?=$field_obligacion['AprobadoPor']?>" />
                            <input type="text" id="NomAprobadoPor" value="<?=htmlentities($field_obligacion['NomAprobadoPor'])?>" style="width:245px;" disabled="disabled" />
                            <input type="text" id="FechaAprobado" value="<?=formatFechaDMA($field_obligacion['FechaAprobado'])?>" style="width:60px;" disabled="disabled" />
                        </td>
                    </tr>
                </table>
            </td>
    	</tr>
    </table>

    <table width="1100" class="tblForm">
    	<tr>
    		<td class="tagForm" width="125">Glosa del Voucher:</td>
    		<td><input type="text" id="Comentarios" value="<?=($field_obligacion['Comentarios'])?>" style="width:95%;" /></td>
    	</tr>
    	<tr>
    		<td class="tagForm">Comentarios Adicional:</td>
    		<td><textarea id="ComentariosAdicional" style="width:95%; height:45px;"><?=($field_obligacion['ComentariosAdicional'])?></textarea></td>
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
            	<select id="NroCuenta" style="width:200px;">
                    <?=loadSelect("ap_ctabancaria", "NroCuenta", "NroCuenta", $field_obligacion['NroCuenta'], 0)?>
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
            	<input type="checkbox" id="FlagCajaChica" <?=chkFlag($field_obligacion['FlagCajaChica'])?> disabled="disabled" /> Pago con Caja Chica (Efectivo)
            </td>
    		<td class="tagForm">Impuesto:</td>
    		<td>
            	<input type="text" id="MontoImpuesto" value="<?=number_format($field_obligacion['MontoImpuesto'], 2, ',', '.')?>" style="width:150px; text-align:right;" disabled />
            </td>
    	</tr>
        <tr>
    		<td class="tagForm">&nbsp;</td>
    		<td>
            	<input type="checkbox" id="FlagPagoIndividual" <?=chkFlag($field_obligacion['FlagPagoIndividual'])?> disabled="disabled" /> Preparar Pago Individual
            </td>
    		<td class="tagForm">Otros Impuestos/Retenciones:</td>
    		<td>
            	<input type="text" id="MontoImpuestoOtros" value="<?=number_format($field_obligacion['MontoImpuestoOtros'], 2, ',', '.')?>" style="width:150px; text-align:right;" disabled="disabled" />
            </td>
    	</tr>
        <tr>
    		<td class="tagForm">&nbsp;</td>
    		<td>
            	<input type="checkbox" id="FlagGenerarPago" <?=chkFlag($field_obligacion['FlagGenerarPago'])?> disabled="disabled" /> Preparar Pago (Autom&aacute;tico)
            </td>
    		<td class="tagForm"><strong>Total Obligaci&oacute;n:</strong></td>
    		<td>
            	<input type="text" id="MontoObligacion" value="<?=number_format($field_obligacion['MontoObligacion'], 2, ',', '.')?>" style="width:150px; text-align:right; font-size:12px; font-weight:bold;" class="codigo" disabled="disabled" />
            </td>
    	</tr>
        <tr>
    		<td class="tagForm">&nbsp;</td>
    		<td>
            	<input type="checkbox" id="FlagPagoDiferido" <?=chkFlag($field_obligacion['FlagPagoDiferido'])?> disabled="disabled" /> Diferir el Pago
            </td>
    		<td class="tagForm">Adelanto:</td>
    		<td>
            	<input type="text" id="MontoAdelanto" value="<?=number_format($field_obligacion['MontoAdelanto'], 2, ',', '.')?>" style="width:150px; text-align:right;" disabled="disabled" />
            </td>
    	</tr>
        <tr>
    		<td class="tagForm">&nbsp;</td>
    		<td>
            	<input type="checkbox" id="FlagDiferido" <?=chkFlag($field_obligacion['FlagDiferido'])?> disabled="disabled" /> Considerarlo como Diferido
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
            	<input type="checkbox" id="FlagAfectoIGV" <?=chkFlag($field_obligacion['FlagAfectoIGV'])?> disabled="disabled" /> Afecto a Defracción de IGV
            </td>
    		<td class="tagForm">Pagos Parciales:</td>
    		<td><input type="text" id="MontoPagoParcial" value="<?=number_format($field_obligacion['MontoPagoParcial'], 2, ',', '.')?>" style="width:150px; text-align:right;" disabled="disabled" /></td>
    	</tr>
        <tr>
    		<td class="tagForm">&nbsp;</td>
    		<td>
            	<input type="checkbox" id="FlagCompromiso" <?=chkFlag($field_obligacion['FlagCompromiso'])?> disabled /> Refiere Compromiso
            </td>
    		<td class="tagForm"><strong>Saldo Pendiente:</strong></td>
    		<td>
            	<?php
    			$MontoPendiente = $MontoPagar - $field_obligacion['MontoPagoParcial'];
    			?>
            	<input type="text" id="MontoPendiente" value="<?=number_format($MontoPendiente, 2, ',', '.')?>" style="width:150px; text-align:right; font-size:12px; font-weight:bold;" disabled="disabled" />
            </td>
    	</tr>
        <tr>
    		<td class="tagForm">&nbsp;</td>
    		<td>
            	<input type="checkbox" id="FlagPresupuesto" <?=chkFlag($field_obligacion['FlagPresupuesto'])?> disabled /> Afecta Presupuesto
            </td>
    		<td class="tagForm">&nbsp;</td>
    		<td class="tagForm">&nbsp;</td>
    	</tr>
        <tr>
    		<td class="tagForm">&nbsp;</td>
    		<td>
            	<input type="checkbox" id="FlagDistribucionManual" <?=chkFlag($field_obligacion['FlagDistribucionManual'])?> disabled /> Pago Directo
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
                            <a id="aInsertarImpuesto" href="../lib/listas/listado_impuestos.php?filtrar=default&ventana=obligacion_impuestos_insertar&CodRegimenFiscal=R&iframe=true&width=1050&height=400" rel="prettyPhoto[iframe4]" style="display:none;"></a>
                            <input type="button" class="btLista" value="Insertar" id="btInsertarImpuesto" onclick="document.getElementById('aInsertarImpuesto').click();" <?=$disabled_impuesto?> />
                            <input type="button" class="btLista" value="Borrar" id="btQuitarImpuesto" onclick="quitarLineaImpuesto(this, 'impuesto');" <?=$disabled_impuesto?> />
                        </td>
                    </tr>
                </table>
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
                                pr_obligacionesretenciones oi
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
                                <input type="text" value="<?=$field_impuestos['Descripcion']?>" class="cell2" readonly />
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
                                <input type="text" name="MontoImpuesto" value="<?=number_format($field_impuestos['MontoImpuesto'], 2, ',', '.')?>" style="text-align:right;" class="cell2" readonly />
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
                            <input type="text" id="impuesto_total" value="<?=number_format($impuesto_total, 2, ',', '.')?>" style="text-align:right; font-weight:bold;" class="cell2" readonly />
                        </th>
                    </tr>
                    </tfoot>
                </table>
                </div></td></tr></table>
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
                        <th width="100">O.C / O.S</th>
                        <th width="100" align="right">Monto Total</th>
                        <th width="100" align="right">Monto Afecto</th>
                        <th width="100" align="right">Impuesto</th>
                        <th width="100" align="right">Monto No Afecto</th>
                        <th align="left">Comentarios</th>
                    </tr>
                    </thead>
                    
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
                            <input type="button" class="btLista" id="btSelPartida" value="Sel. Partida" <?=$disabled_distribucion?> />
                            <input type="button" class="btLista" id="btSelCuenta" value="Sel. Cuenta" <?=$disabled_distribucion?> />
                            <input type="button" class="btLista" id="btSelCuenta20" value="Cta. Pub. 20" <?=$disabled_distribucion?> />
                            <input type="button" class="btLista" id="btSelCCosto" value="Sel. C.Costo" <?=$disabled_distribucion?> />
                            <input type="button" class="btLista" id="btSelPersona" value="Sel. Persona" <?=$disabled_distribucion?> />
                            <input type="button" class="btLista" id="btSelActivo" value="Sel. Activo" disabled="disabled" />
                        </td>
                        <td align="right">
                            <input type="button" class="btLista" id="btInsertarDistribucion" value="Insertar" <?=$disabled_distribucion?> />
                            <input type="button" class="btLista" id="btQuitarDistribucion" value="Quitar" <?=$disabled_distribucion?> />
                        </td>
                    </tr>
                </table>
                
                <table><tr><td><div style="overflow:scroll; width:1100px; height:215px;">
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
                    $sql = "SELECT 
                                oc.*,
                                p.denominacion AS NomPartida,
                                pc.Descripcion AS NomCuenta,
                                pc20.Descripcion AS NomCuentaPub20, 
                                pv.CategoriaProg,
                                pv.Ejercicio
                            FROM 
                                pr_obligacionescuenta oc
                                LEFT JOIN pv_partida p ON (oc.cod_partida = p.cod_partida)
                                LEFT JOIN ac_mastplancuenta pc ON (oc.CodCuenta = pc.CodCuenta)
                                LEFT JOIN ac_mastplancuenta20 pc20 ON (oc.CodCuentapUB20 = pc20.CodCuenta)
                                LEFT JOIN pv_presupuesto pv On (pv.CodOrganismo = oc.CodOrganismo AND pv.CodPresupuesto = oc.CodPresupuesto)
                            WHERE
                                oc.CodProveedor = '".$field_obligacion['CodProveedor']."' AND
                                oc.CodTipoDocumento = '".$field_obligacion['CodTipoDocumento']."' AND
                                oc.NroDocumento = '".$field_obligacion['NroDocumento']."'";
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
                                <input type="text" name="NomPartida" id="NomPartida_<?=$nro_distribucion?>" value="<?=htmlentities($field_distribucion['NomPartida'])?>" style="width:99%;" class="cell2" readonly />
                            </td>
                            <td align="center" width="80">
                                <input type="text" name="CodCuenta" id="CodCuenta_<?=$nro_distribucion?>" value="<?=$field_distribucion['CodCuenta']?>" maxlength="13" style="width:99%; text-align:center;" class="cell" onChange="getDescripcionLista2('accion=getDescripcionCuenta', this, $('#NomCuenta_<?=$nrodetalle?>'));" <?=$disabled_distribucion?> />
                            </td>
                            <td align="center" width="220">
                                <input type="text" name="NomCuenta" id="NomCuenta_<?=$nro_distribucion?>" value="<?=htmlentities($field_distribucion['NomCuenta'])?>" style="width:99%;" class="cell2" readonly />
                            </td>
                            <td align="center" width="80">
                                <input type="text" name="CodCuentaPub20" id="CodCuentaPub20_<?=$nro_distribucion?>" value="<?=$field_distribucion['CodCuentaPub20']?>" maxlength="13" style="width:99%; text-align:center;" class="cell2" readonly />
                            </td>
                            <td align="center" width="220">
                                <input type="text" name="NomCuentaPub20" id="NomCuentaPub20_<?=$nro_distribucion?>" value="<?=htmlentities($field_distribucion['NomCuentaPub20'])?>" style="width:99%;" class="cell2" readonly />
                            </td>
                            <td align="center">
                                <input type="text" name="CodCentroCosto" id="CodCentroCosto_<?=$nro_distribucion?>" value="<?=$field_distribucion['CodCentroCosto']?>" style="text-align:center;" class="cell" <?=$disabled_distribucion?> />
                                <input type="hidden" name="NomCentroCosto" id="NomCentroCosto_<?=$nro_distribucion?>" value="<?=$field_distribucion['NomCentroCosto']?>" />
                            </td>
                            <td align="center">
                                <input type="checkbox" name="FlagNoAfectoIGV" class="FlagNoAfectoIGV" <?=chkFlag($field_distribucion['FlagNoAfectoIGV'])?> onchange="actualizarMontosObligacion();" <?=$disabled_distribucion?> <?=$dFlagNoAfectoIGV?> />
                            </td>
                            <td align="center">
                                <input type="text" name="Monto" value="<?=number_format($field_distribucion['Monto'], 2, ',', '.')?>" style="text-align:right;" class="cell" onchange="actualizarMontosObligacion();" <?=$disabled_distribucion?> />
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
                                <input type="text" name="Descripcion" value="<?=htmlentities($field_distribucion['Descripcion'])?>" maxlength="255" class="cell" <?=$disabled_distribucion?> />
                            </td>
                            <td align="center">
                                <input type="text" name="CodPersona" id="CodPersona_<?=$nro_distribucion?>" value="<?=$field_distribucion['CodPersona']?>" maxlength="6" style="text-align:center;" class="cell" <?=$disabled_distribucion?> />
                                <input type="hidden" name="NomPersona" id="NomPersona_<?=$nro_distribucion?>" />
                            </td>
                            <td align="center">
                                <input type="text" name="NroActivo" id="NroActivo_<?=$nro_distribucion?>" value="<?=$field_distribucion['NroActivo']?>" maxlength="15" style="text-align:center;" class="cell2" readonly />
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
                           	<input type="text" id="distribucion_total" value="<?=number_format($distribucion_total, 2, ',', '.')?>" style="text-align:right; font-weight:bold;" class="cell2" readonly />
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
                    SUM(do.Monto) AS Monto
                FROM
                    ap_distribucionobligacion do
                    INNER JOIN ac_mastplancuenta pc ON (do.CodCuenta = pc.CodCuenta)
                WHERE
                    do.CodProveedor = '".$field_obligacion['CodProveedor']."' AND
                    do.CodTipoDocumento = '".$field_obligacion['CodTipoDocumento']."' AND
                    do.NroDocumento = '".$field_obligacion['NroDocumento']."'
                GROUP BY CodCuenta
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
                    SUM(do.Monto) AS Monto
                FROM
                    pr_obligacionescuenta do
                    INNER JOIN ac_mastplancuenta20 pc ON (do.CodCuentaPub20 = pc.CodCuenta)
                WHERE
                    do.CodProveedor = '".$field_obligacion['CodProveedor']."' AND
                    do.CodTipoDocumento = '".$field_obligacion['CodTipoDocumento']."' AND
                    do.NroDocumento = '".$field_obligacion['NroDocumento']."'
                GROUP BY CodCuentaPub20
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
                    SUM(do.Monto) AS Monto,
                    do.CodPresupuesto,
                    do.CodFuente,
                    pv.CategoriaProg,
                    ff.Denominacion AS Fuente,
                    ue.Denominacion AS UnidadEjecutora,
                    CONCAT(ss.CodSector, pr.CodPrograma, a.CodActividad) AS CatProg
                FROM
                    pr_obligacionescuenta do
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
                GROUP BY CodPresupuesto, CodFuente, cod_partida
                ORDER BY CodPresupuesto, CodFuente, cod_partida";
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
            $MontoPendiente = $PreCompromiso + $CotizacionesAsignadas;
            $MontoDisponible = $MontoAjustado - $MontoCompromiso;
            $MontoDisponibleReal = $MontoAjustado - ($MontoCompromiso + $MontoPendiente);
            ##  valido
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

<div id="tab5" style="display:none;"></div>

<script type="text/javascript" language="javascript">
// 	interfase de cuentas por pagar (generar)
function interfase_cuentas_por_pagar_generar(form) {
	bloqueo(true);
	//	formulario
	if (document.getElementById("FlagCompromiso").checked) var FlagCompromiso = "S"; else var FlagCompromiso = "N";
	var MontoAfecto = new Number(setNumero($("#MontoAfecto").val()));
	var MontoNoAfecto = new Number(setNumero($("#MontoNoAfecto").val()));
	var MontoImpuesto = new Number(setNumero($("#MontoImpuesto").val()));
	var MontoImpuestoOtros = new Number(setNumero($("#MontoImpuestoOtros").val()));
	var MontoObligacion = new Number(setNumero($("#MontoObligacion").val()));
	var MontoAdelanto = new Number(setNumero($("#MontoAdelanto").val()));
	var MontoPagar = new Number(setNumero($("#MontoPagar").val()));
	var MontoPagoParcial = new Number(setNumero($("#MontoPagoParcial").val()));
	var MontoPendiente = new Number(setNumero($("#MontoPendiente").val()));
	var documento_afecto = new Number(setNumero($("#documento_afecto").val()));
	var documento_impuesto = new Number(setNumero($("#documento_impuesto").val()));
	var documento_noafecto = new Number(setNumero($("#documento_noafecto").val()));
	var distribucion_total = new Number(setNumero($("#distribucion_total").val()));
	var impuesto_total = new Number(setNumero($("#impuesto_total").val()));
	var Estado = $('#Estado').val();
	var post = "";
	var error = "";
	for(var i=0; n=form.elements[i]; i++) {
		if (n.type == "hidden" || n.type == "text" || n.type == "password" || n.type == "select-one" || n.type == "textarea") {
			post += n.id + "=" + changeUrl(n.value.trim()) + "&";
		} else {
			if (n.type == "checkbox") {
				if (n.checked) post += n.id + "=S" + "&"; else post += n.id + "=N" + "&";
			}
			else if (n.type == "radio" && n.checked) {
				post += n.name + "=" + n.value.trim() + "&";
			}
		}
		//	errores
		if ((n.id == "CodProveedor" || n.id == "CodProveedorPagar" || n.id == "CodOrganismo" || n.id == "CodCentroCosto" || n.id == "CodTipoDocumento" || n.id == "NroDocumento" || n.id == "NroControl" || n.id == "CodTipoServicio" || n.id == "CodTipoPago" || n.id == "NroCuenta") && n.value.trim() == "") { error = "Debe llenar los campos obligatorios"+n.id; break; }
		else if (!valNumericoEntero(n.value) && n.id == "DiasPago") { error = "Formato de fecha de factura es incorrecta"; break; }
		else if (!valFecha(n.value) && n.id == "FechaFactura") { error = "Formato de fecha de factura es incorrecta"; break; }
		else if (!valFecha(n.value) && n.id == "FechaRegistro") { error = "Formato de fecha de registro es incorrecta"; break; }
		else if (!valFecha(n.value) && n.id == "FechaDocumento") { error = "Formato de fecha de documento es incorrecta"; break; }
		else if (!valFecha(n.value) && n.id == "FechaRecepcion") { error = "Formato de fecha de recepcion es incorrecta"; break; }
		else if (!valFecha(n.value) && n.id == "FechaVencimiento") { error = "Formato de fecha de vencimiento es incorrecta"; break; }
		else if (!valFecha(n.value) && n.id == "FechaProgramada") { error = "Formato de fecha programada es incorrecta"; break; }
		else if (isNaN(setNumero(n.value)) && n.id == "MontoAfecto") { error = "Formato de montos incorrectos"; break; }
		else if (isNaN(setNumero(n.value)) && n.id == "MontoNoAfecto") { error = "Formato de montos incorrectos"; break; }
		else if (isNaN(setNumero(n.value)) && n.id == "MontoImpuesto") { error = "Formato de montos incorrectos"; break; }
		else if (isNaN(setNumero(n.value)) && n.id == "MontoImpuestoOtros") { error = "Formato de montos incorrectos"; break; }
		else if (isNaN(setNumero(n.value)) && n.id == "MontoObligacion") { error = "Formato de montos incorrectos"; break; }
		else if (isNaN(setNumero(n.value)) && n.id == "MontoAdelanto") { error = "Formato de montos incorrectos"; break; }
		else if (isNaN(setNumero(n.value)) && n.id == "MontoPagar") { error = "Formato de montos incorrectos"; break; }
		else if (isNaN(setNumero(n.value)) && n.id == "MontoPagoParcial") { error = "Formato de montos incorrectos"; break; }
		else if (isNaN(setNumero(n.value)) && n.id == "MontoPendiente") { error = "Formato de montos incorrectos"; break; }
	}
	//	detalles impuesto
	var detalles_impuesto = "";
	var frm_impuesto = document.getElementById("frm_impuesto");
	for(var i=0; n=frm_impuesto.elements[i]; i++) {
		if (n.name == "CodImpuesto") detalles_impuesto += n.value + ";char:td;";
		else if (n.name == "CodConcepto") detalles_impuesto += n.value + ";char:td;";
		else if (n.name == "Signo") detalles_impuesto += n.value + ";char:td;";
		else if (n.name == "FlagImponible") detalles_impuesto += n.value + ";char:td;";
		else if (n.name == "FlagProvision") detalles_impuesto += n.value + ";char:td;";
		else if (n.name == "CodCuenta") {
			if (n.value.trim() == "" && $("#CONTONCO").val() == "S") { error = "Se encontraron lineas en los Impuestos sin Cuentas Contables.<br>Revise el Perfil de Conceptos para este Proceso."; break; }
			else detalles_impuesto += n.value + ";char:td;";
		}
		else if (n.name == "CodCuentaPub20") {
			if (n.value.trim() == "" && $("#CONTPUB20").val() == "S") { error = "Se encontraron lineas en los Impuestos sin Cuentas Contables.<br>Revise el Perfil de Conceptos para este Proceso."; break; }
			else detalles_impuesto += n.value + ";char:td;";
		}
		else if (n.name == "MontoAfecto") {
			var _MontoAfecto = new Number(setNumero(n.value));
			//if (isNaN(_MontoAfecto) || _MontoAfecto == 0) error = "Se encontraron lineas en las retenciones con montos incorrectos";
			detalles_impuesto += _MontoAfecto + ";char:td;";
		}
		else if (n.name == "FactorPorcentaje") {
			var _FactorPorcentaje = new Number(setNumero(n.value));
			//if (isNaN(_FactorPorcentaje) || _FactorPorcentaje <= 0) error = "Se encontraron lineas en las retenciones con montos incorrectos";
			detalles_impuesto += _FactorPorcentaje + ";char:td;";
		}
		else if (n.name == "MontoImpuesto") {
			var _MontoImpuesto = new Number(setNumero(n.value));
			if (isNaN(_MontoImpuesto) || _MontoImpuesto == 0) error = "Se encontraron lineas en las retenciones con montos incorrectos";
			detalles_impuesto += _MontoImpuesto + ";char:tr;";
		}
	}
	var len = detalles_impuesto.length; len-=9;
	detalles_impuesto = detalles_impuesto.substr(0, len);
	//	detalles documento
	var detalles_documento = "";
	var frm_documento = document.getElementById("frm_documento");
	for(var i=0; n=frm_documento.elements[i]; i++) {
		if (n.name == "Porcentaje") detalles_documento += n.value + ";char:td;";
		else if (n.name == "ReferenciaTipoDocumento") detalles_documento += n.value + ";char:td;";
		else if (n.name == "DocumentoClasificacion") detalles_documento += n.value + ";char:td;";
		else if (n.name == "DocumentoReferencia") detalles_documento += n.value + ";char:td;";
		else if (n.name == "Fecha") detalles_documento += n.value + ";char:td;";
		else if (n.name == "ReferenciaNroDocumento") detalles_documento += n.value + ";char:td;";
		else if (n.name == "MontoTotal") {
			var _MontoTotal = new Number(setNumero(n.value));
			if (isNaN(_MontoTotal) || _MontoTotal < 0) { error = "Se encontraron lineas en los documentos con montos incorrectos"; break; }
			detalles_documento += _MontoTotal + ";char:td;";
		}
		else if (n.name == "MontoAfecto") {
			var _MontoAfecto = new Number(setNumero(n.value));
			if (isNaN(_MontoAfecto) || _MontoAfecto < 0) { error = "Se encontraron lineas en los documentos con montos incorrectos"; break; }
			detalles_documento += _MontoAfecto + ";char:td;";
		}
		else if (n.name == "MontoImpuestos") {
			var _MontoImpuestos = new Number(setNumero(n.value));
			if (isNaN(_MontoImpuestos) || _MontoImpuestos < 0) { error = "Se encontraron lineas en los documentos con montos incorrectos"; break; }
			detalles_documento += _MontoImpuestos + ";char:td;";
		}
		else if (n.name == "MontoNoAfecto") {
			var _MontoNoAfecto = new Number(setNumero(n.value));
			if (isNaN(_MontoNoAfecto) || _MontoNoAfecto < 0) { error = "Se encontraron lineas en los documentos con montos incorrectos"; break; }
			detalles_documento += _MontoNoAfecto + ";char:td;";
		}
		else if (n.name == "Comentarios") detalles_documento += changeUrl(n.value.trim()) + ";char:tr;";
	}
	var len = detalles_documento.length; len-=9;
	detalles_documento = detalles_documento.substr(0, len);
	//	detalles distribucion
	var _MontoAfecto = new Number(0);
	var _MontoNoAfecto = new Number(0);
	var detalles_distribucion = "";
	var frm_distribucion = document.getElementById("frm_distribucion");
	for(var i=0; n=frm_distribucion.elements[i]; i++) {
		if (n.name == "cod_partida") {
			if (document.getElementById("FlagPresupuesto").checked && n.value == "") error = "Se encontraron lineas en la distribucion sin partidas presupuestarias";
			detalles_distribucion += n.value + ";char:td;";
		}
		else if (n.name == "CodCuenta") {
			if (n.value.trim() == "" && $("#CONTONCO").val() == "S") error = "Se encontraron lineas en la distribucion sin cuentas contables";
			detalles_distribucion += n.value + ";char:td;";
		}
		else if (n.name == "CodCuentaPub20") {
			if (n.value.trim() == "" && $("#CONTPUB20").val() == "S") error = "Se encontraron lineas en la distribucion sin cuentas contables (Pub. 20)";
			detalles_distribucion += n.value + ";char:td;";
		}
		else if (n.name == "CodCentroCosto") detalles_distribucion += n.value + ";char:td;";
		else if (n.name == "FlagNoAfectoIGV") {
			if (n.checked) { detalles_distribucion += "S" + ";char:td;"; var _FlagNoAfectoIGV = "S"; }
			else { detalles_distribucion += "N" + ";char:td;"; var _FlagNoAfectoIGV = "N"; }
		}
		else if (n.name == "Monto") {
			var Monto = new Number(setNumero(n.value));
			if (isNaN(Monto) || Monto <= 0) error = "Se encontraron lineas en la distribucion con montos incorrectos";
			detalles_distribucion += Monto + ";char:td;";
			if (_FlagNoAfectoIGV == "N") _MontoAfecto += Monto; else _MontoNoAfecto += Monto;
		}
        else if (n.name == "detallesCategoriaProg") detalles_distribucion += n.value + ";char:td;";
        else if (n.name == "detallesEjercicio") detalles_distribucion += n.value + ";char:td;";
        else if (n.name == "detallesCodPresupuesto") detalles_distribucion += n.value + ";char:td;";
        else if (n.name == "detallesCodFuente") detalles_distribucion += n.value + ";char:td;";
		else if (n.name == "TipoOrden") detalles_distribucion += n.value + ";char:td;";
		else if (n.name == "NroOrden") detalles_distribucion += n.value + ";char:td;";
		else if (n.name == "Referencia") detalles_distribucion += n.value + ";char:td;";
		else if (n.name == "Descripcion") detalles_distribucion += changeUrl(n.value.trim()) + ";char:td;";
		else if (n.name == "CodPersona") detalles_distribucion += n.value + ";char:td;";
		else if (n.name == "NroActivo") detalles_distribucion += n.value + ";char:td;";
		else if (n.name == "FlagDiferido") {
			if (n.checked) detalles_distribucion += "S" + ";char:tr;";
			else detalles_distribucion += "N" + ";char:tr;";
		}
	}
	var len = detalles_distribucion.length; len-=9;
	detalles_distribucion = detalles_distribucion.substr(0, len);
	//	detalles partidas
	var detalles_partidas = "";
	var frm_partidas = document.getElementById("frm_partidas");
	for(var i=0; n=frm_partidas.elements[i]; i++) {
		if (n.name == "cod_partida") {
			var cod_partida = n.value;
			detalles_partidas += n.value + ";char:td;";
		}
		else if (n.name == "Monto") {
			var Monto = new Number(n.value);
			detalles_partidas += n.value + ";char:td;";
		}
		else if (n.name == "MontoDisponible") {
			var MontoDisponible = new Number(n.value);
			detalles_partidas += n.value + ";char:td;";
		}
		else if (n.name == "MontoPendiente") {
			var MontoPendiente = new Number(n.value);
			if (MontoDisponible < Monto && FlagCompromiso == "S" && Estado == "PR") { error = "Sin disponibilidad presupuestaria la partida <strong>" + cod_partida + "</strong>"; break; }
			else detalles_partidas += n.value + ";char:tr;";
		}
	}
	var len = detalles_partidas.length; len-=9;
	detalles_partidas = detalles_partidas.substr(0, len);
	//	valido errores
	if (error != "") {
		cajaModal(error, "error", 400);
	} else {
		var url = "modulo=interfase_cuentas_por_pagar&accion=generar&"+post+"&detalles_impuesto="+detalles_impuesto+"&detalles_documento="+detalles_documento+"&detalles_distribucion="+detalles_distribucion;
		obligacion_ajax(form, url);
	}
	return false;
}
//	obligacion (ajax)
function obligacion_ajax(form, url) {
	$.ajax({
		type: "POST",
		url: "pr_interfase_cuentas_por_pagar_ajax.php",
		data: url,
		async: false,
		success: function(resp) {
			var datos = resp.split("|");
			if (datos[0].trim() != "") cajaModal(datos[0], "error", 400);
			else form.submit();
		}
	});
}

    //  
    function verDisponibilidadPresupuestaria() {
        //  detalles_partida
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
        var href = "../ap/gehen.php?anz=ap_obligacion_distribucion&detalles_partida="+detalles_partida+"&Anio="+$('#Anio').val()+"&CodOrganismo="+$('#CodOrganismo').val()+"&CodPresupuesto="+$('#CodPresupuesto').val()+"&opcion=<?=$opcion?>"+"&iframe=true&width=100%&height=430";
        $('#a_disponibilidad').attr('href', href);
        $('#a_disponibilidad').click();
    }
</script>