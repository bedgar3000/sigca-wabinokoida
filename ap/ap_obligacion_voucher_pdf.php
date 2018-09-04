<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
list($CodOrganismo, $CodProveedor, $CodTipoDocumento, $NroDocumento) = explode('_', $registro);
if ($_PARAMETRO['CONTORDENDIS'] == "T") {
	$sql = "SELECT 
				vm.*,
				og.Organismo,
				p1.NomCompleto AS NomPreparadoPor,
				p2.NomCompleto AS NomAprobadoPor,
				lc.Descripcion AS NomLibroCont
			FROM
				ac_vouchermast vm
				INNER JOIN ap_obligaciones o ON (o.CodOrganismo = vm.CodOrganismo AND
												 o.VoucherPeriodo = vm.Periodo AND
												 o.Voucher = vm.Voucher)
				INNER JOIN mastorganismos og ON (og.CodOrganismo = vm.CodOrganismo)
				INNER JOIN mastpersonas p1 ON (p1.CodPersona = vm.PreparadoPor)
				INNER JOIN mastpersonas p2 ON (p2.CodPersona = vm.AprobadoPor)
				INNER JOIN ac_librocontable lc ON (lc.CodLibroCont = vm.CodLibroCont)
			WHERE
				o.CodOrganismo = '$CodOrganismo' AND
				o.CodProveedor = '$CodProveedor' AND
				o.CodTipoDocumento = '$CodTipoDocumento' AND
				o.NroDocumento = '$NroDocumento' AND
				vm.CodContabilidad = 'T'";
}
elseif ($_PARAMETRO['CONTORDENDIS'] == "F") {
	$sql = "SELECT 
				vm.*,
				og.Organismo,
				p1.NomCompleto AS NomPreparadoPor,
				p2.NomCompleto AS NomAprobadoPor,
				lc.Descripcion AS NomLibroCont
			FROM
				ac_vouchermast vm
				INNER JOIN ap_obligaciones o ON (o.CodOrganismo = vm.CodOrganismo AND
												 o.VoucherPeriodoPub20 = vm.Periodo AND
												 o.VoucherPub20 = vm.Voucher)
				INNER JOIN mastorganismos og ON (og.CodOrganismo = vm.CodOrganismo)
				INNER JOIN mastpersonas p1 ON (p1.CodPersona = vm.PreparadoPor)
				INNER JOIN mastpersonas p2 ON (p2.CodPersona = vm.AprobadoPor)
				INNER JOIN ac_librocontable lc ON (lc.CodLibroCont = vm.CodLibroCont)
			WHERE
				o.CodOrganismo = '$CodOrganismo' AND
				o.CodProveedor = '$CodProveedor' AND
				o.CodTipoDocumento = '$CodTipoDocumento' AND
				o.NroDocumento = '$NroDocumento' AND
				vm.CodContabilidad = 'F'";
}
$field_mast = getRecord($sql);
//---------------------------------------------------

