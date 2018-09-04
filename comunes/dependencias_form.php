<?php
if ($opcion == "nuevo") {
	$sel_registros = "";
	$field['Estado'] = "A";
	##
	$_titulo = "Nuevo Registro";
	$accion = "nuevo";
	$disabled_ver = "";
	$display_submit = "";
	$label_submit = "Guardar";
	$focus = "Dependencia";
}
elseif ($opcion == "modificar" || $opcion == "ver") {
	//	consulto datos generales
	$sql = "SELECT
				d.*,
				o.Organismo,
				e.CodEmpleado,
				p.NomCompleto AS NomPersona,
				pu.DescripCargo,
				dp.Dependencia AS NomEntidadPadre
			FROM
				mastdependencias d
				INNER JOIN mastorganismos o ON (d.CodOrganismo = o.CodOrganismo)
				LEFT JOIN mastpersonas p ON (d.CodPersona = p.CodPersona)
				LEFT JOIN mastempleado e ON (d.CodPersona = e.CodPersona)
				LEFT JOIN rh_puestos pu ON (pu.CodCargo = d.CodCargo)
				LEFT JOIN mastdependencias dp ON (d.EntidadPadre = dp.CodDependencia)
			WHERE d.CodDependencia = '".$sel_registros."'";
	$field = getRecord($sql);
	##
	if ($opcion == "modificar") {
		$_titulo = "Modificar Registro";
		$accion = "modificar";
		$disabled_ver = "";
		$display_submit = "";
		$label_submit = "Modificar";
		$focus = "Dependencia";
	}
	##
	elseif ($opcion == "ver") {
		$_titulo = "Ver Registro";
		$accion = "";
		$disabled_ver = "disabled";
		$display_submit = "display:none;";
		$label_submit = "";
		$focus = "btCancelar";
	}
}
$clkCancelar = "document.getElementById('frmentrada').submit();";
//	------------------------------------
$_width = 700;
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
		            <li id="li1" onclick="currentTab('tab', this);" class="current"><a href="#" onclick="mostrarTab('tab', 1, 3);">Informaci&oacute;n General</a></li>
		            <?php
		            if ($_SESSION['MODULO'] == 'po') {
		            	?>
		            	<li id="li2" onclick="currentTab('tab', this);"><a href="#" onclick="mostrarTab('tab', 2, 3);">Informaci&oacute;n Adicional</a></li>
		            	<li id="li3" onclick="currentTab('tab', this);"><a href="#" onclick="mostrarTab('tab', 3, 3);">FODA</a></li>
		            	<?php
		            }
		            ?>
	            </ul>
            </div>
        </td>
    </tr>
</table>

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=dependencias_lista" method="POST" enctype="multipart/form-data" onsubmit="return formulario(this, '<?=$accion?>');" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />

