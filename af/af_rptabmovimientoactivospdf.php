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
    
	global $Periodo, $filtro;
	$this->Image('../imagenes/logos/logo.jpg', 10, 10, 10, 10);	
	$this->SetFont('Arial', 'B', 9);
	$this->SetXY(20, 10); $this->Cell(70, 5,utf8_decode('República Bolivariana de Venezuela'), 0, 1, 'L');
	$this->SetXY(20, 14); $this->Cell(70, 5,utf8_decode($_SESSION["NOMBRE_ORGANISMO_ACTUAL"]), 0, 1, 'L'); 
	$this->Ln(4);
	
	$this->SetXY(220, 10);$this->Cell(11,5,'Fecha: ',0,0,'L');$this->Cell(10,5,date('d/m/Y'),0,1,'');
	$this->SetXY(220, 15);$this->Cell(11,5,utf8_decode('Página:'),0,1,'');
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
	
	/*$sql = "select a.* from af_activo a where CodOrganismo<>'' $filtro"; //echo $sql;
	$qry = mysql_query($sql) or die ($sql.mysql_error());
	$field = mysql_fetch_array($qry); */
	
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
	$this->Cell(200, 3, utf8_decode('Relación de Activos Transferidos'), 0, 1, 'C');$this->Ln(4);
	
	$this->SetFont('Arial', '', 8);
	  $this->Cell(130,4,'__________________________________________________________________________________________________________________________________________________________________________________',0,1,'L');
	$this->SetDrawColor(0, 0, 0); $this->SetFillColor(200, 200, 200); $this->SetTextColor(0, 0, 0);
	$this->SetFont('Arial', 'B', 8);
	$this->Cell(17, 4, 'Nro. Mov.', 0, 0, 'C', 0);
	$this->Cell(17, 4, 'Activo', 0, 0, 'C', 0);
	$this->Cell(17, 4, 'Cod. Interno', 0, 0, 'C', 0);
	$this->Cell(62, 4, utf8_decode('Descripción'), 0, 0, 'C', 0);
	$this->Cell(20, 4, 'Cod. Barras', 0, 0, 'C', 0);
	$this->Cell(90, 4, 'Nuevo', 0, 0, 'C', 0);
	$this->Cell(50, 4, 'Anterior', 0, 1, 'L', 0);
	//$this->SetFillColor(255, 255, 255);
	
	$this->SetFont('Arial', '', 8);
	  $this->Cell(130,2,'__________________________________________________________________________________________________________________________________________________________________________________',0,1,'L');
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
$pdf=new PDF('L','mm','A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',12);

//// -----------------------------------------------------------------------------------------
if($_POST['forganismo']!="") $filtro.= " and (a.Organismo= '".$forganismo."')"; 
if($_POST['fDependencia']!="") $filtro.=" and (a.Dependencia= '".$fDependencia."')"; 
if($_POST['centro_costos']!="") $filtro.= " and (a.CentroCosto= '".$centro_costos."')";
if($_POST['fActivo']!="") $filtro.=" and (a.Activo = '".$fActivo."')";

if($_POST['fSituacionActivo']!="") $filtro2.= " and (c.SituacionActivo= '".$fSituacionActivo."')";
if($_POST['fNaturaleza']!="") $filtro2.=" and (c.Naturaleza='".$fNaturaleza."')";


//if($_POST['fNaturaleza']!="") $filtro.=" AND (Naturaleza = '".$fNaturaleza."')"; 
 



if(($_POST['fFechaAprobacionDesde']!="")and($_POST['fFechaAprobacionHasta']!="")){

   list($fad, $fam, $faa) = split('[-]', $_POST['fFechaAprobacionDesde']);
   $fFechaAprobacionDesde= $faa.'-'.$fam.'-'.$fad;
   
   list($fadh, $famh, $faah) = split('[-]', $_POST['fFechaAprobacionHasta']);
   $fFechaAprobacionHasta= $faah.'-'.$famh.'-'.$fadh;

   $filtro3.=" and b.FechaAprobacion>='".$fFechaAprobacionDesde."' and b.FechaAprobacion<='".$fFechaAprobacionHasta."'";
}else{

  $fFechaAprobacionDesde= '0000-00-00';
  $fFechaAprobacionHasta= date("Y-m-d");
  $filtro3.=" and b.FechaAprobacion>='".$fFechaAprobacionDesde."' and b.FechaAprobacion<='".$fFechaAprobacionHasta."'";
} 

