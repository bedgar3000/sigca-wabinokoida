<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
list($NroProceso, $Secuencia, $CodTipoPago) = split("[_]", $registro);
//---------------------------------------------------
//	consulto
$sql = "SELECT
			p.CodOrganismo,
			p.CodProveedor,
			p.NroPago,
			p.CodTipoPago,
			p.MontoPago,
			p.NroOrden,
			p.NomProveedorPagar,
			p.ChequeCargo,
			p.NroCuenta,
			p.PeriodoPagoPub20 AS Periodo,
			p.VoucherPagoPub20 AS VoucherPago,
			p.FechaPago,
			p.GeneradoPor,
			p.ConformadoPor,
			p.AprobadoPor,
			p.RevisadoPor,
			mp.NomCompleto AS NomProveedor,
			cb.CodCuenta AS CodCuentaBanco,
			b.Banco,
			pc2.Descripcion AS NomCuentaBanco,
			b.Banco,
			cb.CtaBanco,
			(SELECT PrefVoucherPA FROM mastaplicaciones WHERE CodAplicacion = 'AP') AS Voucher,
			(SELECT CodSistemaFuente FROM mastaplicaciones WHERE CodAplicacion = 'AP') AS CodSistemaFuente
		FROM
			ap_pagos p
			INNER JOIN ap_ctabancaria cb ON (p.NroCuenta = cb.NroCuenta)
			INNER JOIN mastbancos b ON (cb.CodBanco = b.CodBanco)
			INNER JOIN mastpersonas mp ON (p.CodProveedor = mp.CodPersona)
			LEFT JOIN ac_mastplancuenta20 pc2 ON (cb.CodCuentaPub20 = pc2.CodCuenta)
		WHERE
			p.NroProceso = '".$NroProceso."' AND
			p.Secuencia = '".$Secuencia."'";
$query = mysql_query($sql) or die ($sql.mysql_error());
if (mysql_num_rows($query) != 0) $field = mysql_fetch_array($query);
$field['MontoPago'] = round($field['MontoPago'],2);
//---------------------------------------------------
//	obtengo las firmas
list($_GENERADO['Nombre'], $_GENERADO['Cargo'], $_GENERADO['Nivel']) = getFirma($field['GeneradoPor']);
list($_CONFORMADO['Nombre'], $_CONFORMADO['Cargo'], $_CONFORMADO['Nivel']) = getFirma($field['ConformadoPor']);
list($_REVISADO['Nombre'], $_REVISADO['Cargo'], $_REVISADO['Nivel']) = getFirma($field['RevisadoPor']);
list($_APROBADO['Nombre'], $_APROBADO['Cargo'], $_APROBADO['Nivel']) = getFirma($field['AprobadoPor']);
//---------------------------------------------------

//---------------------------------------------------
function chequeVenezuela($pdf) {
	global $field;
	global $_PARAMETRO;
	list($a, $m, $d) = split("[/.-]", $field['FechaPago']);
	//	----------------
	list($int, $dec) = split("[.]", $field['MontoPago']);
	$int_letras = strtoupper(convertir_a_letras($int, "entero"));
	$monto_letras = "$int_letras CON $dec/100";
	//	----------------	
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetDrawColor(0, 0, 0);
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', 'B', 12);
	//	----------------
	$Ciudad = getVar3("SELECT Ciudad FROM mastciudades WHERE CodCiudad = '".$_PARAMETRO['CIUDADDEFAULT']."'");
	$pdf->SetXY(138, 7); $pdf->Cell(35, 4, number_format($field['MontoPago'], 2, ',', '.').'*****', 0, 0, 'L');
	$pdf->SetXY(33, 24); $pdf->Cell(160, 4, utf8_decode($field['NomProveedorPagar']), 0, 0, 'L');		
	$pdf->SetXY(14, 30); $pdf->MultiCell(200, 4, '                       '.utf8_decode($monto_letras).' **********', 0, 'L');
	$pdf->SetXY(14, 42); $pdf->Cell(160, 4, utf8_decode('        '.$Ciudad.', '.$d.' de '.getNombreMes("$a-$m").'                               '.$a), 0, 0, 'L');
}
//---------------------------------------------------

//---------------------------------------------------
class PDF extends FPDF {
	//	Cabecera de página.
	function Header() {}
	//	Pie de página.
	function Footer() {
		global $_GENERADO;
		global $_CONFORMADO;
		global $_REVISADO;
		global $_APROBADO;
		$this->SetTextColor(0, 0, 0);
		$this->SetDrawColor(0, 0, 0);
		$this->SetFillColor(255, 255, 255);
		$this->SetXY(10, 227);
		$this->Rect(10, 227, 65, 25, "D"); 
		$this->Rect(75, 227, 65, 25, "D");
		$this->Rect(140, 227, 65, 25, "D");
		$this->SetFont('Arial', 'B', 8);
		$this->Cell(65, 5, 'EMITIDO POR', 1, 0, 'L');
		$this->Cell(65, 5, 'REVISADO POR', 1, 0, 'L');
		$this->Cell(65, 5, 'APROBADO POR', 1, 0, 'L');
		##
		$this->SetFont('Arial', 'B', 6);
		$this->SetXY(10, 233); $this->MultiCell(48.75, 3, utf8_decode($_GENERADO['Nivel'].' '.$_GENERADO['Nombre']), 0, 'L');
		$this->SetXY(75, 233); $this->MultiCell(48.75, 3, utf8_decode($_CONFORMADO['Nivel'].' '.$_CONFORMADO['Nombre']), 0, 'L');
		$this->SetXY(140, 233); $this->MultiCell(48.75, 3, utf8_decode($_APROBADO['Nivel'].' '.$_APROBADO['Nombre']), 0, 'L');
		##
		$this->SetXY(10, 240); $this->MultiCell(48.75, 3, utf8_decode($_GENERADO['Cargo']), 0, 'C');
		$this->SetXY(75, 240); $this->MultiCell(48.75, 3, utf8_decode($_CONFORMADO['Cargo']), 0, 'C');
		$this->SetXY(140, 240); $this->MultiCell(48.75, 3, utf8_decode($_APROBADO['Cargo']), 0, 'C');
		##
		$this->SetXY(10, 252);
		$this->Cell(64, 5, 'Entrega Contra Factura Original', 1, 0, 'L', 1);
		$this->Cell(33.5, 5, 'Recibido Por', 1, 0, 'L', 1);
		$this->Cell(33, 5, 'C.I. No.', 1, 0, 'L', 1);
		$this->Cell(32, 5, 'Firma', 1, 0, 'L', 1);
		$this->Cell(32.5, 5, 'Fecha', 1, 0, 'L', 1);
		$this->Ln(5);
		$this->Cell(64, 15, '________ SI ________ NO', 1, 0, 'C', 1);
		$this->Cell(33.5, 15, '', 1, 0, 'L', 1);
		$this->Cell(33, 15, '', 1, 0, 'L', 1);
		$this->Cell(32, 15, '', 1, 0, 'L', 1);
		$this->Cell(32.5, 15, '', 1, 0, 'L', 1);
	}
}
//---------------------------------------------------

