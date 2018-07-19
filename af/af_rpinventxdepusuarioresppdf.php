<?php
// ------------------------------------- ####
include("../lib/fphp.php");
define('FPDF_FONTPATH','font/');
require('fpdf.php');
require('fphp.php');
connect(); 
mysql_query("SET NAMES 'utf8'");
extract ($_POST);
extract ($_GET);
//global $Periodo;
//echo $_SESSION["MYSQL_BD"];
/// ----------------------------------------------------
//---------------------------------------------------
$filtro=strtr($filtro, "*", "'");
//global $filtro;
//$Periodo = $Periodo;
//$filtro=strtr($filtro, ";", "%");
//---------------------------------------------------
//---------------------------------------------------
//echo $Periodo;
class PDF extends FPDF
{
//Page header
function Header(){
    
	global $Periodo, $filtro, $recib_conf_cargo, $recib_conf_nombre; 
	$this->Image('../imagenes/logos/logo.jpg', 10, 10, 10, 10);	
	$this->SetFont('Arial', 'B', 9);
	$this->SetXY(20, 10); $this->Cell(70, 5,utf8_decode('República Bolivariana de Venezuela'), 0, 1, 'L');
	$this->SetXY(20, 14); $this->Cell(70, 5,utf8_decode($_SESSION["NOMBRE_ORGANISMO_ACTUAL"]), 0, 1, 'L'); 
	$this->Ln(4);
	
	$this->SetXY(180, 10);$this->Cell(11,5,'Fecha: ',0,0,'L');$this->Cell(10,5,date('d/m/Y'),0,1,'');
	//$this->SetXY(180, 10);$this->Cell(11,5,'Fecha: ',0,0,'L');$this->Cell(10,5,"30/07/2014",0,1,'');
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
	
	/*$scon02 = "select 
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
					 a.CodPersona = '".$fcon01['CodPersona']."'";*/
	 $scon02 = "select 
					 a.CodCargo,
					 b.DescripCargo,
					 c.NomCompleto,
					 c.CodPersona 
				 from 
				     mastdependencias a 
					 inner join rh_puestos b on (b.CodCargo=a.CodCargo) 
					 inner join mastpersonas c on (c.CodPersona=a.CodPersona)
				where 
				     a.CodDependencia='".$field['CodDependencia']."' and 
					 a.CodPersona = '".$fcon01['CodPersona']."'";	//echo $scon02;			 
	 $qcon02 = mysql_query($scon02) or die ($scon02.mysql_error());
	 $fcon02 = mysql_fetch_array($qcon02);
	
	 $cod_personaDependencia=$fcon02['CodPersona']; //echo $cod_personaDependencia;
	
	
	 $sa= "select CodCargo,TipoAccion 
	         from 
			     rh_empleadonivelacion	
		    where 
				 Secuencia=(select max(Secuencia) from rh_empleadonivelacion where CodPersona='".$fcon01['CodPersona']."') and 
			     CodPersona = '".$fcon01['CodPersona']."'";
	 $qa= mysql_query($sa) or die ($sa.mysql_error());
	 $ra= mysql_num_rows($qa);
	 if($ra!=0)$fa= mysql_fetch_array($qa);	
	
	
	if($fa['CodCargo']!=$fcon02['CodCargo']) $valor='(E)'; 
	elseif(($fa['CodCargo']==$fcon02['CodCargo'])and($fa['TipoAccion']=='ET'))$valor='(E)';
	else $valor="";
	
	
	$recib_conf_cargo= $fcon02['DescripCargo'].' '.$valor; 
	$recib_conf_nombre= $fcon02['NomCompleto'];
	
	list($nombreCompleto, $cargo, $nivel) = getFirmaxDependencia($fcon01['CodDependencia']);
	
	$this->SetFont('Arial', '', 8);
	$this->SetXY(10,22);$this->Cell(19, 3, 'Dependencia:', 0, 0, 'L');$this->Cell(3, 3, $fcon01['Dependencia'], 0,1, 'L');
	$this->SetXY(10,25);$this->Cell(19, 3, 'Responsable:', 0, 0, 'L');$this->Cell(3, 3, $fcon02['NomCompleto'], 0, 1, 'L');
	$this->SetXY(10,28);$this->Cell(19, 3, 'Cargo:', 0, 0, 'L');$this->Cell(3, 3, $cargo, 0, 1, 'L'); $this->Ln(2);
	
	/*$this->SetFont('Arial', '', 8);
	$this->SetXY(10,22);$this->Cell(19, 3, 'Dependencia:', 0, 0, 'L');$this->Cell(3, 3, $fcon01['Dependencia'], 0,1, 'L');
	$this->SetXY(10,25);$this->Cell(19, 3, 'Responsable:', 0, 0, 'L');$this->Cell(3, 3, $fcon02['NomCompleto'], 0, 1, 'L');
	$this->SetXY(10,28);$this->Cell(19, 3, 'Cargo:', 0, 0, 'L');$this->Cell(3, 3, 'DIRECTOR(A) DE RECURSOS HUMANOS (E)', 0, 1, 'L');*/
	
	$this->SetFont('Arial', 'B', 9);
	$this->Cell(200, 3, 'INVENTARIO DE BIENES', 0, 1, 'C');$this->Ln(4);
	
	
	$this->SetDrawColor(0, 0, 0); $this->SetFillColor(200, 200, 200); $this->SetTextColor(0, 0, 0);
	$this->SetFont('Arial', 'B', 6);
	//$this->Cell(18, 3, 'ACTIVO', 1, 0, 'C', 1);
	$this->Cell(16, 3, 'COD. INTERNO', 1, 0, 'C', 1);
	$this->Cell(80, 3, 'DESCRIPCION', 1, 0, 'C', 1);
	$this->Cell(55, 3, 'CENTRO COSTO', 1, 0, 'C', 1);
	$this->Cell(55, 3, 'EMPLEADO RESPONSABLE', 1, 0, 'C', 1);
	$this->Cell(55, 3, 'EMPLEADO USUARIO', 1, 1, 'C', 1);
	//$this->Cell(26, 3, 'UBICACION', 1, 1, 'C', 1);
	$this->SetFillColor(255, 255, 255);
	///// ******************	
}
//Page footer
function Footer(){
    //Position at 1.5 cm from bottom
    $this->SetXY(121,13);
    //Arial italic 8
    $this->SetFont('Arial','B',8);
    //Page number
    $this->Cell(0,10,' '.$this->PageNo().'/{nb}',0,0,'C');
}
}
//Instanciation of inherited class
//$pdf=new PDF('P','mm','letter');
$pdf=new PDF('L','mm','letter');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',12);

