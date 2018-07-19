<?
include("fphp_ac.php");
connect();
 $sql = "select 
      a.CodCuenta as Cuenta,
      b.Descripcion,
      a.MontoVoucher,
	  a.Periodo       
from 
      ac_voucherdet a 
      inner join ac_mastplancuenta b on (a.CodCuenta=b.CodCuenta)
where 
      a.Periodo='2012-01' 
order by 
      a.Codcuenta;";
	  $qry = mysql_query($sql) or die ($sql.mysql_error());
	  $row = mysql_num_rows($qry);
	  
	  if($row!=0){
	  echo"<table border='1'>
	      <tr>
		    <td>Periodo</td>
		    <td>Cuenta</td>
			<td>Descripcion</td>
			<td>Debe</td>
			<td>Haber</td>
			<td>Saldo</td>
		  </tr>";
		for($i=0; $i<$row; $i++){  
	       $field = mysql_fetch_array($qry);
		   if($field['Cuenta']==$cuenta_capt){
			   if($field['MontoVoucher']>0){
				 $debe+= $field['MontoVoucher'];
			   }else{ 
				 $haber+= $field['MontoVoucher'];
			   }
		   }else{
			  if($cont==1){
			   $saldo = $debe + $haber;	  
			   $debeT = number_format($debe,2,',','.');
		       $haberT = number_format($haber,2,',','.');
			   $saldoT = number_format($saldo,2,',','.');
			   echo"<tr>
				  <td>".$field['Periodo']."</td>
				  <td>$cuenta_capt</td>
				  <td>$Descripcion</td>
				  <td width='100' align='right'>$debeT</td>
				  <td width='100' align='right'>$haberT</td>
				  <td width='100' align='right'>$saldoT</td>
				</tr>";
			}
			  
			  $debe = 0; $haber=0; $cont=1; $saldo= 0; $saldoT=0;
			  $cuenta_capt = $field['Cuenta'];
			  $Descripcion = $field['Descripcion'];
		       if($field['MontoVoucher']>0){
				$debe+= $field['MontoVoucher'];
			   }else{ 
				$haber+= $field['MontoVoucher'];
			   }
		   }
		   if($row==($i + 1)){
			   $saldo = $debe + $haber;	  
			   $debeT = number_format($debe,2,',','.');
		       $haberT = number_format($haber,2,',','.');
			   $saldoT = number_format($saldo,2,',','.');
			   echo"<tr>
				  <td>".$field['Periodo']."</td>
				  <td>$cuenta_capt</td>
				  <td>$Descripcion</td>
				  <td width='100' align='right'>$debeT</td>
				  <td width='100' align='right'>$haberT</td>
				  <td width='100' align='right'>$saldoT</td>
				</tr>";
			}
	   }
		echo"</table> <BR>";
	  }
	
	//// CALCULANDO VOUCHER BALANACE  
$sql_01 = "select 
      a.CodCuenta as Cuenta,
      b.Descripcion,
      a.SaldoBalance,
	  a.Periodo       
from 
      ac_voucherbalance a 
      inner join ac_mastplancuenta b on (a.CodCuenta=b.CodCuenta)
where 
      a.Periodo='2012-01' 
order by 
      a.Codcuenta;";
$qry_01 = mysql_query($sql_01) or die ($sql_01.mysql_error());
$row_01 = mysql_num_rows($qry_01);
	  
	  if($row_01!=0){
	  echo" <font size='2' color='#000000'><b> Voucher Balance</b></font>";	  
	  echo"<table border='1'>
	      <tr>
		    <td>Periodo</td>
		    <td>Cuenta</td>
			<td>Descripcion</td>
			<td>Debe</td>
			<td>Haber</td>
			<td>Saldo</td>
		  </tr>";
		for($y=0; $y<$row_01; $y++){  
	       $field_01 = mysql_fetch_array($qry_01);
		   if($field_01['Cuenta']==$cuenta_capt_01){
			   if($field_01['SaldoBalance']>0){
				 $debe+= $field_01['SaldoBalance'];
			   }else{ 
				 $haber+= $field_01['SaldoBalance'];
			   }
		   }else{
			  if($cont==1){
			   $saldo = $debe + $haber;	  
			   $debeT = number_format($debe,2,',','.');
		       $haberT = number_format($haber,2,',','.');
			   $saldoT = number_format($saldo,2,',','.');
			   echo"<tr>
				  <td>".$field_01['Periodo']."</td>
				  <td>$cuenta_capt</td>
				  <td>$Descripcion</td>
				  <td td width='100' align='right'>$debeT</td>
				  <td td width='100' align='right'>$haberT</td>
				  <td td width='100' align='right'>$saldoT</td>
				</tr>";
			}
			  
			  $debe = 0; $haber=0; $cont=1; $saldo= 0;
			  $cuenta_capt = $field_01['Cuenta'];
			  $Descripcion = $field_01['Descripcion'];
		       if($field['SaldoBalance']>0){
				$debe+= $field_01['SaldoBalance'];
			   }else{ 
				$haber+= $field_01['SaldoBalance'];
			   }
		   }
		  if($row_01==($y + 1)){
			   $saldo = $debe + $haber;	  
			   $debeT = number_format($debe,2,',','.');
		       $haberT = number_format($haber,2,',','.');
			   $saldoT = number_format($saldo,2,',','.');
			   echo"<tr>
				  <td>".$field_01['Periodo']."</td>
				  <td>$cuenta_capt</td>
				  <td>$Descripcion</td>
				  <td width='100' align='right'>$debeT</td>
				  <td width='100' align='right'>$haberT</td>
				  <td width='100' align='right'>$saldoT</td>
				</tr>";
			}
	   }
		echo"</table>";
	  }
	  
	  
	  
  //// REPORTES CALCULADO
/*$sql_02 = "SELECT
      a.CodOrganismo,
      a.Periodo,
      a.CodCuenta,
      b.descripcion,
      a. SaldoBalance AS SaldoAnterior,
      (Select SaldoBalance
       from
           ac_voucherbalance a2
       where
           a2.CodOrganismo=a.CodOrganismo and
           a2.CodCuenta=a.CodCuenta and
           a2.Periodo='2012-02') AS SaldoActual,
      (Select sum(a1.SaldoBalance)
      from
         ac_voucherbalance a1
      where
         a1.CodOrganismo=a.CodOrganismo and
         a1.CodCuenta=a.CodCuenta and
         a1.Periodo>='2012-01' and a1.Periodo<='2012-02') as SaldoActual
FROM
     ac_voucherbalance a, ac_mastplancuenta b
where
     a.CodCuenta=b.CodCuenta and a.Periodo='2012-01'";
$qry_02 = mysql_query($sql_02) or die ($sql_02.mysql_error());
$row_02 = mysql_num_rows()*/
?>