<?php
header('Content-Type: text/html; charset=iso-8859-1');
header("Content-Disposition: attachment; filename=N03212000056881555796".$_POST['fPeriodoMes'].$_POST['fPeriodoAnio'].".txt");
header("Pragma: no-cache");
header("Expires: 0");
//---------------------------------------------------
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$LF = 0x0A;
$CR = 0x0D;
$nl = sprintf("%c%c",$CR,$LF);
$fPeriodo = $fPeriodoAnio.'-'.$fPeriodoMes;
$filtro1 = '';
$filtro2 = '';
if ($fCodTipoNom) { $filtro1 .= " AND pp.CodTipoNom = '".$fCodTipoNom."'"; $filtro2 .= " AND ptne.CodTipoNom = '".$fCodTipoNom."'"; }
if ($fCodTipoProceso) { $filtro1 .= " AND pp.CodTipoProceso = '".$fCodTipoProceso."'"; $filtro2 .= " AND ptne.CodTipoProceso = '".$fCodTipoProceso."'"; }
//---------------------------------------------------
##	array con valores del rendimiento
$r = array();
$r['2010-01'] = 87.13;
$r['2010-02'] = 111.23;
$r['2010-03'] = 125.44;
$r['2010-04'] = 141.43;
$r['2010-05'] = 144.03;
$r['2010-06'] = 163.67;
$r['2010-07'] = 195.06;
$r['2010-08'] = 212.16;
$r['2010-09'] = 206.35;
$r['2010-10'] = 171.60;
$r['2010-11'] = 278.16;
$r['2010-12'] = 243.01;
$r['2011-01'] = 274.00;
$r['2011-02'] = 296.58;
$r['2011-03'] = 337.11;
$r['2011-04'] = 414.80;
$r['2011-05'] = 445.87;
$r['2011-06'] = 453.09;
$r['2011-07'] = 482.02;
$r['2011-08'] = 502.27;
$r['2011-09'] = 549.13;
$r['2011-10'] = 533.03;
$r['2011-11'] = 548.69;
$r['2011-12'] = 582.14;
$r['2012-01'] = 568.21;
$r['2012-02'] = 602.49;
$r['2012-03'] = 677.16;
$r['2012-04'] = 618.96;
$r['2012-05'] = 610.43;
$r['2012-06'] = 735.88;
$r['2012-07'] = 745.72;
$r['2012-08'] = 926.85;
$r['2012-09'] = 959.19;
$r['2012-10'] = 1083.93;
$r['2012-11'] = 1042.77;
$r['2012-12'] = 1047.07;
$r['2013-01'] = 980.21;
$r['2013-02'] = 922.87;
$r['2013-03'] = 1026.26;
$r['2013-04'] = 1123.41;
$r['2013-05'] = 1173.82;
$r['2013-06'] = 1713.87;
$r['2013-07'] = 1309.45;
$r['2013-08'] = 1829.92;
$r['2013-09'] = 1269.20;
$r['2013-10'] = 1828.00;
$r['2013-11'] = 1607.16;
$r['2013-12'] = 2118.48;
$r['2014-01'] = 1817.62;
$r['2014-02'] = 1848.42;
$r['2014-03'] = 2117.86;
$r['2014-04'] = 2143.20;
$r['2014-05'] = 2232.37;
$r['2014-06'] = 2883.15;
$r['2014-07'] = 2424.00;
$r['2014-08'] = 2604.54;
$r['2014-09'] = 2660.82;
$r['2014-10'] = 2765.64;
##	consulta
$SumaMonto = 0;
$SumaRendimiento = 0;
$sql = "SELECT 
			mp.CodPersona,
			mp.Nacionalidad,
			mp.Ndocumento, 
			mp.Apellido1,
			mp.Apellido2,
			mp.Nombres,
			SUM(ptnec.Monto) AS MontoRetencion,
			(SELECT SUM(Monto)
			 FROM pr_tiponominaempleadoconcepto ptnec2
			 WHERE
			 	ptnec2.CodOrganismo = ptnec.CodOrganismo AND
				ptnec2.CodTipoNom = ptnec.CodTipoNom AND 
				ptnec2.Periodo = ptnec.Periodo AND 
				ptnec2.CodPersona = ptnec.CodPersona AND
				(ptnec2.CodTipoProceso = 'FIN' OR ptnec2.CodTipoProceso = 'ADE' OR ptnec2.CodTipoProceso = 'SCA') AND
				ptnec2.CodConcepto = '0041'
			 GROUP BY ptnec2.CodPersona) AS MontoAporte,
			e.Fingreso,
			e.Fegreso,
			s.SueldoIntegral
		FROM
			mastpersonas mp
			INNER JOIN mastempleado e ON (e.CodPersona = mp.CodPersona)
			INNER JOIN pr_tiponominaempleado ptne ON (mp.CodPersona = ptne.CodPersona)
			INNER JOIN pr_tiponominaempleadoconcepto ptnec ON (ptne.CodOrganismo = ptnec.CodOrganismo AND
															   ptne.CodTipoNom = ptnec.CodTipoNom AND 
															   ptne.Periodo = ptnec.Periodo AND 
															   ptne.CodTipoProceso = ptnec.CodTipoProceso AND 
															   ptne.CodPersona = ptnec.CodPersona AND
															   ptnec.CodConcepto = '0029')
			INNER JOIN rh_sueldos s ON (s.CodPersona = ptne.CodPersona AND s.Periodo = ptne.Periodo)
		WHERE
			ptne.CodOrganismo = '$fCodOrganismo' AND
			ptne.Periodo = '$fPeriodo' $filtro2
		GROUP BY CodPersona
		ORDER BY LENGTH(mp.Ndocumento), mp.Ndocumento";
