<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$fPeriodoD = formatFechaAMD($fPeriodoD);
$fPeriodoH = formatFechaAMD($fPeriodoH);
$filtro = '';
if (trim($fCodOrganismo)) $filtro .= " AND pptod.CodOrganismo = '$fCodOrganismo'";
if (trim($fPar)) $filtro .= " AND SUBSTRING(pptod.cod_partida, 1, 3) = '$fPar'";
if (trim($fGen)) $filtro .= " AND SUBSTRING(pptod.cod_partida, 1, 6) = '$fPar.$fGen'";
if (trim($fEsp)) $filtro .= " AND SUBSTRING(pptod.cod_partida, 1, 9) = '$fPar.$fGen.$fEsp'";
if (trim($fSub)) $filtro .= " AND SUBSTRING(pptod.cod_partida, 1, 12) = '$fPar.$fGen.$fEsp.$fSub'";
if (trim($fCodDependencia)) $filtro .= " AND cc.CodDependencia = '$fCodDependencia'";
if (trim($fCodUnidadEjec)) $filtro .= " AND cp.CodUnidadEjec = '$fCodUnidadEjec'";
if (trim($fCategoriaProg)) $filtro .= " AND cp.CategoriaProg = '$fCategoriaProg'";
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
        $this->Cell(257, 5, strtoupper(utf8_decode('RESUMEN ESTADISTICO DE PARTIDAS CONSOLIDADO POR ORGANISMO')), 0, 1, 'C');
        $this->Ln(5);
        ##  
        $this->SetFont('Arial','B',6);
        $this->SetDrawColor(0,0,0);
        $this->SetFillColor(255,255,255);
        if ($fFlagDisponible == 'S')
        {
            $this->SetWidths(array(17,80,20,20,20,10,20,10,20,10,20,10,20));
            $this->SetAligns(array('C','L','R','R','R','R','R','R','R','R','R','R','R'));
            $this->Row(array(utf8_decode('PARTIDA'),
                             utf8_decode('DENOMINACION'),
                             utf8_decode('MONTO FORMULADO'),
                             utf8_decode('MONTO ACTUAL'),
                             utf8_decode('MONTO COMPROMISO'),
                             utf8_decode('%'),
                             utf8_decode('MONTO CAUSADO'),
                             utf8_decode('%'),
                             utf8_decode('MONTO PAGADO'),
                             utf8_decode('%'),
                             utf8_decode('MONTO DISPONIBLE'),
                             utf8_decode('%'),
                             utf8_decode('DISP. P/ TRASLADO')
                ));
        }
        else
        {
            $this->SetWidths(array(17,90,20,20,20,10,20,10,20,10,20));
            $this->SetAligns(array('C','L','R','R','R','R','R','R','R','R','R'));
            $this->Row(array(utf8_decode('PARTIDA'),
                             utf8_decode('DENOMINACION'),
                             utf8_decode('MONTO FORMULADO'),
                             utf8_decode('MONTO ACTUAL'),
                             utf8_decode('MONTO COMPROMISO'),
                             utf8_decode('%'),
                             utf8_decode('MONTO CAUSADO'),
                             utf8_decode('%'),
                             utf8_decode('MONTO PAGADO'),
                             utf8_decode('%'),
                             utf8_decode('MONTO DISPONIBLE')
                ));   
        }
        $this->Ln(1);
    }
    
    //  Pie de página.
    function Footer() {
    }
}
//---------------------------------------------------

