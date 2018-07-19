<?php
//	------------------------------------
if ($filtrar == "default") {
	$fEstado = 'A';
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "CodConcepto";
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (cc.CodConcepto LIKE '%".$fBuscar."%'
					  OR cc.Descripcion LIKE '%".$fBuscar."%'
					  OR cc.Categoria LIKE '%".$fBuscar."%'
					  OR cc.cod_partida LIKE '%".$fBuscar."%'
					  OR cc.CodCuenta LIKE '%".$fBuscar."%'
					  OR cc.CodCuentaPub20 LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fEstado != "") { $cEstado = "checked"; $filtro.=" AND (cc.Estado = '".$fEstado."')"; } else $dEstado = "disabled";
if ($fCategoria != "") { $cCategoria = "checked"; $filtro.=" AND (cc.Categoria = '".$fCategoria."')"; } else $dCategoria = "disabled";
if ($fcod_partida != "") { $ccod_partida = "checked"; $filtro.=" AND (cc.cod_partida = '".$fcod_partida."')"; } else $dcod_partida = "visibility:hidden;";
//	------------------------------------
$_titulo = "Conceptos de Compromisos Directos";
$_width = 900;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=ap_conceptoscertificacion_lista" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />

<!--FILTRO-->
<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
	<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
		<tr>
			<td align="right" width="150">Categoria:</td>
			<td>
				<input type="checkbox" <?=$cCategoria?> onclick="chkCampos(this.checked, 'fCategoria');" />
				<select name="fCategoria" id="fCategoria" style="width:250px;" <?=$dCategoria?>>
					<option value="">&nbsp;</option>
					<?=getMiscelaneos($fCategoria,'CATCERTIF')?>
				</select>
			</td>
			<td align="right" width="100">Estado: </td>
			<td>
	            <input type="checkbox" <?=$cEstado?> onclick="chkFiltro(this.checked, 'fEstado');" />
	            <select name="fEstado" id="fEstado" style="width:100px;" <?=$dEstado?>>
	                <option value="">&nbsp;</option>
	                <?=loadSelectGeneral("ESTADO", $fEstado, 0)?>
	            </select>
			</td>
	        <td>&nbsp;</td>
		</tr>
		<tr>
			<td align="right">Buscar:</td>
			<td>
				<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
				<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:250px;" <?=$dBuscar?> />
			</td>
			<td align="right">Partida: </td>
			<td class="gallery clearfix">
	            <input type="checkbox" <?=$ccod_partida?> onClick="ckLista(this.checked,['fcod_partida'],['btcod_partida'])" />
				<input type="text" name="fcod_partida" id="fcod_partida" value="<?=$fcod_partida?>" style="width:100px;" class="disabled" readonly />
	            <a href="../lib/listas/gehen.php?anz=lista_partidas&filtrar=default&campo1=fcod_partida&fcod_tipocuenta=4&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" id="btcod_partida" style=" <?=$dcod_partida?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
	        </td>
	        <td align="right"><input type="submit" value="Buscar"></td>
		</tr>
	</table>
</div>
<div class="sep"></div>

<!--REGISTROS-->
<input type="hidden" name="sel_registros" id="sel_registros" />
<table class="tblBotones" style="width:100%; min-width:<?=$_width?>px; margin:auto;">
    <tr>
        <td><div id="rows"></div></td>
        <td align="right">
            <input type="button" value="Nuevo" style="width:75px;" class="insert" onclick="cargarPagina(this.form, 'gehen.php?anz=ap_conceptoscertificacion_form&opcion=nuevo');" />
            <input type="button" value="Modificar" style="width:75px;" class="update" onclick="cargarOpcion2(this.form, 'gehen.php?anz=ap_conceptoscertificacion_form&opcion=modificar', 'SELF', '', $('#sel_registros').val());" />
            <input type="button" value="Eliminar" style="width:75px;" class="delete" onclick="opcionRegistro3(this.form, $('#sel_registros').val(), 'formulario', 'eliminar', 'ap_conceptoscertificacion_ajax.php');" />
            <input type="button" value="Ver" style="width:75px;" class="ver" onclick="cargarOpcion2(this.form, 'gehen.php?anz=ap_conceptoscertificacion_form&opcion=ver', 'SELF', '', $('#sel_registros').val());" />
        </td>
    </tr>
</table>

<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px; margin:auto;">
	<table class="tblLista" style="width:100%; min-width:<?=$_width?>px;">
		<thead>
		    <tr>
		        <th width="75" onclick="order('CodConcepto')">C&oacute;digo</th>
		        <th align="left" onclick="order('Descripcion')">Descripci&oacute;n</th>
		        <th width="120" onclick="order('Categoria')">Categor&iacute;a</th>
		        <th width="75" onclick="order('cod_partida')">Partida</th>
		        <th width="100" onclick="order('CodCuentaPub20')">Cuenta (Pub.20)</th>
		        <th width="100" onclick="order('CodCuenta')">Cuenta</th>
		        <th width="75" onclick="order('Estado')">Estado</th>
		    </tr>
	    </thead>
	    
	    <tbody id="lista_registros">
		<?php
		//	consulto todos
		$sql = "SELECT cc.*
				FROM
					ap_conceptoscertificacion cc
					LEFT JOIN mastmiscelaneosdet md ON (md.CodDetalle = cc.Categoria AND md.CodMaestro = 'CATCERTIF')
				WHERE 1 $filtro";
		$rows_total = getNumRows3($sql);
		//	consulto lista
		$sql = "SELECT
					cc.*,
					md.Descripcion AS NomCategoria
				FROM
					ap_conceptoscertificacion cc
					LEFT JOIN mastmiscelaneosdet md ON (md.CodDetalle = cc.Categoria AND md.CodMaestro = 'CATCERTIF')
				WHERE 1 $filtro
				ORDER BY $fOrderBy
				LIMIT ".intval($limit).", ".intval($maxlimit);
		$field = getRecords($sql);
		$rows_lista = count($field);
		foreach($field as $f) {
			$id = $f['CodConcepto'];
			?>
			<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
				<td align="center"><?=$f['CodConcepto']?></td>
				<td><?=htmlentities($f['Descripcion'])?></td>
				<td align="center"><?=htmlentities($f['NomCategoria'])?></td>
				<td align="center"><?=$f['cod_partida']?></td>
				<td><?=$f['CodCuentaPub20']?></td>
				<td><?=$f['CodCuenta']?></td>
				<td align="center"><?=printValoresGeneral('ESTADO',$f['Estado'])?></td>
			</tr>
			<?php
		}
		?>
	    </tbody>
	</table>
</div>

<table style="width:100%; min-width:<?=$_width?>px; margin:auto;">
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