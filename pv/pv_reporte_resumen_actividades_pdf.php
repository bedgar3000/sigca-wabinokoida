<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$fPeriodoD = formatFechaAMD($fPeriodoD);
$fPeriodoH = formatFechaAMD($fPeriodoH);
$filtro = '';
if (trim($fCodOrganismo)) $filtro .= " AND pptod.CodOrganismo = '$fCodOrganismo'";
if (trim($fIdSubSector)) $filtro .= " AND ss.IdSubSector = '$fIdSubSector'";
if (trim($fIdPrograma)) $filtro .= " AND pr.IdPrograma = '$fIdPrograma'";
if (trim($fIdSubPrograma)) $filtro .= " AND spr.IdSubPrograma = '$fIdSubPrograma'";
if (trim($fIdProyecto)) $filtro .= " AND py.IdProyecto = '$fIdProyecto'";
if (trim($fIdActividad)) $filtro .= " AND a.IdActividad = '$fIdActividad'";
if (trim($fPar)) $filtro .= " AND SUBSTRING(pptod.cod_partida, 1, 3) = '$fPar'";
if (trim($fGen)) $filtro .= " AND SUBSTRING(pptod.cod_partida, 1, 6) = '$fPar.$fGen'";
if (trim($fEsp)) $filtro .= " AND SUBSTRING(pptod.cod_partida, 1, 9) = '$fPar.$fGen.$fEsp'";
if (trim($fSub)) $filtro .= " AND SUBSTRING(pptod.cod_partida, 1, 12) = '$fPar.$fGen.$fEsp.$fSub'";
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
        $this->Cell(257, 5, strtoupper(utf8_decode('RESUMEN ESTADISTICO DE PARTIDAS POR ACTIVIDADES')), 0, 1, 'C');
        $this->Ln(5);
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
$TotalAprobado = 0;
$TotalAjustado = 0;
$TotalCompromiso = 0;
$TotalCausado = 0;
$TotalPagado = 0;
$TotalDisponible = 0;
$Grupo = '';
$sql = "SELECT
            pptod.CodOrganismo,
            cp.CategoriaProg,
            CONCAT(ss.CodSector, pr.CodPrograma, a.CodActividad) AS CatProg,
            a.Denominacion As Actividad,
            pptod.cod_partida,
            pv.denominacion,
            SUM(pptod.MontoAprobado) AS MontoAprobado,
            SUM(pptod.MontoAjustado) AS MontoAjustado,
            CompromisoConsolidadoActividadPorFecha (
                pptod.CodOrganismo,
                cp.CategoriaProg,
                '$fPeriodoD',
                '$fPeriodoH',
                pptod.cod_partida
            ) AS MontoCompromiso,
            CausadoConsolidadoActividadPorFecha (
                pptod.CodOrganismo,
                cp.CategoriaProg,
                '$fPeriodoD',
                '$fPeriodoH',
                pptod.cod_partida
            ) AS MontoCausado,
            PagadoConsolidadoActividadPorFecha (
                pptod.CodOrganismo,
                cp.CategoriaProg,
                '$fPeriodoD',
                '$fPeriodoH',
                pptod.cod_partida
            ) AS MontoPagado
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
        INNER JOIN pv_actividades a ON (
            a.IdActividad = cp.IdActividad
        )
        INNER JOIN pv_proyectos py ON (py.IdProyecto = a.IdProyecto)
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
        WHERE 1 $filtro
        GROUP BY
            pptod.CodOrganismo,
            cp.CategoriaProg,
            pptod.cod_partida
        ORDER BY
            pptod.CodOrganismo,
            cp.CategoriaProg,
            pptod.cod_partida";
$field_detalle = getRecords($sql);
foreach ($field_detalle as $f) {
    if ($Grupo != $f['CategoriaProg']) {
        if ($Grupo) {
            $PorcentajeCompromiso = $TotalCompromiso * 100 / $TotalAjustado;
            $PorcentajeCausado = $TotalCausado * 100 / $TotalCompromiso;
            $PorcentajePagado = $TotalPagado * 100 / $TotalCompromiso;
            ##  
            $pdf->SetFillColor(255,255,255);
            $pdf->SetDrawColor(0,0,0);
            $pdf->SetFont('Arial','B',6);
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
            ##  
            $TotalAprobado = 0;
            $TotalAjustado = 0;
            $TotalCompromiso = 0;
            $TotalCausado = 0;
            $TotalPagado = 0;
            $TotalDisponible = 0;
            ##  
            $pdf->Ln(5);
        }
        ##  
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(257, 5, mb_strtoupper(utf8_decode($f['CatProg'].' '.$f['Actividad'])), 0, 1, 'L');
        ##  
        $pdf->SetFont('Arial','B',6);
        $pdf->SetDrawColor(0,0,0);
        $pdf->SetFillColor(255,255,255);
        $pdf->SetWidths(array(17,90,20,20,20,10,20,10,20,10,20));
        $pdf->SetAligns(array('C','L','R','R','R','R','R','R','R','R','R'));
        $pdf->Row(array(utf8_decode('PARTIDA'),
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
        $pdf->Ln(1);
        ##  
        $Grupo = $f['CategoriaProg'];
        $i = 0;
    }
    $PorcentajeCompromiso = $f['MontoCompromiso'] * 100 / $f['MontoAjustado'];
    $PorcentajeCausado = $f['MontoCausado'] * 100 / $f['MontoCompromiso'];
    $PorcentajePagado = $f['MontoPagado'] * 100 / $f['MontoCompromiso'];
    $MontoDisponible = $f['MontoAjustado'] - $f['MontoCompromiso'];
    ##  
    $pdf->SetDrawColor(255,255,255);
    if ($i % 2 == 0) { $pdf->SetFillColor(255,255,255); $pdf->SetDrawColor(255,255,255); } else { $pdf->SetFillColor(240,240,240); $pdf->SetDrawColor(240,240,240); }
    $pdf->SetFont('Arial','',6);
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
    $pdf->Ln(1);
    ##  
    $TotalAprobado += $f['MontoAprobado'];
    $TotalAjustado += $f['MontoAjustado'];
    $TotalCompromiso += $f['MontoCompromiso'];
    $TotalCausado += $f['MontoCausado'];
    $TotalPagado += $f['MontoPagado'];
    $TotalDisponible += $MontoDisponible;
    ++$i;
}
$PorcentajeCompromiso = $TotalCompromiso * 100 / $TotalAjustado;
$PorcentajeCausado = $TotalCausado * 100 / $TotalCompromiso;
$PorcentajePagado = $TotalPagado * 100 / $TotalCompromiso;
##  
$pdf->Ln(2);
$pdf->SetFillColor(255,255,255);
$pdf->SetDrawColor(0,0,0);
$pdf->SetFont('Arial','B',6);
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
//---------------------------------------------------

//---------------------------------------------------
//  Muestro el contenido del pdf.
$pdf->Output();
?>