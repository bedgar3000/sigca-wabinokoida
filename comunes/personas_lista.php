<?php
//	------------------------------------
if ($filtrar == "default") {
	$fEstado = 'A';
	$fEsEmpleado = 'S';
	$fEsProveedor = 'S';
	$fEsCliente = 'S';
	$fEsOtros = 'S';
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "NomCompleto";
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (CodPersona LIKE '%".$fBuscar."%' OR
					  NomCompleto LIKE '%".$fBuscar."%' OR
					  Ndocumento LIKE '%".$fBuscar."%' OR
					  DocFiscal LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fTipoPersona != "") { $cTipoPersona = "checked"; $filtro.=" AND (TipoPersona = '".$fTipoPersona."')"; } else $dTipoPersona = "disabled";
if ($fEsEmpleado || $fEsProveedor || $fEsCliente || $fEsOtros) {
	$filtro .= " AND (";
	if ($fEsEmpleado) $filtro .= " EsEmpleado = 'S' ";
	if ($fEsProveedor) {
		if ($fEsEmpleado) $filtro .= " OR ";
		$filtro .= " EsProveedor = 'S' ";
	}
	if ($fEsCliente) {
		if ($fEsEmpleado || $fEsProveedor) $filtro .= " OR ";
		$filtro .= " EsCliente = 'S' ";
	}
	if ($fEsOtros) {
		if ($fEsEmpleado || $fEsProveedor || $fEsCliente) $filtro .= " OR ";
		$filtro .= " EsOtros = 'S' ";
	}
	$filtro .= ")";
} else {
	$filtro .= " AND (EsEmpleado <> 'S' AND EsProveedor <> 'S' AND EsCliente <> 'S' AND EsOtros <> 'S')";
}
//	------------------------------------
$_titulo = "Personas";
$_width = 700;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=personas_lista" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />

<!--FILTRO-->
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
				<input type="checkbox" name="fEsEmpleado" id="fEsEmpleado" value="S" <?=chkFlag($fEsEmpleado)?> /> Empleado
				<input type="checkbox" name="fEsProveedor" id="fEsProveedor" value="S" <?=chkFlag($fEsProveedor)?> /> Proveedor
				<input type="checkbox" name="fEsCliente" id="fEsCliente" value="S" <?=chkFlag($fEsCliente)?> /> Cliente
				<input type="checkbox" name="fEsOtros" id="fEsOtros" value="S" <?=chkFlag($fEsOtros)?> /> Otro
			</td>
			<td align="right">Estado: </td>
			<td>
	            <input type="checkbox" <?=$cEstado?> onclick="chkFiltro(this.checked, 'fEstado');" />
	            <select name="fEstado" id="fEstado" style="width:100px;" <?=$dEstado?>>
	                <option value="">&nbsp;</option>
	                <?=loadSelectGeneral("ESTADO", $fEstado, 0)?>
	            </select>
			</td>
	        <td align="right"><input type="submit" value="Buscar"></td>
		</tr>
	</table>
</div>
<div class="sep"></div>

<!--REGISTROS-->
<input type="hidden" name="sel_registros" id="sel_registros" />
<table class="tblBotones" style="width:100%; min-width:<?=$_width?>px;">
    <tr>
        <td><div id="rows"></div></td>
        <td align="right">
            <input type="button" value="Nuevo" style="width:75px;" class="insert" onclick="cargarPagina(this.form, 'gehen.php?anz=personas_form&opcion=nuevo');" />
            <input type="button" value="Modificar" style="width:75px;" class="update" onclick="cargarOpcion2(this.form, 'gehen.php?anz=personas_form&opcion=modificar', 'SELF', '', $('#sel_registros').val());" />
            <input type="button" value="Ver" style="width:75px;" class="ver" onclick="cargarOpcion2(this.form, 'gehen.php?anz=personas_form&opcion=ver', 'SELF', '', $('#sel_registros').val());" />
        </td>
    </tr>
</table>

<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px;">
	<table class="tblLista" style="width:100%; min-width:100%;">
		<thead>
		    <tr>
		        <th width="75" onclick="order('CodPersona')">C&oacute;digo</th>
		        <th align="left" onclick="order('NomCompleto')">Descripci&oacute;n</th>
		        <th width="35" onclick="order('EsEmpleado')">Emp.</th>
		        <th width="35" onclick="order('EsProveedor')">Pro.</th>
		        <th width="35" onclick="order('EsCliente')">Cli.</th>
		        <th width="35" onclick="order('EsOtros')">Otro</th>
		        <th width="125" onclick="order('Ndocumento')">Nro. Documento</th>
		        <th width="125" onclick="order('DocFiscal')">Doc. Fiscal</th>
		        <th width="75" onclick="order('Estado')">Estado</th>
		    </tr>
	    </thead>
	    
	    <tbody id="lista_registros">
		<?php
		//	consulto todos
		$sql = "SELECT *
				FROM mastpersonas
				WHERE 1 $filtro";
		$rows_total = getNumRows3($sql);
		//	consulto lista
		$sql = "SELECT *
				FROM mastpersonas
				WHERE 1 $filtro
				ORDER BY $fOrderBy
				LIMIT ".intval($limit).", ".intval($maxlimit);
		$field = getRecords($sql);
		$rows_lista = count($field);
		foreach($field as $f) {
			$id = $f['CodPersona'];
			?>
			<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
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