<?php
//	consulto los datos del proveedor
$sql = "SELECT
			p.NomCompleto,
			pv.DiasPago,
			p.DocFiscal,
			p.Busqueda,
			pv.CodTipoDocumento,
			pv.CodTipoServicio,
			pv.CodTipoPago
		FROM
			mastpersonas p
			INNER JOIN mastproveedores pv ON (p.CodPersona = pv.CodProveedor)
		WHERE p.CodPersona = '".$fCodProveedor."'";
$query_obligacion = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
if (mysql_num_rows($query_obligacion) != 0) $field_obligacion = mysql_fetch_array($query_obligacion);

//	consulto la cuenta bancaria por default
$sql = "SELECT NroCuenta
		FROM ap_ctabancariadefault
		WHERE
			CodOrganismo = '".$fCodOrganismo."' AND
			CodTipoPago = '".$field_obligacion['CodTipoPago']."'";
$query_cta = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
if (mysql_num_rows($query_cta) != 0) $field_cta = mysql_fetch_array($query_cta);

$field_obligacion['Estado'] = "PR";
$field_obligacion['CodOrganismo'] = $fCodOrganismo;
$field_obligacion['IngresadoPor'] = $_SESSION["CODPERSONA_ACTUAL"];
$field_obligacion['NomIngresadoPor'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
$field_obligacion['FechaPreparacion'] = $FechaActual;
$field_obligacion['NroCuenta'] = $field_cta["NroCuenta"];
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
$field_obligacion['CodFuente'] = $_PARAMETRO['FFMETASDEF'];
$FactorImpuesto = getPorcentajeIVA($field_obligacion['CodTipoServicio']);
if (!afectaTipoServicio($field_obligacion['CodTipoServicio'])) {
	$disabled_impuesto = "disabled";
	$dFlagNoAfectoIGV = "disabled";
	$cFlagNoAfectoIGV = "checked";
}
$filtro_documentos = "";
$linea_documento = split(";", $registro);	$_Linea=0;
foreach ($linea_documento as $documento) {	$_Linea++;
	list($_Anio, $_DocumentoReferencia) = split("[.]", $documento);
	//	consulto
	$sql = "SELECT * 
			FROM ap_documentos
			WHERE 
				Anio = '".$_Anio."' AND
				CodProveedor = '".$fCodProveedor."' AND
				DocumentoClasificacion = '".$fDocumentoClasificacion."' AND
				DocumentoReferencia = '".$_DocumentoReferencia."'
			ORDER BY DocumentoReferencia
			LIMIT 0, 1";
	$query_documentos = mysql_query($sql) or die ($sql.mysql_error());
	while ($field_documentos = mysql_fetch_array($query_documentos)) {
		$Comentarios = $field_documentos['Comentarios'];
		$field_obligacion['ReferenciaDocumento'] .= $field_documentos['ReferenciaTipoDocumento']."-".$field_documentos['ReferenciaNroDocumento']."; ";
        $field_obligacion['CodCentroCosto'] = $field_documentos['CodCentroCosto'];
	}
	if ($filtro_documentos == "") $filtro_documentos .= " AND ( "; else $filtro_documentos .= " OR (";
	$filtro_documentos .= " d.Anio = '".$_Anio."' AND 
							d.CodProveedor = '".$fCodProveedor."' AND 
							d.DocumentoClasificacion = '".$fDocumentoClasificacion."' AND 
							d.DocumentoReferencia = '".$_DocumentoReferencia."')";
}
$mostrarTabDistribucion = "mostrarTabDistribucionObligacion();";
//	presupuesto
$sql = "SELECT p.*
        FROM pv_presupuesto p
        INNER JOIN pv_categoriaprog cp ON (cp.CategoriaProg = p.CategoriaProg)
        INNER JOIN pv_unidadejecutora ue On (ue.CodUnidadEjec = cp.CodUnidadEjec)
        WHERE p.CodOrganismo = '".$fCodOrganismo."' AND p.Ejercicio = '".$_Anio."' AND ue.CodCentroCosto = '".$field_obligacion['CodCentroCosto']."'";
$field_presupuesto = getRecord($sql);
$field_obligacion['CodPresupuesto'] = $field_presupuesto['CodPresupuesto'];
$field_obligacion['Ejercicio'] = $field_presupuesto['Ejercicio'];
$field_obligacion['CategoriaProg'] = $field_presupuesto['CategoriaProg'];
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo">Nueva Obligaci&oacute;n</td>
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
            <li id="li4" onclick="currentTab('tab', this);">
            	<a href="#" onclick="<?=$mostrarTabDistribucion?>">Resumen Contable y Presup.</a>
            </li>
            <li id="li5" onclick="currentTab('tab', this);"><a href="#" onclick="mostrarTab('tab', 5, 5);">Adelantos y Pagos Parciales</a></li>
            </ul>
            </div>
        </td>
    </tr>
