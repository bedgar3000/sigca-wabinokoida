<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$filtro = '';
if ($fCodOrganismo != "") $filtro.=" AND (vd.CodOrganismo = '".$fCodOrganismo."')";
if ($fCodContabilidad != "") $filtro.=" AND (vd.CodContabilidad = '".$fCodContabilidad."')";
if ($fPeriodo != "") $filtro.=" AND (vd.Periodo LIKE '".$fPeriodo."-%')";
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
		$Logo = getValorCampo("mastorganismos", "CodOrganismo", "Logo", $fCodOrganismo);
		$NomOrganismo = getValorCampo("mastorganismos", "CodOrganismo", "Organismo", $fCodOrganismo);
		$NomDependencia = getValorCampo("mastdependencias", "CodDependencia", "Dependencia", $_PARAMETRO["DEPAT"]);
		##
		$this->SetFillColor(255,255,255);
		$this->SetDrawColor(0,0,0);
		$this->Image($_PARAMETRO["PATHLOGO"].$Logo, 5, 5, 10, 10);		
		$this->SetFont('Arial', '', 8);
		$this->SetXY(15, 5); $this->Cell(100, 5, $NomOrganismo, 0, 1, 'L');
		$this->SetXY(15, 10); $this->Cell(100, 5, $NomDependencia, 0, 0, 'L');	
		$this->SetFont('Arial', '', 8);
		$this->SetXY(240, 5); $this->Cell(20, 5, utf8_decode('Fecha: '), 0, 0, 'L');
		$this->Cell(30, 5, formatFechaDMA(substr($Ahora, 0, 10)), 0, 1, 'L');
		$this->SetXY(240, 10); $this->Cell(20, 5, utf8_decode('Página: '), 0, 0, 'L'); 
		$this->Cell(30, 5, $this->PageNo().' de {nb}', 0, 1, 'L');
		$this->SetFont('Arial', 'B', 10);
		$this->SetY(20); $this->Cell(270, 5, utf8_decode('CIERRE DE CUENTAS'), 0, 1, 'C');
		$this->Ln(5);
		##	-------------------
		if ($fPeriodo) {
			$this->SetFont('Arial', '', 8);
			$this->Cell(30, 5, utf8_decode('PERIODO: '), 0, 0, 'L');
			$this->SetFont('Arial', 'B', 8);
			$this->Cell(240, 5, utf8_decode($fPeriodo), 0, 0, 'L');
			$this->Ln(5);
		}
		if ($fCodContabilidad) {
			$Contabilidad = getVar3("SELECT Descripcion FROM ac_contabilidades WHERE CodContabilidad = '".$fCodContabilidad."'");
			$this->SetFont('Arial', '', 8);
			$this->Cell(30, 5, utf8_decode('CONTABILIDAD: '), 0, 0, 'L');
			$this->SetFont('Arial', 'B', 8);
			$this->Cell(240, 5, utf8_decode($Contabilidad), 0, 0, 'L');
			$this->Ln(5);
		}
		##	-------------------
		$this->SetTextColor(0,0,0);
		$this->SetDrawColor(0,0,0);
		$this->SetFillColor(255,255,255);
		$this->Ln(3);
		##
		$this->SetFont('Arial', 'B', 8);
		$this->SetWidths(array(30,170,35,35));
		$this->SetAligns(array('L','L','R','R'));
		$this->Row(array(utf8_decode('CUENTA'),
						 utf8_decode('DESCRIPCION'),
						 utf8_decode('DEBE'),
						 utf8_decode('HABER')
						 ));
		$this->Ln(1);
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
$pdf->AddPage();
//---------------------------------------------------
$Asiento = 0;
$sql = "SELECT
			*,
			(CASE WHEN Monto > 0 THEN 0 ELSE 1 END) AS 'Pos'
		FROM ac_cierreanual
		WHERE
			CodOrganismo = '".$fCodOrganismo."' AND
			Periodo = '".$fPeriodo."' AND
			CodContabilidad = '".$fCodContabilidad."'
		ORDER BY Asiento ASC, Pos ASC";
