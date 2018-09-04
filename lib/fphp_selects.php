<?php
include("fphp.php");
//	----------------
if ($tabla == "dependencia") {
	?><option value="">&nbsp;</option><?php
	loadSelectDependiente("mastdependencias", "CodDependencia", "Dependencia", "CodOrganismo", "", $opcion, 0);
}

elseif ($tabla == "dependencia_filtro") {
	?><option value="">&nbsp;</option><?php
	getDependencias("", $opcion, 3);
}

elseif ($tabla == "dependencia_fiscal") {
	?><option value="">&nbsp;</option><?php
	loadDependenciaFiscal("", $opcion, 0);
}

elseif ($tabla == "periodo") {
	?><option value="">&nbsp;</option><?php
	loadSelectNominaPeriodos($opcion1, $opcion2, 0);
}

elseif ($tabla == "estado") {
	?><option value="">&nbsp;</option><?php
	loadSelectDependienteEstado("", $opcion, 0);
}

elseif ($tabla == "municipio") {
	?><option value="">&nbsp;</option><?php
	loadSelectDependiente("mastmunicipios", "CodMunicipio", "Municipio", "CodEstado", "", $opcion, 0);
}

elseif ($tabla == "ciudad") {
	?><option value="">&nbsp;</option><?php
	loadSelectDependiente("mastciudades", "CodCiudad", "Ciudad", "CodMunicipio", "", $opcion, 0);
}

elseif ($tabla == "parroquia") {
	?><option value="">&nbsp;</option><?php
	loadSelect2("mastparroquias", "CodParroquia", "Descripcion", "", 0, array('CodMunicipio'), array($opcion));
}

elseif ($tabla == "comunidad") {
	?><option value="">&nbsp;</option><?php
	loadSelect2("mastcomunidades", "CodComunidad", "Descripcion", "", 0, array('CodParroquia'), array($opcion));
}

elseif ($tabla == "centro_costo") {
	?><option value="">&nbsp;</option><?php
	loadSelectDependiente("ac_mastcentrocosto", "CodCentroCosto", "Descripcion", "CodDependencia", "", $opcion, 0);
}

elseif ($tabla == "fases") {
	?><option value="">&nbsp;</option><?php
	loadSelectDependiente("pf_fases", "CodFase", "Descripcion", "CodProceso", "", $opcion, 0);
}

elseif ($tabla == "subgrupocc") {
	?><option value="">&nbsp;</option><?php
	loadSelectDependiente("ac_subgrupocentrocosto", "CodSubGrupoCentroCosto", "Descripcion", "CodGrupoCentroCosto", "", $opcion, 0);
}

elseif ($tabla == "periodo_evaluacion") {
	?><option value="">&nbsp;</option><?php
	loadSelectDependiente("rh_evaluacionperiodo", "Periodo", "Periodo", "CodOrganismo", "", $opcion, 0);
}

elseif ($tabla == "subgrupocentrocosto") {
	?><option value="">&nbsp;</option><?php
	loadSelectDependiente("ac_subgrupocentrocosto", "CodSubGrupoCentroCosto", "Descripcion", "CodGrupoCentroCosto", "", $opcion, 0);
}

elseif ($tabla == "tipo_servicio_documento") {
	loadSelectTipoServicioDocumento($opcion, 0);
}

elseif ($tabla == "cuentas_bancarias") {
	?><option value="">&nbsp;</option><?php
	loadSelectDependiente("ap_ctabancaria", "NroCuenta", "NroCuenta", "CodBanco", "", $opcion, 0);
}

elseif ($tabla == "centro_costo") {
	?><option value="">&nbsp;</option><?php
	loadSelectDependiente("ac_mastcentrocosto", "CodCentroCosto", "Descripcion", "CodDependencia", "", $opcion, 0);
}

elseif ($tabla == "familia") {
	?><option value="">&nbsp;</option><?php
	loadSelectDependiente("lg_clasefamilia", "CodFamilia", "Descripcion", "CodLinea", "", $opcion, 0);
}

elseif ($tabla == "profesiones") {
	?><option value="">&nbsp;</option><?php
	loadSelectDependiente2("rh_profesiones", "CodProfesion", "Descripcion", "CodGradoInstruccion", "Area", "", $opcion1, $opcion2, 0);
}

