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
if ($fCodOrganismo != "") $filtro.=" AND (o.CodOrganismo = '".$fCodOrganismo."')";
if ($fCodProveedor != "") $filtro.=" AND (o.CodProveedor = '".$fCodProveedor."')";
if ($fPeriodo != "") $filtro.=" AND (o.Periodo >= '".$fPeriodo."')";
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
		$this->SetXY(240, 3); $this->Cell(10, 5, utf8_decode('Fecha: '), 0, 0, 'L');
		$this->Cell(30, 5, formatFechaDMA(substr($Ahora, 0, 10)), 0, 1, 'L');
		$this->SetXY(240, 8); $this->Cell(10, 5, utf8_decode('Página: '), 0, 0, 'L');
		$this->Cell(30, 5, $this->PageNo().' de {nb}', 0, 1, 'L');
		$this->SetFont('Arial', 'B', 10);
		$this->Ln(5);
		$this->Cell(272, 5, utf8_decode('OBLIGACIONES VS. PAGOS'), 0, 1, 'C', 0);
		$this->Cell(272, 5, '(PERIODO: '.$fPeriodo.')', 0, 1, 'C', 0);
		$this->Ln(5);
		##
		$this->SetDrawColor(0, 0, 0);
		$this->SetFillColor(200, 200, 200);
		$this->SetWidths(array(15,55,30,18,18,18,24,15,20,15,24,15,5));
		$this->SetAligns(array('C','L','C','R','R','R','C','C','C','C','C','C','C'));
		$this->SetFont('Arial', 'B', 6);
		$this->Row(array(utf8_decode('Fecha Obligación'),
						 'Proveedor',
						 'Nro. Documento',
						 'Monto Obligaciones',
						 '(-)Adelantos (-)Pago Parcial',
						 'Neto por Pagar',
						 utf8_decode('Voucher Provisión'),
						 'Fecha de Pago',
						 'Cuenta Bancaria',
						 'Nro. Pago',
						 'Voucher',
						 'Nro. Proceso',
						 '#')); 
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
$pdf->SetMargins(3, 3, 3);
$pdf->SetAutoPageBreak(1, 3);
$pdf->AddPage();
//---------------------------------------------------
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetDrawColor(255, 255, 255);
//	consulto
$i = 0;
$TotalObligacion = 0;
$TotalPagosSinVoucher = 0;
$TotalObligacionesSinPago = 0;
$sql = "SELECT
			o.CodProveedor,
			o.CodTipoDocumento,
			o.NroDocumento,
			o.FechaDocumento,
			o.MontoObligacion,
			o.MontoAdelanto,
			o.MontoPagoParcial,
			o.VoucherPeriodo,
			o.Voucher,
			o.VoucherPeriodoPub20,
			o.VoucherPub20,
			o.FechaRegistro,
			p.FechaPago,
			p.NroProceso,
			p.NroPago,
			p.VoucherPeriodo AS VoucherPeriodoPago,
			p.VoucherPago,
			p.PeriodoPagoPub20,
			p.VoucherPagoPub20,
			p.MontoPago,
			p.NroCuenta,
			p.Secuencia,
			mp.NomCompleto AS NomProveedor
		FROM
			ap_obligaciones o
			INNER JOIN mastpersonas mp ON (o.CodProveedor = mp.CodPersona)
			LEFT JOIN ap_ordenpago op ON (op.CodProveedor = o.CodProveedor AND
										  op.CodTipoDocumento = o.CodTipoDocumento AND
										  op.NroDocumento = o.NroDocumento AND
										  op.Estado = 'PA')
			LEFT JOIN ap_pagos p ON (p.CodOrganismo = op.CodOrganismo AND
									 p.Anio = op.Anio AND
									 p.NroOrden = op.NroOrden AND
									 p.Estado = 'IM')
		WHERE 1 $filtro
		ORDER BY NroCuenta, CodProveedor";
$field = getRecords($sql);
foreach($field as $f) {	++$i;
	##	voucher provision
	$ProvisionVoucherPeriodoPub20 = str_replace("-", "", $f['VoucherPeriodoPub20']);
	$ProvisionVoucherPub20 = str_replace("-", "", $f['VoucherPub20']);
	$ProvisionVoucherPeriodo = str_replace("-", "", $f['VoucherPeriodo']);
	$ProvisionVoucher = str_replace("-", "", $f['Voucher']);
	$VoucherProvision = $ProvisionVoucherPeriodoPub20."-".$ProvisionVoucherPub20.$nl.$ProvisionVoucherPeriodo."-".$ProvisionVoucher;
	##	voucher pago
	$PagoVoucherPeriodoPub20 = str_replace("-", "", $f['PeriodoPagoPub20']);
	$PagoVoucherPub20 = str_replace("-", "", $f['VoucherPagoPub20']);
	$PagoVoucherPeriodo = str_replace("-", "", $f['VoucherPeriodoPago']);
	$PagoVoucher = str_replace("-", "", $f['VoucherPago']);
	$VoucherPago = $PagoVoucherPeriodoPub20."-".$PagoVoucherPub20.$nl.$PagoVoucherPeriodo."-".$PagoVoucher;
	##	
	$MontoAdelanto = $f['MontoAdelanto'] + $f['MontoPagoParcial'];
	$MontoNeto = $f['MontoObligacion'] - $MontoAdelanto;
	##	imprimo
	if ($i % 2 == 0) { $pdf->SetFillColor(240, 240, 240); $pdf->SetDrawColor(240, 240, 240);  }
	else { $pdf->SetFillColor(255, 255, 255); $pdf->SetDrawColor(255, 255, 255); }
	$pdf->SetFont('Arial', '', 6);
	$pdf->Row(array(formatFechaDMA($f['FechaRegistro']),
					$f['NomProveedor'],
					$f['CodTipoDocumento'].'-'.$f['NroDocumento'],
					number_format($f['MontoObligacion'], 2, ',', '.'),
					number_format($MontoAdelanto, 2, ',', '.'),
					number_format($MontoNeto, 2, ',', '.'),
					$VoucherProvision,
					formatFechaDMA($f['FechaPago']),
					$f['NroCuenta'],
					$f['NroPago'],
					$VoucherPago,
					$f['NroProceso'],
					$f['Secuencia']));
	##	
	++$TotalObligacion;
	if ($f['VoucherPago'] == "") $TotalPagosSinVoucher++;
	if ($f['NroPago'] == "") $TotalObligacionesSinPago++;
}
$pdf->Ln(5);
##	totales
$pdf->SetFont('Arial', 'B', 6);
$pdf->Cell(132, 5, 'Total Obligaciones: '.$TotalObligaciones, 0, 0, 'L');
$pdf->Cell(75, 5, 'Total Pagos No Contabilizados: '.$TotalPagosSinVoucher, 0, 0, 'R');
$pdf->Ln(4);
$pdf->Cell(60, 5, 'Total Obligaciones No Pagadas: '.$TotalObligacionesSinPago, 0, 0, 'L');
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>