<?php
session_start();
if (!isset($_SESSION['USUARIO_ACTUAL']) || !isset($_SESSION['ORGANISMO_ACTUAL'])) header("Location: ../../index.php");
//	------------------------------------
include("../fphp.php");
//	------------------------------------
if ($filtrar == "default") {
	$fOrderBy = "TipoTransaccion, CodTipoTransaccion";
	$maxlimit = $_SESSION["MAXLIMIT"];
}
if ($fCodVoucher != "") { $cCodVoucher = "checked"; $filtro.=" AND (btt.CodVoucher = '".$fCodVoucher."')"; } else $dCodVoucher = "disabled";
if ($fTipoTransaccion != "") { $cTipoTransaccion = "checked"; $filtro.=" AND (btt.TipoTransaccion = '".$fTipoTransaccion."')"; } else $dTipoTransaccion = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (btt.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (btt.CodTipoTransaccion LIKE '%".$fBuscar."%' OR
					  btt.Descripcion LIKE '%".$fBuscar."%')";
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

<form name="frmentrada" id="frmentrada" action="listado_tipo_transaccion_bancaria.php?" method="post">
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="cod" id="cod" value="<?=$cod?>" />
<input type="hidden" name="nom" id="nom" value="<?=$nom?>" />
<input type="hidden" name="campo3" id="campo3" value="<?=$campo3?>" />
<input type="hidden" name="campo4" id="campo4" value="<?=$campo4?>" />
<input type="hidden" name="ventana" id="ventana" value="<?=$ventana?>" />
<input type="hidden" name="seldetalle" id="seldetalle" value="<?=$seldetalle?>" />
<input type="hidden" name="detalle" id="detalle" value="<?=$detalle?>" />
<div class="divBorder" style="width:900px;">
<table width="900" class="tblFiltro">
    <tr>
		<td align="right" width="100">Tipo de Voucher: </td>
		<td>
       		<input type="checkbox" <?=$cCodVoucher?> onclick="chkFiltro(this.checked, 'fCodVoucher');" />
            <select name="fCodVoucher" id="fCodVoucher" style="width:270px;" <?=$dCodVoucher?>>
                <option value=""></option>
                <?php //=loadSelect("ac_voucher", "CodVoucher", "Descripcion", $fCodVoucher, 0)?>
            </select>
		</td>
		<td align="right" width="100">Tipo: </td>
		<td>
       		<input type="checkbox" <?=$cTipoTransaccion?> onclick="chkFiltro(this.checked, 'fTipoTransaccion');" />
            <select name="fTipoTransaccion" id="fTipoTransaccion" style="width:100px;" <?=$dTipoTransaccion?>>
                <option value=""></option>
                <?php //=loadSelectValores("TIPO-TRANSACCION-BANCARIA", $fTipoTransaccion, 0)?>
            </select>
		</td>
	</tr>
    <tr>
		<td align="right">Buscar:</td>
		<td colspan="3">
			<input type="checkbox" <?=$cBuscar?> onclick="chkFiltro(this.checked, 'fBuscar');" />
			<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:264px;" <?=$dBuscar?> />
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

<div style="overflow:scroll; width:900px; height:325px;">
<table width="100%" class="tblLista">
	<thead>
	<tr>
        <th scope="col" width="60" onclick="order('CodTipoTransaccion')"><a href="javascript:">Tipo</a></th>
        <th scope="col" align="left" onclick="order('Descripcion')"><a href="javascript:">Descripci&oacute;n</a></th>
        <th scope="col" width="65" onclick="order('TipoTransaccion, CodTipoTransaccion')"><a href="javascript:">Tipo</a></th>
        <th scope="col" width="35" onclick="order('FlagVoucher')"><a href="javascript:">Gr. Vou.</a></th>
        <th scope="col" width="35" onclick="order('CodVoucher')"><a href="javascript:">Vou.</a></th>
        <th scope="col" width="125" onclick="order('CodCuenta')"><a href="javascript:">Cuenta</a></th>
        <th scope="col" width="125" onclick="order('CodCuentaPub20')"><a href="javascript:">Cuenta (Pub.20)</a></th>
    </tr>
    </thead>
	<?php
    //	consulto todos
    $sql = "SELECT btt.CodTipoTransaccion
            FROM ap_bancotipotransaccion btt
            WHERE 1 $filtro";
    $query = mysql_query($sql) or die ($sql.mysql_error());
    $rows_total = mysql_num_rows($query);
    
    //	consulto lista
    $sql = "SELECT
				btt.CodTipoTransaccion,
				btt.Descripcion,
				btt.FlagVoucher,
				btt.CodVoucher,
				btt.CodCuenta,
				btt.CodCuentaPub20,
				btt.TipoTransaccion,
				btt.Estado
            FROM ap_bancotipotransaccion btt
            WHERE 1 $filtro
            ORDER BY $fOrderBy
            LIMIT ".intval($limit).", ".intval($maxlimit);
	$query = mysql_query($sql) or die ($sql.mysql_error());
	$rows_lista = mysql_num_rows($query);
	//	MUESTRO LA TABLA
	while ($field = mysql_fetch_array($query)) {
		$id = "$field[CodTipoTransaccion]";
		if ($ventana == "transacciones_bancarias_tipo_insertar") {
			?>
        	<tr class="trListaBody" onclick="listado_insertar_linea('<?=$detalle?>', 'accion=<?=$ventana?>&CodTipoTransaccion=<?=$id?>', '<?=$id?>');">
        	<?php
		}
		else {
			?><tr class="trListaBody" onclick="selListado2('<?=$id?>', '<?=htmlentities($field["Descripcion"])?>', '<?=$cod?>', '<?=$nom?>');" id="<?=$id?>"><?php
		}
		?>
            <td><?=$field['CodTipoTransaccion']?></td>
			<td><?=htmlentities($field['Descripcion'])?></td>
            <td align="center"><?=printValoresGeneral("TIPO-TRANSACCION-BANCARIA", $field['TipoTransaccion'])?></td>
            <td align="center"><?=printFlag($field['FlagVoucher'])?></td>
            <td align="center"><?=$field['CodVoucher']?></td>
            <td align="center"><?=$field['CodCuenta']?></td>
            <td align="center"><?=$field['CodCuentaPub20']?></td>
        </tr>
		<?php
	}
	?>
</table>
</div>
<table width="900">
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
</body>
</html>