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
		if (!trim($CodOrganismo) || !trim($CodTipoNom) || !trim($Ejercicio) || !trim($Descripcion)) die("Debe llenar los campos (*) obligatorios.");
		##	codigo
		$Numero = codigo('pr_proyrecursos','Numero',2,['CodOrganismo','Ejercicio'],[$CodOrganismo,$Ejercicio]);
		$CodRecurso = $Ejercicio.$CodOrganismo.$Numero;
		##	inserto
		$sql = "INSERT INTO pr_proyrecursos
				SET
					CodRecurso = '".$CodRecurso."',
					CodOrganismo = '".$CodOrganismo."',
					CodTipoNom = '".$CodTipoNom."',
					Ejercicio = '".$Ejercicio."',
					Numero = '".$Numero."',
					Descripcion = '".$Descripcion."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		##	grado
		$Secuencia = 0;
		for ($i=0; $i < count($empleado_CodCargo); $i++) {
			##	valido
			if (!trim($empleado_CodDependencia[$i])) die("Debe seleccionar todas las Dependencias para los Recursos.");
			elseif (!trim($empleado_CodCargo[$i]) || !trim($empleado_Grado[$i]) || !trim($empleado_Paso[$i])) die("Debe seleccionar todos los Cargos para los Recursos.");
			elseif (!trim($empleado_CategoriaProg[$i])) die("Debe seleccionar todas las Categor&iacute;as Program&aacute;ticas para los Recursos.");
			##	inserto
			if ($empleado_CodPersona[$i] != '') $iCodPersona = "CodPersona = '".$empleado_CodPersona[$i]."',"; else $iCodPersona = "";
			$sql = "INSERT INTO pr_proyrecursosdet
					SET
						CodRecurso = '".$CodRecurso."',
						Secuencia = '".++$Secuencia."',
						$iCodPersona
						CodDependencia = '".$empleado_CodDependencia[$i]."',
						CodCargo = '".$empleado_CodCargo[$i]."',
						CategoriaCargo = '".$empleado_CategoriaCargo[$i]."',
						Grado = '".$empleado_Grado[$i]."',
						Paso = '".$empleado_Paso[$i]."',
						Tipo = '".$empleado_Tipo[$i]."',
						CategoriaProg = '".$empleado_CategoriaProg[$i]."',
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
		$sql = "UPDATE pr_proyrecursos
				SET
					Descripcion = '".$Descripcion."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodRecurso = '".$CodRecurso."'";
		execute($sql);
		##	grado

		execute("DELETE FROM pr_proyejecucion WHERE CodRecurso = '$CodRecurso'");
		execute("DELETE FROM pr_proyrecursosdet WHERE CodRecurso = '$CodRecurso'");
		$Secuencia = 0;
		for ($i=0; $i < count($empleado_CodPersona); $i++) {
			##	valido
			if (!trim($empleado_CodDependencia[$i])) die("Debe seleccionar todas las Dependencias para los Recursos.");
			elseif (!trim($empleado_CodCargo[$i]) || !trim($empleado_Grado[$i]) || !trim($empleado_Paso[$i])) die("Debe seleccionar todos los Cargos para los Recursos.");
			elseif (!trim($empleado_CategoriaProg[$i])) die("Debe seleccionar todas las Categor&iacute;as Program&aacute;ticas para los Recursos.");
			##	inserto
			if ($empleado_CodPersona[$i] != '') $iCodPersona = "CodPersona = '".$empleado_CodPersona[$i]."',"; else $iCodPersona = "";
			$sql = "INSERT INTO pr_proyrecursosdet
					SET
						CodRecurso = '".$CodRecurso."',
						Secuencia = '".++$Secuencia."',
						$iCodPersona
						CodDependencia = '".$empleado_CodDependencia[$i]."',
						CodCargo = '".$empleado_CodCargo[$i]."',
						CategoriaCargo = '".$empleado_CategoriaCargo[$i]."',
						Grado = '".$empleado_Grado[$i]."',
						Paso = '".$empleado_Paso[$i]."',
						Tipo = '".$empleado_Tipo[$i]."',
						CategoriaProg = '".$empleado_CategoriaProg[$i]."',
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
		$sql = "SELECT
					p.CodPersona,
					p.Ndocumento,
					p.NomCompleto,
					e.CodEmpleado,
					e.SueldoActual,
					e.SueldoActual AS SueldoTotal,
					e.CodDependencia,
					e.CategoriaProg,
					d.Dependencia,
					pt.CodCargo,
					pt.DescripCargo,
					pt.Grado,
					pt.Paso,
					pt.CategoriaCargo
				FROM
					mastempleado e
					INNER JOIN mastpersonas p ON (p.CodPersona = e.CodPersona)
					INNER JOIN mastdependencias d On (d.CodDependencia = e.CodDependencia)
					INNER JOIN rh_puestos pt ON (pt.CodCargo = e.CodCargo)
				WHERE
					p.Estado = 'A' AND
					e.CodOrganismo = '$CodOrganismo' AND
					e.CodTipoNom = '$CodTipoNom'
				ORDER BY CodDependencia";
		$field = getRecords($sql);
		foreach ($field as $f) {
			$id = ++$i;
			?>
			<tr class="trListaBody" onclick="clk($(this), 'empleado', 'empleado_<?=$id?>');" id="empleado_<?=$id?>">
				<th align="center">
					<input type="hidden" name="empleado_CodPersona[]" value="<?=$f['CodPersona']?>" />
					<?=$id?>
				</th>
				<td align="center"><?=$f['CodEmpleado']?></td>
				<td><?=htmlentities($f['NomCompleto'])?></td>
				<td align="right"><?=number_format($f['Ndocumento'],0,'','.')?></td>
                <td>
                    <input type="hidden" name="empleado_CodCargo[]" id="empleado_CodCargo<?=$id?>" value="<?=$f['CodCargo']?>" />
                    <input type="hidden" name="empleado_CategoriaCargo[]" id="empleado_CategoriaCargo<?=$id?>" value="<?=$f['CategoriaCargo']?>" />
                    <input type="text" name="empleado_DescripCargo[]" id="empleado_DescripCargo<?=$id?>" value="<?=$f['DescripCargo']?>" class="cell2" disabled />
                </td>
                <td>
                	<input type="text" name="empleado_Grado[]" id="empleado_Grado<?=$id?>" value="<?=$f['Grado']?>" class="cell2" style="text-align:center;" readonly />
                </td>
                <td>
                	<select name="empleado_Paso[]" id="empleado_Paso<?=$id?>" class="cell">
                		<?=loadSelect2('rh_nivelsalarial','Paso','Paso',$f['Paso'],0,['CategoriaCargo','Grado'],[$f['CategoriaCargo'],$f['Grado']])?>
                	</select>
                </td>
                <td>
                	<select name="empleado_CodDependencia[]" id="empleado_CodDependencia<?=$id?>" class="cell">
                		<option value="">&nbsp;</option>
                		<?=loadSelect2('mastdependencias','CodDependencia','Dependencia',$f['CodDependencia'],0,['CodOrganismo'],[$CodOrganismo])?>
                	</select>
                </td>
                <td>
                	<input type="text" name="empleado_CategoriaProg[]" id="empleado_CategoriaProg<?=$id?>" value="<?=$f['CategoriaProg']?>" class="cell2" style="text-align:center;" readonly />
                </td>
                <td>
                	<select name="empleado_Tipo[]" class="cell">
                		<?=loadSelectValores('proyeccion-tipo')?>
                	</select>
                </td>
			</tr>
			<?php
		}
	}
    //  insertar linea
    elseif ($accion == "empleado_insertar") {
        $sql = "SELECT
        			p.CodPersona,
        			e.CodEmpleado,
					e.CategoriaProg,
        			p.NomCompleto,
        			p.Ndocumento,
        			pt.CodCargo,
        			pt.DescripCargo,
        			pt.CategoriaCargo,
        			pt.Grado,
        			pt.Paso,
        			e.CodDependencia
        		FROM
        			mastpersonas p
        			INNER JOIN mastempleado e On (e.CodPersona = p.CodPersona)
        			INNER JOIN rh_puestos pt ON (pt.CodCargo = e.CodCargo)
        		WHERE p.CodPersona = '".$CodPersona."'";
        $field_empleado = getRecords($sql);
        foreach ($field_empleado as $f) {
            $id = $nro_detalles;
            ?>
			<tr class="trListaBody" onclick="clk($(this), 'empleado', 'empleado_<?=$id?>');" id="empleado_<?=$id?>">
				<th align="center">
					<input type="hidden" name="empleado_CodPersona[]" value="<?=$f['CodPersona']?>" />
					<?=$id?>
				</th>
				<td align="center"><?=$f['CodEmpleado']?></td>
				<td><?=htmlentities($f['NomCompleto'])?></td>
				<td align="right"><?=number_format($f['Ndocumento'],0,'','.')?></td>
                <td>
                    <input type="hidden" name="empleado_CodCargo[]" id="empleado_CodCargo<?=$id?>" value="<?=$f['CodCargo']?>" />
					<input type="hidden" name="empleado_CategoriaCargo[]" id="empleado_CategoriaCargo<?=$id?>" value="<?=$f['CategoriaCargo']?>" />
                    <input type="text" name="empleado_DescripCargo[]" id="empleado_DescripCargo<?=$id?>" value="<?=$f['DescripCargo']?>" class="cell2" disabled />
                </td>
                <td>
                	<input type="text" name="empleado_Grado[]" id="empleado_Grado<?=$id?>" value="<?=$f['Grado']?>" class="cell2" style="text-align:center;" readonly />
                </td>
                <td>
                	<select name="empleado_Paso[]" id="empleado_Paso<?=$id?>" class="cell">
                		<?=loadSelect2('rh_nivelsalarial','Paso','Paso',$f['Paso'],0,['CategoriaCargo','Grado'],[$f['CategoriaCargo'],$f['Grado']])?>
                	</select>
                </td>
                <td>
                	<select name="empleado_CodDependencia[]" id="empleado_CodDependencia<?=$id?>" class="cell">
                		<option value="">&nbsp;</option>
                		<?=loadSelect2('mastdependencias','CodDependencia','Dependencia',$f['CodDependencia'],0,['CodOrganismo'],[$CodOrganismo])?>
                	</select>
                </td>
                <td>
                	<input type="text" name="empleado_CategoriaProg[]" id="empleado_CategoriaProg<?=$id?>" value="<?=$f['empleado_CategoriaProg']?>" class="cell2" style="text-align:center;" readonly />
                </td>
                <td>
                	<select name="empleado_Tipo[]" class="cell">
                		<?=loadSelectValores('proyeccion-tipo')?>
                	</select>
                </td>
			</tr>
            <?php
        }
    }
    //  insertar linea
    elseif ($accion == "cargo_insertar") {
        $sql = "SELECT * FROM rh_puestos WHERE CodCargo = '".$CodCargo."'";
        $field_cargo = getRecords($sql);
        foreach ($field_cargo as $f) {
            $id = $nro_detalles;
            ?>
			<tr class="trListaBody" onclick="clk($(this), 'empleado', 'empleado_<?=$id?>');" id="empleado_<?=$id?>">
				<th align="center">
					<input type="hidden" name="empleado_CodPersona[]" value="<?=$f['CodPersona']?>" />
					<?=$id?>
				</th>
				<td align="center"></td>
				<td></td>
				<td align="right"></td>
                <td>
                    <input type="hidden" name="empleado_CodCargo[]" id="empleado_CodCargo<?=$id?>" value="<?=$f['CodCargo']?>" />
					<input type="hidden" name="empleado_CategoriaCargo[]" id="empleado_CategoriaCargo<?=$id?>" value="<?=$f['CategoriaCargo']?>" />
                    <input type="text" name="empleado_DescripCargo[]" id="empleado_DescripCargo<?=$id?>" value="<?=$f['DescripCargo']?>" class="cell2" disabled />
                </td>
                <td>
                	<input type="text" name="empleado_Grado[]" id="empleado_Grado<?=$id?>" value="<?=$f['Grado']?>" class="cell2" style="text-align:center;" readonly />
                </td>
                <td>
                	<select name="empleado_Paso[]" id="empleado_Paso<?=$id?>" class="cell">
                		<?=loadSelect2('rh_nivelsalarial','Paso','Paso',$f['Paso'],0,['CategoriaCargo','Grado'],[$f['CategoriaCargo'],$f['Grado']])?>
                	</select>
                </td>
                <td>
                	<select name="empleado_CodDependencia[]" id="empleado_CodDependencia<?=$id?>" class="cell">
                		<option value="">&nbsp;</option>
                		<?=loadSelect2('mastdependencias','CodDependencia','Dependencia','',0,['CodOrganismo'],[$CodOrganismo])?>
                	</select>
                </td>
                <td>
                	<input type="text" name="empleado_CategoriaProg[]" id="empleado_CategoriaProg<?=$id?>" class="cell2" style="text-align:center;" readonly />
                </td>
                <td>
                	<select name="empleado_Tipo[]" class="cell">
                		<?=loadSelectValores('proyeccion-tipo')?>
                	</select>
                </td>
			</tr>
            <?php
        }
    }
    //	
    elseif ($accion == "cargo_seleccionar") {
    	$sql = "SELECT * FROM rh_puestos WHERE CodCargo = '$CodCargo'";
    	$field_cargo = getRecord($sql);

    	$sql = "SELECT Paso FROM rh_nivelsalarial WHERE CategoriaCargo = '$field_cargo[CategoriaCargo]' AND Grado = '$field_cargo[Grado]'";
    	$field_pasos = getRecords($sql);

    	$pasos = "";
    	foreach ($field_pasos as $f) {
    		$Pasos .= '<option value="'.$f['Paso'].'">'.$f['Paso'].'</option>';
    	}

    	$jsondata = [
			'Pasos' => $Pasos,
			'Grado' => $field_cargo['Grado'],
			'Paso' => $field_cargo['Paso'],
			'CategoriaCargo' => $field_cargo['CategoriaCargo'],
		];

        echo json_encode($jsondata);
        exit();
    }
}
?>