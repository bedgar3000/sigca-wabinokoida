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
        global $field_objetivo;
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
        $this->SetXY(170,28); $this->Cell(100, 5, utf8_decode('Metas y Volúmenes de Trabajo'), 0, 1, 'C');
        ##  
        $this->SetY(40);
        $this->SetFont('Arial','B',8);
        $this->SetDrawColor(0,0,0);
        $this->SetFillColor(255,255,255);
        $this->SetWidths(array(23,23,23,23,141,26));
        $this->SetAligns(array('C','C','C','C','L','C'));
        $this->Row(array(utf8_decode('SECTOR'),
                         utf8_decode('PROGRAMA'),
                         utf8_decode('ACTIVIDAD'),
                         utf8_decode('OBJETIVO'),
                         utf8_decode('UNIDAD EJECUTORA'),
                         utf8_decode('AÑO DEL PLAN')
                ));
        $this->SetFont('Arial','',8);
        $this->Row(array(utf8_decode($field_objetivo['CodClaSectorial']),
                         utf8_decode($field_objetivo['CodPrograma']),
                         utf8_decode($field_objetivo['CodActividad']),
                         utf8_decode($field_objetivo['NroObjetivo']),
                         utf8_decode($field_objetivo['UnidadEjecutora']),
                         utf8_decode($field_objetivo['Ejercicio']),
                ));
        $this->SetY(50);
        $this->SetFont('Arial','',8);
        $this->MultiCell(259, 5, utf8_decode($field_objetivo['Objetivo'].'                                     '), 0, 'FJ');
        $this->SetY(65);
        $this->SetFont('Arial','B',8);
        $this->SetWidths(array(23,130,61,45));
        $this->SetAligns(array('C','FJ','C','R'));
        $this->Row(array(utf8_decode('META'),
                         utf8_decode('DESCRIPCIÓN'),
                         utf8_decode('UNIDAD DE MEDIDA'),
                         utf8_decode('PRESUPUESTADO ASIGNADO')
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
        $this->Rect(10, 50, 259, 150, "D");
        $this->Rect(33, 65, 0.1, 135, "FD");
        $this->Rect(163, 65, 0.1, 135, "FD");
        $this->Rect(224, 65, 0.1, 135, "FD");
        ##  
        if ($FlagTotal) {
            $this->SetY(200);
            $this->SetDrawColor(0,0,0);
            $this->SetFont('Arial','B',8);
            $this->Cell(214, 5, utf8_decode('TOTAL'), 1, 0, 'R');
            $this->Cell(45, 5, number_format($MontoTotal,2,',','.'), 1, 0, 'R');
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
            op.Descripcion AS Objetivo,
            ue.Denominacion As UnidadEjecutora,
            mp.CodMeta,
            mp.NroMeta,
            rfm.Descripcion AS Meta,
            mp.MedioVerificacion1,
            mp.MedioVerificacion2,
            SUM((rfmd.PrecioUnitario + rfmd.MontoIva) * rfmd.Cantidad) AS MontoPresupuestado
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
        GROUP BY CodOrganismo, Ejercicio, CodSector, IdSubSector, IdPrograma, IdActividad, CodObjetivo, CodMeta
        ORDER BY CodOrganismo, Ejercicio, CodSector, IdSubSector, IdPrograma, IdActividad, NroObjetivo, NroMeta";
$field_detalle = getRecords($sql);
foreach ($field_detalle as $f) {
    $field_objetivo = $f;
    if ($Grupo != $f['CodObjetivo']) {
        $Grupo = $f['CodObjetivo'];
        ##  
        if ($Grupo) $FlagTotal = true;
        $pdf->AddPage();
        ##  
        $FlagTotal = false;
        $MontoTotal = 0;
    }
    $pdf->SetDrawColor(255,255,255);
    $pdf->SetFillColor(255,255,255);
    $pdf->SetFont('Arial','',8);
    $pdf->SetAligns(array('C','FJ','L','R'));
    $pdf->Row(array(utf8_decode($f['NroMeta']),
                    utf8_decode($f['Meta'].'                               '),
                    utf8_decode($f['MedioVerificacion1'].$nl.$f['MedioVerificacion2']),
                    number_format($f['MontoPresupuestado'],2,',','.')
                ));
    $pdf->Ln(1);
    ##  
    $MontoTotal += $f['MontoPresupuestado'];
}
$FlagTotal = true;
//---------------------------------------------------

//---------------------------------------------------
//  Muestro el contenido del pdf.
$pdf->Output();
?>