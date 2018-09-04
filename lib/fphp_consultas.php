<?php
//	verificar disponiblidad presupuestaria de una partida
function disponibilidadPartida($Anio, $CodOrganismo, $cod_partida, $CodPresupuesto) {
	//	comprometido
	$sql = "SELECT pd.MontoAjustado, pd.MontoCompromiso
			FROM pv_presupuestodet pd
			WHERE
				pd.CodOrganismo = '".$CodOrganismo."' AND
				pd.CodPresupuesto = '".$CodPresupuesto."' AND
				pd.cod_partida = '".$cod_partida."'";
	$field_comprometido = getRecord($sql);
	//	pre-comprometido
	$sql = "SELECT SUM(Monto) AS Monto
			FROM lg_distribucioncompromisos
			WHERE
				Anio = '".$Anio."' AND
				CodOrganismo = '".$CodOrganismo."' AND
				cod_partida = '".$cod_partida."' AND
				CodPresupuesto = '".$CodPresupuesto."' AND
				Estado = 'PE'
			GROUP BY cod_partida";
	$field_precomprometido = getRecord($sql);
	return array($field_comprometido['MontoAjustado'], $field_comprometido['MontoCompromiso'], $field_precomprometido['Monto']);
}
//	verificar disponiblidad presupuestaria de una partida
function disponibilidadPartida2($Anio, $CodOrganismo, $cod_partida, $CodPresupuesto, $CodFuente='01') {
	##	comprometido
	$sql = "SELECT pd.MontoAjustado, pd.MontoCompromiso
			FROM pv_presupuestodet pd
			WHERE
				pd.CodFuente = '".$CodFuente."' AND
				pd.CodOrganismo = '".$CodOrganismo."' AND
				pd.CodPresupuesto = '".$CodPresupuesto."' AND
				pd.cod_partida = '".$cod_partida."'";
	$field_presupuesto = getRecord($sql);
	##	pre-comprometido
	$sql = "SELECT SUM(Monto) AS Monto
			FROM lg_distribucioncompromisos
			WHERE
				CodFuente = '".$CodFuente."' AND
				Anio = '".$Anio."' AND
				CodOrganismo = '".$CodOrganismo."' AND
				cod_partida = '".$cod_partida."' AND
				CodPresupuesto = '".$CodPresupuesto."' AND
				Estado = 'PE'
			GROUP BY cod_partida";
	$PreCompromiso = getVar3($sql);
	##	obligaciones de nomina
	$PreCompromisoObligacionesNomina = preCompromisoNomina($CodPresupuesto, $CodFuente, $cod_partida);
	$PreCompromiso = $PreCompromiso + $PreCompromisoObligacionesNomina;
	##	cotizaciones asignadas
	$sql = "SELECT SUM(PrecioCantidad)
			FROM
				lg_cotizacion c
				INNER JOIN lg_requerimientosdet rd ON (rd.CodRequerimiento = c.CodRequerimiento AND
													   rd.Secuencia = c.Secuencia)
			WHERE
				rd.CodFuente = '".$CodFuente."' AND
				rd.CodOrganismo = '".$CodOrganismo."' AND
				rd.CodPresupuesto = '".$CodPresupuesto."' AND
				rd.cod_partida = '".$cod_partida."' AND
				c.FlagAsignado = 'S' AND
				c.Estado = 'PE'
			GROUP BY cod_partida";
	$CotizacionesAsignadas = getVar3($sql);
	##	caja chica
	$sql = "SELECT SUM(ccd.Monto) 
			FROM 
				ap_cajachica cc 
				INNER JOIN ap_cajachicadistribucion ccd ON (ccd.FlagCajaChica = cc.FlagCajaChica AND 
															ccd.Periodo = cc.Periodo AND 
															ccd.NroCajaChica = cc.NroCajaChica) 
			WHERE 
				ccd.Periodo = '".$Anio."' AND 
				ccd.CodOrganismo = '".$CodOrganismo."' AND 
				ccd.CodPartida = '".$cod_partida."' AND 
				ccd.CodPresupuesto = '".$CodPresupuesto."' AND 
				cc.Estado = 'PR' 
			GROUP BY CodPartida";
	$CajaChica = getVar3($sql);
	return array(floatval($field_presupuesto['MontoAjustado']), floatval($field_presupuesto['MontoCompromiso']), floatval($PreCompromiso), floatval($CotizacionesAsignadas), floatval($CajaChica));
}
//	verificar disponiblidad presupuestaria de una partida
function distribucionPartida($Anio, $CodOrganismo, $cod_partida, $CodPresupuesto) {
	##	pre-comprometido
	$sql = "(SELECT 
				dc.cod_partida,
				dc.Monto,
				dc.Origen,
				CONCAT(dc.CodTipoDocumento, '-', dc.NroDocumento) AS Documento,
				osd.Descripcion 
			 FROM
				lg_distribucioncompromisos dc 
				INNER JOIN lg_ordenservicio os ON (os.CodOrganismo = dc.CodOrganismo AND 
												   os.Anio = dc.Anio AND 
												   os.NroOrden = dc.NroDocumento)
				INNER JOIN lg_ordenserviciodetalle osd ON (osd.CodOrganismo = os.CodOrganismo AND 
														   osd.Anio = os.Anio AND 
														   osd.NroOrden = os.NroOrden)
			 WHERE 
				dc.Origen = 'OS' AND 
				dc.Anio = '".$Anio."' AND 
				dc.CodOrganismo = '".$CodOrganismo."' AND 
				dc.cod_partida = '".$cod_partida."' AND 
				dc.CodPresupuesto = '".$CodPresupuesto."' AND 
				dc.Estado = 'PE')
			UNION 
			(SELECT 
				dc.cod_partida,
				dc.Monto,
				dc.Origen,
				CONCAT(dc.CodTipoDocumento, '-', dc.NroDocumento) AS Documento,
				ocd.Descripcion 
			 FROM
				lg_distribucioncompromisos dc 
				INNER JOIN lg_ordencompra oc ON (oc.CodOrganismo = dc.CodOrganismo AND 
												 oc.Anio = dc.Anio AND 
												 oc.NroOrden = dc.NroDocumento)
				INNER JOIN lg_ordencompradetalle ocd ON (ocd.CodOrganismo = oc.CodOrganismo AND 
														 ocd.Anio = oc.Anio AND 
														 ocd.NroOrden = oc.NroOrden)
			 WHERE 
				dc.Origen = 'OC' AND 
				dc.Anio = '".$Anio."' AND 
				dc.CodOrganismo = '".$CodOrganismo."' AND 
				dc.cod_partida = '".$cod_partida."' AND 
				dc.CodPresupuesto = '".$CodPresupuesto."' AND 
				dc.Estado = 'PE')
			UNION 
			(SELECT 
				dc.cod_partida,
				dc.Monto,
				dc.Origen,
				CONCAT(dc.CodTipoDocumento, '-', dc.NroDocumento) AS Documento,
				o.Comentarios AS Descripcion 
			 FROM
				lg_distribucioncompromisos dc 
				INNER JOIN ap_obligaciones o ON (o.CodProveedor = dc.CodProveedor AND
												 o.CodTipoDocumento = dc.CodTipoDocumento AND
												 o.NroDocumento = dc.NroDocumento)
			 WHERE 
				dc.Origen = 'OB' AND 
				dc.Anio = '".$Anio."' AND 
				dc.CodOrganismo = '".$CodOrganismo."' AND 
				dc.cod_partida = '".$cod_partida."' AND 
				dc.CodPresupuesto = '".$CodPresupuesto."' AND 
				dc.Estado = 'PE')
			UNION
			(SELECT
				rd.cod_partida,
				c.PrecioCantidad AS Monto,
				'RQ' AS Origen,
				CONCAT('RQ-', r.CodInterno) AS Documento,
				rd.Descripcion 
			 FROM
				lg_cotizacion c
				INNER JOIN lg_requerimientosdet rd ON (rd.CodRequerimiento = c.CodRequerimiento AND
													   rd.Secuencia = c.Secuencia)
				INNER JOIN lg_requerimientos r ON (r.CodRequerimiento = rd.CodRequerimiento)
			 WHERE
				rd.cod_partida = '".$cod_partida."' AND
				c.FlagAsignado = 'S' AND
				c.Estado = 'PE')
			UNION 
			(SELECT 
				dc.CodPartida AS cod_partida,
				dc.Monto,
				'CC' AS Origen,
				CONCAT('CC-', oc.Periodo, oc.NroCajaChica) AS Documento,
				ocd.Descripcion 
			 FROM
				ap_cajachicadistribucion dc 
				INNER JOIN ap_cajachica oc ON (oc.FlagCajaChica = dc.FlagCajaChica AND 
											   oc.Periodo = dc.Periodo AND 
											   oc.NroCajaChica = dc.NroCajaChica)
				INNER JOIN ap_cajachicadetalle ocd ON (ocd.FlagCajaChica = dc.FlagCajaChica AND 
													   ocd.Periodo = dc.Periodo AND 
													   ocd.NroCajaChica = dc.NroCajaChica AND 
													   ocd.Secuencia = dc.Secuencia)
			 WHERE 
				dc.Periodo = '".$Anio."' AND 
				dc.CodOrganismo = '".$CodOrganismo."' AND 
				dc.CodPartida = '".$cod_partida."' AND 
				dc.CodPresupuesto = '".$CodPresupuesto."' AND 
				oc.Estado = 'PR')
			ORDER BY Origen";
	$field = getRecords($sql);
	return $field;
}

