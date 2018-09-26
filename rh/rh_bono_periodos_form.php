<?php
if ($opcion == "nuevo") {
	$field_requerimiento['CodOrganismo'] = $_SESSION["ORGANISMO_ACTUAL"];
	$field['Estado'] = "A";
	$field['Periodo'] = "$AnioActual-$MesActual";
	$field['FechaInicio'] = "$AnioActual-$MesActual-01";
	$field['FechaFin'] = "$AnioActual-$MesActual-" . getDiasMes("$AnioActual-$MesActual");
	$field['TotalDiasPeriodo'] = 30;
	$field['TotalFeriados'] = getDiasFeriados(formatFechaDMA($field['FechaInicio']), formatFechaDMA($field['FechaFin']));
	$field['TotalDiasPago'] = $field['TotalDiasPeriodo'];
	$DiasHabiles = getDiasHabiles(formatFechaDMA($field['FechaInicio']), formatFechaDMA($field['FechaFin']));
	$DiasInactivos = $field['TotalDiasPeriodo'] - $DiasHabiles;
	$UT = getVar3("SELECT Valor FROM mastunidadtributaria WHERE Anio = '$_PARAMETRO[UTANIO]'");
	$field['ValorDia'] = $UT * $_PARAMETRO['UTPORC'] / 100;
	$field['CodTipoNom'] = $_SESSION["NOMINA_ACTUAL"];
	$field['HorasDiaria'] = $_PARAMETRO['HORADIR'];
	$field['HorasSemanal'] = $_PARAMETRO['HORADIR'] * $_PARAMETRO['HORDIAS'];
	$field['ValorSemanal'] = $field['ValorDia'] * 7;
	$field['ValorMes'] = $field['ValorDia'] * $field['TotalDiasPago'];
	##
	$titulo = "Nuevo Registro";
	$accion = "nuevo";
	$label_submit = "Guardar";
	$disabled_nuevo = "disabled";
	$clkCancelar = "document.getElementById('frmentrada').submit();";
}
elseif ($opcion == "modificar" || $opcion == "ver" || $opcion == "cerrar") {
	list($Anio, $CodOrganismo, $CodBonoAlim) = split("[_]", $registro);
	//	consulto datos generales
	$sql = "SELECT
				ba.*,
				ppto.CategoriaProg,
				ppto.Ejercicio
			FROM
				rh_bonoalimentacion ba
				LEFT JOIN pv_presupuesto ppto ON (ba.CodOrganismo = ppto.CodOrganismo and ba.CodPresupuesto = ppto.CodPresupuesto)
			WHERE
				ba.Anio = '$Anio' AND
				ba.CodOrganismo = '$CodOrganismo' AND
				ba.CodBonoAlim = '$CodBonoAlim'";
	$field = getRecord($sql);
	
	if ($opcion == "modificar") {
		$titulo = "Modificar Registro";
		$accion = "modificar";
		$disabled_modificar = "disabled";
		$label_submit = "Modificar";
		$clkCancelar = "document.getElementById('frmentrada').submit();";
	}
	
	elseif ($opcion == "ver") {
		$titulo = "Ver Registro";
		$disabled_nuevo = "disabled";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$disabled_empleados = "disabled";
		$display_submit = "display:none;";
		$clkCancelar = "document.getElementById('frmentrada').submit();";
	}
	
	elseif ($opcion == "cerrar") {
		$titulo = "Cerrar Registro";
		$accion = "cerrar";
		$disabled_nuevo = "disabled";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$disabled_empleados = "disabled";
		$label_submit = "Cerrar";
		$clkCancelar = "document.getElementById('frmentrada').submit();";
	}
}
$UT = getVar3("SELECT Valor FROM mastunidadtributaria WHERE Anio = '$_PARAMETRO[UTANIO]'");
$ValorDia = $UT * $_PARAMETRO['UTPORC'] / 100;
$ValorSemanal = $ValorDia * 7;
$ValorMes = $ValorDia * $field['TotalDiasPago'];
if ($ValorDia != $field['ValorDia']) 
{
	$FlagModificar = '';
	$field['ValorDia'] = $ValorDia;
	$field['ValorSemanal'] = $ValorSemanal;
	$field['ValorMes'] = $ValorMes;
}
else $FlagModificar = 'none';
//	------------------------------------
$_width = 800;
?>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="titulo"><?=$titulo?></td>
		<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
	</tr>