$field = getRecords($sql);
$rows = count($field);
$RendimientoxEmpleado = round(($r[$fPeriodo] / $rows), 2);
$Diferencia = abs($r[$fPeriodo] - ($RendimientoxEmpleado * $rows));
foreach ($field as $f) {
	$Apellido1 = (($f['Apellido1']!='')?$f['Apellido1']:$f['Apellido2']);
	$Nombres = explode(' ', $f['Nombres']);

	$Monto = $f['MontoRetencion'] + $f['MontoAporte'];
	if ($fPeriodo == '2010-07' && $f['CodPersona'] == '000010') $Monto += 39.18 + 78.33;
	elseif ($fPeriodo == '2010-12') {
		if ($f['CodPersona'] == '000091') $Monto += 3.68 + 7.35;
		elseif ($f['CodPersona'] == '000092') $Monto += 3.68 + 7.35;
		elseif ($f['CodPersona'] == '000093') $Monto += 2.97 + 5.93;
		elseif ($f['CodPersona'] == '000094') $Monto += 2.72 + 5.44;
	}
	elseif ($fPeriodo == '2013-05') {
		unset($field_rta);
		$sql = "SELECT
					Monto AS MontoRetencion,
					(SELECT Monto
					 FROM 
						pr_tiponominaempleadoconcepto tnec2
						INNER JOIN pr_procesoperiodo pp2 ON (pp2.CodOrganismo = tnec2.CodOrganismo AND
															 pp2.CodTipoNom = tnec2.CodTipoNom AND
															 pp2.Periodo = tnec2.Periodo AND
															 pp2.CodTipoProceso = tnec2.CodTipoProceso AND
															 pp2.PeriodoNomina = '$fPeriodo')
					 WHERE 
					 	tnec2.CodOrganismo = tnec.CodOrganismo AND
					 	tnec2.CodTipoNom = tnec.CodTipoNom AND
					 	tnec2.Periodo = tnec.Periodo AND
						tnec2.CodTipoProceso = tnec.CodTipoProceso AND
						tnec2.CodPersona = tnec.CodPersona AND
						tnec2.CodConcepto = '0041') AS MontoAporte
				FROM 
					pr_tiponominaempleadoconcepto tnec
					INNER JOIN pr_procesoperiodo pp ON (pp.CodOrganismo = tnec.CodOrganismo AND
														pp.CodTipoNom = tnec.CodTipoNom AND
														pp.Periodo = tnec.Periodo AND
														pp.CodTipoProceso = tnec.CodTipoProceso AND
														pp.PeriodoNomina = '$fPeriodo')
				WHERE 
					tnec.CodTipoProceso = 'RTA' AND
					tnec.CodConcepto = '0029' AND
					tnec.CodPersona = '".$f['CodPersona']."'";
		$field_rta = getRecord($sql);
		$Monto += floatval($field_rta['MontoRetencion']) + floatval($field_rta['MontoAporte']);
	}
	elseif ($fPeriodo == '2014-02') {
		unset($field_rta);
		$sql = "SELECT
					Monto AS MontoRetencion,
					(SELECT Monto
					 FROM 
						pr_tiponominaempleadoconcepto tnec2
						INNER JOIN pr_procesoperiodo pp2 ON (pp2.CodOrganismo = tnec2.CodOrganismo AND
															 pp2.CodTipoNom = tnec2.CodTipoNom AND
															 pp2.Periodo = tnec2.Periodo AND
															 pp2.CodTipoProceso = tnec2.CodTipoProceso AND
															 pp2.PeriodoNomina = '$fPeriodo')
					 WHERE 
					 	tnec2.CodOrganismo = tnec.CodOrganismo AND
					 	tnec2.CodTipoNom = tnec.CodTipoNom AND
					 	tnec2.Periodo = tnec.Periodo AND
						tnec2.CodTipoProceso = tnec.CodTipoProceso AND
						tnec2.CodPersona = tnec.CodPersona AND
						tnec2.CodConcepto = '0041') AS MontoAporte
				FROM 
					pr_tiponominaempleadoconcepto tnec
					INNER JOIN pr_procesoperiodo pp ON (pp.CodOrganismo = tnec.CodOrganismo AND
														pp.CodTipoNom = tnec.CodTipoNom AND
														pp.Periodo = tnec.Periodo AND
														pp.CodTipoProceso = tnec.CodTipoProceso AND
														pp.PeriodoNomina = '$fPeriodo')
				WHERE 
					tnec.CodTipoProceso = 'RTA' AND
					tnec.CodConcepto = '0029' AND
					tnec.CodPersona = '".$f['CodPersona']."'";
		$field_rta = getRecord($sql);
		$Monto += floatval($field_rta['MontoRetencion']) + floatval($field_rta['MontoAporte']);

		unset($field_rta);
		$sql = "SELECT
					Monto AS MontoRetencion,
					(SELECT Monto
					 FROM 
						pr_tiponominaempleadoconcepto tnec2
						INNER JOIN pr_procesoperiodo pp2 ON (pp2.CodOrganismo = tnec2.CodOrganismo AND
															 pp2.CodTipoNom = tnec2.CodTipoNom AND
															 pp2.Periodo = tnec2.Periodo AND
															 pp2.CodTipoProceso = tnec2.CodTipoProceso)
					 WHERE 
					 	tnec2.CodOrganismo = tnec.CodOrganismo AND
					 	tnec2.CodTipoNom = tnec.CodTipoNom AND
					 	tnec2.Periodo = tnec.Periodo AND
						tnec2.CodTipoProceso = tnec.CodTipoProceso AND
						tnec2.CodPersona = tnec.CodPersona AND
						tnec2.CodConcepto = '0041') AS MontoAporte
				FROM 
					pr_tiponominaempleadoconcepto tnec
					INNER JOIN pr_procesoperiodo pp ON (pp.CodOrganismo = tnec.CodOrganismo AND
														pp.CodTipoNom = tnec.CodTipoNom AND
														pp.Periodo = tnec.Periodo AND
														pp.CodTipoProceso = tnec.CodTipoProceso)
				WHERE 
					tnec.Periodo = '$fPeriodo' AND
					tnec.CodTipoProceso = 'DFS' AND
					tnec.CodConcepto = '0029' AND
					tnec.CodPersona = '".$f['CodPersona']."'";
		$field_rta = getRecord($sql);
		$Monto += floatval($field_rta['MontoRetencion']) + floatval($field_rta['MontoAporte']);

	}
	elseif ($fPeriodo == '2014-09') {
		unset($field_rta);
		$sql = "SELECT
					SUM(Monto) AS MontoRetencion,
					(SELECT SUM(Monto)
					 FROM 
						pr_tiponominaempleadoconcepto tnec2
						INNER JOIN pr_procesoperiodo pp2 ON (pp2.CodOrganismo = tnec2.CodOrganismo AND
															 pp2.CodTipoNom = tnec2.CodTipoNom AND
															 pp2.Periodo = tnec2.Periodo AND
															 pp2.CodTipoProceso = tnec2.CodTipoProceso AND
															 pp2.PeriodoNomina = '2014-05')
					 WHERE 
					 	tnec2.CodOrganismo = tnec.CodOrganismo AND
					 	tnec2.CodTipoNom = tnec.CodTipoNom AND
					 	tnec2.Periodo = tnec.Periodo AND
						tnec2.CodTipoProceso = tnec.CodTipoProceso AND
						tnec2.CodPersona = tnec.CodPersona AND
						tnec2.CodConcepto = '0041') AS MontoAporte
				FROM 
					pr_tiponominaempleadoconcepto tnec
					INNER JOIN pr_procesoperiodo pp ON (pp.CodOrganismo = tnec.CodOrganismo AND
														pp.CodTipoNom = tnec.CodTipoNom AND
														pp.Periodo = tnec.Periodo AND
														pp.CodTipoProceso = tnec.CodTipoProceso AND
														pp.PeriodoNomina = '2014-05')
				WHERE 
					tnec.CodTipoNom = '01' AND
					tnec.CodTipoProceso = 'RTA' AND
					tnec.CodConcepto = '0029' AND
					tnec.CodPersona = '".$f['CodPersona']."'";
		$field_rta = getRecord($sql);
		$Monto += floatval($field_rta['MontoRetencion']) + floatval($field_rta['MontoAporte']);

		unset($field_rta);
		$sql = "SELECT
					SUM(Monto) AS MontoRetencion,
					(SELECT SUM(Monto)
					 FROM 
						pr_tiponominaempleadoconcepto tnec2
						INNER JOIN pr_procesoperiodo pp2 ON (pp2.CodOrganismo = tnec2.CodOrganismo AND
															 pp2.CodTipoNom = tnec2.CodTipoNom AND
															 pp2.Periodo = tnec2.Periodo AND
															 pp2.CodTipoProceso = tnec2.CodTipoProceso AND
															 pp2.PeriodoNomina = '2014-06')
					 WHERE 
					 	tnec2.CodOrganismo = tnec.CodOrganismo AND
					 	tnec2.CodTipoNom = tnec.CodTipoNom AND
					 	tnec2.Periodo = tnec.Periodo AND
						tnec2.CodTipoProceso = tnec.CodTipoProceso AND
						tnec2.CodPersona = tnec.CodPersona AND
						tnec2.CodConcepto = '0041') AS MontoAporte
				FROM 
					pr_tiponominaempleadoconcepto tnec
					INNER JOIN pr_procesoperiodo pp ON (pp.CodOrganismo = tnec.CodOrganismo AND
														pp.CodTipoNom = tnec.CodTipoNom AND
														pp.Periodo = tnec.Periodo AND
														pp.CodTipoProceso = tnec.CodTipoProceso AND
														pp.PeriodoNomina = '2014-06')
				WHERE 
					tnec.CodTipoNom = '01' AND
					tnec.CodTipoProceso = 'RTA' AND
					tnec.CodConcepto = '0029' AND
					tnec.CodPersona = '".$f['CodPersona']."'";
		$field_rta = getRecord($sql);
		$Monto += floatval($field_rta['MontoRetencion']) + floatval($field_rta['MontoAporte']);
	}
	$SumaMonto += $Monto;
	$Monto = number_format($Monto,2,'.','');
	$Monto = str_replace('.', '', $Monto);

	$Rendimiento = $RendimientoxEmpleado + $Diferencia;
	$SumaRendimiento += $Rendimiento;
	$Rendimiento = str_replace('.', '', $Rendimiento);

	if ($fPeriodo >= '2014-11') {
		list($AnioI, $MesI, $DiaI) = explode('-', $f['Fingreso']);
		list($AnioE, $MesE, $DiaE) = explode('-', $f['Fegreso']);

		$SueldoIntegral = $f['SueldoIntegral'];
		$SueldoIntegral = number_format($SueldoIntegral,2,'.','');
		$SueldoIntegral = str_replace('.', '', $SueldoIntegral);

		$Fingreso = $DiaI.$MesI.$AnioI;
		if ($f['Fegreso'] == '0000-00-00' || $f['Fegreso'] == '') $Fegreso = ' '; else $Fegreso = $AnioE.$MesE.$DiaE;

		$Texto .= "V,".$f['Ndocumento'].",".$Nombres[0].","." ".",".substr($Apellido1,0,25).","." ".",".$SueldoIntegral.",".$Fingreso.",".$Fegreso.$nl;
	} else {
		$Texto .= "V,".$f['Ndocumento'].",".substr($Apellido1,0,25).","." ".",".$Nombres[0].","." ".",".$Monto.",".$Rendimiento.$nl;
	}
	
	$Diferencia = 0;
}
echo $Texto;
?>