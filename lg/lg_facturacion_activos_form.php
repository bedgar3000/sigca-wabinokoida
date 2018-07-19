<?php
list($CodOrganismo, $Anio, $NroOrden, $Secuencia, $NroSecuencia) = split("[_]", $sel_registros);
//	consulto datos generales
$sql = "SELECT
			af.*,
			oc.NomProveedor,
			m.Descripcion AS NomMarca,
			md.Descripcion AS NomColor
		FROM
			lg_activofijo af
			INNER JOIN lg_ordencompra oc ON (oc.CodOrganismo = af.CodOrganismo AND
											 oc.Anio = af.Anio AND
											 oc.NroOrden = af.NroOrden)
			
			LEFT JOIN lg_marcas m ON (m.CodMarca = af.CodMarca)
			LEFT JOIN mastmiscelaneosdet md ON (md.CodDetalle = af.Color AND
												md.CodMaestro = 'COLOR')
		WHERE
			af.CodOrganismo = '".$CodOrganismo."' AND
			af.Anio = '".$Anio."' AND
			af.NroOrden = '".$NroOrden."' AND
			af.Secuencia = '".$Secuencia."' AND
			af.NroSecuencia = '".$NroSecuencia."'";
$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
if (mysql_num_rows($query)) $field = mysql_fetch_array($query);
//	------------------------------------
$_width = 800;
$_sufijo = "facturacion_activos";
$_titulo = "Facturaci&oacute;n de Activos";
$clkCancelar = "document.getElementById('frmentrada').submit();";
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lg_<?=$_sufijo?>_lista" method="POST" enctype="multipart/form-data" onsubmit="return <?=$_sufijo?>(this);" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
<input type="hidden" id="Secuencia" value="<?=$Secuencia?>" />
<input type="hidden" id="NroSecuencia" value="<?=$NroSecuencia?>" />

<table width="<?=$_width?>" class="tblForm">
	<tr>
    	<td colspan="4" class="divFormCaption">Datos de la Orden</td>
    </tr>
    <tr>
		<td class="tagForm" width="100">Organismo:</td>
		<td>
            <select id="CodOrganismo" style="width:300px;" class="codigo" disabled>
                <?=getOrganismos($field['CodOrganismo'], 3)?>
            </select>
		</td>
		<td class="tagForm" width="125">O/C:</td>
		<td>
        	<input type="hidden" id="Anio" value="<?=$field['Anio']?>" />
        	<input type="hidden" id="NroOrden" value="<?=$field['NroOrden']?>" />
            <input type="text" value="<?=$field['Anio']?>" style="width:30px;" class="codigo" disabled /> -
            <input type="text" value="<?=$field['NroOrden']?>" style="width:65px;" class="codigo" disabled />
		</td>
	</tr>
    <tr>
		<td class="tagForm">Proveedor:</td>
		<td colspan="3">
        	<input type="hidden" id="CodProveedor" value="<?=$field['CodProveedor']?>" />
            <input type="text" value="<?=htmlentities($field['NomProveedor'])?>" style="width:295px;" class="codigo" disabled />
		</td>
	</tr>
	<tr>
    	<td colspan="4" class="divFormCaption">Datos del Activo</td>
    </tr>
    <tr>
		<td class="tagForm">Descripci&oacute;n:</td>
		<td>
            <input type="text" value="<?=$field['CommoditySub']?>" style="width:45px;" disabled />
            <input type="text" value="<?=htmlentities($field['Descripcion'])?>" style="width:240px;" disabled />
		</td>
		<td class="tagForm">Fecha Ingreso:</td>
		<td>
            <input type="text" value="<?=formatFechaDMA($field['FechaIngreso'])?>" style="width:65px;" disabled />
		</td>
	</tr>
    <tr>
		<td class="tagForm">Modelo:</td>
		<td>
            <input type="text" value="<?=htmlentities($field['Modelo'])?>" style="width:295px;" disabled />
		</td>
		<td class="tagForm">Nro. Serie:</td>
		<td>
            <input type="text" value="<?=$field['NroSerie']?>" style="width:115px;" disabled />
		</td>
	</tr>
    <tr>
		<td class="tagForm">Cod. Barra:</td>
		<td>
            <input type="text" value="<?=$field['CodBarra']?>" style="width:150px;" disabled />
		</td>
		<td class="tagForm">Nro. Placa:</td>
		<td>
            <input type="text" value="<?=$field['NroPlaca']?>" style="width:115px;" disabled />
		</td>
	</tr>
    <tr>
		<td class="tagForm">Marca:</td>
		<td>
            <input type="text" value="<?=htmlentities($field['NomMarca'])?>" style="width:150px;" disabled />
		</td>
		<td class="tagForm">Ubicaci&oacute;n:</td>
		<td>
            <input type="text" value="<?=$field['CodUbicacion']?>" style="width:30px;" disabled />
		</td>
	</tr>
    <tr>
		<td class="tagForm">Color:</td>
		<td>
            <input type="text" value="<?=htmlentities($field['NomColor'])?>" style="width:150px;" disabled />
		</td>
		<td class="tagForm">Centro de Costo:</td>
		<td>
            <input type="text" value="<?=$field['CodCentroCosto']?>" style="width:30px;" disabled />
		</td>
	</tr>
	<tr>
    	<td colspan="4" class="divFormCaption">Seleccionar Obligaci&oacute;n</td>
    </tr>
    <tr>
		<td class="tagForm">* Obligaci&oacute;n:</td>
		<td class="gallery clearfix">
            <input type="text" id="CodTipoDocumento" style="width:15px;" disabled="disabled" />
			<input type="text" id="NroDocumento" style="width:125px;" disabled="disabled" />
            <a href="../lib/listas/listado_obligaciones_ordenes.php?filtrar=default&ReferenciaTipoDocumento=OC&Anio=<?=$Anio?>&CodOrganismo=<?=$CodOrganismo?>&ReferenciaNroDocumento=<?=$NroOrden?>&iframe=true&width=715&height=350" rel="prettyPhoto[iframe1]">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
        </td>
		<td class="tagForm">Fecha Documento:</td>
		<td>
            <input type="text" id="FechaRegistro" style="width:65px;" disabled />
		</td>
	</tr>
</table>

<center>
<input type="submit" value="Facturar" style="width:75px;" />
<input type="button" value="Cancelar" style="width:75px;" onclick="<?=$clkCancelar?>" />
</center>

</form>

<div style="width:<?=$_width?>px" class="divMsj">Campos Obligatorios *</div>