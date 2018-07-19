<?php
//	------------------------------------
if ($filtrar == "default") {
	if ($ventana != 'at_componenteshard' && $ventana != 'at_activos_agrupar' && $ventana != 'at_prestamos') {
		$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
		if ($ventana != 'at_soportetecnico') $fCodDependencia = $_SESSION["DEPENDENCIA_ACTUAL"];
	}
	$fNaturaleza = $_PARAMETRO['CATDEPRDEFECT'];
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "Activo";
}
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (a.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fCodDependencia != "") { $cCodDependencia = "checked"; $filtro.=" AND (a.CodDependencia = '".$fCodDependencia."')"; } else $dCodDependencia = "disabled";
if ($fCodCentroCosto != "") { $cCodCentroCosto = "checked"; $filtro.=" AND (a.CodCentroCosto = '".$fCodCentroCosto."')"; } else $dCodCentroCosto = "disabled";
if ($fNaturaleza != "") { $cNaturaleza = "checked"; $filtro.=" AND (a.Naturaleza = '".$fNaturaleza."')"; } else $dNaturaleza = "disabled";
if ($fCodLinea != "") { $cCodLinea = "checked"; $filtro.=" AND (a.CodLinea = '".$fCodLinea."')"; } else $dCodLinea = "visibility:hidden;";
if ($fCodFamilia != "") { $cCodFamilia = "checked"; $filtro.=" AND (a.CodFamilia = '".$fCodFamilia."')"; } else $dCodFamilia = "visibility:hidden;";
if ($fCodSubFamilia != "") { $cCodSubFamilia = "checked"; $filtro.=" AND (a.CodSubFamilia = '".$fCodSubFamilia."')"; } else $dCodSubFamilia = "visibility:hidden;";
if ($fUsuario != "") { $cUsuario = "checked"; $filtro.=" AND (a.EmpleadoUsuario = '".$fUsuario."')"; } else $dUsuario = "visibility:hidden;";
//	------------------------------------
$_titulo = "Activos Tecnol&oacute;gicos";
$_width = 800;
?>
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lista_at_activos" method="post">
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

