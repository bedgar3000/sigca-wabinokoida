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

$filtro1=strtr($filtro1, "*", "'");
$filtro2=strtr($filtro2, "*", "'");

class PDF extends FPDF
{
//Page header
function Header(){
    
	global $Periodo, $contabilidad, $filtro2;
		
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
	//$this->Cell(50, 10, '', 0, 0, 'C');
	$this->Cell(200, 10, utf8_decode('LIBRO MAYOR GENERAL'), 0, 1, 'C');
	
	$this->SetFont('Arial', 'B', 9);
    $this->Cell(200, 3, "Del 01/".$fmes."/".$fano."   Al  31/".$fmes."/".$fano, 0, 1, 'C'); $this->Ln(1); 

	
	
	/*$this->SetFont('Arial', 'B', 7);
	$this->Rect(10,34,195,'','');
	$this->Rect(10,38,195,'','');
	$this->Cell(20, 3, 'VOUCH.', 0, 0, 'C');$this->Cell(10, 3,'#', 0, 0, 'C');$this->Cell(15, 3,'FECHA', 0, 0, 'C');$this->Cell(75, 3, 'CONCEPTO', 0, 0, 'C');
	$this->Cell(25, 3, 'PERS.', 0, 0, 'C');$this->Cell(15, 3, '# DOC', 0, 0, 'L');$this->Cell(18, 3, 'DEBE', 0, 0, 'C');
	$this->Cell(18, 3, 'HABER', 0, 1, 'C');
	
	$this->Cell(8, 4, '', 0, 1, 'C');*/
	
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

if($contabilidad=="F"){ 
  $tabla = "ac_mastplancuenta20";
  $niveles = 3;
}else{ 
  $tabla = "ac_mastplancuenta";
  $niveles = 4;
}


if($Periodo=='')$filtro2=" and a.Periodo>='".date("Y").'-'.'00'."' and a.Periodo<='".date("Y-m")."'";

/// Consulta para obtener los movimientos de las cuentas para el periodo
$sql01 = "select
        		a.CodOrganismo,
				a.Periodo,
				a.CodCuenta,
				a.SaldoBalance,
				b.Descripcion,
				b.Nivel,
				b.Grupo,
				b.SubGrupo
			from
        		ac_voucherbalance a
				inner join $tabla b on (b.CodCuenta=a.CodCuenta)
			where
        		a.CodOrganismo<>'' $filtro1
		group by 
				a.CodCuenta"; //echo $sql01;
$qry01 = mysql_query($sql01) or die ($sql01.mysql_error());
$row01 = mysql_num_rows($qry01);

if($row01!=0){
  $t_debe = 0; $t_haber = 0; $cont = 0;	
  
  for($i=0; $i<$row01; $i++){
     $field01 = mysql_fetch_array($qry01);
	 
	 
	 if($niveles = 4) $cuenta = substr($field01['CodCuenta'], 0, 5);
	 else $cuenta = substr($field01['CodCuenta'], 0, 3);
	 
	 // realizo consulta para obtener cuenta mayor
	 $sql02 = "select 
	                  a.Descripcion as DescpcuentaMayor,
					  b.Descripcion as Descpcuenta
				from 
				      $tabla a,
					  $tabla b 
				where 
				      a.CodCuenta='$cuenta' and 
				      b.CodCuenta='".$field01['CodCuenta']."'";
	 $qry02 = mysql_query($sql02) or die ($sql02.mysql_error());
	 $row02 = mysql_num_rows($qry02);
	 
	 if($row02 != 0){
	     
		$field02 = mysql_fetch_array($qry02);
		 
		//$pdf->Rect(10,34,195,'','');
		//$pdf->Rect(10,40,195,'','');
        $pdf->SetFont('Arial', 'B', 7);
		$pdf->Cell(105,3,"____________________________________________________________________________",0,0,'C');
		$pdf->Cell(70,3, "____________________________________________________________________________",0,1,'C');
		
		//#1
		$pdf->SetFont('Arial', 'B', 7); $pdf->Cell(50,4,"Cuenta de Mayor: ",0,0,'R'); 
		$pdf->SetFont('Arial', '', 7); $pdf->Cell(50,4,utf8_decode($field02['DescpcuentaMayor']),0,1,'L');  
		
		//#2
		$pdf->SetFont('Arial', 'B', 7); $pdf->Cell(50,4,utf8_decode("Código Cuenta: "),0,0,'R'); 
		$pdf->SetFont('Arial', '', 7); $pdf->Cell(50,4,utf8_decode($field01['CodCuenta']),0,1,'L');
		
		//#3
		$pdf->SetFont('Arial', 'B', 7); $pdf->Cell(50,4,utf8_decode("Descripción de la Cuenta: "),0,0,'R'); 
		$pdf->SetFont('Arial', '', 7); $pdf->Cell(50,4,utf8_decode($field02['Descpcuenta']),0,1,'L');
		
		$pdf->Cell(105,3,"____________________________________________________________________________",0,0,'C');
		$pdf->Cell(70,3, "____________________________________________________________________________",0,1,'C');
		
		//#4
		$pdf->SetFont('Arial', 'B', 7); 
		$pdf->Cell(15,4,"Fecha",0,0,'C');
		$pdf->Cell(25,4,utf8_decode("Número"),0,0,'C'); 		
		$pdf->Cell(80,4,utf8_decode("Descripción del Asiento"),0,0,'L');
		$pdf->Cell(25,4,utf8_decode("Débitos"),0,0,'C');
		$pdf->Cell(25,4,utf8_decode("Créditos"),0,0,'C');
		$pdf->Cell(25,4,utf8_decode("Saldo Actual"),0,1,'C');
		$pdf->Cell(105,3,"____________________________________________________________________________",0,0,'C');
		$pdf->Cell(70,3, "____________________________________________________________________________",0,1,'C');
		
		
		
		list($anio, $mes) = split('[-]', $Periodo);
		// consulta para obtener Saldo Anterior
		$sql03 = "select 
  				 		a.*,
				 		b.Descripcion,
				 		b.Nivel
					from 
			     		ac_balancecuenta a 
				 		inner join $tabla b on (b.CodCuenta=a.CodCuenta) 
		    		where 
				 		a.Anio = '$anio' and 
				 		a.CodContabilidad = '".$contabilidad."' and 
				 		a.CodOrganismo = '".$forganismo."' and 
						a.CodCuenta = '".$field01['CodCuenta']."'
				order by 
						CodCuenta"; //echo $sql03;
	    $qry03 = mysql_query($sql03) or die ($sql03.mysql_error());
		$row03 = mysql_num_rows($qry03);
		
		if($row03 != 0) $field03 = mysql_fetch_array($qry03);
		
//formulas
if($mes >= '01')
 	if($field03['SaldoInicial']>0)$saldo_anterior_debe+= $field03['SaldoInicial'];else  $saldo_anterior_haber+= $field03['SaldoInicial'];
	
if($mes>='02')
	if($field03['SaldoBalance01']>0) $saldo_anterior_debe+= $field03['SaldoBalance01'];else $saldo_anterior_haber+= $field03['SaldoBalance01']; 
	
if($mes>='03')
	if($field03['SaldoBalance02']>0) $saldo_anterior_debe+= $field03['SaldoBalance02'];else $saldo_anterior_haber+= $field03['SaldoBalance02'];

if($mes >='04')
	if($field03['SaldoBalance03']>0) $saldo_anterior_debe+= $field03['SaldoBalance03'];else $saldo_anterior_haber+= $field03['SaldoBalance03']; 
	
if($mes >= '05')
  if($field03['SaldoBalance04']>0) $saldo_anterior_debe+= $field03['SaldoBalance04'];else $saldo_anterior_haber+= $field03['SaldoBalance04'];
  
if($mes >= '06')
   if($field03['SaldoBalance05']>0) $saldo_anterior_debe+= $field03['SaldoBalance05'];else $saldo_anterior_haber+= $field03['SaldoBalance05'];
  
if($mes >= '07')
   if($field03['SaldoBalance06']>0) $saldo_anterior_debe+= $field03['SaldoBalance06'];else $saldo_anterior_haber+= $field03['SaldoBalance06'];

if($mes >='08')
   if($field03['SaldoBalance07']>0) $saldo_anterior_debe+= $field03['SaldoBalance07'];else $saldo_anterior_haber+= $field03['SaldoBalance07'];
   
if($mes >='09')
   if($field03['SaldoBalance08']>0) $saldo_anterior_debe+= $field03['SaldoBalance08'];else $saldo_anterior_haber+= $field03['SaldoBalance08'];
  
if($mes >='10')
   if($field03['SaldoBalance09']>0) $saldo_anterior_debe+= $field03['SaldoBalance09'];else $saldo_anterior_haber+= $field03['SaldoBalance09'];

if($mes >='11')
   if($field03['SaldoBalance10']>0) $saldo_anterior_debe+= $field03['SaldoBalance10'];else $saldo_anterior_haber+= $field03['SaldoBalance10'];

if($mes =='12') 
   if($field03['SaldoBalance11']>0) $saldo_anterior_debe+= $field03['SaldoBalance11'];else $saldo_anterior_haber+= $field03['SaldoBalance11'];
		
  		$saldo_anterior = $saldo_anterior_debe + $saldo_anterior_haber;
		$SaldoActual = $saldo_anterior;
		
		$pdf->SetFont('Arial', 'B', 7);	$pdf->Cell(155,4,"Saldo Anterior...",0,0,'R');
		$pdf->SetFont('Arial', '', 7);  
		 if($saldo_anterior < 0) $pdf->Cell(36, 4,"(".number_format($saldo_anterior,2,',','.').")", 0, 1, 'R'); 
		 else $pdf->Cell(36, 4, number_format($saldo_anterior,2,',','.'), 0, 1, 'R'); $pdf->Ln(1);
		 
		 /// Consulta para obtener movimientos de cuenta en el período
		 $sql04 = "select 
		 				  * 
					from 
						  ac_voucherdet 
				   where 
				          Periodo='$Periodo' and 
						  CodContabilidad='$contabilidad' and 
						  Estado='MA' and 
						  CodCuenta='".$field01['CodCuenta']."'
				order by 
				          CodCuenta, FechaVoucher"; 
		 $qry04 = mysql_query($sql04) or die ($sql04.mysql_error());
		 $row04 = mysql_num_rows($qry04);
		 
		 $TotalSaldoActual =0;
		 
		 if($row04!="") for($x=0; $x<$row04; $x++){
			               $field04 = mysql_fetch_array($qry04);
						   
						   if($field04['MontoVoucher']>0){
						     $debitos = number_format($field04['MontoVoucher'],2,',','.');
							 $creditos = "0,00";
							 $SaldoActual+=  $field04['MontoVoucher'];
							 $TotalDebitos+= $field04['MontoVoucher'];
							 
						   }else{
						     $debitos = "0,00";
							 $creditos = number_format((-1*$field04['MontoVoucher']),2,',','.');
							 $SaldoActual+=  $field04['MontoVoucher'];
							 $TotalCreditos+= $field04['MontoVoucher'];
						   }
						   	 					
							//#5
							list($fanio, $fmes, $fdia) = split('[-]', $field04['FechaVoucher']); $fecha_v = $fdia.'-'.$fmes.'-'.$fanio;
							$pdf->SetFont('Arial', '', 6); 
							$pdf->Cell(15,4,$fecha_v,0,0,'C');
							$pdf->Cell(25,4,$field04['ReferenciaTipoDocumento'].'-'.$field04['ReferenciaNroDocumento'],0,0,'C'); 		
							$pdf->Cell(74,4,utf8_decode(substr($field04['Descripcion'], 0, 70)),0,0,'L');
							$pdf->Cell(25,4,$debitos,0,0,'R');
							$pdf->Cell(25,4,$creditos,0,0,'R');
							$pdf->Cell(25,4,number_format($SaldoActual,2,',','.'),0,1,'R'); 
		                 }
							$pdf->SetFont('Arial', 'B', 7); 
							$pdf->Cell(105,3,"____________________________________________________________________________",0,0,'C');
							$pdf->Cell(70,3, "____________________________________________________________________________",0,1,'C');
							
							$pdf->SetFont('Arial', 'B', 7); 
							$pdf->Cell(114,4,"Totales",0,0,'C');
							$pdf->Cell(25,4,number_format($TotalDebitos,2,',','.'),0,0,'R'); 		
							$pdf->Cell(25,4,number_format((-1*$TotalCreditos),2,',','.'),0,0,'R');
							if($TotalSaldoActual<0)$pdf->Cell(25,4,"(".number_format($SaldoActual,2,',','.').")",0,1,'R');
							else $pdf->Cell(25,4,number_format($SaldoActual,2,',','.'),0,1,'R');
		
	 }
	 
	 
	 //echo "Cuenta=".$f_con01['CodCuenta']."--".$cuenta."**";
	 
	 //echo $niveles == $f_con01['Nivel'];
	 
	 if($niveles == $f_con01['Nivel']){
        
		/// dibujo lineas separadoras
		$pdf->SetFont('Arial', 'B', 7);
		//$pdf->Rect(10,34,195,'','');
	 }
	 
	 
	 list($ano, $mes, $dia) = split('[-]', $f_con01['FechaVoucher']); $f_vocucher = $dia.'-'.$mes.'-'.$ano;
	 
	 ///  Capturando subgrupo
	    if($f_con01['Nivel']=='3')$valorCuentaSubgrupo = substr($f_con01['CodCuenta'],0,-1);
		else if($f_con01['Nivel']=='4')$valorCuentaSubgrupo = substr($f_con01['CodCuenta'],0,-3);
		else if($f_con01['Nivel']=='5')$valorCuentaSubgrupo = substr($f_con01['CodCuenta'],0,-5);
		else if($f_con01['Nivel']=='6')$valorCuentaSubgrupo = substr($f_con01['CodCuenta'],0,-7);
		else if($f_con01['Nivel']=='7')$valorCuentaSubgrupo = substr($f_con01['CodCuenta'],0,-10);
	
	///  Obteniendo Descripción de SubGrupo	
		$s_con03 = "select 
						  CodCuenta,
						  Descripcion,
						  Grupo,
						  subGrupo 
					 from 
					      ac_mastplancuenta 
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
		  $pdf->Cell(115,6,substr($f_con01['Descripcion'], 0, 70),0,0,'L');
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
		  $pdf->Cell(20,6,substr($f_con01['Descripcion'], 0, 60),0,0,'L');
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
	$pdf->Row(array($f_con02['Voucher'],$f_con02['Linea'],$fecha_Voucher,$f_con02['TituloVoucher'],$f_con02['CodPersona'],$f_con02['ReferenciaNroDocumento'],$debe01,$haber01));
		
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