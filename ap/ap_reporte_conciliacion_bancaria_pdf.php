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
if ($fCodOrganismo != "") $filtro.=" AND (p.CodOrganismo = '".$fCodOrganismo."')";
if ($fPeriodo != "") $filtro.=" AND (p.Periodo = '".$fEstado."')";
if ($fCodBanco != "") { 
	$filtro.=" AND (p.CodBanco = '".$fCodBanco."')"; 
	if ($fNroCuenta != "") $filtro.=" AND (p.NroCuenta = '".$fNroCuenta."')";
}
//---------------------------------------------------

class PDF extends FPDF {
	//	Cabecera de página.
	function Header() {
		global $_PARAMETRO;
		global $Ahora;
		global $field;
		global $fPeriodo;
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
		$this->Cell(210, 5, utf8_decode('CONCILIACION BANCARIA'), 0, 1, 'C', 0);
		$this->SetFont('Arial', 'BI', 10);
		$this->Cell(210, 5, '(PERIODO '.$fPeriodo.')', 0, 1, 'C', 0);
		$this->Ln(6);
	}
	//	Pie de página.
	function Footer() {}
}
//---------------------------------------------------

//---------------------------------------------------
//	Creación del objeto de la clase heredada.
$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->SetMargins(3, 3, 3);
$pdf->SetAutoPageBreak(1, 6);
$pdf->AddPage();
//---------------------------------------------------
##	banco
$sql = "SELECT
			cb.CtaBanco,
			b.Banco
		FROM
			ap_ctabancaria cb
			INNER JOIN mastbancos b ON (b.CodBanco = cb.CodBanco)
		WHERE NroCuenta = '".$fNroCuenta."'";
$field_banco = getRecord($sql);
##	saldo anterior
$sql = "SELECT SUM(Monto)
		FROM ap_bancotransaccion
		WHERE
			NroCuenta = '".$fNroCuenta."' AND
			PeriodoContable < '".$fPeriodo."' AND
			FlagConciliacion = 'S' AND
			(Estado = 'AP' OR Estado = 'CO')";
$SaldoAnterior = floatval(getVar3($sql));
//	MAS
##	transferencias
$sql = "SELECT SUM(Monto)
		FROM ap_bancotransaccion
		WHERE
			NroCuenta = '".$fNroCuenta."' AND
			PeriodoContable = '".$fPeriodo."' AND
			TipoTransaccion = 'T' AND
			FlagConciliacion = 'S' AND
			Monto > 0 AND
			(Estado = 'AP' OR Estado = 'CO') AND
			(CodTipoTransaccion = 'TRC' OR CodTipoTransaccion = 'TBP')";
$Transferencias = floatval(getVar3($sql));
##	depositos
$sql = "SELECT SUM(bt.Monto)
		FROM
			ap_bancotransaccion bt
			INNER JOIN ap_bancotipotransaccion btt ON (btt.CodTipoTransaccion = bt.CodTipoTransaccion AND btt.FlagDeposito = 'S')
		WHERE
			bt.NroCuenta = '".$fNroCuenta."' AND
			bt.PeriodoContable = '".$fPeriodo."' AND
			bt.TipoTransaccion = 'I' AND
			bt.FlagConciliacion = 'S' AND
			(bt.Estado = 'AP' OR bt.Estado = 'CO')";
$Depositos = floatval(getVar3($sql));
##	notas de creditos
$sql = "SELECT SUM(bt.Monto)
		FROM
			ap_bancotransaccion bt
			INNER JOIN ap_bancotipotransaccion btt ON (btt.CodTipoTransaccion = bt.CodTipoTransaccion AND btt.FlagNotaCredito = 'S')
		WHERE
			bt.NroCuenta = '".$fNroCuenta."' AND
			bt.PeriodoContable = '".$fPeriodo."' AND
			bt.TipoTransaccion = 'I' AND
			bt.FlagConciliacion = 'S' AND
			(bt.Estado = 'AP' OR bt.Estado = 'CO')";
$NotasCreditos = floatval(getVar3($sql));
##	otros ingresos (reintegros)
$sql = "SELECT SUM(bt.Monto)
		FROM
			ap_bancotransaccion bt
			INNER JOIN ap_bancotipotransaccion btt ON (btt.CodTipoTransaccion = bt.CodTipoTransaccion AND btt.FlagOtroIngreso = 'S')
		WHERE
			bt.NroCuenta = '".$fNroCuenta."' AND
			bt.PeriodoContable = '".$fPeriodo."' AND
			bt.TipoTransaccion = 'I' AND
			bt.CodTipoTransaccion <> 'RPA' AND
			bt.FlagConciliacion = 'S' AND
			(bt.Estado = 'AP' OR bt.Estado = 'CO')";