<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
	<tr>
		<td align="right" width="125">Organismo:</td>
		<td>
			<input type="checkbox" <?=$cCodOrganismo?> onclick="this.checked=!this.checked" />
			<select name="fCodOrganismo" id="fCodOrganismo" style="width:260px;" <?=$dCodOrganismo?> onChange="loadSelect($('#fCodDependencia'), 'CodOrganismo='+$('#fCodOrganismo').val(), 1, 'fCodCentrocosto');">
				<?php
                if ($ventana == 'at_componenteshard' || $ventana == 'at_activos_agrupar') getOrganismos($fCodOrganismo, 1);
				else getOrganismos($fCodOrganismo, 3);
				?>
			</select>
		</td>
		<td class="tagForm" width="125">Linea:</td>
		<td class="gallery clearfix">
			<input type="checkbox" id="cCodLinea" <?=$cCodLinea?> onclick="ckLista(this.checked, ['fCodLinea'], ['bCodLinea']);" />
        	<input type="text" name="fCodLinea" id="fCodLinea" value="<?=$fCodLinea?>" style="width:50px; font-weight:bold;" readonly="readonly" />
            <a href="javascript:" onclick="window.open('gehen.php?anz=lista_at_linea&filtrar=default&campo1=fCodLinea&ventana=selListaOpener','lista_at_linea','width=950, height=430, toolbar=no, menubar=no, location=no, scrollbars=yes, left=0, top=0, resizable=no')" style=" <?=$dCodLinea?>" id="bCodLinea">
            	<img src="../../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
		</td>
        <td>&nbsp;</td>
	</tr>
	<tr>
		<td align="right">Dependencia:</td>
		<td>
        	<?php
			if ($ventana == 'at_componenteshard' || $ventana == 'at_activos_agrupar' || $ventana == 'at_prestamos') {
				?>
                <input type="checkbox" <?=$cCodDependencia?> onclick="this.checked=!this.checked;" />
                <select name="fCodDependencia" id="fCodDependencia" style="width:260px;" onChange="loadSelect($('#fCodDependencia'), 'CodDependencia='+$('#fCodDependencia').val(), 1);" <?=$dCodDependencia?>>
                    <?=getDependencias($fCodDependencia, $fCodOrganismo, 1);?>
                </select>
                <?php
			} else {
				?>
                <input type="checkbox" <?=$cCodDependencia?> onclick="chkFiltro(this.checked, 'fCodDependencia');" />
                <select name="fCodDependencia" id="fCodDependencia" style="width:260px;" onChange="loadSelect($('#fCodDependencia'), 'CodDependencia='+$('#fCodDependencia').val(), 1);" <?=$dCodDependencia?>>
                    <option value="">&nbsp;</option>
                    <?=getDependencias($fCodDependencia, $fCodOrganismo, 3);?>
                </select>
                <?php
			}
			?>
		</td>
		<td class="tagForm">Familia:</td>
		<td class="gallery clearfix">
			<input type="checkbox" id="cCodFamilia" <?=$cCodFamilia?> onclick="ckLista(this.checked, ['fCodFamilia'], ['bCodFamilia']);" />
        	<input type="text" name="fCodFamilia" id="fCodFamilia" value="<?=$fCodFamilia?>" style="width:50px; font-weight:bold;" readonly="readonly" />
            <a href="javascript:" onclick="window.open('gehen.php?anz=lista_at_familia&filtrar=default&campo1=fCodLinea&campo2=fCodFamilia&ventana=selListaOpener','lista_at_familia','width=950, height=430, toolbar=no, menubar=no, location=no, scrollbars=yes, left=0, top=0, resizable=no')" style=" <?=$dCodFamilia?>" id="bCodFamilia">
            	<img src="../../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
		</td>
        <td>&nbsp;</td>
	</tr>
	<tr>
		<td align="right">Centro de Costo:</td>
		<td>
			<input type="checkbox" <?=$cCodCentroCosto?> onclick="chkFiltro(this.checked, 'fCodCentroCosto');" />
			<select name="fCodCentroCosto" id="fCodCentroCosto" style="width:260px;" <?=$dCodCentroCosto?>>
            	<option value="">&nbsp;</option>
				<?=loadSelect("ac_mastcentrocosto", "CodCentroCosto", "Descripcion", $fCodCentroCosto, 0)?>
			</select>
		</td>
		<td class="tagForm">Sub-Familia:</td>
		<td class="gallery clearfix">
			<input type="checkbox" <?=$cCodSubFamilia?> onclick="ckLista(this.checked, ['fCodLinea','fCodFamilia','fCodSubFamilia'], ['bCodSubFamilia']);" />
        	<input type="text" name="fCodSubFamilia" id="fCodSubFamilia" value="<?=$fCodSubFamilia?>" style="width:50px; font-weight:bold;" readonly="readonly" />
            <a href="javascript:" onclick="window.open('gehen.php?anz=lista_at_subfamilia&filtrar=default&campo1=fCodLinea&campo2=fCodFamilia&campo3=fCodSubFamilia&ventana=selListaOpener','lista_at_subfamilia','width=950, height=430, toolbar=no, menubar=no, location=no, scrollbars=yes, left=0, top=0, resizable=no')" style=" <?=$dCodLinea?>" id="bCodSubFamilia">
            	<img src="../../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
		</td>
        <td>&nbsp;</td>
	</tr>
	<tr>
		<td align="right">Usuario:</td>
		<td class="gallery clearfix">
            <input type="checkbox" <?=$cUsuario?> onclick="ckLista(this.checked, ['fUsuario','fNomUsuario'], ['aUsuario']);" />
        	<input type="text" name="fUsuario" id="fUsuario" value="<?=$fUsuario?>" style="width:40px;" readonly />
        	<input type="text" name="fNomUsuario" id="fNomUsuario" value="<?=$fNomUsuario?>" style="width:205px;" readonly />
            <a href="javascript:" onclick="window.open('gehen.php?anz=lista_empleados&filtrar=default&campo1=fUsuario&campo2=fNomUsuario&ventana=selListaOpener','lista_empleados','width=950, height=430, toolbar=no, menubar=no, location=no, scrollbars=yes, left=0, top=0, resizable=no')" id="aUsuario" style=" <?=$dUsuario?>">
            	<img src="../../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
		</td>
		<td align="right">Naturaleza:</td>
		<td>
			<input type="checkbox" <?=$cNaturaleza?> onclick="chkFiltro(this.checked, 'fNaturaleza');" />
			<select name="fNaturaleza" id="fNaturaleza" style="width:125px;" <?=$dNaturaleza?>>
				<?=loadSelectGeneral('activo-naturaleza',$fNaturaleza,0)?>
			</select>
		</td>
        <td align="right"><input type="submit" value="Buscar"></td>
	</tr>
