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
        $this->SetFont('Arial','B',8);
        $this->SetDrawColor(0,0,0);
        $this->SetFillColor(255,255,255);
        $this->SetWidths(array(23,23,23,23,23,118,26));
        $this->SetAligns(array('C','C','C','C','C','L','C'));
        $this->Row(array(utf8_decode('SECTOR'),
                         utf8_decode('PROGRAMA'),
                         utf8_decode('ACTIVIDAD'),
                         utf8_decode('OBJETIVO'),
                         utf8_decode('META'),
                         utf8_decode('UNIDAD EJECUTORA'),
                         utf8_decode('AÑO DEL PLAN')
                ));
        $this->SetFont('Arial','',8);
        $this->Row(array(utf8_decode($field_meta['CodClaSectorial']),
                         utf8_decode($field_meta['CodPrograma']),
                         utf8_decode($field_meta['CodActividad']),
                         utf8_decode($field_meta['NroObjetivo']),
                         utf8_decode($field_meta['NroMeta']),
                         utf8_decode($field_meta['UnidadEjecutora']),
                         utf8_decode($field_meta['Ejercicio']),
                ));
        $this->SetY(50);
        $this->SetFont('Arial','B',8);
        $this->Cell(259, 5, utf8_decode('JUSTIFICACIÓN'), 1, 1, 'L');
        $this->SetY(55);
        $this->SetFont('Arial','',8);
        $this->MultiCell(259, 5, utf8_decode($field_meta['Meta'].'                                     '), 0, 'FJ');
        $this->SetFont('Arial','B',8);

        $this->SetXY(30,70); $this->Cell(30, 5, utf8_decode('SUBPARTIDA'), 0, 1, 'C');
        $this->SetXY(199,70); $this->Cell(70, 5, utf8_decode('COSTO'), 0, 1, 'C');

        $this->SetY(75);
        $this->SetDrawColor(255,255,255);
        $this->SetWidths(array(20,10,10,10,104,35,35,35));
        $this->SetAligns(array('C','C','C','C','L','C','C','C'));
        $this->Row(array(utf8_decode('PARTIDA'),
                         utf8_decode('GE'),
                         utf8_decode('ES'),
                         utf8_decode('SE'),
                         utf8_decode('DESCRIPCIÓN'),
                         utf8_decode('CANTIDAD PROGRAMADA'),
                         utf8_decode('UNITARIO'),
                         utf8_decode('TOTAL')
                ));
        $this->Ln(1);
        $this->SetAligns(array('C','C','C','C','L','R','R','R','R','R','R'));
        $this->SetWidths(array(20,10,10,10,104,10,25,7,28,7,28));
    }
    
    //  Pie de página.
    function Footer() {
        global $MontoTotal;
        global $FlagTotal;
        ##  
        $this->SetDrawColor(0,0,0);
        $this->SetFillColor(0,0,0);
        $this->Rect(10, 55, 259, 145, "D");
        $this->Rect(30, 70, 0.1, 130, "FD");
        $this->Rect(40, 75, 0.1, 125, "FD");
        $this->Rect(50, 75, 0.1, 125, "FD");
        $this->Rect(60, 70, 0.1, 130, "FD");
        $this->Rect(164, 70, 0.1, 130, "FD");
        $this->Rect(199, 70, 0.1, 130, "FD");
        $this->Rect(234, 75, 0.1, 125, "FD");
        $this->Rect(10, 70, 259, 0.1, "FD");
        $this->Rect(30, 75, 30, 0.1, "FD");
        $this->Rect(199, 75, 70, 0.1, "FD");
        $this->Rect(10, 85, 259, 0.1, "FD");
        ##  
        if ($FlagTotal) {
            $this->SetY(200);
            $this->SetDrawColor(0,0,0);
            $this->SetFont('Arial','B',8);
            $this->Cell(224, 5, utf8_decode('TOTAL'), 1, 0, 'R');
            $this->Cell(35, 5, number_format($MontoTotal,2,',','.'), 1, 0, 'R');
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
//---------------------------------------------------
$FlagTotal = false;
$Grupo = '';
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
            ue.Denominacion As UnidadEjecutora,
            mp.CodMeta,
            mp.NroMeta,
            rfm.Descripcion AS Meta,
            rfmd.cod_partida,
            rfmd.Descripcion,
            rfmd.CodUnidad,
            rfmd.Cantidad,
            rfmd.PrecioUnitario
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
        $Grupo = $f['CodMeta'];
        ##  
        if ($Grupo) $FlagTotal = true;
        $pdf->AddPage();
        ##  
        $FlagTotal = false;
        $MontoTotal = 0;
    }
    $Total = $f['Cantidad'] * $f['PrecioUnitario'];
    ##  
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
    $pdf->Ln(1);
    ##  
    $MontoTotal += $Total;
}
$FlagTotal = true;
//---------------------------------------------------

//---------------------------------------------------
//  Muestro el contenido del pdf.
$pdf->Output();
?>