<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$filtro = '';
if (trim($fCodOrganismo)) $filtro .= " AND rspf.CodOrganismo = '$fCodOrganismo'";
if (trim($fEjercicio)) $filtro .= " AND rspf.Ejercicio = '$fEjercicio'";
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
        global $field_categoria;
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
        $this->Cell(335, 5, utf8_decode('RELACIÓN DE OBRAS'), 0, 1, 'C');
        $this->SetDrawColor(0, 0, 0);
        $this->Rect(10, 18, 350, 30, "D");
        ##  
        $this->SetY(48);
        $this->SetFont('Arial','B',6);
        $this->SetDrawColor(0,0,0);
        $this->SetFillColor(255,255,255);
        $this->SetWidths(array(160,10,180));
        $this->SetAligns(array('L','C','L'));
        $this->Row(array(utf8_decode('SECTOR:  PROGRAMA:  SUB-PROGRAMA:  PROYECTO:'),
                         '',
                         utf8_decode('UNIDAD(ES) EJECUTORA(S)')
                ));
        $this->SetY(53);
        $this->SetFont('Arial','B',6);
        $this->Cell(136, 5, utf8_decode('OBRAS'), 1, 0, 'C');
        $this->Cell(24, 5, utf8_decode('MES Y AÑO DE'), 1, 0, 'C');
        $this->Cell(30, 5, '', 0, 0, 'C');
        $this->Cell(160, 5, utf8_decode('ASIGNACIONES'), 1, 0, 'C');
        $this->SetXY(200,58);
        $this->Cell(60, 5, utf8_decode('COMPROMETIDAS'), 1, 0, 'C');
        $this->Cell(60, 5, utf8_decode('EJECUTADAS'), 1, 0, 'C');
        $this->Cell(40, 5, utf8_decode('ESTIMADAS'), 1, 0, 'C');
        ##  
        $this->SetY(69);
        $this->SetDrawColor(255,255,255);
        $this->SetFillColor(255,255,255);
        $this->SetWidths(array(18,18,50,50,12,12,10,20,20,20,20,20,20,20,20,20));
        $this->SetAligns(array('C','C','L','L','C','C','C','C','C','C','C','C','C','C','C','C'));
        $this->Row(array(utf8_decode('PARTIDA'),
                         utf8_decode('CODIGO DE LA OBRA'),
                         utf8_decode('DENOMINACION'),
                         utf8_decode('UNIDAD EJECUTORA'),
                         '','','',
                         utf8_decode('COSTO TOTAL'),
                         utf8_decode('AÑOS ANTERIORES'),
                         utf8_decode('AÑO VIGENTE'),
                         utf8_decode('TOTAL'),
                         utf8_decode('AÑOS ANTERIORES'),
                         utf8_decode('AÑO VIGENTE'),
                         utf8_decode('TOTAL'),
                         utf8_decode('PRESUP.'),
                         utf8_decode('AÑOS POSTERIORES')
                ));
        $this->SetFontSize(6);
        $this->TextWithDirection(153,77,'INICIO','U');
        $this->TextWithDirection(165,77,'CONCLUSIÓN','U');
        $this->TextWithDirection(176,77,'SITUACIÓN','U');

        $this->Ln(1);
        $this->SetWidths(array(18,18,50,50,12,12,10,20,20,20,20,20,20,20,20,20));
        $this->SetAligns(array('C','C','L','L','C','C','C','R','R','R','R','R','R','R','R','R'));
    }
    
    //  Pie de página.
    function Footer() {
        global $MontoTotal;
        global $FlagTotal;
        ##  
        $this->SetDrawColor(0,0,0);
        $this->SetFillColor(0,0,0);
        $this->Rect(10, 48, 350, 152, "D");
        $this->Rect(28, 58, 0.1, 142, "FD");
        $this->Rect(46, 58, 0.1, 142, "FD");
        $this->Rect(96, 58, 0.1, 142, "FD");
        $this->Rect(146, 58, 0.1, 142, "FD");
        $this->Rect(158, 58, 0.1, 142, "FD");
        $this->Rect(170, 58, 0.1, 142, "FD");
        $this->Rect(180, 53, 0.1, 147, "FD");
        $this->Rect(200, 63, 0.1, 137, "FD");
        $this->Rect(220, 63, 0.1, 137, "FD");
        $this->Rect(240, 63, 0.1, 137, "FD");
        $this->Rect(260, 63, 0.1, 137, "FD");
        $this->Rect(280, 63, 0.1, 137, "FD");
        $this->Rect(300, 63, 0.1, 137, "FD");
        $this->Rect(320, 63, 0.1, 137, "FD");
        $this->Rect(340, 63, 0.1, 137, "FD");
        $this->Rect(10, 79, 350, 0.1, "FD");
        ##  
        if ($FlagTotal) {
            $this->SetY(200);
            $this->SetDrawColor(0,0,0);
            $this->SetFont('Arial','B',7);
            $this->Cell(170, 5, utf8_decode('TOTAL'), 1, 0, 'R');
            $this->Cell(20, 5, number_format($MontoTotal,2,',','.'), 1, 0, 'R');
            $this->Cell(20, 5, number_format(0,2,',','.'), 1, 0, 'R');
            $this->Cell(20, 5, number_format(0,2,',','.'), 1, 0, 'R');
            $this->Cell(20, 5, number_format(0,2,',','.'), 1, 0, 'R');
            $this->Cell(20, 5, number_format(0,2,',','.'), 1, 0, 'R');
            $this->Cell(20, 5, number_format(0,2,',','.'), 1, 0, 'R');
            $this->Cell(20, 5, number_format(0,2,',','.'), 1, 0, 'R');
            $this->Cell(20, 5, number_format($MontoTotal,2,',','.'), 1, 0, 'R');
            $this->Cell(20, 5, number_format(0,2,',','.'), 1, 0, 'R');
        }
        ##  
        $this->SetY(205);
        $this->SetFont('Arial','B',7);
        $this->Cell(195, 5, utf8_decode('FORMA:     2122'), 0, 0, 'L');
    }
}
//---------------------------------------------------

