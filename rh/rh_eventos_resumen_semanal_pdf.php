<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
list($FechaInicio, $FechaFin) = split("[|]", $fSemana);
list($Anio, $CodOrganismo, $CodBonoAlim) = split("[_]", $fMes);
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
		global $field;
		global $FechaInicio;
		global $FechaFin;
		global $_POST;
		extract($_POST);
		##
		$Logo = getValorCampo("mastorganismos", "CodOrganismo", "Logo", $fCodOrganismo);
		$NomOrganismo = getValorCampo("mastorganismos", "CodOrganismo", "Organismo", $fCodOrganismo);
		$NomDependencia = getValorCampo("mastdependencias", "CodDependencia", "Dependencia", $_PARAMETRO["DEPRHPR"]);
		##
		$this->SetFillColor(255, 255, 255);
		$this->SetDrawColor(0, 0, 0);
		$this->Image($_PARAMETRO["PATHLOGO"].$Logo, 10, 5, 10, 10);
		$this->SetFont('Arial', '', 8);
		$this->SetXY(20, 5); $this->Cell(100, 5, utf8_decode($NomOrganismo), 0, 1, 'L');
		$this->SetXY(20, 10); $this->Cell(100, 5, utf8_decode($NomDependencia), 0, 0, 'L');
		$this->SetFont('Arial', '', 8);
		$this->SetXY(180, 5); $this->Cell(10, 5, utf8_decode('Fecha: '), 0, 0, 'L');
		$this->Cell(30, 5, formatFechaDMA(substr($Ahora, 0, 10)), 0, 1, 'L');
		$this->SetXY(180, 10); $this->Cell(10, 5, utf8_decode('Página: '), 0, 0, 'L');
		$this->Cell(30, 5, $this->PageNo().' de {nb}', 0, 1, 'L');
		$this->SetFont('Arial', 'B', 10);
		$this->SetXY(10, 20); $this->Cell(190, 5, utf8_decode('RESUMEN SEMANAL'), 0, 1, 'C', 0);
		$this->SetFont('Arial', 'BI', 8);
		$this->SetXY(10, 24); $this->Cell(190, 5, utf8_decode('Semana del '.$FechaInicio.' al '.$FechaFin), 0, 1, 'C', 0);
		$this->Ln(5);
	}
	
	//	Pie de página.
	function Footer() {}
}
//---------------------------------------------------

//---------------------------------------------------
//	Creación del objeto de la clase heredada.
$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->SetMargins(5, 5, 5);
$pdf->SetAutoPageBreak(5, 5);
$pdf->AddPage();
//---------------------------------------------------
$pdf->SetTextColor(0, 0, 0);
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
$TotalDias = getFechaDias($FechaInicio, $FechaFin) + 1;
$TotalFeriados = getDiasFeriados($FechaInicio, $FechaFin);
$TotalDiasPago = $TotalDias - $TotalFeriados;
$TotalDiasHabiles = getDiasHabiles($FechaInicio, $FechaFin);
$Inactivos = $TotalDias - $TotalDiasHabiles - $TotalFeriados;
$HorasSemanal = $TotalDiasPago * $field_resumen['HorasDiaria'];
$ValorSemanal = $field_resumen['ValorDia'] * ($TotalDiasHabiles + $Inactivos + $TotalFeriados);
$DiffDias = getFechaDias(formatFechaDMA($field_resumen['FechaInicio']), $FechaInicio) + 1;
##	imprimo resumen
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetFillColor(200, 200, 200);
$pdf->SetWidths(array(28, 28, 30, 30, 30, 30, 30));
$pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C'));
$pdf->SetFont('Arial', 'B', 8);
$pdf->Row(array('HORAS DIARIAS',
				'HORAS SEMANALES',
				'DIAS HABILES',
				'DIAS DESCANSO',
				'DIAS FERIADOS',
				'VALOR X DIA',
				'VALOR X SEMANA'));
