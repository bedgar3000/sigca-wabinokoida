<?php
/// -------------------------------------------------####
include("../lib/fphp.php");
define('FPDF_FONTPATH','font/');
require('fpdf.php');
//require('fphp.php');
//connect();
/// -------------------------------------------------####
$filtro1=strtr($filtro1, "*", "'");

class PDF extends FPDF
{
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
	//echo $fmes;
	/*$this->SetFont('Arial', 'B', 10);
	$this->Cell(50, 10, '', 0, 0, 'C');
	$this->Cell(55, 10, utf8_decode('BALANCE DE COMPROBACION'), 0, 1, 'C');*/
    //$this->Cell(15, 10, $mes, 0, 0, 'C'); $this->Cell(8, 10, utf8_decode('DE'), 0, 0, 'C');
	//$this->Cell(8, 10, $fano, 0, 1, 'C');


	$this->SetFont('Arial', 'B', 7);
	//$this->Rect(10,34,195,'','');
	//$this->Rect(10,38,195,'','');

	$this->Cell(20, 10,'CUENTA', 1, 0, 'C');
	$this->Cell(100, 10,'DESCRIPCION', 1, 0, 'C');
	$this->Cell(40, 5,'SALDO ACTUAL', 1, 1, 'C');
	$this->Cell(20, 0,'', 0, 0, 'C');
	$this->Cell(100, 0,'', 0, 0, 'C');
	$this->Cell(20, 5,'DEBE', 1, 0, 'C');
	$this->Cell(20, 5,'HABER', 1, 1, 'C');

