<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion".".sql", "w+");
##############################################################################/
##	Empleados (NUEVO, MODIFICAR, ELIMINAR, AJAX)
##############################################################################/
if ($modulo == "formulario") {
}
elseif ($modulo == "ajax") 
{
	if ($accion == 'validarCedula') 
	{
		if (!numeric($Ndocumento))
			die(json_encode(['status' => 'error', 'message' => 'Nro. Documento Formato Incorrecto (Solo se permiten valores numéricos)']));
		elseif (!is_unique('mastpersonas','Ndocumento',$Ndocumento))
		{
			$sql = "SELECT
						p.CodPersona,
						p.Apellido1,
						p.Apellido2,
						p.Nombres,
						p.NomCompleto,
						p.Busqueda,
						p.Sexo,
						p.Nacionalidad,
						p.Fnacimiento,
						p.CiudadNacimiento,
						p.Lnacimiento,
						p.EstadoCivil,
						p.FedoCivil,
						p.Direccion,
						p.Telefono1,
						p.Telefono2,
						p.Fax,
						p.CiudadDomicilio,
						p.TipoDocumento,
						p.Ndocumento,
						p.DocFiscal,
						p.TipoPersona,
						p.NomEmerg1,
						p.DirecEmerg1,
						p.TelefEmerg1,
						p.CelEmerg1,
						p.ParentEmerg1,
						p.NomEmerg2,
						p.DirecEmerg2,
						p.TelefEmerg2,
						p.CelEmerg2,
						p.ParentEmerg2,
						p.EsProveedor,
						p.EsCliente,
						p.EsEmpleado,
						p.EsOtros,
						p.SituacionDomicilio,
						p.Email,
						p.Foto,
						p.GrupoSanguineo,
						p.Observacion,
						p.TipoLicencia,
						p.Nlicencia,
						p.ExpiraLicencia,
						p.SiAuto,
						p.Estado AS EdoReg,
						e1.CodPais AS CodPaisNac,
						m1.CodEstado AS CodEstadoNac,
						c1.CodMunicipio AS CodMunicipioNac,
						e1.CodPais AS CodPaisDom,
						m1.CodEstado AS CodEstadoDom,
						c1.CodMunicipio AS CodMunicipioDom,
						e.CodEmpleado
					FROM
						mastpersonas p
						LEFT JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
						LEFT JOIN mastciudades c1 ON (c1.CodCiudad = p.CiudadNacimiento)
						LEFT JOIN mastmunicipios m1 ON (m1.CodMunicipio = c1.CodMunicipio)
						LEFT JOIN mastestados e1 ON (e1.CodEstado = m1.CodEstado)
						LEFT JOIN mastciudades c2 ON (c2.CodCiudad = p.CiudadDomicilio)
						LEFT JOIN mastmunicipios m2 ON (m2.CodMunicipio = c2.CodMunicipio)
						LEFT JOIN mastestados e2 ON (e2.CodEstado = m2.CodEstado)
					WHERE p.Ndocumento = '$Ndocumento'";
			$field = getRecord($sql);
			##	
			if (!empty($field['CodEmpleado']))
				die(json_encode(['status' => 'error', 'message' => 'Nro. Documento ya registrado']));
			else
			{
				$NomCiudad = getVar3("SELECT Ciudad FROM mastciudades WHERE CodCiudad = '$field[CiudadNacimiento]'");
				$NomEstado = getVar3("SELECT Estado FROM mastestados WHERE CodEstado = '$field[CodEstadoNac]'");
				$Lnacimiento = $NomCiudad . ', ' . $NomEstado;
				die(json_encode([
					'status' => 'update',
					'message' => 'Empleado ya existe como persona. Actualizar la información faltante para agregarlo como Empleado',
					'CodPersona' => $field['CodPersona'],
					'Apellido1' => $field['Apellido1'],
					'Apellido2' => $field['Apellido2'],
					'Nombres' => $field['Nombres'],
					'NomCompleto' => $field['NomCompleto'],
					'Busqueda' => $field['Busqueda'],
					'Sexo' => $field['Sexo'],
					'Nacionalidad' => $field['Nacionalidad'],
					'Fnacimiento' => formatFechaAMD($field['Fnacimiento']),
					'CiudadNacimiento' => $field['CiudadNacimiento'],
					'Lnacimiento' => $Lnacimiento,
					'EstadoCivil' => $field['EstadoCivil'],
					'FedoCivil' => formatFechaAMD($field['FedoCivil']),
					'Direccion' => $field['Direccion'],
					'Telefono1' => $field['Telefono1'],
					'Telefono2' => $field['Telefono2'],
					'Fax' => $field['Fax'],
					'CiudadDomicilio' => $field['CiudadDomicilio'],
					'TipoDocumento' => $field['TipoDocumento'],
					'Ndocumento' => $field['Ndocumento'],
					'DocFiscal' => $field['DocFiscal'],
					'TipoPersona' => $field['TipoPersona'],
					'NomEmerg1' => $field['NomEmerg1'],
					'DirecEmerg1' => $field['DirecEmerg1'],
					'TelefEmerg1' => $field['TelefEmerg1'],
					'CelEmerg1' => $field['CelEmerg1'],
					'ParentEmerg1' => $field['ParentEmerg1'],
					'NomEmerg2' => $field['NomEmerg2'],
					'DirecEmerg2' => $field['DirecEmerg2'],
					'TelefEmerg2' => $field['TelefEmerg2'],
					'CelEmerg2' => $field['CelEmerg2'],
					'ParentEmerg2' => $field['ParentEmerg2'],
					'SituacionDomicilio' => $field['SituacionDomicilio'],
					'Email' => $field['Email'],
					'Foto' => $field['Foto'],
					'GrupoSanguineo' => $field['GrupoSanguineo'],
					'Observacion' => $field['Observacion'],
					'TipoLicencia' => $field['TipoLicencia'],
					'Nlicencia' => $field['Nlicencia'],
					'ExpiraLicencia' => $field['ExpiraLicencia'],
					'SiAuto' => $field['SiAuto'],
					'EdoReg' => $field['EdoReg'],
					'CodPaisNac' => $field['CodPaisNac'],
					'CodEstadoNac' => $field['CodEstadoNac'],
					'CodMunicipioNac' => $field['CodMunicipioNac'],
					'CodPaisDom' => $field['CodPaisDom'],
					'CodEstadoDom' => $field['CodEstadoDom'],
					'CodMunicipioDom' => $field['CodMunicipioDom'],
				]));
			}
		}
	}
	elseif ($accion == 'validarRif') 
	{
		if (!valid_rif($DocFiscal))
			die(json_encode(['status' => 'error', 'message' => 'Doc. Fiscal Formato Incorrecto (Solo se permite el formato del rif sin guiones)']));
		elseif (!is_unique('mastpersonas','DocFiscal',$DocFiscal))
			die(json_encode(['status' => 'error', 'message' => 'Doc. Fiscal ya registrado']));
	}
}
