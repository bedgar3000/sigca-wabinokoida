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
		$iCodPersona = (!empty($CodPersona)?"'$CodPersona'":"NULL");
		##	valido
		if (!trim($Descripcion) || !trim($CodParroquia) || !trim($NroCirculo) || !trim($NroFamilias)) die("Debe llenar los campos (*) obligatorios.");
		##	codigo
		$CodComite = codigo('co_comitelocal','CodComite',6);
		##	inserto
		$sql = "INSERT INTO co_comitelocal
				SET
					CodComite = '$CodComite',
					Descripcion = '$Descripcion',
					Direccion = '$Direccion',
					CodPersona = $iCodPersona,
					CodParroquia = '$CodParroquia',
					NroCirculo = '$NroCirculo',
					NroFamilias = '$NroFamilias',
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
			if (!trim($detalle_CodPersona[$i])) die("Debe seleccionar una Persona.");
			##	inserto
			$sql = "INSERT INTO co_comitelocaldet
					SET
						CodComite = '$CodComite',
						Secuencia = '$Secuencia',
						CodPersona = '$detalle_CodPersona[$i]',
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
		if (!trim($Descripcion) || !trim($CodParroquia) || !trim($NroCirculo) || !trim($NroFamilias)) die("Debe llenar los campos (*) obligatorios.");
		##	actualizo
		$sql = "UPDATE co_comitelocal
				SET
					Descripcion = '$Descripcion',
					Direccion = '$Direccion',
					CodPersona = '$CodPersona',
					CodParroquia = '$CodParroquia',
					NroCirculo = '$NroCirculo',
					NroFamilias = '$NroFamilias',
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodComite = '$CodComite'";
		execute($sql);
		##	detalle
		if (count($detalle_Secuencia))
		{
			$sql = "DELETE FROM co_comitelocaldet
					WHERE
						CodComite = '$CodComite'
						AND Secuencia NOT IN (".implode(",",$detalle_Secuencia).")";
		}
		else
		{
			$sql = "DELETE FROM co_comitelocaldet WHERE CodComite = '$CodComite'";
		}
		execute($sql);
		$Secuencia = 0;
		for ($i=0; $i < count($detalle_Secuencia); $i++)
		{
			if (!$detalle_Secuencia[$i]) 
				$detalle_Secuencia[$i] = codigo('co_comitelocaldet','Secuencia',6,['CodComite'],[$CodComite]);
			##	valido
			if (!trim($detalle_CodPersona[$i])) die("Debe seleccionar una Persona.");
			##	inserto
			$sql = "REPLACE INTO co_comitelocaldet
					SET
						CodComite = '$CodComite',
						Secuencia = '$detalle_Secuencia[$i]',
						CodPersona = '$detalle_CodPersona[$i]',
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
		$sql = "DELETE FROM co_comitelocal WHERE CodComite = '$registro'";
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
				<input type="text" name="detalle_DocPersona[]" id="detalle_DocPersona<?=$id?>" value="" class="cell">
			</td>
			<td>
				<input type="hidden" name="detalle_CodPersona[]" id="detalle_CodPersona<?=$id?>" value="">
				<input type="text" name="detalle_NomPersona[]" id="detalle_NomPersona<?=$id?>" value="" class="cell2" readonly="readonly">
			</td>
			<td>
				<input type="text" name="detalle_DirPersona[]" id="detalle_DirPersona<?=$id?>" value="" class="cell">
			</td>
			<td>
				<input type="text" name="detalle_TelPersona[]" id="detalle_TelPersona<?=$id?>" value="" class="cell">
			</td>
			<td>
				<input type="text" name="detalle_CelPersona[]" id="detalle_CelPersona<?=$id?>" value="" class="cell">
			</td>
		</tr>
		<?php
	}
}
?>