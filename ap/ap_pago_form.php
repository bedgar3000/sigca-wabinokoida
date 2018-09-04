<?php
$Ahora = ahora();
if (!$registro) $registro = $sel_registros;
list($NroProceso, $Secuencia, $CodTipoPago) = split("[_]", $registro);
$sql = "SELECT
			p.*,
			mp.NomCompleto AS NomProveedor,
			b.CodBanco,
			b.Banco,
			e1.CodEmpleado AS CodGeneradoPor,
			p1.NomCompleto AS NomGeneradoPor,
			e2.CodEmpleado AS CodConformadoPor,
			p2.NomCompleto AS NomConformadoPor,
			e3.CodEmpleado AS CodAprobadoPor,
			p3.NomCompleto AS NomAprobadoPor,
			e4.CodEmpleado AS CodRevisadoPor,
			p4.NomCompleto AS NomRevisadoPor
		FROM
			ap_pagos p
			INNER JOIN mastpersonas mp ON (p.CodProveedor = mp.CodPersona)
			INNER JOIN ap_ctabancaria cb ON (p.NroCuenta = cb.NroCuenta)
			INNER JOIN mastbancos b ON (cb.CodBanco = b.CodBanco)
			LEFT JOIN mastpersonas p1 ON (p.GeneradoPor = p1.CodPersona)
			LEFT JOIN mastempleado e1 ON (p1.CodPersona = e1.CodPersona)
			LEFT JOIN mastpersonas p2 ON (p.ConformadoPor = p2.CodPersona)
			LEFT JOIN mastempleado e2 ON (p2.CodPersona = e2.CodPersona)
			LEFT JOIN mastpersonas p3 ON (p.AprobadoPor = p3.CodPersona)
			LEFT JOIN mastempleado e3 ON (p3.CodPersona = e3.CodPersona)
			LEFT JOIN mastpersonas p4 ON (p.RevisadoPor = p4.CodPersona)
			LEFT JOIN mastempleado e4 ON (p4.CodPersona = e4.CodPersona)
		WHERE
			p.NroProceso = '".$NroProceso."' AND
			p.Secuencia = '".$Secuencia."'";
$field = getRecord($sql);
##	
$sql = "SELECT (CASE WHEN COUNT(*) > 0 THEN 'S' ELSE 'N' END) AS FlagRetencion
		FROM ap_retenciones
		WHERE
			PagoNroProceso = '".$NroProceso."' AND
			PagoSecuencia = '".$Secuencia."' AND
			Estado = 'PA'";
$FlagRetencion = getVar3($sql);
##	
if ($opcion == "modificar") {
	$titulo = "Modificaci&oacute;n Restringida del Pago";
	$accion = "modificar";
	$label_submit = "Modificar";
	$disabled_anular = "disabled";
}
elseif ($opcion == "ver") {
	$titulo = "Ver Pago";
	$disabled_ver = "disabled";
	$display_ver = "display:none;";
	$display_submit = "display:none;";
	$disabled_anular = "disabled";
}
elseif ($opcion == "anular") {
	$titulo = "Anular Pago";
	$disabled_ver = "disabled";
	$display_ver = "display:none;";
	$accion = "anular";
	$label_submit = "Anular";
}
//	------------------------------------
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="document.getElementById('frmentrada').submit()">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<table width="1000" align="center" cellpadding="0" cellspacing="0">
    <tr>
        <td>
            <div class="header">
            <ul id="tab">
            <!-- CSS Tabs -->
            <li id="li1" onclick="currentTab('tab', this);" class="current"><a href="#" onclick="mostrarTab('tab', 1, 2);">Informaci&oacute;n General</a></li>
            <li id="li2" onclick="currentTab('tab', this);"><a href="#" onclick="mostrarTab('tab', 2, 2);">Sustento del Pago</a></li>
            </ul>
            </div>
        </td>
    </tr>
