<?php
include("../lib/fphp.php");
include("lib/fphp.php");
include("lib/funciones_globales_proyeccion.php");
//	$__archivo = fopen("_"."$modulo-$accion-".$AnioActual.$MesActual.$DiaActual."_".$HorasActual.$MinutosActual.$SegundosActual.".sql", "w+");
//	$__archivo = fopen("_"."$modulo-$accion".".sql", "w+");
##############################################################################/
##	Ajuste Salarias para la Proyeccción de Gastos (NUEVO, MODIFICAR)
##############################################################################/
if ($modulo == "formulario") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		##	-----------------
		##	valido
		if (!trim($CodTipoProceso) || !trim($CodRecurso)) die("Debe llenar los campos (*) obligatorios.");
		##	codigo
		$Numero = codigo('pr_proyparametro','Numero',2,['Ejercicio'],[$Ejercicio]);
		$CodParametro = $Ejercicio.$Numero;
		##	inserto
		$sql = "INSERT INTO pr_proyparametro
				SET
					CodParametro = '".$CodParametro."',
					CodRecurso = '".$CodRecurso."',
					CodTipoProceso = '".$CodTipoProceso."',
					Ejercicio = '".$Ejercicio."',
					Numero = '".$Numero."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		##	detalle
		$Secuencia = 0;
		for ($i=0; $i < count($conceptos_CodConcepto); $i++) {
			eval("\$FlagParametrizable = \$conceptos_FlagParametrizable".$conceptos_CodConcepto[$i].";");
			$sql = "INSERT INTO pr_proyparametrodet
					SET
						CodParametro = '".$CodParametro."',
						CodConcepto = '".$conceptos_CodConcepto[$i]."',
						FlagParametrizable = '".($FlagParametrizable?'S':'N')."',
						Formula = '".$conceptos_Formula[$i]."',
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
		$sql = "UPDATE pr_proyparametro
				SET
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodParametro = '".$CodParametro."'";
		execute($sql);
		##	detalle
		execute("DELETE FROM pr_proyejecucion WHERE CodParametro = '$CodParametro'");
		execute("DELETE FROM pr_proyparametrodet WHERE CodParametro = '$CodParametro'");
		$Secuencia = 0;
		for ($i=0; $i < count($conceptos_CodConcepto); $i++) {
			eval("\$FlagParametrizable = \$conceptos_FlagParametrizable".$conceptos_CodConcepto[$i].";");
			$sql = "INSERT INTO pr_proyparametrodet
					SET
						CodParametro = '".$CodParametro."',
						CodConcepto = '".$conceptos_CodConcepto[$i]."',
						FlagParametrizable = '".($FlagParametrizable?'S':'N')."',
						Formula = '".$conceptos_Formula[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	-----------------
		mysql_query("COMMIT");
	}
	//	establecer
	elseif ($accion == "establecer") {
		mysql_query("BEGIN");
		##	-----------------
		##	detalle
		execute("DELETE FROM pr_proyrecursosparametros WHERE CodParametro = '".$CodParametro."'");
		$Secuencia = 0;
		for ($i=0; $i < count($conceptos_CodRecurso); $i++) {
			$sql = "SELECT ppmd.CodConcepto
					FROM
						pr_proyparametrodet ppmd
						INNER JOIN pr_concepto c On (c.CodConcepto = ppmd.CodConcepto)
						LEFT JOIN pr_proyrecursosparametros prp ON (prp.CodParametro = ppmd.CodParametro AND prp.CodConcepto = ppmd.CodConcepto AND prp.CodRecurso = '$conceptos_CodRecurso[$i]' AND prp.Secuencia = '$conceptos_Secuencia[$i]')
					WHERE ppmd.CodParametro = '$CodParametro'
					ORDER BY CodConcepto";
			$field_conceptos = getRecords($sql);
			foreach ($field_conceptos as $fc) {
				eval("\$Valor = \$conceptos_".$fc['CodConcepto']."[\$i];");
				$sql = "INSERT INTO pr_proyrecursosparametros
						SET
							CodParametro = '".$CodParametro."',
							CodConcepto = '".$fc['CodConcepto']."',
							CodRecurso = '".$conceptos_CodRecurso[$i]."',
							Secuencia = '".$conceptos_Secuencia[$i]."',
							Valor = '".$Valor."',
							UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
							UltimaFecha = NOW()";
				execute($sql);
			}
		}
		##	-----------------
		mysql_query("COMMIT");
	}
}
elseif ($modulo == "ajax") {
	if ($accion == "conceptos_insertar") {
        $sql = "SELECT * FROM pr_concepto WHERE CodConcepto = '".$CodConcepto."'";
        $field_conceptos = getRecords($sql);
        foreach ($field_conceptos as $f) {
            $id = $f['CodConcepto'];
            ##	
            $sql = "SELECT pypd.Formula
            		FROM
            			pr_proyparametrodet pypd
            			INNER JOIN pr_proyparametro pyp ON (pyp.CodParametro = pypd.CodParametro)
            		WHERE
            			-- pyp.Ejercicio <= '$Ejercicio' AND
            			pypd.CodConcepto = '$CodConcepto' AND
            			pypd.Formula <> ''
            		ORDER BY pyp.Ejercicio DESC
            		LIMIT 0, 1";
            $Formula = getVar3($sql);
            ?>
			<tr class="trListaBody" onclick="clk($(this), 'conceptos', 'conceptos_<?=$id?>');" id="conceptos_<?=$id?>">
				<th width="25" align="center">
					<input type="hidden" name="conceptos_CodConcepto[]" value="<?=$f['CodConcepto']?>" />
					<?=$nro_detalles?>
				</th>
				<td>
					<table style="width:100%;">
						<tr>
							<th align="left">
								<div style="float:right;">
									<input type="checkbox" name="conceptos_FlagParametrizable<?=$f['CodConcepto']?>" value="S" <?=chkOpt($f['FlagParametrizable'],'S')?> <?=$disabled_ver?>>
									Parametrizable
								</div>
								<?=htmlentities($f['Descripcion'])?>
							</th>
						</tr>
						<tr>
							<td>
								<textarea name="conceptos_Formula[]" id="conceptos_Formula[]" class="cell" style="height:50px;"><?=$Formula?></textarea>
							</td>
						</tr>
					</table>
				</td>
			</tr>
            <?php
        }
    }
	elseif ($accion == "calcular") {
		##	consulto datos generales
		$sql = "SELECT * FROM pr_proyparametro WHERE CodParametro = '$CodParametro'";
		$field = getRecord($sql);
		$nro_conceptos = 0;
		//	consulto lista
		$sql = "SELECT
					pyrd.CodPersona,
					pyrd.CodRecurso,
					pyrd.Secuencia,
					pyr.CodOrganismo,
					pyr.Ejercicio,
					p.NomCompleto,
					p.Ndocumento,
					p.Sexo,
					p.Fnacimiento,
					e.CodEmpleado,
					e.Fingreso,
					d.CodDependencia,
					d.Dependencia,
					pt.DescripCargo,
					pyrd.Grado,
					pyrd.Paso,
					md.Descripcion AS NomCategoriaCargo
				FROM
					pr_proyrecursosdet pyrd
					INNER JOIN pr_proyrecursos pyr ON (pyr.CodRecurso = pyrd.CodRecurso)
					INNER JOIN pr_proyparametro ppm ON (ppm.CodRecurso = pyr.CodRecurso)
					INNER JOIN mastdependencias d ON (d.CodDependencia = pyrd.CodDependencia)
					INNER JOIN rh_puestos pt ON (pt.CodCargo = pyrd.CodCargo)
					LEFT JOIN mastmiscelaneosdet md ON (md.CodDetalle = pyrd.CategoriaCargo AND md.CodMaestro = 'CATCARGO')
					LEFT JOIN mastpersonas p ON (p.CodPersona = pyrd.CodPersona)
					LEFT JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
				WHERE ppm.CodParametro = '$CodParametro'
				ORDER BY CodDependencia, LENGTH(Ndocumento), Ndocumento";
		$field_empleados = getRecords($sql);
		foreach($field_empleados as $f) {
			$id = ++$nro_conceptos;
			##	establezco valores globales del concepto
			$_ARGS['FlagEjecucion'] = 'N';
			$_ARGS['CodRecurso'] = $f['CodRecurso'];
			$_ARGS['Secuencia'] = $f['Secuencia'];
			$_ARGS['CodOrganismo'] = $f['CodOrganismo'];
			$_ARGS['Ejercicio'] = $f['Ejercicio'];
			$_ARGS['CodPersona'] = $f['CodPersona'];
			$_ARGS['CodEmpleado'] = $f['CodEmpleado'];
			$_ARGS['Sexo'] = $f['Sexo'];
			$_ARGS['FechaNacimiento'] = $f['Fnacimiento'];
			$_ARGS['FechaIngreso'] = $f['Fingreso'];
			$_ARGS['Sexo'] = $f['Sexo'];
			$_ARGS['_SUELDO_BASICO'] = SUELDO_BASICO();
			$_ARGS['_SUELDO_BASICO_DIARIO'] = round(($_ARGS['_SUELDO_BASICO'] / 30), 2);
			?>
			<tr class="trListaBody" onclick="clk($(this), 'conceptos', '<?=$id?>');">
				<th><?=$nro_conceptos?></th>
				<td align="center">
					<input type="hidden" name="conceptos_CodRecurso[]" value="<?=$f['CodRecurso']?>">
					<input type="hidden" name="conceptos_Secuencia[]" value="<?=$f['Secuencia']?>">
					<?=$f['CodPersona']?>
				</td>
				<td><?=htmlentities($f['NomCompleto'])?></td>
				<td align="right"><?=number_format($f['Ndocumento'],0,'','.')?></td>
				<td><?=htmlentities($f['Dependencia'])?></td>
				<td><?=htmlentities($f['DescripCargo'])?></td>
				<td><?=htmlentities($f['NomCategoriaCargo'])?></td>
				<td align="center"><?=$f['Grado']?></td>
				<td align="center"><?=$f['Paso']?></td>
		        <?php
				$sql = "SELECT
							ppmd.CodConcepto,
							ppmd.Formula,
							ppmd.FlagParametrizable,
							c.Abreviatura AS Concepto,
							prp.Valor
						FROM
							pr_proyparametrodet ppmd
							INNER JOIN pr_concepto c On (c.CodConcepto = ppmd.CodConcepto)
							LEFT JOIN pr_proyrecursosparametros prp ON (prp.CodParametro = ppmd.CodParametro AND prp.CodConcepto = ppmd.CodConcepto AND prp.CodRecurso = '$f[CodRecurso]' AND prp.Secuencia = '$f[Secuencia]')
						WHERE
							ppmd.CodParametro = '$CodParametro' AND
							ppmd.FlagParametrizable = 'S'
						ORDER BY CodConcepto";
				$field_conceptos = getRecords($sql);
				foreach ($field_conceptos as $fc) {
					##	establezco valores globales del concepto
					$_ARGS['CodConcepto'] = $fc['CodConcepto'];
					$_ARGS['FlagParametrizable'] = $fc['FlagParametrizable'];

					##	obtengo valor de la formula
					extract($_ARGS);
					$_CANTIDAD = 0;
					$_MONTO = 0;
					eval($fc['Formula']);

					?><td align="center"><input type="text" name="conceptos_<?=$fc['CodConcepto']?>[]" value="<?=number_format((($fc['FlagParametrizable']=='S')?$_CANTIDAD:$_MONTO),2,',','.')?>" class="cell currency" style="text-align:right;"></td><?php
				}
				?>
			</tr>
			<?php
		}
	}
}
?>