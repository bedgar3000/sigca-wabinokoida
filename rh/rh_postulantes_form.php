<?php
if ($opcion == "nuevo") {
	$field['Estado'] = "P";
	$field['CodPaisNac'] = $_PARAMETRO["PAISDEFAULT"];
	$field['CodEstadoNac'] = $_PARAMETRO["ESTADODEFAULT"];
	$field['CodMunicipioNac'] = $_PARAMETRO["MUNICIPIODEFAULT"];
	$field['CodCiudadNac'] = $_PARAMETRO["CIUDADDEFAULT"];
	$field['CodPaisDom'] = $_PARAMETRO["PAISDEFAULT"];
	$field['CodEstadoDom'] = $_PARAMETRO["ESTADODEFAULT"];
	$field['CodMunicipioDom'] = $_PARAMETRO["MUNICIPIODEFAULT"];
	$field['CodCiudadDom'] = $_PARAMETRO["CIUDADDEFAULT"];
	$accion = "nuevo";
	$_titulo = "Nuevo Postulante";
	$label_submit = "Guardar";
	$disabled_ver = "";
	$disabled_detalles = "";
	$display_submit = "display:none;";
	$focus = "";
}
elseif ($opcion == "modificar" || $opcion == "ver") {
	//	consulto datos generales
	$sql = "SELECT
				p.*,
				c1.CodMunicipio AS CodMunicipioNac,
				m1.CodEstado AS CodEstadoNac,
				e1.CodPais AS CodPaisNac,
				c2.CodMunicipio AS CodMunicipioDom,
				m2.CodEstado AS CodEstadoDom,
				e2.CodPais AS CodPaisDom
			FROM
				rh_postulantes p
				INNER JOIN mastciudades c1 ON (c1.Codciudad = p.CiudadNacimiento)
				INNER JOIN mastmunicipios m1 ON (m1.CodMunicipio = c1.CodMunicipio)
				INNER JOIN mastestados e1 ON (e1.CodEstado = m1.CodEstado)
				INNER JOIN mastciudades c2 ON (c2.Codciudad = p.CiudadNacimiento)
				INNER JOIN mastmunicipios m2 ON (m2.CodMunicipio = c2.CodMunicipio)
				INNER JOIN mastestados e2 ON (e2.CodEstado = m2.CodEstado)
			WHERE p.Postulante = '".$sel_registros."'";
	$field = getRecord($sql);
	##
	if ($opcion == "modificar") {
		$_titulo = "Modificar Postulante";
		$accion = "modificar";
		$label_submit = "Modificar";
		$disabled_ver = "";
		$disabled_detalles = "";
		$display_submit = "";
		$focus = "";
	}
	elseif ($opcion == "ver") {
		$_titulo = "Ver Postulante";
		$accion = "";
		$label_submit = "";
		$disabled_ver = "disabled";
		$disabled_detalles = "disabled";
		$display_submit = "display:none;";
		$focus = "btCancelar";
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
		            <li id="li1" onclick="currentTab('tab', this);" class="current"><a href="#" onclick="mostrarTab('tab', 1, 7);">Datos Personales</a></li>
		            <li id="li2" onclick="currentTab('tab', this);"><a href="#" onclick="mostrarTab('tab', 2, 7);">Otros Datos</a></li>
		            <li id="li3" onclick="currentTab('tab', this);"><a href="#" onclick="mostrarTab('tab', 3, 7);">Instrucci&oacute;n</a></li>
		            <li id="li4" onclick="currentTab('tab', this);"><a href="#" onclick="mostrarTab('tab', 4, 7);">Cursos</a></li>
		            <li id="li5" onclick="currentTab('tab', this);"><a href="#" onclick="mostrarTab('tab', 5, 7);">Experiencia Laboral</a></li>
		            <li id="li6" onclick="currentTab('tab', this);"><a href="#" onclick="mostrarTab('tab', 6, 7);">Documentos</a></li>
		            <li id="li7" onclick="currentTab('tab', this);"><a href="#" onclick="mostrarTab('tab', 7, 7);">Cargos Aplicables</a></li>
	            </ul>
            </div>
        </td>
    </tr>
</table>

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=rh_postulantes_lista" method="POST" enctype="multipart/form-data" onsubmit="return formulario(this, '<?=$accion?>');" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="fCodCargo" id="fCodCargo" value="<?=$fCodCargo?>" />
<input type="hidden" name="fDescripCargo" id="fDescripCargo" value="<?=$fDescripCargo?>" />
<input type="hidden" name="fCodCentroEstudio" id="fCodCentroEstudio" value="<?=$fCodCentroEstudio?>" />
<input type="hidden" name="fNomCentroEstudio" id="fNomCentroEstudio" value="<?=$fNomCentroEstudio?>" />
<input type="hidden" name="fCodGradoInstruccion" id="fCodGradoInstruccion" value="<?=$fCodGradoInstruccion?>" />
<input type="hidden" name="fCodCurso" id="fCodCurso" value="<?=$fCodCurso?>" />
<input type="hidden" name="fNomCurso" id="fNomCurso" value="<?=$fNomCurso?>" />
<input type="hidden" name="fSexo" id="fSexo" value="<?=$fSexo?>" />
<input type="hidden" name="fArea" id="fArea" value="<?=$fArea?>" />
<input type="hidden" name="fCodIdioma" id="fCodIdioma" value="<?=$fCodIdioma?>" />
<input type="hidden" name="fEdadD" id="fEdadD" value="<?=$fEdadD?>" />
<input type="hidden" name="fEdadH" id="fEdadH" value="<?=$fEdadH?>" />
<input type="hidden" name="fCodProfesion" id="fCodProfesion" value="<?=$fCodProfesion?>" />
<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
<input type="hidden" name="fAnioD" id="fAnioD" value="<?=$fAnioD?>" />
<input type="hidden" name="fAnioH" id="fAnioH" value="<?=$fAnioH?>" />
<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
<input type="hidden" name="Expediente" id="Expediente" value="<?=$field['Expediente']?>" />

<div id="tab1" style="display:block;">
<table width="<?=$_width?>" class="tblForm">
	<tr>
    	<td colspan="4" class="divFormCaption">Datos Generales</td>
    </tr>
    <tr>
		<td class="tagForm">Postulante:</td>
		<td width="37%">
            <input type="text" name="Postulante" id="Postulante" style="width:75px;" class="codigo" value="<?=$field['Postulante']?>" readonly />
		</td>
		<td class="tagForm">Estado:</td>
		<td width="37%">
            <input type="hidden" name="Estado" id="Estado" value="<?=$field['Estado']?>" />
            <input type="text" style="width:100px;" class="codigo" value="<?=strtoupper(printValores("ESTADO-POSTULANTE", $field['Estado']))?>" disabled="disabled" />
		</td>
	</tr>
    <tr>
		<td class="tagForm">Apellido Paterno:</td>
		<td>
			<input type="text" name="Apellido1" id="Apellido1" style="width:95%;" maxlength="25" value="<?=$field['Apellido1']?>" <?=$disabled_ver?> />
		</td>
		<td class="tagForm">* Materno:</td>
		<td>
			<input type="text" name="Apellido2" id="Apellido2" style="width:95%;" maxlength="25" value="<?=$field['Apellido2']?>" <?=$disabled_ver?> />
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Nombres:</td>
		<td>
			<input type="text" name="Nombres" id="Nombres" style="width:95%;" maxlength="50" value="<?=$field['Nombres']?>" <?=$disabled_ver?> />
		</td>
		<td class="tagForm">* Sexo:</td>
		<td>
			<select name="Sexo" id="Sexo" style="width:100px;" <?=$disabled_ver?>>
				<?=loadSelectGeneral("SEXO", $field['Sexo'], 0)?>
			</select>
		</td>
	</tr>
	<tr>
    	<td colspan="4" class="divFormCaption">Resumen Ejecutivo</td>
    </tr>
    <tr>
		<td colspan="4" align="center">
			<textarea name="ResumenEjec" id="ResumenEjec" style="width:99%; height:75px;" <?=$disabled_ver?>><?=$field['ResumenEjec']?></textarea>
		</td>
	</tr>
	<tr>
    	<td colspan="4" class="divFormCaption">Lugar y Fecha de Nacimiento</td>
    </tr>
	<tr>
		<td class="tagForm">* Pais:</td>
		<td>
            <select name="CodPaisNac" id="CodPaisNac" style="width:200px;" onchange="getOptionsSelect(this.value, 'estado', 'CodEstadoNac', 1, 'CodMunicipioNac', 'CiudadNacimiento');" <?=$disabled_ver?>>
                <?=loadSelect("mastpaises", "CodPais", "Pais", $field['CodPaisNac'], 0);?>
            </select>
		</td>
		<td class="tagForm">* Estado:</td>
		<td>
            <select name="CodEstadoNac" id="CodEstadoNac" style="width:200px;" onchange="getOptionsSelect(this.value, 'municipio', 'CodMunicipioNac', 1, 'CiudadNacimiento');" <?=$disabled_ver?>>
                <?=loadSelectDependienteEstado($field['CodEstadoNac'], $field['CodPaisNac'], 0);?>
            </select>
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Municipio:</td>
		<td>
            <select name="CodMunicipioNac" id="CodMunicipioNac" style="width:200px;" onchange="getOptionsSelect(this.value, 'ciudad', 'CiudadNacimiento', 1);" <?=$disabled_ver?>>
                <?=loadSelectDependiente("mastmunicipios", "CodMunicipio", "Municipio", "CodEstado", $field['CodMunicipioNac'], $field['CodEstadoNac'], 0);?>
            </select>
		</td>
		<td class="tagForm">* Ciudad:</td>
		<td>
            <select name="CiudadNacimiento" id="CiudadNacimiento" style="width:200px;" <?=$disabled_ver?>>
                <?=loadSelectDependiente("mastciudades", "CodCiudad", "Ciudad", "CodMunicipio", $field['CiudadNacimiento'], $field['CodMunicipioNac'], 0);?>
            </select>
		</td>
	</tr>
    <tr>
        <td class="tagForm">* Fecha:</td>
		<td colspan="2">
        	<input type="text" name="Fnacimiento" id="Fnacimiento" value="<?=formatFechaDMA($field['Fnacimiento'])?>" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" onchange="getEdad(this.value, '<?=formatFechaDMA($FechaActual)?>', $('#Anios'), $('#Meses'), $('#Dias'));" <?=$disabled_ver?> />
        </td>
        <td>
        	<input type="text" id="Anios" style="width:25px;" class="disabled" value="<?=$Anios?>" disabled="disabled" /> a &nbsp; 
        	<input type="text" id="Meses" style="width:25px;" class="disabled" value="<?=$Meses?>" disabled="disabled" /> m &nbsp; 
        	<input type="text" id="Dias" style="width:25px;" class="disabled" value="<?=$Dias?>" disabled="disabled" /> d &nbsp; 
        </td>
    </tr>
	<tr>
    	<td colspan="4" class="divFormCaption">Domicilio Actual</td>
    </tr>
    <tr>
		<td class="tagForm">* Direcci&oacute;n:</td>
		<td colspan="3">
			<textarea name="Direccion" id="Direccion" style="width:99%; height:30px;" <?=$disabled_ver?>><?=$field['Direccion']?></textarea>
		</td>
	</tr>
    <tr>
		<td class="tagForm">Referencia:</td>
		<td colspan="3">
			<textarea name="Referencia" id="Referencia" style="width:99%; height:30px;" <?=$disabled_ver?>><?=$field['Referencia']?></textarea>
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Pais:</td>
		<td>
            <select name="CodPaisDom" id="CodPaisDom" style="width:200px;" onchange="getOptionsSelect(this.value, 'estado', 'CodEstadoDom', true, 'CodMunicipioDom', 'CiudadDomicilio');" <?=$disabled_ver?>>
                <?=loadSelect("mastpaises", "CodPais", "Pais", $field['CodPaisDom'], 0);?>
            </select>
		</td>
		<td class="tagForm">* Estado:</td>
		<td>
            <select name="CodEstadoDom" id="CodEstadoDom" style="width:200px;" onchange="getOptionsSelect(this.value, 'municipio', 'CodMunicipioDom', true, 'CiudadDomicilio');" <?=$disabled_ver?>>
                <?=loadSelectDependienteEstado($field['CodEstadoDom'], $field['CodPaisDom'], 0);?>
            </select>
		</td>
	</tr>
	<tr>
		<td class="tagForm">* Municipio:</td>
		<td>
            <select name="CodMunicipioDom" id="CodMunicipioDom" style="width:200px;" onchange="getOptionsSelect(this.value, 'ciudad', 'CiudadDomicilio', true);" <?=$disabled_ver?>>
                <?=loadSelectDependiente("mastmunicipios", "CodMunicipio", "Municipio", "CodEstado", $field['CodMunicipioDom'], $field['CodEstadoDom'], 0);?>
            </select>
		</td>
		<td class="tagForm">* Ciudad:</td>
		<td>
            <select name="CiudadDomicilio" id="CiudadDomicilio" style="width:200px;" <?=$disabled_ver?>>
                <?=loadSelectDependiente("mastciudades", "CodCiudad", "Ciudad", "CodMunicipio", $field['CiudadDomicilio'], $field['CodMunicipioDom'], 0);?>
            </select>
		</td>
	</tr>
    <tr>
		<td class="tagForm">Tel&eacute;fono:</td>
		<td>
			<input type="text" name="Telefono1" id="Telefono1" style="width:200px;" maxlength="15" value="<?=$field['Telefono1']?>" class="phone" <?=$disabled_ver?> />
		</td>
		<td class="tagForm">E-mail:</td>
		<td>
			<input type="text" name="Email" id="Email" style="width:95%;" maxlength="255" value="<?=$field['Email']?>" <?=$disabled_ver?> />
		</td>
	</tr>
	<tr>
    	<td colspan="4" class="divFormCaption">Documentos de Identificaci&oacute;n</td>
    </tr>
    <tr>
		<td class="tagForm">* Documento:</td>
		<td>
            <select name="TipoDocumento" id="TipoDocumento" style="width:200px;" <?=$disabled_ver?>>
            	<option value="">&nbsp;</option>
                <?=getMiscelaneos($field['TipoDocumento'], "DOCUMENTOS", 0);?>
            </select>
		</td>
		<td class="tagForm">* Nro. Documento:</td>
		<td>
			<input type="text" name="Ndocumento" id="Ndocumento" style="width:200px;" maxlength="20" value="<?=$field['Ndocumento']?>" <?=$disabled_ver?> />
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
</div>

<div id="tab2" style="display:none;">
<table width="<?=$_width?>" class="tblForm">
	<tr>
    	<td colspan="4" class="divFormCaption">Otros Datos Personales</td>
    </tr>
    <tr>
		<td class="tagForm">Grupo Sangu&iacute;neo:</td>
		<td width="37%">
            <select name="GrupoSanguineo" id="GrupoSanguineo" style="width:200px;" <?=$disabled_ver?>>
            	<option value="">&nbsp;</option>
                <?=getMiscelaneos($field['GrupoSanguineo'], "SANGRE", 0);?>
            </select>
		</td>
		<td class="tagForm">Situaci&oacute;n Domicilio:</td>
		<td width="37%">
            <select name="SituacionDomicilio" id="SituacionDomicilio" style="width:200px;" <?=$disabled_ver?>>
            	<option value="">&nbsp;</option>
                <?=getMiscelaneos($field['SituacionDomicilio'], "SITDOM", 0);?>
            </select>
		</td>
	</tr>
    <tr>
		<td class="tagForm">* Edo. Civil:</td>
		<td>
            <select name="EstadoCivil" id="EstadoCivil" style="width:200px;" <?=$disabled_ver?>>
            	<option value="">&nbsp;</option>
                <?=getMiscelaneos($field['EstadoCivil'], "EDOCIVIL", 0);?>
            </select>
		</td>
        <td class="tagForm">Fecha:</td>
		<td>
        	<input type="text" name="FedoCivil" id="FedoCivil" value="<?=formatFechaDMA($field['FedoCivil'])?>" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" <?=$disabled_ver?> />
        </td>
	</tr>
	<tr>
    	<td colspan="4" class="divFormCaption">Informaci&oacute;n Adicional</td>
    </tr>
    <tr>
		<td colspan="4" align="center">
			<textarea name="InformacionAdic" id="InformacionAdic" style="width:99%; height:35px;" <?=$disabled_ver?>><?=$field['InformacionAdic']?></textarea>
		</td>
	</tr>
	<tr>
    	<td colspan="4" class="divFormCaption">Actividades Extralaborales</td>
    </tr>
    <tr>
        <td class="tagForm">Beneficas:</td>
		<td align="center">
			<textarea name="Beneficas" id="Beneficas" style="width:98%; height:40px;" <?=$disabled_ver?>><?=$field['Beneficas']?></textarea>
		</td>
        <td class="tagForm">Laborales:</td>
		<td align="center">
			<textarea name="Laborales" id="Laborales" style="width:98%; height:40px;" <?=$disabled_ver?>><?=$field['Laborales']?></textarea>
		</td>
	</tr>
    <tr>
        <td class="tagForm">Culturales:</td>
		<td align="center">
			<textarea name="Culturales" id="Culturales" style="width:98%; height:40px;" <?=$disabled_ver?>><?=$field['Culturales']?></textarea>
		</td>
        <td class="tagForm">Deportivas:</td>
		<td align="center">
			<textarea name="Deportivas" id="Deportivas" style="width:98%; height:40px;" <?=$disabled_ver?>><?=$field['Deportivas']?></textarea>
		</td>
	</tr>
    <tr>
        <td class="tagForm">Religiosas:</td>
		<td align="center">
			<textarea name="Religiosas" id="Religiosas" style="width:98%; height:40px;" <?=$disabled_ver?>><?=$field['Religiosas']?></textarea>
		</td>
        <td class="tagForm">Sociales:</td>
		<td align="center">
			<textarea name="Sociales" id="Sociales" style="width:98%; height:40px;" <?=$disabled_ver?>><?=$field['Sociales']?></textarea>
		</td>
	</tr>
</table>
</div>

<div id="tab3" style="display:none;">
<table style="margin:auto; width:<?=$_width?>px;">
	<tr>
    	<td colspan="2">
			<input type="hidden" id="sel_instruccion" />
			<table width="100%" class="tblBotones">
				<thead>
			    <tr>
			    	<th class="divFormCaption" colspan="2">Instrucci&oacute;n</th>
			    </tr>
			    </thead>
			    <tbody>
			    <tr>
		            <td class="gallery clearfix">
						<a id="a_instruccion" href="#" rel="prettyPhoto[iframe1]" style="display:none;"></a>
		            	<input type="button" value="Centro de Estudio" onclick="listaSelector('instruccion', 'lista_centro_estudio', 'selLista', ['CodCentroEstudio','NomCentroEstudio']);" <?=$disabled_detalles?> />
		            </td>
			        <td align="right">
			            <input type="button" class="btLista" value="Insertar" onclick="insertar2(this, 'instruccion', 'modulo=ajax&accion=instruccion_insertar', 'rh_postulantes_ajax.php');" <?=$disabled_detalles?> />
			            <input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'instruccion');" <?=$disabled_detalles?> />
			        </td>
			    </tr>
			    </tbody>
			</table>
			<div style="overflow:scroll; height:200px; width:100%;">
			<table class="tblLista" style="width:100%;">
			    <tbody id="lista_instruccion">
			    	<?php
					$nro_instruccion = 0;
					$sql = "SELECT
								pi.*,
								ce.Descripcion AS NomCentroEstudio
							FROM
								rh_postulantes_instruccion pi
								INNER JOIN rh_centrosestudios ce ON (ce.CodCentroEstudio = pi.CodCentroEstudio)
							WHERE pi.Postulante = '".$field['Postulante']."'
							ORDER BY FechaGraduacion DESC";
					$field_instruccion = getRecords($sql);
					foreach ($field_instruccion as $f) {
						$id = ++$nro_instruccion;
						?>
			            <tr class="trListaBody" onclick="clk($(this), 'instruccion', 'instruccion_<?=$id?>');" id="instruccion_<?=$id?>">
			                <th><?=$id?></th>
			                <td>
			                	<table border="1" width="100%">
								    <tr>
										<td class="tagForm">* G. Instrucci&oacute;n:</td>
										<td>
											<select name="instruccion_CodGradoInstruccion[]" id="instruccion_CodGradoInstruccion<?=$id?>" style="width:175px;" onChange="getOptionsSelect(this.value, 'nivel-instruccion', 'instruccion_Nivel<?=$id?>', true); getOptionsSelect2('profesiones', 'instruccion_CodProfesion<?=$id?>', true, this.value, $('#instruccion_Area<?=$id?>').val());" <?=$disabled_detalles?>>
								            	<option value="">&nbsp;</option>
												<?=loadSelect("rh_gradoinstruccion", "CodGradoInstruccion", "Descripcion", $f['CodGradoInstruccion'], 0)?>
											</select>
										</td>
										<td class="tagForm">* Nivel:</td>
										<td>
											<select name="instruccion_Nivel[]" id="instruccion_Nivel<?=$id?>" style="width:175px;" <?=$disabled_detalles?>>
								            	<option value="">&nbsp;</option>
												<?=loadSelectDependiente("rh_nivelgradoinstruccion", "Nivel", "Descripcion", "CodGradoInstruccion", $f['Nivel'], $f['CodGradoInstruccion'], 0)?>
											</select>
										</td>
										<td class="tagForm">* F. Graduaci&oacute;n:</td>
										<td>
											<input type="text" name="instruccion_FechaGraduacion[]" value="<?=formatFechaDMA($f['FechaGraduacion'])?>" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" <?=$disabled_detalles?> />
										</td>
									</tr>
								    <tr>
										<td class="tagForm">Area Profesional:</td>
										<td>
											<select name="instruccion_Area[]" id="instruccion_Area<?=$id?>" style="width:175px;" onChange="getOptionsSelect2('profesiones', 'instruccion_CodProfesion<?=$id?>', true, $('#instruccion_CodGradoInstruccion<?=$id?>').val(), this.value);" <?=$disabled_detalles?>>
								            	<option value="">&nbsp;</option>
												<?=getMiscelaneos($f['Area'], "AREA", 0)?>
											</select>
										</td>
										<td class="tagForm">Profesi&oacute;n:</td>
										<td>
											<select name="instruccion_CodProfesion[]" id="instruccion_CodProfesion<?=$id?>" style="width:175px;" <?=$disabled_detalles?>>
								            	<option value="">&nbsp;</option>
												<?=loadSelectDependiente2("rh_profesiones", "CodProfesion", "Descripcion", "CodGradoInstruccion", "Area", $f['CodProfesion'], $f['CodGradoInstruccion'], $f['Area'], 0)?>
											</select>
										</td>
										<td class="tagForm">Desde:</td>
										<td>
											<input type="text" name="instruccion_FechaDesde[]" value="<?=formatFechaDMA($f['FechaDesde'])?>" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" <?=$disabled_detalles?> />
										</td>
									</tr>
								    <tr>
										<td class="tagForm">Colegiatura:</td>
										<td>
								            <select name="instruccion_Colegiatura[]" style="width:175px;" <?=$disabled_detalles?>>
								            	<option value="">&nbsp;</option>
								                <?=getMiscelaneos($f['Colegiatura'], "COLEGIOS", 0);?>
								            </select>
										</td>
										<td class="tagForm">Nro. Colegiatura:</td>
										<td>
											<input type="text" name="instruccion_NroColegiatura[]" style="width:170px;" maxlength="9" value="<?=$f['NroColegiatura']?>" <?=$disabled_detalles?> />
										</td>
										<td class="tagForm">Hasta:</td>
										<td>
											<input type="text" name="instruccion_FechaHasta[]" value="<?=formatFechaDMA($f['FechaHasta'])?>" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" <?=$disabled_detalles?> />
										</td>
									</tr>
								    <tr>
										<td class="tagForm">* Centro de Estudio:</td>
										<td class="gallery clearfix">
								            <input type="hidden" name="instruccion_CodCentroEstudio[]" id="instruccion_CodCentroEstudio<?=$id?>" value="<?=$f['CodCentroEstudio']?>" />
											<textarea name="instruccion_NomCentroEstudio[]" id="instruccion_NomCentroEstudio<?=$id?>" style="width:171px; height:30px;" readonly="readonly"><?=htmlentities($f['NomCentroEstudio'])?></textarea>
										</td>
										<td class="tagForm">Observaciones:</td>
										<td colspan="3">
											<textarea name="instruccion_Observaciones[]" style="width:97%; height:30px;" <?=$disabled_detalles?>><?=$f['Observaciones']?></textarea>
										</td>
									</tr>
								</table>
			                </td>
			            </tr>
			            <?php
					}
					?>
			    </tbody>
			</table>
			</div>
			<input type="hidden" id="nro_instruccion" value="<?=$nro_instruccion?>" />
			<input type="hidden" id="can_instruccion" value="<?=$nro_instruccion?>" />
        </td>
    </tr>
	<tr>
    	<td width="50%">
			<input type="hidden" id="sel_idioma" />
			<table width="100%" class="tblBotones">
				<thead>
			    <tr>
			    	<th class="divFormCaption">Idiomas</th>
			    </tr>
			    </thead>
			    <tbody>
			    <tr>
			        <td align="right">
			            <input type="button" class="btLista" value="Insertar" onclick="insertar2(this, 'idioma', 'modulo=ajax&accion=idioma_insertar', 'rh_postulantes_ajax.php');" <?=$disabled_detalles?> />
			            <input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'idioma');" <?=$disabled_detalles?> />
			        </td>
			    </tr>
			    </tbody>
			</table>
			<div style="overflow:scroll; height:200px; max-width:450px;">
			<table class="tblLista" style="width:700px;">
				<thead>
			    <tr>
			        <th width="20">#</th>
			        <th align="left">Idioma</th>
			        <th width="100">Lectura</th>
			        <th width="100">Oral</th>
			        <th width="100">Escritura</th>
			        <th width="100">General</th>
			    </tr>
			    </thead>
			    
			    <tbody id="lista_idioma">
			    	<?php
					$nro_idioma = 0;
					$sql = "SELECT
								pi.*,
								md1.Descripcion AS NivelLectura,
								md2.Descripcion AS NivelOral,
								md3.Descripcion AS NivelEscritura,
								md4.Descripcion AS NivelGeneral
							FROM
								rh_postulantes_idioma pi
								LEFT JOIN mastmiscelaneosdet md1 ON (md1.CodDetalle = pi.NivelLectura AND
																	 md1.CodMaestro = 'NIVEL')
								LEFT JOIN mastmiscelaneosdet md2 ON (md2.CodDetalle = pi.NivelOral AND
																	 md2.CodMaestro = 'NIVEL')
								LEFT JOIN mastmiscelaneosdet md3 ON (md3.CodDetalle = pi.NivelEscritura AND
																	 md3.CodMaestro = 'NIVEL')
								LEFT JOIN mastmiscelaneosdet md4 ON (md4.CodDetalle = pi.NivelGeneral AND
																	 md4.CodMaestro = 'NIVEL')
							WHERE Postulante = '".$field['Postulante']."'
							ORDER BY CodIdioma";
					$field_idioma = getRecords($sql);
					foreach ($field_idioma as $f) {
						$id = ++$nro_idioma;
						?>
			            <tr class="trListaBody" onclick="clk($(this), 'idioma', 'idioma_<?=$id?>');" id="idioma_<?=$id?>">
			                <th><?=$id?></th>
			                <td>
			                    <select name="idioma_CodIdioma[]" class="cell" <?=$disabled_detalles?>>
			                        <?=loadSelect2("mastidioma", "CodIdioma", "DescripcionLocal", $f['CodIdioma'])?>
			                    </select>
			                </td>
			                <td>
			                    <select name="idioma_NivelLectura[]" class="cell" <?=$disabled_detalles?>>
			                        <?=getMiscelaneos($f['NivelLectura'], "NIVEL")?>
			                    </select>
			                </td>
			                <td>
			                    <select name="idioma_NivelOral[]" class="cell" <?=$disabled_detalles?>>
			                        <?=getMiscelaneos($f['NivelOral'], "NIVEL")?>
			                    </select>
			                </td>
			                <td>
			                    <select name="idioma_NivelEscritura[]" class="cell" <?=$disabled_detalles?>>
			                        <?=getMiscelaneos($f['NivelEscritura'], "NIVEL")?>
			                    </select>
			                </td>
			                <td>
			                    <select name="idioma_NivelGeneral[]" class="cell" <?=$disabled_detalles?>>
			                        <?=getMiscelaneos($f['NivelGeneral'], "NIVEL")?>
			                    </select>
			                </td>
			            </tr>
			            <?php
					}
					?>
			    </tbody>
			</table>
			</div>
			<input type="hidden" id="nro_idioma" value="<?=$nro_idioma?>" />
			<input type="hidden" id="can_idioma" value="<?=$nro_idioma?>" />
        </td>

    	<td width="50%">
			<input type="hidden" id="sel_informat" />
			<table width="100%" class="tblBotones">
				<thead>
			    <tr>
			    	<th class="divFormCaption">Inform&aacute;tica</th>
			    </tr>
			    </thead>
			    <tbody>
			    <tr>
			        <td align="right" class="gallery clearfix">
			            <a id="a_informat" href="../lib/listas/listado_miscelaneos.php?filtrar=default&ventana=postulantes&detalle=informat&CodMaestro=INFORMAT&CodAplicacion=RH&iframe=true&width=525&height=350" rel="prettyPhoto[iframe3]" style="display:none;"></a>
			            <input type="button" class="btLista" value="Insertar" onclick="$('#a_informat').click();" <?=$disabled_ver?> />
			            <input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'informat');" <?=$disabled_ver?> />
			        </td>
			    </tr>
			    </tbody>
			</table>
			<div style="overflow:scroll; height:200px; max-width:450px;">
			<table class="tblLista" style="width:600px;">
				<thead>
			    <tr>
			        <th width="20">#</th>
			        <th align="left">Curso</th>
			        <th width="100">Nivel</th>
			    </tr>
			    </thead>
			    
			    <tbody id="lista_informat">
			    	<?php
					$nro_informat = 0;
					$sql = "SELECT
								pi.*,
								md1.Descripcion AS NomInformatica
							FROM
								rh_postulantes_informat pi
								LEFT JOIN mastmiscelaneosdet md1 ON (md1.CodDetalle = pi.Informatica AND
																	 md1.CodMaestro = 'INFORMAT')
							WHERE Postulante = '".$field['Postulante']."'
							ORDER BY Informatica";
					$field_informat = getRecords($sql);
					foreach ($field_informat as $f) {
						$id = $f['Informatica'];
						++$nro_informat;
						?>
			            <tr class="trListaBody" onclick="clk($(this), 'informat', 'informat_<?=$id?>');" id="informat_<?=$id?>">
			                <th><?=$nro_informat?></th>
			                <td>
			                	<input type="hidden" name="informat_Informatica[]" value="<?=$f['Informatica']?>" />
			                    <?=$f['NomInformatica']?>
			                </td>
			                <td>
			                    <select name="informat_Nivel[]" class="cell" <?=$disabled_detalles?>>
			                        <?=getMiscelaneos($f['Nivel'], "NIVEL")?>
			                    </select>
			                </td>
			            </tr>
			            <?php
					}
					?>
			    </tbody>
			</table>
			</div>
			<input type="hidden" id="nro_informat" value="<?=$nro_informat?>" />
			<input type="hidden" id="can_informat" value="<?=$nro_informat?>" />
        </td>
    </tr>
