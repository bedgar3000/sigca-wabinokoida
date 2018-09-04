<?php
$FechaActual = "$AnioActual-$MesActual-$DiaActual";
$PeriodoActual = "$AnioActual-$MesActual";
//	------------------------------------
if ($opcion == "nuevo") {
	$field['Estado'] = "PE";
	$field['CodOrganismo'] = $_SESSION["ORGANISMO_ACTUAL"];
	$field['Periodo'] = $PeriodoActual;
	$field['PreparadoPor'] = $_SESSION["CODPERSONA_ACTUAL"];
	$field['NomPreparadoPor'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
	$field['FechaPreparacion'] = $FechaActual;
	$field['CodTipoNom'] = $_SESSION["NOMINA_ACTUAL"];
	##
	$_titulo = "Nuevo Ajuste";
	$accion = "nuevo";
	$label_submit = "Guardar";
	$disabled_nuevo = "disabled";
	$focus = "NroResolucion";
	$clkCancelar = "document.getElementById('frmentrada').submit();";
	$ajuste_salarial_emp_nomina_mostrar = "ajuste_salarial_emp_nomina_mostrar($('#CodTipoNom').val());";
}
elseif ($opcion == "modificar" || $opcion == "ver" || $opcion == "aprobar" || $opcion == "anular") {
	//	consulto datos generales
	list($CodOrganismo, $Periodo, $Secuencia) = split("[_]", $sel_registros);
	$sql = "SELECT
				ase.*,
				p1.NomCompleto AS NomPreparadoPor,
				p2.NomCompleto AS NomAprobadoPor
			FROM
				pr_ajustesalarialemp ase
				LEFT JOIN mastpersonas p1 ON (p1.CodPersona = ase.PreparadoPor)
				LEFT JOIN mastpersonas p2 ON (p2.CodPersona = ase.AprobadoPor)
			WHERE
				ase.CodOrganismo = '".$CodOrganismo."' AND
				ase.Periodo = '".$Periodo."' AND
				ase.Secuencia = '".$Secuencia."'";
	$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	if (mysql_num_rows($query)) $field = mysql_fetch_array($query);
	##
	if ($opcion == "modificar") {
		$_titulo = "Modificar Ajuste";
		$accion = "modificar";
		$disabled_modificar = "disabled";
		$label_submit = "Modificar";
		$focus = "Descripcion";
	}
	##
	elseif ($opcion == "ver") {
		$_titulo = "Ver Ajuste";
		$disabled_nuevo = "disabled";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$display_submit = "display:none;";
		$focus = "btCancelar";
	}
	##
	elseif ($opcion == "aprobar") {
		$field['AprobadoPor'] = $_SESSION["CODPERSONA_ACTUAL"];
		$field['NomAprobadoPor'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
		$field['FechaAprobado'] = $FechaActual;
		##
		$_titulo = "Aprobar Ajuste";
		$accion = "aprobar";
		$disabled_nuevo = "disabled";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$label_submit = "Aprobar";
		$focus = "btSubmit";
	}
	##
	elseif ($opcion == "anular") {
		$_titulo = "Anular Ajuste";
		$accion = "anular";
		$disabled_nuevo = "disabled";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$label_submit = "Anular";
		$focus = "btSubmit";
	}
	$clkCancelar = "document.getElementById('frmentrada').submit();";
	$ajuste_salarial_emp_nomina_mostrar = "ajuste_salarial_emp_nomina_mostrar_ver($('#CodTipoNom').val());";
}
//	------------------------------------
$_width = 750;
$_sufijo = "ajuste_salarial_emp";
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<table width="<?=$_width?>" align="center" cellpadding="0" cellspacing="0">
	<tr>
    	<td>
            <div class="header">
            <ul id="tab">
            <!-- CSS Tabs -->
            <li id="li1" onclick="currentTab('tab', this);" class="current">
            	<a href="#" onclick="mostrarTab('tab', 1, 2);">Informaci&oacute;n General</a>
            </li>
            <li id="li2" onclick="currentTab('tab', this); <?=$ajuste_salarial_emp_nomina_mostrar?>">
            	<a href="#" onclick="mostrarTab('tab', 2, 2);">Ajustes</a>
            </li>
            </ul>
            </div>
        </td>
    </tr>
</table>

<div id="tab1" style="display:block;">
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=pr_<?=$_sufijo?>_lista" method="POST" enctype="multipart/form-data" onsubmit="return <?=$_sufijo?>(this, '<?=$accion?>');" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
<input type="hidden" name="fAnio" id="fAnio" value="<?=$fAnio?>" />
<table width="<?=$_width?>" class="tblForm">
	<tr>
    	<td colspan="4" class="divFormCaption">Informaci&oacute;n General</td>
    </tr>
    <tr>
		<td class="tagForm" width="135">* Organismo:</td>
		<td>
			<select id="CodOrganismo" style="width:300px;" <?=$disabled_modificar?>>
				<?=getOrganismos($field['CodOrganismo'], 3)?>
			</select>
		</td>
		<td class="tagForm" width="100"><strong>Estado:</strong></td>
		<td>
        	<input type="hidden" id="Estado" value="<?=$field['Estado']?>" />
        	<input type="text" value="<?=strtoupper(printValores("ESTADO-AJUSTE", $field['Estado']))?>" style="width:100px;" class="codigo" disabled />
        </td>
	</tr>
    <tr>
		<td class="tagForm">* Nro. Resoluci&oacute;n:</td>
		<td>
            <input type="text" id="NroResolucion" value="<?=htmlentities($field['NroResolucion'])?>" style="width:125px;" maxlength="15" <?=$disabled_ver?> />
		</td>
		<td class="tagForm"><strong>Periodo:</strong></td>
		<td>
        	<input type="text" id="Periodo" value="<?=$field['Periodo']?>" style="width:50px;" class="codigo" disabled />
        	<input type="text" id="Secuencia" value="<?=$field['Secuencia']?>" style="width:40px;" class="codigo" disabled />
        </td>
    </tr>
    <tr>
		<td class="tagForm">Nro. Gaceta:</td>
		<td>
            <input type="text" id="NroGaceta" value="<?=htmlentities($field['NroGaceta'])?>" style="width:125px;" maxlength="15" <?=$disabled_ver?> />
		</td>
		<td class="tagForm">* N&oacute;mina:</td>
		<td>
        	<select id="CodTipoNom" style="width:108px;" <?=$disabled_ver?>>
				<?=loadSelect("tiponomina", "CodTipoNom", "Nomina", $field['CodTipoNom'], 0)?>
			</select>
        </td>
    </tr>
    <tr>
		<td class="tagForm">* Descripci&oacute;n:</td>
		<td colspan="3">
            <textarea id="Descripcion" style="width:95%; height:40px;" <?=$disabled_ver?>><?=htmlentities($field['Descripcion'])?></textarea>
		</td>
    </tr>
	<tr>
		<td class="tagForm">Preparado Por:</td>
		<td>
            <input type="hidden" id="PreparadoPor" value="<?=$field['PreparadoPor']?>" />
            <input type="text" id="NomPreparadoPor" value="<?=htmlentities($field['NomPreparadoPor'])?>" style="width:295px;" disabled="disabled" />
        </td>
		<td class="tagForm">Fecha:</td>
		<td>
        	<input type="text" id="FechaPreparacion" value="<?=formatFechaDMA($field['FechaPreparacion'])?>" style="width:65px;" disabled="disabled" />
        </td>
	</tr>
	<tr>
		<td class="tagForm">Aprobado Por:</td>
		<td>
            <input type="hidden" id="AprobadoPor" value="<?=$field['AprobadoPor']?>" />
            <input type="text" id="NomAprobadoPor" value="<?=htmlentities($field['NomAprobadoPor'])?>" style="width:295px;" disabled="disabled" />
        </td>
		<td class="tagForm">Fecha:</td>
		<td>
            <input type="text" id="FechaAprobado" value="<?=formatFechaDMA($field['FechaAprobado'])?>" style="width:65px;" disabled="disabled" />
        </td>
	</tr>
    <tr>
		<td class="tagForm">&Uacute;ltima Modif.:</td>
		<td colspan="3">
			<input type="text" size="30" value="<?=$field['UltimoUsuario']?>" disabled="disabled" />
			<input type="text" size="25" value="<?=$field['UltimaFecha']?>" disabled="disabled" />
		</td>
	</tr>
</table>
<center>
<input type="submit" value="<?=$label_submit?>" id="btSubmit" style="width:75px; <?=$display_submit?>" />
<input type="button" value="Cancelar" style="width:75px;" id="btCancelar" onclick="<?=$clkCancelar?>" />
</center>
</form>
</div>

<center>
<div id="tab2" style="display:none;">
<form name="frm_ajustes" id="frm_ajustes">
    <?php
	$i=0;
    //	consulto lista
	if ($opcion == "nuevo") $CampoSueldo = "e.SueldoActual AS SueldoBasico"; else $CampoSueldo = "asa.SueldoBasico";
    $sql = "SELECT
				p.CodPersona,
				p.NomCompleto,
				e.CodEmpleado,
				e.CodTipoNom,
				$CampoSueldo,
				tn.Nomina,
				asa.Secuencia,
				asa.SueldoPromedio AS SueldoNuevo,
				asa.Porcentaje,
				asa.Monto
			FROM
				mastpersonas p
				INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
				INNER JOIN tiponomina tn ON (tn.CodTipoNom = e.CodTipoNom)
				LEFT JOIN pr_ajustesalarialajustesemp asa ON (asa.CodPersona = p.CodPersona AND
															  asa.CodOrganismo = '".$CodOrganismo."' AND
															  asa.Periodo = '".$Periodo."' AND
															  asa.Secuencia = '".$Secuencia."')
			ORDER BY Nomina, CodTipoNom, CodPersona";
    $query_ajustes = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
    while ($field_ajustes = mysql_fetch_array($query_ajustes)) {
        $id = "$field_ajustes[CodPersona]";
		if ($field_ajustes['Porcentaje'] > 0 || $field_ajustes['Monto'] > 0) {
			$checked = "checked";
			$readonly_ajustes = "";
		} else {
			$checked = "";
			$readonly_ajustes = "readonly";
		}
		if ($disabled_ver == "disabled") $readonly_ajustes = "readonly";
		if ($Grupo != $field_ajustes['CodTipoNom']) {
			$Grupo = $field_ajustes['CodTipoNom'];
			$j=0;
			if ($i > 0) { ?></tbody></table></div><?php }
			?>
            <div style="overflow:scroll; width:<?=$_width?>px; height:400px; display:none;" class="lista <?=$field_ajustes['CodTipoNom']?>">
            <table width="100%" class="tblLista">
                <thead>
                <tr>
                    <th scope="col" width="15"></th>
                    <th scope="col" colspan="2">Grado</th>
                    <th scope="col" align="left">Descripci&oacute;n</th>
                    <th scope="col" width="70" align="right">Sueldo Actual</th>
                    <th scope="col" width="70" align="right">Porcentaje Aumentar</th>
                    <th scope="col" width="70" align="right">Monto Aumentar</th>
                    <th scope="col" width="70" align="right">Sueldo Nuevo</th>
                </tr>
                </thead>
                
                <tbody>
                <tr class="trListaBody2">
                    <td colspan="4" align="left"><?=htmlentities($field_ajustes['Nomina'])?></td>
                </tr>
            <?php
		}
        ?>
        <tr class="trListaBody">
            <th><?=++$j?></th>
            <td align="center" width="15">
            	<input type="checkbox" name="CodPersona" id="CodPersona<?=$id?>" value="<?=$id?>" class="<?=$field_ajustes['CodTipoNom']?>" onclick="ajuste_salarial_emp_check('<?=$id?>');" <?=$disabled_ver?> <?=$checked?> />
            </td>
            <td align="center" width="40">
				<?=$field_ajustes['CodEmpleado']?>
            </td>
            <td align="left">
				<?=htmlentities($field_ajustes['NomCompleto'])?>
            </td>
            <td align="right">
            	<input type="text" name="SueldoBasico" id="SueldoBasico<?=$id?>" value="<?=number_format($field_ajustes['SueldoBasico'], 2, ',', '.')?>" style="text-align:right;" class="cell2" readonly="readonly" />
            </td>
            <td align="right">
            	<input type="text" name="Porcentaje" id="Porcentaje<?=$id?>" value="<?=number_format($field_ajustes['Porcentaje'], 2, ',', '.')?>" style="text-align:right;" class="cell currency Porcentaje<?=$field_ajustes['CodTipoNom']?>" onchange="ajuste_salarial_montos('<?=$id?>');" <?=$readonly_ajustes?> />
            </td>
            <td align="right">
            	<input type="text" name="Monto" id="Monto<?=$id?>" value="<?=number_format($field_ajustes['Monto'], 2, ',', '.')?>" style="text-align:right;" class="cell currency Monto<?=$field_ajustes['CodTipoNom']?>" onchange="ajuste_salarial_montos('<?=$id?>');" <?=$readonly_ajustes?> />
            </td>
            <td align="right">
            	<input type="text" name="SueldoNuevo" id="SueldoNuevo<?=$id?>" value="<?=number_format($field_ajustes['SueldoNuevo'], 2, ',', '.')?>" style="text-align:right; font-weight:bold;" class="cell2 SueldoNuevo<?=$field_ajustes['CodTipoNom']?>" readonly />
        </tr>
        <?php
		++$i;
    }
	if ($i > 0) { ?></tbody></table></div><?php }
    ?>
</form>
</div>
</center>

<div style="width:<?=$_width?>px" class="divMsj">Campos Obligatorios *</div>