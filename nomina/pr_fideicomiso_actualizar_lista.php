<?php
//	------------------------------------
if ($filtrar == "default") {
	$fCodOrganismo = $_SESSION["ORGANISMO_ACTUAL"];
	$fCodTipoNom = $_SESSION["NOMINA_ACTUAL"];
	$fPeriodo = "$AnioActual-$MesActual";
	$fOrderBy = "LENGTH(Ndocumento),Ndocumento";
	$maxlimit = $_SESSION["MAXLIMIT"];
}
$filtro = "";
if ($fEstado == "") $filtro.=" AND (e.Estado = 'A')"; else $cEstado = "checked";
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (tne.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fCodTipoNom != "") { $cCodTipoNom = "checked"; $filtro.=" AND (tne.CodTipoNom = '".$fCodTipoNom."')"; } else $dCodTipoNom = "disabled";
if ($fPeriodo != "") { $cPeriodo = "checked"; $filtro.=" AND (tne.Periodo = '".$fPeriodo."')"; } else $dPeriodo = "disabled";
//	------------------------------------
$_titulo = "Actualizar Dep&oacute;sito de Antiguedad";
$_width = 950;
$_sufijo = "fideicomiso_actualizar";
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=pr_<?=$_sufijo?>_lista" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="sel_registros" id="sel_registros" />

<div class="divBorder" style="width:<?=$_width?>px;">
<table width="<?=$_width?>" class="tblFiltro">
	<tr>
		<td align="right" width="125">Organismo:</td>
		<td>
			<input type="checkbox" checked onclick="this.checked=!this.checked" />
			<select name="fCodOrganismo" id="fCodOrganismo" style="width:275px;" onChange="loadSelect($('#fCodTipoNom'), 'tabla=loadControlNominas3&CodOrganismo='+this.value, 1, destinos=['fCodTipoNom', 'fPeriodo']);">
				<?=getOrganismos($fCodOrganismo, 3)?>
			</select>
		</td>
		<td align="right" width="125">N&oacute;mina:</td>
		<td>
			<input type="checkbox" checked onclick="this.checked=!this.checked" />
			<select name="fCodTipoNom" id="fCodTipoNom" style="width:250px;" onChange="loadSelect($('#fPeriodo'), 'tabla=loadControlPeriodos3&CodOrganismo='+$('#fCodOrganismo').val()+'&CodTipoNom='+this.value, 1, destinos=['fPeriodo']);">
				<?=loadControlNominas3($fCodOrganismo, $fCodTipoNom)?>
			</select>
		</td>
	</tr>
	<tr>
		<td align="right">Periodo:</td>
		<td>
			<input type="checkbox" checked onclick="this.checked=!this.checked" />
            <select name="fPeriodo" id="fPeriodo" style="width:75px;">
				<?=loadControlPeriodos3($fCodOrganismo, $fCodTipoNom, $fPeriodo)?>
            </select>
		</td>
		<td align="right">&nbsp;</td>
		<td>
            <input type="checkbox" name="fEstado" id="fEstado" value="A" <?=$cEstado?> /> Mostrar cesados
		</td>
	</tr>
</table>
</div>
<center><input type="submit" value="Buscar"></center>
<br />

<center>
<table width="<?=$_width?>" class="tblBotones">
    <tr>
        <td style="padding:5px;">
        	<a class="link" href="#" onclick="seleccionarTodos('personas');">Todos</a> |
            <a class="link" href="#" onclick="seleccionarNinguno('personas');">Ninguno</a>
        </td>
        <td align="right">
            <input type="button" id="btActualizar" value="Actualizar Acumulados" style="width:120px;" onClick="actualizar(this.form)" /> |
            <input type="button" id="btTXT" value="Generar TXT" style="width:80px;" disabled="disabled" />
            <input type="button" id="btImprimir" value="Imprimir" style="width:80px;" onclick="abrirReporte(document.getElementById('frmentrada'), 'a_reporte', 'pr_fideicomiso_actualizar_pdf')" />
        </td>
    </tr>
</table>

