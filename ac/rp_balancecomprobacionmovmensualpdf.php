<?php
define('FPDF_FONTPATH','font/');
require('fpdf.php');
require('fphp.php');
connect(); 
/// -------------------------------------------------
//---------------------------------------------------
$filtro1=strtr($filtro1, "*", "'");
//echo 'filtro1'.$filtro1;
/////global $fd, $fh;
global $periodobusqueda;
//---------------------------------------------------
//---------------------------------------------------
//echo $Periodo;
class PDF extends FPDF
{
//Page header
function Header(){
    
	global $fd, $fh;
	global $periodobusqueda;
	$this->Image('../imagenes/logos/logo.jpg', 10, 10, 10, 10);	
	$this->SetFont('Arial', 'B', 8);
	$this->SetXY(20, 10); $this->Cell(146, 5,utf8_decode( $_SESSION["NOMBRE_ORGANISMO_ACTUAL"]), 0, 0, 'L');
	                      $this->Cell(10,5,'Fecha:',0,0,'');$this->Cell(10,5,date('d/m/Y'),0,1,'');
	$this->SetXY(20, 15); $this->Cell(145, 5, utf8_decode('Dirección de Administración y Servicios'), 0, 0, 'L');
	                       $this->Cell(10,5,utf8_decode('Página:'),0,1,'');
	$this->SetXY(19, 20); $this->Cell(150, 5, '', 0, 0, 'L');
	                       $this->Cell(7,5,utf8_decode('Año:'),0,0,'L');$this->Cell(6,5,date('Y'),0,1,'L'); $this->Ln(2);
						   
	/*list($fano, $fmes) = SPLIT('[-]', $Periodo);
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
    }*/
	//echo $fd.''.$fh;
	if(($fd!='')and($fh!='')and($fd!='0000-00')and($fh!='9999-99')){					   
		$this->SetFont('Arial', 'B', 10);
		$this->Cell(50, 5, '', 0, 0, 'C');
		$this->Cell(100, 5, utf8_decode('BALANCE DE COMPROBACIÓN - MOVIMIENTO MENSUAL DE CUENTAS DEL MAYOR'), 0, 1, 'C');
		$this->SetFont('Arial', '', 10);
		$this->Cell(104, 5, utf8_decode('Perídodo: ').$fd, 0, 0, 'R'); $this->Cell(25, 5, 'AL  '.$fh, 0, 1, 'C'); $this->Ln(2);
	}else{
	    $this->SetFont('Arial', 'B', 10);
		$this->Cell(50, 5, '', 0, 0, 'C');
		$this->Cell(100, 5, utf8_decode('BALANCE DE COMPROBACIÓN - MOVIMIENTO MENSUAL DE CUENTAS DEL MAYOR'), 0, 1, 'C');
		$this->SetFont('Arial', '', 10);
		$this->Cell(104, 5, utf8_decode('Perídodo: ').$periodobusqueda, 0, 1, 'R'); ///$this->Cell(25, 5, 'AL   '.date("Y-m"), 0, 1, 'C'); 
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
	$this->Cell(60, 10,'DESCRIPCION', 1, 0, 'C');
	$this->Cell(33, 5,'SALDOS ANTERIORES', 1, 0, 'C');
	$this->Cell(32, 5,'MOVIMIENTO DEL MES', 1, 0, 'C');
	$this->Cell(32, 5,'TOTALES', 1, 0, 'C');
	$this->Cell(32, 5,'SALDOS', 1, 1, 'C');
	$this->Cell(20, 0,'', 0, 0, 'C');
	$this->Cell(60, 0,'', 0, 0, 'C');
	$this->Cell(17, 5,'DEBE', 1, 0, 'C');
	$this->Cell(16, 5,'HABER', 1, 0, 'C');
	$this->Cell(16, 5,'DEBE', 1, 0, 'C');
	$this->Cell(16, 5,'HABER', 1, 0, 'C');
	$this->Cell(16, 5,'DEBE', 1, 0, 'C');
	$this->Cell(16, 5,'HABER', 1, 0, 'C');
	$this->Cell(16, 5,'DEBE', 1, 0, 'C');
	$this->Cell(16, 5,'HABER', 1, 1, 'C');
	
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

    $this->SetXY(145,13);
    //Arial italic 8
    $this->SetFont('Arial','I',8);
    //Page number
    $this->Cell(0,10,' '.$this->PageNo().'/{nb}',0,0,'C');
}
}
//Instanciation of inherited class
$pdf=new PDF('P','mm','Letter');
$pdf->AliasNbPages();
$pdf->SetMargins(4,'',0);
$pdf->AddPage();
$pdf->SetFont('Times','',12);

////------------------------------------------------------------------------ */
//// Consulta para traer los datos segun seleccion de filtro
////------------------------------------------------------------------------ */

list($p_desde, $mes) = split('[-]',$periodobusqueda);
if($mes!=1){
  $d_desde = $mes - 1;
  $d_desde = '0'.$d_desde;
  $p_desde = $p_desde.'-'.$d_desde; 
}

$s_con01 ="select
                 CodCuenta,
				 CodOrganismo,
				 CodContabilidad,
				 Periodo
            from
                 ac_voucherbalance 
		    where 
			     CodOrganismo<>'' and Periodo>='$p_desde' $filtro1 
            order by CodCuenta"; echo $s_con01;
$q_con01 = mysql_query($s_con01) or die ($s_con01.mysql_error());
$r_con01 = mysql_num_rows($q_con01);  //echo $r_con01;
if($r_con01!=0){ 
  for($i=0; $i<$r_con01; $i++){ //echo "i=".$i.'-'."Contador= ".$cont++.'/**/';
	 $f_con01= mysql_fetch_array($q_con01);
	 $periodo = $f_con01['Periodo'];
	 
	 /// ----------------------------------------------------------------------------------------------------------
	 /// Calculando saldos de periodos anteriores "SALDOS ANTERIORES"
	 /// Caso cuando el mes es distinto de "01"
	   list($ano, $mes) = split('[-]',$periodo);
	   if($mes!='01'){
		   $x = $mes - 1; if($x<'10') $x='0'.$x;
		   /*$periodo_01 = $ano.'-'.'01'; */
	       $periodo_02 = $ano.'-'.$x; 
	     
//// ---------------------------------------------------------------------------------------------------------------------------------////
//// ---------------------------------------------------------------------------------------------------------------------------------////	
   
//// CASO MES 01 ------------------------------------------------------------
////-------------------------------------------------------------------------
////-------------------------------------------------------------------------
//echo "Contador=".$i.'-'."Cuenta=".$f_con01['CodCuenta'].'//';
$sql_a = "select 
				SaldoBalance,
				SaldoInicial,
				CodCuenta 
		   from 
				ac_voucherbalance 
		   where 
				CodOrganismo = '".$f_con01['CodOrganismo']."' and 
				CodCuenta = '".$f_con01['CodCuenta']."' and 
				CodContabilidad = '".$f_con01['CodContabilidad']."' and 
				Periodo='$periodo_02'"; //echo $sql_a;
$qry_a = mysql_query($sql_a) or die ($sql_a.mysql_error());
$row_a = mysql_num_rows($qry_a); //echo $row_a;
if($row_a!=0){
   $field_a = mysql_fetch_array($qry_a);
   
   /// Condicion para obtener valor debe o haber saldo inicial
  if($field_a['SaldoInicial']>0){ 
	$s_inicial_anterior_debe = $field_a['SaldoInicial'];
	$s_inicial_anterior_haber = '';
  }else{  
	$s_inicial_anterior_debe = '';
	$s_inicial_anterior_haber = $field_a['SaldoInicial'] ;
  }
	
  /// Condicion para obtener valor debe o haber saldo balance
  if($field_a['SaldoBalance']>0){
	$s_balance_anterior_debe = $field_a['SaldoBalance'];
	$s_balance_anterior_haber = '';
  }else{
	$s_balance_anterior_debe = '';
	$s_balance_anterior_haber = $field_a['SaldoBalance'];  
  }


  $suma_anterior_debe += $s_inicial_anterior_debe;
  $suma_anterior_haber += $s_inicial_anterior_haber;
} 
	       //echo  $suma_anterior_debe.'-'.$suma_anterior_haber;
	       /// -------------------------------------------------------------------------
  		   /// 	" MOVIMIENTOS DEL MES" -------------------------------------------------
           /// Condición que permite obtener detalle de las cuentas dependiendo del tipo de contabilidad
		   if($f_con01['CodContabilidad']=='T') $tabla = 'ac_mastplancuenta';
           else $tabla = 'ac_mastplancuenta20';
  
		  $s_movmes ="select
					 DISTINCT(a1.CodCuenta),
					 sum(a1.MontoVoucher) as Deudor,
					(Select
						   sum(a2.MontoVoucher)
					   From
						  ac_voucherdet a2
					  Where
						  a1.CodCuenta=a2.CodCuenta and
						  a1.Periodo=a2.Periodo and
                          a1.CodContabilidad=a2.CodContabilidad and
						  a2.MontoVoucher<0 and 
						  a1.Estado=a2.Estado
				   Group By
						  a2.Codcuenta, a2.Periodo) as Acreedor
			From
				ac_voucherdet a1
			Where
				a1.CodCuenta='".$f_con01['CodCuenta']."' and
				a1.CodOrganismo='".$f_con01['CodOrganismo']."' and
				a1.Periodo='$periodo_02'and
				a1.MontoVoucher>0 and
				a1.CodContabilidad='".$f_con01['CodContabilidad']."' and 
				a1.Estado='MA'
			Group By
				a1.Codcuenta, a1.Periodo"; //echo $s_movmes;
		 $q_movmes = mysql_query($s_movmes) or die ($s_movmes.mysql_error());
		 $r_movmes = mysql_num_rows($q_movmes);
		  if($r_movmes!=0){
			 $f_movmes = mysql_fetch_array($q_movmes);
		 	 $t_debe_anterior = $f_movmes['Deudor'];
		     $t_haber_anterior = $f_movmes['Acreedor'];
		  }else{
			  //condicion Caso Extra
			  $sql_b = "select 
					        CodCuenta,sum(MontoVoucher) as Acreedor
						From
							ac_voucherdet
						Where
							CodCuenta='".$f_con01['CodCuenta']."' and
							CodOrganismo='".$f_con01['CodOrganismo']."' and
							Periodo='$periodo_02'and
							MontoVoucher<0 and
							CodContabilidad='".$f_con01['CodContabilidad']."' and 
							Estado='MA'
						Group By
							Codcuenta,Periodo"; //echo $sql_b;
			  $qry_b = mysql_query($sql_b) or die ($sql_b.mysql_error());
			  $row_b = mysql_num_rows($qry_b);
			  if($row_b!=0){
				  $field_b = mysql_fetch_array($qry_b);
				  $t_debe_anterior = '0,00';
		          $t_haber_anterior = $field_b['Acreedor'];
			  }else{
			      $t_debe_anterior = '0,00';
		          $t_haber_anterior = '0,00';
			  }
		   }
	       
		   //echo $t_debe_anterior.'/*/'.$t_haber_anterior;
		 
		 $s_cuenta = "select * from $tabla where CodCuenta='".$f_con01['CodCuenta']."'"; //echo $s_cuenta; 
		 $q_cuenta = mysql_query($s_cuenta) or die ($s_cuenta.mysql_error()); 
		 $f_cuenta = mysql_fetch_array($q_cuenta);
	      
		
		//// ------- ******* /////// CALCULOS //////// ********* -------- ////
		$monto_saldo_anterior_debe+=  $suma_anterior_debe + $t_debe_anterior;
		$monto_saldo_anterior_haber+= $suma_anterior_haber + $t_haber_anterior;
		
		$monto_anterior = ($suma_anterior_debe + $t_debe_anterior) + ($suma_anterior_haber + $t_haber_anterior); /// IMPRIMIR EN DETALLE
        if($monto_anterior>0)  $m_anterior_detalle_debe = $monto_anterior;
		else $m_anterior_detalle_haber = $monto_anterior;		
		
		
		if($m_anterior_detalle_debe=='')$m_anterior_detalle_debe='0.00'; 
		if($m_anterior_detalle_haber=='')$m_anterior_detalle_haber='0.00';
		
		$tm_anterior_detalle_debe += $m_anterior_detalle_debe;
		$tm_anterior_detalle_haber += $m_anterior_detalle_haber;
		//$m_totalizador_debe += $m_anterior_detalle_debe; /// IMPRIMIR TOTALIZADOR
		//$m_totalizador_haber += $m_anterior_detalle_haber;

//// ---------------------------------------------------------------------------------------------------------------------------------////
//// ---------------------------------------------------------------------------------------------------------------------------------////	


		   
  /// ------------------------------------------------------------------------------------------------
  /// 								" MOVIMIENTOS DEL MES"
  /// Condición que permite obtener detalle de las cuentas dependiendo del tipo de contabilidad
  if($f_con01['CodContabilidad']=='T') $tabla = 'ac_mastplancuenta';
           else $tabla = 'ac_mastplancuenta20';
  
		  $s_movmes ="select
					 DISTINCT(a1.CodCuenta),
					 sum(a1.MontoVoucher) as Deudor,
					(Select
						   sum(a2.MontoVoucher)
					   From
						  ac_voucherdet a2
					  Where
						  a1.CodCuenta=a2.CodCuenta and
						  a1.Periodo=a2.Periodo and
                          a1.CodContabilidad=a2.CodContabilidad and
						  a2.MontoVoucher<0 and 
						  a1.Estado=a2.Estado
				   Group By
						  a2.Codcuenta, a2.Periodo) as Acreedor
			From
				ac_voucherdet a1
			Where
				a1.CodCuenta='".$f_con01['CodCuenta']."' and
				a1.CodOrganismo='".$f_con01['CodOrganismo']."' and
				a1.Periodo='".$f_con01['Periodo']."'and
				a1.MontoVoucher>0 and
				a1.CodContabilidad='".$f_con01['CodContabilidad']."' and 
				a1.Estado='MA'
			Group By
				a1.Codcuenta, a1.Periodo"; //echo $s_movmes;
		 $q_movmes = mysql_query($s_movmes) or die ($s_movmes.mysql_error());
		 $r_movmes = mysql_num_rows($q_movmes);
		 
		 if($r_movmes!=0){
			 $f_movmes = mysql_fetch_array($q_movmes);
		 	 $t_debe = $f_movmes['Deudor'];
		     $t_haber = $f_movmes['Acreedor'];
		 }else{
			 //condicion Caso Extra
			 $sql_b = "select 
					        CodCuenta,sum(MontoVoucher) as Acreedor
						From
							ac_voucherdet
						Where
							CodCuenta='".$f_con01['CodCuenta']."' and
							CodOrganismo='".$f_con01['CodOrganismo']."' and
							Periodo='".$f_con01['Periodo']."'and
							MontoVoucher<0 and
							CodContabilidad='".$f_con01['CodContabilidad']."' and 
							Estado='MA'
						Group By
							Codcuenta,Periodo"; //echo $sql_b;
			  $qry_b = mysql_query($sql_b) or die ($sql_b.mysql_error());
			  $row_b = mysql_num_rows($qry_b);
			  if($row_b!=0){
				  $field_b=mysql_fetch_array($qry_b);
				  $t_debe = '0,00';
		          $t_haber = $field_b['Acreedor'];
			  }else{
			      $t_debe ='0,00';
		          $t_haber = '0,00';
			  }
		 }
		 
		 $s_cuenta = "select * from $tabla where CodCuenta='".$f_con01['CodCuenta']."'"; //echo $s_cuenta; 
		 $q_cuenta = mysql_query($s_cuenta) or die ($s_cuenta.mysql_error()); 
		 $f_cuenta = mysql_fetch_array($q_cuenta);
  
	//// Condicion para mostrar agrupadores
	$contador+=1; 
	  
		   
		/// OPERACIONES SALDOS
		$monto_saldos_debitos = $m_anterior_detalle_debe + $t_debe; /// imrpime para detalle de la cuenta
		$monto_saldos_creditos = $m_anterior_detalle_haber + $t_haber;
		
		$Total_saldos_debitos_agrup+=$monto_saldos_debitos;
		$Total_saldos_creditos_agrup+=$monto_saldos_creditos;
		
		$saldos = $monto_saldos_debitos + $monto_saldos_creditos; /// SALDOS 
		if($saldos>0)$saldo_debitos = $saldos;
		else $saldo_creditos = $saldos;
		
		$total_saldos_debe+= $saldo_debitos ;
		$total_saldos_haber+= $saldo_creditos;
		
		/// OPERACIONES SUMAS DEL MAYOR AGRUPADOR
		$Total_mayor_debito_agrup += $Total_mayor_debito;  /// imprime agrupador sumas del mayor
		$Total_mayor_creditos_agrup +=  $Total_mayor_creditos;
		
		/// MOVIMIENTOS DEL MES AGRUPADOR 
		$total_debe_mes+= $t_debe;
	    $total_haber_mes+= $t_haber;
	   
	    $total_debe+= $t_debe; 
	    $total_haber+= $t_haber;
	    $debe_cuenta = number_format($t_debe, 2, ',', '.'); 
	    $haber_cuenta = number_format((-1*$t_haber), 2, ',', '.');
		
		$Total_debe_mes_agrup += $total_debe_mes;
		$Total_haber_mes_agrup += $total_haber_mes;
		
		/*if($t_debe=='')$t_debe='0.00'; 
		if($t_haber=='')$t_haber='0.00'; */
		
		if($saldo_creditos=='')$saldo_creditos='0.00'; 
		if($saldo_debitos=='')$saldo_debitos='0.00';
		if($Tmonto_mayor_debito=='')$Tmonto_mayor_debito='0.00'; 
		if($Tmonto_mayor_creditos=='')$Tmonto_mayor_creditos='0.00';
		
		//// ----------------------------------------------------------------------
	   //// Muestra la informacion de detalle de las cuentas
       $pdf->SetFillColor(202, 202, 202);
       $pdf->SetFont('Arial', '', 7);
       $pdf->Cell(20,4,$f_con01['CodCuenta'],0,0,'L'); if($f_con01['CodCuenta']=='2121401')echo "Paso".$f_con01['CodCuenta'];
	   $pdf->Cell(60,4,substr($f_cuenta['Descripcion'],0,45),0,0,'L'); 
	   //$pdf->Cell(17,4,number_format($Tmonto_mayor_debito,2,',','.'),'','','R');
	   //$pdf->Cell(16,4,number_format(-1*$Tmonto_mayor_creditos,2,',','.'),'','','R');
	   $pdf->Cell(17,4,number_format($m_anterior_detalle_debe,2,',','.'),'','','R');
	   $pdf->Cell(16,4,number_format(-1*$m_anterior_detalle_haber,2,',','.'),'','','R');
	   
	   $pdf->Cell(16,4,number_format($t_debe,2,',','.'),'','','R');
	   $pdf->Cell(16,4,number_format(-1*$t_haber,2,',','.'),'','','R');
	   
	   $pdf->Cell(16,4,number_format($monto_saldos_debitos,2,',','.'),'',0,'R');
       $pdf->Cell(16,4,number_format(-1*$monto_saldos_creditos,2,',','.'),'',0,'R');
	   
	   $pdf->Cell(16,4,number_format($saldo_debitos,2,',','.'),'',0,'R');
       $pdf->Cell(16,4,number_format(-1*$saldo_creditos,2,',','.'),'',1,'R');
	   
	    	
	   $m_anterior_detalle_debe=''; $m_anterior_detalle_haber='';
	   $t_debe_anterior=''; $t_haber_anterior='';
	   $saldo_debitos =''; $saldo_creditos ='';
	   $monto_anterior = ''; $suma_anterior_debe=''; $t_debe_anterior=''; $suma_anterior_haber =''; $t_haber_anterior='';   	   
	   }else{
		   
	       //// CASO MES 01 ------------------------------------------------------------
		   ////-------------------------------------------------------------------------
		   ////-------------------------------------------------------------------------
		    $sql_a = "select 
		    				SaldoBalance,
							SaldoInicial,
							CodCuenta 
					   from 
					        ac_voucherbalance 
					   where 
					        CodOrganismo = '".$f_con01['CodOrganismo']."' and 
					        CodCuenta = '".$f_con01['CodCuenta']."' and 
							CodContabilidad = '".$f_con01['CodContabilidad']."' and 
							Periodo='".$f_con01['Periodo']."'"; //echo $sql_a;
			$qry_a = mysql_query($sql_a) or die ($sql_a.mysql_error());
			$row_a = mysql_num_rows($qry_a); //echo $row_a;
			if($row_a!=0){
			   $field_a = mysql_fetch_array($qry_a);
			   
			   /// Condicion para obtener valor debe o haber saldo inicial
			  if($field_a['SaldoInicial']>0) $saldo_inicial_debe = $field_a['SaldoInicial'];
			  else  $saldo_inicial_haber = $field_a['SaldoInicial'];
				
			  /// Condicion para obtener valor debe o haber saldo balance
			  if($field_a['SaldoBalance']>0) $monto_mayor_debito= $field_a['SaldoBalance']; 
			  else $monto_mayor_creditos= $field_a['SaldoBalance'];  
				  
			  /// Operaciones lógicas
			  $Tmonto_mayor_debito+= $saldo_inicial_debe; /// se imprime para el detalle de la cuenta
			  $Tmonto_mayor_creditos+= $saldo_inicial_haber;
			  
			  $Total_mayor_debito+= $saldo_inicial_debe; /// se imprime para agrupador
			  $Total_mayor_creditos+=$saldo_inicial_haber;
			  
			  $total_debe_mayor += $Tmonto_mayor_debito; //// Totalizadores
			  $total_haber_mayor += $Tmonto_mayor_creditos;
		  } 
	  
	       /// -------------------------------------------------------------------------
  		   /// 	" MOVIMIENTOS DEL MES" -------------------------------------------------
           /// Condición que permite obtener detalle de las cuentas dependiendo del tipo de contabilidad
		   if($f_con01['CodContabilidad']=='T') $tabla = 'ac_mastplancuenta';
           else $tabla = 'ac_mastplancuenta20';
  
		  $s_movmes ="select
					 DISTINCT(a1.CodCuenta),
					 sum(a1.MontoVoucher) as Deudor,
					(Select
						   sum(a2.MontoVoucher)
					   From
						  ac_voucherdet a2
					  Where
						  a1.CodCuenta=a2.CodCuenta and
						  a1.Periodo=a2.Periodo and
                          a1.CodContabilidad=a2.CodContabilidad and
						  a2.MontoVoucher<0 and 
						  a1.Estado=a2.Estado
				   Group By
						  a2.Codcuenta, a2.Periodo) as Acreedor
			From
				ac_voucherdet a1
			Where
				a1.CodCuenta='".$f_con01['CodCuenta']."' and
				a1.CodOrganismo='".$f_con01['CodOrganismo']."' and
				a1.Periodo='".$f_con01['Periodo']."'and
				a1.MontoVoucher>0 and
				a1.CodContabilidad='".$f_con01['CodContabilidad']."' and 
				a1.Estado='MA'
			Group By
				a1.Codcuenta, a1.Periodo"; //echo $s_movmes;
		 $q_movmes = mysql_query($s_movmes) or die ($s_movmes.mysql_error());
		 $r_movmes = mysql_num_rows($q_movmes);
		 if($r_movmes!=0){
			 $f_movmes = mysql_fetch_array($q_movmes);
		 	 $t_debe = $f_movmes['Deudor'];
		     $t_haber = $f_movmes['Acreedor'];
		 }else{
			 //condicion Caso Extra
			 $sql_b = "select 
					        CodCuenta,sum(MontoVoucher) as Acreedor
						From
							ac_voucherdet
						Where
							CodCuenta='".$f_con01['CodCuenta']."' and
							CodOrganismo='".$f_con01['CodOrganismo']."' and
							Periodo='".$f_con01['Periodo']."'and
							MontoVoucher<0 and
							CodContabilidad='".$f_con01['CodContabilidad']."' and 
							Estado='MA'
						Group By
							Codcuenta,Periodo"; //echo $sql_b;
			  $qry_b = mysql_query($sql_b) or die ($sql_b.mysql_error());
			  $row_b = mysql_num_rows($qry_b);
			  if($row_b!=0){
				  $field_b=mysql_fetch_array($qry_b);
				  $t_debe = '0,00';
		          $t_haber = $field_b['Acreedor'];
			  }else{
			      $t_debe ='0,00';
		          $t_haber = '0,00';
			  }
		 }
	     
		 $s_cuenta = "select * from $tabla where CodCuenta='".$f_con01['CodCuenta']."'"; //echo $s_cuenta; 
		 $q_cuenta = mysql_query($s_cuenta) or die ($s_cuenta.mysql_error()); 
		 $f_cuenta = mysql_fetch_array($q_cuenta);
	      
		  /// OPERACIONES SALDOS
		$monto_saldos_debitos = $Total_mayor_debito + $t_debe; /// imrpime para detalle de la cuenta
		$monto_saldos_creditos = $Total_mayor_creditos + $t_haber;
		
		$Total_saldos_debitos_agrup+=$monto_saldos_debitos;
		$Total_saldos_creditos_agrup+=$monto_saldos_creditos;
		
		$saldos = $monto_saldos_debitos + $monto_saldos_creditos; /// SALDOS 
		if($saldos>0)$saldo_debitos = $saldos;
		else $saldo_creditos = $saldos;
		
		if($saldo_creditos=='')$saldo_creditos='0.00'; 
		if($saldo_debitos=='')$saldo_debitos='0.00'; 
		
		$total_saldos_debe+= $saldo_debitos ;
		$total_saldos_haber+= $saldo_creditos;
		
		/// OPERACIONES SUMAS DEL MAYOR AGRUPADOR
		$Total_mayor_debito_agrup += $Total_mayor_debito;  /// imprime agrupador sumas del mayor
		$Total_mayor_creditos_agrup +=  $Total_mayor_creditos;
		
		/// MOVIMIENTOS DEL MES AGRUPADOR 
		$total_debe_mes+= $t_debe;
	    $total_haber_mes+= $t_haber;
	   
		/// Movimiento del Mes totalizador	   
	    $total_debe+= $t_debe; 
	    $total_haber+= $t_haber;
		
	    $debe_cuenta = number_format($t_debe, 2, ',', '.'); 
	    $haber_cuenta = number_format((-1*$t_haber), 2, ',', '.');
		
		$Total_debe_mes_agrup += $t_debe;
		$Total_haber_mes_agrup += $t_haber;
		
		$pdf->SetFillColor(202, 202, 202);
       $pdf->SetFont('Arial', '', 7);
       $pdf->Cell(20,4,$f_cuenta['CodCuenta'],0,0,'L'); 
	   $pdf->Cell(60,4,substr($f_cuenta['Descripcion'],0,45),0,0,'L'); 
	   $pdf->Cell(17,4,number_format($Tmonto_mayor_debito,2,',','.'),'','','R');
	   $pdf->Cell(16,4,number_format(-1*$Tmonto_mayor_creditos,2,',','.'),'','','R');
	   
	   $pdf->Cell(16,4,number_format($t_debe,2,',','.'),'','','R');
	   $pdf->Cell(16,4,number_format(-1*$t_haber,2,',','.'),'','','R');
	   
	   $pdf->Cell(16,4,number_format($monto_saldos_debitos,2,',','.'),'',0,'R');
       $pdf->Cell(16,4,number_format(-1*$monto_saldos_creditos,2,',','.'),'',0,'R');
	   
	   $pdf->Cell(16,4,number_format($saldo_debitos,2,',','.'),'',0,'R');
       $pdf->Cell(16,4,number_format(-1*$saldo_creditos,2,',','.'),'',1,'R');
		
       $saldo_creditos=''; $saldo_debitos='';
	   $t_debe=''; $t_haber='';
	  
	  } //// -------

/// Limpiando variables utilizadas para calculo				  
$Tmonto_mayor_debito =''; $Tmonto_mayor_creditos='';				  
$saldo_inicial_debe=''; $saldo_inicial_haber=''; 
$Total_mayor_creditos=''; $Total_mayor_debito='';
//$saldo_debitos_anterior=''; $saldo_creditos_anterior='';

//$total_debe_mes_agrup ='';
//$total_haber_mes_agrup ='';
}}
        $total_debe_saldos = $total_debe + $total_debe_mayor;
		$total_haber_saldos = $total_haber + $total_haber_mayor;
$pdf->SetFillColor(202, 202, 202);
       $pdf->SetFont('Arial', '', 6);
$pdf->Cell(80, 4, 'Total: ', 1, 0, 'R');
$pdf->Cell(16, 4, number_format($tm_anterior_detalle_debe, 2, ',', '.'), 1, 0, 'R');
$pdf->Cell(16, 4, number_format((-1*$tm_anterior_detalle_haber), 2, ',', '.'), 1, 0, 'R');
	
$pdf->Cell(16, 4, number_format($total_debe, 2, ',', '.'), 1, 0, 'R');	
$pdf->Cell(16, 4, number_format((-1*$total_haber), 2, ',', '.'), 1, 0, 'R');

$pdf->Cell(16, 4, number_format($total_debe_saldos, 2, ',', '.'), 1, 0, 'R');
$pdf->Cell(16, 4, number_format((-1*$total_haber_saldos), 2, ',', '.'), 1, 0, 'R');

$pdf->Cell(16, 4, number_format($total_saldos_debe, 2, ',', '.'), 1, 0, 'R');
$pdf->Cell(16, 4, number_format((-1*$total_saldos_haber), 2, ',', '.'), 1, 0, 'R');

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