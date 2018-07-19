<?php
$CodOrganismo = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
##	
$_titulo = "Nueva Venta en Tienda";
$accion = "nuevo";
$label_submit = "Guardar Cambios";
$focus = "btSubmit";
$clkCancelar = "document.getElementById('frmentrada').submit();";
//	------------------------------------
$_width = 900;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<form name="frmentrada" id="frmentrada" action="../framemain.php" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('co_precioshistoria_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />

	<table style="width:100%; min-width:<?=$_width?>px;" class="tblForm">
		<tr>
	    	<td colspan="4" class="divFormCaption">Datos Generales</td>
	    </tr>
	    <tr>
			<td class="tagForm">Organismo:</td>
			<td>
				<select name="CodOrganismo" id="CodOrganismo" style="width:350px;">
					<?=getOrganismos($field['CodOrganismo'], 3)?>
				</select>
			</td>
		</tr>
	</table>

	<center>
		<input type="submit" value="<?=$label_submit?>" style="width:100px;" id="btSubmit" />
		<input type="button" value="Cancelar" style="width:75px;" id="btCancelar" onclick="<?=$clkCancelar?>" />
	</center>

	<input type="hidden" id="sel_detalle" />
	<table style="width:100%; min-width:<?=$_width?>px;" class="tblBotones">
		<thead>
			<tr>
				<th class="divFormCaption" colspan="2">DETALLES</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td align="right" class="gallery clearfix">
					<a id="a_detalle_item" href="../lib/listas/gehen.php?anz=lista_lg_items&filtrar=default&fFlagDisponible=S&ventana=listado_insertar_linea_precios&detalle=detalle&modulo=ajax&accion=detalle_insertar&url=../../co/co_precioshistoria_ajax.php&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" style="display:none;"></a>
					<a id="a_detalle_servicio" href="../lib/listas/gehen.php?anz=lista_co_servicios&filtrar=default&ventana=listado_insertar_linea_precios&detalle=detalle&modulo=ajax&accion=detalle_insertar&url=../../co/co_precioshistoria_ajax.php&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe2]" style="display:none;"></a>

					<input type="button" class="btLista" id="btInsertarItem" value="Item" onclick="$('#a_detalle_item').click();" />
					<input type="button" class="btLista" id="btInsertarServicio" value="Servicio" onclick="$('#a_detalle_servicio').click();" />
					<input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'detalle');" />
				</td>
			</tr>
		</tbody>
	</table>

	<div class="scroll" style="overflow:scroll; height:215px; width:100%; min-width:<?=$_width?>px; margin:auto;">
		<table class="tblLista" style="width:100%; min-width:1800px;">
			<thead>
				<tr>
					<th width="75">Item / Servicio</th>
					<th align="left">Descripci&oacute;n</th>
					<th width="20">I/S</th>
					<th width="100">Almacén</th>
					<th width="40">Uni. Venta</th>
					<th width="125">Monto Venta</th>
					<th width="125">Precio Mayor</th>
					<th width="125">Precio Menor</th>
					<th width="125">Precio Especial</th>
					<th width="125">Precio Costo</th>
					<th width="75">% Var. Precio Mayor</th>
					<th width="75">% Var. Precio Menor</th>
					<th width="75">% Var. Precio Especial</th>
					<th width="75">Fecha Desde</th>
					<th width="75">Fecha Hasta</th>
				</tr>
			</thead>
			
			<tbody id="lista_detalle">
			</tbody>
		</table>
	</div>
	<input type="hidden" id="nro_detalle" value="0" />
	<input type="hidden" id="can_detalle" value="0" />
</form>

<script type="text/javascript">
</script>