</table><hr width="100%" color="#333333" /><br />

<table width="<?=$_width?>" align="center" cellpadding="0" cellspacing="0">
    <tr>
        <td>
            <div class="header">
            <ul id="tab">
            <!-- CSS Tabs -->
            <li id="li1" onclick="currentTab('tab', this);" class="current"><a href="#" onclick="mostrarTab('tab', 1, 2);">Informaci&oacute;n General</a></li>
            <li id="li2" onclick="currentTab('tab', this);"><a href="#" onclick="mostrarTab('tab', 2, 2);">Empleados</a></li>
            </ul>
            </div>
        </td>
    </tr>
</table>

<div id="tab1" style="display:block;">
<form name="frmentrada" id="frmentrada" action="gehen.php?anz=rh_bono_periodos_lista" method="POST" enctype="multipart/form-data" onsubmit="return bono_periodos(this, '<?=$accion?>');" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" id="Anio" value="<?=$field['Anio']?>" />

<center>
	<div class="ui-widget" style="display: <?=$FlagModificar?>;">
	    <div class="ui-state-highlight ui-corner-all" style="width:<?=$_width?>px; text-align:left;">
	        <p>
	        <span class="ui-icon ui-icon-alert" style="float: left;"></span>
	        <strong>Los Valores de la Unidad Tributaria han cambiado debe Hacer Click en Modificar para actualizar los Montos</strong>
	        </p>
	    </div>
	</div>
</center>

