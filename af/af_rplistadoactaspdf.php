<?php
// ------------------------------------------------ ####
include("../lib/fphp.php");
define('FPDF_FONTPATH','font/');
require('fpdf.php');
mysql_query("SET NAMES 'utf8'");
extract ($_POST);
extract ($_GET);
// ------------------------------------------------ ####
$filtro=strtr($filtro, "*", "'");

class PDF extends FPDF{
 //Page header
	function Header(){
	     
		global $tipoActa, $filtro, $fecha_desde, $fecha_hasta ;
		$this->Image('../imagenes/logos/logo.jpg', 10, 10, 12, 12);	
		$this->SetFont('Arial', 'B', 9);
		$this->SetXY(22, 10); $this->Cell(70,5,utf8_decode('República Bolivariana de Venezuela'),0,1,'L');
		$this->SetXY(22, 14); $this->Cell(70,5,utf8_decode($_SESSION["NOMBRE_ORGANISMO_ACTUAL"]),0,1,'L'); 
		$this->Ln(4);
		
		$this->SetXY(180, 10);
								$this->Cell(11,5,'Fecha: ',0,0,'L'); //$this->Cell(10,5,"05/03/2014",0,1,'');
								$this->Cell(10,5,date('d/m/Y'),0,1,'');
		$this->SetXY(179, 15);  $this->Cell(11,5,utf8_decode('Página:'),0,1,''); 
		                        $this->Ln(4);
		//$this->SetXY(183, 20);$this->Cell(7,5,utf8_decode('Año:'),0,0,'');$this->Cell(6,5,date('Y'),0,1,'L');
							   
		list($fano, $fmes) = split('[-]',$Periodo);
	    switch ($fmes) {
			case "01": $mes = Enero; break;  
			case "02": $mes = Febrero; break; 
			case "03": $mes = Marzo; break;   
			case "04": $mes = Abril; break;   
			case "05": $mes = Mayo; break;    
			case "06": $mes = Junio; break;
			case "07": $mes = Julio; break;
			case "08": $mes = Agosto; break;
			case "09": $mes = Septiembre; break;
			case "10": $mes = Octubre; break;
			case "11": $mes = Noviembre; break;
			case "12": $mes = Diciembre; break;
	    }

        if ($tipoActa="AI") {
        	# code...
        	$this->SetFont('Arial', 'B', 9);
		    $this->Cell(200, 3, 'LISTADO DE ACTAS DE INCORPORACION DESDE '.$fecha_desde." HASTA ".$fecha_hasta, 0, 1, 'C');$this->Ln(4);
        }
        
        $this->SetDrawColor(0, 0, 0); $this->SetFillColor(200, 200, 200); $this->SetTextColor(0, 0, 0);
		$this->SetFont('Arial', 'B', 6);
		$this->Cell(16, 4, 'NRO. ACTA', 1, 0, 'C', 1);
		$this->Cell(20, 4, 'FECHA ACTA', 1, 0, 'C', 1);
		$this->Cell(70, 4, 'CONFORMADO', 1, 0, 'C', 1);
		$this->Cell(70, 4, 'APROBADO', 1, 1, 'C', 1);
		//$this->Cell(26, 3, 'UBICACION', 1, 1, 'C', 1);
		$this->SetFillColor(255, 255, 255);

	}
	//Page footer
	function Footer(){
	    //Position at 1.5 cm from bottom
	    $this->SetXY(187,13);
	    //Arial italic 8
	    $this->SetFont('Arial','B',8);
	    //Page number
	    $this->Cell(0,10,' '.$this->PageNo().'/{nb}',0,0,'C');
	}
}

//Instanciation of inherited class
$pdf=new PDF('P','mm','letter');
$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(1,2);
$pdf->AddPage();
$pdf->SetFont('Times','',12);

if ($tipoActa=="AI") {
	# code...
	$tabla= "af_actaincorpactivo";
	$campo= "CodOrganismo<>''";
}
//// ---- Consulta para obtener datos 
$sql= "select distinct NroActa, NroActa, FechaActa, DescripCargoConform, DescripCargoAprob from $tabla where $campo $filtro"; 
$qry= mysql_query($sql) or die ($sql.mysql_error());
$row= mysql_num_rows($qry);