//---------------------------------------------------
//  Creación del objeto de la clase heredada.
$pdf = new PDF('L', 'mm', array(218, 370));
$pdf->AliasNbPages();
$pdf->SetMargins(10, 10, 50);
$pdf->SetAutoPageBreak(1, 15);
//---------------------------------------------------
$FlagTotal = false;
$MontoTotal = 0;
$Grupo = '';
##  
$sql = "SELECT
            rspf.*,
            po.Denominacion,
            ss.Denominacion AS SubSector,
            pr.Denominacion AS Programa,
            ue.Denominacion AS UnidadEjecutora
        FROM
            vw_f21_obras_distribucion rspf
            INNER JOIN pv_subsector ss ON (ss.IdSubSector = rspf.IdSubSector)
            INNER JOIN pv_programas pr ON (pr.IdPrograma = rspf.IdPrograma)
            INNER JOIN ob_planobras po ON (po.CodPlanObra = rspf.CodPlanObra)
            INNER JOIN pv_categoriaprog cp ON (cp.CategoriaProg = rspf.CategoriaProg)
            INNER JOIN pv_unidadejecutora ue ON (cp.CodUnidadEjec = ue.CodUnidadEjec)
        WHERE
            (rspf.CodFuente = '04' OR rspf.CodFuente = '08' OR rspf.CodFuente = '09')
            $filtro
        ORDER BY CodOrganismo, Ejercicio, CodSector, CodPrograma, cod_partida";
$field_detalle = getRecords($sql);
foreach ($field_detalle as $f) {
    $field_categoria = $f;
    $Id = $f['CodSector'].$f['IdPrograma'];
    if ($Grupo != $Id) {
        if ($Grupo) {
            $FlagTotal = true;
        }
        $Grupo = $Id;
        $pdf->AddPage();
        $FlagTotal = false;
        $MontoTotal = 0;
    }  
    $pdf->SetDrawColor(255,255,255);
    $pdf->SetFillColor(255,255,255);
    if ($f['Tipo'] == 'Cta') $pdf->SetFont('Arial','BUI',7);
    elseif ($f['Tipo'] == 'Par') $pdf->SetFont('Arial','BU',7);
    elseif ($f['Tipo'] == 'Gen') $pdf->SetFont('Arial','B',7);
    $pdf->SetFont('Arial','',7);
    $pdf->SetAligns(array('C','C','L','L','C','C','C','R','R','R','R','R','R','R','R','R'));
    $pdf->Row(array(utf8_decode($f['cod_partida']),
                    utf8_decode($f['CodInterno']),
                    utf8_decode($f['Denominacion']),
                    utf8_decode($f['UnidadEjecutora']),
                    substr($f['FechaInicio'], 0, 7),
                    substr($f['FechaFin'], 0, 7),
                    substr($f['Situacion'], 0, 1),
                    number_format($f['Monto'],2,',','.'),'','','','','','',
                    number_format($f['Monto'],2,',','.')
                ));
    $pdf->Ln(2);
    $MontoTotal += $f['Monto'];
}
$FlagTotal = true;
//---------------------------------------------------

//---------------------------------------------------
//  Muestro el contenido del pdf.
$pdf->Output();
?>