$OtrosIngresos1 = floatval(getVar3($sql));
##	otros ingresos (cheques anulados de periodos anteriores)
$sql = "SELECT ABS(SUM(bt.Monto))
		FROM
			ap_bancotransaccion bt
			INNER JOIN ap_pagos p ON (p.NroProceso = bt.PagoNroProceso AND
									  p.Secuencia = bt.PagoSecuencia AND
									  p.CodTipoPago = '02')
		WHERE
			bt.NroCuenta = '".$fNroCuenta."' AND
			bt.PeriodoContable = '".$fPeriodo."' AND
			bt.TipoTransaccion = 'I' AND
			bt.CodTipoTransaccion <> 'CB-' AND
			bt.FlagConciliacion = 'S' AND
			(bt.Estado = 'AP' OR bt.Estado = 'CO') AND
			p.Estado = 'AN' AND
			SUBSTRING(p.FechaPago, 1, 7) <> SUBSTRING(p.FechaAnulacion, 1, 7)";
$OtrosIngresos2 = floatval(getVar3($sql));
$OtrosIngresos = $OtrosIngresos1 + $OtrosIngresos2;
##	total ingresos
$TotalIngresos = $SaldoAnterior + $Transferencias + $Depositos + $NotasCreditos + $OtrosIngresos;
//	MENOS
##	cheques emitidos
$sql = "SELECT ABS(SUM(bt.Monto))
		FROM
			ap_bancotransaccion bt
			INNER JOIN ap_pagos p ON (p.NroProceso = bt.PagoNroProceso AND
									  p.Secuencia = bt.PagoSecuencia AND
									  p.CodTipoPago = '02')
		WHERE
			bt.NroCuenta = '".$fNroCuenta."' AND
			bt.PeriodoContable = '".$fPeriodo."' AND
			bt.TipoTransaccion = 'E' AND
			bt.CodTipoTransaccion <> 'CB-' AND
			bt.FlagConciliacion = 'S' AND
			(bt.Estado = 'AP' OR bt.Estado = 'CO') AND
			(p.Estado = 'IM' OR (p.Estado = 'AN' AND p.PeriodoAnulacion > p.Periodo))";
$ChequesEmitidos = floatval(getVar3($sql));
##	obligaciones (comisiones bancarias)
$sql = "SELECT SUM(MontoObligacion)
		FROM
			ap_obligaciones o
			INNER JOIN ap_ordenpago op ON (op.CodProveedor = o.CodProveedor AND
										   op.CodTipoDocumento = o.CodTipoDocumento AND
										   op.NroDocumento = o.NroDocumento)
			INNER JOIN ap_pagos p ON (p.Anio = op.Anio AND
									  p.CodOrganismo = op.CodOrganismo AND
									  p.NroOrden = op.NroOrden)
		WHERE
			p.Periodo = '".$fPeriodo."' AND
			o.NroCuenta = '".$fNroCuenta."' AND
			o.CodTipoDocumento = 'CB' AND
			o.Estado = 'PA' AND
			op.Estado = 'PA' AND
			p.Estado = 'IM'";
$ObligacionComisionBancaria = floatval(getVar3($sql));
##	notas de debitos (transferencias)
$sql = "SELECT ABS(SUM(Monto))
		FROM ap_bancotransaccion
		WHERE
			NroCuenta = '".$fNroCuenta."' AND
			PeriodoContable = '".$fPeriodo."' AND
			(TipoTransaccion = 'T' OR TipoTransaccion = 'E') AND
			Monto < 0 AND
			CodTipoTransaccion <> 'CB-' AND
			CodTipoTransaccion <> 'CBP' AND
			CodTipoTransaccion <> 'PAG' AND
			FlagConciliacion = 'S' AND
			(Estado = 'AP' OR Estado = 'CO')";
$NotasDebitos1 = floatval(getVar3($sql));
##	notas de debitos (cheques de gerencia)
$sql = "SELECT ABS(SUM(bt.Monto))
		FROM
			ap_bancotransaccion bt
			INNER JOIN ap_pagos p ON (p.NroProceso = bt.PagoNroProceso AND
									  p.Secuencia = bt.PagoSecuencia AND
									  p.CodTipoPago <> '02')
		WHERE
			bt.NroCuenta = '".$fNroCuenta."' AND
			bt.PeriodoContable = '".$fPeriodo."' AND
			bt.TipoTransaccion = 'E' AND
			bt.CodTipoTransaccion <> 'CB-' AND
			bt.FlagConciliacion = 'S' AND
			(bt.Estado = 'AP' OR bt.Estado = 'CO') AND
			p.Estado = 'IM'";
