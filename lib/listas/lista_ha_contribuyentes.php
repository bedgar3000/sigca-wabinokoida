<?php
if (!$ventana) $ventana = "selLista";
##	
if (!empty($accion_selector)) $accion = $accion_selector;
if (!empty($modulo_selector)) $modulo = $modulo_selector;
if (!empty($_APLICACION))
{
	if ($_APLICACION == 'HA') $concepto = '80-0003';
}
if (!empty($concepto)) list ($_SHOW, $_ADMIN, $_INSERT, $_UPDATE, $_DELETE) = opcionesPermisos('', $concepto);
else {
	$_SHOW = 'N';
	$_ADMIN = 'N';
	$_INSERT = 'N';
	$_UPDATE = 'N';
	$_DELETE = 'N';
}
//	------------------------------------
if ($filtrar == "default") {
	$fEstado = 'AC';
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "CodContribuyente";
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (c.CodContribuyente LIKE '%".$fBuscar."%' OR
					  p.NomCompleto LIKE '%".$fBuscar."%' OR
					  p.DocFiscal LIKE '%".$fBuscar."%' OR
					  md1.Descripcion LIKE '%".$fBuscar."%' OR
					  md2.Descripcion LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (c.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fTipoPersona != "") { $cTipoPersona = "checked"; $filtro.=" AND (p.TipoPersona = '".$fTipoPersona."')"; } else $dTipoPersona = "disabled";
if ($fTipoSociedad != "") { $cTipoSociedad = "checked"; $filtro.=" AND (c.TipoSociedad = '".$fTipoSociedad."')"; } else $dTipoSociedad = "disabled";
if ($fTipoNegocio != "") { $cTipoNegocio = "checked"; $filtro.=" AND (c.TipoNegocio = '".$fTipoNegocio."')"; } else $dTipoNegocio = "disabled";
if ($fSituacionComercial != "") { $cSituacionComercial = "checked"; $filtro.=" AND (c.SituacionComercial = '".$fSituacionComercial."')"; } else $dSituacionComercial = "disabled";
//	------------------------------------
$_width = 900;
?>
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lista_personas" method="post">
<input type="hidden" name="registro" id="registro" />
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
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
<input type="hidden" name="seldetalle" id="seldetalle" value="<?=$seldetalle?>" />
<input type="hidden" name="modulo" id="modulo" value="<?=$modulo?>" />
<input type="hidden" name="modulo_selector" id="modulo_selector" value="<?=$modulo?>" />
<input type="hidden" name="accion" id="accion" value="<?=$accion?>" />
<input type="hidden" name="accion_selector" id="accion_selector" value="<?=$accion?>" />
<input type="hidden" name="url" id="url" value="<?=$url?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="FlagClasePersona" id="FlagClasePersona" value="<?=$FlagClasePersona?>" />

<!--FILTRO-->
<div class="divBorder" style="width:100%; min-width:<?=$_width?>px; margin:auto;">
	<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
		<tr>
			<td align="right" width="125">Buscar:</td>
			<td>
				<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
				<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:215px;" <?=$dBuscar?> />
			</td>
			<td align="right" width="125">Situaci&oacute;n Comercial: </td>
			<td>
	            <input type="checkbox" <?=$cSituacionComercial?> onclick="chkFiltro(this.checked, 'fSituacionComercial');" />
	            <select name="fSituacionComercial" id="fSituacionComercial" style="width:125px;" <?=$dSituacionComercial?>>
	                <option value="">&nbsp;</option>
	                <?=getMiscelaneos($fSituacionComercial, "SITCOM", 0)?>
	            </select>
			</td>
			<td align="right" width="100">Tipo de Persona: </td>
			<td>
	            <input type="checkbox" <?=$cTipoPersona?> onclick="chkFiltro(this.checked, 'fTipoPersona');" />
	            <select name="fTipoPersona" id="fTipoPersona" style="width:125px;" <?=$dTipoPersona?>>
	                <option value="">&nbsp;</option>
	                <?=loadSelectGeneral("TIPO-PERSONA",$fTipoPersona)?>
	            </select>
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td align="right">Tipo de Sociedad: </td>
			<td>
	            <input type="checkbox" <?=$cTipoSociedad?> onclick="chkFiltro(this.checked, 'fTipoSociedad');" />
	            <select name="fTipoSociedad" id="fTipoSociedad" style="width:215px;" <?=$dTipoSociedad?>>
	                <option value="">&nbsp;</option>
	                <?=getMiscelaneos($fTipoSociedad, "TSOCIEDAD", 0)?>
	            </select>
			</td>
			<td align="right">Tipo de Negocio: </td>
			<td>
	            <input type="checkbox" <?=$cTipoNegocio?> onclick="chkFiltro(this.checked, 'fTipoNegocio');" />
	            <select name="fTipoNegocio" id="fTipoNegocio" style="width:125px;" <?=$dTipoNegocio?>>
	                <option value="">&nbsp;</option>
	                <?=getMiscelaneos($fTipoNegocio, "TIPNEGOCIO", 0)?>
	            </select>
			</td>
			<td align="right">Estado: </td>
			<td>
	            <input type="checkbox" <?=$cEstado?> onclick="chkFiltro(this.checked, 'fEstado');" />
	            <select name="fEstado" id="fEstado" style="width:125px;" <?=$dEstado?>>
	                <option value="">&nbsp;</option>
	                <?=loadSelectGeneral("contribuyente-estado",$fEstado)?>
	            </select>
			</td>
	        <td align="right"><input type="submit" value="Buscar"></td>
		</tr>
	</table>
</div>
<div class="sep"></div>

<!--REGISTROS-->
<div class="scroll" style="overflow:scroll; height:265px; width:100%; min-width:<?=$_width?>px;">
	<table class="tblLista" style="width:100%; min-width:800px;">
		<thead>
		    <tr>
		        <th width="75" onclick="order('CodContribuyente')">C&oacute;digo</th>
		        <th align="left" onclick="order('NomCompleto')">Raz&oacute;n Social</th>
		        <th width="100" onclick="order('DocFiscal')">Doc. Fiscal</th>
		        <th align="left" width="100" onclick="order('NomTipoNegocio')">Tipo de Negocio</th>
		        <th align="left" width="150" onclick="order('NomSituacionComercial')">Situaci&oacute;n Comercial</th>
		        <th width="75" onclick="order('Estado')">Estado</th>
		    </tr>
	    </thead>
	    
	    <tbody>
		<?php
		//	consulto todos
		$sql = "SELECT *
				FROM ha_contribuyentes c
				INNER JOIN mastpersonas p ON p.CodPersona = c.CodPersona
				LEFT JOIN mastmiscelaneosdet md1 ON (
					md1.CodDetalle = c.TipoNegocio
					AND md1.CodMaestro = 'TIPNEGOCIO'
				)
				LEFT JOIN mastmiscelaneosdet md2 ON (
					md2.CodDetalle = c.SituacionComercial
					AND md2.CodMaestro = 'SITCOM'
				)
				WHERE 1 $filtro";
		$rows_total = getNumRows3($sql);
		//	consulto lista
		$sql = "SELECT
					c.*,
					p.NomCompleto,
					p.DocFiscal,
					md1.Descripcion AS NomTipoNegocio,
					md2.Descripcion AS NomSituacionComercial
				FROM ha_contribuyentes c
				INNER JOIN mastpersonas p ON p.CodPersona = c.CodPersona
				LEFT JOIN mastmiscelaneosdet md1 ON (
					md1.CodDetalle = c.TipoNegocio
					AND md1.CodMaestro = 'TIPNEGOCIO'
				)
				LEFT JOIN mastmiscelaneosdet md2 ON (
					md2.CodDetalle = c.SituacionComercial
					AND md2.CodMaestro = 'SITCOM'
				)
				WHERE 1 $filtro
				ORDER BY $fOrderBy
				LIMIT ".intval($limit).", ".intval($maxlimit);
		$field = getRecords($sql);
		$rows_lista = count($field);
		foreach($field as $f) {
			$id = $f['CodContribuyente'];
			?>
			<tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['CodPersona']?>','<?=$f['DocFiscal']?>','<?=htmlentities($f['NomCompleto'])?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>']);">
				<td align="center"><?=$f['CodContribuyente']?></td>
				<td><?=htmlentities($f['NomCompleto'])?></td>
				<td align="center"><?=$f['DocFiscal']?></td>
				<td><?=htmlentities($f['NomTipoNegocio'])?></td>
				<td><?=htmlentities($f['NomSituacionComercial'])?></td>
				<td align="center"><?=printValoresGeneral('contribuyente-estado',$f['Estado'])?></td>
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

<?php
if ($ventana == "ha_contribuyentes") 
{
	?>
	<script type="text/javascript">
		function ha_contribuyentes(valores, inputs) {
			loadSelectParent(parent.$('#CodEstado'), 'tabla=mastestados&CodPais='+valores[9]+'&CodEstado='+valores[10], 0, ['CodMunicipio','CodParroquia','CodLocalidad']);
			loadSelectParent(parent.$('#CodMunicipio'), 'tabla=mastmunicipios&CodEstado='+valores[10]+'&CodMunicipio='+valores[11], 0, ['CodParroquia','CodLocalidad']);
			loadSelectParent(parent.$('#CodParroquia'), 'tabla=mastparroquias&CodMunicipio='+valores[11], 0, ['CodLocalidad']);
			if (inputs) {
				for(var i=0; i<inputs.length; i++) {
					if (parent.$("#"+inputs[i]).length > 0) parent.$("#"+inputs[i]).val(valores[i]);
				}
			}
			parent.$.prettyPhoto.close();
		}
	</script>
	<?php
}
?>