</table>

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=<?=$origen?>" method="POST" onsubmit="return pago(this, '<?=$accion?>');">
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
<input type="hidden" name="fCodProveedor" id="fCodProveedor" value="<?=$fCodProveedor?>" />
<input type="hidden" name="fNomProveedor" id="fNomProveedor" value="<?=$fNomProveedor?>" />
<input type="hidden" name="fNroProceso" id="fNroProceso" value="<?=$fNroProceso?>" />
<input type="hidden" name="fNroPago" id="fNroPago" value="<?=$fNroPago?>" />
<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
<input type="hidden" name="fFechaPagod" id="fFechaPagod" value="<?=$fFechaPagod?>" />
<input type="hidden" name="fFechaPagoh" id="fFechaPagoh" value="<?=$fFechaPagoh?>" />
<input type="hidden" name="Anio" id="Anio" value="<?=$field['Anio']?>" />
<input type="hidden" name="NroOrden" id="NroOrden" value="<?=$field['NroOrden']?>" />
<input type="hidden" name="Periodo" id="Periodo" value="<?=$field['Periodo']?>" />
<input type="hidden" name="CodVoucher" id="CodVoucher" value="<?=substr($field['VoucherPago'], 0, 2)?>" />
<input type="hidden" name="CodVoucherPub20" id="CodVoucherPub20" value="<?=substr($field['VoucherPagoPub20'], 0, 2)?>" />
<input type="hidden" name="fCodBanco" id="fCodBanco" value="<?=$fCodBanco?>" />
<input type="hidden" name="fNomProveedorPagar" id="fNomProveedorPagar" value="<?=$fNomProveedorPagar?>" />
<input type="hidden" name="fNroCuenta" id="fNroCuenta" value="<?=$fNroCuenta?>" />
<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
<input type="hidden" name="fFlagCobrado" id="fFlagCobrado" value="<?=$fFlagCobrado?>" />
<input type="hidden" name="fEstadoEntrega" id="fEstadoEntrega" value="<?=$fEstadoEntrega?>" />
<input type="hidden" name="fFlagVencidos" id="fFlagVencidos" value="<?=$fFlagVencidos?>" />
<input type="hidden" name="fFechaPagoD" id="fFechaPagoD" value="<?=$fFechaPagoD?>" />
<input type="hidden" name="fFechaPagoH" id="fFechaPagoH" value="<?=$fFechaPagoH?>" />
<input type="hidden" id="FlagRetencion" value="<?=$FlagRetencion?>" />

<div id="tab1" style="display:block;">
<table align="center" width="1000" class="tblForm">
    <tr>
        <td colspan="4" class="divFormCaption" style="height:20px;">Informaci&oacute;n Adicional</td>
    </tr>
    <tr>
        <td class="tagForm" width="125">Organismo:</td>
        <td>
            <select name="CodOrganismo" id="CodOrganismo" style="width:300px;">
                <?=loadSelect("mastorganismos", "CodOrganismo", "Organismo", $field['CodOrganismo'], 1);?>
            </select>
        </td>
        <td class="tagForm" width="125">Pagar A: </td>
        <td>
            <input type="hidden" name="CodProveedor" id="CodProveedor" value="<?=$field['CodProveedor']?>" />
            <input type="text" name="NomProveedorPagar" id="NomProveedorPagar" value="<?=htmlentities($field['NomProveedorPagar'])?>" style="width:300px;" <?=$disabled_ver?> />
        </td>
    </tr>
    <tr>
        <td class="tagForm">Cta. Bancaria:</td>
        <td>
            <select name="NroCuenta" id="NroCuenta" style="width:175px;">
                <?=loadSelect("ap_ctabancaria", "NroCuenta", "NroCuenta", $field['NroCuenta'], 1);?>
            </select>
        </td>
        <td class="tagForm">Pago:</td>
        <td>
            <input type="text" name="NroProceso" id="NroProceso" style="width:60px; font-weight:bold; text-align:center; font-size:14px;" value="<?=$field['NroProceso']?>" readonly />
            <input type="text" name="Secuencia" id="Secuencia" style="width:20px; font-weight:bold; text-align:center; font-size:14px;" value="<?=$field['Secuencia']?>" readonly /> - 
            <input type="text" name="NroPago" id="NroPago" style="width:85px; font-weight:bold; text-align:center; font-size:14px;" value="<?=$field['NroPago']?>" readonly />
        </td>
    </tr>
