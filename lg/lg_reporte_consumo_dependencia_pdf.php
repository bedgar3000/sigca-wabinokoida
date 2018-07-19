<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$LF = 0x0A;
$CR = 0x0D;
$nl = sprintf("%c%c",$CR,$LF);
//---------------------------------------------------
$filtro = "";
if ($fCodOrganismo != "") $filtro.= " AND (t.CodOrganismo = '".$fCodOrganismo."')";
if ($fCodDependencia != "") $filtro.= " AND (t.CodDependencia = '".$fCodDependencia."')";
if ($fCodCentroCosto != "") $filtro.= " AND (t.CodCentroCosto = '".$fCodCentroCosto."')";
if ($fPeriodoD != "") $filtro.= " AND (t.FechaDocumento >= '".formatFechaAMD($fPeriodoD)."')";
if ($fPeriodoH != "") $filtro.= " AND (t.FechaDocumento <= '".formatFechaAMD($fPeriodoH)."')";
if ($fCodItem != "") $filtro.= " AND (i.CodItem = '".$fCodItem."')";
if ($fCodLinea != "") $filtro.= " AND (i.CodLinea = '".$fCodLinea."')";
if ($fCodFamilia != "") $filtro.= " AND (i.CodFamilia = '".$fCodFamilia."')";
if ($fCodSubFamilia != "") $filtro.= " AND (i.CodSubFamilia = '".$fCodSubFamilia."')";
//---------------------------------------------------

class PDF extends FPDF {
	//	Cabecera de página.
	function Header() {
		global $_PARAMETRO;
		global $Ahora;
		global $field;
		global $_POST;
		extract($_POST);
		##
		$Logo = getValorCampo("mastorganismos", "CodOrganismo", "Logo", $_SESSION['ORGANISMO_ACTUAL']);
		$NomOrganismo = getValorCampo("mastorganismos", "CodOrganismo", "Organismo", $_SESSION['ORGANISMO_ACTUAL']);
		$NomDependencia = getValorCampo("mastdependencias", "CodDependencia", "Dependencia", $_PARAMETRO["DEPLOGCXP"]);
		##
		$this->SetFillColor(255, 255, 255);
		$this->SetDrawColor(0, 0, 0);
		$this->Image($_PARAMETRO["PATHLOGO"].$Logo, 3, 3, 10, 10);
		$this->SetFont('Arial', '', 8);
		$this->SetXY(12, 3); $this->Cell(100, 5, $NomOrganismo, 0, 1, 'L');
		$this->SetXY(12, 8); $this->Cell(100, 5, $NomDependencia, 0, 0, 'L');
		$this->SetFont('Arial', '', 8);
		$this->SetXY(175, 3); $this->Cell(10, 5, utf8_decode('Fecha: '), 0, 0, 'L');
		$this->Cell(30, 5, formatFechaDMA(substr($Ahora, 0, 10)), 0, 1, 'L');
		$this->SetXY(175, 8); $this->Cell(10, 5, utf8_decode('Página: '), 0, 0, 'L');
		$this->Cell(30, 5, $this->PageNo().' de {nb}', 0, 1, 'L');
		$this->SetFont('Arial', 'B', 10);
		$this->Ln(5);
		$this->Cell(210, 5, utf8_decode('Consumo por Dependencia'), 0, 1, 'C', 0);
		$this->Ln(5);
		##
		$this->SetDrawColor(0, 0, 0);
		$this->SetFillColor(200, 200, 200);
		$this->SetWidths(array(17,75,16,13,19,55,15));
		$this->SetAligns(array('C','L','C','R','R','L','C'));
		$this->SetFont('Arial', 'B', 7);
		$this->Row(array('Item',
						 utf8_decode('Descripción'),
						 utf8_decode('Transacción'),
						 'Cant.',
						 'Monto',
						 'Recibido Por',
						 'Fecha'));
		$this->Ln(1);
	}
	