</table>
</div>

<div id="tab4" style="display:none;">
<input type="hidden" id="sel_cursos" />
<table class="tblBotones" style="width:<?=$_width?>px;">
	<thead>
    <tr>
    	<th class="divFormCaption" colspan="2">Cursos</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="gallery clearfix">
			<a id="a_cursos" href="#" rel="prettyPhoto[iframe4]" style="display:none;"></a>
        	<input type="button" class="btLista" value="Curso" onclick="listaSelector('cursos', 'lista_cursos', 'selLista', ['CodCurso','NomCurso']);" <?=$disabled_detalles?> />
        	<input type="button" value="Centro de Estudio" onclick="listaSelector('cursos', 'lista_centro_estudio', 'selLista', ['CodCentroEstudio','NomCentroEstudio']);" <?=$disabled_detalles?> />
        </td>
        <td align="right">
            <input type="button" class="btLista" value="Insertar" onclick="insertar2(this, 'cursos', 'modulo=ajax&accion=cursos_insertar', 'rh_postulantes_ajax.php');" <?=$disabled_detalles?> />
            <input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'cursos');" <?=$disabled_detalles?> />
        </td>
    </tr>
    </tbody>
</table>
<div style="overflow:scroll; height:300px; width:<?=$_width?>px; margin:auto;">
<table class="tblLista" style="width:100%;">
    <tbody id="lista_cursos">
    	<?php
		$nro_cursos = 0;
		$sql = "SELECT
					pc.*,
					c.Descripcion AS NomCurso,
					ce.Descripcion AS NomCentroEstudio
				FROM
					rh_postulantes_cursos pc
					INNER JOIN rh_cursos c ON (c.CodCurso = pc.CodCurso)
					INNER JOIN rh_centrosestudios ce ON (ce.CodCentroEstudio = pc.CodCentroEstudio)
				WHERE pc.Postulante = '".$field['Postulante']."'
				ORDER BY Secuencia";
		$field_cursos = getRecords($sql);
		foreach ($field_cursos as $f) {
			$id = ++$nro_cursos;
			?>
            <tr class="trListaBody" onclick="clk($(this), 'cursos', 'cursos_<?=$id?>');" id="cursos_<?=$id?>">
                <th><?=$id?></th>
                <td>
                	<table border="1" width="100%">
					    <tr>
							<td class="tagForm">* Curso:</td>
							<td class="gallery clearfix">
					            <input type="hidden" name="cursos_CodCurso[]" id="cursos_CodCurso<?=$id?>" value="<?=$f['CodCurso']?>" />
					            <input type="text" name="cursos_NomCurso[]" id="cursos_NomCurso<?=$id?>" value="<?=$f['NomCurso']?>" style="width:170px;" readonly="readonly" />
							</td>
							<td class="tagForm">* Periodo:</td>
							<td>
								<input type="text" name="cursos_PeriodoCulminacion[]" value="<?=$f['PeriodoCulminacion']?>" maxlength="7" style="width:60px;" class="periodo" <?=$disabled_detalles?> />
							</td>
							<td class="tagForm">Horas:</td>
							<td>
								<input type="text" name="cursos_TotalHoras[]" value="<?=$f['TotalHoras']?>" maxlength="4" style="width:60px;" <?=$disabled_detalles?> />
							</td>
						</tr>
					    <tr>
							<td class="tagForm">* Centro de Estudio:</td>
							<td class="gallery clearfix">
					            <input type="hidden" name="cursos_CodCentroEstudio[]" id="cursos_CodCentroEstudio<?=$id?>" value="<?=$f['CodCentroEstudio']?>" />
					            <input type="text" name="cursos_NomCentroEstudio[]" id="cursos_NomCentroEstudio<?=$id?>" value="<?=$f['NomCentroEstudio']?>" style="width:170px;" readonly="readonly" />
							</td>
							<td class="tagForm">Desde:</td>
							<td>
								<input type="text" name="cursos_FechaDesde[]" value="<?=formatFechaDMA($f['FechaDesde'])?>" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" <?=$disabled_detalles?> />
							</td>
							<td class="tagForm">A&ntilde;os Vigencia:</td>
							<td>
								<input type="text" name="cursos_AniosVigencia[]" value="<?=$f['AniosVigencia']?>" maxlength="2" style="width:60px;" <?=$disabled_detalles?> />
							</td>
						</tr>
					    <tr>
							<td class="tagForm">* Tipo de Curso:</td>
							<td>
								<select name="cursos_TipoCurso[]" id="cursos_TipoCurso<?=$id?>" style="width:175px;" <?=$disabled_detalles?>>
					            	<option value="">&nbsp;</option>
									<?=getMiscelaneos($f['TipoCurso'], "TIPOCURSO", 0)?>
								</select>
							</td>
							<td class="tagForm">Hasta:</td>
							<td>
								<input type="text" name="cursos_FechaHasta[]" value="<?=formatFechaDMA($f['FechaHasta'])?>" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" <?=$disabled_detalles?> />
							</td>
							<td class="tagForm">Observaciones:</td>
							<td>
								<textarea name="cursos_Observaciones[]" style="width:200px; height:14px;" <?=$disabled_detalles?>><?=$f['Observaciones']?></textarea>
							</td>
						</tr>
					</table>
                </td>
            </tr>
            <?php
		}
		?>
    </tbody>
