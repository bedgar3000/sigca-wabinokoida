<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$filtro = "";
if ($fEstado == "") $filtro.=" AND (e.Estado = 'A')"; else $cEstado = "checked";
if ($fCodOrganismo != "") { $cCodOrganismo = "checked"; $filtro.=" AND (tne.CodOrganismo = '".$fCodOrganismo."')"; } else $dCodOrganismo = "disabled";
if ($fCodTipoNom != "") { $cCodTipoNom = "checked"; $filtro.=" AND (tne.CodTipoNom = '".$fCodTipoNom."')"; } else $dCodTipoNom = "disabled";
if ($fPeriodo != "") { $cPeriodo = "checked"; $filtro.=" AND (tne.Periodo = '".$fPeriodo."')"; } else $dPeriodo = "disabled";
//---------------------------------------------------

class PDF extends FPDF {
	//	Cabecera de página.
	function Header() {
		global $_PARAMETRO;
		global $Ahora;
		global $field_resumen;
		global $_POST;
		global $_GET;
		extract($_POST);
		extract($_GET);
		##
		$Logo = getValorCampo("mastorganismos", "CodOrganismo", "Logo", $fCodOrganismo);
		$NomOrganismo = getValorCampo("mastorganismos", "CodOrganismo", "Organismo", $fCodOrganismo);
		$NomDependencia = getValorCampo("mastdependencias", "CodDependencia", "Dependencia", $_PARAMETRO["DEPRHPR"]);
		##
		$this->SetFillColor(255, 255, 255);
		$this->SetDrawColor(0, 0, 0);
		$this->Image($_PARAMETRO["PATHLOGO"].$Logo, 10, 5, 10, 10);
		$this->SetFont('Arial', '', 8);
		$this->SetXY(20, 5); $this->Cell(100, 5, $NomOrganismo, 0, 1, 'L');
		$this->SetXY(20, 10); $this->Cell(100, 5, $NomDependencia, 0, 0, 'L');
		$this->SetFont('Arial', '', 8);
		$this->SetXY(235, 5); $this->Cell(10, 5, utf8_decode('Fecha: '), 0, 0, 'L');
		$this->Cell(30, 5, formatFechaDMA(substr($Ahora, 0, 10)), 0, 1, 'L');
		$this->SetXY(235, 10); $this->Cell(10, 5, utf8_decode('Página: '), 0, 0, 'L');
		$this->Cell(30, 5, $this->PageNo().' de {nb}', 0, 1, 'L');
		$this->SetFont('Arial', 'B', 10);
		$this->SetXY(10, 20); $this->Cell(260, 5, utf8_decode('ACUMULADO DE ANTIGUEDAD'), 0, 1, 'C', 0);
		$this->SetFont('Arial', 'BI', 8);
		$this->SetXY(10, 24); $this->Cell(260, 5, utf8_decode('Periodo '.$fPeriodo), 0, 1, 'C', 0);
		$this->Ln(5);
		##
		$this->SetFont('Arial', 'B', 8);
		$this->SetWidths(array(18, 85, 20, 15, 13, 13, 13, 13, 13, 10, 17, 17, 17));
		$this->SetAligns(array('R', 'L', 'C', 'R', 'R', 'R', 'R', 'R', 'R', 'C', 'R', 'R', 'R'));
		$this->Row(array('Documento',
						 'Nombre Completo',
						 'F.Ingreso',
						 'Sueldo Mensual',
						 'Bonos',
						 'Diario',
						 'Ali. Vac.',
						 'Ali. Fin.',
						 'Sueldo + Alic.',
						 'Dias',
						 'Prest. Antig.',
						 'Prest. Compl.',
						 'Total'));
		$this->Ln(2);
	}
	
	//	Pie de página.
	function Footer() {
		$this->SetTextColor(0, 0, 0);
		$this->SetDrawColor(0, 0, 0);
		$this->SetFillColor(0, 0, 0);
		##
		$this->SetFont('Arial', 'B', 8);
		$this->SetXY(40, 193); $this->MultiCell(65, 4, 'PREPARADO POR', 0, 'C');
		$this->SetXY(160, 193); $this->MultiCell(65, 4, 'REVISADO POR', 0, 'C');
		$this->SetXY(40, 197); $this->MultiCell(65, 4, 'LICDA. ANDREINA ZAPATA', 0, 'C');
		$this->SetXY(160, 197); $this->MultiCell(65, 4, 'LICDA. OMAIRYS MONTERO', 0, 'C');
		$this->SetXY(40, 201); $this->MultiCell(65, 4, 'ANALISTA DE RECURSOS HUMANOS I', 0, 'C');
		$this->SetXY(160, 201); $this->MultiCell(65, 4, 'DIRECTORA DE RECURSOS HUMANOS', 0, 'C');
	}
}
//---------------------------------------------------

