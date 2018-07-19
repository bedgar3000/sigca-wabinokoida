<?php
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["ORGANISMO_ACTUAL"];
	$fEstado = "A";
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "CodRetencion";
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (rj.CodRetencion LIKE '%".$fBuscar."%' OR
					  rj.Expediente LIKE '%".$fBuscar."%' OR
					  rj.Juzgado LIKE '%".$fBuscar."%' OR
					  p.NomCompleto LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (rj.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (rj.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fFechaResolucionD != "" || $fFechaResolucionH != "") { 
	$cFechaResolucion = "checked";
	if ($fFechaResolucionD != "") $filtro.=" AND (rj.FechaResolucion >= '".$fFechaResolucionD."')"; 
	if ($fFechaResolucionH != "") $filtro.=" AND (rj.FechaResolucion <= '".$fFechaResolucionH."')"; 
} else $dFechaResolucion = "disabled";
//	------------------------------------
$_width = 900;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo">Retenciones Judidiales</td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=rh_retencion_judicial_lista" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />

<!--FILTRO-->
<div class="divBorder" style="width:<?=$_width?>px;">
<table width="<?=$_width?>" class="tblFiltro">
	<tr>
		<td align="right" width="125">Organismo:</td>
		<td>
			<input type="checkbox" <?=$cCodOrganismo?> onclick="chkCampos(this.checked, 'fCodOrganismo');" />
			<select name="fCodOrganismo" id="fCodOrganismo" style="width:262px;" <?=$dCodOrganismo?>>
            	<option value="">&nbsp;</option>
				<?=getOrganismos($fCodOrganismo, 3)?>
			</select>
		</td>
		<td align="right" width="125">Fecha de Resoluci&oacute;n: </td>
		<td>
			<input type="checkbox" <?=$cFechaResolucion?> onclick="chkFiltro_2(this.checked, 'fFechaResolucionD', 'fFechaResolucionH');" />
			<input type="text" name="fFechaResolucionD" id="fFechaResolucionD" value="<?=$fFechaResolucionD?>" <?=$dFechaResolucion?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFecha(this);" /> -
            <input type="text" name="fFechaResolucionH" id="fFechaResolucionH" value="<?=$fFechaResolucionH?>" <?=$dFechaResolucion?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFecha(this);" />
        </td>
	</tr>
	<tr>
		<td align="right">Buscar:</td>
		<td>
			<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
			<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:255px;" <?=$dBuscar?> />
		</td>
		<td align="right">Estado: </td>
		<td>
            <input type="checkbox" <?=$cEstado?> onclick="chkFiltro(this.checked, 'fEstado');" />
            <select name="fEstado" id="fEstado" style="width:143px;" <?=$dEstado?>>
                <option value="">&nbsp;</option>
                <?=loadSelectGeneral("ESTADO", $fEstado, 0)?>
            </select>
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
        <td align="right">
            <input type="button" id="btNuevo" value="Nuevo" style="width:75px; <?=$btNuevo?>" onclick="cargarPagina(this.form, 'gehen.php?anz=rh_retencion_judicial_form&opcion=nuevo');" />
            <input type="button" id="btModificar" value="Modificar" style="width:75px; <?=$btModificar?>" onclick="cargarOpcion2(this.form, 'gehen.php?anz=rh_retencion_judicial_form&opcion=modificar', 'SELF', '', $('#sel_registros').val());" />
            <input type="button" id="btVer" value="Ver" style="width:75px; <?=$btVer?>" onclick="cargarOpcion2(this.form, 'gehen.php?anz=rh_retencion_judicial_form&opcion=ver', 'SELF', '', $('#sel_registros').val());" />
        </td>
    </tr>
</table>

<div style="overflow:scroll; width:<?=$_width?>px; height:300px;">
<table width="1750" class="tblLista">
	<thead>
    <tr>
        <th scope="col" width="60" onclick="order('CodRetencion')">C&oacute;digo</th>
        <th scope="col" width="150" onclick="order('Expediente')">Expediente</th>
        <th scope="col" width="75" onclick="order('FechaResolucion')">Fecha de Resoluci&oacute;n</th>
        <th scope="col" width="350" align="left" onclick="order('NomCompleto')">Empleado</th>
        <th scope="col" align="left" onclick="order('Juzgado')">Juzgado</th>
        <th scope="col" width="75" onclick="order('Estado')">Estado</th>
    </tr>
    </thead>
    
    <tbody id="lista_registros">
	<?php
	//	consulto todos
	$sql = "SELECT
				rj.CodOrganismo,
				rj.CodRetencion
			FROM
				rh_retencionjudicial rj
				INNER JOIN mastpersonas p ON (p.CodPersona = rj.CodPersona)
			WHERE 1 $filtro";
	$rows_total = getNumRows3($sql);
	
	//	consulto lista
	$sql = "SELECT
				rj.CodOrganismo,
				rj.CodRetencion,
				rj.Expediente,
				rj.FechaResolucion,
				rj.Juzgado,
				rj.Estado,
				p.NomCompleto
			FROM
				rh_retencionjudicial rj
				INNER JOIN mastpersonas p ON (p.CodPersona = rj.CodPersona)
			WHERE 1 $filtro
			ORDER BY $fOrderBy
			LIMIT ".intval($limit).", ".intval($maxlimit);
	$field = getRecords($sql);
	$rows_lista = count($field);
	foreach($field as $f) {
		$id = $f['CodOrganismo'].'_'.$f['CodRetencion'];
		?>
		<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
			<td align="center"><?=$f['CodRetencion']?></td>
			<td align="center"><?=$f['Expediente']?></td>
			<td align="center"><?=formatFechaDMA($f['FechaResolucion'])?></td>
			<td><?=htmlentities($f['NomCompleto'])?></td>
			<td><?=htmlentities($f['Juzgado'])?></td>
			<td align="center"><?=printValoresGeneral("ESTADO", $f['Estado'])?></td>
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