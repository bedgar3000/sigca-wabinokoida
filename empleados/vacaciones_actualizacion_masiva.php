<?php
include("../lib/fphp.php");
include("lib/fphp.php");
/*//	------------------------------------
list($AnioActual, $MesActual, $DiaActual) = split("[/.-]", substr($Ahora, 0, 10));
$FechaActual = "$AnioActual-$MesActual-$DiaActual";
$PeriodoActual = "$AnioActual-$MesActual";
//	------------------------------------
$FechaS = obtenerFechaFin(formatFechaDMA($FechaActual), $_PARAMETRO['VACVENDIAS']);
list($DiaSiguiente, $MesSiguiente, $AnioSiguiente) = split("[./-]", $FechaS);
$FechaSiguiente = "$AnioSiguiente-$MesSiguiente-$DiaSiguiente";
##	datos del empleado
$sql = "SELECT
			p.CodPersona,
			p.NomCompleto,
			e.CodEmpleado,
			e.Fingreso,
			e.Fegreso,
			e.CodTipoNom,
			e.Estado,
			pt.Grado,
			tn.Nomina
		FROM
			mastpersonas p
			INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
			INNER JOIN rh_puestos pt ON (pt.CodCargo = e.CodCargo)
			INNER JOIN tiponomina tn On (tn.CodTipoNom = e.CodTipoNom)
		WHERE e.Estado = 'A'";
$f_empleado = getRecords($sql);
foreach ($f_empleado as $field_empleado) {
	##	
	if ($field_empleado['Estado'] == 'I') $FechaSiguiente = $field_empleado['Fegreso'];
	list($AniosAntecedente, $MesesAntecedente, $DiasAntecedente) = getTiempoAntecedente($field_empleado['CodPersona'], 'S');
	list($AniosOrganismo, $MesesOrganismo, $DiasOrganismo) = getEdad(formatFechaDMA($field_empleado['Fingreso']), formatFechaDMA($FechaSiguiente));
	list($AniosServicio, $MesesServicio, $DiasServicio) = totalTiempo($AniosAntecedente+$AniosOrganismo, $MesesAntecedente+$MesesOrganismo, $DiasAntecedente+$DiasOrganismo);
	list($DiasDisfrutes, $DiasAdicionales) = vacacionTabla($registro, $AniosOrganismo, $AniosAntecedente);
	##	tabla de disrute
	$sql = "SELECT * FROM rh_vacaciontabla WHERE CodTipoNom = '".$field_empleado['CodTipoNom']."'";
	$field_periodos = getRecords($sql);
	foreach($field_periodos as $f) {
		$id = $f['NroAnio'];
		$_DISFRUTES[$id] = $f['DiasDisfrutes'];
		$_ADICIONAL[$id] = $f['DiasAdicionales'];
	}
	##	
	list($AnioIngreso, $MesIngreso, $DiaIngreso) = split("[/.-]", $field_empleado['Fingreso']);
	##	
	$NroPeriodo = "";
	$Anio = "";
	$Mes = "";
	$Derecho = "";
	$PendientePeriodo = "";
	$DiasGozados = "";
	$DiasTrabajados = "";
	$DiasInterrumpidos = "";
	$DiasNoGozados = "";
	$TotalUtilizados = "";
	$Pendientes = "";
	$PagosRealizados = "";
	$PendientePago = "";
	//	obtengo los valores almacenados del empleado para el periodo
	$sql = "SELECT
				NroPeriodo,
				Anio,
				Mes,
				Derecho,
				PendientePeriodo,
				DiasGozados,
				DiasTrabajados,
				DiasInterrumpidos,
				DiasNoGozados,
				TotalUtilizados,
				Pendientes,
				PagosRealizados,
				PendientePago
			FROM rh_vacacionperiodo
			WHERE
				CodPersona = '".$field_empleado['CodPersona']."' AND
				CodTipoNom = '".$field_empleado['CodTipoNom']."'";
	$f_periodo = getRecords($sql);
	$rows_periodo = count($f_periodo);
	foreach ($f_periodo as $field_periodo) {
		$NroPeriodo[$i] = $field_periodo['NroPeriodo'];
		$Anio[$i] = $field_periodo['Anio'];
		$Mes[$i] = $field_periodo['Mes'];
		$Derecho[$i] = $field_periodo['Derecho'];
		$PendientePeriodo[$i] = $field_periodo['PendientePeriodo'];
		$DiasGozados[$i] = $field_periodo['DiasGozados'];
		$DiasTrabajados[$i] = $field_periodo['DiasTrabajados'];
		$DiasInterrumpidos[$i] = $field_periodo['DiasInterrumpidos'];
		$DiasNoGozados[$i] = $field_periodo['DiasNoGozados'];
		$TotalUtilizados[$i] = $field_periodo['DiasGozados'] - $field_periodo['DiasInterrumpidos'];
		$Pendientes[$i] = $field_periodo['Pendientes'];
		$PagosRealizados[$i] = $field_periodo['PagosRealizados'];
		$PendientePago[$i] = $field_periodo['PendientePago'];
		$i++;
	}
				
	//	tiempo de servicio
	list($AnioIngreso, $MesIngreso, $DiaIngreso) = split("[/.-]", $field_empleado['Fingreso']);
	list($Anios, $Meses, $Dias) = getTiempo(formatFechaDMA($field_empleado['Fingreso']), formatFechaDMA($FechaSiguiente));
	if ($field_empleado['Estado'] == "A") $NroPeriodos = $Anios;
	else {
		$NroPeriodos = $Anios + 1;
	}
	
	//	recorro los periodos y almaceno
	$Quinquenios = 0;
	$Pendiente = 0;
	$Seleccionable = false;
	for($i=0; $i<$NroPeriodos; $i++) {
		$Anio[$i] = $AnioIngreso + $i;
		if ($NroPeriodo[$i] == "") {
			$NroPeriodo[$i] = $i + 1;
			$Mes[$i] = $MesIngreso;
			##	obtengo los dias de derecho
			if ($_PARAMETRO['VACANTECEDENT'] == "S") {
				if (!isset($_DISFRUTES[$i+1+$AniosAntecedente])) $_DISFRUTES[$i+1+$AniosAntecedente] = $_DISFRUTES[count($_DISFRUTES)];
				$_DiasDisfrutes = $_DISFRUTES[$i+1+$AniosAntecedente];
				$_DiasAdicionales = $_ADICIONAL[$i+1];
				$Derecho[$i] = $_DiasDisfrutes + $_DiasAdicionales;
			} else {
				if (!isset($_DISFRUTES[$i+1])) $_DISFRUTES[$i+1] = $_DISFRUTES[count($_DISFRUTES)];
				$Derecho[$i] = $_DISFRUTES[$i+1] + $_ADICIONAL[$i+1];
			}
			$PendientePeriodo[$i] += $Pendientes[$i-1];
			$DiasGozados[$i] = 0;
			$DiasTrabajados[$i] = 0;
			$DiasInterrumpidos[$i] = 0;
			$TotalUtilizados[$i] = 0;
			if ($field_empleado['CodTipoNom'] == '02') {
				$PendientePago[$i] += $PendientePago[$i-1] + $_DiasDisfrutes;
			}
			elseif ($field_empleado['CodTipoNom'] == '05') {
				$PendientePago[$i] += $PendientePago[$i-1] + $_PARAMETRO['PAGOVACADC'];
			}
			else {
				$PendientePago[$i] += $PendientePago[$i-1] + $_PARAMETRO['PAGOVACA'];
			}
		}
		$Pendientes[$i] = $Derecho[$i] + $PendientePeriodo[$i] - $TotalUtilizados[$i];
		if ($Pendientes[$i] > 0) $FlagUtilizarPeriodo[$i] = "S"; else $FlagUtilizarPeriodo[$i] = "N";
	}

	//	imprimo periodos vacacionales
	for($i=$NroPeriodos-1; $i>=0; $i--) {
		$_Mes = intval($Mes[$i]);
		if ($_Mes <= 1 || $_Mes >= 12) $_Mes = "$MesIngreso"; else $_Mes = $Mes[$i];
		##	
		$sql = "INSERT INTO rh_vacacionperiodo
				SET
					CodPersona = '".$field_empleado['CodPersona']."',
					NroPeriodo = '".$NroPeriodo[$i]."',
					CodTipoNom = '".$field_empleado['CodTipoNom']."',
					Anio = '".$Anio[$i]."',
					Mes = '".$_Mes."',
					Derecho = '".$Derecho[$i]."',
					PendientePeriodo = '".$PendientePeriodo[$i]."',
					DiasGozados = '".$DiasGozados[$i]."',
					DiasTrabajados = '".$DiasTrabajados[$i]."',
					DiasInterrumpidos = '".$DiasInterrumpidos[$i]."',
					TotalUtilizados = '".$TotalUtilizados[$i]."',
					Pendientes = '".$Pendientes[$i]."',
					PagosRealizados = '".$PagosRealizados[$i]."',
					PendientePago = '".$PendientePago[$i]."'";
		echo "$sql ; <br>";
	}
}*/

$sql = "SELECT * FROM mastempleado WHERE Estado='A' AND CodTipoNom<>'06' AND CodTipoNom<>'08'";
$field = getRecords($sql);
foreach ($field as $f) {
	$sql = "UPDATE rh_contratos
			SET
				FechaDesde = '$f[Fingreso]',
				FechaFirma = '$f[Fingreso]',
				FechaContrato = '$f[Fingreso]',
				CodFormato = 'IN',
				Estado = 'VI'
			WHERE CodPersona = '$f[CodPersona]'";
	echo "$sql ; <br>";
}
?>
