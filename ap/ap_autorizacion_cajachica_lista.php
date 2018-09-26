<?php
list($AnioActual, $MesActual, $DiaActual) = split("[/.-]", substr($Ahora, 0, 10));
$FechaActual = "$AnioActual-$MesActual-$DiaActual";
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "CodEmpleado";
}
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (cca.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fCodEmpleado != "") { $cCodEmpleado = "checked"; $filtro.=" AND (cca.CodEmpleado = '".$fCodPersona."')"; } else $dCodEmpleado = "visibility:hidden;";
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (cca.CodEmpleado LIKE '%".$fBuscar."%' OR
					  p.NomCompleto LIKE '%".$fBuscar."%' OR
					  cca.Monto LIKE '%".setNumero($fBuscar)."%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (cca.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
//	------------------------------------
$_width = 1000;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo">Autorizaci&oacute;n de Caja Chica</td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=ap_autorizacion_cajachica_lista" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />

<!--FILTRO-->
<div class="divBorder" style="width:<?=$_width?>px;">
<table width="<?=$_width?>" class="tblFiltro">
	<tr>
		<td align="right" width="100">Organismo:</td>
		<td>
			<input type="checkbox" <?=$cCodOrganismo?> onclick="this.checked=!this.checked" />
			<select name="fCodOrganismo" id="fCodOrganismo" style="width:275px;" <?=$dCodOrganismo?>>
				<?=getOrganismos($fCodOrganismo, 3)?>
			</select>
		</td>
		<td align="right" width="135">Buscar:</td>
		<td>
			<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
			<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:195px;" <?=$dBuscar?> />
		</td>
	</tr>
	<tr>
		<td align="right">Empleado: </td>
		<td class="gallery clearfix">
            <input type="checkbox" <?=$cCodEmpleado?> onclick="chkFiltroLista_3(this.checked, 'fCodEmpleado', 'fNomEmpleado', 'fCodPersona', 'btEmpleado');" />
            <input type="hidden" name="fCodPersona" id="fCodPersona" value="<?=$fCodPersona?>" />
            <input type="hidden" name="fCodEmpleado" id="fCodEmpleado" value="<?=$fCodEmpleado?>" />
			<input type="text" name="fNomEmpleado" id="fNomEmpleado" style="width:270px;" class="disabled" value="<?=$fNomEmpleado?>" readonly />
            <a href="../lib/listas/listado_empleados.php?filtrar=default&cod=fCodEmpleado&nom=fNomEmpleado&campo3=fCodPersona&iframe=true&width=950&height=525" rel="prettyPhoto[iframe1]" id="btEmpleado" style=" <?=$dCodEmpleado?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
		<td align="right">Estado: </td>
		<td>
            <input type="checkbox" <?=$cEstado?> onclick="chkFiltro(this.checked, 'fEstado');" />
            <select name="fEstado" id="fEstado" style="width:100px;" <?=$dEstado?>>
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
            <input type="button" id="btNuevo" value="Nuevo" style="width:75px; <?=$btNuevo?>" onclick="cargarPagina(this.form, 'gehen.php?anz=ap_autorizacion_cajachica_form&opcion=nuevo');" />
            
            <input type="button" id="btModificar" value="Modificar" style="width:75px; <?=$btModificar?>" onclick="cargarOpcion2(this.form, 'gehen.php?anz=ap_autorizacion_cajachica_form&opcion=modificar', 'SELF', '', $('#sel_registros').val());" />
            
            <input type="button" id="btEliminar" value="Eliminar" style="width:75px; <?=$btEliminar?>" onclick="opcionRegistro2(this.form, $('#sel_registros').val(), 'autorizacion_cajachica', 'eliminar');" />
            
            <input type="button" id="btVer" value="Ver" style="width:75px; <?=$btVer?>" onclick="cargarOpcion2(this.form, 'gehen.php?anz=ap_autorizacion_cajachica_form&opcion=ver', 'SELF', '', $('#sel_registros').val());" />
        </td>
    </tr>
</table>

<div style="overflow:scroll; width:<?=$_width?>px; height:300px;">
<table width="100%" class="tblLista">
	<thead>
    <tr>
        <th scope="col" width="35" onclick="order('CodEmpleado')"><a href="javascript:">Empleado</a></th>
        <th scope="col" width="300" align="left" onclick="order('NomCompleto')"><a href="javascript:">Nombre Completo</a></th>
        <th scope="col" align="left" onclick="order('Organismo')"><a href="javascript:">Organismo</a></th>
        <th scope="col" width="100" align="right" onclick="order('Monto')"><a href="javascript:">Monto</a></th>
        <th scope="col" width="75" onclick="order('Estado')"><a href="javascript:">Estado</a></th>
    </tr>
    </thead>
    
    <tbody id="lista_registros">
	<?php
	//	consulto todos
	$sql = "SELECT
				cca.CodOrganismo,
				cca.CodEmpleado AS CodPersona
			FROM
				ap_cajachicaautorizacion cca
				INNER JOIN mastpersonas p ON (cca.CodEmpleado = p.CodPersona)
				INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
				INNER JOIN mastorganismos o ON (o.CodOrganismo = cca.CodOrganismo)
			WHERE 1 $filtro";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	$rows_total = mysql_num_rows($query);
	
	//	consulto lista
	$sql = "SELECT
				cca.CodOrganismo,
				cca.CodEmpleado AS CodPersona,
				cca.Monto,
				cca.Estado,
				p.NomCompleto,
				e.CodEmpleado,
				o.Organismo
			FROM
				ap_cajachicaautorizacion cca
				INNER JOIN mastpersonas p ON (cca.CodEmpleado = p.CodPersona)
				INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
				INNER JOIN mastorganismos o ON (o.CodOrganismo = cca.CodOrganismo)
			WHERE 1 $filtro
			ORDER BY $fOrderBy
			LIMIT ".intval($limit).", ".intval($maxlimit);
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	$rows_lista = mysql_num_rows($query);
	while ($field = mysql_fetch_array($query)) {
		$id = "$field[CodOrganismo]"."_"."$field[CodPersona]";
		?>
		<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
			<td align="center"><?=$field['CodEmpleado']?></td>
			<td><?=htmlentities($field['NomCompleto'])?></td>
			<td><?=htmlentities($field['Organismo'])?></td>
			<td align="right"><strong><?=number_format($field['Monto'], 2, ',', '.')?></strong></td>
			<td align="center"><?=printValoresGeneral("ESTADO", $field['Estado'])?></td>
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