<div style="overflow:scroll; width:<?=$_width?>px; height:350px;">
<table width="100%" class="tblLista">
    <thead>
    <tr>
        <th width="15"></th>
		<th width="20">Tr.</th>
		<th width="50">C&oacute;digo</th>
		<th align="left">Nombre</th>
		<th width="70">Nro. Documento</th>
		<th width="125">Cuenta</th>
		<th width="60">Monto</th>
		<th width="35">Dias</th>
		<th width="60">Compl. Adic.</th>
		<th width="35">Dias Adic.</th>
    </tr>
    </thead>
    
    <tbody id="lista_personas">
    <?php
	if ($fCodOrganismo != "" && $fCodTipoNom != "" && $fPeriodo != "") {
		list($_AnioProceso, $_MesProceso) = explode('-', $fPeriodo);
		if ($_MesProceso <= '03') $_InicioTri = "$_AnioProceso-01-01";
		elseif ($_MesProceso <= '06') $_InicioTri = "$_AnioProceso-04-01";
		elseif ($_MesProceso <= '09') $_InicioTri = "$_AnioProceso-07-01";
		elseif ($_MesProceso <= '12') $_InicioTri = "$_AnioProceso-10-01";
		//	consulto lista
		$sql = "SELECT
					p.CodPersona,
					p.NomCompleto,
					p.Ndocumento,
					e.CodEmpleado,
					e.Fingreso,
					e.Estado,
					e.Fegreso,
					e.FechaFinNomina,
					SUBSTRING(e.Fingreso,1,4) AS AnioIngreso,
					SUBSTRING(e.Fingreso,1,7) AS PeriodoIngreso,
					SUBSTRING(e.Fingreso,6,2) AS MesIngreso,
					SUBSTRING(e.Fingreso,9,2) AS DiaIngreso,
					SUBSTRING(e.Fegreso,1,4) AS AnioEgreso,
					SUBSTRING(e.Fegreso,1,7) AS PeriodoEgreso,
					SUBSTRING(e.Fegreso,6,2) AS MesEgreso,
					SUBSTRING(e.Fegreso,9,2) AS DiaEgreso,
					SUBSTRING(e.FechaFinNomina,1,4) AS AnioFin,
					SUBSTRING(e.FechaFinNomina,1,7) AS PeriodoFin,
					SUBSTRING(e.FechaFinNomina,6,2) AS MesFin,
					SUBSTRING(e.FechaFinNomina,9,2) AS DiaFin,
					pp.FechaDesde,
					pp.FechaHasta,
					bp.Ncuenta,
					tne.Periodo,
					tne.CodTipoProceso,
					s.SueldoNormal AS TotalIngresos,
					tnec.Cantidad,
					tnec.Monto,
					tnecs.Cantidad AS DiasTrabajados,
					s.AliVac,
					s.AliFin,
					SUBSTRING(s.Periodo,1,4) AS Anio,
					SUBSTRING(s.Periodo,6,2) AS Mes,
					ti.Porcentaje AS Tasa,
					(SELECT COUNT(*)
					 FROM pr_acumuladofideicomisodetalle afd
					 WHERE
						afd.CodOrganismo = '".$fCodOrganismo."' AND
						afd.Periodo = '".$fPeriodo."' AND
						afd.CodPersona = tne.CodPersona) AS FlagTransferido
				FROM
					pr_tiponominaempleado tne
					INNER JOIN mastpersonas p ON (p.CodPersona = tne.CodPersona)
					INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
					INNER JOIN rh_sueldos s ON (s.CodPersona = tne.CodPersona AND 
												s.Periodo = tne.Periodo)
					INNER JOIN pr_procesoperiodo pp ON (tne.CodTipoNom = pp.CodTipoNom AND
														tne.Periodo = pp.Periodo AND
														tne.CodOrganismo = pp.CodOrganismo AND
														tne.CodTipoProceso = pp.CodTipoProceso)
					LEFT JOIN pr_tiponominaempleadoconcepto tnec ON (tnec.CodTipoNom = tne.CodTipoNom AND
																	 tnec.Periodo = tne.Periodo AND
																	 tnec.CodPersona = tne.CodPersona AND
																	 tnec.CodOrganismo = tne.CodOrganismo AND
																	 tnec.CodTipoProceso = tne.CodTipoProceso AND
																	 tnec.CodConcepto = '".$_PARAMETRO['PROVISION']."')
					LEFT JOIN pr_tiponominaempleadoconcepto tnecs ON (tnecs.CodTipoNom = tne.CodTipoNom AND
																	  tnecs.Periodo = tne.Periodo AND
																	  tnecs.CodPersona = tne.CodPersona AND
																	  tnecs.CodOrganismo = tne.CodOrganismo AND
																	  tnecs.CodTipoProceso = tne.CodTipoProceso AND
																	  tnecs.CodConcepto = '0001')
					LEFT JOIN masttasainteres ti ON (ti.Periodo = tne.Periodo)
					LEFT JOIN bancopersona bp ON (bp.CodPersona = p.CodPersona AND bp.Aportes = 'FI')
				WHERE
					((tne.CodTipoProceso = 'FIN') OR
					 (tne.CodTipoProceso = 'ADE' AND e.Estado = 'I' AND tnec.Cantidad > 0))
					$filtro
				ORDER BY $fOrderBy";
		$_field = getRecords($sql);
		foreach ($_field as $field) {
			$id = $field['CodPersona'];
			##	
            $DiasPeriodo = getDiasMes($field['Periodo']);
            if ($field['Estado'] == 'I' && $f['Periodo'] == $field['PeriodoFin']) 
                $DiasTrabajados = intval($field['DiaFin']); 
            elseif ($f['Periodo'] == $field['PeriodoIngreso'])
                $DiasTrabajados = 30 - $field['DiaIngreso'] + 1;
            else 
                $DiasTrabajados = $DiasPeriodo;
            if ($DiasTrabajados == $DiasPeriodo) $DiasParaDiario = 30; else $DiasParaDiario = $DiasTrabajados;
            ##	
			$_SUELDO_MENSUAL = floatval($field['TotalIngresos']);
			##	bonos
			if ($fPeriodo <= '2011-12') $filtro_bonos = " OR c.CodConcepto = '0064'"; else $filtro_bonos = "";
			$sql = "SELECT SUM(tnec.Monto) AS Monto
					FROM
						pr_tiponominaempleadoconcepto tnec
						INNER JOIN pr_concepto c ON (tnec.CodConcepto = c.CodConcepto AND
													 ((c.Tipo = 'I' AND c.FlagBonoRemuneracion = 'S') $filtro_bonos))
					WHERE
						tnec.CodPersona = '".$field['CodPersona']."' AND
						tnec.Periodo = '".$fPeriodo."'
					GROUP BY Periodo";
			$_BONOS = floatval(getVar3($sql));
			##	-----
			$_ALIVAC = floatval($field['AliVac']);
			$_ALIFIN = floatval($field['AliFin']);
			##	remuneracion diaria
			$SueldoDiario = round(($field['TotalIngresos'] / $field['DiasTrabajados']), 2);
			$BonosDiario = round(($_BONOS / 30), 2);
            if ($DiasParaDiario == 30) $_REMUN_DIARIA = round((($field['TotalIngresos'] + $_BONOS) / 30), 2); 
            else $_REMUN_DIARIA = $SueldoDiario + $BonosDiario;
			##	-----
			$_SUELDO_ALIC = $_ALIVAC + $_ALIFIN + $_REMUN_DIARIA;
			$_DIAS = floatval($field['Cantidad']);
			$_PREST_ANTIG_MENSUAL = floatval($field['Monto']);
			##	complemento
			if ($field['TotalIngresos'] > 0) $DiasAdicional = getDiasAdicionalesTrimestral($field['Fingreso'], $field['FechaDesde'], $field['FechaHasta'], $field['Fegreso']); else $DiasAdicional = 0;
			if ($DiasAdicional) {
				$_PREST_COMPL = round((calculo_antiguedad_complemento_trimestral($field['CodPersona'], $field['Fingreso'], $field['FechaDesde'], $field['FechaHasta'])),2);
				$style = "font-weight:bold;";
			} else {
				$_PREST_COMPL = 0;
				$style = "";
			}
			##	calculo
			$sql = "SELECT *
					FROM pr_fideicomisocalculo
					WHERE
						CodPersona = '".$field['CodPersona']."' AND
						Periodo < '".$fPeriodo."'
					ORDER BY Periodo DESC
					LIMIT 0, 1";
			$field_calculo = getRecord($sql);
			if (!count($field_calculo)) {
				$sql = "SELECT
							AcumuladoInicialProv AS PrestAcumulada,
							AcumuladoInicialProv AS InteresAcumulado
						FROM pr_acumuladofideicomiso
						WHERE CodPersona = '".$field['CodPersona']."'";
				$field_calculo = getRecord($sql);
			}
			##	-----
			$_PREST_ACUMULADA = floatval($field_calculo['PrestAcumulada']) + $_PREST_ANTIG_MENSUAL + $_PREST_COMPL;
			$_TASA = floatval($field['Tasa']);
			##	Dias
			if ($field['PeriodoFin'] == $fPeriodo) $_DIAS_MES = intval($field['DiaFin']);
			elseif ($fad['Periodo'] == $field['PeriodoIngreso']) $_DIAS_MES = $field['DiasTrabajados'];
			else $_DIAS_MES = getDiasMes($fPeriodo);
			//	interes
			if (getDiasMes($field['Anio'].'-02') == '28') $DiasAnio = 365; else $DiasAnio = 366;
			$_INTERES_MENSUAL = round((floatval($field_calculo['PrestAcumulada']) * $_TASA / 100 * $_DIAS_MES / $DiasAnio),2);
			$_INTERES_ACUMULADO = floatval($field_calculo['InteresAcumulado']) + $_INTERES_MENSUAL;
			##	-----
			##	verifico si es fraccion
			if ($field['Estado'] == 'I' && $fPeriodo == $field['PeriodoEgreso'] && (($fPeriodo >= '2012-05' && ($_DIAS < $_PARAMETRO['DIASANTIG'] * 3)) || ($fPeriodo < '2012-05' && $_DIAS < $_PARAMETRO['DIASANTIG']))) {
				$FlagFraccionado = "S";
				$color = "#900";
			} else {
				$FlagFraccionado = "N";
				$color = "#000";
			}
			if ($field['FlagTransferido'] > 0) $FlagTransferido = 'S'; else $FlagTransferido = 'N';
			?>
			<tr class="trListaBody" onclick="clkMultiDeposito($(this), '<?=$id?>');" style="color:<?=$color?>;">
				<th>
                	<input type="checkbox" name="personas[]" id="<?=$id?>" value="<?=$id?>" style="display:none;" class="chk" />
                    <input type="hidden" name="_CodPersona[]" value="<?=$field['CodPersona']?>" class="<?=$id?> in" disabled />
                    <input type="hidden" name="_SUELDO_MENSUAL[]" value="<?=$_SUELDO_MENSUAL?>" class="<?=$id?> in" disabled />
                    <input type="hidden" name="_BONOS[]" value="<?=$_BONOS?>" class="<?=$id?> in" disabled />
                    <input type="hidden" name="_ALIVAC[]" value="<?=$_ALIVAC?>" class="<?=$id?> in" disabled />
                    <input type="hidden" name="_ALIFIN[]" value="<?=$_ALIFIN?>" class="<?=$id?> in" disabled />
                    <input type="hidden" name="_REMUN_DIARIA[]" value="<?=$_REMUN_DIARIA?>" class="<?=$id?> in" disabled />
                    <input type="hidden" name="_SUELDO_ALIC[]" value="<?=$_SUELDO_ALIC?>" class="<?=$id?> in" disabled />
                    <input type="hidden" name="_DIAS[]" value="<?=$_DIAS?>" class="<?=$id?> in" disabled />
                    <input type="hidden" name="_PREST_ANTIG_MENSUAL[]" value="<?=$_PREST_ANTIG_MENSUAL?>" class="<?=$id?> in" disabled />
                    <input type="hidden" name="_PREST_COMPL[]" value="<?=$_PREST_COMPL?>" class="<?=$id?> in" disabled />
                    <input type="hidden" name="_PREST_ACUMULADA[]" value="<?=$_PREST_ACUMULADA?>" class="<?=$id?> in" disabled />
                    <input type="hidden" name="_TASA[]" value="<?=$_TASA?>" class="<?=$id?> in" disabled />
                    <input type="hidden" name="_DIAS_MES[]" value="<?=$_DIAS_MES?>" class="<?=$id?> in" disabled />
                    <input type="hidden" name="_INTERES_MENSUAL[]" value="<?=$_INTERES_MENSUAL?>" class="<?=$id?> in" disabled />
                    <input type="hidden" name="_INTERES_ACUMULADO[]" value="<?=$_INTERES_ACUMULADO?>" class="<?=$id?> in" disabled />
                    <input type="hidden" name="_CodTipoProceso[]" value="<?=$field['CodTipoProceso']?>" class="<?=$id?> in" disabled />
                    <input type="hidden" name="_DiasAdicional[]" value="<?=$DiasAdicional?>" class="<?=$id?> in" disabled />
                    <input type="hidden" name="_FlagFraccionado[]" value="<?=$FlagFraccionado?>" class="<?=$id?> in" disabled />
					<?=++$i?>
                </th>
				<td align="center"><?=printFlag($FlagTransferido)?></td>
				<td align="center"><?=$field['CodEmpleado']?></td>
				<td><?=htmlentities($field['NomCompleto'])?></td>
				<td align="right"><?=number_format($field['Ndocumento'], 0, '', '.')?></td>
				<td align="center"><?=$field['Ncuenta']?></td>
				<td align="right"><?=number_format($_PREST_ANTIG_MENSUAL, 2, ',', '.')?></td>
				<td align="right"><?=number_format($_DIAS, 2, ',', '.')?></td>
				<td align="right" style=" <?=$style?>"><?=number_format($_PREST_COMPL, 2, ',', '.')?></td>
				<td align="right" style=" <?=$style?>"><?=number_format($DiasAdicional, 2, ',', '.')?></td>
			</tr>
			<?php
		}
	}
    ?>
    </tbody>
