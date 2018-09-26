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
		global $f;
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
		$this->Cell(272, 5, utf8_decode('PAGOS VS. OBLIGACIONES'), 0, 1, 'C', 0);
		$this->Cell(272, 5, '(PERIODO: '.$fPeriodo.')', 0, 1, 'C', 0);
		$this->Ln(5);
		##
		$this->SetDrawColor(0, 0, 0);
		$this->SetFillColor(200, 200, 200);
		$this->SetWidths(array(65,15,15,20,20,30,15,20,20,20,20));
		$this->SetAligns(array('L','C','C','C','R','C','C','R','R','R','C'));
		$this->SetFont('Arial', 'B', 6);
		$this->Row(array('Pagar A',
						 'Fecha de Pago',
						 'Nro. Proceso',
						 'Voucher',
						 'Monto Pagado',
						 'Nro. Documento',
						 'Fecha Documento',
						 'Monto Obligaciones',
						 'Monto Adelantos',
						 'Pagado',
						 utf8_decode('Voucher Provisión')));
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
			p.NomProveedorPagar,
			p.FechaPago,
			p.NroProceso,
			p.VoucherPeriodo AS VoucherPeriodoPago,
			p.VoucherPago,
			p.PeriodoPagoPub20,
			p.VoucherPagoPub20,
			p.MontoPago,
			p.NroCuenta,
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
			o.FlagContabilizacionPendiente,
			o.FlagContPendientePub20,
			cb.Descripcion AS NomCuenta
		FROM
			ap_pagos p
			INNER JOIN ap_ordenpago op ON (op.CodOrganismo = p.CodOrganismo AND
										   op.Anio = p.Anio AND
										   op.NroOrden = p.NroOrden AND
										   op.Estado = 'PA')
			INNER JOIN ap_obligaciones o ON (o.CodProveedor = op.CodProveedor AND
											 o.CodTipoDocumento = op.CodTipoDocumento AND
											 o.NroDocumento = op.NroDocumento AND
											 o.Estado = 'PA')
			INNER JOIN ap_ctabancaria cb ON (p.NroCuenta = cb.NroCuenta)
		WHERE p.Estado = 'IM' $filtro
		ORDER BY NroCuenta, CodProveedor";
$f = getRecords($sql);
foreach($f as $f) {	++$i;
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
	##	
	if ($Grupo != $f['NroCuenta']) {
		$Grupo = $f['NroCuenta'];
		##	sub-total
		if ($TotalPagos > 1) {
			$pdf->SetFont('Arial', 'B', 6);
			$pdf->Cell(60, 5, 'Pagos: '.$SubTotalPagos, 0, 0, 'L');
			$pdf->Cell(75, 5, 'Obligaciones No Contabilizadas: '.$SubTotalObligacionesSinVoucher, 0, 0, 'R');
			$pdf->Ln(10);
		}
		##	cuenta bancaria
		$pdf->SetFont('Arial', 'B', 8);
		$pdf->Cell(25, 5, $f['NroCuenta'], 0, 0, 'L');
		$pdf->Cell(210, 5, utf8_decode($f['NomCuenta']), 0, 1, 'L');
		$pdf->Ln(2);
		##
		$SubTotalPagos = 0;
		$SubTotalObligacionesSinVoucher = 0;
	}
	$x = "($f[FlagContabilizacionPendiente] == N || $f[FlagContPendientePub20] == N) && ($_PARAMETRO[CONTONCO] == S || $_PARAMETRO[CONTPUB20] == S)";
	##	imprimo
	$pdf->SetFillColor(255, 255, 255); $pdf->SetDrawColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 6);
	$pdf->Row(array(utf8_decode($f['NomProveedorPagar']),
					formatFechaDMA($f['FechaPago']),
					$f['NroProceso'],
					$VoucherPago,
					number_format($f['MontoPago'], 2, ',', '.'),
					$f['CodTipoDocumento'].'-'.$f['NroDocumento'],
					formatFechaDMA($f['FechaRegistro']),
					number_format($f['MontoObligacion'], 2, ',', '.'),
					number_format($MontoAdelanto, 2, ',', '.'),
					number_format($MontoNeto, 2, ',', '.'),
					$VoucherProvision));
	##	
	++$TotalPagos;
	++$SubTotalPagos;
	if (!($f['FlagContabilizacionPendiente'] == "N" || $f['FlagContPendientePub20'] == "N") || !($_PARAMETRO['CONTONCO'] == "S" || $_PARAMETRO['CONTPUB20'] == "S")) {
		++$TotalObligacionesSinVoucher;
		++$SubTotalObligacionesSinVoucher;
	}
}
##	sub-total
$pdf->SetFont('Arial', 'B', 6);
$pdf->Cell(60, 5, 'Pagos: '.$SubTotalPagos, 0, 0, 'L');
$pdf->Cell(75, 5, 'Obligaciones No Contabilizadas: '.$SubTotalObligacionesSinVoucher, 0, 0, 'R');
$pdf->Ln(5);
##	totales
$pdf->SetFont('Arial', 'B', 6);
$pdf->Cell(60, 5, 'Total Pagos: '.$TotalPagos, 0, 0, 'L');
$pdf->Cell(75, 5, 'Total Obligaciones No Contabilizadas: '.$TotalObligacionesSinVoucher, 0, 0, 'R');
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>