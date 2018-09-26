<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$fFechaD = formatFechaAMD($fFechaD);
$fFechaH = formatFechaAMD($fFechaH);
$filtro_categorias = '';
if (trim($fCodOrganismo)) $filtro_categorias .= " AND ppto.CodOrganismo = '$fCodOrganismo'";
if (trim($fEjercicio)) $filtro_categorias .= " AND ppto.Ejercicio = '$fEjercicio'";
if (trim($fIdSubSector)) $filtro_categorias .= " AND ss.IdSubSector = '$fIdSubSector'";
if (trim($fIdPrograma)) $filtro_categorias .= " AND pr.IdPrograma = '$fIdPrograma'";
if (trim($fIdSubPrograma)) $filtro_categorias .= " AND spr.IdSubPrograma = '$fIdSubPrograma'";
if (trim($fIdProyecto)) $filtro_categorias .= " AND py.IdProyecto = '$fIdProyecto'";
if (trim($fIdActividad)) $filtro_categorias .= " AND a.IdActividad = '$fIdActividad'";
if (trim($fCodUnidadEjec)) $filtro_categorias .= " AND ue.CodUnidadEjec = '$fCodUnidadEjec'";
if (trim($fCodDependencia)) $filtro_categorias.=" AND (ued.CodDependencia = '$fCodDependencia')";
if (trim($fPar)) $filtro_categorias .= " AND SUBSTRING(pptod.cod_partida,1,3) = '$fPar'";
if (trim($fGen)) $filtro_categorias .= " AND pptod.generica = '$fGen'";
if (trim($fEsp)) $filtro_categorias .= " AND pptod.especifica = '$fEsp'";
if (trim($fSub)) $filtro_categorias .= " AND pptod.subespecifica = '$fSub'";
$filtro_partida = '';
if (trim($fPar)) $filtro_partida .= " AND CONCAT_WS('',pv.cod_tipocuenta,pv.partida1) = '$fPar'";
if (trim($fGen)) $filtro_partida .= " AND pv.generica = '$fGen'";
if (trim($fEsp)) $filtro_partida .= " AND pv.especifica = '$fEsp'";
if (trim($fSub)) $filtro_partida .= " AND pv.subespecifica = '$fSub'";
$filtro_ejecucion = '';
if (trim($fFechaD)) $filtro_ejecucion.=" AND (co.FechaEjecucion >= '$fFechaD')";
if (trim($fFechaH)) $filtro_ejecucion.=" AND (co.FechaEjecucion <= '$fFechaH')";
$filtro_ajuste = '';
if (trim($fFechaD)) $filtro_ajuste.=" AND (aj.Fecha >= '$fFechaD')";
if (trim($fFechaH)) $filtro_ajuste.=" AND (aj.Fecha <= '$fFechaH')";
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
        global $fc;
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
        $this->Cell(257, 5, strtoupper(utf8_decode('EJECUCIÓN PRESUPUESTARIA POR PERIODO')), 0, 1, 'C');
        $this->Ln(5);
        ##  
        $this->SetFont('Arial', '', 8); $this->Cell(45, 6, utf8_decode('CATEGORÍA PROGRAMÁTICA:'), 0, 0, 'L');
        $this->SetFont('Arial', 'B', 8); $this->Cell(212, 6, strtoupper(utf8_decode($fc['CatProg'].' '.$fc['Actividad'])), 0, 1, 'L');
        $this->Ln(1);
        $this->SetFont('Arial', '', 8); $this->Cell(45, 6, utf8_decode('UNIDAD EJECUTORA:'), 0, 0, 'L');
        $this->SetFont('Arial', 'B', 8); $this->Cell(212, 6, strtoupper(utf8_decode($fc['UnidadEjecutora'])), 0, 1, 'L');
        $this->Ln(2);
        ##  
        $this->SetFont('Arial','B',6);
        $this->SetDrawColor(0,0,0);
        $this->SetFillColor(255,255,255);
        $this->SetWidths(array(8,8,8,8,8,75,24,24,24,24,24,24));
        $this->SetAligns(array('C','C','C','C','C','L','R','R','R','R','R','R'));
        $this->Row(array(utf8_decode('PAR'),
                         utf8_decode('GEN'),
                         utf8_decode('ESP'),
                         utf8_decode('SUB'),
                         utf8_decode('F.F.'),
                         utf8_decode('DENOMINACION'),
                         utf8_decode('APROBADO'),
                         utf8_decode('AJUSTADO'),
                         utf8_decode('COMPROMETIDO'),
                         utf8_decode('CAUSADO'),
                         utf8_decode('PAGADO'),
                         utf8_decode('DISP. PRESUP.')
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
//---------------------------------------------------
$i = 0;
$Grupo = '';
##  Categorías Programáticas
$sql = "SELECT
            ppto.CodOrganismo,
            ppto.CodPresupuesto,
            ppto.Ejercicio,
            ppto.CategoriaProg,
            CONCAT_WS('',s.CodSector,pr.CodPrograma,a.CodActividad) AS CatProg,
            UPPER(a.Denominacion) AS Actividad,
            UPPER(ue.Denominacion) AS UnidadEjecutora
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
        WHERE 1 $filtro_categorias
        GROUP BY Ejercicio, CatProg
        ORDER BY Ejercicio, CatProg";
$field_categorias = getRecords($sql);
foreach ($field_categorias as $fc)
{
    $pdf->AddPage();
    ##  Partidas
    $sql = "SELECT
                pptod.CodOrganismo,
                pptod.CodPresupuesto,
                pptod.CodFuente,
                pptod.cod_partida,
                CONCAT_WS('',pv.cod_tipocuenta,pv.partida1) AS Par,
                pv.generica AS Gen,
                pv.especifica AS Esp,
                pv.subespecifica AS Sub,
                pv.denominacion AS NombrePartida,
                pptod.MontoAprobado
            FROM pv_presupuestodet pptod
            INNER JOIN pv_partida pv ON pv.cod_partida = pptod.cod_partida
            WHERE
                pptod.CodOrganismo = '$fc[CodOrganismo]'
                AND pptod.CodPresupuesto = '$fc[CodPresupuesto]'
                $filtro_partida
            ORDER BY CodOrganismo, CodPresupuesto, CodFuente, cod_partida";
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
                WHERE
                    aj.Estado = 'AP'
                    AND co.CodOrganismo = '$fp[CodOrganismo]'
                    AND co.CodPresupuesto = '$fp[CodPresupuesto]'
                    AND co.CodFuente = '$fp[CodFuente]'
                    AND co.cod_partida = '$fp[cod_partida]'
                    $filtro_ajuste
                GROUP BY
                    co.CodOrganismo,
                    co.CodPresupuesto,
                    co.CodFuente,
                    co.cod_partida";
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
                WHERE
                    co.Estado = 'CO'
                    AND co.CodOrganismo = '$fp[CodOrganismo]'
                    AND co.CodPresupuesto = '$fp[CodPresupuesto]'
                    AND co.CodFuente = '$fp[CodFuente]'
                    AND co.cod_partida = '$fp[cod_partida]'
                    $filtro_ejecucion
                GROUP BY
                    co.CodOrganismo,
                    co.CodPresupuesto,
                    co.CodFuente,
                    co.cod_partida";
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
                WHERE
                    co.Estado = 'CA'
                    AND co.CodOrganismo = '$fp[CodOrganismo]'
                    AND co.CodPresupuesto = '$fp[CodPresupuesto]'
                    AND co.CodFuente = '$fp[CodFuente]'
                    AND co.cod_partida = '$fp[cod_partida]'
                    $filtro_ejecucion
                GROUP BY
                    co.CodOrganismo,
                    co.CodPresupuesto,
                    co.CodFuente,
                    co.cod_partida";
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
                WHERE
                    co.Estado = 'PA'
                    AND co.CodOrganismo = '$fp[CodOrganismo]'
                    AND co.CodPresupuesto = '$fp[CodPresupuesto]'
                    AND co.CodFuente = '$fp[CodFuente]'
                    AND co.cod_partida = '$fp[cod_partida]'
                    $filtro2
                GROUP BY
                    co.CodOrganismo,
                    co.CodPresupuesto,
                    co.CodFuente,
                    co.cod_partida";
        $MontoPagado = getVar3($sql);
        ##  
        $MontoAjustado = $MontoAjuste + $fp['MontoAprobado'];
        $DisponibilidadPresupuestaria = $MontoAjustado - $MontoCompromiso;
        ##  
        $pdf->SetDrawColor(255,255,255);
        if ($i % 2 == 0) $pdf->SetFillColor(255,255,255); else $pdf->SetFillColor(240,240,240);
        $pdf->SetFont('Arial','',6);
        $pdf->Row(array(utf8_decode($fp['Par']),
                        utf8_decode($fp['Gen']),
                        utf8_decode($fp['Esp']),
                        utf8_decode($fp['Sub']),
                        utf8_decode($fp['CodFuente']),
                        utf8_decode($fp['NombrePartida']),
                        number_format($fp['MontoAprobado'],2,',','.'),
                        number_format($MontoAjustado,2,',','.'),
                        number_format($MontoCompromiso,2,',','.'),
                        number_format($MontoCausado,2,',','.'),
                        number_format($MontoPagado,2,',','.'),
                        number_format($DisponibilidadPresupuestaria,2,',','.')
                    ));
        $pdf->Ln(1);
        ++$i;

    }
}
//---------------------------------------------------

//---------------------------------------------------
//  Muestro el contenido del pdf.
$pdf->Output();
?>