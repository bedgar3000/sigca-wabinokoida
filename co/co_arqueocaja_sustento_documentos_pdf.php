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
			do.*,
			td.Descripcion AS TipoDocumento
		FROM co_cobranzadet cbd
		INNER JOIN co_cobranza cb ON cb.CodCobranza = cbd.CodCobranza
		INNER JOIN co_documentocobranza doco ON doco.CodCobranza = cb.CodCobranza
		INNER JOIN co_documento do ON do.CodDocumento = doco.CodDocumento
		INNER JOIN co_tipodocumento td ON td.CodTipoDocumento = do.CodTipoDocumento
		LEFT JOIN ap_ctabancaria cb1 ON cb1.NroCuenta = cbd.CtaBancariaPropia
		LEFT JOIN mastbancos bco1 ON bco1.CodBanco = cb1.CodBanco
		LEFT JOIN co_tipopago tp On tp.CodTipoPago = cbd.CodTipoPago
		WHERE cbd.CodArqueo = '$field[CodArqueo]'
		GROUP BY CodDocumento
		ORDER BY CodTipoDocumento, NroDocumento";
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
		$this->Cell(160, 4, utf8_decode($field_organismo['Organismo']), 0, 0, 'L');
		$this->SetFont('Arial','',8);
		$this->Cell(30, 4, utf8_decode('Fecha:  ' . date('d-m-Y')), 0, 1, 'L');
		$this->SetFont('Arial','B',8);
		$this->Cell(150, 4, utf8_decode('ARQUEO DE CAJA Nº: ' . $field['NroArqueo']), 0, 0, 'L');
		$this->SetFont('Arial','',8);
		$this->SetX(175); $this->Cell(30, 4, utf8_decode('Página: ' . $this->PageNo() . ' de {nb}'), 0, 1, 'L');
		$this->Ln(3);
		$this->SetFont('Arial','B',9);
		$this->Cell(190, 4, utf8_decode('Documentos considerados en el Arqueo'), 0, 0, 'C');
		$this->Ln(10);
		##	
		if ($titulo)
		{
			$this->SetFillColor(200,200,200);
			$this->SetFont('Arial','B',6);
			$this->SetWidths([20,20,20,80,20,30]);
			$this->SetAligns(['L','C','C','L','L','R']);
			$this->SetFill('F');
			$this->Row([utf8_decode('Documento'),
						utf8_decode('Fecha Emisión'),
						utf8_decode('Fecha Venc.'),
						utf8_decode('Cliente'),
						utf8_decode('Doc. Cliente'),
						utf8_decode('Monto Documento')
						]);
			$this->SetWidths([20,20,20,80,20,30]);
			$this->SetAligns(['L','C','C','L','L','R']);
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
$TotalMontoTotal = 0;
$Grupo = '';
foreach ($field_detalle as $f)
{
	if ($Grupo != $f['CodTipoDocumento'])
	{
		$pdf->SetFont('Arial','B',6);
		$pdf->Cell(190, 4, utf8_decode($f['CodTipoDocumento'].' '.$f['TipoDocumento']), 0, 0, 'L');
		$pdf->Ln(5);
		##	
		$Grupo = $f['CodTipoDocumento'];
	}
	$pdf->SetFillColor(255,255,255);
	$pdf->SetDrawColor(255,255,255);
	$pdf->SetFont('Arial','',6);
	$pdf->SetWidths([20,20,20,80,20,30]);
	$pdf->SetAligns(['L','C','C','L','L','R']);
	$pdf->SetHeights([3,3,3]);
	$pdf->Row([$f['NroDocumento'],
				formatFechaDMA($f['FechaDocumento']),
				formatFechaDMA($f['FechaVencimiento']),
				utf8_decode($f['NombreCliente']),
				$f['DocFiscalCliente'],
				number_format($f['MontoTotal'],2,',','.')
				]);
	$pdf->Ln(1);
	##	
	$TotalMontoTotal += $f['MontoTotal'];
}
$pdf->SetDrawColor(0,0,0);
$pdf->Line(15, $pdf->GetY(), 205, $pdf->GetY());
$pdf->SetFont('Arial','B',6);
$pdf->Cell(160, 6, 'Totales: ', 0, 0, 'R');
$pdf->Cell(30, 6, number_format($TotalMontoTotal,2,',','.'), 0, 0, 'R');
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>