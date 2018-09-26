<?php
define('FPDF_FONTPATH','font/');
require('mc_table3.php');
require('fphp.php');
connect();
//---------------------------------------------------
$filtro=strtr($filtro, "*", "'");
$filtro=strtr($filtro, ";", "%");
if ($ordenar == "") $orderby = "ORDER BY me.CodEmpleado";
elseif($ordenar == "mp.Ndocumento") $orderby = "ORDER BY LENGTH(mp.Ndocumento), mp.Ndocumento";
else $orderby = "ORDER BY $ordenar";
//---------------------------------------------------

//---------------------------------------------------
//	Imprime la cabedera del documento
function Cabecera($pdf) {
	$pdf->AddPage();
	$pdf->Image('../imagenes/logos/contraloria.jpg', 10, 10, 10, 10);	
	$pdf->SetFont('Arial', 'B', 8);
	$pdf->SetXY(20, 10); $pdf->Cell(190, 5,utf8_decode( 'Alcaldía de Angostura'), 0, 1, 'L');
	$pdf->SetXY(20, 15); $pdf->Cell(190, 5, utf8_decode('Dirección de Recursos Humanos'), 0, 1, 'L');
	$pdf->SetFont('Arial', 'B', 10);
	$pdf->Cell(190, 10, 'Listado de Empleados', 0, 1, 'C');	
	$pdf->SetDrawColor(255, 255, 255); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont('Arial', 'B', 6);
	$pdf->SetWidths(array(10, 45, 15, 50, 70, 15));
	$pdf->SetAligns(array('C', 'L', 'R', 'L', 'L', 'C'));
	$pdf->Row(array(utf8_decode('Código'), 'Nombre Completo', 'Documento', 'Cargo', 'Dependencia', 'Estado'));
	$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(0, 0, 0); $pdf->SetTextColor(0, 0, 0);
	$y=$pdf->GetY();
	$pdf->Rect(5, $y, 200, 0.1, "DF");
	$pdf->Ln(2);
}
//---------------------------------------------------

//---------------------------------------------------
//	Creación del objeto de la clase heredada
$pdf=new PDF_MC_Table();
$pdf->Open();
$pdf->SetMargins(5, 5, 5);
Cabecera($pdf);
//	Cuerpo
$sql="SELECT me.CodEmpleado, me.Estado, mp.NomCompleto, mp.Ndocumento, rp.DescripCargo, md.Dependencia FROM mastempleado me INNER JOIN mastpersonas mp ON (me.CodPersona=mp.CodPersona) INNER JOIN rh_puestos rp ON (me.CodCargo=rp.CodCargo) INNER JOIN mastdependencias md ON (me.CodDependencia=md.CodDependencia) WHERE me.CodEmpleado<>'' $filtro $orderby";
$query=mysql_query($sql) or die ($sql.mysql_error());
while ($field=mysql_fetch_array($query)) {
	if ($field['Estado']=="A") $status="Activo"; else $status="Inactivo";
	$pdf->SetDrawColor(255, 255, 255); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont('Arial', '', 6);
	$pdf->SetWidths(array(10, 45, 15, 50, 70, 15));
	$pdf->SetAligns(array('C', 'L', 'R', 'L', 'L', 'C'));
	$pdf->Row(array($field['CodEmpleado'], utf8_decode($field['NomCompleto']), number_format($field['Ndocumento'], 0, '', '.'), utf8_decode($field['DescripCargo']), utf8_decode($field['Dependencia']), $status));
}
//---------------------------------------------------

$pdf->Output();
?>  
