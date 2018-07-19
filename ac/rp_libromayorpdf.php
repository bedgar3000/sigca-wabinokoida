<?php
// ------------------------------------------------ ####
include("../lib/fphp.php");
define('FPDF_FONTPATH','font/');
require('fpdf.php');
//require('fphp.php');
connect(); 
extract ($_POST);
extract ($_GET);
// ------------------------------------------------ ####
$filtro1=strtr($filtro1, "*", "'");
//---------------------------------------------------
//---------------------------------------------------
//echo $Periodo;
class PDF extends FPDF
{
	//Page header
	function Header(){
	    
		global $Periodo, $filtro2;
			
		$this->Image('../imagenes/logos/logo.jpg', 10, 10, 10, 10);	
		$this->SetFont('Arial', 'B', 8);
		$this->SetXY(20, 10); $this->Cell(146, 5,utf8_decode( $_SESSION["NOMBRE_ORGANISMO_ACTUAL"]), 0, 0, 'L');
		                      $this->Cell(10,5,'Fecha:',0,0,'');$this->Cell(10,5,date('d/m/Y'),0,1,'');
		$this->SetXY(20, 15); $this->Cell(145, 5, utf8_decode('Dirección de Administración'), 0, 0, 'L');
		                       $this->Cell(10,5,utf8_decode('Página:'),0,1,'');
		/*$this->SetXY(19, 20); $this->Cell(150, 5, '', 0, 0, 'L');
		                       $this->Cell(7,5,utf8_decode('Año:'),0,0,'L');$this->Cell(6,5,date('Y'),0,1,'L');*/
							   
		list($fano, $fmes) = SPLIT('[-]', $Periodo); //secho $Periodo;
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
		$this->Cell(50, 10, '', 0, 0, 'C');
		$this->Cell(45, 10, utf8_decode('LIBRO MAYOR AL MES DE'), 0, 0, 'C');
	    //$this->Cell(22, 10, "DICIEMBRE", 0, 0, 'C');
		$this->Cell(24, 10, $mes, 0, 0, 'C'); 
		$this->Cell(5, 10, utf8_decode('DE'), 0, 0, 'C');
		$this->Cell(10, 10, $fano, 0, 1, 'C');
		
		
		$this->SetFont('Arial', 'B', 7);
		$this->Rect(10,34,195,'','');
		$this->Rect(10,38,195,'','');
		$this->Cell(20, 3, 'VOUCH.', 0, 0, 'C');$this->Cell(10, 3,'#', 0, 0, 'C');$this->Cell(15, 3,'FECHA', 0, 0, 'C');$this->Cell(75, 3, 'CONCEPTO', 0, 0, 'C');
		$this->Cell(25, 3, 'PERS.', 0, 0, 'C');$this->Cell(15, 3, '# DOC', 0, 0, 'L');$this->Cell(18, 3, 'DEBE', 0, 0, 'C');
		$this->Cell(18, 3, 'HABER', 0, 1, 'C');
		
		$this->Cell(8, 4, '', 0, 1, 'C');
		
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
$pdf=new PDF('P','mm','Legal');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',12);

if($Periodo=='')$filtro2=" and a.Periodo>='".date("Y").'-'.'00'."' and a.Periodo<='".date("Y-m")."'";

//echo "Contabilidad=".$Contabilidad;
if($Contabilidad=="T") $tabla="ac_mastplancuenta";
elseif($Contabilidad=="F") $tabla="ac_mastplancuenta20";


/// Consulta para obtener los movimientos de las cuentas para el periodo
$s_con01 = "select
        		a.CodOrganismo,
				a.Periodo,
				a.CodCuenta,
				a.SaldoBalance,
				a.CodContabilidad,
				b.Descripcion,
				b.Nivel,
				b.Grupo,
				b.SubGrupo
			from
        		ac_voucherbalance a
				inner join $tabla b on (b.CodCuenta=a.CodCuenta)
			where
        		a.CodOrganismo<>'' $filtro2 $filtro1
		group by 
				a.CodCuenta"; //echo $s_con01;
$q_con01 = mysql_query($s_con01) or die ($s_con01.mysql_error());
$r_con01 = mysql_num_rows($q_con01); //echo $r_con01;

if($r_con01!=0){
  $t_debe = 0; $t_haber = 0; $cont = 0;	
  
  for($i=0; $i<$r_con01; $i++){ //echo $i.'/';
     $f_con01 = mysql_fetch_array($q_con01);
	 
	 //list($ano, $mes, $dia) = split('[-]', $f_con01['FechaVoucher']); 
	 //$f_vocucher = $dia.'-'.$mes.'-'.$ano;
	 
	 

     if($f_con01['CodContabilidad']=='T'){
		if($f_con01['Nivel']=='3')$valorCuentaSubgrupo = substr($f_con01['CodCuenta'], 0, -1); 
		elseif($f_con01['Nivel']=='4')$valorCuentaSubgrupo = substr($f_con01['CodCuenta'], 0, -3);
		elseif($f_con01['Nivel']=='5')$valorCuentaSubgrupo = substr($f_con01['CodCuenta'], 0, -5);
		elseif($f_con01['Nivel']=='6')$valorCuentaSubgrupo = substr($f_con01['CodCuenta'], 0, -7);
		elseif($f_con01['Nivel']=='7')$valorCuentaSubgrupo = substr($f_con01['CodCuenta'], 0, -10);
     }elseif($f_con01['CodContabilidad']=='F'){
		if($f_con01['Nivel']=='3')$valorCuentaSubgrupo = substr($f_con01['CodCuenta'], 0, -3);
		elseif($f_con01['Nivel']=='4')$valorCuentaSubgrupo = substr($f_con01['CodCuenta'], 0, -5); 
		elseif($f_con01['Nivel']=='5')$valorCuentaSubgrupo = substr($f_con01['CodCuenta'], 0, -7);
		elseif($f_con01['Nivel']=='6')$valorCuentaSubgrupo = substr($f_con01['CodCuenta'], 0, -9);
		elseif($f_con01['Nivel']=='7')$valorCuentaSubgrupo = substr($$f_con01['CodCuenta'], 0, -11); 
     }

	
	 ///  Obteniendo Descripción de SubGrupo	
		$s_con03 = "select 
						  CodCuenta,
						  Descripcion,
						  Grupo,
						  subGrupo 
					 from 
					      $tabla 
					 where 
					      CodCuenta = '$valorCuentaSubgrupo'"; 
		$q_con03 = mysql_query($s_con03) or die ($s_con03.mysql_error());
		$r_con03 = mysql_num_rows($q_con03);
		if($r_con03!=0) $f_con03=mysql_fetch_array($q_con03);
			
		if($CuentaCapt != $f_con03['CodCuenta']){ /// condición para mostrar cuenta SubGrupo
		   $pdf->SetFillColor(202, 202, 202);
		   $pdf->SetFont('Arial', 'B', 8);
		   $pdf->Cell(10,6,$f_con03['CodCuenta'],0,0,'L'); 
		   $pdf->Cell(25,6,$f_con03['Descripcion'],0,1,'L'); 
		   $CuentaCapt = $f_con03['CodCuenta'];   
		}
		
	  /// Obteniendo Saldo Anterior
	  if($f_con01['CodCuenta'] != $codCuentaCapturada){
		$CodCuentaCapturada = $f_con01['CodCuenta'];  
	    list($a, $m) = split('[-]',$f_con01['Periodo']);
	    
		if($m=='01'){ 
		  $sa_debe= '0,00'; $sa_haber = '0,00';
		  $pdf->SetFillColor(202, 202, 202);
		  $pdf->SetFont('Arial', 'B', 8);
	      $pdf->Cell(25,6,$f_con01['CodCuenta'],0,0,'L'); 
		  $pdf->Cell(115,6,utf8_decode(substr($f_con01['Descripcion'], 0, 70)),0,0,'L');
		  $pdf->Cell(20,6,'SALDO ANTERIOR ->',0,0,'R');
		  $pdf->Cell(18,6,number_format($sa_debe, 2, ',', '.'),0,0,'R');
		  $pdf->Cell(18,6,number_format($sa_haber, 2, ',', '.'),0,1,'R');
		
		}else{
		  $m = $m - 1 ; //echo $m ;
		  $periodo_anterior = $a.'-'.'0'.$m; //echo $periodo_anterior;
		 
		  $s_saldoanterior = "select 
								    * 
							   from 
								    ac_voucherbalance 
							  where 
								    Periodo = '$periodo_anterior' and 
								    CodOrganismo = '".$f_con01['CodOrganismo']."' and 
								    CodCuenta = '".$f_con01['CodCuenta']."'";
	     $q_saldoanterior = mysql_query($s_saldoanterior) or die ($s_saldoanterior.mysql_error());
		 $r_saldoanterior = mysql_num_rows($q_saldoanterior);
		 
		 if($r_saldoanterior!=0)$f_saldoanterior = mysql_fetch_array($q_saldoanterior);
		 
		 if($f_saldoanterior['SaldoBalance']>=0){
			$sa_debe = $f_saldoanterior['SaldoBalance'];
			$sa_haber = '0,00';
		 }else{ 
		    $sa_debe = '0,00';
		    $sa_haber = $f_saldoanterior['SaldoBalance'];
		 }
		 
		  $pdf->SetFillColor(202, 202, 202);
		  $pdf->SetFont('Arial', 'B', 8);
	      $pdf->Cell(25,6,$f_con01['CodCuenta'],0,0,'L'); 
		  $pdf->Cell(20,6,utf8_decode(substr($f_con01['Descripcion'], 0, 70)),0,0,'L');
		  $pdf->Cell(110,6,'SALDO ANTERIOR ->',0,0,'R');
		  $pdf->Cell(18,6,number_format($sa_debe, 2, ',', '.'),0,0,'R');
		  $pdf->Cell(18,6,number_format($sa_haber, 2, ',', '.'),0,1,'R');
	   }
	  }
	 
	 
//// ----------------------------------------------------------------------
//// 			CONSULTO TABLA AC_VOUCHERDET Y AC_VOCUHERMAST 
list($pa_ano, $pa_mes) = split('[-]',$f_con01['Periodo']); 
$pa_mes = $pa_mes-1; $periodoAnterior = $pa_ano.'-'.'0'.''.$pa_mes; //echo $periodoAnterior;

$s_con02 = "select
			  vmast.Voucher,
			  vdet.Linea,
			  vmast.FechaVoucher,
			  vdet.CodPersona,
			  vdet.ReferenciaNroDocumento,
			  vdet.MontoVoucher,
			  vmast.TituloVoucher
		from
			  ac_voucherdet vdet
			  inner join ac_vouchermast vmast on ((vmast.Voucher = vdet.Voucher) and (vmast.Periodo = vdet.Periodo))
		where
			  vdet.Periodo = '".$f_con01['Periodo']."' and 
			  vdet.CodCuenta = '".$f_con01['CodCuenta']."' and 
			  vdet.CodOrganismo = '".$f_con01['CodOrganismo']."'"; //echo $s_con02;
$q_con02 = mysql_query($s_con02) or die ($s_con02.mysql_error());  
$r_con02 = mysql_num_rows($q_con02);
//// ---------------------------------------------------------------------- 
if($r_con02!=0){ $t_debe = 0; $t_haber = 0;
 for($a=0; $a<$r_con02; $a++){
	$haber = 0; $debe = 0;
	$f_con02 = mysql_fetch_array($q_con02);
	list($ano02, $mes02, $dia02) = split('[-]',$f_con02['FechaVoucher']); $fecha_Voucher = $dia02.'-'.$mes02.'-'.$ano02;
  
	$valor = substr($f_con02['MontoVoucher'],0,1);
	if($valor == '-'){
	  $haber = $f_con02['MontoVoucher']; //echo ' Haber= '.$haber;
	}else{
	  $debe = $f_con02['MontoVoucher']; //echo ' Debe= '.$debe;
	}
	 $t_debe = $t_debe + $debe;// echo ' T_Debe= '.$t_debe;
	 $t_haber = $t_haber + $haber; //echo ' T_Haber= '.$t_haber;
	 $debe01 = number_format($debe,2,',','.');
	 $haber01 = number_format($haber,2,',','.');
	 
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 7);
	$pdf->SetWidths(array(18, 10, 18, 80, 15, 18, 18, 18));
	$pdf->SetAligns(array('C','C','C','L','R','R','R','R'));
	$pdf->Row(array($f_con02['Voucher'], $f_con02['Linea'], $fecha_Voucher, $f_con02['TituloVoucher'], $f_con02['CodPersona'], 
		            $f_con02['ReferenciaNroDocumento'], $debe01,$haber01));
		
 } 

$t_saldoActualCuenta = $t_debe + $t_haber;
$t_saldoActualCuenta = number_format($t_saldoActualCuenta,2,',','.');

$t_saldoAnterior = 
$t_debeA = $t_debeA + $debeAnterior;// echo ' T_Debe= '.$t_debe;
$t_haberA = $t_habera + $haberAnterior; //echo ' T_Haber= '.$t_haber;


$t_debe = number_format($t_debe,2,',','.');	
$t_haber = number_format($t_haber,2,',','.');	
$pdf->SetFillColor(202, 202, 202);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(100,4, '',0,0,'L'); 
$pdf->Cell(48,4,'TOTAL MOVIMIENTO DEL MES->',0,0,'R'); 
$pdf->Cell(30,4,$t_debe,0,0,'R');
$pdf->Cell(18,4,$t_haber,0,1,'R');

if($t_saldoActualCuenta>0){
 $pdf->SetFont('Arial', 'B', 8);
 $pdf->Cell(100,4, '',0,0,'L'); 
 $pdf->Cell(48,4,'SALDO ACTUAL CUENTA '.''.$f_con01['CodCuenta'],0,0,'R'); 
 $pdf->Cell(30,4,$t_saldoActualCuenta,0,0,'R');
 $pdf->Cell(18,4,'',0,1,'R');
 $pdf->ln(2);
}else{
 $pdf->SetFont('Arial', 'B', 8);
 $pdf->Cell(100,4, '',0,0,'L'); 
 $pdf->Cell(48,4,'SALDO ACTUAL CUENTA '.''.$f_con01['CodCuenta'],0,0,'R'); 
 $pdf->Cell(30,4,'',0,0,'R');
 $pdf->Cell(18,4,$t_saldoActualCuenta,0,1,'R');
 $pdf->ln(2);
}

 //// ----------------------------------------------------------------------
$cont = 1; $debe = $haber = 0;
$valor = substr($f_con01['MontoVoucher'],0,1);
if($valor == '-'){
  $haber = substr($f_con01['MontoVoucher'],1,11); //echo ' *Haber= '.$haber;
}else{
  $debe = $f_con01['MontoVoucher']; //echo ' *Debe= '.$debe;
}
$t_debe = $t_debe + $debe; //echo ' *T_Debe= '.$t_debe;
$t_haber = $t_haber + $haber; //echo ' *T_Haber= '.$t_haber;

}}}
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