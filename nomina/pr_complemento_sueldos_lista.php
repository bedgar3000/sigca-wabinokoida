<?php
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["ORGANISMO_ACTUAL"];
	$fPeriodo = "$AnioActual-$MesActual";
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "Nomina,Periodo,Proceso";
}
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (pp.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fCodTipoNom != "") { $cCodTipoNom = "checked"; $filtro.=" AND (pp.CodTipoNom = '".$fCodTipoNom."')"; } else $dCodTipoNom = "disabled";
if ($fCodTipoProceso != "") { $cCodTipoProceso = "checked"; $filtro.=" AND (pp.CodTipoProceso = '".$fCodTipoProceso."')"; } else $dCodTipoProceso = "disabled";
if ($fPeriodo != "") { $cPeriodo = "checked"; $filtro.=" AND (pp.Periodo = '".$fPeriodo."')"; } else $dPeriodo = "disabled";
//	------------------------------------
$_titulo = "Complemento de Sueldos";
$_width = 900;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=pr_complemento_sueldos_lista" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />

<!--FILTRO-->
<div class="divBorder" style="width:100%; max-width:<?=$_width?>px;">
	<table class="tblFiltro" style="width:100%; max-width:<?=$_width?>px;">
		<tr>
			<td align="right" width="75">Organismo:</td>
			<td>
				<input type="checkbox" <?=$cCodOrganismo?> onclick="this.checked=!this.checked" />
				<select name="fCodOrganismo" id="fCodOrganismo" style="width:275px;" <?=$dCodOrganismo?>>
					<?=getOrganismos($fCodOrganismo, 3)?>
				</select>
			</td>
			<td align="right" width="75">N&oacute;mina:</td>
			<td>
				<input type="checkbox" <?=$cCodTipoNom?> onclick="chkFiltro(this.checked, 'fCodTipoNom');" />
				<select name="fCodTipoNom" id="fCodTipoNom" style="width:200px;" <?=$dCodTipoNom?>>
	            	<option value="">&nbsp;</option>
					<?=loadSelect("tiponomina", "CodTipoNom", "Nomina", $fCodTipoNom, 0)?>
				</select>
			</td>
	        <td>&nbsp;</td>
		</tr>
		<tr>
			<td align="right">Periodo:</td>
			<td>
				<input type="checkbox" <?=$cPeriodo?> onclick="chkFiltro(this.checked, 'fPeriodo');" />
				<input type="text" name="fPeriodo" id="fPeriodo" value="<?=$fPeriodo?>" style="width:60px;" <?=$dPeriodo?> />
			</td>
			<td align="right">Proceso:</td>
			<td>
				<input type="checkbox" <?=$cCodTipoProceso?> onclick="chkFiltro(this.checked, 'fCodTipoProceso');" />
				<select name="fCodTipoProceso" id="fCodTipoProceso" style="width:200px;" <?=$dCodTipoProceso?>>
	            	<option value="">&nbsp;</option>
					<?=loadSelect("pr_tipoproceso", "CodTipoProceso", "Descripcion", $fCodTipoProceso, 0)?>
				</select>
	        <td align="right"><input type="submit" value="Buscar"></td>
		</tr>
	</table>
</div>
<div class="sep"></div>

<!--REGISTROS-->
<center>
<input type="hidden" name="sel_registros" id="sel_registros" />
<table class="tblBotones" style="width:100%; max-width:<?=$_width?>px;">
    <tr>
        <td><div id="rows"></div></td>
        <td align="right">
            <input type="button" value="Dias Descanso" style="width:100px;" class="update" onclick="cargarOpcion2(this.form, 'gehen.php?anz=pr_complemento_sueldos_dias&opcion=agregar', 'SELF', '', $('#sel_registros').val());" />
            <input type="button" value="Horas Extras" style="width:100px;" class="update" onclick="cargarOpcion2(this.form, 'gehen.php?anz=pr_complemento_sueldos_horas&opcion=agregar', 'SELF', '', $('#sel_registros').val());" />
        </td>
    </tr>
</table>

<div class="scroll" style="overflow:scroll; width:100%; max-width:<?=$_width?>px; height:265px;">
<table class="tblLista" style="width:100%; max-width:<?=$_width?>px;">
	<thead>
    <tr>
        <th width="25">&nbsp;</th>
        <th width="300" align="left" onclick="order('Nomina,Periodo,Proceso')">N&oacute;mina</th>
        <th width="50" onclick="order('Periodo,Nomina,Proceso')">A&ntilde;o</th>
        <th width="25" onclick="order('Periodo,Nomina,Proceso')">Mes</th>
        <th align="left" onclick="order('Proceso,Nomina,Periodo')">Proceso</th>
    </tr>
    </thead>
    
    <tbody id="lista_registros">
	<?php
	$i = intval($limit);
	//	consulto todos
	$sql = "SELECT *
			FROM
				pr_procesoperiodo pp
				INNER JOIN tiponomina tn ON (tn.CodTipoNom = pp.CodTipoNom)
				INNER JOIN pr_tipoproceso tp ON (tp.CodTipoProceso = pp.CodTipoProceso)
			WHERE pp.FlagPagado = 'N' $filtro";
	$rows_total = getNumRows3($sql);
	//	consulto lista
	$sql = "SELECT
				pp.*,
				SUBSTRING(pp.Periodo,1,4) AS Anio,
				SUBSTRING(pp.Periodo,6,2) AS Mes,
				tn.Nomina,
				tp.Descripcion AS Proceso
			FROM
				pr_procesoperiodo pp
				INNER JOIN tiponomina tn ON (tn.CodTipoNom = pp.CodTipoNom)
				INNER JOIN pr_tipoproceso tp ON (tp.CodTipoProceso = pp.CodTipoProceso)
			WHERE pp.FlagPagado = 'N' $filtro
			ORDER BY $fOrderBy
			LIMIT ".intval($limit).", ".intval($maxlimit);
	$field = getRecords($sql);
	$rows_lista = count($field);
	foreach($field as $f) {
		$id = $f['CodOrganismo'].'_'.$f['CodTipoNom'].'_'.$f['Periodo'].'_'.$f['CodTipoProceso'];
		?>
		<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
			<th><?=++$i?></th>
			<td><?=htmlentities($f['Nomina'])?></td>
			<td align="center"><?=$f['Anio']?></td>
			<td align="center"><?=$f['Mes']?></td>
			<td><?=htmlentities($f['Proceso'])?></td>
		</tr>
		<?php
	}
	?>
    </tbody>
</table>
</div>

<table style="width:100%; max-width:<?=$_width?>px;">
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