if ($row!=0) {
	# code...
	for ($i=0; $i<$row; $i++) { 
		# code...
		$field= mysql_fetch_array($qry);
        
        $pdf->SetDrawColor(255, 255, 255);
	    $pdf->SetFillColor(255, 255, 255); 
	    $pdf->SetFont('Arial', '', 8);
	    $pdf->SetWidths(array(16,20,70,70));
	    $pdf->SetAligns(array('C','C','L','L'));
	    $pdf->Row(array( $field['NroActa'], utf8_decode(formatFechaDMA($field['FechaActa'])), utf8_decode($field['DescripCargoConform']), 
	    	             utf8_decode($field['DescripCargoAprob'])));

	}
}

/*//// ---- Consulta para obtener datos 
$sactivo = "select 
				  a.*, 
				  b.Descripcion as DescpClasficicacion20,
				  c.Descripcion as DescpUbicacion
			  from
				  af_activo a 
				  inner join af_clasificacionactivo20 b on (b.CodClasificacion = a.ClasificacionPublic20) 
				  inner join af_ubicaciones c on (c.CodUbicacion = a.Ubicacion)
			 where 
			      a.CodOrganismo<>'' $filtro 
			order by 
			      CodigoInterno"; //echo $sactivo;
$qactivo = mysql_query($sactivo) or die ($sactivo.mysql_error());
$ractivo = mysql_num_rows($qactivo);



if($ractivo!=0)
   for($i=0; $i<$ractivo; $i++){
      $factivo = mysql_fetch_array($qactivo);
	  $CodDependencia = $factivo['CodDependencia'];
	  $pdf->SetDrawColor(255, 255, 255);
	  $pdf->SetFillColor(255, 255, 255); 
	  $pdf->SetFont('Arial', 'B', 8);
	  $pdf->SetWidths(array(16,100,50,32,26));
	  $pdf->SetAligns(array('C','L','L','C','L'));
	  $pdf->Row(array($factivo['CodigoInterno'],utf8_decode($factivo['Descripcion']),utf8_decode($factivo['DescpClasficicacion20']),$factivo['NumeroSerie']));
   }
   
   list($nombreCompleto, $cargo, $nivel) = getFirmaxDependencia($_PARAMETRO['FIRMAINVENTARIODEP']);
	 
   list($nombreCompleto02, $cargo02, $nivel02) = getFirmaxDependencia($CodDependencia);
	 
	 
   
   
    $pdf->Cell(20,10,'Total de Bienes: '.$ractivo,0,1,'L');
	$pdf->Ln(8);
	if($firmas!='1'){
	    $pdf->SetDrawColor(0, 0, 0); 
	    $pdf->SetFillColor(202, 202, 202); 
	    $pdf->SetTextColor(0, 0, 0);
		$pdf->SetFont('Arial', 'B', 8);
		$pdf->Cell(80,5,'_____________________________',0,0,'C'); $pdf->Cell(100,5,'RECIBI CONFORME: _____________________________',0,1,'C');
	    $pdf->Cell(80,3,$nivel.' '.$nombreCompleto,0,0,'C');      $pdf->Cell(127,3,$nivel02.' '.$nombreCompleto02,0,1,'C');
	    $pdf->Cell(80,3,$cargo,0,0,'C');
	    $pdf->Cell(25,3,'',0,0,'C');                              $pdf->MultiCell(80,3,$cargo02,0,'C');
    }else{

    	$pdf->SetDrawColor(0, 0, 0); 
	    $pdf->SetFillColor(202, 202, 202); 
	    $pdf->SetTextColor(0, 0, 0);
		$pdf->SetFont('Arial', 'B', 8);
		$pdf->Cell(80,5,'_____________________________',0,0,'C'); $pdf->Cell(100,5,'RECIBI CONFORME: _____________________________',0,1,'C');
	    $pdf->Cell(80,3,$nivel.' '.$nombreCompleto,0,0,'C');      $pdf->Cell(127,3,$nivel_cabecera.' '.$nomb_cabecera,0,1,'C');
	    $pdf->Cell(80,3,$cargo,0,0,'C');
	    $pdf->Cell(25,3,'',0,0,'C');                              $pdf->MultiCell(80,3,$cargo_cabecera,0,'C'); 
    }*/
$pdf->Output();
?>  