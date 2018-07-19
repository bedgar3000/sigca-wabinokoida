<?php
list($AnioActual, $MesActual, $DiaActual) = split("[/.-]", substr($Ahora, 0, 10));
$FechaActual = "$AnioActual-$MesActual-$DiaActual";
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["ORGANISMO_ACTUAL"];
	$fPeriodo = "$AnioActual-$MesActual";
	$fOrderBy = "CodOrganismo";
	$maxlimit = $_SESSION["MAXLIMIT"];
}
if ($fEstado == "") $filtro.=" AND (pp.Estado = 'A')"; else $cEstado = "checked";
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (pc.Tipo LIKE '%".$fBuscar."%' OR
					  pc.CodProceso LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (pp.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fCodTipoNom != "") { $cCodTipoNom = "checked"; $filtro.=" AND (pp.CodTipoNom = '".$fCodTipoNom."')"; } else $dCodTipoNom = "disabled";
if ($fCodTipoProceso != "") { $cCodTipoProceso = "checked"; $filtro.=" AND (pp.CodTipoProceso = '".$fCodTipoProceso."')"; } else $dCodTipoProceso = "disabled";
if ($fPeriodo != "") { $cPeriodo = "checked"; $filtro.=" AND (pp.Periodo = '".$fPeriodo."')"; } else $dPeriodo = "disabled";
//	------------------------------------
$_titulo = "Control de Procesos (Pre-N&oacute;mina)";
$_width = 900;
$_sufijo = "prenomina_procesos";
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
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="sel_registros" id="sel_registros" />

<div class="divBorder" style="width:<?=$_width?>px;">
<table width="<?=$_width?>" class="tblFiltro">
	<tr>
		<td align="right" width="125">Organismo:</td>
		<td>
			<input type="checkbox" <?=$cCodOrganismo?> onclick="this.checked=!this.checked" />
			<select name="fCodOrganismo" id="fCodOrganismo" style="width:275px;" <?=$dCodOrganismo?>>
				<?=getOrganismos($fCodOrganismo, 3)?>
			</select>
		</td>
		<td align="right" width="125">N&oacute;mina:</td>
		<td>
			<input type="checkbox" <?=$cCodTipoNom?> onclick="chkFiltro(this.checked, 'fCodTipoNom');" />
			<select name="fCodTipoNom" id="fCodTipoNom" style="width:250px;" <?=$dCodTipoNom?>>
            	<option value="">&nbsp;</option>
				<?=loadSelect("tiponomina", "CodTipoNom", "Nomina", $fCodTipoNom, 0)?>
			</select>
		</td>
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
			<select name="fCodTipoProceso" id="fCodTipoProceso" style="width:250px;" <?=$dCodTipoProceso?>>
            	<option value="">&nbsp;</option>
				<?=loadSelect("pr_tipoproceso", "CodTipoProceso", "Descripcion", $fCodTipoProceso, 0)?>
			</select>
		</td>
	</tr>
	<tr>
		<td align="right">&nbsp;</td>
		<td>
            <input type="checkbox" name="fEstado" id="fEstado" value="A" <?=$cEstado?> /> Mostrar procesos inactivos
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
            <input type="button" id="btNuevo" value="Iniciar Periodo" style="width:90px; <?=$btNuevo?>" onclick="cargarPagina(this.form, 'gehen.php?anz=pr_<?=$_sufijo?>_form&opcion=nuevo&action=pr_<?=$_sufijo?>_lista');" /> |
            
            <input type="button" id="btModificar" value="Modificar" style="width:75px; <?=$btModificar?>" onclick="cargarOpcionValidar2(this.form, $('#sel_registros').val(), 'accion=<?=$_sufijo?>_modificar', 'gehen.php?anz=pr_<?=$_sufijo?>_form&opcion=modificar&action=pr_<?=$_sufijo?>_lista', 'SELF', '');" />
            
            <input type="button" id="btVer" value="Ver" style="width:75px; <?=$btVer?>" onclick="cargarOpcion2(this.form, 'gehen.php?anz=pr_<?=$_sufijo?>_form&opcion=ver&action=pr_<?=$_sufijo?>_lista', 'SELF', '', $('#sel_registros').val());" />
        </td>
    </tr>
</table>
<div style="overflow:scroll; width:<?=$_width?>px; height:350px;">
<table width="100%" class="tblLista">
    <thead>
    <tr>
        <th scope="col" width="15"></th>
        <th scope="col" width="250" align="left" onclick="order('Nomina,Periodo,NomTipoProceso')">N&oacute;mina</th>
        <th scope="col" width="35" onclick="order('Periodo,Nomina,NomTipoProceso')">A&ntilde;o</th>
        <th scope="col" width="20" onclick="order('Periodo,Nomina,NomTipoProceso')">Mes</th>
        <th scope="col" align="left" onclick="order('NomTipoProceso,Periodo,Nomina')">Proceso</th>
        <th scope="col" width="15" onclick="order('Estado,FlagProcesado')">Est.</th>
    </tr>
    </thead>
    
    <tbody id="lista_registros">
    <?php
    //	consulto todos
    $sql = "SELECT
				pp.CodOrganismo,
				pp.CodTipoNom,
				pp.Periodo,
				pp.CodTipoProceso
            FROM
				pr_procesoperiodoprenomina pp
				INNER JOIN tiponomina tn ON (tn.CodTipoNom = pp.CodTipoNom)
				INNER JOIN pr_tipoproceso tp ON (tp.CodTipoProceso = pp.CodTipoProceso)
            WHERE 1 $filtro";
    $query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
    $rows_total = mysql_num_rows($query);
    
    //	consulto lista
    $sql = "SELECT
				pp.CodOrganismo,
				pp.CodTipoNom,
				pp.Periodo,
				pp.CodTipoProceso,
				pp.Estado,
				pp.FlagProcesado,
				tn.Nomina,
				tp.Descripcion AS NomTipoProceso
            FROM
				pr_procesoperiodoprenomina pp
				INNER JOIN tiponomina tn ON (tn.CodTipoNom = pp.CodTipoNom)
				INNER JOIN pr_tipoproceso tp ON (tp.CodTipoProceso = pp.CodTipoProceso)
            WHERE 1 $filtro
            ORDER BY $fOrderBy
            LIMIT ".intval($limit).", ".intval($maxlimit);
    $query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
    $rows_lista = mysql_num_rows($query);	$i=0;
    while ($field = mysql_fetch_array($query)) {
        $id = "$field[CodOrganismo]_$field[CodTipoNom]_$field[Periodo]_$field[CodTipoProceso]";
		list($Anio, $Mes) = split("[./-]", $field['Periodo']);
        ?>
        <tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
            <th><?=++$i?></th>
            <td><?=htmlentities($field['Nomina'])?></td>
            <td align="center"><?=$Anio?></td>
            <td align="center"><?=$Mes?></td>
            <td><?=htmlentities($field['NomTipoProceso'])?></td>
            <td align="center"><?=printEstadoProceso($field['Estado'], $field['FlagProcesado'], $field['FlagPagado'])?></td>
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