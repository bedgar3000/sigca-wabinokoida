<?php
$sql = "SELECT
			i.*,
			ti.Descripcion AS TipoItem,
			cl.Descripcion AS Linea,
			cf.Descripcion AS Familia,
			csf.Descripcion AS SubFamilia
		FROM lg_itemmast i
		INNER JOIN lg_tipoitem ti On ti.CodTipoItem = i.CodTipoItem
		INNER JOIN lg_claselinea cl On cl.CodLinea = i.CodLinea
		INNER JOIN lg_clasefamilia cf ON cf.CodFamilia = i.CodFamilia
		INNER JOIN lg_clasesubfamilia csf ON csf.CodSubFamilia = i.CodSubFamilia
		WHERE i.CodItem = '$CodItem'";
$field = getRecord($sql);
//	------------------------------------
$_width = 800;
?>
<form method="POST" enctype="multipart/form-data" autocomplete="off">
	<table style="width:100%; min-width:<?=$_width?>px;" class="tblForm">
		<tr>
			<td class="tagForm">Item:</td>
			<td>
				<input type="text" name="CodItem" id="CodItem" value="<?=$field['CodItem']?>" style="width:75px;" readonly />
			</td>
			<td class="tagForm">Cod. Interno:</td>
			<td>
				<input type="text" name="CodInterno" id="CodInterno" value="<?=$field['CodInterno']?>" style="width:50px;" readonly />
			</td>
			<td class="tagForm">Unidad:</td>
			<td width="5">
				<input type="text" name="CodUnidad" id="CodUnidad" value="<?=$field['CodUnidad']?>" style="width:50px;" readonly />
			</td>
			<td class="tagForm">Tipo Item:</td>
			<td>
				<input type="text" name="TipoItem" id="TipoItem" value="<?=$field['TipoItem']?>" style="width:150px;" readonly />
			</td>
		</tr>
		<tr>
			<td rowspan="3" class="tagForm">Descripción:</td>
			<td colspan="5" rowspan="3">
				<textarea name="Descripcion" id="Descripcion" style="width:100%; height: 63px;" readonly><?=htmlentities($field['Descripcion'])?></textarea>
			</td>
			<td class="tagForm">Linea:</td>
			<td>
				<input type="text" name="Linea" id="Linea" value="<?=$field['Linea']?>" style="width:150px;" readonly />
			</td>
		</tr>
		<tr>
			<td class="tagForm">Familia:</td>
			<td>
				<input type="text" name="Familia" id="Familia" value="<?=$field['Familia']?>" style="width:150px;" readonly />
			</td>
		</tr>
		<tr>
			<td class="tagForm">Sub-Familia:</td>
			<td>
				<input type="text" name="SubFamilia" id="SubFamilia" value="<?=$field['SubFamilia']?>" style="width:150px;" readonly />
			</td>
		</tr>
	</table>
	<br>

	<div style="overflow:scroll; height:200px; width:100%; min-width:<?=$_width?>px; margin:auto;">
		<table class="tblLista" style="width:100%;">
			<thead>
				<tr>
					<th style="min-width: 250px; text-align: left;">Organismo</th>
					<th style="width: 75px;">Almacén</th>
					<th style="min-width: 250px; text-align: left;">Descripción</th>
					<th style="width: 35px;">Uni.</th>
					<th style="width: 80px;">Stock Actual</th>
					<th style="width: 35px;">Uni. (Equi.)</th>
					<th style="width: 80px;">Stock Actual (Equi.)</th>
				</tr>
			</thead>
			
			<tbody>
				<?php
				$sql = "SELECT
							iai.*,
							i.CodUnidad,
							i.CodUnidadComp,
							i.Descripcion,
							o.Organismo,
							(iai.StockActual / vw.CantidadEqui) AS StockActualEqui
						FROM lg_itemalmaceninv iai
						INNER JOIN lg_itemmast i ON i.CodItem = iai.CodItem
						INNER JOIN lg_almacenmast a ON a.CodAlmacen = iai.CodAlmacen
						INNER JOIN mastorganismos o ON o.CodOrganismo = a.CodOrganismo
						INNER JOIN vw_lg_inventarioactual_item vw ON vw.CodItem = iai.CodItem
						WHERE iai.CodItem = '$field[CodItem]'";
				$field_detalle = getRecords($sql);
				foreach ($field_detalle as $f)
				{
					?>
					<tr class="trListaBody">
						<td><?=$f['Organismo']?></td>
						<td align="center"><?=$f['CodAlmacen']?></td>
						<td><?=$f['Descripcion']?></td>
						<td align="center"><?=$f['CodUnidad']?></td>
						<td align="right"><?=number_format($f['StockActual'],5,',','.')?></td>
						<td align="center"><?=$f['CodUnidadComp']?></td>
						<td align="right"><?=number_format($f['StockActualEqui'],5,',','.')?></td>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
	</div>
</form>