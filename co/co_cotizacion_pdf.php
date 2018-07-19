<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$titulo = true;
$jobs = 0;
$igv = getVar3("SELECT FactorPorcentaje FROM mastimpuestos WHERE CodImpuesto = '$_PARAMETRO[COIVA]'");
##	cotizacion
$sql = "SELECT
			co.*,
			CONCAT_WS('-', co.NroCotizacion, SUBSTRING(co.Anio, 3, 2)) AS Numero,
			p.Telefono1,
			fp.Descripcion AS FormaPago,
			antiguedad(FechaDocumento, FechaVencimiento) AS TiempoVencimiento
		FROM co_cotizacion co
		INNER JOIN mastpersonas p ON p.CodPersona = co.CodPersonaCliente
		LEFT JOIN mastcliente cl ON cl.CodPersona = co.CodPersonaCliente
		LEFT JOIN mastformapago fp ON fp.CodFormaPago = cl.CodFormaPago
		WHERE co.CodCotizacion = '$sel_registros'";
$field = getRecord($sql);
##	cotizacion (detalle)
$sql = "SELECT
			cod.*
		FROM co_cotizaciondet cod
		WHERE cod.CodCotizacion = '$sel_registros'";
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
		if ($field['Estado'] <> 'AP' && $field['Estado'] <> 'CO')
		{
			$txt = printValores('cotizacion-estado',$field['Estado']);

			$this->SetFont('Arial','B',50);
			$this->SetTextColor(255,232,243);
			$this->RotatedText(50,190,utf8_decode(mb_strtoupper($txt)),45);
			$this->SetTextColor(0,0,0);
		}
		##	
		$this->Image($_PARAMETRO['PATHLOGO'].$field_organismo['Logo'], 60, 5, 20, 15);	
		$this->SetFont('Arial','B',8);
		$this->Cell(115, 6, utf8_decode($field_organismo['Organismo']), 0, 0, 'C');
		$this->Cell(15, 6);
		$this->Cell(10, 6, utf8_decode('Rif.:'), 0, 0, 'R');
		$this->SetFont('Arial','',8);
		$this->Cell(35, 6, set_rif($field_organismo['DocFiscal']), 0, 0, 'L');
		$this->Ln(10);
		##	
		$this->SetY(30);
		$this->SetFont('Arial','',6);
		$this->MultiCell(115, 3, utf8_decode($field_organismo['Comentarios']), 0, 'C');
		$this->Ln(1);
		$this->Cell(115, 3, utf8_decode($field_organismo['Direccion'] . ', ' . ucwords(strtolower($field_organismo['Ciudad'])) . ', Estado ' . ucwords(strtolower($field_organismo['NomEstado'])) . ', Zona Postal ' . $field_organismo['CodPostal']), 0, 1, 'C');
		$this->Cell(115, 3, 'Telfs:' . $field_organismo['Telefono1'], 0, 1, 'C');
		##	
		$this->SetXY(135, 27);
		$this->SetFont('Arial','B',8);
		$this->Cell(46, 5, utf8_decode('Lugar de Emisión'), 1, 0, 'C');
		$this->Cell(8, 5, utf8_decode('Dia'), 1, 0, 'C');
		$this->Cell(8, 5, utf8_decode('Mes'), 1, 0, 'C');
		$this->Cell(8, 5, utf8_decode('Año'), 1, 1, 'C');
		$this->SetXY(135, 32);
		$this->SetFont('Arial','',8);
		$this->Cell(46, 7, utf8_decode($field_organismo['Ciudad']), 1, 0, 'C');
		$this->Cell(8, 7, substr($field['FechaDocumento'], 8, 2), 1, 0, 'C');
		$this->Cell(8, 7, substr($field['FechaDocumento'], 5, 2), 1, 0, 'C');
		$this->Cell(8, 7, substr($field['FechaDocumento'], 0, 4), 1, 1, 'C');
		$this->SetXY(135, 41);
		$this->SetFont('Arial','B',9);
		$this->Cell(35, 4, utf8_decode('COTIZACION Nº:'), 0, 0, 'C');
		$this->SetFont('Arial','',9);
		$this->Cell(35, 4, $field['NroCotizacion'] . '-' . substr($field['Anio'], 2, 2), 0, 0, 'C');
		##	
		$this->SetDrawColor(0,0,0);
		$this->SetY(50);

		$y = $this->GetY();
		$this->SetFont('Arial','B',8);
		$this->Write(4, utf8_decode('NOMBRE O RAZON SOCIAL: '));
		$this->SetFont('Arial','',8);
		$this->Write(4, utf8_decode($field['NombreCliente']));
		$this->Ln(7);
		$this->Rect(15, $y-2, 190, $this->GetY() - $y);

		$y = $this->GetY();
		$this->SetFont('Arial','B',8);
		$this->Write(4, utf8_decode('DOMICILIO FISCAL: '));
		$this->SetFont('Arial','',8);
		$this->Write(4, utf8_decode($field['DireccionCliente']));
		$this->Ln(7);
		$this->Rect(15, $y-2, 190, $this->GetY() - $y);

		$y = $this->GetY();
		$this->SetFont('Arial','B',8);
		$this->Write(4, utf8_decode('TELEFONOS: '));
		$this->SetFont('Arial','',8);
		$this->Write(4, $field['Telefono1'].'                                                                             ');
		$this->SetFont('Arial','B',8);
		$this->Write(4, utf8_decode('RIF./C.I.: '));
		$this->SetFont('Arial','',8);
		$this->Write(4, set_rif($field['DocFiscalCliente']));
		$this->Ln(7);
		$this->Rect(15, $y-2, 190, $this->GetY() - $y);

		$y = $this->GetY();
		$this->SetFont('Arial','B',8);
		$this->Write(4, utf8_decode('CONDICIONES DE PAGO: '));
		$this->SetFont('Arial','',8);
		$this->Write(4, utf8_decode($field['FormaPago']));
		$this->Ln(7);
		$this->Rect(15, $y-2, 190, $this->GetY() - $y);

		$this->SetLineWidth(0.6);
		$this->Rect(15, 48, 190, $this->GetY() - 50);

		$this->SetLineWidth(0.2);
		$this->Ln(2);
		##	
		if ($titulo)
		{
			$this->SetFillColor(255,255,255);
			$this->SetFont('Arial','B',8);
			$this->SetWidths([20,95,15,30,30]);
			$this->SetAligns(['C','C','C','C','C']);
			$this->SetFill('F');
			$this->Row([utf8_decode('CANTIDAD'),
						utf8_decode('DESCRIPCIÓN'),
						utf8_decode('UNIDAD'),
						utf8_decode('PRECIO UNIT.'),
						utf8_decode('TOTAL')
						]);
			$this->SetWidths([20,95,15,30,30]);
			$this->SetAligns(['R','L','C','R','R']);
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
		$this->SetDrawColor(0,0,0);
		$this->SetLineWidth(0.6);
		$this->Rect(15, 80, 190, $this->GetY() - 80 - $jobs);
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
$j=0;
foreach ($field_detalle as $f)
{
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',8);
	$pdf->SetWidths([20,95,15,30,30]);
	$pdf->SetAligns(['R','L','C','R','R']);
	$pdf->SetHeights([6,6,6]);
	$pdf->Row([number_format($f['CantidadPedida'],2,',','.'),
				utf8_decode($f['Descripcion']),
				utf8_decode($f['CodUnidadVenta']),
				number_format($f['PrecioUnit'],2,',','.'),
				number_format($f['MontoTotal'],2,',','.')
				]);
}

/*if ($pdf->GetY() > 223) 
{
	$titulo = false;
	$pdf->AddPage();
}*/

$pdf->SetY(229);

$y1 = $pdf->GetY();
list($a, $m, $d) = explode('-', $field['TiempoVencimiento']);
$dias = ($m * 30) + $d;

$pdf->SetFont('Arial','B',7);
$pdf->Write(4, utf8_decode('OBSERVACIONES: '));
$pdf->Ln(5);
$pdf->SetFont('Arial','',7);
$pdf->MultiCell(130,4, utf8_decode('- El presente presupuesto tendrá una validez de ('.$dias.') días continuos.'));
$pdf->MultiCell(130,4, utf8_decode('- Favor emitir cheque a nombre de: '.$field_organismo['Organismo']));

$pdf->SetXY(145, $y1);
$pdf->SetFont('Arial','B',7);
$pdf->Cell(30, 6, utf8_decode('MONTO AFECTO'), 0, 0, 'L');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(30, 6, number_format($field['MontoAfecto'],2,',','.'), 0, 0, 'R');
$pdf->SetXY(145, $y1+6);
$pdf->SetFont('Arial','B',7);
$pdf->Cell(30, 6, utf8_decode('MONTO NO AFECTO'), 0, 0, 'L');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(30, 6, number_format($field['MontoNoAfecto'],2,',','.'), 0, 0, 'R');
$pdf->SetXY(145, $y1+12);
$pdf->SetFont('Arial','B',7);
$pdf->Cell(30, 6, utf8_decode('IVA ('.number_format($igv,2,',','.').')% SOBRE'), 0, 0, 'L');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(30, 6, number_format($field['MontoImpuesto'],2,',','.'), 0, 0, 'R');
$pdf->SetXY(145, $y1+18);
$pdf->SetFont('Arial','B',7);
$pdf->Cell(30, 6, utf8_decode('TOTAL A PAGAR BS.'), 0, 0, 'L');
$pdf->SetFont('Arial','B',8);
$pdf->SetFillColor(200,200,200);
$pdf->Cell(30, 6, number_format($field['MontoTotal'],2,',','.'), 0, 1, 'R', 1);

$y2 = $pdf->GetY();
$jobs = $y2 - $y1;

$pdf->SetLineWidth(0.2);
$pdf->Rect(15, $y1, 130, $y2 - $y1);
$pdf->Rect(145, $y1, 30, $y2 - $y1);
$pdf->Rect(175, $y1, 30, $y2 - $y1);
$pdf->SetLineWidth(0.6);
$pdf->Rect(15, $y1, 190, $y2 - $y1);
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
