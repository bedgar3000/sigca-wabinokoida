<?php
if ($lista == "todos") {
	$titulo = "Lista de Capacitaciones";
	$btAprobar = "display:none;";
	$btIniciar = "display:none;";
	$btTerminar = "display:none;";
}
elseif ($lista == "aprobar") {
	$titulo = "Aprobar Capacitaciones";
	$btNuevo = "display:none;";
	$btModificar = "display:none;";
	$btIniciar = "display:none;";
	$btTerminar = "display:none;";
}
elseif ($lista == "iniciar") {
	$titulo = "Iniciar/Terminar Capacitaciones";
	$btNuevo = "display:none;";
	$btModificar = "display:none;";
	$btAprobar = "display:none;";
}
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "Anio,CodOrganismo,Capacitacion";
	if ($lista == "todos") $fEstado = "PE";
	elseif ($lista == "aprobar") $fEstado = "PE";
	elseif ($lista == "iniciar") $fEstado = "AP";
}
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (c.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (c.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fTipoCurso != "") { $cTipoCurso = "checked"; $filtro.=" AND (c.TipoCurso = '".$fTipoCurso."')"; } else $dTipoCurso = "disabled";
if ($fFechaD != "" || $fFechaH != "") {
	$cFecha = "checked";
	if ($fFechaD != "") $filtro.=" AND ('".formatFechaAMD($fFechaD)."' >= c.FechaDesde AND '".formatFechaAMD($fFechaD)."' <= c.FechaHasta)";
	if ($fFechaH != "") $filtro.=" AND ('".formatFechaAMD($fFechaH)."' >= c.FechaDesde AND '".formatFechaAMD($fFechaH)."' <= c.FechaHasta)";
} else $dFecha = "disabled";
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (c.Capacitacion LIKE '%".$fBuscar."%' OR
					  c.FechaDesde LIKE '%".$fBuscar."%' OR
					  c.CostoEstimado LIKE '%".setNumero($fBuscar)."%' OR
					  cs.Descripcion LIKE '%".$fBuscar."%' OR
					  ce.Descripcion LIKE '%".$fBuscar."%' OR
					  md.Descripcion LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fCodCurso != "") { $cCodCurso = "checked"; $filtro.=" AND (c.CodCurso = '".$fCodCurso."')"; } else $dCodCurso = "visibility:hidden;";
if ($fCodCentroEstudio != "") { $cCodCentroEstudio = "checked"; $filtro.=" AND (c.CodCentroEstudio = '".$fCodCentroEstudio."')"; } else $dCodCentroEstudio = "visibility:hidden;";
if ($fTipoCapacitacion != "") { $cTipoCapacitacion = "checked"; $filtro.=" AND (c.TipoCapacitacion = '".$fTipoCapacitacion."')"; } else $dTipoCapacitacion = "disabled";
if ($fModalidad != "") { $cModalidad = "checked"; $filtro.=" AND (c.Modalidad = '".$fModalidad."')"; } else $dModalidad = "disabled";
//	------------------------------------
$_width = 800;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=rh_capacitaciones_lista" method="post">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />

<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
	<tr>
		<td align="right" width="125">Organismo:</td>
		<td>
			<input type="checkbox" <?=$cCodOrganismo?> onclick="this.checked=!this.checked" />
			<select name="fCodOrganismo" id="fCodOrganismo" style="width:250px;" <?=$dCodOrganismo?> onChange="getOptionsSelect(this.value, 'dependencia_filtro', 'fCodDependencia', true)">
				<?=getOrganismos($fCodOrganismo, 3)?>
			</select>
		</td>
		<td align="right" width="125">Fecha: </td>
		<td>
			<input type="checkbox" <?=$cFecha?> onclick="chkFiltro_2(this.checked, 'fFechaD', 'fFechaH');" />
			<input type="text" name="fFechaD" id="fFechaD" value="<?=$fFechaD?>" <?=$dFecha?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFecha(this);" /> -
            <input type="text" name="fFechaH" id="fFechaH" value="<?=$fFechaH?>" <?=$dFecha?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFecha(this);" />
        </td>
		<td align="right" width="125">Buscar:</td>
		<td>
			<input type="checkbox" <?=$cBuscar?> onclick="chkFiltro(this.checked, 'fBuscar');" />
			<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:137px;" <?=$dBuscar?> />
		</td>
        <td>&nbsp;</td>
	</tr>
	<tr>
		<td align="right">Curso: </td>
		<td class="gallery clearfix">
            <input type="checkbox" <?=$cCodCurso?> onclick="chkListado(this.checked, 'btCurso', 'fCodCurso', 'fNomCurso');" />
            <input type="hidden" name="fCodCurso" id="fCodCurso" value="<?=$fCodCurso?>" />
			<input type="text" name="fNomCurso" id="fNomCurso" style="width:245px;" value="<?=$fNomCurso?>" readonly />
            <a href="../lib/listas/gehen.php?anz=lista_cursos&filtrar=default&campo1=fCodCurso&campo2=fNomCurso&iframe=true&width=100%&height=410" rel="prettyPhoto[iframe1]" id="btCurso" style=" <?=$dCodCurso?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
		<td align="right">Tipo de Capacitaci&oacute;n: </td>
		<td>
            <input type="checkbox" <?=$cTipoCurso?> onclick="chkFiltro(this.checked, 'fTipoCurso');" />
            <select name="fTipoCurso" id="fTipoCurso" style="width:143px;" <?=$dTipoCurso?>>
                <option value=""></option>
                <?=getMiscelaneos($fTipoCurso, "TIPOCURSO")?>
            </select>
		</td>
		<td align="right">Origen: </td>
		<td>
            <input type="checkbox" <?=$cTipoCapacitacion?> onclick="chkFiltro(this.checked, 'fTipoCapacitacion');" />
            <select name="fTipoCapacitacion" id="fTipoCapacitacion" style="width:143px;" <?=$dTipoCapacitacion?>>
                <option value=""></option>
                <?=loadSelectValores("TIPO-CAPACITACION", $fTipoCapacitacion)?>
            </select>
		</td>
        <td>&nbsp;</td>
	</tr>
	<tr>
		<td align="right">Centro de Estudio: </td>
		<td class="gallery clearfix">
            <input type="checkbox" <?=$cCodCentroEstudio?> onclick="chkListado(this.checked, 'btCentro', 'fCodCentroEstudio', 'fNomCentroEstudio');" />
            <input type="hidden" name="fCodCentroEstudio" id="fCodCentroEstudio" value="<?=$fCodCentroEstudio?>" />
			<input type="text" name="fNomCentroEstudio" id="fNomCentroEstudio" style="width:245px;" value="<?=$fNomCentroEstudio?>" readonly />
            <a href="../lib/listas/gehen.php?anz=lista_centro_estudio&filtrar=default&campo1=fCodCentroEstudio&campo2=fNomCentroEstudio&iframe=true&width=100%&height=410" rel="prettyPhoto[iframe2]" id="btCentro" style=" <?=$dCodCentroEstudio?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
		<td align="right">Estado:</td>
		<td>
        	<?php 
			if ($lista == "aprobar") {
				?>
				<input type="checkbox" onclick="this.checked=!this.checked;" checked="checked" />
                <select name="fEstado" id="fEstado" style="width:143px;">
                    <?=loadSelectValores("ESTADO-CAPACITACION", $fEstado, 1)?>
                </select>
                <?php
			} 
			elseif ($lista == "iniciar") {
				?>
				<input type="checkbox" onclick="this.checked=!this.checked;" checked="checked" />
                <select name="fEstado" id="fEstado" style="width:143px;">
                    <?=loadSelectValores("ESTADO-CAPACITACION2", $fEstado, 0)?>
                </select>
                <?php
			}
			else {
				?>
                <input type="checkbox" <?=$cEstado?> onclick="chkFiltro(this.checked, 'fEstado');" />
                <select name="fEstado" id="fEstado" style="width:143px;" <?=$dEstado?>>
                    <option value=""></option>
                    <?=loadSelectValores("ESTADO-CAPACITACION", $fEstado, 0)?>
                </select>
                <?php
			} 
			?>
		</td>
		<td align="right">Modalidad: </td>
		<td>
            <input type="checkbox" <?=$cModalidad?> onclick="chkFiltro(this.checked, 'fModalidad');" />
            <select name="fModalidad" id="fModalidad" style="width:143px;" <?=$dModalidad?>>
                <option value=""></option>
                <?=getMiscelaneos($fModalidad, "MODACAPAC")?>
            </select>
		</td>
        <td align="right"><input type="submit" value="Buscar"></td>
	</tr>
</table>
</div>
<div class="sep"></div>

<center>
<input type="hidden" name="sel_registros" id="sel_registros" />
<table class="tblBotones" style="width:100%; min-width:<?=$_width?>px;">
	<tr>
		<td><div id="rows"></div></td>
		<td align="right">
			<input type="button" id="btNuevo" value="Nuevo" style="width:75px; <?=$btNuevo?>" onclick="cargarPagina(this.form, 'gehen.php?anz=rh_capacitaciones_form&opcion=nuevo&origen=rh_capacitaciones_lista');" />
			<input type="button" id="btModificar" value="Modificar" style="width:75px; <?=$btModificar?>" onclick="cargarOpcionValidar3(this.form, $('#sel_registros').val(), 'rh_capacitaciones_ajax.php', 'modulo=ajax&accion=modificar', 'gehen.php?anz=rh_capacitaciones_form&opcion=modificar&origen=rh_capacitaciones_lista');" />
			<input type="button" id="btVer" value="Ver" style="width:75px; <?=$btVer?>" onclick="cargarOpcion2(this.form, 'gehen.php?anz=rh_capacitaciones_form&opcion=ver&origen=rh_capacitaciones_lista', 'SELF', '', 'sel_registros');" /> | 
			<input type="button" id="btAprobar" value="Aprobar" style="width:75px; <?=$btAprobar?>" onclick="cargarOpcionValidar3(this.form, $('#sel_registros').val(), 'rh_capacitaciones_ajax.php', 'modulo=ajax&accion=aprobar', 'gehen.php?anz=rh_capacitaciones_form&opcion=aprobar&origen=rh_capacitaciones_lista');" />
			<input type="button" id="btIniciar" value="Iniciar" style="width:75px; <?=$btIniciar?>" onclick="cargarOpcionValidar3(this.form, $('#sel_registros').val(), 'rh_capacitaciones_ajax.php', 'modulo=ajax&accion=iniciar', 'gehen.php?anz=rh_capacitaciones_form&opcion=iniciar&origen=rh_capacitaciones_lista');" />
			<input type="button" id="btTerminar" value="Terminar" style="width:75px; <?=$btTerminar?>" onclick="cargarOpcionValidar3(this.form, $('#sel_registros').val(), 'rh_capacitaciones_ajax.php', 'modulo=ajax&accion=terminar', 'gehen.php?anz=rh_capacitaciones_form&opcion=terminar&origen=rh_capacitaciones_lista');" />
		</td>
	</tr>
</table>

<div style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:250px;">
<table class="tblLista" style="width:100%; min-width:<?=$_width?>px;">
	<thead>
    <tr>
		<th width="60" onclick="order('Anio,CodOrganismo,Capacitacion');"># Cap.</th>
		<th width="125" onclick="order('NomTipoCurso');">Tipo</th>
		<th width="400" align="left" onclick="order('NomCurso');">Curso</th>
		<th width="60" onclick="order('FechaDesde');">Inicio</th>
		<th width="75" onclick="order('Estado');">Estado</th>
		<th width="100" align="right" onclick="order('CostoEstimado');">Costo Estimado</th>
		<th align="left" onclick="order('NomCentroEstudio');">Centro</th>
	</tr>
    </thead>
    
    <tbody>
	<?php
	//	consulto todos
	$sql = "SELECT
				c.Capacitacion,
				c.CodOrganismo,
				c.Anio,
				c.Estado,
				c.FechaDesde,
				c.FechaHasta,
				c.CostoEstimado,
				o.Organismo,
				cs.Descripcion AS NomCurso,
				ce.Descripcion AS NomCentroEstudio,
				md.Descripcion AS NomTipoCurso
			FROM
				rh_capacitacion c
				INNER JOIN mastorganismos o ON (o.CodOrganismo = c.CodOrganismo)
				INNER JOIN rh_cursos cs ON (cs.CodCurso = c.CodCurso)
				INNER JOIN rh_centrosestudios ce ON (ce.CodCentroEstudio = c.CodCentroEstudio)
				LEFT JOIN mastmiscelaneosdet md ON (md.CodDetalle = c.TipoCurso AND
													md.CodMaestro = 'TIPOCURSO')
			WHERE 1 $filtro";
	$rows_total = getNumRows3($sql);
	//	consulto lista
	$sql = "SELECT
				c.Capacitacion,
				c.CodOrganismo,
				c.Anio,
				c.Estado,
				c.FechaDesde,
				c.FechaHasta,
				c.CostoEstimado,
				o.Organismo,
				cs.Descripcion AS NomCurso,
				ce.Descripcion AS NomCentroEstudio,
				md.Descripcion AS NomTipoCurso
			FROM
				rh_capacitacion c
				INNER JOIN mastorganismos o ON (o.CodOrganismo = c.CodOrganismo)
				INNER JOIN rh_cursos cs ON (cs.CodCurso = c.CodCurso)
				INNER JOIN rh_centrosestudios ce ON (ce.CodCentroEstudio = c.CodCentroEstudio)
				LEFT JOIN mastmiscelaneosdet md ON (md.CodDetalle = c.TipoCurso AND
													md.CodMaestro = 'TIPOCURSO')
			WHERE 1 $filtro
			ORDER BY CodOrganismo, Capacitacion
			LIMIT ".intval($limit).", ".intval($maxlimit);
	$field = getRecords($sql);
	$rows_lista = count($field);
	foreach($field as $f) {
		$id = $f['Anio'].'_'.$f['CodOrganismo'].'_'.$f['Capacitacion'];
		if ($Grupo != $f['CodOrganismo']) {
			$Grupo = $f['CodOrganismo'];
			?>
			<tr class="trListaBody2">
				<td colspan="7"><?=htmlentities($f['Organismo'])?></td>
			</tr>
			<?php
		}
		?>
		<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
			<td align="center"><?=$f['Capacitacion']?></td>
			<td align="center"><?=htmlentities($f['NomTipoCurso'])?></td>
			<td><?=htmlentities($f['NomCurso'])?></td>
			<td align="center"><?=formatFechaDMA($f['FechaDesde'])?></td>
			<td align="center"><?=printValores("ESTADO-CAPACITACION", $f['Estado'])?></td>
			<td align="right"><?=number_format($f['CostoEstimado'], 2, ',', '.')?></td>
			<td><?=htmlentities($f['NomCentroEstudio'])?></td>
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