<?php
include("../lib/fphp.php");
define('FPDF_FONTPATH','font/');
require('fpdf.php');
//require('fphp.php');
connect(); 

if($origen=='lista'){ 
	list($Organismo, $MovimientoNumero, $Estado, $NroActa, $Anio)= split('[,]',$registro);
	global $MovimientoNumero, $Organismo, $origen, $Estado;  
}else{
	 list($Organismo, $contador, $NroActa, $Anio, $MovimientoNumero)= split('[|]',$registro); 
 	global $Organismo, $contador, $NroActa, $Anio, $MovimientoNumero;
}

//echo $registro;
class PDF extends FPDF{

function Header(){
	
	  global $origen;  
	  if($origen=='lista'){ 
         list($Organismo, $MovimientoNumero, $Estado, $NroActa, $Anio)= split('[,]',$registro);
         global $MovimientoNumero, $Organismo, $Estado, $NroActa, $Anio;
      }else{
	     list($Organismo, $contador, $NroActa, $Anio, $MovimientoNumero)= split('[|]',$registro); 
		 global $Organismo, $contador, $NroActa, $Anio, $MovimientoNumero;
	  }
	  global $MovimientoNumero, $Organismo; 
	
    $sql = "select 
					a.MovimientoNumero,
					a.Activo,
					b.FechaAprobacion,
					b.Comentario,
					a.DependenciaAnterior as cod_dependencia,
					c.Dependencia as DescpDependencia					 
			  from 
			       af_movimientosdetalle a 
				   inner join af_movimientos b on (a.MovimientoNumero=b.MovimientoNumero) and 
				                                  (a.Organismo=b.Organismo) and 
												  (a.Anio=b.Anio)
				   inner join mastdependencias c on (c.CodDependencia = a.Dependencia) 
		     where 
			       a.MovimientoNumero='".$MovimientoNumero."' and 
				   a.Organismo='".$Organismo."' and 
				   a.Anio='".$Anio."'";  
    $qry = mysql_query($sql) or die ($sql.mysql_error());
    $field = mysql_fetch_array($qry);
	
	list($sano, $smes, $sdia)= split('[-]', $field['FechaAprobacion']);
	$fechaAprobacion = $sdia.'-'.$smes.'-'.$sano;
	
	$this->Image('../imagenes/logos/logo.jpg', 10, 10, 15, 15);	
	$this->SetFont('Arial', 'B', 8);
	$this->SetXY(25, 10); $this->Cell(100, 8,utf8_decode('República Bolivariana de Venezuela'), 0, 1, 'L');
	$this->SetXY(25, 14); $this->Cell(100, 8,utf8_decode($_SESSION["NOMBRE_ORGANISMO_ACTUAL"]), 0, 1, 'L');
	$this->SetXY(25, 18); $this->Cell(100, 8,utf8_decode('Dirección de Servicios Generales'), 0, 1, 'L');
	
	$this->SetXY(35, 10); $this->Cell(140, 8, 'Fecha:', 0, 0, 'R');$this->Cell(10,8,$fechaAprobacion,0,1,'');
	
	$this->SetXY(155, 15); $this->Cell(20, 4, utf8_decode('Pág.:'), 0, 1, 'R'); /// NRO DE PÁGINA
	
	$this->SetXY(10, 34); $this->Cell(20, 4, utf8_decode('Movimiento#:'), 0, 0, 'L');/// NRO DE DOCUMENTO
						  $this->Cell(10, 4, $field['cod_dependencia'].'-'.$field['MovimientoNumero'].'-'.$sano, 0, 1, 'L');
	
   $this->SetFont('Arial', 'B', 10);
   $this->SetXY(75, 27); $this->Cell(70, 5, utf8_decode('TRANSFERENCIA DE ACTIVO FIJO'), 0, 1, 'C');
   $this->Ln(7);
	
	
	$this->SetDrawColor(255, 255, 255); $this->SetFillColor(255, 255, 255); $this->SetTextColor(0, 0, 0);
	 $this->SetFont('Arial', 'B', 8);
	 $this->SetWidths(array(20, 170));
	 $this->SetAligns(array('L','L'));
	 $this->Row(array("Comentario: ",utf8_decode($field['Comentario'])));
	
	
	
	
    $this->SetFont('Arial','B','8');
	$this->Cell(60, 5, utf8_decode('Items a ser transferidos: '), 0, 0, 'L');
					     $this->Cell(20, 5,'', 0, 1, 'L');
						 
						 
}
//Page footer
function Footer(){
    //Position at 1.5 cm from bottom
    $this->SetXY(153,13);
    //Arial italic 8
    $this->SetFont('Arial','I',8);
    //Page number
    $this->Cell(0,8,' '.$this->PageNo().'/{nb}',0,0,'C');
}
}

