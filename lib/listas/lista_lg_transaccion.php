<?php
if (empty($ventana)) $ventana = "selLista";
if (empty($FlagOrganismo)) $FlagOrganismo = "N";
if (empty($FlagDocumento)) $FlagDocumento = "N";
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fEstado = "CO";
	$fFechaDocumentod = date('01-m-Y');
	$fFechaDocumentoh = formatFechaDMA($FechaActual);
	$fOrderBy = "CodDocumento,NroInterno";
}
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (t.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fBuscar != "") { 
	$cBuscar = "checked"; 
	$filtro.=" AND (t.CodDocumento LIKE '%".$fBuscar."%' OR 
					t.NroInterno LIKE '%".$fBuscar."%' OR 
					t.CodTransaccion LIKE '%".$fBuscar."%' OR 
					tt.Descripcion LIKE '%".$fBuscar."%' OR 
					a.Descripcion LIKE '%".$fBuscar."%' OR 
					t.CodCentroCosto LIKE '%".$fBuscar."%' OR 
					t.Periodo LIKE '%".$fBuscar."%' OR 
					t.CodDocumentoReferencia LIKE '%".$fBuscar."%' OR 
					t.NroDocumentoReferencia LIKE '%".$fBuscar."%' OR 
					t.DocumentoReferenciaInterno LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fCodDependencia != "") { $cCodDependencia = "checked"; $filtro.=" AND (t.CodDependencia = '".$fCodDependencia."')"; } else $dCodDependencia = "disabled";
