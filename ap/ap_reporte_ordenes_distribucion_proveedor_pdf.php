<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$LF = 0x0A;
$CR = 0x0D;
$nl = sprintf("%c%c",$CR,$LF);
//---------------------------------------------------
$filtro = "";
if ($fCodOrganismo != "") $filtro.=" AND (do.CodOrganismo = '".$fCodOrganismo."')";
if ($fCodProveedor != "") $filtro.=" AND (do.CodProveedor = '".$fCodProveedor."')";
if ($fcod_partida != "") $filtro.=" AND (do.cod_partida = '".$fcod_partida."')";
if ($fPeriodoDesde != "") $filtro.=" AND (do.Periodo >= '".$fPeriodoDesde."')";
if ($fPeriodoHasta != "") $filtro.=" AND (do.Periodo <= '".$fPeriodoHasta."')";
if ($fEstado != "") $filtro.=" AND (do.Estado = '".$fEstado."')";
//---------------------------------------------------

class PDF extends FPDF {
	//	Cabecera de página.
	function Header() {
		global $_PARAMETRO;
		global $Ahora;
		global $field;
		global $_POST;
		extract($_POST);
		##
		$Logo = getValorCampo("mastorganismos", "CodOrganismo", "Logo", $_SESSION['ORGANISMO_ACTUAL']);
		$NomOrganismo = getValorCampo("mastorganismos", "CodOrganismo", "Organismo", $_SESSION['ORGANISMO_ACTUAL']);
		$NomDependencia = getValorCampo("mastdependencias", "CodDependencia", "Dependencia", $_PARAMETRO["DEPLOGCXP"]);
		##
		$this->SetFillColor(255, 255, 255);
		$this->SetDrawColor(0, 0, 0);
		$this->Image($_PARAMETRO["PATHLOGO"].$Logo, 3, 3, 10, 10);
		$this->SetFont('Arial', '', 8);
		$this->SetXY(12, 3); $this->Cell(100, 5, $NomOrganismo, 0, 1, 'L');
		$this->SetXY(12, 8); $this->Cell(100, 5, $NomDependencia, 0, 0, 'L');
		$this->SetFont('Arial', '', 8);
		$this->SetXY(175, 3); $this->Cell(10, 5, utf8_decode('Fecha: '), 0, 0, 'L');
		$this->Cell(30, 5, formatFechaDMA(substr($Ahora, 0, 10)), 0, 1, 'L');
		$this->SetXY(175, 8); $this->Cell(10, 5, utf8_decode('Página: '), 0, 0, 'L');
		$this->Cell(30, 5, $this->PageNo().' de {nb}', 0, 1, 'L');
		$this->SetFont('Arial', 'B', 10);
		$this->Ln(5);
		$this->Cell(210, 5, utf8_decode('PAGADOS POR ORDENES'), 0, 1, 'C', 0);
		$this->Ln(5);
		##
		$this->SetDrawColor(0, 0, 0);
		$this->SetFillColor(200, 200, 200);
		$this->SetWidths(array(20,120,20,20,30));
		$this->SetAligns(array('C','L','C','C','R'));
		$this->SetFont('Arial', 'B', 6);
		$this->Row(array('Partida',
						 utf8_decode('Denominación'),
						 'Estado',
						 'Periodo',
						 'Monto'));
		$this->Ln(1);
	}
	
	//	Pie de página.
	function Footer() {}
}
//---------------------------------------------------

