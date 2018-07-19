<?php
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$fFechaCobranzaD = formatFechaDMA($PeriodoActual.'-01');
	$fFechaCobranzaH = formatFechaDMA($FechaActual);
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "FechaCobranza";
}
//	------------------------------------
if ($lista == "listar") {
	$_titulo = "Lista de Cobranzas";
	$_btNuevo = "";
	$_btModificar = "";
	$_btAprobar = "display:none;";
	$_btAnular = "";
}
elseif ($lista == "aprobar") {
	$fEstado = "PR";
	##	
	$_titulo = "Lista de Cobranzas / Aprobar";
	$_btNuevo = "display:none;";
	$_btModificar = "display:none;";
	$_btAprobar = "";
	$_btAnular = "";
}
//	------------------------------------
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (c.NroCobranza LIKE '%$fBuscar%'
					  OR p1.NomCompleto LIKE '%$fBuscar%'
					  OR p2.NomCompleto LIKE '%$fBuscar%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (c.Estado = '$fEstado')"; } else $dEstado = "disabled";
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (c.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fFechaCobranzaD != "" || $fFechaCobranzaH != "") {
	$cFechaCobranza = "checked";
	if ($fFechaCobranzaD != "") $filtro.=" AND (c.FechaCobranza >= '".formatFechaAMD($fFechaCobranzaD)."')";
	if ($fFechaCobranzaH != "") $filtro.=" AND (c.FechaCobranza <= '".formatFechaAMD($fFechaCobranzaH)."')";
} else $dFechaCobranza = "disabled";
if ($fCodPersonaCliente != "") { $cCodPersonaCliente = "checked"; $filtro.=" AND (c.CodPersonaCliente = '".$fCodPersonaCliente."')"; } else $dCodPersonaCliente = "visibility:hidden;";
if ($fFechaPreparadoD != "" || $fFechaPreparadoH != "") {
	$cFechaPreparado = "checked";
	if ($fFechaPreparadoD != "") $filtro.=" AND (SUBSTRING(c.FechaPreparado,1,10) >= '".formatFechaAMD($fFechaPreparadoD)."')";
	if ($fFechaPreparadoH != "") $filtro.=" AND (SUBSTRING(c.FechaPreparado,1,10) <= '".formatFechaAMD($fFechaPreparadoH)."')";
} else $dFechaPreparado = "disabled";
if ($fCodPersonaCajero != "") { $cCodPersonaCajero = "checked"; $filtro.=" AND (c.CodPersonaCajero = '$fCodPersonaCajero')"; } else $dCodPersonaCajero = "disabled";
//	------------------------------------
$_width = 700;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=co_cobranza_lista" method="post" autocomplete="off">
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
				<td align="right" width="100">Fecha Prep.:</td>
				<td>
					<input type="checkbox" <?=$cFechaPreparado?> onclick="chkCampos2(this.checked, ['fFechaPreparadoD','fFechaPreparadoH']);" />
					<input type="text" name="fFechaPreparadoD" id="fFechaPreparadoD" value="<?=$fFechaPreparadoD?>" <?=$dFechaPreparado?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" /> -
		            <input type="text" name="fFechaPreparadoH" id="fFechaPreparadoH" value="<?=$fFechaPreparadoH?>" <?=$dFechaPreparado?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" />
		        </td>
				<td align="right" width="100">Buscar:</td>
				<td>
					<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
					<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:100px;" <?=$dBuscar?> />
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
					<a href="../lib/listas/gehen.php?anz=lista_personas&campo1=fCodPersonaCliente&campo2=fNombreCliente&campo3=fDocFiscalCliente&ventana=&filtrar=default&FlagClasePersona=S&fEsCliente=S&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" style=" <?=$dCodPersonaCliente?>" id="aCodPersonaCliente">
		            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
		            </a>
				</td>
				<td align="right">Fecha Cobranza:</td>
				<td>
					<input type="checkbox" <?=$cFechaCobranza?> onclick="chkCampos2(this.checked, ['fFechaCobranzaD','fFechaCobranzaH']);" />
					<input type="text" name="fFechaCobranzaD" id="fFechaCobranzaD" value="<?=$fFechaCobranzaD?>" <?=$dFechaCobranza?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" /> -
		            <input type="text" name="fFechaCobranzaH" id="fFechaCobranzaH" value="<?=$fFechaCobranzaH?>" <?=$dFechaCobranza?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" />
		        </td>
				<td align="right">Estado: </td>
				<td>
					<?php if ($lista == 'listar') { ?>
			            <input type="checkbox" <?=$cEstado?> onclick="chkFiltro(this.checked, 'fEstado');" />
			            <select name="fEstado" id="fEstado" style="width:100px;" <?=$dEstado?>>
			                <option value="">&nbsp;</option>
			                <?=loadSelectValores("cobranza-estado", $fEstado, 0)?>
			            </select>
					<?php } else { ?>
			            <input type="checkbox" <?=$cEstado?> onclick="this.checked=!this.checked;" />
			            <select name="fEstado" id="fEstado" style="width:100px;" <?=$dEstado?>>
			                <?=loadSelectValores("cobranza-estado", $fEstado, 1)?>
			            </select>
					<?php } ?>
				</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td align="right">Cajero:</td>
				<td>
					<input type="checkbox" <?=$cCodPersonaCajero?> onclick="chkFiltro(this.checked, 'fCodPersonaCajero');" />
					<select name="fCodPersonaCajero" id="fCodPersonaCajero" style="width:225px;" <?=$dCodPersonaCajero?>>
						<option value="">&nbsp;</option>
						<?=cajeros($fCodPersonaCajero)?>
					</select>
				</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
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

	            <input type="button" value="Nuevo" style="width:75px; <?=$_btNuevo?>" class="insert" onclick="cargarPagina(this.form, 'gehen.php?anz=co_cobranza_form&opcion=nuevo&origen=co_cobranza_lista');" />
            	<input type="button" value="Modificar" style="width:75px; <?=$_btModificar?>" class="update" onclick="cargarOpcionValidar3(this.form, $('#sel_registros').val(), 'co_cobranza_ajax.php', 'modulo=validar&accion=modificar', 'gehen.php?anz=co_cobranza_form&opcion=modificar', 'SELF', '');" />
				<?php if ($_PARAMETRO['COBSTATAP'] == 'S') { ?>
	            	<input type="button" value="Aprobar" style="width:75px; <?=$_btAprobar?>" class="update" onclick="cargarOpcionValidar3(this.form, $('#sel_registros').val(), 'co_cobranza_ajax.php', 'modulo=validar&accion=aprobar', 'gehen.php?anz=co_cobranza_form&opcion=aprobar', 'SELF', '');" />
				<?php } ?>
	            <input type="button" value="Ver" style="width:75px;" class="ver" onclick="cargarOpcion2(this.form, 'gehen.php?anz=co_cobranza_form&opcion=ver', 'SELF', '', $('#sel_registros').val());" />

	            <input type="button" value="Imprimir" style="width:75px;" class="ver" onclick="abrirReporteVal2('a_reporte', 'gehen.php?anz=co_cobranza_sustento', '', '', $('#sel_registros'), 0, this.form);" />
	        </td>
	    </tr>
	</table>

	<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px;">
		<table class="tblLista" style="width:100%; min-width:<?=$_width?>px;">
			<thead>
			    <tr>
			        <th width="75" onclick="order('NroCobranza')">Cobranza #</th>
			        <th width="75" onclick="order('FechaPreparado')">Fecha Prep.</th>
			        <th width="75" onclick="order('FechaCobranza')">Fecha Cobranza</th>
			        <th style="min-width: 200px;" align="left" onclick="order('Cliente')">Cliente</th>
			        <th width="100" onclick="order('Estado')">Estado</th>
			        <th style="min-width: 200px;" align="left" onclick="order('Cajero')">Cajero</th>
			    </tr>
		    </thead>
		    
		    <tbody id="lista_registros">
			<?php
			//	consulto todos
			$sql = "SELECT *
					FROM co_cobranza c
					INNER JOIN mastorganismos o ON o.CodOrganismo = c.CodOrganismo
					INNER JOIN mastpersonas p1 On p1.CodPersona = c.CodPersonaCliente
					LEFT JOIN mastpersonas p2 On p2.CodPersona = c.CodPersonaCajero
					WHERE 1 $filtro";
			$rows_total = getNumRows3($sql);
			//	consulto lista
			$sql = "SELECT
						c.*,
						o.Organismo,
						p1.NomCompleto AS Cliente,
						p2.NomCompleto AS Cajero
					FROM co_cobranza c
					INNER JOIN mastorganismos o ON o.CodOrganismo = c.CodOrganismo
					INNER JOIN mastpersonas p1 On p1.CodPersona = c.CodPersonaCliente
					LEFT JOIN mastpersonas p2 On p2.CodPersona = c.CodPersonaCajero
					WHERE 1 $filtro
					ORDER BY $fOrderBy
					LIMIT ".intval($limit).", ".intval($maxlimit);
			$field = getRecords($sql);
			$rows_lista = count($field);
			foreach($field as $f) {
				$id = $f['CodCobranza'];
				?>
				<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
					<td align="center"><?=$f['NroCobranza']?></td>
					<td align="center"><?=formatFechaAMD(substr($f['FechaPreparado'],0,10))?></td>
					<td align="center"><?=formatFechaAMD($f['FechaCobranza'])?></td>
					<td><?=htmlentities($f['Cliente'])?></td>
					<td align="center"><?=printValores('cobranza-estado',$f['Estado'])?></td>
					<td><?=htmlentities($f['Cajero'])?></td>
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