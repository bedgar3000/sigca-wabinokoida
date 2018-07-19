<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$LF = 0x0A;
$CR = 0x0D;
$nl = sprintf("%c%c",$CR,$LF);
//---------------------------------------------------
##	acumulados
$sql = "SELECT * FROM pr_acumuladofideicomiso WHERE CodPersona = '".$CodPersona."'";
$field_acumulado = getRecord($sql);
##	empleado
$sql = "SELECT
			p.Ndocumento,
			p.NomCompleto,
			e.Fingreso,
			pt.DescripCargo,
			d.Dependencia
		FROM
			mastpersonas p
			INNER JOIN mastempleado e ON (e.codPersona = p.CodPersona)
			INNER JOIN rh_puestos pt ON (pt.CodCargo = e.CodCargo)
			INNER JOIN mastdependencias d ON (d.CodDependencia = e.CodDependencia)
		WHERE e.CodPersona = '".$CodPersona."'";
$field_empleado = getRecord($sql);
$Antiguedad = edad($Fingreso, formatFechaDMA($FechaActual));
//---------------------------------------------------

class PDF extends FPDF {
	//	Cabecera de página.
	function Header() {
		global $_PARAMETRO;
		global $Ahora;
		global $field_empleado;
		global $Antiguedad;
		global $PrimeraPagina;
		global $_POST;
		extract($_POST);
		##
		$Logo = getValorCampo("mastorganismos", "CodOrganismo", "Logo", $_SESSION['ORGANISMO_ACTUAL']);
		$NomOrganismo = getValorCampo("mastorganismos", "CodOrganismo", "Organismo", $_SESSION['ORGANISMO_ACTUAL']);
		$NomDependencia = getValorCampo("mastdependencias", "CodDependencia", "Dependencia", $_PARAMETRO["DEPLOGCXP"]);
		##
		$this->SetTextColor(0,0,0);
		$this->SetFillColor(200,200,200);
		$this->SetDrawColor(0,0,0);
		$this->Image($_PARAMETRO["PATHLOGO"].$Logo, 5, 5, 10, 10);
		$this->SetFont('Arial', '', 8);
		$this->SetXY(15, 5); $this->Cell(100, 5, utf8_decode($NomOrganismo), 0, 1, 'L');
		$this->SetXY(15, 10); $this->Cell(100, 5, utf8_decode($NomDependencia), 0, 0, 'L');
		$this->Ln(10);
		##
		$this->SetFont('Arial', 'B', 10);
		$this->Cell(270, 5, utf8_decode('GARANTIA DE PRESTACIONES'), 0, 1, 'C');
		$this->Ln(5);
		##
		if ($PrimeraPagina) {
			$this->SetFont('Arial', 'B', 8);
			$this->Cell(25, 5, utf8_decode('EMPLEADO:'), 0, 0, 'L');
			$this->SetFont('Arial', '', 8);
			$this->Cell(175, 5, utf8_decode($field_empleado['NomCompleto']), 0, 0, 'L');
			$this->SetFont('Arial', 'B', 8);
			$this->Cell(30, 5, utf8_decode('DOCUMENTO:'), 0, 0, 'L');
			$this->SetFont('Arial', '', 8);
			$this->Cell(90, 5, number_format($field_empleado['Ndocumento'],0,'','.'), 0, 0, 'L');
			$this->Ln(5);
			$this->SetFont('Arial', 'B', 8);
			$this->Cell(25, 5, utf8_decode('CARGO:'), 0, 0, 'L');
			$this->SetFont('Arial', '', 8);
			$this->Cell(175, 5, utf8_decode($field_empleado['DescripCargo']), 0, 0, 'L');
			$this->SetFont('Arial', 'B', 8);
			$this->Cell(30, 5, utf8_decode('FECHA INGRESO:'), 0, 0, 'L');
			$this->SetFont('Arial', '', 8);
			$this->Cell(90, 5, formatFechaDMA($field_empleado['Fingreso']), 0, 0, 'L');
			$this->Ln(5);
			$this->SetFont('Arial', 'B', 8);
			$this->Cell(25, 5, utf8_decode('DEPENDENCIA:'), 0, 0, 'L');
			$this->SetFont('Arial', '', 8);
			$this->Cell(175, 5, utf8_decode($field_empleado['Dependencia']), 0, 0, 'L');
			$this->SetFont('Arial', 'B', 8);
			$this->Cell(30, 5, utf8_decode('ANTIGUEDAD:'), 0, 0, 'L');
			$this->SetFont('Arial', '', 8);
			$this->Cell(90, 5, utf8_decode($Antiguedad['Anios'].' años - '.$Antiguedad['Meses'].' meses - '.$Antiguedad['Dias'].' dias '), 0, 0, 'L');
			$this->Ln(5);
		}
		##
		$this->SetFont('Arial', 'B', 6);
		$this->SetWidths(array(15,25,25,15,15,22,23,10,10,15,15,25,25,30));
		$this->SetAligns(array('C','R','R','R','R','R','R','C','C','C','C','R','R','R'));
		$this->Row(array(utf8_decode('PERIODO'),
						 utf8_decode('SUELDO MENSUAL'),
						 utf8_decode('BONOS'),
						 utf8_decode('ALI. VAC.'),
						 utf8_decode('ALI. FIN.'),
						 utf8_decode('REMUN. DIARIA'),
						 utf8_decode('REMUN. DIARIA + ALIC.'),
						 utf8_decode('AÑOS'),
						 utf8_decode('MESES'),
						 utf8_decode('DIAS PREST.'),
						 utf8_decode('DIAS ADIC.'),
						 utf8_decode('PREST. ANTIG. MENSUAL'),
						 utf8_decode('PREST. COMPL.'),
						 utf8_decode('PREST. ACUMULADA')));
	}
	