elseif ($tabla == "profesion") {
	?><option value="">&nbsp;</option><?php
	loadSelectProfesiones("", $CodGradoInstruccion, $Area, 0);
}

elseif ($tabla == "nivel-instruccion") {
	?><option value="">&nbsp;</option><?php
	loadSelectDependiente("rh_nivelgradoinstruccion", "Nivel", "Descripcion", "CodGradoInstruccion", "", $opcion, 0);
}

elseif ($tabla == "serie-ocupacional") {
	?><option value="">&nbsp;</option><?php
	loadSelectDependiente("rh_serieocupacional", "CodSerieOcup", "SerieOcup", "CodGrupOcup", "", $opcion, 0);
}

elseif ($tabla == "cargo") {
	?><option value="">&nbsp;</option><?php
	loadSelectDependiente("rh_puestos", "CodCargo", "DescripCargo", "CodSerieOcup", "", $opcion, 0);
}

elseif ($tabla == "loadSelectPeriodosNomina") {
	?><option value="">&nbsp;</option><?php
	loadSelectPeriodosNomina("", $CodOrganismo, $CodTipoNom, $opt);
}

elseif ($tabla == "loadSelectPeriodosNominaProcesos") {
	loadSelectPeriodosNominaProcesos("", $Periodo, $CodOrganismo, $CodTipoNom, $opt);
}

elseif ($tabla == "tipo-servicio") {
	?><option value="">&nbsp;</option><?php
	loadSelectDependiente("masttiposervicio", "CodTipoServicio", "Descripcion", "CodRegimenFiscal", "", $opcion, 0);
}

elseif ($tabla == "loadNominaPeriodos") {
	?><option value="">&nbsp;</option><?php
	loadNominaPeriodos($opcion, "", 0);
}

elseif ($tabla == "loadControlNominas") {
	?><option value="">&nbsp;</option><?php
	 loadControlNominas($CodOrganismo, "");
}

elseif ($tabla == "loadControlPeriodos") {
	?><option value="">&nbsp;</option><?php
	loadControlPeriodos($CodOrganismo, $CodTipoNom, "");
}

elseif ($tabla == "loadControlPeriodosAnio") {
	?><option value="">&nbsp;</option><?php
	loadControlPeriodosAnio($CodOrganismo, $CodTipoNom, "");
}

elseif ($tabla == "loadControlPeriodosMes") {
	?><option value="">&nbsp;</option><?php
	loadControlPeriodosMes($CodOrganismo, $CodTipoNom, $Anio, "");
}

elseif ($tabla == "loadControlProcesos") {
	?><option value="">&nbsp;</option><?php
	loadControlProcesos($CodOrganismo, $CodTipoNom, $Periodo, "");
}

elseif ($tabla == "loadControlNominas2") {
	?><option value="">&nbsp;</option><?php
	 loadControlNominas2($CodOrganismo, "");
}

elseif ($tabla == "loadControlPeriodos2") {
	?><option value="">&nbsp;</option><?php
	loadControlPeriodos2($CodOrganismo, $CodTipoNom, "");
}

elseif ($tabla == "loadControlProcesos2") {
	?><option value="">&nbsp;</option><?php
	loadControlProcesos2($CodOrganismo, $CodTipoNom, $Periodo, "");
}

elseif ($tabla == "loadControlNominas3") {
	?><option value="">&nbsp;</option><?php
	 loadControlNominas3($CodOrganismo, "");
}

elseif ($tabla == "loadControlPeriodos3") {
	?><option value="">&nbsp;</option><?php
	loadControlPeriodos3($CodOrganismo, $CodTipoNom, "");
}

elseif ($tabla == "loadControlProcesos3") {
	?><option value="">&nbsp;</option><?php
	loadControlProcesos3($CodOrganismo, $CodTipoNom, $Periodo, "");
}

elseif ($tabla == "loadControlNominasPrenomina") {
	?><option value="">&nbsp;</option><?php
	 loadControlNominasPrenomina($CodOrganismo, "");
}

elseif ($tabla == "loadControlPeriodosPrenomina") {
	?><option value="">&nbsp;</option><?php
	loadControlPeriodosPrenomina($CodOrganismo, $CodTipoNom, "");
}

elseif ($tabla == "loadControlProcesosPrenomina") {
	?><option value="">&nbsp;</option><?php
	loadControlProcesosPrenomina($CodOrganismo, $CodTipoNom, $Periodo, "");
}