//	obtener antecedente del trabajador
function getTiempoAntecedente($CodPersona, $FlagAntVacacion=NULL) {
	$filtro = "";
	if ($FlagAntVacacion) $filtro .= " AND FlagAntVacacion = '".$FlagAntVacacion."'";
	$sql = "SELECT
				FechaDesde,
				FechaHasta
			FROM rh_empleado_experiencia
			WHERE
				CodPersona = '".$CodPersona."' AND
				TipoEnte = '02'
				$filtro
			ORDER BY FechaDesde";
	$field = getRecords($sql);
	foreach($field as $f) {
		list($Anios, $Meses, $Dias) = getEdad(formatFechaDMA($f['FechaDesde']), formatFechaDMA($f['FechaHasta']));
		$TotalTiempoD += $Dias;
		$TotalTiempoM += $Meses;
		$TotalTiempoA += $Anios;
	}
	list($A, $M, $D) = totalTiempo($TotalTiempoA, $TotalTiempoM, $TotalTiempoD);
	return array($A, $M, $D);
}

//	obtener los dias de vacaciones segun tabla
function vacacionTabla($CodPersona, $AniosOrganismo=NULL, $AniosAntecedente=NULL) {
	global $Ahora;
	global $_PARAMETRO;
	##	fecha actual
	list($AnioActual, $MesActual, $DiaActual) = split("[/.-]", substr($Ahora, 0, 10));
	$FechaActual = "$AnioActual-$MesActual-$DiaActual";
	##	fecha de ingreso
	$sql = "SELECT Fingreso FROM mastempleado WHERE CodPersona = '".$CodPersona."'";
	$Fingreso = getVar3($sql);
	##	tipo de nomina
	$sql = "SELECT CodTipoNom FROM mastempleado WHERE CodPersona = '".$CodPersona."'";
	$CodTipoNom = getVar3($sql);
	##	calculo
	if (!isset($AniosOrganismo)) list($AniosOrganismo) = getEdad(formatFechaDMA($Fingreso), formatFechaDMA($FechaActual));
	if (!isset($AniosAntecedente)) list($AniosAntecedente) = getTiempoAntecedente($CodPersona);
	if ($_PARAMETRO['VACANTECEDENT'] == "S") $NroAnio = $AniosOrganismo + $AniosAntecedente;
	else $NroAnio = $AniosOrganismo;
	##	consulto tabla de vacaciones
	$sql = "SELECT MAX(NroAnio) FROM rh_vacaciontabla WHERE CodTipoNom = '".$CodTipoNom."'";
	$MaxNroAnio = getVar3($sql);
	##	disfrutes
	if ($MaxNroAnio > $NroAnio) {
		$sql = "SELECT DiasDisfrutes FROM rh_vacaciontabla WHERE NroAnio = '".$NroAnio."' AND CodTipoNom = '".$CodTipoNom."'";
	} else {
		$sql = "SELECT DiasDisfrutes FROM rh_vacaciontabla WHERE NroAnio = '".$MaxNroAnio."' AND CodTipoNom = '".$CodTipoNom."'";
	}
	$DiasDisfrutes = getVar3($sql);
	##	dias adicionales
	$sql = "SELECT DiasAdicionales FROM rh_vacaciontabla WHERE NroAnio = '".$AniosOrganismo."' AND CodTipoNom = '".$CodTipoNom."'";
	$DiasAdicionales = getVar3($sql);
	return array($DiasDisfrutes, $DiasAdicionales);
}

