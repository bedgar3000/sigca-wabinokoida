<?php
//	------------------------------------
if ($filtrar == "default") {
	$fEstado = 'A';
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "CodLinea,CodFamilia,CodSubFamilia";
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (cs.CodSubFamilia LIKE '%$fBuscar%'
					  OR cs.Descripcion LIKE '%$fBuscar%'
					  OR cf.CodFamilia LIKE '%$fBuscar%'
					  OR cf.Descripcion LIKE '%$fBuscar%'
					  OR cl.CodLinea LIKE '%$fBuscar%'
					  OR cl.Descripcion LIKE '%$fBuscar%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (cs.Estado = '$fEstado')"; } else $dEstado = "disabled";
if ($fCodFamilia != "") { $cCodFamilia = "checked"; $filtro.=" AND (cs.CodFamilia = '$fCodFamilia')"; } else $dCodFamilia = "disabled";
if ($fCodLinea != "") { $cCodLinea = "checked"; $filtro.=" AND (cs.CodLinea = '$fCodLinea')"; } else $dCodLinea = "disabled";
//	------------------------------------
$_titulo = "Sub-Familias";
$_width = 700;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lg_clasesubfamilia_lista" method="post" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />

	<!--FILTRO-->
	<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
		<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
			<tr>
				<td align="right">Linea:</td>
				<td>
					<input type="checkbox" <?=$cCodLinea?> onclick="chkFiltro(this.checked, 'fCodLinea');" />
					<select name="fCodLinea" id="fCodLinea" style="width:300px;" <?=$dCodLinea?> onChange="loadSelect($('#fCodFamilia'), 'tabla=lg_clasefamilia&CodLinea='+$('#fCodLinea').val(), 1, ['fCodFamilia']);">
						<option value="">&nbsp;</option>
						<?=loadSelect2('lg_claselinea','CodLinea','Descripcion',$fCodLinea,10)?>
					</select>
				</td>
				<td align="right">Buscar:</td>
				<td>
					<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
					<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:150px;" <?=$dBuscar?> />
				</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td align="right">Familia:</td>
				<td>
					<input type="checkbox" <?=$cCodFamilia?> onclick="chkFiltro(this.checked, 'fCodFamilia');" />
					<select name="fCodFamilia" id="fCodFamilia" style="width:300px;" <?=$dCodFamilia?>>
						<option value="">&nbsp;</option>
						<?=loadSelect2('lg_clasefamilia','CodFamilia','Descripcion',$fCodFamilia,10,['CodLinea'],[$fCodLinea])?>
					</select>
				</td>
				<td align="right">Estado: </td>
				<td>
		            <input type="checkbox" <?=$cEstado?> onclick="chkFiltro(this.checked, 'fEstado');" />
		            <select name="fEstado" id="fEstado" style="width:150px;" <?=$dEstado?>>
		                <option value="">&nbsp;</option>
		                <?=loadSelectGeneral("ESTADO", $fEstado, 0)?>
		            </select>
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
	            <input type="button" value="Nuevo" style="width:75px;" class="insert F1" onclick="cargarPagina(this.form, 'gehen.php?anz=lg_clasesubfamilia_form&opcion=nuevo');" />
	            <input type="button" value="Modificar" style="width:75px;" class="update" onclick="cargarOpcion2(this.form, 'gehen.php?anz=lg_clasesubfamilia_form&opcion=modificar', 'SELF', '', $('#sel_registros').val());" />
	            <input type="button" value="Eliminar" style="width:75px;" class="delete" onclick="opcionRegistro3(this.form, $('#sel_registros').val(), 'formulario', 'eliminar', 'lg_clasesubfamilia_ajax.php');" />
	            <input type="button" value="Ver" style="width:75px;" class="ver" onclick="cargarOpcion2(this.form, 'gehen.php?anz=lg_clasesubfamilia_form&opcion=ver', 'SELF', '', $('#sel_registros').val());" />
	        </td>
	    </tr>
	</table>

	<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px;">
		<table class="tblLista" style="width:100%; min-width:<?=$_width?>px;">
			<thead>
			    <tr>
			        <th width="60" onclick="order('CodSubFamilia')">Sub-Familia</th>
			        <th style="min-width: 200px;" align="left" onclick="order('Descripcion')">Nombre de la Sub-Familia</th>
			        <th width="60" onclick="order('CodFamilia,CodSubFamilia')">Familia</th>
			        <th style="min-width: 200px;" align="left" onclick="order('CodFamilia,CodSubFamilia')">Nombre de la Familia</th>
			        <th width="60" onclick="order('CodLinea,CodFamilia,CodSubFamilia')">Linea</th>
			        <th style="min-width: 200px;" align="left" onclick="order('Linea,CodFamilia,CodSubFamilia')">Nombre de la Linea</th>
			        <th width="75" onclick="order('Estado')">Estado</th>
			    </tr>
		    </thead>
		    
		    <tbody id="lista_registros">
			<?php
			//	consulto todos
			$sql = "SELECT *
					FROM lg_clasesubfamilia cs
					INNER JOIN lg_clasefamilia cf ON (
						cf.CodFamilia = cs.CodFamilia
						AND cf.CodLinea = cs.CodLinea
					)
					INNER JOIN lg_claselinea cl ON cl.CodLinea = cf.CodLinea
					WHERE 1 $filtro";
			$rows_total = getNumRows3($sql);
			//	consulto lista
			$sql = "SELECT
						cs.*,
						cf.Descripcion AS Familia,
						cl.Descripcion AS Linea
					FROM lg_clasesubfamilia cs
					INNER JOIN lg_clasefamilia cf ON (
						cf.CodFamilia = cs.CodFamilia
						AND cf.CodLinea = cs.CodLinea
					)
					INNER JOIN lg_claselinea cl ON cl.CodLinea = cf.CodLinea
					WHERE 1 $filtro
					ORDER BY $fOrderBy
					LIMIT ".intval($limit).", ".intval($maxlimit);
			$field = getRecords($sql);
			$rows_lista = count($field);
			foreach($field as $f) {
				$id = $f['CodLinea'] . '_' . $f['CodFamilia'] . '_' . $f['CodSubFamilia'];
				?>
				<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
					<td align="center"><?=$f['CodSubFamilia']?></td>
					<td><?=htmlentities($f['Descripcion'])?></td>
					<td align="center"><?=$f['CodFamilia']?></td>
					<td><?=htmlentities($f['Familia'])?></td>
					<td align="center"><?=$f['CodLinea']?></td>
					<td><?=htmlentities($f['Linea'])?></td>
					<td align="center"><?=printValoresGeneral('ESTADO',$f['Estado'])?></td>
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
</form>