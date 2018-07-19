<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$sql = "SELECT
			c.*,
			p.NomCompleto AS NomPersona,
			ue.Denominacion AS UnidadEjecutora
		FROM
			ap_certificaciones c
			INNER JOIN mastpersonas p ON (p.CodPersona = c.CodPersona)
			LEFT JOIN pv_presupuesto ppto ON (c.CodOrganismo = ppto.CodOrganismo AND c.CodPresupuesto = ppto.CodPresupuesto)
			LEFT JOIN pv_categoriaprog cp ON (cp.CategoriaProg = ppto.CategoriaProg)
			LEFT JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cp.CodUnidadEjec)
		WHERE c.CodCertificacion = '$sel_registros'";
$field = getRecord($sql);
//---------------------------------------------------

class PDF extends FPDF {
	//	Cabecera de página.
	function Header() {
		global $_PARAMETRO;
		global $FechaActual;
		global $field;
		global $_POST;
		extract($_POST);
		##	
		$field_organismo = getRecord("SELECT * FROM mastorganismos WHERE CodOrganismo = '$field[CodOrganismo]'");
		$Dependencia = getUnidadEjecutora($_PARAMETRO["CATPV"]);
		##	
		$this->SetFont('Arial','B',12);
		$this->Image($_PARAMETRO["PATHLOGO"].'logo-alcaldia.jpg', 15, 12, 30, 23);
		$this->SetY(25); $this->Cell(190, 5, utf8_decode('COMPROMISO PRESUPUESTARIO DE OBRAS'), 0, 1, 'C');
		##	
		$this->SetY(35);
		$this->SetFont('Arial','B',10);
		$this->Cell(190, 5, utf8_decode('CONTRATO DE OBRAS'), 0, 1, 'C');
		$this->Cell(190, 5, utf8_decode($field['CodTipoCertif'].'-'.$field['Anio'].'-'.$field['CodInterno']), 0, 1, 'C');
		$this->Ln(10);
	}
	
	//	Pie de página.
	function Footer() {
		global $_PARAMETRO;
		global $FechaActual;
		global $field;
		global $_POST;
		extract($_POST);
		##	
		list($_PREPARADO['Nombre'], $_PREPARADO['Cargo'], $_PREPARADO['Nivel']) = getFirma($field['PreparadoPor']);
		list($_REVISADO['Nombre'], $_REVISADO['Cargo'], $_REVISADO['Nivel']) = getFirma(getPersonaUnidadEjecutora($_PARAMETRO["CATPV"]));
		$RevisadoPor = getUnidadEjecutora($_PARAMETRO["CATPV"]);
		list($_CONFORMADO['Nombre'], $_CONFORMADO['Cargo'], $_CONFORMADO['Nivel']) = getFirma(getPersonaUnidadEjecutora($_PARAMETRO["CATADM"]));
		$ConformadoPor = getUnidadEjecutora($_PARAMETRO["CATADM"]);
		##	
		$this->SetDrawColor(0,0,0);
		$this->RoundedRect(13, 230, 190, 35, 2.5, 'D');
		$this->Line(13, 235, 203, 235);
		$this->Line(76, 230, 76, 265);
		$this->Line(139, 230, 139, 265);
		##	
		$this->SetY(230);
		$this->SetFont('Arial','',7);
		$this->Cell(63, 5, utf8_decode('PREPARADO POR'), 0, 0, 'C');
		$this->Cell(63, 5, utf8_decode('REVISADO POR'), 0, 0, 'C');
		$this->Cell(64, 5, utf8_decode('CONFORMADO POR'), 0, 0, 'C');
		##	
		$this->SetFont('Arial','B',7);
		$this->SetY(237);
		$this->SetX(13); $this->MultiCell(63, 3, utf8_decode($_PREPARADO['Nombre']), 0, 'C');
		$this->Ln(2);
		$this->SetX(13); $this->MultiCell(63, 3, utf8_decode($_PREPARADO['Cargo']), 0, 'C');
		$this->SetY(237);
		$this->SetX(76); $this->MultiCell(63, 3, utf8_decode($RevisadoPor), 0, 'C');
		$this->SetY(237);
		$this->SetX(139); $this->MultiCell(63, 3, utf8_decode($ConformadoPor), 0, 'C');
		##	
		$this->SetFont('Arial','',6);
		$this->SetXY(13,253); $this->Cell(63, 3, utf8_decode('Firma:'), 0, 0, 'L');
		$this->SetXY(13,260); $this->Cell(63, 3, utf8_decode('Fecha:'), 0, 0, 'L');
		$this->SetXY(76,253); $this->Cell(63, 3, utf8_decode('Firma:'), 0, 0, 'L');
		$this->SetXY(76,260); $this->Cell(63, 3, utf8_decode('Fecha:'), 0, 0, 'L');
		$this->SetXY(139,253); $this->Cell(63, 3, utf8_decode('Firma:'), 0, 0, 'L');
		$this->SetXY(139,260); $this->Cell(63, 3, utf8_decode('Fecha:'), 0, 0, 'L');

	}
}
//---------------------------------------------------

