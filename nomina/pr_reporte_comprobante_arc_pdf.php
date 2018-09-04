<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$sql = "SELECT
			o.CodOrganismo,
			o.Organismo,
			o.Direccion AS DireccionOrganismo,
			o.DocFiscal AS DocFiscalOrganismo,
			o.Logo,
			p.NomCompleto,
			p.DocFiscal AS DocFiscalPersona,
			p.Direccion AS DireccionPersona,
			p.Telefono1,
			p.Telefono2
		FROM
			mastpersonas p
			INNER JOIN mastempleado e ON (e.CodPersona = p.CodPersona)
			INNER JOIN mastorganismos o ON (o.CodOrganismo = e.CodOrganismo)
		WHERE p.CodPersona = '".$fCodPersona."'";
$field_datos = getRecord($sql);
//---------------------------------------------------

class PDF extends FPDF {
	//	Cabecera de página.
	function Header() {
		global $_PARAMETRO;
		global $Ahora;
		global $field_datos;
		global $_PARAMETRO;
		global $_POST;
		extract($_POST);
		##
		$this->SetTextColor(0,0,0);
		$this->SetDrawColor(0,0,0);
		$this->SetFillColor(255,255,255);
		##	
		$this->Image($_PARAMETRO["PATHLOGO"].$field_datos['Logo'], 5, 5, 10, 10);
		$this->SetFont('Arial', '', 8);
		$this->SetXY(175, 5); $this->Cell(20, 5, utf8_decode('Fecha: '), 0, 0, 'L');
		$this->Cell(30, 5, formatFechaDMA(substr($Ahora, 0, 10)), 0, 1, 'L');
		$this->SetXY(175, 10); $this->Cell(20, 5, utf8_decode('Página: '), 0, 0, 'L'); 
		$this->Cell(30, 5, $this->PageNo().' de {nb}', 0, 1, 'L');
		$this->SetFont('Arial', 'B', 12);
		$this->SetY(20); 
		$_Periodo = "01-01-".$fAnio." al 31-12-".$fAnio;
		$this->Cell(205, 5, utf8_decode('Comprobante de Retención AR-C'), 0, 1, 'C');
		$this->Cell(205, 5, utf8_decode('Periodo '.$_Periodo), 0, 1, 'C');
		$this->Ln(5);
		##	-------------------
		##	datos agente de retencion
		$this->SetFont('Arial', 'B', 12);
		$this->SetFillColor(220,220,220);
		$this->Cell(205, 7, utf8_decode('Datos Agente de Retención'), 0, 1, 'L', 1);
		$this->SetFont('Arial', 'B', 10);
		$this->Cell(20, 5, utf8_decode('Nombre: '), 0, 0, 'L');
		$this->SetFont('Arial', '', 10);
		$this->Cell(185, 5, utf8_decode($field_datos['Organismo']), 0, 1, 'L');
		$this->SetFont('Arial', 'B', 10);
		$this->Cell(20, 5, utf8_decode('Dirección: '), 0, 0, 'L');
		$this->SetFont('Arial', '', 10);
		$this->MultiCell(185, 5, utf8_decode($field_datos['DireccionOrganismo']), 0, 'L');
		$this->SetFont('Arial', 'B', 10);
		$this->Cell(20, 5, utf8_decode('Rif: '), 0, 0, 'L');
		$this->SetFont('Arial', '', 10);
		$this->Cell(185, 5, utf8_decode($field_datos['DocFiscalOrganismo']), 0, 1, 'L');
		$this->Ln(3);
		##	-------------------
		##	funcionario autorizado
		$sql = "SELECT * FROM mastpersonas WHERE CodPersona = '".$_PARAMETRO['FIRMAARC']."'";
		$field_funcionario = getRecord($sql);
		$this->SetFont('Arial', 'B', 12);
		$this->SetFillColor(220,220,220);
		$this->Cell(205, 7, utf8_decode('Funcionario Autorizado para hacer la Retención'), 0, 1, 'L', 1);
		$this->SetFont('Arial', 'B', 10);
		$this->Cell(40, 5, utf8_decode('Apellidos y Nombres: '), 0, 0, 'L');
		$this->SetFont('Arial', '', 10);
		$this->Cell(165, 5, utf8_decode($field_funcionario['NomCompleto']), 0, 1, 'L');
		$this->SetFont('Arial', 'B', 10);
		$this->Cell(40, 5, utf8_decode('Rif: '), 0, 0, 'L');
		$this->SetFont('Arial', '', 10);
		$this->Cell(165, 5, utf8_decode($field_funcionario['DocFiscal']), 0, 1, 'L');
		$this->Ln(3);
		##	beneficiario de las remuneraciones
		$this->SetFont('Arial', 'B', 12);
		$this->SetFillColor(220,220,220);
		$this->Cell(205, 7, utf8_decode('Beneficiario de las Remuneraciones'), 0, 1, 'L', 1);
		$this->SetFont('Arial', 'B', 10);
		$this->Cell(40, 5, utf8_decode('Apellidos y Nombres: '), 0, 0, 'L');
		$this->SetFont('Arial', '', 10);
		$this->Cell(165, 5, utf8_decode($field_datos['NomCompleto']), 0, 1, 'L');
		$this->SetFont('Arial', 'B', 10);
		$this->Cell(40, 5, utf8_decode('Rif: '), 0, 0, 'L');
		$this->SetFont('Arial', '', 10);
		$this->Cell(185, 5, utf8_decode($field_datos['DocFiscalPersona']), 0, 1, 'L');
		$this->Ln(3);
		##	-------------------
		$this->SetTextColor(0, 0, 0);
		$this->SetDrawColor(0, 0, 0);
		$this->SetFillColor(255, 255, 255);
		$this->Ln(3);
		##
		$this->SetFont('Arial', 'B', 8);
		$this->SetWidths(array(15,50,30,30,50,30));
		$this->SetAligns(array('C','C','C','C','C','C'));
		$this->Row(array(utf8_decode('Mes'),
						 utf8_decode('Cantidad Objeto a Retención'),
						 utf8_decode('Porcentaje de Retención'),
						 utf8_decode('Impuesto Retenido'),
						 utf8_decode('Cantidad Objeto a Retención Acumulada'),
						 utf8_decode('Impuesto Retenido Acumulado')
						 ));
	}
	
