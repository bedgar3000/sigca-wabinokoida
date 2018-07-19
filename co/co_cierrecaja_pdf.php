<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$titulo = true;
$jobs = 0;
##	cierre
$sql = "SELECT
			cc.*,
			ef.Descripcion AS Establecimiento,
			p1.NomCompleto AS Cajero,
			p2.NomCompleto AS NomPreparadoPor,
			p3.NomCompleto AS NomAprobadoPor
		FROM co_cierrecaja cc
		INNER JOIN co_establecimientofiscal ef ON ef.CodEstablecimiento = cc.CodEstablecimiento
		LEFT JOIN mastpersonas p1 ON p1.CodPersona = cc.CodPersonaCajero
		LEFT JOIN mastpersonas p2 ON p2.CodPersona = cc.PreparadoPor
		LEFT JOIN mastpersonas p3 ON p3.CodPersona = cc.AprobadoPor
		WHERE cc.CodCierre = '$sel_registros'";
$field = getRecord($sql);
##	cierre (detalle)
$sql = "SELECT
			ccd.*,
			tp.Descripcion AS TipoPago,
			ccj.Descripcion AS ConceptoCaja,
			p.NomCompleto AS NombreCliente
		FROM co_cierrecajadetalle ccd
		INNER JOIN co_tipopago tp On tp.CodTipoPago = ccd.CodTipoPago
		INNER JOIN co_conceptocaja ccj ON ccj.CodConceptoCaja = ccd.CodConceptoCaja
		LEFT JOIN mastpersonas p ON p.CodPersona = ccd.CodPersonaCliente
		WHERE ccd.CodCierre = '$field[CodCierre]'
		ORDER BY CodTipoPago, Secuencia";
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
		if ($field['Estado'] <> 'AP')
		{
			$txt = printValores('cierre-caja-estado',$field['Estado']);

			$this->SetFont('Arial','B',50);
			$this->SetTextColor(255,232,243);
			$this->RotatedText(50,190,utf8_decode(mb_strtoupper($txt)),45);
			$this->SetTextColor(0,0,0);
		}
		##	
		$this->SetFont('Arial','B',9);
		$this->Cell(190, 4, utf8_decode($field['Establecimiento']), 0, 0, 'R');
		$this->Ln(7);
		$this->SetX(145);
		$this->SetFont('Arial','B',9);
		$this->Cell(35, 4, utf8_decode('CIERRE DE CAJA Nº:'), 0, 0, 'R');
		$this->SetFont('Arial','',9);
		$this->Cell(25, 4, $field['NroCierre'], 0, 0, 'R');
		$this->Ln(7);
		$this->SetFont('Arial','',10);
		$this->Cell(115, 6, utf8_decode($field_organismo['Direccion']), 0, 0, 'C');
		$this->Ln(10);
		##	
		$this->SetFont('Arial','B',8);
		$this->Cell(25, 6, utf8_decode('Fecha Cierre: '), 0, 0, 'L');
		$this->SetFont('Arial','',8);
		$this->Cell(60, 6, formatFechaDMA($field['FechaCierre']), 0, 0, 'L');
		$this->SetFont('Arial','B',8);
		$this->Cell(15, 6, utf8_decode('Cajero(a): '), 0, 0, 'L');
		$this->SetFont('Arial','',8);
		$this->Cell(90, 6, utf8_decode($field['Cajero']), 0, 1, 'L');
		$this->SetFont('Arial','B',8);
		$this->Cell(25, 6, utf8_decode('Comentarios: '), 0, 0, 'L');
		$this->SetFont('Arial','',8);
		$this->Write(6, utf8_decode($field['Comentarios']));
		$this->Ln(10);
		##	
		if ($titulo)
		{
			$this->SetFillColor(200,200,200);
			$this->SetFont('Arial','B',6);
			$this->SetWidths([15,30,75,30,40]);
			$this->SetAligns(['C','L','L','R','L']);
			$this->SetFill('F');
			$this->Row([utf8_decode('TIPO'),
						utf8_decode('CONCEPTO'),
						utf8_decode('REFERENCIA'),
						utf8_decode('IMPORTE'),
						utf8_decode('OBSERVACIONES')
						]);
			$this->SetWidths([15,30,75,30,40]);
			$this->SetAligns(['C','L','L','R','L']);
		}
	}
	
	//	Pie de página.
	function Footer() {
		global $_PARAMETRO;
		global $Ahora;
		global $field;
		global $field_organismo;
		global $_POST;
		global $jobs;
		extract($_POST);
		##	
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
$SubMontoLocal = 0;
$Grupo = '';
foreach ($field_detalle as $f)
{
	if ($Grupo != $f['CodTipoPago'])
	{
		if ($Grupo)
		{
			$pdf->SetDrawColor(0,0,0);
			$pdf->Line(135, $pdf->GetY(), 165, $pdf->GetY());
			$pdf->SetFont('Arial','B',6);
			$pdf->SetX(135); $pdf->Cell(30, 6, number_format($SubMontoLocal,2,',','.'), 0, 0, 'R');
			$pdf->Ln(6);
		}
		$pdf->SetFont('Arial','B',6);
		$pdf->Cell(190, 4, utf8_decode($f['TipoPago']), 0, 0, 'L');
		$pdf->Ln(5);
		##	
		$Grupo = $f['CodTipoPago'];
	}
	$pdf->SetFillColor(255,255,255);
	$pdf->SetDrawColor(255,255,255);
	$pdf->SetFont('Arial','',6);
	$pdf->SetWidths([15,30,75,30,40]);
	$pdf->SetAligns(['C','L','L','R','L']);
	$pdf->SetHeights([3,3,3]);
	$pdf->Row(['',
				utf8_decode($f['ConceptoCaja']),
				utf8_decode($f['CodTipoDocumento']) . ' ' . $f['NroDocumento'] . ' ' . utf8_decode($f['NombreCliente']),
				number_format($f['MontoLocal'],2,',','.'),
				utf8_decode($f['Comentarios'])
				]);
	$pdf->Ln(1);
	##	
	$SubMontoLocal += $f['MontoLocal'];
}
$pdf->SetDrawColor(0,0,0);
$pdf->Line(135, $pdf->GetY(), 165, $pdf->GetY());
$pdf->SetFont('Arial','B',6);
$pdf->SetX(135); $pdf->Cell(30, 6, number_format($SubMontoLocal,2,',','.'), 0, 1, 'R');
$pdf->SetDrawColor(0,0,0);
$pdf->Line(15, $pdf->GetY(), 205, $pdf->GetY());
##	
$pdf->SetFont('Arial','',7);
$pdf->Cell(25, 5, utf8_decode('Preparado Por'), 0, 0, 'L');
$pdf->Cell(45, 5, utf8_decode($field['NomPreparadoPor']), 0, 0, 'L');
$pdf->Cell(45, 5, utf8_decode($field['FechaPreparado']), 0, 1, 'L');
$pdf->Cell(25, 5, utf8_decode('Aprobado Por'), 0, 0, 'L');
$pdf->Cell(45, 5, utf8_decode($field['NomAprobadoPor']), 0, 0, 'L');
$pdf->Cell(45, 5, utf8_decode($field['FechaAprobado']), 0, 1, 'L');
$pdf->Cell(25, 5, utf8_decode('Estado'), 0, 0, 'L');
$pdf->Cell(45, 5, utf8_decode(printValores('cierre-caja-estado',$field['Estado'])), 0, 1, 'L');
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
