<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion-".$AnioActual.$MesActual.$DiaActual."_".$HorasActual.$MinutosActual.$SegundosActual.".sql", "w+");
//	$__archivo = fopen("_"."$modulo-$accion".".sql", "w+");
##############################################################################/
##	Proyección presupuestaria
##############################################################################/
if ($modulo == "formulario") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		##	-----------------
		##	valido
		if (!trim($CodOrganismo) || !trim($Ejercicio) || !trim($CategoriaProg) || !trim($Descripcion)) die("Debe llenar los campos (*) obligatorios.");
		##	codigo
		$Secuencia = codigo('pr_proypresupuestaria','Secuencia',2,['CodOrganismo','Ejercicio'],[$CodOrganismo,$Ejercicio]);
		$CodProyPresupuesto = $CodOrganismo.$Ejercicio.$Secuencia;
		##	inserto
		$sql = "INSERT INTO pr_proypresupuestaria
				SET
					CodProyPresupuesto = '".$CodProyPresupuesto."',
					CodOrganismo = '".$CodOrganismo."',
					Ejercicio = '".$Ejercicio."',
					Secuencia = '".intval($Secuencia)."',
					CategoriaProg = '".$CategoriaProg."',
					Descripcion = '".$Descripcion."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		##	detalle
		$Secuencia = 0;
		for ($i=0; $i < count($detalle_cod_partida); $i++) {
			$sql = "INSERT INTO pr_proypresupuestariadet
					SET
						CodProyPresupuesto = '".$CodProyPresupuesto."',
						Secuencia = '".++$Secuencia."',
						cod_partida = '".$detalle_cod_partida[$i]."',
						Descripcion = '".trim($detalle_Descripcion[$i])."',
						Monto = '".setNumero($detalle_Monto[$i])."',
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
		##	actualizo
		$sql = "UPDATE pr_proypresupuestaria
				SET
					Descripcion = '".$Descripcion."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodProyPresupuesto = '".$CodProyPresupuesto."'";
		execute($sql);
		##	detalle
		execute("DELETE FROM pr_proypresupuestariadet WHERE CodProyPresupuesto = '".$CodProyPresupuesto."'");
		$Secuencia = 0;
		for ($i=0; $i < count($detalle_cod_partida); $i++) {
			$sql = "INSERT INTO pr_proypresupuestariadet
					SET
						CodProyPresupuesto = '".$CodProyPresupuesto."',
						Secuencia = '".++$Secuencia."',
						cod_partida = '".$detalle_cod_partida[$i]."',
						Descripcion = '".trim($detalle_Descripcion[$i])."',
						Monto = '".setNumero($detalle_Monto[$i])."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
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
		$sql = "UPDATE pr_proypresupuestaria
				SET
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodProyPresupuesto = '".$CodProyPresupuesto."'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		$Estado = getVar3("SELECT Estado FROM pr_proypresupuestaria WHERE CodProyPresupuesto = '".$registro."'");
		if ($Estado != 'AP') die('No puede modificar una proyecci&oacute;n <strong>'.printValores('proypresupuestaria-estado',$Estado).'</strong>');
		//	elimino
		$sql = "DELETE FROM pr_proypresupuestaria WHERE CodProyPresupuesto = '".$registro."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
elseif ($modulo == "validar") {
	//	modificar
	if($accion == "modificar") {
		$sql = "SELECT Estado FROM pr_proypresupuestaria WHERE CodProyPresupuesto = '$codigo'";
		$Estado = getVar3($sql);
		if ($Estado != 'AP') die('No puede modificar una proyecci&oacute;n <strong>'.printValores('proypresupuestaria-estado',$Estado).'</strong>');
	}
	//	aprobar
	elseif($accion == "aprobar") {
		$sql = "SELECT Estado FROM pr_proypresupuestaria WHERE CodProyPresupuesto = '$codigo'";
		$Estado = getVar3($sql);
		if ($Estado != 'AP') die('No puede aprobar una proyecci&oacute;n <strong>'.printValores('proypresupuestaria-estado',$Estado).'</strong>');
	}
}
elseif($modulo == "ajax") {
	//	insertar partida
	if ($accion == "partida_insertar") {
		$sql = "SELECT * FROM pv_partida WHERE cod_partida = '$cod_partida'";
		$field = getRecords($sql);
		foreach ($field as $f) {
			$id = $f['cod_partida'];
			?>
			<tr class="trListaBody" id="detalle_<?=$id?>" onclick="clk($(this), 'detalle', 'detalle_<?=$id?>');">
				<td align="center">
					<input type="hidden" name="detalle_cod_partida[]" value="<?=$id?>" />
					<?=$f['cod_partida']?>
				</td>
				<td><input type="text" name="detalle_Descripcion[]" value="<?=htmlentities($f['denominacion'])?>" class="cell" readonly /></td>
				<td align="right"><input type="text" name="detalle_Monto[]" value="0,00" class="cell currency" style="text-align:right;" onchange="setMontos();" /></td>
			</tr>
			<?php
			
		}
	}
}
?>