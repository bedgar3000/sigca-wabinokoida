<?php
if ($opcion == "nuevo") {
	$field['CodOrganismo'] = ($fCodOrganismo?$fCodOrganismo:$_SESSION["FILTRO_ORGANISMO_ACTUAL"]);
	$field['Fecha'] = $FechaActual;
	$field['Periodo'] = $PeriodoActual;
	$field['PreparadoPor'] = $_SESSION['CODPERSONA_ACTUAL'];
	$field['NomPreparadoPor'] = $_SESSION['NOMBRE_USUARIO_ACTUAL'];
	$field['FechaPreparado'] = $FechaActual;
	$field['Estado'] = 'PR';
	##
	$_titulo = "Nueva Reformulaci&oacute;n";
	$accion = "nuevo";
	$disabled_modificar = "";
	$disabled_ver = "";
	$display_modificar = "";
	$display_submit = "";
	$label_submit = "Guardar";
	$focus = "Descripcion";
}
elseif ($opcion == "modificar" || $opcion == "ver" || $opcion == "aprobar" || $opcion == "anular") {
	list($CodOrganismo, $CodReformulacion) = explode('_', $sel_registros);
	##	consulto datos generales
	$sql = "SELECT
				r.*,
				p.Ejercicio,
				p.CodPresupuesto,
				p.CategoriaProg,
				cp.CodUnidadEjec,
				ue.Denominacion AS UnidadEjecutora,
				o.Organismo,
				p1.NomCompleto AS NomPreparadoPor,
				p2.NomCompleto AS NomAprobadoPor
			FROM 
				pv_reformulacion r
				INNER JOIN pv_presupuesto p ON (p.CodOrganismo = r.CodOrganismo AND p.CodPresupuesto = r.CodPresupuesto)
				INNER JOIN pv_categoriaprog cp ON (cp.CategoriaProg = p.CategoriaProg)
				INNER JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cp.CodUnidadEjec)
				INNER JOIN mastorganismos o On (o.CodOrganismo = r.CodOrganismo)
				LEFT JOIN mastpersonas p1 ON (p1.CodPersona = r.PreparadoPor)
				LEFT JOIN mastpersonas p2 ON (p2.CodPersona = r.AprobadoPor)
			WHERE
				r.CodOrganismo = '".$CodOrganismo."' AND
				r.CodReformulacion = '".$CodReformulacion."'";
	$field = getRecord($sql);
	##	modificar
	if ($opcion == "modificar") {
		$_titulo = "Modificar Reformulaci&oacute;n";
		$accion = "modificar";
		$disabled_modificar = "disabled";
		$disabled_ver = "";
		$display_modificar = "display:none;";
		$display_submit = "";
		$label_submit = "Modificar";
		$focus = "NroGaceta";
	}
	##	ver
	elseif ($opcion == "ver") {
		$_titulo = "Ver Reformulaci&oacute;n";
		$accion = "";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$display_modificar = "display:none;";
		$display_submit = "display:none;";
		$label_submit = "";
		$focus = "btCancelar";
	}
	##	aprobar
	elseif ($opcion == "aprobar") {
		$field['AprobadoPor'] = $_SESSION['CODPERSONA_ACTUAL'];
		$field['NomAprobadoPor'] = $_SESSION['NOMBRE_USUARIO_ACTUAL'];
		$field['FechaAprobado'] = $FechaActual;
		$field['Estado'] = 'AP';
		##	
		$_titulo = "Aprobar Reformulaci&oacute;n";
		$accion = "aprobar";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$display_modificar = "display:none;";
		$display_submit = "";
		$label_submit = "Aprobar";
		$focus = "btSubmit";
	}
	##	anular
	elseif ($opcion == "anular") {
		$field['AnuladoPor'] = $_SESSION['CODPERSONA_ACTUAL'];
		$field['NomAnuladoPor'] = $_SESSION['NOMBRE_USUARIO_ACTUAL'];
		$field['FechaAnulado'] = $FechaActual;
		##	
		$_titulo = "Anular Reformulaci&oacute;n";
		$accion = "anular";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$display_modificar = "display:none;";
		$display_submit = "";
		$label_submit = "Anular";
		$focus = "btSubmit";
	}
}
$clkCancelar = "document.getElementById('frmentrada').submit();";
	//	------------------------------------
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
		            <li id="li1" onclick="currentTab('tab', this);" class="current"><a href="#" onclick="mostrarTab('tab', 1, 2);">Informaci&oacute;n General</a></li>
		            <li id="li2" onclick="currentTab('tab', this);"><a href="#" onclick="mostrarTab('tab', 2, 2);">Distribuci&oacute;n Presupuestaria</a></li>
	            </ul>
            </div>
        </td>
    </tr>
