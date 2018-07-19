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
		$FechaCobranza = formatFechaAMD($FechaCobranza);
		$TotalCobranza = setNumero($TotalCobranza);
		$MontoCobrado = setNumero($MontoCobrado);
		$iCodPersonaCajero = (!empty($CodPersonaCajero)?"CodPersonaCajero = '$CodPersonaCajero',":'');
		$iCodPersonaCobrador = (!empty($CodPersonaCobrador)?"CodPersonaCobrador = '$CodPersonaCobrador',":'');
		//if ($_PARAMETRO['COBSTATAP'] <> 'S') $Estado = 'AP'; else $Estado = 'PR';
		$Estado = 'PR';
		##	valido
		if (!trim($CodOrganismo)) die("Debe seleccionar un Organismo.");
		elseif (!trim($CodPersonaCliente)) die("Debe seleccionar un Cliente.");
		elseif (!trim($FechaCobranza)) die("La Fecha de Cobranza es obligatoria.");
		elseif ($TotalCobranza <> $MontoCobrado) die("Monto Cobranza no puede ser distinto al Monto Cobrado de los documentos");
		##	codigo
		$CodCobranza = codigo('co_cobranza','CodCobranza',10);
		$NroCobranza = codigo('co_cobranza','NroCobranza',5,['CodOrganismo'],[$CodOrganismo]);
		##	cobranza
		$sql = "INSERT INTO co_cobranza
				SET
					CodCobranza = '$CodCobranza',
					CodOrganismo = '$CodOrganismo',
					NroCobranza = '$NroCobranza',
					FechaCobranza = '$FechaCobranza',
					CodPersonaCliente = '$CodPersonaCliente',
					$iCodPersonaCajero
					$iCodPersonaCobrador
					FechaPreparado = NOW(),
					PreparadoPor = '$_SESSION[CODPERSONA_ACTUAL]',
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()";
		execute($sql);
		##	documentos
		$Secuencia = 0;
		for ($i=0; $i < count($documento_CodDocumento); $i++)
		{
			++$Secuencia;
			$documento_MontoCobrado[$i] = setNumero($documento_MontoCobrado[$i]);
			##	documento
			$sql = "SELECT * FROM co_documento WHERE CodDocumento = '$documento_CodDocumento[$i]'";
			$field_documento = getRecord($sql);
			##	
			$sql = "SELECT * FROM co_documentodet WHERE CodDocumento = '$documento_CodDocumento[$i]'";
			$field_documentodet = getRecord($sql);
			##	inserto
			$sql = "INSERT INTO co_documentocobranza
					SET
						CodCobranza = '$CodCobranza',
						CodDocumento = '$documento_CodDocumento[$i]',
						MontoPagado = '$documento_MontoCobrado[$i]',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	cobranzas
		$Secuencia = 0;
		for ($i=0; $i < count($cobranza_Secuencia); $i++)
		{
			++$Secuencia;
			$cobranza_MontoLocal[$i] = setNumero($cobranza_MontoLocal[$i]);
			$iCodTipoTarjeta = (!empty($cobranza_CodTipoTarjeta[$i])?"CodTipoTarjeta = '$cobranza_CodTipoTarjeta[$i]',":'');
			$iCodBanco = (!empty($cobranza_CodBanco[$i])?"CodBanco = '$cobranza_CodBanco[$i]',":'');
			$iCtaBancaria = (!empty($cobranza_CtaBancaria[$i])?"CtaBancaria = '$cobranza_CtaBancaria[$i]',":'');
			$iCtaBancariaPropia = (!empty($cobranza_CtaBancariaPropia[$i])?"CtaBancariaPropia = '$cobranza_CtaBancariaPropia[$i]',":'');
			##	inserto
			$sql = "INSERT INTO co_cobranzadet
					SET
						CodCobranza = '$CodCobranza',
						Secuencia = '$Secuencia',
						CodTipoPago = '$cobranza_CodTipoPago[$i]',
						$iCodTipoTarjeta
						$iCodBanco
						MonedaDocumento = '$cobranza_MonedaDocumento[$i]',
						MontoLocal = '$cobranza_MontoLocal[$i]',
						DocReferencia = '$cobranza_DocReferencia[$i]',
						$iCtaBancaria
						$iCtaBancariaPropia
						Estado = '$Estado',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()";
			execute($sql);
		}
		if ($_PARAMETRO['COBSTATAP'] <> 'S')
		{
			$_POST['CodCobranza'] = $CodCobranza;
			$_POST['Estado'] = 'AP';
			cobranza_aprobar();
		}
		##	-----------------
		mysql_query("COMMIT");
	}
	//	modificar
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		##	-----------------
		$FechaCobranza = formatFechaAMD($FechaCobranza);
		$TotalCobranza = setNumero($TotalCobranza);
		$MontoCobrado = setNumero($MontoCobrado);
		$iCodPersonaCajero = (!empty($CodPersonaCajero)?"CodPersonaCajero = '$CodPersonaCajero',":'');
		$iCodPersonaCobrador = (!empty($CodPersonaCobrador)?"CodPersonaCobrador = '$CodPersonaCobrador',":'');
		##	valido
		if (!trim($CodOrganismo)) die("Debe seleccionar un Organismo.");
		elseif (!trim($FechaCobranza)) die("La Fecha de Cobranza es obligatoria.");
		elseif ($TotalCobranza <> $MontoCobrado) die("Monto Cobranza no puede ser distinto al Monto Cobrado de los documentos");
		##	codigo
		$CodCobranza = codigo('co_cobranza','CodCobranza',10);
		$NroCobranza = codigo('co_cobranza','NroCobranza',5,['CodOrganismo'],[$CodOrganismo]);
		##	cobranza
		$sql = "UPDATE co_cobranza
				SET
					FechaCobranza = '$FechaCobranza',
					$iCodPersonaCajero
					$iCodPersonaCobrador
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodCobranza = '$CodCobranza'";
		execute($sql);
		##	documentos
		$Secuencia = 0;
		for ($i=0; $i < count($documento_CodDocumento); $i++)
		{
			++$Secuencia;
			$documento_MontoCobrado[$i] = setNumero($documento_MontoCobrado[$i]);
			##	documento
			$sql = "SELECT * FROM co_documento WHERE CodDocumento = '$documento_CodDocumento[$i]'";
			$field_documento = getRecord($sql);
			##	
			$sql = "SELECT * FROM co_documentodet WHERE CodDocumento = '$documento_CodDocumento[$i]'";
			$field_documentodet = getRecord($sql);
			##	inserto
			$sql = "REPLACE INTO co_documentocobranza
					SET
						CodCobranza = '$CodCobranza',
						CodDocumento = '$documento_CodDocumento[$i]',
						MontoPagado = '$documento_MontoCobrado[$i]',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	cobranzas
		$sql = "DELETE FROM co_cobranzadet WHERE CodCobranza = '$CodCobranza'";
		execute($sql);
		$Secuencia = 0;
		for ($i=0; $i < count($cobranza_Secuencia); $i++)
		{
			++$Secuencia;
			$cobranza_MontoLocal[$i] = setNumero($cobranza_MontoLocal[$i]);
			$iCodTipoTarjeta = (!empty($cobranza_CodTipoTarjeta[$i])?"CodTipoTarjeta = '$cobranza_CodTipoTarjeta[$i]',":'');
			$iCodBanco = (!empty($cobranza_CodBanco[$i])?"CodBanco = '$cobranza_CodBanco[$i]',":'');
			$iCtaBancaria = (!empty($cobranza_CtaBancaria[$i])?"CtaBancaria = '$cobranza_CtaBancaria[$i]',":'');
			$iCtaBancariaPropia = (!empty($cobranza_CtaBancariaPropia[$i])?"CtaBancariaPropia = '$cobranza_CtaBancariaPropia[$i]',":'');
			##	inserto
			$sql = "INSERT INTO co_cobranzadet
					SET
						CodCobranza = '$CodCobranza',
						Secuencia = '$Secuencia',
						CodTipoPago = '$cobranza_CodTipoPago[$i]',
						$iCodTipoTarjeta
						$iCodBanco
						MonedaDocumento = '$cobranza_MonedaDocumento[$i]',
						MontoLocal = '$cobranza_MontoLocal[$i]',
						DocReferencia = '$cobranza_DocReferencia[$i]',
						$iCtaBancaria
						$iCtaBancariaPropia
						Estado = '$Estado',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	-----------------
		mysql_query("COMMIT");
	}
	//	aprobar
	elseif ($accion == "aprobar") {
		cobranza_aprobar();
	}
}
elseif ($modulo == "validar") {
	//	modificar
	if($accion == "modificar") {
		$sql = "SELECT Estado FROM co_cobranza WHERE CodCobranza = '$codigo'";
		$Estado = getVar3($sql);
		if ($Estado != 'PR') die('No puede modificar una cobranza <strong>'.printValores('cobranza-estado',$Estado).'</strong>');
	}
	//	aprobar
	if($accion == "aprobar") {
		$sql = "SELECT Estado FROM co_cobranza WHERE CodCobranza = '$codigo'";
		$Estado = getVar3($sql);
		if ($Estado != 'PR') die('No puede aprobar una cobranza <strong>'.printValores('cobranza-estado',$Estado).'</strong>');
	}
	//	anular
	elseif($accion == "anular") {
		$sql = "SELECT Estado FROM co_cobranza WHERE CodCobranza = '$codigo'";
		$Estado = getVar3($sql);
		if ($Estado != 'PR') die('No puede anular una cobranza <strong>'.printValores('cobranza-estado',$Estado).'</strong>');
	}
	//	generar pedido
	elseif ($accion == "generar") {
		$status = 'success';
		$items = [];
		$filtro_detalle = '';
		foreach ($registros as $row)
		{
			list($CodCobranza, $Secuencia) = explode('_', $row);

			$sql = "SELECT
						cd.CantidadPedida,
						i.CodInterno,
						cd.Descripcion,
						COALESCE(iai.StockActual,0) AS StockActual
					FROM co_cotizaciondet cd
					INNER JOIN lg_itemmast i ON i.CodItem = cd.CodItem
					LEFT JOIN lg_itemalmaceninv iai ON iai.CodItem = i.CodItem
					WHERE
						cd.CodCobranza = '$CodCobranza'
						AND cd.Secuencia = '$Secuencia'
						AND cd.TipoDetalle = 'I'";
			$field_detalle = getRecord($sql);
			if ($field_detalle) 
			{
				if ($field_detalle['StockActual'] < $field_detalle['CantidadPedida']) 
				{
					$items[] = '<p style="font-weight:bold;">' . $field_detalle['CodInterno'] . ' - ' . $field_detalle['Descripcion'] . '</p>';
					$status = 'error';
				}
			}
		}
		die(json_encode([
    		'status' => $status,
    		'message' => 'Se encontraron los siguientes items sin Stock: ' . implode('',$items),
    	]));
	}
}
elseif ($modulo == "ajax") {
	if ($accion == "documento_insertar") {
		$id = $CodDocumento;
		##	
		$sql = "SELECT
					do.*,
					td.Descripcion AS TipoDocumento,
					sf.CodSerie,
					sf.NroSerie,
					md1.Descripcion AS NomFormaFactura,
					md2.Descripcion AS NomTipoVenta
				FROM co_documento do
				INNER JOIN co_tipodocumento td ON td.CodTipoDocumento = do.CodTipoDocumento
				INNER JOIN co_establecimientofiscal ef ON ef.CodOrganismo = do.CodOrganismo
				INNER JOIN co_seriefiscal sf ON (
					sf.CodOrganismo = ef.CodOrganismo
					AND sf.CodEstablecimiento = ef.CodEstablecimiento
				)
				LEFT JOIN mastmiscelaneosdet md1 ON (
					md1.CodDetalle = do.FormaFactura
					AND md1.CodMaestro = 'FORMAFACT'
				)
				LEFT JOIN mastmiscelaneosdet md2 ON (
					md2.CodDetalle = do.TipoVenta
					AND md2.CodMaestro = 'TIPOVENTA'
				)
				WHERE do.CodDocumento = '$CodDocumento'";
		$field = getRecords($sql);
		foreach ($field as $f)
		{
			$MontoPendiente = $f['MontoTotal'] - $f['MontoPagado'];
			$MontoCobrado = $MontoPendiente;
			$MontoPorCobrar = $MontoPendiente - $MontoCobrado;
			?>
			<tr class="trListaBody" onclick="clk($(this), 'documento', 'documento_<?=$id?>');" id="documento_<?=$id?>">
				<td align="center">
					<input type="hidden" name="documento_CodDocumento[]" value="<?=$f['CodDocumento']?>">
					<?=$f['CodTipoDocumento']?>
				</td>
				<td align="center"><?=$f['NroSerie']?></td>
				<td align="center"><?=$f['NroDocumento']?></td>
				<td align="center"><?=$f['NroDocumento']?></td>
				<td align="center"><?=formatFechaDMA($f['FechaDocumento'])?></td>
				<td align="center"><?=formatFechaDMA($f['FechaVencimiento'])?></td>
				<td>
					<input type="text" name="documento_MontoTotal[]" value="<?=number_format($f['MontoTotal'],2,',','.')?>" class="cell2" style="text-align:right;" readonly="readonly">
				</td>
				<td>
					<input type="text" name="documento_MontoPendiente[]" value="<?=number_format($MontoPendiente,2,',','.')?>" class="cell2" style="text-align:right;" readonly="readonly">
				</td>
				<td>
					<input type="text" name="documento_MontoCobrado[]" value="<?=number_format($MontoCobrado,2,',','.')?>" class="cell2" style="text-align:right;" readonly="readonly">
				</td>
				<td>
					<input type="text" name="documento_MontoPorCobrar[]" value="<?=number_format($MontoPorCobrar,2,',','.')?>" class="cell2" style="text-align:right;" readonly="readonly">
				</td>
				<td align="center"><?=$f['NroDocumento']?></td>
				<td><?=htmlentities($f['Comentarios'])?></td>
			</tr>
			<?php
		}
	}
	elseif ($accion == "cobranza_insertar") {
		$id = $nro_detalle;
		?>
		<tr class="trListaBody" onclick="clk($(this), 'cobranza', 'cobranza_<?=$id?>');" id="cobranza_<?=$id?>">
			<th>
				<input type="hidden" name="cobranza_Secuencia[]" value="0">
				<?=$id?>
			</th>
			<td>
				<select name="cobranza_CodTipoPago[]" class="cell">
					<option value=''>&nbsp;</option>
					<?=loadSelect2('co_tipopago','CodTipoPago','Descripcion')?>
				</select>
			</td>
			<td>
				<select name="cobranza_MonedaDocumento[]" class="cell">
					<?=loadSelectGeneral("monedas",'L')?>
				</select>
			</td>
            <td>
				<input type="text" name="cobranza_MontoLocal[]" value="0,00" class="cell currency" style="text-align:right; font-weight: bold;" onchange="setMontosCobranza();">
            </td>
            <td>
				<input type="text" name="cobranza_DocReferencia[]" value="" class="cell" maxlength="30">
            </td>
            <td>
				<input type="text" name="cobranza_CtaBancaria[]" value="" class="cell" maxlength="20">
            </td>
			<td>
				<select name="cobranza_CodBanco[]" class="cell">
					<option value=''>&nbsp;</option>
					<?=loadSelect2('mastbancos','CodBanco','Banco')?>
				</select>
			</td>
			<td>
				<select name="cobranza_CodTipoTarjeta[]" class="cell">
					<option value=''>&nbsp;</option>
					<?=loadSelect2('co_tipotarjeta','CodTipoTarjeta','Descripcion')?>
				</select>
			</td>
            <td>
				<select name="cobranza_CtaBancariaPropia[]" class="cell">
					<option value=''>&nbsp;</option>
					<?=loadSelect2('ap_ctabancaria','NroCuenta','NroCuenta')?>
				</select>
            </td>
		</tr>
		<?php
	}
	elseif ($accion == "cobranza_efectivo_insertar") {
		$id = $nro_detalle;
		?>
		<tr class="trListaBody" onclick="clk($(this), 'cobranza', 'cobranza_<?=$id?>');" id="cobranza_<?=$id?>">
			<th>
				<input type="hidden" name="cobranza_Secuencia[]" value="0">
				<?=$id?>
			</th>
			<td>
				<select name="cobranza_CodTipoPago[]" class="cell">
					<?=loadSelect2('co_tipopago','CodTipoPago','Descripcion','EF',1)?>
				</select>
			</td>
			<td>
				<select name="cobranza_MonedaDocumento[]" class="cell">
					<?=loadSelectGeneral("monedas",'L')?>
				</select>
			</td>
            <td>
				<input type="text" name="cobranza_MontoLocal[]" value="<?=number_format($MontoRecibido,2,',','.')?>" class="cell currency" style="text-align:right; font-weight: bold;" readonly>
            </td>
            <td>
				<input type="text" name="cobranza_DocReferencia[]" value="" class="cell" maxlength="30" readonly>
            </td>
            <td>
				<input type="text" name="cobranza_CtaBancaria[]" value="" class="cell" maxlength="20" readonly>
            </td>
			<td>
				<select name="cobranza_CodBanco[]" class="cell">
					<option value=''>&nbsp;</option>
				</select>
			</td>
			<td>
				<select name="cobranza_CodTipoTarjeta[]" class="cell">
					<option value=''>&nbsp;</option>
				</select>
			</td>
            <td>
				<select name="cobranza_CtaBancariaPropia[]" class="cell">
					<option value=''>&nbsp;</option>
				</select>
            </td>
		</tr>
		<?php
		$id = ++$nro_detalle;
		?>
		<tr class="trListaBody" onclick="clk($(this), 'cobranza', 'cobranza_<?=$id?>');" id="cobranza_<?=$id?>">
			<th>
				<input type="hidden" name="cobranza_Secuencia[]" value="0">
				<?=$id?>
			</th>
			<td>
				<select name="cobranza_CodTipoPago[]" class="cell">
					<?=loadSelect2('co_tipopago','CodTipoPago','Descripcion','EF',1)?>
				</select>
			</td>
			<td>
				<select name="cobranza_MonedaDocumento[]" class="cell">
					<?=loadSelectGeneral("monedas",'L')?>
				</select>
			</td>
            <td>
				<input type="text" name="cobranza_MontoLocal[]" value="<?=number_format(($MontoPorCobrar-$MontoRecibido),2,',','.')?>" class="cell currency" style="text-align:right; font-weight: bold;" readonly>
            </td>
            <td>
				<input type="text" name="cobranza_DocReferencia[]" value="" class="cell" maxlength="30" readonly>
            </td>
            <td>
				<input type="text" name="cobranza_CtaBancaria[]" value="" class="cell" maxlength="20" readonly>
            </td>
			<td>
				<select name="cobranza_CodBanco[]" class="cell">
					<option value=''>&nbsp;</option>
				</select>
			</td>
			<td>
				<select name="cobranza_CodTipoTarjeta[]" class="cell">
					<option value=''>&nbsp;</option>
				</select>
			</td>
            <td>
				<select name="cobranza_CtaBancariaPropia[]" class="cell">
					<option value=''>&nbsp;</option>
				</select>
            </td>
		</tr>
		<?php
	}
	elseif ($accion == "adelanto_insertar") {
		$id = $nro_detalles;
		$sql = "SELECT * FROM co_documentoadelanto WHERE CodDocumento = '$CodDocumento'";
		$field = getRecords($sql);
		foreach ($field as $f)
		{
			?>
			<tr class="trListaBody" onclick="clk($(this), 'cobranza', 'cobranza_<?=$id?>');" id="cobranza_<?=$id?>">
				<th>
					<input type="hidden" name="cobranza_Secuencia[]" value="0">
					<?=$id?>
				</th>
				<td>
					<select name="cobranza_CodTipoPago[]" class="cell">
						<?=loadSelect2('co_tipopago','CodTipoPago','Descripcion','AD',1)?>
					</select>
				</td>
				<td>
					<select name="cobranza_MonedaDocumento[]" class="cell">
						<?=loadSelectGeneral("monedas",'L')?>
					</select>
				</td>
	            <td>
					<input type="text" name="cobranza_MontoLocal[]" value="<?=number_format($f['Monto'],2,',','.')?>" class="cell currency" style="text-align:right; font-weight: bold;" onchange="setMontosCobranza();">
	            </td>
	            <td>
					<input type="text" name="cobranza_DocReferencia[]" value="<?=$f['CodTipoDocRel']?><?=$f['NroDocRel']?>" class="cell" maxlength="30">
	            </td>
	            <td>
					<input type="text" name="cobranza_CtaBancaria[]" value="" class="cell" maxlength="20">
	            </td>
				<td>
					<select name="cobranza_CodBanco[]" class="cell">
						<option value=''>&nbsp;</option>
						<?=loadSelect2('mastbancos','CodBanco','Banco')?>
					</select>
				</td>
				<td>
					<select name="cobranza_CodTipoTarjeta[]" class="cell">
						<option value=''>&nbsp;</option>
						<?=loadSelect2('co_tipotarjeta','CodTipoTarjeta','Descripcion')?>
					</select>
				</td>
	            <td>
					<select name="cobranza_CtaBancariaPropia[]" class="cell">
						<option value=''>&nbsp;</option>
						<?=loadSelect2('ap_ctabancaria','NroCuenta','NroCuenta')?>
					</select>
	            </td>
			</tr>
			<?php
		}
	}
}