$field_cierre = getRecords($sql);
if (count($field_cierre)) {
	foreach($field_cierre as $f) {
		if ($Asiento != $f['Asiento']) {
			//	totales
			if ($Asiento) {
				$pdf->SetDrawColor(0,0,0);
				$pdf->Line(205, $pdf->GetY(), 275, $pdf->GetY());
				$pdf->Ln(1);
				$pdf->SetDrawColor(255,255,255);
				$pdf->SetFont('Arial', 'B', 8);
				$pdf->Row(array('',
								'',
								number_format($TotalDebe,2,',','.'),
								number_format($TotalHaber,2,',','.')
								));
				$pdf->Ln(5);
			}
			$Asiento = $f['Asiento'];
			$TotalDebe = 0.00;
			$TotalHaber = 0.00;
			//	Asiento.
			$pdf->SetFont('Arial', 'B', 8);
			$pdf->Cell(205, 5, utf8_decode('ASIENTO '.$f['Asiento'].'.'), 0, 1, 'L');
			$pdf->Ln(3);
		}
		if ($f['Monto'] >= 0) {
			$MontoDebe = $f['Monto'];
			$MontoHaber = 0.00;
		} else {
			$MontoDebe = 0.00;
			$MontoHaber = $f['Monto'];
		}
		$pdf->SetTextColor(0,0,0);
		$pdf->SetDrawColor(255,255,255);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial', '', 8);
		$pdf->Row(array($f['CodCuenta'],
						utf8_decode($f['Descripcion']),
						number_format($MontoDebe,2,',','.'),
						number_format($MontoHaber,2,',','.')
						));
		$pdf->Ln(1);
		##	
		$TotalDebe += $MontoDebe;
		$TotalHaber += $MontoHaber;
	}
	$pdf->SetDrawColor(0,0,0);
	$pdf->Line(205, $pdf->GetY(), 275, $pdf->GetY());
	$pdf->Ln(1);
	$pdf->SetDrawColor(255,255,255);
	$pdf->SetFont('Arial', 'B', 8);
	$pdf->Row(array('',
					'',
					number_format($TotalDebe,2,',','.'),
					number_format($TotalHaber,2,',','.')
					));
	$pdf->Ln(5);
} else {
	$Descripcion23309 = getVar3("SELECT Descripcion FROM ac_mastplancuenta20 WHERE CodCuenta = '23309'");
	$Descripcion232990101 = getVar3("SELECT Descripcion FROM ac_mastplancuenta20 WHERE CodCuenta = '232990101'");
	//	Asiento 1.
	$pdf->SetFont('Arial', 'B', 8);
	$pdf->Cell(205, 5, utf8_decode('ASIENTO 1.'), 0, 1, 'L');
	$pdf->Ln(3);
	if ($fCodContabilidad == 'F') {
		$sql = "SELECT
					pc.CodCuenta,
					pc.Descripcion,
					ABS(SUM(vd.MontoVoucher)) AS Monto
				FROM
					ac_voucherdet vd
					INNER JOIN ac_mastplancuenta20 pc ON (pc.CodCuenta = vd.CodCuenta)
				WHERE vd.Estado = 'MA' AND vd.CodCuenta = '51301' $filtro
				ORDER BY CodCuenta";
	}
	$field = getRecords($sql);
	$Monto51301 = 0;
	foreach($field as $f) {
		$pdf->SetTextColor(0,0,0);
		$pdf->SetDrawColor(255,255,255);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial', '', 8);
		$pdf->Row(array($f['CodCuenta'],
						utf8_decode($f['Descripcion']),
						number_format($f['Monto'],2,',','.'),
						''
						));
		$pdf->Ln(1);
		##	
		$Monto51301 += $f['Monto'];
	}
	$pdf->Row(array('23309',
					utf8_decode($Descripcion23309),
					'',
					number_format(-$f['Monto'],2,',','.')
					));
	$pdf->SetDrawColor(0,0,0);
	$pdf->Line(205, $pdf->GetY(), 275, $pdf->GetY());
	$pdf->Ln(1);
	$pdf->SetDrawColor(255,255,255);
	$pdf->SetFont('Arial', 'B', 8);
	$pdf->Row(array('',
					'',
					number_format($f['Monto'],2,',','.'),
					number_format(-$f['Monto'],2,',','.')
					));
	$pdf->Ln(5);
	//	Asiento 2.
	$pdf->SetFont('Arial', 'B', 8);
	$pdf->Cell(205, 5, utf8_decode('ASIENTO 2.'), 0, 1, 'L');
	$pdf->Ln(3);
	if ($fCodContabilidad == 'F') {
		$sql = "SELECT
					pc.CodCuenta,
					pc.Descripcion,
					ABS(SUM(vd.MontoVoucher)) AS Monto
				FROM
					ac_voucherdet vd
					INNER JOIN ac_mastplancuenta20 pc ON (pc.CodCuenta = vd.CodCuenta)
				WHERE vd.Estado = 'MA' AND vd.CodCuenta = '51303' $filtro
				ORDER BY CodCuenta";
	}
	$field = getRecords($sql);
	$Monto51303 = 0;
	foreach($field as $f) {
		$pdf->SetTextColor(0,0,0);
		$pdf->SetDrawColor(255,255,255);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial', '', 8);
		$pdf->Row(array($f['CodCuenta'],
						utf8_decode($f['Descripcion']),
						number_format($f['Monto'],2,',','.'),
						''
						));
		$pdf->Ln(1);
		##	
		$Monto51303 += $f['Monto'];
	}
	$pdf->Row(array('23309',
					utf8_decode($Descripcion23309),
					'',
					number_format(-$f['Monto'],2,',','.')
					));
	$pdf->SetDrawColor(0,0,0);
	$pdf->Line(205, $pdf->GetY(), 275, $pdf->GetY());
	$pdf->Ln(1);
	$pdf->SetDrawColor(255,255,255);
	$pdf->SetFont('Arial', 'B', 8);
	$pdf->Row(array('',
					'',
					number_format($f['Monto'],2,',','.'),
					number_format(-$f['Monto'],2,',','.')
					));
	$pdf->Ln(5);
	//	Asiento 3.
	$pdf->SetFont('Arial', 'B', 8);
	$pdf->Cell(205, 5, utf8_decode('ASIENTO 3.'), 0, 1, 'L');
	$pdf->Ln(3);
	if ($fCodContabilidad == 'F') {
		$sql = "SELECT ABS(SUM(vd.MontoVoucher)) AS Monto
				FROM ac_voucherdet vd
				WHERE vd.Estado = 'MA' AND vd.CodCuenta LIKE '61300%' $filtro";
	}
	$Monto = getVar3($sql);
	$pdf->SetTextColor(0,0,0);
	$pdf->SetDrawColor(255,255,255);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial', '', 8);
	$pdf->Row(array('23309',
					utf8_decode($Descripcion23309),
					number_format($Monto,2,',','.'),
					''
					));
	$pdf->Ln(1);
	if ($fCodContabilidad == 'F') {
		$sql = "SELECT
					pc.CodCuenta,
					pc.Descripcion,
					SUM(vd.MontoVoucher) AS Monto
				FROM
					ac_voucherdet vd
					INNER JOIN ac_mastplancuenta20 pc ON (pc.CodCuenta = vd.CodCuenta)
				WHERE vd.Estado = 'MA' AND vd.CodCuenta LIKE '61300%' $filtro
				GROUP BY CodCuenta
				ORDER BY CodCuenta";
	}
	$field = getRecords($sql);
	$Monto61300 = 0;
	foreach($field as $f) {
		$pdf->SetTextColor(0,0,0);
		$pdf->SetDrawColor(255,255,255);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial', '', 8);
		$pdf->Row(array($f['CodCuenta'],
						utf8_decode($f['Descripcion']),
						'',
						number_format(-$f['Monto'],2,',','.')
						));
		$pdf->Ln(1);
		##	
		$Monto61300 += $f['Monto'];
	}
	$pdf->SetDrawColor(0,0,0);
	$pdf->Line(205, $pdf->GetY(), 275, $pdf->GetY());
	$pdf->Ln(1);
	$pdf->SetDrawColor(255,255,255);
	$pdf->SetFont('Arial', 'B', 8);
	$pdf->Row(array('',
					'',
					number_format($Monto,2,',','.'),
					number_format(-$Monto61300,2,',','.')
					));
	$pdf->Ln(5);
	//	Asiento 4.
	$pdf->SetFont('Arial', 'B', 8);
	$pdf->Cell(205, 5, utf8_decode('ASIENTO 4.'), 0, 1, 'L');
	$pdf->Ln(3);
	$MontoDebe = $Monto51301 + $Monto51303 - $Monto61300;
	$MontoHaber = -$MontoDebe;
	$pdf->SetTextColor(0,0,0);
	$pdf->SetDrawColor(255,255,255);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial', '', 8);
	$pdf->Row(array('23309',
					utf8_decode($Descripcion23309),
					number_format($MontoDebe,2,',','.'),
					''
					));
	$pdf->Ln(1);
	$pdf->Row(array('232990101',
					utf8_decode($Descripcion232990101),
					'',
					number_format($MontoHaber,2,',','.')
					));
	$pdf->SetDrawColor(0,0,0);
	$pdf->Line(205, $pdf->GetY(), 275, $pdf->GetY());
	$pdf->Ln(1);
	$pdf->SetDrawColor(255,255,255);
	$pdf->SetFont('Arial', 'B', 8);
	$pdf->Row(array('',
					'',
					number_format($MontoDebe,2,',','.'),
					number_format($MontoHaber,2,',','.')
					));
	
}

//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
