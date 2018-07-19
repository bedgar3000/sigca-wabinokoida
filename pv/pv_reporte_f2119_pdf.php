<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$filtro = '';
if (trim($fCodOrganismo)) $filtro .= " AND rspf.CodOrganismo = '$fCodOrganismo'";
if (trim($fEjercicio)) $filtro .= " AND rspf.Ejercicio = '$fEjercicio'";
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
        global $field_categoria;
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
        $this->Cell(258, 5, utf8_decode('CRÉDITOS PRESUPUESTARIOS DEL SECTOR POR PROGRAMA A NIVEL DE PARTIDAS Y FUENTES DE FINANCIAMIENTO'), 0, 1, 'C');
        $this->SetDrawColor(0, 0, 0);
        $this->Rect(10, 18, 258, 30, "D");
        ##  
        $this->SetY(54);
        $this->SetFont('Arial','B',7);
        $this->SetDrawColor(0,0,0);
        $this->SetFillColor(255,255,255);
        $this->SetWidths(array(32,206));
        $this->SetAligns(array('C','L'));
        $this->Cell(20, 5, utf8_decode(''), 0, 0, 'C');
        $this->Row(array(utf8_decode('CODIGO'),
                         utf8_decode('DENOMINACION')
                ));
        $this->Cell(20, 5, utf8_decode('SECTOR'), 1, 0, 'C');
        $this->Row(array(utf8_decode($field_categoria['CodClaSectorial']),
                         utf8_decode($field_categoria['SubSector'])
                ));
        $this->Cell(20, 5, utf8_decode('PROGRAMA'), 1, 0, 'C');
        $this->Row(array(utf8_decode($field_categoria['CodPrograma']),
                         utf8_decode($field_categoria['Programa'])
                ));
        ##  
        $this->SetFont('Arial','B',7);
        ##  
        $this->SetXY(168,69); $this->Cell(100, 5, utf8_decode('ASIGNACIÓN PRESUPUESTARIA'), 1, 0, 'C');
        $this->SetXY(168,74); $this->Cell(60, 5, utf8_decode('APORTE LEGAL'), 1, 0, 'C');
        $this->SetXY(168,79); $this->Cell(40, 5, utf8_decode('SITUADO'), 1, 0, 'C');
        ##  
        $this->SetY(85);
        $this->SetDrawColor(255,255,255);
        $this->SetFillColor(255,255,255);
        $this->SetWidths(array(20,118,20,20,20,20,20,20));
        $this->SetAligns(array('C','C','C','C','C','C','C','C'));
        $this->Row(array(utf8_decode('PARTIDA'),
                         utf8_decode('DENOMINACION'),
                         utf8_decode('INGRESOS PROPIOS'),
                         utf8_decode('MUNICIPAL'),
                         utf8_decode('ESTADAL A MUNICIPAL'),
                         utf8_decode('FCI'),
                         utf8_decode('OTRAS'),
                         utf8_decode('TOTAL')
                ));
        $this->Ln(1);
    }
    
    //  Pie de página.
    function Footer() {
        global $MontoTotal;
        global $IngresosPropios;
        global $Municipal;
        global $EstadalMunicipal;
        global $FCI;
        global $Otras;
        global $FlagTotal;
        ##  
        $this->SetDrawColor(0,0,0);
        $this->SetFillColor(0,0,0);
        $this->Rect(10, 69, 258, 131, "D");
        $this->Rect(30, 69, 0.1, 131, "FD");
        $this->Rect(148, 69, 0.1, 131, "FD");
        $this->Rect(168, 84, 0.1, 116, "FD");
        $this->Rect(188, 84, 0.1, 116, "FD");
        $this->Rect(208, 84, 0.1, 116, "FD");
        $this->Rect(228, 74, 0.1, 125, "FD");
        $this->Rect(248, 74, 0.1, 126, "FD");
        $this->Rect(10, 94, 258, 0.1, "FD");
        ##  
        if ($FlagTotal) {
            $this->SetY(200);
            $this->SetDrawColor(0,0,0);
            $this->SetFont('Arial','B',7);
            $this->Cell(138, 5, utf8_decode('TOTAL'), 1, 0, 'R');
            $this->Cell(20, 5, number_format($IngresosPropios,2,',','.'), 1, 0, 'R');
            $this->Cell(20, 5, number_format($Municipal,2,',','.'), 1, 0, 'R');
            $this->Cell(20, 5, number_format($EstadalMunicipal,2,',','.'), 1, 0, 'R');
            $this->Cell(20, 5, number_format($FCI,2,',','.'), 1, 0, 'R');
            $this->Cell(20, 5, number_format($Otras,2,',','.'), 1, 0, 'R');
            $this->Cell(20, 5, number_format($MontoTotal,2,',','.'), 1, 0, 'R');
        }
        ##  
        $this->SetY(205);
        $this->SetFont('Arial','B',7);
        $this->Cell(195, 5, utf8_decode('FORMA:     2119'), 0, 0, 'L');
    }
}
//---------------------------------------------------

