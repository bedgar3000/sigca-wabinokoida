<?php
session_start();
if (!isset($_SESSION['USUARIO_ACTUAL']) || !isset($_SESSION['ORGANISMO_ACTUAL'])) header("Location: ../../index.php");
//	------------------------------------
extract($_POST);
extract($_GET);
//	------------------------------------
include("../fphp.php");
//	------------------------------------
if ($filtrar == "default") {
	$maxlimit = $_SESSION["MAXLIMIT"];
}
$filtro = "";
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro.=" AND (pt.CodDesc LIKE '%".$fBuscar."%' OR 
					pt.DescripCargo LIKE '%".$fBuscar."%' OR 
					pt.CodGrupOcup LIKE '%".$fBuscar."%' OR 
					go.GrupoOcup LIKE '%".$fBuscar."%' OR 
					pt.CodSerieOcup LIKE '%".$fBuscar."%' OR 
					so.SerieOcup LIKE '%".$fBuscar."%' OR 
					md.Descripcion LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fCodGrupOcup != "") { $cCodGrupOcup = "checked"; $filtro.=" AND (pt.CodGrupOcup = '".$fCodGrupOcup."')"; } else $dCodGrupOcup = "disabled";
if ($fCodSerieOcup != "") { $cCodSerieOcup = "checked"; $filtro.=" AND (pt.CodSerieOcup = '".$fCodSerieOcup."')"; } else $dCodSerieOcup = "disabled";
if ($fCategoriaCargo != "") { $cCategoriaCargo = "checked"; $filtro.=" AND (pt.CategoriaCargo = '".$fCategoriaCargo."')"; } else $dCategoriaCargo = "disabled";
//	------------------------------------
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
</head>

<body>
<form name="frmentrada" id="frmentrada" action="listado_cargos.php?" method="post">
<input type="hidden" name="registro" id="registro" />
<input type="hidden" name="cod" id="cod" value="<?=$cod?>" />
<input type="hidden" name="nom" id="nom" value="<?=$nom?>" />
<input type="hidden" name="campo3" id="campo3" value="<?=$campo3?>" />
<input type="hidden" name="campo4" id="campo4" value="<?=$campo4?>" />
<input type="hidden" name="ventana" id="ventana" value="<?=$ventana?>" />
<input type="hidden" name="seldetalle" id="seldetalle" value="<?=$seldetalle?>" />
<input type="hidden" name="marco" id="marco" value="<?=$marco?>" />
<div class="divBorder" style="width:900px;">
<table width="100%" class="tblFiltro">
    <tr>
		<td align="right" width="125">Grupo Ocupacional:</td>
		<td>
			<input type="checkbox" <?=$cCodGrupOcup?> onclick="chkFiltro(this.checked, 'fCodGrupOcup')" />
			<select name="fCodGrupOcup" id="fCodGrupOcup" style="width:250px;" onchange="getOptionsSelect(this.value, 'serie_ocupacional', 'fCodSerieOcup', true);" <?=$dCodGrupOcup?>>
				<option value="">&nbsp;</option>
				<?=loadSelect("rh_grupoocupacional", "CodGrupOcup", "GrupoOcup", $fCodGrupOcup, 0)?>
			</select>
		</td>
		<td align="right" width="125">Buscar:</td>
        <td>
            <input type="checkbox" <?=$cBuscar?> onclick="chkFiltro(this.checked, 'fBuscar');" />
            <input type="text" name="fBuscar" id="fBuscar" style="width:200px;" value="<?=$fBuscar?>" <?=$dBuscar?> />
		</td>
	</tr>
    <tr>
		<td align="right">Serie Ocupacional:</td>
		<td>
			<input type="checkbox" <?=$cCodSerieOcup?> onclick="chkFiltro(this.checked, 'fCodSerieOcup')" />
			<select name="fCodSerieOcup" id="fCodSerieOcup" style="width:250px;" <?=$dCodSerieOcup?>>
				<option value="">&nbsp;</option>
				<?=loadSelect("rh_serieocupacional", "CodSerieOcup", "SerieOcup", $fCodSerieOcup, 0)?>
			</select>
		</td>
		<td align="right">Categor&iacute;a:</td>
        <td>
			<input type="checkbox" <?=$cCategoriaCargo?> onclick="chkFiltro(this.checked, 'fCategoriaCargo')" />
			<select name="fCategoriaCargo" id="fCategoriaCargo" style="width:100px;" <?=$dCategoriaCargo?>>
				<option value="">&nbsp;</option>
				<?=getMiscelaneos($fCategoriaCargo, "CATCARGO", 0)?>
			</select>
		</td>
	</tr>
</table>
</div>
<center><input type="submit" value="Buscar"></center><br />

<center>
<table width="900" class="tblBotones">
	<tr>
		<td><div id="rows"></div></td>
	</tr>
</table>