	/*$this->Cell(8, 3, 'LIN', 0, 0, 'C');$this->Cell(13, 3,'CUENTA', 0, 0, 'C');$this->Cell(15, 3, 'F. VOUCHER', 0, 0, 'L');
	$this->Cell(13, 3, 'PERS.', 0, 0, 'C');$this->Cell(15, 3, 'C.COSTOS', 0, 0, 'L');$this->Cell(17, 3, 'NRO. DOC.', 0, 0, 'R');
	$this->Cell(23, 3, 'REFERENCIA', 0, 0, 'R');$this->Cell(55, 3, 'DESCRIPCION', 0, 0, 'C');$this->Cell(18, 3, 'DEBE', 0, 0, 'C');
	$this->Cell(18, 3, 'HABER', 0, 1, 'C');*/

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
$pdf=new PDF('P','mm','Letter');
$pdf->AliasNbPages();
$pdf->SetMargins(10, 5, 10);
$pdf->SetAutoPageBreak(1, 18);
$pdf->AddPage();
$pdf->SetFont('Times','',12);

////------------------------------------------------------------------------ */
////
////------------------------------------------------------------------------ */
list($fano, $fmes) = split('[-]', $fperiodo);


if($fCodContabilidad=='T') $tabla = 'ac_mastplancuenta';
else $tabla = 'ac_mastplancuenta20';



$sql_a = "select
  				 a.*,
				 b.Descripcion,
				 b.Nivel
			from
			     ac_balancecuenta a
				 inner join $tabla b on (b.CodCuenta=a.CodCuenta)
		    where
				 a.CodCuenta>='".$cuenta_desde."' and
				 a.CodCuenta<='".$cuenta_hasta."' and
				 a.Anio = '$fano' and
				 a.CodContabilidad = '".$fContabilidad."' and
				 a.CodOrganismo = '".$forganismo."'
			order by CodCuenta"; //echo $sql_a;
$qry_a = mysql_query($sql_a) or die ($sql_a.mysql_error());
$row_a = mysql_num_rows($qry_a);
if($row_a != 0)
 for($i=0; $i<$row_a; $i++){
   $field_a = mysql_fetch_array($qry_a);

switch ($fmes) {
		case "01": $valor = "$field_a[SaldoBalance01]"; break;
		case "02": $valor = "$field_a[SaldoBalance02]";break;
		case "03": $valor = "$field_a[SaldoBalance03]";break;
		case "04": $valor = "$field_a[SaldoBalance04]";break;
		case "05": $valor = "$field_a[SaldoBalance05]";break;
		case "06": $valor = "$field_a[SaldoBalance06]";break;
		case "07": $valor = "$field_a[SaldoBalance07]"; break;
		case "08": $valor = "$field_a[SaldoBalance08]"; break;
		case "09": $valor = "$field_a[SaldoBalance09]"; break;
		case "10": $valor = "$field_a[SaldoBalance10]"; break;
		case "11": $valor = "$field_a[SaldoBalance11]"; break;
		case "12": $valor = "$field_a[SaldoBalance12]"; break;
    }

//formulas
if($fmes != '')
 	if($field_a['SaldoInicial']>0)$saldo_anterior_debe+= $field_a['SaldoInicial'];else  $saldo_anterior_haber = $field_a['SaldoInicial'];

if($fmes>='01')
	if($field_a['SaldoBalance01']>0) $saldo_anterior_debe+= $field_a['SaldoBalance01'];else $saldo_anterior_haber+= $field_a['SaldoBalance01'];

if($fmes>='02')
	if($field_a['SaldoBalance02']>0) $saldo_anterior_debe+= $field_a['SaldoBalance02'];else $saldo_anterior_haber+= $field_a['SaldoBalance02'];

if($fmes>='03')
	if($field_a['SaldoBalance03']>0) $saldo_anterior_debe+= $field_a['SaldoBalance03'];else $saldo_anterior_haber+= $field_a['SaldoBalance03'];

if($fmes>='04')
   if($field_a['SaldoBalance04']>0) $saldo_anterior_debe+= $field_a['SaldoBalance04'];else $saldo_anterior_haber+= $field_a['SaldoBalance04'];

if($fmes>='05')
   if($field_a['SaldoBalance05']>0) $saldo_anterior_debe+= $field_a['SaldoBalance05'];else $saldo_anterior_haber+= $field_a['SaldoBalance05'];

if($fmes>='06')
   if($field_a['SaldoBalance06']>0) $saldo_anterior_debe+= $field_a['SaldoBalance06'];else $saldo_anterior_haber+= $field_a['SaldoBalance06'];

if($fmes>='07')
   if($field_a['SaldoBalance07']>0) $saldo_anterior_debe+= $field_a['SaldoBalance07'];else $saldo_anterior_haber+= $field_a['SaldoBalance07'];

if($fmes>='08')
   if($field_a['SaldoBalance08']>0) $saldo_anterior_debe+= $field_a['SaldoBalance08'];else $saldo_anterior_haber+= $field_a['SaldoBalance08'];

if($fmes>='09')
   if($field_a['SaldoBalance09']>0) $saldo_anterior_debe+= $field_a['SaldoBalance09'];else $saldo_anterior_haber+= $field_a['SaldoBalance09'];

if($fmes>='10')
   if($field_a['SaldoBalance10']>0) $saldo_anterior_debe+= $field_a['SaldoBalance10'];else $saldo_anterior_haber+= $field_a['SaldoBalance10'];

if($fmes>='11')
   if($field_a['SaldoBalance11']>0) $saldo_anterior_debe+= $field_a['SaldoBalance11'];else $saldo_anterior_haber+= $field_a['SaldoBalance11'];

if($fmes>='12')
   if($field_a['SaldoBalance12']>0) $saldo_anterior_debe+= $field_a['SaldoBalance12'];else $saldo_anterior_haber+= $field_a['SaldoBalance12'];

////------------------------------------------------------------------------ */
//// 			Muestra total por agrupadores
////------------------------------------------------------------------------ */
$CuentaCapt= $field_a['CodCuenta'];
  if($field_a['CodContabilidad']=='T'){
	if($field_a['Nivel']=='3')$valor2 = substr($CuentaCapt, 0, -1);
	else if($field_a['Nivel']=='4')$valor2 = substr($CuentaCapt, 0, -3);
	else if($field_a['Nivel']=='5')$valor2 = substr($CuentaCapt, 0, -5);
	else if($field_a['Nivel']=='6')$valor2 = substr($CuentaCapt, 0, -7);
	else if($field_a['Nivel']=='7')$valor2 = substr($CuentaCapt, 0, -10);
  }elseif($field_a['CodContabilidad']=='F'){
	if($field_a['Nivel']=='3')$valor2 = substr($CuentaCapt, 0, -3);
	else if($field_a['Nivel']=='4')$valor2 = substr($CuentaCapt, 0, -5);
	else if($field_a['Nivel']=='5')$valor2 = substr($CuentaCapt, 0, -7);
	else if($field_a['Nivel']=='6')$valor2 = substr($CuentaCapt, 0, -9);
	else if($field_a['Nivel']=='7')$valor2 = substr($CuentaCapt, 0, -11);
  }
	//echo $valor_capturado.'=='.$valor2.'--//';
  if($valor_capturado==$valor2) $cont_cuenta=0;
  else{
	  $cont = $cont + 1;
	  if(($cont==2)and($paso=='')){ $cont_cuenta=1; $cont=0;}
	  elseif($paso!='paso'){
		 $cont_cuenta=0;
		 $valor_capturado = $valor2;
		 $paso='paso';
		 $cont= $cont+1;
	  }else $cont_cuenta=1;
  }
 //if($cont_igual==1){ $cont_cuenta=1; $cont_igual=0;}

$contador+=1;
/*if($cont_cuenta==1){

	$s_cuenta = "select * from $tabla where CodCuenta='$valor_capturado'"; //echo $s_cuenta;
	$q_cuenta = mysql_query($s_cuenta) or die ($s_cuenta.mysql_error());
	$f_cuenta = mysql_fetch_array($q_cuenta);

	if($saldo_anterior_debe_ag=='')$saldo_anterior_debe_ag='0,00';
	if($saldo_actual_debe_ag=='')$saldo_actual_debe_ag='0,00';

	$pdf->SetFillColor(202, 202, 202);
	$pdf->SetFont('Arial', 'B', 7);
	$pdf->Cell(20,4,$f_cuenta['CodCuenta'],0,0,'L');
	$pdf->Cell(90,4,substr($f_cuenta['Descripcion'],0,90),0,0,'L');
	$pdf->Cell(20,4,number_format($saldo_anterior_debe_ag,2,',','.'),'','','R');
	$pdf->Cell(20,4,number_format(-1*$saldo_anterior_haber_ag,2,',','.'),'',1,'R');

	$paso='';
    //$valor_capturado = '';
	$cont_cuenta = '';
	$saldo_anterior_debe_ag=''; $saldo_anterior_haber_ag='';
	$mov_mes_debe_ag =''; $mov_mes_haber_ag='';
	$saldo_actual_debe_ag=''; $saldo_actual_haber_ag='';
}*/


////------------------------------------------------------------------------ */
////     							SALDO ACTUAL
////------------------------------------------------------------------------ */

//$SALDO = $saldo_anterior_debe - abs($saldo_anterior_haber);

$saldo_anterior_debe = round($saldo_anterior_debe, 2);
$saldo_anterior_haber = round($saldo_anterior_haber, 2);
$SALDO = $saldo_anterior_debe + $saldo_anterior_haber;

//if($field_a['CodCuenta']=="12133020209") echo "$SALDO = $saldo_anterior_debe - abs($saldo_anterior_haber);";
//$SALDO = 6836.82 + -6836.82;
//if($field_a['CodCuenta']=="12133020209") echo "saldo_anterior_debe= ".$saldo_anterior_debe." saldo_anterior_haber= ".$saldo_anterior_haber." Saldo = ".$SALDO." SALDO_ACTUAL_DEBE= ".$SALDO_ACTUAL_DEBE." SALDO_ACTUAL_HABER=".$SALDO_ACTUAL_HABER;

if($SALDO > 0){
  $SALDO_ACTUAL_DEBE = $SALDO;
  $SALDO_ACTUAL_DEBE_TOTAL+= $SALDO_ACTUAL_DEBE;
}elseif($SALDO < 0){
  $SALDO_ACTUAL_HABER = $SALDO;
  $SALDO_ACTUAL_HABER_TOTAL+= $SALDO_ACTUAL_HABER;
}



////------------------------------------------------------------------------ */
//// 	MOVIMIENTO DEL MES = "sumatoria del SaldoBalance segun periodo"
////------------------------------------------------------------------------ */
if($valor>0)$mov_mes_debe = $valor;else $mov_mes_haber = $valor; //echo $field_a[CodCuenta].'-'.$valor;

//// Informacion agrupadores movimiento mes
$mov_mes_debe_ag+= $mov_mes_debe;
$mov_mes_haber_ag+= $mov_mes_haber;

//// Totalizador movimiento del mes
$total_mov_mes_debe+= $mov_mes_debe;
$total_mov_mes_haber+= $mov_mes_haber;

////------------------------------------------------------------------------ */
//// 	SALDO ACTUAL = "sumatoria de los debe y haber de saldo anterior y
////							movimiento del mes"
////------------------------------------------------------------------------ */
$saldo_actual = ($mov_mes_debe + $saldo_anterior_debe) + ($mov_mes_haber + $saldo_anterior_haber);
if($saldo_actual>0)$saldo_actual_debe = $saldo_actual; else $saldo_actual_haber = $saldo_actual;

//// Informacion agrupadores saldo actual
$saldo_actual_debe_ag+= $saldo_actual_debe;
$saldo_actual_haber_ag+= $saldo_actual_haber;

//// Totalizador sueldo actual
$total_sueldo_actual_debe+= $saldo_actual_debe;
$total_sueldo_actual_haber+= $saldo_actual_haber;

////------------------------------------------------------------------------ */
//// 					IMPRIMIR DATOS POR PANTALLA
////			Muestra la informacion de detalle de las cuentas
////------------------------------------------------------------------------ */





   //if($field_a['CodCuenta']=="12133020209") echo "SALDO_ACTUAL_DEBE= ".$SALDO_ACTUAL_DEBE." SALDO_ACTUAL_HABER=".$SALDO_ACTUAL_HABER;
   //$SALDO_ACTUAL_HABER = round($SALDO_ACTUAL_HABER,2);
   if(($SALDO_ACTUAL_DEBE>0)or($SALDO_ACTUAL_HABER<0)){
	   /*if($SALDO_ACTUAL_DEBE=='') $SALDO_ACTUAL_DEBE='0,00';
       if($SALDO_ACTUAL_HABER=='') $SALDO_ACTUAL_HABER='0,00';*/

     $pdf->SetFillColor(202, 202, 202);
     $pdf->SetFont('Arial', '', 7);
     $pdf->Cell(20,4,$field_a['CodCuenta'],0,0,'L');
     $pdf->Cell(100,4,utf8_decode(substr($field_a['Descripcion'],0,80)),0,0,'L');
     $pdf->Cell(20,4,number_format($SALDO_ACTUAL_DEBE,2,',','.'),'','','R');
     $pdf->Cell(20,4,number_format(-1*$SALDO_ACTUAL_HABER,2,',','.'),'', 1,'R');
   }
    $SALDO_ACTUAL_DEBE=0;
	$SALDO_ACTUAL_HABER=0;
	$saldo_anterior_debe=0;
	$saldo_anterior_haber=0;
	$SALDO= 0;
////------------------------------------------------------------------------ */
} /// Fin


$pdf->Cell(120, 4, 'Total: ', 1, 0, 'R');
$pdf->Cell(20, 4, number_format($SALDO_ACTUAL_DEBE_TOTAL, 2, ',', '.'), 1, 0, 'R');
$pdf->Cell(20, 4, number_format((-1*$SALDO_ACTUAL_HABER_TOTAL), 2, ',', '.'), 1, 1, 'R');

////------------------------------------------------------------------------ */
/*$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(202, 202, 202); $pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont('Arial', 'B', 7);
	$pdf->Cell(100,3,'',0,1,'L');
	$pdf->Cell(60,3,'PREPARADO POR:',0,0,'L');$pdf->Cell(90,3,'REVISADO POR:',0,0,'L');$pdf->Cell(100,3,'CONFORMADO POR:',0,1,'L');
	$pdf->Cell(100,5,'',0,0,'L');$pdf->Cell(120,5,'',0,0,'L');$pdf->Cell(100,5,'',0,1,'L');
	$pdf->Cell(60,5,'LCDA. AMARILIS GONZALEZ',0,0,'L');$pdf->Cell(90,5,'LCDA. YOSMAR GREHAM',0,0,'L');$pdf->Cell(100,5,'ING. EDILIO VELASQUEZ',0,1,'L');
	$pdf->Cell(60,2,'ANALISTA CONTABLE I',0,0,'L');$pdf->Cell(90,2,'DIRECTORA DE AMINISTRACION',0,0,'L');$pdf->Cell(100,2,'DIRECTOR(A) GENERAL',0,1,'L');*/
    $pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(202, 202, 202); $pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont('Arial', 'B', 7);
	$pdf->Cell(100,3,'',0,1,'L');
	$pdf->Cell(60,3,'PREPARADO POR:',0,0,'L');
	$pdf->Cell(90,3,'REVISADO POR:',0,0,'L');
	$pdf->Cell(100,3,'CONFORMADO POR:',0,1,'L');

