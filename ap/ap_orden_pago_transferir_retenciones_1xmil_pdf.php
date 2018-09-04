<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
list($NroProceso, $Secuencia, $CodTipoPago) = split("[_]", $registro);
//---------------------------------------------------
//	consulto 1X1000
$sql = "SELECT
			r.*,
			o.Comentarios,
			og.Organismo,
			og.DocFiscal AS RifOrganismo,
			og.Direccion AS DirOrganismo,			
			mp.NomCompleto AS NomProveedor,
			mp.DocFiscal AS RifProveedor
		FROM
			ap_retenciones r
			INNER JOIN ap_ordenpago op ON (op.CodOrganismo = r.CodOrganismo AND op.Anio = r.AnioOrden AND op.NroOrden = r.NroOrden)
			INNER JOIN ap_obligaciones o ON (op.CodProveedor = o.CodProveedor AND
																			 op.CodTipoDocumento = o.CodTipoDocumento AND
																			 op.NroDocumento = o.NroDocumento)
			INNER JOIN mastorganismos og ON (og.CodOrganismo = r.CodOrganismo)
			INNER JOIN mastpersonas mp ON (mp.CodPersona = r.CodProveedor)
			INNER JOIN mastimpuestos i ON (i.CodImpuesto = r.CodImpuesto)
		WHERE
			r.PagoNroProceso = '$NroProceso'
			AND r.PagoSecuencia = '$Secuencia'
			AND r.TipoComprobante = '1X1000'";
$query_mil = mysql_query($sql) or die ($sql.mysql_error());
$field_mils = getRecords($sql);
//---------------------------------------------------

//---------------------------------------------------
class PDF extends FPDF {
	//	Cabecera de página.
	function Header() {}
	//	Pie de página.
	function Footer() {
		##
		$this->SetTextColor(0, 0, 0);
		$this->SetDrawColor(0, 0, 0);
		$this->SetFillColor(255, 255, 255);
		$this->SetXY(10, 250);
		$this->Rect(10, 237, 65, 0.1, "D"); 
		$this->Rect(125, 237, 65, 0.1, "D");
		$this->SetFont('Arial', 'B', 10);
		$this->Cell(65, 5, 'BENEFICIARIO', 0, 0, 'C');
		$this->Cell(50, 5);
		$this->Cell(65, 5, 'ALCALDIA', 0, 0, 'C');
	}
}
//---------------------------------------------------

//---------------------------------------------------
//	Creación del objeto de la clase heredada.
$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->SetMargins(10, 5, 10);
$pdf->SetAutoPageBreak(5, 1);
//---------------------------------------------------

