<?php
//	------------------------------------
if ($filtrar == "default") {
	if ($ventana != 'at_activos_agrupar') {
		$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
		$fCodDependencia = $_SESSION["DEPENDENCIA_ACTUAL"];
	}
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "CodComponente";
}
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (ch.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fCodDependencia != "") { $cCodDependencia = "checked"; $filtro.=" AND (ch.CodDependencia = '".$fCodDependencia."')"; } else $dCodDependencia = "disabled";
if ($fCodCentroCosto != "") { $cCodCentroCosto = "checked"; $filtro.=" AND (ch.CodCentroCosto = '".$fCodCentroCosto."')"; } else $dCodCentroCosto = "disabled";
if ($fCodTipoActivo != "") { $cCodTipoActivo = "checked"; $filtro.=" AND (ch.CodTipoActivo = '".$fCodTipoActivo."')"; } else $dCodTipoActivo = "disabled";
if ($fCodSituacionActivo != "") { $cCodSituacionActivo = "checked"; $filtro.=" AND (ch.CodSituacionActivo = '".$fCodSituacionActivo."')"; } else $dCodSituacionActivo = "disabled";
if ($fCodLinea != "") { $cCodLinea = "checked"; $filtro.=" AND (ch.CodLinea = '".$fCodLinea."')"; } else $dCodLinea = "visibility:hidden;";
if ($fCodFamilia != "") { $cCodFamilia = "checked"; $filtro.=" AND (ch.CodFamilia = '".$fCodFamilia."')"; } else $dCodFamilia = "visibility:hidden;";
if ($fCodSubFamilia != "") { $cCodSubFamilia = "checked"; $filtro.=" AND (ch.CodSubFamilia = '".$fCodSubFamilia."')"; } else $dCodSubFamilia = "visibility:hidden;";
//	------------------------------------
$_titulo = "Registro de Componentes";
$_width = 860;
?>
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lista_at_componenteshard" method="post">
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
			<select name="fCodOrganismo" id="fCodOrganismo" style="width:260px;" <?=$dCodOrganismo?> onChange="loadSelect($('#fCodDependencia'), 'opcion='+$('#fCodOrganismo').val()+'&tabla=dependencia_filtro', 1, 'fCodCentrocosto');">
				<?php
                if ($ventana == 'at_componenteshard' || $ventana == 'at_activos_agrupar') getOrganismos($fCodOrganismo, 1);
				else getOrganismos($fCodOrganismo, 3);
				?>
			</select>
		</td>
		<td class="tagForm" width="125">Linea:</td>
		<td class="gallery clearfix">
			<input type="checkbox" id="cCodLinea" <?=$cCodLinea?> onclick="ckLista(this.checked, ['fCodLinea'], ['bCodLinea']);" />
        	<input type="text" name="fCodLinea" id="fCodLinea" value="<?=$fCodLinea?>" style="width:35px;" readonly />
        	<input type="text" name="fLinea" id="fLinea" value="<?=$fLinea?>" style="width:210px;" readonly />
            <a href="../lib/listas/gehen.php?anz=lista_at_linea&filtrar=default&campo1=fCodLinea&campo2=fLinea&ventana=selLista&iframe=true&width=950&height=460" rel="prettyPhoto[iframe1]" style=" <?=$dCodLinea?>" id="bCodLinea">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
		</td>
        <td>&nbsp;</td>
	</tr>
	<tr>
		<td align="right">Dependencia:</td>
		<td>
        	<?php
			if ($ventana == 'at_activos_agrupar') {
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
        	<input type="text" name="fCodFamilia" id="fCodFamilia" value="<?=$fCodFamilia?>" style="width:35px;" readonly />
        	<input type="text" name="fFamilia" id="fFamilia" value="<?=$fFamilia?>" style="width:210px;" readonly />
            <a href="../lib/listas/gehen.php?anz=lista_at_familia&filtrar=default&campo1=fCodLinea&campo2=fCodFamilia&campo3=fLinea&campo4=fFamilia&ventana=selLista&iframe=true&width=950&height=430" rel="prettyPhoto[iframe2]" style=" <?=$dCodFamilia?>" id="bCodFamilia">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
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
				<?=loadSelect2("ac_mastcentrocosto", "CodCentroCosto", "Descripcion", $fCodCentroCosto, 0, array('CodDependencia'), array($fCodDependencia))?>
			</select>
		</td>
		<td class="tagForm">Sub-Familia:</td>
		<td class="gallery clearfix">
			<input type="checkbox" <?=$cCodSubFamilia?> onclick="ckLista(this.checked, ['fCodLinea','fCodFamilia','fCodSubFamilia'], ['bCodSubFamilia']);" />
        	<input type="text" name="fCodSubFamilia" id="fCodSubFamilia" value="<?=$fCodSubFamilia?>" style="width:35px;" readonly />
        	<input type="text" name="fSubFamilia" id="fSubFamilia" value="<?=$fSubFamilia?>" style="width:210px;" readonly />
            <a href="../lib/listas/gehen.php?anz=lista_at_subfamilia&filtrar=default&campo1=fCodLinea&campo2=fCodFamilia&campo3=fCodSubFamilia&campo4=fLinea&campo5=fFamilia&campo6=fSubFamilia&ventana=selLista&iframe=true&width=950&height=430" rel="prettyPhoto[iframe3]" style=" <?=$dCodSubFamilia?>" id="bCodSubFamilia">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
		</td>
        <td>&nbsp;</td>
	</tr>
	<tr>
		<td class="tagForm">Tipo de Activo:</td>
		<td>
			<input type="checkbox" <?=$cCodTipoActivo?> onclick="chkFiltro(this.checked, 'fCodTipoActivo');" />
			<select name="fCodTipoActivo" id="fCodTipoActivo" style="width:260px;" <?=$dCodTipoActivo?>>
            	<option value="">&nbsp;</option>
				<?=loadSelect2("at_tipoactivo", "CodTipoActivo", "Descripcion", $fCodTipoActivo)?>
			</select>
		</td>
		<td class="tagForm">Situaci&oacute;n Activo:</td>
		<td>
			<input type="checkbox" <?=$cCodSituacionActivo?> onclick="chkFiltro(this.checked, 'fCodSituacionActivo');" />
			<select name="fCodSituacionActivo" id="fCodSituacionActivo" style="width:260px;" <?=$dCodSituacionActivo?>>
            	<option value="">&nbsp;</option>
				<?=loadSelect2("at_situacionactivo", "CodSituacionActivo", "Descripcion", $fCodSituacionActivo)?>
			</select>
		</td>
        <td align="right"><input type="submit" value="Buscar"></td>
	</tr>
</table>
</div>
<div class="sep"></div>

<center>
<div style="overflow:scroll; height:260px; width:100%; min-width:<?=$_width?>px;">
<table class="tblLista" style="width:100%; min-width:2250px;">
	<thead>
    <tr>
        <th width="80" onclick="order('CodComponente')">Id.</th>
        <th width="80" onclick="order('CodInterno')">Nro. Interno</th>
        <th align="left" onclick="order('Descripcion')">Descripci&oacute;n</th>
        <th width="150" align="left" onclick="order('TipoActivo')">Tipo de Activo</th>
        <th width="150" align="left" onclick="order('SituacionActivo')">Situaci&oacute;n Activo</th>
        <th width="60" onclick="order('CodUbicacion')">Ubicaci&oacute;n</th>
        <th width="60" onclick="order('CodCentroCosto')">C.C.</th>
        <th width="400" align="left" onclick="order('Dependencia')">Dependencia</th>
        <th width="400" align="left" onclick="order('Organismo')">Organismo</th>
    </tr>
    </thead>
    
    <tbody>
	<?php
	//	consulto todos
	$sql = "SELECT ch.CodComponente
			FROM
				at_componenteshard ch
				INNER JOIN at_tipoactivo ta ON (ta.CodTipoActivo = ch.CodTipoActivo)
				INNER JOIN at_situacionactivo sa ON (sa.CodSituacionActivo = ch.CodSituacionActivo)
				INNER JOIN mastorganismos o ON (o.CodOrganismo = ch.CodOrganismo)
				INNER JOIN mastdependencias d ON (d.CodDependencia = ch.CodDependencia)
			WHERE 1 $filtro";
	$rows_total = getNumRows3($sql);
	//	consulto lista
	$sql = "SELECT
				ch.*,
				ta.Descripcion AS TipoActivo,
				sa.Descripcion AS SituacionActivo,
				o.Organismo,
				d.Dependencia
			FROM
				at_componenteshard ch
				INNER JOIN at_tipoactivo ta ON (ta.CodTipoActivo = ch.CodTipoActivo)
				INNER JOIN at_situacionactivo sa ON (sa.CodSituacionActivo = ch.CodSituacionActivo)
				INNER JOIN mastorganismos o ON (o.CodOrganismo = ch.CodOrganismo)
				INNER JOIN mastdependencias d ON (d.CodDependencia = ch.CodDependencia)
			WHERE 1 $filtro
			ORDER BY $fOrderBy
			LIMIT ".intval($limit).", ".intval($maxlimit);
	$field = getRecords($sql);
	$rows_lista = count($field);
	foreach($field as $f) {
		if ($ventana == 'listado_insertar_linea' || $ventana == 'at_activos_agrupar') {
			?>
            <tr class="trListaBody" onClick="listado_insertar_linea('<?=$detalle?>','modulo=<?=$modulo?>&accion=<?=$accion?>&CodComponente=<?=$f['CodComponente']?>','<?=$f['CodComponente']?>','<?=$url?>');">
            <?php
		} 
		else {
			?>
            <tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['CodComponente']?>','<?=$f['Descripcion']?>'], ['<?=$campo1?>','<?=$campo2?>']);">
            <?php

		}
		?>
			<td align="center"><?=$f['CodComponente']?></td>
			<td align="center"><?=$f['CodInterno']?></td>
			<td><?=htmlentities($f['Descripcion'])?></td>
			<td><?=$f['TipoActivo']?></td>
			<td><?=$f['SituacionActivo']?></td>
			<td align="center"><?=$f['CodUbicacion']?></td>
			<td align="center"><?=$f['CodCentroCosto']?></td>
			<td><?=htmlentities($f['Dependencia'])?></td>
			<td><?=htmlentities($f['Organismo'])?></td>
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