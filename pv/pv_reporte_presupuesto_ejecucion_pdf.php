<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$filtro = '';
if (trim($fCodOrganismo)) $filtro .= " AND CodOrganismo = '$fCodOrganismo'";
if (trim($fEjercicio)) $filtro .= " AND Ejercicio = '$fEjercicio'";
if (trim($fIdSubSector)) $filtro .= " AND IdSubSector = '$fIdSubSector'";
if (trim($fIdPrograma)) $filtro .= " AND IdPrograma = '$fIdPrograma'";
if (trim($fIdSubPrograma)) $filtro .= " AND IdSubPrograma = '$fIdSubPrograma'";
if (trim($fIdProyecto)) $filtro .= " AND IdProyecto = '$fIdProyecto'";
if (trim($fIdActividad)) $filtro .= " AND IdActividad = '$fIdActividad'";
if (trim($fCodUnidadEjec)) $filtro .= " AND CodUnidadEjec = '$fCodUnidadEjec'";
if (trim($fCodDependencia)) $filtro.=" AND (CodDependencia = '$fCodDependencia')";
if (trim($fPar)) $filtro .= " AND Par= '$fPar'";
if (trim($fGen)) $filtro .= " AND Gen= '$fGen'";
if (trim($fEsp)) $filtro .= " AND Esp= '$fEsp'";
if (trim($fSub)) $filtro .= " AND Sub = '$fSub'";
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
        $this->Cell(257, 5, strtoupper(utf8_decode('EJECUCIÓN PRESUPUESTARIA')), 0, 1, 'C');
        $this->Ln(5);
        ##  
        $this->SetFont('Arial', '', 8); $this->Cell(45, 6, utf8_decode('CATEGORÍA PROGRAMÁTICA:'), 0, 0, 'L');
        $this->SetFont('Arial', 'B', 8); $this->Cell(212, 6, strtoupper(utf8_decode($f['CatProg'].' '.$f['Actividad'])), 0, 1, 'L');
        $this->Ln(1);
        $this->SetFont('Arial', '', 8); $this->Cell(45, 6, utf8_decode('UNIDAD EJECUTORA:'), 0, 0, 'L');
        $this->SetFont('Arial', 'B', 8); $this->Cell(212, 6, strtoupper(utf8_decode($f['UnidadEjecutora'])), 0, 1, 'L');
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
##  
$sql = "SELECT *
        FROM view_04_ejecucion_presupuestaria
        WHERE 1 $filtro
        GROUP BY Ejercicio, CatProg, CodFuente, cod_partida
        ORDER BY Ejercicio, CatProg, CodFuente, cod_partida";
$field = getRecords($sql);
foreach ($field as $f)
{
    if ($Grupo != $f['CatProg'])
    {
        $Grupo = $f['CatProg'];   
        ##  
        $pdf->AddPage();
    }
    ##  
    $DisponibilidadPresupuestaria = $f['MontoAjustado'] - $f['MontoCompromiso'];
    ##  
    $pdf->SetDrawColor(255,255,255);
    if ($i % 2 == 0) $pdf->SetFillColor(255,255,255); else $pdf->SetFillColor(240,240,240);
    if (substr($f['Par'], 1, 2) == '00' && $f['Gen'] == '00' && $f['Esp'] == '00' && $f['Sub'] == '00') $pdf->SetFont('Arial','B',6);
    elseif ($f['Gen'] == '00' && $f['Esp'] == '00' && $f['Sub'] == '00') $pdf->SetFont('Arial','BU',6);
    elseif ($f['Esp'] == '00' && $f['Sub'] == '00') $pdf->SetFont('Arial','U',6);
    else $pdf->SetFont('Arial','',6);
    $pdf->Row(array(utf8_decode($f['Par']),
                    utf8_decode($f['Gen']),
                    utf8_decode($f['Esp']),
                    utf8_decode($f['Sub']),
                    utf8_decode($f['CodFuente']),
                    utf8_decode($f['NombrePartida']),
                    number_format($f['MontoAprobado'],2,',','.'),
                    number_format($f['MontoAjustado'],2,',','.'),
                    number_format($f['MontoCompromiso'],2,',','.'),
                    number_format($f['MontoCausado'],2,',','.'),
                    number_format($f['MontoPagado'],2,',','.'),
                    number_format($DisponibilidadPresupuestaria,2,',','.')
                ));
    $pdf->Ln(1);
    ++$i;
}
//---------------------------------------------------

//---------------------------------------------------
//  Muestro el contenido del pdf.
$pdf->Output();
?>