//	obtener los permisos seguridad de un usuario
function opcionesPermisos($grupo, $Concepto) {
	if ($_SESSION['USUARIO_ACTUAL'] == "ADMINISTRADOR") {
		$_ADMIN = "S";
		$_SHOW = "S";
		$_INSERT = "S";
		$_UPDATE = "S";
		$_DELETE = "S";
	} else {
		$sql = "SELECT FlagAdministrador 
				FROM seguridad_autorizaciones 
				WHERE 
					CodAplicacion = '".$_SESSION['APLICACION_ACTUAL']."' AND 
					Usuario = '".$_SESSION['USUARIO_ACTUAL']."' AND 
					FlagAdministrador = 'S' AND
					Estado = 'A'";
		$FlagAdministrador = getVar3($sql);
		if ($FlagAdministrador == 'S') {
			$_ADMIN = "S";
			$_SHOW = "S";
			$_INSERT = "S";
			$_UPDATE = "S";
			$_DELETE = "S";
		} else {
			$_ADMIN = "N";
			//	--------------------------------------------
			$sql = "SELECT
						FlagMostrar,
						FlagAgregar,
						FlagModificar,
						FlagEliminar
					FROM seguridad_autorizaciones
					WHERE
						CodAplicacion = '".$_SESSION['APLICACION_ACTUAL']."' AND
						Usuario = '".$_SESSION['USUARIO_ACTUAL']."' AND
						Concepto = '".$Concepto."' AND
						Estado = 'A'";
			$field = getRecord($sql);
			if (count($field) > 0) {
				$_SHOW = $field['FlagMostrar'];
				$_INSERT = $field['FlagAgregar'];
				$_UPDATE = $field['FlagModificar'];
				$_DELETE = $field['FlagEliminar'];
			} else {
				$_SHOW = "N";
				$_INSERT = "N";
				$_UPDATE = "N";
				$_DELETE = "N";
			}
		}
	}
	return array($_SHOW, $_ADMIN, $_INSERT, $_UPDATE, $_DELETE);
}

