<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$filtro = '';
if (trim($fCodOrganismo)) $filtro .= " AND pyr.CodOrganismo = '$fCodOrganismo'";
if (trim($fEjercicio)) $filtro .= " AND pyr.Ejercicio = '$fEjercicio'";
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
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(523, 5, utf8_decode('RELACIÓN DE LOS RECURSOS HUMANOS CLASIFICADOS POR TIPO DE CARGO Y GÉNERO'), 0, 1, 'C');
        $this->SetDrawColor(0, 0, 0);
        $this->Rect(10, 18, 530, 30, "D");
        ##  
        $this->SetFont('Arial','B',9);

        //$this->SetXY(168,54); $this->Cell(100, 5, utf8_decode('ASIGNACIÓN PRESUPUESTARIA'), 1, 0, 'C');
        $this->SetXY(80,54);
        $this->Cell(151, 5, utf8_decode('AÑO REAL'), 1, 0, 'C');
        $this->Cell(151, 5, utf8_decode('AÑO ÚLTIMO ESTIMADO'), 1, 0, 'C');
        $this->Cell(158, 5, utf8_decode('AÑO PRESUPUESTADO'), 1, 0, 'C');

        $this->SetFont('Arial','B',9);
        $this->SetXY(80,59);
        $this->Cell(48, 5, utf8_decode('N° de Cargos'), 1, 0, 'C');
        $this->Cell(103, 5, utf8_decode('Monto Anual'), 1, 0, 'C');
        $this->Cell(48, 5, utf8_decode('N° de Cargos'), 1, 0, 'C');
        $this->Cell(103, 5, utf8_decode('Monto Anual'), 1, 0, 'C');
        $this->Cell(48, 5, utf8_decode('N° de Cargos'), 1, 0, 'C');
        $this->Cell(110, 5, utf8_decode('Monto Anual'), 1, 0, 'C');

        $this->SetFont('Arial','B',9);
        $this->SetY(65);
        $this->SetDrawColor(255,255,255);
        $this->SetFillColor(255,255,255);
        $this->SetWidths(array(70,10,10,10,18,23,26,18,18,18,10,10,10,18,23,26,18,18,18,10,10,10,18,25,20,22,18,25));
        $this->SetAligns(array('C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C'));
        $this->Row(array(utf8_decode('TIPO DE CARGO'),

                         utf8_decode('F'),
                         utf8_decode('M'),
                         utf8_decode('V'),
                         utf8_decode('TOTAL'),
                         utf8_decode('SUELDOS Y SALARIOS'),
                         utf8_decode('COMPENSACIONES'),
                         utf8_decode('PRIMAS'),
                         utf8_decode('DIETAS'),
                         utf8_decode('TOTAL'),

                         utf8_decode('F'),
                         utf8_decode('M'),
                         utf8_decode('V'),
                         utf8_decode('TOTAL'),
                         utf8_decode('SUELDOS Y SALARIOS'),
                         utf8_decode('COMPENSACIONES'),
                         utf8_decode('PRIMAS'),
                         utf8_decode('DIETAS'),
                         utf8_decode('TOTAL'),

                         utf8_decode('F'),
                         utf8_decode('M'),
                         utf8_decode('V'),
                         utf8_decode('TOTAL'),
                         utf8_decode('SUELDOS Y SALARIOS'),
                         utf8_decode('COMPENSACIONES'),
                         utf8_decode('PRIMAS'),
                         utf8_decode('DIETAS'),
                         utf8_decode('TOTAL')
                ));
        $this->SetAligns(array('L','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C'));
        $this->Ln(1);
    }
    
    //  Pie de página.
    function Footer() {
        global $NroCargoTotal1;
        global $SueldoTotal1;
        global $NroCargoTotal2;
        global $SueldoTotal2;
        global $NroCargoTotal;
        global $SueldoTotal;
        global $Sueldos;
        global $Compensaciones;
        global $Primas;
        global $FlagTotal;
        ##  
        $this->SetDrawColor(0,0,0);
        $this->SetFillColor(0,0,0);
        $this->Rect(10, 54, 530, 146, "D");
        $this->Rect(80, 64, 0.1, 136, "FD");
        $this->Rect(90, 64, 0.1, 136, "FD");
        $this->Rect(100, 64, 0.1, 136, "FD");
        $this->Rect(110, 64, 0.1, 136, "FD");
        $this->Rect(128, 64, 0.1, 136, "FD");
        $this->Rect(151, 64, 0.1, 136, "FD");
        $this->Rect(177, 64, 0.1, 136, "FD");
        $this->Rect(195, 64, 0.1, 136, "FD");
        $this->Rect(213, 64, 0.1, 136, "FD");
        $this->Rect(231, 64, 0.1, 136, "FD");
        $this->Rect(241, 64, 0.1, 136, "FD");
        $this->Rect(251, 64, 0.1, 136, "FD");
        $this->Rect(261, 64, 0.1, 136, "FD");
        $this->Rect(279, 64, 0.1, 136, "FD");
        $this->Rect(302, 64, 0.1, 136, "FD");
        $this->Rect(328, 64, 0.1, 136, "FD");
        $this->Rect(346, 64, 0.1, 136, "FD");
        $this->Rect(364, 64, 0.1, 136, "FD");
        $this->Rect(382, 64, 0.1, 136, "FD");
        $this->Rect(392, 64, 0.1, 136, "FD");
        $this->Rect(402, 64, 0.1, 136, "FD");
        $this->Rect(412, 64, 0.1, 136, "FD");
        $this->Rect(430, 64, 0.1, 136, "FD");
        $this->Rect(455, 64, 0.1, 136, "FD");
        $this->Rect(475, 64, 0.1, 136, "FD");
        $this->Rect(497, 64, 0.1, 136, "FD");
        $this->Rect(515, 64, 0.1, 136, "FD");
        $this->Rect(10, 74, 530, 0.1, "D");
        ##  70,10,10,10,18,23,26,18,18,18,10,10,10,18,23,26,18,18,18,10,10,10,18,25,20,22,18,25
        if ($FlagTotal) {
            $this->SetY(200);
            $this->SetDrawColor(0,0,0);
            $this->SetFont('Arial','B',9);
            $this->Cell(70, 5, utf8_decode('TOTALES'), 1, 0, 'R');
            
            $this->Cell(10, 5, number_format($NroCargo1,2,',','.'), 1, 0, 'R');
            $this->Cell(10, 5, number_format($NroCargo1,2,',','.'), 1, 0, 'R');
            $this->Cell(10, 5, number_format($NroCargo1,2,',','.'), 1, 0, 'R');
            $this->Cell(18, 5, intval($NroCargoTotal1), 1, 0, 'C');
            $this->Cell(23, 5, number_format($NroCargo1,2,',','.'), 1, 0, 'R');
            $this->Cell(26, 5, number_format($NroCargo1,2,',','.'), 1, 0, 'R');
            $this->Cell(18, 5, number_format($NroCargo1,2,',','.'), 1, 0, 'R');
            $this->Cell(18, 5, '', 1, 0, 'R');
            $this->Cell(18, 5, number_format($SueldoTotal1,2,',','.'), 1, 0, 'R');
            
            $this->Cell(10, 5, number_format($NroCargo2,2,',','.'), 1, 0, 'R');
            $this->Cell(10, 5, number_format($NroCargo2,2,',','.'), 1, 0, 'R');
            $this->Cell(10, 5, number_format($NroCargo2,2,',','.'), 1, 0, 'R');
            $this->Cell(18, 5, intval($NroCargoTotal2), 1, 0, 'C');
            $this->Cell(23, 5, number_format($NroCargo2,2,',','.'), 1, 0, 'R');
            $this->Cell(26, 5, number_format($NroCargo2,2,',','.'), 1, 0, 'R');
            $this->Cell(18, 5, number_format($NroCargo2,2,',','.'), 1, 0, 'R');
            $this->Cell(18, 5, '', 1, 0, 'R');
            $this->Cell(18, 5, number_format($SueldoTotal2,2,',','.'), 1, 0, 'R');
            
            $this->Cell(10, 5, number_format($NroCargo,2,',','.'), 1, 0, 'R');
            $this->Cell(10, 5, number_format($NroCargo,2,',','.'), 1, 0, 'R');
            $this->Cell(10, 5, number_format($NroCargo,2,',','.'), 1, 0, 'R');
            $this->Cell(18, 5, intval($NroCargoTotal), 1, 0, 'C');
            $this->Cell(25, 5, number_format($Sueldos,2,',','.'), 1, 0, 'R');
            $this->Cell(20, 5, number_format($Compensaciones,2,',','.'), 1, 0, 'R');
            $this->Cell(22, 5, number_format($Primas,2,',','.'), 1, 0, 'R');
            $this->Cell(18, 5, '', 1, 0, 'R');
            $this->Cell(25, 5, number_format($SueldoTotal,2,',','.'), 1, 0, 'R');
        }
        ##  
        $this->SetY(205);
        $this->SetFont('Arial','B',7);
        $this->Cell(195, 5, utf8_decode('FORMA:     2107'), 0, 0, 'L');
    }
}
//---------------------------------------------------

