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
if ($fEstado != "") $filtro.=" AND (o.Estado = '".$fEstado."')";
if ($fCodTipoDocumento != "") $filtro.=" AND (o.CodTipoDocumento = '".$fCodTipoDocumento."')";
if ($fCodProveedor != "") $filtro.=" AND (o.CodProveedor = '".$fCodProveedor."')";
if ($fIngresadoPor != "") $filtro.=" AND (o.IngresadoPor = '".$fIngresadoPor."')";
if ($fPeriodo != "") $filtro.=" AND (o.Periodo = '".$fPeriodo."')";
if ($fFechaRegistroD != "") $filtro .= " AND (o.FechaRegistro >= '".formatFechaAMD($fFechaRegistroD)."')";
if ($fFechaRegistroH != "") $filtro .= " AND (o.FechaRegistro <= '".formatFechaAMD($fFechaRegistroH)."')";
if ($fFechaAprobacionD != "") $filtro .= " AND (o.FechaAprobacion >= '".formatFechaAMD($fFechaAprobacionD)."')";
if ($fFechaAprobacionH != "") $filtro .= " AND (o.FechaAprobacion <= '".formatFechaAMD($fFechaAprobacionH)."')";
if ($fFechaPagoD != "") $filtro .= " AND (o.FechaPago >= '".formatFechaAMD($fFechaPagoD)."')";
if ($fFechaPagoH != "") $filtro .= " AND (o.FechaPago <= '".formatFechaAMD($fFechaPagoH)."')";
if ($fCategoriaProg != "") $filtro.=" AND (ppto.CategoriaProg = '".$fCategoriaProg."')";
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
		$this->Cell(272, 5, utf8_decode('Listado de Obligaciones'), 0, 1, 'C', 0);
		$this->Ln(5);
		##
		$this->SetDrawColor(0, 0, 0);
		$this->SetFillColor(200, 200, 200);
		$this->SetWidths(array(30,15,15,15,10,20,20,20,20,20,87));
		$this->SetAligns(array('L','C','C','C','C','R','R','R','R','C','L'));
		$this->SetFont('Arial', 'B', 6);
		$this->Row(array('Nro. Documento',
						 'Nro. Reg.',
						 'Fecha Doc.',
						 'Fecha Pago',
						 'Est.',
						 'Monto Obligacion',
						 '(-Adelantos)',
						 '(-Pago Parcial)',
						 'Total',
						 'Voucher',
						 'Concepto'));
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
//	consulto
$i = 0;
$SubNroRegistros = 0;
$sql = "SELECT
			o.CodProveedor,
			o.CodTipoDocumento,
			o.NroDocumento,
			o.NroRegistro,
			o.FechaRegistro,
			o.FechaPago,
			o.Estado,
			o.MontoObligacion,
			o.MontoAdelanto,
			o.MontoPagoParcial,
			o.Comentarios,
			o.VoucherPeriodo,
			o.Voucher,
			p.NomCompleto AS NomProveedor,
			p.Ndocumento,
			p.Telefono1,
			p.Direccion
		FROM
			ap_obligaciones o 
			INNER JOIN mastpersonas p ON (p.CodPersona = o.CodProveedor)
			LEFT JOIN pv_presupuesto ppto ON (ppto.CodOrganismo = o.CodOrganismo AND ppto.CodPresupuesto = o.CodPresupuesto)
		WHERE 1 $filtro
		ORDER BY NomProveedor, CodProveedor";
$field = getRecords($sql);
foreach($field as $f) {	++$i;
	list($VoucherAnio, $VoucherMes) = explode("[-]", $f['VoucherPeriodo']);
	list($CodVoucher, $NroVocuher) = explode("[-]", $f['Voucher']);
	$Voucher = "$VoucherAnio$VoucherMes-$CodVoucher$NroVoucher";
	$MontoTotal = $f['MontoObligacion'] - $f['MontoAdelanto'] - $f['MontoPagoParcial'];
	##	Proveedor
	if ($Grupo != $f['CodProveedor']) {
		$Grupo = $f['CodProveedor'];
		##	Sub-Total
		if ($fFlagTotales == "S" && $i > 1) {
			$pdf->SetFont('Arial', 'B', 6);
			$pdf->Cell(70, 5, 'Nro. Registros: '.$SubNroRegistros, 0, 0, 'L');
			$pdf->Cell(75, 5, 'Sub-Total: ', 0, 0, 'R');
			$pdf->Cell(20, 5, number_format($SubTotal, 2, ',', '.'), 0, 0, 'R');
			$pdf->Ln(8);
		}
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetDrawColor(255, 255, 255);
		$pdf->SetFont('Arial', 'B', 7);
		$pdf->SetWidths(array(80,30,30,120));
		$pdf->SetAligns(array('L','L','L','L'));
		$pdf->Row(array(utf8_decode($f['NomProveedor']),
						'Doc. '.$f['Ndocumento'],
						'Telf. '.$f['Telefono1'],
						'Dir. '.utf8_decode($f['Direccion']))); 
		##
		$SubNroRegistros = 0;
		$SubTotal = 0;
	}
	##
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetDrawColor(255, 255, 255);
	$pdf->SetWidths(array(30,15,15,15,10,20,20,20,20,20,87));
	$pdf->SetAligns(array('L','C','C','C','C','R','R','R','R','C','L'));
	$pdf->SetFont('Arial', '', 6);
	$pdf->Row(array($f['CodTipoDocumento'].'-'.$f['NroDocumento'],
					$f['NroRegistro'],
					formatFechaDMA($f['FechaRegistro']),
					formatFechaDMA($f['FechaPago']),
					$f['Estado'],
					number_format($f['MontoObligacion'], 2, ',', '.'),
					number_format($f['MontoAdelanto'], 2, ',', '.'),
					number_format($f['MontoPagoParcial'], 2, ',', '.'),
					number_format($MontoTotal, 2, ',', '.'),
					$Voucher,
					utf8_decode($f['Comentarios'])));
	##	
	++$SubNroRegistros;
	$SubTotal += $MontoTotal;
	++$TotalNroRegistros;
	$Total += $MontoTotal;
}
##	Sub-Total
if ($fFlagTotales == "S") {
	$pdf->SetFont('Arial', 'B', 6);
	$pdf->Cell(70, 5, 'Nro. Registros: '.$SubNroRegistros, 0, 0, 'L');
	$pdf->Cell(75, 5, 'Sub-Total: ', 0, 0, 'R');
	$pdf->Cell(20, 5, number_format($SubTotal, 2, ',', '.'), 0, 0, 'R');
	$pdf->Ln(8);
}
$pdf->Ln(10);
##	Total
if ($pdf->GetY() > 200) $pdf->AddPage();
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetFillColor(0, 0, 0);
$y = $pdf->GetY();
$pdf->Rect(155, $y, 20, 0.1, "FD");
$y = $pdf->GetY() + 5;
$pdf->Rect(155, $y, 20, 0.1, "FD");
$pdf->SetFont('Arial', 'B', 6);
$pdf->Cell(70, 5, 'Nro. Registros: '.$TotalNroRegistros, 0, 0, 'L');
$pdf->Cell(75, 5, 'Total: ', 0, 0, 'R');
$pdf->Cell(20, 5, number_format($Total, 2, ',', '.'), 0, 0, 'R');
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
