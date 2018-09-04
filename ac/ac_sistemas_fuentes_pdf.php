<?php
/// -------------------------------------------------####
include("../lib/fphp.php");
define('FPDF_FONTPATH','font/');
require('fpdf.php');
connect(); 
extract ($_POST);
extract ($_GET);
global $Periodo;
/// -------------------------------------------------####

class PDF extends FPDF
{
//Page header
function Header(){
    
	global $Periodo;
	$this->Image('../imagenes/logos/logo.jpg', 10, 10, 10, 10);	
	$this->SetFont('Arial', 'B', 8);
	$this->SetXY(20, 10); $this->Cell(146, 5,utf8_decode( $_SESSION["NOMBRE_ORGANISMO_ACTUAL"]), 0, 0, 'L');
	                      $this->Cell(10,5,'Fecha:',0,0,'');$this->Cell(10,5,date('d/m/Y'),0,1,'');
	$this->SetXY(20, 15); $this->Cell(145, 5, utf8_decode('Dirección de Administración'), 0, 0, 'L');
	                       $this->Cell(10,5,utf8_decode('Página:'),0,1,'');
	/*$this->SetXY(19, 20); $this->Cell(150, 5, '', 0, 0, 'L');
	                       $this->Cell(7,5,utf8_decode('Año:'),0,0,'L');$this->Cell(6,5,date('Y'),0,1,'L');*/
						   
	list($fano, $fmes) = SPLIT('[-]', $Periodo);
    switch ($fmes) {
		case "01": $mes = ENERO; break;  
		case "02": $mes = FEBRERO;break; 
		case "03": $mes = MARZO;break;   
		case "04": $mes = ABRIL;break;   
		case "05": $mes = MAYO;break;    
		case "06": $mes = JUNIO;break;
		case "07": $mes = JULIO; break;
		case "08": $mes = AGOSTO; break;
		case "09": $mes = SEPTIEMBRE; break;
		case "10": $mes = OCTUBRE; break;
		case "11": $mes = NOVIEMBRE; break;
		case "12": $mes = DICIEMBRE; break;
    }
						   
		$this->SetFont('Arial', 'B', 8);
		$this->Cell(50, 10, '', 0, 0, 'C');
		$this->Cell(100, 10, utf8_decode('LISTADO SISTEMAS FUENTE'), 0, 1, 'C');

		$this->SetFont('Arial', 'B', 8);
		$this->Cell(10, 4,'Nro.', 1, 0, 'C');
		$this->Cell(40, 4,utf8_decode('CÓDIGO'), 1, 0, 'C');
		$this->Cell(100, 4,utf8_decode('DESCRIPCIÓN'), 1, 0, 'C');
		$this->Cell(40, 4,'ESTADO', 1, 1, 'C'); 
		$this->Ln(1);
}
//Page footer
function Footer(){
    //Position at 1.5 cm from bottom
    $this->SetXY(154,13);
    //Arial italic 8
    $this->SetFont('Arial','I',8);
    //Page number
    $this->Cell(0,10,' '.$this->PageNo().'/{nb}',0,0,'C');
}
}

//Instanciation of inherited class
$pdf=new PDF('P','mm','Letter');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',12);

// ------------------------------------------ ####
$sa= "select * from ac_sistemafuente";
$qa= mysql_query($sa) or die ($sa.mysql_error());
$ra= mysql_num_rows($qa);

if($ra!=""){
  
  for($i=0; $i<$ra; $i++){
      $fa= mysql_fetch_array($qa);
      if($fa['Estado']=="A") $estado="Activo";
      elseif($fa['Estado']=="I") $estado="Inactivo";

      $pdf->SetDrawColor(0, 0, 0); 
      $pdf->SetFillColor(255, 255, 255); 
      $pdf->SetTextColor(0, 0, 0);
	  $pdf->SetFont('Arial', '', 8);
	  $pdf->SetWidths(array(10, 40, 100, 40));
	  $pdf->SetAligns(array('C','C','L','C'));
	  $pdf->Row(array($i+1, $fa['CodSistemaFuente'], utf8_decode($fa['Descripcion']), $estado));
  }

}
// ------------------------------------------ ####

/*$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(202, 202, 202); $pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont('Arial', 'B', 8);
	$pdf->Cell(100,10,'',0,1,'L');
	$pdf->Cell(100,10,'ELABORADO POR:',0,0,'L');$pdf->Cell(120,10,'REVISADO POR:',0,0,'L');$pdf->Cell(100,10,'CONFORMADO POR:',0,1,'L');
	$pdf->Cell(100,5,'',0,0,'L');$pdf->Cell(120,5,'',0,0,'L');$pdf->Cell(100,5,'',0,1,'L');
	$pdf->Cell(100,5,'T.S.U. MARIANA SALAZAR',0,0,'L');$pdf->Cell(120,5,'LCDA. YOSMAR GREHAM',0,0,'L');$pdf->Cell(100,5,'LCDA. ROSIS REQUENA',0,1,'L');
	$pdf->Cell(100,2,'ASISTENTE DE PRESUPUESTI I',0,0,'L');$pdf->Cell(120,2,'JEFE(A) DIV. ADMINISTRACION Y PRESUPUESTO',0,0,'L');$pdf->Cell(100,2,'DIRECTORA GENERAL',0,1,'L');*/
$pdf->Output();
?>  