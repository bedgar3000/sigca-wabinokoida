<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$filtro = '';
if (trim($fCodOrganismo)) $filtro .= " AND ajd.CodOrganismo = '$fCodOrganismo'";
if (trim($fCodDependencia)) $filtro .= " AND cc.CodDependencia = '$fCodDependencia'";
if (trim($fCodUnidadEjec)) $filtro .= " AND cp.CodUnidadEjec = '$fCodUnidadEjec'";
if (trim($fCategoriaProg)) $filtro .= " AND cp.CategoriaProg = '$fCategoriaProg'";
if (trim($fPeriodoD)) $filtro .= " AND aj.Periodo >= '$fPeriodoD'";
if (trim($fPeriodoH)) $filtro .= " AND aj.Periodo <= '$fPeriodoH'";
if (trim($fPar)) $filtro .= " AND SUBSTRING(ajd.cod_partida, 1, 3) = '$fPar'";
if (trim($fGen)) $filtro .= " AND SUBSTRING(ajd.cod_partida, 1, 6) = '$fPar.$fGen'";
if (trim($fEsp)) $filtro .= " AND SUBSTRING(ajd.cod_partida, 1, 9) = '$fPar.$fGen.$fEsp'";
if (trim($fSub)) $filtro .= " AND SUBSTRING(ajd.cod_partida, 1, 12) = '$fPar.$fGen.$fEsp.$fSub'";
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
        $this->SetX(25, 5); $this->Cell(175, 5, strtoupper(utf8_decode($NomOrganismo)), 0, 1, 'L');
        $this->SetX(25, 5); $this->Cell(175, 5, strtoupper(utf8_decode($NomDependencia)), 0, 1, 'L');
        $this->SetFont('Arial', '', 8);
        $this->SetXY(180, 10); $this->Cell(10, 5, utf8_decode('Fecha: '), 0, 0, 'L');
        $this->Cell(30, 5, formatFechaDMA($FechaActual), 0, 1, 'L');
        $this->SetXY(180, 15); $this->Cell(10, 5, utf8_decode('Página: '), 0, 0, 'L');
        $this->Cell(30, 5, $this->PageNo().' de {nb}', 0, 1, 'L');
        ##  -------------------
        $this->SetY(25); 
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(196, 5, utf8_decode('AJUSTES PRESUPUESTARIOS CONSOLIDADO POR PARTIDAS'), 0, 1, 'C');
        $this->Cell(196, 5, utf8_decode('INCREMENTOS DEL ' . $fPeriodoD . ' AL ' . $fPeriodoH), 0, 1, 'C');
        $this->Ln(5);
        ##  
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(196, 5, mb_strtoupper(utf8_decode($f['CatProg'].' '.$f['Actividad'])), 0, 1, 'L');
        $this->Ln(1);
        ##  
        $this->SetFont('Arial','B',7);
        $this->SetDrawColor(0,0,0);
        $this->SetFillColor(255,255,255);
        $this->SetWidths(array(16,130,25,25));
        $this->SetAligns(array('C','L','R','R'));
        $this->Row(array(utf8_decode('PARTIDA'),
                         utf8_decode('DENOMINACION'),
                         utf8_decode('MONTO APROBADO'),
                         utf8_decode('MONTO AJUSTADO')
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
$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->SetMargins(10, 10, 50);
$pdf->SetAutoPageBreak(1, 25);
//---------------------------------------------------
$TotalAprobado = 0;
$TotalAjustado = 0;
$Grupo = '';
$sql = "SELECT
            ajd.CodOrganismo,
            ajd.CodPresupuesto,
            ajd.CodFuente,
            ajd.cod_partida,
            CONCAT(
                ss.CodSector,
                pr.CodPrograma,
                a.CodActividad
            ) AS CatProg,
            a.Denominacion AS Actividad,
            pv.denominacion AS Partida,
            pptod.MontoAprobado,
            SUM(ajd.MontoAjuste) AS MontoAjuste
        FROM
            pv_ajustesdet ajd
        INNER JOIN pv_ajustes aj ON (
            aj.CodOrganismo = ajd.CodOrganismo
            AND aj.CodAjuste = ajd.CodAjuste
        )
        INNER JOIN pv_presupuestodet pptod ON (
            pptod.CodOrganismo = ajd.CodOrganismo
            AND pptod.CodPresupuesto = ajd.CodPresupuesto
            AND pptod.CodFuente = ajd.CodFuente
            AND pptod.cod_partida = ajd.cod_partida
        )
        INNER JOIN pv_partida pv ON (
            pv.cod_partida = ajd.cod_partida
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
            AND ajd.Tipo = 'D' $filtro
        GROUP BY
            CodOrganismo,
            CodPresupuesto,
            CodFuente,
            cod_partida
        ORDER BY
            CatProg,
            CodFuente,
            cod_partida";
$field_detalle = getRecords($sql);
foreach ($field_detalle as $f) {
    if ($Grupo != $f['CatProg']) {
        if ($Grupo) {
            $pdf->SetFillColor(255,255,255);
            $pdf->SetDrawColor(0,0,0);
            $pdf->SetFont('Arial','B',7);
            $pdf->SetWidths(array(146,25,25));
            $pdf->SetAligns(array('C','R','R'));
            $pdf->Row(array('TOTAL',
                            number_format($TotalAprobado,2,',','.'),
                            number_format($TotalAjustado,2,',','.')
                        ));
        }
        $Grupo = $f['CatProg'];
        ##  
        $pdf->AddPage();
    }
    ##  
    $pdf->SetDrawColor(255,255,255);
    if ($i % 2 == 0) { $pdf->SetFillColor(255,255,255); $pdf->SetDrawColor(255,255,255); } else { $pdf->SetFillColor(240,240,240); $pdf->SetDrawColor(240,240,240); }
    if ($f['Tipo'] == 'Cta') $pdf->SetFont('Arial','BUI',6);
    elseif ($f['Tipo'] == 'Par') $pdf->SetFont('Arial','BU',6);
    elseif ($f['Tipo'] == 'Gen') $pdf->SetFont('Arial','B',6);
    else $pdf->SetFont('Arial','',6);
    $pdf->Row(array(utf8_decode($f['cod_partida']),
                    utf8_decode($f['Partida']),
                    number_format($f['MontoAprobado'],2,',','.'),
                    number_format($f['MontoAjuste'],2,',','.')
                ));
    $pdf->Ln(1);
    ##  
    $TotalAprobado += $f['MontoAprobado'];
    $TotalAjustado += $f['MontoAjuste'];
    ++$i;
}
$pdf->SetFillColor(255,255,255);
$pdf->SetDrawColor(0,0,0);
$pdf->SetFont('Arial','B',7);
$pdf->SetWidths(array(146,25,25));
$pdf->SetAligns(array('C','R','R'));
$pdf->Row(array('TOTAL',
                number_format($TotalAprobado,2,',','.'),
                number_format($TotalAjustado,2,',','.')
            ));
//---------------------------------------------------

//---------------------------------------------------
//  Muestro el contenido del pdf.
$pdf->Output();
?>