<?php
if ($opcion == "nuevo") {
	$field['CodOrganismo'] = ($fCodOrganismo?$fCodOrganismo:$_SESSION["FILTRO_ORGANISMO_ACTUAL"]);
	$field['Ejercicio'] = $AnioActual;
	$field['FechaInicio'] = $AnioActual.'-01-01';
	$field['FechaFin'] = $AnioActual.'-12-31';
	$field['PreparadoPor'] = $_SESSION['CODPERSONA_ACTUAL'];
	$field['NomPreparadoPor'] = $_SESSION['NOMBRE_USUARIO_ACTUAL'];
	$field['FechaPreparado'] = $FechaActual;
	$field['Estado'] = 'PR';
	##	
	$sql = "SELECT
				p.cod_partida,
				p.denominacion,
				p.cod_tipocuenta,
				p.partida1,
				p.generica,
				p.especifica,
				p.subespecifica,
				p.tipo,
				'0.00' AS MontoPresupuestado,
				'0.00' AS MontoAprobado,
				'$_PARAMETRO[FFMETASDEF]' AS CodFuente
			FROM pv_partida p
			WHERE p.cod_tipocuenta = '4'";
	$field_partida = getRecords($sql);
	##
	$_titulo = "Anteproyecto de Presupuesto / Crear";
	$accion = "nuevo";
	$disabled_modificar = "";
	$disabled_ver = "";
	$disabled_insertar = "";
	$disabled_borrar = "";
	$disabled_aprobar = "disabled";
	$disabled_generar = "disabled";
	$readonly_modificar = "";
	$readonly_ver = "";
	$opt_codigo = 3;
	$display_modificar = "";
	$display_submit = "";
	$label_submit = "Guardar";
	$focus = "CodSector";
}
elseif ($opcion == "modificar" || $opcion == "ver" || $opcion == "revisar" || $opcion == "aprobar" || $opcion == "generar" || $opcion == "anular") {
	list($CodOrganismo, $CodProyPresupuesto) = explode('_', $sel_registros);
	##	consulto datos generales
	$sql = "SELECT
				pp.*,
				cp.IdActividad,
				cp.CodUnidadEjec,
				a.IdProyecto,
				py.IdSubPrograma,
				sp.IdPrograma,
				pg.IdSubSector,
				ss.CodSector,
				p1.NomCompleto AS NomPreparadoPor,
				p2.NomCompleto AS NomRevisadoPor,
				p3.NomCompleto AS NomAprobadoPor,
				p4.NomCompleto AS NomGeneradoPor
			FROM 
				pv_proyectopresupuesto pp
				INNER JOIN pv_categoriaprog cp ON (cp.CategoriaProg = pp.CategoriaProg)
				INNER JOIN pv_actividades a ON (a.IdActividad = cp.IdActividad)
				INNER JOIN pv_proyectos py ON (py.IdProyecto = a.IdProyecto)
				INNER JOIN pv_subprogramas sp ON (sp.IdSubPrograma = py.IdSubPrograma)
				INNER JOIN pv_programas pg ON (pg.IdPrograma = sp.IdPrograma)
				INNER JOIN pv_subsector ss ON (ss.IdSubSector = pg.IdSubSector)
				LEFT JOIN mastpersonas p1 ON (p1.CodPersona = pp.PreparadoPor)
				LEFT JOIN mastpersonas p2 ON (p2.CodPersona = pp.RevisadoPor)
				LEFT JOIN mastpersonas p3 ON (p3.CodPersona = pp.AprobadoPor)
				LEFT JOIN mastpersonas p4 ON (p4.CodPersona = pp.GeneradoPor)
			WHERE
				pp.CodOrganismo = '".$CodOrganismo."' AND
				pp.CodProyPresupuesto = '".$CodProyPresupuesto."'";
	$field = getRecord($sql);
	##	dependencias de la unidad ejecutora
	$sql = "SELECT
				ued.CodDependencia,
				d.Dependencia
			FROM
				pv_unidadejecutoradep ued 
				INNER JOIN mastdependencias d ON (d.CodDependencia = ued.CodDependencia)
			WHERE ued.CodUnidadEjec = '".$field['CodUnidadEjec']."'";
	$field_dependencias = getRecords($sql);
	##	
	$filtro = " AND (CodOrganismo = '$field[CodOrganismo]' AND CodProyPresupuesto = '$field[CodProyPresupuesto]')";
	$sql = "(SELECT
				p.cod_partida,
				p.denominacion,
				p.cod_tipocuenta,
				p.partida1,
				p.generica,
				p.especifica,
				p.subespecifica,
				'T' AS tipo,
				(SELECT SUM(MontoPresupuestado) FROM pv_proyectopresupuestodet WHERE cod_partida LIKE '4%' $filtro) AS MontoPresupuestado,
				(SELECT SUM(MontoAprobado) FROM pv_proyectopresupuestodet WHERE cod_partida LIKE '4%' $filtro) AS MontoAprobado,
				'N' AS FlagAnexa,
				'' AS CodFuente
			 FROM pv_partida p
			 WHERE
				p.cod_tipocuenta = '4' AND
				p.partida1 = '00' AND
				p.generica = '00' AND
				p.especifica = '00' AND
				p.subespecifica = '00' AND
				SUBSTRING(p.cod_partida, 1, 1) IN (SELECT SUBSTRING(cod_partida, 1, 1) AS partida FROM pv_proyectopresupuestodet WHERE 1 $filtro GROUP BY partida)
			 GROUP BY cod_partida)
			UNION
			(SELECT
				p.cod_partida,
				p.denominacion,
				p.cod_tipocuenta,
				p.partida1,
				p.generica,
				p.especifica,
				p.subespecifica,
				'T' AS tipo,
				(SELECT SUM(MontoPresupuestado) FROM pv_proyectopresupuestodet WHERE cod_partida LIKE CONCAT('4', p.partida1, '.%') $filtro) AS MontoPresupuestado,
				(SELECT SUM(MontoAprobado) FROM pv_proyectopresupuestodet WHERE cod_partida LIKE CONCAT('4', p.partida1, '.%') $filtro) AS MontoAprobado,
				'N' AS FlagAnexa,
				'' AS CodFuente
			 FROM pv_partida p
			 WHERE
				p.cod_tipocuenta = '4' AND
				p.partida1 <> '00' AND
				p.generica = '00' AND
				p.especifica = '00' AND
				p.subespecifica = '00' AND
				SUBSTRING(p.cod_partida, 1, 3) IN (SELECT SUBSTRING(cod_partida, 1, 3) AS partida FROM pv_proyectopresupuestodet WHERE 1 $filtro GROUP BY partida)
			 GROUP BY cod_partida)
			UNION
			(SELECT
				p.cod_partida,
				p.denominacion,
				p.cod_tipocuenta,
				p.partida1,
				p.generica,
				p.especifica,
				p.subespecifica,
				'T' AS tipo,
				(SELECT SUM(MontoPresupuestado) FROM pv_proyectopresupuestodet WHERE cod_partida LIKE CONCAT('4', p.partida1, '.', p.generica, '.%') $filtro) AS MontoPresupuestado,
				(SELECT SUM(MontoAprobado) FROM pv_proyectopresupuestodet WHERE cod_partida LIKE CONCAT('4', p.partida1, '.', p.generica, '.%') $filtro) AS MontoAprobado,
				'N' AS FlagAnexa,
				'' AS CodFuente
			 FROM pv_partida p
			 WHERE
				p.cod_tipocuenta = '4' AND
				p.partida1 <> '00' AND
				p.generica <> '00' AND
				p.especifica = '00' AND
				p.subespecifica = '00' AND
				SUBSTRING(p.cod_partida, 1, 7) IN (SELECT SUBSTRING(cod_partida, 1, 7) AS partida FROM pv_proyectopresupuestodet WHERE 1 $filtro GROUP BY partida)
			 GROUP BY cod_partida)
			UNION
			(SELECT
				p.cod_partida,
				p.denominacion,
				p.cod_tipocuenta,
				p.partida1,
				p.generica,
				p.especifica,
				p.subespecifica,
				p.tipo,
				ppd.MontoPresupuestado,
				ppd.MontoAprobado,
				ppd.FlagAnexa,
				ppd.CodFuente
			 FROM
			 	pv_partida p
			 	INNER JOIN pv_proyectopresupuestodet ppd ON (ppd.cod_partida = p.cod_partida)
			 WHERE 
			 	p.cod_tipocuenta = '4' AND
			 	ppd.CodOrganismo = '$field[CodOrganismo]' AND
			 	ppd.CodProyPresupuesto = '$field[CodProyPresupuesto]')
			ORDER BY cod_partida, CodFuente";
	$field_partida = getRecords($sql);
	##	modificar
	if ($opcion == "modificar") {
		$_titulo = "Anteproyecto de Presupuesto / Modificar";
		$accion = "modificar";
		$disabled_modificar = "disabled";
		$disabled_ver = "";
		$disabled_insertar = "";
		$disabled_borrar = "";
		$disabled_aprobar = "disabled";
		$disabled_generar = "disabled";
		$readonly_modificar = "readonly";
		$readonly_ver = "";
		$opt_codigo = 1;
		$display_modificar = "display:none;";
		$display_submit = "";
		$label_submit = "Modificar";
		$focus = "NroGaceta";
	}
	##	ver
	elseif ($opcion == "ver") {
		$_titulo = "Anteproyecto de Presupuesto / Ver";
		$accion = "";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$disabled_insertar = "disabled";
		$disabled_borrar = "disabled";
		$disabled_aprobar = "disabled";
		$disabled_generar = "disabled";
		$readonly_modificar = "readonly";
		$readonly_ver = "readonly";
		$opt_codigo = 1;
		$display_modificar = "display:none;";
		$display_submit = "display:none;";
		$label_submit = "";
		$focus = "btCancelar";
	}
	##	revisar
	elseif ($opcion == "revisar") {
		$field['RevisadoPor'] = $_SESSION['CODPERSONA_ACTUAL'];
		$field['NomRevisadoPor'] = $_SESSION['NOMBRE_USUARIO_ACTUAL'];
		$field['FechaRevisado'] = $FechaActual;
		$field['Estado'] = 'RV';
		##	
		$_titulo = "Anteproyecto de Presupuesto / Revisar";
		$accion = "revisar";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$disabled_insertar = "disabled";
		$disabled_borrar = "disabled";
		$disabled_aprobar = "disabled";
		$disabled_generar = "disabled";
		$readonly_modificar = "readonly";
		$readonly_ver = "readonly";
		$opt_codigo = 1;
		$display_modificar = "display:none;";
		$display_submit = "";
		$label_submit = "Revisar";
		$focus = "btSubmit";
	}
	##	aprobar
	elseif ($opcion == "aprobar") {
		$field['AprobadoPor'] = $_SESSION['CODPERSONA_ACTUAL'];
		$field['NomAprobadoPor'] = $_SESSION['NOMBRE_USUARIO_ACTUAL'];
		$field['FechaAprobado'] = $FechaActual;
		$field['Estado'] = 'AP';
		##	
		$_titulo = "Anteproyecto de Presupuesto / Aprobar";
		$accion = "aprobar";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$disabled_insertar = "disabled";
		$disabled_borrar = "disabled";
		$disabled_aprobar = "";
		$disabled_generar = "disabled";
		$readonly_modificar = "readonly";
		$readonly_ver = "readonly";
		$opt_codigo = 1;
		$display_modificar = "display:none;";
		$display_submit = "";
		$label_submit = "Aprobar";
		$focus = "btSubmit";
	}
	##	generar
	elseif ($opcion == "generar") {
		$field['GeneradoPor'] = $_SESSION['CODPERSONA_ACTUAL'];
		$field['NomGeneradoPor'] = $_SESSION['NOMBRE_USUARIO_ACTUAL'];
		$field['FechaGenerado'] = $FechaActual;
		$field['Estado'] = 'GE';
		$field['MontoAprobado'] = $field['MontoProyecto'];
		##	
		$_titulo = "Presupuesto / Generar";
		$accion = "generar";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$disabled_insertar = "";
		$disabled_borrar = "disabled";
		$disabled_aprobar = "disabled";
		$disabled_generar = "";
		$readonly_modificar = "readonly";
		$readonly_ver = "readonly";
		$opt_codigo = 1;
		$display_modificar = "display:none;";
		$display_submit = "";
		$label_submit = "Generar";
		$focus = "btSubmit";
	}
	##	anular
	elseif ($opcion == "anular") {
		$field['AnuladoPor'] = $_SESSION['CODPERSONA_ACTUAL'];
		$field['NomAnuladoPor'] = $_SESSION['NOMBRE_USUARIO_ACTUAL'];
		$field['FechaAnulado'] = $FechaActual;
		##	
		$_titulo = "Anteproyecto de Presupuesto / Anular";
		$accion = "anular";
		$disabled_modificar = "disabled";
		$disabled_ver = "disabled";
		$disabled_insertar = "disabled";
		$disabled_borrar = "disabled";
		$disabled_aprobar = "disabled";
		$disabled_generar = "disabled";
		$readonly_modificar = "readonly";
		$readonly_ver = "readonly";
		$opt_codigo = 1;
		$display_modificar = "display:none;";
		$display_submit = "";
		$label_submit = "Anular";
		$focus = "btSubmit";
	}
}
elseif ($opcion == "generar-anteproyecto") {
	list($Ejercicio, $CategoriaProg) = explode('_', $sel_registros);
	$sql = "SELECT
			    fmdt.Ejercicio,
				fmdt.CategoriaProg,
			    cp.IdActividad,
			    a.CodActividad,
			    a.Denominacion AS Actividad,
			    cp.CodUnidadEjec,
			    ue.CodOrganismo,
			    ue.Denominacion AS UnidadEjecutora,
				a.IdProyecto,
				py.IdSubPrograma,
				spg.IdPrograma,
				pg.IdSubSector,
				ss.CodSector,
			    SUM(fmdt.Monto) AS Monto
			FROM
				vw_004formulacionmetasdist fmdt
			    INNER JOIN pv_categoriaprog cp ON (cp.CategoriaProg = fmdt.CategoriaProg)
			    INNER JOIN pv_actividades a ON (a.IdActividad = cp.IdActividad)
				INNER JOIN pv_proyectos py ON (py.IdProyecto = a.IdProyecto)
				INNER JOIN pv_subprogramas spg ON (spg.IdSubPrograma = py.IdSubPrograma)
				INNER JOIN pv_programas pg ON (pg.IdPrograma = spg.IdPrograma)
				INNER JOIN pv_subsector ss ON (ss.IdSubSector = pg.IdSubSector)
			    INNER JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cp.CodUnidadEjec)
			    LEFT JOIN pv_unidadejecutoradep ued ON (ued.CodUnidadEjec = ue.CodUnidadEjec)
			WHERE
				fmdt.Ejercicio = '$Ejercicio' AND
				fmdt.CategoriaProg = '$CategoriaProg'";
	$field = getRecord($sql);
	##	
	$field['FechaInicio'] = $field['Ejercicio'].'-01-01';
	$field['FechaFin'] = $field['Ejercicio'].'-12-31';
	$field['PreparadoPor'] = $_SESSION['CODPERSONA_ACTUAL'];
	$field['NomPreparadoPor'] = $_SESSION['NOMBRE_USUARIO_ACTUAL'];
	$field['FechaPreparado'] = $FechaActual;
	$field['Estado'] = 'PR';
	$field['MontoProyecto'] = getVar3("SELECT SUM(Monto) FROM vw_004formulacionmetasdist WHERE Ejercicio = '$Ejercicio' AND CategoriaProg = '$CategoriaProg'");
	##	dependencias de la unidad ejecutora
	$sql = "SELECT
				ued.CodDependencia,
				d.Dependencia
			FROM
				pv_unidadejecutoradep ued 
				INNER JOIN mastdependencias d ON (d.CodDependencia = ued.CodDependencia)
			WHERE ued.CodUnidadEjec = '".$field['CodUnidadEjec']."'";
	$field_dependencias = getRecords($sql);
	##	
	$filtro = " AND Ejercicio = '$Ejercicio' AND CategoriaProg = '$CategoriaProg'";
	$sql = "(SELECT
				p.cod_partida,
				p.denominacion,
				p.cod_tipocuenta,
				p.partida1,
				p.generica,
				p.especifica,
				p.subespecifica,
				'T' AS tipo,
				(SELECT SUM(Monto) FROM vw_004formulacionmetasdist WHERE cod_partida LIKE '4%' $filtro) AS MontoPresupuestado,
				'0.00' AS MontoAprobado
			 FROM pv_partida p
			 WHERE
				p.partida1 = '00' AND
				p.generica = '00' AND
				p.especifica = '00' AND
				p.subespecifica = '00' AND
				SUBSTRING(p.cod_partida, 1, 1) IN (SELECT SUBSTRING(cod_partida, 1, 1) AS partida FROM vw_004formulacionmetasdist WHERE Monto > 0.00 $filtro GROUP BY partida)
			 GROUP BY cod_partida)
			UNION
			(SELECT
				p.cod_partida,
				p.denominacion,
				p.cod_tipocuenta,
				p.partida1,
				p.generica,
				p.especifica,
				p.subespecifica,
				'T' AS tipo,
				(SELECT SUM(Monto) FROM vw_004formulacionmetasdist WHERE cod_partida LIKE CONCAT('4', p.partida1, '.%') $filtro) AS MontoPresupuestado,
				'0.00' AS MontoAprobado
			 FROM pv_partida p
			 WHERE
				p.partida1 <> '00' AND
				p.generica = '00' AND
				p.especifica = '00' AND
				p.subespecifica = '00' AND
				SUBSTRING(p.cod_partida, 1, 3) IN (SELECT SUBSTRING(cod_partida, 1, 3) AS partida FROM vw_004formulacionmetasdist WHERE Monto > 0.00 $filtro GROUP BY partida)
			 GROUP BY cod_partida)
			UNION
			(SELECT
				p.cod_partida,
				p.denominacion,
				p.cod_tipocuenta,
				p.partida1,
				p.generica,
				p.especifica,
				p.subespecifica,
				'T' AS tipo,
				(SELECT SUM(Monto) FROM vw_004formulacionmetasdist WHERE cod_partida LIKE CONCAT('4', p.partida1, '.', p.generica, '.%') $filtro) AS MontoPresupuestado,
				'0.00' AS MontoAprobado
			 FROM pv_partida p
			 WHERE
				p.cod_tipocuenta = '4' AND
				p.partida1 <> '00' AND
				p.generica <> '00' AND
				p.especifica = '00' AND
				p.subespecifica = '00' AND
				SUBSTRING(p.cod_partida, 1, 7) IN (SELECT SUBSTRING(cod_partida, 1, 7) AS partida FROM vw_004formulacionmetasdist WHERE Monto > 0.00 $filtro GROUP BY partida)
			 GROUP BY cod_partida)
			UNION
			(SELECT
				fmdt.cod_partida,
				fmdt.denominacion,
				p.cod_tipocuenta,
				p.partida1,
				p.generica,
				p.especifica,
				p.subespecifica,
				p.tipo,
				fmdt.Monto AS MontoPresupuestado,
				'0.00' AS MontoAprobado
			 FROM
				vw_004formulacionmetasdist fmdt
				INNER JOIN pv_partida p ON (p.cod_partida = fmdt.cod_partida)
			 WHERE
				fmdt.Ejercicio = '$Ejercicio' AND
				fmdt.CategoriaProg = '$CategoriaProg' AND
				fmdt.Monto > 0.00
			 GROUP BY cod_partida)
			ORDER BY cod_partida";
	$field_partida = getRecords($sql);
	##
	$_titulo = "Anteproyecto de Presupuesto / Generar";
	$accion = "generar-anteproyecto";
	$disabled_modificar = "";
	$disabled_ver = "";
	$disabled_insertar = "disabled";
	$disabled_aprobar = "disabled";
	$disabled_generar = "disabled";
	$readonly_modificar = "readonly";
	$readonly_ver = "readonly";
	$opt_codigo = 1;
	$display_modificar = "display:none;";
	$display_submit = "";
	$label_submit = "Generar";
	$focus = "CodSector";
}
elseif ($opcion == "generar-presupuesto") {
	list($Ejercicio, $CategoriaProg) = explode('_', $sel_registros);
	$sql = "SELECT
			    fmdt.Ejercicio,
				fmdt.CategoriaProg,
			    cp.IdActividad,
			    a.CodActividad,
			    a.Denominacion AS Actividad,
			    cp.CodUnidadEjec,
			    ue.CodOrganismo,
			    ue.Denominacion AS UnidadEjecutora,
				a.IdProyecto,
				py.IdSubPrograma,
				spg.IdPrograma,
				pg.IdSubSector,
				ss.CodSector,
			    SUM(fmdt.Monto) AS Monto
			FROM
				vw_poa_consolidado_categorias fmdt
			    INNER JOIN pv_categoriaprog cp ON (cp.CategoriaProg = fmdt.CategoriaProg)
			    INNER JOIN pv_actividades a ON (a.IdActividad = cp.IdActividad)
				INNER JOIN pv_proyectos py ON (py.IdProyecto = a.IdProyecto)
				INNER JOIN pv_subprogramas spg ON (spg.IdSubPrograma = py.IdSubPrograma)
				INNER JOIN pv_programas pg ON (pg.IdPrograma = spg.IdPrograma)
				INNER JOIN pv_subsector ss ON (ss.IdSubSector = pg.IdSubSector)
			    INNER JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cp.CodUnidadEjec)
			    LEFT JOIN pv_unidadejecutoradep ued ON (ued.CodUnidadEjec = ue.CodUnidadEjec)
			WHERE
				fmdt.Ejercicio = '$Ejercicio' AND
				fmdt.CategoriaProg = '$CategoriaProg'";
	$field = getRecord($sql);
	##	
	$field['FechaInicio'] = $field['Ejercicio'].'-01-01';
	$field['FechaFin'] = $field['Ejercicio'].'-12-31';
	$field['GeneradoPor'] = $_SESSION['CODPERSONA_ACTUAL'];
	$field['NomGeneradoPor'] = $_SESSION['NOMBRE_USUARIO_ACTUAL'];
	$field['FechaGenerado'] = $FechaActual;
	$field['Estado'] = 'GE';
	$MontoMetas = floatval(getVar3("SELECT SUM(Monto) FROM vw_002reformulacionmetasdist WHERE Ejercicio = '$Ejercicio' AND CategoriaProg = '$CategoriaProg' AND Estado ='AP'"));
	$MontoPersonal = floatval(getVar3("SELECT SUM(Monto) FROM vw_003formulacionpersonaldist WHERE Ejercicio = '$Ejercicio' AND CategoriaProg = '$CategoriaProg'"));
	$field['MontoProyecto'] = $MontoMetas + $MontoPersonal;
	$field['MontoAprobado'] = $field['MontoProyecto'];
	##	dependencias de la unidad ejecutora
	$sql = "SELECT
				ued.CodDependencia,
				d.Dependencia
			FROM
				pv_unidadejecutoradep ued 
				INNER JOIN mastdependencias d ON (d.CodDependencia = ued.CodDependencia)
			WHERE ued.CodUnidadEjec = '".$field['CodUnidadEjec']."'";
	$field_dependencias = getRecords($sql);
	##	
	$filtro = " AND Ejercicio = '$Ejercicio' AND CategoriaProg = '$CategoriaProg' AND Estado = 'AP'";
	$sql = "(SELECT
				'' AS CodFuente,
				p.cod_partida,
				p.denominacion,
				p.cod_tipocuenta,
				p.partida1,
				p.generica,
				p.especifica,
				p.subespecifica,
				'T' AS tipo,
				(SELECT SUM(Monto) FROM vw_poa_consolidado_categoria_fuente_partidas WHERE cod_partida LIKE '4%' $filtro) AS MontoPresupuestado,
				'0.00' AS MontoAprobado
			 FROM pv_partida p
			 WHERE
				p.partida1 = '00' AND
				p.generica = '00' AND
				p.especifica = '00' AND
				p.subespecifica = '00' AND
				SUBSTRING(p.cod_partida, 1, 1) IN (SELECT SUBSTRING(cod_partida, 1, 1) AS partida FROM vw_poa_consolidado_categoria_fuente_partidas WHERE Monto > 0.00 $filtro GROUP BY CodFuente, cod_partida)
			 GROUP BY cod_partida)
			UNION
			(SELECT
				'' AS CodFuente,
				p.cod_partida,
				p.denominacion,
				p.cod_tipocuenta,
				p.partida1,
				p.generica,
				p.especifica,
				p.subespecifica,
				'T' AS tipo,
				(SELECT SUM(Monto) FROM vw_poa_consolidado_categoria_fuente_partidas WHERE cod_partida LIKE CONCAT('4', p.partida1, '.%') $filtro) AS MontoPresupuestado,
				'0.00' AS MontoAprobado
			 FROM pv_partida p
			 WHERE
				p.partida1 <> '00' AND
				p.generica = '00' AND
				p.especifica = '00' AND
				p.subespecifica = '00' AND
				SUBSTRING(p.cod_partida, 1, 3) IN (SELECT SUBSTRING(cod_partida, 1, 3) AS partida FROM vw_poa_consolidado_categoria_fuente_partidas WHERE Monto > 0.00 $filtro GROUP BY CodFuente, cod_partida)
			 GROUP BY cod_partida)
			UNION
			(SELECT
				'' AS CodFuente,
				p.cod_partida,
				p.denominacion,
				p.cod_tipocuenta,
				p.partida1,
				p.generica,
				p.especifica,
				p.subespecifica,
				'T' AS tipo,
				(SELECT SUM(Monto) FROM vw_poa_consolidado_categoria_fuente_partidas WHERE cod_partida LIKE CONCAT('4', p.partida1, '.', p.generica, '.%') $filtro) AS MontoPresupuestado,
				'0.00' AS MontoAprobado
			 FROM pv_partida p
			 WHERE
				p.cod_tipocuenta = '4' AND
				p.partida1 <> '00' AND
				p.generica <> '00' AND
				p.especifica = '00' AND
				p.subespecifica = '00' AND
				SUBSTRING(p.cod_partida, 1, 7) IN (SELECT SUBSTRING(cod_partida, 1, 7) AS partida FROM vw_poa_consolidado_categoria_fuente_partidas WHERE Monto > 0.00 $filtro GROUP BY CodFuente, cod_partida)
			 GROUP BY cod_partida)
			UNION
			(SELECT
				fmdt.CodFuente,
				fmdt.cod_partida,
				p.denominacion,
				p.cod_tipocuenta,
				p.partida1,
				p.generica,
				p.especifica,
				p.subespecifica,
				p.tipo,
				SUM(fmdt.Monto) AS MontoPresupuestado,
				'0.00' AS MontoAprobado
			 FROM
				vw_poa_consolidado_categoria_fuente_partidas fmdt
				INNER JOIN pv_partida p ON (p.cod_partida = fmdt.cod_partida)
			 WHERE
				fmdt.Ejercicio = '$Ejercicio' AND
				fmdt.CategoriaProg = '$CategoriaProg' AND
				fmdt.Estado = 'AP' AND
				fmdt.Monto > 0.00
			 GROUP BY CodFuente, cod_partida)
			ORDER BY cod_partida";
	$field_partida = getRecords($sql);
	##
	$_titulo = "Presupuesto / Generar";
	$accion = "generar-presupuesto";
	$disabled_modificar = "disabled";
	$disabled_ver = "disabled";
	$disabled_insertar = "disabled";
	$disabled_aprobar = "disabled";
	$disabled_generar = "readonly";
	$readonly_modificar = "readonly";
	$readonly_ver = "readonly";
	$opt_codigo = 1;
	$display_modificar = "display:none;";
	$display_submit = "";
	$label_submit = "Generar";
	$focus = "btSubmit";
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
		            <li id="li1" onclick="currentTab('tab', this);" class="current">
		            	<a href="#" onclick="mostrarTab('tab', 1, 4);">Información General</a>
		            </li>
		            <li id="li2" onclick="currentTab('tab', this);">
		            	<a href="#" onclick="mostrarTab('tab', 2, 4);">Distribución Presupuestaria</a>
		            </li>
		            <li id="li3" onclick="currentTab('tab', this); resumen_presupuestario();">
		            	<a href="#" onclick="mostrarTab('tab', 3, 4);">Resumen Presupuestario</a>
		            </li>
					<?php if ($field['Estado'] == 'GE') { ?>
						<li id="li4" onclick="currentTab('tab', this); resumen_presupuestario_aprobado();">
							<a href="#" onclick="mostrarTab('tab', 4, 4);">Resumen Presupuestario (Aprobado)</a>
						</li>
					<?php } ?>
	            </ul>
            </div>
        </td>
    </tr>
