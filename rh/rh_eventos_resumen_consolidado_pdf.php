<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
list($Anio, $CodOrganismo, $CodBonoAlim) = split("[_]", $fMes);
//	consulto resumen
$sql = "SELECT
			*,
			(TotalDiasPeriodo - TotalDiasPago - TotalFeriados) AS Inactivos
		FROM rh_bonoalimentacion
		WHERE
			Anio = '".$Anio."' AND
			CodOrganismo = '".$fCodOrganismo."' AND
			CodBonoAlim = '".$CodBonoAlim."'";
$query_resumen = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
if (mysql_num_rows($query_resumen) != 0) $field_resumen = mysql_fetch_array($query_resumen);
##
$FechaInicio = formatFechaDMA($field_resumen['FechaInicio']);
$FechaFin = formatFechaDMA($field_resumen['FechaFin']);
$TotalHorasMensuales = $field_resumen['HorasDiaria'] * $field_resumen['TotalDiasPago'];
$TotalDiasHabiles = getDiasHabiles($FechaInicio, $FechaFin);
$TotalDiasDescanso = $field_resumen['TotalDiasPago'] - $field_resumen['TotalFeriados'] - $TotalDiasHabiles;
$DiffDias = getFechaDias(formatFechaDMA($field_resumen['FechaInicio']), $FechaInicio) + 1;
//	obtengo el nro de semanas
$swSemana = true;
$ns = 0;
$nro_semanas = 0;
$fi = formatFechaDMA($field_resumen['FechaInicio']);
while($swSemana) {
	++$ns;
	++$nro_semanas;
	$dsemana = getWeekDay($fi);
	$dias_semana = 7 - $dsemana + 1;
	$ff = obtenerFechaFin($fi, $dias_semana);
	if (formatFechaAMD($ff) >= $field_resumen['FechaFin']) { $ff = formatFechaDMA($field_resumen['FechaFin']); $swSemana = false; }
	$fechai[$ns] = $fi;
	$fechaf[$ns] = $ff;
	$fi = obtenerFechaFin($ff, 2);
}
//---------------------------------------------------
$filtro = "";
if ($fCodOrganismo != "") $filtro .= " AND (bad.CodOrganismo = '".$fCodOrganismo."')";
if ($fCodDependencia != "") $filtro .= " AND (e.CodDependencia = '".$fCodDependencia."')";
if ($fCodCentroCosto != "") $filtro .= " AND (e.CodCentroCosto = '".$fCodCentroCosto."')";
if ($fCodTipoNom != "") $filtro .= " AND (ba.CodTipoNom = '".$fCodTipoNom."')";
if ($fCodPerfil != "") $filtro .= " AND (e.CodPerfil = '".$fCodPerfil."')";
if ($fEdoReg != "") $filtro .= " AND (p.Estado = '".$fEdoReg."')";
if ($fSitTra != "") $filtro .= " AND (e.Estado = '".$fSitTra."')";
if ($fBuscar != "") $filtro .= " AND (e.CodEmpleado LIKE '%".$fBuscar."%' OR
									  p.NomCompleto LIKE '%".$fBuscar."%' OR
									  p.Ndocumento LIKE '%".$fBuscar."%')";
if ($fCodCargo != "") {
	$inner_cargo = "INNER JOIN rh_cargoreporta cr ON (cr.CodCargo = e.CodCargo AND
													  cr.CargoReporta = '".$fCodCargo."')";
}
//---------------------------------------------------

