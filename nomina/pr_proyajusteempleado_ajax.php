<?php
include("../lib/fphp.php");
include("lib/fphp.php");
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
		$PeriodoDesdeA = substr($PeriodoDesde, 0, 4);
		$PeriodoHastaA = substr($PeriodoHasta, 0, 4);
		if (!trim($CodOrganismo) || !trim($CodTipoNom) || !trim($Ejercicio) || !trim($PeriodoDesde) || !trim($PeriodoHasta) || !trim($Descripcion)) die("Debe llenar los campos (*) obligatorios.");
		elseif ($PeriodoDesde > $PeriodoHasta) die("Periodo incorrecto");
		elseif (!validateDate($PeriodoDesde, 'Y-m') || !validateDate($PeriodoHasta, 'Y-m')) die("Formato de periodos incorrecto (año-mes 0000-00)");
		elseif ($PeriodoDesdeA <> $Ejercicio || $PeriodoHastaA <> $Ejercicio) die("Periodo no pude ser distinto al Ejercicio");
		##	codigo
		$Numero = codigo('pr_proyajusteempleado','Numero',2,['CodOrganismo','Ejercicio'],[$CodOrganismo,$Ejercicio]);
		$CodAjuste = $Ejercicio.$CodOrganismo.$Numero;
		##	inserto
		$sql = "INSERT INTO pr_proyajusteempleado
				SET
					CodAjuste = '".$CodAjuste."',
					CodOrganismo = '".$CodOrganismo."',
					CodTipoNom = '".$CodTipoNom."',
					Ejercicio = '".$Ejercicio."',
					Numero = '".$Numero."',
					PeriodoDesde = '".$PeriodoDesde."',
					PeriodoHasta = '".$PeriodoHasta."',
					Descripcion = '".$Descripcion."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		##	grado
		$Secuencia = 0;
		for ($i=0; $i < count($empleado_CodPersona); $i++) {
			$sql = "INSERT INTO pr_proyajusteempleadodet
					SET
						CodAjuste = '".$CodAjuste."',
						Secuencia = '".++$Secuencia."',
						CodPersona = '".$empleado_CodPersona[$i]."',
						SueldoActual = '".setNumero($empleado_SueldoActual[$i])."',
						Porcentaje = '".setNumero($empleado_Porcentaje[$i])."',
						Monto = '".setNumero($empleado_Monto[$i])."',
						SueldoTotal = '".setNumero($empleado_SueldoTotal[$i])."',
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
		$PeriodoDesdeA = substr($PeriodoDesde, 0, 4);
		$PeriodoHastaA = substr($PeriodoHasta, 0, 4);
		if (!trim($PeriodoDesde) || !trim($PeriodoHasta) || !trim($Descripcion)) die("Debe llenar los campos (*) obligatorios.");
		elseif ($PeriodoDesde > $PeriodoHasta) die("Periodo incorrecto");
		elseif (!validateDate($PeriodoDesde, 'Y-m') || !validateDate($PeriodoHasta, 'Y-m')) die("Formato de periodos incorrecto (año-mes 0000-00)");
		elseif ($PeriodoDesdeA <> $Ejercicio || $PeriodoHastaA <> $Ejercicio) die("Periodo no pude ser distinto al Ejercicio");
		##	actualizo
		$sql = "UPDATE pr_proyajusteempleado
				SET
					PeriodoDesde = '".$PeriodoDesde."',
					PeriodoHasta = '".$PeriodoHasta."',
					Descripcion = '".$Descripcion."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodAjuste = '".$CodAjuste."'";
		execute($sql);
		##	grado
		execute("DELETE FROM pr_proyajusteempleadodet WHERE CodAjuste = '".$CodAjuste."'");
		$Secuencia = 0;
		for ($i=0; $i < count($empleado_CodPersona); $i++) {
			$sql = "INSERT INTO pr_proyajusteempleadodet
					SET
						CodAjuste = '".$CodAjuste."',
						Secuencia = '".++$Secuencia."',
						CodPersona = '".$empleado_CodPersona[$i]."',
						SueldoActual = '".setNumero($empleado_SueldoActual[$i])."',
						Porcentaje = '".setNumero($empleado_Porcentaje[$i])."',
						Monto = '".setNumero($empleado_Monto[$i])."',
						SueldoTotal = '".setNumero($empleado_SueldoTotal[$i])."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	-----------------
		mysql_query("COMMIT");
	}
}
elseif ($modulo == "ajax") {
	if ($accion == "getEmpleados") {
		$i = 0;
		$nro_empleado = 0;
		$sql = "SELECT
					p.CodPersona,
					p.Ndocumento,
					p.NomCompleto,
					e.CodEmpleado,
					e.SueldoActual,
					e.SueldoActual AS SueldoTotal,
					e.CodDependencia,
					d.Dependencia
				FROM
					mastempleado e
					INNER JOIN mastpersonas p ON (p.CodPersona = e.CodPersona)
					INNER JOIN mastdependencias d On (d.CodDependencia = e.CodDependencia)
				WHERE
					p.Estado = 'A' AND
					e.CodOrganismo = '$CodOrganismo' AND
					e.CodTipoNom = '$CodTipoNom'
				ORDER BY CodDependencia, CodTipoNom, CodPersona";
		$field = getRecords($sql);
		foreach ($field as $f) {
			$id = ++$nro_empleado;
			$selChk = "$('#chk_tr".$id."').prop('checked', !$('#chk_tr".$id."').prop('checked'));";
			$disInput = "$('.tr".$id."').prop('disabled', !$('#chk_tr".$id."').prop('checked'));";
			$disabled_detalle = "disabled";
			$checked = "";
			if ($Grupo != $f['CodDependencia']) {
				$Grupo = $f['CodDependencia'];
				?>
				<tr class="trListaBody2">
					<td colspan="9"><?=htmlentities($f['Dependencia'])?></td>
				</tr>
				<?php
			}
			?>
			<tr class="trListaBody">
				<th align="center" onclick="<?=$selChk?> <?=$disInput?>">
					<input type="hidden" name="empleado_CodPersona[]" value="<?=$f['CodPersona']?>" class="tr<?=$id?>" <?=$disabled_detalle?> />
					<?=$nro_empleado?>
				</th>
				<td align="center" width="25">
					<input type="checkbox" id="chk_tr<?=$id?>" <?=$checked?> <?=$disabled_ver?> onclick="$('.tr<?=$id?>').prop('disabled', !$('#chk_tr<?=$id?>').prop('checked'));">
				</td>
				<td align="center" width="50"><?=$f['CodEmpleado']?></td>
				<td><?=$f['NomCompleto']?></td>
				<td align="right"><?=number_format($f['Ndocumento'],0,'','.')?></td>
				<td align="right"><input type="text" name="empleado_SueldoActual[]" value="<?=number_format($f['SueldoActual'],2,',','.')?>" class="cell2 tr<?=$id?>" style="text-align:right;" readonly <?=$disabled_detalle?> /></td>
				<td align="right"><input type="text" name="empleado_Monto[]" value="0,00" class="cell currency tr<?=$id?>" style="text-align:right;" onchange="setSueldoTotal('<?=$i?>');" <?=$disabled_detalle?> /></td>
				<td align="right"><input type="text" name="empleado_Porcentaje[]" value="0,00" class="cell currency tr<?=$id?>" style="text-align:right;" onchange="setSueldoTotal('<?=$i?>');" <?=$disabled_detalle?> /></td>
				<td align="right"><input type="text" name="empleado_SueldoTotal[]" value="<?=number_format($f['SueldoTotal'],2,',','.')?>" class="cell2 tr<?=$id?>" style="text-align:right; font-weight:bold;" readonly <?=$disabled_detalle?> /></td>
			</tr>
			<?php
			++$i;
		}
	}
}
?>