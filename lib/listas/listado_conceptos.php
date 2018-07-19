<?php
session_start();
if (!isset($_SESSION['USUARIO_ACTUAL']) || !isset($_SESSION['ORGANISMO_ACTUAL'])) header("Location: ../../index.php");
//	------------------------------------
include("../fphp.php");
//	------------------------------------
if ($filtrar == "default") {
	$fEstado = "A";
	$fOrderBy = "Tipo, CodConcepto";
	$maxlimit = $_SESSION["MAXLIMIT"];
}
if ($fTipo != "") { $cTipo = "checked"; $filtro.=" AND (c.Tipo = '".$fTipo."')"; } else $dTipo = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (c.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (c.CodConcepto LIKE '%".$fBuscar."%' OR
					  c.Descripcion LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
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
<!-- ui-dialog -->
<div id="cajaModal"></div>

<form name="frmentrada" id="frmentrada" action="listado_conceptos.php?" method="post">
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="cod" id="cod" value="<?=$cod?>" />
<input type="hidden" name="nom" id="nom" value="<?=$nom?>" />
<input type="hidden" name="campo3" id="campo3" value="<?=$campo3?>" />
<input type="hidden" name="campo4" id="campo4" value="<?=$campo4?>" />
<input type="hidden" name="ventana" id="ventana" value="<?=$ventana?>" />
<input type="hidden" name="detalle" id="detalle" value="<?=$detalle?>" />
<input type="hidden" name="seldetalle" id="seldetalle" value="<?=$seldetalle?>" />
<div class="divBorder" style="width:800px;">
<table width="800" class="tblFiltro">
    <tr>
		<td align="right">Buscar:</td>
		<td>
			<input type="checkbox" <?=$cBuscar?> onclick="chkFiltro(this.checked, 'fBuscar');" />
			<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:264px;" <?=$dBuscar?> />
		</td>
		<td align="right">Tipo: </td>
		<td>
       		<input type="checkbox" <?=$cTipo?> onclick="chkFiltro(this.checked, 'fTipo');" />
            <select name="fTipo" id="fTipo" style="width:100px;" <?=$dTipo?>>
                <option value=""></option>
                <?=loadSelectGeneral("CONCEPTO-TIPO", $fTipo, 0)?>
            </select>
		</td>
	</tr>
    <tr>
    	<td colspan="2"></td>
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
<table width="800" class="tblBotones">
	<tr>
		<td><div id="rows"></div></td>
	</tr>
</table>

<div style="overflow:scroll; width:800px; height:250px;">
<table width="100%" class="tblLista">
	<thead>
    <tr>
        <th scope="col" width="50" onclick="order('CodConcepto')"><a href="javascript:">C&oacute;digo</a></th>
        <th scope="col" align="left" onclick="order('Descripcion')"><a href="javascript:">Descripci&oacute;n</a></th>
        <th scope="col" width="75" onclick="order('Tipo, CodConcepto')"><a href="javascript:">Tipo</a></th>
        <th scope="col" width="25" onclick="order('FlagAutomatico')"><a href="javascript:">Aut.</a></th>
        <th scope="col" width="25" onclick="order('FlagRetencion')"><a href="javascript:">Ret.</a></th>
        <th scope="col" width="25" onclick="order('FlagBono')"><a href="javascript:">Bon.</a></th>
    </tr>
    </thead>
    
    <tbody>
	<?php
    //	consulto todos
    $sql = "SELECT c.CodConcepto
            FROM pr_concepto c
            WHERE 1 $filtro";
    $query = mysql_query($sql) or die ($sql.mysql_error());
    $rows_total = mysql_num_rows($query);
    
    //	consulto lista
    $sql = "SELECT
				c.CodConcepto,
				c.Descripcion,
				c.Tipo,
				c.Estado,
				c.FlagAutomatico,
				c.FlagBono,
				c.FlagRetencion
            FROM pr_concepto c
            WHERE 1 $filtro
            ORDER BY $fOrderBy
            LIMIT ".intval($limit).", ".intval($maxlimit);
	$query = mysql_query($sql) or die ($sql.mysql_error());
	$rows_lista = mysql_num_rows($query);
	//	MUESTRO LA TABLA
	while ($field = mysql_fetch_array($query)) {
		if ($ventana == "listado_insertar_linea") {
			?>
        	<tr class="trListaBody" onclick="listado_insertar_linea('<?=$detalle?>', 'accion=<?=$ventana?>&CodConcepto=<?=$field['CodConcepto']?>', '<?=$field['CodConcepto']?>');">
        	<?php
		}
		elseif ($ventana == "retencion_judicial") {
			?>
        	<tr class="trListaBody" onclick="listado_insertar_linea('<?=$detalle?>', 'modulo=ajax&accion=<?=$detalle?>&CodConcepto=<?=$field['CodConcepto']?>', '<?=$f['CodConcepto']?>', '../../rh/rh_retencion_judicial_ajax.php');">
        	<?php
		}
		elseif ($ventana == "empleados_conceptos") {
			?>
        	<tr class="trListaBody" onclick="selListadoData('CodConcepto=<?=$field['CodConcepto']?>&NomConcepto=<?=htmlentities($field['Descripcion'])?>&accion=<?=$ventana?>');" id="<?=$field['CodConcepto']?>">
        	<?php
		}
		else {
			?><tr class="trListaBody" onclick="selListado2('<?=$field['CodConcepto']?>', '<?=htmlentities($field["Descripcion"])?>', '<?=$cod?>', '<?=$nom?>');" id="<?=$field['CodConcepto']?>"><?php
		}
		?>
            <td align="center"><?=$field['CodConcepto']?></td>
            <td><?=htmlentities($field['Descripcion'])?></td>
            <td align="center"><?=printValoresGeneral("CONCEPTO-TIPO", $field['Tipo'])?></td>
            <td align="center"><?=printFlag($field['FlagAutomatico'])?></td>
            <td align="center"><?=printFlag($field['FlagRetencion'])?></td>
            <td align="center"><?=printFlag($field['FlagBono'])?></td>
        </tr>
		<?php
	}
	?>
    </tbody>
</table>
</div>
<table width="800">
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