</table>

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=<?=$action?>" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('pv_reformulacion_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
	<input type="hidden" name="fEjercicio" id="fEjercicio" value="<?=$fEjercicio?>" />
	<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
	<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
	<input type="hidden" name="fCodUnidadEjec" id="fCodUnidadEjec" value="<?=$fCodUnidadEjec?>" />

	<div id="tab1" style="display:block;">
		<table width="<?=$_width?>" class="tblForm">
			<tr>
				<td colspan="4" class="divFormCaption">Datos Generales</td>
			</tr>
			<tr>
				<td class="tagForm" width="125">C&oacute;digo:</td>
				<td>
					<input type="text" name="CodReformulacion" id="CodReformulacion" value="<?=$field['CodReformulacion']?>" style="width:65px; font-weight:bold;" readonly="readonly" />
				</td>
				<td class="tagForm" width="125">Estado:</td>
				<td>
					<input type="hidden" name="Estado" id="Estado" value="<?=$field['Estado']?>" />
					<input type="text" value="<?=strtoupper(printValores('proyecto-estado',$field['Estado']))?>" style="width:100px; font-weight:bold;" disabled />
				</td>
			</tr>
		    <tr>
				<td class="tagForm">* Presupuesto:</td>
				<td class="gallery clearfix">
					<input type="text" name="CodPresupuesto" id="CodPresupuesto" value="<?=$field['CodPresupuesto']?>" style="width:65px;" readonly />
					<input type="text" name="Ejercicio" id="Ejercicio" value="<?=$field['Ejercicio']?>" style="width:35px;" disabled />
		            <a href="../lib/listas/gehen.php?anz=lista_pv_presupuesto&filtrar=default&ventana=pv_reformulacion&campo1=CodPresupuesto&campo2=Ejercicio&campo3=CodOrganismo&campo4=Organismo&campo5=CodUnidadEjec&campo6=UnidadEjecutora&campo7=CategoriaProg&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" id="btPresupuesto" style=" <?=$display_modificar?>">
		            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
		            </a>
				</td>
				<td class="tagForm">Cat. Program&aacute;tica:</td>
				<td>
					<input type="text" name="CategoriaProg" id="CategoriaProg" value="<?=$field['CategoriaProg']?>" style="width:100px;" disabled />
				</td>
			</tr>
		    <tr>
				<td class="tagForm">Organismo:</td>
				<td>
					<input type="hidden" name="CodOrganismo" id="CodOrganismo" value="<?=$field['CodOrganismo']?>" />
					<input type="text" name="Organismo" id="Organismo" value="<?=$field['Organismo']?>" style="width:270px;" disabled />
				</td>
				<td class="tagForm">Unidad Ejecutora:</td>
				<td>
					<input type="hidden" name="CodUnidadEjec" id="CodUnidadEjec" value="<?=$field['CodUnidadEjec']?>" />
					<input type="text" name="UnidadEjecutora" id="UnidadEjecutora" value="<?=$field['UnidadEjecutora']?>" style="width:270px;" disabled />
				</td>
			</tr>
		    <tr>
				<td class="tagForm">* Fecha:</td>
				<td>
					<input type="text" name="Fecha" id="Fecha" value="<?=formatFechaDMA($field['Fecha'])?>" class="datepicker" style="width:60px;" maxlength="10" <?=$disabled_ver?> onchange="$('#Periodo').val(this.value.substr(6,4)+'-'+this.value.substr(3,2));" />
				</td>
				<td class="tagForm">* Periodo:</td>
				<td>
					<input type="text" name="Periodo" id="Periodo" value="<?=$field['Periodo']?>" style="width:60px;" maxlength="7" readonly />
				</td>
			</tr>
		    <tr>
				<td class="tagForm">Gaceta:</td>
				<td>
					<input type="text" name="NroGaceta" id="NroGaceta" value="<?=$field['NroGaceta']?>" style="width:200px;" maxlength="20" <?=$disabled_ver?> />
					<input type="text" name="FechaGaceta" id="FechaGaceta" value="<?=formatFechaDMA($field['FechaGaceta'])?>" class="datepicker" style="width:60px;" maxlength="10" <?=$disabled_ver?> />
				</td>
				<td class="tagForm">Preparado Por:</td>
				<td>
					<input type="hidden" name="PreparadoPor" id="PreparadoPor" value="<?=$field['PreparadoPor']?>" />
					<input type="text" name="NomPreparadoPor" id="NomPreparadoPor" value="<?=$field['NomPreparadoPor']?>" style="width:200px;" readonly />
					<input type="text" name="FechaPreparado" id="FechaPreparado" value="<?=formatFechaDMA($field['FechaPreparado'])?>" style="width:60px;" maxlength="10" readonly />
				</td>
			</tr>
		    <tr>
				<td class="tagForm">Resoluci&oacute;n:</td>
				<td>
					<input type="text" name="NroResolucion" id="NroResolucion" value="<?=$field['NroResolucion']?>" style="width:200px;" maxlength="20" <?=$disabled_ver?> />
					<input type="text" name="FechaResolucion" id="FechaResolucion" value="<?=formatFechaDMA($field['FechaResolucion'])?>" class="datepicker" style="width:60px;" maxlength="10" <?=$disabled_ver?> />
				</td>
				<td class="tagForm">Aprobado Por:</td>
				<td>
					<input type="hidden" name="AprobadoPor" id="AprobadoPor" value="<?=$field['AprobadoPor']?>" />
					<input type="text" name="NomAprobadoPor" id="NomAprobadoPor" value="<?=$field['NomAprobadoPor']?>" style="width:200px;" readonly />
					<input type="text" name="FechaAprobado" id="FechaAprobado" value="<?=formatFechaDMA($field['FechaAprobado'])?>" style="width:60px;" maxlength="10" readonly />
				</td>
			</tr>
		    <tr>
				<td class="tagForm">* Descripci&oacute;n:</td>
				<td colspan="3">
					<textarea name="Descripcion" id="Descripcion" style="width:95%; height:60px;" <?=$disabled_ver?>><?=htmlentities($field['Descripcion'])?></textarea>
				</td>
			</tr>
			<tr>
				<td class="tagForm">&Uacute;ltima Modif.:</td>
				<td colspan="4">
					<input type="text" value="<?=$field['UltimoUsuario']?>" style="width:160px;" disabled="disabled" />
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
					<th class="divFormCaption">Partidas Presupuestarias</th>
				</tr>
			</thead>
		    <tbody>
		    <tr>
		        <td align="right" class="gallery clearfix">
		            <a id="a_partida" href="../lib/listas/gehen.php?anz=lista_partidas&filtrar=default&ventana=reformulacion_insertar&detalle=partida&modulo=ajax&accion=partida_insertar&FlagTipoCuenta=S&fcod_tipocuenta=4&FlagGenerar=<?=($field['Estado']=='GE'?'S':'N')?>&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe2]" style="display:none;"></a>
		            <input type="button" class="btLista" value="Insertar" onclick="$('#a_partida').click();" <?=$disabled_ver?> />
		            <input type="button" class="btLista" value="Borrar" onclick="quitar_partida(this, 'partida');" <?=$disabled_ver?> />
		        </td>
		    </tr>
		    </tbody>
		</table>
		<div style="overflow:scroll; height:400px; width:<?=$_width?>px; margin:auto;">
			<table class="tblLista" style="width:100%;">
				<thead>
					<tr>
						<th width="35">F.F</th>
						<th width="80">Partida</th>
						<th align="left">Denominaci&oacute;n</th>
					</tr>
				</thead>
				
				<tbody id="lista_partida">
					<?php
					$nro_partida = 0;
					$filtro = " AND (CodOrganismo = '$field[CodOrganismo]' AND CodReformulacion = '$field[CodReformulacion]')";
					$sql = "(SELECT
								p.cod_partida,
								p.denominacion,
								'' AS CodFuente,
								p.cod_tipocuenta,
								p.partida1,
								p.generica,
								p.especifica,
								p.subespecifica,
								'T' AS tipo
							 FROM pv_partida p
							 WHERE
								p.cod_tipocuenta = '4' AND
								p.partida1 = '00' AND
								p.generica = '00' AND
								p.especifica = '00' AND
								p.subespecifica = '00' AND
								SUBSTRING(p.cod_partida, 1, 1) IN (SELECT SUBSTRING(cod_partida, 1, 1) AS partida FROM pv_reformulaciondet WHERE 1 $filtro GROUP BY partida)
							 GROUP BY cod_partida)
							UNION
							(SELECT
								p.cod_partida,
								p.denominacion,
								'' AS CodFuente,
								p.cod_tipocuenta,
								p.partida1,
								p.generica,
								p.especifica,
								p.subespecifica,
								'T' AS tipo
							 FROM pv_partida p
							 WHERE
								p.cod_tipocuenta = '4' AND
								p.partida1 <> '00' AND
								p.generica = '00' AND
								p.especifica = '00' AND
								p.subespecifica = '00' AND
								SUBSTRING(p.cod_partida, 1, 3) IN (SELECT SUBSTRING(cod_partida, 1, 3) AS partida FROM pv_reformulaciondet WHERE 1 $filtro GROUP BY partida)
							 GROUP BY cod_partida)
							UNION
							(SELECT
								p.cod_partida,
								p.denominacion,
								'' AS CodFuente,
								p.cod_tipocuenta,
								p.partida1,
								p.generica,
								p.especifica,
								p.subespecifica,
								'T' AS tipo
							 FROM pv_partida p
							 WHERE
								p.cod_tipocuenta = '4' AND
								p.partida1 <> '00' AND
								p.generica <> '00' AND
								p.especifica = '00' AND
								p.subespecifica = '00' AND
								SUBSTRING(p.cod_partida, 1, 7) IN (SELECT SUBSTRING(cod_partida, 1, 7) AS partida FROM pv_reformulaciondet WHERE 1 $filtro GROUP BY partida)
							 GROUP BY cod_partida)
							UNION
							(SELECT
								p.cod_partida,
								p.denominacion,
								ppd.CodFuente,
								p.cod_tipocuenta,
								p.partida1,
								p.generica,
								p.especifica,
								p.subespecifica,
								p.tipo
							 FROM
							 	pv_partida p
							 	INNER JOIN pv_reformulaciondet ppd ON (ppd.cod_partida = p.cod_partida)
							 WHERE 
							 	p.cod_tipocuenta = '4' AND
							 	ppd.CodOrganismo = '$field[CodOrganismo]' AND
							 	ppd.CodReformulacion = '$field[CodReformulacion]')
							ORDER BY cod_partida;";
					$field_partida = getRecords($sql);
					foreach ($field_partida as $f) {
						++$nro_partida;
						$id = $f['cod_partida'];
						if ($f['partida1']=='00' && $f['generica']=='00' && $f['especifica']=='00' && $f['subespecifica']=='00') {
							$background="background-color:#B6B6B6;";
							$weight="font-weight:bold;";
							$detalle='cuenta';
						}
						elseif ($f['generica']=='00' && $f['especifica']=='00' && $f['subespecifica']=='00') {
							$background="background-color:#C7C7C7;";
							$weight="font-weight:bold;";
							$detalle='partida';
						}
						elseif ($f['especifica']=='00' && $f['subespecifica']=='00') {
							$background="background-color:#DEDEDE;";
							$weight="font-weight:bold;";
							$detalle='generica';
						}
						else {
							$background="";
							$weight="";
							$detalle='detalle';
						}
						if ($f['tipo'] == 'T') {
							$readonly = "disabled";
						}
						else {
							$readonly = "";
						}
						?>
						<tr class="trListaBody" style="<?=$background.$weight?>" id="partida_<?=$id?>" onclick="clk($(this), 'partida', 'partida_<?=$id?>');">
							<td align="center">
								<?php
								if ($detalle == 'detalle') {
									?>
									<select name="CodFuente[]" class="cell">
										<?=loadSelect2('pv_fuentefinanciamiento','CodFuente','Denominacion',$f['CodFuente'],20)?>
									</select>
									<?php
								} else {
									echo "&nbsp;";
								}
								?>
							</td>
							<td align="center">
								<input type="hidden" name="cod_partida[]" value="<?=$id?>" <?=$readonly?> />
								<input type="hidden" name="tipo[]" value="<?=$f['tipo']?>" <?=$readonly?> />
								<?=$f['cod_partida']?>
							</td>
							<td><input type="text" value="<?=htmlentities($f['denominacion'])?>" class="cell2" style="<?=$weight?>" readonly /></td>
						</tr>
					<?php
					}
					?>
				</tbody>
			</table>
		</div>
		<input type="hidden" id="nro_partida" value="<?=$nro_partida?>" />
		<input type="hidden" id="can_partida" value="<?=$nro_partida?>" />
	</div>

	<center>
		<input type="submit" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" id="btSubmit" />
		<input type="button" value="Cancelar" style="width:75px;" id="btCancelar" onclick="<?=$clkCancelar?>" />
	</center>