</table>

<table align="center" width="1000" class="tblForm">
    <tr>
        <td colspan="2" class="divFormCaption" style="height:20px;">Datos del pago</td>
        <td colspan="2" class="divFormCaption">Estados del Pago</td>
        <td colspan="2" class="divFormCaption">Contabilizaci&oacute;n</td>
    </tr>
    <tr>
        <td class="tagForm">Fecha de Pago:</td>
        <td><input type="text" name="FechaPago" id="FechaPago" maxlength="10" style="width:75px;" class="datepicker" onkeyup="setFechaDMA(this);" value="<?=formatFechaDMA($field['FechaPago'])?>" <?=$disabled_ver?> /></td>
        <td class="tagForm">De Impresi&oacute;n:</td>
        <td><input type="text" name="Estado" id="Estado" style="width:75px;" value="<?=printValores("ESTADO-PAGO", $field['Estado'])?>" disabled /></td>
        <td class="tagForm">Contabilizado:</td>
        <td>
        	<input type="text" style="width:20px;" value="<?=printValoresGeneral("FLAG-CONTABILIZADO", $field['FlagContabilizacionPendiente'])?>" readonly />
        	<input type="hidden" name="FlagContabilizacionPendiente" id="FlagContabilizacionPendiente" value="<?=$field['FlagContabilizacionPendiente']?>" />
        	<input type="hidden" name="FlagContPendientePub20" id="FlagContPendientePub20" value="<?=$field['FlagContPendientePub20']?>" />
        </td>
    </tr>
    <tr>
        <td class="tagForm">Tipo de Pago</td>
        <td>
            <select name="CodTipoPago" id="CodTipoPago" style="width:175px;" disabled>
                <?=loadSelect("masttipopago", "CodTipoPago", "TipoPago", $field['CodTipoPago'], 1)?>
            </select>
        </td>
        <td class="tagForm">De Entrega:</td>
        <td><input type="text" name="EstadoEntrega" id="EstadoEntrega" style="width:75px;" value="<?=printValores("ESTADO-CHEQUE", $field['EstadoEntrega'])?>" readonly /></td>
        <td class="tagForm">Voucher:</td>
        <td>
            <input type="text" name="VoucherPeriodo" id="VoucherPeriodo" style="width:40px;" value="<?=$field['VoucherPeriodo']?>" readonly />-
            <input type="text" name="VoucherPago" id="VoucherPago" style="width:40px;" value="<?=$field['VoucherPago']?>" readonly />
            &nbsp;
            <input type="text" name="PeriodoPagoPub20" id="PeriodoPagoPub20" style="width:40px;" value="<?=$field['PeriodoPagoPub20']?>" readonly />-
            <input type="text" name="VoucherPagoPub20" id="VoucherPagoPub20" style="width:40px;" value="<?=$field['VoucherPagoPub20']?>" readonly />
        </td>
    </tr>
    <tr>
        <td class="tagForm">Origen</td>
        <td><input type="text" name="OrigenGeneracion" id="OrigenGeneracion" style="width:75px;" value="<?=printValores("ORIGEN-PAGO", $field['OrigenGeneracion'])?>" readonly /></td>
        <td class="tagForm">Fecha de Entrega:</td>
        <td><input type="text" name="FechaEntregado" id="FechaEntregado" style="width:75px;" value="<?=formatFechaDMA($field['FechaEntregado'])?>" disabled /></td>
        <td class="divFormCaption" colspan="2" style="height:20px;">Inf. Adicional</td>
    </tr>
    <tr>
        <td class="tagForm">Monto Pago</td>
        <td><input type="text" name="MontoPago" id="MontoPago" style="width:125px; text-align:right; font-weight:bold; font-size:14px;" value="<?=number_format($field['MontoPago'], 2, ',', '.')?>" readonly /></td>
        <td class="tagForm">De Cobro:</td>
        <td><input type="text" name="FlagCobrado" id="FlagCobrado" style="width:75px;" value="<?=printValores("ESTADO-CHEQUE-COBRO", $field['FlagCobrado'])?>" readonly /></td>
        <td class="tagForm">&nbsp;</td>
        <td>
            <input type="checkbox" name="flagnonegociable" id="flagnonegociable" <?=$flagnonegociable?> readonly /> Cheque No Negociable
        </td>
    </tr>
    
    <tr>
		<td class="tagForm">* Generado Por:</td>
		<td class="gallery clearfix">
        	<input type="hidden" name="GeneradoPor" id="GeneradoPor" value="<?=$field['GeneradoPor']?>" />
        	<input type="text" name="CodGeneradoPor" id="CodGeneradoPor" value="<?=$field['CodGeneradoPor']?>" readonly style="width:60px;" />
			<input type="text" name="NomGeneradoPor" id="NomGeneradoPor" value="<?=($field['NomGeneradoPor'])?>" readonly style="width:250px;" />
			<a href="../lib/listas/listado_empleados.php?filtrar=default&cod=CodGeneradoPor&nom=NomGeneradoPor&campo3=GeneradoPor&iframe=true&width=950&height=425" rel="prettyPhoto[iframe1]" style=" <?=$display_ver?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
        <td class="tagForm">&nbsp;</td>
        <td class="tagForm">&nbsp;</td>
	</tr>
    <tr>
		<td class="tagForm">* Conformado Por:</td>
		<td class="gallery clearfix">
        	<input type="hidden" name="ConformadoPor" id="ConformadoPor" value="<?=$field['ConformadoPor']?>" />
        	<input type="text" name="CodConformadoPor" id="CodConformadoPor" value="<?=$field['CodConformadoPor']?>" readonly style="width:60px;" />
			<input type="text" name="NomConformadoPor" id="NomConformadoPor" value="<?=($field['NomConformadoPor'])?>" readonly style="width:250px;" />
			<a href="../lib/listas/listado_empleados.php?filtrar=default&cod=CodConformadoPor&nom=NomConformadoPor&campo3=ConformadoPor&iframe=true&width=950&height=425" rel="prettyPhoto[iframe2]" style=" <?=$display_ver?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
        <td class="tagForm">&nbsp;</td>
        <td class="tagForm">&nbsp;</td>
	</tr>
    <tr>
		<td class="tagForm">* Revisado Por:</td>
		<td class="gallery clearfix">
        	<input type="hidden" name="RevisadoPor" id="RevisadoPor" value="<?=$field['RevisadoPor']?>" />
        	<input type="text" name="CodRevisadoPor" id="CodRevisadoPor" value="<?=$field['CodRevisadoPor']?>" readonly style="width:60px;" />
			<input type="text" name="NomRevisadoPor" id="NomRevisadoPor" value="<?=($field['NomRevisadoPor'])?>" readonly style="width:250px;" />
			<a href="../lib/listas/listado_empleados.php?filtrar=default&cod=CodRevisadoPor&nom=NomRevisadoPor&campo3=RevisadoPor&iframe=true&width=950&height=425" rel="prettyPhoto[iframe3]" style=" <?=$display_ver?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
        <td class="tagForm">&nbsp;</td>
        <td class="tagForm">&nbsp;</td>
	</tr>
    <tr>
		<td class="tagForm">* Aprobado Por:</td>
		<td class="gallery clearfix">
        	<input type="hidden" name="AprobadoPor" id="AprobadoPor" value="<?=$field['AprobadoPor']?>" />
        	<input type="text" name="CodAprobadoPor" id="CodAprobadoPor" value="<?=$field['CodAprobadoPor']?>" readonly style="width:60px;" />
			<input type="text" name="NomAprobadoPor" id="NomAprobadoPor" value="<?=($field['NomAprobadoPor'])?>" readonly style="width:250px;" />
			<a href="../lib/listas/listado_empleados.php?filtrar=default&cod=CodAprobadoPor&nom=NomAprobadoPor&campo3=AprobadoPor&iframe=true&width=950&height=425" rel="prettyPhoto[iframe4]" style=" <?=$display_ver?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
        <td class="tagForm">&nbsp;</td>
        <td class="tagForm">&nbsp;</td>
	</tr>
