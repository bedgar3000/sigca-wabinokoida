<?php
if (!$ventana) $ventana = "selLista";
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = ($_SESSION["fCodOrganismo"]?$_SESSION["fCodOrganismo"]:$_SESSION["FILTRO_ORGANISMO_ACTUAL"]);
	if ($FlagNomina != 'S') $fCodDependencia = ($_SESSION["fCodDependencia"]?$_SESSION["fCodDependencia"]:$_SESSION["DEPENDENCIA_ACTUAL"]);
	$fEdoReg = "A";
	$fSitTra = "A";
	$fOrderBy = "CodEmpleado";
	$maxlimit = $_SESSION["MAXLIMIT"];
}
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (e.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fCodDependencia != "") { $cCodDependencia = "checked"; $filtro.=" AND (e.CodDependencia = '".$fCodDependencia."')"; } else $dCodDependencia = "disabled";
if ($fCodCentroCosto != "") { $cCodCentroCosto = "checked"; $filtro.=" AND (e.CodCentroCosto = '".$fCodCentroCosto."')"; } else $dCodCentroCosto = "disabled";
if ($fCodTipoNom != "") { $cCodTipoNom = "checked"; $filtro.=" AND (e.CodTipoNom = '".$fCodTipoNom."')"; } else $dCodTipoNom = "disabled";
if ($fCodTipoTrabajador != "") { $cCodTipoTrabajador = "checked"; $filtro.=" AND (e.CodTipoTrabajador = '".$fCodTipoTrabajador."')"; } else $dCodTipoTrabajador = "disabled";
if ($fEdoReg != "") { $cEdoReg = "checked"; $filtro.=" AND (p.Estado = '".$fEdoReg."')"; } else $dEdoReg = "disabled";
if ($fSitTra != "") { $cSitTra = "checked"; $filtro.=" AND (e.Estado = '".$fSitTra."')"; } else $dSitTra = "disabled";
if ($fFingresoD != "" || $fFingresoH != "") {
	$cFingreso = "checked";
	if ($fFingresoD != "") $filtro.=" AND (e.Fingreso >= '".formatFechaAMD($fFingresoD)."')";
	if ($fFingresoH != "") $filtro.=" AND (e.Fingreso <= '".formatFechaAMD($fFingresoH)."')";
} else $dFingreso = "disabled";
if ($fBuscar != "") {
	$cBuscar = "checked";
	$filtro .= " AND (e.CodEmpleado LIKE '%".$fBuscar."%' OR
					  p.NomCompleto LIKE '%".$fBuscar."%' OR
					  p.Ndocumento LIKE '%".$fBuscar."%' OR
					  pu.DescripCargo LIKE '%".$fBuscar."%' OR
					  d.Dependencia LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
//	------------------------------------
$_SESSION["fCodOrganismo"] = $fCodOrganismo;
$_SESSION["fCodDependencia"] = $fCodDependencia;
//	------------------------------------
$_titulo = "Lista de Empleados";
$_width = 900;
if ($ventana == "selListaOpener") {
	?>
	<table width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td class="titulo"><?=$_titulo?></td>
			<td align="right"><a class="cerrar" href="javascript:" onclick="window.close();">[cerrar]</a></td>
		</tr>
	</table><hr width="100%" color="#333333" />
	<?php
}
?>

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=lista_empleados" method="post">
<input type="hidden" name="registro" id="registro" />
<input type="hidden" name="campo1" id="campo1" value="<?=$campo1?>" />
<input type="hidden" name="campo2" id="campo2" value="<?=$campo2?>" />
<input type="hidden" name="campo3" id="campo3" value="<?=$campo3?>" />
<input type="hidden" name="campo4" id="campo4" value="<?=$campo4?>" />
<input type="hidden" name="campo5" id="campo5" value="<?=$campo5?>" />
<input type="hidden" name="campo6" id="campo6" value="<?=$campo6?>" />
<input type="hidden" name="campo7" id="campo7" value="<?=$campo7?>" />
<input type="hidden" name="campo8" id="campo8" value="<?=$campo8?>" />
<input type="hidden" name="campo9" id="campo9" value="<?=$campo9?>" />
<input type="hidden" name="campo10" id="campo10" value="<?=$campo10?>" />
<input type="hidden" name="campo11" id="campo11" value="<?=$campo11?>" />
<input type="hidden" name="campo12" id="campo12" value="<?=$campo12?>" />
<input type="hidden" name="ventana" id="ventana" value="<?=$ventana?>" />
<input type="hidden" name="detalle" id="detalle" value="<?=$detalle?>" />
<input type="hidden" name="modulo" id="modulo" value="<?=$modulo?>" />
<input type="hidden" name="accion" id="accion" value="<?=$accion?>" />
<input type="hidden" name="url" id="url" value="<?=$url?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="FlagOrganismo" id="FlagOrganismo" value="<?=$FlagOrganismo?>" />
<input type="hidden" name="FlagNomina" id="FlagNomina" value="<?=$FlagNomina?>" />

<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
	<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
		<tr>
			<td align="right" width="125">Organismo:</td>
			<td>
				<input type="checkbox" <?=$cCodOrganismo?> onclick="this.checked=!this.checked" />
				<?php
				if ($FlagOrganismo == "S") {
					?>
					<select name="fCodOrganismo" id="fCodOrganismo" style="width:250px;" <?=$dCodOrganismo?> onChange="getOptionsSelect(this.value, 'dependencia_filtro', 'fCodDependencia', true, 'fCodCentroCosto');">
						<?=loadSelect2('mastorganismos','CodOrganismo','Organismo',$fCodOrganismo,1)?>
					</select>
					<?php
				} else {
					?>
					<select name="fCodOrganismo" id="fCodOrganismo" style="width:250px;" <?=$dCodOrganismo?> onChange="getOptionsSelect(this.value, 'dependencia_filtro', 'fCodDependencia', true, 'fCodCentroCosto');">
						<?php
						if ($validar == "no") loadSelect("mastorganismos", "CodOrganismo", "Organismo", $fCodOrganismo, 0);
						else getOrganismos($fCodOrganismo, 3);
						?>
					</select>
					<?php
				}
				?>
			</td>
			<td align="right" width="125">Edo. Reg: </td>
			<td>
	        	<input type="checkbox" <?=$cEdoReg?> onclick="chkFiltro(this.checked, 'fEdoReg');" />
	            <select name="fEdoReg" id="fEdoReg" style="width:143px;" <?=$dEdoReg?>>
	                <option value=""></option>
	                <?=loadSelectGeneral("ESTADO", $fEdoReg, 0)?>
	            </select>
			</td>
	        <td>&nbsp;</td>
		</tr>
		<tr>
			<td align="right">Dependencia:</td>
			<td>
				<input type="checkbox" <?=$cCodDependencia?> onclick="chkFiltro(this.checked, 'fCodDependencia');" />
				<select name="fCodDependencia" id="fCodDependencia" style="width:250px;" onChange="getOptionsSelect(this.value, 'centro_costo', 'fCodCentroCosto', true);" <?=$dCodDependencia?>>
	            	<option value="">&nbsp;</option>
					<?php
					if ($validar == "no") loadSelect("mastdependencias", "CodDependencia", "Dependencia", $fCodDependencia, 0, array('CodOrganismo'), array($fCodOrganismo));
					else getDependencias($fCodDependencia, $fCodOrganismo, 3);
					?>
				</select>
			</td>
			<td align="right">Sit. Tra.: </td>
			<td>
	        	<input type="checkbox" <?=$cSitTra?> onclick="chkFiltro(this.checked, 'fSitTra');" />
	            <select name="fSitTra" id="fSitTra" style="width:143px;" <?=$dSitTra?>>
	                <option value=""></option>
	                <?=loadSelectGeneral("ESTADO", $fSitTra, 0)?>
	            </select>
			</td>
	        <td>&nbsp;</td>
		</tr>
		<tr>
			<td align="right">Centro de Costo:</td>
			<td>
				<input type="checkbox" <?=$cCodCentroCosto?> onclick="chkFiltro(this.checked, 'fCodCentroCosto');" />
				<select name="fCodCentroCosto" id="fCodCentroCosto" style="width:250px;" <?=$dCodCentroCosto?>>
	            	<option value="">&nbsp;</option>
					<?=loadSelect("ac_mastcentrocosto", "CodCentroCosto", "Descripcion", $fCodCentroCosto, 0)?>
				</select>
			</td>
			<td align="right">Fecha de Ingreso: </td>
			<td>
				<input type="checkbox" <?=$cFingreso?> onclick="chkFiltro_2(this.checked, 'fFingresoD', 'fFingresoH');" />
				<input type="text" name="fFingresoD" id="fFingresoD" value="<?=$fFingresoD?>" <?=$dFingreso?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFecha(this);" /> -
	            <input type="text" name="fFingresoH" id="fFingresoH" value="<?=$fFingresoH?>" <?=$dFingreso?> maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFecha(this);" />
	        </td>
	        <td>&nbsp;</td>
		</tr>
		<tr>
			<td align="right">Tipo de Nomina:</td>
			<td>
	        	<?php
				if ($FlagNomina == "S") {
					?>
	                <input type="checkbox" <?=$cCodTipoNom?> onclick="this.checked=!this.checked;" />
	                <select name="fCodTipoNom" id="fCodTipoNom" style="width:250px;" <?=$dCodTipoNom?>>
	                    <?=loadSelect("tiponomina", "CodTipoNom", "Nomina", $fCodTipoNom, 1)?>
	                </select>
	                <?php
				} else {
					?>
	                <input type="checkbox" <?=$cCodTipoNom?> onclick="chkFiltro(this.checked, 'fCodTipoNom');" />
	                <select name="fCodTipoNom" id="fCodTipoNom" style="width:250px;" <?=$dCodTipoNom?>>
	                    <option value="">&nbsp;</option>
	                    <?=loadSelect("tiponomina", "CodTipoNom", "Nomina", $fCodTipoNom, 0)?>
	                </select>
	                <?php
				}
				?>
			</td>
			<td align="right">Buscar:</td>
			<td>
				<input type="checkbox" <?=$cBuscar?> onclick="chkFiltro(this.checked, 'fBuscar');" />
				<input type="text" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" style="width:245px;" <?=$dBuscar?> />
			</td>
	        <td>&nbsp;</td>
		</tr>
		<tr>
			<td align="right">Tipo de Trabajador:</td>
			<td>
				<input type="checkbox" <?=$cCodTipoTrabajador?> onclick="chkFiltro(this.checked, 'fCodTipoTrabajador');" />
				<select name="fCodTipoTrabajador" id="fCodTipoTrabajador" style="width:250px;" <?=$dCodTipoTrabajador?>>
	            	<option value="">&nbsp;</option>
					<?=loadSelect("rh_tipotrabajador", "CodTipoTrabajador", "TipoTrabajador", $fCodTipoTrabajador, 0)?>
				</select>
			</td>
	        <td>&nbsp;</td>
	        <td>&nbsp;</td>
	        <td align="right"><input type="submit" value="Buscar"></td>
		</tr>
	</table>
</div>
<div class="sep"></div>

<center>
<div class="scroll" style="overflow:scroll; height:235px; width:100%; min-width:<?=$_width?>px;">
<table class="tblLista" style="width:100%; min-width:1700px;">
	<thead>
		<tr>
	        <th width="60" onclick="order('CodEmpleado')">C&oacute;digo</th>
	        <th width="400" onclick="order('NomCompleto')">Nombre Completo</th>
	        <th width="75" onclick="order('LENGTH(Ndocumento), Ndocumento')">Documento</th>
	        <th width="75" onclick="order('Fingreso')">F. Ingreso</th>
	        <th width="500" onclick="order('DescripCargo')">Cargo</th>
	        <th onclick="order('Dependencia')">Dependencia</th>
	    </tr>
    </thead>
    
    <tbody>
	<?php
	//	consulto todos
	$sql = "SELECT e.CodEmpleado
			FROM
				mastempleado e
				INNER JOIN mastpersonas p ON (e.CodPersona = p.CodPersona)
				INNER JOIN mastdependencias d ON (e.CodDependencia = d.CodDependencia)
				INNER JOIN mastorganismos o ON (o.codOrganismo = e.CodOrganismo)
				INNER JOIN rh_puestos pu ON (e.CodCargo = pu.CodCargo)
				LEFT JOIN rh_puestos pu2 ON (e.CodCargoTemp = pu2.CodCargo)
				LEFT JOIN mastmiscelaneosdet md ON (md.CodDetalle = pu.CategoriaCargo AND
													 md.CodMaestro = 'CATCARGO')
			WHERE 1 $filtro";
	$rows_total = getNumRows3($sql);
	//	consulto lista
	$sql = "SELECT
				e.CodEmpleado,
				e.CodOrganismo,
				e.CodDependencia,
				e.CodCentroCosto,
				e.CodCargo,
				e.Fingreso,
				e.Estado,
				e.SueldoActual,
				e.CodHorario,
				e.CodCargoTemp,
				p.CodPersona,
				p.NomCompleto,
				p.Ndocumento,
				p.DocFiscal,
				p.Fnacimiento,
				p.Sexo,
				d.Dependencia,
				o.Organismo,
				pu.DescripCargo,
				pu.Grado,
				pu.NivelSalarial,
				pu2.DescripCargo AS DescripCargoTemp,
				md.Descripcion AS NomCategoriaCargo
			FROM
				mastempleado e
				INNER JOIN mastpersonas p ON (e.CodPersona = p.CodPersona)
				INNER JOIN mastdependencias d ON (e.CodDependencia = d.CodDependencia)
				INNER JOIN mastorganismos o ON (o.codOrganismo = e.CodOrganismo)
				INNER JOIN rh_puestos pu ON (e.CodCargo = pu.CodCargo)
				LEFT JOIN rh_puestos pu2 ON (e.CodCargoTemp = pu2.CodCargo)
				LEFT JOIN mastmiscelaneosdet md ON (md.CodDetalle = pu.CategoriaCargo AND
													 md.CodMaestro = 'CATCARGO')
			WHERE 1 $filtro
			ORDER BY $fOrderBy
			LIMIT ".intval($limit).", $maxlimit";
	$field = getRecords($sql);
	$rows_lista = count($field);
	foreach($field as $f) {
		if ($ventana == "listado_insertar_linea") {
			?><tr class="trListaBody" onclick="listado_insertar_linea('<?=$detalle?>', 'modulo=<?=$modulo?>&accion=<?=$accion?>&CodPersona=<?=$f['CodPersona']?>', '<?=$f['CodPersona']?>', '<?=$url?>');"><?php
		}
		elseif ($ventana == "pr_proyrecursos") {
			?><tr class="trListaBody" onclick="listado_insertar_linea('<?=$detalle?>', 'modulo=<?=$modulo?>&accion=<?=$accion?>&CodPersona=<?=$f['CodPersona']?>&CodOrganismo=<?=$f['CodOrganismo']?>', '<?=$f['CodPersona']?>', '<?=$url?>');"><?php
		}
		elseif ($ventana == "listado_insertar_linea_capacitaciones") {
			?>
        	<tr class="trListaBody" onclick="listado_insertar_linea_capacitaciones('<?=$detalle?>', 'modulo=<?=$modulo?>&accion=<?=$accion?>&CodPersona=<?=$f['CodPersona']?>', '<?=$f['CodPersona']?>', '<?=$url?>');">
        	<?php
		}
		elseif ($ventana == "prestaciones_filtro") {
			$Tiempo = edad(formatFechaDMA($f['Fingreso']), formatFechaDMA($FechaActual));
			?>
        	<tr class="trListaBody" onClick="selLista(['<?=$f['CodPersona']?>','<?=$f['NomCompleto']?>','<?=$f['Ndocumento']?>','<?=formatFechaDMA($f['Fingreso'])?>','<?=$Tiempo['Anios']?>','<?=$Tiempo['Meses']?>','<?=$Tiempo['Dias']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>','<?=$campo4?>','<?=$campo5?>','<?=$campo6?>','<?=$campo7?>']);">
        	<?php
		}
		elseif ($ventana == "dependencias") {
			if ($f['CodCargoTemp']) {
				$CodCargo = $f['CodCargoTemp'];
				$DescripCargo = $f['DescripCargoTemp'];
			} else {
				$CodCargo = $f['CodCargo'];
				$DescripCargo = $f['DescripCargo'];
			}
			?>
        	<tr class="trListaBody" onClick="selLista(['<?=$f['CodPersona']?>','<?=$f['NomCompleto']?>','<?=$CodCargo?>','<?=$DescripCargo?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>','<?=$campo4?>']);">
        	<?php
		}
		elseif ($ventana == "fideicomiso_calculo_empleado_sel") {
			?>
        	<tr class="trListaBody" onClick="fideicomiso_calculo_empleado_sel('<?=$f['CodPersona']?>');">
        	<?php
		}
		elseif ($ventana == "caja_chica_beneficiario") {
			$sql = "SELECT Monto
					FROM ap_cajachicaautorizacion
					WHERE
						CodOrganismo = '".$f['CodOrganismo']."' AND
						CodEmpleado = '".$f['CodPersona']."'";
			$MontoAutorizado = getVar3($sql);
			?>
            <tr class="trListaBody" onclick="caja_chica_beneficiario('<?=$f['CodPersona']?>','<?=$f['NomCompleto']?>',<?=floatval($MontoAutorizado)?>);">
        	<?php
		}
		elseif ($ventana == "at_soportetecnico")  {
			##	consulto activo por defecto
			$sql = "SELECT
						a.Activo,
						a.Descripcion,
						at.Usuario
					FROM
						af_activo a
						INNER JOIN at_activotecnologico at ON (at.Activo = a.Activo)
					WHERE
						a.EmpleadoUsuario = '".$field['SolicitadoPor']."' AND
						a.ActivoConsolidado = '' AND
						at.Usuario <> ''
					LIMIT 0, 1";
			$field_activo = getRecord($sql);
			?>
        	<tr class="trListaBody" onClick="selLista(['<?=$f['CodPersona']?>','<?=$f['NomCompleto']?>','<?=$f['CodEmpleado']?>','<?=$field_activo['Activo']?>','<?=$field_activo['Descripcion']?>','<?=$field_activo['Usuario']?>','<?=$f['CodOrganismo']?>','<?=$f['Organismo']?>','<?=$f['CodDependencia']?>','<?=$f['Dependencia']?>','<?=$f['CodCargo']?>','<?=$f['DescripCargo']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>','<?=$campo4?>','<?=$campo5?>','<?=$campo6?>','<?=$campo7?>','<?=$campo8?>','<?=$campo9?>','<?=$campo10?>','<?=$campo11?>','<?=$campo12?>']);">
        	<?php
		}
		elseif ($ventana == "at_prestamos")  {
			?>
        	<tr class="trListaBody" onClick="sellista_at_prestamos(['<?=$f['CodPersona']?>','<?=$f['NomCompleto']?>','<?=$f['CodEmpleado']?>','<?=$f['CodOrganismo']?>','<?=$f['Organismo']?>','<?=$f['CodDependencia']?>','<?=$f['Dependencia']?>','<?=($f['CodCargoTemp']?$f['CodCargoTemp']:$f['CodCargo'])?>','<?=($f['DescripCargoTemp']?$f['DescripCargoTemp']:$f['DescripCargo'])?>','<?=$f['CodDependencia']?>','<?=$f['Dependencia']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>','<?=$campo4?>','<?=$campo5?>','<?=$campo6?>','<?=$campo7?>','<?=$campo8?>','<?=$campo9?>','<?=$campo10?>','<?=$campo11?>']);">
        	<?php
		}
		elseif ($ventana == "pr_complemento_sueldos_dias")  {
			?>
        	<tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['CodPersona']?>','<?=$f['NomCompleto']?>','<?=$f['CodEmpleado']?>','<?=$f['Ndocumento']?>','<?=$f['CodDependencia']?>','<?=$f['CodCargo']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>','<?=$campo4?>','<?=$campo5?>','<?=$campo6?>']);">
        	<?php
		}
		elseif ($ventana == "pr_complemento_sueldos_horas")  {
			?>
        	<tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['CodPersona']?>','<?=$f['NomCompleto']?>','<?=$f['CodEmpleado']?>','<?=$f['Ndocumento']?>','<?=$f['CodDependencia']?>','<?=$f['CodCargo']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>','<?=$campo4?>','<?=$campo5?>','<?=$campo6?>']);">
        	<?php
		}
		else {
			?>
        	<tr class="trListaBody" onClick="<?=$ventana?>(['<?=$f['CodPersona']?>','<?=$f['NomCompleto']?>','<?=$f['CodEmpleado']?>'], ['<?=$campo1?>','<?=$campo2?>','<?=$campo3?>']);">
        	<?php
		}
		?>
			<td align="center"><?=$f['CodEmpleado']?></td>
			<td><?=htmlentities($f['NomCompleto'])?></td>
			<td><?=$f['Ndocumento']?></td>
			<td align="center"><?=formatFechaDMA($f['Fingreso'])?></td>
			<td><?=htmlentities($f['DescripCargo'])?></td>
			<td><?=htmlentities($f['Dependencia'])?></td>
		</tr>
		<?php
	}
	?>
    </tbody>
</table>
</div>
<table style="width:100%; min-width:<?=$_width?>px;">
	<tr>
    	<td>
        	Mostrar: 
            <select name="maxlimit" style="width:50px;" onchange="this.form.submit();">
                <?=loadSelectGeneral("MAXLIMIT", $maxlimit, 0)?>
            </select>
        </td>
        <td align="right">
        	<?=paginacion(intval($rows_total), intval($rows_lista), intval($maxlimit), intval($limit));?>
        </td>
    </tr>
</table>
</center>
</form>

<script type="text/javascript" language="javascript">
<?php
	if ($ventana == "fideicomiso_calculo_empleado_sel") {
		?>
		// 	funcion para seleccionar de una lista un registro y colocar su valor en la ventana que lo llamo
		function fideicomiso_calculo_empleado_sel(CodPersona) {
			//	ajax
			$.ajax({
				type: "POST",
				url: "../../nomina/lib/fphp_funciones_ajax.php",
				data: "accion=fideicomiso_calculo_empleado_sel&CodPersona="+CodPersona,
				async: false,
				success: function(resp) {
					var partes = resp.split("|");
					parent.$("#CodPersona").val(partes[1]);
					parent.$("#NomCompleto").val(partes[2]);
					parent.$("#Ndocumento").val(partes[3]);
					parent.$("#Anios").val(partes[4]);
					parent.$("#Meses").val(partes[5]);
					parent.$("#Dias").val(partes[6]);
					parent.$("#Fingreso").val(partes[7]);
					parent.$("#listaCalculoCuerpo").html("");
					parent.$.prettyPhoto.close();
				}
			});
		}
		<?php
	}
	elseif ($ventana == "caja_chica_beneficiario") {
		?>
		// 	funcion para seleccionar de una lista un registro y colocar su valor en la ventana que lo llamo
		function caja_chica_beneficiario(CodPersona, NomCompleto, MontoAutorizado) {
			parent.$("#CodBeneficiario").val(CodPersona);
			parent.$("#NomBeneficiario").val(NomCompleto);
			parent.$("#CodPersonaPagar").val(CodPersona);
			parent.$("#NomPersonaPagar").val(NomCompleto);
			parent.$("#MontoAutorizado").val(MontoAutorizado).formatCurrency();
			if (MontoAutorizado > 0) caja_chica_habilitar_parent(true); else caja_chica_habilitar_parent(false);
			parent.$.prettyPhoto.close();
		}
		//	habilitar / deshabilitar inputs lista
		function caja_chica_habilitar_parent(boo) {
			parent.$(".iEditable").prop("disabled", !boo);
			parent.$(".bEditable").prop("disabled", !boo);
			if (boo) {
				parent.$(".aEditable").css("visibility", "visible");
				parent.$("#nocumple").css("display", "none");
			} else {
				parent.$(".aEditable").css("visibility", "hidden");
				parent.$("#nocumple").css("display", "block");
			}
		}
		<?php
	}
	elseif ($ventana == "at_prestamos") {
		?>
		function sellista_at_prestamos(valores, inputs) {
			if (inputs) {
				for(var i=0; i<inputs.length; i++) {
					if (parent.$("#"+inputs[i]).length > 0) parent.$("#"+inputs[i]).val(valores[i]);
				}
			}
			parent.$('#lista_activos').html('');
			parent.$.prettyPhoto.close();
		}
		<?php
	}
	elseif ($ventana == "listado_insertar_linea_capacitaciones") {
		?>
		function listado_insertar_linea_capacitaciones(detalle, data, id, url) {
			//	lista
			var nro_detalles = parent.$("#nro_"+detalle);
			var can_detalles = parent.$("#can_"+detalle);
			var lista_detalles = parent.$("#lista_"+detalle);
			var nro = new Number(nro_detalles.val());	nro++;
			var can = new Number(can_detalles.val());	can++;
			if (!id) var idtr = detalle+"_"+nro; else var idtr = detalle+"_"+id;
			if (!url) var url = "../fphp_funciones_ajax.php";
			//	ajax
			$.ajax({
				type: "POST",
				url: url,
				data: "nro_detalles="+nro+"&can_detalles="+can+"&"+data,
				async: false,
				success: function(resp) {
					if (parent.document.getElementById(idtr)) cajaModal("Registro ya insertado", "error_lista", 400);
					else {
						nro_detalles.val(nro);
						can_detalles.val(can);
						lista_detalles.append(resp);
						parent.$.prettyPhoto.close();
						inicializarParent();
						parent.$('#Participantes').val(can);
					}
				}
			});
		}
		<?php
	}
	elseif ($ventana == "pr_complemento_sueldos_dias") {
		?>
		function pr_complemento_sueldos_dias(valores, inputs) {
			//	ajax
			$.ajax({
				type: "POST",
				url: "../../nomina/pr_complemento_sueldos_ajax.php",
				data: "modulo=ajax&accion=obtener_complementos_dias&CodPersona="+valores[0]+"&CodOrganismo="+parent.$("#CodOrganismo").val()+"&CodTipoNom="+parent.$("#CodTipoNom").val()+"&Periodo="+parent.$("#Periodo").val()+"&CodTipoProceso="+parent.$("#CodTipoProceso").val(),
				async: false,
				success: function(resp) {
					parent.$('#lista_detalle').html(resp);
					if (inputs) {
						for(var i=0; i<inputs.length; i++) {
							if (parent.$("#"+inputs[i]).length > 0) parent.$("#"+inputs[i]).val(valores[i]);
						}
					}
					parent.$.prettyPhoto.close();
				}
			});
		}
		<?php
	}
	elseif ($ventana == "pr_complemento_sueldos_horas") {
		?>
		function pr_complemento_sueldos_horas(valores, inputs) {
			//	ajax
			$.ajax({
				type: "POST",
				url: "../../nomina/pr_complemento_sueldos_ajax.php",
				data: "modulo=ajax&accion=obtener_complementos_horas&CodPersona="+valores[0]+"&CodOrganismo="+parent.$("#CodOrganismo").val()+"&CodTipoNom="+parent.$("#CodTipoNom").val()+"&Periodo="+parent.$("#Periodo").val()+"&CodTipoProceso="+parent.$("#CodTipoProceso").val(),
				async: false,
				success: function(resp) {
					parent.$('#lista_detalle').html(resp);
					if (inputs) {
						for(var i=0; i<inputs.length; i++) {
							if (parent.$("#"+inputs[i]).length > 0) parent.$("#"+inputs[i]).val(valores[i]);
						}
					}
					parent.$.prettyPhoto.close();
				}
			});
		}
		<?php
	}
?>
</script>