<div id="tab1" style="display:block;">
	<table width="<?=$_width?>" class="tblForm">
		<tr>
	    	<td colspan="2" class="divFormCaption">Datos Generales</td>
	    </tr>
	    <tr>
			<td class="tagForm">C&oacute;digo:</td>
			<td>
	        	<input type="text" name="CodDependencia" id="CodDependencia" value="<?=$field['CodDependencia']?>" style="width:35px; font-weight:bold;" readonly="readonly" />
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Organismo:</td>
			<td>
	        	<select name="CodOrganismo" id="CodOrganismo" style="width:305px;" <?=$disabled_ver?>>
	                <?=loadSelect("mastorganismos", "CodOrganismo", "Organismo", $field['CodOrganismo'], 0)?>
	            </select>
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Dependencia:</td>
			<td>
	        	<input type="text" name="Dependencia" id="Dependencia" value="<?=$field['Dependencia']?>" style="width:300px;" maxlength="100" <?=$disabled_ver?> />
			</td>
		</tr> 
		<tr>
			<td class="tagForm">* Nro. Interno:</td>
			<td>
	        	<input type="text" name="CodInterno" id="CodInterno" style="width:150px;" maxlength="100" value="<?=$field['CodInterno']?>" <?=$disabled_ver?> />
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Nivel:</td>
			<td>
	        	<input type="text" name="Nivel" id="Nivel" style="width:25px;" maxlength="2" value="<?=$field['Nivel']?>" <?=$disabled_ver?> />
			</td>
		</tr>  
		<tr>
			<td class="tagForm">Entidad Padre:</td>
			<td class="gallery clearfix">
	            <input type="text" name="EntidadPadre" id="EntidadPadre" style="width:45px;" value="<?=$field['EntidadPadre']?>" readonly="readonly" />
				<input type="text" id="NomEntidadPadre" style="width:245px;" value="<?=htmlentities($field['NomEntidadPadre'])?>" disabled="disabled" />
	            <a href="../lib/listas/gehen.php?anz=lista_dependencias&filtrar=default&campo1=EntidadPadre&campo2=NomEntidadPadre&ventana=selLista&iframe=true&width=950&height=430" rel="prettyPhoto[iframe1]" style=" <?=$display_nivel?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
		</tr>
		<tr>
			<td class="tagForm">Empleado:</td>
			<td class="gallery clearfix">
	        	<input type="hidden" name="CodPersona" id="CodPersona" value="<?=$field['CodPersona']?>" />
				<input type="text" id="NomPersona" style="width:300px;" value="<?=htmlentities($field['NomPersona'])?>" disabled="disabled" />
	            <a href="../lib/listas/gehen.php?anz=lista_empleados&filtrar=default&campo1=CodPersona&campo2=NomPersona&campo3=CodCargo&campo4=DescripCargo&ventana=dependencias&iframe=true&width=950&height=430" rel="prettyPhoto[iframe2]" style=" <?=$display_submit?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
		</tr>
		<tr>
			<td class="tagForm">Cargo:</td>
			<td class="gallery clearfix">
	        	<input type="hidden" name="CodCargo" id="CodCargo" value="<?=$field['CodCargo']?>" />
	        	<input type="text" id="DescripCargo" style="width:300px;" maxlength="100" value="<?=htmlentities($field['DescripCargo'])?>" disabled="disabled" />
	            <a href="../lib/listas/gehen.php?anz=lista_cargos&filtrar=default&campo1=CodCargo&campo2=DescripCargo&ventana=&iframe=true&width=950&height=430" rel="prettyPhoto[iframe3]" style=" <?=$display_submit?>">
	            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
	            </a>
			</td>
		</tr>
		<tr>
			<td class="tagForm">&nbsp;</td>
			<td>
	            <input type="checkbox" name="FlagControlFiscal" id="FlagControlFiscal" value="S" <?=chkOpt($field['FlagControlFiscal'], "S");?> <?=$disabled_ver?> /> Ejerce Control Fiscal
			</td>
		</tr>
		<tr>
			<td class="tagForm">&nbsp;</td>
			<td>
	            <input type="checkbox" name="FlagPrincipal" id="FlagPrincipal" value="S" <?=chkOpt($field['FlagPrincipal'], "S");?> <?=$disabled_ver?> /> Principal
			</td>
		</tr>
		<tr>
			<td class="tagForm">Tel&eacute;fonos:</td>
			<td>
	        	<input type="text" name="Telefono1" id="Telefono1" style="width:100px;" maxlength="15" value="<?=$field['Telefono1']?>" <?=$disabled_ver?> />
	        	<input type="text" name="Telefono2" id="Telefono2" style="width:100px;" maxlength="15" value="<?=$field['Telefono2']?>" <?=$disabled_ver?> />
			</td>
		</tr>
		<tr>
			<td class="tagForm">Extencion:</td>
			<td>
	        	<input type="text" name="Extencion1" id="Extencion1" style="width:50px;" maxlength="4" value="<?=$field['Extencion1']?>" <?=$disabled_ver?> />
	        	<input type="text" name="Extencion2" id="Extencion2" style="width:50px;" maxlength="4" value="<?=$field['Extencion2']?>" <?=$disabled_ver?> />
			</td>
		</tr>
		<tr>
			<td class="tagForm">Estado:</td>
			<td>
	            <input type="radio" name="Estado" id="Activo" value="A" <?=chkOpt($field['Estado'], "A");?> <?=$disabled_ver?> /> Activo
	            &nbsp; &nbsp;
	            <input type="radio" name="Estado" id="Inactivo" value="I" <?=chkOpt($field['Estado'], "I");?> <?=$disabled_ver?> /> Inactivo
			</td>
		</tr>
	    <tr>
			<td class="tagForm">&Uacute;ltima Modif.:</td>
			<td>
				<input type="text" size="30" value="<?=$field['UltimoUsuario']?>" disabled="disabled" />
				<input type="text" size="25" value="<?=$field['UltimaFecha']?>" disabled="disabled" />
			</td>
		</tr>
	</table>

	<input type="hidden" id="sel_designacion" />
	<table class="tblBotones" style="width:<?=$_width?>px; margin:auto;">
		<thead>
		    <tr>
		    	<th class="divFormCaption">Designaciones Especiales</th>
		    </tr>
	    </thead>
	    <tbody>
		    <tr>
		        <td align="right">
		            <input type="button" class="btLista" value="Insertar" onclick="insertar2(this, 'designacion', 'modulo=ajax&accion=designacion_insertar', 'dependencias_ajax.php');" <?=$disabled_ver?> />
		            <input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'designacion');" <?=$disabled_ver?> />
		        </td>
		    </tr>
	    </tbody>
	</table>
	<div style="overflow:scroll; height:140px; max-width:<?=$_width?>px; margin:auto;">
	<table class="tblLista" style="width:800px; min-width:<?=$_width?>px;">
		<thead>
	    <tr>
	        <th width="20">#</th>
			<th align="left">Designaci&oacute;n</th>
			<th width="200" align="left">Designaci&oacute;n</th>
	        <th width="75">Desde</th>
	        <th width="75">Hasta</th>
			<th width="35">Desig. Esp.</th>
	    </tr>
	    </thead>
	    
	    <tbody id="lista_designacion">
	    	<?php
			$nro_designacion = 0;
			$sql = "SELECT
						pd.*
					FROM mastpersonasdesignacion pd
					WHERE
						pd.CodDependencia = '$field[CodDependencia]' AND
						pd.CodPersona = '$field[CodPersona]'";
			$field_designacion = getRecords($sql);
			foreach ($field_designacion as $f) {
				$id = ++$nro_designacion;
				?>
	            <tr class="trListaBody" onclick="clk($(this), 'designacion', 'designacion_<?=$id?>');" id="designacion_<?=$id?>">
	                <th><?=$id?></th>
	                <td>
	                    <textarea name="designacion_Designacion[]" class="cell" style="height:16px;" <?=$disabled_ver?>><?=htmlentities($f['Designacion'])?></textarea>
	                </td>
					<td>
						<input type="text" name="designacion_Descripcion[]" value="<?=htmlentities($f['Descripcion'])?>" class="cell" maxlength="50" <?=$disabled_ver?> />
					</td>
					<td>
						<input type="text" name="designacion_FechaDesde[]" value="<?=formatFechaDMA($f['FechaDesde'])?>" class="cell datepicker" style="text-align:center;" <?=$disabled_ver?> />
					</td>
					<td>
						<input type="text" name="designacion_FechaHasta[]" value="<?=formatFechaDMA($f['FechaHasta'])?>" class="cell datepicker" style="text-align:center;" <?=$disabled_ver?> />
					</td>
					<td align="center">
						<input type="checkbox" name="designacion_FlagDesignacionEspecial[]" value="S" <?=chkFlag($f['FlagDesignacionEspecial'])?> <?=$disabled_ver?> />
					</td>
	            </tr>
	            <?php
			}
			?>
	    </tbody>
	</table>
	</div>
	<input type="hidden" id="nro_designacion" value="<?=$nro_designacion?>" />
	<input type="hidden" id="can_designacion" value="<?=$nro_designacion?>" />
