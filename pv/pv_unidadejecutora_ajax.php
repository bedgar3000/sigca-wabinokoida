<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion-".$AnioActual.$MesActual.$DiaActual."_".$HorasActual.$MinutosActual.$SegundosActual.".sql", "w+");
##############################################################################/
##	Unidades Ejecutoras (NUEVO, MODIFICAR, ELIMINAR, AJAX)
##############################################################################/
if ($modulo == "formulario") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		##	-----------------
		##	valido
		if (!trim($Denominacion) || !trim($CodOrganismo) || !trim($CodCentroCosto)) die("Debe llenar los campos (*) obligatorios.");
		##	codigo
		$CodUnidadEjec = codigo('pv_unidadejecutora','CodUnidadEjec',3);
		##	inserto
		$sql = "INSERT INTO pv_unidadejecutora
				SET
					CodUnidadEjec = '".$CodUnidadEjec."',
					Denominacion = '".$Denominacion."',
					CodOrganismo = '".$CodOrganismo."',
					CodCentroCosto = '".$CodCentroCosto."',
					CodPersona = '".$CodPersona."',
					FlagEjecutorUnico = '".($FlagEjecutorUnico?'S':'N')."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		##	
		for ($i=0; $i < count($dep_CodDependencia); $i++) { 
			$sql = "INSERT INTO pv_unidadejecutoradep
					SET
						CodUnidadEjec = '".$CodUnidadEjec."',
						CodDependencia = '".$dep_CodDependencia[$i]."',
						Dependencia = '".$dep_Dependencia[$i]."'";
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
		if (!trim($Denominacion) || !trim($CodOrganismo) || !trim($CodCentroCosto)) die("Debe llenar los campos (*) obligatorios.");
		##	actualizo
		$sql = "UPDATE pv_unidadejecutora
				SET
					Denominacion = '".$Denominacion."',
					CodCentroCosto = '".$CodCentroCosto."',
					CodPersona = '".$CodPersona."',
					FlagEjecutorUnico = '".($FlagEjecutorUnico?'S':'N')."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodUnidadEjec = '".$CodUnidadEjec."'";
		execute($sql);
		##	
		$sql = "DELETE FROM pv_unidadejecutoradep WHERE CodUnidadEjec = '".$CodUnidadEjec."'";
		execute($sql);
		for ($i=0; $i < count($dep_CodDependencia); $i++) { 
			$sql = "INSERT INTO pv_unidadejecutoradep
					SET
						CodUnidadEjec = '".$CodUnidadEjec."',
						CodDependencia = '".$dep_CodDependencia[$i]."',
						Dependencia = '".$dep_Dependencia[$i]."'";
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
		$sql = "DELETE FROM pv_unidadejecutora WHERE CodUnidadEjec = '".$registro."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
elseif ($modulo == "ajax") {
	//	insertar linea
	if ($accion == "dep_insertar") {
		$sql = "SELECT * FROM mastdependencias WHERE CodDependencia = '".$CodDependencia."'";
		$field = getRecords($sql);
		foreach ($field as $f) {
			$id = $f['CodDependencia'];
			?>
			<tr class="trListaBody" onclick="clk($(this), 'dep', 'dep_<?=$id?>');" id="dep_<?=$id?>">
				<th>
					<input type="hidden" name="dep_CodDependencia[]" value="<?=$id?>" />
					<input type="hidden" name="dep_Dependencia[]" value="<?=$f['Dependencia']?>" />
					<?=$nro_detalles?>
				</th>
				<td align="center"><?=$f['CodDependencia']?></td>
				<td><?=htmlentities($f['Dependencia'])?></td>
			</tr>
			<?php
		}
	}
}
?>