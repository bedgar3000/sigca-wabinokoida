<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion-".$AnioActual.$MesActual.$DiaActual."_".$HorasActual.$MinutosActual.$SegundosActual.".sql", "w+");
##############################################################################/
##	Categorias Programaticas (NUEVO, MODIFICAR, ELIMINAR, AJAX)
##############################################################################/
if ($modulo == "formulario") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		##	-----------------
		##	valido
		if (!trim($CategoriaProg) || !trim($CodOrganismo) || !trim($IdActividad) || !trim($CodUnidadEjec)) die("Debe llenar los campos (*) obligatorios.");
		##	inserto
		$sql = "INSERT INTO pv_categoriaprog
				SET
					CategoriaProg = '".$CategoriaProg."',
					IdActividad = '".$IdActividad."',
					CodOrganismo = '".$CodOrganismo."',
					CodUnidadEjec = '".$CodUnidadEjec."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	modificar
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		##	-----------------
		##	actualizo
		$sql = "UPDATE pv_categoriaprog
				SET
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CategoriaProg = '".$CategoriaProg."'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "DELETE FROM pv_categoriaprog WHERE CategoriaProg = '".$registro."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
elseif ($modulo == "ajax") {
	//	obtener dependencias x unidad ejecutora
	if ($accion == "getDependenciasxUnidadEjecutora") {
		$sql = "SELECT
					ued.CodDependencia,
					d.Dependencia
				FROM
					pv_unidadejecutoradep ued 
					INNER JOIN mastdependencias d ON (d.CodDependencia = ued.CodDependencia)
				WHERE ued.CodUnidadEjec = '".$CodUnidadEjec."'";
		$field = getRecords($sql);
		foreach ($field as $f) {
			?>
			<tr class="trListaBody">
				<td align="center" width="40"><?=$f['CodDependencia']?></td>
				<td><?=htmlentities($f['Dependencia'])?></td>
			</tr>
			<?php
		}
	}
	//	obterner categoría programática
	elseif($accion == "setCategoriaProg") {
		$sql = "SELECT CONCAT(ss.CodClaSectorial, pg.CodPrograma, sp.CodSubPrograma, py.CodProyecto, a.CodActividad, '$CodUnidadEjec') AS Codigo
				FROM pv_actividades a
				INNER JOIN pv_proyectos py ON (py.IdProyecto = a.IdProyecto)
				INNER JOIN pv_subprogramas sp ON (sp.IdSubPrograma = py.IdSubPrograma)
				INNER JOIN pv_programas pg ON (pg.IdPrograma = sp.IdPrograma)
				INNER JOIN pv_subsector ss ON (ss.IdSubSector = pg.IdSubSector)
				WHERE a.IdActividad = '$IdActividad'";
		if ($IdActividad && $CodUnidadEjec) $CategoriaProg = getVar3($sql); else $CategoriaProg = "";
		echo $CategoriaProg;
	}
	//	centro de costo
	elseif($accion == "setCentroCosto") {
		$sql = "SELECT
					ue.CodCentroCosto,
					cc.Descripcion AS NomCentroCosto,
					cc.Codigo AS CodigoCC
				FROM
					pv_unidadejecutora ue
					INNER JOIN ac_mastcentrocosto cc ON (cc.CodCentroCosto = ue.CodCentroCosto)
				WHERE CodUnidadEjec = '$CodUnidadEjec'";
		$field = getRecord($sql);
		if (count($field)) {
			$jsondata = [
				'CodCentroCosto' => $field['CodCentroCosto'],
				'NomCentroCosto' => $field['NomCentroCosto'],
				'CodigoCC' => $field['CodigoCC'],
			];
		} else {
			$jsondata = [
				'CodCentroCosto' => '',
				'NomCentroCosto' => '',
				'CodigoCC' => '',
			];
		}
        echo json_encode($jsondata);
        exit();
	}
}
?>