function getActividad($CategoriaProg) {
	$sql = "SELECT a.Denominacion
			FROM
				pv_categoriaprog cp
				INNER JOIN pv_actividades a ON (a.IdActividad = cp.IdActividad)
			WHERE cp.CategoriaProg = '$CategoriaProg'";
	$Actividad = getVar3($sql);
	return $Actividad;
}

function getUnidadEjecutora($CategoriaProg) {
	$sql = "SELECT a.Denominacion
			FROM
				pv_categoriaprog cp
				INNER JOIN pv_unidadejecutora a ON (a.CodUnidadEjec = cp.CodUnidadEjec)
			WHERE cp.CategoriaProg = '$CategoriaProg'";
	$UnidadEjecutora = getVar3($sql);
	return $UnidadEjecutora;
}

function getPersonaUnidadEjecutora($CategoriaProg) {
	$sql = "SELECT a.CodPersona
			FROM
				pv_categoriaprog cp
				INNER JOIN pv_unidadejecutora a ON (a.CodUnidadEjec = cp.CodUnidadEjec)
			WHERE cp.CategoriaProg = '$CategoriaProg'";
	$Persona = getVar3($sql);
	return $Persona;
}

function getCategoriaXDependencia($CodDependencia) {
	$sql = "SELECT cp.CategoriaProg
			FROM
				pv_categoriaprog cp
				INNER JOIN pv_unidadejecutora a ON (a.CodUnidadEjec = cp.CodUnidadEjec)
				INNER JOIN pv_unidadejecutoradep ued ON (ued.CodUnidadEjec = a.CodUnidadEjec)
			WHERE ued.CodDependencia = '$CodDependencia'
			ORDER BY CategoriaProg
			LIMIT 0, 1";
	$CategoriaProg = getVar3($sql);
	return $CategoriaProg;
}

function getCategoriaXCentroCosto($CodCentroCosto) {
	$sql = "SELECT cp.CategoriaProg
			FROM
				pv_categoriaprog cp
				INNER JOIN pv_unidadejecutora a ON (a.CodUnidadEjec = cp.CodUnidadEjec)
			WHERE a.CodCentroCosto = '$CodCentroCosto'
			ORDER BY CategoriaProg
			LIMIT 0, 1";
	$CategoriaProg = getVar3($sql);
	return $CategoriaProg;
}