	//	Pie de página.
	function Footer() {
		global $_PARAMETRO;
		global $nl;
		//	obtengo las firmas
		list($_PREPARADO['Nombre'], $_PREPARADO['Cargo'], $_PREPARADO['Nivel']) = getFirma($_PARAMETRO['FIRMALG1']);
		list($_REVISADO['Nombre'], $_REVISADO['Cargo'], $_REVISADO['Nivel']) = getFirmaxDependencia($_PARAMETRO['DEPLOGCXP']);
		list($_CONFORMADO['Nombre'], $_CONFORMADO['Cargo'], $_CONFORMADO['Nivel']) = getFirma($_PARAMETRO['FIRMALG2']);
		$Preparado = " $_PREPARADO[Nivel] $_PREPARADO[Nombre] $nl $_PREPARADO[Cargo]";
		$Revisado = " $_REVISADO[Nivel] $_REVISADO[Nombre] $nl $_REVISADO[Cargo]";
		$Conformado = " $_CONFORMADO[Nivel] $_CONFORMADO[Nombre] $nl $_CONFORMADO[Cargo]";
		##
		$this->SetDrawColor(0, 0, 0);
		$this->SetFillColor(255, 255, 255);
		$this->Rect(3, 245, 210, 25, 'DF');
		$this->Rect(73, 245, 0.1, 25, 'DF');
		$this->Rect(143, 245, 0.1, 25, 'DF');
		$this->Rect(3, 250, 210, 0.1, 'DF');
		##
		$this->SetFont('Arial', 'B', 8);
		$this->SetXY(3, 245); $this->Cell(70, 5, utf8_decode('PREPARADO POR'), 0, 1, 'L', 0);
		$this->SetXY(73, 245); $this->Cell(70, 5, utf8_decode('REVISADO POR'), 0, 1, 'L', 0);
		$this->SetXY(143, 245); $this->Cell(70, 5, utf8_decode('CONFORMADO POR'), 0, 1, 'L', 0);
		##
		$this->SetXY(3, 250); $this->MultiCell(70, 5, utf8_decode($Preparado), 0, 'L');
		$this->SetXY(73, 250); $this->MultiCell(70, 5, utf8_decode($Revisado), 0, 'L');
		$this->SetXY(143, 250); $this->MultiCell(70, 5, utf8_decode($Conformado), 0, 'L');
	}

	//	total x dependencia
	function TotalDependencia($Cantidad, $Total) {
		$y = $this->GetY() - 1;
		$this->SetDrawColor(0, 0, 0);
		$this->SetFillColor(0, 0, 0);
		$this->Rect(116, $y, 34, 0.1, 'DF');
		$this->Rect(116, $y+0.5, 34, 0.1, 'DF');
		$this->SetDrawColor(255, 255, 255);
		$this->SetFillColor(255, 255, 255);
		$this->SetFont('Arial', 'B', 7);
		$this->Cell(113, 4, 'Total Dependencia: ', 0, 0, 'R');
		$this->Cell(14, 4, number_format($Cantidad, 2, ',', '.'), 0, 0, 'R');
		$this->Cell(20, 4, number_format($Total, 2, ',', '.'), 0, 1, 'R');
		$this->Ln(2);
	}
	
	//	total x centro de costo
	function TotalCentroCosto($Cantidad, $Total) {
		$y = $this->GetY() - 1;
		$this->SetDrawColor(0, 0, 0);
		$this->SetFillColor(0, 0, 0);
		$this->Rect(116, $y, 34, 0.1, 'DF');
		$this->SetDrawColor(255, 255, 255);
		$this->SetFillColor(255, 255, 255);
		$this->SetFont('Arial', 'B', 7);
		$this->Cell(113, 4, 'Total Centro Costo: ', 0, 0, 'R');
		$this->Cell(14, 4, number_format($Cantidad, 2, ',', '.'), 0, 0, 'R');
		$this->Cell(20, 4, number_format($Total, 2, ',', '.'), 0, 1, 'R');
		$this->Ln(2);
	}
}
//---------------------------------------------------

