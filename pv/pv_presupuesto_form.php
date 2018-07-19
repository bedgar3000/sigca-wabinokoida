<?php
list($CodOrganismo, $CodPresupuesto) = explode('_', $sel_registros);
##	consulto datos generales
$sql = "SELECT
			p.*,
			cp.IdActividad,
			a.IdProyecto,
			py.IdSubPrograma,
			sp.IdPrograma,
			pg.IdSubSector,
			ss.CodSector,
			cp.CodUnidadEjec,
			pp.FechaPreparado,
			pp.FechaRevisado,
			pp.FechaAprobado,
			pp.FechaGenerado,
			pp.NroGaceta,
			pp.NroResolucion,
			pp.FechaGaceta,
			pp.FechaResolucion,
			p1.NomCompleto AS NomPreparadoPor,
			p2.NomCompleto AS NomRevisadoPor,
			p3.NomCompleto AS NomAprobadoPor,
			p4.NomCompleto AS NomGeneradoPor
		FROM 
			pv_presupuesto p
			INNER JOIN pv_presupuestodet pd ON (p.CodOrganismo = pd.CodOrganismo AND p.CodPresupuesto = pd.CodPresupuesto)
			INNER JOIN pv_categoriaprog cp ON (cp.CategoriaProg = p.CategoriaProg)
			INNER JOIN pv_actividades a ON (a.IdActividad = cp.IdActividad)
			INNER JOIN pv_proyectos py ON (py.IdProyecto = a.IdProyecto)
			INNER JOIN pv_subprogramas sp ON (sp.IdSubPrograma = py.IdSubPrograma)
			INNER JOIN pv_programas pg ON (pg.IdPrograma = sp.IdPrograma)
			INNER JOIN pv_subsector ss ON (ss.IdSubSector = pg.IdSubSector)
			LEFT JOIN pv_proyectopresupuesto pp ON (pp.CodProyPresupuesto = p.CodProyPresupuesto AND pp.CodOrganismo = p.CodOrganismo)
			LEFT JOIN mastpersonas p1 ON (p1.CodPersona = pp.PreparadoPor)
			LEFT JOIN mastpersonas p2 ON (p2.CodPersona = pp.RevisadoPor)
			LEFT JOIN mastpersonas p3 ON (p3.CodPersona = pp.AprobadoPor)
			LEFT JOIN mastpersonas p4 ON (p4.CodPersona = pp.GeneradoPor)
		WHERE
			p.CodOrganismo = '".$CodOrganismo."' AND
			p.CodPresupuesto = '".$CodPresupuesto."'
		GROUP BY p.CodOrganismo, p.CodPresupuesto";
$field = getRecord($sql);
##	dependencias de la unidad ejecutora
$sql = "SELECT
			ued.CodDependencia,
			d.Dependencia
		FROM
			pv_unidadejecutoradep ued 
			INNER JOIN mastdependencias d ON (d.CodDependencia = ued.CodDependencia)
		WHERE ued.CodUnidadEjec = '".$field['CodUnidadEjec']."'";
$field_dependencias = getRecords($sql);
##	ver
$_titulo = "Ver Presupuesto";
$focus = "btCancelar";
$clkCancelar = "document.getElementById('frmentrada').submit();";
$_width = 900;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<table align="center" cellpadding="0" cellspacing="0" style="width:<?=$_width?>px;">
    <tr>
        <td>
            <div class="header">
	            <ul id="tab">
		            <!-- CSS Tabs -->
		            <li id="li1" onclick="currentTab('tab', this);" class="current"><a href="#" onclick="mostrarTab('tab', 1, 4);">Informaci&oacute;n General</a></li>
		            <li id="li2" onclick="currentTab('tab', this);"><a href="#" onclick="mostrarTab('tab', 2, 4);">Distribuci&oacute;n Presupuestaria</a></li>
		            <li id="li3" onclick="currentTab('tab', this);"><a href="#" onclick="mostrarTab('tab', 3, 4);">Ajustes Positivos</a></li>
		            <li id="li4" onclick="currentTab('tab', this);"><a href="#" onclick="mostrarTab('tab', 4, 4);">Ajustes Negativos</a></li>
	            </ul>
            </div>
        </td>
    </tr>