</table>

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=ap_facturacion_lista" method="POST" onsubmit="return obligacion(this, 'nuevo');">
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
<input type="hidden" name="fCodProveedor" id="fCodProveedor" value="<?=$fCodProveedor?>" />
<input type="hidden" name="fNomProveedor" id="fNomProveedor" value="<?=$fNomProveedor?>" />
<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
<input type="hidden" name="fCodFormaPago" id="fCodFormaPago" value="<?=$fCodFormaPago?>" />
<input type="hidden" name="fDocumentoClasificacion" id="fDocumentoClasificacion" value="<?=$fDocumentoClasificacion?>" />
<input type="hidden" name="FactorImpuesto" id="FactorImpuesto" value="<?=$FactorImpuesto?>" />
<input type="hidden" name="Periodo" id="Periodo" value="<?=$field_obligacion['Periodo']?>" />
<input type="hidden" name="FlagProvision" id="FlagProvision" value="<?=$field_obligacion['FlagProvision']?>" />
<input type="hidden" name="CodVoucher" id="CodVoucher" value="<?=$field_obligacion['CodVoucher']?>" />
<input type="hidden" name="CodPresupuesto" id="CodPresupuesto" value="<?=$field_obligacion['CodPresupuesto']?>" />
<input type="hidden" name="NroDocumento" id="NroDocumento" value="<?=$field_obligacion['NroDocumento']?>" />
<input type="hidden" name="Anio" id="Anio" value="<?=$_Anio?>" />
<input type="hidden" id="CONTPUB20" value="<?=$_PARAMETRO['CONTPUB20']?>" />
<input type="hidden" id="FlagNomina" value="N" />

