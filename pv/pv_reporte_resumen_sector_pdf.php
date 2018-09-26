<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$fPeriodoD = formatFechaAMD($fPeriodoD);
$fPeriodoH = formatFechaAMD($fPeriodoH);
$fEjercicioD = substr($fPeriodoD,0,4);
$fEjercicioH = substr($fPeriodoH,0,4);
$filtro_sectores = '';
if (trim($fCodOrganismo)) $filtro_sectores .= " AND ppto.CodOrganismo = '$fCodOrganismo'";
if (trim($fIdSubSector)) $filtro_sectores .= " AND ss.IdSubSector = '$fIdSubSector'";
if (trim($fPar)) $filtro_sectores .= " AND SUBSTRING(pptod.cod_partida,1,3) = '$fPar'";
if (trim($fPeriodoD)) $filtro_sectores.=" AND (ppto.Ejercicio >= '$fEjercicioD')";
if (trim($fPeriodoH)) $filtro_sectores.=" AND (ppto.Ejercicio <= '$fEjercicioH')";
$filtro_partida = '';
if (trim($fPar)) $filtro_partida .= " AND CONCAT_WS('',pv.cod_tipocuenta,pv.partida1) = '$fPar'";
if (trim($fPeriodoD)) $filtro_partida.=" AND (ppto.Ejercicio >= '$fEjercicioD')";
if (trim($fPeriodoH)) $filtro_partida.=" AND (ppto.Ejercicio <= '$fEjercicioH')";
$filtro_ejecucion = '';
if (trim($fPeriodoD)) $filtro_ejecucion.=" AND (co.FechaEjecucion >= '$fPeriodoD')";
if (trim($fPeriodoH)) $filtro_ejecucion.=" AND (co.FechaEjecucion <= '$fPeriodoH')";
if (trim($fPeriodoD)) $filtro_ejecucion.=" AND (ppto.Ejercicio >= '$fEjercicioD')";
if (trim($fPeriodoH)) $filtro_ejecucion.=" AND (ppto.Ejercicio <= '$fEjercicioH')";
$filtro_ajuste = '';
if (trim($fPeriodoD)) $filtro_ajuste.=" AND (aj.Fecha >= '$fPeriodoD')";
if (trim($fPeriodoH)) $filtro_ajuste.=" AND (aj.Fecha <= '$fPeriodoH')";
if (trim($fPeriodoD)) $filtro_ajuste.=" AND (ppto.Ejercicio >= '$fEjercicioD')";
if (trim($fPeriodoH)) $filtro_ajuste.=" AND (ppto.Ejercicio <= '$fEjercicioH')";
##  Organismo
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
        global $_POST;
        global $_GET;
        extract($_POST);
        extract($_GET);
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
        $this->Cell(257, 5, strtoupper(utf8_decode('RESUMEN ESTADISTICO DE PARTIDAS POR SECTOR')), 0, 1, 'C');
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
##  Sectores
$sql = "SELECT
            ppto.CodOrganismo,
            ppto.Ejercicio,
            s.CodSector,
            UPPER(s.Denominacion) AS Sector
        FROM pv_presupuesto ppto
        INNER JOIN pv_presupuestodet pptod ON (
            pptod.CodOrganismo = ppto.CodOrganismo
            AND pptod.CodPresupuesto = ppto.CodPresupuesto
        )
        INNER JOIN pv_categoriaprog cp ON cp.CategoriaProg = ppto.CategoriaProg
        INNER JOIN pv_unidadejecutora ue ON ue.CodUnidadEjec = cp.CodUnidadEjec
        INNER JOIN pv_unidadejecutoradep ued ON ued.CodUnidadEjec = ue.CodUnidadEjec
        INNER JOIN pv_actividades a ON a.IdActividad = cp.IdActividad
        INNER JOIN pv_proyectos py ON py.IdProyecto = a.IdProyecto
        INNER JOIN pv_subprogramas spr ON spr.IdSubPrograma = py.IdSubPrograma
        INNER JOIN pv_programas pr ON pr.IdPrograma = spr.IdPrograma
        INNER JOIN pv_subsector ss ON ss.IdSubSector = pr.IdSubSector
        INNER JOIN pv_sector s ON s.CodSector = ss.CodSector
        WHERE 1 $filtro_sectores
        GROUP BY CodOrganismo, Ejercicio, CodSector
        ORDER BY CodOrganismo, Ejercicio, CodSector";
