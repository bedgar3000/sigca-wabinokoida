<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$filtro = '';
if (trim($fCodOrganismo)) $filtro .= " AND cp.CodOrganismo = '$fCodOrganismo'";
if (trim($fEjercicio)) $filtro .= " AND rm.Ejercicio = '$fEjercicio'";
$sql = "SELECT
			o.Organismo,
			e.Estado As NomEstado,
			m.Municipio
		FROM
			mastorganismos o
			INNER JOIN mastciudades c ON (c.CodCiudad = o.CodCiudad)
			INNER JOIN mastmunicipios m ON (m.CodMunicipio = c.CodMunicipio)
			INNER JOIN mastestados e ON (e.CodEstado = m.CodEstado)
		WHERE o.CodOrganismo = '$fCodOrganismo'";
$field_organismo = getRecord($sql);
//---------------------------------------------------

class PDF extends FPDF {
	//	Cabecera de página.
	function Header() {
		global $_PARAMETRO;
		global $Ahora;
		global $field_organismo;
		global $field_meta;
		global $_POST;
		extract($_POST);
		##	
		$this->SetY(20);
		$this->SetFont('Arial','B',8);
		$this->Cell(30, 4, utf8_decode('ENTIDAD FEDERAL: '), 0, 0, 'L');
		$this->Cell(170, 4, utf8_decode($field['NomEstado']), 0, 1, 'L');
		$this->Cell(90, 4, utf8_decode('CODIGO PRESUPUESTARIO Y NOMBRE DEL MUNICIPIO: E5607 - '), 0, 0, 'L');
		$this->Cell(170, 4, utf8_decode($field['Municipio']), 0, 1, 'L');
		$this->Cell(42, 4, utf8_decode('PERIODO PRESUPUESTARIO:'), 0, 0, 'L');
		$this->Cell(170, 4, utf8_decode($fEjercicio), 0, 1, 'L');
		$this->SetY(40);
		$this->SetFont('Arial', 'B', 10);
		$this->Cell(260, 5, utf8_decode('METAS DEL PROGRAMA, SUB-PROGRAMA Y/O PROYECTO'), 0, 1, 'C');
		$this->SetDrawColor(0, 0, 0);
		$this->Rect(10, 18, 260, 30, "D");
		$this->SetY(50);
		##
		$this->SetFont('Arial','B',8);
		$this->SetDrawColor(0,0,0);
		$this->SetFillColor(255,255,255);
		$this->SetWidths(array(35,15,210));
		$this->SetAligns(array('C','C','C'));
		$this->Row(array(utf8_decode(''), utf8_decode('CÓDIGO'), utf8_decode('DENOMINACIÓN')));
		$this->SetAligns(array('L','C','L'));
		$this->Row(array(utf8_decode('SECTOR:'), utf8_decode($field_meta['CodClaSectorial']), utf8_decode(mb_strtoupper($field_meta['Sector']))));
		$this->Row(array(utf8_decode('PROGRAMA:'), utf8_decode($field_meta['CodPrograma']), utf8_decode(mb_strtoupper($field_meta['Programa']))));
		$this->Row(array(utf8_decode('SUB-PROGRAMA:'), utf8_decode($field_meta['CodSubPrograma']), utf8_decode(mb_strtoupper($field_meta['SubPrograma']))));
		$this->Row(array(utf8_decode('PROYECTO:'), utf8_decode($field_meta['CodProyecto']), utf8_decode(mb_strtoupper($field_meta['Proyecto']))));
		##	
        $this->SetFont('Arial','B',8);
        $this->SetDrawColor(0,0,0);
        $this->SetFillColor(255,255,255);
        $this->SetWidths(array(120,80,30,30));
        $this->SetAligns(array('C','C','C','C'));
        $this->Row(array(utf8_decode('DENOMINACION'),
                         utf8_decode('UNIDAD DE MEDIDA'),
                         utf8_decode('CANTIDADES PROGRAMADAS'),
                         utf8_decode('COSTO FINANCIERO')));
        $this->SetAligns(array('FJ','FJ','C','R'));
        $this->Ln(1);
	}
	
