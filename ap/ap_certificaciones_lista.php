<?php
//	------------------------------------
if ($lista == "listar" || $lista == "listar-obras") {
	$_titulo = "Gastos Directos";
	$_btNuevo = "";
	$_btModificar = "";
	$_btRevisar = "display:none;";
	$_btGenerar = "display:none;";
	$_btImprimir = "";
}
elseif ($lista == "revisar" || $lista == "revisar-obras") {
	$_titulo = "Gastos Directos - Revisar";
	$_btNuevo = "display:none;";
	$_btModificar = "display:none;";
	$_btRevisar = "";
	$_btGenerar = "display:none;";
	$_btImprimir = "";
}
elseif ($lista == "generar") {
	$_titulo = "Gastos Directos - Generar Obligaci&oacute;n";
	$_btNuevo = "display:none;";
	$_btModificar = "display:none;";
	$_btRevisar = "display:none;";
	$_btGenerar = "";
	$_btImprimir = "display:none;";
}
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	if ($lista == "listar" || $lista == "listar-obras") {
		$fFechaD = "01-$MesActual-$AnioActual";
		$fFechaH = formatFechaDMA($FechaActual);
		if ($lista == "listar-obras") $fCodTipoCertif = '09';
	}
	elseif ($lista == "revisar" || $lista == "revisar-obras") {
		$fEstado = 'PR';
		if ($lista == "revisar-obras") $fCodTipoCertif = '09';
	}
	elseif ($lista == "generar") $fEstado = 'RV';
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "Codigo";
}
$filtro = '';
if ($lista != "listar-obras" && $lista != "revisar-obras") {
	$filtro .= " AND c.CodTipoCertif <> '09'";
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (CONCAT(c.CodTipoCertif, '-', c.Anio, '-', c.CodInterno) LIKE '%".$fBuscar."%'
					  OR c.CodTipoCertif LIKE '%".$fBuscar."%'
					  OR c.Anio LIKE '%".$fBuscar."%'
					  OR c.CodInterno LIKE '%".$fBuscar."%'
					  OR c.Justificacion LIKE '%".$fBuscar."%'
					  OR p.NomCompleto LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (c.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (c.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fCodTipoCertif != "") { $cCodTipoCertif = "checked"; $filtro.=" AND (c.CodTipoCertif = '".$fCodTipoCertif."')"; } else $dCodTipoCertif = "disabled";
if ($fCodPersona != "") { $cCodPersona = "checked"; $filtro.=" AND (c.CodPersona = '".$fCodPersona."')"; } else $dCodPersona = "visibility:hidden;";
if ($fFechaD != "") { 
	$cFecha = "checked"; 
	if ($fFechaD != "") $filtro.=" AND (c.Fecha >= '".formatFechaAMD($fFechaD)."')";
	if ($fFechaH != "") $filtro.=" AND (c.Fecha <= '".formatFechaAMD($fFechaH)."')";
} else $dFecha = "disabled";
//	------------------------------------
$_width = 1000;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=ap_certificaciones_lista" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />

<!--FILTRO-->
<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
	<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
		<tr>
			<td align="right">Organismo:</td>
			<td>
				<input type="checkbox" <?=$cCodOrganismo?> onclick="this.checked=!this.checked;" />
				<select name="fCodOrganismo" id="fCodOrganismo" style="width:250px;" <?=$dCodOrganismo?>>
					<?=getOrganismos($fCodOrganismo, 3)?>
				</select>
			</td>
			<td align="right">Tipo:</td>
			<td>
				<?php
				if ($lista == "listar-obras" || $lista == "revisar-obras") {
					?>
		            <input type="checkbox" <?=$cCodTipoCertif?> onclick="this.checked=!this.checked;" />
		            <select name="fCodTipoCertif" id="fCodTipoCertif" style="width:133px;" <?=$dCodTipoCertif?>>
		                <?=loadSelect2("ap_tiposcertificacion","CodTipoCertif","Descripcion",$fCodTipoCertif,11)?>
		            </select>
					<?php
				} else {
					?>
					<input type="checkbox" <?=$cCodTipoCertif?> onclick="chkCampos(this.checked, 'fCodTipoCertif');" />
					<select name="fCodTipoCertif" id="fCodTipoCertif" style="width:133px;" <?=$dCodTipoCertif?>>
						<option value="">&nbsp;</option>
						<?=loadSelectTiposCertificacion($fCodTipoCertif,10)?>
					</select>
					<?php
				}
				?>
			</td>
			<td align="right">Fecha: </td>
			<td>
				<input type="checkbox" <?=$cFecha?> onclick="chkCampos2(this.checked, ['fFechaD','fFechaH']);" />
				<input type="text" name="fFechaD" id="fFechaD" value="<?=$fFechaD?>" style="width:65px;" maxlength="10" class="datepicker" <?=$dFecha?> />
				<input type="text" name="fFechaH" id="fFechaH" value="<?=$fFechaH?>" style="width:65px;" maxlength="10" class="datepicker" <?=$dFecha?> />
			</td>
	        <td>&nbsp;</td>
		</tr>
		<tr>
			<td align="right">Beneficiario: </td>
			<td class="gallery clearfix">
	            <input type="checkbox" <?=$cCodPersona?> onClick="ckLista(this.checked,['fCodPersona','fNomPersona'],['btCodPersona'])" />
				<input type="text" name="fCodPersona" id="fCodPersona" value="<?=$fCodPersona?>" style="width:45px;" readonly />
				<input type="text" name="fNomPersona" id="fNomPersona" value="<?=$fNomPersona?>" style="width:202px;" readonly />
	            <a href="../lib/listas/gehen.php?anz=lista_personas&filtrar=default&campo1=fCodPersona&campo2=fNomPersona&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" id="btCodPersona" style=" <?=$dCodPersona?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
	        </td>
			<td align="right">Buscar:</td>
			<td>
				<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
				<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:133px;" <?=$dBuscar?> />
			</td>
			<td align="right">Estado: </td>
			<td>
				<?php
				if ($lista == "listar" || $lista == "listar-obras") {
					?>
		            <input type="checkbox" <?=$cEstado?> onclick="chkFiltro(this.checked, 'fEstado');" />
		            <select name="fEstado" id="fEstado" style="width:133px;" <?=$dEstado?>>
		                <option value="">&nbsp;</option>
		                <?=loadSelectValores("certificaciones-estado", $fEstado, 0)?>
		            </select>
					<?php
				} else {
					?>
		            <input type="checkbox" <?=$cEstado?> onclick="this.checked=!this.checked;" />
		            <select name="fEstado" id="fEstado" style="width:133px;" <?=$dEstado?>>
		                <?=loadSelectValores("certificaciones-estado", $fEstado, 1)?>
		            </select>
					<?php
				}
				?>
			</td>
	        <td align="right"><input type="submit" value="Buscar"></td>
		</tr>
	</table>
</div>
<div class="sep"></div>

<!--REGISTROS-->
<input type="hidden" name="sel_registros" id="sel_registros" />
<table class="tblBotones" style="width:100%; min-width:<?=$_width?>px; margin:auto;">
    <tr>
        <td><div id="rows"></div></td>
        <td align="right" class="gallery clearfix">
            <input type="button" value="Nuevo" style="width:75px; <?=$_btNuevo?>" class="insert" onclick="cargarPagina(this.form, 'gehen.php?anz=ap_certificaciones_form&opcion=nuevo&return=ap_certificaciones_lista');" />
            <input type="button" value="Modificar" style="width:75px; <?=$_btModificar?>" class="update" onclick="cargarOpcionValidar3(this.form, $('#sel_registros').val(), 'ap_certificaciones_ajax.php', 'modulo=validar&accion=modificar', 'gehen.php?anz=ap_certificaciones_form&opcion=modificar&action=ap_certificaciones_lista', 'SELF', '');" />
            <input type="button" value="Ver" style="width:75px;" class="ver" onclick="cargarOpcion2(this.form, 'gehen.php?anz=ap_certificaciones_form&opcion=ver', 'SELF', '', $('#sel_registros').val());" /> |
            <input type="button" value="Revisar" style="width:75px; <?=$_btRevisar?>" class="update" onclick="cargarOpcionValidar3(this.form, $('#sel_registros').val(), 'ap_certificaciones_ajax.php', 'modulo=validar&accion=revisar', 'gehen.php?anz=ap_certificaciones_form&opcion=revisar&action=ap_certificaciones_lista', 'SELF', '');" />
            <input type="button" value="Generar" style="width:75px; <?=$_btGenerar?>" class="update" onclick="cargarOpcionValidar3(this.form, $('#sel_registros').val(), 'ap_certificaciones_ajax.php', 'modulo=validar&accion=generar', 'gehen.php?anz=ap_obligacion_form&opcion=certificaciones-generar&origen=ap_certificaciones_lista', 'SELF', '');" />
            <input type="button" value="Anular" style="width:75px; <?=$_btAnular?>" class="update" onclick="cargarOpcionValidar3(this.form, $('#sel_registros').val(), 'ap_certificaciones_ajax.php', 'modulo=validar&accion=anular', 'gehen.php?anz=ap_certificaciones_form&opcion=anular&action=ap_certificaciones_lista', 'SELF', '');" />

            <a href="pagina.php?iframe=true" rel="prettyPhoto[iframe2]" style="display:none;" id="a_imprimir"></a>
            <input type="button" id="btImprimir" value="Imprimir" style="width:75px; <?=$_btImprimir?>" onclick="abrirIFrame(this.form, 'a_imprimir', 'ap_certificaciones_pdf.php?', '100%', '100%', $('#sel_registros').val());" />
        </td>
    </tr>
</table>

<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px; margin:auto;">
	<table class="tblLista" style="width:100%; min-width:<?=$_width?>px;">
		<thead>
		    <tr>
		        <th width="80" onclick="order('Codigo')">C&oacute;digo</th>
		        <th width="65" onclick="order('Fecha')">Fecha</th>
		        <th width="80" onclick="order('Estado')">Estado</th>
		        <th width="350" align="left" onclick="order('NomCompleto')">Beneficiario</th>
		        <th width="100" align="right" onclick="order('Monto')">Monto</th>
		        <th align="left" onclick="order('Justificacion')">Justificaci&oacute;n</th>
		    </tr>
	    </thead>
	    
	    <tbody id="lista_registros">
		<?php
		//	consulto todos
		$sql = "SELECT c.*
				FROM
					ap_certificaciones c
					INNER JOIN mastpersonas p ON (p.CodPersona = c.CodPersona)
				WHERE 1 $filtro";
		$rows_total = getNumRows3($sql);
		//	consulto lista
		$sql = "SELECT
					c.*,
					CONCAT(c.CodTipoCertif, '-', c.Anio, '-', c.CodInterno) AS Codigo,
					p.NomCompleto
				FROM
					ap_certificaciones c
					INNER JOIN mastpersonas p ON (p.CodPersona = c.CodPersona)
				WHERE 1 $filtro
				ORDER BY $fOrderBy
				LIMIT ".intval($limit).", ".intval($maxlimit);
		$field = getRecords($sql);
		$rows_lista = count($field);
		foreach($field as $f) {
			$id = $f['CodCertificacion'];
			?>
			<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
				<td align="center"><?=$f['Codigo']?></td>
				<td align="center"><?=formatFechaDMA($f['Fecha'])?></td>
				<td align="center"><?=printValores('certificaciones-estado',$f['Estado'])?></td>
				<td><?=htmlentities($f['NomCompleto'])?></td>
				<td align="right"><?=number_format($f['Monto'],2,',','.')?></td>
				<td><?=htmlentities($f['Justificacion'])?></td>
			</tr>
			<?php
		}
		?>
	    </tbody>
	</table>
</div>

<table style="width:100%; min-width:<?=$_width?>px; margin:auto;">
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