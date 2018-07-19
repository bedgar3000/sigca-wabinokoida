<?php
/// -------------------------------------------------####
include("../lib/fphp.php");
define('FPDF_FONTPATH','font/');
require('fpdf.php');
require('rp_fphp.php');
connect();
extract($_POST);
extract($_GET);
/// -------------------------------------------------
$PeriodoDesde = $fPeriodoDesde;
$PeriodoHasta = $fPeriodoHasta;
$codorganismo = $forganismo;
$contabilidad = $fContabilidad;

 if($fCuentaDesde){
	  $filtro1.=" and a.CodCuenta>='".$fCuentaDesde."'";
	  $filtro1.=" and a.CodCuenta<='".$fCuentaHasta."'";
 }else{
	  $filtro1.=" and a.CodCuenta>='0000000000000'";
	  $filtro1.=" and a.CodCuenta<='9999999999999'";
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

		list($fano, $fmes) = split('[-]', $Periodo);
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

		$this->SetFont('Arial', 'B', 10);
		$this->Cell(200, 10, utf8_decode('BALANCE GENERAL'), 0, 1, 'C');

		$this->SetFont('Arial', 'B', 9);
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
$pdf->Cell(200,2, utf8_decode('CUENTAS DEL TESORO'), 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 7);
$pdf->Cell(200,0,"_______________________________",0,1,'C');
$pdf->ln(6);
//------------------------------------------------------//

if($contabilidad=="F"){

  $tabla="ac_mastplancuenta20";

  if($PeriodoDesde=="" and $PeriodoHasta==""){
	  $fmes= date("m"); $fanio= date("Y");
  }elseif($PeriodoDesde!="" and $PeriodoHasta!=""){
      list($fanio, $fmes)= split('[-]', $PeriodoHasta);
  }

    //--------------------------- ## CUENTAS DEL TESORO ## ---------------------------//
    $niveles='3';
    $grupo='1';
    $sgrupo='1';   //ACTIVO CUENTAS DEL TESORO
    $sgrupo02='2'; //PASIVO CUENTAS DEL TESORO

  	//ACTIVO CUENTAS DEL TESORO
  	$sql = "select * from $tabla where Nivel='$niveles' and Grupo='$grupo' and SubGrupo='$sgrupo'"; //echo $sql;
  	$qry = mysql_query($sql) or die ($sql.mysql_error());
  	$row = mysql_num_rows($qry);

	   if($row!=0){
    		$y1 = 60;
    		$pdf->SetFont('Arial', 'B', 7); $pdf->Cell(50,4,"ACTIVO",0,0,'R'); $pdf->Cell(100,4,"PASIVO",0,1,'R');

    	  for($x=0; $x<$row; $x++){

    		   $field= mysql_fetch_array($qry);

    		   $saldo= getObtenerSaldo($tabla, $field['Rubro'], $fContabilidad, $forganismo, $fanio, $fmes, $filtro1); //echo $saldo.'##';


    		     $TM_ctactivo+= $saldo;


    		   $pdf->SetDrawColor(255, 255, 255); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
    			 $pdf->SetFont('Arial', '', 8);
    			 $pdf->SetWidths(array(12, 60, 22));
    			 $pdf->SetAligns(array('C', 'L', 'R'));
    			 $pdf->SetXY(10, $y1);$pdf->Row(array($field['Rubro'], utf8_decode($field['Descripcion']), number_format($saldo,2,',','.')));

    			 $y1+= 5;

  			  if($field['Rubro']=='132'){
    			   $pdf->SetDrawColor(255, 255, 255); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
    				 $pdf->SetFont('Arial', 'B', 8);
    				 $pdf->SetWidths(array(11, 60, 22));
    				 $pdf->SetAligns(array('C', 'R', 'R'));

    				 $pdf->SetXY(10, $y1); $pdf->Row(array('', '', '-------------------'));
    				 $y1+= 5;
    				 $MontoTesoroActivo = $TM_ctactivo;
    				 $pdf->SetXY(10, $y1); $pdf->Row(array('','Sub Totales - Cuentas del Tesoro=',number_format($TM_ctactivo,2,',','.')));
  			  }
	      }
	   }

	//PASIVO CUENTAS DEL TESORO
	$sql = "select * from $tabla where Nivel='$niveles' and Grupo='$grupo' and SubGrupo='$sgrupo02'"; //echo $sql;
	$qry = mysql_query($sql) or die ($sql.mysql_error());
	$row = mysql_num_rows($qry);

	$TM_ctpasivo="";

	if($row!=0){
		$y2 = 60;
		$saldo=0;
	    for($z=0; $z<$row; $z++){
		  $field = mysql_fetch_array($qry);

		       $saldo= getObtenerSaldo($tabla, $field['Rubro'], $fContabilidad, $forganismo, $fanio, $fmes, $filtro1);

			   if($field['Rubro']=='199'){
				  $pdf->SetDrawColor(255, 255, 255); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
				  $pdf->SetFont('Arial', '', 8);
				  $pdf->SetWidths(array(15, 60, 20, 20));
				  $pdf->SetAligns(array('C', 'R', 'R'));

				  $pdf->SetXY(105, $y2); $pdf->Row(array('', 'Sub Total=', number_format($TM_ctpasivo,2,',','.')));
				  $y2+= 5;
				}
	           $TM_ctpasivo+= $saldo;

				//
				$pdf->SetDrawColor(255, 255, 255); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
				$pdf->SetFont('Arial', '', 8);
				$pdf->SetWidths(array(15, 60, 25));
				$pdf->SetAligns(array('C', 'L', 'R'));

				$pdf->SetXY(105, $y2); $pdf->Row(array($field['Rubro'], utf8_decode($field['Descripcion']), number_format($saldo,2,',','.')));

				$y2+= 5;

				if($field['Rubro']=='199'){
				    $pdf->SetDrawColor(255, 255, 255); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
					$pdf->SetFont('Arial', 'B', 8);
					$pdf->SetWidths(array(15, 60, 25));
					$pdf->SetAligns(array('C', 'R', 'R'));

					$pdf->SetXY(105, $y2); $pdf->Row(array('', '', '---------------------'));
					$y2+= 5;
					$MontoTesoroPasivo = $TM_ctpasivo;
					$pdf->SetXY(105, $y2); $pdf->Row(array('', '', number_format($TM_ctpasivo,2,',','.')));

				}
	    }
	}

	//--------------------------- ## CUENTAS DE LA HACIENDA ## ---------------------------//
	$pdf->ln(8);
	$pdf->SetFont('Arial', 'B', 8);
	$pdf->Cell(200,2, utf8_decode('CUENTAS DE LA HACIENDA'), 0, 1, 'C');

	$pdf->SetFont('Arial', 'B', 7);
	$pdf->Cell(200,0,"_______________________________",0,1,'C');
	$pdf->ln(5);
	//------------------------------------------------------//

	$niveles='3';
	$grupo='2';
  $sgrupo='1';

	//ACTIVO CUENTAS DE LA HACIENDA
	$sql = "select * from $tabla where Nivel='$niveles' and Grupo='$grupo' and SubGrupo='$sgrupo' order by Rubro"; //echo $sql;
	$qry = mysql_query($sql) or die ($sql.mysql_error());
	$row = mysql_num_rows($qry);

	if($row!=0){
		$y1 = 129;
		$TM_ctactivo="";

	  for($b=0; $b<$row; $b++){
		 $field = mysql_fetch_array($qry);

	     $saldo= getObtenerSaldo($tabla, $field['Rubro'], $fContabilidad, $forganismo, $fanio, $fmes, $filtro1);
       $TM_ctactivo+= $saldo;

		    $pdf->SetDrawColor(255, 255, 255); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
			$pdf->SetFont('Arial', '', 8);
			$pdf->SetWidths(array(12, 60, 22));
			$pdf->SetAligns(array('C', 'L', 'R'));


			$pdf->SetXY(10, $y1); $pdf->Row(array($field['Rubro'], utf8_decode($field['Descripcion']), number_format($saldo,2,',','.')));


			$y1+= 5;

		if($field['Rubro']=='240'){
		    $pdf->SetDrawColor(255, 255, 255); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
			$pdf->SetFont('Arial', 'B', 8);
			$pdf->SetWidths(array(12, 60, 23));
			$pdf->SetAligns(array('C', 'R', 'R'));

			$pdf->SetXY(10, $y1); $pdf->Row(array('', '', '-------------------'));
			$y1+= 5;
			$MontoHaciendaActivo = $TM_ctactivo;
			$pdf->SetXY(10, $y1); $pdf->Row(array('','Sub Totales - Cuentas de la Hacienda=',number_format($TM_ctactivo,2,',','.')));
		}
	  }
	}

	//PASIVO CUENTAS DE LA HACIENDA
    $sgrupo02[0]='2'; $Rubro[0]='203';
	$sgrupo02[1]='2'; $Rubro[1]='221';
	$sgrupo02[2]='3'; $Rubro[2]='299';

	$fveces=3;

	$y2 = 130;
	for($f=0; $f<$fveces; $f++){
		$sql = "select *
		          from $tabla
		         where
				       Nivel='$niveles' and
					   Grupo='$grupo' and
					   SubGrupo='$sgrupo02[$f]' and
					   Rubro='$Rubro[$f]'"; //echo $sql;
		$qry = mysql_query($sql) or die ($sql.mysql_error());
		$row = mysql_num_rows($qry);

	    if($row!=0){

		  $TM_ctpasivo="";
	      for($d=0; $d<$row; $d++){
		      $field = mysql_fetch_array($qry);

	          $saldo= getObtenerSaldo($tabla, $field['Rubro'], $fContabilidad, $forganismo, $fanio, $fmes, $filtro1);

		        $TM_ctpasivo+= $saldo;

				//
				$pdf->SetDrawColor(255, 255, 255); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
				$pdf->SetFont('Arial', '', 8);
				$pdf->SetWidths(array(15, 60, 20));
				$pdf->SetAligns(array('C', 'L', 'R'));

				$pdf->SetXY(105, $y2); $pdf->Row(array($field['Rubro'], utf8_decode($field['Descripcion']), number_format($saldo,2,',','.')));


				$y2+= 22;

				if($field['Rubro']=='299'){
				  $pdf->SetDrawColor(255, 255, 255); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
					$pdf->SetFont('Arial', 'B', 8);
					$pdf->SetWidths(array(15, 60, 20));
					$pdf->SetAligns(array('C', 'R', 'R'));

					$pdf->SetXY(105, $y2-16); $pdf->Row(array('', '', '-------------------'));
					$y1+= 5;
					$MontoHaciendaPasivo = $TM_ctpasivo;
					$pdf->SetXY(105, $y2-12); $pdf->Row(array('','',number_format($TM_ctpasivo,2,',','.')));
				}
	      }
	    }
	}

	//--------------------------- ## CUENTAS DEL PRESUPUESTO ## ---------------------------//
	$pdf->ln(8);
	$pdf->SetFont('Arial', 'B', 8);
	$pdf->Cell(200,2, utf8_decode('CUENTAS DEL PRESUPUESTO'), 0, 1, 'C');

	$pdf->SetFont('Arial', 'B', 7);
	$pdf->Cell(200,0,"_______________________________",0,1,'C');
	$pdf->ln(5);
	//------------------------------------------------------//

	$niveles='3';
	$grupo='6';
	$sgrupo='1';  //ACTIVO
    $grupo02='5'; //PASIVO

	//ACTIVO CUENTAS DEL PRESUPUESTO
	$sql = "select * from $tabla where Nivel='$niveles' and Grupo='$grupo' and SubGrupo='$sgrupo' order by Rubro"; //echo $sql;
	$qry = mysql_query($sql) or die ($sql.mysql_error());
	$row = mysql_num_rows($qry);

	if($row!=0){
		$y1 = 205;
		$TM_ctactivo="";

	  for($b=0; $b<$row; $b++){
		$field= mysql_fetch_array($qry);

		$saldo= getObtenerSaldo($tabla, $field['Rubro'], $fContabilidad, $forganismo, $fanio, $fmes, $filtro1);


	     $TM_ctactivo+= $saldo;

	  	    $pdf->SetDrawColor(255, 255, 255); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
			$pdf->SetFont('Arial', '', 8);
			$pdf->SetWidths(array(12, 60, 22));
			$pdf->SetAligns(array('C', 'L', 'R'));

			//if($m_ctactivo=="")$m_ctactivo=0;
			$pdf->SetXY(10, $y1); $pdf->Row(array($field['Rubro'], utf8_decode($field['Descripcion']), number_format($saldo,2,',','.')));

			$y1+= 10;

		if($field['Rubro']=='300'){
		    $pdf->SetDrawColor(255, 255, 255); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
			$pdf->SetFont('Arial', 'B', 8);
			$pdf->SetWidths(array(12, 58, 25));
			$pdf->SetAligns(array('C', 'R', 'R'));

			$pdf->SetXY(10, $y1); $pdf->Row(array('', '', '-------------------'));
			$y1+= 5;
			$MontoActivo= $TM_ctactivo + $MontoHaciendaActivo + $MontoTesoroActivo;
			//$pdf->SetXY(10, $y1); $pdf->Row(array('','Total=',number_format($TM_ctactivo,2,',','.')));
			$pdf->SetXY(10, $y1); $pdf->Row(array('','Total=',number_format($MontoActivo,2,',','.')));
		}
	  }
	}

	//PASIVO CUENTAS DEL PRESUPUESTO
	$sql = "select * from $tabla where Nivel='$niveles' and Grupo='$grupo02' and SubGrupo='$sgrupo'";
	$qry = mysql_query($sql) or die ($sql.mysql_error());
	$row = mysql_num_rows($qry);

	$TM_ctpasivo="";

	if($row!=0){
		$y2 = 205;
	  for($z=0; $z<$row; $z++){

		 $field= mysql_fetch_array($qry);

	       $saldo= getObtenerSaldo($tabla, $field['Rubro'], $fContabilidad, $forganismo, $fanio, $fmes, $filtro1);

		     $TM_ctpasivo+= $saldo;

   			  //
			  $pdf->SetDrawColor(255, 255, 255); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
			  $pdf->SetFont('Arial', '', 8);
			  $pdf->SetWidths(array(15, 60, 23));
			  $pdf->SetAligns(array('C', 'L', 'R'));

		      $pdf->SetXY(105, $y2); $pdf->Row(array($field['Rubro'], utf8_decode($field['Descripcion']), number_format($saldo,2,',','.')));

		      //$pdf->Row(array($field['Rubro'], $field['Descripcion'], number_format($m_ctpasivo,2,',','.')));
		      $y2+= 5;

		     if($field['Rubro']=='303'){
		      $pdf->SetDrawColor(255, 255, 255); $pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
			  $pdf->SetFont('Arial', 'B', 8);
			  $pdf->SetWidths(array(15, 58, 25));
			  $pdf->SetAligns(array('C', 'R', 'R'));

			  $pdf->SetXY(105, $y2); $pdf->Row(array('', '', '-------------------'));
			  $y2+= 5; $MontoPasivo = $TM_ctpasivo + $MontoHaciendaPasivo + $MontoTesoroPasivo;
			  //$pdf->SetXY(105, $y2); $pdf->Row(array('', '', number_format($TM_ctpasivo,2,',','.')));
			  $pdf->SetXY(105, $y2); $pdf->Row(array('', '', number_format($MontoPasivo,2,',','.')));
		     }
	    }
	}

}
    $pdf->Ln(5);
    $pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(202, 202, 202); $pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont('Arial', 'B', 7);
	$pdf->Cell(100,3,'',0,1,'L');
	$pdf->Cell(75,3,'PREPARADO POR:',0,0,'L');
	$pdf->Cell(90,3,'REVISADO POR:',0,0,'L');
	$pdf->Cell(100,3,'CONFORMADO POR:',0,1,'L');

	$pdf->Cell(100,5,'',0,0,'L');
	$pdf->Cell(120,5,'',0,0,'L');
	$pdf->Cell(100,5,'',0,1,'L');

	$pdf->Cell(75,5,'LCDA. DIANNELYS SILVA',0,0,'L');
	$pdf->Cell(90,5,'LCDA. YOSMAR GREHAM',0,0,'L');
	//#*$pdf->Cell(90,5,'LCDA. YOSMAR GREHAM',0,1,'L');
	//*$pdf->Cell(90,5,'LCDA. ROSIS REQUENA',0,1,'L');
	$pdf->Cell(100,5,'ING. EDILIO VELASQUEZ',0,1,'L');

	//$pdf->Cell(60,2,'ANALISTA CONTABLE I',0,0,'L');
	//$pdf->Cell(90,2,'DIRECTORA DE AMINISTRACION(E)',0,0,'L');
	$pdf->Cell(75,2,'ANALISTA CONTABLE I',0,0,'L');
	$pdf->Cell(90,2,'DIRECTORA DE AMINISTRACION',0,0,'L');
	//#*$pdf->Cell(100,2,'DIRECTORA DE AMINISTRACION',0,1,'L');
	//*$pdf->Cell(100,2,'DIRECTORA GENERAL',0,1,'L');
	$pdf->Cell(100,2,'DIRECTOR GENERAL',0,1,'L');
$pdf->Output();
?>
