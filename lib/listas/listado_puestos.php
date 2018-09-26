<?php
//	------------------------------------
include("../fphp.php");
if (!isset($_SESSION['USUARIO_ACTUAL']) || !isset($_SESSION['ORGANISMO_ACTUAL'])) header("Location: ../../index.php");
//	------------------------------------
if ($filtrar == "default") {
	$fEstado = "A";
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "NomCategoriaCargo,GrupoOcup,SerieOcup,CodDesc";
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
$_width = 900;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
<link type="text/css" rel="stylesheet" href="../../css/custom-theme/jquery-ui-1.8.16.custom.css" charset="utf-8" />
<link type="text/css" rel="stylesheet" href="../../css/estilo.css" charset="utf-8" />
<link type="text/css" rel="stylesheet" href="../../css/prettyPhoto.css" media="screen" title="prettyPhoto main stylesheet" charset="utf-8" />
<script type="text/javascript" src="../../js/jquery-1.7.min.js" charset="utf-8"></script>
<script type="text/javascript" src="../../js/jquery-ui-1.8.16.custom.min.js" charset="utf-8"></script>
<script type="text/javascript" src="../../js/jquery.prettyPhoto.js" charset="utf-8"></script>
<script type="text/javascript" src="../../js/funciones.js" charset="utf-8"></script>
<script type="text/javascript" src="../../js/fscript.js" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
</script>
</head>

<body>
<!-- ui-dialog -->
<div id="cajaModal"></div>

<form name="frmentrada" id="frmentrada" action="listado_puestos.php?" method="post">
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="cod" id="cod" value="<?=$cod?>" />
<input type="hidden" name="nom" id="nom" value="<?=$nom?>" />
<input type="hidden" name="campo3" id="campo3" value="<?=$campo3?>" />
<input type="hidden" name="campo4" id="campo4" value="<?=$campo4?>" />
<input type="hidden" name="ventana" id="ventana" value="<?=$ventana?>" />
<input type="hidden" name="seldetalle" id="seldetalle" value="<?=$seldetalle?>" />
<input type="hidden" name="detalle" id="detalle" value="<?=$detalle?>" />
<div class="divBorder" style="width:<?=$_width?>px;">
<table width="<?=$_width?>" class="tblFiltro">
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
	</tr>
	<tr>
		<td align="right">Estado: </td>
		<td>
            <input type="checkbox" <?=$cEstado?> onclick="this.checked=!this.checked;" />
            <select name="fEstado" id="fEstado" style="width:100px;" <?=$dEstado?>>
                <?=loadSelectGeneral("ESTADO", $fEstado, 1)?>
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
	</tr>
</table>
</div>
<center><input type="submit" value="Buscar"></center><br />

<center>
<table width="<?=$_width?>" class="tblBotones">
	<tr>
		<td><div id="rows"></div></td>
	</tr>
</table>

<div style="overflow:scroll; width:<?=$_width?>px; height:200px;">
<table width="1500" class="tblLista">
	<thead>
    <tr>
        <th scope="col" width="60" onclick="order('CodDesc')">C&oacute;digo</th>
        <th scope="col" align="left" onclick="order('DescripCargo')">Descripci&oacute;n</th>
        <th scope="col" align="left" onclick="order('SerieOcup,CodDesc')">Serie</th>
        <th scope="col" align="left" onclick="order('GrupoOcup,SerieOcup,CodDesc')">Grupo</th>
        <th scope="col" width="100" onclick="order('NomCategoriaCargo,GrupoOcup,SerieOcup,CodDesc')">Categor&iacute;a</th>
        <th scope="col" width="75" onclick="order('Estado')">Estado</th>
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
		if ($ventana == "listado_insertar_linea") {
			?>
        	<tr class="trListaBody" onclick="listado_insertar_linea('<?=$detalle?>', 'accion=<?=$ventana?>&CodCargo=<?=$f['CodCargo']?>', '<?=$f['CodCargo']?>');">
        	<?php
		}
		elseif ($ventana == "cargos") {
			?>
        	<tr class="trListaBody" onclick="listado_insertar_linea('<?=$detalle?>', 'modulo=ajax&accion=<?=$detalle?>&CodCargo=<?=$f['CodCargo']?>', '<?=$f['CodCargo']?>', '../../organizacion/cargos_ajax.php');">
        	<?php
		}
		else {
			?><tr class="trListaBody" onclick="selListado2('<?=$f['CodDesc']?>', '<?=htmlentities($f["DescripCargo"])?>', '<?=$cod?>', '<?=$nom?>');" id="<?=$f['CodCargo']?>"><?php
		}
		?>
			<td align="right"><?=$f['CodDesc']?></td>
			<td><?=htmlentities($f['DescripCargo'])?></td>
			<td><?=htmlentities($f['SerieOcup'])?></td>
			<td><?=htmlentities($f['GrupoOcup'])?></td>
			<td align="center"><?=htmlentities($f['NomCategoriaCargo'])?></td>
			<td align="center"><?=printValoresGeneral("ESTADO", $f['Estado'])?></td>
		</tr>
		<?php
	}
	?>
</table>
</div>
<table width="<?=$_width?>">
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
	totalRegistros(parseInt(<?=$rows_total?>));
</script>
</body>
</html>