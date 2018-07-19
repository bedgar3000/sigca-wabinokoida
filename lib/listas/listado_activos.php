<?php
session_start();
if (!isset($_SESSION['USUARIO_ACTUAL']) || !isset($_SESSION['ORGANISMO_ACTUAL'])) header("Location: ../../index.php");
//	------------------------------------
include("../fphp.php");
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$fCodDependencia = $_SESSION["DEPENDENCIA_ACTUAL"];
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "Activo";
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (a.Activo LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (a.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fCodDependencia != "") { $cCodDependencia = "checked"; $filtro.=" AND (a.CodDependencia = '".$fCodDependencia."')"; } else $dCodDependencia = "disabled";
if ($fCodCentroCosto != "") { $cCodCentroCosto = "checked"; $filtro.=" AND (a.CodCentroCosto = '".$fCodCentroCosto."')"; } else $dCodCentroCosto = "disabled";
if ($fTipoActivo != "") { $cTipoActivo = "checked"; $filtro.=" AND (a.TipoActivo = '".$fTipoActivo."')"; } else $dTipoActivo = "disabled";
if ($fCategoria != "") { $cCategoria = "checked"; $filtro.=" AND (a.Categoria = '".$fCategoria."')"; } else $dCategoria = "disabled";
if ($fSituacionActivo != "") { $cSituacionActivo = "checked"; $filtro.=" AND (a.SituacionActivo = '".$fSituacionActivo."')"; } else $dSituacionActivo = "disabled";
if ($fTipoSeguro != "") { $cTipoSeguro = "checked"; $filtro.=" AND (a.TipoSeguro = '".$fTipoSeguro."')"; } else $dTipoSeguro = "disabled";
if ($fColor != "") { $cColor = "checked"; $filtro.=" AND (a.Color = '".$fColor."')"; } else $dColor = "disabled";
if ($fClasificacion != "") { $cClasificacion = "checked"; $filtro.=" AND (a.Clasificacion = '".$fClasificacion."')"; } else $dClasificacion = "visibility:hidden;";
if ($fClasificacionPublic20 != "") { $cClasificacionPublic20 = "checked"; $filtro.=" AND (a.ClasificacionPublic20 = '".$fClasificacionPublic20."')"; } else $dClasificacionPublic20 = "visibility:hidden;";
if ($fUbicacion != "") { $cUbicacion = "checked"; $filtro.=" AND (a.Ubicacion = '".$fUbicacion."')"; } else $dUbicacion = "visibility:hidden;";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (a.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
//	------------------------------------
$_width = 1000;
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

<form name="frmentrada" id="frmentrada" action="listado_activos.php?" method="post">
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="campo1" id="campo1" value="<?=$campo1?>" />
<input type="hidden" name="campo2" id="campo2" value="<?=$campo2?>" />
<input type="hidden" name="campo3" id="campo3" value="<?=$campo3?>" />
<input type="hidden" name="campo4" id="campo4" value="<?=$campo4?>" />
<input type="hidden" name="ventana" id="ventana" value="<?=$ventana?>" />
<input type="hidden" name="seldetalle" id="seldetalle" value="<?=$seldetalle?>" />
<div class="divBorder" style="width:<?=$_width?>px;">
<table width="<?=$_width?>" class="tblFiltro">
	<tr>
		<td align="right">Organismo: </td>
		<td>
			<input type="checkbox" <?=$cCodOrganismo?> onclick="chkFiltro(this.checked, 'fCodOrganismo');" />
			<select name="fCodOrganismo" id="fCodOrganismo" style="width:250px;" <?=$dCodOrganismo?> onChange="getOptionsSelect(this.value, 'dependencia', 'fCodDependencia', 0, 'fCentroCosto');">
            	<option value="">&nbsp;</option>
				<?=loadSelect2("mastorganismos", "CodOrganismo", "Organismo", $fCodOrganismo, 0)?>
			</select>
        </td>
		<td align="right">Tipo de Activo: </td>
		<td>
            <input type="checkbox" <?=$cTipoActivo?> onclick="chkFiltro(this.checked, 'fTipoActivo');" />
            <select name="fTipoActivo" id="fTipoActivo" style="width:125px;" <?=$dTipoActivo?>>
                <option value="">&nbsp;</option>
                <?=getMiscelaneos($fTipoActivo, "TIPOACTIVO", 0)?>
            </select>
        </td>
		<td align="right">Buscar:</td>
		<td>
			<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
			<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:120px;" <?=$dBuscar?> />
		</td>
	</tr>
	<tr>
		<td align="right">Dependencia: </td>
		<td>
			<input type="checkbox" <?=$cCodDependencia?> onclick="chkFiltro(this.checked, 'fCodDependencia');" />
			<select name="fCodDependencia" id="fCodDependencia" style="width:250px;" onChange="getOptionsSelect(this.value, 'centro_costo', 'fCentroCosto', 0);" <?=$dCodDependencia?>>
            	<option value="">&nbsp;</option>
				<?=loadSelect2("mastdependencias", "CodDependencia", "Dependencia", $fCodDependencia, 0, array('CodOrganismo'), array($fCodOrganismo))?>
			</select>
        </td>
		<td align="right">Situaci&oacute;n: </td>
		<td>
            <input type="checkbox" <?=$cSituacionActivo?> onclick="chkFiltro(this.checked, 'fSituacionActivo');" />
            <select name="fSituacionActivo" id="fSituacionActivo" style="width:125px;" <?=$dSituacionActivo?>>
                <option value="">&nbsp;</option>
				<?=loadSelect2("af_situacionactivo", "CodSituActivo", "Descripcion", $fSituacionActivo)?>
            </select>
        </td>
		<td align="right">Estado: </td>
		<td>
            <input type="checkbox" <?=$cEstado?> onclick="chkFiltro(this.checked, 'fEstado');" />
            <select name="fEstado" id="fEstado" style="width:125px;" <?=$dEstado?>>
                <option value="">&nbsp;</option>
                <?=loadSelectGeneral("ESTADO-ACTIVO", $fEstado, 0)?>
            </select>
        </td>
	</tr>
	<tr>
		<td align="right">Centro de Costo: </td>
		<td>
			<input type="checkbox" <?=$cCodCentroCosto?> onclick="chkFiltro(this.checked, 'fCentroCosto');" />
			<select name="fCentroCosto" id="fCentroCosto" style="width:250px;" <?=$dCodCentroCosto?>>
            	<option value="">&nbsp;</option>
				<?=loadSelect2("ac_mastcentrocosto", "CodCentroCosto", "Descripcion", $fCodCentroCosto, 0, array('CodDependencia'), array($fCodDependencia))?>
			</select>
        </td>
		<td align="right">T.Seguro: </td>
		<td>
            <input type="checkbox" <?=$cTipoSeguro?> onclick="chkFiltro(this.checked, 'fTipoSeguro');" />
            <select name="fTipoSeguro" id="fTipoSeguro" style="width:125px;" <?=$dTipoSeguro?>>
                <option value="">&nbsp;</option>
				<?=loadSelect2("af_tiposeguro", "CodTipoSeguro", "Descripcion", $fTipoSeguro)?>
            </select>
        </td>
		<?php
		if ($_PARAMETRO['CONTPUB20'] == "S") {
			?>
			<td align="right">Clasif.P.20: </td>
			<td>
	            <input type="checkbox" <?=$cClasificacionPublic20?> onclick="ckLista(this.checked,['fClasificacionPublic20'],['btClasificacionPublic20']);" />
	            <input type="text" name="fClasificacionPublic20" id="fClasificacionPublic20" style="width:75px;" value="<?=$fClasificacionPublic20?>" readonly="readonly" />
	            <a href="javascript:" onclick="window.open('listado_clasificacion_activospub20.php?filtrar=default&campo1=fClasificacionPublic20&ventana=selListaOpener', 'listado_clasificacion_activos20', 'width=825,height=400');" id="btClasificacionPublic20" style=" <?=$dClasificacionPublic20?>">
	            	<img src="../../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
    		</td>
			<?php
		} else {
			?>
			<td align="right">Cat/Clasf.: </td>
			<td>
	            <input type="checkbox" <?=$cClasificacion?> onclick="ckLista(this.checked,['fClasificacion'],['btClasificacion']);" />
	            <input type="text" name="fClasificacion" id="fClasificacion" style="width:75px;" value="<?=$fClasificacion?>" readonly="readonly" />
	            <a href="javascript:" onclick="window.open('listado_clasificacion_activos.php?filtrar=default&campo1=fClasificacion&ventana=selListaOpener', 'listado_clasificacion_activos', 'width=650,height=400');" id="btClasificacion" style=" <?=$dClasificacion?>">
	            	<img src="../../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
    		</td>
			<?php
		}
		?>
	</tr>
	<tr>
		<td align="right">Categor&iacute;a: </td>
		<td>
			<input type="checkbox" <?=$cCategoria?> onclick="chkFiltro(this.checked, 'fCategoria');" />
			<select name="fCategoria" id="fCategoria" style="width:250px;" <?=$dCategoria?>>
            	<option value="">&nbsp;</option>
				<?=loadSelect2("af_categoriadeprec", "CodCategoria", "DescripcionLocal", $fCategoria, 10)?>
			</select>
        </td>
		<td align="right">Color: </td>
		<td>
            <input type="checkbox" <?=$cColor?> onclick="chkFiltro(this.checked, 'fColor');" />
            <select name="fColor" id="fColor" style="width:125px;" <?=$dColor?>>
                <option value="">&nbsp;</option>
                <?=getMiscelaneos($fColor, "COLOR", 0)?>
            </select>
        </td>
		<td align="right">Ubicaci&oacute;n: </td>
		<td>
            <input type="checkbox" <?=$cUbicacion?> onclick="ckLista(this.checked,['fUbicacion'],['btUbicacion']);" />
            <input type="text" name="fUbicacion" id="fUbicacion" style="width:75px;" value="<?=$fUbicacion?>" readonly="readonly" />
            <a href="javascript:" onclick="window.open('listado_ubicaciones.php?filtrar=default&campo1=fUbicacion&ventana=selListaOpener', 'listado_clasificacion_activos', 'width=825,height=400');" id="btUbicacion" style=" <?=$dUbicacion?>">
            	<img src="../../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
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

<div style="overflow:scroll; width:<?=$_width?>px; height:175px;">
<table width="2100" class="tblLista">
	<thead>
	    <tr>
	        <th scope="col" width="90" onclick="order('Activo')">Activo</th>
	        <th scope="col" width="75" onclick="order('CodigoInterno')">Cod. Interno</th>
	        <th scope="col" width="100" onclick="order('CodigoBarras')">Cod. Barra</th>
	        <th scope="col" align="left" onclick="order('Descripcion')">Descripci&oacute;n</th>
	        <th scope="col" width="75" onclick="order('NomTipoActivo')">Tipo de Activo</th>
	        <th scope="col" width="100" onclick="order('NomSituacionActivo')">Situaci&oacute;n</th>
	        <th scope="col" width="300" align="left" onclick="order('NomCategoria')">Categor&iacute;a</th>
			<?php
			if ($_PARAMETRO['CONTPUB20'] == "S") { ?> <th scope="col" width="300" align="left" onclick="order('NomClasificacionPublic20')">Clasificaci&oacute;n (Pub.20)</th> <?php }
			else { ?> <th scope="col" width="300" align="left" onclick="order('NomClasificacion')">Clasificaci&oacute;n</th> <?php } 
			?>
	        <th scope="col" width="35" onclick="order('CentroCosto')">C.C</th>
	        <th scope="col" width="35" onclick="order('Ubicacion')">Ubicaci&oacute;n</th>
	        <th scope="col" width="150" onclick="order('NumeroSerie')">Numero de Serie</th>
	        <th scope="col" width="125" onclick="order('Estado')">Estado</th>
	    </tr>
    </thead>

    <tbody>
		<?php
	    //	consulto todos
		$sql = "SELECT a.Activo
				FROM
					af_activo a
					INNER JOIN af_situacionactivo sa ON (sa.CodSituActivo = a.SituacionActivo)
					INNER JOIN af_categoriadeprec cd ON (cd.CodCategoria = a.Categoria)
					LEFT JOIN af_clasificacionactivo ca ON (ca.CodClasificacion = a.Clasificacion)
					LEFT JOIN af_clasificacionactivo20 ca20 ON (ca20.CodClasificacion = a.ClasificacionPublic20)
					LEFT JOIN mastmiscelaneosdet md1 ON (md1.CodDetalle = a.TipoActivo AND
														 md1.CodMaestro = 'TIPOACTIVO')
				WHERE 1 $filtro";
		$rows_total = getNumRows3($sql);
		
		//	consulto lista
		$sql = "SELECT
					a.Activo,
					a.CodigoInterno,
					a.CodigoBarras,
					a.Descripcion,
					a.CentroCosto,
					a.Ubicacion,
					a.NumeroSerie,
					a.Estado,
					sa.Descripcion AS NomSituacionActivo,
					cd.DescripcionLocal AS NomCategoria,
					ca.Descripcion AS NomClasificacion,
					ca20.Descripcion AS NomClasificacionPublic20,
					md1.Descripcion AS NomTipoActivo
				FROM
					af_activo a
					INNER JOIN af_situacionactivo sa ON (sa.CodSituActivo = a.SituacionActivo)
					INNER JOIN af_categoriadeprec cd ON (cd.CodCategoria = a.Categoria)
					LEFT JOIN af_clasificacionactivo ca ON (ca.CodClasificacion = a.Clasificacion)
					LEFT JOIN af_clasificacionactivo20 ca20 ON (ca20.CodClasificacion = a.ClasificacionPublic20)
					LEFT JOIN mastmiscelaneosdet md1 ON (md1.CodDetalle = a.TipoActivo AND
														 md1.CodMaestro = 'TIPOACTIVO')
				WHERE 1 $filtro
				ORDER BY $fOrderBy
				LIMIT ".intval($limit).", ".intval($maxlimit);
		$field = getRecords($sql);
		$rows_lista = count($field);
		foreach($field as $f) {
			$id = $f['Activo'];
			if ($ventana == "selListadoLista") {
				?><tr class="trListaBody" onclick="selListadoLista('<?=$seldetalle?>','<?=$field["Activo"]?>','<?=$field["Descripcion"]?>','<?=$cod?>','<?=$nom?>');" id="<?=$f['Activo']?>"><?php
			}
			elseif ($ventana == "selListadoListaParent") {
				?><tr class="trListaBody" onclick="selListadoListaParent('<?=$seldetalle?>',['<?=$campo1?>','<?=$campo2?>'],['<?=$f['Activo']?>','<?=htmlentities($f['Descripcion'])?>']);" id="<?=$f['Activo']?>"><?php
			}
			else {
				?><tr class="trListaBody" onclick="selListado2('<?=$f['Activo']?>','<?=htmlentities($f["Activo"])?>','<?=$cod?>','<?=$nom?>');" id="<?=$f['Activo']?>"><?php
			}
			?>
				<td align="center"><?=$f['Activo']?></td>
				<td align="center"><?=$f['CodigoInterno']?></td>
				<td align="center"><?=$f['CodigoBarras']?></td>
				<td><?=htmlentities($f['Descripcion'])?></td>
				<td align="center"><?=htmlentities($f['NomTipoActivo'])?></td>
				<td align="center"><?=htmlentities($f['NomSituacionActivo'])?></td>
				<td><?=htmlentities($f['NomCategoria'])?></td>
				<?php
				if ($_PARAMETRO['CONTPUB20'] == "S") { ?> <td><?=htmlentities($f['NomClasificacionPublic20'])?></td> <?php }
				else { ?> <td><?=htmlentities($f['NomClasificacion'])?></td> <?php } 
				?>
				<td align="center"><?=$f['CentroCosto']?></td>
				<td align="center"><?=$f['Ubicacion']?></td>
				<td align="center"><?=$f['NumeroSerie']?></td>
				<td align="center"><?=printValoresGeneral("ESTADO-ACTIVO",$f['Estado'])?></td>

	        </tr>
			<?php
		}
		?>
    </tbody>
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