function preCompromisoNomina($CodPresupuesto, $CodFuente, $cod_partida) {
	##	obligaciones de nomina
	$sql = "SELECT SUM(proc.Monto)
			FROM pr_obligaciones pro
			INNER JOIN pr_obligacionescuenta proc ON (
				proc.CodProveedor = pro.CodProveedor
				AND proc.CodTipoDocumento = pro.CodTipoDocumento
				AND proc.NroDocumento = pro.NroDocumento
			)
			LEFT JOIN ap_obligaciones apo ON (
				apo.CodProveedor = pro.CodProveedor
				AND apo.CodTipoDocumento = pro.CodTipoDocumento
				AND apo.NroDocumento = pro.NroDocumento
			)
			WHERE
				cod_partida
				AND pro.FlagVerificado = 'S'
				AND pro.Estado = 'PE'
				AND (apo.Estado = 'PR' OR apo.Estado IS NULL)
				AND proc.CodPresupuesto = '$CodPresupuesto'
				AND proc.CodFuente = '$CodFuente'
				AND proc.cod_partida = '$cod_partida'";
	$PreCompromisoObligacionesNomina = getVar3($sql);

	return floatval($PreCompromisoObligacionesNomina);
}

function correlativo_documento($CodOrganismo, $CodTipoDocumento, $NroSerie = NULL, $exe = TRUE) {
	$UltNroEmitido = 0;
	$Numero = 0;
	if (!empty($NroSerie))
	{
		$sql = "SELECT * FROM co_seriefiscal WHERE NroSerie = '$NroSerie'";
		$field_serie = getRecord($sql);
		##	
		$digitos = 7;
		$iCodSerie = "AND CodSerie = '$field_serie[CodSerie]'";
	}
	else 
	{
		$digitos = 10;
		$iCodSerie = "AND CodSerie IS NULL";
	}
	##	
	$sql = "SELECT *
			FROM co_correlativodocumento
			WHERE
				CodOrganismo = '$CodOrganismo'
				AND CodTipoDocumento = '$CodTipoDocumento'
				$iCodSerie";
	$field_correlativo = getRecord($sql);
	##	
	if (!empty($field_correlativo['CodCorrelativo']))
	{
		if ($field_correlativo['UltNroEmitido'] < $field_correlativo['NroHasta'])
		{
			$UltNroEmitido = $field_correlativo['UltNroEmitido'] + 1;
			$NroDocumento = (string) str_repeat("0", $digitos-strlen($UltNroEmitido)).$UltNroEmitido;

			$Numero = $NroSerie . $NroDocumento;
		}
	}
	else
	{
		if($exe)
		{
			##	codigo
			$CodCorrelativo = codigo('co_correlativodocumento','CodCorrelativo',6);
			##	inserto
			$sql = "INSERT INTO co_correlativodocumento
					SET
						CodCorrelativo = '$CodCorrelativo',
						CodOrganismo = '$CodOrganismo',
						CodTipoDocumento = '$CodTipoDocumento',
						NroDesde = 1,
						NroHasta = 9999999,
						UltNroEmitido = 0,
						Estado = 'A',
						UltimoUsuario = '$_SESSION[USUARIO_ACTUAL]',
						UltimaFecha = NOW()";
			execute($sql);
		}
		##	
		$UltNroEmitido = 1;
		$NroDocumento = (string) str_repeat("0", $digitos-strlen($UltNroEmitido)).$UltNroEmitido;

		$Numero = $NroSerie . $NroDocumento;
	}

	return [$Numero, $UltNroEmitido];
}

function correlativo_documento_update($UltNroEmitido, $CodOrganismo, $CodTipoDocumento, $NroSerie = NULL) {
	if (!empty($NroSerie))
	{
		$sql = "SELECT * FROM co_seriefiscal WHERE NroSerie = '$NroSerie'";
		$field_serie = getRecord($sql);
		##	
		$iCodSerie = "AND CodSerie = '$field_serie[CodSerie]'";
	}
	else
	{
		$iCodSerie = "AND CodSerie IS NULL";
	}
	##	
	$sql = "UPDATE co_correlativodocumento
			SET UltNroEmitido = '$UltNroEmitido'
			WHERE
				CodOrganismo = '$CodOrganismo'
				AND CodTipoDocumento = '$CodTipoDocumento'
				$iCodSerie";
	execute($sql);
}
?>