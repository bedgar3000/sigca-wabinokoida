<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$titulo = true;
$filtro = '';
$filtrod = '';
if (trim($fCodOrganismo)) $filtro .= " AND aj.CodOrganismo = '$fCodOrganismo'";
if (trim($fPeriodoD)) $filtro .= " AND aj.Periodo >= '$fPeriodoD'";
if (trim($fPeriodoH)) $filtro .= " AND aj.Periodo <= '$fPeriodoH'";
if (trim($fCodDependencia)) $filtro .= " AND cc.CodDependencia = '$fCodDependencia'";
if (trim($fCodUnidadEjec)) $filtro .= " AND cp.CodUnidadEjec = '$fCodUnidadEjec'";
if (trim($fTipo)) $filtro .= " AND aj.Tipo = '$fTipo'";
if (trim($fCategoriaProg)) $filtro .= " AND cp.CategoriaProg = '$fCategoriaProg'";
if (trim($fPar)) $filtrod .= " AND SUBSTRING(ajd.cod_partida, 1, 3) = '$fPar'";
if (trim($fGen)) $filtrod .= " AND SUBSTRING(ajd.cod_partida, 1, 6) = '$fPar.$fGen'";
if (trim($fEsp)) $filtrod .= " AND SUBSTRING(ajd.cod_partida, 1, 9) = '$fPar.$fGen.$fEsp'";
if (trim($fSub)) $filtrod .= " AND SUBSTRING(ajd.cod_partida, 1, 12) = '$fPar.$fGen.$fEsp.$fSub'";
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
        global $FechaActual;
        global $field;
        global $fc;
        global $titulo;
        global $_POST;
        extract($_POST);
        ##  
        $Logo = getValorCampo("mastorganismos", "CodOrganismo", "Logo", $fCodOrganismo);
        $NomOrganismo = getValorCampo("mastorganismos", "CodOrganismo", "Organismo", $fCodOrganismo);
        $NomDependencia = getValorCampo("mastdependencias", "CodDependencia", "Dependencia", $_PARAMETRO["CODMEMBPV"]);
        ##
        $this->SetFillColor(255, 255, 255);
        $this->SetDrawColor(0, 0, 0);
        $this->SetFont('Arial', 'B', 8);
        $this->Image($_PARAMETRO["PATHLOGO"].$Logo, 10, 10, 10, 10);
        $this->SetX(25, 5); $this->Cell(175, 5, strtoupper(utf8_decode($NomOrganismo)), 0, 1, 'L');
        $this->SetX(25, 5); $this->Cell(175, 5, strtoupper(utf8_decode($NomDependencia)), 0, 1, 'L');
        $this->SetFont('Arial', '', 8);
        $this->SetXY(240, 10); $this->Cell(10, 5, utf8_decode('Fecha: '), 0, 0, 'L');
        $this->Cell(30, 5, formatFechaDMA($FechaActual), 0, 1, 'L');
        $this->SetXY(240, 15); $this->Cell(10, 5, utf8_decode('Página: '), 0, 0, 'L');
        $this->Cell(30, 5, $this->PageNo().' de {nb}', 0, 1, 'L');
        ##  -------------------
        $this->SetY(25); 
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(257, 5, strtoupper(utf8_decode('AJUSTES PRESUPUESTARIOS RESUMEN DETALLADO')), 0, 1, 'C');
        $this->Ln(5);
        ##  
        if ($titulo) {
            $this->SetFont('Arial','B',6);
            $this->SetDrawColor(0,0,0);
            $this->SetFillColor(255,255,255);
            $this->SetWidths(array(15,15,40,140,30,20));
            $this->SetAligns(array('C','C','C','L','C','C'));
            $this->Row(array(utf8_decode('AJUSTE'),
                             utf8_decode('FECHA'),
                             utf8_decode('TIPO'),
                             utf8_decode('MOTIVO'),
                             utf8_decode('NRO. RESOLUCIÓN'),
                             utf8_decode('FECHA RESOLUCIÓN')
                    ));
            $this->Ln(1);
        } else {
            $this->SetX(40);
        }
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
$pdf->SetAutoPageBreak(1, 25);
$pdf->AddPage();
//---------------------------------------------------
$sql = "SELECT
            aj.CodOrganismo,
            aj.CodAjuste,
            aj.Fecha,
            aj.Descripcion,
            aj.NroResolucion,
            aj.FechaResolucion,
            md.Descripcion AS NomTipo
        FROM
            pv_ajustes aj
        INNER JOIN pv_ajustesdet ajd ON (
            ajd.CodOrganismo = aj.CodOrganismo
            AND ajd.CodAjuste = aj.CodAjuste
        )
        LEFT JOIN mastmiscelaneosdet md ON (
            md.CodDetalle = aj.Tipo
            AND md.CodMaestro = 'TIPOAJUSTE'
        )
        WHERE
            aj.Estado = 'AP' $filtro $filtrod
        GROUP BY
            aj.CodOrganismo,
            aj.CodAjuste
        ORDER BY
            aj.CodAjuste";