class PDF extends FPDF {
	//	Cabecera de página.
	function Header() {
		global $_PARAMETRO;
		global $Ahora;
		global $field_mast;
		global $_POST;
		extract($_POST);
		global $_GET;
		extract($_GET);
		##
		$Logo = getValorCampo("mastorganismos", "CodOrganismo", "Logo", $fCodOrganismo);
		$NomOrganismo = getValorCampo("mastorganismos", "CodOrganismo", "Organismo", $fCodOrganismo);
		$NomDependencia = getValorCampo("mastdependencias", "CodDependencia", "Dependencia", $_PARAMETRO["DEPAT"]);
		##
		$this->SetFillColor(255, 255, 255);
		$this->SetDrawColor(0, 0, 0);
		$this->Image($_PARAMETRO["PATHLOGO"].$Logo, 5, 5, 10, 10);		
		$this->SetFont('Arial', '', 8);
		$this->SetXY(15, 5); $this->Cell(100, 5, $NomOrganismo, 0, 1, 'L');
		$this->SetXY(15, 10); $this->Cell(100, 5, $NomDependencia, 0, 0, 'L');	
		$this->SetFont('Arial', '', 8);
		$this->SetXY(230, 5); $this->Cell(20, 5, utf8_decode('Fecha: '), 0, 0, 'L');
		$this->Cell(30, 5, formatFechaDMA(substr($Ahora, 0, 10)), 0, 1, 'L');
		$this->SetXY(230, 10); $this->Cell(20, 5, utf8_decode('Página: '), 0, 0, 'L'); 
		$this->Cell(30, 5, $this->PageNo().' de {nb}', 0, 1, 'L');
		$this->SetFont('Arial', 'B', 12);
		$this->SetY(20); $this->Cell(270, 5, utf8_decode('VOUCHER DE PROVISIÓN'), 0, 1, 'C');
		$this->Ln(8);
		##	-------------------
		$this->SetFillColor(200,200,200);
		##
		$this->SetFont('Arial','',10);
		$this->Cell(23, 6, utf8_decode('Organismo'), 0, 0, 'L', 1);
		$this->SetFont('Arial','B',10);
		$this->Cell(127, 6, utf8_decode($field_mast['Organismo']), 0, 0, 'L');
		$this->SetFont('Arial','',10);
		$this->Cell(27, 6, utf8_decode('Preparado Por'), 0, 0, 'L', 1);
		$this->SetFont('Arial','B',10);
		$this->Cell(123, 6, utf8_decode($field_mast['NomPreparadoPor']), 0, 1, 'L');
		$this->Ln(1);
		$this->SetFont('Arial','',10);
		$this->Cell(23, 6, utf8_decode('Fecha'), 0, 0, 'L', 1);
		$this->SetFont('Arial','B',10);
		$this->Cell(127, 6, formatFechaDMA($field_mast['FechaVoucher']), 0, 0, 'L');
		$this->SetFont('Arial','',10);
		$this->Cell(27, 6, utf8_decode('Aprobado Por'), 0, 0, 'L', 1);
		$this->SetFont('Arial','B',10);
		$this->Cell(123, 6, utf8_decode($field_mast['NomAprobadoPor']), 0, 1, 'L');
		$this->Ln(1);
		$this->SetFont('Arial','',10);
		$this->Cell(23, 6, utf8_decode('Voucher'), 0, 0, 'L', 1);
		$this->SetFont('Arial','B',10);
		$this->Cell(127, 6, $field_mast['Periodo'].' '.$field_mast['Voucher'], 0, 0, 'L');
		$this->SetFont('Arial','',10);
		$this->Cell(27, 6, utf8_decode('Libro Contable'), 0, 0, 'L', 1);
		$this->SetFont('Arial','B',10);
		$this->Cell(123, 6, utf8_decode($field_mast['NomLibroCont']), 0, 1, 'L');
		$this->Ln(1);
		$this->SetFont('Arial','',10);
		$this->Cell(23, 6, utf8_decode('Descripción'), 0, 0, 'L', 1);
		$this->SetFont('Arial','B',10);
		$this->MultiCell(247, 6, utf8_decode($field_mast['ComentariosVoucher']), 0, 'L');
		$this->Ln(5);
		##	-------------------
		$this->SetDrawColor(0,0,0);
		$this->SetFillColor(255,255,255);
		$this->SetWidths(array(30,18,18,140,32,32));
		$this->SetAligns(array('L','C','C','L','R','R'));
		$this->SetFont('Arial','B',10);
		$this->Row(array('Cuenta',
						 'Persona',
						 'C.C',
						 utf8_decode('Descripción'),
						 'Debe',
						 'Haber'));
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
$Debe = 0;
$Haber = 0;
$TotalDebe = 0;
$TotalHaber = 0;
$sql = "SELECT *
		FROM ac_voucherdet
		WHERE
			CodOrganismo = '".$field_mast['CodOrganismo']."' AND
			Periodo = '".$field_mast['Periodo']."' AND
			Voucher = '".$field_mast['Voucher']."' AND
			CodContabilidad = '".$field_mast['CodContabilidad']."'
		ORDER BY Linea";
$field_det = getRecords($sql);
foreach ($field_det as $fd) {
	if ($fd['MontoVoucher'] < 0) {
		$Debe = 0;
		$Haber = $fd['MontoVoucher'];
	} else {
		$Debe = $fd['MontoVoucher'];
		$Haber = 0;
	}
	$TotalDebe += $Debe;
	$TotalHaber += $Haber;
	##	
	$pdf->SetDrawColor(255,255,255);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',10);
	$pdf->Row(array(utf8_decode($fd['CodCuenta']),
					$fd['CodPersona'],
					$fd['CodCentroCosto'],
					utf8_decode($fd['Descripcion']),
					number_format($Debe,2,',','.'),
					number_format($Haber,2,',','.')
	));
	$pdf->Ln(2);
}
$pdf->SetDrawColor(0,0,0);
$pdf->Line(5, $pdf->GetY(), 275, $pdf->GetY());
$pdf->Ln(2);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(206, 6);
$pdf->Cell(32, 6, number_format($TotalDebe,2,',','.'), 0, 0, 'R', 1);
$pdf->Cell(32, 6, number_format($TotalHaber,2,',','.'), 0, 0, 'R', 1);
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>