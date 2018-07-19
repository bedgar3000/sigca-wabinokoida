<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$fPeriodoD = formatFechaAMD($fPeriodoD);
$fPeriodoH = formatFechaAMD($fPeriodoH);
$Inicio = substr($fPeriodoD, 0, 4) . "-01-01";
$Anterior = addDate($fPeriodoD, -1);
$fEjercicioD = substr($fPeriodoD,0,4);
$fEjercicioH = substr($fPeriodoH,0,4);
##  
$filtro = '';
if (trim($fCodOrganismo)) $filtro .= " AND ppto.CodOrganismo = '$fCodOrganismo'";
if (trim($fPeriodoD)) $filtro.=" AND (ppto.Ejercicio >= '$fEjercicioD')";
if (trim($fPeriodoH)) $filtro.=" AND (ppto.Ejercicio <= '$fEjercicioH')";
$filtro_ejecucion = '';
if (trim($fCodOrganismo)) $filtro_ejecucion .= " AND co.CodOrganismo = '$fCodOrganismo'";
if (trim($fPeriodoD)) $filtro_ejecucion.=" AND (co.FechaEjecucion >= '$fPeriodoD')";
if (trim($fPeriodoH)) $filtro_ejecucion.=" AND (co.FechaEjecucion <= '$fPeriodoH')";
if (trim($fPeriodoD)) $filtro_ejecucion.=" AND (ppto.Ejercicio >= '$fEjercicioD')";
if (trim($fPeriodoH)) $filtro_ejecucion.=" AND (ppto.Ejercicio <= '$fEjercicioH')";
$filtro_ajuste = '';
if (trim($fCodOrganismo)) $filtro_ajuste .= " AND aj.CodOrganismo = '$fCodOrganismo'";
if (trim($fPeriodoD)) $filtro_ajuste.=" AND (aj.Fecha >= '$fPeriodoD')";
if (trim($fPeriodoH)) $filtro_ajuste.=" AND (aj.Fecha <= '$fPeriodoH')";
if (trim($fPeriodoD)) $filtro_ajuste.=" AND (ppto.Ejercicio >= '$fEjercicioD')";
if (trim($fPeriodoH)) $filtro_ajuste.=" AND (ppto.Ejercicio <= '$fEjercicioH')";
$filtro_ejecucion_anterior = '';
if (trim($fCodOrganismo)) $filtro_ejecucion_anterior .= " AND co.CodOrganismo = '$fCodOrganismo'";
if (trim($fPeriodoD)) $filtro_ejecucion_anterior.=" AND (co.FechaEjecucion >= '$Inicio')";
if (trim($fPeriodoH)) $filtro_ejecucion_anterior.=" AND (co.FechaEjecucion <= '$Anterior')";
if (trim($fPeriodoD)) $filtro_ejecucion_anterior.=" AND (ppto.Ejercicio >= '$fEjercicioD')";
if (trim($fPeriodoH)) $filtro_ejecucion_anterior.=" AND (ppto.Ejercicio <= '$fEjercicioH')";
$filtro_ajuste_anterior = '';
if (trim($fCodOrganismo)) $filtro_ajuste_anterior .= " AND aj.CodOrganismo = '$fCodOrganismo'";
if (trim($fPeriodoD)) $filtro_ajuste_anterior.=" AND (aj.Fecha >= '$Inicio')";
if (trim($fPeriodoH)) $filtro_ajuste_anterior.=" AND (aj.Fecha <= '$Anterior')";
if (trim($fPeriodoD)) $filtro_ajuste_anterior.=" AND (ppto.Ejercicio >= '$fEjercicioD')";
if (trim($fPeriodoH)) $filtro_ajuste_anterior.=" AND (ppto.Ejercicio <= '$fEjercicioH')";
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
        $this->Cell(257, 5, strtoupper(utf8_decode('RESUMEN ESTADISTICO DE PARTIDAS CONSOLIDADO')), 0, 1, 'C');
        $this->Ln(5);
        ##  
        $this->SetFont('Arial','B',6);
        $this->SetDrawColor(0,0,0);
        $this->SetFillColor(255,255,255);
        $this->SetWidths(array(10,77,20,20,20,20,10,20,10,20,10,20));
        $this->SetAligns(array('C','L','R','R','R','R','R','R','R','R','R','R'));
        $this->Row(array(utf8_decode('PAR'),
                         utf8_decode('DENOMINACION'),
                         utf8_decode('MONTO FORMULADO'),
                         utf8_decode('MONTO ACTUAL'),
                         utf8_decode('COMPROMISO ANTERIOR'),
                         utf8_decode('MONTO COMPROMISO'),
                         utf8_decode('%'),
                         utf8_decode('MONTO CAUSADO'),
                         utf8_decode('%'),
                         utf8_decode('MONTO PAGADO'),
                         utf8_decode('%'),
                         utf8_decode('MONTO DISPONIBLE')
                ));
        $this->Ln(1);
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
$TotalCompromisoAnterior = 0;
$TotalCausado = 0;
$TotalPagado = 0;
$TotalDisponible = 0;
##  
$sql = "SELECT
            pptod.CodOrganismo,
            SUBSTRING(pptod.cod_partida,1,3) AS Par,
            SUM(pptod.MontoAprobado) AS MontoAprobado
        FROM pv_presupuestodet pptod
        INNER JOIN pv_presupuesto ppto ON (
            ppto.CodOrganismo = pptod.CodOrganismo
            AND ppto.CodPresupuesto = pptod.CodPresupuesto
        )
        WHERE 1 $filtro
        GROUP BY CodOrganismo, Ejercicio, Par
        ORDER BY CodOrganismo, Ejercicio, Par";