//---------------------------------------------------
//	Creación del objeto de la clase heredada.
$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->SetMargins(3, 3, 3);
$pdf->SetAutoPageBreak(1, 35);
$pdf->AddPage();
//---------------------------------------------------
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetDrawColor(255, 255, 255);
//	consulto
$i = 0;
$sql = "SELECT
			t.CodOrganismo,
			t.CodDocumento,
			t.NroDocumento,
			t.CodTransaccion,
			t.FechaDocumento,
			t.Periodo,
			t.CodCentroCosto,
			t.CodDependencia,
			t.NroInterno,
			td.CodItem,
			td.CodUnidad,
			td.CantidadRecibida,
			td.Total,
			rd.Descripcion,
			d.Dependencia,
			cc.Descripcion AS NomCentroCosto,
			cc.Abreviatura,
			mp.NomCompleto AS NomRecibidoPor
		FROM
			lg_transaccion t
			INNER JOIN lg_transacciondetalle td ON (t.CodOrganismo = td.CodOrganismo AND
													  t.CodDocumento = td.CodDocumento AND
													  t.NroDocumento = td.NroDocumento)
			INNER JOIN lg_requerimientosdet rd ON (td.CodOrganismo = rd.CodOrganismo AND
												   td.ReferenciaNroDocumento = rd.CodRequerimiento AND
												   td.ReferenciaSecuencia = rd.Secuencia)
			INNER JOIN lg_tipotransaccion tt ON (t.CodTransaccion = tt.CodTransaccion AND tt.TipoMovimiento = 'E')
			INNER JOIN mastdependencias d ON (t.CodDependencia = d.CodDependencia)
			INNER JOIN ac_mastcentrocosto cc ON (t.CodCentroCosto = cc.CodCentroCosto)
			INNER JOIN mastpersonas mp ON (t.RecibidoPor = mp.CodPersona)
			INNER JOIN lg_itemmast i ON (i.CodItem = td.CodItem)
		WHERE t.Estado = 'CO' $filtro
		ORDER BY CodOrganismo, CodDependencia, CodCentroCosto, CodDocumento, NroInterno";
$field = getRecords($sql);
foreach($field as $f) {	++$i;
	if ($Grupo1 != $f['CodDependencia']) {
		$Grupo1 = $f['CodDependencia'];
		$Grupo2 = "";
		if ($NroDep > 1) {
			$pdf->TotalCentroCosto($CantidadCentroCosto, $TotalCentroCosto);
			$pdf->TotalDependencia($CantidadDependencia, $TotalDependencia);
		}
		$pdf->SetDrawColor(255, 255, 255);
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetFont('Arial', 'BU', 8);
		$pdf->Cell(20, 4, 'Dependencia: ', 0, 0, 'L');
		$pdf->Cell(240, 4, utf8_decode($f['Dependencia']), 0, 1, 'L');
		$CantidadDependencia = 0;
		$TotalDependencia = 0;
		$NroDep = 0;
		$NroCC = 0;
	}
	##
	if ($Grupo2 != $f['CodCentroCosto']) {
		$Grupo2 = $f['CodCentroCosto'];
		if ($NroCC > 1) $pdf->TotalCentroCosto($CantidadCentroCosto, $TotalCentroCosto);
		$pdf->SetFont('Arial', 'B', 8);
		$pdf->Cell(20, 4, 'Centro Costo: ', 0, 0, 'L');
		$pdf->Cell(240, 4, utf8_decode($f['Abreviatura'].' '.$f['NomCentroCosto']), 0, 1, 'L');
		$CantidadCentroCosto = 0;
		$TotalCentroCosto = 0;
	}
	##
	$pdf->SetFont('Arial', '', 7);
	$pdf->Row(array($f['CodItem'],
					utf8_decode($f['Descripcion']),
					$f['CodDocumento'].'-'.$f['NroInterno'],
					number_format($f['CantidadRecibida'], 2, ',', '.'),
					number_format($f['Total'], 2, ',', '.'),
					$f['NomRecibidoPor'],
					formatFechaDMA($f['FechaDocumento'])));
	$pdf->Ln(1);
	##
	$CantidadDependencia += $f['CantidadRecibida'];
	$TotalDependencia += $f['Total'];
	$CantidadCentroCosto += $f['CantidadRecibida'];
	$TotalCentroCosto += $f['Total'];
	++$NroDep;
	++$NroCC;
}
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