</table>
</div>
</center>
</form>

<div class="gallery clearfix">
    <a href="pagina.php?iframe=true" rel="prettyPhoto[iframe1]" style="display:none;" id="a_reporte"></a>
</div>

<script type="text/javascript" language="javascript">
	function actualizar(form) {
		bloqueo(true);
		//	valido
		var error = "";
		//	valido errores
		if (error != "") {
			cajaModal(error, "error", 400);
		} else {
			//	ajax
			$.ajax({
				type: "POST",
				url: "pr_fideicomiso_actualizar_ajax.php",
				data: "modulo=formulario&accion=actualizar&"+$('#frmentrada').serialize(),
				async: false,
				success: function(resp) {
					if (resp.trim() != '') cajaModal(resp.trim(), 'error', 400);
					else form.submit();
				}
			});
		}
		return false;
	}
	function clkMultiDeposito(src, id) {
		if ($('#'+id).prop('checked')) {
			src.removeClass('trListaBodySel');
			src.addClass('trListaBody');
			$('#'+id).prop('checked', false);
			$('.'+id).prop('disabled', true);
		} else {
			src.removeClass('trListaBody');
			src.addClass('trListaBodySel');
			$('#'+id).prop('checked', true);
			$('.'+id).prop('disabled', false);
		}
	}
	function seleccionarTodos(lista, chk) {
		if (chk) var check = chk; else var check = lista;
		$("#lista_"+lista+" tr").removeClass("trListaBody").addClass("trListaBodySel");
		$("#lista_"+lista+" tr [name="+check+"]").prop("checked", true);

		$('#lista_personas tr').removeClass('trListaBodySel');
		$('#lista_personas tr').addClass('trListaBody');
		$('#lista_personas tr hidden').prop('checked', false);
		$('#lista_personas tr hidden').prop('disabled', true);
	}
	function seleccionarNinguno(lista, chk) {
		if (chk) var check = chk; else var check = lista;
		$("#lista_"+lista+" tr").removeClass("trListaBodySel").addClass("trListaBody");
		$("#lista_"+lista+" tr [name="+check+"]").prop("checked", false);

		$('#lista_personas tr').removeClass('trListaBody');
		$('#lista_personas tr').addClass('trListaBodySel');
		$('#lista_personas tr hidden').prop('checked', true);
		$('#lista_personas tr hidden').prop('disabled', false);
	}
	function seleccionarTodos(lista, chk) {
		if (chk) var check = chk; else var check = lista;
		$("#lista_"+lista+" tr").removeClass("trListaBody").addClass("trListaBodySel");
		$("#lista_"+lista+" tr [name="+check+"]").prop("checked", true);

		$('.chk').prop('checked', true);
		$('.in').prop('disabled', false);
	}
	function seleccionarNinguno(lista, chk) {
		if (chk) var check = chk; else var check = lista;
		$("#lista_"+lista+" tr").removeClass("trListaBodySel").addClass("trListaBody");
		$("#lista_"+lista+" tr [name="+check+"]").prop("checked", false);
		
		$('.chk').prop('checked', false);
		$('.in').prop('disabled', true);
	}
</script>