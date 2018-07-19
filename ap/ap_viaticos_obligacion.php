<?php
$Estado = "PR";
$IngresadoPor = $_SESSION["CODPERSONA_ACTUAL"];
$NomIngresadoPor = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
$FechaPreparacion = $FechaActual;
$FechaFactura = $FechaActual;
$FechaRegistro = $FechaActual;
$FechaDocumento = $FechaActual;
$FechaRecepcion = $FechaActual;
$FechaVencimiento = $FechaActual;
$FechaProgramada = $FechaActual;
$CodTipoServicio = "NING";
$FlagGenerarPago = "S";
$FlagCompromiso = "S";
$FlagPresupuesto = "S";
$FlagDistribucionManual = "N";
##
$accion = "nuevo";
$titulo = "Nueva Obligaci&oacute;n";
$_width = 1100;
//	------------------------------------
list($CodOrganismo, $CodViatico) = explode("_", $sel_registros);
$sql = "SELECT
			v.*,
			p1.NomCompleto,
			p1.Busqueda,
			p1.DocFiscal,
			e1.CodTipoPago,
                pv.CategoriaProg,
                pv.Ejercicio
		FROM
			ap_viaticos v
			INNER JOIN mastpersonas p1 ON (p1.CodPersona = v.CodPersona)
			LEFT JOIN mastempleado e1 ON (e1.CodPersona = p1.CodPersona)
                LEFT JOIN pv_presupuesto pv ON (pv.CodOrganismo = v.CodOrganismo AND pv.CodPresupuesto = v.CodPresupuesto)
		WHERE
			v.CodOrganismo = '".$CodOrganismo."' AND
			v.CodViatico = '".$CodViatico."'";
$field = getRecord($sql);
$NroControl = $_PARAMETRO['DOCVIAT'].'-'.$field['Anio'].$field['CodInterno'];
//	------------------------------------
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
            <li id="li4" onclick="currentTab('tab', this);"><a href="#" onclick="mostrarTab('tab', 4, 5);">Resumen Contable y Presup.</a></li>
            <li id="li5" onclick="currentTab('tab', this);"><a href="#" onclick="mostrarTab('tab', 5, 5);">Adelantos y Pagos Parciales</a></li>
            </ul>
            </div>
        </td>
    </tr>
</table>

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=ap_viaticos_lista" method="POST" autocomplete="off" onsubmit="return obligacion(this, 'nuevo');">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
<input type="hidden" name="fCodDependencia" id="fCodDependencia" value="<?=$fCodDependencia?>" />
<input type="hidden" name="fFechaPreparadoD" id="fFechaPreparadoD" value="<?=$fFechaPreparadoD?>" />
<input type="hidden" name="fFechaPreparadoH" id="fFechaPreparadoH" value="<?=$fFechaPreparadoH?>" />
<input type="hidden" name="fPeriodo" id="fPeriodo" value="<?=$fPeriodo?>" />
<input type="hidden" name="CodPresupuesto" id="CodPresupuesto" value="<?=$field['CodPresupuesto']?>" />
<input type="hidden" name="NroDocumento" id="NroDocumento" />
<input type="hidden" id="FlagNomina" value="N" />

