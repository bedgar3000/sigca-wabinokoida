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
//if ($fCodOrganismo != "") $filtro.=" AND (pc.CodOrganismo = '".$fCodOrganismo."')";
//if ($fCodDependencia != "") $filtro.=" AND (pc.CodDependencia = '".$fCodDependencia."')";
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
		$Logo = getValorCampo("mastorganismos", "CodOrganismo", "Logo", $fCodOrganismo);
		$NomOrganismo = getValorCampo("mastorganismos", "CodOrganismo", "Organismo", $fCodOrganismo);
		$NomDependencia = getValorCampo("mastdependencias", "CodDependencia", "Dependencia", $_PARAMETRO["DEPRHPR"]);
		##
		$this->SetFillColor(255, 255, 255);
		$this->SetDrawColor(0, 0, 0);
		$this->Image($_PARAMETRO["PATHLOGO"].$Logo, 3, 3, 10, 10);
		$this->SetFont('Arial', '', 8);
		$this->SetXY(12, 3); $this->Cell(100, 5, $NomOrganismo, 0, 1, 'L');
		$this->SetXY(12, 8); $this->Cell(100, 5, $NomDependencia, 0, 0, 'L');
		$this->SetFont('Arial', '', 8);
		$this->SetXY(185, 3); $this->Cell(10, 5, utf8_decode('Fecha: '), 0, 0, 'L');
		$this->Cell(30, 5, formatFechaDMA(substr($Ahora, 0, 10)), 0, 1, 'L');
		$this->SetXY(185, 8); $this->Cell(10, 5, utf8_decode('Página: '), 0, 0, 'L');
		$this->Cell(30, 5, $this->PageNo().' de {nb}', 0, 1, 'L');
		$this->SetFont('Arial', 'B', 10);
		$this->Ln(5);
		$this->Cell(210, 5, utf8_decode('Pago de Vacaciones'), 0, 1, 'C', 0);
		$this->Ln(5);
		##
		$this->SetDrawColor(0, 0, 0);
		$this->SetFillColor(200, 200, 200);
		$this->SetWidths(array(10,75,25,25,50,25));
		$this->SetAligns(array('R','L','C','R','C','C'));
		$this->SetFont('Arial', 'B', 8);
		$this->Row(array('#',
						 'Concepto',
						 'Periodo',
						 'Dias Pago',
						 utf8_decode('Nro. Obligación'),
						 'Fecha Pago'));
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
$pdf->SetAutoPageBreak(1, 0);
$pdf->AddPage();
//---------------------------------------------------
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetDrawColor(255, 255, 255);
//	consulto
$sql = "SELECT
			vp.Periodo,
			vp.DiasPago,
			vp.CodTipoDocumento,
			vp.NroDocumento,
			vp.FechaPago,
			vpr.Anio,
			c.Descripcion AS NomConcepto,
			e.CodEmpleado,
			p.NomCompleto,
			p.Ndocumento
		FROM
			rh_vacacionpago vp
			INNER JOIN rh_vacacionperiodo vpr ON (vpr.CodPersona = vp.CodPersona AND
												  vpr.NroPeriodo = vp.NroPeriodo AND
												  vpr.CodTipoNom = vp.CodTipoNom)
			INNER JOIN mastpersonas p ON (p.CodPersona = vp.CodPersona)
			INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
			LEFT JOIN pr_concepto c ON (c.CodConcepto = vp.CodConcepto) 
		WHERE 1 $filtro
		ORDER BY LENGTH(Ndocumento), Ndocumento, Anio";
$field = getRecords($sql);
foreach($field as $f) {
	if ($Grupo != $f['CodEmpleado']) {
		$Grupo = $f['CodEmpleado'];
		$pdf->SetFont('Arial', 'B', 9);
		$pdf->Cell(20, 5, number_format($f['Ndocumento'],0,'','.'), 0, 0, 'L', 0);
		$pdf->Cell(190, 5, utf8_decode($f['NomCompleto']), 0, 1, 'L', 0);
		$i = 0;
	}
	++$i;
	$pdf->SetFont('Arial', '', 8);
	$pdf->Row(array($i,
					utf8_decode($f['NomConcepto'].' '.$f['Anio']),
					$f['Periodo'],
					number_format($f['DiasPago'], 2, ',', '.'),
					$f['CodTipoDocumento'].'-'.$f['NroDocumento'],
					formatFechaDMA($f['FechaPago'])));
	$pdf->Ln(1);
}
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