class PDF extends FPDF {
	//	Cabecera de página.
	function Header() {
		global $_PARAMETRO;
		global $Ahora;
		global $field_resumen;
		global $FechaInicio;
		global $FechaFin;
		global $TotalHorasMensuales;
		global $TotalDiasPago;
		global $TotalDiasDescanso;
		global $nro_semanas;
		global $TotalDiasHabiles;
		global $_POST;
		extract($_POST);
		##
		$Logo = getValorCampo("mastorganismos", "CodOrganismo", "Logo", $fCodOrganismo);
		$NomOrganismo = getValorCampo("mastorganismos", "CodOrganismo", "Organismo", $fCodOrganismo);
		$NomDependencia = getValorCampo("mastdependencias", "CodDependencia", "Dependencia", $_PARAMETRO["DEPRHPR"]);
		$ValorMensual = $TotalDiasPago * $field_resumen['ValorDia'];
		##
		$this->SetFillColor(255, 255, 255);
		$this->SetDrawColor(0, 0, 0);
		$this->Image($_PARAMETRO["PATHLOGO"].$Logo, 10, 5, 10, 10);
		$this->SetFont('Arial', '', 8);
		$this->SetXY(20, 5); $this->Cell(100, 5, utf8_decode($NomOrganismo), 0, 1, 'L');
		$this->SetXY(20, 10); $this->Cell(100, 5, utf8_decode($NomDependencia), 0, 0, 'L');
		$this->SetFont('Arial', '', 8);
		$this->SetXY(260, 5); $this->Cell(10, 5, utf8_decode('Fecha: '), 0, 0, 'L');
		$this->Cell(30, 5, formatFechaDMA(substr($Ahora, 0, 10)), 0, 1, 'L');
		$this->SetXY(260, 10); $this->Cell(10, 5, utf8_decode('Página: '), 0, 0, 'L');
		$this->Cell(30, 5, $this->PageNo().' de {nb}', 0, 1, 'L');
		$this->SetFont('Arial', 'B', 10);
		$this->SetXY(5, 20); $this->Cell(286, 5, utf8_decode('CONSOLIDADO MENSUAL'), 0, 1, 'C', 0);
		$this->SetFont('Arial', 'BI', 8);
		$this->SetXY(5, 24); $this->Cell(286, 5, utf8_decode('Periodo del '.formatFechaDMA($field_resumen['FechaInicio']).' al '.formatFechaDMA($field_resumen['FechaFin'])), 0, 1, 'C', 0);
		$this->Ln(5);
		##	imprimo resumen
		$this->SetDrawColor(0, 0, 0);
		$this->SetFillColor(200, 200, 200);
		$this->SetWidths(array(28, 28, 30, 30, 30, 30, 30));
		$this->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C'));
		$this->SetFont('Arial', 'B', 8);
		$this->Cell(40, 5);
		$this->Row(array('HORAS DIARIAS',
						 'HORAS MENSUALES',
						 'DIAS HABILES',
						 'DIAS DESCANSO',
						 'DIAS FERIADOS',
						 'VALOR X DIA',
						 'VALOR MENSUAL'));
		$this->SetFillColor(255, 255, 255);
		$this->Cell(40, 5);
		$this->Row(array($field_resumen['HorasDiaria'],
						 $TotalHorasMensuales,
						 $TotalDiasHabiles,
						 $TotalDiasDescanso,
						 $field_resumen['TotalFeriados'],
						 number_format($field_resumen['ValorDia'], 2, ',', '.'),
						 number_format($field_resumen['ValorMes'], 2, ',', '.')));
		$this->Ln();
		//	
		$w1 = 110 - (7.5 * $nro_semanas);
		$w2 = 125 - (7.5 * $nro_semanas);
		$SetWidths[0] = $w1; $SetWidths[1] = 18; $SetWidths[2] = $w2;
		$SetAligns[0] = "L"; $SetAligns[1] = "R"; $SetAligns[2] = "L";
		$Row[0] = "Empleado"; $Row[1] = "Documento"; $Row[2] = "Cargo";
		$j = 0;
		for($i=3;$i<$nro_semanas+3;$i++) {
			++$j;
			$SetWidths[$i] = 17;
			$SetAligns[$i] = "R";
			$Row[$i] = "Sem. ".$j;
		}
		$SetWidths[$i] = 23;
		$SetAligns[$i] = "R";
		$Row[$i] = "Total";
		##
		$this->SetDrawColor(0, 0, 0);
		$this->SetFillColor(230, 230, 230);
		$this->SetFont('Arial', 'B', 8);
		$this->SetWidths($SetWidths);
		$this->SetAligns($SetAligns);
		$this->Row($Row);
	}
	
	//	Pie de página.
	function Footer() {}
}
//---------------------------------------------------

