<?php
$FechaLimite = obtenerFechaFin("$DiaActual-$MesActual-$AnioActual", $_PARAMETRO['DIASLIMCOT']);
$FechaInvitacion = "$DiaActual-$MesActual-$AnioActual";
//	------------------------------------
$clkCancelar = "document.getElementById('frmentrada').submit();";
$_width = 800;
$_sufijo = "cotizaciones_items_invitar_proveedores";
$_titulo = "Invitar Proveedores a Cotizar";
//	------------------------------------
$filtro = "";
$requerimientos = split(";", $sel_registros);
foreach($requerimientos as $requerimiento) {
	list($CodRequerimiento, $Secuencia) = split("_", $requerimiento);
	if ($filtro == "") $filtro .= "AND "; else $filtro .= "OR ";
	$filtro .= "(rd.CodRequerimiento = '".$CodRequerimiento."' AND rd.Secuencia = '".$Secuencia."')";
}
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<table width="<?=$_width?>" align="center" cellpadding="0" cellspacing="0">
    <tr>
        <td>
            <div class="header">
            <ul id="tab">
            <!-- CSS Tabs -->
            <li id="li1" class="current" onclick="currentTab('tab', this);">
            	<a href="#" onclick="mostrarTab('tab', '1', 2)">Invitaciones</a>
            </li>
            <li id="li2" onclick="currentTab('tab', this);">
            	<a href="#" onclick="mostrarTab('tab', '2', 2);">Ingresar Cotizaciones</a>
            </li>
            </ul>
            </div>
        </td>
    </tr>
</table>

<div id="tab1" style="display:block;">
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lg_cotizaciones_items_invitar_lista" method="POST" enctype="multipart/form-data" onsubmit="return <?=$_sufijo?>(this);" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
<input type="hidden" name="fOrderByItems" id="fOrderByItems" value="<?=$fOrderByItems?>" />
<input type="hidden" name="fOrderByCommodity" id="fOrderByCommodity" value="<?=$fOrderByCommodity?>" />
<input type="hidden" name="id_tab" id="id_tab" value="<?=$id_tab?>" />
<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
<input type="hidden" name="fCodDependencia" id="fCodDependencia" value="<?=$fCodDependencia?>" />
<input type="hidden" name="fCodCentroCosto" id="fCodCentroCosto" value="<?=$fCodCentroCosto?>" />
<input type="hidden" name="fCodClasificacion" id="fCodClasificacion" value="<?=$fCodClasificacion?>" />
<input type="hidden" id="detalles_requerimientos" value="<?=$sel_registros?>" />

<table width="<?=$_width?>" class="tblForm">
	<tr>
    	<td colspan="4" class="divFormCaption">Informaci&oacute;n General</td>
    </tr>
	<tr>
		<td class="tagForm" width="125">Fecha L&iacute;mite para Cotizar:</td>
		<td width="100">
        	<input type="text" name="FechaLimite" id="FechaLimite" value="<?=$FechaLimite?>" style="width:60px;" class="datepicker" readonly="readonly" />
		</td>
		<td class="tagForm" width="125">Condiciones de Entrega:</td>
		<td>
        	<textarea name="Condiciones" id="Condiciones" style="width:98%; height:40px;"><?=$_PARAMETRO['CONDCOTDEF']?></textarea>
		</td>
	</tr>
	<tr>
		<td class="tagForm">Fecha Invitaci&oacute;n:</td>
		<td>
        	<input type="text" name="FechaInvitacion" id="FechaInvitacion" value="<?=$FechaInvitacion?>" style="width:60px;" class="datepicker" />
		</td>
		<td class="tagForm">Observaciones:</td>
		<td>
        	<textarea name="Observaciones" id="Observaciones" style="width:99%; height:40px;"><?=$_PARAMETRO['OBSCOTDEF']?></textarea>
		</td>
	</tr>
	<tr>
		<td class="tagForm">Especificaciones T&eacute;cnicas:</td>
		<td colspan="3">
        	<textarea name="Especificaciones" id="Especificaciones" style="width:99%; height:40px;"></textarea>
		</td>
	</tr>
</table>

<center>
<input type="submit" value="Aceptar" style="width:75px;" />
<input type="button" value="Cancelar" style="width:75px;" onclick="<?=$clkCancelar?>" />
</center>
</form>

<center>
<form name="frm_proveedores" id="frm_proveedores" autocomplete="off">
<input type="hidden" id="sel_proveedores" />
<table width="<?=$_width?>" class="tblBotones">
	<thead>
    <tr>
    	<th class="divFormCaption">Proveedores Invitados</th>
	</tr>
    </thead>
    <tbody>
    <tr>
        <td align="right" class="gallery clearfix">
            <a id="a_proveedores" href="../lib/listas/listado_personas.php?filtrar=default&ventana=cotizaciones_proveedores_insertar&detalle=proveedores&EsProveedor=S&iframe=true&width=100%&height=425" rel="prettyPhoto[iframe1]" style="display:none;"></a>
            <input type="button" class="btLista" value="Insertar" onclick="$('#a_proveedores').click();" />
            <input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'proveedores');" />
        </td>
    </tr>
    </tbody>
</table>
<div style="overflow:scroll; width:<?=$_width?>px; height:150px;">
<table width="100%" class="tblLista">
    <thead>
    <tr>
        <th scope="col" width="25">&nbsp;</th>
        <th scope="col" align="left">Raz&oacute;n Social</th>
        <th scope="col" width="150">Forma de Pago</th>
    </tr>
    </thead>
    
    <tbody id="lista_proveedores">
    </tbody>
