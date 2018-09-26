<?php
if ($opcion == "nuevo") {
	$field['TipoPersona'] = 'N';
	$field['CodPais'] = $_PARAMETRO['PAISDEFAULT'];
	$field['CodEstado'] = $_PARAMETRO['ESTADODEFAULT'];
	$field['CodMunicipio'] = $_PARAMETRO['MUNICIPIODEFAULT'];
	$field['CodCiudad'] = $_PARAMETRO['CIUDADDEFAULT'];
	$field['Estado'] = 'A';
	$field['EstadoEmpleado'] = 'A';
	$field['Nacionalidad'] = 'N';
	$field_cliente['TipoActividad'] = 'I';
	$field_cliente['Clasificacion'] = 'B';
	$field_cliente['CodTipoDocumento'] = 'BDV';
	$field_cliente['FormaFactura'] = 'FA';
	$field_cliente['TipoCliente'] = 'MI';
	$field_cliente['TipoVenta'] = 'NO';
	$field_cliente['CodTipoPago'] = 'CH';
	$field_cliente['CodFormaPago'] = '001';
	##
	$_titulo = "Nuevo Registro";
	$accion = "nuevo";
	$disabled_ver = "";
	$disabled_natural = "";
	$readonly_natural = "readonly";
	$display_submit = "";
	$display_tab2 = "display:none;";
	$display_tab3 = "display:none;";
	$display_tab4 = "display:none;";
	$label_submit = "Guardar";
	$focus = "Ndocumento";
}
elseif ($opcion == "modificar" || $opcion == "ver") {
	##	consulto datos generales
	$sql = "SELECT
				p.*,
				e.CodOrganismo,
				e.CodDependencia,
				e.CodCentroCosto,
				e.Estado AS EstadoEmpleado,
				e.Usuario,
				pv.CodTipoDocumento,
				pv.CodFormaPago,
				pv.CodTipoPago,
				pv.CodTipoServicio,
				pv.DiasPago,
				pv.RegistroPublico,
				pv.LicenciaMunicipal,
				pv.FechaConstitucion,
				pv.RepresentanteLegal,
				pv.ContactoVendedor,
				pv.FlagSNC,
				pv.NroInscripcionSNC,
				pv.FechaEmisionSNC,
				pv.FechaValidacionSNC,
				pv.Nacionalidad,
				c.CodMunicipio,
				m.CodEstado,
				et.CodPais
			FROM
				mastpersonas p
				INNER JOIN mastciudades c ON (c.CodCiudad = p.CiudadDomicilio)
				INNER JOIN mastmunicipios m ON (m.CodMunicipio = c.CodMunicipio)
				INNER JOIN mastestados et ON (et.CodEstado = m.CodEstado)
				LEFT JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
				LEFT JOIN mastproveedores pv ON (pv.CodProveedor = p.CodPersona)
			WHERE p.CodPersona = '".$sel_registros."'";
	$field = getRecord($sql);
	##	
	$sql = "SELECT * FROM mastcliente WHERE CodPersona = '$field[CodPersona]'";
	$field_cliente = getRecord($sql);
	##
	if ($opcion == "modificar") {
		$_titulo = "Modificar Registro";
		$accion = "modificar";
		$disabled_ver = "";
		$disabled_natural = (($field['TipoPersona']=='N')?'':'disabled');
		$readonly_natural = (($field['TipoPersona']=='N')?'readonly':'');
		$display_submit = "";
		$display_tab2 = (($field['EsEmpleado']=='S')?'':'display:none;');
		$display_tab3 = (($field['EsProveedor']=='S')?'':'display:none;');
		$display_tab4 = (($field['EsCliente']=='S')?'':'display:none;');
		$label_submit = "Modificar";
		$focus = (($field['TipoPersona']=='N')?'Apellido1':'NomCompleto');
	}
	##
	elseif ($opcion == "ver") {
		$_titulo = "Ver Registro";
		$accion = "";
		$disabled_ver = "disabled";
		$disabled_natural = "disabled";
		$readonly_natural = "disabled";
		$display_submit = "display:none;";
		$display_tab2 = (($field['EsEmpleado']=='S')?'':'display:none;');
		$display_tab3 = (($field['EsProveedor']=='S')?'':'display:none;');
		$display_tab4 = (($field['EsCliente']=='S')?'':'display:none;');
		$label_submit = "";
		$focus = "btCancelar";
	}
}
if (empty($action)) $action = "gehen.php?anz=personas_lista";
$clkCancelar = "document.getElementById('frmentrada').submit();";
//	------------------------------------
$_width = 850;
?>
<?php
if (empty($selector))
{
	?>
	<table width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td class="titulo"><?=$_titulo?></td>
			<td align="right"><a class="cerrar" href="#" onclick="<?=$clkCancelar?>">[cerrar]</a></td>
		</tr>
	</table><hr width="100%" color="#333333" />
	<?php
}
?>

