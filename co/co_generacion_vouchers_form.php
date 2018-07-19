<?php
if ($opcion == "documento_pub20")
{
	##	Centro Costo
	$sql = "SELECT * FROM ac_mastcentrocosto WHERE Codigo = '$_PARAMETRO[CCOSTOVOUCHER]'";
	$field_cc = getRecord($sql);
	##	consulto datos generales
	$sql = "SELECT
				do.CodDocumento,
				do.CodOrganismo,
				do.Comentarios AS ComentariosVoucher,
				do.FechaDocumento AS FechaVoucher,
				SUBSTRING(do.FechaDocumento, 1, 7) AS Periodo,
				do.CodPersonaCliente AS CodPersona,
				do.CodTipoDocumento,
				do.NroDocumento
			FROM co_documento do
			WHERE do.CodDocumento = '$sel_documentos'";
	$field = getRecord($sql);
	$field['CodVoucher'] = '08';
	$field['CodLibroCont'] = 'CO';
	$field['CodContabilidad'] = 'F';
	$field['PreparadoPor'] = $_SESSION["CODPERSONA_ACTUAL"];
	$field['NomPreparadoPor'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
	$field['FechaPreparacion'] = $FechaActual;
	$field['AprobadoPor'] = $_SESSION["CODPERSONA_ACTUAL"];
	$field['NomAprobadoPor'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
	$field['FechaAprobacion'] = $FechaActual;
	$field['CodDependencia'] = $field_cc['CodDependencia'];
	$field['CodSistemaFuente'] = '';
	##	detalle
	$sql = "(SELECT
				td.CodCuentaPub20 AS CodCuenta,
				pc.Descripcion,
				SUM(do.MontoTotal) AS MontoVoucher,
				(CASE WHEN pc.FlagReqCC = 'S' THEN '$field_cc[CodCentroCosto]' ELSE '' END) AS CodCentroCosto,
				'$field[CodPersona]' AS CodPersona,
				'$field[FechaVoucher]' AS FechaVoucher,
				'$field[CodTipoDocumento]' AS ReferenciaTipoDocumento,
				'$field[NroDocumento]' AS ReferenciaNroDocumento,
				'' AS NroCheque,
				'' AS NroPagoVoucher,
				'Debe' AS Columna,
				'01' AS Orden
			 FROM co_documento do
			 INNER JOIN co_tipodocumento td ON td.CodTipoDocumento = do.CodTipoDocumento
			 INNER JOIN ac_mastplancuenta20 pc ON pc.CodCuenta = td.CodCuentaPub20
			 WHERE do.CodDocumento = '$field[CodDocumento]'
			 GROUP BY CodCuenta)

			UNION
			
			(SELECT
				(CASE WHEN dod.TipoDetalle = 'I' THEN i.CtaVentaPub20 ELSE s.CodCuentaPub20 END) AS CodCuenta,
				(CASE WHEN dod.TipoDetalle = 'I' THEN pc1.Descripcion ELSE pc2.Descripcion END) AS Descripcion,
				SUM(dod.MontoTotalFinal) AS MontoVoucher,
				(CASE WHEN pc1.FlagReqCC = 'S' OR pc2.FlagReqCC = 'S' THEN '$field_cc[CodCentroCosto]' ELSE '' END) AS CodCentroCosto,
				'$field[CodPersona]' AS CodPersona,
				'$field[FechaVoucher]' AS FechaVoucher,
				'$field[CodTipoDocumento]' AS ReferenciaTipoDocumento,
				'$field[NroDocumento]' AS ReferenciaNroDocumento,
				'' AS NroCheque,
				'' AS NroPagoVoucher,
				'Haber' AS Columna,
				'02' AS Orden
			 FROM co_documentodet dod
			 LEFT JOIN lg_itemmast i ON (
			 	i.CodItem = dod.CodItem
			 	AND dod.TipoDetalle = 'I'
			 )
			 LEFT JOIN co_mastservicios s ON (
			 	s.CodServicio = dod.CodItem
			 	AND dod.TipoDetalle = 'S'
			 )
			 LEFT JOIN ac_mastplancuenta20 pc1 ON pc1.CodCuenta = i.CtaVentaPub20
			 LEFT JOIN ac_mastplancuenta20 pc2 ON pc2.CodCuenta = s.CodCuentaPub20
			 WHERE dod.CodDocumento = '$field[CodDocumento]'
			 GROUP BY CodCuenta)

			UNION

			(SELECT
				i.CodCuentaPub20 AS CodCuenta,
				pc.Descripcion,
				SUM(doi.Monto) AS MontoVoucher,
				(CASE WHEN pc.FlagReqCC = 'S' THEN '$field_cc[CodCentroCosto]' ELSE '' END) AS CodCentroCosto,
				'$field[CodPersona]' AS CodPersona,
				'$field[FechaVoucher]' AS FechaVoucher,
				'$field[CodTipoDocumento]' AS ReferenciaTipoDocumento,
				'$field[NroDocumento]' AS ReferenciaNroDocumento,
				'' AS NroCheque,
				'' AS NroPagoVoucher,
				'Haber' AS Columna,
				'03' AS Orden
			 FROM co_documentoimpuesto doi
			 INNER JOIN mastimpuestos i ON i.CodImpuesto = doi.CodImpuesto
			 INNER JOIN ac_mastplancuenta20 pc ON pc.CodCuenta = i.CodCuentaPub20
			 WHERE doi.CodDocumento = '$field[CodDocumento]'
			 GROUP BY CodCuenta)

			ORDER BY Orden, CodCuenta";
	$field_detalle = getRecords($sql);
	##
	$_titulo = "Voucher Contable Pub. 20";
	$accion = "documento_pub20";
}
elseif ($opcion == "documento_oncop")
{
	##	Centro Costo
	$sql = "SELECT * FROM ac_mastcentrocosto WHERE Codigo = '$_PARAMETRO[CCOSTOVOUCHER]'";
	$field_cc = getRecord($sql);
	##	consulto datos generales
	$sql = "SELECT
				do.CodDocumento,
				do.CodOrganismo,
				do.Comentarios AS ComentariosVoucher,
				do.FechaDocumento AS FechaVoucher,
				SUBSTRING(do.FechaDocumento, 1, 7) AS Periodo,
				do.CodPersonaCliente AS CodPersona,
				do.CodTipoDocumento,
				do.NroDocumento
			FROM co_documento do
			WHERE do.CodDocumento = '$sel_documentos'";
	$field = getRecord($sql);
	$field['CodVoucher'] = '08';
	$field['CodLibroCont'] = 'CO';
	$field['CodContabilidad'] = 'F';
	$field['PreparadoPor'] = $_SESSION["CODPERSONA_ACTUAL"];
	$field['NomPreparadoPor'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
	$field['FechaPreparacion'] = $FechaActual;
	$field['AprobadoPor'] = $_SESSION["CODPERSONA_ACTUAL"];
	$field['NomAprobadoPor'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
	$field['FechaAprobacion'] = $FechaActual;
	$field['CodDependencia'] = $field_cc['CodDependencia'];
	$field['CodSistemaFuente'] = '';
	##	detalle
	$sql = "(SELECT
				td.CodCuentaOncop AS CodCuenta,
				pc.Descripcion,
				SUM(do.MontoTotal) AS MontoVoucher,
				(CASE WHEN pc.FlagReqCC = 'S' THEN '$field_cc[CodCentroCosto]' ELSE '' END) AS CodCentroCosto,
				'$field[CodPersona]' AS CodPersona,
				'$field[FechaVoucher]' AS FechaVoucher,
				'$field[CodTipoDocumento]' AS ReferenciaTipoDocumento,
				'$field[NroDocumento]' AS ReferenciaNroDocumento,
				'' AS NroCheque,
				'' AS NroPagoVoucher,
				'Debe' AS Columna,
				'01' AS Orden
			 FROM co_documento do
			 INNER JOIN co_tipodocumento td ON td.CodTipoDocumento = do.CodTipoDocumento
			 INNER JOIN ac_mastplancuenta pc ON pc.CodCuenta = td.CodCuentaOncop
			 WHERE do.CodDocumento = '$field[CodDocumento]'
			 GROUP BY CodCuenta)

			UNION
			
			(SELECT
				(CASE WHEN dod.TipoDetalle = 'I' THEN i.CtaVenta ELSE s.CodCuentaOncop END) AS CodCuenta,
				(CASE WHEN dod.TipoDetalle = 'I' THEN pc1.Descripcion ELSE pc2.Descripcion END) AS Descripcion,
				SUM(dod.MontoTotalFinal) AS MontoVoucher,
				(CASE WHEN pc1.FlagReqCC = 'S' OR pc2.FlagReqCC = 'S' THEN '$field_cc[CodCentroCosto]' ELSE '' END) AS CodCentroCosto,
				'$field[CodPersona]' AS CodPersona,
				'$field[FechaVoucher]' AS FechaVoucher,
				'$field[CodTipoDocumento]' AS ReferenciaTipoDocumento,
				'$field[NroDocumento]' AS ReferenciaNroDocumento,
				'' AS NroCheque,
				'' AS NroPagoVoucher,
				'Haber' AS Columna,
				'02' AS Orden
			 FROM co_documentodet dod
			 LEFT JOIN lg_itemmast i ON (
			 	i.CodItem = dod.CodItem
			 	AND dod.TipoDetalle = 'I'
			 )
			 LEFT JOIN co_mastservicios s ON (
			 	s.CodServicio = dod.CodItem
			 	AND dod.TipoDetalle = 'S'
			 )
			 LEFT JOIN ac_mastplancuenta pc1 ON pc1.CodCuenta = i.CtaVenta
			 LEFT JOIN ac_mastplancuenta pc2 ON pc2.CodCuenta = s.CodCuentaOncop
			 WHERE dod.CodDocumento = '$field[CodDocumento]'
			 GROUP BY CodCuenta)

			UNION

			(SELECT
				i.CodCuenta AS CodCuenta,
				pc.Descripcion,
				SUM(doi.Monto) AS MontoVoucher,
				(CASE WHEN pc.FlagReqCC = 'S' THEN '$field_cc[CodCentroCosto]' ELSE '' END) AS CodCentroCosto,
				'$field[CodPersona]' AS CodPersona,
				'$field[FechaVoucher]' AS FechaVoucher,
				'$field[CodTipoDocumento]' AS ReferenciaTipoDocumento,
				'$field[NroDocumento]' AS ReferenciaNroDocumento,
				'' AS NroCheque,
				'' AS NroPagoVoucher,
				'Haber' AS Columna,
				'03' AS Orden
			 FROM co_documentoimpuesto doi
			 INNER JOIN mastimpuestos i ON i.CodImpuesto = doi.CodImpuesto
			 INNER JOIN ac_mastplancuenta pc ON pc.CodCuenta = i.CodCuenta
			 WHERE doi.CodDocumento = '$field[CodDocumento]'
			 GROUP BY CodCuenta)

			ORDER BY Orden, CodCuenta";
	$field_detalle = getRecords($sql);
	##
	$_titulo = "Voucher Contable ONCOP";
	$accion = "documento_oncop";
}
elseif ($opcion == "cobranza_pub20")
{
	##	Centro Costo
	$sql = "SELECT * FROM ac_mastcentrocosto WHERE Codigo = '$_PARAMETRO[CCOSTOVOUCHER]'";
	$field_cc = getRecord($sql);
	##	consulto datos generales
	$sql = "SELECT
				ac.CodArqueo,
				ac.CodOrganismo,
				CONCAT_WS(' ', 'Arqueo de Caja ', ac.NroArqueo) AS ComentariosVoucher,
				ac.Fecha AS FechaVoucher,
				SUBSTRING(ac.Fecha, 1, 7) AS Periodo,
				ac.NroArqueo
			FROM co_arqueocaja ac
			WHERE ac.CodArqueo = '$sel_cobranzas'";
	$field = getRecord($sql);
	$field['CodVoucher'] = '09';
	$field['CodLibroCont'] = 'CO';
	$field['CodContabilidad'] = 'F';
	$field['PreparadoPor'] = $_SESSION["CODPERSONA_ACTUAL"];
	$field['NomPreparadoPor'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
	$field['FechaPreparacion'] = $FechaActual;
	$field['AprobadoPor'] = $_SESSION["CODPERSONA_ACTUAL"];
	$field['NomAprobadoPor'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
	$field['FechaAprobacion'] = $FechaActual;
	$field['CodDependencia'] = $field_cc['CodDependencia'];
	$field['CodSistemaFuente'] = '';
	##	detalle
	$sql = "(SELECT
				cb.CodCuentaPub20 AS CodCuenta,
				pc.Descripcion,
				SUM(cod.MontoLocal) AS MontoVoucher,
				(CASE WHEN pc.FlagReqCC = 'S' THEN '$field_cc[CodCentroCosto]' ELSE '' END) AS CodCentroCosto,
				'' AS CodPersona,
				'$field[FechaVoucher]' AS FechaVoucher,
				'' AS ReferenciaTipoDocumento,
				'$field[NroArqueo]' AS ReferenciaNroDocumento,
				'' AS NroCheque,
				'' AS NroPagoVoucher,
				'Debe' AS Columna,
				'01' AS Orden
			 FROM co_arqueocaja ac
			 INNER JOIN co_cobranzadet cod ON cod.CodArqueo = ac.CodArqueo
			 INNER JOIN ap_ctabancaria cb ON cb.Nrocuenta = ac.NroCuenta
			 INNER JOIN ac_mastplancuenta20 pc ON pc.CodCuenta = cb.CodCuentaPub20
			 WHERE ac.CodArqueo = '$field[CodArqueo]'
			 GROUP BY CodCuenta)

			UNION
			
			(SELECT
				'' AS CodCuenta,
				'' AS Descripcion,
				'0.00' AS MontoVoucher,
				(CASE WHEN pc.FlagReqCC = 'S' THEN '$field_cc[CodCentroCosto]' ELSE '' END) AS CodCentroCosto,
				'' AS CodPersona,
				'$field[FechaVoucher]' AS FechaVoucher,
				'' AS ReferenciaTipoDocumento,
				'$field[NroArqueo]' AS ReferenciaNroDocumento,
				'' AS NroCheque,
				'' AS NroPagoVoucher,
				'Haber' AS Columna,
				'02' AS Orden
			 FROM co_arqueocaja ac
			 INNER JOIN co_cobranzadet cod ON cod.CodArqueo = ac.CodArqueo
			 INNER JOIN ap_ctabancaria cb ON cb.Nrocuenta = ac.NroCuenta
			 INNER JOIN ac_mastplancuenta20 pc ON pc.CodCuenta = cb.CodCuentaPub20
			 WHERE ac.CodArqueo = '$field[CodArqueo]'
			 GROUP BY CodCuenta)

			ORDER BY Orden, CodCuenta";
	$field_detalle = getRecords($sql);
	##
	$_titulo = "Voucher Contable Pub. 20";
	$accion = "cobranza_pub20";
}
elseif ($opcion == "cobranza_oncop")
{
	##	Centro Costo
	$sql = "SELECT * FROM ac_mastcentrocosto WHERE Codigo = '$_PARAMETRO[CCOSTOVOUCHER]'";
	$field_cc = getRecord($sql);
	##	consulto datos generales
	$sql = "SELECT
				ac.CodArqueo,
				ac.CodOrganismo,
				CONCAT_WS(' ', 'Arqueo de Caja ', ac.NroArqueo) AS ComentariosVoucher,
				ac.Fecha AS FechaVoucher,
				SUBSTRING(ac.Fecha, 1, 7) AS Periodo,
				ac.NroArqueo
			FROM co_arqueocaja ac
			WHERE ac.CodArqueo = '$sel_cobranzas'";
	$field = getRecord($sql);
	$field['CodVoucher'] = '09';
	$field['CodLibroCont'] = 'CO';
	$field['CodContabilidad'] = 'F';
	$field['PreparadoPor'] = $_SESSION["CODPERSONA_ACTUAL"];
	$field['NomPreparadoPor'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
	$field['FechaPreparacion'] = $FechaActual;
	$field['AprobadoPor'] = $_SESSION["CODPERSONA_ACTUAL"];
	$field['NomAprobadoPor'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
	$field['FechaAprobacion'] = $FechaActual;
	$field['CodDependencia'] = $field_cc['CodDependencia'];
	$field['CodSistemaFuente'] = '';
	##	detalle
	$sql = "(SELECT
				cb.CodCuenta AS CodCuenta,
				pc.Descripcion,
				SUM(cod.MontoLocal) AS MontoVoucher,
				(CASE WHEN pc.FlagReqCC = 'S' THEN '$field_cc[CodCentroCosto]' ELSE '' END) AS CodCentroCosto,
				'' AS CodPersona,
				'$field[FechaVoucher]' AS FechaVoucher,
				'' AS ReferenciaTipoDocumento,
				'$field[NroArqueo]' AS ReferenciaNroDocumento,
				'' AS NroCheque,
				'' AS NroPagoVoucher,
				'Debe' AS Columna,
				'01' AS Orden
			 FROM co_arqueocaja ac
			 INNER JOIN co_cobranzadet cod ON cod.CodArqueo = ac.CodArqueo
			 INNER JOIN ap_ctabancaria cb ON cb.Nrocuenta = ac.NroCuenta
			 INNER JOIN ac_mastplancuenta pc ON pc.CodCuenta = cb.CodCuenta
			 WHERE ac.CodArqueo = '$field[CodArqueo]'
			 GROUP BY CodCuenta)

			UNION
			
			(SELECT
				'' AS CodCuenta,
				'' AS Descripcion,
				'0.00' AS MontoVoucher,
				(CASE WHEN pc.FlagReqCC = 'S' THEN '$field_cc[CodCentroCosto]' ELSE '' END) AS CodCentroCosto,
				'' AS CodPersona,
				'$field[FechaVoucher]' AS FechaVoucher,
				'' AS ReferenciaTipoDocumento,
				'$field[NroArqueo]' AS ReferenciaNroDocumento,
				'' AS NroCheque,
				'' AS NroPagoVoucher,
				'Haber' AS Columna,
				'02' AS Orden
			 FROM co_arqueocaja ac
			 INNER JOIN co_cobranzadet cod ON cod.CodArqueo = ac.CodArqueo
			 INNER JOIN ap_ctabancaria cb ON cb.Nrocuenta = ac.NroCuenta
			 INNER JOIN ac_mastplancuenta pc ON pc.CodCuenta = cb.CodCuenta
			 WHERE ac.CodArqueo = '$field[CodArqueo]'
			 GROUP BY CodCuenta)

			ORDER BY Orden, CodCuenta";
	$field_detalle = getRecords($sql);
	##
	$_titulo = "Voucher Contable ONCOP";
	$accion = "cobranza_oncop";
}
##	consulto si el periodo esta abierto
$sql = "SELECT Estado
		FROM ac_controlcierremensual
		WHERE
			TipoRegistro = 'AB' AND
			CodOrganismo = '$field[CodOrganismo]' AND
			Periodo = '$field[Periodo]'";
$PeriodoEstado = getVar3($sql);
##	
$clkCancelar = "document.getElementById('frmentrada').submit();";
//	------------------------------------
$_width = 500;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=co_generacion_vouchers_lista" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('co_generacion_vouchers_ajax', 'modulo=generar-vouchers&accion=<?=$accion?>');" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
	<input type="hidden" name="fPeriodo" id="fPeriodo" value="<?=$fPeriodo?>" />
	<input type="hidden" name="fFechaDocumentoD" id="fFechaDocumentoD" value="<?=$fFechaDocumentoD?>" />
	<input type="hidden" name="fFechaDocumentoH" id="fFechaDocumentoH" value="<?=$fFechaDocumentoH?>" />
	<input type="hidden" name="fCodTipoDocumento" id="fCodTipoDocumento" value="<?=$fCodTipoDocumento?>" />
	<input type="hidden" name="fNroSerie" id="fNroSerie" value="<?=$fNroSerie?>" />
	<input type="hidden" name="CodDependencia" id="CodDependencia" value="<?=$field['CodDependencia']?>" />
	<input type="hidden" name="CodSistemaFuente" id="CodSistemaFuente" value="<?=$field['CodSistemaFuente']?>" />
	<input type="hidden" name="PeriodoEstado" id="PeriodoEstado" value="<?=$PeriodoEstado?>" />
	<input type="hidden" name="CodDocumento" id="CodDocumento" value="<?=$field['CodDocumento']?>" />
	<input type="hidden" name="CodArqueo" id="CodArqueo" value="<?=$field['CodArqueo']?>" />
	
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
	                        <select name="CodOrganismo" id="CodOrganismo" style="width:300px;">
	                            <?=loadSelect("mastorganismos","CodOrganismo","Organismo",$field['CodOrganismo'])?>
	                        </select>
	                    </td>
	                    <td class="tagForm">Descripción:</td>
	                    <td><input type="text" name="ComentariosVoucher" id="ComentariosVoucher" style="width:300px;" value="<?=htmlentities($field['ComentariosVoucher'])?>" /></td>
	                </tr>
	                <tr>
	                    <td class="tagForm">* Fecha:</td>
	                    <td><input type="text" name="FechaVoucher" id="FechaVoucher" value="<?=formatFechaDMA($field['FechaVoucher'])?>" style="width:96px;" class="datepicker" onchange="setPeriodoFromFecha(this.value, $('#Periodo'));" /></td>
	                    <td class="tagForm">Preparado Por:</td>
	                    <td>
	                        <input type="hidden" name="PreparadoPor" id="PreparadoPor" value="<?=$field['PreparadoPor']?>" />
	                        <input type="text" style="width:235px;" value="<?=htmlentities($field['NomPreparadoPor'])?>" disabled />
	                        <input type="text" name="FechaPreparacion" id="FechaPreparacion" style="width:60px;" value="<?=formatFechaDMA($field['FechaPreparacion'])?>" readonly />
	                    </td>
	                </tr>
	                <tr>
	                    <td class="tagForm">Voucher:</td>
	                    <td>
							<input type="text" name="Periodo" id="Periodo" value="<?=$field['Periodo']?>" style="width:50px;" readonly />
	                        <select name="CodVoucher" id="CodVoucher"  style="width: 42px;">
	                            <?=loadSelect("ac_voucher","CodVoucher","Descripcion",$field['CodVoucher'],11)?>
	                        </select>
	                        <input type="text" name="NroVoucher" id="NroVoucher" style="width:50px;" disabled="disabled" />
	                    </td>
	                    <td class="tagForm">Aprobado Por:</td>
	                    <td>
	                        <input type="hidden" name="AprobadoPor" id="AprobadoPor" value="<?=$field['AprobadoPor']?>" />
	                        <input type="text" style="width:235px;" value="<?=htmlentities($field['NomAprobadoPor'])?>" disabled />
	                        <input type="text" name="FechaAprobacion" id="FechaAprobacion" style="width:60px;" value="<?=formatFechaDMA($field['FechaAprobacion'])?>" readonly />
	                    </td>
	                </tr>
	                <tr>
	                    <td class="tagForm">* Libro Contable:</td>
	                    <td>
	                        <select name="CodLibroCont" id="CodLibroCont" style="width:150px;">
	                            <?=loadSelect("ac_librocontable","CodLibroCont","Descripcion","")?>
	                        </select>
	                    </td>
	                    <td class="tagForm">* Contabilidad:</td>
	                    <td>
	                        <select name="CodContabilidad" id="CodContabilidad" style="width:150px;">
	                            <?=loadSelect("ac_contabilidades","CodContabilidad","Descripcion","F",1)?>
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
	                        <input type="submit" value="Aceptar" id="btAceptar" style="width:75px;" />
							<input type="button" value="Rechazar" id="btCancelar" style="width:75px;" onclick="<?=$clkCancelar?>" />
	                    </td>

	                </tr>
	            </table>
	            
	            <table><tr><td><div style="overflow:scroll; width:960px; height:175px;">
		            <table width="1100" class="tblLista">
		            	<thead>
			                <tr>
			                    <th width="30">#</th>
			                    <th width="100">Cuenta</th>
			                    <th>Descripci&oacute;n</th>
			                    <th width="125">Monto</th>
			                    <th width="60">Persona</th>
			                    <th colspan="2">Documento</th>
			                    <th width="45">C.C</th>
			                    <th width="75">Fecha</th>
			                </tr>
		                </thead>
		                
		                <tbody>
			                <?php
			                foreach ($field_detalle as $f)
			                {
								$MontoVoucher = $f['MontoVoucher'];
								if ($f['Columna'] == "Haber") {
									$style = " color:red;";
									$MontoVoucher = abs($MontoVoucher) * (-1);
									$Debitos += $MontoVoucher;
								} else {
									$style = "";
									$MontoVoucher = abs($MontoVoucher);
									$Creditos += $MontoVoucher;
								}
								?>
								<tr class="trListaBody">
			                    	<th>
			                    		<input type="hidden" name="detalle_Linea[]" value="0">
			                    		<input type="hidden" name="detalle_NroCheque[]" value="<?=$f['NroCheque']?>">
			                    		<input type="hidden" name="detalle_NroPagoVoucher[]" value="<?=$f['NroPagoVoucher']?>">
			                    		<?=++$Linea?>
			                        </th>
			                    	<td>
			                        	<input type="text" name="detalle_CodCuenta[]" value="<?=$f['CodCuenta']?>" class="cell2" readonly />
			                        </td>
			                    	<td>
			                        	<input type="text" name="detalle_Descripcion[]" value="<?=htmlentities($f['Descripcion'])?>" class="cell2" readonly />
			                        </td>
			                    	<td>
			                        	<input type="text" name="detalle_MontoVoucher[]" value="<?=number_format($MontoVoucher, 2, ',', '.')?>" class="cell2" style="text-align:right; <?=$style?>" readonly />
			                        </td>
			                    	<td>
			                        	<input type="text" name="detalle_CodPersona[]" value="<?=$f['CodPersona']?>" class="cell2" style="text-align:center;" readonly />
			                        </td>
			                    	<td width="25">
			                        	<input type="text" name="detalle_ReferenciaTipoDocumento[]" value="<?=$f['ReferenciaTipoDocumento']?>" class="cell2" readonly />
			                        </td>
			                    	<td width="125">
			                        	<input type="text" name="detalle_ReferenciaNroDocumento[]" value="<?=$f['ReferenciaNroDocumento']?>" class="cell2" readonly />
			                        </td>
			                    	<td>
			                        	<input type="text" name="detalle_CodCentroCosto[]" value="<?=$f['CodCentroCosto']?>" class="cell2" style="text-align:center;" readonly />
			                        </td>
			                    	<td>
			                        	<input type="text" name="detalle_FechaVoucher[]" value="<?=formatFechaDMA($f['FechaVoucher'])?>" class="cell2" style="width:75px; text-align:center;" readonly />
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
	                    <th width="140">Nro Lineas: <input type="text" name="Lineas" id="Lineas" value="<?=$Linea?>" class="cell2" style="text-align:center; font-weight:bold; font-size:12px; width:20px;" readonly /></th>
	                    <th width="75">&nbsp;</th>
	                    <th width="150">&nbsp;</th>
	                    <th width="75">Total:</th>
	                    <th width="125">
	                    	<input type="text" name="Creditos" id="Creditos" value="<?=number_format($Creditos, 2, ',', '.')?>" class="cell2" style="text-align:right; font-weight:bold; font-size:12px;" readonly />
	                    </th>
	                    <th width="125">
	                    	<input type="text" name="Debitos" id="Debitos" value="<?=number_format($Debitos, 2, ',', '.')?>" class="cell2" style="text-align:right; font-weight:bold; font-size:12px; color:red;" readonly />
						</th>
	                    <th width="125">&nbsp;</th>
	                </tr>
				</table>
	        </td>
	    </tr>
	</table>
</form>
<div style="width:100%; min-width:<?=$_width?>px;" class="divMsj">Campos Obligatorios *</div>

<script type="text/javascript">
    $(document).ready(function() {
        validarErroresVoucher();
    });
</script>