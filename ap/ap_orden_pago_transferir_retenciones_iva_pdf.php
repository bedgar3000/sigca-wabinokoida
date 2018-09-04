<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
list($NroProceso, $Secuencia, $CodTipoPago) = split("[_]", $registro);
//---------------------------------------------------
//	consulto iva
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
			AND r.TipoComprobante = 'IVA'";
$query_iva = mysql_query($sql) or die ($sql.mysql_error());
$field_ivas = getRecords($sql);
//---------------------------------------------------

//---------------------------------------------------
class PDF extends FPDF {
	//	Cabecera de página.
	function Header() {}
	//	Pie de página.
	function Footer() {
	}
}
//---------------------------------------------------

//---------------------------------------------------
//	Creación del objeto de la clase heredada.
$pdf = new PDF('L', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->SetMargins(15, 15, 10);
$pdf->SetAutoPageBreak(5, 1);
//---------------------------------------------------

//---------------------------------------------------
//	imprimo iva
if (mysql_num_rows($query_iva) != 0) {
	$field_iva = mysql_fetch_array($query_iva);
	list($anio, $mes) = split("[-]", $field_iva['PeriodoFiscal']);
	$periodo_fiscal = $anio.$mes;
	$nrocomprobante = $periodo_fiscal.$field_iva['NroComprobante'];
	//---------------------------------------------------
	//	imprimo los datos generales
	$pdf->AddPage();
	$pdf->SetFont('Arial','',8);
	$pdf->SetX(45);	$pdf->MultiCell(170, 4, utf8_decode('Ley IVA - Art. 11: "La Administración Tributaria podrá designar como responsable del pago del impuesto, en calidad de agente de retención, a quienes por sus funciones públicas o por razón de sus actividades privadas intervengan en operaciones gravadas con el impuesto establecido en este Decreto con Rango, Valor y Fuerza de Ley."'), 0, 'C');
	##	
	$pdf->Ln(5);
	$pdf->SetFont('Arial','B',10);
	$pdf->Cell(250, 5, utf8_decode('COMPROBANTE DE RETENCIÓN DE IVA'), 0, 1, 'C');
	$pdf->SetFont('Arial','',6);
	$pdf->Cell(250, 3, utf8_decode('Providencia administrativa N° SNAT/2015/0049 del 10/08/2015'), 0, 1, 'C');
	$pdf->Ln(3);
	$pdf->SetFont('Arial','B',8);
	$pdf->SetX(85); $pdf->Cell(110, 5, utf8_decode('               '.'N° DE COMPROBANTE '.$nrocomprobante.'               '), 1, 1, 'C');
	##	
	$pdf->SetFont('Arial','B',8);
	$pdf->SetXY(15, 55); $pdf->Cell(90, 5, utf8_decode('Nombre o Razón Social del Agente de Retención'), 1, 1, 'C');
	$pdf->SetDrawColor(0, 0, 0); $pdf->Rect(15, $pdf->GetY(), 90, 10, 'D');
	$pdf->SetXY(15, 60); $pdf->MultiCell(90, 4, utf8_decode($field_iva['Organismo']), 0, 'C');
	##	
	$pdf->SetFont('Arial','B',8);
	$pdf->SetXY(125, 55); $pdf->Cell(90, 5, utf8_decode('Registro de Información Fiscal del Agente de Retención'), 1, 1, 'C');
	$pdf->SetDrawColor(0, 0, 0); $pdf->Rect(125, $pdf->GetY(), 90, 10, 'D');
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY(125, 62); $pdf->MultiCell(90, 4, $field_iva['RifOrganismo'], 0, 'C');
	##	
	$pdf->SetFont('Arial','B',8);
	$pdf->SetXY(235, 55); $pdf->Cell(30, 5, utf8_decode('Periodo Fiscal'), 1, 1, 'C');
	$pdf->SetDrawColor(0, 0, 0); $pdf->Rect(235, $pdf->GetY(), 30, 10, 'D');
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY(235, 62); $pdf->MultiCell(30, 4, utf8_decode('Año: '.$anio.' Mes: '.$mes), 0, 'C');
	##	
	$pdf->SetY(75);
	$pdf->SetDrawColor(0, 0, 0); $pdf->Rect(15, $pdf->GetY(), 250, 15, 'D');
	$pdf->SetFont('Arial','B',8);
	$pdf->Cell(250, 5, utf8_decode('Dirección Fiscal del Agente de Retención'), 0, 1, 'C');
	$pdf->SetFont('Arial','',8);
	$pdf->MultiCell(250, 4, utf8_decode($field_iva['DirOrganismo']), 0, 'C');
	##	
	$pdf->SetFont('Arial','B',8);
	$pdf->SetXY(15, 95); $pdf->Cell(90, 5, utf8_decode('Nombre o Razón Social del Sujeto Retenido'), 1, 1, 'C');
	$pdf->SetDrawColor(0, 0, 0); $pdf->Rect(15, $pdf->GetY(), 90, 10, 'D');
	$pdf->SetXY(15, 100); $pdf->MultiCell(90, 4, utf8_decode($field_iva['NomProveedor']), 0, 'C');
	##	
	$pdf->SetFont('Arial','B',8);
	$pdf->SetXY(125, 95); $pdf->Cell(90, 5, utf8_decode('Registro de Información Fiscal del Sujeto Retenido'), 1, 1, 'C');
	$pdf->SetDrawColor(0, 0, 0); $pdf->Rect(125, $pdf->GetY(), 90, 10, 'D');
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY(125, 102); $pdf->MultiCell(90, 4, $field_iva['RifProveedor'], 0, 'C');
	##	
	$i = 0;
	$pdf->SetY(115);
	$pdf->SetDrawColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','B',8);
	$pdf->SetWidths(array(10,18,22,17,22,17,17,17,22,22,17,17,17,17));
	$pdf->SetAligns(array('C','C','C','C','C','C','C','C','C','C','C','C','C','C'));
	$pdf->Row(array(utf8_decode('Nro. de Oper.'),
					utf8_decode('Fecha del Documento'),
					utf8_decode('N° de Factura'),
					utf8_decode('N° Control'),
					utf8_decode('N° Nota Débit.'),
					utf8_decode('N° Nota Crédito'),
					utf8_decode('Clase de Operación'),
					utf8_decode('N° Factura Afectada'),
					utf8_decode('Monto Total de la Factura o Nota de Débito'),
					utf8_decode('Monto sin Derecho a Crédito Fiscal'),
					utf8_decode('Base Imponible'),
					utf8_decode('% Alicuota'),
					utf8_decode('Impuesto Causado'),
					utf8_decode('Impuesto Retenido')
					));
	foreach ($field_ivas as $f) {
		$pdf->SetFont('Arial','',7);
		$pdf->Row(array(++$i,
						formatFechaDMA($f['FechaFactura']),
						$f['NroFactura'],
						$f['NroControl'],
						'',
						'',
						'',
						'',
						number_format($f['MontoFactura'], 2, ',', '.'),
						number_format($f['MontoNoAfecto'], 2, ',', '.'),
						number_format($f['MontoAfecto'], 2, ',', '.'),
						number_format($f['Porcentaje'], 2, ',', '.'),
						number_format($f['MontoImpuesto'], 2, ',', '.'),
						number_format(abs($f['MontoRetenido']), 2, ',', '.')));
	}
	##	
	$pdf->SetFont('Arial','B',8);
	$pdf->SetXY(45, 180); $pdf->Cell(60, 5, utf8_decode('Fecha de Emisión'), 1, 1, 'C');
	$pdf->SetDrawColor(0, 0, 0); $pdf->Rect(45, $pdf->GetY(), 60, 10, 'D');
	$pdf->SetXY(45, 185); $pdf->MultiCell(60, 4, '', 0, 'C');
	##	
	$pdf->SetFont('Arial','B',8);
	$pdf->SetXY(155, 180); $pdf->Cell(60, 5, utf8_decode('Fecha de Entrega'), 1, 1, 'C');
	$pdf->SetDrawColor(0, 0, 0); $pdf->Rect(155, $pdf->GetY(), 60, 10, 'D');
	$pdf->SetFont('Arial','',8);
	$pdf->SetXY(155, 185); $pdf->MultiCell(60, 4, '', 0, 'C');
}
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>