function cobranza_aprobar() {
	extract($_POST);
	extract($_GET);
	##	-----------------
	##	actualizo
	$sql = "UPDATE co_cobranza
			SET
				AprobadoPor = '$_SESSION[CODPERSONA_ACTUAL]',
				FechaAprobado = NOW(),
				Estado = '$Estado',
				UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
				UltimaFecha = NOW()
			WHERE CodCobranza = '$CodCobranza'";
	execute($sql);
	##	detalle
	$sql = "UPDATE co_cobranzadet
			SET
				Estado = '$Estado',
				UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
				UltimaFecha = NOW()
			WHERE CodCobranza = '$CodCobranza'";
	execute($sql);
	##	documentos
	$Linea = 0;
	$sql = "SELECT * FROM co_documentocobranza WHERE CodCobranza = '$CodCobranza'";
	$field_documentos = getRecords($sql);
	foreach ($field_documentos as $f)
	{
		++$Linea;
		$sql = "SELECT * FROM  co_documento WHERE CodDocumento = '$f[CodDocumento]'";
		$field_documento = getRecord($sql);
		##	
		$sql = "UPDATE co_documento
				SET
					MontoPagado = '$f[MontoPagado]',
					Estado = 'CO',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodDocumento = '$f[CodDocumento]'";
		execute($sql);
		##	
		if ($field_documento['CodTipoDocumento'] == 'AD')
		{
			$sql = "INSERT INTO co_documentoadelanto
					SET
						CodDocumento = '$field_documento[CodDocumento]',
						Linea = '$Linea',
						CodCobranza = '$CodCobranza',
						CodClienteRel = '$field_documento[CodPersonaCliente]',
						CodTipoDocRel = '$field_documento[CodTipoDocumento]',
						NroDocRel = '$field_documento[NroDocumento]',
						Monto = '$f[MontoPagado]',
						Estado = 'PE',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()";
			execute($sql);
		}
	}
}
?>