elseif ($tabla == "lg_commoditymast") {
	?><option value="">&nbsp;</option><?php
	loadSelect2("lg_commoditymast", "CommodityMast", "Descripcion", "", 0, array('Clasificacion'), array($fClasificacion));
}

elseif ($tabla == "rh_serieocupacional") {
	?><option value="">&nbsp;</option><?php
	loadSelect2("rh_serieocupacional", "CodSerieOcup", "SerieOcup", "", 0, array('CodGrupOcup'), array($CodGrupOcup));
}

elseif ($tabla == "rh_nivelclasecargo") {
	?><option value="">&nbsp;</option><?php
	loadSelect2("rh_nivelclasecargo", "CodNivelClase", "NivelClase", "", 0, array('CodTipoCargo'), array($CodTipoCargo));
}

elseif ($tabla == "rh_nivelsalarial") {
	?><option value="">&nbsp;</option><?php
	loadSelect2("rh_nivelsalarial", "Grado", "Grado", "", 0, array('CategoriaCargo'), array($CategoriaCargo));
}

elseif ($tabla == "gradosalarial") {
	?><option value="">&nbsp;</option><?php
	loadSelect2("rh_nivelsalarial", "Grado", "Grado", "", 30, array('CategoriaCargo'), array($CategoriaCargo));
}

elseif ($tabla == "pasosalarial") {
	?><option value="">&nbsp;</option><?php
	loadSelect2("rh_nivelsalarial", "Paso", "Paso", "", 0, array('CategoriaCargo','Grado'), array($CategoriaCargo,$Grado));
}

elseif ($tabla == "at_tiposoftware") {
	?><option value="">&nbsp;</option><?php
	loadSelect2("at_tiposoftware", "CodTiposoftware", "Descripcion", "", 0, array('Categoria'), array($Categoria));
}

elseif ($tabla == "loadSelectPeriodosBonoAnio") {
	?><option value="">&nbsp;</option><?php
	loadSelectPeriodosBonoAnio('', $CodOrganismo, $CodTipoNom);
}

elseif ($tabla == "loadSelectPeriodosBonoMes") {
	?><option value="">&nbsp;</option><?php
	loadSelectPeriodosBonoMes($Anio, '', $CodOrganismo, $CodTipoNom);
}

elseif ($tabla == "periodo-bono") {
	?><option value="">&nbsp;</option><?php
	loadSelectPeriodosBono("", $CodOrganismo, $CodTipoNom, 0);
}

elseif ($tabla == "loadSelectSemanasBono") {
	loadSelectSemanasBono($Semana, $Periodo, 0);
}

elseif ($tabla == "pv_subsector") {
	?><option value="">&nbsp;</option><?php
	loadSelect2('pv_subsector','IdSubSector','Denominacion','',0,['CodSector'],[$CodSector],'CodClaSectorial');
}

elseif ($tabla == "pv_programas") {
	?><option value="">&nbsp;</option><?php
	loadSelect2('pv_programas','IdPrograma','Denominacion','',0,['IdSubSector'],[$IdSubSector],'CodPrograma');
}

elseif ($tabla == "pv_subprogramas") {
	?><option value="">&nbsp;</option><?php
	loadSelect2('pv_subprogramas','IdSubPrograma','Denominacion','',0,['IdPrograma'],[$IdPrograma],'CodSubPrograma');
}

elseif ($tabla == "pv_proyectos") {
	?><option value="">&nbsp;</option><?php
	loadSelect2('pv_proyectos','IdProyecto','Denominacion','',0,['IdSubPrograma'],[$IdSubPrograma],'CodProyecto');
}

elseif ($tabla == "pv_actividades") {
	?><option value="">&nbsp;</option><?php
	loadSelect2('pv_actividades','IdActividad','Denominacion','',0,['IdProyecto'],[$IdProyecto],'CodActividad');
}

elseif ($tabla == "pv_unidadejecutora") {
	?><option value="">&nbsp;</option><?php
	loadSelect2('pv_unidadejecutora','CodUnidadEjec','Denominacion','',10,['CodOrganismo'],[$CodOrganismo]);
}

elseif ($tabla == "mastestados") {
	?><option value="">&nbsp;</option><?php
	loadSelect2('mastestados','CodEstado','Estado',$CodEstado,0,['CodPais'],[$CodPais]);
}

