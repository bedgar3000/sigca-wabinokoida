<?php
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$fFechaDocumentoD = formatFechaDMA($PeriodoActual.'-01');
	$fFechaDocumentoH = formatFechaDMA($FechaActual);
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "FechaDocumento";
}
//	------------------------------------
if ($lista == "listar") {
	$_titulo = "Lista de Cotizaciones";
	$_btNuevo = "";
	$_btModificar = "";
	$_btAprobar = "display:none;";
	$_btAnular = "";
}
elseif ($lista == "aprobar") {
	$fEstado = "PR";
	##	
	$_titulo = "Lista de Cotizaciones / Aprobar";
	$_btNuevo = "display:none;";
	$_btModificar = "display:none;";
	$_btAprobar = "";
	$_btAnular = "";
}
//	------------------------------------
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (c.NroCotizacion LIKE '%$fBuscar%'
					  OR c.DocFiscalCliente LIKE '%$fBuscar%'
					  OR c.NombreCliente LIKE '%$fBuscar%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (c.Estado = '$fEstado')"; } else $dEstado = "disabled";
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (c.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fCodEstablecimiento != "") { $cCodEstablecimiento = "checked"; $filtro.=" AND (c.CodEstablecimiento = '".$fCodEstablecimiento."')"; } else $dCodEstablecimiento = "disabled";
if ($fFechaDocumentoD != "" || $fFechaDocumentoH != "") {
	$cFechaDocumento = "checked";
	if ($fFechaDocumentoD != "") $filtro.=" AND (c.FechaDocumento >= '".formatFechaAMD($fFechaDocumentoD)."')";
	if ($fFechaDocumentoH != "") $filtro.=" AND (c.FechaDocumento <= '".formatFechaAMD($fFechaDocumentoH)."')";
} else $dFechaDocumento = "disabled";
if ($fCodPersonaCliente != "") { $cCodPersonaCliente = "checked"; $filtro.=" AND (c.CodPersonaCliente = '".$fCodPersonaCliente."')"; } else $dCodPersonaCliente = "visibility:hidden;";
//	------------------------------------
$_width = 700;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=co_cotizacion_lista" method="post" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
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
					<input type="text" name="fNombreCliente" id="fNombreCliente" value="<?=htmlentities($fNombreCliente)?>" style="width:225px;" readonly />
					<a href="../lib/listas/gehen.php?anz=lista_personas&campo1=fCodPersonaCliente&campo2=fNombreCliente&campo3=fDocFiscalCliente&ventana=&filtrar=default&FlagClasePersona=S&fEsCliente=S&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" style=" <?=$dCodPersonaCliente?>" id="aCodPersonaCliente">
		            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
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
					<?php if ($lista == 'listar') { ?>
			            <input type="checkbox" <?=$cEstado?> onclick="chkFiltro(this.checked, 'fEstado');" />
			            <select name="fEstado" id="fEstado" style="width:100px;" <?=$dEstado?>>
			                <option value="">&nbsp;</option>
			                <?=loadSelectValores("cotizacion-estado", $fEstado, 0)?>
			            </select>
					<?php } else { ?>
			            <input type="checkbox" <?=$cEstado?> onclick="this.checked=!this.checked;" />
			            <select name="fEstado" id="fEstado" style="width:100px;" <?=$dEstado?>>
			                <?=loadSelectValores("cotizacion-estado", $fEstado, 1)?>
			            </select>
					<?php } ?>
				</td>
		        <td align="right"><input type="submit" value="Buscar"></td>
			</tr>
		</table>
	</div>
	<div class="sep"></div>

	<!--REGISTROS-->
	<input type="hidden" name="sel_registros" id="sel_registros" />
	<table class="tblBotones" style="width:100%; min-width:<?=$_width?>px;">
	    <tr>
	        <td><div id="rows"></div></td>
	        <td align="right" class="gallery clearfix">
	        	<a href="pagina.php?iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe2]" style="display:none;" id="a_reporte"></a>

	            <input type="button" value="Nuevo" style="width:75px; <?=$_btNuevo?>" class="insert" onclick="cargarPagina(this.form, 'gehen.php?anz=co_cotizacion_form&opcion=nuevo&origen=co_cotizacion_lista');" />
            	<input type="button" value="Modificar" style="width:75px; <?=$_btModificar?>" class="update" onclick="cargarOpcionValidar3(this.form, $('#sel_registros').val(), 'co_cotizacion_ajax.php', 'modulo=validar&accion=modificar', 'gehen.php?anz=co_cotizacion_form&opcion=modificar', 'SELF', '');" />
	            <input type="button" value="Copiar" style="width:75px; <?=$_btNuevo?>" class="insert" onclick="cargarOpcion2(this.form, 'gehen.php?anz=co_cotizacion_form&opcion=copiar', 'SELF', '', $('#sel_registros').val());" />
	            <input type="button" value="Aprobar" style="width:75px; <?=$_btAprobar?>" class="update" onclick="cargarOpcionValidar3(this.form, $('#sel_registros').val(), 'co_cotizacion_ajax.php', 'modulo=validar&accion=aprobar', 'gehen.php?anz=co_cotizacion_form&opcion=aprobar', 'SELF', '');" />
	            <input type="button" value="Anular" style="width:75px; <?=$_btAnular?>" class="update" onclick="cargarOpcionValidar3(this.form, $('#sel_registros').val(), 'co_cotizacion_ajax.php', 'modulo=validar&accion=anular', 'gehen.php?anz=co_cotizacion_form&opcion=anular', 'SELF', '');" />
	            <input type="button" value="Ver" style="width:75px;" class="ver" onclick="cargarOpcion2(this.form, 'gehen.php?anz=co_cotizacion_form&opcion=ver', 'SELF', '', $('#sel_registros').val());" />

	            <input type="button" value="Imprimir" style="width:75px;" class="ver" onclick="abrirReporteVal('a_reporte', 'co_cotizacion_pdf', '', '', $('#sel_registros'), 0, this.form);" />
	        </td>
	    </tr>
	</table>

	<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px;">
		<table class="tblLista" style="width:100%; min-width:<?=$_width?>px;">
			<thead>
			    <tr>
			        <th style="min-width: 200px;" align="left" onclick="order('Establecimiento')">Establecimiento</th>
			        <th width="100" onclick="order('NroCotizacion')">N&uacute;mero</th>
			        <th width="75" onclick="order('FechaDocumento')">Fecha</th>
			        <th style="min-width: 200px;" align="left" onclick="order('NombreCliente')">Nombre del Cliente</th>
			        <th width="150" onclick="order('MontoTotal')">Monto Total</th>
			        <th width="100" onclick="order('Estado')">Estado</th>
			    </tr>
		    </thead>
		    
		    <tbody id="lista_registros">
			<?php
			//	consulto todos
			$sql = "SELECT *
					FROM co_cotizacion c
					INNER JOIN co_establecimientofiscal ef ON ef.CodEstablecimiento = c.CodEstablecimiento
					WHERE 1 $filtro";
			$rows_total = getNumRows3($sql);
			//	consulto lista
			$sql = "SELECT
						c.*,
						ef.Descripcion AS Establecimiento
					FROM co_cotizacion c
					INNER JOIN co_establecimientofiscal ef ON ef.CodEstablecimiento = c.CodEstablecimiento
					WHERE 1 $filtro
					ORDER BY $fOrderBy
					LIMIT ".intval($limit).", ".intval($maxlimit);
			$field = getRecords($sql);
			$rows_lista = count($field);
			foreach($field as $f) {
				$id = $f['CodCotizacion'];
				?>
				<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
					<td><?=htmlentities($f['Establecimiento'])?></td>
					<td align="center"><?=$f['NroCotizacion']?></td>
					<td align="center"><?=formatFechaAMD($f['FechaDocumento'])?></td>
					<td><?=htmlentities($f['NombreCliente'])?></td>
					<td align="right"><?=number_format($f['MontoTotal'],2,',','.')?></td>
					<td align="center"><?=printValores('cotizacion-estado',$f['Estado'])?></td>
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