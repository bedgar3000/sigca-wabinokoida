<?php
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fEstado = "PA";
	$fOrderBy = "CodOrganismo";
	if ($ventana == 'obligaciones_proveedor') {
		$fCodProveedor = $CodProveedor;
		$fProveedor = getVar3("SELECT NomCompleto FROM mastpersonas WHERE CodPersona='".$CodProveedor."'");
	}
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (o.CodTipoDocumento LIKE '%".$fBuscar."%'
					  OR o.NroDocumento LIKE '%".$fBuscar."%'
					  OR CONCAT(o.CodTipoDocumento, '-', o.NroDocumento) LIKE '%".$fBuscar."%'
				)";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (o.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (o.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fCodProveedor != "") { $cCodProveedor = "checked"; $filtro.=" AND (o.CodProveedor = '".$fCodProveedor."')"; } else $dCodProveedor = "visibility:hidden;";
//	------------------------------------
?>
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lista_obligaciones" method="post">
<input type="hidden" name="registro" id="registro" />
<input type="hidden" name="campo1" id="campo1" value="<?=$campo1?>" />
<input type="hidden" name="campo2" id="campo2" value="<?=$campo2?>" />
<input type="hidden" name="campo3" id="campo3" value="<?=$campo3?>" />
<input type="hidden" name="campo4" id="campo4" value="<?=$campo4?>" />
<input type="hidden" name="campo5" id="campo5" value="<?=$campo5?>" />
<input type="hidden" name="campo6" id="campo6" value="<?=$campo6?>" />
<input type="hidden" name="campo7" id="campo7" value="<?=$campo7?>" />
<input type="hidden" name="campo8" id="campo8" value="<?=$campo8?>" />
<input type="hidden" name="campo9" id="campo9" value="<?=$campo9?>" />
<input type="hidden" name="campo10" id="campo10" value="<?=$campo10?>" />
<input type="hidden" name="ventana" id="ventana" value="<?=$ventana?>" />
<input type="hidden" name="detalle" id="detalle" value="<?=$detalle?>" />
<input type="hidden" name="modulo" id="modulo" value="<?=$modulo?>" />
<input type="hidden" name="accion" id="accion" value="<?=$accion?>" />
<input type="hidden" name="url" id="url" value="<?=$url?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />

<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
	<tr>
		<td align="right" width="125">Organismo:</td>
		<td>
			<input type="checkbox" <?=$cCodOrganismo?> onclick="this.checked=!this.checked" />
			<select name="fCodOrganismo" id="fCodOrganismo" style="width:260px;" <?=$dCodOrganismo?>>
				<?=getOrganismos($fCodOrganismo, 3)?>
			</select>
		</td>
		<td align="right" width="100">Proveedor:</td>
		<td class="gallery clearfix">
        	<?php
			if ($ventana == 'obligaciones_proveedor') {
				?>
                <input type="checkbox" <?=$cCodProveedor?> onclick="this.checked=!this.checked" />
                <input type="text" name="fCodProveedor" id="fCodProveedor" value="<?=$fCodProveedor?>" style="width:40px;" readonly />
                <input type="text" name="fProveedor" id="fProveedor" value="<?=$fProveedor?>" style="width:205px;" readonly />
                <?php
			}
			else {
				?>
                <input type="checkbox" <?=$cCodProveedor?> onclick="ckLista(this.checked, ['fCodProveedor'], ['btCodProveedor']);" />
                <input type="text" name="fCodProveedor" id="fCodProveedor" value="<?=$fCodProveedor?>" style="width:40px;" readonly />
                <input type="text" name="fProveedor" id="fProveedor" value="<?=$fProveedor?>" style="width:205px;" readonly />
                <a href="../lib/listas/gehen.php?anz=lista_personas&filtrar=default&campo1=fCodProveedor&campo2=fProveedor&ventana=selListaOpener&iframe=true&width=950&height=430" rel="prettyPhoto[iframe1]" style=" <?=$dCodProveedor?>" id="btCodProveedor">
                    <img src="../../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
                </a>
                <?php
			}
			?>
		</td>
        <td>&nbsp;</td>
	</tr>
	<tr>
		<td align="right">Buscar:</td>
		<td>
			<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
			<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:250px;" <?=$dBuscar?> />
		</td>
		<td align="right">Estado: </td>
		<td>
            <input type="checkbox" <?=$cEstado?> onclick="this.checked=!this.checked;" />
            <select name="fEstado" id="fEstado" style="width:100px;" <?=$dEstado?>>
            	<option value="">&nbsp;</option>
                <?=loadSelectGeneral("ESTADO-OBLIGACIONES", $fEstado, 0)?>
            </select>
		</td>
        <td align="right"><input type="submit" value="Buscar"></td>
	</tr>
</table>
</div>
<div class="sep"></div>

<center>
<div style="overflow:scroll; height:315px; width:100%; min-width:<?=$_width?>px;">
<table class="tblLista" style="width:100%; min-width:800px;">
	<thead>
    <tr>
        <th align="left" onclick="order('Proveedor')">Proveedor</th>
        <th width="200" colspan="2" onclick="order('Documento')">Documento</th>
        <th width="90" onclick="order('Estado')">Estado</th>
    </tr>
    </thead>
    
    <tbody>
	<?php
	//	consulto todos
	$sql = "SELECT
				o.*,
				CONCAT(o.CodTipoDocumento, '-', o.NroDocumento) AS Documento,
				p.NomCompleto AS Proveedor
			FROM
				ap_obligaciones o
				INNER JOIN mastpersonas p ON (p.CodPersona = o.CodProveedor)
			WHERE 1 $filtro";
	$rows_total = getNumRows3($sql);
	//	consulto lista
	$sql = "SELECT
				o.*,
				CONCAT(o.CodTipoDocumento, '-', o.NroDocumento) AS Documento,
				p.NomCompleto AS Proveedor
			FROM
				ap_obligaciones o
				INNER JOIN mastpersonas p ON (p.CodPersona = o.CodProveedor)
			WHERE 1 $filtro
			ORDER BY $fOrderBy
			LIMIT ".intval($limit).", ".intval($maxlimit);
	$field = getRecords($sql);
	$rows_lista = count($field);
	foreach($field as $f) {
		if ($ventana == "obligaciones_proveedor") {
			?>
        	<tr class="trListaBody" onClick="selLista(['<?=$f['CodProveedor']?>','<?=$f['CodTipoDocumento']?>','<?=$f['NroDocumento']?>','<?=$f['Proveedor']?>','<?=$f['NroControl']?>','<?=formatFechaDMA($f['FechaDocumento'])?>','<?=formatFechaDMA($f['FechaFactura'])?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>','<?=$campo4?>','<?=$campo5?>','<?=$campo6?>','<?=$campo7?>']);">
            <?php
		}
		else {
			?>
        	<tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['CodProveedor']?>','<?=$f['CodTipoDocumento']?>','<?=$f['NroDocumento']?>','<?=$f['Proveedor']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>','<?=$campo4?>']);">
            <?php
		}
		?>
			<td><?=htmlentities($f['Proveedor'])?></td>
			<td align="center" width="10"><?=$f['CodTipoDocumento']?></td>
			<td width="125"><?=$f['NroDocumento']?></td>
			<td align="center"><?=printValoresGeneral("ESTADO-OBLIGACIONES", $f['Estado'])?></td>
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