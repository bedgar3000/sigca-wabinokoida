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
if ($fCodTipoDocumento != "") $filtro.=" AND (o.CodTipoDocumento = '".$fCodTipoDocumento."')";
if ($fCodProveedor != "") $filtro.=" AND (o.CodProveedor = '".$fCodProveedor."')";
if ($fFechaRegistroD != "") $filtro .= " AND (o.FechaRegistro >= '".formatFechaAMD($fFechaRegistroD)."')";
if ($fFechaRegistroH != "") $filtro .= " AND (o.FechaRegistro <= '".formatFechaAMD($fFechaRegistroH)."')";
if ($fFechaOrdenPagoD != "") $filtro .= " AND (op.FechaOrdenPago >= '".formatFechaAMD($fFechaOrdenPagoD)."')";
if ($fFechaOrdenPagoH != "") $filtro .= " AND (op.FechaOrdenPago <= '".formatFechaAMD($fFechaOrdenPagoH)."')";
if ($fFechaPagoD != "") $filtro .= " AND (p.FechaPago >= '".formatFechaAMD($fFechaPagoD)."')";
if ($fFechaPagoH != "") $filtro .= " AND (p.FechaPago <= '".formatFechaAMD($fFechaPagoH)."')";
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
		$this->SetXY(255, 3); $this->Cell(10, 5, utf8_decode('Fecha: '), 0, 0, 'L');
		$this->Cell(30, 5, formatFechaDMA(substr($Ahora, 0, 10)), 0, 1, 'L');
		$this->SetXY(255, 8); $this->Cell(10, 5, utf8_decode('Página: '), 0, 0, 'L');
		$this->Cell(30, 5, $this->PageNo().' de {nb}', 0, 1, 'L');
		$this->SetFont('Arial', 'B', 10);
		$this->Ln(5);
		$this->Cell(290, 5, utf8_decode('OBLIGACIONES PAGADAS'), 0, 1, 'C', 0);
		$this->Ln(5);
		##
		$this->SetDrawColor(0, 0, 0);
		$this->SetFillColor(200, 200, 200);
		$this->SetWidths(array(54,18,27,15,21,21,21,15,21,15,15,22,25));
		$this->SetAligns(array('L','C','C','C','C','R','C','C','C','C','C','C','C'));
		$this->SetFont('Arial', 'B', 6);
		$this->Row(array('Proveedor',
						 'Doc. Fiscal',
						 'Nro. Documento',
						 'Fecha',
						 utf8_decode('Voucher Provisión'),
						 utf8_decode('Monto Obligación'),
						 'O/P',
						 'Fecha O/P',
						 'Voucher O/P',
						 'Nro. Pago',
						 'Fecha Pago',
						 'Voucher Pago',
						 'Tipo Pago'));
		$this->Ln(1);
	}
	
	//	Pie de página.
	function Footer() {}
}
//---------------------------------------------------

