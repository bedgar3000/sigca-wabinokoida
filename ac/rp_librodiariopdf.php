<?php
/// -------------------------------------------------####
include("../lib/fphp.php");
define('FPDF_FONTPATH','font/');
require('fpdf.php');
//require('fphp.php');
connect(); 
extract ($_POST);
extract ($_GET);
global $Periodo;
/// -------------------------------------------------####
$filtro1=strtr($filtro1, "*", "'");

class PDF extends FPDF
{
//Page header
function Header(){
    
	global $Periodo;
	$this->Image('../imagenes/logos/logo.jpg', 10, 10, 10, 10);	
	$this->SetFont('Arial', 'B', 8);
	$this->SetXY(20, 10); $this->Cell(146, 5,utf8_decode( $_SESSION["NOMBRE_ORGANISMO_ACTUAL"]), 0, 0, 'L');
	                      $this->Cell(10,5,'Fecha:',0,0,'');$this->Cell(10,5,date('d/m/Y'),0,1,'');
	$this->SetXY(20, 15); $this->Cell(145, 5, utf8_decode('Dirección de Administración'), 0, 0, 'L');
	                       $this->Cell(10,5,utf8_decode('Página:'),0,1,'');
	/*$this->SetXY(19, 20); $this->Cell(150, 5, '', 0, 0, 'L');
	                       $this->Cell(7,5,utf8_decode('Año:'),0,0,'L');$this->Cell(6,5,date('Y'),0,1,'L');*/
						   
	list($fano, $fmes) = SPLIT('[-]', $Periodo);
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
	if($fmes!=0){					   
		$this->SetFont('Arial', 'B', 10);
		$this->Cell(50, 10, '', 0, 0, 'C');
		$this->Cell(48, 10, utf8_decode('LIBRO DIARIO AL MES DE'), 0, 0, 'C');
		$this->Cell(18, 10, $mes, 0, 0, 'C'); $this->Cell(8, 10, utf8_decode('DE'), 0, 0, 'C');
		$this->Cell(8, 10, $fano, 0, 1, 'C');
	}else{
	    $this->SetFont('Arial', 'B', 10);
		$this->Cell(50, 10, '', 0, 0, 'C');
		$this->Cell(35, 10, utf8_decode('LIBRO DIARIO DEL'), 0, 0, 'L');
		$this->Cell(10, 10, date("Y"), 0, 0, 'C'); $this->Cell(16, 10, utf8_decode('A LA FECHA'), 0, 1, 'l');
	}
	
	
	$this->SetFont('Arial', 'B', 7);
	//$this->Rect(10,34,191,'','');
	//$this->Rect(10,29,191,'',''); 
	$this->Cell(100, 3, '_________________________________________________________________________', 0, 0, 'C');
	$this->Cell(92, 3, '____________________________________________________________________', 0, 1, 'C'); $this->Ln(1);
	
	$this->Cell(8, 3, 'LIN', 0, 0, 'C');
	$this->Cell(20, 3,'CUENTA', 0, 0, 'C');
	$this->Cell(70, 3, 'DESCRIPCION', 0, 0, 'C');
	$this->Cell(18, 3, 'C.COSTOS', 0, 0, 'R');
	$this->Cell(35, 3, 'DOC. REFERENCIA', 0, 0, 'C');
	$this->Cell(16, 3, 'DEBE', 0, 0, 'R');
	$this->Cell(22, 3, 'HABER', 0, 1, 'R'); 
	$this->Cell(100, 3, '_________________________________________________________________________', 0, 0, 'C');
	$this->Cell(92, 3, '____________________________________________________________________', 0, 1, 'C');
	$this->Ln(5);
	
	///// ******************	
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

$s_con01 = "select 
                  a.Voucher,
				  a.Periodo,
				  a.Linea,
				  a.Descripcion,
				  a.CodCentroCosto,
				  a.ReferenciaTipoDocumento,
				  a.ReferenciaNroDocumento,
				  a.CodCuenta,
				  a.CodPersona,
				  a.MontoVoucher,
				  b.FechaVoucher,
				  b.TituloVoucher,
				  b.Creditos
		    from 
			      ac_voucherdet a
				  inner join ac_vouchermast b on ((b.Voucher = a.Voucher) and 
				  								 (b.Periodo = a.Periodo) and 
												 (b.CodOrganismo = a.CodOrganismo) and 
												 (b.CodContabilidad=a.CodContabilidad))
		    where
			  	  a.Estado='MA' and 
				  a.CodOrganismo<>'' $filtro1
			order by 
			      a.Voucher"; //echo $s_con01;
$q_con01 = mysql_query($s_con01) or die ($s_con01.mysql_error());
$r_con01 = mysql_num_rows($q_con01); //echo $r_con01;
if($r_con01!=0){
  $debe01 = "0,00"; $haber01 ="0,00"; $debe = "0,00"; $haber ="0,00";	
  $t_debe = 0; $t_haber = 0; $cont = 0;	
  for($i=0; $i<$r_con01; $i++){ //echo $i.'/';
     $f_con01 = mysql_fetch_array($q_con01);
	 list($ano, $mes, $dia) = split('[-]',$f_con01['FechaVoucher']); $f_vocucher = $dia.'-'.$mes.'-'.$ano;
	 
	 if($f_con01['Voucher'] != $codVoucherCapturada){
		if($cont==1){
			//echo $t_debe.'-'.$t_haber.'/';
		   $t_debe = number_format($t_debe,2,',','.');	
		   $t_haber = number_format($t_haber,2,',','.');
		   
		   $pdf->SetFont('Arial', 'B', 7);
		   $pdf->Cell(192,6, '______________________________',0,1,'R');
		   	
		   $pdf->SetFillColor(202, 202, 202);
		   $pdf->SetFont('Arial', 'B', 7);
	       $pdf->Cell(104,2, '',0,0,'L'); $pdf->Cell(48,2,'TOTAL VOUCHER ->',0,0,'R'); $pdf->Cell(22,2,$t_debe,0,0,'C');
		   $pdf->Cell(18,2,$t_haber,0,1,'C'); $pdf->ln(3);
		   $t_debe=''; $t_haber='';
		}
		
		list($anio, $mes , $dia) = split('[-]', $f_con01['FechaVoucher']); $fecha_v = $dia.'-'.$mes.'-'.$anio;
		
		$pdf->SetDrawColor(255, 255, 255);$pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
	  	$pdf->SetFont('Arial', 'B', 8);
	  	$pdf->SetWidths(array(30, 135, 30));
	  	$pdf->SetAligns(array('L','L','L'));
	  	$pdf->Row(array("Voucher #: ".$f_con01['Voucher'], "", "Fecha: ".$fecha_v, ""));
		
		$pdf->SetWidths(array(25, 135, 30));
	  	$pdf->SetAligns(array('L','L','L'));
	  	$pdf->Row(array(utf8_decode("Descripción: "), utf8_decode($f_con01['TituloVoucher']), "")); 
		
		
		
		
		$codVoucherCapturada = $f_con01['Voucher']; 
		/*$pdf->SetFillColor(202, 202, 202);
		$pdf->SetFont('Arial', 'B', 7);
		$pdf->Cell(25,6,"Voucher #: ".$f_con01['Voucher'],0,1,'L');*/
		
	    /*$pdf->Cell(25,6,$f_con01['Periodo'].'-'.$f_con01['Voucher'],0,0,'L');
		$pdf->Cell(15,6,$f_vocucher,0,0,'L');
		$pdf->Cell(15,6,$f_con01['CodPersona'],0,0,'L');
		$pdf->Cell(25,6,$f_con01['TituloVoucher'],0,1,'L');*/
	 }
	$cont = 1;
	$valor = substr($f_con01['MontoVoucher'],0,1);
    if($valor == '-'){
      $haber = $f_con01['MontoVoucher'];  //echo $haber;
	  $haber01 = number_format($haber,2,',','.');
    }else{
      $debe = $f_con01['MontoVoucher'];
	  $debe01 = number_format($debe,2,',','.');
    }
	$t_debe = $t_debe + $debe; //echo $t_debe;
	$t_haber = $t_haber + $haber; //echo $t_haber;
	$debe = 0; $haber = 0;
	//***********
	$contMax += 1;
	$_i = $contMax+1; 
	
	/*$this->SetDrawColor(255, 255, 255); $this->SetFillColor(255, 255, 255); $this->SetTextColor(0, 0, 0);
	 $this->SetFont('Arial', 'B', 8);
	 $this->SetWidths(array(20, 170));
	 $this->SetAligns(array('L','L'));
	 $this->Row(array("Comentario: ",$field['Comentario']));*/
	
	
	  $pdf->SetDrawColor(255, 255, 255);$pdf->SetFillColor(255, 255, 255); $pdf->SetTextColor(0, 0, 0);
	  $pdf->SetFont('Arial', '', 7);
	  $pdf->SetWidths(array(6, 20, 75, 15, 35, 20, 20));
	  $pdf->SetAligns(array('C','C','L','C','C','R','R'));
	  $pdf->Row(array($f_con01['Linea'],$f_con01['CodCuenta'],utf8_decode($f_con01['Descripcion']),$f_con01['CodCentroCosto'],$f_con01['ReferenciaTipoDocumento'].'-'.$f_con01['ReferenciaNroDocumento'], $debe01, $haber01));
	  
	  $debe01 = "0,00"; $haber01 ="0,00";
    //echo $_i.'/'.$contMax.'*';
	if($_i > $r_con01){
		   $t_debe = number_format($t_debe,2,',','.');	
		   $t_haber = number_format($t_haber,2,',','.');
		   $pdf->SetFont('Arial', 'B', 7);
		   $pdf->Cell(192,6, '________________________________',0,1,'R');
		   
		   $pdf->SetFillColor(202, 202, 202);
		   $pdf->SetFont('Arial', 'B', 7);
	       $pdf->Cell(104,3, '',0,0,'L'); $pdf->Cell(48,3,'TOTAL VOUCHER ->',0,0,'R'); $pdf->Cell(22,3,$t_debe,0,0,'C');
		   $pdf->Cell(18,3,$t_haber,0,1,'C');
		   
		   $t_debe=''; $t_haber='';
	}
}
}
//---------------------------------------------------*/
/*$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(202, 202, 202); $pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont('Arial', 'B', 8);
	$pdf->Cell(100,10,'',0,1,'L');
	$pdf->Cell(100,10,'ELABORADO POR:',0,0,'L');$pdf->Cell(120,10,'REVISADO POR:',0,0,'L');$pdf->Cell(100,10,'CONFORMADO POR:',0,1,'L');
	$pdf->Cell(100,5,'',0,0,'L');$pdf->Cell(120,5,'',0,0,'L');$pdf->Cell(100,5,'',0,1,'L');
	$pdf->Cell(100,5,'T.S.U. MARIANA SALAZAR',0,0,'L');$pdf->Cell(120,5,'LCDA. YOSMAR GREHAM',0,0,'L');$pdf->Cell(100,5,'LCDA. ROSIS REQUENA',0,1,'L');
	$pdf->Cell(100,2,'ASISTENTE DE PRESUPUESTI I',0,0,'L');$pdf->Cell(120,2,'JEFE(A) DIV. ADMINISTRACION Y PRESUPUESTO',0,0,'L');$pdf->Cell(100,2,'DIRECTORA GENERAL',0,1,'L');*/
$pdf->Output();
?>  