<div style="overflow:scroll; width:900px; height:350px;">
<table width="100%" class="tblLista">
	<thead>
	<tr>
		<th scope="col" width="100">Clasificaci&oacute;n</th>
		<th scope="col">Descripci&oacute;n</th>
	</tr>
    </thead>
    
    <tbody>
	<?php
	//	consulto todos	
	$sql = "SELECT
				pt.CodCargo,
				pt.DescripCargo,
				pt.CodDesc,
				pt.CategoriaCargo,
				pt.CodGrupOcup,
				pt.CodSerieOcup,
				go.GrupoOcup,
				so.SerieOcup,
				md.Descripcion AS NomCategoriaCargo
			FROM
				rh_puestos pt
				INNER JOIN rh_serieocupacional so ON (so.CodSerieOcup = pt.CodSerieOcup)
				INNER JOIN rh_grupoocupacional go ON (go.CodGrupOcup = pt.CodGrupOcup)
				LEFT JOIN mastmiscelaneosdet md ON (md.CodDetalle = pt.CategoriaCargo AND
													md.CodMaestro = 'CATCARGO')
			WHERE pt.Estado = 'A' $filtro";
	$query = mysql_query($sql) or die ($sql.mysql_error());
	$rows_total = mysql_num_rows($query);
	
	//	consulto lista
	$sql = "SELECT
				pt.CodCargo,
				pt.DescripCargo,
				pt.CodDesc,
				pt.CategoriaCargo,
				pt.CodGrupOcup,
				pt.CodSerieOcup,
				pt.NivelSalarial,
				go.GrupoOcup,
				so.SerieOcup,
				md.Descripcion AS NomCategoriaCargo
			FROM
				rh_puestos pt
				INNER JOIN rh_serieocupacional so ON (so.CodSerieOcup = pt.CodSerieOcup)
				INNER JOIN rh_grupoocupacional go ON (go.CodGrupOcup = pt.CodGrupOcup)
				LEFT JOIN mastmiscelaneosdet md ON (md.CodDetalle = pt.CategoriaCargo AND
													md.CodMaestro = 'CATCARGO')
			WHERE pt.Estado = 'A' $filtro
			ORDER BY CategoriaCargo, CodGrupOcup, CodSerieOcup, CodDesc
			LIMIT ".intval($limit).", $maxlimit";
	$query = mysql_query($sql) or die ($sql.mysql_error());
	$rows_lista = mysql_num_rows($query);
	while ($field = mysql_fetch_array($query)) {
		if ($grupo1 != $field['CategoriaCargo']) {
			$grupo1 = $field['CategoriaCargo'];
			$grupo2 = "";
			$grupo3 = "";
			?>
			<tr class="trListaBody2">
				<td colspan="2"><?=$field['NomCategoriaCargo']?></td>
			</tr>
			<?php
		}
		if ($grupo2 != $field['CodGrupOcup']) {
			$grupo2 = $field['CodGrupOcup'];
			$grupo3 = "";
			?>
			<tr class="trListaBody3">
				<td align="center"><?=$field['CodGrupOcup']?></td>
				<td><?=$field['GrupoOcup']?></td>
			</tr>
			<?php
		}
		if ($grupo3 != $field['CodSerieOcup']) {
			$grupo3 = $field['CodSerieOcup'];
			?>
			<tr class="trListaBody4">
				<td align="center"><?=$field['CodSerieOcup']?></td>
				<td><?=$field['SerieOcup']?></td>
			</tr>
			<?php
		}
		
		if ($ventana == "requerimientos_cargo_selector") {
			?>
        	<tr class="trListaBody" onclick="requerimientos_cargo_selector('<?=$field['CodCargo']?>', '<?=$field["CodDesc"]?>', '<?=$field["DescripCargo"]?>');" id="<?=$field['CodCargo']?>">
        	<?php
		}
		elseif ($ventana == "selListadoIFrame") {
			?><tr class="trListaBody" onclick="selListadoIFrame('<?=$marco?>', '<?=$field['CodCargo']?>', '<?=($field["DescripCargo"])?>', '<?=$cod?>', '<?=$nom?>');" id="<?=$field['CodCargo']?>"><?php
		}
		elseif ($ventana == "reingreso_cargo_sel") {
			?>
        	<tr class="trListaBody" onclick="selListado2('<?=$field['CodCargo']?>', '<?=$field["DescripCargo"]?>', '<?=$cod?>', '<?=$nom?>', '<?=number_format($field["NivelSalarial"], 2, ',', '.')?>', '<?=$campo3?>');" id="<?=$field['CodCargo']?>">
        	<?php
		}
		else {
			?>
        	<tr class="trListaBody" onclick="selListado2('<?=$field['CodCargo']?>', '<?=$field["DescripCargo"]?>', '<?=$cod?>', '<?=$nom?>', '<?=$field["CodDesc"]?>', '<?=$campo3?>');" id="<?=$field['CodCargo']?>">
        	<?php
		}
        ?>
			<td align="center"><?=$field['CodDesc']?></td>
			<td><?=$field['DescripCargo']?></td>
		</tr>
		<?php
	}
	?>
    </tbody>
</table>
</div>
<table width="100%">
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