if ($fFechaDocumentod != "" || $fFechaDocumentoh != "") {
	$cFechaDocumento = "checked";
	if ($fFechaDocumentod != "") $filtro.=" AND (t.FechaDocumento >= '".formatFechaAMD($fFechaDocumentod)."')";
	if ($fFechaDocumentoh != "") $filtro.=" AND (t.FechaDocumento <= '".formatFechaAMD($fFechaDocumentoh)."')";
} else $dFechaDocumento = "disabled";
if ($fCodCentroCosto != "") { $cCodCentroCosto = "checked"; $filtro.=" AND (t.CodCentroCosto = '".$fCodCentroCosto."')"; } else $dCodCentroCosto = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (t.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fCodTransaccion != "") { $cCodTransaccion = "checked"; $filtro.=" AND (t.CodTransaccion = '".$fCodTransaccion."')"; } else $dCodTransaccion = "visibility:hidden;";
if ($fCodDocumento != "") { 
	$cCodDocumento = "checked";
	if ($fCodDocumento != "") $filtro.=" AND (t.CodDocumento = '".$fCodDocumento."')";
	if ($fNroInterno != "") $filtro.=" AND (t.NroInterno = '".$fNroInterno."')";
} else $dCodDocumento = "disabled";
if ($fPeriodo != "") { $cPeriodo = "checked"; $filtro.=" AND (t.Periodo = '".$fPeriodo."')"; } else $dPeriodo = "disabled";
if ($fCodDocumentoReferencia != "" || $fNroDocumentoReferencia != "") { 
	$cCodDocumentoReferencia = "checked";
	if ($fCodDocumentoReferencia != "") $filtro.=" AND (t.CodDocumentoReferencia = '".$fCodDocumentoReferencia."')";
	if ($fNroDocumentoReferencia != "") $filtro.=" AND (t.NroDocumentoReferencia = '".$fNroDocumentoReferencia."')";
} else $dCodDocumentoReferencia = "disabled";
//	------------------------------------
$_titulo = "Listado de Transacciones";
$_width = 900;
?>
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lista_lg_transaccion" method="post">
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
	<input type="hidden" name="FlagOrganismo" id="FlagOrganismo" value="<?=$FlagOrganismo?>" />
	<input type="hidden" name="FlagDocumento" id="FlagDocumento" value="<?=$FlagDocumento?>" />

	<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
		<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
			<tr>
				<td align="right">Organismo:</td>
				<td>
					<input type="checkbox" <?=$cCodOrganismo?> onclick="this.checked=!this.checked" />
					<?php if ($FlagOrganismo != 'S') { ?>
						<select name="fCodOrganismo" id="fCodOrganismo" style="width:275px;" onchange="getOptionsSelect(this.value, 'dependencia', 'fCodDependencia', true, 'fCodCentroCosto');" <?=$dCodOrganismo?>>
							<?=getOrganismos($fCodOrganismo, 3)?>
						</select>
					<?php } else { ?>
						<select name="fCodOrganismo" id="fCodOrganismo" style="width:275px;" onchange="getOptionsSelect(this.value, 'dependencia', 'fCodDependencia', true, 'fCodCentroCosto');" <?=$dCodOrganismo?>>
							<?=loadSelect2('mastorganismos','CodOrganismo','Organismo',1)?>
						</select>
					<?php } ?>
				</td>
				<td align="right">Buscar:</td>
				<td>
					<input type="checkbox" <?=$cBuscar?> onclick="chkFiltro(this.checked, 'fBuscar');" />
					<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:131px;" <?=$dBuscar?> />
				</td>
				<td></td>
			</tr>
		    <tr>
				<td align="right">Dependencia:</td>
				<td>
					<input type="checkbox" <?=$cCodDependencia?> onclick="chkFiltro(this.checked, 'fCodDependencia')" />
					<select name="fCodDependencia" id="fCodDependencia" style="width:275px;" onchange="getOptionsSelect(this.value, 'centro_costo', 'fCodCentroCosto', true);" <?=$dCodDependencia?>>
						<option value="">&nbsp;</option>
						<?=getDependencias($fCodDependencia, $fCodOrganismo, 3);?>
					</select>
				</td>
				<td align="right">F.Documento: </td>
				<td>
					<input type="checkbox" <?=$cFechaDocumento?> onclick="chkFiltro_2(this.checked, 'fFechaDocumentod', 'fFechaDocumentoh');" />
					<input type="text" name="fFechaDocumentod" id="fFechaDocumentod" value="<?=$fFechaDocumentod?>" <?=$dFechaDocumento?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" /> -
		            <input type="text" name="fFechaDocumentoh" id="fFechaDocumentoh" value="<?=$fFechaDocumentoh?>" <?=$dFechaDocumento?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" />
		        </td>
				<td></td>
			</tr>
			<tr>
				<td align="right">Centro de Costo:</td>
				<td>
					<input type="checkbox" <?=$cCodCentroCosto?> onclick="chkFiltro(this.checked, 'fCodCentroCosto')" />
					<select name="fCodCentroCosto" id="fCodCentroCosto" style="width:275px;" <?=$dCodCentroCosto?>>
						<option value="">&nbsp;</option>
						<?=loadSelectDependiente("ac_mastcentrocosto", "CodCentroCosto", "Descripcion", "CodDependencia", $fCodCentroCosto, $fCodDependencia, 0)?>
					</select>
				</td>
				<td align="right">Estado:</td>
				<td>
					<input type="checkbox" onclick="this.checked=!this.checked;" checked="checked" />
	                <select name="fEstado" id="fEstado" style="width:131px;">
	                    <?=loadSelectGeneral("ESTADO-TRANSACCION", $fEstado, 1)?>
	                </select>
				</td>
				<td></td>
			</tr>
		    <tr>
				<td align="right">Tipo de Transaccion: </td>
				<td class="gallery clearfix">
		            <input type="checkbox" <?=$cCodTransaccion?> onclick="chkFiltroLista_3(this.checked, 'fCodTransaccion', 'fNomTransaccion', '', 'btTransaccion');" />
		            <input type="text" name="fCodTransaccion" id="fCodTransaccion" style="width:50px;" class="disabled" value="<?=$fCodTransaccion?>" readonly="readonly" />
					<input type="text" name="fNomTransaccion" id="fNomTransaccion" style="width:222px;" class="disabled" value="<?=$fNomTransaccion?>" readonly="readonly" />
		            <a href="../lib/listas/listado_tipo_transacciones.php?filtrar=default&cod=fCodTransaccion&nom=fNomTransaccion&iframe=true&width=950&height=525" rel="prettyPhoto[iframe1]" id="btTransaccion" style=" <?=$dCodTransaccion?>">
		            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
		            </a>
		        </td>
				<td align="right">Doc. Generado:</td>
		        <td>
					<?php if ($FlagDocumento != 'S') { ?>
			            <input type="checkbox" <?=$cCodDocumento?> onclick="chkFiltro_2(this.checked, 'fCodDocumento', 'fNroInterno');" />
			        	<select name="fCodDocumento" id="fCodDocumento" style="width:42px;" <?=$dCodDocumento?>>
			            	<option value="">&nbsp;</option>
							<?=loadSelect("lg_tipodocumento", "CodDocumento", "Descripcion", $fCodDocumento, 10);?>
						</select>
					<?php } else { ?>
			            <input type="checkbox" <?=$cCodDocumento?> onclick="this.checked=!this.checked;" />
			        	<select name="fCodDocumento" id="fCodDocumento" style="width:42px;" <?=$dCodDocumento?>>
							<?=loadSelect("lg_tipodocumento","CodDocumento","Descripcion",$fCodDocumento,11)?>
						</select>
					<?php } ?>
					<input type="text" name="fNroInterno" id="fNroInterno" maxlength="20" style="width:86px;" value="<?=$fNroInterno?>" <?=$dCodDocumento?> />
		        </td>
				<td></td>
			</tr>
			<tr>
				<td align="right">Periodo:</td>
				<td>
					<input type="checkbox" <?=$cPeriodo?> onclick="chkFiltro(this.checked, 'fPeriodo');" />
					<input type="text" name="fPeriodo" id="fPeriodo" value="<?=$fPeriodo?>" maxlength="7" style="width:50px;" <?=$dPeriodo?> />
				</td>
				<td align="right">Doc. Referencia:</td>
		        <td>
		            <input type="checkbox" <?=$cCodDocumentoReferencia?> onclick="chkFiltro_2(this.checked, 'fCodDocumentoReferencia', 'fNroDocumentoReferencia');" />
		        	<select name="fCodDocumentoReferencia" id="fCodDocumentoReferencia" style="width:42px;" <?=$dCodDocumentoReferencia?>>
		            	<option value="">&nbsp;</option>
						<?=loadSelect("lg_tipodocumento", "CodDocumento", "Descripcion", $fCodDocumentoReferencia, 10);?>
					</select>
		            <input type="text" name="fNroDocumentoReferencia" id="fNroDocumentoReferencia" maxlength="20" style="width:86px;" value="<?=$fNroDocumentoReferencia?>" <?=$dCodDocumentoReferencia?> />
		        </td>
		        <td align="right"><input type="submit" value="Buscar"></td>
			</tr>
		</table>
	</div>
	<div class="sep"></div>
	<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px;">
		<table class="tblLista" style="width:100%; min-width:<?=$_width?>px;">
			<thead>
			    <tr>
					<th colspan="2" onclick="order('CodDocumento,NroInterno')">Doc. Generado</th>
					<th width="65" onclick="order('FechaDocumento')">Fecha Doc.</th>
					<th colspan="2" onclick="order('CodTransaccion')">Transacci&oacute;n</th>
					<th width="175" onclick="order('NomTransaccion')">Almac&eacute;n</th>
					<th width="35" onclick="order('CentroCosto')">C.C.</th>
					<th width="60" onclick="order('Periodo')">Periodo</th>
					<th width="75" onclick="order('Estado')">Estado</th>
					<th colspan="2" onclick="order('CodDocumentoReferencia,NroDocumentoReferencia')">Doc. Referencia</th>
					<th width="125" onclick="order('DocumentoReferenciaInterno')">Doc. Ref. Interno</th>
			    </tr>
		    </thead>
		    
		    <tbody>
			<?php
			//	consulto todos
			$sql = "SELECT *
					FROM lg_transaccion t 
					INNER JOIN lg_almacenmast a ON (a.CodAlmacen = t.CodAlmacen AND a.FlagCommodity = 'N')
					INNER JOIN lg_tipotransaccion tt ON (t.CodTransaccion = tt.CodTransaccion)
					WHERE 1 $filtro";
			$rows_total = getNumRows3($sql);
			//	consulto lista
			$sql = "SELECT
						t.CodOrganismo,
						t.CodDocumento,
						t.NroDocumento,
						t.NroInterno,
						t.FechaDocumento,
						t.CodTransaccion,
						t.CodCentroCosto,
						t.Periodo,
						t.Estado,
						t.CodDocumentoReferencia,
						t.NroDocumentoReferencia,
						t.DocumentoReferenciaInterno,
						t.FlagDocumentoFiscal,
						a.Descripcion AS NomAlmacen,
						a.Direccion AS AlmacenDireccion,
						a.CodAlmacen,
						tt.Descripcion AS NomTransaccion,
						tt.TipoMovimiento,
						cc.Codigo AS CentroCosto
					FROM lg_transaccion t 
					INNER JOIN lg_almacenmast a ON (a.CodAlmacen = t.CodAlmacen AND a.FlagCommodity = 'N')
					INNER JOIN lg_tipotransaccion tt ON (t.CodTransaccion = tt.CodTransaccion)
					INNER JOIN ac_mastcentrocosto cc On cc.CodCentroCosto = t.CodCentroCosto
					WHERE 1 $filtro
					ORDER BY $fOrderBy
					LIMIT ".intval($limit).", ".intval($maxlimit);
			$field = getRecords($sql);
			$rows_lista = count($field);
			foreach($field as $f) {
				$id = "$f[CodOrganismo].$f[CodDocumento].$f[NroDocumento].$f[TipoMovimiento]";
				if ($ventana == 'listado_insertar_linea') {
				}
				elseif ($ventana == 'lg_guiaremision') {
					?>
		            <tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['CodOrganismo']?>','<?=$f['CodDocumento']?>','<?=$f['NroDocumento']?>','<?=$f['CodTransaccion']?>','<?=htmlentities($f['NomTransaccion'])?>','<?=$f['CodAlmacen']?>','<?=htmlentities($f['AlmacenDireccion'])?>','<?=$f['CodDocumentoReferencia']?><?=$f['NroDocumentoReferencia']?>','<?=formatFechaDMA($f['FechaDocumento'])?>','<?=formatFechaDMA($f['FechaDocumento'])?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>','<?=$campo4?>','<?=$campo5?>','<?=$campo6?>','<?=$campo7?>','<?=$campo8?>','<?=$campo9?>','<?=$campo10?>'], '<?=$f['FlagDocumentoFiscal']?>');">
		            <?php
				}
				else {
					?>
		            <tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['CodOrganismo']?>','<?=$f['CodDocumento']?>','<?=$f['NroDocumento']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>']);">
		            <?php
				}
				?>
		            <td align="center" width="10"><?=$f['CodDocumento']?></td>
		            <td width="40"><?=$f['NroInterno']?></td>
		            <td align="center"><?=formatFechaDMA($f['FechaDocumento'])?></td>
		            <td align="center" width="25"><?=$f['CodTransaccion']?></td>
		            <td><?=htmlentities($f['NomTransaccion'])?></td>
		            <td align="center"><?=htmlentities($f['NomAlmacen'])?></td>
		            <td align="center"><?=$f['CentroCosto']?></td>
		            <td align="center"><?=$f['Periodo']?></td>
		            <td align="center"><?=printValoresGeneral("ESTADO-TRANSACCION", $f['Estado'])?></td>
		            <td align="center" width="10"><?=$f['CodDocumentoReferencia']?></td>
		            <td width="115"><?=$f['NroDocumentoReferencia']?></td>
		            <td><?=$f['DocumentoReferenciaInterno']?></td>
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
	function lg_guiaremision(valores, inputs, FlagDocumentoFiscal) {
		$.post('../../lg/lg_guiaremision_ajax.php', 'modulo=ajax&accion=detalle_transaccion&CodOrganismo='+valores[0]+'&CodDocumento='+valores[1]+'&NroDocumento='+valores[2], function(data) {
			parent.$('#lista_detalle').html(data);
			//	
			if (inputs) {
				for(var i=0; i<inputs.length; i++) {
					if (parent.$("#"+inputs[i]).length > 0) parent.$("#"+inputs[i]).val(valores[i]);
				}
			}
			if (FlagDocumentoFiscal == 'S') {
				parent.$('#EstadoFacturacion').val('FA');
				parent.$('#NomEstadoFacturacion').val('FACTURADO');
			}
			parent.$.prettyPhoto.close();
	    });
	}
</script>