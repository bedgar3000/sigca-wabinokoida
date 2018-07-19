<?php
if ($opcion == "ap_bancotransaccion") {
	list($NroTransaccion, $Secuencia) = explode('_', $sel_registros);
	##	consulto datos generales
	$sql = "SELECT
				bt.NroTransaccion,
				bt.CodOrganismo,
				bt.Comentarios AS ComentariosVoucher,
				bt.TipoTransaccion,
				bt.FechaTransaccion AS FechaVoucher,
				bt.PeriodoContable AS Periodo,
				btt.CodVoucher,
				'' AS CodLibroCont
			FROM
				ap_bancotransaccion bt
				INNER JOIN ap_bancotipotransaccion btt ON btt.CodTipoTransaccion = bt.CodTipoTransaccion
			WHERE bt.NroTransaccion = '$NroTransaccion'
			GROUP BY NroTransaccion";
	$field = getRecord($sql);
	$field['PreparadoPor'] = $_SESSION['CODPERSONA_ACTUAL'];
	$field['NomPreparadoPor'] = $_SESSION['NOMBRE_USUARIO_ACTUAL'];
	$field['FechaPreparacion'] = $FechaActual;
	$field['AprobadoPor'] = $_SESSION['CODPERSONA_ACTUAL'];
	$field['NomAprobadoPor'] = $_SESSION['NOMBRE_USUARIO_ACTUAL'];
	$field['FechaAprobacion'] = $FechaActual;
	if ($_PARAMETRO['CONTPUB20'] == 'S')
	{
		$field['CodContabilidad'] = 'F';
		##	detalle
		if ($field['TipoTransaccion'] == "I") {
			$sql = "(SELECT
						pc.CodCuenta,
						pc.Descripcion,
                        pc.FlagReqCC,
						bt.Monto AS MontoVoucher,
						bt.CodProveedor AS CodPersona,
						bt.CodTipoDocumento AS ReferenciaTipoDocumento,
						bt.CodigoReferenciaBanco AS ReferenciaNroDocumento,
						bt.FechaTransaccion AS FechaVoucher,
						'Debe' AS Columna
					 FROM
						ap_bancotransaccion bt
						INNER JOIN ap_ctabancaria cb ON (bt.NroCuenta = cb.NroCuenta)
						INNER JOIN ac_mastplancuenta20 pc ON (cb.CodCuentaPub20 = pc.CodCuenta)
					 WHERE bt.NroTransaccion = '$NroTransaccion')
					UNION
					(SELECT
						pc.CodCuenta,
						pc.Descripcion,
                        pc.FlagReqCC,
						bt.Monto AS MontoVoucher,
						bt.CodProveedor AS CodPersona,
						bt.CodTipoDocumento AS ReferenciaTipoDocumento,
						bt.CodigoReferenciaBanco AS ReferenciaNroDocumento,
						bt.FechaTransaccion AS FechaVoucher,
						'Haber' AS Columna
					 FROM
						ap_bancotransaccion bt
						INNER JOIN ap_bancotipotransaccion btt ON (bt.CodTipoTransaccion = btt.CodTipoTransaccion)
						INNER JOIN ac_mastplancuenta20 pc ON (btt.CodCuentaPub20 = pc.CodCuenta)
					 WHERE bt.NroTransaccion = '$NroTransaccion')";
		}
		elseif ($field['TipoTransaccion'] == "E") {
			$sql = "(SELECT
						pc.CodCuenta,
						pc.Descripcion,
                        pc.FlagReqCC,
						bt.Monto AS MontoVoucher,
						bt.CodProveedor AS CodPersona,
						bt.CodTipoDocumento AS ReferenciaTipoDocumento,
						bt.CodigoReferenciaBanco AS ReferenciaNroDocumento,
						bt.FechaTransaccion AS FechaVoucher,
						'Haber' AS Columna
					 FROM
						ap_bancotransaccion bt
						INNER JOIN ap_ctabancaria cb ON (bt.NroCuenta = cb.NroCuenta)
						INNER JOIN ac_mastplancuenta20 pc ON (cb.CodCuentaPub20 = pc.CodCuenta)
					 WHERE bt.NroTransaccion = '$NroTransaccion')
					UNION
					(SELECT
						pc.CodCuenta,
						pc.Descripcion,
                        pc.FlagReqCC,
						bt.Monto AS MontoVoucher,
						bt.CodProveedor AS CodPersona,
						bt.CodTipoDocumento AS ReferenciaTipoDocumento,
						bt.CodigoReferenciaBanco AS ReferenciaNroDocumento,
						bt.FechaTransaccion AS FechaVoucher,
						'Debe' AS Columna
					 FROM
						ap_bancotransaccion bt
						INNER JOIN ap_bancotipotransaccion btt ON (bt.CodTipoTransaccion = btt.CodTipoTransaccion)
						INNER JOIN ac_mastplancuenta20 pc ON (btt.CodCuentaPub20 = pc.CodCuenta)
					 WHERE bt.NroTransaccion = '$NroTransaccion')";
		}
		elseif ($field['TipoTransaccion'] == "T") {
			$sql = "(SELECT
						pc.CodCuenta,
						pc.Descripcion,
                        pc.FlagReqCC,
						bt.Monto AS MontoVoucher,
						bt.CodProveedor AS CodPersona,
						bt.CodTipoDocumento AS ReferenciaTipoDocumento,
						bt.CodigoReferenciaBanco AS ReferenciaNroDocumento,
						bt.FechaTransaccion AS FechaVoucher,
						'Haber' AS Columna
					 FROM
						ap_bancotransaccion bt
						INNER JOIN ap_ctabancaria cb ON (bt.NroCuenta = cb.NroCuenta)
						INNER JOIN ac_mastplancuenta20 pc ON (cb.CodCuentaPub20 = pc.CodCuenta)
					 WHERE
					 	bt.NroTransaccion = '$NroTransaccion' AND
						bt.Monto < 0)
					UNION
					(SELECT
						pc.CodCuenta,
						pc.Descripcion,
                        pc.FlagReqCC,
						bt.Monto AS MontoVoucher,
						bt.CodProveedor AS CodPersona,
						bt.CodTipoDocumento AS ReferenciaTipoDocumento,
						bt.CodigoReferenciaBanco AS ReferenciaNroDocumento,
						bt.FechaTransaccion AS FechaVoucher,
						'Debe' AS Columna
					 FROM
						ap_bancotransaccion bt
						INNER JOIN ap_ctabancaria cb ON (bt.NroCuenta = cb.NroCuenta)
						INNER JOIN ac_mastplancuenta20 pc ON (cb.CodCuentaPub20 = pc.CodCuenta)
					 WHERE
					 	bt.NroTransaccion = '$NroTransaccion' AND
						bt.Monto >= 0)";
		}
		$detalle = getRecords($sql);
		$field_detalle = [];
		foreach ($detalle as $d) 
		{
			$CodCentroCosto = ($d['FlagReqCC']=='S')?$_PARAMETRO['CCOSTOVOUCHER']:'';
			##	
			$field_detalle[] = [
				'CodCuenta' => $d['CodCuenta'],
				'Descripcion' => $d['Descripcion'],
				'MontoVoucher' => $d['MontoVoucher'],
				'CodPersona' => $d['CodPersona'],
				'ReferenciaTipoDocumento' => $d['ReferenciaTipoDocumento'],
				'ReferenciaNroDocumento' => $d['ReferenciaNroDocumento'],
				'CodCentroCosto' => $CodCentroCosto,
				'NomCentroCosto' => getVar3("SELECT Codigo FROM ac_mastcentrocosto WHERE CodCentroCosto = '$CodCentroCosto'"),
				'FechaVoucher' => $d['FechaVoucher'],
				'Columna' => $d['Columna'],
			];
		}
	}
	else
	{
		$field['CodContabilidad'] = 'T';
	}
	##	
	$accion = "ap_bancotransaccion";
}
##	Sistema Fuente
$CodSistemaFuente = getVar3("SELECT CodSistemaFuente FROM mastaplicaciones WHERE CodAplicacion = 'AP'");
##	Periodo Contable
$sql = "SELECT Estado
		FROM ac_controlcierremensual
		WHERE
			TipoRegistro = 'AB' AND
			CodOrganismo = '$field[CodOrganismo]' AND
			Periodo = '$field[Periodo]'";
