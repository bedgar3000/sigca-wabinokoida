<?php
//	------------------------------------
$filtro = "";
if ($filtrar == "default") {
	$ffEstado = "A";
	$ffOrderBy = "CodConcepto";
	$maxlimit = $_SESSION["MAXLIMIT"];
	$CodPersona = $registro;
}
if ($ffEstado != "") { $cEstado = "checked"; $filtro.=" AND (ec.Estado = '".$ffEstado."')"; } else $dEstado = "disabled";
if ($ffBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (ec.CodConcepto LIKE '%".$ffBuscar."%' OR
					  ec.Descripcion LIKE '%".$ffBuscar."%')";
} else $dBuscar = "disabled";
//	------------------------------------
//	datos del empleado
$sql = "SELECT
			p.CodPersona,
			p.NomCompleto,
			e.CodEmpleado
		FROM
			mastpersonas p
			INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
		WHERE p.CodPersona = '".$CodPersona."'";
$query_empleado = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
if (mysql_num_rows($query_empleado)) $field_empleado = mysql_fetch_array($query_empleado);
//	------------------------------------
$_titulo = "Asignaci&oacute;n de Conceptos";
$_width = 1000;
$_sufijo = "empleados_conceptos";
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="$('#frmentrada').attr('action', 'gehen.php?anz=empleados_lista'); document.getElementById('frmentrada').submit()">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=<?=$_sufijo?>_lista" method="post" autocomplete="off">
<input type="hidden" name="ffOrderBy" id="ffOrderBy" value="<?=$ffOrderBy?>" />
<input type="hidden" name="CodPersona" id="CodPersona" value="<?=$CodPersona?>" />
<input type="hidden" name="sel_registros" id="sel_registros" />
<?=filtroEmpleados()?>

<div class="divBorder" style="width:<?=$_width?>px;">
<table width="<?=$_width?>" class="tblFiltro">
	<tr>
    	<td colspan="2" class="divFormCaption">Datos del Empleado</td>
    </tr>
	<tr>
		<td align="right" width="125">Empleado:</td>
		<td>
        	<input type="text" id="CodEmpleado" style="width:60px;" class="codigo" value="<?=$field_empleado['CodEmpleado']?>" disabled />
		</td>
	</tr>
	<tr>
		<td align="right">Nombre Completo:</td>
		<td>
        	<input type="text" id="NomCompleto" style="width:500px;" class="codigo" value="<?=$field_empleado['NomCompleto']?>" disabled />
		</td>
	</tr>
</table>
</div><br />

<div class="divBorder" style="width:<?=$_width?>px;">
<table width="<?=$_width?>" class="tblFiltro">
    <tr>
		<td align="right">Buscar:</td>
		<td>
			<input type="checkbox" <?=$cBuscar?> onclick="chkFiltro(this.checked, 'ffBuscar');" />
			<input type="text" name="ffBuscar" id="ffBuscar" value="<?=$ffBuscar?>" style="width:264px;" <?=$dBuscar?> />
		</td>
		<td align="right">Estado: </td>
		<td>
       		<input type="checkbox" <?=$cEstado?> onclick="chkFiltro(this.checked, 'ffEstado');" />
            <select name="ffEstado" id="ffEstado" style="width:100px;" <?=$dEstado?>>
                <option value=""></option>
                <?=loadSelectGeneral("ESTADO", $ffEstado, 0)?>
            </select>
		</td>
	</tr>
</table>
</div>
<center><input type="submit" value="Buscar"></center><br />

