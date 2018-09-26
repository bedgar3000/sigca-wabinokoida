<?php
if ($lista == "todos") {
	if ($filtrar == "default") {
		$fEstado = "PR";
		$fFechaPreparadoD = "01-$MesActual-$AnioActual";
		$fFechaPreparadoH = "$DiaActual-$MesActual-$AnioActual";
		$fPeriodo = $PeriodoActual;
	}
	##
	$_titulo = "Listado de Vi&aacute;ticos";
	$btNuevo = "";
	$btModificar = "";
	$btRevisar = "display:none;";
	$btGenerar = "display:none;";
	$btRelacionar = "";
	$btCopiar = "";
	$btAnular = "";
	$btVer = "";
	$btImprimir = "";
}
elseif ($lista == "revisar") {
	$fEstado = "PR";
	##
	$_titulo = "Revisar Vi&aacute;ticos";
	$btNuevo = "display:none;";
	$btModificar = "display:none;";
	$btRevisar = "";
	$btGenerar = "display:none;";
	$btRelacionar = "display:none;";
	$btCopiar = "display:none;";
	$btAnular = "";
	$btVer = "";
	$btImprimir = "";
}
elseif ($lista == "generar") {
	$fEstado = "RV";
	##
	$_titulo = "Generar Obligaciones";
	$btNuevo = "display:none;";
	$btModificar = "display:none;";
	$btRevisar = "display:none;";
	$btGenerar = "";
	$btRelacionar = "display:none;";
	$btCopiar = "display:none;";
	$btAnular = "";
	$btVer = "";
	$btImprimir = "";
}
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "CodViatico";
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (v.CodOrganismo LIKE '%".$fBuscar."%' OR
					  v.CodViatico LIKE '%".$fBuscar."%' OR
					  v.CodInterno LIKE '%".$fBuscar."%' OR
					  v.Periodo LIKE '%".$fBuscar."%' OR
					  v.Motivo LIKE '%".$fBuscar."%' OR
					  v.Monto LIKE '%".setNumero($fBuscar)."%' OR
					  v.FechaPreparado LIKE '%".formatFechaAMD($fBuscar)."%' OR
					  v.FechaRevisado LIKE '%".formatFechaAMD($fBuscar)."%' OR
					  p.NomCompleto LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (v.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (v.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fCodDependencia != "") { $cCodDependencia = "checked"; $filtro.=" AND (v.CodDependencia = '".$fCodDependencia."')"; } else $dCodDependencia = "disabled";
if ($fPeriodo != "") { $cPeriodo = "checked"; $filtro.=" AND (v.Periodo = '".$fPeriodo."')"; } else $dPeriodo = "disabled";
if ($fFechaPreparadoD != "" || $fFechaPreparadoH != "") {
	$cFechaPreparado = "checked";
	if ($fFechaPreparadoD != "") $filtro.=" AND (v.FechaPreparado >= '".formatFechaAMD($fFechaPreparadoD)."')";
	if ($fFechaPreparadoH != "") $filtro.=" AND (v.FechaPreparado <= '".formatFechaAMD($fFechaPreparadoH)."')";
} else $dFechaPreparado = "disabled";
//	------------------------------------
$_width = 900;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=ap_viaticos_lista" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />

<!--FILTRO-->
<div class="divBorder" style="width:<?=$_width?>px;">
<table width="<?=$_width?>" class="tblFiltro">
	<tr>
		<td align="right">Organismo: </td>
		<td>
            <input type="checkbox" <?=$cCodOrganismo?> onclick="chkFiltro(this.checked, 'fCodOrganismo');" />
            <select name="fCodOrganismo" id="fCodOrganismo" style="width:300px;" <?=$dCodOrganismo?> onChange="getOptionsSelect(this.value, 'dependencia_filtro', 'fCodDependencia', true);">
                <option value="">&nbsp;</option>
                <?=getOrganismos($fCodOrganismo, 3)?>
            </select>
        </td>
		<td align="right">Estado: </td>
		<td>
        	<?php
			if ($lista == "revisar" || $lista == "generar") {
				?>
                <input type="checkbox" <?=$cEstado?> onclick="this.checked=!this.checked;" />
                <select name="fEstado" id="fEstado" style="width:145px;" <?=$dEstado?>>
                    <?=loadSelectValores("ESTADO-VIATICOS", $fEstado, 1)?>
                </select>
                <?php
			} else {
				?>
                <input type="checkbox" <?=$cEstado?> onclick="chkFiltro(this.checked, 'fEstado');" />
                <select name="fEstado" id="fEstado" style="width:145px;" <?=$dEstado?>>
                    <option value="">&nbsp;</option>
                    <?=loadSelectValores("ESTADO-VIATICOS", $fEstado, 0)?>
                </select>
                <?php
			}
			?>
        </td>
	</tr>
	<tr>
		<td align="right">Dependencia:</td>
		<td>
			<input type="checkbox" <?=$cCodDependencia?> onclick="chkFiltro(this.checked, 'fCodDependencia');" />
			<select name="fCodDependencia" id="fCodDependencia" style="width:300px;" <?=$dCodDependencia?>>
            	<option value="">&nbsp;</option>
				<?=getDependencias($fCodDependencia, $fCodOrganismo, 3)?>
			</select>
		</td>
		<td align="right">Fecha Preparaci&oacute;n:</td>
		<td>
			<input type="checkbox" <?=$cFechaPreparado?> onclick="chkCampos2(this.checked, ['fFechaPreparadoD','fFechaPreparadoH']);" />
			<input type="text" name="fFechaPreparadoD" id="fFechaPreparadoD" value="<?=$fFechaPreparadoD?>" style="width:65px;" class="datepicker" maxlength="10" <?=$dFechaPreparado?> />
			<input type="text" name="fFechaPreparadoH" id="fFechaPreparadoH" value="<?=$fFechaPreparadoH?>" style="width:65px;" class="datepicker" maxlength="10" <?=$dFechaPreparado?> />
		</td>
	</tr>
	<tr>
		<td align="right">Buscar:</td>
		<td>
			<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
			<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:195px;" <?=$dBuscar?> />
		</td>
		<td align="right">Periodo:</td>
		<td>
			<input type="checkbox" <?=$cPeriodo?> onclick="chkCampos(this.checked, 'fPeriodo');" />
			<input type="text" name="fPeriodo" id="fPeriodo" value="<?=$fPeriodo?>" style="width:65px;" maxlength="7" <?=$dPeriodo?> />
		</td>
	</tr>
</table>
</div>
<center><input type="submit" value="Buscar"></center><br />

<!--REGISTROS-->
<center>
<input type="hidden" name="sel_registros" id="sel_registros" />
<table width="<?=$_width?>" class="tblBotones">
    <tr>
        <td><div id="rows"></div></td>
        <td align="right" class="gallery clearfix">
            <input type="button" value="Nuevo" style="width:75px; <?=$btNuevo?>" class="insert" onclick="cargarPagina(this.form, 'gehen.php?anz=ap_viaticos_form&opcion=nuevo&origen=ap_viaticos_lista');" />
            <input type="button" value="Modificar" style="width:75px; <?=$btModificar?>" class="update" onclick="cargarOpcionValidar3(this.form, $('#sel_registros').val(), 'ap_viaticos_ajax.php', 'modulo=validar&accion=viaticos_modificar', 'gehen.php?anz=ap_viaticos_form&opcion=modificar&origen=ap_viaticos_lista', 'SELF', '');" />
            <input type="button" value="Ver" style="width:75px; <?=$btVer?>" class="ver" onclick="cargarOpcion2(this.form, 'gehen.php?anz=ap_viaticos_form&opcion=ver&origen=ap_viaticos_lista', 'SELF', '', $('#sel_registros').val());" /> |
            <input type="button" value="Relacionar" style="width:75px; <?=$btRelacionar?>" class="update" onclick="cargarOpcionValidar3(this.form, $('#sel_registros').val(), 'ap_viaticos_ajax.php', 'modulo=validar&accion=viaticos_relacionar', 'gehen.php?anz=ap_viaticos_form&opcion=relacionar&origen=ap_viaticos_lista', 'SELF', '');" />
            <input type="button" value="Copiar" style="width:75px; <?=$btCopiar?>" class="update" onclick="cargarOpcion2(this.form, 'gehen.php?anz=ap_viaticos_form&opcion=copiar&origen=ap_viaticos_lista', 'SELF', '', $('#sel_registros').val());" />
            <input type="button" value="Revisar" style="width:75px; <?=$btRevisar?>" class="update" onclick="cargarOpcion2(this.form, 'gehen.php?anz=ap_viaticos_form&opcion=revisar&origen=ap_viaticos_lista', 'SELF', '', $('#sel_registros').val());" />
            <input type="button" value="Generar" style="width:75px; <?=$btGenerar?>" class="update" onclick="cargarOpcionValidar3(this.form, $('#sel_registros').val(), 'ap_viaticos_ajax.php', 'modulo=validar&accion=generar', 'gehen.php?anz=ap_obligacion_form&opcion=viaticos-generar&origen=ap_viaticos_lista', 'SELF', '');" />
            <input type="button" value="Anular" style="width:75px; <?=$btAnular?>" class="update" onclick="cargarOpcionValidar3(this.form, $('#sel_registros').val(), 'ap_viaticos_ajax.php', 'modulo=validar&accion=viaticos_anular', 'gehen.php?anz=ap_viaticos_form&opcion=anular&origen=ap_viaticos_lista', 'SELF', '');" /> |
        	<a href="pagina.php?iframe=true" rel="prettyPhoto[iframe1]" style="display:none;" id="a_imprimir"></a>
            <input type="button" value="Imprimir" style="width:75px; <?=$btImprimir?>" class="ver" onclick="abrirIFrame(this.form, 'a_imprimir', 'ap_viaticos_pdf.php?', '100%', '100%', $('#sel_registros').val());" />
        </td>
    </tr>
</table>

<div style="overflow:scroll; width:<?=$_width?>px; height:300px;">
<table width="1750" class="tblLista">
	<thead>
    <tr>
        <th width="75" onclick="order('CodViatico')">Vi&aacute;tico</th>
        <th width="75" onclick="order('CodInterno')">Nro. Interno</th>
        <th width="75" onclick="order('Periodo')">Periodo</th>
        <th width="350" align="left" onclick="order('NomPersona')">Beneficiario</th>
        <th width="100" align="right" onclick="order('Monto')">Monto</th>
        <th width="100" onclick="order('Estado')">Estado</th>
        <th width="80" onclick="order('FechaPreparado')">Fecha Preparaci&oacute;n</th>
        <th width="80" onclick="order('FechaRevisado')">Fecha Revisi&oacute;n</th>
        <th align="left" onclick="order('Motivo')">Motivo</th>
    </tr>
    </thead>
    
    <tbody id="lista_registros">
	<?php
	//	consulto todos
	$sql = "SELECT
				v.CodOrganismo,
				v.CodViatico
			FROM
				ap_viaticos v
				INNER JOIN mastpersonas p ON (p.CodPersona = v.CodPersona)
			WHERE 1 $filtro";
	$rows_total = getNumRows3($sql);
	
	//	consulto lista
	$sql = "SELECT
				v.CodOrganismo,
				v.CodViatico,
				v.CodInterno,
				v.Periodo,
				v.Motivo,
				v.Monto,
				v.FechaPreparado,
				v.FechaRevisado,
				v.Estado,
				p.NomCompleto AS NomPersona
			FROM
				ap_viaticos v
				INNER JOIN mastpersonas p ON (p.CodPersona = v.CodPersona)
			WHERE 1 $filtro
			ORDER BY $fOrderBy
			LIMIT ".intval($limit).", ".intval($maxlimit);
	$field = getRecords($sql);
	$rows_lista = count($field);
	foreach($field as $f) {
		$id = $f['CodOrganismo']."_".$f['CodViatico'];
		?>
		<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
			<td align="center"><?=$f['CodViatico']?></td>
			<td align="center"><?=$f['CodInterno']?></td>
			<td align="center"><?=$f['Periodo']?></td>
			<td><?=htmlentities($f['NomPersona'])?></td>
			<td align="right"><?=number_format($f['Monto'], 2, ',', '.')?></td>
			<td align="center"><?=printValores("ESTADO-VIATICOS", $f['Estado'])?></td>
			<td align="center"><?=formatFechaDMA(substr($f['FechaPreparado'], 0, 10))?></td>
			<td align="center"><?=formatFechaDMA($f['FechaRevisado'])?></td>
			<td><?=substr(htmlentities($f['Motivo']), 0, 200);?>...</td>
		</tr>
		<?php
	}
	?>
    </tbody>
</table>
</div>
<table width="<?=$_width?>">

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
</center>
</form>