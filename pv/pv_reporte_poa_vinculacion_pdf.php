<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$filtro = '';
if (trim($fCodOrganismo)) $filtro .= " AND cp.CodOrganismo = '$fCodOrganismo'";
if (trim($fEjercicio)) $filtro .= " AND rfm.Ejercicio = '$fEjercicio'";
if (trim($fIdSubSector)) $filtro .= " AND ss.IdSubSector = '$fIdSubSector'";
if (trim($fIdPrograma)) $filtro .= " AND pr.IdPrograma = '$fIdPrograma'";
if (trim($fIdSubPrograma)) $filtro .= " AND spr.IdSubPrograma = '$fIdSubPrograma'";
if (trim($fIdProyecto)) $filtro .= " AND py.IdProyecto = '$fIdProyecto'";
if (trim($fIdActividad)) $filtro .= " AND a.IdActividad = '$fIdActividad'";
if (trim($fCodUnidadEjec)) $filtro .= " AND ue.CodUnidadEjec = '$fCodUnidadEjec'";
if (trim($fCodDependencia)) $filtro .= " AND ued.CodDependencia = '$fCodDependencia'";
$sql = "SELECT
            o.Organismo,
            o.Logo,
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
        global $field_meta;
        global $_POST;
        extract($_POST);
        ##  
        $this->Image($_PARAMETRO['PATHLOGO'].$field['Logo'], 10, 12, 30, 23);
        $this->SetFont('Arial','B',10);
        $this->SetXY(40,15); $this->Cell(150, 5, utf8_decode($field['Organismo']), 0, 1, 'L');
        $this->SetXY(40,20); $this->Cell(150, 5, utf8_decode('OFICINA DE PLANIFICACIÓN Y PRESUPUESTO'), 0, 1, 'L');
        $this->SetXY(40,25); $this->Cell(150, 5, utf8_decode('ENTIDAD FEDERAL: '.$field['NomEstado']), 0, 1, 'L');
        $this->SetXY(40,30); $this->Cell(150, 5, utf8_decode('MUNICIPIO: '.$field['Municipio']), 0, 1, 'L');
        $this->SetFont('Arial','BI',25);
        $this->SetXY(170,20); $this->Cell(100, 5, utf8_decode('PLAN OPERATIVO'), 0, 1, 'C');
        $this->SetFont('Arial','B',10);
        $this->SetXY(170,28); $this->Cell(100, 5, utf8_decode('Vinculación Plan Presupuesto'), 0, 1, 'C');
        ##  
        $this->SetY(40);
    }
    
    //  Pie de página.
    function Footer() {
    }
}
//---------------------------------------------------

