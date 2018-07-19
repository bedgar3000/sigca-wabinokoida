<?php
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$fFechaDocumentoD = formatFechaDMA($PeriodoActual.'-01');
	$fFechaDocumentoH = formatFechaDMA($FechaActual);
	$fVer = 'PP';
}
//	------------------------------------
$filtro = '';
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (do.CodTipoDocumento LIKE '%$fBuscar%'
					  OR do.NroDocumento LIKE '%$fBuscar%'
					  OR do.NombreCliente LIKE '%$fBuscar%')";
} else $dBuscar = "disabled";
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (do.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fFechaDocumentoD != "" || $fFechaDocumentoH != "") {
	$cFechaDocumento = "checked";
	if ($fFechaDocumentoD != "") $filtro.=" AND (do.FechaDocumento >= '".formatFechaAMD($fFechaDocumentoD)."')";
	if ($fFechaDocumentoH != "") $filtro.=" AND (do.FechaDocumento <= '".formatFechaAMD($fFechaDocumentoH)."')";
} else $dFechaDocumento = "disabled";
if ($fCodPersonaCliente != "") { $cCodPersonaCliente = "checked"; $filtro.=" AND (do.CodPersonaCliente = '".$fCodPersonaCliente."')"; } else $dCodPersonaCliente = "visibility:hidden;";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (do.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fVer != "") { 
	$cVer = "checked";
	if ($fVer == 'PP') $filtro.=" AND (do.MontoAdelantoSaldo > 0.00)";
	elseif ($fVer == 'PA') $filtro.=" AND (do.MontoAdelantoSaldo = 0.00)";
} else $dVer = "disabled";
//	------------------------------------
$_titulo = "Lista de Adelantos";
$_width = 900;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=co_documentoadelanto_lista" method="post" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />

	<!--FILTRO-->
	<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
		<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
			<tr>
				<td align="right">Organismo:</td>
				<td>
					<input type="checkbox" <?=$cCodOrganismo?> onclick="this.checked=!this.checked;" />
					<select name="fCodOrganismo" id="fCodOrganismo" style="width:225px;" <?=$dCodOrganismo?>>
						<?=getOrganismos($fCodOrganismo, 3);?>
					</select>
				</td>
				<td align="right">Fecha:</td>
				<td>
					<input type="checkbox" <?=$cFechaDocumento?> onclick="chkCampos2(this.checked, ['fFechaDocumentoD','fFechaDocumentoH']);" />
					<input type="text" name="fFechaDocumentoD" id="fFechaDocumentoD" value="<?=$fFechaDocumentoD?>" <?=$dFechaDocumento?> maxlength="10" style="width:70px;" class="datepicker" onkeyup="setFechaDMA(this);" /> -
		            <input type="text" name="fFechaDocumentoH" id="fFechaDocumentoH" value="<?=$fFechaDocumentoH?>" <?=$dFechaDocumento?> maxlength="10" style="width:70px;" class="datepicker" onkeyup="setFechaDMA(this);" />
		        </td>
				<td align="right">Estado: </td>
				<td>
		            <input type="checkbox" <?=$cEstado?> onclick="chkFiltro(this.checked, 'fEstado');" />
		            <select name="fEstado" id="fEstado" style="width:100px;" <?=$dEstado?>>
		                <option value="">&nbsp;</option>
		                <?=loadSelectValores("documento3-estado", $fEstado)?>
		            </select>
				</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td align="right">Cliente:</td>
				<td class="gallery clearfix">
					<input type="checkbox" <?=$cCodPersonaCliente?> onclick="ckLista(this.checked, ['fCodPersonaCliente','fNombreCliente','fDocFiscalCliente'], ['aCodPersonaCliente']);" />
					<input type="hidden" name="fDocFiscalCliente" id="fDocFiscalCliente" value="<?=$fDocFiscalCliente?>">
					<input type="hidden" name="fCodPersonaCliente" id="fCodPersonaCliente" value="<?=$fCodPersonaCliente?>" />
					<input type="text" name="fNombreCliente" id="fNombreCliente" value="<?=htmlentities($fNombreCliente)?>" style="width:225px;" readonly />
					<a href="../lib/listas/gehen.php?anz=lista_personas&campo1=fCodPersonaCliente&campo2=fNombreCliente&campo3=fDocFiscalCliente&ventana=filtro&filtrar=default&FlagClasePersona=S&fEsCliente=S&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" style=" <?=$dCodPersonaCliente?>" id="aCodPersonaCliente">
		            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
		            </a>
				</td>
				<td align="right">Ver: </td>
				<td>
					<input type="checkbox" <?=$cVer?> onclick="this.checked=!this.checked;" />
		            <select name="fVer" id="fVer" style="width:151px;" <?=$dVer?>>
		                <?=loadSelectValores("documento-pagos", $fVer)?>
		            </select>
				</td>
				<td align="right">Buscar:</td>
				<td>
					<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
					<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:100px;" <?=$dBuscar?> />
				</td>
		        <td align="right"><input type="submit" value="Buscar"></td>
			</tr>
		</table>
	</div>
	<div class="sep"></div>

	<!--CABECERA-->
	<input type="hidden" name="sel_registros" id="sel_registros" />
	<table class="tblBotones" style="width:100%; min-width:<?=$_width?>px;">
	    <tr>
	        <td></td>
	        <td align="right">
	            <input type="button" value="Nuevo" style="width:75px; <?=$_btNuevo?>" class="insert" onclick="cargarPagina(this.form, 'gehen.php?anz=co_documento_form&opcion=nuevo-adelanto&origen=co_documentoadelanto_lista');" />
	        </td>
	    </tr>
	</table>
	<div style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:225px;">
		<table class="tblLista" style="width:100%; min-width:1000px;">
			<thead>
			    <tr>
			        <th width="125">Tipo</th>
			        <th width="75">Número</th>
			        <th width="75">Fecha</th>
			        <th style="min-width: 200px;" align="left">Nombre del Cliente</th>
			        <th width="75">Fecha Pago</th>
			        <th width="100" align="right">Monto Total</th>
			        <th width="100" align="right">Monto Saldo</th>
			        <th width="100">Estado</th>
			    </tr>
		    </thead>
		    
		    <tbody id="lista_registros">
			<?php
			//	consulto lista
			$sql = "SELECT
						do.CodDocumento,
						do.CodTipoDocumento,
						do.NroDocumento,
						do.FechaDocumento,
						do.NombreCliente,

						do.MontoTotal,
						do.MontoAdelantoSaldo,
						do.Estado,
						td.Descripcion As TipoDocumento
					FROM co_documento do
					INNER JOIN co_tipodocumento td ON td.CodTipoDocumento = do.CodTipoDocumento
					WHERE do.CodTipoDocumento = '$_PARAMETRO[CODOCADE]' $filtro
					ORDER BY NroDocumento";
			$field = getRecords($sql);
			$rows_lista = count($field);
			foreach($field as $f) {
				$id = $f['CodDocumento'];
				?>
				<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>'); get_documento_detalle('<?=$f['CodDocumento']?>');">
					<td><?=htmlentities($f['TipoDocumento'])?></td>
					<td align="center"><?=$f['NroDocumento']?></td>
					<td align="center"><?=formatFechaAMD($f['FechaDocumento'])?></td>
					<td><?=htmlentities($f['NombreCliente'])?></td>
					<td align="center"><?=formatFechaAMD($f['FechaDocumento'])?></td>
					<td align="right"><?=number_format($f['MontoTotal'],2,',','.')?></td>
					<td align="right"><?=number_format($f['MontoAdelantoSaldo'],2,',','.')?></td>
					<td align="center"><?=printValores('documento2-estado',$f['Estado'])?></td>
				</tr>
				<?php
			}
			?>
		    </tbody>
		</table>
	</div>

	<!--DETALLE-->
	<table class="tblBotones" style="width:100%; min-width:<?=$_width?>px;">
	    <tr>
	        <td></td>
	        <td align="right"></td>
	    </tr>
	</table>
	<div style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:225px;">
		<table class="tblLista" style="width:100%; min-width:800px;">
			<thead>
			    <tr>
			        <th width="35">#</th>
			        <th width="60">Número</th>
			        <th style="min-width: 300px;" align="left">Cajero</th>
			        <th width="100" align="right">Monto</th>
			        <th width="200">Documento Relacionado</th>
			    </tr>
		    </thead>
		    
		    <tbody id="lista_adelanto">
		    </tbody>
		</table>
	</div>
</form>
<script type="text/javascript">
	function get_documento_detalle(CodDocumento) {
		$('#lista_detalle').html('Cargando...');
		$.post('co_documentoadelanto_ajax.php', "modulo=ajax&accion=adelanto_detalle&CodDocumento="+CodDocumento, function(data) {
			$('#lista_adelanto').html(data);
	    });
	}
</script>