//---------------------------------------------------
//  Creación del objeto de la clase heredada.
$pdf = new PDF('L', 'mm', array(218, 547));
$pdf->AliasNbPages();
$pdf->SetMargins(10, 10, 50);
$pdf->SetAutoPageBreak(1, 25);
$pdf->AddPage();
//---------------------------------------------------
$FlagTotal = false;
$NroCargoTotal1 = 0;
$SueldoTotal1 = 0;
$NroCargoTotal2 = 0;
$SueldoTotal2 = 0;
$NroCargoTotal = 0;
$SueldoTotal = 0;
$Sueldos = 0;
$Compensaciones = 0;
$Primas = 0;
##  PERSONAL FIJO A TIEMPO COMPLETO
$pdf->SetFont('Arial','BI',9);
$pdf->Cell(20, 5, utf8_decode('PERSONAL FIJO A TIEMPO COMPLETO'), 0, 1, 'L');
$sql = "SELECT
            pyr.CodOrganismo,
            pyr.Ejercicio,
            tpn.CodPerfil,
            tpn.Perfil,
            SUM(CASE WHEN pyr.Sexo = 'F' THEN 1 ELSE 0 END) AS NroCargoF,
            SUM(CASE WHEN pyr.Sexo = 'M' THEN 1 ELSE 0 END) AS NroCargoM,
            SUM(CASE WHEN pyr.Tipo = 'VA' THEN 1 ELSE 0 END) AS NroCargoV,
            SUM(pyr.Sueldo) AS Sueldos,
            SUM(pyr.Compensacion) AS Compensaciones,
            SUM(pyr.Primas) AS Primas
        FROM
            tipoperfilnom tpn
            LEFT JOIN vw_proyrecursos pyr ON (pyr.CodPerfil = tpn.CodPerfil $filtro)
        GROUP BY CodOrganismo, Ejercicio, CodPerfil
        ORDER BY CodPerfil";