<table width="<?=$_width?>" class="tblForm">
	<tr>
    	<td colspan="4" class="divFormCaption">Informaci&oacute;n del Periodo</td>
    </tr>
	<tr>
		<td class="tagForm" width="125">* Organismo:</td>
		<td>
			<select name="CodOrganismo" id="CodOrganismo" style="width:300px;" <?=$disabled_modificar?> onchange="setHrefPartida();">
				<?=getOrganismos($field['CodOrganismo'], 3)?>
			</select>
		</td>
		<td class="tagForm" width="125">N&uacute;mero:</td>
		<td>
            <input type="text" name="CodBonoAlim" id="CodBonoAlim" style="width:60px;" class="codigo" value="<?=$field['CodBonoAlim']?>" readonly />
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Descripci&oacute;n:</td>
		<td>
            <input type="text" name="Descripcion" id="Descripcion" style="width:300px;" maxlength="100" value="<?=htmlentities($field['Descripcion'])?>" <?=$disabled_ver?> />
		</td>
		<td class="tagForm">Estado:</td>
		<td>
        	<input type="hidden" name="Estado" id="Estado" value="<?=$field['Estado']?>" />
            <input type="text" style="width:60px;" class="codigo" value="<?=strtoupper(printValores("ESTADO-BONO", $field['Estado']))?>" disabled />
		</td>
    </tr>
	<tr>
		<td class="tagForm">* N&oacute;mina</td>
		<td>
			<select name="CodTipoNom" id="CodTipoNom" style="width:175px;" onchange="$('#lista_empleados').html('');" <?=$disabled_modificar?>>
            	<option value="">&nbsp;</option>
				<?=loadSelect("tiponomina", "CodTipoNom", "Nomina", $field['CodTipoNom'], 0)?>
			</select>
		</td>
		<td class="tagForm">* Periodo:</td>
		<td>
            <input type="text" name="Periodo" id="Periodo" style="width:60px;" maxlength="7" value="<?=$field['Periodo']?>" <?=$disabled_ver?> />
		</td>
    </tr>
	<tr>
		<td class="tagForm">* Inicio</td>
		<td>
            <input type="text" name="FechaInicio" id="FechaInicio" style="width:60px;" class="datepicker" maxlength="10" value="<?=formatFechaDMA($field['FechaInicio'])?>" onchange="getDiasBonoPeriodo();" <?=$disabled_ver?> />
		</td>
		<td class="tagForm">* Fin:</td>
		<td>
            <input type="text" name="FechaFin" id="FechaFin" style="width:60px;" class="datepicker" maxlength="10" value="<?=formatFechaDMA($field['FechaFin'])?>" onchange="getDiasBonoPeriodo();" <?=$disabled_ver?> />
		</td>
    </tr>
	<tr>
    	<td colspan="4" class="divFormCaption">Dias del Periodo</td>
    </tr>
	<tr>
		<td class="tagForm">Dias del Periodo:</td>
		<td>
            <input type="text" name="TotalDiasPeriodo" id="TotalDiasPeriodo" style="width:60px; text-align:right;" value="<?=number_format($field['TotalDiasPeriodo'], 0, '', '.')?>" readonly />
        </td>
		<td class="tagForm">Horas Diaria:</td>
		<td>
            <input type="text" name="HorasDiaria" id="HorasDiaria" style="width:60px; text-align:right;" value="<?=number_format($field['HorasDiaria'], 2, ',', '.')?>" readonly />
		</td>
    </tr>
	<tr>
		<td class="tagForm">Dias de Pago:</td>
		<td>
            <input type="text" name="TotalDiasPago" id="TotalDiasPago" style="width:60px; text-align:right;" value="<?=number_format($field['TotalDiasPago'], 0, '', '.')?>" readonly />
		</td>
		<td class="tagForm">Horas Semanales:</td>
		<td>
            <input type="text" name="HorasSemanal" id="HorasSemanal" style="width:60px; text-align:right;" value="<?=number_format($field['HorasSemanal'], 2, ',', '.')?>" readonly />
		</td>
    </tr>
	<tr>
		<td class="tagForm">Dias Feriados:</td>
		<td>
            <input type="text" name="TotalFeriados" id="TotalFeriados" style="width:60px; text-align:right;" value="<?=number_format($field['TotalFeriados'], 0, '', '.')?>" readonly />
		</td>
		<td class="tagForm">Valor Semanal:</td>
		<td>
            <input type="text" name="ValorSemanal" id="ValorSemanal" style="width:60px; text-align:right;" value="<?=number_format($field['ValorSemanal'], 2, ',', '.')?>" readonly />
		</td>
    </tr>
	<tr>
		<td class="tagForm">Valor Diario:</td>
		<td>
            <input type="text" name="ValorDia" id="ValorDia" style="width:60px; text-align:right;" value="<?=number_format($field['ValorDia'], 2, ',', '.')?>" readonly />
		</td>
		<td class="tagForm">Valor Mensual:</td>
		<td>
            <input type="text" name="ValorMes" id="ValorMes" style="width:60px; text-align:right;" value="<?=number_format($field['ValorMes'], 2, ',', '.')?>" readonly />
		</td>
    </tr>
    <tr>
		<td class="tagForm">&Uacute;ltima Modif.:</td>
		<td colspan="3">
			<input type="text" size="30" value="<?=$field['UltimoUsuario']?>" disabled="disabled" />
			<input type="text" size="25" value="<?=$field['UltimaFecha']?>" disabled="disabled" />
		</td>
	</tr>
	<tr>
		<td colspan="4" class="divFormCaption">Presupuesto</td>
	</tr>
	<tr>
		<td class="tagForm">Tipo de Documento:</td>
		<td>
			<select name="CodTipoDocumento" id="CodTipoDocumento" style="width:300px;" <?=$disabled_ver?>>
				<option value="">&nbsp;</option>
				<?=loadSelect2("ap_tipodocumento","CodTipoDocumento","Descripcion",$field['CodTipoDocumento'],10)?>
			</select>
		</td>
		<td class="tagForm">Presupuesto:</td>
		<td class="gallery clearfix">
			<input type="text" name="Ejercicio" id="Ejercicio" value="<?=$field['Ejercicio']?>" style="width:48px;" class="Ejercicio" readonly />
			<input type="text" name="CodPresupuesto" id="CodPresupuesto" value="<?=$field['CodPresupuesto']?>" style="width:48px;" class="CodPresupuesto" readonly />
			<a href="../lib/listas/gehen.php?anz=lista_pv_presupuesto&filtrar=default&campo1=Ejercicio&campo2=CodPresupuesto&campo3=CategoriaProg&ventana=rh_bono_periodos&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" style=" <?=$display_ver?>" id="btPresupuesto">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
		</td>
	</tr>
	<tr>
		<td class="tagForm">Partida:</td>
		<td class="gallery clearfix">
			<input type="text" name="cod_partida" id="cod_partida" value="<?=$field['cod_partida']?>" style="width:70px;" readonly />
			<input type="text" name="CodFuente" id="CodFuente" value="<?=$field['CodFuente']?>" style="width:25px;" readonly />
			<a href="../lib/listas/gehen.php?anz=lista_pv_partida_presupuesto&filtrar=default&campo1=cod_partida&campo2=CodFuente&FlagCategoriaProg=S&fCodOrganismo=<?=$field['CodOrganismo']?>&fCodPresupuesto=<?=$field['CodPresupuesto']?>&fEjercicio=<?=$field['Ejercicio']?>&fCategoriaProg=<?=$field['CategoriaProg']?>&ventana=fuente&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe4]" style=" <?=$display_ver?>" id="btPartida">
            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
            </a>
		</td>
		<td class="tagForm">Cat. Prog.:</td>
		<td><input type="text" name="CategoriaProg" id="CategoriaProg" value="<?=$field['CategoriaProg']?>" style="width:100px;" class="CategoriaProg" readonly /></td>
	</tr>
