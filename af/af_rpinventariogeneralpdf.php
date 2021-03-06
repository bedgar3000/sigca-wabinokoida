<?php
// ------------------------------------------------ ####
include("../lib/fphp.php");
define('FPDF_FONTPATH','font/');
require('fpdf.php');
require('fphp.php');
connect(); 
mysql_query("SET NAMES 'utf8'");
extract ($_POST);
extract ($_GET);
// ------------------------------------------------ ####

$filtro=strtr($filtro, "*", "'");


if($fecha_desde!="" and $fecha_hasta!=""){
   list($dd, $md, $ad)= split('[-]',$fecha_desde);
   list($dh, $mh, $ah)= split('[-]',$fecha_hasta);

   $fecha_desde= $ad.'-'.$md.'-'.$dd;
   $fecha_hasta= $ah.'-'.$mh.'-'.$dh;
   $filtro.=" and a.FechaIngreso>='$fecha_desde' and a.FechaIngreso<='$fecha_hasta'";

}else{

   $fecha_desde= '0001-01-01';
   $fecha_hasta= date("Y-m-d");

   $filtro.=" and a.FechaIngreso>='$fecha_desde' and a.FechaIngreso<='$fecha_hasta'";
}


class PDF extends FPDF
{
//Page header
function Header(){
     
	global $Periodo, $filtro, $dep;
	$this->Image('../imagenes/logos/logo.jpg', 10, 10, 10, 10);	
	$this->SetFont('Arial', 'B', 9);
	$this->SetXY(20, 10); $this->Cell(70, 5,utf8_decode('República Bolivariana de Venezuela'), 0, 1, 'L');
	$this->SetXY(20, 14); $this->Cell(70, 5,utf8_decode($_SESSION["NOMBRE_ORGANISMO_ACTUAL"]), 0, 1, 'L'); 
	$this->Ln(4);
	
	$this->SetXY(180, 10);
							$this->Cell(11,5,'Fecha: ',0,0,'L'); 
							$this->Cell(10,5,date('d/m/Y'),0,1,'');
							//$this->Cell(10,5,'30/12/2016',0,1,'');
	$this->SetXY(179, 15);$this->Cell(11,5,utf8_decode('Página:'),0,1,'');
	//$this->SetXY(183, 20);$this->Cell(7,5,utf8_decode('Año:'),0,0,'');$this->Cell(6,5,date('Y'),0,1,'L');
						   
	list($fano, $fmes) = split('[-]',$Periodo);
    switch ($fmes) {
		case "01": $mes = Enero; break;  
		case "02": $mes = Febrero;break; 
		case "03": $mes = Marzo;break;   
		case "04": $mes = Abril;break;   
		case "05": $mes = Mayo;break;    
		case "06": $mes = Junio;break;
		case "07": $mes = Julio; break;
		case "08": $mes = Agosto; break;
		case "09": $mes = Septiembre; break;
		case "10": $mes = Octubre; break;
		case "11": $mes = Noviembre; break;
		case "12": $mes = Diciembre; break;
    }
	//echo $fmes;					   
	/*$this->SetFont('Arial', 'B', 10);
	$this->Cell(105, 10, '', 0, 0, 'C');
	$this->Cell(47, 10, utf8_decode('Ejecución Presupuestaria'), 0, 0, 'C');
    $this->Cell(13, 10, $mes, 0, 0, 'C'); $this->Cell(13, 10, $fano, 0, 1, 'C');*/
	///// PRUEBA ***********
	
	/*if($dep==1){
		$fcon01['Dependencia']="CONTRALORIA DEL ESTADO DELTA AMACURO";
		$fcon02['NomCompleto']="MAGALIS DEL VALLE BARRIOS MORENO";
		$fcon02['DescripCargo']="CONTRALOR(A) DEL ESTADO";
		
	}else{
		$this->SetFont('Arial', 'B', 8);
		
		$sql = "select a.* from af_activo a where CodOrganismo<>'' $filtro"; //echo $sql;
		$qry = mysql_query($sql) or die ($sql.mysql_error());
		$field = mysql_fetch_array($qry); 
		
		$scon01 = "select 
						 CodDependencia, Dependencia, CodPersona 
					from 
						 mastdependencias a 
				   where 
						 CodDependencia='".$field['CodDependencia']."'"; //echo $scon01;
		$qcon01 = mysql_query($scon01) or die ($scon01.mysql_error());
		$fcon01 = mysql_fetch_array($qcon01);
		
		$scon02 = "select 
						 a.*,
						 b.DescripCargo,
						 c.NomCompleto,
						 c.CodPersona 
					 from 
						 rh_empleadonivelacion a 
						 inner join rh_puestos b on (b.CodCargo=a.CodCargo) 
						 inner join mastpersonas c on (c.CodPersona=a.CodPersona)
					where 
						 a.Secuencia=(select max(Secuencia) from rh_empleadonivelacion where CodPersona='".$fcon01['CodPersona']."') and 
						 a.CodPersona = '".$fcon01['CodPersona']."'";
		 $qcon02 = mysql_query($scon02) or die ($scon02.mysql_error());
		 $fcon02 = mysql_fetch_array($qcon02);
		
		 $cod_personaDependencia=$fcon02['CodPersona']; //echo $cod_personaDependencia;
	}*/
	
	
	/*$this->SetFont('Arial', '', 8);
	$this->SetXY(10,22);$this->Cell(19, 3, 'Dependencia:', 0, 0, 'L');$this->Cell(3, 3, $fcon01['Dependencia'], 0,1, 'L');
	$this->SetXY(10,25);$this->Cell(19, 3, 'Responsable:', 0, 0, 'L');$this->Cell(3, 3, $fcon02['NomCompleto'], 0, 1, 'L');
	$this->SetXY(10,28);$this->Cell(19, 3, 'Cargo:', 0, 0, 'L');$this->Cell(3, 3, $fcon02['DescripCargo'], 0, 1, 'L');*/
	
	$this->SetFont('Arial', 'B', 9);
	$this->Cell(200, 3, 'INVENTARIO DE BIENES', 0, 1, 'C');$this->Ln(4);
	
	
	$this->SetDrawColor(0, 0, 0); $this->SetFillColor(200, 200, 200); $this->SetTextColor(0, 0, 0);
	$this->SetFont('Arial', 'B', 6);
	//$this->Cell(18, 3, 'ACTIVO', 1, 0, 'C', 1);
	$this->Cell(16, 3, 'COD. INTERNO', 1, 0, 'C', 1);
	$this->Cell(100, 3, 'DESCRIPCION', 1, 0, 'C', 1);
	$this->Cell(50, 3, 'CLASIFICACION', 1, 0, 'C', 1);
	$this->Cell(32, 3, 'SERIAL', 1, 1, 'C', 1);
	//$this->Cell(26, 3, 'UBICACION', 1, 1, 'C', 1);
	$this->SetFillColor(255, 255, 255);
	///// ******************	
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
$pdf->AddPage();
$pdf->SetFont('Times','',12);

//// ---- Consulta para obtener datos 
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
			      a.CodigoInterno"; //echo $sactivo;
$qactivo = mysql_query($sactivo) or die ($sactivo.mysql_error());
$ractivo = mysql_num_rows($qactivo);

if($ractivo!=0)
   for($i=0; $i<$ractivo; $i++){
      $factivo = mysql_fetch_array($qactivo);
	  $CodDependencia = $factivo['CodDependencia'];
	  $pdf->SetDrawColor(255, 255, 255);
	  $pdf->SetFillColor(255, 255, 255); 
	  $pdf->SetFont('Arial', 'B', 7);
	  $pdf->SetWidths(array(16,100,50,32,26));
	  $pdf->SetAligns(array('C','L','L','C','L'));
	  $pdf->Row(array($factivo['CodigoInterno'],utf8_decode($factivo['Descripcion']),utf8_decode($factivo['DescpClasficicacion20']),$factivo['NumeroSerie']));
   }
   
   
   
   list($nombreCompleto, $cargo, $nivel) = getFirmaxDependencia($_PARAMETRO['FIRMAINVENTARIODEP']);
   

    $pdf->Cell(20,10,'Total de Bienes: '.$ractivo,0,1,'L');
	$pdf->Ln(8);
    $pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(202, 202, 202); $pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont('Arial', 'B', 8);
	//$pdf->Cell(80,5,'_____________________________',0,0,'C');$pdf->Cell(100,5,'RECIBI CONFORME: _____________________________',0,1,'C');
	/*$pdf->Cell(80,5,'_____________________________',0,0,'C'); $pdf->Cell(87,5,'_____________________________',0,1,'R');
    $pdf->Cell(80,2,"".' '.utf8_decode("CESAR RODRIGUEZ"),0,0,'C');    $pdf->Cell(127,2,$nivel.' '.$nombreCompleto,0,1,'C');
    $pdf->Cell(80,3,"REGISTRADOR DE BIENES I",0,0,'C');
    $pdf->Cell(25,3,'',0,0,'C');                             $pdf->MultiCell(80,3,$cargo,0,'C');*/
	
	
	/// Cuando no se encuentra el registrador de bienes
	$pdf->Cell(125,5,'_____________________________',0,1,'R');
    $pdf->Cell(200,2,$nivel.' '.$nombreCompleto,0,1,'C');
    
    $pdf->Cell(25,3,'',0,0,'C');                             $pdf->MultiCell(150,3,$cargo,0,'C');
	
	
	//$pdf->Cell(80,5,'_____________________________',0,0,'C');$pdf->Cell(100,5,'RECIBI CONFORME: _____________________________',0,1,'C');
    //$pdf->Cell(80,2,$nivel.' '.$nombreCompleto,0,0,'C');    $pdf->Cell(127,2,$nivel02.' '.$nombreCompleto02,0,1,'C');
    //$pdf->Cell(80,3,$cargo,0,0,'C');
    //$pdf->Cell(25,3,'',0,0,'C');                             $pdf->MultiCell(80,3,$cargo02,0,'C');

$pdf->Output();
?>  