//---------------------------------------------------
//	Creación del objeto de la clase heredada.
$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->SetMargins(13, 15, 15);
$pdf->SetAutoPageBreak(1, 25);
$pdf->AddPage();
//---------------------------------------------------
##	
$pdf->SetDrawColor(0,0,0); $pdf->SetFillColor(255,255,255);
$pdf->SetFont('Arial','',8);
$pdf->Cell(35, 5, utf8_decode('UNIDAD EJECUTORA:'), 0, 0, 'L');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(120, 5, utf8_decode($field['UnidadEjecutora']), 0, 0, 'L');
$pdf->SetFont('Arial','',8);
$pdf->Cell(15, 5, utf8_decode('FECHA:'), 0, 0, 'L');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(20, 5, formatFechaDMA($field['Fecha']), 0, 0, 'L');
$pdf->Ln(8);
##	
$pdf->SetDrawColor(0,0,0);
$pdf->RoundedRect(13, 62, 190, 60, 2.5, 'D');
##	
$pdf->SetDrawColor(255,255,255);
$pdf->SetFont('Arial','',8);
$pdf->Cell(190, 5, utf8_decode('CONCEPTO DEL GASTO'), 0, 0, 'C');
$pdf->Ln(6);
$pdf->SetFont('Arial','B',8);
$pdf->MultiCell(190, 5, utf8_decode($field['Justificacion']), 0, 'L');
##	
$pdf->SetY(105);
$pdf->SetFont('Arial','',9);
$pdf->Cell(140, 5, utf8_decode('MONTO A PAGAR: '), 0, 0, 'R');
$pdf->SetFont('Arial','B',9);
$pdf->Cell(10, 5, utf8_decode('Bs.'), 0, 0, 'R');
$pdf->Cell(40, 5, number_format($field['Monto'],2,',','.'), 0, 0, 'R');
$pdf->Ln(10);
##	
$pdf->SetFont('Arial','',9);
$pdf->Cell(30, 5, utf8_decode('BENEFICIARIO: '), 0, 0, 'L');
$pdf->SetFont('Arial','B',9);
$pdf->Cell(160, 5, utf8_decode($field['NomPersona']), 0, 0, 'L');
$pdf->Ln(13);
##	
$pdf->SetDrawColor(0,0,0);
$pdf->RoundedRect(13, 127, 190, 75, 2.5, 'D');
##	
$pdf->SetDrawColor(255,255,255);
$pdf->SetFont('Arial','',9);
$pdf->Cell(190, 5, utf8_decode('DISTRIBUCIÓN PRESUPUESTARIA'), 0, 1, 'C');
##	
$Total = 0;
$pdf->SetFont('Arial','B',8);
$pdf->SetDrawColor(0,0,0); $pdf->SetFillColor(255,255,255);
$pdf->SetWidths(array(20,10,20,105,35));
$pdf->SetAligns(array('C','C','C','L','R'));
$pdf->Row(array(utf8_decode('Cat. Prog.'),
				utf8_decode('F.F'),
				utf8_decode('Partida'),
				utf8_decode('Denominación'),
				utf8_decode('Monto')
		));
$sql = "SELECT
			cd.*,
			pv.denominacion,
			CONCAT(ss.CodSector, pr.CodPrograma, a.CodActividad) AS CatProg
		FROM
			ap_certificacionesdet cd
			INNER JOIN pv_partida pv On (pv.cod_partida = cd.cod_partida)
			LEFT JOIN pv_presupuestodet pptod ON (cd.CodOrganismo = pptod.CodOrganismo 
												  AND cd.CodPresupuesto = pptod.CodPresupuesto
												  AND pptod.CodFuente = cd.CodFuente
												  AND pptod.cod_partida = cd.cod_partida)
			LEFT JOIN pv_presupuesto ppto ON (ppto.CodOrganismo = cd.CodOrganismo AND ppto.CodPresupuesto = cd.CodPresupuesto)
			LEFT JOIN pv_categoriaprog cp ON (cp.CategoriaProg = ppto.CategoriaProg)
			LEFT JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cp.CodUnidadEjec)
			LEFT JOIN pv_actividades a ON (a.IdActividad = cp.IdActividad)
			LEFT JOIN pv_proyectos py ON (py.IdProyecto = a.IdProyecto)
			LEFT JOIN pv_subprogramas spr ON (spr.IdSubPrograma = py.IdSubPrograma)
			LEFT JOIN pv_programas pr ON (pr.IdPrograma = spr.IdPrograma)
			LEFT JOIN pv_subsector ss ON (ss.IdSubSector = pr.IdSubSector)
		WHERE cd.CodCertificacion = '$sel_registros'";
$field_detalle = getRecords($sql);
foreach ($field_detalle as $f) {
	$pdf->SetFont('Arial','',8);
	$pdf->Row(array(utf8_decode($f['CatProg']),
					utf8_decode($f['CodFuente']),
					utf8_decode($f['cod_partida']),
					utf8_decode($f['denominacion']),
					number_format($f['Monto'],2,',','.')
			));
	##	
	$Total += $f['Monto'];
}
$pdf->Line(13, 197, 203, 197);
$pdf->Line(168, 197, 168, 202);
##	
$pdf->SetY(197);
$pdf->SetFont('Arial','B',8);
$pdf->Cell(155, 5, utf8_decode('TOTAL'), 0, 0, 'R');
$pdf->Cell(35, 5, number_format($Total,2,',','.'), 0, 1, 'R');
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
