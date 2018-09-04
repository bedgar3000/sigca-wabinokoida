<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion-".".sql", "w+");
##############################################################################/
if ($modulo == "formulario") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		##	-----------------
		$Fnacimiento = formatFechaAMD($Fnacimiento);
		$ExpiraLicencia = formatFechaAMD($ExpiraLicencia);
		$NomCompleto = "$Nombres $Apellido1 $Apellido2";
		##	valido
		if (!trim($Ndocumento) || !trim($Nombres) || !trim($Apellido1) || !trim($CodCiudad)) die("Debe llenar los campos (*) obligatorios.");
		else {
			$sql = "SELECT * FROM lg_choferes WHERE CodPersona = '$CodPersona'";
			$field_valido = getRecords($sql);
			if (count($field_valido)) die('Persona ya ingresada como chofer');
		}
		##	codigo
		$CodChofer = codigo('lg_choferes','CodChofer',6);
		if (empty($CodPersona)) $CodPersona = codigo('mastpersonas','CodPersona',6);
		##	persona
		$sql = "REPLACE INTO mastpersonas
				SET
					CodPersona = '$CodPersona',
					TipoPersona = 'N',
					EsEmpleado = 'N',
					EsProveedor = 'N',
					EsCliente = 'N',
					EsOtros = 'S',
					Busqueda = '$NomCompleto',
					NomCompleto = '$NomCompleto',
					Apellido1 = '$Apellido1',
					Apellido2 = '$Apellido2',
					Nombres = '$Nombres',
					Sexo = '$Sexo',
					EstadoCivil = '$EstadoCivil',
					Fnacimiento = '$Fnacimiento',
					Direccion = '$Direccion',
					CiudadNacimiento = '$CodCiudad',
					CiudadDomicilio = '$CodCiudad',
					Lnacimiento = '',
					Email = '',
					Telefono1 = '$Telefono1',
					Telefono2 = '$Telefono2',
					Fax = '',
					NomEmerg1 = '',
					DirecEmerg1 = '',
					TipoDocumento = '01',
					DocFiscal = '$Ndocumento',
					Ndocumento = '$Ndocumento',
					TipoLicencia = '$TipoLicencia',
					Nlicencia = '$Nlicencia',
					ExpiraLicencia = '$ExpiraLicencia',
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()";
		execute($sql);
		##	inserto
		$sql = "INSERT INTO lg_choferes
				SET
					CodChofer = '$CodChofer',
					CodPersona = '$CodPersona',
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	modificar
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		##	-----------------
		$ExpiraLicencia = formatFechaAMD($ExpiraLicencia);
		$NomCompleto = "$Nombres $Apellido1 $Apellido2";
		##	valido
		if (!trim($Nombres) || !trim($Apellido1) || !trim($CodCiudad)) die("Debe llenar los campos (*) obligatorios.");
		##	persona
		$sql = "REPLACE INTO mastpersonas
				SET
					CodPersona = '$CodPersona',
					TipoPersona = 'N',
					EsEmpleado = 'N',
					EsProveedor = 'N',
					EsCliente = 'N',
					EsOtros = 'S',
					Busqueda = '$NomCompleto',
					NomCompleto = '$NomCompleto',
					Apellido1 = '$Apellido1',
					Apellido2 = '$Apellido2',
					Nombres = '$Nombres',
					Sexo = 'M',
					EstadoCivil = '$EstadoCivil',
					Fnacimiento = '',
					Direccion = '$Direccion',
					CiudadNacimiento = '$CodCiudad',
					CiudadDomicilio = '$CodCiudad',
					Lnacimiento = '',
					Email = '',
					Telefono1 = '$Telefono1',
					Telefono2 = '$Telefono2',
					Fax = '',
					NomEmerg1 = '',
					DirecEmerg1 = '',
					TipoDocumento = '01',
					DocFiscal = '$Ndocumento',
					Ndocumento = '$Ndocumento',
					TipoLicencia = '$TipoLicencia',
					Nlicencia = '$Nlicencia',
					ExpiraLicencia = '$ExpiraLicencia',
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()";
		execute($sql);
		##	-----------------
		mysql_query("COMMIT");
	}
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "DELETE FROM lg_choferes WHERE CodChofer = '$registro'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
elseif ($modulo == "ajax") {
	if ($accion == 'get_persona') {
		$sql = "SELECT
					p.*,
					em.CodEmpleado,
					em.Usuario,
					em.Fingreso,
					m.CodMunicipio,
					e.CodEstado,
					pi.CodPais,
					cl.CodTipoDocumento,
					cl.FormaFactura,
					cl.TipoVenta,
					cl.CodFormaPago,
					cl.CodRutaDespacho,
					cl.CodVendedor AS CodClienteVendedor,
					md1.Descripcion AS NomFormaFactura,
					rd.CodComunidad,
					cm.Descripcion AS Comunidad,
					v.CodPersona AS CodPersonaVendedor
				FROM
					mastpersonas p
					LEFT JOIN mastempleado em On (em.CodPersona = p.CodPersona)
					LEFT JOIN mastcliente cl ON (cl.CodPersona = p.CodPersona)
					LEFT JOIN mastciudades c ON (c.CodCiudad = p.CiudadDomicilio)
					LEFT JOIN mastmunicipios m ON (m.CodMunicipio = c.CodMunicipio)
					LEFT JOIN mastestados e ON (e.CodEstado = m.CodEstado)
					LEFT JOIN mastpaises pi ON (pi.CodPais = e.CodPais)
					LEFT JOIN mastmiscelaneosdet md1 ON (
						md1.CodDetalle = cl.FormaFactura
						AND md1.CodMaestro = 'FORMAFACT'
					)
					LEFT JOIN co_rutadespacho rd ON rd.CodRutaDespacho = cl.CodRutaDespacho
					LEFT JOIN mastcomunidades cm ON cm.CodComunidad = rd.CodComunidad
					LEFT JOIN co_vendedor v ON v.CodPersona = p.CodPersona
				WHERE p.Ndocumento = '$Ndocumento'";
		$field = getRecord($sql);

		if (empty($field['CodPersona'])) 
		{			
			$field['CodPais'] = $_PARAMETRO['PAISDEFAULT'];
			$field['CodEstado'] = $_PARAMETRO['ESTADODEFAULT'];
			$field['CodMunicipio'] = $_PARAMETRO['MUNICIPIODEFAULT'];
			$field['CiudadDomicilio'] = $_PARAMETRO['CIUDADDEFAULT'];
		}

		echo $field['CodPersona'] . '|' . $field['Apellido1'] . '|' . $field['Apellido2'] . '|' . $field['Nombres'] . '|' . $field['EstadoCivil'] . '|' . $field['Telefono1'] . '|' . $field['Telefono2'] . '|' . $field['Direccion'] . '|' . $field['CiudadDomicilio'] . '|' . $field['CodMunicipio'] . '|' . $field['CodEstado'] . '|' . $field['CodPais'] . '|' . $field['TipoLicencia'] . '|' . $field['Nlicencia'] . '|' . formatFechaDMA($field['ExpiraLicencia']) . '|' . formatFechaDMA($field['Fnacimiento']) . '|' . $field['Sexo'];

		?>|<option value="">&nbsp;</option><?php
		loadSelect2('mastestados','CodEstado','Estado',$field['CiudadDomicilio'],0,['CodPais'],[$field['CodPais']]);

		?>|<option value="">&nbsp;</option><?php
		loadSelect2('mastmunicipios','CodMunicipio','Municipio',$field['CodMunicipio'],0,['CodEstado'],[$field['CodEstado']]);

		?>|<option value="">&nbsp;</option><?php
		loadSelect2('mastciudades','CodCiudad','Ciudad',$field['CiudadDomicilio'],0,['CodMunicipio'],[$field['CodMunicipio']]);
	}
}
?>