<div id="tab1" style="display:block;">
<table width="1100" class="tblForm">
	<tr>
    	<td colspan="4" class="divFormCaption">Informaci&oacute;n del Proveedor</td>
    </tr>
    <tr>
		<td class="tagForm" width="125">* Proveedor:</td>
		<td class="gallery clearfix">
        	<input type="text" id="CodProveedor" value="<?=$field['CodPersona']?>" disabled="disabled" style="width:100px;" />
			<input type="text" id="NomCompleto" value="<?=($field['NomCompleto'])?>" disabled="disabled" style="width:250px;" />
        </td>
		<td class="tagForm" width="125">Dias Pago:</td>
		<td><input type="text" id="DiasPago" style="width:50px;" <?=$disabled_ver?> /></td>
	</tr>
    <tr>
		<td class="tagForm">R.I.F:</td>
		<td>
        	<input type="text" id="DocFiscal" style="width:100px;" value="<?=$field['DocFiscal']?>" disabled="disabled" />
            <input type="text" id="Busqueda" style="width:250px;" value="<?=($field['Busqueda'])?>" disabled="disabled" />
        </td>
		<td class="tagForm">* Pagar A:</td>
		<td class="gallery clearfix">
        	<input type="text" id="CodProveedorPagar" value="<?=$field['CodPersona']?>" maxlength="6" style="width:50px;" onchange="getDescripcionLista('accion=getDescripcionPersona&flagproveedor=S&flagempleado=S&flagotros=S', this, 'nompagara');" disabled="disabled" />
			<input type="text" id="NomProveedorPagar" value="<?=($field['NomCompleto'])?>" style="width:250px;" disabled="disabled" />
			<a href="../lib/listas/listado_personas.php?filtrar=default&cod=CodProveedorPagar&nom=NomProveedorPagar&EsEmpleado=S&EsProveedor=S&EsOtros=S&iframe=true&width=825&height=400" rel="prettyPhoto[iframe2]" style=" <?=$display_ver?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
	</tr>
	<tr>
		<td class="tagForm">* Organismo:</td>
		<td>
        	<select id="CodOrganismo" style="width:300px;" disabled="disabled">
            	<?=loadSelect("mastorganismos", "CodOrganismo", "Organismo", $field['CodOrganismo'], 0)?>
            </select>
		</td>
		<td class="tagForm">* Centro Costo:</td>
		<td class="gallery clearfix">
        	<input type="text" id="CodCentroCosto" value="<?=$field['CodCentroCosto']?>" style="width:50px;" disabled="disabled" />
			<input type="hidden" id="NomCentroCosto" />
			<a href="../lib/listas/listado_centro_costos.php?filtrar=default&cod=CodCentroCosto&nom=NomCentroCosto&iframe=true&width=825&height=400" rel="prettyPhoto[iframe3]" style=" <?=$display_ver?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
	</tr>
    <tr>
		<td class="tagForm">* Tipo de Documento:</td>
		<td>
        	<select id="CodTipoDocumento" style="width:300px;" disabled="disabled">
                <?=loadSelect("ap_tipodocumento", "CodTipoDocumento", "Descripcion", $_PARAMETRO['DOCVIAT'], 1);?>
            </select>
        </td>
		<td class="tagForm">Nro. Registro:</td>
		<td><input type="text" id="NroRegistro" style="width:100px;" class="codigo" disabled="disabled" /></td>
	</tr>
    <tr>
		<td class="tagForm">* Nro. Control:</td>
		<td><input type="text" id="NroControl" value="<?=$NroControl?>" maxlength="20" style="width:150px;" /></td>
		<td class="tagForm">* Nro. Factura:</td>
		<td><input type="text" id="NroFactura" value="<?=$NroControl?>" maxlength="20" style="width:150px;" /></td>
	</tr>
    <tr>
		<td height="22" class="tagForm">Estado:</td>
		<td>
       	  <input type="hidden" id="Estado" value="<?=$Estado?>" />
        	<input type="text" style="width:100px;" class="codigo" value="<?=printValores("ESTADO-OBLIGACIONES", $Estado)?>" disabled="disabled" />
		</td>
        <td class="tagForm">Fecha Factura:</td>
		<td><input type="text" id="FechaFactura" value="<?=formatFechaDMA($FechaFactura)?>" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" <?=$disabled_ver?> /></td>
	</tr>
</table>

