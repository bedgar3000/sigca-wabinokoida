<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$filtro = '';
if (trim($fCodOrganismo)) $filtro .= " AND pca.CodOrganismo = '$fCodOrganismo'";
if (trim($fEjercicio)) $filtro .= " AND pca.Ejercicio = '$fEjercicio'";
if (trim($fIdSubSector)) $filtro .= " AND pca.IdSubSector = '$fIdSubSector'";
if (trim($fIdPrograma)) $filtro .= " AND pca.IdPrograma = '$fIdPrograma'";
if (trim($fIdSubPrograma)) $filtro .= " AND pca.IdSubPrograma = '$fIdSubPrograma'";
if (trim($fIdProyecto)) $filtro .= " AND pca.IdProyecto = '$fIdProyecto'";
if (trim($fIdActividad)) $filtro .= " AND pca.IdActividad = '$fIdActividad'";
if (trim($fCodUnidadEjec)) $filtro .= " AND pca.CodUnidadEjec = '$fCodUnidadEjec'";
if (trim($fCodDependencia)) $filtro.=" AND (ued.CodDependencia = '".$fCodDependencia."')";
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
        $this->Cell(195, 5, utf8_decode('CREDITOS PRESUPUESTARIOS A NIVEL DE SECTOR, PROGRAMA Y ACTIVIDAD'), 0, 1, 'C');
        $this->SetDrawColor(0, 0, 0);
        $this->Rect(10, 18, 195, 30, "D");
        ##  
        $this->SetY(50);
        $this->SetFont('Arial','B',6);
        $this->SetDrawColor(0,0,0);
        $this->SetFillColor(255,255,255);
        $this->SetWidths(array(8,8,8,8,8,8,8,109,30));
        $this->SetAligns(array('C','C','C','C','C','C','C','L','R'));
        $this->Row(array(utf8_decode('SEC'),
                         utf8_decode('PRO'),
                         utf8_decode('ACT'),
                         utf8_decode('PAR'),
                         utf8_decode('GEN'),
                         utf8_decode('ESP'),
                         utf8_decode('SUB'),
                         utf8_decode('DENOMINACION'),
                         utf8_decode('CREDITOS PRESUPUESTARIOS')
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
        $this->Rect(10, 60, 195, 190, "D");
        $this->Rect(18, 60, 0.1, 190, "FD");
        $this->Rect(26, 60, 0.1, 190, "FD");
        $this->Rect(34, 60, 0.1, 190, "FD");
        $this->Rect(42, 60, 0.1, 190, "FD");
        $this->Rect(50, 60, 0.1, 190, "FD");
        $this->Rect(58, 60, 0.1, 190, "FD");
        $this->Rect(66, 60, 0.1, 190, "FD");
        $this->Rect(175, 60, 0.1, 190, "FD");
        ##  
        if ($FlagTotal) {
            $this->SetY(250);
            $this->SetDrawColor(0,0,0);
            $this->SetFont('Arial','B',7);
            $this->Cell(165, 5, utf8_decode('TOTAL'), 1, 0, 'R');
            $this->Cell(30, 5, number_format($MontoTotal,2,',','.'), 1, 0, 'R');
        }
    }
}
//---------------------------------------------------

//---------------------------------------------------
//  Creación del objeto de la clase heredada.
$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->SetMargins(10, 10, 50);
$pdf->SetAutoPageBreak(1, 25);
if ($FlagAgrupar <> 'S') $pdf->AddPage();
//---------------------------------------------------
$FlagTotal = false;
$MontoTotal = 0;
$Grupo = '';
$i = 0;
##  
$sql = "SELECT
            pca.CodSector,
            pca.CodSubSector,
            pca.CodPrograma,
            pca.CodActividad,
            pca.cod_partida,
            pca.denominacion,
            SUM(pca.Monto) AS Monto,
            SUBSTRING(pca.cod_partida, 1, 3) AS Par,
            SUBSTRING(pca.cod_partida, 5, 2) AS Gen,
            SUBSTRING(pca.cod_partida, 8, 2) AS Esp,
            SUBSTRING(pca.cod_partida, 11, 2) AS Sub
        FROM
            vw_poa_consolidado_actividad pca
            INNER JOIN pv_unidadejecutora ue ON (ue.CodUnidadEjec = pca.CodUnidadEjec)
            LEFT JOIN pv_unidadejecutoradep ued ON (ued.CodUnidadEjec = ue.CodUnidadEjec)
        WHERE 1 $filtro
        GROUP BY CodSector, CodSubSector, CodPrograma, CodActividad, cod_partida
        ORDER BY CodSector, CodSubSector, CodPrograma, CodActividad, cod_partida";
$field_detalle = getRecords($sql);
foreach ($field_detalle as $f) {
    $id = $f['CodSector'].$f['CodPrograma'].$f['CodActividad'];
    if ($FlagAgrupar == 'S') {
        if ($Grupo != $id) {
            if ($Grupo) $FlagTotal = true;
            $Grupo = $id;
            ##  
            $pdf->AddPage();
            ##  
            $MontoTotal = 0;
        }
    }
    $pdf->SetDrawColor(255,255,255);
    $pdf->SetFillColor(255,255,255);
    $pdf->SetFont('Arial','',6);
    $pdf->Row(array(utf8_decode($f['CodSector']),
                    utf8_decode($f['CodPrograma']),
                    utf8_decode($f['CodActividad']),
                    utf8_decode($f['Par']),
                    utf8_decode($f['Gen']),
                    utf8_decode($f['Esp']),
                    utf8_decode($f['Sub']),
                    utf8_decode($f['denominacion']),
                    number_format($f['Monto'],2,',','.')
                ));
    $MontoTotal += $f['Monto'];
}
$FlagTotal = true;
//---------------------------------------------------

//---------------------------------------------------
//  Muestro el contenido del pdf.
$pdf->Output();
?>