if(($_POST['fFechaPreparacionDesde']!="")and($_POST['fFechaPreparacionHasta']!="")){
  
   list($fpd, $fpm, $fpa) = split('[-]', $_POST['fFechaPreparacionDesde']);
   $fFechaPreparacionDesde= $fpa.'-'.$fpm.'-'.$fpd;
   
   list($fpdh, $fpmh, $fpah) = split('[-]', $_POST['fFechaPreparacionHasta']);
   $fFechaPreparacionHasta= $fpah.'-'.$fpmh.'-'.$fpdh;
   
   $filtro3.=" and b.FechaPreparacion>='".$fFechaPreparacionDesde."' and b.FechaPreparacion<='".$fFechaPreparacionHasta."'";
} 

if($_POST['fub_actual']!="") $filtro.=" and a.Ubicacion='".$fub_actual."'";
if($_POST['fub_anterior']!="") $filtro.=" and a.UbicacionAnterior='".$fub_anterior."'";
//// -----------------------------------------------------------------------------------------


$s_con_01 = "select 
					a.MovimientoNumero, a.Activo, a.Ubicacion, a.UbicacionAnterior, 
					a.Organismo, a.CentroCosto, a.CentroCostoAnterior,a.Dependencia,
					a.DependenciaAnterior,a.EmpleadoUsuario,a.EmpleadoUsuarioAnterior,
					a.OrganismoActual,a.Organismoanterior,a.Dependencia,a.DependenciaAnterior,
					b.PreparadoPor, b.FechaPreparacion, b.AprobadoPor, b.FechaAprobacion, 
					b.MotivoTraslado, b.InternoExternoFlag,
					c.CodigoInterno,c.Descripcion,
					c.CodigoBarras,c.Naturaleza 
			   from 
			    	af_movimientosdetalle a
					inner join af_movimientos b on (a.MovimientoNumero=b.MovimientoNumero) and (a.Organismo=b.Organismo) and (b.Estado='AP') $filtro3
					inner join af_activo c on (c.Activo=a.Activo)and(c.CodOrganismo=a.Organismo) $filtro2
			   where 
			        a.Organismo<>'' $filtro"; //echo $s_con_01;
$q_con_01 = mysql_query($s_con_01) or die ($s_con_01.mysql_error());
$r_con_01 = mysql_num_rows($q_con_01); //echo $r_con_01;