//---------------------------------------------------
//	imprimo 1X1000
if (mysql_num_rows($query_mil) != 0) {
	$field_mil = mysql_fetch_array($query_mil);
	list($anio, $mes) = split("[-]", $field_mil['PeriodoFiscal']);
	$periodo_fiscal = $anio.$mes;
	$nrocomprobante = $periodo_fiscal.$field_mil['NroComprobante'];
	//---------------------------------------------------
	//	imprimo los datos generales
	$pdf->AddPage();
	$pdf->Image($_PARAMETRO["PATHLOGO"].'logo-alcaldia.jpg', 8, 5, 12, 12);
	$pdf->SetFont('Arial', '', 10);
	$pdf->SetXY(20, 5); $pdf->Cell(100, 5, utf8_decode($_SESSION['NOMBRE_ORGANISMO_ACTUAL']), 0, 1, 'L');
	$pdf->SetXY(20, 9); $pdf->Cell(100, 5, utf8_decode('DIRECCIÓN DE ADMINISTRACIÓN'), 0, 0, 'L');
	$pdf->SetXY(20, 13); $pdf->Cell(100, 5, $_SESSION['RIF_ORGANISMO_ACTUAL'], 0, 0, 'L');
	$pdf->Ln(15);
	$pdf->SetFont('Arial', 'B', 14);
	$pdf->Cell(195, 5, utf8_decode('Comprobante de Retención 1x1000'), 0, 0, 'C');
	$pdf->Ln(20);
	$pdf->SetFont('Arial', '', 10);
	$pdf->Cell(70, 5, utf8_decode('Número de Comprobante: '), 0, 0, 'L');
	$pdf->Cell(60, 5, ('Fecha: '), 0, 0, 'L');
	$pdf->Cell(80, 5, ('Periodo Fiscal: '), 0, 0, 'L');
	$pdf->Ln(6);
	$pdf->SetFont('Arial', 'B', 10);
	$pdf->Cell(70, 5, $nrocomprobante, 0, 0, 'L');
	$pdf->Cell(60, 5, formatFechaDMA($field_mil['FechaComprobante']), 0, 0, 'L');
	$pdf->Cell(80, 5, $periodo_fiscal, 0, 0, 'L');
	$pdf->Ln(10);
	$pdf->SetFont('Arial', '', 10);
	$pdf->Cell(130, 5, utf8_decode('Nombre o Razón Social Agente de Retención: '), 0, 0, 'L');
	$pdf->Cell(80, 5, utf8_decode('R.I.F. del Agente de Retención: '), 0, 0, 'L');
	$pdf->Ln(6);
	$pdf->SetFont('Arial', 'B', 10);
	$pdf->Cell(130, 5, utf8_decode($field_mil['Organismo']), 0, 0, 'L');
	$pdf->Cell(80, 5, $field_mil['RifOrganismo'], 0, 0, 'L');
	$pdf->Ln(10);
	$pdf->SetFont('Arial', '', 10);
	$pdf->Cell(190, 5, utf8_decode('Dirección Fiscal del Agente de Retención: '), 0, 0, 'L');
	$pdf->Ln(6);
	$pdf->SetFont('Arial', 'B', 10);
	$pdf->MultiCell(195, 4, utf8_decode($field_mil['DirOrganismo']), 0, 'L');
	$pdf->Ln(4);
	$pdf->SetFont('Arial', '', 10);
	$pdf->Cell(130, 5, utf8_decode('Nombre o Razón Social del Sujeto Retenido: '), 0, 0, 'L');
	$pdf->Cell(80, 5, ('R.I.F. del Agente del Sujeto Retenido: '), 0, 0, 'L');
	$pdf->Ln(6);
	$pdf->SetFont('Arial', 'B', 10);
	$pdf->Cell(130, 5, utf8_decode($field_mil['NomProveedor']), 0, 0, 'L');
	$pdf->Cell(80, 5, $field_mil['RifProveedor'], 0, 0, 'L');
	$pdf->Ln(8);
	//---------------------------------------------------
	//	imprimo el concepto
	$pdf->SetFont('Arial', '', 10);
	$pdf->Cell(195, 5, ('Concepto: '), 0, 0, 'L');
	$pdf->Ln(6);
	$pdf->SetFont('Arial', 'B', 10);
	foreach ($field_mils as $f) {
		if ($Grupo != utf8_decode($f['Comentarios'])) {
			$Grupo = utf8_decode($f['Comentarios']);
			$pdf->SetX(15); $pdf->MultiCell(195, 6, utf8_decode(' - '.$f['Comentarios']), 0, 'J');
		}
	}
	$pdf->Ln(5);
	//---------------------------------------------------
	//	imprimo los montos
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetDrawColor(255, 255, 255);
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', 'B', 9);
	$pdf->SetWidths(array(40, 30, 40, 40, 40));
	$pdf->SetAligns(array('C', 'C', 'R', 'R', 'R'));
	$pdf->Row(array('Nro. O/P',
					'Periodo',
					'Base Imponible',
					'Impuesto a Retener',
					'Total a Enterar'));
	$pdf->Ln(1);
	$pdf->SetDrawColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 10);
	foreach ($field_mils as $f) {
		$pdf->Row(array($f['NroOrden'],
						$periodo_fiscal,
						number_format($f['MontoAfecto'], 2, ',', '.'),
						'1X1000',
						number_format(abs($f['MontoRetenido']), 2, ',', '.')));
	}
}
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>