</table>

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=<?=$action?>" method="POST" enctype="multipart/form-data" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
	<input type="hidden" name="fEjercicio" id="fEjercicio" value="<?=$fEjercicio?>" />
	<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
	<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
	<input type="hidden" name="fCodDependencia" id="fCodDependencia" value="<?=$fCodDependencia?>" />
	<input type="hidden" name="CodProyPresupuesto" id="CodProyPresupuesto" value="<?=$field['CodProyPresupuesto']?>" />

	<div id="tab1" style="display:block;">
		<table width="<?=$_width?>" class="tblForm">
			<tr>
				<td colspan="4" class="divFormCaption">Datos Generales</td>
			</tr>
			<tr>
				<td class="tagForm" width="125">C&oacute;digo:</td>
				<td>
					<input type="text" name="CodPresupuesto" id="CodPresupuesto" value="<?=$field['CodPresupuesto']?>" style="width:65px; font-weight:bold;" readonly="readonly" />
				</td>
				<td class="tagForm" width="125">Estado:</td>
				<td>
					<input type="hidden" name="Estado" id="Estado" value="<?=$field['Estado']?>" />
					<input type="text" value="<?=strtoupper(printValores('presupuesto-estado',$field['Estado']))?>" style="width:100px; font-weight:bold;" disabled />
				</td>
			</tr>
		    <tr>
				<td class="tagForm">* Organismo:</td>
				<td>
					<select name="CodOrganismo" id="CodOrganismo" style="width:275px;" onChange="loadSelect($('#CodUnidadEjec'), 'tabla=pv_unidadejecutora&CodOrganismo='+$('#CodOrganismo').val(), 1);" disabled>
						<?=getOrganismos($field['CodOrganismo'], 0);?>
					</select>
				</td>
				<td class="tagForm">Cat. Program&aacute;tica:</td>
				<td>
					<input type="text" name="CategoriaProg" id="CategoriaProg" value="<?=$field['CategoriaProg']?>" style="width:100px;" readonly="readonly" />
				</td>
			</tr>
		    <tr>
				<td class="tagForm">* Sub-Sector:</td>
				<td>
					<select name="IdSubSector" id="IdSubSector" style="width:275px;" disabled onChange="loadSelect($('#IdPrograma'), 'tabla=pv_programas&IdSubSector='+$('#IdSubSector').val(), 1, ['IdSubPrograma','IdProyecto','IdActividad']); setCategoriaProg();">
						<option value="">&nbsp;</option>
						<?=loadSelect2('pv_subsector','IdSubSector','Denominacion',$field['IdSubSector'],0,NULL,NULL,'CodClaSectorial');?>
					</select>
				</td>
				<td class="tagForm">* Ejercicio:</td>
				<td>
					<input type="text" name="Ejercicio" id="Ejercicio" value="<?=$field['Ejercicio']?>" style="width:60px;" maxlength="4" disabled onchange="$('#FechaInicio').val('01-01-'+this.value); $('#FechaFin').val('31-12-'+this.value);" />
				</td>
			</tr>
		    <tr>
				<td class="tagForm">* Programa:</td>
				<td>
					<select name="IdPrograma" id="IdPrograma" style="width:275px;" disabled onChange="loadSelect($('#IdSubPrograma'), 'tabla=pv_subprogramas&IdPrograma='+$('#IdPrograma').val(), 1, ['IdProyecto','IdActividad']); setCategoriaProg();">
						<?=loadSelect2('pv_programas','IdPrograma','Denominacion',$field['IdPrograma'],0,['IdSubSector'],[$field['IdSubSector']],'CodPrograma');?>
					</select>
				</td>
				<td class="tagForm">* Fecha Inicio:</td>
				<td>
					<input type="text" name="FechaInicio" id="FechaInicio" value="<?=formatFechaDMA($field['FechaInicio'])?>" style="width:60px;" maxlength="10" readonly />
				</td>
			</tr>
		    <tr>
				<td class="tagForm">* Sub-Programa:</td>
				<td>
					<select name="IdSubPrograma" id="IdSubPrograma" style="width:275px;" disabled onChange="loadSelect($('#IdProyecto'), 'tabla=pv_proyectos&IdSubPrograma='+$('#IdSubPrograma').val(), 1, ['IdActividad']); setCategoriaProg();">
						<?=loadSelect2('pv_subprogramas','IdSubPrograma','Denominacion',$field['IdSubPrograma'],0,['IdPrograma'],[$field['IdPrograma']],'CodSubPrograma');?>
					</select>
				</td>
				<td class="tagForm">* Fecha Fin:</td>
				<td>
					<input type="text" name="FechaFin" id="FechaFin" value="<?=formatFechaDMA($field['FechaFin'])?>" style="width:60px;" maxlength="10" readonly />
				</td>
			</tr>
		    <tr>
				<td class="tagForm">* Proyecto:</td>
				<td>
					<select name="IdProyecto" id="IdProyecto" style="width:275px;" disabled onChange="loadSelect($('#IdActividad'), 'tabla=pv_actividades&IdProyecto='+$('#IdProyecto').val(), 1); setCategoriaProg();">
						<?=loadSelect2('pv_proyectos','IdProyecto','Denominacion',$field['IdProyecto'],0,['IdSubPrograma'],[$field['IdSubPrograma']],'CodProyecto');?>
					</select>
				</td>
				<td class="tagForm">Monto Aprobado:</td>
				<td>
					<input type="text" name="MontoAprobado" id="MontoAprobado" value="<?=number_format($field['MontoAprobado'],2,',','.')?>" style="width:100px; font-weight:bold; text-align:right;" class="currency" disabled onchange="setMontoAprobado();" />
				</td>
			</tr>
		    <tr>
				<td class="tagForm">* Actividad:</td>
				<td>
					<select name="IdActividad" id="IdActividad" style="width:275px;" disabled onchange="setCategoriaProg();">
						<?=loadSelect2('pv_actividades','IdActividad','Denominacion',$field['IdActividad'],0,['IdProyecto'],[$field['IdProyecto']],'CodActividad');?>
					</select>
				</td>
				<td class="tagForm">Monto Ajustado:</td>
				<td>
					<input type="text" name="MontoAjustado" id="MontoAjustado" value="<?=number_format($field['MontoAjustado'],2,',','.')?>" style="width:100px; font-weight:bold; text-align:right;" class="currency" disabled onchange="setMontoAprobado();" />
				</td>
			</tr>
		    <tr>
				<td class="tagForm">* Unidad Ejecutora:</td>
				<td>
					<select name="CodUnidadEjec" id="CodUnidadEjec" style="width:275px;" disabled onchange="getDependenciasxUnidadEjecutora(this.value); setCategoriaProg();">
						<option value="">&nbsp;</option>
						<?=loadSelect2('pv_unidadejecutora','CodUnidadEjec','Denominacion',$field['CodUnidadEjec'],10,['CodOrganismo'],[$field['CodOrganismo']]);?>
					</select>
				</td>
				<td class="tagForm">Monto Compromiso:</td>
				<td>
					<input type="text" name="MontoCompromiso" id="MontoCompromiso" value="<?=number_format($field['MontoCompromiso'],2,',','.')?>" style="width:100px; font-weight:bold; text-align:right;" class="currency" disabled onchange="setMontoAprobado();" />
				</td>
			</tr>
		    <tr>
				<td class="tagForm">Gaceta:</td>
				<td>
					<input type="text" name="NroGaceta" id="NroGaceta" value="<?=$field['NroGaceta']?>" style="width:220px;" maxlength="20" disabled />
					<input type="text" name="FechaGaceta" id="FechaGaceta" value="<?=formatFechaDMA($field['FechaGaceta'])?>" class="datepicker" style="width:60px;" maxlength="10" disabled />
				</td>
				<td class="tagForm">Monto Causado:</td>
				<td>
					<input type="text" name="MontoCausado" id="MontoCausado" value="<?=number_format($field['MontoCausado'],2,',','.')?>" style="width:100px; font-weight:bold; text-align:right;" class="currency" disabled onchange="setMontoAprobado();" />
				</td>
			</tr>
		    <tr>
				<td class="tagForm">Resoluci&oacute;n:</td>
				<td>
					<input type="text" name="NroResolucion" id="NroResolucion" value="<?=$field['NroResolucion']?>" style="width:220px;" maxlength="20" disabled />
					<input type="text" name="FechaResolucion" id="FechaResolucion" value="<?=formatFechaDMA($field['FechaResolucion'])?>" class="datepicker" style="width:60px;" maxlength="10" disabled />
				</td>
				<td class="tagForm">Monto Pagado:</td>
				<td>
					<input type="text" name="MontoPagado" id="MontoPagado" value="<?=number_format($field['MontoPagado'],2,',','.')?>" style="width:100px; font-weight:bold; text-align:right;" class="currency" disabled onchange="setMontoAprobado();" />
				</td>
			</tr>
			<tr>
				<td colspan="2" class="divFormCaption">Dependencias</td>
				<td colspan="2" class="divFormCaption">Auditoria</td>
			</tr>
			<tr>
				<td colspan="2" rowspan="5">
					<div style="overflow:scroll; height:90px; width:445px;">
						<table class="tblLista" style="width:700px;">
							<tbody id="lista_dep">
								<?php
								foreach ($field_dependencias as $fd) {
									?>
									<tr class="trListaBody">
										<td align="center" width="40"><?=$fd['CodDependencia']?></td>
										<td><?=htmlentities($fd['Dependencia'])?></td>
									</tr>
									<?php
								}
								?>
							</tbody>
						</table>
					</div>
				</td>
				<td class="tagForm">Preparado Por:</td>
				<td>
					<input type="hidden" name="PreparadoPor" id="PreparadoPor" value="<?=$field['PreparadoPor']?>" />
					<input type="text" name="NomPreparadoPor" id="NomPreparadoPor" value="<?=$field['NomPreparadoPor']?>" style="width:220px;" readonly />
					<input type="text" name="FechaPreparado" id="FechaPreparado" value="<?=formatFechaDMA($field['FechaPreparado'])?>" style="width:60px;" maxlength="10" readonly />
				</td>
			</tr>
			<tr>
				<td class="tagForm">Revisado Por:</td>
				<td>
					<input type="hidden" name="RevisadoPor" id="RevisadoPor" value="<?=$field['RevisadoPor']?>" />
					<input type="text" name="NomRevisadoPor" id="NomRevisadoPor" value="<?=$field['NomRevisadoPor']?>" style="width:220px;" readonly />
					<input type="text" name="FechaRevisado" id="FechaRevisado" value="<?=formatFechaDMA($field['FechaRevisado'])?>" style="width:60px;" maxlength="10" readonly />
				</td>
			</tr>
			<tr>
				<td class="tagForm">Aprobado Por:</td>
				<td>
					<input type="hidden" name="AprobadoPor" id="AprobadoPor" value="<?=$field['AprobadoPor']?>" />
					<input type="text" name="NomAprobadoPor" id="NomAprobadoPor" value="<?=$field['NomAprobadoPor']?>" style="width:220px;" readonly />
					<input type="text" name="FechaAprobado" id="FechaAprobado" value="<?=formatFechaDMA($field['FechaAprobado'])?>" style="width:60px;" maxlength="10" readonly />
				</td>
			</tr>
			<tr>
				<td class="tagForm">Generado Por:</td>
				<td>
					<input type="hidden" name="GeneradoPor" id="GeneradoPor" value="<?=$field['GeneradoPor']?>" />
					<input type="text" name="NomGeneradoPor" id="NomGeneradoPor" value="<?=$field['NomGeneradoPor']?>" style="width:220px;" readonly />
					<input type="text" name="FechaGenerado" id="FechaGenerado" value="<?=formatFechaDMA($field['FechaGenerado'])?>" style="width:60px;" maxlength="10" readonly />
				</td>
			</tr>
			<tr>
				<td class="tagForm">&Uacute;ltima Modif.:</td>
				<td>
					<input type="text" value="<?=$field['UltimoUsuario']?>" style="width:150px;" disabled="disabled" />
					<input type="text" value="<?=$field['UltimaFecha']?>" style="width:100px" disabled="disabled" />
				</td>
			</tr>
		</table>
	</div>

	<div id="tab2" style="display:none;">
		<input type="hidden" id="sel_partida" />
		<table width="<?=$_width?>" class="tblBotones">
			<thead>
				<tr>
					<th class="divFormCaption">Distribuci&oacute;n del Presupuesto</th>
				</tr>
			</thead>
		</table>
		<div style="overflow:scroll; height:400px; width:<?=$_width?>px; margin:auto;">
			<table class="tblLista" style="width:1300px;">
				<thead>
					<tr>
						<th width="25">F.F.</th>
						<th width="80">Partida</th>
						<th align="left">Denominaci&oacute;n</th>
						<th width="100">Monto Presupuesto</th>
						<th width="100">Monto Ajustado</th>
						<th width="100">Monto Comprometido</th>
						<th width="100">Monto Causado</th>
						<th width="100">Monto Pagado</th>
					</tr>
				</thead>
				
				<tbody id="lista_partida">
					<?php
					$nro_partida = 0;
					$filtro = " AND (CodOrganismo = '$field[CodOrganismo]' AND CodPresupuesto = '$field[CodPresupuesto]')";
					$sql = "(SELECT
								p.cod_partida,
								p.denominacion,
								p.cod_tipocuenta,
								p.partida1,
								p.generica,
								p.especifica,
								p.subespecifica,
								'T' AS tipo,
								(SELECT SUM(MontoAprobado) FROM pv_presupuestodet WHERE cod_partida LIKE '4%' $filtro) AS MontoAprobado,
								(SELECT SUM(MontoAjustado) FROM pv_presupuestodet WHERE cod_partida LIKE '4%' $filtro) AS MontoAjustado,
								(SELECT SUM(MontoCompromiso) FROM pv_presupuestodet WHERE cod_partida LIKE '4%' $filtro) AS MontoCompromiso,
								(SELECT SUM(MontoCausado) FROM pv_presupuestodet WHERE cod_partida LIKE '4%' $filtro) AS MontoCausado,
								(SELECT SUM(MontoPagado) FROM pv_presupuestodet WHERE cod_partida LIKE '4%' $filtro) AS MontoPagado,
								'N' AS FlagAnexa,
								'' AS CodFuente
							 FROM pv_partida p
							 WHERE
								p.cod_tipocuenta = '4' AND
								p.partida1 = '00' AND
								p.generica = '00' AND
								p.especifica = '00' AND
								p.subespecifica = '00' AND
								SUBSTRING(p.cod_partida, 1, 1) IN (SELECT SUBSTRING(cod_partida, 1, 1) AS partida FROM pv_presupuestodet WHERE 1 $filtro GROUP BY partida)
							 GROUP BY cod_partida)
							UNION
							(SELECT
								p.cod_partida,
								p.denominacion,
								p.cod_tipocuenta,
								p.partida1,
								p.generica,
								p.especifica,
								p.subespecifica,
								'T' AS tipo,
								(SELECT SUM(MontoAprobado) FROM pv_presupuestodet WHERE cod_partida LIKE CONCAT('4', p.partida1, '.%') $filtro) AS MontoAprobado,
								(SELECT SUM(MontoAjustado) FROM pv_presupuestodet WHERE cod_partida LIKE CONCAT('4', p.partida1, '.%') $filtro) AS MontoAjustado,
								(SELECT SUM(MontoCompromiso) FROM pv_presupuestodet WHERE cod_partida LIKE CONCAT('4', p.partida1, '.%') $filtro) AS MontoCompromiso,
								(SELECT SUM(MontoCausado) FROM pv_presupuestodet WHERE cod_partida LIKE CONCAT('4', p.partida1, '.%') $filtro) AS MontoCausado,
								(SELECT SUM(MontoPagado) FROM pv_presupuestodet WHERE cod_partida LIKE CONCAT('4', p.partida1, '.%') $filtro) AS MontoPagado,
								'N' AS FlagAnexa,
								'' AS CodFuente
							 FROM pv_partida p
							 WHERE
								p.cod_tipocuenta = '4' AND
								p.partida1 <> '00' AND
								p.generica = '00' AND
								p.especifica = '00' AND
								p.subespecifica = '00' AND
								SUBSTRING(p.cod_partida, 1, 3) IN (SELECT SUBSTRING(cod_partida, 1, 3) AS partida FROM pv_presupuestodet WHERE 1 $filtro GROUP BY partida)
							 GROUP BY cod_partida)
							UNION
							(SELECT
								p.cod_partida,
								p.denominacion,
								p.cod_tipocuenta,
								p.partida1,
								p.generica,
								p.especifica,
								p.subespecifica,
								'T' AS tipo,
								(SELECT SUM(MontoAprobado) FROM pv_presupuestodet WHERE cod_partida LIKE CONCAT('4', p.partida1, '.', p.generica, '.%') $filtro) AS MontoAprobado,
								(SELECT SUM(MontoAjustado) FROM pv_presupuestodet WHERE cod_partida LIKE CONCAT('4', p.partida1, '.', p.generica, '.%') $filtro) AS MontoAjustado,
								(SELECT SUM(MontoCompromiso) FROM pv_presupuestodet WHERE cod_partida LIKE CONCAT('4', p.partida1, '.', p.generica, '.%') $filtro) AS MontoCompromiso,
								(SELECT SUM(MontoCausado) FROM pv_presupuestodet WHERE cod_partida LIKE CONCAT('4', p.partida1, '.', p.generica, '.%') $filtro) AS MontoCausado,
								(SELECT SUM(MontoPagado) FROM pv_presupuestodet WHERE cod_partida LIKE CONCAT('4', p.partida1, '.', p.generica, '.%') $filtro) AS MontoPagado,
								'N' AS FlagAnexa,
								'' AS CodFuente
							 FROM pv_partida p
							 WHERE
								p.cod_tipocuenta = '4' AND
								p.partida1 <> '00' AND
								p.generica <> '00' AND
								p.especifica = '00' AND
								p.subespecifica = '00' AND
								SUBSTRING(p.cod_partida, 1, 7) IN (SELECT SUBSTRING(cod_partida, 1, 7) AS partida FROM pv_presupuestodet WHERE 1 $filtro GROUP BY partida)
							 GROUP BY cod_partida)
							UNION
							(SELECT
								p.cod_partida,
								p.denominacion,
								p.cod_tipocuenta,
								p.partida1,
								p.generica,
								p.especifica,
								p.subespecifica,
								p.tipo,
								ppd.MontoAprobado,
								ppd.MontoAjustado,
								ppd.MontoCompromiso,
								ppd.MontoCausado,
								ppd.MontoPagado,
								ppd.FlagAnexa,
								ppd.CodFuente
							 FROM
							 	pv_partida p
							 	INNER JOIN pv_presupuestodet ppd ON (ppd.cod_partida = p.cod_partida)
							 WHERE 
							 	p.cod_tipocuenta = '4' AND
							 	ppd.CodOrganismo = '$field[CodOrganismo]' AND
							 	ppd.CodPresupuesto = '$field[CodPresupuesto]')
							ORDER BY cod_partida;";
					$field_partida = getRecords($sql);
					foreach ($field_partida as $f) {
						++$nro_partida;
						$id = $f['cod_partida'];
						if ($f['partida1']=='00' && $f['generica']=='00' && $f['especifica']=='00' && $f['subespecifica']=='00') {
							$background="background-color:#B6B6B6;";
							$weight="font-weight:bold;";
						}
						elseif ($f['generica']=='00' && $f['especifica']=='00' && $f['subespecifica']=='00') {
							$background="background-color:#C7C7C7;";
							$weight="font-weight:bold;";
						}
						elseif ($f['especifica']=='00' && $f['subespecifica']=='00') {
							$background="background-color:#DEDEDE;";
							$weight="font-weight:bold;";
						}
						else {
							$background="";
							$weight="";
						}
						?>
						<tr class="trListaBody" style="<?=$background.$weight?>">
							<td align="center"><?=$f['CodFuente']?></td>
							<td align="center"><?=$f['cod_partida']?></td>
							<td><?=htmlentities($f['denominacion'])?></td>
							<td align="right"><?=number_format($f['MontoAprobado'],2,',','.')?></td>
							<td align="right"><?=number_format($f['MontoAjustado'],2,',','.')?></td>
							<td align="right"><?=number_format($f['MontoCompromiso'],2,',','.')?></td>
							<td align="right"><?=number_format($f['MontoCausado'],2,',','.')?></td>
							<td align="right"><?=number_format($f['MontoPagado'],2,',','.')?></td>
						</tr>
					<?php
					}
					?>
				</tbody>
			</table>
		</div>
	</div>

	<div id="tab3" style="display:none;">
		<table width="<?=$_width?>" class="tblBotones">
			<thead>
				<tr>
					<th class="divFormCaption">Ajustes Positivos</th>
				</tr>
			</thead>
		</table>
		<div style="overflow:scroll; height:400px; width:<?=$_width?>px; margin:auto;">
			<table class="tblLista" style="width:100%;">
				<thead>
					<tr>
						<th width="30">F.F.</th>
						<th width="80">Partida</th>
						<th align="left">Denominaci&oacute;n</th>
						<th width="100">Monto Ajustes</th>
					</tr>
				</thead>
				
				<tbody>
					<?php
					$sql = "SELECT
								ajd.CodFuente,
								ajd.cod_partida,
								pv.denominacion,
								SUM(MontoAjuste) AS MontoAjustes
							FROM
								pv_ajustesdet ajd
								INNER JOIN pv_presupuestodet pd ON (ajd.CodOrganismo = pd.CodOrganismo
																	AND ajd.CodPresupuesto = pd.CodPresupuesto
																	AND ajd.CodFuente = pd.CodFuente
																	AND ajd.cod_partida = pd.cod_partida)
								INNER JOIN pv_partida pv ON (pv.cod_partida = pd.cod_partida)
							WHERE
								ajd.CodOrganismo = '$field[CodOrganismo]'
								AND ajd.CodPresupuesto = '$field[CodPresupuesto]'
								AND ajd.Tipo = 'I'
							GROUP BY CodFuente, cod_partida";
					$field_ajustes_partidas = getRecords($sql);
					foreach ($field_ajustes_partidas as $f) {
						?>
						<tr class="trListaBody2">
							<td align="center"><?=$f['CodFuente']?></td>
							<td align="center"><?=$f['cod_partida']?></td>
							<td><?=htmlentities($f['denominacion'])?></td>
							<td align="right"><?=number_format($f['MontoAjustes'],2,',','.')?></td>
						</tr>
						<?php
						$sql = "SELECT
									aj.CodAjuste,
									aj.Descripcion,
									aj.Fecha,
									ajd.MontoAjuste
								FROM
									pv_ajustesdet ajd
									INNER JOIN pv_ajustes aj ON (aj.CodOrganismo = ajd.CodOrganismo AND aj.CodAjuste = ajd.CodAjuste)
								WHERE
									ajd.CodOrganismo = '$field[CodOrganismo]' AND
									ajd.CodPresupuesto = '$field[CodPresupuesto]' AND
									ajd.CodFuente = '$f[CodFuente]' AND
									ajd.cod_partida = '$f[cod_partida]'";
						$field_ajustes = getRecords($sql);
						foreach ($field_ajustes as $fa) {
							?>
							<tr class="trListaBody">
								<td align="center">&nbsp;</td>
								<td align="center"><?=$fa['CodAjuste']?></td>
								<td><?=htmlentities($fa['Descripcion'])?></td>
								<td align="right"><?=number_format($fa['MontoAjuste'],2,',','.')?></td>
							</tr>
							<?php
						}
					}
					?>
				</tbody>
			</table>
		</div>
	</div>

	<div id="tab4" style="display:none;">
		<table width="<?=$_width?>" class="tblBotones">
			<thead>
				<tr>
					<th class="divFormCaption">Ajustes Negativos</th>
				</tr>
			</thead>
		</table>
		<div style="overflow:scroll; height:400px; width:<?=$_width?>px; margin:auto;">
			<table class="tblLista" style="width:100%;">
				<thead>
					<tr>
						<th width="30">F.F.</th>
						<th width="80">Partida</th>
						<th align="left">Denominaci&oacute;n</th>
						<th width="100">Monto Ajustes</th>
					</tr>
				</thead>
				
				<tbody>
					<?php
					$sql = "SELECT
								ajd.CodFuente,
								ajd.cod_partida,
								pv.denominacion,
								SUM(MontoAjuste) AS MontoAjustes
							FROM
								pv_ajustesdet ajd
								INNER JOIN pv_presupuestodet pd ON (ajd.CodOrganismo = pd.CodOrganismo
																	AND ajd.CodPresupuesto = pd.CodPresupuesto
																	AND ajd.CodFuente = pd.CodFuente
																	AND ajd.cod_partida = pd.cod_partida)
								INNER JOIN pv_partida pv ON (pv.cod_partida = pd.cod_partida)
							WHERE
								ajd.CodOrganismo = '$field[CodOrganismo]'
								AND ajd.CodPresupuesto = '$field[CodPresupuesto]'
								AND ajd.Tipo = 'D'
							GROUP BY CodFuente, cod_partida";
					$field_ajustes_partidas = getRecords($sql);
					foreach ($field_ajustes_partidas as $f) {
						?>
						<tr class="trListaBody2">
							<td align="center"><?=$f['CodFuente']?></td>
							<td align="center"><?=$f['cod_partida']?></td>
							<td><?=htmlentities($f['denominacion'])?></td>
							<td align="right"><?=number_format($f['MontoAjustes'],2,',','.')?></td>
						</tr>
						<?php
						$sql = "SELECT
									aj.CodAjuste,
									aj.Descripcion,
									aj.Fecha,
									ajd.MontoAjuste
								FROM
									pv_ajustesdet ajd
									INNER JOIN pv_ajustes aj ON (aj.CodOrganismo = ajd.CodOrganismo AND aj.CodAjuste = ajd.CodAjuste)
								WHERE
									ajd.CodOrganismo = '$field[CodOrganismo]' AND
									ajd.CodPresupuesto = '$field[CodPresupuesto]' AND
									ajd.CodFuente = '$f[CodFuente]' AND
									ajd.cod_partida = '$f[cod_partida]'";
						$field_ajustes = getRecords($sql);
						foreach ($field_ajustes as $fa) {
							?>
							<tr class="trListaBody">
								<td align="center">&nbsp;</td>
								<td align="center"><?=$fa['CodAjuste']?></td>
								<td><?=htmlentities($fa['Descripcion'])?></td>
								<td align="right"><?=number_format($fa['MontoAjuste'],2,',','.')?></td>
							</tr>
							<?php
						}
					}
					?>
				</tbody>
			</table>
		</div>
	</div>

	<center>
		<input type="button" value="Cancelar" style="width:75px;" id="btCancelar" onclick="<?=$clkCancelar?>" />
	</center>
</form>
<div style="width:<?=$_width?>px" class="divMsj">Campos Obligatorios *</div>

<script type="text/javascript" language="javascript">
</script>