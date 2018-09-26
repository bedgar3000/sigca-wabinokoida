<?php
//	------------------------------------
if ($filtrar == "default") {
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
if ($fCodGrupOcup != "") { $cCodGrupOcup = "checked"; $filtro.=" AND (p.CodGrupOcup = '".$fCodGrupOcup."')"; } else $dCodGrupOcup = "disabled";
if ($fCodSerieOcup != "") { $cCodSerieOcup = "checked"; $filtro.=" AND (p.CodSerieOcup = '".$fCodSerieOcup."')"; } else $dCodSerieOcup = "disabled";
if ($fCodTipoCargo != "") { $cCodTipoCargo = "checked"; $filtro.=" AND (p.CodTipoCargo = '".$fCodTipoCargo."')"; } else $dCodTipoCargo = "disabled";
if ($fCodNivelClase != "") { $cCodNivelClase = "checked"; $filtro.=" AND (p.CodNivelClase = '".$fCodNivelClase."')"; } else $dCodNivelClase = "disabled";
if ($fCategoriaCargo != "") { $cCategoriaCargo = "checked"; $filtro.=" AND (p.CategoriaCargo = '".$fCategoriaCargo."')"; } else $dCategoriaCargo = "disabled";
if ($fGrado != "") { $cGrado = "checked"; $filtro.=" AND (p.Grado = '".$fGrado."')"; } else $dGrado = "disabled";
//	------------------------------------
$_titulo = "Maestro de Cargos";
$_width = 800;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo">Maestro de Cargos</td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=cargos_lista" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />

<!--FILTRO-->
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
			<td align="right">Estado: </td>
			<td>
	            <input type="checkbox" <?=$cEstado?> onclick="chkFiltro(this.checked, 'fEstado');" />
	            <select name="fEstado" id="fEstado" style="width:125px;" <?=$dEstado?>>
	                <option value="">&nbsp;</option>
	                <?=loadSelectGeneral("ESTADO", $fEstado, 0)?>
	            </select>
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
			<td align="right">Serie Ocupacional: </td>
			<td>
	            <input type="checkbox" <?=$cCodSerieOcup?> onclick="chkFiltro(this.checked, 'fCodSerieOcup');" />
	            <select name="fCodSerieOcup" id="fCodSerieOcup" style="width:230px;" <?=$dCodSerieOcup?>>
	                <option value="">&nbsp;</option>
	                <?=loadSelect2("rh_serieocupacional", "CodSerieOcup", "SerieOcup", $fCodSerieOcup, 0, array('CodGrupOcup'), array($fCodGrupOcup))?>
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
			<td align="right">Grado:</td>
			<td>
				<input type="checkbox" <?=$cGrado?> onclick="chkCampos(this.checked, 'fGrado');" />
	            <select name="fGrado" id="fGrado" style="width:50px;" <?=$dGrado?>>
	                <option value="">&nbsp;</option>
	                <?=loadSelect2("rh_nivelsalarial", "Grado", "Grado", $fGrado, 0, array('CategoriaCargo'), array($fCategoriaCargo))?>
	            </select>
			</td>
	        <td>&nbsp;</td>
		</tr>
		<tr>
			<td align="right">Buscar:</td>
			<td>
				<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
				<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:230px;" <?=$dBuscar?> />
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
            <input type="button" id="btNuevo" value="Nuevo" style="width:75px; <?=$btNuevo?>" onclick="cargarPagina(this.form, 'gehen.php?anz=cargos_form&opcion=nuevo');" />
            <input type="button" id="btModificar" value="Modificar" style="width:75px; <?=$btModificar?>" onclick="cargarOpcion2(this.form, 'gehen.php?anz=cargos_form&opcion=modificar', 'SELF', '', $('#sel_registros').val());" />
            <input type="button" id="btEliminar" value="Eliminar" style="width:75px; <?=$btEliminar?>" onclick="opcionRegistro3(this.form, $('#sel_registros').val(), 'cargos', 'eliminar', 'cargos_ajax.php');" />
            <input type="button" id="btVer" value="Ver" style="width:75px; <?=$btVer?>" onclick="cargarOpcion2(this.form, 'gehen.php?anz=cargos_form&opcion=ver', 'SELF', '', $('#sel_registros').val());" />
        </td>
    </tr>
</table>

<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px;">
	<table class="tblLista" style="width:100%; min-width:1600px;">
		<thead>
		    <tr>
		        <th width="60" onclick="order('CodDesc')">C&oacute;digo</th>
		        <th align="left" onclick="order('DescripCargo')">Descripci&oacute;n</th>
		        <th align="left" onclick="order('SerieOcup,CodDesc')">Serie</th>
		        <th align="left" onclick="order('GrupoOcup,SerieOcup,CodDesc')">Grupo</th>
		        <th width="125" onclick="order('NomCategoriaCargo,GrupoOcup,SerieOcup,CodDesc')">Categor&iacute;a</th>
		        <th width="30" onclick="order('Grado')">Grado</th>
		        <th width="30" onclick="order('Paso')">Paso</th>
		        <th width="75" onclick="order('Estado')">Estado</th>
		    </tr>
	    </thead>
    
	    <tbody id="lista_registros">
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
					p.Grado,
					p.Paso,
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
			##
			?>
			<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
				<td align="right"><?=$f['CodDesc']?></td>
				<td><?=htmlentities($f['DescripCargo'])?></td>
				<td><?=htmlentities($f['SerieOcup'])?></td>
				<td><?=htmlentities($f['GrupoOcup'])?></td>
				<td align="center"><?=htmlentities($f['NomCategoriaCargo'])?></td>
				<td align="center"><?=$f['Grado']?></td>
				<td align="center"><?=$f['Paso']?></td>
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
</form>