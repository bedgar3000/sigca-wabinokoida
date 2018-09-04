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
        $this->SetXY(170,28); $this->Cell(100, 5, utf8_decode('Matriz Lógica'), 0, 1, 'C');
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
$Grupo = '';
$GrupoUnidad = '';
$i = 0;
$sql = "SELECT
            cp.CodOrganismo,
            rfm.Ejercicio,
            s.CodSector,
            ss.IdSubSector,
            ss.CodClaSectorial,
            pr.IdPrograma,
            pr.CodPrograma,
            pr.Denominacion AS Programa,
            a.IdActividad,
            a.CodActividad,
            a.Denominacion AS Actividad,
            op.CodObjetivo,
            op.NroObjetivo,
            op.Descripcion AS Objetivo,
            ue.CodUnidadEjec,
            ue.Denominacion As UnidadEjecutora,
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
        GROUP BY CodOrganismo, Ejercicio, CodSector, IdSubSector, IdPrograma, IdActividad, CodObjetivo
        ORDER BY CodOrganismo, Ejercicio, CodSector, IdSubSector, CodPrograma, CodActividad, NroObjetivo";
$field_detalle = getRecords($sql);
foreach ($field_detalle as $f) {
    $field_objetivo = $f;
    ##  indicadores
    $sql = "SELECT mpi.Descripcion
            FROM
                pv_metaspoaindicadores mpi
                INNER JOIN pv_metaspoa mp ON (mpi.CodMeta = mp.CodMeta)
                INNER JOIN pv_objetivospoa op ON (op.CodObjetivo = mp.CodObjetivo)
            WHERE mp.CodObjetivo = '$f[CodObjetivo]'";
    $field_indicadores = getRecords($sql);
    $indicadores = '';
    foreach ($field_indicadores as $fi) {
        $indicadores .= $fi['Descripcion'].$nl;
    }
    ##  indicadores
    $sql = "SELECT mp.MedioVerificacion1, mp.MedioVerificacion2
            FROM
                pv_metaspoa mp
                INNER JOIN pv_objetivospoa op ON (op.CodObjetivo = mp.CodObjetivo)
            WHERE mp.CodObjetivo = '$f[CodObjetivo]'";
    $field_medios = getRecords($sql);
    $medios = '';
    foreach ($field_medios as $fi) {
        $medios .= $fi['MedioVerificacion1'].$nl.$fi['MedioVerificacion2'].$nl;
    }
    ##  
    if ($Grupo != $f['CodObjetivo']) {
        $Grupo != $f['CodObjetivo'];
        ++$i;
        if ($i == 1 || $pdf->GetY() > 130 || $GrupoUnidad != $f['CodUnidadEjec']) {
            $pdf->AddPage();
            $pdf->SetY(40);
            if ($pdf->GetY() > 130) $i = 0;
        } else {
            $pdf->Ln(15);
            $i = 0;
        }
        $GrupoUnidad = $f['CodUnidadEjec'];
        ##  
        $pdf->SetFont('Arial','B',8);
        $pdf->SetDrawColor(0,0,0);
        $pdf->SetFillColor(255,255,255);
        $pdf->SetWidths(array(23,23,23,23,141,26));
        $pdf->SetAligns(array('C','C','C','C','L','C'));
        $pdf->Row(array(utf8_decode('SECTOR'),
                         utf8_decode('PROGRAMA'),
                         utf8_decode('ACTIVIDAD'),
                         utf8_decode('OBJETIVO'),
                         utf8_decode('UNIDAD EJECUTORA'),
                         utf8_decode('AÑO DEL PLAN')
                ));
        $pdf->SetFont('Arial','',8);
        $pdf->Row(array(utf8_decode($field_objetivo['CodClaSectorial']),
                         utf8_decode($field_objetivo['CodPrograma']),
                         utf8_decode($field_objetivo['CodActividad']),
                         utf8_decode($field_objetivo['NroObjetivo']),
                         utf8_decode($field_objetivo['UnidadEjecutora']),
                         utf8_decode($field_objetivo['Ejercicio']),
                ));
        $pdf->SetFont('Arial','B',8);
        $pdf->SetWidths(array(129,65,65));
        $pdf->SetAligns(array('FJ','L','L'));
        $pdf->Row(array(utf8_decode('DESCRIPCIÓN'),
                         utf8_decode('INDICADORES'),
                         utf8_decode('MEDIOS DE VERIFICACIÓN')
                ));
        ##  
        $pdf->SetDrawColor(0,0,0);
        $pdf->SetFillColor(255,255,255);
        $pdf->SetFont('Arial','',8);
        $pdf->SetAligns(array('FJ','L','L'));
        $pdf->Row(array(utf8_decode($f['Objetivo'].'                               '),
                        utf8_decode($indicadores),
                        utf8_decode($medios)
                    ));
        $MontoPresupuestado = $f['MontoPresupuestado'];
        $CodPrograma = $f['CodPrograma'];
        $Programa = $f['Programa'];
        $CodActividad = $f['CodActividad'];
        $Actividad = $f['Actividad'];
        ##  
        $y = $pdf->GetY();
        $pdf->SetFont('Arial','B',10);
        $pdf->SetY($y);
        $pdf->MultiCell(46, 5, utf8_decode('ASIGNACIÓN PRESUPUESTARIA'), 0, 'L');
        $pdf->SetFont('Arial','',8);
        ##  
        $pdf->SetY($y);
        $pdf->SetX(56);
        $pdf->Cell(18, 5, utf8_decode('Programa: '), 0, 0, 'L');
        $pdf->MultiCell(100, 5, utf8_decode($CodPrograma.' '.$Programa), 0, 'L');
        $pdf->Ln(1);
        $pdf->SetX(56);
        $pdf->Cell(18, 5, utf8_decode('Actividad: '), 0, 0, 'L');
        $pdf->MultiCell(130, 5, utf8_decode($CodActividad.' '.$Actividad), 0, 'L');
        ##  
        $pdf->SetY($y);
        $pdf->SetX(205);
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(100, 5, utf8_decode('Presupuesto Asignado: '), 0, 1, 'L');
        $pdf->Ln(1);
        $pdf->SetX(205);
        $pdf->Cell(35, 5, utf8_decode('Bs. '), 0, 0, 'L');
        $pdf->MultiCell(65, 5, number_format($MontoPresupuestado,2,',','.'), 0, 'L');
        ##  
        $pdf->SetDrawColor(0,0,0);
        $pdf->SetFillColor(0,0,0);
        $pdf->Rect(10, $y, 46, 12, "D");
        $pdf->Rect(56, $y, 148, 12, "D");
        $pdf->Rect(204, $y, 65, 12, "D");

    }
}
//---------------------------------------------------

//---------------------------------------------------
//  Muestro el contenido del pdf.
$pdf->Output();
?>