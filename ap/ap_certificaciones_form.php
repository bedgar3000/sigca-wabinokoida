<?php
if ($opcion == "nuevo") {
	$field['CodOrganismo'] = $_SESSION["FILTRO_ORGANISMO_ACTUAL"];
	$field['CodFuente'] = $_PARAMETRO['FFMETASDEF'];
	$field['Estado'] = 'PR';
	$field['PreparadoPor'] = $_SESSION["CODPERSONA_ACTUAL"];
	$field['NomPreparadoPor'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
	$field['FechaPreparado'] = $FechaActual;
	##
	$_titulo = "Nuevo Gasto";
	$accion = "nuevo";
	$disabled_ver = "";
	$disabled_anular = "disabled";
	$display_modificar = "";
	$display_ver = "";
	$display_submit = "";
	$label_submit = "Guardar";
	$focus = "Justificacion";
	$return = (!isset($return)?"":"ap_certificaciones_lista");
	$action = "gehen.php?anz=$return";
	$getDistribucion = "getDistribucion();";
}
elseif ($opcion == "generar-compromiso") {
	##	consulto datos generales
	$sql = "SELECT
				o.CodObra,
				o.CodOrganismo,
				o.CodProveedor AS CodPersona,
				o.FechaActaInicio AS Fecha,
				o.CodInterno AS NroInterno,
				o.CodPresupuesto,
				o.CodFuente,
				o.Nombre AS Justificacion,
				'09' AS CodTipoCertif,
				p.NomCompleto AS NomPersona,
				ppto.Ejercicio,
				ppto.CategoriaProg
			FROM
				ob_obras o
				INNER JOIN mastpersonas p ON (p.CodPersona = o.CodProveedor)
				LEFT JOIN pv_presupuesto ppto ON (ppto.CodOrganismo = o.CodOrganismo AND ppto.CodPresupuesto = o.CodPresupuesto)
			WHERE o.CodObra = '$sel_registros'";
	$field = getRecord($sql);
	$field['Estado'] = 'PR';
	$field['PreparadoPor'] = $_SESSION["CODPERSONA_ACTUAL"];
	$field['NomPreparadoPor'] = $_SESSION["NOMBRE_USUARIO_ACTUAL"];
	$field['FechaPreparado'] = $FechaActual;
	##	
	$sql = "SELECT
				op.CodObra,
				op.CodOrganismo,
				op.CodPresupuesto,
				op.CodFuente,
				op.cod_partida,
				op.MontoAprobado AS Monto,
				pv.denominacion AS Descripcion,
				pv.CodCuenta,
				pv.CodCuentaPub20,
				'' AS NomCategoria,
				ppto.Ejercicio,
				ppto.CategoriaProg
			FROM
				ob_obraspresupuesto op
				LEFT JOIN pv_presupuesto ppto ON (ppto.CodOrganismo = op.CodOrganismo AND ppto.CodPresupuesto = op.CodPresupuesto)
				INNER JOIN pv_partida pv ON (pv.cod_partida = op.cod_partida)
			WHERE op.CodObra = '$sel_registros'";
	$field_concepto = getRecords($sql);
	##
	$_titulo = "Nuevo Gasto";
	$accion = "generar-compromiso";
	$disabled_ver = "";
	$disabled_anular = "disabled";
	$display_modificar = "display:none;";
	$display_ver = "";
	$display_submit = "";
	$label_submit = "Guardar";
	$focus = "Justificacion";
	$return = "ob_obras_lista";
	$action = "../ob/gehen.php?anz=$return";
	$getDistribucion = "getDistribucion();";
}
elseif ($opcion == "modificar" || $opcion == "ver" || $opcion == "revisar" || $opcion == "generar" || $opcion == "anular") {
	##	consulto datos generales
	$sql = "SELECT
				c.*,
				p.NomCompleto AS NomPersona,
				p1.NomCompleto AS NomPreparadoPor,
				p2.NomCompleto AS NomRevisadoPor,
				p3.NomCompleto AS NomGeneradoPor,
				p4.NomCompleto AS NomAnuladoPor,
				ppto.Ejercicio,
				ppto.CategoriaProg
			FROM
				ap_certificaciones c
				INNER JOIN mastpersonas p ON (p.CodPersona = c.CodPersona)
				LEFT JOIN pv_presupuesto ppto ON (ppto.CodOrganismo = c.CodOrganismo AND ppto.CodPresupuesto = c.CodPresupuesto)
				LEFT JOIN mastpersonas p1 ON (p1.CodPersona = c.PreparadoPor)
				LEFT JOIN mastpersonas p2 ON (p2.CodPersona = c.RevisadoPor)
				LEFT JOIN mastpersonas p3 ON (p3.CodPersona = c.GeneradoPor)
				LEFT JOIN mastpersonas p4 ON (p4.CodPersona = c.AnuladoPor)
			WHERE c.CodCertificacion = '$sel_registros'";
	$field = getRecord($sql);
	##	
	$sql = "SELECT
				cd.*,
				md.Descripcion AS NomCategoria,
				ppto.Ejercicio,
				ppto.CategoriaProg
			FROM
				ap_certificacionesdet cd
				LEFT JOIN mastmiscelaneosdet md ON (md.CodDetalle = cd.Categoria AND md.CodMaestro = 'CATCERTIF')
				LEFT JOIN pv_presupuesto ppto ON (ppto.CodOrganismo = cd.CodOrganismo AND ppto.CodPresupuesto = cd.CodPresupuesto)
			WHERE cd.CodCertificacion = '$field[CodCertificacion]'";
	$field_concepto = getRecords($sql);
	##
	if ($opcion == "modificar") {
		$_titulo = "Modificar Gasto";
		$accion = "modificar";
		$disabled_ver = "";
		$disabled_anular = "disabled";
		$display_modificar = "display:none;";
		$display_ver = "";
		$display_submit = "";
		$label_submit = "Modificar";
		$focus = "Descripcion";
		$getDistribucion = "getDistribucion();";
	}
	##
	elseif ($opcion == "ver") {
		$_titulo = "Ver Gasto";
		$accion = "";
		$disabled_ver = "disabled";
		$disabled_anular = "disabled";
		$display_modificar = "display:none;";
		$display_ver = "display:none;";
		$display_submit = "display:none;";
		$label_submit = "";
		$focus = "btCancelar";
		$getDistribucion = "";
	}
	##
	elseif ($opcion == "revisar") {
		$_titulo = "Revisar Gasto";
		$accion = "revisar";
		$disabled_ver = "disabled";
		$disabled_anular = "disabled";
		$display_modificar = "display:none;";
		$display_ver = "display:none;";
		$display_submit = "";
		$label_submit = "Revisar";
		$focus = "btCancelar";
		$getDistribucion = "";
	}
	##
	elseif ($opcion == "anular") {
		$_titulo = "Anular Gasto";
		$accion = "anular";
		$disabled_ver = "disabled";
		$disabled_anular = "disabled";
		$display_modificar = "display:none;";
		$display_ver = "display:none;";
		$display_submit = "";
		$label_submit = "Anular";
		$focus = "btCancelar";
		$getDistribucion = "";
	}
	$return = "ap_certificaciones_lista";
	$action = "gehen.php?anz=$return";
}
$clkCancelar = "document.getElementById('frmentrada').submit();";
//	------------------------------------
$_width = 800;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$_titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" />

<table align="center" cellpadding="0" cellspacing="0" style="width:<?=$_width?>px;">
    <tr>
        <td>
            <div class="header">
	            <ul id="tab">
		            <li id="li1" onclick="currentTab('tab', this);" class="current"><a href="#" onclick="mostrarTab('tab', 1, 2);">Informaci&oacute;n General</a></li>
		            <li id="li2" onclick="currentTab('tab', this); <?=$getDistribucion?>"><a href="#" onclick="mostrarTab('tab', 2, 2);">Distribuci&oacute;n Presupuesto</a></li>
	            </ul>
            </div>
        </td>
    </tr>
</table>

<form name="frmentrada" id="frmentrada" action="<?=$action?>" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('ap_certificaciones_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
	<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
	<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
	<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
	<input type="hidden" name="fTipo" id="fTipo" value="<?=$fTipo?>" />
	<input type="hidden" name="fFechaD" id="fFechaD" value="<?=$fFechaD?>" />
	<input type="hidden" name="fFechaH" id="fFechaH" value="<?=$fFechaH?>" />
	<input type="hidden" name="fCodPersona" id="fCodPersona" value="<?=$fCodPersona?>" />
	<input type="hidden" name="fNomPersona" id="fNomPersona" value="<?=$fNomPersona?>" />
	<input type="hidden" name="CodCertificacion" id="CodCertificacion" value="<?=$field['CodCertificacion']?>" />
	<input type="hidden" name="CodObra" id="CodObra" value="<?=$field['CodObra']?>" />

	<div id="tab1" style="display:block;">
		<table width="<?=$_width?>" class="tblForm">
			<tr>
		    	<td colspan="4" class="divFormCaption">Datos Generales</td>
		    </tr>
		    <tr>
				<td class="tagForm">* Organismo:</td>
				<td>
					<select name="CodOrganismo" id="CodOrganismo" style="width:250px;" <?=$disabled_ver?> onchange="resetearOrganismo();">
						<?=getOrganismos($field['CodOrganismo'],3)?>
					</select>
				</td>
				<td class="tagForm">C&oacute;digo:</td>
				<td>
		        	<input type="text" name="CodInterno" id="CodInterno" value="<?=$field['CodInterno']?>" style="width:100px; font-weight:bold;" readonly="readonly" />
				</td>
			</tr>
		    <tr>
				<td class="tagForm">* Tipo:</td>
				<td>
					<select name="CodTipoCertif" id="CodTipoCertif" style="width:250px;" <?=$disabled_ver?>>
						<?php
						if ($opcion == "generar-compromiso") {
							echo loadSelect2('ap_tiposcertificacion','CodTipoCertif','Descripcion',$field['CodTipoCertif'],11);
						} else {
							echo loadSelect2('ap_tiposcertificacion','CodTipoCertif','Descripcion',$field['CodTipoCertif'],10);
						}
						?>
					</select>
				</td>
				<td class="tagForm">Estado:</td>
				<td>
		        	<input type="hidden" name="Estado" id="Estado" value="<?=$field['Estado']?>" />
		        	<input type="text" value="<?=mb_strtoupper(printValores('certificaciones-estado',$field['Estado']))?>" style="width:100px; font-weight:bold;" disabled />
				</td>
			</tr>
			<tr>
				<td align="right">* Beneficiario: </td>
				<td class="gallery clearfix">
					<input type="text" name="CodPersona" id="CodPersona" value="<?=$field['CodPersona']?>" style="width:45px;" class="disabled" readonly />
					<input type="text" name="NomPersona" id="NomPersona" value="<?=$field['NomPersona']?>" style="width:201px;" class="disabled" readonly />
		            <a href="../lib/listas/gehen.php?anz=lista_personas&filtrar=default&campo1=CodPersona&campo2=NomPersona&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" id="btCodPersona" style=" <?=$display_ver?>">
		            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
		            </a>
		        </td>
				<td class="tagForm">* Fecha:</td>
				<td>
		        	<input type="text" name="Fecha" id="Fecha" value="<?=formatFechaDMA($field['Fecha'])?>" style="width:100px;" class="datepicker" maxlength="10" <?=$disabled_ver?> />
				</td>
			</tr>
			<tr>
				<td align="right">Obligaci&oacute;n: </td>
				<td>
					<input type="text" name="CodTipoDocumento" id="CodTipoDocumento" value="<?=$field['CodTipoDocumento']?>" style="width:45px;" disabled />
					<input type="text" name="NroDocumento" id="NroDocumento" value="<?=$field['NroDocumento']?>" style="width:132px;" disabled />
		        </td>
				<td class="tagForm">* Nro. Solicitud:</td>
				<td>
		        	<input type="text" name="NroInterno" id="NroInterno" value="<?=$field['NroInterno']?>" style="width:100px;" maxlength="30" <?=$disabled_ver?> />
				</td>
			</tr>
			<tr>
				<td align="right">Preparado Por: </td>
				<td class="gallery clearfix">
					<input type="hidden" name="PreparadoPor" id="PreparadoPor" value="<?=$field['PreparadoPor']?>" />
					<input type="text" name="NomPreparadoPor" id="NomPreparadoPor" value="<?=$field['NomPreparadoPor']?>" style="width:181px;" disabled />
					<input type="text" name="FechaPreparado" id="FechaPreparado" value="<?=formatFechaDMA($field['FechaPreparado'])?>" style="width:65px;" readonly />
		            <a href="../lib/listas/gehen.php?anz=lista_personas&filtrar=default&campo1=PreparadoPor&campo2=NomPreparadoPor&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe3]" id="btPreparadoPor" style="display:none;">
		            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
		            </a>
		        </td>
				<td class="tagForm">Presupuesto:</td>
				<td class="gallery clearfix">
					<input type="text" name="Ejercicio" id="Ejercicio" value="<?=$field['Ejercicio']?>" style="width:48px;" readonly />
					<input type="text" name="CodPresupuesto" id="CodPresupuesto" value="<?=$field['CodPresupuesto']?>" style="width:48px;" readonly />
					<a href="../lib/listas/gehen.php?anz=lista_pv_presupuesto&filtrar=default&campo1=Ejercicio&campo2=CodPresupuesto&campo3=CategoriaProg&ventana=categorias&FlagOrganismo=S&fCodOrganismo=<?=$field['CodOrganismo']?>&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe2]" style=" <?=$display_modificar?>" id="btPresupuesto">
		            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
		            </a>
				</td>
			</tr>
			<tr>
				<td align="right">Revisado Por: </td>
				<td class="gallery clearfix">
					<input type="hidden" name="RevisadoPor" id="RevisadoPor" value="<?=$field['RevisadoPor']?>" />
					<input type="text" name="NomRevisadoPor" id="NomRevisadoPor" value="<?=$field['NomRevisadoPor']?>" style="width:181px;" disabled />
					<input type="text" name="FechaRevisado" id="FechaRevisado" value="<?=formatFechaDMA($field['FechaRevisado'])?>" style="width:65px;" readonly />
		            <a href="../lib/listas/gehen.php?anz=lista_personas&filtrar=default&campo1=RevisadoPor&campo2=NomRevisadoPor&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe3]" id="btRevisadoPor" style="display:none;">
		            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
		            </a>
		        </td>
				<td class="tagForm">Cat. Prog.:</td>
				<td>
					<input type="text" name="CategoriaProg" id="CategoriaProg" value="<?=$field['CategoriaProg']?>" style="width:100px;" readonly />
				</td>
			</tr>
			<tr>
				<td align="right">Generado Por: </td>
				<td class="gallery clearfix">
					<input type="hidden" name="GeneradoPor" id="GeneradoPor" value="<?=$field['GeneradoPor']?>" />
					<input type="text" name="NomGeneradoPor" id="NomGeneradoPor" value="<?=$field['NomGeneradoPor']?>" style="width:181px;" disabled />
					<input type="text" name="FechaGenerado" id="FechaGenerado" value="<?=formatFechaDMA($field['FechaGenerado'])?>" style="width:65px;" readonly />
		            <a href="../lib/listas/gehen.php?anz=lista_personas&filtrar=default&campo1=GeneradoPor&campo2=NomGeneradoPor&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe3]" id="btGeneradoPor" style="display:none;">
		            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
		            </a>
		        </td>
				<td class="tagForm">Fuente Financiamiento:</td>
				<td>
					<select name="CodFuente" id="CodFuente" style="width:40px;" onchange="$('.CodFuente').val(this.value); getDistribucion();" <?=$disabled_ver?>>
						<?php
						if ($opcion == "generar-compromiso") {
							echo loadSelect2("pv_fuentefinanciamiento","CodFuente","Denominacion",$field['CodFuente'],11);
						} else {
							echo loadSelect2("pv_fuentefinanciamiento","CodFuente","Denominacion",$field['CodFuente'],10);
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td align="right">Anulado Por: </td>
				<td class="gallery clearfix">
					<input type="hidden" name="AnuladoPor" id="AnuladoPor" value="<?=$field['AnuladoPor']?>" />
					<input type="text" name="NomAnuladoPor" id="NomAnuladoPor" value="<?=$field['NomAnuladoPor']?>" style="width:181px;" disabled />
					<input type="text" name="FechaAnulado" id="FechaAnulado" value="<?=formatFechaDMA($field['FechaAnulado'])?>" style="width:65px;" readonly />
		            <a href="../lib/listas/gehen.php?anz=lista_personas&filtrar=default&campo1=AnuladoPor&campo2=NomAnuladoPor&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe3]" id="btAnuladoPor" style="display:none;">
		            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
		            </a>
		        </td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		    <tr>
				<td class="tagForm">* Justificaci&oacute;n:</td>
				<td colspan="3">
		        	<textarea name="Justificacion" id="Justificacion" style="width:95%; height:50px;" <?=$disabled_ver?>><?=$field['Justificacion']?></textarea>
				</td>
			</tr>
		    <tr>
				<td class="tagForm">Motivo Anulaci&oacute;n:</td>
				<td colspan="3">
		        	<textarea name="MotivoAnulacion" id="MotivoAnulacion" style="width:95%; height:20px;" <?=$disabled_anular?>><?=$field['MotivoAnulacion']?></textarea>
				</td>
			</tr>
		    <tr>
				<td class="tagForm">&Uacute;ltima Modif.:</td>
				<td>
					<input type="text" value="<?=$field['UltimoUsuario']?>" style="width:110px;" disabled="disabled" />
					<input type="text" value="<?=$field['UltimaFecha']?>" style="width:136px" disabled="disabled" />
				</td>
			</tr>
		</table>

		<center>
			<input type="submit" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" id="btSubmit" />
			<input type="button" value="Cancelar" style="width:75px;" id="btCancelar" onclick="<?=$clkCancelar?>" />
		</center>

		<input type="hidden" id="sel_concepto" />
		<table width="<?=$_width?>" class="tblBotones">
			<thead>
				<tr>
					<th class="divFormCaption">Conceptos de Gasto</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td align="right" class="gallery clearfix">
						<a id="a_concepto" href="../lib/listas/gehen.php?anz=lista_ap_conceptoscertificacion&filtrar=default&CodPresupuesto=<?=$field['CodPresupuesto']?>&Ejercicio=<?=$field['Ejercicio']?>&CategoriaProg=<?=$field['CategoriaProg']?>&CodFuente=<?=$field['CodFuente']?>&ventana=ap_certificaciones&detalle=concepto&modulo=ajax&accion=concepto_insertar&url=../../ap/ap_certificaciones_ajax.php&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe7]" style="display:none;"></a>
						<input type="button" id="btInsertarConcepto" class="btLista" value="Concepto" onclick="concepto_insertar();" <?=$disabled_ver?> />
						<input type="button" id="btInsertarPartida" class="btLista" value="Partida" onclick="partida_insertar();" <?=$disabled_ver?> />
						<input type="button" id="btBorrar" class="btLista" value="Borrar" onclick="quitar(this, 'concepto'); setTotal(); getDistribucion();" <?=$disabled_ver?> />
					</td>
				</tr>
			</tbody>
		</table>
		<div style="overflow:scroll; height:150px; width:<?=$_width?>px; margin:auto;">
			<table class="tblLista" style="width:100%;">
				<thead>
					<tr>
						<th width="20">#</th>
						<th width="35">Cod.</th>
						<th align="left">Descripci&oacute;n</th>
						<th width="90">Cat. Prog.</th>
						<th width="30">F.F</th>
						<th width="75">Partida</th>
						<th width="65">Categor&iacute;a</th>
						<th width="90">Monto</th>
					</tr>
				</thead>
				
				<tbody id="lista_concepto">
					<?php
					$nro_concepto = 0;
					$Monto = 0;
					foreach ($field_concepto as $f) {
						$id = ($f['CodConcepto']?$f['CodConcepto']:$f['cod_partida']);
						?>
						<tr class="trListaBody" onclick="clk($(this), 'concepto', 'concepto_<?=$id?>');" id="concepto_<?=$id?>">
							<th>
								<input type="hidden" name="concepto_CodConcepto[]" value="<?=$f['CodConcepto']?>" />
								<?=++$nro_concepto;?>
							</th>
							<td align="center"><?=$f['CodConcepto']?></td>
							<td><input type="text" name="concepto_Descripcion[]" value="<?=htmlentities($f['Descripcion'])?>" class="cell"></td>
							<td>
								<input type="hidden" name="concepto_CodPresupuesto[]" value="<?=$f['CodPresupuesto']?>" class="CodPresupuesto" />
								<input type="hidden" name="concepto_Ejercicio[]" value="<?=$f['Ejercicio']?>" class="Ejercicio" />
								<input type="text" name="concepto_CategoriaProg[]" value="<?=$f['CategoriaProg']?>" class="cell CategoriaProg" style="text-align:center;">
							</td>
							<td>
								<select name="concepto_CodFuente[]" class="cell2 CodFuente" <?=$disabled_ver?>>
									<?=loadSelect2("pv_fuentefinanciamiento","CodFuente","Denominacion",$f['CodFuente'],10)?>
								</select>
							</td>
							<td align="center">
								<input type="hidden" name="concepto_cod_partida[]" value="<?=$f['cod_partida']?>" />
								<input type="hidden" name="concepto_CodCuenta[]" value="<?=$f['CodCuenta']?>" />
								<input type="hidden" name="concepto_CodCuentaPub20[]" value="<?=$f['CodCuentaPub20']?>" />
								<?=$f['cod_partida']?>
							</td>
							<td align="center">
								<input type="hidden" name="concepto_Categoria[]" value="<?=$f['Categoria']?>" />
								<?=htmlentities($f['NomCategoria'])?>
							</td>
							<td><input type="text" name="concepto_Monto[]" value="<?=number_format($f['Monto'],2,',','.')?>" class="cell currency" style="text-align:right;" onchange="setTotal();" <?=$disabled_ver?>></td>
						</tr>
						<?php
						$Monto += $f['Monto'];
					}
					?>
				</tbody>
			</table>
		</div>
		<input type="hidden" id="nro_concepto" value="<?=$nro_concepto?>" />
		<input type="hidden" id="can_concepto" value="<?=$nro_concepto?>" />

		<table style="width:<?=$_width?>px;" class="tblBotones">
		    <tbody>
			    <tr>
			        <td align="right"><strong>Total General: </strong></td>
			        <td width="100"><input type="text" name="Monto" id="Monto" value="<?=number_format($Monto,2,',','.')?>" style="width:125px; font-weight:bold; text-align:right;" readonly></td>
			    </tr>
		    </tbody>
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
			<table class="tblLista" style="width:100%;">
				<thead>
					<tr>
						<th width="40">Cat. Prog</th>
						<th width="20">F.F.</th>
						<th width="80">Partida</th>
						<th align="left">Denominaci&oacute;n</th>
						<th width="100">Monto</th>
					</tr>
				</thead>
				
				<tbody id="lista_partida">
					<?php
					$MontoTotal = 0;
					$sql = "SELECT
								cd.CodOrganismo,
								cd.CodPresupuesto,
								cd.CodFuente,
								cd.cod_partida,
								SUM(cd.Monto) AS Monto,
								p.denominacion,
								ppto.Ejercicio,
								ppto.CategoriaProg,
								CONCAT(ss.CodSector, pr.CodPrograma, a.CodActividad) AS CatProg
							FROM
								ap_certificacionesdet cd
								INNER JOIN pv_partida p ON (p.cod_partida = cd.cod_partida)
								INNER JOIN pv_presupuesto ppto ON (ppto.CodOrganismo = cd.CodOrganismo AND ppto.CodPresupuesto = cd.CodPresupuesto)
								INNER JOIN pv_categoriaprog cp ON (cp.CategoriaProg = ppto.CategoriaProg)
								INNER JOIN pv_actividades a ON (a.IdActividad = cp.IdActividad)
								INNER JOIN pv_proyectos py ON (py.IdProyecto = a.IdProyecto)
								INNER JOIN pv_subprogramas spr ON (spr.IdSubPrograma = py.IdSubPrograma)
								INNER JOIN pv_programas pr ON (pr.IdPrograma = spr.IdPrograma)
								INNER JOIN pv_subsector ss ON (ss.IdSubSector = pr.IdSubSector)
							WHERE cd.CodCertificacion = '$field[CodCertificacion]'
							GROUP BY CodOrganismo, CodPresupuesto, CodFuente, cod_partida";
					$field_partida = getRecords($sql);
					foreach ($field_partida as $f) {
						$MontoTotal += $f['Monto'];
						##	
						list($_MontoAjustado, $_MontoCompromiso, $_PreCompromiso, $_CotizacionesAsignadas) = disponibilidadPartida2($f['Ejercicio'], $f['CodOrganismo'], $f['cod_partida'], $f['CodPresupuesto'], $f['CodFuente']);
						$_MontoPendiente = $_PreCompromiso + $_CotizacionesAsignadas;
						$_MontoDisponible = $_MontoAjustado - $_MontoCompromiso;
						$_MontoDisponibleReal = $_MontoAjustado - ($_MontoCompromiso + $_MontoPendiente);
						##	
						if (($_MontoDisponible - $Monto) <= 0) $style = "style='background-color:#F8637D;'";
						elseif(($_MontoDisponibleReal - $Monto) <= 0) $style = "style='background-color:#FFC;'";
						else $style = "style='background-color:#D0FDD2;'";
						?>
						<tr class="trListaBody" <?=$style?>>
							<td align="center">
								<?=$f['CatProg']?>
								<input type="hidden" name="partida_CodPresupuesto[]" value="<?=$f['CodPresupuesto']?>">
								<input type="hidden" name="partida_CodFuente[]" value="<?=$f['CodFuente']?>">
								<input type="hidden" name="partida_cod_partida[]" value="<?=$f['cod_partida']?>">
								<input type="hidden" name="partida_Monto[]" value="<?=$f['Monto']?>">
							</td>
							<td align="center"><?=$f['CodFuente']?></td>
							<td align="center"><?=$f['cod_partida']?></td>
							<td><?=htmlentities($f['denominacion'])?></td>
							<td align="right"><?=number_format($f['Monto'],2,',','.')?></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
		<input type="hidden" id="nro_partida" value="<?=$nro_partida?>" />
		<input type="hidden" id="can_partida" value="<?=$nro_partida?>" />
	</div>

</form>
<div style="width:<?=$_width?>px" class="divMsj">Campos Obligatorios *</div>

<script type="text/javascript">
	function concepto_insertar() {
		if ($('#CodPresupuesto').val() == "") cajaModal('Debe seleccionar el Presupuesto','error');
		else {
			$('#a_concepto').attr('href','../lib/listas/gehen.php?anz=lista_ap_conceptoscertificacion&filtrar=default&CodPresupuesto='+$('#CodPresupuesto').val()+'&Ejercicio='+$('#Ejercicio').val()+'&CategoriaProg='+$('#CategoriaProg').val()+'&CodFuente='+$('#CodFuente').val()+'&Monto='+$('#Monto').val()+'&ventana=ap_certificaciones&detalle=concepto&modulo=ajax&accion=concepto_insertar&url=../../ap/ap_certificaciones_ajax.php&iframe=true&width=100%&height=100%');
			$('#a_concepto').click();
		}
	}
	function partida_insertar() {
		if ($('#CodPresupuesto').val() == "") cajaModal('Debe seleccionar el Presupuesto','error');
		else {
			$('#a_concepto').attr('href','../lib/listas/gehen.php?anz=lista_partidas&filtrar=default&CodPresupuesto='+$('#CodPresupuesto').val()+'&Ejercicio='+$('#Ejercicio').val()+'&CategoriaProg='+$('#CategoriaProg').val()+'&CodFuente='+$('#CodFuente').val()+'&Monto='+$('#Monto').val()+'&ventana=ap_certificaciones&detalle=concepto&modulo=ajax&accion=partida_insertar&url=../../ap/ap_certificaciones_ajax.php&FlagTipoCuenta=S&fcod_tipocuenta=4&iframe=true&width=100%&height=100%');
			$('#a_concepto').click();
		}
	}
	function setTotal() {
		//	TOTAL GENERAL
		var Monto = 0;
		$('input[name="concepto_Monto[]"]').each(function(idx) {
			var concepto_Monto = setNumero($(this).val());
			Monto += concepto_Monto;
		});
		$('#Monto').val(Monto).formatCurrency();
	}
	function resetearOrganismo() {
		$('#btPresupuesto').attr('href','../lib/listas/gehen.php?anz=lista_pv_presupuesto&filtrar=default&campo1=Ejercicio&campo2=CodPresupuesto&campo3=CategoriaProg&ventana=categorias&FlagOrganismo=S&fCodOrganismo='+$('#CodOrganismo').val()+'&iframe=true&width=100%&height=100%');
		$('#lista_concepto').html('');
		$('#CodPresupuesto, .CodPresupuesto').val('');
		$('#Ejercicio, .Ejercicio').val('');
		$('#CategoriaProg, .CategoriaProg').val('');
		setTotal();
	}
	function getDistribucion() {
		$.ajax({
			type: "POST",
			url: "ap_certificaciones_ajax.php",
			data: "modulo=ajax&accion=getDistribucion&"+$('form').serialize(),
			async: false,
			success: function(resp) {
				$('#lista_partida').html(resp);
			}
		});
	}
</script>