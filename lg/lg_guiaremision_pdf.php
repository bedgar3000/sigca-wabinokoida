<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$titulo = true;
$jobs = 0;
##	guia
$sql = "SELECT
			gr.*,
			vh.Placa,
			md.Descripcion NomMotivoTraslado
		FROM lg_guiaremision gr
		LEFT JOIN lg_vehiculos vh ON vh.CodVehiculo = gr.CodVehiculoTrans
		LEFT JOIN mastmiscelaneosdet md ON (
			md.CodDetalle = gr.MotivoTraslado
			AND md.CodMaestro = 'LGMOTRAS'
		)
		WHERE gr.CodGuia = '$sel_registros'";
$field = getRecord($sql);
##	guia (detalle)
$sql = "SELECT
			grd.*,
			i.CodInterno
		FROM lg_guiaremisiondet grd
		INNER JOIN lg_itemmast i ON i.CodItem = grd.CodItem
		WHERE grd.CodGuia = '$field[CodGuia]'
		ORDER BY CodInterno";
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
		$y = $this->GetY();
		##	
		$this->SetY($y);
		$this->SetDrawColor(0,0,0);
		$this->SetFillColor(255,255,255);
		$this->SetFont('Arial','B',10);
		$this->SetX(145); $this->Cell(60, 8, utf8_decode('Rif. '.set_rif($field_organismo['DocFiscal'])), 1, 1, 'C');
		$this->SetFillColor(220,220,220);
		$this->SetFont('Arial','B',10);
		$this->SetX(145); $this->Cell(60, 6, utf8_decode('GUIA REMISIÓN'), 1, 1, 'C', 1);
		$this->SetFont('Arial','',10);
		$this->SetX(145); $this->Cell(60, 8, utf8_decode($field['NroSerie'].' '.$field['NroGuia']), 1, 1, 'C');
		$this->Ln(5);
		$y1 = $this->GetY();
		##	
		$this->SetY($y);
		$this->SetFont('Arial','',10);
		$this->Cell(130, 10, utf8_decode($field_organismo['Organismo']), 0, 1, 'C');
		$this->SetFont('Arial','',8);
		$this->MultiCell(130, 4, utf8_decode($field_organismo['Direccion']), 0, 'C');
		$this->Ln(5);
		$y2 = $this->GetY();
		##	
		$yi = (($y1 > $y2) ? $y1 : $y2);
		$this->SetY($yi);
		$this->SetFillColor(220,220,220);
		$this->SetFont('Arial','',6);
		$this->Cell(69, 5, utf8_decode('DESTINATARIO'), 1, 0, 'C', 1);
		$this->Cell(69, 5, utf8_decode('TRANSPORTISTA'), 1, 0, 'C', 1);
		$this->Cell(52, 5, utf8_decode('MOTIVO DE TRASLADO'), 1, 0, 'C', 1);
		##	
		$this->SetY($yi+5);
		$this->SetFont('Arial','',6);
		$this->Cell(10, 4, utf8_decode('Sr.'), 0, 0, 'L');
		$this->MultiCell(59, 4, utf8_decode($field['NombreDestino']), 0, 'L');
		$this->Ln(1);
		$this->Cell(10, 4, utf8_decode('Dirección'), 0, 0, 'L');
		$this->MultiCell(59, 4, utf8_decode($field['DireccionDestino']), 0, 'L');
		$this->Ln(1);
		$this->Cell(10, 4, utf8_decode('Rif.'), 0, 0, 'L');
		$this->Cell(59, 4, set_rif($field['DocFiscalDestino']), 0, 0, 'L');
		$this->Ln(5);
		$this->Cell(10, 4, utf8_decode('Fact. Nº'), 0, 0, 'L');
		$this->Cell(30, 4, utf8_decode($field['NroFactura']), 0, 0, 'L');
		$this->Cell(16, 4, utf8_decode('Fecha Emisión'), 0, 0, 'L');
		$this->Cell(11, 4, formatFechaDMA($field['FechaFactura']), 0, 0, 'L');
		$this->Ln(5);
		$yf = $this->GetY();
		##	
		$this->SetY($yi+5);
		$this->SetFont('Arial','',6);
		$this->SetX(84);
		$this->Cell(6, 4, utf8_decode('Sr.'), 0, 0, 'L');
		$this->MultiCell(63, 4, utf8_decode($field['NombreTrans']), 0, 'L');
		$this->Ln(1);
		$this->SetX(84);
		$this->Cell(6, 4, utf8_decode('Rif.'), 0, 0, 'L');
		$this->Cell(63, 4, set_rif($field['DocFiscalTrans']), 0, 0, 'L');
		$this->Ln(5);
		$this->SetX(84);
		$this->Cell(18, 4, utf8_decode('Placa Vehiculo'), 0, 0, 'L');
		$this->Cell(34, 4, set_rif($field['Placa']), 0, 0, 'L');
		##	
		$this->SetY($yi+5);
		$this->SetFont('Arial','',6);
		$this->SetX(153);
		$this->MultiCell(52, 4, utf8_decode($field['NomMotivoTraslado']), 0, 'L');
		##	
		$this->SetDrawColor(0,0,0);
		$this->Rect(15, $yi, 69, $yf - $yi);
		$this->Rect(84, $yi, 69, $yf - $yi);
		$this->Rect(153, $yi, 52, $yf - $yi);
		##	
		$this->SetY($yf);
		$this->SetDrawColor(0,0,0);
		$this->SetFillColor(220,220,220);
		$this->SetWidths([15,100,15,30,30]);
		$this->SetAligns(['C','L','C','R','C']);
		$this->SetFont('Arial','B',6);
		$this->Row([utf8_decode('ITEM'),
					utf8_decode('DESCRIPCIÓN'),
					utf8_decode('UNIDAD'),
					utf8_decode('CANT.'),
					utf8_decode('SERIE')
					]);
		$this->SetFillColor(255,255,255);
		$this->SetWidths([15,100,15,30,30]);
		$this->SetAligns(['C','L','C','R','C']);
		$this->SetFont('Arial','',6);
		$this->Ln(1);
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
foreach ($field_detalle as $f)
{
	$pdf->SetDrawColor(255,255,255);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetWidths([15,100,15,30,30]);
	$pdf->SetAligns(['C','L','C','R','C']);
	$pdf->SetFont('Arial','',6);
	$pdf->Row([$f['CodInterno'],
				utf8_decode($f['Descripcion']),
				utf8_decode($f['CodUnidad']),
				number_format($f['Cantidad'],2,',','.'),
				''
				]);
	$pdf->Ln(1);
	##	
}
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
