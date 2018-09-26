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
		if (!trim($CodClasificacion) || !trim($Descripcion) || !trim($Aplicacion)) die("Debe llenar los campos (*) obligatorios.");
		elseif (!is_unique('ap_clasificaciongastos','CodClasificacion',$CodClasificacion)) die("Código ya ingresado");
		##	inserto
		$sql = "INSERT INTO ap_clasificaciongastos
				SET
					CodClasificacion = '$CodClasificacion',
					Descripcion = '$Descripcion',
					Aplicacion = '$Aplicacion',
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()";
		execute($sql);
		##	detalle
		$Secuencia = 0;
		for ($i=0; $i < count($detalle_CodConceptoGasto); $i++)
		{
			++$Secuencia;
			##	inserto
			$sql = "INSERT INTO ap_conceptoclasificaciongastos
					SET
						CodClasificacion = '$CodClasificacion',
						CodConceptoGasto = '$detalle_CodConceptoGasto[$i]',
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
		if (!trim($Descripcion) || !trim($Aplicacion)) die("Debe llenar los campos (*) obligatorios.");
		##	actualizo
		$sql = "UPDATE ap_clasificaciongastos
				SET
					Descripcion = '$Descripcion',
					Aplicacion = '$Aplicacion',
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodClasificacion = '$CodClasificacion'";
		execute($sql);
		##	detalle
		if (count($detalle_CodConceptoGasto))
		{
			$sql = "DELETE FROM ap_conceptoclasificaciongastos
					WHERE
						CodClasificacion = '$CodClasificacion'
						AND CodConceptoGasto NOT IN (".implode(",",$detalle_CodConceptoGasto).")";
		}
		else
		{
			$sql = "DELETE FROM ap_conceptoclasificaciongastos WHERE CodClasificacion = '$CodClasificacion'";
		}
		execute($sql);
		$Secuencia = 0;
		for ($i=0; $i < count($detalle_CodConceptoGasto); $i++)
		{
			##	inserto
			$sql = "REPLACE INTO ap_conceptoclasificaciongastos
					SET
						CodClasificacion = '$CodClasificacion',
						CodConceptoGasto = '$detalle_CodConceptoGasto[$i]',
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
		$sql = "DELETE FROM ap_clasificaciongastos WHERE CodClasificacion = '$registro'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
elseif ($modulo == "ajax") {
	if ($accion == "detalle_insertar") {
		$sql = "SELECT *
				FROM ap_conceptogastos
				WHERE CodConceptoGasto = '$CodConceptoGasto'";
		$field = getRecords($sql);
		foreach ($field as $f)
		{
			$id = $f['CodConceptoGasto'];
			?>
			<tr class="trListaBody" onclick="clk($(this), 'detalle', 'detalle_<?=$id?>');" id="detalle_<?=$id?>">
				<th>
					<input type="hidden" name="detalle_CodConceptoGasto[]" value="<?=$f['CodConceptoGasto']?>">
					<?=$nro_detalles?>
				</th>
				<td>
					<?=htmlentities($f['Descripcion'])?>
				</td>
			</tr>
			<?php
		}
	}
}
?>