</table>
</div>
<input type="hidden" id="nro_proveedores" value="<?=$nro_proveedores?>" />
<input type="hidden" id="can_proveedores" value="<?=$nro_proveedores?>" />
</form>
</center>

<center>
<table width="<?=$_width?>" class="tblBotones">
	<thead>
    <tr>
    	<th class="divFormCaption">Requerimientos Asociados</th>
	</tr>
    </thead>
</table>
<table width="<?=$_width?>" align="center" cellpadding="0" cellspacing="0">
    <tr>
        <td>
            <div class="header">
            <ul id="tabreq">
            <!-- CSS Tabs -->
            <li id="li1" class="current" onclick="currentTab('tabreq', this);">
            	<a href="#" onclick="mostrarTab('tabreq', '1', 2)">Requerimientos a Invitar</a>
            </li>
            <li id="li2" onclick="currentTab('tabreq', this);">
            	<a href="#" onclick="mostrarTab('tabreq', '2', 2);">Invitaciones Realizadas</a>
            </li>
            </ul>
            </div>
        </td>
    </tr>
</table>
<div id="tabreq1" style="display:block;">
<div style="overflow:scroll; width:<?=$_width?>px; height:150px;">
<table width="2800" class="tblLista">
    <thead>
    <tr>
        <th scope="col" width="15">&nbsp;</th>
        <th scope="col" width="100">Requerimiento</th>
        <th scope="col" width="20">#</th>
        <th scope="col" width="80">C&oacute;digo</th>
        <th scope="col" width="400" align="left">Descripci&oacute;n</th>
        <th scope="col" width="35">Uni.</th>
        <th scope="col" width="50">Cant.</th>
        <th scope="col" width="25">Inv.</th>
        <th scope="col" width="45">C.C.</th>
        <th scope="col" width="75">Prioridad</th>
        <th scope="col" width="115">Linea-Familia</th>
        <th scope="col" align="left">Comentarios</th>
        <th scope="col" width="75">Fecha Requerida</th>
        <th scope="col" width="400">Dependencia</th>
    </tr>
    </thead>
    
    <tbody id="lista_requerimientos">
	<?php
	//	consulto lista
	$sql = "SELECT
				rd.CodRequerimiento,
				rd.Secuencia,
				rd.CodItem,
				rd.CommoditySub,
				rd.Descripcion,
				rd.CodUnidad,
				rd.CantidadPedida,
				rd.CodCentroCosto,
				rd.CotizacionRegistros,
				rd.Comentarios,
				o.Organismo,
				d.Dependencia,
				r.Clasificacion,
				r.FechaRequerida,
				r.Comentarios,
				i.CodLinea,
				i.CodFamilia,
				r.Prioridad,
				r.CodInterno
			FROM
				lg_requerimientosdet rd
				LEFT JOIN lg_itemmast i ON (rd.CodItem = i.CodItem)
				LEFT JOIN lg_commoditysub cs ON (rd.CommoditySub = cs.Codigo)
				INNER JOIN mastorganismos o ON (rd.CodOrganismo = o.CodOrganismo)
				INNER JOIN lg_requerimientos r ON (rd.CodRequerimiento = r.CodRequerimiento)
				INNER JOIN mastdependencias d ON (r.CodDependencia = d.CodDependencia)
			WHERE 1 $filtro
			ORDER BY CodRequerimiento, CodItem, CommoditySub";
	$query_requerimientos = mysql_query($sql) or die ($sql.mysql_error());
	$rows_lista = mysql_num_rows($query_requerimientos);
	$nro_requerimientos = 0;
	while ($field_requerimientos = mysql_fetch_array($query_requerimientos)) {
		if (strlen($field_requerimientos['Comentarios']) > 350) $Comentarios = substr($field_requerimientos['Comentarios'], 0, 350)."...";
		else $Comentarios = $field_requerimientos['Comentarios'];
		?>
		<tr class="trListaBody" onclick="clkMulti($(this), '<?=$id?>');">
        	<th height="25">
            	<?=++$nro_requerimientos?>
            </th>
			<td align="center">
				<?=$field_requerimientos['CodInterno']?>
            </td>
			<td align="center"><?=$field_requerimientos['Secuencia']?></td>
			<td align="center"><?=$field_requerimientos['CodItem']?></td>
			<td><?=htmlentities($field_requerimientos['Descripcion'])?></td>
			<td align="center"><?=$field_requerimientos['CodUnidad']?></td>
			<td align="right"><?=number_format($field_requerimientos['CantidadPedida'], 2, ',', '.')?></td>
			<td align="center"><?=$field_requerimientos['CotizacionRegistros']?></td>
			<td align="center"><?=$field_requerimientos['CodCentroCosto']?></td>
			<td align="center"><?=printValores("PRIORIDAD", $field_requerimientos['Prioridad'])?></td>
			<td align="center"><?=$field_requerimientos['CodLinea']?>-<?=$field_requerimientos['CodFamilia']?></td>
			<td title="<?=htmlentities($field_requerimientos['Comentarios'])?>"><?=htmlentities($Comentarios)?></td>
			<td align="center"><?=formatFechaDMA($field_requerimientos['FechaRequerida'])?></td>
			<td><?=htmlentities($field_requerimientos['Dependencia'])?></td>
		</tr>
		<?php
	}
	?>
    </tbody>
</table>
</div>
</div>

<div id="tabreq2" style="display:none;">
</div>
</center>

</div>

<div id="tab2" style="display:none;">
</div>

<div style="width:<?=$_width?>px" class="divMsj">Campos Obligatorios *</div>