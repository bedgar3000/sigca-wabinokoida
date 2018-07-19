<?php
//	------------------------------------
if ($filtrar == "default") {
	$fEstado = 'A';
	$fDigitos = 2;
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "CodInterno";
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (s.CodInterno LIKE '%".$fBuscar."%'
					  OR s.Descripcion LIKE '%".$fBuscar."%'
					  OR md.Descripcion LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (s.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fDigitos != "") { $cDigitos = "checked"; $filtro.=" AND (s.Digitos = '".$fDigitos."')"; } else $dDigitos = "disabled";
//	------------------------------------
$_titulo = "Servicios";
$_width = 700;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=co_servicios_lista" method="post" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />

	<!--FILTRO-->
	<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
		<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
			<tr>
				<td align="right" width="100">Buscar:</td>
				<td>
					<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
					<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:169px;" <?=$dBuscar?> />
				</td>
				<td align="right">Digitos: </td>
				<td>
		            <input type="checkbox" <?=$cDigitos?> onclick="chkFiltro(this.checked, 'fDigitos');" />
		            <select name="fDigitos" id="fDigitos" style="width:100px;" <?=$dDigitos?>>
		                <option value="">&nbsp;</option>
		                <?=loadSelectValores("servicios-digitos",$fDigitos)?>
		            </select>
				</td>
				<td align="right">Estado: </td>
				<td>
		            <input type="checkbox" <?=$cEstado?> onclick="chkFiltro(this.checked, 'fEstado');" />
		            <select name="fEstado" id="fEstado" style="width:100px;" <?=$dEstado?>>
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
	            <input type="button" value="Nuevo" style="width:75px;" class="insert" onclick="cargarPagina(this.form, 'gehen.php?anz=co_servicios_form&opcion=nuevo');" />
	            <input type="button" value="Modificar" style="width:75px;" class="update" onclick="cargarOpcion2(this.form, 'gehen.php?anz=co_servicios_form&opcion=modificar', 'SELF', '', $('#sel_registros').val());" />
	            <input type="button" value="Eliminar" style="width:75px;" class="delete" onclick="opcionRegistro3(this.form, $('#sel_registros').val(), 'formulario', 'eliminar', 'co_servicios_ajax.php');" />
	            <input type="button" value="Ver" style="width:75px;" class="ver" onclick="cargarOpcion2(this.form, 'gehen.php?anz=co_servicios_form&opcion=ver', 'SELF', '', $('#sel_registros').val());" />
	        </td>
	    </tr>
	</table>

	<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px;">
		<table class="tblLista" style="width:100%; min-width:100%;">
			<thead>
			    <tr>
			        <th width="125" onclick="order('CodInterno')">C&oacute;digo</th>
			        <th align="left" onclick="order('Descripcion')">Descripci&oacute;n</th>
			        <th width="125" onclick="order('NomClasificacion')">Clasificación</th>
			        <th width="45" onclick="order('FlagExoneradoIva')">Exon.</th>
			        <th width="45" onclick="order('FlagAfectoDescuento')">Desc.</th>
			        <th width="125" align="right" onclick="order('PrecioVenta')">Precio Venta</th>
			        <th width="75" onclick="order('Estado')">Estado</th>
			    </tr>
		    </thead>
		    
		    <tbody id="lista_registros">
			<?php
			//	consulto todos
			$sql = "SELECT *
					FROM co_mastservicios S
					LEFT JOIN mastmiscelaneosdet md ON (
						md.CodDetalle = s.CodClasificacion
						AND md.CodMaestro = 'CLASIFSERV'
					)
					WHERE 1 $filtro";
			$rows_total = getNumRows3($sql);
			//	consulto lista
			$sql = "SELECT
						s.*,
						md.Descripcion AS NomClasificacion
					FROM co_mastservicios S
					LEFT JOIN mastmiscelaneosdet md ON (
						md.CodDetalle = s.CodClasificacion
						AND md.CodMaestro = 'CLASIFSERV'
					)
					WHERE 1 $filtro
					ORDER BY $fOrderBy
					LIMIT ".intval($limit).", ".intval($maxlimit);
			$field = getRecords($sql);
			$rows_lista = count($field);
			foreach($field as $f) {
				$id = $f['CodServicio'];
				?>
				<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
					<td><?=$f['CodInterno']?></td>
					<td><?=htmlentities($f['Descripcion'])?></td>
					<td align="center"><?=htmlentities($f['NomClasificacion'])?></td>
					<td align="center"><?=printFlag2($f['FlagExoneradoIva'])?></td>
					<td align="center"><?=printFlag2($f['FlagAfectoDescuento'])?></td>
					<td align="right"><?=number_format($f['PrecioVenta'],2,',','.')?></td>
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