<table width="1100" class="tblForm">
    <tr>
		<td width="50%" valign="top">
        	<table width="100%">
            	<tr><td colspan="2" class="divFormCaption">Fechas del Documento</td></tr>
            	<tr>
                	<td class="tagForm" width="125"><strong>Obligaci&oacute;n:</strong></td>
                    <td><input type="text" id="FechaRegistro" value="<?=formatFechaDMA($FechaRegistro)?>" style="width:100px;" class="datepicker codigo" onkeyup="setFechaDMA(this);" /></td>
                </tr>
            	<tr>
                	<td class="tagForm">Emisi&oacute;n:</td>
                    <td><input type="text" id="FechaDocumento" value="<?=formatFechaDMA($FechaDocumento)?>" style="width:100px;" class="datepicker" onkeyup="setFechaDMA(this);" <?=$disabled_ver?> /></td>
                </tr>
            	<tr>
                	<td class="tagForm">Recepci&oacute;n:</td>
                    <td><input type="text" id="FechaRecepcion" value="<?=formatFechaDMA($FechaRecepcion)?>" style="width:100px;" class="datepicker" onkeyup="setFechaDMA(this);" <?=$disabled_ver?> /></td>
                </tr>
            	<tr>
                	<td class="tagForm">Vencimiento:</td>
                    <td><input type="text" id="FechaVencimiento" value="<?=formatFechaDMA($FechaVencimiento)?>" style="width:100px;" class="datepicker" onkeyup="setFechaDMA(this);" <?=$disabled_ver?> /></td>
                </tr>
            	<tr>
                	<td class="tagForm">Prog. Pago:</td>
                    <td><input type="text" id="FechaProgramada" value="<?=formatFechaDMA($FechaProgramada)?>" style="width:100px;" class="datepicker" onkeyup="setFechaDMA(this);" <?=$disabled_ver?> /></td>
                </tr>
                    <tr>
                        <td colspan="2" class="divFormCaption">Presupuesto</td>
                    </tr>
                    <tr>
                        <td class="tagForm" width="150">Presupuesto:</td>
                        <td class="gallery clearfix">
                            <input type="text" name="Ejercicio" id="Ejercicio" value="<?=$field['Ejercicio']?>" style="width:48px;" class="Ejercicio" readonly />
                            <input type="text" name="CodPresupuesto" id="CodPresupuesto" value="<?=$field['CodPresupuesto']?>" style="width:48px;" class="CodPresupuesto" readonly />
                            
                        </td>
                    </tr>
                    <tr>
                        <td class="tagForm">Cat. Prog.:</td>
                        <td><input type="text" name="CategoriaProg" id="CategoriaProg" value="<?=$field['CategoriaProg']?>" style="width:100px;" class="CategoriaProg" readonly /></td>
                    </tr>
                    <tr>
                        <td class="tagForm">Fuente de Financiamiento:</td>
                        <td>
                            <select name="CodFuente" id="CodFuente" style="width:250px;" onchange="$('.CodFuente').val(this.value);" <?=$disabled_ver?>>
                                <?=loadSelect2("pv_fuentefinanciamiento","CodFuente","Denominacion",$field['CodFuente'],11)?>
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
                        <select id="CodTipoServicio" style="width:150px;" disabled="disabled">
                            <?=loadSelect("masttiposervicio", "CodTipoServicio", "Descripcion", $CodTipoServicio, 0)?>
                        </select>
                    </td>
                </tr>
            	<tr>
                	<td class="tagForm">* Tipo de Pago:</td>
                    <td>
                        <select id="CodTipoPago" style="width:150px;" onchange="ctabancariadefault($('#CodOrganismo').val(), $(this).val(), $('#NroCuenta'));">
                            <?=loadSelect("masttipopago", "CodTipoPago", "TipoPago", $field['CodTipoPago'], 0)?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="tagForm">Ingresado Por:</td>
                    <td>
                        <input type="hidden" id="IngresadoPor" value="<?=$IngresadoPor?>" />
                        <input type="text" id="NomIngresadoPor" value="<?=htmlentities($NomIngresadoPor)?>" style="width:245px;" disabled="disabled" />
                        <input type="text" id="FechaPreparacion" value="<?=formatFechaDMA($FechaPreparacion)?>" style="width:60px;" disabled="disabled" />
                    </td>
                </tr>
                <tr>
                    <td class="tagForm">Revisado Por:</td>
                    <td>
                        <input type="hidden" id="RevisadoPor" />
                        <input type="text" id="NomRevisadoPor" style="width:245px;" disabled="disabled" />
                        <input type="text" id="FechaRevision" style="width:60px;" disabled="disabled" />
                    </td>
                </tr>
                <tr>
                    <td class="tagForm">Aprobador CxP:</td>
                    <td>
                        <input type="hidden" id="AprobadoPor" />
                        <input type="text" id="NomAprobadoPor" style="width:245px;" disabled="disabled" />
                        <input type="text" id="FechaAprobado" style="width:60px;" disabled="disabled" />
                    </td>
                </tr>
            </table>
        </td>
	</tr>
</table>

<table width="1100" class="tblForm">
	<tr>
		<td class="tagForm" width="125">Glosa del Voucher:</td>
		<td><input type="text" id="Comentarios" value="<?=htmlentities($field['Motivo'])?>" style="width:95%;" /></td>
	</tr>
	<tr>
		<td class="tagForm">Comentarios Adicional:</td>
		<td><textarea id="ComentariosAdicional" style="width:95%; height:45px;"><?=htmlentities($field['Motivo'])?></textarea></td>
	</tr>
	<tr>
		<td class="tagForm">Raz&oacute;n Anulaci&oacute;n:</td>
		<td><input type="text" id="MotivoAnulacion" style="width:95%;" disabled="disabled" /></td>
	</tr>
	<tr>
		<td class="tagForm">&Uacute;ltima Modif.:</td>
		<td>
			<input type="text" size="30" disabled="disabled" />
			<input type="text" size="25" disabled="disabled" />
		</td>
	</tr>
