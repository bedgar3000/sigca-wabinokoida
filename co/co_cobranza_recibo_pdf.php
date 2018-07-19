<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$titulo = true;
$jobs = 0;
##	cobranza
$sql = "SELECT
			co.*,
			p1.DocFiscal AS DocFiscalCliente,
			p1.NomCompleto AS NombreCliente,
			cj1.CodCajero,
			cj2.CodCajero AS CodCobrador,
			(SELECT SUM(cod2.MontoLocal) FROM co_cobranzadet cod2 WHERE cod2.CodCobranza = co.CodCobranza) AS TotalCobranza
		FROM co_cobranza co
		INNER JOIN mastpersonas p1 ON p1.CodPersona = co.CodPersonaCliente
		LEFT JOIN co_cajeros cj1 ON cj1.CodPersona = co.CodPersonaCajero
		LEFT JOIN co_cajeros cj2 ON cj2.CodPersona = co.CodPersonaCobrador
		LEFT JOIN mastpersonas p2 ON p2.CodPersona = cj1.CodPersona
		LEFT JOIN mastpersonas p3 ON p3.CodPersona = cj2.CodPersona
		WHERE co.CodCobranza = '$sel_registros'";
$field = getRecord($sql);
##	documentos
$sql = "SELECT
			do.*,
			td.Descripcion AS TipoDocumento,
			sf.CodSerie,
			sf.NroSerie,
			md1.Descripcion AS NomFormaFactura,
			md2.Descripcion AS NomTipoVenta
		FROM co_documentocobranza doco
		INNER JOIN co_documento do ON do.CodDocumento = doco.CodDocumento
		INNER JOIN co_tipodocumento td ON td.CodTipoDocumento = do.CodTipoDocumento
		INNER JOIN co_establecimientofiscal ef ON ef.CodOrganismo = do.CodOrganismo
		INNER JOIN co_seriefiscal sf ON (
			sf.CodOrganismo = ef.CodOrganismo
			AND sf.CodEstablecimiento = ef.CodEstablecimiento
		)
		LEFT JOIN mastmiscelaneosdet md1 ON (
			md1.CodDetalle = do.FormaFactura
			AND md1.CodMaestro = 'FORMAFACT'
		)
		LEFT JOIN mastmiscelaneosdet md2 ON (
			md2.CodDetalle = do.TipoVenta
			AND md2.CodMaestro = 'TIPOVENTA'
		)
		WHERE doco.CodCobranza = '$field[CodCobranza]'";
$field_documento = getRecords($sql);
##	cobranza (detalle)
$sql = "SELECT
			cod.*,
			tp.Descripcion AS TipoPago,
			bc.Banco
		FROM co_cobranzadet cod
		INNER JOIN co_tipopago tp ON tp.CodTipoPago = cod.CodTipoPago
		LEFT JOIN mastbancos bc ON bc.CodBanco = cod.CodBanco
		WHERE cod.CodCobranza = '$field[CodCobranza]'";
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
		if ($field['Estado'] != 'AP')
		{
			$txt = printValores('cobranza-estado',$field['Estado']);

			$this->SetFont('Arial','B',50);
			$this->SetTextColor(255,232,243);
			$this->RotatedText(50,190,utf8_decode(mb_strtoupper($txt)),45);
			$this->SetTextColor(0,0,0);
		}
		##	
		$this->SetFont('Arial','B',9);
		$this->Cell(100, 4, utf8_decode($field_organismo['Organismo']), 0, 0, 'L');
		$this->SetFont('Arial','B',9);
		$this->Cell(88, 4, utf8_decode('RECIBO DE COBRANZA Nº ' . $field['NroCobranza']), 0, 0, 'R');
		$this->Ln(10);
		$this->SetFont('Arial','B',10);
		$this->Cell(140, 4, utf8_decode('IMPORTE BS.'), 0, 0, 'R');
		$this->Cell(48, 4, number_format($field['TotalCobranza'],2,',','.'), 0, 1, 'R');
		$this->SetDrawColor(0,0,0);
		$this->Line(158, $this->GetY(), 203, $this->GetY());
		$this->Ln(6);

		/*$this->SetFont('Arial','',8);
		$this->Cell(30, 4, utf8_decode('Fecha:  ' . date('d-m-Y')), 0, 1, 'L');
		$this->SetFont('Arial','',8);
		$this->SetX(175); $this->Cell(30, 4, utf8_decode('Página: ' . $this->PageNo() . ' de {nb}'), 0, 1, 'L');
		$this->Ln(5);
		$this->SetFont('Arial','B',10);
		$this->Cell(188, 4, utf8_decode('COBRANZA Nº ' . $field['NroCobranza']), 0, 0, 'C');
		$this->Ln(10);
		$this->SetFont('Arial','B',8);
		$this->Cell(25, 5, utf8_decode('Cliente: '), 0, 0, 'L');
		$this->Cell(103, 5, utf8_decode($field['NombreCliente']), 0, 1, 'L');
		$this->SetFont('Arial','',8);
		$this->Cell(25, 5, utf8_decode('Doc. Fiscal: '), 0, 0, 'L');
		$this->Cell(90, 5, utf8_decode($field['DocFiscalCliente']), 0, 0, 'L');
		$this->SetFont('Arial','',8);
		$this->Cell(15, 5, utf8_decode('Cobrador: '), 0, 0, 'L');
		$this->Cell(35, 5, utf8_decode($field['CodCobrador']), 0, 1, 'L');
		$this->SetFont('Arial','',8);
		$this->Cell(25, 5, utf8_decode('Fecha Cobranza: '), 0, 0, 'L');
		$this->Cell(90, 5, formatFechaDMA($field['FechaCobranza']), 0, 0, 'L');
		$this->SetFont('Arial','',8);
		$this->Cell(15, 5, utf8_decode('Cajero: '), 0, 0, 'L');
		$this->Cell(35, 5, utf8_decode($field['CodCajero']), 0, 1, 'L');
		$this->Cell(115, 5, '', 0, 0, 'L');
		$this->Cell(15, 5, utf8_decode('Estado: '), 0, 0, 'L');
		$this->Cell(35, 5, utf8_decode(mb_strtoupper(printValores('cobranza-estado',$field['Estado']))), 0, 1, 'L');
		$this->Ln(1);*/
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
##	documentos
$pdf->SetFont('Arial','B',6);
$pdf->SetX(130); $pdf->Cell(58, 6, utf8_decode('FORMA DE PAGO'), 0, 1, 'L');
##	
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','B',6);
$pdf->SetWidths([8,35,30]);
$pdf->SetAligns(['C','L','R']);
$pdf->SetFill('F');
$pdf->SetX(130);
$pdf->Row([utf8_decode('#'),
			utf8_decode('TIPO DE PAGO'),
			utf8_decode('MONTO')
			]);
