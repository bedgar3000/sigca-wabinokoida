<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion-".".sql", "w+");
##############################################################################/
if ($modulo == "formulario") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		##	-----------------
		$sql = "SELECT
					do.ComercialNroPedido AS NroPedido,
					do.ComercialFechaReq AS FechaPedido
				FROM lg_transaccion tr
				INNER JOIN co_documento do ON (
					do.CodDocumento = tr.ReferenciaNroDocumento
					AND do.CodTipoDocumento = tr.CodDocumentoReferencia
					AND do.NroDocumento = tr.NroDocumentoReferencia
					AND (
						do.CodTipoDocumento = 'BV'
						OR do.CodTipoDocumento = 'FC'
						OR do.CodTipoDocumento = 'TV'
					)
				)
				WHERE
					tr.CodOrganismo = '$CodOrganismo'
					AND tr.CodDocumento = '$RefCodTransaccion'
					AND tr.NroDocumento = '$RefNroTransaccion'";
		$field_pedido = getRecord($sql);
		##	
		$FechaDocumento = formatFechaAMD($FechaDocumento);
		$FechaFactura = formatFechaAMD($FechaFactura);
		$FlagFacturacionPrevia = (!empty($FlagFacturacionPrevia)?'S':'N');
		$iCodAlmacenDestino = (!empty($CodAlmacenDestino)?"'$CodAlmacenDestino'":"NULL");
		##	valido
		if (!trim($CodOrganismo) || !trim($FechaDocumento) || !trim($CodPersonaDestino) || !trim($CodVehiculoTrans) || !trim($CodChofer) || !trim($CodAlmacen) || !trim($MotivoTraslado) || !trim($RefCodTransaccion)) die("Debe llenar los campos (*) obligatorios.");
		##	codigo
		$CodGuia = codigo('lg_guiaremision','CodGuia',10);
		$NroGuia = codigo('lg_guiaremision','NroGuia',10,['CodOrganismo','NroSerie'],[$CodOrganismo,$NroSerie]);
		##	inserto
		$sql = "INSERT INTO lg_guiaremision
				SET
					CodGuia = '$CodGuia',
					CodOrganismo = '$CodOrganismo',
					NroSerie = '$NroSerie',
					NroGuia = '$NroGuia',
					FechaDocumento = '$FechaDocumento',
					CodPersonaDestino = '$CodPersonaDestino',
					DocFiscalDestino = '$DocFiscalDestino',
					NombreDestino = '$NombreDestino',
					DireccionDestino = '$DireccionDestino',
					CodVehiculoTrans = '$CodVehiculoTrans',
					CodPersonaTrans = '$CodPersonaTrans',
					DocFiscalTrans = '$DocFiscalTrans',
					NombreTrans = '$NombreTrans',
					CodChofer = '$CodChofer',
					RefCodTransaccion = '$RefCodTransaccion',
					RefNroTransaccion = '$RefNroTransaccion',
					RefTipoTransaccion = '$RefTipoTransaccion',
					CodAlmacen = '$CodAlmacen',
					AlmacenDireccion = '$AlmacenDireccion',
					CodAlmacenDestino = $iCodAlmacenDestino,
					MotivoTraslado = '$MotivoTraslado',
					MotivoDevolucion = '$MotivoDevolucion',
					NroBultos = '$NroBultos',
					FlagFacturacionPrevia = '$FlagFacturacionPrevia',
					NroFactura = '$NroFactura',
					FechaFactura = '$FechaFactura',
					CodCentroCosto = '$CodCentroCosto',
					NroPedido = '$field_pedido[NroPedido]',
					FechaPedido = '$field_pedido[FechaPedido]',
					Estado = '$Estado',
					EstadoDespacho = '$EstadoDespacho',
					EstadoFacturacion = '$EstadoFacturacion',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()";
		execute($sql);
		##	
		$message = "|Se ha generado la guia de remision Nro. <strong>$NroGuia</strong>";
		##	detalle
		$Secuencia = 0;
		for ($i=0; $i < count($detalle_Secuencia); $i++)
		{
			++$Secuencia;
			$detalle_Cantidad[$i] = setNumero($detalle_Cantidad[$i]);
			$detalle_CantidadRecibida[$i] = setNumero($detalle_CantidadRecibida[$i]);
			$detalle_CantidadDevuelta[$i] = setNumero($detalle_CantidadDevuelta[$i]);
			##	valido
			if (!trim($detalle_Cantidad[$i])) die("La Cantidad no puede ser cero.");
			##	inserto
			$sql = "INSERT INTO lg_guiaremisiondet
					SET
						CodGuia = '$CodGuia',
						Secuencia = '$Secuencia',
						CodItem = '$detalle_CodItem[$i]',
						Descripcion = '$detalle_Descripcion[$i]',
						CodUnidad = '$detalle_CodUnidad[$i]',
						Cantidad = '$detalle_Cantidad[$i]',
						CantidadRecibida = '$detalle_CantidadRecibida[$i]',
						CantidadDevuelta = '$detalle_CantidadDevuelta[$i]',
						RefCodTransaccion = '$detalle_RefCodTransaccion[$i]',
						RefNroTransaccion = '$detalle_RefNroTransaccion[$i]',
						RefSecTransaccion = '$detalle_RefSecTransaccion[$i]',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	-----------------
		mysql_query("COMMIT");
		##	
		die($message.'|'.$CodGuia);
	}
	//	modificar
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		##	-----------------
		$FechaDocumento = formatFechaAMD($FechaDocumento);
		$FechaFactura = formatFechaAMD($FechaFactura);
		$FlagFacturacionPrevia = (!empty($FlagFacturacionPrevia)?'S':'N');
		$iCodAlmacenDestino = (!empty($CodAlmacenDestino)?"'$CodAlmacenDestino'":"NULL");
		##	valido
		if (!trim($FechaDocumento) || !trim($CodVehiculoTrans) || !trim($CodChofer) || !trim($CodAlmacen) || !trim($MotivoTraslado)) die("Debe llenar los campos (*) obligatorios.");
		##	actualizo
		$sql = "UPDATE lg_guiaremision
				SET
					FechaDocumento = '$FechaDocumento',
					CodVehiculoTrans = '$CodVehiculoTrans',
					CodPersonaTrans = '$CodPersonaTrans',
					DocFiscalTrans = '$DocFiscalTrans',
					NombreTrans = '$NombreTrans',
					CodChofer = '$CodChofer',
					CodAlmacen = '$CodAlmacen',
					AlmacenDireccion = '$AlmacenDireccion',
					CodAlmacenDestino = $iCodAlmacenDestino,
					MotivoTraslado = '$MotivoTraslado',
					MotivoDevolucion = '$MotivoDevolucion',
					NroBultos = '$NroBultos',
					FlagFacturacionPrevia = '$FlagFacturacionPrevia',
					NroFactura = '$NroFactura',
					FechaFactura = '$FechaFactura',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodGuia = '$CodGuia'";
		execute($sql);
		##	detalle
		if (count($detalle_Secuencia))
		{
			$sql = "DELETE FROM lg_guiaremisiondet
					WHERE
						CodGuia = '$CodGuia'
						AND Secuencia NOT IN (".implode(",",$detalle_Secuencia).")";
		}
		else
		{
			$sql = "DELETE FROM lg_guiaremisiondet WHERE CodGuia = '$CodGuia'";
		}
		execute($sql);
		$Secuencia = 0;
		for ($i=0; $i < count($detalle_Secuencia); $i++)
		{
			if (!$detalle_Secuencia[$i]) 
				$detalle_Secuencia[$i] = codigo('lg_guiaremisiondet','Secuencia',11,['CodGuia'],[$CodGuia]);
			$detalle_Cantidad[$i] = setNumero($detalle_Cantidad[$i]);
			$detalle_CantidadRecibida[$i] = setNumero($detalle_CantidadRecibida[$i]);
			$detalle_CantidadDevuelta[$i] = setNumero($detalle_CantidadDevuelta[$i]);
			##	valido
			if (!trim($detalle_Cantidad[$i])) die("La Cantidad no puede ser cero.");
			##	inserto
			$sql = "REPLACE INTO lg_guiaremisiondet
					SET
						CodGuia = '$CodGuia',
						Secuencia = '$detalle_Secuencia[$i]',
						CodItem = '$detalle_CodItem[$i]',
						Descripcion = '$detalle_Descripcion[$i]',
						CodUnidad = '$detalle_CodUnidad[$i]',
						Cantidad = '$detalle_Cantidad[$i]',
						CantidadRecibida = '$detalle_CantidadRecibida[$i]',
						CantidadDevuelta = '$detalle_CantidadDevuelta[$i]',
						RefCodTransaccion = '$detalle_RefCodTransaccion[$i]',
						RefNroTransaccion = '$detalle_RefNroTransaccion[$i]',
						RefSecTransaccion = '$detalle_RefSecTransaccion[$i]',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	-----------------
		mysql_query("COMMIT");
	}
	//	despacho
	elseif ($accion == "despacho") {
		mysql_query("BEGIN");
		##	-----------------
		$FechaInicioTraslado = formatFechaAMD($FechaInicioTraslado);
		$FechaProgramadaEntrega = formatFechaAMD($FechaProgramadaEntrega);
		##	actualizo
		$sql = "UPDATE lg_guiaremision
				SET
					FechaInicioTraslado = '$FechaInicioTraslado',
					FechaProgramadaEntrega = '$FechaProgramadaEntrega',
					EstadoDespacho = 'DE',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodGuia = '$CodGuia'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	confirmar
	elseif ($accion == "confirmar") {
		mysql_query("BEGIN");
		##	-----------------
		$sql = "SELECT * FROM lg_guiaremisiondet WHERE CodGuia = '$CodGuia'";
		$field = getRecord($sql);
		##	
		$FechaFinalEntrega = formatFechaAMD($FechaFinalEntrega);
		##	valido
		if (!trim($FechaFinalEntrega)) die("Debe llenar los campos (*) obligatorios.");
		##	actualizo
		$sql = "UPDATE lg_guiaremision
				SET
					FechaFinalEntrega = '$FechaFinalEntrega',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodGuia = '$CodGuia'";
		execute($sql);
		##	detalle
		for ($i=0; $i < count($detalle_Secuencia); $i++)
		{
			$sql = "SELECT *
					FROM lg_guiaremisiondet
					WHERE
						CodGuia = '$CodGuia'
						AND Secuencia = '$detalle_Secuencia[$i]'";
			$field_detalle = getRecord($sql);
			##	
			$detalle_CantidadRecibida[$i] = setNumero($detalle_CantidadRecibida[$i]);
			$detalle_CantidadDevuelta[$i] = setNumero($detalle_CantidadDevuelta[$i]);
			##	valido
			if ((round(($detalle_CantidadRecibida[$i] + $detalle_CantidadDevuelta[$i]),5) > $field_detalle['Cantidad']))
				die('Cantidad recibida + cantidad devuelta no puede ser mayor a la cantidad por confirmar');
			##	
			if (round($detalle_CantidadRecibida[$i],5) == $field_detalle['Cantidad']) $iEstadoDespacho = "Estado = 'CO',";
			else $iEstadoDespacho = '';
			##	actualizo
			$sql = "UPDATE lg_guiaremisiondet
					SET
						CantidadRecibida = '$detalle_CantidadRecibida[$i]',
						CantidadDevuelta = '$detalle_CantidadDevuelta[$i]',
						$iEstadoDespacho
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()
					WHERE
						CodGuia = '$CodGuia'
						AND Secuencia = '$detalle_Secuencia[$i]'";
			execute($sql);
		}
		$sql = "SELECT *
				FROM lg_guiaremisiondet
				WHERE
					CodGuia = '$CodGuia'
					AND Estado <> 'CO'";
		$field_estados = getRecords($sql);
		if (!count($field_estados))
		{
			##	actualizo
			$sql = "UPDATE lg_guiaremision
					SET
						EstadoDespacho = 'CO',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()
					WHERE CodGuia = '$CodGuia'";
			execute($sql);
		}
		##	-----------------
		mysql_query("COMMIT");
	}
	//	anular
	elseif ($accion == "anular") {
		mysql_query("BEGIN");
		##	-----------------
		$field = getRecord("SELECT * FROM lg_guiaremision WHERE CodGuia = '$CodGuia'");
		##	
		if ($field['Estado'] == 'PR') $NuevoEstado = 'AN';
		else die('No puede anular una guia <strong>'.printValores('guia-remision-estado',$field['Estado']).'</strong>');
		##	actualizo
		$sql = "UPDATE lg_guiaremision
				SET
					Estado = '$NuevoEstado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodGuia = '$CodGuia'";
		execute($sql);
		##	detalle
		$sql = "UPDATE lg_guiaremisiondet
				SET
					Estado = '$NuevoEstado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodGuia = '$CodGuia'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
}
elseif ($modulo == "validar") {
	//	modificar
	if($accion == "modificar") {
		$sql = "SELECT Estado FROM lg_guiaremision WHERE CodGuia = '$codigo'";
		$Estado = getVar3($sql);
		if ($Estado != 'PR') die('No puede modificar una guia <strong>'.printValores('guia-remision-estado',$Estado).'</strong>');
	}
	//	despacho
	elseif($accion == "despacho") {
		$sql = "SELECT EstadoDespacho FROM lg_guiaremision WHERE CodGuia = '$codigo'";
		$EstadoDespacho = getVar3($sql);
		if ($EstadoDespacho != 'PE') die('No puede despachar una guia con Estado Despacho <strong>'.printValores('guia-remision-estado-despacho',$EstadoDespacho).'</strong>');
	}
	//	confirmar
	elseif($accion == "confirmar") {
		$sql = "SELECT EstadoDespacho FROM lg_guiaremision WHERE CodGuia = '$codigo'";
		$EstadoDespacho = getVar3($sql);
		if ($EstadoDespacho != 'DE') die('No puede confirmar una guia con Estado Despacho <strong>'.printValores('guia-remision-estado-despacho',$EstadoDespacho).'</strong>');
	}
	//	anular
	elseif($accion == "anular") {
		$sql = "SELECT Estado FROM lg_guiaremision WHERE CodGuia = '$codigo'";
		$Estado = getVar3($sql);
		if ($Estado != 'PR') die('No puede anular una guia <strong>'.printValores('guia-remision-estado',$Estado).'</strong>');
	}
}
elseif ($modulo == "ajax") {
	if ($accion == "detalle_insertar") {
		$id = $nro_detalles;
		##	
		$sql = "SELECT i.*
				FROM vw_lg_inventarioactual_item i
				WHERE i.CodItem = '$CodItem'";
		$field = getRecords($sql);
		foreach ($field as $f)
		{
			$id = $nro_detalles;
			?>
			<tr class="trListaBody" onclick="clk($(this), 'detalle', 'detalle_<?=$id?>');" id="detalle_<?=$id?>">
				<th>
					<input type="hidden" name="detalle_Secuencia[]" id="detalle_Secuencia<?=$id?>" value="0">
					<input type="hidden" name="detalle_RefCodTransaccion[]" value="">
					<input type="hidden" name="detalle_RefNroTransaccion[]" value="">
					<input type="hidden" name="detalle_RefSecTransaccion[]" value="">
					<?=$nro_detalles?>
				</th>
				<td>
					<input type="hidden" name="detalle_CodItem[]" value="<?=$f['CodItem']?>">
					<input type="text" name="detalle_CodInterno[]" value="<?=$f['CodInterno']?>" class="cell2" style="text-align: center;" readonly>
				</td>
				<td>
					<input type="text" name="detalle_Descripcion[]" value="<?=$f['Descripcion']?>" class="cell2" readonly>
				</td>
				<td>
					<select name="detalle_CodUnidad[]" id="detalle_CodUnidad<?=$id?>" class="cell">
						<?=loadSelect2('mastunidades','CodUnidad','CodUnidad',$f['CodUnidad'],1)?>
					</select>
				</td>
				<td>
					<select name="detalle_CodUnidadEqui[]" id="detalle_CodUnidadEqui<?=$id?>" class="cell">
						<?=loadSelect2('mastunidades','CodUnidad','CodUnidad',$f['CodUnidadEqui'],1)?>
					</select>
				</td>
				<td>
					<input type="text" name="detalle_Cantidad[]" value="<?=number_format(0,5,',','.')?>" class="cell currency5" style="text-align:right;">
				</td>
				<td>
					<input type="text" name="detalle_CantidadRecibida[]" value="<?=number_format(0,5,',','.')?>" class="cell2 currency5" style="text-align:right;" readonly>
				</td>
				<td>
					<input type="text" name="detalle_CantidadDevuelta[]" value="<?=number_format(0,5,',','.')?>" class="cell2 currency5" style="text-align:right;" readonly>
				</td>
			</tr>
			<?php
		}
	}
	elseif ($accion == "detalle_transaccion") {
		$nro_detalles = 0;
		##	
		$sql = "SELECT
					td.*,
					i.CodInterno
				FROM lg_transacciondetalle td
				INNER JOIN vw_lg_inventarioactual_item i ON i.CodItem = td.CodItem
				WHERE
					td.CodOrganismo = '$CodOrganismo'
					AND td.CodDocumento = '$CodDocumento'
					AND td.NroDocumento = '$NroDocumento'";
		$field = getRecords($sql);
		foreach ($field as $f)
		{
			$id = ++$nro_detalles;
			?>
			<tr class="trListaBody" onclick="clk($(this), 'detalle', 'detalle_<?=$id?>');" id="detalle_<?=$id?>">
				<th>
					<input type="hidden" name="detalle_Secuencia[]" id="detalle_Secuencia<?=$id?>" value="<?=$f['Secuencia']?>">
					<input type="hidden" name="detalle_RefCodTransaccion[]" value="<?=$f['CodDocumento']?>">
					<input type="hidden" name="detalle_RefNroTransaccion[]" value="<?=$f['NroDocumento']?>">
					<input type="hidden" name="detalle_RefSecTransaccion[]" value="<?=$f['Secuencia']?>">
					<?=$nro_detalles?>
				</th>
				<td>
					<input type="hidden" name="detalle_CodItem[]" value="<?=$f['CodItem']?>">
					<input type="text" name="detalle_CodInterno[]" value="<?=$f['CodInterno']?>" class="cell2" style="text-align: center;" readonly>
				</td>
				<td>
					<input type="text" name="detalle_Descripcion[]" value="<?=$f['Descripcion']?>" class="cell2" readonly>
				</td>
				<td>
					<select name="detalle_CodUnidad[]" id="detalle_CodUnidad<?=$id?>" class="cell">
						<?=loadSelect2('mastunidades','CodUnidad','CodUnidad',$f['CodUnidad'],1)?>
					</select>
				</td>
				<td>
					<select name="detalle_CodUnidadEqui[]" id="detalle_CodUnidadEqui<?=$id?>" class="cell">
						<?=loadSelect2('mastunidades','CodUnidad','CodUnidad',$f['CodUnidadEqui'],1)?>
					</select>
				</td>
				<td>
					<input type="text" name="detalle_Cantidad[]" value="<?=number_format($f['CantidadPedida'],5,',','.')?>" class="cell2 currency5" style="text-align:right;" readonly>
				</td>
				<td>
					<input type="text" name="detalle_CantidadRecibida[]" value="<?=number_format(0,5,',','.')?>" class="cell2 currency5" style="text-align:right;" readonly>
				</td>
				<td>
					<input type="text" name="detalle_CantidadDevuelta[]" value="<?=number_format(0,5,',','.')?>" class="cell2 currency5" style="text-align:right;" readonly>
				</td>
			</tr>
			<?php
		}
	}
	elseif ($accion == "getVehiculo") {
		$sql = "SELECT
					v.CodChofer,
					p1.CodPersona AS CodEmpresa,
					p1.NomCompleto AS Empresa,
					p1.DocFiscal AS DocFiscalEmpresa,
					md.Descripcion AS NomMarca
				FROM lg_vehiculos V
				INNER JOIN mastpersonas p1 On p1.CodPersona = v.CodEmpresa
				LEFT JOIN mastmiscelaneosdet md ON (
					md.CodDetalle = v.Marca
					AND md.CodMaestro = 'MARCAUTO'
				)
				WHERE v.CodVehiculo = '$CodVehiculo'";
		$field = getRecord($sql);
		if (empty($field))
			die(json_encode([
				'NomMarca' => '',
				'CodEmpresa' => '',
				'DocFiscalEmpresa' => '',
				'Empresa' => '',
				'CodChofer' => '',
			]));
		else
			die(json_encode($field));
	}
	elseif ($accion == "getDireccionAlmacen") {
		$sql = "SELECT * FROM lg_almacenmast WHERE CodAlmacen = '$CodAlmacen'";
		$field = getRecord($sql);
		if (empty($field))
			die(json_encode([
				'Direccion' => '',
			]));
		else
			die(json_encode($field));
	}
}
?>