$NotasDebitos2 = floatval(getVar3($sql));
$NotasDebitos = $NotasDebitos1 + $NotasDebitos2 - $ObligacionComisionBancaria;
##	comision bancaria
$sql = "SELECT ABS(SUM(bt.Monto))
		FROM ap_bancotransaccion bt
		WHERE
			bt.NroCuenta = '".$fNroCuenta."' AND
			bt.PeriodoContable = '".$fPeriodo."' AND
			bt.TipoTransaccion = 'E' AND
			(bt.CodTipoTransaccion = 'CB-' OR bt.CodTipoTransaccion = 'CBP') AND
			bt.FlagConciliacion = 'S' AND
			(bt.Estado = 'AP' OR bt.Estado = 'CO')";
$ComisionBancaria = floatval(getVar3($sql));
$ComisionBancaria = $ComisionBancaria + $ObligacionComisionBancaria;
##	total egresos
$TotalEgresos = $ChequesEmitidos + $NotasDebitos + $ComisionBancaria;
##	total libro
$TotalLibro = $TotalIngresos - $TotalEgresos;
##	otros ajustes (ingresos)
$sql = "SELECT SUM(bt.Monto)
		FROM
			ap_bancotransaccion bt
			INNER JOIN ap_bancotipotransaccion btt ON (btt.CodTipoTransaccion = bt.CodTipoTransaccion AND btt.FlagOtroAjuste = 'S' AND btt.TipoTransaccion = 'I')
		WHERE
			bt.NroCuenta = '".$fNroCuenta."' AND
			bt.PeriodoContable = '".$fPeriodo."' AND
			bt.TipoTransaccion = 'I' AND
			bt.FlagConciliacion = 'S' AND
			(bt.Estado = 'AP' OR bt.Estado = 'CO')";
$OtrosAjustesIngresos = floatval(getVar3($sql));
##	otros ajustes (egresos)
$sql = "SELECT SUM(bt.Monto)
		FROM
			ap_bancotransaccion bt
			INNER JOIN ap_bancotipotransaccion btt ON (btt.CodTipoTransaccion = bt.CodTipoTransaccion AND btt.FlagOtroAjuste = 'S' AND btt.TipoTransaccion = 'E')
		WHERE
			bt.NroCuenta = '".$fNroCuenta."' AND
			bt.PeriodoContable = '".$fPeriodo."' AND
			bt.TipoTransaccion = 'E' AND
			bt.FlagConciliacion = 'S' AND
			(bt.Estado = 'AP' OR bt.Estado = 'CO')";
$OtrosAjustesEgresos = floatval(getVar3($sql));
$TotalOtros = $OtrosAjustesIngresos + $OtrosAjustesEgresos;
//	CHEQUES NO COBRADOS
$sql = "SELECT
			p.NroPago,
			p.FechaPago,
			p.NomProveedorPagar,
			p.MontoPago
		FROM
			ap_bancotransaccion bt
			INNER JOIN ap_pagos p ON (p.NroProceso = bt.PagoNroProceso AND
									  p.Secuencia = bt.PagoSecuencia AND
									  p.CodTipoPago = '02')
		WHERE
			bt.NroCuenta = '".$fNroCuenta."' AND
			bt.PeriodoContable <= '".$fPeriodo."' AND
			bt.TipoTransaccion = 'E' AND
			bt.FlagConciliacion = 'S' AND
			(bt.Estado = 'AP' OR bt.Estado = 'CO') AND
			((p.Estado = 'AN' AND p.FechaAnulacion > '".$fPeriodo."-31') OR
			 (p.Estado = 'IM' AND (p.FlagCobrado = 'N' OR p.EstadoEntrega = 'C')))
		ORDER BY FechaPago, NroPago";