<table cellpadding="0" cellspacing="0" style="width:<?=$_width?>px; margin:auto;">
    <tr>
        <td>
            <div class="header">
                <ul id="tab">
                    <!-- CSS Tabs -->
                    <li id="li1" onclick="currentTab('tab', this);" class="current"><a href="#" onclick="mostrarTab('tab', 1, 4);">Informaci&oacute;n General</a></li>
                    <li id="li2" onclick="currentTab('tab', this);" style=" <?=$display_tab2?>"><a href="#" onclick="mostrarTab('tab', 2, 4);">Empleado</a></li>
                    <li id="li3" onclick="currentTab('tab', this);" style=" <?=$display_tab3?>"><a href="#" onclick="mostrarTab('tab', 3, 4);">Proveedor</a></li>
                    <li id="li4" onclick="currentTab('tab', this);" style=" <?=$display_tab4?>"><a href="#" onclick="mostrarTab('tab', 4, 4);">Cliente</a></li>
                </ul>
            </div>
        </td>
    </tr>
</table>

<form name="frmentrada" id="frmentrada" action="<?=$action?>" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('personas_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
<input type="hidden" name="fBuscar" id="fBuscar" value="<?=$fBuscar?>" />
<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
<input type="hidden" name="fTipoPersona" id="fTipoPersona" value="<?=$fTipoPersona?>" />
<input type="hidden" name="fEsEmpleado" id="fEsEmpleado" value="<?=$fEsEmpleado?>" />
<input type="hidden" name="fEsProveedor" id="fEsProveedor" value="<?=$fEsProveedor?>" />
<input type="hidden" name="fEsCliente" id="fEsCliente" value="<?=$fEsCliente?>" />
<input type="hidden" name="fEsOtros" id="fEsOtros" value="<?=$fEsOtros?>" />

<input type="hidden" name="campo1" id="campo1" value="<?=$campo1?>" />
<input type="hidden" name="campo2" id="campo2" value="<?=$campo2?>" />
<input type="hidden" name="campo3" id="campo3" value="<?=$campo3?>" />
<input type="hidden" name="campo4" id="campo4" value="<?=$campo4?>" />
<input type="hidden" name="campo5" id="campo5" value="<?=$campo5?>" />
<input type="hidden" name="campo6" id="campo6" value="<?=$campo6?>" />
<input type="hidden" name="campo7" id="campo7" value="<?=$campo7?>" />
<input type="hidden" name="campo8" id="campo8" value="<?=$campo8?>" />
<input type="hidden" name="campo9" id="campo9" value="<?=$campo9?>" />
<input type="hidden" name="campo10" id="campo10" value="<?=$campo10?>" />
<input type="hidden" name="ventana" id="ventana" value="<?=$ventana?>" />
<input type="hidden" name="detalle" id="detalle" value="<?=$detalle?>" />
<input type="hidden" name="seldetalle" id="seldetalle" value="<?=$seldetalle?>" />
<input type="hidden" name="modulo_selector" id="modulo_selector" value="<?=$modulo_selector?>" />
<input type="hidden" name="accion_selector" id="accion_selector" value="<?=$accion_selector?>" />
<input type="hidden" name="url" id="url" value="<?=$url?>" />
<input type="hidden" name="FlagClasePersona" id="FlagClasePersona" value="<?=$FlagClasePersona?>" />
<input type="hidden" name="selector" id="selector" value="<?=$selector?>" />

<div id="tab1" style="display:block;">
	<table width="<?=$_width?>" class="tblForm">
		<tr>
	    	<td class="divFormCaption" colspan="4">Datos Generales</td>
	    </tr>
		<tr>
			<td class="tagForm" width="125">Persona:</td>
			<td>
	        	<input type="text" name="CodPersona" id="CodPersona" style="width:100px; font-weight:bold; font-size:12px;" value="<?=$field['CodPersona']?>" readonly />
			</td>
			<td class="tagForm" width="125">* Nombre B&uacute;squeda:</td>
			<td>
	        	<input type="text" name="Busqueda" id="Busqueda" style="width:250px;" maxlength="100" value="<?=htmlentities($field['Busqueda'])?>" <?=$readonly_natural?> />
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Clase de Persona:</td>
			<td>
	            <select name="TipoPersona" id="TipoPersona" style="width:105px;" <?=$disabled_ver?> onchange="setTipoPersona(this.value);">
	                <?=loadSelectGeneral("TIPO-PERSONA", $field['TipoPersona'], 0)?>
	            </select>
			</td>
			<td class="tagForm">* Nombre Completo:</td>
			<td>
	        	<input type="text" name="NomCompleto" id="NomCompleto" style="width:250px;" maxlength="100" value="<?=htmlentities($field['NomCompleto'])?>" <?=$readonly_natural?> onkeyup="$('#Busqueda').val(this.value);" />
			</td>
		</tr>
		<tr>
			<td class="tagForm">Tipo de Persona:</td>
			<td colspan="3">
	            <input type="checkbox" name="EsEmpleado" id="EsEmpleado" value="S" onchange="setTab(this.checked, '2')" <?=chkFlag($field['EsEmpleado'])?> <?=$disabled_ver?> /> Empleado
	            &nbsp; &nbsp; &nbsp; 
	            <input type="checkbox" name="EsProveedor" id="EsProveedor" value="S" onchange="setTab(this.checked, '3')" <?=chkFlag($field['EsProveedor'])?> <?=$disabled_ver?> onclick="setProveedor(this.checked);" /> Proveedor
	            &nbsp; &nbsp; &nbsp; 
	            <input type="checkbox" name="EsCliente" id="EsCliente" value="S" onchange="setTab(this.checked, '4')" <?=chkFlag($field['EsCliente'])?> <?=$disabled_ver?> /> Cliente
	            &nbsp; &nbsp; &nbsp; 
	            <input type="checkbox" name="EsOtros" id="EsOtros" value="S" <?=chkFlag($field['EsOtros'])?> <?=$disabled_ver?> /> Otro
			</td>
		</tr>
	</table>
	<table width="<?=$_width?>" class="tblForm">
		<tr>
	    	<td class="divFormCaption" colspan="6">Documentos de Identificación</td>
	    </tr>
		<tr>
			<td class="tagForm">* Principal:</td>
			<td>
	            <select name="TipoDocumento" id="TipoDocumento" style="width:150px;" <?=$disabled_ver?>>
	                <?=getMiscelaneos($field['TipoDocumento'], "DOCUMENTOS", 0)?>
	            </select>
			</td>
			<td class="tagForm">* Nro. Documento:</td>
			<td>
	        	<input type="text" name="Ndocumento" id="Ndocumento" style="width:150px;" maxlength="20" value="<?=$field['Ndocumento']?>" class="documento" onchange="validarCedula(this.value);" <?=$disabled_ver?> />
			</td>
			<td class="tagForm">* Doc. Fiscal:</td>
			<td>
	        	<input type="text" name="DocFiscal" id="DocFiscal" style="width:150px;" maxlength="20" value="<?=$field['DocFiscal']?>" class="documento" onchange="validarRif(this.value);" <?=$disabled_ver?> />
			</td>
		</tr>
	</table>
	<table width="<?=$_width?>" class="tblForm">
	    <tr>
	    	<td class="divFormCaption" colspan="4">Datos Persona <span id="sTipoPersonaT"><?=printValoresGeneral('TIPO-PERSONA',$field['TipoPersona'])?></span></td>
	    </tr>
		<tr>
			<td class="tagForm">* 1er. Apellido:</td>
			<td>
	        	<input type="text" name="Apellido1" id="Apellido1" style="width:250px;" maxlength="25" value="<?=htmlentities($field['Apellido1'])?>" <?=$disabled_natural?> onchange="setNomCompleto();" />
			</td>
			<td class="tagForm">2do. Apellido:</td>
			<td>
	        	<input type="text" name="Apellido2" id="Apellido2" style="width:250px;" maxlength="25" value="<?=htmlentities($field['Apellido2'])?>" <?=$disabled_natural?> onchange="setNomCompleto();" />
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Nombres:</td>
			<td>
	        	<input type="text" name="Nombres" id="Nombres" style="width:250px;" maxlength="50" value="<?=htmlentities($field['Nombres'])?>" <?=$disabled_natural?> onchange="setNomCompleto();" />
			</td>
			<td class="tagForm">* Sexo:</td>
			<td>
	            <select name="Sexo" id="Sexo" style="width:105px;" <?=$disabled_natural?>>
	            	<option value="">&nbsp;</option>
	                <?=loadSelectGeneral("SEXO", $field['Sexo'], 0)?>
	            </select>
			</td>
		</tr>
		<tr>
			<td class="tagForm"><span id="sTipoPersonaF"><?=($field['TipoPersona']=='N'?'* Fecha de Nac.':'Fecha de Const.')?></span>:</td>
			<td>
	        	<input type="text" name="Fnacimiento" id="Fnacimiento" value="<?=formatFechaDMA($field['Fnacimiento'])?>" style="width:75px;" maxlength="10" class="datepicker" onkeyup="setFechaDMA(this);" <?=$disabled_ver?> />
			</td>
			<td class="tagForm">* Estado Civil:</td>
			<td>
	            <select name="EstadoCivil" id="EstadoCivil" style="width:105px;" <?=$disabled_natural?>>
	            	<option value="">&nbsp;</option>
	                <?=getMiscelaneos($field['EstadoCivil'], "EDOCIVIL", 0)?>
	            </select>
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Direcci&oacute;n:</td>
			<td colspan="3">
				<textarea name="Direccion" id="Direccion" style="width:98%; height:50px;" <?=$disabled_ver?>><?=$field['Direccion']?></textarea>
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Pais:</td>
			<td>
	            <select name="CodPais" id="CodPais" style="width:250px;" <?=$disabled_ver?> onChange="loadSelect($('#CodEstado'), 'tabla=mastestados&CodPais='+$(this).val(), 1, ['CodMunicipio','CodCiudad']);">
	                <?=loadSelect2("mastpaises", "CodPais", "Pais", $field['CodPais'], 0);?>
	            </select>
			</td>
			<td class="tagForm">* Estado:</td>
			<td>
	            <select name="CodEstado" id="CodEstado" style="width:250px;" <?=$disabled_ver?> onChange="loadSelect($('#CodMunicipio'), 'tabla=mastmunicipios&CodEstado='+$(this).val(), 1, ['CodCiudad']);">
	                <?=loadSelectDependienteEstado($field['CodEstado'], $field['CodPais'], 0);?>
	            </select>
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Municipio:</td>
			<td>
	            <select name="CodMunicipio" id="CodMunicipio" style="width:250px;" <?=$disabled_ver?> onChange="loadSelect($('#CodCiudad'), 'tabla=mastciudades&CodMunicipio='+$(this).val(), 1);">
	                <?=loadSelect2("mastmunicipios", "CodMunicipio", "Municipio", $field['CodMunicipio'], 0, ["CodEstado"], [$field['CodEstado']]);?>
	            </select>
			</td>
			<td class="tagForm">* Ciudad:</td>
			<td>
	            <select name="CodCiudad" id="CodCiudad" style="width:250px;" <?=$disabled_ver?>>
	                <?=loadSelect2("mastciudades", "CodCiudad", "Ciudad", $field['CodCiudad'], 0, ["CodMunicipio"], [$field['CodMunicipio']]);?>
	            </select>
			</td>
		</tr>
		<tr>
			<td class="tagForm">E-mail:</td>
			<td>
	        	<input type="text" name="Email" id="Email" style="width:250px;" maxlength="100" value="<?=$field['Email']?>" <?=$disabled_ver?> />
			</td>
			<td class="tagForm">Telefono:</td>
			<td>
	        	<input type="text" name="Telefono1" id="Telefono1" value="<?=$field['Telefono1']?>" style="width:250px;" maxlength="15" <?=$disabled_ver?> />
			</td>
		</tr>
		<tr>
			<td class="tagForm">Celular:</td>
			<td>
	        	<input type="text" name="Telefono2" id="Telefono2" value="<?=$field['Telefono2']?>" style="width:250px;" maxlength="15" <?=$disabled_ver?> />
			</td>
			<td class="tagForm">Fax:</td>
			<td>
	        	<input type="text" name="Fax" id="Fax" value="<?=$field['Fax']?>" style="width:250px;" maxlength="15" <?=$disabled_ver?> />
			</td>
		</tr>
		<tr>
			<td class="tagForm">Nombre Emerg.:</td>
			<td>
	        	<input type="text" name="NomEmerg1" id="NomEmerg1" style="width:250px;" maxlength="100" value="<?=$field['NomEmerg1']?>" <?=$disabled_ver?> />
			</td>
			<td class="tagForm">Dir. Emerg.:</td>
			<td>
	        	<input type="text" name="DirecEmerg1" id="DirecEmerg1" style="width:250px;" maxlength="255" value="<?=$field['DirecEmerg1']?>" <?=$disabled_ver?> />
			</td>
		</tr>
	</table>
	<table width="<?=$_width?>" class="tblForm">
		<tr>
	    	<td class="divFormCaption" colspan="6">Información Bancaria</td>
	    </tr>
		<tr>
			<td class="tagForm">Banco:</td>
			<td>
	            <select name="CodBanco" id="CodBanco" style="width:175px;" <?=$disabled_ver?>>
	            	<option value="">&nbsp;</option>
	                <?=loadSelect("mastbancos", "CodBanco", "Banco", $field['CodBanco'], 0);?>
	            </select>
			</td>
			<td class="tagForm">Nro. Cuenta:</td>
			<td>
	        	<input type="text" name="Ncuenta" id="Ncuenta" style="width:175px;" maxlength="30" value="<?=$field['Ncuenta']?>" <?=$disabled_ver?> />
			</td>
			<td class="tagForm">Tipo Cuenta:</td>
			<td>
	            <select name="TipoCuenta" id="TipoCuenta" style="width:105px;" <?=$disabled_ver?>>
	            	<option value="">&nbsp;</option>
	                <?=getMiscelaneos($field['TipoCuenta'], "TIPOCTA", 0)?>
	            </select>
			</td>
		</tr>
	</table>
	<table width="<?=$_width?>" class="tblForm">
		<tr>
	    	<td class="divFormCaption" colspan="4">Datos de Auditor&iacute;a</td>
	    </tr>
		<tr>
			<td class="tagForm">Estado:</td>
			<td>
	            <input type="radio" name="Estado" id="Activo" value="A" <?=chkOpt($field['Estado'], "A")?> <?=$disabled_ver?> /> Activo
	            &nbsp; &nbsp;
	            <input type="radio" name="Estado" id="Inactivo" value="I" <?=chkOpt($field['Estado'], "I")?> <?=$disabled_ver?> /> Inactivo
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
	<input type="hidden" name="CodEmpleado" id="CodEmpleado" value="<?=$field['CodEmpleado']?>" />
	<table width="<?=$_width?>" class="tblForm">
		<tr>
	    	<td class="divFormCaption" colspan="2">Datos del Empleado</td>
	    </tr>
		<tr>
			<td class="tagForm" width="125">* Organismo:</td>
			<td>
	            <select name="CodOrganismo" id="CodOrganismo" style="width:300px;" <?=$disabled_ver?> onChange="loadSelect($('#CodDependencia'), 'tabla=mastdependencias&CodOrganismo='+$(this).val(), 1, ['CodCentroCosto']);">
	            	<option value="">&nbsp;</option>
	                <?=loadSelect2("mastorganismos", "CodOrganismo", "Organismo", $field['CodOrganismo'], 0);?>
	            </select>
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Dependencia:</td>
			<td>
	            <select name="CodDependencia" id="CodDependencia" style="width:300px;" <?=$disabled_ver?> onChange="loadSelect($('#CodCentroCosto'), 'tabla=ac_mastcentrocosto&CodDependencia='+$(this).val(), 1);">
	            	<option value="">&nbsp;</option>
	                <?=loadSelect2("mastdependencias", "CodDependencia", "Dependencia", $field['CodDependencia'], 0, ['CodOrganismo'], [$field['CodOrganismo']]);?>
	            </select>
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Centro de Costo:</td>
			<td>
	            <select name="CodCentroCosto" id="CodCentroCosto" style="width:300px;" <?=$disabled_ver?>>
	            	<option value="">&nbsp;</option>
	                <?=loadSelect2("ac_mastcentrocosto", "CodCentroCosto", "Descripcion", $field['CodCentroCosto'], 0, ['CodDependencia'], [$field['CodDependencia']]);?>
	            </select>
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Usuario:</td>
			<td>
	        	<input type="text" name="Usuario" id="Usuario" style="width:200px;" maxlength="20" value="<?=$field['Usuario']?>" <?=$disabled_ver?> />
			</td>
		</tr>
		<tr>
			<td class="tagForm">Estado:</td>
			<td>
	            <input type="radio" name="EstadoEmpleado" id="ActivoEmpleado" value="A" <?=chkOpt($field['EstadoEmpleado'], "A")?> <?=$disabled_ver?> /> Activo
	            &nbsp; &nbsp;
	            <input type="radio" name="EstadoEmpleado" id="InactivoEmpleado" value="I" <?=chkOpt($field['EstadoEmpleado'], "I")?> <?=$disabled_ver?> /> Inactivo
			</td>
		</tr>
	</table>
</div>

<div id="tab3" style="display:none;">
	<input type="hidden" name="CodProveedor" id="CodProveedor" value="<?=$field['CodProveedor']?>" />
	<table width="<?=$_width?>" class="tblForm">
		<tr>
	    	<td class="divFormCaption" colspan="4">Informaci&oacute;n para Pagos</td>
	    </tr>
		<tr>
			<td class="tagForm" width="175">* Documento del Proveedor:</td>
			<td>
	            <select name="CodTipoDocumento" id="CodTipoDocumento" style="width:225px;" <?=$disabled_ver?>>
	            	<option value="">&nbsp;</option>
	                <?=loadSelect2("ap_tipodocumento", "CodTipoDocumento", "Descripcion", $field['CodTipoDocumento']);?>
	            </select>
			</td>
			<td class="tagForm">* Documento de Pago:</td>
			<td>
	            <select name="CodTipoPago" id="CodTipoPago" style="width:225px;" <?=$disabled_ver?>>
	            	<option value="">&nbsp;</option>
	                <?=loadSelect2("masttipopago", "CodTipoPago", "TipoPago", $field['CodTipoPago']);?>
	            </select>
			</td>
		</tr>
		<tr>
			<td class="tagForm">* Tipo de Servicio:</td>
			<td>
	            <select name="CodTipoServicio" id="CodTipoServicio" style="width:225px;" <?=$disabled_ver?>>
	            	<option value="">&nbsp;</option>
	                <?=loadSelect2("masttiposervicio", "CodTipoServicio", "Descripcion", $field['CodTipoServicio']);?>
	            </select>
			</td>
			<td class="tagForm">* Forma de Pago:</td>
			<td>
	            <select name="CodFormaPago" id="CodFormaPago" style="width:225px;" <?=$disabled_ver?>>
	            	<option value="">&nbsp;</option>
	                <?=loadSelect2("mastformapago", "CodFormaPago", "Descripcion", $field['CodFormaPago']);?>
	            </select>
			</td>
		</tr>
		<tr>
	    	<td class="divFormCaption" colspan="4">Informaci&oacute;n Adicional</td>
	    </tr>
		<tr>
			<td class="tagForm">Nro. Dias para pago:</td>
			<td>
	        	<input type="text" name="DiasPago" id="DiasPago" style="width:50px;" maxlength="4" value="<?=$field['DiasPago']?>" <?=$disabled_ver?> />
			</td>
			<td class="tagForm">Registro P&uacute;blico:</td>
			<td>
	        	<input type="text" name="RegistroPublico" id="RegistroPublico" style="width:200px;" maxlength="20" value="<?=$field['RegistroPublico']?>" <?=$disabled_ver?> />
			</td>
		</tr>
		<tr>
			<td class="tagForm">Licencia Municipal:</td>
			<td>
	        	<input type="text" name="LicenciaMunicipal" id="LicenciaMunicipal" style="width:200px;" maxlength="20" value="<?=$field['LicenciaMunicipal']?>" <?=$disabled_ver?> />
			</td>
			<td class="tagForm">Fecha de Const.:</td>
			<td>
	        	<input type="text" name="FechaConstitucion" id="FechaConstitucion" value="<?=formatFechaDMA($field['FechaConstitucion'])?>" style="width:75px;" maxlength="10" class="datepicker" onkeyup="setFechaDMA(this);" <?=$disabled_ver?> />
			</td>
		</tr>
		<tr>
			<td class="tagForm">Representante Legal:</td>
			<td>
	        	<input type="text" name="RepresentanteLegal" id="RepresentanteLegal" style="width:200px;" maxlength="50" value="<?=$field['RepresentanteLegal']?>" <?=$disabled_ver?> />
			</td>
			<td class="tagForm">Contacto/Vendedor:</td>
			<td>
	        	<input type="text" name="ContactoVendedor" id="ContactoVendedor" style="width:200px;" maxlength="50" value="<?=$field['ContactoVendedor']?>" <?=$disabled_ver?> />
			</td>
		</tr>
		<tr>
	    	<td class="divFormCaption" colspan="4">Informaci&oacute;n SNC</td>
	    </tr>
		<tr>
			<td class="tagForm">Inscripci&oacute;n SNC:</td>
			<td>
	        	<input type="checkbox" name="FlagSNC" id="FlagSNC" value="S" <?=$FlagSNC?> <?=$disabled_ver?> />
			</td>
			<td class="tagForm">Nro. Insc. SNC:</td>
			<td>
	        	<input type="text" name="NroInscripcionSNC" id="NroInscripcionSNC" style="width:200px;" maxlength="20" value="<?=$field['NroInscripcionSNC']?>" <?=$disabled_ver?> />
			</td>
		</tr>
		<tr>
			<td class="tagForm">F. Emisi&oacute;n SNC:</td>
			<td>
	        	<input type="text" name="FechaEmisionSNC" id="FechaEmisionSNC" value="<?=formatFechaDMA($field['FechaEmisionSNC'])?>" style="width:75px;" maxlength="10" class="datepicker" onkeyup="setFechaDMA(this);" <?=$disabled_ver?> />
			</td>
			<td class="tagForm">F. Validaci&oacute;n SNC:</td>
			<td>
	        	<input type="text" name="FechaValidacionSNC" id="FechaValidacionSNC" value="<?=formatFechaDMA($field['FechaValidacionSNC'])?>" style="width:75px;" maxlength="10" class="datepicker" onkeyup="setFechaDMA(this);" <?=$disabled_ver?> />
			</td>
		</tr>
		<tr>
			<td class="tagForm">Nacionalidad:</td>
			<td>
	            <input type="radio" name="Nacionalidad" id="Nacional" value="N" <?=chkOpt($field['Nacionalidad'], "N")?> <?=$disabled_ver?> /> Nacional
	            &nbsp; &nbsp;
	            <input type="radio" name="Nacionalidad" id="Extranjero" value="E" <?=chkOpt($field['Nacionalidad'], "E")?> <?=$disabled_ver?> /> Extranjero
			</td>
		</tr>
	</table>
</div>

<div id="tab4" style="display:none;">
	<table style="width:100%; max-width:<?=$_width?>px;" class="tblForm">
		<tr>
	    	<td colspan="4" class="divFormCaption">Datos del Cliente</td>
	    </tr>
		<tr>
			<td class="tagForm">Tipo de Actividad:</td>
			<td>
	            <input type="radio" name="ClienteTipoActividad" id="Independiente" value="I" <?=chkOpt($field_cliente['TipoActividad'], "I");?> <?=$disabled_ver?> /> Independiente
	            &nbsp; &nbsp;
	            <input type="radio" name="ClienteTipoActividad" id="Dependiente" value="D" <?=chkOpt($field_cliente['TipoActividad'], "D");?> <?=$disabled_ver?> /> Dependiente
			</td>
			<th colspan="2">Valores por Defecto para Comercial</th>
		</tr>
	    <tr>
			<td class="tagForm">* Forma de Pago:</td>
			<td>
				<select name="ClienteCodFormaPago" id="ClienteCodFormaPago" style="width:200px;" <?=$disabled_ver?>>
					<option value="">&nbsp;</option>
					<?=loadSelect2('mastformapago','CodFormaPago','Descripcion',$field_cliente['CodFormaPago'],0)?>
				</select>
			</td>
			<td class="tagForm">* Tipo de Documento:</td>
			<td>
				<select name="ClienteCodTipoDocumento" id="ClienteCodTipoDocumento" style="width:200px;" <?=$disabled_ver?>>
					<option value="">&nbsp;</option>
					<?=loadSelect2('co_tipodocumento','CodTipoDocumento','Descripcion',$field_cliente['CodTipoDocumento'],0)?>
				</select>
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Tipo de Pago:</td>
			<td>
				<select name="ClienteCodTipoPago" id="ClienteCodTipoPago" style="width:200px;" <?=$disabled_ver?>>
					<option value="">&nbsp;</option>
					<?=loadSelect2('co_tipopago','CodTipoPago','Descripcion',$field_cliente['CodTipoPago'],0)?>
				</select>
			</td>
			<td class="tagForm">* Forma de Facturaci&oacute;n:</td>
			<td>
				<select name="ClienteFormaFactura" id="ClienteFormaFactura" style="width:200px;" <?=$disabled_ver?>>
					<option value="">&nbsp;</option>
					<?=getMiscelaneos($field_cliente['FormaFactura'], "FORMAFACT", 0)?>
				</select>
			</td>
		</tr>
	    <tr>
			<td class="tagForm">Persona Contacto:</td>
			<td>
	        	<input type="text" name="ClientePersonaContacto" id="ClientePersonaContacto" value="<?=$field_cliente['PersonaContacto']?>" style="width:200px;" maxlength="255" <?=$disabled_ver?> />
			</td>
			<td class="tagForm">* Tipo de Cliente:</td>
			<td>
				<select name="ClienteTipoCliente" id="ClienteTipoCliente" style="width:200px;" <?=$disabled_ver?>>
					<option value="">&nbsp;</option>
					<?=getMiscelaneos($field_cliente['TipoCliente'], "TIPOCLIEN", 0)?>
				</select>
			</td>
		</tr>
	    <tr>
			<td class="tagForm">Cargo (Contacto):</td>
			<td>
	        	<input type="text" name="ClienteCargoContacto" id="ClienteCargoContacto" value="<?=$field_cliente['CargoContacto']?>" style="width:200px;" maxlength="255" <?=$disabled_ver?> />
			</td>
			<td class="tagForm">* Tipo de Venta:</td>
			<td>
				<select name="ClienteTipoVenta" id="ClienteTipoVenta" style="width:200px;" <?=$disabled_ver?>>
					<option value="">&nbsp;</option>
					<?=getMiscelaneos($field_cliente['TipoVenta'], "TIPOVENTA", 0)?>
				</select>
			</td>
		</tr>
	    <tr>
			<th colspan="2">Informaci&oacute;n Crediticia</th>
			<td class="tagForm">* Ruta de Despacho:</td>
			<td>
				<select name="ClienteCodRutaDespacho" id="ClienteCodRutaDespacho" style="width:200px;" <?=$disabled_ver?>>
					<option value="">&nbsp;</option>
					<?=loadSelect2('co_rutadespacho','CodRutaDespacho','Descripcion',$field_cliente['CodRutaDespacho'],0)?>
				</select>
			</td>
		</tr>
	    <tr>
			<td class="tagForm">* Clasificaci&oacute;n:</td>
			<td>
				<select name="ClienteClasificacion" id="ClienteClasificacion" style="width:200px;" <?=$disabled_ver?>>
					<?=loadSelectGeneral("cliente-clasificacion", $f['Clasificacion'])?>
				</select>
			</td>
			<td class="tagForm">* Linea de Cr&eacute;dito:</td>
			<td>
				<input type="text" name="ClienteMontoLineaCredito" id="ClienteMontoLineaCredito" value="<?=number_format($field_cliente['MontoLineaCredito'],2,',','.')?>" style="width:125px; text-align: right;" class="currency" <?=$disabled_ver?> />
				<select name="ClienteLineaCreditoMoneda" id="ClienteLineaCreditoMoneda" style="width:71px;" <?=$disabled_ver?>>
					<?=loadSelectGeneral("monedas", $field_cliente['LineaCreditoMoneda'])?>
				</select>
			</td>
		</tr>
	    <tr>
			<th colspan="2">Datos Adicionales del Cliente</th>
			<td class="tagForm">* Fecha Vencimiento:</td>
			<td>
				<input type="text" name="ClienteFecVencLineaCredito" id="ClienteFecVencLineaCredito" value="<?=formatFechaDMA($field_cliente['FecVencLineaCredito'])?>" maxlength="10" style="width:125px;" class="datepicker" onkeyup="setFechaDMA(this);" <?=$disabled_ver?> />
			</td>
		</tr>
	    <tr>
			<td class="tagForm">&nbsp;</td>
			<td>
	            <input type="checkbox" name="ClienteFlagSuspendido" id="ClienteFlagSuspendido" value="S" <?=chkOpt($field_cliente['FlagSuspendido'], "S");?> <?=$disabled_ver?> /> Suspendido
			</td>
			<td class="tagForm">Vendedor:</td>
			<td>
				<select name="ClienteCodVendedor" id="ClienteCodVendedor" style="width:205px;" <?=$disabled_ver?>>
					<option value="">&nbsp;</option>
					<?=vendedores($field_cliente['CodVendedor'])?>
				</select>
			</td>
		</tr>
	</table>
</div>

<center>
<input type="submit" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" id="btSubmit" />
<input type="button" value="Cancelar" style="width:75px;" id="btCancelar" onclick="<?=$clkCancelar?>" />
</center>
</form>
<div style="width:<?=$_width?>px" class="divMsj">Campos Obligatorios *</div>

<script type="text/javascript" language="javascript">
	function setTipoPersona(TipoPersona) {
		if (TipoPersona == 'N') {
			$('#sTipoPersonaT').html('Natural');
			$('#sTipoPersonaF').html('* Fecha de Nac.');
			$('#Apellido1').attr('disabled',false);
			$('#Apellido2').attr('disabled',false);
			$('#Nombres').attr('disabled',false);
			$('#Sexo').attr('disabled',false);
			$('#EstadoCivil').attr('disabled',false);
			$('#Fnacimiento').attr('disabled',false);
			$('#Busqueda').attr('readonly',true);
			$('#NomCompleto').attr('readonly',true);
		}
		else if (TipoPersona == 'J') {
			$('#sTipoPersonaT').html('Jur&iacute;dica');
			$('#sTipoPersonaF').html('Fecha de Const.');
			$('#Apellido1').attr('disabled',true).val('');
			$('#Apellido2').attr('disabled',true).val('');
			$('#Nombres').attr('disabled',true).val('');
			$('#Sexo').attr('disabled',true).val('');
			$('#EstadoCivil').attr('disabled',true).val('');
			$('#Fnacimiento').attr('disabled',true).val('');
			$('#Busqueda').attr('readonly',false);
			$('#NomCompleto').attr('readonly',false).focus();
		}
		$('#Ndocumento').val('');
		$('#DocFiscal').val('');
	}
	function setTab(checked, tab) {
		if (checked) $('#li'+tab).css('display','block');
		else $('#li'+tab).css('display','none');
	}
    function setNomCompleto() {
        var NomCompleto = $('#Nombres').val().trim() + ' ' + $('#Apellido1').val().trim() + ' ' + $('#Apellido2').val().trim();
        $('#NomCompleto').val(NomCompleto);
        $('#Busqueda').val(NomCompleto);
    }
    function validarCedula(Ndocumento) {
    	if (Ndocumento) {
	        $.post('personas_ajax.php', {Ndocumento:Ndocumento, TipoPersona:$('#TipoPersona').val(), CodPersona:$('#CodPersona').val(), modulo:'ajax', accion:'validarCedula'}, function(data) {
	            if (data['status'] == 'error') {
	            	cajaModal(data['message']);
	            	$('#Ndocumento').val('').focus();
	            }
	        }, 'json');
    	}
    }
    function validarRif(DocFiscal) {
    	if (DocFiscal) {
	        $.post('personas_ajax.php', {DocFiscal:DocFiscal, TipoPersona:$('#TipoPersona').val(), CodPersona:$('#CodPersona').val(), modulo:'ajax', accion:'validarRif'}, function(data) {
	            if (data['status'] == 'error') {
	            	cajaModal(data['message']);
	            	$('#DocFiscal').val('').focus();
	            }
	        }, 'json');
    	}
    }
    function setProveedor(checked) {
    	if (checked) {
	    	$('#TipoPersona').val('J');
	    	setTipoPersona('J');
	    	$('#TipoDocumento').val('06');
    	}
    	else {
	    	$('#TipoPersona').val('N');
	    	setTipoPersona('N');
	    	$('#TipoDocumento').val('01');
    	}
    }
</script>
