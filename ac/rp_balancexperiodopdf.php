<?php
/// ------------------------------------------------ ####
include("../lib/fphp.php");
define('FPDF_FONTPATH','font/');
require('fpdf.php');
//require('fphp.php');
connect(); 
/// ------------------------------------------------ ####
$filtro1=strtr($filtro1, "*", "'");

class PDF extends FPDF{
		//Page header
		function Header(){
		    
			global $fd, $fh;
			global $fperiodo;
			$this->Image('../imagenes/logos/logo.jpg', 10, 10, 10, 10);	
			$this->SetFont('Arial', 'B', 8);
			$this->SetXY(20, 10); $this->Cell(146, 5,utf8_decode( $_SESSION["NOMBRE_ORGANISMO_ACTUAL"]), 0, 0, 'L');
			                      $this->Cell(10,5,'Fecha:',0,0,'');$this->Cell(10,5,date('d/m/Y'),0,1,'');
			$this->SetXY(20, 15); $this->Cell(145, 5, utf8_decode('Dirección de Administración'), 0, 0, 'L');
			                       $this->Cell(10,5,utf8_decode('Página:'),0,1,'');
			$this->SetXY(19, 20); $this->Cell(150, 5, '', 0, 0, 'L');
			                       $this->Cell(7,5,utf8_decode('Año:'),0,0,'L');$this->Cell(6,5,date('Y'),0,1,'L');
								   
			list($fano, $fmes) = split('[-]', $fperiodo);
			//echo $fperiodo;
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
			//echo $fd.''.$fh;
			if(($fd!='')and($fh!='')and($fd!='0000-00')and($fh!='9999-99')){					   
				$this->SetFont('Arial', 'B', 10);
				$this->Cell(50, 5, '', 0, 0, 'C');
				$this->Cell(100, 5, utf8_decode('BALANCE DE COMPROBACIÓN'), 0, 1, 'C');
				$this->SetFont('Arial', '', 10);
				$this->Cell(104, 5, utf8_decode('Perídodo: ').$fd, 0, 0, 'R'); $this->Cell(25, 5, 'AL  '.$fh, 0, 1, 'C'); $this->Ln(2);
			}else{
			    $this->SetFont('Arial', 'B', 10);
				$this->Cell(40, 5, '', 0, 0, 'C');
				$this->Cell(100, 5, utf8_decode('BALANCE DE COMPROBACION AL MES DE ').$mes.' DEL '.$fano, 0, 1, 'C'); 
				$this->SetFont('Arial', '', 10);
				$this->Cell(115, 5, utf8_decode('Comprobación Acumulado'), 0, 1, 'R'); ///$this->Cell(25, 5, 'AL   '.date("Y-m"), 0, 1, 'C'); 
				$this->Ln(2);
			}
			
			
			
			$this->SetFont('Arial', 'B', 7);
			$this->Cell(20, 10,'CUENTA', 1, 0, 'C');
			$this->Cell(100, 10,'DESCRIPCION', 1, 0, 'C');
			$this->Cell(40, 5,'SALDO ACTUAL', 1, 1, 'C');
			$this->Cell(20, 0,'', 0, 0, 'C');
			$this->Cell(100, 0,'', 0, 0, 'C');
			$this->Cell(20, 5,'DEBE', 1, 0, 'C');
			$this->Cell(20, 5,'HABER', 1, 1, 'C');
			
			$this->Cell(8, 4, '', 0, 1, 'C');
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
$pdf->SetMargins(10, 5, 10);
$pdf->SetAutoPageBreak(1, 18);
$pdf->AddPage();
$pdf->SetFont('Times','',12);

////------------------------------------------------------------------------ */
//// 
list($fano, $fmes)= split('[-]', $fperiodo);
$periodo_desde= $fano.'-01';
$periodo_hasta= $fano.'-'.($fmes-1);

if($fContabilidad=='T') $tabla = 'ac_mastplancuenta';
else $tabla = 'ac_mastplancuenta20';


$sa= "select a.*,
             b.Descripcion,
             b.Nivel 
        from 
             ac_balancecuenta a 
             inner join $tabla b on (b.CodCuenta=a.CodCuenta)
       where 
             a.CodCuenta>='".$cuenta_desde."' and 
			 a.CodCuenta<='".$cuenta_hasta."' and 
			 a.Anio='".$fano."' and 
			 a.CodContabilidad= '".$fContabilidad."' and 
			 a.CodOrganismo= '".$forganismo."' 
	order by a.CodCuenta"; //echo $sa;
$qa= mysql_query($sa) or die ($sa.mysql_error());
$ra= mysql_num_rows($qa);

if($ra!=0){
  for($i=0; $i<$ra; $i++){
	$field_a = mysql_fetch_array($qa);
	
   /// Movimiento del periodo consultado ---- ####
   $sb= "select a.*,
				b.Descripcion,
				b.Nivel 
		   from 
				ac_voucherdet a 
				inner join $tabla b on (b.CodCuenta=a.CodCuenta and b.Estado='A')
		  where 
				a.Periodo= '".$fperiodo."' and 
				a.CodContabilidad= '".$fContabilidad."' and 
				a.CodOrganismo= '".$forganismo."' and 
				a.Estado= 'MA' and 
				a.CodCuenta= '".$field_a['CodCuenta']."'"; //echo $sb;
   $qb= mysql_query($sb) or die ($sb.mysql_error());
   $rb= mysql_num_rows($qb);
 
   if($rb!=0){ 
	  for($x=0; $x<$rb; $x++){
		 $fb= mysql_fetch_array($qb); 
		 list($voucherA, $voucherB) = split('[-]', $fb['Voucher']); //echo $voucherA.'##';
		 if($voucherA!='33'){
		   if($fb['MontoVoucher']>0) $cta_mes_debe+= $fb['MontoVoucher'];
		   else $cta_mes_haber+= $fb['MontoVoucher'];
		 }
	  }
   }
       
   // Saldo Anterior ------------------- ####
   $sc= "select a.*,
				b.Descripcion,
				b.Nivel 
		   from 
				ac_voucherdet a 
				inner join $tabla b on (b.CodCuenta=a.CodCuenta and b.Estado='A')
		  where 
				a.Periodo>= '".$periodo_desde."' and 
				a.Periodo<= '".$periodo_hasta."' and 
				a.CodContabilidad= '".$fContabilidad."' and 
				a.CodOrganismo= '".$forganismo."' and 
				a.Estado= 'MA' and 
				a.CodCuenta= '".$field_a['CodCuenta']."'"; //echo $sc;
   $qc= mysql_query($sc) or die ($sc.mysql_error());
   $rc= mysql_num_rows($qc);
	 
   if($rc!=0){
	  
	  for($y=0; $y<$rc; $y++){
		  $fc= mysql_fetch_array($qc); 
		  if($fc['MontoVoucher']>"0") $cta_anterior_debe+= $fc['MontoVoucher'];
		  else $cta_anterior_haber+= $fc['MontoVoucher']; 
	  } 
   }

          // Calculos -----------------------------####
          $monto_debe= $cta_anterior_debe + $cta_mes_debe + $field_a['SaldoInicial']; 
          $monto_haber= $cta_anterior_haber + $cta_mes_haber; 
          $monto= round($monto_debe, 2) + round($monto_haber, 2); 

          if($monto>0){ 
          	 $t_monto_debe= $monto; $t_monto_haber=0;
          	 $SALDO_DEBE_TOTAL+=$monto;

          }else{ 
          	 $t_monto_haber= $monto; $t_monto_debe= 0;
          	 $SALDO_HABER_TOTAL+=$monto;
          }
         
          // Imprimir Datos ------------------------####
		  //$t_monto_debe = round($t_monto_debe, 2);
		  //$t_monto_haber = round($t_monto_haber, 2);

		  if(($t_monto_debe!=0)or($t_monto_haber!=0)){
			   /*if($SALDO_ACTUAL_DEBE=='') $SALDO_ACTUAL_DEBE='0,00';
		       if($SALDO_ACTUAL_HABER=='') $SALDO_ACTUAL_HABER='0,00';*/
			   
		     $pdf->SetFillColor(202, 202, 202);
		     $pdf->SetFont('Arial', '', 7);
		     $pdf->Cell(20,4,$field_a['CodCuenta'],0,0,'L'); 
		     $pdf->Cell(100,4,utf8_decode(substr($field_a['Descripcion'],0,80)),0,0,'L'); 
		     $pdf->Cell(20,4,number_format($t_monto_debe,2,',','.'),'','','R');
		     $pdf->Cell(20,4,number_format(-1*$t_monto_haber,2,',','.'),'', 1,'R');
		  }

          $t_monto_debe=0; $t_monto_haber=0; $monto=0;
          $cta_anterior_haber=0; $cta_mes_haber=0;
          $cta_anterior_debe=0;  $cta_mes_debe=0;

 } 
 
 // calculos ------------------------------####
 /*$SALDO_DEBE_TOTAL= $monto_mes_debe + $monto_anterior_debe;
 $SALDO_HABER_TOTAL= $monto_mes_haber + $monto_anterior_haber;*/

    /*$monto_mes_debe+= $cta_mes_debe;
	$monto_mes_haber+= $cta_mes_haber;

	$monto_anterior_debe+= $cta_anterior_debe;
    $monto_anterior_haber+= $cta_anterior_haber;*/

			$pdf->Cell(120, 4, 'Total: ', 1, 0, 'R');	
			$pdf->Cell(20, 4, number_format($SALDO_DEBE_TOTAL, 2, ',', '.'), 1, 0, 'R');	
			$pdf->Cell(20, 4, number_format((-1*$SALDO_HABER_TOTAL), 2, ',', '.'), 1, 1, 'R');
			////------------------------------------------------------------------------ */
			/*$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(202, 202, 202); $pdf->SetTextColor(0, 0, 0);
				$pdf->SetFont('Arial', 'B', 7);
				$pdf->Cell(100,3,'',0,1,'L');
				$pdf->Cell(60,3,'PREPARADO POR:',0,0,'L');$pdf->Cell(90,3,'REVISADO POR:',0,0,'L');$pdf->Cell(100,3,'CONFORMADO POR:',0,1,'L');
				$pdf->Cell(100,5,'',0,0,'L');$pdf->Cell(120,5,'',0,0,'L');$pdf->Cell(100,5,'',0,1,'L');
				$pdf->Cell(60,5,'LCDA. AMARILIS GONZALEZ',0,0,'L');$pdf->Cell(90,5,'LCDA. YOSMAR GREHAM',0,0,'L');$pdf->Cell(100,5,'ING. EDILIO VELASQUEZ',0,1,'L');
				$pdf->Cell(60,2,'ANALISTA CONTABLE I',0,0,'L');$pdf->Cell(90,2,'DIRECTORA DE AMINISTRACION',0,0,'L');$pdf->Cell(100,2,'DIRECTOR(A) GENERAL',0,1,'L');*/
			/*$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(202, 202, 202); $pdf->SetTextColor(0, 0, 0);
				$pdf->SetFont('Arial', 'B', 7);
				$pdf->Cell(100,3,'',0,1,'L');
				$pdf->Cell(60,3,'PREPARADO POR:',0,0,'L');
				$pdf->Cell(90,3,'REVISADO POR:',0,0,'L');
				$pdf->Cell(100,3,'CONFORMADO POR:',0,1,'L');
				
				$pdf->Cell(100,5,'',0,0,'L');
				$pdf->Cell(120,5,'',0,0,'L');
				$pdf->Cell(100,5,'',0,1,'L');
				$pdf->Cell(60,5,'LCDA. YOSMAR GREHAM',0,0,'L');
				$pdf->Cell(90,5,'LCDA. YOSMAR GREHAM',0,0,'L');
				//$pdf->Cell(90,5,'LCDA. ROSIS REQUENA',0,0,'L');
				$pdf->Cell(100,5,'LCDA. ROSIS REQUENA',0,1,'L');
				$pdf->Cell(60,2,'DIRECTORA DE AMINISTRACION',0,0,'L');
				//$pdf->Cell(90,2,'DIRECTORA DE AMINISTRACION(E)',0,0,'L');
				$pdf->Cell(90,2,'DIRECTORA DE AMINISTRACION',0,0,'L');
				$pdf->Cell(100,2,'DIRECTORA GENERAL',0,1,'L');

				/*$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(202, 202, 202); $pdf->SetTextColor(0, 0, 0);
					$pdf->SetFont('Arial', 'B', 7);
					$pdf->Cell(100,3,'',0,1,'L');
					$pdf->Cell(60,3,'PREPARADO POR:',0,0,'L');$pdf->Cell(90,3,'REVISADO POR:',0,0,'L');$pdf->Cell(100,3,'CONFORMADO POR:',0,1,'L');
					$pdf->Cell(100,5,'',0,0,'L');$pdf->Cell(120,5,'',0,0,'L');$pdf->Cell(100,5,'',0,1,'L');
					$pdf->Cell(60,5,'LCDA. YOSMAR GREHAM',0,0,'L');$pdf->Cell(90,5,'LCDA. YOSMAR GREHAM',0,0,'L');$pdf->Cell(100,5,'LCDA. ROSIS REQUENA',0,1,'L');
					$pdf->Cell(60,2,'DIRECTORA DE AMINISTRACION',0,0,'L');$pdf->Cell(90,2,'DIRECTORA DE AMINISTRACION',0,0,'L');$pdf->Cell(100,2,'DIRECTORA GENERAL',0,1,'L');*/
					
$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(202, 202, 202); $pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont('Arial', 'B', 7);
	$pdf->Cell(100,3,'',0,1,'L');
	$pdf->Cell(60,3,'PREPARADO POR:',0,0,'L');
	$pdf->Cell(90,3,'REVISADO POR:',0,0,'L');
	$pdf->Cell(100,3,'CONFORMADO POR:',0,1,'L');
	
	$pdf->Cell(100,5,'',0,0,'L');
	$pdf->Cell(120,5,'',0,0,'L');
	$pdf->Cell(100,5,'',0,1,'L');
	$pdf->Cell(60,5,'LCDA. MARIA RODRIGUEZ',0,0,'L');
	$pdf->Cell(90,5,'LCDA. YOSMAR GREHAM',0,0,'L');
	$pdf->Cell(100,5,'LCDA. ROSIS REQUENA',0,1,'L');
	//$pdf->Cell(100,5,'ING. EDILIO VELASQUEZ',0,1,'L');
	
	$pdf->Cell(60,2,'ANALISTA CONTABLE I',0,0,'L');
	$pdf->Cell(90,2,'DIRECTORA DE AMINISTRACION',0,0,'L');
	$pdf->Cell(90,2,'DIRECTORA GENERAL',0,0,'L');
	//$pdf->Cell(100,2,'DIRECTOR GENERAL(E)',0,1,'L');		
					
					
}
$pdf->Output();
?>  