</table>
</div>
<input type="hidden" id="nro_cursos" value="<?=$nro_cursos?>" />
<input type="hidden" id="can_cursos" value="<?=$nro_cursos?>" />
</div>

<div id="tab5" style="display:none;">
<input type="hidden" id="sel_experiencia" />
<table class="tblBotones" style="width:<?=$_width?>px;">
	<thead>
    <tr>
    	<th class="divFormCaption" colspan="2">Informaci&oacute;n de la Experiencia Laboral</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td align="right">
            <input type="button" class="btLista" value="Insertar" onclick="insertar2(this, 'experiencia', 'modulo=ajax&accion=experiencia_insertar', 'rh_postulantes_ajax.php');" <?=$disabled_detalles?> />
            <input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'experiencia');" <?=$disabled_detalles?> />
        </td>
    </tr>
    </tbody>
</table>
<div style="overflow:scroll; height:200px; width:<?=$_width?>px; margin:auto;">
<table class="tblLista" style="width:100%;">
    <tbody id="lista_experiencia">
    	<?php
		$nro_experiencia = 0;
		$sql = "SELECT *
				FROM rh_postulantes_experiencia
				WHERE Postulante = '".$field['Postulante']."'
				ORDER BY Secuencia";
		$field_experiencia = getRecords($sql);
		foreach ($field_experiencia as $f) {
			$id = ++$nro_experiencia;
			?>
            <tr class="trListaBody" onclick="clk($(this), 'experiencia', 'experiencia_<?=$id?>');" id="experiencia_<?=$id?>">
                <th><?=$id?></th>
                <td>
                	<table border="1" width="100%">
					    <tr>
							<td class="tagForm">* Empresa:</td>
							<td>
								<input type="text" name="experiencia_Empresa[]" value="<?=$f['Empresa']?>" maxlength="255" style="width:175px;" <?=$disabled_detalles?> />
							</td>
							<td class="tagForm">* Desde:</td>
							<td>
								<input type="text" name="experiencia_FechaDesde[]" value="<?=formatFechaDMA($f['FechaDesde'])?>" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" <?=$disabled_detalles?> />
							</td>
							<td class="tagForm">* Tipo de Ente:</td>
							<td>
								<select name="experiencia_TipoEnte[]" style="width:175px;" <?=$disabled_detalles?>>
					            	<option value="">&nbsp;</option>
									<?=getMiscelaneos($f['TipoEnte'], "TIPOENTE", 0)?>
								</select>
							</td>
						</tr>
					    <tr>
							<td class="tagForm">CargoOcupado:</td>
							<td>
								<input type="text" name="experiencia_CargoOcupado[]" value="<?=$f['CargoOcupado']?>" maxlength="255" style="width:175px;" <?=$disabled_detalles?> />
							</td>
							<td class="tagForm">* Hasta:</td>
							<td>
								<input type="text" name="experiencia_FechaHasta[]" value="<?=formatFechaDMA($f['FechaHasta'])?>" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" <?=$disabled_detalles?> />
							</td>
							<td class="tagForm">Motivo de Cese:</td>
							<td>
								<select name="experiencia_MotivoCese[]" style="width:175px;" <?=$disabled_detalles?>>
					            	<option value="">&nbsp;</option>
									<?=getMiscelaneos($f['MotivoCese'], "MOTCESE", 0)?>
								</select>
							</td>
						</tr>
					    <tr>
							<td class="tagForm">Area de Experiencia:</td>
							<td>
								<select name="experiencia_AreaExperiencia[]" style="width:181px;" <?=$disabled_detalles?>>
					            	<option value="">&nbsp;</option>
									<?=getMiscelaneos($f['AreaExperiencia'], "AREAEXP", 0)?>
								</select>
							</td>
							<td class="tagForm">Sueldo:</td>
							<td>
								<input type="text" name="experiencia_Sueldo[]" value="<?=number_format($f['Sueldo'],2,',','.')?>" style="width:60px; text-align:right;" class="currency" <?=$disabled_detalles?> />
							</td>
							<td class="tagForm">Funciones:</td>
							<td>
								<textarea name="cursos_Funciones[]" style="width:200px; height:14px;" <?=$disabled_detalles?>><?=$f['Funciones']?></textarea>
							</td>
						</tr>
					</table>
                </td>
            </tr>
            <?php
		}
		?>
    </tbody>
