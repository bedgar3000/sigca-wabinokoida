<?php
if ($opcion == "nuevo") {
	$field['CodOrganismo'] = ($fCodOrganismo?$fCodOrganismo:$_SESSION["FILTRO_ORGANISMO_ACTUAL"]);
	$field['Fecha'] = $FechaActual;
	$field['Periodo'] = $PeriodoActual;
	$field['PreparadoPor'] = $_SESSION['CODPERSONA_ACTUAL'];
	$field['NomPreparadoPor'] = $_SESSION['NOMBRE_USUARIO_ACTUAL'];
	$field['FechaPreparado'] = $FechaActual;
	$field['CodFuente'] = $_PARAMETRO['FFMETASDEF'];
	$field['Estado'] = 'PR';
	##
	$_titulo = "Nuevo Ajuste";
	$accion = "nuevo";
	$disabled_ver = "";
	$disabled_fuente = "disabled";
	$disabled_aprobado = "disabled";
	$display_modificar = "";
	$display_cedentes = "display:none;";
	$display_ver = "";
	$display_submit = "";
	$opt_modificar = 0;
	$label_submit = "Guardar";
	$focus = "Descripcion";
}
elseif ($opcion == "modificar" || $opcion == "ver" || $opcion == "aprobar" || $opcion == "anular") {
	list($CodOrganismo, $CodAjuste) = explode('_', $sel_registros);
	##	consulto datos generales
	$sql = "SELECT
				aj.*,
				p.Ejercicio,
				p.CategoriaProg,
				cp.CodUnidadEjec,
				o.Organismo,
				ue.Denominacion AS UnidadEjecutora,
				p1.NomCompleto AS NomPreparadoPor,
				p2.NomCompleto AS NomAprobadoPor
			FROM 
				pv_ajustes aj
				INNER JOIN pv_presupuesto p ON (p.CodOrganismo = aj.CodOrganismo AND p.CodPresupuesto = aj.CodPresupuesto)
				INNER JOIN pv_categoriaprog cp ON (cp.CategoriaProg = p.CategoriaProg)
				INNER JOIN mastorganismos o ON (o.CodOrganismo = p.CodOrganismo)
				INNER JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cp.CodUnidadEjec)
				LEFT JOIN mastpersonas p1 ON (p1.CodPersona = aj.PreparadoPor)
				LEFT JOIN mastpersonas p2 ON (p2.CodPersona = aj.AprobadoPor)
			WHERE
				aj.CodOrganismo = '".$CodOrganismo."' AND
				aj.CodAjuste = '".$CodAjuste."'";
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
	##	modificar
	if ($opcion == "modificar") {
		$_titulo = "Modificar Ajuste";
		$accion = "modificar";
		$disabled_ver = "";
		$disabled_aprobado = (($field['Tipo']=='CA')?'':'disabled');
		$disabled_fuente = (($field['Tipo']=='CA')?'':'disabled');
		$display_modificar = "display:none;";
		$display_cedentes = (($field['Tipo']=='RT' || $field['Tipo']=='TC' || $field['Tipo']=='TP')?'':'display:none;');
		$display_ver = "";
		$display_submit = "";
		$opt_modificar = 1;
		$label_submit = "Modificar";
		$focus = "Descripcion";
	}
	##	ver
	elseif ($opcion == "ver") {
		$_titulo = "Ver Ajuste";
		$accion = "";
		$disabled_ver = "disabled";
		$disabled_aprobado = 'disabled';
		$disabled_fuente = "disabled";
		$display_modificar = "display:none;";
		$display_cedentes = (($field['Tipo']=='RT' || $field['Tipo']=='TC' || $field['Tipo']=='TP')?'':'display:none;');
		$display_ver = "display:none;";
		$display_submit = 'display:none;';
		$opt_modificar = 1;
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
		$_titulo = "Aprobar Ajuste";
		$accion = "aprobar";
		$disabled_ver = "disabled";
		$disabled_aprobado = 'disabled';
		$disabled_fuente = "disabled";
		$display_modificar = "display:none;";
		$display_cedentes = (($field['Tipo']=='RT' || $field['Tipo']=='TC' || $field['Tipo']=='TP')?'':'display:none;');
		$display_ver = "display:none;";
		$display_submit = "";
		$opt_modificar = 1;
		$label_submit = "Aprobar";
		$focus = "btSubmit";

	}
	##	anular
	elseif ($opcion == "anular") {
		$field['AnuladoPor'] = $_SESSION['CODPERSONA_ACTUAL'];
		$field['NomAnuladoPor'] = $_SESSION['NOMBRE_USUARIO_ACTUAL'];
		$field['FechaAnulado'] = $FechaActual;
		##	
		$_titulo = "Anular Ajuste";
		$accion = "anular";
		$disabled_ver = "disabled";
		$disabled_aprobado = 'disabled';
		$disabled_fuente = "disabled";
		$display_modificar = "display:none;";
		$display_cedentes = (($field['Tipo']=='RT' || $field['Tipo']=='TC' || $field['Tipo']=='TP')?'':'display:none;');
		$display_ver = "display:none;";
		$display_submit = "";
		$opt_modificar = 1;
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

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=<?=$action?>" method="POST" enctype="multipart/form-data" onsubmit="return formSubmitAjustes('pv_ajustes_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
	<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
	<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
	<input type="hidden" name="fIdSubSector" id="fIdSubSector" value="<?=$fIdSubSector?>" />
	<input type="hidden" name="fIdProyecto" id="fIdProyecto" value="<?=$fIdProyecto?>" />
	<input type="hidden" name="fCodDependencia" id="fCodDependencia" value="<?=$fCodDependencia?>" />
	<input type="hidden" name="fIdPrograma" id="fIdPrograma" value="<?=$fIdPrograma?>" />
	<input type="hidden" name="fIdActividad" id="fIdActividad" value="<?=$fIdActividad?>" />
	<input type="hidden" name="fCodUnidadEjec" id="fCodUnidadEjec" value="<?=$fCodUnidadEjec?>" />
	<input type="hidden" name="fIdSubPrograma" id="fIdSubPrograma" value="<?=$fIdSubPrograma?>" />
	<input type="hidden" name="fEjercicio" id="fEjercicio" value="<?=$fEjercicio?>" />

	<div id="tab1" style="display:block;">
		<table width="<?=$_width?>" class="tblForm">
			<tr>
				<td colspan="4" class="divFormCaption">Datos Generales</td>
			</tr>
			<tr>
				<td class="tagForm" width="150">C&oacute;digo:</td>
				<td>
					<input type="text" name="CodAjuste" id="CodAjuste" value="<?=$field['CodAjuste']?>" style="width:65px; font-weight:bold;" readonly="readonly" />
				</td>
				<td class="tagForm" width="125">Estado:</td>
				<td>
					<input type="hidden" name="Estado" id="Estado" value="<?=$field['Estado']?>" />
					<input type="text" value="<?=strtoupper(printValores('ajustes-estado',$field['Estado']))?>" style="width:100px; font-weight:bold;" disabled />
				</td>
			</tr>
			<tr>
				<td class="tagForm" width="150">Presupuesto:</td>
				<td class="gallery clearfix">
					<input type="text" name="Ejercicio" id="Ejercicio" value="<?=$field['Ejercicio']?>" style="width:48px;" class="Ejercicio" readonly />
					<input type="text" name="CodPresupuesto" id="CodPresupuesto" value="<?=$field['CodPresupuesto']?>" style="width:48px;" class="CodPresupuesto" readonly />
					<a href="../lib/listas/gehen.php?anz=lista_pv_presupuesto&filtrar=default&campo1=Ejercicio&campo2=CodPresupuesto&campo3=CategoriaProg&campo4=CodOrganismo&campo5=Organismo&ventana=pv_ajuste&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe14]" style=" <?=$display_ver?>" id="btPresupuesto">
		            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
		            </a>
				</td>
			</tr>
			<tr>
				<td class="tagForm">Cat. Prog.:</td>
				<td><input type="text" name="CategoriaProg" id="CategoriaProg" value="<?=$field['CategoriaProg']?>" style="width:100px;" class="CategoriaProg" readonly /></td>
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
					<input type="text" name="Fecha" id="Fecha" value="<?=formatFechaDMA($field['Fecha'])?>" class="datepicker" style="width:65px;" maxlength="10" <?=$disabled_ver?> onchange="$('#Periodo').val(this.value.substr(6,4)+'-'+this.value.substr(3,2));" />
				</td>
				<td class="tagForm">* Periodo:</td>
				<td>
					<input type="text" name="Periodo" id="Periodo" value="<?=$field['Periodo']?>" style="width:60px;" maxlength="7" readonly />
				</td>
			</tr>
		    <tr>
				<td class="tagForm">* Tipo:</td>
				<td>
					<select name="Tipo" id="Tipo" style="width:206px;" <?=$disabled_ver?> onchange="setTipo(this.value);">
						<?php if ($opcion == 'nuevo') { ?><option value="">&nbsp;</option><?php } ?>
						<?=getMiscelaneos($field['Tipo'],'TIPOAJUSTE',$opt_modificar)?>
					</select>
				</td>
				<td class="tagForm">Monto Aprobado:</td>
				<td>
					<input type="text" name="MontoAprobado" id="MontoAprobado" value="<?=number_format($field['MontoAprobado'],2,',','.')?>" style="width:100px; text-align:right;" class="currency" <?=$disabled_aprobado?> />
				</td>
			</tr>
		    <tr>
				<td class="tagForm">* Fuente de Financ.:</td>
				<td>
					<select name="CodFuente" id="CodFuente" style="width:206px;" <?=$disabled_fuente?>>
						<option value="">&nbsp;</option>
						<?=loadSelect2('pv_fuentefinanciamiento','CodFuente','Denominacion',$field['CodFuente'],10)?>
					</select>
				</td>
				<td class="tagForm">Total D&eacute;bitos:</td>
				<td>
					<input type="text" name="TotalDebitos" id="TotalDebitos" value="<?=number_format($field['TotalDebitos'],2,',','.')?>" style="width:100px; font-weight:bold; text-align:right;" readonly="readonly" />
				</td>
			</tr>
		    <tr>
				<td class="tagForm">Gaceta:</td>
				<td>
					<input type="text" name="NroGaceta" id="NroGaceta" value="<?=$field['NroGaceta']?>" style="width:200px;" maxlength="20" <?=$disabled_ver?> />
					<input type="text" name="FechaGaceta" id="FechaGaceta" value="<?=formatFechaDMA($field['FechaGaceta'])?>" class="datepicker" style="width:60px;" maxlength="10" <?=$disabled_ver?> />
				</td>
				<td class="tagForm">Total Cr&eacute;ditos:</td>
				<td>
					<input type="text" name="TotalCreditos" id="TotalCreditos" value="<?=number_format($field['TotalCreditos'],2,',','.')?>" style="width:100px; font-weight:bold; text-align:right;" readonly="readonly" />
				</td>
			</tr>
		    <tr>
				<td class="tagForm">Resoluci&oacute;n:</td>
				<td>
					<input type="text" name="NroResolucion" id="NroResolucion" value="<?=$field['NroResolucion']?>" style="width:200px;" maxlength="20" <?=$disabled_ver?> />
					<input type="text" name="FechaResolucion" id="FechaResolucion" value="<?=formatFechaDMA($field['FechaResolucion'])?>" class="datepicker" style="width:60px;" maxlength="10" <?=$disabled_ver?> />
				</td>
				<td class="tagForm">Preparado Por:</td>
				<td>
					<input type="hidden" name="PreparadoPor" id="PreparadoPor" value="<?=$field['PreparadoPor']?>" />
					<input type="text" name="NomPreparadoPor" id="NomPreparadoPor" value="<?=$field['NomPreparadoPor']?>" style="width:200px;" readonly />
					<input type="text" name="FechaPreparado" id="FechaPreparado" value="<?=formatFechaDMA($field['FechaPreparado'])?>" style="width:60px;" maxlength="10" readonly />
				</td>
			</tr>
		    <tr>
		    	<td>&nbsp;</td>
		    	<td>&nbsp;</td>
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
					<input type="text" value="<?=$field['UltimoUsuario']?>" style="width:140px;" disabled="disabled" />
					<input type="text" value="<?=$field['UltimaFecha']?>" style="width:120px" disabled="disabled" />
				</td>
			</tr>
		</table>
	</div>

	<div id="tab2" style="display:none;">
		<div id="lista_cedentes" style="<?=$display_cedentes?>">
			<input type="hidden" id="sel_partidac" />
			<table width="<?=$_width?>" class="tblBotones">
				<thead>
					<tr>
						<th class="divFormCaption" colspan="2">PARTIDAS CEDENTES</th>
					</tr>
				</thead>
			    <tbody>
			    <tr>
			        <td align="right" class="gallery clearfix">
						<a id="a_partida" href="pagina.php?iframe=true&width=950&height=430" rel="prettyPhoto[iframe2]" style="display:none;"></a>
			            <input type="button" class="btLista" value="Insertar" onclick="insertar_partida('partidac');" <?=$disabled_ver?> />
			            <input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'partidac');" <?=$disabled_ver?> />
			        </td>
			    </tr>
			    </tbody>
			</table>
			<div style="overflow:scroll; height:150px; width:<?=$_width?>px; margin:auto;">
				<table class="tblLista" style="width:100%; min-width:1200px;">
					<thead>
						<tr>
							<th width="90">Cat. Prog.</th>
							<th width="30">F.F.</th>
							<th width="80">Partida</th>
							<th align="left">Denominaci&oacute;n</th>
							<th width="100">Monto a Ceder</th>
							<th width="100">Monto Aprobado</th>
							<th width="100">Monto Ajustado</th>
							<th width="100">Monto Compromiso</th>
							<th width="100">Monto Disponible</th>
						</tr>
					</thead>
					
					<tbody id="lista_partidac">
						<?php
						$detalle = "partidac";
						$nro_partidac = 0;
						$sql = "SELECT
									ajd.*,
									pv.CategoriaProg,
									pv.Ejercicio,
									p.denominacion,
									pd.MontoAprobado,
									pd.MontoAjustado,
									pd.MontoCompromiso
								FROM
									pv_ajustesdet ajd
									INNER JOIN pv_partida p ON (p.cod_partida = ajd.cod_partida)
									INNER JOIN pv_presupuestodet pd ON (
										pd.cod_partida = ajd.cod_partida
										AND pd.CodPresupuesto = ajd.CodPresupuesto
										AND pd.CodOrganismo = ajd.CodOrganismo
										AND pd.CodFuente = ajd.CodFuente
									)
									INNER JOIN pv_presupuesto pv ON (pv.CodOrganismo = pd.CodOrganismo AND pv.CodPresupuesto = pd.CodPresupuesto)
								WHERE
									ajd.CodOrganismo = '".$CodOrganismo."' AND
									ajd.CodAjuste = '".$CodAjuste."' AND
									ajd.Tipo = 'D'";
						$field_partidac = getRecords($sql);
						foreach ($field_partidac as $f) {
							++$nro_partidac;
							$id = $f['cod_partida'].$nro_partidac;
							$id = str_replace('.', '', $f['cod_partida'].$nro_partidac);
							$MontoDisponible = $f['MontoAjustado'] - $f['MontoCompromiso'];
							?>
							<tr class="trListaBody" id="<?=$detalle?>_<?=$id?>" onclick="clk($(this), '<?=$detalle?>', '<?=$detalle?>_<?=$id?>');">
								<td align="center">
									<input type="text" name="<?=$detalle?>_CategoriaProg[]" value="<?=$f['CategoriaProg']?>" class="cell2" />
									<input type="hidden" name="<?=$detalle?>_Ejercicio[]" value="<?=$f['Ejercicio']?>" />
									<input type="hidden" name="<?=$detalle?>_CodPresupuesto[]" value="<?=$f['CodPresupuesto']?>" />
								</td>
					            <td>
									<select name="<?=$detalle?>_CodFuente[]" class="cell2 CodFuente">
										<?=loadSelect2("pv_fuentefinanciamiento","CodFuente","Denominacion",$f['CodFuente'],11)?>
									</select>
					            </td>
								<td align="center">
									<input type="hidden" name="<?=$detalle?>_cod_partida[]" value="<?=$f['cod_partida']?>" />
									<?=$f['cod_partida']?>
								</td>
								<td><input type="text" value="<?=htmlentities($f['denominacion'])?>" class="cell2" readonly /></td>
								<td><input type="text" name="<?=$detalle?>_MontoAjuste[]" value="<?=number_format($f['MontoAjuste'],2,',','.')?>" class="cell currency" style="text-align:right;" onchange="setMontos('<?=$detalle?>');" /></td>
								<td align="right"><?=number_format($f['MontoAprobado'],2,',','.')?></td>
								<td align="right"><?=number_format($f['MontoAjustado'],2,',','.')?></td>
								<td align="right"><?=number_format($f['MontoCompromiso'],2,',','.')?></td>
								<td align="right"><input type="text" name="<?=$detalle?>_MontoDisponible[]" value="<?=number_format($MontoDisponible,2,',','.')?>" class="cell2" style="text-align:right;" readonly /></td>
							</tr>
							<?php
						}
						?>
					</tbody>
				</table>
			</div>
			<table width="<?=$_width?>" class="tblBotones">
			    <tbody>
			    <tr>
			        <td align="right">
			        	<strong>Total D&eacute;bitos: </strong>
			        	<input type="text" id="Debitos" value="<?=number_format($field['TotalDebitos'],2,',','.')?>" style="width:125px; font-weight:bold; text-align:right;" disabled>
			        </td>
			    </tr>
			    </tbody>
			</table>
			<input type="hidden" id="nro_partidac" value="<?=$nro_partidac?>" />
			<input type="hidden" id="can_partidac" value="<?=$nro_partidac?>" />
		</div>

		<div id="lista_receptoras">
			<input type="hidden" id="sel_partidar" />
			<table width="<?=$_width?>" class="tblBotones">
				<thead>
					<tr>
						<th class="divFormCaption" colspan="2">PARTIDAS RECEPTORAS</th>
					</tr>
				</thead>
			    <tbody>
			    <tr>
			    	<td class="gallery clearfix">
			    		<a id="aSelCategoriaProgR" href="../lib/listas/gehen.php?anz=lista_pv_presupuesto&filtrar=default&campo1=partidar_CategoriaProg&campo2=partidar_Ejercicio&campo3=partidar_CodPresupuesto&ventana=selListadoListaParentRequerimiento&seldetalle=sel_partidar&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe3]" style="display:none;"></a>
			    		<input type="button" style="width:90px;" id="btSelCategoriaProgR" value="Sel. Presupuesto" onclick="validarAbrirLista('sel_partidar', 'aSelCategoriaProgR');" <?=$disabled_ver?> />
			    	</td>
			        <td align="right" class="gallery clearfix">
			            <input type="button" class="btLista" value="Insertar" onclick="insertar_partida('partidar');" <?=$disabled_ver?> />
			            <input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'partidar');" <?=$disabled_ver?> />
			        </td>
			    </tr>
			    </tbody>
			</table>
			<div style="overflow:scroll; height:150px; width:<?=$_width?>px; margin:auto;">
				<table class="tblLista" style="width:100%; min-width:1200px;">
					<thead>
						<tr>
							<th width="90">Cat. Prog.</th>
							<th width="30">F.F.</th>
							<th width="80">Partida</th>
							<th align="left">Denominaci&oacute;n</th>
							<th width="100">Monto a Recibir</th>
							<th width="100">Monto Aprobado</th>
							<th width="100">Monto Ajustado</th>
							<th width="100">Monto Compromiso</th>
							<th width="100">Monto Disponible</th>
						</tr>
					</thead>
					
					<tbody id="lista_partidar">
						<?php
						$detalle = "partidar";
						$nro_partidar = 0;
						$sql = "SELECT
									ajd.*,
									pv.CategoriaProg,
									pv.Ejercicio,
									p.denominacion,
									pd.MontoAprobado,
									pd.MontoAjustado,
									pd.MontoCompromiso
								FROM
									pv_ajustesdet ajd
									INNER JOIN pv_partida p ON (p.cod_partida = ajd.cod_partida)
									INNER JOIN pv_presupuestodet pd ON (
										pd.cod_partida = ajd.cod_partida
										AND pd.CodPresupuesto = ajd.CodPresupuesto
										AND pd.CodOrganismo = ajd.CodOrganismo
										AND pd.CodFuente = ajd.CodFuente
									)
									INNER JOIN pv_presupuesto pv ON (pv.CodOrganismo = pd.CodOrganismo AND pv.CodPresupuesto = pd.CodPresupuesto)
								WHERE
									ajd.CodOrganismo = '".$CodOrganismo."' AND
									ajd.CodAjuste = '".$CodAjuste."' AND
									ajd.Tipo = 'I'";
						$field_partidac = getRecords($sql);
						foreach ($field_partidac as $f) {
							++$nro_partidar;
							$id = $f['cod_partida'].$nro_partidar;
							$id = str_replace('.', '', $f['cod_partida'].$nro_partidar);
							$MontoDisponible = $f['MontoAjustado'] - $f['MontoCompromiso'];
							?>
							<tr class="trListaBody" id="<?=$detalle?>_<?=$id?>" onclick="clk($(this), '<?=$detalle?>', '<?=$detalle?>_<?=$id?>');">
								<td align="center">
									<input type="text" name="<?=$detalle?>_CategoriaProg[]" id="<?=$detalle?>_CategoriaProg_<?=$id?>" value="<?=$f['CategoriaProg']?>" class="cell2" />
									<input type="hidden" name="<?=$detalle?>_Ejercicio[]" id="<?=$detalle?>_Ejercicio_<?=$id?>" value="<?=$f['Ejercicio']?>" />
									<input type="hidden" name="<?=$detalle?>_CodPresupuesto[]" id="<?=$detalle?>_CodPresupuesto_<?=$id?>" value="<?=$f['CodPresupuesto']?>" />
								</td>
					            <td>
									<select name="<?=$detalle?>_CodFuente[]" class="cell2 CodFuente">
										<?=loadSelect2("pv_fuentefinanciamiento","CodFuente","Denominacion",$f['CodFuente'],11)?>
									</select>
					            </td>
								<td align="center">
									<input type="hidden" name="<?=$detalle?>_cod_partida[]" value="<?=$f['cod_partida']?>" />
									<?=$f['cod_partida']?>
								</td>
								<td><input type="text" value="<?=htmlentities($f['denominacion'])?>" class="cell2" readonly /></td>
								<td><input type="text" name="<?=$detalle?>_MontoAjuste[]" value="<?=number_format($f['MontoAjuste'],2,',','.')?>" class="cell currency" style="text-align:right;" onchange="setMontos('<?=$detalle?>');" <?=$disabled_ver?> /></td>
								<td align="right"><?=number_format($f['MontoAprobado'],2,',','.')?></td>
								<td align="right"><?=number_format($f['MontoAjustado'],2,',','.')?></td>
								<td align="right"><?=number_format($f['MontoCompromiso'],2,',','.')?></td>
								<td align="right"><input type="text" name="<?=$detalle?>_MontoDisponible[]" value="<?=number_format($MontoDisponible,2,',','.')?>" class="cell2" style="text-align:right;" readonly /></td>
							</tr>
							<?php
						}
						?>
					</tbody>
				</table>
			</div>
			<table width="<?=$_width?>" class="tblBotones">
			    <tbody>
			    <tr>
			        <td align="right">
			        	<strong>Total Cr&eacute;ditos: </strong>
			        	<input type="text" id="Creditos" value="<?=number_format($field['TotalCreditos'],2,',','.')?>" style="width:125px; font-weight:bold; text-align:right;" disabled>
			        </td>
			    </tr>
			    </tbody>
			</table>
			<input type="hidden" id="nro_partidar" value="<?=$nro_partidar?>" />
			<input type="hidden" id="can_partidar" value="<?=$nro_partidar?>" />
		</div>
	</div>

	<center>
		<input type="submit" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" id="btSubmit" />
		<input type="button" value="Cancelar" style="width:75px;" id="btCancelar" onclick="<?=$clkCancelar?>" />
	</center>