//// ---- Consulta para obtener datos 
/*$sactivo = "select 
				  a.*, 
				  b.Descripcion as DescpClasficicacion20,
				  c.Descripcion as DescpUbicacion
			  from
				  af_activo a 
				  inner join af_clasificacionactivo20 b on (b.CodClasificacion=a.ClasificacionPublic20) 
				  inner join af_ubicaciones c on (c.CodUbicacion=a.Ubicacion)
			 where 
			      CodOrganismo<>'' $filtro 
			order by 
			      a.CodigoInterno"; //echo $sactivo;
$qactivo = mysql_query($sactivo) or die ($sactivo.mysql_error());
$ractivo = mysql_num_rows($qactivo);*/

$sactivo = "select 
				  a.*, 
				  d.Descripcion as DescpCosto
			  from
				  af_activo a
				  inner join ac_mastcentrocosto d on (d.CodCentroCosto=a.CentroCosto)
			 where 
			      CodOrganismo<>'' $filtro 
			order by 
			      a.CodigoInterno"; //echo $sactivo;
$qactivo = mysql_query($sactivo) or die ($sactivo.mysql_error());
$ractivo = mysql_num_rows($qactivo);



if($ractivo!=0)
   for($i=0; $i<$ractivo; $i++){
      $factivo = mysql_fetch_array($qactivo);
	  
	  //-----------------------------------------
	  $sa = "select 
	               a.NomCompleto as NombreResponsable,
				   b.NomCompleto as NombreUsuario 
			   from 
			       mastpersonas a,
				   mastpersonas b
			  where 
			       a.CodPersona='".$factivo['EmpleadoResponsable']."' and 
				   b.CodPersona='".$factivo['EmpleadoUsuario']."'";
	  $qa = mysql_query($sa) or die ($sa.mysql_error());
	  $fa = mysql_fetch_array($qa);
	  //-----------------------------------------
	  
	  
	  
	  
	  $CodDependencia = $factivo['CodDependencia'];
	  $pdf->SetDrawColor(255, 255, 255);
	  $pdf->SetFillColor(255, 255, 255); 
	  $pdf->SetFont('Arial', 'B', 7);
	  $pdf->SetWidths(array(16,80,55,55,55));
	  $pdf->SetAligns(array('C','L','L','L','L'));
	  $pdf->Row(array($factivo['CodigoInterno'], utf8_decode($factivo['Descripcion']), utf8_decode($factivo['DescpCosto']),
	                  utf8_decode($fa['NombreResponsable']), utf8_decode($fa['NombreUsuario']))); 
   }
   
  
	 
	 list($nombreCompleto, $cargo, $nivel) = getFirmaxDependencia($_PARAMETRO['FIRMAINVENTARIODEP']);
	 
     list($nombreCompleto02, $cargo02, $nivel02) = getFirmaxDependencia($CodDependencia);
   
    $pdf->Cell(20,10,'Total de Bienes: '.$ractivo,0,1,'L');
	$pdf->Ln(8);
    /*$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(202, 202, 202); $pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont('Arial', 'B', 8);
	$pdf->Cell(80,5,'_____________________________',0,0,'C');$pdf->Cell(100,5,'RECIBI CONFORME: _____________________________',0,1,'C');
	$pdf->Cell(80,2,$nivel.' '.$nombreCompleto,0,0,'C');    $pdf->Cell(127,2,$nivel02.' '.$nombreCompleto02,0,1,'C');
	//$pdf->Cell(80,2,$nivel.' '.$nombreCompleto,0,0,'C');    $pdf->Cell(127,2,$nivel02.' '.$recib_conf_nombre,0,1,'C');
	$pdf->Cell(80,3,$cargo,0,0,'C');
	//$pdf->Cell(25,3,'',0,0,'C');                             $pdf->MultiCell(80,3,$cargo02,0,'C');
	$pdf->Cell(25,3,'',0,0,'C');                             $pdf->MultiCell(80,3,$recib_conf_cargo,0,'C');
	/*$pdf->Cell(25,3,'',0,0,'C');                             $pdf->MultiCell(80,3,'DIRECTOR(A) DE RECURSOS HUMANOS (E)',0,'C');*/
$pdf->Output();
?>  

