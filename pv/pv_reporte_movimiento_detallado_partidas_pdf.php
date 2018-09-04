<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$fPeriodoD = formatFechaAMD($fPeriodoD);
$fPeriodoH = formatFechaAMD($fPeriodoH);
$subtitulo = false;
$filtro = '';
$filtrod = '';
if (trim($fCodOrganismo)) $filtro .= " AND co.CodOrganismo = '$fCodOrganismo'";
if (trim($fCodDependencia)) $filtro .= " AND cc.CodDependencia = '$fCodDependencia'";
if (trim($fCodUnidadEjec)) $filtro .= " AND cp.CodUnidadEjec = '$fCodUnidadEjec'";
if (trim($fCategoriaProg)) $filtro .= " AND cp.CategoriaProg = '$fCategoriaProg'";
if (trim($fPeriodoD)) {$filtro .= " AND co.Fecha >= '$fPeriodoD'";$filtrod .= " AND co.Fecha >= '$fPeriodoD'";}
if (trim($fPeriodoH)) {$filtro .= " AND co.Fecha <= '$fPeriodoH'";$filtrod .= " AND co.Fecha <= '$fPeriodoH'";}
if (trim($fPar)) $filtro .= " AND SUBSTRING(co.cod_partida, 1, 3) = '$fPar'";
if (trim($fGen)) $filtro .= " AND SUBSTRING(co.cod_partida, 1, 6) = '$fPar.$fGen'";
if (trim($fEsp)) $filtro .= " AND SUBSTRING(co.cod_partida, 1, 9) = '$fPar.$fGen.$fEsp'";
if (trim($fSub)) $filtro .= " AND SUBSTRING(co.cod_partida, 1, 12) = '$fPar.$fGen.$fEsp.$fSub'";

$filtro2 = '';
if (trim($fCodOrganismo)) $filtro2 .= " AND co.CodOrganismo = '$fCodOrganismo'";
if (trim($fCodDependencia)) $filtro2 .= " AND cc.CodDependencia = '$fCodDependencia'";
if (trim($fCodUnidadEjec)) $filtro2 .= " AND cp.CodUnidadEjec = '$fCodUnidadEjec'";
if (trim($fCategoriaProg)) $filtro2 .= " AND cp.CategoriaProg = '$fCategoriaProg'";
if (trim($fPeriodoD)) $filtro2 .= " AND aj.Fecha >= '$fPeriodoD'";
if (trim($fPeriodoH)) $filtro2 .= " AND aj.Fecha <= '$fPeriodoH'";
if (trim($fPar)) $filtro2 .= " AND SUBSTRING(co.cod_partida, 1, 3) = '$fPar'";
if (trim($fGen)) $filtro2 .= " AND SUBSTRING(co.cod_partida, 1, 6) = '$fPar.$fGen'";
if (trim($fEsp)) $filtro2 .= " AND SUBSTRING(co.cod_partida, 1, 9) = '$fPar.$fGen.$fEsp'";
if (trim($fSub)) $filtro2 .= " AND SUBSTRING(co.cod_partida, 1, 12) = '$fPar.$fGen.$fEsp.$fSub'";

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
        global $f;
        global $subtitulo;
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
        $this->Image($_PARAMETRO["PATHLOGO"].$Logo, 9, 9, 15, 12);
        $this->SetX(25, 5); $this->Cell(175, 5, utf8_decode(mb_strtoupper($NomOrganismo)), 0, 1, 'L');
        $this->SetX(25, 5); $this->Cell(175, 5, utf8_decode(mb_strtoupper($NomDependencia)), 0, 1, 'L');
        $this->SetFont('Arial', '', 8);
        $this->SetXY(180, 10); $this->Cell(10, 5, utf8_decode('Fecha: '), 0, 0, 'L');
        $this->Cell(30, 5, formatFechaDMA($FechaActual), 0, 1, 'L');
        $this->SetXY(180, 15); $this->Cell(10, 5, utf8_decode('Página: '), 0, 0, 'L');
        $this->Cell(30, 5, $this->PageNo().' de {nb}', 0, 1, 'L');
        ##  -------------------
        $this->SetY(25); 
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(196, 5, utf8_decode('MOVIMIENTO DETALLADO POR PARTIDAS'), 0, 1, 'C');
        $this->Ln(5);
        ##  
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(196, 5, mb_strtoupper(utf8_decode($f['CatProg'].' '.$f['Actividad'])), 0, 1, 'L');
        $this->Ln(1);
        ##  
        $this->SetFont('Arial','B',7);
        $this->SetDrawColor(0,0,0);
        $this->SetFillColor(255,255,255);
        $this->SetWidths(array(20,151,25));
        $this->SetAligns(array('C','L','R'));
        $this->Row(array(utf8_decode('PARTIDA'),
                         utf8_decode('DENOMINACION'),
                         utf8_decode('MONTO')
                ));
        $this->Ln(1);
        if ($subtitulo) {
            $this->SetX(30);
            $this->SetFont('Arial','',7);
            $this->SetDrawColor(255,255,255);
            $this->SetFillColor(255,255,255);
            $this->SetWidths(array(15,33,103,25));
            $this->SetAligns(array('C','L','L','R'));
        }
    }
    
    //  Pie de página.
    function Footer() {
    }
}
//---------------------------------------------------