//---------------------------------------------------
//	Creación del objeto de la clase heredada.
$pdf = new PDF('L', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(5, 30);
$pdf->AddPage();
//---------------------------------------------------
$sql = "SELECT
			p.CodPersona,
			p.NomCompleto,
			p.Ndocumento,
			e.CodEmpleado,
			e.Fingreso,
			SUBSTRING(e.Fingreso,1,4) AS AnioIngreso,
			SUBSTRING(e.Fingreso,1,7) AS PeriodoIngreso,
			SUBSTRING(e.Fingreso,6,2) AS MesIngreso,
			SUBSTRING(e.Fingreso,9,2) AS DiaIngreso,
			e.Fegreso,
			SUBSTRING(e.Fegreso,1,4) AS AnioEgreso,
			SUBSTRING(e.Fegreso,1,7) AS PeriodoEgreso,
			SUBSTRING(e.Fegreso,6,2) AS MesEgreso,
			SUBSTRING(e.Fegreso,9,2) AS DiaEgreso,
			FechaFinNomina,
			SUBSTRING(e.FechaFinNomina,1,4) AS AnioFin,
			SUBSTRING(e.FechaFinNomina,1,7) AS PeriodoFin,
			SUBSTRING(e.FechaFinNomina,6,2) AS MesFin,
			SUBSTRING(e.FechaFinNomina,9,2) AS DiaFin,
			e.Estado,
			afd.Dias,
			afd.Transaccion,
			afd.DiasAdicional,
			afd.Complemento,
			(afd.Transaccion + afd.Complemento) AS Total
		FROM
			pr_tiponominaempleado tne
			INNER JOIN mastpersonas p ON (p.CodPersona = tne.CodPersona)
			INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
			INNER JOIN pr_acumuladofideicomisodetalle afd ON (afd.CodPersona = tne.CodPersona AND
															  afd.Periodo = tne.Periodo)
		WHERE
			afd.FlagFraccionado <> 'S'
			$filtro
		GROUP BY tne.Periodo, tne.CodPersona
		ORDER BY LENGTH(p.Ndocumento), p.Ndocumento";
$query = mysql_query($sql) or die(getErrorSql(mysql_errno(), mysql_error(), $sql));
while ($field = mysql_fetch_array($query)) {
	$DiasPeriodo = getDiasMes($fad['Periodo']);
	if ($field['Estado'] == 'I' && $fPeriodo == $field['PeriodoFin']) $DiasTrabajados = intval($field['DiaFin']); 
	elseif ($fPeriodo == $field['PeriodoIngreso']) $DiasTrabajados = 30 - $field['DiaIngreso'] + 1;
	else $DiasTrabajados = $DiasPeriodo;
	$DiasTrabajados = (($DiasTrabajados <= 30)?$DiasTrabajados:30);
	if ($DiasTrabajados == $DiasPeriodo) $DiasParaDiario = 30; else $DiasParaDiario = $DiasTrabajados;
	$SueldoMensual = getVar2("rh_sueldos", "SueldoNormal", array("Periodo","CodPersona"), array($fPeriodo,$field['CodPersona']));
	$Bono = getBono($field['CodPersona'], $fPeriodo);
	$RemuneracionMensual = $SueldoMensual + $Bono;
	$SueldoDiario = round(($SueldoMensual / $DiasParaDiario), 2);
	$BonoDiario = round(($Bono / 30), 2);
	$RemuneracionDiaria = $SueldoDiario + $BonoDiario;
	$AliVac = getVar2("rh_sueldos", "AliVac", array("Periodo","CodPersona"), array($fPeriodo,$field['CodPersona']));
	$AliFin = getVar2("rh_sueldos", "AliFin", array("Periodo","CodPersona"), array($fPeriodo,$field['CodPersona']));
	$SueldoAlicuotas = $RemuneracionDiaria + $AliVac + $AliFin;
	##
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetDrawColor(255, 255, 255);
	if (++$i % 2 == 0) $pdf->SetFillColor(240, 240, 240); else $pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 8);
	$pdf->Row(array(number_format($field['Ndocumento'], 0, '', '.'),
					utf8_decode($field['NomCompleto']),
					formatFechaDMA($field['Fingreso']),
					number_format($SueldoMensual, 2, ',', '.'),
					number_format($Bono, 2, ',', '.'),
					number_format($RemuneracionDiaria, 2, ',', '.'),
					number_format($AliVac, 2, ',', '.'),
					number_format($AliFin, 2, ',', '.'),
					number_format($SueldoAlicuotas, 2, ',', '.'),
					number_format($field['Dias'], 2, ',', '.'),
					number_format($field['Transaccion'], 2, ',', '.'),
					number_format($field['Complemento'], 2, ',', '.'),
					number_format($field['Total'], 2, ',', '.')));
	$SumMonto += $field['Transaccion'];
	$SumComplemento += $field['Complemento'];
	$SumTotal += $field['Total'];
}
##
$pdf->Cell(213, 5);
$pdf->SetDrawColor(255, 255, 255);
$pdf->SetFillColor(200, 200, 200);
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetWidths(array(17, 17, 17));
$pdf->SetAligns(array('R', 'R', 'R'));
$pdf->Row(array(number_format($SumMonto, 2, ',', '.'),
				number_format($SumComplemento, 2, ',', '.'),
				number_format($SumTotal, 2, ',', '.')));
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>