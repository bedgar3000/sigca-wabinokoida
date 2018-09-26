<?php
include("../fphp.php");
//	------------------------------------
if ($filtrar == "default") {
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "CodClasificacion";
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (CodClasificacion LIKE '%".$fBuscar."%' OR
					  Descripcion LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
//	------------------------------------
$_width = 600;
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
<script type="text/javascript" src="../../js/jquery.formatCurrency-1.4.0.js" charset="utf-8"></script>
<script type="text/javascript" src="../../js/jquery.formatCurrency.all.js" charset="utf-8"></script>
<script type="text/javascript" src="../../js/funciones.js" charset="utf-8"></script>
<script type="text/javascript" src="../../js/fscript.js" charset="utf-8"></script>
<script type="text/javascript" language="javascript">
$(document).ready(function() {});
</script>
</head>

<body>
<!-- ui-dialog -->
<div id="cajaModal"></div>

<form name="frmentrada" id="frmentrada" action="listado_clasificacion_activos.php?" method="post">
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="cod" id="cod" value="<?=$cod?>" />
<input type="hidden" name="nom" id="nom" value="<?=$nom?>" />
<input type="hidden" name="campo1" id="campo1" value="<?=$campo1?>" />
<input type="hidden" name="campo2" id="campo2" value="<?=$campo2?>" />
<input type="hidden" name="campo3" id="campo3" value="<?=$campo3?>" />
<input type="hidden" name="campo4" id="campo4" value="<?=$campo4?>" />
<input type="hidden" name="ventana" id="ventana" value="<?=$ventana?>" />
<input type="hidden" name="seldetalle" id="seldetalle" value="<?=$seldetalle?>" />
<div class="divBorder" style="width:<?=$_width?>px;">
<table width="<?=$_width?>" class="tblFiltro">
	<tr>
		<td align="right">Buscar:</td>
		<td>
			<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
			<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:400px;" <?=$dBuscar?> />
			<input type="submit" value="Buscar">
		</td>
	</tr>
</table>
</div>
<br />

<center>
<table width="<?=$_width?>" class="tblBotones">
	<tr>
		<td><div id="rows"></div></td>
	</tr>
</table>

<div style="overflow:scroll; width:<?=$_width?>px; height:250px;">
<table width="100%" class="tblLista">
	<thead>
    <tr>
        <th scope="col" width="50" onclick="order('CodClasificacion')">C&oacute;digo</th>
        <th scope="col" align="left" onclick="order('Descripcion')">Descripci&oacute;n</th>
    </tr>
    </thead>
	<?php
    //	consulto todos
	$sql = "SELECT CodClasificacion
			FROM af_clasificacionactivo
			WHERE Estado = 'A' $filtro";
	$rows_total = getNumRows3($sql);
	
	//	consulto lista
	$sql = "SELECT
				CodClasificacion,
				Descripcion
			FROM af_clasificacionactivo
			WHERE Estado = 'A' $filtro
			ORDER BY $fOrderBy
			LIMIT ".intval($limit).", ".intval($maxlimit);
	$field = getRecords($sql);
	$rows_lista = count($field);
	foreach($field as $f) {
		$id = $f['CodClasificacion'];
		if ($ventana == "selListaOpener") {
			?><tr class="trListaBody" onclick="selListaOpener(['<?=$f["CodClasificacion"]?>','<?=$f["Descripcion"]?>'],['<?=$campo1?>','<?=$campo2?>']);" id="<?=$f['CodClasificacion']?>"><?php
		}
		elseif ($ventana == "selListadoLista") {
			?><tr class="trListaBody" onclick="selListadoLista('<?=$seldetalle?>', '<?=$f["CodClasificacion"]?>', '<?=$f["Descripcion"]?>', '<?=$cod?>', '<?=$nom?>');" id="<?=$f['CodClasificacion']?>"><?php
		}
		else {
			?><tr class="trListaBody" onclick="selListado2('<?=$f['CodClasificacion']?>','<?=htmlentities($f["Descripcion"])?>','<?=$cod?>','<?=$nom?>');" id="<?=$f['CodClasificacion']?>"><?php
		}
		?>
			<td align="center"><?=$f['CodClasificacion']?></td>
			<td><?=htmlentities($f['Descripcion'])?></td>
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