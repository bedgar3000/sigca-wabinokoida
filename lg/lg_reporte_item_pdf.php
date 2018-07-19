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
if ($fBuscar != "") $filtro .= " AND (i.CodItem LIKE '%".$fBuscar."%' OR
									  i.CodInterno LIKE '%".$fBuscar."%' OR
									  i.Descripcion LIKE '%".$fBuscar."%' OR
									  i.CodUnidad LIKE '%".$fBuscar."%' OR
									  i.CodLinea LIKE '%".$fBuscar."%' OR
									  i.CodFamilia LIKE '%".$fBuscar."%' OR
									  i.CodSubFamilia LIKE '%".$fBuscar."%' OR
									  i.CtaGasto LIKE '%".$fBuscar."%' OR
									  i.CtaGastoPub20 LIKE '%".$fBuscar."%' OR
									  i.PartidaPresupuestal LIKE '%".$fBuscar."%' OR
									  ti.Descripcion LIKE '%".$fBuscar."%' OR
									  p.Descripcion)";
if ($fCodUnidad != "") $filtro.=" AND (i.CodUnidad = '".$fCodUnidad."')";
if ($fCodTipoItem != "") $filtro.=" AND (i.CodTipoItem = '".$fCodTipoItem."')";
if ($fEstado != "") $filtro.=" AND (i.Estado = '".$fEstado."')";
if ($fCtaGasto != "") $filtro.=" AND (i.CtaGasto = '".$fCtaGasto."')";
if ($fCodLinea != "") $filtro.=" AND (i.CodLinea = '".$fCodLinea."')";
if ($fCtaGastoPub20 != "") $filtro.=" AND (i.CtaGastoPub20 = '".$fCtaGastoPub20."')";
if ($fCodFamilia != "") $filtro.=" AND (i.CodFamilia = '".$fCodFamilia."')";
if ($fPartidaPresupuestal != "") $filtro.=" AND (i.PartidaPresupuestal = '".$fPartidaPresupuestal."')";
if ($fCodSubFamilia != "") $filtro.=" AND (i.CodSubFamilia = '".$fCodSubFamilia."')";
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
		$this->SetXY(245, 3); $this->Cell(10, 5, utf8_decode('Fecha: '), 0, 0, 'L');
		$this->Cell(30, 5, formatFechaDMA(substr($Ahora, 0, 10)), 0, 1, 'L');
		$this->SetXY(245, 8); $this->Cell(10, 5, utf8_decode('Página: '), 0, 0, 'L');
		$this->Cell(30, 5, $this->PageNo().' de {nb}', 0, 1, 'L');
		$this->SetFont('Arial', 'B', 10);
		$this->Ln(5);
		$this->Cell(272, 5, utf8_decode('Listado de Items'), 0, 1, 'C', 0);
		$this->Ln(5);
		##
		$this->SetDrawColor(0, 0, 0);
		$this->SetFillColor(200, 200, 200);
		$this->SetWidths(array(20,12,60,10,51,20,13,13,13,20,15,25));
		$this->SetAligns(array('C','C','L','C','C','C','C','C','C','C','C','C'));
		$this->SetFont('Arial', 'B', 8);
		$this->Row(array('Item',
						 '#',
						 utf8_decode('Descripción'),
						 'Und.',
						 'Tipo',
						 'Procedencia',
						 'Linea',
						 'Familia',
						 'Sub.F.',
						 'Partida',
						 'Cta. Gasto',
						 'Cta. Gasto (Pub.20)'));
		$this->Ln(1);
	}
	
	//	Pie de página.
	function Footer() {}
}
//---------------------------------------------------

//---------------------------------------------------
//	Creación del objeto de la clase heredada.
$pdf = new PDF('L', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->SetMargins(3, 3, 3);
$pdf->SetAutoPageBreak(1, 0);
$pdf->AddPage();
//---------------------------------------------------
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(255, 255, 255);
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
			i.CtaGasto,
			i.CtaGastoPub20,
			i.PartidaPresupuestal,
			ti.Descripcion AS NomTipoItem,
			p.Descripcion AS NomProcedencia
		FROM
			lg_itemmast i
			INNER JOIN lg_tipoitem ti ON (i.CodTipoItem = ti.CodTipoItem)
			LEFT JOIN lg_procedencias p ON (i.CodProcedencia = p.CodProcedencia)
		WHERE 1 $filtro
		ORDER BY $fOrderBy";
$field = getRecords($sql);
foreach($field as $f) {	++$i;
	if ($i % 2 == 0) { $pdf->SetFillColor(240, 240, 240); $pdf->SetDrawColor(240, 240, 240);  }
	else { $pdf->SetFillColor(255, 255, 255); $pdf->SetDrawColor(255, 255, 255); }
	$pdf->SetFont('Arial', '', 8);
	$pdf->Row(array($f['CodItem'],
					$f['CodInterno'],
					utf8_decode($f['Descripcion']),
					$f['CodUnidad'],
					utf8_decode($f['NomTipoItem']),
					utf8_decode($f['NomProcedencia']),
					$f['CodLinea'],
					$f['CodFamilia'],
					$f['CodSubFamilia'],
					$f['PartidaPresupuestal'],
					$f['CtaGasto'],
					$f['CtaGastoPub20']));
	$pdf->Ln(1);
}
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