$pdf->SetFillColor(255, 255, 255);
$pdf->Row(array($field_resumen['HorasDiaria'],
				$HorasSemanal,
				$TotalDiasHabiles,
				$Inactivos,
				$TotalFeriados,
				number_format($field_resumen['ValorDia'], 2, ',', '.'),
				number_format($ValorSemanal, 2, ',', '.')));

//	consulto
$sql = "SELECT
			bad.Anio,
			bad.CodOrganismo,
			bad.CodBonoAlim,
			bad.CodPersona,
			p.NomCompleto,
			e.CodEmpleado
		FROM
			rh_bonoalimentaciondet bad
			INNER JOIN rh_bonoalimentacion ba ON (ba.Anio = bad.Anio AND
												  ba.CodOrganismo = bad.CodOrganismo AND
												  ba.CodBonoAlim = bad.CodBonoAlim)
			INNER JOIN mastpersonas p ON (p.CodPersona = bad.CodPersona)
			INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
			INNER JOIN rh_bonoalimentacioneventos bae ON (bae.Anio = bad.Anio AND
														  bae.CodOrganismo = bad.CodOrganismo AND
														  bae.CodBonoAlim = bad.CodBonoAlim AND
														  bae.CodPersona = bad.CodPersona AND
														  bae.Fecha >= '".formatFechaAMD($FechaInicio)."' AND
														  bae.Fecha <= '".formatFechaAMD($FechaFin)."')
			$inner_cargo
		WHERE
			bad.Anio = '".$field_resumen['Anio']."' AND
			bad.CodOrganismo = '".$field_resumen['CodOrganismo']."' AND
			bad.CodBonoAlim = '".$field_resumen['CodBonoAlim']."' $filtro
		GROUP BY CodPersona
		ORDER BY CodEmpleado";
