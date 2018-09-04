<?php
if (!$ventana) $ventana = "selLista";
//	------------------------------------
if ($filtrar == "default") {
	$fCodGrupOcup = $_SESSION["fCodGrupOcup"];
	$fCodSerieOcup = $_SESSION["fCodSerieOcup"];
	$fEstado = "A";
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "CodDesc";
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (p.CodDesc LIKE '%".$fBuscar."%' OR
					  p.DescripCargo LIKE '%".$fBuscar."%' OR
					  md.Descripcion LIKE '%".$fBuscar."%' OR
					  go.GrupoOcup LIKE '%".$fBuscar."%' OR
					  so.SerieOcup LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (p.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fCodGrupOcup != "") { $cCodGrupOcup = "checked"; $filtro.=" AND (p.CodGrupOcup = '".$fCodGrupOcup."')"; $_SESSION["fCodGrupOcup"] = $fCodGrupOcup; } else $dCodGrupOcup = "disabled";
if ($fCodSerieOcup != "") { $cCodSerieOcup = "checked"; $filtro.=" AND (p.CodSerieOcup = '".$fCodSerieOcup."')"; $_SESSION["fCodSerieOcup"] = $fCodSerieOcup; } else $dCodSerieOcup = "disabled";
if ($fCodTipoCargo != "") { $cCodTipoCargo = "checked"; $filtro.=" AND (p.CodTipoCargo = '".$fCodTipoCargo."')"; } else $dCodTipoCargo = "disabled";
if ($fCodNivelClase != "") { $cCodNivelClase = "checked"; $filtro.=" AND (p.CodNivelClase = '".$fCodNivelClase."')"; } else $dCodNivelClase = "disabled";
if ($fCategoriaCargo != "") { $cCategoriaCargo = "checked"; $filtro.=" AND (p.CategoriaCargo = '".$fCategoriaCargo."')"; } else $dCategoriaCargo = "disabled";
if ($fGrado != "") { $cGrado = "checked"; $filtro.=" AND (p.Grado = '".$fGrado."')"; } else $dGrado = "disabled";
//	------------------------------------
?>
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lista_cargos" method="post">
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
<input type="hidden" name="CodOrganismo" id="CodOrganismo" value="<?=$CodOrganismo?>" />

<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
	<tr>
		<td align="right">Grupo Ocupacional:</td>
		<td>
			<input type="checkbox" <?=$cCodGrupOcup?> onclick="chkCampos(this.checked, 'fCodGrupOcup');" />
            <select name="fCodGrupOcup" id="fCodGrupOcup" style="width:230px;" <?=$dCodGrupOcup?> onchange="loadSelect($('#fCodSerieOcup'), 'tabla=rh_serieocupacional&CodGrupOcup='+$(this).val(), 1);">
                <option value="">&nbsp;</option>
                <?=loadSelect2("rh_grupoocupacional", "CodGrupOcup", "GrupoOcup", $fCodGrupOcup, 0)?>
            </select>
		</td>
		<td align="right">Tipo de Cargo: </td>
		<td>
            <input type="checkbox" <?=$cCodTipoCargo?> onclick="chkFiltro(this.checked, 'fCodTipoCargo');" />
            <select name="fCodTipoCargo" id="fCodTipoCargo" style="width:125px;" <?=$dCodTipoCargo?> onchange="loadSelect($('#fCodNivelClase'), 'tabla=rh_nivelclasecargo&CodTipoCargo='+$(this).val(), 1);">
                <option value="">&nbsp;</option>
                <?=loadSelect2("rh_tipocargo", "CodTipoCargo", "TipCargo", $fCodTipoCargo, 0)?>
            </select>
        </td>
        <td>&nbsp;</td>
	</tr>
	<tr>
		<td align="right">Serie Ocupacional: </td>
		<td>
            <input type="checkbox" <?=$cCodSerieOcup?> onclick="chkFiltro(this.checked, 'fCodSerieOcup');" />
            <select name="fCodSerieOcup" id="fCodSerieOcup" style="width:230px;" <?=$dCodSerieOcup?>>
                <option value="">&nbsp;</option>
                <?=loadSelect2("rh_serieocupacional", "CodSerieOcup", "SerieOcup", $fCodSerieOcup, 0, array('CodGrupOcup'), array($fCodGrupOcup))?>
            </select>
        </td>
		<td align="right">Nivel: </td>
		<td>
            <input type="checkbox" <?=$cCodNivelClase?> onclick="chkFiltro(this.checked, 'fCodNivelClase');" />
            <select name="fCodNivelClase" id="fCodNivelClase" style="width:125px;" <?=$dCodNivelClase?>>
                <option value="">&nbsp;</option>
                <?=loadSelect2("rh_nivelclasecargo", "CodNivelClase", "NivelClase", $fCodNivelClase, 0, array('CodTipoCargo'), array($fCodTipoCargo))?>
            </select>
        </td>
        <td>&nbsp;</td>
	</tr>
	<tr>
		<td align="right">Buscar:</td>
		<td>
			<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
			<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:225px;" <?=$dBuscar?> />
		</td>
		<td align="right">Categor&iacute;a:</td>
		<td>
			<input type="checkbox" <?=$cCategoriaCargo?> onclick="chkCampos(this.checked, 'fCategoriaCargo');" />
            <select name="fCategoriaCargo" id="fCategoriaCargo" style="width:125px;" <?=$dCategoriaCargo?> onchange="loadSelect($('#fGrado'), 'tabla=rh_nivelsalarial&CategoriaCargo='+$(this).val(), 1);">
                <option value="">&nbsp;</option>
                <?=getMiscelaneos($fCategoriaCargo, "CATCARGO", 0)?>
            </select>
		</td>
        <td>&nbsp;</td>
	</tr>
	<tr>
		<td align="right">Estado: </td>
		<td>
            <input type="checkbox" <?=$cEstado?> onclick="chkFiltro(this.checked, 'fEstado');" />
            <select name="fEstado" id="fEstado" style="width:100px;" <?=$dEstado?>>
                <option value="">&nbsp;</option>
                <?=loadSelectGeneral("ESTADO", $fEstado, 0)?>
            </select>
        </td>
		<td align="right">Grado:</td>
		<td>
			<input type="checkbox" <?=$cGrado?> onclick="chkCampos(this.checked, 'fGrado');" />
            <select name="fGrado" id="fGrado" style="width:50px;" <?=$dGrado?>>
                <option value="">&nbsp;</option>
                <?=loadSelect2("rh_nivelsalarial", "Grado", "Grado", $fGrado, 0, array('CategoriaCargo'), array($fCategoriaCargo))?>
            </select>
		</td>
        <td align="right"><input type="submit" value="Buscar"></td>
	</tr>
</table>
</div>
<div class="sep"></div>

<center>
<div class="scroll" style="overflow:scroll; height:230px; width:100%; min-width:<?=$_width?>px;">
<table class="tblLista" style="width:100%; min-width:1700px;">
	<thead>
	<tr>
        <th width="60" onclick="order('CodDesc')">C&oacute;digo</th>
        <th align="left" onclick="order('DescripCargo')">Descripci&oacute;n</th>
        <th align="left" onclick="order('SerieOcup,CodDesc')">Serie</th>
        <th align="left" onclick="order('GrupoOcup,SerieOcup,CodDesc')">Grupo</th>
        <th width="100" onclick="order('NomCategoriaCargo,GrupoOcup,SerieOcup,CodDesc')">Categor&iacute;a</th>
        <th width="75" onclick="order('Estado')">Estado</th>
    </tr>
    </thead>
    
    <tbody>
	<?php
	//	consulto todos
	$sql = "SELECT p.CodCargo
			FROM
				rh_puestos p
				INNER JOIN rh_grupoocupacional go ON (go.CodGrupOcup = p.CodGrupOcup)
				INNER JOIN rh_serieocupacional so ON (so.CodSerieOcup = p.CodSerieOcup)
				LEFT JOIN mastmiscelaneosdet md ON (md.CodDetalle = p.CategoriaCargo AND
													md.CodMaestro = 'CATCARGO' AND
													md.CodAplicacion = 'RH')
			WHERE 1 $filtro";
	$rows_total = getNumRows3($sql);
	
	//	consulto lista
	$sql = "SELECT
				p.CodCargo,
				p.CodDesc,
				p.DescripCargo,
				p.CodGrupOcup,
				p.CodSerieOcup,
				p.CategoriaCargo,
				p.Estado,
				go.GrupoOcup,
				so.SerieOcup,
				md.Descripcion AS NomCategoriaCargo
			FROM
				rh_puestos p
				INNER JOIN rh_grupoocupacional go ON (go.CodGrupOcup = p.CodGrupOcup)
				INNER JOIN rh_serieocupacional so ON (so.CodSerieOcup = p.CodSerieOcup)
				LEFT JOIN mastmiscelaneosdet md ON (md.CodDetalle = p.CategoriaCargo AND
													md.CodMaestro = 'CATCARGO' AND
													md.CodAplicacion = 'RH')
			WHERE 1 $filtro
			ORDER BY $fOrderBy
			LIMIT ".intval($limit).", ".intval($maxlimit);
	$field = getRecords($sql);
	$rows_lista = count($field);
	foreach($field as $f) {
		$id = "$f[CodCargo]";
		if ($ventana == "listado_insertar_linea") {
			?><tr class="trListaBody" onclick="<?=$ventana?>('<?=$detalle?>', 'modulo=<?=$modulo?>&accion=<?=$accion?>&CodCargo=<?=$f['CodCargo']?>', '<?=$f['CodCargo']?>', '<?=$url?>');"><?php
		}
		elseif ($ventana == "pr_proyrecursos") {
			?><tr class="trListaBody" onclick="listado_insertar_linea('<?=$detalle?>', 'modulo=<?=$modulo?>&accion=<?=$accion?>&CodCargo=<?=$f['CodCargo']?>&CodOrganismo=<?=$CodOrganismo?>', '', '<?=$url?>');"><?php
		}
		elseif ($ventana == "pr_proyrecursos_sel") {
			?><tr class="trListaBody" onClick="pr_proyrecursos_sel(['<?=$f['CodCargo']?>','<?=$f['DescripCargo']?>'], ['<?=$campo1?>','<?=$campo2?>']);"><?php
		}
		else {
			?><tr class="trListaBody" onClick="selLista(['<?=$f['CodCargo']?>','<?=$f['DescripCargo']?>'], ['<?=$campo1?>','<?=$campo2?>']);"><?php
		}
		?>
			<td align="right"><?=$f['CodCargo']?></td>
			<td><?=htmlentities($f['DescripCargo'])?></td>
			<td><?=htmlentities($f['SerieOcup'])?></td>
			<td><?=htmlentities($f['GrupoOcup'])?></td>
			<td align="center"><?=htmlentities($f['NomCategoriaCargo'])?></td>
			<td align="center"><?=printValoresGeneral("ESTADO", $f['Estado'])?></td>
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

<script type="text/javascript">
	<?php
	if ($ventana == "pr_proyrecursos_sel") {
		?>
		function pr_proyrecursos_sel(valores, inputs) {
			if (inputs) {
				for(var i=0; i<inputs.length; i++) {
					if (parent.$("#"+inputs[i]).length > 0) parent.$("#"+inputs[i]).val(valores[i]);
				}
			}

			$.ajax({
				type: "POST",
				url: "../../nomina/pr_proyrecursos_ajax.php",
				data: "modulo=ajax&accion=cargo_seleccionar&CodCargo="+valores[0],
				async: false,
				dataType: "json",
				success: function(data) {
					var id = inputs[0].substr(17);

					parent.$('#empleado_CategoriaCargo'+id).val(data['CategoriaCargo']);

					parent.$('#empleado_Grado'+id).val(data['Grado']);

					parent.$('#empleado_Paso'+id).html(data['Pasos']);
					parent.$('#empleado_Paso'+id).val(data['Paso']);
				}
			});

			parent.$.prettyPhoto.close();
		}
		<?php
	}
	?>
</script>