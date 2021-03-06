<?php
if (empty($ventana)) $ventana = "selLista";
//	------------------------------------
if ($filtrar == "default") {
	$fEstado = 'AP';
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$fFechaDocumentoD = formatFechaDMA($FechaActual);
	$fFechaDocumentoH = formatFechaDMA($FechaActual);
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "FechaDocumento,NroDocumento";
}
//	------------------------------------
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (do.NroDocumento LIKE '%$fBuscar%'
					  OR do.NombreCliente LIKE '%$fBuscar%'
					  OR do.Comentarios LIKE '%$fBuscar%'
					  OR md1.Descripcion LIKE '%$fBuscar%'
					  OR md2.Descripcion LIKE '%$fBuscar%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (do.Estado = '$fEstado')"; } else $dEstado = "disabled";
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (do.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fCodEstablecimiento != "") { $cCodEstablecimiento = "checked"; $filtro.=" AND (do.CodEstablecimiento = '".$fCodEstablecimiento."')"; } else $dCodEstablecimiento = "disabled";
if ($fFechaDocumentoD != "" || $fFechaDocumentoH != "") {
	$cFechaDocumento = "checked";
	if ($fFechaDocumentoD != "") $filtro.=" AND (do.FechaDocumento >= '".formatFechaAMD($fFechaDocumentoD)."')";
	if ($fFechaDocumentoH != "") $filtro.=" AND (do.FechaDocumento <= '".formatFechaAMD($fFechaDocumentoH)."')";
} else $dFechaDocumento = "disabled";
if ($fCodPersonaCliente != "") { $cCodPersonaCliente = "checked"; $filtro.=" AND (do.CodPersonaCliente = '".$fCodPersonaCliente."')"; } else $dCodPersonaCliente = "visibility:hidden;";
//	------------------------------------
$_titulo = "Pedidos";
$_width = 900;
?>
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lista_co_pedidos" method="post">
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

	<!--FILTRO-->
	<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
		<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
			<tr>
				<td align="right" width="100">Organismo:</td>
				<td>
					<input type="checkbox" <?=$cCodOrganismo?> onclick="this.checked=!this.checked;" />
					<select name="fCodOrganismo" id="fCodOrganismo" style="width:225px;" <?=$dCodOrganismo?>>
						<?=getOrganismos($fCodOrganismo, 3);?>
					</select>
				</td>
				<td align="right" width="100">Cliente:</td>
				<td class="gallery clearfix">
					<input type="checkbox" <?=$cCodPersonaCliente?> onclick="ckLista(this.checked, ['fCodPersonaCliente','fNombreCliente','fDocFiscalCliente'], ['aCodPersonaCliente']);" />
					<input type="hidden" name="fDocFiscalCliente" id="fDocFiscalCliente" value="<?=$fDocFiscalCliente?>">
					<input type="hidden" name="fCodPersonaCliente" id="fCodPersonaCliente" value="<?=$fCodPersonaCliente?>" />
					<input type="text" name="fNombreCliente" id="fNombreCliente" value="<?=$fNombreCliente?>" style="width:225px;" readonly />
		            <a href="javascript:" onclick="window.open('gehen.php?anz=lista_personas&campo1=fCodPersonaCliente&campo2=fNombreCliente&campo3=fDocFiscalCliente&ventana=selListaOpener&filtrar=default&FlagClasePersona=S&fEsCliente=S','lista_at_linea','width=950, height=430, toolbar=no, menubar=no, location=no, scrollbars=yes, left=0, top=0, resizable=no')" style=" <?=$dCodPersonaCliente?>" id="aCodPersonaCliente">
		            	<img src="../../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
		            </a>
				</td>
				<td align="right" width="100">Buscar:</td>
				<td>
					<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
					<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:100px;" <?=$dBuscar?> />
				</td>
			</tr>
			<tr>
				<td align="right">Establecimiento:</td>
				<td>
					<input type="checkbox" <?=$cCodEstablecimiento?> onclick="chkFiltro(this.checked, 'fCodEstablecimiento');" />
					<select name="fCodEstablecimiento" id="fCodEstablecimiento" style="width:225px;" <?=$dCodEstablecimiento?>>
						<option value="">&nbsp;</option>
						<?=loadSelect2('co_establecimientofiscal','CodEstablecimiento','Descripcion',$fCodEstablecimiento)?>
					</select>
				</td>
				<td align="right">Fecha:</td>
				<td>
					<input type="checkbox" <?=$cFechaDocumento?> onclick="chkCampos2(this.checked, ['fFechaDocumentoD','fFechaDocumentoH']);" />
					<input type="text" name="fFechaDocumentoD" id="fFechaDocumentoD" value="<?=$fFechaDocumentoD?>" <?=$dFechaDocumento?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" /> -
		            <input type="text" name="fFechaDocumentoH" id="fFechaDocumentoH" value="<?=$fFechaDocumentoH?>" <?=$dFechaDocumento?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" />
		        </td>
				<td align="right">Estado: </td>
				<td>
		            <input type="checkbox" <?=$cEstado?> onclick="this.checked=!this.checked;" />
		            <select name="fEstado" id="fEstado" style="width:100px;" <?=$dEstado?>>
		                <?=loadSelectGeneral("co-documento1-estado", $fEstado, 1)?>
		            </select>
				</td>
		        <td align="right"><input type="submit" value="Buscar"></td>
			</tr>
		</table>
	</div>
	<div class="sep"></div>

	<!--REGISTROS-->
	<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px;">
		<table class="tblLista" style="width:100%; min-width:1950px;">
			<thead>
			    <tr>
			        <th style="min-width: 200px;" align="left" onclick="order('Establecimiento')">Establecimiento</th>
			        <th width="75" onclick="order('NroDocumento')">N&uacute;mero</th>
			        <th width="75" onclick="order('FechaDocumento,NroDocumento')">Fecha</th>
			        <th style="min-width: 200px;" align="left" onclick="order('NombreCliente')">Nombre del Cliente</th>
			        <th width="150" onclick="order('MontoTotal')">Monto Total</th>
			        <th style="min-width: 400px;" align="left" onclick="order('Comentarios')">Comentarios</th>
			        <th width="75" onclick="order('FechaVencimiento')">Fecha Requerida</th>
			        <th width="75" onclick="order('FechaAprobado')">Fecha Aprobación</th>
			        <th width="75" onclick="order('NomFormaFactura')">Forma Facturación</th>
			        <th width="75" onclick="order('NomTipoVenta')">Tipo Venta</th>
			    </tr>
		    </thead>
		    
		    <tbody>
			<?php
			//	consulto todos
			$sql = "SELECT *
					FROM co_documento do
					INNER JOIN co_establecimientofiscal ef ON ef.CodEstablecimiento = do.CodEstablecimiento
					LEFT JOIN mastmiscelaneosdet md1 ON (
						md1.CodDetalle = do.FormaFactura
						AND md1.CodMaestro = 'FORMAFACT'
					)
					LEFT JOIN mastmiscelaneosdet md2 ON (
						md2.CodDetalle = do.FormaFactura
						AND md2.CodMaestro = 'TIPOVENTA'
					)
					WHERE
						do.FlagDocumento <> 'S'
						$filtro";
			$rows_total = getNumRows3($sql);
			//	consulto lista
			$sql = "SELECT
						do.*,
						ef.Descripcion AS Establecimiento,
						md1.Descripcion AS NomFormaFactura,
						md2.Descripcion AS NomTipoVenta
					FROM co_documento do
					INNER JOIN co_establecimientofiscal ef ON ef.CodEstablecimiento = do.CodEstablecimiento
					LEFT JOIN mastmiscelaneosdet md1 ON (
						md1.CodDetalle = do.FormaFactura
						AND md1.CodMaestro = 'FORMAFACT'
					)
					LEFT JOIN mastmiscelaneosdet md2 ON (
						md2.CodDetalle = do.TipoVenta
						AND md2.CodMaestro = 'TIPOVENTA'
					)
					WHERE
						do.FlagDocumento <> 'S'
						$filtro
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
				else {
					?>
		            <tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['CodDocumento']?>','<?=$f['CodTipoDocumento']?><?=$f['NroDocumento']?>'], ['<?=$campo1?>','<?=$campo2?>']);">
		            <?php

				}
				?>
					<td><?=htmlentities($f['Establecimiento'])?></td>
					<td align="center"><?=$f['NroDocumento']?></td>
					<td align="center"><?=formatFechaAMD($f['FechaDocumento'])?></td>
					<td><?=htmlentities($f['NombreCliente'])?></td>
					<td align="right"><?=number_format($f['MontoTotal'],2,',','.')?></td>
					<td><?=htmlentities($f['Comentarios'])?></td>
					<td align="center"><?=formatFechaAMD($f['FechaVencimiento'])?></td>
					<td align="center"><?=formatFechaAMD(substr($f['FechaAprobado'],0,10))?></td>
					<td align="center"><?=$f['NomFormaFactura']?></td>
					<td align="center"><?=$f['NomTipoVenta']?></td>
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