<?php
if (!$ventana) $ventana = "selLista";
##	
if (!empty($accion_selector)) $accion = $accion_selector;
if (!empty($modulo_selector)) $modulo = $modulo_selector;
if (!empty($_APLICACION))
{
	if ($_APLICACION == 'HA') $concepto = '80-0003';
	elseif ($_APLICACION == 'LG') $concepto = '03-0001';
	elseif ($_APLICACION == 'CO') $concepto = '80-0040';
}
if (!empty($concepto)) list ($_SHOW, $_ADMIN, $_INSERT, $_UPDATE, $_DELETE) = opcionesPermisos('', $concepto);
else {
	$_SHOW = 'N';
	$_ADMIN = 'N';
	$_INSERT = 'N';
	$_UPDATE = 'N';
	$_DELETE = 'N';
}
//	------------------------------------
if (isset($selector)) 
{
	$fBuscar = $DocFiscal;
}
if ($filtrar == "default") {
	$fEstado = 'A';
	if ($FlagClasePersona != 'S') {
		$fEsEmpleado = 'S';
		$fEsProveedor = 'S';
		$fEsCliente = 'S';
		$fEsOtros = 'S';
	}
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "NomCompleto";
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (p.CodPersona LIKE '%".$fBuscar."%' OR
					  p.NomCompleto LIKE '%".$fBuscar."%' OR
					  p.Ndocumento LIKE '%".$fBuscar."%' OR
					  p.DocFiscal LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (p.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fTipoPersona != "") { $cTipoPersona = "checked"; $filtro.=" AND (p.TipoPersona = '".$fTipoPersona."')"; } else $dTipoPersona = "disabled";
if (!empty($fEsEmpleado) || !empty($fEsProveedor) || !empty($fEsCliente) || !empty($fEsOtros)) {
	$filtro .= " AND (";
	if ($fEsEmpleado) $filtro .= " p.EsEmpleado = 'S' ";
	if ($fEsProveedor) {
		if ($fEsEmpleado) $filtro .= " OR ";
		$filtro .= " p.EsProveedor = 'S' ";
	}
	if ($fEsCliente) {
		if ($fEsEmpleado || $fEsProveedor) $filtro .= " OR ";
		$filtro .= " p.EsCliente = 'S' ";
	}
	if ($fEsOtros) {
		if ($fEsEmpleado || $fEsProveedor || $fEsCliente) $filtro .= " OR ";
		$filtro .= " p.EsOtros = 'S' ";
	}
	$filtro .= ")";
} else {
	$filtro .= " AND (p.EsEmpleado <> 'S' AND p.EsProveedor <> 'S' AND p.EsCliente <> 'S' AND p.EsOtros <> 'S')";
}
if ($FlagClasePersona == 'S') {
	$dEsEmpleado = 'onclick="this.checked=!this.checked;"';
	$dEsProveedor = 'onclick="this.checked=!this.checked;"';
	$dEsCliente = 'onclick="this.checked=!this.checked;"';
	$dEsOtros = 'onclick="this.checked=!this.checked;"';
}
//	------------------------------------
$_width = 900;
?>
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lista_personas" method="post">
<input type="hidden" name="registro" id="registro" />
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="campo1" id="campo1" value="<?=$campo1?>" />
<input type="hidden" name="campo2" id="campo2" value="<?=$campo2?>" />
<input type="hidden" name="campo3" id="campo3" value="<?=$campo3?>" />
<input type="hidden" name="campo4" id="campo4" value="<?=$campo4?>" />
<input type="hidden" name="campo5" id="campo5" value="<?=$campo5?>" />
<input type="hidden" name="campo6" id="campo6" value="<?=$campo6?>" />
<input type="hidden" name="campo7" id="campo7" value="<?=$campo7?>" />
<input type="hidden" name="campo8" id="campo8" value="<?=$campo8?>" />
<input type="hidden" name="campo9" id="campo9" value="<?=$campo9?>" />
<input type="hidden" name="campo10" id="campo10" value="<?=$campo10?>" />
<input type="hidden" name="campo11" id="campo11" value="<?=$campo11?>" />
<input type="hidden" name="campo12" id="campo12" value="<?=$campo12?>" />
<input type="hidden" name="campo13" id="campo13" value="<?=$campo13?>" />
<input type="hidden" name="campo14" id="campo14" value="<?=$campo14?>" />
<input type="hidden" name="campo15" id="campo15" value="<?=$campo15?>" />
<input type="hidden" name="campo16" id="campo16" value="<?=$campo16?>" />
<input type="hidden" name="campo17" id="campo17" value="<?=$campo17?>" />
<input type="hidden" name="campo18" id="campo18" value="<?=$campo18?>" />
<input type="hidden" name="campo19" id="campo19" value="<?=$campo19?>" />
<input type="hidden" name="ventana" id="ventana" value="<?=$ventana?>" />
<input type="hidden" name="seldetalle" id="seldetalle" value="<?=$seldetalle?>" />
<input type="hidden" name="modulo" id="modulo" value="<?=$modulo?>" />
<input type="hidden" name="modulo_selector" id="modulo_selector" value="<?=$modulo?>" />
<input type="hidden" name="accion" id="accion" value="<?=$accion?>" />
<input type="hidden" name="accion_selector" id="accion_selector" value="<?=$accion?>" />
<input type="hidden" name="url" id="url" value="<?=$url?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="FlagClasePersona" id="FlagClasePersona" value="<?=$FlagClasePersona?>" />

<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
	<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
		<tr>
			<td align="right" width="125">Buscar:</td>
			<td>
				<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
				<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:255px;" <?=$dBuscar?> />
			</td>
			<td align="right" width="125">Clase de Persona: </td>
			<td>
	            <input type="checkbox" <?=$cTipoPersona?> onclick="chkFiltro(this.checked, 'fTipoPersona');" />
	            <select name="fTipoPersona" id="fTipoPersona" style="width:100px;" <?=$dTipoPersona?>>
	                <option value="">&nbsp;</option>
	                <?=loadSelectGeneral("TIPO-PERSONA", $fTipoPersona, 0)?>
	            </select>
			</td>
	        <td>&nbsp;</td>
		</tr>
		<tr>
			<td align="right">Tipo de Persona:</td>
			<td>
				<input type="checkbox" name="fEsEmpleado" id="fEsEmpleado" value="S" <?=chkFlag($fEsEmpleado)?> <?=$dEsEmpleado?> /> Empleado
				<input type="checkbox" name="fEsProveedor" id="fEsProveedor" value="S" <?=chkFlag($fEsProveedor)?> <?=$dEsProveedor?> /> Proveedor
				<input type="checkbox" name="fEsCliente" id="fEsCliente" value="S" <?=chkFlag($fEsCliente)?> <?=$dEsCliente?> /> Cliente
				<input type="checkbox" name="fEsOtros" id="fEsOtros" value="S" <?=chkFlag($fEsOtros)?> <?=$dEsOtros?> /> Otro
			</td>
			<td align="right">Estado: </td>
			<td>
	            <input type="checkbox" <?=$cEstado?> onclick="this.checked=!this.checked;" />
	            <select name="fEstado" id="fEstado" style="width:100px;" <?=$dEstado?>>
	                <?=loadSelectGeneral("ESTADO", $fEstado, 1)?>
	            </select>
			</td>
	        <td align="right"><input type="submit" value="Buscar"></td>
		</tr>
	</table>
</div>
<div class="sep"></div>

<!--REGISTROS-->
<input type="hidden" name="sel_registros" id="sel_registros" />
<?php
if (!empty($_APLICACION))
{
	?>
	<table class="tblBotones" style="width:100%; min-width:<?=$_width?>px;">
	    <tr>
	        <td><div id="rows"></div></td>
	        <td align="right">
	            <input type="button" value="Nuevo" style="width:75px;" class="insert" onclick="cargarPagina(this.form, '../../comunes/gehen.php?anz=personas_form&opcion=nuevo&selector=true&action=../lib/listas/gehen.php?anz=lista_personas');" />
	        </td>
	    </tr>
	</table>
	<?php	
}
?>

<div class="scroll" style="overflow:scroll; height:265px; width:100%; min-width:<?=$_width?>px;">
	<table class="tblLista" style="width:100%; min-width:800px;">
		<thead>
		    <tr>
		        <th width="50" onclick="order('CodPersona')">C&oacute;digo</th>
		        <th align="left" style="min-width: 400px;" onclick="order('NomCompleto')">Descripci&oacute;n</th>
		        <th width="35" onclick="order('EsEmpleado')">Emp.</th>
		        <th width="35" onclick="order('EsProveedor')">Pro.</th>
		        <th width="35" onclick="order('EsCliente')">Cli.</th>
		        <th width="35" onclick="order('EsOtros')">Otro</th>
		        <th width="75" onclick="order('Ndocumento')">Nro. Documento</th>
		        <th width="75" onclick="order('DocFiscal')">Doc. Fiscal</th>
		        <th width="50" onclick="order('Estado')">Estado</th>
		    </tr>
	    </thead>
	    
	    <tbody>
		<?php
		//	consulto todos
		$sql = "SELECT *
				FROM
					mastpersonas p
					LEFT JOIN mastempleado em On (em.CodPersona = p.CodPersona)
					LEFT JOIN mastcliente cl ON (cl.CodPersona = p.CodPersona)
					LEFT JOIN mastciudades c ON (c.CodCiudad = p.CiudadDomicilio)
					LEFT JOIN mastmunicipios m ON (m.CodMunicipio = c.CodMunicipio)
					LEFT JOIN mastestados e ON (e.CodEstado = m.CodEstado)
					LEFT JOIN mastpaises pi ON (pi.CodPais = e.CodPais)
					LEFT JOIN mastmiscelaneosdet md1 ON (
						md1.CodDetalle = cl.FormaFactura
						AND md1.CodMaestro = 'FORMAFACT'
					)
					LEFT JOIN co_vendedor v ON v.CodPersona = p.CodPersona
					LEFT JOIN co_rutadespacho rd ON rd.CodRutaDespacho = cl.CodRutaDespacho
					LEFT JOIN mastparroquias pr ON pr.CodParroquia = rd.CodParroquia
					LEFT JOIN mastproveedores po ON po.CodProveedor = p.CodPersona
				WHERE 1 $filtro";
		$rows_total = getNumRows3($sql);
		//	consulto lista
		$sql = "SELECT
					p.*,
					em.CodEmpleado,
					em.Usuario,
					em.Fingreso,
					m.CodMunicipio,
					e.CodEstado,
					pi.CodPais,
					cl.CodTipoDocumento,
					cl.FormaFactura,
					cl.TipoVenta,
					cl.CodFormaPago,
					cl.CodRutaDespacho,
					cl.CodVendedor AS CodClienteVendedor,
					md1.Descripcion AS NomFormaFactura,
					'' AS CodComunidad,
					'' AS Comunidad,
					rd.CodParroquia,
					pr.Descripcion AS Parroquia,
					v.CodPersona AS CodPersonaVendedor,
					po.CodTipoPago,
					po.CodTipoServicio
				FROM
					mastpersonas p
					LEFT JOIN mastempleado em On (em.CodPersona = p.CodPersona)
					LEFT JOIN mastcliente cl ON (cl.CodPersona = p.CodPersona)
					LEFT JOIN mastciudades c ON (c.CodCiudad = p.CiudadDomicilio)
					LEFT JOIN mastmunicipios m ON (m.CodMunicipio = c.CodMunicipio)
					LEFT JOIN mastestados e ON (e.CodEstado = m.CodEstado)
					LEFT JOIN mastpaises pi ON (pi.CodPais = e.CodPais)
					LEFT JOIN mastmiscelaneosdet md1 ON (
						md1.CodDetalle = cl.FormaFactura
						AND md1.CodMaestro = 'FORMAFACT'
					)
					LEFT JOIN co_vendedor v ON v.CodPersona = p.CodPersona
					LEFT JOIN co_rutadespacho rd ON rd.CodRutaDespacho = cl.CodRutaDespacho
					LEFT JOIN mastparroquias pr ON pr.CodParroquia = rd.CodParroquia
					LEFT JOIN mastproveedores po ON po.CodProveedor = p.CodPersona
				WHERE 1 $filtro
				ORDER BY $fOrderBy
				LIMIT ".intval($limit).", ".intval($maxlimit);
		$field = getRecords($sql);
		$rows_lista = count($field);
		foreach($field as $f) {
			if ($ventana == "Ndocumento") 
			{
				?><tr class="trListaBody" onClick="selLista(['<?=$f['CodPersona']?>','<?=htmlentities($f['NomCompleto'])?>','<?=$f['Ndocumento']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>']);"><?php
			}
			elseif ($ventana == "DocFiscal") 
			{
				?><tr class="trListaBody" onClick="selLista(['<?=$f['CodPersona']?>','<?=htmlentities($f['NomCompleto'])?>','<?=$f['DocFiscal']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>']);"><?php
			}
			elseif ($ventana == "selListadoListaParent") 
			{
				?><tr class="trListaBody" onclick="<?=$ventana?>('<?=$seldetalle?>',['<?=$campo1?>','<?=$campo2?>'],['<?=$f['CodPersona']?>','<?=htmlentities($f['NomCompleto'])?>']);" id="<?=$f['CodPersona']?>"><?php
			}
			elseif ($ventana == "ha_contribuyentes") 
			{
				?><tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['CodPersona']?>','<?=$f['Ndocumento']?>','<?=htmlentities($f['NomCompleto'])?>','<?=htmlentities($f['NomCompleto'])?>','<?=htmlentities($f['Direccion'])?>','<?=$f['Telefono1']?>','<?=$f['Telefono2']?>','<?=$f['Email']?>','<?=$f['TipoPersona']?>','<?=$f['CodPais']?>','<?=$f['CodEstado']?>','<?=$f['CodMunicipio']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>','<?=$campo4?>','<?=$campo5?>','<?=$campo6?>','<?=$campo7?>','<?=$campo8?>','<?=$campo9?>','<?=$campo10?>','<?=$campo11?>']);"><?php
			}
			elseif ($ventana == "propietario") 
			{
				?><tr class="trListaBody" onClick="selLista(['<?=$f['CodPersona']?>','<?=$f['Ndocumento']?>','<?=htmlentities($f['NomCompleto'])?>','<?=htmlentities($f['Direccion'])?>','<?=$f['Telefono1']?>','<?=$f['Telefono2']?>','<?=$f['Email']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>','<?=$campo4?>','<?=$campo5?>','<?=$campo6?>','<?=$campo7?>']);"><?php
			}
			elseif ($ventana == 'listado_insertar_linea') 
			{
				?><tr class="trListaBody" onClick="<?=$ventana?>('<?=$detalle?>','modulo=<?=$modulo?>&accion=<?=$accion?>&CodPersona=<?=$f['CodPersona']?>&CodOrganismo=<?=$CodOrganismo?>&CodPresupuesto=<?=$CodPresupuesto?>&detalle=<?=$detalle?>','<?=$f['CodPersona']?>','<?=$url?>');"><?php
			}
			elseif ($ventana == 'empleados') 
			{
				?><tr class="trListaBody" onClick="selLista(['<?=$f['CodPersona']?>','<?=htmlentities($f['NomCompleto'])?>','<?=$f['CodEmpleado']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>']);"><?php
			}
			elseif ($ventana == 'co_cotizacion') 
			{
				?><tr class="trListaBody" onClick="selLista(['<?=$f['CodPersona']?>','<?=htmlentities($f['NomCompleto'])?>','<?=$f['DocFiscal']?>','<?=$f['Direccion']?>','<?=$f['CodClienteVendedor']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>','<?=$campo4?>','<?=$campo5?>']);"><?php
			}
			elseif ($ventana == 'co_documento') 
			{
				?><tr class="trListaBody" onClick="selLista(['<?=$f['CodPersona']?>','<?=htmlentities($f['NomCompleto'])?>','<?=$f['DocFiscal']?>','<?=$f['Direccion']?>','<?=$f['FormaFactura']?>','<?=$f['NomFormaFactura']?>','<?=$f['TipoVenta']?>','<?=$f['CodFormaPago']?>','<?=$f['CodRutaDespacho']?>','<?=$f['CodPersona']?>','<?=$f['CodParroquia']?>','<?=$f['Parroquia']?>','<?=$f['Telefono1']?>','<?=$f['CodClienteVendedor']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>','<?=$campo4?>','<?=$campo5?>','<?=$campo6?>','<?=$campo7?>','<?=$campo8?>','<?=$campo9?>','<?=$campo10?>','<?=$campo11?>','<?=$campo12?>','<?=$campo13?>','<?=$campo14?>']);"><?php
			}
			elseif ($ventana == 'co_pedidos') 
			{
				?><tr class="trListaBody" onClick="selLista(['<?=$f['CodPersona']?>','<?=htmlentities($f['NomCompleto'])?>','<?=$f['DocFiscal']?>','<?=$f['Direccion']?>','<?=$f['FormaFactura']?>','<?=$f['NomFormaFactura']?>','<?=$f['TipoVenta']?>','<?=$f['CodFormaPago']?>','<?=$f['CodRutaDespacho']?>','<?=$f['CodPersona']?>','<?=$f['CodClienteVendedor']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>','<?=$campo4?>','<?=$campo5?>','<?=$campo6?>','<?=$campo7?>','<?=$campo8?>','<?=$campo9?>','<?=$campo10?>','<?=$campo11?>']);"><?php
			}
			elseif ($ventana == 'co_cobranza') 
			{
				?><tr class="trListaBody" onClick="selLista(['<?=$f['CodPersona']?>','<?=htmlentities($f['NomCompleto'])?>','<?=$f['DocFiscal']?>','<?=$f['CodClienteVendedor']?>','<?=$f['CodClienteVendedor']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>','<?=$campo4?>','<?=$campo5?>']);"><?php
			}
			elseif ($ventana == 'co_cajeros') 
			{
				?><tr class="trListaBody" onClick="co_cajeros(['<?=$f['CodPersona']?>','<?=htmlentities($f['NomCompleto'])?>','<?=$f['CodPersonaVendedor']?>','<?=$f['Fingreso']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>','<?=$campo4?>']);"><?php
			}
			elseif ($ventana == 'usuarios') 
			{
				?><tr class="trListaBody" onClick="selLista(['<?=$f['CodPersona']?>','<?=htmlentities($f['NomCompleto'])?>','<?=$f['CodEmpleado']?>','<?=$f['Usuario']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>','<?=$campo4?>']);"><?php
			}
			elseif ($ventana == 'lg_choferes') 
			{
				?><tr class="trListaBody" onClick="lg_choferes(['<?=$f['CodPersona']?>','<?=$f['Ndocumento']?>','<?=htmlentities($f['Apellido1'])?>','<?=htmlentities($f['Apellido2'])?>','<?=htmlentities($f['Nombres'])?>','<?=$f['EstadoCivil']?>','<?=$f['Telefono1']?>','<?=$f['Telefono2']?>','<?=htmlentities($f['Direccion'])?>','<?=$f['CiudadDomicilio']?>','<?=$f['CodMunicipio']?>','<?=$f['CodEstado']?>','<?=$f['CodPais']?>','<?=$f['TipoLicencia']?>','<?=$f['Nlicencia']?>','<?=formatFechaDMA($f['ExpiraLicencia'])?>','<?=formatFechaDMA($f['Fnacimiento'])?>','<?=$f['Sexo']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>','<?=$campo4?>','<?=$campo5?>','<?=$campo6?>','<?=$campo7?>','<?=$campo8?>','<?=$campo9?>','<?=$campo10?>','<?=$campo11?>','<?=$campo12?>','<?=$campo13?>','<?=$campo14?>','<?=$campo15?>','<?=$campo16?>','<?=$campo17?>','<?=$campo18?>']);"><?php
			}
			elseif ($ventana == 'lg_guiaremision') 
			{
				?><tr class="trListaBody" onClick="selLista(['<?=$f['CodPersona']?>','<?=htmlentities($f['NomCompleto'])?>','<?=$f['DocFiscal']?>','<?=htmlentities($f['Direccion'])?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>','<?=$campo4?>']);"><?php
			}
			elseif ($ventana == 'selListaOpener') 
			{
				?><tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['CodPersona']?>','<?=htmlentities($f['NomCompleto'])?>'], ['<?=$campo1?>','<?=$campo2?>']);"><?php
			}
			elseif ($ventana == 'filtro') 
			{
				?><tr class="trListaBody" onClick="selLista(['<?=$f['CodPersona']?>','<?=htmlentities($f['NomCompleto'])?>','<?=$f['DocFiscal']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>']);"><?php
			}
			elseif ($ventana == 'pagara') 
			{
				?><tr class="trListaBody" onClick="selLista(['<?=$f['CodPersona']?>','<?=htmlentities($f['NomCompleto'])?>','<?=$f['DocFiscal']?>','<?=$f['CodPersona']?>','<?=htmlentities($f['NomCompleto'])?>','<?=$f['DocFiscal']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>','<?=$campo4?>','<?=$campo5?>','<?=$campo6?>']);"><?php
			}
			elseif ($ventana == 'selListaGastoAdelanto') 
			{
				?><tr class="trListaBody" onClick="selListaGastoAdelanto(['<?=$f['CodPersona']?>','<?=htmlentities($f['NomCompleto'])?>','<?=$f['DocFiscal']?>','<?=$f['CodPersona']?>','<?=htmlentities($f['NomCompleto'])?>','<?=$f['DocFiscal']?>','<?=$f['CodTipoPago']?>','<?=$f['CodTipoServicio']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>','<?=$campo4?>','<?=$campo5?>','<?=$campo6?>','<?=$campo7?>','<?=$campo8?>']);"><?php
			}
			elseif ($ventana == "selListadoOrdenCompraPersona") {
				?><tr class="trListaBody" onclick="selListadoOrdenCompraPersona('<?=$f['CodPersona']?>');" id="<?=$f['CodPersona']?>"><?php 
			}
			elseif ($ventana == "selListadoOrdenServicioPersona") {
				?><tr class="trListaBody" onclick="selListadoOrdenServicioPersona('<?=$f['CodPersona']?>');" id="<?=$f['CodPersona']?>"><?php 
			}
			elseif ($ventana == "co_comitelocal") 
			{
				?><tr class="trListaBody" onClick="selLista(['<?=$f['CodPersona']?>','<?=htmlentities($f['NomCompleto'])?>','<?=$f['Ndocumento']?>','<?=htmlentities($f['Direccion'])?>','<?=$f['Telefono1']?>','<?=$f['Telefono2']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>','<?=$campo4?>','<?=$campo5?>','<?=$campo6?>']);"><?php
			}
			else 
			{
				?><tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['CodPersona']?>','<?=htmlentities($f['NomCompleto'])?>'], ['<?=$campo1?>','<?=$campo2?>']);"><?php
			}
			?>
				<td align="center"><?=$f['CodPersona']?></td>
				<td><?=htmlentities($f['NomCompleto'])?></td>
				<td align="center"><?=printFlag($f['EsEmpleado'])?></td>
				<td align="center"><?=printFlag($f['EsProveedor'])?></td>
				<td align="center"><?=printFlag($f['EsCliente'])?></td>
				<td align="center"><?=printFlag($f['EsOtros'])?></td>
				<td><?=$f['Ndocumento']?></td>
				<td><?=$f['DocFiscal']?></td>
				<td align="center"><?=printValoresGeneral('ESTADO',$f['Estado'])?></td>
			</tr>
			<?php
		}
		?>
	    </tbody>
	</table>
</div>
<table style="width:100%; min-width:<?=$_width?>px;">
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
</form>

<script type="text/javascript">
	<?php if ($ventana == "ha_contribuyentes") { ?>
		function ha_contribuyentes(valores, inputs) {
			loadSelectParent(parent.$('#CodEstado'), 'tabla=mastestados&CodPais='+valores[9]+'&CodEstado='+valores[10], 0, ['CodMunicipio','CodParroquia','CodLocalidad']);
			loadSelectParent(parent.$('#CodMunicipio'), 'tabla=mastmunicipios&CodEstado='+valores[10]+'&CodMunicipio='+valores[11], 0, ['CodParroquia','CodLocalidad']);
			loadSelectParent(parent.$('#CodParroquia'), 'tabla=mastparroquias&CodMunicipio='+valores[11], 0, ['CodLocalidad']);
			if (inputs) {
				for(var i=0; i<inputs.length; i++) {
					if (parent.$("#"+inputs[i]).length > 0) parent.$("#"+inputs[i]).val(valores[i]);
				}
			}
			parent.$.prettyPhoto.close();
		}
	<?php } elseif ($ventana == "lg_choferes") { ?>
		function lg_choferes(valores, inputs) {
			$.ajax({
				type: "POST",
				url: "../fphp_selects.php",
				data: 'tabla=ubicacion_ciudad&CodPais='+valores[12]+'&CodEstado='+valores[11]+'&CodMunicipio='+valores[10]+'&CodCiudad='+valores[9],
				async: true,
				success: function(resp) {
					var data = resp.split("|");

					parent.$('#CodEstado').empty().append(data[0]);
					parent.$('#CodMunicipio').empty().append(data[1]);
					parent.$('#CodCiudad').empty().append(data[2]);

					if (inputs) {
						for(var i=0; i<inputs.length; i++) {
							if (parent.$("#"+inputs[i]).length > 0) parent.$("#"+inputs[i]).val(valores[i]);
						}
					}
					parent.$.prettyPhoto.close();
				}
			});
		}
	<?php } elseif ($ventana == "co_cajeros") { ?>
		function co_cajeros(valores, inputs) {
			if (valores[2] != '') {
				parent.$('#FlagVendedor').prop('checked', true);
				parent.$('#CodPersonaVendedor').prop('disabled', false);
			} else {
				parent.$('#FlagVendedor').prop('checked', false);
				parent.$('#CodPersonaVendedor').prop('disabled', true).val('');
			}
			selLista(valores, inputs);
		}
	<?php } elseif ($ventana == "selListaGastoAdelanto") { ?>
		function selListaGastoAdelanto(valores, inputs) {
			var CodTipoServicio = valores[7];
			if (inputs) {
				for(var i=0; i<inputs.length; i++) {
					if (parent.$("#"+inputs[i]).length > 0) parent.$("#"+inputs[i]).val(valores[i]);
				}
			}
			parent.setFactorPorcentaje(CodTipoServicio);
			parent.$.prettyPhoto.close();
		}
	<?php } ?>
</script>