elseif ($tabla == "mastmunicipios") {
	?><option value="">&nbsp;</option><?php
	loadSelect2('mastmunicipios','CodMunicipio','Municipio',$CodMunicipio,0,['CodEstado'],[$CodEstado]);
}

elseif ($tabla == "mastparroquias") {
	?><option value="">&nbsp;</option><?php
	loadSelect2('mastparroquias','CodParroquia','Descripcion','',0,['CodMunicipio'],[$CodMunicipio]);
}

elseif ($tabla == "mastcomunidades") {
	?><option value="">&nbsp;</option><?php
	loadSelect2('mastcomunidades','CodComunidad','Descripcion','',0,['CodParroquia'],[$CodParroquia]);
}

elseif ($tabla == "mastlocalidades") {
	?><option value="">&nbsp;</option><?php
	loadSelect2('mastlocalidades','CodLocalidad','Descripcion','',0,['CodParroquia'],[$CodParroquia]);
}

elseif ($tabla == "mastciudades") {
	?><option value="">&nbsp;</option><?php
	loadSelect2('mastciudades','CodCiudad','Ciudad',$CodCiudad,0,['CodMunicipio'],[$CodMunicipio]);
}

elseif ($tabla == "mastdependencias") {
	?><option value="">&nbsp;</option><?php
	loadSelect2('mastdependencias','CodDependencia','Dependencia','',0,['CodOrganismo'],[$CodOrganismo]);
}

elseif ($tabla == "ac_mastcentrocosto") {
	?><option value="">&nbsp;</option><?php
	loadSelect2('ac_mastcentrocosto','CodCentroCosto','Descripcion','',0,['CodDependencia'],[$CodDependencia]);
}

elseif ($tabla == "pv_metaspoa") {
	?><option value="">&nbsp;</option><?php
	loadSelect2('pv_metaspoa','CodMeta','NroMeta','',0,['CodObjetivo'],[$CodObjetivo]);
}

elseif ($tabla == "loadSelectUnidadEjecutora") {
	?><option value="">&nbsp;</option><?php
	loadSelectUnidadEjecutora('',0,$CodDependencia);
}

elseif ($tabla == "rh_formatocontrato") {
	?><option value="">&nbsp;</option><?php
	loadSelect2('rh_formatocontrato','CodFormato','Documento','',0,['TipoContrato'],[$TipoContrato],'CodFormato');
}

elseif ($tabla == "loadBonoNomina") {
	?><option value="">&nbsp;</option><?php
	loadBonoNomina($CodOrganismo);
}

elseif ($tabla == "loadBonoAnio") {
	?><option value="">&nbsp;</option><?php
	loadBonoAnio($CodOrganismo, $CodTipoNom);
}

elseif ($tabla == "loadBonoMes") {
	?><option value="">&nbsp;</option><?php
	loadBonoMes($CodOrganismo, $CodTipoNom, $Anio);
}

elseif ($tabla == "loadBonoProceso") {
	?><option value="">&nbsp;</option><?php
	loadBonoProceso($CodOrganismo, $CodTipoNom, $Anio, $Mes);
}

elseif ($tabla == "ubicacion_ciudad") {
	?><option value="">&nbsp;</option><?php
	loadSelect2('mastestados','CodEstado','Estado',$CodEstado,0,['CodPais'],[$CodPais]);

	?>|<option value="">&nbsp;</option><?php
	loadSelect2('mastmunicipios','CodMunicipio','Municipio',$CodMunicipio,0,['CodEstado'],[$CodEstado]);

	?>|<option value="">&nbsp;</option><?php
	loadSelect2('mastciudades','CodCiudad','Ciudad',$CodCiudad,0,['CodMunicipio'],[$CodMunicipio]);
}

elseif ($tabla == "lg_clasefamilia") {
	?><option value="">&nbsp;</option><?php
	loadSelect2('lg_clasefamilia','CodFamilia','Descripcion',$CodFamilia,10,['CodLinea'],[$CodLinea]);
}

elseif ($tabla == "lg_clasesubfamilia") {
	?><option value="">&nbsp;</option><?php
	loadSelect2('lg_clasesubfamilia','CodSubFamilia','Descripcion',$CodSubFamilia,10,['CodLinea','CodFamilia'],[$CodLinea,$CodFamilia]);
}
?>