</table>
<center>
<input type="submit" value="Generar" style="width:75px;" />
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
		<td><input type="text" id="ReferenciaDocumento" style="width:195px;" value="<?=$_PARAMETRO['DOCVIAT']?>-<?=$field['CodViatico']?>" disabled="disabled" /></td>
		<td class="tagForm" width="150">Monto Afecto:</td>
		<td>
        	<input type="text" id="MontoAfecto" value="0,00" style="width:150px; text-align:right;" disabled="disabled" />
        </td>
	</tr>
    <tr>
		<td class="tagForm">* Cuenta Bancaria:</td>
		<td>
        	<select id="NroCuenta" style="width:200px;">
                <?=loadSelect("ap_ctabancaria", "NroCuenta", "NroCuenta", $NroCuenta, 0)?>
            </select>
        </td>
		<td class="tagForm">Monto No Afecto:</td>
		<td>
        	<input type="text" id="MontoNoAfecto" value="<?=number_format($field['Monto'], 2, ',', '.')?>" style="width:150px; text-align:right;" disabled="disabled" />
        </td>
	</tr>
    <tr>
		<td class="tagForm">&nbsp;</td>
		<td>
        	<input type="checkbox" id="FlagCajaChica" /> Pago con Caja Chica (Efectivo)
        </td>
		<td class="tagForm">Impuesto:</td>
		<td>
        	<input type="text" id="MontoImpuesto" value="0,00" style="width:150px; text-align:right;" disabled="disabled" />
        </td>
	</tr>
    <tr>
		<td class="tagForm">&nbsp;</td>
		<td>
        	<input type="checkbox" id="FlagPagoIndividual" /> Preparar Pago Individual
        </td>
		<td class="tagForm">Otros Impuestos/Retenciones:</td>
		<td>
        	<input type="text" id="MontoImpuestoOtros" value="0,00" style="width:150px; text-align:right;" disabled="disabled" />
        </td>
	</tr>
    <tr>
		<td class="tagForm">&nbsp;</td>
		<td>
        	<input type="checkbox" id="FlagGenerarPago" <?=chkFlag($FlagGenerarPago)?> /> Preparar Pago (Autom&aacute;tico)
        </td>
		<td class="tagForm"><strong>Total Obligaci&oacute;n:</strong></td>
		<td>
        	<input type="text" id="MontoObligacion" value="<?=number_format($field['Monto'], 2, ',', '.')?>" style="width:150px; text-align:right; font-size:12px; font-weight:bold;" class="codigo" disabled="disabled" />
        </td>
	</tr>
    <tr>
		<td class="tagForm">&nbsp;</td>
		<td>
        	<input type="checkbox" id="FlagPagoDiferido" /> Diferir el Pago
        </td>
		<td class="tagForm">Adelanto:</td>
		<td>
        	<input type="text" id="MontoAdelanto" value="0,00" style="width:150px; text-align:right;" disabled="disabled" />
        </td>
	</tr>
    <tr>
		<td class="tagForm">&nbsp;</td>
		<td>
        	<input type="checkbox" id="FlagDiferido" /> Considerarlo como Diferido
        </td>
		<td class="tagForm"><strong>Total a Pagar:</strong></td>
		<td>
        	<input type="text" id="MontoPagar" value="<?=number_format($field['Monto'], 2, ',', '.')?>" style="width:150px; text-align:right; font-size:12px; font-weight:bold;" class="codigo" disabled="disabled" />
        </td>
	</tr>
    <tr>
		<td class="tagForm">&nbsp;</td>
		<td>
        	<input type="checkbox" id="FlagAfectoIGV" /> Afecto a Defracción de IGV
        </td>
		<td class="tagForm">Pagos Parciales:</td>
		<td><input type="text" id="MontoPagoParcial" value="0,00" style="width:150px; text-align:right;" disabled="disabled" /></td>
	</tr>
    <tr>
		<td class="tagForm">&nbsp;</td>
		<td>
        	<input type="checkbox" id="FlagCompromiso" <?=chkFlag($FlagCompromiso)?> disabled="disabled" /> Refiere Compromiso
        </td>
		<td class="tagForm"><strong>Saldo Pendiente:</strong></td>
		<td>
        	<input type="text" id="MontoPendiente" value="<?=number_format($field['Monto'], 2, ',', '.')?>" style="width:150px; text-align:right; font-size:12px; font-weight:bold;" disabled="disabled" />
        </td>
	</tr>
    <tr>
		<td class="tagForm">&nbsp;</td>
		<td>
        	<input type="checkbox" id="FlagPresupuesto" <?=chkFlag($FlagPresupuesto)?> disabled="disabled" /> Afecta Presupuesto
        </td>
		<td class="tagForm">&nbsp;</td>
		<td class="tagForm">&nbsp;</td>
	</tr>
    <tr>
		<td class="tagForm">&nbsp;</td>
		<td>
        	<input type="checkbox" id="FlagDistribucionManual" <?=chkFlag($FlagDistribucionManual)?> disabled="disabled" /> Pago Directo
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
                    <td align="right">
                        <input type="button" class="btLista" value="Insertar" id="btInsertarImpuesto" disabled="disabled" />
                        <input type="button" class="btLista" value="Borrar" id="btQuitarImpuesto" disabled="disabled" />
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
                </tbody>
                
                <tfoot>
                <tr>
                    <th colspan="4">&nbsp;</th>
                    <th>
                        <input type="text" id="impuesto_total" value="0,00" style="text-align:right; font-weight:bold;" class="cell" disabled="disabled" />
                    </th>
                </tr>
                </tfoot>
            </table>
            </div></td></tr></table>
            <input type="hidden" id="nro_impuesto" />
            <input type="hidden" id="can_impuesto" />
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
                    <th width="100">O.C / O.S</th>
                    <th width="100" align="right">Monto Total</th>
                    <th width="100" align="right">Monto Afecto</th>
                    <th width="100" align="right">Impuesto</th>
                    <th width="100" align="right">Monto No Afecto</th>
                    <th align="left">Comentarios</th>
                </tr>
                </thead>
                
                <tbody id="lista_documento">
                </tbody>
                
                <tfoot id="foot_documento">
                <tr>
                    <th colspan="5">&nbsp;</th>
                    <th>
                       	<input type="text" id="documento_total" value="0,00" style="text-align:right; font-weight:bold;" class="cell" disabled="disabled" />
                    </th>
                    <th>
                       	<input type="text" id="documento_afecto" value="0,00" style="text-align:right; font-weight:bold;" class="cell" disabled="disabled" />
                    </th>
                    <th>
                       	<input type="text" id="documento_impuesto" value="0,00" style="text-align:right; font-weight:bold;" class="cell" disabled="disabled" />
                    </th>
                    <th>
                       	<input type="text" id="documento_noafecto" value="0,00" style="text-align:right; font-weight:bold;" class="cell" disabled="disabled" />
                    </th>
                </tr>
                </tfoot>
            </table>
            </div></td></tr></table>
            <input type="hidden" id="nro_documento" />
            <input type="hidden" id="can_documento" />
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
                        <a id="aSelCCosto" href="../lib/listas/listado_centro_costos.php?filtrar=default&cod=CodCentroCosto&nom=CodCentroCosto&ventana=selListadoLista&seldetalle=sel_distribucion&iframe=true&width=825&height=400" rel="prettyPhoto[iframe8]" style="display:none;"></a>
                        <input type="button" class="btLista" id="btSelPartida" value="Sel. Partida" disabled="disabled" />
                        <input type="button" class="btLista" id="btSelCuenta" value="Sel. Cuenta" disabled="disabled" />
                        <input type="button" class="btLista" id="btSelCuenta20" value="Cta. Pub. 20" disabled="disabled" />
                        <input type="button" class="btLista" id="btSelCCosto" value="Sel. C.Costo" onclick="validarAbrirLista('sel_distribucion', 'aSelCCosto');" />
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
				$sql = "SELECT
							vd.*,
							pv.denominacion As NomPartida,
							pc.Descripcion AS NomCuenta,
							pc20.Descripcion As NomCuentaPub20, 
                                p.CategoriaProg
						FROM
							ap_viaticosdetalle vd
							INNER JOIN pv_partida pv ON (pv.cod_partida = vd.cod_partida)
							LEFT JOIN ac_mastplancuenta pc ON (pc.CodCuenta = vd.CodCuenta)
							LEFT JOIN ac_mastplancuenta20 pc20 ON (pc20.CodCuenta = vd.CodCuentaPub20)
                                LEFT JOIN pv_presupuesto p On (p.CodOrganismo = vd.CodOrganismo AND p.CodPresupuesto = vd.CodPresupuesto)
						WHERE
							vd.CodOrganismo = '".$CodOrganismo."' AND
							vd.CodViatico = '".$CodViatico."'";
				$field_distribucion = getRecords($sql);
				foreach($field_distribucion as $f) {
                    $nro_distribucion++;
                    ?>
                    <tr class="trListaBody" onclick="mClk(this, 'sel_distribucion');" id="distribucion_<?=$nro_distribucion?>">
                        <th><?=$nro_distribucion?></th>
                        <td align="center" width="75">
                            <input type="text" name="cod_partida" id="cod_partida_<?=$nro_distribucion?>" value="<?=$f['cod_partida']?>" style="width:99%; text-align:center;" maxlength="12" class="cell cod_partida" disabled="disabled" />
                        </td>
                        <td align="center" width="225">
                            <input type="text" name="NomPartida" id="NomPartida_<?=$nro_distribucion?>" value="<?=htmlentities($f['NomPartida'])?>" style="width:99%;" class="cell" disabled="disabled" />
                        </td>
                        <td align="center" width="80">
                            <input type="text" name="CodCuenta" id="CodCuenta_<?=$nro_distribucion?>" value="<?=$f['CodCuenta']?>" maxlength="13" style="width:99%; text-align:center;" class="cell" disabled="disabled" />
                        </td>
                        <td align="center" width="220">
                            <input type="text" name="NomCuenta" id="NomCuenta_<?=$nro_distribucion?>" value="<?=htmlentities($f['NomCuenta'])?>" style="width:99%;" class="cell" disabled="disabled" />
                        </td>
                        <td align="center" width="80">
                            <input type="text" name="CodCuentaPub20" id="CodCuentaPub20_<?=$nro_distribucion?>" value="<?=$f['CodCuentaPub20']?>" maxlength="13" style="width:99%; text-align:center;" class="cell" disabled="disabled" />
                        </td>
                        <td align="center" width="220">
                            <input type="text" name="NomCuentaPub20" id="NomCuentaPub20_<?=$nro_distribucion?>" value="<?=htmlentities($f['NomCuentaPub20'])?>" style="width:99%;" class="cell" disabled="disabled" />
                        </td>
                        <td align="center">
                            <input type="text" name="CodCentroCosto" id="CodCentroCosto_<?=$nro_distribucion?>" value="<?=$field['CodCentroCosto']?>" style="text-align:center;" class="cell" disabled="disabled" />
                        </td>
                        <td align="center">
                            <input type="checkbox" name="FlagNoAfectoIGV" class="FlagNoAfectoIGV" <?=chkFlag('S')?> disabled="disabled" />
                        </td>
                        <td align="center">
                            <input type="text" name="Monto" value="<?=number_format($f['MontoTotal'], 2, ',', '.')?>" style="text-align:right;" class="cell" disabled="disabled" />
                        </td>
                            <td align="center">
                                <input type="text" name="detallesCategoriaProg" id="detallesCategoriaProg_<?=$nrodetalles?>" class="cell2 CategoriaProg" style="text-align:center;" value="<?=$f['CategoriaProg']?>" readonly />
                                <input type="hidden" name="detallesEjercicio" id="detallesEjercicio_<?=$nrodetalles?>" class="cell2 Ejercicio" style="text-align:center;" value="<?=$f['Ejercicio']?>" readonly />
                                <input type="hidden" name="detallesCodPresupuesto" id="detallesCodPresupuesto_<?=$nrodetalles?>" class="cell2 CodPresupuesto" style="text-align:center;" value="<?=$f['CodPresupuesto']?>" readonly />
                            </td>
                            <td>
                                <select name="detallesCodFuente" id="detallesCodFuente_<?=$nrodetalles?>" class="cell2 CodFuente" <?=$disabled_ver?>>
                                    <?=loadSelect2("pv_fuentefinanciamiento","CodFuente","Denominacion",$f['CodFuente'],11)?>
                                </select>
                            </td>
                        <td align="center" width="25">
                            <input type="text" name="TipoOrden" value="<?=$_PARAMETRO['DOCVIAT']?>" maxlength="2" style="width:99%; text-align:center;" class="cell" disabled="disabled" />
                        </td>
                        <td align="center" width="85">
                            <input type="text" name="NroOrden" value="<?=$field['Anio']?>-<?=$field['CodInterno']?>" maxlength="100" style="width:99%;" class="cell" disabled="disabled" />
                        </td>
                        <td align="center">
                            <input type="text" name="Referencia" value="<?=$f['CodViatico']?>-<?=$f['Secuencia']?>" maxlength="25" class="cell" disabled="disabled" />
                        </td>
                        <td align="center">
                            <input type="text" name="Descripcion" value="<?=htmlentities($f['Descripcion'])?>" maxlength="255" class="cell" disabled="disabled" />
                        </td>
                        <td align="center">
                            <input type="text" name="CodPersona" id="CodPersona_<?=$nro_distribucion?>" value="<?=$field['CodPersona']?>" maxlength="6" style="text-align:center;" class="cell" disabled="disabled" />
                        </td>
                        <td align="center">
                            <input type="text" name="NroActivo" id="NroActivo_<?=$nro_distribucion?>" maxlength="15" style="text-align:center;" class="cell" disabled="disabled" />
                        </td>
                        <td align="center">
                            <input type="checkbox" name="FlagDiferido" disabled="disabled" />
                        </td>
                    </tr>
                    <?php
                    $distribucion_total += $f['MontoTotal'];
                }
                ?>
                </tbody>
                
                <tfoot id="foot_distribucion">
                <tr>
                    <th colspan="9">&nbsp;</th>
                    <th>
                       	<input type="text" id="distribucion_total" value="<?=number_format($distribucion_total, 2, ',', '.')?>" style="text-align:right; font-weight:bold;" class="cell" disabled="disabled" />
                    </th>
                </tr>
                </tfoot>
            </table>
            </div></td></tr></table>
            <input type="hidden" id="nro_distribucion" />
            <input type="hidden" id="can_distribucion" />
            </form>
        </td>
    </tr>
