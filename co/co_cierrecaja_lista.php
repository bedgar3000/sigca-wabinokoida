<?php
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$fFechaCierreD = formatFechaDMA($PeriodoActual.'-01');
	$fFechaCierreH = formatFechaDMA($FechaActual);
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "FechaCierre";
}
//	------------------------------------
if ($lista == "listar") {
	$_titulo = "Cierre de Caja";
	$_btNuevo = "";
	$_btModificar = "";
	$_btAprobar = "display:none;";
	$_btAnular = "";
}
elseif ($lista == "aprobar") {
	$fEstado = "PR";
	##	
	$_titulo = "Cierre de Caja / Aprobar";
	$_btNuevo = "display:none;";
	$_btModificar = "display:none;";
	$_btAprobar = "";
	$_btAnular = "";
}
//	------------------------------------
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (cc.NroCierre LIKE '%$fBuscar%'
					  OR cc.DocFiscalCliente LIKE '%$fBuscar%'
					  OR cc.NombreCliente LIKE '%$fBuscar%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (cc.Estado = '$fEstado')"; } else $dEstado = "disabled";
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (cc.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fCodEstablecimiento != "") { $cCodEstablecimiento = "checked"; $filtro.=" AND (cc.CodEstablecimiento = '".$fCodEstablecimiento."')"; } else $dCodEstablecimiento = "disabled";
if ($fFechaCierreD != "" || $fFechaCierreH != "") {
	$cFechaCierre = "checked";
	if ($fFechaCierreD != "") $filtro.=" AND (cc.FechaCierre >= '".formatFechaAMD($fFechaCierreD)."')";
	if ($fFechaCierreH != "") $filtro.=" AND (cc.FechaCierre <= '".formatFechaAMD($fFechaCierreH)."')";
} else $dFechaCierre = "disabled";
if ($fCodPersonaCajero != "") { $cCodPersonaCajero = "checked"; $filtro.=" AND (cc.CodPersonaCajero = '$fCodPersonaCajero')"; } else $dCodPersonaCajero = "disabled";
//	------------------------------------
$_width = 700;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=co_cierrecaja_lista" method="post" autocomplete="off">
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
				<td align="right" width="100">Cajero:</td>
				<td>
					<input type="checkbox" <?=$cCodPersonaCajero?> onclick="chkFiltro(this.checked, 'fCodPersonaCajero');" />
					<select name="fCodPersonaCajero" id="fCodPersonaCajero" style="width:225px;" <?=$dCodPersonaCajero?>>
						<option value="">&nbsp;</option>
						<?=cajeros($fCodPersonaCajero)?>
					</select>
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
				<td align="right">Fecha Cierre:</td>
				<td>
					<input type="checkbox" <?=$cFechaCierre?> onclick="chkCampos2(this.checked, ['fFechaCierreD','fFechaCierreH']);" />
					<input type="text" name="fFechaCierreD" id="fFechaCierreD" value="<?=$fFechaCierreD?>" <?=$dFechaCierre?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" /> -
		            <input type="text" name="fFechaCierreH" id="fFechaCierreH" value="<?=$fFechaCierreH?>" <?=$dFechaCierre?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" />
		        </td>
				<td align="right">Estado: </td>
				<td>
					<?php if ($lista == 'listar') { ?>
			            <input type="checkbox" <?=$cEstado?> onclick="chkFiltro(this.checked, 'fEstado');" />
			            <select name="fEstado" id="fEstado" style="width:100px;" <?=$dEstado?>>
			                <option value="">&nbsp;</option>
			                <?=loadSelectValores("cierre-caja-estado", $fEstado, 0)?>
			            </select>
					<?php } else { ?>
			            <input type="checkbox" <?=$cEstado?> onclick="this.checked=!this.checked;" />
			            <select name="fEstado" id="fEstado" style="width:100px;" <?=$dEstado?>>
			                <?=loadSelectValores("cierre-caja-estado", $fEstado, 1)?>
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

	            <input type="button" value="Nuevo" style="width:75px; <?=$_btNuevo?>" class="insert" onclick="cargarPagina(this.form, 'gehen.php?anz=co_cierrecaja_form&opcion=nuevo&origen=co_cierrecaja_lista');" />
            	<input type="button" value="Modificar" style="width:75px; <?=$_btModificar?>" class="update" onclick="cargarOpcionValidar3(this.form, $('#sel_registros').val(), 'co_cierrecaja_ajax.php', 'modulo=validar&accion=modificar', 'gehen.php?anz=co_cierrecaja_form&opcion=modificar', 'SELF', '');" />
	            <input type="button" value="Aprobar" style="width:75px; <?=$_btAprobar?>" class="update" onclick="cargarOpcionValidar3(this.form, $('#sel_registros').val(), 'co_cierrecaja_ajax.php', 'modulo=validar&accion=aprobar', 'gehen.php?anz=co_cierrecaja_form&opcion=aprobar', 'SELF', '');" />
	            <input type="button" value="Anular" style="width:75px; <?=$_btAnular?>" class="update" onclick="cargarOpcionValidar3(this.form, $('#sel_registros').val(), 'co_cierrecaja_ajax.php', 'modulo=validar&accion=anular', 'gehen.php?anz=co_cierrecaja_form&opcion=anular', 'SELF', '');" />
	            <input type="button" value="Ver" style="width:75px;" class="ver" onclick="cargarOpcion2(this.form, 'gehen.php?anz=co_cierrecaja_form&opcion=ver', 'SELF', '', $('#sel_registros').val());" />
	            <input type="button" value="Imprimir" style="width:75px;" class="ver" onclick="abrirReporteVal('a_reporte', 'co_cierrecaja_pdf', '', '', $('#sel_registros'), 0, this.form);" />
	        </td>
	    </tr>
	</table>

	<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px;">
		<table class="tblLista" style="width:100%; min-width:1000px;">
			<thead>
			    <tr>
			        <th style="min-width: 200px;" align="left" onclick="order('Establecimiento')">Establecimiento</th>
			        <th width="100" onclick="order('NroCierre')">N&uacute;mero</th>
			        <th width="75" onclick="order('FechaCierre')">Fecha</th>
			        <th width="100" onclick="order('Estado')">Estado</th>
			        <th style="min-width: 200px;" align="left" onclick="order('Cajero')">Cajero</th>
			        <th style="min-width: 200px;" align="left" onclick="order('Comentarios')">Comentarios</th>
			    </tr>
		    </thead>
		    
		    <tbody id="lista_registros">
			<?php
			//	consulto todos
			$sql = "SELECT *
					FROM co_cierrecaja cc
					INNER JOIN co_establecimientofiscal ef ON ef.CodEstablecimiento = cc.CodEstablecimiento
					INNER JOIN mastpersonas p ON p.CodPersona = cc.CodPersonaCajero
					WHERE 1 $filtro";
			$rows_total = getNumRows3($sql);
			//	consulto lista
			$sql = "SELECT
						cc.*,
						ef.Descripcion AS Establecimiento,
						p.NomCompleto AS Cajero
					FROM co_cierrecaja cc
					INNER JOIN co_establecimientofiscal ef ON ef.CodEstablecimiento = cc.CodEstablecimiento
					INNER JOIN mastpersonas p ON p.CodPersona = cc.CodPersonaCajero
					WHERE 1 $filtro
					ORDER BY $fOrderBy
					LIMIT ".intval($limit).", ".intval($maxlimit);
			$field = getRecords($sql);
			$rows_lista = count($field);
			foreach($field as $f) {
				$id = $f['CodCierre'];
				?>
				<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
					<td><?=htmlentities($f['Establecimiento'])?></td>
					<td align="center"><?=$f['NroCierre']?></td>
					<td align="center"><?=formatFechaAMD($f['FechaCierre'])?></td>
					<td align="center"><?=printValores('cierre-caja-estado',$f['Estado'])?></td>
					<td><?=htmlentities($f['Cajero'])?></td>
					<td><?=htmlentities($f['Comentarios'])?></td>
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