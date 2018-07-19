<?php
// ------------------------------------------------####
include("../lib/fphp.php");
define('FPDF_FONTPATH','font/');
require('fpdf.php');
require('fphp.php');
connect(); 
mysql_query("SET NAMES 'utf8'");
extract ($_POST);
extract ($_GET);
// ------------------------------------------------####

$filtro=strtr($filtro, "*", "'");


if($fecha_desde!="" and $fecha_hasta!=""){
   list($dd, $md, $ad)= split('[-]',$fecha_desde);
   list($dh, $mh, $ah)= split('[-]',$fecha_hasta);

   $fecha_desde= $ad.'-'.$md.'-'.$dd;
   $fecha_hasta= $ah.'-'.$mh.'-'.$dh;
   $filtro.=" and a.FechaIngreso>='$fecha_desde' and a.FechaIngreso<='$fecha_hasta'";

}else{

   //$fecha_desde= date("Y").'-01-01';
   $fecha_desde= '0000-00-00';
   $fecha_hasta= date("Y-m-d");

   $filtro.=" and a.FechaIngreso>='$fecha_desde' and a.FechaIngreso<='$fecha_hasta'";
}


class PDF extends FPDF
{
//Page header
function Header(){
    
	global $Periodo, $filtro;
	$this->Image('../imagenes/logos/logo.jpg', 10, 10, 10, 10);	
	$this->SetFont('Arial', 'B', 9);
	$this->SetXY(20, 10); $this->Cell(70, 5,utf8_decode('República Bolivariana de Venezuela'), 0, 1, 'L');
	$this->SetXY(20, 14); $this->Cell(70, 5,utf8_decode($_SESSION["NOMBRE_ORGANISMO_ACTUAL"]), 0, 1, 'L'); 
	$this->Ln(4);
	
	$this->SetXY(180, 10);$this->Cell(10,5,'Fecha:',0,0,'');
		$this->Cell(10,5,date('d/m/Y'),0,1,'');
		//$this->Cell(10,5,"04/01/2016",0,1,'');
	$this->SetXY(179, 15);$this->Cell(10,5,utf8_decode('Página:'),0,1,'');
	//$this->SetXY(183, 20);$this->Cell(7,5,utf8_decode('Año:'),0,0,'');$this->Cell(6,5,date('Y'),0,1,'L');
						   
	list($fano, $fmes) = SPLIT('[-]',$Periodo);
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
	$this->SetFont('Arial', 'B', 8);
	
	$sql = "select a.* from af_activo a where CodOrganismo<>'' $filtro"; //echo $sql;
	$qry = mysql_query($sql) or die ($sql.mysql_error());
	$field = mysql_fetch_array($qry); 
	
	$scon01 = "select 
					 CodDependencia, Dependencia, CodPersona 
				from 
				     mastdependencias a 
			   where 
			   	     CodDependencia='".$field['CodDependencia']."'";
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
	
	
	
	/*$this->SetFont('Arial', '', 8);
	$this->SetXY(10,22);$this->Cell(19, 3, 'Dependencia:', 0, 0, 'L');$this->Cell(3, 3, $fcon01['Dependencia'], 0,1, 'L');
	$this->SetXY(10,25);$this->Cell(19, 3, 'Responsable:', 0, 0, 'L');$this->Cell(3, 3, $fcon02['NomCompleto'], 0, 1, 'L');
	$this->SetXY(10,28);$this->Cell(19, 3, 'Cargo:', 0, 0, 'L');$this->Cell(3, 3, $fcon02['DescripCargo'], 0, 1, 'L');*/
	
	$this->SetFont('Arial', 'B', 9);
	$this->Cell(200, 3, 'INVENTARIO  ACTIVOS COSTO', 0, 1, 'C');$this->Ln(4);
	
	
	$this->SetDrawColor(0, 0, 0); $this->SetFillColor(200, 200, 200); $this->SetTextColor(0, 0, 0);
	$this->SetFont('Arial', 'B', 8);
	$this->Cell(20, 3, 'ACTIVO', 1, 0, 'C', 1);
	$this->Cell(21, 3, 'COD. INTERNO', 1, 0, 'C', 1);
	$this->Cell(80, 3, 'DESCRIPCION', 1, 0, 'C', 1);
	$this->Cell(50, 3, 'CLASIFICACION', 1, 0, 'C', 1);
	$this->Cell(25, 3, 'MONTO COSTO', 1, 1, 'C', 1);
	$this->SetFillColor(255, 255, 255);
	///// ******************	
}
//Page footer
function Footer(){
    //Position at 1.5 cm from bottom
    $this->SetXY(184,13);
    //Arial italic 8
    $this->SetFont('Arial','I',8);
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
				  inner join af_clasificacionactivo20 b on (b.CodClasificacion=a.ClasificacionPublic20) 
				  inner join af_ubicaciones c on (c.CodUbicacion=a.Ubicacion)
			 where 
			      CodOrganismo<>'' $filtro"; //echo $sactivo;
$qactivo = mysql_query($sactivo) or die ($sactivo.mysql_error());
$ractivo = mysql_num_rows($qactivo);

if($ractivo!=0)
   for($i=0; $i<$ractivo; $i++){
      $factivo = mysql_fetch_array($qactivo);
	  $CodDependencia = $factivo['CodDependencia'];
	  $MONTO_TOTAL = $MONTO_TOTAL + $factivo['MontoLocal'];
	  $MONTO = number_format($factivo['MontoLocal'],2,',','.');
	  
	  $pdf->SetFillColor(255, 255, 255); 
	  $pdf->SetFont('Arial', 'B', 8);
	  $pdf->SetWidths(array(20,21,80,50,25));
	  $pdf->SetAligns(array('C','C','L','L','R'));
	  $pdf->Row(array($factivo['Activo'],$factivo['CodigoInterno'],utf8_decode($factivo['Descripcion']),utf8_decode($factivo['DescpClasficicacion20']),$MONTO));
   }
   
   $scon03 = "select 
   					 CodPersona
			    from 
				     mastdependencias
				where     
					CodDependencia=(select ValorParam from mastparametros where ParametroClave='FIRMAINVENTARIODEP') and 
					CodOrganismo='".$factivo['CodOrganismo']."' ";
   $qcon03 = mysql_query($scon03) or die ($scon03.mysql_error());
   $fcon03 = mysql_fetch_array($qcon03);
   
   
   
    $MONTO_TOTAL = number_format($MONTO_TOTAL,2,',','.');
    $pdf->Cell(20,10,'Total de Bienes: '.$ractivo,0,0,'L'); 
	$pdf->Cell(152,10,'Total Costo=',0,0,'R'); 
	$pdf->Cell(25,10,$MONTO_TOTAL,0,1,'R'); $pdf->Ln(3);
	
	
	
	list($nombreCompleto, $cargo, $nivel) = getFirmaxDependencia($_PARAMETRO['FIRMAINVENTARIODEP']);
	
	
	
/*$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(202, 202, 202); $pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont('Arial', 'B', 8);
	$pdf->Cell(80,5,'_____________________________',0,0,'C');$pdf->Cell(100,5,'RECIBI CONFORME: _____________________________',0,1,'C');
	$pdf->Cell(80,2,$nivel.' '.$nombreCompleto,0,0,'C');    $pdf->Cell(127,2,$nivel02.' '.$nombreCompleto02,0,1,'C');
	$pdf->Cell(80,3,$cargo,0,0,'C');
	$pdf->Cell(25,3,'',0,0,'C');                             $pdf->MultiCell(80,3,$cargo02,0,'C');*/
	
	/* Modif 29/03/2016
	$pdf->Cell(80,5,'_____________________________',0,0,'C');$pdf->Cell(87,5,'_____________________________',0,1,'R');
    $pdf->Cell(80,3,"".' '.utf8_decode("CESAR RODRIGUEZ"),0,0,'C');    $pdf->Cell(127,3,$nivel.' '.$nombreCompleto,0,1,'C');
    $pdf->Cell(80,3,"REGISTRADOR DE BIENES I",0,0,'C');
    $pdf->Cell(25,3,'',0,0,'C');                             $pdf->MultiCell(80,3,$cargo,0,'C');
	*/
	
	$pdf->Cell(80,5,'',0,0,'C');$pdf->Cell(87,5,'_____________________________',0,1,'R');
    $pdf->Cell(80,3,"".' ',0,0,'C');    $pdf->Cell(127,3,$nivel.' '.$nombreCompleto,0,1,'C');
    $pdf->Cell(80,3,"",0,0,'C');
    $pdf->Cell(25,3,'',0,0,'C');                             $pdf->MultiCell(80,3,$cargo,0,'C');
	
$pdf->Output();
?>  