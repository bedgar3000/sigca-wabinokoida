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
		$this->Cell(210, 5, utf8_decode('CONSOLIDADO POR PARTIDAS'), 0, 1, 'C', 0);
		$this->Ln(5);
		##
		$this->SetDrawColor(0, 0, 0);
		$this->SetFillColor(200, 200, 200);
		$this->SetWidths(array(20,160,30));
		$this->SetAligns(array('C','L','R'));
		$this->SetFont('Arial', 'B', 8);
		$this->Row(array('Partida',
						 utf8_decode('Denominación'),
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
//	consulto
$i = 0;
$sql = "SELECT
			cod_partida,
			SUM(Monto) AS Monto,
			denominacion
		FROM
			(SELECT
				do.cod_partida,
				do.Monto,
				p.denominacion
			 FROM
				ap_distribucionobligacion do
				INNER JOIN ap_obligaciones o ON (o.CodProveedor = do.CodProveedor AND
												 o.CodTipoDocumento = do.CodTipoDocumento AND
												 o.NroDocumento = do.NroDocumento)
				INNER JOIN pv_partida p ON (p.cod_partida = do.cod_partida)
			 WHERE 1 $filtro
			 UNION ALL
			 SELECT
				do.cod_partida,
				do.Monto,
				p.denominacion
			 FROM
				ap_distribucionobligacion do
				INNER JOIN ap_bancotransaccion o ON (o.CodProveedor = do.CodProveedor AND
													 o.CodTipoDocumento = do.CodTipoDocumento AND
													 o.CodigoReferenciaBanco = do.NroDocumento)
				INNER JOIN pv_partida p ON (p.cod_partida = do.cod_partida)
			 WHERE 1 $filtro) AS Causadas
		GROUP BY cod_partida
		ORDER BY cod_partida";
$field = getRecords($sql);
foreach($field as $f) {	++$i;
	if ($fFlagMostrar == "S") $pdf->SetFont('Arial', 'B', 8); else $pdf->SetFont('Arial', '', 8);
	$pdf->SetDrawColor(255, 255, 255);
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetWidths(array(20,160,30));
	$pdf->SetAligns(array('C','L','R'));
	$pdf->Row(array($f['cod_partida'],
					utf8_decode($f['denominacion']),
					number_format($f['Monto'], 2, ',', '.')));
	if ($fFlagMostrar == "S") {
		$sql = "(SELECT
					do.CodTipoDocumento,
					do.NroDocumento,
					do.Monto,
					o.FechaRegistro,
					p.NomCompleto AS NomProveedor,
					'OB' AS Origen
				 FROM
					ap_distribucionobligacion do
					INNER JOIN ap_obligaciones o ON (o.CodProveedor = do.CodProveedor AND
													 o.CodTipoDocumento = do.CodTipoDocumento AND
													 o.NroDocumento = do.NroDocumento)
					INNER JOIN mastpersonas p ON (p.CodPersona = o.CodProveedor)
				 WHERE
					do.cod_partida = '".$f['cod_partida']."'
					$filtro)
				UNION
				(SELECT
					do.CodTipoDocumento,
					do.NroDocumento,
					do.Monto,
					o.FechaTransaccion AS FechaRegistro,
					p.NomCompleto AS NomProveedor,
					'TB' AS Origen
				 FROM
					ap_distribucionobligacion do
					INNER JOIN ap_bancotransaccion o ON (o.CodProveedor = do.CodProveedor AND
														 o.CodTipoDocumento = do.CodTipoDocumento AND
														 o.CodigoReferenciaBanco = do.NroDocumento)
					INNER JOIN mastpersonas p ON (p.CodPersona = o.CodProveedor)
				 WHERE
					do.cod_partida = '".$f['cod_partida']."'
					$filtro)
				ORDER BY FechaRegistro";
		$field_obligaciones = getRecords($sql);
		foreach($field_obligaciones as $fo) {
			$pdf->SetFont('Arial', '', 8);
			$pdf->Cell(5, 5);
			$pdf->Cell(40, 5, $fo['CodTipoDocumento'].'-'.$fo['NroDocumento'], 0, 0, 'L');
			$pdf->Cell(105, 5, utf8_decode($fo['NomProveedor']), 0, 0, 'L');
			$pdf->Cell(30, 5, formatFechaDMA($fo['FechaRegistro']), 0, 0, 'C');
			$pdf->Cell(30, 5, number_format($fo['Monto'], 2, ',', '.'), 0, 1, 'R');
		}
	}
	$pdf->Ln(1);
	##
	$Total += $f['Monto'];
}
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(180, 5, 'TOTAL DISTRIBUIDO', 0, 0, 'R');
$pdf->SetFont('Arial', 'BU', 8);
$pdf->Cell(30, 5, number_format($Total, 2, ',', '.'), 0, 1, 'R', 1);
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