</table>

<form name="frmentrada" id="frmentrada" action="gehen.php?anz=<?=$action?>" method="POST" enctype="multipart/form-data" onsubmit="return formSubmit('pv_proyectopresupuesto_ajax', 'modulo=formulario&accion=<?=$accion?>');" autocomplete="off">
	<input type="hidden" name="_APLICACION" id="_APLICACION" value="<?=$_APLICACION?>" />
	<input type="hidden" name="concepto" id="concepto" value="<?=$concepto?>" />
	<input type="hidden" name="maxlimit" id="maxlimit" value="<?=$maxlimit?>" />
	<input type="hidden" name="lista" id="lista" value="<?=$lista?>" />
	<input type="hidden" name="fOrderBy" id="fOrderBy" value="<?=$fOrderBy?>" />
	<input type="hidden" name="fEjercicio" id="fEjercicio" value="<?=$fEjercicio?>" />
	<input type="hidden" name="fEstado" id="fEstado" value="<?=$fEstado?>" />
	<input type="hidden" name="fCodOrganismo" id="fCodOrganismo" value="<?=$fCodOrganismo?>" />
	<input type="hidden" name="fCodDependencia" id="fCodDependencia" value="<?=$fCodDependencia?>" />
	<input type="hidden" name="fCodUnidadEjec" id="fCodUnidadEjec" value="<?=$fCodUnidadEjec?>" />
	<input type="hidden" name="fIdSubSector" id="fIdSubSector" value="<?=$fIdSubSector?>" />
	<input type="hidden" name="fIdPrograma" id="fIdPrograma" value="<?=$fIdPrograma?>" />
	<input type="hidden" name="fIdSubPrograma" id="fIdSubPrograma" value="<?=$fIdSubPrograma?>" />
	<input type="hidden" name="fIdProyecto" id="fIdProyecto" value="<?=$fIdProyecto?>" />
	<input type="hidden" name="fIdActividad" id="fIdActividad" value="<?=$fIdActividad?>" />
	<input type="hidden" name="fEjercicio" id="fEjercicio" value="<?=$fEjercicio?>" />

	<div id="tab1" style="display:block;">
		<table width="<?=$_width?>" class="tblForm">
			<tr>
				<td colspan="4" class="divFormCaption">Datos Generales</td>
			</tr>
			<tr>
				<td class="tagForm" width="125">C&oacute;digo:</td>
				<td>
					<input type="text" name="CodProyPresupuesto" id="CodProyPresupuesto" value="<?=$field['CodProyPresupuesto']?>" style="width:65px; font-weight:bold;" readonly="readonly" />
				</td>
				<td class="tagForm" width="125">Estado:</td>
				<td>
					<input type="hidden" name="Estado" id="Estado" value="<?=$field['Estado']?>" />
					<input type="text" value="<?=strtoupper(printValores('proyecto-estado',$field['Estado']))?>" style="width:100px; font-weight:bold;" disabled />
				</td>
			</tr>
		    <tr>
				<td class="tagForm">* Organismo:</td>
				<td>
					<select name="CodOrganismo" id="CodOrganismo" style="width:275px;" class=" <?=$disabled_modificar?>" onchange="$('#aCategoriaProg').attr('href', '../lib/listas/gehen.php?anz=lista_pv_categoriaprog&filtrar=default&ventana=presupuesto&FlagOrganismo=S&fCodOrganismo='+$(this).val()+'&campo1=CategoriaProg&campo2=IdSubSector&campo3=IdPrograma&campo4=IdSubPrograma&campo5=IdProyecto&campo6=IdActividad&campo7=CodUnidadEjec&iframe=true&width=100%&height=360'); $('#CategoriaProg').val('');">
						<?=getOrganismos($field['CodOrganismo'], $opt_codigo);?>
					</select>
				</td>
				<td class="tagForm">Cat. Program&aacute;tica:</td>
				<td class="gallery clearfix">
					<input type="text" name="CategoriaProg" id="CategoriaProg" value="<?=$field['CategoriaProg']?>" style="width:100px;" readonly="readonly" />
					<a href="../lib/listas/gehen.php?anz=lista_pv_categoriaprog&filtrar=default&ventana=pv_proyectopresupuesto&FlagOrganismo=S&fCodOrganismo=<?=$field['CodOrganismo']?>&campo1=CategoriaProg&campo2=IdSubSector&campo3=IdPrograma&campo4=IdSubPrograma&campo5=IdProyecto&campo6=IdActividad&campo7=CodUnidadEjec&iframe=true&width=100%&height=100%" rel="prettyPhoto[iframe1]" id="aCategoriaProg" style=" <?=$display_modificar?>">
		            	<img src="../imagenes/f_boton.png" width="20" title="Seleccionar" align="absbottom" style="cursor:pointer;" />
		            </a>
				</td>
			</tr>
		    <tr>
				<td class="tagForm">* Sub-Sector:</td>
				<td>
					<select name="IdSubSector" id="IdSubSector" style="width:275px;" disabled>
						<option value="">&nbsp;</option>
						<?=loadSelect2('pv_subsector','IdSubSector','Denominacion',$field['IdSubSector'],0,NULL,NULL,'CodClaSectorial');?>
					</select>
				</td>
				<td class="tagForm">* Ejercicio:</td>
				<td>
					<input type="text" name="Ejercicio" id="Ejercicio" value="<?=$field['Ejercicio']?>" style="width:60px;" maxlength="4" <?=$readonly_modificar?> onchange="$('#FechaInicio').val('01-01-'+this.value); $('#FechaFin').val('31-12-'+this.value);" />
				</td>
			</tr>
		    <tr>
				<td class="tagForm">* Programa:</td>
				<td>
					<select name="IdPrograma" id="IdPrograma" style="width:275px;" disabled>
						<option value="">&nbsp;</option>
						<?=loadSelect2('pv_programas','IdPrograma','Denominacion',$field['IdPrograma'],0,NULL,NULL,'CodPrograma');?>
					</select>
				</td>
				<td class="tagForm">* Fecha Inicio:</td>
				<td>
					<input type="text" name="FechaInicio" id="FechaInicio" value="<?=formatFechaDMA($field['FechaInicio'])?>" style="width:60px;" maxlength="10" readonly />
				</td>
			</tr>
		    <tr>
				<td class="tagForm">* Sub-Programa:</td>
				<td>
					<select name="IdSubPrograma" id="IdSubPrograma" style="width:275px;" disabled>
						<option value="">&nbsp;</option>
						<?=loadSelect2('pv_subprogramas','IdSubPrograma','Denominacion',$field['IdSubPrograma'],0,NULL,NULL,'CodSubPrograma');?>
					</select>
				</td>
				<td class="tagForm">* Fecha Fin:</td>
				<td>
					<input type="text" name="FechaFin" id="FechaFin" value="<?=formatFechaDMA($field['FechaFin'])?>" style="width:60px;" maxlength="10" readonly />
				</td>
			</tr>
		    <tr>
				<td class="tagForm">* Proyecto:</td>
				<td>
					<select name="IdProyecto" id="IdProyecto" style="width:275px;" disabled>
						<option value="">&nbsp;</option>
						<?=loadSelect2('pv_proyectos','IdProyecto','Denominacion',$field['IdProyecto'],0,NULL,NULL,'CodProyecto');?>
					</select>
				</td>
			</tr>
		    <tr>
				<td class="tagForm">* Actividad:</td>
				<td>
					<select name="IdActividad" id="IdActividad" style="width:275px;" disabled>
						<option value="">&nbsp;</option>
						<?=loadSelect2('pv_actividades','IdActividad','Denominacion',$field['IdActividad'],0,NULL,NULL,'CodActividad');?>
					</select>
				</td>
				<td colspan="2" class="divFormCaption">Total Anteproyecto</td>
			</tr>
		    <tr>
				<td class="tagForm">* Unidad Ejecutora:</td>
				<td>
					<select name="CodUnidadEjec" id="CodUnidadEjec" style="width:275px;" disabled onchange="getDependenciasxUnidadEjecutora(this.value); setCategoriaProg();">
						<option value="">&nbsp;</option>
						<?=loadSelect2('pv_unidadejecutora','CodUnidadEjec','Denominacion',$field['CodUnidadEjec'],10);?>
					</select>
				</td>
				<td class="tagForm">Monto Proyecto:</td>
				<td>
					<input type="text" name="MontoProyecto" id="MontoProyecto" value="<?=number_format($field['MontoProyecto'],2,',','.')?>" style="width:100px; font-weight:bold; text-align:right;" readonly="readonly" />
				</td>
			</tr>
		    <tr>
				<td class="tagForm">Gaceta:</td>
				<td>
					<input type="text" name="NroGaceta" id="NroGaceta" value="<?=$field['NroGaceta']?>" style="width:220px;" maxlength="20" <?=$disabled_aprobar?> />
					<input type="text" name="FechaGaceta" id="FechaGaceta" value="<?=formatFechaDMA($field['FechaGaceta'])?>" class="datepicker" style="width:60px;" maxlength="10" <?=$disabled_aprobar?> />
				</td>
				<td class="tagForm">Monto Aprobado:</td>
				<td>
					<input type="hidden" name="MontoxAprobar" id="MontoxAprobar" value="<?=$field['MontoAprobado']?>" />
					<input type="text" name="MontoAprobado" id="MontoAprobado" value="<?=number_format($field['MontoAprobado'],2,',','.')?>" style="width:100px; font-weight:bold; text-align:right;" class="currency" <?=$disabled_generar?> onchange="setMontoAprobado();" />
				</td>
			</tr>
		    <tr>
				<td class="tagForm">Resoluci&oacute;n:</td>
				<td>
					<input type="text" name="NroResolucion" id="NroResolucion" value="<?=$field['NroResolucion']?>" style="width:220px;" maxlength="20" <?=$disabled_aprobar?> />
					<input type="text" name="FechaResolucion" id="FechaResolucion" value="<?=formatFechaDMA($field['FechaResolucion'])?>" class="datepicker" style="width:60px;" maxlength="10" <?=$disabled_aprobar?> />
				</td>
				<td class="tagForm">Diferencia:</td>
				<td>
					<input type="text" name="MontoDiferencia" id="MontoDiferencia" value="<?=number_format($field['MontoDiferencia'],2,',','.')?>" style="width:100px; font-weight:bold; text-align:right;" disabled />
				</td>
			</tr>
			<tr>
				<td colspan="2" class="divFormCaption">Dependencias</td>
				<td colspan="2" class="divFormCaption">Auditoria</td>
			</tr>
			<tr>
				<td colspan="2" rowspan="5">
					<div style="overflow:scroll; height:90px; width:445px;">
						<table class="tblLista" style="width:700px;">
							<tbody id="lista_dep">
								<?php
								foreach ($field_dependencias as $fd) {
									?>
									<tr class="trListaBody">
										<td align="center" width="40"><?=$fd['CodDependencia']?></td>
										<td><?=htmlentities($fd['Dependencia'])?></td>
									</tr>
									<?php
								}
								?>
							</tbody>
						</table>
					</div>
				</td>
				<td class="tagForm">Preparado Por:</td>
				<td>
					<input type="hidden" name="PreparadoPor" id="PreparadoPor" value="<?=$field['PreparadoPor']?>" />
					<input type="text" name="NomPreparadoPor" id="NomPreparadoPor" value="<?=$field['NomPreparadoPor']?>" style="width:220px;" readonly />
					<input type="text" name="FechaPreparado" id="FechaPreparado" value="<?=formatFechaDMA($field['FechaPreparado'])?>" style="width:60px;" maxlength="10" readonly />
				</td>
			</tr>
			<tr>
				<td class="tagForm">Revisado Por:</td>
				<td>
					<input type="hidden" name="RevisadoPor" id="RevisadoPor" value="<?=$field['RevisadoPor']?>" />
					<input type="text" name="NomRevisadoPor" id="NomRevisadoPor" value="<?=$field['NomRevisadoPor']?>" style="width:220px;" readonly />
					<input type="text" name="FechaRevisado" id="FechaRevisado" value="<?=formatFechaDMA($field['FechaRevisado'])?>" style="width:60px;" maxlength="10" readonly />
				</td>
			</tr>
			<tr>
				<td class="tagForm">Aprobado Por:</td>
				<td>
					<input type="hidden" name="AprobadoPor" id="AprobadoPor" value="<?=$field['AprobadoPor']?>" />
					<input type="text" name="NomAprobadoPor" id="NomAprobadoPor" value="<?=$field['NomAprobadoPor']?>" style="width:220px;" readonly />
					<input type="text" name="FechaAprobado" id="FechaAprobado" value="<?=formatFechaDMA($field['FechaAprobado'])?>" style="width:60px;" maxlength="10" readonly />
				</td>
			</tr>
			<tr>
				<td class="tagForm">Generado Por:</td>
				<td>
					<input type="hidden" name="GeneradoPor" id="GeneradoPor" value="<?=$field['GeneradoPor']?>" />
					<input type="text" name="NomGeneradoPor" id="NomGeneradoPor" value="<?=$field['NomGeneradoPor']?>" style="width:220px;" readonly />
					<input type="text" name="FechaGenerado" id="FechaGenerado" value="<?=formatFechaDMA($field['FechaGenerado'])?>" style="width:60px;" maxlength="10" readonly />
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
		<input type="hidden" id="sel_partida" />
		<table width="<?=$_width?>" class="tblBotones">
			<thead>
				<tr>
					<th class="divFormCaption">Distribuci&oacute;n del Presupuesto</th>
				</tr>
			</thead>
		    <tbody>
		    <tr>
		        <td align="right" class="gallery clearfix">
		            <a id="a_partida" href="../lib/listas/gehen.php?anz=lista_partidas&filtrar=default&ventana=proyectopresupuesto_insertar&detalle=partida&modulo=ajax&accion=partida_insertar&FlagTipoCuenta=S&fcod_tipocuenta=4&FlagGenerar=<?=($field['Estado']=='GE'?'S':'N')?>&iframe=true&width=950&height=430" rel="prettyPhoto[iframe2]" style="display:none;"></a>
		            <input type="button" class="btLista" value="Insertar" onclick="$('#a_partida').click();" <?=$disabled_insertar?> />
		            <input type="button" class="btLista" value="Borrar" onclick="quitar_partida(this, 'partida');" <?=$disabled_borrar?> />
		        </td>
		    </tr>
		    </tbody>
		</table>
		<div style="overflow:scroll; height:400px; width:<?=$_width?>px; margin:auto;">
			<table class="tblLista" style="width:100%;">
				<thead>
					<tr>
						<th width="30">F.F.</th>
						<th width="80">Partida</th>
						<th align="left">Denominaci&oacute;n</th>
						<th width="100">Monto Proyecto</th>
						<?php
						if ($field['Estado'] == 'GE') { ?> <th width="100">Monto Aprobado</th> <?php }
						?>
						
					</tr>
				</thead>
				
				<tbody id="lista_partida">
					<?php
					$nro_partida = 0;
					foreach ($field_partida as $f) {
						++$nro_partida;
						$id = $f['cod_partida'];
						if ($f['partida1']=='00' && $f['generica']=='00' && $f['especifica']=='00' && $f['subespecifica']=='00') {
							$background="background-color:#B6B6B6;";
							$weight="font-weight:bold;";
							$detalle='cuenta';
							$f['CodFuente']='';
						}
						elseif ($f['generica']=='00' && $f['especifica']=='00' && $f['subespecifica']=='00') {
							$background="background-color:#C7C7C7;";
							$weight="font-weight:bold;";
							$detalle='partida';
							$f['CodFuente']='';
						}
						elseif ($f['especifica']=='00' && $f['subespecifica']=='00') {
							$background="background-color:#DEDEDE;";
							$weight="font-weight:bold;";
							$detalle='generica';
							$f['CodFuente']='';
						}
						else {
							$background="";
							$weight="";
							$detalle='detalle';
						}
						if ($f['tipo'] == 'T') {
							$readonly = "disabled";
							?><tr class="trListaBody" style="<?=$background.$weight?>" id="partida_<?=$id?>"><?php
						}
						else {
							$readonly = "";
							?><tr class="trListaBody FlagAnexa<?=$f['FlagAnexa']?>" style="<?=$background.$weight?>" id="partida_<?=$id?>" onclick="clk($(this), 'partida', 'partida_<?=$id?>');"><?php
						}
						if ($opcion == "generar" || $opcion == "generar-presupuesto") $f['MontoAprobado'] = $f['MontoPresupuestado'];
						?>
							<td align="center">
								<?php if ($detalle == 'detalle') { ?>
									<select name="CodFuente[]" class="cell">
										<?=loadSelect2("pv_fuentefinanciamiento","CodFuente","Denominacion",$f['CodFuente'],(($disabled_ver=='disabled'?11:10)))?>
									</select>
								<?php } ?>
							</td>
							<td align="center">
								<input type="hidden" name="cod_partida[]" value="<?=$id?>" <?=$readonly?> />
								<input type="hidden" name="tipo[]" value="<?=$f['tipo']?>" <?=$readonly?> />
								<input type="hidden" name="FlagAnexa[]" value="<?=$f['FlagAnexa']?>" <?=$readonly?> />
								<?=$f['cod_partida']?>
							</td>
							<td><input type="text" value="<?=htmlentities($f['denominacion'])?>" class="cell2" style="<?=$weight?>" readonly /></td>
							<td><input type="text" name="MontoPresupuestado[]" value="<?=number_format($f['MontoPresupuestado'],2,',','.')?>" class="cell currency presupuestado <?=$detalle?> tc<?=$f['cod_tipocuenta']?> p<?=$f['partida1']?> g<?=$f['generica']?> e<?=$f['especifica']?> se<?=$f['subespecifica']?>" style="text-align:right; <?=$weight?>" <?=$readonly_ver?> <?=$readonly?> onchange="setMontos('tc<?=$f['cod_tipocuenta']?>', 'p<?=$f['partida1']?>', 'g<?=$f['generica']?>', 1);" /></td>
							<?php
							if ($field['Estado'] == 'GE') { ?> <td><input type="text" name="MontoAprobadoDet[]" value="<?=number_format($f['MontoAprobado'],2,',','.')?>" class="cell currency aprobado <?=$detalle?> atc<?=$f['cod_tipocuenta']?> ap<?=$f['partida1']?> ag<?=$f['generica']?> ae<?=$f['especifica']?> ase<?=$f['subespecifica']?>" style="text-align:right; <?=$weight?>" <?=$disabled_generar?> <?=$readonly?> onchange="setMontos('atc<?=$f['cod_tipocuenta']?>', 'ap<?=$f['partida1']?>', 'ag<?=$f['generica']?>', 0);" /></td> <?php }
							?>
						</tr>
					<?php
					}
					?>
				</tbody>
			</table>
		</div>
		<input type="hidden" id="nro_partida" value="<?=$nro_partida?>" />
		<input type="hidden" id="can_partida" value="<?=$nro_partida?>" />
	</div>

	<div id="tab3" style="display:none;">
		<table width="<?=$_width?>" class="tblBotones">
			<thead>
				<tr>
					<th class="divFormCaption">Resumen Presupuetario</th>
				</tr>
			</thead>
		</table>
		<div style="overflow:scroll; height:400px; width:<?=$_width?>px; margin:auto;">
			<table class="tblLista" style="width:100%;">
				<thead>
					<tr>
						<th width="80">Partida</th>
						<th align="left">Denominación</th>
						<th width="150">Monto</th>
					</tr>
				</thead>
				<tbody id="tabla_resumen">
					
				</tbody>
			</table>
		</div>
	</div>

	<div id="tab4" style="display:none;">
		<table width="<?=$_width?>" class="tblBotones">
			<thead>
				<tr>
					<th class="divFormCaption">Resumen Presupuetario (Aprobado)</th>
				</tr>
			</thead>
		</table>
		<div style="overflow:scroll; height:400px; width:<?=$_width?>px; margin:auto;">
			<table class="tblLista" style="width:100%;">
				<thead>
					<tr>
						<th width="80">Partida</th>
						<th align="left">Denominación</th>
						<th width="150">Monto</th>
					</tr>
				</thead>
				<tbody id="tabla_resumen_aprobado">
					
				</tbody>
			</table>
		</div>
	</div>

	<center>
		<input type="submit" value="<?=$label_submit?>" style="width:75px; <?=$display_submit?>" id="btSubmit" />
		<input type="button" value="Cancelar" style="width:75px;" id="btCancelar" onclick="<?=$clkCancelar?>" />
	</center>