//---------------------------------------------------
//	Creación del objeto de la clase heredada.
$pdf = new PDF('L', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->SetMargins(3, 3, 3);
$pdf->SetAutoPageBreak(1, 3);
$pdf->AddPage();
//---------------------------------------------------
$pdf->SetTextColor(0, 0, 0);
//	consulto
$i = 0;
$SubNroRegistros = 0;
$sql = "SELECT
			o.CodTipoDocumento,
			o.NroDocumento,
			o.FechaRegistro,
			o.Voucher,
			o.VoucherPeriodo,
			o.VoucherPub20,
			o.VoucherPeriodoPub20,
			o.MontoObligacion,
			op.NomProveedorPagar,
			op.Anio,
			op.NroOrden,
			op.FechaOrdenPago,
			op.VoucherPeriodo AS VoucherPeriodoOP,
			op.Voucher AS VoucherOP,
			op.MontoTotal AS MontoOrden,
			p.NroPago,
			p.FechaPago,
			p.VoucherPago,
			p.VoucherPeriodo AS VoucherPeriodoPago,
			p.VoucherPagoPub20,
			p.PeriodoPagoPub20,
			tp.TipoPago,
			pr.DocFiscal
		FROM
			ap_obligaciones o
			LEFT JOIN ap_ordenpago op ON (op.CodProveedor = o.CodProveedor AND
										  op.CodTipoDocumento = o.CodTipoDocumento AND
										  op.NroDocumento = o.NroDocumento)
			LEFT JOIN ap_pagos p ON (p.CodOrganismo = op.CodOrganismo AND
									 p.Anio = op.Anio AND
									 p.NroOrden = op.NroOrden)
			INNER JOIN masttipopago tp ON (tp.CodTipopago = p.CodTipoPago)
			INNER JOIN mastpersonas pr ON (pr.CodPersona = p.CodProveedor)
		WHERE o.Estado = 'PA' $filtro
		ORDER BY FechaRegistro";
$field = getRecords($sql);
foreach($field as $f) {	++$i;
	list($VoucherAnioProvisionPub20, $VoucherMesProvisionPub20) = explode("[-]", $f['VoucherPeriodoPub20']);
	list($CodVoucherProvisionPub20, $NroVocuherProvisionPub20) = explode("[-]", $f['VoucherPub20']);
	$VoucherProvision = "$VoucherAnioProvisionPub20$VoucherMesProvisionPub20-$CodVoucherProvisionPub20$NroVoucherProvisionPub20";
	list($VoucherAnioProvision, $VoucherMesProvision) = explode("[-]", $f['VoucherPeriodo']);
	list($CodVoucherProvision, $NroVocuherProvision) = explode("[-]", $f['Voucher']);
	$VoucherProvision .= $nl."$VoucherAnioProvision$VoucherMesProvision-$CodVoucherProvision$NroVoucherProvision";
	##
	list($VoucherAnioOP, $VoucherMesOP) = explode("[-]", $f['VoucherPeriodo']);
	list($CodVoucherOP, $NroVocuherOP) = explode("[-]", $f['Voucher']);
	$VoucherOP = "$VoucherAnioOP$VoucherMesOP-$CodVoucherOP$NroVoucherOP";
	##
	list($VoucherAnioPagoPub20, $VoucherMesPagoPub20) = explode("[-]", $f['PeriodoPagoPub20']);
	list($CodVoucherPagoPub20, $NroVocuherPagoPub20) = explode("[-]", $f['VoucherPagoPub20']);
	$VoucherPago = "$VoucherAnioPagPub20o$VoucherMesPagoPub20-$CodVoucherPagoPub20$NroVoucherPagoPub20";
	list($VoucherAnioPago, $VoucherMesPago) = explode("[-]", $f['VoucherPeriodoPago']);
	list($CodVoucherPago, $NroVocuherPago) = explode("[-]", $f['VoucherPago']);
	$VoucherPago .= $nl."$VoucherAnioPago$VoucherMesPago-$CodVoucherPago$NroVoucherPago";
	##
	if ($i % 2 == 0) { $pdf->SetFillColor(240, 240, 240); $pdf->SetDrawColor(240, 240, 240);  }
	else { $pdf->SetFillColor(255, 255, 255); $pdf->SetDrawColor(255, 255, 255); }
	$pdf->SetFont('Arial', '', 6);
	$pdf->Row(array(utf8_decode($f['NomProveedorPagar']),
					$f['DocFiscal'],
					$f['CodTipoDocumento'].'-'.$f['NroDocumento'],
					formatFechaDMA($f['FechaRegistro']),
					$VoucherProvision.$nl.$VoucherProvisionPub20,
					number_format($f['MontoObligacion'], 2, ',', '.'),
					$f['Anio'].'-'.$f['NroOrden'],
					formatFechaDMA($f['FechaOrdenPago']),
					$VoucherOP,
					$f['NroPago'],
					formatFechaDMA($f['FechaPago']),
					$VoucherPago,
					utf8_decode($f['TipoPago'])));
}
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
