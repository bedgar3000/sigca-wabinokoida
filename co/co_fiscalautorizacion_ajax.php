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
		$FechaDocumento = formatFechaAMD($FechaDocumento);
		##	valido
		if (!trim($CodOrganismo) || !trim($NroAutorizacion) || !trim($FechaDocumento) || !trim($NroOrden)) die("Debe llenar los campos (*) obligatorios.");
		##	codigo
		$CodAutorizacion = codigo('co_fiscalautorizacion','CodAutorizacion',6);
		##	inserto
		$sql = "INSERT INTO co_fiscalautorizacion
				SET
					CodAutorizacion = '$CodAutorizacion',
					CodOrganismo = '$CodOrganismo',
					NroAutorizacion = '$NroAutorizacion',
					FechaDocumento = '$FechaDocumento',
					NroOrden = '$NroOrden',
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()";
		execute($sql);
		##	detalle
		$Secuencia = 0;
		for ($i=0; $i < count($detalle_Secuencia); $i++)
		{
			++$Secuencia;
			##	valido
			if (!trim($detalle_CodTipoDocumento[$i])) die("Debe seleccionar un Tipo de Documento.");
			elseif (!trim($detalle_CodSerie[$i])) die("Debe seleccionar una Serie.");
			##	inserto
			$sql = "INSERT INTO co_fiscalautorizaciondet
					SET
						CodAutorizacion = '$CodAutorizacion',
						Secuencia = '$Secuencia',
						CodOrganismo = '$CodOrganismo',
						CodTipoDocumento = '$detalle_CodTipoDocumento[$i]',
						CodSerie = '$detalle_CodSerie[$i]',
						NroDesde = '$detalle_NroDesde[$i]',
						NroHasta = '$detalle_NroHasta[$i]',
						UltNroEmitido = '$detalle_UltNroEmitido[$i]',
						Estado = '$Estado',
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
		$field = getRecord("SELECT * FROM co_fiscalautorizacion WHERE CodAutorizacion = '$CodAutorizacion'");
		$FechaDocumento = formatFechaAMD($FechaDocumento);
		##	valido
		if (!trim($NroAutorizacion) || !trim($FechaDocumento) || !trim($NroOrden)) die("Debe llenar los campos (*) obligatorios.");
		##	actualizo
		$sql = "UPDATE co_fiscalautorizacion
				SET
					NroAutorizacion = '$NroAutorizacion',
					FechaDocumento = '$FechaDocumento',
					NroOrden = '$NroOrden',
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodAutorizacion = '$CodAutorizacion'";
		execute($sql);
		##	detalle
		if (count($detalle_Secuencia))
		{
			$sql = "DELETE FROM co_fiscalautorizaciondet
					WHERE
						CodAutorizacion = '$CodAutorizacion'
						AND Secuencia NOT IN (".implode(",",$detalle_Secuencia).")";
		}
		else
		{
			$sql = "DELETE FROM co_fiscalautorizaciondet WHERE CodAutorizacion = '$CodAutorizacion'";
		}
		execute($sql);
		$Secuencia = 0;
		for ($i=0; $i < count($detalle_Secuencia); $i++)
		{
			if (!$detalle_Secuencia[$i]) 
				$detalle_Secuencia[$i] = codigo('co_fiscalautorizaciondet','Secuencia',11,['CodAutorizacion'],[$CodAutorizacion]);
			##	valido
			if (!trim($detalle_CodTipoDocumento[$i])) die("Debe seleccionar un Tipo de Documento.");
			elseif (!trim($detalle_CodSerie[$i])) die("Debe seleccionar una Serie.");
			##	inserto
			$sql = "REPLACE INTO co_fiscalautorizaciondet
					SET
						CodAutorizacion = '$CodAutorizacion',
						Secuencia = '$detalle_Secuencia[$i]',
						CodOrganismo = '$field[CodOrganismo]',
						CodTipoDocumento = '$detalle_CodTipoDocumento[$i]',
						CodSerie = '$detalle_CodSerie[$i]',
						NroDesde = '$detalle_NroDesde[$i]',
						NroHasta = '$detalle_NroHasta[$i]',
						UltNroEmitido = '$detalle_UltNroEmitido[$i]',
						Estado = '$detalle_Estado[$i]',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	-----------------
		mysql_query("COMMIT");
	}
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "DELETE FROM co_fiscalautorizacion WHERE CodAutorizacion = '$registro'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
elseif ($modulo == "ajax") {
	if ($accion == "detalle_insertar") {
		$id = $nro_detalle;
		?>
		<tr class="trListaBody" onclick="clk($(this), 'detalle', 'detalle_<?=$id?>');" id="detalle_<?=$id?>">
			<th>
				<input type="hidden" name="detalle_Secuencia[]" id="detalle_Secuencia<?=$id?>" value="0">
				<?=$id?>
			</th>
            <td>
                <select name="detalle_CodTipoDocumento[]" class="cell">
                	<option value="">&nbsp;</option>
	                <?=loadSelect2('co_tipodocumento','CodTipoDocumento','Descripcion')?>
	            </select>
            </td>
            <td>
                <select name="detalle_CodSerie[]" class="cell">
                	<option value="">&nbsp;</option>
	                <?=loadSelect2('co_seriefiscal','CodSerie','NroSerie')?>
	            </select>
            </td>
			<td>
				<input type="text" name="detalle_NroDesde[]" value="0" class="cell">
			</td>
			<td>
				<input type="text" name="detalle_NroHasta[]" value="0" class="cell">
			</td>
			<td>
				<input type="text" name="detalle_UltiNroEmitido[]" value="0" class="cell">
			</td>
		</tr>
		<?php
	}
}
?>