</table>
</div>

<div id="tab4" style="display:none;">
<center>
<div style="width:<?=$_width-5?>px;" class="divFormCaption">Distribuci&oacute;n Contable</div>
<div style="overflow:scroll; width:<?=$_width?>px; height:102px;">
<table width="100%" class="tblLista">
    <thead>
    <tr>
        <th width="100">C&oacute;digo</th>
        <th align="left">Descripci&oacute;n</th>
        <th width="125">Monto Total</th>
    </tr>
    </thead>
    
    <tbody id="lista_cuentas">
    <?php
	$sql = "SELECT
				vd.CodCuenta As Codigo,
				vd.Monto,
				pc.Descripcion
			FROM
				ap_viaticosdistribucion vd
				INNER JOIN ac_mastplancuenta pc ON (pc.CodCuenta = vd.CodCuenta)
			WHERE
				vd.CodOrganismo = '".$CodOrganismo."' AND
				vd.CodViatico = '".$CodViatico."'
			ORDER BY Codigo";
	$field_cuentas = getRecords($sql);
	foreach($field_cuentas as $f) {
		?>
        <tr class="trListaBody">
            <td align="center"><?=$f['Codigo']?></td>
            <td><?=$f['Descripcion']?></td>
            <td align="right"><strong><?=number_format($f['Monto'], 2, ',', '.')?></strong></td>
        </tr>
        <?php
	}
    ?>
    </tbody>
