<?php
/// -------------------------------------------------####
include("../lib/fphp.php");
define('FPDF_FONTPATH','font/');
require('fpdf.php');
//require('fphp.php');
require('rp_fphp.php');
connect(); 
extract($_POST);
extract($_GET);
/// -------------------------------------------------
//---------------------------------------------------
//$filtro1=strtr($filtro1, "*", "'");
//$filtro2=strtr($filtro2, "*", "'");
//---------------------------------------------------
//---------------------------------------------------
//echo $forganismo;
$PeriodoDesde = $fPeriodoDesde;
$PeriodoHasta = $fPeriodoHasta;
$codorganismo = $forganismo;
$contabilidad = $fContabilidad;

 if($fCuentaDesde){ 
	  $filtro1.=" and a.CodCuenta>='".$fCuentaDesde."'";
	  $filtro1.=" and a.CodCuenta<='".$fCuentaHasta."'";
	  
	  //$filtro2.=" and a.CodCuenta>='".$fCuentaDesde."'";
	  //$filtro2.=" and a.CodCuenta<='".$fCuentaHasta."'";
 }else{ 
	  $filtro1.=" and a.CodCuenta>='0000000000000'";
	  $filtro1.=" and a.CodCuenta<='9999999999999'";
	  
	  //$filtro2.=" and a.CodCuenta>='0000000000000'";
	  //$filtro2.=" and a.CodCuenta<='9999999999999'";
 }

