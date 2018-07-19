<?php
session_start();
include("../lib/fphp.php");
include("lib/fphp.php");
//	$__archivo = fopen("_"."$modulo-$accion.sql", "w+");
//	fwrite($__archivo, $sql.";\n\n");
///////////////////////////////////////////////////////////////////////////////
//	CARGOS (NUEVO, MODIFICAR, ELIMINAR)
///////////////////////////////////////////////////////////////////////////////
if ($modulo == "cargos") {
	//	nuevo
	if ($accion == "nuevo") {
		mysql_query("BEGIN");
		//	-----------------
		if (getVar3("SELECT COUNT(*) FROM rh_puestos WHERE CodDesc = '".$CodDesc."'")) die("<strong>Clasificaci&oacute;n</strong> ya existe");
		elseif ($Paso != '01') die('Paso incorrecto');
		##	inserto
		$CodCargo = codigo("rh_puestos", "CodCargo", 4);
		$sql = "INSERT INTO rh_puestos
				SET
					CodCargo = '".$CodCargo."',
					CodGrupOcup = '".$CodGrupOcup."',
					CodSerieOcup = '".$CodSerieOcup."',
					CodTipoCargo = '".$CodTipoCargo."',
					CodNivelClase = '".$CodNivelClase."',
					NivelSalarial = '".setNumero($NivelSalarial)."',
					DescripCargo = '".changeUrl($DescripCargo)."',
					CategoriaCargo = '".$CategoriaCargo."',
					Grado = '".$Grado."',
					Paso = '".$Paso."',
					Plantilla = '".$Plantilla."',
					DescGenerica = '".changeUrl($DescGenerica)."',
					CodDesc = '".$CodDesc."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()";
		execute($sql);
		##	cargos a quien reporta
		$_Secuencia = 0;
		for ($i=0; $i < count($cargoreporta_CargoReporta); $i++) {
			##	inserto
			$sql = "INSERT INTO rh_cargoreporta
					SET
						CodCargo = '".$CodCargo."',
						Secuencia = '".++$_Secuencia."',
						CargoReporta = '".$cargoreporta_CargoReporta[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	relaciones externas e internas
		$_Secuencia = 0;
		for ($i=0; $i < count($cargorelaciones_TipoRelacion); $i++) {
			if (trim($cargorelaciones_EnteRelacionado[$i]) == '') die("Debe seleccionar el <strong>Ente Relacionado</strong> en la ficha <strong>Relaciones Externas e Internas</strong>");
			##	inserto
			$sql = "INSERT INTO rh_cargorelaciones
					SET
						CodCargo = '".$CodCargo."',
						Secuencia = '".++$_Secuencia."',
						TipoRelacion = '".$cargorelaciones_TipoRelacion[$i]."',
						EnteRelacionado = '".$cargorelaciones_EnteRelacionado[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	funciones del cargo
		$_Secuencia = 0;
		for ($i=0; $i < count($cargofunciones_Funcion); $i++) {
			if (trim($cargofunciones_Descripcion[$i]) == '') die("Debe ingresar la <strong>Descripci&oacute;n</strong> en la ficha <strong>Funciones del Cargo</strong>");
			##	inserto
			$sql = "INSERT INTO rh_cargofunciones
					SET
						CodCargo = '".$CodCargo."',
						CodFuncion = '".++$_Secuencia."',
						Funcion = '".$cargofunciones_Funcion[$i]."',
						Descripcion = '".$cargofunciones_Descripcion[$i]."',
						Estado = '".$cargofunciones_Estado[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	formación académica
		$_Secuencia = 0;
		for ($i=0; $i < count($cargoformacion_CodGradoInstruccion); $i++) {
			if (trim($cargoformacion_CodGradoInstruccion[$i]) == '') die("Debe seleccionar el <strong>Grado de Instrucci&oacute;n</strong> en la ficha <strong>Formaci&oacute;n Acad&eacute;mica</strong>");
			##	inserto
			$sql = "INSERT INTO rh_cargoformacion
					SET
						CodCargo = '".$CodCargo."',
						Secuencia = '".++$_Secuencia."',
						CodGradoInstruccion = '".$cargoformacion_CodGradoInstruccion[$i]."',
						Area = '".$cargoformacion_Area[$i]."',
						CodProfesion = '".$cargoformacion_CodProfesion[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	cursos de informática
		$_Secuencia = 0;
		for ($i=0; $i < count($cargoinformat_Informatica); $i++) {
			##	inserto
			$sql = "INSERT INTO rh_cargoinformat
					SET
						CodCargo = '".$CodCargo."',
						Informatica = '".$cargoinformat_Informatica[$i]."',
						Nivel = '".$cargoinformat_Nivel[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	dominio de idiomas
		$_Secuencia = 0;
		for ($i=0; $i < count($cargoidioma_CodIdioma); $i++) {
			##	inserto
			$sql = "INSERT INTO rh_cargoidioma
					SET
						CodCargo = '".$CodCargo."',
						CodIdioma = '".$cargoidioma_CodIdioma[$i]."',
						NivelLectura = '".$cargoidioma_NivelLectura[$i]."',
						NivelOral = '".$cargoidioma_NivelOral[$i]."',
						NivelEscritura = '".$cargoidioma_NivelEscritura[$i]."',
						NivelGeneral = '".$cargoidioma_NivelGeneral[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	experiencias previas
		$_Secuencia = 0;
		for ($i=0; $i < count($cargoexperiencia_CargoExperiencia); $i++) {
			$cargoexperiencia_FlagNecesario[$i] = valFlag($cargoexperiencia_FlagNecesario[$i]);
			if ($cargoexperiencia_AreaExperiencia[$i] == '') die("Debe seleccionar el <strong>&Aacute;rea</strong> en la ficha <strong>Experiencia Previa</strong>");
			##	inserto
			$sql = "INSERT INTO rh_cargoexperiencia
					SET
						CodCargo = '".$CodCargo."',
						Secuencia = '".++$_Secuencia."',
						CargoExperiencia = '".$cargoexperiencia_CargoExperiencia[$i]."',
						AreaExperiencia = '".$cargoexperiencia_AreaExperiencia[$i]."',
						Meses = '".$cargoexperiencia_Meses[$i]."',
						FlagNecesario = '".$cargoexperiencia_FlagNecesario[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	riesgos de trabajo
		$_Secuencia = 0;
		for ($i=0; $i < count($cargoriesgo_TipoRiesgo); $i++) {
			if ($cargoriesgo_Riesgo[$i] == '') die("Debe ingresar la <strong>Descripci&oacute;n del Riesgo</strong> en la ficha <strong>Riesgos de Trabajo</strong>");
			##	inserto
			$sql = "INSERT INTO rh_cargoriesgo
					SET
						CodCargo = '".$CodCargo."',
						Secuencia = '".++$_Secuencia."',
						TipoRiesgo = '".$cargoriesgo_TipoRiesgo[$i]."',
						Riesgo = '".$cargoriesgo_Riesgo[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	evaluación - reclutamiento
		$_Secuencia = 0;
		for ($i=0; $i < count($cargoevaluacion_Evaluacion); $i++) {
			##	inserto
			$sql = "INSERT INTO rh_cargoevaluacion
					SET
						CodCargo = '".$CodCargo."',
						Secuencia = '".++$_Secuencia."',
						Evaluacion = '".$cargoevaluacion_Evaluacion[$i]."',
						Etapa = '".$cargoevaluacion_Etapa[$i]."',
						Factor = '".$cargoevaluacion_Factor[$i]."',
						Estado = '".$cargoevaluacion_Estado[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	cargos subordinados
		$_Secuencia = 0;
		for ($i=0; $i < count($cargosub_CargoSubordinado); $i++) {
			if (intval($cargosub_Cantidad[$i]) == 0) die("Debe ingresar la <strong>Cantidad</strong> en la ficha de <strong>Puestos Subordinados</strong>");
			##	inserto
			$sql = "INSERT INTO rh_cargosub
					SET
						CodCargo = '".$CodCargo."',
						CargoSubordinado = '".$cargosub_CargoSubordinado[$i]."',
						Cantidad = '".$cargosub_Cantidad[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	otros estudios
		$_Secuencia = 0;
		for ($i=0; $i < count($cargocursos_Curso); $i++) {
			##	inserto
			$sql = "INSERT INTO rh_cargocursos
					SET
						CodCargo = '".$CodCargo."',
						Secuencia = '".++$_Secuencia."',
						Curso = '".$cargocursos_Curso[$i]."',
						TotalHoras = '".$cargocursos_TotalHoras[$i]."',
						AniosVigencia = '".$cargocursos_AniosVigencia[$i]."',
						Observaciones = '".$cargocursos_Observaciones[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	objetivos y/o metas
		$_Secuencia = 0;
		for ($i=0; $i < count($cargometas_Descripcion); $i++) {
			##	inserto
			$sql = "INSERT INTO rh_cargometas
					SET
						CodCargo = '".$CodCargo."',
						Secuencia = '".++$_Secuencia."',
						Descripcion = '".$cargometas_Descripcion[$i]."',
						FactorParticipacion = '".$cargometas_FactorParticipacion[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	ambiente de trabajo
		$_Secuencia = 0;
		for ($i=0; $i < count($cargoambiente_Ambiente); $i++) {
			##	inserto
			$sql = "INSERT INTO rh_cargoambiente
					SET
						CodCargo = '".$CodCargo."',
						Secuencia = '".++$_Secuencia."',
						Ambiente = '".$cargoambiente_Ambiente[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	habilidades / destrezas
		$_Secuencia = 0;
		for ($i=0; $i < count($cargohabilidades_Tipo); $i++) {
			##	inserto
			$sql = "INSERT INTO rh_cargohabilidades
					SET
						CodCargo = '".$CodCargo."',
						Secuencia = '".++$_Secuencia."',
						Tipo = '".$cargohabilidades_Tipo[$i]."',
						Descripcion = '".$cargohabilidades_Descripcion[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		//	-----------------
		mysql_query("COMMIT");
	}
	//	modificar
	elseif ($accion == "modificar") {
		mysql_query("BEGIN");
		//	-----------------
		if (getVar3("SELECT COUNT(*) FROM rh_puestos WHERE CodDesc = '".$CodDesc."' AND CodCargo <> '".$CodCargo."'")) die("<strong>Clasificaci&oacute;n</strong> ya existe");
		elseif ($Paso != '01') die('Paso incorrecto');
		##	actualizo
		$sql = "UPDATE rh_puestos
				SET
					CodGrupOcup = '".$CodGrupOcup."',
					CodSerieOcup = '".$CodSerieOcup."',
					CodTipoCargo = '".$CodTipoCargo."',
					CodNivelClase = '".$CodNivelClase."',
					NivelSalarial = '".setNumero($NivelSalarial)."',
					DescripCargo = '".changeUrl($DescripCargo)."',
					CategoriaCargo = '".$CategoriaCargo."',
					Grado = '".$Grado."',
					Paso = '".$Paso."',
					Plantilla = '".$Plantilla."',
					DescGenerica = '".changeUrl($DescGenerica)."',
					CodDesc = '".$CodDesc."',
					Estado = '".$Estado."',
					UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
					UltimaFecha = NOW()
				WHERE CodCargo = '".$CodCargo."'";
		execute($sql);
		##	empleados
		$sql = "UPDATE mastempleado SET SueldoActual = '".setNumero($NivelSalarial)."' WHERE CodCargo = '".$CodCargo."' AND Estado = 'A'";
		execute($sql);
		##	cargos a quien reporta
		execute("DELETE FROM rh_cargoreporta WHERE CodCargo = '".$CodCargo."'");
		$_Secuencia = 0;
		for ($i=0; $i < count($cargoreporta_CargoReporta); $i++) {
			##	inserto
			$sql = "INSERT INTO rh_cargoreporta
					SET
						CodCargo = '".$CodCargo."',
						Secuencia = '".++$_Secuencia."',
						CargoReporta = '".$cargoreporta_CargoReporta[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	relaciones externas e internas
		execute("DELETE FROM rh_cargorelaciones WHERE CodCargo = '".$CodCargo."'");
		$_Secuencia = 0;
		for ($i=0; $i < count($cargorelaciones_TipoRelacion); $i++) {
			if (trim($cargorelaciones_EnteRelacionado[$i]) == '') die("Debe seleccionar el <strong>Ente Relacionado</strong> en la ficha <strong>Relaciones Externas e Internas</strong>");
			##	inserto
			$sql = "INSERT INTO rh_cargorelaciones
					SET
						CodCargo = '".$CodCargo."',
						Secuencia = '".++$_Secuencia."',
						TipoRelacion = '".$cargorelaciones_TipoRelacion[$i]."',
						EnteRelacionado = '".$cargorelaciones_EnteRelacionado[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	funciones del cargo
		execute("DELETE FROM rh_cargofunciones WHERE CodCargo = '".$CodCargo."'");
		$_Secuencia = 0;
		for ($i=0; $i < count($cargofunciones_Funcion); $i++) {
			if (trim($cargofunciones_Descripcion[$i]) == '') die("Debe ingresar la <strong>Descripci&oacute;n</strong> en la ficha <strong>Funciones del Cargo</strong>");
			##	inserto
			$sql = "INSERT INTO rh_cargofunciones
					SET
						CodCargo = '".$CodCargo."',
						CodFuncion = '".++$_Secuencia."',
						Funcion = '".$cargofunciones_Funcion[$i]."',
						Descripcion = '".$cargofunciones_Descripcion[$i]."',
						Estado = '".$cargofunciones_Estado[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	formación académica
		execute("DELETE FROM rh_cargoformacion WHERE CodCargo = '".$CodCargo."'");
		$_Secuencia = 0;
		for ($i=0; $i < count($cargoformacion_CodGradoInstruccion); $i++) {
			if (trim($cargoformacion_CodGradoInstruccion[$i]) == '') die("Debe seleccionar el <strong>Grado de Instrucci&oacute;n</strong> en la ficha <strong>Formaci&oacute;n Acad&eacute;mica</strong>");
			##	inserto
			$sql = "INSERT INTO rh_cargoformacion
					SET
						CodCargo = '".$CodCargo."',
						Secuencia = '".++$_Secuencia."',
						CodGradoInstruccion = '".$cargoformacion_CodGradoInstruccion[$i]."',
						Area = '".$cargoformacion_Area[$i]."',
						CodProfesion = '".$cargoformacion_CodProfesion[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	cursos de informática
		execute("DELETE FROM rh_cargoinformat WHERE CodCargo = '".$CodCargo."'");
		$_Secuencia = 0;
		for ($i=0; $i < count($cargoinformat_Informatica); $i++) {
			##	inserto
			$sql = "INSERT INTO rh_cargoinformat
					SET
						CodCargo = '".$CodCargo."',
						Informatica = '".$cargoinformat_Informatica[$i]."',
						Nivel = '".$cargoinformat_Nivel[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	dominio de idiomas
		execute("DELETE FROM rh_cargoidioma WHERE CodCargo = '".$CodCargo."'");
		$_Secuencia = 0;
		for ($i=0; $i < count($cargoidioma_CodIdioma); $i++) {
			##	inserto
			$sql = "INSERT INTO rh_cargoidioma
					SET
						CodCargo = '".$CodCargo."',
						CodIdioma = '".$cargoidioma_CodIdioma[$i]."',
						NivelLectura = '".$cargoidioma_NivelLectura[$i]."',
						NivelOral = '".$cargoidioma_NivelOral[$i]."',
						NivelEscritura = '".$cargoidioma_NivelEscritura[$i]."',
						NivelGeneral = '".$cargoidioma_NivelGeneral[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	experiencias previas
		execute("DELETE FROM rh_cargoexperiencia WHERE CodCargo = '".$CodCargo."'");
		$_Secuencia = 0;
		for ($i=0; $i < count($cargoexperiencia_CargoExperiencia); $i++) {
			$cargoexperiencia_FlagNecesario[$i] = valFlag($cargoexperiencia_FlagNecesario[$i]);
			if ($cargoexperiencia_AreaExperiencia[$i] == '') die("Debe seleccionar el <strong>&Aacute;rea</strong> en la ficha <strong>Experiencia Previa</strong>");
			##	inserto
			$sql = "INSERT INTO rh_cargoexperiencia
					SET
						CodCargo = '".$CodCargo."',
						Secuencia = '".++$_Secuencia."',
						CargoExperiencia = '".$cargoexperiencia_CargoExperiencia[$i]."',
						AreaExperiencia = '".$cargoexperiencia_AreaExperiencia[$i]."',
						Meses = '".$cargoexperiencia_Meses[$i]."',
						FlagNecesario = '".$cargoexperiencia_FlagNecesario[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	riesgos de trabajo
		execute("DELETE FROM rh_cargoriesgo WHERE CodCargo = '".$CodCargo."'");
		$_Secuencia = 0;
		for ($i=0; $i < count($cargoriesgo_TipoRiesgo); $i++) {
			if ($cargoriesgo_Riesgo[$i] == '') die("Debe ingresar la <strong>Descripci&oacute;n del Riesgo</strong> en la ficha <strong>Riesgos de Trabajo</strong>");
			##	inserto
			$sql = "INSERT INTO rh_cargoriesgo
					SET
						CodCargo = '".$CodCargo."',
						Secuencia = '".++$_Secuencia."',
						TipoRiesgo = '".$cargoriesgo_TipoRiesgo[$i]."',
						Riesgo = '".$cargoriesgo_Riesgo[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	evaluación - reclutamiento
		execute("DELETE FROM rh_cargoevaluacion WHERE CodCargo = '".$CodCargo."'");
		$_Secuencia = 0;
		for ($i=0; $i < count($cargoevaluacion_Evaluacion); $i++) {
			##	inserto
			$sql = "INSERT INTO rh_cargoevaluacion
					SET
						CodCargo = '".$CodCargo."',
						Secuencia = '".++$_Secuencia."',
						Evaluacion = '".$cargoevaluacion_Evaluacion[$i]."',
						Etapa = '".$cargoevaluacion_Etapa[$i]."',
						Factor = '".$cargoevaluacion_Factor[$i]."',
						Estado = '".$cargoevaluacion_Estado[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	cargos subordinados
		execute("DELETE FROM rh_cargosub WHERE CodCargo = '".$CodCargo."'");
		$_Secuencia = 0;
		for ($i=0; $i < count($cargosub_CargoSubordinado); $i++) {
			if (intval($cargosub_Cantidad[$i]) == 0) die("Debe ingresar la <strong>Cantidad</strong> en la ficha de <strong>Puestos Subordinados</strong>");
			##	inserto
			$sql = "INSERT INTO rh_cargosub
					SET
						CodCargo = '".$CodCargo."',
						CargoSubordinado = '".$cargosub_CargoSubordinado[$i]."',
						Cantidad = '".$cargosub_Cantidad[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	otros estudios
		execute("DELETE FROM rh_cargocursos WHERE CodCargo = '".$CodCargo."'");
		$_Secuencia = 0;
		for ($i=0; $i < count($cargocursos_Curso); $i++) {
			##	inserto
			$sql = "INSERT INTO rh_cargocursos
					SET
						CodCargo = '".$CodCargo."',
						Secuencia = '".++$_Secuencia."',
						Curso = '".$cargocursos_Curso[$i]."',
						TotalHoras = '".$cargocursos_TotalHoras[$i]."',
						AniosVigencia = '".$cargocursos_AniosVigencia[$i]."',
						Observaciones = '".$cargocursos_Observaciones[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	objetivos y/o metas
		execute("DELETE FROM rh_cargometas WHERE CodCargo = '".$CodCargo."'");
		$_Secuencia = 0;
		for ($i=0; $i < count($cargometas_Descripcion); $i++) {
			##	inserto
			$sql = "INSERT INTO rh_cargometas
					SET
						CodCargo = '".$CodCargo."',
						Secuencia = '".++$_Secuencia."',
						Descripcion = '".$cargometas_Descripcion[$i]."',
						FactorParticipacion = '".$cargometas_FactorParticipacion[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	ambiente de trabajo
		execute("DELETE FROM rh_cargoambiente WHERE CodCargo = '".$CodCargo."'");
		$_Secuencia = 0;
		for ($i=0; $i < count($cargoambiente_Ambiente); $i++) {
			##	inserto
			$sql = "INSERT INTO rh_cargoambiente
					SET
						CodCargo = '".$CodCargo."',
						Secuencia = '".++$_Secuencia."',
						Ambiente = '".$cargoambiente_Ambiente[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	habilidades / destrezas
		execute("DELETE FROM rh_cargohabilidades WHERE CodCargo = '".$CodCargo."'");
		$_Secuencia = 0;
		for ($i=0; $i < count($cargohabilidades_Tipo); $i++) {
			##	inserto
			$sql = "INSERT INTO rh_cargohabilidades
					SET
						CodCargo = '".$CodCargo."',
						Secuencia = '".++$_Secuencia."',
						Tipo = '".$cargohabilidades_Tipo[$i]."',
						Descripcion = '".$cargohabilidades_Descripcion[$i]."',
						UltimoUsuario = '".$_SESSION["USUARIO_ACTUAL"]."',
						UltimaFecha = NOW()";
			execute($sql);
		}
		//	-----------------
		mysql_query("COMMIT");
	}
	//	eliminar
	elseif ($accion == "eliminar") {
		mysql_query("BEGIN");
		//	-----------------
		//	actualizo
		$sql = "DELETE FROM rh_puestos WHERE CodCargo = '".$registro."'";
		execute($sql);
		//	-----------------
		mysql_query("COMMIT");
	}
}
elseif ($modulo == "ajax") {
	//	cargos a quien reporta
	if ($accion == "cargoreporta") {
		$sql = "SELECT
					CodCargo,
					CodDesc,
					DescripCargo
				FROM rh_puestos
				WHERE CodCargo = '".$CodCargo."'
				ORDER BY CategoriaCargo, Grado DESC";
		$field_cargoreporta = getRecords($sql);
		foreach ($field_cargoreporta as $f) {
			$id = $f['CodCargo'];
			?>
            <tr class="trListaBody" onclick="clk($(this), 'cargoreporta', 'cargoreporta_<?=$id?>');" id="cargoreporta_<?=$id?>">
                <th>
					<?=$nro_detalles?>
                    <input type="hidden" name="cargoreporta_CargoReporta[]" value="<?=$f['CodCargo']?>" />
                </th>
                <td align="center"><?=$f['CodDesc']?></td>
                <td><?=htmlentities($f['DescripCargo'])?></td>
            </tr>
            <?php
		}
	}
	//	relaciones externas e internas
	elseif ($accion == "cargorelaciones") {
		$id = $nro_detalle;
		?>
		<tr class="trListaBody" onclick="clk($(this), 'cargorelaciones', 'cargorelaciones_<?=$id?>');" id="cargorelaciones_<?=$id?>">
			<th><?=$nro_detalle?></th>
			<td>
                <select name="cargorelaciones_TipoRelacion[]" class="cell" style="text-align:center;">
                    <?=loadSelectValores("tipo-relacion", '')?>
                </select>
            </td>
            <td>
                <input type="text" name="cargorelaciones_EnteRelacionado[]" class="cell" />
            </td>
		</tr>
		<?php
	}
	//	funciones del cargo
	elseif ($accion == "cargofunciones") {
		$id = $nro_detalle;
		?>
        <tr class="trListaBody" onclick="clk($(this), 'cargofunciones', 'cargofunciones_<?=$id?>');" id="cargofunciones_<?=$id?>">
            <td>
                <table width="100%">
                    <tr>
                        <th rowspan="2" width="35"><?=$nro_detalle?></th>
                        <td width="175">
                            <select name="cargofunciones_Funcion[]" class="cell">
                                <?=getMiscelaneos('', "FUNCION")?>
                            </select>
                        </td>
                        <td></td>
                        <td width="75">
                            <select name="cargofunciones_Estado[]" class="cell">
                                <?=loadSelectGeneral("ESTADO", '')?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <textarea name="cargofunciones_Descripcion[]" class="cell" style="height:50px;"></textarea>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
		<?php
	}
	//	formación académica
	elseif ($accion == "cargoformacion") {
		$id = $nro_detalle;
		?>
        <tr class="trListaBody" onclick="clk($(this), 'cargoformacion', 'cargoformacion_<?=$id?>');" id="cargoformacion_<?=$id?>">
                <th><?=$nro_detalle?></th>
                <td>
                    <select name="cargoformacion_CodGradoInstruccion[]" id="CodGradoInstruccion_<?=$id?>" class="cell" onchange="loadSelect($('#CodProfesion_<?=$id?>'), 'tabla=profesion&CodGradoInstruccion='+$('#CodGradoInstruccion_<?=$id?>').val()+'&Area='+$('#Area_<?=$id?>').val(), 1);">
                    	<option value="">&nbsp;</option>
                        <?=loadSelect2("rh_gradoinstruccion", "CodGradoInstruccion", "Descripcion")?>
                    </select>
                </td>
                <td>
                    <select name="cargoformacion_Area[]" id="Area_<?=$id?>" class="cell" onchange="loadSelect($('#CodProfesion_<?=$id?>'), 'tabla=profesion&CodGradoInstruccion='+$('#CodGradoInstruccion_<?=$id?>').val()+'&Area='+$('#Area_<?=$id?>').val(), 1);">
                    	<option value="">&nbsp;</option>
                        <?=getMiscelaneos("", "AREA")?>
                    </select>
                </td>
                <td>
                    <select name="cargoformacion_CodProfesion[]" id="CodProfesion_<?=$id?>" class="cell">
                    	<option value="">&nbsp;</option>
                    </select>
                </td>
            </tr>
		<?php
	}
	//	cursos de informática
	elseif ($accion == "cargoinformat") {
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
            <tr class="trListaBody" onclick="clk($(this), 'cargoinformat', 'cargoinformat_<?=$id?>');" id="cargoinformat_<?=$id?>">
                <th><?=$nro_detalles?></th>
                <td>
                	<input type="hidden" name="cargoinformat_Informatica[]" value="<?=$f['Informatica']?>" />
                    <?=$f['NomInformatica']?>
                </td>
                <td>
                    <select name="cargoinformat_Nivel[]" class="cell">
                        <?=getMiscelaneos('', "NIVEL")?>
                    </select>
                </td>
            </tr>
            <?php
		}
	}
	//	dominio de idiomas
	elseif ($accion == "cargoidioma") {
		$id = $nro_detalle;
		?>
        <tr class="trListaBody" onclick="clk($(this), 'cargoidioma', 'cargoidioma_<?=$id?>');" id="cargoidioma_<?=$id?>">
            <th><?=$nro_detalle?></th>
            <td>
                <select name="cargoidioma_CodIdioma[]" class="cell">
                	<option value="">&nbsp;</option>
                    <?=loadSelect2("mastidioma", "CodIdioma", "DescripcionLocal", $f['CodIdioma'])?>
                </select>
            </td>
            <td>
                <select name="cargoidioma_NivelLectura[]" class="cell">
                    <?=getMiscelaneos($f['NivelLectura'], "NIVEL")?>
                </select>
            </td>
            <td>
                <select name="cargoidioma_NivelOral[]" class="cell">
                    <?=getMiscelaneos($f['NivelOral'], "NIVEL")?>
                </select>
            </td>
            <td>
                <select name="cargoidioma_NivelEscritura[]" class="cell">
                    <?=getMiscelaneos($f['NivelEscritura'], "NIVEL")?>
                </select>
            </td>
            <td>
                <select name="cargoidioma_NivelGeneral[]" class="cell">
                    <?=getMiscelaneos($f['NivelGeneral'], "NIVEL")?>
                </select>
            </td>
        </tr>
		<?php
	}
	//	experiencias previas
	elseif ($accion == "cargoexperiencia") {
		$sql = "SELECT
					CodCargo AS CargoExperiencia,
					DescripCargo AS NomCargoExperiencia
				FROM rh_puestos
				WHERE CodCargo = '".$CodCargo."'";
		$field_cargoexperiencia = getRecords($sql);
		foreach ($field_cargoexperiencia as $f) {
			$id = $nro_detalles;
			?>
            <tr class="trListaBody" onclick="clk($(this), 'cargoexperiencia', 'cargoexperiencia_<?=$id?>');" id="cargoexperiencia_<?=$id?>">
                <th><?=$nro_detalles?></th>
                <td>
                	<input type="hidden" name="cargoexperiencia_CargoExperiencia[]" value="<?=$f['CargoExperiencia']?>" />
                    <?=$f['NomCargoExperiencia']?>
                </td>
                <td>
                    <select name="cargoexperiencia_AreaExperiencia[]" class="cell">
                    	<option value="">&nbsp;</option>
                        <?=getMiscelaneos('', "AREAEXP")?>
                    </select>
                </td>
                <td>
                    <input type="text" name="cargoexperiencia_Meses[]" value="0" class="cell integer" maxlength="3" style="text-align:center;" />
                </td>
                <td align="center">
                    <input type="checkbox" name="cargoexperiencia_FlagNecesario[]" value="S" />
                </td>
            </tr>
            <?php
		}
	}
	//	riesgos de trabajo
	elseif ($accion == "cargoriesgo") {
		$id = $nro_detalle;
		?>
        <tr class="trListaBody" onclick="clk($(this), 'cargoriesgo', 'cargoriesgo_<?=$id?>');" id="cargoriesgo_<?=$id?>">
            <th><?=$nro_detalle?></th>
            <td>
                <select name="cargoriesgo_TipoRiesgo[]" class="cell">
                    <?=getMiscelaneos('', "TRIESGO", 0)?>
                </select>
            </td>
            <td>
                <textarea name="cargoriesgo_Riesgo[]" class="cell" style="height:30px;"></textarea>
            </td>
        </tr>
		<?php
	}
	//	evaluación - reclutamiento
	elseif ($accion == "cargoevaluacion") {
		$sql = "SELECT
					Evaluacion,
					Descripcion AS NomEvaluacion
				FROM rh_evaluacion
				WHERE Evaluacion = '".$Evaluacion."'";
		$field_cargoreporta = getRecords($sql);
		foreach ($field_cargoreporta as $f) {
			$id = $f['Evaluacion'];
			?>
            <tr class="trListaBody" onclick="clk($(this), 'cargoevaluacion', 'cargoevaluacion_<?=$id?>');" id="cargoevaluacion_<?=$id?>">
                <th><?=$nro_detalles?></th>
                <td>
                	<input type="hidden" name="cargoevaluacion_Evaluacion[]" value="<?=$f['Evaluacion']?>" />
                    <?=$f['NomEvaluacion']?>
                </td>
                <td>
                    <input type="text" name="cargoevaluacion_Etapa[]" class="cell integer" maxlength="3" style="text-align:center;" />
                </td>
                <td>
                    <input type="text" name="cargoevaluacion_Factor[]" class="cell integer" maxlength="3" style="text-align:center;" />
                </td>
                <td>
                    <select name="cargoevaluacion_Estado[]" class="cell" <?=$disabled_detalles?>>
                        <?=loadSelectGeneral("ESTADO", 'A')?>
                    </select>
                </td>
            </tr>
            <?php
		}
	}
	//	cargos subordinados
	elseif ($accion == "cargosub") {
		$sql = "SELECT
					CodCargo,
					CodDesc,
					DescripCargo
				FROM rh_puestos
				WHERE CodCargo = '".$CodCargo."'";
		$field_cargoreporta = getRecords($sql);
		foreach ($field_cargoreporta as $f) {
			$id = $f['CodCargo'];
			?>
            <tr class="trListaBody" onclick="clk($(this), 'cargosub', 'cargosub_<?=$id?>');" id="cargosub_<?=$id?>">
                <th>
					<?=$nro_detalles?>
                    <input type="hidden" name="cargosub_CargoSubordinado[]" value="<?=$f['CodCargo']?>" />
                </th>
                <td align="center"><?=$f['CodDesc']?></td>
                <td><?=htmlentities($f['DescripCargo'])?></td>
                <td>
                	<input type="text" name="cargosub_Cantidad[]" value="1" class="cell integer" maxlength="4" style="text-align:center;" />
				</td>
            </tr>
            <?php
		}
	}
	//	otros estudios
	elseif ($accion == "cargocursos") {
		$sql = "SELECT
					CodCurso,
					Descripcion
				FROM rh_cursos
				WHERE CodCurso = '".$CodCurso."'";
		$field_cargocursos = getRecords($sql);
		foreach ($field_cargocursos as $f) {
			$id = $f['CodCurso'];
			?>
            <tr class="trListaBody" onclick="clk($(this), 'cargocursos', 'cargocursos_<?=$id?>');" id="cargocursos_<?=$id?>">
            	<td>
                	<table width="100%">
                    	<tr>
                            <th rowspan="2" width="30">
                                <?=$nro_detalles?>
                                <input type="hidden" name="cargocursos_Curso[]" value="<?=$f['CodCurso']?>" />
                            </th>
                            <th width="50">
								Curso:
                            </th>
                            <td><?=htmlentities($f['Descripcion'])?></td>
                            <th width="50">
								Horas:
                            </th>
                            <td width="50">
                                <input type="text" name="cargocursos_TotalHoras[]" class="cell integer" value="0" maxlength="4" style="text-align:center; width:90%;" />
                            </td>
                            <th width="50">
								A&ntilde;os:
                            </th>
                            <td width="50">
                                <input type="text" name="cargocursos_AniosVigencia[]" value="0" class="cell integer" maxlength="2" style="text-align:center;" />
                            </td>
                        </tr>
                        <tr>
                            <th width="65">
								Observaciones:
                            </th>
                            <td colspan="5">
                                <textarea name="cargocursos_Observaciones[]" class="cell" style="height:30px;"></textarea>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <?php
		}
	}
	//	objetivos y/o metas
	elseif ($accion == "cargometas") {
		$id = $nro_detalle;
		?>
		<tr class="trListaBody" onclick="clk($(this), 'cargometas', 'cargometas_<?=$id?>');" id="cargometas_<?=$id?>">
            <th><?=$nro_detalle?></th>
            <td>
                <input type="text" name="cargometas_Descripcion[]" class="cell" maxlength="50" />
            </td>
            <td>
                <input type="text" name="cargometas_FactorParticipacion[]" class="cell integer" maxlength="8" style="text-align:center;" />
            </td>
        </tr>
		<?php
	}
	//	ambiente de trabajo
	elseif ($accion == "cargoambiente") {
		$id = $nro_detalle;
		?>
		<tr class="trListaBody" onclick="clk($(this), 'cargoambiente', 'cargoambiente_<?=$id?>');" id="cargoambiente_<?=$id?>">
            <th width="30"><?=$nro_detalle?></th>
            <td>
                <textarea name="cargoambiente_Ambiente[]" class="cell" style="height:30px;"></textarea>
            </td>
        </tr>
		<?php
	}
	//	habilidades / destrezas
	elseif ($accion == "cargohabilidades") {
		$id = $nro_detalle;
		?>
		<tr class="trListaBody" onclick="clk($(this), 'cargohabilidades', 'cargohabilidades_<?=$id?>');" id="cargohabilidades_<?=$id?>">
            <th><?=$nro_detalle?></th>
            <td>
                <select name="cargohabilidades_Tipo[]" class="cell">
                    <?=loadSelectValores("tipo-habilidad", '')?>
                </select>
            </td>
            <td>
                <textarea name="cargohabilidades_Descripcion[]" class="cell" style="height:30px;"></textarea>
            </td>
        </tr>
		<?php
	}
}
?>