</table>
</div>

<div style="width:<?=$_width-5?>px;" class="divFormCaption">Distribuci&oacute;n Contable (Pub. 20)</div>
<div style="overflow:scroll; width:<?=$_width?>px; height:102px;">
<table width="100%" class="tblLista">
    <thead>
    <tr>
        <th width="100">C&oacute;digo</th>
        <th align="left">Descripci&oacute;n</th>
        <th width="125">Monto Total</th>
    </tr>
    </thead>
    
    <tbody id="lista_cuentas20">
    <?php
	$sql = "SELECT
				vd.CodCuentaPub20 As Codigo,
				vd.Monto,
				pc.Descripcion
			FROM
				ap_viaticosdistribucion vd
				INNER JOIN ac_mastplancuenta20 pc ON (pc.CodCuenta = vd.CodCuentaPub20)
			WHERE
				vd.CodOrganismo = '".$CodOrganismo."' AND
				vd.CodViatico = '".$CodViatico."'
			ORDER BY Codigo";
	$field_cuentas20 = getRecords($sql);
	foreach($field_cuentas20 as $f) {
		?>
        <tr class="trListaBody">
            <td align="center"><?=$f['Codigo']?></td>
            <td><?=$f['Descripcion']?></td>
            <td align="right"><strong><?=number_format($f['Monto'], 2, ',', '.')?></strong></td>
        </tr>
        <?php
	}
    ?>
    </tbody>