</table>

<center>
<input type="submit" style="display:none;" />
</center>
</form>
</div>

<div id="tab2" style="display:none;">
	<center>
	<form name="frm_empleados" id="frm_empleados">
	<input type="hidden" id="sel_empleados" />
	<table width="<?=$_width?>" class="tblBotones">
	    <tr>
	        <td>
	            <input type="button" value="Importar Empleados" onClick="bono_periodos_empleados_importar();" <?=$disabled_empleados?> />
	        </td>
	        <td align="right" class="gallery clearfix">
	            <a id="a_empleados" href="pagina.php?iframe=true&width=100%&height=500" rel="prettyPhoto[iframe2]" style="display:none;"></a>
	            <input type="button" class="btLista" value="Insertar" onclick="bono_periodos_abrir_lista_empleados();" <?=$disabled_empleados?> />
	            <input type="button" class="btLista" value="Borrar" onclick="quitarLinea(this, 'empleados');" <?=$disabled_empleados?> />
	        </td>
	    </tr>
	</table>
	<div style="overflow:scroll; width:<?=$_width?>px; height:300px;">
		<table width="1500" class="tblLista">
		    <thead>
		    <tr>
		        <th width="15">&nbsp;</th>
		        <th width="30">Empleado</th>
		        <th width="70">Cedula</th>
		        <th width="350" align="left">Nombre Completo</th>
		        <th width="450" align="left">Cargo</th>
		        <th align="left">Dependencia</th>
		    </tr>
		    </thead>
		    
		    <tbody id="lista_empleados">
		    <?php
			$sql = "SELECT
						bad.Anio,
						bad.CodOrganismo,
						bad.CodBonoAlim,
						bad.CodPersona,
						p.NomCompleto,
						p.Ndocumento,
						e.CodEmpleado,
						o.Organismo,
						d.Dependencia,
						pt.DescripCargo
					FROM
						rh_bonoalimentaciondet bad
						INNER JOIN mastpersonas p ON (p.CodPersona = bad.CodPersona)
						INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
						INNER JOIN mastorganismos o ON (o.CodOrganismo = e.CodOrganismo)
						INNER JOIN mastdependencias d ON (d.CodDependencia = e.CodDependencia)
						INNER JOIN rh_puestos pt ON (pt.CodCargo = e.CodCargo)
					WHERE
						bad.Anio = '".$Anio."' AND
						bad.CodOrganismo = '".$CodOrganismo."' AND
						bad.CodBonoAlim = '".$CodBonoAlim."'
					ORDER BY CodEmpleado";
			$query_empleados = mysql_query($sql) or die ($sql.mysql_error());
			while ($field_empleados = mysql_fetch_array($query_empleados)) {	$nro_empleados++;
				?>
				<tr class="trListaBody" onclick="mClk(this, 'sel_empleados');" id="empleados_<?=$field_empleados['CodPersona']?>">
					<th>
						<?=$nro_empleados?>
					</th>
					<td align="center">
						<input type="hidden" name="CodPersona" value="<?=$field_empleados['CodPersona']?>" />
		                <?=$field_empleados['CodEmpleado']?>
					</td>
					<td align="right">
		                <?=$field_empleados['Ndocumento']?>
					</td>
					<td>
		                <?=htmlentities($field_empleados['NomCompleto'])?>
					</td>
					<td>
		                <?=htmlentities($field_empleados['DescripCargo'])?>
					</td>
					<td>
		                <?=htmlentities($field_empleados['Dependencia'])?>
					</td>
				</tr>
				<?php
			}
		    ?>
		    </tbody>
		</table>
	</div>
	<input type="hidden" id="nro_empleados" value="<?=$nro_empleados?>" />
	<input type="hidden" id="can_empleados" value="<?=$nro_empleados?>" />
	</form>
	</center>
