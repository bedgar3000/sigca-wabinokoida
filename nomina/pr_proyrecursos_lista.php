<?php
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];

	$sql = "SELECT MAX(Ejercicio) FROM pr_proyrecursos";
	$Ejercicio = getVar3($sql);
	$fEjercicio = ($Ejercicio?$Ejercicio:$AnioActual);
	
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "CodRecurso";
}
//	------------------------------------
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (pyr.Ejercicio LIKE '%".$fBuscar."%' OR
					  pyr.Numero LIKE '%".$fBuscar."%' OR
					  pyr.Descripcion LIKE '%".$fBuscar."%' OR
					  o.Organismo LIKE '%".$fBuscar."%' OR
					  tn.Nomina LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (pyr.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (pyr.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fCodTipoNom != "") { $cCodTipoNom = "checked"; $filtro.=" AND (pyr.CodTipoNom = '".$fCodTipoNom."')"; } else $dCodTipoNom = "disabled";
if ($fEjercicio != "") { $cEjercicio = "checked"; $filtro.=" AND (pyr.Ejercicio = '".$fEjercicio."')"; } else $dEjercicio = "disabled";
//	------------------------------------
$_titulo = "Planificaci&oacute;n de Recursos";
$_width = 900;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=pr_proyrecursos_lista" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />

<!--FILTRO-->
<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
	<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
		<tr>
			<td align="right" width="100">Organismo:</td>
			<td>
				<input type="checkbox" <?=$cCodOrganismo?> onclick="this.checked=!this.checked;" />
				<select name="fCodOrganismo" id="fCodOrganismo" style="width:275px;" <?=$dCodOrganismo?>>
					<?=getOrganismos($fCodOrganismo, 3);?>
				</select>
			</td>
			<td align="right" width="150">Estado: </td>
			<td>
	            <input type="checkbox" <?=$cEstado?> onclick="chkFiltro(this.checked, 'fEstado');" />
	            <select name="fEstado" id="fEstado" style="width:107px;" <?=$dEstado?>>
	                <option value="">&nbsp;</option>
	                <?=loadSelectValores("proypresupuestaria-estado", $fEstado, 0)?>
	            </select>
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td align="right">N&oacute;mina: </td>
			<td>
	            <input type="checkbox" <?=$cCodTipoNom?> onclick="chkFiltro(this.checked, 'fCodTipoNom');" />
	            <select name="fCodTipoNom" id="fCodTipoNom" style="width:275px;" <?=$dCodTipoNom?>>
	                <option value="">&nbsp;</option>
	                <?=loadSelect2("tiponomina", "CodTipoNom", "Nomina", $fCodTipoNom)?>
	            </select>
			</td>
			<td align="right">Ejercicio: </td>
			<td>
				<input type="checkbox" <?=$cEjercicio?> onclick="chkCampos(this.checked, 'fEjercicio');" />
				<input type="text" name="fEjercicio" id="fBuscar" value="<?=$fEjercicio?>" style="width:45px;" maxlength="4" <?=$dEjercicio?> />
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td align="right">Buscar: </td>
			<td>
				<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
				<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:275px;" <?=$dBuscar?> />
			</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
	        <td align="right"><input type="submit" value="Buscar"></td>
		</tr>
	</table>
</div>
<div class="sep"></div>

<!--REGISTROS-->
<input type="hidden" name="sel_registros" id="sel_registros" />
<table class="tblBotones" style="width:100%; min-width:<?=$_width?>px;">
    <tr>
        <td><div id="rows"></div></td>
        <td align="right">
            <input type="button" value="Nuevo" style="width:75px; <?=$_btNuevo?>" class="insert" onclick="cargarPagina(this.form, 'gehen.php?anz=pr_proyrecursos_form&opcion=nuevo');" />
            <input type="button" value="Modificar" style="width:75px; <?=$_btModificar?>" class="update" onclick="cargarOpcion2(this.form, 'gehen.php?anz=pr_proyrecursos_form&opcion=modificar', 'SELF', '', $('#sel_registros').val());" />
            <input type="button" value="Ver" style="width:75px;" class="ver" onclick="cargarOpcion2(this.form, 'gehen.php?anz=pr_proyrecursos_form&opcion=ver', 'SELF', '', $('#sel_registros').val());" />
        </td>
    </tr>
</table>

<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px; margin:auto;">
	<table class="tblLista" style="width:100%; min-width:1200px;">
		<thead>
		    <tr>
		        <th width="300" align="left" onclick="order('Organismo,Ejercicio,Numero')">Organismo</th>
		        <th width="60" onclick="order('Ejercicio,Numero')">Ejercicio</th>
		        <th width="35" onclick="order('Numero')">Nro.</th>
		        <th width="150" align="left" onclick="order('Nomina')">N&oacute;mina</th>
		        <th align="left" onclick="order('Descripcion')">Descripci&oacute;n</th>
		        <th width="100" onclick="order('Estado')">Estado</th>
		    </tr>
	    </thead>
	    
	    <tbody id="lista_registros">
		<?php
		//	consulto todos
		$sql = "SELECT pyr.*
				FROM
					pr_proyrecursos pyr
					INNER JOIN mastorganismos o ON (o.CodOrganismo = pyr.CodOrganismo)
					INNER JOIN tiponomina tn On (tn.CodTipoNom = pyr.CodTipoNom)
				WHERE 1 $filtro";
		$rows_total = getNumRows3($sql);
		//	consulto lista
		$sql = "SELECT
					pyr.*,
					o.Organismo,
					tn.Nomina
				FROM
					pr_proyrecursos pyr
					INNER JOIN mastorganismos o ON (o.CodOrganismo = pyr.CodOrganismo)
					INNER JOIN tiponomina tn On (tn.CodTipoNom = pyr.CodTipoNom)
				WHERE 1 $filtro
				ORDER BY $fOrderBy
				LIMIT ".intval($limit).", ".intval($maxlimit);
		$field = getRecords($sql);
		$rows_lista = count($field);
		foreach($field as $f) {
			$id = $f['CodRecurso'];
			?>
			<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
				<td><?=htmlentities($f['Organismo'])?></td>
				<td align="center"><?=$f['Ejercicio']?></td>
				<td align="center"><?=$f['Numero']?></td>
				<td><?=htmlentities($f['Nomina'])?></td>
				<td><?=htmlentities($f['Descripcion'])?></td>
				<td align="center"><?=printValores('proypresupuestaria-estado',$f['Estado'])?></td>
			</tr>
			<?php
		}
		?>
	    </tbody>
	</table>
</div>

<table style="width:100%; min-width:<?=$_width?>px; margin:auto;">
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
</form>