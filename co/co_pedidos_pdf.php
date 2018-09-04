<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$titulo = true;
$jobs = 0;
##	documento
$sql = "SELECT
			do.*
		FROM co_documento do
		WHERE do.CodDocumento = '$sel_registros'";
$field = getRecord($sql);
##	documento (detalle)
$sql = "SELECT
			dod.*,
			(CASE WHEN dod.TipoDetalle = 'I' THEN i.CodInterno ELSE s.CodInterno END) AS CodInterno
		FROM co_documentodet dod
		LEFT JOIN lg_itemmast i ON (
			i.CodItem = dod.CodItem
			AND dod.TipoDetalle = 'I'
		)
		LEFT JOIN co_mastservicios s ON (
			s.CodServicio = dod.CodItem
			AND dod.TipoDetalle = 'S'
		)
		WHERE dod.CodDocumento = '$sel_registros'";
$field_detalle = getRecords($sql);
##	
$sql = "SELECT
			o.*,
			c.Ciudad,
			c.CodPostal,
			e.Estado AS NomEstado
		FROM mastorganismos o
		LEFT JOIN mastciudades c ON c.CodCiudad = o.CodCiudad
		LEFT JOIN mastmunicipios m ON m.CodMunicipio = c.CodMunicipio
		LEFT JOIN mastestados e ON e.CodEstado = m.CodEstado
		WHERE o.CodOrganismo = '$field[CodOrganismo]'";
$field_organismo = getRecord($sql);
//---------------------------------------------------

class PDF extends FPDF {
	//	Cabecera de página.
	function Header() {
		global $_PARAMETRO;
		global $Ahora;
		global $field;
		global $field_organismo;
		global $titulo;
		global $_POST;
		extract($_POST);
		##	
		//	Put the watermark
		if ($field['Estado'] == 'PR' || $field['Estado'] == 'AN')
		{
			$txt = printValores('documento1-estado',$field['Estado']);

			$this->SetFont('Arial','B',50);
			$this->SetTextColor(255,232,243);
			$this->RotatedText(50,190,utf8_decode(mb_strtoupper($txt)),45);
			$this->SetTextColor(0,0,0);
		}
		##	
		$this->SetFont('Arial','B',9);
		$this->Cell(160, 4, utf8_decode($field_organismo['Organismo']), 0, 0, 'L');
		$this->SetFont('Arial','',8);
		$this->Cell(30, 4, utf8_decode('Fecha:  ' . date('d-m-Y')), 0, 1, 'L');
		$this->SetFont('Arial','',8);
		$this->SetX(175); $this->Cell(30, 4, utf8_decode('Página: ' . $this->PageNo() . ' de {nb}'), 0, 1, 'L');
		$this->Ln(5);
		$this->SetFont('Arial','B',10);
		$this->Cell(188, 4, utf8_decode('PEDIDO Nº ' . $field['NroDocumento']), 0, 0, 'C');
		$this->Ln(10);
		$this->SetFont('Arial','B',8);
		$this->Cell(25, 5, utf8_decode('Cliente: '), 0, 0, 'L');
		$this->Cell(123, 5, utf8_decode($field['NombreCliente']), 0, 0, 'L');
		$this->SetFont('Arial','',8);
		$this->Cell(16, 5, utf8_decode('Doc. Fiscal: '), 0, 0, 'L');
		$this->Cell(44, 5, utf8_decode($field['DocFiscalCliente']), 0, 1, 'L');
		$this->SetFont('Arial','',8);
		$this->Cell(25, 5, utf8_decode('Fecha Documento: '), 0, 0, 'L');
		$this->Cell(45, 5, formatFechaDMA($field['FechaDocumento']), 0, 0, 'L');
		$this->Cell(27, 5, utf8_decode('Fecha Vencimiento: '), 0, 0, 'L');
		$this->Cell(51, 5, formatFechaDMA($field['FechaVencimiento']), 0, 0, 'L');
		$this->Cell(16, 5, utf8_decode('Estado: '), 0, 0, 'L');
		$this->Cell(44, 5, utf8_decode(mb_strtoupper(printValores('documento1-estado',$field['Estado']))), 0, 1, 'L');
		$this->SetFont('Arial','',8);
		$this->Cell(25, 5, utf8_decode('Comentarios: '), 0, 0, 'L');
		$this->MultiCell(163, 5, utf8_decode($field['Comentarios']), 0, 'L');
		$this->Ln(5);
		##	
		if ($titulo)
		{
			$this->SetFillColor(255,255,255);
			$this->SetFont('Arial','B',7);
			$this->SetWidths([8,20,70,10,20,30,30]);
			$this->SetAligns(['C','C','C','C','C','C','C']);
			$this->SetFill('F');
			$this->Row([utf8_decode('#'),
						utf8_decode('ITEM'),
						utf8_decode('DESCRIPCIÓN'),
						utf8_decode('UNI.'),
						utf8_decode('CANTIDAD'),
						utf8_decode('PRECIO UNIT.'),
						utf8_decode('TOTAL')
						]);
			$this->SetWidths([8,20,70,10,20,30,30]);
			$this->SetAligns(['C','C','L','C','R','R','R']);
			$this->Ln(1);
		}
	}
	
