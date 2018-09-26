<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$titulo = true;
##	filtro
$filtro = '';
if ($fBuscar != "") {
	$filtro .= " AND (rv.Periodo LIKE '%$fBuscar%'
					  OR rv.CodTipoDocumento LIKE '%$fBuscar%'
					  OR rv.NroDocumento LIKE '%$fBuscar%'
					  OR rv.NombreCliente LIKE '%$fBuscar%'
					  OR rv.Voucher LIKE '%$fBuscar%'
					  OR rv.Comentarios LIKE '%$fBuscar%')";
}
if ($fCodOrganismo != "") $filtro.=" AND (rv.CodOrganismo = '$fCodOrganismo')";
if ($fCodTipoDocumento != "") $filtro.=" AND (rv.CodTipoDocumento = '$fCodTipoDocumento')";
if ($fSistemaFuente != "") $filtro.=" AND (rv.SistemaFuente = '$fSistemaFuente')";
if ($fCodPersonaCliente != "") $filtro.=" AND (rv.CodPersonaCliente = '$fCodPersonaCliente')";
if ($fPeriodo != "") $filtro.=" AND (SUBSTRING(rv.FechaDocumento, 1, 7) = '$fPeriodo')";
##	registros
$sql = "SELECT
			SUM(rv.MontoAfecto) AS MontoAfecto,
			SUM(rv.MontoNoAfecto) AS MontoNoAfecto,
			SUM(rv.MontoImpuestoVentas) AS MontoImpuestoVentas,
			SUM(rv.MontoTotal) AS MontoTotal,
			td.CodTipoDocumento,
			td.Descripcion AS TipoDocumento
		FROM co_registroventas rv
		INNER JOIN co_tipodocumento td ON td.CodTipoDocumento = rv.CodTipoDocumento
		WHERE 1 $filtro
		GROUP BY CodTipoDocumento
		ORDER BY CodTipoDocumento, NroDocumento";
$field = getRecords($sql);
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
		WHERE o.CodOrganismo = '$fCodOrganismo'";
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
		$this->SetFont('Arial','B',8);
		$this->Cell(150, 4, utf8_decode($field_organismo['Organismo']), 0, 0, 'L');
		$this->SetFont('Arial','',8);
		$this->SetX(150); $this->Cell(30, 4, utf8_decode('Fecha:  ' . date('d-m-Y')), 0, 1, 'L');
		$this->SetX(150); $this->Cell(30, 4, utf8_decode('Página: ' . $this->PageNo() . ' de {nb}'), 0, 1, 'L');
		$this->Ln(3);
		$this->SetFont('Arial','B',9);
		$this->Cell(190, 4, utf8_decode('REGISTRO DE VENTAS POR TIPO DE DOCUMENTO'), 0, 1, 'C');
		$this->SetFont('Arial','',9);
		$this->Cell(190, 4, utf8_decode('(SUMARIZADO)'), 0, 0, 'C');
		$this->Ln(5);
		##	
		$this->SetFont('Arial','B',8);
		$this->Cell(22, 6, utf8_decode('Periodo: '), 0, 0, 'L');
		$this->SetFont('Arial','',8);
		$this->Cell(25, 6, $fPeriodo, 0, 0, 'L');
		$this->Ln(7);
		##	
		if ($titulo)
		{
			$this->SetFillColor(255,255,255);
			$this->SetFont('Arial','B',6);
			$this->SetWidths([90,25,25,25,25]);
			$this->SetAligns(['L','R','R','R','R']);
			$this->Row([utf8_decode('Tipo de Documento'),
						utf8_decode('Monto Afecto'),
						utf8_decode('No Afecto'),
						utf8_decode('Monto IGV'),
						utf8_decode('Monto Total')
						]);
			$this->SetWidths([90,25,25,25,25]);
			$this->SetAligns(['L','R','R','R','R']);
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
$MontoAfecto = 0;
$MontoNoAfecto = 0;
$MontoImpuestoVentas = 0;
$MontoTotal = 0;
$Grupo = '';
foreach ($field as $f)
{
	$pdf->SetFillColor(255,255,255);
	$pdf->SetDrawColor(255,255,255);
	$pdf->SetFont('Arial','',6);
	$pdf->SetWidths([90,25,25,25,25]);
	$pdf->SetAligns(['L','R','R','R','R']);
	$pdf->Row([utf8_decode($f['CodTipoDocumento'].' '.$f['TipoDocumento']),
				number_format($f['MontoAfecto'],2,',','.'),
				number_format($f['MontoNoAfecto'],2,',','.'),
				number_format($f['MontoImpuestoVentas'],2,',','.'),
				number_format($f['MontoTotal'],2,',','.')
				]);
	$pdf->Ln(1);
	##	
	$MontoAfecto += $f['MontoAfecto'];
	$MontoNoAfecto += $f['MontoNoAfecto'];
	$MontoImpuestoVentas += $f['MontoImpuestoVentas'];
	$MontoTotal += $f['MontoTotal'];
}
$pdf->SetDrawColor(0,0,0);
$pdf->Line(15, $pdf->GetY(), 205, $pdf->GetY());
$pdf->SetFont('Arial','B',6);
$pdf->Cell(90, 6, 'Total Tipo de Documento: ', 0, 0, 'R');
$pdf->Cell(25, 6, number_format($MontoAfecto,2,',','.'), 0, 0, 'R');
$pdf->Cell(25, 6, number_format($MontoNoAfecto,2,',','.'), 0, 0, 'R');
$pdf->Cell(25, 6, number_format($MontoImpuestoVentas,2,',','.'), 0, 0, 'R');
$pdf->Cell(25, 6, number_format($MontoTotal,2,',','.'), 0, 0, 'R');
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>