class PDF extends FPDF{
	//Page header
	function Header(){
	    
		global $PeriodoDesde, $PeriodoHasta, $contabilidad, $codorganismo, $filtro2; 

		$this->Image('../imagenes/logos/logo.jpg', 10, 10, 10, 10);	
		$this->SetFont('Arial', 'B', 8);
		$this->SetXY(20, 10); $this->Cell(146, 5,utf8_decode( $_SESSION["NOMBRE_ORGANISMO_ACTUAL"]), 0, 0, 'L');
		                      $this->Cell(10,5,'Fecha:',0,0,''); $this->Cell(10,5,date('d/m/Y'),0,1,'');
		$this->SetXY(20, 15); $this->Cell(145, 5, utf8_decode('Dirección de Administración'), 0, 0, 'L');
		                       $this->Cell(10,5,utf8_decode('Página:'),0,1,'');
		/*$this->SetXY(19, 20); $this->Cell(150, 5, '', 0, 0, 'L');
		                       $this->Cell(7,5,utf8_decode('Año:'),0,0,'L');$this->Cell(6,5,date('Y'),0,1,'L');*/
							   
		list($fano, $fmes) = split('[-]', $Periodo); //secho $Periodo;
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
		//echo $fmes;					   
		$this->SetFont('Arial', 'B', 10);
		//$this->Cell(50, 10, '', 0, 0, 'C');
		$this->Cell(200, 10, utf8_decode('ESTADO DE RESULTADO'), 0, 1, 'C');
		
		$this->SetFont('Arial', 'B', 9);
	    //$this->Cell(200, 3, "Del 01/".$fmes."/".$fano."   Al  31/".$fmes."/".$fano, 0, 1, 'C'); $this->Ln(1); 
		$this->Cell(200, 3, "Del ".$PeriodoDesde."   Al  ".$PeriodoHasta, 0, 1, 'C'); $this->Ln(5); 
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
$pdf=new PDF('P','mm','letter');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',12);

//------------------------------------------------------//
$pdf->ln(5);
$pdf->SetFont('Arial', 'B', 8);
//$pdf->Cell(20,2,'5', 0, 0, 'L'); 
//$pdf->Cell(100,2, utf8_decode('INGRESOS'), 0, 1, 'L'); 

$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(30,2,'5', 0, 0, 'L');
$pdf->Cell(100,2, utf8_decode('INGRESOS'), 0, 1, 'L'); 

$pdf->SetFont('Arial', 'B', 7);
$pdf->Cell(30,2,'', 0, 0, 'C');
$pdf->Cell(100,0,"___________",0,1,'L');
$pdf->ln(4);


if($contabilidad=="F"){
	 
  $tabla="ac_mastplancuenta20";

  /*if($PeriodoDesde=="" and $PeriodoHasta==""){
	  $filtro2=" and a.Periodo>='".date("Y").'-'.'01'."' and a.Periodo<='".date("Y-m")."'";
  }elseif($PeriodoDesde!="" and $PeriodoHasta!=""){
      $filtro2=" and a.Periodo>='".$PeriodoDesde."' and a.Periodo<='".$PeriodoHasta."'";
  }*/
  
  if($PeriodoDesde=="" and $PeriodoHasta==""){
	  $fmes= date("m"); $fanio= date("Y");
  }elseif($PeriodoDesde!="" and $PeriodoHasta!=""){
      list($fanio, $fmes)= split('[-]', $PeriodoHasta);
  }
  
  	$niveles='3'; 
  	$sgrupo='1';
  	$grupo02='5';

   //INGRESO
	$sql = "select * 
	          from $tabla 
	         where Nivel='$niveles' and 
	               Grupo='$grupo02' and 
	               SubGrupo='$sgrupo'"; 
	$qry = mysql_query($sql) or die ($sql.mysql_error());
	$row = mysql_num_rows($qry);
	
	$TM_ctpasivo="";
	
	if($row!=0){
	  for($z=0; $z<$row; $z++){
		 $field= mysql_fetch_array($qry);
	       
	       $saldo= getObtenerSaldo($tabla, $field['Rubro'], $fContabilidad, $forganismo, $fanio, $fmes, $filtro1);
		   
		   $totalIngresos+=$saldo; 
		   //
		   $pdf->SetDrawColor(255, 255, 255); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
		   $pdf->SetFont('Arial', '', 8);
		   $pdf->SetWidths(array(30, 120, 40));
		   $pdf->SetAligns(array('L', 'L','L'));
		   $pdf->Row(array($field['Grupo'].'-'.$field['SubGrupo'].'-'.$field['Rubro'], utf8_decode($field['Descripcion']), number_format(-1*$saldo,2,',','.')));
		
		     if($field['Rubro']=='303'){

		     	 $pdf->Cell(169,1,"---------------------",0,1,'R');

		         $pdf->SetDrawColor(255, 255, 255); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
			     $pdf->SetFont('Arial', 'B', 8);

			     $pdf->SetWidths(array(30, 120, 40));
			     $pdf->SetAligns(array('C', 'L', 'R'));
			     $pdf->Row(array('', utf8_decode('TOTAL INGRESOS'), number_format(-1*$totalIngresos,2,',','.')));
			
		
				 $pdf->Cell(190,0,"---------------------",0,1,'R');
				 $pdf->Cell(190,2,"---------------------",0,1,'R');

			     $y2+= 5; $MontoPasivo = $TM_ctpasivo + $MontoHaciendaPasivo + $MontoTesoroPasivo;
			  //$pdf->SetXY(105, $y2); $pdf->Row(array('', '', number_format($TM_ctpasivo,2,',','.')));
			  $pdf->SetXY(105, $y2); $pdf->Row(array('', '', number_format($MontoPasivo,2,',','.')));
		     }
	    }
	}
    
    //------------------------------------------------------//
    //                 GASTOS PRESUPUESTARIOS               //
	//------------------------------------------------------//
			$pdf->ln(60);
			$pdf->SetFont('Arial', 'B', 8);
			$pdf->Cell(30,2,'6-1-300', 0, 0, 'L');
			$pdf->Cell(100,2, utf8_decode('GASTOS PRESUPUESTARIOS'), 0, 1, 'L'); 

			$pdf->SetFont('Arial', 'B', 7);
			$pdf->Cell(30,2,'', 0, 0, 'C');
			$pdf->Cell(100,0,"______________________________",0,1,'L');
			$pdf->ln(6);

			$pdf->SetFont('Arial', 'B', 8);
			$pdf->Cell(30,2,'6-1-300-01', 0, 0, 'L');
			$pdf->Cell(100,3, utf8_decode('GASTOS PERSONAL'), 0, 1, 'L'); 


		    /// -------------------------------------------------- // 
		    $gpgrupo= '6';
		    $gpSubGrupo= '1';
		    $Rubro= '300';
		    $gpCuenta= '01';
		    $gpnivel= '4';

			$sql = "select * 
			          from $tabla 
			         where Nivel> '$gpnivel' and 
			               Grupo= '$gpgrupo' and 
			               SubGrupo= '$gpSubGrupo' and 
			               Cuenta= '$gpCuenta'"; //echo $sql;
			$qry = mysql_query($sql) or die ($sql.mysql_error());
			$row = mysql_num_rows($qry);
			
			$TM_ctpasivo="";
			
			if($row!=0){
			  for($z=0; $z<$row; $z++){
				 $field= mysql_fetch_array($qry);
			       
			       $saldo= getObtenerSaldoGP($tabla, $field['Rubro'], $field['CodCuenta'], $fContabilidad, $forganismo, $fanio, $fmes, $filtro1);
				   
				   $totalIngresos2+=$saldo; 
				   //
				   if ($saldo>'0') {
				   	  # code...
				   	  	$pdf->SetDrawColor(255, 255, 255); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
				   		$pdf->SetFont('Arial', '', 8);
				   		$pdf->SetWidths(array(30, 98, 40));
				   		$pdf->SetAligns(array('L', 'L', 'R'));
				   		$pdf->Row(array($field['Grupo'].'-'.$field['SubGrupo'].'-'.$field['Rubro'].'-'.$field['Cuenta'].'-'.$field['SubCuenta1'].$field['SubCuenta2'], 
				   				   utf8_decode($field['Descripcion']), number_format($saldo,2,',','.')));
				   }
			    }
			}

		    /// Incluyendo las cuentas de Transferencias y Donaciones (6-1-300-07)
		    $gpgrupo= '6';
		    $gpSubGrupo= '1';
		    $Rubro= '300';
		    $gpCuenta= '07';
		    $gpnivel= '5';

		    $sql = "select * 
			          from $tabla 
			         where Nivel> '$gpnivel' and 
			               Grupo= '$gpgrupo' and 
			               SubGrupo= '$gpSubGrupo' and 
			               Cuenta= '$gpCuenta'"; //echo $sql;
			$qry = mysql_query($sql) or die ($sql.mysql_error());
			$row = mysql_num_rows($qry);
		    
		    if($row!=0){
			  for($z=0; $z<$row; $z++){
				 $field= mysql_fetch_array($qry);
			       
			       $saldo= getObtenerSaldoGP($tabla, $field['Rubro'], $field['CodCuenta'], $fContabilidad, $forganismo, $fanio, $fmes, $filtro1);
				   
				   $totalIngresos2+=$saldo; 
				   //
				   if ($saldo>'0') {
				   	  # code...
				   	  	$pdf->SetDrawColor(255, 255, 255); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
				   		$pdf->SetFont('Arial', '', 8);
				   		$pdf->SetWidths(array(30, 98, 40));
				   		$pdf->SetAligns(array('L', 'L', 'R'));
				   		$pdf->Row(array($field['Grupo'].'-'.$field['SubGrupo'].'-'.$field['Rubro'].'-'.$field['Cuenta'].'-'.$field['SubCuenta1'].$field['SubCuenta2'].$field['SubCuenta3'], 
				   				   utf8_decode($field['Descripcion']), number_format($saldo,2,',','.')));
				   }
			    }
			}


		    $pdf->ln(2);
		    $pdf->SetDrawColor(255, 255, 255); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
			$pdf->SetFont('Arial', 'B', 8);
			$pdf->SetWidths(array(30, 118, 40));
			$pdf->SetAligns(array('L', 'L', 'R'));
			$pdf->Row(array('', utf8_decode('TOTAL GASTOS DE PERSONAL'), number_format($totalIngresos2,2,',','.')));
		    $pdf->Ln(3);
    //------------------------------------------------------// 
    
    //------------------------------------------------------//
    //                 GASTOS DE FUNCIONAMIENTO             //
	//------------------------------------------------------//

		    $gpgrupo= '6';
		    $gpSubGrupo= '1';
		    $Rubro= '300';
		    $gpCuenta= '02';
		    $gpnivel= '4';

			$sql = "select * 
			          from $tabla 
			         where Grupo= '$gpgrupo' and 
			               SubGrupo= '$gpSubGrupo' and 
			               Cuenta>= '$gpCuenta' and Cuenta<>'07' "; //echo $sql;
			$qry = mysql_query($sql) or die ($sql.mysql_error());
			$row = mysql_num_rows($qry);
			
			$TM_ctpasivo="";
			
			if($row!=0){
		   
		       
			  for($z=0; $z<$row; $z++){
				 $field= mysql_fetch_array($qry);

				 
				 /// para mostrar totalizador por cuenta
				 if ($field['Cuenta']!=$nro_cuenta and $pase==1 and $montosip==1) {
						  	# code...
						  	$pdf->ln(2);
						    $pdf->SetDrawColor(255, 255, 255); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
							$pdf->SetFont('Arial', 'B', 8);
							$pdf->SetWidths(array(30, 118, 40));
							$pdf->SetAligns(array('L', 'L', 'R'));
							$pdf->Row(array('', 'TOTAL '.utf8_decode(strtr(strtoupper($Descripcion2), "ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ", "àáâãäåæçèéêëìíîïðñòóôõöøùüú")), number_format($totalXcuenta,2,',','.')));
						    $pdf->Ln(3);

						    $totalXcuenta="";
						    $montosip="";
				 }
				 /// ----------------------------

				 if($field['Nivel']=='4'){
			         $nro_cuenta= $field['Cuenta'];
			         $cuentaMostrar= $field['Grupo'].'-'.$field['SubGrupo'].'-'.$field['Rubro'].'-'.$field['Cuenta'];
			         $Descripcion= $field['Descripcion'];
			         $Descripcion2= $field['Descripcion'];
			         $pase=1;
				 }
			     /// ----------------------------	
			       
			       $saldo= getObtenerSaldoGP($tabla, $field['Rubro'], $field['CodCuenta'], $fContabilidad, $forganismo, $fanio, $fmes, $filtro1);
				   
				   $totalIngresos3+= $saldo; 
				   $totalXcuenta+= $saldo;
				   //
				   if ($saldo>'0') {
                      
                      $montosip=1;
                      if($Descripcion!=""){

                      	  $pdf->Ln(2);
	                      $pdf->SetFont('Arial', 'B', 8);
					      $pdf->Cell(30,2, $cuentaMostrar, 0, 0, 'L');
					      $pdf->Cell(100,2,  utf8_decode(strtr(strtoupper($Descripcion), "ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ", "àáâãäåæçèéêëìíîïðñòóôõöøùüú")), 0, 1, 'L');  
					      $pdf->Ln(2);

					      $cuentaMostrar= "";
			         	  $Descripcion= "";

					  }

					    # code...
				   	  	$pdf->SetDrawColor(255, 255, 255); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
				   		$pdf->SetFont('Arial', '', 8);
				   		$pdf->SetWidths(array(30, 98, 40));
				   		$pdf->SetAligns(array('L', 'L', 'R'));
				   		$pdf->Row(array($field['Grupo'].'-'.$field['SubGrupo'].'-'.$field['Rubro'].'-'.$field['Cuenta'].'-'.$field['SubCuenta1'].$field['SubCuenta2'], 
				   				   utf8_decode($field['Descripcion']), number_format($saldo,2,',','.')));
				   }
			    }
			}
		    $pdf->ln(2);
		    $pdf->SetDrawColor(255, 255, 255); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
			$pdf->SetFont('Arial', 'B', 8);
			$pdf->SetWidths(array(30, 118, 40));
			$pdf->SetAligns(array('L', 'L', 'R'));
			$pdf->Row(array('', utf8_decode('TOTAL GASTOS DE FUNCIONAMIENTO'), number_format($totalIngresos3,2,',','.')));

		    $pdf->Ln(2);
    /// -------------------------------------------------- // 
		    
    
    /// -------------------------------------------------- //
      $TotalGastosPresup= $totalIngresos3 + $totalIngresos2;

        $pdf->SetFont('Arial', 'B', 8);
	    $pdf->Cell(30,2,'', 0, 0, 'L');
	    $pdf->Cell(118,2, utf8_decode('TOTAL GASTOS PRESUPUESTARIOS'), 0, 0, 'L');  
	    $pdf->Cell(40,2, number_format($TotalGastosPresup,2,',','.'), 0, 1, 'R');
	    $pdf->Cell(188,1,'--------------------', 0, 1, 'R');
	    $pdf->Ln(3);
	/// -------------------------------------------------- //	

    
   /// EJECUCION DEL PRESUPUESTO
		$ejecucionPresup= (-1*$totalIngresos) - $TotalGastosPresup;	

	       //$saldo= getObtenerSaldo($tabla, '309', $fContabilidad, $forganismo, $fanio, $fmes, $filtro1);
		   
		   //$totalIngresos4+= $saldo; 

		   //if ($totalIngresos4>0) {
		   	  # code...
		   	    $pdf->SetFont('Arial', 'B', 8);
	     		$pdf->Cell(30,2,'2-3-309', 0, 0, 'L');
	            $pdf->Cell(118,2, utf8_decode('EJECUCION DEL PRESUPUESTO'), 0, 0, 'L');
	            $pdf->Cell(40,2, number_format($ejecucionPresup,2,',','.'), 0, 1, 'R');  
	            $pdf->Cell(188,1,'--------------------', 0, 1, 'R');
	            $pdf->Cell(188,1,'--------------------', 0, 1, 'R');
	            $pdf->Ln(2);
		  // }

}
   $pdf->Ln(5);    
    $pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(202, 202, 202); $pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont('Arial', 'B', 7);
	$pdf->Cell(100,3,'',0,1,'L');
	$pdf->Cell(75,3,'PREPARADO POR:',0,0,'L');
	$pdf->Cell(90,3,'REVISADO POR:',0,0,'L');
	$pdf->Cell(100,3,'APROBADO POR:',0,1,'L');
	
	$pdf->Cell(100,5,'',0,0,'L');
	$pdf->Cell(120,5,'',0,0,'L');
	$pdf->Cell(100,5,'',0,1,'L');
	//$pdf->Cell(60,5,'LCDA. MARIA RODRIGUEZ',0,0,'L');
	$pdf->Cell(75,5,'LCDA. DIANNELYS SILVA',0,0,'L');
	$pdf->Cell(90,5,'LCDA. YOSMAR GREHAM',0,0,'L');
	//#$pdf->Cell(90,5,'LCDA. YOSMAR GREHAM',0,1,'L');
	//*$pdf->Cell(90,5,'LCDA. ROSIS REQUENA',0,1,'L');
	$pdf->Cell(100,5,'ING. EDILIO VELASQUEZ',0,1,'L');
	
	$pdf->Cell(75,2,'ANALISTA CONTABLE I',0,0,'L');
	//$pdf->Cell(90,2,'DIRECTORA DE AMINISTRACION(E)',0,0,'L');
	//$pdf->Cell(75,2,'DIRECTORA DE AMINISTRACION',0,0,'L');
	$pdf->Cell(90,2,'DIRECTORA DE AMINISTRACION',0,0,'L');
	//#$pdf->Cell(100,2,'DIRECTORA DE AMINISTRACION',0,1,'L');
	//*$pdf->Cell(100,2,'DIRECTORA GENERAL',0,1,'L');
	$pdf->Cell(100,2,'DIRECTOR GENERAL',0,1,'L');
$pdf->Output();
?>  