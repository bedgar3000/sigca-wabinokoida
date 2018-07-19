<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
list($CodOrganismo, $CodAjuste) = explode('_', $sel_registros);
$sql = "SELECT
			a.*,
			md.Descripcion AS TipoAjuste
		FROM
			pv_ajustes a
			LEFT JOIN mastmiscelaneosdet md ON (
				md.CodDetalle = a.Tipo
				AND md.CodMaestro = 'TIPOAJUSTE'
			)
		WHERE
			a.CodOrganismo = '$CodOrganismo'
			AND a.CodAjuste = '$CodAjuste'";
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
		$this->SetFont('Arial','B',8);
		$this->Image($_PARAMETRO["PATHLOGO"].'logo-alcaldia.jpg', 15, 12, 20, 15);
		$this->SetXY(38, 15); $this->Cell(100, 5, utf8_decode($field_organismo['Organismo']), 0, 1, 'L');
		$this->SetXY(38, 20); $this->Cell(100, 5, utf8_decode($Dependencia), 0, 1, 'L');

		$this->SetFont('Arial','',8);
		$this->SetXY(165, 15); $this->Cell(20, 5, utf8_decode('Fecha: '), 0, 0, 'L');
		$this->Cell(30, 5, formatFechaDMA($FechaActual), 0, 1, 'L');
		$this->SetXY(165, 20); $this->Cell(20, 5, utf8_decode('Página: '), 0, 0, 'L'); 
		$this->Cell(30, 5, $this->PageNo().' de {nb}', 0, 1, 'L');
		##	
		$this->SetY(35);
		$this->SetFont('Arial','B',10);
		$this->Cell(190, 5, utf8_decode('REPORTE DE ' . $field['TipoAjuste']), 0, 1, 'C');
		$this->Ln(10);
		##	
		$this->SetDrawColor(0,0,0); $this->SetFillColor(240,240,240);
		$this->SetFont('Arial','B',8);
		$this->Cell(25, 5, utf8_decode('Traslado N°:'), 0, 0, 'L', 1);
		$this->Cell(50, 5, utf8_decode($field['CodAjuste']), 0, 0, 'L');
		$this->Cell(35, 5, utf8_decode('Fecha de Traslado:'), 0, 0, 'L', 1);
		$this->Cell(20, 5, formatFechaDMA($field['Fecha']), 0, 0, 'L');
		$this->Ln(6);
		$this->Cell(25, 5, utf8_decode('Resolución N°:'), 0, 0, 'L', 1);
		$this->Cell(50, 5, utf8_decode($field['NroResolucion']), 0, 0, 'L');
		$this->Cell(35, 5, utf8_decode('Fecha de Resolución:'), 0, 0, 'L', 1);
		$this->Cell(15, 5, formatFechaDMA($field['FechaResolucion']), 0, 0, 'L');
		$this->Ln(6);
		$this->Cell(25, 5, utf8_decode('Monto Traslado:'), 0, 0, 'L', 1);
		$this->Cell(50, 5, number_format($field['TotalCreditos'],2,',','.'), 0, 0, 'L');
		$this->Ln(6);
		$this->Cell(25, 5, utf8_decode('Justificación:'), 0, 0, 'L', 1);
		$this->MultiCell(190, 5, utf8_decode($field['Descripcion']), 0, 'L');
		$this->Ln(5);
	}
	
	//	Pie de página.
	function Footer() {
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
$pdf->SetFont('Arial','B',8);
$pdf->Cell(190, 5, utf8_decode('DISTRIBUCIÓN PRESUPUESTARIA:'), 0, 1, 'L');
##	PARTIDAS CEDENTES
$MontoCedido = 0;
##	
$sql = "SELECT
			ajd.*,
			pvd.MontoAprobado,
			pvd.MontoAjustado,
			pvd.MontoCompromiso,
			pvd.MontoCausado,
			pvd.MontoPagado,
			CONCAT(ss.CodSector, pr.CodPrograma, a.CodActividad) AS CatProg
		FROM
			pv_ajustesdet ajd
			INNER JOIN pv_presupuestodet pvd ON (pvd.CodOrganismo = ajd.CodOrganismo
												AND pvd.CodPresupuesto = ajd.CodPresupuesto
												AND pvd.CodFuente = ajd.CodFuente
												AND pvd.cod_partida = ajd.cod_partida)
			INNER JOIN pv_presupuesto pv ON (pv.CodOrganismo = pvd.CodOrganismo AND pv.CodPresupuesto = pvd.CodPresupuesto)
			INNER JOIN pv_categoriaprog cp ON (cp.CategoriaProg = pv.CategoriaProg)
			INNER JOIN pv_actividades a ON (a.IdActividad = cp.IdActividad)
			INNER JOIN pv_proyectos py ON (py.IdProyecto = a.IdProyecto)
			INNER JOIN pv_subprogramas spr ON (spr.IdSubPrograma = py.IdSubPrograma)
			INNER JOIN pv_programas pr ON (pr.IdPrograma = spr.IdPrograma)
			INNER JOIN pv_subsector ss ON (ss.IdSubSector = pr.IdSubSector)
		WHERE
			ajd.CodOrganismo = '$CodOrganismo'
			AND ajd.CodAjuste = '$CodAjuste'
			AND ajd.Tipo = 'D'";
$field_cedentes = getRecords($sql);
##	
if (count($field_cedentes)) {
	$pdf->SetFont('Arial','B',8);
	$pdf->Cell(190, 5, utf8_decode('Partidas Cedentes:'), 0, 1, 'L');
	$pdf->SetDrawColor(0,0,0); $pdf->SetFillColor(240,240,240);
	$pdf->SetWidths(array(16,20,10,24,24,24,24,24,24));
	$pdf->SetAligns(array('C','C','C','C','C','C','C','C','C'));
	$pdf->Row(array(utf8_decode('Cat. Prog.'),
					utf8_decode('Partida'),
					utf8_decode('F.F'),
					utf8_decode('Monto Original'),
					utf8_decode('Monto Actual'),
					utf8_decode('Monto Compromiso'),
					utf8_decode('Monto Causado'),
					utf8_decode('Monto Pagado'),
					utf8_decode('Monto Cedido')
			));
	foreach ($field_cedentes as $f) {
		$pdf->SetDrawColor(0,0,0); $pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',8);
		$pdf->SetAligns(array('C','C','C','R','R','R','R','R','R'));
		$pdf->Row(array(utf8_decode($f['CatProg']),
						utf8_decode($f['cod_partida']),
						utf8_decode($f['CodFuente']),
						number_format($f['MontoAprobado'],2,',','.'),
						number_format($f['MontoAjustado'],2,',','.'),
						number_format($f['MontoCompromiso'],2,',','.'),
						number_format($f['MontoCausado'],2,',','.'),
						number_format($f['MontoPagado'],2,',','.'),
						number_format($f['MontoAjuste'],2,',','.')
					));
		$MontoCedido += $f['MontoAjuste'];
	}
	$pdf->SetFont('Arial','B',8);
	$pdf->Cell(142, 5);
	$pdf->Cell(24, 5, utf8_decode('Total Cedido:'), 1, 0, 'R');
	$pdf->Cell(24, 5, number_format($MontoCedido,2,',','.'), 1, 1, 'R');
	$pdf->Ln(10);
}
##	PARTIDAS RECEPTORAS
$MontoRecibido = 0;
##	
$pdf->SetFont('Arial','B',8);
$pdf->Cell(190, 5, utf8_decode('Partidas Receptoras:'), 0, 1, 'L');
$pdf->SetDrawColor(0,0,0); $pdf->SetFillColor(240,240,240);
$pdf->SetWidths(array(16,20,10,24,24,24,24,24,24));
$pdf->SetAligns(array('C','C','C','C','C','C','C','C','C'));
$pdf->Row(array(utf8_decode('Cat. Prog.'),
				utf8_decode('Partida'),
				utf8_decode('F.F'),
				utf8_decode('Monto Original'),
				utf8_decode('Monto Actual'),
				utf8_decode('Monto Compromiso'),
				utf8_decode('Monto Causado'),
				utf8_decode('Monto Pagado'),
				utf8_decode('Monto Recibido')
		));
##	
$sql = "SELECT
			ajd.*,
			pvd.MontoAprobado,
			pvd.MontoAjustado,
			pvd.MontoCompromiso,
			pvd.MontoCausado,
			pvd.MontoPagado,
			CONCAT(ss.CodSector, pr.CodPrograma, a.CodActividad) AS CatProg
		FROM
			pv_ajustesdet ajd
			INNER JOIN pv_presupuestodet pvd ON (pvd.CodOrganismo = ajd.CodOrganismo
												AND pvd.CodPresupuesto = ajd.CodPresupuesto
												AND pvd.CodFuente = ajd.CodFuente
												AND pvd.cod_partida = ajd.cod_partida)
			INNER JOIN pv_presupuesto pv ON (pv.CodOrganismo = pvd.CodOrganismo AND pv.CodPresupuesto = pvd.CodPresupuesto)
			INNER JOIN pv_categoriaprog cp ON (cp.CategoriaProg = pv.CategoriaProg)
			INNER JOIN pv_actividades a ON (a.IdActividad = cp.IdActividad)
			INNER JOIN pv_proyectos py ON (py.IdProyecto = a.IdProyecto)
			INNER JOIN pv_subprogramas spr ON (spr.IdSubPrograma = py.IdSubPrograma)
			INNER JOIN pv_programas pr ON (pr.IdPrograma = spr.IdPrograma)
			INNER JOIN pv_subsector ss ON (ss.IdSubSector = pr.IdSubSector)
		WHERE
			ajd.CodOrganismo = '$CodOrganismo'
			AND ajd.CodAjuste = '$CodAjuste'
			AND ajd.Tipo = 'I'";
$field_receptoras = getRecords($sql);
foreach ($field_receptoras as $f) {
	$pdf->SetDrawColor(0,0,0); $pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',8);
	$pdf->SetAligns(array('C','C','C','R','R','R','R','R','R'));
	$pdf->Row(array(utf8_decode($f['CatProg']),
					utf8_decode($f['cod_partida']),
					utf8_decode($f['CodFuente']),
					number_format($f['MontoAprobado'],2,',','.'),
					number_format($f['MontoAjustado'],2,',','.'),
					number_format($f['MontoCompromiso'],2,',','.'),
					number_format($f['MontoCausado'],2,',','.'),
					number_format($f['MontoPagado'],2,',','.'),
					number_format($f['MontoAjuste'],2,',','.')
				));
	$MontoRecibido += $f['MontoAjuste'];
}
$pdf->SetFont('Arial','B',8);
$pdf->Cell(142, 5);
$pdf->Cell(24, 5, utf8_decode('Total Recibido:'), 1, 0, 'R');
$pdf->Cell(24, 5, number_format($MontoRecibido,2,',','.'), 1, 1, 'R');
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
