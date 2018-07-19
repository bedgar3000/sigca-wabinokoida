<?php
//	------------------------------------
$filtro_documentos = '';
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$fPeriodo = $PeriodoActual;
}
if ($fCodOrganismo != "") {
	$cCodOrganismo = "checked";
	$filtro_documentos.=" AND (do.CodOrganismo = '$fCodOrganismo')";
	$filtro_cobranzas.=" AND (ac.CodOrganismo = '$fCodOrganismo')";
} else $dCodOrganismo = "disabled";
if ($fPeriodo != "") {
	$cPeriodo = "checked";
	$filtro_documentos.=" AND (do.VoucherPeriodo = '$fPeriodo')";
	$filtro_cobranzas.=" AND (ac.VoucherPeriodo = '$fPeriodo')";
} else $dPeriodo = "disabled";

if ($fFechaDocumentoD != "" || $fFechaDocumentoH != "") {
	$cFechaDocumento = "checked";
	if ($fFechaDocumentoD != "") $filtro_documentos.=" AND (do.FechaDocumento >= '".formatFechaAMD($fFechaDocumentoD)."')";
	if ($fFechaDocumentoH != "") $filtro_documentos.=" AND (do.FechaDocumento <= '".formatFechaAMD($fFechaDocumentoH)."')";
} else $dFechaDocumento = "disabled";
if ($fCodTipoDocumento != "") {
	$cCodTipoDocumento = "checked";
	$filtro_documentos.=" AND (do.CodTipoDocumento = '$fCodTipoDocumento')";
} else $dCodTipoDocumento = "disabled";
if ($fNroSerie != "") {
	$cNroSerie = "checked";
	$filtro_documentos.=" AND (SUBSTRING(do.NroDocumento, 1, 3) = '$fNroSerie')";
} else $dNroSerie = "disabled";

