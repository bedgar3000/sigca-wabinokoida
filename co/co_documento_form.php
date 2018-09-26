<?php
$igv = getVar3("SELECT FactorPorcentaje FROM mastimpuestos WHERE CodImpuesto = '$_PARAMETRO[COIVA]'");
$igvp = $igv / 100 + 1;
if ($opcion == "nuevo") {
	$field_fp = getRecord("SELECT * FROM mastformapago LIMIT 0, 1");
	##	
	$field['Estado'] = 'PE';
	$field['CodCentroCosto'] = $_PARAMETRO['COCCOSTO'];
	$field['CodAlmacen'] = $_PARAMETRO['COVTAALMACEN'];
	$field['CodTipoDocumento'] = $_PARAMETRO['COTIPODOC'];
	$field['TipoDocumento'] = getVar3("SELECT Descripcion FROM co_tipodocumento WHERE CodTipoDocumento = '$field[CodTipoDocumento]'");
	$field['CentroCosto'] = getVar3("SELECT Codigo FROM ac_mastcentrocosto WHERE CodCentroCosto = '$_PARAMETRO[COCCOSTO]'");
	$field['CodFormaPago'] = $field_fp['CodFormaPago'];
	$field['FlagCredito'] = $field_fp['FlagCredito'];
	$field['FechaDocumento'] = $FechaActual;
	$field['FechaVencimiento'] = formatFechaAMD(fechaFin(formatFechaDMA($FechaActual), $field_fp['DiasVence']));
	$field['PreparadoPor'] = $_SESSION["CODPERSONA_ACTUAL"];
	$field['NomPreparadoPor'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
	$field['FechaPreparado'] = $Ahora;
	$field['CodOrganismo'] = $_SESSION["ORGANISMO_ACTUAL"];
	$sql = "SELECT CodEstablecimiento
			FROM co_establecimientofiscal
			WHERE CodOrganismo = '$field[CodOrganismo]' LIMIT 1";
	$field['CodEstablecimiento'] = getVar3($sql);
	$sql = "SELECT *
			FROM co_seriefiscal
			WHERE
				CodOrganismo = '$field[CodOrganismo]'
				AND CodEstablecimiento = '$field[CodEstablecimiento]'
			LIMIT 1";
	$field_establecimiento = getRecord($sql);
	$field['CodSerie'] = $field_establecimiento['CodSerie'];
	$field['NroSerie'] = $field_establecimiento['NroSerie'];
	list($NroDocumento, $UltNroEmitido) = correlativo_documento($field['CodOrganismo'], $field['CodTipoDocumento'], $field['NroSerie']);
	$field['NroDocumento'] = $NroDocumento;
	$field_detalle = [];
	$field_cobranza = [];
	##
	$_titulo = "Nueva Venta en Tienda";
	$accion = "nuevo";
	$disabled_modificar = "";
	$disabled_ver = "";
	$disabled_descuento = "";
	$disabled_item = "";
	$disabled_servicio = "";
	$disabled_opciones = "";
	$disabled_adelanto = "";
	$read_modificar = "";
	$read_generar = "";
	$display_modificar = "";
	$display_ver = "";
	$display_facturar = "display:none;";
	$display_submit = "";
	$change_generar = 'setMontos();';
	$check_generar = "";
	$label_submit = "Guardar";
	$focus = "CodPersona";
}
elseif ($opcion == "nuevo-adelanto") {
	$field['Estado'] = 'PE';
	$field['CodCentroCosto'] = $_PARAMETRO['COCCOSTO'];
	$field['CodAlmacen'] = $_PARAMETRO['COVTAALMACEN'];
	$field['CodTipoDocumento'] = $_PARAMETRO['CODOCADE'];
	$field['TipoDocumento'] = getVar3("SELECT Descripcion FROM co_tipodocumento WHERE CodTipoDocumento = '$field[CodTipoDocumento]'");
	$field['CentroCosto'] = getVar3("SELECT Codigo FROM ac_mastcentrocosto WHERE CodCentroCosto = '$_PARAMETRO[COCCOSTO]'");
	$field['FechaDocumento'] = $FechaActual;
	$field['FechaVencimiento'] = addDias($FechaActual,1,'month');
	$field['PreparadoPor'] = $_SESSION["CODPERSONA_ACTUAL"];
	$field['NomPreparadoPor'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
	$field['FechaPreparado'] = $Ahora;
	$field['CodOrganismo'] = $_SESSION["ORGANISMO_ACTUAL"];
	$sql = "SELECT CodEstablecimiento
			FROM co_establecimientofiscal
			WHERE CodOrganismo = '$field[CodOrganismo]' LIMIT 1";
	$field['CodEstablecimiento'] = getVar3($sql);
	$sql = "SELECT sf.*
			FROM co_fiscalautorizaciondet fad
			INNER JOIN co_seriefiscal sf ON sf.CodSerie = fad.CodSerie
			WHERE
				fad.CodOrganismo = '$field[CodOrganismo]'
				AND fad.CodTipoDocumento = '$field[CodTipoDocumento]'
			LIMIT 1";
	$field_establecimiento = getRecord($sql);
	$field['CodSerie'] = $field_establecimiento['CodSerie'];
	$field['NroSerie'] = $field_establecimiento['NroSerie'];
	$field_detalle = [];
	$field_cobranza = [];
	##
	$_titulo = "Nuevo Documento";
	$accion = "nuevo-adelanto";
	$disabled_modificar = "";
	$disabled_ver = "";
	$disabled_descuento = "";
	$disabled_item = "";
	$disabled_servicio = "";
	$disabled_opciones = "";
	$disabled_adelanto = "disabled";
	$read_modificar = "";
	$read_generar = "";
	$display_modificar = "";
	$display_ver = "";
	$display_facturar = "display:none;";
	$display_submit = "";
	$change_generar = 'setMontos();';
	$check_generar = "";
	$label_submit = "Guardar";
	$focus = "CodPersona";
}
elseif ($opcion == "modificar" || $opcion == "ver" || $opcion == "ver-modal") {
	##	consulto datos generales
	$sql = "SELECT
				do.*,
				td.Descripcion AS TipoDocumento,
				sf.CodSerie,
				sf.NroSerie,
				md1.Descripcion AS NomFormaFactura,
				md2.Descripcion AS NomTipoVenta,
				cc.Codigo AS CentroCosto,
				p1.NomCompleto AS NombreCliente,
				p1.Direccion AS DireccionCliente,
				p1.Telefono1 As TelefonoCliente,
				p2.NomCompleto AS NomPreparadoPor,
				p3.NomCompleto AS NomAprobadoPor,
				p4.NomCompleto AS NombreCobrar,
				p4.DocFiscal AS DocFiscalCobrar,
				rd.Descripcion AS RutaDespacho,
				rd.CodParroquia,
				cm.Descripcion AS Parroquia,
				do2.NroDocumento AS NroPedido,
				do3.NroDocumento AS NroOriginal
			FROM co_documento do
			INNER JOIN ac_mastcentrocosto cc ON cc.CodCentroCosto = do.CodCentroCosto
			INNER JOIN co_tipodocumento td ON td.CodTipoDocumento = do.CodTipoDocumento
			INNER JOIN co_establecimientofiscal ef ON ef.CodOrganismo = do.CodOrganismo
			INNER JOIN co_seriefiscal sf ON (
				sf.CodOrganismo = ef.CodOrganismo
				AND sf.CodEstablecimiento = ef.CodEstablecimiento
			)
			INNER JOIN mastpersonas p1 ON p1.CodPersona = do.CodPersonaCliente
			LEFT JOIN mastpersonas p2 ON p2.CodPersona = do.PreparadoPor
			LEFT JOIN mastpersonas p3 ON p3.CodPersona = do.AprobadoPor
			LEFT JOIN mastpersonas p4 ON p4.CodPersona = do.CodPersonaCobrar
			LEFT JOIN mastmiscelaneosdet md1 ON (
				md1.CodDetalle = do.FormaFactura
				AND md1.CodMaestro = 'FORMAFACT'
			)
			LEFT JOIN mastmiscelaneosdet md2 ON (
				md2.CodDetalle = do.TipoVenta
				AND md2.CodMaestro = 'TIPOVENTA'
			)
			LEFT JOIN co_rutadespacho rd ON rd.CodRutaDespacho = do.CodRutaDespacho
			LEFT JOIN mastparroquias cm ON cm.CodParroquia = rd.CodParroquia
			LEFT JOIN co_documento do2 ON do2.CodDocumento = do.CodPedido
			LEFT JOIN co_documento do3 ON do3.CodDocumento = do.DocOriginal
			WHERE do.CodDocumento = '$sel_registros'";
	$field = getRecord($sql);
	##	
	$sql = "SELECT
				dod.*,
				(CASE WHEN dod.TipoDetalle = 'I' THEN i.CodInterno ELSE s.CodInterno END) AS CodInterno,
				(CASE WHEN dod.TipoDetalle = 'I' THEN 0 ELSE 1 END) AS Unidades,
				(CASE WHEN dod.TipoDetalle = 'I' THEN i.StockActual ELSE 0 END) AS StockActual,
				(CASE WHEN dod.TipoDetalle = 'I' THEN i.StockActualEqui ELSE 0 END) AS StockActualEqui,
				(CASE WHEN dod.TipoDetalle = 'I' THEN i.CodUnidadEqui ELSE 'UNI' END) AS CodUnidadEqui,
				(CASE WHEN dod.TipoDetalle = 'I' THEN i.CantidadEqui ELSE 0 END) AS CantidadEqui,
				(CASE WHEN dod.TipoDetalle = 'I' THEN i.CodImpuesto
					  ELSE (CASE WHEN s.FlagExoneradoIva = 'S' THEN 0 ELSE '$_PARAMETRO[COIVA]' END) END) AS CodImpuesto,
				(CASE WHEN dod.TipoDetalle = 'I' THEN i.FactorImpuesto
					  ELSE (CASE WHEN s.FlagExoneradoIva = 'S' THEN 0 ELSE '$igv' END) END) AS FactorImpuesto,
				(CASE WHEN dod.TipoDetalle = 'I' THEN i.MontoVenta ELSE 0 END) AS MontoVenta,
				(CASE WHEN dod.TipoDetalle = 'I' THEN i.MontoVentaUnitario ELSE 0 END) AS MontoVentaUnitario
			FROM co_documentodet dod
			LEFT JOIN vw_lg_inventarioactual_item i ON (
				i.CodItem = dod.CodItem
				AND dod.TipoDetalle = 'I'
			)
			LEFT JOIN co_mastservicios s ON (
				s.CodServicio = dod.CodItem
				AND dod.TipoDetalle = 'S'
			)
			WHERE dod.CodDocumento = '$field[CodDocumento]'";
	$field_detalle = getRecords($sql);
	##	
	$sql = "SELECT
				doco.*,
				o.Organismo,
				co.FechaCobranza,
				co.NroCobranza,
				tp.Descripcion AS TipoPago,
				cod.MontoLocal,
				b.Banco,
				cod.DocReferencia,
				cod.Estado
			FROM co_documentocobranza doco
			INNER JOIN co_cobranzadet cod ON cod.CodCobranza = doco.CodCobranza
			INNER JOIN co_cobranza co ON co.CodCobranza = cod.CodCobranza
			INNER JOIN mastorganismos o ON o.CodOrganismo = co.CodOrganismo
			LEFT JOIN co_tipopago tp ON tp.CodTipoPago = cod.CodTipoPago
			LEFT JOIN mastbancos b On b.CodBanco = cod.CodBanco
			WHERE doco.CodDocumento = '$field[CodDocumento]'";
	$field_cobranza = getRecords($sql);
	##	
	$sql = "SELECT
				td.CodItem,
				td.Descripcion,
				td.CantidadPedida,
				td.CantidadRecibida,
				t.CodDocumento,
				t.NroDocumento,
				t.NroInterno,
				t.CodAlmacen
			FROM lg_transacciondetalle td
			INNER JOIN lg_transaccion t ON (
				t.CodOrganismo = td.CodOrganismo
				AND t.CodDocumento = td.CodDocumento
				AND t.NroDocumento = td.NroDocumento
			)
			WHERE
				t.CodOrganismo = '$field[CodOrganismo]'
				AND t.CodTransaccion = '$_PARAMETRO[COTIPOTR]'
				AND t.CodDocumentoReferencia = '$field[CodTipoDocumento]'
				AND t.ReferenciaNroDocumento = '$field[CodDocumento]'";
	$field_transacciones = getRecords($sql);
	##
	if ($opcion == "modificar") {
		$_titulo = "Documentos / Modificar Registro";
		$accion = "modificar";
		$disabled_modificar = "disabled";
		$disabled_ver = "";
		$disabled_descuento = "";
		$disabled_item = "";
		$disabled_servicio = "";
		$disabled_opciones = "";
		$disabled_adelanto = "";
		$read_modificar = "readonly";
		$read_generar = "";
		$display_modificar = "display:none;";
		$display_ver = "";
		$display_facturar = "display:none;";
		$display_submit = "";
		$change_generar = 'setMontos();';
		$check_generar = "";
		$label_submit = "Modificar";
		$focus = "Comentarios";
	}
	##
	elseif ($opcion == "anular") {
		$_titulo = "Documentos / Anular Registro";
		$accion = "anular";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$disabled_descuento = "disabled";
		$disabled_item = "disabled";
		$disabled_servicio = "disabled";
		$disabled_opciones = "disabled";
		$disabled_adelanto = "";
		$read_modificar = "readonly";
		$read_generar = "";
		$display_modificar = "display:none;";
		$display_ver = "display:none;";
		$display_facturar = "display:none;";
		$display_submit = "";
		$change_generar = '';
		$check_generar = "this.checked = !this.checked";
		$label_submit = "Anular";
		$focus = "btSubmit";
	}
	##
	elseif ($opcion == "ver" || $opcion == "ver-modal") {
		$_titulo = "Documentos / Ver Registro";
		$accion = "";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$disabled_descuento = "disabled";
		$disabled_item = "disabled";
		$disabled_servicio = "disabled";
		$disabled_opciones = "disabled";
		$disabled_adelanto = "";
		$read_modificar = "readonly";
		$read_generar = "";
		$display_modificar = "display:none;";
		$display_ver = "display:none;";
		$display_facturar = "display:none;";
		$display_submit = "display:none;";
		$change_generar = '';
		$check_generar = "this.checked = !this.checked";
		$label_submit = "";
		$focus = "btCancelar";
	}
}
$clkCancelar = "document.getElementById('frmentrada').submit();";
//	------------------------------------
$_width = 900;
if ($origen == 'framemain') $action = '../framemain.php';
elseif (!empty($origen)) $action = "gehen.php?anz=$origen";
else $action = "gehen.php?anz=co_documento_lista";
?>

<?php if ($opcion != "ver-modal") { ?>
	<table width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td class="titulo"><?=$_titulo?></td>
			<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
		</tr>
	</table><hr width="100%" color="#333333" />
<?php } ?>

<form name="frmentrada" id="frmentrada" action="<?=$action?>" method="POST" enctype="multipart/form-data" onsubmit="return formSubmitFactura('co_documento_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
	<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
	<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
	<input type="hidden" name="fCodEstablecimiento" id="fCodEstablecimiento" value="<?=$fCodEstablecimiento?>" />
	<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
	<input type="hidden" name="fFechaDocumentoD" id="fFechaDocumentoD" value="<?=$fFechaDocumentoD?>" />
	<input type="hidden" name="fFechaDocumentoH" id="fFechaDocumentoH" value="<?=$fFechaDocumentoH?>" />
	<input type="hidden" name="fCodPersonaCliente" id="fCodPersonaCliente" value="<?=$fCodPersonaCliente?>" />
	<input type="hidden" name="fNombreCliente" id="fNombreCliente" value="<?=htmlentities($fNombreCliente)?>" />
	<input type="hidden" name="fDocFiscalCliente" id="fDocFiscalCliente" value="<?=$fDocFiscalCliente?>" />
	<input type="hidden" name="fVer" id="fVer" value="<?=$fVer?>" />
	<input type="hidden" name="FlagCredito" id="FlagCredito" value="<?=$field['FlagCredito']?>">
	<input type="hidden" name="DiasVence" id="DiasVence" value="<?=$field['DiasVence']?>">
	<input type="hidden" name="FlagImprimirDocumento" id="FlagImprimirDocumento" value="N" />
	<input type="hidden" name="CodImprimirDocumento" id="CodImprimirDocumento" value="" />
	<input type="hidden" name="CodDocumento" id="CodDocumento" value="<?=$field['CodDocumento']?>" />

	<table style="width:100%; min-width:<?=$_width?>px;" align="center" cellpadding="0" cellspacing="0">
	    <tr>
	        <td>
	            <div class="header">
		            <ul id="tab">
			            <!-- CSS Tabs -->
			            <li id="li1" onclick="currentTab('tab', this);" class="current">
			            	<a href="#" onclick="mostrarTab('tab', 1, 4);">Datos Generales</a>
			            </li>
			            <li id="li2" onclick="currentTab('tab', this);">
			            	<a href="#" onclick="mostrarTab('tab', 2, 4);">Datos Auxiliares</a>
			            </li>
			            <li id="li3" onclick="currentTab('tab', this);">
			            	<a href="#" onclick="mostrarTab('tab', 3, 4);">Otros Datos</a>
			            </li>
			            <li id="li4" onclick="currentTab('tab', this);">
			            	<a href="#" onclick="mostrarTab('tab', 4, 4);">Documentos Relacionados</a>
			            </li>
		            </ul>
	            </div>
	        </td>
	    </tr>
	</table>

	<div id="tab1" style="display:block;">
		<table style="width:100%; min-width:<?=$_width?>px;" class="tblForm">
			<tr>
		    	<td colspan="4" class="divFormCaption">Datos Generales</td>
		    </tr>
		    <tr>
				<td class="tagForm">Organismo:</td>
				<td>
					<select name="CodOrganismo" id="CodOrganismo" style="width:295px;" <?=$disabled_modificar?>>
						<?=getOrganismos($field['CodOrganismo'], 3)?>
					</select>
				</td>
				<td class="tagForm">Nro. Pedido:</td>
				<td class="gallery clearfix">
					<input type="hidden" name="CodPedido" id="CodPedido" value="<?=$field['CodPedido']?>">
		        	<input type="text" name="NroPedidoRef" id="NroPedidoRef" value="<?=$field['NroPedidoRef']?>" style="width:100px; font-weight:bold;" readonly />
					<a href="../lib/listas/gehen.php?anz=lista_co_pedidos&campo1=CodPedido&campo2=NroPedidoRef&ventana=&filtrar=default&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" style="<?=$display_modificar?>">
		            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
		            </a>
				</td>
			</tr>
		    <tr>
				<td class="tagForm">Establecimiento:</td>
				<td>
					<select name="CodEstablecimiento" id="CodEstablecimiento" style="width:295px;" <?=$disabled_ver?>>
						<?=loadSelect2('co_establecimientofiscal','CodEstablecimiento','Descripcion',$field['CodEstablecimiento'])?>
					</select>
				</td>
				<td class="tagForm">Nro. Cotización:</td>
				<td class="gallery clearfix">
					<input type="hidden" name="CodCotizacion" id="CodCotizacion" value="<?=$field['CodCotizacion']?>">
		        	<input type="text" name="NroCotizacion" id="NroCotizacion" value="<?=$field['NroCotizacion']?>" style="width:100px; font-weight:bold;" readonly />
					<a href="../lib/listas/gehen.php?anz=lista_co_cotizacion&campo1=CodCotizacion&campo2=NroCotizacion&ventana=&filtrar=default&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe2]" style="<?=$display_modificar?>">
		            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
		            </a>
				</td>
			</tr>
		    <tr>
				<td class="tagForm">Documento:</td>
				<td class="gallery clearfix">
					<a id="a_tipodocumento" href="../lib/listas/gehen.php?anz=lista_co_tipodocumento&campo1=CodTipoDocumento&campo2=TipoDocumento&campo3=CodSerie&campo4=NroSerie&campo5=NroDocumento&ventana=facturar&filtrar=default&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe3]" style="display: none;"></a>

					<input type="hidden" name="CodTipoDocumento" id="CodTipoDocumento" value="<?=$field['CodTipoDocumento']?>">
					<input type="text" name="TipoDocumento" id="TipoDocumento" value="<?=$field['TipoDocumento']?>" style="width:295px; font-weight: bold; font-size: 14px;" readonly />
					<a href="javascript:" onclick="seleccionar_tipodocumento();" style="display: none;" class="<?=(($opcion!='nuevo-adelanto')?'F1':'')?>">
		            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
		            </a>
				</td>
				<td class="tagForm">Doc. Original:</td>
				<td class="gallery clearfix">
					<a id="a_documento" href="../lib/listas/gehen.php?anz=lista_co_documento&campo1=DocOriginal&campo2=NroOriginal&ventana=&filtrar=default&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe4]"></a>

					<input type="hidden" name="DocOriginal" id="DocOriginal" value="<?=$field['DocOriginal']?>">
		        	<input type="text" name="NroOriginal" id="NroOriginal" value="<?=$field['NroOriginal']?>" style="width:100px; font-weight:bold;" readonly />
					<a href="javascript:" onclick="seleccionar_documento();" style="visibility: hidden;" id="a_documento_original">
		            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
		            </a>
				</td>
			</tr>
			<tr>
				<td class="tagForm">Número:</td>
				<td>
					<input type="hidden" name="CodSerie" id="CodSerie" value="<?=$field['CodSerie']?>">
					<input type="text" name="NroSerie" id="NroSerie" value="<?=$field['NroSerie']?>" style="width:66px; font-weight: bold; font-size: 14px;" readonly />
					<input type="text" name="NroDocumento" id="NroDocumento" value="<?=$field['NroDocumento']?>" style="width:225px; font-weight: bold; font-size: 14px;" readonly />
				</td>
				<td class="tagForm">Forma de Pago:</td>
				<td>
					<select name="CodFormaPago" id="CodFormaPago" style="width:125px;" onchange="setDiasVence(this.value);" <?=$disabled_ver?>>
						<?=loadSelect2('mastformapago','CodFormaPago','Descripcion',$field['CodFormaPago'],0)?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="tagForm">Cliente:</td>
				<td class="gallery clearfix">
					<input type="hidden" name="DocFiscalCliente" id="DocFiscalCliente" value="<?=$field['DocFiscalCliente']?>">
					<input type="text" name="CodPersonaCliente" id="CodPersonaCliente" value="<?=$field['CodPersonaCliente']?>" style="width:66px;" readonly />
					<input type="text" name="NombreCliente" id="NombreCliente" value="<?=htmlentities($field['NombreCliente'])?>" style="width:225px;" readonly />
					<a href="../lib/listas/gehen.php?anz=lista_personas&campo1=CodPersonaCliente&campo2=NombreCliente&campo3=DocFiscalCliente&campo4=DireccionCliente&campo5=FormaFactura&campo6=NomFormaFactura&campo7=TipoVenta&campo8=CodFormaPago&campo9=CodRutaDespacho&campo10=CodPersonaCobrar&campo11=CodParroquia&campo12=Parroquia&campo13=TelefonoCliente&campo14=CodPersonaVendedor&ventana=co_documento&filtrar=default&FlagClasePersona=S&fEsCliente=S&concepto=80-0003&_APLICACION=<?=$_APLICACION?>&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe5]" style="<?=$display_ver?>" class="F2">
		            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
		            </a>
				</td>
				<td class="tagForm">Forma Factura:</td>
				<td>
					<input type="hidden" name="NomFormaFactura" id="NomFormaFactura" value="<?=$field['NomFormaFactura']?>">
					<select name="CodFormaFactura" id="CodFormaFactura" style="width:125px;" <?=$disabled_ver?>>
						<?=getMiscelaneos($field['CodFormaFactura'], "FORMAFACT")?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="tagForm" rowspan="2">Dirección:</td>
				<td rowspan="2">
					<textarea name="DireccionCliente" id="DireccionCliente" style="width: 295px; height: 40px;" readonly="readonly"><?=htmlentities($field['DireccionCliente'])?></textarea>
				</td>
				<td class="tagForm">Almacén:</td>
				<td class="gallery clearfix">
					<input type="text" name="CodAlmacen" id="CodAlmacen" value="<?=$field['CodAlmacen']?>" style="width:125px;" readonly />
					<a href="../lib/listas/gehen.php?anz=lista_lg_almacen&campo1=CodAlmacen&fFlagVenta=S&ventana=&filtrar=default&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe6]" style="display: none;" class="F4">
		            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
		            </a>
				</td>
			</tr>
			<tr>
				<td class="tagForm">Fecha Emisión:</td>
				<td>
					<input type="text" name="FechaDocumento" id="FechaDocumento" value="<?=formatFechaDMA($field['FechaDocumento'])?>" maxlength="10" style="width:75px;" class="datepicker" onkeyup="setFechaDMA(this);" onchange="setDiasVence($('#CodFormaPago').val());" <?=$disabled_ver?> />
				</td>
			</tr>
		    <tr>
				<td class="tagForm">Parroquia:</td>
				<td>
					<input type="hidden" name="CodParroquia" id="CodParroquia" value="<?=$field['CodParroquia']?>">
					<input type="text" name="Parroquia" id="Parroquia" value="<?=$field['Parroquia']?>" style="width:295px;" readonly />
				</td>
				<td class="tagForm">Fecha Vencimiento:</td>
				<td>
					<input type="text" name="FechaVencimiento" id="FechaVencimiento" value="<?=formatFechaDMA($field['FechaVencimiento'])?>" maxlength="10" style="width:75px;" class="datepicker" onkeyup="setFechaDMA(this);" <?=$disabled_ver?> />
				</td>
			</tr>
		    <tr>
				<td class="tagForm">Tel&eacute;fono:</td>
				<td>
					<input type="text" name="TelefonoCliente" id="TelefonoCliente" value="<?=$field['TelefonoCliente']?>" style="width:146px;" readonly />
				</td>
				<th style="text-align: right;">Informaci&oacute;n Monetaria</th>
				<th></th>
			</tr>
		    <tr>
				<td class="tagForm">Vendedor:</td>
				<td>
					<select name="CodPersonaVendedor" id="CodPersonaVendedor" style="width:295px;" <?=$disabled_ver?>>
						<option value="">&nbsp;</option>
						<?=vendedores($field['CodPersonaVendedor'])?>
					</select>
				</td>
				<td class="tagForm">Moneda:</td>
				<td>
					<select name="MonedaDocumento" id="MonedaDocumento" style="width:125px;" <?=$disabled_ver?>>
						<?=loadSelectGeneral("monedas", $field['MonedaDocumento'])?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="tagForm" rowspan="4">Comentarios:</td>
				<td rowspan="4">
					<textarea name="Comentarios" id="Comentarios" style="width:295px; height: 85px;" <?=$disabled_ver?>><?=$field['Comentarios']?></textarea>
				</td>
				<td class="tagForm">Monto Afecto:</td>
				<td>
					<input type="text" name="MontoAfecto" id="MontoAfecto" value="<?=number_format($field['MontoAfecto'],2,',','.')?>" style="width:125px; text-align: right;" class="currency" readonly />
				</td>
			</tr>
			<tr>
				<td class="tagForm">Monto No Afecto:</td>
				<td>
					<input type="text" name="MontoNoAfecto" id="MontoNoAfecto" value="<?=number_format($field['MontoNoAfecto'],2,',','.')?>" style="width:125px; text-align: right;" class="currency" readonly />
				</td>
			</tr>
		    <tr>
				<td class="tagForm">
					<?php
					$PorcentajeDcto = $field['MontoDcto'] * 100 / ($field['MontoAfecto'] + $field['MontoNoAfecto'] + $field['MontoImpuesto']);
					?>
					(-) Monto Dcto.
					(<input type="text" id="PorcentajeDcto" value="<?=number_format($PorcentajeDcto,2,',','.')?>%" class="cell2 currency" style="width: 45px; text-align: right;" disabled="disabled" />):
				</td>
				<td>
					<input type="text" name="MontoDcto" id="MontoDcto" value="<?=number_format($field['MontoDcto'],2,',','.')?>" style="width:125px; text-align: right;" class="currency" readonly />
				</td>
			</tr>
			<tr>
				<td class="tagForm">(+) Imp. Vtas.:</td>
				<td>
					<input type="text" name="MontoImpuesto" id="MontoImpuesto" value="<?=number_format($field['MontoImpuesto'],2,',','.')?>" style="width:125px; text-align: right;" class="currency" readonly />
				</td>
			</tr>
		    <tr>
		    	<td>&nbsp;</td>
				<td>
					<input type="checkbox" name="FlagImprimir" id="FlagImprimir" value="S" <?=$disabled_ver?>> Imprimir Guia Remisión
				</td>
				<td class="tagForm"><strong>Monto Total:</strong></td>
				<td>
					<input type="text" name="MontoTotal" id="MontoTotal" value="<?=number_format($field['MontoTotal'],2,',','.')?>" style="width:125px; text-align: right; font-weight: bold; font-size: 14px;" class="currency" readonly />
				</td>
			</tr>
		    <tr>
				<td class="tagForm">&Uacute;ltima Modif.:</td>
				<td>
					<input type="text" value="<?=$field['UltimoUsuario']?>" style="width:146px;" disabled="disabled" />
					<input type="text" value="<?=$field['UltimaFecha']?>" style="width:145px" disabled="disabled" />
				</td>
				<td class="tagForm">Saldo del Adelanto:</td>
				<td>
					<input type="text" name="MontoAdelantoSaldo" id="MontoAdelantoSaldo" value="<?=number_format($field['MontoAdelantoSaldo'],2,',','.')?>" style="width:125px; text-align: right;" class="currency" readonly />
				</td>
			</tr>
		</table>

		<?php if ($opcion != "ver-modal") { ?>
			<?php if ($opcion != "nuevo-adelanto") { ?>
				<div class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
					<div style="padding: 5px;">
						F1: Tipo de Documento &nbsp; &nbsp; &nbsp; &nbsp;
						F2: Cliente &nbsp; &nbsp; &nbsp; &nbsp;
						F4: Almacén &nbsp; &nbsp; &nbsp; &nbsp;
						F6: Items &nbsp; &nbsp; &nbsp; &nbsp;
						F9: Guardar
					</div>
				</div>
			<?php } ?>

			<center>
				<input type="submit" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" id="btSubmit" />
				<input type="button" value="Cancelar" style="width:75px;" id="btCancelar" onclick="<?=$clkCancelar?>" />
			</center>

			<input type="hidden" id="sel_detalle" />
			<table style="width:100%; min-width:<?=$_width?>px;" class="tblBotones">
				<thead>
					<tr>
						<th class="divFormCaption" colspan="2">DETALLES</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>
							<input type="button" class="btLista" value="Descuento" onclick="modalDescuentoVentas();" <?=$disabled_ver?> <?=$disabled_adelanto?> />
						</td>
						<td align="right" class="gallery clearfix">
							<a class="F6" id="a_detalle_item" href="../lib/listas/gehen.php?anz=lista_lg_items&filtrar=default&fFlagDisponible=S&ventana=listado_insertar_linea_cotizacion&detalle=detalle&modulo=ajax&accion=detalle_insertar&url=../../co/co_documento_ajax.php&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe7]" style="display:none;"></a>
							<a id="a_detalle_servicio" href="../lib/listas/gehen.php?anz=lista_co_servicios&filtrar=default&ventana=listado_insertar_linea_cotizacion&detalle=detalle&modulo=ajax&accion=detalle_insertar&url=../../co/co_documento_ajax.php&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe8]" style="display:none;"></a>

							<input type="button" class="btLista" id="btInsertarItem" value="Item" onclick="$('#a_detalle_item').click();" <?=$disabled_ver?> <?=$disabled_adelanto?> />
							<input type="button" class="btLista" id="btInsertarServicio" value="Servicio" onclick="$('#a_detalle_servicio').click();" <?=$disabled_ver?> />
							<input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'detalle'); setMontosVentas();" <?=$disabled_ver?> />
						</td>
					</tr>
				</tbody>
			</table>
		<?php } ?>
		<div style="overflow:scroll; height:215px; width:100%; min-width:<?=$_width?>px; margin:auto;">
			<table class="tblLista" style="width:100%; min-width:1650px;">
				<thead>
					<tr>
						<th width="20">#</th>
						<th width="75">Item / Servicio</th>
						<th align="left">Descripci&oacute;n</th>
						<th width="20">I/S</th>
						<th width="100">Almacén</th>
						<th width="60">Stock Actual</th>
						<th width="60">Stock Actual (Venta)</th>
						<th width="40">Uni.</th>
						<th width="40">Uni. Venta</th>
						<th width="75">Cant. Pedida</th>
						<th width="125">Precio Unit.</th>
						<th width="125">Monto Total</th>
						<th width="45">Exon. Imp.</th>
						<th width="125">Precio Unit. s/Dcto.</th>
						<th width="125">Precio Unit.</th>
						<th width="125">Monto</th>
						<th width="40">Dcto. %</th>
					</tr>
				</thead>
				
				<tbody id="lista_detalle">
					<?php
					$nro_detalle = 0;
					$PrecioUnit = 0;
					$MontoTotal = 0;
					$PrecioUnitOriginal = 0;
					$PrecioUnitFinal = 0;
					$MontoTotalFinal = 0;
					foreach ($field_detalle as $f)
					{
						$id = ++$nro_detalle;
						?>
						<tr class="trListaBody" onclick="clk($(this), 'detalle', 'detalle_<?=$id?>');" id="detalle_<?=$id?>">
							<th>
								<input type="hidden" name="detalle_Secuencia[]" id="detalle_Secuencia<?=$id?>" value="<?=$f['Secuencia']?>">
								<input type="hidden" name="detalle_TipoDetalle[]" value="<?=$f['TipoDetalle']?>">
								<input type="hidden" name="detalle_Estado[]" value="<?=$f['Estado']?>">
								<input type="hidden" name="detalle_CodUnidadEqui[]" id="detalle_CodUnidadEqui<?=$id?>" value="<?=$f['CodUnidadEqui']?>">
								<input type="hidden" name="detalle_CantidadEqui[]" id="detalle_CantidadEqui<?=$id?>" value="<?=$f['CantidadEqui']?>">
								<input type="hidden" name="detalle_CodImpuesto[]" id="detalle_CodImpuesto<?=$id?>" value="<?=$f['CodImpuesto']?>">
								<input type="hidden" name="detalle_FactorImpuesto[]" id="detalle_FactorImpuesto<?=$id?>" value="<?=$f['FactorImpuesto']?>">
								<input type="hidden" name="detalle_MontoVenta[]" id="detalle_MontoVenta<?=$id?>" value="<?=$f['MontoVenta']?>">
								<input type="hidden" name="detalle_MontoVentaUnitario[]" id="detalle_MontoVentaUnitario<?=$id?>" value="<?=$f['MontoVentaUnitario']?>">
								<?=++$nro_detalle?>
							</th>
							<td>
								<input type="hidden" name="detalle_CodItem[]" value="<?=$f['CodItem']?>">
								<input type="text" name="detalle_CodInterno[]" value="<?=$f['CodInterno']?>" class="cell2" style="text-align: center;" readonly>
							</td>
							<td>
								<input type="text" name="detalle_Descripcion[]" value="<?=$f['Descripcion']?>" class="cell2" readonly>
							</td>
							<td align="center"><?=$f['TipoDetalle']?></td>
							<td>
								<input type="text" name="detalle_CodAlmacen[]" value="<?=$f['CodAlmacen']?>" style="text-align:center;" class="cell2" readonly>
							</td>
							<td>
								<input type="text" name="detalle_StockActual[]" value="<?=number_format($f['StockActual'],5,',','.')?>" class="cell2" style="text-align:right;" readonly>
							</td>
							<td>
								<input type="text" name="detalle_StockActualEqui[]" value="<?=number_format($f['StockActualEqui'],5,',','.')?>" class="cell2" style="text-align:right;" readonly>
							</td>
							<td>
								<select name="detalle_CodUnidad[]" id="detalle_CodUnidad<?=$id?>" class="cell" <?=$disabled_ver?>>
									<?=loadSelect2('mastunidades','CodUnidad','CodUnidad',$f['CodUnidad'],1)?>
								</select>
							</td>
							<td>
								<?php if ($f['TipoDetalle'] == 'I') { ?>
									<select name="detalle_CodUnidadVenta[]" id="detalle_CodUnidadVenta<?=$id?>" class="cell" onchange="cambiarUnidad('<?=$id?>');" <?=$disabled_ver?>>
										<?=unidades_item($f['CodItem'],$f['CodUnidadVenta'],0)?>
									</select>
								<?php } else { ?>
									<select name="detalle_CodUnidadVenta[]" id="detalle_CodUnidadVenta<?=$id?>" class="cell" <?=$disabled_ver?>>
										<?=loadSelect2('mastunidades','CodUnidad','CodUnidad',$f['CodUnidadVenta'],1)?>
									</select>
								<?php } ?>
							</td>
							<td>
								<input type="text" name="detalle_CantidadPedida[]" value="<?=number_format($f['CantidadPedida'],5,',','.')?>" class="cell currency5" style="text-align:right;" onchange="setMontosVentas();" <?=$disabled_ver?> <?=$read_generar?>>
							</td>
							<td>
								<input type="text" name="detalle_PrecioUnit[]" value="<?=number_format($f['PrecioUnit'],2,',','.')?>" class="cell currency" style="text-align:right;" onchange="setMontosVentas(true, '<?=$id?>');" <?=$disabled_ver?> <?=$read_generar?>>
							</td>
							<td>
								<input type="text" name="detalle_MontoTotal[]" value="<?=number_format($f['MontoTotal'],2,',','.')?>" class="cell2" style="text-align:right;" readonly>
							</td>
							<td align="center">
								<input type="checkbox" name="detalle_FlagExonIva[]" value="S" onchange="<?=$change_generar?>" onclick="<?=$check_generar?>" <?=chkFlag($f['FlagExonIva'])?> <?=$disabled_ver?> />
							</td>
							<td>
								<input type="text" name="detalle_PrecioUnitOriginal[]" id="detalle_PrecioUnitOriginal<?=$id?>" value="<?=number_format($f['PrecioUnitOriginal'],2,',','.')?>" class="cell2" style="text-align:right;" readonly>
							</td>
							<td>
								<input type="text" name="detalle_PrecioUnitFinal[]" id="detalle_PrecioUnitFinal<?=$id?>" value="<?=number_format($f['PrecioUnitFinal'],5,',','.')?>" class="cell2" style="text-align:right;" readonly>
							</td>
							<td>
								<input type="text" name="detalle_MontoTotalFinal[]" id="detalle_MontoTotalFinal<?=$id?>" value="<?=number_format($f['MontoTotalFinal'],2,',','.')?>" class="cell2" style="text-align:right;" readonly>
							</td>
							<td>
								<input type="hidden" name="detalle_MontoDcto[]" id="detalle_MontoDcto<?=$id?>" value="<?=number_format($f['MontoDcto'],2,',','.')?>" class="">
								<input type="text" name="detalle_PorcentajeDcto[]" id="detalle_PorcentajeDcto<?=$id?>" value="<?=number_format($f['PorcentajeDcto'],2,',','.')?>" class="cell2" style="text-align:right;" readonly>
							</td>
						</tr>
						<?php
						$PrecioUnit += $f['PrecioUnit'];
						$MontoTotal += $f['MontoTotal'];
						$PrecioUnitOriginal += $f['PrecioUnitOriginal'];
						$PrecioUnitFinal += $f['PrecioUnitFinal'];
						$MontoTotalFinal += $f['MontoTotalFinal'];
					}
					?>
				</tbody>

				<tfoot>
					<tr>
						<th colspan="9"></th>
						<th align="right" id="thPrecioUnit"><?=number_format($PrecioUnit,2,',','.')?></th>
						<th align="right" id="thMontoTotal"><?=number_format($MontoTotal,2,',','.')?></th>
						<th></th>
						<th align="right" id="thPrecioUnitOriginal"><?=number_format($PrecioUnitOriginal,2,',','.')?></th>
						<th align="right" id="thPrecioUnitFinal"><?=number_format($PrecioUnitFinal,2,',','.')?></th>
						<th align="right" id="thMontoTotalFinal"><?=number_format($MontoTotalFinal,2,',','.')?></th>
						<th></th>
					</tr>
				</tfoot>
			</table>
		</div>
		<input type="hidden" id="nro_detalle" value="<?=$nro_detalle?>" />
		<input type="hidden" id="can_detalle" value="<?=$nro_detalle?>" />
	</div>

	<div id="tab2" style="display:none;">
		<table style="width:100%; min-width:<?=$_width?>px;" class="tblForm">
			<tr>
		    	<td colspan="4" class="divFormCaption">Datos Adicionales</td>
		    </tr>
		    <tr>
				<td class="tagForm">Ruta Despacho:</td>
				<td>
					<input type="hidden" name="CodRutaDespacho" id="CodRutaDespacho" value="<?=$field['CodRutaDespacho']?>">
		        	<input type="text" name="RutaDespacho" id="RutaDespacho" value="<?=$field['RutaDespacho']?>" style="width:295px;" disabled />
				</td>
				<td class="tagForm">Tipo Venta:</td>
				<td>
					<input type="hidden" name="TipoVenta" id="TipoVenta" value="<?=$field['TipoVenta']?>">
		        	<input type="text" name="NomTipoVenta" id="NomTipoVenta" value="<?=$field['NomTipoVenta']?>" style="width:100px;" disabled />
				</td>
			</tr>
			<tr>
				<td class="tagForm">Persona Cobrar:</td>
				<td>
					<input type="hidden" name="DocFiscalCobrar" id="DocFiscalCobrar" value="<?=$field['DocFiscalCobrar']?>">
					<input type="text" name="CodPersonaCobrar" id="CodPersonaCobrar" value="<?=$field['CodPersonaCobrar']?>" style="width:66px;" readonly />
					<input type="text" name="NombreCobrar" id="NombreCobrar" value="<?=$field['NombreCobrar']?>" style="width:225px;" readonly />
				</td>
				<td class="tagForm">C.Costo:</td>
				<td>
					<input type="hidden" name="CodCentroCosto" id="CodCentroCosto" value="<?=$field['CodCentroCosto']?>">
		        	<input type="text" name="CentroCosto" id="CentroCosto" value="<?=$field['CentroCosto']?>" style="width:100px;" disabled />
				</td>
			</tr>
			<tr>
				<td class="tagForm">Preparado Por:</td>
				<td>
					<input type="hidden" name="PreparadoPor" id="PreparadoPor" value="<?=$field['PreparadoPor']?>" />
					<input type="text" name="NomPreparadoPor" id="NomPreparadoPor" value="<?=$field['NomPreparadoPor']?>" style="width:185px;" readonly />
					<input type="text" name="FechaPreparado" id="FechaPreparado" value="<?=$field['FechaPreparado']?>" style="width:106px;" readonly />
				</td>
				<td class="tagForm">Estado:</td>
				<td>
					<input type="hidden" name="Estado" id="Estado" value="<?=$field['Estado']?>">
		        	<input type="text" value="<?=mb_strtoupper(printValores('documento3-estado',$field['Estado']))?>" style="width:100px; font-weight:bold;" disabled />
				</td>
			</tr>
			<tr>
				<td class="tagForm">Aprobado Por:</td>
				<td>
					<input type="hidden" name="AprobadoPor" id="AprobadoPor" value="<?=$field['AprobadoPor']?>" />
					<input type="text" name="NomAprobadoPor" id="NomAprobadoPor" value="<?=$field['NomAprobadoPor']?>" style="width:185px;" readonly />
					<input type="text" name="FechaAprobado" id="FechaAprobado" value="<?=$field['FechaAprobado']?>" style="width:106px;" readonly />
				</td>
				<td class="tagForm">% IVA:</td>
				<td>
					<input type="hidden" name="igv" id="igv" value="<?=$igv?>" />
					<input type="text" value="<?=number_format($igv,2,',','.')?>" style="width:100px; text-align: right;" readonly />
				</td>
			</tr>
		</table>
	</div>

	<div id="tab3" style="display:none;">
	</div>

	<div id="tab4" style="display:none;">
		<table style="width:100%; min-width:<?=$_width?>px;" class="tblBotones">
			<thead>
				<tr>
					<th class="divFormCaption" colspan="2">COBRANZAS</th>
				</tr>
			</thead>
		</table>
		<div style="overflow:scroll; height:200px; width:100%; min-width:<?=$_width?>px; margin:auto;">
			<table class="tblLista" style="width:100%; min-width:<?=$_width?>px;">
				<thead>
					<tr>
						<th width="20">#</th>
						<th style="min-width: 250px;" align="left">Organismo</th>
						<th width="100">Cobranza #</th>
						<th width="75">Fecha</th>
						<th width="150">Tipo Pago</th>
						<th width="150">Moneda Pago</th>
						<th width="150">Monto Local</th>
						<th style="min-width: 250px;" align="left">Banco</th>
						<th width="150">Doc. Referencia</th>
						<th width="150">Estado</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$nro_detalle = 0;
					foreach ($field_cobranza as $f)
					{
						++$nro_detalle;
						?>
						<tr class="trListaBody">
							<th><?=$nro_detalle?></th>
							<td><?=htmlentities($f['Organismo'])?></td>
							<td align="center"><?=$f['NroCobranza']?></td>
							<td align="center"><?=formatFechaAMD($f['FechaCobranza'])?></td>
							<td align="center"><?=$f['TipoPago']?></td>
							<td align="center"><?=printValores('monedas',$f['MonedaDocumento'])?></td>
							<td align="right"><?=number_format($f['MontoLocal'],5,',','.')?></td>
							<td><?=htmlentities($f['Banco'])?></td>
							<td align="center"><?=$f['DocReferencia']?></td>
							<td align="center"><?=printValores('cobranza-estado',$f['Estado'])?></td>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>
		</div>

		<table style="width:100%; min-width:<?=$_width?>px;" class="tblBotones">
			<thead>
				<tr>
					<th class="divFormCaption" colspan="2">TRANSACCIONES DE ALMACÉN</th>
				</tr>
			</thead>
		</table>
		<div style="overflow:scroll; height:200px; width:100%; min-width:<?=$_width?>px; margin:auto;">
			<table class="tblLista" style="width:100%; min-width:<?=$_width?>px;">
				<thead>
					<tr>
						<th width="20">#</th>
						<th width="60">Código</th>
						<th align="left">Descripci&oacute;n</th>
						<th width="75">Cantidad</th>
						<th width="150">Transacción</th>
						<th width="150">Almacén</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$nro_detalle = 0;
					foreach ($field_transacciones as $f)
					{
						++$nro_detalle;
						?>
						<tr class="trListaBody">
							<th><?=$nro_detalle?></th>
							<td align="center"><?=$f['CodItem']?></td>
							<td><?=htmlentities($f['Descripcion'])?></td>
							<td align="right"><?=number_format($f['CantidadPedida'],5,',','.')?></td>
							<td align="center"><?=$f['CodDocumento']?>-<?=$f['NroInterno']?></td>
							<td align="center"><?=$f['CodAlmacen']?></td>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>
		</div>
	</div>
</form>
<div style="width:100%; min-width:<?=$_width?>px;" class="divMsj">Campos Obligatorios *</div>

<script type="text/javascript">
	function formSubmitFactura() {
		$("#cajaModal").dialog({
			buttons: {
				"Si": function() {
					if ('<?=$_PARAMETRO['COBRANZAVENTA']?>' == 'S' && $('#FlagCredito').val() != 'S') {
						cobranza();
					} else {
						formSubmitCobranza();
					}

					$(this).dialog("close");
				},
				"No": function() {
					$(this).dialog("close");
				}
			}
		});
		cajaModalConfirm("Se va a generar el documento <strong>"+$('#CodTipoDocumento').val()+"-"+$('#NroDocumento').val()+"</strong><br>¿Está seguro de continuar?", 800);

		return false;
	}
	function cobranza() {
		//	ajax
		$.post('co_pedidos_cobranza_form.php', $('#frmentrada').serialize(), function(data) {
			$("#cajaModal").dialog({
				buttons: {
					"Aceptar": function() {
						$(this).dialog("close");
						formSubmitCobranza();
					},
					"Cancelar": function() {
						$(this).dialog("close");
					}
				}
			});
			$("#cajaModal").dialog({ title: "<img src='../imagenes/info.png' width='24' align='absmiddle' />Cobranza", width: 800 });
			$("#cajaModal").html(data);
			$('#cajaModal').dialog('open');
			inicializar();
	    });
	}
	function formSubmitCobranza() {
		$.post('co_documento_ajax.php', 'modulo=formulario&accion=<?=$accion?>&'+$('#frmentrada').serialize()+'&'+$('#frmcobranza').serialize(), function(resp) {
			var data = resp.split("|");

			if (data[0].trim() != '') cajaModal(data[0].trim(), 'error', 600);
			else {
				$('#FlagImprimirDocumento').val('S');
				$('#CodImprimirDocumento').val(data[2]);
				cajaModal(data[1].trim(), 'success', 600, "document.getElementById('frmentrada').submit();");
			}
	    });
	}
	function seleccionar_documento() {
		var CodPersonaCliente = $('#CodPersonaCliente').val();
		if (CodPersonaCliente) {
			var href='../lib/listas/gehen.php?anz=lista_co_documento&campo1=DocOriginal&campo2=NroOriginal&campo3=CodPedido&campo4=NroPedidoRef&campo5=CodCotizacion&campo6=NroCotizacion&ventana=documento&filtrar=default&fCodPersonaCliente='+CodPersonaCliente+'&iframe=true&width=100%&height=100%';
			$('#a_documento').attr('href', href);
			$('#a_documento').click();
		} else {
			cajaModal('Debe seleccionar el Cliente');
		}
	}
	function seleccionar_tipodocumento() {
		var CodOrganismo = $('#CodOrganismo').val();
		var CodEstablecimiento = $('#CodEstablecimiento').val();
		if (CodOrganismo && CodEstablecimiento) {
			var href = '../lib/listas/gehen.php?anz=lista_co_tipodocumento&campo1=CodTipoDocumento&campo2=TipoDocumento&campo3=CodSerie&campo4=NroSerie&campo5=NroDocumento&ventana=facturar&filtrar=default&CodOrganismo='+CodOrganismo+'&CodEstablecimiento='+CodEstablecimiento+'&iframe=true&width=100%&height=100%';
			$('#a_tipodocumento').attr('href', href);
			$('#a_tipodocumento').click();
		} else {
			cajaModal('Debe seleccionar el Organismo y Establecimeinto');
		}
	}
	function setDiasVence(CodFormaPago) {
		$.post('co_documento_ajax.php', 'modulo=ajax&accion=setDiasVence&CodFormaPago='+CodFormaPago+'&FechaDocumento='+$('#FechaDocumento').val(), function(data) {
			$('#FechaVencimiento').val(data['FechaVencimiento']);
	    }, 'json');
	}
</script>