$field_nocobrados = getRecords($sql);
//---------------------------------------------------
$pdf->SetDrawColor(0, 0, 0);
$pdf->Rect(3, 34, 0.1, 100, 'D');
//
$pdf->SetTextColor(0, 0, 0);
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetFillColor(255, 255, 255);
//	cuerpo
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(130, 5, '1. BANCO', 1, 0, 'L', 0);
$pdf->Cell(40, 5, '2. CUENTA NRO.', 1, 0, 'L', 0);
$pdf->Cell(40, 5, '3. FECHA', 1, 1, 'L', 0);
$pdf->Cell(130, 5, utf8_decode($field_banco['Banco']), 1, 0, 'L', 0);
$pdf->Cell(40, 5, $field_banco['CtaBanco'], 1, 0, 'C', 0);
$pdf->Cell(40, 5, formatFechaDMA($fPeriodo.'-'.getDiasMes($fPeriodo)), 1, 1, 'C', 0);
##	saldo anterior
$pdf->Cell(130, 5, '4. SALDO ANTERIOR', 1, 0, 'L', 0);
$pdf->Cell(40, 5, number_format($SaldoAnterior, 2, ',', '.'), 1, 1, 'R', 0);
//	MAS
$pdf->Cell(170, 5, utf8_decode('MÁS'), 1, 1, 'L', 0);
##	
$pdf->Cell(130, 5, '5. TRANSFERENCIAS', 0, 0, 'L', 0);
$pdf->Cell(40, 5, number_format($Transferencias, 2, ',', '.'), 1, 1, 'R', 0);
$pdf->Cell(130, 5, utf8_decode('6. DEPÓSITOS'), 0, 0, 'L', 0);
$pdf->Cell(40, 5, number_format($Depositos, 2, ',', '.'), 1, 1, 'R', 0);
$pdf->Cell(130, 5, utf8_decode('7. NOTAS DE CRÉDITOS'), 0, 0, 'L', 0);
$pdf->Cell(40, 5, number_format($NotasCreditos, 2, ',', '.'), 1, 1, 'R', 0);
$pdf->Cell(130, 5, utf8_decode('8. OTROS. (REVERSO POR CHEQUE ANULADO / REINTEGROS)'), 0, 0, 'L', 0);
$pdf->Cell(40, 5, number_format($OtrosIngresos, 2, ',', '.'), 1, 1, 'R', 0);
$pdf->Cell(130, 5);
$pdf->Cell(40, 5, '9. TOTAL INGRESOS', 1, 0, 'R', 0);
$pdf->Cell(40, 5, number_format($TotalIngresos, 2, ',', '.'), 1, 1, 'R', 0);
//	MENOS
$pdf->Cell(170, 5, 'MENOS', 1, 1, 'L', 0);
##	
$pdf->Cell(130, 5, '10. MONTO DE LOS CHEQUES EMITIDOS', 0, 0, 'L', 0);
$pdf->Cell(40, 5, number_format($ChequesEmitidos, 2, ',', '.'), 1, 1, 'R', 0);
$pdf->Cell(130, 5, utf8_decode('11. NOTAS DE DÉBITOS (TRANSFERENCIAS, CHEQUES DE GERENCIA)'), 0, 0, 'L', 0);
$pdf->Cell(40, 5, number_format($NotasDebitos, 2, ',', '.'), 1, 1, 'R', 0);
$pdf->Cell(130, 5, utf8_decode('12. OTROS (COMISIÓN BANCARIA)'), 0, 0, 'L', 0);
$pdf->Cell(40, 5, number_format($ComisionBancaria, 2, ',', '.'), 1, 1, 'R', 0);
$pdf->Cell(130, 5);
$pdf->Cell(40, 5, '13. TOTAL EGRESOS', 1, 0, 'R', 0);
$pdf->Cell(40, 5, number_format($TotalEgresos, 2, ',', '.'), 1, 1, 'R', 0);
##	total
$pdf->Cell(170, 5, utf8_decode('14. DISPONIBLE SEGÚN LIBROS'), 1, 0, 'L', 0);
$pdf->Cell(40, 5, number_format($TotalLibro, 2, ',', '.'), 1, 1, 'R', 0);
//	MAS
$pdf->Cell(170, 5, utf8_decode('MÁS'), 1, 1, 'L', 0);
##	cheques no cobrados
$pdf->Cell(170, 5, '15. CHEQUES NO COBRADOS', 1, 1, 'L', 0);
$pdf->Cell(40, 5, utf8_decode('15.1 N°         Y         FECHA'), 1, 0, 'L', 0);
$pdf->Cell(90, 5, '15.2 BENEFICIARIO', 1, 0, 'L', 0);
$pdf->Cell(40, 5, '15.3 MONTO (Bs.)', 1, 1, 'L', 0);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetWidths(array(40,90,40));
$pdf->SetAligns(array('L','L','R'));
$TotalNoCobrados = 0;
$i = 0;
foreach($field_nocobrados as $f) {
	$pdf->SetFont('Arial', '', 8);
	$pdf->Row(array($f['NroPago'].'          '.formatFechaDMA($f['FechaPago']),
					utf8_decode($f['NomProveedorPagar']),
					number_format($f['MontoPago'], 2, ',', '.')));
	$TotalNoCobrados += $f['MontoPago'];
	++$i;
	$y = $pdf->GetY();
	if ($y == 264) {
		$pdf->SetFont('Arial', 'B', 8);
		$pdf->Row(array('',
						'VAN...',
						number_format($TotalNoCobrados, 2, ',', '.')));
	}
}
for($j=$i;$j<=19;$j++) {
	$pdf->Row(array('','',''));
}
$y = $pdf->GetY();
if ($y > 224) {
	$pdf->SetFont('Arial', 'B', 8);
	$pdf->Row(array('',
					'VAN...',
					number_format($TotalNoCobrados, 2, ',', '.')));
	$pdf->AddPage();
	$pdf->SetFont('Arial', 'B', 8);
	$pdf->Row(array('',
					'VIENEN...',
					number_format($TotalNoCobrados, 2, ',', '.')));
}
$pdf->SetFont('Arial', '', 8);
##	totales
$TotalMasOtros = $TotalNoCobrados + $TotalOtros;
$TotalBanco = $TotalLibro + $TotalMasOtros;
$pdf->Cell(170, 5, utf8_decode('16. TOTAL CHEQUES NO COBRADOS'), 1, 0, 'L', 0);
$pdf->Cell(40, 5, number_format($TotalNoCobrados, 2, ',', '.'), 1, 1, 'R', 0);
$pdf->Cell(170, 5, utf8_decode('17. OTROS'), 1, 0, 'L', 0);
$pdf->Cell(40, 5, number_format($TotalOtros, 2, ',', '.'), 1, 1, 'R', 0);
$pdf->Cell(170, 5, utf8_decode('18. TOTAL'), 1, 0, 'L', 0);
$pdf->Cell(40, 5, number_format($TotalMasOtros, 2, ',', '.'), 1, 1, 'R', 0);
$pdf->Cell(170, 5, utf8_decode('19. SALDO SEGÚN EDO. DE CUENTAS DEL BANCO (14 + 18)'), 1, 0, 'L', 0);
$pdf->Cell(40, 5, number_format($TotalBanco, 2, ',', '.'), 1, 1, 'R', 0);
##
//	obtengo las firmas
list($_ELABORADO['Nombre'], $_ELABORADO['Cargo'], $_ELABORADO['Nivel']) = getFirma($_PARAMETRO['FIRMAOP5']);
list($_REVISADO['Nombre'], $_REVISADO['Cargo'], $_REVISADO['Nivel']) = getFirma($_PARAMETRO['FIRMAOP1']);
list($_CONFORMADO['Nombre'], $_CONFORMADO['Cargo'], $_CONFORMADO['Nivel']) = getFirma($_PARAMETRO['FIRMAOP3']);
$pdf->SetDrawColor(0, 0, 0);
$y = $pdf->GetY();
$pdf->SetY($y);
$pdf->Cell(70, 5, utf8_decode('20. ELABORADO POR'), 1, 0, 'L', 0);
$pdf->Cell(70, 5, utf8_decode('21. REVISADO POR'), 1, 0, 'L', 0);
$pdf->Cell(70, 5, utf8_decode('22. CONFORMADO POR'), 1, 0, 'L', 0);
$pdf->Ln(5);
$y = $pdf->GetY();
$pdf->Rect(3, $y, 70, 25, 'D');
$pdf->Rect(73, $y, 70, 25, 'D');
$pdf->Rect(143, $y, 70, 25, 'D');
$pdf->SetXY(3, $y); $pdf->MultiCell(70, 5, utf8_decode($_ELABORADO['Nivel'].' '.$_ELABORADO['Nombre']), 0, 'L', 0);
$pdf->SetXY(73, $y); $pdf->MultiCell(70, 5, utf8_decode($_REVISADO['Nivel'].' '.$_REVISADO['Nombre']), 0, 'L', 0);
$pdf->SetXY(143, $y); $pdf->MultiCell(70, 5, utf8_decode($_CONFORMADO['Nivel'].' '.$_CONFORMADO['Nombre']), 0, 'L', 0);
$y = $pdf->GetY();
$pdf->SetXY(3, $y); $pdf->MultiCell(70, 5, utf8_decode($_ELABORADO['Cargo']), 0, 'L', 0);
$pdf->SetXY(73, $y); $pdf->MultiCell(70, 5, utf8_decode($_REVISADO['Cargo']), 0, 'L', 0);
$pdf->SetXY(143, $y); $pdf->MultiCell(70, 5, utf8_decode($_CONFORMADO['Cargo']), 0, 'L', 0);
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