</table>
</div>
<input type="hidden" id="nro_experiencia" value="<?=$nro_experiencia?>" />
<input type="hidden" id="can_experiencia" value="<?=$nro_experiencia?>" />

<input type="hidden" id="sel_referencias" />
<table class="tblBotones" style="width:<?=$_width?>px;">
	<thead>
    <tr>
    	<th class="divFormCaption" colspan="2">Datos de la Referencia Laboral</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td align="right">
            <input type="button" class="btLista" value="Insertar" onclick="insertar2(this, 'referencias', 'modulo=ajax&accion=referencias_insertar', 'rh_postulantes_ajax.php');" <?=$disabled_detalles?> />
            <input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'referencias');" <?=$disabled_detalles?> />
        </td>
    </tr>
    </tbody>
</table>
<div style="overflow:scroll; height:200px; width:<?=$_width?>px; margin:auto;">
<table class="tblLista" style="width:100%;">
    <tbody id="lista_referencias">
    	<?php
		$nro_referencias = 0;
		$sql = "SELECT *
				FROM rh_postulantes_referencias
				WHERE Postulante = '".$field['Postulante']."'
				ORDER BY Secuencia";
		$field_referencias = getRecords($sql);
		foreach ($field_referencias as $f) {
			$id = ++$nro_referencias;
			?>
            <tr class="trListaBody" onclick="clk($(this), 'referencias', 'referencias_<?=$id?>');" id="referencias_<?=$id?>">
                <th><?=$id?></th>
                <td>
                	<table border="1" width="100%">
					    <tr>
							<td class="tagForm" width="125">* Nombre:</td>
							<td>
								<input type="text" name="referencias_Nombre[]" value="<?=$f['Nombre']?>" maxlength="100" style="width:250px;" <?=$disabled_detalles?> />
							</td>
							<td class="tagForm" width="125">* Cargo:</td>
							<td>
								<input type="text" name="referencias_Cargo[]" value="<?=$f['Cargo']?>" maxlength="255" style="width:250px;" <?=$disabled_detalles?> />
							</td>
						</tr>
					    <tr>
							<td class="tagForm">* Empresa:</td>
							<td>
								<input type="text" name="referencias_Empresa[]" value="<?=$f['Empresa']?>" maxlength="255" style="width:250px;" <?=$disabled_detalles?> />
							</td>
							<td class="tagForm">* Tel&eacute;fono:</td>
							<td>
								<input type="text" name="referencias_Telefono[]" value="<?=$f['Telefono']?>" maxlength="15" style="width:100px;" class="phone" <?=$disabled_detalles?> />
							</td>
						</tr>
					    <tr>
							<td class="tagForm">* Direcci&oacute;n:</td>
							<td colspan="3">
								<textarea name="referencias_Direccion[]" style="width:95%; height:35px;" <?=$disabled_detalles?>><?=$f['Direccion']?></textarea>
							</td>
						</tr>
					</table>
                </td>
            </tr>
            <?php
		}
		?>
    </tbody>
