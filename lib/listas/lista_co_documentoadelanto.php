<?php
if (empty($ventana)) $ventana = "selLista";
if (empty($FlagCobranza)) $FlagCobranza = "N";
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$fFechaDocumentoD = formatFechaDMA($PeriodoActual.'-01');
	$fFechaDocumentoH = formatFechaDMA($FechaActual);
	$fVer = 'PP';
	##	
	$sql = "SELECT * FROM mastpersonas WHERE CodPersona = '$fCodPersonaCliente'";
	$field_cliente = getRecord($sql);
	$fDocFiscalCliente = $field_cliente['DocFiscal'];
	$fNombreCliente = $field_cliente['NomCompleto'];
	##	
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "CodTipoDocumento,NroDocumento";
}
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
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lista_co_documentoadelanto" method="post">
	<input type="hidden" name="registro" id="registro" />
	<input type="hidden" name="campo1" id="campo1" value="<?=$campo1?>" />
	<input type="hidden" name="campo2" id="campo2" value="<?=$campo2?>" />
	<input type="hidden" name="campo3" id="campo3" value="<?=$campo3?>" />
	<input type="hidden" name="campo4" id="campo4" value="<?=$campo4?>" />
	<input type="hidden" name="campo5" id="campo5" value="<?=$campo5?>" />
	<input type="hidden" name="campo6" id="campo6" value="<?=$campo6?>" />
	<input type="hidden" name="campo7" id="campo7" value="<?=$campo7?>" />
	<input type="hidden" name="campo8" id="campo8" value="<?=$campo8?>" />
	<input type="hidden" name="campo9" id="campo9" value="<?=$campo9?>" />
	<input type="hidden" name="campo10" id="campo10" value="<?=$campo10?>" />
	<input type="hidden" name="ventana" id="ventana" value="<?=$ventana?>" />
	<input type="hidden" name="detalle" id="detalle" value="<?=$detalle?>" />
	<input type="hidden" name="modulo" id="modulo" value="<?=$modulo?>" />
	<input type="hidden" name="accion" id="accion" value="<?=$accion?>" />
	<input type="hidden" name="url" id="url" value="<?=$url?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
	<input type="hidden" name="FlagCobranza" id="FlagCobranza" value="<?=$FlagCobranza?>" />

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
		                <?=loadSelectGeneral("co-documento3-estado", $fEstado)?>
		            </select>
				</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td align="right">Cliente:</td>
				<td class="gallery clearfix">
					<?php if ($FlagCobranza == 'S') { ?>
						<input type="checkbox" <?=$cCodPersonaCliente?> onclick="this.checked=!this.checked;" />
						<input type="hidden" name="fDocFiscalCliente" id="fDocFiscalCliente" value="<?=$fDocFiscalCliente?>">
						<input type="hidden" name="fCodPersonaCliente" id="fCodPersonaCliente" value="<?=$fCodPersonaCliente?>" />
						<input type="text" name="fNombreCliente" id="fNombreCliente" value="<?=$fNombreCliente?>" style="width:225px;" readonly />
					<?php } else { ?>
						<input type="checkbox" <?=$cCodPersonaCliente?> onclick="ckLista(this.checked, ['fCodPersonaCliente','fNombreCliente','fDocFiscalCliente'], ['aCodPersonaCliente']);" />
						<input type="hidden" name="fDocFiscalCliente" id="fDocFiscalCliente" value="<?=$fDocFiscalCliente?>">
						<input type="hidden" name="fCodPersonaCliente" id="fCodPersonaCliente" value="<?=$fCodPersonaCliente?>" />
						<input type="text" name="fNombreCliente" id="fNombreCliente" value="<?=$fNombreCliente?>" style="width:225px;" readonly />
			            <a href="javascript:" onclick="window.open('gehen.php?anz=lista_personas&campo1=fCodPersonaCliente&campo2=fNombreCliente&campo3=fDocFiscalCliente&ventana=selListaOpener&filtrar=default&FlagClasePersona=S&fEsCliente=S','lista_personas','width=950, height=430, toolbar=no, menubar=no, location=no, scrollbars=yes, left=0, top=0, resizable=no')" style=" <?=$dCodPersonaCliente?>" id="aCodPersonaCliente">
			            	<img src="../../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
			            </a>
					<?php } ?>
				</td>
				<td align="right">Ver: </td>
				<td>
					<input type="checkbox" <?=$cVer?> onclick="this.checked=!this.checked;" />
		            <select name="fVer" id="fVer" style="width:151px;" <?=$dVer?>>
		                <?=loadSelectGeneral("co-documento-pagos", $fVer)?>
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
	<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px;">
		<table class="tblLista" style="width:100%; min-width:1000px;">
			<thead>
			    <tr>
			        <th width="125" onclick="order('CodTipoDocumento')">Tipo</th>
			        <th width="75" onclick="order('CodTipoDocumento')">Número</th>
			        <th width="75" onclick="order('CodTipoDocumento')">Fecha</th>
			        <th style="min-width: 200px;" align="left" onclick="order('CodTipoDocumento')">Nombre del Cliente</th>
			        <th width="75" onclick="order('CodTipoDocumento')">Fecha Pago</th>
			        <th width="100" align="right" onclick="order('CodTipoDocumento')">Monto Total</th>
			        <th width="100" align="right" onclick="order('CodTipoDocumento')">Monto Saldo</th>
			        <th width="100" onclick="order('CodTipoDocumento')">Estado</th>
			    </tr>
		    </thead>
		    
		    <tbody>
			<?php
			//	consulto todos
			$sql = "SELECT *
					FROM co_documento do
					INNER JOIN co_tipodocumento td ON td.CodTipoDocumento = do.CodTipoDocumento
					WHERE do.CodTipoDocumento = '$_PARAMETRO[CODOCADE]' $filtro";
			$rows_total = getNumRows3($sql);
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
					ORDER BY $fOrderBy
					LIMIT ".intval($limit).", ".intval($maxlimit);
			$field = getRecords($sql);
			$rows_lista = count($field);
			foreach($field as $f) {
				$id = $f['CodDocumento'];
				if ($ventana == 'listado_insertar_linea') {
					?>
		            <tr class="trListaBody" onClick="listado_insertar_linea('<?=$detalle?>','modulo=<?=$modulo?>&accion=<?=$accion?>&CodDocumento=<?=$f['CodDocumento']?>','<?=$f['CodDocumento']?>','<?=$url?>');">
		            <?php
				}
				elseif ($ventana == 'listado_insertar_linea_cobranza') {
					?>
		            <tr class="trListaBody" onClick="<?=$ventana?>('<?=$detalle?>','modulo=<?=$modulo?>&accion=<?=$accion?>&CodDocumento=<?=$f['CodDocumento']?>','<?=$f['CodDocumento']?>','<?=$url?>');" id="documento_<?=$id?>">
		            <?php
				}
				else {
					?>
		            <tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['CodDocumento']?>','<?=$f['Descripcion']?>'], ['<?=$campo1?>','<?=$campo2?>']);">
		            <?php
				}
				?>
					<td><?=htmlentities($f['TipoDocumento'])?></td>
					<td align="center"><?=$f['NroDocumento']?></td>
					<td align="center"><?=formatFechaAMD($f['FechaDocumento'])?></td>
					<td><?=htmlentities($f['NombreCliente'])?></td>
					<td align="center"><?=formatFechaAMD($f['FechaDocumento'])?></td>
					<td align="right"><?=number_format($f['MontoTotal'],2,',','.')?></td>
					<td align="right"><?=number_format($f['MontoAdelantoSaldo'],2,',','.')?></td>
					<td align="center"><?=printValoresGeneral('co-documento2-estado',$f['Estado'])?></td>
				</tr>
				<?php
			}
			?>
		    </tbody>
		</table>
	</div>
	<table style="width:100%; min-width:<?=$_width?>px;">
		<tr>
	    	<td>
	        	Mostrar: 
	            <select name="maxlimit" style="width:50px;" onchange="this.form.submit();">
	                <?=loadSelectGeneral("MAXLIMIT", $maxlimit, 0)?>
	            </select>
	        </td>
	        <td align="right">
	        	<?=paginacion(intval($rows_total), intval($rows_lista), intval($maxlimit), intval($limit));?>
	        </td>
	    </tr>
	</table>
</form>

<script type="text/javascript">
	function listado_insertar_linea_cobranza(detalle, data, id, url) {
		var nro_detalles = parent.$("#nro_"+detalle);
		var can_detalles = parent.$("#can_"+detalle);
		var lista_detalles = parent.$("#lista_"+detalle);
		var nro = new Number(nro_detalles.val());	nro++;
		var can = new Number(can_detalles.val());	can++;
		if (!id) var idtr = detalle+"_"+nro; else var idtr = detalle+"_"+id;
		//	ajax
		$.ajax({
			type: "POST",
			url: url,
			data: "nro_detalles="+nro+"&can_detalles="+can+"&"+data,
			async: false,
			success: function(resp) {
				if (parent.document.getElementById(idtr)) cajaModal("Registro ya insertado", "error_lista", 400);
				else {
					nro_detalles.val(nro);
					can_detalles.val(can);
					lista_detalles.append(resp);
					inicializarParent();
					parent.setMontosDocumentos();
					parent.$.prettyPhoto.close();
				}
			}
		});
	}
</script>