$field_detalle = getRecords($sql);
foreach ($field_detalle as $f) {
    $NroCargo = intval($f['NroCargoF']) + intval($f['NroCargoM']) + intval($f['NroCargoV']);
    $Sueldo = floatval($f['Sueldos']) + floatval($f['Compensaciones']) + floatval($f['Primas']) + floatval($f['Dietas']);
	##	
    $pdf->SetDrawColor(255,255,255);
    $pdf->SetFillColor(255,255,255);
    $pdf->SetFont('Arial','',9);
	$pdf->SetWidths(array(70,10,10,10,18,23,26,18,18,18,10,10,10,18,23,26,18,18,18,10,10,10,18,25,20,22,18,25));
    $pdf->SetAligns(array('L','C','C','C','C','R','R','R','R','R','C','C','C','C','R','R','R','R','R','C','C','C','C','R','R','R','R','R'));
    $pdf->Row(array(utf8_decode($f['Perfil']),
                    '','','','','','','','','',
                    '','','','','','','','','',
                    intval($f['NroCargoF']),
                    intval($f['NroCargoM']),
                    intval($f['NroCargoV']),
                    intval($NroCargo),
                    number_format($f['Sueldos'],2,',','.'),
                    number_format($f['Compensaciones'],2,',','.'),
                    number_format($f['Primas'],2,',','.'),
                    '',
                    number_format($Sueldo,2,',','.')
                ));
    $NroCargoTotal += $NroCargo;
    $SueldoTotal += $Sueldo;
    $Sueldos += floatval($f['Sueldos']);
    $Compensaciones += floatval($f['Compensaciones']);
    $Primas += floatval($f['Primas']);
}
$pdf->Ln(4);
##  PERSONAL FIJO A TIEMPO PARCIAL
$pdf->SetFont('Arial','BI',9);
$pdf->Cell(20, 5, utf8_decode('PERSONAL FIJO A TIEMPO PARCIAL'), 0, 1, 'L');
foreach ($field_detalle as $f) {
    $Total = $f['IngresosPropios'] + $f['Municipal'] + $f['EstadalMunicipal'] + $f['FCI'] + $f['Otras'];
    ##  
    $pdf->SetDrawColor(255,255,255);
    $pdf->SetFillColor(255,255,255);
    $pdf->SetFont('Arial','',9);
    $pdf->SetWidths(array(70,10,10,10,18,23,26,18,18,18,10,10,10,18,23,26,18,18,18,10,10,10,18,25,20,22,18,25));
    $pdf->SetAligns(array('L','C','C','C','C','R','R','R','R','R','C','C','C','C','R','R','R','R','R','C','C','C','C','R','R','R','R','R'));
    $pdf->Row(array(utf8_decode($f['Perfil'])
                ));
    $MontoTotal += $Total;
}
$pdf->Ln(4);
##  PERSONAL CONTRATADO
$pdf->SetFont('Arial','BI',9);
$pdf->Cell(20, 5, utf8_decode('PERSONAL CONTRATADO'), 0, 1, 'L');
foreach ($field_detalle as $f) {
    $Total = $f['IngresosPropios'] + $f['Municipal'] + $f['EstadalMunicipal'] + $f['FCI'] + $f['Otras'];
    ##  
    $pdf->SetDrawColor(255,255,255);
    $pdf->SetFillColor(255,255,255);
    $pdf->SetFont('Arial','',9);
    $pdf->SetWidths(array(70,10,10,10,18,23,26,18,18,18,10,10,10,18,23,26,18,18,18,10,10,10,18,25,20,22,18,25));
    $pdf->SetAligns(array('L','C','C','C','C','R','R','R','R','R','C','C','C','C','R','R','R','R','R','C','C','C','C','R','R','R','R','R'));
    $pdf->Row(array(utf8_decode($f['Perfil'])
                ));
    $MontoTotal += $Total;
}
$pdf->Ln(4);
##  OTROS
$pdf->SetFont('Arial','BI',9);
$pdf->Cell(20, 5, utf8_decode('OTROS'), 0, 1, 'L');
##  
    $pdf->SetDrawColor(255,255,255);
    $pdf->SetFillColor(255,255,255);
    $pdf->SetFont('Arial','',9);
    $pdf->SetWidths(array(70,10,10,10,18,23,26,18,18,18,10,10,10,18,23,26,18,18,18,10,10,10,18,25,20,22,18,25));
    $pdf->SetAligns(array('L','C','C','C','C','R','R','R','R','R','C','C','C','C','R','R','R','R','R','C','C','C','C','R','R','R','R','R'));
    $pdf->Row(array(utf8_decode('ALTOS FUNCIONARIOS Y DE ELECCIÓN POPULAR (CONCEJAL)')));
    $pdf->Row(array(utf8_decode('MIEMBRO JUNTA PARROQUIAL COMUNAL')));
    $MontoTotal += $Total;
##  
$FlagTotal = true;
//---------------------------------------------------

//---------------------------------------------------
//  Muestro el contenido del pdf.
$pdf->Output();
?>