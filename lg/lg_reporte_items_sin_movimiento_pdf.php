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
		$this->Cell(210, 5, utf8_decode('Items sin Movimiento'), 0, 1, 'C', 0);
		$this->Ln(5);
		##
		$this->SetDrawColor(0, 0, 0);
		$this->SetFillColor(200, 200, 200);
		$this->SetWidths(array(20,10,90,10,20,20,20,20));
		$this->SetAligns(array('C','C','L','C','R','C','C','C'));
		$this->SetFont('Arial', 'B', 9);
		$this->Row(array('Item',
						 'Cod.',
						 utf8_decode('Descripción'),
						 'Und.',
						 'Stock',
						 'Linea',
						 'Familia',
						 'SubFamilia'));
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
$sql = "SELECT
			i.CodItem,
			i.CodInterno,
			i.Descripcion,
			i.CodUnidad,
			i.CodLinea,
			i.CodFamilia,
			i.CodSubFamilia,
			iai.StockActual
		FROM
			lg_itemmast i
			INNER JOIN lg_itemalmaceninv iai ON (iai.CodItem = i.CodItem)
			INNER JOIN lg_almacenmast a ON (a.CodAlmacen = iai.CodAlmacen)
		WHERE
			i.Estado = 'A' AND
			iai.CodAlmacen = '".$fCodAlmacen."' AND
			a.CodOrganismo = '".$fCodOrganismo."' AND
			i.CodItem NOT IN (SELECT td.CodItem
							  FROM
							  	lg_transacciondetalle td 
								INNER JOIN lg_transaccion t ON (t.CodOrganismo = td.CodOrganismo AND
																t.CodDocumento = td.CodDocumento AND
																t.NroDocumento = td.NroDocumento)
								WHERE
									td.CodItem = i.CodItem AND
									td.CodOrganismo = a.CodOrganismo AND
									t.Periodo >= '".$fPeriodoD."' AND
									t.Periodo <= '".$fPeriodoH."')
			$filtro
		GROUP BY CodItem
		ORDER BY CodInterno";
$field = getRecords($sql);
foreach($field as $f) {	++$i;
	if ($i % 2 == 0) { $pdf->SetFillColor(240, 240, 240); $pdf->SetDrawColor(240, 240, 240);  }
	else { $pdf->SetFillColor(255, 255, 255); $pdf->SetDrawColor(255, 255, 255); }
	$pdf->SetFont('Arial', '', 9);
	$pdf->Row(array($f['CodItem'],
					$f['CodInterno'],
					utf8_decode($f['Descripcion']),
					$f['CodUnidad'],
					number_format($f['StockActual'], 2, ',', '.'),
					$f['CodLinea'],
					$f['CodFamilia'],
					$f['CodSubFamilia']));
	$pdf->Ln(1);
}
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
