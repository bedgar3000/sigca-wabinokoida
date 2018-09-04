<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$titulo = true;
$jobs = 0;
##	arqueo
$sql = "SELECT *
		FROM co_arqueocaja ac
		WHERE ac.CodArqueo = '$sel_registros'";
$field = getRecord($sql);
##	cobranza (detalle)
$sql = "SELECT
			cbd.*,
			cbd.CtaBancariaPropia AS NroCuentaPropia,
			cb.NroCobranza,
			cb.FechaCobranza,
			bco1.Banco,
			tp.Descripcion AS TipoPago
		FROM co_cobranzadet cbd
		INNER JOIN co_cobranza cb ON cb.CodCobranza = cbd.CodCobranza
		LEFT JOIN ap_ctabancaria cb1 ON cb1.NroCuenta = cbd.CtaBancariaPropia
		LEFT JOIN mastbancos bco1 ON bco1.CodBanco = cb1.CodBanco
		LEFT JOIN co_tipopago tp On tp.CodTipoPago = cbd.CodTipoPago
		WHERE cbd.CodArqueo = '$field[CodArqueo]'
		ORDER BY CtaBancariaPropia, CodTipoPago, FechaCobranza, Secuencia";
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
		if ($field['Estado'] == 'AN' || $field['Estado'] == 'PR')
		{
			$txt = printValores('arqueo-caja-estado',$field['Estado']);

			$this->SetFont('Arial','B',50);
			$this->SetTextColor(255,232,243);
			$this->RotatedText(50,160,utf8_decode(mb_strtoupper($txt)),45);
			$this->SetTextColor(0,0,0);
		}
		##	
		$this->SetFont('Arial','B',9);
		$this->Cell(150, 4, utf8_decode($field_organismo['Organismo']), 0, 0, 'L');
		$this->SetFont('Arial','',8);
		$this->SetX(175); $this->Cell(30, 4, utf8_decode('Fecha:  ' . date('d-m-Y')), 0, 1, 'L');
		$this->SetX(175); $this->Cell(30, 4, utf8_decode('Página: ' . $this->PageNo() . ' de {nb}'), 0, 1, 'L');
		$this->Ln(3);
		$this->SetFont('Arial','B',9);
		$this->Cell(190, 4, utf8_decode('ARQUEO DE CAJA Nº: ' . $field['NroArqueo']), 0, 0, 'C');
		$this->Ln(10);
		##	
		$this->SetFont('Arial','B',8);
		$this->Cell(22, 6, utf8_decode('Fecha Arqueo: '), 0, 0, 'L');
		$this->SetFont('Arial','',8);
		$this->Cell(25, 6, formatFechaDMA($field['Fecha']), 0, 0, 'L');

		$this->SetFont('Arial','B',8);
		$this->Cell(26, 6, utf8_decode('Transacción CxP: '), 0, 0, 'L');
		$this->SetFont('Arial','',8);
		$this->Cell(10, 6, $field['NroTransaccionCxP'], 0, 0, 'L');

		$this->SetFont('Arial','B',8);
		$this->Cell(14, 6, utf8_decode('Voucher: '), 0, 0, 'L');
		$this->SetFont('Arial','',8);
		$this->Cell(40, 6, $field['VoucherNro'] . ' ' . $field['VoucherPeriodo'], 0, 0, 'L');

		$this->SetFont('Arial','B',8);
		$this->Cell(14, 6, utf8_decode('Estado: '), 0, 0, 'L');
		$this->SetFont('Arial','',8);
		$this->Cell(30, 6, utf8_decode(printValores('arqueo-caja-estado',$field['Estado'])), 0, 0, 'L');
		$this->Ln(10);
		##	
		if ($titulo)
		{
			$this->SetFillColor(200,200,200);
			$this->SetFont('Arial','B',6);
			$this->SetWidths([27,50,26,30,27,30]);
			$this->SetAligns(['C','L','L','L','C','R']);
			$this->SetFill('F');
			$this->Row([utf8_decode('Cta. Bancaria Depósito'),
						utf8_decode('Banco'),
						utf8_decode('Tipo Pago'),
						utf8_decode('Doc. Referencia'),
						utf8_decode('Cta. Cliente'),
						utf8_decode('Monto Local')
						]);
			$this->SetWidths([27,50,26,30,27,30]);
			$this->SetAligns(['C','L','L','L','C','R']);
			$this->Ln(1);
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
$TotalMontoLocal = 0;
foreach ($field_detalle as $f)
{
	$pdf->SetFillColor(255,255,255);
	$pdf->SetDrawColor(255,255,255);
	$pdf->SetFont('Arial','',6);
	$pdf->SetWidths([27,50,26,30,27,30]);
	$pdf->SetAligns(['C','L','L','L','C','R']);
	$pdf->SetHeights([3,3,3]);
	$pdf->Row([$f['CtaBancariaPropia'],
				utf8_decode($f['Banco']),
				utf8_decode($f['TipoPago']),
				utf8_decode($f['ArqueoDocReferencia']),
				$f['CtaBancaria'],
				number_format($f['MontoLocal'],2,',','.')
				]);
	$pdf->Ln(1);
	##	
	$TotalMontoLocal += $f['MontoLocal'];
}
$pdf->SetDrawColor(0,0,0);
$pdf->Line(15, $pdf->GetY(), 205, $pdf->GetY());
$pdf->SetFont('Arial','B',6);
$pdf->Cell(160, 6, 'Totales: ', 0, 0, 'R');
$pdf->Cell(30, 6, number_format($TotalMontoLocal,2,',','.'), 0, 0, 'R');
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>