</div>

<div id="tab2" style="display:none;">
	<table width="<?=$_width?>" class="tblForm">
		<tr>
	    	<td class="divFormCaption">Misi&oacute;n</td>
	    </tr>
	    <tr>
			<td>
	        	<textarea name="Mision" id="Mision" style="width:99%; height:147px;" <?=$disabled_ver?>><?=htmlentities($field['Mision'])?></textarea>
			</td>
		</tr>
		<tr>
	    	<td class="divFormCaption">Visi&oacute;n</td>
	    </tr>
	    <tr>
			<td>
	        	<textarea name="Vision" id="Vision" style="width:99%; height:148px;" <?=$disabled_ver?>><?=htmlentities($field['Vision'])?></textarea>
			</td>
		</tr>
	</table>
</div>

<div id="tab3" style="display:none;">
	<input type="hidden" id="sel_matriz" />
	<table class="tblBotones" style="width:<?=$_width?>px">
		<thead>
	    <tr>
	    	<th class="divFormCaption">Matriz FODA</th>
	    </tr>
	    </thead>
	    <tbody>
	    <tr>
	        <td align="right">
	            <input type="button" class="btLista" value="Insertar" onclick="insertar2(this, 'matriz', 'modulo=ajax&accion=matriz_insertar', 'dependencias_ajax.php');" <?=$disabled_ver?> />
	            <input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'matriz');" <?=$disabled_ver?> />
	        </td>
	    </tr>
	    </tbody>
	</table>
	<div style="overflow:scroll; height:308px; max-width:<?=$_width?>px; margin:auto;">
	<table class="tblLista" style="width:100%;">
		<thead>
	    <tr>
	        <th width="20">#</th>
	        <th width="100">Columna</th>
	        <th align="left">Descripci&oacute;n</th>
	    </tr>
	    </thead>
	    
	    <tbody id="lista_matriz">
	    	<?php
			$nro_matriz = 0;
			$sql = "SELECT *
					FROM po_matrizfoda
					WHERE CodDependencia = '".$field['CodDependencia']."'
					ORDER BY Columna, CodMatriz";
			$field_matriz = getRecords($sql);
			foreach ($field_matriz as $f) {
				$id = ++$nro_matriz;
				?>
	            <tr class="trListaBody" onclick="clk($(this), 'matriz', 'matriz_<?=$id?>');" id="matriz_<?=$id?>">
	                <th><?=$id?></th>
	                <td>
	                    <select name="matriz_Columna[]" class="cell" <?=$disabled_ver?>>
	                    	<?=loadSelectValores('columna-foda', $f['Columna'])?>
	                    </select>
	                </td>
					<td>
						<textarea name="matriz_Descripcion[]" class="cell" style="height:35px;" <?=$disabled_ver?>><?=htmlentities($f['Descripcion'])?></textarea>
					</td>
	            </tr>
	            <?php
			}
			?>
	    </tbody>
	</table>
	</div>
	<input type="hidden" id="nro_matriz" value="<?=$nro_matriz?>" />
	<input type="hidden" id="can_matriz" value="<?=$nro_matriz?>" />
</div>

<center>
<input type="submit" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" id="btSubmit" />
<input type="button" value="Cancelar" style="width:75px;" id="btCancelar" onclick="<?=$clkCancelar?>" />
</center>
</form>
<div style="width:<?=$_width?>px" class="divMsj">Campos Obligatorios *</div>

<script type="text/javascript" language="javascript">
	function formulario(form, accion) {
		bloqueo(true);
		//	valido
		var error = "";
		if ($("#CodOrganismo").val().trim() == "" || $("#Dependencia").val().trim() == "" || $("#CodInterno").val().trim() == "" || $("#Nivel").val().trim() == "") error = "Debe llenar los campos obligatorios";
		//	valido errores
		if (error != "") {
			cajaModal(error, "error", 400);
		} else {
			//	ajax
			$.ajax({
				type: "POST",
				url: "dependencias_ajax.php",
				data: "modulo=formulario&accion="+accion+"&"+$('#frmentrada').serialize(),
				async: false,
				success: function(resp) {
					if (resp.trim() != '') cajaModal(resp.trim(), 'error', 400);
					else form.submit();
				}
			});
		}
		return false;
	}
</script>