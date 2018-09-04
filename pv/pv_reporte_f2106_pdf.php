<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$filtro = '';
if (trim($fCodOrganismo)) $filtro .= " AND rspf.CodOrganismo = '$fCodOrganismo'";
if (trim($fEjercicio)) $filtro .= " AND rspf.Ejercicio = '$fEjercicio'";
if (trim($fPar)) $filtro .= " AND SUBSTRING(rspf.cod_partida, 1, 3) = '$fPar'";
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
$w = 256;
$h = 275;
$wdS = 0;
$filtro_sectores = '';
$sql = "SELECT CodSector, CodClaSectorial, IdSubSector, CodSubSector
        FROM vw_f21_resumen_partida_sector rspf
        WHERE 1 $filtro
        GROUP BY IdSubSector";
$field_sectores = getRecords($sql);
$sectores = intval(count($field_sectores));
if ($sectores <= 5) $wd = 100 + ((5 - $sectores) * 20);
else {
    $wd = 100;
    $w = $w + (($sectores - 5) * 20);
}
$SetWidths = [16,$wd];
$SetAlignsT = ['C','C'];
$SetAlignsD = ['C','L'];
$RowT = ['PARTIDA','DENOMINACION'];
foreach($field_sectores AS $fs) {
    $wdS += 20;
    $SetWidths[] = 20;
    $SetAlignsT[] = 'C';
    $SetAlignsD[] = 'R';
    $RowT[] = $fs['CodClaSectorial'];
    $filtro_sectores .= ", (SELECT SUM(Monto) FROM vw_f21_resumen_partida_sector rspf WHERE cod_partida LIKE CONCAT(pv.cod_tipocuenta, pv.partida1, '.%') AND rspf.CodClaSectorial = '$fs[CodClaSectorial]' $filtro GROUP BY CodClaSectorial) AS Sector".$fs['CodClaSectorial'];
}
$SetWidths[] = 20;
$SetAlignsT[] = 'C';
$SetAlignsD[] = 'R';
$RowT[] = 'TOTAL';
//---------------------------------------------------

class PDF extends FPDF {
    //  Cabecera de página.
    function Header() {
        global $_PARAMETRO;
        global $Ahora;
        global $field;
        global $field_sectores;
        global $sectores;
        global $wd;
        global $wdS;
        global $SetWidths;
        global $SetAlignsT;
        global $RowT;
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
        $this->Cell(36+$wd+$wdS, 5, utf8_decode('RESUMEN DE LOS CRÉDITOS PRESUPUESTARIOS POR PARTIDAS A NIVEL DE SECTORES'), 0, 1, 'C');
        $this->SetDrawColor(0, 0, 0);
        $this->Rect(10, 18, 36+$wd+$wdS, 30, "D");
        ##  
        $this->SetDrawColor(0,0,0);
        $this->SetFillColor(255,255,255);
        $this->SetFont('Arial','B',7);

        $x = $wd + 16 + 10;
        $w = count($field_sectores) * 20;
        $this->SetXY($x, 50);
        $this->Cell($w, 4, utf8_decode('SECTORES'), 1, 0, 'C');

        $this->SetDrawColor(255,255,255);
        $this->SetY(55);
        $this->SetWidths($SetWidths);
        $this->SetAligns($SetAlignsT);
        $this->Row($RowT);
        $this->Ln(1);
    }
    
    //  Pie de página.
    function Footer() {
        global $MontoTotal;
        global $FlagTotal;
        global $field_sectores;
        global $Sector;
        global $wd;
        ##  
        $this->SetDrawColor(0,0,0);
        $this->SetFillColor(0,0,0);
        $this->Rect(26, 50, 0.1, 150, "FD");
        $x = 26+$wd;
        $x1 = $x;
        $w = 0;
        $this->Rect($x, 54, 0.1, 146, "FD");
        foreach ($field_sectores as $fs) {
            $x += 20;
            $w += 20;
            $this->Rect($x, 54, 0.1, 146, "FD");
        }
        $this->Rect(10, 50, $w+$wd+36, 150, "D");
        $this->Rect(10, 60, $w+$wd+36, 0.1, "D");
        ##  
        if ($FlagTotal) {
            $this->SetY(200);
            $this->SetDrawColor(0,0,0);
            $this->SetFont('Arial','B',7);
            $this->Cell(16+$wd, 5, utf8_decode('TOTAL'), 1, 0, 'R');
            foreach ($field_sectores as $f) {
                $idx = 'Sector'.$f['CodClaSectorial'];
                $this->Cell(20, 5, number_format($Sector[$idx],2,',','.'), 1, 0, 'R');
            }
            $this->Cell(20, 5, number_format($MontoTotal,2,',','.'), 1, 0, 'R');
        }
        ##  
        $this->SetY(205);
        $this->SetFont('Arial','B',7);
        $this->Cell(195, 5, utf8_decode('FORMA:     2106'), 0, 0, 'L');
    }
}
//---------------------------------------------------

//---------------------------------------------------
//  Creación del objeto de la clase heredada.
$pdf = new PDF('P', 'mm', array($w,220));
$pdf->AliasNbPages();
$pdf->SetMargins(10, 10, 50);
$pdf->SetAutoPageBreak(1, 25);
$pdf->AddPage();
//---------------------------------------------------
$FlagTotal = false;
$MontoTotal = 0;
$Sector = array();
##  
$sql = "SELECT
            SUBSTRING(pv.cod_partida, 1, 3) AS Par,
            pv.denominacion
            $filtro_sectores
         FROM pv_partida pv
         WHERE
            pv.partida1 <> '00' AND
            pv.generica <> '00' AND
            pv.especifica = '00' AND
            pv.subespecifica = '00' AND
            SUBSTRING(pv.cod_partida, 1, 3) IN (SELECT SUBSTRING(cod_partida, 1, 3) AS partida FROM vw_f21_resumen_partida_sector rspf WHERE 1 $filtro GROUP BY partida)
         GROUP BY Par
        ORDER BY Par";
$field_detalle = getRecords($sql);
foreach ($field_detalle as $f) {
    $Total = 0;
    $pdf->SetDrawColor(255,255,255);
    $pdf->SetFillColor(255,255,255);
    $pdf->SetFont('Arial','',7);
    $RowD = [utf8_decode($f['Par']),utf8_decode($f['denominacion'])];
    foreach($field_sectores AS $fs) {
        $idx = 'Sector'.$fs['CodClaSectorial'];
        $RowD[] = number_format($f[$idx],2,',','.');
        $Total += $f[$idx];
        $Sector[$idx] += $f[$idx];
    }
    $RowD[] = number_format($Total,2,',','.');

    $pdf->SetAligns($SetAlignsD);
    $pdf->Row($RowD);
    $MontoTotal += $Total;
}
$FlagTotal = true;
//---------------------------------------------------

//---------------------------------------------------
//  Muestro el contenido del pdf.
$pdf->Output();
?>