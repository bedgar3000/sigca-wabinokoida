<?php
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "NroArqueo";
}
//	------------------------------------
if ($lista == "listar") {
	$_titulo = "Arqueo de Caja";
	$_btNuevo = "";
	$_btModificar = "";
	$_btAprobar = "display:none;";
	$_btAnular = "";
}
elseif ($lista == "aprobar") {
	$fEstado = "PR";
	##	
	$_titulo = "Arqueo de Caja / Aprobar";
	$_btNuevo = "display:none;";
	$_btModificar = "display:none;";
	$_btAprobar = "";
	$_btAnular = "";
}
//	------------------------------------
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (ac.NroArqueo LIKE '%$fBuscar%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (ac.Estado = '$fEstado')"; } else $dEstado = "disabled";
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (ac.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fFechaD != "" || $fFechaH != "") {
	$cFecha = "checked";
	if ($fFechaD != "") $filtro.=" AND (ac.Fecha >= '".formatFechaAMD($fFechaD)."')";
	if ($fFechaH != "") $filtro.=" AND (ac.Fecha <= '".formatFechaAMD($fFechaH)."')";
} else $dFecha = "disabled";
//	------------------------------------
$_width = 700;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=co_arqueocaja_lista" method="post" autocomplete="off">
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
					<select name="fCodOrganismo" id="fCodOrganismo" style="width:295px;" <?=$dCodOrganismo?>>
						<?=getOrganismos($fCodOrganismo, 3);?>
					</select>
				</td>
				<td align="right" width="100">Buscar:</td>
				<td>
					<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
					<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:100px;" <?=$dBuscar?> />
				</td>
			</tr>
			<tr>
				<td align="right">Fecha:</td>
				<td>
					<input type="checkbox" <?=$cFecha?> onclick="chkCampos2(this.checked, ['fFechaD','fFechaH']);" />
					<input type="text" name="fFechaD" id="fFechaD" value="<?=$fFechaD?>" <?=$dFecha?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" /> -
		            <input type="text" name="fFechaH" id="fFechaH" value="<?=$fFechaH?>" <?=$dFecha?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" />
		        </td>
				<td align="right">Estado: </td>
				<td>
					<?php if ($lista == 'listar') { ?>
			            <input type="checkbox" <?=$cEstado?> onclick="chkFiltro(this.checked, 'fEstado');" />
			            <select name="fEstado" id="fEstado" style="width:100px;" <?=$dEstado?>>
			                <option value="">&nbsp;</option>
			                <?=loadSelectValores("arqueo-caja-estado", $fEstado, 0)?>
			            </select>
					<?php } else { ?>
			            <input type="checkbox" <?=$cEstado?> onclick="this.checked=!this.checked;" />
			            <select name="fEstado" id="fEstado" style="width:100px;" <?=$dEstado?>>
			                <?=loadSelectValores("arqueo-caja-estado", $fEstado, 1)?>
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

	            <input type="button" value="Nuevo" style="width:75px; <?=$_btNuevo?>" class="insert" onclick="cargarPagina(this.form, 'gehen.php?anz=co_arqueocaja_form&opcion=nuevo&origen=co_arqueocaja_lista');" />
	            <input type="button" value="Aprobar" style="width:75px; <?=$_btAprobar?>" class="update" onclick="cargarOpcionValidar3(this.form, $('#sel_registros').val(), 'co_arqueocaja_ajax.php', 'modulo=validar&accion=aprobar', 'gehen.php?anz=co_arqueocaja_form&opcion=aprobar', 'SELF', '');" />
	            <input type="button" value="Anular" style="width:75px; <?=$_btAnular?>" class="update" onclick="cargarOpcionValidar3(this.form, $('#sel_registros').val(), 'co_arqueocaja_ajax.php', 'modulo=validar&accion=anular', 'gehen.php?anz=co_arqueocaja_form&opcion=anular', 'SELF', '');" />
	            <input type="button" value="Ver" style="width:75px;" class="ver" onclick="cargarOpcion2(this.form, 'gehen.php?anz=co_arqueocaja_form&opcion=ver', 'SELF', '', $('#sel_registros').val());" />
	            <input type="button" value="Imprimir" style="width:75px;" class="ver" onclick="abrirReporteVal2('a_reporte', 'gehen.php?anz=co_arqueocaja_sustento', '', '', $('#sel_registros'), 0, this.form);" />
	        </td>
	    </tr>
	</table>

	<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px;">
		<table class="tblLista" style="width:100%; min-width:1000px;">
			<thead>
			    <tr>
			        <th style="min-width: 200px;" align="left" onclick="order('Organismo')">Organismo</th>
			        <th width="100" onclick="order('NroArqueo')">N&uacute;mero</th>
			        <th width="75" onclick="order('Fecha')">Fecha</th>
			        <th width="150" onclick="order('NroTransaccionCxP')">Transacción</th>
			        <th style="min-width: 200px;" align="left" onclick="order('NomPreparadoPor')">Preparado Por</th>
			        <th width="100" onclick="order('Estado')">Estado</th>
			    </tr>
		    </thead>
		    
		    <tbody id="lista_registros">
			<?php
			//	consulto todos
			$sql = "SELECT *
					FROM co_arqueocaja ac
					INNER JOIN mastorganismos o On o.CodOrganismo = ac.CodOrganismo
					LEFT JOIN mastpersonas p ON p.CodPersona = ac.PreparadoPor
					WHERE 1 $filtro";
			$rows_total = getNumRows3($sql);
			//	consulto lista
			$sql = "SELECT
						ac.*,
						o.Organismo,
						p.NomCompleto AS NomPreparadoPor
					FROM co_arqueocaja ac
					INNER JOIN mastorganismos o On o.CodOrganismo = ac.CodOrganismo
					LEFT JOIN mastpersonas p ON p.CodPersona = ac.PreparadoPor
					WHERE 1 $filtro
					ORDER BY $fOrderBy
					LIMIT ".intval($limit).", ".intval($maxlimit);
			$field = getRecords($sql);
			$rows_lista = count($field);
			foreach($field as $f) {
				$id = $f['CodArqueo'];
				?>
				<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
					<td><?=htmlentities($f['Organismo'])?></td>
					<td align="center"><?=$f['NroArqueo']?></td>
					<td align="center"><?=formatFechaAMD($f['Fecha'])?></td>
					<td align="center"><?=$f['NroTransaccionCxP']?></td>
					<td><?=htmlentities($f['NomPreparadoPor'])?></td>
					<td align="center"><?=printValores('arqueo-caja-estado',$f['Estado'])?></td>
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