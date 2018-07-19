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
if ($fCodOrganismo != "") { $filtro.=" AND (r.CodOrganismo = '".$fCodOrganismo."')"; }
if ($fCodImpuesto != "") { $filtro.=" AND (r.CodImpuesto = '".$fCodImpuesto."')"; }
if ($fCodProveedor != "") { $filtro.=" AND (r.CodProveedor = '".$fCodProveedor."')"; }
if ($fFechaComprobanteD != "" || $fFechaComprobanteH != "") { 
	if ($fFechaComprobanteD != "") $filtro.=" AND (r.FechaComprobante >= '".formatFechaAMD($fFechaComprobanteD)."')"; 
	if ($fFechaComprobanteH != "") $filtro.=" AND (r.FechaComprobante <= '".formatFechaAMD($fFechaComprobanteH)."')"; 
}
if ($fFechaFacturaD != "" || $fFechaFacturaH != "") { 
	if ($fFechaFacturaD != "") $filtro.=" AND (r.FechaFactura >= '".formatFechaAMD($fFechaFacturaD)."')"; 
	if ($fFechaFacturaH != "") $filtro.=" AND (r.FechaFactura <= '".formatFechaAMD($fFechaFacturaH)."')"; 
}
if ($fPeriodoFiscalD != "" || $fPeriodoFiscalH != "") { 
	if ($fPeriodoFiscalD != "") $filtro.=" AND (r.PeriodoFiscal >= '".$fPeriodoFiscalD."')"; 
	if ($fPeriodoFiscalH != "") $filtro.=" AND (r.PeriodoFiscal <= '".$fPeriodoFiscalH."')"; 
}
if ($fBuscar != "") { 
	$filtro.=" AND (r.PeriodoFiscal LIKE '%".$fBuscar."%' 
					OR r.NroComprobante LIKE '%".$fBuscar."%' 
					OR r.NroDocumento LIKE '%".$fBuscar."%' 
					OR r.NroControl LIKE '%".$fBuscar."%' 
					OR r.PagoNroProceso LIKE '%".$fBuscar."%' 
					OR CONCAT(SUBSTRING(r.PeriodoFiscal, 1, 4), SUBSTRING(r.PeriodoFiscal, 6, 2), r.NroComprobante) LIKE '%".$fBuscar."%' 
					OR CONCAT(r.AnioOrden, '-', r.NroOrden) LIKE '%".$fBuscar."%' 
			)"; 
}
if ($fTipoComprobante != "") { $filtro.=" AND (r.TipoComprobante = '".$fTipoComprobante."')"; }
if ($fEstado != "") { $filtro.=" AND (r.Estado = '".$fEstado."')"; }
//---------------------------------------------------

