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
		$FechaCierre = formatFechaAMD($FechaCierre);
		$TotalEfectivo = setNumero($TotalEfectivo);
		$iCodPersonaCajero = (!empty($CodPersonaCajero)?"CodPersonaCajero = '$CodPersonaCajero',":'');
		##	valido
		if (!trim($CodOrganismo) || !trim($CodEstablecimiento) || !trim($FechaCierre)) die("Debe llenar los campos (*) obligatorios.");
		##	codigo
		$CodCierre = codigo('co_cierrecaja','CodCierre',10);
		$NroCierre = codigo('co_cierrecaja','NroCierre',10,['CodOrganismo','CodEstablecimiento'],[$CodOrganismo,$CodEstablecimiento]);
		##	inserto
		$sql = "INSERT INTO co_cierrecaja
				SET
					CodCierre = '$CodCierre',
					CodOrganismo = '$CodOrganismo',
					CodEstablecimiento = '$CodEstablecimiento',
					NroCierre = '$NroCierre',
					FechaCierre = '$FechaCierre',
					$iCodPersonaCajero
					TotalEfectivo = '$TotalEfectivo',
					Comentarios = '$Comentarios',
					PreparadoPor = '$PreparadoPor',
					FechaPreparado = '$FechaPreparado',
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()";
		execute($sql);
		##	detalle
		$Secuencia = 0;
		for ($i=0; $i < count($detalle_Secuencia); $i++)
		{
			++$Secuencia;
			$detalle_MontoLocal[$i] = setNumero($detalle_MontoLocal[$i]);
			$detalle_MontoAfecto[$i] = setNumero($detalle_MontoAfecto[$i]);
			$detalle_MontoOriginal[$i] = setNumero($detalle_MontoOriginal[$i]);
			$detalle_MontoImpuesto[$i] = setNumero($detalle_MontoImpuesto[$i]);
			$idetalle_CodTipoDocumento = (!empty($detalle_CodTipoDocumento[$i])?"CodTipoDocumento = '$detalle_CodTipoDocumento[$i]',":'');
			$idetalle_CodPersonaCliente = (!empty($detalle_CodPersonaCliente[$i])?"CodPersonaCliente = '$detalle_CodPersonaCliente[$i]',":'');
			##	valido
			if (!trim($detalle_CodTipoPago[$i])) die("El Tipo de Pago es obligatorio.");
			if (!trim($detalle_CodConceptoCaja[$i])) die("El Concepto es obligatorio.");
			elseif (!trim($detalle_MontoLocal[$i])) die("El Monto Local no puede ser cero.");
			##	inserto
			$sql = "INSERT INTO co_cierrecajadetalle
					SET
						CodCierre = '$CodCierre',
						Secuencia = '$Secuencia',
						CodConceptoCaja = '$detalle_CodConceptoCaja[$i]',
						TipoConcepto = '$detalle_TipoConcepto[$i]',
						Comentarios = '$detalle_Comentarios[$i]',
						CodTipoPago = '$detalle_CodTipoPago[$i]',
						MonedaDocumento = '$detalle_MonedaDocumento[$i]',
						MontoOriginal = '$detalle_MontoOriginal[$i]',
						MontoLocal = '$detalle_MontoLocal[$i]',
						MontoAfecto = '$detalle_MontoAfecto[$i]',
						MontoImpuesto = '$detalle_MontoImpuesto[$i]',
						$idetalle_CodPersonaCliente
						CodDocumento = '$detalle_CodDocumento[$i]',
						$idetalle_CodTipoDocumento
						NroDocumento = '$detalle_NroDocumento[$i]',
						RefCobranza = '$detalle_RefCobranza[$i]',
						RefCodCobranza = '$detalle_RefCodCobranza[$i]',
						RefSecuencia = '$detalle_RefSecuencia[$i]',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	-----------------
		mysql_query("COMMIT");
	}
	//	modificar
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		##	-----------------
		$FechaCierre = formatFechaAMD($FechaCierre);
		$TotalEfectivo = setNumero($TotalEfectivo);
		$iCodPersonaCajero = (!empty($CodPersonaCajero)?"CodPersonaCajero = '$CodPersonaCajero',":'');
		##	valido
		if (!trim($FechaCierre)) die("Debe llenar los campos (*) obligatorios.");
		##	actualizo
		$sql = "UPDATE co_cierrecaja
				SET
					FechaCierre = '$FechaCierre',
					$iCodPersonaCajero
					TotalEfectivo = '$TotalEfectivo',
					Comentarios = '$Comentarios',
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodCierre = '$CodCierre'";
		execute($sql);
		##	detalle
		if (count($detalle_Secuencia))
		{
			$sql = "DELETE FROM co_cierrecajadetalle
					WHERE
						CodCierre = '$CodCierre'
						AND Secuencia NOT IN (".implode(",",$detalle_Secuencia).")";
		}
		else
		{
			$sql = "DELETE FROM co_cierrecajadetalle WHERE CodCierre = '$CodCierre'";
		}
		execute($sql);
		$Secuencia = 0;
		for ($i=0; $i < count($detalle_Secuencia); $i++)
		{
			if (!$detalle_Secuencia[$i]) 
				$detalle_Secuencia[$i] = codigo('co_cierrecajadetalle','Secuencia',11,['CodCierre'],[$CodCierre]);
			$detalle_MontoLocal[$i] = setNumero($detalle_MontoLocal[$i]);
			$detalle_MontoAfecto[$i] = setNumero($detalle_MontoAfecto[$i]);
			$detalle_MontoOriginal[$i] = setNumero($detalle_MontoOriginal[$i]);
			$detalle_MontoImpuesto[$i] = setNumero($detalle_MontoImpuesto[$i]);
			$idetalle_CodTipoDocumento = (!empty($detalle_CodTipoDocumento[$i])?"CodTipoDocumento = '$detalle_CodTipoDocumento[$i]',":'');
			$idetalle_CodPersonaCliente = (!empty($detalle_CodPersonaCliente[$i])?"CodPersonaCliente = '$detalle_CodPersonaCliente[$i]',":'');
			##	valido
			if (!trim($detalle_CodTipoPago[$i])) die("El Tipo de Pago es obligatorio.");
			if (!trim($detalle_CodConceptoCaja[$i])) die("El Concepto es obligatorio.");
			elseif (!trim($detalle_MontoLocal[$i])) die("El Monto Local no puede ser cero.");
			##	inserto
			$sql = "REPLACE INTO co_cierrecajadetalle
					SET
						CodCierre = '$CodCierre',
						Secuencia = '$detalle_Secuencia[$i]',
						CodConceptoCaja = '$detalle_CodConceptoCaja[$i]',
						TipoConcepto = '$detalle_TipoConcepto[$i]',
						Comentarios = '$detalle_Comentarios[$i]',
						CodTipoPago = '$detalle_CodTipoPago[$i]',
						MonedaDocumento = '$detalle_MonedaDocumento[$i]',
						MontoOriginal = '$detalle_MontoOriginal[$i]',
						MontoLocal = '$detalle_MontoLocal[$i]',
						MontoAfecto = '$detalle_MontoAfecto[$i]',
						MontoImpuesto = '$detalle_MontoImpuesto[$i]',
						$idetalle_CodPersonaCliente
						CodDocumento = '$detalle_CodDocumento[$i]',
						$idetalle_CodTipoDocumento
						NroDocumento = '$detalle_NroDocumento[$i]',
						RefCobranza = '$detalle_RefCobranza[$i]',
						RefCodCobranza = '$detalle_RefCodCobranza[$i]',
						RefSecuencia = '$detalle_RefSecuencia[$i]',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	-----------------
		mysql_query("COMMIT");
	}
	//	aprobar
	elseif ($accion == "aprobar") {
		mysql_query("BEGIN");
		##	-----------------
		##	actualizo
		$sql = "UPDATE co_cierrecaja
				SET
					AprobadoPor = '$AprobadoPor',
					FechaAprobado = '$FechaAprobado',
					Estado = 'AP',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodCierre = '$CodCierre'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	anular
	elseif ($accion == "anular") {
		mysql_query("BEGIN");
		##	-----------------
		$field = getRecord("SELECT * FROM co_cierrecaja WHERE CodCierre = '$CodCierre'");
		##	
		if ($field['Estado'] != 'PR') die('No puede anular un cierre <strong>'.printValores('cierre-caja-estado',$field['Estado']).'</strong>');
		##	actualizo
		$sql = "UPDATE co_cierrecaja
				SET
					Estado = 'AN',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodCierre = '$CodCierre'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
}
elseif ($modulo == "validar") {
	//	modificar
	if($accion == "modificar") {
		$sql = "SELECT Estado FROM co_cierrecaja WHERE CodCierre = '$codigo'";
		$Estado = getVar3($sql);
		if ($Estado != 'PR') die('No puede modificar un cierre <strong>'.printValores('cierre-caja-estado',$Estado).'</strong>');
	}
	//	aprobar
	elseif($accion == "aprobar") {
		$sql = "SELECT Estado FROM co_cierrecaja WHERE CodCierre = '$codigo'";
		$Estado = getVar3($sql);
		if ($Estado != 'PR') die('No puede aprobar un cierre <strong>'.printValores('cierre-caja-estado',$Estado).'</strong>');
	}
	//	anular
	elseif($accion == "anular") {
		$sql = "SELECT Estado FROM co_cierrecaja WHERE CodCierre = '$codigo'";
		$Estado = getVar3($sql);
		if ($Estado != 'PR') die('No puede anular un cierre <strong>'.printValores('cierre-caja-estado',$Estado).'</strong>');
	}
}
elseif ($modulo == "ajax") {
	if ($accion == "detalle_insertar") {
		$id = $nro_detalle;
		?>
		<tr class="trListaBody" onclick="clk($(this), 'detalle', 'detalle_<?=$id?>');" id="detalle_<?=$id?>">
			<th>
				<input type="hidden" name="detalle_Secuencia[]" id="detalle_Secuencia<?=$id?>" value="0">
				<?=$nro_detalle?>
			</th>
			<td>
				<select name="detalle_CodTipoPago[]" class="cell" onchange="setMontosCierre();">
					<option value="">&nbsp;</option>
					<?=loadSelect2('co_tipopago','CodTipoPago','Descripcion',$f['CodTipoPago'])?>
				</select>
			</td>
			<td>
				<input type="hidden" name="detalle_TipoConcepto[]" id="detalle_TipoConcepto<?=$id?>" value="">
				<select name="detalle_CodConceptoCaja[]" class="cell">
					<option value="">&nbsp;</option>
					<?=co_conceptocaja($f['CodConceptoCaja'])?>
				</select>
			</td>
			<td>
				<select name="detalle_MonedaDocumento[]" class="cell">
					<?=loadSelectGeneral("monedas", 'L')?>
				</select>
			</td>
			<td>
				<input type="text" name="detalle_MontoLocal[]" value="0,00" class="cell currency" style="text-align:right;" onchange="setMontosCierre();">
			</td>
			<td>
				<input type="text" name="detalle_MontoOriginal[]" value="0,00" class="cell2" style="text-align:right;" readonly>
			</td>
			<td>
				<input type="text" name="detalle_Comentarios[]" value="<?=$f['Comentarios']?>" class="cell">
			</td>
			<td>
				<select name="detalle_CodTipoDocumento[]" class="cell2">
					<option value="">&nbsp;</option>
					<?=loadSelect2('co_tipodocumento','CodTipoDocumento','Descripcion',$f['CodTipoDocumento'],10)?>
				</select>
			</td>
			<td>
				<input type="hidden" name="detalle_CodDocumento[]" value="">
				<input type="text" name="detalle_NroDocumento[]" value="" class="cell" style="text-align:center;" maxlength="10">
			</td>
			<td>
				<input type="text" name="detalle_CodPersonaCliente[]" value="" class="cell2" style="text-align:center;" readonly>
			</td>
			<td>
				<input type="text" name="detalle_NomPersonaCliente[]" value="" class="cell2" disabled>
			</td>
			<td>
				<input type="text" name="detalle_MontoAfecto[]" value="0,00" class="cell2" style="text-align:right;" readonly>
			</td>
			<td>
				<input type="text" name="detalle_MontoImpuesto[]" value="0,00" class="cell2" style="text-align:right;" readonly>
			</td>
			<td>
				<input type="hidden" name="detalle_RefCodCobranza[]" value="">
				<input type="text" name="detalle_RefCobranza[]" value="" class="cell2" readonly="readonly">
			</td>
			<td>
				<input type="text" name="detalle_RefSecuencia[]" value="" class="cell2" style="text-align:center;" readonly="readonly">
			</td>
		</tr>
		<?php
	}
	elseif ($accion == "cobranza_insertar") {
		$FechaCobranza = formatFechaAMD($FechaCierre);
		$sql = "SELECT
					cod.*,
					co.NroCobranza AS RefCobranza,
					doco.CodDocumento,
					do.NroDocumento,
					do.CodTipoDocumento,
					do.CodPersonaCliente,
					do.NombreCliente
				FROM co_cobranzadet cod
				INNER JOIN co_cobranza co ON co.CodCobranza = cod.CodCobranza
				LEFT JOIN co_documentocobranza doco ON doco.CodCobranza = co.CodCobranza
				LEFT JOIN co_documento do ON do.CodDocumento = doco.CodDocumento
				WHERE
					cod.Estado = 'AP'
					AND co.FechaCobranza = '$FechaCobranza'";
		$field = getRecords($sql);
		foreach ($field as $f)
		{
			$id = ++$nro_detalle;
			?>
			<tr class="trListaBody" onclick="clk($(this), 'detalle', 'detalle_<?=$id?>');" id="detalle_<?=$id?>">
				<th>
					<input type="hidden" name="detalle_Secuencia[]" id="detalle_Secuencia<?=$id?>" value="0">
					<?=$nro_detalle?>
				</th>
				<td>
					<input type="hidden" name="detalle_TipoConcepto[]" id="detalle_TipoConcepto<?=$id?>" value="">
					<select name="detalle_CodTipoPago[]" class="cell" onchange="setMontosCierre();">
						<?=loadSelect2('co_tipopago','CodTipoPago','Descripcion',$f['CodTipoPago'],1)?>
					</select>
				</td>
				<td>
					<select name="detalle_CodConceptoCaja[]" class="cell">
						<?=co_conceptocaja($f['CodConceptoCaja'])?>
					</select>
				</td>
				<td>
					<select name="detalle_MonedaDocumento[]" class="cell">
						<?=loadSelectGeneral("monedas", $f['MonedaDocumento'])?>
					</select>
				</td>
				<td>
					<input type="text" name="detalle_MontoLocal[]" value="<?=number_format($f['MontoLocal'],2,',','.')?>" class="cell currency" style="text-align:right;" onchange="setMontosCierre();">
				</td>
				<td>
					<input type="text" name="detalle_MontoOriginal[]" value="<?=number_format($f['MontoLocal'],2,',','.')?>" class="cell2" style="text-align:right;" readonly>
				</td>
				<td>
					<input type="text" name="detalle_Comentarios[]" value="<?=$f['Comentarios']?>" class="cell">
				</td>
				<td>
					<select name="detalle_CodTipoDocumento[]" class="cell2">
						<?=loadSelect2('co_tipodocumento','CodTipoDocumento','Descripcion',$f['CodTipoDocumento'],11)?>
					</select>
				</td>
				<td>
					<input type="hidden" name="detalle_CodDocumento[]" value="<?=$f['CodDocumento']?>">
					<input type="text" name="detalle_NroDocumento[]" value="<?=$f['NroDocumento']?>" class="cell" style="text-align:center;" maxlength="10" readonly>
				</td>
				<td>
					<input type="text" name="detalle_CodPersonaCliente[]" value="<?=$f['CodPersonaCliente']?>" class="cell2" style="text-align:center;" readonly>
				</td>
				<td>
					<input type="text" name="detalle_NomPersonaCliente[]" value="<?=$f['NombreCliente']?>" class="cell2" disabled>
				</td>
				<td>
					<input type="text" name="detalle_MontoAfecto[]" value="<?=number_format($f['MontoAfecto'],2,',','.')?>" class="cell2" style="text-align:right;" readonly>
				</td>
				<td>
					<input type="text" name="detalle_MontoImpuesto[]" value="<?=number_format($f['MontoImpuesto'],2,',','.')?>" class="cell2" style="text-align:right;" readonly>
				</td>
				<td>
					<input type="hidden" name="detalle_RefCodCobranza[]" value="<?=$f['CodCobranza']?>">
					<input type="text" name="detalle_RefCobranza[]" value="<?=$f['RefCobranza']?>" class="cell2" style="text-align:center;" readonly="readonly">
				</td>
				<td>
					<input type="text" name="detalle_RefSecuencia[]" value="<?=$f['Secuencia']?>" class="cell2" style="text-align:center;" readonly="readonly">
				</td>
			</tr>
			<?php
		}
	}
	elseif ($accion == "fondo_caja") {
		$sql = "SELECT * FROM co_fondocaja WHERE CodPersonaCajero = '$CodPersonaCajero'";
		$field = getRecord($sql);

		echo floatval($field['Monto']);
	}
}
?>