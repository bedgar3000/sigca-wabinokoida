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
$_titulo = "Actualizar Intereses";
$_width = 950;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="../framemain.php">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=pr_fideicomiso_intereses_lista" method="post" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="sel_registros" id="sel_registros" />

<div class="divBorder" style="width:100%; min-width:<?=$_width?>px;">
<table class="tblFiltro" style="width:100%; min-width:<?=$_width?>px;">
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
<table class="tblBotones" style="width:100%; min-width:<?=$_width?>px;">
    <tr>
        <td style="padding:5px;">
        	<a class="link" href="#" onclick="seleccionarTodos('personas');">Todos</a> |
            <a class="link" href="#" onclick="seleccionarNinguno('personas');">Ninguno</a>
        </td>
        <td align="right">
            <input type="button" id="btActualizar" value="Actualizar Intereses" style="width:120px;" onClick="actualizar(this.form)" />
        </td>
    </tr>
</table>

<div style="overflow:scroll; width:100%; min-width:<?=$_width?>px; height:250px;">
<table class="tblLista" style="width:100%; min-width:100%;">
    <thead>
    <tr>
        <th width="15"></th>
		<th width="20">Tr.</th>
		<th width="50">C&oacute;digo</th>
		<th align="left">Nombre</th>
		<th width="70">Nro. Documento</th>
		<th width="100">Antiguedad Acumulada</th>
		<th width="60">Tasa (%)</th>
		<th width="35">Dias del Mes</th>
		<th width="100">Interes Mensual</th>
		<th width="100">Interes Acumulado</th>
    </tr>
    </thead>
    
    <tbody id="lista_personas">
    <?php
	if ($fCodOrganismo != "" && $fCodTipoNom != "" && $fPeriodo != "") {
		$sql = "SELECT Porcentaje FROM masttasainteres WHERE Periodo = '".$fPeriodo."'";
		$_TASA = getVAr3($sql);
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
					pp.FlagUltimaSemana,
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
						afd.CodPersona = tne.CodPersona AND
						afd.TransaccionFide > 0.00) AS FlagTransferido
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
					 (tne.CodTipoProceso = 'ADE' AND e.Estado = 'I' AND tnec.Cantidad > 0)) OR
					((tne.CodTipoProceso = 'QU2') OR
					 (tne.CodTipoProceso = 'QU1' AND e.Estado = 'I' AND tnec.Cantidad > 0)) OR
					((pp.FlagUltimaSemana = 'S') OR
					 ((tne.CodTipoProceso = 'SE1' OR tne.CodTipoProceso = 'SE2' OR tne.CodTipoProceso = 'SE3' OR tne.CodTipoProceso = 'SE4') AND e.Estado = 'I' AND tnec.Cantidad > 0))
					$filtro
				ORDER BY $fOrderBy";
		$_field = getRecords($sql);
		foreach ($_field as $field) {
			$id = $field['CodPersona'];
			##	calculo anterior
			$sql = "SELECT *
					FROM pr_fideicomisocalculo
					WHERE
						CodPersona = '".$field['CodPersona']."' AND
						Periodo < '".$field['Periodo']."'
					ORDER BY Periodo DESC
					LIMIT 0, 1";
			$field_acumulado = getRecord($sql);
			##	dias
			$sql = "SELECT *
					FROM pr_tiponominaempleadoconcepto
					WHERE ";
			##	-----
			##	Dias
			if ($field['PeriodoFin'] == $fPeriodo) $_DIAS_MES = intval($field['DiaFin']);
			elseif ($field['Periodo'] == $field['PeriodoIngreso']) $_DIAS_MES = $field['DiasTrabajados'];
			else $_DIAS_MES = getDiasMes($fPeriodo);
			//	interes
			if (getDiasMes($field['Anio'].'-02') == '28') $DiasAnio = 365; else $DiasAnio = 366;
			$_INTERES_MENSUAL = round((floatval($field_acumulado['PrestAcumulada']) * $_TASA / 100 * $_DIAS_MES / $DiasAnio),2);
			$_INTERES_ACUMULADO = floatval($field_acumulado['InteresAcumulado']) + $_INTERES_MENSUAL;
			if ($field['FlagTransferido'] > 0) $FlagTransferido = 'S'; else $FlagTransferido = 'N';
			?>
			<tr class="trListaBody" onclick="clkMultiDeposito($(this), '<?=$id?>');" style="color:<?=$color?>;">
				<th>
                	<input type="checkbox" name="personas[]" id="<?=$id?>" value="<?=$id?>" style="display:none;" class="chk" />
                    <input type="hidden" name="_CodPersona[]" value="<?=$field['CodPersona']?>" class="<?=$id?> in" disabled />
                    <input type="hidden" name="_TASA[]" value="<?=$_TASA?>" class="<?=$id?> in" disabled />
                    <input type="hidden" name="_DIAS_MES[]" value="<?=$_DIAS_MES?>" class="<?=$id?> in" disabled />
                    <input type="hidden" name="_INTERES_MENSUAL[]" value="<?=$_INTERES_MENSUAL?>" class="<?=$id?> in" disabled />
                    <input type="hidden" name="_INTERES_ACUMULADO[]" value="<?=$_INTERES_ACUMULADO?>" class="<?=$id?> in" disabled />
                    <input type="hidden" name="_FlagFraccionado[]" value="<?=$FlagFraccionado?>" class="<?=$id?> in" disabled />
					<?=++$i?>
                </th>
				<td align="center"><?=printFlag($FlagTransferido)?></td>
				<td align="center"><?=$field['CodEmpleado']?></td>
				<td><?=htmlentities($field['NomCompleto'])?></td>
				<td align="right"><?=number_format($field['Ndocumento'], 0, '', '.')?></td>
				<td align="right"><?=number_format($field_acumulado['PrestAcumulada'], 2, ',', '.')?></td>
				<td align="right"><?=number_format($_TASA, 2, ',', '.')?></td>
				<td align="right"><?=number_format($_DIAS_MES, 2, ',', '.')?></td>
				<td align="right" style=" <?=$style?>"><?=number_format($_INTERES_MENSUAL, 2, ',', '.')?></td>
				<td align="right" style=" <?=$style?>"><?=number_format($_INTERES_ACUMULADO, 2, ',', '.')?></td>
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
				url: "pr_fideicomiso_intereses_ajax.php",
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