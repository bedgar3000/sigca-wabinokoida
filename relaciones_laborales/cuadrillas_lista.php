<?php
//	------------------------------------
if ($filtrar == "default") {
	$fEstado = 'A';
	$fCodPais = $_PARAMETRO["PAISDEFAULT"];
	$fCodEstado = $_PARAMETRO["ESTADODEFAULT"];
	$fCodMunicipio = $_PARAMETRO["MUNICIPIODEFAULT"];
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "CodCuadrilla";
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (c.CodCuadrilla LIKE '%".$fBuscar."%' OR
					  c.Denominacion LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (c.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fCodPais != "") { $cCodPais = "checked"; $filtro.=" AND (e.CodPais = '".$fCodPais."')"; } else $dCodPais = "disabled";
if ($fCodEstado != "") { $cCodEstado = "checked"; $filtro.=" AND (e.CodEstado = '".$fCodEstado."')"; } else $dCodEstado = "disabled";
if ($fCodMunicipio != "") { $cCodMunicipio = "checked"; $filtro.=" AND (m.CodMunicipio = '".$fCodMunicipio."')"; } else $dCodMunicipio = "disabled";
if ($fCodParroquia != "") { $cCodParroquia = "checked"; $filtro.=" AND (pr.CodParroquia = '".$fCodParroquia."')"; } else $dCodParroquia = "disabled";
if ($fCodComunidad != "") { $cCodComunidad = "checked"; $filtro.=" AND (c.CodComunidad = '".$fCodComunidad."')"; } else $dCodComunidad = "disabled";
if ($fCodCiudad != "") { $cCodCiudad = "checked"; $filtro.=" AND (c.CodCiudad = '".$fCodCiudad."')"; } else $dCodCiudad = "disabled";
//	------------------------------------
$_titulo = "Maestro de Cuadrillas";
$_width = 700;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=cuadrillas_lista" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />

<!--FILTRO-->
<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
	<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
		<tr>
			<td align="right">Pais:</td>
			<td>
				<input type="checkbox" <?=$cCodPais?> onclick="chkCampos(this.checked, 'fCodPais');" onChange="loadSelect($('#fCodEstado'), 'tabla=mastestados&CodPais='+$(this).val(), 1, ['fCodMunicipio','fCodParroquia','fCodComunidad','fCodCiudad']);" />
				<select name="fCodPais" id="fCodPais" style="width:150px;" <?=$dCodPais?> onChange="loadSelect($('#fCodEstado'), 'tabla=mastestados&CodPais='+$(this).val(), 1, ['fCodMunicipio','fCodParroquia','fCodComunidad','fCodCiudad']);">
					<option value="">&nbsp;</option>
					<?=loadSelect2('mastpaises','CodPais','Pais',$fCodPais)?>
				</select>
			</td>
			<td align="right" width="100">Municipio:</td>
			<td>
				<input type="checkbox" <?=$cCodMunicipio?> onclick="chkCampos(this.checked, 'fCodMunicipio');" onChange="loadSelect($('#fCodParroquia'), 'tabla=mastparroquias&CodMunicipio='+$(this).val(), 1, ['fCodComunidad']); loadSelect($('#fCodCiudad'), 'tabla=mastciudades&CodMunicipio='+$(this).val(), 1);" />
				<select name="fCodMunicipio" id="fCodMunicipio" style="width:150px;" <?=$dCodMunicipio?> onChange="loadSelect($('#fCodParroquia'), 'tabla=mastparroquias&CodMunicipio='+$(this).val(), 1, ['fCodComunidad']); loadSelect($('#fCodCiudad'), 'tabla=mastciudades&CodMunicipio='+$(this).val(), 1);">
					<option value="">&nbsp;</option>
					<?=loadSelect2('mastmunicipios','CodMunicipio','Municipio',$fCodMunicipio,0,['CodEstado'],[$fCodEstado])?>
				</select>
			</td>
			<td align="right" width="100">Parroquias:</td>
			<td>
				<input type="checkbox" <?=$cCodParroquia?> onclick="chkCampos(this.checked, 'fCodParroquia');" onChange="loadSelect($('#fCodComunidad'), 'tabla=mastcomunidades&CodParroquia='+$(this).val(), 1);" />
				<select name="fCodParroquia" id="fCodParroquia" style="width:150px;" <?=$dCodParroquia?> onChange="loadSelect($('#fCodComunidad'), 'tabla=mastcomunidades&CodParroquia='+$(this).val(), 1);">
					<option value="">&nbsp;</option>
					<?=loadSelect2('mastparroquias','CodParroquia','Descripcion',$fCodParroquia,0,['CodMunicipio'],[$fCodMunicipio])?>
				</select>
			</td>
			<td align="right" width="100">Buscar:</td>
			<td>
				<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
				<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:95px;" <?=$dBuscar?> />
			</td>
	        <td>&nbsp;</td>
		</tr>
		<tr>
			<td align="right">Estado:</td>
			<td>
				<input type="checkbox" <?=$cCodEstado?> onclick="chkCampos(this.checked, 'fCodEstado');" onChange="loadSelect($('#fCodMunicipio'), 'tabla=mastmunicipios&CodEstado='+$(this).val(), 1, ['fCodParroquia','fCodComunidad','fCodCiudad']);" />
				<select name="fCodEstado" id="fCodEstado" style="width:150px;" <?=$dCodEstado?> onChange="loadSelect($('#fCodMunicipio'), 'tabla=mastmunicipios&CodEstado='+$(this).val(), 1, ['fCodParroquia','fCodComunidad','fCodCiudad']);">
					<option value="">&nbsp;</option>
					<?=loadSelect2('mastestados','CodEstado','Estado',$fCodEstado,0,['CodPais'],[$fCodPais])?>
				</select>
			</td>
			<td align="right">Ciudad:</td>
			<td>
				<input type="checkbox" <?=$cCodCiudad?> onclick="chkCampos(this.checked, 'fCodCiudad');" />
				<select name="fCodCiudad" id="fCodCiudad" style="width:150px;" <?=$dCodCiudad?>>
					<option value="">&nbsp;</option>
					<?=loadSelect2('mastciudades','CodCiudad','Ciudad',$fCodCiudad,0,['CodMunicipio'],[$fCodMunicipio])?>
				</select>
			</td>
			<td align="right">Comunidades:</td>
			<td>
				<input type="checkbox" <?=$cCodComunidad?> onclick="chkCampos(this.checked, 'fCodComunidad');" />
				<select name="fCodComunidad" id="fCodComunidad" style="width:150px;" <?=$dCodComunidad?>>
					<option value="">&nbsp;</option>
					<?=loadSelect2('mastcomunidades','CodComunidad','Descripcion',$fCodComunidad,0,['CodParroquia'],[$fCodParroquia])?>
				</select>
			</td>
			<td align="right">Estado: </td>
			<td>
	            <input type="checkbox" <?=$cEstado?> onclick="chkFiltro(this.checked, 'fEstado');" />
	            <select name="fEstado" id="fEstado" style="width:100px;" <?=$dEstado?>>
	                <option value="">&nbsp;</option>
	                <?=loadSelectGeneral("ESTADO", $fEstado, 0)?>
	            </select>
			</td>
	        <td align="right"><input type="submit" value="Buscar"></td>
		</tr>
	</table>
</div>
<div class="sep"></div>

<!--REGISTROS-->
<center>
<input type="hidden" name="sel_registros" id="sel_registros" />
<table class="tblBotones" style="width:100%; min-width:<?=$_width?>px;">
    <tr>
        <td><div id="rows"></div></td>
        <td align="right">
            <input type="button" value="Nuevo" style="width:75px;" class="insert" onclick="cargarPagina(this.form, 'gehen.php?anz=cuadrillas_form&opcion=nuevo');" />
            <input type="button" value="Modificar" style="width:75px;" class="update" onclick="cargarOpcion2(this.form, 'gehen.php?anz=cuadrillas_form&opcion=modificar', 'SELF', '', $('#sel_registros').val());" />
            <input type="button" value="Eliminar" style="width:75px;" class="delete" onclick="opcionRegistro3(this.form, $('#sel_registros').val(), 'formulario', 'eliminar', 'cuadrillas_ajax.php');" />
            <input type="button" value="Ver" style="width:75px;" class="ver" onclick="cargarOpcion2(this.form, 'gehen.php?anz=cuadrillas_form&opcion=ver', 'SELF', '', $('#sel_registros').val());" />
        </td>
    </tr>
</table>

<div style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px;">
<table class="tblLista" style="width:100%; min-width:1100px;">
	<thead>
    <tr>
        <th width="75" onclick="order('CodCuadrilla')">Id.</th>
        <th align="left" onclick="order('Denominacion')">Denominaci&oacute;n</th>
        <th width="150" align="left" onclick="order('Comunidad')">Comunidad</th>
        <th width="150" align="left" onclick="order('Parroquia')">Parroquia</th>
        <th width="150" align="left" onclick="order('Ciudad')">Ciudad</th>
        <th width="75" onclick="order('Estado')">Estado</th>
    </tr>
    </thead>
    
    <tbody id="lista_registros">
	<?php
	//	consulto todos
	$sql = "SELECT c.*
			FROM
				rh_cuadrillas c
				INNER JOIN mastciudades cd ON (cd.CodCiudad = c.CodCiudad)
				INNER JOIN mastcomunidades cm ON (cm.CodComunidad = c.CodComunidad)
				INNER JOIN mastparroquias pr ON (pr.CodParroquia = cm.CodParroquia)
				INNER JOIN mastmunicipios m ON (m.CodMunicipio = pr.CodMunicipio)
				INNER JOIN mastestados e ON (e.CodEstado = m.CodEstado)
			WHERE 1 $filtro";
	$rows_total = getNumRows3($sql);
	//	consulto lista
	$sql = "SELECT
				c.*,
				cm.Descripcion AS Comunidad,
				pr.Descripcion AS Parroquia,
				cd.Ciudad
			FROM
				rh_cuadrillas c
				INNER JOIN mastciudades cd ON (cd.CodCiudad = c.CodCiudad)
				INNER JOIN mastcomunidades cm ON (cm.CodComunidad = c.CodComunidad)
				INNER JOIN mastparroquias pr ON (pr.CodParroquia = cm.CodParroquia)
				INNER JOIN mastmunicipios m ON (m.CodMunicipio = pr.CodMunicipio)
				INNER JOIN mastestados e ON (e.CodEstado = m.CodEstado)
			WHERE 1 $filtro
			ORDER BY $fOrderBy
			LIMIT ".intval($limit).", ".intval($maxlimit);
	$field = getRecords($sql);
	$rows_lista = count($field);
	foreach($field as $f) {
		$id = $f['CodCuadrilla'];
		?>
		<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
			<td align="center"><?=$f['CodCuadrilla']?></td>
			<td><?=htmlentities($f['Denominacion'])?></td>
			<td><?=htmlentities($f['Comunidad'])?></td>
			<td><?=htmlentities($f['Parroquia'])?></td>
			<td><?=htmlentities($f['Ciudad'])?></td>
			<td align="center"><?=printValoresGeneral('ESTADO',$f['Estado'])?></td>
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