$field_ajuste = getRecords($sql);
foreach ($field_ajuste as $f) {
    $titulo = true;
    ##  
    $sql = "SELECT
                CONCAT(
                    ss.CodSector,
                    pr.CodPrograma,
                    a.CodActividad
                ) AS CatProg,
                ajd.CodFuente,
                ajd.cod_partida,
                ajd.MontoDisponible,
                ajd.MontoAjuste,
                pv.denominacion AS Partida
            FROM
                pv_ajustesdet ajd
                INNER JOIN pv_partida pv ON (
                    pv.cod_partida = ajd.cod_partida
                )
                INNER JOIN pv_presupuestodet pptod ON (
                    pptod.CodOrganismo = ajd.CodOrganismo
                    AND pptod.CodPresupuesto = ajd.CodPresupuesto
                    AND pptod.CodFuente = ajd.CodFuente
                    AND pptod.cod_partida = ajd.cod_partida
                )
                INNER JOIN pv_presupuesto ppto ON (
                    ppto.CodOrganismo = pptod.CodOrganismo
                    AND ppto.CodPresupuesto = pptod.CodPresupuesto
                )
                INNER JOIN pv_categoriaprog cp ON (
                    cp.CategoriaProg = ppto.CategoriaProg
                )
                INNER JOIN pv_unidadejecutora ue ON (
                    ue.CodUnidadEjec = cp.CodUnidadEjec
                )
                INNER JOIN pv_actividades a ON (
                    a.IdActividad = cp.IdActividad
                )
                INNER JOIN pv_proyectos py ON (
                    py.IdProyecto = a.IdProyecto
                )
                INNER JOIN pv_subprogramas spr ON (
                    spr.IdSubPrograma = py.IdSubPrograma
                )
                INNER JOIN pv_programas pr ON (
                    pr.IdPrograma = spr.IdPrograma
                )
                INNER JOIN pv_subsector ss ON (
                    ss.IdSubSector = pr.IdSubSector
                )
                INNER JOIN pv_sector s ON (
                    s.CodSector = ss.CodSector
                )
                LEFT JOIN ac_mastcentrocosto cc ON (
                    cc.CodCentroCosto = ue.CodCentroCosto
                )
            WHERE
                ajd.CodOrganismo = '$f[CodOrganismo]'
                AND ajd.CodAjuste = '$f[CodAjuste]'
                AND ajd.Tipo = 'D'
            ORDER BY
                CatProg,
                CodFuente,
                cod_partida";
    $field_cedentes = getRecords($sql);
    ##  
    $sql = "SELECT
                CONCAT(
                    ss.CodSector,
                    pr.CodPrograma,
                    a.CodActividad
                ) AS CatProg,
                ajd.CodFuente,
                ajd.cod_partida,
                ajd.MontoDisponible,
                ajd.MontoAjuste,
                pv.denominacion AS Partida
            FROM
                pv_ajustesdet ajd
                INNER JOIN pv_partida pv ON (
                    pv.cod_partida = ajd.cod_partida
                )
                INNER JOIN pv_presupuestodet pptod ON (
                    pptod.CodOrganismo = ajd.CodOrganismo
                    AND pptod.CodPresupuesto = ajd.CodPresupuesto
                    AND pptod.CodFuente = ajd.CodFuente
                    AND pptod.cod_partida = ajd.cod_partida
                )
                INNER JOIN pv_presupuesto ppto ON (
                    ppto.CodOrganismo = pptod.CodOrganismo
                    AND ppto.CodPresupuesto = pptod.CodPresupuesto
                )
                INNER JOIN pv_categoriaprog cp ON (
                    cp.CategoriaProg = ppto.CategoriaProg
                )
                INNER JOIN pv_unidadejecutora ue ON (
                    ue.CodUnidadEjec = cp.CodUnidadEjec
                )
                INNER JOIN pv_actividades a ON (
                    a.IdActividad = cp.IdActividad
                )
                INNER JOIN pv_proyectos py ON (
                    py.IdProyecto = a.IdProyecto
                )
                INNER JOIN pv_subprogramas spr ON (
                    spr.IdSubPrograma = py.IdSubPrograma
                )
                INNER JOIN pv_programas pr ON (
                    pr.IdPrograma = spr.IdPrograma
                )
                INNER JOIN pv_subsector ss ON (
                    ss.IdSubSector = pr.IdSubSector
                )
                INNER JOIN pv_sector s ON (
                    s.CodSector = ss.CodSector
                )
                LEFT JOIN ac_mastcentrocosto cc ON (
                    cc.CodCentroCosto = ue.CodCentroCosto
                )
            WHERE
                ajd.CodOrganismo = '$f[CodOrganismo]'
                AND ajd.CodAjuste = '$f[CodAjuste]'
                AND ajd.Tipo = 'I'
            ORDER BY
                CatProg,
                CodFuente,
                cod_partida";
    $field_receptoras = getRecords($sql);
    ##  
    $h = 5 * count($field_cedentes);
    $m = 184 - $h - 5 - 5;
    if ($pdf->GetY() >= $m && count($field_cedentes) < 10) $pdf->AddPage();
    ##  
    $pdf->SetFont('Arial','B',6);
    $pdf->SetDrawColor(255,255,255);
    $pdf->SetFillColor(255,255,255);
    $pdf->SetWidths(array(15,15,40,140,30,20));
    $pdf->SetAligns(array('C','C','C','L','C','C'));
    $pdf->Row(array(utf8_decode($f['CodAjuste']),
                    formatFechaDMA($f['Fecha']),
                    utf8_decode($f['NomTipo']),
                    utf8_decode($f['Descripcion']),
                    utf8_decode($f['NroResolucion']),
                    formatFechaDMA($f['FechaResolucion'])
                ));
    ##  Cedentes
    if (count($field_cedentes)) {
        $titulo = false;
        $pdf->SetX(40);
        $pdf->SetFont('Arial','B',6);
        $pdf->Cell(257, 5, utf8_decode('Cedentes'), 0, 1, 'L');
        $pdf->SetX(40);
        $pdf->SetFont('Arial','',6);
        $pdf->SetDrawColor(0,0,0);
        $pdf->SetFillColor(255,255,255);
        $pdf->SetWidths(array(15,10,20,92,30,30));
        $pdf->SetAligns(array('C','C','C','L','R','R'));
        $pdf->Row(array(utf8_decode('Cat. Prog.'),
                        utf8_decode('F.F.'),
                        utf8_decode('Partida'),
                        utf8_decode('Denominación'),
                        utf8_decode('Monto Disponible'),
                        utf8_decode('Monto Aprobado')
                ));
        foreach ($field_cedentes as $fd) {
            $pdf->SetX(40);
            $pdf->SetFont('Arial','',6);
            $pdf->SetDrawColor(0,0,0);
            $pdf->SetFillColor(255,255,255);
            $pdf->SetWidths(array(15,10,20,92,30,30));
            $pdf->SetAligns(array('C','C','C','L','R','R'));
            $pdf->Row(array($m.utf8_decode($fd['CatProg']),
                            utf8_decode($fd['CodFuente']),
                            utf8_decode($fd['cod_partida']),
                            utf8_decode($fd['Partida']),
                            number_format($fd['MontoDisponible'],2,',','.'),
                            number_format($fd['MontoAjuste'],2,',','.')
                    ));
        }
    }
    ##  Receptoras
    if (count($field_receptoras)) {
        $h = 5 * count($field_receptoras);
        $m = 184 - $h - 5;
        if ($pdf->GetY() >= $m && count($field_receptoras) < 10) $pdf->AddPage();
        ##  
        $titulo = false;
        $pdf->SetX(40);
        $pdf->SetFont('Arial','B',6);
        $pdf->Cell(257, 5, utf8_decode('Receptoras'), 0, 1, 'L');
        $pdf->SetX(40);
        $pdf->SetFont('Arial','',6);
        $pdf->SetDrawColor(0,0,0);
        $pdf->SetFillColor(255,255,255);
        $pdf->SetWidths(array(15,10,20,92,30,30));
        $pdf->SetAligns(array('C','C','C','L','R','R'));
        $pdf->Row(array(utf8_decode('Cat. Prog.'),
                        utf8_decode('F.F.'),
                        utf8_decode('Partida'),
                        utf8_decode('Denominación'),
                        utf8_decode('Monto Disponible'),
                        utf8_decode('Monto Aprobado')
                ));
        foreach ($field_receptoras as $fd) {
            $pdf->SetX(40);
            $pdf->SetFont('Arial','',6);
            $pdf->SetDrawColor(0,0,0);
            $pdf->SetFillColor(255,255,255);
            $pdf->SetWidths(array(15,10,20,92,30,30));
            $pdf->SetAligns(array('C','C','C','L','R','R'));
            $pdf->Row(array($m.utf8_decode($fd['CatProg']),
                            utf8_decode($fd['CodFuente']),
                            utf8_decode($fd['cod_partida']),
                            utf8_decode($fd['Partida']),
                            number_format($fd['MontoDisponible'],2,',','.'),
                            number_format($fd['MontoAjuste'],2,',','.')
                    ));
        }
    }
    $pdf->Ln(6);
}
//---------------------------------------------------

//---------------------------------------------------
//  Muestro el contenido del pdf.
$pdf->Output();
?>