</table>
</div>
<div class="sep"></div>

<center>
<div style="overflow:scroll; height:260px; width:100%; min-width:<?=$_width?>px;">
<table class="tblLista" style="width:100%; min-width:2100px;">
	<thead>
    <tr>
        <th scope="col" width="75" onclick="order('Activo')">Activo</th>
        <th scope="col" width="55" onclick="order('CodigoInterno')">C&oacute;digo Interno</th>
        <th scope="col" width="125" onclick="order('CodigoBarras')">C&oacute;digo Barras</th>
        <th scope="col" align="left" onclick="order('Descripcion')">Descripci&oacute;n</th>
        <th scope="col" width="90" onclick="order('TipoActivo')">Tipo de Activo</th>
        <th scope="col" width="125" onclick="order('SituacionActivo')">Situaci&oacute;n</th>
        <th scope="col" width="90" onclick="order('Categoria')">Categor&iacute;a</th>
        <?php
		if ($_PARAMETRO['CONTONCO'] == 'S') {
			?><th scope="col" width="90" onclick="order('Clasificacion')">Clasificaci&oacute;n</th><?php
		}
		if ($_PARAMETRO['CONTPUB20'] == 'S') {
			?><th scope="col" width="90" onclick="order('ClasificacionPublic20')">Clasificaci&oacute;n (Pub.20)</th><?php
		}
		?>
        <th scope="col" width="60" onclick="order('CentroCosto')">C.C.</th>
        <th scope="col" width="60" onclick="order('CentroCosto')">C.C. Destino</th>
        <th scope="col" width="75" onclick="order('Ubicacion')">Ubicaci&oacute;n</th>
        <th scope="col" width="125" onclick="order('NumeroSerie')">Nro. Serie</th>
        <th scope="col" width="75" onclick="order('EstadoRegistro')">Estado</th>
    </tr>
    </thead>
    
    <tbody>
	<?php
	//	consulto todos
	$sql = "SELECT a.Activo
			FROM
				af_activo a
				INNER JOIN at_activotecnologico at ON (at.Activo = a.Activo)
				LEFT JOIN af_situacionactivo sa ON (sa.CodSituActivo = a.SituacionActivo)
				LEFT JOIN mastmiscelaneosdet md1 ON (md1.CodDetalle = a.TipoActivo AND
												 md1.CodMaestro = 'TIPOACTIVO')
				LEFT JOIN mastpersonas p1 ON (p1.CodPersona = a.EmpleadoUsuario)
				LEFT JOIN mastpersonas p2 ON (p2.CodPersona = a.EmpleadoResponsable)
			WHERE a.FlagParaOperaciones = 'S' $filtro";
	$rows_total = getNumRows3($sql);
	//	consulto lista
	$sql = "SELECT
				a.*,
				sa.Descripcion AS NomSituActivo,
				md1.Descripcion AS NomTipoActivo,
				p1.NomCompleto AS NomEmpleadoUsuario,
				p2.NomCompleto AS NomEmpleadoResponsable,
				u.Descripcion AS NomUbicacion
			FROM
				af_activo a
				INNER JOIN at_activotecnologico at ON (at.Activo = a.Activo)
				LEFT JOIN af_situacionactivo sa ON (sa.CodSituActivo = a.SituacionActivo)
				LEFT JOIN mastmiscelaneosdet md1 ON (md1.CodDetalle = a.TipoActivo AND
												 md1.CodMaestro = 'TIPOACTIVO')
				LEFT JOIN mastpersonas p1 ON (p1.CodPersona = a.EmpleadoUsuario)
				LEFT JOIN mastpersonas p2 ON (p2.CodPersona = a.EmpleadoResponsable)
				LEFT JOIN af_ubicaciones u ON (u.CodUbicacion = a.Ubicacion)
			WHERE a.FlagParaOperaciones = 'S' $filtro
			ORDER BY $fOrderBy
			LIMIT ".intval($limit).", ".intval($maxlimit);
	$field = getRecords($sql);
	$rows_lista = count($field);
	foreach($field as $f) {
		if ($ventana == 'at_componenteshard') {
			?>
            <tr class="trListaBody" onClick="selLista(['<?=$f['Activo']?>','<?=$f['Descripcion']?>','<?=$f['EmpleadoUsuario']?>','<?=$f['NomEmpleadoUsuario']?>','<?=$f['EmpleadoResponsable']?>','<?=$f['NomEmpleadoResponsable']?>','<?=$f['Ubicacion']?>','<?=$f['NomUbicacion']?>'], 
            										  ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>','<?=$campo4?>','<?=$campo5?>','<?=$campo6?>','<?=$campo7?>','<?=$campo8?>']);">
            <?php
		} 
		elseif ($ventana == 'listado_insertar_linea' || $ventana == 'at_activos_agrupar' || $ventana == 'at_prestamos') {
			?>
            <tr class="trListaBody" onClick="listado_insertar_linea('<?=$detalle?>','modulo=<?=$modulo?>&accion=<?=$accion?>&Activo=<?=$f['Activo']?>','<?=$f['Activo']?>','<?=$url?>');">
            <?php
		} 
		elseif ($ventana == 'at_soportetecnico') {
			?>
            <tr class="trListaBody" onClick="selLista(['<?=$f['Activo']?>','<?=$f['Descripcion']?>'], ['<?=$campo1?>','<?=$campo2?>']);">
            <?php
		} 
		else {
			?>
            <tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['Activo']?>','<?=$f['Descripcion']?>'], ['<?=$campo1?>','<?=$campo2?>']);">
            <?php
		}
		?>
			<td align="center"><?=$f['Activo']?></td>
			<td align="center"><?=$f['CodigoInterno']?></td>
			<td align="center"><?=$f['CodigoBarras']?></td>
			<td><?=htmlentities($f['Descripcion'])?></td>
			<td align="center"><?=$f['NomTipoActivo']?></td>
			<td align="center"><?=$f['NomSituActivo']?></td>
			<td align="center"><?=$f['Categoria']?></td>
			<?php
            if ($_PARAMETRO['CONTONCO'] == 'S') {
                ?><td align="center"><?=$f['Clasificacion']?></td><?php
            }
            if ($_PARAMETRO['CONTPUB20'] == 'S') {
                ?><td align="center"><?=$f['ClasificacionPublic20']?></td><?php
            }
            ?>
			<td align="center"><?=$f['CentroCosto']?></td>
			<td align="center"><?=$f['CentroCosto']?></td>
			<td align="center"><?=$f['Ubicacion']?></td>
			<td align="center"><?=$f['NumeroSerie']?></td>
			<td align="center"><?=printValoresGeneral("ESTADO", $f['EstadoRegistro'])?></td>
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
</center>
</form>