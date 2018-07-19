<?php
if (empty($ventana)) $ventana = "selLista";
//	------------------------------------
if ($filtrar == "default") {
	$fEstado = 'A';
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "CodTipoDocumento";
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (td.CodTipoDocumento LIKE '%$fBuscar%'
					  OR td.Descripcion LIKE '%$fBuscar%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (td.Estado = '$fEstado')"; } else $dEstado = "disabled";
//	------------------------------------
$_titulo = "Maestro de Items";
$_width = 900;
?>
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lista_co_tipodocumento" method="post">
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
	<input type="hidden" name="CodOrganismo" id="CodOrganismo" value="<?=$CodOrganismo?>" />
	<input type="hidden" name="CodEstablecimiento" id="CodEstablecimiento" value="<?=$CodEstablecimiento?>" />

	<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
		<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
			<tr>
				<td align="right" width="100">Buscar:</td>
				<td>
					<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
					<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:169px;" <?=$dBuscar?> />
				</td>
				<td align="right">Estado: </td>
				<td>
		            <input type="checkbox" <?=$cEstado?> onclick="this.checked=!this.checked;" />
		            <select name="fEstado" id="fEstado" style="width:100px;" <?=$dEstado?>>
		                <?=loadSelectGeneral("ESTADO", $fEstado, 1)?>
		            </select>
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
			        <th width="60" onclick="order('CodTipoDocumento')">C&oacute;digo</th>
			        <th style="min-width: 250px;" align="left" onclick="order('Descripcion')">Descripci&oacute;n</th>
			        <th width="40" onclick="order('FlagEsFiscal')">Fiscal</th>
			        <th width="40" onclick="order('FlagProvision')">Prov.</th>
			    </tr>
		    </thead>
		    
		    <tbody>
			<?php
			if (!empty($CodOrganismo) && !empty($CodEstablecimiento))
			{
				//	consulto todos
				$sql = "SELECT *
						FROM co_tipodocumento td
						LEFT JOIN co_fiscalautorizaciondet fad ON (
							fad.CodOrganismo = '$CodOrganismo'
							AND fad.CodTipoDocumento = td.CodTipoDocumento
						)
						LEFT JOIN co_seriefiscal sf ON (
							sf.CodSerie = fad.CodSerie
							AND sf.CodOrganismo = fad.CodOrganismo
							AND sf.CodEstablecimiento = '$CodEstablecimiento'
						)
						WHERE 1 $filtro";
				$rows_total = getNumRows3($sql);
				//	consulto lista
				$sql = "SELECT
							td.*,
							sf.CodSerie,
							sf.NroSerie
						FROM co_tipodocumento td
						LEFT JOIN co_fiscalautorizaciondet fad ON (
							fad.CodOrganismo = '$CodOrganismo'
							AND fad.CodTipoDocumento = td.CodTipoDocumento
						)
						LEFT JOIN co_seriefiscal sf ON (
							sf.CodSerie = fad.CodSerie
							AND sf.CodOrganismo = fad.CodOrganismo
							AND sf.CodEstablecimiento = '$CodEstablecimiento'
						)
						WHERE 1 $filtro
						ORDER BY $fOrderBy
						LIMIT ".intval($limit).", ".intval($maxlimit);
				$field = getRecords($sql);
				$rows_lista = count($field);	
			}
			elseif ($ventana == 'facturar')
			{
				//	consulto todos
				$sql = "SELECT *
						FROM co_tipodocumento td
						LEFT JOIN co_fiscalautorizaciondet fad ON (
							fad.CodOrganismo = '$CodOrganismo'
							AND fad.CodTipoDocumento = td.CodTipoDocumento
						)
						LEFT JOIN co_seriefiscal sf ON (
							sf.CodSerie = fad.CodSerie
							AND sf.CodOrganismo = fad.CodOrganismo
						)
						WHERE 1 $filtro";
				$rows_total = getNumRows3($sql);
				//	consulto lista
				$sql = "SELECT
							td.*,
							sf.CodSerie,
							sf.NroSerie
						FROM co_tipodocumento td
						LEFT JOIN co_fiscalautorizaciondet fad ON (
							fad.CodOrganismo = '$CodOrganismo'
							AND fad.CodTipoDocumento = td.CodTipoDocumento
						)
						LEFT JOIN co_seriefiscal sf ON (
							sf.CodSerie = fad.CodSerie
							AND sf.CodOrganismo = fad.CodOrganismo
						)
						WHERE 1 $filtro
						ORDER BY $fOrderBy
						LIMIT ".intval($limit).", ".intval($maxlimit);
				$field = getRecords($sql);
				$rows_lista = count($field);	
			}
			else
			{
				//	consulto todos
				$sql = "SELECT *
						FROM co_tipodocumento td
						LEFT JOIN co_autorizacionfiscaldet fad ON (
							fad.CodTipoDocumento = td.CodTipoDocumento
							AND fad.CodOrganismo = '$CodOrganismo'
						)
						WHERE 1 $filtro";
				$rows_total = getNumRows3($sql);
				//	consulto lista
				$sql = "SELECT *
						FROM co_tipodocumento td
						WHERE 1 $filtro
						ORDER BY $fOrderBy
						LIMIT ".intval($limit).", ".intval($maxlimit);
				$field = getRecords($sql);
				$rows_lista = count($field);
			}
			foreach($field as $f) {
				$id = $f['CodTipoDocumento'];
				if ($ventana == 'listado_insertar_linea') {
					?>
		            <tr class="trListaBody" onClick="listado_insertar_linea('<?=$detalle?>','modulo=<?=$modulo?>&accion=<?=$accion?>&CodTipoDocumento=<?=$f['CodTipoDocumento']?>','<?=$f['CodTipoDocumento']?>','<?=$url?>');">
		            <?php
				}
				elseif ($ventana == 'documento') {
					?>
		            <tr class="trListaBody" onClick="selListaDocumento(['<?=$f['CodTipoDocumento']?>','<?=$f['Descripcion']?>','<?=$f['CodSerie']?>','<?=$f['NroSerie']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>','<?=$campo4?>']);">
		            <?php
				}
				elseif ($ventana == 'facturar') {
					list($NroDocumento, $UltNroEmitido) = correlativo_documento($CodOrganismo, $f['CodTipoDocumento'], $f['NroSerie'], FALSE);
					?>
		            <tr class="trListaBody" onClick="selLista(['<?=$f['CodTipoDocumento']?>','<?=$f['Descripcion']?>','<?=$f['CodSerie']?>','<?=$f['NroSerie']?>','<?=$NroDocumento?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>','<?=$campo4?>','<?=$campo5?>']);">
		            <?php
				}
				else {
					?>
		            <tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['CodTipoDocumento']?>','<?=$f['Descripcion']?>'], ['<?=$campo1?>','<?=$campo2?>']);">
		            <?php
				}
				?>
					<td align="center"><?=$f['CodTipoDocumento']?></td>
					<td><?=htmlentities($f['Descripcion'])?></td>
					<td align="center"><?=printFlag2($f['FlagEsFiscal'])?></td>
					<td align="center"><?=printFlag2($f['FlagProvision'])?></td>
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
	<?php if ($ventana == 'documento') { ?>
		function selListaDocumento(valores, inputs) {
			if (inputs) {
				for(var i=0; i<inputs.length; i++) {
					if (parent.$("#"+inputs[i]).length > 0) parent.$("#"+inputs[i]).val(valores[i]);
				}
			}
			if (valores[0] == 'NC') {
				parent.$('#a_documento_original').css('visibility','visible');
			}
			else {
				parent.$('#a_documento_original').css('visibility','hidden');
			}
			parent.$('#DocOriginal').val('');
			parent.$('#NroOriginal').val('');
			parent.$('#lista_detalle').html('');
			parent.setMontosVentas();
			parent.$('#nro_detalle').val('0');
			parent.$('#can_detalle').val('0');
			parent.$.prettyPhoto.close();
		}
	<?php } ?>
</script>