</table>
</div>
<input type="hidden" id="nro_referencias" value="<?=$nro_referencias?>" />
<input type="hidden" id="can_referencias" value="<?=$nro_referencias?>" />
</div>

<div id="tab6" style="display:none;">
<input type="hidden" id="sel_documentos" />
<table class="tblBotones" style="width:<?=$_width?>px">
	<thead>
    <tr>
    	<th class="divFormCaption">Documentos</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td align="right">
            <input type="button" class="btLista" value="Insertar" onclick="insertar2(this, 'documentos', 'modulo=ajax&accion=documentos_insertar', 'rh_postulantes_ajax.php');" <?=$disabled_detalles?> />
            <input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'documentos');" <?=$disabled_detalles?> />
        </td>
    </tr>
    </tbody>
</table>
<div style="overflow:scroll; height:400px; max-width:<?=$_width?>px; margin:auto;">
<table class="tblLista" style="width:100%;">
	<thead>
    <tr>
        <th width="20">#</th>
        <th width="150">Documento</th>
        <th width="100">¿Present&oacute;?</th>
        <th align="left">Observaciones</th>
    </tr>
    </thead>
    
    <tbody id="lista_documentos">
    	<?php
		$nro_documentos = 0;
		$sql = "SELECT *
				FROM rh_postulantes_documentos
				WHERE Postulante = '".$field['Postulante']."'
				ORDER BY Secuencia";
		$field_documentos = getRecords($sql);
		foreach ($field_documentos as $f) {
			$id = ++$nro_documentos;
			?>
            <tr class="trListaBody" onclick="clk($(this), 'documentos', 'documentos_<?=$id?>');" id="documentos_<?=$id?>">
                <th><?=$id?></th>
                <td>
                    <select name="documentos_Documento[]" class="cell" <?=$disabled_detalles?>>
                    	<option value="">&nbsp;</option>
                        <?=getMiscelaneos($f['Documento'], "DOCUMENTOS")?>
                    </select>
                </td>
                <td align="center">
                    <input type="checkbox" name="documentos_FlagPresento[]" value="S" <?=chkFlag($f['FlagPresento'])?> <?=$disabled_detalles?> />
                </td>
                <td>
                    <textarea name="documentos_Observaciones[]" style="height:25px;" class="cell" <?=$disabled_detalles?>><?=htmlentities($f['Observaciones'])?></textarea>
                </td>
            </tr>
            <?php
		}
		?>
    </tbody>