if($r_con_01!=0)
   for($i=0; $i<$r_con_01; $i++){
      $f_con_01 = mysql_fetch_array($q_con_01);
	  $CodDependencia = $factivo['CodDependencia'];
	  
	  
	  /// Consultas ///
	  $s_con_02 = "select 
	  					  a.Descripcion as DescpActual,
						  b.Descripcion as DescpAnterior 
					 from 
					      af_ubicaciones a,
						  af_ubicaciones b 
					where 
					      a.CodUbicacion='".$f_con_01['Ubicacion']."' and 
						  b.CodUbicacion='".$f_con_01['UbicacionAnterior']."'";
	  $q_con_02 = mysql_query($s_con_02) or die ($s_con_02.mysql_error());
	  $r_con_02 = mysql_num_rows($q_con_02);
	  if($r_con_02!=0) $f_con_02 = mysql_fetch_array($q_con_02);
	  
	  if($f_con_01['InternoExternoFlag']=='I') $cod_maestro= 'MMOVINTER';
	  else $cod_maestro= 'MMOVINTER';
	  
	   $s_con_03 = "select Descripcion from mastmiscelaneosdet where CodMaestro='$cod_maestro'";
	   $q_con_03 = mysql_query($s_con_03) or die ($s_con_03.mysql_query());
	   $r_con_03 = mysql_num_rows($q_con_03); 
       if($r_con_03!=0)$f_con_03=mysql_fetch_array($q_con_03);	  
	 
	   $s_con_04 = "select 
	                      a.NomCompleto as NombPreparado,
						  b.NomCompleto as NombAprobado,
						  c.NomCompleto as NombEmpleadoUsuario,
						  d.NomCompleto as NombEmpleadoUsuarioAnterior 
				      from 
					       mastpersonas a,
						   mastpersonas b,
						   mastpersonas c,
						   mastpersonas d 
					  where 
					       a.CodPersona='".$f_con_01['PreparadoPor']."' and 
						   b.CodPersona='".$f_con_01['AprobadoPor']."' and 
						   c.CodPersona='".$f_con_01['EmpleadoUsuario']."' and 
						   d.CodPersona='".$f_con_01['EmpleadoUsuarioAnterior']."'";
		$q_con_04 = mysql_query($s_con_04) or die ($s_con_04.mysql_error());
		$r_con_04 = mysql_num_rows($q_con_04);
		if($r_con_04!=0)$f_con_04=mysql_fetch_array($q_con_04);
		
	   list($fano, $fmes, $fdia) = split('[-]', $f_con_01['FechaPreparacion']);
	   list($fa, $fm, $fd )= split('[-]', $f_con_01['FechaAprobacion']);
	   $FechaPreparacion = $fdia.'-'.$fmes.'-'.$fano;
	   $FechaAprobacion = $fd.'-'.$fm.'-'.$fa;
	 
	   $s_con_05 = "select 
	                      a.Descripcion as DescpCentroCostoActual,
						  b.Descripcion as DescpCentroCostoAnterior 
				      from 
					       ac_mastcentrocosto a,
						   ac_mastcentrocosto b 
					  where 
					       a.CodCentroCosto='".$f_con_01['CentroCosto']."' and 
						   b.CodCentroCosto='".$f_con_01['CentroCostoAnterior']."'";
		$q_con_05 = mysql_query($s_con_05) or die ($s_con_05.mysql_error());
		$r_con_05 = mysql_num_rows($q_con_05);
		if($r_con_05!=0) $f_con_05=mysql_fetch_array($q_con_05);
	   
	   $s_con_06 = "select 
	                      a.Dependencia as DependenciaActual,
						  b.Dependencia as DependenciaAnterior,
						  c.Organismo as OrganismoActual,
						  d.Organismo as OrganismoAnterior 
				      from 
					       mastdependencias a,
						   mastdependencias b,
						   mastorganismos c,
						   mastorganismos d 
					  where 
					       a.CodDependencia='".$f_con_01['Dependencia']."' and 
						   b.CodDependencia='".$f_con_01['DependenciaAnterior']."' and 
						   c.CodOrganismo='".$f_con_01['OrganismoActual']."' and
						   d.CodOrganismo='".$f_con_01['Organismoanterior']."'";
		$q_con_06 = mysql_query($s_con_06) or die ($s_con_06.mysql_error());
		$r_con_06 = mysql_num_rows($q_con_06);
		if($r_con_06!=0) $f_con_06=mysql_fetch_array($q_con_06);
	   
	 
	 $DescpActual = myTruncate($f_con_02['DescpActual'], '56', '', '...');
	 $DescpAnterior = myTruncate($f_con_02['DescpAnterior'], '56', '', '...');
	 $DescpCentroCostoActual = myTruncate($f_con_05['DescpCentroCostoActual'], '60', '', '...');
	 $DescpCentroCostoAnterior = myTruncate($f_con_05['DescpCentroCostoAnterior'], '60', '', '...');
	 /*$DependenciaActual = myTruncate($f_con_06['DependenciaActual'], '50', '', '...');
	 $DependenciaAnterior = myTruncate($f_con_06['DependenciaAnterior'], '50', '', '...');*/
	 
	 
	  //// primera línea  
	  $pdf->SetFont('Arial', 'B', 7);
		  $pdf->Cell(17,5,$f_con_01['MovimientoNumero'],0,0,'C');
		  $pdf->Cell(17,5,$f_con_01['Activo'],0,0,'C');
		  $pdf->Cell(17,5,$f_con_01['CodigoInterno'],0,0,'C');
		  $pdf->Cell(62,5,$f_con_01['Descripcion'],0,0,'L');
		  $pdf->Cell(20,5,$f_con_01['CodigoBarras'],0,0,'C');
		  $pdf->SetFont('Arial', 'B', 7);$pdf->Cell(19,5,utf8_decode('Ubicación:'),0,0,'L');
	      $pdf->SetFont('Arial', '', 7);$pdf->Cell(7,5,$f_con_01['Ubicacion'],0,0,'L');
	      $pdf->Cell(62,5,substr (utf8_decode($DescpActual),0,70),0,0,'L');
	      $pdf->SetFont('Arial', '', 7); $pdf->Cell(7,5,$f_con_01['UbicacionAnterior'],0,0,'L');
		  $pdf->Cell(62,5,substr (utf8_decode($DescpAnterior),0,70),0,1,'L');
	  
	  if($f_con_01['Naturaleza']=='AN') $Naturaleza = 'Activo Normal'; else $Naturaleza = 'Activo Menor';
	  //// Segunda Línea
	  $pdf->SetFont('Arial', 'B', 7); $pdf->Cell(35,4,'Naturaleza:',0,0,'R');
	  $pdf->SetFont('Arial', '', 7);  $pdf->Cell(30,4,$Naturaleza,0,0,'L');
	  $pdf->SetFont('Arial', 'B', 7); $pdf->Cell(16,4,utf8_decode('Cód Interno:'),0,0,'L');
	  $pdf->SetFont('Arial', '', 7);  $pdf->Cell(52,4,$f_con_01['CodigoInterno'],0,0,'L');
	  $pdf->SetFont('Arial', 'B', 7); $pdf->Cell(19,4,'Centro Costos:',0,0,'L');
	  $pdf->SetFont('Arial', '', 7);  $pdf->Cell(7,4,$f_con_01['CentroCosto'],0,0,'L');
	  								  $pdf->Cell(62,4,substr (utf8_decode($DescpCentroCostoActual),0,60),0,0,'L');
									  $pdf->Cell(7,4,$f_con_01['CentroCostoAnterior'],0,0,'L');
	  								  $pdf->Cell(62,4,substr (utf8_decode($DescpCentroCostoAnterior),0,60),0,1,'L');
	  
	  $MotivoTraslado=strtolower($f_con_03['Descripcion']);$MotivoTraslado=ucwords($MotivoTraslado); /// Conversión a Minúscula MotivoTraslado
	  $Emp_usuar_actual=strtolower($f_con_04['NombEmpleadoUsuario']); $Emp_usuar_actual=ucwords($Emp_usuar_actual);/// Conversión a Minúscula EmpUsuarActual
	  $Emp_usuar_anterior=strtolower($f_con_04['NombEmpleadoUsuarioAnterior']); $Emp_usuar_anterior=ucwords($Emp_usuar_anterior);/// Conversión a Minúscula EmpUsuarAnterior
	  //// Tercera Línea
	  $pdf->SetFont('Arial', 'B', 7);  $pdf->Cell(35,4,'Motivo de Traslado:',0,0,'R');
	  $pdf->SetFont('Arial', '', 7);   $pdf->Cell(98,4,$MotivoTraslado,0,0,'L');
	  $pdf->SetFont('Arial', 'B', 7);  $pdf->Cell(19,4,'Persona:',0,0,'L');
	  $pdf->SetFont('Arial', '', 7);   $pdf->Cell(9,4,$f_con_01['EmpleadoUsuario'],0,0,'L');
	     							   $pdf->Cell(60,4,$Emp_usuar_actual,0,0,'L');
									   $pdf->Cell(9,4,$f_con_01['EmpleadoUsuarioAnterior'],0,0,'L');
	     							   $pdf->Cell(60,4,$Emp_usuar_anterior,0,1,'L');
	  
	  $PreparadoPor = strtolower($f_con_04['NombPreparado']); $PreparadoPor = ucwords($PreparadoPor); /// Conversión a Minúscula Preparado Por
	  $DependenciaActual = strtolower($f_con_06['DependenciaActual']); $DependenciaActual = ucwords($DependenciaActual); /// Conversión a Minúscula Dependencia Actual
	  $DependenciaAnterior = strtolower($f_con_06['DependenciaAnterior']); $DependenciaAnterior = ucwords($DependenciaAnterior); /// Conversión a Minúscula Dependencia Anterior
	  //// Cuarta Línea
	  $pdf->SetFont('Arial', 'B', 7); $pdf->Cell(35,4,'Preparado Por:',0,0,'R');
	  $pdf->SetFont('Arial', '', 7);  $pdf->Cell(60,4,$PreparadoPor,0,0,'L'); $pdf->Cell(38,4,$FechaPreparacion,0,0,'L');
	  $pdf->SetFont('Arial', 'B', 7); $pdf->Cell(19,4,'Dependencia:',0,0,'L');
	  $pdf->SetFont('Arial', '', 7);  $pdf->Cell(7,4,$f_con_01['Dependencia'],0,0,'L');
	  								  $pdf->Cell(62,4,substr(utf8_decode($DependenciaActual),0,50),0,0,'L');
									  $pdf->Cell(7,4,$f_con_01['DependenciaAnterior'],0,0,'L');
	  								  $pdf->Cell(60,4,substr(utf8_decode($DependenciaAnterior),0,50),0,1,'L');
	  
	  
	  $AprobadoPor = strtolower($f_con_04['NombAprobado']); $AprobadoPor = ucwords($AprobadoPor); /// Conversión a Minúscula Aprobado Por
	  $OrganismoActual = strtolower($f_con_06['OrganismoActual']); $OrganismoActual = ucwords($OrganismoActual); /// Conversión a Minúscula Organismo Actual
	  $OrganismoAnterior = strtolower($f_con_06['OrganismoAnterior']); $OrganismoAnterior = ucwords($OrganismoAnterior); /// Conversión a Minúscula Organismo Anterior
	  //// Quinta Línea
	  $pdf->SetFont('Arial', 'B', 7); $pdf->Cell(35,4,'Aprobado Por:',0,0,'R');
	  $pdf->SetFont('Arial', '', 7);  $pdf->Cell(60,4,$AprobadoPor,0,0,'L'); $pdf->Cell(38,4,$FechaAprobacion,0,0,'L');
	  $pdf->SetFont('Arial', 'B', 7); $pdf->Cell(19,4,'Organismo:',0,0,'L');
	  $pdf->SetFont('Arial', '', 7);  $pdf->Cell(7,4,$f_con_01['OrganismoActual'],0,0,'L');
	  								  $pdf->Cell(62,4,$OrganismoActual,0,0,'L');
									  $pdf->Cell(7,4,$f_con_01['Organismoanterior'],0,0,'L');
	  								  $pdf->Cell(60,4,$OrganismoAnterior,0,1,'L');
	  
	  
	  $pdf->SetFont('Arial', '', 8);
	  $pdf->Cell(130,4,'---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------',0,1,'L');

}

   list($nombreCompleto, $cargo, $nivel) = getFirmaxDependencia($_PARAMETRO['FIRMAINVENTARIODEP']);
	 
   list($nombreCompleto02, $cargo02, $nivel02) = getFirmaxDependencia($CodDependencia);
   /*
    $pdf->Cell(20,10,'Total de Bienes: '.$ractivo,0,1,'L');
$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(202, 202, 202); $pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont('Arial', 'B', 8);
	$pdf->Cell(80,5,'_____________________________',0,0,'C');$pdf->Cell(100,5,'RECIBI CONFORME: _____________________________',0,1,'C');
	$pdf->Cell(80,2,$nivel.' '.$nombreCompleto,0,0,'C');    $pdf->Cell(127,2,$nivel02.' '.$nombreCompleto02,0,1,'C');
	$pdf->Cell(80,3,$cargo,0,0,'C');
	$pdf->Cell(25,3,'',0,0,'C');                             $pdf->MultiCell(80,3,$cargo02,0,'C');*/
$pdf->Output();
?>  