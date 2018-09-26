<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion-".$AnioActual.$MesActual.$DiaActual."_".$HorasActual.$MinutosActual.$SegundosActual.".sql", "w+");
//	$__archivo = fopen("_"."$modulo-$accion".".sql", "w+");
##############################################################################/
##	Proyectos de Presupuesto (NUEVO, MODIFICAR, REVISAR, APROBAR, GENERAR, AJAX)
##############################################################################/
if ($modulo == "formulario") {
	//	nuevo
	if ($accion == "nuevo" || $accion == "generar-anteproyecto") {
		mysql_query("BEGIN");
		##	-----------------
		##	valido
		if (!trim($CodOrganismo) || !trim($CategoriaProg) || !trim($Ejercicio) || !trim($FechaInicio) || !trim($FechaFin)) die("Debe llenar los campos (*) obligatorios.");
		elseif (!is_numeric($Ejercicio) || strlen($Ejercicio) != 4) die("Formato <strong>Ejercicio</strong> incorrecto");
		elseif (!validateDate($FechaInicio,'d-m-Y')) die("Formato <strong>Fecha Inicio</strong> incorrecta");
		elseif (!validateDate($FechaFin,'d-m-Y')) die("Formato <strong>Fecha Fin</strong> incorrecta");
		##	codigo
		$CodProyPresupuesto = codigo('pv_proyectopresupuesto','CodProyPresupuesto',4,['CodOrganismo'],[$CodOrganismo]);
		##	inserto
		$sql = "INSERT INTO pv_proyectopresupuesto
				SET
					CodOrganismo = '".$CodOrganismo."',
					CodProyPresupuesto = '".$CodProyPresupuesto."',
					CategoriaProg = '".$CategoriaProg."',
					Ejercicio = '".$Ejercicio."',
					FechaInicio = '".formatFechaAMD($FechaInicio)."',
					FechaFin = '".formatFechaAMD($FechaFin)."',
					MontoProyecto = '".setNumero($MontoProyecto)."',
					MontoAprobado = '".setNumero($MontoAprobado)."',
					PreparadoPor = '".$PreparadoPor."',
					FechaPreparado = '".formatFechaAMD($FechaPreparado)."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		##	detalle
		$Secuencia = 0;
		for ($i=0; $i < count($cod_partida); $i++) {
			if (setNumero($MontoPresupuestado[$i])) {
				$sql = "SELECT *
						FROM pv_proyectopresupuestodet
						WHERE
							CodOrganismo = '$CodOrganismo'
							AND CodProyPresupuesto = '$CodProyPresupuesto'
							AND CodFuente = '$CodFuente[$i]'
							AND cod_partida = '$cod_partida[$i]'";
				$field_detalle = getRecord($sql);
				##	
				if (!count($field_detalle))
				{
					$sql = "INSERT INTO pv_proyectopresupuestodet
							SET
								CodOrganismo = '".$CodOrganismo."',
								CodProyPresupuesto = '".$CodProyPresupuesto."',
								CodFuente = '".$CodFuente[$i]."',
								cod_partida = '".$cod_partida[$i]."',
								MontoPresupuestado = '".setNumero($MontoPresupuestado[$i])."',
								Estado = 'PR',
								UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
								UltimaFecha = NOW()";
					execute($sql);
				} else {
					die("La partida <strong>$cod_partida[$i]</strong> esta repetida para La F.F <strong>$CodFuente[$i]/strong> ");
				}
			}
		}
		if ($accion == 'generar-anteproyecto') {
			$sql = "SELECT fm.*
					FROM
						pv_formulacionmetas fm
						INNER JOIN pv_metaspoa mp ON (mp.CodMeta = fm.CodMeta)
						INNER JOIN pv_objetivospoa op ON (op.CodObjetivo = mp.CodObjetivo)
					WHERE
						fm.Ejercicio = '$Ejercicio' AND
						op.CategoriaProg = '$CategoriaProg'";
			$field_metas = getRecords($sql);
			foreach ($field_metas as $f) {
				##	formulacion
				$sql = "UPDATE pv_formulacionmetas
						SET
							CodOrganismo = '$CodOrganismo',
							CodProyPresupuesto = '$CodProyPresupuesto',
							Estado = 'GE',
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()
						WHERE
							CodMeta = '".$f['CodMeta']."' AND
							Ejercicio = '".$f['Ejercicio']."'";
				execute($sql);
			}
		}
		##	-----------------
		mysql_query("COMMIT");
	}
	//	modificar
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		##	-----------------
		##	actualizar
		$sql = "UPDATE pv_proyectopresupuesto
				SET
					MontoProyecto = '".setNumero($MontoProyecto)."',
					MontoAprobado = '".setNumero($MontoAprobado)."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					CodProyPresupuesto = '".$CodProyPresupuesto."'";
		execute($sql);
		##	detalle
		$sql = "DELETE FROM pv_proyectopresupuestodet WHERE CodOrganismo = '".$CodOrganismo."' AND CodProyPresupuesto = '".$CodProyPresupuesto."'";
		execute($sql);
		##	
		$Secuencia = 0;
		for ($i=0; $i < count($cod_partida); $i++) {
			if (setNumero($MontoPresupuestado[$i])) {
				$sql = "SELECT *
						FROM pv_proyectopresupuestodet
						WHERE
							CodOrganismo = '$CodOrganismo'
							AND CodProyPresupuesto = '$CodProyPresupuesto'
							AND CodFuente = '$CodFuente[$i]'
							AND cod_partida = '$cod_partida[$i]'";
				$field_detalle = getRecord($sql);
				##	
				if (!count($field_detalle))
				{
					$sql = "INSERT INTO pv_proyectopresupuestodet
							SET
								CodOrganismo = '".$CodOrganismo."',
								CodProyPresupuesto = '".$CodProyPresupuesto."',
								CodFuente = '".$CodFuente[$i]."',
								cod_partida = '".$cod_partida[$i]."',
								MontoPresupuestado = '".setNumero($MontoPresupuestado[$i])."',
								Estado = 'PR',
								UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
								UltimaFecha = NOW()";
					execute($sql);
				} else {
					die("La partida <strong>$cod_partida[$i]</strong> esta repetida para La F.F <strong>$CodFuente[$i]/strong> ");
				}
			}
		}
		##	-----------------
		mysql_query("COMMIT");
	}
	//	revisar
	elseif ($accion == "revisar") {
		mysql_query("BEGIN");
		##	-----------------
		##	actualizar
		$sql = "UPDATE pv_proyectopresupuesto
				SET
					Estado = '".$Estado."',
					RevisadoPor = '".$RevisadoPor."',
					FechaRevisado = '".formatFechaAMD($FechaRevisado)."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					CodProyPresupuesto = '".$CodProyPresupuesto."'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	aprobar
	elseif ($accion == "aprobar") {
		mysql_query("BEGIN");
		##	-----------------
		##	actualizar
		$sql = "UPDATE pv_proyectopresupuesto
				SET
					NroGaceta = '".$NroGaceta."',
					FechaGaceta = '".formatFechaAMD($FechaGaceta)."',
					NroResolucion = '".$NroResolucion."',
					FechaResolucion = '".formatFechaAMD($FechaResolucion)."',
					Estado = '".$Estado."',
					AprobadoPor = '".$AprobadoPor."',
					FechaAprobado = '".formatFechaAMD($FechaAprobado)."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					CodProyPresupuesto = '".$CodProyPresupuesto."'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	generar
	elseif ($accion == "generar") {
		mysql_query("BEGIN");
		##	-----------------
		$Diferencia = round(setNumero($MontoAprobado),2) - round($MontoxAprobar,2);
		if (round(setNumero($MontoAprobado),2) == 0.00) die('El <strong>Monto Aprobado</strong> no puede ser 0.');
		elseif (round($MontoxAprobar,2) == 0.00) die('El <strong>Monto Distribuido</strong> no puede ser 0.');
		elseif (round(setNumero($MontoAprobado),2) > round(setNumero($MontoProyecto),2)) die('El <strong>Monto Aprobado</strong> no puede ser mayor que el <strong>Monto del Proyecto</strong>.');
		elseif ($Diferencia > 0.00) die('El <strong>Monto Aprobado</strong> es mayor que el Monto Distribuido.<br />Faltan por Distribuir <strong>'.number_format($Diferencia,2,',','.').' Bs.</strong>.');
		elseif ($Diferencia < 0.00) die('El <strong>Monto Distribuido</strong> es mayor que el <strong>Monto Aprobado</strong>.');
		##	actualizar
		$sql = "UPDATE pv_proyectopresupuesto
				SET
					MontoAprobado = '".setNumero($MontoAprobado)."',
					Estado = '".$Estado."',
					GeneradoPor = '".$GeneradoPor."',
					FechaGenerado = '".formatFechaAMD($FechaGenerado)."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					CodProyPresupuesto = '".$CodProyPresupuesto."'";
		execute($sql);
		##	detalle
		for ($i=0; $i < count($cod_partida); $i++) {
			if (setNumero($MontoAprobadoDet[$i]) > 0) {
				if ($FlagAnexa[$i] == 'S')
				{
					if (setNumero($MontoAprobadoDet[$i]) > 0)
					{
						$sql = "INSERT INTO pv_proyectopresupuestodet
								SET
									CodOrganismo = '".$CodOrganismo."',
									CodProyPresupuesto = '".$CodProyPresupuesto."',
									CodFuente = '".$CodFuente[$i]."',
									cod_partida = '".$cod_partida[$i]."',
									MontoAprobado = '".setNumero($MontoAprobadoDet[$i])."',
									FlagAnexa = '".$FlagAnexa[$i]."',
									Estado = 'AP',
									UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
									UltimaFecha = NOW()";
						execute($sql);
					}
				}
				else
				{
					$sql = "UPDATE pv_proyectopresupuestodet
							SET
								MontoAprobado = '".setNumero($MontoAprobadoDet[$i])."',
								Estado = 'AP',
								UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
								UltimaFecha = NOW()
							WHERE
								CodOrganismo = '".$CodOrganismo."' AND
								CodProyPresupuesto = '".$CodProyPresupuesto."' AND
								CodFuente = '".$CodFuente[$i]."' AND
								cod_partida = '".$cod_partida[$i]."'";
					execute($sql);
				}
			}
		}
		//	presupuesto
		$sql = "SELECT * FROM pv_proyectopresupuesto WHERE CodOrganismo = '".$CodOrganismo."' AND CodProyPresupuesto = '".$CodProyPresupuesto."'";
		$field = getRecord($sql);
		##	codigo
		$CodPresupuesto = codigo('pv_presupuesto','CodPresupuesto',4,['CodOrganismo'],[$CodOrganismo]);
		##	inserto
		$sql = "INSERT INTO pv_presupuesto
				SET
					CodOrganismo = '".$field['CodOrganismo']."',
					CodPresupuesto = '".$CodPresupuesto."',
					CodProyPresupuesto = '".$field['CodProyPresupuesto']."',
					CategoriaProg = '".$field['CategoriaProg']."',
					Ejercicio = '".$field['Ejercicio']."',
					FechaInicio = '".$field['FechaInicio']."',
					FechaFin = '".$field['FechaFin']."',
					MontoAprobado = '".setNumero($MontoAprobado)."',
					MontoAjustado = '".setNumero($MontoAprobado)."',
					Estado = 'AP',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		##	detalle
		$sql = "SELECT * FROM pv_proyectopresupuestodet WHERE CodOrganismo = '".$field['CodOrganismo']."' AND CodProyPresupuesto = '".$field['CodProyPresupuesto']."'";
		$field_detalle = getRecords($sql);
		foreach ($field_detalle as $f) {
			if ($f['MontoAprobado'] > 0) {
				$sql = "INSERT INTO pv_presupuestodet
						SET
							CodOrganismo = '".$f['CodOrganismo']."',
							CodPresupuesto = '".$CodPresupuesto."',
							CodFuente = '".$f['CodFuente']."',
							cod_partida = '".$f['cod_partida']."',
							MontoAprobado = '".$f['MontoAprobado']."',
							MontoAjustado = '".$f['MontoAprobado']."',
							FlagAnexa = '".$f['FlagAnexa']."',
							Estado = 'AP',
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()";
				execute($sql);
			}
		}
		##	-----------------
		mysql_query("COMMIT");
	}
	//	generar
	elseif ($accion == "generar-presupuesto") {
		mysql_query("BEGIN");
		##	-----------------
		$Diferencia = round(setNumero($MontoAprobado),2) - round($MontoxAprobar,2);
		if (round(setNumero($MontoAprobado),2) == 0.00) die('El <strong>Monto Aprobado</strong> no puede ser 0.');
		elseif (round($MontoxAprobar,2) == 0.00) die('El <strong>Monto Distribuido</strong> no puede ser 0.');
		elseif (round(setNumero($MontoAprobado),2) > round(setNumero($MontoProyecto),2)) die('El <strong>Monto Aprobado</strong> no puede ser mayor que el <strong>Monto del Proyecto</strong>.');
		elseif ($Diferencia > 0.00) die('El <strong>Monto Aprobado</strong> es mayor que el Monto Distribuido.<br />Faltan por Distribuir <strong>'.number_format($Diferencia,2,',','.').' Bs.</strong>.');
		elseif ($Diferencia < 0.00) die('El <strong>Monto Distribuido</strong> es mayor que el <strong>Monto Aprobado</strong>.');
		##	codigo
		$CodPresupuesto = codigo('pv_presupuesto','CodPresupuesto',4,['CodOrganismo'],[$CodOrganismo]);
		##	inserto
		$sql = "INSERT INTO pv_presupuesto
				SET
					CodOrganismo = '".$CodOrganismo."',
					CodPresupuesto = '".$CodPresupuesto."',
					CategoriaProg = '".$CategoriaProg."',
					Ejercicio = '".$Ejercicio."',
					FechaInicio = '".formatFechaAMD($FechaInicio)."',
					FechaFin = '".formatFechaAMD($FechaFin)."',
					MontoAprobado = '".setNumero($MontoAprobado)."',
					MontoAjustado = '".setNumero($MontoAprobado)."',
					Estado = 'AP',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		##	detalle
		for ($i=0; $i < count($cod_partida); $i++) {
			if (setNumero($MontoPresupuestado[$i])) {
				$sql = "INSERT INTO pv_presupuestodet
						SET
							CodOrganismo = '".$CodOrganismo."',
							CodPresupuesto = '".$CodPresupuesto."',
							CodFuente = '".$CodFuente[$i]."',
							cod_partida = '".$cod_partida[$i]."',
							MontoAprobado = '".setNumero($MontoPresupuestado[$i])."',
							MontoAjustado = '".setNumero($MontoPresupuestado[$i])."',
							Estado = 'AP',
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()";
				execute($sql);
			}
		}
		##	actualizo metas
		$sql = "SELECT fm.*
				FROM
					pv_reformulacionmetas fm
					INNER JOIN pv_metaspoa mp ON (mp.CodMeta = fm.CodMeta)
					INNER JOIN pv_objetivospoa op ON (op.CodObjetivo = mp.CodObjetivo)
				WHERE
					fm.Ejercicio = '$Ejercicio' AND
					op.CategoriaProg = '$CategoriaProg'";
		$field_metas = getRecords($sql);
		foreach ($field_metas as $f) {
			##	formulacion
			$sql = "UPDATE pv_reformulacionmetas
					SET
						CodOrganismo = '$CodOrganismo',
						CodPresupuesto = '$CodPresupuesto',
						Estado = 'GE',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()
					WHERE
						CodMeta = '".$f['CodMeta']."' AND
						Ejercicio = '".$f['Ejercicio']."'";
			execute($sql);
		}
		##	actualizo personal
		$sql = "SELECT * FROM pr_proyrecursos WHERE Ejercicio = '$Ejercicio'";
		$field_recursos = getRecords($sql);
		foreach ($field_recursos as $f) {
			$sql = "UPDATE pr_proyrecursosdet SET Estado = 'GE' WHERE CodRecurso = '$f[CodRecurso]' AND CategoriaProg = '$CategoriaProg'";
			execute($sql);
			##	
			$sql = "SELECT * FROM pr_proyrecursosdet WHERE CodRecurso = '$f[CodRecurso]' AND Estado = 'AP'";
			$field_valido_recurso = getRecords($sql);
			if (!count($field_valido_recurso)) {
				$sql = "UPDATE pr_proyrecursos SET Estado = 'GE' WHERE CodRecurso = '$f[CodRecurso]'";
				execute($sql);
			}
		}
		##	actualizo personal (proyeccion x  partidas)
		$sql = "SELECT * FROM pr_proypresupuestaria WHERE CodOrganismo = '$CodOrganismo' AND Ejercicio = '$Ejercicio' AND CategoriaProg = '$CategoriaProg'";
		$field_recursos = getRecords($sql);
		foreach ($field_recursos as $f) {
			$sql = "UPDATE pr_proypresupuestariadet SET Estado = 'GE' WHERE CodProyPresupuesto = '$f[CodProyPresupuesto]'";
			execute($sql);
		}
		$sql = "UPDATE pr_proypresupuestaria SET Estado = 'GE' WHERE CodOrganismo = '$CodOrganismo' AND Ejercicio = '$Ejercicio' AND CategoriaProg = '$CategoriaProg'";
		execute($sql);
		##	actualizo obras
		$sql = "SELECT * FROM ob_planobras WHERE Ejercicio = '$Ejercicio' AND CategoriaProg = '$CategoriaProg'";
		$field_obras = getRecords($sql);
		foreach ($field_obras as $f) {
			$sql = "UPDATE pv_presupuestoobra SET Estado = 'GE' WHERE CodPlanObra = '$f[CodPlanObra]'";
			execute($sql);
		}

		##	-----------------
		mysql_query("COMMIT");
	}
	//	anular
	elseif ($accion == "anular") {
		mysql_query("BEGIN");
		##	-----------------
		##	valido
		if ($Estado == 'AN' || $Estado == 'GE') die('No puede anular un proyecto <strong>'.printValores('proyecto-estado',$Estado).'</strong>');
		##	
		if ($Estado == 'AP' || $Estado == 'RV') $Estado = 'PR';
		elseif ($Estado == 'PR') $Estado = 'AN';
		##	actualizar
		$sql = "UPDATE pv_proyectopresupuesto
				SET
					Estado = '".$Estado."',
					AnuladoPor = '".$_SESSION['CODPERSONA_ACTUAL']."',
					FechaAnulado = NOW(),
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					CodProyPresupuesto = '".$CodProyPresupuesto."'";
		execute($sql);
		##	detalle
		$sql = "UPDATE pv_proyectopresupuestodet
				SET
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					CodProyPresupuesto = '".$CodProyPresupuesto."'";
		execute($sql);
		##	
		$sql = "DELETE FROM pv_proyectopresupuestodet 
				WHERE
					CodOrganismo = '".$CodOrganismo."' AND
					CodProyPresupuesto = '".$CodProyPresupuesto."' AND
					FlagAnexa = 'S'";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
}
elseif ($modulo == "validar") {
	//	modificar
	if($accion == "modificar") {
		list($CodOrganismo, $CodProyPresupuesto) = explode('_', $codigo);
		$sql = "SELECT Estado FROM pv_proyectopresupuesto WHERE CodOrganismo = '$CodOrganismo' AND CodProyPresupuesto = '$CodProyPresupuesto'";
		$Estado = getVar3($sql);
		if ($Estado != 'PR') die('No puede modificar un proyecto <strong>'.printValores('proyecto-estado',$Estado).'</strong>');
	}
	//	revisar
	elseif($accion == "revisar") {
		list($CodOrganismo, $CodProyPresupuesto) = explode('_', $codigo);
		$sql = "SELECT Estado FROM pv_proyectopresupuesto WHERE CodOrganismo = '$CodOrganismo' AND CodProyPresupuesto = '$CodProyPresupuesto'";
		$Estado = getVar3($sql);
		if ($Estado != 'PR') die('No puede revisar un proyecto <strong>'.printValores('proyecto-estado',$Estado).'</strong>');
	}
	//	aprobar
	elseif($accion == "aprobar") {
		list($CodOrganismo, $CodProyPresupuesto) = explode('_', $codigo);
		$sql = "SELECT Estado FROM pv_proyectopresupuesto WHERE CodOrganismo = '$CodOrganismo' AND CodProyPresupuesto = '$CodProyPresupuesto'";
		$Estado = getVar3($sql);
		if ($Estado != 'RV') die('No puede aprobar un proyecto <strong>'.printValores('proyecto-estado',$Estado).'</strong>');
	}
	//	anular
	elseif($accion == "anular") {
		list($CodOrganismo, $CodProyPresupuesto) = explode('_', $codigo);
		$sql = "SELECT Estado FROM pv_proyectopresupuesto WHERE CodOrganismo = '$CodOrganismo' AND CodProyPresupuesto = '$CodProyPresupuesto'";
		$Estado = getVar3($sql);
		if ($Estado == 'AN' || $Estado == 'GE') die('No puede anular un proyecto <strong>'.printValores('proyecto-estado',$Estado).'</strong>');
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
	//	insertar linea
	elseif($accion == "partida_insertar") {
		##	detalle
		$sql = "SELECT * FROM pv_partida WHERE cod_partida = '$cod_partida'";
		$field_detalle = getRecord($sql);
		##	generica
		$cod_partida_generica = substr($cod_partida, 0, 7) . "00.00";
		$sql = "SELECT * FROM pv_partida WHERE cod_partida = '$cod_partida_generica'";
		$field_generica = getRecord($sql);
		##	partida
		$cod_partida_partida = substr($cod_partida, 0, 4) . "00.00.00";
		$sql = "SELECT * FROM pv_partida WHERE cod_partida = '$cod_partida_partida'";
		$field_partida = getRecord($sql);
		##	tipocuenta
		$cod_partida_tipocuenta = substr($cod_partida, 0, 1) . "00.00.00.00";
		$sql = "SELECT * FROM pv_partida WHERE cod_partida = '$cod_partida_tipocuenta'";
		$field_tipocuenta = getRecord($sql);
		##	---------------------------------
		$background="";
		$weight=(($FlagGenerar == 'S')?" color:red;":"");
		$detalle='detalle';
		$readonly = "";
		$id = $field_detalle['cod_partida'];
		?>
		<tr class="trListaBody" style="<?=$background.$weight?>" id="partida_<?=$id?>" onclick="clk($(this), 'partida', 'partida_<?=$id?>');">
			<td align="center">
				<select name="CodFuente[]" class="cell" <?=$disabled_ver?>>
					<?=loadSelect2("pv_fuentefinanciamiento","CodFuente","Denominacion",$_PARAMETRO['FFMETASDEF'],10)?>
				</select>
			</td>
			<td align="center">
				<input type="hidden" name="cod_partida[]" value="<?=$id?>" <?=$readonly?> />
				<input type="hidden" name="tipo[]" value="<?=$field_detalle['tipo']?>" <?=$readonly?> />
				<input type="hidden" name="FlagAnexa[]" value="S" <?=$readonly?> />
				<?=$field_detalle['cod_partida']?>
			</td>
			<td><input type="text" value="<?=htmlentities($field_detalle['denominacion'])?>" class="cell2" style="<?=$weight?>" readonly /></td>
			<td><input type="text" name="MontoPresupuestado[]" value="0,00" class="cell currency presupuestado <?=$detalle?> tc<?=$field_detalle['cod_tipocuenta']?> p<?=$field_detalle['partida1']?> g<?=$field_detalle['generica']?> e<?=$field_detalle['especifica']?> se<?=$field_detalle['subespecifica']?>" style="text-align:right; <?=$weight?>" <?=$readonly?> onchange="setMontos('tc<?=$field_detalle['cod_tipocuenta']?>', 'p<?=$field_detalle['partida1']?>', 'g<?=$field_detalle['generica']?>',1);" /></td>
			<?php 
			if ($FlagGenerar == 'S') { ?> <td><input type="text" name="MontoAprobadoDet[]" value="0,00" class="cell currency aprobado <?=$detalle?> atc<?=$field_detalle['cod_tipocuenta']?> ap<?=$field_detalle['partida1']?> ag<?=$field_detalle['generica']?> ae<?=$field_detalle['especifica']?> ase<?=$field_detalle['subespecifica']?>" style="text-align:right; <?=$weight?>" onchange="setMontos('atc<?=$field_detalle['cod_tipocuenta']?>', 'ap<?=$field_detalle['partida1']?>', 'ag<?=$field_detalle['generica']?>',0);" /></td> <?php }
			?>
		</tr>|
		<?php
		##	---------------------------------
		$background="background-color:#DEDEDE;";
		$weight="font-weight:bold;";
		$detalle='generica';
		$readonly = "disabled";
		$id = $field_generica['cod_partida'];
		?>
		<tr class="trListaBody" style="<?=$background.$weight?>" id="partida_<?=$id?>">
			<td align="center"></td>
			<td align="center">
				<input type="hidden" name="cod_partida[]" value="<?=$id?>" <?=$readonly?> />
				<input type="hidden" name="tipo[]" value="<?=$field_generica['tipo']?>" <?=$readonly?> />
				<input type="hidden" name="FlagAnexa[]" value="N" <?=$readonly?> />
				<?=$field_generica['cod_partida']?>
			</td>
			<td><input type="text" value="<?=htmlentities($field_generica['denominacion'])?>" class="cell2" style="<?=$weight?>" readonly /></td>
			<td><input type="text" name="MontoPresupuestado[]" value="0,00" class="cell currency presupuestado <?=$detalle?> tc<?=$field_generica['cod_tipocuenta']?> p<?=$field_generica['partida1']?> g<?=$field_generica['generica']?> e<?=$field_generica['especifica']?> se<?=$field_generica['subespecifica']?>" style="text-align:right; <?=$weight?>" <?=$readonly?> onchange="setMontos('tc<?=$field_generica['cod_tipocuenta']?>', 'p<?=$field_generica['partida1']?>', 'g<?=$field_generica['generica']?>',1);" /></td>
			<?php
			if ($FlagGenerar == 'S') { ?> <td><input type="text" name="MontoAprobadoDet[]" value="0,00" class="cell currency aprobado <?=$detalle?> atc<?=$field_detalle['cod_tipocuenta']?> ap<?=$field_detalle['partida1']?> ag<?=$field_detalle['generica']?> ae<?=$field_detalle['especifica']?> ase<?=$field_detalle['subespecifica']?>" style="text-align:right; <?=$weight?>" <?=$readonly?> onchange="setMontos('atc<?=$field_detalle['cod_tipocuenta']?>', 'ap<?=$field_detalle['partida1']?>', 'ag<?=$field_detalle['generica']?>',0);" /></td> <?php }
			?>
		</tr>|
		<?php
		##	---------------------------------
		$background="background-color:#C7C7C7;";
		$weight="font-weight:bold;";
		$detalle='partida';
		$readonly = "disabled";
		$id = $field_partida['cod_partida'];
		?>
		<tr class="trListaBody" style="<?=$background.$weight?>" id="partida_<?=$id?>">
			<td align="center"></td>
			<td align="center">
				<input type="hidden" name="cod_partida[]" value="<?=$id?>" <?=$readonly?> />
				<input type="hidden" name="tipo[]" value="<?=$field_partida['tipo']?>" <?=$readonly?> />
				<input type="hidden" name="FlagAnexa[]" value="N" <?=$readonly?> />
				<?=$field_partida['cod_partida']?>
			</td>
			<td><input type="text" value="<?=htmlentities($field_partida['denominacion'])?>" class="cell2" style="<?=$weight?>" readonly /></td>
			<td><input type="text" name="MontoPresupuestado[]" value="0,00" class="cell currency presupuestado <?=$detalle?> tc<?=$field_partida['cod_tipocuenta']?> p<?=$field_partida['partida1']?> g<?=$field_partida['generica']?> e<?=$field_partida['especifica']?> se<?=$field_partida['subespecifica']?>" style="text-align:right; <?=$weight?>" <?=$readonly?> onchange="setMontos('tc<?=$field_partida['cod_tipocuenta']?>', 'p<?=$field_partida['partida1']?>', 'g<?=$field_partida['generica']?>',1);" /></td>
			<?php
			if ($FlagGenerar == 'S') { ?> <td><input type="text" name="MontoAprobadoDet[]" value="0,00" class="cell currency aprobado <?=$detalle?> atc<?=$field_detalle['cod_tipocuenta']?> ap<?=$field_detalle['partida1']?> ag<?=$field_detalle['generica']?> ae<?=$field_detalle['especifica']?> ase<?=$field_detalle['subespecifica']?>" style="text-align:right; <?=$weight?>" <?=$readonly?> onchange="setMontos('atc<?=$field_detalle['cod_tipocuenta']?>', 'ap<?=$field_detalle['partida1']?>', 'ag<?=$field_detalle['generica']?>',0);" /></td> <?php }
			?>
		</tr>|
		<?php
		##	---------------------------------
		$background="background-color:#B6B6B6;";
		$weight="font-weight:bold;";
		$disabled='disabled';
		$detalle='cuenta';
		$readonly = "disabled";
		$id = $field_tipocuenta['cod_partida'];
		?>
		<tr class="trListaBody" style="<?=$background.$weight?>" id="partida_<?=$id?>">
			<td align="center"></td>
			<td align="center">
				<input type="hidden" name="cod_partida[]" value="<?=$id?>" <?=$readonly?> />
				<input type="hidden" name="tipo[]" value="<?=$field_tipocuenta['tipo']?>" <?=$readonly?> />
				<input type="hidden" name="FlagAnexa[]" value="N" <?=$readonly?> />
				<?=$field_tipocuenta['cod_partida']?>
			</td>
			<td><input type="text" value="<?=htmlentities($field_tipocuenta['denominacion'])?>" class="cell2" style="<?=$weight?>" readonly /></td>
			<td><input type="text" name="MontoPresupuestado[]" value="0,00" class="cell currency presupuestado <?=$detalle?> tc<?=$field_tipocuenta['cod_tipocuenta']?> p<?=$field_tipocuenta['partida1']?> g<?=$field_tipocuenta['generica']?> e<?=$field_tipocuenta['especifica']?> se<?=$field_tipocuenta['subespecifica']?>" style="text-align:right; <?=$weight?>" <?=$readonly?> onchange="setMontos('tc<?=$field_tipocuenta['cod_tipocuenta']?>', 'p<?=$field_tipocuenta['partida1']?>', 'g<?=$field_tipocuenta['generica']?>',1);" /></td>
			<?php
			if ($FlagGenerar == 'S') { ?> <td><input type="text" name="MontoAprobadoDet[]" value="0,00" class="cell currency aprobado <?=$detalle?> atc<?=$field_detalle['cod_tipocuenta']?> ap<?=$field_detalle['partida1']?> ag<?=$field_detalle['generica']?> ae<?=$field_detalle['especifica']?> ase<?=$field_detalle['subespecifica']?>" style="text-align:right; <?=$weight?>" <?=$readonly?> onchange="setMontos('atc<?=$field_detalle['cod_tipocuenta']?>', 'ap<?=$field_detalle['partida1']?>', 'ag<?=$field_detalle['generica']?>',0);" /></td> <?php }
			?>
		</tr>|
		<?php
		echo $field_detalle['cod_partida'].'|';
		echo $field_generica['cod_partida'].'|';
		echo $field_partida['cod_partida'].'|';
		echo $field_tipocuenta['cod_partida'];
	}
	//	resumen presupuestario
	elseif($accion == "resumen_presupuestario") {
		$Fuente = [];
		$Partida = [];
		for ($i=0; $i < count($CodFuente); $i++)
		{
			$MontoPresupuestado[$i] = setNumero($MontoPresupuestado[$i]);

			if ($MontoPresupuestado[$i] > 0) 
			{
				$f = $CodFuente[$i];
				$Fuente[$f] += $MontoPresupuestado[$i];

				$p = $cod_partida[$i];
				$Partida[$f][$p] += $MontoPresupuestado[$i];
			}
		}

		foreach ($Fuente as $CodigoFuente => $MontoFuente)
		{
			?>
			<tr class="trListaBody2">
				<td colspan="2">
					<?=$CodigoFuente?> - <?=getVar3("SELECT Denominacion FROM pv_fuentefinanciamiento WHERE CodFuente = '$CodigoFuente'")?>
				</td>
				<td align="right"><?=number_format($MontoFuente,2,',','.')?></td>
			</tr>
			<?php

			foreach ($Partida[$CodigoFuente] as $CodigoPartida => $MontoPartida)
			{
				?>
				<tr class="trListaBody">
					<td align="center"><?=$CodigoPartida?></td>
					<td>
						<input type="text" value="<?=getVar3("SELECT denominacion FROM pv_partida WHERE cod_partida = '$CodigoPartida'")?>" class="cell2" readonly />
						</td>
					<td align="right"><?=number_format($MontoPartida,2,',','.')?></td>
				</tr>
				<?php
			}
		}
		?>
		<?php
	}
	//	resumen presupuestario aprobado
	elseif($accion == "resumen_presupuestario_aprobado") {
		$Fuente = [];
		$Partida = [];
		for ($i=0; $i < count($CodFuente); $i++)
		{
			$MontoAprobadoDet[$i] = setNumero($MontoAprobadoDet[$i]);

			if ($MontoAprobadoDet[$i] > 0) 
			{
				$f = $CodFuente[$i];
				$Fuente[$f] += $MontoAprobadoDet[$i];

				$p = $cod_partida[$i];
				$Partida[$f][$p] += $MontoAprobadoDet[$i];
			}
		}

		foreach ($Fuente as $CodigoFuente => $MontoFuente)
		{
			?>
			<tr class="trListaBody2">
				<td colspan="2">
					<?=$CodigoFuente?> - <?=getVar3("SELECT Denominacion FROM pv_fuentefinanciamiento WHERE CodFuente = '$CodigoFuente'")?>
				</td>
				<td align="right"><?=number_format($MontoFuente,2,',','.')?></td>
			</tr>
			<?php

			foreach ($Partida[$CodigoFuente] as $CodigoPartida => $MontoPartida)
			{
				?>
				<tr class="trListaBody">
					<td align="center"><?=$CodigoPartida?></td>
					<td>
						<input type="text" value="<?=getVar3("SELECT denominacion FROM pv_partida WHERE cod_partida = '$CodigoPartida'")?>" class="cell2" readonly />
						</td>
					<td align="right"><?=number_format($MontoPartida,2,',','.')?></td>
				</tr>
				<?php
			}
		}
		?>
		<?php
	}
}
?>