class PDF extends FPDF {
	//	Cabecera de página.
	function Header() {
		global $_PARAMETRO;
		global $Ahora;
		global $field_proveedor;
		global $_POST;
		extract($_POST);
		##
		$Logo = getValorCampo("mastorganismos", "CodOrganismo", "Logo", $_SESSION['ORGANISMO_ACTUAL']);
		$NomOrganismo = getValorCampo("mastorganismos", "CodOrganismo", "Organismo", $_SESSION['ORGANISMO_ACTUAL']);
		$DocFiscal = getValorCampo("mastorganismos", "CodOrganismo", "DocFiscal", $_SESSION['ORGANISMO_ACTUAL']);
		$Direccion = getValorCampo("mastorganismos", "CodOrganismo", "Direccion", $_SESSION['ORGANISMO_ACTUAL']);
		$NomDependencia = getValorCampo("mastdependencias", "CodDependencia", "Dependencia", $_PARAMETRO["DEPLOGCXP"]);
		##
		$this->SetFillColor(255, 255, 255);
		$this->SetDrawColor(0, 0, 0);
		$this->Image($_PARAMETRO["PATHLOGO"].$Logo, 3, 3, 10, 10);
		$this->SetFont('Arial', '', 8);
		$this->SetXY(12, 3); $this->Cell(100, 5, utf8_decode($NomOrganismo), 0, 1, 'L');
		$this->SetXY(12, 8); $this->Cell(100, 5, utf8_decode($NomDependencia), 0, 0, 'L');
		$this->SetFont('Arial', '', 8);
		$this->SetXY(250, 3); $this->Cell(10, 5, utf8_decode('Fecha: '), 0, 0, 'L');
		$this->Cell(30, 5, formatFechaDMA(substr($Ahora, 0, 10)), 0, 1, 'L');
		$this->SetXY(250, 8); $this->Cell(10, 5, utf8_decode('Página: '), 0, 0, 'L');
		$this->Cell(30, 5, $this->PageNo().' de {nb}', 0, 1, 'L');
		$this->SetFont('Arial', 'B', 10);
		$this->Ln(5);
		$this->Cell(272, 5, utf8_decode('COMPROBANTES ENTERADOS'), 0, 1, 'C', 0);
		$this->Ln(5);
		##
		$y = $this->GetY();
		$this->Rect(3, $y, 150, 20, "D");
		$this->Rect(163, $y, 100, 20, "D");
		$this->SetFont('Arial', 'B', 8);
		$this->Cell(150, 5, utf8_decode('Nombre o Razón Social del Agente de Retención'), 0, 0, 'L');
		$this->Cell(10, 5);
		$this->Cell(100, 5, utf8_decode('Registro de Información Fiscal del Agente de Retención'), 0, 1, 'L');
		$this->Cell(150, 5, utf8_decode($NomOrganismo), 0, 0, 'L');
		$this->Cell(10, 5);
		$this->Cell(100, 5, 'R.I.F :     '.$DocFiscal, 0, 1, 'L');
		$this->SetFont('Arial', '', 8);
		$this->MultiCell(150, 5, utf8_decode($Direccion), 0, 'L');
		$this->Ln(10);
		##
		$this->SetDrawColor(255,255,255);
		$this->SetFillColor(255,255,255);
		$this->SetWidths(array(22,17,24,16,32,32,17,20,20,20,20,12,20));
		$this->SetAligns(array('C','C','C','C','C','C','C','R','R','R','R','R','R'));
		$this->SetFont('Arial', 'B', 7);
		$this->Row(array('Comprobante',
						 'Fecha',
						 'O/P',
						 'Nro. Pago',
						 'Nro. Control',
						 'Nro. Factura',
						 'Fecha Enterado',
						 'Monto Imponible',
						 'Monto Exento',
						 'Monto Impuesto',
						 'Monto Factura',
						 '%',
						 'Monto Retenido'
		));
		$this->SetDrawColor(0,0,0);
		$this->Line(3, $this->GetY(), 275, $this->GetY());
		$this->Ln(2);
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
$TotalMontoRetenido = 0;
$ProveedorMontoRetenido = 0;
$sql = "SELECT
			r.Anio,
			r.TipoComprobante,
			r.NroComprobante,
			CONCAT(SUBSTRING(r.PeriodoFiscal, 1, 4), SUBSTRING(r.PeriodoFiscal, 6, 2), r.NroComprobante) AS Comprobante,
			p.NomCompleto AS Proveedor,
			p.DocFiscal,
			r.CodProveedor,
			r.PeriodoFiscal,
			r.FechaComprobante,
			r.FechaEnterado,
			r.AnioOrden,
			r.NroOrden,
			CONCAT(r.AnioOrden, '-', r.NroOrden) AS OrdenPago,
			r.PagoNroProceso,
			r.PagoSecuencia,
			r.NroDocumento,
			r.NroControl,
			r.FechaFactura,
			r.MontoAfecto,
			r.MontoNoAfecto,
			r.MontoImpuesto,
			r.MontoFactura,
			r.Porcentaje,
			ABS(r.MontoRetenido) AS MontoRetenido,
			r.Estado
		FROM
			ap_retenciones r
			INNER JOIN mastpersonas p ON (p.CodPersona = r.CodProveedor)
		WHERE 1 $filtro
		ORDER BY Comprobante";
$field = getRecords($sql);
foreach($field as $f) {	++$i;
	if ($Grupo != $f['CodProveedor']) {
		$Grupo = $f['CodProveedor'];
		##	
		if ($i > 1) {
			$pdf->SetFont('Arial', 'B', 7);
			$pdf->Cell(252, 5, 'Total Proveedor: ', 0, 0, 'R');
			$pdf->Cell(20, 5, number_format($ProveedorMontoRetenido,2,',','.'), 0, 1, 'R');
		}
		##	
		$pdf->SetFont('Arial', 'B', 7);
		$pdf->Cell(20, 5, utf8_decode($f['DocFiscal'].' '.$f['Proveedor']), 0, 1, 'L');
		##	
		$ProveedorMontoRetenido = 0;
	}
	##	
	$pdf->SetFont('Arial', '', 7);
	$pdf->SetDrawColor(255, 255, 255);
	$pdf->SetFillColor(255, 255, 255);
	$pdf->Row(array($f['Comprobante'],
					formatFechaDMA($f['FechaComprobante']),
					$f['OrdenPago'],
					$f['PagoNroProceso'],
					$f['NroDocumento'],
					$f['NroControl'],
					formatFechaDMA($f['FechaEnterado']),
					number_format($f['MontoAfecto'],2,',','.'),
					number_format($f['MontoNoAfecto'],2,',','.'),
					number_format($f['MontoImpuesto'],2,',','.'),
					number_format($f['MontoFactura'],2,',','.'),
					number_format($f['Porcentaje'],2,',','.'),
					number_format($f['MontoRetenido'],2,',','.')
	));
	##	
	$TotalMontoRetenido += $f['MontoRetenido'];
	$ProveedorMontoRetenido += $f['MontoRetenido'];
}
$pdf->SetFont('Arial', 'B', 7);
$pdf->Cell(252, 5, 'Total Proveedor: ', 0, 0, 'R');
$pdf->Cell(20, 5, number_format($ProveedorMontoRetenido,2,',','.'), 0, 1, 'R');
##	
$pdf->SetDrawColor(0,0,0);
$pdf->Line(3, $pdf->GetY(), 275, $pdf->GetY());
$pdf->Ln(1);
$pdf->SetDrawColor(255,255,255);
$pdf->SetFillColor(215,215,215);
$pdf->SetFont('Arial', 'B', 7);
$pdf->Cell(252, 5, 'Total Retenido: ', 0, 0, 'R');
$pdf->Cell(20, 5, number_format($TotalMontoRetenido,2,',','.'), 0, 1, 'R', 1);
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