if ($lista == 'oncop') {
	$filtro_documentos.=" AND (do.FlagContabilizacionPendiente = 'S')";
	$filtro_cobranzas.=" AND (ac.FlagContabilizacionPendiente = 'S')";
} else {
	$filtro_documentos.=" AND (do.FlagContabilizacionPendientePub20 = 'S')";
	$filtro_cobranzas.=" AND (ac.FlagContabilizacionPendientePub20 = 'S')";
}
//	------------------------------------
if ($lista == 'oncop')
{
	$_titulo = "Generación de Vouchers (ONCOP)";
}
else
{
	$_titulo = "Generación de Vouchers (Pub. 20)";	
}
$_width = 800;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=co_generacion_vouchers_lista" method="post" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />

	<!--FILTRO-->
	<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
		<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
			<tr>
				<td align="right" width="125">Organismo:</td>
				<td>
					<input type="checkbox" <?=$cCodOrganismo?> onclick="this.checked=!this.checked;" />
					<select name="fCodOrganismo" id="fCodOrganismo" style="width:350px;" <?=$dCodOrganismo?>>
						<?=getOrganismos($fCodOrganismo, 3);?>
					</select>
				</td>
				<td align="right">Periodo:</td>
				<td>
					<input type="checkbox" <?=$cPeriodo?> onclick="chkCampos(this.checked, 'fPeriodo');" />
					<input type="text" name="fPeriodo" id="fPeriodo" value="<?=$fPeriodo?>" style="width:75px;" <?=$dPeriodo?> />
				</td>
		        <td align="right"><input type="submit" value="Buscar"></td>
			</tr>
		</table>
	</div>
	<div class="sep"></div>

    <table style="width:100%; min-width:<?=$_width?>px;">
        <tr>
            <td>
                <div class="header">
                    <ul id="tab">
                        <!-- CSS Tabs -->
                        <li id="li1" onclick="currentTab('tab', this);" class="current">
                            <a href="#" onclick="mostrarTab('tab', 1, 2);">
                                Provisión de Documentos
                            </a>
                        </li>
                        <li id="li2" onclick="currentTab('tab', this);">
                            <a href="#" onclick="mostrarTab('tab', 2, 2);">
                                Cobranzas (Arqueo de Caja)
                            </a>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
    </table>

    <div id="tab1" style="display:block;">
		<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
			<tr>
				<td align="right" width="125">Fecha Documento:</td>
				<td>
					<input type="checkbox" <?=$cFechaDocumento?> onclick="chkCampos2(this.checked, ['fFechaDocumentoD','fFechaDocumentoH']);" />
					<input type="text" name="fFechaDocumentoD" id="fFechaDocumentoD" value="<?=$fFechaDocumentoD?>" maxlength="10" style="width:65px;" class="datepicker" onkeyup="setFechaDMA(this);" <?=$dFechaDocumento?> /> -
		            <input type="text" name="fFechaDocumentoH" id="fFechaDocumentoH" value="<?=$fFechaDocumentoH?>" maxlength="10" style="width:65px;" class="datepicker" onkeyup="setFechaDMA(this);" <?=$dFechaDocumento?> />
		        </td>
				<td align="right">Tipo Doc.:</td>
				<td>
					<input type="checkbox" <?=$cCodTipoDocumento?> onclick="chkFiltro(this.checked, 'fCodTipoDocumento');" />
					<select name="fCodTipoDocumento" id="fCodTipoDocumento" style="width:200px;" <?=$dCodTipoDocumento?>>
						<option value="">&nbsp;</option>
						<?=loadSelect2('co_tipodocumento','CodTipoDocumento','Descripcion',$fCodTipoDocumento,10)?>
					</select>
				</td>
				<td align="right">Serie:</td>
				<td>
					<input type="checkbox" <?=$cNroSerie?> onclick="chkCampos(this.checked, 'fNroSerie');" />
					<input type="text" name="fNroSerie" id="fBuscar" value="<?=$fNroSerie?>" style="width:75px;" maxlength="3" <?=$dNroSerie?> />
				</td>
			</tr>
		</table>
		<div class="sep"></div>
		<!--REGISTROS-->
		<input type="hidden" name="sel_documentos" id="sel_documentos" />
		<table class="tblBotones" style="width:100%; min-width:<?=$_width?>px;">
		    <tr>
		        <td align="right">
		            <input type="button" value="Generar Voucher" style="width:105px;" class="insert" onclick="cargarOpcion2(this.form, 'gehen.php?anz=co_generacion_vouchers_form&opcion=documento_<?=$lista?>', 'SELF', '', $('#sel_documentos').val());" />
		        </td>
		    </tr>
		</table>

		<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px;">
			<table class="tblLista" style="width:100%; min-width:<?=$_width?>px;">
				<thead>
				    <tr>
				        <th width="90">Doc. Fiscal</th>
				        <th style="min-width: 300px;" align="left">Cliente</th>
				        <th width="100">Documento</th>
				        <th width="75">Fecha Doc.</th>
				        <th width="75">Moneda</th>
				        <th width="150" align="right">Monto Total</th>
				    </tr>
			    </thead>
			    
			    <tbody id="lista_documentos">
					<?php
					//	consulto todos
					$sql = "SELECT
								do.*,
								td.FlagProvision
							FROM co_documento do
							INNER JOIN co_tipodocumento td ON td.CodTipoDocumento = do.CodTipoDocumento
							WHERE
								do.Estado <> 'AN'
								AND td.FlagProvision = 'S'
								$filtro_documentos
							ORDER BY FechaDocumento, CodTipoDocumento, NroDocumento";
					$field_documentos = getRecords($sql);
					$rows_documentos = count($field_documentos);
					foreach($field_documentos as $f) {
						$id = $f['CodDocumento'];
						?>
						<tr class="trListaBody" onclick="clk($(this), 'documentos', '<?=$id?>');">
							<td align="center"><?=set_rif($f['DocFiscalCliente'])?></td>
							<td><?=htmlentities($f['NombreCliente'])?></td>
							<td align="center"><?=$f['CodTipoDocumento']?> <?=$f['NroDocumento']?></td>
							<td align="center"><?=formatFechaDMA($f['FechaDocumento'])?></td>
							<td align="center"><?=printValoresGeneral('monedas',$f['MonedaDocumento'])?></td>
							<td align="right"><?=number_format($f['MontoTotal'],2,',','.')?></td>
						</tr>
						<?php
					}
					?>
			    </tbody>
			</table>
		</div>
    </div>

    <div id="tab2" style="display:none;">
		<!--REGISTROS-->
		<input type="hidden" name="sel_cobranzas" id="sel_cobranzas" />
		<table class="tblBotones" style="width:100%; min-width:<?=$_width?>px;">
		    <tr>
		        <td align="right">
		            <input type="button" value="Generar Voucher" style="width:105px;" class="insert" onclick="cargarOpcion2(this.form, 'gehen.php?anz=co_generacion_vouchers_form&opcion=cobranza_<?=$lista?>', 'SELF', '', $('#sel_cobranzas').val());" />
		        </td>
		    </tr>
		</table>

		<div style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:225px;">
			<table class="tblLista" style="width:100%; min-width:<?=$_width?>px;">
				<thead>
				    <tr>
				        <th width="100">Arqueo #</th>
				        <th width="100">Fecha</th>
				        <th width="150" align="right">Monto Total</th>
				        <th style="min-width: 300px;" align="left">Organismo</th>
				    </tr>
			    </thead>
			    
			    <tbody id="lista_cobranzas">
					<?php
					//	consulto todos
					$sql = "SELECT
								ac.*,
								o.Organismo,
								SUM(cod.MontoLocal) AS MontoTotal
							FROM co_arqueocaja ac
							INNER JOIN mastorganismos o ON o.CodOrganismo = ac.CodOrganismo
							INNER JOIN co_cobranzadet cod ON cod.CodArqueo = ac.CodArqueo
							WHERE
								ac.Estado <> 'AN'
								$filtro_cobranzas
							GROUP BY CodArqueo
							ORDER BY NroArqueo";
					$field_cobranzas = getRecords($sql);
					$rows_cobranzas = count($field_cobranzas);
					foreach($field_cobranzas as $f) {
						$id = $f['CodArqueo'];
						?>
						<tr class="trListaBody" onclick="clk($(this), 'cobranzas', '<?=$id?>'); get_cobranzas_detalle('<?=$id?>')">
							<td align="center"><?=$f['NroArqueo']?></td>
							<td align="center"><?=formatFechaDMA($f['Fecha'])?></td>
							<td align="right"><?=number_format($f['MontoTotal'],2,',','.')?></td>
							<td><?=htmlentities($f['Organismo'])?></td>
						</tr>
						<?php
					}
					?>
			    </tbody>
			</table>
		</div>
		<table class="tblBotones" style="width:100%; min-width:<?=$_width?>px;">
		    <tr>
		        <td>DETALLES</td>
		    </tr>
		</table>
		<div style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:225px;">
			<table class="tblLista" style="width:100%; min-width:1100px;">
				<thead>
				    <tr>
				        <th width="25">#</th>
				        <th width="160">Cta. Bancaria</th>
				        <th style="min-width: 300px;" align="left">Banco</th>
				        <th width="100">Cta. Contable</th>
				        <th width="50">Moneda Pago</th>
				        <th width="100">Tipo de Pago</th>
				        <th width="75">Fecha</th>
				        <th width="150">Monto</th>
				        <th width="100">Cta. Descuento</th>
				        <th width="100">Arqueo Doc. Referencia</th>
				    </tr>
			    </thead>
			    
			    <tbody id="lista_cobranzas_detalle">
			    </tbody>
			</table>
		</div>
    </div>
</form>

<script type="text/javascript">
	function get_cobranzas_detalle(CodArqueo) {
		$('#lista_cobranzas_detalle').html('Cargando...');
		$.post('co_generacion_vouchers_ajax.php', "modulo=ajax&accion=cobranzas_detalle&lista=<?=$lista?>&CodArqueo="+CodArqueo, function(data) {
			$('#lista_cobranzas_detalle').html(data);
	    });
	}
</script>