</table>
</div>
<input type="hidden" id="nro_documentos" value="<?=$nro_documentos?>" />
<input type="hidden" id="can_documentos" value="<?=$nro_documentos?>" />
</div>

<div id="tab7" style="display:none;">
<input type="hidden" id="sel_cargos" />
<table class="tblBotones" style="width:<?=$_width?>px;">
	<thead>
    <tr>
    	<th class="divFormCaption">Datos del Cargo</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td align="right" class="gallery clearfix">
            <a id="a_cargos" href="../lib/listas/gehen.php?anz=lista_cargos&filtrar=default&ventana=listado_insertar_linea&detalle=cargos&modulo=ajax&accion=cargos_insertar&url=../../rh/rh_postulantes_ajax.php&iframe=true&width=900&height=400" rel="prettyPhoto[iframe5]" style="display:none;"></a>
            <input type="button" class="btLista" value="Insertar" onclick="$('#a_cargos').click();" <?=$disabled_detalles?> />
            <input type="button" class="btLista" value="Borrar" onclick="quitar(this, 'cargos');" <?=$disabled_detalles?> />
        </td>
    </tr>
    </tbody>
</table>
<div style="overflow:scroll; height:400px; width:<?=$_width?>px; margin:auto">
<table class="tblLista" style="width:1100px;">
	<thead>
    <tr>
        <th width="20">#</th>
        <th width="300">Cargo</th>
        <th align="left">Observaciones</th>
        <th width="300">Organismo</th>
    </tr>
    </thead>
    
    <tbody id="lista_cargos">
    	<?php
		$nro_cargos = 0;
		$sql = "SELECT
					pc.*,
					pt.DescripCargo
				FROM
					rh_postulantes_cargos pc
					INNER JOIN rh_puestos pt ON (pt.CodCargo = pc.CodCargo)
				WHERE pc.Postulante = '".$field['Postulante']."'
				ORDER BY DescripCargo";
		$field_cargos = getRecords($sql);
		foreach ($field_cargos as $f) {
			$id = $f['CodCargo'];
			++$nro_cargos;
			?>
            <tr class="trListaBody" onclick="clk($(this), 'cargos', 'cargos_<?=$id?>');" id="cargos_<?=$id?>">
                <th><?=$nro_cargos?></th>
                <td>
                	<input type="hidden" name="cargos_CodCargo[]" value="<?=$f['CodCargo']?>" />
                    <?=$f['DescripCargo']?>
                </td>
				<td>
					<textarea name="cargos_Comentario[]" class="cell" style="height:25px;" <?=$disabled_detalles?>><?=$f['Comentario']?></textarea>
				</td>
                <td>
                    <select name="cargos_CodOrganismo[]" class="cell" <?=$disabled_detalles?>>
                        <?=loadSelect2('mastorganismos','CodOrganismo','Organismo',$f['CodOrganismo'])?>
                    </select>
                </td>
            </tr>
            <?php
		}
		?>
    </tbody>
</table>
</div>
<input type="hidden" id="nro_cargos" value="<?=$nro_cargos?>" />
<input type="hidden" id="can_cargos" value="<?=$nro_cargos?>" />
</div>

<center>
<input type="submit" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" id="btSubmit" />
<input type="button" value="Cancelar" style="width:75px;" id="btCancelar" onclick="<?=$clkCancelar?>" />
</center>
</form>
<div style="width:<?=$_width?>px" class="divMsj">Campos Obligatorios *</div>

<script type="text/javascript" language="javascript">
	//	valido formulario
	function formulario(form, accion) {
		bloqueo(true);
		//	ajax
		$.ajax({
			type: "POST",
			url: "rh_postulantes_ajax.php",
			data: "modulo=formulario&accion="+accion+"&"+$('#frmentrada').serialize(),
			async: false,
			success: function(resp) {
				if (resp.trim() != '') cajaModal(resp.trim(), 'error', 400);
				else form.submit();
			}
		});
		return false;
	}
</script>