</div>

<center>
<input type="button" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" onclick="bono_periodos(document.getElementById('frmentrada'), '<?=$accion?>');" />
<input type="button" value="Cancelar" style="width:75px;" onclick="<?=$clkCancelar?>" />
</center>
<div style="width:700px" class="divMsj">Campos Obligatorios *</div>

<script type="text/javascript">
	//	apertura de periodo (bono de alimentacion)
	function bono_periodos(form, accion) {
		bloqueo(1);
		//	valido
		var error = "";
		if ($("#Descripcion").val().trim() == "" || $("#CodTipoNom").val().trim() == "" || $("#Periodo").val().trim() == "" || $("#FechaInicio").val().trim() == "" || $("#FechaFin").val().trim() == "") error = "Debe llenar los campos obligatorios";
		else if (!valPeriodo($("#Periodo").val())) error = "Formato del periodo incorrecto";
		else if (!valFecha($("#FechaInicio").val()) || !valFecha($("#FechaInicio").val()) || formatFechaAMD($("#FechaInicio").val()) > formatFechaAMD($("#FechaFin").val())) error = "Periodo de Fechas incorrecta";
		//	detalles
		if (error == "") {
			var detalles_empleados = "";
			var frm = document.getElementById("frm_empleados");
			for(var i=0; n=frm.elements[i]; i++) {
				if (n.name == "CodPersona") detalles_empleados += n.value + ";char:tr;";
			}
			var len = detalles_empleados.length; len-=9;
			detalles_empleados = detalles_empleados.substr(0, len);
			if (detalles_empleados == "") error = "Debe seleccionar la lista de empleados por procesar";
		}
		//	valido errores
		if (error != "") {
			cajaModal(error, "error", 400);
		} else {
			//	formulario
			var post = getForm(form);
			//	ajax
			$.ajax({
				type: "POST",
				url: "rh_bono_periodos_ajax.php",
				data: "modulo=formulario&accion="+accion+"&detalles_empleados="+detalles_empleados+"&"+post,
				async: false,
				success: function(resp) {
					if (resp.trim() != "") cajaModal(resp, "error", 400);
					else form.submit();
				}
			});
		}
		return false;
	}
	function setHrefPartida() {
		var href = "../lib/listas/gehen.php?anz=lista_pv_partida_presupuesto&filtrar=default&campo1=cod_partida&campo2=CodFuente&FlagCategoriaProg=S&fCodOrganismo="+$('#CodOrganismo').val()+"&fCodPresupuesto="+$('#CodPresupuesto').val()+"&fEjercicio="+$('#Ejercicio').val()+"&fCategoriaProg="+$('#CategoriaProg').val()+"&ventana=fuente&iframe=true&width=100%&height=100%";
		$('#btPartida').attr('href', href);
	}
</script>