$field_partidas = getRecords($sql);
foreach ($field_partidas as $fp)
{
    ##  Monto Ajustes Anterior
    $sql = "SELECT
                (SUM(CASE WHEN co.Tipo = 'I' THEN COALESCE(co.MontoAjuste,0) ELSE 0 END) -
                 SUM(CASE WHEN co.Tipo = 'D' THEN COALESCE(co.MontoAjuste,0) ELSE 0 END))
            FROM pv_ajustesdet co
            INNER JOIN pv_ajustes aj ON (
                aj.CodOrganismo = co.CodOrganismo
                AND aj.CodAjuste = co.CodAjuste
            )
            INNER JOIN pv_presupuestodet pptod ON (
                pptod.CodOrganismo = co.CodOrganismo
                AND pptod.CodPresupuesto = co.CodPresupuesto
                AND pptod.CodFuente = co.CodFuente
                AND pptod.cod_partida = co.cod_partida
            )
            INNER JOIN pv_presupuesto ppto ON (
                pptod.CodOrganismo = ppto.CodOrganismo
                AND pptod.CodPresupuesto = ppto.CodPresupuesto
            )
            WHERE
                aj.Estado = 'AP'
                AND co.CodOrganismo = '$fp[CodOrganismo]'
                AND SUBSTRING(co.cod_partida,1,3) = '$fp[Par]'
                $filtro_ajuste_anterior
            GROUP BY
                aj.CodOrganismo,
                ppto.Ejercicio,
                SUBSTRING(co.cod_partida,1,3)";
    $MontoAjusteAnterior = getVar3($sql);
    ##  Monto Compromiso Anterior
    $sql = "SELECT SUM(COALESCE(co.Monto,0))
            FROM lg_distribucioncompromisos co
            INNER JOIN pv_presupuestodet pptod ON (
                pptod.CodOrganismo = co.CodOrganismo
                AND pptod.CodPresupuesto = co.CodPresupuesto
                AND pptod.CodFuente = co.CodFuente
                AND pptod.cod_partida = co.cod_partida
            )
            INNER JOIN pv_presupuesto ppto ON (
                pptod.CodOrganismo = ppto.CodOrganismo
                AND pptod.CodPresupuesto = ppto.CodPresupuesto
            )
            WHERE
                co.Estado = 'CO'
                AND co.CodOrganismo = '$fp[CodOrganismo]'
                AND SUBSTRING(co.cod_partida,1,3) = '$fp[Par]'
                $filtro_ejecucion_anterior
            GROUP BY
                co.CodOrganismo,
                ppto.Ejercicio,
                SUBSTRING(co.cod_partida,1,3)";
    $MontoCompromisoAnterior = getVar3($sql);
    ##  Monto Ajustes
    $sql = "SELECT
                (SUM(CASE WHEN co.Tipo = 'I' THEN COALESCE(co.MontoAjuste,0) ELSE 0 END) -
                 SUM(CASE WHEN co.Tipo = 'D' THEN COALESCE(co.MontoAjuste,0) ELSE 0 END))
            FROM pv_ajustesdet co
            INNER JOIN pv_ajustes aj ON (
                aj.CodOrganismo = co.CodOrganismo
                AND aj.CodAjuste = co.CodAjuste
            )
            INNER JOIN pv_presupuestodet pptod ON (
                pptod.CodOrganismo = co.CodOrganismo
                AND pptod.CodPresupuesto = co.CodPresupuesto
                AND pptod.CodFuente = co.CodFuente
                AND pptod.cod_partida = co.cod_partida
            )
            INNER JOIN pv_presupuesto ppto ON (
                pptod.CodOrganismo = ppto.CodOrganismo
                AND pptod.CodPresupuesto = ppto.CodPresupuesto
            )
            WHERE
                aj.Estado = 'AP'
                AND co.CodOrganismo = '$fp[CodOrganismo]'
                AND SUBSTRING(co.cod_partida,1,3) = '$fp[Par]'
                $filtro_ajuste
            GROUP BY
                aj.CodOrganismo,
                ppto.Ejercicio,
                SUBSTRING(co.cod_partida,1,3)";
    $MontoAjuste = getVar3($sql);
    ##  Monto Compromiso
    $sql = "SELECT SUM(COALESCE(co.Monto,0))
            FROM lg_distribucioncompromisos co
            INNER JOIN pv_presupuestodet pptod ON (
                pptod.CodOrganismo = co.CodOrganismo
                AND pptod.CodPresupuesto = co.CodPresupuesto
                AND pptod.CodFuente = co.CodFuente
                AND pptod.cod_partida = co.cod_partida
            )
            INNER JOIN pv_presupuesto ppto ON (
                pptod.CodOrganismo = ppto.CodOrganismo
                AND pptod.CodPresupuesto = ppto.CodPresupuesto
            )
            WHERE
                co.Estado = 'CO'
                AND co.CodOrganismo = '$fp[CodOrganismo]'
                AND SUBSTRING(co.cod_partida,1,3) = '$fp[Par]'
                $filtro_ejecucion
            GROUP BY
                co.CodOrganismo,
                ppto.Ejercicio,
                SUBSTRING(co.cod_partida,1,3)";
    $MontoCompromiso = getVar3($sql);
    ##  Monto Causado
    $sql = "SELECT SUM(COALESCE(co.Monto,0))
            FROM ap_distribucionobligacion co
            INNER JOIN pv_presupuestodet pptod ON (
                pptod.CodOrganismo = co.CodOrganismo
                AND pptod.CodPresupuesto = co.CodPresupuesto
                AND pptod.CodFuente = co.CodFuente
                AND pptod.cod_partida = co.cod_partida
            )
            INNER JOIN pv_presupuesto ppto ON (
                pptod.CodOrganismo = ppto.CodOrganismo
                AND pptod.CodPresupuesto = ppto.CodPresupuesto
            )
            WHERE
                co.Estado = 'CA'
                AND co.CodOrganismo = '$fp[CodOrganismo]'
                AND SUBSTRING(co.cod_partida,1,3) = '$fp[Par]'
                $filtro_ejecucion
            GROUP BY
                co.CodOrganismo,
                ppto.Ejercicio,
                SUBSTRING(co.cod_partida,1,3)";
    $MontoCausado = getVar3($sql);
    ##  Monto Pagado
    $sql = "SELECT SUM(COALESCE(co.Monto,0))
            FROM ap_ordenpagodistribucion co
            INNER JOIN pv_presupuestodet pptod ON (
                pptod.CodOrganismo = co.CodOrganismo
                AND pptod.CodPresupuesto = co.CodPresupuesto
                AND pptod.CodFuente = co.CodFuente
                AND pptod.cod_partida = co.cod_partida
            )
            INNER JOIN pv_presupuesto ppto ON (
                pptod.CodOrganismo = ppto.CodOrganismo
                AND pptod.CodPresupuesto = ppto.CodPresupuesto
            )
            WHERE
                co.Estado = 'PA'
                AND co.CodOrganismo = '$fp[CodOrganismo]'
                AND SUBSTRING(co.cod_partida,1,3) = '$fp[Par]'
                $filtro_ejecucion
            GROUP BY
                co.CodOrganismo,
                ppto.Ejercicio,
                SUBSTRING(co.cod_partida,1,3)";
    $MontoPagado = getVar3($sql);
    ##  
    $MontoAjustado = $MontoAjuste + $fp['MontoAprobado'] + $MontoAjusteAnterior;
    $MontoDisponible = $MontoAjustado - ($MontoCompromiso + $MontoCompromisoAnterior);
    $PorcentajeCompromiso = $MontoCompromiso * 100 / $MontoAjustado;
    $PorcentajeCausado = $MontoCausado * 100 / $MontoCompromiso;
    $PorcentajePagado = $MontoPagado * 100 / $MontoCompromiso;
    ##  
    $pdf->SetDrawColor(255,255,255);
    if ($i % 2 == 0) { $pdf->SetFillColor(255,255,255); $pdf->SetDrawColor(255,255,255); } 
    else { $pdf->SetFillColor(240,240,240); $pdf->SetDrawColor(240,240,240); }
    $pdf->SetFont('Arial','',6);
    $pdf->Row(array(utf8_decode($fp['Par']),
                    utf8_decode(getVar3("SELECT denominacion FROM pv_partida WHERE cod_partida = '$fp[Par].00.00.00'")),
                    number_format($fp['MontoAprobado'],2,',','.'),
                    number_format($MontoAjustado,2,',','.'),
                    number_format($MontoCompromisoAnterior,2,',','.'),
                    number_format($MontoCompromiso,2,',','.'),
                    number_format($PorcentajeCompromiso,2,',','.'),
                    number_format($MontoCausado,2,',','.'),
                    number_format($PorcentajeCausado,2,',','.'),
                    number_format($MontoPagado,2,',','.'),
                    number_format($PorcentajePagado,2,',','.'),
                    number_format($MontoDisponible,2,',','.')
                ));
    $pdf->Ln(1);
    ##  
    $TotalAprobado += $fp['MontoAprobado'];
    $TotalAjustado += $MontoAjustado;
    $TotalCompromisoAnterior += $MontoCompromisoAnterior;
    $TotalCompromiso += $MontoCompromiso;
    $TotalCausado += $MontoCausado;
    $TotalPagado += $MontoPagado;
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
$pdf->SetWidths(array(87,20,20,20,20,10,20,10,20,10,20));
$pdf->SetAligns(array('C','R','R','R','R','R','R','R','R','R','R'));
$pdf->Row(array('TOTALES',
                number_format($TotalAprobado,2,',','.'),
                number_format($TotalAjustado,2,',','.'),
                number_format($TotalCompromisoAnterior,2,',','.'),
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