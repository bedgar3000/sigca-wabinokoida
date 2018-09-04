<?php
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion-".$AnioActual.$MesActual.$DiaActual."_".$HorasActual.$MinutosActual.$SegundosActual.".sql", "w+");
##############################################################################/
##	Postulantes (NUEVO, MODIFICAR, ELIMINAR, AJAX)
##############################################################################/
if ($modulo == "formulario") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		##	-----------------
		##	valido
		if (!trim($Apellido2) || !trim($Nombres) || !trim($CiudadNacimiento) || !trim($CiudadDomicilio) || !trim($Fnacimiento) || !trim($TipoDocumento) || !trim($Ndocumento) || !trim($EstadoCivil)) die("Debe llenar los campos (*) obligatorios.");
		##	codigo
		$Postulante = codigo('rh_postulantes','Postulante',6);
		$Expediente = $AnioActual.$Postulante;
		##	inserto
		$sql = "INSERT INTO rh_postulantes
				SET
					Postulante = '".$Postulante."',
					Apellido1 = '".$Apellido1."',
					Apellido2 = '".$Apellido2."',
					Nombres = '".$Nombres."',
					ResumenEjec = '".$ResumenEjec."',
					CiudadNacimiento = '".$CiudadNacimiento."',
					CiudadDomicilio = '".$CiudadDomicilio."',
					Fnacimiento = '".formatFechaAMD($Fnacimiento)."',
					Sexo = '".$Sexo."',
					Direccion = '".$Direccion."',
					Referencia = '".$Referencia."',
					Email = '".$Email."',
					Telefono1 = '".$Telefono1."',
					FechaRegistro = '".$FechaActual."',
					Expediente = '".$Expediente."',
					TipoDocumento = '".$TipoDocumento."',
					Ndocumento = '".$Ndocumento."',
					EstadoCivil = '".$EstadoCivil."',
					FedoCivil = '".formatFechaAMD($FedoCivil)."',
					GrupoSanguineo = '".$GrupoSanguineo."',
					SituacionDomicilio = '".$SituacionDomicilio."',
					InformacionAdic = '".$InformacionAdic."',
					FlagBeneficas = '".(trim($Beneficas)?'S':'N')."',
					Beneficas = '".$Beneficas."',
					FlagLaborales = '".(trim($Laborales)?'S':'N')."',
					Laborales = '".$Laborales."',
					FlagCulturales = '".(trim($Culturales)?'S':'N')."',
					Culturales = '".$Culturales."',
					FlagDeportivas = '".(trim($Deportivas)?'S':'N')."',
					Deportivas = '".$Deportivas."',
					FlagReligiosas = '".(trim($Religiosas)?'S':'N')."',
					Religiosas = '".$Religiosas."',
					FlagSociales = '".(trim($Sociales)?'S':'N')."',
					Sociales = '".$Sociales."',
					Anio = '".$AnioActual."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		##	instruccion
		for ($i=0; $i < count($instruccion_CodGradoInstruccion); $i++) { 
			##	valido
			if (!trim($instruccion_CodGradoInstruccion[$i]) || !trim($instruccion_Nivel[$i]) || !trim($instruccion_CodCentroEstudio[$i])) die("<strong>Ficha Instrucci&oacute;n.</strong> Debe llenar los campos (*) obligatorios.");
			$sql = "SELECT COUNT(*)
					FROM rh_postulantes_instruccion
					WHERE
						Postulante = '".$Postulante."' AND
						CodGradoInstruccion = '".$instruccion_CodGradoInstruccion[$i]."' AND
						Area = '".$instruccion_Area[$i]."' AND
						CodProfesion = '".$instruccion_CodProfesion[$i]."' AND
						Nivel = '".$instruccion_Nivel[$i]."'";
			$Count = getVar3($sql);
			if ($Count) die("<strong>Ficha Instrucci&oacute;n.</strong> Se encontrar&oacute;n registros duplicados.");
			##	inserto
			$sql = "INSERT INTO rh_postulantes_instruccion
					SET
						Postulante = '".$Postulante."',
						Secuencia = '".($i+1)."',
						CodGradoInstruccion = '".$instruccion_CodGradoInstruccion[$i]."',
						Area = '".$instruccion_Area[$i]."',
						CodProfesion = '".$instruccion_CodProfesion[$i]."',
						Nivel = '".$instruccion_Nivel[$i]."',
						CodCentroEstudio = '".$instruccion_CodCentroEstudio[$i]."',
						FechaDesde = '".formatFechaAMD($instruccion_FechaDesde[$i])."',
						FechaHasta = '".formatFechaAMD($instruccion_FechaHasta[$i])."',
						Colegiatura = '".$instruccion_Colegiatura[$i]."',
						NroColegiatura = '".$instruccion_NroColegiatura[$i]."',
						Observaciones = '".$instruccion_Observaciones[$i]."',
						FechaGraduacion = '".formatFechaAMD($instruccion_FechaGraduacion[$i])."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	idioma
		for ($i=0; $i < count($idioma_CodIdioma); $i++) { 
			##	valido
			if (!trim($idioma_CodIdioma[$i])) die("<strong>Ficha Idiomas.</strong> Debe llenar los campos (*) obligatorios.");
			$sql = "SELECT COUNT(*)
					FROM rh_postulantes_idioma
					WHERE
						Postulante = '".$Postulante."' AND
						CodIdioma = '".$idioma_CodIdioma[$i]."'";
			$Count = getVar3($sql);
			if ($Count) die("<strong>Ficha Idiomas.</strong> Se encontrar&oacute;n registros duplicados.");
			##	inserto
			$sql = "INSERT INTO rh_postulantes_idioma
					SET
						Postulante = '".$Postulante."',
						CodIdioma = '".$idioma_CodIdioma[$i]."',
						NivelLectura = '".$idioma_NivelLectura[$i]."',
						NivelOral = '".$idioma_NivelOral[$i]."',
						NivelEscritura = '".$idioma_NivelEscritura[$i]."',
						NivelGeneral = '".$idioma_NivelGeneral[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	informatica
		for ($i=0; $i < count($informat_Informatica); $i++) {
			##	valido
			$sql = "SELECT COUNT(*)
					FROM rh_postulantes_informat
					WHERE
						Postulante = '".$Postulante."' AND
						Informatica = '".$informat_Informatica[$i]."'";
			$Count = getVar3($sql);
			if ($Count) die("<strong>Ficha Inform&aacute;tica.</strong> Se encontrar&oacute;n registros duplicados.");
			##	inserto
			$sql = "INSERT INTO rh_postulantes_informat
					SET
						Postulante = '".$Postulante."',
						Informatica = '".$informat_Informatica[$i]."',
						Nivel = '".$informat_Nivel[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	cursos
		for ($i=0; $i < count($cursos_CodCurso); $i++) { 
			##	valido
			if (!trim($cursos_CodCurso[$i]) || !trim($cursos_TipoCurso[$i]) || !trim($cursos_CodCentroEstudio[$i]) || !trim($cursos_PeriodoCulminacion[$i])) die("<strong>Ficha Cursos.</strong> Debe llenar los campos (*) obligatorios.");
			$sql = "INSERT INTO rh_postulantes_cursos
					SET
						Postulante = '".$Postulante."',
						Secuencia = '".($i+1)."',
						CodCurso = '".$cursos_CodCurso[$i]."',
						TipoCurso = '".$cursos_TipoCurso[$i]."',
						CodCentroEstudio = '".$cursos_CodCentroEstudio[$i]."',
						FechaDesde = '".formatFechaAMD($cursos_FechaDesde[$i])."',
						FechaHasta = '".formatFechaAMD($cursos_FechaHasta[$i])."',
						TotalHoras = '".$cursos_TotalHoras[$i]."',
						AniosVigencia = '".$cursos_AniosVigencia[$i]."',
						Observaciones = '".$cursos_Observaciones[$i]."',
						PeriodoCulminacion = '".$cursos_PeriodoCulminacion[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	experiencia
		for ($i=0; $i < count($experiencia_Empresa); $i++) { 
			##	valido
			if (!trim($experiencia_Empresa[$i]) || !trim($experiencia_FechaDesde[$i]) || !trim($experiencia_FechaHasta[$i]) || !trim($experiencia_TipoEnte[$i])) die("<strong>Ficha Experiencia Laboral.</strong> Debe llenar los campos (*) obligatorios.");
			$sql = "INSERT INTO rh_postulantes_experiencia
					SET
						Postulante = '".$Postulante."',
						Secuencia = '".($i+1)."',
						Empresa = '".$experiencia_Empresa[$i]."',
						FechaDesde = '".formatFechaAMD($experiencia_FechaDesde[$i])."',
						TipoEnte = '".$experiencia_TipoEnte[$i]."',
						CargoOcupado = '".$experiencia_CargoOcupado[$i]."',
						FechaHasta = '".formatFechaAMD($experiencia_FechaHasta[$i])."',
						MotivoCese = '".$experiencia_MotivoCese[$i]."',
						AreaExperiencia = '".$experiencia_AreaExperiencia[$i]."',
						Sueldo = '".setNumero($experiencia_Sueldo[$i])."',
						Funciones = '".$experiencia_Funciones[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	referencias
		for ($i=0; $i < count($referencias_Nombre); $i++) { 
			##	valido
			if (!trim($referencias_Nombre[$i]) || !trim($referencias_Cargo[$i]) || !trim($referencias_Empresa[$i]) || !trim($referencias_Telefono[$i]) || !trim($referencias_Direccion[$i])) die("<strong>Ficha Referencia Laboral.</strong> Debe llenar los campos (*) obligatorios.");
			$sql = "INSERT INTO rh_postulantes_referencias
					SET
						Postulante = '".$Postulante."',
						Secuencia = '".($i+1)."',
						Nombre = '".$referencias_Nombre[$i]."',
						Cargo = '".$referencias_Cargo[$i]."',
						Empresa = '".$referencias_Empresa[$i]."',
						Telefono = '".$referencias_Telefono[$i]."',
						Direccion = '".$referencias_Direccion[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	documentos
		for ($i=0; $i < count($documentos_Documento); $i++) { 
			##	valido
			if (!trim($documentos_Documento[$i])) die("<strong>Ficha Documentos .</strong> Debe llenar los campos (*) obligatorios.");
			$sql = "SELECT COUNT(*)
					FROM rh_postulantes_documentos
					WHERE
						Postulante = '".$Postulante."' AND
						Documento = '".$documentos_Documento[$i]."'";
			$Count = getVar3($sql);
			if ($Count) die("<strong>Ficha Documentos.</strong> Se encontrar&oacute;n registros duplicados.");
			##	inserto
			$sql = "INSERT INTO rh_postulantes_documentos
					SET
						Postulante = '".$Postulante."',
						Secuencia = '".($i+1)."',
						Documento = '".$documentos_Documento[$i]."',
						FlagPresento = '".($documentos_FlagPresento[$i]?'S':'N')."',
						Observaciones = '".$documentos_Observaciones[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	cargos
		for ($i=0; $i < count($cargos_CodCargo); $i++) { 
			$sql = "INSERT INTO rh_postulantes_cargos
					SET
						Postulante = '".$Postulante."',
						Secuencia = '".($i+1)."',
						CodCargo = '".$cargos_CodCargo[$i]."',
						Comentario = '".$cargos_Comentario[$i]."',
						CodOrganismo = '".$cargos_CodOrganismo[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
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
		##	valido
		if (!trim($Apellido2) || !trim($Nombres) || !trim($CiudadNacimiento) || !trim($CiudadDomicilio) || !trim($Fnacimiento) || !trim($TipoDocumento) || !trim($Ndocumento) || !trim($EstadoCivil)) die("Debe llenar los campos (*) obligatorios.");
		##	actualizo
		$sql = "UPDATE rh_postulantes
				SET
					Apellido1 = '".$Apellido1."',
					Apellido2 = '".$Apellido2."',
					Nombres = '".$Nombres."',
					ResumenEjec = '".$ResumenEjec."',
					CiudadNacimiento = '".$CiudadNacimiento."',
					CiudadDomicilio = '".$CiudadDomicilio."',
					Fnacimiento = '".formatFechaAMD($Fnacimiento)."',
					Sexo = '".$Sexo."',
					Direccion = '".$Direccion."',
					Referencia = '".$Referencia."',
					Email = '".$Email."',
					Telefono1 = '".$Telefono1."',
					FechaRegistro = '".$FechaActual."',
					Expediente = '".$Expediente."',
					TipoDocumento = '".$TipoDocumento."',
					Ndocumento = '".$Ndocumento."',
					EstadoCivil = '".$EstadoCivil."',
					FedoCivil = '".formatFechaAMD($FedoCivil)."',
					GrupoSanguineo = '".$GrupoSanguineo."',
					SituacionDomicilio = '".$SituacionDomicilio."',
					InformacionAdic = '".$InformacionAdic."',
					FlagBeneficas = '".(trim($Beneficas)?'S':'N')."',
					Beneficas = '".$Beneficas."',
					FlagLaborales = '".(trim($Laborales)?'S':'N')."',
					Laborales = '".$Laborales."',
					FlagCulturales = '".(trim($Culturales)?'S':'N')."',
					Culturales = '".$Culturales."',
					FlagDeportivas = '".(trim($Deportivas)?'S':'N')."',
					Deportivas = '".$Deportivas."',
					FlagReligiosas = '".(trim($Religiosas)?'S':'N')."',
					Religiosas = '".$Religiosas."',
					FlagSociales = '".(trim($Sociales)?'S':'N')."',
					Sociales = '".$Sociales."',
					Anio = '".$AnioActual."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE Postulante = '".$Postulante."'";
		execute($sql);
		##	instruccion
		$sql = "DELETE FROM rh_postulantes_instruccion WHERE Postulante = '".$Postulante."'";
		execute($sql);
		for ($i=0; $i < count($instruccion_CodGradoInstruccion); $i++) { 
			##	valido
			if (!trim($instruccion_CodGradoInstruccion[$i]) || !trim($instruccion_Nivel[$i]) || !trim($instruccion_CodCentroEstudio[$i])) die("<strong>Ficha Instrucci&oacute;n.</strong> Debe llenar los campos (*) obligatorios.");
			$sql = "SELECT COUNT(*)
					FROM rh_postulantes_instruccion
					WHERE
						Postulante = '".$Postulante."' AND
						CodGradoInstruccion = '".$instruccion_CodGradoInstruccion[$i]."' AND
						Area = '".$instruccion_Area[$i]."' AND
						CodProfesion = '".$instruccion_CodProfesion[$i]."' AND
						Nivel = '".$instruccion_Nivel[$i]."'";
			$Count = getVar3($sql);
			if ($Count) die("<strong>Ficha Instrucci&oacute;n.</strong> Se encontrar&oacute;n registros duplicados.");
			##	inserto
			$sql = "INSERT INTO rh_postulantes_instruccion
					SET
						Postulante = '".$Postulante."',
						Secuencia = '".($i+1)."',
						CodGradoInstruccion = '".$instruccion_CodGradoInstruccion[$i]."',
						Area = '".$instruccion_Area[$i]."',
						CodProfesion = '".$instruccion_CodProfesion[$i]."',
						Nivel = '".$instruccion_Nivel[$i]."',
						CodCentroEstudio = '".$instruccion_CodCentroEstudio[$i]."',
						FechaDesde = '".formatFechaAMD($instruccion_FechaDesde[$i])."',
						FechaHasta = '".formatFechaAMD($instruccion_FechaHasta[$i])."',
						Colegiatura = '".$instruccion_Colegiatura[$i]."',
						NroColegiatura = '".$instruccion_NroColegiatura[$i]."',
						Observaciones = '".$instruccion_Observaciones[$i]."',
						FechaGraduacion = '".formatFechaAMD($instruccion_FechaGraduacion[$i])."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	idioma
		$sql = "DELETE FROM rh_postulantes_idioma WHERE Postulante = '".$Postulante."'";
		execute($sql);
		for ($i=0; $i < count($idioma_CodIdioma); $i++) { 
			##	valido
			if (!trim($idioma_CodIdioma[$i])) die("<strong>Ficha Idiomas.</strong> Debe llenar los campos (*) obligatorios.");
			$sql = "SELECT COUNT(*)
					FROM rh_postulantes_idioma
					WHERE
						Postulante = '".$Postulante."' AND
						CodIdioma = '".$idioma_CodIdioma[$i]."'";
			$Count = getVar3($sql);
			if ($Count) die("<strong>Ficha Idiomas.</strong> Se encontrar&oacute;n registros duplicados.");
			##	inserto
			$sql = "INSERT INTO rh_postulantes_idioma
					SET
						Postulante = '".$Postulante."',
						CodIdioma = '".$idioma_CodIdioma[$i]."',
						NivelLectura = '".$idioma_NivelLectura[$i]."',
						NivelOral = '".$idioma_NivelOral[$i]."',
						NivelEscritura = '".$idioma_NivelEscritura[$i]."',
						NivelGeneral = '".$idioma_NivelGeneral[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	informatica
		$sql = "DELETE FROM rh_postulantes_informat WHERE Postulante = '".$Postulante."'";
		execute($sql);
		for ($i=0; $i < count($informat_Informatica); $i++) {
			##	valido
			$sql = "SELECT COUNT(*)
					FROM rh_postulantes_informat
					WHERE
						Postulante = '".$Postulante."' AND
						Informatica = '".$informat_Informatica[$i]."'";
			$Count = getVar3($sql);
			if ($Count) die("<strong>Ficha Inform&aacute;tica.</strong> Se encontrar&oacute;n registros duplicados.");
			##	inserto
			$sql = "INSERT INTO rh_postulantes_informat
					SET
						Postulante = '".$Postulante."',
						Informatica = '".$informat_Informatica[$i]."',
						Nivel = '".$informat_Nivel[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	cursos
		$sql = "DELETE FROM rh_postulantes_cursos WHERE Postulante = '".$Postulante."'";
		execute($sql);
		for ($i=0; $i < count($cursos_CodCurso); $i++) { 
			##	valido
			if (!trim($cursos_CodCurso[$i]) || !trim($cursos_TipoCurso[$i]) || !trim($cursos_CodCentroEstudio[$i]) || !trim($cursos_PeriodoCulminacion[$i])) die("<strong>Ficha Cursos.</strong> Debe llenar los campos (*) obligatorios.");
			$sql = "INSERT INTO rh_postulantes_cursos
					SET
						Postulante = '".$Postulante."',
						Secuencia = '".($i+1)."',
						CodCurso = '".$cursos_CodCurso[$i]."',
						TipoCurso = '".$cursos_TipoCurso[$i]."',
						CodCentroEstudio = '".$cursos_CodCentroEstudio[$i]."',
						FechaDesde = '".formatFechaAMD($cursos_FechaDesde[$i])."',
						FechaHasta = '".formatFechaAMD($cursos_FechaHasta[$i])."',
						TotalHoras = '".$cursos_TotalHoras[$i]."',
						AniosVigencia = '".$cursos_AniosVigencia[$i]."',
						Observaciones = '".$cursos_Observaciones[$i]."',
						PeriodoCulminacion = '".$cursos_PeriodoCulminacion[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	experiencia
		$sql = "DELETE FROM rh_postulantes_experiencia WHERE Postulante = '".$Postulante."'";
		execute($sql);
		for ($i=0; $i < count($experiencia_Empresa); $i++) { 
			##	valido
			if (!trim($experiencia_Empresa[$i]) || !trim($experiencia_FechaDesde[$i]) || !trim($experiencia_FechaHasta[$i]) || !trim($experiencia_TipoEnte[$i])) die("<strong>Ficha Experiencia Laboral.</strong> Debe llenar los campos (*) obligatorios.");
			$sql = "INSERT INTO rh_postulantes_experiencia
					SET
						Postulante = '".$Postulante."',
						Secuencia = '".($i+1)."',
						Empresa = '".$experiencia_Empresa[$i]."',
						FechaDesde = '".formatFechaAMD($experiencia_FechaDesde[$i])."',
						TipoEnte = '".$experiencia_TipoEnte[$i]."',
						CargoOcupado = '".$experiencia_CargoOcupado[$i]."',
						FechaHasta = '".formatFechaAMD($experiencia_FechaHasta[$i])."',
						MotivoCese = '".$experiencia_MotivoCese[$i]."',
						AreaExperiencia = '".$experiencia_AreaExperiencia[$i]."',
						Sueldo = '".setNumero($experiencia_Sueldo[$i])."',
						Funciones = '".$experiencia_Funciones[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	referencias
		$sql = "DELETE FROM rh_postulantes_referencias WHERE Postulante = '".$Postulante."'";
		execute($sql);
		for ($i=0; $i < count($referencias_Nombre); $i++) { 
			##	valido
			if (!trim($referencias_Nombre[$i]) || !trim($referencias_Cargo[$i]) || !trim($referencias_Empresa[$i]) || !trim($referencias_Telefono[$i]) || !trim($referencias_Direccion[$i])) die("<strong>Ficha Referencia Laboral.</strong> Debe llenar los campos (*) obligatorios.");
			$sql = "INSERT INTO rh_postulantes_referencias
					SET
						Postulante = '".$Postulante."',
						Secuencia = '".($i+1)."',
						Nombre = '".$referencias_Nombre[$i]."',
						Cargo = '".$referencias_Cargo[$i]."',
						Empresa = '".$referencias_Empresa[$i]."',
						Telefono = '".$referencias_Telefono[$i]."',
						Direccion = '".$referencias_Direccion[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	documentos
		$sql = "DELETE FROM rh_postulantes_documentos WHERE Postulante = '".$Postulante."'";
		execute($sql);
		for ($i=0; $i < count($documentos_Documento); $i++) { 
			##	valido
			if (!trim($documentos_Documento[$i])) die("<strong>Ficha Documentos .</strong> Debe llenar los campos (*) obligatorios.");
			$sql = "SELECT COUNT(*)
					FROM rh_postulantes_documentos
					WHERE
						Postulante = '".$Postulante."' AND
						Documento = '".$documentos_Documento[$i]."'";
			$Count = getVar3($sql);
			if ($Count) die("<strong>Ficha Documentos.</strong> Se encontrar&oacute;n registros duplicados.");
			##	inserto
			$sql = "INSERT INTO rh_postulantes_documentos
					SET
						Postulante = '".$Postulante."',
						Secuencia = '".($i+1)."',
						Documento = '".$documentos_Documento[$i]."',
						FlagPresento = '".($documentos_FlagPresento[$i]?'S':'N')."',
						Observaciones = '".$documentos_Observaciones[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	cargos
		$sql = "DELETE FROM rh_postulantes_cargos WHERE Postulante = '".$Postulante."'";
		execute($sql);
		for ($i=0; $i < count($cargos_CodCargo); $i++) { 
			$sql = "INSERT INTO rh_postulantes_cargos
					SET
						Postulante = '".$Postulante."',
						Secuencia = '".($i+1)."',
						CodCargo = '".$cargos_CodCargo[$i]."',
						Comentario = '".$cargos_Comentario[$i]."',
						CodOrganismo = '".$cargos_CodOrganismo[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	-----------------
		mysql_query("COMMIT");
	}
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "DELETE FROM rh_postulantes WHERE Postulante = '".$registro."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
elseif ($modulo == "ajax") {
	//	insertar linea
	if ($accion == "instruccion_insertar") {
		$id = $nro_detalle;
		?>
        <tr class="trListaBody" onclick="clk($(this), 'instruccion', 'instruccion_<?=$id?>');" id="instruccion_<?=$id?>">
            <th><?=$id?></th>
            <td>
            	<table border="1" width="100%">
				    <tr>
						<td class="tagForm">* G. Instrucci&oacute;n:</td>
						<td>
							<select name="instruccion_CodGradoInstruccion[]" id="instruccion_CodGradoInstruccion<?=$id?>" style="width:175px;" onChange="getOptionsSelect(this.value, 'nivel-instruccion', 'instruccion_Nivel<?=$id?>', true); getOptionsSelect2('profesiones', 'instruccion_CodProfesion<?=$id?>', true, this.value, $('#instruccion_Area<?=$id?>').val());">
				            	<option value="">&nbsp;</option>
								<?=loadSelect("rh_gradoinstruccion", "CodGradoInstruccion", "Descripcion")?>
							</select>
						</td>
						<td class="tagForm">* Nivel:</td>
						<td>
							<select name="instruccion_Nivel[]" id="instruccion_Nivel<?=$id?>" style="width:175px;">
				            	<option value="">&nbsp;</option>
							</select>
						</td>
						<td class="tagForm">* F. Graduaci&oacute;n:</td>
						<td>
							<input type="text" name="instruccion_FechaGraduacion[]" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" />
						</td>
					</tr>
				    <tr>
						<td class="tagForm">Area Profesional:</td>
						<td>
							<select name="instruccion_Area[]" id="instruccion_Area<?=$id?>" style="width:175px;" onChange="getOptionsSelect2('profesiones', 'instruccion_CodProfesion<?=$id?>', true, $('#instruccion_CodGradoInstruccion<?=$id?>').val(), this.value);">
				            	<option value="">&nbsp;</option>
								<?=getMiscelaneos('', "AREA", 0)?>
							</select>
						</td>
						<td class="tagForm">Profesi&oacute;n:</td>
						<td>
							<select name="instruccion_CodProfesion[]" id="instruccion_CodProfesion<?=$id?>" style="width:175px;">
				            	<option value="">&nbsp;</option>
							</select>
						</td>
						<td class="tagForm">Desde:</td>
						<td>
							<input type="text" name="instruccion_FechaDesde[]" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" />
						</td>
					</tr>
				    <tr>
						<td class="tagForm">Colegiatura:</td>
						<td>
				            <select name="instruccion_Colegiatura[]" style="width:175px;">
				            	<option value="">&nbsp;</option>
				                <?=getMiscelaneos('', "COLEGIOS", 0);?>
				            </select>
						</td>
						<td class="tagForm">Nro. Colegiatura:</td>
						<td>
							<input type="text" name="instruccion_NroColegiatura[]" style="width:170px;" maxlength="9" />
						</td>
						<td class="tagForm">Hasta:</td>
						<td>
							<input type="text" name="instruccion_FechaHasta[]" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" />
						</td>
					</tr>
				    <tr>
						<td class="tagForm">* Centro de Estudio:</td>
						<td class="gallery clearfix">
				            <input type="hidden" name="instruccion_CodCentroEstudio[]" id="instruccion_CodCentroEstudio<?=$id?>" />
							<textarea name="instruccion_NomCentroEstudio[]" id="instruccion_NomCentroEstudio<?=$id?>" style="width:171px; height:30px;" readonly="readonly"></textarea>
						</td>
						<td class="tagForm">Observaciones:</td>
						<td colspan="3">
							<textarea name="instruccion_Observaciones[]" style="width:97%; height:30px;"></textarea>
						</td>
					</tr>
				</table>
            </td>
        </tr>
        <?php
	}
	//	insertar linea
	elseif ($accion == "idioma_insertar") {
		$id = $nro_detalle;
		?>
        <tr class="trListaBody" onclick="clk($(this), 'idioma', 'idioma_<?=$id?>');" id="idioma_<?=$id?>">
            <th><?=$id?></th>
            <td>
                <select name="idioma_CodIdioma[]" class="cell">
                	<option value="">&nbsp;</option>
                    <?=loadSelect2("mastidioma", "CodIdioma", "DescripcionLocal")?>
                </select>
            </td>
            <td>
                <select name="idioma_NivelLectura[]" class="cell">
                    <?=getMiscelaneos('', "NIVEL")?>
                </select>
            </td>
            <td>
                <select name="idioma_NivelOral[]" class="cell">
                    <?=getMiscelaneos('', "NIVEL")?>
                </select>
            </td>
            <td>
                <select name="idioma_NivelEscritura[]" class="cell">
                    <?=getMiscelaneos('', "NIVEL")?>
                </select>
            </td>
            <td>
                <select name="idioma_NivelGeneral[]" class="cell">
                    <?=getMiscelaneos('', "NIVEL")?>
                </select>
            </td>
        </tr>
        <?php
	}
	//	insertar linea
	elseif ($accion == "informat_insertar") {
		$sql = "SELECT
					CodDetalle As Informatica,
					Descripcion AS NomInformatica
				FROM mastmiscelaneosdet
				WHERE
					CodAplicacion = '".$CodAplicacion."' AND
					CodMaestro = '".$CodMaestro."' AND
					CodDetalle = '".$CodDetalle."'";
		$field_cargoinformat = getRecords($sql);
		foreach ($field_cargoinformat as $f) {
			$id = $f['Informatica'];
			?>
            <tr class="trListaBody" onclick="clk($(this), 'informat', 'informat_<?=$id?>');" id="informat_<?=$id?>">
                <th><?=$nro_detalles?></th>
                <td>
                	<input type="hidden" name="informat_Informatica[]" value="<?=$f['Informatica']?>" />
                    <?=$f['NomInformatica']?>
                </td>
                <td>
                    <select name="informat_Nivel[]" class="cell">
                        <?=getMiscelaneos('', "NIVEL")?>
                    </select>
                </td>
            </tr>
            <?php
		}
	}
	//	insertar linea
	elseif ($accion == "cursos_insertar") {
		$id = $nro_detalle;
		?>
        <tr class="trListaBody" onclick="clk($(this), 'cursos', 'cursos_<?=$id?>');" id="cursos_<?=$id?>">
            <th><?=$id?></th>
            <td>
            	<table border="1" width="100%">
				    <tr>
						<td class="tagForm">* Curso:</td>
						<td class="gallery clearfix">
				            <input type="hidden" name="cursos_CodCurso[]" id="cursos_CodCurso<?=$id?>" />
				            <input type="text" name="cursos_NomCurso[]" id="cursos_NomCurso<?=$id?>" style="width:170px;" readonly="readonly" />
						</td>
						<td class="tagForm">* Periodo:</td>
						<td>
							<input type="text" name="cursos_PeriodoCulminacion[]" maxlength="7" style="width:60px;" class="periodo" />
						</td>
						<td class="tagForm">Horas:</td>
						<td>
							<input type="text" name="cursos_TotalHoras[]" maxlength="4" style="width:60px;" />
						</td>
					</tr>
				    <tr>
						<td class="tagForm">* Centro de Estudio:</td>
						<td class="gallery clearfix">
				            <input type="hidden" name="cursos_CodCentroEstudio[]" id="cursos_CodCentroEstudio<?=$id?>" />
				            <input type="text" name="cursos_NomCentroEstudio[]" id="cursos_NomCentroEstudio<?=$id?>" style="width:170px;" readonly="readonly" />
						</td>
						<td class="tagForm">Desde:</td>
						<td>
							<input type="text" name="cursos_FechaDesde[]" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" />
						</td>
						<td class="tagForm">A&ntilde;os Vigencia:</td>
						<td>
							<input type="text" name="cursos_AniosVigencia[]" maxlength="2" style="width:60px;" />
						</td>
					</tr>
				    <tr>
						<td class="tagForm">* Tipo de Curso:</td>
						<td>
							<select name="cursos_TipoCurso[]" id="cursos_TipoCurso<?=$id?>" style="width:175px;">
				            	<option value="">&nbsp;</option>
								<?=getMiscelaneos('', "TIPOCURSO")?>
							</select>
						</td>
						<td class="tagForm">Hasta:</td>
						<td>
							<input type="text" name="cursos_FechaHasta[]" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" />
						</td>
						<td class="tagForm">Observaciones:</td>
						<td>
							<textarea name="cursos_Observaciones[]" style="width:200px; height:14px;"></textarea>
						</td>
					</tr>
				</table>
            </td>
        </tr>
        <?php
	}
	//	insertar linea
	elseif ($accion == "experiencia_insertar") {
		$id = $nro_detalle;
		?>
        <tr class="trListaBody" onclick="clk($(this), 'experiencia', 'experiencia_<?=$id?>');" id="experiencia_<?=$id?>">
            <th><?=$id?></th>
            <td>
            	<table border="1" width="100%">
				    <tr>
						<td class="tagForm">* Empresa:</td>
						<td>
							<input type="text" name="experiencia_Empresa[]" value="<?=$f['Empresa']?>" maxlength="255" style="width:175px;" />
						</td>
						<td class="tagForm">* Desde:</td>
						<td>
							<input type="text" name="experiencia_FechaDesde[]" value="<?=formatFechaDMA($f['FechaDesde'])?>" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" />
						</td>
						<td class="tagForm">* Tipo de Ente:</td>
						<td>
							<select name="experiencia_TipoEnte[]" style="width:175px;">
				            	<option value="">&nbsp;</option>
								<?=getMiscelaneos($f['TipoEnte'], "TIPOENTE", 0)?>
							</select>
						</td>
					</tr>
				    <tr>
						<td class="tagForm">CargoOcupado:</td>
						<td>
							<input type="text" name="experiencia_CargoOcupado[]" value="<?=$f['CargoOcupado']?>" maxlength="255" style="width:175px;" />
						</td>
						<td class="tagForm">* Hasta:</td>
						<td>
							<input type="text" name="experiencia_FechaHasta[]" value="<?=formatFechaDMA($f['FechaHasta'])?>" maxlength="10" style="width:60px;" class="datepicker" onkeyup="setFechaDMA(this);" />
						</td>
						<td class="tagForm">Motivo de Cese:</td>
						<td>
							<select name="experiencia_MotivoCese[]" style="width:175px;">
				            	<option value="">&nbsp;</option>
								<?=getMiscelaneos($f['MotivoCese'], "MOTCESE", 0)?>
							</select>
						</td>
					</tr>
				    <tr>
						<td class="tagForm">Area de Experiencia:</td>
						<td>
							<select name="experiencia_AreaExperiencia[]" style="width:181px;">
				            	<option value="">&nbsp;</option>
								<?=getMiscelaneos($f['AreaExperiencia'], "AREAEXP", 0)?>
							</select>
						</td>
						<td class="tagForm">Sueldo:</td>
						<td>
							<input type="text" name="experiencia_Sueldo[]" value="<?=number_format($f['Sueldo'],2,',','.')?>" style="width:60px; text-align:right;" class="currency" />
						</td>
						<td class="tagForm">Funciones:</td>
						<td>
							<textarea name="cursos_Funciones[]" style="width:200px; height:14px;"><?=$f['Funciones']?></textarea>
						</td>
					</tr>
				</table>
            </td>
        </tr>
        <?php
	}
	//	insertar linea
	elseif ($accion == "referencias_insertar") {
		$id = $nro_detalle;
		?>
        <tr class="trListaBody" onclick="clk($(this), 'referencias', 'referencias_<?=$id?>');" id="referencias_<?=$id?>">
            <th><?=$id?></th>
            <td>
            	<table border="1" width="100%">
				    <tr>
						<td class="tagForm" width="125">* Nombre:</td>
						<td>
							<input type="text" name="referencias_Nombre[]" value="<?=$f['Nombre']?>" maxlength="100" style="width:250px;" />
						</td>
						<td class="tagForm" width="125">* Cargo:</td>
						<td>
							<input type="text" name="referencias_Cargo[]" value="<?=$f['Cargo']?>" maxlength="255" style="width:250px;" />
						</td>
					</tr>
				    <tr>
						<td class="tagForm">* Empresa:</td>
						<td>
							<input type="text" name="referencias_Empresa[]" value="<?=$f['Empresa']?>" maxlength="255" style="width:250px;" />
						</td>
						<td class="tagForm">* Tel&eacute;fono:</td>
						<td>
							<input type="text" name="referencias_Telefono[]" value="<?=$f['Telefono']?>" maxlength="15" style="width:100px;" class="phone" />
						</td>
					</tr>
				    <tr>
						<td class="tagForm">* Direcci&oacute;n:</td>
						<td colspan="3">
							<textarea name="referencias_Direccion[]" style="width:95%; height:35px;"><?=$f['Direccion']?></textarea>
						</td>
					</tr>
				</table>
            </td>
        </tr>
        <?php
	}
	//	insertar linea
	elseif ($accion == "documentos_insertar") {
		$id = $nro_detalle;
		?>
        <tr class="trListaBody" onclick="clk($(this), 'documentos', 'documentos_<?=$id?>');" id="documentos_<?=$id?>">
            <th><?=$id?></th>
            <td>
                <select name="documentos_Documento[]" class="cell">
                	<option value="">&nbsp;</option>
                    <?=getMiscelaneos($f['Documento'], "DOCUMENTOS")?>
                </select>
            </td>
            <td align="center">
                <input type="checkbox" name="documentos_FlagPresento[]" value="S" />
            </td>
            <td>
                <textarea name="documentos_Observaciones[]" style="height:25px;" class="cell"></textarea>
            </td>
        </tr>
        <?php
	}
	//	insertar linea
	elseif ($accion == "cargos_insertar") {
		$sql = "SELECT *
				FROM rh_puestos
				WHERE CodCargo = '".$CodCargo."'";
		$field_cargo = getRecords($sql);
		foreach ($field_cargo as $f) {
			$id = $f['CodCargo'];
			?>
            <tr class="trListaBody" onclick="clk($(this), 'cargos', 'cargos_<?=$id?>');" id="cargos_<?=$id?>">
                <th><?=$nro_detalles?></th>
                <td>
                	<input type="hidden" name="cargos_CodCargo[]" value="<?=$f['CodCargo']?>" />
                    <?=$f['DescripCargo']?>
                </td>
				<td>
					<textarea name="cargos_Comentario[]" class="cell" style="height:25px;"></textarea>
				</td>
                <td>
                    <select name="cargos_CodOrganismo[]" class="cell">
                        <?=loadSelect2('mastorganismos','CodOrganismo','Organismo')?>
                    </select>
                </td>
            </tr>
            <?php
		}
	}

}
?>