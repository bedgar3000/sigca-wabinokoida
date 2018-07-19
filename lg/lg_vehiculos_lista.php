<?php
//	------------------------------------
if ($filtrar == "default") {
	$fEstado = 'A';
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "CodVehiculo";
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (v.CodVehiculo LIKE '%$fBuscar%'
					  OR v.CodPersona LIKE '%$fBuscar%'
					  OR p1.NomCompleto LIKE '%$fBuscar%'
					  OR p1.Ndocumento LIKE '%$fBuscar%'
					  OR md1.Descripcion LIKE '%$fBuscar%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (v.Estado = '$fEstado')"; } else $dEstado = "disabled";
//	------------------------------------
$_titulo = "Vehiculos";
$_width = 700;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lg_vehiculos_lista" method="post" autocomplete="off">
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
	            <input type="button" value="Nuevo" style="width:75px;" class="insert" onclick="cargarPagina(this.form, 'gehen.php?anz=lg_vehiculos_form&opcion=nuevo');" />
	            <input type="button" value="Modificar" style="width:75px;" class="update" onclick="cargarOpcion2(this.form, 'gehen.php?anz=lg_vehiculos_form&opcion=modificar', 'SELF', '', $('#sel_registros').val());" />
	            <input type="button" value="Eliminar" style="width:75px;" class="delete" onclick="opcionRegistro3(this.form, $('#sel_registros').val(), 'formulario', 'eliminar', 'lg_vehiculos_ajax.php');" />
	            <input type="button" value="Ver" style="width:75px;" class="ver" onclick="cargarOpcion2(this.form, 'gehen.php?anz=lg_vehiculos_form&opcion=ver', 'SELF', '', $('#sel_registros').val());" />
	        </td>
	    </tr>
	</table>

	<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px;">
		<table class="tblLista" style="width:100%; min-width:<?=$_width?>px;">
			<thead>
			    <tr>
			        <th width="100" align="left" onclick="order('Placa')">Placa</th>
			        <th style="min-width: 200px;" align="left" onclick="order('Empresa')">Empresa</th>
			        <th width="100" onclick="order('Placa')">Doc. Fiscal</th>
			        <th style="min-width: 200px;" align="left" onclick="order('Chofer')">Chofer</th>
			        <th width="200" align="left" onclick="order('NomMarca')">Marca</th>
			        <th width="75" onclick="order('Estado')">Estado</th>
			    </tr>
		    </thead>
		    
		    <tbody id="lista_registros">
			<?php
			//	consulto todos
			$sql = "SELECT *
					FROM lg_vehiculos v
					LEFT JOIN mastpersonas p1 ON p1.CodPersona = v.CodEmpresa
					LEFT JOIN mastpersonas p2 ON p2.CodPersona = v.CodChofer
					LEFT JOIN mastmiscelaneosdet md1 ON (
						md1.CodDetalle = v.Marca
						AND md1.CodMaestro = 'MARCAUTO'
					)
					WHERE 1 $filtro";
			$rows_total = getNumRows3($sql);
			//	consulto lista
			$sql = "SELECT
						v.*,
						p1.NomCompleto AS Empresa,
						p1.DocFiscal,
						p2.NomCompleto AS Chofer,
						md1.Descripcion AS NomMarca
					FROM lg_vehiculos v
					LEFT JOIN mastpersonas p1 ON p1.CodPersona = v.CodEmpresa
					LEFT JOIN mastpersonas p2 ON p2.CodPersona = v.CodChofer
					LEFT JOIN mastmiscelaneosdet md1 ON (
						md1.CodDetalle = v.Marca
						AND md1.CodMaestro = 'MARCAUTO'
					)
					WHERE 1 $filtro
					ORDER BY $fOrderBy
					LIMIT ".intval($limit).", ".intval($maxlimit);
			$field = getRecords($sql);
			$rows_lista = count($field);
			foreach($field as $f) {
				$id = $f['CodVehiculo'];
				?>
				<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
					<td align="center"><?=$f['Placa']?></td>
					<td><?=htmlentities($f['Empresa'])?></td>
					<td><?=$f['DocFiscal']?></td>
					<td><?=htmlentities($f['Chofer'])?></td>
					<td><?=htmlentities($f['NomMarca'])?></td>
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