<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$filtro = '';
if (trim($fCodOrganismo)) $filtro .= " AND tnec.CodOrganismo = '$fCodOrganismo'";
if (trim($fEjercicioD)) $filtro .= " AND tnec.Periodo >= '$fEjercicioD'";
if (trim($fEjercicioH)) $filtro .= " AND tnec.Periodo <= '$fEjercicioH'";
//---------------------------------------------------

class PDF extends FPDF 
{
	//	Cabecera de página.
	function Header() 
	{
		global $_PARAMETRO;
		global $Ahora;
		global $f;
		global $_POST;
		extract($_POST);
		##	
		$this->SetY(36);
		$this->SetFont('Arial', 'B', 12);
		$this->Cell(195, 5, utf8_decode('RELACIÓN DE SUELDOS ' . $f['Periodo']), 0, 1, 'C');
		##	
		$this->SetY(45);
		$this->SetFont('Arial','B',10);
		$this->SetDrawColor(0,0,0);
		$this->SetFillColor(255,255,255);
		$this->SetWidths(array(30,130,30));
		$this->SetAligns(array('R','L','R'));
		$this->Row(array(utf8_decode('CEDULA'),
						 utf8_decode('NOMBRE COMPLETO'),
						 utf8_decode('SUELDO BÁSICO'),
				));
	}
	
	//	Pie de página.
	function Footer() 
	{
	}
}
//---------------------------------------------------

//---------------------------------------------------
//	Creación del objeto de la clase heredada.
$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->SetMargins(10, 10, 50);
$pdf->SetAutoPageBreak(1, 25);
$Grupo = '';
//---------------------------------------------------
$sql = "SELECT
			tnec.Periodo,
			tnec.CodPersona,
			tnec.CodConcepto,
			p.Ndocumento,
			p.NomCompleto,
			SUM(CASE WHEN tnec.CodConcepto = '0001' THEN tnec.Monto ELSE 0 END) AS Sueldo,
			SUM(CASE WHEN tnec.CodConcepto = '0001' THEN tnec.Cantidad ELSE 0 END) AS Cantidad
		FROM pr_tiponominaempleadoconcepto tnec
		INNER JOIN mastpersonas p ON (p.CodPersona = tnec.CodPersona)
		WHERE
			tnec.CodTipoProceso = 'QU1'
			OR tnec.CodTipoProceso = 'QU2'
			OR tnec.CodTipoProceso = 'SE1'
			OR tnec.CodTipoProceso = 'SE2'
			OR tnec.CodTipoProceso = 'SE3'
			OR tnec.CodTipoProceso = 'SE4'
			OR tnec.CodTipoProceso = 'SE5'
		GROUP BY Periodo, CodPersona
		ORDER BY Periodo, LENGTH(Ndocumento), Ndocumento";
$field = getRecords($sql);
foreach ($field as $f)
{
	if ($Grupo != $f['Periodo'])
	{
		$Grupo = $f['Periodo'];
		$pdf->AddPage();
	}
	$pdf->SetFont('Arial','',10);
	$pdf->SetDrawColor(0,0,0);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetWidths(array(30,130,30));
	$pdf->SetAligns(array('R','L','R'));
	$pdf->Row(array(number_format($f['Ndocumento'],0,'','.'),
					utf8_decode($f['NomCompleto']),
					number_format($f['Sueldo'],2,',','.'),
				));

}
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