$pdf->Ln(1);
$i = 0;
$MontoLocal = 0;
foreach ($field_detalle as $f)
{
	$MontoLocal += $f['MontoLocal'];
	##	
	$pdf->SetFillColor(255,255,255);
	$pdf->SetDrawColor(255,255,255);
	$pdf->SetFont('Arial','',6);
	$pdf->SetWidths([8,35,30]);
	$pdf->SetAligns(['C','L','R']);
	$pdf->SetX(130);
	$pdf->Row([++$i,
				utf8_decode($f['TipoPago']),
				number_format($f['MontoLocal'],2,',','.')
				]);
}
$y1 = $pdf->GetY();
##	
$pdf->SetY(42);
$pdf->SetFont('Arial','',7);
$pdf->Cell(15, 4, utf8_decode('Recibi de'), 0, 0, 'L');
$pdf->SetFont('Arial','BU',7);
$pdf->MultiCell(95, 4, utf8_decode($field['NombreCliente']), 0, 'L');
$pdf->Ln(2);
$pdf->SetFont('Arial','',7);
$pdf->Cell(20, 4, utf8_decode('La cantidad de'), 0, 0, 'L');
$pdf->MultiCell(90, 4, utf8_decode(mb_strtoupper(convertir_a_letras($field['TotalCobranza'], "moneda"))), 0, 'L');
$y2 = $pdf->GetY();
##	documentos
$y = (($y1 > $y2) ? $y1 : $y2) + 5;
$pdf->SetY($y);
$pdf->SetFont('Arial','',7);
$pdf->Cell(188, 4, utf8_decode('Por concepto de: Cancelación de los siguientes documentos'), 0, 1, 'L');
##	
$pdf->SetDrawColor(0,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','',7);
$pdf->SetWidths([20,20,20,30,30,70]);
$pdf->SetAligns(['C','C','C','R','R','L']);
$pdf->SetFill('F');
$pdf->Row([utf8_decode('DOCUMENTO'),
			utf8_decode('FECHA EMISIÓN'),
			utf8_decode('FECHA VENCIMIENTO'),
			utf8_decode('MONTO TOTAL'),
			utf8_decode('MONTO PAGADO'),
			utf8_decode('COMENTARIOS')
			]);
$pdf->Ln(1);
$i = 0;
$MontoTotal = 0;
$MontoPagado = 0;
foreach ($field_documento as $f)
{
	++$i;
	$MontoTotal += $f['MontoTotal'];
	$MontoPagado += $f['MontoPagado'];
	##	
	$pdf->SetFillColor(255,255,255);
	$pdf->SetDrawColor(255,255,255);
	$pdf->SetFont('Arial','',7);
	$pdf->SetWidths([20,20,20,30,30,70]);
	$pdf->SetAligns(['C','C','C','R','R','L']);
	$pdf->Row([utf8_decode($f['CodTipoDocumento'].$f['NroDocumento']),
				formatFechaDMA($f['FechaDocumento']),
				formatFechaDMA($f['FechaVencimiento']),
				number_format($f['MontoTotal'],2,',','.'),
				number_format($f['MontoPagado'],2,',','.'),
				utf8_decode($f['Comentarios'])
				]);
	##	
	$pdf->Ln(1);
}
$pdf->SetDrawColor(0,0,0);
$pdf->Line(15, $pdf->GetY(), 203, $pdf->GetY());
$pdf->Ln(1);
$pdf->SetFont('Arial','B',7);
$pdf->Cell(60, 5, utf8_decode('Totales'), 0, 0, 'R');
$pdf->Cell(30, 5, number_format($MontoTotal,2,',','.'), 0, 0, 'R');
$pdf->Cell(30, 5, number_format($MontoPagado,2,',','.'), 0, 1, 'R');
$pdf->Ln(10);
##	
$pdf->SetFont('Arial','',7);
$pdf->SetX(50); $pdf->Cell(20, 4, formatFechaDMA($field['FechaCobranza']), 0, 0, 'C');
$pdf->Ln(5);
$pdf->SetX(50);
$pdf->Cell(20, 4, utf8_decode('FECHA'), 0, 0, 'C');
$pdf->Cell(40, 4);
$pdf->Cell(50, 4, utf8_decode('CAJA'), 0, 0, 'C');
$pdf->SetDrawColor(0,0,0);
$pdf->Line(43, $pdf->GetY(), 77, $pdf->GetY());
$pdf->Line(100, $pdf->GetY(), 170, $pdf->GetY());
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
