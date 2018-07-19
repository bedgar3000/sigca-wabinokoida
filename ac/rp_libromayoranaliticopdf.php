<?php
// ------------------------------------------------ ####
include("../lib/fphp.php");
define('FPDF_FONTPATH','font/');
require('fpdf.php');
//require('fphp.php');
connect(); 
extract ($_POST);
extract ($_GET);
/// ----------------------------------------------- ####
//---------------------------------------------------
$filtro1=strtr($filtro1, "*", "'");
$filtro2=strtr($filtro2, "*", "'");
//---------------------------------------------------
//---------------------------------------------------
class PDF extends FPDF{
//Page header
function Header(){
    
	global $PeriodoDesde,$PeriodoHasta,$contabilidad, $filtro2, $chkPeriodo;
	//echo $PeriodoDesde,$PeriodoHasta,$contabilidad, $filtro2, $chkPeriodo;
		
	$this->Image('../imagenes/logos/logo.jpg', 10, 10, 10, 10);	
	$this->SetFont('Arial', 'B', 8);
	$this->SetXY(20, 10); $this->Cell(146, 5,utf8_decode( $_SESSION["NOMBRE_ORGANISMO_ACTUAL"]), 0, 0, 'L');
	                      $this->Cell(10,5,'Fecha:',0,0,'');$this->Cell(10,5,date('d/m/Y'),0,1,'');
	$this->SetXY(20, 15); $this->Cell(145, 5, utf8_decode('Dirección de Administración'), 0, 0, 'L');
	                       $this->Cell(10,5,utf8_decode('Página:'),0,1,'');
	/*$this->SetXY(19, 20); $this->Cell(150, 5, '', 0, 0, 'L');
	                       $this->Cell(7,5,utf8_decode('Año:'),0,0,'L');$this->Cell(6,5,date('Y'),0,1,'L');*/
						   
	list($fanio, $fmes) = SPLIT('[-]', $PeriodoHasta); 
    switch ($fmes) {
		case "01": $mes = 31; break;  
		case "02": $mes = 28;break; 
		case "03": $mes = 31;break;   
		case "04": $mes = 30;break;   
		case "05": $mes = 31;break;    
		case "06": $mes = 30;break;
		case "07": $mes = 31; break;
		case "08": $mes = 31; break;
		case "09": $mes = 30; break;
		case "10": $mes = 31; break;
		case "11": $mes = 30; break;
		case "12": $mes = 31; break;
    }
	//echo $fmes;					   
	$this->SetFont('Arial', 'B', 10);
	//$this->Cell(50, 10, '', 0, 0, 'C');
	$this->Cell(200, 8, utf8_decode('LIBRO MAYOR ANALITICO'), 0, 1, 'C');
	
	$this->SetFont('Arial', 'B', 9);
    
	if($chkPeriodo==1){
	   list($fanio2, $fmes2) = SPLIT('[-]', $PeriodoDesde); $pdesde = "01/".$fmes2."/".$fanio2;
	   $phasta = $mes."/".$fmes."/".$fanio;	 
	   
	   $this->Cell(200, 3, "Del ".$pdesde."  Al  ".$phasta, 0, 1, 'C'); $this->Ln(4); 
	}else{ 
	   $this->Cell(200, 3, "Del 01/".$fmes."/".$fano."   Al  31/".$fmes."/".$fano, 0, 1, 'C'); $this->Ln(1); 
	}

    
	
	/// 
	$this->SetFont('Arial', 'B', 8);
	$this->Cell(20,3,"Fecha",0,0,"C");
	$this->Cell(20,3,utf8_decode("Número"),0,0,"C");
	$this->Cell(80,3,utf8_decode("Descripción del Asiento"),0,0,"L");
	$this->Cell(25,3,utf8_decode("Débitos"),0,0,"C");
	$this->Cell(25,3,utf8_decode("Créditos"),0,0,"C");
	$this->Cell(25,3,"Saldo Actual",0,1,"C");

    $this->Rect(10,34,195,0,""); $this->Rect(10,39,195,0,""); $this->Ln(3); 


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
$pdf->SetAutoPageBreak(1,9);

if($contabilidad=="F"){ 
  $tabla = "ac_mastplancuenta20";
  $niveles = 3;
}else{ 
  $tabla = "ac_mastplancuenta";
  $niveles = 4;
}


//if($Periodo=='')$filtro2=" and a.Periodo>='".date("Y").'-'.'00'."' and a.Periodo<='".date("Y-m")."'";



//list($anio, $mes) = split('[-]', $PeriodoHasta);
list($anio, $mes) = split('[-]', $PeriodoDesde);
// Modificacion 26-11-2015
/*if($mes!="1"){
   $mes= ($mes-1); 
} */

/// Consulta para obtener cuentas del periodo
$sql = "select a.* 
          from ac_balancecuenta a 
         where a.Anio='$anio' and 
               a.CodContabilidad='$contabilidad' and 
               a.CodOrganismo='$forganismo' $filtro2";  
$qry = mysql_query($sql) or die ($sql.mysql_error());
$row = mysql_num_rows($qry);
if($row != 0){
  for($i=0; $i<$row; $i++){
    $field = mysql_fetch_array($qry);
    $saldo_anterior=""; $saldo_anterior_debe=""; $saldo_anterior_haber="";

//formulas
if($mes>='01')
 	if($field['SaldoInicial']>0)$saldo_anterior_debe+= $field['SaldoInicial'];else  $saldo_anterior_haber+= $field['SaldoInicial'];
	
if($mes>='02')
	if($field['SaldoBalance01']>0) $saldo_anterior_debe+= $field['SaldoBalance01'];else $saldo_anterior_haber+= $field['SaldoBalance01']; 
	
if($mes>='03')
	if($field['SaldoBalance02']>0) $saldo_anterior_debe+= $field['SaldoBalance02'];else $saldo_anterior_haber+= $field['SaldoBalance02'];

if($mes>='04')
	if($field['SaldoBalance03']>0) $saldo_anterior_debe+= $field['SaldoBalance03'];else $saldo_anterior_haber+= $field['SaldoBalance03']; 
	
if($mes>='05')
  if($field['SaldoBalance04']>0) $saldo_anterior_debe+= $field['SaldoBalance04'];else $saldo_anterior_haber+= $field['SaldoBalance04'];
  
if($mes>='06')
   if($field['SaldoBalance05']>0) $saldo_anterior_debe+= $field['SaldoBalance05'];else $saldo_anterior_haber+= $field['SaldoBalance05'];
  
if($mes>='07')
   if($field['SaldoBalance06']>0) $saldo_anterior_debe+= $field['SaldoBalance06'];else $saldo_anterior_haber+= $field['SaldoBalance06'];

if($mes>='08')
   if($field['SaldoBalance07']>0) $saldo_anterior_debe+= $field['SaldoBalance07'];else $saldo_anterior_haber+= $field['SaldoBalance07'];
   
if($mes>='09')
   if($field['SaldoBalance08']>0) $saldo_anterior_debe+= $field['SaldoBalance08'];else $saldo_anterior_haber+= $field['SaldoBalance08'];
  
if($mes >='10'){ 
   if($field['SaldoBalance09']>0) $saldo_anterior_debe+= $field['SaldoBalance09'];else $saldo_anterior_haber+= $field['SaldoBalance09'];
}
if($mes >='11'){
   if($field['SaldoBalance10']>0) $saldo_anterior_debe+= $field['SaldoBalance10'];else $saldo_anterior_haber+= $field['SaldoBalance10'];
}
if($mes =='12') 
   if($field['SaldoBalance11']>0) $saldo_anterior_debe+= $field['SaldoBalance11'];else $saldo_anterior_haber+= $field['SaldoBalance11'];
  
  $saldo_anterior = $saldo_anterior_debe + $saldo_anterior_haber;
  
  
  /*if($niveles = 4) $cuenta = substr($field['CodCuenta'], 0, 5);
  else $cuenta = substr($field['CodCuenta'], 0, 3);*/
	 
 // realizo consulta para obtener cuenta mayor
 $sql01 = "select 
				  a.Descripcion as DescpcuentaMayor
			from 
				  $tabla a
			where 
				  a.CodCuenta='".$field['CodCuenta']."'";
 $qry01 = mysql_query($sql01) or die ($sql01.mysql_error());
 $row01 = mysql_num_rows($qry01);
 
 if($row01 != 0) $field01 = mysql_fetch_array($qry01);
  

  /// imprimir el saldo anterior
  $pdf->SetFont('Arial', 'B', 7);
  $pdf->Cell(30,3, $field['CodCuenta'],0,0,"L");
  $pdf->Cell(100,3, utf8_decode(substr($field01['DescpcuentaMayor'], 0, 70)),0,0,"L");
  $pdf->Cell(25,3, "Saldo Anterior...",0,0,"R");
  $pdf->Cell(35,3, number_format($saldo_anterior,2,',','.'),0,1,"R");
  
  
     $sql02 = "select 
					  a.*,
					  b.Descripcion as DescpCuenta 
				from 
					  ac_voucherdet a
					  inner join $tabla b on (b.Codcuenta=a.Codcuenta) 
			   where 
					  a.Periodo>='$PeriodoDesde' and 
					  a.Periodo<='$PeriodoHasta' and 
					  a.CodContabilidad='$contabilidad' and 
					  a.Estado='MA' and 
					  a.CodCuenta='".$field['CodCuenta']."' and 
					  a.CodOrganismo='$forganismo'
			order by 
					  a.FechaVoucher,a.UltimaFecha"; //echo $sql02;
	 $qry02 = mysql_query($sql02) or die ($sql02.mysql_error());
	 $row02 = mysql_num_rows($qry02);
	 
	 $TotalSaldoActual =0; $TotalDebitos=0; $TotalCreditos=0; $SaldoActual=0;
	 $periodoCapturado=""; $cuentaCapturada=""; $paso="";
	 $SaldoActual=$saldo_anterior;
	 
	 if($row02!=0) for($x=0; $x<$row02; $x++){
					   $field02 = mysql_fetch_array($qry02);
					   
					   if($field02['MontoVoucher']>0){
						 $debitos = number_format($field02['MontoVoucher'],2,',','.');
						 $creditos = "0,00";
						 $SaldoActual+=  $field02['MontoVoucher'];
						 $TotalDebitos+= $field02['MontoVoucher'];
						 
					   }else{
						 $debitos = "0,00";
						 $creditos = number_format((-1*$field02['MontoVoucher']),2,',','.');
						 $SaldoActual+=  $field02['MontoVoucher'];
						 $TotalCreditos+= $field02['MontoVoucher'];
					   }
						
						list($a, $b, $c) = split('[-]', $field02['FechaVoucher']);
						$fechaVoucher = $c.'-'.$b.'-'.$a;					
						
						// echo $periodoCapturado."**".$cuentaCapturada;
						
						if(($periodoCapturado!=$field02['Periodo']) and ($paso==1) and ($cuentaCapturada==$field02['CodCuenta'])){
						  $pdf->SetFont('Arial', 'B', 7); 
						  $pdf->Cell(105,3,"",0,0,'C');
						  $pdf->Cell(70,3, "____________________________________________________________________________",0,1,'C');
						  $pdf->SetFont('Arial', 'B', 7); 
						  $pdf->Cell(114,4, utf8_decode("Total Período ").$periodoCapturado,0,0,'R'); 
						  $pdf->Cell(25,4,number_format($TotalDebitos,2,',','.'),0,0,'R');
						  $pdf->Cell(25,4,number_format((-1*$TotalCreditos),2,',','.'),0,0,'R');
						  if($TotalSaldoActual<0)$pdf->Cell(25,4,"(".number_format($SaldoActual,2,',','.').")",0,1,'R');
						  else $pdf->Cell(25,4,number_format($SaldoActual,2,',','.'),0,1,'R'); 
						  $periodoCapturado = $field02['Periodo'];
						  $cuentaCapturada = $field02['CodCuenta']; 
						}elseif($paso!=1){
						  $paso = 1;
						  $periodoCapturado = $field02['Periodo'];
						  $cuentaCapturada = $field02['CodCuenta']; 
						} 
						
												
						//#5
						$pdf->SetFont('Arial', '', 6); 
						$pdf->Cell(15,4, $fechaVoucher,0,0,'C'); 
						$pdf->Cell(25,4,$field02['ReferenciaTipoDocumento'].'-'.$field02['ReferenciaNroDocumento'],0,0,'C'); 		
						$pdf->Cell(74,4,utf8_decode(substr($field02['Descripcion'], 0, 68)),0,0,'L');
						$pdf->Cell(25,4,$debitos,0,0,'R');
						$pdf->Cell(25,4,$creditos,0,0,'R');
		
						if(number_format($SaldoActual,2,',','.') == "-0.00") $SaldoActual="0";
						$pdf->Cell(25,4,number_format($SaldoActual,2,',','.'),0,1,'R'); 
						
						if( ($periodoCapturado==$field02['Periodo']) and ($row02==($x+1)) and ($cuentaCapturada==$field02['CodCuenta']) ){
						  $pdf->SetFont('Arial', 'B', 7); 
						  $pdf->Cell(105,3,"",0,0,'C');
						  $pdf->Cell(70,3, "____________________________________________________________________________",0,1,'C');
						  $pdf->SetFont('Arial', 'B', 7); 
						  $pdf->Cell(114,4, utf8_decode("Total Período ").$periodoCapturado,0,0,'R'); 
						  $pdf->Cell(25,4,number_format($TotalDebitos,2,',','.'),0,0,'R');
						  $pdf->Cell(25,4,number_format((-1*$TotalCreditos),2,',','.'),0,0,'R');
						  if($TotalSaldoActual<0)$pdf->Cell(25,4,"(".number_format($SaldoActual,2,',','.').")",0,1,'R');
						  else $pdf->Cell(25,4,number_format($SaldoActual,2,',','.'),0,1,'R'); 
						}
						
					 }
						$pdf->SetFont('Arial', 'B', 7); 
						$pdf->Cell(105,3,"",0,0,'C');
						$pdf->Cell(70,3, "____________________________________________________________________________",0,1,'C');
						
						$pdf->SetFont('Arial', 'B', 7); 
						$pdf->Cell(114,4,"Totales Cuenta",0,0,'R');
						$pdf->Cell(25,4,number_format($TotalDebitos,2,',','.'),0,0,'R'); 		
						$pdf->Cell(25,4,number_format((-1*$TotalCreditos),2,',','.'),0,0,'R');
						if($TotalSaldoActual<0)$pdf->Cell(25,4,"(".number_format($SaldoActual,2,',','.').")",0,1,'R');
						else $pdf->Cell(25,4,number_format($SaldoActual,2,',','.'),0,1,'R'); 
						$pdf->Ln(3);
  }}
//---------------------------------------------------*/
/*$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(202, 202, 202); $pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont('Arial', 'B', 8);
	$pdf->Cell(100,10,'',0,1,'L');
	$pdf->Cell(100,10,'ELABORADO POR:',0,0,'L');$pdf->Cell(120,10,'REVISADO POR:',0,0,'L');$pdf->Cell(100,10,'CONFORMADO POR:',0,1,'L');
	$pdf->Cell(100,5,'',0,0,'L');$pdf->Cell(120,5,'',0,0,'L');$pdf->Cell(100,5,'',0,1,'L');
	$pdf->Cell(100,5,'T.S.U. MARIANA SALAZAR',0,0,'L');$pdf->Cell(120,5,'LCDA. YOSMAR GREHAM',0,0,'L');$pdf->Cell(100,5,'LCDA. ROSIS REQUENA',0,1,'L');
	$pdf->Cell(100,2,'ASISTENTE DE PRESUPUESTI I',0,0,'L');$pdf->Cell(120,2,'DIRECTOR(A) DE ADMINISTRACION Y SERVICIOS GENERALES',0,0,'L');$pdf->Cell(100,2,'DIRECTORA GENERAL',0,1,'L');*/
$pdf->Output();
?>  