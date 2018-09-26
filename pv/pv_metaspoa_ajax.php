<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion-".$AnioActual.$MesActual.$DiaActual."_".$HorasActual.$MinutosActual.$SegundosActual.".sql", "w+");
##############################################################################/
##	Metas (NUEVO, MODIFICAR, ELIMINAR, AJAX)
##############################################################################/
if ($modulo == "formulario") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		##	-----------------
		##	valido
		if (!trim($CodObjetivo) || !trim($Descripcion)) die("Debe llenar los campos (*) obligatorios.");
		##	codigo
		$CodMeta = codigo('pv_metaspoa','CodMeta',6);
		$NroMeta = codigo('pv_metaspoa','NroMeta',6,['CodObjetivo'],[$CodObjetivo]);
		##	inserto
		$sql = "INSERT INTO pv_metaspoa
				SET
					CodMeta = '".$CodMeta."',
					CodObjetivo = '".$CodObjetivo."',
					NroMeta = '".$NroMeta."',
					Descripcion = '".$Descripcion."',
					MedioVerificacion1 = '".$MedioVerificacion1."',
					MedioVerificacion2 = '".$MedioVerificacion2."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		##	indicadores
		$Secuencia=0;
		for ($i=0; $i < count($indicadores_Descripcion); $i++) {
			$sql = "INSERT INTO pv_metaspoaindicadores
					SET
						CodMeta = '".$CodMeta."',
						Secuencia = '".++$Secuencia."',
						Descripcion = '".$indicadores_Descripcion[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
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
		if (!trim($Descripcion)) die("Debe llenar los campos (*) obligatorios.");
		##	actualizo
		$sql = "UPDATE pv_metaspoa
				SET
					Descripcion = '".$Descripcion."',
					MedioVerificacion1 = '".$MedioVerificacion1."',
					MedioVerificacion2 = '".$MedioVerificacion2."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodMeta = '".$CodMeta."'";
		execute($sql);
		##	indicadores
		execute("DELETE FROM pv_metaspoaindicadores WHERE CodMeta = '$CodMeta'");
		$Secuencia=0;
		for ($i=0; $i < count($indicadores_Descripcion); $i++) {
			$sql = "INSERT INTO pv_metaspoaindicadores
					SET
						CodMeta = '".$CodMeta."',
						Secuencia = '".++$Secuencia."',
						Descripcion = '".$indicadores_Descripcion[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
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
		$sql = "DELETE FROM pv_metaspoa WHERE CodMeta = '".$registro."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
elseif($modulo == "ajax") {
	//	obtener objetivos x categoria programática
	if ($accion == "getObjetivos") {
		?><option value="">&nbsp;</option><?php
		$sql = "SELECT * FROM pv_objetivospoa WHERE CategoriaProg = '$CategoriaProg'";
		$field = getRecords($sql);
		foreach ($field as $f) {
			?><option value="<?=$f['CodObjetivo']?>"><?=$f['NroObjetivo']?></option><?php
		}
	}
	//	insertar linea
	elseif ($accion == "insertar_indicadores") {
		$detalle = "indicadores";
		$id = $nro_detalle;
		?>
		<tr class="trListaBody" id="<?=$detalle?>_<?=$id?>" onclick="clk($(this), '<?=$detalle?>', '<?=$detalle?>_<?=$id?>');">
			<th width="15"><?=$id?></th>
			<td><input type="text" name="<?=$detalle?>_Descripcion[]" class="cell" /></td>
		</tr>
		<?php
	}
}
?>