</form>
<div style="width:<?=$_width?>px" class="divMsj">Campos Obligatorios *</div>

<script type="text/javascript" language="javascript">
	function quitar_partida(boton, detalle) {
		boton.disabled = true;
		var can = "#can_" + detalle;
		var sel = "#sel_" + detalle;	
		var lista = "#lista_" + detalle;
		if ($(sel).val() == "") cajaModal("Debe seleccionar una linea", "error", 400);
		else {
			var candetalle = parseInt($(can).val()); candetalle--;
			$(can).val(candetalle);
			$(sel).val("");
			//
			var idtr = $(lista+" .trListaBodySel").attr('id');
			var partes = idtr.split('_');
			var partida = partes[1].split('.');
			//	
			$(lista+" .trListaBodySel").remove();
			var tc = 'tc' + partida[0].substr(0, 1);
			var p = 'p' + partida[0].substr(1, 2);
			var g = 'g' + partida[1];
			var tc = 'atc' + partida[0].substr(0, 1);
			var p = 'ap' + partida[0].substr(1, 2);
			var g = 'ag' + partida[1];
			// seleccionar todos los tr cuyo id empiece por lo especificado
			var selector_generica = "partida_" + partida[0] + "." + partida[1] + ".";
      		var trgenerica = $('tr[id*="'+selector_generica+'"]').length;
      		if (trgenerica == 1) {
      			$('tr[id*="'+selector_generica+'"]').remove();
				// seleccionar todos los tr cuyo id empiece por lo especificado
				var selector_partida = "partida_" + partida[0] + ".";
	      		var trpartida = $('tr[id*="'+selector_partida+'"]').length;
	      		if (trpartida == 1) $('tr[id*="'+selector_partida+'"]').remove();
      		}
		}
		boton.disabled = false;
	}
</script>