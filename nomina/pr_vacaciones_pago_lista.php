<?php
list($AnioActual, $MesActual, $DiaActual) = split("[/.-]", substr($Ahora, 0, 10));
$FechaActual = "$AnioActual-$MesActual-$DiaActual";
//	------------------------------------
if ($filtrar == "default") {
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "CodEmpleado";
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$fCodTipoNom = $_SESSION["NOMINA_ACTUAL"];
	$fPeriodo = "$AnioActual-$MesActual";
	$fCodTipoProceso = "BVC";
}
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (tne.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fCodTipoNom != "") { $cCodTipoNom = "checked"; $filtro.=" AND (tne.CodTipoNom = '".$fCodTipoNom."')"; } else $dCodTipoNom = "disabled";
if ($fPeriodo != "") { $cPeriodo = "checked"; $filtro.=" AND (tne.Periodo = '".$fPeriodo."')"; } else $dPeriodo = "disabled";
if ($fCodTipoProceso != "") { $cCodTipoProceso = "checked"; $filtro.=" AND (tne.CodTipoProceso = '".$fCodTipoProceso."')"; } else $dCodTipoProceso = "disabled";
//	------------------------------------
$_width = 1000;
$_sufijo = "vacaciones_pago";
$_titulo = "Participaci&oacute;n de Pago de Vacaciones";
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=pr_<?=$_sufijo?>_lista" method="post" autocomplete="off">
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
			<input type="checkbox" checked="checked" onclick="this.checked=!this.checked" />
			<select name="fCodOrganismo" id="fCodOrganismo" style="width:270px;">
				<?=getOrganismos($fCodOrganismo, 3)?>
			</select>
		</td>
		<td align="right" width="100">N&oacute;mina:</td>
		<td>
			<input type="checkbox" checked="checked" onclick="this.checked=!this.checked" />
			<select name="fCodTipoNom" id="fCodTipoNom" style="width:270px;">
				<?=loadSelect2("tiponomina", "CodTipoNom", "Nomina", $fCodTipoNom)?>
			</select>
		</td>
	</tr>
	<tr>
		<td align="right">Periodo:</td>
		<td>
			<input type="checkbox" checked="checked" onclick="this.checked=!this.checked" />
			<select name="fPeriodo" id="fPeriodo" style="width:75px;">
            	<?=loadSelectPeriodosNomina($fPeriodo, $fCodOrganismo, $fCodTipoNom)?>
			</select>
		</td>
		<td align="right">Proceso:</td>
		<td>
			<input type="checkbox" checked="checked" onclick="this.checked=!this.checked" />
			<select name="fCodTipoProceso" id="fCodTipoProceso" style="width:270px;">
            	<?=loadSelectProcesosVacaciones($fCodTipoProceso)?>
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
        <td align="right" class="gallery clearfix">
        	<a href="pagina.php?iframe=true" rel="prettyPhoto[iframe1]" style="display:none;" id="a_imprimir"></a>
            <input type="button" id="btImprimir" value="Imprimir" style="width:75px; <?=$btImprimir?>" onclick="abrirIFrame(this.form, 'a_imprimir', 'pr_vacaciones_pago_pdf.php?', '100%', '100%', $('#sel_registros').val());" />
        </td>
    </tr>
</table>

<div style="overflow:scroll; width:<?=$_width?>px; height:300px;">
<table width="1450" class="tblLista">
	<thead>
    <tr>
        <th scope="col" width="35" onclick="order('CodEmpleado')"><a href="javascript:">Empleado</a></th>
        <th scope="col" width="350" align="left" onclick="order('NomCompleto')"><a href="javascript:">Nombre Completo</a></th>
        <th scope="col" width="400" align="left" onclick="order('DescripCargo')"><a href="javascript:">Cargo</a></th>
        <th scope="col" align="left" onclick="order('Dependencia')"><a href="javascript:">Dependencia</a></th>
        <th scope="col" width="75" onclick="order('Estado')"><a href="javascript:">Sit. Tra.</a></th>
    </tr>
    </thead>
    
    <tbody id="lista_registros">
	<?php
	//	consulto todos
	$sql = "SELECT
				e.CodPersona,
				e.CodEmpleado,
				e.Estado,
				p.NomCompleto,
				p.Ndocumento,
				d.Dependencia,
				pt.DescripCargo
			FROM
				pr_tiponominaempleado tne
				INNER JOIN mastempleado e ON (tne.CodPersona = e.CodPersona)
				INNER JOIN mastpersonas p ON (e.CodPersona = p.CodPersona)
				INNER JOIN mastdependencias d ON (e.CodDependencia = d.CodDependencia)
				INNER JOIN rh_puestos pt ON (e.CodCargo = pt.CodCargo)
			WHERE 1 $filtro";
	$query = mysql_query($sql) or die ($sql.mysql_error());
	$rows_total = mysql_num_rows($query);
	
	//	consulto lista
	$sql = "SELECT
				e.CodPersona,
				e.CodEmpleado,
				e.Estado,
				p.NomCompleto,
				p.Ndocumento,
				d.Dependencia,
				pt.DescripCargo
			FROM
				pr_tiponominaempleado tne
				INNER JOIN mastempleado e ON (tne.CodPersona = e.CodPersona)
				INNER JOIN mastpersonas p ON (e.CodPersona = p.CodPersona)
				INNER JOIN mastdependencias d ON (e.CodDependencia = d.CodDependencia)
				INNER JOIN rh_puestos pt ON (e.CodCargo = pt.CodCargo)
			WHERE 1 $filtro
			ORDER BY $fOrderBy
			LIMIT ".intval($limit).", ".intval($maxlimit);
	$query = mysql_query($sql) or die ($sql.mysql_error());
	$rows_lista = mysql_num_rows($query);
	while ($field = mysql_fetch_array($query)) {
		$id = "$field[CodPersona]";
		?>
		<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
			<td align="center"><?=$field['CodEmpleado']?></td>
			<td><?=htmlentities($field['NomCompleto'])?></td>
			<td><?=htmlentities($field['DescripCargo'])?></td>
			<td><?=htmlentities($field['Dependencia'])?></td>
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