</table>

<table align="center" width="1000" class="tblForm">
    <tr>
        <td colspan="4" class="divFormCaption" style="height:20px;">Anulaci&oacute;n / Reemplazo</td>
    </tr>
    <tr>
        <td class="tagForm" width="125">Fecha:</td>
        <td><input type="text" name="FechaAnulacion" id="FechaAnulacion" style="width:75px;" value="<?=formatFechaDMA($field['FechaAnulacion'])?>" readonly /></td>
        <td class="tagForm" width="125">Voucher: </td>
        <td>
            <input type="text" name="PeriodoAnulacion" id="PeriodoAnulacion" style="width:50px;" value="<?=$field['PeriodoAnulacion']?>" readonly />-
            <input type="text" name="VoucherAnulacion" id="VoucherAnulacion" style="width:50px;" value="<?=$field['VoucherAnulacion']?>" readonly />
        </td>
    </tr>
    <tr>
        <td class="tagForm">Anulado Por:</td>
        <td colspan="3"><input type="text" name="NomAnuladoPor" id="NomAnuladoPor" style="width:300px;" value="<?=($field['NomAnuladoPor'])?>" readonly /></td>
    </tr>
    <tr>
        <td class="tagForm">Motivo:</td>
        <td colspan="3"><input type="text" name="MotivoAnulacion" id="MotivoAnulacion" style="width:300px;" value="<?=($field['MotivoAnulacion'])?>" <?=$disabled_anular?> /></td>
    </tr>
    <tr>
        <td class="tagForm">Reemplazado Por:</td>
        <td colspan="3"><input type="text" name="NomReemplazadoPor" id="NomReemplazadoPor" style="width:300px;" value="<?=($field['NomReemplazadoPor'])?>" readonly /></td>
    </tr>
    <tr>
        <td class="tagForm">&Uacute;ltima Modif.:</td>
        <td colspan="3">
            <input type="text" id="UltimoUsuario" value="<?=$field['UltimoUsuario']?>" size="30" readonly />
            <input type="text" id="UltimaFecha" size="25" value="<?=$field['UltimaFecha']?>" readonly />
        </td>
    </tr>