//---------------------------------------------------
//  Creación del objeto de la clase heredada.
$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->SetMargins(10, 10, 50);
$pdf->SetAutoPageBreak(1, 25);
//---------------------------------------------------
$Monto = 0;
$TotalAjustado = 0;
$Grupo = '';
$sql = "(
            SELECT
                co.CodOrganismo,
                co.CodPresupuesto,
                CONCAT(
                        ss.CodSector,
                        pr.CodPrograma,
                        a.CodActividad
                ) AS CatProg,
                a.Denominacion AS Actividad,
                co.CodFuente,
                co.cod_partida,
                pv.denominacion AS Partida,
                SUM(co.Monto) AS Monto,
                'Compromisos' AS Tipo
            FROM
                pv_compromisos co
            INNER JOIN pv_partida pv ON (
                pv.cod_partida = co.cod_partida
            )
            INNER JOIN pv_presupuestodet pptod ON (
                pptod.CodOrganismo = co.CodOrganismo
                AND pptod.CodPresupuesto = co.CodPresupuesto
                AND pptod.CodFuente = co.CodFuente
                AND pptod.cod_partida = co.cod_partida
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
                co.Estado = 'CO' $filtro
            GROUP BY
                CodOrganismo,
                CodPresupuesto,
                CodFuente,
                cod_partida
        )
        UNION
        (
            SELECT
                co.CodOrganismo,
                co.CodPresupuesto,
                CONCAT(
                        ss.CodSector,
                        pr.CodPrograma,
                        a.CodActividad
                ) AS CatProg,
                a.Denominacion AS Actividad,
                co.CodFuente,
                co.cod_partida,
                pv.denominacion AS Partida,
                SUM(co.MontoAjuste) AS Monto,
                'Ajustes' AS Tipo
            FROM
                pv_ajustesdet co
            INNER JOIN pv_ajustes aj ON (
                aj.CodOrganismo = co.CodOrganismo
                AND aj.CodAjuste = co.CodAjuste
            )
            INNER JOIN pv_partida pv ON (
                pv.cod_partida = co.cod_partida
            )
            INNER JOIN pv_presupuestodet pptod ON (
                pptod.CodOrganismo = co.CodOrganismo
                AND pptod.CodPresupuesto = co.CodPresupuesto
                AND pptod.CodFuente = co.CodFuente
                AND pptod.cod_partida = co.cod_partida
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
                aj.Estado = 'AP'
                AND co.Tipo = 'D' $filtro2
            GROUP BY
                CodOrganismo,
                CodPresupuesto,
                CodFuente,
                cod_partida
        )
        ORDER BY
            Tipo,
            CodOrganismo,
            CodPresupuesto,
            CodFuente,
            cod_partida";
$field_detalle = getRecords($sql);
foreach ($field_detalle as $f) {
    $subtitulo = false;
    if ($Grupo != $f['CatProg']) {
        if ($Grupo) {
            $pdf->SetFillColor(255,255,255);
            $pdf->SetDrawColor(0,0,0);
            $pdf->SetFont('Arial','B',7);
            $pdf->SetWidths(array(171,25));
            $pdf->SetAligns(array('C','R'));
            $pdf->Row(array('TOTAL',
                            number_format($Monto,2,',','.')
                        ));
        }
        $Grupo = $f['CatProg'];
        ##  
        $pdf->AddPage();
    }
    ##  
    $pdf->SetDrawColor(255,255,255);
    $pdf->SetFillColor(255,255,255);
    $pdf->SetFont('Arial','B',7);
    $pdf->SetWidths(array(20,151,25));
    $pdf->SetAligns(array('C','L','R'));
    $pdf->Row(array(utf8_decode($f['cod_partida']),
                    utf8_decode($f['Partida']),
                    number_format($f['Monto'],2,',','.')
                ));
    $pdf->Ln(1);
    ##  
    if ($f['Tipo'] == 'Compromisos') {
        $sql = "SELECT
                    co.Fecha,
                    co.Monto,
                    (
                        CASE
                        WHEN co.Origen = 'BO' THEN
                            CONCAT('OB/', co.CodCertificacion)
                        WHEN co.Origen = 'NO' OR co.Origen = 'OB' THEN
                            CONCAT(co.CodTipoDocumento, '-', co.NroDocumento)
                        WHEN co.Origen = 'OC' OR co.Origen = 'OS' THEN
                            CONCAT(co.Origen, '/', co.NroOrden)
                        WHEN co.Origen = 'TB' THEN
                            CONCAT('TB/', co.NroTransaccion)
                        ELSE
                            ''
                        END
                    ) AS NroTransaccion,
                    (
                        CASE
                        WHEN co.Origen = 'BO' THEN
                            bo.Justificacion
                        WHEN co.Origen = 'NO' OR co.Origen = 'OB' THEN
                            ob.Comentarios
                        WHEN co.Origen = 'OC' THEN
                            oc.Observaciones
                        WHEN co.Origen = 'OS' THEN
                            os.Descripcion
                        WHEN co.Origen = 'TB' THEN
                            tb.Comentarios
                        ELSE
                            ''
                        END
                    ) AS Descripcion
                FROM
                    pv_compromisos co
                    LEFT JOIN ap_certificaciones bo ON (bo.CodCertificacion = co.CodCertificacion)
                    LEFT JOIN ap_obligaciones ob On (
                        ob.CodProveedor = co.CodProveedor
                        AND ob.CodTipoDocumento = co.CodTipoDocumento
                        AND ob.NroDocumento = co.NroDocumento)
                    LEFT JOIN lg_ordencompra oc ON (
                        oc.Anio = co.Anio
                        AND oc.CodOrganismo = co.CodOrganismo
                        AND oc.NroOrden = co.NroOrden)
                    LEFT JOIN lg_ordenservicio os ON (
                        os.Anio = co.Anio
                        AND os.CodOrganismo = co.CodOrganismo
                        AND os.NroOrden = co.NroOrden)
                    LEFT JOIN ap_bancotransaccion tb ON (
                        tb.NroTransaccion = co.NroTransaccion
                        AND tb.Secuencia = co.TransaccionSecuencia)
                WHERE
                    co.CodOrganismo = '$f[CodOrganismo]'
                AND co.CodPresupuesto = '$f[CodPresupuesto]'
                AND co.CodFuente = '$f[CodFuente]'
                AND co.cod_partida = '$f[cod_partida]' $filtrod
                ORDER BY
                    Fecha,
                    Monto";
    } else  {
        $sql = "SELECT
                    aj.Fecha,
                    co.MontoAjuste AS Monto,
                    aj.CodAjuste AS NroTransaccion,
                    aj.Descripcion
                FROM
                    pv_ajustesdet co
                    INNER JOIN pv_ajustes aj ON (
                        aj.CodOrganismo = co.CodOrganismo
                        AND aj.CodAjuste = co.CodAjuste
                    )
                WHERE
                    co.CodOrganismo = '$f[CodOrganismo]'
                AND co.CodPresupuesto = '$f[CodPresupuesto]'
                AND co.CodFuente = '$f[CodFuente]'
                AND co.cod_partida = '$f[cod_partida]'
                AND co.Tipo = 'D'
                AND aj.Estado = 'AP' $filtro2
                ORDER BY
                    NroTransaccion,
                    Fecha,
                    Monto";
    }
    $field_compromisos = getRecords($sql);
    foreach ($field_compromisos as $fc) {
        $subtitulo = true;
        $pdf->SetX(30);
        $pdf->SetFont('Arial','',7);
        $pdf->SetDrawColor(255,255,255);
        $pdf->SetFillColor(255,255,255);
        $pdf->SetWidths(array(15,33,103,25));
        $pdf->SetAligns(array('C','L','L','R'));
        $pdf->Row(array(formatFechaDMA($fc['Fecha']),
                        utf8_decode($fc['NroTransaccion']),
                        utf8_decode($fc['Descripcion']),
                        number_format($fc['Monto'],2,',','.')
                ));
    }
    $pdf->Ln(3);
    ##  
    $Monto += $f['Monto'];
    ++$i;
}
$pdf->SetFillColor(255,255,255);
$pdf->SetDrawColor(0,0,0);
$pdf->SetFont('Arial','B',7);
$pdf->SetWidths(array(171,25));
$pdf->SetAligns(array('C','R'));
$pdf->Row(array('TOTAL',
                number_format($Monto,2,',','.')
            ));
//---------------------------------------------------

//---------------------------------------------------
//  Muestro el contenido del pdf.
$pdf->Output();
?>