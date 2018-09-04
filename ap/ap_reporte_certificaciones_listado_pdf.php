<?php
require('../lib/fpdf.php');
include("../lib/fphp.php");
include("lib/fphp.php");
//---------------------------------------------------
$filtro = '';
if ($lista != "listar-obras") {
    $filtro .= " AND c.CodTipoCertif <> '09'";
}
if ($fBuscar != "") {
    $filtro .= " AND (CONCAT(c.CodTipoCertif, '-', c.Anio, '-', c.CodInterno) LIKE '%".$fBuscar."%'
                      OR c.CodTipoCertif LIKE '%".$fBuscar."%'
                      OR c.Anio LIKE '%".$fBuscar."%'
                      OR c.CodInterno LIKE '%".$fBuscar."%'
                      OR c.Justificacion LIKE '%".$fBuscar."%'
                      OR p.NomCompleto LIKE '%".$fBuscar."%')";
} else $dBuscar = "disabled";
if ($fEstado != "") $filtro.=" AND (c.Estado = '".$fEstado."')";
if ($fCodOrganismo != "") $filtro.=" AND (c.CodOrganismo = '".$fCodOrganismo."')";
if ($fCodTipoCertif != "") $filtro.=" AND (c.CodTipoCertif = '".$fCodTipoCertif."')";
if ($fCodPersona != "") $filtro.=" AND (c.CodPersona = '".$fCodPersona."')";
if ($fFechaD != "" || $fFechaH != "") { 
    if ($fFechaD != "") $filtro.=" AND (c.Fecha >= '".formatFechaAMD($fFechaD)."')";
    if ($fFechaH != "") $filtro.=" AND (c.Fecha <= '".formatFechaAMD($fFechaH)."')";
}

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
        global $subtitulo;
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
        $this->SetX(25, 5); $this->Cell(175, 5, utf8_decode(mb_strtoupper($NomOrganismo)), 0, 1, 'L');
        $this->SetX(25, 5); $this->Cell(175, 5, utf8_decode(mb_strtoupper($NomDependencia)), 0, 1, 'L');
        $this->SetFont('Arial', '', 8);
        $this->SetXY(230, 10); $this->Cell(10, 5, utf8_decode('Fecha: '), 0, 0, 'L');
        $this->Cell(30, 5, formatFechaDMA($FechaActual), 0, 1, 'L');
        $this->SetXY(230, 15); $this->Cell(10, 5, utf8_decode('Página: '), 0, 0, 'L');
        $this->Cell(30, 5, $this->PageNo().' de {nb}', 0, 1, 'L');
        ##  -------------------
        $this->SetY(25); 
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(260, 5, utf8_decode('LISTA DE GASTOS DIRECTOS'), 0, 1, 'C');
        $this->Ln(5);
        ##  
        if ($fCodTipoCertif) {
            $TipoCertif = getVar3("SELECT Descripcion FROM ap_tiposcertificacion WHERE CodTipoCertif = '$fCodTipoCertif'");
            $this->SetFont('Arial','',8); $this->Cell(25, 5, utf8_decode('TIPO: '), 0, 0, 'L');
            $this->SetFont('Arial','B',8); $this->Cell(230, 5, mb_strtoupper(utf8_decode($fCodTipoCertif.' - '.$TipoCertif)), 0, 1, 'L');
        }
        if ($fFechaD && $fFechaH) {
            $this->SetFont('Arial','',8); $this->Cell(25, 5, utf8_decode('PERIODO: '), 0, 0, 'L');
            $this->SetFont('Arial','B',8); $this->Cell(230, 5, $fFechaD . ' AL ' . $fFechaH, 0, 1, 'L');
        }
        if ($fCodPersona) {
            $Persona = getVar3("SELECT NomCompleto FROM mastpersonas WHERE CodPersona = '$fCodPersona'");
            $this->SetFont('Arial','',8); $this->Cell(25, 5, utf8_decode('BENEFICIARIO: '), 0, 0, 'L');
            $this->SetFont('Arial','B',8); $this->Cell(230, 5, mb_strtoupper(utf8_decode($Persona)), 0, 1, 'L');
        }
        $this->Ln(2);
        ##  
        $this->SetFont('Arial','B',7);
        $this->SetDrawColor(0,0,0);
        $this->SetFillColor(220,220,220);
        $this->SetWidths(array(20,17,23,60,30,110));
        $this->SetAligns(array('C','C','C','L','R','L'));
        $this->Row(array(utf8_decode('CÓDIGO'),
                         utf8_decode('FECHA'),
                         utf8_decode('ESTADO'),
                         utf8_decode('BENEFICIARIO'),
                         utf8_decode('MONTO'),
                         utf8_decode('JUSTIFICACIÓN')
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
$Monto = 0;
##  
$sql = "SELECT
            c.*,
            CONCAT(c.CodTipoCertif, '-', c.Anio, '-', c.CodInterno) AS Codigo,
            p.NomCompleto
        FROM
            ap_certificaciones c
            INNER JOIN mastpersonas p ON (p.CodPersona = c.CodPersona)
        WHERE 1 $filtro
        ORDER BY Codigo";
$field = getRecords($sql);
foreach($field as $f) {
    $pdf->SetFont('Arial','',7);
    $pdf->SetDrawColor(255,255,255);
    $pdf->SetFillColor(255,255,255);
    $pdf->Row(array(utf8_decode($f['Codigo']),
                    formatFechaDMA($f['Fecha']),
                    utf8_decode(printValores('certificaciones-estado',$f['Estado'])),
                    utf8_decode($f['NomCompleto']),
                    number_format($f['Monto'],2,',','.'),
                    trim(utf8_decode($f['Justificacion']))
            ));
    ##  
    $pdf->SetDrawColor(0,0,0);
    $pdf->Line(10, $pdf->GetY()-1, 270, $pdf->GetY()-1);
    ##  
    $Monto += $f['Monto'];
}
$pdf->SetFont('Arial','B',7);
$pdf->SetDrawColor(220,220,220);
$pdf->SetFillColor(220,220,220);
$pdf->Row(array('','','','TOTAL:',number_format($Monto,2,',','.'),''));

//---------------------------------------------------

//---------------------------------------------------
//  Muestro el contenido del pdf.
$pdf->Output();
?>