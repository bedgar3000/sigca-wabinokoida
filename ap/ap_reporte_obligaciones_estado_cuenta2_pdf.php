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
$filtro.=" AND (o.CodProveedor = '".$fCodProveedor."')";
if ($fCodOrganismo != "") $filtro.=" AND (o.CodOrganismo = '".$fCodOrganismo."')";
if ($fFechaRegistroD != "") $filtro.=" AND (o.FechaRegistro >= '".formatFechaAMD($fFechaRegistroD)."')";
if ($fFechaRegistroH != "") $filtro.=" AND (o.FechaRegistro <= '".formatFechaAMD($fFechaRegistroH)."')";
//---------------------------------------------------
$sql = "SELECT
			p.NomCompleto AS NomProveedor,
			p.Ndocumento AS RifProveedor,
			p.Direccion AS DirProveedor,
			p.Telefono1 As TelProveedor,
			p.Fax AS FaxProveedor
		FROM mastpersonas p
		WHERE p.CodPersona = '".$fCodProveedor."'";
$field_proveedor = getRecord($sql);
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
		$this->Cell(210, 5, utf8_decode('ESTADO DE CUENTA'), 0, 1, 'C', 0);
		$this->Cell(210, 5, utf8_decode(' DEL '.$fFechaRegistroD.' AL '.$fFechaRegistroH), 0, 1, 'C', 0);
		$this->Ln(10);
		##
		$this->SetDrawColor(0, 0, 0);
		$this->RoundedRect(3, 38, 140, 30, 2.5, 'D');
		$this->SetFont('Arial', 'B', 8);
		$this->Cell(125, 5, utf8_decode('Nombre / Razón Social del Proveedor'), 0, 1, 'L');
		$this->SetFont('Arial', '', 12);
		$this->Cell(125, 6, $field_proveedor['NomProveedor'], 0, 1, 'L');
		$this->SetFont('Arial', 'B', 8);
		$this->Cell(125, 4, utf8_decode('Dirección'), 0, 1, 'L');
		$this->SetFont('Arial', '', 8);
		$this->MultiCell(125, 5, utf8_decode($field_proveedor['DirProveedor']), 0, 'L');
		$this->SetFont('Arial', 'B', 8);
		$this->SetY(63);
		$this->Cell(15, 5, utf8_decode('Teléfono:'), 0, 0, 'L');
		$this->SetFont('Arial', '', 8);
		$this->Cell(35, 5, $field_proveedor['TelProveedor'], 0, 0, 'L');
		$this->SetFont('Arial', 'B', 8);
		$this->Cell(8, 5, utf8_decode('Fax:'), 0, 0, 'L');
		$this->SetFont('Arial', '', 8);
		$this->Cell(37, 5, $field_proveedor['FaxProveedor'], 0, 0, 'L');
		$this->SetFont('Arial', 'B', 8);
		$this->Cell(8, 5, utf8_decode('Rif:'), 0, 0, 'L');
		$this->SetFont('Arial', '', 8);
		$this->Cell(22, 5, $field_proveedor['RifProveedor'], 0, 0, 'L');
		##
		$this->RoundedRect(147, 38, 65, 30, 2.5, 'D');
		$this->SetFont('Arial', 'B', 8);
		$this->SetXY(147, 38); $this->Cell(60, 6, utf8_decode('DEPOSITOS'), 0, 0, 'L');
		$this->SetXY(147, 49); 
		$this->SetFont('Arial', 'B', 8);
		$this->Cell(18, 5, utf8_decode('Nro. Cuenta:'), 0, 0, 'L');
		$this->SetFont('Arial', '', 8);
		$this->Cell(42, 5, $field_proveedor['NroCuenta'], 0, 0, 'L');
		$this->SetXY(147, 56);
		$this->SetFont('Arial', 'B', 8);
		$this->Cell(18, 5, utf8_decode('Tipo:'), 0, 0, 'L');
		$this->SetFont('Arial', '', 8);
		$this->Cell(42, 5, $field_proveedor['TipoCuenta'], 0, 0, 'L');
		$this->SetXY(147, 63);
		$this->SetFont('Arial', 'B', 8);
		$this->Cell(18, 5, utf8_decode('Banco:'), 0, 0, 'L');
		$this->SetFont('Arial', '', 8);
		$this->Cell(42, 5, $field_proveedor['Banco'], 0, 0, 'L');
		##
		$this->SetY(75);
		$this->SetDrawColor(255, 255, 255);
		$this->SetFillColor(255, 255, 255);
		$this->SetWidths(array(40,20,22,22,22,22,22,40));
		$this->SetAligns(array('L','C','C','C','C','C','C','R'));
		$this->SetFont('Arial', 'B', 8);
		$this->Row(array(utf8_decode('Obligación'),
						 'Nro. Registro',
						 'F. Documento',
						 'F. Registro',
						 utf8_decode('F. Recepción'),
						 'F. Prog. Pago',
						 'F. Pago',
						 'Monto'));
		$this->SetDrawColor(0, 0, 0);
		$this->Line(3, $this->GetY(), 213, $this->GetY());
		$this->Ln(2);
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
$pdf->SetAutoPageBreak(1, 3);
$pdf->AddPage();
//---------------------------------------------------
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetDrawColor(255, 255, 255);
//	consulto
$i = 0;
$sql = "SELECT
			o.CodTipoDocumento,
			o.NroDocumento,
			o.NroRegistro,
			o.FechaDocumento,
			o.FechaRegistro,
			o.FechaRecepcion,
			o.FechaProgramada,
			o.FechaPago,
			o.MontoObligacion,
			o.Estado
		FROM ap_obligaciones o
		WHERE o.Estado = 'PA' $filtro
		ORDER BY CodTipoDocumento, NroDocumento";
$field = getRecords($sql);
foreach($field as $f) {	++$i;
	$pdf->SetFont('Arial', '', 8);
	$pdf->SetDrawColor(255, 255, 255);
	$pdf->SetFillColor(255, 255, 255);
	$pdf->Row(array($f['CodTipoDocumento'].'-'.$f['NroDocumento'],
					$f['NroRegistro'],
					formatFechaDMA($f['FechaDocumento']),
					formatFechaDMA($f['FechaRegistro']),
					formatFechaDMA($f['FechaRecepcion']),
					formatFechaDMA($f['FechaProgramada']),
					formatFechaDMA($f['Fechapago']),
					number_format($f['MontoObligacion'], 2, ',', '.')));
	##
	if ($f['Estado'] == 'PA') $TotalPagados += $f['MontoObligacion'];
	$TotalObligacion += $f['MontoObligacion'];
}
##	imprimo total
$pdf->SetDrawColor(0, 0, 0);
//$pdf->SetFillColor(0, 0, 0);
$y = $pdf->GetY();
//$pdf->Rect(162, $y, 35, 0.1, "FD");
$pdf->Line(173, $pdf->GetY(), 213, $pdf->GetY());

$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(170, 5, 'Total Pagados: ', 0, 0, 'R');
$pdf->Cell(40, 5, number_format($TotalPagados, 2, ',', '.'), 0, 0, 'R');
$pdf->Ln(10);
$y = $pdf->GetY();
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(170, 5, 'Total Documentos del Proveedor: ', 0, 0, 'R');
$pdf->Cell(40, 5, number_format($TotalObligacion, 2, ',', '.'), 1, 0, 'R');
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