	//	Pie de página.
	function Footer() {
		global $FlagTotal;
		##	
		$this->SetDrawColor(0,0,0);
		$this->SetFillColor(0,0,0);
		$this->Rect(10, 50, 260, 150, "D");
        $this->Rect(130, 85, 0.1, 115, "FD");
        $this->Rect(210, 85, 0.1, 115, "FD");
        $this->Rect(240, 85, 0.1, 115, "FD");
		##	
		//if ($FlagTotal) {
			##	
			$this->SetY(200);
			$this->SetFont('Arial','B',7);
			$this->Cell(195, 5, utf8_decode('FORMA:     2114'), 0, 0, 'L');
		//}
	}
}
//---------------------------------------------------

//---------------------------------------------------
//	Creación del objeto de la clase heredada.
$pdf = new PDF('L', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->SetMargins(10, 10, 50);
$pdf->SetAutoPageBreak(1, 25);
//---------------------------------------------------
$FlagTotal = false;
$Grupo = '';
##	
$sql = "SELECT
			ss.CodSector,
			pg.IdPrograma,
			spg.IdSubPrograma,
			ss.CodClaSectorial,
			ss.Denominacion AS Sector,
			pg.CodPrograma,
			pg.Denominacion AS Programa,
			spg.CodSubPrograma,
			spg.Denominacion AS SubPrograma,
			py.CodProyecto,
			py.Denominacion AS Proyecto,
			rm.Descripcion,
			rm.Cantidad,
			(SELECT SUM((Cantidad * (PrecioUnitario + MontoIva))) FROM pv_reformulacionmetasdet WHERE Ejercicio = rm.Ejercicio AND CodMeta = rm.CodMeta) AS Monto
		FROM
			pv_reformulacionmetas rm
			INNER JOIN pv_metaspoa mp ON (mp.CodMeta = rm.CodMeta)
			INNER JOIN pv_objetivospoa op ON (op.CodObjetivo = mp.CodObjetivo)
			INNER JOIN pv_categoriaprog cp ON (cp.CategoriaProg = op.CategoriaProg)
			INNER JOIN pv_actividades a ON (a.IdActividad = cp.IdActividad)
			INNER JOIN pv_proyectos py ON (py.IdProyecto = a.IdProyecto)
			INNER JOIN pv_subprogramas spg ON (spg.IdSubPrograma = py.IdSubPrograma)
			INNER JOIN pv_programas pg ON (pg.IdPrograma = spg.IdPrograma)
			INNER JOIN pv_subsector ss ON (ss.IdSubSector = pg.IdSubSector)
		WHERE 1 $filtro
		ORDER BY cp.CodOrganismo, rm.Ejercicio, ss.CodSector, pg.IdPrograma, spg.IdSubPrograma, py.IdProyecto, rm.CodMeta";
$field_detalle = getRecords($sql);
foreach ($field_detalle as $f) {
	$field_meta = $f;
	##	
	$Proyecto = $f['CodSector'].$f['IdPrograma'].$f['IdSubPrograma'].$f['IdProyecto'];
	if ($Grupo != $Proyecto) {
		$Grupo = $Proyecto;
		$pdf->AddPage();
	}
    $pdf->SetFont('Arial','',8);
    $pdf->SetDrawColor(255,255,255);
    $pdf->SetFillColor(255,255,255);
    $pdf->SetWidths(array(120,80,30,30));
    $pdf->SetAligns(array('FJ','FJ','C','R'));
    $pdf->Row(array(utf8_decode($f['Descripcion'].'                                      '),
                    utf8_decode(''),
                    utf8_decode($f['Cantidad']),
                    number_format($f['Monto'],2,',','.')));
    $pdf->Ln(2);
}
$FlagTotal = true;
//---------------------------------------------------

//---------------------------------------------------
//	Muestro el contenido del pdf.
$pdf->Output();
?>  