</table>
</div>

<div style="width:<?=$_width-5?>px;" class="divFormCaption">Distribuci&oacute;n Presupuestaria</div>
<div style="overflow:scroll; width:<?=$_width?>px; height:102px;">
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
	$sql = "SELECT
				do.cod_partida,
				do.Monto,
				do.CodPresupuesto,
                do.CodOrganismo,
				p.denominacion,
                    pv.Ejercicio,
                    do.CodPresupuesto,
                    do.CodFuente,
                    pv.CategoriaProg,
                    ff.Denominacion AS Fuente,
                    ue.Denominacion AS UnidadEjecutora,
                    CONCAT(ss.CodSector, pr.CodPrograma, a.CodActividad) AS CatProg
			FROM
				ap_viaticosdistribucion do
				INNER JOIN pv_partida p ON (p.cod_partida = do.cod_partida)
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
				do.CodOrganismo = '".$CodOrganismo."' AND
				do.CodViatico = '".$CodViatico."'
			ORDER BY CodOrganismo, CodPresupuesto, CodFuente, cod_partida";
	$field_partidas = getRecords($sql);
	foreach($field_partidas as $f) {
            if ($Grupo != $f['CatProg']) {
                $Grupo = $f['CatProg'];
                ?>
                <tr class="trListaBody2">
                    <td colspan="3">
                        <?=$f['CatProg']?> - <?=$f['UnidadEjecutora']?>
                    </td>
                </tr>
                <?php
            }
            list($MontoAjustado, $MontoCompromiso, $PreCompromiso, $CotizacionesAsignadas) = disponibilidadPartida2($f['Ejercicio'], $f['CodOrganismo'], $f['cod_partida'], $f['CodPresupuesto'], $f['CodFuente']);
            if ($opcion == 'nuevo') {
                $PreCompromiso -= $f['Monto'];
            }
            else {
                $MontoCompromiso -= $f['Monto'];
            }
            $MontoPendiente = $PreCompromiso + $CotizacionesAsignadas;
            $MontoDisponible = $MontoAjustado - $MontoCompromiso;
            $MontoDisponibleReal = $MontoAjustado - ($MontoCompromiso + $MontoPendiente);
            ##  valido
            if (($MontoDisponible - $f['Monto']) <= 0) $style = "style='background-color:#F8637D;'";
            elseif(($MontoDisponibleReal - $f['Monto']) <= 0) $style = "style='background-color:#FFC;'";
            else $style = "style='background-color:#D0FDD2;'";
            ?>
            <tr class="trListaBody" <?=$style?>>
                <td align="center"><?=$f['CodFuente']?></td>
                <td align="center">
                    <input type="hidden" name="cod_partida" value="<?=$f['cod_partida']?>" />
                    <input type="hidden" name="CodCuenta" value="<?=$f['CodCuenta']?>" />
                    <input type="hidden" name="CodCuentaPub20" value="<?=$f['CodCuentaPub20']?>" />
                    <input type="hidden" name="Monto" value="<?=$f['Monto']?>" />
                    <input type="hidden" name="MontoAjustado" value="<?=$MontoAjustado?>" />
                    <input type="hidden" name="MontoCompromiso" value="<?=$MontoCompromiso?>" />
                    <input type="hidden" name="PreCompromiso" value="<?=$PreCompromiso?>" />
                    <input type="hidden" name="CotizacionesAsignadas" value="<?=$CotizacionesAsignadas?>" />
                    <input type="hidden" name="MontoDisponible" value="<?=$MontoDisponible?>" />
                    <input type="hidden" name="MontoDisponibleReal" value="<?=$MontoDisponibleReal?>" />
                    <input type="hidden" name="MontoPendiente" value="<?=$MontoPendiente?>" />
                    <input type="hidden" name="partidasCodFuente" value="<?=$f['CodFuente']?>" />
                    <input type="hidden" name="partidasCategoriaProg" value="<?=$f['CategoriaProg']?>" />
                    <?=$f['cod_partida']?>
                </td>
                <td>
                    <?=htmlentities($f['denominacion'])?>
                </td>
                <td align="right">
                    <?=number_format($f['Monto'], 2, ',', '.')?>
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