	//	Pie de página.
	function Footer() {}
}
//---------------------------------------------------

//---------------------------------------------------
//	Creación del objeto de la clase heredada.
$pdf = new PDF('L', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->SetMargins(5, 5, 5);
$pdf->SetAutoPageBreak(5, 5);
$PrimeraPagina = true;
$pdf->AddPage();
$PrimeraPagina = false;
//---------------------------------------------------
$i = 0;
$Fi = explode('-', $Fingreso);
$PeriodoIngreso = $Fi[2].'-'.$Fi[1];
$Tiempo = edad($Fingreso, formatFechaDMA($field_acumulado['PeriodoInicial'].'-'.$Fi[0]));
$Anios = $Tiempo['Anios'];
$Meses = $Tiempo['Meses'];
if ($field_acumulado['PeriodoInicial'] > $PeriodoIngreso) {
	--$Meses;
	if ($Meses < 0) { $Meses = 11; --$Anios; }
}
##	acumulado inicial
$pdf->SetTextColor(0,0,0);
$pdf->SetDrawColor(0,0,0);
$pdf->SetFillColor(210,210,210);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Row(array('',
				'',
				'',
				'',
				'',
				'',
				'',
				$Anios,
				$Meses,
				number_format($field_acumulado['AcumuladoInicialDias'],2,',','.'),
				number_format($field_acumulado['AcumuladoDiasAdicionalInicial'],2,',','.'),
				'',
				'',
				number_format($field_acumulado['AcumuladoInicialProv'],2,',','.')));
##	consulto
$AcumuladoDias = $field_acumulado['AcumuladoInicialDias'];
$AcumuladoComplemento = $field_acumulado['AcumuladoDiasAdicionalInicial'];
$sql = "SELECT
			s.Periodo,
			s.SueldoNormal,
			fc.Bonificaciones,
			fc.AliVac,
			fc.AliFin,
			fc.SueldoDiario,
			fc.SueldoDiarioAli,
			fc.Dias,
			DiasComplemento,
			PrestAntiguedad,
			PrestComplemento,
			PrestAcumulada
		FROM
			rh_sueldos s
			LEFT JOIN pr_fideicomisocalculo fc ON (fc.CodPersona = s.CodPersona AND
												   fc.Periodo = s.Periodo)
		WHERE
			s.CodPersona = '".$CodPersona."' AND
			s.Periodo >= '".$field_acumulado['PeriodoInicial']."'
		ORDER BY Periodo";
$field = getRecords($sql);
foreach($field as $f) {
	++$i;
	++$Meses;
	if ($Meses > 11) { ++$Anios; $Meses = 0; }
	$AcumuladoDias += $f['Dias'];
	$AcumuladoComplemento += $f['DiasComplemento'];
	##
	$pdf->SetTextColor(0,0,0);
	$pdf->SetDrawColor(0,0,0);
	if ($i % 2 == 0) $pdf->SetFillColor(240,240,240); else $pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial', '', 8);
	$pdf->Row(array($f['Periodo'],
					number_format($f['SueldoNormal'],2,',','.'),
					number_format($f['Bonificaciones'],2,',','.'),
					number_format($f['AliVac'],2,',','.'),
					number_format($f['AliFin'],2,',','.'),
					number_format($f['SueldoDiario'],2,',','.'),
					number_format($f['SueldoDiarioAli'],2,',','.'),
					$Anios,
					$Meses,
					number_format($f['Dias'],2,',','.'),
					number_format($f['DiasComplemento'],2,',','.'),
					number_format($f['PrestAntiguedad'],2,',','.'),
					number_format($f['PrestComplemento'],2,',','.'),
					number_format($f['PrestAcumulada'],2,',','.')));
	##
	$Periodo = explode('-', $f['Periodo']);
	if ($Periodo[1] == '12' || $i == count($field)) {
		$pdf->SetTextColor(0,0,0);
		$pdf->SetDrawColor(0,0,0);
		$pdf->SetFillColor(210,210,210);
		$pdf->SetFont('Arial', 'B', 8);
		$pdf->Row(array('',
						'',
						'',
						'',
						'',
						'',
						'',
						$Anios,
						$Meses,
						number_format($AcumuladoDias,2,',','.'),
						number_format($AcumuladoComplemento,2,',','.'),
						'',
						'',
						number_format($f['PrestAcumulada'],2,',','.')));
	}
}
//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