//---------------------------------------------------
//	Creación del objeto de la clase heredada.
$pdf = new PDF('L', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->SetMargins(5, 5, 5);
$pdf->SetAutoPageBreak(5, 5);
$pdf->AddPage();
//---------------------------------------------------
$pdf->SetTextColor(0, 0, 0);
//	--
$w1 = 110 - (7.5 * $nro_semanas);
$w2 = 143 - (7.5 * $nro_semanas);
$SetWidths[0] = $w1; $SetWidths[1] = 18; $SetWidths[2] = $w2;
$SetAligns[0] = "L"; $SetAligns[1] = "R"; $SetAligns[2] = "L";
$j = 0;
for($i=3;$i<$nro_semanas+3;$i++) {
	++$j;
	$SetWidths[$i] = 15;
	$SetAligns[$i] = "R";
	$Row[$i] = "Sem. ".$j;
}
$SetWidths[$i] = 15;
$SetAligns[$i] = "R";
$NroTrabajadores = 0;
//	consulto
$sql = "SELECT
			bad.*,
			p.NomCompleto,
			p.Ndocumento,
			e.CodEmpleado,
			pt.DescripCargo
		FROM
			rh_bonoalimentaciondet bad
			INNER JOIN rh_bonoalimentacion ba ON (ba.Anio = bad.Anio AND
												  ba.CodOrganismo = bad.CodOrganismo AND
												  ba.CodBonoAlim = bad.CodBonoAlim)
			INNER JOIN mastpersonas p ON (p.CodPersona = bad.CodPersona)
			INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
			INNER JOIN rh_puestos pt ON (pt.CodCargo = e.CodCargo)
			$inner_cargo
		WHERE
			bad.Anio = '".$field_resumen['Anio']."' AND
			bad.CodOrganismo = '".$field_resumen['CodOrganismo']."' AND
			bad.CodBonoAlim = '".$field_resumen['CodBonoAlim']."' $filtro
		GROUP BY CodPersona
		ORDER BY LENGTH(Ndocumento), Ndocumento";
$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
while ($field = mysql_fetch_array($query)) {
	++$NroTrabajadores;
	$Row[0] = utf8_decode($field['NomCompleto']);
	$Row[1] = number_format($field['Ndocumento'],0,'','.');
	$Row[2] = utf8_decode($field['DescripCargo']);
	##
	$_DiffDias = $DiffDias;
	$j = 2;
	$Pagar = 0;
	for($ns=1;$ns<=$nro_semanas;$ns++) {
		++$j;
		##
		$DiasPago = 0;
		$DiasDescuento = 0;
		$DiasFeriados = 0;
		$DiasInactivos = 0;
		$TotalDiasSemana = getFechaDias($fechai[$ns], $fechaf[$ns]) + 1;
		for($i=$_DiffDias;$i<($_DiffDias+$TotalDiasSemana);$i++) {
			$sql = "SELECT Dia".$i." AS Dia
					FROM rh_bonoalimentaciondet
					WHERE
						Anio = '".$field['Anio']."' AND
						CodOrganismo = '".$field['CodOrganismo']."' AND
						CodBonoAlim = '".$field['CodBonoAlim']."' AND
						CodPersona = '".$field['CodPersona']."'";
			$query_total = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
			if (mysql_num_rows($query_total) != 0) $field_total = mysql_fetch_array($query_total);
			if ($field_total['Dia'] == "X") ++$DiasPago;
			elseif ($field_total['Dia'] == "I") ++$DiasInactivos;
			elseif ($field_total['Dia'] == "D") ++$DiasDescuento;
			elseif ($field_total['Dia'] == "F") ++$DiasFeriados;
		}
		$_DiffDias += $TotalDiasSemana;
		$ValorDescuento = $field_resumen['ValorDia'] * $DiasDescuento;
		$TotalPagar = $field_resumen['ValorDia'] * ($DiasPago + $DiasInactivos + $DiasFeriados);
		##
		$Row[$j] = number_format($TotalPagar, 2, ',', '.');
		$Pagar += $TotalPagar;
	}
	$Row[++$j] = number_format($field['TotalPagar'], 2, ',', '.');
	$SumaTotal += $field['TotalPagar'];
	##
	$pdf->SetDrawColor(0, 0, 0);
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 8);
	##
	$pdf->Row($Row);
}

$pdf->SetDrawColor(0, 0, 0);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetWidths(array(286));
$pdf->SetAligns(array('R'));
$pdf->Row(array(number_format($SumaTotal, 2, ',', '.')));

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(286, 5, utf8_decode('NRO. DE TRABAJADORES: ').$NroTrabajadores, 0, 1, 'L', 0);

//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>