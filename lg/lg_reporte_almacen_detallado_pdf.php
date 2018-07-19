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
if ($fCodItem != "") $filtro.= " AND (i.CodItem = '".$fCodItem."')";
if ($fCodLinea != "") $filtro.= " AND (i.CodLinea = '".$fCodLinea."')";
if ($fCodFamilia != "") $filtro.= " AND (i.CodFamilia = '".$fCodFamilia."')";
if ($fCodSubFamilia != "") $filtro.= " AND (i.CodSubFamilia = '".$fCodSubFamilia."')";
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
		$this->Cell(210, 5, utf8_decode('Transacciones por Item'), 0, 1, 'C', 0);
		$this->Ln(5);
		##
		$this->SetDrawColor(0, 0, 0);
		$this->SetFillColor(200, 200, 200);
		$this->SetWidths(array(30,100,30,30,20));
		$this->SetAligns(array('C','L','C','C','R'));
		$this->SetFont('Arial', 'B', 9);
		$this->Row(array('Fecha',
						 utf8_decode('Transacción'),
						 'Documento',
						 'Referencia',
						 'Cantidad'));
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
$pdf->SetAutoPageBreak(1, 5);
$pdf->AddPage();
//---------------------------------------------------
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetDrawColor(255, 255, 255);
//	consulto
$i = 0;
$sql = "(SELECT
			i.CodItem,
			i.Descripcion,
			t.FechaDocumento,
			t.CodTransaccion,
			t.CodDocumento,
			t.NroDocumento,
			t.NroInterno,
			tt.Descripcion AS NomTransaccion,
			td.ReferenciaCodDocumento,
			td.ReferenciaNroDocumento,
			td.CantidadRecibida,
			'Egresos' AS Tipo
		 FROM
			lg_transaccion t
			INNER JOIN lg_transacciondetalle td ON (t.CodOrganismo = td.CodOrganismo AND
													t.CodDocumento = td.CodDocumento AND
													t.NroDocumento = td.NroDocumento)
			INNER JOIN lg_tipotransaccion tt ON (t.CodTransaccion = tt.CodTransaccion)
			INNER JOIN lg_itemmast i ON (td.CodItem = i.CodItem AND i.Estado = 'A')
		 WHERE
		 	t.Estado = 'CO' AND
			t.CodAlmacen = '".$fCodAlmacen."' AND
			t.Periodo >= '".$fPeriodoD."' AND
			t.Periodo <= '".$fPeriodoH."' AND
		 	td.Estado = 'CO' AND
			tt.TipoMovimiento = 'E'
			$filtro)
		UNION
		(SELECT
			i.CodItem,
			i.Descripcion,
			t.FechaDocumento,
			t.CodTransaccion,
			t.CodDocumento,
			t.NroDocumento,
			t.NroInterno,
			tt.Descripcion AS NomTransaccion,
			td.ReferenciaCodDocumento,
			td.ReferenciaNroDocumento,
			td.CantidadRecibida,
			'Ingresos' AS Tipo
		 FROM
			lg_transaccion t
			INNER JOIN lg_transacciondetalle td ON (t.CodOrganismo = td.CodOrganismo AND
													t.CodDocumento = td.CodDocumento AND
													t.NroDocumento = td.NroDocumento)
			INNER JOIN lg_tipotransaccion tt ON (t.CodTransaccion = tt.CodTransaccion)
			INNER JOIN lg_itemmast i ON (td.CodItem = i.CodItem AND i.Estado = 'A')
		 WHERE
		 	t.Estado = 'CO' AND
			t.CodAlmacen = '".$fCodAlmacen."' AND
			t.Periodo >= '".$fPeriodoD."' AND
			t.Periodo <= '".$fPeriodoH."' AND
		 	td.Estado = 'CO' AND
			tt.TipoMovimiento = 'I'
			$filtro)
		ORDER BY CodItem, Tipo, FechaDocumento";
$field = getRecords($sql);
foreach($field as $f) {	++$i;
	if ($Grupo != $f['CodItem']) {
		if ($i > 1) {
			$pdf->SetFont('Arial', 'B', 9);
			$pdf->Cell(190, 5, 'Total de '.$Tipo, 0, 0, 'R');
			$pdf->Cell(20, 5, number_format($Total, 2, ',', '.'), 0, 1, 'R');
			$pdf->Ln(10);
			$Total = 0;
		}
		$Grupo = $f['CodItem'];
		$pdf->SetFont('Arial', 'B', 10);
		$pdf->Cell(30, 5, $f['CodItem'], 0, 0, 'L');
		$pdf->Cell(180, 5, utf8_decode($f['Descripcion']), 0, 1, 'L');		
		$Tipo = "";
	}
	##
	if ($Tipo != $f['Tipo']) {
		if ($i > 1 && $Tipo != "") {
			$pdf->SetFont('Arial', 'B', 9);
			$pdf->Cell(160, 5, 'Total de '.$Tipo, 0, 0, 'R');
			$pdf->Cell(20, 5, number_format($Total, 2, ',', '.'), 0, 1, 'R');
			$Total = 0;
		}
		$Tipo = $f['Tipo'];
		$pdf->SetFont('Arial', 'B', 9);
		$pdf->Cell(210, 5, $f['Tipo'], 0, 1, 'L');
	}
	##
	$pdf->SetFont('Arial', '', 9);
	$pdf->Row(array(formatFechaDMA($f['FechaDocumento']),
					utf8_decode($f['NomTransaccion']),
					$f['CodDocumento'].' '.$f['NroInterno'],
					$f['ReferenciaCodDocumento'].' '.$f['ReferenciaNroDocumento'],
					number_format($f['CantidadRecibida'], 2, ',', '.')));
	$pdf->Ln(1);
	##
	$Total += $f['CantidadRecibida'];
}
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(160, 5, 'Total de '.$Tipo, 0, 0, 'R');
$pdf->Cell(20, 5, number_format($Total, 2, ',', '.'), 0, 1, 'R');
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>