<?php
session_start();
if (!isset($_SESSION['USUARIO_ACTUAL']) || !isset($_SESSION['ORGANISMO_ACTUAL'])) header("Location: ../../index.php");
//	------------------------------------
include("../fphp.php");
//	------------------------------------
if ($filtrar == "default") {
	$fEstado = "A";
	$fOrderBy = "NomGastoGrupo, CodConceptoGasto";
	$maxlimit = $_SESSION["MAXLIMIT"];
}
if ($fCodGastoGrupo != "") { $cCodGastoGrupo = "checked"; $filtro.=" AND (cg.CodGastoGrupo = '".$fCodGastoGrupo."')"; } else $dCodGastoGrupo = "disabled";
if ($fCodPartida != "") { $cCodPartida = "checked"; $filtro.=" AND (cg.CodPartida = '".$fCodPartida."')"; } else $dCodPartida = "visibility:hidden;";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (cg.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (cg.CodConceptoGasto LIKE '%".$fBuscar."%' OR
					  cg.Descripcion LIKE '%".$fBuscar."%' OR
					  cg.CodPartida LIKE '%".$fBuscar."%' OR
					  cg.CodCuenta LIKE '%".$fBuscar."%' OR
					  cg.CodCuentaPub20 LIKE '%".$fBuscar."%' OR
					  cgg.Descripcion LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
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
// 	funcion para seleccionar de una lista un registro y colocar su valor en la ventana que lo llamo
function caja_chica_conceptos_insertar(CodConceptoGasto) {
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
		url: "../../ap/ap_caja_chica_ajax.php",
		data: "modulo=ajax&accion=caja_chica_conceptos_insertar&nro_detalles="+nro+"&can_detalles="+can+"&CodConceptoGasto="+CodConceptoGasto,
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
//	------------------------------------------
</script>
</head>

<body>
<!-- ui-dialog -->
<div id="cajaModal"></div>

<form name="frmentrada" id="frmentrada" action="listado_concepto_gastos.php?" method="post">
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="cod" id="cod" value="<?=$cod?>" />
<input type="hidden" name="nom" id="nom" value="<?=$nom?>" />
<input type="hidden" name="campo3" id="campo3" value="<?=$campo3?>" />
<input type="hidden" name="campo4" id="campo4" value="<?=$campo4?>" />
<input type="hidden" name="ventana" id="ventana" value="<?=$ventana?>" />
<input type="hidden" name="seldetalle" id="seldetalle" value="<?=$seldetalle?>" />
<input type="hidden" name="detalle" id="detalle" value="<?=$detalle?>" />
<input type="hidden" name="CodCentroCosto" id="CodCentroCosto" value="<?=$CodCentroCosto?>" />
<div class="divBorder" style="width:<?=$_width?>px;">
<table width="<?=$_width?>" class="tblFiltro">
    <tr>
		<td align="right" width="100">Grupo de Gasto: </td>
		<td>
       		<input type="checkbox" <?=$cCodGastoGrupo?> onclick="chkFiltro(this.checked, 'fCodGastoGrupo');" />
            <select name="fCodGastoGrupo" id="fCodGastoGrupo" style="width:270px;" <?=$dCodGastoGrupo?>>
                <option value=""></option>
                <?=loadSelect("ap_conceptogastogrupo", "CodGastoGrupo", "Descripcion", $fCodGastoGrupo, 0)?>
            </select>
		</td>
		<td align="right" width="100">Partida: </td>
		<td class="gallery clearfix">
            <input type="checkbox" <?=$cCodPartida?> onclick="chkListado(this.checked, 'aCodPartida', 'fCodPartida');" />            
            <input type="text" name="fCodPartida" id="fCodPartida" style="width:95px;" class="disabled" value="<?=$fCodPartida?>" readonly="readonly" />
            <a id="aCodPartida" href="../lib/listas/listado_clasificador_presupuestario.php?filtrar=default&cod=fCodPartida&iframe=true&width=950&height=525" rel="prettyPhoto[iframe1]" style=" <?=$dCodPartida?>">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
	</tr>
    <tr>
		<td align="right">Buscar:</td>
		<td>
			<input type="checkbox" <?=$cBuscar?> onclick="chkFiltro(this.checked, 'fBuscar');" />
			<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:264px;" <?=$dBuscar?> />
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

<div style="overflow:scroll; width:<?=$_width?>px; height:250px;">
<table width="1400" class="tblLista">
	<thead>
	<tr>
        <th scope="col" width="35" onclick="order('CodConceptoGasto')">Cod.</th>
        <th scope="col" align="left" onclick="order('Descripcion')">Descripci&oacute;n</th>
        <th scope="col" width="400" align="left" onclick="order('NomGastoGrupo, CodConceptoGasto')">Grupo de Gasto</th>
        <th scope="col" width="85" onclick="order('CodPartida')">Partida</th>
        <th scope="col" width="125" onclick="order('CodCuenta')">Cuenta</th>
        <th scope="col" width="125" onclick="order('CodCuentaPub20')">Cta. Pub. 20</th>
    </tr>
    </thead>
	<?php
    //	consulto todos
    $sql = "SELECT cg.CodConceptoGasto
            FROM
				ap_conceptogastos cg
				INNER JOIN ap_conceptogastogrupo cgg ON (cgg.CodGastoGrupo = cg.CodGastoGrupo)
            WHERE 1 $filtro";
    $query = mysql_query($sql) or die ($sql.mysql_error());
    $rows_total = mysql_num_rows($query);
    
    //	consulto lista
    $sql = "SELECT
				cg.CodConceptoGasto,
				cg.Descripcion,
				cg.Estado,
				cg.CodPartida,
				cg.CodCuenta,
				cg.CodCuentaPub20,
				cgg.Descripcion AS NomGastoGrupo
            FROM
				ap_conceptogastos cg
				INNER JOIN ap_conceptogastogrupo cgg ON (cgg.CodGastoGrupo = cg.CodGastoGrupo)
            WHERE 1 $filtro
            ORDER BY $fOrderBy
            LIMIT ".intval($limit).", ".intval($maxlimit);
	$query = mysql_query($sql) or die ($sql.mysql_error());
	$rows_lista = mysql_num_rows($query);
	//	MUESTRO LA TABLA
	while ($field = mysql_fetch_array($query)) {
        $id = "$field[CodConceptoGasto]";
		if ($ventana == "caja_chica_conceptos_insertar") {
			?>
        	<tr class="trListaBody" onclick="caja_chica_conceptos_insertar('<?=$field['CodConceptoGasto']?>');">
        	<?php
		}
		elseif ($ventana == "listado_insertar_linea") {
			?>
        	<tr class="trListaBody" onclick="listado_insertar_linea('<?=$detalle?>', 'accion=<?=$ventana?>&CodConceptoGasto=<?=$field['CodConceptoGasto']?>', '<?=$field['CodConceptoGasto']?>');">
        	<?php
		}
		elseif ($ventana == "caja_chica_distribucion_insertar") {
			?>
        	<tr class="trListaBody" onclick="listado_insertar_linea2('<?=$detalle?>', 'modulo=ajax&accion=<?=$ventana?>&CodConceptoGasto=<?=$field['CodConceptoGasto']?>&CodCentroCosto=<?=$CodCentroCosto?>', '<?=$field['CodConceptoGasto']?>', '../../ap/ap_caja_chica_ajax.php');">
        	<?php
		}
		else {
			?><tr class="trListaBody" onclick="selListado2('<?=$field['CodConceptoGasto']?>', '<?=htmlentities($field["TipoPago"])?>', '<?=$cod?>', '<?=$nom?>');" id="<?=$field['CodConceptoGasto']?>"><?php
		}
		?>
            <td align="center"><?=$field['CodConceptoGasto']?></td>
            <td><?=htmlentities($field['Descripcion'])?></td>
            <td><?=htmlentities($field['NomGastoGrupo'])?></td>
            <td align="center"><?=$field['CodPartida']?></td>
            <td align="center"><?=$field['CodCuenta']?></td>
            <td align="center"><?=$field['CodCuentaPub20']?></td>
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