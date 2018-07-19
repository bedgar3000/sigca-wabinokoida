<?php
session_start();
if (!isset($_SESSION['USUARIO_ACTUAL']) || !isset($_SESSION['ORGANISMO_ACTUAL'])) header("Location: ../../index.php");
//	------------------------------------
include("../fphp.php");
//	------------------------------------
if ($filtrar == "default") {
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "CodConcepto";
	$fEstado = 'A';
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (cgv.CodConcepto LIKE '%".$fBuscar."%' OR
					  cgv.Descripcion LIKE '%".$fBuscar."%' OR
					  cgv.Numeral LIKE '%".$fBuscar."%' OR
					  md1.Descripcion LIKE '%".$fBuscar."%' OR
					  md2.Descripcion LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (cgv.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fCategoria != "") { $cCategoria = "checked"; $filtro.=" AND (cgv.Categoria = '".$fCategoria."')"; } else $dCategoria = "disabled";
if ($fArticulo != "") { $cArticulo = "checked"; $filtro.=" AND (cgv.Articulo = '".$fArticulo."')"; } else $dArticulo = "disabled";
//	------------------------------------
$_width = 800;
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
function viaticos_conceptos_insertar(CodConcepto) {
	var detalle = "conceptos";
	//	lista
	var nro_detalles = parent.$("#nro_"+detalle);
	var can_detalles = parent.$("#nro_"+detalle);
	var lista_detalles = parent.$("#lista_"+detalle);
	var nro = new Number(nro_detalles.val());	nro++;
	var can = new Number(can_detalles.val());	can++;	
	var idtr = detalle+"_"+nro;
	//	
	$.ajax({
		type: "POST",
		url: "../../ap/ap_viaticos_ajax.php",
		data: "modulo=ajax&accion=viaticos_conceptos_insertar&nro_detalles="+nro+"&can_detalles="+can+"&CodConcepto="+CodConcepto+"&CodPresupuesto="+parent.$('#CodPresupuesto').val()+"&CodFuente="+parent.$('#CodFuente').val()+"&CategoriaProg="+parent.$('#CategoriaProg').val(),
		async: false,
		success: function(resp) {
			if (parent.document.getElementById(idtr)) cajaModal("Registro ya insertado", "error_lista", 400);
			else {
				var partes = resp.split("|char:sep|");
				nro_detalles.val(nro);
				can_detalles.val(can);
				lista_detalles.append(partes[0]);
				parent.$("#lista_distribucion").append(partes[1]);
				parent.$.prettyPhoto.close();
				inicializarParent();
			}
		}
	});
}
</script>
</head>

<body>
<!-- ui-dialog -->
<div id="cajaModal"></div>

<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo">Listado de Conceptos de Vi&aacute;ticos</td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="listado_viatico_concepto.php?" method="post">
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
		<td align="right">Categor&iacute;as: </td>
		<td>
            <input type="checkbox" <?=$cCategoria?> onclick="chkFiltro(this.checked, 'fCategoria');" />
            <select name="fCategoria" id="fCategoria" style="width:200px;" <?=$dCategoria?>>
                <option value="">&nbsp;</option>
                <?=getMiscelaneos($fCategoria, "CATVIAT", 0)?>
            </select>
        </td>
		<td align="right">Buscar:</td>
		<td>
			<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
			<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:195px;" <?=$dBuscar?> />
		</td>
	</tr>
	<tr>
		<td align="right">Art&iacute;culos: </td>
		<td>
            <input type="checkbox" <?=$cArticulo?> onclick="chkFiltro(this.checked, 'fArticulo');" />
            <select name="fArticulo" id="fArticulo" style="width:200px;" <?=$dArticulo?>>
                <option value="">&nbsp;</option>
                <?=getMiscelaneos($fArticulo, "ARTVIAT", 0)?>
            </select>
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

<div style="overflow:scroll; width:<?=$_width?>px; height:200px;">
<table width="1200" class="tblLista">
	<thead>
    <tr>
        <th scope="col" width="50" onclick="order('CodConcepto')">Concepto</th>
        <th scope="col" align="left" onclick="order('Descripcion')">Descripci&oacute;n</th>
        <th scope="col" width="100" onclick="order('NomArticulo')">Art&iacute;culo</th>
        <th scope="col" width="75" onclick="order('Numeral')">Numeral</th>
        <th scope="col" width="150" onclick="order('NomCategoria')">Categor&iacute;a</th>
        <th scope="col" width="75" onclick="order('ValorUT')">Valor UT</th>
        <th scope="col" width="35" onclick="order('FlagMonto')">Mon.</th>
        <th scope="col" width="75" onclick="order('Estado')">Estado</th>
    </tr>
    </thead>
	<?php
    //	consulto todos
	$sql = "SELECT cgv.CodConcepto
			FROM
				ap_conceptogastoviatico cgv
				LEFT JOIN mastmiscelaneosdet md1 ON (md1.CodDetalle = cgv.Articulo AND
													 md1.CodMaestro = 'ARTVIAT' AND
													 md1.CodAplicacion = 'AP')
				LEFT JOIN mastmiscelaneosdet md2 ON (md2.CodDetalle = cgv.Categoria AND
													 md2.CodMaestro = 'CATVIAT' AND
													 md2.CodAplicacion = 'AP')
			WHERE 1 $filtro";
	$rows_total = getNumRows3($sql);
	
	//	consulto lista
	$sql = "SELECT
				cgv.CodConcepto,
				cgv.Descripcion,
				cgv.Articulo,
				cgv.Numeral,
				cgv.Categoria,
				cgv.ValorUT,
				cgv.FlagMonto,
				cgv.Estado,
				md1.Descripcion AS NomArticulo,
				md2.Descripcion AS NomCategoria
			FROM
				ap_conceptogastoviatico cgv
				LEFT JOIN mastmiscelaneosdet md1 ON (md1.CodDetalle = cgv.Articulo AND
													 md1.CodMaestro = 'ARTVIAT' AND
													 md1.CodAplicacion = 'AP')
				LEFT JOIN mastmiscelaneosdet md2 ON (md2.CodDetalle = cgv.Categoria AND
													 md2.CodMaestro = 'CATVIAT' AND
													 md2.CodAplicacion = 'AP')
			WHERE 1 $filtro
			ORDER BY $fOrderBy
			LIMIT ".intval($limit).", ".intval($maxlimit);
	$field = getRecords($sql);
	$rows_lista = count($field);
	foreach($field as $f) {
		$id = "$f[CodConcepto]";
		if ($ventana == "viaticos_conceptos_insertar") {
			?>
        	<tr class="trListaBody" onclick="viaticos_conceptos_insertar('<?=$f['CodConcepto']?>');">
        	<?php
		}
		elseif ($ventana == "listado_insertar_linea") {
			?>
        	<tr class="trListaBody" onclick="listado_insertar_linea('<?=$detalle?>', 'accion=<?=$ventana?>&CodConcepto=<?=$f['CodConcepto']?>', '<?=$f['CodConcepto']?>');">
        	<?php
		}
		else {
			?><tr class="trListaBody" onclick="selListado2('<?=$f['CodConcepto']?>', '<?=htmlentities($f["TipoPago"])?>', '<?=$cod?>', '<?=$nom?>');" id="<?=$f['CodConcepto']?>"><?php
		}
		?>
			<td align="center"><?=$f['CodConcepto']?></td>
			<td><?=htmlentities($f['Descripcion'])?></td>
			<td align="center"><?=$f['NomArticulo']?></td>
			<td align="center"><?=$f['Numeral']?></td>
			<td align="center"><?=$f['NomCategoria']?></td>
			<td align="center"><?=number_format($f['ValorUT'], 2, ',', '.')?></td>
			<td align="center"><?=printFlag($f['FlagMonto'])?></td>
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