//---------------------------------------------------
//  Creación del objeto de la clase heredada.
$pdf = new PDF('L', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->SetMargins(10, 10, 50);
$pdf->SetAutoPageBreak(1, 5);
//---------------------------------------------------
$i = 0;
$Grupo = '';
$GrupoUnidad = '';
$MontoTotal = 0;
$NroDetalle = 0;
$sql = "SELECT
            cp.CodOrganismo,
            rfm.Ejercicio,
            s.CodSector,
            ss.IdSubSector,
            ss.CodClaSectorial,
            pr.IdPrograma,
            pr.CodPrograma,
            a.IdActividad,
            a.CodActividad,
            op.CodObjetivo,
            op.NroObjetivo,
            ue.CodUnidadEjec,
            ue.Denominacion As UnidadEjecutora,
            mp.CodMeta,
            mp.NroMeta,
            rfm.Descripcion AS Meta,
            rfmd.cod_partida,
            rfmd.Descripcion,
            rfmd.CodUnidad,
            rfmd.Cantidad,
            rfmd.PrecioUnitario,
            (SELECT COUNT(*) FROM pv_reformulacionmetasdet WHERE CodMeta = rfm.CodMeta AND Ejercicio = rfm.Ejercicio) AS NroDetalle
        FROM
            pv_reformulacionmetas rfm
            INNER JOIN pv_reformulacionmetasdet rfmd ON (rfmd.CodMeta = rfm.CodMeta AND rfmd.Ejercicio = rfm.Ejercicio)
            INNER JOIN pv_metaspoa mp ON (mp.CodMeta = rfm.CodMeta)
            INNER JOIN pv_objetivospoa op ON (op.CodObjetivo = mp.CodObjetivo)
            INNER JOIN pv_categoriaprog cp ON (cp.CategoriaProg = op.CategoriaProg)
            INNER JOIN pv_actividades a ON (a.IdActividad = cp.IdActividad)
            INNER JOIN pv_proyectos py ON (py.IdProyecto = a.IdProyecto)
            INNER JOIN pv_subprogramas spr ON (spr.IdSubPrograma = py.IdSubPrograma)
            INNER JOIN pv_programas pr ON (pr.IdPrograma = spr.IdPrograma)
            INNER JOIN pv_subsector ss ON (ss.IdSubSector = pr.IdSubSector)
            INNER JOIN pv_sector s ON (s.CodSector = ss.CodSector)
            INNER JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = cp.CodUnidadEjec)
            LEFT JOIN pv_unidadejecutoradep ued ON (ued.CodUnidadEjec = ue.CodUnidadEjec)
        WHERE 1 $filtro
        ORDER BY CodOrganismo, Ejercicio, CodSector, IdSubSector, IdPrograma, IdActividad, NroObjetivo, NroMeta";
