<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$filtro = '';
if (trim($fCodOrganismo)) $filtro .= " AND rspf.CodOrganismo = '$fCodOrganismo'";
if (trim($fEjercicio)) $filtro .= " AND rspf.Ejercicio = '$fEjercicio'";
if (trim($fIdSubSector)) $filtro .= " AND rspf.IdSubSector = '$fIdSubSector'";
if (trim($fPar)) $filtro .= " AND SUBSTRING(rspf.cod_partida, 1, 3) = '$fPar'";
if (trim($fGen)) $filtro .= " AND SUBSTRING(rspf.cod_partida, 1, 6) = '$fPar.$fGen'";
if (trim($fEsp)) $filtro .= " AND SUBSTRING(rspf.cod_partida, 1, 9) = '$fPar.$fGen.$fEsp'";
if (trim($fSub)) $filtro .= " AND SUBSTRING(rspf.cod_partida, 1, 12) = '$fPar.$fGen.$fEsp.$fSub'";
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
        $this->Cell(258, 5, utf8_decode('TRANSFERENCIAS Y DONACIONES OTORGADAS A ORGANISMOS DEL SECTOR PÚBLICO Y PRIVADO'), 0, 1, 'C');
        $this->SetDrawColor(0, 0, 0);
        $this->Rect(10, 18, 258, 30, "D");
        ##  
        $this->SetFont('Arial','B',7);
        $this->SetXY(178, 52);
        $this->Cell(60, 5, utf8_decode('TIPO DE GASTO'), 0, 0, 'C');
        $this->SetY(65);
        $this->SetDrawColor(255,255,255);
        $this->SetFillColor(255,255,255);
        $this->SetWidths(array(8,8,8,8,8,128,30,30,30));
        $this->SetAligns(array('C','C','C','C','C','C','C','C','C'));
        $this->Row(array('','','','','',
                         utf8_decode('DENOMINACION'),
                         utf8_decode('CORRIENTE'),
                         utf8_decode('CAPITAL'),
                         utf8_decode('TOTAL')));
        $this->SetFontSize(7);
        $this->TextWithDirection(15, 72,'SECTOR','U');
        $this->TextWithDirection(23, 72,'PARTIDA','U');
        $this->TextWithDirection(31, 72,'GENERICA','U');
        $this->TextWithDirection(39, 72,'ESPECIFICA','U');
        $this->TextWithDirection(47, 72,'SUB-ESPECIFICA','U');
        $this->SetAligns(array('C','C','C','C','C','L','R','R','R'));

        $this->SetY(75);
        $this->Ln(1);
    }
    
    //  Pie de página.
    function Footer() {
        global $MontoTotal;
        global $FlagTotal;
        ##  
        $this->SetDrawColor(0,0,0);
        $this->SetFillColor(0,0,0);
        $this->Rect(10, 50, 258, 150, "D");
        $this->Rect(18, 50, 0.1, 150, "FD");
        $this->Rect(26, 50, 0.1, 150, "FD");
        $this->Rect(34, 50, 0.1, 150, "FD");
        $this->Rect(42, 50, 0.1, 150, "FD");
        $this->Rect(50, 50, 0.1, 150, "FD");
        $this->Rect(178, 50, 0.1, 150, "FD");
        $this->Rect(208, 60, 0.1, 140, "FD");
        $this->Rect(238, 50, 0.1, 150, "FD");
        $this->Rect(10, 75, 258, 0.1, "FD");
        $this->Rect(178, 60, 60, 0.1, "FD");
        ##  
        if ($FlagTotal) {
            $this->SetY(200);
            $this->SetDrawColor(0,0,0);
            $this->SetFont('Arial','B',7);
            $this->Cell(168, 5, utf8_decode('TOTAL'), 1, 0, 'R');
            $this->Cell(30, 5, number_format($MontoTotal,2,',','.'), 1, 0, 'R');
            $this->Cell(30, 5, number_format($MontoTotal,2,',','.'), 1, 0, 'R');
            $this->Cell(30, 5, number_format($MontoTotal,2,',','.'), 1, 0, 'R');
        }
        ##  
        $this->SetY(205);
        $this->SetFont('Arial','B',7);
        $this->Cell(195, 5, utf8_decode('FORMA:     2112'), 0, 0, 'L');
    }
}
//---------------------------------------------------

//---------------------------------------------------
//  Creación del objeto de la clase heredada.
$pdf = new PDF('L', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->SetMargins(10, 10, 50);
$pdf->SetAutoPageBreak(1, 10);
$pdf->AddPage();
//---------------------------------------------------
$FlagTotal = false;
$MontoTotal = 0;
##  
$sql = "SELECT
            rspf.CodClaSectorial,
            rspf.cod_partida,
            pv.denominacion,
            SUM(CASE WHEN 
                        rspf.cod_partida LIKE '407.01.%'
                        OR rspf.cod_partida LIKE '407.02.%'
                        OR rspf.cod_partida LIKE '407.12.01.%'
                     THEN rspf.Monto
                     ELSE 0 END) AS Corriente,
            SUM(CASE WHEN 
                        rspf.cod_partida LIKE '407.03.%'
                        OR rspf.cod_partida LIKE '407.04.%'
                        OR rspf.cod_partida LIKE '407.12.02.%'
                     THEN rspf.Monto
                     ELSE 0 END) AS Capital,
            SUM(rspf.Monto) AS Monto,
            SUBSTRING(rspf.cod_partida, 1, 3) AS Par,
            SUBSTRING(rspf.cod_partida, 5, 2) AS Gen,
            SUBSTRING(rspf.cod_partida, 8, 2) AS Esp,
            SUBSTRING(rspf.cod_partida, 11, 2) AS Sub
        FROM
            vw_consolidado_sector_partidas rspf
            INNER JOIN pv_partida pv ON (pv.cod_partida = rspf.cod_partida)
        WHERE
            (rspf.cod_partida LIKE '407.01.%'
             OR rspf.cod_partida LIKE '407.01.%'
             OR rspf.cod_partida LIKE '407.12.01.%'
             OR rspf.cod_partida LIKE '407.03.%'
             OR rspf.cod_partida LIKE '407.04.%'
             OR rspf.cod_partida LIKE '407.12.02.%')
            $filtro
        GROUP BY CodOrganismo, Ejercicio, CodSector, IdSubSector, cod_partida
        ORDER BY CodOrganismo, Ejercicio, CodSector, IdSubSector, cod_partida";
$field_detalle = getRecords($sql);
foreach ($field_detalle as $f) {
    ##  
    $pdf->SetDrawColor(255,255,255);
    $pdf->SetFillColor(255,255,255);
    $pdf->SetFont('Arial','',7);
	$pdf->SetAligns(array('C','C','C','C','C','L','R','R','R'));
    $pdf->Row(array(utf8_decode($f['CodClaSectorial']),
                    utf8_decode($f['Par']),
                    utf8_decode($f['Gen']),
                    utf8_decode($f['Esp']),
                    utf8_decode($f['Sub']),
                    utf8_decode($f['denominacion']),
                    number_format($f['Corriente'],2,',','.'),
                    number_format($f['Capital'],2,',','.'),
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