<center>
<table width="<?=$_width?>" class="tblBotones">
    <tr>
        <td><div id="rows"></div></td>
        <td align="right">
            <input type="button" id="btNuevo" value="Nuevo" style="width:75px;" onclick="cargarPagina(this.form, 'gehen.php?anz=<?=$_sufijo?>_form&opcion=nuevo');" />
            
            <input type="button" id="btModificar" value="Modificar" style="width:75px;" onclick="cargarOpcion2(this.form, 'gehen.php?anz=<?=$_sufijo?>_form&opcion=modificar', 'SELF', '', $('#sel_registros').val());" />
            
            <input type="button" id="btEliminar" value="Eliminar" style="width:75px;" onclick="opcionRegistro2(this.form, this.form.sel_registros.value, '<?=$_sufijo?>', 'eliminar');" />
            
            <input type="button" id="btVer" value="Ver" style="width:75px;" onclick="cargarOpcion2(this.form, 'gehen.php?anz=<?=$_sufijo?>_form&opcion=ver', 'SELF', '', $('#sel_registros').val());" />
        </td>
    </tr>
</table>
<div style="overflow:scroll; width:<?=$_width?>px; height:300px;">
<table width="100%" class="tblLista">
    <thead>
    <tr>
        <th scope="col" width="15"></th>
        <th scope="col" width="30" onclick="order('CodConcepto', '', 'ffOrderBy')">Cod.</th>
        <th scope="col" align="left" colspan="2" onclick="order('NomConcepto', '', 'ffOrderBy')">Concepto</th>
        <th scope="col" width="55" onclick="order('PeriodoDesde', '', 'ffOrderBy')">Desde</th>
        <th scope="col" width="55" onclick="order('PeriodoHasta', '', 'ffOrderBy')">Hasta</th>
        <th scope="col" width="20" onclick="order('FlagManual', '', 'ffOrderBy')">Man.</th>
        <th scope="col" width="75" align="right" onclick="order('Monto', '', 'ffOrderBy')">Monto</th>
        <th scope="col" width="35" align="right" onclick="order('Cantidad', '', 'ffOrderBy')">Cant.</th>
        <th scope="col" width="150" onclick="order('Procesos', '', 'ffOrderBy')">Procesos</th>
        <th scope="col" width="60" onclick="order('Estado', '', 'ffOrderBy')">Estado</th>
    </tr>
    </thead>
    
    <tbody id="lista_registros">
    <?php
    //	consulto todos
    $sql = "SELECT ec.CodConcepto
            FROM
				pr_empleadoconcepto ec
				INNER JOIN pr_concepto c ON (c.CodConcepto = ec.CodConcepto)
            WHERE
				ec.CodPersona = '".$CodPersona."'
				$filtro";
    $query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
    $rows_total = mysql_num_rows($query);
    
    //	consulto lista
    $sql = "SELECT
				ec.CodConcepto,
				ec.TipoAplicacion,
				ec.FlagManual,
				ec.PeriodoDesde,
				ec.PeriodoHasta,
				ec.Monto,
				ec.Cantidad,
				ec.Procesos,
				ec.Estado,
				c.Descripcion AS NomConcepto
            FROM
				pr_empleadoconcepto ec
				INNER JOIN pr_concepto c ON (c.CodConcepto = ec.CodConcepto)
            WHERE
				ec.CodPersona = '".$CodPersona."'
				$filtro
            ORDER BY $ffOrderBy
            LIMIT ".intval($limit).", ".intval($maxlimit);
    $query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
    $rows_lista = mysql_num_rows($query);	$i=0;
    while ($field = mysql_fetch_array($query)) {
        $id = $CodPersona."_".$field['CodConcepto'];
        ?>
        <tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
            <th><?=++$i?></th>
            <td align="center"><?=$field['CodConcepto']?></td>
            <td><?=htmlentities($field['NomConcepto'])?></td>
            <td align="center" width="10"><?=$field['TipoAplicacion']?></td>
            <td align="center"><?=$field['PeriodoDesde']?></td>
            <td align="center"><?=$field['PeriodoHasta']?></td>
            <td align="center"><?=printFlag($field['FlagManual'])?></td>
            <td align="right"><?=number_format($field['Monto'], 2, ',', '.')?></td>
            <td align="right"><?=number_format($field['Cantidad'], 2, ',', '.')?></td>
            <td align="center"><?=$field['Procesos']?></td>
            <td align="center"><?=printValoresGeneral("ESTADO", $field['Estado'])?></td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>
</div>
</center>
</form>