<?php
session_start();
if (!isset($_SESSION['USUARIO_ACTUAL']) || !isset($_SESSION['ORGANISMO_ACTUAL'])) header("Location: ../../index.php");
//	------------------------------------
include("../fphp.php");
//	------------------------------------
if ($filtrar == "default") {
	$fEstado = "A";
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "Descripcion";
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (CodCurso LIKE '%".$fBuscar."%' OR
					  Descripcion LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (Estado = '".$fEstado."')"; } else $dEstado = "disabled";
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

<form name="frmentrada" id="frmentrada" action="listado_evaluaciones.php?" method="post">
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="cod" id="cod" value="<?=$cod?>" />
<input type="hidden" name="nom" id="nom" value="<?=$nom?>" />
<input type="hidden" name="campo3" id="campo3" value="<?=$campo3?>" />
<input type="hidden" name="campo4" id="campo4" value="<?=$campo4?>" />
<input type="hidden" name="ventana" id="ventana" value="<?=$ventana?>" />
<input type="hidden" name="seldetalle" id="seldetalle" value="<?=$seldetalle?>" />
<input type="hidden" name="detalle" id="detalle" value="<?=$detalle?>" />
<input type="hidden" name="marco" id="marco" value="<?=$marco?>" />
<div class="divBorder" style="width:<?=$_width?>px;">
<table width="<?=$_width?>" class="tblFiltro">
	<tr>
		<td align="right">Buscar:</td>
		<td>
			<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
			<input type="text" name="fBuscar" id="fBuscar" style="width:300px;" value="<?=$fBuscar?>" <?=$dBuscar?> />
		</td>
		<td align="right">Estado: </td>
		<td>
            <input type="checkbox" <?=$cEstado?> onclick="this.checked=!this.checked;" />
            <select name="fEstado" id="fEstado" style="width:100px;" <?=$dEstado?>>
                <?=loadSelectGeneral("ESTADO", $fEstado, 1)?>
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

<div style="overflow:scroll; width:<?=$_width?>px; height:270px;">
<table width="100%" class="tblLista">
	<thead>
    <tr>
        <th width="60" onclick="order('CodCurso')">C&oacute;digo</th>
        <th align="left" onclick="order('Descripcion')">Descripci&oacute;n</th>
    </tr>
    </thead>
    
    <tbody id="lista_registros">
	<?php
	//	consulto todos
	$sql = "SELECT CodCurso
			FROM rh_cursos
			WHERE 1 $filtro";
	$rows_total = getNumRows3($sql);
	
	//	consulto lista
	$sql = "SELECT
				CodCurso,
				Descripcion
			FROM rh_cursos
			WHERE 1 $filtro
			ORDER BY $fOrderBy
			LIMIT ".intval($limit).", ".intval($maxlimit);
	$field = getRecords($sql);
	$rows_lista = count($field);
	foreach($field as $f) {
		$id = "$f[CodCurso]";
		##
		if ($ventana == "listado_insertar_linea") {
			?>
        	<tr class="trListaBody" onclick="selListadoLista('<?=$seldetalle?>', '<?=$id?>', '<?=$f["Descripcion"]?>', '<?=$cod?>', '<?=$nom?>');">
        	<?php
		}
		elseif ($ventana == "listado_insertar_linea") {
			?>
        	<tr class="trListaBody" onclick="listado_insertar_linea('<?=$detalle?>', 'accion=<?=$ventana?>&CodCurso=<?=$id?>', '<?=$id?>');">
        	<?php
		}
		elseif ($ventana == "selListadoIFrame") {
			?>
        	<tr class="trListaBody" onclick="selListadoIFrame('<?=$marco?>', '<?=$id?>', '<?=($f["Descripcion"])?>', '<?=$cod?>', '<?=$nom?>');">
        	<?php
		}
		elseif ($ventana == "cargos") {
			?>
        	<tr class="trListaBody" onclick="listado_insertar_linea('<?=$detalle?>', 'modulo=ajax&accion=<?=$detalle?>&CodCurso=<?=$id?>', '<?=$id?>', '../../organizacion/cargos_ajax.php');">
        	<?php
		}
		else {
			?><tr class="trListaBody" onclick="selListado2('<?=$id?>', '<?=htmlentities($f["Descripcion"])?>', '<?=$cod?>', '<?=$nom?>');" id="<?=$id?>"><?php
		}
		?>
			<td align="center"><?=$f['CodCurso']?></td>
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