$field_detalle = getRecords($sql);
foreach ($field_detalle as $f) {
    $field_meta = $f;
    if ($Grupo != $f['CodMeta']) {
        $NroDetalle += $f['NroDetalle'];
        if ($Grupo) {
            $y = $pdf->GetY();
            $pdf->SetDrawColor(0,0,0);
            ##  
            $pdf->SetFont('Arial','B',8);
            $pdf->Cell(224, 5, utf8_decode('TOTAL'), 1, 0, 'R');
            $pdf->Cell(35, 5, number_format($MontoTotal,2,',','.'), 1, 0, 'R');
            $MontoTotal = 0;
        }
        ##  
        $Grupo = $f['CodMeta'];
        ++$i;
        ##  
        if ($i == 1 || $pdf->GetY() > 130 || $GrupoUnidad != $f['CodUnidadEjec'] || $NroDetalle > 11) {
            $pdf->AddPage();
            $NroDetalle = 0;
            $NroDetalle += $f['NroDetalle'];
            $pdf->SetY(40);
            if ($pdf->GetY() > 130) {
                $i = 0;
            }
        } else {
            $pdf->Ln(15);
            $i = 0;
        }
        $GrupoUnidad = $f['CodUnidadEjec'];
        ##  
        $pdf->SetFont('Arial','B',8);
        $pdf->SetDrawColor(0,0,0);
        $pdf->SetFillColor(255,255,255);
        $pdf->SetWidths(array(23,23,23,23,23,118,26));
        $pdf->SetAligns(array('C','C','C','C','C','L','C'));
        $pdf->Row(array(utf8_decode($NroDetalle.'SECTOR'),
                         utf8_decode('PROGRAMA'),
                         utf8_decode('ACTIVIDAD'),
                         utf8_decode('OBJETIVO'),
                         utf8_decode('META'),
                         utf8_decode('UNIDAD EJECUTORA'),
                         utf8_decode('AÑO DEL PLAN')
                ));
        $pdf->SetFont('Arial','',8);
        $pdf->Row(array(utf8_decode($field_meta['CodClaSectorial']),
                         utf8_decode($field_meta['CodPrograma']),
                         utf8_decode($field_meta['CodActividad']),
                         utf8_decode($field_meta['NroObjetivo']),
                         utf8_decode($field_meta['NroMeta']),
                         utf8_decode($field_meta['UnidadEjecutora']),
                         utf8_decode($field_meta['Ejercicio']),
                ));
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(259, 5, utf8_decode('JUSTIFICACIÓN'), 1, 1, 'L');
        $pdf->SetFont('Arial','',8);
        $pdf->MultiCell(259, 5, utf8_decode($field_meta['Meta'].'                                     '), 1, 'FJ');
        $pdf->SetFont('Arial','B',8);
        $pdf->SetX(30); $pdf->Cell(30, 5, utf8_decode('SUBPARTIDA'), 1, 0, 'C');
        $pdf->SetX(199); $pdf->Cell(70, 5, utf8_decode('COSTO'), 1, 0, 'C');
        $pdf->Ln();
        $pdf->SetDrawColor(0,0,0);
        $pdf->SetWidths(array(20,10,10,10,104,35,35,35));
        $pdf->SetAligns(array('C','C','C','C','L','C','C','C'));
        $pdf->Row(array(utf8_decode('PARTIDA'),
                         utf8_decode('GE'),
                         utf8_decode('ES'),
                         utf8_decode('SE'),
                         utf8_decode('DESCRIPCIÓN'),
                         utf8_decode('CANTIDAD PROGRAMADA'),
                         utf8_decode('UNITARIO'),
                         utf8_decode('TOTAL')
                ));
        $pdf->SetAligns(array('C','C','C','C','L','R','R','R','R','R','R'));
        $pdf->SetWidths(array(20,10,10,10,104,10,25,7,28,7,28));
    }
    $Total = $f['Cantidad'] * $f['PrecioUnitario'];
    ##  
    $y1 = $pdf->GetY();
    $pdf->Ln(1);
    $pdf->SetDrawColor(255,255,255);
    $pdf->SetFillColor(255,255,255);
    $pdf->SetFont('Arial','',8);
    $pdf->SetWidths(array(20,10,10,10,104,10,25,7,28,7,28));
    $pdf->SetAligns(array('C','C','C','C','L','R','R','R','R','R','R'));
    $pdf->Row(array(substr($f['cod_partida'],0,3),
                    substr($f['cod_partida'],4,2),
                    substr($f['cod_partida'],7,2),
                    substr($f['cod_partida'],10,2),
                    utf8_decode($f['Descripcion']),
                    number_format($f['Cantidad'],2,',','.'),
                    utf8_decode($f['CodUnidad']?$f['CodUnidad']:'Partida'),
                    utf8_decode('Bs.'),
                    number_format($f['PrecioUnitario'],2,',','.'),
                    utf8_decode('Bs.'),
                    number_format($Total,2,',','.')
                ));
    //$pdf->Ln(1);
    $y2 = $pdf->GetY();
    $pdf->SetDrawColor(0,0,0);
    $pdf->Rect(10, $y1, 0.1, $y2-$y1, "FD");
    $pdf->Rect(30, $y1, 0.1, $y2-$y1, "FD");
    $pdf->Rect(40, $y1, 0.1, $y2-$y1, "FD");
    $pdf->Rect(50, $y1, 0.1, $y2-$y1, "FD");
    $pdf->Rect(60, $y1, 0.1, $y2-$y1, "FD");
    $pdf->Rect(164, $y1, 0.1, $y2-$y1, "FD");
    $pdf->Rect(199, $y1, 0.1, $y2-$y1, "FD");
    $pdf->Rect(234, $y1, 0.1, $y2-$y1, "FD");
    $pdf->Rect(269, $y1, 0.1, $y2-$y1, "FD");
    ##  
    $MontoTotal += $Total;
}
$y = $pdf->GetY();
$pdf->SetDrawColor(0,0,0);
##  
$pdf->SetFont('Arial','B',8);
$pdf->Cell(224, 5, utf8_decode('TOTAL'), 1, 0, 'R');
$pdf->Cell(35, 5, number_format($MontoTotal,2,',','.'), 1, 0, 'R');
$MontoTotal = 0;
//---------------------------------------------------

//---------------------------------------------------
//  Muestro el contenido del pdf.
$pdf->Output();
?>