$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
while ($field = mysql_fetch_array($query)) {
	##	imprimo empleado
	$pdf->SetDrawColor(255, 255, 255);
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', 'BU', 8);
	$pdf->Ln(5);
	$pdf->Cell(12, 5, $field['CodEmpleado'], 0, 0, 'L', 0);
	$pdf->Cell(170, 5, utf8_decode($field['NomCompleto']), 0, 0, 'L', 0);
	$pdf->Ln(5);
	//	eventos
	$pdf->SetDrawColor(0, 0, 0);
	$pdf->SetFillColor(240, 240, 240);
	$pdf->SetWidths(array(18, 16, 16, 14, 30, 38, 74));
	$pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C'));
	$pdf->SetFont('Arial', 'B', 8);
	$pdf->Row(array('FECHA',
					'SALIDA',
					'ENTRADA',
					'TOTAL HORAS',
					'TIPO DE EVENTO',
					'MOTIVO',
					'OBSERVACIONES'));
	##	consulto eventos
	$SumH = 0;
	$SumM = 0;
	$sql = "SELECT
				bae.Fecha,
				bae.HoraSalida,
				bae.HoraEntrada,
				bae.TotalHoras,
				bae.Observaciones,
				p.NomCompleto,
				e.CodEmpleado,
				md1.Descripcion AS NomTipoEvento,
				md2.Descripcion AS NomMotivo
			FROM
				rh_bonoalimentacioneventos bae
				INNER JOIN mastpersonas p ON (p.CodPersona = bae.CodPersona)
				INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
				LEFT JOIN mastmiscelaneosdet md1 ON (md1.CodDetalle = bae.TipoEvento AND
													 md1.CodMaestro = 'TIPOFALTAS')
				LEFT JOIN mastmiscelaneosdet md2 ON (md2.CodDetalle = bae.Motivo AND
													 md2.CodMaestro = 'PERMISOS')
			WHERE
				bae.Anio = '".$field['Anio']."' AND
				bae.CodOrganismo = '".$field['CodOrganismo']."' AND
				bae.CodBonoAlim = '".$field['CodBonoAlim']."' AND
				bae.CodPersona = '".$field['CodPersona']."' AND
				bae.Fecha >= '".formatFechaAMD($FechaInicio)."' AND
				bae.Fecha <= '".formatFechaAMD($FechaFin)."'
			ORDER BY CodEmpleado";
	$query_eventos = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
	while ($field_eventos = mysql_fetch_array($query_eventos)) {
		list($_H, $_M) = split("[:]", $field_eventos['TotalHoras']);
		if ($_H < 10) $H = "0$_H"; else $H = "$_H";
		if ($_M < 10) $M = "0$_M"; else $M = "$_M";
		$TotalHoras = "$H:$M";
		$SumH += $_H;
		$SumM += $_M;
		$pdf->SetDrawColor(0, 0, 0);
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetFont('Arial', '', 8);
		$pdf->Row(array(formatFechaDMA($field_eventos['Fecha']),
						$field_eventos['HoraSalida'],
						$field_eventos['HoraEntrada'],
						$TotalHoras,
						utf8_decode($field_eventos['NomTipoEvento']),
						utf8_decode($field_eventos['NomMotivo']),
						utf8_decode($field_eventos['Observaciones'])));
	}
	if (mysql_num_rows($query_eventos) == 0) {
		$pdf->SetDrawColor(0, 0, 0);
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetWidths(array(206));
		$pdf->SetAligns(array('C'));
		$pdf->SetFont('Arial', 'I', 6);
		$pdf->Row(array('NO TIENE EVENTOS REGISTRADOS'));
	}
	$pdf->Ln(5);
	
	##	imprimo resultados
	$pdf->SetDrawColor(0, 0, 0);
	$pdf->SetFillColor(240, 240, 240);
	$pdf->SetWidths(array(30, 30, 30, 30, 30, 30));
	$pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C'));
	$pdf->SetFont('Arial', 'B', 8);
	$pdf->Row(array('HORAS FALTANTES',
					'HORAS TRABAJADAS',
					'DIAS DESCUENTO',
					'DIAS A PAGAR',
					'VALOR A DESCONTAR',
					'VALOR A PAGAR'));
	
	//	totalizo valores
	if ($SumM > 60) {
		$Horas = $SumH + intval($SumM / 60);
		$Minutos = ($Horas * 60) - $SumM;
	} else {
		$Horas = $SumH;
		$Minutos = $SumM;
	}
	if ($Horas < 10) $HF = "0$Horas"; else $HF = "$Horas";
	if ($Minutos < 10) $MF = "0$Minutos"; else $MF = "$Minutos";
	$HT = $HorasSemanal - $Horas;
	if ($Minutos > 0) $MT = 60 - $Minutos; else $MT = 0;
	if ($HT < 10) $HT = "0$HT"; else $HT = "$HT";
	if ($MT < 10) $MT = "0$MT"; else $MT = "$MT";
	$HorasFaltantes = "$HF:$MF";
	$HorasTrabajadas = "$HT:$MT";
	$DiasPago = 0;
	$DiasDescuento = 0;
	for($i=$DiffDias;$i<($DiffDias+$TotalDias);$i++) {
		$sql = "SELECT Dia".$i." AS Dia
				FROM rh_bonoalimentaciondet
				WHERE
					Anio = '".$field['Anio']."' AND
					CodOrganismo = '".$field['CodOrganismo']."' AND
					CodBonoAlim = '".$field['CodBonoAlim']."' AND
					CodPersona = '".$field['CodPersona']."'";
		$query_total = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
		if (mysql_num_rows($query_total) != 0) $field_total = mysql_fetch_array($query_total);
		//if ($field_total['Dia'] == "X") ++$DiasPago;
		if ($field_total['Dia'] == "X" || $field_total['Dia'] == "I") ++$DiasPago;
		elseif ($field_total['Dia'] == "D") ++$DiasDescuento;
	}
	$ValorDescuento = $field_resumen['ValorDia'] * $DiasDescuento;
	$TotalPagar = $field_resumen['ValorDia'] * $DiasPago;
	$pdf->SetDrawColor(0, 0, 0);
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', 'B', 8);
	$pdf->Row(array($HorasFaltantes,
					$HorasTrabajadas,
					$DiasDescuento,
					$DiasPago,
					number_format($ValorDescuento, 2, ',', '.'),
					number_format($TotalPagar, 2, ',', '.')));
	$pdf->Ln(10);
}
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>