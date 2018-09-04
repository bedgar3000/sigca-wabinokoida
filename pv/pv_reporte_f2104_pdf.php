<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$filtro = '';
if (trim($fCodOrganismo)) $filtro .= " AND rspf.CodOrganismo = '$fCodOrganismo'";
if (trim($fEjercicio)) $filtro .= " AND rspf.Ejercicio = '$fEjercicio'";
if (trim($fIdSubSector)) $filtro .= " AND rspf.IdSubSector = '$fIdSubSector'";
if (trim($fIdPrograma)) $filtro .= " AND rspf.IdPrograma = '$fIdPrograma'";
if (trim($fIdSubPrograma)) $filtro .= " AND rspf.IdSubPrograma = '$fIdSubPrograma'";
if (trim($fIdProyecto)) $filtro .= " AND rspf.IdProyecto = '$fIdProyecto'";
if (trim($fIdActividad)) $filtro .= " AND rspf.IdActividad = '$fIdActividad'";
if (trim($fCodUnidadEjec)) $filtro .= " AND ue.CodUnidadEjec = '$fCodUnidadEjec'";
if (trim($fCodDependencia)) $filtro .= " AND ued.CodDependencia = '$fCodDependencia'";
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
$field = getRecord($sql);
//---------------------------------------------------

class PDF extends FPDF {
    //  Cabecera de página.
    function Header() {
        global $_PARAMETRO;
        global $Ahora;
        global $field;
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
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(258, 5, utf8_decode('RESUMEN CREDITOS PRESUPUESTARIOS A NIVEL DE SECTOR, PROGRAMA Y FUENTES DE FINANCIAMIENTO'), 0, 1, 'C');
        $this->SetDrawColor(0, 0, 0);
        $this->Rect(10, 18, 258, 30, "D");
        ##  
        $this->SetFont('Arial','B',7);

        $this->SetXY(168,54); $this->Cell(100, 5, utf8_decode('ASIGNACIÓN PRESUPUESTARIA'), 1, 0, 'C');
        $this->SetXY(168,59); $this->Cell(60, 5, utf8_decode('APORTE LEGAL'), 1, 0, 'C');
        $this->SetXY(168,64); $this->Cell(40, 5, utf8_decode('SITUADO'), 1, 0, 'C');

        $this->SetY(70);
        $this->SetDrawColor(255,255,255);
        $this->SetFillColor(255,255,255);
        $this->SetWidths(array(8,8,122,20,20,20,20,20,20));
        $this->SetAligns(array('C','C','C','C','C','C','C','C','C'));
        $this->Row(array(utf8_decode('SEC'),
                         utf8_decode('PRO'),
                         utf8_decode('DENOMINACION'),
                         utf8_decode('INGRESOS PROPIOS'),
                         utf8_decode('MUNICIPAL'),
                         utf8_decode('ESTADAL A MUNICIPAL'),
                         utf8_decode('FCI'),
                         utf8_decode('OTRAS'),
                         utf8_decode('TOTAL')
                ));
        $this->Ln(1);
    }
    
    //  Pie de página.
    function Footer() {
        global $MontoTotal;
        global $FlagTotal;
        ##  
        $this->SetDrawColor(0,0,0);
        $this->SetFillColor(0,0,0);
        $this->Rect(10, 54, 258, 146, "D");
        $this->Rect(18, 54, 0.1, 146, "FD");
        $this->Rect(26, 54, 0.1, 146, "FD");
        $this->Rect(148, 54, 0.1, 146, "FD");
        $this->Rect(168, 69, 0.1, 131, "FD");
        $this->Rect(188, 69, 0.1, 131, "FD");
        $this->Rect(208, 69, 0.1, 131, "FD");
        $this->Rect(228, 59, 0.1, 141, "FD");
        $this->Rect(248, 59, 0.1, 141, "FD");
        $this->Rect(10, 79, 258, 0.1, "FD");
        ##  
        if ($FlagTotal) {
            $this->SetY(200);
            $this->SetDrawColor(0,0,0);
            $this->SetFont('Arial','B',7);
            $this->Cell(238, 5, utf8_decode('TOTAL'), 1, 0, 'R');
            $this->Cell(20, 5, number_format($MontoTotal,2,',','.'), 1, 0, 'R');
			##	
			$this->SetY(205);
			$this->SetFont('Arial','B',7);
			$this->Cell(195, 5, utf8_decode('FORMA:     2104'), 0, 0, 'L');
        }
    }
}
//---------------------------------------------------

//---------------------------------------------------
//  Creación del objeto de la clase heredada.
$pdf = new PDF('L', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->SetMargins(10, 10, 50);
$pdf->SetAutoPageBreak(1, 25);
$pdf->AddPage();
//---------------------------------------------------
$FlagTotal = false;
$MontoTotal = 0;
##  
$sql = "SELECT
            rspf.CodSector,
            rspf.CodSubSector,
            rspf.CodPrograma,
            pr.Denominacion,
            SUM(CASE WHEN rspf.CodFuente = '01' THEN rspf.Monto ELSE 0 END) AS IngresosPropios,
            SUM(CASE WHEN rspf.CodFuente = '02' THEN rspf.Monto ELSE 0 END) AS Municipal,
            SUM(CASE WHEN rspf.CodFuente = '03' THEN rspf.Monto ELSE 0 END) AS EstadalMunicipal,
            SUM(CASE WHEN rspf.CodFuente = '04' THEN rspf.Monto ELSE 0 END) AS FCI,
            SUM(CASE WHEN rspf.CodFuente > '04' THEN rspf.Monto ELSE 0 END) AS Otras
        FROM
        	vw_f21_resumen_sector_programa_fuente rspf
        	INNER JOIN pv_programas pr ON (pr.IdPrograma = rspf.IdPrograma)
            INNER JOIN pv_categoriaprog cp ON (cp.CategoriaProg = rspf.CategoriaProg)
            INNER JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cp.CodUnidadEjec)
            LEFT JOIN pv_unidadejecutoradep ued ON (ued.CodUnidadEjec = ue.CodUnidadEjec)
        WHERE 1 $filtro
        GROUP BY CodSector, CodSubSector, CodPrograma
        ORDER BY CodSector, CodSubSector, CodPrograma";
$field_detalle = getRecords($sql);
foreach ($field_detalle as $f) {
	$Total = $f['IngresosPropios'] + $f['Municipal'] + $f['EstadalMunicipal'] + $f['FCI'] + $f['Otras'];
	##	
    $pdf->SetDrawColor(255,255,255);
    $pdf->SetFillColor(255,255,255);
    $pdf->SetFont('Arial','',7);
	$pdf->SetAligns(array('C','C','L','R','R','R','R','R','R'));
    $pdf->Row(array(utf8_decode($f['CodSector']),
                    utf8_decode($f['CodPrograma']),
                    utf8_decode($f['Denominacion']),
                    number_format($f['IngresosPropios'],2,',','.'),
                    number_format($f['Municipal'],2,',','.'),
                    number_format($f['EstadalMunicipal'],2,',','.'),
                    number_format($f['FCI'],2,',','.'),
                    number_format($f['Otras'],2,',','.'),
                    number_format($Total,2,',','.')
                ));
    $MontoTotal += $Total;
}
$FlagTotal = true;
//---------------------------------------------------

//---------------------------------------------------
//  Muestro el contenido del pdf.
$pdf->Output();
?>