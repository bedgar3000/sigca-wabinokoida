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
		##	valido
		if (!trim($Descripcion) || !trim($CodParroquia)) die("Debe llenar los campos (*) obligatorios.");
		##	codigo
		$CodRutaDespacho = codigo('co_rutadespacho','CodRutaDespacho',6);
		##	inserto
		$sql = "INSERT INTO co_rutadespacho
				SET
					CodRutaDespacho = '$CodRutaDespacho',
					Descripcion = '$Descripcion',
					CodParroquia = '$CodParroquia',
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
			if (!trim($detalle_CodPersona[$i])) die("Debe seleccionar un Cliente.");
			##	inserto
			$sql = "INSERT INTO co_rutadespachodet
					SET
						CodRutaDespacho = '$CodRutaDespacho',
						Secuencia = '$Secuencia',
						CodPersona = '$detalle_CodPersona[$i]',
						Direccion = '$detalle_Direccion[$i]',
						Estado = '$detalle_Estado[$i]',
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
		##	valido
		if (!trim($Descripcion) || !trim($CodParroquia)) die("Debe llenar los campos (*) obligatorios.");
		##	actualizo
		$sql = "UPDATE co_rutadespacho
				SET
					Descripcion = '$Descripcion',
					CodParroquia = '$CodParroquia',
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodRutaDespacho = '$CodRutaDespacho'";
		execute($sql);
		##	detalle
		if (count($detalle_Secuencia))
		{
			$sql = "DELETE FROM co_rutadespachodet
					WHERE
						CodRutaDespacho = '$CodRutaDespacho'
						AND Secuencia NOT IN (".implode(",",$detalle_Secuencia).")";
		}
		else
		{
			$sql = "DELETE FROM co_rutadespachodet WHERE CodRutaDespacho = '$CodRutaDespacho'";
		}
		execute($sql);
		$Secuencia = 0;
		for ($i=0; $i < count($detalle_Secuencia); $i++)
		{
			if (!$detalle_Secuencia[$i]) 
				$detalle_Secuencia[$i] = codigo('co_rutadespachodet','Secuencia',6,['CodRutaDespacho'],[$CodRutaDespacho]);
			##	valido
			if (!trim($detalle_CodPersona[$i])) die("Debe seleccionar un Cliente.");
			##	inserto
			$sql = "REPLACE INTO co_rutadespachodet
					SET
						CodRutaDespacho = '$CodRutaDespacho',
						Secuencia = '$detalle_Secuencia[$i]',
						CodPersona = '$detalle_CodPersona[$i]',
						Direccion = '$detalle_Direccion[$i]',
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
		$sql = "DELETE FROM co_rutadespacho WHERE CodRutaDespacho = '$registro'";
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
				<input type="hidden" name="detalle_CodPersona[]" id="detalle_CodPersona<?=$id?>" value="">
				<input type="text" name="detalle_NomPersona[]" id="detalle_NomPersona<?=$id?>" value="" class="cell2" readonly="readonly">
			</td>
			<td>
				<input type="text" name="detalle_Direccion[]" value="" class="cell">
			</td>
            <td>
                <select name="detalle_Estado[]" class="cell">
	                <?=loadSelectGeneral("ESTADO", 'A')?>
	            </select>
            </td>
		</tr>
		<?php
	}
}
?>