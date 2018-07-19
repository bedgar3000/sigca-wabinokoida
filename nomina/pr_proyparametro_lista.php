<?php
if ($filtrar == "default") {

	$sql = "SELECT MAX(Ejercicio) FROM pr_proyparametro";
	$Ejercicio = getVar3($sql);
	$fEjercicio = ($Ejercicio?$Ejercicio:$AnioActual);
	
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "CodParametro";
}
//	------------------------------------
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (ppr.Ejercicio LIKE '%".$fBuscar."%' OR
					  tp.Descripcion LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (ppm.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fEjercicio != "") { $cEjercicio = "checked"; $filtro.=" AND (ppr.Ejercicio = '".$fEjercicio."')"; } else $dEjercicio = "disabled";
if ($fCodTipoProceso != "") { $cCodTipoProceso = "checked"; $filtro.=" AND (ppm.CodTipoProceso = '".$fCodTipoProceso."')"; } else $dCodTipoProceso = "disabled";
//	------------------------------------
$_titulo = "Definici&oacute;n de Par&aacute;metros";
$_width = 900;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=pr_proyparametro_lista" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />

<!--FILTRO-->
<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
	<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
		<tr>
			<td align="right" width="100">Proceso:</td>
			<td>
	            <input type="checkbox" <?=$cCodTipoProceso?> onclick="chkFiltro(this.checked, 'fCodTipoProceso');" />
				<select name="fCodTipoProceso" id="fCodTipoProceso" style="width:275px;" <?=$dCodTipoProceso?>>
	                <option value="">&nbsp;</option>
					<?=loadSelect2('pr_tipoproceso','CodTipoproceso','Descripcion',$fCodTipoProceso)?>
				</select>
			</td>
			<td align="right" width="150">Estado: </td>
			<td>
	            <input type="checkbox" <?=$cEstado?> onclick="chkFiltro(this.checked, 'fEstado');" />
	            <select name="fEstado" id="fEstado" style="width:107px;" <?=$dEstado?>>
	                <option value="">&nbsp;</option>
	                <?=loadSelectGeneral("ESTADO", $fEstado, 0)?>
	            </select>
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td align="right">Buscar: </td>
			<td>
				<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
				<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:275px;" <?=$dBuscar?> />
			</td>
			<td align="right">Ejercicio: </td>
			<td>
				<input type="checkbox" <?=$cEjercicio?> onclick="chkCampos(this.checked, 'fEjercicio');" />
				<input type="text" name="fEjercicio" id="fBuscar" value="<?=$fEjercicio?>" style="width:45px;" maxlength="4" <?=$dEjercicio?> />
			</td>
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
            <input type="button" value="Nuevo" style="width:75px;" class="insert" onclick="cargarPagina(this.form, 'gehen.php?anz=pr_proyparametro_form&opcion=nuevo');" />
            <input type="button" value="Modificar" style="width:75px;" class="update" onclick="cargarOpcion2(this.form, 'gehen.php?anz=pr_proyparametro_form&opcion=modificar', 'SELF', '', $('#sel_registros').val());" />
            <input type="button" value="Ver" style="width:75px;" class="ver" onclick="cargarOpcion2(this.form, 'gehen.php?anz=pr_proyparametro_form&opcion=ver', 'SELF', '', $('#sel_registros').val());" />
            <input type="button" value="Establecer Par&aacute;metros" style="width:125px;" class="update" onclick="cargarOpcion2(this.form, 'gehen.php?anz=pr_proyparametro_parametros&accion=establecer', 'SELF', '', $('#sel_registros').val());" />
        </td>
    </tr>
</table>

<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px; margin:auto;">
	<table class="tblLista" style="width:100%; min-width:1200px;">
		<thead>
		    <tr>
		        <th width="60" onclick="order('Ejercicio')">Ejercicio</th>
		        <th width="30" onclick="order('Numero')">Nro.</th>
		        <th align="left" onclick="order('Nomina')">N&oacute;mina</th>
		        <th align="left" onclick="order('TipoProceso')">Proceso</th>
		        <th width="75" onclick="order('Estado')">Estado</th>
		    </tr>
	    </thead>
	    
	    <tbody id="lista_registros">
		<?php
		//	consulto todos
		$sql = "SELECT ppm.*
				FROM
					pr_proyparametro ppm
					INNER JOIN pr_tipoproceso tp ON (tp.CodTipoProceso = ppm.CodTipoProceso)
					INNER JOIN pr_proyrecursos ppr ON (ppr.CodRecurso = ppm.CodRecurso)
					INNER JOIN tiponomina tn ON (tn.CodTipoNom = ppr.CodTipoNom)
				WHERE 1 $filtro";
		$rows_total = getNumRows3($sql);
		//	consulto lista
		$sql = "SELECT
					ppm.*,
					tp.Descripcion AS TipoProceso,
					ppr.Ejercicio,
					ppr.Numero,
					tn.Nomina
				FROM
					pr_proyparametro ppm
					INNER JOIN pr_tipoproceso tp ON (tp.CodTipoProceso = ppm.CodTipoProceso)
					INNER JOIN pr_proyrecursos ppr ON (ppr.CodRecurso = ppm.CodRecurso)
					INNER JOIN tiponomina tn ON (tn.CodTipoNom = ppr.CodTipoNom)
				WHERE 1 $filtro
				ORDER BY $fOrderBy
				LIMIT ".intval($limit).", ".intval($maxlimit);
		$field = getRecords($sql);
		$rows_lista = count($field);
		foreach($field as $f) {
			$id = $f['CodParametro'];
			?>
			<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
				<td align="center"><?=$f['Ejercicio']?></td>
				<td align="center"><?=$f['Numero']?></td>
				<td><?=htmlentities($f['Nomina'])?></td>
				<td><?=htmlentities($f['TipoProceso'])?></td>
				<td align="center"><?=printValoresGeneral('ESTADO',$f['Estado'])?></td>
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