</form>
<div style="width:<?=$_width?>px" class="divMsj">Campos Obligatorios *</div>

<script type="text/javascript" language="javascript">
	function getDependenciasxUnidadEjecutora(CodUnidadEjec) {
		//	ajax
		$.ajax({
			type: "POST",
			url: "pv_proyectopresupuesto_ajax.php",
			data: "modulo=ajax&accion=getDependenciasxUnidadEjecutora&CodUnidadEjec="+CodUnidadEjec,
			async: false,
			success: function(resp) {
				$('#lista_dep').html(resp);
			}
		});
	}
	function setMontos(tc, p, g, FlagProyecto) {
		var Generica = 0;
		$('.'+tc +'.'+p +'.'+g +'.detalle').each(function() {
			var Monto = setNumero($(this).val());
			Generica += Monto;
		});
		$('.'+tc +'.'+p +'.'+g +'.generica').val(Generica).formatCurrency();

		var Partida = 0;
		$('.'+tc +'.'+p +'.generica').each(function() {
			var Monto = setNumero($(this).val());
			Partida += Monto;
		});
		$('.'+tc +'.'+p +'.partida').val(Partida).formatCurrency();

		var Cuenta = 0;
		$('.'+tc +'.partida').each(function() {
			var Monto = setNumero($(this).val());
			Cuenta += Monto;
		});
		$('.'+tc +'.cuenta').val(Cuenta).formatCurrency();

		if (FlagProyecto) $('#MontoProyecto').val(Cuenta).formatCurrency();
		else $('#MontoxAprobar').val(Cuenta);
	}
	function quitar_partida(boton, detalle) {
		boton.disabled = true;
		var can = "#can_" + detalle;
		var sel = "#sel_" + detalle;	
		var lista = "#lista_" + detalle;
		if ($(sel).val() == "") cajaModal("Debe seleccionar una linea", "error", 400);
		else {
			var candetalle = parseInt($(can).val()); candetalle--;
			$(can).val(candetalle);
			$(sel).val("");
			//
			if ($(lista+" .trListaBodySel").hasClass("FlagAnexaN") && '<?=$field['Estado']?>' == 'GE') cajaModal('No puede eliminar esta partida del proyecto');
			else {
				var idtr = $(lista+" .trListaBodySel").attr('id');
				var partes = idtr.split('_');
				var partida = partes[1].split('.');
				//	
				$(lista+" .trListaBodySel").remove();
				var tc = 'tc' + partida[0].substr(0, 1);
				var p = 'p' + partida[0].substr(1, 2);
				var g = 'g' + partida[1];
				setMontos(tc, p, g, 1);
				var tc = 'atc' + partida[0].substr(0, 1);
				var p = 'ap' + partida[0].substr(1, 2);
				var g = 'ag' + partida[1];
				setMontos(tc, p, g, 0);
				// seleccionar todos los tr cuyo id empiece por lo especificado
				var selector_generica = "partida_" + partida[0] + "." + partida[1] + ".";
	      		var trgenerica = $('tr[id*="'+selector_generica+'"]').length;
	      		if (trgenerica == 1) {
	      			$('tr[id*="'+selector_generica+'"]').remove();
					// seleccionar todos los tr cuyo id empiece por lo especificado
					var selector_partida = "partida_" + partida[0] + ".";
		      		var trpartida = $('tr[id*="'+selector_partida+'"]').length;
		      		if (trpartida == 1) $('tr[id*="'+selector_partida+'"]').remove();
	      		}
			}
		}
		boton.disabled = false;
	}
	function setMontoAprobado() {
		var MontoProyecto = setNumero($('#MontoProyecto').val());
		var MontoAprobado = setNumero($('#MontoAprobado').val());
		var MontoxAprobar = setNumero($('#MontoxAprobar').val());
		var MontoDiferencia = MontoProyecto - MontoAprobado;
		if (MontoAprobado > MontoProyecto) {
			cajaModal('El <strong>Monto Aprobado</strong> no puede ser mayor que el <strong>Monto del Proyecto</strong>');
			var Cuenta = setNumero($('.aprobado.partida').val());
			$('#MontoAprobado').val(Cuenta).formatCurrency();
			$('#MontoxAprobar').val(Cuenta);
		} else {
			$('#MontoxAprobar').val('0.00');
			$('.aprobado').val('0,00');
		}
		$('#MontoDiferencia').val(MontoDiferencia).formatCurrency();
	}
	function resumen_presupuestario() {
		$('#tabla_resumen').html('Cargando resumen....');
		$.post('pv_proyectopresupuesto_ajax.php', "modulo=ajax&accion=resumen_presupuestario&"+$('#frmentrada').serialize(), function(data) {
			$('#tabla_resumen').html(data);
	    });
	}
	function resumen_presupuestario_aprobado() {
		$('#tabla_resumen').html('Cargando resumen....');
		$.post('pv_proyectopresupuesto_ajax.php', "modulo=ajax&accion=resumen_presupuestario_aprobado&"+$('#frmentrada').serialize(), function(data) {
			$('#tabla_resumen_aprobado').html(data);
	    });
	}
</script>