//Instanciation of inherited class
$pdf=new PDF('P','mm','letter');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',12);

$scon= "select a.*, c.NomCompleto, d.Descripcion as DescpActivo,
			   d.CodigoInterno, b.AprobadoPor, b.PreparadoPor as quienPrepara
		  from af_movimientosdetalle a
			   inner join af_movimientos b on (a.MovimientoNumero=b.MovimientoNumero) and 
			                                  (a.Organismo=b.Organismo) and 
											  (b.Estado='AP') and 
											  (b.Anio=a.Anio)
			   inner join mastpersonas c on (c.CodPersona=b.AprobadoPor)
			   inner join af_activo d on (d.Activo=a.Activo) and 
			                             (d.CodOrganismo=a.Organismo)
		 where a.MovimientoNumero= '".$MovimientoNumero."' and
			   a.Organismo= '".$Organismo."' and 
			   a.Anio= '".$Anio."'";  
$qcon= mysql_query($scon) or die ($scon.mysql_error());
$rcon= mysql_num_rows($qcon); 

if($rcon!=0){
  for($a=0; $a<$rcon; $a++){
	  
	  $pdf->SetFont('Arial','B','8');
	  $pdf->Cell(80, 3,"___________________________________________________________", 0, 0, 'L');
	  $pdf->Cell(80, 3,"_______________________________________________________________________", 0, 1, 'L'); $pdf->ln(1);
	  
	  $pdf->Cell(20, 2, utf8_decode('#'), 0, 0, 'C');
						 $pdf->Cell(15,2,'Activo', 0, 0, 'C');
						 $pdf->Cell(100,2,utf8_decode('Descripción'), 0, 0, 'C');
						 $pdf->Cell(25,2,utf8_decode('Código Interno'), 0, 0, 'C');
						 $pdf->Cell(25,2,utf8_decode('#Serie'),0, 1, 'C'); 
	  $pdf->SetFont('Arial','B','8');
	  $pdf->Cell(80, 1,"___________________________________________________________", 0, 0, 'L');
	  $pdf->Cell(80, 1,"_______________________________________________________________________", 0, 1, 'L'); $pdf->ln(2);
	  
	  
     $fcon=mysql_fetch_array($qcon);

	//// -------------   CENTRO COSTOS
	$CentroCosto[0]= $fcon['CentroCosto']; 
	$CentroCosto[1]= $fcon['CentroCostoAnterior'];
	$C_Costo = 2;
  	for($i=0; $i<$C_Costo; $i++){
		 $scc = "select Descripcion from ac_mastcentrocosto where CodCentroCosto='$CentroCosto[$i]'";
		 $qcc = mysql_query($scc) or die ($scc.mysql_error());
		 $fcc = mysql_fetch_array($qcc);
	 
		 if($i==0)$cc_actual = $fcc['Descripcion'];	 
		 if($i==1)$cc_anterior = $fcc['Descripcion'];	
     }
	 
	//// ------------    UBICACION
	$ubicacion[0]= $fcon['Ubicacion'];
	$ubicacion[1]= $fcon['UbicacionAnterior'];
	$v_ubicacion = 2;
	for($x=0; $x<$v_ubicacion; $x++){
		 $su = "select Descripcion from af_ubicaciones where CodUbicacion ='$ubicacion[$x]'";
		 $qu = mysql_query($su) or die ($su.mysql_error());
		 $fu = mysql_fetch_array($qu);
		
		 if($x==0)$ubicacion_actual=$fu['Descripcion'];
		 if($x==1)$ubicacion_anterior=$fu['Descripcion'];
	}
	
	//// ------------    DEPENDENCIA
	$dependencia['0']= $fcon['Dependencia'];
	$dependencia['1']= $fcon['DependenciaAnterior'];
	$v_dependencia = 2;
	for($y=0; $y<$v_dependencia; $y++){
		 $sd = "select CodPersona,Dependencia from mastdependencias where CodDependencia ='$dependencia[$y]'";
		 $qd = mysql_query($sd) or die ($sd.mysql_error());
		 $fd = mysql_fetch_array($qd);
		
		 if($y==0)$dependencia_actual=$fd['Dependencia'];
		 if($y==1)$dependencia_anterior=$fd['Dependencia'];
	}
	
	//// ------------    EMPLEADO USUARIO , EMPLEADO RESPONSABLE
	$empleado['0']= $fcon['EmpleadoUsuario'];
	$empleado['1']= $fcon['EmpleadoUsuarioAnterior'];
	$empleado['2']= $fcon['EmpleadoResponsable'];
	$empleado['3']= $fcon['EmpleadoResponsableAnterior'];
	$empleado['4']= $fcon['AprobadoPor'];
	$empleado['5']= $fcon['EmpleadoResponsableAnterior'];
	$empleado['6']= $fcon['quienPrepara'];

	$v_emp = 6;
	for($z=0; $z<$v_emp; $z++){
		 $se = "select NomCompleto from mastpersonas where CodPersona ='$empleado[$z]'";
		 $qe = mysql_query($se) or die ($se.mysql_error());
		 $fe = mysql_fetch_array($qe);
		
		 if($z==0)$empleado_usuario=$fe['NomCompleto'];
		 if($z==1)$empleado_usuario_anterior=$fe['NomCompleto'];
		 if($z==2)$empleado_responsable=$fe['NomCompleto'];
		 if($z==3)$empleado_responsable_anterior=$fe['NomCompleto'];
		 if($z==4)$AprobadoPor=$fe['NomCompleto'];
		 if($z==5)$empleado_responsable_anterior=$fe['NomCompleto'];
	}
		 
	 $pdf->SetDrawColor(255, 255, 255); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
	 $pdf->SetFont('Arial', 'B', 8);
	 $pdf->SetWidths(array(35, 105, 20));
	 $pdf->SetAligns(array('R','L','L'));
	 $pdf->Row(array($fcon['MovimientoNumero'].'  '.$fcon['Activo'],$fcon['DescpActivo'],$fcon['CodigoInterno']));
     
	 $pdf->Cell(80, 5, 'Anterior', 0, 0 , 'C');  $pdf->Cell(85, 5, 'Actual', 0, 1, 'C');
	 
	 $pdf->SetFont('Arial', '', 6);
     
	 /// Mostrando datos en pantalla -----------------------------------####	 
	 $pdf->SetDrawColor(255, 255, 255); 
	 $pdf->SetFillColor(255, 255, 255); 
	 $pdf->SetTextColor(0, 0, 0);
	 $pdf->SetFont('Arial', 'B', 6);
	 $pdf->SetWidths(array(20,11,80,10,70));
	 $pdf->SetAligns(array('L','L','L','L','L'));
	 $pdf->Row(array("C.Costos:", $fcon['CentroCostoAnterior'], strtoupper(utf8_decode($cc_anterior)), $fcon['CentroCosto'],
	                 utf8_decode(strtoupper($cc_actual)) ));
	 
	 $pdf->Cell(20, 4, utf8_decode('Ubicación:'), 0, 0, 'L'); 
	 					  $pdf->Cell(11, 4, $fcon['UbicacionAnterior'], 0, 0, 'L');
						  $pdf->Cell(80, 4, strtoupper(utf8_decode($ubicacion_anterior)), 0, 0, 'L');
						  $pdf->Cell(10, 4, $fcon['Ubicacion'], 0, 0, 'L');
						  $pdf->Cell(10, 4, strtoupper(utf8_decode($ubicacion_actual)), 0, 1, 'L'); 
	 	 
	 $pdf->Cell(20, 4, utf8_decode('Dependencia:'), 0, 0, 'L'); 
	 					  $pdf->Cell(11, 4, $fcon['DependenciaAnterior'], 0, 0, 'L');
						  $pdf->Cell(80, 4, $dependencia_anterior, 0, 0, 'L'); 
						  $pdf->Cell(10, 4, $fcon['Dependencia'], 0, 0, 'L'); 
						  $pdf->Cell(10, 4, $dependencia_actual, 0, 1, 'L'); 
	 
	 $pdf->Cell(20, 4, utf8_decode('Emp. Usuario:'), 0, 0, 'L');
	 					  $pdf->Cell(11, 4, $fcon['EmpleadoUsuarioAnterior'], 0, 0, 'L');
						  $pdf->Cell(80, 4, $empleado_usuario_anterior, 0, 0, 'L');
						  $pdf->Cell(10, 4, $fcon['EmpleadoUsuario'], 0, 0, 'L');
						  $pdf->Cell(10, 4, $empleado_usuario, 0, 1, 'L'); 
						      
	 $pdf->Cell(20, 4, utf8_decode('Emp. Responsable:'), 0, 0, 'L'); 
	 					  $pdf->Cell(11, 4, $fcon['EmpleadoResponsableAnterior'], 0, 0, 'L');
						  $pdf->Cell(80, 4, $empleado_responsable_anterior, 0, 0, 'L'); 
						  $pdf->Cell(10, 4, $fcon['EmpleadoResponsable'], 0, 0, 'L');
						  $pdf->Cell(10, 4, $empleado_responsable, 0, 1, 'L');
	 $pdf->Ln(5);

      
	  $sql01= "select a.CodPersona as CodPersonaActual,
	    			  a.CodCargo as CargoPersonaActual,
					  b.CodPersona as CodPersonaAnterior,
					  b.CodCargo as CargoPersonaAnterior
				 from 
				      mastdependencias a,
					  mastdependencias b 
				where 
				      a.CodDependencia='".$fcon['Dependencia']."' and  
					  b.CodDependencia='".$fcon['DependenciaAnterior']."'";
	  $qry01= mysql_query($sql01) or die ($sql01.mysql_error());
	  $row01= mysql_num_rows($qry01);
	  if($row01!=0) $field01= mysql_fetch_array($qry01);
     
	 // OBTENIENDO FIRMAS -----------------------------------------------####
	 $sa= "select ResponsablePrimario, 
	              EmpleadoRespon, 
	              DescripCargoRespon 
	         from af_actaentregaactivo 
			where NroActa='".$NroActa."' and 
			      Anio='".$Anio."' and 
				  CodOrganismo='".$Organismo."'";
	 $qa= mysql_query($sa) or die ($sa.mysql_error());
	 $fa= mysql_fetch_array($qa);
	 
	 // Responsable anterior
	 //list($nombResponAnterior, $cargoResponAnterior, $nivelResponAnterior)= getFirma($field01['CodPersonaAnterior'],'','',$field01['CargoPersonaAnterior']);
	 
	 list($nombResponAnterior, $cargoResponAnterior, $nivelResponAnterior)= getFirmaxDependencia($fcon['DependenciaAnterior'],'','');
	 
	 // quein recibe - responsable primario - responsable actual
	 list($nombResponActual, $cargoResponActual, $nivelResponActual)= getfirma($fa['ResponsablePrimario']); 
	 
     // quien prepara - registrador de bienes
	 list($nombQuienPrepara, $cargoQuienPrepara, $nivelQuienPrepara)= getfirma($fcon['quienPrepara']);
	 
	 
	 list($anos, $meses, $dias, $hora)=split('[-, ]', $fcon['UltimaFechaModif']);
	 
	 list($ano, $mes, $dia) = split('[-]', $fcon['FechaRevisadoPor']);
	 $fecha = $dia.'-'.$mes.'-'.$ano; //echo $mes;
	 
	 switch($mes){
			case "01": $fmes= Enero;break;  
			case "02": $fmes= Febrero;break; 
			case "03": $fmes= Marzo;break;   
			case "04": $fmes= Abril;break;   
			case "05": $fmes= Mayo;break;    
			case "06": $fmes= Junio;break;
			case "07": $fmes= Julio; break;
			case "08": $fmes= Agosto; break;
			case "09": $fmes= Septiembre; break;
			case "10": $fmes= Octubre; break;
			case "11": $fmes= Noviembre; break;
			case "12": $fmes= Diciembre; break;
		}
	
	  $montoLocal = number_format($fcon['MontoLocal'],2,',','.');
	  
	  }
  
  
     $pdf->ln(5); 
     $pdf->SetFont('Arial','B','8');
	 $pdf->Cell(95,3,"_______________________________________",0,0,'C');
	 $pdf->Cell(95,3,"_______________________________________",0,1,'C'); $pdf->ln(1);
	   
	 $pdf->Cell(90, 5,$nivelResponAnterior.' '.$nombResponAnterior,0,0,'C');    
	 $pdf->Cell(100, 5,$nivelResponActual.' '.$nombResponActual,0,1,'C');
	
	 // Responsable Primario - Responsable Actual
	 $pdf->SetDrawColor(255, 255, 255); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
	 $pdf->SetFont('Arial', 'B', 8);
	 $pdf->SetWidths(array(95,95));
	 $pdf->SetAligns(array('C','C'));
	 $pdf->Row(array($cargoResponAnterior, $fa['DescripCargoRespon']));
	 $pdf->Ln(10);
  	 
	 // quien prepara - registrador de bienes 
     $pdf->SetFont('Arial','B','8');
	 $pdf->Cell(180, 3,"_______________________________________", 0, 1, 'C');
	 $pdf->Cell(180, 4,$nivelQuienPrepara.' '.$nombQuienPrepara, 0, 1, 'C'); 
	 $pdf->Cell(180, 4,$cargoQuienPrepara, 0, 1, 'C'); $pdf->ln(1);  	  
}
/*$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(202, 202, 202); $pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont('Arial', 'B', 8);
	$pdf->Cell(100,10,'',0,1,'L');
	$pdf->Cell(100,10,'ELABORADO POR:',0,0,'L');$pdf->Cell(120,10,'REVISADO POR:',0,0,'L');$pdf->Cell(100,10,'CONFORMADO POR:',0,1,'L');
	$pdf->Cell(100,5,'',0,0,'L');$pdf->Cell(120,5,'',0,0,'L');$pdf->Cell(100,5,'',0,1,'L');
	$pdf->Cell(100,5,'T.S.U. MARIANA SALAZAR',0,0,'L');$pdf->Cell(120,5,'LCDA. YOSMAR GREHAM',0,0,'L');$pdf->Cell(100,5,'LCDA. ROSIS REQUENA',0,1,'L');
	$pdf->Cell(100,2,'ASISTENTE DE PRESUPUESTI I',0,0,'L');$pdf->Cell(120,2,'JEFE(A) DIV. ADMINISTRACION Y PRESUPUESTO',0,0,'L');$pdf->Cell(100,2,'DIRECTORA GENERAL',0,1,'L');*/ 

$pdf->Output();
?>  