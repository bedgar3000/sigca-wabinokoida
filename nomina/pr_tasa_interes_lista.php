<?php
//	------------------------------------
if ($filtrar == "default") {
	$fAnio = substr(getVar3("SELECT MAX(Periodo) FROM masttasainteres WHERE Estado = 'A'"), 0, 4);
	$fEstado = "A";
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "Periodo DESC";
}
if ($fAnio != "") {
	$cAnio = "checked";
	$filtro .= " AND (Periodo LIKE '".$fAnio."-%')";
} else $dAnio = "disabled";
//	------------------------------------
$_width = 500;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo">Tasas de Interes</td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=pr_tasa_interes_lista" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />

<!--REGISTROS-->
<center>
<input type="hidden" name="sel_registros" id="sel_registros" />
<table width="<?=$_width?>" class="tblBotones">
    <tr>
        <td>
        	Anio: 
        	<select name="fAnio" id="fAnio" onchange="this.form.submit();">
        		<option value="">&nbsp;</option>
        		<?=getPeriodosTasas($fAnio)?>
        	</select>
        </td>
        <td align="right">
            <input type="button" id="btNuevo" value="Nuevo" style="width:75px; <?=$btNuevo?>" onclick="cargarPagina(this.form, 'gehen.php?anz=pr_tasa_interes_form&opcion=nuevo');" />
            <input type="button" id="btModificar" value="Modificar" style="width:75px; <?=$btModificar?>" onclick="cargarOpcion2(this.form, 'gehen.php?anz=pr_tasa_interes_form&opcion=modificar', 'SELF', '', $('#sel_registros').val());" />
            <input type="button" id="btVer" value="Ver" style="width:75px; <?=$btVer?>" onclick="cargarOpcion2(this.form, 'gehen.php?anz=pr_tasa_interes_form&opcion=ver', 'SELF', '', $('#sel_registros').val());" />
        </td>
    </tr>
</table>

<div style="overflow:scroll; width:<?=$_width?>px; height:350px;">
<table width="100%" class="tblLista">
	<thead>
    <tr>
        <th scope="col" width="100" onclick="order('Periodo')">Periodo</th>
        <th scope="col" width="100" onclick="order('Fecha')">Fecha</th>
        <th scope="col" align="right" onclick="order('Porcentaje')">Porcentaje</th>
        <th scope="col" width="100" onclick="order('Estado')">Estado</th>
    </tr>
    </thead>
    
    <tbody id="lista_registros">
	<?php
	//	consulto todos
	$sql = "SELECT Periodo
			FROM masttasainteres
			WHERE 1 $filtro";
	$rows_total = getNumRows3($sql);
	
	//	consulto lista
	$sql = "SELECT
				Periodo,
				Porcentaje,
				Fecha,
				Estado
			FROM masttasainteres
			WHERE 1 $filtro
			ORDER BY $fOrderBy
			LIMIT ".intval($limit).", ".intval($maxlimit);
	$field = getRecords($sql);
	$rows_lista = count($field);
	foreach($field as $f) {
		$id = $f['Periodo'];
		?>
		<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
			<td align="center"><?=$f['Periodo']?></td>
			<td align="center"><?=formatFechaDMA($f['Fecha'])?></td>
			<td align="right"><?=number_format($f['Porcentaje'], 2, ',', '.')?></td>
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