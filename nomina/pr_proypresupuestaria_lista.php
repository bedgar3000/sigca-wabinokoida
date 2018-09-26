<?php
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$sql = "SELECT MAX(Ejercicio) FROM pr_proypresupuestaria";
	$Ejercicio = getVar3($sql);
	$fEjercicio = ($Ejercicio?$Ejercicio:$AnioActual);
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "Ejercicio,CategoriaProg";
}
//	------------------------------------
if ($lista == "listar") {
	$_titulo = "Proyección Presupuestaria";
	$_btNuevo = "";
	$_btModificar = "";
	$_btAprobar = "display:none;";
}
elseif ($lista == "aprobar") {
	$fEstado = "AP";
	##	
	$_titulo = "Proyección Presupuestaria / Aprobar";
	$_btNuevo = "display:none;";
	$_btModificar = "display:none;";
	$_btAprobar = "";
}
//	------------------------------------
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (pp.CategoriaProg LIKE '%".$fBuscar."%' OR
					  pp.Descripcion LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (pp.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fCategoriaProg != "") { $cCategoriaProg = "checked"; $filtro.=" AND (pp.CategoriaProg = '".$fCategoriaProg."')"; } else $dCategoriaProg = "visibility:hidden;";
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (pp.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fEjercicio != "") { $cEjercicio = "checked"; $filtro.=" AND (pp.Ejercicio = '".$fEjercicio."')"; } else $dEjercicio = "disabled";
//	------------------------------------
$_width = 900;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=pr_proypresupuestaria_lista" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />

<!--FILTRO-->
<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
	<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
		<tr>
			<td align="right" width="150">Organismo: </td>
			<td>
	            <input type="checkbox" <?=$cCodOrganismo?> onclick="this.checked=!this.checked;" />
	            <select name="fCodOrganismo" id="fCodOrganismo" style="width:242px;" <?=$dCodOrganismo?> onchange="$('#aCategoriaProg').attr('href','../lib/listas/gehen.php?anz=lista_pv_categoriaprog&filtrar=default&ventana=&campo1=fCategoriaProg&FlagOrganismo=S&fCodOrganismo='+$(this).val()+'&iframe=true&width=100%&height=100%'); $('#fCategoriaProg').val('');">
	                <?=loadSelect2('mastorganismos','CodOrganismo','Organismo',$fCodOrganismo)?>
	            </select>
			</td>
			<td align="right" width="150">Estado: </td>
			<td>
				<?php
				if ($lista == "listar") {
					?>
		            <input type="checkbox" <?=$cEstado?> onclick="chkFiltro(this.checked, 'fEstado');" />
		            <select name="fEstado" id="fEstado" style="width:107px;" <?=$dEstado?>>
		                <option value="">&nbsp;</option>
		                <?=loadSelectValores("proypresupuestaria-estado", $fEstado, 0)?>
		            </select>
					<?php
				} else {
					?>
		            <input type="checkbox" <?=$cEstado?> onclick="this.checked=!this.checked;" />
		            <select name="fEstado" id="fEstado" style="width:107px;" <?=$dEstado?>>
		                <?=loadSelectValores("proypresupuestaria-estado", $fEstado, 1)?>
		            </select>
					<?php
				}
				?>
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td align="right">Categoria Prog.:</td>
			<td class="gallery clearfix">
				<input type="checkbox" <?=$cCategoriaProg?> onclick="ckLista(this.checked, ['fCategoriaProg'], ['aCategoriaProg']); $('#fCodObjetivo').html('<option>&nbsp;</option>');" />
				<input type="text" name="fCategoriaProg" id="fCategoriaProg" value="<?=$fCategoriaProg?>" style="width:100px;" readonly="readonly" />
				<a href="../lib/listas/gehen.php?anz=lista_pv_categoriaprog&filtrar=default&ventana=&campo1=fCategoriaProg&FlagOrganismo=S&fCodOrganismo=<?=$fCodOrganismo?>&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" id="aCategoriaProg" style=" <?=$dCategoriaProg?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
			<td align="right">Ejercicio: </td>
			<td>
				<input type="checkbox" <?=$cEjercicio?> onclick="chkCampos(this.checked, 'fEjercicio');" />
				<input type="text" name="fEjercicio" id="fEjercicio" value="<?=$fEjercicio?>" style="width:50px;" maxlength="4" <?=$dEjercicio?> />
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
            <input type="button" value="Nuevo" style="width:75px; <?=$_btNuevo?>" class="insert" onclick="cargarPagina(this.form, 'gehen.php?anz=pr_proypresupuestaria_form&opcion=nuevo&action=pr_proypresupuestaria_lista');" />
            <input type="button" value="Modificar" style="width:75px; <?=$_btModificar?>" class="update" onclick="cargarOpcionValidar3(this.form, $('#sel_registros').val(), 'pr_proypresupuestaria_ajax.php', 'modulo=validar&accion=modificar', 'gehen.php?anz=pr_proypresupuestaria_form&opcion=modificar&action=pr_proypresupuestaria_lista', 'SELF', '');" />
            <input type="button" value="Eliminar" style="width:75px; <?=$_btModificar?>" class="delete" onclick="opcionRegistro3(this.form, $('#sel_registros').val(), 'formulario', 'eliminar', 'pr_proypresupuestaria_ajax.php');" />
            <input type="button" value="Aprobar" style="width:75px; <?=$_btModificar?>" class="update" onclick="cargarOpcionValidar3(this.form, $('#sel_registros').val(), 'pr_proypresupuestaria_ajax.php', 'modulo=validar&accion=aprobar', 'gehen.php?anz=pr_proypresupuestaria_form&opcion=aprobar&action=pr_proypresupuestaria_lista', 'SELF', '');" />
            <input type="button" value="Ver" style="width:75px;" class="ver" onclick="cargarOpcion2(this.form, 'gehen.php?anz=pr_proypresupuestaria_form&opcion=ver&action=pr_proypresupuestaria_lista', 'SELF', '', $('#sel_registros').val());" />
        </td>
    </tr>
</table>

<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px; margin:auto;">
	<table class="tblLista" style="width:100%; min-width:1200px;">
		<thead>
		    <tr>
		        <th width="75" onclick="order('Ejercicio,CategoriaProg')">Ejercicio</th>
		        <th width="125" onclick="order('CategoriaProg')">Cat. Prog.</th>
		        <th align="left" onclick="order('Descripcion')">Descripci&oacute;n</th>
		        <th width="100" onclick="order('Estado')">Estado</th>
		    </tr>
	    </thead>
	    
	    <tbody id="lista_registros">
		<?php
		//	consulto todos
		$sql = "SELECT pp.*
				FROM
					pr_proypresupuestaria pp
					INNER JOIN pv_categoriaprog cp ON (cp.CategoriaProg = pp.CategoriaProg)
					INNER JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cp.CodUnidadEjec)
				WHERE 1 $filtro";
		$rows_total = getNumRows3($sql);
		//	consulto lista
		$sql = "SELECT
					pp.*,
					ue.Denominacion AS UnidadEjecutora
				FROM
					pr_proypresupuestaria pp
					INNER JOIN pv_categoriaprog cp ON (cp.CategoriaProg = pp.CategoriaProg)
					INNER JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cp.CodUnidadEjec)
				WHERE 1 $filtro
				ORDER BY $fOrderBy
				LIMIT ".intval($limit).", ".intval($maxlimit);
		$field = getRecords($sql);
		$rows_lista = count($field);
		foreach($field as $f) {
			$id = $f['CodProyPresupuesto'];
			?>
			<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
				<td align="center"><?=$f['Ejercicio']?></td>
				<td align="center"><?=$f['CategoriaProg']?></td>
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