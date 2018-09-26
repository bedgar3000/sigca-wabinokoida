<?php
//	------------------------------------
if ($filtrar == "default") {
	$fEstado = 'A';
	$maxlimit = $_SESSION["MAXLIMIT"];
	$fOrderBy = "CodOrganismo,TipoPago";
}
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (cbd.CodOrganismo LIKE '%$fBuscar%'
					  OR cbd.CodTipoPago LIKE '%$fBuscar%'
					  OR cbd.NroCuenta LIKE '%$fBuscar%'
					  OR o.Organismo LIKE '%$fBuscar%'
					  OR tp.TipoPago LIKE '%$fBuscar%')";
} else $dBuscar = "disabled";
//	------------------------------------
$_titulo = "Asignación de Cuentas Bancarias por Defecto";
$_width = 700;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=ap_ctabancariadefault_lista" method="post" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />

	<!--FILTRO-->
	<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
		<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
			<tr>
				<td align="right" width="100">Buscar:</td>
				<td>
					<input type="checkbox" <?=$cBuscar?> onclick="chkCampos(this.checked, 'fBuscar');" />
					<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:169px;" <?=$dBuscar?> />
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
	            <input type="button" value="Nuevo" style="width:75px;" class="insert F1" onclick="cargarPagina(this.form, 'gehen.php?anz=ap_ctabancariadefault_form&opcion=nuevo');" />
	            <input type="button" value="Modificar" style="width:75px;" class="update" onclick="cargarOpcion2(this.form, 'gehen.php?anz=ap_ctabancariadefault_form&opcion=modificar', 'SELF', '', $('#sel_registros').val());" />
	            <input type="button" value="Eliminar" style="width:75px;" class="delete" onclick="opcionRegistro3(this.form, $('#sel_registros').val(), 'formulario', 'eliminar', 'ap_ctabancariadefault_ajax.php');" />
	            <input type="button" value="Ver" style="width:75px;" class="ver" onclick="cargarOpcion2(this.form, 'gehen.php?anz=ap_ctabancariadefault_form&opcion=ver', 'SELF', '', $('#sel_registros').val());" />
	        </td>
	    </tr>
	</table>

	<div class="scroll" style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:265px;">
		<table class="tblLista" style="width:100%; min-width:<?=$_width?>px;">
			<thead>
			    <tr>
			        <th style="min-width: 200px;" align="left" onclick="order('Organismo')">Organismo</th>
			        <th style="min-width: 200px;" align="left" onclick="order('TipoPago')">Tipo de Pago</th>
			        <th style="min-width: 200px;" onclick="order('NroCuenta')">Cta. Bancaria</th>
			    </tr>
		    </thead>
		    
		    <tbody id="lista_registros">
			<?php
			//	consulto todos
			$sql = "SELECT *
					FROM ap_ctabancariadefault cbd
					INNER JOIN mastorganismos o ON o.CodOrganismo = cbd.CodOrganismo
					INNER JOIN masttipopago tp ON tp.CodTipoPago = cbd.CodTipoPago
					WHERE 1 $filtro";
			$rows_total = getNumRows3($sql);
			//	consulto lista
			$sql = "SELECT
						cbd.*,
						o.Organismo,
						tp.TipoPago
					FROM ap_ctabancariadefault cbd
					INNER JOIN mastorganismos o ON o.CodOrganismo = cbd.CodOrganismo
					INNER JOIN masttipopago tp ON tp.CodTipoPago = cbd.CodTipoPago
					WHERE 1 $filtro
					ORDER BY $fOrderBy
					LIMIT ".intval($limit).", ".intval($maxlimit);
			$field = getRecords($sql);
			$rows_lista = count($field);
			foreach($field as $f) {
				$id = $f['CodOrganismo'].'_'.$f['CodTipoPago'];
				?>
				<tr class="trListaBody" onclick="clk($(this), 'registros', '<?=$id?>');">
					<td><?=htmlentities($f['Organismo'])?></td>
					<td><?=htmlentities($f['TipoPago'])?></td>
					<td align="center"><?=$f['NroCuenta']?></td>
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