	//	Pie de página.
	function Footer() {}
}
//---------------------------------------------------

//---------------------------------------------------
//	Creación del objeto de la clase heredada.
$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->SetMargins(5, 5, 5);
$pdf->SetAutoPageBreak(5, 5);
//---------------------------------------------------
//	consulto
$AcumuladoMonto = 0;
$sql = "SELECT
			tnec.Periodo,
			SUBSTRING(tnec.Periodo, 6, 2) As Mes,
			SUM(tnec.Cantidad) AS Cantidad,
			SUM(tnec.Monto) AS Monto,
			s.SueldoNormal,
			(SELECT SUM(Monto) 
			 FROM pr_tiponominaempleadoconcepto tnec2
			 WHERE
			 	tnec2.CodPersona = tnec.CodPersona AND
			 	tnec2.Periodo = tnec.Periodo AND
			 	(tnec2.CodTipoProceso = 'BVC' OR tnec2.CodTipoProceso = 'BFA')) AS Bono
		FROM 
			pr_tiponominaempleadoconcepto tnec
			INNER JOIN rh_sueldos s ON (s.Periodo = tnec.Periodo AND s.CodPersona = tnec.CodPersona)
		WHERE
			tnec.CodConcepto = '0051' AND
			tnec.CodPersona = '".$fCodPersona."' AND
			tnec.Periodo LIKE '%".$fPeriodo."%'
		GROUP BY tnec.CodPersona
		ORDER BY Periodo";
$field = getRecords($sql);
if (count($field) > 0) $pdf->AddPage();
foreach($field as $f) {
	$SueldoNormal = $f['SueldoNormal'] + $f['Bono'];
	$TotalSueldoNormal += $SueldoNormal;
	$TotalMonto += $f['Monto'];
	##	
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetDrawColor(0, 0, 0);
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 8);
	$pdf->SetAligns(array('C','R','R','R','R','R'));
	$pdf->Row(array(substr(strtoupper(getNombreMes($f['Periodo'])),0,3),
					number_format($SueldoNormal,2,',','.'),
					number_format($f['Cantidad'],2,',','.').' %',
					number_format($f['Monto'],2,',','.'),
					number_format($TotalSueldoNormal,2,',','.'),
					number_format($TotalMonto,2,',','.')
					));
	$pdf->Ln(2);
}
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