//---------------------------------------------------
//	Creación del objeto de la clase heredada.
$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->SetMargins(10, 5, 10);
$pdf->SetAutoPageBreak(5, 1);
$pdf->AddPage();
//---------------------------------------------------
//	imprimo cheque
chequeVenezuela($pdf);

//	imprimo cuerpo
$pdf->SetTextColor(0, 0, 0);
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetXY(10, 100);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(20, 5, utf8_decode('Beneficiario: '), 0, 0, 'L');
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(175, 5, utf8_decode($field['NomProveedorPagar']), 0, 0, 'L');
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(20, 5, utf8_decode('Nro. Cuenta: '), 0, 0, 'L');
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(105, 5, $field['CtaBanco'], 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(20, 5, utf8_decode('Nro. Pago: '), 0, 0, 'L');
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(50, 5, $field['NroPago'], 0, 0, 'L');
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(20, 5, utf8_decode('Voucher: '), 0, 0, 'L');
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(105, 5, $field['VoucherPago'], 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(20, 5, utf8_decode('Fecha: '), 0, 0, 'L');
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(50, 5, formatFechaDMA($field['FechaPago']), 0, 0, 'L');
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(20, 5, utf8_decode('Banco: '), 0, 0, 'L');
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(105, 5, utf8_decode($field['Banco']), 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(20, 5, utf8_decode('Monto: '), 0, 0, 'L');
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(50, 5, number_format($field['MontoPago'], 2, ',', '.'), 0, 0, 'L');
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(20, 5, utf8_decode('Descripción: '), 0, 0, 'L');
$pdf->SetFont('Arial', '', 8);
$sql = "SELECT ComentariosVoucher 
		FROM ac_vouchermast 
		WHERE
			CodOrganismo = '".$field['CodOrganismo']."' AND
			Periodo = '".$field['Periodo']."' AND
			Voucher = '".$field['VoucherPago']."' AND
			CodContabilidad = 'F'";
$Descripcion = getVar3($sql);
$pdf->MultiCell(175, 5, utf8_decode($Descripcion), 0, 'L');
$pdf->Ln(3);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetFillColor(255, 255, 255);

//	imprimo cuerpo
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetWidths(array(30, 15, 15, 85, 25, 25));
$pdf->SetAligns(array('L', 'C', 'C', 'L', 'R', 'R'));
$pdf->Row(array('Cuenta',
				'Persona',
				'C.C',
				utf8_decode('Descripción'),
				'Debe',
				'Haber'));
$pdf->Ln(1);
$pdf->SetDrawColor(255, 255, 255);
$pdf->SetFont('Arial', '', 8);

//	consulto voucher de pago
$sql = "SELECT
			vd.*,
			pc.Descripcion AS NomCuenta
		FROM
			ac_voucherdet vd
			INNER JOIN ac_mastplancuenta20 pc On (vd.CodCuenta = pc.CodCuenta)
		WHERE
			CodOrganismo = '".$field['CodOrganismo']."' AND
			Periodo = '".$field['Periodo']."' AND
			Voucher = '".$field['VoucherPago']."' AND
			CodContabilidad = 'F'
		ORDER BY CodCuenta";
$query_voucher = mysql_query($sql) or die ($sql.mysql_error());
while($field_voucher = mysql_fetch_array($query_voucher)) {
	if ($field_voucher['MontoVoucher'] > 0) { $debe = $field_voucher['MontoVoucher']; $haber = 0.00; $total_debe += $debe; }
	else { $debe = 0.00; $haber = $field_voucher['MontoVoucher']; $total_haber += $haber; }
	$pdf->Row(array($field_voucher['CodCuenta'],
					$field_voucher['CodPersona'],
					$field_voucher['CodCentroCosto'],
					utf8_decode($field_voucher['NomCuenta']),
					number_format($debe, 2, ',', '.'),
					number_format($haber, 2, ',', '.')));
}
//---------------------------------------------------
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetFillColor(0, 0, 0);
$y = $pdf->GetY();
$pdf->Rect(10, $y, 195, 0.1, "FD");
$pdf->SetY($y+2);
$pdf->SetDrawColor(255, 255, 255);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Row(array('',
				'',
				'',
				'',
				number_format($total_debe, 2, ',', '.'),
				number_format($total_haber, 2, ',', '.')));
//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