	//	Pie de página.
	function Footer() {
	}
}
//---------------------------------------------------

//---------------------------------------------------
//	Creación del objeto de la clase heredada.
$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->SetMargins(15, 20, 15);
$pdf->SetAutoPageBreak(1, 20);
$pdf->AddPage();
//---------------------------------------------------
$i = 0;
foreach ($field_detalle as $f)
{
	$pdf->SetFillColor(255,255,255);
	$pdf->SetDrawColor(255,255,255);
	$pdf->SetFont('Arial','',7);
	$pdf->SetWidths([8,20,70,10,20,30,30]);
	$pdf->SetAligns(['C','C','L','C','R','R','R']);
	$pdf->Row([++$i,
				utf8_decode($f['CodInterno']),
				utf8_decode($f['Descripcion']),
				utf8_decode($f['CodUnidad']),
				number_format($f['CantidadPedida'],2,',','.'),
				number_format($f['PrecioUnitFinal'],2,',','.'),
				number_format($f['MontoTotalFinal'],2,',','.')
				]);
	##	
	$pdf->Ln(1);
}

$pdf->SetY(225);
$pdf->SetDrawColor(0,0,0);
$pdf->Line(15, $pdf->GetY(), 203, $pdf->GetY());

$pdf->Ln(1);
$pdf->SetFont('Arial','',7);
$pdf->Cell(148, 5, utf8_decode('Monto Afecto'), 0, 0, 'R');
$pdf->Cell(40, 5, number_format($field['MontoAfecto'],2,',','.'), 0, 1, 'R');
$pdf->Cell(148, 5, utf8_decode('Monto No Afecto'), 0, 0, 'R');
$pdf->Cell(40, 5, number_format($field['MontoNoAfecto'],2,',','.'), 0, 1, 'R');
$pdf->Cell(148, 5, utf8_decode('Descuentos (-)'), 0, 0, 'R');
$pdf->Cell(40, 5, number_format($field['MontoDcto'],2,',','.'), 0, 1, 'R');
$pdf->Cell(148, 5, utf8_decode('Impuestos (+)'), 0, 0, 'R');
$pdf->Cell(40, 5, number_format($field['MontoImpuesto'],2,',','.'), 0, 1, 'R');

$pdf->SetDrawColor(0,0,0);
$pdf->Line(135, $pdf->GetY(), 203, $pdf->GetY());

$pdf->SetFont('Arial','B',7);
$pdf->Cell(148, 5, utf8_decode('Total'), 0, 0, 'R');
$pdf->Cell(40, 5, number_format($field['MontoTotal'],2,',','.'), 0, 1, 'R');
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