$EstadoPeriodo = getVar3($sql);
##	
$CodDependencia = $_SESSION['DEPENDENCIA_ACTUAL'];
//	------------------------------------
$_width = 1000;
?>
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=ap_voucher" method="POST" enctype="multipart/form-data" data-ventana="modal" onsubmit="return formSubmit('ap_voucher_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="CodSistemaFuente" id="CodSistemaFuente" value="<?=$CodSistemaFuente?>" />
	<input type="hidden" name="EstadoPeriodo" id="EstadoPeriodo" value="<?=$EstadoPeriodo?>" />
	<input type="hidden" name="CodDependencia" id="CodDependencia" value="<?=$CodDependencia?>" />
	<input type="hidden" name="NroTransaccion" id="NroTransaccion" value="<?=$NroTransaccion?>" />

	<table style="padding:0px; margin:0px; width:<?=$_width?>px; margin:auto;">
		<tr>
			<td width="50%">
				<div style="overflow:scroll; height:100px; width:<?=$_width/2?>px; margin:auto;">
					<table class="tblLista" style="width:100%; min-width:600px;">
						<thead>
							<tr>
								<th width="75">Periodo</th>
								<th width="100">Voucher</th>
								<th width="75">Fecha</th>
								<th width="75">Status</th>
								<th>Organismo</th>
							</tr>
						</thead>
					</table>
				</div>
			</td>
			<td width="50%">
				<div style="overflow:scroll; height:100px; width:<?=$_width/2?>px;; margin:auto;">
					<table class="tblLista" style="width:100%; min-width:700px;">
						<thead>
							<tr>
								<th width="50">Linea</th>
								<th style="min-width:200px;">Errores Encontrados</th>
								<th width="75">Periodo</th>
								<th width="100">Voucher</th>
								<th>Organismo</th>
							</tr>
						</thead>
						<tbody id="lista_errores"></tbody>
					</table>
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<table width="100%" class="tblForm">
				    <tr>
						<td class="tagForm">* Organismo:</td>
						<td>
							<select name="CodOrganismo" id="CodOrganismo" style="width:290px;">
								<?=getOrganismos($field['CodOrganismo'], 1);?>
							</select>
						</td>
						<td class="tagForm">* Descripci&oacute;n:</td>
						<td>
							<input type="text" name="ComentariosVoucher" id="ComentariosVoucher" value="<?=$field['ComentariosVoucher']?>" style="width:290px;">
						</td>
					</tr>
				    <tr>
						<td class="tagForm">Fecha:</td>
						<td>
							<input type="text" name="FechaVoucher" id="FechaVoucher" value="<?=formatFechaDMA($field['FechaVoucher'])?>" style="width:106px;" readonly>
						</td>
						<td class="tagForm">Preparado Por:</td>
						<td>
							<input type="hidden" name="PreparadoPor" id="PreparadoPor" value="<?=$field['PreparadoPor']?>" />
							<input type="text" name="NomPreparadoPor" id="NomPreparadoPor" value="<?=$field['NomPreparadoPor']?>" style="width:220px;" disabled />
							<input type="text" name="FechaPreparacion" id="FechaPreparacion" value="<?=formatFechaDMA($field['FechaPreparacion'])?>" style="width:65px;" maxlength="10" readonly />
						</td>
					</tr>
				    <tr>
						<td class="tagForm">Periodo:</td>
						<td>
							<input type="text" name="Periodo" id="Periodo" value="<?=$field['Periodo']?>" style="width:57px;" readonly>
							<select name="CodVoucher" id="CodVoucher" style="width:45px;">
								<?=loadSelect2('ac_voucher','CodVoucher','CodVoucher',$field['CodVoucher'],1)?>
							</select>
							<input type="text" name="NroVoucher" id="NroVoucher" value="<?=$field['NroVoucher']?>" style="width:90px;" readonly>
						</td>
						<td class="tagForm">Aprobado Por:</td>
						<td>
							<input type="hidden" name="AprobadoPor" id="AprobadoPor" value="<?=$field['AprobadoPor']?>" />
							<input type="text" name="NomAprobadoPor" id="NomAprobadoPor" value="<?=$field['NomAprobadoPor']?>" style="width:220px;" disabled />
							<input type="text" name="FechaAprobacion" id="FechaAprobacion" value="<?=formatFechaDMA($field['FechaAprobacion'])?>" style="width:65px;" maxlength="10" readonly />
						</td>
					</tr>
				    <tr>
						<td class="tagForm">Libro Contable:</td>
						<td>
							<select name="CodLibroCont" id="CodLibroCont" style="width:200px;">
								<?=loadSelect2('ac_librocontable','CodLibroCont','Descripcion',$field['CodLibroCont'])?>
							</select>
						</td>
						<td class="tagForm">Contabilidad:</td>
						<td>
							<select name="CodContabilidad" id="CodContabilidad" style="width:200px;">
								<?=loadSelect2('ac_contabilidades','CodContabilidad','Descripcion',$field['CodContabilidad'],1)?>
							</select>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<table width="100%" class="tblBotones">
				    <tbody>
					    <tr>
					        <td align="right">
					            <input type="submit" style="width:85px;" value="Aceptar" id="btAceptar" disabled="disabled" />
					            <input type="button" style="width:85px;" value="Cancelar" onclick="parent.$.prettyPhoto.close();" />
					        </td>
					    </tr>
				    </tbody>
				</table>
				<div style="overflow:scroll; height:200px; width:<?=$_width?>px; margin:auto;">
					<table class="tblLista" style="width:100%; min-width:1000px;">
						<thead>
							<tr>
			                    <th width="30">#</th>
			                    <th width="110">Cuenta</th>
			                    <th>Descripci&oacute;n</th>
			                    <th width="125">Monto</th>
			                    <th width="60">Persona</th>
			                    <th colspan="2">Documento</th>
			                    <th width="50">C.C</th>
			                    <th width="75">Fecha</th>
							</tr>
						</thead>
						<tbody id="lista_detalle">
						<?php
						$Debitos = 0;
						$Creditos = 0;
						foreach ($field_detalle as $f) 
						{
							++$Linea;
							if ($f['Columna'] == 'Haber')
							{
								$style = "color:red;";
								$MontoVoucher = abs($f['MontoVoucher']) * -1;
								$Debitos += $MontoVoucher;
							}
							else
							{
								$style = "color:#000;";
								$MontoVoucher = abs($f['MontoVoucher']);
								$Creditos += $MontoVoucher;
							}
							?>
							<tr class="trListaBody">
								<th><?=$Linea?></th>
								<td><input type="text" name="detalle_CodCuenta[]" value="<?=$f['CodCuenta']?>" class="cell2" readonly /></td>
								<td><input type="text" name="detalle_Descripcion[]" value="<?=$f['Descripcion']?>" class="cell2" readonly /></td>
								<td><input type="text" name="detalle_MontoVoucher[]" value="<?=number_format($MontoVoucher,2,',','.')?>" class="cell2" style="text-align:right; <?=$style?>" readonly /></td>
								<td><input type="text" name="detalle_CodPersona[]" value="<?=$f['CodPersona']?>" class="cell2" style="text-align:center;" readonly /></td>
								<td width="25"><input type="text" name="detalle_ReferenciaTipoDocumento[]" value="<?=$f['ReferenciaTipoDocumento']?>" class="cell2" style="text-align:center;" readonly /></td>
								<td width="125"><input type="text" name="detalle_ReferenciaNroDocumento[]" value="<?=$f['ReferenciaNroDocumento']?>" class="cell2" readonly /></td>
								<td>
									<input type="hidden" name="detalle_CodCentroCosto[]" value="<?=$f['CodCentroCosto']?>" />
									<input type="text" value="<?=$f['NomCentroCosto']?>" class="cell2" style="text-align:center;" readonly />
								</td>
								<td><input type="text" name="detalle_FechaVoucher[]" value="<?=formatFechaDMA($f['FechaVoucher'])?>" class="cell2" style="text-align:center;" readonly /></td>
							</tr>
							<?php
						}
						?>
						</tbody>
					</table>
				</div>
				<table width="100%" class="tblBotones">
				    <tbody>
					    <tr>
					    	<td width="75">Nro. Lineas: </td>
					        <td width="100"><input type="text" name="Lineas" id="Lineas" value="<?=$Linea?>" class="cell2" style="font-weight:bold;" readonly /></td>
					        <td width="100"></td>
					        <td width="75">Cr&eacute;ditos: </td>
					        <td width="150"><input type="text" name="Creditos" id="Creditos" value="<?=number_format($Creditos+0.001,2,',','.')?>" class="cell2" style="font-weight:bold;" readonly /></td>
					        <td width="75">D&eacute;bitos: </td>
					        <td width="150"><input type="text" name="Debitos" id="Debitos" value="<?=number_format($Debitos,2,',','.')?>" class="cell2" style="font-weight:bold; color:red;" readonly /></td>
					        <td></td>
					    </tr>
				    </tbody>
				</table>
			</td>
		</tr>
	</table>
</form>

<script type="text/javascript" language="javascript">
	$(document).ready(function() {
		validarErrores();
	});
	function validarErrores() {
		var Creditos = new Number(setNumero($("#Creditos").val()));
		var Debitos = new Number(setNumero($("#Debitos").val()));
		var Periodo = $("#Periodo").val();
		var PeriodoEstado = $("#PeriodoEstado").val();
		var i = 0;
		
		//	valido diferencias de saldos
		if ((Creditos + Debitos) != 0) {
			i++;
			$("#lista_errores").append("<tr class=trListaBody'>");
			$("#lista_errores").append("<th align='center'>"+i+"</th>");
			$("#lista_errores").append("<td style='color:red;'>Monto de créditos y débitos deben ser igual</td>");
			$("#lista_errores").append("<td></td>");
			$("#lista_errores").append("<td></td>");
			$("#lista_errores").append("<td></td>");
			$("#lista_errores").append("</tr>");
			$("#btAceptar").attr("disabled", "disabled");
		}
		//	valido diferencias de saldos
		else if (Creditos == 0 || Debitos == 0) {
			i++;
			$("#lista_errores").append("<tr class=trListaBody'>");
			$("#lista_errores").append("<th align='center'>"+i+"</th>");
			$("#lista_errores").append("<td style='color:red;'>Monto de créditos y débitos no puede ser cero</td>");
			$("#lista_errores").append("<td></td>");
			$("#lista_errores").append("<td></td>");
			$("#lista_errores").append("<td></td>");
			$("#lista_errores").append("</tr>");
			$("#btAceptar").attr("disabled", "disabled");
		}
		//	valido periodo
		else if (PeriodoEstado == "") {
			i++;
			$("#lista_errores").append("<tr class=trListaBody'>");
			$("#lista_errores").append("<th align='center'>"+i+"</th>");
			$("#lista_errores").append("<td style='color:red;'>El Periodo "+Periodo+" no se ha creado</td>");
			$("#lista_errores").append("<td></td>");
			$("#lista_errores").append("<td></td>");
			$("#lista_errores").append("<td></td>");
			$("#lista_errores").append("</tr>");
			$("#btAceptar").attr("disabled", "disabled");
		}
		//	valido periodo
		else if (PeriodoEstado == "C") {
			i++;
			$("#lista_errores").append("<tr class=trListaBody'>");
			$("#lista_errores").append("<th align='center'>"+i+"</th>");
			$("#lista_errores").append("<td style='color:red;'>El Periodo "+Periodo+" esta cerrado</td>");
			$("#lista_errores").append("<td></td>");
			$("#lista_errores").append("<td></td>");
			$("#lista_errores").append("<td></td>");
			$("#lista_errores").append("</tr>");
		}
		else $("#btAceptar").prop("disabled", false);
	}
</script>