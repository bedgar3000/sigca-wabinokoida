<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
list($Anio, $CodOrganismo, $NroOrden) = explode('_', $registro);
$sql = "SELECT 
			op.*,
			o.Organismo,
			p.NomCompleto AS NomProveedor
		FROM
			ap_ordenpago op
			INNER JOIN mastorganismos o ON (o.CodOrganismo = op.CodOrganismo)
			INNER JOIN mastpersonas p ON (p.CodPersona = op.CodProveedor)
		WHERE
			op.Anio = '$Anio' AND
			op.CodOrganismo = '$CodOrganismo' AND
			op.NroOrden = '$NroOrden'";
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
		$this->SetY(20); $this->Cell(270, 5, utf8_decode('VOUCHER DE ORDEN DE PAGO'), 0, 1, 'C');
		$this->Ln(8);
		##	-------------------
		$this->SetFillColor(200,200,200);
		##
		$this->SetFont('Arial','',10);
		$this->Cell(23, 6, utf8_decode('Organismo'), 0, 0, 'L', 1);
		$this->SetFont('Arial','B',10);
		$this->Cell(157, 6, utf8_decode($field_mast['Organismo']), 0, 0, 'L');
		$this->SetFont('Arial','',10);
		$this->Cell(27, 6, utf8_decode('Fecha'), 0, 0, 'L', 1);
		$this->SetFont('Arial','B',10);
		$this->Cell(93, 6, formatFechaDMA($field_mast['FechaOrdenPago']), 0, 1, 'L');
		$this->Ln(1);
		$this->SetFont('Arial','',10);
		$this->Cell(23, 6, utf8_decode('Proveedor'), 0, 0, 'L', 1);
		$this->SetFont('Arial','B',10);
		$this->Cell(157, 6, utf8_decode($field_mast['NomProveedor']), 0, 0, 'L');
		$this->SetFont('Arial','',10);
		$this->Cell(27, 6, utf8_decode('Nro. Orden'), 0, 0, 'L', 1);
		$this->SetFont('Arial','B',10);
		$this->Cell(93, 6, $field_mast['Anio'].'-'.$field_mast['NroOrden'], 0, 1, 'L');
		$this->Ln(1);
		$this->SetFont('Arial','',10);
		$this->Cell(23, 6, utf8_decode('Concepto'), 0, 0, 'L', 1);
		$this->SetFont('Arial','B',10);
		$this->MultiCell(247, 6, utf8_decode($field_mast['Concepto']), 0, 'L');
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
$sql = "SELECT
			opc.CodCuenta,
			opc.Monto AS MontoVoucher,
			pc.Descripcion,
			pc.TipoSaldo
		FROM 
			ap_ordenpagocontabilidad opc 
			INNER JOIN ac_mastplancuenta20 pc ON (opc.CodCuenta = pc.CodCuenta)
		WHERE
			opc.Anio = '".$Anio."' AND
			opc.CodOrganismo = '".$CodOrganismo."' AND
			opc.NroOrden = '".$NroOrden."' AND
			opc.CodContabilidad = '".$_PARAMETRO['CONTORDENDIS']."'
		ORDER BY Secuencia";
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
					'',
					'',
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