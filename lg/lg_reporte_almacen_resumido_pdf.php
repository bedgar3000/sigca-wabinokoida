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
if ($fCodItem != "") $filtro.= " AND (CodItem = '".$fCodItem."')";
if ($fCodLinea != "") $filtro.= " AND (CodLinea = '".$fCodLinea."')";
if ($fCodFamilia != "") $filtro.= " AND (CodFamilia = '".$fCodFamilia."')";
if ($fCodSubFamilia != "") $filtro.= " AND (CodSubFamilia = '".$fCodSubFamilia."')";
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
		$this->Cell(210, 5, utf8_decode('Movimiento de Almacén Resumido'), 0, 1, 'C', 0);
		if ($fPeriodoD != $fPeriodoH) $Periodo = getNombreMes($fPeriodoD)." ".substr($fPeriodoD, 0, 4)." A ".getNombreMes($fPeriodoH)." ".substr($fPeriodoH, 0, 4);
		else $Periodo = getNombreMes($fPeriodoD)." ".substr($fPeriodoD, 0, 4);
		$this->Cell(210, 5, utf8_decode(strtoupper($Periodo)), 0, 1, 'C', 0);
		$this->Ln(5);
		##
		$this->SetDrawColor(0, 0, 0);
		$this->SetFillColor(200, 200, 200);
		$this->SetWidths(array(20,10,90,10,20,20,20,20));
		$this->SetAligns(array('C','C','L','C','R','R','R','R'));
		$this->SetFont('Arial', 'B', 9);
		$this->Row(array('Item',
						 'Cod.',
						 utf8_decode('Descripción'),
						 'Und.',
						 'Stock Inici.',
						 'Ingresos',
						 'Egresos',
						 'Stock Final'));
		$this->Ln(1);
	}
	
	//	Pie de página.
	function Footer() {
		global $_PARAMETRO;
		global $nl;
		//	obtengo las firmas
		list($_PREPARADO['Nombre'], $_PREPARADO['Cargo'], $_PREPARADO['Nivel']) = getFirma($_PARAMETRO['FIRMALG1']);
		list($_REVISADO['Nombre'], $_REVISADO['Cargo'], $_REVISADO['Nivel']) = getFirmaxDependencia($_PARAMETRO['DEPLOGCXP']);
		list($_CONFORMADO['Nombre'], $_CONFORMADO['Cargo'], $_CONFORMADO['Nivel']) = getFirma($_PARAMETRO['FIRMALG2']);
		$Preparado = " $_PREPARADO[Nivel] $_PREPARADO[Nombre] $nl $_PREPARADO[Cargo]";
		$Revisado = " $_REVISADO[Nivel] $_REVISADO[Nombre] $nl $_REVISADO[Cargo]";
		$Conformado = " $_CONFORMADO[Nivel] $_CONFORMADO[Nombre] $nl $_CONFORMADO[Cargo]";
		##
		$this->SetDrawColor(0, 0, 0);
		$this->SetFillColor(255, 255, 255);
		$this->Rect(3, 245, 210, 25, 'DF');
		$this->Rect(73, 245, 0.1, 25, 'DF');
		$this->Rect(143, 245, 0.1, 25, 'DF');
		$this->Rect(3, 250, 210, 0.1, 'DF');
		##
		$this->SetFont('Arial', 'B', 8);
		$this->SetXY(3, 245); $this->Cell(70, 5, utf8_decode('PREPARADO POR'), 0, 1, 'L', 0);
		$this->SetXY(73, 245); $this->Cell(70, 5, utf8_decode('REVISADO POR'), 0, 1, 'L', 0);
		$this->SetXY(143, 245); $this->Cell(70, 5, utf8_decode('CONFORMADO POR'), 0, 1, 'L', 0);
		##
		$this->SetXY(3, 250); $this->MultiCell(70, 5, utf8_decode($Preparado), 0, 'L');
		$this->SetXY(73, 250); $this->MultiCell(70, 5, utf8_decode($Revisado), 0, 'L');
		$this->SetXY(143, 250); $this->MultiCell(70, 5, utf8_decode($Conformado), 0, 'L');
	}
}
//---------------------------------------------------

//---------------------------------------------------
//	Creación del objeto de la clase heredada.
$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->SetMargins(3, 3, 3);
$pdf->SetAutoPageBreak(1, 35);
$pdf->AddPage();
//---------------------------------------------------
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetDrawColor(255, 255, 255);
//	consulto
$i = 0;
if ($fFlagMostrarTodos == "S") {
	$sql = "(SELECT
				CodItem,
				CodInterno,
				Descripcion,
				CodUnidad,
				SUM(Ingresos) AS Ingresos,
				SUM(Egresos) AS Egresos
			 FROM vw_transacciones_almacen_periodo
			 WHERE
				CodAlmacen = '".$fCodAlmacen."' AND
				Periodo >= '".$fPeriodoD."' AND
				Periodo <= '".$fPeriodoH."'
				$filtro
			 GROUP BY CodItem)
			UNION
			(SELECT
				i.CodItem,
				i.CodInterno,
				i.Descripcion,
				i.CodUnidad,
				'0.00' As Ingresos,
				'0.00' As Egresos
			 FROM
			 	lg_itemmast i
				INNER JOIN lg_itemalmaceninv iai ON (iai.CodItem = i.CodItem)
			 WHERE
				iai.CodAlmacen = '".$fCodAlmacen."' AND
			 	i.Estado = 'A' AND
				i.CodItem NOT IN (
					SELECT CodItem
					 FROM vw_transacciones_almacen_periodo
					 WHERE
						CodAlmacen = '".$fCodAlmacen."' AND
						Periodo >= '".$fPeriodoD."' AND
						Periodo <= '".$fPeriodoH."'
						$filtro
					 GROUP BY CodItem
				)
			)
			ORDER BY CodInterno";
} else {
	$sql = "SELECT
				CodItem,
				CodInterno,
				Descripcion,
				CodUnidad,
				SUM(Ingresos) AS Ingresos,
				SUM(Egresos) AS Egresos
			FROM vw_transacciones_almacen_periodo
			WHERE
				CodAlmacen = '".$fCodAlmacen."' AND
				Periodo >= '".$fPeriodoD."' AND
				Periodo <= '".$fPeriodoH."'
				$filtro
			GROUP BY CodItem
			ORDER BY CodInterno";
}
$field = getRecords($sql);
foreach($field as $f) {	++$i;
	##	stock inicial
	$sql = "SELECT (SUM(Ingresos) - SUM(Egresos))
			FROM vw_transacciones_almacen_periodo
			WHERE
				CodAlmacen = '".$fCodAlmacen."' AND
				Periodo < '".$fPeriodoD."' AND
				CodItem = '".$f['CodItem']."'";
	$StockInicial = getVar3($sql);
	##	stock final
	$StockFinal = $StockInicial + $f['Ingresos'] - $f['Egresos'];
	##
	if ($i % 2 == 0) { $pdf->SetFillColor(240, 240, 240); $pdf->SetDrawColor(240, 240, 240);  }
	else { $pdf->SetFillColor(255, 255, 255); $pdf->SetDrawColor(255, 255, 255); }
	$pdf->SetFont('Arial', '', 9);
	$pdf->Row(array($f['CodItem'],
					$f['CodInterno'],
					utf8_decode($f['Descripcion']),
					$f['CodUnidad'],
					number_format($StockInicial, 2, ',', '.'),
					number_format($f['Ingresos'], 2, ',', '.'),
					number_format($f['Egresos'], 2, ',', '.'),
					number_format($StockFinal, 2, ',', '.')));
	$pdf->Ln(1);
}
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
