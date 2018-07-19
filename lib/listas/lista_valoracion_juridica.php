<?php
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$fEstado = "AP";
	$fCodDependencia = $_SESSION["DEPENDENCIA_ACTUAL"];
	$fAnio = $AnioActual;
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "Anio,CodDependencia,Secuencia";
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (af.CodValJur LIKE '%".$fBuscar."%' OR
					  af.ObjetivoGeneral LIKE '%".$fBuscar."%' OR
					  oe.Organismo LIKE '%".$fBuscar."%' OR
					  de.Dependencia LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (af.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (af.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fCodDependencia != "") { $cCodDependencia = "checked"; $filtro.=" AND (af.CodDependencia = '".$fCodDependencia."')"; } else $dCodDependencia = "disabled";
if ($fCodCentroCosto != "") { $cCodCentroCosto = "checked"; $filtro.=" AND (af.CodCentroCosto = '".$fCodCentroCosto."')"; } else $dCodCentroCosto = "disabled";
if ($fFechaInicioD != "" || $fFechaInicioH != "") {
	$cFechaInicio = "checked";
	if ($fFechaInicioD != "") $filtro.=" AND (af.FechaInicio >= '".formatFechaAMD($fFechaInicioD)."')";
	if ($fFechaInicioH != "") $filtro.=" AND (af.FechaInicio <= '".formatFechaAMD($fFechaInicioH)."')";
} else $dFechaInicio = "disabled";
if ($fFechaTerminoD != "" || $fFechaTerminoH != "") {
	$cFechaTermino = "checked";
	if ($fFechaTerminoD != "") $filtro.=" AND (af.FechaTermino >= '".formatFechaAMD($fFechaTerminoD)."')";
	if ($fFechaTerminoH != "") $filtro.=" AND (af.FechaTermino <= '".formatFechaAMD($fFechaTerminoH)."')";
} else $dFechaTermino = "disabled";
if ($fAnio != "") { $cAnio = "checked"; $filtro.=" AND (af.Anio = '".$fAnio."')"; } else $dAnio = "disabled";
if ($fCodOrganismoExterno != "") { $cfCodOrganismoExterno = "checked"; $filtro.=" AND (af.CodOrganismoExterno = '".$fCodOrganismoExterno."')"; } else $dfCodOrganismoExterno = "visibility:hidden;";
if ($fCodDependenciaExterna != "") { $filtro.=" AND (af.CodDependenciaExterna = '".$fCodDependenciaExterna."')"; }
//	------------------------------------
$_width = 950;
?>
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lista_valoracion_juridica" method="post" autocomplete="off">
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="campo1" id="campo1" value="<?=$campo1?>" />
<input type="hidden" name="campo2" id="campo2" value="<?=$campo2?>" />
<input type="hidden" name="campo3" id="campo3" value="<?=$campo3?>" />
<input type="hidden" name="campo4" id="campo4" value="<?=$campo4?>" />
<input type="hidden" name="campo5" id="campo5" value="<?=$campo5?>" />
<input type="hidden" name="campo6" id="campo6" value="<?=$campo6?>" />
<input type="hidden" name="campo7" id="campo7" value="<?=$campo7?>" />
<input type="hidden" name="ventana" id="ventana" value="<?=$ventana?>" />

<!--FILTRO-->
<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
	<tr>
		<td align="right" width="100">Organismo:</td>
		<td>
			<input type="checkbox" <?=$cCodOrganismo?> onclick="chkFiltro(this.checked, 'fCodOrganismo');" />
			<select name="fCodOrganismo" id="fCodOrganismo" style="width:300px;" <?=$dCodOrganismo?> onchange="loadSelect($('#fCodDependencia'), 'tabla=dependencia_fiscal&opcion='+$(this).val(), 1, ['fCodCentroCosto']);">
                <option value="">&nbsp;</option>
				<?=getOrganismos($fCodOrganismo, 3)?>
			</select>
		</td>
		<td align="right" width="100">Buscar:</td>
		<td>
			<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
			<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:295px;" <?=$dBuscar?> />
		</td>
        <td>&nbsp;</td>
	</tr>
	<tr>
		<td align="right">Dependencia:</td>
		<td>
			<input type="checkbox" <?=$cCodDependencia?> onclick="chkFiltro(this.checked, 'fCodDependencia');" />
			<select name="fCodDependencia" id="fCodDependencia" style="width:300px;" <?=$dCodDependencia?> onchange="loadSelect($('#fCodCentroCosto'), 'tabla=centro_costo&opcion='+$(this).val(), 1);">
            	<option value="">&nbsp;</option>
				<?=loadDependenciaFiscal($fCodDependencia, $fCodOrganismo, 0)?>
			</select>
		</td>
		<td align="right">Estado: </td>
		<td>
			<?php
			if ($ventana == 'prorrogas') {
				?>
	        	<input type="checkbox" <?=$cEstado?> onclick="this.checked=!this.checked;" />
	            <select name="fEstado" id="fEstado" style="width:143px;" <?=$dEstado?>>
	                <?=loadSelectGeneral("ESTADO-VALORACION", $fEstado, 1)?>
	            </select>
				<?php
			} else {
				?>
	        	<input type="checkbox" <?=$cEstado?> onclick="chkFiltro(this.checked, 'fEstado');" />
	            <select name="fEstado" id="fEstado" style="width:143px;" <?=$dEstado?>>
	                <option value="">&nbsp;</option>
	                <?=loadSelectGeneral("ESTADO-VALORACION", $fEstado, 0)?>
	            </select>
				<?php
			}
			?>
		</td>
        <td>&nbsp;</td>
	</tr>
	<tr>
		<td align="right">Centro de Costo:</td>
		<td>
			<input type="checkbox" <?=$cCodCentroCosto?> onclick="chkFiltro(this.checked, 'fCodCentroCosto');" />
			<select name="fCodCentroCosto" id="fCodCentroCosto" style="width:300px;" <?=$dCodCentroCosto?>>
            	<option value="">&nbsp;</option>
				<?=loadSelect2("ac_mastcentrocosto","CodCentroCosto","Descripcion",$fCodCentroCosto,0,array('CodDependencia'),array($fCodDependencia))?>
			</select>
		</td>
		<td align="right">Fecha Inicio: </td>
		<td>
			<input type="checkbox" <?=$cFechaInicio?> onclick="chkFiltro_2(this.checked, 'fFechaInicioD', 'fFechaInicioH');" />
			<input type="text" name="fFechaInicioD" id="fFechaInicioD" value="<?=$fFechaInicioD?>" <?=$dFechaInicio?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFecha(this);" /> -
            <input type="text" name="fFechaInicioH" id="fFechaInicioH" value="<?=$fFechaInicioH?>" <?=$dFechaInicio?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFecha(this);" />
        </td>
        <td>&nbsp;</td>
	</tr>
	<tr>
		<td class="tagForm">Ente:</td>
		<td class="gallery clearfix">
        	<input type="checkbox" <?=$cfCodOrganismoExterno?> onclick="ckLista(this.checked,['fCodOrganismoExterno','fNomOrganismoExterno','fCodDependenciaExterna','fNomDependenciaExterna'],['btfCodOrganismoExterno']);" />
            <input type="hidden" name="fCodOrganismoExterno" id="fCodOrganismoExterno" value="<?=$fCodOrganismoExterno?>" />
            <input type="text" name="fNomOrganismoExterno" id="fNomOrganismoExterno" value="<?=$fNomOrganismoExterno?>" style="width:295px;" readonly />
            <a href="../lib/listas/gehen.php?anz=listado_entes_externos&filtrar=default&ventana=selLista&campo1=fCodOrganismoExterno&campo2=fNomOrganismoExterno&campo3=fCodDependenciaExterna&campo4=fNomDependenciaExterna&iframe=true&width=950&height=430" rel="prettyPhoto[iframe1]" id="btfCodOrganismoExterno" style=" <?=$dfCodOrganismoExterno?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
		<td align="right">Fecha T&eacute;rmino: </td>
		<td>
			<input type="checkbox" <?=$cFechaTermino?> onclick="chkFiltro_2(this.checked, 'fFechaTerminoD', 'fFechaTerminoH');" />
			<input type="text" name="fFechaTerminoD" id="fFechaTerminoD" value="<?=$fFechaTerminoD?>" <?=$dFechaTermino?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFecha(this);" /> -
            <input type="text" name="fFechaTerminoH" id="fFechaTerminoH" value="<?=$fFechaTerminoH?>" <?=$dFechaTermino?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFecha(this);" />
        </td>
        <td>&nbsp;</td>
	</tr>
	<tr>
		<td class="tagForm">&nbsp;</td>
		<td>
        	<input type="checkbox" style="visibility:hidden;" />
            <input type="hidden" name="fCodDependenciaExterna" id="fCodDependenciaExterna" value="<?=$fCodDependenciaExterna?>" />
            <input type="text" name="fNomDependenciaExterna" id="fNomDependenciaExterna" value="<?=$fNomDependenciaExterna?>" style="width:295px;" readonly />
        </td>
		<td align="right">Año Fiscal:</td>
		<td>
			<input type="checkbox" <?=$cAnio?> onclick="chkCampos(this.checked, 'fAnio');" />
			<input type="text" name="fAnio" id="fAnio" value="<?=$fAnio?>" style="width:60px;" <?=$dAnio?> />
		</td>
        <td align="right"><input type="submit" value="Buscar"></td>
	</tr>
</table>
</div>
<div class="sep"></div>

<!--REGISTROS-->
<center>
<input type="hidden" name="sel_registros" id="sel_registros" />
<table class="tblBotones" style="width:100%; min-width:<?=$_width?>px;">
    <tr>
        <td><div id="rows"></div></td>
    </tr>
</table>

<div style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:215px;">
<table class="tblLista" style="width:100%; min-width:2500px;">
	<thead>
    <tr>
        <th scope="col" width="90" onclick="order('Anio,CodDependencia,Secuencia')">C&oacute;digo</th>
        <th scope="col" width="600" align="left" onclick="order('NomOrganismoExterno,NomDependenciaExterna')">Ente</th>
        <th scope="col" width="75" onclick="order('FechaInicio')">F.Inicio</th>
        <th scope="col" width="75" onclick="order('FechaTermino')">F.T&eacute;rmino</th>
        <th scope="col" width="75" onclick="order('FechaTerminoReal')">F.T&eacute;rmino Real</th>
        <th scope="col" width="90" onclick="order('Estado')">Estado</th>
        <th scope="col" align="left" onclick="order('ObjetivoGeneral')">Objetivo General</th>
    </tr>
    </thead>
    
    <tbody id="lista_registros">
	<?php
	//	consulto todos
	$sql = "SELECT af.CodValJur
			FROM
				pf_valoracionjuridica af
				INNER JOIN seguridad_alterna sa ON (af.CodDependencia = sa.CodDependencia AND
													sa.CodAplicacion = 'PF' AND
													sa.Usuario = '".$_SESSION["USUARIO_ACTUAL"]."' AND
													sa.FlagMostrar = 'S')
				INNER JOIN pf_organismosexternos oe ON (af.CodOrganismoExterno = oe.CodOrganismo)
				LEFT JOIN pf_dependenciasexternas de ON (af.CodDependenciaExterna = de.CodDependencia)
			WHERE 1 $filtro";
	$rows_total = getNumRows3($sql);
	//	consulto lista
	$sql = "SELECT
				af.CodValJur,
				af.Anio,
				af.CodDependencia,
				af.Secuencia,
				af.ObjetivoGeneral,
				af.FechaInicio,
				af.FechaTermino,
				af.FechaTerminoReal,
				af.Estado,
				af.CodOrganismo,
				oe.Organismo AS NomOrganismoExterno,
				de.Dependencia As NomDependenciaExterna
			FROM
				pf_valoracionjuridica af
				INNER JOIN seguridad_alterna sa ON (af.CodDependencia = sa.CodDependencia AND
													sa.CodAplicacion = 'PF' AND
													sa.Usuario = '".$_SESSION["USUARIO_ACTUAL"]."' AND
													sa.FlagMostrar = 'S')
				INNER JOIN pf_organismosexternos oe ON (af.CodOrganismoExterno = oe.CodOrganismo)
				LEFT JOIN pf_dependenciasexternas de ON (af.CodDependenciaExterna = de.CodDependencia)
			WHERE 1 $filtro
			ORDER BY $fOrderBy
			LIMIT ".intval($limit).", ".intval($maxlimit);
	$field = getRecords($sql);
	$rows_lista = count($field);
	foreach($field as $f) {
		$id = $f['CodValJur'];
		$Ente = "<strong>".htmlentities($f['NomOrganismoExterno'])."</strong>"."<br />".htmlentities($f['NomDependenciaExterna']);
		if ($ventana == "prorrogas") {
			?><tr class="trListaBody" onClick="getActividadesProrroga(['<?=$f['CodValJur']?>','<?=$f['CodOrganismo']?>'], ['<?=$campo1?>','<?=$campo2?>'], '<?=$f['CodValJur']?>');"><?php
		}
		else {
			?><tr class="trListaBody" onClick="selLista(['<?=$f['CodValJur']?>'], ['<?=$campo1?>']);"><?php
		}
		?>
			<td align="center"><?=$f['CodValJur']?></td>
			<td><?=$Ente?></td>
			<td align="center"><?=formatFechaDMA($f['FechaInicio'])?></td>
			<td align="center"><?=formatFechaDMA($f['FechaTermino'])?></td>
			<td align="center"><?=formatFechaDMA($f['FechaTerminoReal'])?></td>
			<td align="center"><?=printValoresGeneral("ESTADO-VALORACION", $f['Estado'])?></td>
			<td><?=htmlentities($f['ObjetivoGeneral'])?></td>
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

<script type="text/javascript" language="javascript">
	function getActividadesProrroga(valores, inputs, CodValJur) {
		//	ajax
		$.ajax({
			type: "POST",
			url: "../../pf/lib/fphp_funciones_ajax.php",
			data: "accion=getActividadesProrroga&CodValJur="+CodValJur,
			async: false,
			success: function(resp) {
				parent.$('#lista_actividades').html(resp);
				selLista(valores, inputs);
			}
		});
	}
	$(document).ready(function() {
	});
</script>