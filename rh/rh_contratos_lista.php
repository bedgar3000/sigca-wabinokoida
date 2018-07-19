<?php
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$ficha = 1;
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (p.Ndocumento LIKE '%".$fBuscar."%' OR
					  p.NomCompleto LIKE '%".$fBuscar."%' OR
					  e.CodEmpleado LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (c.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fCodDependencia != "") { $cCodDependencia = "checked"; $filtro.=" AND (e.CodDependencia = '".$fCodDependencia."')"; } else $dCodDependencia = "disabled";
if ($fCodFormato != "") { $cCodFormato = "checked"; $filtro.=" AND (c.CodFormato = '".$fCodFormato."')"; } else $dCodFormato = "disabled";
if ($fTipoContrato != "") { $cTipoContrato = "checked"; $filtro.=" AND (tc.TipoContrato = '".$fTipoContrato."')"; } else $dTipoContrato = "disabled";
if ($fFechaDesde != "" || $fFechaHasta != "") {
	$cFechaVigencia = "checked";
	if ($fFechaDesde != "") $filtro.=" AND ('".formatFechaAMD($fFechaDesde)."' >= c.FechaDesde AND '".formatFechaAMD($fFechaDesde)."' <= c.FechaHasta)";
	if ($fFechaHasta != "") $filtro.=" AND ('".formatFechaAMD($fFechaHasta)."' >= c.FechaDesde AND '".formatFechaAMD($fFechaHasta)."' <= c.FechaHasta)";
} else $dFechaVigencia = "disabled";
if ($fFechaFirmaD != "" || $fFechaFirmaH != "") {
	$cFechaFirma = "checked";
	if ($fFechaFirmaD != "") $filtro.=" AND (c.FechaFirma >= '".formatFechaAMD($fFechaFirmaD)."')";
	if ($fFechaFirmaH != "") $filtro.=" AND (c.FechaFirma <= '".formatFechaAMD($fFechaFirmaH)."')";
} else $dFechaFirma = "disabled";
//	------------------------------------
if ($ficha == 1) {
	$tab1 = "display:block;"; $tab2 = "display:none;"; $tab3 = "display:none;"; $tab4 = "display:none;";
	$current1 = "current"; $current2 = ""; $current3 = ""; $current4 = "";
}
elseif ($ficha == 2) {
	$tab2 = "display:block;"; $tab1 = "display:none;"; $tab3 = "display:none;"; $tab4 = "display:none;";
	$current2 = "current"; $current1 = ""; $current3 = ""; $current4 = "";
}
elseif ($ficha == 3) {
	$tab3 = "display:block;"; $tab1 = "display:none;"; $tab2 = "display:none;"; $tab4 = "display:none;";
	$current3 = "current"; $current1 = ""; $current2 = ""; $current4 = "";
}
elseif ($ficha == 4) {
	$tab4 = "display:block;"; $tab1 = "display:none;"; $tab2 = "display:none;"; $tab3 = "display:none;";
	$current4 = "current"; $current1 = ""; $current2 = ""; $current3 = "";
}
//	------------------------------------
##	actualizo contratoa
$sql = "UPDATE rh_contratos SET Estado = 'VE' WHERE CURRENT_DATE() > FechaHasta AND Estado = 'VI' AND FechaHasta <> '0000-00-00'";
execute($sql);
//	------------------------------------
$_titulo = "Control de Contratos";
$_width = 900;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=rh_contratos_lista" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="ficha" id="ficha" value="<?=$ficha?>" />
<input type="hidden" name="sel_registros" id="sel_registros" />

<!--FILTRO-->
<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
	<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
		<tr>
			<td align="right" width="125">Organismo:</td>
			<td>
				<input type="checkbox" <?=$cCodOrganismo?> onclick="this.checked=!this.checked;" onChange="loadSelect($('#fCodDependencia'), 'tabla=dependencia_filtro&opcion='+$(this).val(), 1);" />
				<select name="fCodOrganismo" id="fCodOrganismo" style="width:225px;" <?=$dCodOrganismo?> onChange="loadSelect($('#fCodDependencia'), 'tabla=dependencia_filtro&opcion='+$(this).val(), 1);">
					<?=getOrganismos($fCodOrganismo, 3);?>
				</select>
			</td>
			<td align="right">Tipo de Contrato:</td>
			<td>
				<input type="checkbox" <?=$cTipoContrato?> onclick="chkCampos(this.checked, 'fTipoContrato');" onChange="loadSelect($('#fCodFormato'), 'tabla=rh_formatocontrato&TipoContrato='+$(this).val(), 1);" />
				<select name="fTipoContrato" id="fTipoContrato" style="width:125px;" <?=$dTipoContrato?> onChange="loadSelect($('#fCodFormato'), 'tabla=rh_formatocontrato&TipoContrato='+$(this).val(), 1);">
					<option value="">&nbsp;</option>
					<?=loadSelect2('rh_tipocontrato','TipoContrato','Descripcion',$fTipoContrato,0,NULL,NULL,'TipoContrato')?>
				</select>
			</td>
			<td align="right">Vigencia de Contrato: </td>
			<td>
				<input type="checkbox" <?=$cFechaVigencia?> onclick="chkCampos2(this.checked, ['fFechaDesde','fFechaHasta']);" />
				<input type="text" name="fFechaDesde" id="fFechaDesde" value="<?=$fFechaDesde?>" style="width:65px;" maxlength="10" class="datepicker" <?=$dFechaVigencia?> />
				<input type="text" name="fFechaHasta" id="fFechaHasta" value="<?=$fFechaHasta?>" style="width:65px;" maxlength="10" class="datepicker" <?=$dFechaVigencia?> />
			</td>
	        <td>&nbsp;</td>
		</tr>
		<tr>
			<td align="right">Dependencia: </td>
			<td>
	            <input type="checkbox" <?=$cCodDependencia?> onclick="chkFiltro(this.checked, 'fCodDependencia');" />
				<select name="fCodDependencia" id="fCodDependencia" style="width:225px;" <?=$dCodDependencia?>>
					<option value="">&nbsp;</option>
					<?=getDependencias($fCodDependencia, $fCodOrganismo, 0);?>
				</select>
			</td>
			<td align="right">Formato de Contrato:</td>
			<td>
				<input type="checkbox" <?=$cCodFormato?> onclick="chkCampos(this.checked, 'fCodFormato');" />
				<select name="fCodFormato" id="fCodFormato" style="width:125px;" <?=$dCodFormato?>>
					<option value="">&nbsp;</option>
					<?=loadSelect2('rh_formatocontrato','CodFormato','Documento',$fCodFormato,0,['TipoContrato'],[$fTipoContrato],'CodFormato')?>
				</select>
			</td>
			<td align="right">Fecha de Firma: </td>
			<td>
				<input type="checkbox" <?=$cFechaFirma?> onclick="chkCampos2(this.checked, ['fFechaFirmaD','fFechaFirmaH']);" />
				<input type="text" name="fFechaFirmaD" id="fFechaFirmaD" value="<?=$fFechaFirmaD?>" style="width:65px;" maxlength="10" class="datepicker" <?=$dFechaFirma?> />
				<input type="text" name="fFechaFirmaH" id="fFechaFirmaH" value="<?=$fFechaFirmaH?>" style="width:65px;" maxlength="10" class="datepicker" <?=$dFechaFirma?> />
			</td>
	        <td>&nbsp;</td>
		</tr>
		<tr>
			<td align="right">Buscar:</td>
			<td>
				<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
				<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:225px;" <?=$dBuscar?> />
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

<table align="center" cellpadding="0" cellspacing="0" style="width:100%; min-width:<?=$_width?>px;">
    <tr>
        <td>
            <div class="header">
	            <ul id="tab">
		            <li id="li1" onclick="current($(this));" class=" <?=$current1?>"><a href="#" onclick="mostrarTab('tab', 1, 4);">Contratos Vigentes</a></li>
		            <li id="li2" onclick="current($(this));" class=" <?=$current2?>"><a href="#" onclick="mostrarTab('tab', 2, 4);">Contratos Vencidos</a></li>
		            <li id="li3" onclick="current($(this));" class=" <?=$current3?>"><a href="#" onclick="mostrarTab('tab', 3, 4);">Contratos por Vencer</a></li>
		            <li id="li4" onclick="current($(this));" class=" <?=$current4?>"><a href="#" onclick="mostrarTab('tab', 4, 4);">Empleados sin Contrato</a></li>
	            </ul>
            </div>
        </td>
    </tr>
</table>

<!--REGISTROS-->
<div id="tab1" style=" <?=$tab1?>">
	<table class="tblBotones" style="width:100%; min-width:<?=$_width?>px;">
	    <tr>
	        <td align="right">
	            <input type="button" value="Modificar" style="width:75px;" class="update" onclick="cargarOpcion2(this.form, 'gehen.php?anz=rh_contratos_form&opcion=modificar', 'SELF', '', $('#sel_registros').val());" />
	            <input type="button" value="Ver" style="width:75px;" class="ver" onclick="cargarOpcion2(this.form, 'gehen.php?anz=rh_contratos_form&opcion=ver', 'SELF', '', $('#sel_registros').val());" />
	            <input type="button" value="Abrir" style="width:75px;" class="ver" onclick="abrirContrato();" />
	        </td>
	    </tr>
	</table>
	<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px;">
		<table class="tblLista" style="width:100%; min-width:1100px;">
			<thead>
			    <tr>
			        <th width="55">Empleado</th>
			        <th align="left">Nombre Completo</th>
			        <th width="60">Nro. Documento</th>
			        <th width="125">Tipo de Contrato</th>
			        <th width="125">Formato de Contrato</th>
			        <th width="75">Inicio de Contrato</th>
			        <th width="75">Fin de Contrato</th>
			        <th width="50">Firma?</th>
			        <th width="75">Fecha de Firma</th>
			        <th width="60">Dias para Vencer</th>
			    </tr>
		    </thead>
		    
		    <tbody id="lista_registros">
			<?php
			//	consulto lista
			$sql = "SELECT
						c.*,
						p.Ndocumento,
						p.NomCompleto,
						e.CodEmpleado,
						fc.Documento AS NomFormato,
						tc.TipoContrato,
						tc.Descripcion AS NomTipoContrato,
						DATEDIFF(c.FechaHasta, CURRENT_DATE()) AS DiasParaVencer
					FROM
						rh_contratos c
						INNER JOIN mastpersonas p ON (p.CodPersona = c.CodPersona)
						INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
						LEFT JOIN rh_formatocontrato fc ON (fc.CodFormato = c.CodFormato)
						LEFT JOIN rh_tipocontrato tc ON (tc.TipoContrato = fc.TipoContrato)
					WHERE c.Estado = 'VI' $filtro
					ORDER BY CodEmpleado";
			$field = getRecords($sql);
			$rows_lista = count($field);
			foreach($field as $f) {
				$id = $f['CodContrato'];
				?>
				<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
					<td align="center"><?=$f['CodEmpleado']?></td>
					<td><?=htmlentities($f['NomCompleto'])?></td>
					<td align="right"><?=number_format($f['Ndocumento'],0,'','.')?></td>
					<td><?=htmlentities($f['TipoContrato'].'-'.$f['NomTipoContrato'])?></td>
					<td><?=htmlentities($f['CodFormato'].'-'.$f['NomFormato'])?></td>
					<td align="center"><?=formatFechaDMA($f['FechaDesde'])?></td>
					<td align="center"><?=formatFechaDMA($f['FechaHasta'])?></td>
					<td align="center"><?=printFlag($f['FlagFirma'])?></td>
					<td align="center"><?=formatFechaDMA($f['FechaFirma'])?></td>
					<td align="center"><?=$f['DiasParaVencer']?></td>
				</tr>
				<?php
			}
			?>
		    </tbody>
		</table>
	</div>
</div>

<div id="tab2" style=" <?=$tab2?>">
	<table class="tblBotones" style="width:100%; min-width:<?=$_width?>px;">
	    <tr>
	        <td align="right">
	            <input type="button" value="Renovar" style="width:75px;" class="insert" onclick="cargarPagina(this.form, 'gehen.php?anz=rh_contratos_form&opcion=renovar');" />
	            <input type="button" value="Ver" style="width:75px;" class="ver" onclick="cargarOpcion2(this.form, 'gehen.php?anz=rh_contratos_form&opcion=ver', 'SELF', '', $('#sel_registros').val());" />
	        </td>
	    </tr>
	</table>
	<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px;">
		<table class="tblLista" style="width:100%; min-width:1100px;">
			<thead>
			    <tr>
			        <th width="55">Empleado</th>
			        <th align="left">Nombre Completo</th>
			        <th width="60">Nro. Documento</th>
			        <th width="125">Tipo de Contrato</th>
			        <th width="125">Formato de Contrato</th>
			        <th width="75">Inicio de Contrato</th>
			        <th width="75">Fin de Contrato</th>
			        <th width="50">Firma?</th>
			        <th width="75">Fecha de Firma</th>
			    </tr>
		    </thead>
		    
		    <tbody id="lista_registros">
			<?php
			//	consulto lista
			$sql = "SELECT
						c.*,
						p.Ndocumento,
						p.NomCompleto,
						e.CodEmpleado,
						fc.Documento AS NomFormato,
						tc.TipoContrato,
						tc.Descripcion AS NomTipoContrato
					FROM
						rh_contratos c
						INNER JOIN mastpersonas p ON (p.CodPersona = c.CodPersona)
						INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
						LEFT JOIN rh_formatocontrato fc ON (fc.CodFormato = c.CodFormato)
						LEFT JOIN rh_tipocontrato tc ON (tc.TipoContrato = fc.TipoContrato)
					WHERE c.Estado = 'VE' $filtro
					ORDER BY CodEmpleado";
			$field = getRecords($sql);
			$rows_lista = count($field);
			foreach($field as $f) {
				$id = $f['CodContrato'];
				?>
				<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
					<td align="center"><?=$f['CodEmpleado']?></td>
					<td><?=htmlentities($f['NomCompleto'])?></td>
					<td align="right"><?=number_format($f['Ndocumento'],0,'','.')?></td>
					<td><?=htmlentities($f['TipoContrato'].'-'.$f['NomTipoContrato'])?></td>
					<td><?=htmlentities($f['CodFormato'].'-'.$f['NomFormato'])?></td>
					<td align="center"><?=formatFechaDMA($f['FechaDesde'])?></td>
					<td align="center"><?=formatFechaDMA($f['FechaHasta'])?></td>
					<td align="center"><?=printFlag($f['FlagFirma'])?></td>
					<td align="center"><?=formatFechaDMA($f['FechaFirma'])?></td>
				</tr>
				<?php
			}
			?>
		    </tbody>
		</table>
	</div>
</div>

<div id="tab3" style=" <?=$tab3?>">
	<table class="tblBotones" style="width:100%; min-width:<?=$_width?>px;">
	    <tr>
	        <td align="right">
	            <input type="button" value="Ver" style="width:75px;" class="ver" onclick="cargarOpcion2(this.form, 'gehen.php?anz=rh_contratos_form&opcion=ver', 'SELF', '', $('#sel_registros').val());" />
	        </td>
	    </tr>
	</table>
	<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px;">
		<table class="tblLista" style="width:100%; min-width:1100px;">
			<thead>
			    <tr>
			        <th width="55">Empleado</th>
			        <th align="left">Nombre Completo</th>
			        <th width="60">Nro. Documento</th>
			        <th width="125">Tipo de Contrato</th>
			        <th width="125">Formato de Contrato</th>
			        <th width="75">Inicio de Contrato</th>
			        <th width="75">Fin de Contrato</th>
			        <th width="50">Firma?</th>
			        <th width="75">Fecha de Firma</th>
			        <th width="60">Dias para Vencer</th>
			    </tr>
		    </thead>
		    
		    <tbody id="lista_registros">
			<?php
			//	consulto lista
			$sql = "SELECT
						c.*,
						p.Ndocumento,
						p.NomCompleto,
						e.CodEmpleado,
						fc.Documento AS NomFormato,
						tc.TipoContrato,
						tc.Descripcion AS NomTipoContrato,
						DATEDIFF(c.FechaHasta, CURRENT_DATE()) AS DiasParaVencer
					FROM
						rh_contratos c
						INNER JOIN mastpersonas p ON (p.CodPersona = c.CodPersona)
						INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
						LEFT JOIN rh_formatocontrato fc ON (fc.CodFormato = c.CodFormato)
						LEFT JOIN rh_tipocontrato tc ON (tc.TipoContrato = fc.TipoContrato)
					WHERE c.Estado = 'VI' AND DATEDIFF(c.FechaHasta, CURRENT_DATE()) <= ".intval($_PARAMETRO['VENCON'])." $filtro
					ORDER BY CodEmpleado";
			$field = getRecords($sql);
			$rows_lista = count($field);
			foreach($field as $f) {
				$id = $f['CodContrato'];
				?>
				<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
					<td align="center"><?=$f['CodEmpleado']?></td>
					<td><?=htmlentities($f['NomCompleto'])?></td>
					<td align="right"><?=number_format($f['Ndocumento'],0,'','.')?></td>
					<td><?=htmlentities($f['TipoContrato'].'-'.$f['NomTipoContrato'])?></td>
					<td><?=htmlentities($f['CodFormato'].'-'.$f['NomFormato'])?></td>
					<td align="center"><?=formatFechaDMA($f['FechaDesde'])?></td>
					<td align="center"><?=formatFechaDMA($f['FechaHasta'])?></td>
					<td align="center"><?=printFlag($f['FlagFirma'])?></td>
					<td align="center"><?=formatFechaDMA($f['FechaFirma'])?></td>
					<td align="center"><?=$f['DiasParaVencer']?></td>
				</tr>
				<?php
			}
			?>
		    </tbody>
		</table>
	</div>
</div>

<div id="tab4" style=" <?=$tab4?>">
	<table class="tblBotones" style="width:100%; min-width:<?=$_width?>px;">
	    <tr>
	        <td align="right">
	            <input type="button" value="Nuevo" style="width:75px;" class="insert" onclick="cargarOpcion2(this.form, 'gehen.php?anz=rh_contratos_form&opcion=nuevo', 'SELF', '', $('#sel_registros').val());" />
	        </td>
	    </tr>
	</table>
	<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px;">
		<table class="tblLista" style="width:100%; min-width:1400px;">
			<thead>
		    <tr>
		        <th width="55">Empleado</th>
		        <th width="300" align="left">Nombre Completo</th>
		        <th width="60">Nro. Documento</th>
		        <th width="75">Fecha de Ingreso</th>
		        <th align="left">Cargo</th>
		        <th align="left">Dependencia</th>
		    </tr>
		    </thead>
		    
		    <tbody id="lista_registros">
			<?php
			//	consulto lista
			$sql = "SELECT
						c.*,
						p.Ndocumento,
						p.NomCompleto,
						e.CodEmpleado,
						e.Fingreso,
						pt.DescripCargo,
						d.Dependencia,
						DATEDIFF(c.FechaHasta, CURRENT_DATE()) AS DiasParaVencer
					FROM
						rh_contratos c
						INNER JOIN mastpersonas p ON (p.CodPersona = c.CodPersona)
						INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
						INNER JOIN mastdependencias d On (d.CodDependencia = e.CodDependencia)
						INNER JOIN rh_puestos pt ON (pt.CodCargo = e.CodCargo)
						LEFT JOIN rh_formatocontrato fc ON (fc.CodFormato = c.CodFormato)
						LEFT JOIN rh_tipocontrato tc ON (tc.TipoContrato = fc.TipoContrato)
					WHERE c.Estado = 'PE' $filtro
					ORDER BY CodEmpleado";
			$field = getRecords($sql);
			$rows_lista = count($field);
			foreach($field as $f) {
				$id = $f['CodContrato'];
				?>
				<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
					<td align="center"><?=$f['CodEmpleado']?></td>
					<td><?=htmlentities($f['NomCompleto'])?></td>
					<td align="right"><?=number_format($f['Ndocumento'],0,'','.')?></td>
					<td align="center"><?=formatFechaDMA($f['Fingreso'])?></td>
					<td><?=htmlentities($f['DescripCargo'])?></td>
					<td><?=htmlentities($f['Dependencia'])?></td>
				</tr>
				<?php
			}
			?>
		    </tbody>
		</table>
	</div>
</div>

</form>

<script type="text/javascript">
	function abrirContrato() {
		var CodContrato = $('#sel_registros').val();
		if (CodContrato == '') cajaModal("Debe seleccionar un registro","error");
		else {
			window.open("rh_contratos_abrir.php?CodContrato="+CodContrato, "contrato", "toolbar=no, menubar=no, location=no, scrollbars=yes");
		}
	}
</script>