	$pdf->Cell(100,5,'',0,0,'L');
	$pdf->Cell(120,5,'',0,0,'L');
	$pdf->Cell(100,5,'',0,1,'L');
	//$pdf->Cell(60,5,'LCDA. MARIA RODRIGUEZ',0,0,'L');
	$pdf->Cell(60,5,'LCDA. DIANNELYS SILVA',0,0,'L');
	$pdf->Cell(90,5,'LCDA. YOSMAR GREHAM',0,0,'L');
	//*$pdf->Cell(90,5,'LCDA. YOSMAR GREHAM',0,1,'L');
	//*$pdf->Cell(90,5,'LCDA. ROSIS REQUENA',0,1,'L');
	$pdf->Cell(100,5,'ING. EDILIO VELASQUEZ',0,1,'L');

	//$pdf->Cell(60,2,'ANALISTA CONTABLE I',0,0,'L');
	//$pdf->Cell(90,2,'DIRECTORA DE AMINISTRACION(E)',0,0,'L');
	$pdf->Cell(60,2,'ANALISTA CONTABLE I',0,0,'L');
	$pdf->Cell(90,2,'DIRECTORA DE AMINISTRACION',0,0,'L');
	//*$pdf->Cell(100,2,'DIRECTORA DE AMINISTRACION',0,1,'L');
	//*$pdf->Cell(100,2,'DIRECTORA GENERAL',0,1,'L');
	$pdf->Cell(100,2,'DIRECTOR GENERAL',0,1,'L');

/*$pdf->SetDrawColor(0, 0, 0); $pdf->SetFillColor(202, 202, 202); $pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont('Arial', 'B', 7);
	$pdf->Cell(100,3,'',0,1,'L');
	$pdf->Cell(60,3,'PREPARADO POR:',0,0,'L');$pdf->Cell(90,3,'REVISADO POR:',0,0,'L');$pdf->Cell(100,3,'CONFORMADO POR:',0,1,'L');
	$pdf->Cell(100,5,'',0,0,'L');$pdf->Cell(120,5,'',0,0,'L');$pdf->Cell(100,5,'',0,1,'L');
	$pdf->Cell(60,5,'LCDA. YOSMAR GREHAM',0,0,'L');$pdf->Cell(90,5,'LCDA. YOSMAR GREHAM',0,0,'L');$pdf->Cell(100,5,'LCDA. ROSIS REQUENA',0,1,'L');
	$pdf->Cell(60,2,'DIRECTORA DE AMINISTRACION',0,0,'L');$pdf->Cell(90,2,'DIRECTORA DE AMINISTRACION',0,0,'L');$pdf->Cell(100,2,'DIRECTORA GENERAL',0,1,'L');*/
$pdf->Output();
?>
