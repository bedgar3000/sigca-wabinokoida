<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion".".sql", "w+");
##############################################################################/
##	Personas (NUEVO, MODIFICAR, ELIMINAR, AJAX)
##############################################################################/
if ($modulo == "formulario") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		##	-----------------
		$Fnacimiento = formatFechaAMD($Fnacimiento);
		$EsEmpleado = ($EsEmpleado?'S':'N');
		$EsProveedor = ($EsProveedor?'S':'N');
		$EsCliente = ($EsCliente?'S':'N');
		$EsOtros = ($EsOtros?'S':'N');
		$NomCiudad = getVar3("SELECT Ciudad FROM mastciudades WHERE CodCiudad = '$CodCiudad'");
		$NomEstado = getVar3("SELECT Estado FROM mastestados WHERE CodEstado = '$CodEstado'");
		$Lnacimiento = $NomCiudad . ', ' . $NomEstado;
		##	valido
		if (!trim($TipoPersona) || !trim($Busqueda) || !trim($NomCompleto) || !trim($CodCiudad) || !trim($TipoDocumento) || !trim($Ndocumento) || !trim($DocFiscal) || !trim($Direccion)) die("Debe llenar los campos (*) obligatorios.");
		elseif ($TipoPersona == 'N' && (!trim($Apellido1) || !trim($Nombres) || !trim($Sexo) || !trim($EstadoCivil) || !trim($Fnacimiento))) die("Debe llenar los campos (*) obligatorios.");
		elseif ($TipoPersona == 'N' && !numeric($Ndocumento)) die("Nro. Documento Formato Incorrecto");
		elseif ($TipoPersona == 'J' && !valid_rif($Ndocumento)) die("Nro. Documento Formato Incorrecto");
		elseif (!valid_rif($DocFiscal)) die("Doc. Fiscal Formato Incorrecto");
		elseif (!is_unique('mastpersonas','Ndocumento',$Ndocumento)) die('Nro. Documento ya registrado');
		elseif (!is_unique('mastpersonas','DocFiscal',$DocFiscal)) die('Doc. Fiscal ya registrado');
		##	codigo
		$CodPersona = codigo('mastpersonas','CodPersona',6);
		##	inserto
		$sql = "INSERT INTO mastpersonas
				SET
					CodPersona = '$CodPersona',
					TipoPersona = '$TipoPersona',
					EsEmpleado = '$EsEmpleado',
					EsProveedor = '$EsProveedor',
					EsCliente = '$EsCliente',
					EsOtros = '$EsOtros',
					Busqueda = '$Busqueda',
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
					Lnacimiento = '$Lnacimiento',
					Email = '$Email',
					Telefono1 = '$Telefono1',
					Telefono2 = '$Telefono2',
					Fax = '$Fax',
					NomEmerg1 = '$NomEmerg1',
					DirecEmerg1 = '$DirecEmerg1',
					TipoDocumento = '$TipoDocumento',
					DocFiscal = '$DocFiscal',
					Ndocumento = '$Ndocumento',
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()";
		execute($sql);
		##	banco 
		execute("DELETE FROM bancopersona WHERE CodPersona = '$CodPersona' AND FlagPrincipal='S'");
		$CodSecuencia = codigo('bancopersona','CodSecuencia',6,['CodPersona'],[$CodPersona]);
		if ($CodBanco)
		{
			$sql = "INSERT INTO bancopersona
				SET
					CodSecuencia = '$CodSecuencia',
					CodBanco = '$CodBanco',
					CodPersona = '$CodPersona',
					TipoCuenta = '$TipoCuenta',
					Ncuenta = '$Ncuenta',
					Aportes = 'PQ',
					FlagPrincipal = 'S',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()";
			execute($sql);
		}
		//	EMPLEADO
		if ($EsEmpleado == 'S') {
			##	valido
			if (!trim($CodOrganismo) || !trim($CodDependencia) || !trim($CodCentroCosto)|| !trim($Usuario)) die("Debe llenar los campos (*) obligatorios.");
			else {
				##	Usuario
				$sql = "SELECT COUNT(*) FROM mastempleado WHERE Usuario = '".$Usuario."'";
				if (getVar3($sql)) die('Usuario ya registrado');
			}
			##	codigo
			$CodEmpleado = codigo('mastempleado','CodEmpleado',6);
			##	inserto
			$sql = "INSERT INTO mastempleado
					SET
						CodPersona = '$CodPersona',
						CodEmpleado = '$CodEmpleado',
						CodOrganismo = '$CodOrganismo',
						CodDependencia = '$CodDependencia',
						CodCentroCosto = '$CodCentroCosto',
						Usuario = '$Usuario',
						Estado = '$EstadoEmpleado',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()";
			execute($sql);
		}
		//	PROVEEDOR
		if ($EsProveedor == 'S') {
			$FechaConstitucion = formatFechaAMD($FechaConstitucion);
			$FechaEmisionSNC = formatFechaAMD($FechaEmisionSNC);
			$FechaValidacionSNC = formatFechaAMD($FechaValidacionSNC);
			$FlagSNC = ($FlagSNC?'S':'N');
			##	valido
			if (!trim($CodTipoDocumento) || !trim($CodTipoPago) || !trim($CodTipoServicio) || !trim($CodFormaPago)) die("Debe llenar los campos (*) obligatorios.");
			##	codigo
			$NroProveedor = codigo('mastproveedores','NroProveedor',6);
			##	inserto
			$sql = "INSERT INTO mastproveedores
					SET
						CodProveedor = '$CodPersona',
						NroProveedor = '$NroProveedor',
						CodTipoDocumento = '$CodTipoDocumento',
						CodTipoPago = '$CodTipoPago',
						CodFormaPago = '$CodFormaPago',
						CodTipoServicio = '$CodTipoServicio',
						DiasPago = '$DiasPago',
						RegistroPublico = '$RegistroPublico',
						LicenciaMunicipal = '$LicenciaMunicipal',
						FechaConstitucion = '$FechaConstitucion',
						RepresentanteLegal = '$RepresentanteLegal',
						ContactoVendedor = '$ContactoVendedor',
						FlagSNC = '$FlagSNC',
						NroInscripcionSNC = '$NroInscripcionSNC',
						FechaEmisionSNC = '$FechaEmisionSNC',
						FechaValidacionSNC = '$FechaValidacionSNC',
						Nacionalidad = '$Nacionalidad',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()";
			execute($sql);
		}
		//	CLIENTE
		if ($EsCliente == 'S') {
			$ClienteFecVencLineaCredito = formatFechaAMD($ClienteFecVencLineaCredito);
			$ClienteMontoLineaCredito = getNumero($ClienteMontoLineaCredito);
			$iCodVendedor = (!empty($ClienteCodVendedor)?"CodVendedor = '$ClienteCodVendedor',":'');
			##	valido
			if (!trim($ClienteCodFormaPago) || !trim($ClienteClasificacion) || !trim($ClienteCodTipoDocumento) || !trim($ClienteFormaFactura) || !trim($ClienteTipoCliente) || !trim($ClienteTipoVenta) || !trim($ClienteCodTipoPago) || !trim($ClienteCodRutaDespacho) || !trim($ClienteFecVencLineaCredito)) die("Debe llenar los campos (*) obligatorios.");
			##	codigo
			$CodCliente = codigo('mastcliente','CodCliente',6);
			##	inserto
			$sql = "INSERT INTO mastcliente
					SET
						CodCliente = '$CodCliente',
						CodPersona = '$CodPersona',
						CodFormaPago = '$ClienteCodFormaPago',
						CodTipoPago = '$ClienteCodTipoPago',
						CodTipoDocumento = '$ClienteCodTipoDocumento',
						$iCodVendedor
						CodRutaDespacho = '$ClienteCodRutaDespacho',
						TipoActividad = '$ClienteTipoActividad',
						TipoCliente = '$ClienteTipoCliente',
						FormaFactura = '$ClienteFormaFactura',
						TipoVenta = '$ClienteTipoVenta',
						Clasificacion = '$ClienteClasificacion',
						LineaCreditoMoneda = '$ClienteLineaCreditoMoneda',
						MontoLineaCredito = '$ClienteMontoLineaCredito',
						FecVencLineaCredito = '$ClienteFecVencLineaCredito',
						PersonaContacto = '$ClientePersonaContacto',
						CargoContacto = '$ClienteCargoContacto',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	-----------------
		mysql_query("COMMIT");
	}
	//	modificar
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		##	-----------------
		$Fnacimiento = formatFechaAMD($Fnacimiento);
		$EsEmpleado = ($EsEmpleado?'S':'N');
		$EsProveedor = ($EsProveedor?'S':'N');
		$EsCliente = ($EsCliente?'S':'N');
		$EsOtros = ($EsOtros?'S':'N');
		$NomCiudad = getVar3("SELECT Ciudad FROM mastciudades WHERE CodCiudad = '$CodCiudad'");
		$NomEstado = getVar3("SELECT Estado FROM mastestados WHERE CodEstado = '$CodEstado'");
		$Lnacimiento = $NomCiudad . ', ' . $NomEstado;
		##	valido
		if (!trim($TipoPersona) || !trim($Busqueda) || !trim($NomCompleto) || !trim($CodCiudad) || !trim($TipoDocumento) || !trim($Ndocumento) || !trim($DocFiscal) || !trim($Direccion)) die("Debe llenar los campos (*) obligatorios.");
		elseif ($TipoPersona == 'N' && (!trim($Apellido1) || !trim($Nombres) || !trim($Sexo) || !trim($EstadoCivil) || !trim($Fnacimiento))) die("Debe llenar los campos (*) obligatorios.");
		elseif ($TipoPersona == 'N' && !numeric($Ndocumento)) die("Nro. Documento Formato Incorrecto");
		elseif ($TipoPersona == 'J' && !valid_rif($Ndocumento)) die("Nro. Documento Formato Incorrecto");
		elseif (!valid_rif($DocFiscal)) die("Doc. Fiscal Formato Incorrecto");
		elseif (!is_unique('mastpersonas','Ndocumento',$Ndocumento,'CodPersona',$CodPersona))
			die('Nro. Documento ya registrado');
		elseif (!is_unique('mastpersonas','DocFiscal',$DocFiscal,'CodPersona',$CodPersona))
			die('Doc. Fiscal ya registrado');
		##	inserto
		$sql = "UPDATE mastpersonas
				SET
					TipoPersona = '$TipoPersona',
					EsEmpleado = '$EsEmpleado',
					EsProveedor = '$EsProveedor',
					EsCliente = '$EsCliente',
					EsOtros = '$EsOtros',
					Busqueda = '$Busqueda',
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
					Lnacimiento = '$Lnacimiento',
					Email = '$Email',
					Telefono1 = '$Telefono1',
					Telefono2 = '$Telefono2',
					Fax = '$Fax',
					NomEmerg1 = '$NomEmerg1',
					DirecEmerg1 = '$DirecEmerg1',
					TipoDocumento = '$TipoDocumento',
					DocFiscal = '$DocFiscal',
					Ndocumento = '$Ndocumento',
					Estado = '$Estado',
					UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
					UltimaFecha = NOW()
				WHERE CodPersona = '$CodPersona'";
		execute($sql);
		##	banco 
		execute("DELETE FROM bancopersona WHERE CodPersona = '$CodPersona' AND FlagPrincipal='S'");
		$CodSecuencia = codigo('bancopersona','CodSecuencia',6,['CodPersona'],[$CodPersona]);
		if ($CodBanco)
		{
			$sql = "INSERT INTO bancopersona
					SET
						CodSecuencia = '$CodSecuencia',
						CodBanco = '$CodBanco',
						CodPersona = '$CodPersona',
						TipoCuenta = '$TipoCuenta',
						Ncuenta = '$Ncuenta',
						Aportes = 'PQ',
						FlagPrincipal = 'S',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()";
			execute($sql);
		}
		//	EMPLEADO
		if ($EsEmpleado == 'S') {
			##	valido
			if (!trim($CodOrganismo) || !trim($CodDependencia) || !trim($CodCentroCosto)|| !trim($Usuario)) die("Debe llenar los campos (*) obligatorios.");
			else {
				##	Usuario
				$sql = "SELECT COUNT(*)
						FROM mastempleado
						WHERE
							Usuario = '$Usuario'
							AND CodPersona <> '$CodPersona'";
				if (getVar3($sql)) die('Usuario ya registrado');
			}
			##	consulto
			$sql = "SELECT * FROM mastempleado WHERE CodPersona = '$CodPersona'";
			$field_empleado = getRecord($sql);
			if (count($field_empleado)) {
				##	actualizo
				$sql = "UPDATE mastempleado
						SET
							CodOrganismo = '$CodOrganismo',
							CodDependencia = '$CodDependencia',
							CodCentroCosto = '$CodCentroCosto',
							Usuario = '$Usuario',
							Estado = '$EstadoEmpleado',
							UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
							UltimaFecha = NOW()
						WHERE CodPersona = '$CodPersona'";
			} else {
				##	codigo
				$CodEmpleado = codigo('mastempleado','CodEmpleado',6);
				##	inserto
				$sql = "INSERT INTO mastempleado
						SET
							CodPersona = '$CodPersona',
							CodEmpleado = '$CodEmpleado',
							CodOrganismo = '$CodOrganismo',
							CodDependencia = '$CodDependencia',
							CodCentroCosto = '$CodCentroCosto',
							Usuario = '$Usuario',
							Estado = '$EstadoEmpleado',
							UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
							UltimaFecha = NOW()";
			}
			execute($sql);
		}
		//	PROVEEDOR
		if ($EsProveedor == 'S') {
			$FechaConstitucion = formatFechaAMD($FechaConstitucion);
			$FechaEmisionSNC = formatFechaAMD($FechaEmisionSNC);
			$FechaValidacionSNC = formatFechaAMD($FechaValidacionSNC);
			$FlagSNC = ($FlagSNC?'S':'N');
			##	valido
			if (!trim($CodTipoDocumento) || !trim($CodTipoPago) || !trim($CodTipoServicio) || !trim($CodFormaPago)) die("Debe llenar los campos (*) obligatorios.");
			##	consulto
			$sql = "SELECT * FROM mastproveedores WHERE CodProveedor = '$CodPersona'";
			$field_proveedor = getRecord($sql);
			if (count($field_proveedor)) {
				##	actualizo
				$sql = "UPDATE mastproveedores
						SET
							CodTipoDocumento = '$CodTipoDocumento',
							CodTipoPago = '$CodTipoPago',
							CodFormaPago = '$CodFormaPago',
							CodTipoServicio = '$CodTipoServicio',
							DiasPago = '$DiasPago',
							RegistroPublico = '$RegistroPublico',
							LicenciaMunicipal = '$LicenciaMunicipal',
							FechaConstitucion = '$FechaConstitucion',
							RepresentanteLegal = '$RepresentanteLegal',
							ContactoVendedor = '$ContactoVendedor',
							FlagSNC = '$FlagSNC',
							NroInscripcionSNC = '$NroInscripcionSNC',
							FechaEmisionSNC = '$FechaEmisionSNC',
							FechaValidacionSNC = '$FechaValidacionSNC',
							Nacionalidad = '$Nacionalidad',
							UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
							UltimaFecha = NOW()
						WHERE CodProveedor = '$CodPersona'";
				execute($sql);
			} else {
				##	codigo
				$NroProveedor = codigo('mastproveedores','NroProveedor',6);
				##	inserto
				$sql = "INSERT INTO mastproveedores
						SET
							CodProveedor = '$CodPersona',
							NroProveedor = '$NroProveedor',
							CodTipoDocumento = '$CodTipoDocumento',
							CodTipoPago = '$CodTipoPago',
							CodFormaPago = '$CodFormaPago',
							CodTipoServicio = '$CodTipoServicio',
							DiasPago = '$DiasPago',
							RegistroPublico = '$RegistroPublico',
							LicenciaMunicipal = '$LicenciaMunicipal',
							FechaConstitucion = '$FechaConstitucion',
							RepresentanteLegal = '$RepresentanteLegal',
							ContactoVendedor = '$ContactoVendedor',
							FlagSNC = '$FlagSNC',
							NroInscripcionSNC = '$NroInscripcionSNC',
							FechaEmisionSNC = '$FechaEmisionSNC',
							FechaValidacionSNC = '$FechaValidacionSNC',
							Nacionalidad = '$Nacionalidad',
							UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
							UltimaFecha = NOW()";
				execute($sql);
			}
		}
		//	CLIENTE
		if ($EsCliente == 'S') {
			$ClienteFecVencLineaCredito = formatFechaAMD($ClienteFecVencLineaCredito);
			$ClienteMontoLineaCredito = getNumero($ClienteMontoLineaCredito);
			$iCodVendedor = (!empty($ClienteCodVendedor)?"CodVendedor = '$ClienteCodVendedor',":'');
			##	valido
			if (!trim($ClienteCodFormaPago) || !trim($ClienteClasificacion) || !trim($ClienteCodTipoDocumento) || !trim($ClienteFormaFactura) || !trim($ClienteTipoCliente) || !trim($ClienteTipoVenta) || !trim($ClienteCodTipoPago) || !trim($ClienteCodRutaDespacho) || !trim($ClienteFecVencLineaCredito)) die("Debe llenar los campos (*) obligatorios.");
			##	consulto
			$sql = "SELECT * FROM mastcliente WHERE CodPersona = '$CodPersona'";
			$field_cliente = getRecord($sql);
			if (count($field_cliente)) {
				##	actualizo
				$sql = "UPDATE mastcliente
						SET
							CodFormaPago = '$ClienteCodFormaPago',
							CodTipoPago = '$ClienteCodTipoPago',
							CodTipoDocumento = '$ClienteCodTipoDocumento',
							$iCodVendedor
							CodRutaDespacho = '$ClienteCodRutaDespacho',
							TipoActividad = '$ClienteTipoActividad',
							TipoCliente = '$ClienteTipoCliente',
							FormaFactura = '$ClienteFormaFactura',
							TipoVenta = '$ClienteTipoVenta',
							Clasificacion = '$ClienteClasificacion',
							LineaCreditoMoneda = '$ClienteLineaCreditoMoneda',
							MontoLineaCredito = '$ClienteMontoLineaCredito',
							FecVencLineaCredito = '$ClienteFecVencLineaCredito',
							PersonaContacto = '$ClientePersonaContacto',
							CargoContacto = '$ClienteCargoContacto',
							UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
							UltimaFecha = NOW()
						WHERE CodPersona = '$CodPersona'";
				execute($sql);
			} else {
				##	codigo
				$CodCliente = codigo('mastcliente','CodCliente',6);
				##	inserto
				$sql = "INSERT INTO mastcliente
						SET
							CodCliente = '$CodCliente',
							CodPersona = '$CodPersona',
							CodFormaPago = '$ClienteCodFormaPago',
							CodTipoPago = '$ClienteCodTipoPago',
							CodTipoDocumento = '$ClienteCodTipoDocumento',
							$iCodVendedor
							CodRutaDespacho = '$ClienteCodRutaDespacho',
							TipoActividad = '$ClienteTipoActividad',
							TipoCliente = '$ClienteTipoCliente',
							FormaFactura = '$ClienteFormaFactura',
							TipoVenta = '$ClienteTipoVenta',
							Clasificacion = '$ClienteClasificacion',
							LineaCreditoMoneda = '$ClienteLineaCreditoMoneda',
							MontoLineaCredito = '$ClienteMontoLineaCredito',
							FecVencLineaCredito = '$ClienteFecVencLineaCredito',
							PersonaContacto = '$ClientePersonaContacto',
							CargoContacto = '$ClienteCargoContacto',
							UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
							UltimaFecha = NOW()";
				execute($sql);
			}
		}
		##	-----------------
		mysql_query("COMMIT");
	}
}
elseif ($modulo == "ajax") 
{
	if ($accion == 'validarCedula') 
	{
		if ($TipoPersona == 'N' && !numeric($Ndocumento))
			die(json_encode(['status' => 'error', 'message' => 'Nro. Documento Formato Incorrecto (Solo se permiten valores numéricos)']));
		elseif ($TipoPersona == 'J' && !valid_rif($Ndocumento))
			die(json_encode(['status' => 'error', 'message' => 'Nro. Documento Formato Incorrecto (Solo se permite el formato del rif sin guiones)']));
		elseif (!is_unique('mastpersonas','Ndocumento',$Ndocumento))
			die(json_encode(['status' => 'error', 'message' => 'Nro. Documento ya registrado']));
	}
	elseif ($accion == 'validarRif') 
	{
		if (!valid_rif($DocFiscal))
			die(json_encode(['status' => 'error', 'message' => 'Doc. Fiscal Formato Incorrecto (Solo se permite el formato del rif sin guiones)']));
		elseif (!is_unique('mastpersonas','DocFiscal',$DocFiscal))
			die(json_encode(['status' => 'error', 'message' => 'Doc. Fiscal ya registrado']));
	}
}