$field_sectores = getRecords($sql);
foreach ($field_sectores as $fs)
{
    if ($Grupo != $fs['CodSector']) {
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
        $pdf->Cell(257, 5, utf8_decode($fs['CodSector'].' '.$fs['Sector']), 0, 1, 'L');
        ##  
        $pdf->SetFont('Arial','B',6);
        $pdf->SetDrawColor(0,0,0);
        $pdf->SetFillColor(255,255,255);
        $pdf->SetWidths(array(10,97,20,20,20,10,20,10,20,10,20));
        $pdf->SetAligns(array('C','L','R','R','R','R','R','R','R','R','R'));
        $pdf->Row(array(utf8_decode('PAR'),
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
        $Grupo = $fs['CodSector'];
        $i = 0;
    }
    ##  Partidas
    $sql = "SELECT
                pptod.CodOrganismo,
                ppto.Ejercicio,
                s.CodSector,
                SUBSTRING(pptod.cod_partida,1,3) AS Par,
                SUM(pptod.MontoAprobado) AS MontoAprobado
            FROM pv_presupuestodet pptod
            INNER JOIN pv_presupuesto ppto ON (
                pptod.CodOrganismo = ppto.CodOrganismo
                AND pptod.CodPresupuesto = ppto.CodPresupuesto
            )
            INNER JOIN pv_categoriaprog cp ON cp.CategoriaProg = ppto.CategoriaProg
            INNER JOIN pv_unidadejecutora ue ON ue.CodUnidadEjec = cp.CodUnidadEjec
            INNER JOIN pv_unidadejecutoradep ued ON ued.CodUnidadEjec = ue.CodUnidadEjec
            INNER JOIN pv_actividades a ON a.IdActividad = cp.IdActividad
            INNER JOIN pv_proyectos py ON py.IdProyecto = a.IdProyecto
            INNER JOIN pv_subprogramas spr ON spr.IdSubPrograma = py.IdSubPrograma
            INNER JOIN pv_programas pr ON pr.IdPrograma = spr.IdPrograma
            INNER JOIN pv_subsector ss ON ss.IdSubSector = pr.IdSubSector
            INNER JOIN pv_sector s ON s.CodSector = ss.CodSector
            WHERE
                pptod.CodOrganismo = '$fs[CodOrganismo]'
                AND ppto.Ejercicio = '$fs[Ejercicio]'
                AND s.CodSector = '$fs[CodSector]'
                $filtro_partida
            GROUP BY CodOrganismo, Ejercicio, CodSector, Par
            ORDER BY CodOrganismo, Ejercicio, CodSector, Par";
    $field_partidas = getRecords($sql);
    foreach ($field_partidas as $fp)
    {
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
                INNER JOIN pv_categoriaprog cp ON cp.CategoriaProg = ppto.CategoriaProg
                INNER JOIN pv_unidadejecutora ue ON ue.CodUnidadEjec = cp.CodUnidadEjec
                INNER JOIN pv_unidadejecutoradep ued ON ued.CodUnidadEjec = ue.CodUnidadEjec
                INNER JOIN pv_actividades a ON a.IdActividad = cp.IdActividad
                INNER JOIN pv_proyectos py ON py.IdProyecto = a.IdProyecto
                INNER JOIN pv_subprogramas spr ON spr.IdSubPrograma = py.IdSubPrograma
                INNER JOIN pv_programas pr ON pr.IdPrograma = spr.IdPrograma
                INNER JOIN pv_subsector ss ON ss.IdSubSector = pr.IdSubSector
                INNER JOIN pv_sector s ON s.CodSector = ss.CodSector
                WHERE
                    aj.Estado = 'AP'
                    AND co.CodOrganismo = '$fp[CodOrganismo]'
                    AND ppto.Ejercicio = '$fp[Ejercicio]'
                    AND s.CodSector = '$fp[CodSector]'
                    AND SUBSTRING(co.cod_partida,1,3) = '$fp[Par]'
                    $filtro_ajuste
                GROUP BY
                    ppto.CodOrganismo,
                    ppto.Ejercicio,
                    s.CodSector,
                    SUBSTRING(co.cod_partida,1,3)";
        $MontoAjuste = getVar3($sql);
        ##  Monto Compromiso
        $sql = "SELECT SUM(co.Monto)
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
                INNER JOIN pv_categoriaprog cp ON cp.CategoriaProg = ppto.CategoriaProg
                INNER JOIN pv_unidadejecutora ue ON ue.CodUnidadEjec = cp.CodUnidadEjec
                INNER JOIN pv_unidadejecutoradep ued ON ued.CodUnidadEjec = ue.CodUnidadEjec
                INNER JOIN pv_actividades a ON a.IdActividad = cp.IdActividad
                INNER JOIN pv_proyectos py ON py.IdProyecto = a.IdProyecto
                INNER JOIN pv_subprogramas spr ON spr.IdSubPrograma = py.IdSubPrograma
                INNER JOIN pv_programas pr ON pr.IdPrograma = spr.IdPrograma
                INNER JOIN pv_subsector ss ON ss.IdSubSector = pr.IdSubSector
                INNER JOIN pv_sector s ON s.CodSector = ss.CodSector
                WHERE
                    co.Estado = 'CO'
                    AND co.CodOrganismo = '$fp[CodOrganismo]'
                    AND ppto.Ejercicio = '$fp[Ejercicio]'
                    AND s.CodSector = '$fp[CodSector]'
                    AND SUBSTRING(co.cod_partida,1,3) = '$fp[Par]'
                    $filtro_ejecucion
                GROUP BY
                    ppto.CodOrganismo,
                    ppto.Ejercicio,
                    s.CodSector,
                    SUBSTRING(co.cod_partida,1,3)";
        $MontoCompromiso = getVar3($sql);
        ##  Monto Causado
        $sql = "SELECT SUM(co.Monto)
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
                INNER JOIN pv_categoriaprog cp ON cp.CategoriaProg = ppto.CategoriaProg
                INNER JOIN pv_unidadejecutora ue ON ue.CodUnidadEjec = cp.CodUnidadEjec
                INNER JOIN pv_unidadejecutoradep ued ON ued.CodUnidadEjec = ue.CodUnidadEjec
                INNER JOIN pv_actividades a ON a.IdActividad = cp.IdActividad
                INNER JOIN pv_proyectos py ON py.IdProyecto = a.IdProyecto
                INNER JOIN pv_subprogramas spr ON spr.IdSubPrograma = py.IdSubPrograma
                INNER JOIN pv_programas pr ON pr.IdPrograma = spr.IdPrograma
                INNER JOIN pv_subsector ss ON ss.IdSubSector = pr.IdSubSector
                INNER JOIN pv_sector s ON s.CodSector = ss.CodSector
                WHERE
                    co.Estado = 'CA'
                    AND co.CodOrganismo = '$fp[CodOrganismo]'
                    AND ppto.Ejercicio = '$fp[Ejercicio]'
                    AND s.CodSector = '$fp[CodSector]'
                    AND SUBSTRING(co.cod_partida,1,3) = '$fp[Par]'
                    $filtro_ejecucion
                GROUP BY
                    ppto.CodOrganismo,
                    ppto.Ejercicio,
                    s.CodSector,
                    SUBSTRING(co.cod_partida,1,3)";
        $MontoCausado = getVar3($sql);
        ##  Monto Pagado
        $sql = "SELECT SUM(co.Monto)
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
                INNER JOIN pv_categoriaprog cp ON cp.CategoriaProg = ppto.CategoriaProg
                INNER JOIN pv_unidadejecutora ue ON ue.CodUnidadEjec = cp.CodUnidadEjec
                INNER JOIN pv_unidadejecutoradep ued ON ued.CodUnidadEjec = ue.CodUnidadEjec
                INNER JOIN pv_actividades a ON a.IdActividad = cp.IdActividad
                INNER JOIN pv_proyectos py ON py.IdProyecto = a.IdProyecto
                INNER JOIN pv_subprogramas spr ON spr.IdSubPrograma = py.IdSubPrograma
                INNER JOIN pv_programas pr ON pr.IdPrograma = spr.IdPrograma
                INNER JOIN pv_subsector ss ON ss.IdSubSector = pr.IdSubSector
                INNER JOIN pv_sector s ON s.CodSector = ss.CodSector
                WHERE
                    co.Estado = 'PA'
                    AND co.CodOrganismo = '$fp[CodOrganismo]'
                    AND ppto.Ejercicio = '$fp[Ejercicio]'
                    AND s.CodSector = '$fp[CodSector]'
                    AND SUBSTRING(co.cod_partida,1,3) = '$fp[Par]'
                    $filtro_ejecucion
                GROUP BY
                    ppto.CodOrganismo,
                    ppto.Ejercicio,
                    s.CodSector,
                    SUBSTRING(co.cod_partida,1,3)";
        $MontoPagado = getVar3($sql);
        ##  
        $MontoAjustado = $MontoAjuste + $fp['MontoAprobado'];
        $PorcentajeCompromiso = $MontoCompromiso * 100 / $MontoAjustado;
        $PorcentajeCausado = $MontoCausado * 100 / $MontoCompromiso;
        $PorcentajePagado = $MontoPagado * 100 / $MontoCompromiso;
        $MontoDisponible = $MontoAjustado - $MontoCompromiso;
        ##  
        $pdf->SetDrawColor(255,255,255);
        if ($i % 2 == 0) { $pdf->SetFillColor(255,255,255); $pdf->SetDrawColor(255,255,255); } else { $pdf->SetFillColor(240,240,240); $pdf->SetDrawColor(240,240,240); }
        $pdf->SetFont('Arial','',6);
        $pdf->Row(array(utf8_decode($fp['Par']),
                        utf8_decode(getVar3("SELECT denominacion FROM pv_partida WHERE cod_partida = '$fp[Par].00.00.00'")),
                        number_format($fp['MontoAprobado'],2,',','.'),
                        number_format($MontoAjustado,2,',','.'),
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
        $TotalAprobado += $MontoAprobado;
        $TotalAjustado += $MontoAjustado;
        $TotalCompromiso += $MontoCompromiso;
        $TotalCausado += $MontoCausado;
        $TotalPagado += $MontoPagado;
        $TotalDisponible += $MontoDisponible;
        ++$i;
    }
}
//---------------------------------------------------

//---------------------------------------------------
//  Muestro el contenido del pdf.
$pdf->Output();
?>