<div id="tab1" style="display:block;">
    <table width="1100" class="tblForm">
    	<tr>
        	<td colspan="4" class="divFormCaption">Informaci&oacute;n del Proveedor</td>
        </tr>
        <tr>
    		<td class="tagForm" width="125">* Proveedor:</td>
    		<td class="gallery clearfix">
            	<input type="text" id="CodProveedor" value="<?=$fCodProveedor?>" disabled="disabled" style="width:100px;" />
    			<input type="text" id="NomCompleto" value="<?=($field_obligacion['NomCompleto'])?>" disabled="disabled" style="width:250px;" />
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
            	<input type="text" id="CodProveedorPagar" value="<?=$fCodProveedor?>" maxlength="6" style="width:50px;" onchange="getDescripcionLista('accion=getDescripcionPersona&flagproveedor=S&flagempleado=S&flagotros=S', this, 'nompagara');" disabled="disabled" />
    			<input type="text" id="NomProveedorPagar" value="<?=($field_obligacion['NomCompleto'])?>" style="width:250px;" disabled="disabled" />
            </td>
    	</tr>
    	<tr>
    		<td class="tagForm">* Organismo:</td>
    		<td>
            	<select id="CodOrganismo" style="width:300px;">
                	<?=loadSelect("mastorganismos", "CodOrganismo", "Organismo", $fCodOrganismo, 1)?>
                </select>
    		</td>
    		<td class="tagForm">* Centro Costo:</td>
    		<td class="gallery clearfix">
            	<input type="text" id="CodCentroCosto" value="<?=$field_obligacion['CodCentroCosto']?>" style="width:50px;" onchange="getDescripcionLista('accion=getDescripcionCCosto', this, 'nomccosto');" disabled="disabled" />
    			<input type="hidden" id="NomCentroCosto" value="<?=($field_obligacion['CodCentroCosto'])?>" />
    			<a href="../lib/listas/listado_centro_costos.php?filtrar=default&cod=CodCentroCosto&nom=NomCentroCosto&iframe=true&width=950&height=525" rel="prettyPhoto[iframe3]" style=" <?=$display_ver?>">
                	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
                </a>
            </td>
    	</tr>
        <tr>
    		<td class="tagForm">* Tipo de Documento:</td>
    		<td>
            	<select id="CodTipoDocumento" style="width:300px;">
                    <?=loadSelect("ap_tipodocumento", "CodTipoDocumento", "Descripcion", $field_obligacion['CodTipoDocumento'], 1)?>
                </select>
            </td>
    		<td class="tagForm">Nro. Registro:</td>
    		<td><input type="text" id="NroRegistro" value="<?=$field_obligacion['NroRegistro']?>" style="width:100px;" class="codigo" disabled="disabled" /></td>
    	</tr>
        <tr>
    		<td class="tagForm">* Nro. Control:</td>
    		<td><input type="text" id="NroControl" maxlength="20" style="width:150px;" value="<?=$field_obligacion['NroControl']?>" /></td>
    		<td class="tagForm">* Nro. Factura:</td>
    		<td><input type="text" id="NroFactura" maxlength="20" value="<?=$field_obligacion['NroFactura']?>" style="width:150px;" /></td>
    	</tr>
        <tr>
    		<td height="22" class="tagForm">Estado:</td>
    		<td>
           	  <input type="hidden" id="Estado" value="<?=$field_obligacion['Estado']?>" />
            	<input type="text" style="width:100px;" class="codigo" value="<?=printValores("ESTADO-OBLIGACIONES", $field_obligacion['Estado'])?>" disabled="disabled" />
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
                        <td><input type="text" id="FechaRegistro" value="<?=formatFechaDMA($field_obligacion['FechaRegistro'])?>" style="width:100px;" class="datepicker codigo" onkeyup="setFechaDMA(this);" /></td>
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
                            <select id="CodTipoServicio" style="width:150px;" onchange="afectaTipoServicioObligacion(this.value);">
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
    		<td><input type="text" id="Comentarios" value="<?=$Comentarios?>" style="width:95%;" /></td>
    	</tr>
    	<tr>
    		<td class="tagForm">Comentarios Adicional:</td>
    		<td><textarea id="ComentariosAdicional" style="width:95%; height:45px;"><?=$Comentarios?></textarea></td>
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
    <input type="submit" value="Preparar" />
    <input type="button" value="Cancelar" onclick="this.form.submit();" />
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
    		<td><input type="text" id="ReferenciaDocumento" style="width:195px;" value="<?=$field_obligacion['ReferenciaDocumento']?>" disabled="disabled" /></td>
    		<td class="tagForm" width="150">Monto Afecto:</td>
    		<td>
            	<input type="text" id="MontoAfecto" value="<?=number_format($field_obligacion['MontoAfecto'], 2, ',', '.')?>" style="width:150px; text-align:right;" onfocus="numeroFocus(this);" onblur="numeroBlur(this);" disabled="disabled" />
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
            	<input type="text" id="MontoNoAfecto" value="<?=number_format($field_obligacion['MontoNoAfecto'], 2, ',', '.')?>" style="width:150px; text-align:right;" onfocus="numeroFocus(this);" onblur="numeroBlur(this);" disabled="disabled" />
            </td>
    	</tr>
        <tr>
    		<td class="tagForm">&nbsp;</td>
    		<td>
            	<input type="checkbox" id="FlagCajaChica" <?=chkFlag($field_obligacion['FlagCajaChica'])?> disabled="disabled" /> Pago con Caja Chica (Efectivo)
            </td>
    		<td class="tagForm">Impuesto:</td>
    		<td>
            	<input type="text" id="MontoImpuesto" value="<?=number_format($field_obligacion['MontoImpuesto'], 2, ',', '.')?>" style="width:150px; text-align:right;" onfocus="numeroFocus(this);" onblur="numeroBlur(this);" onchange="cambiar_monto_impuesto();" />
            </td>
    	</tr>
        <tr>
    		<td class="tagForm">&nbsp;</td>
    		<td>
            	<input type="checkbox" id="FlagPagoIndividual" <?=chkFlag($field_obligacion['FlagPagoIndividual'])?> disabled="disabled" /> Preparar Pago Individual
            </td>
    		<td class="tagForm">Otros Impuestos/Retenciones:</td>
    		<td>
            	<input type="text" id="MontoImpuestoOtros" value="<?=number_format($field_obligacion['MontoImpuestoOtros'], 2, ',', '.')?>" style="width:150px; text-align:right;" onfocus="numeroFocus(this);" onblur="numeroBlur(this);" disabled="disabled" />
            </td>
    	</tr>
        <tr>
    		<td class="tagForm">&nbsp;</td>
    		<td>
            	<input type="checkbox" id="FlagGenerarPago" <?=chkFlag($field_obligacion['FlagGenerarPago'])?> disabled="disabled" /> Preparar Pago (Autom&aacute;tico)
            </td>
    		<td class="tagForm"><strong>Total Obligaci&oacute;n:</strong></td>
    		<td>
            	<input type="text" id="MontoObligacion" value="<?=number_format($field_obligacion['MontoObligacion'], 2, ',', '.')?>" style="width:150px; text-align:right; font-size:12px; font-weight:bold;" class="codigo" onfocus="numeroFocus(this);" onblur="numeroBlur(this);" disabled="disabled" />
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
            	<input type="text" id="MontoPagar" value="<?=number_format($MontoPagar, 2, ',', '.')?>" style="width:150px; text-align:right; font-size:12px; font-weight:bold;" class="codigo" onfocus="numeroFocus(this);" onblur="numeroBlur(this);" disabled="disabled" />
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
            	<input type="checkbox" id="FlagCompromiso" <?=chkFlag($field_obligacion['FlagCompromiso'])?> disabled="disabled" onchange="FlagCompromisoObligacion(this.checked);" /> Refiere Compromiso
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
            	<input type="checkbox" id="FlagPresupuesto" <?=chkFlag($field_obligacion['FlagPresupuesto'])?> disabled="disabled" /> Afecta Presupuesto
            </td>
    		<td class="tagForm">&nbsp;</td>
    		<td class="tagForm">&nbsp;</td>
    	</tr>
        <tr>
    		<td class="tagForm">&nbsp;</td>
    		<td>
            	<input type="checkbox" id="FlagDistribucionManual" <?=chkFlag($field_obligacion['FlagDistribucionManual'])?> disabled="disabled" /> Pago Directo
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
                            <input type="button" class="btLista" value="Insertar" id="btInsertarImpuesto" onclick="document.getElementById('aInsertarImpuesto').click();" />
                            <input type="button" class="btLista" value="Borrar" id="btQuitarImpuesto" onclick="quitarLineaImpuesto(this, 'impuesto');" />
                        </td>
                    </tr>
                </table>
                
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
    				$sql = "SELECT
    							i.CodImpuesto,
    							i.Descripcion,
    							i.Signo,
    							i.FlagImponible,
    							i.FlagProvision,
    							i.FactorPorcentaje,
    							i.CodCuenta,
    							i.CodCuentaPub20
    						FROM
    							masttiposervicio ts
    							INNER JOIN masttiposervicioimpuesto tsi ON (ts.CodTipoServicio = tsi.CodTipoServicio)
    							INNER JOIN mastimpuestos i ON (tsi.CodImpuesto = i.CodImpuesto AND i.CodRegimenFiscal = 'R')
    						WHERE ts.CodTipoServicio = '".$field_obligacion['CodTipoServicio']."'";
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
                            	<input type="text" name="MontoAfecto" value="0,00" style="text-align:right;" class="cell2" readonly="readonly" />
    						</td>
    						<td>
                            	<input type="text" name="FactorPorcentaje" value="<?=number_format($field_impuestos['FactorPorcentaje'], 2, ',', '.')?>" style="text-align:right;" class="cell2" readonly="readonly" />
    						</td>
    						<td>
                            	<input type="text" name="MontoImpuesto" value="0,00" style="text-align:right;" class="cell2" readonly="readonly" />
    						</td>
    					</tr>
    					<?php
    					$impuesto_total += $MontoImpuesto;
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
                            <input type="button" class="btLista" value="Insertar" id="btInsertarDocumento" disabled="disabled" />
                            <input type="button" class="btLista" value="Borrar" id="btQuitarDocumento" disabled="disabled" />
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
    				$linea_documento = split(";", $registro);	$_Linea=0;
    				foreach ($linea_documento as $documento) {	$_Linea++;
    					list($_Anio, $_DocumentoReferencia) = split("[.]", $documento);
    					//	consulto
    					$sql = "SELECT * 
    							FROM ap_documentos
    							WHERE 
    								Anio = '".$_Anio."' AND
    								CodProveedor = '".$fCodProveedor."' AND
    								DocumentoClasificacion = '".$fDocumentoClasificacion."' AND
    								DocumentoReferencia = '".$_DocumentoReferencia."'
    							ORDER BY DocumentoReferencia";
    					$query_documentos = mysql_query($sql) or die ($sql.mysql_error());	$nro_documento = 0;
    					while ($field_documentos = mysql_fetch_array($query_documentos)) {	$nro_documento++;
    						$iddoc = $field_documentos['ReferenciaTipoDocumento']."|".$field_documentos['ReferenciaNroDocumento']."|".$field_documentos['DocumentoClasificacion']."|".$field_documentos['DocumentoReferencia'];
    						if ($fDocumentoClasificacion != "SER") $clasificacion = "O.Compra"; else $clasificacion = "O.Servicio";
    						?>
    						<tr class="trListaBody" id="documento_<?=$iddoc?>">
    							<th><?=$nro_documento?></th>
    							<td>
    								<input type="text" value="<?=$clasificacion?>" class="cell2" readonly="readonly" />
    								<input type="hidden" name="Porcentaje" value="1" />
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
    						$documento_total += $field_documentos['MontoTotal'];
    						$documento_afecto += $field_documentos['MontoAfecto'];
    						$documento_impuesto += $field_documentos['MontoImpuestos'];
    						$documento_noafecto += $field_documentos['MontoNoAfecto'];
    					}
    				}
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
                            <input type="button" class="btLista" id="btSelPartida" value="Sel. Partida" disabled="disabled" />
                            <input type="button" class="btLista" id="btSelCuenta" value="Sel. Cuenta" disabled="disabled" />
                            <input type="button" class="btLista" id="btSelCuenta20" value="Cta. Pub. 20" disabled="disabled" />
                            <input type="button" class="btLista" id="btSelCCosto" value="Sel. C.Costo" disabled="disabled" />
                            <input type="button" class="btLista" id="btSelPersona" value="Sel. Persona" disabled="disabled" />
                            <input type="button" class="btLista" id="btSelActivo" value="Sel. Activo" disabled="disabled" />
                        </td>
                        <td align="right">
                            <input type="button" class="btLista" id="btInsertarDistribucion" value="Insertar" disabled="disabled" />
                            <input type="button" class="btLista" id="btQuitarDistribucion" value="Quitar" disabled="disabled" />
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
    				$linea_documento = split(";", $registro);	$_Linea=0;
    				foreach ($linea_documento as $documento) {	$_Linea++;
    					list($_Anio, $_DocumentoReferencia) = split("[.]", $documento);
    					//	consulto
    					$sql = "SELECT * 
    							FROM ap_documentos
    							WHERE 
    								Anio = '".$_Anio."' AND
    								CodProveedor = '".$fCodProveedor."' AND
    								DocumentoClasificacion = '".$fDocumentoClasificacion."' AND
    								DocumentoReferencia = '".$_DocumentoReferencia."'
    							ORDER BY DocumentoReferencia";
    					$query_documentos = mysql_query($sql) or die ($sql.mysql_error());
    					while ($field_documentos = mysql_fetch_array($query_documentos)) {
    						$TipoDoc = $field_documentos['ReferenciaTipoDocumento'];
    						$NroOrden = $field_documentos['ReferenciaNroDocumento'];
    						
    						//	verifico si la orden tiene activos fijos
    						if ($TipoDoc == "OC") {
    							$sql = "SELECT ocd.*
    									FROM 
    										lg_ordencompradetalle ocd
    										INNER JOIN lg_commoditysub cs ON (ocd.CommoditySub = cs.Codigo)
    										INNER JOIN lg_commoditymast cm ON (cs.CommodityMast = cm.CommodityMast)
    									WHERE
    										(cm.Clasificacion = 'BME' OR cm.Clasificacion = 'ACT') AND
    										ocd.Anio = '".$_Anio."' AND
    										ocd.CodOrganismo = '".$fCodOrganismo."' AND
    										ocd.NroOrden = '".$NroOrden."'";
    							$query_activo = mysql_query($sql) or die ($sql.mysql_error());
    							$rows_activo = mysql_num_rows($query_activo);
    						} else $rows_activo = 0;
    						
    						//	si es una orden de compra y no tiene activos fijos
    						if ($TipoDoc == "OC" && $rows_activo == 0) {
    							$sql = "SELECT
    										ocd.cod_partida,
    										pv.denominacion AS NomPartida,
    										ocd.CodCuenta,
    										pc.Descripcion AS NomCuenta,
    										ocd.CodCuentaPub20,
    										pc20.Descripcion AS NomCuentaPub20,
    										dd.CodCentroCosto,
    										'OC' AS TipoOrden,
    										ocd.NroOrden,
    										dd.DocumentoReferencia,
    										dd.Descripcion,
    										d.CodProveedor AS CodPersona,
    										SUM(dd.PrecioCantidad) AS Monto,
    										p.NomCompleto As NomPersona,
                                            ocd.CodPresupuesto,
                                            ocd.Ejercicio,
                                            ocd.CodFuente,
                                            pv1.CategoriaProg
    									FROM
    										ap_documentosdetalle dd
    										INNER JOIN ap_documentos d ON (d.Anio = dd.Anio AND
    																	   d.CodProveedor = dd.CodProveedor AND
    																	   d.DocumentoClasificacion = dd.DocumentoClasificacion AND
    																	   d.DocumentoReferencia = dd.DocumentoReferencia)
    										INNER JOIN lg_ordencompradetalle ocd ON (ocd.Anio = d.Anio AND
    																				 ocd.CodOrganismo = d.CodOrganismo AND
    																				 ocd.NroOrden = d.ReferenciaNroDocumento AND
    																				 ocd.Secuencia = dd.Secuencia AND
    																				 d.ReferenciaTipoDocumento = 'OC')
    										INNER JOIN mastpersonas p ON (p.CodPersona = d.CodProveedor)
    										
    										
    										LEFT JOIN pv_partida pv ON (pv.cod_partida = ocd.cod_partida)
    										LEFT JOIN ac_mastplancuenta pc ON (pc.CodCuenta = ocd.CodCuenta)
    										LEFT JOIN ac_mastplancuenta20 pc20 ON (pc20.CodCuenta = ocd.CodCuentaPub20)
                                            LEFT JOIN pv_presupuesto pv1 On (pv1.codOrganismo = ocd.CodOrganismo AND pv1.CodPresupuesto = ocd.CodPresupuesto)
    									WHERE
    										d.Anio = '".$field_documentos['Anio']."' AND 
    										d.CodProveedor = '".$field_documentos['CodProveedor']."' AND 
    										d.DocumentoClasificacion = '".$field_documentos['DocumentoClasificacion']."' AND 
    										d.DocumentoReferencia = '".$field_documentos['DocumentoReferencia']."'
    									GROUP BY cod_partida
    									ORDER BY cod_partida";
    							$query_distribucion = mysql_query($sql) or die ($sql.mysql_error());
    							while ($field_distribucion = mysql_fetch_array($query_distribucion)) {
    								$nro_distribucion++;
    								$Monto = $field_distribucion['Monto'];
    								?>
    								<tr class="trListaBody">
    									<th><?=$nro_distribucion?></th>
    									<td align="center" width="75">
    										<input type="text" name="cod_partida" id="cod_partida_<?=$nro_distribucion?>" value="<?=$field_distribucion['cod_partida']?>" style="width:99%; text-align:center;" maxlength="12" class="cell2" readonly="readonly" />
    									</td>
    									<td align="center" width="225">
    										<input type="text" name="NomPartida" id="NomPartida_<?=$nro_distribucion?>" value="<?=($field_distribucion['NomPartida'])?>" style="width:99%;" class="cell2" readonly="readonly" />
    									</td>
    									<td align="center" width="80">
    										<input type="text" name="CodCuenta" id="CodCuenta_<?=$nro_distribucion?>" value="<?=$field_distribucion['CodCuenta']?>" maxlength="13" style="width:99%; text-align:center;" class="cell2" readonly="readonly" />
    									</td>
    									<td align="center" width="220">
    										<input type="text" name="NomCuenta" id="NomCuenta_<?=$nro_distribucion?>" value="<?=($field_distribucion['NomCuenta'])?>" style="width:99%;" class="cell2" readonly="readonly" />
    									</td>
    									<td align="center" width="80">
    										<input type="text" name="CodCuentaPub20" id="CodCuentaPub20_<?=$nro_distribucion?>" value="<?=$field_distribucion['CodCuentaPub20']?>" maxlength="13" style="width:99%; text-align:center;" class="cell2" readonly="readonly" />
    									</td>
    									<td align="center" width="220">
    										<input type="text" name="NomCuentaPub20" id="NomCuentaPub20_<?=$nro_distribucion?>" value="<?=($field_distribucion['NomCuentaPub20'])?>" style="width:99%;" class="cell2" readonly="readonly" />
    									</td>
    									<td align="center">
    										<input type="text" name="CodCentroCosto" id="CodCentroCosto_<?=$nro_distribucion?>" value="<?=$field_distribucion['CodCentroCosto']?>" style="text-align:center;" class="cell2" readonly="readonly" />
    										<input type="hidden" name="NomCentroCosto" id="NomCentroCosto_<?=$nro_distribucion?>" value="<?=$field_distribucion['NomCentroCosto']?>" />
    									</td>
    									<td align="center">
    										<input type="checkbox" name="FlagNoAfectoIGV" class="FlagNoAfectoIGV" <?=$cFlagNoAfectoIGV?> disabled="disabled" />
    									</td>
    									<td align="center">
    										<input type="text" name="Monto" value="<?=number_format($field_distribucion['Monto'], 2, ',', '.')?>" style="text-align:right;" class="cell2" readonly="readonly" />
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
    										<input type="text" name="TipoOrden" value="<?=$field_distribucion['TipoOrden']?>" maxlength="2" style="width:99%; text-align:center;" class="cell2" readonly="readonly"/>
    									</td>
    									<td align="center" width="85">
    										<input type="text" name="NroOrden" value="<?=$field_distribucion['NroOrden']?>" maxlength="100" style="width:99%;" class="cell2" readonly="readonly" />
    									</td>
    									<td align="center">
    										<input type="text" name="Referencia" value="<?=$field_distribucion['DocumentoReferencia']?>" maxlength="25" class="cell2" readonly="readonly" />
    									</td>
    									<td align="center">
    										<input type="text" name="Descripcion" value="<?=($field_distribucion['Descripcion'])?>" maxlength="255" class="cell2" readonly="readonly" />
    									</td>
    									<td align="center">
    										<input type="text" name="CodPersona" id="CodPersona_<?=$nro_distribucion?>" value="<?=$field_distribucion['CodPersona']?>" maxlength="6" style="text-align:center;" class="cell2" readonly="readonly" />
    										<input type="hidden" name="NomPersona" id="NomPersona_<?=$nro_distribucion?>" value="<?=$field_distribucion['NomPersona']?>" />
    									</td>
    									<td align="center">
    										<input type="text" name="NroActivo" id="NroActivo_<?=$nro_distribucion?>" value="<?=$field_distribucion['NroActivo']?>" maxlength="15" style="text-align:center;" class="cell2" readonly="readonly" />
    									</td>
    									<td align="center">
    										<input type="checkbox" name="FlagDiferido" disabled="disabled" />
    									</td>
    								</tr>
    								<?php
    								$distribucion_total += $field_distribucion['Monto'];
    							}
    						}
    						//	si es una orden de servicio o si tiene activos fijos
    						elseif ($TipoDoc == "OS" || $rows_activo != 0) {
    							//	si es una orden de servicio
    							if ($TipoDoc == "OS") {
    								$sql = "SELECT
    											osd.cod_partida,
    											osd.CodCuenta,
    											osd.CodCuentaPub20,
    											osd.CodCentroCosto,
    											osd.PrecioUnit,
    											osd.NroOrden,
    											osd.Descripcion,
    											p.denominacion AS NomPartida,
    											pc.Descripcion AS NomCuenta,
    											pc20.Descripcion AS NomCuentaPub20,
    											os.CodProveedor,
    											d.DocumentoReferencia AS Referencia,
    											dd.Cantidad As CantidadPedida,
    											'OS' AS TipoOrden,
                                                osd.CodPresupuesto,
                                                osd.Ejercicio,
                                                osd.CodFuente,
                                                pv1.CategoriaProg
    										FROM 
    											ap_documentosdetalle dd
    											INNER JOIN ap_documentos d ON (dd.Anio = d.Anio AND
    																		   dd.CodProveedor = d.CodProveedor AND
    																		   dd.DocumentoClasificacion = d.DocumentoClasificacion AND
    																		   dd.DocumentoReferencia = d.DocumentoReferencia)
    											INNER JOIN lg_ordenserviciodetalle osd ON (d.Anio = osd.Anio AND
    																					   d.CodOrganismo = osd.CodOrganismo AND
    																					   d.ReferenciaNroDocumento = osd.NroOrden AND
    																					   osd.CommoditySub = dd.CommoditySub AND
    																					   osd.Secuencia = dd.ReferenciaSecuencia)
    											INNER JOIN lg_ordenservicio os ON (osd.Anio = os.Anio AND
    																			   osd.CodOrganismo = os.CodOrganismo AND
    																			   osd.NroOrden = os.NroOrden)
    											LEFT JOIN pv_partida p ON (osd.cod_partida = p.cod_partida)
    											LEFT JOIN ac_mastplancuenta pc ON (osd.CodCuenta = pc.CodCuenta)
    											LEFT JOIN ac_mastplancuenta20 pc20 ON (osd.CodCuentaPub20 = pc20.CodCuenta)
                                                LEFT JOIN pv_presupuesto pv1 On (pv1.codOrganismo = osd.CodOrganismo AND pv1.CodPresupuesto = osd.CodPresupuesto)
    										WHERE
    											dd.Anio = '".$field_documentos['Anio']."' AND 
    											dd.CodProveedor = '".$field_documentos['CodProveedor']."' AND 
    											dd.DocumentoClasificacion = '".$field_documentos['DocumentoClasificacion']."' AND 
    											dd.DocumentoReferencia = '".$field_documentos['DocumentoReferencia']."'
    										GROUP BY dd.DocumentoClasificacion, dd.DocumentoReferencia, dd.CodProveedor, dd.Secuencia
    										ORDER BY osd.Secuencia";
    							}
    							//	si tiene activos fijos
    							else {
    								$sql = "SELECT 
    											ocd.*,
    											p.denominacion AS NomPartida,
    											pc.Descripcion AS NomCuenta,
    											pc20.Descripcion AS NomCuentaPub20,
    											oc.CodProveedor AS CodPersona,
    											'OC' AS TipoOrden,
                                                ocd.CodPresupuesto,
                                                ocd.Ejercicio,
                                                ocd.CodFuente,
                                                pv1.CategoriaProg
    										FROM 
    											lg_ordencompradetalle ocd
    											INNER JOIN lg_ordencompra oc ON (ocd.Anio = oc.Anio AND
    																			 ocd.CodOrganismo = oc.CodOrganismo AND
    																			 ocd.NroOrden = oc.NroOrden)
    											LEFT JOIN pv_partida p ON (ocd.cod_partida = p.cod_partida)
    											LEFT JOIN ac_mastplancuenta pc ON (ocd.CodCuenta = pc.CodCuenta)
    											LEFT JOIN ac_mastplancuenta20 pc20 ON (ocd.CodCuentaPub20 = pc20.CodCuenta)
                                                LEFT JOIN pv_presupuesto pv1 On (pv1.codOrganismo = ocd.CodOrganismo AND pv1.CodPresupuesto = ocd.CodPresupuesto)
    										WHERE 
    											ocd.Anio = '".$_Anio."' AND
    											ocd.CodOrganismo = '".$fCodOrganismo."' AND
    											ocd.NroOrden = '".$NroOrden."'
    										ORDER BY Secuencia";
    							}
    							$query_distribucion = mysql_query($sql) or die ($sql.mysql_error());
    							while ($field_distribucion = mysql_fetch_array($query_distribucion)) {
    								for ($i=1; $i<=$field_distribucion['CantidadPedida']; $i++) {
    									$nro_distribucion++;
    									$Monto = $field_distribucion['Monto'];
    									?>
    									<tr class="trListaBody">
    										<th><?=$nro_distribucion?></th>
    										<td align="center" width="75">
                                                <input type="text" name="cod_partida" id="cod_partida_<?=$nro_distribucion?>" value="<?=$field_distribucion['cod_partida']?>" style="width:99%; text-align:center;" maxlength="12" class="cell2" readonly="readonly" />
                                            </td>
                                            <td align="center" width="225">
                                                <input type="text" name="NomPartida" id="NomPartida_<?=$nro_distribucion?>" value="<?=($field_distribucion['NomPartida'])?>" style="width:99%;" class="cell2" readonly="readonly" />
                                            </td>
                                            <td align="center" width="80">
                                                <input type="text" name="CodCuenta" id="CodCuenta_<?=$nro_distribucion?>" value="<?=$field_distribucion['CodCuenta']?>" maxlength="13" style="width:99%; text-align:center;" class="cell2" readonly="readonly" />
                                            </td>
                                            <td align="center" width="220">
                                                <input type="text" name="NomCuenta" id="NomCuenta_<?=$nro_distribucion?>" value="<?=($field_distribucion['NomCuenta'])?>" style="width:99%;" class="cell2" readonly="readonly" />
                                            </td>
                                            <td align="center" width="80">
                                                <input type="text" name="CodCuentaPub20" id="CodCuentaPub20_<?=$nro_distribucion?>" value="<?=$field_distribucion['CodCuentaPub20']?>" maxlength="13" style="width:99%; text-align:center;" class="cell2" readonly="readonly" />
                                            </td>
                                            <td align="center" width="220">
                                                <input type="text" name="NomCuentaPub20" id="NomCuentaPub20_<?=$nro_distribucion?>" value="<?=($field_distribucion['NomCuentaPub20'])?>" style="width:99%;" class="cell2" readonly="readonly" />
                                            </td>
    										<td align="center">
    											<input type="text" name="CodCentroCosto" id="CodCentroCosto_<?=$nro_distribucion?>" value="<?=$field_distribucion['CodCentroCosto']?>" style="text-align:center;" class="cell2" readonly="readonly" />
    											<input type="hidden" name="NomCentroCosto" id="NomCentroCosto_<?=$nro_distribucion?>" value="<?=$field_distribucion['NomCentroCosto']?>" />
    										</td>
    										<td align="center">
    											<input type="checkbox" name="FlagNoAfectoIGV" class="FlagNoAfectoIGV" <?=$cFlagNoAfectoIGV?> disabled="disabled" />
    										</td>
    										<td align="center">
    											<input type="text" name="Monto" value="<?=number_format($field_distribucion['PrecioUnit'], 2, ',', '.')?>" style="text-align:right;" class="cell2" readonly="readonly" />
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
                                                <input type="text" name="TipoOrden" value="<?=$field_distribucion['TipoOrden']?>" maxlength="2" style="width:99%; text-align:center;" class="cell2" readonly="readonly"/>
                                            </td>
                                            <td align="center" width="85">
                                                <input type="text" name="NroOrden" value="<?=$field_distribucion['NroOrden']?>" maxlength="100" style="width:99%;" class="cell2" readonly="readonly" />
                                            </td>
    										<td align="center">
    											<input type="text" name="Referencia" value="<?=$field_distribucion['Referencia']?>" maxlength="25" class="cell2" readonly="readonly" />
    										</td>
    										<td align="center">
    											<input type="text" name="Descripcion" value="<?=($field_distribucion['Descripcion'])?>" maxlength="255" class="cell2" readonly="readonly" />
    										</td>
    										<td align="center">
    											<input type="text" name="CodPersona" id="CodPersona_<?=$nro_distribucion?>" value="<?=$field_distribucion['CodPersona']?>" maxlength="6" style="text-align:center;" class="cell2" readonly="readonly" />
    											<input type="hidden" name="NomPersona" id="NomPersona_<?=$nro_distribucion?>" value="<?=$field_distribucion['NomPersona']?>" />
    										</td>
    										<td align="center">
    											<input type="text" name="NroActivo" id="NroActivo_<?=$nro_distribucion?>" value="<?=$field_distribucion['NroActivo']?>" maxlength="15" style="text-align:center;" class="cell2" readonly="readonly" />
    										</td>
    										<td align="center">
    											<input type="checkbox" name="FlagDiferido" disabled="disabled" />
    										</td>
    									</tr>
    									<?php
    									$distribucion_total += $field_distribucion['PrecioUnit'];
    								}
    							}
    						}
    					}
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
        </tbody>
    </table>
    </div>

    <div style="width:1100px;" class="divFormCaption">Distribuci&oacute;n Presupuestaria</div>
    <div style="overflow:scroll; width:1100px; height:150px;">
    <form name="frm_partidas" id="frm_partidas">
    <table width="100%" class="tblLista">
    	<thead>
    	<tr>
            <th width="25">F.F.</th>
            <th width="100">Partida</th>
            <th>Descripci&oacute;n</th>
            <th width="100">Monto</th>
        </tr>
        </thead>
        
        <tbody id="lista_partidas">
        </tbody>
    </table>
    </form>
    </div>
    </center>
</div>

<div id="tab5" style="display:none;"></div>

<script>
$(document).ready(function() {
	actualizar_montos_obligacion();
});
</script>