</table>
<center> 
<input type="submit" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" />
<input type="button" value="Cancelar" style="width:75px;" onclick="this.form.submit();" />
</center>
</div>

<div id="tab2" style="display:none;">
<center>
<div style="overflow:scroll; width:1000px; height:150px;">
<table align="center" width="100%" class="tblLista">
	<thead>
    <tr>
        <th>Proveedor</th>
        <th width="150">Documento</th>
        <th width="100">Fecha</th>
        <th width="100">Estado</th>
        <th width="125">Monto Pagado</th>
        <th width="125">Monto Retenci&oacute;n</th>
    </tr>
    </thead>
    
    <tbody>
    <?php
	$sql = "SELECT
				mp.NomCompleto As NomProveedor,
				o.CodTipoDocumento,
				o.NroDocumento,
				o.MontoObligacion,
				o.MontoImpuestoOtros,
				o.FechaRegistro,
				o.Estado
			FROM
				ap_pagos p
				INNER JOIN mastpersonas mp ON (p.CodProveedor = mp.CodPersona)
				INNER JOIN ap_ordenpago op ON (p.CodOrganismo = op.CodOrganismo AND
											   p.Anio = op.Anio AND
											   p.NroOrden = op.NroOrden)
				INNER JOIN ap_obligaciones o ON (op.CodProveedor = o.CodProveedor AND
												 op.CodTipoDocumento = o.CodTipoDocumento AND
												 op.NroDocumento = o.NroDocumento)
			WHERE
				p.NroProceso = '".$NroProceso."' AND
				p.Secuencia = '".$Secuencia."'";
	$query_obligacion = mysql_query($sql) or die($sql.mysql_error());
	while($field_obligacion = mysql_fetch_array($query_obligacion)) {
		?>
        <tr class="trListaBody">
        	<td><?=htmlentities($field_obligacion['NomProveedor'])?></td>
        	<td align="center"><?=$field_obligacion['CodTipoDocumento']?>-<?=$field_obligacion['NroDocumento']?></td>
        	<td align="center"><?=formatFechaDMA($field_obligacion['FechaRegistro'])?></td>
            <td align="center"><?=printValores("ESTADO-OBLIGACIONES", $field_obligacion['Estado'])?></td>
        	<td align="right"><strong><?=number_format($field_obligacion['MontoObligacion'], 2, ',', '.')?></strong></td>
        	<td align="right"><strong><?=number_format($field_obligacion['MontoImpuestoOtros'], 2, ',', '.')?></strong></td>
        </tr>
        <?php
	}
	?>
    </tbody>
