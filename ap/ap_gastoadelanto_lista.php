<?php
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	if ($lista == 'listar') {
		$fFechaDocumentoD = formatFechaDMA($PeriodoActual.'-01');
		$fFechaDocumentoH = formatFechaDMA($FechaActual);
	}
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "FechaDocumento";
}
//	------------------------------------
if ($lista == "listar") {
	$_titulo = "Lista de Adelantos";
	$_btNuevo = "";
	$_btModificar = "";
	$_btAprobar = "display:none;";
	$_btGenerar = "display:none;";
	$_btAnular = "";
}
elseif ($lista == "aprobar") {
	$fEstado = "PR";
	##	
	$_titulo = "Lista de Adelantos / Aprobar";
	$_btNuevo = "display:none;";
	$_btModificar = "display:none;";
	$_btAprobar = "";
	$_btGenerar = "display:none;";
	$_btAnular = "";
}
elseif ($lista == "generar") {
	$fEstado = "AP";
	##	
	$_titulo = "Lista de Adelantos / Generar Obligación";
	$_btNuevo = "display:none;";
	$_btModificar = "display:none;";
	$_btAprobar = "display:none;";
	$_btGenerar = "";
	$_btAnular = "";
}
//	------------------------------------
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (ga.NroAdelanto LIKE '%$fBuscar%'
					  OR p.NomCompleto LIKE '%$fBuscar%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (ga.Estado = '$fEstado')"; } else $dEstado = "disabled";
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (ga.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fFechaDocumentoD != "" || $fFechaDocumentoH != "") {
	$cFechaDocumento = "checked";
	if ($fFechaDocumentoD != "") $filtro.=" AND (ga.FechaDocumento >= '".formatFechaAMD($fFechaDocumentoD)."')";
	if ($fFechaDocumentoH != "") $filtro.=" AND (ga.FechaDocumento <= '".formatFechaAMD($fFechaDocumentoH)."')";
} else $dFechaDocumento = "disabled";
if ($fTipoAdelanto != "") { $cTipoAdelanto = "checked"; $filtro.=" AND (ga.TipoAdelanto = '$fTipoAdelanto')"; } else $dTipoAdelanto = "disabled";
if ($fCodProveedor != "") { $cCodProveedor = "checked"; $filtro.=" AND (ga.CodProveedor = '".$fCodProveedor."')"; } else $dCodProveedor = "visibility:hidden;";
if ($fCodCentroCosto != "") { $cCodCentroCosto = "checked"; $filtro.=" AND (ga.CodCentroCosto = '$fCodCentroCosto')"; } else $dCodCentroCosto = "disabled";
//	------------------------------------
$_width = 800;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=ap_gastoadelanto_lista" method="post" autocomplete="off">
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
				<td align="right" width="100">Proveedor:</td>
				<td class="gallery clearfix">
					<input type="checkbox" <?=$cCodProveedor?> onclick="ckLista(this.checked, ['fCodProveedor','fNombreProveedor','fDocFiscalProveedor'], ['aCodProveedor']);" />
					<input type="hidden" name="fDocFiscalProveedor" id="fDocFiscalProveedor" value="<?=$fDocFiscalProveedor?>">
					<input type="hidden" name="fCodProveedor" id="fCodProveedor" value="<?=$fCodProveedor?>" />
					<input type="text" name="fNombreProveedor" id="fNombreProveedor" value="<?=htmlentities($fNombreProveedor)?>" style="width:225px;" readonly />
					<a href="../lib/listas/gehen.php?anz=lista_personas&campo1=fCodProveedor&campo2=fNombreProveedor&campo3=fDocFiscalProveedor&ventana=&filtrar=default&FlagClasePersona=S&fEsProveedor=S&fEsOtros=S&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" style=" <?=$dCodProveedor?>" id="aCodProveedor">
		            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
		            </a>
				</td>
				<td align="right" width="100">Buscar:</td>
				<td>
					<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
					<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:100px;" <?=$dBuscar?> />
				</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td align="right">Centro de Costo:</td>
				<td>
					<input type="checkbox" <?=$cCodCentroCosto?> onclick="chkFiltro(this.checked, 'fCodCentroCosto');" />
					<select name="fCodCentroCosto" id="fCodCentroCosto" style="width:225px;" <?=$dCodCentroCosto?>>
						<option value="">&nbsp;</option>
						<?=loadSelect2('ac_mastcentrocosto','CodCentroCosto','Descripcion',$fCodCentroCosto)?>
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
			                <?=loadSelectValores("adelanto-estado", $fEstado, 0)?>
			            </select>
					<?php } else { ?>
			            <input type="checkbox" <?=$cEstado?> onclick="this.checked=!this.checked;" />
			            <select name="fEstado" id="fEstado" style="width:100px;" <?=$dEstado?>>
			                <?=loadSelectValores("adelanto-estado", $fEstado, 1)?>
			            </select>
					<?php } ?>
				</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td align="right">Tipo Adelanto: </td>
				<td>
		            <input type="checkbox" <?=$cTipoAdelanto?> onclick="chkFiltro(this.checked, 'fTipoAdelanto');" />
		            <select name="fTipoAdelanto" id="fTipoAdelanto" style="width:100px;" <?=$dTipoAdelanto?>>
		                <option value="">&nbsp;</option>
		                <?=loadSelectValores("adelanto-tipo", $fTipoAdelanto, 0)?>
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

	            <input type="button" value="Nuevo" style="width:75px; <?=$_btNuevo?>" class="insert" onclick="cargarPagina(this.form, 'gehen.php?anz=ap_gastoadelanto_form&opcion=nuevo&origen=ap_gastoadelanto_lista');" />
            	<input type="button" value="Modificar" style="width:75px; <?=$_btModificar?>" class="update" onclick="cargarOpcionValidar3(this.form, $('#sel_registros').val(), 'ap_gastoadelanto_ajax.php', 'modulo=validar&accion=modificar', 'gehen.php?anz=ap_gastoadelanto_form&opcion=modificar', 'SELF', '');" />
	            <input type="button" value="Aprobar" style="width:75px; <?=$_btAprobar?>" class="update" onclick="cargarOpcionValidar3(this.form, $('#sel_registros').val(), 'ap_gastoadelanto_ajax.php', 'modulo=validar&accion=aprobar', 'gehen.php?anz=ap_gastoadelanto_form&opcion=aprobar', 'SELF', '');" />
	            <input type="button" value="Generar" style="width:75px; <?=$_btGenerar?>" class="update" onclick="cargarOpcionValidar3(this.form, $('#sel_registros').val(), 'ap_gastoadelanto_ajax.php', 'modulo=validar&accion=generar', 'gehen.php?anz=ap_obligacion_form&opcion=adelanto-generar&origen=ap_gastoadelanto_lista', 'SELF', '');" />
	            <input type="button" value="Anular" style="width:75px; <?=$_btAnular?>" class="update" onclick="cargarOpcionValidar3(this.form, $('#sel_registros').val(), 'ap_gastoadelanto_ajax.php', 'modulo=validar&accion=anular', 'gehen.php?anz=ap_gastoadelanto_form&opcion=anular', 'SELF', '');" />
	            <input type="button" value="Ver" style="width:75px;" class="ver" onclick="cargarOpcion2(this.form, 'gehen.php?anz=ap_gastoadelanto_form&opcion=ver', 'SELF', '', $('#sel_registros').val());" />
	        </td>
	    </tr>
	</table>

	<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px;">
		<table class="tblLista" style="width:100%; min-width:<?=$_width?>px;">
			<thead>
			    <tr>
			        <th width="75" onclick="order('NroAdelanto')">N&uacute;mero</th>
			        <th style="min-width: 200px;" align="left" onclick="order('NomProveedor')">Proveedor</th>
			        <th width="50" onclick="order('CentroCosto')">C.C</th>
			        <th width="150" onclick="order('MontoTotal')">Monto Total</th>
			        <th width="150" onclick="order('SaldoAdelanto')">Saldo Adelanto</th>
			        <th width="75" onclick="order('FechaDocumento')">Fecha</th>
			        <th width="100" onclick="order('Estado')">Estado</th>
			    </tr>
		    </thead>
		    
		    <tbody id="lista_registros">
			<?php
			//	consulto todos
			$sql = "SELECT *
					FROM ap_gastoadelanto ga
					INNER JOIN ac_mastcentrocosto cc ON cc.CodCentrocosto = ga.CodCentroCosto
					WHERE 1 $filtro";
			$rows_total = getNumRows3($sql);
			//	consulto lista
			$sql = "SELECT
						ga.*,
						p.NomCompleto AS NomProveedor,
						cc.Codigo AS CentroCosto
					FROM ap_gastoadelanto ga
					INNER JOIN mastpersonas p ON p.CodPersona = ga.CodProveedor
					INNER JOIN ac_mastcentrocosto cc ON cc.CodCentrocosto = ga.CodCentroCosto
					WHERE 1 $filtro
					ORDER BY $fOrderBy
					LIMIT ".intval($limit).", ".intval($maxlimit);
			$field = getRecords($sql);
			$rows_lista = count($field);
			foreach($field as $f) {
				$id = $f['CodAdelanto'];
				?>
				<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
					<td align="center"><?=$f['NroAdelanto']?></td>
					<td><?=htmlentities($f['NomProveedor'])?></td>
					<td align="center"><?=$f['CentroCosto']?></td>
					<td align="right"><?=number_format($f['MontoTotal'],2,',','.')?></td>
					<td align="right"><?=number_format($f['SaldoAdelanto'],2,',','.')?></td>
					<td align="center"><?=formatFechaAMD($f['FechaDocumento'])?></td>
					<td align="center"><?=printValores('adelanto-estado',$f['Estado'])?></td>
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