//---------------------------------------------------
//  Creación del objeto de la clase heredada.
if ($fFlagDisponible == 'S') $pdf = new PDF('L', 'mm', 'A4');
else $pdf = new PDF('L', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->SetMargins(10, 10, 50);
$pdf->SetAutoPageBreak(1, 15);
$pdf->AddPage();
//---------------------------------------------------
$TotalAprobado = 0;
$TotalAjustado = 0;
$TotalCompromiso = 0;
$TotalCausado = 0;
$TotalPagado = 0;
$TotalDisponible = 0;
$TotalAprobado80 = 0;
$TotalDisponible80 = 0;
/*
    $sql = "(SELECT
            p.cod_partida,
            SUBSTRING(p.cod_partida, 1, 3) AS Par,
            SUBSTRING(p.cod_partida, 5, 2) AS Gen,
            SUBSTRING(p.cod_partida, 8, 2) AS Esp,
            SUBSTRING(p.cod_partida, 11, 2) AS Sub,
            p.denominacion,
            (SELECT SUM(MontoAprobado) FROM pv_presupuestodet WHERE cod_partida LIKE CONCAT(p.cod_tipocuenta, '%')) AS MontoAprobado,
            (SELECT SUM(MontoAjustado) FROM pv_presupuestodet WHERE cod_partida LIKE CONCAT(p.cod_tipocuenta, '%')) AS MontoAjustado,
            (SELECT SUM(MontoCompromiso) FROM vw_resumen_partidas WHERE cod_partida LIKE CONCAT(p.cod_tipocuenta, '%') $filtro) AS MontoCompromiso,
            (SELECT SUM(MontoCausado) FROM vw_resumen_partidas WHERE cod_partida LIKE CONCAT(p.cod_tipocuenta, '%') $filtro) AS MontoCausado,
            (SELECT SUM(MontoPagado) FROM vw_resumen_partidas WHERE cod_partida LIKE CONCAT(p.cod_tipocuenta, '%') $filtro) AS MontoPagado,
            'Cta' AS Tipo
         FROM pv_partida p
         WHERE
            p.partida1 = '00' AND
            p.generica = '00' AND
            p.especifica = '00' AND
            p.subespecifica = '00' AND
            SUBSTRING(p.cod_partida, 1, 1) IN (SELECT SUBSTRING(cod_partida, 1, 1) AS partida FROM vw_resumen_partidas WHERE 1 $filtro GROUP BY partida)
         GROUP BY cod_partida)
        UNION
        (SELECT
            p.cod_partida,
            SUBSTRING(p.cod_partida, 1, 3) AS Par,
            SUBSTRING(p.cod_partida, 5, 2) AS Gen,
            SUBSTRING(p.cod_partida, 8, 2) AS Esp,
            SUBSTRING(p.cod_partida, 11, 2) AS Sub,
            p.denominacion,
            (SELECT SUM(MontoAprobado) FROM pv_presupuestodet WHERE cod_partida LIKE CONCAT(p.cod_tipocuenta, p.partida1, '.%')) AS MontoAprobado,
            (SELECT SUM(MontoAjustado) FROM pv_presupuestodet WHERE cod_partida LIKE CONCAT(p.cod_tipocuenta, p.partida1, '.%')) AS MontoAjustado,
            (SELECT SUM(MontoCompromiso) FROM vw_resumen_partidas WHERE cod_partida LIKE CONCAT(p.cod_tipocuenta, p.partida1, '.%') $filtro) AS MontoCompromiso,
            (SELECT SUM(MontoCausado) FROM vw_resumen_partidas WHERE cod_partida LIKE CONCAT(p.cod_tipocuenta, p.partida1, '.%') $filtro) AS MontoCausado,
            (SELECT SUM(MontoPagado) FROM vw_resumen_partidas WHERE cod_partida LIKE CONCAT(p.cod_tipocuenta, p.partida1, '.%') $filtro) AS MontoPagado,
            'Par' AS Tipo
         FROM pv_partida p
         WHERE
            p.partida1 <> '00' AND
            p.generica = '00' AND
            p.especifica = '00' AND
            p.subespecifica = '00' AND
            SUBSTRING(p.cod_partida, 1, 3) IN (SELECT SUBSTRING(cod_partida, 1, 3) AS partida FROM vw_resumen_partidas WHERE 1 $filtro GROUP BY partida)
         GROUP BY cod_partida)
        UNION
        (SELECT
            p.cod_partida,
            SUBSTRING(p.cod_partida, 1, 3) AS Par,
            SUBSTRING(p.cod_partida, 5, 2) AS Gen,
            SUBSTRING(p.cod_partida, 8, 2) AS Esp,
            SUBSTRING(p.cod_partida, 11, 2) AS Sub,
            p.denominacion,
            (SELECT SUM(MontoAprobado) FROM pv_presupuestodet WHERE cod_partida LIKE CONCAT(p.cod_tipocuenta, p.partida1, '.', p.generica, '.%')) AS MontoAprobado,
            (SELECT SUM(MontoAjustado) FROM pv_presupuestodet WHERE cod_partida LIKE CONCAT(p.cod_tipocuenta, p.partida1, '.', p.generica, '.%')) AS MontoAjustado,
            (SELECT SUM(MontoCompromiso) FROM vw_resumen_partidas WHERE cod_partida LIKE CONCAT(p.cod_tipocuenta, p.partida1, '.', p.generica, '.%') $filtro) AS MontoCompromiso,
            (SELECT SUM(MontoCausado) FROM vw_resumen_partidas WHERE cod_partida LIKE CONCAT(p.cod_tipocuenta, p.partida1, '.', p.generica, '.%') $filtro) AS MontoCausado,
            (SELECT SUM(MontoPagado) FROM vw_resumen_partidas WHERE cod_partida LIKE CONCAT(p.cod_tipocuenta, p.partida1, '.', p.generica, '.%') $filtro) AS MontoPagado,
            'Gen' AS Tipo
         FROM pv_partida p
         WHERE
            p.partida1 <> '00' AND
            p.generica <> '00' AND
            p.especifica = '00' AND
            p.subespecifica = '00' AND
            SUBSTRING(p.cod_partida, 1, 7) IN (SELECT SUBSTRING(cod_partida, 1, 7) AS partida FROM vw_resumen_partidas WHERE 1 $filtro GROUP BY partida)
         GROUP BY cod_partida)
        UNION
        (SELECT
            rp.cod_partida,
            SUBSTRING(p.cod_partida, 1, 3) AS Par,
            SUBSTRING(p.cod_partida, 5, 2) AS Gen,
            SUBSTRING(p.cod_partida, 8, 2) AS Esp,
            SUBSTRING(p.cod_partida, 11, 2) AS Sub,
            p.denominacion,
            (SELECT SUM(MontoAprobado) FROM pv_presupuestodet WHERE cod_partida = rp.cod_partida) AS MontoAprobado,
            (SELECT SUM(MontoAjustado) FROM pv_presupuestodet WHERE cod_partida = rp.cod_partida) AS MontoAjustado,
            rp.MontoCompromiso,
            rp.MontoCausado,
            rp.MontoPagado,
            'Esp' AS Tipo
         FROM
            pv_partida p
            INNER JOIN vw_resumen_partidas rp ON (rp.cod_partida = p.cod_partida)
         WHERE 
            rp.CodOrganismo = '$fCodOrganismo' AND
            rp.Periodo >= '$fPeriodoD' AND
            rp.Periodo <= '$fPeriodoH'
         GROUP BY cod_partida)
        ORDER BY cod_partida;";
*/
$sql = "SELECT
            pptod.cod_partida,
            pv.denominacion,
            SUM(pptod.MontoAprobado) AS MontoAprobado,
            SUM(pptod.MontoAjustado) AS MontoAjustado,
            --CompromisoConsolidadoOrganismo(pptod.CodOrganismo, '$fPeriodoD', '$fPeriodoH', pptod.cod_partida) AS MontoCompromiso,
            --CausadoConsolidadoOrganismo(pptod.CodOrganismo, '$fPeriodoD', '$fPeriodoH', pptod.cod_partida) AS MontoCausado,
            --PagadoConsolidadoOrganismo(pptod.CodOrganismo, '$fPeriodoD', '$fPeriodoH', pptod.cod_partida) AS MontoPagado,
            pptod.CodOrganismo
        FROM
            pv_presupuestodet pptod
        INNER JOIN pv_partida pv ON (
            pv.cod_partida = pptod.cod_partida
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
            ppto.FechaInicio >= '$fPeriodoD'
            AND ppto.FechaInicio <= '$fPeriodoH'
            $filtro
        GROUP BY
            pptod.CodOrganismo,
            pptod.cod_partida
        ORDER BY
            pptod.CodOrganismo,
            pptod.cod_partida";
$field_detalle = getRecords($sql);
foreach ($field_detalle as $f) {
    $sql = "SELECT SUM(c.Monto)
            FROM lg_distribucioncompromisos c
            INNER JOIN pv_presupuestodet pptod ON (
                c.CodOrganismo = pptod.CodOrganismo
                AND c.CodPresupuesto = pptod.CodPresupuesto
                AND c.cod_partida = pptod.cod_partida
                AND c.CodFuente = pptod.CodFuente
            )
            INNER JOIN pv_presupuesto ppto ON (
                ppto.CodOrganismo = c.CodOrganismo
                AND ppto.CodPresupuesto = c.CodPresupuesto
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
                c.Estado = 'CO' AND
                c.CodOrganismo = '$f[CodOrganismo]'
                AND c.cod_partida = '$f[cod_partida]'
                AND c.FechaEjecucion >= '$fPeriodoD'
                AND c.FechaEjecucion <= '$fPeriodoH' $filtro
                ";
    $f['MontoCompromiso'] = getVar3($sql);
    ##  
    $sql = "SELECT SUM(c.Monto)
            FROM ap_distribucionobligacion c
            INNER JOIN pv_presupuestodet pptod ON (
                c.CodOrganismo = pptod.CodOrganismo
                AND c.CodPresupuesto = pptod.CodPresupuesto
                AND c.cod_partida = pptod.cod_partida
                AND c.CodFuente = pptod.CodFuente
            )
            INNER JOIN pv_presupuesto ppto ON (
                ppto.CodOrganismo = c.CodOrganismo
                AND ppto.CodPresupuesto = c.CodPresupuesto
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
                c.Estado = 'CA' AND
                c.CodOrganismo = '$f[CodOrganismo]'
                AND c.cod_partida = '$f[cod_partida]'
                AND c.FechaEjecucion >= '$fPeriodoD'
                AND c.FechaEjecucion <= '$fPeriodoH' $filtro
                ";
    $f['MontoCausado'] = getVar3($sql);
    ##  
    $sql = "SELECT SUM(c.Monto)
            FROM ap_ordenpagodistribucion c
            INNER JOIN pv_presupuestodet pptod ON (
                c.CodOrganismo = pptod.CodOrganismo
                AND c.CodPresupuesto = pptod.CodPresupuesto
                AND c.cod_partida = pptod.cod_partida
                AND c.CodFuente = pptod.CodFuente
            )
            INNER JOIN pv_presupuesto ppto ON (
                ppto.CodOrganismo = c.CodOrganismo
                AND ppto.CodPresupuesto = c.CodPresupuesto
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
                c.Estado = 'PA' AND
                c.CodOrganismo = '$f[CodOrganismo]'
                AND c.cod_partida = '$f[cod_partida]'
                AND c.FechaEjecucion >= '$fPeriodoD'
                AND c.FechaEjecucion <= '$fPeriodoH' $filtro
                ";
    $f['MontoPagado'] = getVar3($sql);
    ##  
    $PorcentajeCompromiso = $f['MontoCompromiso'] * 100 / $f['MontoAjustado'];
    $PorcentajeCausado = $f['MontoCausado'] * 100 / $f['MontoCompromiso'];
    $PorcentajePagado = $f['MontoPagado'] * 100 / $f['MontoCompromiso'];
    $MontoDisponible = $f['MontoAjustado'] - $f['MontoCompromiso'];
    $MontoDisponible80 = 0;
    $PorcentajeDisponible80 = 0;
    ##  
    if ($fFlagDisponible == 'S') 
    {
        $sql = "SELECT SUM(MontoAjuste)
                FROM
                    pv_ajustesdet ad
                    INNER JOIN pv_ajustes a ON (
                        a.CodOrganismo = ad.CodOrganismo
                        AND a.CodAjuste = ad.CodAjuste
                    )
                    INNER JOIN pv_presupuestodet pptod ON (
                        ad.CodOrganismo = pptod.CodOrganismo
                        AND ad.CodPresupuesto = pptod.CodPresupuesto
                        AND ad.cod_partida = pptod.cod_partida
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
                    INNER JOIN pv_actividades a2 ON (
                        a2.IdActividad = cp.IdActividad
                    )
                    INNER JOIN pv_proyectos py ON (
                        py.IdProyecto = a2.IdProyecto
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
                    ad.CodOrganismo = '$f[CodOrganismo]'
                    AND ad.cod_partida = '$f[cod_partida]'
                    AND ad.Tipo = 'D'
                    AND a.Tipo = 'TP'
                    AND a.Periodo >= '$fPeriodoD'
                    AND a.Periodo <= '$fPeriodoH' $filtro";
        $MontoAjustes = getVar3($sql);
        $MontoAprobado80 = $f['MontoAprobado'] * 80 / 100;
        $MontoDisponible80 = $MontoAprobado80 - $MontoAjustes;
        if ($MontoDisponible80 > $MontoDisponible) $MontoDisponible80 = $MontoDisponible;
        elseif ($MontoDisponible80 < 0) $MontoDisponible80 = 0;
        $PorcentajeDisponible80 = $MontoDisponible80 * 100 / $MontoAprobado80;
    }
    ##  
    $pdf->SetDrawColor(255,255,255);
    if ($i % 2 == 0) { $pdf->SetFillColor(255,255,255); $pdf->SetDrawColor(255,255,255); } else { $pdf->SetFillColor(240,240,240); $pdf->SetDrawColor(240,240,240); }
    if ($f['Tipo'] == 'Cta') $pdf->SetFont('Arial','BUI',6);
    elseif ($f['Tipo'] == 'Par') $pdf->SetFont('Arial','BU',6);
    elseif ($f['Tipo'] == 'Gen') $pdf->SetFont('Arial','B',6);
    else $pdf->SetFont('Arial','',6);
    if ($fFlagDisponible == 'S') 
    {
        $pdf->Row(array(utf8_decode($f['cod_partida']),
                        utf8_decode($f['denominacion']),
                        number_format($f['MontoAprobado'],2,',','.'),
                        number_format($f['MontoAjustado'],2,',','.'),
                        number_format($f['MontoCompromiso'],2,',','.'),
                        number_format($PorcentajeCompromiso,2,',','.'),
                        number_format($f['MontoCausado'],2,',','.'),
                        number_format($PorcentajeCausado,2,',','.'),
                        number_format($f['MontoPagado'],2,',','.'),
                        number_format($PorcentajePagado,2,',','.'),
                        number_format($MontoDisponible,2,',','.'),
                        number_format($PorcentajeDisponible80,2,',','.'),
                        number_format($MontoDisponible80,2,',','.')
                    ));
    }
    else
    {
        $pdf->Row(array(utf8_decode($f['cod_partida']),
                        utf8_decode($f['denominacion']),
                        number_format($f['MontoAprobado'],2,',','.'),
                        number_format($f['MontoAjustado'],2,',','.'),
                        number_format($f['MontoCompromiso'],2,',','.'),
                        number_format($PorcentajeCompromiso,2,',','.'),
                        number_format($f['MontoCausado'],2,',','.'),
                        number_format($PorcentajeCausado,2,',','.'),
                        number_format($f['MontoPagado'],2,',','.'),
                        number_format($PorcentajePagado,2,',','.'),
                        number_format($MontoDisponible,2,',','.')
                    ));
    }
    $pdf->Ln(1);
    ##  
    $TotalAprobado += $f['MontoAprobado'];
    $TotalAjustado += $f['MontoAjustado'];
    $TotalCompromiso += $f['MontoCompromiso'];
    $TotalCausado += $f['MontoCausado'];
    $TotalPagado += $f['MontoPagado'];
    $TotalDisponible += $MontoDisponible;
    $TotalAprobado80 += $MontoAprobado80;
    $TotalDisponible80 += $MontoDisponible80;
    ++$i;
}
$PorcentajeCompromiso = $TotalCompromiso * 100 / $TotalAjustado;
$PorcentajeCausado = $TotalCausado * 100 / $TotalCompromiso;
$PorcentajePagado = $TotalPagado * 100 / $TotalCompromiso;
$PorcentajeDisponible80 = $TotalDisponible80 * 100 / $TotalAprobado80;
##  
$pdf->Ln(2);
$pdf->SetFillColor(255,255,255);
$pdf->SetDrawColor(0,0,0);
$pdf->SetFont('Arial','B',6);
if ($fFlagDisponible == 'S') 
{
    $pdf->SetWidths(array(97,20,20,20,10,20,10,20,10,20,10,20));
    $pdf->SetAligns(array('C','R','R','R','R','R','R','R','R','R','R','R'));
    $pdf->Row(array('TOTALES',
                    number_format($TotalAprobado,2,',','.'),
                    number_format($TotalAjustado,2,',','.'),
                    number_format($TotalCompromiso,2,',','.'),
                    number_format($PorcentajeCompromiso,2,',','.'),
                    number_format($TotalCausado,2,',','.'),
                    number_format($PorcentajeCausado,2,',','.'),
                    number_format($TotalPagado,2,',','.'),
                    number_format($PorcentajePagado,2,',','.'),
                    number_format($TotalDisponible,2,',','.'),
                    number_format($PorcentajeDisponible80,2,',','.'),
                    number_format($TotalDisponible80,2,',','.')
            ));
}
else
{
    $pdf->SetWidths(array(107,20,20,20,10,20,10,20,10,20));
    $pdf->SetAligns(array('C','R','R','R','R','R','R','R','R','R'));
    $pdf->Row(array('TOTALES',
                    number_format($TotalAprobado,2,',','.'),
                    number_format($TotalAjustado,2,',','.'),
                    number_format($TotalCompromiso,2,',','.'),
                    number_format($PorcentajeCompromiso,2,',','.'),
                    number_format($TotalCausado,2,',','.'),
                    number_format($PorcentajeCausado,2,',','.'),
                    number_format($TotalPagado,2,',','.'),
                    number_format($PorcentajePagado,2,',','.'),
                    number_format($TotalDisponible,2,',','.')
            ));   
}
//---------------------------------------------------

//---------------------------------------------------
//  Muestro el contenido del pdf.
$pdf->Output();
?>