</table>
</div>
</center>
<br />

<center>
<div style="overflow:scroll; width:1000px; height:150px;">
<table align="center" class="tblLista" style="width:100%;">
	<thead>
    <tr>
    	<th width="60">Tipo</th>
        <th>Comprobante</th>
        <th width="60">Periodo Fiscal</th>
        <th width="75">Monto Imponible</th>
        <th width="75" align="right">Monto Exento</th>
        <th width="75" align="right">Monto Impuesto</th>
        <th width="100" align="right">Monto Factura</th>
        <th width="35" align="right">%</th>
        <th width="100" align="right">Monto Retenido</th>
        <th width="100">Estado</th>
    </tr>
    </thead>
    
    <tbody>
    <?php
	$sql = "SELECT
				r.Anio,
				r.TipoComprobante,
				r.NroComprobante,
				CONCAT(SUBSTRING(r.PeriodoFiscal, 1, 4), SUBSTRING(r.PeriodoFiscal, 6, 2), r.NroComprobante) AS Comprobante,
				r.PeriodoFiscal,
				r.FechaComprobante,
				r.NroDocumento,
				r.NroControl,
                r.NroFactura,
				r.FechaFactura,
				r.MontoAfecto,
				r.MontoNoAfecto,
				r.MontoImpuesto,
				r.MontoFactura,
				r.Porcentaje,
				ABS(r.MontoRetenido) AS MontoRetenido,
				r.Estado
			FROM
				ap_retenciones r
			WHERE
				r.PagoNroProceso = '".$NroProceso."' AND
				r.PagoSecuencia = '".$Secuencia."'";
	$field_retenciones = getRecords($sql);
	foreach($field_retenciones as $f) {
		$id = "";
		?>
        <tr class="trListaBody">
			<td align="center">
            	<input type="hidden" name="retenciones_Anio[]" value="<?=$f['Anio']?>" />
            	<input type="hidden" name="retenciones_TipoComprobante[]" value="<?=$f['TipoComprobante']?>" />
            	<input type="hidden" name="retenciones_NroComprobante[]" value="<?=$f['NroComprobante']?>" />
            	<input type="hidden" name="retenciones_Estado[]" value="<?=$f['Estado']?>" />
				<strong><?=$f['TipoComprobante']?></strong>
            </td>
			<td align="center"><?=$f['Comprobante']?></td>
			<td align="center"><?=$f['PeriodoFiscal']?></td>
			<td align="right"><?=number_format($f['MontoAfecto'],2,',','.')?></td>
			<td align="right"><?=number_format($f['MontoNoAfecto'],2,',','.')?></td>
			<td align="right"><?=number_format($f['MontoImpuesto'],2,',','.')?></td>
			<td align="right"><?=number_format($f['MontoFactura'],2,',','.')?></td>
			<td align="right"><strong><?=number_format($f['Porcentaje'],2,',','.')?></strong></td>
			<td align="right"><strong><?=number_format($f['MontoRetenido'],2,',','.')?></strong></td>
			<td align="center"><?=printValores('estado-retencion',$f['Estado'])?></td>
        </tr>
        <?php
	}
	?>
    </tbody>
</table>
</div>
</center>
</div>
</form>