</form>
<div style="width:<?=$_width?>px" class="divMsj">Campos Obligatorios *</div>

<script type="text/javascript" language="javascript">
	function formSubmitAjustes(url, data, form) {
		bloqueo(true);
		if (!form) var form = document.getElementById($('form').attr('id'));
		var idform = form.id;
		//	ajax
		$.ajax({
			type: "POST",
			url: url + ".php",
			data: data+"&"+$('#'+idform).serialize(),
			async: false,
			success: function(resp) {
				if (resp.trim() != '') cajaModal(resp.trim(), 'error', 400);
				else {
					if ('<?=$opcion?>' == 'aprobar') {
						form.action = form.action + '&FlagImprimir=S';
					}
					form.submit();
				}
			}
		});
		return false;
	}
	function setMontos(detalle) {
		var Total = 0;
		var iMontoAjuste = $('input[name="'+detalle+'_MontoAjuste[]"]');
		iMontoAjuste.each(function() {
			var Monto = setNumero($(this).val());
			Total += Monto;
		});
		if (detalle == 'partidac') {
			$('#TotalDebitos').val(Total).formatCurrency();
			$('#Debitos').val(Total).formatCurrency();
		}
		else if (detalle == 'partidar') {
			$('#TotalCreditos').val(Total).formatCurrency();
			$('#Creditos').val(Total).formatCurrency();
		}
	}
	function insertar_partida(detalle) {
		if ($('#CodPresupuesto').val() == '') {
			cajaModal('Debe seleccionar el Presupuesto');
		} else {
			var href = "../lib/listas/gehen.php?anz=lista_pv_partida_presupuesto&filtrar=default&ventana=pv_ajustes&detalle="+detalle+"&modulo=ajax&accion=partida_insertar&url=../../pv/pv_ajustes_ajax.php&Tipo="+$('#Tipo').val()+"&fCodOrganismo="+$('#CodOrganismo').val()+"&fCodPresupuesto="+$('#CodPresupuesto').val()+"&fEjercicio="+$('#Ejercicio').val()+"&fCategoriaProg="+$('#CategoriaProg').val()+"&iframe=true&width=100%&height=100%";
			$('#a_partida').attr('href',href);
			$('#a_partida').click();
		}
	}
	function setTipo(Tipo) {
		$('#lista_partidac').html('');
		$('#lista_partidar').html('');
		//	Crédito Adicional
		if (Tipo == 'CA') {
			$('#MontoAprobado').val('0,00').attr('disabled', false);
			$('#CodFuente').val('').attr('disabled', false);
			$('#lista_cedentes').css('display','none');
			$('#lista_receptoras').css('display','block');
		}
		//	Reintegro
		else if (Tipo == 'RI') {
			$('#MontoAprobado').val('0,00').attr('disabled', true);
			$('#CodFuente').val('').attr('disabled', true);
			$('#lista_cedentes').css('display','none');
			$('#lista_receptoras').css('display','block');
		}
		//	Rectificaciones
		else if (Tipo == 'RT') {
			$('#MontoAprobado').val('0,00').attr('disabled', true);
			$('#CodFuente').val('').attr('disabled', true);
			$('#lista_cedentes').css('display','block');
			$('#lista_receptoras').css('display','block');
		}
		//	Traslado Cámara
		else if (Tipo == 'TC') {
			$('#MontoAprobado').val('0,00').attr('disabled', true);
			$('#CodFuente').val('').attr('disabled', true);
			$('#lista_cedentes').css('display','block');
			$('#lista_receptoras').css('display','block');
		}
		//	Traslado entre Partidas
		else if (Tipo == 'TP') {
			$('#MontoAprobado').val('0,00').attr('disabled', true);
			$('#CodFuente').val('').attr('disabled', true);
			$('#lista_cedentes').css('display','block');
			$('#lista_receptoras').css('display','block');
		}
		setMontos('partidac');
		setMontos('partidar');
	}
</script>