//---------------------------------------------------
//  Creación del objeto de la clase heredada.
$pdf = new PDF('L', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->SetMargins(10, 10, 50);
$pdf->SetAutoPageBreak(1, 15);
//---------------------------------------------------
$FlagTotal = false;
$MontoTotal = 0;
$Grupo = '';
##  
$sql = "SELECT
            rspf.CodSector,
            rspf.CodClaSectorial,
            ss.Denominacion AS SubSector,
            rspf.IdPrograma,
            rspf.CodPrograma,
            pr.Denominacion AS Programa,
            SUBSTRING(pv.cod_partida, 1, 3) AS Par,
            (SELECT denominacion FROM pv_partida WHERE SUBSTRING(cod_partida, 1, 3) = SUBSTRING(pv.cod_partida, 1, 3) AND generica = '00' AND especifica = '00' AND subespecifica = '00') denominacion,
            SUM(CASE WHEN rspf.CodFuente = '01' AND rspf.cod_partida LIKE CONCAT(pv.cod_tipocuenta, pv.partida1, '.%') THEN rspf.Monto ELSE 0 END) AS IngresosPropios,
            SUM(CASE WHEN rspf.CodFuente = '02' AND rspf.cod_partida LIKE CONCAT(pv.cod_tipocuenta, pv.partida1, '.%') THEN rspf.Monto ELSE 0 END) AS Municipal,
            SUM(CASE WHEN rspf.CodFuente = '03' AND rspf.cod_partida LIKE CONCAT(pv.cod_tipocuenta, pv.partida1, '.%') THEN rspf.Monto ELSE 0 END) AS EstadalMunicipal,
            SUM(CASE WHEN rspf.CodFuente = '04' AND rspf.cod_partida LIKE CONCAT(pv.cod_tipocuenta, pv.partida1, '.%') THEN rspf.Monto ELSE 0 END) AS FCI,
            SUM(CASE WHEN rspf.CodFuente > '04' AND rspf.cod_partida LIKE CONCAT(pv.cod_tipocuenta, pv.partida1, '.%') THEN rspf.Monto ELSE 0 END) AS Otras
        FROM
            vw_f21_sector_programa_partida_fuente rspf
            INNER JOIN pv_subsector ss ON (ss.IdSubSector = rspf.IdSubSector)
            INNER JOIN pv_programas pr ON (pr.IdPrograma = rspf.IdPrograma)
            INNER JOIN pv_partida pv ON (pv.cod_partida = rspf.cod_partida)
        WHERE 1 $filtro
        GROUP BY CodOrganismo, Ejercicio, CodSector, IdPrograma, Par
        ORDER BY CodOrganismo, Ejercicio, CodSector, CodPrograma, Par";
$field_detalle = getRecords($sql);
foreach ($field_detalle as $f) {
    $field_categoria = $f;
    $Id = $f['CodSector'].$f['IdPrograma'];
    if ($Grupo != $Id) {
        if ($Grupo) {
            $FlagTotal = true;
        }
        $Grupo = $Id;
        $pdf->AddPage();
        $FlagTotal = false;
        $IngresosPropios = 0;
        $Municipal = 0;
        $EstadalMunicipal = 0;
        $FCI = 0;
        $Otras = 0;
    }
    $Total = $f['IngresosPropios'] + $f['Municipal'] + $f['EstadalMunicipal'] + $f['FCI'] + $f['Otras'];
    ##  
    $pdf->SetDrawColor(255,255,255);
    $pdf->SetFillColor(255,255,255);
    if ($f['Tipo'] == 'Cta') $pdf->SetFont('Arial','BUI',7);
    elseif ($f['Tipo'] == 'Par') $pdf->SetFont('Arial','BU',7);
    elseif ($f['Tipo'] == 'Gen') $pdf->SetFont('Arial','B',7);
    $pdf->SetFont('Arial','',7);
    $pdf->SetAligns(array('C','L','R','R','R','R','R','R'));
    $pdf->Row(array(utf8_decode($f['Par']),
                    utf8_decode($f['denominacion']),
                    number_format($f['IngresosPropios'],2,',','.'),
                    number_format($f['Municipal'],2,',','.'),
                    number_format($f['EstadalMunicipal'],2,',','.'),
                    number_format($f['FCI'],2,',','.'),
                    number_format($f['Otras'],2,',','.'),
                    number_format($Total,2,',','.')
                ));
    $MontoTotal += $Total;
    $IngresosPropios += $f['IngresosPropios'];
    $Municipal += $f['Municipal'];
    $EstadalMunicipal += $f['EstadalMunicipal'];
    $FCI += $f['FCI'];
    $Otras += $f['Otras'];
}
$FlagTotal = true;
//---------------------------------------------------

//---------------------------------------------------
//  Muestro el contenido del pdf.
$pdf->Output();
?>