//---------------------------------------------------
//	Creación del objeto de la clase heredada.
$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->SetMargins(3, 3, 3);
$pdf->SetAutoPageBreak(1, 3);
$pdf->AddPage();
//---------------------------------------------------
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetDrawColor(255, 255, 255);
//	consulto
$i = 0;
$sql = "SELECT
			op.CodProveedor,
			op.Anio,
			op.NroOrden,
			CONCAT(op.Anio, '-', op.NroOrden) AS Documento,
			op.FechaOrdenPago,
			op.Estado AS EstadoOrden,
			op.MontoTotal,
			p.NomCompleto AS NomProveedor,
			p.DocFiscal,
			do.cod_partida,
			do.Periodo,
			do.Monto AS MontoEjecucion,
			do.Estado AS EstadoEjecucion,
			pv.denominacion AS NomPartida,
			o.MontoImpuestoOtros,
			(ABS(o.MontoImpuestoOtros) + op.MontoTotal) AS MontoDistribuido
		FROM
			ap_ordenpago op
			INNER JOIN mastpersonas p ON (p.CodPersona = op.CodProveedor)
			INNER JOIN ap_ordenpagodistribucion do ON (do.CodOrganismo = op.CodOrganismo AND
													   do.Anio = op.Anio AND
													   do.NroOrden = op.NroOrden)
			INNER JOIN pv_partida pv ON (pv.cod_partida = do.cod_partida)
			INNER JOIN ap_obligaciones o ON (o.CodProveedor = op.CodProveedor AND
											 o.CodTipoDocumento = op.CodTipoDocumento AND
											 o.NroDocumento = op.NroDocumento)
		WHERE 1 $filtro
		ORDER BY CodProveedor, FechaOrdenPago";
$field = getRecords($sql);
foreach($field as $f) {	++$i;
	if ($Grupo1 != $f['CodProveedor']) {
		$Grupo1 = $f['CodProveedor'];
		$Grupo2 = "";
		##
		$sql = "SELECT SUM(do.Monto) AS Monto
				FROM
					ap_ordenpago o
					INNER JOIN mastpersonas p ON (p.CodPersona = o.CodProveedor)
					INNER JOIN ap_ordenpagodistribucion do ON (do.CodOrganismo = o.CodOrganismo AND
															   do.Anio = o.Anio AND
															   do.NroOrden = o.NroOrden)
					INNER JOIN pv_partida pv ON (pv.cod_partida = do.cod_partida)
				WHERE o.CodProveedor = '".$f['CodProveedor']."' $filtro
				GROUP BY o.CodProveedor";
		$MontoProveedor = getVar3($sql);
		##
		if ($i > 1) $pdf->Ln(5);
		$pdf->SetFont('Arial', 'B', 6);
		$pdf->SetFillColor(230, 230, 230);
		$pdf->Cell(100, 5, utf8_decode($f['NomProveedor']), 0, 0, 'L', 1);
		$pdf->Cell(30, 5, 'Rif. '.$f['DocFiscal'], 0, 0, 'L', 1);
		$pdf->Cell(50, 5, 'Total Proveedor: ', 0, 0, 'R', 1);
		$pdf->Cell(30, 5, number_format($MontoProveedor, 2, ',', '.'), 0, 1, 'R', 1);
	}
	if ($Grupo2 != $f['Documento']) {
		$Grupo2 = $f['Documento'];
		$pdf->Ln(2);
		$pdf->SetFont('Arial', 'B', 6);
		$pdf->SetFillColor(255, 255, 255);
		$pdf->Cell(30, 5, 'O/P. '.$f['Documento'], 0, 0, 'L');
		$pdf->Cell(30, 5, 'Fecha O/P. '.formatFechaDMA($f['FechaOrdenPago']), 0, 0, 'L');
		$pdf->Cell(25, 5, strtoupper(printValores("ESTADO-ORDEN-PAGO", $f['EstadoOrden'])), 0, 0, 'L');
		$pdf->Cell(20, 5, 'Monto O/P: ', 0, 0, 'R');
		$pdf->Cell(20, 5, number_format($f['MontoTotal'], 2, ',', '.'), 0, 0, 'R');
		$pdf->Cell(20, 5, 'Ret.: ', 0, 0, 'R');
		$pdf->Cell(15, 5, number_format($f['MontoImpuestoOtros'], 2, ',', '.'), 0, 0, 'R');
		$pdf->Cell(20, 5, 'Total: ', 0, 0, 'R');
		$pdf->Cell(30, 5, number_format($f['MontoDistribuido'], 2, ',', '.'), 0, 1, 'R');
	}
	$pdf->SetFont('Arial', '', 6);
	$pdf->SetDrawColor(255, 255, 255);
	$pdf->SetFillColor(255, 255, 255);
	$pdf->Row(array($f['cod_partida'],
					utf8_decode($f['NomPartida']),
					strtoupper(printValores("ESTADO-PAGADO", $f['EstadoEjecucion'])),
					$f['Periodo'],
					number_format($f['MontoEjecucion'], 2, ',', '.')));
}
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
